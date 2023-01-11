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
	require_once("../../class_folder/dao/sigesp_srh_c_solicitud_empleo.php");
	require_once("../../class_folder/dao/sigesp_srh_c_estado.php");
    require_once("../../class_folder/dao/sigesp_srh_c_municipio.php");
    require_once("../../class_folder/dao/sigesp_srh_c_parroquia.php");
    require_once("../../class_folder/utilidades/class_funciones_srh.php");
    require_once("../../class_folder/dao/sigesp_srh_c_pais.php");
    $io_pais=new sigesp_srh_c_pais('../../../');
	$io_estado = new sigesp_srh_c_estado('../../../');
	$io_municipio = new sigesp_srh_c_municipio ('../../../');
	$io_parroquia = new sigesp_srh_c_parroquia('../../../');
	$io_solicitud= new sigesp_srh_c_solicitud_empleo('../../../');
    $io_fun_srh=new class_funciones_srh('../../../');
	$ls_permisos = "";
	$la_seguridad = Array();
	$la_permisos = Array();
	$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_p_solicitud_empleo.php",$ls_permisos,$la_seguridad,$la_permisos);
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
			$ls_nrosol="%%";
			$ls_cedper="%%";
			$ls_apeper="%%";
			$ls_nomper="%%";
			$ls_fecsol1=$_REQUEST['txtfecsoldes'];	
			$ls_fecsol2=$_REQUEST['txtfecsolhas'];
			
			$ls_tipo=$_REQUEST['txttipo'];////agregado el 28/02/2008
			$ls_tipo_caja=$_REQUEST['hidtipo'];////agregado el 28/02/2008						
		    header('Content-type:text/xml');			
			print $io_solicitud->uf_srh_buscar_solicitud_empleo($ls_nrosol,$ls_cedper,$ls_apeper,$ls_nomper,$ls_fecsol1,$ls_fecsol2,$ls_tipo,$ls_tipo_caja);
		}
		
		elseif($evento=="buscar")
		{
			$ls_nrosol="%".utf8_encode($_REQUEST['txtnrosol'])."%";
			$ls_cedper="%".utf8_encode($_REQUEST['txtcedper'])."%";
			$ls_apeper="%".utf8_encode($_REQUEST['txtapeper'])."%";
			$ls_nomper="%".utf8_encode($_REQUEST['txtnomper'])."%";
			$ls_fecsol1=$_REQUEST['txtfecsoldes'];			
			$ls_fecsol2=$_REQUEST['txtfecsolhas'];			
			$ls_tipo=$_REQUEST['txttipo'];////agregado el 28/02/2008
			$ls_tipo_caja=$_REQUEST['hidtipo'];////agregado el 28/02/2008	
			header('Content-type:text/xml');	
			print $io_solicitud->uf_srh_buscar_solicitud_empleo($ls_nrosol,$ls_cedper,$ls_apeper,$ls_nomper,$ls_fecsol1,$ls_fecsol2,$ls_tipo,$ls_tipo_caja);
			
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



if ($ls_operacion == "ue_inicializar")
			{  	
			
			//Países
			 $arrResultado = $io_pais->getPais("ORDER BY despai ASC",$la_paises);
			 $la_paises = $arrResultado['pa_datos'];
			 $lb_hay = $arrResultado['lb_valido'];
			   if ($lb_hay)
			   {$ls_salida = $io_json->encode($la_paises);}
			
			}		

elseif ($ls_operacion == "ue_inicializarestado")
{
	  $arrResultado = $io_estado->getEstados($_GET["codpai"],"ORDER BY desest ASC",$la_estados);
	  $la_estados=$arrResultado['pa_datos'];
	  $lb_hay= $arrResultado['lb_valido'];
	  if ($lb_hay)
	  {$ls_salida  = $io_json->encode($la_estados);}
}
elseif ($ls_operacion == "ue_inicializarmunicipio")
{
	  $arrResultado = $io_municipio->getMunicipios($_GET["codpai"],$_GET["codest"],"ORDER BY denmun ASC",$la_municipios);
	  $la_municipios=$arrResultado['pa_datos'];
	  $lb_hay= $arrResultado['lb_valido'];
	  if ($lb_hay)
	  {$ls_salida  = $io_json->encode($la_municipios);}
}

elseif ($ls_operacion == "ue_inicializarparroquia")
{
	  $arrResultado = $io_parroquia->getparroquias($_GET["codpai"],$_GET["codest"],$_GET["codmun"],"ORDER BY denpar ASC",$la_parroquias);
	  $la_parroquias=$arrResultado['pa_datos'];
	  $lb_hay = $arrResultado['lb_valido'];
	  if ($lb_hay)
	  {$ls_salida  = $io_json->encode($la_parroquias);}
}
elseif ($ls_operacion == "ue_nuevo_codigo")
{  
    $ls_salida = $io_solicitud->uf_srh_getProximoCodigo();  

}
elseif($ls_operacion == "ue_chequear_cedula")
{
 	 list($lb_existe,$ls_numsol) = $io_solicitud->getCedPersonal($_GET["cedper"],$la_datos);
	  if ($lb_existe)
	  {
	 
	   $ls_salida  ='La Cédula ya Existe en la Solicitud de Empleo '.$ls_numsol;
	  }

}
 elseif ($ls_operacion == "ue_guardar")
{  
  $objeto = str_replace('\"','"',$_POST["objeto"]);
  $io_sol = $io_json->decode(utf8_decode ($objeto));
  $valido = $io_solicitud-> uf_srh_guardarsolicitud_empleo($io_sol->nrosol,$io_sol,$_POST["insmod"], $la_seguridad);
   if ($valido) {
    if ($_POST["insmod"]=='modificar')
	 {$ls_salida = 'La Solicitud de Empleo fue Actualizada';	}
	else { $ls_salida = 'La Solicitud de Empleo fue Registrada';}
  }
  else {$ls_salida = 'Error al guardar la Solicitud de Empleo';}
 
}
elseif ($ls_operacion == "ue_eliminar")
{  
  $io_solicitud->uf_srh_eliminarsolicitud_empleo($_GET["nrosol"], $la_seguridad);
  $ls_salida = 'La Solicitud de Empleo fue Eliminada';
}


  echo utf8_encode($ls_salida);


?>
