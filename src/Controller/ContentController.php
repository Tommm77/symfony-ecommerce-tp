<?php

namespace App\Controller;

use App\Entity\CartsProducts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CartsProductsRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AddCartType;
use App\Repository\BrandRepository;
use App\Repository\CategoryRepository;
use App\Entity\User;
use App\Form\CartQuantityType;

class ContentController extends AbstractController
{
    #[Route('/', name: 'app_product_home')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('content/home.html.twig', [
            'controller_name' => 'ContentController',
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/{id}/cart', name: 'app_product_cart', methods: ['GET', 'POST'])]
    public function cart(User $user, Request $request, CartsProductsRepository $cartProducts): Response
    {
        $cart = $user->getCart();
        $cp = $cart->getCartsProducts();
        $cparray = $cp->toArray();
        $quantities = [];
        foreach ($cparray as $cartsProducts) {
            $quantities[$cartsProducts->getProduct()->getId()] = $cartsProducts->getQuantity();
        }

        // $form = $this->createForm(CartQuantityType::class, $cart);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     //$cartProducts->save($cp, true);
        //     $this->addFlash('success', 'Quantité modifiée avec succès');
        //     return $this->redirectToRoute('app_product_cart', [], Response::HTTP_SEE_OTHER);
        // }


        return $this->render('content/cart.html.twig', [
            'controller_name' => 'ContentController',
            'products' => $cp,
            'quantities' => $quantities,
        ]);
    }

    #[Route('/cart/update/', name: 'app_product_cart_update', methods: ['GET', 'POST'])]
    public function updatecart(CartsProductsRepository $cartProducts, Request $request, ProductRepository $productRepository): Response {
        $test = $request->getContent();
        $data = json_decode($test, true);
        $cp = $cartProducts->find($data['id']);
        $cp->setQuantity($data['quantity']);
        $cartProducts->save($cp, true);


        return $this->redirectToRoute('app_product_home');
    }

    #[Route('/admin', name: 'app_product_admin')]
    public function admin(): Response
    {
        return $this->render('content/product/index.html.twig', [
            'controller_name' => 'ContentController',
        ]);
    }

    #[Route('/admin/product', name: 'app_products', methods: ['GET'])]
    public function showAll(ProductRepository $productRepository): Response
    {
        return $this->render('content/product/adminproduct.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/shop', name: 'app_shop', methods: ['GET'])]
    public function showAllShop(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('content/shop.html.twig', [
            'products' => $paginator->paginate($productRepository->findAll(),
            $request->query->getInt('page', 1),6
        ),
        ]);
    }

    #[Route('/products/{id}', name: 'app_product_read', methods: ['GET', 'POST'])]
    public function show(Product $product, Request $request, CartsProductsRepository $cpRepository): Response
    {
        /** @var User $user **/
        $user = $this->getUser();
        $cart = $user->getCart();
        $cartProduct = new CartsProducts();
        $form = $this->createForm(AddCartType::class, $cartProduct);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $cartProduct->setCart($cart);
            $cartProduct->setProduct($product);
            $quantity = $form->get('quantity')->getData();
            $cartProduct->setQuantity($quantity);

            $cpRepository->save($cartProduct, true);

            return $this->redirectToRoute('app_products');
        }
        return $this->render('content/product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/{id}', name: 'app_profile', methods: ['GET'])]
    public function profile(ProductRepository $productRepository, User $user, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('content/profile.html.twig', [
            'products' => $paginator->paginate($productRepository->findBy(['seller' => $user->getId()]), $request->query->getInt('page', 1),2),
        ]);
    }
}
