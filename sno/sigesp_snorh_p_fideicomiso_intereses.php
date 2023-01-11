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
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_p_fideicomiso_intereses.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $li_anocurper,$ls_mescurper,$ls_desmesper,$li_totrows,$ls_operacion,$ls_titletable,$li_widthtable,$ls_nametable;
		global $lo_title,$la_nominas,$ls_existe,$io_fun_nomina,$ls_meses,$la_nomsele,$li_fidconper, $la_mescalculo;
		global $lo_object;
		
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();
	 	$li_anocurper="";
		$ls_mescurper="";
		$ls_desmesper="";
		$ls_titletable="Personal";
		$li_widthtable=710;
		$ls_nametable="grid";
		$lo_object=Array();
		$lo_title[1]="Código";
		$lo_title[2]="Cédula";
		$lo_title[3]="Apellidos y Nombres";
		$lo_title[4]="Antiguedad Acreditada";
		$lo_title[5]="Antiguedad Acumulada";
		$lo_title[6]="Anticipos Acumulados";
		$lo_title[7]="Capital Intereses";
		$lo_title[8]="Tasa de Interes";
		$lo_title[9]="Monto Interes";
		$lo_title[10]="Capital";
		$la_nominas=Array();
		$la_nomsele="";
		$li_fidconper="0";
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
		if($ls_existe=="TRUE")
		{
			$ls_meses="style='visibility:hidden'";
		}
		else
		{
			$ls_meses="style='visibility:visible'";
		}
		$la_mescalculo[0]="";
		$la_mescalculo[1]="";
		$la_mescalculo[2]="";
		$la_mescalculo[3]="";
		$la_mescalculo[4]="";
		$la_mescalculo[5]="";
		$la_mescalculo[6]="";
		$la_mescalculo[7]="";
		$la_mescalculo[8]="";
		$la_mescalculo[9]="";
		$la_mescalculo[10]="";
		$la_mescalculo[11]="";
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
		$aa_object[$ai_totrows][8]=" ";
		$aa_object[$ai_totrows][9]=" ";
		$aa_object[$ai_totrows][10]=" ";
		return $aa_object;
   }
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_print_lista($as_nombre,$as_campoclave,$as_campoimprimir,$aa_lista,$aa_seleccionado)
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_print_lista
		//		   Access: private
		//      Arguments: as_nombre  // Nombre del Campo
		//      		   as_campoclave  // campo por medio del cual se va filtrar la lista
		//      		   as_campoimprimir  // campo que se va a mostrar
		//      		   aa_lista  // arreglo que se va a colocar en la lista
		//      		   aa_seleccionado  // arreglo de nóminas que ya se ha seleccionado
		//	  Description: Función que imprime un arreglo de lista
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 11/04/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		if(empty($aa_lista[$as_campoclave]))
		{
			$li_total=0;
		}
		else
		{
			$li_total=count((Array)$aa_lista[$as_campoclave]);
		}
		print "<select name='".$as_nombre."[]' id='".$as_nombre."' size='5' style='width:350px' multiple>";
		for($li_i=0;$li_i<$li_total;$li_i++)
		{
			if(empty($aa_seleccionado))
			{
				$li_totalselec=0;
			}
			else
			{
				$li_totalselec=count((Array)$aa_seleccionado);
			}
			$ls_seleccionado="";
			for($li_j=0;$li_j<$li_totalselec;$li_j++)
			{
				if($aa_seleccionado[$li_j]==$aa_lista[$as_campoclave][$li_i])
				{
					$ls_seleccionado=" selected";
					break;
				}
			}
			print "<option value='".$aa_lista[$as_campoclave][$li_i]."' ".$ls_seleccionado.">".$aa_lista[$as_campoimprimir][$li_i];
		}
		print "</select>";
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
   		global $li_anocurper,$ls_mescurper,$ls_desmesper,$li_totrows,$ls_operacion,$la_nomsele,$ls_existe,$io_fun_nomina,$li_fidconper, $ls_mescalculo, $li_aniocalculo, $la_mescalculo;
		
	 	$li_anocurper=$_POST["txtanocurper"];
		$ls_mescurper=$_POST["txtmescurper"];
		$ls_desmesper=$_POST["txtdesmesper"];
		$li_aniocalculo=$_POST["cmbaniocalculo"]; 
		$ls_mescalculo=$_POST["cmbmescalculo"]; 
		$la_nomsele=$io_fun_nomina->uf_obtenervalor("txtnominas","");
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",1);
		$li_fidconper=$_POST["fidconper"];
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		$ls_existe=$io_fun_nomina->uf_obtenerexiste();
		
		$la_mescalculo[0]="";
		$la_mescalculo[1]="";
		$la_mescalculo[2]="";
		$la_mescalculo[3]="";
		$la_mescalculo[4]="";
		$la_mescalculo[5]="";
		$la_mescalculo[6]="";
		$la_mescalculo[7]="";
		$la_mescalculo[8]="";
		$la_mescalculo[9]="";
		$la_mescalculo[10]="";
		$la_mescalculo[11]="";
		$la_mescalculo=$io_fun_nomina->uf_seleccionarcombo("01-02-03-04-05-06-07-08-09-10-11-12",$ls_mescalculo,$la_mescalculo,12);
		
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
<title >Prestaci&oacute;n de Antiguedad (Intereses)</title>
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
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$li_calint=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULO_INT_FIDEICOISO","0","I"));
	if($li_calint=="0")
	{
		print("<script language=JavaScript>");
		print(" alert('El Sistema no está definido para calcular los intereses de Prestación Antiguedad.');");
		print(" location.href='sigespwindow_blank.php'");
		print("</script>");
	}	
	unset($io_sno);
	require_once("sigesp_snorh_c_fideicomiso_intereses.php");
	$io_fideicomiso=new sigesp_snorh_c_fideicomiso_intereses();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$li_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,1);
		break;

		case "CARGARNOMINAS":
			$li_totrows=1;
			$lo_object=uf_agregarlineablanca($lo_object,1);
			uf_load_variables();
			$arrResultado=$io_fideicomiso->uf_load_nomina($li_anocurper,$ls_mescurper,$li_fidconper,$la_nominas);
			$li_fidconper=$arrResultado['ai_fidintconper'];
			$la_nominas=$arrResultado['aa_nominas'];
			$lb_valido=$arrResultado['lb_valido'];
			$arrResultado=$io_fideicomiso->uf_load_fideiperiodointereses($li_anocurper,$ls_mescurper,$la_nominas,$li_totrows,$lo_object);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
		break;

		case "BUSCAR":
			uf_load_variables();
			$arrResultado=$io_fideicomiso->uf_load_fideiperiodointereses($li_aniocalculo,$ls_mescalculo,$la_nomsele,$li_totrows,$lo_object);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
		break;

		case "ELIMINAR":
			uf_load_variables();
			$lb_valido=$io_fideicomiso->uf_delete_fideiperiodo_intereses($li_aniocalculo,$ls_mescalculo,$la_seguridad);
			if($lb_valido)
			{
				uf_limpiarvariables($ls_sueint);
				$ls_existe="FALSE";
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,1);
			}
			else
			{
				$arrResultado=$io_fideicomiso->uf_load_fideiperiodointereses($li_aniocalculo,$ls_mescalculo,$la_nomsele,$li_totrows,$lo_object);
				$li_totrows=$arrResultado['ai_totrows'];
				$lo_object=$arrResultado['ao_object'];
				$lb_valido=$arrResultado['lb_valido'];
			}
		break;

		case "PROCESAR":
			uf_load_variables();
			$lb_valido=$io_fideicomiso->uf_procesar_fideicomiso_intereses($li_anocurper,$ls_mescurper,$li_aniocalculo,$ls_mescalculo,$la_nomsele,$la_seguridad);
			if($lb_valido===false)
			{
				$li_totrows=1;
				$lo_object=uf_agregarlineablanca($lo_object,1);
				$ls_existe="FALSE";
				$ls_meses="style='visibility:visible'";
			}
			else
			{
				$ls_existe="TRUE";
				$ls_meses="style='visibility:hidden'";
			}
			$arrResultado=$io_fideicomiso->uf_load_fideiperiodointereses($li_aniocalculo,$ls_mescalculo,$la_nomsele,$li_totrows,$lo_object);			
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];
		break;
	}
	$arrResultado=$io_fideicomiso->uf_load_nomina($li_anocurper,$ls_mescurper,$li_fidconper,$la_nominas);
	$li_fidconper=$arrResultado['ai_fidintconper'];
	$la_nominas=$arrResultado['aa_nominas'];
	$lb_valido=$arrResultado['lb_valido'];
	$io_fideicomiso->uf_destructor();
	unset($io_fideicomiso);
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif"  title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif"  title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" title="Eliminar" alt="Eliminar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title="Ejecutar" alt="Ejecutar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif" title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
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
          <td height="20" colspan="2" class="titulo-ventana">Prestaci&oacute;n de Antiguedad (Intereses) </td>
        </tr>
        <tr>
          <td width="170" height="22"><div align="right"></div></td>
          <td width="574">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Per&iacute;odos de Prestaci&oacute;n Antiguedad/Deuda Anterior </div></td>
          <td>
		  	<div align="left">
		  	  <input name="txtanocurper" type="text" id="txtanocurper" value="<?php print $li_anocurper;?>" size="7" maxlength="4" readonly>
		  	  <input name="txtmescurper" type="text" id="txtmescurper" value="<?php print $ls_mescurper;?>" size="6" maxlength="3" readonly>
		  	  <a href="javascript: ue_buscarmeses();"><img id="meses" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0" <?php print $ls_meses; ?>></a>
		  	  <input name="txtdesmesper" type="text" class="sin-borde" id="txtdesmesper" value="<?php print $ls_desmesper;?>" size="30" maxlength="20" readonly>
	  	      </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Mes de C&aacute;lculo </div></td>
          <td><label>
            <select name="cmbaniocalculo">
              <option value="<?php print substr($_SESSION["la_empresa"]["periodo"],0,4);?>"><?php print substr($_SESSION["la_empresa"]["periodo"],0,4);?></option>
            </select>
            <select name="cmbmescalculo">
              <option value="01" <?php print $la_mescalculo[0];?>>ENERO</option>
              <option value="02" <?php print $la_mescalculo[1];?>>FEBRERO</option>
              <option value="03" <?php print $la_mescalculo[2];?>>MARZO</option>
              <option value="04" <?php print $la_mescalculo[3];?>>ABRIL</option>
              <option value="05" <?php print $la_mescalculo[4];?>>MAYO</option>
              <option value="06" <?php print $la_mescalculo[5];?>>JUNIO</option>
              <option value="07" <?php print $la_mescalculo[6];?>>JULIO</option>
              <option value="08" <?php print $la_mescalculo[7];?>>AGOSTO</option>
              <option value="09" <?php print $la_mescalculo[8];?>>SEPTIEMBRE</option>
              <option value="10" <?php print $la_mescalculo[9];?>>OCTUBRE</option>
              <option value="11" <?php print $la_mescalculo[10];?>>NOVIEMBRE</option>
              <option value="12" <?php print $la_mescalculo[11];?>>DICIEMBRE</option>
            </select>
          </label></td>
        </tr>
        <tr>
          <td height="22"><div align="right">N&oacute;minas</div></td>
          <td><div align="left">
            <?php uf_print_lista("txtnominas","codnom","desnom",$la_nominas,$la_nomsele); ?>
          </div></td>
        </tr>
        <tr>
          <td><div align="right"></div></td>
          <td>
		  	<div align="left">
		  	  <input name="operacion" type="hidden" id="operacion">
		  	  <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
	  	      </div></td>
        </tr>
        <tr>
          <td colspan="2">
		  	<div align="center">
			    <?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
				?>
			  </div>
              <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
              <input name="fidconper" type="hidden" id="fidconper" value="<?php print $li_fidconper;?>"></td>		  
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
		f.existe.value="FALSE";
		f.action="sigesp_snorh_p_fideicomiso_intereses.php";
		f.submit();
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_eliminar()
{
	f=document.form1;
	li_eliminar=f.eliminar.value;
	if(li_eliminar==1)
	{	
		if(f.existe.value=="TRUE")
		{
			fidconper=f.fidconper.value;
			if(fidconper=="0")
			{
				var nomsel=0;
				var totnom=0;
				anocurper=ue_validarvacio(f.cmbaniocalculo.value);
				mescurper=ue_validarvacio(f.cmbmescalculo.value);
				if(f.txtnominas!=null)
				{
					totnom=f.txtnominas.length;
				}
				for(i=0;i<totnom;i++) // se coloca en el arreglo los campos seleccionados
				{	
					if(f.txtnominas.options[i].selected) 
					{
						nomsel=nomsel+1;
					}
				}
				if ((anocurper!="")&&(mescurper!="")&&(nomsel>0))
				{
					if(confirm("¿Desea eliminar el Registro Fideicomiso del Año "+anocurper+" Período "+mescurper+"?"))
					{
						f.operacion.value="ELIMINAR";
						f.action="sigesp_snorh_p_fideicomiso_intereses.php";
						f.submit();
					}
				}
				else
				{
					alert("Debe buscar el registro a eliminar.");
				}
			}
			else
			{
				alert("La Prestación Antiguedad esta Contabilizada. Debe Reversarla para poder eliminar.");
			}
		}
		else
		{
			alert("Debe buscar el registro a eliminar.");
		}
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
		window.open("sigesp_snorh_cat_fideicomiso_intereses.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
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
	valido=false;
	if(li_ejecutar==1)
	{	
		fidconper=f.fidconper.value;
		if(fidconper=="0")
		{
			totnom=0;
			nomsel=0;
			anocurper=ue_validarvacio(f.txtanocurper.value);
			mescurper=ue_validarvacio(f.txtmescurper.value);
			mescalculo=ue_validarvacio(f.cmbmescalculo.value);
			aniocalculo=ue_validarvacio(f.cmbaniocalculo.value);
			if(f.txtnominas!=null)
			{
				totnom=f.txtnominas.length;
			}
			for(i=0;i<totnom;i++) // se coloca en el arreglo los campos seleccionados
			{	
				if(f.txtnominas.options[i].selected) 
				{
					nomsel=nomsel+1;
				}
			}
			if(aniocalculo>anocurper)
			{
				valido=true;
			}
			else
			{
				if(aniocalculo==anocurper)
				{
					if(mescurper <= mescalculo)
					{
						valido=true;
					}
					else
					{
						alert("El mes de calculo, no puede ser menor al mes de la prestacion o deuda anterior.");
					}
				}
				else
				{
					alert("El año de calculo, no puede ser menor al año de la prestacion o deuda anterior.");
				}
			}
			if(valido)
			{
				if ((anocurper!="")&&(mescurper!="")&&(nomsel>0))
				{
					f.operacion.value="PROCESAR";
					f.action="sigesp_snorh_p_fideicomiso_intereses.php";
					f.submit();
				}
				else
				{
					alert("Debe seleccionar el año, mes y al menos una nómina.");
				}
			}
		}
		else
		{
			alert("La Prestación Antiguedad (Intereses) esta Contabilizada. Debe Reversarla para poder procesar.");
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

function ue_buscarmeses()
{
	f=document.form1;
	if(f.existe.value=="FALSE")
	{
		window.open("sigesp_snorh_cat_fideicomiso.php?tipo=intereses","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=200,top=200,location=no,resizable=no");
	}
}
</script> 
</html>