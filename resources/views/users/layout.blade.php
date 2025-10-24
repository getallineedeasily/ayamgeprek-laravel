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
</head>

<body class="max-w-[1440px] mx-auto bg-brand-yellow">
    <main class="relative min-h-screen md:flex">

        <div class="md:hidden flex gap-2 justify-between items-center p-4 bg-gray-50 rounded-b-[15px]">
            <h1 class="text-xl font-bold text-brand-orange">Ayam Geprek 77</h1>
            <button type="button" id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>
        </div>


        <aside id="sidebar"
            class="sidebar bg-gray-50 text-gray-800 w-64 space-y-6 py-7 px-2 fixed inset-y-0 left-0 transform -translate-x-full md:translate-x-0 z-20 md:rounded-r-[15px] min-h-dvh h-full">

            <div class="px-4 text-center">
                <a href="/" class="text-brand-orange text-2xl font-extrabold">Ayam Geprek 77</a>
            </div>

            <nav>
                <a href="{{ route('user.view.home') }}"
                    class="flex items-center py-2.5 px-4 rounded-[10px] transition duration-200 {{ Request::is('user/home*') ? 'bg-brand-orange text-white' : 'text-black hover:bg-gray-100' }}">
                    <span class="material-symbols-outlined pr-2">
                        home
                    </span>
                    Beranda
                </a>
                <a href="{{ route('user.view.order') }}"
                    class="flex items-center py-2.5 px-4 rounded-[10px] transition duration-200 {{ Request::is('user/order*') ? 'bg-brand-orange text-white' : 'text-black hover:bg-gray-100' }}">
                    <span class="material-symbols-outlined pr-2">
                        shopping_cart
                    </span>
                    Pesan
                </a>
                <a href="{{ route('user.view.history') }}"
                    class="flex items-center py-2.5 px-4 rounded-[10px] transition duration-200 {{ Request::is('user/history*') ? 'bg-brand-orange text-white' : 'text-black hover:bg-gray-100' }}">
                    <span class="material-symbols-outlined pr-2">
                        history
                    </span>
                    Riwayat
                </a>
                <a href="{{ route('user.view.profile') }}"
                    class="flex items-center py-2.5 px-4 rounded-[10px] transition duration-200 {{ Request::is('user/profile*') ? 'bg-brand-orange text-white' : 'text-black hover:bg-gray-100' }}">
                    <span class="material-symbols-outlined pr-2">
                        person
                    </span>
                    Profil
                </a>
            </nav>

            <div class="absolute bottom-0 w-full left-0 px-2 pb-4">
                <form action="{{ route('user.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full py-2.5 px-4 rounded-[10px] transition duration-200 text-red-500 hover:bg-gray-100 cursor-pointer font-semibold">
                        <span class="material-symbols-outlined pr-2">
                            logout
                        </span>
                        Keluar
                    </button>
                    <button type="button" disabled
                        class="w-full py-2.5 px-4 rounded-[10px] transition duration-200 text-gray-700 hover:bg-gray-100 cursor-not-allowed font-semibold loading hidden">
                        <div class="flex items-center">
                            <span class="material-symbols-outlined pr-2">
                                logout
                            </span>
                            <span>Keluar</span>
                        </div>
                    </button>
                </form>
            </div>
        </aside>

        <section class="w-full md:ml-64">
            @yield('user-content')
        </section>

    </main>
    <footer class="md:ml-64 flex justify-center pb-6 px-6 md:px-10">
         <span class="bg-gray-50 p-3 rounded-xl w-full text-center text-sm font-medium">&copy; 2025 by ricky</span>
    </footer>
    <script>
        const btn = document.getElementById('mobile-menu-button');
        const sidebar = document.getElementById('sidebar');

        btn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>

</html>
