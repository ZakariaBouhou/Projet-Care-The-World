<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Event;
use App\Form\FilterType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
    * @Route("/event", name="event")
    */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="_browse")
     */
    public function browse(Request $request, CategoryRepository $categoryRepository, EventRepository $eventRepository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(FilterType::class);

        $events = $eventRepository->findAll();

        $categories = $categoryRepository->findAll();

        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1, 20)
        );

        return $this->render('event/browse.html.twig', [
            'events' => $events,
            'categories' => $categories,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/filters/", name="_browse-filters")
     */
    public function browseByFilters(Request $request, CategoryRepository $categoryRepository, EventRepository $eventRepository, PaginatorInterface $paginator): Response
    {
        
        $form = $this->createForm(FilterType::class);
        // $form->handleRequest($request);

        // //pre-filler
        // $form->get('categories')->setData($request->query->get('category'));
        // $form->get('zipCode')->setData($request->query->get('zipCode'));

        // $form->get('city')->setData($request->query->get('city'));

        //TODO annuler le filtre (retour a la page event) 

        if($request->query->get('category') && $request->query->get('city') && $request->query->get('zipCode')) {
            //Category and zipcode
            $events = $eventRepository->findBy([
                'category' => $request->query->get('category'),
                'zipCode' => $request->query->get('zipCode'),
                'city' => $request->query->get('city'),
            ]);
        }

        if ($request->query->get('category') && !$request->query->get('city')  && !$request->query->get('zipCode')) {
            //Only category
            $events = $eventRepository->findBy([
                'category' => $request->query->get('category')
            ]);
        } 
        
        if ($request->query->get('city') && $request->query->get('zipCode') && !$request->query->get('category')) {
            //Only zipcode
            $events = $eventRepository->findBy([
                'zipCode' => $request->query->get('zipCode'),
                'city' => $request->query->get('city'),
            ]);
        } 
       
        if (!$request->query->get('category') && !$request->query->get('city') && !$request->query->get('zipCode') ) {
            //any
            $events = $eventRepository->findAll();
        } 
       
        $categories = $categoryRepository->findAll();
    
        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1, 20)
        );
       
        return $this->render('event/browse.html.twig', [
            'events' => $events,
            'categories' => $categories,
            'form' => $form->createView(),
            'city' => $request->query->get('city'),
            'zipCode' => $request->query->get('zipCode'),
            'categorySelected' => $request->query->get('category'),
        ]);

    }
    
    /**
     * @Route("/{id}", name="_read", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function read(Event $event): Response
    {
        return $this->render('event/read.html.twig', [
            'event' => $event,
        ]);
    }
}
