<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\User;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Sync\V1\ServiceContext;

class UserService
{
    public const LIST_MAP_NAME = 'UserListMap';

    public function __construct(private ServiceContext $syncService)
    {
    }

    public function getUserById(string $userId): User
    {
        try {
            $user = $this->syncService
                ->syncMaps(self::LIST_MAP_NAME)
                ->syncMapItems($userId)
                ->fetch();
        } catch (RestException $e) {
            if ($e->getStatusCode() !== 404) {
                throw $e;
            }

            $user = $this->syncService
                ->syncMaps(self::LIST_MAP_NAME)
                ->syncMapItems
                ->create($userId, ['name' => null]);
        }

        return new User($user->key, $user->data);
    }

    /**
     * @return User[]
     */
    public function getAllUsers(): array
    {
        $return = [];
        foreach (
            $this->syncService
                ->syncMaps(self::LIST_MAP_NAME)
                ->syncMapItems
                ->read([], 20) as $item
        ) {
            $return[] = new User($item->key, $item->data);
        }
        return $return;
    }

    /**
     * @return User[]
     */
    public function displayAllUsers(): array
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            $user->userId = sha1($user->userId . config('service.twilio.token'));
        }
        return $users;
    }

    public function deleteAllUsers(): void
    {
        $this->syncService
            ->syncMaps(self::LIST_MAP_NAME)
            ->delete();

        $this->syncService
            ->syncMaps
            ->create(['uniqueName' => self::LIST_MAP_NAME]);

        $document = $this->syncService->documents->read()[0];
        $data = $document->data;
        $data['people'] = [];
        $document->update(['data' => $data]);
    }

    public function save(User $user): void
    {
        $this->syncService
            ->syncMaps(self::LIST_MAP_NAME)
            ->syncMapItems($user->userId)
            ->update(
                [
                    'data' => [
                        'name' => $user->name,
                        'output' => $user->output,
                    ]
                ]
            );
    }

    public function delete(User $user): void
    {
        $this->syncService
            ->syncMaps(self::LIST_MAP_NAME)
            ->syncMapItems($user->userId)
            ->delete();

    }

    public function addToOutput(User $user, string $post): void
    {
        $user->output[] = $post;
    }
}
