<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreGuruRequest;
use App\Models\Student;
use App\Models\Rombel;
use App\Models\Presence;
use OpenSpout\Reader\XLSX\Reader;
use Illuminate\Support\Facades\DB;
use App\Models\Journal;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\UpdateGuruRequest;
class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_siswa'   => Student::count(),
            'total_guru'    => User::whereHas('roles', fn($q) => $q->where('name', 'guru'))->count(),
            'total_rombel'  => Rombel::count(),
            'presensi_hari_ini' => Presence::whereDate('date', today())->count(),
        ];

        // Mengambil 5 jurnal terbaru
        $recent_journals = Journal::with(['presence.schedule.subject', 'presence.schedule.teacher'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact('stats', 'recent_journals'));
    }
    public function indexRole() {
        $roles = Role::paginate(10)
        ->withQueryString();
        return view('admin.roles.index', compact('roles'));
    }

   public function storeRole(Request $request)
    {
        $request->validate(['name' => 'required|unique:roles']);

        Role::create([
            'name' => $request->name,
            'can_jadwal_kelas' => $request->has('can_jadwal_kelas'),
            'can_laporan_presensi_siswa_guru' => $request->has('can_laporan_presensi_siswa_guru'),
            'can_laporan_presensi_siswa_all' => $request->has('can_laporan_presensi_siswa_all'),
            'can_laporan_presensi_guru' => $request->has('can_laporan_presensi_guru'),
            'can_monitoring_kelas' => $request->has('can_monitoring_kelas'),
            'can_laporan_jurnal_pembelajaran' => $request->has('can_laporan_jurnal_pembelajaran'),
            'can_laporan_presensi_siswa_perguru' => $request->has('can_laporan_presensi_siswa_perguru'),
        ]);

        return back()->with('success', 'Role berhasil dibuat');
    }

    public function roleUpdate(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $role->update([
            'name' => $request->name,
            'can_jadwal_kelas' => $request->has('can_jadwal_kelas'),
            'can_laporan_presensi_siswa_guru' => $request->has('can_laporan_presensi_siswa_guru'),
            'can_laporan_presensi_siswa_all' => $request->has('can_laporan_presensi_siswa_all'),
            'can_laporan_presensi_guru' => $request->has('can_laporan_presensi_guru'),
            'can_monitoring_kelas' => $request->has('can_monitoring_kelas'),
            'can_laporan_jurnal_pembelajaran' => $request->has('can_laporan_jurnal_pembelajaran'),
             'can_laporan_presensi_siswa_perguru' => $request->has('can_laporan_presensi_siswa_perguru'),
        ]);
        return back()->with('success', 'Role berhasil diupdate');
    }

    public function destroyRole($id) {
        try {
            Role::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Role berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus role.');
        }
    }

    public function guruIndex(Request $request)
    {
        $roles = Role::where('name', '!=', 'admin')->get();

        $gurus = User::query()
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'admin');
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('nip', 'like', '%' . $request->search . '%');
                });
            })
            
            ->when($request->role_id, function ($query) use ($request) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('roles.id', $request->role_id);
                });
            })
            ->paginate(10)
            ->withQueryString();;

            return view('admin.guru.index', compact('gurus', 'roles'));
    }
    public function createGuru() {
        $roles = Role::where('name', '!=', 'admin')->get();
        return view('admin.guru.create', compact('roles'));
    }

   public function storeGuru(StoreGuruRequest $request) 
    {
        try{

                $data = $request->validated();
                
                $user = User::create([
                'name'     => $data['name'],
                'nip'      => $data['nip'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'type'     => 'guru',
            ]);
            
            $user->roles()->attach($data['role_id']);

            return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal simpan data guru.');
        }
    }
    public function editGuru($id) {
        $guru = User::findOrFail($id);
        $roles = Role::where('name', '!=', 'admin')->get();
        return view('admin.guru.edit', compact('guru', 'roles'));
    }

    public function updateGuru(UpdateGuruRequest $request, $id) 
    {
        $guru = User::findOrFail($id);
        $guru->update([
            'name'  => $request->name,
            'nip'   => $request->nip,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $guru->password,
        ]);

        $guru->roles()->sync([$request->role_id]);

        return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diupdate!');
    }

    public function destroyGuru($id) {
        $guru = User::findOrFail($id);
        $guru->roles()->detach();
        $guru->delete();

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus!');
    }

    public function edit()
    {
        return view('admin.profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
    public function updateStatus(Request $request, $id)
    {
        $guru = User::findOrFail($id);
        $guru->status = $request->status;
        $guru->save();

        return back()->with('success', 'Status berhasil diubah!');
    }
public function import(Request $request)
{
    $request->validate(['file' => 'required|mimes:xlsx']);

    $reader = new Reader();
    $reader->open($request->file('file')->getPathname());

    DB::transaction(function () use ($reader) {
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $index => $row) {
                if ($index === 1) continue;

                $cells = $row->toArray();
                if (empty($cells[2])) continue; 
                $user = User::updateOrCreate(
                    ['email' => $cells[2]],
                    [
                        'name'     => $cells[0],
                        'nip'      => $cells[1],
                        'password' => Hash::make('password123'),
                        'status'   => 1,
                    ]
                );
                $role = Role::where('name', 'guru')->first();
                if ($role && !$user->hasRole('guru')) {
                    $user->roles()->attach($role->id);
                }
            }
            break;
        }
    });
    $reader->close();
    return back()->with('success', 'Data berhasil diimport!');
}
}

