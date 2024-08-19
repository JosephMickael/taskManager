<?php

namespace App\Form;

use App\Entity\Projet;
use App\Entity\Ticket;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TicketType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();

        $builder
            ->add('titre', TextType::class, ["attr" => ["class" => "form-control"], 'label' => 'Titre Ticket'])
            ->add('projet', EntityType::class, [
                'class' => Projet::class,
                'choice_label' => 'nom',
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom du projet concerné',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
                        return $er->createQueryBuilder('p');
                    } else {
                        return $er->createQueryBuilder('p')
                            ->andWhere('p.porteur = :user')
                            ->setParameter('user', $user);
                    }
                }
            ]);
        // ->add('createdAt')
        // ->add('porteur', EntityType::class, ['class' => User::class, 'choice_label' => 'etat', "attr" => ["class" => "form-control"], 'label' => 'Statut du projet']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
