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
   //--------------------------------------------------------------
   function uf_print($as_codigo, $as_denominacion, $as_tipo, $ls_codnomdes,$ls_codnomhas,$ls_codperides,$ls_codperihas)
   {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print
		//		   Access: public
		//	    Arguments: as_codpro  // C�digo de Profesi�n
		//				   as_despro  // Descripci�n de la profesi�n
		//				   as_tipo  // Verifica de donde se est� llamando el cat�logo
		//	  Description: Funci�n que obtiene e imprime los resultados de la busqueda
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 01/01/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $io_fun_nomina;
		
		$li_len1=0;
		$li_len2=0;
		$li_len3=0;
		$li_len4=0;
		$li_len5=0;
		$ls_titulo="";
		$arrResultado=$io_fun_nomina->uf_loadmodalidad($li_len1,$li_len2,$li_len3,$li_len4,$li_len5,$ls_titulo);
		$li_len1=$arrResultado['ai_len1'];
		$li_len2=$arrResultado['ai_len2'];
		$li_len3=$arrResultado['ai_len3'];
		$li_len4=$arrResultado['ai_len4'];
		$li_len5=$arrResultado['ai_len5'];
		$ls_titulo=$arrResultado['as_titulo'];
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
		
		if (($as_tipo!="pagounides")&&($as_tipo!="pagounihas"))
		{		   		
        	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
        	$ls_codperi=$_SESSION["la_nomina"]["peractnom"];
        	$ls_anocur=$_SESSION["la_nomina"]["anocurnom"];
		}
		print "<table width=500 border=0 cellpadding=1 cellspacing=1 class=fondo-tabla align=center>";
		print "<tr class=titulo-celda>";
		print "<td>C�digo </td>";
		print "<td>Denominaci�n</td>";
		print "<td>".$ls_titulo."</td>";
		print "<td>Tipo</td>";
		print "</tr>";
		if($as_codigo!="")
		{
			$as_codigo=str_pad($as_codigo,12,"0",0);
		}
		$ls_coduniad1="%".substr($as_codigo,0,4)."%";
		$ls_coduniad2="%".substr($as_codigo,4,2)."%";
		$ls_coduniad3="%".substr($as_codigo,6,2)."%";
		$ls_coduniad4="%".substr($as_codigo,8,2)."%";
		$ls_coduniad5="%".substr($as_codigo,10,2)."%";
		$ls_criterio="";
		if (($as_tipo=="pagounides")||($as_tipo=="pagounihas"))
		{		 
			$ls_sql="SELECT codemp,minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm,desuniadm,codestpro1,codestpro2,codestpro3,codestpro4,".
					"		codestpro5,estcla, ".
					"		(SELECT denestpro1 ".
					"		   FROM spg_ep1 ".
					"		  WHERE codemp=sno_hunidadadmin.codemp".
					"		    AND spg_ep1.codestpro1=sno_hunidadadmin.codestpro1  ".
					"           AND spg_ep1.estcla=sno_hunidadadmin.estcla) as denestpro1, ".
					"		(SELECT denestpro2 ".
					"		   FROM spg_ep2 ".
					"		  WHERE codemp=sno_hunidadadmin.codemp".
					"		    AND spg_ep2.codestpro1=sno_hunidadadmin.codestpro1 ".
					"		    AND spg_ep2.codestpro2=sno_hunidadadmin.codestpro2 ".
					"           AND spg_ep2.estcla=sno_hunidadadmin.estcla) as denestpro2, ".
					"		(SELECT denestpro3 ".
					"		   FROM spg_ep3 ".
					"		  WHERE codemp=sno_hunidadadmin.codemp".
					"		    AND spg_ep3.codestpro1=sno_hunidadadmin.codestpro1 ".
					"		    AND spg_ep3.codestpro2=sno_hunidadadmin.codestpro2 ".
					"		    AND spg_ep3.codestpro3=sno_hunidadadmin.codestpro3 ".
					"           AND spg_ep3.estcla=sno_hunidadadmin.estcla) as denestpro3, ".
					"		(SELECT denestpro4 ".
					"		   FROM spg_ep4 ".
					"		  WHERE codemp=sno_hunidadadmin.codemp".
					"		    AND spg_ep4.codestpro1=sno_hunidadadmin.codestpro1 ".
					"		    AND spg_ep4.codestpro2=sno_hunidadadmin.codestpro2 ".
					"		    AND spg_ep4.codestpro3=sno_hunidadadmin.codestpro3 ".
					"		    AND spg_ep4.codestpro4=sno_hunidadadmin.codestpro4 ".
					"           AND spg_ep4.estcla=sno_hunidadadmin.estcla) as denestpro4, ".
					"		(SELECT denestpro5 ".
					"		   FROM spg_ep5 ".
					"		  WHERE codemp=sno_hunidadadmin.codemp".
					"		    AND spg_ep5.codestpro1=sno_hunidadadmin.codestpro1 ".
					"		    AND spg_ep5.codestpro2=sno_hunidadadmin.codestpro2 ".
					"		    AND spg_ep5.codestpro3=sno_hunidadadmin.codestpro3 ".
					"		    AND spg_ep5.codestpro4=sno_hunidadadmin.codestpro4 ".
					"		    AND spg_ep5.codestpro5=sno_hunidadadmin.codestpro5 ".
					"           AND spg_ep5.estcla=sno_hunidadadmin.estcla) as denestpro5 ".
					"  FROM sno_hunidadadmin ".
					" WHERE codemp='".$ls_codemp."' ".
					"   AND minorguniadm like '".$ls_coduniad1."' ".
					"   AND ofiuniadm like '".$ls_coduniad2."' ".
					"   AND uniuniadm like '".$ls_coduniad3."' ".
					"   AND depuniadm like '".$ls_coduniad4."' ".
					"   AND prouniadm like '".$ls_coduniad5."' ".
					"   AND desuniadm like '".$as_denominacion."' ".
					"	AND codnom BETWEEN '".$ls_codnomdes."' AND '".$ls_codnomhas."'".					
					"	AND codperi BETWEEN '".$ls_codperides."' AND '".$ls_codperihas."'".
					"   GROUP BY codemp,minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm,desuniadm,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla ".
					" ORDER BY minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm ";
			}
			else
			{
			  $ls_sql="SELECT codemp,minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm,desuniadm,codestpro1,codestpro2,codestpro3,codestpro4,".
			  	"			  codestpro5,estcla, ".
				"		(SELECT denestpro1 ".
				"		   FROM spg_ep1 ".
				"		  WHERE codemp=sno_hunidadadmin.codemp".
				"		    AND spg_ep1.codestpro1=sno_hunidadadmin.codestpro1  ".
				"           AND spg_ep1.estcla=sno_hunidadadmin.estcla) as denestpro1, ".
				"		(SELECT denestpro2 ".
				"		   FROM spg_ep2 ".
				"		  WHERE codemp=sno_hunidadadmin.codemp".
				"		    AND spg_ep2.codestpro1=sno_hunidadadmin.codestpro1 ".
				"		    AND spg_ep2.codestpro2=sno_hunidadadmin.codestpro2 ".
				"           AND spg_ep2.estcla=sno_hunidadadmin.estcla) as denestpro2, ".
				"		(SELECT denestpro3 ".
				"		   FROM spg_ep3 ".
				"		  WHERE codemp=sno_hunidadadmin.codemp".
				"		    AND spg_ep3.codestpro1=sno_hunidadadmin.codestpro1 ".
				"		    AND spg_ep3.codestpro2=sno_hunidadadmin.codestpro2 ".
				"		    AND spg_ep3.codestpro3=sno_hunidadadmin.codestpro3 ".
				"           AND spg_ep3.estcla=sno_hunidadadmin.estcla) as denestpro3, ".
				"		(SELECT denestpro4 ".
				"		   FROM spg_ep4 ".
				"		  WHERE codemp=sno_hunidadadmin.codemp".
				"		    AND spg_ep4.codestpro1=sno_hunidadadmin.codestpro1 ".
				"		    AND spg_ep4.codestpro2=sno_hunidadadmin.codestpro2 ".
				"		    AND spg_ep4.codestpro3=sno_hunidadadmin.codestpro3 ".
				"		    AND spg_ep4.codestpro4=sno_hunidadadmin.codestpro4 ".
				"           AND spg_ep4.estcla=sno_hunidadadmin.estcla) as denestpro4, ".
				"		(SELECT denestpro5 ".
				"		   FROM spg_ep5 ".
				"		  WHERE codemp=sno_hunidadadmin.codemp".
				"		    AND spg_ep5.codestpro1=sno_hunidadadmin.codestpro1 ".
				"		    AND spg_ep5.codestpro2=sno_hunidadadmin.codestpro2 ".
				"		    AND spg_ep5.codestpro3=sno_hunidadadmin.codestpro3 ".
				"		    AND spg_ep5.codestpro4=sno_hunidadadmin.codestpro4 ".
				"		    AND spg_ep5.codestpro5=sno_hunidadadmin.codestpro5 ".
				"           AND spg_ep5.estcla=sno_hunidadadmin.estcla) as denestpro5 ".
				"  FROM sno_hunidadadmin ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND minorguniadm like '".$ls_coduniad1."' ".
				"   AND ofiuniadm like '".$ls_coduniad2."' ".
				"   AND uniuniadm like '".$ls_coduniad3."' ".
				"   AND depuniadm like '".$ls_coduniad4."' ".
				"   AND prouniadm like '".$ls_coduniad5."' ".
				"   AND desuniadm like '".$as_denominacion."' ".
				"	AND codnom = '".$ls_codnom."' ".
				"	AND anocur = '".$ls_anocur."' ".
				"	AND codperi = '".$ls_codperi."' ".
	   			" ORDER BY minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm ";			
			}
		$rs_data=$io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$codigo=$rs_data->fields["minorguniadm"].$rs_data->fields["ofiuniadm"].$rs_data->fields["uniuniadm"].$rs_data->fields["depuniadm"].$rs_data->fields["prouniadm"];
				$ls_minorguniadm=$rs_data->fields["minorguniadm"];
				$ls_ofiuniadm=$rs_data->fields["ofiuniadm"]; 
				$ls_uniuniadm=$rs_data->fields["uniuniadm"];
				$ls_depuniadm=$rs_data->fields["depuniadm"];
				$ls_prouniadm=$rs_data->fields["prouniadm"];
				$denominacion=$rs_data->fields["desuniadm"];
				$ls_codest1=$rs_data->fields["codestpro1"];
				$ls_codest2=$rs_data->fields["codestpro2"];
				$ls_codest3=$rs_data->fields["codestpro3"];
				$ls_codest4=$rs_data->fields["codestpro4"];
				$ls_codest5=$rs_data->fields["codestpro5"];
				$ls_codest1=$io_fun_nomina->uf_formato_programatica_detallado($li_len1,$ls_codest1);
				$ls_codest2=$io_fun_nomina->uf_formato_programatica_detallado($li_len2,$ls_codest2);
				$ls_codest3=$io_fun_nomina->uf_formato_programatica_detallado($li_len3,$ls_codest3);
				$ls_codest4=$io_fun_nomina->uf_formato_programatica_detallado($li_len4,$ls_codest4);
				$ls_codest5=$io_fun_nomina->uf_formato_programatica_detallado($li_len5,$ls_codest5);
				$ls_denestpro1=$rs_data->fields["denestpro1"];
				$ls_denestpro2=$rs_data->fields["denestpro2"];
				$ls_denestpro3=$rs_data->fields["denestpro3"];
				$ls_denestpro4=$rs_data->fields["denestpro4"];
				$ls_denestpro5=$rs_data->fields["denestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				switch($ls_estcla)
				{
					case "P":
					$ls_estclatipo="PROYECTO";
				    break;
					
					case "A":
					$ls_estclatipo="ACCION";
				    break;
				}
				switch($as_tipo)
				{
					case "": // Se hace el llamado desde sigesp_snorh_d_uni_adm.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptar('$codigo','$denominacion','$ls_codest1',";
						print "'$ls_codest2','$ls_codest3','$ls_codest4','$ls_codest5','$ls_denestpro1','$ls_denestpro2',";
						print "'$ls_denestpro3','$ls_denestpro4','$ls_denestpro5','$ls_estcla');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "cestaticket": // Se hace el llamado desde sigesp_snorh_d_ct_unid.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarcestaticket('$codigo','$denominacion');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";
						break;			

					case "asignacion": // Se hace el llamado desde sigesp_sno_d_personalnomina.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarasignacion('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm','$denominacion');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "replisconc": // Se hace el llamado desde sigesp_sno_r_listadoconcepto.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisconc('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm','$denominacion');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "asignacioncargo": // Se hace el llamado desde sigesp_sno_d_asignacioncargo.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarasignacioncargo('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm','$denominacion','$ls_codest1','$ls_codest2','$ls_codest3',";
						print "'$ls_codest4','$ls_codest5','$ls_denestpro1','$ls_denestpro2','$ls_denestpro3','$ls_denestpro4','$ls_denestpro5','$ls_estcla');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "represconcuni": // Se hace el llamado desde sigesp_sno_r_resumenconceptounidad.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarrepresconcuni('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm','$denominacion');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "reprecpag": // Se hace el llamado desde sigesp_sno_r_recibopago.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreprecpag('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm','$denominacion');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "replisfir": // Se hace el llamado desde sigesp_sno_r_recibopago.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreplisfir('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm','$denominacion');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "reppagnomdes": // Se hace el llamado desde sigesp_sno_r_pagonominaunidadadmin.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreppagnomdes('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			

					case "reppagnomhas": // Se hace el llamado desde sigesp_sno_r_pagonominaunidadadmin.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptarreppagnomhas('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;	
				  
				  case "pagounides": // Se hace el llamado desde sigesp_sno_r_pagonominaunidadadmin.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptapagounides('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;	
						
				  case "pagounihas": // Se hace el llamado desde sigesp_sno_r_pagonominaunidadadmin.php
						print "<tr class=celdas-blancas>";
						print "<td align=center><a href=\"javascript: aceptapagounihas('$ls_minorguniadm','$ls_ofiuniadm',";
						print "'$ls_uniuniadm','$ls_depuniadm','$ls_prouniadm');\">".$codigo."</a></td>";
						print "<td>".$denominacion."</td>";
						print "<td align=center>".$ls_codest1.$ls_codest2.$ls_codest3.$ls_codest4.$ls_codest5."</td>";
						print "<td>".$ls_estclatipo."</td>";
						print "</tr>";			
						break;			
				}
				$rs_data->MoveNext();
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
		unset($io_unidadadmin);
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Cat&aacute;logo de Unidades Administrativas</title>
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
<style type="text/css">
<!--
.Estilo1 {font-size: 11px}
-->
</style>
</head>

<body>
<form name="form1" method="post" action="">
    <input name="operacion" type="hidden" id="operacion">
  	 <table width="500" border="0" align="center" cellpadding="1" cellspacing="1">
    	<tr>
     	 	<td width="500" height="20" colspan="2" class="titulo-ventana">Cat&aacute;logo de Unidades Administrativas  </td>
    	</tr>
  </table>
	 <br>
	 <table width="500" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
        <td width="111" height="22"><div align="right">Codigo</div></td>
        <td width="451"><div align="left">
          <input name="codigo" type="text" id="codigo" onKeyPress="javascript: ue_mostrar(this,event);">        
        </div></td>
      </tr>
      <tr>
        <td height="22"><div align="right">Denominacion</div></td>
        <td><div align="left">
          <input name="denominacion" type="text" id="denominacion" onKeyPress="javascript: ue_mostrar(this,event);">
        </div></td>
      </tr>
      <tr>
        <td height="22">&nbsp;</td>
        <td><div align="right"><a href="javascript: ue_search();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"> Buscar</a></div></td>
      </tr>
  </table>
	<br>
    <?php
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_operacion =$io_fun_nomina->uf_obteneroperacion();
	$ls_tipo=$io_fun_nomina->uf_obtenertipo();
	$codnomdes=$io_fun_nomina->uf_obtenervalor_get("codnomdes","");	
	$codnomhas=$io_fun_nomina->uf_obtenervalor_get("codnomhas","");	
	$codperides=$io_fun_nomina->uf_obtenervalor_get("codperides","");	
	$codperihas=$io_fun_nomina->uf_obtenervalor_get("codperihas","");	
	if($ls_operacion=="BUSCAR")
	{
		$ls_codigo=$_POST["codigo"];
		$ls_denominacion="%".$_POST["denominacion"]."%";
		uf_print($ls_codigo, $ls_denominacion, $ls_tipo,$codnomdes,$codnomhas,$codperides,$codperihas);
	}
	else
	{
		$ls_codigo="";
		$ls_denominacion="%%";
		uf_print($ls_codigo, $ls_denominacion, $ls_tipo,$codnomdes,$codnomhas,$codperides,$codperihas);
	}	
	unset($io_fun_nomina);
?>
</div>
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
  function aceptar(codigo,deno,codest1,codest2,codest3,codest4,codest5,denestpro1,denestpro2,denestpro3,denestpro4,denestpro5,estcla)
  {
    opener.document.form1.txtcodigo.value=codigo;
	opener.document.form1.txtcodigo.readOnly=true;
    opener.document.form1.txtdenominacion.value=deno;
	opener.document.form1.txtcodestpro1.value=codest1;
	opener.document.form1.txtcodestpro2.value=codest2;
	opener.document.form1.txtcodestpro3.value=codest3;
	opener.document.form1.txtcodestpro4.value=codest4;
	opener.document.form1.txtcodestpro5.value=codest5;
	opener.document.form1.txtdenestpro1.value=denestpro1;
	opener.document.form1.txtdenestpro2.value=denestpro2;
	opener.document.form1.txtdenestpro3.value=denestpro3;
	opener.document.form1.txtdenestpro4.value=denestpro4;
	opener.document.form1.txtdenestpro5.value=denestpro5;
	opener.document.form1.txtestcla.value=estcla;
    opener.document.form1.existe.value="TRUE";
	close();
  }

  function aceptarcestaticket(codigo,deno)
  {
    opener.document.form1.txtcodigo.value=codigo;
    opener.document.form1.txtdenominacion.value=deno;
	close();
  }

  function aceptarasignacion(ministerio,oficina,unidad,departamento,programa,desuniadm)
  {
    opener.document.form1.txtcoduniadm.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadm.readOnly=true;
    opener.document.form1.txtdesuniadm.value=desuniadm;
    opener.document.form1.txtdesuniadm.readOnly=true;
	close();
  }
  function aceptarreplisconc(ministerio,oficina,unidad,departamento,programa,desuniadm)
  {
    opener.document.form1.txtcoduniadm.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadm.readOnly=true;
    opener.document.form1.txtdenuniadm.value=desuniadm;
    opener.document.form1.txtdenuniadm.readOnly=true;
	close();
  }

  function aceptarasignacioncargo(ministerio,oficina,unidad,departamento,programa,desuniadm,codest1,codest2,codest3,codest4,
  								  codest5,denestpro1,denestpro2,denestpro3,denestpro4,denestpro5,estcla)
  {
    opener.document.form1.txtcoduniadm.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadm.readOnly=true;
    opener.document.form1.txtdesuniadm.value=desuniadm;
    opener.document.form1.txtdesuniadm.readOnly=true;
    opener.document.form1.txtcodestpro1.value=codest1;
    opener.document.form1.txtcodestpro1.readOnly=true;
    opener.document.form1.txtcodestpro2.value=codest2;
    opener.document.form1.txtcodestpro2.readOnly=true;
    opener.document.form1.txtcodestpro3.value=codest3;
    opener.document.form1.txtcodestpro3.readOnly=true;
	opener.document.form1.txtcodestpro4.value=codest4;
    opener.document.form1.txtcodestpro4.readOnly=true;
	opener.document.form1.txtcodestpro5.value=codest5;
    opener.document.form1.txtcodestpro5.readOnly=true;
    opener.document.form1.txtdenestpro1.value=denestpro1;
    opener.document.form1.txtdenestpro1.readOnly=true;
    opener.document.form1.txtdenestpro2.value=denestpro2;
    opener.document.form1.txtdenestpro2.readOnly=true;
    opener.document.form1.txtdenestpro3.value=denestpro3;
    opener.document.form1.txtdenestpro3.readOnly=true;
	opener.document.form1.txtdenestpro4.value=denestpro4;
    opener.document.form1.txtdenestpro4.readOnly=true;
	opener.document.form1.txtdenestpro5.value=denestpro5;
    opener.document.form1.txtdenestpro5.readOnly=true;
	close();
  }

  function aceptarrepresconcuni(ministerio,oficina,unidad,departamento,programa,desuniadm)
  {
    opener.document.form1.txtcoduniadm.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadm.readOnly=true;
    opener.document.form1.txtdenuniadm.value=desuniadm;
    opener.document.form1.txtdenuniadm.readOnly=true;
	close();
  }

  function aceptarreprecpag(ministerio,oficina,unidad,departamento,programa,desuniadm)
  {
    opener.document.form1.txtcoduniadm.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadm.readOnly=true;
    opener.document.form1.txtdenuniadm.value=desuniadm;
    opener.document.form1.txtdenuniadm.readOnly=true;
	close();
  }

  function aceptarreplisfir(ministerio,oficina,unidad,departamento,programa,desuniadm)
  {
    opener.document.form1.txtcoduniadm.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadm.readOnly=true;
    opener.document.form1.txtdenuniadm.value=desuniadm;
    opener.document.form1.txtdenuniadm.readOnly=true;
	close();
  }

  function aceptarreppagnomdes(ministerio,oficina,unidad,departamento,programa)
  {
    opener.document.form1.txtcoduniadmdes.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
    opener.document.form1.txtcoduniadmdes.readOnly=true;
	close();
  }

  function aceptarreppagnomhas(ministerio,oficina,unidad,departamento,programa)
  {
	if(opener.document.form1.txtcoduniadmdes.value<=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa)
	{
		opener.document.form1.txtcoduniadmhas.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;
		opener.document.form1.txtcoduniadmhas.readOnly=true;
		close();
	}
	else
	{
		alert("Rango de Unidad Administrativa inv�lido");
	}
  }
//-----------------------------------------------------------------------------------------------------------------------------
     function aceptapagounides(ministerio,oficina,unidad,departamento,programa)
	  {
		opener.document.form1.txtcodunides.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;			
		opener.document.form1.txtcodunides.readOnly=true;
		close();
	  }
	  
	 function aceptapagounihas(ministerio,oficina,unidad,departamento,programa)
	  {
		opener.document.form1.txtcodunihas.value=ministerio+"-"+oficina+"-"+unidad+"-"+departamento+"-"+programa;		
		opener.document.form1.txtcodunihas.readOnly=true;
		close();
	  }
//-----------------------------------------------------------------------------------------------------------------------------

function ue_mostrar(myfield,e)
{
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return true;
	if (keycode == 13)
	{
		ue_search();
		return false;
	}
	else
		return true
}

  function ue_search()
  {
	  f=document.form1;
	  f.operacion.value="BUSCAR";
	  f.action="sigesp_snorh_cat_uni_ad.php?tipo=<?php print $ls_tipo;?>";
	  f.submit();
  }
</script>
</html>
