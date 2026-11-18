@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Buku Jurnal Umum (Double-Entry Ledger)</h1>
            <p class="text-xs text-stone-500 mt-1">Audit transaksi finansial otomatis. Total Debit dan Kredit selalu seimbang secara atomik.</p>
        </div>
        <a href="{{ route('accounting.coa') }}" class="bg-stone-100 hover:bg-stone-200 text-stone-800 px-4 py-2 rounded-xl text-xs font-bold transition inline-flex items-center space-x-1 self-start">
            <i data-lucide="list-tree" class="w-4 h-4"></i>
            <span>Bagan Akun (COA)</span>
        </a>
    </div>

    <!-- Journal Entries Cards -->
    <div class="space-y-4">
        @forelse($entries as $entry)
            @php
                $entryDebit = $entry->lines->sum('debit');
                $entryCredit = $entry->lines->sum('credit');
                $isBalanced = abs($entryDebit - $entryCredit) < 0.01;
            @endphp
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="p-4 bg-stone-50/70 border-b border-stone-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex items-center space-x-3">
                        <span class="font-mono font-bold text-stone-900 text-sm">{{ $entry->entry_number }}</span>
                        <span class="text-stone-500">Tanggal: <strong class="text-stone-700 font-mono">{{ date('d/m/Y', strtotime($entry->entry_date)) }}</strong></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-stone-200 text-stone-800 font-mono">{{ $entry->source_type }}</span>
                    </div>

                    <div class="flex items-center space-x-3">
                        <span class="text-[11px] text-stone-500">{{ $entry->branch ? $entry->branch->name : 'Pusat' }}</span>
                        @if($isBalanced)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                SEIMBANG (BALANCED)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                TIDAK SEIMBANG
                            </span>
                        @endif
                    </div>
                </div>

                <div class="px-4 py-2 text-xs text-stone-600 bg-stone-50/20 italic">
                    {{ $entry->description }}
                </div>

                <!-- Lines Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-stone-400 border-b border-stone-100 text-[11px]">
                            <tr>
                                <th class="py-2 px-4 font-medium w-28">Kode Akun</th>
                                <th class="py-2 px-4 font-medium">Nama Akun COA</th>
                                <th class="py-2 px-4 font-medium">Keterangan Baris</th>
                                <th class="py-2 px-4 font-medium text-right w-36">Debit</th>
                                <th class="py-2 px-4 font-medium text-right w-36">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($entry->lines as $line)
                                <tr>
                                    <td class="py-2.5 px-4 font-mono font-bold text-stone-700">{{ $line->account->code }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-stone-900">{{ $line->account->name }}</td>
                                    <td class="py-2.5 px-4 text-stone-500 text-[11px]">{{ $line->description ?: '-' }}</td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-stone-900">
                                        {{ $line->debit > 0 ? 'Rp ' . number_format($line->debit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-stone-900">
                                        {{ $line->credit > 0 ? 'Rp ' . number_format($line->credit, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-stone-50/50 font-bold border-t border-stone-200">
                            <tr>
                                <td colspan="3" class="py-2.5 px-4 text-right text-stone-500">Total:</td>
                                <td class="py-2.5 px-4 text-right font-mono text-emerald-800">Rp {{ number_format($entryDebit, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-4 text-right font-mono text-emerald-800">Rp {{ number_format($entryCredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-stone-200 p-8 text-center text-stone-400 text-xs">
                Belum ada jurnal umum yang tercatat.
            </div>
        @endforelse

        <div class="pt-2">
            {{ $entries->links() }}
        </div>
    </div>

</div>
@endsection
