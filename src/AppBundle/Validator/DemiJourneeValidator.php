<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/10/2017
 * Time: 16:02
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DemiJourneeValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $actual = new \DateTime();

        $reservationHours = date('H', $value->getTimeStamp());

        if ($reservationHours < $actual && $reservationHours < 14) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}