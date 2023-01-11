<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
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
require_once ($dirsrv."/modelo/servicio/spg/sigesp_srv_spg_icerrarpre.php");
require_once ($dirsrv."/modelo/servicio/sss/sigesp_srv_sss_evento.php");

class ServicioCierrePresupuestarioGasto implements ICierrePresupuestarioGasto {

	public  $mensaje; 
	public  $valido; 
	private $conexionBaseDatos; 
	private $daoComprobante;
	
	public function __construct()
	{
		$this->mensaje = '';
		$this->valido = true;
		$this->daoComprobante = null;
		$this->conexionbd  = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function procesarCierrePresupuestario($codemp,$objson,$arrevento)
	{
		DaoGenerico::iniciarTrans();
		$strPK = "codemp='{$codemp}'";
		$this->daoCambioEstatus = FabricaDao::CrearDAO('C','sigesp_empresa',null,$strPK);
		$this->daoCambioEstatus->estciespg=$objson->cierev;
		$this->daoCambioEstatus->estciespi=$objson->cierev;
		if($this->daoCambioEstatus->modificar()==0){
			$this->mensaje=' Error al actualizar estatus de cierre presupuestario ';
			$this->valido=false;
		}
		if($this->valido){
			$servicioEvento = new ServicioEvento();
			$servicioEvento->evento=$arrEvento['evento'];
			$servicioEvento->codemp=$arrEvento['codemp'];
			$servicioEvento->codsis=$arrEvento['codsis'];
			$servicioEvento->nomfisico=$arrEvento['nomfisico'];
			if(DaoGenerico::completarTrans($this->valido)){
				$servicioEvento->desevetra=$arrEvento['desevetra'];
				$servicioEvento->tipoevento=true;
				$servicioEvento->incluirEvento();
				if($objson->cierev==1){
					$this->mensaje = 'El Cierre Presupuestario de Gasto fue realizado exitosamente ';
				}
				else{
					$this->mensaje = 'El Reverso del Cierre Presupuestario de Gasto fue realizado exitosamente';
				}
				$this->valido = true;
			}
			else{
				$servicioEvento->desevetra='error en procesamiento de actualizacion';
				$servicioEvento->tipoevento=false;
				$servicioEvento->incluirEvento();
				if($objson->cierev==1){
					$this->mensaje = ' Ocurrio un error al realizar el Cierre Presupuestario de Gasto ';
				}
				else{
					$this->mensaje = 'Ocurrio un error al realizar el Reverso del Cierre Presupuestario de Gasto ';
				}
				$this->valido = false;
			}
			unset($servicioEvento);
		}
		return $this->valido;
	}
	
	public function buscarEstCiePreGas($codemp)
	{
		$cadenasql = '';
		$estatus = '';
		$cadenasql="SELECT estciespg ".
			   "    FROM sigesp_empresa  ".
			   "    WHERE codemp='".$codemp."' ";	
		$resultado = $this->conexionbd->Execute($cadenasql);
		if($resultado===false)
		{
			$this->mensaje .= ' CLASE->SPG MÉTODO->buscarEstCiePreGas ERROR->'.$this->conexionbd->ErrorMsg();
			$this->valido = false;
		}
		else{
			if(!$resultado->EOF){
				$estatus=$resultado->fields['estciespg'];
			}
		}
		return $estatus;
	}
}
?>