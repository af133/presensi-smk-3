<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Guru | Presensi</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/icon/logo-smk3.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-cover bg-center bg-no-repeat" style="background-image: url('https://tugujatim.id/wp-content/uploads/2024/05/a1131603-9b3e-48c5-bf3d-c14c912975aa-1-4.jpeg');">
    
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8 bg-gray-900/70 backdrop-blur-sm">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md bg-gray-800/60 p-8 rounded-2xl shadow-2xl border border-white/10 backdrop-blur-md">
            
            <div class="sm:mx-auto sm:w-full sm:max-w-sm text-center">
                <div class="mx-auto h-16 w-16 mb-4">
                    <img src="{{ asset('storage/icon/logo-smk3.png') }}" alt="Logo SMK" class="w-full h-full object-contain">
                </div>
                <h2 class="text-2xl font-bold tracking-tight text-white">Portal Guru</h2>
                <p class="mt-2 text-sm text-gray-400">Silakan masukkan kredensial guru untuk melakukan presensi</p>
            </div>

            <div class="mt-8">
                <form action="{{ route('guru.login.process') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="p-3 text-sm text-red-400 bg-red-900/30 border border-red-500/20 rounded-lg">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <div>
                        <label for="nip" class="block text-sm font-medium text-gray-200">NIP / NIk</label>
                        <div class="mt-2">
                            <input id="nip" name="nip" type="text" required 
                                class="block w-full rounded-lg bg-white/5 border-0 py-2.5 px-3 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-emerald-500 sm:text-sm"
                                placeholder="Masukkan NIP atau NIS Anda">
                        </div>
                    </div>
                     <div class="relative">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-200">Kata Sandi</label>
                    </div>
                    <div class="mt-2 relative">
                        <input id="password" name="password" type="password" required 
                            class="block w-full rounded-lg bg-white/5 border-0 py-2.5 px-3 pr-10 text-white shadow-sm ring-1 ring-inset ring-white/10 focus:ring-2 focus:ring-emerald-500 sm:text-sm">
                        
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400">
                            <svg id="eyeIconOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg id="eyeIconClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                    <div class="flex items-center gap-2">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 bg-gray-700 text-emerald-600 focus:ring-emerald-600">
                        <label for="remember" class="text-sm text-gray-300">Ingat saya</label>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-all">
                            Masuk ke Portal Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIconOpen = document.querySelector('#eyeIconOpen');
    const eyeIconClose = document.querySelector('#eyeIconClose');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        eyeIconOpen.classList.toggle('hidden');
        eyeIconClose.classList.toggle('hidden');
    });
</script>
</body>
</html>