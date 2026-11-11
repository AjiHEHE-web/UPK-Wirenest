<?php

namespace App\Http\Controllers;

use App\Models\ArModel;
use Illuminate\Contracts\View\View;

class ArExperienceController extends Controller
{
    public function show(ArModel $arModel): View
    {
        return view('pages.ar.show', [
            'arModel' => $arModel,
            'modelUrl' => '/storage/'.ltrim($arModel->image_ar, '/'),
            'modelFormat' => strtolower(pathinfo($arModel->image_ar, PATHINFO_EXTENSION)),
        ]);
    }
}
