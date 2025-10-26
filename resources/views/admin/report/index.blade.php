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
            <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
            <p class="text-gray-600 mt-2">Lihat dan cetak laporan penjualan</p>
        </div>

        <div class="grid grid-cols-1 mt-8 bg-gray-50 p-6 rounded-[15px]">
            <form action="{{ route('admin.print.report') }}" method="GET" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="start_date" class="text-gray-500 block">Tanggal Awal</label>
                        <input type="date" name="start_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange text-gray-500 bg-white block">
                        @error('start_date')
                            <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="end_date" class="text-gray-500 block">Tanggal Akhir</label>
                        <input type="date" name="end_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange text-gray-500 bg-white block">
                        @error('end_date')
                            <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="submit"
                        class="bg-brand-orange text-white font-bold py-2 px-4 rounded-[10px] hover:bg-orange-600 cursor-pointer block w-full md:w-1/2">
                        Lihat
                    </button>
                    <button type="button" disabled
                        class="bg-gray-300 font-bold py-2 px-4 rounded-[10px] cursor-not-allowed hidden loading w-full md:w-1/2">
                        Lihat
                    </button>
                </div>
            </form>

        </div>
    </section>
@endsection
