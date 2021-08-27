<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Image;
use App\Entity\Annonces;
use App\Form\AnnoncesType;
use App\Form\EditProfileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    

    #[Route('/users/data', name: 'users_data')]
    public function userdata(): Response
    {
        return $this->render('users/data.html.twig', [
            
        ]);
    } 


    #[Route('/users/data/download', name: 'users_data_download')]
    public function userdatadownload()
    {
       $pdfOptions = new Options;
       $pdfOptions->set('defaultFont', 'Arial');
       $pdfOptions->isRemoteEnabled(true);

       $dompdf = new Dompdf($pdfOptions);
       $context = stream_context_create([
           'ssl'=>[
               'verify_peer' => FALSE,
               'verify_perr_name' => FALSE,
               'allow_self_signed' => True
           ]
           ]);
        $dompdf->setHttpContext($context);

        $html = $this->renderView('users/download.html.twig');
        $dompdf->loadhtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fichier = 'user-data-'.$this->getUser()->getId().'.pdf';

        $dompdf->stream($fichier, ['Attachment'=>true]);

        return new Response();

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


  
    #[Route('/annonces/deleteimg/{id}', name: 'annonce_delete_image')]
    public function deleteimg(Image $img, Request $request): Response
    {
        if($this->isCsrfTokenValid('delete'.$img->getId(),$request->request->get('_token'))){
            $nom = $img->getName();
            unlink($this->getParameter('annonces_folder').'/'.$nom);
            $em=$this->getDoctrine()->getManager();
            $em->remove($img);
            $em->flush();
            
        }
        return $this->redirectToRoute('users_edit_annonces', array('id'=> $img->getAnnonces()->getId() ) , Response::HTTP_SEE_OTHER);
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
