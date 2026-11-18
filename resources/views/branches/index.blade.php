@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-stone-200 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-stone-900">Unit Usaha & Manajemen Cabang (Branches)</h1>
            <p class="text-xs text-stone-500 mt-1">Struktur multi-cabang perusahaan (Kantor Pusat & Central Kitchen, Toko/Outlet).</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($branches as $b)
            <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 space-y-4 hover:border-amber-400 transition">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded border border-amber-200">
                        {{ $b->code }}
                    </span>
                    @if($b->is_central)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                            KANTOR PUSAT & CENTRAL KITCHEN
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            OUTLET TOKO
                        </span>
                    @endif
                </div>

                <div>
                    <h2 class="text-base font-bold text-stone-900">{{ $b->name }}</h2>
                    <p class="text-xs text-stone-500 mt-1">{{ $b->address }}</p>
                </div>

                <div class="pt-3 border-t border-stone-100 flex items-center justify-between text-xs text-stone-600">
                    <span>Telepon: <strong class="text-stone-800">{{ $b->phone }}</strong></span>
                    <span>Staf Terhubung: <strong class="text-stone-800">{{ $b->users_count }} pengguna</strong></span>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
