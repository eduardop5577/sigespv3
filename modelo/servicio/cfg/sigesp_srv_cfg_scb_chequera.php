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

require_once ('../../base/librerias/php/general/sigesp_lib_daogenerico.php');

class servicioChequera extends DaoGenerico
{
    public $codemp;
    public $codban;
    public $ctaban;
    public $numche;
    public $estche;
    public $numchequera;
    public $estins;
    public $orden;
    public $codusu;
    
    function __construct($table = false) {
        parent::__construct('scb_cheques');
    }
    
    public function verificarExistenciaChequera()
	{
		/*************************************************************************************************************
		 * Funcion: verificarExistenciaChequera
		 * Descripcion: Función que verifica la existencia del numero de chequera en ese Banco, 
		 *              para esa Cuenta.
		 *************************************************************************************************************/
	
		$existe = false;
		$cadenasql = "SELECT scb_cheques.numchequera ".
					 "  FROM scb_cheques ". 
					 " WHERE scb_cheques.codemp='".$this->codemp."'".
					 "   AND scb_cheques.codban='".$this->codban."'".
					 "   AND scb_cheques.ctaban='".$this->ctaban."'".
					 "   AND scb_cheques.numchequera='".$this->numchequera."'";
		
		$resultado = $this->buscarSql($cadenasql);
		
		if($resultado->RecordCount()>0)
		{
			$existe =  true;
		}
		return $existe;
	}//Function verificarExistenciaChequera.
	
	public function verificarExistenciaCheque($status)
	{
	 /*************************************************************************************************************
	 * Funcion: verificarExistenciaCheque
	 * Descripcion: Función que verifica la existencia del numero de cheque en ese Banco, 
	 *              para esa cuenta y chequera.
	 *************************************************************************************************************/
	  	$existe = false;
	  	$cadenasql = "SELECT scb_cheques.estche ".
	   	             "  FROM scb_cheques ".
				     " WHERE scb_cheques.codemp='".$this->codemp."'". 
				     "   AND scb_cheques.codban='".$this->codban."'".
				     " 	 AND scb_cheques.ctaban='".$this->ctaban."'". 
				     "	 AND scb_cheques.numche='".$this->cheque."'";
	  	$resultado = $this->buscarSql($cadenasql);
		
		if($resultado->RecordCount()>0)
		{
			$status = $resultado->fields['estche'];
			$existe= true;
		}
		else
		{
			$status = 0;
		}
		
		return $existe;
	}
	
	public function cargarUsuariosCheques()
	{
     /************************************************************************************************************
	 * Funcion: cargarUsuariosCheques
	 * Descripcion: Función que retorna los usuarios asociados a la chequera en ese Banco, 
	 *              cuenta y chequera.
	 *************************************************************************************************************/
		$cadenasql="SELECT scb_cheques.codusu  ".
				   "  FROM scb_cheques ".
				   " WHERE scb_cheques.codemp='".$this->codemp."'".
				   "   AND scb_cheques.codban='".$this->codban."'". 
				   "   AND scb_cheques.ctaban='".$this->ctaban."'". 
				   "   AND scb_cheques.numchequera='".$this->numchequera."'".
				   " GROUP BY scb_cheques.codusu";
	  	$resultado = $this->buscarSql($cadenasql);
		if($resultado->RecordCount()>0)
		{
			$criterio = '';
			while (!$resultado->EOF)
			{
				$codusu = $resultado->fields['codusu'];
				$codusu = str_replace("::","','",$codusu);
				$codusu = str_replace(":","'",$codusu);
				$criterio .= ','.$codusu;
				$resultado->MoveNext();
			}
			$cadenasql="SELECT sss_usuarios.codusu, sss_usuarios.nomusu, sss_usuarios.apeusu ".
					   "  FROM sss_usuarios ".
					   " WHERE sss_usuarios.codemp='".$this->codemp."'".
					   "   AND sss_usuarios.codusu IN (".substr($criterio,1).")".
					   " ORDER BY sss_usuarios.codusu ASC, sss_usuarios.nomusu ASC, sss_usuarios.apeusu ASC;";
		  	return $resultado = $this->buscarSql($cadenasql);
		}
	}
	
