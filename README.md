# vkPosting
Отложенный постинг для сообществ VK


## Использование

  * Подключите файл

        require_once 'loadPoster.php';
     
  * Создайте объект VKPoster 

        $poster = new VKPoster($tokens, $group_id, $posts);
        
    + $tokens - массив токенов пользователей
    + $group_id - группа для размещения Ваших записей
    + $posts - Ваши посты:
    
    ####  Пример массива $posts     

        $posts = [
                [
                    '2017 15/09 11:35', // пост выйдет 15 сентября в 11:35
                    'текст Вашего поста...',
                    $attachments // массив медиавложений к посту
                ],
                [
                    NULL, // пост выйдет прямо сейчас
                    'текст какого-то другого поста...',
                    [] // без медиавложений
                ],
        ];
   
   
  * Опубликуйте посты или поставьте их на таймер

            $poster -> sending();

    ####  Как преобразовать фото в $attachments?
        
        $paths = ['picture1.jpg', 'picture2.jpg', 'picture3.png'];
            
        $uploadPhoto = new UploadPhoto($tokens, $group_id, $paths);
        $attachments = $uploadPhoto->getAttachments();
        
        
        
        