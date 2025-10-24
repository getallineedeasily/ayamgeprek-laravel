@extends('layout')
@section('content')
    <header class="p-4">
        <nav class="flex justify-center gap-4 max-sm:hidden" id="desktop-menu">
            <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                href="#menu">Menu</a>
            <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                href="#review">Ulasan</a>
            <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                href="#infos">Informasi</a>
            <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                href="{{ route('view.signup') }}">Daftar</a>
        </nav>
        <div class="burger-menu-wrapper absolute top-0 left-0 w-full p-4 block sm:hidden z-10">
            <span id="menu-icon" class="text-black cursor-pointer sm:hidden material-symbols-outlined"> menu
            </span>
            <nav id="burger-menu" class="hidden">
                <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                    href="#menu">Menu</a>
                <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                    href="#review">Ulasan</a>
                <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                    href="#infos">Informasi</a>
                <a class="text-center no-underline py-3 px-5 text-black rounded-[10px] transition-all duration-200 font-medium text-lg hover:bg-brand-red hover:text-white max-lg:text-base max-md:text-sm max-sm:w-full max-sm:text-white"
                    href="{{ route('view.signup') }}">Daftar</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero grid grid-cols-2 p-8 gap-4 max-sm:grid-cols-1 max-sm:p-4 max-sm:gap-5 fade-in">
            <aside class="hero-image order-2 sm:order-1 p-4 sm:p-0">
                <img class="rounded-[15px] transition-all duration-300 ease-in-out cursor-pointer object-cover hover:scale-105"
                    width="100%" height="100%" src="/storage/images/hero.jpg" alt="hero-image" />
            </aside>
            <div
                class="hero-text self-center text-center p-4 max-lg:p-0 max-sm:flex max-sm:flex-col max-sm:justify-center max-sm:items-center order-1 sm:order-2">
                <h1 class="text-5xl pb-2 max-lg:text-4xl max-md:text-4xl max-sm:pt-8 font-bold leading-normal">Ayam
                    Geprek 77</h1>
                <span class="block pb-7 text-xl italic max-lg:text-base max-md:text-base max-sm:w-fit max-sm:text-sm">Tiada
                    dua menggoyang lidah</span>
                <a class="text-2xl no-underline bg-brand-red py-3 px-6 text-white rounded-[15px] inline-block transition-all ease-in duration-300 font-semibold hover:bg-[#9d1e1e] max-lg:text-xl max-md:text-sm"
                    href="{{ route('view.login') }}">Pesan sekarang</a>
            </div>
        </section>
        <section id="menu" class="menu fade-in p-8">
            <h2 class="pb-4 max-md:text-xl text-2xl font-bold">Menu</h2>
            <div class="menu-card-wrapper grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-5">
                @foreach ($foods as $food)
                    <div
                        class="menu-card bg-gray-50 rounded-[15px] cursor-pointer transition-all duration-300 ease-in-out hover:scale-110">
                        <div class="menu-card-image h-[250px] w-auto max-[321px]:h-[200px]">
                            <img class="rounded-t-[15px] w-full h-full object-cover"
                                src="{{ '/storage/images/' . $food['image'] }}" alt="{{ $food['name'] }}" />
                        </div>
                        <div class="menu-card-text p-4 text-center">
                            <h3 class="pb-2 max-md:text-sm text-lg font-bold">{{ $food['name'] }}</h3>
                            <span class="max-md:text-[0.825rem]">{{ 'IDR ' . $food['price'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        <section id="review" class="review fade-in p-8 mb-8">
            <h2 class="pb-4 max-md:text-xl text-2xl font-bold">Ulasan</h2>
            <div
                class="review-card-wrapper grid grid-cols-[repeat(auto-fit,minmax(150px,1fr))] gap-10 max-sm:gap-6 max-[426px]:grid-cols-[repeat(auto-fit,minmax(200px,1fr))] max-[426px]:gap-5">
                <div
                    class="review-card bg-gray-50 rounded-[15px] p-4 flex flex-col justify-between cursor-pointer transition-all duration-300 ease-in-out hover:scale-110">
                    <h3 class="italic max-md:text-base font-bold text-xl">"Enak bangettt pokoknya langganan terus deh di
                        sini!"</h3>
                    <div class="review-detail self-end mt-5">
                        <div class="review-score text-right max-md:text-[0.65rem]">
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                        </div>
                        <span class="review-owner text-sm italic text-right block w-full max-md:text-xs">Ani —
                            Mahasiswa</span>
                    </div>
                </div>
                <div
                    class="review-card bg-gray-50 rounded-[15px] p-4 flex flex-col justify-between cursor-pointer transition-all duration-300 ease-in-out hover:scale-110">
                    <h3 class="italic max-md:text-base font-bold text-xl">"Juara! Pedesnya nampol, ayamnya gede dan
                        juicy!"</h3>
                    <div class="review-detail self-end mt-5">
                        <div class="review-score text-right max-md:text-[0.65rem]">
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                        </div>
                        <span class="review-owner text-sm italic text-right block w-full max-md:text-xs">Budi —
                            Karyawan</span>
                    </div>
                </div>
                <div
                    class="review-card bg-gray-50 rounded-[15px] p-4 flex flex-col justify-between cursor-pointer transition-all duration-300 ease-in-out hover:scale-110">
                    <h3 class="italic max-md:text-base font-bold text-xl">
                        "Enak, murce, pedes, auto repeat order kalo lagi males masak.."
                    </h3>
                    <div class="review-detail self-end mt-5">
                        <div class="review-score text-right max-md:text-[0.65rem]">
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                            <span class="material-symbols-outlined text-brand-orange">star</span>
                        </div>
                        <span class="review-owner text-sm italic text-right block w-full max-md:text-xs">Citra — Ibu
                            Rumah Tangga</span>
                    </div>
                </div>
            </div>
        </section>
        <section id="infos" class="order fade-in p-8 bg-brand-green pb-12 rounded-t-2xl">
            <h2 class="pb-4 max-md:text-xl text-2xl font-bold text-gray-50 ">Informasi</h2>
            <div class="order-wrapper grid grid-cols-2 gap-11 max-sm:grid-cols-1">
                <div class="order-information rounded-[15px] maps">
                    <iframe class="rounded-[15px]" id="iframe"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63395.53798849505!2d108.51283910247328!3d-6.742860872798948!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6ee2649e6e5bbb%3A0x70a07638a7fe12fe!2sCirebon%2C%20Cirebon%20City%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1758078355212!5m2!1sen!2sid"
                        width="100%" height="100%" style="border: 0" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <div class="order-information bg-gray-50 p-6 rounded-[15px]">
                    <h3 class="pb-3 max-md:text-base font-bold text-xl">Jam Buka</h3>
                    <p class="mb-3 max-md:text-sm max-[321px]:text-[0.675rem]">
                        <span class="align-middle material-symbols-outlined"> schedule </span> 08.00 -
                        20.00
                    </p>
                    <p class="mb-3 max-md:text-sm max-[321px]:text-[0.675rem]">
                        <span class="align-middle material-symbols-outlined"> location_on </span> Jln.
                        Dr. Cipto Mangunkusumo 77
                    </p>
                    <p class="mb-3 max-md:text-sm max-[321px]:text-[0.675rem]">
                        <span class="align-middle material-symbols-outlined"> call </span>
                        <a class="no-underline text-black hover:underline" href="https://wa.me/+62812345678"
                            target="_blank">0812345678</a>
                    </p>
                </div>
            </div>
        </section>
    </main>
    <footer class="bg-brand-red p-4 text-sm text-center font-semibold text-white max-md:text-[0.675rem]">
        <div class="bottom-nav mb-5">
            <p class="mb-2">Ayam Geprek 77</p>
            <nav>
                <a class="no-underline text-white px-2 hover:underline" href="#menu">Menu</a>
                <a class="no-underline text-white px-2 hover:underline" href="#review">Ulasan</a>
                <a class="no-underline text-white px-2 hover:underline" href="#infos">Informasi</a>
                <a class="no-underline text-white px-2 hover:underline" href="{{ route('view.signup') }}">Daftar</a>
            </nav>
        </div>
        <span>&copy; 2025 by ricky</span>
    </footer>
    <script>
        const burgerIcon = document.getElementById("menu-icon");
        const burgerMenu = document.getElementById("burger-menu");
        const burgerMenuWrapper = document.getElementsByClassName(
            "burger-menu-wrapper"
        )[0];
        const faders = document.querySelectorAll(".fade-in");

        function toggleActive() {
            burgerMenu.classList.toggle("active");
            burgerMenuWrapper.classList.toggle("active");
            burgerIcon.classList.toggle("active");
        }

        function checkVisibility() {
            const triggerBottom = window.innerHeight * 0.85;

            faders.forEach((el) => {
                const rect = el.getBoundingClientRect();
                if (rect.top < triggerBottom) {
                    el.classList.add("show");
                }
            });
        }

        burgerIcon.addEventListener("click", toggleActive);
        window.addEventListener("scroll", checkVisibility);
        window.addEventListener("load", checkVisibility);
    </script>
@endsection
