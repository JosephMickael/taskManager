<?php

namespace App\Form;

use App\Utils\PasswordForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, ["attr" => ["class" => "form-control mb-3"], 'label' => 'Ancien mot de passe'])
            ->add('newPassword', PasswordType::class, ["attr" => ["class" => "form-control mb-3"], 'label' => 'Nouveau mot de passe'])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregister',
                'attr' => ['class' => 'btn btn-secondary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordForm::class,
        ]);
    }
}
