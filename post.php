<?php
define('IMAGES_DIR', './images/'); // 画像を保存するディレクトリの指定
define('IMAGE_MAX_WIDTH', 600); // アップロード画像の最大幅の指定

session_start();

if ($_SESSION['csrf'] !== $_POST['csrf']) {
    header('Location: ./');
    exit;
}

try {
    if (!isset($_FILES['post_image']['error']) || !is_int($_FILES['post_image']['error'])) {
        throw new RuntimeException('不正なリクエストです');
    }

    switch ($_FILES['post_image']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('1MB以下のファイルを選択してください');
        default:
            throw new RuntimeException('エラーが発生しました');
    }

    if (!$extension = array_search(
        mime_content_type($_FILES['post_image']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
        ),
        true
    )) {
        throw new RuntimeException('投稿できないファイル形式です');
    }

    $image_name = md5(uniqid(rand(), true)) . '.' . $extension;
    $new_image_path = IMAGES_DIR . $image_name;


    if (move_uploaded_file($_FILES['post_image']['tmp_name'], $new_image_path)) {
        list($new_image_width, $new_image_height) = getimagesize($new_image_path);
        $resize_width = IMAGE_MAX_WIDTH;
        $resize_height = $resize_width * $new_image_height / $new_image_width;

        if ($new_image_width > $resize_width) {
            $resize_image_p = imagecreatetruecolor($resize_width, $resize_height);
            
            if ($extension === 'jpg') {
                $resize_image = imagecreatefromjpeg($new_image_path);
                imagecopyresampled($resize_image_p, $resize_image, 0, 0, 0, 0, $resize_width, $resize_height, $new_image_width, $new_image_height);
                imagejpeg($resize_image_p, $new_image_path, 100);
            } else {
                imagealphablending($resize_image_p, false);
                imagesavealpha($resize_image_p, true);
                $resize_image = imagecreatefrompng($new_image_path);
                imagecopyresampled($resize_image_p, $resize_image, 0, 0, 0, 0, $resize_width, $resize_height, $new_image_width, $new_image_height);
                imagepng($resize_image_p, $new_image_path, 9);
            }

            imagedestroy($resize_image_p);
        }

        chmod($new_image_path, 0644);
    } else {
        throw new RuntimeException('画像を保存できませんでした');
    }
} catch (RuntimeException $e) {
    echo $e->getMessage();

    echo '<br><a href="./">投稿画面に戻る</a>';
}

header ('Location: compleat.php');
exit;
