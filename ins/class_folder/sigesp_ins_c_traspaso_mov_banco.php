<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_ins_c_traspaso_mov_banco
{
	var $io_sql;
	var $DS;
	var $DS_R;
	var $io_mensajes;
	var $io_funciones;
	var $io_fun_nomina;
	var $io_sno;
	var $ls_codemp;
	var $ls_codnom;
	
	public function __construct()
	{	
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_ins_c_traspaso_concepto_aporte
		//		   Access: public (sigesp_sno_c_cierre_periodo)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creación: 15/02/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 29/05/2006
		//////////////////////////////////////////////////////////////////////////////
		$this->ls_database_source = $_SESSION["ls_database"];
	    $this->ls_database_target = $_SESSION["ls_database_destino"];
		$this->ls_hostname_target = $_SESSION["ls_hostname_destino"];
		$this->ls_login_target    = $_SESSION["ls_login_destino"];
		$this->ls_password_target = $_SESSION["ls_password_destino"];
		$this->ls_gestor_target   = $_SESSION["ls_gestor_destino"];
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		////////////////////////////////////////////////////////////////////////////////////////
		$io_conexion_origen       = $io_include->uf_conectar();
		$io_conexion_destino      = $io_include->uf_conectar_otra_bd ($this->ls_hostname_target, $this->ls_login_target, $this->ls_password_target,$this->ls_database_target,$this->ls_gestor_target);
		$this->io_sql_origen      = new class_sql($io_conexion_origen);
	    $this->io_sql_destino     = new class_sql($io_conexion_destino);
		////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_R=new class_datastore();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();						
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
	}// end function sigesp_ins_c_traspaso_concepto_aporte
	
	
	
	
	
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_insert_cabecera_movbanco($ls_codigoemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,
									  $ls_estmov,$ls_codpro,$ls_cedbene,$ls_destino,$ls_codconmov,
									  $ls_fecmov,$ls_conmov,$ls_nomproben,$ls_monto,$ls_estbpd,$ls_estcon,
									  $ls_estcobing,$ls_esttra,$ls_chevau,$ls_estimpche,$ls_monobjret,$ls_monret,
									  $ls_procede,$ls_comprobante,$ls_fecha,$ls_idmco,$ls_emicheproc,$ls_emicheced,
									  $ls_emichenom,$ls_emichefec,$ls_estmovint,$ls_codusu,$ls_codopeidb,$ls_aliidb,
									  $ls_feccon,$ls_estreglib,$ls_numcarord,$ls_numpolcon,$ls_coduniadmsig,$ls_codbansig,
									  $ls_fecordpagsig,$ls_tipdocressig,$ls_numdocressig,$ls_estmodordpag,$ls_codfuefin,
									  $ls_forpagsig,$ls_medpagsig,$ls_codestprosig,$ls_nrocontrolop,$ls_fechaconta,
									  $ls_fechaanula,$ls_conanu,$ls_estant,$ls_docant,$ls_monamo,$ls_numordpagmin,
									  $ls_codtipfon,$ls_estserext,$ls_estmovcob,$ls_numconint,$ls_codper,$ls_codperi,
									  $ls_estapribs,$ls_estxmlibs,$ls_tranoreglib,$ls_estcondoc,$ls_fecenvfir,$ls_fecenvcaj)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cabecera_movbanco
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_aliidb=0;
		$ls_numpolcon=0;
		$ls_monamo=0.00;
		$ls_sql="INSERT INTO scb_movbco(codemp, codban, ctaban, numdoc, codope, estmov, cod_pro, ced_bene, tipo_destino, codconmov, fecmov, 
										conmov, nomproben, monto, estbpd, estcon, estcobing, esttra, chevau, estimpche, monobjret, monret, 
										procede, comprobante, fecha, id_mco, emicheproc, emicheced, emichenom, emichefec, estmovint, codusu, 
										codopeidb, aliidb, feccon, estreglib, numcarord, numpolcon, coduniadmsig, codbansig, fecordpagsig, tipdocressig, 
										numdocressig, estmodordpag, codfuefin, forpagsig, medpagsig, codestprosig, nrocontrolop, fechaconta, fechaanula, 
										conanu, estant, docant, monamo, numordpagmin, codtipfon, estserext, estmovcob, numconint, estapribs, estxmlibs, 
										codper, codperi, tranoreglib, estcondoc, fecenvfir, fecenvcaj) 
										VALUES ('".$ls_codigoemp."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."','".$ls_codope."','".$ls_estmov."','".$ls_codpro."',
												'".$ls_cedbene."','".$ls_destino."','".$ls_codconmov."','".$ls_fecmov."','".$ls_conmov."','".$ls_nomproben."',$ls_monto,'".$ls_estbpd."',
												'".$ls_estcon."','".$ls_estcobing."','".$ls_esttra."','".$ls_chevau."','".$ls_estimpche."',$ls_monobjret,$ls_monret,
												'".$ls_procede."','".$ls_comprobante."','".$ls_fecha."','".$ls_idmco."','".$ls_emicheproc."','".$ls_emicheced."','".$ls_emichenom."',
												'".$ls_emichefec."','".$ls_estmovint."','".$ls_codusu."','".$ls_codopeidb."',$ls_aliidb,'".$ls_feccon."','".$ls_estreglib."','".$ls_numcarord."',
												$ls_numpolcon,'".$ls_coduniadmsig."','".$ls_codbansig."','".$ls_fecordpagsig."','".$ls_tipdocressig."','".$ls_numdocressig."','".$ls_estmodordpag."','".$ls_codfuefin."',
									  			'".$ls_forpagsig."','".$ls_medpagsig."','".$ls_codestprosig."','".$ls_nrocontrolop."','".$ls_fechaconta."','".$ls_fechaanula."','".$ls_conanu."','".$ls_estant."','".$ls_docant."',$ls_monamo,
									  			'".$ls_numordpagmin."','".$ls_codtipfon."','".$ls_estserext."','".$ls_estmovcob."','".$ls_numconint."','".$ls_estapribs."','".$ls_estxmlibs."','".$ls_codper."','".$ls_codperi."',
												'".$ls_tranoreglib."','".$ls_estcondoc."','".$ls_fecenvfir."','".$ls_fecenvcaj."')";	
		
		$this->io_sql_destino->begin_transaction();
		$li_row=$this->io_sql_destino->execute($ls_sql);
		if($li_row===false)
		{
 			if($this->io_sql_destino->errno=='23505' || $this->io_sql_destino->errno=='1062' || $this->io_sql_destino->errno=='-239' || $this->io_sql_destino->errno=='-5'|| $this->io_sql_destino->errno=='-1')
			{
				$this->io_mensajes->message("Existe data duplicada o con el mismo codigo de banco..".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
			}
			else
			{
				$this->io_mensajes->message("CLASE->Traspaso Mov - Banco MÉTODO->uf_insert_cabecera_movbanco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
			}
			$lb_valido=false;
			
		}
		return $lb_valido;
	
}//end function uf_insert_cabecera_movbanco									 

function uf_insert_dt_movbanco($ls_codigoempdet,$ls_codbandet,$ls_ctabandet,$ls_numdocdet,$ls_codopedet,
							   $ls_estmovdet,$ls_cuentadet,$ls_debhabdet,$ls_coddeddet,$ls_documentodet,
							   $ls_desmovdet,$ls_procededet,$ls_montodet,$ls_monobjretdet,$ls_codperdet,$ls_codperidet)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cabecera_movbanco
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		
		$ls_sql="INSERT INTO scb_movbco_scg(codemp, codban, ctaban, numdoc, codope, estmov, scg_cuenta, debhab, codded, 
											documento, desmov, procede_doc, monto, monobjret, codper, codperi) 
										VALUES ('".$ls_codigoempdet."','".$ls_codbandet."','".$ls_ctabandet."','".$ls_numdocdet."',
												'".$ls_codopedet."','".$ls_estmovdet."','".$ls_cuentadet."','".$ls_debhabdet."','".$ls_coddeddet."',
												'".$ls_documentodet."','".$ls_desmovdet."','".$ls_procededet."',$ls_montodet,$ls_monobjretdet,'".$ls_codperdet."',
												'".$ls_codperidet."')";	
		$li_row=$this->io_sql_destino->execute($ls_sql);
		if($li_row===false)
		{
 			if($this->io_sql_destino->errno=='23505' || $this->io_sql_destino->errno=='1062' || $this->io_sql_destino->errno=='-239' || $this->io_sql_destino->errno=='-5'|| $this->io_sql_destino->errno=='-1')
			{
				$this->io_mensajes->message("Existe data duplicada o con el mismo codigo de banco..".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
			}
			else
			{
				$this->io_mensajes->message("CLASE->Traspaso Mov - Banco MÉTODO->uf_insert_cabecera_movbanco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
			}
			$lb_valido=false;
		}
		return $lb_valido;
	
}//end function uf_insert_cabecera_movbanco									 
//-----------------------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_chequear_beneficiario($as_codper,$as_ctahaber)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_chequear_beneficiario
		//	    Arguments: $as_codper // código del personal
		//                 $as_ctahaber // cuenta del beneficiario
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar si el personal existe como benficiario y buscar
		//                 su cuenta contable
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
 		$as_ctahaber="";
		$ls_sql="SELECT sc_cuenta ".
		        "  FROM rpc_beneficiario, sno_personal ".
				" WHERE rpc_beneficiario.codemp='".$this->ls_codemp."'  ". 
				"   AND sno_personal.codper='".$as_codper."'            ".
			    "   AND sno_personal.codemp=rpc_beneficiario.codemp     ".
				"   AND sno_personal.cedper=rpc_beneficiario.ced_bene   ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_chequear_beneficiario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if (!$rs_data->EOF)
			{		 
				$as_ctahaber=$rs_data->fields["sc_cuenta"];				
			}
			else
			{
				$this->io_mensajes->message("El personal ".$as_codper." no se encuentra registrado como beneficiario. No se puede realizar la recepción de Documento al personal");
				$lb_valido=false;
			}
		}	
		$arrResultado['as_ctahaber']=$as_ctahaber;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_chequear_beneficiario
