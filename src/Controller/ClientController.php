<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Barber;
use App\Entity\Client;
use App\Service\AppointmentService;
use App\Service\BarberService;
use App\Service\ClientService;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends AbstractController
{
    private const CLIENT_INFO_URL = '/client/info/';
    private const BARBER_DELETE_URL = '/barber/delete/';
    private const APPOINTMENT_CREATING_URL = '/appointment/new/';
    private ClientService $clientService;
    private BarberService $barberService;
    private AppointmentService $appointmentService;

    public function __construct(ClientService $clientService, BarberService $barberService, AppointmentService $appointmentService)
    {
        $this->clientService = $clientService;
        $this->barberService = $barberService;
        $this->appointmentService = $appointmentService;
    }

    public function listClients(): Response
    {
        $clients = $this->clientService->listClients();

        foreach ($clients as &$client) {
            $client['info_url'] = $this->getClientInfoUrl($client['id']);
            $client['date'] = $this->appointmentService->getLastAppointmentDateByClientId($client['id']);
        }

        return $this->render('client/list.twig', [
            'clients' => $clients
        ]);
    }

    /**
     * @throws Exception
     */
    public function viewClient(string $clientId): Response
    {
        $clientId = (int)$clientId;
        $client = $this->clientService->getClient($clientId);
        $appointments = $this->appointmentService->getAppointmentsByClientId($clientId);

        foreach($appointments as &$appointment)
        {
            $appointment = $this->formatAppointmentData($appointment);
        }

        return $this->render('client/info.twig', [
            'client' => $client,
            'appointments' => $appointments
        ]);
    }

    public function updateClient(Request $request): Response
    {
        if ($request->getMethod() !== Request::METHOD_POST)
        {
            return $this->redirectToRoute('list_clients');
        }

        $client = $this->clientService->updateClient(
            (int) $request->get('id'),
            $request->get('name'),
            $request->get('surname'),
            $request->get('phone')
        );

        $clientId = $client->getId();
        return $this->redirect("/client/info/$clientId");
    }

    public function addClient(): Response
    {
        $clientId = $this->clientService->addClient();

        return $this->redirect("/client/info/$clientId");
    }

    private function getClientInfoUrl(int $clientId): string
    {
        return self::CLIENT_INFO_URL . $clientId;
    }

    private function formatAppointmentData(Appointment $appointment): array
    {
        $barber = $this->barberService->getBarber($appointment->getBarberId());

        return [
            'id' => $appointment->getId(),
            'barber_name' => $barber->getName(),
            'barber_surname' => $barber->getSurname(),
            'date' => $appointment->getDate()
        ];
    }
}
