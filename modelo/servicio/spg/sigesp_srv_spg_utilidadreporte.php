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
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_conexion.php');

class ServicioUtilidadReporte
{
	private $conexionbd;
	
	public function __construct()
	{
		$this->conexionbd  = null;
	}
	
	function obtenerDenominacionEstructura($codestpro, $nivel, $estcla, $codestpro1= '', $codestpro2 = '', $codestpro3 = '', $codestpro4 = '') {
		$this->conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$denominacion = '';
	 	$tabla = '';
	 	$campoSel = '';
	 	$campoWhe = '';
	 	$filtroPadre = ''; 
		switch ($nivel) {
			case 1:
				$tabla = 'spg_ep1';
				$campoSel = 'denestpro1';
	 			$campoWhe = 'codestpro1';
				break;
				
			case 2:
				$tabla = 'spg_ep2';
				$campoSel = 'denestpro2';
				$campoWhe = 'codestpro2';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}'";
				break;
		
			case 3:
				$tabla = 'spg_ep3';
				$campoSel = 'denestpro3';
				$campoWhe = 'codestpro3';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}' 
				                 AND  codestpro2 = '{$codestpro2}'";
				break;
				
			case 4:
				$tabla = 'spg_ep4';
				$campoSel = 'denestpro4';
				$campoWhe = 'codestpro4';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}'
								 AND  codestpro2 = '{$codestpro2}'
								 AND  codestpro3 = '{$codestpro3}'";
				break;
				
			case 5:
				$tabla = 'spg_ep5';
				$campoSel = 'denestpro5';
				$campoWhe = 'codestpro5';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}'
								 AND  codestpro2 = '{$codestpro2}'
								 AND  codestpro3 = '{$codestpro3}'
								 AND  codestpro4 = '{$codestpro4}'	";
				break;
		}
		
	 	$cadenaSQL = "SELECT {$campoSel} 
	 					FROM {$tabla} 
	 					WHERE codemp = '{$_SESSION['la_empresa']['codemp']}' 
	 					AND {$campoWhe} ='{$codestpro}' 
	 					AND estcla = '{$estcla}' {$filtroPadre}";
	 	$dataSet = $this->conexionbd->Execute($cadenaSQL);
	 	if (!$dataSet->EOF) {
	 		$denominacion = $dataSet->fields[$campoSel];
	 	}
	 	
	 	return $denominacion;
	}
	
	public function obtenerCodigoMaxMin($maxmin, $nivel, $estcla = false, $codestcla = '', $codestpro1= '', $codestpro2 = '', $codestpro3 = '', $codestpro4 = '') {
		$this->conexionbd = ConexionBaseDatos::getInstanciaConexion();
		//$this->conexionbd->debug = true; 
		$codestpro = '';
		$tabla = '';
		$campoSel = '';
		$filtroPadre = '';
		switch ($nivel) {
			case 0:
				$tabla = 'spg_ep1';
				$campoSel = 'codestpro1';
				$filtroPadre = " AND estcla <> '-' ";
				break;
				
			case 1:
				$tabla = 'spg_ep1';
				$campoSel = 'codestpro1';
				$filtroPadre = " AND estcla = '{$codestcla}' AND codestpro1 <> '-------------------------' ";
				break;
		
			case 2:
				$tabla = 'spg_ep2';
				$campoSel = 'codestpro2';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}' 
				                 AND estcla = '{$codestcla}'";
				break;
		
			case 3:
				$tabla = 'spg_ep3';
				$campoSel = 'codestpro3';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}'
								 AND estcla = '{$codestcla}'
								 AND  codestpro2 = '{$codestpro2}'";
				break;
		
				case 4:
				$tabla = 'spg_ep4';
				$campoSel = 'codestpro4';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}'
								 AND estcla = '{$codestcla}'
								 AND  codestpro2 = '{$codestpro2}'
								 AND  codestpro3 = '{$codestpro3}'";
				break;
		
				case 5:
				$tabla = 'spg_ep5';
				$campoSel = 'codestpro5';
				$filtroPadre = " AND codestpro1 = '{$codestpro1}'
								 AND estcla = '{$codestcla}'
								 AND  codestpro2 = '{$codestpro2}'
								 AND  codestpro3 = '{$codestpro3}'
								 AND  codestpro4 = '{$codestpro4}'	";
				break;
		}
		
		if ($estcla) {
			$cadenaSQL = "SELECT {$maxmin}(estcla) AS estcla
							FROM {$tabla}
							WHERE codemp = '{$_SESSION['la_empresa']['codemp']}'
							{$filtroPadre}  LIMIT 1";
		}
		else {
			$cadenaSQL = "SELECT {$maxmin}({$campoSel}) AS {$campoSel}
							FROM {$tabla}
							WHERE codemp = '{$_SESSION['la_empresa']['codemp']}'
							{$filtroPadre} LIMIT 1";
		}
		
	 	$dataSet = $this->conexionbd->Execute($cadenaSQL);
		if (!$dataSet->EOF) {
			if ($estcla) {
				$codestpro = $dataSet->fields["estcla"];
			}
			else {
				$codestpro = $dataSet->fields[$campoSel];
			}
			
		}
		 
		return $codestpro;
	}
}