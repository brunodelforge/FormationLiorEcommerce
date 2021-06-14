<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{
    protected $manager;
    protected $cartService;

    public function __construct(CartService $cartService, EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->cartService = $cartService;
    }
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EventDispatcherInterface $eventDispatcher)
    {
        //1. Je récupère la commande
        $purchase = $purchaseRepository->find($id);

        //2. Je la fait passer en status payé
        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->manager->flush();

        //3. Je vide le panier
        $this->cartService->empty();

        // lancer un évènement pour que les autres dévelopeurs puisent agir
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $eventDispatcher->dispatch($purchaseEvent, 'purchase.success');

        //4. Je redirige avec un flash vers la liste des commandes
        $this->addFlash('success', 'Votre paiement a été confirmé');
        return $this->redirectToRoute('purchase_index', []);
    }
}
