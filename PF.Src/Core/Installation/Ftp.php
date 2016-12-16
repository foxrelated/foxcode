<?php
namespace Core\Installation;


class Ftp extends Vfs
{
    /**
     * @var
     */
    private $connect_id;
    /**
     * @var
     */
    private $connect_status;

    /**
     * Fpt constructor.
     *
     * @param $param
     */
    public function __construct($param)
    {
        if (isset($param['port']) && ($param['port'] != 'auto')) {
            $this->port = $param['port'];
        } else {
            $this->port = 21;
        }

        if (isset($param['host'])) {
            $this->host = $param['host'];
        } else {
            $this->host = 'localhost';
        }

        if (isset($param['user'])) {
            $this->user = $param['user'];
        } else {
            $this->error = _p('Please set user name');
        }

        if (isset($param['pass'])) {
            $this->pass = $param['pass'];
        } else {
            $this->error = _p('Please set password');
        }
    }

    /**
     * @param $ftpCon
     * @param $ftpBaseDir
     * @param $ftPath
     */
    private function ftp_mkSubDirs($ftpCon, $ftpBaseDir, $ftPath)
    {
        $ftpBaseDir = $this->correctFtpPath($ftpBaseDir);
        @ftp_chdir($ftpCon, $ftpBaseDir);
        $ftPath = str_replace($ftpBaseDir, '', $ftPath);
        $parts = explode('/', $ftPath);
        foreach ($parts as $part) {
            if (empty($part)) {
                continue;
            }
            if (!@ftp_chdir($ftpCon, $part)) {
                ftp_mkdir($ftpCon, $part);
                ftp_chdir($ftpCon, $part);
                @ftp_chmod($ftpCon, $this->folderPermission, $part);
            }
        }
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @return array
     */
    public function verify()
    {
        if ($this->error) {
            return [false, $this->error];
        } else {
            $this->verified = true;

            return [true, $this->connect_status];
        }
    }

    /**
     * @return bool
     */
    public function connect()
    {
        $this->connect_id = ftp_connect($this->host, $this->port);
        if ($this->connect_id === false)
        {
            return false;
        }
        // login with username and password
        $this->connect_status = ftp_login($this->connect_id, $this->user, $this->pass);
        if (!$this->connect_status && function_exists('ftp_ssl_connect')) {
            $this->connect_status = ftp_ssl_connect($this->connect_id, $this->user, $this->pass);
        }
        //Update $root_path in case ftp only have relative path
        $testPath = PHPFOX_DIR_CACHE . md5(PHPFOX_TIME);
        mkdir($testPath);
        $testPath = trim($testPath, PHPFOX_DS);
        $aTestPath = explode(PHPFOX_DS, $testPath);
        $this->root_path = '/';
        foreach ($aTestPath as $iKey => $path){
            $FtpDir = '/' . implode($aTestPath, '/');
            if ($this->ftp_is_dir($FtpDir)){
                $this->root_path = '/' . trim($this->root_path, '/');
                break;
            }
            $this->root_path .= trim($path, PHPFOX_DS) . '/';
            unset($aTestPath[$iKey]);
        }
        if ($this->root_path == ($testPath . '/')){
            $this->root_path = '';
        }
        rmdir(PHPFOX_DIR_CACHE . md5(PHPFOX_TIME));
        return ($this->connect_status) ? true : false;
    }

    /**
     *
     */
    private function close()
    {
        if (isset($this->connect_id) && $this->connect_id !== false) {
            ftp_close($this->connect_id);
        }
    }

    /**
     * @param        $file_path
     * @param string $to_file_path
     *
     * @return array
     */
    public function up($file_path, $to_file_path = null)
    {
        if(null == $to_file_path){
            $to_file_path =  $file_path;
        }

        $bReturn = true;
        $fromPath = realpath($this->from_path . PHPFOX_DS . $file_path);
        $toPath = $this->to_path . PHPFOX_DS . $to_file_path;

        /*Create folder on server*/
        $createPath = dirname($toPath);
        $basePath = dirname(PHPFOX_DIR);
        $this->ftp_mkSubDirs($this->connect_id, $basePath, $createPath);
        $valid = false;

        /*Up file to server*/
        try {
            if (ftp_put($this->connect_id, $toPath, $fromPath, FTP_BINARY)) {
                $msg = _p("Successfully copied") . $file_path;
                $valid = true;
            }
        } catch (\Exception $ex) {
            throw new \RuntimeException($ex->getMessage());
        }


        if (!$valid) {
            if (file_exists($toPath)) {
                @unlink($toPath);
            }
            $toPathDir = dirname($toPath);
            if (file_exists($fromPath) && is_dir($toPathDir)){
                @copy($fromPath, $toPath);
                $valid = true;
            }
        }

        if (!$valid) {
//            Prefer throw new exception than return false
            throw new \RuntimeException(sprintf('Can not put from "%s"=> "%s"', $fromPath, $toPath));

        }
        if (!isset($msg)){
            $msg = '';
        }
        return [$bReturn, $msg];
    }

    public function deleteDir($path)
    {
        @$files = ftp_nlist($this->connect_id, $path);
        if (count($files)) {
            foreach ($files as $file) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                if ($this->ftp_is_dir($file)) {
                    $this->deleteDir($file);
                } else {
                    $this->deleteFile($file);
                }
            }
        }
        $this->deleteSingFolder($path);
    }

    private function ftp_is_dir($dir)
    {
        //DO NOT CORRECT FTP PATH THIS FUNCTION
        $pushDir = ftp_pwd($this->connect_id);
        if (@ftp_chdir($this->connect_id, $dir)) {
            ftp_chdir($this->connect_id, $pushDir);

            return true;
        }

        return false;
    }

    public function deleteFile($file_path)
    {
        $bReturn = true;
        if (isset($this->to_path)) {
            $toPath = $this->to_path . PHPFOX_DS . $file_path;
        } else {
            $toPath = $file_path;
        }

        /*Delete file to server*/
        if (ftp_delete($this->connect_id, $toPath)) {
            $msg = _p("Successfully deleting") . $file_path;
        } else {
            $bReturn = false;
            $msg = _p("There was a problem while deleting") . $file_path;
        }
        //delete folder if empty
        $this->deleteSingFolder($file_path);

        return [$bReturn, $msg];
    }

    /**
     * @param $dirPath
     *
     * @return bool
     */
    public function deleteSingFolder($dirPath)
    {
        if (!$this->ftp_is_dir($dirPath)) {
            return false;
        }

        return (@ftp_rmdir($this->connect_id, $dirPath)) ? true : false;
    }
}