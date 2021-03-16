<?php


namespace mallka\easygeo;
use MaxMind\Db\Reader;


class Geo
{
    public $cityDb = '';
    public $asnDb='';

    private $cityObj = null;
    private $asnObj = null;

    public function __construct($cityDbFile,$asnDbFile)
    {
        $this->cityDb = $cityDbFile;
        $this->asnDb = $asnDbFile;
        if(!file_exists($this->cityDb) || !file_exists($this->asnDb)){
            throw  new \Exception('Miss db files');
        }

        $this->cityObj = new Reader($this->cityDb);
        $this->asnObj = new Reader($this->asnObj);
        return $this;
    }

    public function get($ip){
        $cityData = $this->cityObj->get($ip);
        $asnData = $this->asnObj->get($ip);

        var_dump($cityData);
        var_dump($asnData);
    }


}
