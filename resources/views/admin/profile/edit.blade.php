@extends('admin.layout')

@section('header', 'Edit Profil Saya')

@section('content')
<div class="max-w-2xl mx-auto pb-10 md:p-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white/70 backdrop-blur-md shadow-xl rounded-2xl border border-white/30 p-8">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition" required>
                </div>

                <hr class="my-6">

                <h3 class="font-bold text-gray-800">Ubah Password</h3>
                <p class="text-xs text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Password Baru</label>
                        <input type="password" name="password" 
                               class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" 
                               class="w-full border border-gray-300 bg-white/50 p-3 rounded-lg focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-indigo-700 transition shadow-lg">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection