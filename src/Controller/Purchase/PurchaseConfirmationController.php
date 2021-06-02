<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController
{
    protected $formFactory;
    protected $routerInterface;
    protected $security;
    protected $cartService;
    protected $manager;
    protected $persister;

    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory, RouterInterface $routerInterface, Security $security, CartService $cartService, PurchasePersister $persister)
    {
        $this->formFactory = $formFactory;
        $this->routerInterface = $routerInterface;
        $this->security = $security;
        $this->cartService = $cartService;
        $this->manager = $manager;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        //1. lire les données du formulaire -> formfactoryinterface / request
        $form = $this->formFactory->create(CartConfirmationType::class);

        //2. aucun produit dans le panier => sortir (cartservice)
        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $flashBag->add('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return new RedirectResponse($this->routerInterface->generate('cart_show'));
        }

        //3. si je ne suis pas connecté => sortir
        $user = $this->security->getUser();

        //4. si le formulaire n'a pas été soumis => sortir
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            return new RedirectResponse($this->routerInterface->generate('cart_show'));
        }

        //5. créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        $this->persister->storePurchase($purchase);

        return new RedirectResponse($this->routerInterface->generate('purchase_payment_form', [
            'id' => $purchase->getId()
        ]));
    }
}