	public function cargarCheques()
	{
	 /************************************************************************************************************
	 * Funcion: cargarCheques
	 * Descripcion: Función que retorna los cheques asociados a la chequera en ese Banco, 
	 *              cuenta y chequera.
	 *************************************************************************************************************/
		 $cadenasql= "SELECT scb_cheques.numche, scb_cheques.estche, scb_cheques.codusu, scb_cheques.orden   ".
					 "	FROM scb_cheques ".
					 " WHERE scb_cheques.codemp='".$this->codemp."'".
					 "	 AND scb_cheques.codban='".$this->codban."'". 
					 "	 AND scb_cheques.ctaban='".$this->ctaban."'". 
					 "	 AND scb_cheques.numchequera='".$this->numchequera."'".
			         " ORDER BY scb_cheques.orden ASC";
		 return $resultado = $this->buscarSql($cadenasql);
	}
	
	public function  obtenerCatalogoCuentaBancoChequera()
	{
		/************************************************************************************************************
		 * Funcion: obtenerCatalogoCuentaBancoChequera
		 * Descripcion: Función que retorna las cuentas bancarias para asociarlas a los cheques
		 ************************************************************************************************************/
		$cadenasql = "SELECT scb_ctabanco.codban, scb_ctabanco.ctaban, scb_ctabanco.codtipcta, scb_ctabanco.ctabanext, ".
					 "	     scb_ctabanco.dencta, scb_ctabanco.sc_cuenta, scb_ctabanco.fecapr, scb_ctabanco.feccie, ".
					 "	     scb_ctabanco.estact, scb_ctabanco.ctaserext, scb_banco.nomban, scb_tipocuenta.nomtipcta, ". 
					 "	     scg_cuentas.denominacion ".
					 "	FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas ".
					 " WHERE scb_ctabanco.codemp = '".$this->codemp."' ".
		             "   AND scb_ctabanco.codban LIKE '%".$this->codban."%' ".
					 "	 AND scb_ctabanco.codemp = scb_banco.codemp ".
					 "	 AND scb_ctabanco.codban = scb_banco.codban ".
					 "	 AND scb_ctabanco.codtipcta = scb_tipocuenta.codtipcta ".
					 "	 AND scb_ctabanco.codemp = scg_cuentas.codemp ".
					 "	 AND scb_ctabanco.sc_cuenta = scg_cuentas.sc_cuenta ";
		return $resultado = $this->buscarSql($cadenasql);
		
	}
	
	public function buscarChequeras()
	{
		/************************************************************************************************************
		 * Funcion: buscarChequeras
		 * Descripcion: Función que retorna las chequeras registradas 
		 ************************************************************************************************************/
		$cadenasql = "SELECT DISTINCT scb_cheques.numchequera, scb_cheques.codban, scb_cheques.ctaban, scb_ctabanco.codtipcta, ". 
					 "	     scb_ctabanco.dencta, scb_tipocuenta.nomtipcta, scb_banco.nomban ".
					 "	FROM scb_cheques, scb_banco, scb_ctabanco, scb_tipocuenta ".
					 "	WHERE scb_cheques.codemp ='".$this->codemp."' ".
					 "	  AND scb_cheques.codemp = scb_ctabanco.codemp ".
					 "	  AND scb_cheques.codban = scb_ctabanco.codban ".
					 "	  AND scb_cheques.ctaban = scb_ctabanco.ctaban ".
					 "	  AND scb_ctabanco.codtipcta = scb_tipocuenta.codtipcta ".
					 "	  AND scb_ctabanco.codemp = scb_banco.codemp ".
					 "	  AND scb_ctabanco.codban = scb_banco.codban ".
					 "  ORDER BY 1";
		return $this->buscarSql($cadenasql);
	}
}
?>