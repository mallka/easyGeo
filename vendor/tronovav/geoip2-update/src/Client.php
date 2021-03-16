<?php

/*
 * This file is part of tronovav\GeoIP2Update.
 *
 * (c) Andrey Tronov
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace tronovav\GeoIP2Update;

/**
 * Class Client
 * @package tronovav\GeoIP2Update
 */
class Client
{

    /**
     * @var string Your account’s actual license key in www.maxmind.com.
     * @link https://support.maxmind.com/account-faq/license-keys/where-do-i-find-my-license-key/
     */
    public $license_key;

    /**
     * @var string[] Database editions list to update.
     */
    public $editions = array(
        'GeoLite2-ASN',
        'GeoLite2-City',
        'GeoLite2-Country',
    );

    /**
     * @var string Destination directory. Directory where your copies of databases are stored. Your old databases will be updated there.
     */
    public $dir;

    /**
     * The type of databases being updated. May be "mmdb" or "csv". If not specified, the mmdb type is the default.
     * @var string
     */
    public $type = 'mmdb';

    /**
     * Temporary directory for updating. By default the directory obtained by the sys_get_temp_dir() function.
     * @var string
     */
    public $tmpDir;

    private $urlApi = 'https://download.maxmind.com/app/geoip_download';
    private $updated =array();
    private $errors =array();
    private $remoteEditions = array(
        'GeoLite2-ASN',
        'GeoIP2-ASN',

        'GeoLite2-City',
        'GeoIP2-City',

        'GeoLite2-Country',
        'GeoIP2-Country',
    );
    private $remoteTypes = array(
        'mmdb' => 'tar.gz',
        // TODO implement csv extension
        //'csv' => 'zip',
    );

    public function __construct($params = array())
    {
        $thisClass = new \ReflectionClass($this);
        foreach ($params as $key => $value)
            if($thisClass->hasProperty($key) && $thisClass->getProperty($key)->isPublic())
                $this->$key = $value;
            else
                $this->errors[] = "Parameter \"{$key}\" does not exist.";
    }

    /**
     * @return array
     */
    public function updated(){
        return $this->updated;
    }

    /**
     * Critical update errors.
     * @return array
     */
    public function errors(){
        return $this->errors;
    }

    /**
     * Database update launcher.
     */
    public function run(){

        $this->tmpDir = !empty($this->tmpDir) ?$this->tmpDir : sys_get_temp_dir();

        if(!is_dir($this->tmpDir) || !is_writable($this->tmpDir))
            $this->errors[] = sprintf("Temporary directory %s.",(empty($this->tmpDir) ? "not specified" : "{$this->tmpDir} is not writable"));

        if(!is_dir($this->dir) || !is_writable($this->dir))
            $this->errors[] = sprintf("Destination directory %s.",(empty($this->dir) ? "not specified" : "$this->dir is not writable"));

        if(empty($this->type) || !array_key_exists($this->type,$this->remoteTypes))
            $this->errors[] = sprintf("Database type %s.",(empty($this->type) ? "not specified" : "$this->type does not exist"));

        if(empty($this->license_key))
            $this->errors[] = "You must specify your license_key https://support.maxmind.com/account-faq/license-keys/where-do-i-find-my-license-key/";

        $editionsForUpdate = array_intersect($this->editions,$this->remoteEditions);
        if(empty($editionsForUpdate))
            $this->errors[] = "No revision names are specified for the update.";

        if(!empty($this->errors))
            return;

        foreach ($editionsForUpdate as $edition){
            $this->updateEdition($edition);
        }
    }

    private function updateEdition($editionId){
        $newFileRequestHeaders = $this->request(array(
            'edition_id' => $editionId
        ));

        if(empty($newFileRequestHeaders['content-disposition'])){
            $this->errors[] = "{$editionId}.{$this->type} not found in maxmind.com";
            return;
        }
        preg_match('/filename=(?<attachment>[\w.\d-]+)$/' ,$newFileRequestHeaders['content-disposition'][0],$matches);
        $newFileName = $this->tmpDir.DIRECTORY_SEPARATOR.$matches['attachment'];

        $remoteFileLastModified = date_create($newFileRequestHeaders['last-modified'][0])->getTimestamp();
        $localFileLastModified =
            is_file($this->dir.DIRECTORY_SEPARATOR.$editionId.'.'.$this->type.'.last-modified') ?
                (int)file_get_contents($this->dir.DIRECTORY_SEPARATOR.$editionId.'.'.$this->type.'.last-modified') : 0;

        $oldFileName = $this->dir.DIRECTORY_SEPARATOR.$editionId.'.'.$this->type;

        if(!is_file($oldFileName) || $remoteFileLastModified !== $localFileLastModified){

            if(is_file($newFileName))
                unlink($newFileName);

            $this->request(array(
                'edition_id' => $editionId,
                'save_to' => $newFileName,
            ));

            $phar = new \PharData($newFileName);
            $phar->extractTo($this->tmpDir, null, true);

            $iterator = new \FilesystemIterator(substr($newFileName,0,-7));

            foreach ($iterator as $fileIterator)
                if($fileIterator->isFile() && $fileIterator->getExtension() === $this->type)
                    rename($fileIterator->getPathname(),$this->dir.DIRECTORY_SEPARATOR.$fileIterator->getFilename());
                else
                    unlink($fileIterator->getPathname());

            rmdir($iterator->getPath());
            unlink($newFileName);

            file_put_contents($this->dir.DIRECTORY_SEPARATOR.$editionId.'.'.$this->type.'.last-modified',$remoteFileLastModified);
            $this->updated[] = "$editionId.{$this->type} has been updated.";
        }
        else
            $this->updated[] = "$editionId.{$this->type} does not need to be updated.";
    }

    /**
     * @param array $params
     * @return array|void
     */
    private function request($params = array()){
        $url = $this->urlApi.'?'.http_build_query(array(
                'edition_id'=>!empty($params['edition_id']) ? $params['edition_id'] : '',
                'suffix'=>$this->remoteTypes[$this->type],
                'license_key' => $this->license_key,
            ));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        if(!empty($params['save_to'])){
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $fh = fopen($params['save_to'], 'w');
            curl_setopt($ch, CURLOPT_FILE, $fh);
            curl_exec($ch);
            curl_close($ch);
            fclose($fh);
        }
        else{
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $header = curl_exec($ch);
            curl_close($ch);
            return $this->parseHeaders($header);
        }
    }

    /**
     * @param string $header
     * @return array
     */
    private function parseHeaders($header)
    {
        $lines = explode("\n",$header);
        $headers = array();
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            $headers[strtolower(trim($parts[0]))][] = isset($parts[1]) ? trim($parts[1]) : null;
        }
        return $headers;
    }
}
