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

require_once ("../../base/librerias/php/general/sigesp_lib_daogenericoplus.php");

class servicioColocacion extends DaoGenericoPlus
{
	private $objcabecera;
	
	function __construct($tabcabecera,$arrtabdetalles)
	{
        parent::__construct($tabcabecera,$arrtabdetalles);
    }
    
	public function grabarColocacion($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$resultado = $this->incluirDto();
		return $resultado;
	}
	
	public function getColocaciones($codemp,$numctaban,$dencol,$nomban)
	{
		$cadenasql ="SELECT scb_colocacion.codban,scb_banco.nomban,scb_colocacion.ctaban,scb_colocacion.numcol,scb_colocacion.dencol,scb_colocacion.codtipcol,".
					"	    scb_colocacion.feccol,scb_colocacion.diacol,scb_colocacion.tascol, scb_colocacion.monto,scb_colocacion.fecvencol,scb_colocacion.monint,".
					"		scb_colocacion.sc_cuenta,scb_colocacion.spi_cuenta,scb_colocacion.estreicol,scb_colocacion.sc_cuentacob,scb_colocacion.codestpro1,".
					"		scb_colocacion.codestpro2,scb_colocacion.codestpro3,scb_colocacion.codestpro4,scb_colocacion.codestpro5,scb_colocacion.estcla,".
					"		scb_tipocolocacion.nomtipcol,scg_cuentas.denominacion as scgctadeno,scb_ctabanco.dencta,spi_cuentas.denominacion as spictadeno,".
					"		tabcuenta.denominacion as denocob ".
					"  FROM scb_colocacion, scb_banco, scb_tipocolocacion, scg_cuentas, scb_ctabanco, spi_cuentas, scg_cuentas as tabcuenta ".
					" WHERE scb_colocacion.codemp = '".$codemp."' ".
  					"	AND scb_colocacion.ctaban LIKE '%".$numctaban."%' ".
  					"	AND scb_colocacion.dencol LIKE '%".$dencol."%' ".
  					"	AND scb_banco.nomban LIKE '%".$nomban."%'".
  					"	AND scb_colocacion.codemp = scb_banco.codemp ".
  					"	AND scb_colocacion.codban = scb_banco.codban ".
  					"	AND scb_colocacion.codtipcol = scb_tipocolocacion.codtipcol ".
  					"	AND scb_colocacion.codemp = scg_cuentas.codemp ".
  					"	AND scb_colocacion.sc_cuenta = scg_cuentas.sc_cuenta ".
  					"	AND scb_ctabanco.codemp = scb_colocacion.codemp ".
  					"	AND scb_ctabanco.codban = scb_colocacion.codban ".
  					"	AND scb_ctabanco.ctaban = scb_colocacion.ctaban ".
  					"	AND spi_cuentas.codemp = scb_colocacion.codemp ".
  					"	AND spi_cuentas.spi_cuenta = scb_colocacion.spi_cuenta ".
  					"	AND scb_colocacion.codemp = tabcuenta.codemp ".
  					"	AND scb_colocacion.sc_cuentacob = tabcuenta.sc_cuenta ";
		$this->objcabecera = $this->getCabecera();
		return $this->objcabecera->buscarSql($cadenasql);
	}
	
	public function getDetalleColocacion($codemp,$codban,$ctaban,$numcol)
	{
		$cadenasql = "SELECT scb_dt_colocacion.fecreint,scb_dt_colocacion.montoreint ".
					 "  FROM scb_dt_colocacion ".
					 " WHERE scb_dt_colocacion.codemp = '".$codemp."' ".
  					 "	 AND scb_dt_colocacion.codban = '".$codban."' ".
  					 "	 AND scb_dt_colocacion.ctaban = '".$ctaban."' ". 
  					 "	 AND scb_dt_colocacion.numcol = '".$numcol."'";
		$this->objcabecera = $this->getCabecera();
		return $this->objcabecera->buscarSql($cadenasql);
	}
	
	public function borrarColocacion($arrjson,$codemp)
	{
		$this->setData($arrjson,$codemp);
		$datosCabecera    = $arrjson->datoscabecera[0];
		$arrtabignorar[0] = 'scb_colocacion';
		$arrtabignorar[1] = 'scb_dt_colocacion';
		return $this->eliminarDto(true, 'numcol', $datosCabecera->numcol, $arrtabignorar);
	}
}
?>