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

class sigesp_scg_class_situacionfinanciera
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
	
	public function uf_situacion_financiera($li_ano,$ad_fecfin,$as_rango,$ad_fecdesde,$ad_fechasta){
		//varibles filtros de la consulta
		$ls_codemp    = $this->la_empresa["codemp"];
		$ls_activo    = trim($this->la_empresa["activo"]);
		$ls_pasivo    = trim($this->la_empresa["pasivo"]);
		$ls_capital   = trim($this->la_empresa["capital"]);
		$ls_resultado = trim($this->la_empresa["resultado"]);
		$ls_orden_d   = trim($this->la_empresa["orden_d"]);
		$ls_orden_h   = trim($this->la_empresa["orden_h"]);
		
		$rangoFecha = '';
		if ($as_rango == '1') {
			$rangoFecha = " AND fecsal<='".$ad_fecfin."' ";
		}
		else {
			$rangoFecha = " AND fecsal BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' ";
		}
		
		$ls_sql=  " SELECT SC.sc_cuenta, SC.denominacion, SC.nivel, coalesce(T_Saldo_anterior,0) as saldo_anterior, coalesce(T_Saldo,0) as saldo,  1 as tiporden 
						FROM scg_cuentas SC 
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' {$rangoFecha} GROUP BY codemp,sc_cuenta) curSaldo ON SC.sc_cuenta=curSaldo.sc_cuenta
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo_anterior
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' AND fecsal<='{$li_ano}-12-31 00:00:00' GROUP BY codemp,sc_cuenta) curSaldo_an ON SC.sc_cuenta=curSaldo_an.sc_cuenta
						WHERE SC.codemp=curSaldo.codemp AND curSaldo.codemp='{$ls_codemp}' AND (SC.sc_cuenta like '{$ls_activo}%') AND SC.nivel<=4 AND SC.nivel<>3
					UNION 
					SELECT SC.sc_cuenta, SC.denominacion, SC.nivel, coalesce(T_Saldo_anterior,0) as saldo_anterior, coalesce(T_Saldo,0) as saldo, 2 as tiporden 
						FROM scg_cuentas SC 
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' {$rangoFecha} GROUP BY codemp,sc_cuenta) curSaldo ON SC.sc_cuenta=curSaldo.sc_cuenta 
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo_anterior
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' AND fecsal<='{$li_ano}-12-31 00:00:00' GROUP BY codemp,sc_cuenta) curSaldo_an ON SC.sc_cuenta=curSaldo_an.sc_cuenta
						WHERE SC.codemp=curSaldo.codemp AND curSaldo.codemp='{$ls_codemp}' AND (SC.sc_cuenta like '{$ls_pasivo}%' OR SC.sc_cuenta like '{$ls_resultado}%' OR SC.sc_cuenta like '{$ls_capital}%') AND SC.nivel<=4 AND SC.nivel<>3
					UNION
					SELECT SC.sc_cuenta, SC.denominacion, SC.nivel, coalesce(T_Saldo_anterior,0) as saldo_anterior, coalesce(T_Saldo,0) as saldo,  3 as tiporden 
						FROM scg_cuentas SC 
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' {$rangoFecha} GROUP BY codemp,sc_cuenta) curSaldo ON SC.sc_cuenta=curSaldo.sc_cuenta
							LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as T_Saldo_anterior
												FROM scg_saldos 
												WHERE codemp='{$ls_codemp}' AND fecsal<='{$li_ano}-12-31 00:00:00' GROUP BY codemp,sc_cuenta) curSaldo_an ON SC.sc_cuenta=curSaldo_an.sc_cuenta
						WHERE SC.codemp=curSaldo.codemp AND curSaldo.codemp='{$ls_codemp}' AND (SC.sc_cuenta like '{$ls_orden_h}%' OR SC.sc_cuenta like '{$ls_orden_d}%') AND SC.nivel<=4 AND SC.nivel<>3 
					ORDER BY 6,1";
	
	 	//echo $ls_sql."<br>";
	 	//die();
	
     	return $this->io_sql->select($ls_sql);
    }
    
    public function uf_buscar_ganancia($fecha,$as_rango,$ad_fecdesde,$ad_fechasta) {
    	$ld_ganancia = 0;
    	$ld_ingreso  = $this->uf_buscar_ingreso($fecha,$as_rango,$ad_fecdesde,$ad_fechasta);
    	$ld_gasto    = $this->uf_buscar_gastos($fecha,$as_rango,$ad_fecdesde,$ad_fechasta);
    	$ld_ganancia = abs($ld_ingreso-$ld_gasto);	 
    	
    	return $ld_ganancia;
    	
    }
    
    public function uf_buscar_ingreso($fecha,$as_rango,$ad_fecdesde,$ad_fechasta) {
    	$ad_saldo   = 0;
    	$ai_ingreso = trim($this->la_empresa['ingreso']);
    	$rangoFecha1 = '';
    	$rangoFecha2 = '';
    	if ($as_rango == '1') {
    		$rangoFecha1 = " AND fecsal<='".$fecha."' ";
    		$rangoFecha2 = " fecha <='".$fecha."' ";
    	}
    	else {
    		$rangoFecha1 = " AND fecsal BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' ";
    		$rangoFecha2 = " fecha BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' ";
    	}
    	
    	$ls_sql= " SELECT COALESCE(sum(SD.haber_mes-SD.debe_mes),0) as saldo ".
                 " FROM   scg_cuentas SC, scg_saldos SD ".
                 " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C')  ".
			     "        {$rangoFecha1} AND (SC.sc_cuenta like '".$ai_ingreso."%') AND SC.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE {$rangoFecha2} AND sc_cuenta LIKE '".$ai_ingreso."%')";
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
    
    public function uf_buscar_gastos($fecha,$as_rango,$ad_fecdesde,$ad_fechasta) {
    	$ad_saldo = 0;
    	$ai_gasto = trim($this->la_empresa['gasto']);
    	$rangoFecha1 = '';
    	$rangoFecha2 = '';
    	if ($as_rango == '1') {
    		$rangoFecha1 = " AND fecsal<='".$fecha."' ";
    		$rangoFecha2 = " fecha <='".$fecha."' ";
    	}
    	else {
    		$rangoFecha1 = " AND fecsal BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' ";
    		$rangoFecha2 = " fecha BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' ";
    	}
    	
    	$ls_sql=" SELECT COALESCE(sum(SD.debe_mes-SD.haber_mes),0) as saldo ".
                " FROM   scg_cuentas SC, scg_saldos SD ".
             " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') ".
			 "        {$rangoFecha1} AND (SC.sc_cuenta like '".$ai_gasto."%') AND SC.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE {$rangoFecha2} AND sc_cuenta LIKE '".$ai_gasto."%')";
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
}