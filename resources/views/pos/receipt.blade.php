@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto space-y-4">

    <!-- Action Bar -->
    <div class="no-print bg-white p-4 rounded-2xl border border-stone-200/90 shadow-sm flex items-center justify-between">
        <a href="{{ route('pos.index') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-stone-600 hover:text-stone-900">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Kasir POS</span>
        </a>

        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Nota Sekarang</span>
            </button>
        </div>
    </div>

    <!-- NOTA THERMAL CONTAINER -->
    <div id="receipt-paper" class="bg-white p-6 sm:p-8 rounded-2xl border border-stone-300 shadow-sm font-mono text-xs max-w-sm mx-auto text-stone-900">

        <!-- Store Header -->
        <div class="text-center pb-4 border-b border-dashed border-stone-300 space-y-1">
            <h2 class="font-bold text-base uppercase tracking-tight">{{ $sale->branch->company->name ?? 'PT Roti & Kue Nusantara' }}</h2>
            <p class="font-bold text-xs uppercase">{{ $sale->branch->name }}</p>
            <p class="text-[11px] text-stone-500">{{ $sale->branch->address }}</p>
            <p class="text-[11px] text-stone-500">Telp: {{ $sale->branch->phone }}</p>
        </div>

        <!-- Meta Info -->
        <div class="py-3 border-b border-dashed border-stone-300 text-[11px] space-y-0.5">
            <div class="flex justify-between">
                <span>No. Nota:</span>
                <span class="font-bold">{{ $sale->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal:</span>
                <span>{{ date('d/m/Y H:i', strtotime($sale->created_at)) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kasir:</span>
                <span>{{ $sale->cashier->name ?? 'Kasir' }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pelanggan:</span>
                <span>{{ $sale->customer->name ?? 'Umum (Walk-in)' }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="py-3 border-b border-dashed border-stone-300 space-y-2">
            @foreach($sale->items as $item)
                <div>
                    <div class="font-bold text-xs">{{ $item->product->name }}</div>
                    <div class="flex justify-between text-[11px] text-stone-600">
                        <span>{{ number_format($item->quantity, 0) }} {{ $item->product->unit->symbol }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                        <span class="font-bold text-stone-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Totals & Payment -->
        <div class="py-3 border-b border-dashed border-stone-300 space-y-1 text-xs">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>Rp {{ number_format($sale->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($sale->discount_amount > 0)
                <div class="flex justify-between text-rose-600">
                    <span>Diskon:</span>
                    <span>- Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</span>
                </div>
            @endif
            <div class="flex justify-between font-bold text-sm pt-1 border-t border-stone-200">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between pt-1">
                <span class="capitalize">Bayar ({{ $sale->payment_method }}):</span>
                <span>Rp {{ number_format($sale->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if($sale->payment_method === 'cash')
                <div class="flex justify-between font-semibold">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($sale->change_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="pt-4 text-center text-[10px] text-stone-500 space-y-1">
            <p class="font-semibold text-stone-700">Terima kasih atas kunjungan Anda!</p>
            <p>Kue dan roti segar dipanggang dengan bahan berkualitas terbaik.</p>
            <p class="pt-2 text-[9px] text-stone-400">--- Powered by Bakery ERP Pro ---</p>
        </div>

    </div>

</div>

<style>
@media print {
    body {
        background: white !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #receipt-paper {
        border: none !important;
        box-shadow: none !important;
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
    }
}
</style>
@endsection
