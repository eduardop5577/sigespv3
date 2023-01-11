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

class ServicioAcumuladoCuenta
{
	private $conexionbd;
	
	public function __construct() 
	{
		$this->conexionbd  = null;
		$this->conexionbd = ConexionBaseDatos::getInstanciaConexion();
	}
	
	function filtroSeguridadProgramatica($as_tabla)
	{
		$ls_gestor    = $_SESSION["ls_gestor"];
		$li_estmodest = $_SESSION["la_empresa"]["estmodest"];
		$ls_usuario   = $_SESSION["la_logusr"];
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		if ((strtoupper($ls_gestor) == "MYSQLT") || (strtoupper($ls_gestor) == "MYSQLI"))
		{
			if ($li_estmodest == 2)
			{
				$as_filtro = " AND CONCAT('".$ls_codemp."','SPG','".$ls_usuario."',".$as_tabla.".codestpro1,".$as_tabla.".codestpro2,".$as_tabla.".codestpro3,".
				             "".$as_tabla.".codestpro4,".$as_tabla.".codestpro5,".$as_tabla.".estcla) IN (SELECT distinct CONCAT(codemp,codsis,codusu,codintper) ".
		                     "  FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
			}
			else
			{
				$as_filtro = " AND CONCAT('".$ls_codemp."','SPG','".$ls_usuario."',".$as_tabla.".codestpro1,".$as_tabla.".codestpro2,".$as_tabla.".codestpro3,".
							 "'00000000000000000000000000000000000000000000000000',".$as_tabla.".estcla) IN (SELECT distinct CONCAT(codemp,codsis,codusu,codintper) ".
		                     "  FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
			}				   
		}
		else
		{
			if ($li_estmodest == 2)
			{
				$as_filtro = " AND '".$ls_codemp."'||'SPG'||'".$ls_usuario."'||".$as_tabla.".codestpro1||".$as_tabla.".codestpro2||".$as_tabla.".codestpro3||".
							 "".$as_tabla.".codestpro4||".$as_tabla.".codestpro5||".$as_tabla.".estcla IN (SELECT distinct codemp||codsis||codusu||codintper ".
		                     "   FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
			}
			else
			{
				$as_filtro = " AND '".$ls_codemp."'||'SPG'||'".$ls_usuario."'||".$as_tabla.".codestpro1||".$as_tabla.".codestpro2||".$as_tabla.".codestpro3||".
							 "'00000000000000000000000000000000000000000000000000'||".$as_tabla.".estcla IN (SELECT distinct codemp||codsis||codusu||codintper".
		                     "   FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
			}						
		}
		return $as_filtro;	 
	}
	
