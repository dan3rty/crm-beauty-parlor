<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Barber;
use App\Database\BarberTable;
use App\Service\Data\BarberData;

class BarberService
{
    private BarberTable $barberTable;

    private const NEW_BARBER_NAME = 'Имя';
    private const NEW_BARBER_SURNAME = 'Фамилия';

    public function __construct(BarberTable $barberTable)
    {
        $this->barberTable = $barberTable;
    }

    /**
     * @return Barber[]
     */
    public function listBarbers(): array
    {
        $barbers = $this->barberTable->listAll();

        foreach ($barbers as &$barber)
        {
            $barber = $this->getBarberFromArray($barber);
        }

        return $barbers;
    }

    public function getBarber(int $barberId): ?Barber
    {
        $barber = $this->barberTable->findById($barberId);

        if (!$barber)
        {
            return null;
        }

        return $this->getBarberFromArray($barber);
    }

    public function addBarber(): int
    {
        $barber = new Barber(
            null,
            self::NEW_BARBER_NAME,
            self::NEW_BARBER_SURNAME,
            null
        );

        return $this->barberTable->insert($barber);
    }

    public function updateBarber(int $id, string $name, string $surname, string $phone): Barber
    {
        $barber = $this->barberTable->findById($id);
        $barber = $this->getBarberFromArray($barber);

        $barber->setName($name);
        $barber->setSurname($surname);
        $barber->setPhone($phone);
        $this->barberTable->update($barber);

        return $barber;
    }

    public function deleteBarber(int $barberId): void
    {
        $this->barberTable->delete($barberId);
    }

    private function getBarberFromArray(array $params): Barber
    {
        return new Barber(
            $params['id'],
            $params['name'],
            $params['surname'],
            $params['phone']
        );
    }
}