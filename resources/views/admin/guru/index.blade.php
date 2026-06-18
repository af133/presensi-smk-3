@extends('admin.layout')
@section('header', 'Manajemen Guru')

@section('content')
<div class="p-6">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <form action="{{ route('admin.guru.index') }}" method="GET" class="flex flex-wrap gap-2 flex-grow">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." class="border rounded-md px-3 py-2 w-64">
            <select name="role_id" class="border rounded-md px-3 py-2">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Cari</button>
            <a href="{{ route('admin.guru.index') }}" class="bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300">Reset</a>
        </form>

        <div class="flex items-center gap-4">
            <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md cursor-pointer transition text-sm font-medium">
                    <input type="file" name="file" class="hidden" onchange="this.form.submit()">
                    Import Excel
                </label>
            </form>
            
            <a href="{{ asset('doc/Tamplate Excel Guru.xlsx') }}" class="text-indigo-600 hover:underline text-sm font-medium whitespace-nowrap">
                Download Template
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left whitespace-nowrap">Nama</th>
                        <th class="px-6 py-3 text-left whitespace-nowrap">NIP</th>
                        <th class="px-6 py-3 text-left whitespace-nowrap">Email</th>
                        <th class="px-6 py-3 text-left whitespace-nowrap">Role</th>
                        <th class="px-6 py-3 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($gurus as $guru)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $guru->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $guru->nip }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $guru->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap capitalize">
                            {{ $guru->roles->pluck('name')->implode(', ') ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex justify-center items-center gap-3">
                                <a href="{{ route('admin.guru.edit', $guru->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                <form action="{{ route('admin.guru.update-status', $guru->id) }}" method="POST">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                        class="text-xs px-2 py-1 rounded-full font-semibold cursor-pointer border-none shadow-sm {{ $guru->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <option value="1" {{ $guru->status == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $guru->status == 0 ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 bg-gray-50 border-t">
            {{ $gurus->links() }}
        </div>
    </div>
</div>
@endsection