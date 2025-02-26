<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Log;


trait HandlesImages
{
    /**
     * Redimensionar y guardar una imagen.
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param string $path
     * @param int $width
     * @param int|null $height
     * @return string
     */
    public function resizeAndSaveImage($image, $path, $width, $height = null)
    {
        // Instanciar ImageManager con el driver GD
        $imageManager = new ImageManager(new GdDriver());
        
        // Leer la imagen y redimensionar
        $resizedImage = $imageManager->read($image->getRealPath())->scale(width: $width);
        
        if ($height) {
            $resizedImage = $resizedImage->scale(height: $height);
        }
        
        // Generar nombre único y guardar la imagen
        $imagePath = $path . '/' . uniqid() . '.' . $image->getClientOriginalExtension();
        Storage::disk('public')->put($imagePath, $resizedImage->encode());
        
        // Retornar solo la ruta relativa sin 'storage/'
        return $imagePath;
    }

    /**
     * Eliminar una imagen existente.
     *
     * @param string $path
     */
    public function deleteImageIfExists($path)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}