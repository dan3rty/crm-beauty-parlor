<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Barber;
use App\Service\AppointmentService;
use App\Service\BarberService;
use App\Service\ClientService;
use DateInterval;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BarberController extends AbstractController
{
    private const BARBER_INFO_URL = '/barber/info/';
    private const BARBER_DELETE_URL = '/barber/delete/';
    private const APPOINTMENT_CREATING_URL = '/appointment/new/';
    private BarberService $barberService;
    private AppointmentService $appointmentService;
    private ClientService $clientService;

    public function __construct(BarberService $barberService, AppointmentService $appointmentService, ClientService $clientService)
    {
        $this->barberService = $barberService;
        $this->appointmentService = $appointmentService;
        $this->clientService = $clientService;
    }

    public function index(): Response
    {
        return $this->redirectToRoute('list_barbers');
    }

    public function listBarbers(): Response
    {
        $barbers = $this->barberService->listBarbers();

        foreach ($barbers as &$barber)
        {
            $barber = $this->formatBarberData($barber);
        }

        return $this->render('barber/list.twig', [
            'barbers' => $barbers
        ]);
    }

    /**
     * @throws Exception
     */
    public function viewBarber(string $barberId): Response
    {
        $barberId = (int)$barberId;
        $appointments = $this->appointmentService->getAppointmentsByBarberId($barberId);
        $barberInfo = $this->barberService->getBarber($barberId);

        foreach ($appointments as &$appointment)
        {
            $appointment = $this->formatAppointmentData($appointment);
        }

        return $this->render('barber/info.twig', [
            'barber' => $barberInfo,
            'dates' => $this->getTenNearDates($barberId),
            'appointments' => $appointments
        ]);
    }

    public function updateBarber(Request $request): Response
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return $this->redirectToRoute('list_barbers');
        }

        $barber = $this->barberService->updateBarber(
            (int) $request->get('id'),
            $request->get('name'),
            $request->get('surname'),
            $request->get('phone')
        );

        $barberId = $barber->getId();
        return $this->redirect("/barber/info/$barberId");
    }

    public function deleteBarber(string $barberId): Response
    {
        $barberId = (int)$barberId;

        $this->barberService->deleteBarber($barberId);

        return $this->redirectToRoute('list_barbers');
    }

    public function addBarber(): Response
    {
        $barberId = $this->barberService->addBarber();

        return $this->redirect("/barber/info/$barberId");
    }

    private function getBarberInfoUrl(int $barberId): string
    {
        return self::BARBER_INFO_URL . $barberId;
    }

    private function getBarberDeleteUrl(int $barberId): string
    {
        return self::BARBER_DELETE_URL . $barberId;
    }

    private function getTenNearDates(int $barberId): array
    {
        $dates = [];

        $date = new DateTime(date(""));

        for ($i = 0; $i < 10; $i++) {
            $newDate = $date->format("j F Y");
            $dates[] = [
                'date_str' => $date->format("d-m-Y"),
                'date' => $newDate,
                'appointment_creating_url' => $this->getAppointmentCreatingUrl($barberId, $newDate)
            ];
            $date->add(new DateInterval('P1D'));
        }

        return $dates;
    }

    private function getAppointmentCreatingUrl(int $barberId, string $date): string
    {
        $date = date('Y-m-d', strtotime($date));
        return self::APPOINTMENT_CREATING_URL . $barberId . '/' . $date;
    }

    private function formatAppointmentData(Appointment $appointment): array
    {
        $client = $this->clientService->getClient($appointment->getClientId());

        return [
            'id' => $appointment->getId(),
            'client_name' => $client->getName(),
            'client_surname' => $client->getSurname(),
            'datetime' => $appointment->getDate(),
            'date' => date('d-m-Y', strtotime($appointment->getDate())),
        ];
    }

    private function formatBarberData(Barber $barber): array
    {
        return [
            'id' => $barber->getId(),
            'name' => $barber->getName(),
            'surname' => $barber->getSurname(),
            'phone' => $barber->getPhone(),
            'info_url' => $this->getBarberInfoUrl($barber->getId()),
            'delete_url' => $this->getBarberDeleteUrl($barber->getId())
        ];
    }
}
