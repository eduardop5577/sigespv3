<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
</head>
<?php   
	include("pChart/pData.class");  
	include("pChart/pChart.class");   
	$DataSet = new pData;  $DataSet->AddPoint(array(10,2,3,5,3),"Serie1");  
	$DataSet->AddPoint(array("Jan","Feb","Mar","Apr","May"),"Serie2");  
	$DataSet->AddAllSeries();  
	$DataSet->SetAbsciseLabelSerie("Serie2");   
	$Test = new pChart(380,200);  
	$Test->drawFilledRoundedRectangle(7,7,373,193,5,240,240,240);  
	$Test->drawRoundedRectangle(5,5,375,195,5,230,230,230);   
	$Test->setFontProperties("Fonts/tahoma.ttf",8); 
	$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);  
	$Test->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);   
	$Test->Render("ple10.png"); 
?>
<body>
<img src="ple10.png" />
</body>
</html>
