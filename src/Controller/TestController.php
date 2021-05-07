<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        var_dump("ca fonctionne");
        die();
    }

    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET","POST"}, host="localhost", schemes={"http","https"})
     */
    public function test(Request $request, $age)
    {
        //dump($request);
        //$age = $request->query->get('age', 0);
        //$age = $request->attributes->get('age', 0);
        //dump($age);

        return new Response("vous avez $age ans");
    }
}
