<div class="form-grid">
    <div class="field">
        <label for="name">Nama lengkap</label>
        <input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required>
        @error('name') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required>
        @error('email') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="role">Peran</label>
        <select id="role" name="role" required>
            <option value="user" @selected(old('role', $user->role ?? 'user') === 'user')>Anggota</option>
            <option value="admin" @selected(old('role', $user->role ?? '') === 'admin')>Admin</option>
        </select>
        @if (isset($user) && $user->is(auth()->user()))
            <small class="muted">Peran akun yang sedang digunakan tidak dapat diturunkan.</small>
        @endif
        @error('role') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="password">{{ isset($user) ? 'Kata sandi baru' : 'Kata sandi' }}</label>
        <input id="password" name="password" type="password" autocomplete="new-password" {{ isset($user) ? '' : 'required' }}>
        @if (isset($user)) <small class="muted">Kosongkan jika tidak ingin mengganti kata sandi.</small> @endif
        @error('password') <small class="field-error">{{ $message }}</small> @enderror
    </div>
    <div class="field">
        <label for="password_confirmation">Ulangi kata sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" {{ isset($user) ? '' : 'required' }}>
    </div>
</div>
