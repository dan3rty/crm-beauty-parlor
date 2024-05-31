<?php
declare(strict_types=1);

namespace App\Entity;

class Client
{
    private ?int $id;
    private string $name;
    private string $surname;
    private ?string $phone;

    public function __construct(
        ?int $id,
        string $name,
        string $surname,
        ?string $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->phone = $phone;
    }

    public function getId(): ?int
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

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}