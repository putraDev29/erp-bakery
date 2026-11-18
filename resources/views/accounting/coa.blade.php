@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Bagan Akun Standar (Chart of Accounts - COA)</h1>
            <p class="text-xs text-stone-500 mt-1">Hierarki akun akuntansi terstandarisasi untuk bisnis bakery, makanan & minuman.</p>
        </div>
        <a href="{{ route('accounting.journals') }}" class="text-xs font-semibold text-stone-600 hover:text-stone-900 flex items-center space-x-1">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Buku Jurnal</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-stone-600 border-b border-stone-200">
                    <tr>
                        <th class="py-3 px-4 font-semibold w-32">Kode Akun</th>
                        <th class="py-3 px-4 font-semibold">Nama Akun</th>
                        <th class="py-3 px-4 font-semibold">Klasifikasi</th>
                        <th class="py-3 px-4 font-semibold text-center">Saldo Normal</th>
                        <th class="py-3 px-4 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($accounts as $acc)
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="py-3 px-4 font-mono font-bold text-stone-900">{{ $acc->code }}</td>
                            <td class="py-3 px-4 font-bold text-stone-800">{{ $acc->name }}</td>
                            <td class="py-3 px-4 uppercase font-semibold text-stone-500 text-[10px]">{{ $acc->type }}</td>
                            <td class="py-3 px-4 text-center font-mono font-bold uppercase text-stone-700">{{ $acc->normal_balance }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700">AKTIF</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
