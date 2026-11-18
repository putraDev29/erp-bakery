@extends('layouts.app')

@section('content')
<div x-data="productionPlanner()" class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Perencanaan Order Produksi Baru</h1>
            <p class="text-xs text-stone-500 mt-1">Pilih formula resep standar dan tentukan target kuantitas batch.</p>
        </div>
        <a href="{{ route('production.index') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('production.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Configuration Card -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-stone-900 border-b border-stone-100 pb-2">1. Formula Resep & Target Output</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Pilih Resep (BOM)</label>
                    <select name="recipe_id" x-model="selectedRecipeId" @change="calculate()" required
                            class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50 focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Pilih Formula Resep --</option>
                        @foreach($recipes as $rec)
                            <option value="{{ $rec->id }}">
                                {{ $rec->name }} (Yield: {{ number_format($rec->yield_quantity, 0) }} {{ $rec->yieldUnit->symbol }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Target Kuantitas Hasil</label>
                    <input type="number" step="1" min="1" name="target_quantity" x-model.number="targetQuantity" @input="calculate()" required
                           class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50 focus:ring-2 focus:ring-amber-500 font-bold text-stone-900">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1.5">Tanggal Rencana</label>
                    <input type="date" name="planned_date" value="{{ date('Y-m-d') }}"
                           class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2.5 bg-stone-50 focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1.5">Catatan Produksi (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Batch Pagi untuk Display Menteng"
                       class="w-full text-xs border border-stone-300 rounded-xl px-3 py-2 bg-stone-50">
            </div>
        </div>

        <!-- Dynamic Calculation Results -->
        <div x-show="calculation" class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-stone-900">2. Kalkulasi Kebutuhan Bahan Baku (BOM Scaling)</h2>
                    <p class="text-xs text-stone-500">Pengecekan otomatis terhadap saldo persediaan cabang aktif</p>
                </div>
                <template x-if="calculation && calculation.can_produce">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center space-x-1">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                        <span>Stok Cukup</span>
                    </span>
                </template>
                <template x-if="calculation && !calculation.can_produce">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 inline-flex items-center space-x-1">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                        <span>Ada Bahan Kurang</span>
                    </span>
                </template>
            </div>

            <!-- Material Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                        <tr>
                            <th class="py-2.5 px-3 font-semibold">Nama Bahan Baku</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Kebutuhan Resep</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Stok Cabang Tersedia</th>
                            <th class="py-2.5 px-3 font-semibold text-right">Estimasi Biaya</th>
                            <th class="py-2.5 px-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <template x-for="mat in calculation?.materials" :key="mat.ingredient_id">
                            <tr>
                                <td class="py-2.5 px-3 font-bold text-stone-800" x-text="mat.ingredient_name"></td>
                                <td class="py-2.5 px-3 text-right font-medium text-stone-700" x-text="mat.required_qty + ' ' + mat.unit_symbol"></td>
                                <td class="py-2.5 px-3 text-right font-medium text-stone-700" x-text="mat.available_qty + ' ' + mat.unit_symbol"></td>
                                <td class="py-2.5 px-3 text-right font-bold text-stone-900" x-text="formatRupiah(mat.estimated_cost)"></td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                          :class="mat.shortage_qty == 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'">
                                        <span x-text="mat.shortage_qty == 0 ? 'Cukup' : 'Kurang ' + mat.shortage_qty + ' ' + mat.unit_symbol"></span>
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Summary Cost Box -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-stone-100">
                <div class="p-3 bg-stone-50 rounded-xl">
                    <span class="text-[11px] text-stone-500 font-medium block">Estimasi Biaya Bahan:</span>
                    <span class="text-sm font-bold text-stone-900" x-text="formatRupiah(calculation?.estimated_material_cost || 0)"></span>
                </div>
                <div class="p-3 bg-stone-50 rounded-xl">
                    <span class="text-[11px] text-stone-500 font-medium block">Total Estimasi Biaya Batch:</span>
                    <span class="text-sm font-bold text-stone-900" x-text="formatRupiah(calculation?.estimated_batch_cost || 0)"></span>
                </div>
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl">
                    <span class="text-[11px] text-amber-800 font-semibold block">Estimasi HPP per Unit:</span>
                    <span class="text-base font-extrabold text-amber-900" x-text="formatRupiah(calculation?.estimated_unit_hpp || 0) + ' / unit'"></span>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('production.index') }}" class="px-5 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-bold hover:bg-stone-100">Batal</a>
            <button type="submit" class="px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md transition">
                Simpan & Rencanakan Order
            </button>
        </div>
    </form>

</div>

@push('scripts')
<script>
function productionPlanner() {
    return {
        selectedRecipeId: '',
        targetQuantity: 10,
        calculation: null,

        async calculate() {
            if (!this.selectedRecipeId || this.targetQuantity <= 0) {
                this.calculation = null;
                return;
            }

            try {
                const res = await fetch("{{ route('production.calc') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        recipe_id: this.selectedRecipeId,
                        target_quantity: this.targetQuantity,
                    }),
                });

                if (res.ok) {
                    this.calculation = await res.json();
                    this.$nextTick(() => lucide.createIcons());
                }
            } catch (e) {
                console.error(e);
            }
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        }
    }
}
</script>
@endpush
@endsection
