<?php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	session_start();
	require_once("../../class_folder/dao/sigesp_srh_c_tipodeduccion.php");
    require_once("../../class_folder/utilidades/class_funciones_srh.php");
	$io_deduccion=new sigesp_srh_c_tipodeduccion('../../../');
	$io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_configuracion_deduccion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	$ls_logusr=$_SESSION["la_logusr"];
	$ls_salida = "";

if (isset($_GET['valor']))
{
	    $evento=$_GET['valor'];

		if($evento=="buscar")
			{       
			        $ls_codtipded="%".utf8_encode($_REQUEST['txtcodtipded'])."%";
	                $ls_dentipded="%".utf8_encode($_REQUEST['txtdentipded'])."%";
					
					header('Content-type:text/xml');
					print $io_deduccion->uf_srh_buscar_configuracion_deduccion($ls_codtipded, $ls_dentipded);
					
			}
			elseif($evento=="createXML")
			{

    				$ls_codtipded="%%";
	                $ls_dentipded="%%";

					header('Content-type:text/xml');
					print $io_deduccion->uf_srh_buscar_configuracion_deduccion($ls_codtipded, $ls_dentipded);
					
					
			}
			
	
}



require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/Json.php");	
$io_json = new Services_JSON();	


if (array_key_exists("operacion",$_GET))
{
  $ls_operacion = $_GET["operacion"];
}
else
{
	if (array_key_exists("operacion",$_POST))
	{
	  $ls_operacion = $_POST["operacion"];
	}
	else
	{
	
	  $ls_operacion = "";
	}
}

if ($ls_operacion == "ue_guardar")
{  
  $objeto = str_replace('\"','"',$_POST["objeto"]);
  $io_req = $io_json->decode(utf8_decode ($objeto));
  $valido = $io_deduccion-> uf_srh_guardar_configuracion_deduccion ($io_req,$la_seguridad);
  if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La Configuracion del Tipo de Deduccion fue Actualizada';	}
	else { $ls_salida = 'La Configuracion del Tipo de Deduccion fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar La Configuracion del Tipo de Deduccion';}
 
}
elseif ($ls_operacion == "calcular_monto_deduccionnew")
{  
    $ls_salida =$io_deduccion->uf_srh_calcular_monto_deduccionnew ($_GET["codper"], $_GET["codtipded"], $_GET["sexper"]);
}
elseif ($ls_operacion == "calcular_monto_deduccionnew_cod")
{
	$ls_salida =$io_deduccion->uf_srh_calcular_monto_deduccionnew_cod ($_GET["codper"], $_GET["codtipded"], $_GET["sexper"]);
}
elseif ($ls_operacion == "calcular_monto_deduccionnewfam")
{  
    $ls_salida =$io_deduccion->uf_srh_calcular_monto_deduccionnewfam ($_GET["codper"], $_GET["codtipded"], $_GET["sexper"], $_GET["cedfam"]);
}
elseif ($ls_operacion == "calcular_monto_deduccionnew_codfam")
{
	$ls_salida =$io_deduccion->uf_srh_calcular_monto_deduccionnew_codfam ($_GET["codper"], $_GET["codtipded"], $_GET["sexper"], $_GET["cedfam"]);
}
elseif ($ls_operacion == "ue_eliminar")
{  
  $io_deduccion->uf_srh_eliminar_dt_configuracion_deduccion($_GET["codtipded"], $la_seguridad);
  $ls_salida = 'La Configuracion del Tipo de Deduccion  fue Eliminada';
}


  echo utf8_encode($ls_salida);


?>

