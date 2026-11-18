@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Master Data Produk Jadi Bakery</h1>
            <p class="text-xs text-stone-500 mt-1">Daftar produk jadi roti, kue, dan pastry yang dijual di gerai kasir POS.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">SKU / Barcode</th>
                        <th class="py-3 px-4 font-semibold">Nama Produk</th>
                        <th class="py-3 px-4 font-semibold">Kategori</th>
                        <th class="py-3 px-4 font-semibold text-center">Satuan Jual</th>
                        <th class="py-3 px-4 font-semibold text-right">Harga Jual (POS)</th>
                        <th class="py-3 px-4 font-semibold text-right">Min. Stok Toko</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($products as $prod)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-stone-900">
                                {{ $prod->code }}
                                @if($prod->barcode)
                                    <span class="block text-[10px] text-stone-400 font-normal">{{ $prod->barcode }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">{{ $prod->name }}</td>
                            <td class="py-3.5 px-4 text-stone-600">{{ $prod->category->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-stone-700">{{ $prod->unit->symbol }}</td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-amber-700 text-sm">
                                Rp {{ number_format($prod->sale_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-medium text-stone-600">{{ number_format($prod->min_stock, 0) }} {{ $prod->unit->symbol }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">AKTIF</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
