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

session_start();
$dirsrvcfg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrvcfg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirsrvcfg."/modelo/servicio/spg/sigesp_srv_spg_icomfsestructurafuentecuenta.php");

class ServicioComEstructuraFuenteCuenta implements IComEstructuraFuenteCuenta
{
	private $conexionBaseDatos;
	
	public function __construct() 
	{
		$this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();
	}
	
	public function buscarSpgEp1($codemp)
	{
		$filtro=$this->filtroSeguridad("spg_ep1","1");
		$cadenaSQL= "SELECT codestpro1,denestpro1,estcla,'0' AS central ".
		 			"  FROM spg_ep1 ".
					" WHERE codemp='".$codemp."' ".
					"   AND ".$filtro.
					" ORDER BY codestpro1";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarSpgEp2($codemp, $codest1)
	{
		$filtro=$this->filtroSeguridad("spg_ep2","2");
		$filtroestructura='';
		if ((trim($codest1)<>'')&&(trim($codest1)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro1='".$codest1."' ";
		}
		$cadenaSQL= "SELECT codestpro1,codestpro2,denestpro2,'0' AS central ".
		 			"  FROM spg_ep2 ".
					" WHERE codemp='".$codemp."' ".
					$filtroestructura.
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarSpgEp3($codemp, $codest1, $codest2)
	{
		$filtro=$this->filtroSeguridad("spg_ep3","3");
		$filtroestructura='';
		if ((trim($codest1)<>'')&&(trim($codest1)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro1='".$codest1."' ";
		}
		if ((trim($codest2)<>'')&&(trim($codest2)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro2='".$codest2."' ";
		}
		$cadenaSQL= "SELECT codestpro1,codestpro2,codestpro3,denestpro3,'0' AS central ".
		 			"  FROM spg_ep3 ".
					" WHERE codemp='".$codemp."' ".
					$filtroestructura.
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2,codestpro3";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarSpgEp4($codemp, $codest1, $codest2, $codest3)
	{
		$filtro=$this->filtroSeguridad("spg_ep4","4");
		$filtroestructura='';
		if ((trim($codest1)<>'')&&(trim($codest1)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro1='".$codest1."' ";
		}
		if ((trim($codest2)<>'')&&(trim($codest2)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro2='".$codest2."' ";
		}
		if ((trim($codest3)<>'')&&(trim($codest3)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro3='".$codest3."' ";
		}
		$cadenaSQL= "SELECT codestpro1,codestpro2,codestpro3,codestpro4,denestpro4,'0' AS central ".
		 			"  FROM spg_ep4 ".
					" WHERE codemp='".$codemp."' ".
					$filtroestructura.
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2,codestpro3,codestpro4";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarSpgEp5($codemp, $codest1, $codest2, $codest3, $codest4)
	{
		$filtro=$this->filtroSeguridad("spg_ep5","4");
		$filtroestructura='';
		if ((trim($codest1)<>'')&&(trim($codest1)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro1='".$codest1."' ";
		}
		if ((trim($codest2)<>'')&&(trim($codest2)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro2='".$codest2."' ";
		}
		if ((trim($codest3)<>'')&&(trim($codest3)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro3='".$codest3."' ";
		}
		if ((trim($codest4)<>'')&&(trim($codest4)<>'0000000000000000000000000'))
		{
			$filtroestructura .="   AND codestpro4='".$codest4."' ";
		}
		$cadenaSQL= "SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,denestpro5,'0' AS central ".
		 			"  FROM spg_ep5 ".
					" WHERE codemp='".$codemp."' ".
					$filtroestructura.
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarSpgEpN($cantnivel, $codemp) 
	{
		switch ($cantnivel)
		{
			case "1":
				$filtro=$this->filtroSeguridad("spg_ep1","1");
				$cadenaSQL= "SELECT codestpro1, denestpro1, estcla".
							"  FROM spg_ep1 ".
							" WHERE codemp='{$codemp}' ".
							"   AND ".$filtro.
							" ORDER BY codestpro1";
				break;
			
			case "2":
				$filtro=$this->filtroSeguridad("spg_ep2","2");
				$cadenaSQL= "SELECT spg_ep2.codestpro1, spg_ep2.codestpro2, spg_ep1.denestpro1, spg_ep2.denestpro2, spg_ep2.estcla ".
							"  FROM spg_ep1 ".
							" INNER JOIN spg_ep2 ".
							"    ON spg_ep1.codemp = spg_ep2.codemp ".
							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
							"   AND spg_ep1.estcla = spg_ep2.estcla ".	 
							"   AND ".$filtro.
							" WHERE spg_ep1.codemp = '{$codemp}' ". 
							" ORDER BY spg_ep2.codestpro1,spg_ep2.codestpro2";
				break;
				
			case "3":
				$filtro=$this->filtroSeguridad("spg_ep3","3");
				$cadenaSQL= "SELECT spg_ep3.codestpro1, spg_ep3.codestpro2, spg_ep3.codestpro3, spg_ep1.denestpro1, spg_ep2.denestpro2,".
							"		spg_ep3.denestpro3, spg_ep3.estcla ".
							"	FROM spg_ep1 ". 
						 	"  INNER JOIN spg_ep2 ".
							"     ON spg_ep1.codemp = spg_ep2.codemp ".
							"    AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
							"	 AND spg_ep1.estcla = spg_ep2.estcla ".
							"  INNER JOIN spg_ep3  ".
							"     ON spg_ep2.codemp = spg_ep3.codemp  ".
							"	 AND spg_ep2.codestpro1 = spg_ep3.codestpro1 ".
							"	 AND spg_ep2.codestpro2 = spg_ep3.codestpro2 ". 
							"	 AND spg_ep2.estcla = spg_ep3.estcla ". 
							"   AND ".$filtro.
							"  WHERE spg_ep1.codemp = '{$codemp}' ". 
							"  ORDER BY spg_ep3.codestpro1,spg_ep3.codestpro2,spg_ep3.codestpro3";
				break;
			case "4":
				$filtro=$this->filtroSeguridad("spg_ep4","4");
				$cadenaSQL= "SELECT spg_ep4.codestpro1, spg_ep4.codestpro2, spg_ep4.codestpro3, spg_ep4.codestpro4, spg_ep1.denestpro1,".
  							"		spg_ep3.denestpro3, spg_ep2.denestpro2, spg_ep4.denestpro4, spg_ep4.estcla ".
  							"  FROM spg_ep1 ".
  							" INNER JOIN spg_ep2 ".
							"    ON spg_ep1.codemp = spg_ep2.codemp ".
							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
							"   AND spg_ep1.estcla = spg_ep2.estcla ".
							" INNER JOIN spg_ep3 ".
							"    ON spg_ep2.codemp = spg_ep3.codemp ".
							"   AND spg_ep2.codestpro1 = spg_ep3.codestpro1 ".
							"	AND spg_ep2.codestpro2 = spg_ep3.codestpro2 ". 
							"	AND spg_ep2.estcla = spg_ep3.estcla ".
							" INNER JOIN spg_ep4 ".
							"    ON spg_ep3.codemp = spg_ep4.codemp ".
							"   AND spg_ep3.codestpro1 = spg_ep4.codestpro1 ".
							"   AND spg_ep3.codestpro2 = spg_ep3.codestpro2 ". 
							"	AND spg_ep3.codestpro3 = spg_ep4.codestpro3 ".
							"	AND spg_ep3.estcla = spg_ep4.estcla ".
							"   AND ".$filtro.
							" WHERE spg_ep1.codemp = '{$codemp}' ". 
							" ORDER BY spg_ep4.codestpro1,spg_ep4.codestpro2,spg_ep4.codestpro3,spg_ep4.codestpro4";
				break;
			case "5":
				$filtro=$this->filtroSeguridad("spg_ep5","5");
				$cadenaSQL= "SELECT spg_ep5.codestpro1, spg_ep5.codestpro2, spg_ep5.codestpro3, spg_ep5.codestpro4, spg_ep5.codestpro5, spg_ep5.estcla, MAX(spg_ep1.denestpro1) AS denestpro1, ". 
				            "        MAX(spg_ep2.denestpro2) AS denestpro2, MAX(spg_ep3.denestpro3) AS denestpro3, MAX(spg_ep4.denestpro4) AS denestpro4, MAX(spg_ep5.denestpro5) AS denestpro5 ".
  							"  FROM spg_ep1 ".
  							" INNER JOIN spg_ep2 ".
							"    ON spg_ep1.codemp = spg_ep2.codemp ".
							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
							"   AND spg_ep1.estcla = spg_ep2.estcla ".
							" INNER JOIN spg_ep3 ".
							"    ON spg_ep2.codemp = spg_ep3.codemp ".
							"   AND spg_ep2.codestpro1 = spg_ep3.codestpro1 ".
							"	AND spg_ep2.codestpro2 = spg_ep3.codestpro2 ". 
							"	AND spg_ep2.estcla = spg_ep3.estcla ".
							" INNER JOIN spg_ep4 ".
							"    ON spg_ep3.codemp = spg_ep4.codemp ".
							"   AND spg_ep3.codestpro1 = spg_ep4.codestpro1 ".
							"   AND spg_ep3.codestpro2 = spg_ep3.codestpro2 ". 
							"	AND spg_ep3.codestpro3 = spg_ep4.codestpro3 ".
							"	AND spg_ep3.estcla = spg_ep4.estcla ".
							" INNER JOIN spg_ep5 ".
							"    ON spg_ep4.codemp = spg_ep5.codemp ".
							"   AND spg_ep4.codestpro1 = spg_ep5.codestpro1 ".
							"   AND spg_ep4.codestpro2 = spg_ep5.codestpro2 ".
							"   AND spg_ep4.codestpro3 = spg_ep5.codestpro3 ".
							"   AND spg_ep4.codestpro4 = spg_ep5.codestpro4 ".
							"   AND spg_ep4.estcla = spg_ep5.estcla ".
							"   AND ".$filtro.
							" WHERE spg_ep1.codemp = '{$codemp}' ".
							" GROUP BY spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5, spg_ep5.estcla ".
							" ORDER BY spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5";
				break;
			
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarFuentes($codemp, $codest1, $codest2, $codest3, $codest4, $codest5, $estcla)
	{
		if ($codest1 == "0000000000000000000000000")
		{
			$cadenaSQL = "SELECT codfuefin, denfuefin ".
						 "  FROM sigesp_fuentefinanciamiento ".
						 " WHERE codemp='{$codemp}' ".
						 "   AND codfuefin<>'--'";
		}
		else
		{
			$cadenaSQL = "SELECT DT.codfuefin, FF.denfuefin ".
						 "  FROM spg_dt_fuentefinanciamiento DT ".
						 " INNER JOIN sigesp_fuentefinanciamiento FF ".
						 "    ON DT.codemp=FF.codemp ".
						 "   AND DT.codfuefin=FF.codfuefin ".
						 " WHERE DT.codemp='{$codemp}' ".
						 "   AND DT.codestpro1='{$codest1}' ".
						 "   AND DT.codestpro2='{$codest2}' ".
						 "   AND DT.codestpro3='{$codest3}' ".
						 "   AND DT.codestpro4='{$codest4}' ".
						 "   AND DT.codestpro5='{$codest5}' ".
						 "   AND DT.estcla='{$estcla}' ".
						 "   AND DT.codfuefin<>'--'";
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}
	
	public function buscarCuentas($codemp, $codest1, $codest2, $codest3, $codest4, $codest5, $estcla, $codfuefin, $codigo, $denominacion, $codcontable, $logusr, $grupo, $nofiltroest, $CuentaMovimiento)
	{
		$cadenaFiltro = "";
		$cadenaEstructura = "";
		if(!empty($codigo))
		{
			$cadenaFiltro .= "AND c.spg_cuenta like '{$codigo}%'"; 
		}
		if(!empty($denominacion))
		{
			$cadenaFiltro .= "AND c.denominacion like '%{$denominacion}%'";
		}
		if (!empty($codcontable))
		{
			$cadenaFiltro .= "AND c.sc_cuenta like '{$codcontable}%'";
		}
		if(!empty($grupo))
		{
			$cadenaFiltro .= "AND c.spg_cuenta like '{$grupo}%'";
		}
		if($CuentaMovimiento)
		{
			$cadenaFiltro .= " AND status='C' ";
		}
		$concatA = $this->conexionBaseDatos->Concat("'{$codemp}'","'SPG'","'{$logusr}'",'c.codestpro1','c.codestpro2','c.codestpro3','c.codestpro4','c.codestpro5','c.estcla');
		$concatB = $this->conexionBaseDatos->Concat('codemp','codsis','codusu','codintper');
		$cadenaSeguridad = " AND {$concatA} IN (SELECT distinct {$concatB} FROM sss_permisos_internos WHERE codusu = '{$logusr}' AND codsis = 'SPG' AND enabled=1) ";
		if ($codest1 == "0000000000000000000000000" || $nofiltroest == "1") 
		{
			if($codest1 <> "0000000000000000000000000")
			{
				$cadenaEstructura .= " AND c.codestpro1 = '{$codest1}' AND c.estcla='{$estcla}' ";
				if($codest2 <> "0000000000000000000000000")
				{
					$cadenaEstructura .= " AND c.codestpro2 = '{$codest2}' ";
				}
				if($codest3 <> "0000000000000000000000000")
				{
					$cadenaEstructura .= " AND c.codestpro3 = '{$codest3}' ";
				}
				if($codest4 <> "0000000000000000000000000")
				{
					$cadenaEstructura .= " AND c.codestpro4 = '{$codest4}' ";
				}
				if($codest5 <> "0000000000000000000000000")
				{
					$cadenaEstructura .= " AND c.codestpro5 = '{$codest5}' ";
				}
			}
			$cadenaSQL = "SELECT DISTINCT c.spg_cuenta, MAX(c.denominacion) AS denominacion, c.sc_cuenta, SUM((c.asignado-(c.comprometido+c.precomprometido)+c.aumento-c.disminucion)) as disponible ".
						 "  FROM  spg_cuentas c ".
						 " INNER JOIN spg_cuenta_fuentefinanciamiento f ".
						 "    ON c.codemp = f.codemp ".
						 "   AND c.codestpro1 = f.codestpro1 ".
						 "   AND c.codestpro2 = f.codestpro2 ".
						 "   AND c.codestpro3 = f.codestpro3 ".
						 "   AND c.codestpro4 = f.codestpro4 ".
						 "   AND c.codestpro5 = f.codestpro5 ".
						 "   AND c.estcla = f.estcla ".
						 "   AND c.spg_cuenta=f.spg_cuenta ".
						 " WHERE c.codemp = '{$codemp}' ".
						 " 	 AND f.codfuefin = '{$codfuefin}' {$cadenaEstructura} {$cadenaFiltro} {$cadenaSeguridad} ".
						 " GROUP BY c.spg_cuenta, c.sc_cuenta ".
						 " ORDER BY c.spg_cuenta";
		}
		else
		{
			$cadenaSQL = "SELECT DISTINCT c.spg_cuenta, MAX(c.denominacion) AS denominacion, c.sc_cuenta, SUM((c.asignado-(c.comprometido+c.precomprometido)+c.aumento-c.disminucion)) as disponible ".
						 "  FROM  spg_cuentas c ".
						 " INNER JOIN spg_cuenta_fuentefinanciamiento f ".
						 "    ON c.codemp = f.codemp ".
						 "   AND c.codestpro1 = f.codestpro1 ".
						 "   AND c.codestpro2 = f.codestpro2 ".
						 "   AND c.codestpro3 = f.codestpro3 ".
						 "   AND c.codestpro4 = f.codestpro4 ".
						 "   AND c.codestpro5 = f.codestpro5 ".
						 "   AND c.estcla = f.estcla ".
						 "   AND c.spg_cuenta=f.spg_cuenta ".
						 " WHERE c.codemp = '{$codemp}' ".
						 "   AND c.codestpro1 = '{$codest1}' ".
						 "   AND c.codestpro2 = '{$codest2}' ". 
						 "   AND c.codestpro3 = '{$codest3}' ".
						 "   AND c.codestpro4 = '{$codest4}' ".
						 "   AND c.codestpro5 = '{$codest5}' ".
						 " 	 AND c.estcla='{$estcla}' ".
						 "   AND f.codfuefin = '{$codfuefin}' ".
						 "		{$cadenaFiltro} {$cadenaSeguridad} ".
						 " GROUP BY c.spg_cuenta, c.sc_cuenta ".
						 " ORDER BY c.spg_cuenta";
		}
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

	public function filtroSeguridad($tabla,$nivel)
	{
		$gestor    = $_SESSION["ls_gestor"];
		$estmodest = $_SESSION["la_empresa"]["estmodest"];
		$usuario   = $_SESSION["la_logusr"];
		$codemp    = $_SESSION["la_empresa"]["codemp"];
		$filtro = "";
		$filtroa = "";
		$filtrob = "";
		$filtroc = "";
		if ((strtoupper($gestor)=="MYSQLT") || (strtoupper($gestor)=="MYSQLI"))
		{
			$filtroa = " CONCAT('".$codemp."','SPG','".$usuario."'";
			$filtrob = " IN (SELECT distinct CONCAT(codemp,codsis,codusu";
			$filtroc = " FROM sss_permisos_internos WHERE codemp = '".$codemp."' AND codusu = '".$usuario."' AND codsis = 'SPG' AND enabled=1)";
			switch ($nivel)
			{
				case "1":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,25),substr(codintper,126,1)) ";
				break;
				
				case "2":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,50),substr(codintper,126,1)) ";
				break;
				
				case "3":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".codestpro3,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,75),substr(codintper,126,1)) ";
				break;

				case "4":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".codestpro3,".$tabla.".codestpro4,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,100),substr(codintper,126,1)) ";
				break;

				case "5":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".codestpro3,".$tabla.".codestpro4,".$tabla.".codestpro5,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,125),substr(codintper,126,1)) ";
				break;
			}
		 }
		 else
		 {
			$filtroa = " '".$codemp."'||'SPG'||'".$usuario."'";
			$filtrob = " IN (SELECT distinct codemp||codsis||codusu";
			$filtroc = " FROM sss_permisos_internos WHERE codemp = '".$codemp."' AND codusu = '".$usuario."' AND codsis = 'SPG' AND enabled=1)";
			switch ($nivel)
			{
				case "1":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,25)||substr(codintper,126,1) ";
				break;
				
				case "2":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,50)||substr(codintper,126,1) ";
				break;
				
				case "3":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".codestpro3||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,75)||substr(codintper,126,1)  ";
				break;

				case "4":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".codestpro3||".$tabla.".codestpro4||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,100)||substr(codintper,126,1) ";
				break;

				case "5":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".codestpro3||".$tabla.".codestpro4||".$tabla.".codestpro5||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,125)||substr(codintper,126,1) ";
				break;
			}
		}
		$filtro = $filtroa.$filtrob.$filtroc;
		return $filtro;	 
	}
}