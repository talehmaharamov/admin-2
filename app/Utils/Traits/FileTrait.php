<?php

namespace App\Utils\Traits;
use Exception;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

trait FileTrait
{
    /**
     * @throws Exception
     */
    public function uploadImage(string $path, $file): string
    {
        try {
            $imagePath = public_path('images/' . $path);
            if (!\Illuminate\Support\Facades\File::isDirectory($imagePath)) {
                File::makeDirectory($imagePath, 0777, true, true);
            }
            $filename = uniqid() . '.webp';
            $this->resizeAndSaveImage($file, $imagePath . '/' . $filename);
            return 'images/' . $path . '/' . $filename;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function resizeAndSaveImage($file, $path): void
    {
        Image::make($file)->resize(null, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->encode('webp', 75)->save($path, 60);
    }

    /**
     * @throws Exception
     */
    public function uploadPDF($file): string
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('pdf', $filename);
            return 'pdf/' . $filename;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function uploadMultipleImages(string $path, array $files): array
    {
        try {
            $imagePath = public_path('images/' . $path);
            $result = [];
            if (!File::isDirectory($imagePath)) {
                File::makeDirectory($imagePath, 0777, true, true);
            }
            foreach ($files as $file) {
                $filename = uniqid() . '.webp';
                $this->resizeAndSaveImage($file, $imagePath . '/' . $filename);
                $result[] = "images/$path/$filename";
            }
            return $result;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