//------------------------------------------------------------------------------------------------------------------------------------
	function uf_select_movbanco_peri($as_codperi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codperi    Código del periodo del movimiento  bancario
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en scb_movbco con este periodo dado
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 28/09/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT * FROM scb_movbco ".
		        " WHERE codemp= '0001' ".
				"  AND  codperi= '".$as_codperi."' ";	
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Mov - Banco MÉTODO->uf_select_movbanco_peri ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message)); 
		}
		else
		{
			if($row=$this->io_sql_origen->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql_origen->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql_origen->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_scg
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_movbanco_dt_peri($as_codperi,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codperi    Código del periodo del movimiento  bancario
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en scb_movbco con este periodo dado
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 28/09/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT * FROM scb_movbco_scg ".
		        " WHERE codemp= '0001' ".
				"  AND  codban= '".$as_codban."' ".
				"  AND  ctaban= '".$as_ctaban."' ".
				"  AND  numdoc= '".$as_numdoc."' ".
				"  AND  codope= '".$as_codope."' ".
				"  AND  estmov= '".$as_estmov."' ".
				"  AND  codperi= '".$as_codperi."' ";	
		$rs_data=$this->io_sql_origen->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Mov - Banco MÉTODO->uf_select_movbanco_dt_peri ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_origen->message)); 
		}
		else
		{
			if($row=$this->io_sql_origen->fetch_row($rs_data))
			{
				$this->DS_R->data=$this->io_sql_origen->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql_origen->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_scg
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_bancos($as_codban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codperi    Código del periodo del movimiento  bancario
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en scb_movbco con este periodo dado
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 28/09/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT codban FROM scb_banco ".
		        " WHERE codemp= '0001' ".
				"  AND  codban= '".$as_codban."' ";	
		$rs_data=$this->io_sql_destino->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Mov - Banco MÉTODO->uf_buscar_bancos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 

		}
		else
		{
			if($this->io_sql_destino->fetch_row($rs_data)>0)
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql_destino->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_scg
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_cuenta($as_codban,$as_ctaban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codperi    Código del periodo del movimiento  bancario
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en scb_movbco con este periodo dado
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 28/09/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT * FROM scb_ctabanco ".
		        " WHERE codemp= '0001' ".
				"  AND  codban= '".$as_codban."' ".
				"  AND  ctaban= '".$as_ctaban."' ";	
		$rs_data=$this->io_sql_destino->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Traspaso Mov - Banco MÉTODO->uf_buscar_cuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql_destino->message)); 
		}
		else
		{
			if($this->io_sql_destino->fetch_row($rs_data)>0)
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql_destino->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_scg
//-----------------------------------------------------------------------------------------------------------------------------------
}
?>
