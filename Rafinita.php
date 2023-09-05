<?php

class Rafinita
{

    protected $api_host = 'https://dev-api.rafinita.com/post';
    protected $public = '';
    protected $secret = '';
    private $defaultErrorMsg = 'Please wait a moment, our support team will contact you and help you complete the transaction';
    private $requestData;

    /**
     * Resolving response and checking signature on success
     * @param $response
     * @return array ['error_message'] OR ['redirect_url'] OR ['redirect_form'] OR ['pending'] OR ['success']
     */
    public function getResolvedResponse($response){
        $p = []; // prepared responce
        if($response){
            if($response['action']=='SALE'){
                if($response['result'] == 'REDIRECT' AND $response['status'] == '3DS'){
                    if($response['redirect_method'] == 'GET' AND $response['redirect_url']) {
                        // returns url to redirect
                        $p['redirect_url'] = $response['redirect_url'] . "?" . http_build_query($response['redirect_params']);
                    }elseif($response['redirect_method'] == 'POST' AND $response['redirect_url']){
                        // returns form for displaying payment button
                        $form = '<form method="POST" class="_redirect_form" action="'.$response['redirect_url'].'">';
                        foreach($response['redirect_params'] AS $name => $value){
                            $form .= '<input class="_redirect_input" type = "hidden" name="'.$name.'" value="'.$value.'" >';
                        }
                        $form .= '<input class="_redirect_submit" type = "submit"></form>';
                        $p['redirect_form'] = $form;
                    }else{
                        $p['error_message'] = $this->defaultErrorMsg;
                    }
                }
                if($response['result'] == 'SUCCESS'){
                    if($response['status'] == 'SETTLED'){
                        $p['success'] = $response['trans_id'];
                    }else{
                        // need to check it later by trans_id
                        $p['pending'] = $response['trans_id'];
                    }
                }
                if($response['result'] == 'DECLINED'){
                        $p['error_message'] = $response['decline_reason'];
                }
            }
        }else{
            $p['error_message'] = $this->defaultErrorMsg;
        }
        return $p;
    }
    public function sale($post){
        $data = $this->validate($post);
        return $this->query($data);
    }

    /**
     * we can call it to handle the callback
     * @param $data
     * @return array
     */
    public function callbackHandler($data){
        if($this->checkResponse($data));
        return $this->getResolvedResponse($data);
    }
    private function signRequest($data){
        return md5(strtoupper(strrev($data['payer_email']).$this->secret.strrev(substr($data['card_number'],0,6).substr($data['card_number'],-4))));
    }
    private function checkResponse($response){
        $r = $this->requestData;
        $signature = md5(strtoupper(strrev($r['email']).$this->secret.$response['trans_id'].strrev(substr($r['card_number'],0,6).substr($r['card_number'],-4))));
        if($signature == $response['hash']){
            return true;
        }
        return false;
    }
    protected function query($data) {
        $data['action'] = 'SALE';
        $data['client_key'] = $this->public;
        $data['payer_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['term_url_3ds'] = $_SERVER['HTTP_ORIGIN'].'/?success_order_id='.$data['order_id'];
        $data['hash'] = $this->signRequest($data);
        $this->saveRequestData($data);
        $this->log( 'REQUEST data: '.http_build_query($data));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_host);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $this->log( 'RESPONSE: '.$response);
        if(!$responseDecoded = json_decode($response, true)){
            $this->log( 'ERROR in: json_decode  ');
            return null;
        }
        return $responseDecoded;
    }
    private function validate($data){
        // todo
        return $data;
    }
    private function log($string) {
        file_put_contents('logs/rafinita_request.log', "\r\n".date('Y-m-d H:i:s').': '.$string, FILE_APPEND);
    }

    /*
     * if we want to check the 3ds response, we haft to save response in some deposit and get it on callback
     * for now just keep it here
     */
    private function saveRequestData($data)
    {
        $this->requestData = $data;
    }

}