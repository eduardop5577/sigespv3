<?php
/***********************************************************************************
* @fecha de modificacion: 29/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_c_cuentas_banco
{
	var $dat;
	var $SQL;
	var $fun;
	
	public function __construct()
	{
		$sig_inc=new sigesp_include();
		$con=$sig_inc->uf_conectar();
		$this->SQL=new class_sql($con);
		$this->fun=new class_funciones();	
		$this->dat = $_SESSION["la_empresa"];
	}



	function uf_verificar_saldo($as_codban,$as_ctaban,$ldec_saldo)
	{
		/////////////////////////////////////////////////////////////////////////////
		// Funtion	  :  uf_verificar_saldo
		//
		//	Return	   :	ldec_saldo
		//
		//	Descripcion :  Fucnion que se encarga de obtener el saldo disponible para
		//						el banco y cuenta recibido como parametro
		/////////////////////////////////////////////////////////////////////////////
		$ls_codemp=0;
		$ldec_monto_debe=0;$ldec_monto_haber=0;$ldec_saldo=0;
		
		$ls_codemp = $this->dat["codemp"];
		
		$ls_sql="SELECT monhab,mondeb,(mondeb - monhab) As saldo
				FROM ( SELECT COALESCE(SUM(monto - monret),0) As monhab
					   FROM  scb_movbco 
					   WHERE codban='".$as_codban."' AND ctaban='".$as_ctaban."' AND 
					   (codope='RE' OR codope='ND' OR codope='CH') AND estmov<>'A' AND estmov<>'O' AND codemp='".$ls_codemp."') D,
					 ( SELECT COALESCE(SUM(monto - monret),0) As mondeb
					   FROM scb_movbco
					   WHERE codban='".$as_codban."' AND ctaban='".$as_ctaban."' AND 
					   (codope='NC' OR codope='DP') AND estmov<>'A' AND estmov<>'O' AND codemp='".$ls_codemp."') H";

		$rs_cta=$this->SQL->select($ls_sql);
		if($rs_cta==false)
		{
			$this->is_msg_error="Error al consultar saldo ".$this->fun->uf_convertirmsg($this->SQL->message);
		    $lb_valido = false;
			$ldec_saldo=0;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_cta))
			{
				$ldec_saldo      = $row["saldo"];
				$ldec_monto_debe = $row["mondeb"];
				$ldec_monto_haber= $row["monhab"];
				if(is_null($ldec_saldo))
				{
					$ldec_saldo=0;
				}
				if((is_null($ldec_monto_debe))&&($ldec_monto_haber>0))
				{
					$ldec_saldo=$ldec_monto_haber;
				}
				if(is_null($ldec_monto_haber)&&($ldec_monto_debe>0))
				{
					$ldec_saldo=$ldec_monto_debe;
				}
				
				$lb_valido=true;
				
			}
			else
			{
				$lb_valido=false;
				$this->is_msg_error="No hay movimientos para la cuenta";
				$ldec_saldo=0;
			}
		}	
		return  $lb_valido;
	}

}
?>