<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class,[
                'label'    => 'Quantité : ',
                'label_attr' => ['class'=>'fs-6'],
                'attr' => [
                    'class' => 'spinner',
                    'min' => 0,
                    'max' => 99
                ],
                'data' => $options['initialQuantity'],
                'required' => true,
                'mapped' => false,
                ])
            ->add('product_id', HiddenType::class,[
                'data' => $options['product_id'],
                'required' => true,
                'mapped' => false,
                ])
            ->add('submit', SubmitType::class, [
                'label' => $options['submitLabel'],
                'attr' => ['class' => 'form-control btn custom-btn custom2-btn'],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'submitLabel' => 'Ajouter au panier',           // label par défaut du bouton submit
            'initialQuantity' => 0,       // quantité initiale 0
            'product_id' => null,       // quantité initiale 0
        ]);
    }
}
