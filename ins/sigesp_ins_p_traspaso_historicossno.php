<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
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
	print "	window.close();";
	print "</script>";		
}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_ins.php");
	$oi_fun_instala=new class_funciones_ins("../");
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_traspaso_historicossno.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];	
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
require_once("../shared/class_folder/grid_param.php");
$io_grid=new grid_param();
require_once("class_folder/sigesp_ins_c_traspasohistoricosno.php");
$io_integracion=new sigesp_ins_c_traspasohistoricosno();

$ls_gestor_int=$io_integracion->uf_select_config("INS","INTEGRACION-HISTORICOSSNO","GESTOR_INT","POSTGRES","C");
$ls_puerto_int=$io_integracion->uf_select_config("INS","INTEGRACION-HISTORICOSSNO","PUERTO_INT","5432","C");
$ls_servidor_int=$io_integracion->uf_select_config("INS","INTEGRACION-HISTORICOSSNO","SERVIDOR_INT","127.0.0.1","C");
$ls_basedatos_int=$io_integracion->uf_select_config("INS","INTEGRACION-HISTORICOSSNO","BASE_DATOS_INT","db_sigefirrhh","C");
$ls_login_int=$io_integracion->uf_select_config("INS","INTEGRACION-HISTORICOSSNO","LOGIN_INT","xxxxxxx","C");
$ls_password_int=trim($io_integracion->uf_select_config("INS","INTEGRACION-HISTORICOSSNO","PASSWORD_INT","xxxxxx","C"));
?>  
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Traspaso de Hist&oacute;ricos de N&oacute;mina</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style type="text/css">

</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php 
	$ls_titletable="Información de Históricos";
	$li_widthtable=500;
	$ls_nametable="grid";
	$lo_title[1]="";
	$lo_title[2]="Nómina";
	$lo_title[3]="Descripcion";
	$lo_title[4]="Periodo";
	$lo_title[5]="Fecha Desde";
	$lo_title[6]="Fecha Hasta";
	$li_totrows=$oi_fun_instala->uf_obtenervalor("totalfilas",1);
	$ls_operacion=$oi_fun_instala->uf_obteneroperacion();
	switch ($ls_operacion) 
	{
		case "BUSCAR":
			$ls_gestor_int=$_POST["txtgestor"];
			$ls_puerto_int=$_POST["txtpuerto"];
			$ls_servidor_int=$_POST["txtservidor"];
			$ls_basedatos_int=$_POST["txtbasedatos"];
			$ls_login_int=$_POST["txtlogin"];
			$ls_password_int=trim($_POST["txtpassword"]);
			$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","GESTOR_INT",$ls_gestor_int,"C");
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","PUERTO_INT",$ls_puerto_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","SERVIDOR_INT",$ls_servidor_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","BASE_DATOS_INT",$ls_basedatos_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","LOGIN_INT",$ls_login_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","PASSWORD_INT",$ls_password_int,"C");
			}
			if ($lb_valido)
			{
				$arrResultado=$io_integracion->uf_load_datos($ls_gestor_int,$ls_puerto_int,$ls_servidor_int,$ls_basedatos_int,$ls_login_int,
														  $ls_password_int,$li_totrows,$lo_object);
				$li_totrows = $arrResultado['ai_totrows'];
				$lo_object = $arrResultado['ao_object'];
				$lb_valido = $arrResultado['lb_valido'];
			}
			break;

		case "GUARDAR":
			$ls_gestor_int=$_POST["txtgestor"];
			$ls_puerto_int=$_POST["txtpuerto"];
			$ls_servidor_int=$_POST["txtservidor"];
			$ls_basedatos_int=$_POST["txtbasedatos"];
			$ls_login_int=$_POST["txtlogin"];
			$ls_password_int=trim($_POST["txtpassword"]);
			$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","GESTOR_INT",$ls_gestor_int,"C");
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","PUERTO_INT",$ls_puerto_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","SERVIDOR_INT",$ls_servidor_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","BASE_DATOS_INT",$ls_basedatos_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","LOGIN_INT",$ls_login_int,"C");
			}
			if ($lb_valido)
			{
				$lb_valido=$io_integracion->uf_insert_config("INS","INTEGRACION-HISTORICOSSNO","PASSWORD_INT",$ls_password_int,"C");
			}
			$io_integracion->io_sql->begin_transaction();
			if($lb_valido)
			{
				for($li_i=1;(($li_i<=$li_totrows)&&$lb_valido);$li_i++)
				{
					
					if(array_key_exists("chksel".$li_i,$_POST))
					{
						$ls_codnom=$_POST["txtcodnom".$li_i];
						$ls_codperi=$_POST["txtcodperi".$li_i];
						$ls_fecdes=$_POST["txtfecdes".$li_i];
						$ls_fechas=$_POST["txtfechas".$li_i];
						$lb_valido=$io_integracion->uf_procesar_historicos_nomina($ls_gestor_int,$ls_puerto_int,$ls_servidor_int,$ls_basedatos_int,$ls_login_int,
														 				          $ls_password_int,$ls_codnom,$ls_codperi,$ls_fecdes,$ls_fechas,$la_seguridad);
					}
				}
			}
			if($lb_valido)
			{
				$io_integracion->io_sql->commit();
				$io_integracion->io_mensajes->message("La Integración fué realizada con exito.");
			}
			else
			{
				$io_integracion->io_sql->rollback();
				$io_integracion->io_mensajes->message("Ocurrio un error al realizar la integración.");
			}
			$arrResultado=$io_integracion->uf_load_datos($ls_gestor_int,$ls_puerto_int,$ls_servidor_int,$ls_basedatos_int,$ls_login_int,
													  $ls_password_int,$li_totrows,$lo_object);
			$li_totrows = $arrResultado['ai_totrows'];
			$lo_object = $arrResultado['ao_object'];
			$lb_valido = $arrResultado['lb_valido'];
		break;
	}
	unset($io_integracion);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Instala</td>
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
    <td height="20" width="25" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" title="Nuevo" alt="Nuevo" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" title="Buscar" alt="Buscar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif" title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>
