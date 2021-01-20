<?php

declare(strict_types=1);

namespace App\Service;

use App\Document\Room;
use App\Document\User;
use Twilio\Rest\Sync\V1\ServiceContext;

class RoomService
{
    public function __construct(private ServiceContext $syncService)
    {
    }

    public function getDefaultRoom(): Room
    {
        return new Room($this->syncService->documents->read()[0]->data);
    }

    public function addUserToRoom(User $user, Room $room): Room
    {
        $room->people[$user->userId] = $user->name;
        return $room;
    }

    public function save(Room $room): void
    {
        $document = $this->syncService->documents->read()[0];
        $document->update(['data' => (array) $room]);
    }

    public function removeUserFromRoom(User $user, Room $room): Room
    {
        unset($room->people[$user->userId]);
        return $room;
    }
}
