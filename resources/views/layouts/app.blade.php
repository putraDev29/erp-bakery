<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'Bakery ERP Pro') }}</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN for instant robust styling & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        bakery: {
                            50: '#fdfbf7',
                            100: '#f7f2ea',
                            200: '#eee3d2',
                            300: '#e1cdb2',
                            400: '#cdab83',
                            500: '#b8895b',
                            600: '#9e6d45',
                            700: '#7e5337',
                            800: '#674430',
                            900: '#543929',
                        },
                        amberbrand: {
                            500: '#d97706',
                            600: '#b45309',
                            700: '#92400e',
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fdfbf7;
        }
        h1, h2, h3, h4, .font-heading {
            font-family: 'Outfit', sans-serif;
        }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800 antialiased selection:bg-amber-100 selection:text-amber-900" x-data="{ mobileMenuOpen: false }">

<div class="min-h-screen flex flex-col md:flex-row">

    <!-- SIDEBAR -->
    <aside class="no-print fixed inset-y-0 left-0 z-50 w-64 bg-stone-900 text-stone-200 flex flex-col transition-transform duration-300 transform md:translate-x-0"
           :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">

        <!-- Logo & Brand Header -->
        <div class="h-16 flex items-center justify-between px-6 border-b border-stone-800 bg-stone-950/40">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-600 to-amber-400 flex items-center justify-center text-white shadow-md shadow-amber-600/20">
                    <i data-lucide="croissant" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="font-heading font-bold text-lg text-white tracking-tight">Bakery ERP</span>
                    <span class="block text-[10px] text-amber-400/90 font-medium tracking-wider uppercase">Multi-Branch Pro</span>
                </div>
            </a>
            <button @click="mobileMenuOpen = false" class="md:hidden text-stone-400 hover:text-white">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm font-medium">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Dashboard</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[11px] font-semibold tracking-wider text-stone-500 uppercase">Operasional Toko</div>

            <a href="{{ route('pos.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('pos.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                <span class="flex-1">Kasir & POS</span>
                <span class="px-1.5 py-0.5 text-[10px] font-bold rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">Cepat</span>
            </a>

            <a href="{{ route('sales.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('sales.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Riwayat Penjualan</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[11px] font-semibold tracking-wider text-stone-500 uppercase">Manufaktur & Gudang</div>

            <a href="{{ route('production.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('production.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="chef-hat" class="w-4 h-4"></i>
                <span>Produksi & Batch</span>
            </a>

            <a href="{{ route('recipes.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('recipes.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span>Resep & BOM</span>
            </a>

            <a href="{{ route('inventory.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('inventory.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="boxes" class="w-4 h-4"></i>
                <span>Stok & Kartu Stok</span>
            </a>

            <a href="{{ route('transfers.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('transfers.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                <span>Transfer Antarcabang</span>
            </a>

            <a href="{{ route('purchases.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('purchases.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="truck" class="w-4 h-4"></i>
                <span>Pembelian Bahan</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[11px] font-semibold tracking-wider text-stone-500 uppercase">Finansial & Akuntansi</div>

            <a href="{{ route('finance.cash') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('finance.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                <span>Kas, Bank & Biaya</span>
            </a>

            <a href="{{ route('accounting.journals') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('accounting.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="scale" class="w-4 h-4"></i>
                <span>Buku Jurnal (Double-Entry)</span>
            </a>

            <a href="{{ route('reports.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="line-chart" class="w-4 h-4"></i>
                <span>Laporan & Konsolidasi</span>
            </a>

            <div class="pt-3 pb-1 px-3 text-[11px] font-semibold tracking-wider text-stone-500 uppercase">Data Master</div>

            <a href="{{ route('master.ingredients') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('master.ingredients*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="wheat" class="w-4 h-4"></i>
                <span>Bahan Baku</span>
            </a>

            <a href="{{ route('master.products') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('master.products*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="cake" class="w-4 h-4"></i>
                <span>Produk Jadi</span>
            </a>

            <a href="{{ route('branches.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('branches.*') ? 'bg-amber-600 text-white font-semibold' : 'text-stone-300 hover:bg-stone-800 hover:text-white' }}">
                <i data-lucide="store" class="w-4 h-4"></i>
                <span>Kelola Cabang</span>
            </a>
        </nav>

        <!-- User profile preview -->
        <div class="p-4 border-t border-stone-800 bg-stone-950/60 flex items-center justify-between">
            <div class="flex items-center space-x-3 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-amber-600/30 border border-amber-500/40 text-amber-300 font-bold flex items-center justify-center text-xs flex-shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="truncate">
                    <p class="text-xs font-semibold text-white truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[10px] text-stone-400 capitalize truncate">{{ Auth::user()->roles->first()->display_name ?? 'Staf' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Keluar" class="text-stone-400 hover:text-rose-400 transition p-1">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 md:pl-64 flex flex-col min-w-0">

        <!-- TOPBAR -->
        <header class="no-print h-16 bg-white border-b border-stone-200/80 sticky top-0 z-40 px-4 md:px-8 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-4">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-stone-600 hover:text-stone-900 focus:outline-none">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>

                <div class="flex items-center space-x-2">
                    <span class="text-xs text-stone-500 font-medium hidden sm:inline">Lokasi Cabang Aktif:</span>
                    <!-- Branch Selector Form -->
                    <form action="{{ route('branch.switch') }}" method="POST" class="inline-flex items-center">
                        @csrf
                        <div class="relative">
                            <select name="branch_id" onchange="this.form.submit()"
                                    class="bg-amber-50 border border-amber-200 text-amber-900 text-xs font-semibold rounded-lg pl-3 pr-8 py-1.5 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 appearance-none cursor-pointer">
                                @php
                                    $accessibleBranches = Auth::user()->hasRole(['owner', 'admin_pusat'])
                                        ? \App\Models\Branch::where('company_id', Auth::user()->company_id)->get()
                                        : Auth::user()->branches;
                                    $activeBranchId = session('active_branch_id');
                                @endphp
                                @foreach($accessibleBranches as $branch)
                                    <option value="{{ $branch->id }}" {{ $activeBranchId == $branch->id ? 'selected' : '' }}>
                                        📍 {{ $branch->code }} — {{ $branch->name }} {{ $branch->is_central ? '(Pusat)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-amber-700 absolute right-2.5 top-2 pointer-events-none"></i>
                        </div>
                    </form>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-semibold text-stone-800">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-stone-500">{{ Auth::user()->email }}</p>
                </div>
                <div class="h-8 w-px bg-stone-200 hidden sm:block"></div>
                <a href="{{ route('pos.index') }}" class="inline-flex items-center space-x-1.5 bg-gradient-to-r from-amber-600 to-amber-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm hover:from-amber-700 hover:to-amber-800 transition">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                    <span>Buka POS</span>
                </a>
            </div>
        </header>

        <!-- FLASH NOTIFICATIONS -->
        <div class="no-print px-4 md:px-8 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm mb-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                    <p class="text-xs font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-center space-x-3 shadow-sm mb-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                    <p class="text-xs font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl flex items-start space-x-3 shadow-sm mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5"></i>
                    <ul class="text-xs list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- MAIN BODY CONTENT -->
        <main class="flex-1 px-4 md:px-8 py-6">
            {{ $slot ?? '' }}
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="no-print border-t border-stone-200/80 bg-white/70 py-4 px-4 md:px-8 text-xs text-stone-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                <span class="font-semibold text-stone-700">PT Roti & Kue Nusantara</span> — Sistem Manajemen Usaha & Akuntansi Terintegrasi
            </div>
            <div>
                Versi 1.0 • Real-time Moving Average Costing • Double-Entry Ledger
            </div>
        </footer>

    </div>
</div>

<script>
    // Initialize Lucide Icons
    document.addEventListener("DOMContentLoaded", function () {
        lucide.createIcons();
    });
</script>
@stack('scripts')
</body>
</html>
