<?php
include "authy.php";

function gerarUsuarioTeste($access_token){
    $url = "https://api.mercadolibre.com/users/test_user";

    $headers = array(
        "Authorization: Bearer {$access_token}",
        "Content-Type: application/json"
    );
    
    $params = array('site_id' => 'MLB');
    
    $curl = curl_init();
    
    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($curl,CURLOPT_POST,1);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($params));
    
    $curlResponse = json_decode(curl_exec($curl),1);
    
    curl_close($curl);
    
    return $curlResponse;
}

//executa e printa o retorno da função

print_r(gerarUsuarioTeste($access_token));

//Retorna o array com as informações

/*Array (   [id] => xxx
            [nickname] => xxx
            [password] => xxx
            [site_status] => xxx
            [email] => xxx )*/