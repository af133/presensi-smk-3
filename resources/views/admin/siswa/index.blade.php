@extends('admin.layout')

@section('content')
<div class="pb-10 md:p-6" x-data="{ 
    showModal: false, 
    editMode: false, 
    formUrl: '',
    formData: { name: '', nisn: '', rombel_id: '' }
}">

    <div class="mb-6 flex flex-col gap-4 bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.students.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NISN atau Nama..." 
                class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none w-full">
            
            <select name="rombel_id" class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 outline-none w-full">
                <option value="">Semua Rombel</option>
                @foreach($rombels as $r)
                    <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
            
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition">Cari</button>
                <a href="{{ route('admin.students.index') }}" class="flex-1 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-center">Reset</a>
            </div>
        </form>

        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100">
            <button @click="showModal = true; editMode = false; formUrl = '{{ route('admin.students.store') }}'; formData = { name: '', nisn: '', rombel_id: '' }" 
                    class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700 font-medium transition w-full md:w-auto">
                + Tambah Siswa
            </button>
            <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" class="w-full md:w-auto">
                @csrf
                <label class="block bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg cursor-pointer transition text-center font-medium shadow-sm">
                    <input type="file" name="file" class="hidden" onchange="this.form.submit()">
                    Import Siswa
                </label>
            </form>
            <a href="{{ asset('doc/Tamplate Excel Siswa.xlsx') }}" class="text-indigo-600 hover:underline text-sm font-medium w-full md:w-auto text-center">Download Template</a>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="bg-white shadow-xl  rounded-xl overflow-hidden border">
        {{-- Desktop View --}}
        <div class="hidden md:block overflow-x-auto">
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
                        <td class="p-4 flex gap-2 justify-center">
                            <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.students.update', $student->id) }}'; formData = { name: '{{ $student->name }}', nisn: '{{ $student->nisn }}', rombel_id: '{{ $student->rombels->first()?->id ?? '' }}' }" 
                                    class="text-blue-600 hover:underline">Edit</button>
                            <form action="{{ route('admin.students.delete', $student->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('POST')
                                <button class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile View (Card List) --}}
        <div class="md:hidden divide-y">
            @foreach($students as $student)
            <div class="p-4 space-y-2">
                <div class="flex justify-between">
                    <span class="font-bold text-gray-800">{{ $student->name }}</span>
                    <span class="text-xs text-gray-500">{{ $student->nisn }}</span>
                </div>
                <div class="text-sm">
                    @foreach($student->rombels as $r)
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded text-xs">{{ $r->name }}</span>
                    @endforeach
                </div>
                <div class="flex gap-4 pt-2">
                    <button @click="showModal = true; editMode = true; formUrl = '{{ route('admin.students.update', $student->id) }}'; formData = { name: '{{ $student->name }}', nisn: '{{ $student->nisn }}', rombel_id: '{{ $student->rombels->first()?->id ?? '' }}' }" 
                            class="text-blue-600 font-medium">Edit</button>
                    <form action="{{ route('admin.students.delete', $student->id) }}" method="POST" onsubmit="return confirm('Hapus?')">
                        @csrf @method('POST')
                        <button class="text-red-600 font-medium">Hapus</button>
                    </form>
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
    <div class="p-4 ">
        {{ $students->links() }}
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/40 p-4 z-50">
        <div class="bg-white p-6 rounded-2xl w-full max-w-sm shadow-xl" @click.away="showModal = false">
            <h3 class="text-lg font-bold mb-4" x-text="editMode ? 'Edit Siswa' : 'Tambah Siswa'"></h3>
            <form :action="formUrl" method="POST">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="POST"></template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">NISN</label>
                        <input type="text" name="nisn" x-model="formData.nisn" class="w-full border p-2 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Nama</label>
                        <input type="text" name="name" x-model="formData.name" class="w-full border p-2 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Rombel</label>
                        <select name="rombel_id" x-model="formData.rombel_id" class="w-full border p-2 rounded-lg" required>
                            <option value="">-- Pilih Rombel --</option>
                            @foreach($rombels as $r) <option value="{{ $r->id }}">{{ $r->name }}</option> @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-gray-200 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection