<?php

namespace App\Controller\Api;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/api/event", name="api_main", methods={"GET"})
     */
    public function browse(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();
        //dump($events);

        return $this->json($events, 200, [], [
            'groups' => ['browse'],
        ]);
    }
}
