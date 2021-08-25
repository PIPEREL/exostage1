<?php

namespace App\Controller\admin;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Repository\AnnoncesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AnnoncesController extends AbstractController
{
    #[Route('/admin/annonces', name: 'admin_annonces_home')]
    public function index(AnnoncesRepository $annoncesRepository): Response
    {
        return $this->render('admin/annonces/index.html.twig', [
           'annonces' => $annoncesRepository->findAll(),
        ]);
    }   
    
    #[Route('/admin/annonces/switch/{id}', name: 'admin_annonces_activer')]
    public function activerannonce(Annonces $annonce, Request $request)
    {
        $annonce->setActive($annonce->getActive()?false:true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($annonce);
        $em->flush();
        return new Response("true");
    }    
    
    
    #[Route('/admin/annonces/supprimer/{id}', name: 'admin_annonces_supprimer')]
    public function supprimerAnnonce(Annonces $annonce)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($annonce);
        $em->flush();
        $this->addFlash('message', 'annonce supprimée avec succès');
        return $this->redirectToRoute("admin_annonces_home");
    }

      // #[Route('/admin/annoncess/ajout', name: 'admin_annonces_ajout')]
    // public function ajoutannonces(Request $request)
    // {
    //     $annonces = new Annonces;

    //     $form = $this->createForm(AnnoncesType::class, $annonces);

    //     $form->handleRequest($request);

    //     if($form->isSubmitted() && $form->isValid()){
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($annonces);
    //         $em->flush();
    //         return $this->redirectToRoute('admin_annonces_home');
    //     }

    //     return $this->render('admin/annonces/ajout.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }

    // #[Route('/admin/annonces/modifier/{id}', name: 'admin_annonces_modifier')]
    // public function modifannonces(Annonces $annonce, Request $request)
    // {

    //     $form = $this->createForm(AnnoncesType::class, $annonce);

    //     $form->handleRequest($request);

    //     if($form->isSubmitted() && $form->isValid()){
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($annonce);
    //         $em->flush();
    //         return $this->redirectToRoute('admin_annonces_home');
    //     }

    //     return $this->render('admin/annonces/ajout.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }


}
