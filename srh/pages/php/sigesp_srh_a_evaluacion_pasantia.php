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
	require_once("../../class_folder/dao/sigesp_srh_c_evaluacion_pasantia.php");
	require_once("../../class_folder/utilidades/class_funciones_srh.php");
	
	$io_evaluacion= new sigesp_srh_c_evaluacion_pasantia('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_p_evaluacion_pasantias.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	$ls_logusr=$_SESSION["la_logusr"];
    $ls_salida = "";

if (isset($_GET['valor']))
{
	    $evento=$_GET['valor'];

		if($evento=="createXML")
		{
			$ls_nropas="%%";
			$ls_cedpas="%%";
			$ls_apepas="%%";
			$ls_nompas="%%";
			$ls_feceval1=$_REQUEST['txtfecevaldes'];
			$ls_feceval2=$_REQUEST['txtfecevalhas'];
			
			header('Content-type:text/xml');
			print ($io_evaluacion->uf_srh_buscar_evaluacion_pasantia($ls_nropas,$ls_cedpas,$ls_apepas,$ls_nompas,$ls_feceval1,$ls_feceval2));
			
		}
		
		elseif($evento=="buscar")
		{
			$ls_nropas="%".utf8_encode($_REQUEST['txtnropas'])."%";
			$ls_cedpas="%".utf8_encode($_REQUEST['txtcedpas'])."%";
			$ls_apepas="%".utf8_encode($_REQUEST['txtapepas'])."%";
			$ls_nompas="%".utf8_encode($_REQUEST['txtnompas'])."%";
			$ls_feceval1=$_REQUEST['txtfecevaldes'];
			$ls_feceval2=$_REQUEST['txtfecevalhas'];
			
			header('Content-type:text/xml');			
			print ($io_evaluacion->uf_srh_buscar_evaluacion_pasantia($ls_nropas,$ls_cedpas,$ls_apepas,$ls_nompas,$ls_feceval1,$ls_feceval2));
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
	$ls_operacion ="";
}

if ($ls_operacion == "ue_guardar")
{  
  $objeto = str_replace('\"','"',$_POST["objeto"]);
  $io_pas = $io_json->decode(utf8_decode ($objeto));
  $valido = $io_evaluacion-> uf_srh_guardarEvaluacion_Pasantia($io_pas,$_POST["insmod"], $la_seguridad);
   if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La Evaluaci?n de Pasantia fue Actualizada';	}
	else { $ls_salida = 'La Evaluaci?n de Pasantia fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar la Evaluaci?n de Pasantia';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  $io_evaluacion->uf_srh_eliminarEvaluacion_Pasantia($_GET["nropas"], $_GET["feceval"], $la_seguridad);
  $ls_salida = 'La Evaluacion de Pasantia fue Eliminada';
}


  echo utf8_encode($ls_salida);


?>
