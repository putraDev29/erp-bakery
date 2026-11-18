@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Penerimaan Fisik Barang dari Supplier</h1>
            <p class="text-xs text-stone-500 mt-1">Verifikasi jumlah barang yang benar-benar tiba di gudang cabang aktif.</p>
        </div>
        <span class="font-mono text-xs font-bold text-stone-700 bg-stone-100 px-3 py-1.5 rounded-lg border border-stone-200">
            {{ $purchase->purchase_number }}
        </span>
    </div>

    <form action="{{ route('purchases.receive.post', $purchase->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-200 text-xs flex justify-between items-center">
            <div>
                <span class="text-stone-500 block">Supplier:</span>
                <span class="font-bold text-stone-900 text-sm">{{ $purchase->supplier->name }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Cabang Penerima:</span>
                <span class="font-bold text-stone-900 text-sm">{{ $purchase->branch->name }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Tanggal Pesan:</span>
                <span class="font-mono text-stone-700">{{ date('d/m/Y', strtotime($purchase->purchase_date)) }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Item Bahan Baku Diterima</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                        <tr>
                            <th class="py-2.5 px-3 font-semibold">Nama Bahan</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Dipesan</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Sudah Diterima</th>
                            <th class="py-2.5 px-3 font-semibold text-right w-36">Diterima Sekarang</th>
                            <th class="py-2.5 px-3 font-semibold">No. Batch / Exp</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($purchase->items as $idx => $pItem)
                            @php
                                $remainingToReceive = max(0, (float) $pItem->quantity - (float) $pItem->received_qty);
                            @endphp
                            <tr>
                                <td class="py-3 px-3 font-bold text-stone-800">
                                    {{ $pItem->ingredient->name }}
                                    <input type="hidden" name="items[{{ $idx }}][purchase_item_id]" value="{{ $pItem->id }}">
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-stone-600">
                                    {{ number_format($pItem->quantity, 2) }} {{ $pItem->unit->symbol }}
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-stone-600">
                                    {{ number_format($pItem->received_qty, 2) }} {{ $pItem->unit->symbol }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <input type="number" step="0.1" min="0" max="{{ $remainingToReceive }}" name="items[{{ $idx }}][quantity_received]" value="{{ $remainingToReceive }}" required
                                               class="w-20 text-right font-bold text-xs border border-emerald-300 rounded-lg p-1.5 focus:ring-1 focus:ring-emerald-500 bg-emerald-50/50">
                                        <span class="text-stone-500 text-[10px]">{{ $pItem->unit->symbol }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3">
                                    <input type="text" name="items[{{ $idx }}][batch_number]" placeholder="No. Batch"
                                           class="w-28 text-xs border border-stone-300 rounded p-1 font-mono">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Catatan Penerimaan Gudang</label>
                <input type="text" name="notes" placeholder="Kondisi barang baik, kemasan rapi"
                       class="w-full text-xs border border-stone-300 rounded-lg p-2">
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-100">Batal</a>
            <button type="submit" onclick="return confirm('Konfirmasi penerimaan barang? Stok gudang akan bertambah dan nilai rata-rata persediaan akan dihitung ulang otomatis.')"
                    class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                Posting Penerimaan & Masukkan ke Stok
            </button>
        </div>

    </form>

</div>
@endsection
