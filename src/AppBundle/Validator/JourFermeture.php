<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 25/09/2017
 * Time: 15:54
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

class JourFermeture extends Constraint
{
    public $message = 'Le louvre est fermé.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}