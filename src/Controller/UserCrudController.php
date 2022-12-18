<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\CartRepository;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user/crud')]
class UserCrudController extends AbstractController
{
    #[Route('/', name: 'app_user_crud_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_crud/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_crud/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_crud/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_crud_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository, CartRepository $cartRepository, ResetPasswordRequestRepository $rp): Response
    {
        $userid = $user->getId();
        $productCreateByUser = $userRepository->find($userid)->getProducts();
        $usercart = $user->getCart();
        if ($usercart != null) {
            $productcartuser = $usercart->getCartsProducts();
        }
        $requestuser = $rp->findBy(['user' => $user]);
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
if ($usercart != null) {
    foreach ($productcartuser as $productcart) {
        $productcart->setCart(null);
    }
}
            foreach ($requestuser as $request) {
                $rp->remove($request, true);
            }
            foreach ($productCreateByUser as $product) {
                $product->setSeller(null);
            }
            $userRepository->remove($user, true);
            if ($usercart != null) {
                $cartRepository->remove($usercart, true);
            }
        }
        if ( $user == $this->getUser()) {
            return $this->redirectToRoute('app_logout', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute('app_user_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
