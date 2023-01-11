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

$dirctrscg = "";
$dirctrscg = dirname(__FILE__);
$dirctrscg = str_replace("\\","/",$dirctrscg);
$dirsrvrpc = str_replace("/modelo/servicio/scg","",$dirctrscg); 
$dirctrscg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirctrscg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirctrscg."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_ilistado_comprobante.php");

class servicioListadoComp implements iListadoComp 
{
	private $daoPago;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
	{
		$this->daoPago = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
	}
	
	/* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarCodigoParametro()
	 */
	public function buscarCentroCostos() {
		$this->daoPago = FabricaDao::CrearDAO("N", "sigesp_cencosto");
		$datacencos = $this->daoPago->leerTodos('codcencos',1,'');
		unset($this->daoPago);
		return $datacencos;
	}
	
		 /* (non-PHPdoc)
	 * @see modelo/servicio/rpc/iparametroclasificacion::buscarParametros()
	 */
	public function buscarComprobantes($ls_comprobante,$ls_procedencia) {
		$ls_gestor = $_SESSION["ls_gestor"];
		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		
		if($ls_comprobante=="000000000000000")
		{
			$ls_comprobante="";
		}
		
		$ls_order=" ORDER BY sigesp_cmp.comprobante,sigesp_cmp.fecha,sigesp_cmp.procede";
		
		switch($ls_gestor)
		{
		case 'MYSQLT':
			$cadenasql="SELECT sigesp_cmp.codemp,sigesp_cmp.procede,
			                sigesp_cmp.comprobante,sigesp_cmp.descripcion,
							sigesp_cmp.fecha,sigesp_cmp.cod_pro,
							sigesp_cmp.ced_bene,sigesp_cmp.tipo_destino,
							SUM(scg_dt_cmp.monto) AS monto,
							(CASE   sigesp_cmp.tipo_destino
								WHEN 'P'
                    				THEN
                        				(SELECT rpc_proveedor.nompro
                        				 FROM rpc_proveedor
                                         WHERE rpc_proveedor.codemp=sigesp_cmp.codemp AND sigesp_cmp.cod_pro=rpc_proveedor.cod_pro)
								WHEN 'B'
                    				THEN
                      					(SELECT CONCAT(RTRIM(rpc_beneficiario.apebene),',',rpc_beneficiario.nombene)
                      					 FROM rpc_beneficiario
                      					 WHERE rpc_beneficiario.codemp=sigesp_cmp.codemp AND sigesp_cmp.cod_pro=rpc_beneficiario.ced_bene)
								ELSE 'Ninguno'
								END)  as  nombre
					 FROM sigesp_cmp
					 INNER JOIN scg_dt_cmp
					 ON  scg_dt_cmp.debhab = 'D' AND scg_dt_cmp.codemp=sigesp_cmp.codemp
					 AND scg_dt_cmp.procede=sigesp_cmp.procede AND scg_dt_cmp.comprobante=sigesp_cmp.comprobante
					 AND scg_dt_cmp.fecha=sigesp_cmp.fecha AND scg_dt_cmp.codban=sigesp_cmp.codban
					 AND scg_dt_cmp.ctaban=sigesp_cmp.ctaban
					 WHERE sigesp_cmp.codemp='".$_SESSION['la_empresa']['codemp']."' AND  sigesp_cmp.comprobante like '%".$ls_comprobante."%' 
					 AND sigesp_cmp.procede like '%".$ls_procedencia."%'
					 GROUP BY sigesp_cmp.codemp,sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.descripcion,sigesp_cmp.fecha,sigesp_cmp.cod_pro, sigesp_cmp.ced_bene,sigesp_cmp.tipo_destino,sigesp_cmp.codban,sigesp_cmp.ctaban";
			break;

		case 'MYSQLI':
			$cadenasql="SELECT sigesp_cmp.codemp,sigesp_cmp.procede,
			                sigesp_cmp.comprobante,sigesp_cmp.descripcion,
							sigesp_cmp.fecha,sigesp_cmp.cod_pro,
							sigesp_cmp.ced_bene,sigesp_cmp.tipo_destino,
							SUM(scg_dt_cmp.monto) AS monto,
							(CASE   sigesp_cmp.tipo_destino
								WHEN 'P'
                    				THEN
                        				(SELECT rpc_proveedor.nompro
                        				 FROM rpc_proveedor
                                         WHERE rpc_proveedor.codemp=sigesp_cmp.codemp AND sigesp_cmp.cod_pro=rpc_proveedor.cod_pro)
								WHEN 'B'
                    				THEN
                      					(SELECT CONCAT(RTRIM(rpc_beneficiario.apebene),',',rpc_beneficiario.nombene)
                      					 FROM rpc_beneficiario
                      					 WHERE rpc_beneficiario.codemp=sigesp_cmp.codemp AND sigesp_cmp.cod_pro=rpc_beneficiario.ced_bene)
								ELSE 'Ninguno'
								END)  as  nombre
					 FROM sigesp_cmp
					 INNER JOIN scg_dt_cmp
					 ON  scg_dt_cmp.debhab = 'D' AND scg_dt_cmp.codemp=sigesp_cmp.codemp
					 AND scg_dt_cmp.procede=sigesp_cmp.procede AND scg_dt_cmp.comprobante=sigesp_cmp.comprobante
					 AND scg_dt_cmp.fecha=sigesp_cmp.fecha AND scg_dt_cmp.codban=sigesp_cmp.codban
					 AND scg_dt_cmp.ctaban=sigesp_cmp.ctaban
					 WHERE sigesp_cmp.codemp='".$_SESSION['la_empresa']['codemp']."' AND  sigesp_cmp.comprobante like '%".$ls_comprobante."%' 
					 AND sigesp_cmp.procede like '%".$ls_procedencia."%'
					 GROUP BY sigesp_cmp.codemp,sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.descripcion,sigesp_cmp.fecha,sigesp_cmp.cod_pro, sigesp_cmp.ced_bene,sigesp_cmp.tipo_destino,sigesp_cmp.codban,sigesp_cmp.ctaban";
			break;
				
		case 'POSTGRES':
			$cadenasql="SELECT sigesp_cmp.codemp,sigesp_cmp.procede,
			                sigesp_cmp.comprobante,sigesp_cmp.descripcion,
							sigesp_cmp.fecha,sigesp_cmp.cod_pro,
							sigesp_cmp.ced_bene,sigesp_cmp.tipo_destino,
							SUM(scg_dt_cmp.monto) AS monto,
							(CASE   sigesp_cmp.tipo_destino
								WHEN 'P'
                    				THEN
                        				(SELECT rpc_proveedor.nompro
                        				 FROM rpc_proveedor
                                         WHERE rpc_proveedor.codemp=sigesp_cmp.codemp AND sigesp_cmp.cod_pro=rpc_proveedor.cod_pro)
								WHEN 'B'
                    				THEN
                      					(SELECT TRIM(rpc_beneficiario.apebene)||','||rpc_beneficiario.nombene
                      					 FROM rpc_beneficiario
                      					 WHERE rpc_beneficiario.codemp=sigesp_cmp.codemp AND sigesp_cmp.cod_pro=rpc_beneficiario.ced_bene)
								ELSE 'Ninguno'
								END)  as  nombre
					 FROM sigesp_cmp
					 INNER JOIN scg_dt_cmp
					 ON  scg_dt_cmp.debhab = 'D' AND scg_dt_cmp.codemp=sigesp_cmp.codemp
					 AND scg_dt_cmp.procede=sigesp_cmp.procede AND scg_dt_cmp.comprobante=sigesp_cmp.comprobante
					 AND scg_dt_cmp.fecha=sigesp_cmp.fecha AND scg_dt_cmp.codban=sigesp_cmp.codban
					 AND scg_dt_cmp.ctaban=sigesp_cmp.ctaban
					 WHERE sigesp_cmp.codemp='".$_SESSION['la_empresa']['codemp']."' AND  sigesp_cmp.comprobante like '%".$ls_comprobante."%' 
					 AND sigesp_cmp.procede like '%".$ls_procedencia."%'
					 GROUP BY sigesp_cmp.codemp,sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.descripcion,sigesp_cmp.fecha,sigesp_cmp.cod_pro, sigesp_cmp.ced_bene,sigesp_cmp.tipo_destino,sigesp_cmp.codban,sigesp_cmp.ctaban ";
			break;
	}
		$cadenasql=$cadenasql.$ls_order;
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;

	}
}
?>