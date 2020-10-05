<?php
declare(strict_types=1);

namespace Mamoot\CardMarket\Helper;

/**
 * Class CsvStockFileHelper
 *
 * @package Mamoot\CardMarket\Helper
 *
 * @author Nicolas Perussel <nicolas.perussel@gmail.com>
 */
final class CsvStockFileHelper
{
    /**
     * @var string
     */
    private $stockFileContent;

    public function __construct(string $stockFileContent)
    {
        $this->stockFileContent = $stockFileContent;
    }

    /**
     * Store the CSV stock content into CSV file on disk.
     *
     * @param string $pathToSave
     *
     * @return bool
     */
    public function storeStockFileOnDisk(string $pathToSave): bool
    {
        return self::saveFileOnDisk(self::decodeAndUnzipContent($this->stockFileContent), $pathToSave);
    }

    /**
     * Save CSV file on disk.
     *
     * @param string $csvContent
     * @param string $pathToSave
     *
     * @return bool
     */
    private function saveFileOnDisk(string $csvContent, string $pathToSave): bool
    {
        return file_put_contents($pathToSave, $csvContent, LOCK_EX) ? true : false;
    }

    /**
     * Decode and Gzip the CSV stock content.
     *
     * @param string $stockFileContent
     *
     * @return string
     */
    private function decodeAndUnzipContent(string $stockFileContent): string
    {
        if (!$decodedContent = base64_decode($stockFileContent)) {
            throw new \RuntimeException("CSV content can't be decoded.");
        }

        if (!$unzipContent = gzdecode($decodedContent)) {
            throw new \RuntimeException("CSV content can't be unzip.");
        }

        return $unzipContent;
    }
}
