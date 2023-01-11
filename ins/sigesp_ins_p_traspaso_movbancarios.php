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
	$arrResultado = $oi_fun_instala->uf_load_seguridad("INS","sigesp_ins_p_traspaso_movbancarios.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];	
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
require_once("sigesp_ins_config_traspaso.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
//require_once("../cfg/class_folder/sigesp_cfg_c_empresa.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_mensajes.php");
$io_conect = new sigesp_include();
$msg=new class_mensajes();
if(array_key_exists("dbdestino",$_POST))
{
  $ls_dbdestino=$_POST["dbdestino"];
}
else
{
  $ls_dbdestino="";
}		

$_SESSION["ls_data_des"] = $ls_dbdestino;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Traspaso de Movimientos Bancarios</title>
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
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css"></head>
<script type="text/javascript"  src="js/stm31.js"></script>
<body>

  <table width="800" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
    <tr>
      <td width="800" height="30" class="cd-logo"><img src="../shared/imagebank/header.jpg" alt="Encabezado" width="800" height="40" /></td>
    </tr>
    <tr>
      <td height="20" colspan="12" bgcolor="#E7E7E7"><table width="800" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td width="430" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Instala</td>
            <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
          </tr>
        <tr>
            <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
          <td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
        </tr>
      </table></td>
    </tr>
    <tr>
      <td height="20" bgcolor="#E7E7E7" class="cd-menu" style="text-align:left"><script type="text/javascript"  src="js/menu.js"></script></td>
    </tr>
    <tr>
      <td height="13" colspan="11" class="toolbar"></td>
    </tr>
    <tr style="text-align:left">
    <td height="20" bgcolor="#FFFFFF" class="toolbar"><a href="javascript:ue_nuevo();"></a><a href="javascript:ue_salir();"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0"></a><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20"></td>
    </tr>
  </table>
  <p>&nbsp;</p>
<p>
  <?php
	function uf_conectar_destino() 
	{
		global $msg;	
		if ((strtoupper($_SESSION["ls_gestor_destino"])==strtoupper("MYSQLT")) || (strtoupper($_SESSION["ls_gestor_destino"])==strtoupper("MYSQLI")))
		{
		    $conec = @mysql_connect($_SESSION["ls_hostname_destino"],$_SESSION["ls_login_destino"],$_SESSION["ls_password_destino"]);						
			if($conec===false)
			{
				$msg->message("No pudo conectar con el servidor de datos MYSQL,".$_SESSION["ls_hostname_destino"]." , contacte al administrador del sistema");	
			}
			else
			{			    
				$lb_ok=@mysql_select_db(trim($_SESSION["ls_database_destino"]),$conec);
				if (!$lb_ok)
				{
					$msg->message("No existe la base de datos ".$_SESSION["ls_database_destino"]);					
				}
			}
		return $conec;
		}		
		if(strtoupper($_SESSION["ls_gestor_destino"])==strtoupper("POSTGRES"))
		{
			$conec = @pg_connect("host=".$_SESSION["ls_hostname_destino"]." port=".$_SESSION["ls_port_destino"]."  dbname=".$_SESSION["ls_database_destino"]." user=".$_SESSION["ls_login_destino"]." password=".$_SESSION["ls_password_destino"]); 
			if (!$conec)
			{
				$msg->message("No pudo conectar al servidor de base de datos POSTGRES, contacte al administrador del sistema");				
			}
      	 return $conec;
	    }		
	}		
	if(array_key_exists("operacion",$_POST))
	{
		$lb_connect=$_POST["hidconnect"];
		$ls_operacion=$_POST["operacion"];
		if ($ls_operacion=="MOSTRAR")
		{
			$posicion=$_POST["cmbdb"];
			//Realizo la conexion a la base de datos
			if($posicion=="")
			{}
			else
			  {
				$_SESSION["ls_database_destino"] = $empresa["database"][$posicion];							
				$_SESSION["ls_hostname_destino"] = $empresa["hostname"][$posicion];
				$_SESSION["ls_login_destino"]    = $empresa["login"][$posicion];
				$_SESSION["ls_password_destino"] = $empresa["password"][$posicion];
				$_SESSION["ls_gestor_destino"]   = $empresa["gestor"][$posicion];	
				$_SESSION["ls_port_destino"]     = $empresa["port"][$posicion];	
				$_SESSION["ls_width_destino"]    = $empresa["width"][$posicion];
				$_SESSION["ls_height_destino"]   = $empresa["height"][$posicion];	
				$_SESSION["ls_logo_destino"]     = $empresa["logo"][$posicion];
				if ($_SESSION["ls_database_destino"]!='')
				{
					$lb_valido=uf_conectar_destino();
					if ($lb_valido)
					{
						$lb_connect=1;
					}
					else
					{
						$lb_connect=0;
					}
				}
			}
			/*print "<script language=JavaScript>";
			print "location.href='sigesp_ins_p_traspaso_conceptos_aportes.php'" ;
			print "</script>";*/
		}
	}
	else
	{ 
		$ls_operacion="";		
		$lb_connect=0;
		/*if(!isset($_SESSION))
		{
			unset($_SESSION);
		}*/
	}	
?>
</p>
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$oi_fun_instala->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($oi_fun_instala);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
<form name="form1" method="post" action="">
  <table width="200" border="0" align="center">
    <tr>
      <td><div align="center">
        <table width="570" border="0" cellpadding="1" cellspacing="0" class="formato-blanco" align="center">
      <tr>
         <td height="22" colspan="3" class="titulo-celdanew">Traspaso de Movimientos Bancarios </td>
      </tr>
      <tr class="formato-blanco">
         <td height="13" colspan="3">&nbsp;</td>
      </tr>
      <?php
      //if($ls_operacion=="")
      //{
	  ?>                    
          <tr>
            <td height="22" colspan="3" class="titulo-celdanew"><div align="right"></div>
                <div align="center">Base de Datos </div></td>
          </tr>
          <tr>
            <td width="143" height="21"><input name="operacion" type="hidden" id="operacion" value="<?php $_REQUEST["OPERACION"] ?>"></td>
			<td width="256" height="21"><input name="hidconnect" type="hidden" id="hidconnect" value="<?php print $lb_connect; ?>"></td>
            <td width="122" height="21" colspan="-1">&nbsp;</td>
            <td width="39" height="21" colspan="-1">&nbsp;</td>
          </tr>
          <tr>
            <td ><div align="right">
                  <p><strong>Base de Datos Destino</strong></p>
                  </div></td>
            <td colspan="-1">
              <p>
                <?php
   	$li_total = count((array)$empresa["database"]);
    ?>
		<select name="cmbdb" style="width:120px " onChange="javascript:selec();">
		<option value="">Seleccione</option>
        <?php
			for($i=1; $i <= $li_total ; $i++)
			{
				if($posicion==$i)
				{
					$selected="selected";
				}
				else
				{
					$selected="";
				}
		?>
				<option value="<?php echo $i;?>" <?php print $selected; ?>>
					<?php
						echo $empresa["database"][$i];				
					?>
				</option>
        <?php
		}
		?>
        </select>
		<input name="dbdestino" type="hidden" id="dbdestino" value="<?php print $ls_dbdestino;?>">
              <input name="botejecutar" style="height:15" type="button" class="boton" id="botejecutar" value="Aceptar" onClick="javascript:uf_ejecutar();">
              </p>            </td>
            <td colspan="-1">&nbsp;</td>
          </tr>
          <?php
		  //}		 
		  ?>
		<tr>
            <td height="27">&nbsp;</td>
			 <td height="27">&nbsp;</td>
        </tr> 
        </table>
      </div></td>
    </tr>
  </table>
  </form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
<script >
function selec()
{	
	f=document.form1;
	f.operacion.value="MOSTRAR";	
	f.action="sigesp_ins_p_traspaso_movbancarios.php";
	f.submit();
}

function  uf_ejecutar()
{
	f=document.form1;
	if (f.hidconnect.value==1)
	{
		f.action="sigesp_ins_p_movbancarios_trans.php";
		f.submit();
	}
	else
	{
		alert("No se realizó la conexión con exito, chequee su archivo de configuración")
	}	
}

function ue_salir()
{
   location.href='sigespwindow_blank.php' 
}
</script>
</html>
