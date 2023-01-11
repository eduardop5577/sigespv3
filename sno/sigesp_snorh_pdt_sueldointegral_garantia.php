<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "close();";
		print "opener.document.form1.submit();";
		print "</script>";		
	}
	$ls_codper=$_GET["codper"];
	$ls_anocurper=$_GET["anocurper"];
	$ls_mescurper=$_GET["mescurpe"];
	$ls_metodo=$_GET["metodo"];
	$ls_tipo=$_GET["tipo"];
	$ls_sueint=$_GET["sueint"];
	$ls_sueint=strtoupper($ls_sueint);

   //--------------------------------------------------------------
   function uf_print($as_codper, $ai_anocurper, $as_mescurper,$ls_sueint,$as_metodo,$as_tipo)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	  Description: Función que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$io_funciones=new class_funciones();		
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_titulo="";
		$ls_salarioprestacion=' SALARIO PARA EL CALCULO ';
		switch ($as_metodo)
		{
			case 0://PROMEDIO MENSUAL VARIABLE
				$ls_titulo="PROMEDIO MENSUAL VARIABLE";
			break;
			
			case 1://PROMEDIO MENSUAL INTEGRAL
				$ls_titulo="PROMEDIO MENSUAL INTEGRAL";
			break;
			
			case 2://ULTIMO MES EFECTIVO
				$ls_titulo="ULTIMO MES EFECTIVO";
			break;
		}
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda >";
		print "<td colspan=4>MÉTODO ".$ls_titulo."</td>";
		print "</tr>";
		print "<tr class=titulo-celda >";
		print "<td width=60>Mes</td>";
		print "<td width=200>Conceptos Fijos</td>";
		print "<td width=200>Conceptos Variables</td>";
		print "<td width=60>Salario Normal</td>";
		print "</tr>";
	   	$lb_valido=true;
		$anio=$ai_anocurper;
		$mes=intval($as_mescurper);
		$ls_criterio='';
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		$li_tope=6;
		if($as_tipo=='A')
		{
			$li_tope=12;
		}
		for($i=1;$i<=$li_tope;$i++)
		{
			if($ai_mes==0)
			{
				$ai_mes=12;
				$ai_anio=intval($ai_anio)-1;
			}
			$ls_meses.="'".$ai_anio."-".str_pad($ai_mes,2,'0',0)."',";
			$ai_mes=intval($ai_mes)-1;
		}
		$ls_meses='('.substr($ls_meses,0,strlen($ls_meses)-1).')';
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		$ls_sql="SELECT convar, confij, comsue, fecsue ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp = '".$ls_codemp."' ".
				"   AND codper = '".$as_codper."' ".
				"   AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses."  ".
				" ORDER BY fecsue";
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$io_mensajes->message("CLASE->FIDEICOMISO METODO->obtenerSueldo ERROR->".$io_funciones->uf_convertirmsg($io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$i=0;
			$li_fijo=0;
			$li_variable=0;
			$li_sueldo=0;
			while(!$rs_data->EOF)
			{
				$i++;
				$ai_sueldovariable = number_format($rs_data->fields["convar"],2,'.','');
				$ai_sueldofijo = number_format($rs_data->fields["confij"],2,'.','');
				$ai_compensacion = number_format($rs_data->fields["comsue"],2,'.','');
				$ai_salario = number_format($ai_sueldofijo+$ai_sueldovariable,2,'.','');
				$ld_fecsue=substr($rs_data->fields["fecsue"],0,7);
				print "<tr class=celdas-blancas>";
				print "<td align='center'>".$ld_fecsue."</td>";
				print "<td align='right'>".number_format($ai_sueldofijo,2,',','.')."</td>";
				print "<td align='right'>".number_format($ai_sueldovariable,2,',','.')."</td>";
				print "<td align='right'>".number_format($ai_salario,2,',','.')."</td>";
				print "</tr>";			
				$rs_data->MoveNext();
				switch ($as_metodo)
				{
					case 0://PROMEDIO MENSUAL VARIABLE
						$ls_salariovariable .=''.number_format($ai_sueldovariable,2,',','.').' + ';
						$li_variable=$li_variable+$ai_sueldovariable;
					break;
					
					case 1://PROMEDIO MENSUAL INTEGRAL
						$ls_salariovariable .=''.number_format($ai_sueldovariable,2,',','.').' + ';
						$ls_salariofijo .=''.number_format($ai_sueldofijo,2,',','.').' + ';
                                                $ls_compensacion .=''.number_format($ai_compensacion,2,',','.').' + ';
						$li_variable=$li_variable+$ai_sueldovariable;
						$li_fijo=$li_fijo+$ai_sueldofijo;
                                                $li_compensacion=$li_compensacion+$ai_compensacion;
					break;
				}
			}
			print "<tr>";
			print "<td align='center' colspan=4>".$ls_salarioprestacion."</td>";
			print "</tr>";
			print "<tr>";
			switch ($as_metodo)
			{
				case 0://PROMEDIO MENSUAL VARIABLE
					$ls_salariovariable=' + ('.substr($ls_salariovariable,0,strlen($ls_salariovariable)-3).')/'.$i;
					$ls_salariofijo ='('.number_format($ai_sueldofijo,2,',','.').')';
                                        $ls_compensacion =' + ('.number_format($ai_compensacion,2,',','.').')';
					$li_variable=$li_variable/$i;
					$li_fijo=$ai_sueldofijo;
					print "<td align='center' colspan=4>".$ls_salariofijo.$ls_salariovariable.$ls_compensacion." <strong>=</strong></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' colspan=4>(".number_format($li_fijo,2,',','.').')'.' + ('.number_format($li_variable,2,',','.').' + ('.number_format($ai_compensacion,2,',','.').") <strong>=</strong></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' colspan=4><strong>(".number_format($li_fijo+$li_variable,2,',','.').")</strong></td>";
					print "</tr>";
				break;
				
				case 1://PROMEDIO MENSUAL INTEGRAL
					$ls_salariovariable=' + ('.substr($ls_salariovariable,0,strlen($ls_salariovariable)-3).')/'.$i;
					$ls_salariofijo ='('.substr($ls_salariofijo,0,strlen($ls_salariofijo)-3).')/'.$i;
                                        $ls_compensacion ='('.substr($ls_compensacion,0,strlen($ls_compensacion)-3).')/'.$i;
					$li_variable=$li_variable/$i;
					$li_fijo=$li_fijo/$i;
                                        $li_compensacion=$li_fijo/$i;
					print "<td align='center' colspan=4>".$ls_salariofijo.$ls_salariovariable.$ls_compensacion." <strong>=</strong></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' colspan=4>(".number_format($li_fijo,2,',','.').')'.' + ('.number_format($li_variable,2,',','.').' + ('.number_format($ai_compensacion,2,',','.').") <strong>=</strong></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' colspan=4><strong>(".number_format($li_fijo+$li_variable+$li_compensacion,2,',','.').")</strong></td>";
					print "</tr>";
				break;
				
				case 2://ULTIMO MES EFECTIVO
					$ls_salariovariable .=' + ('.number_format($ai_sueldovariable,2,',','.').')';
					$ls_salariofijo ='('.number_format($ai_sueldofijo,2,',','.').')';
                                        $ls_compensacion =' + ('.number_format($ai_compensacion,2,',','.').')';
					$li_variable=$ai_sueldovariable;
					$li_fijo=$ai_sueldofijo;
					print "<td align='center' colspan=4>".$ls_salariofijo.$ls_salariovariable.$ls_compensacion." <strong>=</strong></td>";
					print "</tr>";
					print "<tr>";
					print "<td align='center' colspan=4><strong>(".number_format($li_fijo+$li_variable+$ai_compensacion,2,',','.').")</strong></td>";
					print "</tr>";
				break;
			}
			$io_sql->free_result($rs_data);
		}		
		print "</table>";
		unset($io_include);
		unset($io_conexion);
		unset($io_sql);
		unset($io_mensajes);
		unset($io_funciones);
		unset($ls_codemp);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?php print $ls_sueint." POR PERSONAL";?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}
-->
</style>
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
</head>

<body>
<form name="form1" method="post" action="">
  <p align="center">
    <input name="operacion" type="hidden" id="operacion">
</p>
  <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    <tr>
      <td width="496" height="20" colspan="2" class="titulo-ventana"><?php print $ls_sueint?></td>
    </tr>
  </table>
<br>
    <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="139" height="22"><div align="right">C&oacute;digo Personal</div></td>
        <td width="355"><div align="left">
          <input name="txtcodper" type="text" id="txtcodper" size="16" style="text-align:center" value="<?php print ($ls_codper);?>" readonly>        
        </div></td>
      </tr>      
  </table>
  <?php
  	 uf_print($ls_codper, $ls_anocurper, $ls_mescurper,$ls_sueint,$ls_metodo,$ls_tipo);   
  ?>
  <br>

</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>

</html>
