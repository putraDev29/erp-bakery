@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- PAGE HEADER & FILTER BAR -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200/90 shadow-sm">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-stone-900 tracking-tight">Dashboard Ringkasan Operasional & Keuangan</h1>
            <p class="text-xs text-stone-500 mt-1">
                Data real-time berdasarkan transaksi aktual, jurnal akuntansi, dan mutasi stok cabang.
            </p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->hasRole(['owner', 'admin_pusat']))
                <select name="branch_id" class="text-xs border border-stone-300 rounded-lg px-2.5 py-2 bg-stone-50 text-stone-800 font-medium focus:ring-amber-500">
                    <option value="all" {{ is_null($selectedBranchId) ? 'selected' : '' }}>🏢 Konsolidasi (Semua Cabang)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>
                            📍 {{ $b->code }} — {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 text-stone-800 font-medium">
            <span class="text-xs text-stone-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 text-stone-800 font-medium">

            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition inline-flex items-center space-x-1">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Terapkan</span>
            </button>
        </form>
    </div>

    <!-- STATS CARDS GRID -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card: Omzet Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/90 shadow-sm relative overflow-hidden group hover:border-amber-400 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Omzet Bulan Ini</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-stone-900 tracking-tight">
                Rp {{ number_format($stats['omzet_month'], 0, ',', '.') }}
            </div>
            <div class="mt-2 flex items-center justify-between text-xs text-stone-500">
                <span>Hari ini: <strong class="text-stone-700">Rp {{ number_format($stats['omzet_today'], 0, ',', '.') }}</strong></span>
                <span>{{ $stats['sales_count_month'] }} transaksi</span>
            </div>
        </div>

        <!-- Card: Laba Kotor -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/90 shadow-sm relative overflow-hidden group hover:border-emerald-400 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Laba Kotor (Gross)</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="pie-chart" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-emerald-700 tracking-tight">
                Rp {{ number_format($stats['gross_profit'], 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-stone-500">
                HPP Terjual: <strong class="text-stone-700">Rp {{ number_format($stats['cogs_month'], 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Card: Laba Bersih -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/90 shadow-sm relative overflow-hidden group hover:border-blue-400 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Laba Bersih (Net)</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                    <i data-lucide="badge-dollar-sign" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold {{ $stats['net_profit'] >= 0 ? 'text-blue-700' : 'text-rose-600' }} tracking-tight">
                Rp {{ number_format($stats['net_profit'], 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-stone-500">
                Beban Operasional: <strong class="text-stone-700">Rp {{ number_format($stats['expenses_month'], 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Card: Saldo Kas & Bank -->
        <div class="bg-white rounded-2xl p-5 border border-stone-200/90 shadow-sm relative overflow-hidden group hover:border-purple-400 transition">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Saldo Kas & Bank</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center">
                    <i data-lucide="vault" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-stone-900 tracking-tight">
                Rp {{ number_format($stats['cash_and_bank'], 0, ',', '.') }}
            </div>
            <div class="mt-2 text-xs text-stone-500">
                Total di seluruh akun aktif
            </div>
        </div>

    </div>

    <!-- SECOND STATS ROW (Inventory, AP, AR) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Nilai Persediaan -->
        <div class="bg-amber-50/60 border border-amber-200/70 rounded-2xl p-4 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i data-lucide="package-search" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-amber-800 font-semibold uppercase tracking-wider">Nilai Persediaan (Moving Avg)</p>
                <p class="text-xl font-bold text-stone-900">Rp {{ number_format($stats['inventory_valuation'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-stone-500">Bahan baku & produk jadi siap jual</p>
            </div>
        </div>

        <!-- Hutang Supplier (AP) -->
        <div class="bg-rose-50/60 border border-rose-200/70 rounded-2xl p-4 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i data-lucide="clock-alert" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-rose-800 font-semibold uppercase tracking-wider">Hutang Usaha Supplier (AP)</p>
                <p class="text-xl font-bold text-stone-900">Rp {{ number_format($stats['accounts_payable'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-stone-500">Sisa kewajiban tempo pembelian</p>
            </div>
        </div>

        <!-- Piutang Pelanggan (AR) -->
        <div class="bg-emerald-50/60 border border-emerald-200/70 rounded-2xl p-4 flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i data-lucide="hand-coins" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-emerald-800 font-semibold uppercase tracking-wider">Piutang Pelanggan (AR)</p>
                <p class="text-xl font-bold text-stone-900">Rp {{ number_format($stats['accounts_receivable'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-stone-500">Tagihan kredit penjualan kasir</p>
            </div>
        </div>
    </div>

    <!-- MAIN DASHBOARD CONTENT (Charts & Tables) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Grafik Penjualan Harian & Top Produk -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Chart Box -->
            <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-base font-bold text-stone-900">Grafik Performa Penjualan & Margin</h2>
                        <p class="text-xs text-stone-500">Tren penjualan harian vs HPP berdasarkan data aktual database</p>
                    </div>
                    <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-full border border-amber-200">14 Hari Terakhir</span>
                </div>

                <!-- CSS Visual Bar Chart -->
                <div class="h-64 flex items-end gap-2 pt-6 pb-2 border-b border-stone-100">
                    @forelse($stats['daily_sales'] as $day)
                        @php
                            $maxVal = max(100000, $stats['daily_sales']->max('total_revenue'));
                            $heightPct = min(100, round(($day->total_revenue / $maxVal) * 100));
                        @endphp
                        <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                            <!-- Tooltip -->
                            <div class="absolute -top-12 hidden group-hover:flex flex-col items-center bg-stone-900 text-white text-[10px] rounded-lg py-1 px-2 z-20 shadow-md whitespace-nowrap">
                                <span>{{ $day->sale_date }}</span>
                                <span class="font-bold text-amber-300">Rp {{ number_format($day->total_revenue, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-amber-500/80 group-hover:bg-amber-600 rounded-t transition-all duration-300" style="height: {{ max(4, $heightPct) }}%"></div>
                            <span class="text-[9px] text-stone-400 mt-2 truncate w-full text-center">{{ date('d/m', strtotime($day->sale_date)) }}</span>
                        </div>
                    @empty
                        <div class="w-full h-full flex flex-col items-center justify-center text-stone-400 text-xs">
                            <i data-lucide="bar-chart-2" class="w-8 h-8 mb-2 stroke-1"></i>
                            <span>Belum ada transaksi penjualan dalam 14 hari terakhir. Mulai transaksi di POS!</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Top Products Table -->
            <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-stone-900">Produk Paling Laris (Top 5)</h2>
                    <a href="{{ route('sales.index') }}" class="text-xs font-semibold text-amber-700 hover:underline">Semua Penjualan &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-stone-200 text-stone-500">
                                <th class="pb-3 font-semibold">Nama Produk</th>
                                <th class="pb-3 font-semibold text-right">Kuantitas Terjual</th>
                                <th class="pb-3 font-semibold text-right">Total Nilai Penjualan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @forelse($stats['top_products'] as $prod)
                                <tr>
                                    <td class="py-3 font-semibold text-stone-800">{{ $prod->name }}</td>
                                    <td class="py-3 text-right font-medium text-stone-600">{{ number_format($prod->total_qty, 0) }} pcs</td>
                                    <td class="py-3 text-right font-bold text-stone-900">Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-stone-400">Belum ada data produk terjual pada periode ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Col: Low Stock Alerts & Quick Actions -->
        <div class="space-y-6">

            <!-- Quick Action Buttons -->
            <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-sm">
                <h2 class="text-base font-bold text-stone-900 mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <a href="{{ route('pos.index') }}" class="p-3.5 bg-amber-50 hover:bg-amber-100/80 border border-amber-200/80 rounded-xl text-center font-bold text-amber-900 transition flex flex-col items-center justify-center space-y-1.5 shadow-sm">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-amber-700"></i>
                        <span>Kasir (POS)</span>
                    </a>
                    <a href="{{ route('production.create') }}" class="p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-xl text-center font-bold text-stone-800 transition flex flex-col items-center justify-center space-y-1.5">
                        <i data-lucide="chef-hat" class="w-5 h-5 text-stone-700"></i>
                        <span>Order Produksi</span>
                    </a>
                    <a href="{{ route('purchases.create') }}" class="p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-xl text-center font-bold text-stone-800 transition flex flex-col items-center justify-center space-y-1.5">
                        <i data-lucide="truck" class="w-5 h-5 text-stone-700"></i>
                        <span>Beli Bahan</span>
                    </a>
                    <a href="{{ route('transfers.create') }}" class="p-3.5 bg-stone-50 hover:bg-stone-100 border border-stone-200 rounded-xl text-center font-bold text-stone-800 transition flex flex-col items-center justify-center space-y-1.5">
                        <i data-lucide="arrow-left-right" class="w-5 h-5 text-stone-700"></i>
                        <span>Transfer Stok</span>
                    </a>
                </div>
            </div>

            <!-- Low Stock Warnings -->
            <div class="bg-white p-6 rounded-2xl border border-stone-200/90 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                        <h2 class="text-base font-bold text-stone-900">Peringatan Stok Menipis</h2>
                    </div>
                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">Perhatian</span>
                </div>

                <div class="space-y-3">
                    @forelse($stats['low_stocks'] as $ls)
                        <div class="p-3 rounded-xl border border-rose-100 bg-rose-50/40 flex items-center justify-between text-xs">
                            <div>
                                <p class="font-bold text-stone-800">{{ $ls->item->name ?? 'Item' }}</p>
                                <p class="text-[11px] text-stone-500">
                                    Cabang: {{ $ls->branch->code }} • Batas Min: {{ number_format($ls->item->min_stock ?? 0, 1) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-rose-700 text-sm">{{ number_format($ls->quantity, 1) }}</span>
                                <span class="text-[10px] text-stone-500 block">{{ $ls->item->unit->symbol ?? '' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-stone-400 text-xs">
                            <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-500 mx-auto mb-2 stroke-1"></i>
                            <span>Semua stok berada di atas batas minimum.</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
