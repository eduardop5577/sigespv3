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
	require_once("../../class_folder/dao/sigesp_srh_c_escalageneral.php");	
	require_once("../../class_folder/utilidades/class_funciones_srh.php");
	require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/Json.php");	
	$io_json = new Services_JSON();	

	$io_escalageneral= new sigesp_srh_c_escalageneral('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_escalageneral.php",$ls_permisos,$la_seguridad,$la_permisos);
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
			$ls_codesc="%%";
			$ls_denesc="%%";
			
		    header('Content-type:text/xml');
			print  $io_escalageneral->uf_srh_buscar_escalageneral($ls_codesc,$ls_denesc);
		}
		
		elseif($evento=="buscar")
		{
			$ls_codesc="%".utf8_encode($_REQUEST['txtcodesc'])."%";
			$ls_denesc="%".utf8_encode($_REQUEST['txtdenesc'])."%";
				
			header('Content-type:text/xml');
			print $io_escalageneral->uf_srh_buscar_escalageneral($ls_codesc,ls_denesc);
		}
			
	
}

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
  $io_esc = $io_json->decode(utf8_decode ($objeto));
  $valido= $io_escalageneral-> uf_srh_guardar_escalageneral ($io_esc,$_POST["insmod"], $la_seguridad);
   if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La Escala General fue Actualizada';	}
	else { $ls_salida = 'La Escala General fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar la Escala General';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  list($valido,$existe)= $io_escalageneral->uf_srh_eliminar_escalageneral($_POST["codesc"], $la_seguridad);
  if ($existe)
  {$ls_salida = 'La Escala de Evaluacion no puede ser eliminada porque esta asociada a un Tipo de Evaluacion';}
  else 
  {
	  if ($valido)
	  {$ls_salida = 'La Escala de Evaluacion fue Eliminada';}
	  else 
	  {$ls_salida = 'Ocurrio un error al eliminar la escala de Evaluacion';}
  }
}
elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_escalageneral->uf_srh_getProximoCodigo();  

}

  echo utf8_encode($ls_salida);


?>
