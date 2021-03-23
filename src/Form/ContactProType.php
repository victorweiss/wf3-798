<?php

namespace App\Form;

use App\Entity\ContactPro;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactProType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => "Prénom",
            ])
            ->add('lastname', TextType::class, [
                'label' => "Nom",
            ])
            ->add('company', TextType::class, [
                'label' => "Entreprise",
            ])
            ->add('email', EmailType::class, [
                'label' => "Email",
            ])
            ->add('subject', TextType::class, [
                'label' => "Sujet",
            ])
            ->add('message', TextareaType::class, [
                'label' => "Message",
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Envoyer",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ContactPro::class,
        ]);
    }
}
