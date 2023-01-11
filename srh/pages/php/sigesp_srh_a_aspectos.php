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
	require_once("../../class_folder/dao/sigesp_srh_c_aspectos.php");	
	require_once("../../class_folder/utilidades/class_funciones_srh.php");

	$io_aspectos= new sigesp_srh_c_aspectos('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_aspectos.php",$ls_permisos,$la_seguridad,$la_permisos);
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
			$ls_codeval="%%";
			$ls_deneval="%%";
			
		    header('Content-type:text/xml');
			print  $io_aspectos->uf_srh_buscar_aspectos_cat($ls_codeval,$ls_deneval);
		}
		
		elseif($evento=="buscar")
		{
			$ls_codeval="%".utf8_encode($_REQUEST['txtcodeval'])."%";
			$ls_deneval="%".utf8_encode($_REQUEST['txtdeneval'])."%";
				
			header('Content-type:text/xml');
			print $io_aspectos->uf_srh_buscar_aspectos($ls_codeval,$ls_deneval);
		}
		
		elseif($evento=="buscar_aspectos")
			{
					
				   
				    $ls_codasp="%".utf8_encode($_REQUEST['txtcodasp'])."%";
	                $ls_denasp="%".utf8_encode($_REQUEST['txtdenasp'])."%";
					$ls_codeval=$_REQUEST['codeval'];
					header('Content-type:text/xml');
					print $io_aspectos->uf_srh_buscar_aspectos_items($ls_codeval, $ls_codasp, $ls_denasp);
					
			}
		elseif($evento=="createXML_aspectos")
			{
                       
				    $ls_codasp="%%";
	                $ls_denasp="%%";
					$ls_codeval=$_REQUEST['codeval'];
					header('Content-type:text/xml');
					print $io_aspectos->uf_srh_buscar_aspectos_items($ls_codeval, $ls_codasp, $ls_denasp);
					
					
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
  $io_asp = $io_json->decode(utf8_decode ($objeto));
    
   list($valido,$guardo)= $io_aspectos-> uf_srh_guardar_aspectos ($io_asp,$_POST["insmod"], $la_seguridad);
   if (($valido) && ($_POST["insmod"]!='modificar')) 
     {
     $ls_salida = 'El Aspecto de Evaluación ya esta registrado, no se puede agregar. Si desea modificarlo seleccione el registro del catalogo.';	
	}
	else if ((!$valido) &&($guardo))
	{ $ls_salida = 'El Aspecto de Evaluacion fue Registrado';}
	else if (($valido) && ($_POST["insmod"]=='modificar')&&($guardo))
	{ $ls_salida = 'El Aspecto de Evaluacion fue Actualizado';}
    else {$ls_salida = 'Error al guardar el Aspecto de Evaluacion';}   
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  list($valido,$existe)= $io_aspectos->uf_srh_delete_aspectos($_GET["codeval"],$_GET["codasp"], $la_seguridad);
  if ($existe)
  {$ls_salida = 'El Aspecto de Evaluacion no pueden ser eliminados porque esta asociados a Items de Evaluacion';}
  else 
  {
	  if ($valido)
	  {$ls_salida = 'El Aspecto de Evaluación fue Eliminado';}
	  else 
	  {$ls_salida = 'Ocurrio un error al eliminar el Aspecto de Evaluacion';}
  }
}
elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_aspectos->uf_srh_getProximoCodigo($_GET["codeval"],$_GET["codaspaux"] );  

}


  echo utf8_encode($ls_salida);


?>
