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
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
