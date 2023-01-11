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
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "window.close();";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_d_hojatiempo.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function:  uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/07/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_codper,$ls_nomper,$ls_cedper,$ls_uniad,$ls_codnom,$ls_desnom,$ld_fecdesper,$li_totrows,$ls_operacion;
		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$io_fun_nomina,$li_calculada;
		
		$ls_codper="";
		$ls_nomper="";
		$ls_cedper="";
		$ls_uniad="";
		$ls_codnom="";
		$ls_desnom="";
		$ld_fecdesper="";
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_titletable="Hoja de Tiempo";
		$li_widthtable=650;
		$ls_nametable="grid";
		$lo_title[1]="Fecha";
		$lo_title[2]="Semana";
		$lo_title[3]="Turno";
		$lo_title[4]="Horas";
		$lo_title[5]="Horas Extra";
		$lo_title[6]="Trab. Subterraneo";
		$lo_title[7]="Escalera";
		$lo_title[8]="Reposo/Comida";
		$lo_title[9]="Aprobado";
		$lo_title[10]=" ";
		$lo_title[11]=" ";
		$li_calculada=0;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_agregarlineablanca
		//		   Access: private
		//	    Arguments: aa_object  // arreglo de Objetos
		//			       ai_totrows  // total de Filas
		//	  Description: Función que agrega una linea mas en el grid
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/07/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]="<input name=txtfechojtie".$ai_totrows." type=text id=txtfechojtie".$ai_totrows." class=sin-borde size=12 maxlength=10 onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=ue_validar_formatofecha(this);>";
		$aa_object[$ai_totrows][2]="<input name=txtsemhojtie".$ai_totrows." type=text id=txtsemhojtie".$ai_totrows." class=sin-borde size=4 maxlength=2 onKeyPress=return(ue_formatonumero(this,'','',event))>";
		$aa_object[$ai_totrows][3]="<input name=txtcodhor".$ai_totrows." type=hidden id=txtcodhor".$ai_totrows." >".
								   "<input name=txtdenhor".$ai_totrows." type=text id=txtdenhor".$ai_totrows." class=sin-borde size=25 readonly>".
								   "<a href='javascript:ue_buscarhorario(".$ai_totrows.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Buscar' width='15' height='15' border='0'></a>";
		$aa_object[$ai_totrows][4]="<input name=txthorlab".$ai_totrows." type=text id=txthorlab".$ai_totrows." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
		$aa_object[$ai_totrows][5]="<input name=txthorextlab".$ai_totrows." type=text id=txthorextlab".$ai_totrows." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
		$aa_object[$ai_totrows][6]="<input name=chktrasub".$ai_totrows." type=checkbox id=chktrasub".$ai_totrows." value=1 class=sin-borde>";
		$aa_object[$ai_totrows][7]="<input name=chktraesc".$ai_totrows." type=checkbox id=chktraesc".$ai_totrows." value=1 class=sin-borde>";
		$aa_object[$ai_totrows][8]="<input name=chkrepcom".$ai_totrows." type=checkbox id=chkrepcom".$ai_totrows." value=1 class=sin-borde>";
		$aa_object[$ai_totrows][9]="<input name=txtesthojtie".$ai_totrows." type=hidden id=txtesthojtie".$ai_totrows." value=0 >".
								   "<input name=chkesthojtie".$ai_totrows." type=checkbox id=chkesthojtie".$ai_totrows." value=1 class=sin-borde disabled>";
		$aa_object[$ai_totrows][10]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
		$aa_object[$ai_totrows][11]="<a href=javascript:uf_eliminar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";	
		return $aa_object;
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="js/stm31.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title>Definici&oacute;n de Hoja de Tiempo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">
<!--
body {
	background-color: #EAEAEA;
	margin-left: 0px;
}
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
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_hojatiempo.php");
	$io_hojatiempo=new sigesp_snorh_c_hojatiempo();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$lo_object=uf_agregarlineablanca($lo_object,1);
		break;

		case "AGREGARDETALLE":
			$ls_codper=$_POST["txtcodper"];
			$ls_nomper=$_POST["txtnomper"];
			$ls_cedper=$_POST["txtcedper"];
			$ls_uniad=$_POST["txtuniad"];
			$ls_codnom=$_POST["txtcodnom"];
			$ls_desnom=$_POST["txtdesnom"];
			$ld_fecdesper=$_POST["txtfecdesper"];
			$li_totrows=$li_totrows+1;			
			for($li_i=1;$li_i<$li_totrows;$li_i++)
			{
				$ld_fechojtie=$_POST["txtfechojtie".$li_i];
				$li_semhojtie=$_POST["txtsemhojtie".$li_i];
				$ls_codhor=$_POST["txtcodhor".$li_i];
				$ls_denhor=$_POST["txtdenhor".$li_i];
				$li_horlab=$_POST["txthorlab".$li_i];
				$li_horextlab=$_POST["txthorextlab".$li_i];
				$li_trasub=$io_fun_nomina->uf_obtenervalor("chktrasub".$li_i,"0");
				$li_traesc=$io_fun_nomina->uf_obtenervalor("chktraesc".$li_i,"0");
				$li_repcom=$io_fun_nomina->uf_obtenervalor("chkrepcom".$li_i,"0");
				$li_esthojtie=$_POST["txtesthojtie".$li_i];
				$ls_trasub=$io_fun_nomina->uf_obtenervariable($li_trasub,1,0,"checked","","");
				$ls_traesc=$io_fun_nomina->uf_obtenervariable($li_traesc,1,0,"checked","","");
				$ls_repcom=$io_fun_nomina->uf_obtenervariable($li_repcom,1,0,"checked","","");
				$ls_esthojtie=$io_fun_nomina->uf_obtenervariable($li_esthojtie,1,0,"checked","","");
				$li_semhojtie = strftime("%W", mktime(0,0,0,substr($ld_fechojtie,3,2),substr($ld_fechojtie,0,2),substr($ld_fechojtie,6,4)));
				$li_semhojtie += 0; 
				$primer_dia_anno = getdate(mktime(0,0,0,1,1,substr($ld_fechojtie,6,4)));
				if ($primer_dia_anno["wday"] != 1)
				$li_semhojtie += 1;

				
				$lo_object[$li_i][1]="<input name=txtfechojtie".$li_i." type=text id=txtfechojtie".$li_i." value=".$ld_fechojtie." class=sin-borde size=12 maxlength=10 onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=ue_validar_formatofecha(this);>";
				$lo_object[$li_i][2]="<input name=txtsemhojtie".$li_i." type=text id=txtsemhojtie".$li_i." value=".$li_semhojtie." class=sin-borde size=4 maxlength=2 onKeyPress=return(ue_formatonumero(this,'','',event))>";
				$lo_object[$li_i][3]="<input name=txtcodhor".$li_i." type=hidden id=txtcodhor".$li_i." value=".$ls_codhor.">".
									 "<input name=txtdenhor".$li_i." type=text id=txtdenhor".$li_i." value=".$ls_denhor." class=sin-borde size=25 readonly>".
									 "<a href='javascript:ue_buscarhorario(".$li_i.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Buscar' width='15' height='15' border='0'></a>";
				$lo_object[$li_i][4]="<input name=txthorlab".$li_i." type=text id=txthorlab".$li_i." value=".$li_horlab." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
				$lo_object[$li_i][5]="<input name=txthorextlab".$li_i." type=text id=txthorextlab".$li_i." value=".$li_horextlab." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
				$lo_object[$li_i][6]="<input name=chktrasub".$li_i." type=checkbox id=chktrasub".$li_i." value=1 class=sin-borde ".$ls_trasub.">";
				$lo_object[$li_i][7]="<input name=chktraesc".$li_i." type=checkbox id=chktraesc".$li_i." value=1 class=sin-borde ".$ls_traesc.">";
				$lo_object[$li_i][8]="<input name=chkrepcom".$li_i." type=checkbox id=chkrepcom".$li_i." value=1 class=sin-borde ".$ls_repcom.">";
				$lo_object[$li_i][9]="<input name=txtesthojtie".$li_i." type=hidden id=txtesthojtie".$li_i." value=".$li_esthojtie." >".
									 "<input name=chkesthojtie".$li_i." type=checkbox id=chkesthojtie".$li_i." value=1 class=sin-borde ".$ls_esthojtie." disabled>";
				$lo_object[$li_i][10]="<a href=javascript:uf_agregar_dt(".$li_i.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
				$lo_object[$li_i][11]="<a href=javascript:uf_eliminar_dt(".$li_i.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";	
			}
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
		break;

		case "ELIMINARDETALLE":
			$ls_codper=$_POST["txtcodper"];
			$ls_nomper=$_POST["txtnomper"];
			$ls_cedper=$_POST["txtcedper"];
			$ls_uniad=$_POST["txtuniad"];
			$ls_codnom=$_POST["txtcodnom"];
			$ls_desnom=$_POST["txtdesnom"];
			$ld_fecdesper=$_POST["txtfecdesper"];
			$li_rowdelete=$_POST["filadelete"];
			$li_totrows=$li_totrows-1;	
			$li_temp=0;		
			for($li_i=1;$li_i<=$li_totrows;$li_i++)
			{
				if($li_i!=$li_rowdelete)
				{		
					$li_temp++;
					$ld_fechojtie=$_POST["txtfechojtie".$li_i];
					$li_semhojtie=$_POST["txtsemhojtie".$li_i];
					$ls_codhor=$_POST["txtcodhor".$li_i];
					$ls_denhor=$_POST["txtdenhor".$li_i];
					$li_horlab=$_POST["txthorlab".$li_i];
					$li_horextlab=$_POST["txthorextlab".$li_i];
					$li_trasub=$io_fun_nomina->uf_obtenervalor("chktrasub".$li_i,"0");
					$li_traesc=$io_fun_nomina->uf_obtenervalor("chktraesc".$li_i,"0");
					$li_repcom=$io_fun_nomina->uf_obtenervalor("chkrepcom".$li_i,"0");
					$li_esthojtie=$_POST["txtesthojtie".$li_i];
					$ls_trasub=$io_fun_nomina->uf_obtenervariable($li_trasub,1,0,"checked","","");
					$ls_traesc=$io_fun_nomina->uf_obtenervariable($li_traesc,1,0,"checked","","");
					$ls_repcom=$io_fun_nomina->uf_obtenervariable($li_repcom,1,0,"checked","","");
					$ls_esthojtie=$io_fun_nomina->uf_obtenervariable($li_esthojtie,1,0,"checked","","");
					
					$lo_object[$li_temp][1]="<input name=txtfechojtie".$li_temp." type=text id=txtfechojtie".$li_temp." value=".$ld_fechojtie." class=sin-borde size=12 maxlength=10 onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=ue_validar_formatofecha(this);>";
					$lo_object[$li_temp][2]="<input name=txtsemhojtie".$li_temp." type=text id=txtsemhojtie".$li_temp." value=".$li_semhojtie." class=sin-borde size=4 maxlength=2 onKeyPress=return(ue_formatonumero(this,'','',event))>";
					$lo_object[$li_temp][3]="<input name=txtcodhor".$li_temp." type=hidden id=txtcodhor".$li_temp." value=".$ls_codhor.">".
										 "<input name=txtdenhor".$li_temp." type=text id=txtdenhor".$li_temp." value=".$ls_denhor." class=sin-borde size=25 readonly>".
										 "<a href='javascript:ue_buscarhorario(".$li_temp.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Buscar' width='15' height='15' border='0'></a>";
					$lo_object[$li_temp][4]="<input name=txthorlab".$li_temp." type=text id=txthorlab".$li_temp." value=".$li_horlab." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
					$lo_object[$li_temp][5]="<input name=txthorextlab".$li_temp." type=text id=txthorextlab".$li_temp." value=".$li_horextlab." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
					$lo_object[$li_temp][6]="<input name=chktrasub".$li_temp." type=checkbox id=chktrasub".$li_temp." value=1 class=sin-borde ".$ls_trasub.">";
					$lo_object[$li_temp][7]="<input name=chktraesc".$li_temp." type=checkbox id=chktraesc".$li_temp." value=1 class=sin-borde ".$ls_traesc.">";
					$lo_object[$li_temp][8]="<input name=chkrepcom".$li_temp." type=checkbox id=chkrepcom".$li_temp." value=1 class=sin-borde ".$ls_repcom.">";
					$lo_object[$li_temp][9]="<input name=txtesthojtie".$li_temp." type=hidden id=txtesthojtie".$li_temp." value=".$li_esthojtie." >".
											"<input name=chkesthojtie".$li_temp." type=checkbox id=chkesthojtie".$li_temp." value=1 class=sin-borde ".$ls_esthojtie." disabled>";
					$lo_object[$li_temp][10]="<a href=javascript:uf_agregar_dt(".$li_temp.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
					$lo_object[$li_temp][11]="<a href=javascript:uf_eliminar_dt(".$li_temp.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";	
				}
			}
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
		break;

		case "BUSCARDETALLE":
			$ls_codper=$_POST["txtcodper"];
			$ls_nomper=$_POST["txtnomper"];
			$ls_cedper=$_POST["txtcedper"];
			$ls_uniad=$_POST["txtuniad"];
			$ls_codnom=$_POST["txtcodnom"];
			$ls_desnom=$_POST["txtdesnom"];
			$ld_fecdesper=$_POST["txtfecdesper"];
			$io_hojatiempo->ls_codnom=$ls_codnom;
			$io_hojatiempo->ld_fecdesper=$ld_fecdesper;
			$li_totrows=0;
			$lo_object=Array();
			$arrResultado=$io_hojatiempo->uf_load_hojatiempo($ls_codper,$li_totrows,$lo_object); 			
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['aa_object'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido==false)
			{
				$li_totrows=1;				
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			}
			break;

		case "GUARDAR":
			$ls_codper=$_POST["txtcodper"];
			$ls_nomper=$_POST["txtnomper"];
			$ls_cedper=$_POST["txtcedper"];
			$ls_uniad=$_POST["txtuniad"];
			$ls_codnom=$_POST["txtcodnom"];
			$ls_desnom=$_POST["txtdesnom"];
			$ld_fecdesper=$_POST["txtfecdesper"];
			$lb_valido=true;
			$io_hojatiempo->ls_codnom=$ls_codnom;
			$io_hojatiempo->ld_fecdesper=$ld_fecdesper;			
			$io_hojatiempo->io_sql->begin_transaction();
			$lb_valido=$io_hojatiempo->uf_delete_hojatiempo($ls_codper,$la_seguridad);
			for($li_i=1;$li_i<$li_totrows&&$lb_valido;$li_i++)
			{
				$ld_fechojtie=$_POST["txtfechojtie".$li_i];
				$li_semhojtie=$_POST["txtsemhojtie".$li_i];
				$ls_codhor=$_POST["txtcodhor".$li_i];
				$ls_denhor=$_POST["txtdenhor".$li_i];
				$li_horlab=$_POST["txthorlab".$li_i];
				$li_horextlab=$_POST["txthorextlab".$li_i];
				$li_trasub=$io_fun_nomina->uf_obtenervalor("chktrasub".$li_i,"0");
				$li_traesc=$io_fun_nomina->uf_obtenervalor("chktraesc".$li_i,"0");
				$li_repcom=$io_fun_nomina->uf_obtenervalor("chkrepcom".$li_i,"0");
				$li_esthojtie=$_POST["txtesthojtie".$li_i];
				if($li_esthojtie=='0')
				{
					$lb_valido=$io_hojatiempo->uf_guardar_hojatiempo($ls_codper,$ld_fechojtie,$li_semhojtie,$ls_codhor,$li_horlab,
																	 $li_horextlab,$li_trasub,$li_traesc,$li_repcom,$la_seguridad);
				}
			}
			if($lb_valido)
			{
				$io_hojatiempo->io_sql->commit();
				$io_hojatiempo->io_mensajes->message("La Hoja de Tiempo fue registrada.");
			}
			else
			{
				$io_hojatiempo->io_sql->rollback();
				$io_hojatiempo->io_mensajes->message("Ocurrio un error al registrar la hoja de tiempo.");
			}
			$li_totrows=0;
			$lo_object=Array();
			$arrResultado=$io_hojatiempo->uf_load_hojatiempo($ls_codper,$li_totrows,$lo_object); 			
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['aa_object'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido==false)
			{
				$li_totrows=1;				
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			}
			break;
			
	}
	$io_hojatiempo->uf_destructor();
	unset($io_hojatiempo);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>
	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title='Nuevo' alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title='Guardar 'alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title='Salir' alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title='Ayuda' alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<div align="center">
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="650" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td  valign="top">
		  <p>&nbsp;</p>
		  <table width="600" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
              <tr class="titulo-ventana">
                <td height="20" colspan="4"><div align="center">Hoja de Tiempo </div></td>
              </tr>
              <tr >
                <td height="22" colspan="4"> <div align="center" class="sin-borde3"></div></td>
              </tr>
              <tr>
                <td width="159" height="22"><div align="right" >
                    <p>Codigo</p>
                </div></td>
                <td width="172">                      <div align="left">
                  <input name="txtcodper" type="text" class="sin-borde3" id="txtcodper" style="text-align:left "  value="<?php print $ls_codper ?>" size="13" maxlength="10" readonly>
                </div></td>
                <td width="67"><div align="right">Cedula</div></td>
                <td width="167"><div align="left">
                  <input name="txtcedper" type="text" class="sin-borde3" id="txtcedper" style="text-align:left" value="<?php print $ls_cedper ?>" size="13" maxlength="10" readonly>
