@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Master Formula Resep & Bill of Materials (BOM)</h1>
            <p class="text-xs text-stone-500 mt-1">Standarisasi komposisi bahan, yield target, dan estimasi biaya per batch.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($recipes as $rec)
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-4 hover:border-amber-400 transition">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 font-mono">
                            VERSI {{ $rec->version }}
                        </span>
                        <h2 class="text-base font-bold text-stone-900 mt-1.5">{{ $rec->name }}</h2>
                        <p class="text-xs text-stone-500">Produk: <strong class="text-stone-800">{{ $rec->product->name }}</strong></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-stone-500 block">Yield Hasil:</span>
                        <span class="text-lg font-extrabold text-stone-900">{{ number_format($rec->yield_quantity, 0) }} {{ $rec->yieldUnit->symbol }}</span>
                    </div>
                </div>

                <!-- Items breakdown -->
                <div class="border-t border-b border-stone-100 py-3 space-y-1.5">
                    <p class="text-[11px] font-semibold text-stone-600 uppercase tracking-wider mb-2">Komposisi Standar ({{ $rec->items->count() }} Bahan):</p>
                    @foreach($rec->items->take(5) as $item)
                        <div class="flex justify-between text-xs text-stone-700">
                            <span>• {{ $item->ingredient->name }}</span>
                            <span class="font-semibold">{{ number_format($item->quantity, 2) }} {{ $item->unit->symbol }}</span>
                        </div>
                    @endforeach
                    @if($rec->items->count() > 5)
                        <p class="text-[10px] text-amber-700 font-semibold pt-1">+ {{ $rec->items->count() - 5 }} bahan lainnya...</p>
                    @endif
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <div>
                        <span class="text-stone-500 text-[11px] block">Estimasi Biaya Standar:</span>
                        <span class="font-extrabold text-stone-900 text-sm">Rp {{ number_format($rec->standard_cost, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('production.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-1.5 rounded-xl font-bold text-xs shadow-sm transition inline-flex items-center space-x-1">
                        <i data-lucide="play" class="w-3.5 h-3.5"></i>
                        <span>Gunakan di Produksi</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
