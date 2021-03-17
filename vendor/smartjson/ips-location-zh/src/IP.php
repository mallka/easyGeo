<?php

namespace SmartJson\IpLocationZh;

class IP
{
    private static $ip = NULL;

    private static $fp = NULL;

    public static function find($ip, $language = 'CN')
    {
        if (empty($ip) === TRUE) {
            return 'N/A';
        }

        $baseStation = self::init();
        return $baseStation->find($ip, $language);
    }

    public static function findInfo($ip, $language = 'CN')
    {
        if (empty($ip) === TRUE) {
            return 'N/A';
        }

        $baseStation = self::init();
        return $baseStation->findInfo($ip, $language);
    }

    public static function findMap($ip, $language = 'CN')
    {
        if (empty($ip) === TRUE) {
            return 'N/A';
        }

        $baseStation = self::init();
        return $baseStation->findMap($ip, $language);
    }

    private static function init()
    {
        try {

            $ipdb_file = __DIR__ . '/ipipfree.ipdb';

            return new BaseStation($ipdb_file);

        } catch (\Exception $exception) {
            throw new \Exception("IPIP DB NOT EXISTS.");
        }
    }

}