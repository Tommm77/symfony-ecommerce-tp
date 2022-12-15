<?php

namespace App\Controller;

use App\Entity\CartsProducts;
use App\Form\CartsProductsType;
use App\Repository\CartsProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/carts/products')]
class CartsProductsController extends AbstractController
{
    #[Route('/', name: 'app_carts_products_index', methods: ['GET'])]
    public function index(CartsProductsRepository $cartsProductsRepository): Response
    {
        return $this->render('carts_products/index.html.twig', [
            'carts_products' => $cartsProductsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_carts_products_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CartsProductsRepository $cartsProductsRepository): Response
    {
        $cartsProduct = new CartsProducts();
        $form = $this->createForm(CartsProductsType::class, $cartsProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartsProductsRepository->save($cartsProduct, true);

            return $this->redirectToRoute('app_carts_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('carts_products/new.html.twig', [
            'carts_product' => $cartsProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carts_products_show', methods: ['GET'])]
    public function show(CartsProducts $cartsProduct): Response
    {
        return $this->render('carts_products/show.html.twig', [
            'carts_product' => $cartsProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_carts_products_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CartsProducts $cartsProduct, CartsProductsRepository $cartsProductsRepository): Response
    {
        $form = $this->createForm(CartsProductsType::class, $cartsProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cartsProductsRepository->save($cartsProduct, true);

            return $this->redirectToRoute('app_carts_products_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('carts_products/edit.html.twig', [
            'carts_product' => $cartsProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_carts_products_delete', methods: ['POST'])]
    public function delete(Request $request, CartsProducts $cartsProduct, CartsProductsRepository $cartsProductsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartsProduct->getId(), $request->request->get('_token'))) {
            $cartsProductsRepository->remove($cartsProduct, true);
        }

        return $this->redirectToRoute('{id}/cart', [], Response::HTTP_SEE_OTHER);
    }
}
