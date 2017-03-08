<?php
use ZPHP\Common\Route;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>扫码登录</title>
</head>
<body>
<p id="msg"></p>
<p><img src="http://qr.liantu.com/api.php?text=<?= Route::makeUrl('qrcode', 'wx', ['code' => $code]); ?>"></p>
<script type="text/javascript">
    var ws = new WebSocket("<?=$ws_url?>?code=<?=$code?>");
    ws.onopen = function (evt) {
        console.log('onopen');
        console.log(evt);
    }

    ws.onmessage = function (evt) {
        alert(evt.data);
    }

    ws.onclose = function (evt) {
        console.log('onclose');
        console.log(evt);
    }

    ws.onerror = function (evt) {
        console.log('onerror');
        console.log(evt);
    }
</script>
</body>
</html>