<?php

namespace App\Validation;

use Illuminate\Validation\Validator as BaseValidator;

/**
 * Backports the Laravel 12.60 fix for GHSA-5vg9-5847-vvmq: the stock `email`
 * rule accepts quoted local parts containing CR/LF, which lets an address
 * smuggle extra mail headers. Symfony Mime refuses such addresses, but only at
 * send time, after the record holding the address is already saved. Laravel 11
 * never got the patch. Delete this class and its resolver in AppServiceProvider once on
 * Laravel 12.60+.
 */
class CrlfSafeValidator extends BaseValidator
{
    public function validateEmail($attribute, $value, $parameters)
    {
        return parent::validateEmail($attribute, $value, $parameters)
            && preg_match('/[\r\n]/', (string) $value) === 0;
    }
}
