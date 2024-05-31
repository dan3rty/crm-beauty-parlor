<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;
use App\Entity\Barber;
use PDO;

class BarberTable
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = ConnectionProvider::getConnection();
    }

    public function listAll(): array
    {
        $query = <<<SQL
        SELECT
            id,
            name,
            surname,
            phone
        FROM barber b
        WHERE b.deleted_at IS NULL;
        SQL;

        return $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): array
    {
        $query = <<<SQL
            SELECT
                id,
                name,
                surname,
                phone
            FROM barber b
            WHERE id = $id
                AND b.deleted_at IS NULL;
        SQL;

        $barber = $this->connection->execute($query)->fetch(PDO::FETCH_ASSOC);
        return $barber ?: [];
    }

    public function insert(Barber $barber): int
    {
        $params = [
            ':barber_name' => $barber->getName(),
            ':barber_surname' => $barber->getSurname(),
            ':barber_phone' => $barber->getPhone()
        ];

        $query = <<<SQL
            INSERT INTO barber(name, surname, phone)
            VALUES (:barber_name, :barber_surname, :barber_phone);            
        SQL;

        $this->connection->execute($query, $params);

        return $this->connection->getLastInsertId();
    }

    public function update(Barber $barber): void
    {
        $barberId = $barber->getId();
        $params = [
            ':barber_name' => $barber->getName(),
            ':barber_surname' => $barber->getSurname(),
            ':barber_phone' => $barber->getPhone()
        ];

        $query = <<<SQL
            UPDATE barber
            SET 
                name = :barber_name, 
                surname = :barber_surname, 
                phone = :barber_phone
            WHERE id = $barberId;            
        SQL;

        $this->connection->execute($query, $params);
    }

    public function delete(int $barberId): void
    {
        $query = <<<SQL
            UPDATE barber b
            SET b.deleted_at = NOW()
            WHERE id = $barberId;
        SQL;

        $this->connection->execute($query);
    }
}