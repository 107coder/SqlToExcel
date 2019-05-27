<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Action extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('Index_model','index');
    }
    public function index(){
        $sql = $this->input->post('sql');

        p($sql);
        $status = $this->index->insert($sql);

        p($status);
    }

    public function Output(){

    }
}