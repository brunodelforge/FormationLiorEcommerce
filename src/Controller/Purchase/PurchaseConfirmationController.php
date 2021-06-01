<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationController
{
    protected $formFactory;
    protected $routerInterface;
    protected $security;
    protected $cartService;
    protected $manager;

    public function __construct(EntityManagerInterface $manager, FormFactoryInterface $formFactory, RouterInterface $routerInterface, Security $security, CartService $cartService)
    {
        $this->formFactory = $formFactory;
        $this->routerInterface = $routerInterface;
        $this->security = $security;
        $this->cartService = $cartService;
        $this->manager = $manager;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
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
        if (!$user) throw new AccessDeniedException('Vous devez être connecté pour confirmer une commande');

        //4. si le formulaire n'a pas été soumis => sortir
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            return new RedirectResponse($this->routerInterface->generate('cart_show'));
        }

        //5. créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        //6. lier avec l'utilisateuir connecté (security)
        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());


        $this->manager->persist($purchase);

        //7. lier avec les produits dans le panier
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            $this->manager->persist($purchaseItem);
        }

        //8. enregistrer dans doctrine (entitymanager)
        $this->manager->flush();

        //9. on vide le panier
        $this->cartService->empty();

        $flashBag->add('success', 'La commande a bien été enregistrée');
        return new RedirectResponse($this->routerInterface->generate('purchase_index'));
    }
}
