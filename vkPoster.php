<?php

namespace classes;

require_once 'vkPosterException.php';

date_default_timezone_set('Europe/Moscow');

class vkPoster {

    const exampleTime = '2017 13/10 15:00';

    private $unixTimes = [];
    private $texts = [];
    private $pictures = [];

    private $group_id;
    private $tokens;

    public function __construct(array $tokens, $group_id, array $posts) {

        $pieces = function ($part) use ($posts) {
            return array_map(function ($post) use ($part) {
                return $post[$part];
            }, $posts);
        };

        $this->texts = $pieces(0);
        $this->pictures = $pieces(1);

        $this->tokens = $tokens;
        $this->group_id = $group_id;

        $this->convertUnixTime($pieces(2));
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
                    'Example: ' . self::exampleTime
                );
            }
            $this->unixTimes[] = $unix->getTimestamp();
        }
    }

    public function getUnixTimes () {
        return $this->unixTimes;
    }
}


try {
    $posts = [
        ['Текст какой-нибудь', 'img.jpg', '2017 13/10 15:00'],
        ['Текст ещё какой-нибудь', 'img2.jpg', '2017 13/10 16:00']
    ];

    $poster = new vkPoster($posts);
    echo json_encode($poster->getUnixTimes());
} catch (vkPosterException $e) {
    echo $e->getMessage();
}