<p>
</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$oi_fun_instala->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($oi_fun_instala);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<label></label>
  <table width="200" border="0" align="center">
    <tr>
      <td><div align="center">
        <table width="570" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
         <td height="22" colspan="3" class="titulo-celdanew">Traspaso de Hist&oacute;ricos de N&oacute;mina</td>
      </tr>
      <tr class="formato-blanco">
         <td height="13" colspan="3">&nbsp;</td>
      </tr>
          <tr>
            <td height="22" colspan="3" class="titulo-celdanew"><div align="right"></div>
                <div align="center">Informaci&oacute;n de Conexion base de Datos origen </div></td>
          </tr>
          <tr>
            <td width="143" height="21"><div align="right">Gestor</div></td>
            <td height="21" colspan="2"><div align="left">
              <input name="txtgestor" type="text" id="txtgestor" value="<?php print $ls_gestor_int;?>">
            </div></td>
			</tr>
          <tr>
            <td height="21"><div align="right">Puerto</div></td>
            <td height="21" colspan="2"><input name="txtpuerto" type="text" id="txtpuerto" value="<?php print $ls_puerto_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Servidor</div></td>
            <td height="21" colspan="2"><input name="txtservidor" type="text" id="txtservidor" value="<?php print $ls_servidor_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Base de Datos Origen </div></td>
            <td height="21" colspan="2"><label>
              <input name="txtbasedatos" type="text" id="txtbasedatos" value="<?php print $ls_basedatos_int;?>">
            </label></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Login</div></td>
            <td height="21" colspan="2"><input name="txtlogin" type="text" id="txtlogin" value="<?php print $ls_login_int;?>"></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Password</div></td>
            <td height="21" colspan="2"><label>
              <input name="txtpassword" type="text" id="txtpassword" value="<?php print $ls_password_int;?>">
            </label></td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td height="21" colspan="3" class="titulo-celdanew">&nbsp;</td>
            </tr>
          <tr>
            <td height="21" colspan="3">&nbsp;</td>
          </tr>
          <tr>
            <td height="21" colspan="3">
		  	<div align="center">
			<?php
					$io_grid->makegrid($li_totrows,$lo_title,$lo_object,$li_widthtable,$ls_titletable,$ls_nametable);
					unset($io_grid);
			?>
			  </div>
		  	<p>
              <input name="totalfilas" type="hidden" id="totalfilas" value="<?php print $li_totrows;?>">
			</td>
            </tr>
          <tr>
            <td >&nbsp;</td>
            <td width="256" colspan="-1">
              <p>&nbsp;</p>            </td>
            <td width="122">&nbsp;</td>
          </tr>
        </table>
      </div></td>
    </tr>
  </table>
  <input name="operacion" type="hidden" id="operacion" value="<?php $_REQUEST["OPERACION"] ?>">
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function ue_nuevo()
{
   location.href='sigesp_ins_p_traspaso_historicossno.php' 
}

function ue_buscar()
{	
	f=document.form1;
	f.operacion.value="BUSCAR";	
	f.action="sigesp_ins_p_traspaso_historicossno.php";
	f.submit();
}

function ue_guardar()
{
	f=document.form1;
	li_incluir=f.incluir.value;
	li_cambiar=f.cambiar.value;
	if((li_cambiar==1)||(li_incluir==1))
	{
		f.operacion.value="GUARDAR";	
		f.action="sigesp_ins_p_traspaso_historicossno.php";
		f.submit();
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion.")
	}	
}

function ue_cerrar()
{
   location.href='sigespwindow_blank.php' 
}
</script>
</html>
