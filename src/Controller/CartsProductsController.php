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
    #[Route('/{id}', name: 'app_carts_products_delete', methods: ['POST'])]
    public function delete(Request $request, CartsProducts $cartsProduct, CartsProductsRepository $cartsProductsRepository): Response
    {
        $userid = $this->getUser()->getId();
        if ($this->isCsrfTokenValid('delete'.$cartsProduct->getId(), $request->request->get('_token'))) {
            $cartsProductsRepository->remove($cartsProduct, true);
        }

        return $this->redirectToRoute('app_product_cart', ['id' => $userid], Response::HTTP_SEE_OTHER);
    }
}
