@if (session('success'))
    <div role="status" class="mb-6 flex items-start gap-3 rounded-2xl border border-botanical-200 bg-botanical-50 px-4 py-3 text-sm text-botanical-800">
        @include('pages.admin.partials.icon', ['name' => 'check', 'class' => 'mt-0.5 h-5 w-5 shrink-0'])
        <p>{{ session('success') }}</p>
    </div>
@endif

@if (session('error'))
    <div role="alert" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        {{ session('error') }}
    </div>
@endif

@if ($errors->any())
    <div role="alert" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
        <p class="font-semibold">Periksa kembali data yang dimasukkan.</p>
        <ul class="mt-2 list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
