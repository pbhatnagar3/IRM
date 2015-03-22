<?php // content="text/plain; charset=utf-8"
require_once ('/var/www/html/js/jpgraph/src/jpgraph.php');
require_once ('/var/www/html/js/jpgraph/src/jpgraph_scatter.php');

$id = $_GET["id"];
$id=(string)$id;

//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";
// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

$avg_score=mysql_query("select Avg from MinedData where question_id IN ($id)");
$avg_rating=mysql_query("select AvgRating from MinedData where question_id IN ($id)");

$datax=array();
while($row=mysql_fetch_assoc($avg_rating))
{
$temp=$row["AvgRating"];
$datax[]=(float)$temp;
}


$datay=array();
while($row=mysql_fetch_assoc($avg_score))
{
$temp=$row["Avg"];
$datay[]=(float)$temp;
}

//graph stuff
$graph=new Graph(500,300);



$graph->SetScale('linlin');
$graph->img->SetMargin(40,40,40,40);	

$splot = new ScatterPlot($datay,$datax);


$graph->title->Set("Avg. Score Vs. Avg. Rating");
$graph->xaxis->title->Set('Avg. Rating');
$graph->yaxis->title->Set('Avg. Score');
$splot->mark->SetType(MARK_FILLEDCIRCLE);
$splot->mark->SetFillColor("blue");
	


$graph->Add($splot);
$graph->Stroke();

?>

