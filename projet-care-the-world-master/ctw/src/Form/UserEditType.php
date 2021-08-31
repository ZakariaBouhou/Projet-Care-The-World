<?php

namespace App\Form;

use App\Entity\User;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $adultMax = date('Y-m-d', strtotime('-18 year'));

        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control', 
                    'id' => 'firstname', 
                    'placeholder' => 'Entrer votre prénom', 
                ],
                'label' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([ 'normalizer' => 'trim' ]),
                    new Length([ 'min' => 2, 'max' => 255 ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control', 
                    'id' => 'lastname', 
                    'placeholder' => 'Entrer votre nom de famille' 
                ],
                'label' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([ 'normalizer' => 'trim' ]),
                    new Length([ 'min' => 2, 'max' => 255 ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control', 
                    'id' => 'email', 
                    'placeholder'=> 'Entrer votre adresse email' 
                ],
                'label' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank([ 'normalizer' => 'trim' ]),
                    new Length([ 'min' => 2, 'max' => 255 ]),
                ],
            ])
    
            ->add('zipCode', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => '5',
                    'minlength' => '5',
                    'id' => 'zipCode',
                    'placeholder' => 'Entrer votre code postal', 
                ],
                'mapped' => true,
                'label' => false,
                'required' => false,
                'constraints' => [
                    new NotBlank( ['normalizer' => 'trim'] ),
                ],
            ])

            -> add('birth', DateType::class, [
                'attr' => [
                    'max' => $adultMax,
                ],
                'required' => false,
                'widget' => 'single_text',
                'constraints' => [
                    //new Length( [ 'min' => $adultMax, ] )
                ] ,
                'mapped' => true,
            ])


            ->add('city', ChoiceType::class, [
                'choices' => [
                    'Selectionner une ville' => '',
                ],
                'attr' => [
                  'class' => 'form-control'
                ],
                'required' => false,
                'mapped' => true,
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent etre identiques.',
                'required' => false,
                'mapped' => false,
                'constraints' => [

                ],
            ])

        ;

        
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
