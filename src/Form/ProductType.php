<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            // ->add('createdAt')
            // ->add('updateAt')
            // ->add('seller')
            // ->add('users')
            // ->add('category')
            // ->add('brand')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
