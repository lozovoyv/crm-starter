<?php
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);
/*
 * This file is part of Opxx Starter project
 *
 * (c) Viacheslav Lozovoy <vialoz@yandex.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileUtils
{
    /**
     * Get file size over urt without downloading it.
     *
     * @param string $url
     *
     * @return int
     */
    public static function remoteSize(string $url): int
    {
        // Assume failure.
        $result = -1;

        $curl = curl_init($url);

        // Issue a HEAD request and follow any redirects.
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($curl);
        curl_close($curl);

        if ($data) {
            $content_length = "unknown";
            $status = "unknown";
            $data = strtolower($data);
            if (preg_match("/^http\/[1|2](\.[01])* (\d\d\d)/", $data, $matches)) {
                $status = (int)$matches[2];
            }

            if (preg_match("/content-length: (\d+)/", $data, $matches)) {
                $content_length = (int)$matches[1];
            }

            // http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
            if ($status === 200 || ($status > 300 && $status <= 308)) {
                $result = $content_length;
            }
        }

        return $result;
    }

    /**
     * Convert size in bytes to human-readable format/
     *
     * @param int $sizeInBytes
     * @param int $dec
     *
     * @return string
     */
    public static function humanReadableSize(int $sizeInBytes, int $dec = 2): string
    {
        if ($sizeInBytes < 0) {
            return 'не возможно вычислить объем';
        }
        $sizes = ['б', 'Кб', 'Мб', 'Гб', 'Тб', 'Пб', 'Еб'];
        $factor = (int)floor((strlen((string)$sizeInBytes) - 1) / 3);
        if ($factor === 0) {
            $dec = 0;
        }

        return sprintf("%.{$dec}f %s", $sizeInBytes / (1024 ** $factor), $sizes[$factor]);
    }

    /**
     * Remove directory with all content.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function removeDirWithContent(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        $iterator = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        return rmdir($path);
    }
}
