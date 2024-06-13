<?php

namespace App\Controller;

use App\Entity\Lostitem;
use App\Form\LostitemType;
use App\Repository\LostitemRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use App\Entity\Rooms; // Import Rooms entity if not already imported

class LostitemController extends AbstractController
{
    #[Route('/lostitem', name: 'app_lostitem_index', methods: ['GET'])]
    public function index(LostitemRepository $lostitemRepository): Response
    {
        return $this->render('lostitem/index.html.twig', [
            'lostitems' => $lostitemRepository->findAll(),
        ]);
    }

    #[Route('/lostitem/new', name: 'app_lostitem_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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
    public function edit(Request $request, Lostitem $lostitem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LostitemType::class, $lostitem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * @Route("/fetch-latest-booking/{roomId}", name="fetch_latest_booking", methods={"GET"})
     */
    #[Route('/fetch-latest-booking/{roomId}', name: 'fetch_latest_booking', methods: ['GET'])]
    public function fetchLatestBookingAction(Request $request, BookingRepository $bookingRepository, int $roomId): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $room = $entityManager->getRepository(Rooms::class)->find($roomId);

        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Fetch the latest booking for the room
        $latestBooking = $bookingRepository->findLatestBookingByRoom($room);

        // Prepare data to send back as JSON response
        $responseData = [
            'ownerName' => $latestBooking ? $latestBooking->getOwnerName() : '',
            'ownerContact' => $latestBooking ? $latestBooking->getOwnerContact() : '',
        ];

        return new JsonResponse($responseData);
    }
}
