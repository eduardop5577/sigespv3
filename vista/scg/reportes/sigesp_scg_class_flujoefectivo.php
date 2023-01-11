<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scg_class_flujoefectivo
{
	private $la_empresa;
	private $io_include;
	private $io_conexion;
	private $io_sql;
	
	public function __construct()
	{
		require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
				
		$this->io_include  = new sigesp_include();
		$this->io_conexion = $this->io_include->uf_conectar();
		$this->io_sql      = new class_sql($this->io_conexion);
		$this->la_empresa  = $_SESSION["la_empresa"];
	}
	
	public function uf_obtener_saldo($cuenta, $ad_fecha){
		//varibles filtros de la consulta
		$arrSaldos  = array();
		$ls_codemp  = $this->la_empresa["codemp"];
		$formcont   = $this->la_empresa["formcont"];
		$formcont   = str_replace('-', '', $formcont);
		$li_ano     = substr($ad_fecha, 0, 4) - 1;
		$cuenta     = str_pad($cuenta, strlen($formcont), '0');
		
		$ls_sql=  " SELECT coalesce(T_Saldo_anterior,0) as saldo_anterior, coalesce(T_Saldo,0) as saldo
						FROM scg_cuentas SC 
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' AND fecsal <= '{$ad_fecha}' GROUP BY codemp,sc_cuenta) curSaldo ON SC.sc_cuenta=curSaldo.sc_cuenta
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo_anterior
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' AND fecsal<='{$li_ano}-12-31 00:00:00' GROUP BY codemp,sc_cuenta) curSaldo_an ON SC.sc_cuenta=curSaldo_an.sc_cuenta
						WHERE SC.codemp=curSaldo.codemp AND curSaldo.codemp='{$ls_codemp}' AND (SC.sc_cuenta='{$cuenta}')";
		$dataSaldos = $this->io_sql->select($ls_sql);
		if ($dataSaldos != false) {
			$arrSaldos['salAct'] = $dataSaldos->fields['saldo'];
			$arrSaldos['salAnt'] = $dataSaldos->fields['saldo_anterior'];
		}
     	
		return $arrSaldos;
    }
    
	public function uf_buscar_ingreso($fecha) {
    	$ad_saldo   = 0;
    	$ai_ingreso = trim($this->la_empresa['ingreso']);
    	$ls_sql= " SELECT COALESCE(sum(SD.haber_mes-SD.debe_mes),0) as saldo ".
                 " FROM   scg_cuentas SC, scg_saldos SD ".
                 " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			     "        fecsal<='".$fecha."' AND (SC.sc_cuenta like '".$ai_ingreso."%') AND SC.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE fecha <= '".$fecha."' AND sc_cuenta LIKE '".$ai_ingreso."%')";
    	$rs_data=$this->io_sql->select($ls_sql);
	 	if($rs_data===false){// error interno sql
	 		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_ingreso_BG ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido = false;
	 	}
	 	else{
	 		if($row=$this->io_sql->fetch_row($rs_data)){
	 			$ad_saldo=$row["saldo"];
			}
			$this->io_sql->free_result($rs_data);
	 	}
	 	
	 	return $ad_saldo;
    }
    
    public function uf_buscar_gastos($fecha) {
    	$ad_saldo = 0;
    	$ai_gasto = trim($this->la_empresa['gasto']);
    	$ls_sql=" SELECT COALESCE(sum(SD.debe_mes-SD.haber_mes),0) as saldo ".
                " FROM   scg_cuentas SC, scg_saldos SD ".
             " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			 "        fecsal<='".$fecha."' AND (SC.sc_cuenta like '".$ai_gasto."%') AND SC.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE fecha <= '".$fecha."' AND sc_cuenta LIKE '".$ai_gasto."%')";
    	$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false){// error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto_BG ".$this->io_fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido = false;
	 	}
	 	else{
			if($row=$this->io_sql->fetch_row($rs_data)){
				$ad_saldo=$row["saldo"];
			}
			$this->io_sql->free_result($rs_data);
	 	}
	 	
	 	return $ad_saldo;
    }
      
	public function uf_buscar_resultado($fecha) {
    	$ld_ganancia = 0;
    	$ld_ingreso  = $this->uf_buscar_ingreso($fecha);
    	$ld_gasto    = $this->uf_buscar_gastos($fecha);
    	$ld_ganancia = abs($ld_ingreso-$ld_gasto);	 
    	
    	return $ld_ganancia;
    	
    }
    
    public function uf_calcular_variacion_relativa($saldo, $saldoant) {
    	$varRelativa = 0;
    	$varAbsoluta = $saldo - $saldoant;
    	if ($saldoant != 0) {
    		$varRelativa = ($varAbsoluta/$saldoant)*100;
	    	$varRelativa = number_format($varRelativa, 0, ',', '');
    	}
    	
    	if ($varRelativa<0) {
    		$varRelativa = '('.abs($varRelativa).')';
    	}
    	    	
    	return $varRelativa;
    }
}