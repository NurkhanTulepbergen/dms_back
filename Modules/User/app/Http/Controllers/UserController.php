<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Models\User;

class UserController extends Controller
{

    // GET /api/v1/me
    public function me(Request $request)
    {
        return result($request->user(), 200, 'Информация о пользователе');
    }

    // GET /api/v1/users
    public function index()
    {
        $users = User::query()->orderByDesc('id')->get();
        return result($users, 200, 'Пользователи');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show(User $user)
    {
        return result($user, 200, 'Пользователь');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('user::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
