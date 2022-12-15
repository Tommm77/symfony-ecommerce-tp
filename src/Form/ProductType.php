<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Brand;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Choice;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => 'Enter the name'
                ]
            ])
            // ->add('excerpt')
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Enter the description'
                ]
            ])
            ->add('image', TextType::class, [
                'label' => 'Image',
                'attr' => [
                    'placeholder' => 'Enter the image'
                ]
            ])
            ->add('quantity', TextType::class, [
                'label' => 'Quantity',
                'attr' => [
                    'placeholder' => 'Enter the quantity'
                ]
            ])
            ->add('sold', TextType::class, [
                'label' => 'Sold',
                'attr' => [
                    'placeholder' => 'Enter the number of sold'
                ]
            ])
            ->add('price', TextType::class, [
                'label' => 'Price',
                'attr' => [
                    'placeholder' => 'Enter the price'
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Available' => '2',
                    'Unavailable' => '1',
                    'Draft' => '0',
                ],
            ])
                //  ->add('seller', ChoiceType::class, [
                //     'choices' => [
                //         'Admin' => 1,]] )
                //  ->add('users', ChoiceType::class, [
                //     'choices' => [
                //         'Admin' => '1',]])
            ->add('category', EntityType ::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('brand', EntityType ::class, [
                'class' => Brand::class,
                'choice_label' => 'name',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
