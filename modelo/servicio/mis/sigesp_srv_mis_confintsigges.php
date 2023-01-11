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
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_conexion.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');

class ServicioConfIntSigGes //implements IIntegracionSPG
{

	public  $mensaje; 
	private $conexionBaseDatos; 
	private $conexionAlterna;
	private $daoConfintsigges;
	private $daoDetalleConf;
	
	public function __construct()
	{
		$this->mensaje           = null;
		$this->conexionBaseDatos = null;
		$this->conexionAlterna   = null;
		$this->daoConfintsigges  = null;
		$this->daoDetalleConf    = null;
	}
	
	public function buscarNumero() {
		$this->daoConfintsigges = FabricaDao::CrearDAO('N', 'mis_confintsigges');
		$numero = $this->daoConfintsigges->buscarCodigo('numcon', false, 4);
		unset($this->daoConfintsigges);
		return $numero;
	}
	
	public function buscarFondo($codagencia, $denagencia) {
		$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
		$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
				$arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
		$cadenaSQL = "SELECT COD_AGENCIA, NOMBRE 
						FROM CG_AGENCIAS 
						WHERE COD_AGENCIA LIKE '%{$codagencia}%'";
		return $this->conexionAlterna->Execute($cadenaSQL);
	}
	
	public function buscarCuenta($objetoJson) {
		$dataCuenta = null;
		if ($objetoJson->bdbus == 'S') {
			$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
			$cadenaSQL  = "SELECT sc_cuenta AS codcuenta, denominacion as dencuenta 
							FROM scg_cuentas 
							WHERE status = 'C' AND sc_cuenta LIKE '%{$objetoJson->codcue}%' 
							AND denominacion ILIKE '%{$objetoJson->dencue}%' 
							ORDER BY sc_cuenta";
			$dataCuenta = $this->conexionBaseDatos->Execute($cadenaSQL);
		}
		else {
			$arrConfi = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'].'/base/conf/conf_conexion.ini');
			$this->conexionAlterna = ConexionBaseDatos::conectarAlternaBD($arrConfi['HOST_DBALT'], $arrConfi['LOGG_DBALT'], $arrConfi['PASS_DBALT'],
				$arrConfi['NOMB_DBALT'], $arrConfi['GEST_DBALT'], $arrConfi['PORT_DBALT']);
			$cadenaSQL = "SELECT COD_CUENTA AS codcuenta, NOMBRE AS dencuenta
							FROM CG_PLAN_CUENTAS 
							WHERE COD_AGENCIA = '{$objetoJson->codfon}'
							AND COD_CUENTA LIKE '%{$objetoJson->codcue}%' AND NOMBRE  LIKE '%{$objetoJson->dencue}%'";
			$dataCuenta = $this->conexionAlterna->Execute($cadenaSQL);
		}
		
		return $dataCuenta;
	}
	
	public function eliminarCuentas($numcon) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "DELETE FROM mis_dt_confinsigges WHERE numcon = '{$numcon}'";
		$this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function insertarConfiguracion ($objJson) {
		$respuesta = true;
		//ConexionBaseDatos::getInstanciaConexion()->debug = true;
		$this->daoConfintsigges = FabricaDao::CrearDAO('N', 'mis_confintsigges');
		$this->daoConfintsigges->setData($objJson);
		if ($this->daoConfintsigges->incluir()) {
			foreach ($objJson->arrConf as $recdetalle) {
				$this->daoDetalleConf = FabricaDao::CrearDAO('N', 'mis_dt_confinsigges');
				$this->daoDetalleConf->setData($recdetalle);
				$this->daoDetalleConf->numcon = $this->daoConfintsigges->numcon;
				if (!$this->daoDetalleConf->incluir()) {
					$this->mensaje = 'Ocurri&#243; un error al insertar los destalles '.$this->daoDetalleConf->ErrorMsg();
					$this->eliminarCuentas($this->daoConfintsigges->numcon);
					$this->daoConfintsigges->eliminar();
					$respuesta = false;
					break;
				}
				unset($this->daoDetalleConf);
			}
				
			if ($respuesta) {
				$this->mensaje = "La configuracion fue procesada exitosamente ";
			}
		}
		else {
			$respuesta = false;
			$this->mensaje = 'Ocurrio un error al procesar la configuracion '.$this->daoActividad->ErrorMsg();
		}
		unset($this->daoConfintsigges);
		return $respuesta;
	}
	
