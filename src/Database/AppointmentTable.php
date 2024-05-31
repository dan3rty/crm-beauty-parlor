<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;
use App\Entity\Appointment;
use App\Entity\Barber;
use PDO;

class AppointmentTable
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = ConnectionProvider::getConnection();
    }

    /**
     * @return Barber[]
     */
    public function listAll(): array
    {
        $query = <<<SQL
        SELECT
            id,
            name,
            surname,
            phone
        FROM barber;
        SQL;

        return $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByBarberId(int $id): array
    {
        $query = <<<SQL
            SELECT
                id,
                barber_id,
                client_id,
                DATE_FORMAT(date, '%d %M %Y %H:%i') AS date
            FROM appointment
            WHERE barber_id = $id;
        SQL;

        return $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByClientId(int $id): array
    {
        $query = <<<SQL
            SELECT
                id,
                barber_id, 
                client_id, 
                DATE_FORMAT(date, '%d %M %Y %H:%i') AS date
            FROM appointment
            WHERE client_id = $id;
        SQL;

        return $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findLastByClientId(int $id): array
    {
        $query = <<<SQL
            SELECT
                barber_id,
                client_id,
                DATE_FORMAT(date, '%d %M %Y %H:%i') AS datetime
            FROM appointment a
            WHERE client_id = $id
            ORDER BY date DESC
            LIMIT 1;
        SQL;

        return $this->connection->execute($query)->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function insert(Appointment $appointment): void
    {
        $params = [
            ':barber_id' => $appointment->getBarberId(),
            ':client_id' => $appointment->getClientId(),
            ':date' => $appointment->getDate()
        ];

        $query = <<<SQL
            INSERT INTO appointment(barber_id, client_id, date)
            VALUES (:barber_id, :client_id, :date);            
        SQL;

        $this->connection->execute($query, $params);
    }
}