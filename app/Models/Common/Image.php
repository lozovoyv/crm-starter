<?php

namespace App\Models\Common;

use App\Models\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $hash
 * @property string $disk
 * @property string $filename
 * @property string $extension
 * @property string $original_filename
 * @property string $mime
 * @property int $size
 * @property string $tags
 *
 * @property-read  string $url
 */
class Image extends Model
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
     * @param array $attributes
     * @param string $disk
     *
     * @return  Image|null
     * @noinspection DuplicatedCode
     */
    public static function createFrom(array $attributes, string $disk): ?Image
    {
        // If attributes has ID return existing image. No overwriting.
        if (isset($attributes['id'])) {
            /** @var Image $image */
            $image = self::query()->where('id', $attributes['id'])->first();
            return $image;
        }

        if (!isset($attributes['content'])) {
            return null;
        }
        // Otherwise, try to search by existing image by hash
        $hash = md5($attributes['content']) . $attributes['size'];
        if (($image = self::query()->where('hash', $hash)->first()) !== null) {
            /** @var Image $image */
            return $image;
        }
        // Finally, store file and create new image
        $extension = pathinfo($attributes['name'], PATHINFO_EXTENSION);
        $parts = explode(';base64,', $attributes['content']);
        $content = base64_decode($parts[1]);
        Storage::disk($disk)->put("$hash.$extension", $content);
        $image = new Image;
        $image->setAttribute('hash', $hash);
        $image->setAttribute('disk', $disk);
        $image->setAttribute('filename', "$hash.$extension");
        $image->setAttribute('extension', $extension);
        $image->setAttribute('original_filename', $attributes['name']);
        $image->setAttribute('mime', $attributes['type']);
        $image->setAttribute('size', $attributes['size']);
        $image->save();

        return $image;
    }

    /**
     * Create collection of images for given array of attributes.
     *
     * @param array $images
     * @param string $disk
     *
     * @return  Collection
     */
    public static function createFromMany(array $images, string $disk): Collection
    {
        $collection = new Collection;

        foreach ($images as $image) {
            $result = self::createFrom($image, $disk);
            if ($result !== null) {
                $collection->push($result);
            }
        }

        return $collection;
    }
}
