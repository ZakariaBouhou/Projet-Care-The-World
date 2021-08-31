<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'maxlength' => "35",
                    'placeholder' => 'Entrer le titre de l\'événement' 
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['normalizer' => 'trim']),
                ],
            ])
            
            ->add('description', CKEditorType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'cols' => '40',
                    'rows' => '10',
                    'wrap' => 'hard',
                    'style' => 'resize:none',
                    'placeholder' => 'Entrer description de l\'événement' 
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['normalizer' => 'trim']),
                ],
            ])
            /*
            ->add('image', FileType::class, [
                'attr' => [
                    'accept' => '.pnj,.jpg,.jpeg',
                    'class' => 'custom-file-input',
                ],
                'required' => false,
            ])
            */
            ->add('startAt', DateType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => date('Y-m-d', strtotime('now')),
                ],
                'required' => true,
                'widget' => 'single_text',
            ])

            ->add('endAt', DateType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min' => date('Y-m-d', strtotime('now')),
                ],
                'required' => true,
                'widget' => 'single_text',
            ])
            
            ->add('zipCode', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'zipCode',
                    'maxlength' => "5",
                    'placeholder' => 'Entrer le code postal'
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank(['normalizer' => 'trim']),
                ],
            ])
            
            ->add('city', ChoiceType::class, [
                'choices' => [
                    'Selectionner une ville' => '',

                ],
                'attr' => [
                  'class' => 'form-control'
                ],
                'required' => true,
            ])
            
            ->add('category')

        ;        
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
