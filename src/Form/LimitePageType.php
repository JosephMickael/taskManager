<?php

namespace App\Form;

use App\Entity\LimitePage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class LimitePageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', NumberType::class, [
                "attr" => ["class" => "form-control", 'placeholder' => 'Nombre de taches à afficher'],
                'label' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Afficher',
                'attr' => ['class' => 'btn btn-secondary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LimitePage::class,
        ]);
    }
}
