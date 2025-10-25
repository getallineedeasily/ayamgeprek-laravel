<!DOCTYPE html>
<html class="scroll-smooth font-poppins" lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Ayam Geprek 77</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    @vite('resources/js/app.js')
    <style>
        @media print {
            .no-print {
                display: none;
            }

            .print-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }

            body {
                background-color: #ffffff;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="max-w-4xl mx-auto py-6 px-4 md:px-0 no-print">
        <div class="flex justify-between items-center">
            <div class="flex gap-1">
                <span class="material-symbols-outlined pr-2">
                    arrow_back
                </span>
                <a href="{{ route('admin.view.report') }}" class="text-base hover:underline text-gray-600 flex items-center">
                    Kembali ke Menu Laporan
                </a>
            </div>

            <button type="button" onclick="window.print()"
                class="bg-brand-orange text-white font-bold py-2 px-6 rounded-[10px] hover:bg-orange-600 transition-all duration-300 flex items-center cursor-pointer">
                <span class="material-symbols-outlined pr-2">
                    print
                </span>
                Cetak Laporan </button>
        </div>
    </div>

    <div class="print-container max-w-4xl mx-auto bg-white p-10 shadow-lg rounded-[10px] border border-gray-200">
        <header class="border-b-2 border-gray-100 pb-6 mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">Laporan Penjualan</h1>
                    <p class="text-gray-600">Ayam Geprek 77</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-700">Periode:</p>
                    <p class="text-gray-500">{{ formatDate($start_date, false) }} - {{ formatDate($end_date, false) }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-4">Dicetak pada: {{ formatDate($now, false) }}</p>
        </header>

        <section class="mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan Penjualan</h2>
            <div class="grid grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-[10px] p-4">
                    <p class="text-sm text-gray-500">Total Pendapatan</p>
                    <p class="text-2xl font-bold">Rp {{ formatPrice($totalRevenue) }}</p>
                </div>
                <div class="border border-gray-200 rounded-[10px] p-4">
                    <p class="text-sm text-gray-500">Total Transaksi</p>
                    <p class="text-2xl font-bold">{{ $totalSales }}</p>
                </div>
                <div class="border border-gray-200 rounded-[10px] p-4">
                    <p class="text-sm text-gray-500">Produk Terlaris</p>
                    <p class="text-xl font-bold">{{ $mostSoldFood }}</p>
                </div>
            </div>
        </section>

        <section>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Rincian Transaksi</h2>
            <div class="overflow-x-auto border rounded-[10px]">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-3 text-xs font-semibold text-gray-600 uppercase">Tanggal
                            </th>
                            <th class="p-3 text-xs font-semibold text-gray-600 uppercase">ID
                                Pesanan</th>
                            <th class="p-3 text-xs font-semibold text-gray-600 uppercase">Pelanggan
                            </th>
                            <th class="p-3 text-xs font-semibold text-gray-600 uppercase">Total</th>
                            <th class="p-3 text-xs font-semibold text-gray-600 uppercase">Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($transactions as $t)
                            <tr>
                                <td class="p-3 text-sm">{{ formatDate($t->created_at) }}</td>
                                <td class="p-3 text-sm font-medium">#{{ $t->invoice_id }}</td>
                                <td class="p-3 text-sm">{{ $t->user->name }}</td>
                                <td class="p-3 text-sm">Rp {{ formatPrice($t->total) }}</td>
                                <td class="p-3 text-sm"><span class="text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ $t->status }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="text-center mt-12 pt-6 border-t border-gray-100">
            <p class="text-xs text-gray-500">*** Akhir Laporan ***</p>
        </footer>
    </div>
</body>

</html>
