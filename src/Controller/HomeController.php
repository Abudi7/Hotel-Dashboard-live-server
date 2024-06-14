<?php

// src/Controller/HomeController.php
namespace App\Controller;

use App\Entity\Rooms;
use App\Form\DateRangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // Create the date range form
        $form = $this->createForm(DateRangeType::class);
        
        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data (start date and end date)
            $data = $form->getData();
            $startDate = $data['startdate'];
            $endDate = $data['enddate'];

            // Store the start date and end date in the session
            $session->set('startDate', $startDate);
            $session->set('endDate', $endDate);

            // Redirect to the available rooms page
            return $this->redirectToRoute('app_available_rooms');
        }

        // Render the template with the form
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/available-rooms', name: 'app_available_rooms')]
    public function showAvailableRooms(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Get start and end dates from the session
        $startDate = $session->get('startDate');
        $endDate = $session->get('endDate');

        // Validate dates
        $today = new \DateTime();
        $today->setTime(0, 0);

        if (!$startDate || !$endDate) {
            $this->addFlash('error', 'Start date and end date must be provided.');
            return $this->redirectToRoute('app_home'); // Change 'app_home' to the route you want to redirect to
        }

        // Check if the retrieved dates are strings and convert them to DateTime objects
        if (!$startDate instanceof \DateTime) {
            try {
                $startDate = new \DateTime($startDate);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid start date format.');
                return $this->redirectToRoute('app_home'); // Change 'app_home' to the route you want to redirect to
            }
        }

        if (!$endDate instanceof \DateTime) {
            try {
                $endDate = new \DateTime($endDate);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Invalid end date format.');
                return $this->redirectToRoute('app_home'); // Change 'app_home' to the route you want to redirect to
            }
        }

        if ($startDate < $today || $endDate < $today) {
            $this->addFlash('error', 'Dates cannot be in the past.');
            return $this->redirectToRoute('app_home'); // Change 'app_home' to the route you want to redirect to
        }

        if ($startDate > $endDate) {
            $this->addFlash('error', 'Start date cannot be later than end date.');
            return $this->redirectToRoute('app_home'); // Change 'app_home' to the route you want to redirect to
        }

        // Query the database to find rooms that are available within the selected date range
        $availableRooms = $entityManager->getRepository(Rooms::class)->createQueryBuilder('r')
            ->leftJoin('r.bookings', 'b')
            ->where('b.id IS NULL OR :endDate < b.startdate OR :startDate > b.enddate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getResult();

        // Render the template with the available rooms data
        return $this->render('home/available_rooms.html.twig', [
            'availableRooms' => $availableRooms,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
    }
}
