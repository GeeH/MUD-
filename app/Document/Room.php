<?php

declare(strict_types=1);


namespace App\Document;


class Room
{
    public string $room;
    public string $description;
    public array $people;

    public function __construct(array $data)
    {
        // Add some validation later
        $this->room = $data['room'];
        $this->description = $data['description'];
        $this->people = $data['people'];
    }
}
