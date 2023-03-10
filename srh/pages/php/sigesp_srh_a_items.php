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
	require_once("../../class_folder/dao/sigesp_srh_c_items.php");
    require_once("../../class_folder/utilidades/class_funciones_srh.php");
	$io_item=new sigesp_srh_c_items('../../../');
	$io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_items.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];
	$ls_logusr=utf8_encode($_SESSION["la_logusr"]);

	 $ls_salida="";

	
	if (isset($_GET['valor']))
	{
	
	   $evento=$_GET['valor'];
	   
		if($evento=="buscar")
			{
					$ls_codeval="%".utf8_encode($_REQUEST['txtcodeval'])."%";	               
					$ls_codasp="%".utf8_encode($_REQUEST['txtcodasp'])."%";	              
				   
					header('Content-type:text/xml');
					print $io_item->uf_srh_buscar_items($ls_codeval, $ls_codasp);
					
					
					
					
			}
			elseif($evento=="createXML")
			{
                       
				    $ls_codeval="%%";	         
					$ls_codasp="%%";	           
				    
					header('Content-type:text/xml');
					print $io_item->uf_srh_buscar_items($ls_codeval, $ls_codasp);
					
					
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
  $io_ite = $io_json->decode(utf8_decode ($objeto));
  list($valido,$guardo)= $io_item-> uf_srh_guardar_items ($io_ite, $_POST["insmod"], $la_seguridad);
   if (($valido) && ($_POST["insmod"]!='modificar')) 
     {
     $ls_salida = 'El item de Evaluacion ya esta registrado, no se puede agregar. Si desea modificarlo seleccione el registro del catalogo.';	
	}
	else if ((!$valido) &&($guardo))
	{ $ls_salida = 'El Item de Evaluacion fue Registrado';}
	else if (($valido) && ($_POST["insmod"]=='modificar')&&($guardo))
	{ $ls_salida = 'El Item de Evaluacion fue Actualizado';}
    else {$ls_salida = 'Error al guardar el Item de Evaluacion';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  list($valido,$existe)= $io_item->uf_srh_delete_items($_GET["codite"],$_GET["codasp"],$_GET["codeval"],$la_seguridad);
  if ($existe)
  {$ls_salida = 'El Item de Evaluacion no puede ser eliminado porque esta asociados a una Evaluacion';}
  else 
  {
	  if ($valido)
	  {$ls_salida = 'El Item de Evaluacion fue Eliminado';}
	  else 
	  {$ls_salida = 'Ocurrio un error al eliminar el Item de Evaluacion';}
  }
}
elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_item->uf_srh_getProximoCodigo($_GET["codeval"],$_GET["codasp"],$_GET["coditeaux"]);  

}

  echo utf8_encode($ls_salida);


?>

