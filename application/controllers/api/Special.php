<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Special extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }


    public function vip_star(){
        echo 'IMBA SVIP';
        echo time();
    }
}
