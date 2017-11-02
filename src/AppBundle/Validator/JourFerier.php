<?php

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

class JourFerier extends Constraint
{
    /**
     * @var string
     */
    public $message = 'Le louvre est fermé.';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}