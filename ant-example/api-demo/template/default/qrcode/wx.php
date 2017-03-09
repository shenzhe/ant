<?php
use ZPHP\Common\Route;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width = device-width,initial-scale=1">
    <title>扫码登录</title>
</head>
<body>
<form action="<?= Route::makeUrl('qrcode', 'check') ?>" method="post">
    <p><label>请输入姓名：</label><input type="text" name="name" required></p>
    <p><label>请输入密码：</label><input type="password" name="password" required></p>
    <p>
        <input type="hidden" name="code" value="<?= $data['code'] ?>">
        <input type="submit" value="登录">
    </p>
</form>
</body>
</html>