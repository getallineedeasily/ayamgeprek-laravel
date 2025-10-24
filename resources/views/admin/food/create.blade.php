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
            <div class="flex gap-3 items-center">
                <a href="{{ route('admin.view.food') }}" class="">
                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Tambah Menu</h1>
            </div>
            <p class="text-gray-600 mt-2">Isi detail menu yang akan ditambahkan</p>
        </div>

        <div class="mt-8 bg-gray-50 p-8 rounded-[15px] w-full mx-auto">
            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama
                        Produk</label>
                    <input type="text" id="name" name="name" placeholder="Contoh: Sate Ayam Madura" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange"
                        value="{{ old('name') }}">
                    @error('name')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-semibold text-gray-700 mb-1">Harga</label>
                    <div class="relative rounded-[10px] space-x-4">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" id="price" name="price" placeholder="25000" required
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange"
                            value="{{ old('price') }}">
                    </div>
                    @error('price')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Produk</label>
                    <div
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-[10px]">
                        <div class="space-y-1 text-center">

                            <svg class="mx-auto" xmlns="http://www.w3.org/2000/svg" height="80px" viewBox="0 -960 960 960"
                                width="80px" fill="#B7B7B7">
                                <path
                                    d="M480-480ZM180-120q-24 0-42-18t-18-42v-600q0-24 18-42t42-18h365v60H180v600h600v-365h60v365q0 24-18 42t-42 18H180Zm60-162h480L576-474 449-307l-94-124-115 149Zm453-323v-87h-88v-60h88v-88h60v88h87v60h-87v87h-60Z" />
                            </svg>

                            <div class="flex text-sm text-gray-600 justify-center">
                                <label for="image"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-brand-orange hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-orange">
                                    <span>Unggah gambar</span>
                                </label>
                                <input id="image" name="image" type="file" class="sr-only">
                            </div>
                            <p class="text-xs text-gray-500 mb-4">PNG, JPG, JPEG hingga 2MB</p>
                            <p class="text-xs text-gray-800" id="image-output"></p>
                        </div>
                    </div>
                    @error('image')
                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-center text-center pt-4">
                    <button type="submit"
                        class="w-full md:w-1/2 bg-brand-green text-white font-bold py-2.5 px-6 rounded-[10px] hover:bg-green-700 transition-all duration-300 cursor-pointer text-center block">
                        Simpan
                    </button>
                    <button type="button" disabled
                        class="w-full md:w-1/2 bg-gray-300 font-bold py-2.5 px-6 rounded-[10px] transition-all duration-300 cursor-not-allowed hidden loading text-center">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </section>
    <script>
        const input = document.getElementById('image');
        const imageOutput = document.getElementById('image-output');

        input.addEventListener('change', function() {
            if (input.files.length > 0) {
                imageOutput.textContent = input.files[0].name;
            } else {
                imageOutput.textContent = '';
            }
        });
    </script>
@endsection
