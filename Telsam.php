<?php

class Telsam{

    function __construct(){

        $this->telsamEndpoint = "http://websms.telsam.com.tr/xmlapi/sendsms";

    }

    function setUsername($username){
        $this->telsamUsername = $username;
    }

    function setPassword($password){
        $this->telsamPassword = $password;
    }

    function setOriginator($originator){
        $this->telsamOriginator = $originator;
    }

    function prepareXml($textToSend, $receiversToSend){

        $xml = new DOMDocument();
        
        $SMS = $xml->createElement("SMS");
        $SMS = $xml->appendChild($SMS);
        
        $authentication = $xml->createElement("authentication");
        $authentication = $SMS->appendChild($authentication);
        
        $authentication->appendChild($xml->createElement('username', $this->telsamUsername));
        $authentication->appendChild($xml->createElement('password', $this->telsamPassword));
        
        $message = $xml->createElement("message");
        $message = $SMS->appendChild($message);
        
        $message->appendChild($xml->createElement('originator', $this->telsamOriginator));

        $text = $message->appendChild($xml->createElement('text'));
        $text->appendChild($xml->createCDATASection($textToSend));

        $unicode = $message->appendChild($xml->createElement('unicode'));
        $international = $message->appendChild($xml->createElement('international'));
        $canceltext = $message->appendChild($xml->createElement('canceltext'));

        $message->appendChild($xml->createElement('originator', $this->telsamOriginator));

        $receivers = $xml->createElement("receivers");
        $receivers = $SMS->appendChild($receivers);

        foreach($receiversToSend as $receiver){

            $receivers->appendChild($xml->createElement('receiver', $receiver));

        }
        
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        
        return $xml->saveXML();

    }

    function sendSingle($number, $text){

        if(is_string($number) || is_int($number)){
            $getXml = $this->prepareXml($text, array($number));
            return $this->telsamPost($getXml);
        }
        else{
            return $this->telsamReturn("error", "sendSingle(int|string, string) parametre hatası!");
        }
        
    }

    function sendBulk($numbers, $text){

        if(is_array($numbers)){
            $getXml = $this->prepareXml($text, $numbers);
            return $this->telsamPost($getXml);
        }
        else{
            return $this->telsamReturn("error", "sendBulk(array, string) parametre hatası!");
        }
        
    }

    private function telsamPost($postData){
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->telsamEndpoint);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 5000);
        
        $result = curl_exec($ch);
        $error = curl_error($ch);

        if(strlen($error) > 0){
            return $this->telsamReturn("error", $error);
        }
        else{
            
            $result = simplexml_load_string($result);

            if(isset($result->status) && $result->status == "ERROR"){
                return $this->telsamReturn("error", $result->error_code . " - " . $result->error_description);
            }
            elseif(isset($result->status) && $result->status == "OK"){
                $successReturn = array(
                    "batch_id"  => $result->message_id,
                    "paid"      => $result->amount,
                    "balance"   => $result->credit
                );
                return $this->telsamReturn("success", "Başarılı", $successReturn);
            }
            else{
                return $this->telsamReturn("error", "Api cevabı anlaşılamadı!");
            }
            

        }
        
    }

    private function telsamReturn($status, $message, $data = array()){
        return array(
            "status" => $status,
            "message" => $message,
            "data" => $data
        );
    }

}