<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProduitRepository $produitRepository): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo_prod = $form->get('photo')->getData();
            $originalFilename = $photo_prod->getClientOriginalName();
            $newFilename = $originalFilename.'-'.uniqid().'.'.$photo_prod->getClientOriginalExtension();
            $photo_prod->move($this->getParameter('images_directory'), $newFilename);
            $produit->setPhoto($newFilename);

            $produitRepository->save($produit, true);

            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_show', methods: ['GET'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit, [
            'photo_value' => $produit->getPhoto(), // <--- passer la valeur actuelle de la photo du produit
        ]);
   
        
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid())
       {
            $produit = $form->getData();
                        // Check if the photo field has a value
                        $photo = $form->get('photo')->getData();
                        if ($photo) {
                            $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                            $newFilename = $originalFilename.'-'.uniqid().'.'.$photo->getClientOriginalExtension();
                            $photo->move(
                                $this->getParameter('images_directory'),
                                $newFilename
                            );
                            $produit->setPhoto($newFilename);
                        }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
            return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, ProduitRepository $produitRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $produitRepository->remove($produit, true);
        }

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
