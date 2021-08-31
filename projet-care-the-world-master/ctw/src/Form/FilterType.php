<?php

namespace App\Form;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormEvent;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class FilterType extends AbstractType
{
    private $categoryRepository;
    
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categories = $this->categoryRepository->findAll();
        
        foreach ($categories as $categorie) {
            $categoriesChoices[$categorie->getName()] =  $categorie->getId();
        };
        
   
        $builder
            ->add('categories', ChoiceType::class, [
                'label' => 'Catégories',
                'placeholder' => 'Veuillez choisir une catégorie',
                'choices' => $categoriesChoices,
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control',
                    
                ],
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'required' => false
            ])

            ->add('zipCode', null, [
                'label' => 'Code postal',             
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'zipCode',
                    'placeholder' => 'Veuillez entrer un code postal',  
                ],
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'required'=>false,
            ])

            ->add('city', ChoiceType::class, [
                'label' => 'Ville',
                'placeholder' => 'Veuillez choisir une ville',
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'mt-3'
                ],
                'required' => false,
            
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
                $form = $event->getForm();

                $dataForm = $event->getData();
                
                if (isset($dataForm['city'])) {

                    $form->add('city', ChoiceType::class, [
                    'label' => 'Ville',
                    'choices' => [$dataForm['city'] => $dataForm['city']],
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'label_attr' => [
                        'class' => 'mt-3'
                    ],
                    'required' => false,
                
                    ]);

                } else {

                    $form->add('city', ChoiceType::class, [
                        'label' => 'Ville',
                        'choices' => ['' => 'city'],
                        'attr' => [
                            'class' => 'form-control'
                        ],
                        'required' => false,
                    ]);

                }

            })

            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ]); 
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
