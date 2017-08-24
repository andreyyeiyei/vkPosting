<?php

function photo ($token, $group_id) {
$params = [
	'group_id' => $group_id
];

    $params['v'] = '5.63';
    $params['access_token'] = $token;
    
    $method = 'https://api.vk.com/method/photos.getWallUploadServer?';
    
    return json_decode(file_get_contents($method.http_build_query($params)));
}