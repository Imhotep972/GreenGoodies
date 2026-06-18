<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserSetApiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEnabled = $options["access_enabled"];
        $builder
            ->add('toggleApi',SubmitType::class,[
                'label' => ($isEnabled)? 'Désactiver mon accès API' : 'Activer mon accès API',
                 'attr' => ['class' => 'btn custom-btn mt-3 fs-6 fw-normal '],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'access_enabled' => false,
        ]);

        $resolver->setAllowedTypes('access_enabled', 'bool');
    }
}
