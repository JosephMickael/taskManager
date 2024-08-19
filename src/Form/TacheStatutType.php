<?php

namespace App\Form;

use App\Entity\Statut;
use App\Entity\TacheStatut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TacheStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', EntityType::class, [
                'label' => 'Filtre par statut',
                'class' => Statut::class,
                'attr' => ['class' => 'form-control'],
                'choice_label' => 'etat',
                'multiple' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher par statut',
                'attr' => [
                    'class' => 'btn btn-secondary mt-4 mb-3 px-4',
                    'style' => 'font-size: .9em'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TacheStatut::class,
        ]);
    }
}
