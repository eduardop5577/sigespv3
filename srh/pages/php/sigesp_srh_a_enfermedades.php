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
	require_once("../../class_folder/dao/sigesp_srh_c_enfermedades.php");
	require_once("../../class_folder/utilidades/class_funciones_srh.php");

	$io_enfermedades= new sigesp_srh_c_enfermedades('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_p_enfermedades.php",$ls_permisos,$la_seguridad,$la_permisos);
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
			$ls_nroreg="%%";
			$ls_codper="%%";
			$ls_apeper="%%";
			$ls_nomper="%%";
			
			
		    header('Content-type:text/xml');			
			print $io_enfermedades->uf_srh_buscar_enfermedades($ls_nroreg,$ls_codper,$ls_apeper,$ls_nomper);
		}
		
		elseif($evento=="buscar")
		{
			$ls_nroreg="%".utf8_encode($_REQUEST['txtnroreg'])."%";
			$ls_codper="%".utf8_encode($_REQUEST['txtcodper'])."%";
			$ls_apeper="%".utf8_encode($_REQUEST['txtapeper'])."%";
			$ls_nomper="%".utf8_encode($_REQUEST['txtnomper'])."%";
			
				
			header('Content-type:text/xml');			
			print $io_enfermedades->uf_srh_buscar_enfermedades($ls_nroreg,$ls_codper,$ls_apeper,$ls_nomper);
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
  $io_enf = $io_json->decode(utf8_decode ($objeto));
  $valido= $io_enfermedades-> uf_srh_guardarEnfermedad ($io_enf,$_POST["insmod"], $la_seguridad);
  if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La enfermedad fue Actualizada';	}
	else { $ls_salida = 'La enfermedad fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar la enfermedad';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  $io_enfermedades->uf_srh_eliminarEnfermedad($_GET["nroreg"], $la_seguridad);
  $ls_salida = 'La Enfermedad fue Eliminada';
}

elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_enfermedades->uf_srh_getProximoCodigo();  

}

  echo utf8_encode($ls_salida);


?>
