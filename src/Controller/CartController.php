<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;

class CartController extends AbstractController
{ 
    
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request): Response
    { 
        // Retrieve the cart items from the session
        $cartItems = $request->getSession()->get('cart_items', []);

        // Create an array to store the cart items with quantity
        $cart = [];
        

        // Count the quantity of each item in the cart
        foreach ($cartItems as $itemId) {
            if (!isset($cart[$itemId])) {
                $cart[$itemId] = 0;
            }
            $cart[$itemId]++;
        }

        // Retrieve the actual product objects from the database using the cart item IDs
        $total=0;
        $entityManager = $this->getDoctrine()->getManager();
        $articles = $entityManager->getRepository(Produit::class)->findBy(['id' => array_keys($cart)]);
        foreach ($articles as $article) {
            $total += $article->getPrice() * $cart[$article->getId()];
        }

        return $this->render('cart/index.html.twig', ['articles' => $articles, 'cart' => $cart, 'total'=> $total]);
    }

    /**
     * @Route("/add/{id}", name="app_add")
     */
    public function add($id, Request $request)
    {
        // Retrieve the cart items from the session
        $cartItems = $request->getSession()->get('cart_items', []);

        // Add the new item to the cart items array
        $cartItems[] = $id;

        // Store the updated cart items in the session
        $request->getSession()->set('cart_items', $cartItems);

        // Redirect to the cart page
        return $this->redirectToRoute('index');
    }
    public function add2($id, Request $request)
    {
        // Retrieve the cart items from the session
        $cartItems = $request->getSession()->get('cart_items', []);

        // Add the new item to the cart items array
        $cartItems[] = $id;

        // Store the updated cart items in the session
        $request->getSession()->set('cart_items', $cartItems);

        // Redirect to the cart page
        return $this->redirectToRoute('cart');
    }
}