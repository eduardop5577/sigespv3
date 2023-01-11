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
	require_once("../../class_folder/dao/sigesp_srh_c_unidadadmin.php");
	
	$io_unidad= new sigesp_srh_c_unidadadmin('../../../');
    
if (isset($_GET['valor']))
{
	    $evento=$_GET['valor'];

		if($evento=="createXML")
		{
			
			$ls_denuniadm="%%";
			$ls_tipo=$_REQUEST['txttipo'];
						
			header('Content-type:text/xml');			
			print $io_unidad->uf_srh_buscar_unidadadmin($ls_denuniadm,$ls_tipo);
		}
		
		elseif($evento=="buscar")
		{

			$ls_denuniadm="%".utf8_encode($_REQUEST['txtdenuniadm'])."%";			
			$ls_tipo=$_REQUEST['txttipo'];
			header('Content-type:text/xml');	
			print $io_unidad->uf_srh_buscar_unidadadmin($ls_denuniadm,$ls_tipo);
			
		}
			
}



?>
