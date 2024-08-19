<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\TacheSearch;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TacheSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'label' => 'Filtre par utilisateurs',
                'class' => User::class,
                'attr' => ['class' => 'form-control'],
                'choice_label' => 'username',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_DEV"%')
                        ->orderBy('u.username', 'ASC');
                },
                'multiple' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher par utilisateurs',
                'attr' => [
                    'class' => 'btn btn-secondary mt-4 mb-3 px-4',
                    'style' => 'font-size: .9em'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TacheSearch::class,
        ]);
    }
}
