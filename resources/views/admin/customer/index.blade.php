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
            <h1 class="text-3xl font-bold text-gray-800">Daftar Pelanggan</h1>
            <p class="text-gray-600 mt-2">Lihat semua akun pelanggan yang terdaftar</p>
        </div>

        <div class="mt-8 bg-gray-50 p-6 rounded-[15px]">
            <form action="{{ route('admin.view.customer') }}" method="get">
                <div class="flex flex-col md:flex-row gap-4">
                    <input type="text" placeholder="Cari nama atau email pelanggan"
                        class="px-4 py-2 border border-gray-300 rounded-[10px] focus:outline-none focus:ring-2 focus:ring-brand-orange bg-white flex-1"
                        name="search">
                    <button
                        class="bg-brand-orange shrink-0 text-white font-bold py-2 px-4 rounded-[10px] hover:bg-orange-600 cursor-pointer"
                        type="submit">
                        Cari
                    </button>
                    <button
                        class="bg-gray-300 shrink-0 font-bold py-2 px-4 rounded-[10px] cursor-not-allowed hidden loading"
                        type="button" disabled>
                        Cari
                    </button>
                </div>
            </form>

            @if ($search && $search !== null && $search !== '')
                <div class="mt-6 space-y-2">
                    <p class="text-gray-500 text-base">Menampilkan hasil pencarian <span
                            class="font-bold">"{{ $search }}"</span></p>
                    <a class="text-brand-green underline cursor-pointer font-semibold"
                        href="{{ route('admin.view.customer') }}">Atur ulang</a>
                </div>
            @endif

            <div class="mt-6 bg-white rounded-[15px] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-200">
                            <tr class="border-b-gray-200">
                                <th class="py-3 px-6 font-semibold">Pelanggan</th>
                                <th class="py-3 px-6 font-semibold">No WA</th>
                                <th class="py-3 px-6 font-semibold">Alamat</th>
                                <th class="py-3 px-6 font-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 divide-y divide-gray-200">
                            @foreach ($users as $user)
                                <tr>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-4">

                                            <div>
                                                <div class="font-medium">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">{{ $user->phone }}</td>
                                    <td class="py-4 px-6">{{ $user->address }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('admin.reset.customer.password', ['user' => $user->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                onclick="return confirm('Yakin mau atur ulang kata sandi?')"
                                                class="text-blue-600 hover:underline font-semibold  cursor-pointer">Atur
                                                ulang kata
                                                sandi</button>
                                            <button type="button" disabled
                                                class="text-gray-600 hover:underline font-semibold  cursor-not-allowed loading hidden">Atur
                                                ulang kata
                                                sandi</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

            {{ $users->links() }}
        </div>
    </section>
@endsection
