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

namespace App\Tools;

use RuntimeException;
use ZipArchive;

class ZipFileProcessor
{
    /** @var string Zip archive file to be processed */
    protected string $archiveFileName;

    /** @var string Working directory */
    protected string $workDir;

    /** @var callable|null Progress callback */
    protected $progressCallback;

    /**
     * @param string $archiveFileName Zip archive file to be processed
     * @param string $workDir Working directory
     * @param callable|null $progressCallback Progress callback, (string)$filename, (int)$index, (int)$count passed
     */
    public function __construct(string $archiveFileName, string $workDir, ?callable $progressCallback = null)
    {
        $this->archiveFileName = $archiveFileName;
        $this->workDir = $workDir;
        $this->progressCallback = $progressCallback;
    }

    public function run(array $except = []): void
    {
        $zip = new ZipArchive();
        if ($zip->open($this->archiveFileName, ZipArchive::RDONLY) !== true) {
            throw new RuntimeException('Ошибка распаковки архива');
        }

        $names = [];

        for ($i = 0; $i < $zip->count(); $i++) {
            $details = $zip->statIndex($i);
            if (!is_array($details)) {
                $zip->close();
                throw new RuntimeException('Ошибка распаковки архива');
            }
            $archiveFileName = $details['name'];
            if (!empty($except)) {
                foreach ($except as $pattern) {
                    if (preg_match($pattern, $archiveFileName) > 0) {
                        continue 2;
                    }
                }
            }
            $names[] = $archiveFileName;
        }

        $count = count($names);

        foreach ($names as $index => $archiveFileName) {

            $zip->extractTo($this->workDir, $archiveFileName);

            $extractedFileName = $this->workDir . DIRECTORY_SEPARATOR . $archiveFileName;

            if ($this->progressCallback !== null) {
                call_user_func($this->progressCallback, $extractedFileName, $index + 1, $count);
            }

            unlink($extractedFileName);
        }

        $zip->close();
    }
}
