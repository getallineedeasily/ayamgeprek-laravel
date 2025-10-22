@extends('admin.layout')
@section('admin-content')
    <section class="flex-1 p-6 md:p-10">
        <div class="bg-gray-50 p-6 rounded-[15px]">
            <h1 class="text-3xl font-bold text-gray-800">Halo, {{ $name }}!</h1>
            <p class="text-gray-600 mt-2">Semoga laris ya hari ini!</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
            <div class="bg-white p-6 rounded-[15px] flex flex-col items-start gap-4">
                <div class="bg-green-100 p-3 rounded-[10px] flex items-center">
                    <span class="material-symbols-outlined text-green-800">
                        paid
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-2xl font-bold text-gray-800">Rp 12.550.000</p>
                    <p class="text-gray-500">Total Pendapatan</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[15px] flex flex-col items-start gap-4">
                <div class="bg-orange-100 p-3 rounded-[10px] flex items-center">
                    <span class="material-symbols-outlined text-orange-800">
                        task
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-2xl font-bold text-gray-800">320</p>
                    <p class="text-gray-500">Jumlah Pesanan</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[15px] flex flex-col items-start gap-4">
                <div class="bg-yellow-100 p-3 rounded-[10px] flex items-center">
                    <span class="material-symbols-outlined text-yellow-800">
                        group
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-2xl font-bold text-gray-800">89</p>
                    <p class="text-gray-500">Jumlah Pelanggan</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[15px] flex flex-col items-start gap-4">
                <div class="bg-red-100 p-3 rounded-[10px] flex items-center">
                    <span class="material-symbols-outlined text-red-800">
                        stars_2
                    </span>
                </div>
                <div class="space-y-2">
                    <p class="text-xl font-bold text-gray-800">Nasi Goreng Spesial</p>
                    <p class="text-gray-500">Produk Terlaris</p>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 gap-6 mt-8">

            <div class="lg:col-span-2 bg-gray-50 p-6 rounded-[15px]">
                <h3 class="font-bold text-xl text-gray-800">Transaksi Terbaru</h3>
                <div class="mt-4 overflow-x-auto rounded-[15px]">
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
                        <tbody class="text-gray-600 bg-white divide-gray-200">
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
        </div>

        </main>
    @endsection
