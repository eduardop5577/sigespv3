<?php
/***********************************************************************************
* @fecha de modificacion: 18/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv."/modelo/servicio/mis/sigesp_srv_mis_ireportedocumento.php");

class ServicioReporteDocumento implements IReporteDocumento
{
	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
		
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
	}

	public function buscarDocumentos($codusu, $fecdes, $fechas, $modulo, $concepto, $order) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$parametroBusqueda = '';
		
		if($fecdes!='' && $fechas!=''){
			$parametroBusqueda .= "AND cmp.fecha  between '".convertirFechaBd($fecdes)."' AND '".convertirFechaBd($fechas)."' ";	
		}
				
		if ($codusu!='') {
			$parametroBusqueda .= "AND cmp.codusu like '%".$codusu."%' ";
		}
		
		if($modulo!="NSD"){
			$parametroBusqueda .= "AND cmp.procede like '".$modulo."%' ";	
		}
		
		if($concepto!=""){
			$parametroBusqueda .= "AND cmp.descripcion like '%".$concepto."%'";
		}
	
		$cadenaSQL = "SELECT cmp.comprobante AS numdoc,cmp.total as monto,cmp.fecha,cmp.procede,pro.desproc,cmp.codusu
				 		FROM sigesp_cmp cmp
				 			INNER JOIN sigesp_procedencias pro ON cmp.procede=pro.procede
				 		WHERE cmp.codemp = '".$_SESSION["la_empresa"]["codemp"]."' 
				       	".$parametroBusqueda."  
				 		ORDER BY ".$order;
		
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarUsuarios() {
		$cadenaSQL = "SELECT 
						FROM sss_usuarios
						WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."'";
	}
	
}
?>