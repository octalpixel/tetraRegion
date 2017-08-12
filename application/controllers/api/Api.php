<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH. "libraries/Requests.php";

 class Api extends REST_Controller{


    public function index_get(){
        

        $imgURL = "https://image.ibb.co/gBqWsF/Whats_App_Image_2017_08_12_at_7_48_57_PM.jpg";

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
        
        $categories=array();
        foreach($tags as $tag){
                if(in_array($tag,$allowedTag)){
                    $categories[] =$tag;
                }
        }
        

        $customUrl = "https://southcentralus.api.cognitive.microsoft.com/customvision/v1.0/Prediction/c5792f59-13f1-47eb-910c-025489d91fa5/url?iterationId=0106c87d-1978-41cf-9fde-545694fcbea1";
        $customHeader = array("Prediction-Key"=>" 776c6c0e4f0d4065aa348056241146e0" ,'Content-Type' => 'application/json');
        $imageUploadData = array("Url"=> $imgURL);

        $customResponse = Requests::post($customUrl, $customHeader, json_encode($imageUploadData));
        $body = json_decode($customResponse->body,true);
        $this->response($body);
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