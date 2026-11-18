@extends('layouts.app')

@section('content')
<div x-data="posApp()" class="h-[calc(100vh-8.5rem)] flex flex-col lg:flex-row gap-5 -m-2">

    <!-- LEFT: PRODUCT CATALOG -->
    <div class="flex-1 flex flex-col bg-white rounded-2xl border border-stone-200/90 shadow-sm overflow-hidden">

        <!-- Search & Category Filters -->
        <div class="p-4 border-b border-stone-200/80 bg-stone-50/50 space-y-3">
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama kue, roti, atau barcode SKU..."
                           class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none">
                </div>
            </div>

            <!-- Categories Scrollable Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <button type="button" @click="selectedCategory = ''"
                        :class="selectedCategory === '' ? 'bg-amber-600 text-white font-bold' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'"
                        class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition">
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button type="button" @click="selectedCategory = '{{ $cat->id }}'"
                            :class="selectedCategory == '{{ $cat->id }}' ? 'bg-amber-600 text-white font-bold' : 'bg-white text-stone-700 hover:bg-stone-100 border border-stone-200'"
                            class="px-3.5 py-1.5 rounded-lg whitespace-nowrap transition">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3.5">
            <template x-for="product in filteredProducts" :key="product.id">
                <div @click="addToCart(product)"
                     :class="product.current_stock > 0 ? 'cursor-pointer hover:border-amber-400 hover:shadow-md' : 'opacity-60 cursor-not-allowed bg-stone-50'"
                     class="border border-stone-200/90 rounded-xl p-3 flex flex-col justify-between transition group relative bg-white">

                    <!-- Stock Badge -->
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                              :class="product.current_stock > 5 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (product.current_stock > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-rose-50 text-rose-700 border border-rose-200')">
                            <span x-text="product.current_stock > 0 ? 'Stok: ' + product.current_stock + ' ' + product.unit.symbol : 'Habis'"></span>
                        </span>
                        <span class="text-[10px] text-stone-400 font-mono" x-text="product.code"></span>
                    </div>

                    <!-- Icon / Placeholder -->
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center mx-auto my-1 group-hover:scale-105 transition">
                        <i data-lucide="cake" class="w-6 h-6"></i>
                    </div>

                    <!-- Name & Price -->
                    <div class="mt-2 text-center">
                        <h3 class="text-xs font-bold text-stone-900 line-clamp-1" x-text="product.name"></h3>
                        <p class="text-xs font-extrabold text-amber-700 mt-0.5" x-text="formatRupiah(product.sale_price)"></p>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- RIGHT: CART & CHECKOUT PANEL -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-2xl border border-stone-200/90 shadow-sm overflow-hidden flex-shrink-0">

        <!-- Cart Header -->
        <div class="p-4 border-b border-stone-200 flex items-center justify-between bg-stone-50/60">
            <div class="flex items-center space-x-2">
                <i data-lucide="shopping-bag" class="w-4 h-4 text-amber-600"></i>
                <h2 class="text-sm font-bold text-stone-900">Keranjang Kasir</h2>
            </div>
            <button @click="clearCart()" x-show="cart.length > 0" class="text-[11px] text-rose-600 hover:underline font-semibold">
                Kosongkan
            </button>
        </div>

        <!-- Customer Selector -->
        <div class="px-4 py-2.5 border-b border-stone-100 bg-stone-50/20">
            <label class="block text-[10px] font-semibold text-stone-500 uppercase tracking-wider mb-1">Pelanggan</label>
            <select x-model="customerId" class="w-full text-xs border border-stone-300 rounded-lg px-2.5 py-1.5 bg-white font-medium">
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                @endforeach
            </select>
        </div>

        <!-- Cart Item List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2.5 divide-y divide-stone-100">
            <template x-for="(item, index) in cart" :key="item.product_id">
                <div class="pt-2.5 first:pt-0 flex items-center justify-between">
                    <div class="flex-1 pr-2">
                        <h4 class="text-xs font-bold text-stone-800" x-text="item.name"></h4>
                        <p class="text-[11px] text-stone-500 font-medium" x-text="formatRupiah(item.unit_price) + ' × ' + item.quantity"></p>
                    </div>

                    <!-- Quantity Adjuster -->
                    <div class="flex items-center space-x-1.5">
                        <button @click="decreaseQty(index)" class="w-6 h-6 rounded-md bg-stone-100 hover:bg-stone-200 text-stone-700 flex items-center justify-center font-bold text-xs">-</button>
                        <span class="text-xs font-bold w-6 text-center" x-text="item.quantity"></span>
                        <button @click="increaseQty(index)" class="w-6 h-6 rounded-md bg-stone-100 hover:bg-stone-200 text-stone-700 flex items-center justify-center font-bold text-xs">+</button>
                    </div>

                    <div class="text-right pl-3">
                        <p class="text-xs font-bold text-stone-900" x-text="formatRupiah(item.quantity * item.unit_price)"></p>
                        <button @click="removeItem(index)" class="text-[10px] text-rose-500 hover:underline">Hapus</button>
                    </div>
                </div>
            </template>

            <div x-show="cart.length === 0" class="h-40 flex flex-col items-center justify-center text-stone-400 text-xs">
                <i data-lucide="shopping-cart" class="w-8 h-8 mb-2 stroke-1"></i>
                <span>Belum ada kue di keranjang</span>
            </div>
        </div>

        <!-- Cart Totals & Checkout Trigger -->
        <div class="p-4 border-t border-stone-200 bg-stone-50/50 space-y-3">
            <div class="space-y-1.5 text-xs text-stone-600">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-bold text-stone-800" x-text="formatRupiah(subtotal)"></span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-stone-900 pt-1 border-t border-stone-200">
                    <span>Grand Total:</span>
                    <span class="text-amber-700" x-text="formatRupiah(subtotal)"></span>
                </div>
            </div>

            <button type="button" @click="openPaymentModal()" :disabled="cart.length === 0"
                    :class="cart.length > 0 ? 'bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white' : 'bg-stone-200 text-stone-400 cursor-not-allowed'"
                    class="w-full py-3 rounded-xl font-bold text-xs shadow-md transition flex items-center justify-center space-x-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Bayar Transaksi (F9)</span>
            </button>
        </div>
    </div>

    <!-- MODAL PEMBAYARAN TENDER -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-stone-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="showPaymentModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200 space-y-5">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="text-base font-bold text-stone-900">Pembayaran Kasir POS</h3>
                <button @click="showPaymentModal = false" class="text-stone-400 hover:text-stone-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Total Tagihan Display -->
            <div class="bg-amber-50 p-4 rounded-xl border border-amber-200/80 text-center">
                <span class="text-xs text-amber-800 font-semibold uppercase tracking-wider block">Total Tagihan</span>
                <span class="text-2xl font-black text-amber-900" x-text="formatRupiah(subtotal)"></span>
            </div>

            <!-- Metode Pembayaran Tabs -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-2">Metode Pembayaran</label>
                <div class="grid grid-cols-4 gap-2 text-xs">
                    <button type="button" @click="paymentMethod = 'cash'; paidAmount = subtotal"
                            :class="paymentMethod === 'cash' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-100 text-stone-700'"
                            class="py-2 rounded-lg transition text-center">Tunai</button>
                    <button type="button" @click="paymentMethod = 'transfer'; paidAmount = subtotal"
                            :class="paymentMethod === 'transfer' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-100 text-stone-700'"
                            class="py-2 rounded-lg transition text-center">Transfer</button>
                    <button type="button" @click="paymentMethod = 'qris'; paidAmount = subtotal"
                            :class="paymentMethod === 'qris' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-100 text-stone-700'"
                            class="py-2 rounded-lg transition text-center">QRIS</button>
                    <button type="button" @click="paymentMethod = 'tempo'; paidAmount = 0"
                            :class="paymentMethod === 'tempo' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-100 text-stone-700'"
                            class="py-2 rounded-lg transition text-center">Tempo</button>
                </div>
            </div>

            <!-- Nominal Uang Diterima -->
            <div x-show="paymentMethod !== 'tempo'">
                <label class="block text-xs font-semibold text-stone-700 mb-1.5">Jumlah Uang Diterima (Rp)</label>
                <input type="number" x-model.number="paidAmount"
                       class="w-full px-3.5 py-2.5 border border-stone-300 rounded-xl text-lg font-bold text-stone-900 focus:ring-2 focus:ring-amber-500 outline-none">

                <!-- Tombol Nominal Cepat -->
                <div class="grid grid-cols-4 gap-1.5 mt-2">
                    <button type="button" @click="paidAmount = subtotal" class="py-1 px-2 border border-stone-200 rounded text-[11px] font-semibold hover:bg-stone-100">Uang Pas</button>
                    <button type="button" @click="paidAmount = 50000" class="py-1 px-2 border border-stone-200 rounded text-[11px] font-semibold hover:bg-stone-100">50.000</button>
                    <button type="button" @click="paidAmount = 100000" class="py-1 px-2 border border-stone-200 rounded text-[11px] font-semibold hover:bg-stone-100">100.000</button>
                    <button type="button" @click="paidAmount = 200000" class="py-1 px-2 border border-stone-200 rounded text-[11px] font-semibold hover:bg-stone-100">200.000</button>
                </div>
            </div>

            <!-- Kembalian Display -->
            <div x-show="paymentMethod === 'cash'" class="p-3 bg-stone-50 rounded-xl border border-stone-200 flex justify-between items-center text-xs">
                <span class="font-semibold text-stone-600">Kembalian:</span>
                <span class="font-extrabold text-base" :class="changeAmount >= 0 ? 'text-emerald-700' : 'text-rose-600'" x-text="formatRupiah(changeAmount)"></span>
            </div>

            <!-- Catatan -->
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Catatan Nota (Opsional)</label>
                <input type="text" x-model="notes" placeholder="Contoh: Meja 4 / Pesanan Khusus"
                       class="w-full px-3 py-1.5 border border-stone-300 rounded-lg text-xs outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 pt-2">
                <button type="button" @click="showPaymentModal = false" class="flex-1 py-2.5 border border-stone-300 text-stone-700 rounded-xl text-xs font-semibold hover:bg-stone-100">Batal</button>
                <button type="button" @click="submitOrder()" :disabled="isSubmitting"
                        class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center justify-center space-x-1">
                    <span x-text="isSubmitting ? 'Memproses...' : 'Selesaikan & Cetak Nota'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function posApp() {
    return {
        products: @json($products),
        searchQuery: '',
        selectedCategory: '',
        cart: [],
        customerId: {{ $customers->first()->id ?? 1 }},
        paymentMethod: 'cash',
        paidAmount: 0,
        notes: '',
        showPaymentModal: false,
        isSubmitting: false,

        get filteredProducts() {
            return this.products.filter(p => {
                const matchCat = this.selectedCategory === '' || p.category_id == this.selectedCategory;
                const matchSearch = this.searchQuery === '' ||
                    p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    p.code.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    (p.barcode && p.barcode.includes(this.searchQuery));
                return matchCat && matchSearch;
            });
        },

        get subtotal() {
            return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0);
        },

        get changeAmount() {
            return Math.max(0, this.paidAmount - this.subtotal);
        },

        addToCart(product) {
            if (product.current_stock <= 0) {
                alert("Stok produk ini sedang habis di cabang aktif.");
                return;
            }
            const exist = this.cart.find(c => c.product_id === product.id);
            if (exist) {
                if (exist.quantity + 1 > product.current_stock) {
                    alert("Kuantitas melebihi sisa stok yang tersedia!");
                    return;
                }
                exist.quantity += 1;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    unit_price: parseFloat(product.sale_price),
                    quantity: 1,
                    max_stock: product.current_stock,
                });
            }
        },

        increaseQty(index) {
            if (this.cart[index].quantity + 1 > this.cart[index].max_stock) {
                alert("Kuantitas melebihi stok tersedia!");
                return;
            }
            this.cart[index].quantity += 1;
        },

        decreaseQty(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity -= 1;
            } else {
                this.cart.splice(index, 1);
            }
        },

        removeItem(index) {
            this.cart.splice(index, 1);
        },

        clearCart() {
            if (confirm("Kosongkan semua pesanan di keranjang?")) {
                this.cart = [];
            }
        },

        openPaymentModal() {
            this.paidAmount = this.subtotal;
            this.showPaymentModal = true;
        },

        formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
        },

        async submitOrder() {
            if (this.paymentMethod === 'cash' && this.paidAmount < this.subtotal) {
                alert("Nominal uang yang diterima kurang dari total belanja!");
                return;
            }

            this.isSubmitting = true;

            const payload = {
                items: this.cart.map(i => ({
                    product_id: i.product_id,
                    quantity: i.quantity,
                    unit_price: i.unit_price,
                })),
                payment_method: this.paymentMethod,
                paid_amount: this.paidAmount,
                customer_id: this.customerId,
                notes: this.notes,
            };

            try {
                const response = await fetch("{{ route('pos.checkout') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.receipt_url;
                } else {
                    alert("Error: " + data.message);
                    this.isSubmitting = false;
                }
            } catch (err) {
                alert("Terjadi kesalahan jaringan.");
                this.isSubmitting = false;
            }
        }
    }
}
</script>
@endpush
@endsection
