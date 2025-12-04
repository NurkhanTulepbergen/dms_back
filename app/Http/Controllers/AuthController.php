<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
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

        return response()->json([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out'
        ]);
    }

    public function me(Request $request){
        return response()->json($request->user());
    }
}
