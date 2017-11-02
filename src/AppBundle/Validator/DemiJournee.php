<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/10/2017
 * Time: 15:42
 */

namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraint;

class DemiJournee extends Constraint
{
    public $message = "Vous ne pouvez pas comma,der de billet journée une fois 14h00 passées";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }
}