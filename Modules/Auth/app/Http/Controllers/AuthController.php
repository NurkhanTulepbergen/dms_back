<?php
namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\password;

class AuthController extends Controller
{

    public function register(Request $request){
        $request->validate([
            'role' => 'required|in:admin,student,manager,employee',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone_number' => 'required',
            'lastname' => 'required',
            'name' => 'required',
            'middlename' => 'required',
            'uni_id' => 'required| unique:users, uni_id',
        ]);
        $user = User::create([
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'lastname' => $request->lastname,
            'name' => $request->name,
            'middlename' => $request->middlename,
            'uni_id' => $request->uni_id
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
        return result([
            'token' => [
                'access_token' => $token,
                'type' => 'Bearer',
            ],
            'user' => $user,
        ], 201, 'Регистрация завершена');
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || ! Hash::check($request->password, $user->password)){
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return result([
            'token' => [
                'access_token' => $token,
                'type' => 'Bearer',
            ],
            'user' => $user,
        ], 200, 'Вход выполнен успешно');
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return result(null,204);
    }

    public function resetpassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);
        $user = Auth::user();

        if(!Hash::check($request->old_password, $user->password)) {
            return result(
                null,
                422,
                "Старый пароль указан неверно"
            );
        }
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return result(
            [
                'email' => $user->email,
                'uni_id' => $user->uni_id
            ],

            200,
            'Пароль успешно обновлен'
        );
    }
}
