<?php
// src/Controller/MailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
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
            // Use a default recipient email for testing purposes
            $recipientEmail = 'casper.king14@gmail.com'; // Replace with your default test email

            // Create an Email object
            $email = (new TemplatedEmail())
                ->from('info@hotel-dashboard.at')
                ->to($recipientEmail)
                ->subject('Test Email Subject!')
                ->text('This is the plain text version of the test email message.')
                ->html('
                    <h1 style="color: #fff300; background-color: #0073ff; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
                    This is a test email.
                    </h1>
                    <p>This email is sent for testing purposes.</p>
                    <img src="cid:Image_Name_1" style="width: 600px; border-radius: 50px">
                    <br>
                    <img src="cid:Image_Name_2" style="width: 600px; border-radius: 50px">
                    <h1 style="color: #ff0000; background-color: #5bff9c; width: 500px; padding: 16px 0; text-align: center; border-radius: 50px;">
                    The End!
                    </h1>
                ');
                $mailer->send($email);
                return $this->render('send_email.html.twig');
        } else {
            // Handle GET request (optional)
            return $this->render('send_email.html.twig');
        }
    }
}
