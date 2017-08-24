<?php

$files = ['hhgg'];
$file = implode(",", $files);
echo $file;

$token = 'token';
$text = 'Тест';
$groups = '676';

$config = array(
  'owner_id' => $groups,
  'from_group' => '1',
  'message' => $text,
  'mark_as_ads' => '0',
  'attachments' => $file
);

//var_dump(send_request_params ('wall.post', $config));

//////////ФУНКЦИИ//////////

function send_request_params ($method, $params) {
    global $token;

    $params['v'] = '5.63';
    $params['access_token'] = $token;
    
    $method = 'https://api.vk.com/method/'.$method.'?';
    
    return json_decode(file_get_contents($method.http_build_query($params)));
}