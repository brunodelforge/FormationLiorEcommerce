<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    protected $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
    /**
     * @Route("/hello/{name<\w+>?World}", name="hello")
     */
    public function hello($name, LoggerInterface $logger, Slugify $slugify, Environment $twig)
    {
        //$slugify = new Slugify();
        dump($twig);
        dump($slugify->slugify("µHello World tout va bien #f !!!"));
        $logger->info("Mon message de log !"); //dans le terminal du "server"
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello $name");
    }
}
