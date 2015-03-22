<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Untitled</title>
<style type="text/css">	
input[type="radio"].toggle {display: none;color:red}
input[type="radio"].toggle:checked + label {cursor: default;border:1px solid #999;}
input[type="radio"].toggle + label {cursor: pointer; width: 3em;padding: 10px;}
input[type="radio"].toggle:checked + label.btn:hover {
    background-color: inherit;
    background-position: 0 0;
}
div.mode { padding:15px;text-align:middle }
</style>
</head>

<body style="background-color:#fff">
<div class="mode">
<input id="toggle-on" class="toggle" name="toggle" value="false" type="radio" checked
><label for="toggle-on" class="btn">ASSIGNMENTS</label
><input id="toggle-off" class="toggle" name="toggle" value="true" type="radio"
><label for="toggle-off" class="btn">CONCEPTS</label>
</div>
</body>
</html>