</div></td>
              </tr>
              <tr>
                <td height="22"><div align="right">Nombre</div></td>
                <td colspan="3"><div align="left">
                    <input name="txtnomper" type="text" class="sin-borde3" id="txtnomper"  value="<?php print $ls_nomper ?>" size="50" maxlength="40" readonly>
                </div></td>
              </tr>
              <tr >
                <td height="22"><div align="right">Unidad Administrativa</div></td>
                <td colspan="3">                <div align="left">
                  <input name="txtuniad" type="text" class="sin-borde3" id="txtuniad" value="<?php print $ls_uniad ?>" size="50" maxlength="30" readonly>
</div></td>
              </tr>
              <tr>
                <td height="18"><div align="right">N&oacute;mina </div></td>
                <td height="18" colspan="3"><input name="txtdesnom" type="text" class="sin-borde3" id="txtdesnom" value="<?php print $ls_desnom ?>" size="50" maxlength="30" readonly>
				<input name="txtcodnom" type="hidden" id="txtcodnom" value="<?php print $ls_codnom ?>">
				<input name="txtfecdesper" type="hidden" id="txtfecdesper" value="<?php print $ld_fecdesper ?>"></td>
              </tr>
            <tr>
              <td height="18" colspan="4"><div align="center">
		    <?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
			?>
                <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows; ?>">
                <input name="operacion" type="hidden" id="operacion">
				 <input name="calculada" type="hidden" id="calculada" value="<?php print $li_calculada;?>">
                <input name="filaactual" type="hidden" id="filaactual">
				<input name="filadelete" type="hidden" id="filadelete">
              </div></td>
            </tr>
            <tr>
              <td height="18" colspan="4"><div align="center">
