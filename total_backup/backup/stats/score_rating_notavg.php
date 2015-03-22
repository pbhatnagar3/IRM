<?php // content="text/plain; charset=utf-8"
require_once ('/var/www/html/js/jpgraph/src/jpgraph.php');
require_once ('/var/www/html/js/jpgraph/src/jpgraph_scatter.php');



$qid = $_GET["qid"];



//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";
// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";


$query="select id from users ";
if(!($result = @ mysql_query($query,$connection)))
  echo "query failed<br>";
$a=0;
while($row = mysql_fetch_array($result))
{
$ids[$a]=$row[0];
$a = $a+1;
}


$a=0;
for($k=0;$k<=count($ids);$k++)
{
$r = "stats_{$ids[$k]}";
$query="select score, rating from $r where rating and score and question_id=$qid";
$result = @ mysql_query($query,$connection);
while($row = mysql_fetch_array($result,MYSQL_NUM))
{
$score[$a]=$row[0];
$rating[$a]=$row[1];
$a = $a+1;
}
}

for($i=0;$i<count($score);$i++)
{
$datax[$i]=$rating[$i];
$datay[$i]=$score[$i];
}



//graph stuff
$graph=new Graph(500,300);



$graph->SetScale('linlin');
$graph->img->SetMargin(40,40,40,40);	

$splot = new ScatterPlot($datay,$datax);


$graph->title->Set("Score Vs. Rating");
$graph->xaxis->title->Set('Rating');
$graph->yaxis->title->Set('Score');
$splot->mark->SetType(MARK_FILLEDCIRCLE);
$splot->mark->SetFillColor("red");
	


$graph->Add($splot);
$graph->Stroke();

?>

