@extends('admin.layout')
@section('content')
<div class="p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    formData: { name: '', nisn: '', rombel_id: '' }
}">

    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">

        <form action="{{ route('admin.students.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari NISN atau Nama..." 
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none w-64">
            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900">Cari</button>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
        </form>

        <button @click="
            showModal = true; editMode = false; 
            formUrl = '{{ route('admin.students.store') }}';
            formData = { name: '', nisn: '', rombel_id: '' }
        " class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg hover:bg-indigo-700">
            + Tambah Siswa
        </button>
    </div>

    <div class="bg-white shadow-xl rounded-xl overflow-hidden border">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-4">NISN</th>
                    <th class="p-4">Nama</th>
                    <th class="p-4">Rombel</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="p-4">{{ $student->nisn }}</td>
                    <td class="p-4 font-semibold">{{ $student->name }}</td>
                    <td class="p-4">
                        @foreach($student->rombels as $r)
                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded">{{ $r->name }}</span>
                        @endforeach
                    </td>
                    <td class="p-4 text-center flex gap-2 justify-center">
                        <button @click="
                            showModal = true; editMode = true; 
                            formUrl = '{{ route('admin.students.update', $student->id) }}';
                            formData = { 
                                name: '{{ $student->name }}', 
                                nisn: '{{ $student->nisn }}', 
                                rombel_id: '{{ $student->rombels->first()?->id ?? '' }}' 
                            }
                        " class="text-blue-600 hover:underline">Edit</button>
                        
                        <form action="{{ route('admin.students.delete', $student->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $students->links() }}
        </div>
    </div>

    <div x-show="showModal" class="fixed inset-0 flex items-center justify-center bg-black/40 p-4 z-50">
        <div class="bg-white p-6 rounded-2xl w-full max-w-sm" @click.away="showModal = false">
            <h3 class="text-lg font-bold mb-4" x-text="editMode ? 'Edit Siswa' : 'Tambah Siswa'"></h3>
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm">NISN</label>
                        <input type="text" name="nisn" x-model="formData.nisn" class="w-full border p-2 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm">Nama</label>
                        <input type="text" name="name" x-model="formData.name" class="w-full border p-2 rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm">Rombel</label>
                        <select name="rombel_id" x-model="formData.rombel_id" class="w-full border p-2 rounded" required>
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombels as $r) <option value="{{ $r->id }}">{{ $r->name }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection