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
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h1>
            <p class="text-gray-600 mt-2">Lihat semua pesanan yang telah Anda selesaikan</p>
        </div>

        <div class="bg-gray-50 p-6 md:p-8 rounded-[15px] mt-8">
            <div class="space-y-6">
                @foreach ($transaction as $t)
                    <div class="bg-white border border-gray-200 rounded-[15px] p-4 transition">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div class="flex-1 mb-4 sm:mb-0">
                                <div class="flex items-center gap-4 mb-2">
                                    <p class="font-semibold text-gray-800">ID Pesanan: #{{ $t['invoice_id'] }}</p>
                                    @include('users.history.transaction-status', [
                                        'status' => $t['status'],
                                    ])
                                </div>
                                <p class="text-sm text-gray-500">{{ $t['created_at'] }}</p>
                                <p class="text-md font-bold text-gray-900 mt-2">Rp {{ $t['total'] }}</p>
                            </div>
                            <a href="{{ route('user.view.history.detail', ['transaction' => $t->invoice_id]) }}"
                                class="w-full sm:w-auto text-center font-bold text-brand-green py-2 px-4 rounded-[10px] hover:bg-green-100 transition-all duration-300">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                @endforeach

                {{ $transaction->links() }}


            </div>
        </div>
    </section>
@endsection
