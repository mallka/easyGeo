<?php

namespace mallka\easygeo;

interface Provider{
    public function get($ip);
    
    public function update();
    
    public function init($config);
}