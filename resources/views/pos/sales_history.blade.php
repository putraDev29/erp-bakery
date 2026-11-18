@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Riwayat Transaksi Penjualan</h1>
            <p class="text-xs text-stone-500 mt-1">Daftar seluruh penjualan POS kasir dan invoice tempo.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1.5 self-start">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Transaksi POS Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">No. Invoice</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Cabang</th>
                        <th class="py-3 px-4 font-semibold">Pelanggan</th>
                        <th class="py-3 px-4 font-semibold">Metode Bayar</th>
                        <th class="py-3 px-4 font-semibold text-right">Total Transaksi</th>
                        <th class="py-3 px-4 font-semibold text-right">Laba Kotor</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-stone-900">{{ $sale->invoice_number }}</td>
                            <td class="py-3.5 px-4 text-stone-600">{{ date('d/m/Y H:i', strtotime($sale->created_at)) }}</td>
                            <td class="py-3.5 px-4 font-medium text-stone-700">{{ $sale->branch->code }}</td>
                            <td class="py-3.5 px-4 text-stone-800">{{ $sale->customer->name ?? 'Pelanggan Umum' }}</td>
                            <td class="py-3.5 px-4 capitalize font-medium text-stone-700">{{ $sale->payment_method }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-900">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-700">Rp {{ number_format($sale->gross_profit, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sale->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ strtoupper($sale->payment_status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('pos.receipt', $sale->id) }}" class="inline-flex items-center space-x-1 text-amber-700 hover:text-amber-900 font-semibold">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    <span>Nota</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-stone-400">Belum ada transaksi penjualan yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $sales->links() }}
        </div>
    </div>

</div>
@endsection
