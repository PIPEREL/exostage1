<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_home')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }   
    
    
    // #[Route('/admin/categories/ajout', name: 'admin_categories_ajout')]
    // public function ajoutcategorie(Request $request)
    // {
    //     $categorie = new Categories;

    //     $form = $this->createForm(CategoriesType::class, $categorie);

    //     $form->handleRequest($request);

    //     if($form->isSubmitted() && $form->isValid()){
    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($categorie);
    //         $em->flush();
    //         return $this->redirectToRoute('admin_home');
    //     }

    //     return $this->render('admin/categories/ajout.html.twig', [
    //         'form' => $form->createView()
    //     ]);
    // }
}
