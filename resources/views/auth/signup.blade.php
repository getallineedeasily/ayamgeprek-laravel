@extends('layout')
@section('content')
    <main class="min-h-screen flex items-center justify-center p-8">
        <section class="bg-gray-50 p-8 rounded-[15px] w-full max-w-md">
            <a href="{{ route('view.landing') }}" class="">
                <span class="material-symbols-outlined pb-4">
                    arrow_back
                </span>
            </a>

            @session('error')
                <div class="bg-red-200 text-red-800 rounded-xl p-4 mb-8 font-semibold">
                    <p>{{ $value }}</p>
                </div>
            @endsession

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Ayam Geprek 77</h1>
                <p class="text-gray-500 mt-2 italic">Tiada dua menggoyang lidah</p>
            </div>

            <form id="signupForm" method="POST" action="{{ route('user.signup') }}">
                @csrf
                <div class="mb-4">
                    <label for="fullName" class="block text-gray-700 text-sm font-semibold mb-2">Nama</label>
                    <input type="text" id="fullName" name="name" placeholder="Nama"
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition"
                        required value="{{ old('name') }}">
                    @error('name')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@email.com"
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition"
                        required value="{{ old('email') }}">
                    @error('email')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition"
                        required>
                    @error('password')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 text-sm font-semibold mb-2">Nomor WA</label>
                    <input type="tel" id="phone" name="phone" placeholder="08123456789"
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition"
                        required value="{{ old('phone') }}">
                    @error('phone')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-gray-700 text-sm font-semibold mb-2">Alamat</label>
                    <textarea name="address" id=""
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition resize-none"
                        placeholder="Jln. Dr. Cipto Mangunkusumo 77" required rows="5">{{ old('address') }}</textarea>
                    @error('address')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full mt-8 bg-brand-orange text-white font-bold py-3 px-4 rounded-[15px] hover:bg-orange-600 transition-all duration-300 ease-in-out transform cursor-pointer block">
                    Daftar
                </button>

                <button type="button" disabled
                    class="loading w-full mt-8 bg-gray-300 text-black font-bold py-3 px-4 rounded-[15px]  transition-all duration-300 ease-in-out transform cursor-not-allowed hidden">
                    Daftar
                </button>
            </form>

            <p class="text-center text-sm text-gray-600 mt-8">
                Sudah punya akun?
                <a href="{{ route('view.login') }}" class="font-bold text-brand-green hover:underline">Masuk</a>
            </p>

        </section>
    </main>
@endsection
