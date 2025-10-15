@extends('users.layout')
@section('user-content')
    <section class="flex-1 p-6 md:p-10">
        <div class="bg-gray-50 p-6 rounded-[15px]">
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h1>
            <p class="text-gray-600 mt-2">Lihat semua pesanan yang telah Anda selesaikan</p>
        </div>

        <div class="bg-gray-50 p-6 md:p-8 rounded-[15px] mt-8">
            <div class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-[15px] p-4 hover:bg-gray-50 transition">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="flex-1 mb-4 sm:mb-0">
                            <div class="flex items-center gap-4 mb-2">
                                <p class="font-semibold text-gray-800">ID Pesanan: #INV-12345</p>
                                <span
                                    class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-brand-green">Selesai</span>
                            </div>
                            <p class="text-sm text-gray-500">Tanggal: 14 Oktober 2025</p>
                            <p class="text-md font-bold text-gray-900 mt-2">Total: Rp 75.000</p>
                        </div>
                        <a href="#"
                            class="w-full sm:w-auto text-center font-bold text-brand-green py-2 px-4 rounded-[10px] hover:bg-green-100 transition-all duration-300">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-[15px] p-4 hover:bg-gray-50 transition">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="flex-1 mb-4 sm:mb-0">
                            <div class="flex items-center gap-4 mb-2">
                                <p class="font-semibold text-gray-800">ID Pesanan: #INV-12331</p>
                                <span
                                    class="text-xs font-medium px-3 py-1 rounded-full bg-red-100 text-brand-red">Dibatalkan</span>
                            </div>
                            <p class="text-sm text-gray-500">Tanggal: 11 Oktober 2025</p>
                            <p class="text-md font-bold text-gray-900 mt-2">Total: Rp 52.000</p>
                        </div>
                        <a href="#"
                            class="w-full sm:w-auto text-center font-bold text-brand-green py-2 px-4 rounded-[10px] hover:bg-green-100 transition-all duration-300">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-[15px] p-4 hover:bg-gray-50 transition">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="flex-1 mb-4 sm:mb-0">
                            <div class="flex items-center gap-4 mb-2">
                                <p class="font-semibold text-gray-800">ID Pesanan: #INV-12305</p>
                                <span
                                    class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-brand-green">Selesai</span>
                            </div>
                            <p class="text-sm text-gray-500">Tanggal: 09 Oktober 2025</p>
                            <p class="text-md font-bold text-gray-900 mt-2">Total: Rp 128.000</p>
                        </div>
                        <a href="#"
                            class="w-full sm:w-auto text-center font-bold text-brand-green py-2 px-4 rounded-[10px] hover:bg-green-100 transition-all duration-300">
                            Lihat Detail
                        </a>
                    </div>
                </div>

                <div class="flex justify-center items-center pt-6">
                    <nav class="flex items-center space-x-2" aria-label="Pagination">
                        <a href="#" class="text-gray-500 hover:text-gray-700 p-2 rounded-md hover:bg-gray-100">&laquo;</a>
                        <a href="#" class="bg-brand-orange text-white px-4 py-2 rounded-md">1</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100">2</a>
                        <a href="#" class="text-gray-600 hover:text-gray-900 px-4 py-2 rounded-md hover:bg-gray-100">3</a>
                        <a href="#" class="text-gray-500 hover:text-gray-700 p-2 rounded-md hover:bg-gray-100">&raquo;</a>
                    </nav>
                </div>

            </div>
        </div>
    </section>
@endsection