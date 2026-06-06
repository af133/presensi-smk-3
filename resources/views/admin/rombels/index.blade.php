@extends('admin.layout')

@section('header', 'Manajemen Rombongan Belajar')

@section('content')
<div class="p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    name: '',
    guru_id: '',
    academic_year_id: ''
}">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Rombel</h2>
        
        <form action="{{ route('admin.rombels.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama rombel..." 
                   class="w-full md:w-64 border border-gray-300 rounded-lg px-4 py-2 bg-white/50 focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition">Cari</button>
            <a href="{{ route('admin.rombels.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Reset</a>
        </form>

        <button @click="showModal = true; editMode = false; formUrl = '{{ route('admin.rombels.store') }}'; name=''; guru_id=''; academic_year_id=''" 
                class="bg-indigo-600/80 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-indigo-700/90 transition shadow-lg w-full md:w-auto">
            + Tambah Rombel
        </button>
    </div>

    <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-xl overflow-hidden border border-white/30">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100/50">
                    <th class="p-4 text-sm font-bold text-gray-700">Nama Rombel</th>
                    <th class="p-4 text-sm font-bold text-gray-700">Wali Kelas</th>
                    <th class="p-4 text-sm font-bold text-gray-700">Tahun Akademik</th>
                    <th class="p-4 text-sm font-bold text-gray-700 text-center">Jml Siswa</th>
                    <th class="p-4 text-sm font-bold text-gray-700 text-center">Management</th>
                    <th class="p-4 text-sm font-bold text-gray-700 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/50">
                @forelse($rombels as $rombel)
                <tr class="hover:bg-indigo-50/50 transition-colors">
                    <td class="p-4 font-semibold text-gray-800">{{ $rombel->name }}</td>
                    <td class="p-4 text-gray-600">{{ $rombel->waliKelas->name ?? '-' }}</td>
                    <td class="p-4 text-gray-600">{{ $rombel->academicYear->name ?? '-' }}</td>
                    <td class="p-4 text-center font-bold text-indigo-600">{{ $rombel->students->count() }}</td>
                    <td class="p-4 text-center">
                        <a href="{{ route('admin.rombels.show', $rombel->id) }}" 
                           class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition text-sm">Kelola</a>
                    </td>
                    <td class="p-4 flex gap-3 justify-center">
                        <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.rombels.update', $rombel->id) }}'; name='{{ $rombel->name }}'; guru_id='{{ $rombel->guru_id }}'; academic_year_id='{{ $rombel->academic_year_id }}'"
                                class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</button>
                        
                        <form action="{{ route('admin.rombels.delete', $rombel->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-4 text-center text-gray-500">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $rombels->links() }}
    </div>

    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl w-[450px] shadow-2xl border border-white/30" @click.away="showModal = false">
            <h3 class="text-xl font-extrabold text-gray-900 mb-6" x-text="editMode ? 'Edit Rombel' : 'Tambah Rombel'"></h3>
            
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Rombel</label>
                    <input type="text" name="name" x-model="name" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Wali Kelas</label>
                    <select name="guru_id" x-model="guru_id" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                        <option value="">Pilih Wali Kelas</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tahun Akademik</label>
                    <select name="academic_year_id" x-model="academic_year_id" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                        <option value="">Pilih Tahun</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection