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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_p_aprobacionanticipos.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
   //--------------------------------------------------------------
   function uf_limpiarvariables($ls_sueint)
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ld_fecdes,$ld_fechas,$ls_codper,$ls_nomper,$li_calintpreant,$ls_tipdocant,$ls_tipooperacion;
		global $lo_title,$io_fun_nomina,$li_totrows,$ls_operacion,$ls_titletable,$li_widthtable,$ls_nametable;

	 	$ld_fecdes=date("01/m/Y");
		$ld_fechas=date("d/m/Y");
		$ls_codper="";
		$ls_nomper="";
		$ls_tipooperacion="A";
		$ls_titletable="Antipipos de Prestación";
		$li_widthtable=710;
		$ls_nametable="grid";
		$lo_title[1]="";
		$lo_title[2]="Anticipo";
		$lo_title[3]="Código";
		$lo_title[4]="Apellidos y Nombres";
		$lo_title[5]="Fecha";
		$lo_title[6]="Monto";
		$lo_title[7]="Motivo";
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();
		$li_calintpreant=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULO_INT_FIDEICOISO","0","I"));
		$ls_tipdocant=trim($io_sno->uf_select_config("SNO","NOMINA","TIPO_DOC_ANTICIPO","","C"));
		unset($io_sno);
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_agregarlineablanca($aa_object,$ai_totrows)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	Function: uf_agregarlineablanca
		//	Arguments: aa_object  // arreglo de Objetos
		//			   ai_totrows  // total de Filas
		//	Description:  Función que agrega una linea mas en el grid
		//////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]=" ";
		$aa_object[$ai_totrows][2]=" ";
		$aa_object[$ai_totrows][3]=" ";
		$aa_object[$ai_totrows][4]=" ";
		$aa_object[$ai_totrows][5]=" ";
		$aa_object[$ai_totrows][6]=" ";
		$aa_object[$ai_totrows][7]=" ";
		return $aa_object;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Función que carga todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/04/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ld_fecdes,$ld_fechas,$ls_codper,$ls_nomper,$ls_tipooperacion,$li_totrows,$ls_operacion,$io_fun_nomina;
		
	 	$ld_fecdes=$_POST["txtfecdes"];
		$ld_fechas=$_POST["txtfechas"];
		$ls_codper=$_POST["txtcodper"];
		$ls_nomper=$_POST["txtnomper"];
		$ls_tipooperacion=$_POST["rdtipooperacion"];
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
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
<title >Aprobaci&oacute;n / Reverso / Anulaci&oacute;n de Anticipos</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
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
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<!-- AGREGADO POR OFIMATICA DE VENEZUELA -->
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<!-- FIN BLOQUE AGREGADO POR OFIMATICA DE VENEZUELA -->
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<!-- AGREGADO POR OFIMATICA DE VENEZUELA -->
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<!-- FIN BLOQUE AGREGADO POR OFIMATICA DE VENEZUELA -->
</head>
<body>
<?php 
	require_once("sigesp_snorh_c_anticipos.php");
	$io_anticipos=new sigesp_snorh_c_anticipos();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables($ls_sueint);
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$li_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,1);
			break;

		case "BUSCAR":
			uf_load_variables();
			$arrResultado=$io_anticipos->uf_buscar($ld_fecdes,$ld_fechas,$ls_codper,$ls_tipooperacion,$li_totrows,$lo_object);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_existe'];
			break;


		case "PROCESAR":
			uf_load_variables();
			$lb_valido=true;
			$io_anticipos->io_sql->begin_transaction();
			for($li_i=1;($li_i<=$li_totrows)&&($lb_valido);$li_i++)
			{
				if (array_key_exists("chkper".$li_i,$_POST))
				{
					$ls_codant=$_POST["codant".$li_i];
					$ls_codper=$_POST["codper".$li_i];
					$ls_motant=$_POST["motant".$li_i];
					$li_monant=$_POST["monant".$li_i];
					$li_monint=$_POST["monint".$li_i];
					$ls_estact="";
					$ls_estant="";
					switch ($ls_tipooperacion)
					{
						case "A": // aprobación
							$ls_estact="A";
							$ls_estant="R";
						break;
					
						case "R": // reversar aprobación
							$ls_estact="R";
							$ls_estant="A";
						break;
					
						case "X": // Anular
							$ls_estact="X";
							$ls_estant="R";
						break;
					}
					$lb_valido=$io_anticipos->uf_cambiar_estatus($ls_codant,$ls_codper,$ls_estact,$ls_estant,$li_monant,$li_monint,$li_calintpreant,
																 $ls_motant,$ls_tipdocant,$la_seguridad);
				}
			}
			if($lb_valido)
			{
				$io_anticipos->io_mensajes->message("El proceso se realizo con Exito");
				$io_anticipos->io_sql->commit();
			}
			else
			{
				$io_anticipos->io_mensajes->message("No se pudo realizar el proceso");
				$io_anticipos->io_sql->rollback();
			}
			break;
	}
	$io_anticipos->uf_destructor();
	unset($io_anticipos);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="762" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de Nómina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif"  title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif"  title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"></a><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title="Ejecutar" alt="Ejecutar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="782" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td width="780">
      <p>&nbsp;</p>
      <table width="750" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="2" class="titulo-ventana">Aprobaci&oacute;n / Reverso / Anulaci&oacute;n de Anticipos </td>
        </tr>
        <tr>
          <td width="170" height="22"><div align="right"></div></td>
          <td width="574">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Fecha Desde </div></td>
          <td>
		  	<div align="left">
		  	  <input name="txtfecdes" type="text" id="txtfecdes" value="<?php print $ld_fecdes;?>" size="15" maxlength="10" onKeyPress="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" datepicker="true">
		  	</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Fecha Hasta </div></td>
          <td><div align="left">
            <input name="txtfechas" type="text" id="txtfechas" value="<?php print $ld_fechas;?>" size="15" maxlength="10" onKeyPress="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" datepicker="true">
          </div></td>
        </tr>
        <tr>
          <td><div align="right">Personal</div></td>
          <td><input name="txtcodper" type="text" id="txtcodper" size="13" maxlength="10" value="<?php print $ls_codper;?>" readonly>
            <a href="javascript: ue_buscarpersonal();"><img id="personal" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0">
            <input name="txtnomper" type="text" id="txtnomper" size="50" value="<?php print $ls_nomper;?>" readonly>
            </a></td>
        </tr>
        <tr>
          <td colspan="2">
            <label>
            <div align="center">
              <input name="rdtipooperacion" type="radio" class="sin-borde" value="A" <?php if($ls_tipooperacion=='A'){print "checked";} ?> >
            Aprobaci&oacute;n 
            <input name="rdtipooperacion" type="radio" class="sin-borde" value="R" <?php if($ls_tipooperacion=='R'){print "checked";} ?>>
             Reverso de Aprobaci&oacute;n 
             <input name="rdtipooperacion" type="radio" class="sin-borde" value="X" <?php if($ls_tipooperacion=='X'){print "checked";} ?>>
             Anulaci&oacute;n</div>
            </label>
            <div align="left"></div></td>
          </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2">
		  	<div align="center">
			    <?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
				?>
			  </div>
              <input name="totrow" type="hidden" id="totrow" value="<?php print $li_totrows;?>">			
			  <input name="operacion" type="hidden" id="operacion"></td>		  
          </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
</body>
<script >
function ue_nuevo()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	if(li_incluir==1)
	{	
		f.operacion.value="NUEVO";
		f.action="sigesp_snorh_p_aprobacionanticipos.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		f.operacion.value="BUSCAR";
		f.action="sigesp_snorh_p_aprobacionanticipos.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_procesar()
{
	f=document.form1;
	li_ejecutar=f.ejecutar.value;
	if(li_ejecutar==1)
	{	
		total=f.totrow.value;
		valido=false;
		for(i=1;i<=total;i++)
		{
			if(eval("f.chkper"+i+".checked")==true)
			{
				valido=true;
			}
		}
		if(valido==true)
		{
			f.operacion.value="PROCESAR";
			f.action="sigesp_snorh_p_aprobacionanticipos.php";
			f.submit();		
		}
		else
		{
			alert("Debe marcar los Anticipos a aprobar");
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	location.href = "sigespwindow_blank.php";
}


function ue_buscarpersonal()
{
	window.open("sigesp_snorh_cat_personal.php?tipo=buscar","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

<!-- AGREGADO POR OFIMATICA DE VENEZUELA -->
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
<!-- FIN BLOQUE AGREGADO POR OFIMATICA DE VENEZUELA -->
</script> 
</html>