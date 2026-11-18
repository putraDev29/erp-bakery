<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Bakery ERP Pro</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fbf8f3; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

<div class="max-w-md w-full">
    <!-- Brand Icon & Header -->
    <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-700 to-amber-500 text-white flex items-center justify-center mx-auto shadow-lg shadow-amber-700/20 mb-3">
            <i data-lucide="croissant" class="w-9 h-9"></i>
        </div>
        <h1 class="text-2xl font-bold text-stone-900 tracking-tight">Bakery ERP Pro</h1>
        <p class="text-xs text-stone-600 mt-1">Sistem Manajemen Usaha Makanan & Bakery Multi-Cabang</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl border border-stone-200/90 shadow-xl shadow-stone-200/50 p-7">
        <h2 class="text-base font-bold text-stone-800 mb-1">Masuk ke Sistem</h2>
        <p class="text-xs text-stone-500 mb-6">Gunakan kredensial yang telah terdaftar untuk melanjutkan.</p>

        @if($errors->any())
            <div class="mb-4 bg-rose-50 border border-rose-200 text-rose-800 px-3.5 py-2.5 rounded-xl text-xs flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-stone-700 mb-1.5">Alamat Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email', 'owner@bakeryerp.local') }}" required
                           class="w-full pl-9 pr-3.5 py-2.5 border border-stone-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition"
                           placeholder="nama@bakeryerp.local">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-stone-700 mb-1.5">Kata Sandi</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" id="password" name="password" value="password" required
                           class="w-full pl-9 pr-3.5 py-2.5 border border-stone-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition"
                           placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center space-x-2 text-stone-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                    <span>Ingat saya</span>
                </label>
                <span class="text-amber-700 hover:underline cursor-pointer text-[11px]">Bantuan Login?</span>
            </div>

            <button type="submit"
                    class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 shadow-md shadow-amber-600/25 transition flex items-center justify-center space-x-2">
                <span>Masuk Sekarang</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <!-- Quick Demo Switcher -->
        <div class="mt-6 pt-5 border-t border-stone-100">
            <p class="text-[11px] font-semibold text-stone-500 uppercase tracking-wider text-center mb-3">Pilihan Akun Demo (Password: password)</p>
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                <button type="button" onclick="setAccount('owner@bakeryerp.local')" class="p-2 border border-stone-200 rounded-lg hover:border-amber-500 text-left transition bg-stone-50/50">
                    <span class="font-bold text-stone-800 block">Owner (Pusat)</span>
                    <span class="text-stone-500 text-[10px]">owner@bakeryerp.local</span>
                </button>
                <button type="button" onclick="setAccount('kasir.cbg1@bakeryerp.local')" class="p-2 border border-stone-200 rounded-lg hover:border-amber-500 text-left transition bg-stone-50/50">
                    <span class="font-bold text-stone-800 block">Kasir Menteng</span>
                    <span class="text-stone-500 text-[10px]">kasir.cbg1@...</span>
                </button>
                <button type="button" onclick="setAccount('produksi.pusat@bakeryerp.local')" class="p-2 border border-stone-200 rounded-lg hover:border-amber-500 text-left transition bg-stone-50/50">
                    <span class="font-bold text-stone-800 block">Staf Produksi</span>
                    <span class="text-stone-500 text-[10px]">produksi.pusat@...</span>
                </button>
                <button type="button" onclick="setAccount('manager.cbg1@bakeryerp.local')" class="p-2 border border-stone-200 rounded-lg hover:border-amber-500 text-left transition bg-stone-50/50">
                    <span class="font-bold text-stone-800 block">Manajer Cabang</span>
                    <span class="text-stone-500 text-[10px]">manager.cbg1@...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    function setAccount(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
</script>
</body>
</html>
