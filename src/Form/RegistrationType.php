<?php
// src/Form/RegistrationType.php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Your name',
                'attr' => ['placeholder' => 'First and last name']
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail Address',
                'attr' => ['placeholder' => 'Enter your email']
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'attr' => ['placeholder' => 'At least 6 characters']
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'label' => 'Password again'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class, // Lier le formulaire à l'entité Users
        ]);
    }
}
