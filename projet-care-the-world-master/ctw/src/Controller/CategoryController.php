<?php

namespace App\Controller;

use App\Form\FilterType;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    /**
     * @Route("/category", name="category")
     */

class CategoryController extends AbstractController
{
    /**
     * @Route("/{id}", name="_read", requirements={"id"="\d+"})
     */
    public function read(Request $request, int $id): Response
    {
        $idCategory = $id;

        $this->get('session')->set('previousUrl', 'category');
        $this->get('session')->set('categories', $id);

        return $this->redirectToRoute('event_browse');
        
    }
}

