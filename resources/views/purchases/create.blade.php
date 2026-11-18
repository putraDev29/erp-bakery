@extends('layouts.app')

@section('content')
<div x-data="purchaseForm()" class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Buat Pembelian Bahan Baku Baru</h1>
            <p class="text-xs text-stone-500 mt-1">Pencatatan Purchase Order (PO) kepada supplier terdaftar.</p>
        </div>
        <a href="{{ route('purchases.index') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('purchases.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">1. Data Supplier & Termin Pembayaran</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Pilih Supplier</label>
                    <select name="supplier_id" required class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50 font-bold text-stone-900 focus:ring-2 focus:ring-amber-500">
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Metode / Termin Pembayaran</label>
                    <select name="payment_method" class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50 font-medium">
                        <option value="tempo">Kredit / Tempo (Hutang Usaha)</option>
                        <option value="cash">Tunai Langsung</option>
                        <option value="transfer">Transfer Bank Langsung</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Tanggal Jatuh Tempo (Jika Tempo)</label>
                    <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                           class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1.5">No. Invoice / Surat Jalan Supplier (Opsional)</label>
                <input type="text" name="supplier_invoice_no" placeholder="Contoh: INV-BGS-2026-891"
                       class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2 bg-stone-50">
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h2 class="text-sm font-bold text-stone-900">2. Rincian Item Bahan Baku</h2>
                <button type="button" @click="addItem()" class="bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 px-3 py-1 rounded-lg text-xs font-bold transition inline-flex items-center space-x-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Tambah Baris</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex flex-wrap items-center gap-3 text-xs">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-[10px] font-semibold text-stone-500 uppercase">Pilih Bahan Baku</label>
                            <select :name="'items[' + idx + '][ingredient_id]'" x-model="item.ingredient_id" @change="updatePrice(idx)" required class="w-full border border-stone-300 rounded-lg p-1.5 bg-white font-medium">
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" data-price="{{ $ing->cost_price }}">{{ $ing->name }} ({{ $ing->unit->symbol }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="w-28">
                            <label class="block text-[10px] font-semibold text-stone-500 uppercase">Kuantitas</label>
                            <input type="number" step="0.1" min="0.1" :name="'items[' + idx + '][quantity]'" x-model.number="item.quantity" required
                                   class="w-full border border-stone-300 rounded-lg p-1.5 bg-white font-bold text-stone-900 text-right">
                        </div>

                        <div class="w-36">
                            <label class="block text-[10px] font-semibold text-stone-500 uppercase">Harga Satuan (Rp)</label>
                            <input type="number" step="100" min="0" :name="'items[' + idx + '][unit_price]'" x-model.number="item.unit_price" required
                                   class="w-full border border-stone-300 rounded-lg p-1.5 bg-white font-bold text-stone-900 text-right">
                        </div>

                        <div class="w-32 text-right pt-3">
                            <span class="text-[10px] text-stone-500 block">Subtotal:</span>
                            <span class="font-bold text-stone-900 text-xs" x-text="'Rp ' + (item.quantity * item.unit_price).toLocaleString('id-ID')"></span>
                        </div>

                        <div class="pt-3">
                            <button type="button" @click="removeItem(idx)" x-show="items.length > 1" class="text-rose-600 hover:text-rose-800 p-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Grand Total Display -->
            <div class="p-4 bg-amber-50 rounded-xl border border-amber-200/80 flex justify-between items-center">
                <span class="text-xs font-bold text-amber-900">Total Estimasi Pembelian:</span>
                <span class="text-lg font-black text-amber-950" x-text="'Rp ' + totalAmount.toLocaleString('id-ID')"></span>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-100">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                Simpan & Buat Dokumen Pembelian
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
function purchaseForm() {
    return {
        items: [
            { ingredient_id: '{{ $ingredients->first()->id ?? 1 }}', quantity: 10, unit_price: {{ $ingredients->first()->cost_price ?? 0 }} }
        ],
        get totalAmount() {
            return this.items.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        },
        addItem() {
            this.items.push({ ingredient_id: '{{ $ingredients->first()->id ?? 1 }}', quantity: 10, unit_price: {{ $ingredients->first()->cost_price ?? 0 }} });
            this.$nextTick(() => lucide.createIcons());
        },
        removeItem(idx) {
            this.items.splice(idx, 1);
        },
        updatePrice(idx) {
            // Price auto populate if needed
        }
    }
}
</script>
@endpush
@endsection
