$codesQdy = 1;
$codeSize = 20;
$showBlocks = true;

for($i = 0; $i < $codesQdy; $i++) {
    $a = str_replace('.', $i, microtime());
    $chars = "0123456789CORVO";
    $charsSize = strlen($chars) - 1;
    $code = '';

    $h = str_pad(dechex($i), $codeSize / 2, 0, STR_PAD_LEFT);
    for($j = 0; $j < $codeSize; $j++) {
        $fm = ($j % 4) == 0;
        if($showBlocks === true) {
            if($j > 0 && $fm) {
                $code .= '-';
            }
        }

        $tm = $j / 2;
        if($fm) {
            $code .= ($h[$tm] != '0' ? $h[$tm] : $chars[mt_rand(16, $charsSize)]);
        } elseif(($j - 3) % 4 == 0) {
            $key = (int) floor($tm);
            $code .= ($h[$key] != '0' ? $h[$key] : $chars[mt_rand(16, $charsSize)]);
        } else {
            $code .= $chars[mt_rand(0, $charsSize)];
        }
    }

    echo strtoupper($code) . '<br>';
}
