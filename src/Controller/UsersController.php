<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            $em= $this->getDoctrine()->getManager();
            $em->persist($annonce);
            $em->flush();
            return $this->redirectToRoute('users');
        }


        return $this->render('users/annonces/ajout.html.twig', [
            "form" => $form->createView(),

        ]);
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
