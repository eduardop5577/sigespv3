<?php
/***********************************************************************************
* @fecha de modificacion: 01/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once ("../../base/librerias/php/general/sigesp_lib_daogenerico.php");

class ServicioSpg
{
	private $daogenerico;
	
	public function __construct($tabla)
	{
		$this->daogenerico = new DaoGenerico ( $tabla );
	}
	
	public function getCodemp()
	{
		return $this->daogenerico->codemp;
	}
	
	public function setCodemp($codemp)
	{
		
		$this->daogenerico->codemp = $codemp;
	}
	
	/***********************************/
	/* Metodos Estandar DAO Generico   */
	/***********************************/
	
	public static function iniTransaccion()
	{
		DaoGenerico::iniciarTrans ();
	}
	
	public static function comTransaccion()
	{
		return DaoGenerico::completarTrans ();
	}
	
	public function incluirDto($dto)
	{
		$this->pasarDatos ( $dto );
		$this->daogenerico->incluir ();
	}
	
	public function modificarDto($dto)
	{
		$this->pasarDatos ( $dto );
		$this->daogenerico->modificar ();
	}
	
	public function eliminarDto($dto)
	{
		$this->pasarDatos ( $dto );
		$this->daogenerico->eliminar ();
	}
	
	function pasarDatos($ObJson)
	{
		$arratributos = $this->daogenerico->getAttributeNames();
		foreach ( $arratributos as $IndiceDAO )
		{
			foreach ( $ObJson as $IndiceJson => $valorJson )
			{
				if ($IndiceJson == $IndiceDAO && $IndiceJson != "codemp")
				{
					$this->daogenerico->$IndiceJson = utf8_decode ( $valorJson );
				}
				else
				{
					$GLOBALS [$IndiceJson] = $valorJson;
				}
			}
		}
	}
	
	public function buscarTodos()
	{
		return $this->daogenerico->leerTodos ();
	}
	
	public function buscarCampo($campo, $valor)
	{
		return $this->daogenerico->buscarCampo ( $campo, $valor );
	}
	
	public function buscarCampoRestriccion($restricciones)
	{
		return $this->daogenerico->buscarCampoRestriccion($restricciones) ;
	}
	
	public function buscarSql($cadenasql) 
	{
		return $this->daogenerico->buscarSql($cadenasql) ;
	}
	/***************************************/
	/* Fin Metodos Estandar DAO Generico   */
	/***************************************/
	
	/***************************************/
	/* Metodos Asociados al Servicio       */
	/***************************************/
	
	public function buscarEstructuraNivel1()
	{
		$filtro=$this->filtroSeguridad("spg_ep1","1");
		$cadenasql= "SELECT codestpro1,denestpro1,estcla,'0' AS central ".
		 			"  FROM spg_ep1 ".
					" WHERE codemp='".$this->daogenerico->codemp."' ".
					"    AND ".$filtro.
					" ORDER BY codestpro1";
		return $this->daogenerico->buscarSql($cadenasql);
	}
	
	public function buscarEstructuraNivel2($codest1)
	{
		$filtro=$this->filtroSeguridad("spg_ep2","2");
		$cadenasql= "SELECT codestpro1,codestpro2,denestpro2,'0' AS central ".
		 			"  FROM spg_ep2 ".
					" WHERE codemp='".$this->daogenerico->codemp."' ".
					"   AND codestpro1='".$codest1."' ".
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2";
		return $this->daogenerico->buscarSql($cadenasql);
	}
	
	public function buscarEstructuraNivel3($codest1,$codest2)
	{
		$filtro=$this->filtroSeguridad("spg_ep3","3");
		$cadenasql= "SELECT codestpro1,codestpro2,codestpro3,denestpro3,'0' AS central ".
		 			"  FROM spg_ep3 ".
					" WHERE codemp='".$this->daogenerico->codemp."' ".
					"   AND codestpro1='".$codest1."' ".
					"   AND codestpro2='".$codest2."' ".
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2,codestpro3";
		return $this->daogenerico->buscarSql($cadenasql);
	}
	
	public function buscarEstructuraNivel4($codest1,$codest2,$codest3)
	{
		$filtro=$this->filtroSeguridad("spg_ep4","4");
		$cadenasql= "SELECT codestpro1,codestpro2,codestpro3,codestpro4,denestpro4,'0' AS central ".
		 			"  FROM spg_ep4 ".
					"WHERE codemp='".$this->daogenerico->codemp."' ".
					"  AND codestpro1='".$codest1."' ".
					"  AND codestpro2='".$codest2."' ".
					"  AND codestpro3='".$codest3."' ".
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2,codestpro3,codestpro4";
		return $this->daogenerico->buscarSql($cadenasql);
	}
	
	public function buscarEstructuraNivel5($codest1,$codest2,$codest3,$codest4)
	{
		$filtro=$this->filtroSeguridad("spg_ep5","4");
		$cadenasql= "SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,denestpro5,'0' AS central ".
		 			"  FROM spg_ep5 ".
					" WHERE codemp='".$this->daogenerico->codemp."' ".
					"   AND codestpro1='".$codest1."' ".
					"   AND codestpro2='".$codest2."' ".
					"   AND codestpro3='".$codest3."' ".
					"   AND codestpro4='".$codest4."' ". 
					"   AND ".$filtro.
					" ORDER BY codestpro1,codestpro2,codestpro3,codestpro4,codestpro5";
		return $this->daogenerico->buscarSql($cadenasql);
	}
	
	public function buscarEstructuraNivelN($cantnivel, $sess_empresa)
	{
		switch ($cantnivel)
		{
			case "1":
				$filtro=$this->filtroSeguridad("spg_ep1","1");
				$cadenasql= "SELECT SUBSTR(codestpro1,length(codestpro1)-({$sess_empresa["loncodestpro1"]}-1)) AS codestpro1, ".
							"		denestpro1, estcla,'0' AS central  ".
							"  FROM spg_ep1  ".
							" WHERE codemp='".$this->daogenerico->codemp."'  ".
							"   AND ".$filtro.
							" ORDER BY codestpro1";
				break;
			
			case "2":
				$filtro=$this->filtroSeguridad("spg_ep2","2");
				$cadenasql= "SELECT SUBSTR(spg_ep2.codestpro1,length(spg_ep2.codestpro1)-({$sess_empresa["loncodestpro1"]}-1)) AS codestpro1,".
							"		SUBSTR(spg_ep2.codestpro2,length(spg_ep2.codestpro2)-({$sess_empresa["loncodestpro2"]}-1)) AS codestpro2, ".
							"		spg_ep1.denestpro1,	spg_ep2.denestpro2,	spg_ep2.estcla,'0' AS central ".
							"  FROM spg_ep1,spg_ep2 ".
							" WHERE spg_ep1.codemp = '".$this->daogenerico->codemp."' ".
  							"   AND spg_ep1.codemp = spg_ep2.codemp ".
  							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
  							"   AND spg_ep1.estcla = spg_ep2.estcla ".
							"   AND ".$filtro.
  							" ORDER BY spg_ep2.codestpro1,spg_ep2.codestpro2";
				break;
				
			case "3":
				$filtro=$this->filtroSeguridad("spg_ep3","3");
				$cadenasql= "SELECT SUBSTR(spg_ep3.codestpro1,length(spg_ep3.codestpro1)-({$sess_empresa["loncodestpro1"]}-1)) AS codestpro1,".
							"		SUBSTR(spg_ep3.codestpro2,length(spg_ep3.codestpro2)-({$sess_empresa["loncodestpro2"]}-1)) AS codestpro2,".
							"		SUBSTR(spg_ep3.codestpro3,length(spg_ep3.codestpro3)-({$sess_empresa["loncodestpro3"]}-1)) AS codestpro3,".
							"		spg_ep1.denestpro1,spg_ep2.denestpro2,spg_ep3.denestpro3,spg_ep3.estcla,'0' AS central ".
							"  FROM spg_ep1,spg_ep2,spg_ep3 ".
							" WHERE spg_ep1.codemp = '".$this->daogenerico->codemp."' ".
							"   AND spg_ep1.codemp = spg_ep2.codemp ".
							"   AND spg_ep1.estcla = spg_ep2.estcla  ".
							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
							"   AND spg_ep2.codemp = spg_ep3.codemp  ".
							"   AND spg_ep2.estcla = spg_ep3.estcla  ".
							"   AND spg_ep2.codestpro1 = spg_ep3.codestpro1 ".
							"   AND spg_ep2.codestpro2 = spg_ep3.codestpro2  ".
							"   AND ".$filtro.
							" ORDER BY spg_ep3.codestpro1,spg_ep3.codestpro2,spg_ep3.codestpro3";
				break;
			case "4":
				$filtro=$this->filtroSeguridad("spg_ep4","4");
				$cadenasql= "SELECT SUBSTR(spg_ep4.codestpro1,length(spg_ep4.codestpro1)-({$sess_empresa["loncodestpro1"]}-1)) AS codestpro1,".
							"		SUBSTR(spg_ep4.codestpro2,length(spg_ep4.codestpro2)-({$sess_empresa["loncodestpro2"]}-1)) AS codestpro2,". 
  							"		SUBSTR(spg_ep4.codestpro3,length(spg_ep4.codestpro3)-({$sess_empresa["loncodestpro3"]}-1)) AS codestpro3,".
  							"		SUBSTR(spg_ep4.codestpro4,length(spg_ep4.codestpro4)-({$sess_empresa["loncodestpro4"]}-1)) AS codestpro4,".
  							"		spg_ep1.denestpro1,spg_ep3.denestpro3,spg_ep2.denestpro2, spg_ep4.denestpro4,spg_ep4.estcla,'0' AS central".
  							"  FROM spg_ep1,spg_ep2,spg_ep3,spg_ep4".
							" WHERE spg_ep1.codemp = '".$this->daogenerico->codemp."' ".
							"   AND spg_ep1.codemp = spg_ep2.codemp ".
							"   AND spg_ep1.estcla = spg_ep2.estcla ".
							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
							"   AND spg_ep2.codemp = spg_ep3.codemp ".
							"   AND spg_ep2.estcla = spg_ep3.estcla ".
							"   AND spg_ep2.codestpro1 = spg_ep3.codestpro1 ".
							"   AND spg_ep2.codestpro2 = spg_ep3.codestpro2 ".
							"   AND spg_ep3.codemp = spg_ep4.codemp ".
							"   AND spg_ep3.estcla = spg_ep4.estcla ".
							"   AND spg_ep3.codestpro1 = spg_ep4.codestpro1 ".
							"   AND spg_ep3.codestpro2 = spg_ep4.codestpro2 ".
							"   AND spg_ep3.codestpro3 = spg_ep4.codestpro3 ".
							"   AND ".$filtro.
							" ORDER BY spg_ep4.codestpro1,spg_ep4.codestpro2,spg_ep4.codestpro3,spg_ep4.codestpro4";
				break;
			case "5":
				$filtro=$this->filtroSeguridad("spg_ep5","5");
				$cadenasql= "SELECT SUBSTR(spg_ep5.codestpro1,length(spg_ep5.codestpro1)-({$sess_empresa["loncodestpro1"]}-1)) AS codestpro1,".
							"		SUBSTR(spg_ep5.codestpro2,length(spg_ep5.codestpro2)-({$sess_empresa["loncodestpro2"]}-1)) AS codestpro2,". 
  							"		SUBSTR(spg_ep5.codestpro3,length(spg_ep5.codestpro3)-({$sess_empresa["loncodestpro3"]}-1)) AS codestpro3,".
  							"		SUBSTR(spg_ep5.codestpro4,length(spg_ep5.codestpro4)-({$sess_empresa["loncodestpro4"]}-1)) AS codestpro4,".
  							"		SUBSTR(spg_ep5.codestpro5,length(spg_ep5.codestpro5)-({$sess_empresa["loncodestpro5"]}-1)) AS codestpro5,". 
  							"		spg_ep1.denestpro1,spg_ep2.denestpro2,spg_ep3.denestpro3,spg_ep4.denestpro4,spg_ep5.denestpro5,spg_ep5.estcla,'0' AS central".
							"  FROM spg_ep1,spg_ep2,spg_ep3,spg_ep4,spg_ep5".
							" WHERE spg_ep1.codemp = '".$this->daogenerico->codemp."' ".
							"   AND spg_ep1.codemp = spg_ep2.codemp ". 
							"   AND spg_ep1.estcla = spg_ep2.estcla  ".
							"   AND spg_ep1.codestpro1 = spg_ep2.codestpro1  ".
							"   AND spg_ep2.codemp = spg_ep3.codemp  ".
							"   AND spg_ep2.estcla = spg_ep3.estcla  ".
							"   AND spg_ep2.codestpro1 = spg_ep3.codestpro1  ".
							"   AND spg_ep2.codestpro2 = spg_ep3.codestpro2  ".
							"   AND spg_ep3.codemp = spg_ep4.codemp  ".
							"   AND spg_ep3.estcla = spg_ep4.estcla  ".
							"   AND spg_ep3.codestpro1 = spg_ep4.codestpro1  ".
							"   AND spg_ep3.codestpro2 = spg_ep4.codestpro2  ".
							"   AND spg_ep3.codestpro3 = spg_ep4.codestpro3  ".
							"   AND spg_ep4.codemp = spg_ep5.codemp  ".
							"   AND spg_ep4.estcla = spg_ep5.estcla  ".
							"   AND spg_ep4.codestpro1 = spg_ep5.codestpro1  ".
							"   AND spg_ep4.codestpro2 = spg_ep5.codestpro2  ".
							"   AND spg_ep4.codestpro3 = spg_ep5.codestpro3  ".
							"   AND spg_ep4.codestpro4 = spg_ep5.codestpro4 ".
							"   AND ".$filtro.
							" ORDER BY spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5";
				break;
			
		}
		return $this->daogenerico->buscarSql($cadenasql);
		
	}
	
	public function buscarCuentas($codest1,$codest2,$codest3,$codest4,$codest5,$estcla)
	{
		$cadenasql= "SELECT spg_cuentas.spg_cuenta as sig_cuenta,spg_cuentas.denominacion, spg_cuentas.sc_cuenta".
					"  FROM spg_cuentas".
					" WHERE spg_cuentas.codemp ='".$this->daogenerico->codemp."'  ".
					"   AND spg_cuentas.codestpro1 ='".$codest1."'  ".
					"   AND spg_cuentas.codestpro2 ='".$codest2."' ".
					"	AND spg_cuentas.codestpro3 ='".$codest3."' ".
					"	AND spg_cuentas.codestpro4 ='".$codest4."' ".
					"	AND spg_cuentas.codestpro5 ='".$codest5."'  ".
					"	AND spg_cuentas.estcla ='".$estcla."' ".
					"	AND spg_cuentas.status ='C' ".   
					" ORDER BY  spg_cuentas.spg_cuenta ASC";
		return $this->daogenerico->buscarSql($cadenasql);
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
	/***************************************/
	/* Fin Metodos Asociados al Servicio   */
	/***************************************/	
}

?>