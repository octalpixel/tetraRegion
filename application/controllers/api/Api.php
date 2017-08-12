<?php
require APPPATH . 'libraries/REST_Controller.php';

 class Api extends REST_Controller{


    public function index_get(){
        //$this->response("Test");

        $this->response($this->products_model->getAllProducts());

    }


    public function products_get(){
        $this->response("this is test");
    }


 }