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
            <h1 class="text-3xl font-bold text-gray-800">Ubah Profil</h1>
            <p class="text-gray-600 mt-2">Perbarui informasi dan data pribadi Anda di sini</p>
        </div>

        <div class="bg-gray-50 p-8 rounded-[15px] mt-8">
            <form action="{{ route('user.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="flex flex-col md:flex-row items-start gap-8">
                    <div class="w-full grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
                            <input type="text" name="name" id="name" value="{{ $name }}"
                                class="mt-1 w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-brand-orange transition">
                            @error('name')
                                <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ $email }}"
                                class="mt-1 w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white  focus:outline-none focus:ring-2 focus:ring-brand-orange transition">
                            @error('email')
                                <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor WA</label>
                            <input type="tel" name="phone" id="phone" value="{{ $phone }}"
                                class="mt-1 w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white  focus:outline-none focus:ring-2 focus:ring-brand-orange transition">
                            @error('phone')
                                <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <textarea id="address" name="address" rows="3"
                                class="mt-1 w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white  focus:outline-none focus:ring-2 focus:ring-brand-orange transition resize-none">{{ $address }}</textarea>
                            @error('address')
                                <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <hr class="my-8 border-gray-200">

                <div>
                    <h3 class="text-lg font-semibold leading-6 text-gray-900">Ubah Kata Sandi</h3>
                    <p class="mt-1 text-sm text-gray-500">Biarkan kosong jika Anda tidak ingin mengubah kata sandi</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="old_password" class="block text-sm font-medium text-gray-700">Kata Sandi Saat
                                Ini</label>
                            <input type="password" name="old_password" id="old_password" placeholder="••••••••"
                                class="mt-1 w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white  focus:outline-none focus:ring-2 focus:ring-brand-orange transition">
                            @error('old_password')
                                <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                            <input type="password" name="password" id="password" placeholder="••••••••"
                                class="mt-1 w-full px-4 py-3 rounded-[10px] border border-gray-300 bg-white  focus:outline-none focus:ring-2 focus:ring-brand-orange transition">
                            @error('password')
                                <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-center gap-4">
                    <button type="submit"
                        class="bg-brand-orange text-white font-bold py-3 px-6 rounded-[15px] hover:bg-orange-600 transition-all duration-300 transform cursor-pointer w-full md:w-1/2">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </section>
@endsection
