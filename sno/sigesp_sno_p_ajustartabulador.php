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
	$arrResultado=$io_fun_nomina->uf_load_seguridad_nomina("SNO","sigesp_sno_p_ajustartabulador.php",$ls_codnom,$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_limpiarvariables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Función que limpia todas las variables necesarias en la página
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
   		global $ls_desnom,$ls_peractnom,$ld_fecdesper,$ld_fechasper,$li_totper,$li_totperfil,$li_poraum,$li_porcom,$li_porpri;
		global $ls_titletable,$li_widthtable,$ls_nametable,$lo_title,$li_totrows,$io_fun_nomina,$li_rac,$ls_codtab,$ls_destab;
		global $ls_operacion,$ls_desper,$li_calculada;
		
		require_once("sigesp_sno.php");
		$io_sno=new sigesp_sno();
		$lb_valido=$io_sno->uf_crear_sessionnomina();		
		$ls_desnom="";
		$ls_peractnom="";
		$ls_desper="";			
		$ld_fecdesper="";
		$ld_fechasper="";
		if($lb_valido==false)
		{
			print "<script language=JavaScript>";
			print "location.href='sigespwindow_blank.php'";
			print "</script>";		
		}
		else
		{
			$ls_desnom=$_SESSION["la_nomina"]["desnom"];
			$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
			$ls_desper=$_SESSION["la_nomina"]["descripcionperiodo"];
			$ld_fecdesper=$io_sno->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
			$ld_fechasper=$io_sno->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
		}
		$ls_codtab="";
		$ls_destab="";
		$li_totper="0";
		$li_totperfil="0";
		$li_poraum="0";
		$li_porcom="0";
		$li_porpri="0";
		$ls_titletable="Pasos y Grados";
		$li_widthtable=700;
		$ls_nametable="grid";
		$lo_title[1]="Grado";
		$lo_title[2]="Paso";
		$lo_title[3]="Salario Actual";
		$lo_title[4]="Salario Nuevo";
		$lo_title[5]="Compensacion Actual";
		$lo_title[6]="Compensacion Nueva";
		$li_totrows=$io_fun_nomina->uf_obtenervalor("totalfilas",0);
		$li_rac=$_SESSION["la_nomina"]["racnom"];
		$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
		unset($io_sno);
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
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$aa_object[$ai_totrows][1]=" ";
		$aa_object[$ai_totrows][2]=" ";
		$aa_object[$ai_totrows][3]=" ";
		$aa_object[$ai_totrows][4]="<input name=txtsuenue".$ai_totrows." type=text id=txtsuenue".$ai_totrows." class=sin-borde size=20 maxlength=23 onKeyPress=return(ue_formatonumero(this,'.',',',event));>";
		$aa_object[$ai_totrows][5]=" ";
		$aa_object[$ai_totrows][6]="<input name=txtcomnue".$ai_totrows." type=text id=txtcomnue".$ai_totrows." class=sin-borde size=20 maxlength=23 onKeyPress=return(ue_formatonumero(this,'.',',',event));>";
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
<title >Ajustar Tabulador</title>
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
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	require_once("sigesp_sno_c_ajustes.php");
	$io_ajustes=new sigesp_sno_c_ajustes();
	require_once("../shared/class_folder/grid_param.php");
	$io_grid=new grid_param();
	uf_limpiarvariables();
	if($li_rac=="0")
	{
		print("<script language=JavaScript>");
		print(" alert('Esta definición esta desactiva para nóminas que NO utilizan RAC.');");
		print(" location.href='sigespwindow_blank_nomina.php'");
		print("</script>");
	}	
	switch ($ls_operacion) 
	{
		case "NUEVO":
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			break;

		case "GUARDAR":
			$io_ajustes->io_sql->begin_transaction();
			$lb_valido=true;
			$ls_codperi=$_POST["txtperactnom"];
			$ls_codtab=$_POST["txtcodtab"];
			$ls_destab=$_POST["txtdestab"];  
                        $li_porpri=$_POST["txtporpri"];                        
			$li_porpri=str_replace(".","",$li_porpri);
			$li_porpri=str_replace(",",".",$li_porpri);
                        $li_porpri=$li_porpri/100;
			for($li_i=1;($li_i<=$li_totrows)&&($lb_valido);$li_i++)
			{
				$ls_codgra=$_POST["txtcodgra".$li_i];
				$ls_codpas=$_POST["txtcodpas".$li_i];
				$li_monsalgra=$_POST["txtmonsalgra".$li_i];
				$li_monsalgranue=$_POST["txtmonsalgranue".$li_i];
				$li_moncomgra=$_POST["txtmoncomgra".$li_i];
				$li_moncomgranue=$_POST["txtmoncomgranue".$li_i];

				$li_monsalgra=str_replace(".","",$li_monsalgra);
				$li_monsalgra=str_replace(",",".",$li_monsalgra);
				$li_monsalgranue=str_replace(".","",$li_monsalgranue);
				$li_monsalgranue=str_replace(",",".",$li_monsalgranue);
				$li_moncomgra=str_replace(".","",$li_moncomgra);
				$li_moncomgra=str_replace(",",".",$li_moncomgra);
				$li_moncomgranue=str_replace(".","",$li_moncomgranue);
				$li_moncomgranue=str_replace(",",".",$li_moncomgranue);
				if(($li_monsalgra!=$li_monsalgranue)||($li_moncomgra!=$li_moncomgranue))
				{
					$lb_valido=$io_ajustes->uf_update_ajustartabulador($ls_codtab,$ls_codgra,$ls_codpas,$li_monsalgranue,$li_moncomgranue,$li_porpri,$la_seguridad);
				}
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="UPDATE";
				$lb_valido= $io_ajustes->io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
												$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
												$la_seguridad["ventanas"],$io_ajustes->ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$io_ajustes->io_sql->commit();
				$io_ajustes->io_mensajes->message("Los sueldos fueron ajustados.");
			}
			else
			{
				$io_ajustes->io_sql->rollback();
				$io_ajustes->io_mensajes->message("Ocurrio un error al ajustar los sueldos.");
			}
			$li_totrows=0;
			$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			break;

		case "BUSCAR":
			$ls_codperi=$_POST["txtperactnom"];
			$ls_codtab=$_POST["txtcodtab"];
			$ls_destab=$_POST["txtdestab"];
			$li_totper=0;
			$li_totperfil=0;
			$li_totrows=0;
			$lo_object=Array();
			$arrResultado=$io_ajustes->uf_load_ajustartabulador($ls_codtab);
			$li_totper=$arrResultado['ai_totper'];
			$li_totperfil=$arrResultado['ai_totperfil'];
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['aa_object'];
			$lb_valido=$arrResultado['lb_valido'];
			if ($lb_valido===false)
			{
				$li_totrows=0;
				$lo_object=uf_agregarlineablanca($lo_object,$li_totrows);
			}
			break;
			
		case "PROCESAR":
			$ls_codperi=$_POST["txtperactnom"];
			$ls_codtab=$_POST["txtcodtab"];
			$ls_destab=$_POST["txtdestab"];
			$li_totper=$_POST["txttotper"];
			$li_totperfil=$_POST["txttotperfil"];
			$ls_tipaum=$_POST["cmbtipaum"];
			$li_poraum=$_POST["txtporaum"];
			$li_poraum=str_replace(".","",$li_poraum);
			$li_poraum=str_replace(",",".",$li_poraum);
			$li_porcom=$_POST["txtporcom"];
			$li_porcom=str_replace(".","",$li_porcom);
			$li_porcom=str_replace(",",".",$li_porcom);
                        $li_porpri=$_POST["txtporpri"];
			$lb_valido=true;
			for($li_i=1;($li_i<=$li_totrows)&&($lb_valido);$li_i++)
			{
				$ls_codgra=$_POST["txtcodgra".$li_i];
				$ls_codpas=$_POST["txtcodpas".$li_i];
				$li_monsalgra=$_POST["txtmonsalgra".$li_i];
				$li_moncomgra=$_POST["txtmoncomgra".$li_i];
				if($ls_tipaum=="P") // Por Porcentaje
				{
					if($li_poraum>0)
					{
						$li_salario=$li_monsalgra;
						$li_salario=str_replace(".","",$li_salario);
						$li_salario=str_replace(",",".",$li_salario);
						$li_salarionue=$li_salario+(($li_salario*$li_poraum)/100);
						$li_salarionue=$io_fun_nomina->uf_formatonumerico($li_salarionue);

						$li_compensacion=$li_moncomgra;
						$li_compensacion=str_replace(".","",$li_compensacion);
						$li_compensacion=str_replace(",",".",$li_compensacion);
						$li_compensacionnue=$li_compensacion+(($li_compensacion*$li_porcom)/100);
						$li_compensacionnue=$io_fun_nomina->uf_formatonumerico($li_compensacionnue);
                                                
                                        }
					else
					{
						$li_salarionue=$li_monsalgra;
                                                $li_compensacionnue=$li_moncomgra;
					}
				}
                                $lo_object[$li_i][1]="<input name=txtcodgra".$li_i." type=text id=txtcodgra".$li_i." class=sin-borde size=18 maxlength=15 value='".$ls_codgra."' readonly>";
                                $lo_object[$li_i][2]="<input name=txtcodpas".$li_i." type=text id=txtcodpas".$li_i." class=sin-borde size=18 maxlength=15 value='".$ls_codpas."' readonly>";
                                $lo_object[$li_i][3]="<input name=txtmonsalgra".$li_i." type=text id=txtmonsalgra".$li_i." class=sin-borde size=20 maxlength=23 value='".$li_monsalgra."' style=text-align:right readonly>";
                                $lo_object[$li_i][4]="<input name=txtmonsalgranue".$li_i." type=text id=txtmonsalgranue".$li_i." class=sin-borde size=20 maxlength=23 value='".$li_salarionue."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); style=text-align:right>";
                                $lo_object[$li_i][5]="<input name=txtmoncomgra".$li_i." type=text id=txtmoncomgra".$li_i." class=sin-borde size=20 maxlength=23 value='".$li_moncomgra."' style=text-align:right readonly>";
                                $lo_object[$li_i][6]="<input name=txtmoncomgranue".$li_i." type=text id=txtmoncomgranue".$li_i." class=sin-borde size=20 maxlength=23 value='".$li_compensacionnue."' onKeyPress=return(ue_formatonumero(this,'.',',',event)); style=text-align:right>";
			}
			$li_poraum=number_format($li_poraum,2,",",".");
                        $li_porcom=number_format($li_porcom,2,",",".");
                        break;
	}
	$io_ajustes->uf_destructor();
	unset($io_ajustes);
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
	  </table>
	</td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu_nomina.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title='Guardar 'alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title='Buscar' alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_procesar();"><img src="../shared/imagebank/tools20/ejecutar.gif" title="Ejecutar" alt="Imprimir" width="20" height="20" border="0"></a></div></td>
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
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank_nomina.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="710" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventana">Ajustar Tabulador </td>
        </tr>
        <tr>
          <td width="186" height="22">&nbsp;</td>
          <td colspan="3">&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Per&iacute;odo</div></td>
          <td colspan="3"><div align="left">
            <input name="txtperactnom" type="text" class="sin-borde3" id="txtperactnom" value="<?php print $ls_peractnom;?>" size="6" maxlength="3" readonly>
          Fecha Inicio 
              <input name="txtfecdesper" type="text" class="sin-borde3" id="txtfecdesper" value="<?php print  $ld_fecdesper;?>" size="13" maxlength="10" readonly>
            Fecha Fin 
                  <input name="txtfechasper" type="text" class="sin-borde3" id="txtfechasper" value="<?php print  $ld_fechasper;?>" size="13" maxlength="10" readonly>
            </div></td>
          </tr>
<?php if($li_rac=="1") { ?>		
        <tr>
          <td height="22"><div align="right">Tabulador</div></td>
          <td colspan="3"><div align="left">
            <input name="txtcodtab" type="text" id="txtcodtab" value="<?php print $ls_codtab;?>" size="18" maxlength="15" readonly>
            <a href="javascript: ue_buscartabulador();"><img id="cargo" src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
            <input name="txtdestab" type="text" class="sin-borde" id="txtdestab" value="<?php print $ls_destab;?>" size="30" maxlength="30" readonly>
          </div></td>
        </tr>
<?php }
      if(($ls_operacion=="BUSCAR")||($ls_operacion=="PROCESAR")) { ?>		
        <tr>
          <td height="22"><div align="right">Total de Personas</div></td>
          <td colspan="3"><div align="left">
            <input name="txttotper" type="text" id="txttotper" value="<?php print $li_totper;?>" size="10" maxlength="5" style="text-align:right" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Total de Grados y pasos del tabulador</div></td>
          <td colspan="3"><div align="left">
            <input name="txttotperfil" type="text" id="txttotperfil" value="<?php print $li_totperfil;?>" size="10" maxlength="5" style="text-align:right">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tipo de Aumento 
            </div></td>
          <td colspan="3"><label>
          <select name="cmbtipaum" id="cmbtipaum">
            <option value="P" selected>Por Porcentaje</option>
          </select>
          </label></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Porcentaje de Aumento Salario</div></td>
          <td>
            <div align="left">
              <input name="txtporaum" type="text" id="txtporaum" onKeyPress="javascript: return(ue_formatonumero(this,'.',',',event));" value="<?php print $li_poraum;?>" size="8" maxlength="5" style="text-align:right">
            </div>
          </td>
          <td height="22"><div align="left">Porcentaje de Aumento Compensacion
              <input name="txtporcom" type="text" id="txtporcom" onKeyPress="javascript: return(ue_formatonumero(this,'.',',',event));" value="<?php print $li_porcom;?>" size="8" maxlength="5" style="text-align:right">
            </div>
          </td>
          <td><div align="left">Porcentaje de Aumento Primas
              <input name="txtporpri" type="text" id="txtporpri" onKeyPress="javascript: return(ue_formatonumero(this,'.',',',event));" value="<?php print $li_porpri;?>" size="8" maxlength="5" style="text-align:right">
            </div>
          </tr>
<?php } ?>		
        <tr>
          <td height="22">&nbsp;</td>
          <td colspan="3"><input name="operacion" type="hidden" id="operacion" value="<?php print $ls_operacion;?>">
            <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
            <input name="rac" type="hidden" id="rac" value="<?php print $li_rac;?>"></td>
        </tr>
        <tr>
          <td colspan="4"><div align="center">
<?php
        $io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
        unset($io_grid);
?>
          </div>
            <p>
              <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
			  <input name="calculada" type="hidden" id="calculada" value="<?php print $li_calculada;?>">
            </p></td>
          </tr>
      </table>    
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>      
<p>&nbsp;</p>
</body>
<script >

function ue_guardar()
{
	f=document.form1;
	li_calculada=f.calculada.value;
	if(li_calculada=="0")
	{		
		li_cambiar=f.cambiar.value;
		if(li_cambiar==1)
		{
			if(f.rac.value=="1")
			{
				if((f.operacion.value=="BUSCAR")||(f.operacion.value=="PROCESAR"))
				{
					totperfil = ue_validarvacio(f.txttotperfil.value);
					peractnom = ue_validarvacio(f.txtperactnom.value);
					totrow = ue_validarvacio(f.totalfilas.value);
					if ((totperfil!="")&&(totperfil!="0")&&(peractnom!="")&&(totrow!="")&&(totrow!="0"))
					{
						f.operacion.value="GUARDAR";
						f.action="sigesp_sno_p_ajustartabulador.php";
						f.submit();
					}
					else
					{
						alert("Debe llenar todos los datos.");
					}
				}
				else
				{
					alert("Primero debe consultar la información");
				}
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

function ue_cerrar()
{
	location.href = "sigespwindow_blank_nomina.php";
}

function ue_buscar()
{
	f=document.form1;
	li_leer=f.leer.value;
	if (li_leer==1)
   	{
		if(f.rac.value=="1")
		{
                    f.operacion.value="BUSCAR";
                    f.action="sigesp_sno_p_ajustartabulador.php";
                    f.submit();
		}
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
		if(f.rac.value=="1")
		{
			if((f.operacion.value=="BUSCAR")||(f.operacion.value=="PROCESAR"))
			{
				totperfil = ue_validarvacio(f.txttotperfil.value);
				if((totperfil!="")&&(totperfil!="0"))
				{
					f.operacion.value="PROCESAR";
					f.action="sigesp_sno_p_ajustartabulador.php";
					f.submit();
				}
				else
				{
					alert("no hay personal que procesar");
				}
			}
			else
			{
				alert("Primero debe consultar la información");
			}
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}		
}

function ue_buscartabulador()
{
	window.open("sigesp_sno_cat_tabulador.php?tipo=ajustartabulador","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}
</script> 
</html>