	public function buscarCuentas($codestproD, $codestproH, $spg_cuentaD, $spg_cuentaH, $nivel, $filtroD='', $filtroH='')
	{
		$filtroSQL = '';
		if(!empty($codestproD) && !empty($codestproH))
		{
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				if($filtroD=='')
				{
					$filtroSQL = " AND (CONCAT(PCT.codestpro1,PCT.codestpro2,PCT.codestpro3,PCT.codestpro4,PCT.codestpro5,PCT.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroSQL = " AND (CONCAT({$filtroD}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				
				}
			}
			else
			{
				if($filtroH=='')
				{
					$filtroSQL = " AND ((PCT.codestpro1||PCT.codestpro2||PCT.codestpro3||PCT.codestpro4||PCT.codestpro5||PCT.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroSQL = " AND (CONCAT({$filtroH}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				
				}
			}
		}
		if(!empty($spg_cuentaD) && !empty($spg_cuentaH))
		{
			$filtroSQL .= " AND PCT.spg_cuenta BETWEEN '{$spg_cuentaD}' AND '{$spg_cuentaH}' ";
		}
		$filtoSSS = $this->filtroSeguridadProgramatica('PCT');
		
		$cadenaSQL = "SELECT DISTINCT PCT.spg_cuenta, PCT.nivel, MIN(PCT.denominacion) AS denominacion, PCT.status, SUM(PCT.asignado) as asignado ".
					 "  FROM spg_cuentas PCT ".
					 " WHERE PCT.codemp='{$_SESSION['la_empresa']['codemp']}' {$filtroSQL} ".
					 "   AND (PCT.nivel<='{$nivel}') {$filtoSSS} ".
					 " GROUP BY PCT.spg_cuenta, PCT.nivel,PCT.status ".
					 " ORDER BY PCT.spg_cuenta ";
		return $this->conexionbd->Execute($cadenaSQL);
	}
	
	function formatearCuenta($spg_cuenta)
	{
		$formato = $_SESSION['la_empresa']['formpre'];
		$arrFormato = explode('-', $formato);
		$numNiv     = count($arrFormato);
		$arrCuenta  = array();
		$posicion   = 0;
		for ($i = 0; $i < $numNiv; $i++)
		{
			$digitos = strlen($arrFormato[$i]);
			$arrCuenta[$i] = substr($spg_cuenta, $posicion, $digitos);
			$posicion = $posicion + $digitos;
		}
		return $arrCuenta;
	}
		
	function obtenerDigitoCuenta($spg_cuenta)
	{
		$arrCuenta = $this->formatearCuenta($spg_cuenta);
		$numNiv  = count($arrCuenta);
		$cuentaFiltro = '';
		for ($i = 0; $i < $numNiv; $i++)
		{
			if (($i+1) == ($numNiv))
			{
				if (intval($arrCuenta[$i])!=0)
				{
					$cuentaFiltro .= $arrCuenta[$i];
				}
			}
			else
			{
				if (intval($arrCuenta[$i])!=0)
				{
					$cuentaFiltro .= $arrCuenta[$i];
				}
				else
				{
					if (intval($arrCuenta[$i+1])!=0)
					{
						$cuentaFiltro .= $arrCuenta[$i];
					}
				}
			}
		}
		return $cuentaFiltro;
	}
	
	public function buscarSaldoCuenta($codestproD, $codestproH, $spg_cuenta, $nivel, $codfuefinD, $codfuefinH, $fecha, $filtroD='',$filtroH='', $status='')
	{
		$filtroSQL = '';
		if(!empty($codestproD) && !empty($codestproH))
		{
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				if($filtroD=='')
				{
					$filtroSQL = " AND programatica BETWEEN '{$codestproD}' AND '{$codestproH}' ";
				}
				else
				{
					$filtroD = str_replace("PCT.","",$filtroD);
					$filtroSQL = " AND (CONCAT({$filtroD}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
			}
			else
			{
				if($filtroH=='')
				{
					$filtroSQL = " AND programatica BETWEEN '{$codestproD}' AND '{$codestproH}' ";
				}
				else
				{
					$filtroH = str_replace("PCT.","",$filtroH);
					$filtroSQL = " AND (CONCAT({$filtroH}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
			}
		}
		if(!empty($codfuefinD) && !empty($codfuefinD))
		{
			$filtroSQL .= " AND codfuefin BETWEEN '{$codfuefinD}' AND '{$codfuefinH}' ";
		}
		$spg_cuenta=$this->obtenerDigitoCuenta($spg_cuenta);
                if($status=='C')
                {
                    $filtroSQL .= " AND nivel<=".$nivel." ";
                }
		$cadenaSQL = "SELECT SUM(aumento) as aumento, SUM(disminucion) as disminucion, SUM(precompromiso) as precompromiso, ".  
					 "		 SUM(compromiso) as compromiso, SUM(causado) as causado,  SUM(pagado) as pagado ". 
					 "  FROM detalle_acumulado ".
					 " WHERE spg_cuenta LIKE '{$spg_cuenta}%' AND fecha <= '{$fecha}' {$filtroSQL} ";
		return $this->conexionbd->Execute($cadenaSQL);
	}
	
	public function buscarSaldoCuentaEst($codestpro, $spg_cuenta, $nivel, $codfuefinD, $codfuefinH, $fecha)
	{
		$filtroSQL = '';
		if(!empty($codestpro))
		{
			$filtroSQL = " AND programatica = '{$codestpro}'";
		}
		if(!empty($codfuefinD) && !empty($codfuefinD))
		{
			$filtroSQL .= " AND codfuefin BETWEEN '{$codfuefinD}' AND '{$codfuefinH}' ";
		}
		$spg_cuenta=$this->obtenerDigitoCuenta($spg_cuenta);
		$cadenaSQL = "SELECT SUM(aumento) as aumento, SUM(disminucion) as disminucion, SUM(precompromiso) as precompromiso, ".
					 "		 SUM(compromiso) as compromiso, SUM(causado) as causado,  SUM(pagado) as pagado ".
					 "	FROM detalle_acumulado ".
					 " WHERE spg_cuenta LIKE '{$spg_cuenta}%' AND fecha <= '{$fecha}' {$filtroSQL} ";
		return $this->conexionbd->Execute($cadenaSQL);
	}
	
	public function buscarAsignadoFuente($codestproD, $codestproH, $spg_cuenta, $nivel, $codfuefinD, $codfuefinH, $filtroD='', $filtroH='')
	{
		$asignado  = 0;
		$filtroSQL = '';
		if(!empty($codestproD) && !empty($codestproH))
		{
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				if($filtroD=='')
				{
					$filtroSQL = " AND (CONCAT(PCT.codestpro1,PCT.codestpro2,PCT.codestpro3,PCT.codestpro4,PCT.codestpro5,PCT.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroSQL = " AND (CONCAT({$filtroD}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				
				}
			}
			else
			{
				if($filtroH=='')
				{
					$filtroSQL = " AND ((PCT.codestpro1||PCT.codestpro2||PCT.codestpro3||PCT.codestpro4||PCT.codestpro5||PCT.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroSQL = " AND (CONCAT({$filtroH}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				
				}
			}
		}
		
		$spg_cuenta = $this->obtenerDigitoCuenta($spg_cuenta);
		$cadenaSQL  = "SELECT SUM (monto) AS monto ".
  					  "  FROM spg_cuenta_fuentefinanciamiento PCT  ".
  					  "	WHERE PCT.codfuefin BETWEEN '{$codfuefinD}' AND '{$codfuefinH}' {$filtroSQL} ".
  					  "   AND PCT.spg_cuenta LIKE '{$spg_cuenta}%'";
		$dataAsig = $this->conexionbd->Execute($cadenaSQL);
		if (!$dataAsig->EOF)
		{
			$asignado = $dataAsig->fields['monto'];
		}
		unset($dataAsig);
		return $asignado;
	}
	
	public function buscarAsignadoFuenteEst($codestpro, $spg_cuenta, $nivel, $codfuefinD, $codfuefinH)
	{
		$asignado  = 0;
		$filtroSQL = '';
		if(!empty($codestpro))
		{
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				$filtroSQL = " AND (CONCAT(CF.codestpro1,CF.codestpro2,CF.codestpro3,CF.codestpro4,CF.codestpro5,CF.estcla) = '{$codestpro}') ";
			}
			else
			{
				$filtroSQL = " AND ((CF.codestpro1||CF.codestpro2||CF.codestpro3||CF.codestpro4||CF.codestpro5||CF.estcla) = '{$codestpro}') ";
			}
		}
		$spg_cuenta = $this->obtenerDigitoCuenta($spg_cuenta);
		$cadenaSQL  = "SELECT SUM (monto) AS monto ".
					  "	 FROM spg_cuenta_fuentefinanciamiento CF ".
					  "	WHERE CF.codfuefin BETWEEN '{$codfuefinD}' AND '{$codfuefinH}' {$filtroSQL} ".
					  "	  AND CF.spg_cuenta LIKE '{$spg_cuenta}%'";
		$dataAsig = $this->conexionbd->Execute($cadenaSQL);
		if (!$dataAsig->EOF)
		{
			$asignado = $dataAsig->fields['monto'];
		}
		unset($dataAsig);
		return $asignado;
	}
	
	public function buscarEstructuraCuenta($codestproD, $codestproH, $spg_cuentaD, $spg_cuentaH, $nivel, $filtroD='', $filtroH='')
	{
		$filtroSQL = '';
		if(!empty($codestproD) && !empty($codestproH))
		{
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				if($filtroD=='')
				{
					$filtroSQL = " AND (CONCAT(spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5,spg_cuentas.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroD = str_replace("PCT.","spg_cuentas.",$filtroD);
					$filtroSQL = " AND (CONCAT({$filtroD}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
			}
			else
			{
				if($filtroH=='')
				{
					$filtroSQL = " AND ((spg_cuentas.codestpro1||spg_cuentas.codestpro2||spg_cuentas.codestpro3||spg_cuentas.codestpro4||spg_cuentas.codestpro5||spg_cuentas.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroH = str_replace("PCT.","spg_cuentas.",$filtroH);
					$filtroSQL = " AND (CONCAT({$filtroH}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
			}				
		}
		if(!empty($spg_cuentaD) && !empty($spg_cuentaH))
		{
			$filtroSQL .= " AND spg_cuentas.spg_cuenta BETWEEN '{$spg_cuentaD}' AND '{$spg_cuentaH}' ";
		}
		$filtoSSS = $this->filtroSeguridadProgramatica('spg_cuentas');
		$cadenaSQL = "SELECT spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, spg_cuentas.codestpro4, spg_cuentas.codestpro5, ".
					 "		 spg_cuentas.estcla, spg_ep1.denestpro1, spg_ep2.denestpro2, spg_ep3.denestpro3, spg_ep4.denestpro4, spg_ep5.denestpro5, ".
					 "		 spg_cuentas.spg_cuenta, spg_cuentas.denominacion, spg_cuentas.nivel, spg_cuentas.status, ". 
					 "		 SUM(spg_cuentas.asignado) AS asignado ".
					 "	FROM spg_cuentas ".
					 "		INNER JOIN spg_ep1 ".
					 "		   ON spg_cuentas.codemp=spg_ep1.codemp AND spg_cuentas.codestpro1=spg_ep1.codestpro1 ".
					 "		INNER JOIN spg_ep2 ".
					 "		   ON spg_cuentas.codemp=spg_ep2.codemp AND spg_cuentas.codestpro1=spg_ep2.codestpro1 ".
					 "		  AND spg_cuentas.codestpro2=spg_ep2.codestpro2 ".
					 "		INNER JOIN spg_ep3 ".
					 "		   ON spg_cuentas.codemp=spg_ep3.codemp ".
					 "		  AND spg_cuentas.codestpro1=spg_ep3.codestpro1 ". 
					 "		  AND spg_cuentas.codestpro2=spg_ep3.codestpro2 ".
					 "		  AND spg_cuentas.codestpro3=spg_ep3.codestpro3 ".
					 "		INNER JOIN spg_ep4 ". 
					 "		   ON spg_cuentas.codemp=spg_ep4.codemp ".
					 "		  AND spg_cuentas.codestpro1=spg_ep4.codestpro1 ". 
					 "		  AND spg_cuentas.codestpro2=spg_ep4.codestpro2 ".
					 "		  AND spg_cuentas.codestpro3=spg_ep4.codestpro3 ". 
					 "		  AND spg_cuentas.codestpro4=spg_ep4.codestpro4 ". 
					 "		INNER JOIN spg_ep5 ".
					 "		   ON spg_cuentas.codemp=spg_ep5.codemp ".
					 "		  AND spg_cuentas.codestpro1=spg_ep5.codestpro1 ".
					 "		  AND spg_cuentas.codestpro2=spg_ep5.codestpro2 ".
					 "		  AND spg_cuentas.codestpro3=spg_ep5.codestpro3 ".
					 "		  AND spg_cuentas.codestpro4=spg_ep5.codestpro4 ".
					 "		  AND spg_cuentas.codestpro5=spg_ep5.codestpro5 ".
					 "	WHERE spg_cuentas.codemp='{$_SESSION['la_empresa']['codemp']}' AND spg_cuentas.nivel<='{$nivel}' {$filtroSQL} {$filtoSSS} ".
					 "	GROUP BY spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5, ".
					 "			 spg_cuentas.estcla,spg_ep1.denestpro1,spg_ep2.denestpro2,spg_ep3.denestpro3,spg_ep4.denestpro4,spg_ep5.denestpro5, ".
					 "			 spg_cuentas.spg_cuenta,spg_cuentas.denominacion,spg_cuentas.nivel,spg_cuentas.status ".
					 "	ORDER BY spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5, ".
					 "			 spg_cuentas.estcla,spg_ep1.denestpro1,spg_ep2.denestpro2,spg_ep3.denestpro3,spg_ep4.denestpro4,spg_ep5.denestpro5, ".
					 "			 spg_cuentas.spg_cuenta";
		return $this->conexionbd->Execute($cadenaSQL);
	}

	public function buscarNombreEstructura($codestproD, $codestproH, $nivel, $filtroD='', $filtroH='')
	{
		$filtroSQL = '';
		if(!empty($codestproD) && !empty($codestproH))
		{
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				if($filtroD=='')
				{
					$filtroSQL = " AND (CONCAT(spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5,spg_cuentas.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroD = str_replace("PCT.","spg_cuentas.",$filtroD);
					$filtroSQL = " AND (CONCAT({$filtroD}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
			}
			else
			{
				if($filtroH=='')
				{
					$filtroSQL = " AND ((spg_cuentas.codestpro1||spg_cuentas.codestpro2||spg_cuentas.codestpro3||spg_cuentas.codestpro4||spg_cuentas.codestpro5||spg_cuentas.estcla) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
				else
				{
					$filtroH = str_replace("PCT.","spg_cuentas.",$filtroH);
					$filtroSQL = " AND (CONCAT({$filtroH}) BETWEEN '{$codestproD}' AND '{$codestproH}') ";
				}
			}
		}
		$filtoSSS = $this->filtroSeguridadProgramatica('spg_cuentas');
		$cadenaSQL = "SELECT spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5,".
					 "       MAX(spg_ep1.denestpro1) AS denestpro1, MAX(spg_ep2.denestpro2) AS denestpro2, MAX(spg_ep3.denestpro3) AS denestpro3, ".
		             " 		 MAX(spg_ep4.denestpro4) AS denestpro4, MAX(spg_ep5.denestpro5) AS denestpro5 ".
					 "	FROM spg_cuentas ".
					 "		INNER JOIN spg_ep1 ".
					 "		   ON spg_cuentas.codemp=spg_ep1.codemp ".
					 "        AND spg_cuentas.codestpro1=spg_ep1.codestpro1 ".
					 "		INNER JOIN spg_ep2 ".
					 "		   ON spg_cuentas.codemp=spg_ep2.codemp ".
					 "        AND spg_cuentas.codestpro1=spg_ep2.codestpro1 ".
					 "		  AND spg_cuentas.codestpro2=spg_ep2.codestpro2 ".
					 "		INNER JOIN spg_ep3 ".
					 "		   ON spg_cuentas.codemp=spg_ep3.codemp ".
					 "		  AND spg_cuentas.codestpro1=spg_ep3.codestpro1 ". 
					 "		  AND spg_cuentas.codestpro2=spg_ep3.codestpro2 ".
					 "		  AND spg_cuentas.codestpro3=spg_ep3.codestpro3 ".
					 "		INNER JOIN spg_ep4 ". 
					 "		   ON spg_cuentas.codemp=spg_ep4.codemp ".
					 "		  AND spg_cuentas.codestpro1=spg_ep4.codestpro1 ". 
					 "		  AND spg_cuentas.codestpro2=spg_ep4.codestpro2 ".
					 "		  AND spg_cuentas.codestpro3=spg_ep4.codestpro3 ". 
					 "		  AND spg_cuentas.codestpro4=spg_ep4.codestpro4 ". 
					 "		INNER JOIN spg_ep5 ".
					 "		   ON spg_cuentas.codemp=spg_ep5.codemp ".
					 "		  AND spg_cuentas.codestpro1=spg_ep5.codestpro1 ".
					 "		  AND spg_cuentas.codestpro2=spg_ep5.codestpro2 ".
					 "		  AND spg_cuentas.codestpro3=spg_ep5.codestpro3 ".
					 "		  AND spg_cuentas.codestpro4=spg_ep5.codestpro4 ".
					 "		  AND spg_cuentas.codestpro5=spg_ep5.codestpro5 ".
					 "	WHERE spg_cuentas.codemp='{$_SESSION['la_empresa']['codemp']}' ".
					 "	  AND spg_cuentas.nivel<='{$nivel}' ".
					 "	   {$filtroSQL} {$filtoSSS} ".
					 "	GROUP BY spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5, ".
					 " 	 	     spg_cuentas.estcla ".
					 "	ORDER BY spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5, ".
					 "			 spg_cuentas.estcla ";
		return $this->conexionbd->Execute($cadenaSQL);
	}
}