<?php

declare(strict_types=1);

namespace App\Form\Type\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;

class UserRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'max' => 100,
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                    new NotNull()
                ]
            ])
            ->add('password', TextType::class, [
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'min' => 8
                    ])
                ]
            ])
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotNull(),
                    new Length([
                        'min' => 2
                    ])
                ]
            ])
            ->add('surname', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
