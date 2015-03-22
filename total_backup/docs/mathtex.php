<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>VM - UBUNTU</title>
	<link rel="stylesheet" href="css/docs.css">
</head>
<body>
<h3>
Instructions for installing MathTex on Ubuntu:
</h3><center>
  <table class="ITS_version" summary="ITS versions">
	    <!--------------------------------------------------------------------->
			<tr><th>ITEM</th><th>ACTION</th></tr>
	    <!--------------------------------------------------------------------->
	    <tr>
		  <td>Download</td>
			<td class="list">
				<a href="http://www.forkosh.com/mathtex.zip">mathtex.zip</a>
			</td>
	    </tr>	    
	    <tr>
		  <td>Install</td>
			<td class="list">
			  <ol>
				<li>Unzip to:<br><code>/usr/lib/cgi-bin</code></li>
				<li>Compile:</li><code> cc mathtex.c -DPNG -DLATEX=\"$(which latex)\" -DDVIPNG=\"$(which dvipng)\" -o mathtex.cgi</code></li>
        </ol>
			</td>
	    </tr>			
	    <tr>			
		  <td>Test</td>
			<td class="list">
				<code>http://localhost/cgi-bin/mathtex.cgi?x^2+y^2</code>
			</td>			
		  </tr>
	    <!--------------------------------------------------------------------->			
  </table></center>
</body>
</html>
