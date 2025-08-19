<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Transaksi Hafnan Mart' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-gray-100 font-quicksand">
    {{ $slot }}

    @livewireScripts
    
    <script>
        window.addEventListener('hide-popup', () => {
            setTimeout(() => {
                const popup = document.getElementById('success-popup');
                if (popup) {
                    popup.style.display = 'none';
                }
            }, 2000);
        });

        const button = document.getElementById('userDropdownButton');
        const menu = document.getElementById('userDropdownMenu');
        const wrapper = document.getElementById('userDropdownWrapper');

        button.addEventListener('click', function (event) {
            event.stopPropagation();
            menu.classList.toggle('hidden');
        });

        // Klik di luar dropdown -> sembunyikan menu
        document.addEventListener('click', function (event) {
            if (!wrapper.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });


        document.addEventListener("DOMContentLoaded", function () {
            const menuToggleButton = document.getElementById("menuToggleButton");
            const menuCloseButton = document.getElementById("menuCloseButton");
            const sidebarOverlay = document.getElementById("sidebarOverlay");
            const sidebarMenu = document.getElementById("sidebarMenu");

            function openMenu() {
                // Tampilkan overlay
                sidebarOverlay.classList.remove("opacity-0", "pointer-events-none");
                sidebarOverlay.classList.add("opacity-100", "pointer-events-auto");

                // Slide sidebar masuk
                sidebarMenu.classList.remove("-translate-x-full");
                sidebarMenu.classList.add("translate-x-0");
            }

            function closeMenu() {
                // Sembunyikan overlay
                sidebarOverlay.classList.remove("opacity-100", "pointer-events-auto");
                sidebarOverlay.classList.add("opacity-0", "pointer-events-none");

                // Slide sidebar keluar
                sidebarMenu.classList.remove("translate-x-0");
                sidebarMenu.classList.add("-translate-x-full");
            }

            menuToggleButton.addEventListener("click", openMenu);
            menuCloseButton.addEventListener("click", closeMenu);
            sidebarOverlay.addEventListener("click", function (e) {
                // Pastikan hanya tertutup jika klik di luar sidebar
                if (e.target === sidebarOverlay) {
                    closeMenu();
                }
            });
        });
    </script>
</body>
</html>
