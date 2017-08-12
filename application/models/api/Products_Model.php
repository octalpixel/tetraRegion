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

    


    public function getProductByImage($tag,$hasMore =false){

        $results = $this->db->get_where("products",array("product_tags"=>$tag));

        if(!$hasMore){
            $item = $results->row_array();
            $product =array();
            $product['item'] =$item;
            $showroom = $this->getShowroomById($item['showroom_id']);
            $product['showroom']=$showroom;

            return $product;

        }else{
            //show this out also not done;
            return $results->result_array();
        }



    }



    public function getShowroomById($showroom_id){

            //assuming that store is there,check is done in the controller

            $showroom = $this->db->get_where("showrooms",array("showroom_id"=>$showroom_id));
            return $showroom->row_array();

    }

    public function hasProduct($index){

        $query = $this->db->get_where("products",array("product_id"=>$index));
        if($this->db->affected_rows()>0){
            return true;
        }

        return false;

    }

}