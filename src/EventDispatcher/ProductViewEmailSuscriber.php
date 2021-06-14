<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use App\Event\PurchaseSuccessEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSuscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendViewProductEmail'
        ];
    }

    public function sendViewProductEmail(ProductViewEvent $productView)
    {
        $this->logger->info("Email envoyé pour le produit n°" . $productView->getProduct()->getId());
    }
}
