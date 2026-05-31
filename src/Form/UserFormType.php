<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prenom',TextType::class,[
                'label'=> 'Prénom *',
                'label_attr' => ['class'=>'form_label fs-6'],
                 'attr' => ['class' => 'form-control customInput'],
            ])
            ->add('nom',TextType::class,[
                'label'=> 'Nom *',
                'label_attr' => ['class'=>'form_label fs-6'],
                'attr' => ['class' => 'form-control customInput'],
            ])
            ->add('email',EmailType::class,[
                'label'=> 'Adresse email *',
                'label_attr' => ['class'=>'form_label fs-6'],
                'attr' => ['class' => 'form-control customInput'],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'form-control customInput password-field'],'label_attr' => ['class' => 'form_label fs-6']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe *'],
                'second_options' => ['label' => 'Confirmation mot de passe *'],    
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez indiquer votre mot de passe',
                    ),
                    new Length(
                        min: 8,
                        minMessage: 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                ],
            ])
            ->add('CGU', CheckboxType::class, [
                'label'    => "J’accepte les CGU de Green Goodies *",
                'label_attr' => ['class'=>'form-check-label fs-6 fw-normal'],
                'attr' => ['class' => 'form-check-input custom-check'],
                'required' => true,
                'mapped' => false,
                ])
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
