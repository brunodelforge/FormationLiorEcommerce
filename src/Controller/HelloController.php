<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    protected $calculator;

    public function __construct()
    {
    }
    /**
     * @Route("/hello/{name<\w+>?World}", name="hello")
     */
    public function hello($name = "World")
    {

        return $this->render("Hello.html.twig", [
            'name' => $name
        ]);
    }
}
