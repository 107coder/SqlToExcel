<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index_model extends CI_Model{
    public function insert($sql){
        $status = $this->db->query($sql)->result_array();
        return $status;
    }
}