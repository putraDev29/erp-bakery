@extends('layouts.app')

@section('content')
<div x-data="{ payModalOpen: false, activePurchase: null }" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Manajemen Pengadaan Bahan Baku (Purchasing)</h1>
            <p class="text-xs text-stone-500 mt-1">Pencatatan PO, penerimaan fisik barang ke gudang, dan pengelolaan hutang usaha (AP).</p>
        </div>
        <a href="{{ route('purchases.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1.5 self-start">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Buat Pembelian Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">No. Pembelian</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Supplier</th>
                        <th class="py-3 px-4 font-semibold">Cabang</th>
                        <th class="py-3 px-4 font-semibold text-right">Total Tagihan</th>
                        <th class="py-3 px-4 font-semibold text-right">Sisa Hutang</th>
                        <th class="py-3 px-4 font-semibold text-center">Status Barang</th>
                        <th class="py-3 px-4 font-semibold text-center">Status Bayar</th>
                        <th class="py-3 px-4 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-stone-900">{{ $p->purchase_number }}</td>
                            <td class="py-3.5 px-4 text-stone-600">{{ date('d/m/Y', strtotime($p->purchase_date)) }}</td>
                            <td class="py-3.5 px-4 font-bold text-stone-800">{{ $p->supplier->name }}</td>
                            <td class="py-3.5 px-4 font-medium text-stone-600">{{ $p->branch->code }}</td>
                            <td class="py-3.5 px-4 text-right font-bold text-stone-900">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-right font-bold {{ $p->remaining_due > 0 ? 'text-rose-700' : 'text-stone-400' }}">
                                Rp {{ number_format($p->remaining_due, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($p->status === 'received')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">DITERIMA</span>
                                @elseif($p->status === 'partially_received')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">SEBAGIAN</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">DIPESAN</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($p->payment_status === 'paid')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">LUNAS</span>
                                @elseif($p->payment_status === 'partially_paid')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700">SEBAGIAN</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700">BELUM LUNAS</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center space-x-1">
                                @if($p->status !== 'received')
                                    <a href="{{ route('purchases.receive.form', $p->id) }}" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-[11px] font-bold transition">
                                        Terima Fisik
                                    </a>
                                @endif

                                @if($p->remaining_due > 0)
                                    <button type="button" @click="activePurchase = {{ $p->toJson() }}; payModalOpen = true"
                                            class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-bold transition">
                                        Bayar Hutang
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-stone-400">Belum ada transaksi pembelian bahan baku.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $purchases->links() }}
        </div>
    </div>

    <!-- MODAL BAYAR HUTANG SUPPLIER -->
    <div x-show="payModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-stone-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="payModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="text-base font-bold text-stone-900">Pelunasan Hutang Supplier</h3>
                <button @click="payModalOpen = false" class="text-stone-400 hover:text-stone-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <template x-if="activePurchase">
                <form :action="'/purchases/' + activePurchase.id + '/pay'" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div class="bg-stone-50 p-3 rounded-xl border border-stone-200 space-y-1">
                        <div class="flex justify-between">
                            <span class="text-stone-500">No. Pembelian:</span>
                            <span class="font-bold text-stone-900" x-text="activePurchase.purchase_number"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Supplier:</span>
                            <span class="font-bold text-stone-900" x-text="activePurchase.supplier.name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-stone-500">Sisa Hutang:</span>
                            <span class="font-bold text-rose-700 text-sm" x-text="'Rp ' + (activePurchase.total_amount - activePurchase.paid_amount).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Pilih Rekening Kas / Bank Pembayar</label>
                        <select name="account_id" required class="w-full border border-stone-300 rounded-lg p-2 bg-white font-medium">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Saldo: Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Nominal Pembayaran (Rp)</label>
                        <input type="number" name="amount" :max="activePurchase.total_amount - activePurchase.paid_amount" :value="activePurchase.total_amount - activePurchase.paid_amount" required
                               class="w-full border border-stone-300 rounded-lg p-2 font-bold text-stone-900 text-sm">
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Metode</label>
                        <select name="payment_method" class="w-full border border-stone-300 rounded-lg p-2 bg-white font-medium">
                            <option value="bank">Transfer Bank</option>
                            <option value="cash">Kas Tunai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-stone-700 mb-1">Catatan</label>
                        <input type="text" name="notes" placeholder="No. ref transfer / bukti transfer"
                               class="w-full border border-stone-300 rounded-lg p-2">
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" @click="payModalOpen = false" class="px-4 py-2 border border-stone-300 rounded-lg font-semibold hover:bg-stone-50">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold shadow-sm">
                            Konfirmasi Bayar
                        </button>
                    </div>
                </form>
            </template>
        </div>
    </div>

</div>
@endsection
