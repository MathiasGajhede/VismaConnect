<?php
class VismaConnect
{
    private $token;
    private $visma_client_id;
    private $visma_secret;
    private $access_token;
    private $uri;
    private $scope;
    function __construct($code,$client_id,$secret, $uri)
    {
        $this->token = $code;
        $this->visma_client_id = $client_id;
        $this->visma_secret = $secret;
        $this->uri = $uri;
    }
    public function update_token($code) {
        $this->token = $code;
    }
    public function Connect() {
        return $this->request("authorization_code","code");
    }
    public function Refresh() {
        return $this->request("refresh_token","refresh_token")->access_token;
    }
    public function UpdateScope($append_to_scope) {
        $scope = explode("+",$this->scope);
        $txt_scope = "";
        foreach($scope as $value)
            $txt_scope .= $value . "+";
        $this->scope = rtrim($append_to_scope."+".$txt_scope,"+");
    }
    public function requestUrl() {
        $nonce = $this->generate_code();
        $state = $nonce;
        return "https://connect.visma.com/connect/authorize?client_id=$this->visma_client_id&redirect_uri=$this->uri&response_type=code&nbsp;id_token&scope=$this->scope&response_mode=form_post&nonce=$nonce&state=$state";
    }
    private function generate_code() {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 20; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }
        return $randomString;
    }
    private function request($grant_type = "authorize_code", $type = "code") {
        $url = "https://connect.visma.com/connect/token";
        $fields = [];
        $fields["grant_type"] = $grant_type;
        $fields[$type] = $this->token;
        $fields["redirect_uri"] = $this->uri;
        $fields["client_id"] = $this->visma_client_id;
        $headers = array(
            'User-Agent: YourpayConnectClass',
            'cache-control: no-cache',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic '.base64_encode($this->visma_client_id . ":" . $this->visma_secret),
        );
        $fields_string = "";
        foreach($fields as $key=>$value)
            $fields_string .= $key ."=".$value."&";
        $fields_string = rtrim($fields_string,"&");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec ($ch);
        curl_close ($ch);
        return json_decode($server_output);
    }
}