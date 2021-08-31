<?php

namespace App\Controller\Admin;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/event", name="admin_event_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/verified/{is_verified}/status/{status}", name="browse")
     */

     public function browseVerifiedAndStatus(EventRepository $eventRepository, $is_verified="all", $status="all"): Response
     {
        // test if status & verified is in URL
        if (isset($is_verified) && isset($status)) {

            // then test if each equal to 0,1 or 2
            if (($is_verified == 0 || $is_verified == 1 || $is_verified == 2 || $is_verified == "all") && ($status == 0 || $status == 1 || $status == 2 || $status == "all")) {

                if ($is_verified == "all") {
                    $events = $eventRepository->findBy([
                        'status' => $status,
                        ]);
                } elseif ($status == "all") {
                    $events = $eventRepository->findBy([
                        'is_verified' => $is_verified,
                        ]);
                } else {
                    $events = $eventRepository->findBy([
                        'is_verified' => $is_verified,
                        'status' => $status,
                        ]);
                }
                            
            // if 'verified' or 'status' don't have good params then show all events
            } else {
                $events = $eventRepository->findAll();
            }
        } else {
            $events = $eventRepository->findAll();
        }

        return $this->render('admin/event/browse.html.twig', [
            'events' => $events,
            'stats' => $eventRepository->statistics($eventRepository),
            'status' => $status,
            'is_verified' => $is_verified,
            ]);
    }
}
