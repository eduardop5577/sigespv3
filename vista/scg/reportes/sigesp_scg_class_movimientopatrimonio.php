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

class sigesp_scg_class_movimientopatrimonio
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
	
	public function uf_obtener_saldo($fechasaldo, $grupocuenta, $nivel){
		$ld_saldo = 0;
		
		$ls_sql=  " SELECT coalesce(saldo,0) as saldo
						FROM scg_cuentas SC 
						LEFT OUTER JOIN (SELECT codemp, sc_cuenta, coalesce(sum(debe_mes)-sum(haber_mes),0)as saldo
											FROM scg_saldos 
											WHERE codemp='{$this->la_empresa["codemp"]}' AND fecsal<='{$fechasaldo}' GROUP BY codemp,sc_cuenta) curSaldo ON SC.sc_cuenta=curSaldo.sc_cuenta
						WHERE curSaldo.codemp='{$this->la_empresa["codemp"]}' AND SC.codemp=curSaldo.codemp AND (SC.sc_cuenta like '{$grupocuenta}%') AND SC.nivel={$nivel}";
	
	 	//echo $ls_sql."<br>";
		
     	$data = $this->io_sql->select($ls_sql);
     	if($data != false){
     		if($row=$this->io_sql->fetch_row($data)){
     			$ld_saldo = $row["saldo"];
			}
     	}
     	
     	return $ld_saldo;
    }
    
    public function uf_obtener_movimiento($fecha) {
    	
    	
    	$ls_sql=  "SELECT sc_cuenta, debhab, monto
  					FROM scg_dt_cmp
  					WHERE sc_cuenta like '3%' and fecha<='{$fecha}' 
  					ORDER BY sc_cuenta";
    	
    	//echo $ls_sql."<br>";
    	//die();
	
     	return $this->io_sql->select($ls_sql);
    }
    
    
}