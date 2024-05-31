<?php
declare(strict_types=1);

namespace App\Tests\Common;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;
use DateInterval;
use DateTime;
use http\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractWebTestCase extends WebTestCase
{
    protected KernelBrowser $browser;
    private Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->browser = static::createClient();
        $this->connection = ConnectionProvider::getConnection();
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollback();
        parent::tearDown();
    }

    final protected function getConnection(): Connection
    {
        return $this->connection;
    }

    protected function shouldUpdateSnapshots(): bool
    {
        return false;
    }

    protected function doNewBarber(): ?int
    {
        $this->browser->request('GET', '/barber/new');
        $response = $this->browser->getResponse();
        return $this->extractEntityIdFromResponse($response, 'barber');
    }

    protected function doUpdateBarber(array $newBarberData): void
    {
        $this->browser->request('POST', '/barber/update', $newBarberData);
    }

    protected function doDeleteBarber(int $id): void
    {
        $this->browser->request('GET', '/barber/delete/' . $id);
    }

    protected function doGetBarberInfoContent(int $id): string
    {
        $this->browser->request('GET', '/barber/info/' . $id);
        $response = $this->browser->getResponse();
        return $response->getContent();
    }

    protected function doGetBarbersListContent(): string
    {
        $this->browser->request('GET', '/barber/list');
        $response = $this->browser->getResponse();
        return $response->getContent();
    }

    protected function doNewClient(): ?int
    {
        $this->browser->request('GET', '/client/new');
        $response = $this->browser->getResponse();
        return $this->extractEntityIdFromResponse($response, 'client');
    }

    protected function doUpdateClient(array $newClientData): void
    {
        $this->browser->request('POST', '/client/update', $newClientData);
    }

    protected function doGetClientInfoContent(int $id): string
    {
        $this->browser->request('GET', '/client/info/' . $id);
        $response = $this->browser->getResponse();
        return $response->getContent();
    }

    protected function doGetClientsListContent(): string
    {
        $this->browser->request('GET', '/client/list');
        $response = $this->browser->getResponse();
        return $response->getContent();
    }

    protected function doAddAppointment(array $appointmentData): void
    {
        $this->browser->request('POST', '/appointment/add', $appointmentData);
    }

    /**
     * @param array<string, string> $replacements
     */
    protected function doSnapshotTest(string $snapshotPath, string $text, array $replacements = []): void
    {
        $path = implode('/', [__DIR__, $snapshotPath]);
        $text = str_replace("\r\n", "\n", $text);

        if ($this->shouldUpdateSnapshots())
        {
            $replacements = array_flip($replacements);
            $snapshotText = strtr($text, $replacements);
            if (!file_put_contents($path, $snapshotText))
            {
                throw new RuntimeException("Failed to save snapshot to file: $path");
            }
            $this->fail("SNAPSHOT UPDATED, NOW YOU SHOULD DISABLE UPDATE MODE: $path");
        }

        $snapshotText = file_get_contents($path);
        if (!$snapshotText)
        {
            throw new RuntimeException("Failed to load snapshot from file: $path");
        }

        $snapshotText = str_replace("\r\n", "\n", $snapshotText);
        $expectedText = strtr($snapshotText, $replacements);

        $this->assertEquals($text, $expectedText, "Snapshot");
    }

    protected function extractEntityIdFromResponse(Response $response, string $entityName): ?int
    {
        $pattern = "/\/$entityName\/info\/(\d+)/";
        $string = $response->getContent();

        return preg_match($pattern, $string, $matches) ? intval($matches[1]) : null;
    }


    protected function getTenNearDates(): array
    {
        $dates = [];

        $date = new DateTime(date(""));

        for ($i = 0; $i < 10; $i++)
        {
            $dates['{ date + ' . $i . ' days }'] = $date->format("Y-m-d");
            $dates['{ beautifull date + ' . $i . ' days }'] = $date->format("j F Y");
            $date->add(new DateInterval('P1D'));
        }

        return $dates;
    }
}
