@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Konfirmasi Penerimaan Transfer Masuk</h1>
            <p class="text-xs text-stone-500 mt-1">Verifikasi jumlah fisik barang yang diterima di cabang tujuan.</p>
        </div>
        <span class="font-mono text-xs font-bold text-stone-700 bg-stone-100 px-3 py-1.5 rounded-lg border border-stone-200">
            {{ $transfer->transfer_number }}
        </span>
    </div>

    <form action="{{ route('transfers.receive.post', $transfer->id) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-purple-50/60 p-4 rounded-2xl border border-purple-200 text-xs flex justify-between items-center">
            <div>
                <span class="text-stone-500 block">Dikirim Dari:</span>
                <span class="font-bold text-stone-900 text-sm">{{ $transfer->fromBranch->name }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Tujuan Penerimaan:</span>
                <span class="font-bold text-stone-900 text-sm">{{ $transfer->toBranch->name }}</span>
            </div>
            <div>
                <span class="text-stone-500 block">Tanggal Kirim:</span>
                <span class="font-mono text-stone-700">{{ date('d/m/Y H:i', strtotime($transfer->shipped_at)) }}</span>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">Rincian Fisik Barang Diterima</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                        <tr>
                            <th class="py-2.5 px-3 font-semibold">Nama Item</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Kuantitas Dikirim</th>
                            <th class="py-2.5 px-3 font-semibold text-right w-40">Kuantitas Diterima</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($transfer->items as $idx => $tItem)
                            <tr>
                                <td class="py-3 px-3 font-bold text-stone-800">
                                    {{ $tItem->item->name ?? '-' }}
                                    <input type="hidden" name="items[{{ $idx }}][transfer_item_id]" value="{{ $tItem->id }}">
                                </td>
                                <td class="py-3 px-3 text-right font-medium text-stone-600">
                                    {{ number_format($tItem->quantity_shipped, 2) }} {{ $tItem->item->unit->symbol ?? '' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <div class="flex items-center justify-end space-x-1.5">
                                        <input type="number" step="0.1" min="0" name="items[{{ $idx }}][quantity_received]" value="{{ $tItem->quantity_shipped }}" required
                                               class="w-24 text-right font-bold text-xs border border-emerald-300 rounded-lg px-2 py-1.5 focus:ring-1 focus:ring-emerald-500 bg-emerald-50/50">
                                        <span class="text-stone-500 text-[11px]">{{ $tItem->item->unit->symbol ?? '' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('transfers.index') }}" class="px-5 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-100">Batal</a>
            <button type="submit" onclick="return confirm('Konfirmasi penerimaan barang? Stok cabang tujuan akan bertambah dan stok transit akan dikliring.')"
                    class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                Konfirmasi Penerimaan Fisik
            </button>
        </div>

    </form>

</div>
@endsection
