<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH. "libraries/Requests.php";

 class Api extends REST_Controller{


    public function index_get(){
        Requests::register_autoloader();

        //$this->response("Test");
        $url = 'https://westcentralus.api.cognitive.microsoft.com/vision/v1.0';
        $headers = array('Content-Type' => 'application/json','Ocp-Apim-Subscription-Key'=>'e9066739808947bd82a6712c02e1e91b');
        $data = array('url' => 'https://nationalzoo.si.edu/sites/default/files/animals/africanlion-005_0.jpg');
        $response = Requests::post($url, $headers, json_encode($data));
        var_dump($response);
        //$this->response($response);
    }


    public function products_get($index){
        if($index == "all"){
            $this->response($this->products_model->getAllProducts());
        }


        if(is_numeric($index)){

            if($this->products_model->hasProduct($index)){

                    $product = $this->products_model->getProductById($index);
                    $this->response($product);

            }else{
                $this->response(array("success"=> false,"msg"=>"Product Not Found"));
            }


        }else{
            $this->response(array("success"=>false,"msg"=>"Invalid Request"));
        }


    }


 }