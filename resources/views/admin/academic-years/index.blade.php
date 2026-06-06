@extends('admin.layout')

@section('header', 'Manajemen Tahun Akademik')

@section('content')
<div class="p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    name: '',
    is_active: false 
}">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Tahun Akademik</h2>
        
        <form action="{{ route('admin.academic-years.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tahun..." 
                   class="w-full md:w-64 border border-gray-300 rounded-lg px-4 py-2 bg-white/50 focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition">Cari</button>
            <a href="{{ route('admin.academic-years.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">Reset</a>
        </form>

        <button @click="showModal = true; editMode = false; formUrl = '{{ route('admin.academic-years.store') }}'; name=''; is_active=false" 
                class="bg-indigo-600/80 backdrop-blur-sm text-white px-4 py-2 rounded-lg hover:bg-indigo-700/90 transition shadow-lg w-full md:w-auto">
            + Tambah Tahun
        </button>
    </div>

    <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-xl overflow-hidden border border-white/30">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100/50">
                    <th class="p-4 text-sm font-bold text-gray-700">Nama Tahun</th>
                    <th class="p-4 text-sm font-bold text-gray-700">Status</th>
                    <th class="p-4 text-sm font-bold text-gray-700 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200/50">
                @forelse($years as $item)
                <tr class="hover:bg-indigo-50/50 transition-colors">
                    <td class="p-4 font-semibold text-gray-800">{{ $item->name }}</td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="p-4 flex gap-3 justify-end">
                        <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.academic-years.update', $item->id) }}'; name='{{ $item->name }}'; is_active={{ $item->is_active ? 'true' : 'false' }}"
                                class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm">Edit</button>
                        
                        <form action="{{ route('admin.academic-years.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf @method('POST')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-semibold text-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="p-4 text-center text-gray-500">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t">
        {{ $years->links() }}
    </div>

    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-50">
        <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl w-[400px] shadow-2xl border border-white/30" @click.away="showModal = false">
            <h3 class="text-xl font-extrabold text-gray-900 mb-6" x-text="editMode ? 'Edit Tahun Akademik' : 'Tambah Tahun Akademik'"></h3>
            
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="POST"></template>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Tahun</label>
                    <input type="text" name="name" x-model="name" placeholder="Contoh: 2025/2026" class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>
                
                <div class="mb-4 flex items-center gap-2">
                    <input type="checkbox" name="is_active" x-model="is_active" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500">
                    <label class="text-sm font-semibold text-gray-700">Tahun Aktif</label>
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