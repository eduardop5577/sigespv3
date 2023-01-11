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
	require_once("../../class_folder/dao/sigesp_srh_c_entrevista_tecnica.php");	
	require_once("../../class_folder/utilidades/class_funciones_srh.php");

	$io_entrevista_tecnica= new sigesp_srh_c_entrevista_tecnica('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_p_entrevista_tecnica.php",$ls_permisos,$la_seguridad,$la_permisos);
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
			$ls_codper="%%";
			$ls_fecha1=$_REQUEST['txtfechades'];
			$ls_fecha2=$_REQUEST['txtfechahas'];
			
		    header('Content-type:text/xml');			
			print $io_entrevista_tecnica->uf_srh_buscar_entrevista_tecnica($ls_codper,$ls_fecha1,$ls_fecha2);
		}
		
		elseif($evento=="buscar")
		{
			$ls_codper="%".utf8_encode($_REQUEST['txtcodper'])."%";
			$ls_fecha1=$_REQUEST['txtfechades'];
			$ls_fecha2=$_REQUEST['txtfechahas'];
			header('Content-type:text/xml');			
			print $io_entrevista_tecnica->uf_srh_buscar_entrevista_tecnica($ls_codper,$ls_fecha1,$ls_fecha2);
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
	$ls_operacion="";
}

if($ls_operacion == "ue_chequear_codigo")
{
 	 list($lb_existe,$ls_codcon) = $io_entrevista_tecnica->getCodPersonal($_GET["codper"],$_GET["codcon"]);
	  if ($lb_existe)
	  {
	 
	   $ls_salida  ='El código del personal '.$_GET["codper"].' ya fue evaluado en el concurso  '.$ls_codcon;
	  }

}
elseif ($ls_operacion == "ue_guardar")
{  
  $objeto = str_replace('\"','"',$_POST["objeto"]);
  $io_req = $io_json->decode(utf8_decode ($objeto));
  $valido= $io_entrevista_tecnica-> uf_srh_guardarentrevista_tecnica ($io_req,$_POST["insmod"], $la_seguridad);
   if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La Entrevista Técnica fue Actualizada';	}
	else { $ls_salida = 'La Entrevista Técnica fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar la Entrevista Técnica';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  $io_entrevista_tecnica->uf_srh_eliminarentrevista_tecnica($_GET["codper"],$_GET["fecha"], $_GET["codcon"],$la_seguridad);
  $ls_salida = 'La Entrevista Técnica fue Eliminada';
}
  echo utf8_encode($ls_salida);
?>