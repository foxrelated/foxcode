<?php
namespace Core\Installation;

class FileSystem extends Vfs
{
    /**
     * FileSystem constructor.
     *
     * @param null $param
     */
    public function __construct($param = null)
    {

    }

    /**
     * @return bool
     */
    public function verify()
    {
        $this->verified = true;

        return true;
    }

    /**
     * @return bool
     */
    public function connect()
    {
        return true;
    }

    /**
     * @param string $file_path
     * @param string $to_file_path
     *
     * @return array
     */
    public function up($file_path, $to_file_path = null)
    {

        if (null == $to_file_path)
            $to_file_path = $file_path;

        // skips package.json, checksum.json
        if (in_array($file_path, ['package.json', 'checksum.json'])) {
            return [true, null];
        }

        $fromPath = $this->from_path . PHPFOX_DS . $file_path;
        $toPath = $this->to_path . PHPFOX_DS . $to_file_path;
        $base = dirname($toPath);

        if (!is_dir($base)) {
            mkdir($base, 0755, true);
            chmod($base, 0755);
        }

        // check file exists
        if (file_exists($toPath)) {
            if (!@unlink($toPath)) {
                throw new \RuntimeException(sprintf('Can not open "%s" to overwrite', $toPath));
            }
        }

        if (@copy($fromPath, $toPath)) {
            return [true, $this->error];
        } else {
            throw new \InvalidArgumentException("Can not write to file " . $toPath);
        }
    }

    public function deleteDir($path)
    {
        $realPath = realpath($path);
        if (!is_dir($realPath)) {
            return false;
        }
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($realPath), null);

        foreach ($iterator as $fileInfo) {
            $this->deleteFile($fileInfo);
        }
    }

    public function deleteFile($file_path)
    {
        if (isset($this->to_path)) {
            $toPath = $this->to_path . PHPFOX_DS . $file_path;
        } else {
            $toPath = $file_path;
        }
        if (!file_exists($toPath)) {
            return false;
        }

        return (unlink($toPath)) ? true : false;
    }

    public function deleteSingFolder($path)
    {
        if (!is_dir($path)) {
            return false;
        }

        return rmdir($path) ? true : false;
    }
}