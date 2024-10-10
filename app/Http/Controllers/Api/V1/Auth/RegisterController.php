<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Services\CodeGenerateServices;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use App\Http\Services\UserServices;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
class RegisterController extends Controller
{
    private UserServices $userServices;
    private CodeGenerateServices $codeGenerateServices;
    public function __construct()
    {
        $this->userServices = new UserServices();
        $this->codeGenerateService = new CodeGenerateServices();
    }


    public function registerFunction(RegisterRequest $request)
    {

        $userData = [];
        $userData['full_name'] = $request->full_name;
        $userData['phone'] = $request->phone;
        $userData['password'] = Hash::make($request->password);
        $userData['address'] = $request->address;
        $userData['governorate'] = $request->governorate;
        $userData['city'] = $request->city;
        $userData['email'] = $request->email;
        $userData['photo'] = $request->photo;
        $user = User::create($userData);
        $code = $this->codeGenerateService->generateCode();
        $this->codeGenerateService->storeCodeInCache($user->id, $code);

        $user->notify(new VerificationCodeNotification($code));
        $plainTextToken = $user->createToken($user)->plainTextToken;
        return response()->json(['token' => $plainTextToken]);


    }












}
