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
            <h1 class="text-3xl font-bold text-gray-800">Pesan Makanan</h1>
            <p class="text-gray-600 mt-2">Pilih dan pesan menu favorit Anda di bawah ini</p>
        </div>
        <form method="POST" action="{{ route('txn.create') }}" class="bg-gray-50 p-8 rounded-[15px] mt-8">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach ($foods as $index => $food)
                    <div
                        class="bg-white rounded-[15px] overflow-hidden flex flex-col transform hover:-translate-y-2 transition-transform duration-300">
                        <img class="w-full h-48 object-cover" src="{{ '/storage/images/' . $food['image'] }}"
                            alt="{{ $food['name'] }}">
                        <div class="p-4 flex flex-col flex-grow text-center gap-2.5">
                            <h3 class="text-xl font-bold text-gray-800">{{ $food['name'] }}</h3>
                            <p class="mt-1 font-semibold text-brand-green">{{ 'IDR ' . $food['price'] }}</p>
                            <div class="mt-4 flex flex-col flex-grow justify-start">

                                <input type="hidden" name="order[{{ $index }}][food_id]"
                                    value="{{ $food['id'] }}">

                                <label for="{{ 'quantity-' . $food['id'] }}"
                                    class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                                <input type="text" id="{{ 'quantity-' . $food['id'] }}"
                                    name="order[{{ $index }}][quantity]" placeholder="0"
                                    class="w-full text-center py-2 px-3 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange transition">
                                @error("order.$index.quantity")
                                    <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-center">
                <button type="submit"
                    class="w-full md:w-1/2 mt-4 bg-brand-orange text-white font-bold py-2.5 px-4 rounded-[10px] hover:bg-orange-600 transition-all duration-300 flex items-center cursor-pointer justify-center">
                    Pesan
                </button>
            </div>
        </form>
    </section>
@endsection
