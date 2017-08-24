<?php

namespace classes;

require_once 'vkPosterException.php';

date_default_timezone_set('Europe/Moscow');

class vkPoster {

    const LINK = 'https://api.vk.com/method/';
    const VERSION = 5.68;

    const EXAMPLE_TIME = '2017 13/10 15:00';

    private $unixTimes = [];
    private $texts = [];

    private $group_id;
    private $tokens;

    public function __construct(array $tokens, $group_id, array $posts) {

        $pieces = function ($part) use ($posts) {
            return array_values(array_map(function ($post) use ($part) {
                return $post[$part];
            }, $posts));
        };

        $this->texts = $pieces(0);

        $this->tokens = $tokens;
        $this->group_id = $group_id;

        $this->convertUnixTime($pieces(1));
    }

    private function convertUnixTime (array $times) {

        if (empty($times)) {
            throw new vkPosterException (
              'Empty $times array'
            );
        }

        unset($this->unixTimes);

        foreach ($times as $time) {
            $unix = \DateTime::createFromFormat("Y d/m G:i", $time);
            if ($unix === false) {
                throw new vkPosterException(
                    'Not valid format: '. $time. PHP_EOL.
                    'Example: ' . self::EXAMPLE_TIME
                );
            }
            $this->unixTimes[] = $unix->getTimestamp();
        }
    }

    public function getUnixTimes () {
        return $this->unixTimes;
    }

    public function sending() {
        for ($i = 0; $i < count($this->texts); $i++) {
            $this->sendPost($this->texts[$i], $this->unixTimes[$i]);
        }
    }

    private function sendPost ($text, $time) {
        $params = [
            'owner_id' => '-'.$this->group_id,
            'from_group' => 1,
            'message' => $text,
            'publish_date' => $time,
        ];

        $this->sendParams('wall.post', $params);
    }

    private function sendParams ($method, $params) {
        $params['v'] = self::VERSION;
        $params['access_token'] = $this->getRandToken();

        $URL = self::LINK.$method.'?'.http_build_query($params);

        return json_decode(file_get_contents($URL));
    }

    private function getRandToken () {
        return $this->tokens[array_rand($this->tokens)];
    }
}