<?php
// src/Controller/MailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailController extends AbstractController
{
    /**
     * @Route("/send-email", name="send_email", methods={"GET", "POST"})
     */
    #[Route('/send-email', name: 'send_email', methods: ['GET', 'POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer)
    {
        // Check if the request is a POST request
        if ($request->isMethod('POST')) {
            // Create an Email object
            $email = (new Email())
                ->from('noreply@hotel-dashboard.at')
                ->to('casper.king14@gmail.com')
                ->subject('A Test Subject!')
                ->text('The plain text version of the message.')
                ->html('
                    <h1 style="color: #fff300; background-color: #0073ff; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
                    The HTML version of the message.
                    </h1>
                    <img src="cid:Image_Name_1" style="width: 600px; border-radius: 50px">
                    <br>
                    <img src="cid:Image_Name_2" style="width: 600px; border-radius: 50px">
                    <h1 style="color: #ff0000; background-color: #5bff9c; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
                    The End!
                    </h1>
                ');

            // Sending email with status
            try {
                // Send email
                $mailer->send($email);

                // Display custom successful message
                return $this->json(['message' => 'Email sent successfully!']);
            } catch (TransportExceptionInterface $e) {
                // Display custom error message
                return $this->json(['error' => 'Error sending email!']);
            }
        } else {
            // Handle GET request (optional)
            return $this->render('send_email.html.twig');
        }
    }
}
