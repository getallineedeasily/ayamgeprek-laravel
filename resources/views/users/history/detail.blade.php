@php
    use App\Enums\TransactionStatus;
@endphp

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

        <div class="bg-gray-50 p-6 md:p-8 rounded-[15px] mt-8">
            @switch($transactions[0]->status)
                @case(TransactionStatus::PENDING_PAYMENT->value)
                    <div class="bg-orange-50 border-l-4 border-orange-800 text-orange-800 p-4 rounded-r-[10px]" role="alert">
                        <p class="font-bold">Pesanan Belum Dibayar</p>
                        <p>Pesanan ini belum dibayar. Silahkan lakukan pembayaran dan unggah bukti pembayaran.</p>
                    </div>
                @break

                @case(TransactionStatus::WAITING_CONFIRMATION->value)
                    <div class="bg-yellow-50 border-l-4 border-yellow-800 text-yellow-800 p-4 rounded-r-[10px]" role="alert">
                        <p class="font-bold">Pesanan Sedang Dikonfirmasi</p>
                        <p>Pesanan ini sedang dikonfirmasi. Mohon menunggu.</p>
                    </div>
                @break

                @case(TransactionStatus::CONFIRMED->value)
                    <div class="bg-blue-50 border-l-4 border-blue-800 text-blue-800 p-4 rounded-r-[10px]" role="alert">
                        <p class="font-bold">Pesanan Berhasil Dikonfirmasi</p>
                        <p>Pesanan sedang dibuat. Mohon menunggu.</p>
                    </div>
                @break

                @case(TransactionStatus::DELIVERED->value)
                    <div class="bg-green-50 border-l-4 border-green-800 text-green-800 p-4 rounded-r-[10px]" role="alert">
                        <p class="font-bold">Pesanan Berhasil</p>
                        <p>Pesanan ini sudah berhasil dibayar dan dikirim. Terima kasih!</p>
                    </div>
                @break

                @default
                    <div class="bg-red-50 border-l-4 border-red-800 text-red-800 p-4 rounded-r-[10px]" role="alert">
                        <p class="font-bold">Pesanan Dibatalkan</p>
                        <p>Pesanan ini belum dibayar dan sudah dibatalkan.</p>
                    </div>
            @endswitch

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">

                <div class="lg:col-span-1">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-3 text-gray-700">
                        <p><strong>Tanggal:</strong> {{ $transactions[0]->created_at }}</p>
                        <div><strong>Status:</strong>
                            @include('users.history.transaction-status', [
                                'status' => $transactions[0]['status'],
                            ])
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
                @switch($transactions[0]->status)
                    @case(TransactionStatus::PENDING_PAYMENT->value)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-28">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">Konfirmasi Pembayaran</h3>
                                <p class="text-sm text-gray-600 mt-1 mb-4">Unggah bukti transfer Anda di sini.</p>
                                <form
                                    action="{{ route('user.upload.payment.proof', ['transaction' => $transactions[0]->invoice_id]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH')
                                    <input type="file" name="payment_proof" id="payment_proof"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-[10px] file:border-0 file:text-sm file:font-semibold file:bg-orange-100 file:text-brand-orange hover:file:cursor-pointer cursor-pointer bg-white">
                                    @error('payment_proof')
                                        <span class="block mt-1.5 text-sm text-red-500 font-semibold">{{ $message }}</span>
                                    @enderror
                                    <button type="submit"
                                        class="w-full mt-6 bg-brand-green text-white font-bold py-2.5 px-4 rounded-[10px] hover:bg-green-700 transition-all duration-300 cursor-pointer">
                                        Konfirmasi Pembayaran
                                    </button>
                                </form>
                            </div>

                            <div>
                                <form method="POST"
                                    action="{{ route('user.cancel.order', ['transaction' => $transactions[0]->invoice_id]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <h3 class="text-lg font-bold text-gray-800">Batalkan Pesanan?</h3>
                                    <p class="text-sm text-gray-600 mt-1 mb-4">Pesanan tidak dapat dikembalikan jika sudah
                                        dibatalkan.
                                    </p>
                                    <button type="submit" onclick="return confirm('Yakin ingin membatalkan pesanan?')"
                                        class="w-full bg-red-100 text-brand-red font-bold py-2.5 px-4 rounded-[10px] hover:bg-red-200 transition-all duration-300 cursor-pointer">
                                        Batalkan Pesanan
                                    </button>
                                </form>
                            </div>
                        </div>
                    @break

                    @case(TransactionStatus::CANCELLED->value)
                    @break

                    @default
                        <a href="{{ route('user.view.payment.proof', ['transaction' => $transactions[0]->invoice_id]) }}"
                            class="inline-block w-full sm:w-auto text-center font-bold text-brand-green py-2 px-4 rounded-[10px] hover:bg-green-100 transition-all duration-300 underline">
                            Lihat Bukti Pembayaran
                        </a>
                @endswitch
            </div>
        </div>
    </section>
@endsection
