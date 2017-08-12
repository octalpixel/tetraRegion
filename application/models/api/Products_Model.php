<?php

class Products_Model extends CI_Model{


    public function __construct(){
        $this->load->database();
    }


    public function getAllProducts(){

        $productArray = $this->db->get("products")->result_array();
        $products= array();
        foreach($productArray as $product){

            unset($product['showroom_id']);
            $product['product_image'] = base_url() . "assets/products/" . $product['product_image'];
            $products[] = $product;

        }

        return $products;

    }



}