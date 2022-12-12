<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Brand;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function __construct(protected ManagerRegistry $registry, protected UserPasswordHasherInterface $passwordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('teest@gmail.com');
        $user->setFirstname('Test');
        $user->setLastname('Test');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'test'));
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $ur = $this->registry->getRepository(User::class);
        $ur->save($user, true);


        $category = new Category();
        $category->setName('Category 1');
        $cr = $this->registry->getRepository(Category::class);
        $cr->save($category, true);

        $brand = new Brand();
        $brand->setName('Brand 1');
        $br = $this->registry->getRepository(Brand::class);
        $br->save($brand, true);


        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setExcerpt('Excerpt ' . $i);
            $product->setDescription('Description ' . $i);
            $product->setImage('https://image-cdn.hypb.st/https%3A%2F%2Fhypebeast.com%2Fimage%2F2021%2F01%2Femerging-sneaker-brands-notwoways-roscomar-scry-lab-viron-good-news-1.jpg?w=1600&cbr=1&q=90&fit=max');
            $product->setQuantity(10);
            $product->setSold(0);
            $product->setPrice(10.99);
            $product->setStatut(1);
            $product->setCreatedAt(new \DateTimeImmutable());
            $product->setUpdateAt(new \DateTimeImmutable());
            $product->setSeller($user);
            $product->setCategory($category);
            $product->setBrand($brand);
            $manager->persist($product);
        }
        $manager->flush();
    }
}
