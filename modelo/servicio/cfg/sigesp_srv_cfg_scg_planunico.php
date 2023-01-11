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

class servicioPlanUnico extends DaoGenerico
{
	
	public function __construct()
	{
		parent::__construct ( 'sigesp_plan_unico_re' );
	}
	
	public function LeerPorCadena($cr,$cad)
	{
		return $this->Find("{$cr} like  '%{$cad}%' ");
	}
	
	public function guardarCuentaRecursos($arrjson)
	{
		$this->setData($arrjson);
		$this->sig_cuenta = str_pad(trim($this->sig_cuenta),9,'0');
		$this->status = 'C';
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
	
	public function modificarCuentaRecursos($arrjson)
	{
		$this->setData($arrjson);
		$this->sig_cuenta = str_pad(trim($this->sig_cuenta),9,'0');
		$this->status = 'C';
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
	
	public function eliminarCuentaRecursos($codemp, $arrjson)
	{
		$respuesta = '1';
		DaoGenerico::iniciarTrans();
		if (!$this->validarRelacionCuenta($codemp, trim($arrjson->sig_cuenta)))
		{
			$this->setData($arrjson);
			if (!$this->eliminar())
			{
				$respuesta = '0';
			}
			if (DaoGenerico::completarTrans())
			{
				$respuesta = '1';
			}
			else
			{
				$respuesta = '0';
			}
		}
		else
		{ 
			$respuesta = '-9'; 
		}
		return $respuesta;
	}
	
	public function buscarCuentaRecursosEgresos($cuenta, $denominacion)
	{
		$cadenaSql = "SELECT sig_cuenta, denominacion ".
  					 "	FROM sigesp_plan_unico_re ".
  					 " WHERE sig_cuenta LIKE '{$cuenta}%' ".
  					 "   AND denominacion LIKE '%{$denominacion}%' ".
  					 " ORDER BY sig_cuenta";
		return $this->buscarSql($cadenaSql);
	}
	
	public function buscarCuentaDigitoEstatus($digito, $estatus)
	{
		$filEst = '';
		if($estatus!='')
		{
			$filEst = " AND status = '{$estatus}' ";
		}
		$cadenaSql = "SELECT trim(sigesp_plan_unico_re.sig_cuenta) AS sig_cuenta, MAX(sigesp_plan_unico_re.denominacion) AS denominacion, MAX(trim(int.sc_cuenta)) AS sc_cuenta , MAX(trim(int.cueclaeco)) AS cueclaeco ".
  					 "	FROM sigesp_plan_unico_re ".
  					 "  LEFT OUTER JOIN scg_casa_presu int ".
					 "    ON sigesp_plan_unico_re.sig_cuenta = int.sig_cuenta ".
  					 " WHERE sigesp_plan_unico_re.sig_cuenta LIKE '{$digito}%' {$filEst} ".
                                         " GROUP BY sigesp_plan_unico_re.sig_cuenta ".
  					 " ORDER BY sigesp_plan_unico_re.sig_cuenta";
		return $this->buscarSql($cadenaSql);
	}
	
	public function buscarCuentaPlaunicore($digito, $cuenta, $denominacion, $estatus)
	{
		$cadenaSql = "SELECT trim(plan.sig_cuenta) AS sig_cuenta, MAX(plan.denominacion) AS denominacion, MAX(trim(int.sc_cuenta)) AS sc_cuenta , MAX(trim(int.cueclaeco)) AS cueclaeco ".
  					 "	FROM sigesp_plan_unico_re plan ".
  					 "	LEFT OUTER JOIN scg_casa_presu int ON plan.sig_cuenta = int.sig_cuenta ".
  					 " WHERE plan.sig_cuenta LIKE '{$digito}%' ".
  					 "	 AND plan.sig_cuenta LIKE '{$cuenta}%' ".
  					 "	 AND plan.denominacion LIKE '%{$denominacion}%' ".
  					 "	 AND plan.status = '{$estatus}' ".
                                         " GROUP BY plan.sig_cuenta ".
  					 " ORDER BY plan.sig_cuenta";
		return $this->buscarSql($cadenaSql);
	}
	
	
	public function validarRelacionCuenta($codemp, $cuenta)
	{
		$tieneRelacion = false;
		
		$cadenaSQL = "SELECT sig_cuenta ".
					 "	FROM scg_casa_presu  ".
					 " WHERE codemp='{$codemp}' ".
					 "   AND sig_cuenta='{$cuenta}'";
		$data = $this->buscarSql($cadenaSQL);
		if($data->_numOfRows > 0)
		{
			$tieneRelacion = true;
		}
		else
		{
			if (substr($cuenta, 0, 1) == '4')
			{
				$cadenaSQL = "SELECT spg_cuenta ".
							 "  FROM spg_cuentas ".
							 " WHERE codemp='{$codemp}' ".
							 "	 AND spg_cuenta like '{$cuenta}%'";
			}
			else
			{
				$cadenaSQL = "SELECT spi_cuenta ".
							 "	FROM spi_cuentas ".
							 " WHERE codemp='{$codemp}' ".
							 "   AND spi_cuenta like '{$cuenta}%'";
			}
			$data = $this->buscarSql($cadenaSQL);
			if($data->_numOfRows > 0)
			{
				$tieneRelacion = true;
			}
		}
		return $tieneRelacion;
	}
}
?>