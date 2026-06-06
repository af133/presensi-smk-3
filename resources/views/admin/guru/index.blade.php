@extends('admin.layout')
@section('header', 'Manajemen Guru')
@section('content')
<div class="p-6">
    

    <form action="{{ route('admin.guru.index') }}" method="GET" class="flex gap-4 mb-6 bg-white p-4 rounded-lg shadow-sm border">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." class="border rounded px-3 py-2 w-full">
        
        <select name="role_id" class="border rounded px-3 py-2">
            <option value="">Semua Role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Cari</button>
        <a href="{{ route('admin.guru.index') }}" class="bg-gray-200 px-4 py-2 rounded">Reset</a>
    </form>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Nama</th>
                    <th class="px-6 py-3 text-left">NIP</th>
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left">Role</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gurus as $guru)
                <tr class="border-t">
                    <td class="px-6 py-4">{{ $guru->name }}</td>
                    <td class="px-6 py-4">{{ $guru->nip }}</td>
                    <td class="px-6 py-4">{{ $guru->email }}</td>
                    <td class="px-6 py-4 capitalize">
                        {{ $guru->roles->pluck('name')->implode(', ') ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.guru.edit', $guru->id) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                            
                            <form action="{{ route('admin.guru.delete', $guru->id) }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('Yakin hapus guru ini?')" class="text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 bg-white border-t">
            {{ $gurus->links() }}
        </div>
    </div>
</div>
@endsection