@php
    use App\Enums\TransactionStatus;
@endphp

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

        <div class="bg-gray-50 p-6 rounded-[15px] mb-8">
            <div class="flex gap-3 items-center">
                <a href="{{ route('admin.view.txn') }}" class="">
                    <span class="material-symbols-outlined">
                        arrow_back
                    </span>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Detail Transaksi</h1>
            </div>
            <p class="text-gray-600 mt-2">ID Transaksi: <span
                    class="font-semibold text-gray-600">#{{ $transactions[0]->invoice_id }}</span>
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 order-2">
                <div class="bg-gray-50 p-6 rounded-[15px]">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Rincian Pesanan</h3>
                    <div class="space-y-4">

                        @foreach ($transactions as $transaction)
                            <div class="flex items-center justify-between pb-4">
                                <div class="flex items-center">
                                    <img src="{{ '/storage/images/' . $transaction->food->image }}"
                                        alt="{{ $transaction->food->name }}"
                                        class="w-16 h-16 rounded-[10px] object-cover mr-4">
                                    <div>
                                        <p class="font-bold">{{ $transaction->food->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->quantity }} x</p>
                                    </div>
                                </div>
                                <p class="font-semibold text-gray-700">Rp {{ formatPrice($transaction->price) }} </p>
                            </div>
                        @endforeach

                    </div>

                    <div class="mt-4 border-t-2 border-t-gray-200">
                        <div class="flex justify-between items-center font-bold text-lg text-gray-800 mt-4">
                            <p>Total Pembayaran</p>
                            <p>Rp
                                {{ formatPrice(collect($transactions)->reduce(function ($prev, $curr) {return $prev + $curr['total'];}, 0)) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="lg:col-span-1 order-1">
                <div class="bg-gray-50 p-6 rounded-[15px] space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Informasi
                            Pelanggan
                        </h3>
                        <div class="mt-4 space-y-2 text-gray-600">
                            <p><span class="font-semibold">Nama:</span> {{ $transactions[0]->user->name }}</p>
                            <p><span class="font-semibold">Email:</span> {{ $transactions[0]->user->email }}</p>
                            <p><span class="font-semibold">No. HP:</span> {{ $transactions[0]->user->phone }}</p>
                        </div>
                    </div>

                    @if (
                        $transactions[0]->status !== TransactionStatus::PENDING_PAYMENT->value &&
                            $transactions[0]->status !== TransactionStatus::CANCELLED->value)
                        <div class="border-t-2 border-t-gray-200 pt-6 space-y-4">
                            <h3 class="text-xl font-bold text-gray-800">Bukti Pembayaran</h3>

                            <div class="">
                                <a href="{{ route('admin.view.payment.proof', ['transaction' => $transactions[0]->invoice_id]) }}"
                                    class="text-brand-green underline font-medium cursor-pointer" target="_blank">Lihat
                                    bukti pembayaran</a>
                            </div>

                        </div>
                    @endif

                    <div class="border-t-2 border-t-gray-200 pt-6 space-y-4">
                        <h3 class="text-xl font-bold text-gray-800">Status Transaksi</h3>

                        <div class="">
                            @include('transaction-status', ['status' => $transactions[0]->status])
                        </div>

                    </div>

                    @if (
                        $transactions[0]->status !== TransactionStatus::DELIVERED->value &&
                            $transactions[0]->status !== TransactionStatus::CANCELLED->value &&
                            $transactions[0]->status !== TransactionStatus::PENDING_PAYMENT->value)
                        <form action="" method="post">
                            @csrf
                            @method('PATCH')
                            <div class="border-t-2 border-t-gray-200 pt-6 space-y-4">
                                <h3 class="text-xl font-bold text-gray-800">Ubah Status Transaksi</h3>

                                <div class="space-y-8">
                                    <select id="status" name="status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}"
                                                {{ $transactions[0]->status == $status->value ? 'selected' : '' }}>
                                                {{ $status->value }}</option>
                                        @endforeach
                                    </select>

                                    <div class="border-t-2 border-t-gray-200 pt-6">
                                        <button type="submit" onclick="return confirm('Yakin mau ubah status transaksi?')"
                                            class="w-full bg-brand-green text-white font-bold py-3 px-6 rounded-[10px] hover:bg-green-700 transition-all duration-300 flex items-center justify-center cursor-pointer">
                                            Simpan
                                        </button>
                                        <button type="button" disabled
                                            class="w-full bg-gray-300 font-bold py-3 px-6 rounded-[10px] transition-all duration-300 justify-center cursor-not-allowed hidden loading">
                                            Simpan
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
