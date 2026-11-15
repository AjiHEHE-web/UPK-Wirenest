@extends('layouts.admin')

@section('title')
    @switch($page)
        @case('index') Semua Produk @break
        @case('create') Tambah Produk @break
        @case('edit') Edit Produk @break
        @case('show') Detail Produk @break
    @endswitch
@endsection

@section('breadcrumb')
    @if ($page === 'index') Manajemen Produk @else
        <a href="{{ route('admin.products.index') }}" class="hover:text-botanical-800">Manajemen Produk</a>
        <span aria-hidden="true">/</span>
        <span>@yield('title')</span>
    @endif
@endsection

@section('content')
@if ($page === 'index')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm text-stone-500">Kelola katalog dan ketersediaan bunga di toko Anda.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-botanical-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-800">
            @include('pages.admin.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4'])
            Tambah Produk
        </a>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
                    <form method="GET" action="{{ route('admin.products.index') }}" data-ajax-search data-ajax-results="admin-products-results" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-ink">Daftar produk</h2>
                <p id="admin-products-total" data-ajax-refresh class="mt-1 text-sm text-stone-500">{{ $products->total() }} produk terdaftar</p>
            </div>
            <div class="flex w-full gap-2 sm:max-w-md">
                <label for="search-products" class="sr-only">Cari nama atau kategori produk</label>
                <div class="relative min-w-0 flex-1">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-stone-400">
                        @include('pages.admin.partials.icon', ['name' => 'search', 'class' => 'h-4 w-4'])
                    </span>
                    <input id="search-products" name="search" value="{{ $search }}" type="search" placeholder="Cari nama atau kategori..." class="h-11 w-full rounded-xl border-line bg-stone-50 pl-10 pr-3 text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:bg-white focus:ring-botanical-500">
                </div>
                <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line bg-white px-4 text-sm font-semibold text-ink transition hover:bg-stone-50">Cari</button>
            </div>
        </form>

        <div id="admin-products-results" data-ajax-results aria-live="polite">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-semibold">Produk</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Kategori</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Harga</th>
                        <th scope="col" class="px-6 py-3 font-semibold">Stok</th>
                        <th scope="col" class="px-6 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($products as $product)
                        <tr class="transition hover:bg-stone-50/70">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="Foto {{ $product->name_produk }}" class="h-12 w-12 rounded-xl object-cover" loading="lazy">
                                    @else
                                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-botanical-50 text-botanical-600">
                                            @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-5 w-5'])
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="max-w-xs truncate font-semibold text-ink">{{ $product->name_produk }}</p>
                                        <p class="mt-1 text-xs text-stone-500">ID {{ Str::limit($product->id_produk, 12, '') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-stone-600">{{ $product->category->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-ink">Rp {{ number_format((float) $product->harga_produk, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-rose-50 text-rose-700' => $product->stock_produk < 5,
                                    'bg-botanical-50 text-botanical-800' => $product->stock_produk >= 5,
                                ])>{{ number_format($product->stock_produk) }} unit</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.products.show', $product) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-stone-500 transition hover:bg-botanical-50 hover:text-botanical-700" aria-label="Detail {{ $product->name_produk }}">
                                        @include('pages.admin.partials.icon', ['name' => 'eye', 'class' => 'h-4 w-4'])
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-stone-500 transition hover:bg-botanical-50 hover:text-botanical-700" aria-label="Edit {{ $product->name_produk }}">
                                        @include('pages.admin.partials.icon', ['name' => 'edit', 'class' => 'h-4 w-4'])
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-botanical-50 text-botanical-700">
                                    @include('pages.admin.partials.icon', ['name' => 'package', 'class' => 'h-6 w-6'])
                                </span>
                                <p class="mt-3 font-semibold text-ink">{{ $search ? 'Produk tidak ditemukan' : 'Belum ada produk' }}</p>
                                <p class="mt-1 text-sm text-stone-500">{{ $search ? 'Coba kata kunci yang berbeda.' : 'Tambahkan produk pertama ke katalog.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($products->hasPages())
            <div class="border-t border-line px-5 py-4 sm:px-6">{{ $products->links() }}</div>
        @endif
        </div>
    </section>
@elseif ($page === 'create')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.products.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-semibold text-stone-600 transition hover:text-botanical-700">
            @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
            Kembali ke produk
        </a>
        <section class="mt-4 rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-8">
            <div class="mb-7 border-b border-line pb-5">
                <h2 class="text-lg font-semibold text-ink">Informasi produk</h2>
                <p class="mt-1 text-sm text-stone-500">Lengkapi detail produk baru yang akan ditampilkan di katalog.</p>
            </div>
            @php
                $product = null;
                $action = route('admin.products.store');
                $method = 'POST';
                $submitLabel = 'Simpan Produk';
            @endphp
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name_produk" class="mb-2 block text-sm font-semibold text-ink">Nama produk <span class="text-rose-600">*</span></label>
            <input id="name_produk" name="name_produk" value="{{ old('name_produk', $product?->name_produk) }}" required maxlength="150" class="min-h-11 w-full rounded-xl border-line bg-white text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:ring-botanical-500" placeholder="Contoh: Buket Mawar Pastel">
            @error('name_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="id_kategori" class="mb-2 block text-sm font-semibold text-ink">Kategori <span class="text-rose-600">*</span></label>
            <select id="id_kategori" name="id_kategori" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id_kategori }}" @selected(old('id_kategori', $product?->id_kategori) === $category->id_kategori)>{{ $category->name }}</option>
                @endforeach
            </select>
            @if ($categories->isEmpty())
                <p class="mt-1.5 text-sm text-amber-700">Kategori belum tersedia. Tambahkan kategori melalui pengelolaan database sebelum membuat produk.</p>
            @endif
            @error('id_kategori') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="harga_produk" class="mb-2 block text-sm font-semibold text-ink">Harga (Rp) <span class="text-rose-600">*</span></label>
            <input id="harga_produk" name="harga_produk" value="{{ old('harga_produk', $product?->harga_produk) }}" type="number" step="0.01" min="0.01" max="9999999999.99" inputmode="decimal" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="150000">
            @error('harga_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="stock_produk" class="mb-2 block text-sm font-semibold text-ink">Stok <span class="text-rose-600">*</span></label>
            <input id="stock_produk" name="stock_produk" value="{{ old('stock_produk', $product?->stock_produk ?? 0) }}" type="number" step="1" min="0" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="0">
            @error('stock_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="deskripsi_produk" class="mb-2 block text-sm font-semibold text-ink">Deskripsi</label>
            <textarea id="deskripsi_produk" name="deskripsi_produk" rows="5" maxlength="10000" class="w-full rounded-xl border-line bg-white text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:ring-botanical-500" placeholder="Ceritakan detail bunga, ukuran, atau isi rangkaian...">{{ old('deskripsi_produk', $product?->deskripsi_produk) }}</textarea>
            @error('deskripsi_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2" x-data="{ preview: null }">
            <label for="image" class="mb-2 block text-sm font-semibold text-ink">Gambar produk @if(! $product) <span class="text-rose-600">*</span> @endif</label>
            <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50/70 p-4 sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <template x-if="preview">
                        <img :src="preview" alt="Pratinjau gambar produk" class="h-24 w-24 rounded-xl object-cover">
                    </template>
                    @if ($product?->image)
                        <img x-show="!preview" src="{{ Storage::url($product->image) }}" alt="Gambar saat ini: {{ $product->name_produk }}" class="h-24 w-24 rounded-xl object-cover">
                    @else
                        <span x-show="!preview" class="flex h-24 w-24 items-center justify-center rounded-xl bg-botanical-100 text-botanical-700">
                            @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-8 w-8'])
                        </span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @if(! $product) required @endif x-on:change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="block min-h-11 w-full text-sm text-stone-600 file:mr-3 file:min-h-10 file:rounded-lg file:border-0 file:bg-botanical-100 file:px-4 file:text-sm file:font-semibold file:text-botanical-800 hover:file:bg-botanical-200">
                        <p class="mt-2 text-xs leading-5 text-stone-500">Format JPG, PNG, atau WebP. Ukuran maksimal 5 MB. @if($product) Kosongkan jika tidak ingin mengganti gambar. @endif</p>
                    </div>
                </div>
            </div>
            @error('image') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $product ? route('admin.products.show', $product) : route('admin.products.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line px-5 text-sm font-semibold text-stone-600 transition hover:bg-stone-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-botanical-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-800">
            @include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4'])
            {{ $submitLabel }}
        </button>
    </div>
</form>
        </section>
    </div>
@elseif ($page === 'edit')
    <div class="mx-auto max-w-4xl">
        <a href="{{ route('admin.products.show', $product) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-semibold text-stone-600 transition hover:text-botanical-700">
            @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
            Kembali ke detail
        </a>
        <section class="mt-4 rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-8">
            <div class="mb-7 border-b border-line pb-5">
                <h2 class="text-lg font-semibold text-ink">Perbarui informasi produk</h2>
                <p class="mt-1 text-sm text-stone-500">Perubahan akan langsung tersimpan pada katalog.</p>
            </div>
            @php
                $action = route('admin.products.update', $product);
                $method = 'PUT';
                $submitLabel = 'Simpan Perubahan';
            @endphp
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="name_produk" class="mb-2 block text-sm font-semibold text-ink">Nama produk <span class="text-rose-600">*</span></label>
            <input id="name_produk" name="name_produk" value="{{ old('name_produk', $product?->name_produk) }}" required maxlength="150" class="min-h-11 w-full rounded-xl border-line bg-white text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:ring-botanical-500" placeholder="Contoh: Buket Mawar Pastel">
            @error('name_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="id_kategori" class="mb-2 block text-sm font-semibold text-ink">Kategori <span class="text-rose-600">*</span></label>
            <select id="id_kategori" name="id_kategori" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">
                <option value="">Pilih kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id_kategori }}" @selected(old('id_kategori', $product?->id_kategori) === $category->id_kategori)>{{ $category->name }}</option>
                @endforeach
            </select>
            @if ($categories->isEmpty())
                <p class="mt-1.5 text-sm text-amber-700">Kategori belum tersedia. Tambahkan kategori melalui pengelolaan database sebelum membuat produk.</p>
            @endif
            @error('id_kategori') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="harga_produk" class="mb-2 block text-sm font-semibold text-ink">Harga (Rp) <span class="text-rose-600">*</span></label>
            <input id="harga_produk" name="harga_produk" value="{{ old('harga_produk', $product?->harga_produk) }}" type="number" step="0.01" min="0.01" max="9999999999.99" inputmode="decimal" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="150000">
            @error('harga_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="stock_produk" class="mb-2 block text-sm font-semibold text-ink">Stok <span class="text-rose-600">*</span></label>
            <input id="stock_produk" name="stock_produk" value="{{ old('stock_produk', $product?->stock_produk ?? 0) }}" type="number" step="1" min="0" required class="min-h-11 w-full rounded-xl border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500" placeholder="0">
            @error('stock_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="deskripsi_produk" class="mb-2 block text-sm font-semibold text-ink">Deskripsi</label>
            <textarea id="deskripsi_produk" name="deskripsi_produk" rows="5" maxlength="10000" class="w-full rounded-xl border-line bg-white text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:ring-botanical-500" placeholder="Ceritakan detail bunga, ukuran, atau isi rangkaian...">{{ old('deskripsi_produk', $product?->deskripsi_produk) }}</textarea>
            @error('deskripsi_produk') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>

        <div class="sm:col-span-2" x-data="{ preview: null }">
            <label for="image" class="mb-2 block text-sm font-semibold text-ink">Gambar produk @if(! $product) <span class="text-rose-600">*</span> @endif</label>
            <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50/70 p-4 sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                    <template x-if="preview">
                        <img :src="preview" alt="Pratinjau gambar produk" class="h-24 w-24 rounded-xl object-cover">
                    </template>
                    @if ($product?->image)
                        <img x-show="!preview" src="{{ Storage::url($product->image) }}" alt="Gambar saat ini: {{ $product->name_produk }}" class="h-24 w-24 rounded-xl object-cover">
                    @else
                        <span x-show="!preview" class="flex h-24 w-24 items-center justify-center rounded-xl bg-botanical-100 text-botanical-700">
                            @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-8 w-8'])
                        </span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @if(! $product) required @endif x-on:change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="block min-h-11 w-full text-sm text-stone-600 file:mr-3 file:min-h-10 file:rounded-lg file:border-0 file:bg-botanical-100 file:px-4 file:text-sm file:font-semibold file:text-botanical-800 hover:file:bg-botanical-200">
                        <p class="mt-2 text-xs leading-5 text-stone-500">Format JPG, PNG, atau WebP. Ukuran maksimal 5 MB. @if($product) Kosongkan jika tidak ingin mengganti gambar. @endif</p>
                    </div>
                </div>
            </div>
            @error('image') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:justify-end">
        <a href="{{ $product ? route('admin.products.show', $product) : route('admin.products.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line px-5 text-sm font-semibold text-stone-600 transition hover:bg-stone-50">Batal</a>
        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-botanical-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-800">
            @include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4'])
            {{ $submitLabel }}
        </button>
    </div>
</form>
        </section>
    </div>
@elseif ($page === 'show')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <a href="{{ route('admin.products.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg text-sm font-semibold text-stone-600 transition hover:text-botanical-700">
            @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
            Kembali ke daftar
        </a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-botanical-700 px-4 text-sm font-semibold text-white transition hover:bg-botanical-800">
                @include('pages.admin.partials.icon', ['name' => 'edit', 'class' => 'h-4 w-4'])
                Edit Produk
            </a>
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" x-data @submit.prevent="if (window.confirm('Hapus produk ini?')) $el.submit()">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-rose-200 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">
                    @include('pages.admin.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <section class="mt-5 grid overflow-hidden rounded-2xl border border-line bg-white shadow-sm lg:grid-cols-[0.9fr_1.1fr]">
        <div class="min-h-72 bg-botanical-50 p-5 sm:p-8">
            @if ($product->image)
                <img src="{{ Storage::url($product->image) }}" alt="Foto {{ $product->name_produk }}" class="h-full min-h-72 w-full rounded-xl object-cover">
            @else
                <div class="flex h-full min-h-72 items-center justify-center rounded-xl bg-botanical-100 text-botanical-700">
                    @include('pages.admin.partials.icon', ['name' => 'flower', 'class' => 'h-24 w-24'])
                </div>
            @endif
        </div>
        <div class="p-5 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-botanical-50 px-3 py-1 text-xs font-semibold text-botanical-800">{{ $product->category->name }}</span>
                <span @class([
                    'rounded-full px-3 py-1 text-xs font-semibold',
                    'bg-rose-50 text-rose-700' => $product->stock_produk < 5,
                    'bg-stone-100 text-stone-700' => $product->stock_produk >= 5,
                ])>{{ number_format($product->stock_produk) }} stok</span>
            </div>
            <h2 class="mt-4 text-2xl font-semibold tracking-tight text-ink sm:text-3xl">{{ $product->name_produk }}</h2>
            <p class="mt-3 text-2xl font-semibold text-botanical-700">Rp {{ number_format((float) $product->harga_produk, 0, ',', '.') }}</p>
            <div class="mt-7 border-t border-line pt-6">
                <h3 class="text-sm font-semibold text-ink">Deskripsi produk</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-stone-600">{{ $product->deskripsi_produk ?: 'Belum ada deskripsi produk.' }}</p>
            </div>
            <dl class="mt-7 grid gap-4 border-t border-line pt-6 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Tanggal dibuat</dt>
                    <dd class="mt-1 text-sm font-medium text-ink">{{ $product->created_at?->format('d M Y, H:i') ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium uppercase tracking-wide text-stone-500">Terakhir diperbarui</dt>
                    <dd class="mt-1 text-sm font-medium text-ink">{{ $product->updated_at?->format('d M Y, H:i') ?? '—' }}</dd>
                </div>
            </dl>
        </div>
    </section>
@endif
@endsection
