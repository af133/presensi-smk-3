@extends('admin.layout')
@section('header', 'Manajemen Role')

@section('content')
<div x-data="{ 
    createModal: false, editModal: false, roleName: '', roleId: '',
    canJadwal: false, canPresensiGuru: false, canPresensiPerGuru: false, // Tambahkan ini
    canPresensiAll: false, canPresensiGuruOnly: false, canMonitoring: false, canJurnal: false,
    
    resetForm() {
        this.roleName = ''; this.canJadwal = false; this.canPresensiGuru = false;
        this.canPresensiPerGuru = false; // Tambahkan ini
        this.canPresensiAll = false; this.canPresensiGuruOnly = false;
        this.canMonitoring = false; this.canJurnal = false;
    },
    openEdit(role) {
        this.editModal = true;
        this.roleId = role.id;
        this.roleName = role.name;
        this.canJadwal = role.can_jadwal_kelas;
        this.canPresensiGuru = role.can_laporan_presensi_siswa_guru;
        this.canPresensiPerGuru = role.can_laporan_presensi_siswa_perguru; // Tambahkan ini
        this.canPresensiAll = role.can_laporan_presensi_siswa_all;
        this.canPresensiGuruOnly = role.can_laporan_presensi_guru;
        this.canMonitoring = role.can_monitoring_kelas;
        this.canJurnal = role.can_laporan_jurnal_pembelajaran;
    }
}" class="space-y-6 px-0 md:px-6 pb-10">
    
    <button @click="createModal = true; resetForm()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg my-6 hover:bg-indigo-700">
        + Tambah Role
    </button>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left">Nama Role</th>
                    <th class="px-6 py-3 text-left">Hak Akses</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr class="border-b">
                    <td class="px-6 py-2 capitalize font-medium">{{ $role->name }}</td>
                    
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @if($role->can_jadwal_kelas) <span class="bg-indigo-100 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded">Jadwal</span> @endif
                            
                            @if($role->can_laporan_presensi_siswa_guru) <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded">Laporan Presensi Siswa (Wali Kelas)</span> @endif
                            @if($role->can_laporan_presensi_siswa_perguru) <span class="bg-teal-100 text-teal-700 text-[10px] font-bold px-2 py-0.5 rounded">Laporan Presensi Siswa (Per Guru)</span> @endif
                            
                            @if($role->can_laporan_presensi_siswa_all) <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded">Laporan Presensi Semua Siswa (BK)</span> @endif
                            @if($role->can_laporan_presensi_guru) <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded">Laporan Presensi Guru</span> @endif
                            @if($role->can_monitoring_kelas) <span class="bg-purple-100 text-purple-700 text-[10px] font-bold px-2 py-0.5 rounded">Monitoring</span> @endif
                            @if($role->can_laporan_jurnal_pembelajaran) <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded">Jurnal</span> @endif
                        </div>
                    </td>

                    <td class="px-6 py-4 text-right">
                        <button @click="openEdit({{ json_encode($role) }})" class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                        <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Yakin?')" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="createModal || editModal" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 p-4">
        <div class="bg-white p-6 rounded-lg w-full max-w-md" @click.away="createModal = false; editModal = false">
            <h3 class="font-bold mb-4" x-text="editModal ? 'Edit Role' : 'Tambah Role Baru'"></h3>
            <form :action="editModal ? '{{ url('admin/roles/update') }}/' + roleId : '{{ route('admin.roles.store') }}'" method="POST">
                @csrf
                <input type="text" name="name" x-model="roleName" class="w-full border rounded p-2 mb-4" placeholder="Nama Role" required>
                
                <div class="space-y-2 text-sm max-h-60 overflow-y-auto border-t pt-2">
                    <label class="flex items-center"><input type="checkbox" name="can_jadwal_kelas" x-model="canJadwal" class="mr-2"> Jadwal Kelas</label>
                    
                    <label class="flex items-center"><input type="checkbox" name="can_laporan_presensi_siswa_guru" x-model="canPresensiGuru" class="mr-2"> Laporan Presensi Siswa (Wali Kelas)</label>
                    <label class="flex items-center"><input type="checkbox" name="can_laporan_presensi_siswa_perguru" x-model="canPresensiPerGuru" class="mr-2"> Laporan Presensi Siswa (Per Guru)</label>
                    
                    <label class="flex items-center"><input type="checkbox" name="can_laporan_presensi_siswa_all" x-model="canPresensiAll" class="mr-2"> Laporan Presensi Semua Siswa (BK)</label>
                    <label class="flex items-center"><input type="checkbox" name="can_laporan_presensi_guru" x-model="canPresensiGuruOnly" class="mr-2"> Laporan Presensi Guru</label>
                    <label class="flex items-center"><input type="checkbox" name="can_monitoring_kelas" x-model="canMonitoring" class="mr-2"> Monitoring Kelas</label>
                </div>

                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="createModal = false; editModal = false" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection