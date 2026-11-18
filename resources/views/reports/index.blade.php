@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Laporan Keuangan & Konsolidasi Cabang</h1>
            <p class="text-xs text-stone-500 mt-1">Laba Rugi (P&L), efisiensi biaya produksi, dan evaluasi perbandingan seluruh cabang usaha.</p>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->hasRole(['owner', 'admin_pusat']))
                <select name="branch_id" class="text-xs border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 font-medium">
                    <option value="all" {{ is_null($selectedBranchId) ? 'selected' : '' }}>🏢 Seluruh Cabang (Konsolidasi)</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ $selectedBranchId == $b->id ? 'selected' : '' }}>
                            📍 {{ $b->code }} — {{ $b->name }}
                        </option>
                    @endforeach
                </select>
            @endif

            <input type="date" name="start_date" value="{{ $startDate }}" class="text-xs border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 font-medium">
            <span class="text-xs text-stone-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="text-xs border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 font-medium">

            <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition">
                Terapkan
            </button>
        </form>
    </div>

    <!-- P&L STATEMENT (LABA RUGI) -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden p-6 max-w-3xl mx-auto space-y-6">
        <div class="text-center border-b border-stone-100 pb-4">
            <h2 class="text-lg font-bold text-stone-900">Laporan Laba Rugi Komprehensif</h2>
            <p class="text-xs text-stone-500">Periode: {{ date('d F Y', strtotime($startDate)) }} s/d {{ date('d F Y', strtotime($endDate)) }}</p>
        </div>

        <div class="space-y-4 text-xs font-medium text-stone-700">

            <!-- PENDAPATAN -->
            <div class="space-y-2">
                <div class="font-bold text-stone-900 uppercase tracking-wider text-xs border-b border-stone-100 pb-1 flex justify-between">
                    <span>1. PENDAPATAN USAHA</span>
                </div>
                <div class="flex justify-between pl-4">
                    <span>Penjualan Bakery & Makanan Kotor</span>
                    <span class="font-mono">Rp {{ number_format($pnl['gross_sales'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pl-4 text-rose-600">
                    <span>Potongan Penjualan & Diskon</span>
                    <span class="font-mono">- Rp {{ number_format($pnl['discounts'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-stone-900 bg-stone-50 p-2 rounded-lg">
                    <span>PENJUALAN BERSIH (NET REVENUE)</span>
                    <span class="font-mono">Rp {{ number_format($pnl['net_sales'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- HARGA POKOK PENJUALAN -->
            <div class="space-y-2">
                <div class="font-bold text-stone-900 uppercase tracking-wider text-xs border-b border-stone-100 pb-1 flex justify-between">
                    <span>2. HARGA POKOK PENJUALAN (HPP)</span>
                </div>
                <div class="flex justify-between pl-4 text-rose-700">
                    <span>Beban Pokok Penjualan (Moving Average Biaya Produksi)</span>
                    <span class="font-mono">- Rp {{ number_format($pnl['cogs'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-extrabold text-emerald-800 bg-emerald-50/70 p-2 rounded-lg border border-emerald-100">
                    <span>LABA KOTOR (GROSS PROFIT) [Margin: {{ $pnl['gross_margin_pct'] }}%]</span>
                    <span class="font-mono text-sm">Rp {{ number_format($pnl['gross_profit'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- BEBAN OPERASIONAL -->
            <div class="space-y-2">
                <div class="font-bold text-stone-900 uppercase tracking-wider text-xs border-b border-stone-100 pb-1 flex justify-between">
                    <span>3. BEBAN OPERASIONAL TOKO</span>
                </div>
                @forelse($pnl['expenses'] as $exp)
                    <div class="flex justify-between pl-4">
                        <span>{{ $exp['category_name'] }}</span>
                        <span class="font-mono">- Rp {{ number_format($exp['total'], 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div class="pl-4 text-stone-400 italic">Belum ada beban operasional tercatat pada periode ini.</div>
                @endforelse
                <div class="flex justify-between font-bold text-stone-800 bg-stone-50 p-2 rounded-lg">
                    <span>TOTAL BEBAN OPERASIONAL</span>
                    <span class="font-mono text-rose-700">- Rp {{ number_format($pnl['total_expenses'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- LABA BERSIH -->
            <div class="pt-2">
                <div class="flex justify-between font-black text-base {{ $pnl['net_profit'] >= 0 ? 'text-blue-900 bg-blue-50 border-blue-200' : 'text-rose-900 bg-rose-50 border-rose-200' }} p-3.5 rounded-xl border">
                    <span>LABA BERSIH BERJALAN (NET PROFIT) [Margin: {{ $pnl['net_margin_pct'] }}%]</span>
                    <span class="font-mono">Rp {{ number_format($pnl['net_profit'], 0, ',', '.') }}</span>
                </div>
            </div>

        </div>
    </div>

    <!-- MULTI-BRANCH COMPARISON CONSOLIDATION -->
    @if(Auth::user()->hasRole(['owner', 'admin_pusat']))
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div>
                    <h2 class="text-base font-bold text-stone-900">Perbandingan Kinerja Antarcabang (Konsolidasi Usaha)</h2>
                    <p class="text-xs text-stone-500">Evaluasi omzet, margin kotor, laba bersih, saldo kas, dan valuasi persediaan di setiap cabang.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                        <tr>
                            <th class="py-3 px-4 font-semibold">Cabang</th>
                            <th class="py-3 px-4 font-semibold text-right">Omzet Penjualan</th>
                            <th class="py-3 px-4 font-semibold text-right">HPP Terjual</th>
                            <th class="py-3 px-4 font-semibold text-right">Laba Kotor</th>
                            <th class="py-3 px-4 font-semibold text-right">Beban Operasional</th>
                            <th class="py-3 px-4 font-semibold text-right">Laba Bersih</th>
                            <th class="py-3 px-4 font-semibold text-right">Valuasi Stok</th>
                            <th class="py-3 px-4 font-semibold text-right">Saldo Kas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($comparison as $c)
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="py-3 px-4 font-bold text-stone-900">
                                    {{ $c['branch_code'] }} — {{ $c['branch_name'] }}
                                    @if($c['is_central'])
                                        <span class="ml-1 text-[9px] bg-stone-200 text-stone-700 px-1 rounded font-bold">PUSAT</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-stone-900 font-mono">Rp {{ number_format($c['sales'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-medium text-stone-600 font-mono">Rp {{ number_format($c['cogs'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-emerald-700 font-mono">Rp {{ number_format($c['gross_profit'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-medium text-rose-700 font-mono">Rp {{ number_format($c['expenses'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-extrabold {{ $c['net_profit'] >= 0 ? 'text-blue-700' : 'text-rose-600' }} font-mono">Rp {{ number_format($c['net_profit'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-stone-700 font-mono">Rp {{ number_format($c['inventory_valuation'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-bold text-purple-700 font-mono">Rp {{ number_format($c['cash_balance'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-stone-50 font-bold border-t border-stone-200">
                        <tr>
                            <td class="py-3 px-4 uppercase text-stone-600">Total Konsolidasi:</td>
                            <td class="py-3 px-4 text-right font-mono text-stone-900">Rp {{ number_format(collect($comparison)->sum('sales'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-stone-600">Rp {{ number_format(collect($comparison)->sum('cogs'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-800">Rp {{ number_format(collect($comparison)->sum('gross_profit'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-rose-700">Rp {{ number_format(collect($comparison)->sum('expenses'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-blue-900">Rp {{ number_format(collect($comparison)->sum('net_profit'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-stone-800">Rp {{ number_format(collect($comparison)->sum('inventory_valuation'), 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono text-purple-900">Rp {{ number_format(collect($comparison)->sum('cash_balance'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
