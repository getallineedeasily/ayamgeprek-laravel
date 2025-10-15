@extends('users.layout')
@section('user-content')
    <section class="flex-1 p-6 md:p-10">
        <div class="bg-gray-50 p-6 rounded-[15px]">
            <h1 class="text-3xl font-bold text-gray-800">Halo, {{ $userName }}!</h1>
            <p class="text-gray-600 mt-2">Mau pesan apa?</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mt-8">

            <div class="bg-gray-50 p-6 rounded-[15px]">
                <div class="flex items-center">
                    <div class="bg-orange-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-brand-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Pesanan Aktif</h2>
                </div>
                <p class="text-gray-500 mt-4">Anda tidak memiliki pesanan aktif</p>
                <a href="{{ route('user.view.order') }}"
                    class="w-full mt-6 bg-brand-orange text-white font-bold py-3 px-4 rounded-[15px] hover:bg-orange-600 transition-all duration-300 cursor-pointer block text-center">
                    Pesan Sekarang
                </a>
            </div>

            <div class="bg-gray-50 p-6 rounded-[15px]">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-brand-green" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Lihat Riwayat</h2>
                </div>
                <p class="text-gray-500 mt-4">Cek kembali semua transaksi dan pesanan yang sudah selesai</p>
                <a href="{{ route('user.view.history') }}"
                    class="w-full mt-6 bg-brand-green text-white font-bold py-3 px-4 rounded-[15px] hover:bg-green-800 transition-all duration-300 cursor-pointer block text-center">
                    Lihat Semua Riwayat
                </a>
            </div>

            <div class="bg-gray-50 p-6 rounded-[15px]">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Pengaturan Akun</h2>
                </div>
                <p class="text-gray-500 mt-4">Perbarui informasi pribadi, alamat, atau kata sandi Anda</p>
                <a href="{{ route('user.view.profile') }}"
                    class="w-full mt-6 bg-brand-red text-white font-bold py-3 px-4 rounded-[15px] hover:bg-red-800 transition-all duration-300 cursor-pointer block text-center">
                    Ubah Profil
                </a>
            </div>
        </div>
    </section>
@endsection