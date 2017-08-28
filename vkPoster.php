<?php

namespace vk;

require_once 'loadPoster.php';

date_default_timezone_set('Europe/Moscow');

class VKPoster {

    use sendParams;

    const EXAMPLE_TIME = '2017 15/10 15:00';

    private $unixTimes = [];

    private $texts = [];
    private $attachments = [];

    private $group_id;
    protected $tokens;

    public function __construct(array $tokens, $group_id, array $posts) {

        $pieces = function ($part) use ($posts) {
            return array_values(array_map(function ($post) use ($part) {
                return $post[$part];
            }, $posts));
        };

        $this->texts = $pieces(1);
        foreach ($this->texts as $text) {
            if (!is_string($text) && !is_null($text)) {
                throw new vkPosterException(json_encode($text) . ' - no string');
            }
        }

        $this->attachments = $pieces(2);
        foreach ($this->attachments as $attachment) {
            if (!is_array($attachment)) {
                throw new vkPosterException(json_encode($attachment) . ' - no array');
            }
        }

        $this->tokens = $tokens;
        $this->group_id = $group_id;

        $this->convertUnixTime($pieces(0));
    }

    private function convertUnixTime (array $times) {

        if (empty($times)) {
            throw new vkPosterException (
              'Empty $times array'
            );
        }

        unset($this->unixTimes);

        foreach ($times as $time) {

            if ($time === null) {
                $this->unixTimes[] = null;
                continue;
            }

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
            $this->API_sendPost(
                $this->texts[$i],
                $this->attachments[$i],
                $this->unixTimes[$i]
            );
        }
    }

    private function API_sendPost ($text, array $attachments, $time) {
        $params = [
            'owner_id' => '-'.$this->group_id,
            'from_group' => 1,
            'message' => $text,
            'attachments' => implode(',', $attachments),
            'publish_date' => $time,
        ];

        $this->API_sendParams('wall.post', $params);
    }
}