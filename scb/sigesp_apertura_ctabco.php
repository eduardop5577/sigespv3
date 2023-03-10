<?phpഀ
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

	class sigesp_apertura_ctabcoഀ
	{ഀ
		var $SQL;ഀ
		var $fun;ഀ
		var $io_msg;ഀ
		var $is_msg_error;	ഀ
		var $dat;ഀ
		var $ds_data;ഀ
		ഀ
		ഀ
		public function __construct()
		{ഀ
			require_once("../base/librerias/php/general/sigesp_lib_sql.php");ഀ
			require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");ഀ
			require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");ഀ
			require_once("../base/librerias/php/general/sigesp_lib_include.php");ഀ
			require_once("../base/librerias/php/general/sigesp_lib_fecha.php");ഀ
			$sig_inc=new sigesp_include();ഀ
			$con=$sig_inc->uf_conectar();ഀ
			$this->io_sql=new class_sql($con);ഀ
			$this->SQL_aux=new class_sql($con);ഀ
			$this->io_funcion=new class_funciones();ഀ
			$this->io_fecha=new class_fecha();ഀ
			$this->io_msg=new class_mensajes();ഀ
			$this->dat=$_SESSION["la_empresa"];	ഀ
			$this->ds_data=new class_datastore();			ഀ
		}ഀ
		ഀ
		function uf_calcular_saldo_colocacion($as_codban,$as_ctaban) ഀ
		{ഀ
			/*------------------------------------------------------------------ഀ
		    	- Funcion que calcula el saldo de las colocacionesഀ
				- Retorna el saldo si se ejecuto correctamente, de lo contrarioഀ
				  retorna falso.ഀ
				- Elaborado por Ing. Laura Cabré.ഀ
				- Fecha: 12/01/2007			ഀ
			//-----------------------------------------------------------------*/ഀ
			$lb_valido=false;ഀ
			//Calculando el monto de los Creditos positivos (no anulados)ഀ
			$ls_codemp=$this->dat["codemp"];ഀ
			$ls_sql:"SELECT COALESCE(SUM(monmovcol),0) AS montoഀ
					FROM scb_movcolഀ
					WHERE codemp='$ls_codemp' AND codban='$as_codban' AND ctaban='$as_ctaban' AND ഀ
					(codope='CH' OR codope='ND' OR codope='RE')	AND estcol<>'A'";ഀ
			$rs_datos=$this->io_sql->select($ls_sql);ഀ
			if(($rs_datos==false))ഀ
			{ഀ
				$lb_valido=false;ഀ
				$this->is_msg_error=$this->io_funcion->uf_convertirmsg($this->io_sql->message);		ഀ
				print "Error en uf_calcular_saldo_colocacion ".$this->is_msg_error;ഀ
			}ഀ
			elseഀ
			{ഀ
				if($row=$this->io_sql->fetch_row($rs_datos))ഀ
				{ഀ
					$ldec_creditostmp=$row["monto"];ഀ
					$lb_valido=true;					ഀ
				}			ഀ
			}ഀ
			if($lb_valido)//  Calculando el monto de los Creditos negativos (anulados)ഀ
			{ഀ
				$ls_sql="SELECT COALESCE(SUM(monmovcol),0) AS montoഀ
						FROM scb_movcolഀ
						WHERE codemp='$ls_codemp' AND codban='$as_codban' AND ctaban='$as_ctaban' ANDഀ
						(codope='CH' OR codope='ND' OR codope='RE') AND estcol='A'";ഀ
				$rs_datos=$this->io_sql->select($ls_sql);ഀ
				if(($rs_datos==false))ഀ
				{ഀ
					$lb_valido=false;ഀ
					$this->is_msg_error=$this->io_funcion->uf_convertirmsg($this->io_sql->message);		ഀ
					print "Error en uf_calcular_saldo_colocacion ".$this->is_msg_error;ഀ
				}ഀ
				elseഀ
				{ഀ
					if($row=$this->io_sql->fetch_row($rs_datos))ഀ
					{ഀ
						$ldec_creditos_negativostmp=$row["monto"];ഀ
						$lb_valido=true;					ഀ
					}ഀ
				ഀ
				}				ഀ
			}ഀ
			if($lb_valido)//  Calculando el monto de los Debitos positivos (no anulados)ഀ
			{ഀ
				$ls_sql="SELECT COALESCE(SUM(monmovcol),0) AS montoഀ
						FROM scb_movcolഀ
						WHERE codemp='$ls_codemp' AND codban='$as_codban' AND ctaban='$as_ctaban'ഀ
						AND (codope='DP' OR codope='NC') AND estcol<>'A'";ഀ
				$rs_datos=$this->io_sql->select($ls_sql);ഀ
				if(($rs_datos==false))ഀ
				{ഀ
					$lb_valido=false;ഀ
					$this->is_msg_error=$this->io_funcion->uf_convertirmsg($this->io_sql->message);		ഀ
					print "Error en uf_calcular_saldo_colocacion ".$this->is_msg_error;ഀ
				}ഀ
				elseഀ
				{ഀ
					if($row=$this->io_sql->fetch_row($rs_datos))ഀ
					{ഀ
						$ldec_debitostmp=$row["monto"];ഀ
						$lb_valido=true;					ഀ
					}				ഀ
				}				ഀ
			}ഀ
			if($lb_valido)//  Calculando el monto de los Debitos negativos (anulados)ഀ
			{ഀ
				$ls_sql="SELECT COALESCE(SUM(monmovcol),0) AS montoഀ
						FROM scb_movcolഀ
						WHERE codemp='$ls_codemp' AND codban='$as_codban' AND ctaban='$as_ctaban'ഀ
						AND (codope='DP' OR codope='NC') AND estcol='A'";ഀ
				$rs_datos=$this->io_sql->select($ls_sql);ഀ
				if(($rs_datos==false))ഀ
				{ഀ
					$lb_valido=false;ഀ
					$this->is_msg_error=$this->io_funcion->uf_convertirmsg($this->io_sql->message);		ഀ
					print "Error en uf_calcular_saldo_colocacion ".$this->is_msg_error;ഀ
				}ഀ
				elseഀ
				{ഀ
					if($row=$this->io_sql->fetch_row($rs_datos))ഀ
					{ഀ
						$ldec_debitos_negativostmp=$row["monto"];ഀ
						$lb_valido=true;					ഀ
					}				ഀ
				}				ഀ
			}ഀ
			if($lb_valido)ഀ
			{ഀ
				$ldec_debitos  = $ldec_debitostmp  - $ldec_debitos_negativostmp;ഀ
				$ldec_creditos = $ldec_creditostmp - $ldec_creditos_negativostmp;ഀ
				$ldec_saldo    = $ldec_creditos    - $ldec_debitos; ഀ
				return $ldec_saldo;ഀ
			}ഀ
			elseഀ
			{ഀ
				return $lb_valido;ഀ
			}			ഀ
		}ഀ
		ഀ
		function uf_calcular_saldo_documento($as_codban,$as_ctaban)ഀ
		{ഀ
		    /*------------------------------------------------------------------ഀ
		    	- Funcion que calcula el salso de las colocacionesഀ
				- Retorna el saldo si se ejecuto correctamente, de lo contrarioഀ
				  retorna falso.ഀ
				- Elaborado por Ing. Laura Cabré.ഀ
				- Fecha: 12/01/2007			ഀ
			//-----------------------------------------------------------------*/ഀ
			$ls_codemp=$this->dat["codemp"];ഀ
			$lb_valido=false;ഀ
			$ls_sql="SELECT codope AS operacion, (monto-monret) AS monto, estmov AS estadoഀ
					FROM scb_movbcoഀ
					WHERE codemp='$ls_codemp' AND codban='$as_codban' AND ctaban='$as_ctaban'";ഀ
			$rs_datos=$this->io_sql->select($ls_sql);ഀ
			if(($rs_datos==false))ഀ
			{ഀ
				$lb_valido=false;ഀ
				$this->is_msg_error=$this->io_funcion->uf_convertirmsg($this->io_sql->message);		ഀ
				print "Error en uf_calcular_saldo_colocacion ".$this->is_msg_error;ഀ
			}ഀ
			elseഀ
			{ഀ
				if($row=$this->io_sql->fetch_row($rs_datos))ഀ
				{ഀ
					$la_data=$this->io_sql->obtener_datos($rs_datos);ഀ
					$this->ds_data->data=$la_data;ഀ
					$li_total=$this->ds_data->getRowCount("operacion");ഀ
					$ldec_debitostmp=0;ഀ
					$ldec_creditostmp=0;ഀ
					$ldec_debitos_negativostmp=0;ഀ
					$ldec_creditos_negativostmp=0;ഀ
					for($li_i=1;$li_i<=$li_total;$li_i++)ഀ
					{ഀ
						$ls_operacion = $this->ds_data->getValue("operacion",$li_i);ഀ
						$ls_estado    = $this->ds_data->getValue("estado",$li_i);ഀ
						if((($ls_operacion=="CH") || ($ls_operacion=="ND") || ($ls_operacion=="RE")) && ($ls_estado<>"A"))ഀ
						ഀ
						ഀ
						ഀ
						Sum monto for (operacion$"CH,ND,RE") and (Estado <> "A") to nCreditosTmpഀ
						Sum monto for (operacion$"CH,ND,RE") and (Estado = "A") to nCreditosTmpNegഀ
						Sum monto for (operacion$"DP,NC") and (Estado <> "A") to nDebitosTmpഀ
						Sum monto for (operacion$"DP,NC") and (Estado = "A") to nDebitosTmpNegഀ
					ഀ
					}ഀ
										ഀ
				}				ഀ
			}ഀ
		}ഀ
	}ഀ
?>