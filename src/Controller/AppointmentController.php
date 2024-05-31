<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Service\AppointmentService;
use App\Service\ClientService;
use App\Service\BarberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends AbstractController
{
    private BarberService $barberService;
    private AppointmentService $appointmentService;
    private ClientService $clientService;

    public function __construct(BarberService $barberService, AppointmentService $appointmentService, ClientService $clientService)
    {
        $this->barberService = $barberService;
        $this->appointmentService = $appointmentService;
        $this->clientService = $clientService;
    }

    public function showAddAppointmentForm(string $barberId, string $date): Response
    {
        $barberId = (int)$barberId;
        $barber = $this->barberService->getBarber($barberId);
        $clients = $this->clientService->listClients();

        return $this->render('appointment/creating.twig', [
            'barber' => $barber,
            'clients' => $clients,
            'date' => $date
        ]);
    }

    public function addAppointment(Request $request): Response
    {
        $appointment = $this->appointmentService->addAppointment(
            (int) $request->get('barber_id'),
            (int) $request->get('client_id'),
            $request->get('date'),
        );

        $barberId = $appointment->getBarberId();
        return $this->redirect("/barber/info/$barberId");
    }
}