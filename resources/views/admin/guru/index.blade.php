@extends('admin.layout')
@section('header', 'Manajemen Guru')

@section('content')
<div class="pb-10 md:p-4">
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 mb-6">
        
        <form action="{{ route('admin.guru.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama atau NIP..." 
                class="border rounded-md px-3 py-2 w-full sm:w-64 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            
            <select name="role_id" class="border rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                <option value="">Semua Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 w-full sm:w-auto">Cari</button>
                <a href="{{ route('admin.guru.index') }}" class="bg-gray-200 px-4 py-2 rounded-md hover:bg-gray-300 w-full sm:w-auto text-center">Reset</a>
            </div>
        </form>

        <div class="flex flex-col sm:flex-row items-center gap-3">
            <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data" class="w-full sm:w-auto">
                @csrf
                <label class="block text-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md cursor-pointer transition text-sm font-medium">
                    <input type="file" name="file" class="hidden" onchange="this.form.submit()">
                    Import Excel
                </label>
            </form>
            
            <a href="{{ asset('doc/Tamplate Excel Guru.xlsx') }}" class="text-indigo-600 hover:underline text-sm font-medium text-center">
                Download Template
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left hidden sm:table-cell">NIP</th>
                        <th class="px-4 py-3 text-left hidden md:table-cell">Email</th>
                        <th class="px-4 py-3 text-left">Role</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($gurus as $guru)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-900">{{ $guru->name }}</div>
                            <div class="text-xs text-gray-500 sm:hidden">NIP: {{ $guru->nip }}</div>
                        </td>
                        <td class="px-4 py-4 hidden sm:table-cell">{{ $guru->nip }}</td>
                        <td class="px-4 py-4 hidden md:table-cell">{{ $guru->email }}</td>
                        <td class="px-4 py-4 capitalize">{{ $guru->roles->pluck('name')->implode(', ') ?? '-' }}</td>
                        <td class="px-4 py-4">
                            <div class="flex flex-col sm:flex-row justify-center items-center gap-2">
                                <a href="{{ route('admin.guru.edit', $guru->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">Edit</a>
                                <form action="{{ route('admin.guru.update-status', $guru->id) }}" method="POST">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" 
                                        class="text-[10px] sm:text-xs px-2 py-1 rounded-full font-semibold cursor-pointer border-none shadow-sm {{ $guru->status == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        <option value="1" {{ $guru->status == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $guru->status == 0 ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data guru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 bg-gray-50 border-t">
            {{ $gurus->links() }}
        </div>
    </div>
</div>
@endsection