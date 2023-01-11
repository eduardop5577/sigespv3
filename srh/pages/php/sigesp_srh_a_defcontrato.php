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
	require_once("../../class_folder/dao/sigesp_srh_c_defcontrato.php");	
	require_once("../../class_folder/utilidades/class_funciones_srh.php");

	$io_defcontrato= new sigesp_srh_c_defcontrato('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_defcontrato.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	$ls_logusr=utf8_encode($_SESSION["la_logusr"]);
    $ls_salida = "";

if (isset($_GET['valor']))
{
	    $evento=$_GET['valor'];

		if($evento=="createXML")
		{
			$ls_codcont="%%";
			$ls_descont="%%";			
			
		    header('Content-type:text/xml');			
			print $io_defcontrato->uf_srh_buscar_defcontrato($ls_codcont,$ls_descont);
		}
		
		elseif($evento=="buscar")
		{
			$ls_codcont="%".utf8_encode($_REQUEST['txtcodcont'])."%";
			$ls_descont="%".utf8_encode($_REQUEST['txtdescont'])."%";		
				
			header('Content-type:text/xml');			
			print $io_defcontrato->uf_srh_buscar_defcontrato($ls_codcont,$ls_descont);
		}
			
	
}


require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/Json.php");	
$io_json = new Services_JSON();	


if (array_key_exists("operacion",$_GET))
{
  $ls_operacion = $_GET["operacion"];
}
elseif (array_key_exists("operacion",$_POST))
{
  $ls_operacion = $_POST["operacion"];
}
else
{
  $ls_operacion = "";
}

if ($ls_operacion == "ue_guardar")
{  
  $objeto = str_replace('\"','"',$_POST["objeto"]);
  $io_cont = $io_json->decode(utf8_decode ($objeto));
  $valido= $io_defcontrato-> uf_srh_guardar_defcontrato ($io_cont,$_POST["insmod"], $la_seguridad);
  if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La Configuraci�n de Contrato fue Actualizada';	}
	else { $ls_salida = 'La Configuraci�n de Contrato fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar la Configuraci�n de Contrato';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  $io_defcontrato->uf_srh_eliminar_defcontrato($_GET["codcont"], $la_seguridad);
  $ls_salida = 'La Configuraci�n de Contrato fue Eliminada';
}

elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_defcontrato->uf_srh_getProximoCodigo();  

}

  echo utf8_encode($ls_salida);


?>
