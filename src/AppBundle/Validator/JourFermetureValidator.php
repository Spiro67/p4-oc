<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/09/2017
 * Time: 15:54
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JourFermetureValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value !== null) {
            $jourFermeture = ['Tue', 'Sun'];

            $jour = date('D', $value->getTimeStamp());

            foreach ($jourFermeture as $fermeture) {
                if ($jour === $fermeture) {
                    $this->context->buildViolation($constraint->message)
                        ->addViolation();
                }
            }
        }
    }
}