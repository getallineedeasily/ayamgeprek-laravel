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

            <div class="lg:col-span-2 bg-white p-6 rounded-[15px]">
                <h3 class="font-bold text-xl text-gray-800">Transaksi Terbaru</h3>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b">
                                <th class="py-2">ID Pesanan</th>
                                <th class="py-2">Pelanggan</th>
                                <th class="py-2">Total</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            <tr class="border-b">
                                <td class="py-3">#INV-12345</td>
                                <td class="py-3">Budi Santoso</td>
                                <td class="py-3">Rp 75.000</td>
                                <td class="py-3"><span
                                        class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-brand-green">Selesai</span>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="py-3">#INV-12344</td>
                                <td class="py-3">Ani Suryani</td>
                                <td class="py-3">Rp 120.000</td>
                                <td class="py-3"><span
                                        class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-brand-green">Selesai</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3">#INV-12343</td>
                                <td class="py-3">Citra Lestari</td>
                                <td class="py-3">Rp 55.000</td>
                                <td class="py-3"><span
                                        class="text-xs font-medium px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        </main>
    @endsection
