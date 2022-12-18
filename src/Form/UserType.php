<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserType extends AbstractType
{

    public function __construct(protected AuthorizationCheckerInterface $authChecker)
    {
    }


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('password')
            ->add('lastname')
            ->add('firstname')
            ->add('phonenumber')
            ->add('country')
            ->add('address')
            ->add('postalcode')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('Image')
            // ->add('favorites', EntityType::class, [
            //     'class' => Product::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('cart', EntityType::class, [
            //     'class' => Cart::class,
            //     'choice_label' => 'id',
            // ])
        ;
        if ($this->authChecker->isGranted('ROLE_ADMIN')) {
            $builder
            ->add('roles', ChoiceType::class, [
                'required' => false,
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
