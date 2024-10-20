<?php

namespace App\Services;


use Illuminate\Support\Facades\Cache;


class CodeGenerateServices
{
    public function generateCode()
    {
        $lowercaseLetters = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $characters = $lowercaseLetters . $uppercaseLetters . $numbers;
        $verificationCode = substr(str_shuffle($characters), 0, 6);

        return $verificationCode;
    }

    public function storeCodeInCache($email, $code)
    {
        Cache::put("verification_code_{$email}", $code, 10 * 60);
    }

    public function verifyCode($email, $inputCode)
    {
        $cachedCode = Cache::get("verification_code_{$email}");
        return $cachedCode && $cachedCode === $inputCode;
    }




}

