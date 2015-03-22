<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Untitled</title>
<style type="text/css">
#navcontainerMain { 
  position: absolute;
  top: 20px;
  left: 0;
  z-index: 2;
  margin: 0;
  padding: 0;
  width: 177px;
  }
#navcontainerMain ul {
  margin-top: 0;
  margin-left: 0;
  padding-left: 0;
  list-style-type: none;
  font-family: Arial, Helvetica, sans-serif;
  }
#navcontainerMain li {
  margin-bottom: 8px;
  }
#navcontainerMain a {
  display: block;
  padding: 3px;
  width: 160px;
  background-color: #fff;
  border: 1px solid #fff;
  /*border-left: 0;*/
  }
#navcontainerMain a:link, #navlist a:visited {
  color: #000;
  text-decoration: none;
  }
#navcontainerMain a:hover {
  background-color: #fff;
  /*margin-left: 10px;*/
  border-right: 0;
  border: 1px solid #666;
  }
a#current {
  background-color: #fff;
  margin-left: 10px;
  border-right: 0;
  border: 1px solid #666;
  border-right: 1px solid #fff;
  }
#content {
  position: absolute;
  top: 20px;
  left: 176px;
  margin: 0;
  padding: 0;
  height: 500px;
  width: 500px;
  background-color: #fff;
  border-left: 1px solid #666;
  }
</style>
</head>

<body style="background-color:#fff">

<div id="navcontainerMain">
<ul id="navlist">
<li id="active"><a href="#">ASSIGNMENTS</a></li>
<li><a href="#" id="current">CONCEPTS</a></li>
</ul>
</div>

<div id="content">content here</div>
<?php
        $chList = '<div class="QuestionMode"><select id="QuestionMode">'
        .'<option value="MODULE">Modules</option>'
        .'<option value="CONCEPT" id="showConcepts">Concepts</option></select>'
        .'<input type="button" style="display:none" name="changeConcept" id="changeConcept" value="change Concept"/></div>'
        .'<div class="module_index" id="ModuleListingDiv"></div><div id="chapterListingDiv"><ul id="chList">';
        
echo $chList;
?>
</body>
</html>
