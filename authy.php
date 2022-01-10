<?php

function gerarAccessToken($paramsList){
    $url = "https://api.mercadolibre.com/oauth/token";
 
    $headers = array(
        "accept: application/json",
        "content-type: application/x-www-form-urlencoded"
    );
    
    $params = array(
        "grant_type" => "authorization_code",
        "client_id" =>  $paramsList['appId'],
        "client_secret" => $paramsList['clientSecret'],
        "code" => $paramsList['code'],
        "redirect_uri" => $paramsList['redirect_uri']
    );
    
    $curl = curl_init();

    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($curl,CURLOPT_POST,1);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl,CURLOPT_POSTFIELDS,http_build_query($params));
    
    $curlResponse = json_decode(curl_exec($curl),1);
    
    curl_close($curl);

    return $curlResponse;

}

function salvarRefreshToken($refreshTokenInfo){
    try{
        file_put_contents('logToken.json',json_encode($refreshTokenInfo));
    }catch(Exception $e){
        echo "Erro ao salvar o refresh token: " . $e->getMessage();
    }
}

function validaAccessToken($response){
    return array_key_exists('access_token',$response);
}

function getValidAccessToken(){
    $str = file_get_contents('logToken.json');
    $data = json_decode($str,1);

    if (isset($data['access_token'])){
        if (time() < $data['expires_timestamp'])
            return $data['access_token'];
    }else {
        return false;
    }
}

function getValidRefreshToken(){
    $str = file_get_contents('logToken.json');
    $data = json_decode($str,1);

    if (isset($data['refresh_token'])){
        return $data['refresh_token'];
    }else {
        return false;
    }
}

if (getValidAccessToken()){
    $access_token = getValidAccessToken();
    exit();
}else {
    if (getValidRefreshToken()){
        $authorizationCode = getValidRefreshToken();
    }else {
        //Defini o código de autorização manual apenas 1 vez
        $authorizationCode = "TG-61db87b79859c4001bbe7296-494138856";
    }
}

$appId = "5071444836265136";

$clientSecret = "rbQhY1VCGbVuju6iYFT0hwaktmUo19nl";

$redirect_uri = "https://www.mercadolivre.com.br/";

$paramsToGenerateAccessToken = array(
    "appId" => $appId,
    "clientSecret" => $clientSecret,
    "code" => $authorizationCode,
    "redirect_uri" => $redirect_uri
);

$response = gerarAccessToken($paramsToGenerateAccessToken);

if (validaAccessToken($response)){
    $refreshInfo = array(
        "access_token" => $response['access_token'],
        "refresh_token" => $response['refresh_token'],
        "expires_timestamp" =>  time() + 6*60*60
    );
    salvarRefreshToken($refreshInfo);
}else {
    echo "O token não foi gerado: " . $response['message'];
}

#URL de autenticação

#Exemplo - https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=$APP_ID&redirect_uri=$YOUR_URL

#ExemploCompleto = https://auth.mercadolivre.com.br/authorization?response_type=code&client_id=5071444836265136&redirect_uri=https://www.mercadolivre.com.br/
?>