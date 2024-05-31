<?php
declare(strict_types=1);

namespace App\Service\Data;

class ClientData
{
    private int $id;
    private string $name;
    private string $surname;
    private ?string $phone;

    public function __construct(
        int $id,
        string $name,
        string $surname,
        ?string $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->phone = $phone;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
}