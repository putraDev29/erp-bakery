@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Kartu Stok & Mutasi Inventaris (Stock Ledger)</h1>
            <p class="text-xs text-stone-500 mt-1">Audit trail lengkap setiap barang masuk, keluar, konversi produksi, penjualan POS, dan transfer.</p>
        </div>
        <a href="{{ route('inventory.index') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Saldo Stok</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm">
        <form method="GET" action="{{ route('inventory.ledger') }}" class="flex flex-wrap items-center gap-3 text-xs">
            <div>
                <label class="block text-[11px] font-semibold text-stone-600 mb-1">Tipe Item</label>
                <select name="item_type" class="border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 font-medium">
                    <option value="ingredient" {{ $selectedItemType === 'ingredient' ? 'selected' : '' }}>Bahan Baku</option>
                    <option value="product" {{ $selectedItemType === 'product' ? 'selected' : '' }}>Produk Jadi</option>
                </select>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-[11px] font-semibold text-stone-600 mb-1">Pilih Item Spesifik</label>
                <select name="item_id" class="w-full border border-stone-300 rounded-lg px-2.5 py-1.5 bg-stone-50 font-medium">
                    <option value="">-- Tampilkan Semua Item --</option>
                    @if($selectedItemType === 'ingredient')
                        @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}" {{ $selectedItemId == $ing->id ? 'selected' : '' }}>
                                {{ $ing->name }} ({{ $ing->code }})
                            </option>
                        @endforeach
                    @else
                        @foreach($products as $prd)
                            <option value="{{ $prd->id }}" {{ $selectedItemId == $prd->id ? 'selected' : '' }}>
                                {{ $prd->name }} ({{ $prd->code }})
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="self-end">
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition">
                    Filter Kartu Stok
                </button>
            </div>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Tanggal & Waktu</th>
                        <th class="py-3 px-4 font-semibold">Item</th>
                        <th class="py-3 px-4 font-semibold">Jenis Mutasi</th>
                        <th class="py-3 px-4 font-semibold text-right text-emerald-700">Masuk (In)</th>
                        <th class="py-3 px-4 font-semibold text-right text-rose-700">Keluar (Out)</th>
                        <th class="py-3 px-4 font-semibold text-right font-mono">Biaya Satuan</th>
                        <th class="py-3 px-4 font-semibold text-right font-bold">Saldo Akhir</th>
                        <th class="py-3 px-4 font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($history as $mov)
                        @php
                            $item = $mov->item_type === 'ingredient' ? $mov->ingredient : $mov->product;
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3 px-4 text-stone-600 font-mono">{{ date('d/m/Y H:i', strtotime($mov->created_at)) }}</td>
                            <td class="py-3 px-4 font-bold text-stone-800">{{ $item->name ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-100 text-stone-800">
                                    {{ strtoupper($mov->movement_type) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-700">
                                {{ $mov->quantity_in > 0 ? '+' . number_format($mov->quantity_in, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-rose-700">
                                {{ $mov->quantity_out > 0 ? '-' . number_format($mov->quantity_out, 2) : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-stone-700">
                                Rp {{ number_format($mov->unit_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-stone-900 text-sm">
                                {{ number_format($mov->balance_after, 2) }} <span class="text-xs font-normal text-stone-500">{{ $item->unit->symbol ?? '' }}</span>
                            </td>
                            <td class="py-3 px-4 text-stone-600 text-[11px]">{{ $mov->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400">Belum ada catatan mutasi kartu stok untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $history->links() }}
        </div>
    </div>

</div>
@endsection
