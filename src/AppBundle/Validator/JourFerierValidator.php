<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class JourFerierValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $jourFerier= ['01/05','01/11','25/12'];

        $date = date('d/m', $value->getTimeStamp());
        foreach ($jourFerier as $ferier) {
            if ($date === $ferier) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
            }
        }
    }
}