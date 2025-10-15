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
                <h1 class="text-3xl font-bold text-gray-800">Ayam Geprek Kang Awan</h1>
                <p class="text-gray-500 mt-2 italic">Tiada dua menggoyang lidah</p>
            </div>

            <form id="loginForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" placeholder="contoh@email.com"
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition"
                        required autocomplete="email" value="john@example.com">
                    @error('email')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>


                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="••••••••"
                        class="w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition"
                        autocomplete="current-password" required value="halo1234">
                    @error('password')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" id="submitButton"
                    class="w-full mt-6 bg-brand-orange text-white font-bold py-3 px-4 rounded-[15px] hover:bg-orange-600 transition-all duration-300 ease-in-out transform cursor-pointer">
                    Masuk
                </button>
            </form>


            <div class="flex items-center my-6">
                <hr class="flex-grow border-gray-300">
                <span class="mx-4 text-gray-400 text-sm font-semibold">ATAU</span>
                <hr class="flex-grow border-gray-300">
            </div>


            <p class="text-center text-sm text-gray-600 mt-6">
                <span id="toggleText">Belum punya akun?</span>
                <a href="{{ route('view.signup') }}" id="toggleLink" class="font-bold text-brand-green hover:underline">Buat
                    akun</a>
            </p>

        </section>
    </main>

@endsection