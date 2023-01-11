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
	$ls_codnom=$_SESSION["la_nomina"]["codnom"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad_nomina("SNO","sigesp_sno_p_aprobacionhojatiempo.php",$ls_codnom,$ls_permisos,$la_seguridad,$la_permisos);
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
   		global $ls_codperdes,$ls_codperhas,$ld_fecdes,$ld_fechas,$la_estatus;
		global $li_totrows,$ls_operacion,$ls_titletable,$li_widthtable,$ls_nametable,$lo_title;
		global $io_fun_nomina,$ls_desper,$li_calculada,$io_hojatiempo;
		
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ls_desper=$_SESSION["la_nomina"]["descripcionperiodo"];
		$ls_codperdes="";
		$ls_codperhas="";
		$ld_fechas=$_SESSION["la_nomina"]["fecdesper"];
		if(substr($ld_fechas,5,2)=='01')
		{
			$li_anio=substr($ld_fechas,0,4)-1;
			$ld_fecdes=$li_anio."-12-01";
		}
		else
		{
			$li_mes=str_pad(substr($ld_fechas,5,2)-1,2,"0",0);
			$ld_fecdes=substr($ld_fechas,0,4)."-".$li_mes."-01";
		}
		$ld_fecdes=$io_hojatiempo->io_funciones->uf_convertirfecmostrar($ld_fecdes);
		$ld_fechas=$io_hojatiempo->io_funciones->uf_convertirfecmostrar($ld_fechas);
		$la_estatus[0]="selected";
		$la_estatus[1]="";
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_titletable="Hoja de Tiempo";
		$li_widthtable=700;
		$ls_aplcontodo="";
		$ls_nametable="grid";
		$lo_title[1]="Personal";
		$lo_title[2]="Fecha";
		$lo_title[3]="Semana";
		$lo_title[4]="Turno";
		$lo_title[5]="Horas";
		$lo_title[6]="Horas Extra";
		$lo_title[7]="Trab. Sub.";
		$lo_title[8]="Esc.";
		$lo_title[9]="Rep./Com.";
		$lo_title[10]="Aprobado<input name=chkall type=checkbox id=chkall value=T style=height:15px;width:15px onClick=javascript:uf_select_all(); ".$ls_aplcontodo.">";
		require_once("sigesp_sno_c_calcularnomina.php");
		$io_calcularnomina=new sigesp_sno_c_calcularnomina();
		$li_calculada=str_pad($io_calcularnomina->uf_existesalida(),1,"0");
		unset($io_calcularnomina);
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
		$aa_object[$ai_totrows][1]="<input name=txtcodper".$ai_totrows." type=hidden id=txtcodper".$ai_totrows." class=sin-borde size=20 >";
		$aa_object[$ai_totrows][2]="<input name=txtfechojtie".$ai_totrows." type=text id=txtfechojtie".$ai_totrows." class=sin-borde size=12 maxlength=10 onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=ue_validar_formatofecha(this);>";
		$aa_object[$ai_totrows][3]="";
		$aa_object[$ai_totrows][4]="";
		$aa_object[$ai_totrows][5]="";
		$aa_object[$ai_totrows][6]="";
		$aa_object[$ai_totrows][7]="";
		$aa_object[$ai_totrows][8]="";
		$aa_object[$ai_totrows][9]="";
		$aa_object[$ai_totrows][10]="<input name=txtesthojtie".$ai_totrows." type=hidden id=txtesthojtie".$ai_totrows." value=0 >".
								    "<input name=chkesthojtie".$ai_totrows." type=checkbox id=chkesthojtie".$ai_totrows." value=1 class=sin-borde>";
		return $aa_object;
   }
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
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
<script type="text/javascript"  src="js/stm31.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<title>Aprobar Hoja De Tiempo</title>
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
	require_once("sigesp_sno_c_hojatiempo.php");
	$io_hojatiempo=new sigesp_sno_c_hojatiempo();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	if($_SESSION["la_nomina"]["hojtienom"]=="0")
	{
		print("<script language=JavaScript>");
		print(" alert('Esta definición esta desactiva para nóminas que no utilizan Hoja de Tiempo.');");
		print(" location.href='sigespwindow_blank_nomina.php'");
		print("</script>");
	}	
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$lo_object=uf_agregarlineablanca($lo_object,1);
			break;

		case "GUARDAR":
			$lb_valido=true;
			$io_hojatiempo->io_sql->begin_transaction();
			for($li_i=1;$li_i<$li_totrows&&$lb_valido;$li_i++)
			{
				$ls_codper=$_POST["txtcodper".$li_i];  
				$ld_fechojtie=$_POST["txtfechojtie".$li_i];  
				$li_esthojtie=$io_fun_nomina->uf_obtenervalor("chkesthojtie".$li_i,"0");
				$lb_valido=$io_hojatiempo->uf_aprobar_hojatiempo($ls_codper,$ld_fechojtie,$li_esthojtie,$la_seguridad);
			}
			if($lb_valido)
			{
				$io_hojatiempo->io_sql->commit();
				$io_hojatiempo->io_mensajes->message("La hoja de tiempo fué aprobada.");
			}
			else
			{
				$io_hojatiempo->io_sql->rollback();
				$io_hojatiempo->io_mensajes->message("Ocurrio un error al Aprobar la hoja de tiempo.");
			}
			$li_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			break;
			
		case "BUSCAR":
			$ls_codperdes=$_POST["txtcodperdes"];
			$ls_codperhas=$_POST["txtcodperhas"];
			$ld_fecdes=$_POST["txtfecdes"];
			$ld_fechas=$_POST["txtfechas"];
			$ls_estatus=$_POST["cmbestatus"];
			if($ls_estatus=='1')
			{
				$la_estatus[0]="";
				$la_estatus[1]="selected";
			}
			$arrResultado=$io_hojatiempo->uf_buscar_hojatiempo($ls_codperdes,$ls_codperhas,$ld_fecdes,$ld_fechas,$ls_estatus,$li_totrows,$lo_object, $ls_aplcontodo); 			
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['aa_object'];
			$lb_valido=$arrResultado['lb_valido'];
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
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema"><?php print $ls_desnom;?></td>
			<td width="346" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><?php print $ls_desper;?></span></div></td>
			 <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
	  </table>	</td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu_nomina.js"></script></td>
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
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank_nomina.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <table width="797" border="0" cellpadding="0" cellspacing="0" class="formato-blanco">
      <tr>
        <td width="795"  valign="top">
		  <p>&nbsp;</p>
		  <table width="751" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
              <tr class="titulo-ventana">
                <td height="20" colspan="4"><div align="center">Aprobar Hoja de Tiempo </div></td>
              </tr>
              <tr >
                <td height="22" colspan="4"> <div align="center" class="sin-borde3"></div></td>
              </tr>
              <tr>
                <td width="151" height="22"><div align="right" >
                    <p>Personal Desde </p>
                </div></td>
                <td width="196">                      <div align="left">
                  <input name="txtcodperdes" type="text" id="txtcodperdes" size="13" maxlength="10" value="" readonly>
                  <a href="javascript: ue_buscarpersonaldesde();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
                <td width="114"><div align="right">Personal Hasta </div></td>
                <td width="280"><div align="left">
                  <input name="txtcodperhas" type="text" id="txtcodperhas" value="" size="13" maxlength="10" readonly>
                  <a href="javascript: ue_buscarpersonalhasta();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
              </tr>
              <tr>
                <td height="22"><div align="right">Fecha Desde </div></td>
                <td><div align="left">
                  <input name="txtfecdes" type="text" id="txtfecdes" value="<?php print $ld_fecdes;?>" size="12" maxlength="10" readonly>
                </div></td>
                <td><div align="right">Fecha Hasta </div></td>
                <td><input name="txtfechas" type="text" id="txtfechas" value="<?php print $ld_fechas;?>" size="12" maxlength="10" readonly></td>
              </tr>
              <tr >
                <td height="22"><div align="right">Estatus</div></td>
                <td>                <div align="left">
                  <label>
                  <select name="cmbestatus" id="cmbestatus">
                    <option value="0" <?php print $la_estatus[0];?>>No Aprobado</option>
                    <option value="1" <?php print $la_estatus[1];?>>Aprobado</option>
                  </select>
                  </label>
                </div></td>
                <td><div align="right"></div></td>
                <td><div align="left"></div></td>
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
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

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
			f.action="sigesp_sno_p_aprobacionhojatiempo.php";
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

