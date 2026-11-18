@extends('layouts.app')

@section('content')
<div x-data="transferForm()" class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Buat Permintaan Transfer Antarcabang</h1>
            <p class="text-xs text-stone-500 mt-1">Kirim bahan baku atau produk jadi dari cabang asal ke cabang tujuan.</p>
        </div>
        <a href="{{ route('transfers.index') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('transfers.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">1. Rute Pengiriman</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Cabang Asal (Pengirim)</label>
                    <input type="text" readonly value="{{ $currentBranch->name ?? 'Cabang Aktif' }} ({{ $currentBranch->code ?? '' }})"
                           class="w-full text-xs font-bold border border-stone-200 bg-stone-100/70 text-stone-800 rounded-xl px-3 py-2.5">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Pilih Cabang Tujuan (Penerima)</label>
                    <select name="to_branch_id" required class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50 font-bold text-stone-900 focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Pilih Cabang Tujuan --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->code }} — {{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1.5">Catatan Pengiriman</label>
                <input type="text" name="notes" placeholder="Contoh: Stok harian toko menteng, kirim via kurir internal"
                       class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2 bg-stone-50">
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-2">
                <h2 class="text-sm font-bold text-stone-900">2. Daftar Item yang Ditransfer</h2>
                <button type="button" @click="addItem()" class="bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 px-3 py-1 rounded-lg text-xs font-bold transition inline-flex items-center space-x-1">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Tambah Item</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex flex-wrap items-center gap-3 text-xs">
                        <div class="w-32">
                            <label class="block text-[10px] font-semibold text-stone-500 uppercase">Tipe Item</label>
                            <select :name="'items[' + idx + '][item_type]'" x-model="item.item_type" class="w-full border border-stone-300 rounded-lg p-1.5 bg-white font-medium">
                                <option value="product">Produk Jadi</option>
                                <option value="ingredient">Bahan Baku</option>
                            </select>
                        </div>

                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-[10px] font-semibold text-stone-500 uppercase">Pilih Item</label>
                            <select :name="'items[' + idx + '][item_id]'" x-model="item.item_id" required class="w-full border border-stone-300 rounded-lg p-1.5 bg-white font-medium">
                                <template x-if="item.item_type === 'product'">
                                    <optgroup label="Produk Jadi">
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                                        @endforeach
                                    </optgroup>
                                </template>
                                <template x-if="item.item_type === 'ingredient'">
                                    <optgroup label="Bahan Baku">
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->code }})</option>
                                        @endforeach
                                    </optgroup>
                                </template>
                            </select>
                        </div>

                        <div class="w-28">
                            <label class="block text-[10px] font-semibold text-stone-500 uppercase">Kuantitas</label>
                            <input type="number" step="0.1" min="0.1" :name="'items[' + idx + '][quantity]'" x-model.number="item.quantity" required
                                   class="w-full border border-stone-300 rounded-lg p-1.5 bg-white font-bold text-stone-900 text-right">
                        </div>

                        <div class="pt-4">
                            <button type="button" @click="removeItem(idx)" x-show="items.length > 1" class="text-rose-600 hover:text-rose-800 p-1">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('transfers.index') }}" class="px-5 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-100">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                Simpan Permintaan Transfer
            </button>
        </div>

    </form>

</div>

@push('scripts')
<script>
function transferForm() {
    return {
        items: [
            { item_type: 'product', item_id: '{{ $products->first()->id ?? 1 }}', quantity: 10 }
        ],
        addItem() {
            this.items.push({ item_type: 'product', item_id: '{{ $products->first()->id ?? 1 }}', quantity: 10 });
            this.$nextTick(() => lucide.createIcons());
        },
        removeItem(idx) {
            this.items.splice(idx, 1);
        }
    }
}
</script>
@endpush
@endsection
