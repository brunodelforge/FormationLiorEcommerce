<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSuscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }

    public function sendEmail(ProductViewEvent $productView)
    {
        // $email = new TemplatedEmail();

        // $email->from(new Address("contact@mail.com", "Infos de la boutique"))
        //     ->to("admin@mail.com")
        //     ->text("un visiteur est en train de voir la page du produit n°" . $productView->getProduct()->getId())
        //     ->htmlTemplate("emails/product_view.html.twig")
        //     ->context(['product' => $productView->getProduct()])
        //     ->subject("Viste du produit n°" . $productView->getProduct()->getId());

        // $this->mailer->send($email);

        $this->logger->info("Email envoyé à l'admin pour le produit" . $productView->getProduct()->getId());
    }
}
