<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Brand;
use App\Form\UserType;
use App\Entity\Product;
use App\Form\BrandType;
use App\Entity\Category;
use App\Form\ProductType;
use App\Form\CategoryType;
use App\Entity\CartsProducts;
use App\Repository\CartRepository;
use App\Repository\UserRepository;
use App\Repository\BrandRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\CartsProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ActionController extends AbstractController
{
    #[Route('/admin/products/{id}/update', name: 'app_product_update', methods: ['GET', 'POST'])]
    public function updateProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $product->setUpdateAt(new \DateTimeImmutable("now"));

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/product/add/{id}', name: 'app_product_add', methods: ['GET', 'POST'])]
    public function addProductToCart(Request $request, Product $product, CartsProductsRepository $cartProductsRepository, CartRepository $cartRepository): Response
    {
    /** @var User $user **/
    $user = $this->getUser();
    $cart = $user->getCart();
    $cp = $cart->getCartsProducts()->toArray();

    if (empty($cp)) {
        $cProduct = new CartsProducts();
        $cProduct->setQuantity(1);
        $cProduct->setCart($cart);
        $cProduct->setProduct($product);
    } else {
        foreach ($cp as $cProduct) {
            if ($cProduct->getProduct()->getId() === $product->getId()) {
                $cProduct->setQuantity($cProduct->getQuantity() + 1);
                break;
            } else {
                $cProduct = new CartsProducts();
                $cProduct->setQuantity(1);
                $cProduct->setCart($cart);
                $cProduct->setProduct($product);
            }
        }
    }
    $cartProductsRepository->save($cProduct, true);
    return $this->redirectToRoute('app_product_home', [], Response::HTTP_SEE_OTHER);
}

    #[Route('/admin/products/create', name: 'app_product_create', methods: ['GET', 'POST'])]
    public function createProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $product->setCreatedAt(new \DateTimeImmutable("now"));
        $product->setUpdateAt(new \DateTimeImmutable("now"));

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/admin/brand/new', name: 'app_brand_new', methods: ['GET', 'POST'])]
    public function createBrand(Request $request, BrandRepository $brandRepository): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->save($brand, true);

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('brand/new.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/admin/brand/{id}/edit', name: 'app_brand_edit', methods: ['GET', 'POST'])]
    public function updateBrand(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brandRepository->save($brand, true);

            return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('brand/edit.html.twig', [
            'brand' => $brand,
            'form' => $form,
        ]);
    }

    #[Route('/admin/brand/{id}', name: 'app_brand_delete', methods: ['POST'])]
    public function deleteBrand(Request $request, Brand $brand, BrandRepository $brandRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$brand->getId(), $request->request->get('_token'))) {
            $brandRepository->remove($brand, true);
        }

        return $this->redirectToRoute('app_brand_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/brand-category/', name: 'app_brand_index', methods: ['GET'])]
    public function indexBrand(BrandRepository $brandRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('brand/index.html.twig', [
            'brands' => $brandRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/admin/category/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryRepository->save($category, true);

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/admin/category/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $categoryRepository->remove($category, true);
        }

        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/{id}/update/product', name: 'app_product_admin_update', methods: ['GET', 'POST'])]
    public function updateProductonAdmin(Request $request, Product $product, ProductRepository $productRepository): Response
    {
    $form = $this->createForm(ProductType::class, $product);
    $form->handleRequest($request);
    $product->setUpdateAt(new \DateTimeImmutable("now"));
    $userid = $product->getSeller()->getId();

    if ($form->isSubmitted() && $form->isValid()) {
        $productRepository->save($product, true);

        return $this->redirectToRoute('app_product_index',[],  Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('content/product/edit.html.twig', [
        'product' => $product,
        'form' => $form,
    ]);
}

    #[Route('/profile/{id}/update/product', name: 'app_product_update', methods: ['GET', 'POST'])]
    public function updateProductonProfile(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $userproudctid = $product->getSeller()->getId();
        $userid = $this->getUser()->getId();
        if ($userproudctid != $userid) {
            return $this->redirectToRoute('app_product_home', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $product->setUpdateAt(new \DateTimeImmutable("now"));
        $userid = $product->getSeller()->getId();

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_profile', ['id' => $userid], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/products/create', name: 'app_product_create_profile', methods: ['GET', 'POST'])]
    public function createProductProfile(Request $request, ProductRepository $productRepository, User $user): Response
    {
        $userid = $this->getUser()->getId();
        if ($userid !== $user->getId()) {
            return $this->redirectToRoute('app_profile', ['id' => $userid]);
        }
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $product->setCreatedAt(new \DateTimeImmutable("now"));
        $product->setUpdateAt(new \DateTimeImmutable("now"));
        $product->setSeller($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_profile', ['id' => $userid], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_profile_edit', methods: ['GET', 'POST'])]
    public function editprofile(Request $request, User $user, UserRepository $userRepository): Response
    {
        $userid = $this->getUser()->getId();
        if ($userid !== $user->getId()) {
                return $this->redirectToRoute('app_profile', ['id' => $userid]);
            }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_profile', ['id' => $userid], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
