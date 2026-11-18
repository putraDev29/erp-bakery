@extends('layouts.app')

@section('content')
<div x-data="{ expenseModalOpen: false }" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Kas, Bank & Beban Operasional</h1>
            <p class="text-xs text-stone-500 mt-1">Kelola saldo brankas kasir, rekening bank usaha, dan catat beban pengeluaran toko.</p>
        </div>
        <button type="button" @click="expenseModalOpen = true" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md transition inline-flex items-center space-x-1.5 self-start">
            <i data-lucide="minus-circle" class="w-4 h-4"></i>
            <span>Catat Pengeluaran Kas Baru</span>
        </button>
    </div>

    <!-- Accounts Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($accounts as $acc)
            <div class="bg-white p-5 rounded-2xl border border-stone-200/90 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-mono font-bold text-stone-400">{{ $acc->code }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $acc->type === 'bank' ? 'bg-blue-50 text-blue-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ $acc->type }}
                    </span>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-stone-800">{{ $acc->name }}</h3>
                    <p class="text-[11px] text-stone-500">{{ $acc->account_number ?: 'Brankas Tunai Cabang' }}</p>
                </div>
                <div class="mt-4 pt-3 border-t border-stone-100 flex items-center justify-between">
                    <span class="text-[11px] text-stone-500">Saldo:</span>
                    <span class="text-base font-extrabold text-stone-900">Rp {{ number_format($acc->current_balance, 0, ',', '.') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Expenses History -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-stone-100">
            <h2 class="text-base font-bold text-stone-900">Riwayat Beban Pengeluaran Operasional</h2>
            <p class="text-xs text-stone-500">Setiap pengeluaran otomatis dijurnal ke akun beban dan mengurangi saldo kas/bank terkait.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold">No. Bukti</th>
                        <th class="py-3 px-4 font-semibold">Tanggal</th>
                        <th class="py-3 px-4 font-semibold">Kategori Beban</th>
                        <th class="py-3 px-4 font-semibold">Akun Kas / Bank</th>
                        <th class="py-3 px-4 font-semibold">Cabang</th>
                        <th class="py-3 px-4 font-semibold">Catatan</th>
                        <th class="py-3 px-4 font-semibold text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">{{ $exp->expense_number }}</td>
                            <td class="py-3 px-4 text-stone-600">{{ date('d/m/Y', strtotime($exp->expense_date)) }}</td>
                            <td class="py-3 px-4 font-bold text-stone-800">{{ $exp->category->name }}</td>
                            <td class="py-3 px-4 font-medium text-stone-700">{{ $exp->account->name }}</td>
                            <td class="py-3 px-4 font-medium text-stone-600">{{ $exp->branch->code }}</td>
                            <td class="py-3 px-4 text-stone-600">{{ $exp->notes ?: '-' }}</td>
                            <td class="py-3 px-4 text-right font-bold text-rose-700">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-stone-400">Belum ada pengeluaran operasional yang dicatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-stone-100">
            {{ $expenses->links() }}
        </div>
    </div>

    <!-- MODAL CATAT BEBAN BARU -->
    <div x-show="expenseModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-stone-950/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="expenseModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-stone-200 space-y-4">
            <div class="flex items-center justify-between border-b border-stone-100 pb-3">
                <h3 class="text-base font-bold text-stone-900">Catat Beban Operasional Baru</h3>
                <button @click="expenseModalOpen = false" class="text-stone-400 hover:text-stone-700">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('finance.expenses.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Kategori Beban</label>
                    <select name="expense_category_id" required class="w-full border border-stone-300 rounded-lg p-2 bg-stone-50 font-medium">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Sumber Kas / Bank</label>
                    <select name="account_id" required class="w-full border border-stone-300 rounded-lg p-2 bg-stone-50 font-medium">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }} (Rp {{ number_format($acc->current_balance, 0, ',', '.') }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Nominal Biaya (Rp)</label>
                    <input type="number" step="1000" min="1" name="amount" required placeholder="50000"
                           class="w-full border border-stone-300 rounded-lg p-2 font-bold text-stone-900 text-sm">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Tanggal</label>
                    <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required
                           class="w-full border border-stone-300 rounded-lg p-2 bg-stone-50">
                </div>

                <div>
                    <label class="block font-semibold text-stone-700 mb-1">Keterangan Pengeluaran</label>
                    <input type="text" name="notes" placeholder="Contoh: Beli token listrik toko 100rb"
                           class="w-full border border-stone-300 rounded-lg p-2">
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" @click="expenseModalOpen = false" class="px-4 py-2 border border-stone-300 rounded-lg font-semibold hover:bg-stone-50">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold shadow-sm">
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
