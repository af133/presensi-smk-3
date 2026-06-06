@extends('admin.layout')
@section('header', 'Tambah Guru')
@section('content')
<div class="p-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">

        <form action="{{ route('admin.guru.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full border rounded p-2" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">NIP</label>
                <input type="text" name="nip" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Password</label>
                <input type="password" name="password" class="w-full border rounded p-2" required>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Role</label>
                <select name="role_id" class="w-full border rounded p-2" required>
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ ucwords($role->name) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.guru.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan Guru</button>
            </div>
        </form>
    </div>
</div>
@endsection