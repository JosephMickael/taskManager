<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Projet;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ["attr" => ["class" => "form-control"], 'label' => 'Nom projet'])
            ->add('porteur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                "attr" => ["class" => "form-control"],
                'label' => 'Porteur de projet',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_PORTEUR_PROJET"%')
                        ->orderBy('u.username', 'ASC');
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
        ]);
    }
}
