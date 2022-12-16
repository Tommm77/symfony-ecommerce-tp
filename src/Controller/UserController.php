<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use Stripe\StripeClient;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Stripe;

class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $user->setCreatedAt(new \DateTimeImmutable("now"));
        $user->setUpdatedAt(new \DateTimeImmutable("now"));

        if ($form->isSubmitted() && $form->isValid()) {
            $cart = new Cart();
            $user->setCart($cart);
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/profile/{id}/checkout={total}', name: 'app_profile_checkout')]
    public function cartCheckout(User $user, $total) : Response {
        // $currentUser = $this->getUser()->getId();
        // if ($currentUser !== $user->getId()) {
        //     return $this->redirectToRoute('app_product_cart', ['id' => $currentUser]);
        // }
        // // $stripe = new StripeClient($this->getParameter('stripe_sk'));
        // $stripe = $this->getParameter('stripe_sk');
        // // dd($stripe_key);

        // // $stripe = new \Stripe\StripeClient('sk_test_51MFEq4GwodRabcetF8riN0uXwTToj6aVcgnzVVmP4Oj424ssUrWVx02x3JHbXUJZB1yri9LS5Nav55WogLvylJXJ00VMD7ZGBk');

        // // dd($stripe);

        // return $this->render('content/stripe.html.twig'
        //     , [
        //         'stripe' => $stripe
        //     ]
        // );
        $cart = $user->getCart();
        $cp = $cart->getCartsProducts();
        $cparray = $cp->toArray();
        $quantities = [];
        foreach ($cparray as $cartsProducts) {
            array_push($quantities, $cartsProducts->getQuantity() * $cartsProducts->getProduct()->getPrice());
            //$quantities[$cartsProducts->getProduct()->getId()] = $cartsProducts->getQuantity();
        }

        $totalPrice = array_sum($quantities);
        $stripe_pk = $this->getParameter('stripe_pk');
            return $this->render('content/stripe.html.twig', [
                'stripe_key' => $stripe_pk,
                'total' => $total
            ]);
    }

    #[Route('/profile/checkout/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    public function createCharge(Request $request)
    {
        $user = $this->getUser();
        $cart = $user->getCart();
        $cp = $cart->getCartsProducts();
        $cparray = $cp->toArray();
        $quantities = [];
        foreach ($cparray as $cartsProducts) {
            array_push($quantities, $cartsProducts->getQuantity() * $cartsProducts->getProduct()->getPrice());
            //$quantities[$cartsProducts->getProduct()->getId()] = $cartsProducts->getQuantity();
        }

        $totalPrice = array_sum($quantities);
        $stripe_sk = $this->getParameter('stripe_sk');
        Stripe\Stripe::setApiKey($stripe_sk);
        Stripe\Charge::create ([
                "amount" => $totalPrice * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test",
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->render('content/succes.html.twig');
    }
}
