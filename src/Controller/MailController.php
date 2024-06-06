<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class MailController extends AbstractController
{
    /**
     * @Route("/send-email", name="send_email", methods={"GET", "POST"})
     */
    #[Route('/send-email', name: 'send_email', methods: ['GET', 'POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer, LoggerInterface $logger)
    {
        // Define variables for "from" and "to" headers
        $from = 'info@hotel-dashboard.at';
        $to = 'casper.king14@gmail.com';

        // Define the email subject
        $subject = 'Test Email';

        // Define the email message body
        $message = 'This is a test email message.';

        if ($request->isMethod('POST')) {
            $email = (new Email())
                ->from($from) // Use the $from variable
                ->to($to) // Use the $to variable
                ->subject($subject) // Use the $subject variable
                ->text($message); // Use the $message variable for the email body

            try {
                // Try sending the email using Symfony Mailer
                $mailer->send($email);

                // Log success
                $logger->info('Email sent successfully.');

                $this->addFlash('success', 'Email sent successfully.');
            } catch (TransportExceptionInterface $e) {
                // Log failure
                $logger->error('Failed to send email: ' . $e->getMessage());

                $this->addFlash('error', 'Failed to send email.');
            } catch (\Exception $e) {
                // Log any other exception
                $logger->error('An unexpected error occurred: ' . $e->getMessage());

                $this->addFlash('error', 'An unexpected error occurred.');
            }
        }

        // Log: Rendering the form
        $logger->debug('Rendering send_email form.');

        return $this->render('send_email.html.twig');
    }
}
