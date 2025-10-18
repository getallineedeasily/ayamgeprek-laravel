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
            <div class="flex gap-3 items-center">
                <a href="{{ route('user.view.history') }}" class="">
                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Transaksi</h1>
            </div>
            <p class="text-gray-600 mt-2">ID Pesanan: {{ $transactions[0]->invoice_id }}</p>
        </div>

        @if ($transactions[0]->status === 'pending payment')
            <div class="bg-gray-50 p-6 md:p-8 rounded-[15px] mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-1">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                        <div class="space-y-3 text-gray-700">
                            <p><strong>Tanggal:</strong> {{ $transactions[0]->created_at }}</p>
                            <div><strong>Status:</strong> <span
                                    class="text-xs font-medium px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">{{ $transactions[0]->status }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Rincian Item</h2>
                        <div class="space-y-4">

                            @foreach ($transactions as $transaction)
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold">{{ $transaction->food->name }}</p>
                                        <p class="text-sm text-gray-500">Jumlah: {{ $transaction->quantity }}</p>
                                    </div>
                                    <p class="font-semibold">Rp {{ $transaction->price }}</p>
                                </div>
                            @endforeach

                        </div>
                        <hr class="my-4">
                        <div class="space-y-2">
                            <div class="flex justify-between font-bold text-lg">
                                <p class="text-2xl font-bold text-brand-green pt-2">Total:</p>
                                <p class="text-2xl font-bold text-brand-green pt-2">Rp
                                    {{ collect($transactions)->reduce(function ($prev, $curr) {return $prev + $curr->total;}, 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-300 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-28">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Konfirmasi Pembayaran</h3>
                            <p class="text-sm text-gray-600 mt-1 mb-4">Unggah bukti transfer Anda di sini.</p>
                            <form action="#" method="POST" enctype="multipart/form-data">
                                <input type="file" name="payment_proof" id="payment_proof"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-[10px] file:border-0 file:text-sm file:font-semibold file:bg-orange-100 file:text-brand-orange hover:file:cursor-pointer cursor-pointer bg-white">
                                <button type="submit"
                                    class="w-full mt-6 bg-brand-green text-white font-bold py-2.5 px-4 rounded-[10px] hover:bg-green-700 transition-all duration-300 cursor-pointer">
                                    Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>

                        <div>
                            <h3 class="text-lg font-bold text-gray-800">Batalkan Pesanan?</h3>
                            <p class="text-sm text-gray-600 mt-1 mb-4">Pesanan tidak dapat dikembalikan jika sudah
                                dibatalkan.
                            </p>
                            <button type="button"
                                class="w-full bg-red-100 text-brand-red font-bold py-2.5 px-4 rounded-[10px] hover:bg-red-200 transition-all duration-300 cursor-pointer">
                                Batalkan Pesanan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-50 p-6 md:p-8 rounded-[15px] mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-1">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                        <div class="space-y-3 text-gray-700">
                            <p><strong>Tanggal:</strong> 14 Oktober 2025</p>
                            <div><strong>Status:</strong> <span
                                    class="text-xs font-medium px-3 py-1 rounded-full bg-green-100 text-brand-green">Selesai</span>
                            </div>
                            <p class="text-2xl font-bold text-brand-green pt-2">Total: Rp 75.000</p>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Rincian Item</h2>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <p>Sate Ayam (x1)</p>
                                <p>Rp 25.000</p>
                            </div>
                            <div class="flex justify-between items-center">
                                <p>Nasi Goreng Spesial (x1)</p>
                                <p>Rp 30.000</p>
                            </div>
                            <div class="flex justify-between items-center">
                                <p>Es Teh Manis (x2)</p>
                                <p>Rp 10.000</p>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <p>Subtotal:</p>
                                <p>Rp 65.000</p>
                            </div>
                            <div class="flex justify-between">
                                <p>Pajak & Layanan (15%):</p>
                                <p>Rp 10.000</p>
                            </div>
                            <div class="flex justify-between font-bold text-lg">
                                <p>Total:</p>
                                <p>Rp 75.000</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="bg-green-50 border-l-4 border-brand-green text-brand-green p-4 rounded-r-[10px]"
                        role="alert">
                        <p class="font-bold">Pembayaran Berhasil</p>
                        <p>Pesanan ini sudah berhasil dibayar. Terima kasih!</p>
                    </div>
                </div>
            </div>
        @endif






    </section>
@endsection
