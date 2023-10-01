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

class Downloader
{
    /** @var int|null Internal state */
    protected ?int $downloadProgress = null;

    /** @var string File url to download */
    protected string $fileUrl;

    /** @var string Filename */
    protected string $fileTargetName;

    /** @var callable|null Progress callback */
    protected $progressCallback;

    /**
     * @param string $fileUrl File url to be downloaded
     * @param string $fileTargetName Target filename to download to
     * @param callable|null $progressCallback Progress callback, (int)$progress passed
     */
    public function __construct(string $fileUrl, string $fileTargetName, ?callable $progressCallback = null)
    {
        $this->fileUrl = $fileUrl;
        $this->fileTargetName = $fileTargetName;
        $this->progressCallback = $progressCallback;
    }

    /**
     * Perform download process.
     *
     * @return bool
     */
    public function download(): bool
    {
        set_time_limit(0);

        $fp = fopen($this->fileTargetName, 'wb+');

        $curl = curl_init($this->fileUrl);
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);
        curl_setopt($curl, CURLOPT_FILE, $fp);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_NOPROGRESS, false);
        curl_setopt($curl, CURLOPT_PROGRESSFUNCTION, [$this, 'progress']);

        $result = curl_exec($curl);

        curl_close($curl);
        fclose($fp);

        return $result;
    }

    /**
     * Internal progress handler.
     *
     * @param $resource
     * @param $download_size
     * @param $downloaded
     * @param $upload_size
     * @param $uploaded
     *
     * @return void
     */
    protected function progress($resource, $download_size, $downloaded, $upload_size, $uploaded): void
    {
        $progress = (int)ceil($download_size <= 0 ? 0 : $downloaded / $download_size * 100);
        if ($this->downloadProgress !== $progress) {
            $this->downloadProgress = $progress;
            if ($this->progressCallback !== null) {
                call_user_func($this->progressCallback, $progress);
            }
        }
    }
}
