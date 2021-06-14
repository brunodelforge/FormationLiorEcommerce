<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$category->setSlug(strtolower($slugger->slug($category->getName())));

            $manager->persist($category);
            $manager->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView,

        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, Request $request, CategoryRepository $categoryRepository, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) throw new NotFoundHttpException("Cette catgégorie n'existe pas");

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$category->setSlug(strtolower($slugger->slug($category->getName())));

            $manager->flush();

            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
