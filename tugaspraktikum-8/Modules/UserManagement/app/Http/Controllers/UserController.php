<?php

namespace Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\UserManagement\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()->latest()->paginate(12);

        return view('usermanagement::index', compact('users'));
    }

    public function create(): View
    {
        return view('usermanagement::create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('usermanagement::edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        if ($user->is(auth()->user()) && $request->validated('role') !== 'admin') {
            throw ValidationException::withMessages([
                'role' => 'Anda tidak dapat menurunkan peran akun yang sedang digunakan.',
            ]);
        }

        $data = $request->validated();
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 403, 'Akun yang sedang digunakan tidak dapat dihapus.');

        if ($user->loans()->exists()) {
            throw ValidationException::withMessages([
                'user' => 'Pengguna tidak dapat dihapus karena memiliki riwayat peminjaman.',
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
