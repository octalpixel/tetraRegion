<?php

class Products_Model extends CI_Model{


    public function __construct(){
        $this->load->database();
    }


    public function getAllProducts(){

        return $this->db->get("products")->result_array();

    }



}