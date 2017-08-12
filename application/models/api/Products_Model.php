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

        //clean string by removing "product_" when giving out
        return $products;

    }

    public function getProductById($index){

        $query = $this->db->get_where("products",array("product_id"=>$index));
        return $query->row_array();
        
    }


    public function hasProduct($index){

        $query = $this->db->get_where("products",array("product_id"=>$index));
        if($this->db->affected_rows()>0){
            return true;
        }

        return false;

    }

}