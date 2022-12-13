<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('excerpt')
            ->add('description')
            ->add('image')
            ->add('quantity')
            ->add('sold')
            ->add('price')
            ->add('statut')
            ->add('createdAt')
            ->add('updateAt')
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
