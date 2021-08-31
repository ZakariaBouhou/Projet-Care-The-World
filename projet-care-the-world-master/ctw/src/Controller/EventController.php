<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Event;
use App\Entity\Comment;
use App\Form\EventType;
use App\Form\FilterType;
use App\Form\CommentType;
use App\Service\EventSlugger;
use App\Service\ImageUploader;
use App\Repository\EventRepository;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;

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
        // On créer un formulaire et on y associe la requete
        $form = $this->createForm(FilterType::class);
        $form->handleRequest($request);

        // On recupère tout les events actifs avec le statut "verifié" et on les pagine 
        $events = $eventRepository->findBy(
            [
                'status' => 0,
                'is_verified' => 2,
            ],

            ['createdAt' => 'DESC'],
        
        );

        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1) , 10
        );

        // Si on vient de la route category/{id} on applique le filtre
        if ($this->get('session')->get('categories') && $this->get('session')->get('previousUrl') === 'category') {
            
            $form = $form->setData( [
                'categories' => $this->get('session')->get('categories'),
                'zipCode' => null,
                'city' => null,
            
            ]);
            
            $events = $this->filterEvents($form, $eventRepository, $paginator, $request);
            $this->get('session')->set('previousUrl', 'event');
            
        }

        // Si le formulaire de filtrage des events a été soumis et qu'il est valide on applique la methode "filterEvents()" en lui passant en argument le formulaire
        if ($form->isSubmitted() && $form->isValid()){

            $events = $this->filterEvents($form, $eventRepository, $paginator, $request);

        }

        return $this->render('event/browse.html.twig', [
            'events' => $events,
            'form' => $form->createView(),
        ]);

    }

    public function filterEvents($form, $eventRepository, $paginator, $request) {

        $dataForm = $form->getData();
        $events = $eventRepository->findBy(
            [
                'status' => 0,
                'is_verified' => 2,
            ],
            ['createdAt' => 'DESC'],
        );

         //dd($dataForm);
        
        if ($dataForm['categories'] != null && !$dataForm['zipCode'] && !$dataForm['city'] ) {
            // Only categories

            $events = $eventRepository->findBy(
                [
                'category' => $dataForm['categories'],
                'status' => 0,
                'is_verified' => 2,
                ],
                ['createdAt' => 'DESC'],
            );

            // $request->get('categories')->set($dataForm['categories']);
        }

        if ($dataForm['categories'] == null && $dataForm['zipCode'] && $dataForm['city']) {
            // Only location

            $events = $eventRepository->findBy(
                [
                'zipCode' => $dataForm['zipCode'],
                'city' => $dataForm['city'],
                'status' => 0,
                'is_verified' => 2,
                ],
                ['createdAt' => 'DESC'],
            );

        }

        if ($dataForm['categories'] && $dataForm['zipCode'] && $dataForm['city']) {
            // All filters

            $events = $eventRepository->findBy(
                [
                'category' => $dataForm['categories'],
                'zipCode' => $dataForm['zipCode'],
                'city' => $dataForm['city'],
                'status' => 0,
                'is_verified' => 2,
                ],
                ['createdAt' => 'DESC'],
            );

        }

        $events = $paginator->paginate(
            $events,
            $request->query->getInt('page', 1, 20)
        );

        return $events;

    }
    
    /**
     * @Route("/{id}", name="_read", requirements={"id"="\d+"})
     */
    public function read(Request $request, Event $event, PaginatorInterface $paginator): Response
    {
       
        if ($this->getUser() != null) {
            if(!in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
                if ($event->getStatus()!= 0 || $event->getIsVerified()!= 2) {
                    $this->addFlash('danger', 'Cet événement n\'est pas ou plus actif');
                    return $this->redirectToRoute('event_browse');
                }
            };
        }
        
        $form = $this->createForm(CommentType::class);
        
        $comments = $event->getComments();
        
        $comments = $paginator->paginate(
            $comments, // Requête contenant les données à paginer (ici nos commentaires)
            $request->query->getInt('page', 1), 5
        );

        return $this->render('event/read.html.twig', [
            'event' => $event,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/add", name="_add")
     */
    public function add(Request $request, EventSlugger $slugger, ImageUploader $imageUploader){

        $event = new Event();
        $this->denyAccessUnlessGranted('ROLE_USER');
    

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        // dd($request);
        
        if($form->isSubmitted() && $form->isValid()){

            $code = $request->request->get('city');

            if(!empty($code)){
                $url = "https://geo.api.gouv.fr/communes?code=". $code ."&fields=nom,centre&format=json&geometry=centre";
                $reponse = file_get_contents($url);
                $data = json_decode($reponse);

                $event->setImage(random_int(1,5) . '.jpg');
                $event->setCity($data[0]->nom);
                $event->setLongitude($data[0]->centre->coordinates[0]);
                $event->setLatitude($data[0]->centre->coordinates[1]);
            }
            
            //$image = $form->get('image')->getData();
            
            //$event->setImage($imageUploader->upload($image));
            $event->setCategory($form->get('category')->getData());
            $slug = $slugger->slugify($form->get('title')->getData());
            $event->setSlug($slug);
            $event->setStatus(0);
            $event->setIsVerified(0);

            

            $event->setCreatedBy($this->getUser());


            $en = $this->getDoctrine()->getManager();
            $en->persist($event);
            $en->flush();
            
            $this->addFlash('success', 'L\'événement a été créé');
            
            return $this->redirectToRoute('profile');
            
        }

        return $this->render('event/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/user/add", name="_add_user")
     */
    public function addUser(int $id, Event $event): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->getUser() == $event->getCreatedBy() || ($event->getStatus() !=0 || $event->getIsVerified() != 2)) {
            
            throw $this->createAccessDeniedException('Vous ne pouvez pas faire ça');
        } 

        $event->addUser($this->getUser());
        $en = $this->getDoctrine()->getManager();
        $en->flush();

        $this->addFlash('success', 'Vous vous êtes inscrits à l\'événement: ' . $event->getTitle()); 
        return $this->redirectToRoute('event_read', ['id' => $id]);
    }


    /**
     * @Route("/{id}/user/delete/{route}", name="_delete_user")
     */
    public function deleteUser(int $id, Event $event, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->getUser() == $event->getCreatedBy() || ($event->getStatus() !=0 || $event->getIsVerified() != 2)) {
            
            throw $this->createAccessDeniedException('Vous ne pouvez pas faire ça');
        } 

        $previousRoute = $request->attributes->get('_route_params')['route'];
  
        $event->removeUser($this->getUser());
        $en = $this->getDoctrine()->getManager();
        $en->flush();

        $this->addFlash('danger', 'Vous vous êtes désinscrits de l\'événement: ' . $event->getTitle());

        if ($previousRoute == "profile") {
            return $this->redirectToRoute('profile');
        }
        elseif ($previousRoute =="detail") {
            return $this->redirectToRoute('event_read', ['id' => $id]);
        }
    }

    /**
     * @Route("/{id}/status/{status}", name="_status")
     */
    public function changeStatus(Event $event, int $status): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->getUser() != $event->getCreatedBy() && !$this->isGranted('ROLE_ADMIN')) {
            
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet événement');
        } 

        if ($status == 1 ) {
            $event->setStatus(1);
            $this->addFlash('danger', 'Vous avez annulé l\'événement: ' . $event->getTitle());
        }

        if ($status == 2) {
            $event->setStatus(2);
            $this->addFlash('warning', 'Vous avez terminé l\'événement: ' . $event->getTitle());
        }
        
        $this->getDoctrine()->getManager()->flush();

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_event_browse');
        } else {
            return $this->redirectToRoute('profile');
        }
    }

    /**
     * @Route("/{id}/verified/{is_verified}", name="_verified")
     */
    public function changeVerified(Event $event, int $is_verified): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($is_verified == 1) {
            $event->setIsVerified(1);
            $this->addFlash('danger', 'Vous avez bloqué l\'événement: ' . $event->getTitle());
        }

        if ($is_verified == 2) {
            $event->setIsVerified(2);
            $this->addFlash('success', 'Vous avez activé l\'événement: ' . $event->getTitle());
        }
        
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_event_browse');
    }
    
    /**
     * @Route("/edit/{id}", name="_edit", requirements={"id": "\d+"})
     */
    public function edit(int $id, Request $request, Event $event, EventSlugger $slugger): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            if ($this->getUser() != $event->getCreatedBy() &&  $event->getStatus() !=0) {
                throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet événement');
            }
        }

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid())
        {
            $code = $request->request->get('city');

            if(!empty($code)){
                $url = "https://geo.api.gouv.fr/communes?code=". $code ."&fields=nom,centre&format=json&geometry=centre";
                $reponse = file_get_contents($url);
                $data = json_decode($reponse);

                $event->setCity($data[0]->nom);
                $event->setLongitude($data[0]->centre->coordinates[0]);
                $event->setLatitude($data[0]->centre->coordinates[1]);
            }
            
            //$image = $form->get('image')->getData();
            
            //$event->setImage($imageUploader->upload($image));
            $event->setCategory($form->get('category')->getData());
            $slug = $slugger->slugify($form->get('title')->getData());
            $event->setSlug($slug);
            $event->setUpdatedAt(new DateTime());
            $en = $this->getDoctrine()->getManager();
            $en->flush();

            $this->addFlash('info', 'Vous avez bien édité cet événement'); 

            return $this->redirectToRoute('event_read', ['id' => $id]);
        }

        return $this->render('event/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
    

     