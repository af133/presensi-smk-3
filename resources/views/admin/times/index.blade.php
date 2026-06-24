@extends('admin.layout')
@section('header', 'Daftar Jam Pelajaran')

@section('content')
<div class="pb-11 md:p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    day_id: '{{ request('day_id') }}',
    jam_ke: '', start: '', end: '' 
}">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="w-full md:w-64">
            <select onchange="window.location.href = this.value" 
                    class="w-full p-2.5 rounded-lg border border-gray-300 bg-white shadow-sm focus:ring-2 focus:ring-indigo-500">
                <option value="{{ route('admin.times.index') }}">Semua Hari</option>
                @foreach($days as $day)
                    <option value="{{ route('admin.times.index', ['day_id' => $day->id]) }}" 
                            {{ request('day_id') == $day->id ? 'selected' : '' }}>
                        {{ $day->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button @click="showModal = true; editMode = false; formUrl = '{{ route('admin.times.store') }}'; day_id=''; jam_ke=''; start=''; end=''" 
                class="w-full md:w-auto bg-indigo-600/80 backdrop-blur-sm text-white px-4 py-2.5 rounded-lg hover:bg-indigo-700/90 transition shadow-lg font-semibold">
            + Tambah Jam
        </button>
    </div>

    <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-xl overflow-hidden border border-white/30">
        <div class="overflow-x-auto"> <table class="w-full text-left border-collapse min-w-[600px]"> <thead>
                    <tr class="bg-gray-100/50">
                        <th class="p-4 text-sm font-bold text-gray-700">Hari</th>
                        <th class="p-4 text-sm font-bold text-gray-700">Jam Ke</th>
                        <th class="p-4 text-sm font-bold text-gray-700">Mulai</th>
                        <th class="p-4 text-sm font-bold text-gray-700">Selesai</th>
                        <th class="p-4 text-sm font-bold text-gray-700 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($times as $time)
                    <tr class="hover:bg-indigo-50/50 transition-colors">
                        <td class="p-4 font-semibold text-gray-800">{{ $time->day->name }}</td>
                        <td class="p-4 text-gray-600">{{ $time->jam_ke }}</td>
                        <td class="p-4 text-gray-600 font-mono">{{ date('H:i', strtotime($time->start_time)) }}</td>
                        <td class="p-4 text-gray-600 font-mono">{{ date('H:i', strtotime($time->end_time)) }}</td>
                        <td class="p-4 flex gap-3 justify-center">
                            <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.times.update', $time->id) }}'; day_id='{{ $time->day_id }}'; jam_ke='{{ $time->jam_ke }}'; start='{{ date('H:i', strtotime($time->start_time)) }}'; end='{{ date('H:i', strtotime($time->end_time)) }}'"
                                    class="text-indigo-600 hover:text-indigo-900 font-semibold">Edit</button>
                            
                            <form action="{{ route('admin.times.delete', $time->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf @method('POST')
                                <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-500 italic">Tidak ada data untuk hari ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="py-4">
        {{ $times->links() }}
    </div>

    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-4">
        <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl w-full max-w-md shadow-2xl border border-white/30" @click.away="showModal = false">
            <h3 class="text-xl font-extrabold text-gray-900 mb-6" x-text="editMode ? 'Edit Jam Pelajaran' : 'Tambah Jam Pelajaran'"></h3>
            
            <form :action="formUrl" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Hari</label>
                    <select name="day_id" x-model="day_id" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg" required>
                        <option value="">-- Pilih Hari --</option>
                        @foreach($days as $day)
                            <option value="{{ $day->id }}">{{ $day->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Jam/Jam Ke</label>
                    <input type="text" name="jam_ke" x-model="jam_ke" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg" required>
                </div>
                <div class="flex gap-4">
                    <div class="mb-5 flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Mulai</label>
                        <input type="time" name="start_time" x-model="start" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg" required>
                    </div>
                    <div class="mb-5 flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Selesai</label>
                        <input type="time" name="end_time" x-model="end" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg" required>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection