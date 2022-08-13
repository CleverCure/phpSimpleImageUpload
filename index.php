<?php
session_start();
$csrf =  base64_encode( openssl_random_pseudo_bytes(32));
$_SESSION['csrf'] = $csrf;
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPを使って画像を投稿するシンプルな例</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h1>画像の投稿</h1>

    <form action="post.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
        <div>画像（1MB以下のjpgかpng）<br><input id="post_image" type="file" name="post_image" accept=".jpg,.jpeg,.JPG,.JPEG,.png,.PNG" required></div>
        <div><img id="preview_image" src=""></div>
        <div><input id="submit" type="submit" value="送信"></div>
        <input type="hidden" name="csrf" value="<?=$csrf?>">
    </form>

    <script src="assets/js/script.js"></script>
</body>
</html>