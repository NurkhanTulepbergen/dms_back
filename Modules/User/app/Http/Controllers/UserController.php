<?php

namespace Modules\User\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\User\Models\User;

class UserController extends Controller
{
    private function userResourceColumns(): array
    {
        return [
            'id',
            'role',
            'email',
            'phone_number',
            'lastname',
            'name',
            'middlename',
            'uni_id',
            'gender',
            'discipline_limit',
            'created_at',
            'updated_at',
        ];
    }

    private function userPayloadRules(?User $user = null): array
    {
        return [
            'role' => ['required', Rule::in(['admin', 'student', 'manager', 'dorm-admin', 'employee'])],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8'],
            'phone_number' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'middlename' => ['required', 'string', 'max:255'],
            'uni_id' => ['required', 'string', 'max:255', Rule::unique('users', 'uni_id')->ignore($user?->id)],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'discipline_limit' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function serializeUser(User $user): array
    {
        return $user->only($this->userResourceColumns());
    }

    // GET /api/v1/me
    public function me(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        return result($this->serializeUser($user), 200, 'Информация о пользователе');
    }

    // GET /api/v1/users
    public function index(Request $request)
    {
        $query = User::query()
            ->select($this->userResourceColumns());

        if ($request->user()?->role === 'employee') {
            $query->where('role', 'student');
        }

        $users = $query
            ->orderByDesc('id')
            ->get();

        return result($users, 200, 'Пользователи');
    }

    // GET /api/v1/users/{user}
    public function show(User $user)
    {
        return result($this->serializeUser($user), 200, 'Пользователь');
    }

    // POST /api/v1/users
    public function store(Request $request)
    {
        $validated = $request->validate($this->userPayloadRules());

        $user = User::query()->create([
            'role' => $validated['role'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'lastname' => $validated['lastname'],
            'name' => $validated['name'],
            'middlename' => $validated['middlename'],
            'uni_id' => $validated['uni_id'],
            'gender' => $validated['gender'],
            'discipline_limit' => $validated['discipline_limit'] ?? 10,
        ]);

        return result($this->serializeUser($user), 201, 'Пользователь создан');
    }

    // PUT /api/v1/users/{user}
    public function update(Request $request, User $user)
    {
        $validated = $request->validate($this->userPayloadRules($user));

        $payload = [
            'role' => $validated['role'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'lastname' => $validated['lastname'],
            'name' => $validated['name'],
            'middlename' => $validated['middlename'],
            'uni_id' => $validated['uni_id'],
            'gender' => $validated['gender'],
            'discipline_limit' => $validated['discipline_limit'] ?? $user->discipline_limit ?? 10,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $user->update($payload);

        return result($this->serializeUser($user->fresh()), 200, 'Пользователь обновлен');
    }

    // DELETE /api/v1/users/{user}
    public function destroy(Request $request, User $user)
    {
        /** @var User $currentUser */
        $currentUser = $request->user();

        if ($currentUser->id === $user->id) {
            throw new BusinessException('Нельзя удалить текущего пользователя', 422);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->noContent();
    }
}
