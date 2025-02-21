<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
final class UserController extends AbstractController{


    #[Route('/admin', name: 'admin_users')]
    public function adminUsers(UserRepository $userRepository): Response
    {
        return $this->render('user/admin_users.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    
    #[Route('/{id}/make-admin', name: 'make_admin', methods: ['POST'])]
    public function makeAdmin(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('make_admin'.$user->getId(), $request->request->get('_token'))) {
            if (!in_array('ROLE_ADMIN', $user->getRoles())) {
                $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
                $entityManager->flush();
                $this->addFlash('success', 'Usuario promovido a administrador correctamente.');
            }
        }
        return $this->redirectToRoute('admin_users');
    }


    #[Route('/search', name: 'app_user_search', methods: ['GET'])]
    public function search(UserRepository $userRepository, PostRepository $postRepository, Request $request): Response
    {   
        $users = null;
        $posts = null;

        // Para comprobar si el usuario ha enviado el formulario de búsqueda
        $search = $request->query->get('search');
        // Guardamos el texto del input tipo texto en una variable
        $text = $request->query->get('find');

        if (isset($text) && $text != "") {
            if (isset($search)) {
                if ($search == "username") {
                    $users = $userRepository->findByUsername($text);
                } else { // == post
                    $posts = $postRepository->findByPost($text);
                }
            }
        }

        return $this->render('user/search.html.twig', [
            'users' => $users,
            'posts' => $posts
        ]);
    }



        
    
    
    #[Route('/{id}/follow', name: 'app_user_follow', methods: ['GET'])]
    public function follow(User $targetUser, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || $this->getUser()->getId() === $targetUser->getId()) {
            return $this->json(['error' => 'You cannot follow yourself'], 403);
        }
       
        $this->getUser()->addFollow($targetUser); 
        $entityManager->persist($this->getUser());
        $entityManager->flush();

        // return $this->json(['message' => 'User followed successfully']);
        return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    

 
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user , UserRepository $userRepository): Response
    {
        $user = $userRepository->findAll();
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


}
