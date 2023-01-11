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

require_once ("../../base/librerias/php/general/sigesp_lib_daogenerico.php");

class servicioPlanCuentaPatrimonial extends DaoGenerico
{
	
	public function __construct()
	{
		parent::__construct ( 'sigesp_plan_unico' );
	}
	
	public function guardarCuenta($arrjson)
	{
		$this->setData($arrjson);
		DaoGenerico::iniciarTrans();
		if(!$this->incluir())
		{
			return false;
		}
		if (DaoGenerico::completarTrans())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function modificarCuenta($arrjson)
	{
		$this->setData($arrjson);
		DaoGenerico::iniciarTrans();
		if(!$this->modificar())
		{
			return false;
		}
		if (DaoGenerico::completarTrans())
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function eliminarCuenta($arrjson, $arrevento, $codemp, $formatoplan)
	{
		$respuesta = '1';
		$this->setData($arrjson);
		if($this->validarRelacionCuenta($codemp, $arrjson->sc_cuenta, $formatoplan))
		{
			$respuesta = '-9';
		}
		else
		{
			DaoGenerico::iniciarTrans();
			if (!$this->eliminar())
			{
				$resultado = '0';
			}
			if (DaoGenerico::completarTrans())
			{
				$respuesta = '1';
			}
			else
			{
				$respuesta = 0;
			}
		}
		return $respuesta;
	}
	
	public function validarRelacionCuenta($codemp, $cuenta, $formatoplan)
	{
		$valido   = false;
		$formato  = str_replace( "-", "",$formatoplan);
		$longitud = strlen($formato);
		
		$cadenaSql = "SELECT sc_cuenta ".
  					 "  FROM scg_cuentas ".
					 " WHERE codemp = '{$codemp}' ".
					 "   AND substr(sc_cuenta,1,{$longitud})='{$cuenta}'";
		$data = $this->buscarSql( $cadenaSql );
		if ($data->_numOfRows > 0)
		{
			$valido = true;
		}
		return $valido;		
	}
	
	public function buscarCuenta($cuenta, $denominacion)
	{
		$cadenaSql = "SELECT sc_cuenta, denominacion ".
  					 "	FROM sigesp_plan_unico ".
					 " WHERE sc_cuenta like '{$cuenta}%' ".
					 "   AND denominacion like '%{$denominacion}%' ".
					 " ORDER BY sc_cuenta";
		return $this->buscarSql( $cadenaSql );
	}
	
	public function leerTodosLocal($campoorden="",$tipoorden=0)
	{
		$cadena="";
		if($campoorden != "")
		{
			$cadena = " order by ".$campoorden;
			switch($tipoorden)
			{
				case 1: $cadena = $cadena." ASC";
						break;
						
				case 2: $cadena = $cadena." DESC";
						break;
						
				default: $cadena = $cadena." ASC";
			}
		}
		return $this->buscarSql("select * from {$this->_table} ".$cadena);
	}
}
?>