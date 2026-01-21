<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;


class Media
{

    public function upload($image, $path, $oldImage = null)
    {
        if ($oldImage) {
            $this->delete($oldImage, $path);
        }
        $newImageName = $image->hashName();
        $image->storeAs($path, $newImageName, 'public');
        return $newImageName;
    }


    public function delete($image, $path)
    {
        if (Storage::disk('public')->exists($path . '/' . $image)) {
            return Storage::disk('public')->delete($path . '/' . $image);
        }
        return false;
    }
}
