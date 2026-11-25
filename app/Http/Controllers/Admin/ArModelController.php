<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArModelRequest;
use App\Http\Requests\Admin\UpdateArModelRequest;
use App\Models\ArModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ArModelController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $arModels = ArModel::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('nama_model', 'like', '%'.$search.'%');
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id_model_ar')
            ->paginate(5)
            ->withQueryString();

        return view('pages.admin.ar-models', [
            'page' => 'index',
            'arModels' => $arModels,
            'search' => $search,
            'totalArModels' => ArModel::query()->count(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.ar-models', [
            'page' => 'create',
            'arModel' => null,
        ]);
    }

    public function store(StoreArModelRequest $request): RedirectResponse
    {
        $attributes = $request->validated();
        $file = $attributes['image_ar'];
        unset($attributes['image_ar']);

        $path = $file->store('models', 'public');

        if (! is_string($path)) {
            throw new RuntimeException('Berkas model AR gagal disimpan.');
        }

        try {
            DB::transaction(fn () => ArModel::create([
                ...$attributes,
                'id_model_ar' => (string) Str::uuid(),
                'image_ar' => $path,
                'created_at' => now(),
            ]));
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('admin.ar-models.index')
            ->with('success', 'Model AR berhasil ditambahkan.');
    }

    public function edit(ArModel $arModel): View
    {
        return view('pages.admin.ar-models', [
            'page' => 'edit',
            'arModel' => $arModel,
        ]);
    }

    public function update(UpdateArModelRequest $request, ArModel $arModel): RedirectResponse
    {
        $attributes = $request->validated();
        $file = $attributes['image_ar'] ?? null;
        unset($attributes['image_ar']);
        $oldPath = $arModel->image_ar;

        if ($file !== null) {
            $path = $file->store('models', 'public');

            if (! is_string($path)) {
                throw new RuntimeException('Berkas model AR gagal disimpan.');
            }

            $attributes['image_ar'] = $path;
        }

        try {
            DB::transaction(fn () => $arModel->update($attributes));
        } catch (Throwable $exception) {
            if (isset($path)) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        if (isset($path)) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('admin.ar-models.index')
            ->with('success', 'Model AR berhasil diperbarui.');
    }
}
