<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends AbstractController
{
    #[Route('/', name: 'app_product_home')]
    public function index(): Response
    {
        return $this->render('content/home.html.twig', [
            'controller_name' => 'ContentController',
        ]);
    }

    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function showAll(ProductRepository $productRepository): Response
    {
        return $this->render('content/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_read', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
        ]);
    }
}
