<?php

namespace App\Form;

use App\Entity\Question;
use App\Entity\Tag;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextType::class)
            ->add('tags', TextType::class, [
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Entrez des tags',
                    'class' => 'tag-input'
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
