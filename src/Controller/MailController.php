<?php
// src/Controller/MailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailController extends AbstractController
{
    /**
     * @Route("/send-email", name="send_email")
     */
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('alshalalhiess@gmail.com') // Your Gmail address
            ->to('casper.king14@gmail.com')
            ->subject('Test Email')
            ->text('This is a test email.');

        $mailer->send($email);

        return $this->json(['message' => 'Email sent successfully!']);
    }
}