	public function buscarConfiguracion($numcon, $descon) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "SELECT * FROM mis_confintsigges 
						WHERE numcon LIKE '%{$numcon}%' AND descon ILIKE '%{$descon}%'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function obtenerCuentas($numcon) {
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
		$cadenaSQL = "SELECT *, CAST('S' AS char) AS estbdt 
						FROM mis_dt_confinsigges
						WHERE numcon = '{$numcon}'";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function eliminarCuenta($objJson) {
		$respuesta = true;
		$cadenaPkDt = "numcon = '{$objJson->numcon}' AND cueori = '{$objJson->cueori}' AND codopo = '{$objJson->codopo}' AND 
					   cuedes = '{$objJson->cuedes}' AND codopd = '{$objJson->codopd}'";
		$this->daoDetalleConf = FabricaDao::CrearDAO('C', 'mis_dt_confinsigges', array(), $cadenaPkDt);
		if ($this->daoDetalleConf->_saved) {
			$respuesta = $this->daoDetalleConf->eliminar();
		}
		return $respuesta;
	}
	
	public function modificarConfiguracion($objJson) {
		$respuesta = true;
		//ConexionBaseDatos::getInstanciaConexion()->debug = true;
		$cadenaPk = "numcon = '{$objJson->numcon}'";
		$this->daoConfintsigges = FabricaDao::CrearDAO('C', 'mis_confintsigges', array(), $cadenaPk);
		$this->daoConfintsigges->descon = utf8_decode($objJson->descon);
		$this->daoConfintsigges->codfon = $objJson->codfon;
		$this->daoConfintsigges->movban = $objJson->movban;
		$this->daoConfintsigges->baslec = $objJson->baslec;
		$this->daoConfintsigges->basesc = $objJson->basesc;
		$this->daoConfintsigges->obvcue = $objJson->obvcue;
		if ($this->daoConfintsigges->modificar() != 0) {
			foreach ($objJson->arrConf as $recdetalle) {
				$cadenaPkDt = "cueori = '{$recdetalle->cueori}' AND codopo = '{$recdetalle->codopo}' AND  
						       cuedes = '{$recdetalle->cuedes}' AND codopd = '{$recdetalle->codopd}' AND
						       numcon = '{$this->daoConfintsigges->numcon}'";
				$this->daoDetalleConf = FabricaDao::CrearDAO('C', 'mis_dt_confinsigges', array(), $cadenaPkDt);
				if (!$this->daoDetalleConf->_saved) {
					$this->daoDetalleConf->setData($recdetalle);
					$this->daoDetalleConf->numcon = $this->daoConfintsigges->numcon;
					if (!$this->daoDetalleConf->incluir()) {
						$this->mensaje = 'Ocurri&#243; un error al insertar los destalles '.$this->daoDetalleConf->ErrorMsg();
						$this->eliminarCuentas($this->daoConfintsigges->numcon);
						$this->daoConfintsigges->eliminar();
						$respuesta = false;
						break;
					}
				}
								
				unset($this->daoDetalleConf);
			}
	
			if ($respuesta) {
				$this->mensaje = "La configuracion fue procesada exitosamente ";
			}
		}
		else {
			$respuesta = false;
			$this->mensaje = 'Ocurrio un error al procesar la configuracion '.$this->daoActividad->ErrorMsg();
		}
		unset($this->daoConfintsigges);
		return $respuesta;
	}
	
	public function eliminarConfiguracion($numcon) {
		$respuesta =  true;
		$cadenaPk = "numcon = '{$numcon}'";
		$this->daoConfintsigges = FabricaDao::CrearDAO('C', 'mis_confintsigges', array(), $cadenaPk);
		if ($this->daoConfintsigges->_saved) {
			$this->eliminarCuentas($numcon);
			$respuesta = $this->daoConfintsigges->eliminar();
		}
		unset($this->daoConfintsigges);
		return $respuesta;
	}
}
?>