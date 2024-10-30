<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'La question est requise'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Votre question ne pas faire moins de {{ limit }} caracteres',
                        'max' => 255,
                        'maxMessage' => 'Houla ! Moins longue la question !'
                    ])
                    ],
                    'attr' => [
                        'class' => "form-control",
                    ]
            ])
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => ['rows' => 10],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Le contenu ne peux pas etre nul'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => '{{ limit }} caractères au minimum'
                    ])
                ],
                'attr' => [
                    'class' => "form-control",
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Modifier', 
                'attr' => array(
                    'class' => 'btn btn-primary'
                )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
