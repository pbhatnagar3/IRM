<?php

$str = 'Hello LaTeX draw a "pie":  <latex>\pi</latex>';
$path = '/cgi-bin/mathtex.cgi?\Large';

echo '<p style="margin:10%"><center>Input String: <pre>'.$str.'</pre>'
	.latexCheck($str,$path).'</center></p>';

//=====================================================================//
function latexCheck($str,$path) {
//=====================================================================//
die('ERROR');
    $pattern     = "/<latex[^>]*>(.*?)<\/latex>/im";
    $replacement = '<img class="ITS_LaTeX" latex="${1}" src="' . $path . '${1}"/>';
    $str         = preg_replace($pattern, $replacement, $str);

    return $str;
}
//=====================================================================//
?>
