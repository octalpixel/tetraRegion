<?php
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH. "libraries/Requests.php";

 class Api extends REST_Controller{


    public function base64toUrl($baseString){
            
            $fileName = "assets/" . rand() . ".jpg";
            file_put_contents("./" . $fileName,base64_decode($baseString));
            return base_url($fileName);
            //return $fileName;
    }

    public function search_get(){
        //$this->response("this is a test");
        
    }


    public function search_post(){

            $baseImage = $this->post();
            //$this->response($baseImage['base']);            
            $imgUrl = $this->base64toUrl($baseImage['base']);
            $this->response($imgUrl);
            $data =  $this->matchFinder($imgUrl);
            $this->response($data);

    }

    public function matchFinder($imgURL){
        

        //$imgURL = "https://image.ibb.co/gBqWsF/Whats_App_Image_2017_08_12_at_7_48_57_PM.jpg";
        //$imgURL = "https://image.ibb.co/kiuwRa/Whats_App_Image_2017_08_12_at_9_53_48_PM.jpg";

        Requests::register_autoloader();

        //$this->response("Test");
        $url = 'https://westcentralus.api.cognitive.microsoft.com/vision/v1.0/analyze?visualFeatures=Categories,Description,Color';
        $headers = array('Content-Type' => 'application/json','Ocp-Apim-Subscription-Key'=> "56badec883f04a1e8a01638ace97e24e");
        $data = array('url' => $imgURL);
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
        //$this->response(max($body['Predictions']['Probability']));
        
       $getCount = count($body['Predictions']);
    
        $predictionresults = $body['Predictions'];
        $maxResults = array();
        $maxValue = 0;
        $maxTag = "";

       
       for( $x = 0; $x < $getCount;$x++){
            $current = $predictionresults[$x];
            if($current['Tag'] !="front" && $current['Tag'] != "back"){

                    if($current['Probability'] > $maxValue){
                        $maxValue = $current['Probability'];
                        $maxTag = $current['Tag'];
                    }

            }

       }

        $frontSide = 0;
        $backSide = 0;

        for( $x = 0; $x < $getCount;$x++){
            $current = $predictionresults[$x];
            if($current['Tag'] == "front"){
                $frontSide = $current['Probability'];
            }

            if($current['Tag'] == "back"){
                $backSide = $current['Probability'];
            }
        }

        $sideResult =array();
        if($backSide > $frontSide){
            $sideResult['side'] = "back";
            $sideResult['Probability'] = $backSide;
        }else{

            $sideResult['side'] = "front";
            $sideResult['Probability'] = $frontSide;
        }
       
        
        $maxResults['tag'] = $maxTag;
        $maxResults['probability'] = $maxValue;

        //$this->response(array('type'=>$maxResults ,"side"=>$sideResult));


        //$this->response($returnTags);
        
        /*
            brainstroming idea => the max results probabiliy is less tat 0.8 , the loop throught he others for matches greater that 0.5 and also get the results


        */
        $productResults = array();
        if($maxResults['probability']>0.8){

            $products=$this->products_model->getProductByImage($maxResults['tag']);
            $productResults['product'] = $products;

        }else{
            $products=$this->products_model->getProductByImage($maxResults['tag'],true);
        }

        $productResults['matchResult'] = array('tag'=>$maxResults ,"side"=>$sideResult,'type'=>$categories);


        $this->response($productResults);

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