<?php

namespace App\Controller;

use App\Form\RegistrationType;
use App\Entity\Users;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/{page}', name: 'registration', requirements: ['page' => 'checkout|home|account'])]
    public function checkout(
        string $page,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ManagerRegistry $doctrine
    ) {
        // Vérifie si la page demandée est valide
        $allowedPages = ['checkout', 'profile', 'account']; // Ajoute ici les pages autorisées
        if (!in_array($page, $allowedPages)) {
            throw $this->createNotFoundException('Page not found.');
        }

        // Créer un nouvel utilisateur
        $user = new Users();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hashage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            // Sauvegarde de l'utilisateur
            $entityManager = $doctrine->getManager();
            try {
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                return $this->render("$page.html.twig", [
                    'registrationForm' => $form->createView(),
                    'error' => 'Une erreur est survenue lors de l\'inscription.',
                ]);
            }

            // Redirection après l'inscription réussie
            return $this->redirectToRoute('home');
        }

        // Afficher le formulaire sur la page demandée
        return $this->render("$page.html.twig", [
            'registrationForm' => $form->createView(),
        ]);
    }



}
