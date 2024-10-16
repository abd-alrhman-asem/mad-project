<?php

namespace App\Services;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class UserServices
{
    public function generateCode()
    {
        $lowercaseLetters = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $characters = $lowercaseLetters . $uppercaseLetters . $numbers;
        return substr(str_shuffle($characters), 0, 6);
    }

    public function storeCodeInCache($userId, $code)
    {Cache::put("verification_code_{$userId}", $code, 10 * 60);}

    public function verifyCode($userId, $inputCode)
    {
        $cachedCode = Cache::get("verification_code_{$userId}");
        return $cachedCode && $cachedCode === $inputCode;
    }
    public function updateUserStatusToExpired($userId)
    {
        $user = User::find($userId);

        if ($user && $user->status === UserStatus::Trial->value) {
            $user->status = UserStatus::Expired->value;
            $user->save();
        }
    }




}

