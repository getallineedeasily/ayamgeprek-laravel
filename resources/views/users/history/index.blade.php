@php
    use App\Enums\TransactionStatus;
@endphp

@extends('users.layout')
@section('user-content')
    <section class="flex-1 p-6 md:p-10">
        @session('error')
            <div class="bg-red-200 text-red-800 rounded-xl p-4 mb-8 font-semibold">
                <p>{{ $value }}</p>
            </div>
        @endsession

        @session('success')
            <div class="bg-green-200 text-green-800 rounded-xl p-4 mb-8 font-semibold">
                <p>{{ $value }}</p>
            </div>
        @endsession

        <div class="bg-gray-50 p-6 rounded-[15px]">
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h1>
            <p class="text-gray-600 mt-2">Lihat semua pesanan yang dibuat</p>
        </div>

        <div class="bg-gray-50 p-6 md:p-8 rounded-[15px] mt-8">
            <form action="{{ route('user.view.history') }}" method="GET" class="space-y-5">
                <input type="text" placeholder="Cari ID pesanan"
                    class="px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white w-full block"
                    name="search">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="space-y-2">
                        <label for="status" class="text-gray-500 block">Status</label>
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white"
                            name="status">
                            <option value="" selected>Pilih status</option>
                            @foreach ($statuses as $s)
                                <option value="{{ $s->value }}">{{ $s->value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="start_date" class="text-gray-500 block">Tanggal Awal</label>
                        <input type="date" name="start_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange text-gray-500 bg-white block">
                    </div>
                    <div class="space-y-2">
                        <label for="end_date" class="text-gray-500 block">Tanggal Akhir</label>
                        <input type="date" name="end_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange text-gray-500 bg-white block">
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="submit"
                        class="bg-brand-orange text-white font-bold py-2 px-4 rounded-[10px] hover:bg-orange-600 cursor-pointer block w-full md:w-1/2">
                        Cari
                    </button>
                    <button type="button" disabled
                        class="bg-gray-300 font-bold py-2 px-4 rounded-[10px] cursor-not-allowed hidden loading w-full md:w-1/2">
                        Cari
                    </button>
                </div>
            </form>

            @if ($search || $status || $start_date || $end_date)
                <div class="mt-8 space-y-2">
                    @if ($start_date && $end_date)
                        <p class="text-gray-500 text-base">Menampilkan transaksi dari <span
                                class="font-bold">"{{ formatDate($start_date, false) }}"</span> sampai <span
                                class="font-bold">"{{ formatDate($end_date, false) }}"</span> </p>
                    @elseif ($start_date)
                        <p class="text-gray-500 text-base">Menampilkan transaksi dari <span
                                class="font-bold">"{{ formatDate($start_date, false) }}"</span></p>
                    @elseif ($end_date)
                        <p class="text-gray-500 text-base">Menampilkan transaksi sampai <span
                                class="font-bold">"{{ formatDate($end_date, false) }}"</span></p>
                    @elseif ($search)
                        <p class="text-gray-500 text-base">Menampilkan hasil pencarian <span
                                class="font-bold">"{{ $search }}"</span></p>
                    @elseif ($status)
                        <p class="text-gray-500 text-base">Menampilkan status transaksi <span
                                class="font-bold">"{{ $status }}"</span></p>
                    @endif
                    <a class="text-brand-green underline cursor-pointer font-semibold"
                        href="{{ route('user.view.history') }}">Atur ulang</a>
                </div>
            @endif

            <div class="mt-8 space-y-6">
                @if ($transactions->isNotEmpty())
                    @foreach ($transactions as $t)
                        <div class="bg-white border border-gray-200 rounded-[15px] p-4 transition">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                <div class="flex-1 mb-4 sm:mb-0">
                                    <div class="flex flex-col-reverse lg:flex-row lg:items-center gap-4 mb-2.5">
                                        <p class="font-semibold text-gray-800">ID Pesanan: #{{ $t['invoice_id'] }}</p>
                                        <div class="">
                                            @include('transaction-status', [
                                                'status' => $t['status'],
                                            ])
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ formatDate($t['created_at']) }}</p>
                                    <p class="text-md font-bold text-gray-900 mt-2">Rp {{ formatPrice($t['total']) }}</p>
                                </div>
                                <a href="{{ route('user.view.history.detail', ['transaction' => $t->invoice_id]) }}"
                                    class="w-full sm:w-auto text-center font-bold text-brand-green py-2 px-4 rounded-[10px] hover:bg-green-100 transition-all duration-300">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                    {{ $transactions->links() }}
                @else
                    <p class="text-center">Belum ada transaksi</p>
                @endif


            </div>
        </div>
    </section>
@endsection
