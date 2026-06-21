@extends('admin.layout')

@section('header', 'Manajemen Rombongan Belajar')

@section('content')
<div class="p-4 md:p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    name: '',
    guru_id: '',
    academic_year_id: ''
}">
    
    <div class="flex flex-col gap-4 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3">
            <h2 class="text-lg md:text-xl font-bold text-gray-800">Daftar Rombel</h2>

            <button @click="showModal = true; editMode = false; formUrl = '{{ route('admin.rombels.store') }}'; name=''; guru_id=''; academic_year_id=''" 
                    class="w-full sm:w-auto bg-indigo-600 text-white px-4 py-2.5 rounded-lg hover:bg-indigo-700 transition shadow-lg text-sm font-semibold">
                + Tambah Rombel
            </button>
        </div>

        <form action="{{ route('admin.rombels.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama rombel..." 
                   class="w-full sm:flex-1 border border-gray-300 rounded-lg px-4 py-2 bg-white/50 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
            <div class="flex gap-2">
                <button type="submit" class="flex-1 sm:flex-none bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition text-sm">Cari</button>
                <a href="{{ route('admin.rombels.index') }}" class="flex-1 sm:flex-none bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-center text-sm">Reset</a>
            </div>
        </form>
    </div>

    {{-- ===== MOBILE: Card List (tampil di bawah md) ===== --}}
    <div class="md:hidden space-y-3">
        @forelse($rombels as $rombel)
        <div class="bg-white/70 backdrop-blur-md shadow-md rounded-xl border border-white/30 p-4">
            <div class="flex justify-between items-start gap-3 mb-3">
                <div>
                    <p class="font-bold text-gray-800 text-base">{{ $rombel->name }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $rombel->academicYear->name ?? '-' }}</p>
                </div>
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                    {{ $rombel->students->count() }} Siswa
                </span>
            </div>

            <div class="text-sm text-gray-600 mb-4">
                <span class="text-xs font-bold text-gray-400 uppercase">Wali Kelas:</span>
                {{ $rombel->waliKelas->name ?? '-' }}
            </div>

            <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-200/70">
                <a href="{{ route('admin.rombels.show', $rombel->id) }}" 
                   class="flex-1 text-center bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition text-xs font-semibold min-w-[90px]">Kelola</a>
                <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.rombels.update', $rombel->id) }}'; name='{{ $rombel->name }}'; guru_id='{{ $rombel->guru_id }}'; academic_year_id='{{ $rombel->academic_year_id }}'"
                        class="flex-1 text-center bg-indigo-50 text-indigo-700 px-3 py-2 rounded-lg hover:bg-indigo-100 transition text-xs font-semibold min-w-[90px]">Edit</button>
                <form action="{{ route('admin.rombels.delete', $rombel->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')" class="flex-1 min-w-[90px]">
                    @csrf @method('POST')
                    <button type="submit" class="w-full bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 transition text-xs font-semibold">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white/70 rounded-xl border border-white/30 p-6 text-center text-gray-500 text-sm">
            Data tidak ditemukan.
        </div>
        @endforelse
    </div>

    {{-- ===== DESKTOP/TABLET: Table (tampil mulai md) ===== --}}
    <div class="hidden md:block bg-white/70 backdrop-blur-md shadow-xl rounded-xl overflow-hidden border border-white/30">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                    <tr class="bg-gray-100/50">
                        <th class="p-4 text-sm font-bold text-gray-700">Nama Rombel</th>
                        <th class="p-4 text-sm font-bold text-gray-700">Wali Kelas</th>
                        <th class="p-4 text-sm font-bold text-gray-700">Tahun</th>
                        <th class="p-4 text-sm font-bold text-gray-700 text-center">Siswa</th>
                        <th class="p-4 text-sm font-bold text-gray-700 text-center">Management</th>
                        <th class="p-4 text-sm font-bold text-gray-700 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/50">
                    @forelse($rombels as $rombel)
                    <tr class="hover:bg-indigo-50/50 transition-colors text-sm">
                        <td class="p-4 font-semibold text-gray-800">{{ $rombel->name }}</td>
                        <td class="p-4 text-gray-600">{{ $rombel->waliKelas->name ?? '-' }}</td>
                        <td class="p-4 text-gray-600">{{ $rombel->academicYear->name ?? '-' }}</td>
                        <td class="p-4 text-center font-bold text-indigo-600">{{ $rombel->students->count() }}</td>
                        <td class="p-4 text-center">
                            <a href="{{ route('admin.rombels.show', $rombel->id) }}" 
                               class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 transition text-xs whitespace-nowrap">Kelola</a>
                        </td>
                        <td class="p-4">
                            <div class="flex gap-3 justify-center items-center">
                                <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.rombels.update', $rombel->id) }}'; name='{{ $rombel->name }}'; guru_id='{{ $rombel->guru_id }}'; academic_year_id='{{ $rombel->academic_year_id }}'"
                                        class="text-indigo-600 hover:text-indigo-900 font-bold text-xs whitespace-nowrap">Edit</button>
                                <form action="{{ route('admin.rombels.delete', $rombel->id) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf @method('POST')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-xs whitespace-nowrap">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="p-6 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex justify-center sm:justify-start">
        {{ $rombels->links() }}
    </div>

    {{-- ===== MODAL ===== --}}
    <div x-show="showModal" x-cloak 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50 p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white p-5 sm:p-6 rounded-2xl w-full max-w-md shadow-2xl my-8" @click.away="showModal = false">
            <h3 class="text-lg font-extrabold text-gray-900 mb-4" x-text="editMode ? 'Edit Rombel' : 'Tambah Rombel'"></h3>
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="POST"></template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Rombel</label>
                        <input type="text" name="name" x-model="name" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Wali Kelas</label>
                        <select name="guru_id" x-model="guru_id" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
                            <option value="">Pilih Wali Kelas</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tahun Akademik</label>
                        <select name="academic_year_id" x-model="academic_year_id" class="w-full border rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-indigo-500 text-sm" required>
                            <option value="">Pilih Tahun</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 mt-6">
                    <button type="button" @click="showModal = false" class="px-4 py-2.5 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection