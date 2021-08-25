<?php

namespace App\Controller\admin;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CategoriesController extends AbstractController
{
    #[Route('/admin/categories', name: 'admin_categorie_home')]
    public function index(CategoriesRepository $categoryRepository): Response
    {
        return $this->render('admin/categories/index.html.twig', [
           'categories' => $categoryRepository->findAll(),
        ]);
    }   
    
    
    #[Route('/admin/categories/ajout', name: 'admin_categories_ajout')]
    public function ajoutcategorie(Request $request)
    {
        $categorie = new Categories;

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('admin_categorie_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/categories/modifier/{id}', name: 'admin_categories_modifier')]
    public function modifcategories(Categories $categorie, Request $request)
    {

        $form = $this->createForm(CategoriesType::class, $categorie);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('admin_categorie_home');
        }

        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
