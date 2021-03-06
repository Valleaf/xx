<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'user_profile')]
    public function index(): Response
    {
        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profil/modifier',name: 'user_profile_modify')]
    public function modifyProfile(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response{



        $user = $this->getUser();
        $userForm = $this->createForm(UserType::class,$user);

        $userForm->handleRequest($request);


        if($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $userForm->get('plainPassword')->getData()
                )
            );
            $em->persist($user);
            $em->flush();
            $this->addFlash('success','Le profil a bien été modifié!');
            return $this->redirectToRoute('user_profile',[]);
        }

        return $this->render('user/modify.html.twig',["userForm"=>$userForm->createView()]);

    }
}
