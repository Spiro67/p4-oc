<?php

namespace AppBundle\Form\Type;

use AppBundle\Validator\JourFerier;
use AppBundle\Validator\JourFermeture;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;

// Types
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

// Validators
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotBlank;


class CommandeType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
    {
		$builder
            ->add('dateEntree', DateType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de renseigner une date de visite valide ',
                    )),
                    new JourFerier(),
                    new JourFermeture(),
                    new GreaterThanOrEqual(array('value' => 'today',
                        'message' => 'Merci de renseigner une date supérieur à la date du jour',
                    )),
                ],
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd-MM-yyyy',
                'attr' => ['class' => 'js-datepicker', 'readonly' => true],
                'label' => 'Date de visite',
            ])
            ->add('typeBillet', ChoiceType::class, [
                'choices' => [
                    'Journée complète' => 'Journée complète',
                    'Demi-journée' => 'Demi-journèe',
                ],
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de choisir un type de billet valide',
                    )),
                ],
                'label' => 'Type de billet',
            ])
			->add('quantite', IntegerType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de renseiger une quantité au moins égale à 1',
                    )),
                    new Range ([
                        'min' => 1,
                    ]),
                ],
                'label' => 'Quantité',
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de renseigner une adresse mail valide',
                    )),
                ],
            ]);
	}

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commande',
            'constraints' => [
                new Assert\Callback([ 'callback' => function($data, ExecutionContext $context) {

                    if($data->getDateEntree() !== null) {

                        $heureActuelle = date('H:i:s');

                        $dateJour = date_format(new \Datetime(), 'd/m/Y');

                        $dateSelectionne = date_format($data->getDateEntree(), 'd/m/Y');

                        $typeBillet = $data->getTypeBillet();

                        if($dateSelectionne === $dateJour && $typeBillet === "Journée complète") {
                            if($heureActuelle >= "14:00:00" && $heureActuelle <= "23:59:59") {
                                $context
                                    ->buildViolation('Vous ne pouvez pas commander de billet journée une fois 14h00 passées.')
                                    ->atPath('typeBillet')
                                    ->addViolation();
                            }
                        }
                    }
                }])
            ]
        ));
    }

	public function getName()
    {
        return 'commande';
    }

}