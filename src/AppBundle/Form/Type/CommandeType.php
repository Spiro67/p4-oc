<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Commande;
use AppBundle\Validator\JourFerier;
use AppBundle\Validator\JourFermeture;
use AppBundle\Validator\DemiJournee;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Types
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

// Validators
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;


class CommandeType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('dateEntree', DateType::class, [
                'constraints' => [
                    new NotBlank(),
                    new JourFerier(),
                    new JourFermeture(),
                    new DateTime([]),
                    new GreaterThanOrEqual("today")
                ],
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' => ['class' => 'js-datepicker'],
            ])
            ->add('typeBillet', ChoiceType::class, [
                'choices' => [
                    'Journée complète' => 'Journée complète',
                    'Demi-journée' => 'Demi-journèe',
                ],
                'constraints' => [
                    new NotBlank(),
                ],
                'choice_attr' => [

                    1 => ['data-test' => 'Journée complète']

                    // set disabled to true based on the value, key or index of the choice...
                ],
            ])
			->add('quantite', IntegerType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Range ([
                        'min' => 1,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
	}

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commande'
        ));
    }

	public function getName()
    {
        return 'commande';
    }

}