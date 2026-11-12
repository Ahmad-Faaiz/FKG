<?php
function ex($file, $cssOut, $jsOut = null) {
    $s = file_get_contents($file);
    if (preg_match("/<style\b[^>]*>([\s\S]*?)<\/style>/i", $s, $m)) {
        @mkdir(dirname($cssOut), 0777, true);
        file_put_contents($cssOut, trim($m[1]) . "\n");
        $href = "assets/css/" . basename($cssOut);
        $s = preg_replace("/<style\b[^>]*>[\s\S]*?<\/style>/i", "<link rel=\"stylesheet\" href=\"$href\">", $s, 1);
    }
    if ($jsOut && preg_match_all("/<script\b[^>]*>([\s\S]*?)<\/script>/i", $s, $sm, PREG_SET_ORDER)) {
        $js = "";
        foreach ($sm as $mm) {
            $c = trim($mm[1]);
            if ($c === "") continue;
            $js .= $c . "\n\n";
            $s = str_replace($mm[0], "", $s);
        }
        if ($js !== "") {
            @mkdir(dirname($jsOut), 0777, true);
            file_put_contents($jsOut, $js);
            $jshref = "assets/js/" . basename($jsOut);
            if (stripos($s, "</body>") !== false) {
                $s = str_ireplace("</body>", "<script src=\"$jshref\"></script>\n</body>", $s);
            } else {
                $s .= "\n<script src=\"$jshref\"></script>\n";
            }
        }
    }
    file_put_contents($file, $s);
}

ex("user/index.php", "user/assets/css/index.css");
ex("user/booking_saya.php", "user/assets/css/booking_saya.css");
ex("user/pilih_jadwal.php", "user/assets/css/pilih_jadwal.css", "user/assets/js/pilih_jadwal.js");
ex("user/booking.php", "user/assets/css/booking.css", "user/assets/js/booking.js");
echo "done";
