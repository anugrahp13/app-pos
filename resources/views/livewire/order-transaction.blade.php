<div class="">
    <!-- Top Header -->
    <Header class="bg-white border-b shadow-sm sticky top-0 z-40">
        <div class="container mx-auto px-4 py-2 lg:max-w-7xl flex items-center justify-between">
            {{-- Logo --}}
            <div class="flex items-center order-2 md:order-1 gap-2">
                <picture>
                    <img src="{{ asset('images/hafnanmart.webp') }}" alt="Hafnan Mart Logo"
                        class="lazyload rounded-full object-cover max-w-full w-8 h-8 dark:brightness-75 lazyloaded" />
                </picture>
                <span class="font-bold text-base">Hafnan Mart</span>
            </div>

            <nav class="flex flex-row gap-4 items-center order-1">
                <!-- Tombol Hamburger -->
                <svg id="menuToggleButton" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="text-slate-800 dark:text-white text-lg block md:hidden h-8 w-8 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                </svg>

                <!-- Menu Desktop -->
                <div class="hidden md:inline-flex gap-4">
                    <a href="{{ route('filament.admin.resources.products.index') }}" class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-slate-800">Product</a>
                    <a href="{{ route('filament.admin.resources.transactions.index') }}" class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-slate-800">Riwayat</a>
                    <a href="#" class="px-3 py-2 text-sm font-bold text-gray-600 hover:text-slate-800">Laporan</a>
                </div>
            </nav>

            <!-- Dropdown User -->
            <div class="relative flex items-center gap-2 order-2" id="userDropdownWrapper">
                <button id="userDropdownButton"
                    class="p-0 w-8 h-8 rounded-full overflow-hidden bg-gray-100 hover:bg-gray-200 focus:outline-none flex items-center justify-center">
                    <picture class="w-full h-full">
                        <img 
                            src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('storage/avatars/default-avatar.png') }}" 
                            alt="{{ Auth::user()->name }}" 
                            class="w-full h-full object-cover">
                    </picture>
                </button>

                <div id="userDropdownMenu"
                    class="hidden absolute right-0 top-12 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                    <div class="px-4 py-2 flex items-center text-sm text-gray-700 gap-2">
                        <picture>
                            <img 
                                src="{{ Auth::user()->image ? asset('storage/' . Auth::user()->image) : asset('storage/avatars/default-avatar.png') }}" 
                                alt="{{ Auth::user()->name }}" 
                                class="w-6 h-6 object-cover">
                        </picture>
                        <span class="font-semibold">{{ Auth::user()->name }}</span>
                    </div>
                    <a href="{{ url('/admin/users/' . Auth::user()->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                </div>
            </div>
        </div>
    </Header>

    <!-- Sidebar Overlay (parent) + Sidebar Menu -->
    <div id="sidebarOverlay"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden">
        <!-- Sidebar Menu (child) -->
        <div id="sidebarMenu"
            class="transform -translate-x-full transition-transform duration-300 ease-in-out p-4 bg-white text-gray-100 text-xl text-center w-96 h-screen overflow-y-auto font-semibold rounded-tr-xl rounded-br-lg">
            <div class="px-3 mt-1 flex items-center justify-between">
                <a href="#" class="font-bold text-lg text-primary py-3 dark:text-white">
                    <picture>
                        <img src="{{ asset('images/hafnanmart.webp') }}" alt="Hafnan Mart Logo" class="rounded-full w-9 h-9" />
                    </picture>
                </a>
                <button id="menuCloseButton">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-8 w-8 scale-75 text-slate-800 dark:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <hr class="my-2 text-gray-600" />
            <nav class="my-4 text-left">
                <a href="{{ route('filament.admin.resources.products.index') }}" class="block p-2.5 text-[15px] rounded-lg px-4 font-bold hover:bg-slate-400/25 text-slate-800 hover:text-primary dark:text-white dark:hover:text-primary">Product</a>
                <a href="{{ route('filament.admin.resources.transactions.index') }}" class="block p-2.5 text-[15px] rounded-lg px-4 font-bold hover:bg-slate-400/25 text-slate-800 hover:text-primary dark:text-white dark:hover:text-primary">Riwayat</a>
                <a href="#" class="block p-2.5 text-[15px] rounded-lg px-4 font-bold hover:bg-slate-400/25 text-slate-800 hover:text-primary dark:text-white dark:hover:text-primary">Laporan</a>
            </nav>
            <hr class="my-2 text-gray-600" />
            <div class="p-2.5 px-4 space-y-4">
                <p class="text-left font-semibold text-sm text-slate-800 dark:text-white">
                    © 2025 <span class="font-bold dark:text-white">Hafnan Store</span>. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <div class="container mx-auto p-4 lg:max-w-7xl flex flex-col md:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex flex-wrap">
            <!-- Produk -->
            <div class="lg:w-2/3 w-full">
                <div class="grid gap-5">
                    <!-- Search -->
                    <div class="w-full relative">
                        <input
                            type="text"
                            wire:model="search"
                            wire:keydown.debounce.100ms="$set('search', $event.target.value)"
                            placeholder="Cari nama produk . . ."
                            class="w-full px-4 py-3 text-base border rounded-xl shadow-sm text-gray-700 dark:text-white dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none"
                        />
                    
                        @if($search)
                            <button
                                type="button"
                                wire:click="$set('search', '')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-red-500 focus:outline-none"
                                title="Clear"
                            >
                                <span class="bg-red-500 font-semibold text-white text-sm flex items-center justify-center px-2 py-1 rounded-lg">Hapus</span>
                            </button>
                        @endif
                    </div>                    

                    <!-- Kategori Produk (All tetap diam, kategori lain bisa scroll) -->
                    <div class="hidden md:flex gap-3 items-stretch w-full overflow-hidden">
                        <!-- Tombol All -->
                        <div class="shrink-0 pb-2">
                            <button
                                wire:click="selectCategory(null)"
                                class="{{ is_null($selectedCategory) ? 'bg-blue-500 text-white' : 'bg-white text-gray-800' }} w-28 h-full px-4 py-3 rounded-xl shadow-md flex flex-col items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3.75 3.75h5.5v5.5h-5.5v-5.5zm0 11h5.5v5.5h-5.5v-5.5zm11 0h5.5v5.5h-5.5v-5.5zm0-11h5.5v5.5h-5.5v-5.5z"/>
                                </svg>
                                <span class="text-sm font-semibold text-center">All</span>
                            </button>
                        </div>

                        <!-- Scrollable Kategori -->
                        <div class="overflow-x-auto w-full">
                            <div class="flex gap-3 pb-2">
                                @foreach($categories as $category)
                                <button
                                    wire:click="selectCategory({{ $category->id }})"
                                    class="{{ $selectedCategory == $category->id ? 'bg-blue-500 text-white' : 'bg-white text-gray-800' }} w-28 flex-shrink-0 px-4 py-3 rounded-xl shadow-md flex flex-col items-center justify-center gap-1">
                                    <img src="{{ $category->image ? asset('storage/' . $category->image) : 'https://via.placeholder.com/60' }}"
                                        class="h-10 w-10 object-cover" />
                                    <span class="text-sm font-semibold text-center">{{ $category->name }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Produk -->
                    @if ($products->count())
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-5">
                            @foreach($products as $product)
                                <div
                                    @if ($product->stock > 0)
                                        wire:click="addToCart({{ $product->id }})"
                                        class="relative shadow-md p-6 rounded-xl cursor-pointer bg-white dark:bg-slate-800 hover:shadow-lg dark:hover:outline dark:hover:outline-slate-600 dark:hover:outline-1 grid gap-1"
                                    @else
                                        class="relative shadow-md p-6 rounded-xl bg-gray-200 dark:bg-gray-700 opacity-60 cursor-not-allowed grid gap-1"
                                    @endif
                                >
                                    <!-- Label Harga Hijau -->
                                    <div class="absolute top-4 right-0 bg-green-500 text-white text-sm font-bold px-2 py-1 rounded-l-lg z-10">
                                        Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                    </div>
                                    @if ($product->stock == 0)
                                        <div class="absolute top-4 left-0 bg-red-500 text-white text-sm font-bold px-2 py-1 rounded-r-lg z-10">
                                            Stok Habis
                                        </div>
                                    @endif

                                    <!-- Gambar Produk -->
                                    <div class="mb-3 inline-block">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-xl overflow-hidden">
                                            <picture>
                                                <img src="{{ $product->image ? asset('storage/' .$product->image) : 'storage/products/default-image.png' }}"
                                                    class="lazyload w-full rounded-xl object-cover max-w-full hover:brightness-90 dark:brightness-100 transition-transform hover:scale-110" />
                                            </picture>
                                        </div>
                                    </div>

                                    <!-- Nama Produk -->
                                    <div class="text-xl lg:text-sm font-bold tracking-tight line-clamp-2 mb-3 min-h-[3rem]">
                                        {{ $product->name }}
                                    </div>

                                    <!-- Info Tambahan -->
                                    <div class="flex justify-between items-center">
                                        <span class="flex flex-col text-xs text-gray-600">
                                            Terjual
                                            <span>{{ $product->sold }}</span>
                                        </span>
                                        {{-- Harga juga ditampilkan kecil di bawah jika ingin --}}
                                        <span class="flex items-center justify-center text-sm font-semibold gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                                            </svg>                                          
                                            {{ $product->stock }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            {{-- Bagian kiri --}}
                            <div class="text-sm text-gray-600">
                                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                            </div>
                        
                            {{-- Bagian kanan --}}
                            <div class="flex justify-center items-center gap-2">
                                {{-- Nomor halaman --}}
                                @php
                                    $currentPage = $products->currentPage();
                                    $lastPage = $products->lastPage();
                                @endphp

                                {{-- Tombol Sebelumnya --}}
                                <button 
                                    wire:click="previousPage" 
                                    @if ($currentPage === 1) disabled @endif
                                    class="px-3 py-1 rounded-lg 
                                        {{ $currentPage === 1 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-200 hover:bg-blue-200 text-gray-700' }}">
                                    ←
                                </button>

                                {{-- Halaman pertama --}}
                                @if ($currentPage > 3)
                                    <button wire:click="setCustomPage(1)" class="px-3 py-1 bg-gray-200 hover:bg-blue-200 text-gray-700 rounded">1</button>
                                    @if ($currentPage > 4)
                                        <span class="px-2">...</span>
                                    @endif
                                @endif

                                {{-- Window halaman sekitar currentPage --}}
                                @for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++)
                                    @if ($i == $currentPage)
                                        <button class="px-3 py-1 bg-green-500 text-white rounded-lg">{{ $i }}</button>
                                    @else
                                        <button wire:click="setCustomPage({{ $i }})" class="px-3 py-1 bg-gray-200 hover:bg-blue-200 text-gray-700 rounded-lg">{{ $i }}</button>
                                    @endif
                                @endfor

                                {{-- Halaman terakhir --}}
                                @if ($currentPage < $lastPage - 2)
                                    @if ($currentPage < $lastPage - 3)
                                        <span class="px-2">...</span>
                                    @endif
                                    <button wire:click="setCustomPage({{ $lastPage }})" class="px-3 py-1 bg-gray-200 hover:bg-blue-200 text-gray-700 rounded-lg">{{ $lastPage }}</button>
                                @endif

                                {{-- Tombol Selanjutnya --}}
                                <button 
                                    wire:click="nextPage" 
                                    @if ($currentPage === $lastPage) disabled @endif
                                    class="px-3 py-1 rounded-lg 
                                        {{ $currentPage === $lastPage ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-gray-200 hover:bg-blue-200 text-gray-700' }}">
                                    →
                                </button>
                            </div>                            
                        </div>                                               
                    @else
                        <div class="flex flex-col justify-center items-center min-h-[200px] text-gray-500 dark:text-gray-300 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2a4 4 0 018 0v2m-4 4a4 4 0 100-8 4 4 0 000 8zM3 9a4 4 0 118 0 4 4 0 01-8 0z" />
                            </svg>
                            <span class="text-lg font-semibold">Produk tidak ada.</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Transaksi -->
            <div class="lg:w-1/3 w-full">
                <div class="ml-0 mt-8 lg:mt-0 lg:ml-4 lg:sticky lg:top-16">
                    <div class="bg-white rounded-xl shadow-md p-4 space-y-4">
                        {{-- Judul --}}
                        <h2 class="text-center text-lg font-semibold">Keranjang</h2>

                        {{-- Search Customer --}}
                        <div class="mb-4 relative">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer</label>
                        
                            <input 
                                type="text"
                                wire:model="customerSearch"
                                wire:keydown.debounce.200ms="$set('customerSearch', $event.target.value)"
                                placeholder="Cari atau ketik nama customer..."
                                class="w-full px-4 py-2 border rounded-xl shadow-sm text-gray-700 dark:text-white dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none"
                            />
                        
                            {{-- Dropdown hasil pencarian --}}
                            @if($customerSearch && is_null($selectedCustomer))
                                <ul class="absolute z-10 w-full border rounded-xl mt-1 bg-white shadow-md max-h-40 overflow-auto">
                                    @forelse($this->filteredCustomers as $customer)
                                        <li 
                                            wire:click="selectCustomer({{ $customer->id }})"
                                            class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
                                        >
                                            {{ $customer->name }}
                                        </li>
                                    @empty
                                        <li class="px-3 py-2 text-gray-500 italic">Customer tidak ditemukan.</li>
                                    @endforelse
                                </ul>
                            @endif
                        
                            {{-- Jika customer dipilih --}}
                            @if($selectedCustomer)
                                <div class="text-green-600 text-sm mt-1 flex justify-between items-center">
                                    <span>Customer terpilih: <strong>{{ $selectedCustomer->name }}</strong></span>
                                    <button wire:click="resetCustomer" class="ml-2 text-red-600 text-xs hover:underline">
                                        Ganti
                                    </button>
                                </div>
                            @elseif($customerSearch)
                                <p class="text-gray-600 text-sm mt-1">Nama customer sementara: <strong>{{ $customerSearch }}</strong></p>
                            @endif
                        </div>                                           

                        {{-- Daftar Item --}}
                        @forelse($cart as $index => $item)
                        <div class="flex items-center justify-between border-b pb-2 mb-2 space-x-4">
                            <img src="{{ $item['image'] ? asset('storage/'.$item['image']) : asset('storage/products/default-image.png') }}"
                                class="w-10 h-10 object-cover rounded" />

                            <div class="flex-1 space-y-1">
                                <p class="font-semibold text-sm">{{ $item['name'] }}</p>

                                <div class="flex gap-4">
                                    {{-- Editable Price --}}
                                    <div class="flex  items-center space-x-2">
                                        <span class="text-gray-600">Rp</span>
                                        <input
                                            step="1"
                                            type="number"
                                            wire:model.lazy="cart.{{ $index }}.price"
                                            class="w-20 px-3 py-1 border rounded-xl text-sm"
                                        />
                                    </div>

                                    {{-- Editable Quantity --}}
                                    <div class="flex bg-gray-200 rounded-xl items-center">
                                        <button
                                            wire:click="decrementQty({{ $index }})"
                                            class="ml-1 px-2">–</button>
                                        <input
                                            min="1"
                                            max="{{ $item['stock'] ?? 999 }}"
                                            wire:model.lazy="cart.{{ $index }}.quantity"
                                            class="w-8 text-center text-sm bg-gray-200"
                                            min="1"
                                        />
                                        <button
                                            wire:click="incrementQty({{ $index }})"
                                            class="mr-1 px-2">+</button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-1">
                                <button
                                    wire:click="removeFromCart({{ $item['id'] }})"
                                    class="bg-red-500 text-white text-xs flex items-center justify-center px-2 py-1 rounded-lg">
                                    Hapus
                                </button>
                                <p class="text-sm font-semibold">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @empty
                            <p class="text-gray-500">Belum ada item dalam keranjang.</p>
                        @endforelse

                        {{-- Total --}}
                        <div class="flex justify-between font-bold border-t pt-2">
                            <span>Total</span>
                            <span class="text-lg">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>

                    
                        {{-- Input Uang Bayar --}}
                        <div class="mt-4">
                            <label for="paidAmount" class="block text-sm font-medium text-gray-700 mb-1">
                                Uang Dibayar
                            </label>
                            @php
                                // Tombol Uang Pas hanya disabled kalau keranjang kosong
                                $disableExact = empty($cart);
                            @endphp
                            <div class="flex items-center justify-center gap-2">
                                <input
                                type="number"
                                id="paidAmount"
                                wire:model.lazy="paidAmount"
                                min="0"
                                class="w-full px-4 py-2 border rounded-xl shadow-sm text-gray-700 dark:text-white dark:bg-gray-800 focus:bg-white dark:focus:bg-gray-700 focus:border-primary focus:ring-2 focus:ring-primary focus:outline-none"
                                placeholder="Masukkan jumlah uang" />
                                <button
                                    wire:click="payExact"
                                    {{ $disableExact ? 'disabled' : '' }}
                                    {{-- @disabled($disableExact) --}}
                                    class="w-16 py-2.5 rounded-xl flex items-center justify-center text-white font-bold
                                        {{ $disableExact
                                        ? 'bg-gray-400 cursor-not-allowed'
                                        : 'bg-green-500 hover:bg-green-600' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                        stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" 
                                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 
                                                4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 
                                                12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 
                                                0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 
                                                1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </button>
                            </div>
                            <!-- Checkbox Piutang dan Modal Piutang -->
                            <div wire:key="debt-checkbox-{{ now() }}" class="mt-2">
                                <input
                                    type="checkbox"
                                    wire:click="toggleDebtModal"
                                    @if (!$selectedCustomer) disabled @endif
                                    {{ $isDebt ? 'checked' : '' }}
                                >
                                <label for="isDebt">Piutang</label>
                                @if (!$selectedCustomer)
                                    <p class="text-xs text-red-400 font-bold italic">Pilih customer terlebih dahulu untuk mengaktifkan piutang.</p>
                                @endif
                                <div class="text-sm text-gray-500 mt-2">Status: {{ $isDebt ? '✔️ Piutang' : '❌ Cash' }}</div>
                            </div>             
                        </div>

                        {{-- Kembalian / Pesan Uang Kurang --}}
                        @php
                            $change = ($paidAmount !== null) ? $paidAmount - $total : 0;
                            $disablePay = empty($cart) || (!$isDebt && ($paidAmount === null || $paidAmount < $total));
                            $remainingDebt = ($isDebt && $paidAmount !== null) ? $total - $paidAmount : 0;
                        @endphp

                        <div class="mt-2 text-sm font-semibold">
                            @if (empty($cart))
                                <span class="text-gray-600">Kembalian: Rp 0</span>
                            @elseif ($paidAmount === null)
                                <span class="text-gray-600">Kembalian: Rp 0</span>
                            @elseif ($isDebt && $paidAmount < $total)
                                <span class="text-orange-600">
                                    Piutang: Rp <span class="text-lg">{{ number_format($remainingDebt, 0, ',', '.') }}</span>
                                </span>
                            @elseif ($paidAmount < $total)
                                <span class="text-red-600">
                                    Uang yang dimasukkan kurang dari total harga produk.
                                </span>
                            @else
                                <div class="text-green-600">
                                    Kembalian: Rp <span class="text-lg">{{ number_format($change, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>

                        <button
                            wire:click="checkout"
                            {{ $disablePay ? 'disabled' : '' }}
                            {{-- @disabled($disablePay) --}}
                            class="mt-4 w-full py-2 rounded-xl text-white font-bold
                            {{ $disablePay
                                ? 'bg-gray-400 cursor-not-allowed'
                                : 'bg-green-500 hover:bg-green-600' }}">
                            Bayar Sekarang
                        </button>
                    </div>
                </div>
            </div>        

            {{-- Popup Sukses --}}
            @if ($showSuccessPopup)
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden">
                    {{-- Header --}}
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Transaksi Berhasil</h2>
                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Close modal</span>
                        </button>
                    </div>

                    {{-- Body --}}
                    <div class="p-4 md:p-5 space-y-4">
                        <p class="text-base leading-relaxed text-gray-500">
                            Terima kasih sudah berbelanja di Hafnan Mart.
                        </p>
                        @php
                            $sisaPiutang = $successIsDebt && $successPaidAmount < $successTotal
                                ? $successTotal - $successPaidAmount
                                : 0;
                        @endphp

                        @if ($successIsDebt && $sisaPiutang > 0)
                            <p class="text-base leading-relaxed text-gray-500">
                                Sisa Piutang Anda : <span class="text-lg font-bold text-orange-500">Rp {{ number_format($sisaPiutang, 0, ',', '.') }}</span>
                            </p>
                        @else
                            <p class="text-base leading-relaxed text-gray-500">
                                Uang Kembali anda : <span class="text-lg font-bold text-green-500">Rp {{ number_format($changeResult, 0, ',', '.') }}</span>
                            </p>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between p-4 md:p-5 border-t border-gray-200 rounded-b">
                        <button 
                            wire:click="$set('showSuccessPopup', false)" 
                            class="text-white bg-red-500 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Tutup
                        </button>
                        @php
                            $routeStruk = $successIsDebt 
                                ? route('print.struk.piutang', $transaction_id) 
                                : route('print.struk', $transaction_id);
                        @endphp

                        <a 
                            href="{{ $routeStruk }}" 
                            target="_blank"
                            class="inline-flex items-center px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        >
                            Cetak Struk
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Modal Piutang -->
            @if ($showDebtModalOnce)
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                    <div class="bg-white w-full max-w-md rounded-lg shadow-lg overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b">
                            <h2 class="text-lg font-semibold text-gray-800">Form Piutang</h2>
                            <button wire:click="cancelDebtModal" class="text-gray-400 hover:text-gray-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="p-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Customer</label>
                                <input type="text" value="{{ $selectedCustomer?->name }}" disabled class="w-full mt-1 px-3 py-2 border rounded-lg shadow-sm bg-gray-100">
                            </div>                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                                <input type="date" wire:model.defer="dueDate" class="w-full mt-1 px-3 py-2 border rounded-lg shadow-sm">
                            </div>
                            @if ($errors->any())
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded relative mb-4">
                                    <ul class="list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        @if ($selectedCustomer)
                            <div class="flex justify-end p-4 border-t">
                                <button wire:click="saveDebtData" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Simpan</button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
