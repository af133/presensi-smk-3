@extends('admin.layout')

@section('header', 'Manajemen Mata Pelajaran')

@section('content')
<div class="p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    name: '' 
}">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Ruang Kelas</h2>
        
        <form action="{{ route('admin.classrooms.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama..." 
                   class="w-full md:w-64 border border-gray-300 rounded-lg px-4 py-2 bg-white/50 focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition">Cari</button>
            <a href="{{ route('admin.classrooms.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Reset</a>
        </form>

        <button @click="showModal = true; editMode = false; formUrl = '{{ route('admin.classrooms.store') }}'; name=''" 
                class="bg-indigo-600/80 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-indigo-700/90 transition shadow-lg w-full md:w-auto">
            + Tambah Ruangan
        </button>
    </div>

    <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-xl overflow-hidden border border-white/30">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="p-4 font-bold text-gray-700">Nama</th>
                    <th class="p-4 font-bold text-gray-700 text-right">Aksi</th>
                </tr>
            </thead>
            
            <tbody class="divide-y divide-gray-200/50">
                @forelse($classrooms as $item)
                <tr class="hover:bg-indigo-50/50 transition-colors">
                    <td class="p-4 font-semibold text-gray-800">{{ $item->name }}</td>
                    <td class="p-4 flex gap-3 justify-end">
                        <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.classrooms.update', $item->id) }}'; name='{{ $item->name }}'"
                                class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</button>
                        
                        <form action="{{ route('admin.classrooms.delete', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="2" class="p-4 text-center text-gray-500">Data tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $classrooms->links() }}
    </div>

    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl w-[400px] shadow-2xl border border-white/30" @click.away="showModal = false">
            <h3 class="text-xl font-extrabold text-gray-900 mb-6" x-text="editMode ? 'Edit Ruang Kelas' : 'Tambah Ruang Kelas'"></h3>
            
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="POST"></template>
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" x-model="name" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>
                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" @click="showModal = false" class="px-5 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
