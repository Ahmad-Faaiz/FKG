<?php
$s = file_get_contents('admin/index.php');
if (preg_match('/<style>([\s\S]*?)<\/style>/', $s, $m)) {
    file_put_contents('admin/assets/css/admin.css', trim($m[1]) . "\n");
    echo "ok";
}
