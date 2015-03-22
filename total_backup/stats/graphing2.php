<?php // content="text/plain; charset=utf-8"
$gtype=$_GET["gtype"];
$xaxis=$_GET["xaxis"];
$yaxis=$_GET["yaxis"];

if($gtype=="scatter")
{
require_once ('/var/www/html/js/jpgraph/src/jpgraph.php');
require_once ('/var/www/html/js/jpgraph/src/jpgraph_scatter.php');


//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";
// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

$x=mysql_query("select $xaxis from MinedData ");
$y=mysql_query("select $yaxis from MinedData ");

$datax=array();
while($row=mysql_fetch_assoc($x,MYSQL_NUM))
{


$temp=$row[0];
$datax[]=(float)$temp;
}


$datay=array();
while($row=mysql_fetch_array($y,MYSQL_NUM))
{
$temp=$row[0];
$datay[]=(float)$temp;
}



//graph stuff
$graph=new Graph(1000,600);



$graph->SetScale('linlin');
$graph->img->SetMargin(40,40,40,40);	

$splot = new ScatterPlot($datay,$datax);


$graph->title->Set("$yaxis Vs. $xaxis");
$graph->xaxis->title->Set("$xaxis");
$graph->yaxis->title->Set("$yaxis");
$splot->mark->SetType(MARK_FILLEDCIRCLE);
$splot->mark->SetFillColor("navy");
	


$graph->Add($splot);
$graph->Stroke();

}


else if($gtype=="lineplot")
{


require_once ('/var/www/html/jpgraph/src/jpgraph.php');
require_once ('/var/www/html/jpgraph/src/jpgraph_line.php');


//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";
// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

$x=mysql_query("select $xaxis from MinedData ");
$y=mysql_query("select $yaxis from MinedData ");

$datax=array();
while($row=mysql_fetch_assoc($x,MYSQL_NUM))
{


$temp=$row[0];
$datax[]=(float)$temp;
}


$datay=array();
while($row=mysql_fetch_array($y,MYSQL_NUM))
{
$temp=$row[0];
$datay[]=(float)$temp;
}



//graph stuff
$graph=new Graph(1000,600);



$graph->SetScale('intint');
$graph->img->SetMargin(40,40,40,40);	

$lineplot = new LinePlot($datay);
$lineplot->SetColor( 'blue' );
$lineplot->SetWeight( 0.5 );   // Two pixel wide
$lineplot->mark->SetType(MARK_UTRIANGLE);
$lineplot->mark->SetColor('red');
$lineplot->mark->SetFillColor('red');

$graph->title->Set("$yaxis Vs. $xaxis");
$graph->xaxis->title->Set("$xaxis");
$graph->yaxis->title->Set("$yaxis");



$graph->Add($lineplot);
$graph->Stroke();

}


else if($gtype=="spline")
{


require_once ('/var/www/html/jpgraph/src/jpgraph.php');
require_once ('/var/www/html/jpgraph/src/jpgraph_line.php');
require_once ('/var/www/html/jpgraph/src/jpgraph_scatter.php');
require_once ('/var/www/html/jpgraph/src/jpgraph_regstat.php');


//Connect to the MySQL server
if(!($connection = @ mysql_connect(localhost,root,csip)))
  echo "connect failed<br> ";
// Select database
if(!(mysql_select_db("its",$connection)))
  echo "select failed<br>\n";

$x=mysql_query("select $xaxis from MinedData ");
$y=mysql_query("select $yaxis from MinedData ");

$datax=array();
while($row=mysql_fetch_assoc($x,MYSQL_NUM))
{
$temp=$row[0];
if($temp!=NULL)
$datax[]=(float)$temp;
}


$datay=array();
while($row=mysql_fetch_array($y,MYSQL_NUM))
{
$temp=$row[0];
if($temp!=NULL)
$datay[]=(float)$temp;
}

//graph stuff
$spline = new Spline($datax,$datay);
list($newx,$newy) = $spline->Get(5000);
$g = new Graph(1100,600);
$g->SetMargin(30,20,40,30);
$g->title->Set("$yaxis Vs. $xaxis");

$g->SetMarginColor('lightblue');
$g->SetScale('linlin');
$g->xaxis->title->Set("$xaxis");
$g->yaxis->title->Set("$yaxis");

$splot = new ScatterPlot($datay,$datax);

$splot->mark->SetFillColor('red@0.3');
$splot->mark->SetColor('red@0.5');
$lplot = new LinePlot($newy,$newx);
$lplot->SetColor('navy');



$g->Add($lplot);
$g->Add($splot);
$g->Stroke();

}



?>
