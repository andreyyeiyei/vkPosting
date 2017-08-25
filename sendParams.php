<?php

namespace classes;

trait SendParams {

    private $link = 'https://api.vk.com/method/';
    private $version = 5.68;

    protected $tokens = [];

    protected function API_sendParams ($method, $params) {
        $params['v'] = $this->version;
        $params['access_token'] = $this->getRandToken();

        $URL = $this->link.$method.'?';

        ///// CURL /////
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $json = curl_exec($ch);

        curl_close($ch);

        return json_decode($json);
    }

    protected function getRandToken () {
        return $this->tokens[array_rand($this->tokens)];
    }
}