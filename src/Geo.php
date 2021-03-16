<?php

namespace mallka\easygeo;

use Exception;
use MaxMind\Db\Reader;
use tronovav\GeoIP2Update\Client;

class Geo
{
    
    private $cityObj = null;
    private $asnObj  = null;
    
    public function __construct($cityDbFile, $asnDbFile)
    {
        if (!file_exists($cityDbFile) || !file_exists($asnDbFile)) {
            throw  new Exception('Miss db files');
        }
        
        $this->cityObj = new Reader($cityDbFile);
        $this->asnObj  = new Reader($asnDbFile);
        return $this;
    }
    
    /**
     *
     * update ipdb file ,
     *
     * @param          $license_key get the key from maxmind account
     * @param          $dir         your db file floder path
     *
     * @return array|bool
     */
    public static function update($license_key, $dir): bool
    {
        $client = new Client([
            'license_key' => $license_key,
            'dir'         => $dir,
            'editions'    => ['GeoLite2-ASN', 'GeoLite2-City'],
            'type'        => 'mmdb',
        ]);
        $client->run();
        
        return !empty($client->errors()) ? false : true;
        
        //Update success:
        //Array
        //(
        //    [0] => GeoLite2-ASN.mmdb has been updated.
        //    [1] => GeoLite2-City.mmdb has been updated.
        //)
        
        //  print_r($client->updated());
        // Will  get  an array with error info when updated fail
        //        print_r($client->errors());
    }
    
    /**
     * method 1(static ): get extra info with an ip given
     *
     * @param $ip
     * @param $cityDb  file path string
     * @param $asnDb   file path string
     * @param $lang    default is zh-CN
     *
     * @return array
     */
    public static function getInfo($ip, $cityDb, $asnDb,$lang = 'zh-CN'):array
    {
        $obj = new Geo($cityDb,$asnDb);
        return $obj->get($ip,$lang);
    }
    
    /**
     * method 2: get extra info with an ip given
     *
     * @param        $ip
     * @param string $lang
     *
     * @return array
     * @throws Reader\InvalidDatabaseException
     */
    public function get($ip, $lang = 'zh-CN'): array
    {
        $cityData = $this->cityObj->get($ip);
        $asnData  = $this->asnObj->get($ip);
        
        $this->cityObj->close();
        $this->asnObj->close();
        
        $data                              = array_merge($cityData, $asnData);
        $res                               = [];
        $res['continent']                  = isset($data['continent']['names'][$lang]) ? $data['continent']['names'][$lang] : (isset($data['continent']['names']['en']) ? $data['continent']['names']['en'] : '');
        $res['country']                    = isset($data['country']['names'][$lang]) ? $data['country']['names'][$lang] : (isset($data['country']['names']['en']) ? $data['country']['names']['en'] : '');
        $res['registered_country']         = isset($data['registered_country']['names'][$lang]) ? $data['registered_country']['names'][$lang] : (isset($data['registered_country']['names']['en']) ? $data['registered_country']['names']['en'] : '');
        $res['country_code']               = $data['country']['iso_code'];
        $res['province']                   = isset($data['subdivisions'][0]['names'][$lang]) ? $data['subdivisions'][0]['names'][$lang] : (isset($data['subdivisions'][0]['names']['en']) ? $data['subdivisions'][0]['names']['en'] : '');
        $res['province_code']              = $data['subdivisions'][0]['iso_code'];
        $res['city']                       = isset($data['city']['names'][$lang]) ? $data['city']['names'][$lang] : (isset($data['city']['names']['en']) ? $data['city']['names']['en'] : '');
        $res['location"']                  = $data['location'];
        $res['autonomous_system_number""'] = 'AS' . $data['autonomous_system_number'];
        $res['organization"']              = $data['autonomous_system_organization'];
        
        return $res;
    }
    
}
