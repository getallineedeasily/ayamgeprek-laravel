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

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 p-6 rounded-[15px]">
            <div class="">
                <h1 class="text-3xl font-bold text-gray-800">Kelola Menu</h1>
                <p class="text-gray-600 mt-2">Tambah, ubah, dan hapus menu</p>
            </div>

            <a href="{{ route('admin.create.food') }}"
                class="mt-4 sm:mt-0 w-full sm:w-auto bg-brand-green text-white font-bold py-2.5 px-6 rounded-[10px] hover:bg-green-700 flex items-center justify-center cursor-pointer">
                <span class="material-symbols-outlined mr-2">
                    add
                </span>
                Tambah Produk Baru
            </a>
        </div>

        <div class="mt-8 bg-gray-50 p-6 rounded-[15px]">

            <form action="{{ route('admin.view.food') }}" method="get">
                <div class="flex flex-col md:flex-row gap-4">
                    <input type="text" placeholder="Cari nama produk"
                        class="px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white flex-1"
                        name="search">
                    <button
                        class="bg-brand-orange shrink-0 text-white font-bold py-2 px-4 rounded-[10px] hover:bg-orange-600 cursor-pointer"
                        type="submit">
                        Cari
                    </button>
                </div>
            </form>

            @if ($search && $search !== null && $search !== '')
                <div class="mt-6 space-y-2">
                    <p class="text-gray-500 text-base">Menampilkan hasil pencarian <span
                            class="font-bold">"{{ $search }}"</span></p>
                    <a class="text-brand-green underline cursor-pointer font-semibold"
                        href="{{ route('admin.view.food') }}">Atur ulang</a>
                </div>
            @endif

            <div class="mt-6 bg-gray-50 rounded-[15px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-200">
                            <tr class="border-b-gray-200">
                                <th class="py-3 px-6 font-semibold">Foto</th>
                                <th class="py-3 px-6 font-semibold">Menu</th>
                                <th class="py-3 px-6 font-semibold">Harga</th>
                                <th class="py-3 px-6 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 divide-y divide-gray-200 bg-white">
                            @foreach ($foods as $food)
                                <tr>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-4">
                                            <img src="{{ '/storage/images/' . $food->image }}" alt="{{ $food->name }}"
                                                class="w-32 h-32 rounded-[10px] object-cover">
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-4">
                                            <span class="font-medium">{{ $food->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">Rp {{ $food->price }}</td>

                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.destroy.food', ['food' => $food->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <a href="{{ route('admin.edit.food', ['food' => $food->id]) }}"
                                                class="text-blue-600 hover:underline font-semibold mr-4 cursor-pointer">Ubah</a>
                                            <button type="submit" onclick="return confirm('Yakin mau hapus menu?')"
                                                class="text-brand-red hover:underline font-semibold cursor-pointer">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $foods->links() }}
        </div>
    </section>
@endsection
