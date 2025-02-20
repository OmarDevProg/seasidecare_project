<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface; // Import the logger

class OrderController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/place_order', name: 'place_order', methods: ['POST'])]
    public function placeOrder(Request $request, UsersRepository $ur): Response
    {
        // Vérifier si l'utilisateur est connecté via la session
        $session = $request->getSession();
        if (!$session->has('id')) {
            return $this->json(['success' => false, 'message' => 'You must be logged in to place an order.']);
        }

        // Récupérer l'utilisateur depuis la session
        $userId = $session->get('id');
        $user = $ur->find($userId);

        if (!$user) {
            return $this->json(['success' => false, 'message' => 'User not found.']);
        }

        // Récupérer les données JSON envoyées par le frontend
        $data = json_decode($request->getContent(), true);

        // Vérifier si les données nécessaires sont présentes
        $cartItems = $data['items'] ?? [];
        $subtotal = $data['subtotal'] ?? 0;
        $total = $data['total'] ?? 0;
        $customer = $data['customer'] ?? [];

        if (empty($cartItems)) {
            return $this->json(['success' => false, 'message' => 'The cart is empty.']);
        }

        // Parcourir chaque élément du panier et créer un ordre pour chaque
         // For tracking the orders processed
        foreach ($cartItems as $cartItem) {
            // Create a new order for each cart item
            $order = new Order();
            $order->setUser($user); // Associer l'utilisateur à la commande
            $order->setItems([$cartItem]); // Process one item at a time, so we wrap it in an array
            $order->setSubtotal($subtotal); // You might want to adjust this per order
            $order->setTotal($total); // Adjust total if necessary for each order
            $order->setCustomer($customer);

            // Save the order to the database
            try {
                $this->entityManager->persist($order);  // Persist the new order
                $this->entityManager->flush();          // Save the order to the database
                $orderMessages[] = "Order for item " . $cartItem['id'] . " placed successfully.";  // Track success
            } catch (\Exception $e) {
                // Log the error if saving to the database fails
                $this->logger->error('Error saving order to database', ['exception' => $e]);
                $orderMessages[] = "Error placing order for item " . $cartItem['id']; // Track failure
            }
        }

        // Return a response JSON with the success/failure messages
        return $this->json([
            'success' => true,
            'message' => implode(', ', $orderMessages),
            'redirect' => $this->generateUrl('order_confirmation'), // Redirect to the confirmation page
        ]);
    }
}