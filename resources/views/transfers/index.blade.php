@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Transfer Stok Antarcabang & Transit</h1>
            <p class="text-xs text-stone-500 mt-1">Distribusi bahan dan produk jadi antar cabang dengan pelacakan stok dalam perjalanan (In-Transit).</p>
        </div>
        <a href="{{ route('transfers.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1.5 self-start">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Minta Transfer Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">No. Dokumen</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Cabang Asal</th>
                        <th class="py-3 px-4 font-semibold">Cabang Tujuan</th>
                        <th class="py-3 px-4 font-semibold text-center">Jumlah Item</th>
                        <th class="py-3 px-4 font-semibold text-right">Nilai Barang</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($transfers as $tr)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-stone-900">{{ $tr->transfer_number }}</td>
                            <td class="py-3.5 px-4 text-stone-600">{{ date('d/m/Y', strtotime($tr->transfer_date)) }}</td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">{{ $tr->fromBranch->code }} — {{ $tr->fromBranch->name }}</td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">{{ $tr->toBranch->code }} — {{ $tr->toBranch->name }}</td>
                            <td class="py-3.5 px-4 text-center font-medium">{{ $tr->items->count() }} item</td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-900">Rp {{ number_format($tr->total_cost, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-center">
                                @if($tr->status === 'received')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">DITERIMA</span>
                                @elseif($tr->status === 'shipped')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 animate-pulse">DALAM PERJALANAN</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">MENUNGGU</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center space-x-1">
                                @if($tr->status === 'pending_approval' || $tr->status === 'draft')
                                    <form action="{{ route('transfers.ship', $tr->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Kirim transfer ini? Stok cabang asal akan berkurang dan masuk ke akun transit.')"
                                                class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-[11px] font-bold transition">
                                            Kirim Sekarang
                                        </button>
                                    </form>
                                @elseif($tr->status === 'shipped')
                                    <a href="{{ route('transfers.receive.form', $tr->id) }}" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold transition">
                                        Konfirmasi Terima
                                    </a>
                                @else
                                    <span class="text-stone-400 text-[11px]">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400">Belum ada dokumen transfer stok antarcabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $transfers->links() }}
        </div>
    </div>

</div>
@endsection
