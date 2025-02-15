<?php

declare(strict_types=1);

namespace App\User\Form;

use App\Entity\Gender;
use App\User\Model\UserRegistration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirmation'],
            ])
            ->add('gender', EnumType::class, [
                'class' => Gender::class,
                'expanded' => true,
            ])
            ->add('fullName', TextType::class)
            ->add('country', CountryType::class, [
                'preferred_choices' => ['AE', 'CA', 'CH', 'DE', 'FR', 'IT', 'SG', 'US'],
                'required' => false,
            ])
            ->add('birthdate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistration::class,
        ]);
    }
}
