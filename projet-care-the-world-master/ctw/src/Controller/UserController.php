<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserEditType;
use App\Form\UserFormType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/inscription", name="inscription")
     */
    public function add(UserPasswordEncoderInterface $passwordEncoder, Request $request): Response
    {

        $user = new User();       


        $form = $this->createForm(UserFormType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() AND $form->isValid()){

            $code = $request->request->get('city');

            if(!empty($code)){
                $url = "https://geo.api.gouv.fr/communes?code=". $code ."&fields=nom,centre&format=json&geometry=centre";
                $reponse = file_get_contents($url);
                $data = json_decode($reponse);

                $user->setCity($data[0]->nom);
            }

            $password = $form->get('password')->getData();
            $encoded = $passwordEncoder->encodePassword($user, $password);
            $user->setPassword($encoded);
    
            $user->setStatus(1);
            $user->setRoles(['ROLE_USER']);

            $user->setZipCode($form->get('zipCode')->getData());

            $user->setBirth($form->get('birth')->getData());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous êtes bien inscrit !');
            return $this->redirectToRoute('home');
        }
        return $this->render('user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/edit/{id}", name="profile_edit", requirements={"id": "\d+"})
     */
    public function edit(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder){

        if ($user !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier cet utilisateur');
        }
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère ce qui vient du champs password
            // Attention, ce champs n'est plus mappé avec User, donc on traite le mot de passe à part
            // Ce mot de passe est brut, non hashé.
            $password = $form->get('password')->getData();
            
            if ($password !== null) {
                // Il faut maintenant hashé ce mdp et le placer dans $user
                // mais seulement si le mdp est différent de null !
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
                // En une seule ligne ça donne :
                // $user->setPassword($passwordEncoder->encodePassword($user, $password));
            }

            $code = $request->request->get('city');

            if(!empty($code)){
                $url = "https://geo.api.gouv.fr/communes?code=". $code ."&fields=nom,centre&format=json&geometry=centre";
                $reponse = file_get_contents($url);
                $data = json_decode($reponse);

                $user->setCity($data[0]->nom);
            }
            
            $user->setUpdateAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre profil a bien été modifié');
            return $this->redirectToRoute('profile');
        }
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(), 
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function read(){

        $this->denyAccessUnlessGranted('ROLE_USER');
        

        return $this->render('user/profile.html.twig', [
            'controller_name'=> 'my_controller',
            'user'=>$this->getUser(),
        ]);
    }
}
