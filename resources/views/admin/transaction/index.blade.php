@extends('admin.layout')
@section('admin-content')
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
            <h1 class="text-3xl font-bold text-gray-800">Kelola Transaksi</h1>
            <p class="text-gray-600 mt-2">Lihat dan kelola semua transaksi yang masuk</p>
        </div>


        <div class="mt-8 bg-gray-50 p-6 rounded-[15px]">
            <form action="{{ route('admin.view.txn') }}" method="GET" class="space-y-5">
                <input type="text" placeholder="Cari ID pesanan atau nama"
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
                                class="font-bold">"{{ $start_date }}"</span> sampai <span
                                class="font-bold">"{{ $end_date }}"</span> </p>
                    @elseif ($start_date)
                        <p class="text-gray-500 text-base">Menampilkan transaksi dari <span
                                class="font-bold">"{{ $start_date }}"</span></p>
                    @elseif ($end_date)
                        <p class="text-gray-500 text-base">Menampilkan transaksi sampai <span
                                class="font-bold">"{{ $end_date }}"</span></p>
                    @elseif ($search)
                        <p class="text-gray-500 text-base">Menampilkan hasil pencarian <span
                                class="font-bold">"{{ $search }}"</span></p>
                    @elseif ($status)
                        <p class="text-gray-500 text-base">Menampilkan status transaksi <span
                                class="font-bold">"{{ $status }}"</span></p>
                    @endif
                    <a class="text-brand-green underline cursor-pointer font-semibold"
                        href="{{ route('admin.view.txn') }}">Atur ulang</a>
                </div>
            @endif

            <div class="mt-10 bg-white rounded-[15px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-200">
                            <tr class="border-b-gray-200">
                                <th class="py-3 px-6 font-semibold">ID Pesanan</th>
                                <th class="py-3 px-6 font-semibold">Pelanggan</th>
                                <th class="py-3 px-6 font-semibold">Tanggal</th>
                                <th class="py-3 px-6 font-semibold">Total</th>
                                <th class="py-3 px-6 font-semibold min-w-[200px]">Status</th>
                                <th class="py-3 px-6 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 divide-gray-200">
                            @foreach ($transactions as $t)
                                <tr>
                                    <td class="py-4 px-6 font-medium">#{{ $t->invoice_id }}</td>
                                    <td class="py-4 px-6">{{ $t->user->name }}</td>
                                    <td class="py-4 px-6">{{ $t->created_at }}</td>
                                    <td class="py-4 px-6">Rp {{ $t->total }}</td>
                                    <td class="py-4 px-6">
                                        @include('transaction-status', ['status' => $t->status])
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a href="{{ route('admin.edit.txn', ['transaction' => $t->invoice_id]) }}"
                                            class="text-brand-orange hover:underline font-semibold">Detail</a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            {{ $transactions->links() }}
        </div>

    </section>
@endsection
