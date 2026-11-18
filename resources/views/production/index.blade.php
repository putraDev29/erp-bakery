@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Perintah Produksi & Batch Bakery</h1>
            <p class="text-xs text-stone-500 mt-1">Kelola siklus pembuatan kue, scaling bahan baku, dan pengakuan HPP produk jadi.</p>
        </div>
        <a href="{{ route('production.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1.5 self-start">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Order Produksi Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">No. Order / Batch</th>
                        <th class="py-3 px-4 font-semibold">Produk Jadi</th>
                        <th class="py-3 px-4 font-semibold">Cabang</th>
                        <th class="py-3 px-4 font-semibold text-right">Target Kuantitas</th>
                        <th class="py-3 px-4 font-semibold text-right">Hasil Layak Jual</th>
                        <th class="py-3 px-4 font-semibold text-right">Biaya HPP / Unit</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-stone-900 block">{{ $ord->order_number }}</span>
                                <span class="text-[10px] text-stone-500 font-mono">{{ $ord->batch_number }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">{{ $ord->product->name }}</td>
                            <td class="py-3.5 px-4 font-medium text-stone-600">{{ $ord->branch->code }}</td>
                            <td class="py-3.5 px-4 text-right font-medium text-stone-700">
                                {{ number_format($ord->target_quantity, 0) }} {{ $ord->product->unit->symbol }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-900">
                                {{ $ord->status === 'posted' ? number_format($ord->actual_good_quantity, 0) . ' ' . $ord->product->unit->symbol : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-700">
                                {{ $ord->unit_cost > 0 ? 'Rp ' . number_format($ord->unit_cost, 0, ',', '.') : 'Estimasi: Rp ' . number_format($ord->total_cost / max(1, $ord->target_quantity), 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($ord->status === 'posted')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">SELESAI (POSTED)</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">TERENCANA</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($ord->status !== 'posted')
                                    <a href="{{ route('production.finish.form', $ord->id) }}" class="bg-amber-600 hover:bg-amber-700 text-white px-2.5 py-1 rounded-lg font-bold text-[11px] shadow-sm transition inline-flex items-center space-x-1">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Proses & Selesaikan</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-stone-400 font-medium">Telah Diposting</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400">Belum ada order produksi yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
