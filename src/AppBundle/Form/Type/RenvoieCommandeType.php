<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 29/11/2017
 * Time: 15:47
 */

namespace AppBundle\Form\Type;


use AppBundle\Validator\JourFerier;
use AppBundle\Validator\JourFermeture;
use Symfony\Component\Form\FormBuilderInterface;

// Types
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

// Validators
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class RenvoieCommandeType extends AbstractType
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
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(array(
                        'message' => 'Merci de renseigner une adresse mail valide',
                    )),
                ],
            ]);
    }
}