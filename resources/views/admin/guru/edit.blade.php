@extends('admin.layout')

@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Data Guru</h2>

        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ $guru->name }}" class="w-full border rounded p-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">NIP</label>
                <input type="text" name="nip" value="{{ $guru->nip }}" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ $guru->email }}" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="w-full border rounded p-2">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role_id" class="w-full border rounded p-2" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $guru->roles->contains($role->id) ? 'selected' : '' }}>
                            {{ ucwords($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.guru.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection