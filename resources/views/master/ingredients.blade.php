@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Master Data Bahan Baku (Ingredients)</h1>
            <p class="text-xs text-stone-500 mt-1">Daftar bahan baku pembuatan roti & kue beserta satuan dasar dan estimasi biaya perolehan.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Kode Bahan</th>
                        <th class="py-3 px-4 font-semibold">Nama Bahan Baku</th>
                        <th class="py-3 px-4 font-semibold">Kategori</th>
                        <th class="py-3 px-4 font-semibold text-center">Satuan Dasar</th>
                        <th class="py-3 px-4 font-semibold text-right">Batas Min. Stok</th>
                        <th class="py-3 px-4 font-semibold text-right">Biaya Standar / Acuan</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($ingredients as $ing)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-stone-900">{{ $ing->code }}</td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">{{ $ing->name }}</td>
                            <td class="py-3.5 px-4 text-stone-600">{{ $ing->category->name ?? '-' }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-stone-700">{{ $ing->unit->symbol }} ({{ $ing->unit->name }})</td>
                            <td class="py-3.5 px-4 text-right font-medium text-stone-700">{{ number_format($ing->min_stock, 1) }} {{ $ing->unit->symbol }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-900">Rp {{ number_format($ing->cost_price, 0, ',', '.') }} / {{ $ing->unit->symbol }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">AKTIF</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $ingredients->links() }}
        </div>
    </div>

</div>
@endsection
