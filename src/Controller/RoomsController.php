<?php

namespace App\Controller;

use App\Entity\Rooms;
use App\Form\RoomsType;
use App\Repository\RoomsRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Timezone;

class RoomsController extends AbstractController
{
    #[Route('/rooms', name: 'app_rooms_index', methods: ['GET'])]
    public function index(RoomsRepository $roomsRepository): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('rooms/index.html.twig', [
            'rooms' => $roomsRepository->findAll(),
        ]);
    }

    #[Route('rooms/new', name: 'app_rooms_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');
        $room = new Rooms();
        $form = $this->createForm(RoomsType::class, $room);
        $form->handleRequest($request);
        
        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('img')->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            
            if($imageFile){
                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    // Move the image file to the desired directory within the project
                    $imageFile->move($this->getParameter('image_directory'), $newFilename);
                } catch (FileException $e) {
                    // Handle any file upload error gracefully
                    throw new \Exception('Failed to upload the file: ' . $e->getMessage());
                }
                $room->setImg($newFilename);
            }
            // Set the created_at timestamp to the current time in the local timezone
            $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
            $room->setCreatedAt($now);    
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_rooms_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rooms/new.html.twig', [
            'room' => $room,
            'form' => $form,
        ]);
    }

    #[Route('rooms/{id}', name: 'app_rooms_show', methods: ['GET'])]
    public function show(Rooms $room): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('rooms/show.html.twig', [
            'room' => $room,
        ]);
    }

    #[Route('rooms/{id}/edit', name: 'app_rooms_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rooms $room, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(RoomsType::class, $room);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('img')->getData();
    
            if ($imageFile) {
                $originalFileName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFileName);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
    
                try {
                    $imageFile->move($this->getParameter('image_directory'), $newFilename);
                } catch (FileException $e) {
                    // Handle the exception gracefully
                    throw new \Exception('Failed to upload the file: '.$e->getMessage());
                }
    
                // Remove the old image file if exists
                $oldImage = $room->getImg();
                if ($oldImage) {
                    unlink($this->getParameter('image_directory').'/'.$oldImage);
                }
    
                // Update the room's image property
                $room->setImg($newFilename);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_rooms_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('rooms/edit.html.twig', [
            'room' => $room,
            'form' => $form->createView(),
        ]);
    }
    #[Route('rooms/{id}', name: 'app_rooms_delete', methods: ['POST'])]
    public function delete(Request $request, Rooms $room, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$room->getId(), $request->request->get('_token'))) {
            $entityManager->remove($room);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rooms_index', [], Response::HTTP_SEE_OTHER);
    }
}
