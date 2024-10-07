<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
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

    public function __construct()
    {
        $this->userServices = new UserServices();
    }


    public function registerFunction(RegisterRequest $request)
    {
        $user = User::create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'governorate' => $request->governorate,
            'city' => $request->city,
            'email' => $request->email,
            'photo' => $request->photo,
        ]);


        $code = $this->userServices->generateCode();
        $this->userServices->storeCodeInCache($user->id, $code);

        $user->notify(new VerificationCodeNotification($code));
        $tokenResult = $user->createToken($user); // Name your token 'auth_token'

        // Get the plain-text token value
        $plainTextToken = $tokenResult->plainTextToken;
        return response()->json(['token' => $plainTextToken]);


    }
    public function logout()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'No user is currently logged in.',
            ], 401);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }











}
