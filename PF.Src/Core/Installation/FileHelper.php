<?php

namespace Core\Installation;

use Core\Db;

class FileHelper
{
    /**
     * @var array
     */
    private $excludes = ['_static_', 'app.lock', 'composer.phar', '.git', '.idea'];

    /**
     * @var string
     */
    private $rootPath;

    /**
     * Get root path parents
     *
     * @return string
     */
    public function getRootPath()
    {
        if (null == $this->rootPath) {
            $this->rootPath = realpath(dirname(PHPFOX_DIR));
        }

        return $this->rootPath;
    }

    /**
     * @param string $targetZipFilename
     * @param array  $paths
     * @param array  $packageInformation
     *
     * @return array
     */
    public function exportTheme($targetZipFilename, $paths, $packageInformation = [])
    {

        if (!is_dir(dirname($targetZipFilename))) {
            mkdir(dirname($targetZipFilename), 0777, true);
        }

        $db = new Db();

        $flavors = $db->select('*')
            ->from(':theme_style')
            ->where(['theme_id' => $packageInformation['theme_id']])
            ->all();

        $packageInformation['flavors'] = [];

        foreach ($flavors as $flavor) {
            $packageInformation['flavors'][ $flavor['folder'] ] = $flavor['name'];
        }

        $checksumInformation = [];

        if (is_string($paths)) {
            $paths = [$paths];
        }

        if (file_exists($targetZipFilename)) {
            if (!@unlink($targetZipFilename)) {
                exit(sprintf('Unable write to "%s"', $targetZipFilename));
            }
        }

        $zipArchive = new \ZipArchive();
        $zipArchive->open($targetZipFilename, \ZipArchive::CREATE);

        $result = [];
        foreach ($paths as $path) {

            $path = realpath($path);

            if (is_file($path)) {

                $local = $this->normalizeFileName($path);
                $zipArchive->addFile($path, $local);
                $checksumInformation[] = $local;

                continue;
            }


            if (!is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), null);
            foreach ($iterator as $fileInfo) {

                $pathname = $fileInfo->getPathName();

                $local = $this->normalizeFileName($pathname);

                if (!$local) {
                    continue;
                }

                if ($fileInfo->isDir()) {
                    $zipArchive->addEmptyDir($local);
                } else {
                    $zipArchive->addFile($pathname, $local);
                }
                $checksumInformation[] = $local;
            }
        }

        $zipArchive->addFromString('checksum.json', json_encode($checksumInformation, JSON_PRETTY_PRINT));
        $zipArchive->addFromString('package.json', json_encode($packageInformation, JSON_PRETTY_PRINT));

        $zipArchive->close();

        return $result;
    }

    /**
     * Normalize filename: Directory => "/", strip parent of "PF.Base"
     *
     * @param $filename
     *
     * @return string
     */
    private function normalizeFileName($filename)
    {
        $path = substr($filename, strlen($this->getRootPath()));
        $path = trim($path, DIRECTORY_SEPARATOR);

        /**
         *
         */
        foreach ($this->excludes as $exclude) {
            if (strpos($path, $exclude)) {
                return false;
            }
        }

        /**
         *
         */
        if (substr($path, -1) == '.') {
            return false;
        }

        return str_replace('\\', '/', $path);
    }

    /**
     * @param string $targetZipFilename
     * @param array  $paths
     * @param array  $tempContents
     * @param array  $packageInformation
     *
     * @return array
     */
    public function export($targetZipFilename, $paths, $tempContents, $packageInformation = [])
    {
        $checksumInformation = [];

        if (file_exists($targetZipFilename)) {
            if (!@unlink($targetZipFilename)) {
                exit(sprintf('Unable write to "%s"', $targetZipFilename));
            }
        }

        if (is_string($paths)) {
            $paths = [$paths];
        }

        if (!is_dir($targeDir = dirname($targetZipFilename))) {
            mkdir($targeDir, 0777, true);
            chmod($targeDir, 0777);

        }

        $zipArchive = new \ZipArchive();
        $zipArchive->open($targetZipFilename, \ZipArchive::CREATE);

        $result = [];
        foreach ($paths as $path) {

            $path = realpath($path);

            if (is_file($path)) {

                $local = $this->normalizeFileName($path);
                $zipArchive->addFile($path, $local);
                $checksumInformation[] = $local;

                continue;
            }


            if (!is_dir($path)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), null);
            foreach ($iterator as $fileInfo) {

                $pathname = $fileInfo->getPathName();

                $local = $this->normalizeFileName($pathname);

                if (!$local) {
                    continue;
                }

                if ($fileInfo->isDir()) {
                    $zipArchive->addEmptyDir($local);
                } else {
                    $zipArchive->addFile($pathname, $local);
                }
                $checksumInformation[] = $local;
            }
        }

        $tempContents['package.json'] =  json_encode($packageInformation, JSON_PRETTY_PRINT);

        foreach ($tempContents as $local => $content) {
            $local = trim(str_replace('\\', '/', $local), '/');
            $zipArchive->addFromString($local, $content);
            $checksumInformation[] = $local;
        }

        $checksumInformation[] =  'checksum.json';
        $zipArchive->addFromString('checksum.json', json_encode($checksumInformation, JSON_PRETTY_PRINT));


        $zipArchive->close();

        return $result;
    }
}
