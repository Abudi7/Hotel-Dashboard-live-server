<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Rooms;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BookingController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/booking/new/{roomId}', name: 'app_booking_new')]
    public function new(Request $request, int $roomId, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);
    
        // Fetch available rooms based on selected date range
        $startDate = $form->get('startdate')->getData();
        $endDate = $form->get('enddate')->getData();
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the logged-in user
            $user = $this->getUser();
    
            if ($user === null) {
                return $this->redirectToRoute('app_login');
            }
    
            $customerName = $user->getUserIdentifier();
            $booking->setCustomername($customerName);
    
            $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
            $booking->setCreatedat($now);
    
            $room = $this->entityManager->getRepository(Rooms::class)->find($roomId);
            $booking->setRooms($room);
    
            $address = $booking->getAddress();
            $this->entityManager->persist($address);
            $this->entityManager->persist($booking);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_booking_success');
        }
        
        // Check if start and end dates are not null before fetching available rooms
        if ($startDate !== null && $endDate !== null) {
            // Retrieve available rooms by date range from the repository
            $availableRooms = $bookingRepository->findAvailableRoomsByDateRange($startDate, $endDate);
        } else {
            $availableRooms = [];
        }
    
        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
            'availableRooms' => $availableRooms,
        ]);
    }
    


    #[Route('/booking/success', name: 'app_booking_success')]
    public function success(): Response
    {
        return $this->render('booking/success.html.twig');
    }


    #[Route('/booking', name: 'app_booking')]
    public function index(BookingRepository $bookingRepository): Response
    {
        $user = $this->getUser();
        $bookings = $bookingRepository->findBy(['customername' => $user->getUserIdentifier()]);

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings,
        ]);
    }

    #[Route('/booking/edit/{id}', name: 'app_booking_edit')]
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_booking');
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/booking/delete/{id}', name: 'app_booking_delete')]
    public function delete(Request $request, Booking $booking): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($booking);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_booking');
    }
}

