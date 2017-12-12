<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Info;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Types
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

// Validators
use Symfony\Component\Validator\Constraints\NotBlank;

class InfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Veuillez renseigner votre Nom',
                    )),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Veuillez renseigner votre Prénom',
                    )),
                ],
                'label' => 'Prénom',
            ])
            ->add('dateNaissance', DateType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de renseigner votre date de naissance',
                    )),
                ],
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' => ['class' => 'datepicker-js', 'readonly' => true],
                'label' => 'Date de naissance',
            ])
            ->add('pays', CountryType::class, [
                'preferred_choices' => [
                    'FR', 'DE', 'US', 'ES', 'GB', 'IT', 'JP',
                ],
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de renseigner votre Pays',
                    )),
                    ],
                'label' => 'Nationalité',
            ])
            ->add('tarifReduit', CheckboxType::class, [
                'required' => false,
                'label' => 'Tarif réduit*',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Info::class,
        ]);
    }

    public function getName()
    {
        return 'info';
    }

}