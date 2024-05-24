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

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create the date range form
        $form = $this->createForm(DateRangeType::class);
        
        // Handle the form submission
        $form->handleRequest($request);
    
        // Initialize an empty array to hold the available rooms
        $availableRooms = [];
    
        // Get the current date and time
        $currentDate = new \DateTime();
    
        // Check if the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the form data (start date and end date)
            $data = $form->getData();
            $startDate = $data['startdate'];
            $endDate = $data['enddate'];
    
            // Query the database to find rooms that are available within the selected date range
            $availableRooms = $entityManager->getRepository(Rooms::class)->createQueryBuilder('r')
                ->leftJoin('r.bookings', 'b')
                ->where('b.id IS NULL OR :endDate < b.startdate OR :startDate > b.enddate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate)
                ->getQuery()
                ->getResult();
        } else {
            // If the form is not submitted or not valid, show rooms available for the current date
            $availableRooms = $entityManager->getRepository(Rooms::class)->createQueryBuilder('r')
                ->leftJoin('r.bookings', 'b')
                ->where('b.id IS NULL OR :currentDate < b.startdate OR :currentDate > b.enddate')
                ->setParameter('currentDate', $currentDate)
                ->getQuery()
                ->getResult();
        }
    
        // Render the template with the form and available rooms data
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'form' => $form->createView(),
            'availableRooms' => $availableRooms,
        ]);
    }
    
    
}
