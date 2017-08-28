<?php

namespace vk;

require_once 'loadPoster.php';

class UploadPhoto
{

    use sendParams;

    private $paths = [];
    private $group_id;

    protected $tokens;

    public function __construct(array $tokens, $group_id, array $paths) {
        $this->group_id = $group_id;
        $this->paths = $paths;
        $this->tokens = $tokens;
    }

    public function getAttachments () {

        $attachments = [];

        foreach ($this->paths as $path) {
            $result = $this->API_getWallUploadServer();
            $result = $this->uploading($result, $path);
            $result = $this->API_saveWallPhoto($result);
            $attachments[] = $this->buildAttachment($result);
        }

        return $attachments;
    }

    private function API_getWallUploadServer () {
        return $this->API_sendParams('photos.getWallUploadServer', ['group_id' => $this->group_id]);
    }

    private function API_saveWallPhoto ($result) {

        return $this->API_sendParams('photos.saveWallPhoto', [
            'photo' => $result->photo,
            'server' => $result->server,
            'hash' => $result->hash,
            'group_id' => $this->group_id
        ]);
    }

    private function uploading ($resultUpload, $imagePath) {
        // Получаем upload_url
        $upload_url = $resultUpload->response->upload_url;

        // Отправка фото на upload_url методом POST
        $ch = curl_init($upload_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['photo' => new \CURLFile($imagePath)]);
        // ------

        // Получаем результаты отправки картинки на сервер ВК по upload_url
        $result = json_decode(curl_exec($ch));

        curl_close($ch);

        return $result;
    }

    private function buildAttachment ($result) {
        $photo = $result->response[0];
        return 'photo'.$photo->owner_id.'_'.$photo->id;
    }
}