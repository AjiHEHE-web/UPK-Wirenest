@extends('layouts.admin')

@section('title')
    @switch($page)
        @case('index') Manajemen AR @break
        @case('create') Tambah Model AR @break
        @case('edit') Edit Model AR @break
    @endswitch
@endsection

@section('breadcrumb')
    @if ($page === 'index')
        Manajemen AR
    @else
        <a href="{{ route('admin.ar-models.index') }}" class="hover:text-botanical-800">Manajemen AR</a>
        <span aria-hidden="true">/</span>
        <span>@yield('title')</span>
    @endif
@endsection

@section('content')
    @if ($page === 'index')
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="max-w-2xl text-sm leading-6 text-stone-600">Kelola model 3D untuk pengalaman augmented reality produk Anda.</p>
            </div>
            <a href="{{ route('admin.ar-models.create') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-botanical-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-800">
                @include('pages.admin.partials.icon', ['name' => 'plus', 'class' => 'h-4 w-4'])
                Tambah Model AR
            </a>
        </div>

        <section class="mt-6 grid gap-4 sm:grid-cols-3" aria-label="Ringkasan model AR">
            <article class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-stone-600">Model AR terdaftar</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-ink">{{ number_format($arModels->total()) }}</p>
                <p class="mt-1 text-xs text-stone-500">Berkas model yang sudah tersimpan</p>
            </article>
            <article class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-stone-600">QR siap dibagikan</p>
                <p class="mt-2 text-3xl font-semibold tracking-tight text-ink">{{ number_format($totalArModels) }}</p>
                <p class="mt-1 text-xs text-stone-500">Setiap model memiliki tautan publik</p>
            </article>
            <article class="rounded-2xl border border-line bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-stone-600">Format didukung</p>
                <p class="mt-2 text-lg font-semibold tracking-tight text-ink">GLB · GLTF · USDZ</p>
                <p class="mt-1 text-xs text-stone-500">OBJ · FBX · STL · DAE</p>
            </article>
        </section>

        <section class="mt-6 overflow-hidden rounded-2xl border border-line bg-white shadow-sm">
            <form method="GET" action="{{ route('admin.ar-models.index') }}" class="flex flex-col gap-3 border-b border-line p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div>
                    <h2 class="text-base font-semibold text-ink">Daftar model AR</h2>
                    <p class="mt-1 text-sm text-stone-500">{{ $arModels->total() }} model terdaftar</p>
                </div>
                <div class="flex w-full gap-2 sm:max-w-md">
                    <label for="search-ar-models" class="sr-only">Cari nama model</label>
                    <input id="search-ar-models" name="search" value="{{ $search }}" type="search" placeholder="Cari nama model..." class="h-11 w-full min-w-0 flex-1 rounded-xl border-line bg-stone-50 px-3 text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:bg-white focus:ring-botanical-500">
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line bg-white px-4 text-sm font-semibold text-ink transition hover:bg-stone-50">Cari</button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left text-sm">
                    <thead class="bg-stone-50/80 text-xs uppercase tracking-wide text-stone-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 font-semibold">Model AR</th>
                            <th scope="col" class="px-6 py-3 font-semibold">QR Code</th>
                            <th scope="col" class="px-6 py-3 font-semibold">Berkas</th>
                            <th scope="col" class="px-6 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @forelse ($arModels as $arModel)
                            @php($extension = strtoupper(pathinfo($arModel->image_ar, PATHINFO_EXTENSION)))
                            <tr class="transition hover:bg-stone-50/70">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-botanical-50 text-botanical-700">
                                            @include('pages.admin.partials.icon', ['name' => 'cube', 'class' => 'h-5 w-5'])
                                        </span>
                                        <div class="min-w-0">
                                            <p class="max-w-xs truncate font-semibold text-ink">{{ $arModel->nama_model }}</p>
                                            <p class="mt-1 max-w-xs truncate text-xs text-stone-500">{{ $arModel->id_model_ar }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3" data-ar-qr-value="{{ rtrim(config('app.url'), '/').route('ar.show', $arModel->id_model_ar, false) }}" data-ar-model-id="{{ $arModel->id_model_ar }}">
                                        <canvas data-ar-qr-canvas role="img" aria-label="QR untuk {{ $arModel->nama_model }}" class="h-20 w-20 rounded-lg border border-line bg-white p-1"></canvas>
                                        <div class="flex flex-col items-start gap-1">
                                            <button type="button" data-ar-qr-download class="inline-flex min-h-10 items-center rounded-lg border border-line px-3 text-xs font-semibold text-botanical-800 transition hover:bg-botanical-50">Unduh QR</button>
                                            <p data-ar-qr-status aria-live="polite" class="text-xs text-stone-500"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ Storage::disk('public')->url($arModel->image_ar) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-line px-3 text-xs font-semibold text-botanical-800 transition hover:bg-botanical-50">
                                        <span class="rounded bg-botanical-50 px-2 py-1">{{ $extension }}</span>
                                        <span class="max-w-40 truncate">{{ basename($arModel->image_ar) }}</span>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('admin.ar-models.edit', $arModel) }}" class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-stone-500 transition hover:bg-botanical-50 hover:text-botanical-700" aria-label="Edit {{ $arModel->nama_model }}">
                                            @include('pages.admin.partials.icon', ['name' => 'edit', 'class' => 'h-4 w-4'])
                                        </a>
                                        <form method="POST" action="{{ route('admin.ar-models.destroy', $arModel) }}" onsubmit="return confirm('Hapus model AR ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-11 w-11 items-center justify-center rounded-lg text-stone-500 transition hover:bg-rose-50 hover:text-rose-700" aria-label="Hapus {{ $arModel->nama_model }}">
                                                @include('pages.admin.partials.icon', ['name' => 'trash', 'class' => 'h-4 w-4'])
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-botanical-50 text-botanical-700">
                                        @include('pages.admin.partials.icon', ['name' => 'cube', 'class' => 'h-6 w-6'])
                                    </span>
                                    <p class="mt-3 font-semibold text-ink">{{ $search ? 'Model AR tidak ditemukan' : 'Belum ada model AR' }}</p>
                                    <p class="mt-1 text-sm text-stone-500">{{ $search ? 'Coba kata kunci yang berbeda.' : 'Tambahkan model 3D pertama untuk membuat QR publik.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($arModels->hasPages())
                <div class="border-t border-line px-5 py-4 sm:px-6">{{ $arModels->links() }}</div>
            @endif
        </section>
    @else
        <div class="mx-auto max-w-4xl">
            <a href="{{ route('admin.ar-models.index') }}" class="inline-flex min-h-11 items-center gap-2 rounded-lg text-sm font-semibold text-stone-600 transition hover:text-botanical-700">
                @include('pages.admin.partials.icon', ['name' => 'back', 'class' => 'h-4 w-4'])
                Kembali ke manajemen AR
            </a>

            <section class="mt-4 rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-8">
                <div class="mb-7 border-b border-line pb-5">
                    <h2 class="text-lg font-semibold text-ink">{{ $page === 'edit' ? 'Perbarui model AR' : 'Informasi model AR' }}</h2>
                    <p class="mt-1 text-sm leading-6 text-stone-500">Unggah model 3D untuk membuat halaman AR dan QR publik. Format GLB, GLTF, USDZ, OBJ, FBX, STL, dan DAE hingga 100 MB.</p>
                </div>

                    <form method="POST" action="{{ $page === 'edit' ? route('admin.ar-models.update', $arModel) : route('admin.ar-models.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @if ($page === 'edit')
                            @method('PUT')
                        @endif

                        <div>
                            <label for="nama_model" class="mb-2 block text-sm font-semibold text-ink">Nama model AR <span class="text-rose-600">*</span></label>
                            <input id="nama_model" name="nama_model" value="{{ old('nama_model', $arModel?->nama_model) }}" required maxlength="150" class="min-h-11 w-full rounded-xl border-line bg-white text-sm placeholder:text-stone-400 focus:border-botanical-500 focus:ring-botanical-500" placeholder="Contoh: Buket Mawar 3D">
                            @error('nama_model') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="image_ar" class="mb-2 block text-sm font-semibold text-ink">Berkas model AR @if ($page === 'create') <span class="text-rose-600">*</span> @endif</label>
                            <div class="rounded-2xl border border-dashed border-stone-300 bg-stone-50/70 p-4 sm:p-5">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl bg-botanical-100 text-botanical-700">
                                        @include('pages.admin.partials.icon', ['name' => 'cube', 'class' => 'h-8 w-8'])
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <input id="image_ar" name="image_ar" type="file" accept=".glb,.gltf,.usdz,.obj,.fbx,.stl,.dae" @if ($page === 'create') required @endif class="block min-h-11 w-full text-sm text-stone-600 file:mr-3 file:min-h-10 file:rounded-lg file:border-0 file:bg-botanical-100 file:px-4 file:text-sm file:font-semibold file:text-botanical-800 hover:file:bg-botanical-200">
                                        <p class="mt-2 text-xs leading-5 text-stone-600">GLB, GLTF, USDZ, OBJ, FBX, STL, atau DAE. Maksimal 100 MB. @if ($page === 'edit') Kosongkan jika tidak ingin mengganti berkas. @endif</p>
                                        @if ($arModel?->image_ar)
                                            <a href="{{ Storage::disk('public')->url($arModel->image_ar) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-flex min-h-10 items-center text-sm font-semibold text-botanical-800 underline underline-offset-2">{{ basename($arModel->image_ar) }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @error('image_ar') <p class="mt-1.5 text-sm text-rose-700">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col-reverse gap-3 border-t border-line pt-5 sm:flex-row sm:justify-end">
                            <a href="{{ route('admin.ar-models.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-line px-5 text-sm font-semibold text-stone-600 transition hover:bg-stone-50">Batal</a>
                            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-botanical-700 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-800">
                                @include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'h-4 w-4'])
                                {{ $page === 'edit' ? 'Simpan Perubahan' : 'Simpan Model AR' }}
                            </button>
                        </div>
                    </form>
            </section>
        </div>
    @endif
@endsection
