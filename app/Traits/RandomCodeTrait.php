<?php

namespace App\Traits;

trait RandomCodeTrait
{
    public function generateRandomCode()
    {
        $lowerCase = 'abcdefghijklmnopqrstuvwxyz';
        $upperCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';

        $two_lower = substr(str_shuffle($lowerCase), 0, 2);
        $two_upper = substr(str_shuffle($upperCase), 0, 2);
        $two_numbers = substr(str_shuffle($numbers), 0, 2);

        $random_code = str_shuffle($two_lower . $two_upper . $two_numbers);
        return $random_code;
    }
}
