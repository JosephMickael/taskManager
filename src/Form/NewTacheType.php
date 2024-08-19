<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\User;
use App\Entity\Tache;
use App\Entity\Projet;
use App\Entity\Statut;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;

class NewTacheType extends AbstractType
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
      ->add('nom', TextType::class, ["attr" => ["class" => "form-control"], 'label' => 'Tache'])
      ->add('description', TextareaType::class, ["attr" => ["class" => "form-control"], 'label' => 'Description de la tache'])
      ->add('developpeur', EntityType::class, [
        'class' => User::class,
        'choice_label' => 'username',
        "attr" => ["class" => "form-control"],
        'label' => 'Developpeur en charge',
        'query_builder' => function (EntityRepository $er) {
          return $er->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_DEV"%')
            ->orderBy('u.username', 'ASC');
        }
      ])
      ->add('dateEchUser', DateTimeType::class, ["attr" => ["class" => "form-control"], 'label' => 'Date echeance developpeur', 'widget' => 'single_text'])
      ->add('dateEchChef', DateTimeType::class, ["attr" => ["class" => "form-control"], 'label' => 'Date echeance Chef de projet', 'widget' => 'single_text'])
      // ->add('dateEchPorteurProjet', DateTimeType::class, ["attr" => ["class" => "form-control"], 'label' => 'Date echeance porteur projet', 'widget' => 'single_text'])
      ->add('statut', EntityType::class, ['class' => Statut::class, 'choice_label' => 'etat', "attr" => ["class" => "form-control"], 'label' => 'Statut du projet'])
      ->add('projet', EntityType::class, [
        'class' => Projet::class,
        'choice_label' => 'nom',
        "attr" => ["class" => "form-control"],
        'label' => 'Projet',
        'query_builder' => function (EntityRepository $er) use ($user) {
          if (in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return $er->createQueryBuilder('p');
          } else {
            return $er->createQueryBuilder('p')
              ->andWhere('p.chefprojet = :user')
              ->setParameter('user', $user);
          }
        }
      ])
      ->add('duration', NumberType::class, [
        'attr' => ['class' => 'form-control'],
        'label' => 'Durée'
      ])
      ->add('categorie', EntityType::class, ['class' => Categorie::class, 'choice_label' => 'nom', "attr" => ["class" => "form-control"], 'label' => 'Catégorie']);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => Tache::class,
    ]);
  }
}
