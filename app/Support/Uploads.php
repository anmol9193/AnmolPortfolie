<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class Uploads
{
    /** Folder inside /public that holds files uploaded from the admin panel. */
    public const DIR = 'uploads';

    /**
     * Store an uploaded file in public/uploads and return its public path ("uploads/abc.jpg").
     */
    public static function store(UploadedFile $file, string $prefix = 'file'): string
    {
        $name = Str::slug($prefix).'-'.Str::lower(Str::random(10)).'.'.$file->extension();

        $file->move(public_path(static::DIR), $name);

        return static::DIR.'/'.$name;
    }

    /**
     * Delete a previously uploaded file. Bundled files outside public/uploads are never touched.
     */
    public static function delete(?string $path): void
    {
        if (blank($path) || ! Str::startsWith($path, static::DIR.'/') || Str::contains($path, '..')) {
            return;
        }

        $full = public_path($path);

        if (is_file($full)) {
            @unlink($full);
        }
    }
}
