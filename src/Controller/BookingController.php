<?php 
namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Rooms;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\RoomsRepository;
use App\Repository\UserRepository;
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
    public function new(Request $request, int $roomId,RoomsRepository $roomsRepository ,BookingRepository $bookingRepository, SessionInterface $session, MailerInterface $mailer,UserRepository $userRepository): Response
    {
        // Create a new booking entity
        $booking = new Booking();

        $room = $roomsRepository->find($roomId);
    
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

            $customerName = $userRepository->findByEmail($user->getUserIdentifier());
            
            $booking->setCustomername($customerName->getEmail());
            $booking->setRooms($room);
            $booking->setSwitcher("1");
            $username = $customerName->getName() . $customerName->getSurname();

            $now = new DateTime('now', new DateTimeZone(date_default_timezone_get()));
            $booking->setCreatedat($now);

            $room = $this->entityManager->getRepository(Rooms::class)->find($roomId);
            $booking->setRooms($room);
            $invoiceNumber = 'Hotel-Dashboard ' . mt_rand(1000, 9999);
            $booking->setInvoicenumber($invoiceNumber);

            $address = $booking->getAddress();
            $this->entityManager->persist($address);
            $this->entityManager->persist($booking);
            $this->entityManager->flush();

            // Calculate number of nights
            $numberOfNights = $booking->getStartdate()->diff($booking->getEnddate())->days;

            // Calculate total price
            $totalPrice = $numberOfNights * $room->getPrice();

            // <!-- Construct email content -->
            $emailContent = "
                <div style='font-family: Arial, sans-serif;'>
                    <p style='color: #333; font-size: 16px;'>Dear $username,</p>
                    <p style='color: #333; font-size: 16px;'>Thank you for booking with us!</p>
                    <div style='background-color: #f5f5f5; padding: 10px; border-radius: 5px;'>
                        <p style='color: #333; font-size: 16px;'>Here are your booking details:</p>
                        <ul style='list-style-type: none; padding-left: 0;'>
                            <li style='color: #333; font-size: 16px;'>Number of Nights: $numberOfNights</li>
                            <li style='color: #333; font-size: 16px;'>Price per Night: $" . number_format($room->getPrice(), 2) . "</li>
                            <li style='color: #333; font-size: 16px;'>Total Price Paid: $" . number_format($totalPrice, 2) . "</li>
                        </ul>
                    </div>
                    <p style='color: #333; font-size: 16px;'>If you have any questions or need further assistance, feel free to contact us.</p>
                    <p style='color: #333; font-size: 16px;'>Best regards,<br>Hotel Dashboard Team</p>
                </div>
            ";


            // Send email with invoice and thank you message
            $email = (new Email())
                ->from('info@hotel-dashboard.at')
                ->to($user->getEmail())
                ->subject('Booking Confirmation and Invoice')
                ->html($emailContent);
            //dump($email);die;
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
            'room' => $room
        ]);
    }
    
    #[Route('/booking/toggle-visibility', name: 'app_booking_toggle_visibility')]
    public function toggleVisibility(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        $bookingId = $data['id'];
        $switcherStatus = $data['switcher'];

        $booking = $em->getRepository(Booking::class)->find($bookingId);

        if ($booking) {
            $booking->setSwitcher($switcherStatus);
            $em->flush();

            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['success' => false], 400);
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
        $invoiceNumber = $latestBooking->getInvoicenumber();

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
    public function delete(Request $request, Booking $booking, Security $security): Response
    {
        // Check if the user has the admin role
        if ($security->isGranted('ROLE_ADMIN')) {
            // Replace this part with CSRF token validation as needed
            // if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            
            // Delete the booking record
            $this->entityManager->remove($booking);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_booking_filter');
        } else {
            // If the user is not an admin, deny access or handle accordingly
            throw new AccessDeniedException('You do not have permission to delete this booking.');
        }
    }
    

    #[Route('/booking/filter', name: 'app_booking_filter')]
    public function filter(Request $request, BookingRepository $bookingRepository, UserRepository $userRepository): Response
    {
        // Check if there's an authenticated user
        if (!$this->getUser()) {
            // Handle the case where there's no authenticated user (e.g., redirect to login)
            return $this->redirectToRoute('app_login');
        }

        // Fetch bookings with associated room and user data
        $bookings = $bookingRepository->findBookingsWithRoomAndUser();

        // Initialize an array to store users associated with bookings
        $users = [];

        // Iterate over each booking to access its associated user
        foreach ($bookings as $booking) {
            // Accessing user associated with the booking
            $userEmail = $booking->getCustomername();
            $user = $userRepository->findOneByEmail($userEmail);
            
            // Storing user data in the array
            $users[] = $user;
            
        }

        // Render template with filtered bookings and associated users
        return $this->render('booking/filter.html.twig', [
            'bookings' => $bookings,
            'users' => $users
        ]);
    }



}