function ue_buscar()
{
	f=document.form1;
	li_calculada=f.calculada.value;
	if(li_calculada=="0")
	{		
		li_incluir=f.incluir.value;
		if(li_incluir==1)
		{	
			f.operacion.value="BUSCAR";
			f.totalfilas.value=1;
			f.action="sigesp_sno_p_aprobacionhojatiempo.php";
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
			f.operacion.value ="GUARDAR";
			f.action="sigesp_sno_p_aprobacionhojatiempo.php";
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

function ue_buscarpersonaldesde()
{
	window.open("sigesp_sno_cat_personalnomina.php?tipo=aprobarhojatiempodes","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarpersonalhasta()
{
	window.open("sigesp_sno_cat_personalnomina.php?tipo=aprobarhojatiempohas","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_cerrar()
{
	f=document.form1;
	f.action="sigespwindow_blank_nomina.php";
	f.submit();
}

function uf_select_all()
{
	  f=document.form1;	 
	  total=f.totalfilas.value; 
	  sel_all=f.chkall.value;	  	  	  
	  if(f.chkall.checked==true)
	  {
		  for(i=1;i<=total;i++)	
		  {
			eval("f.chkesthojtie"+i+".checked=true");			
		  }		 
	  }
	  else
	  {
	  	for(i=1;i<=total;i++)	
		  {
			eval("f.chkesthojtie"+i+".checked=false");			
		  }	
	  }
}
</script>
</html>
