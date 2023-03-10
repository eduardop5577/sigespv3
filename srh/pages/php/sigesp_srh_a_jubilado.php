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
require_once("../../class_folder/utilidades/class_funciones_srh.php");
$io_fun_srh=new class_funciones_srh('../../../');
$ls_permisos = "";
$la_seguridad = Array();
$la_permisos = Array();
$arrResultado = $io_fun_srh->uf_load_seguridad("SRH","sigesp_srh_d_jubilado.php",$ls_permisos,$la_seguridad,$la_permisos);
$ls_permisos = $arrResultado['as_permisos'];
$la_seguridad = $arrResultado['aa_seguridad'];
$la_permisos = $arrResultado['aa_permisos'];
$ls_logusr=utf8_encode($_SESSION["la_logusr"]);

require_once("../../class_folder/dao/sigesp_srh_c_personal.php");
$io_personal=new sigesp_srh_c_personal('../../../');
require_once("../../class_folder/dao/sigesp_srh_c_pais.php");
$io_pais=new sigesp_srh_c_pais('../../../');
require_once("../../class_folder/dao/sigesp_srh_c_estado.php");
$io_estado=new sigesp_srh_c_estado('../../../');
require_once("../../class_folder/dao/sigesp_srh_c_municipio.php");
$io_municipio=new sigesp_srh_c_municipio('../../../');
require_once("../../class_folder/dao/sigesp_srh_c_parroquia.php");
$io_parroquia=new sigesp_srh_c_parroquia('../../../');
require_once("../../class_folder/dao/sigesp_srh_c_tipodeduccion.php");
$io_deduccion=new sigesp_srh_c_tipodeduccion('../../../');
$ls_salida="";
$ls_operacion = ""; 
if (isset($_GET['valor']))
{
	$evento=$_GET['valor'];
	if($evento=="buscar")
	{
		$ls_codper="%".utf8_encode($_REQUEST['txtcodper'])."%";
		$ls_cedper="%".utf8_encode($_REQUEST['txtcedper'])."%";
		$ls_apeper="%".utf8_encode($_REQUEST['txtapeper'])."%";
		$ls_nomper="%".utf8_encode($_REQUEST['txtnomper'])."%";
		$ls_tipo=$_REQUEST['hidtipo'];
			
		header('Content-type:text/xml');
		print $io_personal->uf_srh_buscar_personaljubilado($ls_codper,$ls_cedper,$ls_apeper,$ls_nomper,$ls_tipo);
	}
}
require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/Json.php");	
$io_json=new Services_JSON();
if (array_key_exists("operacion",$_GET))
{
	$ls_operacion = $_GET["operacion"];  
}
else if (array_key_exists("operacion",$_POST))
{
	$ls_operacion = $_POST["operacion"];  
}

switch ($ls_operacion)
{
	case "ue_inicializar":
		$arrResultado = $io_pais->getPais("ORDER BY despai ASC",$la_paises);
	    $la_paises = $arrResultado['pa_datos'];
	    $lb_hay = $arrResultado['lb_valido'];
		if ($lb_hay)
		{
			$ls_salida = $io_json->encode($la_paises);
		} 
	break;

	case "ue_inicializarestado":
		$arrResultado = $io_estado->getEstados($_GET["codpai"],"ORDER BY desest ASC",$la_estados);
	     $la_estados=$arrResultado['pa_datos'];
	     $lb_hay= $arrResultado['lb_valido'];
		if ($lb_hay)
		{
			$ls_salida  = $io_json->encode($la_estados);
		}
	break;

	case "ue_inicializarmunicipio":
		$arrResultado = $io_municipio->getMunicipios($_GET["codpai"],$_GET["codest"],"ORDER BY denmun ASC",$la_municipios);
		$la_municipios=$arrResultado['pa_datos'];
		$lb_hay= $arrResultado['lb_valido'];
		if ($lb_hay)
		{
			$ls_salida  = $io_json->encode($la_municipios);
		}
	break;

	case "ue_inicializarparroquia":
		$arrResultado = $io_parroquia->getparroquias($_GET["codpai"],$_GET["codest"],$_GET["codmun"],"ORDER BY denpar ASC",$la_parroquias);
		$la_parroquias=$arrResultado['pa_datos'];
		$lb_hay = $arrResultado['lb_valido'];
		if ($lb_hay)
		{
			$ls_salida  = $io_json->encode($la_parroquias);
		}
	break;

	case "ue_guardar":
		$objeto = str_replace('\"','"',$_POST["objeto"]);
		$lo_personal = $io_json->decode(utf8_decode ($objeto));
		$valido= $io_personal->uf_srh_guardarjubilado ($lo_personal,$_POST["insmod"],$la_seguridad); 
		if ($valido)
		{
			if ($_POST["insmod"]=='modificar')
			{
				$ls_salida = 'El Personal fue Actualizado';	
			}
			else
			{
				$ls_salida = 'El Personal fue Registrado';
			}
		}
		else 
		{
			$ls_salida = 'Error al guardar el personal'; 
		}
	break;

	case "ue_nuevo_beneficiario":
		$ls_salida = $io_personal->uf_srh_getProximoCodigo_Beneficiario($_GET["codper"]);  
	break;

	case "ue_guardar_beneficiario":
		$objeto4 = str_replace('\"','"',$_POST["objeto4"]);	
		$lo_ben = $io_json->decode(utf8_decode ($objeto4));	
		$valido= $io_personal->uf_srh_guardar_beneficiario($lo_ben,$_POST["insmod"],$la_seguridad);
		if ($valido)
		{
			if ($_POST["insmod"]=='modificar')
			{
				$ls_salida = 'El Beneficiario fue Actualizado';
			}
			else 
			{
				$ls_salida = 'El Beneficiario fue Registrado';
			}
		}
		else 
		{
			$ls_salida = 'Error al Guardar Beneficiario';
		} 
	break;

	case "ue_eliminar_beneficiario":
		$valido= $io_personal->uf_srh_eliminar_beneficiario($_GET["codben"],$_GET["codper"],$_GET["tipben"],$la_seguridad);  
		if ($valido)
		{
			$ls_salida = 'El Beneficiario fue eliminado';
		}
		else
		{
			$ls_salida = 'Error al Eliminar Beneficiario';
		} 
	break;
}

echo utf8_encode($ls_salida);
?>