<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;
use App\Entity\Barber;
use App\Entity\Client;
use PDO;

class ClientTable
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = ConnectionProvider::getConnection();
    }

    /**
     * @return Client[]
     */
    public function listAll(): array
    {
        $query = <<<SQL
        SELECT
            id,
            name,
            surname,
            phone
        FROM client;
        SQL;

        return $this->connection->execute($query)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listAllWithLastAppointment(): array
    {
        $query = <<<SQL
        SELECT
            c.id,
            name,
            surname,
            a.date 
        FROM client c
        INNER JOIN appointment a ON c.id = a.client_id
        HAVING date = MAX(date);
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
            FROM client с
            WHERE id = $id;
        SQL;

        $client = $this->connection->execute($query)->fetch(PDO::FETCH_ASSOC);

        return $client ?: [];
    }

    public function update(Client $client): void
    {
        $clientId = $client->getId();
        $params = [
            ':client_name' => $client->getName(),
            ':client_surname' => $client->getSurname(),
            ':client_phone' => $client->getPhone()
        ];

        $query = <<<SQL
            UPDATE client
            SET 
                name = :client_name, 
                surname = :client_surname, 
                phone = :client_phone
            WHERE id = $clientId;            
        SQL;

        $this->connection->execute($query, $params);
    }

    public function insert(Client $client): int
    {
        $params = [
            ':client_name' => $client->getName(),
            ':client_surname' => $client->getSurname(),
            ':client_phone' => $client->getPhone()
        ];

        $query = <<<SQL
            INSERT INTO client(name, surname, phone)
            VALUES (:client_name, :client_surname, :client_phone);            
        SQL;

        $this->connection->execute($query, $params);

        return $this->connection->getLastInsertId();
    }
}