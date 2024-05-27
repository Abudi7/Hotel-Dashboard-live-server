<?php 
namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Rooms;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function new(Request $request, int $roomId, BookingRepository $bookingRepository, SessionInterface $session): Response
    {
        // Create a new booking entity
        $booking = new Booking();
    
        // Create the booking form
        $form = $this->createForm(BookingType::class, $booking);
    
        // Get the start date and end date from the session
        $startDate = $session->get('startDate');
        $endDate = $session->get('endDate');
    
        // Set the start date and end date values in the form
        $form->get('startdate')->setData($startDate);
        $form->get('enddate')->setData($endDate);
    
        // Handle the form submission
        $form->handleRequest($request);


        

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

            // Calculate number of nights
            $numberOfNights = $booking->getStartdate()->diff($booking->getEnddate())->days;

            // Calculate total price
            $totalPrice = $numberOfNights * $room->getPrice();

            // Construct email content
            $emailContent = "
                <p>Dear $customerName,</p>
                <p>Thank you for booking with us!</p>
                <p>Here are your booking details:</p>
                <ul>
                    <li>Number of Nights: $numberOfNights</li>
                    <li>Price per Night: $" . number_format($room->getPrice(), 2) . "</li>
                    <li>Total Price Paid: $" . number_format($totalPrice, 2) . "</li>
                </ul>
                <p>If you have any questions or need further assistance, feel free to contact us.</p>
                <p>Best regards,<br>Hotel Dashboard Team</p>
            ";

            // Send email with invoice and thank you message
            $email = (new Email())
                ->from('info-Alshalal-Hiess@hotel-dashboard.at')
                ->to($user->getEmail())
                ->subject('Booking Confirmation and Invoice')
                ->html($emailContent);

            $mailer->send($email);

            // Return a response if needed
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
    public function success(BookingRepository $bookingRepository): Response
    {
        // Fetch the latest booking details from the repository
        $latestBooking = $bookingRepository->findLatestBooking();

        // Check if a booking was found
        if ($latestBooking === null) {
            throw $this->createNotFoundException('No booking found.');
        }
        // Get the logged-in user
        $user = $this->security->getUser();

        // Calculate the price per night
        $room = $latestBooking->getRooms();
        $roomPrice = $room->getPrice();
        $numberOfNights = $latestBooking->getNumberOfNights();
        $pricePerNight = $roomPrice;

        // Calculate total price paid
        $totalPrice = $roomPrice * $numberOfNights;

        // Generate invoice number
        $invoiceNumber = 'Hotel-Dashboard ' . mt_rand(1000, 9999);

        // Pass the necessary data to the template
        return $this->render('booking/success.html.twig', [
            'invoiceNumber' => $invoiceNumber,
            'username' => $user->getName() . ' ' . $user->getSurname(),
            'booking' => $latestBooking,
            'pricePerNight' => $pricePerNight,
            'numberOfNights' => $numberOfNights,
            'totalPrice' => $totalPrice,
        ]);
    }



    #[Route('/booking/check-availability', name: 'app_booking_check_availability', methods: ['POST'])]
    public function checkAvailability(Request $request, BookingRepository $bookingRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $startDate = new \DateTime($data['startdate']);
        $endDate = new \DateTime($data['enddate']);

        // Retrieve available rooms by date range from the repository
        $availableRooms = $bookingRepository->findAvailableRoomsByDateRange($startDate, $endDate);

        return new JsonResponse(['availableRooms' => $availableRooms]);
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
