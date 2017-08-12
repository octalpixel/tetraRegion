<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH. "libraries/Requests.php";

 class Api extends REST_Controller{


    public function index_get(){
        Requests::register_autoloader();

        //$this->response("Test");
        $url = 'https://westcentralus.api.cognitive.microsoft.com/vision/v1.0/analyze?visualFeatures=Categories,Description,Color';
        $headers = array('Content-Type' => 'application/json','Ocp-Apim-Subscription-Key'=> "56badec883f04a1e8a01638ace97e24e");
        $data = array('url' => 'https://riverisland.scene7.com/is/image/RiverIsland/291357_main?$CrossSellProductPage514$');
        $response = Requests::post($url, $headers, json_encode($data));
        //var_dump($response);
        
        //Convert JSON to Array : because otherwise it gain makes it a string when sending response
        $response =json_decode($response->body,true);

        $tags= $response['description']['tags'];
        //$this->response($response['description']);
        $allowedTag = array("t-shirt" ,"shirt","trouser","jeans","pants");
        
        $returnTags  =array();
        foreach($tags as $tag){
                if(in_array($tag,$allowedTag)){
                    $returnTags[] =$tag;
                }
        }
        
        $this->response($returnTags);

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