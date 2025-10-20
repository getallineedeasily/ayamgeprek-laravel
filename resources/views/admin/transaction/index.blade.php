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
            <h1 class="text-3xl font-bold text-gray-800">Kelola Transaksi</h1>
            <p class="text-gray-600 mt-2">Lihat dan kelola semua transaksi yang masuk</p>
        </div>


        <div class="mt-8 bg-gray-50 p-6 rounded-[15px]">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <input type="text" placeholder="Cari ID Pesanan atau Nama..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white">
                <select
                    class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>
                <input type="date"
                    class="w-full px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange text-gray-500  bg-white">
                <button
                    class="w-full bg-brand-orange text-white font-bold py-2 px-4 rounded-[10px] hover:bg-orange-600 transition-all duration-300">
                    Filter
                </button>
            </div>

            <div class="mt-6 bg-white rounded-[15px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-200">
                            <tr class="border-b-gray-200">
                                <th class="py-3 px-6 font-semibold">ID Pesanan</th>
                                <th class="py-3 px-6 font-semibold">Pelanggan</th>
                                <th class="py-3 px-6 font-semibold">Tanggal</th>
                                <th class="py-3 px-6 font-semibold">Total</th>
                                <th class="py-3 px-6 font-semibold min-w-[200px]">Status</th>
                                <th class="py-3 px-6 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 divide-gray-200">
                            @foreach ($transactions as $t)
                                <tr>
                                    <td class="py-4 px-6 font-medium">#{{ $t->invoice_id }}</td>
                                    <td class="py-4 px-6">{{ $t->user->name }}</td>
                                    <td class="py-4 px-6">{{ $t->created_at }}</td>
                                    <td class="py-4 px-6">Rp {{ $t->total }}</td>
                                    <td class="py-4 px-6">
                                        @include('transaction-status', ['status' => $t->status])
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <a href="#" class="text-brand-orange hover:underline font-semibold">Detail</a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            {{ $transactions->links() }}
        </div>

    </section>
@endsection
