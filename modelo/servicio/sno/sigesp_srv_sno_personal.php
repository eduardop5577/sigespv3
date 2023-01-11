<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrvrpc = "";
$dirsrvrpc = dirname(__FILE__);
$dirsrvrpc = str_replace("\\","/",$dirsrvrpc);
$dirsrvrpc = str_replace("/modelo/servicio/sno","",$dirsrvrpc); 
require_once ($dirsrvrpc."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvrpc."/modelo/servicio/sno/sigesp_srv_sno_ipersonal.php");
require_once ($dirsrvrpc."/modelo/servicio/sss/sigesp_srv_sss_evento.php");

class servicioPersonal implements ipersonal
{
	private $daoPersonal;
	private $conexionbd;
	
	public function __construct()
	{
		$this->daoPersonal = null;	
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
		$this->valido=true;
		$this->mensaje='';		
	}

	public function buscarPersonal($codemp,$codper,$cedper,$nomper,$apeper,$esBeneficiario)
	{
		$cadenaFiltro = '';
		$cadenaOrden = '';
		if(!empty($esBeneficiario))
		{ 
			$cadenaFiltro = $cadenaFiltro." AND cedper NOT IN (SELECT ced_bene FROM rpc_beneficiario  WHERE codemp = '".$codemp."')";
		}
		if(!empty($codper))
		{
			$cadenaFiltro = $cadenaFiltro." AND codper like '%$codper%'";
		}
		if(!empty($cedper))
		{
			$cadenaFiltro = $cadenaFiltro." AND cedper like '%$cedper%'";
		}
		if(!empty($nomper))
		{
			$cadenaFiltro =  $cadenaFiltro." AND ".ConexionBaseDatos::criterioUpperSIGESP('nomper', "'%{$nomper}%'", 'LIKE');
		}
		if(!empty($apeper))
		{
			$cadenaFiltro =  $cadenaFiltro." AND ".ConexionBaseDatos::criterioUpperSIGESP('apeper', "'%{$apeper}%'", 'LIKE');
		}
		switch (strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":
				$cadenaOrden .= ' ORDER BY CAST(trim(cedper) AS SIGNED) ';
				break;

			case "MYSQLI":
				$cadenaOrden .= ' ORDER BY CAST(trim(cedper) AS SIGNED) ';
				break;
				
			case "POSTGRES":
				$cadenaOrden .= ' ORDER BY CAST(trim(cedper) AS INT) ';
				break;
				
			case "OCI8PO":
				$cadenaOrden .= 'ORDER BY CAST(trim(cedper) AS INT) ';
				break;
	   	}
		
		$cadenaSql ="SELECT codper, cedper, nomper, apeper ".
					"  FROM sno_personal ".
					" WHERE codemp='".$codemp."' ".
					"   AND estper='1' ".
					"   {$cadenaFiltro}".
					"  ".$cadenaOrden;
		$dataSet  = $this->conexionbd->Execute ( $cadenaSql );
		if ($dataSet===false)
		{
			$this->mensaje .= '  ->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		return $dataSet;		
	}
}
?>