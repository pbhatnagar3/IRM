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

$avg_score=mysql_query("select Avg_Score from MinedDataV1 where qid IN ($id)");
$avg_dur=mysql_query("select Avg_Dur from MinedDataV1 where qid IN ($id)");

$datax=array();
while($row=mysql_fetch_assoc($avg_dur))
{
$temp=$row["Avg_Dur"];
$datax[]=(float)$temp;
}


$datay=array();
while($row=mysql_fetch_assoc($avg_score))
{
$temp=$row["Avg_Score"];
$datay[]=(float)$temp;
}

//graph stuff
$graph=new Graph(500,500);



$graph->SetScale('linlin');
$graph->img->SetMargin(40,40,40,40);	

$splot = new ScatterPlot($datay,$datax);


$graph->title->Set("Avg. Score Vs. Avg. Duration");
$graph->xaxis->title->Set('Avg. Duration');
$graph->yaxis->title->Set('Avg. Score');
$splot->mark->SetType(MARK_FILLEDCIRCLE);
$splot->mark->SetFillColor("red");
	


$graph->Add($splot);
$graph->Stroke();

?>

