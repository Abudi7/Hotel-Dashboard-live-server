<?php

namespace App\Controller;

use App\Entity\Lostitem;
use App\Form\LostitemType;
use App\Repository\LostitemRepository;
use App\Repository\BookingRepository;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

use App\Entity\Rooms; 

class LostitemController extends AbstractController
{
    #[Route('/lostitem', name: 'app_lostitem_index', methods: ['GET'])]
    public function index(LostitemRepository $lostitemRepository, RoomsRepository $roomsRepository): Response
    {
        $lostitems = $lostitemRepository->findAll(); // Fetch all lost items
        $rooms = $roomsRepository->findAll(); // Fetch all rooms for dropdown

        return $this->render('lostitem/index.html.twig', [
            'lostitems' => $lostitems,
            'rooms' => $rooms,
        ]);
    }
    #[Route('/lostitems/latest-booking', name: 'app_lostitem_latest_booking', methods: ['GET'])]
    public function latestBookingDetails(Request $request, BookingRepository $bookingRepository, UserRepository $userRepository, RoomsRepository $roomsRepository): Response
    {
        $roomName = $request->query->get('roomName');
        $rooms = $roomsRepository->findAll(); // Fetch all rooms for dropdown
    
        $latestBookings = []; // Initialize an empty array for latest bookings
        $users = []; // Initialize an empty array for users
    
        if ($roomName) {
            // Find the room by name
            $room = $roomsRepository->findOneBy(['name' => $roomName]);
    
            if (!$room) {
                throw $this->createNotFoundException('Room not found');
            }
    
            // Fetch latest booking(s) for the selected room
            $latestBookings = $bookingRepository->findLatestBookingByRoom($room);
    
            // Fetch user associated with the latest booking
            $userEmail = $latestBookings->getCustomername();
            $user = $userRepository->findOneByEmail($userEmail);
                
            // Storing user data in the array
                $users[] = $user;
                
            
        }
    
        return $this->render('lostitem/latest_booking.html.twig', [
            'rooms' => $rooms,
            'selectedRoomName' => $roomName,
            'latestBooking' => $latestBookings,
            'users' => $users, // Pass users to the template
        ]);
    }
    
    #[Route('/lostitem/new', name: 'app_lostitem_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer, SluggerInterface $slugger, UserRepository $userRepository): Response
    {
        $lostitem = new Lostitem();
        $form = $this->createForm(LostitemType::class, $lostitem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            /** @var UploadedFile|null $imgFile */
            $imgFile = $form->get('img')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();

                try {
                    $imgFile->move(
                        $this->getParameter('lostFounds_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                    throw new \Exception('Error uploading file: '.$e->getMessage());
                }

                // Store the filename in the database
                $lostitem->setImg($newFilename);
            }

            // Persist the entity
            $entityManager->persist($lostitem);
            $entityManager->flush();

           // Find admins
           $admins = $userRepository->findByRole('ROLE_ADMIN');
            //dd($admins);
           // Send email notification to admins
           foreach ($admins as $admin) {

               $email = (new TemplatedEmail())
                   ->from(new Address($lostitem->getOwnerContact(), $lostitem->getOwnerName()))
                   ->to(new Address($admin->getEmail()))
                   ->subject('New Lost Item Reported')
                   ->htmlTemplate('email/lostitem_notification.html.twig')
                   ->context([
                       'lostitem' => $lostitem,
                   ]);

                   if ($lostitem->getImg()) {
                    $email->attachFromPath(
                        $this->getParameter('lostFounds_directory') . '/' . $lostitem->getImg(),
                        $lostitem->getImg(),
                        mime_content_type($this->getParameter('lostFounds_directory') . '/' . $lostitem->getImg())
                    );
                }

               $mailer->send($email);
           }


            // Redirect to index or wherever appropriate
            return $this->redirectToRoute('app_lostitem_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lostitem/new.html.twig', [
            'lostitem' => $lostitem,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/lostitem/{id}', name: 'app_lostitem_show', methods: ['GET'])]
    public function show(Lostitem $lostitem): Response
    {
        return $this->render('lostitem/show.html.twig', [
            'lostitem' => $lostitem,
        ]);
    }

    #[Route('/lostitem/{id}/edit', name: 'app_lostitem_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lostitem $lostitem, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(LostitemType::class, $lostitem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $imgFile = $form->get('img')->getData();

            if ($imgFile) {
                $originalFilename = pathinfo($imgFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imgFile->guessExtension();

                try {
                    $imgFile->move(
                        $this->getParameter('lostFounds_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload error
                    throw new \Exception('Error uploading file: '.$e->getMessage());
                }

                // Remove old image if exists
                $oldFilename = $lostitem->getImg();
                if ($oldFilename) {
                    unlink($this->getParameter('lostFounds_directory') . '/' . $oldFilename);
                }

                // Update the image filename in the entity
                $lostitem->setImg($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_lostitem_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lostitem/edit.html.twig', [
            'lostitem' => $lostitem,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/lostitem/{id}', name: 'app_lostitem_delete', methods: ['POST'])]
    public function delete(Request $request, Lostitem $lostitem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lostitem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lostitem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lostitem_index', [], Response::HTTP_SEE_OTHER);
    }

     /**
     * @Route("/lostitems/{roomIdOrName}", name="lostitems_by_room")
     */
    #[Route('/lostitems/{roomIdOrName}', name: 'lostitems_by_room', methods: ['POST'])]
    public function lostItemsByRoom(LostitemRepository $lostitemRepository, $roomIdOrName): Response
    {
        // Check if the roomIdOrName is numeric (assuming it's room ID) or not (assuming it's room name)
        if (is_numeric($roomIdOrName)) {
            $room = $this->getDoctrine()->getRepository(Rooms::class)->find($roomIdOrName);
        } else {
            $room = $this->getDoctrine()->getRepository(Rooms::class)->findOneBy(['name' => $roomIdOrName]);
        }

        if (!$room) {
            throw $this->createNotFoundException('Room not found');
        }

        // Fetch lost items associated with the room
        $lostItems = $lostitemRepository->findByRoom($room);

        return $this->render('lostitem/lostitems_by_room.html.twig', [
            'room' => $room,
            'lostItems' => $lostItems,
        ]);
    }

    #[Route('/fetch-latest-booking', name: 'app_fetch_latest_booking', methods: ['GET'])]
    public function fetchLatestBooking(Request $request, BookingRepository $bookingRepository, RoomsRepository $roomsRepository): JsonResponse
    {
        $roomName = $request->query->get('roomName');

        // Find the room by name
        $room = $roomsRepository->findOneBy(['name' => $roomName]);

        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Fetch latest booking for the room
        $latestBooking = $bookingRepository->findLatestBookingByRoom($room);

        if (!$latestBooking) {
            return new JsonResponse(['error' => 'No booking found for this room'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Prepare JSON response with relevant booking information
        $responseData = [
            'id' => $latestBooking->getId(),
            'createdAt' => $latestBooking->getCreatedAt()->format('Y-m-d H:i:s'),
            // Add other fields as needed based on your Booking entity
        ];

        return new JsonResponse($responseData);
    }
}
