<?php

$str = 'Hello LaTeX draw a "pie":  <latex>\pi</latex>';
$path = '/cgi-bin/mimetex.cgi?\Large';

echo '<p>Input String: <pre>'.$str.'</pre></p>';

echo latexCheck($str,$path);

//=====================================================================//
function latexCheck($str,$path) {
//=====================================================================//
die('ERROR 3');
    $pattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
    $replacement = '<img class="ITS_LaTeX" latex="${1}" src="' . $path . '${1}"/>';
    $str         = preg_replace($pattern, $replacement, $str);

    return $str;
}
//=====================================================================//
?>
