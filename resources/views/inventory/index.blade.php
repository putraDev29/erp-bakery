@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Persediaan & Saldo Stok Cabang</h1>
            <p class="text-xs text-stone-500 mt-1">Saldo fisik terkini di lokasi cabang aktif beserta nilai rata-rata biaya perolehan (Moving Average).</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('inventory.ledger') }}" class="bg-stone-100 hover:bg-stone-200 text-stone-800 px-3.5 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>Buka Kartu Stok</span>
            </a>
            <a href="{{ route('transfers.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1">
                <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                <span>Transfer Antarcabang</span>
            </a>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center space-x-2 text-xs">
        <a href="{{ route('inventory.index', ['type' => 'all']) }}" class="px-3.5 py-1.5 rounded-lg font-bold transition {{ $selectedType === 'all' ? 'bg-amber-600 text-white' : 'bg-white text-stone-700 border border-stone-200' }}">
            Semua Item
        </a>
        <a href="{{ route('inventory.index', ['type' => 'ingredient']) }}" class="px-3.5 py-1.5 rounded-lg font-bold transition {{ $selectedType === 'ingredient' ? 'bg-amber-600 text-white' : 'bg-white text-stone-700 border border-stone-200' }}">
            Hanya Bahan Baku
        </a>
        <a href="{{ route('inventory.index', ['type' => 'product']) }}" class="px-3.5 py-1.5 rounded-lg font-bold transition {{ $selectedType === 'product' ? 'bg-amber-600 text-white' : 'bg-white text-stone-700 border border-stone-200' }}">
            Hanya Produk Jadi
        </a>
    </div>

    <!-- Stock Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Tipe</th>
                        <th class="py-3 px-4 font-semibold">Kode SKU</th>
                        <th class="py-3 px-4 font-semibold">Nama Item</th>
                        <th class="py-3 px-4 font-semibold text-right">Kuantitas Fisik</th>
                        <th class="py-3 px-4 font-semibold text-right">Biaya Rata-Rata (Moving Avg)</th>
                        <th class="py-3 px-4 font-semibold text-right">Total Nilai Persediaan</th>
                        <th class="py-3 px-4 font-semibold text-center">Status Stok</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($balances as $bal)
                        @php
                            $item = $bal->item;
                            $minStock = $item ? (float) $item->min_stock : 0;
                            $isLow = $minStock > 0 && (float) $bal->quantity <= $minStock;
                            $totalValuation = (float) $bal->quantity * (float) $bal->avg_cost;
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $bal->item_type === 'product' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $bal->item_type === 'product' ? 'PRODUK JADI' : 'BAHAN BAKU' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-stone-500 font-semibold">{{ $item->code ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-bold text-stone-900">{{ $item->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-stone-900 text-sm">
                                {{ number_format($bal->quantity, 2) }} <span class="text-xs font-normal text-stone-500">{{ $item->unit->symbol ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-700">
                                Rp {{ number_format($bal->avg_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-bold text-emerald-800">
                                Rp {{ number_format($totalValuation, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($isLow)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 animate-pulse">MENIPIS</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">AMAN</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('inventory.ledger', ['item_type' => $bal->item_type, 'item_id' => $bal->item_id]) }}" class="text-amber-700 hover:text-amber-900 font-semibold inline-flex items-center space-x-1">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                    <span>Kartu Stok</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400">Belum ada saldo stok di cabang ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $balances->links() }}
        </div>
    </div>

</div>
@endsection
