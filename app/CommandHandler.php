<?php

declare(strict_types=1);

namespace App;

use App\Document\User;
use App\Service\RoomService;
use App\Service\UserService;

final class CommandHandler
{
    private const ASKED_USER_NAME = '??????????';

    public array $bannedCommands = [
        'askUserForName',
        'handle',
        'setUserName',
        '__construct',
    ];

    public function __construct(
        private RoomService $roomService,
        private UserService $userService,
    ) {
    }

    public function handle(User $user, string $command): void
    {
        $this->userService->addToOutput($user, '> '.$command);

        if ($user->name === null) {
            $this->askUserForName($user);
            return;
        }

        if ($user->name === self::ASKED_USER_NAME) {
            $this->setUserName($user, $command);
            return;
        }

        $commandArray = explode(' ', $command);
        $command = array_shift($commandArray);

        if (method_exists($this, $command) && !in_array($command, $this->bannedCommands, true)) {
            $this->{$command}($user, $commandArray);
            return;
        }

        $this->userService->addToOutput($user, 'I don\'t understand '.$command);
    }

    private function askUserForName(User $user): void
    {
        $user->name = self::ASKED_USER_NAME;
        $this->userService->addToOutput($user, 'Welcome to the MUD! What\'s your name?');
        $this->userService->save($user);
    }

    private function setUserName(User $user, string $name): void
    {
        // sanitise input here for the LOVE OF GOD!
        $user->name = $name;
        $this->userService->addToOutput($user, 'Welcome to the MUD '.$user->name);
        $this->userService->save($user);

        $room = $this->roomService->addUserToRoom($user, $this->roomService->getDefaultRoom());
        $this->roomService->save($room);
    }

    private function look(User $user): void
    {
        $room = $this->roomService->getDefaultRoom();
        $message = $room->room.PHP_EOL;
        $message .= $room->description.PHP_EOL;
        $message .= PHP_EOL;
        foreach ($room->people as $userId => $name) {
            $message .= $name.' is here'.PHP_EOL;
        }

        $this->userService->addToOutput($user, $message);
        $this->userService->save($user);
    }

    private function leave(User $user): void
    {
        $room = $this->roomService->removeUserFromRoom($user, $this->roomService->getDefaultRoom());
        $this->roomService->save($room);
        $this->userService->addToOutput($user, 'You left.');
        $this->userService->save($user); // save the user to trigger the update on front-end
        $this->userService->delete($user);
    }

    private function say(User $user, array $message): void
    {
        $messageToSay = implode(' ', $message);
        $this->userService->addToOutput($user, 'You said "'.$messageToSay.'"');
        $this->userService->save($user);
        $peopleInTheRoom = $this->roomService->getDefaultRoom()->people;
        foreach ($peopleInTheRoom as $userId => $name) {
            if ($userId === $user->userId) {
                continue;
            }
            $userToUpdate = $this->userService->getUserById($userId);
            $this->userService->addToOutput($userToUpdate, $user->name.' said "'.$messageToSay.'"');
            $this->userService->save($userToUpdate);
        }
    }

}
