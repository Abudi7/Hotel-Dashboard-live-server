<?php

// src/Controller/CustomersController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditCustomerFormType;
use App\Form\UserProfileFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomersController extends AbstractController
{
    #[Route('/customers', name: 'app_customers')]
    public function index(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll();

        // Get all available roles
        $allRoles = $this->getParameter('security.role_hierarchy.roles');

        // Handle form submission
        if ($request->isMethod('POST')) {
            $userId = $request->request->get('user_id');
            $user = $userRepository->find($userId);
            if (!$user) {
                throw $this->createNotFoundException('User not found');
            }

            $user->setRoles($request->request->get('roles'));
            $entityManager->flush();

            // Redirect to avoid resubmission
            return $this->redirectToRoute('app_customers');
        }

        return $this->render('customers/index.html.twig', [
            'controller_name' => 'CustomersController',
            'users' => $users,
            'allRoles' => $allRoles,
        ]);
    }
    #[Route('/customer/edit/{id}', name: 'app_edit_customer')]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Create the edit form for the selected user
        $form = $this->createForm(EditCustomerFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTime('now', new \DateTimeZone(date_default_timezone_get())));
            // Save the updated user data
            $entityManager->flush();

            // Redirect to the customers list page
            return $this->redirectToRoute('app_customers');
        }

        return $this->render('customers/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
