<?php
namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Media;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use App\Services\CodeGenerateServices;
use App\Services\UserServices;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct(
        protected UserServices $userServices,
        protected CodeGenerateServices $codeGenerateServices
    ) {}

    public function registerFunction(RegisterRequest $request)
    {

        $userData = $request->validated();
        $userData['password'] = Hash::make($userData['password']);
        $user = User::create($userData);
        if ($request->hasFile('photo')) {
            try {

                $photoPath = $request->file('photo')->store('photos', 'public');
                $media = Media::create([
                    'mediable_id' => $user->id,
                    'mediable_type' => User::class,
                    'file_path' => $photoPath,
                    'file_type' => $request->file('photo')->getClientMimeType(),
                ]);

                if (!$media) {
                    $user->delete();
                    throw new \Exception('Media record was not created.');
                }
            } catch (\Exception $e) {
                $user->delete();
                return response()->json(['error' => 'Failed to store photo: ' . $e->getMessage()], 500);
            }
        }

        $code = $this->codeGenerateServices->generateCode();
        $this->codeGenerateServices->storeCodeInCache($user->email, $code);
        $user->notify(new VerificationCodeNotification($code));
        return response()->json([
            'message' => 'we send code for your email',
        ]);
    }
}
