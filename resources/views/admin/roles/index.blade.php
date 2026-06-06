@extends('admin.layout')

@section('content')
<div x-data="{ createModal: false, editModal: false, roleName: '', roleId: '' }" class="p-6">
    
 
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-bold">Manajemen Role</h2>
        <button @click="createModal = true" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
            + Tambah Role
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Role</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr class="border-t">
                    <td class="px-6 py-4 capitalize">{{ $role->name }}</td>
                    <td class="px-6 py-4 text-right">
                        <button @click="editModal = true; roleName = '{{ $role->name }}'; roleId = '{{ $role->id }}'" 
                                class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                        
                        <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin ingin hapus?')" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="createModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-6 rounded-lg w-96" @click.away="createModal = false">
            <h3 class="font-bold mb-4">Tambah Role Baru</h3>
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <input type="text" name="name" class="w-full border rounded p-2 mb-4" placeholder="Nama Role" required>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="editModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50">
        <div class="bg-white p-6 rounded-lg w-96" @click.away="editModal = false">
            <h3 class="font-bold mb-4">Edit Role</h3>
            <form :action="'{{ url('admin/roles/update') }}/' + roleId" method="POST">
                @csrf
                <input type="text" name="name" x-model="roleName" class="w-full border rounded p-2 mb-4" required>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection