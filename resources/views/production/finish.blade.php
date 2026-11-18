@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Penyelesaian & Posting Hasil Produksi</h1>
            <p class="text-xs text-stone-500 mt-1">Konfirmasi pemakaian bahan aktual, hasil produk jadi layak jual, dan pengakuan HPP batch.</p>
        </div>
        <span class="font-mono text-xs font-bold text-stone-700 bg-stone-100 px-3 py-1.5 rounded-lg border border-stone-200">
            {{ $order->order_number }}
        </span>
    </div>

    <form action="{{ route('production.finish.post', $order->id) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Order Summary Card -->
        <div class="bg-amber-50/60 p-5 rounded-2xl border border-amber-200/80 flex flex-wrap items-center justify-between gap-4 text-xs">
            <div>
                <span class="text-stone-500 block">Produk Hasil:</span>
                <span class="font-bold text-base text-stone-900">{{ $order->product->name }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Target Rencana:</span>
                <span class="font-bold text-sm text-stone-800">{{ number_format($order->target_quantity, 0) }} {{ $order->product->unit->symbol }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Batch Number:</span>
                <span class="font-mono font-bold text-amber-900">{{ $order->batch_number }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Lokasi Fasilitas:</span>
                <span class="font-bold text-stone-800">{{ $order->branch->name }}</span>
            </div>
        </div>

        <!-- Actual Output Quantities Card -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">1. Hasil Output Produksi</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Jumlah Produk Layak Jual (Good)</label>
                    <input type="number" step="1" min="1" name="actual_good_quantity" value="{{ (int) $order->target_quantity }}" required
                           class="w-full text-sm font-bold border border-emerald-300 bg-emerald-50/40 text-emerald-900 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none">
                    <span class="text-[10px] text-stone-500 mt-1 block">Akan langsung menambah stok siap jual di POS.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Produk Rusak / Gagal (Waste)</label>
                    <input type="number" step="1" min="0" name="actual_waste_quantity" value="0"
                           class="w-full text-sm font-bold border border-stone-300 bg-stone-50 text-stone-800 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-amber-500 outline-none">
                    <span class="text-[10px] text-stone-500 mt-1 block">Dicatat dalam histori evaluasi loss.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Tanggal Kedaluwarsa Batch</label>
                    <input type="date" name="expiry_date" value="{{ date('Y-m-d', strtotime('+4 days')) }}"
                           class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50">
                </div>
            </div>
        </div>

        <!-- Actual Consumed Materials Card -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">2. Pemakaian Bahan Baku Aktual (Dipotong dari Stok Gudang)</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                        <tr>
                            <th class="py-2.5 px-3 font-semibold">Nama Bahan Baku</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Rencana Standar</th>
                            <th class="py-2.5 px-3 font-semibold text-right w-44">Pemakaian Aktual</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($order->materials as $idx => $mat)
                            <tr>
                                <td class="py-3 px-3 font-bold text-stone-800">
                                    {{ $mat->ingredient->name }}
                                    <input type="hidden" name="materials[{{ $idx }}][ingredient_id]" value="{{ $mat->ingredient_id }}">
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-stone-600">
                                    {{ number_format($mat->planned_quantity, 2) }} {{ $mat->unit->symbol }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <input type="number" step="0.001" min="0" name="materials[{{ $idx }}][actual_quantity]" value="{{ $mat->planned_quantity }}" required
                                               class="w-28 text-right font-bold text-xs border border-stone-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-amber-500">
                                        <span class="text-stone-500 text-[11px]">{{ $mat->unit->symbol }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Additional Costs (Labor & Overhead) -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">3. Alokasi Biaya Tenaga Kerja & Overhead (Opsional)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Biaya Tenaga Kerja Langsung (Rp)</label>
                    <input type="number" step="1000" min="0" name="labor_cost" value="{{ (int) $order->labor_cost }}"
                           class="w-full text-xs font-bold border border-stone-300 rounded-xl px-3 py-2 bg-stone-50">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Biaya Overhead Pabrik / Listrik (Rp)</label>
                    <input type="number" step="1000" min="0" name="overhead_cost" value="{{ (int) $order->overhead_cost }}"
                           class="w-full text-xs font-bold border border-stone-300 rounded-xl px-3 py-2 bg-stone-50">
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('production.index') }}" class="px-5 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-100">Batal</a>
            <button type="submit" onclick="return confirm('Pastikan data sudah benar. Sistem akan memotong stok bahan, menambah stok produk jadi, dan memposting jurnal otomatis.')"
                    class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white rounded-xl text-xs font-bold shadow-md transition">
                Posting Selesai & Perbarui Stok
            </button>
        </div>

    </form>

</div>
@endsection
