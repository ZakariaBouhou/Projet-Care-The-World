<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(UserRepository $userRepository, EventRepository $eventRepository): Response
    {
        $allUsers = $userRepository->countAllUsersVerified();
        $allEvents = $eventRepository->countAllEventsValid();

        $events = $eventRepository->findBy(['status' => 0, 'is_verified' => 2], ['createdAt' => 'DESC'], 6);

        return $this->render('main/home.html.twig', [
            'events' => $events,
            'allEvents' => $allEvents,
            'allUsers' => $allUsers
        ]);
    }

     /**
     * @Route("/legals", name="legals")
     */
     public function legals(): Response
     { 
         return $this->render('main/legals.html.twig', []);
     }

}
