<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Event;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
    * @Route("/comment", name="comment")
    */
class CommentController extends AbstractController
{
    /**
        * @Route("/add/{id}", name="_add", requirements={"id"="\d+"}, methods={"POST"})
        */
    public function add(int $id, Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEvent($event);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été ajouté'); 
            return $this->redirectToRoute('event_read', ['id' => $id]);
        }
    }

    /**
        * @Route("/delete/{id}", name="_delete", requirements={"id"="\d+"}, methods={"DELETE"})
        */
    public function delete(Comment $comment, Request $request): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas faire ça');
        }


        $eventId=$request->request->get('_eventId');

        if ($this->isCsrfTokenValid('commentDelete', $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush();
            
            $this->addFlash('success', 'Le commentaire a bien été supprimé'); 

            return $this->redirectToRoute('event_read', ['id' => $eventId]);
        }
        
        $this->addFlash('danger', 'Le commentaire n\'a pas pu être supprimé'); 
        return $this->redirectToRoute('event_read', ['id' => $eventId]);
    }
}
