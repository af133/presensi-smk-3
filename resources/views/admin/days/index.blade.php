@extends('admin.layout')

@section('header', 'Manajemen Hari & Jadwal')

@section('content')
<div class="pb-15 md:p-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-800">Daftar Hari</h2>
        <p class="text-gray-600 text-sm">Pilih hari untuk mengelola jadwal pelajaran.</p>
    </div>

    <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-xl overflow-hidden border border-white/30">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100/50">
                    <th class="p-4 text-sm font-bold text-gray-700">No</th>
                    <th class="p-4 text-sm font-bold text-gray-700">Nama Hari</th>
                    <th class="p-4 text-sm font-bold text-gray-700 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/50">
                @foreach($days as $index => $day)
                <tr class="hover:bg-indigo-50/50 transition-colors">
                    <td class="p-4 text-gray-700">{{ $index + 1 }}</td>
                    <td class="p-4 font-semibold text-gray-800">{{ $day->name }}</td>
                    <td class="p-4 text-center">
                        <a href="{{ route('admin.days.manage', $day->id) }}" 
                           class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition text-sm font-semibold shadow-md">
                           Kelola Jadwal
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection