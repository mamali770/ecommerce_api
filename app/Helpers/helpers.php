<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

function saveImage($image, $path)
{
    $imageName = $image->getClientOriginalName();
    $fileName = pathinfo($imageName, PATHINFO_FILENAME);
    $extension = $image->getClientOriginalExtension();

    while (Storage::disk('public')->exists($path . '/' . $imageName)) {
        $imageName = $fileName . "-" . Carbon::now()->format('u') . "." . $extension;
    }

    $imagePath = $image->storeAs($path, $imageName, 'public');

    return $imagePath;
}
