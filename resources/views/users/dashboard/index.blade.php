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
                    <div class="bg-orange-100 p-3 rounded-full flex justify-center items-center">
                        <span class="material-symbols-outlined text-brand-orange">
                            bolt
                        </span>
                    </div>
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Pesanan Aktif</h2>
                </div>
                @if ($hasActiveOrder)
                    <p class="text-gray-500 mt-4">Anda memiliki {{ $activeOrderCount }} pesanan aktif</p>
                    <a href="{{ route('user.view.order') }}"
                        class="w-full mt-6 bg-brand-orange text-white font-bold py-3 px-4 rounded-[15px] hover:bg-orange-600 transition-all duration-300 cursor-pointer block text-center">
                        Pesan Lagi
                    </a>
                @else
                    <p class="text-gray-500 mt-4">Anda tidak memiliki pesanan aktif</p>
                    <a href="{{ route('user.view.order') }}"
                        class="w-full mt-6 bg-brand-orange text-white font-bold py-3 px-4 rounded-[15px] hover:bg-orange-600 transition-all duration-300 cursor-pointer block text-center">
                        Pesan Sekarang
                    </a>
                @endif

            </div>

            <div class="bg-gray-50 p-6 rounded-[15px]">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full flex justify-center items-center">
                        <span class="material-symbols-outlined text-brand-green">
                            description
                        </span>
                    </div>
                    <h2 class="ml-4 text-xl font-semibold text-gray-800">Lihat Riwayat</h2>
                </div>
                <p class="text-gray-500 mt-4">Cek semua pesanan yang dibuat</p>
                <a href="{{ route('user.view.history') }}"
                    class="w-full mt-6 bg-brand-green text-white font-bold py-3 px-4 rounded-[15px] hover:bg-green-800 transition-all duration-300 cursor-pointer block text-center">
                    Lihat Semua Riwayat
                </a>
            </div>

            <div class="bg-gray-50 p-6 rounded-[15px]">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full flex justify-center items-center">
                        <span class="material-symbols-outlined text-brand-red">
                            settings
                        </span>
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
