<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    #[Route('/users', name: 'users')]
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    } 
    
    #[Route('/annonces/ajouter', name: 'users_ajouter_annonces')]
    public function ajouterAnnonces(Request $request)
    {
        $annonce = new Annonces;

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $annonce->SetUsers($this->getUser());
            $annonce->setActive(false);
            $images = $form->get('images')->getData();
            if($images !== null){
                foreach($images as $image){
                    $fichier= md5(uniqid()).'.'.$image->guessExtension();
                    $image->move($this->getParameter('annonces_folder'),$fichier);
                    $img = new Image();
                    $img->setName($fichier);
                    $annonce->addImage($img);
                }
            }
            
            $em= $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            return $this->redirectToRoute('users');
        }


        return $this->render('users/annonces/ajout.html.twig', [
            "form" => $form->createView(),

        ]);
    }

  
    #[Route('/annonces/edit/{id}', name: 'users_edit_annonces')]
    public function editAnnonces(Annonces $annonce, Request $request)
    {

        $form = $this->createForm(AnnoncesType::class, $annonce);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $annonce->SetUsers($this->getUser());
            $annonce->setActive(false);
            $images = $form->get('images')->getData();
            if($images !== null){
                foreach($images as $image){
                    $fichier= md5(uniqid()).'.'.$image->guessExtension();
                    $image->move($this->getParameter('annonces_folder'),$fichier);
                    $img = new Image();
                    $img->setName($fichier);
                    $annonce->addImage($img);
                }
            }
            
            $em= $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            return $this->redirectToRoute('users');
        }


        return $this->render('users/annonces/edit.html.twig', [
            "form" => $form->createView(),
            "annonce" => $annonce

        ]);
    }


  
    #[Route('/annonces/deleteimg/{id}', name: 'users_delete_image')]
    public function deleteimg(Image $img, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if($this->isCsrfTokenValid('delete'.$img->getId(), $data['_token'])){
            $nom = $img->getName();
            unlink($this->getParameter('annonces_folder').'/'.$nom);
            $em=$this->getDoctrine()->getManager();
            $em->remove($img);
            $em->flush();
            
        return new JsonResponse(['success', 1]);
        }else{
            return new JsonResponse(['error'=> "token invalide"], 400);
        }
    }






    #[Route('/user/editprofile', name: 'users_edit')]
    public function editprofile(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em= $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('message', "Profil mis à jour");
            return $this->redirectToRoute('users');
        }


        return $this->render('users/editprofile.html.twig', [
            "form" => $form->createView(),

        ]);
    }
    

    #[Route('/user/editpassword', name: 'users_pass_modifier')]
    public function editpass(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        if($request->isMethod('POST')){
            $user = $this->getUser();
            if($request->request->get('password') == $request->request->get('password2')){
            $user->setPassword($passwordEncoder->encodePassword($user, $request->request->get('password')));
            $em= $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('message', 'Mot de passe mis a jour avec succès');
            return $this->redirectToRoute('users');
            }else{
                $this->addFlash('verify_password_error', 'les deux mots de passe ne sont pas identiques');
            }
        }
      
        return $this->render('users/editmdp.html.twig', [
        ]);
    }
    
}