</div></td>
            </tr>
          </table>
        <p>&nbsp;</p></td>
      </tr>
  </table>
  </form>
</div>
</body>
<script >
function ue_nuevo()
{
	f=document.form1;
	li_calculada=f.calculada.value;
	if(li_calculada=="0")
	{		
		li_incluir=f.incluir.value;
		if(li_incluir==1)
		{	
			f.operacion.value="NUEVO";
			f.totalfilas.value=1;
			f.action="sigesp_snorh_d_hojatiempo.php";
			f.submit();
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}
	else
	{
		alert("La nómina ya se calculó reverse y vuelva a intentar");
	}
}
function ue_guardar()
{
	f=document.form1;
	li_calculada=f.calculada.value;
	if(li_calculada=="0")
	{		
		li_cambiar=f.cambiar.value;
		if(li_cambiar==1)
		{
			codper=f.txtcodper.value;
			if(codper=='')
			{
				alert("Debe seleccionar el personal.");
			}
			else
			{
				f.operacion.value ="GUARDAR";
				f.action="sigesp_snorh_d_hojatiempo.php";
				f.submit();
			}
		}
		else
		{
			alert("No tiene permiso para realizar esta operacion");
		}
	}
	else
	{
		alert("La nómina ya se calculó reverse y vuelva a intentar");
	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		window.open("sigesp_snorh_cat_hojatiempo.php?tipo=hojatiempo","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	f=document.form1;
	f.action="sigespwindow_blank.php";
	f.submit();
}

function ue_buscarhorario(fila)
{	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		f.filaactual.value=fila;
		window.open('sigesp_snorh_cat_horario.php?tipo=hojatiempo','catalogo','menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no');
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function uf_agregar_dt(fila)
{
	f=document.form1;	
	total=f.totalfilas.value;
	codper=f.txtcodper.value;
	if(codper=='')
	{
		alert("Debe seleccionar el personal.");
	}
	else
	{
		if(total==fila)
		{
			valido=true;
			fechojtienew=eval("f.txtfechojtie"+fila+".value");
			for(i=1;i<=total&&valido!=true;i++)
			{
				fechojtie=eval("f.txtfechojtie"+i+".value");
				if((fechojtie==fechojtienew)&&(i!=row))
				{
					alert("La Fecha ya Existe");
					valido=false;
				}
			}
			fechojtie=eval("f.txtfechojtie"+fila+".value");
			semhojtie=eval("f.txtsemhojtie"+fila+".value");
			codhor=eval("f.txtcodhor"+fila+".value");
			horlab=eval("f.txthorlab"+fila+".value");
			horextlab=eval("f.txthorextlab"+fila+".value");
			trasub=eval("f.chktrasub"+fila+".value");
			traesc=eval("f.chktraesc"+fila+".value");
			repcom=eval("f.chkrepcom"+fila+".value");
			fechojtie=ue_validarvacio(fechojtie);
			semhojtie=ue_validarvacio(semhojtie);
			codhor=ue_validarvacio(codhor);
			horlab=ue_validarvacio(horlab);
			horextlab=ue_validarvacio(horextlab);
			trasub=ue_validarvacio(trasub);
			traesc=ue_validarvacio(traesc);
			repcom=ue_validarvacio(repcom);
			if((fechojtie=="")||(codhor=="")||(horlab=="")||(horextlab==""))
			{
				alert("Debe llenar los campos: Fecha, Turno, horas Laboradas, Horas Extra.");
				valido=false;
			}
			if(valido)
			{
				f.operacion.value="AGREGARDETALLE";
				f.action="sigesp_snorh_d_hojatiempo.php";
				f.submit();
			}
		}
	}
}

function uf_eliminar_dt(fila)
{
	f=document.form1;
	li_total=f.totalfilas.value;
	codper=f.txtcodper.value;
	if(codper=='')
	{
		alert("Debe seleccionar el personal.");
	}
	else
	{
		if(li_total>fila)
		{
			esthojtie=eval("f.txtesthojtie"+fila+".value");
			if(esthojtie=='0')
			{
				if(confirm("¿Desea eliminar el Registro actual?"))
				{
					f.filadelete.value=fila;
					f.operacion.value="ELIMINARDETALLE"
					f.action="sigesp_snorh_d_hojatiempo.php";
					f.submit();
				}
			}
			else
			{
				alert("No puede eliminar este registro ya fue aprobado.");
			}
		}
	}
}


var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
</script>
</html>
