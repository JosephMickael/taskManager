<?php

namespace App\Form;

use App\Utils\TicketSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TicketSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filter', ChoiceType::class, [
                'attr' => ['class' => 'form-control'],
                'choices' => [
                    'En cours' => 'enCours',
                    'Terminé' => 'termine',
                    'Non traité' => 'nonTraite',
                ],
                'placeholder' => 'Choisir un filtre',
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher par statut',
                'attr' => ['class' => 'btn btn-secondary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketSearch::class,
        ]);
    }
}
