<?php 
// src/Controller/ContactController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     */
    #[Route('/contact', name: 'contact', methods: ['GET', 'POST'])]
    public function index(Request $request, MailerInterface $mailer, ValidatorInterface $validator): Response
    {
        $formData = [
            'name' => null,
            'email' => null,
            'message' => null,
        ];

        // If the form is submitted, process the data
        if ($request->isMethod('POST')) {
            $formData['name'] = $request->request->get('name');
            $formData['email'] = $request->request->get('email');
            $formData['message'] = $request->request->get('message');

            // Validate the form data
            $errors = $validator->validate($formData);

            // If there are no validation errors, send the email
            if (count($errors) === 0) {
                $email = (new Email())
                    ->from($formData['email'])
                    ->to('info@hotel-dashboard.at')
                    ->subject('Contact Form Submission')
                    ->html("Name: {$formData['name']}<br>Email: {$formData['email']}<br>Message: {$formData['message']}");

                $mailer->send($email);

                $this->addFlash('success', 'Your message has been sent successfully!');
                return $this->redirectToRoute('contact');
            } else {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('contact/index.html.twig');
    }
}
