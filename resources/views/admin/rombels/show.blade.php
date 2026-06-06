@extends('admin.layout')

@section('header', 'Kelola Siswa: ' . $rombel->name)

@section('content')
<div class="p-6" x-data="{ 
    selectedStudents: [], 
    showAddModal: false, 
    showMoveModal: false,
    selectAll: false,
    searchQuery: '',
    toggleAll() {
        this.selectAll = !this.selectAll;
        this.selectedStudents = this.selectAll ? {{ $rombel->students->pluck('id') }} : [];
    }
}">
    
    <div x-show="selectedStudents.length > 0" class="mb-6 p-4 bg-indigo-100 rounded-xl flex items-center justify-between shadow-sm transition-all duration-300">
        <span class="font-bold text-indigo-900" x-text="selectedStudents.length + ' Siswa dipilih'"></span>
        <div class="flex gap-2">
            <form action="{{ route('admin.rombels.bulk-remove', $rombel->id) }}" method="POST">
                @csrf @method('DELETE')
                <template x-for="id in selectedStudents"><input type="hidden" name="student_ids[]" :value="id"></template>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">Hapus Terpilih</button>
            </form>
            <button @click="showMoveModal = true" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-yellow-700">Pindah Rombel</button>
        </div>
    </div>

    <div class="flex justify-between mb-6">
        <a href="{{ route('admin.rombels.index') }}" class="text-indigo-600 hover:underline">&larr; Kembali ke Rombel</a>
        <button @click="showAddModal = true" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">+ Tambah Siswa</button>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg border">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="p-3 w-10"><input type="checkbox" @click="toggleAll()" :checked="selectAll"></th>
                    <th class="p-3">NISN</th>
                    <th class="p-3">Nama</th>
                    <th class="p-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rombel->students as $student)
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="p-3"><input type="checkbox" :value="{{ $student->id }}" x-model="selectedStudents"></td>
                    <td class="p-3">{{ $student->nisn }}</td>
                    <td class="p-3">{{ $student->name }}</td>
                    <td class="p-3 text-center">
                        <form action="{{ route('admin.rombels.remove-student', [$rombel->id, $student->id]) }}" method="POST" onsubmit="return confirm('Hapus siswa ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-bold">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="showAddModal" 
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" 
         @click.self="showAddModal = false">
         
        <div class="bg-white p-8 rounded-2xl w-[500px] relative shadow-2xl">
            <button @click="showAddModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 text-2xl font-bold">&times;</button>
            
            <h3 class="font-bold mb-4 text-xl">Tambah Siswa</h3>
            
            <input type="text" x-model="searchQuery" placeholder="Cari NISN atau Nama..." class="w-full border p-3 rounded-lg mb-4 focus:ring-2 focus:ring-indigo-500 outline-none">
            
            <form action="{{ route('admin.rombels.bulk-add', $rombel->id) }}" method="POST">
                @csrf
                <div class="max-h-60 overflow-y-auto mb-4 border p-2 rounded-lg bg-gray-50">
                    @foreach(\App\Models\Student::all() as $s)
                    <label class="block p-2 hover:bg-indigo-100 cursor-pointer rounded transition"
                           x-show="searchQuery === '' || '{{ $s->nisn }}'.includes(searchQuery) || '{{ strtolower($s->name) }}'.includes(searchQuery.toLowerCase())">
                        <input type="checkbox" name="student_ids[]" value="{{ $s->id }}"> 
                        {{ $s->nisn }} - {{ $s->name }}
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-bold">Simpan Pilihan</button>
            </form>
        </div>
    </div>

    <div x-show="showMoveModal" 
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" 
         @click.self="showMoveModal = false">
         
        <div class="bg-white p-8 rounded-2xl w-[400px] relative shadow-2xl">
            <button @click="showMoveModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 text-2xl font-bold">&times;</button>
            
            <h3 class="font-bold mb-4 text-xl">Pindah ke Rombel Lain</h3>
            <form action="{{ route('admin.rombels.bulk-move', $rombel->id) }}" method="POST">
                @csrf
                <template x-for="id in selectedStudents"><input type="hidden" name="student_ids[]" :value="id"></template>
                
                <label class="block text-sm text-gray-600 mb-2">Pilih Rombel Tujuan:</label>
                <select name="target_rombel_id" class="w-full border p-2 mb-4 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                    @foreach(\App\Models\Rombel::where('id', '!=', $rombel->id)->get() as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 font-bold">Konfirmasi Pindah</button>
            </form>
        </div>
    </div>
</div>
@endsection