<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;

    public function __construct(EntityManagerInterface $manager, Security $security, CartService $cartService)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->manager = $manager;
    }

    public function storePurchase(Purchase $purchase)
    {
        //6. lier avec l'utilisateuir connecté (security)
        $purchase->setUser($this->security->getUser());

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
    }
}
