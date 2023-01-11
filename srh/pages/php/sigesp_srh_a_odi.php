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
	require_once("../../class_folder/dao/sigesp_srh_c_odi.php");	
	require_once("../../class_folder/utilidades/class_funciones_srh.php");

	$io_odi= new sigesp_srh_c_odi('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_p_odi.php",$ls_permisos,$la_seguridad,$la_permisos);
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
			$ls_fecha1=$_REQUEST['txtfechades'];
			$ls_fecha2=$_REQUEST['txtfechahas'];
			
		    header('Content-type:text/xml');
			print $io_odi->uf_srh_buscar_odi($ls_nroreg,$ls_fecha1,$ls_fecha2);
		}
		
		elseif($evento=="buscar")
		{
			$ls_nroreg="%".utf8_encode($_REQUEST['txtnroreg'])."%";
			$ls_fecha1=$_REQUEST['txtfechades'];
			$ls_fecha2=$_REQUEST['txtfechahas'];	
			header('Content-type:text/xml');
			print $io_odi->uf_srh_buscar_odi($ls_nroreg,$ls_fecha1,$ls_fecha2);
		}
			
	
}



require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/Json.php");	
$io_json = new Services_JSON();	


if (array_key_exists("operacion",$_GET))
{
  $ls_operacion = $_GET["operacion"];
}
else if(array_key_exists("operacion",$_POST))
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
  $io_obj = $io_json->decode(utf8_decode($objeto));
  $lb_exist = true;
  $lb_existanterior_odi=$io_odi-> uf_busca_anterior_odi($io_obj);
  if (!$lb_existanterior_odi)
  {
	  $arrResultado = $io_odi-> uf_srh_guardarodi ($io_obj->nroreg,$io_obj,$_POST["insmod"],$lb_exist,$la_seguridad);
	  $lb_exist = $arrResultado['lb_exist'];
	  $valido = $arrResultado['lb_valido'];
	  if ($valido) 
	  {
		if ($_POST["insmod"]=='modificar')
		{
			$ls_salida = 'Los Objetivos de Desempeño Individual fueron Actualizados';	
		}
		else 
		{ 
			$ls_salida = 'Los Objetivos de Desempeño Individual fueron Registrados';
		}
	  }
	  elseif((!$valido)&&(!$lb_exist))
	  {
		$ls_salida = 'Error al guardar, existen periodos de evaluación en meses iguales, por favor chequee';
	  }
	  else 
	  {
		$ls_salida = 'Error al guardar Los Objetivos de Desempeño Individual';
	  }
   }
   else
   {
		$arrResultado = $io_odi-> uf_srh_guardarodi ($io_obj->nroreg,$io_obj,$_POST["insmod"],$lb_exist,$la_seguridad);
	    $lb_exist = $arrResultado['lb_exist'];
	    $valido = $arrResultado['lb_valido'];
		  if ($valido) 
		  {
			if ($_POST["insmod"]=='modificar')
			{
				$ls_salida = 'Los Objetivos de Desempeño Individual fueron Actualizados';	
			}
			else 
			{ 
				$ls_salida = 'Los Objetivos de Desempeño Individual fueron Registrados';
			}
		  }
		  elseif((!$valido)&&(!$lb_exist))
		  {
			$ls_salida = 'Error al guardar, existen periodos de evaluación en meses iguales, por favor chequee';
		  }
		  else 
		  {
			$ls_salida = 'Error al guardar Los Objetivos de Desempeño Individual';
		  }
   }
}

elseif ($ls_operacion == "ue_eliminar")
{  
   list($existe,$valido)= $io_odi->uf_srh_eliminarodi($_GET["nroreg"], $la_seguridad);
   if (!$existe)
  {$ls_salida = 'Los Objetivos de Desempeño Individual no pueden ser eliminados porque esta asociada a una Revsion';}
  else 
  {
	  if (!$valido)
	  {$ls_salida = 'Los Objetivos de Desempeño Individual fueron Eliminados';}
	  else 
	  {$ls_salida = 'Ocurrio un error al eliminar los Objetivos de Desempeño Individual';}
  }
  
}
elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_odi->uf_srh_getProximoCodigo();  

}

  echo utf8_encode($ls_salida);


?>
