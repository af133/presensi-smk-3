@extends('admin.layout')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-sm text-gray-500 uppercase font-bold">Total Siswa</h3>
            <p class="text-3xl font-bold mt-2">1,240</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-sm text-gray-500 uppercase font-bold">Guru Aktif</h3>
            <p class="text-3xl font-bold mt-2">45</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="text-sm text-gray-500 uppercase font-bold">Piket Hari Ini</h3>
            <p class="text-3xl font-bold mt-2">12</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-700 mb-4">Aktivitas Terakhir</h3>
        <table class="w-full text-left">
            <thead>
                <tr class="text-gray-400 border-b">
                    <th class="pb-3">User</th>
                    <th class="pb-3">Aksi</th>
                    <th class="pb-3">Waktu</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b">
                    <td class="py-3">Budi Santoso</td>
                    <td class="py-3">Input Presensi</td>
                    <td class="py-3 text-sm text-gray-500">10:00 AM</td>
                </tr>
                <tr>
                    <td class="py-3">Siti Aminah</td>
                    <td class="py-3">Update Jadwal</td>
                    <td class="py-3 text-sm text-gray-500">09:30 AM</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection