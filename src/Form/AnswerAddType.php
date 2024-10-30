<?php

namespace App\Form;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updated_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('is_solution')
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('question', EntityType::class, [
            //     'class' => Question::class,
            //     'choice_label' => 'id',
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Answer::class,
        ]);
    }
}