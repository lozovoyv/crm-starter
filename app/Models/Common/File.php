<?php

namespace App\Models\Common;

use App\Models\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @property int $id
 * @property string $hash
 * @property string $disk
 * @property string $filename
 * @property string $extension
 * @property string $original_filename
 * @property string $mime
 * @property int $size
 *
 * @property-read  string $url
 */
class File extends Model
{
    /** @var string[] Append attributes */
    protected $appends = ['url'];

    /**
     * Accessor for url attribute.
     *
     * @return  string|null
     */
    public function getUrlAttribute(): ?string
    {
        return Storage::disk($this->disk)->exists($this->filename) ? Storage::disk($this->disk)->url($this->filename) : null;
    }

    /**
     * Create file from raw content.
     *
     * @param string $disk
     * @param string $name
     * @param int $size
     * @param string $mime
     * @param $content
     *
     * @return  File|null
     */
    public static function create(string $disk, string $name, int $size, string $mime, $content): ?File
    {
        if (!isset($content)) {
            return null;
        }
        // Otherwise, try to search by existing file by hash
        $hash = md5($content) . $size;
        if (($file = self::query()->where('hash', $hash)->first()) !== null) {
            /** @var File $file */
            return $file;
        }
        $extension = pathinfo($name, PATHINFO_EXTENSION);

        // Finally, store file and create new file
        Storage::disk($disk)->put("$hash.$extension", $content);
        $file = new File;
        $file->setAttribute('hash', $hash);
        $file->setAttribute('disk', $disk);
        $file->setAttribute('filename', "$hash.$extension");
        $file->setAttribute('extension', $extension);
        $file->setAttribute('original_filename', $name);
        $file->setAttribute('mime', $mime);
        $file->setAttribute('size', $size);
        $file->save();

        return $file;
    }

    /**
     * @param array $attributes
     * @param string $disk
     *
     * @return  File|null
     * @noinspection DuplicatedCode
     */
    public static function createFrom(array $attributes, string $disk): ?File
    {
        // If attributes has ID return existing file. No overwriting.
        if (isset($attributes['id'])) {
            /** @var File $file */
            $file = self::query()->where('id', $attributes['id'])->first();
            return $file;
        }

        if (!isset($attributes['content'])) {
            return null;
        }
        // Otherwise, try to search by existing file by hash
        $hash = md5($attributes['content']) . $attributes['size'];
        if (($file = self::query()->where('hash', $hash)->first()) !== null) {
            /** @var File $file */
            return $file;
        }
        // Finally, store file and create new file
        $extension = pathinfo($attributes['name'], PATHINFO_EXTENSION);
        $parts = explode(';base64,', $attributes['content']);
        $content = base64_decode($parts[1]);
        Storage::disk($disk)->put("$hash.$extension", $content);
        $file = new File;
        $file->setAttribute('hash', $hash);
        $file->setAttribute('disk', $disk);
        $file->setAttribute('filename', "$hash.$extension");
        $file->setAttribute('extension', $extension);
        $file->setAttribute('original_filename', $attributes['name']);
        $file->setAttribute('mime', $attributes['type']);
        $file->setAttribute('size', $attributes['size']);
        $file->save();

        return $file;
    }

    /**
     * Create collection of files for given array of attributes.
     *
     * @param array $files
     * @param string $disk
     *
     * @return  Collection
     */
    public static function createFromMany(array $files, string $disk): Collection
    {
        $collection = new Collection;

        foreach ($files as $file) {
            $result = self::createFrom($file, $disk);
            if ($result !== null) {
                $collection->push($result);
            }
        }

        return $collection;
    }

    /**
     * Download file.
     *
     * @return  string
     */
    public function path(): string
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Storage::disk($this->disk)->getDriver()->getAdapter()->getPathPrefix() . DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * Download file.
     *
     * @return  StreamedResponse
     */
    public function download(): StreamedResponse
    {
        return Storage::disk($this->disk)->download($this->filename, $this->original_filename);
    }
}
