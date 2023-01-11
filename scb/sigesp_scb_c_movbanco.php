<?php
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

class sigesp_scb_c_movbanco
{
	var $is_msg_error;
	var $io_sql;
	var $siginc;
	var $int_scg;
	var $int_spg;
	var $msg;
	var $fun;
	var $io_fecha;
	var $dat;
	var $io_seguridad;
	
	public function __construct($aa_security)
	{
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
		$this->io_ds_bancos=new class_datastore(); // Datastored de movimientos de banco
		$this->io_seguridad= new sigesp_c_seguridad();		
		$this->siginc=new sigesp_include();
		$this->io_fecha=new class_fecha();
		$con=$this->siginc->uf_conectar();
		//$con->debug=true;
		$this->io_sql=new class_sql($con);
		$this->is_msg_error="";
		$this->msg=new class_mensajes();
		$this->fun=new class_funciones();
		$this->dat=$_SESSION["la_empresa"];
		$this->la_security=$aa_security;
	}
	
	function uf_generar_num_cmp($as_codemp,$as_procede)
	{
		 $ls_sql="SELECT numdoc 
		 		  FROM scb_movbco 
				  WHERE codemp='".$as_codemp."' AND codope='".$as_procede."' 
				  ORDER BY comprobante DESC";		
		  $rs_funciondb=$this->io_sql->select($ls_sql);
		  if ($row=$this->io_sql->fetch_row($rs_funciondb))
		  { 
			  $codigo=$row["numdoc"];
			  settype($codigo,'int');                             // Asigna el tipo a la variable.
			  $codigo = $codigo + 1;                              // Le sumo uno al entero.
			  settype($codigo,'string');                          // Lo convierto a varchar nuevamente.
			  $ls_codigo=$this->fun->uf_cerosizquierda($codigo,15);
		  }
		  else
		  {
			  $codigo="1";
			  $ls_codigo=$this->fun->uf_cerosizquierda($codigo,15);
		  }
		return $ls_codigo;
	}
	
	function uf_generar_voucher($as_codemp)
	{
		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		$io_keygen= new sigesp_c_generar_consecutivo();
		$codigo= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","chevau","SCBBCH",25,"","","");
		$codigo = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','chevau','SCBBCH',25,'','','',$this->la_security["logusr"]);
		unset($io_keygen);
		$ls_codigo=$this->fun->uf_cerosizquierda($codigo,25);
		return $ls_codigo;
	}
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_total_bancos($as_codcon,$ls_codban,$ls_denban)
	{
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		//-Funcion que verifica que el movimiento bancario no exista
		//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_casacon=$dat["casconmov"];
		$li_row=0;
    	$ls_sql = "SELECT scb_banco.codban, scb_banco.nomban 
						 FROM scb_banco, scb_casamientoconcepto  
						 WHERE scb_banco.codemp='".$ls_codemp."' 
						 AND scb_banco.codban like '%".$ls_codban."%' 
						 AND scb_banco.nomban like '%".strtoupper($ls_denban)."%' 
						 AND scb_casamientoconcepto.codban=scb_banco.codban
						 AND scb_casamientoconcepto.codconmov='".$as_codcon."' 
						 GROUP BY scb_banco.codban, scb_banco.nomban
						 ORDER BY scb_banco.codban ASC"; 
		$rs_data =$this->io_sql->select($ls_sql);
		if(($rs_data===false))
		{
			$this->is_msg_error="Error en uf_total_bancos".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			while (!$rs_data->EOF)
			   {        
				 $li_row++;
				 $ls_codban	 = $rs_data->fields["codban"];
				 $ls_nomban  = $rs_data->fields["nomban"];
				 $rs_data->MoveNext();
			   }	
		}
		return $li_row;
	}// fin del uf_total_bancos 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_bancos_ctaconcepto($as_codcon,$ls_codban,$ls_denban)
	{
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		//-Funcion que verifica que el movimiento bancario no exista
		//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_casacon=$dat["casconmov"];
		$li_row=0;
    	$ls_sql = "SELECT scb_banco.codban, scb_banco.nomban 
						 FROM scb_banco, scb_casamientoconcepto  
						 WHERE scb_banco.codemp='".$ls_codemp."' 
						 AND scb_banco.codban like '%".$ls_codban."%' 
						 AND scb_banco.nomban like '%".strtoupper($ls_denban)."%' 
						 AND scb_casamientoconcepto.codban=scb_banco.codban
						 AND scb_casamientoconcepto.codconmov='".$as_codcon."' 
						 GROUP BY scb_banco.codban, scb_banco.nomban
						 ORDER BY scb_banco.codban ASC"; 
		$rs_data =$this->io_sql->select($ls_sql);
		if(($rs_data===false))
		{
			$this->is_msg_error="Error en uf_bancos_ctaconcepto".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		return $rs_data;
	}// fin del uf_total_bancos 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_total_cuentas($as_codcon,$ls_codban,$ls_ctaban,$ls_denctaban)
	{
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		//-Funcion que verifica que el movimiento bancario no exista
		//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_c_cuentas_banco.php");
		$io_ctaban   = new sigesp_c_cuentas_banco();
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_casacon=$dat["casconmov"];
		$li_row=0;
    	$ls_sql="SELECT scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta,
					 scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban,
					 scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr,
					 scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact
			    FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, scb_casamientoconcepto
			   WHERE scb_ctabanco.codemp='".$ls_codemp."' 
			     AND scb_ctabanco.codban like '%".$ls_codban."%'  
				 AND scb_ctabanco.ctaban like '".$ls_ctaban."%' 
				 AND scb_casamientoconcepto.codconmov='".$as_codcon."'
				 AND scb_casamientoconcepto.codban='".$ls_codban."'
				 AND UPPER(scb_ctabanco.dencta) like '%".strtoupper($ls_denctaban)."%'
			     AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta 
			     AND scb_ctabanco.codban=scb_banco.codban 
				 AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta 
				 AND scb_ctabanco.codemp=scg_cuentas.codemp
				 GROUP BY scb_ctabanco.ctaban, scb_ctabanco.dencta, scb_ctabanco.sc_cuenta,
				 scg_cuentas.denominacion, scb_ctabanco.codban, scb_banco.nomban, scb_ctabanco.codtipcta,
				 scb_tipocuenta.nomtipcta, scb_ctabanco.fecapr, scb_ctabanco.feccie, scb_ctabanco.estact";  
		$rs_data =$this->io_sql->select($ls_sql);
		if(($rs_data===false))
		{
			$this->is_msg_error="Error en uf_total_cuentas".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			while (!$rs_data->EOF)
			   {        
				 $li_row++;
				 $rs_data->MoveNext();
			   }	
		}
		return $li_row;
	}// fin del uf_total_cuentas 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_ctas_ctaconcepto($as_codcon,$ls_codban,$ls_ctaban,$ls_denctaban)
	{
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//
		//-Funcion que verifica que el movimiento bancario no exista
		//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_c_cuentas_banco.php");
		$io_ctaban   = new sigesp_c_cuentas_banco();
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_casacon=$dat["casconmov"];
		$li_row=0;
    	$ls_sql="SELECT scb_ctabanco.ctaban as ctaban,scb_ctabanco.dencta as dencta,TRIM(scb_ctabanco.sc_cuenta) as sc_cuenta,
					 scg_cuentas.denominacion as denominacion,scb_ctabanco.codban as codban,scb_banco.nomban as nomban,
					 scb_ctabanco.codtipcta as codtipcta,scb_tipocuenta.nomtipcta as nomtipcta,scb_ctabanco.fecapr as fecapr,
					 scb_ctabanco.feccie as feccie,scb_ctabanco.estact as estact
			    FROM scb_ctabanco, scb_tipocuenta, scb_banco, scg_cuentas, scb_casamientoconcepto
			   WHERE scb_ctabanco.codemp='".$ls_codemp."' 
			     AND scb_ctabanco.codban like '%".$ls_codban."%'  
				 AND scb_ctabanco.ctaban like '".$ls_ctaban."%' 
				 AND scb_casamientoconcepto.codconmov='".$as_codcon."'
				 AND scb_casamientoconcepto.codban='".$ls_codban."'
				 AND UPPER(scb_ctabanco.dencta) like '%".strtoupper($ls_denctaban)."%'
			     AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta 
			     AND scb_ctabanco.codban=scb_banco.codban 
				 AND scb_ctabanco.sc_cuenta=scg_cuentas.sc_cuenta 
				 AND scb_ctabanco.codemp=scg_cuentas.codemp
				 GROUP BY scb_ctabanco.ctaban, scb_ctabanco.dencta, scb_ctabanco.sc_cuenta,
				 scg_cuentas.denominacion, scb_ctabanco.codban, scb_banco.nomban, scb_ctabanco.codtipcta,
				 scb_tipocuenta.nomtipcta, scb_ctabanco.fecapr, scb_ctabanco.feccie, scb_ctabanco.estact";  
		$rs_data =$this->io_sql->select($ls_sql);
		if(($rs_data===false))
		{
			$this->is_msg_error="Error en uf_ctas_ctaconcepto".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		return $rs_data;
	}// fin del uf_total_cuentas 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	
	
	function uf_select_movimiento($ls_numdoc,$ls_codope,$ls_codban,$ls_ctaban,$ls_estmov)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que verifica que el movimiento bancario no exista
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		
		$ls_sql="SELECT numdoc,codope,estmov 
				 FROM   scb_movbco
				 WHERE  codemp='".$ls_codemp."' AND codban ='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND    numdoc='".$ls_numdoc."' AND codope ='".$ls_codope."' ";
		$rs_mov=$this->io_sql->select($ls_sql);
		if(($rs_mov===false))
		{
			$this->is_msg_error="Error en select movimiento,".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_mov))
			{
				return true;
			}
			else
			{
				return false;
			}	
		}
	}
	
	function uf_select_movimiento_completo($ls_numdoc,$ls_codope,$ls_codban,$ls_ctaban,$ls_estmov,$ad_fecha)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que verifica que el movimiento bancario no exista
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ad_fecha=$this->fun->uf_convertirdatetobd($ad_fecha);
		$ls_sql="SELECT * 
				 FROM   scb_movbco
				 WHERE  codemp='".$ls_codemp."' AND codban ='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND    numdoc='".$ls_numdoc."' AND fecmov='".$ad_fecha."' ";
		$rs_mov=$this->io_sql->select($ls_sql);
		if(($rs_mov===false))
		{
			$this->is_msg_error="Error en select movimiento,".$this->fun->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_mov))
			{
				$ls_codban			=$row["codban"]; $this->io_ds_bancos->insertRow("codban",$ls_codban);
				$ls_ctaban			=$row["ctaban"]; $this->io_ds_bancos->insertRow("ctaban",$ls_ctaban);
				$ls_numdoc			=$row["numdoc"]; $this->io_ds_bancos->insertRow("numdoc",$ls_numdoc);
				$ls_codope			=$row["codope"]; $this->io_ds_bancos->insertRow("codope",$ls_codope);
				$ls_estmov			=$row["estmov"]; $this->io_ds_bancos->insertRow("estmov",$ls_estmov);
				$ls_cod_pro			=$row["cod_pro"]; $this->io_ds_bancos->insertRow("cod_pro",$ls_cod_pro);
				$ls_ced_bene		=$row["ced_bene"]; $this->io_ds_bancos->insertRow("ced_bene",$ls_ced_bene);
				$ls_tipo_destino	=$row["tipo_destino"]; $this->io_ds_bancos->insertRow("tipo_destino",$ls_tipo_destino);
				$ls_codconmov		=$row["codconmov"]; $this->io_ds_bancos->insertRow("codconmov",$ls_codconmov);
				$ld_femov			=$row["fecmov"]; $this->io_ds_bancos->insertRow("femov",$ld_femov);
				$ls_conmov			=$row["conmov"]; $this->io_ds_bancos->insertRow("conmov",$ls_conmov);
				$ls_nomproben		=$row["nomproben"]; $this->io_ds_bancos->insertRow("nomproben",$ls_nomproben);
				$li_monto			=$row["monto"]; $this->io_ds_bancos->insertRow("monto",$li_monto);
				$ls_estbpd			=$row["estbpd"]; $this->io_ds_bancos->insertRow("estbpd",$ls_estbpd);
				$ls_estcon			=$row["estcon"]; $this->io_ds_bancos->insertRow("estcon",$ls_estcon);
				$ls_estcobing		=$row["estcobing"]; $this->io_ds_bancos->insertRow("estcobing",$ls_estcobing);
				$ls_chevau			=$row["chevau"]; $this->io_ds_bancos->insertRow("chevau",$ls_chevau);
				$ls_estimpche		=$row["estimpche"]; $this->io_ds_bancos->insertRow("estimpche",$ls_estimpche);
				$li_monobjret		=$row["monobjret"]; $this->io_ds_bancos->insertRow("monobjret",$li_monobjret);
				$ls_monret			=$row["monret"]; $this->io_ds_bancos->insertRow("monret",$ls_monret);
				$ls_procede			=$row["procede"]; $this->io_ds_bancos->insertRow("procede",$ls_procede);
				$ls_comprobante		=$row["comprobante"]; $this->io_ds_bancos->insertRow("comprobante",$ls_comprobante);
				$ld_fecha			=$row["fecha"]; $this->io_ds_bancos->insertRow("fecha",$ld_fecha);
				$ls_idmco			=$row["id_mco"]; $this->io_ds_bancos->insertRow("id_mco",$ls_idmco);
				$ls_emichepro		=$row["emicheproc"]; $this->io_ds_bancos->insertRow("emicheproc",$ls_emichepro);
				$ls_emicheced		=$row["emicheced"]; $this->io_ds_bancos->insertRow("emicheced",$ls_emicheced);
				$ls_emichenom		=$row["emichenom"]; $this->io_ds_bancos->insertRow("emichenom",$ls_emichenom);
				$ls_emichefec		=$row["emichefec"]; $this->io_ds_bancos->insertRow("emichefec",$ls_emichefec);
				$ls_estmovint		=$row["estmovint"]; $this->io_ds_bancos->insertRow("estmovint",$ls_estmovint);
				$ls_codusu			=$row["codusu"]; $this->io_ds_bancos->insertRow("codusu",$ls_codusu);
				$ls_codopeidb		=$row["codopeidb"]; $this->io_ds_bancos->insertRow("codopeidb",$ls_codopeidb);
				$ls_aliidb			=$row["aliidb"]; $this->io_ds_bancos->insertRow("aliidb",$ls_aliidb);
				$ls_feccon			=$row["feccon"]; $this->io_ds_bancos->insertRow("feccon",$ls_feccon);
				$ls_estreglib		=$row["estreglib"]; $this->io_ds_bancos->insertRow("estreglib",$ls_estreglib);
				$ls_numcarord		=$row["numcarord"]; $this->io_ds_bancos->insertRow("numcarord",$ls_numcarord);
				$ls_numpolcon		=$row["numpolcon"]; $this->io_ds_bancos->insertRow("numpolcon",$ls_numpolcon);
				$ls_coduniadmsig	=$row["coduniadmsig"]; $this->io_ds_bancos->insertRow("coduniadmsig",$ls_coduniadmsig);
				$ls_codbansig		=$row["codbansig"]; $this->io_ds_bancos->insertRow("codbansig",$ls_codbansig);
				$ls_fecordpagsig	=$row["fecordpagsig"]; $this->io_ds_bancos->insertRow("fecordpagsig",$ls_fecordpagsig);
				$ls_tipdocressig	=$row["tipdocressig"]; $this->io_ds_bancos->insertRow("tipdocressig",$ls_tipdocressig);
				$ls_numdocressig	=$row["numdocressig"]; $this->io_ds_bancos->insertRow("numdocressig",$ls_numdocressig);
				$ls_estmodordpag	=$row["estmodordpag"]; $this->io_ds_bancos->insertRow("estmodordpag",$ls_estmodordpag);
				$ls_codfuefin		=$row["codfuefin"]; $this->io_ds_bancos->insertRow("codfuefin",$ls_codfuefin);
				$ls_forpagsig		=$row["forpagsig"]; $this->io_ds_bancos->insertRow("forpagsig",$ls_forpagsig);
				$ls_medpagsig		=$row["medpagsig"]; $this->io_ds_bancos->insertRow("medpagsig",$ls_medpagsig);
				$ls_codestprosig	=$row["codestprosig"]; $this->io_ds_bancos->insertRow("codestprosig",$ls_codestprosig);
				$ls_nrocontrolop	=$row["nrocontrolop"]; $this->io_ds_bancos->insertRow("nrocontrolop",$ls_nrocontrolop);
				$ls_fechaconta		=$row["fechaconta"]; $this->io_ds_bancos->insertRow("fechaconta",$ls_fechaconta);
				$ls_fechaanula		=$row["fechaanula"]; $this->io_ds_bancos->insertRow("fechaanula",$ls_fechaanula);
				$ls_conanu			=$row["conanu"]; $this->io_ds_bancos->insertRow("conanu",$ls_conanu);
				$ls_estant			=$row["estant"]; $this->io_ds_bancos->insertRow("estant",$ls_estant);
				$ls_docant			=$row["docant"]; $this->io_ds_bancos->insertRow("docant",$ls_docant);
				$ls_monamo			=$row["monamo"]; $this->io_ds_bancos->insertRow("monamo",$ls_monamo);
				$ls_numordpagmin	=$row["numordpagmin"]; $this->io_ds_bancos->insertRow("numordpagmin",$ls_numordpagmin);
				$ls_codtipfon		=$row["codtipfon"]; $this->io_ds_bancos->insertRow("codtipfon",$ls_codtipfon);
				$ls_estserext		=$row["estserext"]; $this->io_ds_bancos->insertRow("estserext",$ls_estserext);
				$ls_estmovcob		=$row["estmovcob"]; $this->io_ds_bancos->insertRow("estmovcob",$ls_estmovcob);
				$ls_numconint		=$row["numconint"]; $this->io_ds_bancos->insertRow("numconint",$ls_numconint);
				return true; 
			}
			else
			{
				return false;
			}	
		}
	}

	function uf_guardar_automatico($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,
				       $ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_estdoc,
				       $ls_tipproben,$ls_codfuefin,$as_numordpagmin,$as_codtipfon,$li_estmovcob="0",$as_numcontint="",$as_reglibro="",$ls_codmon="---",$ldec_tascam=1,$ldec_montot=0)
	{								
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que procesa los datos de la cabecera del movimiento bancario
		//	validando que no exista y que el periodo este abierto.
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_codusu=$_SESSION["la_logusr"];
		$as_numcontint="";
                if ($ldec_montot==0)
                {
                    $ldec_montot=$ldec_monto;
                }
		if($this->io_fecha->uf_valida_fecha_periodo($ldt_fecha,$ls_codemp))
		{
			   if(!$this->uf_select_movimiento($ls_numdoc,$ls_codope,$ls_codban,$ls_ctaban,$ls_estmov))
			   {
				 //  $this->io_sql->begin_transaction();
				   $arrResultado="";
				   $lb_valido="";
				   $arrResultado = $this->uf_insert_movimiento($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben,$as_numordpagmin,$as_codtipfon,$li_estmovcob,$as_numcontint,$as_reglibro,$ls_codmon,$ldec_tascam,$ldec_montot);
				   $lb_valido=$arrResultado["lb_valido"];
				   $as_numcontint=$arrResultado["as_numcontint"];
				   if($lb_valido)
				   {
						$lb_valido = $this->uf_insert_fuentefinancimiento($ls_codemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ls_estmov,$ls_codfuefin);
				   }
				   $ib_valido = $lb_valido;
				   if($lb_valido)
				   {
						$ib_new = false;
				   }	
			   }
			   elseif($ls_estdoc=='C')
			   {
					
					$lb_valido=true;
					$lb_valido=$this->uf_update_movimiento($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben,$li_estmovcob,$as_numcontint,$as_reglibro);
			   }
			   else
			   {
					$lb_valido=false;   
					$this->is_msg_error="El numero de documento ya existe";
			   }
		}
		else
		{
				$this->msg->message($this->io_fecha->is_msg_error);           
		}
		$arrResultado["lb_valido"]=$lb_valido;					
		$arrResultado["as_numcontint"]=$as_numcontint;					
		return $arrResultado;
	}

function uf_insert_movimiento($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,
							  $ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,
							  $ls_estreglib,$ls_tipproben,$as_numordpagmin,$as_codtipfon,$li_estmovcob,$as_numcontint,$as_reglibro,$ls_codmon,$ldec_tascam,$ldec_montot)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta la cabecera del movimiento  bancario
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido=true;		
    $ls_gestor = $_SESSION["ls_gestor"];
	$ldt_fecha=$this->fun->uf_convertirdatetobd($ldt_fecha);
	$ls_procede='SCBBCH';
	if ($ldec_monto=="")
	{
		$ldec_monto=0;
	}
	if ($ldec_monobjret=="")
	{
		$ldec_monobjret=0;
	}
	if ($ldec_monret=="")
	{
		$ldec_monret=0;
	}
	
	switch ($ls_codope) {
		case "ND":
			$ls_procede='SCBBND';
			break;
		
		case "NC":
			$ls_procede='SCBBNC';
			break;
		
		case "DP":
			$ls_procede='SCBBDP';
			break;
		
		case "RE":
			$ls_procede='SCBBRE';
			break;
			
		case "CH":
			$ls_procede='SCBBCH';
			break;
	}
	
	if (($ls_codope=="CH")&&($ls_chevau!=""))
	   {
	     $ls_chevau= str_pad($ls_chevau,25,"0",STR_PAD_LEFT);
	   }
	if (empty($as_numordpagmin))
	   {
	     $as_numordpagmin = '-';
	   }
	if (empty($as_codtipfon))
	   {
	     $as_codtipfon = '----';
	   }
	if (strlen($ls_nomproben)>100)
	{
		$ls_nomproben=substr($ls_nomproben,0,100);
	}
	// se le quitan los espacios en blanco al nro de control interno ya que cuando se guardan con espacio en blanco da error al contabilizar el movimiento
	$as_numcontint=trim($as_numcontint);
	if(empty($as_numcontint)||($as_numcontint==='')||($ls_codope=="CH"))
	{
		$io_keygen= new sigesp_c_generar_consecutivo();
		$as_numcontint=NULL;
		//$as_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBCH",15,"","","");//Ajuste a la emisión de cheque
		$as_numcontint = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','numconint','SCBBCH',15,'','','',$this->la_security["logusr"]);
		$as_numcontintaux=$as_numcontint;
	}
	if (($ls_gestor=='oci8po')&&($ls_codope=="RE")&&($as_numcontint==''))
	{
		$io_keygen= new sigesp_c_generar_consecutivo();
		//$as_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBCH",15,"","","");
		$as_numcontint = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','numconint','SCBBCH',15,'','','',$this->la_security["logusr"]);
		unset($io_keygen);
	}
	if (($ls_gestor=='oci8po')&&($ls_codope=="CH")&&($as_numcontint==''))
	{
		$io_keygen= new sigesp_c_generar_consecutivo();
		$as_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBCH",15,"","","");
		unset($io_keygen);
	}
	if(($ls_codope=="NC")||($ls_codope=="ND")||($ls_codope=="DP"))
	{
		$as_numcontintaux=$as_numcontint;
		//$arrResultado= $this->io_keygen->uf_verificar_numero_generado("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","",$_SESSION["la_logusr"]);
		$arrResultado=$this->io_keygen->uf_verificar_numero_generado2("SCB","scb_movbco","numconint","SCBBRE",15,"valinimovban","","",$as_numcontint,$_SESSION["la_logusr"]);
		$lb_valido=$arrResultado["lb_valido"];
		$as_numcontint=$arrResultado["as_numero"];
		if (($ls_gestor=='oci8po')&&($as_numcontint==''))
		{
			$io_keygen= new sigesp_c_generar_consecutivo();
			//$as_numcontint= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco","numconint","SCBBCH",15,"","","");
		    $as_numcontint = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco','numconint','SCBBCH',15,'','','',$this->la_security["logusr"]);
			unset($io_keygen);
		}
	}
	if ($as_reglibro=='1')
	{
		$ls_estreglib='A';
	}
	$ls_sql="INSERT INTO scb_movbco(codemp,codusu,codban,ctaban,numdoc,codope,fecmov,conmov,codconmov,cod_pro,ced_bene,nomproben,
					monto,monobjret,monret,chevau,estmov,estmovint,estcobing,esttra,estbpd,estcon,feccon,estreglib,
					tipo_destino,fecha,procede, codfuefin,numordpagmin,codtipfon,estmovcob,numconint,tranoreglib,codmon,tascam,montot)
		 VALUES                ('".$ls_codemp."','".$ls_codusu."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."',
					'".$ls_codope."','".$ldt_fecha."','".$ls_conmov."','".$ls_codconmov."','".$ls_codpro."',
					'".$ls_cedbene."','".$ls_nomproben."',".$ldec_monto.",".$ldec_monobjret.",".$ldec_monret.",
					'".$ls_chevau."','".$ls_estmov."',".$li_estmovint.",".$li_cobrapaga.", 0    ,'".$ls_estbpd."',
					0  ,'1900-01-01','".$ls_estreglib."','".$ls_tipproben."','1900-01-01','".$ls_procede."','--','".$as_numordpagmin."',
                                        '".$as_codtipfon."','".$li_estmovcob."','".$as_numcontint."','".$as_reglibro."','".$ls_codmon."',".$ldec_tascam.",".$ldec_montot.")";										

	/*if(empty($as_numcontint)||($as_numcontint==='')||($ls_codope=="CH"))
	{
		$ls_sql="INSERT INTO scb_movbco(codemp,codusu,codban,ctaban,numdoc,codope,fecmov,conmov,codconmov,cod_pro,ced_bene,nomproben,
										 monto,monobjret,monret,chevau,estmov,estmovint,estcobing,esttra,estbpd,estcon,feccon,estreglib,
										 tipo_destino,fecha,procede, codfuefin,numordpagmin,codtipfon,estmovcob,tranoreglib)
				 VALUES                ('".$ls_codemp."','".$ls_codusu."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."',
										'".$ls_codope."','".$ldt_fecha."','".$ls_conmov."','".$ls_codconmov."','".$ls_codpro."',
										'".$ls_cedbene."','".$ls_nomproben."',".$ldec_monto.",".$ldec_monobjret.",".$ldec_monret.",
										'".$ls_chevau."','".$ls_estmov."',".$li_estmovint.",".$li_cobrapaga.", 0    ,'".$ls_estbpd."',
											0  ,'1900-01-01','".$ls_estreglib."','".$ls_tipproben."','1900-01-01','".$ls_procede."','--','".$as_numordpagmin."','".$as_codtipfon."','".$li_estmovcob."','".$as_reglibro."')";										
	}
	else
	{
		$ls_sql="INSERT INTO scb_movbco(codemp,codusu,codban,ctaban,numdoc,codope,fecmov,conmov,codconmov,cod_pro,ced_bene,nomproben,
										 monto,monobjret,monret,chevau,estmov,estmovint,estcobing,esttra,estbpd,estcon,feccon,estreglib,
										 tipo_destino,fecha,procede, codfuefin,numordpagmin,codtipfon,estmovcob,numconint,tranoreglib)
				 VALUES                ('".$ls_codemp."','".$ls_codusu."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."',
										'".$ls_codope."','".$ldt_fecha."','".$ls_conmov."','".$ls_codconmov."','".$ls_codpro."',
										'".$ls_cedbene."','".$ls_nomproben."',".$ldec_monto.",".$ldec_monobjret.",".$ldec_monret.",
										'".$ls_chevau."','".$ls_estmov."',".$li_estmovint.",".$li_cobrapaga.", 0    ,'".$ls_estbpd."',
											0  ,'1900-01-01','".$ls_estreglib."','".$ls_tipproben."','1900-01-01','".$ls_procede."','--','".$as_numordpagmin."','".$as_codtipfon."','".$li_estmovcob."','".$as_numcontint."','".$as_reglibro."')";										
	}*/
	$li_result=$this->io_sql->execute($ls_sql);
	if(($li_result===false))
	{
		$this->io_sql->rollback();
		if($this->io_sql->errno=='23505' || $this->io_sql->errno=='1062' || $this->io_sql->errno=='-239' || $this->io_sql->errno=='-5'|| $this->io_sql->errno=='-1')
		{
			$arrResultado="";
			$lb_valido="";
			$as_numcontint="";
			$arrResultado=$this->uf_insert_movimiento($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben,$as_numordpagmin,$as_codtipfon,$li_estmovcob,$as_numcontint,$as_reglibro);
			$lb_valido=$arrResultado["lb_valido"];
			$as_numcontint=$arrResultado["as_numcontint"];
		}
		else
		{
			$this->is_msg_error="Fallo insercion de movimiento, ".$this->fun->uf_convertirmsg($this->io_sql->message);		
			return false;
		}
	}
	else
	{
		$this->is_msg_error="El movimiento Bancario fue registrado";
		if($as_numcontintaux!=$as_numcontint)
		{
			$this->is_msg_error=$this->is_msg_error." Se le asigno un nuevo numero de control ".$as_numcontint;
		}
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="INSERT";
		$ls_descripcion="Inserto el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban." por un monto de ".$ldec_monto;
		//$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="UPDATE scb_movbco SET numconint=null WHERE numconint=''";
		$li_result=$this->io_sql->execute($ls_sql);
	}
	$arrResultado["lb_valido"]=$lb_valido;
	$arrResultado["as_numcontint"]=$as_numcontint;
	return $arrResultado;		
}

function uf_update_movimiento($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,
							  $ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,
							  $ls_estreglib,$ls_tipproben,$li_estmovcob,$as_numcontint,$as_reglibro)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta la cabecera del movimiento  bancario
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$ldt_fecha=$this->fun->uf_convertirdatetobd($ldt_fecha);
	if ($ldec_monto=="")
	{
		$ldec_monto=0;
	}
	if ($ldec_monobjret=="")
	{
		$ldec_monobjret=0;
	}
	if ($ldec_monret=="")
	{
		$ldec_monret=0;
	}
	$ls_sql="UPDATE scb_movbco SET conmov='".$ls_conmov."',codconmov='".$ls_codconmov."',cod_pro='".$ls_codpro."',ced_bene='".$ls_cedbene."',nomproben='".$ls_nomproben."',monto=".$ldec_monto.",monobjret=".$ldec_monobjret.",monret=".$ldec_monret.",tipo_destino='$ls_tipproben',tranoreglib='".$as_reglibro."'
			 WHERE codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND numdoc='".$ls_numdoc."' AND codope='".$ls_codope."'";

	$li_result=$this->io_sql->execute($ls_sql);
	if($li_result===false)
	{
		$this->is_msg_error=" Fallo Actualizacion de movimiento, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		return false;
	}
	else
	{
		$this->is_msg_error="El movimiento Bancario fue Actualizado";
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="UPDATE";
		$ls_descripcion="Actualizo el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban." por un monto de ".$ldec_monto;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////			
		return $lb_valido;
	}
	
}

function uf_select_dt_contable($arr_movbco,$ls_cuenta,$ls_procede,$ls_descripcion,$ls_documento,$ls_operacioncon,$ldec_monto,$ldec_actual,$ls_codded)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que verifica si existe el movimiento contable
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];
    $ls_codban     = trim($arr_movbco["codban"]);
 	$ls_ctaban     = trim($arr_movbco["ctaban"]);
	$ls_numdoc     = $arr_movbco["mov_document"];
	$ls_codope     = $arr_movbco["codope"];
	$ls_estmov	   = $arr_movbco["estmov"];
	$ldec_monobjret= $arr_movbco["objret"];	 
	
	$ls_sql="SELECT monto 
			   FROM scb_movbco_scg
			  WHERE codemp='".$ls_codemp."'
			    AND codban='".$ls_codban."'
				AND ctaban='".$ls_ctaban."'
				AND numdoc='".$ls_numdoc."' 
			    AND codope='".$ls_codope."'
				AND estmov='".$ls_estmov."'
				AND scg_cuenta='".$ls_cuenta."' 
			    AND debhab='".$ls_operacioncon."'
				AND codded='".$ls_codded."'
				AND documento='$ls_documento'";
	$rs_dt_scg=$this->io_sql->select($ls_sql);
	if(($rs_dt_scg===false))
	{
		$this->is_msg_error="Error en select detalle contable ".$this->fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido=false;
	}
	else
	{
		if($row=$this->io_sql->fetch_row($rs_dt_scg))
		{
			$lb_valido=true;
			$ldec_actual=$row["monto"];
		}
		else
		{
			$lb_valido=false;
		}
	}	
	$arrResultado["lb_valido"]=$lb_valido;
	$arrResultado["ldec_actual"]=$ldec_actual;
	return $arrResultado;
}

function  uf_procesar_dt_contable($arr_movbco,$ls_cuenta,$ls_procede,$ls_descripcion,$ls_documento,$ls_operacioncon,$ldec_monto,$ldec_objret,$lb_mov_mandatory,$ls_codded)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta el detalle contable del movimiento 
	//
	///////////////////////////////////////////////////////////////////////////////////////////////	
	$dat       = $_SESSION["la_empresa"];
	$ls_codemp = $dat["codemp"];
    $ls_codban = $arr_movbco["codban"];
 	$ls_ctaban = $arr_movbco["ctaban"];
	$ls_numdoc = $arr_movbco["mov_document"];
	$ls_codope = $arr_movbco["codope"];
	$ls_estmov = $arr_movbco["estmov"];
	$arrResultado="";
	$ldec_actual="";
	$lb_valido="";
	$arrResultado = $this->uf_select_dt_contable($arr_movbco,$ls_cuenta,$ls_procede,$ls_descripcion,$ls_documento,$ls_operacioncon,$ldec_monto,$ldec_actual,$ls_codded);
	$ldec_actual=$arrResultado["ldec_actual"];
	$lb_valido=$arrResultado["lb_valido"];
	if(!$lb_valido)
	{
			$ls_sql="INSERT INTO scb_movbco_scg(codemp,codban,ctaban,numdoc,codope,estmov,scg_cuenta,debhab,documento,codded,desmov,procede_doc,monto,monobjret)
					 VALUES ('".$ls_codemp."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."','".$ls_codope."','".$ls_estmov."','".$ls_cuenta."','".$ls_operacioncon."','".$ls_documento."','".$ls_codded."','".$ls_descripcion."','".$ls_procede."',".$ldec_monto.",".$ldec_objret.")";
			$li_result=$this->io_sql->execute($ls_sql);
			if(($li_result===false))
			{
				$this->is_msg_error="Error al procesar detalle contable, ".$this->fun->uf_convertirmsg($this->io_sql->message);
				$lb_valido=false;			
			}
			else
			{
				$lb_valido=true;
				///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
				$ls_evento="INSERT";
				$ls_descripcion="Inserto el detalle contable a la cuenta ".$ls_cuenta." por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		        $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
				////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
	}
	else
	{   
		$ldec_monto=$ldec_monto+$ldec_actual;
		$ls_sql="UPDATE scb_movbco_scg ".
		        "   SET monto=".$ldec_monto." ".
				" WHERE codemp='".$ls_codemp."'     AND codban='".$ls_codban."'      ".
				"   AND ctaban='".$ls_ctaban."'     AND numdoc='".$ls_numdoc."'      ".
				"   AND codope='".$ls_codope."'     AND estmov='".$ls_estmov."'      ".
				"   AND scg_cuenta='".$ls_cuenta."' AND debhab='".$ls_operacioncon."'".
				"   AND codded='".$ls_codded."'     AND documento='".$ls_documento."'";
		if(($lb_valido)&&(!$lb_mov_mandatory))
		{
			$li_result=$this->io_sql->execute($ls_sql);	

			if(($li_result===false))
			{
				$this->is_msg_error="Error al procesar detalle contable, ".$this->fun->uf_convertirmsg($this->io_sql->message);
				$lb_valido=false;			
			}
			else
			{
				$lb_valido=true;
				///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
				$ls_evento="UPDATE";
				$ls_descripcion="Actualizo el detalle contable a la cuenta ".$ls_cuenta." por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		        $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
				////////////////////////////////////////////////////////////////////////////////////////////////////////////				
			}
		}		
	}
	return $lb_valido;
}

function uf_update_monto_mov($arr_movbco,$ls_cuenta,$ls_procede,$ls_descripcion,$ls_documento,$ls_operacioncon,$ldec_monto,$ldec_objret,$ls_codded)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que actualiza el monto de un movimiento cuando se selecciona la misma cuenta
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
		
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_codban = $arr_movbco["codban"];
 		$ls_ctaban = $arr_movbco["ctaban"];
		$ls_numdoc = $arr_movbco["mov_document"];
		$ls_codope = $arr_movbco["codope"];
		$ls_estmov = $arr_movbco["estmov"];
		
		$ls_sql="UPDATE scb_movbco_scg SET monto=".$ldec_monto." 
				 WHERE codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND numdoc='".$ls_numdoc."' AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' 
				 AND scg_cuenta='".$ls_cuenta."' AND debhab='".$ls_operacioncon."' AND codded='".$ls_codded."' AND documento='".$ls_documento."'";

		$li_result=$this->io_sql->execute($ls_sql);	

		if($li_result===false)
		{
			$this->is_msg_error="Error al procesar detalle contable, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;			
		}
		else
		{
			$lb_valido=true;			
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="UPDATE";
			$ls_descripcion="Actualizo el detalle contable a la cuenta ".$ls_cuenta." por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		    $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
	return $lb_valido;
}

function uf_update_montodelete($arr_movbco,$ls_cuenta,$ls_procede,$ls_descripcion,$ls_documento,$ls_operacioncon,$ldec_monto,$ldec_objret,$ls_codded)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que actualiza el monto del movimiento padre cuando se elimina una retencion
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
		
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_codban     = $arr_movbco["codban"];
 		$ls_ctaban     = $arr_movbco["ctaban"];
		$ls_numdoc     = $arr_movbco["mov_document"];
		$ls_codope     = $arr_movbco["codope"];
		$ls_estmov     = $arr_movbco["estmov"];
		
		$ls_sql="UPDATE scb_movbco_scg SET monto=monto + ".$ldec_monto." 
				 WHERE codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND numdoc='".$ls_numdoc."' AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' 
				 AND scg_cuenta='".$ls_cuenta."' AND debhab='".$ls_operacioncon."' AND codded='".$ls_codded."' AND documento='".$ls_documento."'";
		$li_result=$this->io_sql->execute($ls_sql);	
		if(($li_result===false))
		{
			$this->is_msg_error="Error al procesar detalle contable, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;			
		}
		else
		{
			
			//$this->uf_update_montos_auxiliares_movbco_scg($ls_codemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ls_estmov,$ls_cuenta,$ls_operacioncon,$ls_codded,$ls_documento);
			
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="UPDATE";
			$ls_descripcion="Actualizo el detalle contable a la cuenta ".$ls_cuenta." por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		    $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}

	return $lb_valido;
}

function uf_select_dt_gasto($ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_programa,$ls_spgcuenta,$ls_documento,$as_estcla,$as_codfuefin = '--')
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que verifica si existe el movimiento contable
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido = false;
	$ls_codemp = $_SESSION["la_empresa"]["codemp"];
   	
	$ls_sql="SELECT codemp 
			   FROM scb_movbco_spg
			  WHERE codemp='".$ls_codemp."' 
			    AND codban='".$ls_codban."' 
				AND ctaban='".$ls_ctaban."' 
			    AND numdoc='".$ls_numdoc."' 
				AND codope='".$as_codope."' 
				AND estmov='".$ls_estmov."' 
			    AND spg_cuenta='".$ls_spgcuenta."' 
				AND codestpro='".$ls_programa."' 
				AND documento='".$ls_documento."'
				AND estcla='".$as_estcla."' 
			    AND codfuefin='".$as_codfuefin."'";
			
	$rs_data = $this->io_sql->select($ls_sql);
	if ($rs_data===false)
	   {
	     $this->is_msg_error="Error en select detalle Presupuestario de gasto ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  if($row["codemp"]!="")
				return true;
				else
				return false;
		    }
	} 	
	return $lb_valido;
}


function uf_procesar_dt_gasto($ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_programa,$ls_spgcuenta,$ls_documento,$ls_desmov,$ls_procededoc,$ldec_monto,$ls_operacion,$as_estcla,$as_codfuefin)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta el detalle presupuestario del movimiento 
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	if ($as_codfuefin=='')
	{
		$as_codfuefin='--';
	}
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];	
	$ls_desmov=str_replace("'","",$ls_desmov);
	$lb_existe=$this->uf_select_dt_gasto($ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_programa,$ls_spgcuenta,$ls_documento,$as_estcla,$as_codfuefin);
	if(!$lb_existe)
	{
		$ls_sql="INSERT INTO scb_movbco_spg(codemp,codban,ctaban,numdoc,codope,estmov,codestpro,spg_cuenta,documento,desmov,procede_doc,monto,operacion,estcla,codfuefin)
				 VALUES ('".$ls_codemp."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."','".$as_codope."','".$ls_estmov."','".$ls_programa."','".$ls_spgcuenta."','".$ls_documento."','".$ls_desmov."','".$ls_procededoc."',".$ldec_monto.",'".$ls_operacion."','".$as_estcla."','".$as_codfuefin."')";
		$ls_evento="INSERT";
		$this->is_msg_error="Registro Insertado";
		$ls_descripcion="Inserto el detalle presupuestario a la cuenta ".$ls_spgcuenta." asociado a la programatica ".$ls_programa." de tipo $as_estcla, por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$as_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
	}
	else
	{
		$ls_sql="UPDATE scb_movbco_spg 
				    SET monto=monto+".$ldec_monto."
				  WHERE codemp='".$ls_codemp."' 
				    AND codban='".$ls_codban."'
					AND ctaban='".$ls_ctaban."' 
				    AND numdoc='".$ls_numdoc."'
					AND codope='".$as_codope."'
					AND estmov='".$ls_estmov."' 
				    AND codestpro='".$ls_programa."'
					AND spg_cuenta='".$ls_spgcuenta."'
					AND documento='".$ls_documento."'
					AND estcla='".$as_estcla."'
					AND codfuefin='".$as_codfuefin."' ";
		$ls_evento="UPDATE";
		$this->is_msg_error="Registro Actualizado";
		$ls_descripcion="Actualizo el detalle presupuestario a la cuenta ".$ls_spgcuenta." asociado a la programatica ".$ls_programa." de tipo $as_estcla, por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$as_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
    }
	$li_result=$this->io_sql->execute($ls_sql);
	
	if(($li_result===false))
	{
		$this->is_msg_error="Error al guardar detalle de gasto, ".$this->fun->uf_convertirmsg($this->io_sql->message);		
		$lb_valido=false;
	}
	else
	{
		if ($lb_existe)
		   {	 
			 $this->uf_update_montos_auxiliares_movbco_spg($ls_codemp,$ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_programa,$ls_spgcuenta,$ls_documento,$as_estcla);
		   }   
		$lb_valido=true;
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	
	return $lb_valido;
}

function uf_select_dt_ingreso($ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_spicuenta,$ls_documento,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que verifica si existe el movimiento contable
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];
   	
	$ls_sql="SELECT monto 
			   FROM scb_movbco_spi
			  WHERE codemp='".$ls_codemp."'
			    AND codban='".$ls_codban."'
				AND ctaban='".$ls_ctaban."' 
			    AND numdoc='".$ls_numdoc."'
				AND codope='".$as_codope."'
				AND estmov='".$ls_estmov."' 
			    AND spi_cuenta='".$ls_spicuenta."'
				AND documento='".$ls_documento."'
				AND codestpro1 = '".$as_codestpro1."'
				AND codestpro2 = '".$as_codestpro2."'
				AND codestpro3 = '".$as_codestpro3."'
				AND codestpro4 = '".$as_codestpro4."'
				AND codestpro5 = '".$as_codestpro5."'
				AND estcla = '".$as_estcla."'";
			
	$rs_dt_scg=$this->io_sql->select($ls_sql);
	if ($rs_dt_scg===false)
	   {
		 $this->is_msg_error="Error en select detalle de ingreso ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_dt_scg))
		    {
			  $lb_valido=true;
			  $ldec_actual=$row["monto"];
			  unset($rs_dt_scg,$row);
		    }
		 else
		    {
			  $lb_valido=false;
		    }
	   } 	
	return $lb_valido;
}

function uf_procesar_dt_ingreso($ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_spicuenta,$ls_documento,$ls_desmov,$ls_procededoc,$ldec_monto,$ls_operacion,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta el detalle presupuestario del movimiento 
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];	

	$lb_existe = $this->uf_select_dt_ingreso($ls_codban,$ls_ctaban,$ls_numdoc,$as_codope,$ls_estmov,$ls_spicuenta,$ls_documento,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla);
	if (!$lb_existe)
	   {
		 $ls_sql="INSERT INTO scb_movbco_spi(codemp,codban,ctaban,numdoc,codope,estmov,spi_cuenta,documento,desmov,procede_doc,monto,operacion,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla)
				  VALUES ('".$ls_codemp."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."','".$as_codope."','".$ls_estmov."','".$ls_spicuenta."','".$ls_documento."','".$ls_desmov."','".$ls_procededoc."',".$ldec_monto.",'".$ls_operacion."',
				          '".$as_codestpro1."','".$as_codestpro2."','".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."','".$as_estcla."')";
		 $ls_evento="INSERT";
		 $this->is_msg_error="Registro Insertado";
	     $ls_descripcion="Inserto el detalle de ingreso a la cuenta ".$ls_spicuenta."  por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$as_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
	   }
	else
	   {
		 $ls_sql="UPDATE scb_movbco_spi 
				     SET monto=monto+".$ldec_monto."
				   WHERE codemp='".$ls_codemp."'
				     AND codban='".$ls_codban."'
					 AND ctaban='".$ls_ctaban."' 
				     AND numdoc='".$ls_numdoc."'
					 AND codope='".$as_codope."'
					 AND estmov='".$ls_estmov."' 
				     AND TRIM(spi_cuenta)='".trim($ls_spicuenta)."'
					 AND documento='".$ls_documento."'
					 AND codestpro1='".$as_codestpro1."'
					 AND codestpro2='".$as_codestpro2."'
					 AND codestpro3='".$as_codestpro3."'
					 AND codestpro4='".$as_codestpro4."'
					 AND codestpro5='".$as_codestpro5."'
					 AND estcla='".$as_estcla."'";
		 $ls_evento="UPDATE";
		 $this->is_msg_error="Registro Actualizado";
		 $ls_descripcion="Actualizo el detalle de ingreso a la cuenta ".$ls_spicuenta." por un monto de ".$ldec_monto." para el movimiento bancario de operacion ".$as_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
	   }
	
	$rs_data = $this->io_sql->execute($ls_sql);	
	if ($rs_data===false)
	   {
		 $this->is_msg_error="Error al guardar detalle de ingreso, ".$this->fun->uf_convertirmsg($this->io_sql->message);		
		 $lb_valido=false;
	   }
	else
	   {
	     $lb_valido=true;
		 ///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		 $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   }
	return $lb_valido;
}

function uf_delete_dt_spi($ls_mov_document,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ls_numdoc,$ls_cuenta_spi,$ls_operacion,$ldec_monto,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que elimina el detalle presupuestario del movimiento 
	//  junto con el contable asociado a la cuenta de presupuesto.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$ls_codemp     = $_SESSION["la_empresa"]["codemp"];	
	$ls_cuenta_scg = $this->uf_select_cuenta_contable($ls_codemp,$ls_cuenta_spi);
	
	$ls_sql=" DELETE FROM scb_movbco_spi 
			   WHERE codemp='".$ls_codemp."'
			     AND codban='".$ls_codban."'
				 AND ctaban='".$ls_ctaban."' 
			     AND numdoc='".$ls_mov_document."'
				 AND codope='".$ls_codope."'
				 AND estmov='".$ls_estmov."'
				 AND operacion='".$ls_operacion."' 
			     AND documento='".$ls_numdoc."'
				 AND TRIM(spi_cuenta) = '".trim($ls_cuenta_spi)."'
				 AND codestpro1 = '".$as_codestpro1."'
				 AND codestpro2 = '".$as_codestpro2."'
				 AND codestpro3 = '".$as_codestpro3."'
				 AND codestpro4 = '".$as_codestpro4."'
				 AND codestpro5 = '".$as_codestpro5."'";
				 //AND estcla = '".$as_estcla."'";//NO ELIMINAR!! Ing. Carlos Zambrano
	$li_result=$this->io_sql->execute($ls_sql);				  

	if($li_result===false)	
	{
		$this->is_msg_error="Error al eliminar registro, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido=false;
	}
	else
	{
		$lb_valido=true;
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="DELETE";
		$ls_descripcion="Elimino el detalle de ingreso a la cuenta ".$ls_cuenta_spi." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=$this->uf_delete_dt_scg($ls_mov_document,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ls_numdoc,$ls_cuenta_scg,'H','00000',$ldec_monto,'SPG');
		
		$this->is_msg_error="El detalle de ingreso fue eliminado";				
	}
	return $lb_valido;
}

function uf_cargar_dt($as_numdoc,$as_codban,$as_ctaban,$as_codope,$ls_estmov,$objectScg,$li_row_scg,$ldec_mondeb,$ldec_monhab,$objectSpg,$li_temp_spg,$ldec_monto_spg,$objectSpi,$li_temp_spi,$ldec_monto_spi)
{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que carga todos los detalles del movimiento de banco en los object 
		//	requeridos por la clase grid_param.
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		
		$li_row_scg=0;
		$li_temp_spg=0;
		$li_temp_spi=0;
		$li_temp_ret=0;
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_sql="SELECT codban,ctaban,codope,estmov,scg_cuenta,codded,debhab,documento,desmov,procede_doc,monto,monobjret
				   FROM scb_movbco_scg
				  WHERE codemp='".$ls_codemp ."'
				    AND numdoc ='".$as_numdoc."' 
					AND codban='".$as_codban."' 
					AND ctaban='".$as_ctaban."' 
					AND codope='".$as_codope."'
					AND estmov='".$ls_estmov."'	
				  ORDER BY debhab,numdoc ASC"; //print $ls_sql;
				 
		$rs_data=$this->io_sql->select($ls_sql);		
		if ($rs_data===false)
		   {
			 $this->is_msg_error="Error en inserción, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			 $lb_valido=false;
		   }
		else
		   {
		     while (!$rs_data->EOF)
			       {        
					 $li_row_scg++;
					 $ls_cuenta		 = trim($rs_data->fields["scg_cuenta"]);
					 $ls_documento	 = $rs_data->fields["documento"];
					 $ls_descripcion = $rs_data->fields["desmov"];
					 $ls_procede	 = $rs_data->fields["procede_doc"];
					 $ls_debhab	 = $rs_data->fields["debhab"];
					 $ldec_monto	 = $rs_data->fields["monto"];
					 if ($ls_debhab=="D")
					    {
						  $ldec_mondeb = $ldec_mondeb+$ldec_monto;
					    }
					 else
					    {
						  $ldec_monhab = $ldec_monhab+$ldec_monto;
						}
					 $ls_codded=$rs_data->fields["codded"];
					 $objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$ls_cuenta."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
					 $objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$ls_documento."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
					 $objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_descripcion."'  title='".$ls_descripcion."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
					 $objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
					 $objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='".$ls_debhab."' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
					 $objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($ldec_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
					 $objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
					 $objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
			         $rs_data->MoveNext();
				   }
                                   $ldec_mondeb= number_format($ldec_mondeb,2,".","");
                                   $ldec_monhab= number_format($ldec_monhab,2,".","");
				   /*$pos1 =strpos($ldec_mondeb,".",0);
				   if ($pos1 ==0)
				   {
				   		$ldec_mondeb=$ldec_mondeb;
				   }
				   else
				   {
				   		$ldec_mondeb=substr($ldec_mondeb,0,$pos1+3);
				   }
				   $pos2 =strpos($ldec_monhab,".",0);
				   if ($pos2 ==0)
				   {
				   		$ldec_monhab=$ldec_monhab;
				   }
				   else
				   {
				   		$ldec_monhab=substr($ldec_monhab,0,$pos2+3);//Agregado
				   }*/
			if($li_row_scg==0)		
			{
				$li_row_scg=1;
				$objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
				$objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
				$objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
				$objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
				$objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
				$objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
				$objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
				$objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
			
			}
			$this->io_sql->free_result($rs_data);
		}		 

		$ls_estmodest     = $_SESSION["la_empresa"]["estmodest"];
		$li_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
		$li_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
		$li_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
		$li_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
		$li_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];

		$ls_sql="SELECT codban,ctaban,estmov,operacion,codestpro,spg_cuenta,documento,desmov,procede_doc,monto,estcla
				   FROM scb_movbco_spg
        		  WHERE codemp='".$ls_codemp."' 
				    AND numdoc ='".$as_numdoc."' 
					AND codban='".$as_codban."' 
				    AND ctaban='".$as_ctaban."' 
					AND codope='".$as_codope."' 
					AND estmov='".$ls_estmov."'
		  	     ORDER BY numdoc asc";

		$rs_data = $this->io_sql->select($ls_sql);		
		if($rs_data===false)
		{
			$this->is_msg_error="Error en inserción, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;
		}
		else
		{
		  while(!$rs_data->EOF)
			   {
				 $li_temp_spg++;
				 $ls_cuenta        = trim($rs_data->fields["spg_cuenta"]);
				 $ls_programatica  = trim($rs_data->fields["codestpro"]);
				 $ls_documento     = $rs_data->fields["documento"];
				 $ls_descripcion   = $rs_data->fields["desmov"];
				 $ls_procede       = $rs_data->fields["procede_doc"];
				 $ls_operacion_spg = $rs_data->fields["operacion"];
				 $ldec_monto       = $rs_data->fields["monto"];
				 $ls_estcla        = $rs_data->fields["estcla"];
				 $ls_codestpro1    = trim(substr(substr($rs_data->fields["codestpro"],0,25),-$li_loncodestpro1));
				 $ls_codestpro2    = trim(substr(substr($rs_data->fields["codestpro"],25,25),-$li_loncodestpro2));
				 $ls_codestpro3    = trim(substr(substr($rs_data->fields["codestpro"],50,25),-$li_loncodestpro3));
				 if ($ls_estmodest==2)
				    {
					  $ls_denestcla="";
					  $ls_codestpro4   = trim(substr(substr($rs_data->fields["codestpro"],75,25),-$li_loncodestpro4));
					  $ls_codestpro5   = trim(substr(substr($rs_data->fields["codestpro"],100,25),-$li_loncodestpro5));
					  $ls_programatica = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
				    }
				 else
				    {
					  if ($ls_estcla=='P')
					     {
					       $ls_denestcla = 'Proyecto';
					     }
					  else
					     {
					       $ls_denestcla = 'Acción Centralizada';
					     }
					  $ls_programatica=$ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
				    }
				
				 $ldec_monto_spg = $ldec_monto_spg+$ldec_monto;
				 $objectSpg[$li_temp_spg][1]  = "<input type=text name=txtcuenta".$li_temp_spg."       id=txtcuenta".$li_temp_spg."       value='".$ls_cuenta."'       class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
				 $objectSpg[$li_temp_spg][2]  = "<input type=text name=txtprogramatico".$li_temp_spg." id=txtprogramatico".$li_temp_spg." value='".$ls_programatica."' title='".$ls_programatica.'-'.$ls_denestcla."' class=sin-borde readonly style=text-align:center size=30 maxlength=129><input type=hidden name=hidestcla".$li_temp_spg." id=hidestcla".$li_temp_spg." value='".$ls_estcla."'>"; 
				 $objectSpg[$li_temp_spg][3]  = "<input type=text name=txtdocumento".$li_temp_spg."    id=txtdocumento".$li_temp_spg."    value='".$ls_documento."'    class=sin-borde readonly style=text-align:center size=13 maxlength=15>";
				 $objectSpg[$li_temp_spg][4]  = "<input type=text name=txtdescripcion".$li_temp_spg."  id=txtdescripcion".$li_temp_spg."  value='".$ls_descripcion."'  title='".$ls_descripcion."' class=sin-borde readonly style=text-align:left>";
				 $objectSpg[$li_temp_spg][5]  = "<input type=text name=txtprocede".$li_temp_spg."      id=txtprocede".$li_temp_spg."      value='".$ls_procede."'      class=sin-borde readonly style=text-align:center size=5 maxlength=6>";
				 $objectSpg[$li_temp_spg][6]  = "<input type=text name=txtoperacion".$li_temp_spg."    id=txtoperacion".$li_temp_spg."    value='".$ls_operacion_spg."'    class=sin-borde readonly style=text-align:center size=5 maxlength=3>";
				 $objectSpg[$li_temp_spg][7]  = "<input type=text name=txtmonto".$li_temp_spg."        id=txtmonto".$li_temp_spg."        value='".number_format($ldec_monto,2,",",".")."'      class=sin-borde readonly style=text-align:right size=15 maxlength=19>";		
				 $objectSpg[$li_temp_spg][8]  = "<a href=javascript:uf_delete_Spg('".$li_temp_spg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Gasto' width=15 height=15 border=0></a>";	
			     $rs_data->MoveNext();
			   }
			if($li_temp_spg==0)
			{
				$li_temp_spg=1;
				$objectSpg[$li_temp_spg][1]  = "<input type=text name=txtcuenta".$li_temp_spg."       id=txtcuenta".$li_temp_spg."       value='' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
				$objectSpg[$li_temp_spg][2]  = "<input type=text name=txtprogramatico".$li_temp_spg." id=txtprogramatico".$li_temp_spg." value='' class=sin-borde readonly style=text-align:center size=30 maxlength=129><input type=hidden name=hidestcla".$li_temp_spg." id=hidestcla".$li_temp_spg." value=''>"; 
				$objectSpg[$li_temp_spg][3]  = "<input type=text name=txtdocumento".$li_temp_spg."    id=txtdocumento".$li_temp_spg."    value='' class=sin-borde readonly style=text-align:center size=13 maxlength=15>";
				$objectSpg[$li_temp_spg][4]  = "<input type=text name=txtdescripcion".$li_temp_spg."  id=txtdescripcion".$li_temp_spg."  value='' class=sin-borde readonly style=text-align:left>";
				$objectSpg[$li_temp_spg][5]  = "<input type=text name=txtprocede".$li_temp_spg."      id=txtprocede".$li_temp_spg."      value='' class=sin-borde readonly style=text-align:center size=5 maxlength=6>";
				$objectSpg[$li_temp_spg][6]  = "<input type=text name=txtoperacion".$li_temp_spg."    id=txtoperacion".$li_temp_spg."    value='' class=sin-borde readonly style=text-align:center size=5 maxlength=3>";
				$objectSpg[$li_temp_spg][7]  = "<input type=text name=txtmonto".$li_temp_spg."        id=txtmonto".$li_temp_spg."        value='' class=sin-borde readonly style=text-align:right size=15 maxlength=19>";		
				$objectSpg[$li_temp_spg][8]  = "<a href=javascript:uf_delete_Spg('".$li_temp_spg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Gasto' width=15 height=15 border=0></a>";	
			}
			$this->io_sql->free_result($rs_data);
		}
		
		$li_estpreing = $_SESSION["la_empresa"]["estpreing"];
		$ls_sql="SELECT codban,ctaban,estmov,operacion,TRIM(spi_cuenta) as spi_cuenta,documento,desmov,procede_doc,monto,
		                codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla
				   FROM scb_movbco_spi
        		  WHERE codemp='".$ls_codemp."'
				    AND numdoc ='".$as_numdoc."'
					AND codban='".$as_codban."'
					AND ctaban='".$as_ctaban."'
					AND codope='".$as_codope."'
					AND estmov='".$ls_estmov."'
				  ORDER BY numdoc ASC";
		$rs_data = $this->io_sql->select($ls_sql);		
		if ($rs_data===false)
		   {
			 $this->is_msg_error="Error en select, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			 $lb_valido=false;
		   }
		else
		   {
			 while (!$rs_data->EOF)
			       {
				     $li_temp_spi++;
					 $ls_spicta = $rs_data->fields["spi_cuenta"];
					 $ls_desmov = $rs_data->fields["desmov"];
					 $ls_prodoc = $rs_data->fields["procede_doc"];
					 $ls_numdoc = $rs_data->fields["documento"];
					 $ls_opespi = $rs_data->fields["operacion"];
					 $ld_monspi = $rs_data->fields["monto"];
					 $ldec_monto_spi = $ldec_monto_spi + $ld_monspi;
					 $objectSpi[$li_temp_spi][1] = "<input type=text name=txtcuentaspi".$li_temp_spi." value='".$ls_spicta."' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
					 if ($li_estpreing==1)
					    {
						  $ls_estcla 	 = $rs_data->fields["estcla"];
						  $ls_codestpro1 = substr($rs_data->fields["codestpro1"],-$li_loncodestpro1);
						  $ls_codestpro2 = substr($rs_data->fields["codestpro2"],-$li_loncodestpro2);
						  $ls_codestpro3 = substr($rs_data->fields["codestpro3"],-$li_loncodestpro3);
						  $ls_codestpro  = $ls_codestpro1.'-'.$ls_codestpro2.'-'.$ls_codestpro3;
						  if ($ls_estmodest==2)
						     {
							   $ls_denestcla  = "";
							   $ls_codestpro4 = substr($rs_data->fields["codestpro4"],-$li_loncodestpro4);
							   $ls_codestpro5 = substr($rs_data->fields["codestpro5"],-$li_loncodestpro5);
							   $ls_codestpro  = $ls_codestpro.'-'.$ls_codestpro4.'-'.$ls_codestpro5;
							 }
						  else
						     {
							   if ($ls_estcla=='P')
						          {
								    $ls_denestcla = 'Proyecto';
								  }
							   elseif($ls_estcla=='A')
								  {
								    $ls_denestcla = 'Acción Centralizada';
								  }
							 }						  
						  $objectSpi[$li_temp_spi][2] = "<input type=text name=txtcodestprospi".$li_temp_spi." value='".$ls_codestpro."' class=sin-borde readonly style=text-align:center size=30 maxlength=129 title='".$ls_codestpro.'-'.$ls_denestcla."'><input type=hidden name=hidestclaspi".$li_temp_spg." id=hidestclaspi".$li_temp_spg." value='".$ls_estcla."'>";
						  $objectSpi[$li_temp_spi][3] = "<input type=text name=txtdocspi".$li_temp_spi."       value='".$ls_numdoc."'    class=sin-borde readonly style=text-align:center size=13 maxlength=15>";
						  $objectSpi[$li_temp_spi][4] = "<input type=text name=txtdescspi".$li_temp_spi."      value='".$ls_desmov."'    class=sin-borde readonly style=text-align:left title='".$ls_desmov."'>"; 
						  $objectSpi[$li_temp_spi][5] = "<input type=text name=txtprocspi".$li_temp_spi."      value='".$ls_prodoc."'    class=sin-borde readonly style=text-align:center size=5 maxlength=6>";
						  $objectSpi[$li_temp_spi][6] = "<input type=text name=txtopespi".$li_temp_spi."       value='".$ls_opespi."'    class=sin-borde readonly style=text-align:center size=5 maxlength=3>";
						  $objectSpi[$li_temp_spi][7] = "<input type=text name=txtmontospi".$li_temp_spi."     value='".number_format($ld_monspi,2,",",".")."' class=sin-borde readonly style=text-align:right size=15 maxlength=19>";
						  $objectSpi[$li_temp_spi][8] = "<a href=javascript:uf_delete_Spi('".$li_temp_spi."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Ingreso' width=15 height=15 border=0></a>";			   
						}
					 else
					    {
						  $objectSpi[$li_temp_spi][2] = "<input type=text name=txtdescspi".$li_temp_spi."   value='".$ls_desmov."' title='".$ls_desmov."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>"; 
						  $objectSpi[$li_temp_spi][3] = "<input type=text name=txtprocspi".$li_temp_spi."   value='".$ls_prodoc."' class=sin-borde readonly style=text-align:center size=32 maxlength=45>";
						  $objectSpi[$li_temp_spi][4] = "<input type=text name=txtdocspi".$li_temp_spi."    value='".$ls_numdoc."' class=sin-borde readonly style=text-align:center>";
						  $objectSpi[$li_temp_spi][5] = "<input type=text name=txtopespi".$li_temp_spi."    value='".$ls_opespi."' class=sin-borde readonly style=text-align:center size=7 maxlength=6>";
						  $objectSpi[$li_temp_spi][6] = "<input type=text name=txtmontospi".$li_temp_spi."  value='".number_format($ld_monspi,2,",",".")."' class=sin-borde readonly style=text-align:center size=15 maxlength=19>";
						  $objectSpi[$li_temp_spi][7] = "<a href=javascript:uf_delete_Spi('".$li_temp_spi."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Ingreso' width=15 height=15 border=0></a>";	
						}
			         $rs_data->MoveNext();
				   }
			 if ($li_temp_spi==0)
			    {
				  $li_temp_spi=1;
				  $objectSpi[$li_temp_spi][1] = "<input type=text name=txtcuentaspi".$li_temp_spi." value='' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
				  if ($li_estpreing==1)
				     {
					   $objectSpi[$li_temp_spi][2] = "<input type=text name=txtcodestprospi".$li_temp_spi." value='' class=sin-borde readonly style=text-align:center size=30 maxlength=129><input type=hidden name=hidestclaspi".$li_temp_spg." id=hidestclaspi".$li_temp_spg." value=''>";
					   $objectSpi[$li_temp_spi][3] = "<input type=text name=txtdocspi".$li_temp_spi."       value='' class=sin-borde readonly style=text-align:center size=13 maxlength=15>";
					   $objectSpi[$li_temp_spi][4] = "<input type=text name=txtdescspi".$li_temp_spi."      value='' class=sin-borde readonly style=text-align:left>"; 
					   $objectSpi[$li_temp_spi][5] = "<input type=text name=txtprocspi".$li_temp_spi."      value='' class=sin-borde readonly style=text-align:center size=5  maxlength=6>";
					   $objectSpi[$li_temp_spi][6] = "<input type=text name=txtopespi".$li_temp_spi."       value='' class=sin-borde readonly style=text-align:center size=5  maxlength=3>";
					   $objectSpi[$li_temp_spi][7] = "<input type=text name=txtmontospi".$li_temp_spi."     value='' class=sin-borde readonly style=text-align:right  size=15 maxlength=19>";
					   $objectSpi[$li_temp_spi][8] = "<a href=javascript:uf_delete_Spi('".$li_temp_spi."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Ingreso' width=15 height=15 border=0></a>";			   
					 }
				  else
				     { 
					   $objectSpi[$li_temp_spi][2] = "<input type=text name=txtdescspi".$li_temp_spi."   value='' class=sin-borde readonly style=text-align:center size=15 maxlength=15>"; 
					   $objectSpi[$li_temp_spi][3] = "<input type=text name=txtprocspi".$li_temp_spi."   value='' class=sin-borde readonly style=text-align:center size=32 maxlength=45>";
					   $objectSpi[$li_temp_spi][4] = "<input type=text name=txtdocspi".$li_temp_spi."    value='' class=sin-borde readonly style=text-align:center>";
					   $objectSpi[$li_temp_spi][5] = "<input type=text name=txtopespi".$li_temp_spi."    value='' class=sin-borde readonly style=text-align:center size=7 maxlength=6>";
					   $objectSpi[$li_temp_spi][6] = "<input type=text name=txtmontospi".$li_temp_spi."  value='' class=sin-borde readonly style=text-align:center size=15 maxlength=19>";
					   $objectSpi[$li_temp_spi][7] = "<img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Ingreso' width=15 height=15 border=0>";	
					 }
			    }
			 $this->io_sql->free_result($rs_data);
		   }		
		$arrResultado['objectScg']=$objectScg;
		$arrResultado['li_row_scg']=$li_row_scg;
		$arrResultado['ldec_mondeb']=$ldec_mondeb;
		$arrResultado['ldec_monhab']=$ldec_monhab;
		$arrResultado['objectSpg']=$objectSpg;
		$arrResultado['li_temp_spg']=$li_temp_spg;
		$arrResultado['ldec_monto_spg']=$ldec_monto_spg;
		$arrResultado['objectSpi']=$objectSpi;
		$arrResultado['li_temp_spi']=$li_temp_spi;
		$arrResultado['ldec_monto_spi']=$ldec_monto_spi;
		return $arrResultado;
}
	
function uf_delete_dt_scg($ls_mov_document,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ls_documento,$ls_scgcuenta,$ls_debhab,$ls_codded,$ldec_monto,$ls_proc_delete)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// - Funcion que elimina el detalle contable del movimiento  de banco
	//
	///////////////////////////////////////////////////////////////////////////////////////////////

	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];
	
	if($ls_proc_delete=="SCG")
	{
		$ls_sql=" DELETE FROM scb_movbco_scg 
				   WHERE codemp='".$ls_codemp."' 
				     AND codban='".$ls_codban."' 
					 AND ctaban='".$ls_ctaban."' 
				     AND numdoc='".$ls_mov_document."' 
					 AND codope='".$ls_codope."' 
					 AND estmov='".$ls_estmov."' 
				     AND debhab='".$ls_debhab."' 
					 AND codded='".$ls_codded."' 
					 AND documento='".$ls_documento."' 
				     AND scg_cuenta='".$ls_scgcuenta."'";
	}
	else
	{
		$ldec_diferencia=$this->uf_calcular_diferencia($ls_codemp,$ls_codban,$ls_ctaban,$ls_mov_document,$ls_codope,$ldec_monto,$ls_scgcuenta,$ls_documento);
		
		if($ldec_diferencia!=0)
		{
			$ls_sql=" UPDATE scb_movbco_scg 
					  SET monto=(monto-$ldec_monto)
					  WHERE  codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
					  AND    numdoc='".$ls_mov_document."' AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' 
					  AND debhab='".$ls_debhab."' AND codded='".$ls_codded."' AND documento='".$ls_documento."'
					  AND scg_cuenta='".$ls_scgcuenta."'";
		    
		}
		else
		{
			$ls_sql=" DELETE FROM scb_movbco_scg 
					   WHERE codemp='".$ls_codemp."' 
					     AND codban='".$ls_codban."' 
						 AND ctaban='".$ls_ctaban."' 
					     AND numdoc='".$ls_mov_document."' 
						 AND codope='".$ls_codope."' 
						 AND estmov='".$ls_estmov."' 
					     AND debhab='".$ls_debhab."' 
						 AND codded='".$ls_codded."' 
						 AND documento='".$ls_documento."' 
					     AND scg_cuenta='".$ls_scgcuenta."'";
		}
	}
	$li_result=$this->io_sql->execute($ls_sql);			

	if(($li_result===false))	
	{
		$this->is_msg_error="Error al eliminar registro, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido=false;
	}
	else
	{
		$lb_valido=true;
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="DELETE";
		$ls_descripcion="Elimino el detalle contable a la cuenta ".$ls_scgcuenta." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_mov_document." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->is_msg_error="El detalle contable fue eliminado";				
	}

	return $lb_valido;
}
	
function uf_calcular_diferencia($ls_codemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldec_monto,$ls_scgcuenta,$ls_documento)
{
	
	$ls_sql="SELECT monto 
			   FROM scb_movbco_scg 
			  WHERE codemp='".$ls_codemp."'
			    AND codban='".$ls_codban."'
				AND ctaban='".$ls_ctaban."'
				AND numdoc='".$ls_numdoc."'
				AND codope='".$ls_codope."' 
			    AND scg_cuenta='".$ls_scgcuenta."'
				AND documento='".$ls_documento."'";
	$rs_data=$this->io_sql->select($ls_sql);
	if ($rs_data===false)	
	   {
		 $this->is_msg_error="Error al buscar cuenta, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
		 $ldec_monto_scg=0;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  $ldec_monto_scg=$row["monto"];
		    }
		 else
		    {
			  $ldec_monto_scg=0;
		    }
	   }
	$ldec_diferencia=$ldec_monto-$ldec_monto_scg;
	return $ldec_diferencia;	 
			 
}
	
function uf_select_cuenta_scg($ls_codemp,$ls_programatica,$ls_cuenta_spg,$as_estcla)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que retorna cuenta contable asociada 
	//  a la cuenta presupuestaria enviada como parametro.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$ls_codest1=substr($ls_programatica,0,20);
	$ls_codest2=substr($ls_programatica,20,6);
	$ls_codest3=substr($ls_programatica,26,3);
	$ls_codest4=substr($ls_programatica,29,2);
	$ls_codest5=substr($ls_programatica,31,2);
	$ls_sql="SELECT sc_cuenta 
	           FROM spg_cuentas 
			  WHERE codemp='".$ls_codemp."'
			    AND codestpro1='".$ls_codest1."' 
				AND codestpro2='".$ls_codest2."' 
			    AND codestpro3='".$ls_codest3."' 
				AND codestpro4='".$ls_codest4."'
				AND codestpro5='".$ls_codest5."' 
			    AND spg_cuenta='".$ls_cuenta_spg."'
				AND estcla='".$as_estcla."'";

	$rs_cuenta=$this->io_sql->select($ls_sql);				  
	
	if(($rs_cuenta===false))	
	{
		$this->is_msg_error="Error al busacr cuenta, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido=false;
		$ls_cuenta_scg="";
	}
	else
	{
		if($row=$this->io_sql->fetch_row($rs_cuenta))
		{
			$ls_cuenta_scg=$row["sc_cuenta"];
		}
		else
		{
			$ls_cuenta_scg="";
		}
	}
return $ls_cuenta_scg;
}

function uf_select_cuenta_contable($ls_codemp,$ls_cuenta_spi)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que retorna cuenta contable asociada 
	//  a la cuenta de ingreso enviada como parametro.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////

	$ls_scgcta = "";
	$ls_sql="SELECT TRIM(sc_cuenta) as sc_cuenta
	           FROM spi_cuentas 
			  WHERE codemp='".$ls_codemp."' 	 
			    AND TRIM(spi_cuenta) = '".trim($ls_cuenta_spi)."'" ;
	$rs_data = $this->io_sql->select($ls_sql);				  
	if ($rs_data===false)	
	   {
	     $this->is_msg_error="Error al buscar cuenta, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  $ls_scgcta = $row["sc_cuenta"];
		    }
	   }
    return $ls_scgcta;
}

function uf_delete_dt_spg($ls_mov_document,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ls_numdoc,$ls_cuenta_spg,$ls_operacion,$ls_programatica,$ldec_monto,$as_estcla)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que elimina el detalle presupuestario del movimiento 
	//  junto con el contable asociado a la cuenta de presupuesto.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_sql=" DELETE FROM scb_movbco_spg 
			   WHERE codemp='".$ls_codemp."' 
			     AND codban='".$ls_codban."' 
				 AND ctaban='".$ls_ctaban."' 
			     AND numdoc='".$ls_mov_document."' 
				 AND codope='".$ls_codope."' 
				 AND estmov='".$ls_estmov."' 
				 AND operacion='".$ls_operacion."' 
			     AND codestpro='".$ls_programatica."' 
				 AND documento='".$ls_numdoc."' 
				 AND spg_cuenta='".$ls_cuenta_spg."'
				 AND estcla='".$as_estcla."'";
	
	$li_result=$this->io_sql->execute($ls_sql);				  
	if(($li_result===false))	
	{
		$this->is_msg_error="Error al eliminar registro, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		$lb_valido=false;
	}
	else
	{
		$lb_valido=true;
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="DELETE";
		$ls_descripcion="Elimino el detalle presupuestario a la cuenta ".$ls_cuenta_spg." de programatica ".$ls_programatica." para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cuenta_scg = $this->uf_select_cuenta_scg($ls_codemp,$ls_programatica,$ls_cuenta_spg,$as_estcla);
		$lb_valido     = $this->uf_delete_dt_scg($ls_mov_document,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ls_numdoc,$ls_cuenta_scg,'D','00000',$ldec_monto,'SPG');
		$this->is_msg_error="El detalle presupuestario fue eliminado";				
	}
	return $lb_valido;
}
	
function uf_delete_all_movimiento($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que elimina el movimiento Bancario junto con los detalles contables,presupuestarios
	//  asociados a el mismo.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];		
	
	$lb_valido=	$this->uf_delete_all_dtmov($ls_codemp,$ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov);//Funcion que elimina los detalles del movimiento

	if($lb_valido)
	{
		$ls_sql="DELETE FROM scb_movbco 
				 WHERE 	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."' AND estmov<>'C' AND estmov<>'A' AND estmov<>'O'";
		
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_delete_all_movimiento->Error al eliminar detalle de movimiento".$this->fun->uf_convertirmsg($this->io_sql->message);
			if($this->io_sql->errno==1451)
			{
				$this->is_msg_error="No se pudo eliminar el Movimiento,posee relaciones";	
			}				
		}
		else
		{
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion="Elimino el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$this->is_msg_error="El movimiento Bancario fue eliminado";
		}		
	}
	else
	{
		$lb_valido=false;
	}
	return $lb_valido;
}

function uf_delete_anulado($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov)
{
////////////////////////////////////////////////////////////////////////////////////////////////
//
// -Funcion que elimina el movimiento Bancario junto con los detalles contables,presupuestarios
//  asociados a el mismo.
//
///////////////////////////////////////////////////////////////////////////////////////////////
	
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];		
	
	$lb_valido=	$this->uf_delete_all_dtmov($ls_codemp,$ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov);//Funcion que elimina los detalles del movimiento

	if($lb_valido)
	{
		$ls_sql="DELETE FROM scb_movbco 
				 WHERE 	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."' ";
		
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_delete_anulado->Error al eliminar detalle de movimiento".$this->fun->uf_convertirmsg($this->io_sql->message);
			if($this->io_sql->errno==1451)
			{
				$this->is_msg_error="No se pudo eliminar el Movimiento,posee relaciones";	
			}				
		}
		else
		{
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion="Elimino el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$this->is_msg_error="El movimiento Bancario fue eliminado";
		}		
	}
	else
	{
		$lb_valido=false;
	}
	return $lb_valido;
}

function uf_actualiza_documento($ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ad_fecha,$as_numdocnew)
{
////////////////////////////////////////////////////////////////////////////////////////////////
//
// -Funcion que elimina el movimiento Bancario junto con los detalles contables,presupuestarios
//  asociados a el mismo.
//
///////////////////////////////////////////////////////////////////////////////////////////////
	
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];		
	
	$lb_valido=	$this->uf_select_movimiento_completo($ls_numdoc,$ls_codope,$ls_codban,$ls_ctaban,$ls_estmov,$ad_fecha);
	$ad_fecha=$this->fun->uf_convertirdatetobd($ad_fecha);
	if ($this->io_ds_bancos->data["numconint"][1] != null) {
		$lb_valido = $this->uf_update_numconint($ls_numdoc, $ls_codope, $ls_codban, $ls_ctaban, $ls_estmov, $ad_fecha);
	}
	if($lb_valido)
	{
		$ls_codusu	 =$this->io_ds_bancos->data["codusu"][1];
		$ls_codban	 =$this->io_ds_bancos->data["codban"][1];
		$ls_ctaban	 =$this->io_ds_bancos->data["ctaban"][1];
		$ls_codope	 =$this->io_ds_bancos->data["codope"][1];
		//$ldt_fecha	 =$this->io_ds_bancos->data["fecmov"][1];
		$ls_conmov	 =$this->io_ds_bancos->data["conmov"][1];
		$ls_codconmov=$this->io_ds_bancos->data["codconmov"][1];
		$ls_codpro	 =$this->io_ds_bancos->data["cod_pro"][1];
		$ls_cedbene	 =$this->io_ds_bancos->data["ced_bene"][1];
		$ls_nomproben=$this->io_ds_bancos->data["nomproben"][1];
		$ldec_monto	 =$this->io_ds_bancos->data["monto"][1];
		$ldec_monobjret=$this->io_ds_bancos->data["monobjret"][1];
		$ldec_monret =$this->io_ds_bancos->data["monret"][1];
		$ls_chevau   =$this->io_ds_bancos->data["chevau"][1];
		$ls_estmov   =$this->io_ds_bancos->data["estmov"][1];
		$li_estmovint=$this->io_ds_bancos->data["estmovint"][1];
		$li_cobrapaga=$this->io_ds_bancos->data["estcobing"][1];
		$ls_estbpd	 =$this->io_ds_bancos->data["estbpd"][1];
		$ls_estreglib=$this->io_ds_bancos->data["estreglib"][1];
		$ls_tipproben=$this->io_ds_bancos->data["tipo_destino"][1];
		$as_numordpagmin=$this->io_ds_bancos->data["numordpagmin"][1];
		$as_codtipfon=$this->io_ds_bancos->data["codtipfon"][1];
		$li_estmovcob=$this->io_ds_bancos->data["estmovcob"][1];
		$as_numcontint=$this->io_ds_bancos->data["numconint"][1];
		
		$ls_sql="INSERT INTO scb_movbco(codemp,codusu,codban,ctaban,numdoc,codope,fecmov,conmov,codconmov,cod_pro,ced_bene,nomproben,
	                                 monto,monobjret,monret,chevau,estmov,estmovint,estcobing,esttra,estbpd,estcon,feccon,estreglib,
									 tipo_destino,fecha,procede, codfuefin,numordpagmin,codtipfon,estmovcob,numconint)
			 VALUES                ('".$ls_codemp."','".$ls_codusu."','".$ls_codban."','".$ls_ctaban."','".$as_numdocnew."',
			 						'".$ls_codope."','".$ad_fecha."','".$ls_conmov."','".$ls_codconmov."','".$ls_codpro."',
									'".$ls_cedbene."','".$ls_nomproben."',".$ldec_monto.",".$ldec_monobjret.",".$ldec_monret.",
									'".$ls_chevau."','".$ls_estmov."',".$li_estmovint.",".$li_cobrapaga.", 0    ,'".$ls_estbpd."',
									    0  ,'1900-01-01','".$ls_estreglib."','".$ls_tipproben."','1900-01-01','SCBBCH','--','".$as_numordpagmin."','".$as_codtipfon."','".$li_estmovcob."','".$as_numcontint."')";										
		
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);
			if($this->io_sql->errno==1451)
			{
				$this->is_msg_error="Ocurrio un error al insertar el movimiento";	
			}				
		}
		else
		{
			$ls_sql_fuefin=" UPDATE scb_movbco_fuefinanciamiento SET numdoc='".$as_numdocnew."' ".
						   " WHERE codemp='".$ls_codemp."' ".
						   " AND codban='".$ls_codban."' ".
						   " AND ctaban='".$ls_ctaban."' ".
						   " AND numdoc='".$ls_numdoc."'";
			$li_rsfuefin=$this->io_sql->execute($ls_sql_fuefin);
			if ($li_rsfuefin==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento FUEFIN->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			$ls_sql_scg=" UPDATE scb_movbco_scg SET numdoc='".$as_numdocnew."', documento='".$as_numdocnew."' ".
						   " WHERE codemp='".$ls_codemp."' ".
						   " AND codban='".$ls_codban."' ".
						   " AND ctaban='".$ls_ctaban."' ".
						   " AND numdoc='".$ls_numdoc."'";
			$li_rsscg=$this->io_sql->execute($ls_sql_scg);
			if ($li_rsscg==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento SCG->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			$ls_sql_spg=" UPDATE scb_movbco_spg SET numdoc='".$as_numdocnew."' ".
						   " WHERE codemp='".$ls_codemp."' ".
						   " AND codban='".$ls_codban."' ".
						   " AND ctaban='".$ls_ctaban."' ".
						   " AND numdoc='".$ls_numdoc."'";
			$li_rsspg=$this->io_sql->execute($ls_sql_spg);
			if ($li_rsspg==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento SPG->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			$ls_sql_spi=" UPDATE scb_movbco_spi SET numdoc='".$as_numdocnew."' ".
						   " WHERE codemp='".$ls_codemp."' ".
						   " AND codban='".$ls_codban."' ".
						   " AND ctaban='".$ls_ctaban."' ".
						   " AND numdoc='".$ls_numdoc."'";
			$li_rsspi=$this->io_sql->execute($ls_sql_spi);
			if ($li_rsspi==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento SPI->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			
			$ls_sql_cxp_sol=" UPDATE cxp_sol_banco SET numdoc='".$as_numdocnew."' ".
						   " WHERE codemp='".$ls_codemp."' ".
						   " AND codban='".$ls_codban."' ".
						   " AND ctaban='".$ls_ctaban."' ".
						   " AND numdoc='".$ls_numdoc."'";
			$li_rs_cxp_sol=$this->io_sql->execute($ls_sql_cxp_sol);
			if ($li_rs_cxp_sol==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento CXP->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			
			$ls_sql_scb_dt=" UPDATE scb_dt_movbco SET numdoc='".$as_numdocnew."' ".
						   " WHERE codemp='".$ls_codemp."' ".
						   " AND codban='".$ls_codban."' ".
						   " AND ctaban='".$ls_ctaban."' ".
						   " AND numdoc='".$ls_numdoc."'";
			$li_rs_scb_dt=$this->io_sql->execute($ls_sql_scb_dt);
			if ($li_rs_scb_dt==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento SCB_DT->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			
			$ls_sql_movbco="DELETE FROM scb_movbco 
				 WHERE 	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."'";
			$li_rsmov=$this->io_sql->execute($ls_sql_movbco);
			if ($li_rsmov==false)
			{
				$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_actualiza_documento MOVBCO->Error al modificar el numero de documento".$this->fun->uf_convertirmsg($this->io_sql->message);	
				$lb_valido=false;
			}
			
			if (($li_rsfuefin!=false)&&($li_rsscg!=false)&&($li_rsspg!=false)&&($li_rsspi!=false)&&($li_rs_cxp_sol!=false)&&($li_rs_scb_dt!=false)&&($li_rsmov!=false))
			{
				$lb_valido=true;
				$this->is_msg_error="El movimiento Bancario fue Modificado";
			}
		}		
	}
	else
	{
		$lb_valido=false;
	}
	return $lb_valido;
}

function uf_delete_all_dtmov($ls_codemp,$ls_numdoc,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que elimina todos los detalles asociados al movimiento Bancario 
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$ls_sql="DELETE FROM scb_movbco_scg 
			 WHERE	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
			 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."'";
	
	$li_result=$this->io_sql->execute($ls_sql);
	
	if(($li_result===false))
	{
		$lb_valido=false;
		$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_delete_all_dtmov->Error al eliminar detalle de movimiento".$this->fun->un_convertirmsg($this->io_sql->message);
	}
	else
	{
		$lb_valido=true;
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="DELETE";
		$ls_descripcion="Elimino los detalles contables del movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
	}
	
	if($lb_valido)
	{
		$ls_sql="DELETE FROM scb_movbco_spg 
				 WHERE	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."'";
		
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php->Método->uf_delete_all_dtmov.Error al eliminar detalle de movimiento".$this->fun->un_convertirmsg($this->io_sql->message);
		}
		else
		{
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion="Elimino los detalles presupuestarios del movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
	}		
	if($lb_valido)
	{
		$ls_sql="DELETE FROM scb_movbco_spi
				 WHERE	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."'";
		
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_delete_all_dtmov.Error al eliminar detalle de movimiento".$this->fun->un_convertirmsg($this->io_sql->message);
		}
		else
		{
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion="Elimino los detalles de ingresos del movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
	}			
	if($lb_valido)
	{
		$ls_sql="DELETE FROM scb_movbco_fuefinanciamiento
				 WHERE	codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' 
				 AND codope='".$ls_codope."' AND estmov='".$ls_estmov."' AND numdoc='".$ls_numdoc."'";
		
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="CLASS->sigesp_scb_c_movbanco.php.Método->uf_delete_all_dtmov.Error al eliminar detalle de movimiento".$this->fun->un_convertirmsg($this->io_sql->message);
		}
		else
		{
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion="Elimino los detalles de fuente de financimiento del movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
		}
	}			

	
	return $lb_valido;
}

function uf_procesar_errorbanco($arr_errorbco)
{
	//////////////////////////////////////////////////////////////////////////////
	//	Function:	uf_procesar_errorbanco
	// Access:		public
	//	Returns:	Boolean Retorna si proceso correctamente
	//	Description:	Funcion que se encarga de guardar el movimiento por erro de banco de la conciliacion
	//						insertando o actualizando
	//////////////////////////////////////////////////////////////////////////////
	
		$ls_codemp		= $this->dat["codemp"];
		$ls_codban      = $arr_errorbco["codban"];
		$ls_ctaban      = $arr_errorbco["ctaban"];
		$ls_numdoc      = $arr_errorbco["numdoc"];
		$ls_codope      = $arr_errorbco["codope"];
		$ls_estmov      = $arr_errorbco["estmov"];
		$ls_fecmes 		= str_replace("/","",$arr_errorbco["fecmes"]);
		$ldt_fecmov     = $this->fun->uf_convertirdatetobd($arr_errorbco["fecmov"]);
		$ls_conmov      = $arr_errorbco["conmov"];
		$ldec_monto     = $arr_errorbco["monto"] ;
		$ldec_monret    = $arr_errorbco["monret"];
		$li_cobrapaga   = $arr_errorbco["cobrapaga"];
		$li_estmovint   = $arr_errorbco["estmovint"];
		$ls_chevau      = $arr_errorbco["chevau"];
		$ls_procede     = $arr_errorbco["procede_doc"];
		$ls_estbpd      = $arr_errorbco["estbpd"];
		$ls_esterr 		= $arr_errorbco["esterrcon"];
		
		$lb_existe=$this->uf_select_error_banco($ls_numdoc,$ls_codope,$ls_ctaban,$ls_codban,$ls_fecmes);
		if(!$lb_existe)
		{
			$ls_sql="INSERT INTO scb_errorconcbco(codemp,codban,ctaban,numdoc,codope,fecmov,conmov,monmov,monret,chevou,estmov,estbpd,esterrcon,fecmesano,estcon)
					 VALUES('".$ls_codemp."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."','".$ls_codope."','".$ldt_fecmov."','".$ls_conmov."',".$ldec_monto.",".$ldec_monret.",'".$ls_chevau."','".$ls_estmov."','".$ls_estbpd."','".$ls_esterr."','".$ls_fecmes."',1)";
			$ls_mensaje="Error en insert error banco";
			$ls_descripcion="Inserto el movimiento bancario por error en banco de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		    
		}
		else
		{
			$ls_sql="UPDATE scb_errorconcbco
					SET fecmov='$ldt_fecmov',conmov='$ls_conmov',monmov='$ldec_monto',monret='$ldec_monret',
					chevou='$ls_chevau',estmov='$ls_estmov',estbpd='$ls_estbpd',esterrcon='$ls_esterr',estcon=1
					WHERE codemp='".$ls_codemp."' AND numdoc='".$ls_numdoc."' AND codope='$ls_codope' and
					ctaban='$ls_ctaban' AND codban='$ls_codban'AND fecmesano='$ls_fecmes'";
			$ls_mensaje="Error en update error banco";
			$ls_descripcion="Actualizo el movimiento bancario por error en banco de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;

		
		}
		$li_result=$this->io_sql->execute($ls_sql);
		
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error=$ls_mensaje." ".$this->fun->uf_convertirmsg($this->io_sql->message);
		}
		else
		{
			$lb_valido=true;
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="INSERT";
			$ls_descripcion="Inserto el movimiento bancario por error en banco de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$this->is_msg_error="El movimiento de error en banco fue registrado";
		}
		return $lb_valido;

}

function uf_numero_voucher($as_codemp,$ls_codban,$ls_ctaban,$ls_numdoc)
{
	 $ls_sql="  SELECT chevau 
				FROM   scb_movbco  
				WHERE  codemp ='".$as_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND numdoc='".$ls_numdoc."' AND codope='CH'";		
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {
			$ls_codigo="";  
	  }
	  else
	  {
		  if ($row=$this->io_sql->fetch_row($rs_data))
		  { 
			  $ls_codigo=$row["chevau"];		  
		  }
		  else
		  {
			  $ls_codigo="";  
		  }
	  }
	return $ls_codigo;
}
	
function uf_select_voucher($ls_chevau)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que verifica que retorna true si el vaucher introducido ya existe
	// Autor: Ing. Laura Cabre
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];		
	$ls_sql="SELECT chevau 
			FROM scb_movbco 
			WHERE chevau='$ls_chevau' AND codope='CH' and  codemp='$ls_codemp'";		
	$rs_mov=$this->io_sql->select($ls_sql);
	if(($rs_mov===false))
	{
		pg_set_error_verbosity($this->io_sql->conn, PGSQL_ERRORS_TERSE);
		$ls_x=pg_last_error($this->io_sql->conn);
		$this->is_msg_error="Error en uf_select_voucher,".$this->io_sql->message;
		return false;
	}
	else
	{
		if($row=$this->io_sql->fetch_row($rs_mov))
		{
			if($row["chevau"]!="")
				return true;
			else
				return false;
		}
		else
		{
			return false;
		}	
	}	
}
	
function uf_eliminar_error_banco($as_documento,$as_ctaban,$as_codban,$as_fecmesano)
{
	/*----------------------------------------------------------
	Funcion: uf_eliminar_error_banco
	Descripcion: Funcion que permite eliminar un error en banco
	Autor: Ing. Laura Cabré
	Fecha: 06/12/2006
	-----------------------------------------------------------*/
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];
	$ls_sql="DELETE FROM scb_errorconcbco
			 WHERE	codemp='".$ls_codemp."' AND numdoc like '".$as_documento."' AND codope<>'OP' and
			 ctaban='$as_ctaban' AND codban='$as_codban' AND fecmesano='$as_fecmesano'";
		
		$li_result=$this->io_sql->execute($ls_sql);
		if(($li_result===false))
		{
			$lb_valido=false;
			$this->is_msg_error="Error uf_eliminar_error_banco".$this->fun->un_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			//////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion="Elimino el Error de Banco ".$as_documento." para el banco ".$as_codban." cuenta ".$as_ctaban;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			return true;
		}

}
	
function uf_select_error_banco($as_documento,$as_codope,$as_ctaban,$as_codban,$as_mesano)
{
	/*-----------------------------------------------
	Funcion:uf_select_error_banco
	Descripcion: Funcion que retorna true si existe el registro
	Autor: Ing. Laura Cabré
	Fecha: 07/12/2006
	-----------------------------------------------------*/
	$dat=$_SESSION["la_empresa"];
	$ls_codemp=$dat["codemp"];		
	$ls_sql="SELECT * FROM scb_errorconcbco
			 WHERE codemp='".$ls_codemp."' AND numdoc='".$as_documento."' AND codope='$as_codope' and
			 ctaban='$as_ctaban' AND codban='$as_codban' AND fecmesano='$as_mesano'";
	
	$rs_mov=$this->io_sql->select($ls_sql);
	if(($rs_mov===false))
	{
		$this->is_msg_error="Error en uf_select_error_banco,".$this->uf_convertirmsg($this->io_sql->message);
		return false;
	}
	else
	{
		if($row=$this->io_sql->fetch_row($rs_mov))
		{
			return true;
		}
		else
		{
			return false;
		}	
	}
}

function uf_update_montos_auxiliares_movbco_scg($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_cuenta,$as_debhab,$as_codded,$as_documento)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_update_montos_auxiliares_movbco_scg
//		    Access: private
//	     Arguments: 
//       $as_codemp
//       $as_codban
//       $as_ctaban
//       $as_numdoc
//       $as_codope
//       $as_estmov
//       $as_cuenta
// $as_operacioncon
//       $as_codded
//    $as_documento
//	       Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
//	   Description: Función que busca y actualiza monto con su correspondiente en Bs.F.
//	    Creado Por: Ing. Nestor Falcón.
//  Fecha Creación: 15/08/2007 								Fecha Última Modificación : 15/08/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;

  $ls_sql  = "SELECT monto
                FROM scb_movbco_scg
			   WHERE codemp='".$as_codemp."'
			     AND codban='".$as_codban."'
				 AND ctaban='".$as_ctaban."'
				 AND numdoc='".$as_numdoc."'
				 AND codope='".$as_codope."'
				 AND estmov='".$as_estmov."'
				 AND scg_cuenta='".$as_cuenta."'
				 AND debhab='".$as_debhab."'
				 AND codded='".$as_codded."'
				 AND documento='".$as_documento."'";
  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	 }
  else
     {
	   if ($row=$this->io_sql->fetch_row($rs_data))
	      {
		    $ld_monto = $row["monto"] ;  
		  }
	 }
  return $lb_valido;
}

function uf_update_montos_auxiliares_movbco_spg($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_programa,$as_spgcuenta,$as_documento,$as_estcla)
{
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	      Function: uf_update_montos_auxiliares_movbco_spg
//		    Access: private
//	     Arguments: 
//       $as_codemp
//       $as_codban
//       $as_ctaban
//       $as_numdoc
//       $as_codope
//       $as_estmov
//     $as_programa
//    $as_spgcuenta
//    $as_documento
//	       Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
//	   Description: Función que busca y actualiza monto con su correspondiente en Bs.F.
//	    Creado Por: Ing. Nestor Falcón.
//  Fecha Creación: 15/08/2007 								Fecha Última Modificación : 15/08/2007
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  $lb_valido = true;

  $ls_sql  = "SELECT monto
                FROM scb_movbco_spg
			   WHERE codemp='".$as_codemp."' 
			     AND codban='".$as_codban."'
				 AND ctaban='".$as_ctaban."' 
				 AND numdoc='".$as_numdoc."'
				 AND codope='".$as_codope."' 
				 AND estmov='".$as_estmov."' 
				 AND codestpro='".$as_programa."' 
				 AND spg_cuenta='".$as_spgcuenta."' 
				 AND documento='".$as_documento."'
				 AND estcla='".$as_estcla."'";
  $rs_data = $this->io_sql->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	 }
  else
     {
	   if ($row=$this->io_sql->fetch_row($rs_data))
	      {
		    $ld_monto = $row["monto"];			
		  }
	 }
  return $lb_valido;
}

	function uf_actualizar_estatus_ch($ls_codban,$ls_ctaban,$ls_numdoc,$ls_numchequera,$ai_estche)
	{
	  $lb_valido = true;
	  if (!empty($ls_numdoc)&(!empty($ls_numchequera)))
		 {
			$ls_sql="SELECT codban
					   FROM scb_cheques
					  WHERE codban='".$ls_codban."' 
					    AND ctaban='".$ls_ctaban."' 
						AND numche='".$ls_numdoc."' 
						AND numchequera='".$ls_numchequera."'";
			
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->is_msg_error="Error en actualizar estatus Cheque.".$this->fun->uf_convertirmsg($this->io_sql->message);
				$lb_valido=false;					
			}
			else
			{
			 if ($row=$this->io_sql->fetch_row($rs_data))
				{
				  $ls_sql = "UPDATE scb_cheques 
							    SET estche=".$ai_estche.", estins=".$ai_estche."
							  WHERE codban='".$ls_codban."' 
							    AND ctaban='".$ls_ctaban."' 
								AND numche='".$ls_numdoc."' 
								AND numchequera='".$ls_numchequera."'";
				   $li_result=$this->io_sql->execute($ls_sql);
				   if ($li_result===false)
					  {
						$this->is_msg_error="Error en actualizar estatus Cheque.".$this->fun->uf_convertirmsg($this->io_sql->message);
						$lb_valido=false;					
					  }		 
				}
			}
		}
	  return $lb_valido;
	}


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_fuentefinancimiento($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_codfuefin)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_fuentefinancimiento
		//		   Access: public  
		//	    Arguments: as_codemp  // Código de empresa
		//				   as_codban  // Código de Banco
		//				   as_ctaban  // Cuenta del Banco
		//				   as_numdoc  // Número de Documento
		//				   as_codope  // Código de Operación
		//				   as_estmov  // Estatus del Movimiento
		//				   as_codfuefin  // código de La fuente de Financiamiento
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la fuente de financiamiento por movimiento de banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/10/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO scb_movbco_fuefinanciamiento(codemp, codban, ctaban, numdoc, codope, estmov, codfuefin) VALUES ".
				"('".$as_codemp."','".$as_codban."','".$as_ctaban."','".$as_numdoc."','".$as_codope."','".$as_estmov."','".$as_codfuefin."')";
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
 			$lb_valido=false;
			$this->msg->message("CLASE->Movimiento de Banco MÉTODO->uf_insert_fuentefinancimiento ERROR->".$this->fun->uf_convertirmsg($this->io_sql->message));
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_fuentefinancimiento($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_fuentefinancimiento
		//		   Access: public  
		//	    Arguments: as_codemp  // Código de empresa
		//				   as_codban  // Código de Banco
		//				   as_ctaban  // Cuenta del Banco
		//				   as_numdoc  // Número de Documento
		//				   as_codope  // Código de Operación
		//				   as_estmov  // Estatus del Movimiento
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que elimina la fuente de financiamiento por movimiento de banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/10/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM scb_movbco_fuefinanciamiento ".
				" WHERE	codemp='".$as_codemp."' ".
				"   AND codban='".$as_codban."' ".
				"   AND ctaban='".$as_ctaban."' ".
				"   AND codope='".$as_codope."' ".
				"   AND estmov='".$as_estmov."' ".
				"   AND numdoc='".$as_numdoc."'";
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
 			$lb_valido=false;
			$this->msg->message("CLASE->Movimiento de Banco MÉTODO->uf_delete_fuentefinancimiento ERROR->".$this->fun->uf_convertirmsg($this->io_sql->message));
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_check_insert_fuentefinancimiento($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_codfuefin)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_check_insert_fuentefinancimiento
		//		   Access: public  
		//	    Arguments: as_codemp  // Código de empresa
		//				   as_codban  // Código de Banco
		//				   as_ctaban  // Cuenta del Banco
		//				   as_numdoc  // Número de Documento
		//				   as_codope  // Código de Operación
		//				   as_estmov  // Estatus del Movimiento
		//				   as_codfuefin  // código de La fuente de Financiamiento
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la fuente de financiamiento por movimiento de banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/10/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codfuefin ".
				"  FROM scb_movbco_fuefinanciamiento ".
				" WHERE	codemp='".$as_codemp."' ".
				"   AND codban='".$as_codban."' ".
				"   AND ctaban='".$as_ctaban."' ".
				"   AND codope='".$as_codope."' ".
				"   AND estmov='".$as_estmov."' ".
				"   AND numdoc='".$as_numdoc."' ".
				"   AND codfuefin='".$as_codfuefin."' ";
		$rs_data=$this->io_sql->select($ls_sql);	
		if($rs_data===false)
		{
			$this->is_msg_error="Error en consulta,".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;		
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			else
			{
				$ls_sql="INSERT INTO scb_movbco_fuefinanciamiento(codemp, codban, ctaban, numdoc, codope, estmov, codfuefin) VALUES ".
						"('".$as_codemp."','".$as_codban."','".$as_ctaban."','".$as_numdoc."','".$as_codope."','".$as_estmov."','".$as_codfuefin."')";
				$li_numrow=$this->io_sql->execute($ls_sql);
				if($li_numrow===false)
				{
					$lb_valido=false;
					$this->msg->message("CLASE->Movimiento de Banco MÉTODO->uf_check_insert_fuentefinancimiento ERROR->".$this->fun->uf_convertirmsg($this->io_sql->message));
				}
			}
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------
    function uf_guardar_automatico2($ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_estdoc,$ls_tipproben,$ls_codfuefin, $ls_anticipo, $as_docant,$as_monamo,$li_estmovcob)
	{								
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que procesa los datos de la cabecera del movimiento bancario
		//	validando que no exista y que el periodo este abierto.
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_codusu=$_SESSION["la_logusr"];

		if($this->io_fecha->uf_valida_fecha_periodo($ldt_fecha,$ls_codemp))
		{
		   if(!$this->uf_select_movimiento($ls_numdoc,$ls_codope,$ls_codban,$ls_ctaban,$ls_estmov))
		   {	
			   $this->io_sql->begin_transaction();
			   $lb_valido = $this->uf_insert_movimiento2($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben, $ls_anticipo, $as_docant,$as_monamo,$li_estmovcob);
			   if($lb_valido)
			   {
					$lb_valido = $this->uf_insert_fuentefinancimiento($ls_codemp,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ls_estmov,$ls_codfuefin);
			   }
			   $ib_valido = $lb_valido;
			   if($lb_valido)
			   {
					$ib_new = false;
			   }	
		   }
		   elseif($ls_estdoc=='C')
		   {
				
				$lb_valido=true;
				$lb_valido=$this->uf_update_movimiento2($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben, $ls_anticipo,$as_docant,$as_monamo);
		   }
		   else
		   {
				$lb_valido=false;   
				$this->is_msg_error="El numero de documento ya existe";
		   }
				
		}
		else
		{
			$this->is_msg_error=$this->io_fecha->is_msg_error;
			$lb_valido=false;
		}	
		return $lb_valido;
	}//fin de uf_guardar_automatico2
//-------------------------------------------------------------------------------------------------------------------------------------	
//------------------------------------------------------------------------------------------------------------------------------------
   function uf_insert_movimiento2($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben, $ls_anticipo,$as_docant,$as_monamo,$li_estmovcob)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta la cabecera del movimiento  bancario
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$ldt_fecha=$this->fun->uf_convertirdatetobd($ldt_fecha);
	if (($ls_codope=="CH")&&($ls_chevau!=""))
	   {
	     $ls_chevau= str_pad($ls_chevau,25,"0",STR_PAD_LEFT);
	   }
	$ls_sql="INSERT INTO scb_movbco(       codemp   ,     codusu   ,       codban   ,       ctaban   ,       numdoc   ,
									       codope   ,     fecmov   ,       conmov   ,       codconmov   ,      cod_pro   , 
										   ced_bene ,     nomproben,        monto   ,        monobjret  ,        monret  ,
										   chevau   ,     estmov   ,      estmovint ,      estcobing  ,esttra,estbpd, 
										   estcon   ,     feccon   ,   estreglib       ,tipo_destino,fecha,procede, codfuefin, estant, docant, monamo, estmovcob)
			 VALUES                ('".$ls_codemp."','".$ls_codusu."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."',
			 						'".$ls_codope."','".$ldt_fecha."','".$ls_conmov."','".$ls_codconmov."','".$ls_codpro."',
									'".$ls_cedbene."','".$ls_nomproben."',".$ldec_monto.",".$ldec_monobjret.",".$ldec_monret.",
									'".$ls_chevau."','".$ls_estmov."',".$li_estmovint.",".$li_cobrapaga.", 0    ,'".$ls_estbpd."',
									    0  ,'1900-01-01','".$ls_estreglib."','".$ls_tipproben."','1900-01-01','SCBBCH','--','".$ls_anticipo."','".$as_docant."',".$as_monamo.",".$li_estmovcob.")"; 									
	$li_result=$this->io_sql->execute($ls_sql);
	if(($li_result===false))
	{
		$this->is_msg_error="Fallo insercion de movimiento, ".$this->fun->uf_convertirmsg($this->io_sql->message);		
		return false;
	}
	else
	{
	    $this->is_msg_error="El movimiento Bancario fue registrado";
		///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="INSERT";
		$ls_descripcion="Inserto el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban." por un monto de ".$ldec_monto;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////
		return $lb_valido;		
	}
}//fin de uf_insert_movimiento2 
///-----------------------------------------------------------------------------------------------------------------------------------
   function uf_update_movimiento2($ls_codemp,$ls_codusu,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope,$ldt_fecha,$ls_conmov,$ls_codconmov,$ls_codpro,$ls_cedbene,$ls_nomproben,$ldec_monto,$ldec_monobjret,$ldec_monret,$ls_chevau,$ls_estmov,$li_estmovint,$li_cobrapaga,$ls_estbpd,$ls_procede,$ls_estreglib,$ls_tipproben, $ls_anticipo,$as_docant,$as_monamo)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta la cabecera del movimiento  bancario
	//
	///////////////////////////////////////////////////////////////////////////////////////////////
	$ldt_fecha=$this->fun->uf_convertirdatetobd($ldt_fecha);
	
	$ls_sql=" UPDATE scb_movbco ".
	        "    SET conmov='".$ls_conmov."', ".
			"     codconmov='".$ls_codconmov."', ".
			"       cod_pro='".$ls_codpro."', ".
			"      ced_bene='".$ls_cedbene."', ".
			"     nomproben='".$ls_nomproben."', ".
			"         monto='".$ldec_monto."', ".
			"     monobjret='".$ldec_monobjret."', ".
			"        monret='".$ldec_monret."', ".
			"  tipo_destino='".$ls_tipproben."', ".
			"        estant='".$ls_anticipo."', ".
			"        docant='".$as_docant."', ".
			"        monamo=".$as_monamo."  ".
			" WHERE codemp='".$ls_codemp."' ".
			"   AND codban='".$ls_codban."' ".
			"   AND ctaban='".$ls_ctaban."' ".
			"   AND numdoc='".$ls_numdoc."' ".
			"   AND codope='".$ls_codope."'";    
	$li_result=$this->io_sql->execute($ls_sql);
	if($li_result===false)
	{
		$this->is_msg_error=" Fallo Actualizacion de movimiento, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		return false;
	}
	else
	{
	    $this->is_msg_error="El movimiento Bancario fue Actualizado";
		//////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
		$ls_evento="UPDATE";
		$ls_descripcion="Actualizo el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc." para el banco ".$ls_codban." cuenta ".$ls_ctaban." por un monto de ".$ldec_monto;
		$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],$this->la_security["sistema"],$ls_evento,$this->la_security["logusr"],$this->la_security["ventanas"],$ls_descripcion);
		////////////////////////////////////////////////////////////////////////////////////////////////////////////			
		return $lb_valido;
	}
	
}// fin de uf_update_movimiento2
//-----------------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------------------
function uf_cargar_dt_cont($as_numdoc,$as_codban,$as_ctaban,$as_codope,$ls_estmov,$objectScg,$li_row_scg,
                           $ldec_mondeb,$ldec_monhab)
{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que carga todos los detalles del movimiento de banco en los object 
		//	requeridos por la clase grid_param.
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		
		$li_row_scg=0;		
		$li_temp_ret=0;
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_sql="SELECT   codban,ctaban,codope,estmov,scg_cuenta,codded,debhab,documento,desmov,procede_doc,monto,monobjret
				   FROM   scb_movbco_scg
				  WHERE    codemp='".$ls_codemp ."'".
			    "   AND numdoc ='".$as_numdoc."'".
				"   AND codban='".$as_codban."' ".
				"   AND ctaban='".$as_ctaban."' ".
				"   AND codope='".$as_codope."' ".
				"   AND estmov='".$ls_estmov."'	".
				" ORDER BY debhab asc,numdoc asc"; 
				 
		$rs_scg=$this->io_sql->select($ls_sql);		
		if(($rs_scg===false))
		{
			$this->is_msg_error="Error en inserción, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_scg))
			{       
					$li_row_scg=$li_row_scg+1;
					$ls_cuenta=trim($row["scg_cuenta"]);
					$ls_documento=$row["documento"];
					$ls_descripcion=$row["desmov"];
					$ls_procede=$row["procede_doc"];
					$ls_debhab=$row["debhab"];
					$ldec_monto=$row["monto"];
					if($ls_debhab=="D")
					{
						$ldec_mondeb=$ldec_mondeb+$ldec_monto;
					}
					else
					{
						$ldec_monhab=$ldec_monhab+$ldec_monto;
					}
					$ls_codded=$row["codded"];
					$objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$ls_cuenta."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
					$objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$ls_documento."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
					$objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_descripcion."'  title='".$ls_descripcion."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
					$objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
					$objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='".$ls_debhab."' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
					$objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($ldec_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
					$objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
					$objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
					
			}
			
			if($li_row_scg==0)		
			{
				$li_row_scg=1;
				$objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
				$objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
				$objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
				$objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
				$objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
				$objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
				$objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
				$objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
			
			}
			$this->io_sql->free_result($rs_scg);
		$arrResultado['objectScg']=$objectScg;
		$arrResultado['li_row_scg']=$li_row_scg;
		$arrResultado['ldec_mondeb']=$ldec_mondeb;
		$arrResultado['ldec_monhab']=$ldec_monhab;
		return $arrResultado;
		}	
}// fin de  uf_cargar_dt_cont
//------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_validar_pago_anticipo($as_estmanant)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que valida el permiso del proceso de pago de anticipo
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		
		$as_estmanant=0;		
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		$ls_sql=" SELECT estmanant FROM sigesp_empresa WHERE codemp='".$ls_codemp."'";						 
		$rs_data=$this->io_sql->select($ls_sql);
				
		if(($rs_data===false))
		{
			$this->is_msg_error="Error en el select, ".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{       
				$as_estmanant=$row["estmanant"]; 	
			}
			$this->io_sql->free_result($rs_data);
		}	
		return $as_estmanant;
    }// uf_validar_pago_anticipo
//------------------------------------------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------------------------------------------
   	function uf_select_anticipo($arr_movbco,$ls_cuenta,$ls_operacioncon,$ldec_monto,$monto_actual,$ls_codamo,$ls_monamo)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que verifica si existe el movimiento contable
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$dat           =$_SESSION["la_empresa"];
		$ls_codemp     =$dat["codemp"];
		$ls_codban     = trim($arr_movbco["codban"]);
		$ls_ctaban     = trim($arr_movbco["ctaban"]);
		$ls_numdoc     = $arr_movbco["mov_document"];
		$ls_codope     = $arr_movbco["codope"];
		$ls_estmov	   = $arr_movbco["estmov"];
		$ldec_monobjret= $arr_movbco["objret"];	 
		
		$ls_sql=" SELECT codemp, codban, ctaban, numdoc, codope, 
		                 estmov, codamo, monamo, monsal, montotamo, sc_cuenta
                    FROM scb_movbco_anticipo
				   WHERE codemp='".$ls_codemp."'".
				   " AND codban='".$ls_codban."'".
				   " AND ctaban='".$ls_ctaban."'".
				   " AND numdoc='".$ls_numdoc."'".
				   " AND codope='".$ls_codope."'".
				   " AND estmov='".$ls_estmov."'".
				   " AND sc_cuenta='".$ls_cuenta."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if(($rs_data===false))
		{
			$this->is_msg_error="Error en select detalle del anticipo ".$this->fun->uf_convertirmsg($this->io_sql->message);
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$monto_actual=$row["monsal"];
				$ls_codamo=$row["codamo"];
				$ls_monamo=$row["monamo"];
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado["lb_valido"]=$lb_valido;	
		$arrResultado["monto_actual"]=$monto_actual;	
		$arrResultado["ls_codamo"]=$ls_codamo;	
		$arrResultado["ls_monamo"]=$ls_monamo;	
		return $arrResultado;
	}// fin de uf_select_anticipo	
//-----------------------------------------------------------------------------------------------------------------------------------
    function  uf_procesar_anticipo($arr_movbco,$ls_cuenta,$ls_procede,$ls_descripcion,$ls_documento,$ls_operacioncon,
	                               $ldec_monto,$ldec_objret)
    {
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que inserta o actualiza el anticipo  
	//
	///////////////////////////////////////////////////////////////////////////////////////////////	
	$dat       = $_SESSION["la_empresa"];
	$ls_codemp = $dat["codemp"];
    $ls_codban = $arr_movbco["codban"];
 	$ls_ctaban = $arr_movbco["ctaban"];
	$ls_numdoc = $arr_movbco["mov_document"];
	$ls_codope = $arr_movbco["codope"];
	$ls_estmov = $arr_movbco["estmov"];
	$monto_actual=0; 
	$arrResultado="";	
	$monto_actual="";	
	$ls_codamo="";	
	$ls_monamo="";	
	$lb_valido="";	
	$arrResultado = $this->uf_select_anticipo($arr_movbco,$ls_cuenta,$ls_operacioncon,$ldec_monto,$monto_actual,$ls_codamo,$ls_monamo);	
	$monto_actual=$arrResultado["monto_actual"];	
	$ls_codamo=$arrResultado["ls_codamo"];		
	$ls_monamo=$arrResultado["ls_monamo"];		
	$lb_valido=$arrResultado["lb_valido"];	
	if(!$lb_valido)
	{
			require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
		    $io_keygen= new sigesp_c_generar_consecutivo();
		    //$ls_codamo= $io_keygen->uf_generar_numero_nuevo("SCB","scb_movbco_anticipo","codamo","SCBBCH",5,"","","");
			$ls_codamo = $io_keygen->uf_generar_numero_nuevo2('SCB','scb_movbco_anticipo','codamo','SCBBCH',5,'','','',$this->la_security["logusr"]);
			$ls_sql="INSERT INTO scb_movbco_anticipo(codemp, codban, ctaban, numdoc, codope,
			                                         estmov, codamo, monamo, monsal, montotamo,sc_cuenta)
                                             VALUES ('".$ls_codemp."','".$ls_codban."','".$ls_ctaban."',
											         '".$ls_numdoc."','".$ls_codope."','".$ls_estmov."',
													 '".$ls_codamo."',0,".$ldec_monto.",".$ldec_monto.",
													 '".$ls_cuenta."');";
			
			$li_result=$this->io_sql->execute($ls_sql);
			
			if(($li_result===false))
			{
				$this->is_msg_error="Error al procesar el anticipo, ".$this->fun->uf_convertirmsg($this->io_sql->message);
				$lb_valido=false;			
			}
			else
			{
				$lb_valido=true;
				///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
				$ls_evento="INSERT";
				$ls_descripcion="Inserto el detalle del anticipo a la cuenta ".$ls_cuenta." por un monto de ".$ldec_monto.
				                " para el movimiento bancario de operacion por anticipo".$ls_codope." numero ".$ls_numdoc.
								" para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		        $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],
				                                                                $this->la_security["sistema"],
																				$ls_evento,$this->la_security["logusr"],
																				$this->la_security["ventanas"],$ls_descripcion);
				////////////////////////////////////////////////////////////////////////////////////////////////////////////
			}
	}
	else
	{   
		$ldec_monto=$ldec_monto+$monto_actual;
		
		$ls_sql="UPDATE scb_movbco_anticipo
				   SET monamo=".$ls_monamo.", 
					   monsal=".$ldec_monto.", 
					   montotamo=".$ldec_monto."
				 WHERE codemp='".$ls_codemp."'".
				 " AND codban='".$ls_codban."'".
			     " AND ctaban='".$ls_ctaban."'".
				 " AND numdoc='".$ls_numdoc."'".
				 " AND codope='".$ls_codope."'".
				 " AND estmov='".$ls_estmov."'".
			     " AND codamo='".$ls_codamo."'".
				 " AND sc_cuenta='".$ls_cuenta."'";
		if($lb_valido)
		{
			$li_result=$this->io_sql->execute($ls_sql);	

			if(($li_result===false))
			{
				$this->is_msg_error="Error al procesar el anticipo, ".$this->fun->uf_convertirmsg($this->io_sql->message);
				$lb_valido=false;			
			}
			else
			{
				$lb_valido=true;
				///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
				$ls_evento="UPDATE";
				$ls_descripcion="Actualizo del anticipo a la cuenta ".$ls_cuenta." por un monto de ".$ldec_monto.
				                " para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_numdoc.
								" para el banco ".$ls_codban." cuenta ".$ls_ctaban;
		        $lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],
				                                                                $this->la_security["sistema"],
																				$ls_evento,$this->la_security["logusr"],
																				$this->la_security["ventanas"],$ls_descripcion);
				////////////////////////////////////////////////////////////////////////////////////////////////////////////				
			}
		}		
	}
	return $lb_valido;
} // fin de uf_procesar_anticipo
//-------------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_delete_anticipo($ls_mov_document,$ls_codban,$ls_ctaban,$ls_codope,$ls_estmov,$ls_scgcuenta)
   {
   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   //// function que elimina los anticipos
   /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	   $dat       = $_SESSION["la_empresa"];
	   $ls_codemp = $dat["codemp"];
	   $arr_movbco["codban"]=$ls_codban;
	   $arr_movbco["ctaban"]=$ls_ctaban;
	   $arr_movbco["mov_document"]=$ls_mov_document;	  
	   $arr_movbco["codope"]=$ls_codope;	
	   $arr_movbco["estmov"]=$ls_estmov;
	   $monto_actual=0;
	    
		$arrResultado="";	
		$monto_actual="";	
		$ls_codamo="";	
		$ls_monamo="";	
		$lb_valido="";	
	    $arrResultado = $this->uf_select_anticipo($arr_movbco,$ls_scgcuenta,$ls_operacioncon,$ldec_monto,
	                                          $monto_actual,$ls_codamo,$ls_monamo);
		$monto_actual=$arrResultado["monto_actual"];	
		$ls_codamo=$arrResultado["ls_codamo"];		
		$ls_monamo=$arrResultado["ls_monamo"];		
		$lb_valido=$arrResultado["lb_valido"];	
											  
											 
	   if($ls_monamo==0)
	   {
		   $dat=$_SESSION["la_empresa"];
		   $ls_codemp=$dat["codemp"];	
		   $ls_sql=" DELETE FROM scb_movbco_anticipo 
						   WHERE codemp='".$ls_codemp."' 
							 AND codban='".$ls_codban."' 
							 AND ctaban='".$ls_ctaban."' 
							 AND numdoc='".$ls_mov_document."' 
							 AND codope='".$ls_codope."' 
							 AND estmov='".$ls_estmov."'					
							 AND sc_cuenta='".$ls_scgcuenta."'
							 AND codamo='".$ls_codamo."'"; 
			$li_result=$this->io_sql->execute($ls_sql);			 
		
			if(($li_result===false))	
			{
				$this->is_msg_error="Error al eliminar Anticipo, ".$this->fun->uf_convertirmsg($this->io_sql->message);
				$lb_valido=false;
			}
			else
			{
				$lb_valido=true;
				///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion="Elimino el detalle del anticipo a la cuenta ".$ls_scgcuenta.
								" para el movimiento bancario de operacion ".$ls_codope." numero ".$ls_mov_document.
								" para el banco ".$ls_codban." cuenta ".$ls_ctaban;
				$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($this->la_security["empresa"],
																				$this->la_security["sistema"],
																				$ls_evento,$this->la_security["logusr"],
																				$this->la_security["ventanas"],$ls_descripcion);
				////////////////////////////////////////////////////////////////////////////////////////////////////////////
				$this->is_msg_error="El detalle del anticipo fue eliminado";				
			}
		}
		else
		{
			$this->is_msg_error="No se puede eliminar el Detalle pues posee Amortizaciones";
			$lb_valido=false;	
		}	
		return $lb_valido;
   }// fin de uf_delete_anticipos
//------------------------------------------------------------------------------------------------------------------------------------

function uf_load_documentos_asociados($as_numordpagmin,$as_codtipfon)
{
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  //	     Function: uf_load_documentos_asociados
  //		   Access: private
  //	    Arguments: 
  //	  Description: Función que verifica si existen Recepciones de Documentos, Cheque o Notas de Débitos
  //                   que utilizan un Número de Orden de Pago Ministerio para evitar su eliminación.  
  //	   Creado Por: Ing. Néstor Falcón
  //   Fecha Creación: 19/02/2009.								Fecha Última Modificación : 19/02/2009.
  ////////////////////////////////////////////////////////////////////////////////////////////////////////
  $lb_valido = true;
  $ls_sql    = "SELECT codemp
				  FROM scb_movbco 
				 WHERE numordpagmin<>'-' 
				   AND numordpagmin<>''
				   AND numordpagmin = '".$as_numordpagmin."'
				   AND codtipfon = '".$as_codtipfon."'
				   AND (codope='CH' OR codope='ND')
				 GROUP BY scb_movbco.codemp
				 UNION
			    SELECT codemp
				  FROM cxp_rd
				 WHERE numordpagmin<>'-' 
				   AND numordpagmin<>''
				   AND numordpagmin = '".$as_numordpagmin.$as_codtipfon."'
				 GROUP BY cxp_rd.codemp";
  $rs_data   = $this->io_sql->execute($ls_sql);//echo $ls_sql.'<br>';			 
  if ($rs_data===false)	
	 {
	   $this->is_msg_error="CLASS->sigesp_scb_c_movbco.php;Método->uf_load_documentos_asociados.".$this->fun->uf_convertirmsg($this->io_sql->message);
	   $lb_valido = false;
	 }
  else
	 {
	   if ($row=$this->io_sql->fetch_row($rs_data))
	      {
		    $lb_valido = false;
			$this->msg->message("Existen Documentos Asociados a este Movimiento Bancario, No puede Eliminarse !!!");
		  }
     }
  return $lb_valido;
}//uf_load_documentos_asociados

function uf_select_pagados($as_numsol,$as_numdoc,$as_codban,$as_ctaban)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_pagados
		//		   Access: private
		//	    Arguments: $as_numsol = Numero de solicitud de pago
		//	      Returns: Monto pagado de la solicitud
		//	  Description: Funcion que obtiene el monto pagado por solicitud.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 15/03/2010. 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
   	$li_pagado=0;
	$ls_sql="SELECT monto,numdoc,codban,ctaban".
			"   FROM cxp_sol_banco".
			"  WHERE codemp='".$ls_codemp."'".
			"	 AND numsol = '".$as_numsol."'".
			"    AND estmov<>'A'".
			"    AND estmov<>'O'";
	$rs_data=$this->io_sql->select($ls_sql);
	if ($rs_data===false)
	   {
		 $this->is_msg_error="Error en select pagado ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
	   }
	else
	   {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_monto=$row["monto"];
				$ls_numdoc=trim($row["numdoc"]);
				$ls_codban=trim($row["codban"]);
				$ls_ctaban=trim($row["ctaban"]);
				if(($ls_numdoc!=trim($as_numdoc)))
				{
					$li_pagado=$li_pagado+$li_monto;
				}
				else
				{
					if(($ls_codban!=trim($as_codban))||($ls_ctaban!=trim($as_ctaban)))
					{
						$li_pagado=$li_pagado+$li_monto;
					}
				}
			}
	   } 	
	return $li_pagado;
}

function uf_select_original($as_numsol)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_original
		//		   Access: private
		//	    Arguments: $as_numsol = Numero de solicitud de pago
		//	      Returns: Monto de la solicitud
		//	  Description: Funcion que obtiene el monto original por solicitud.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 15/03/2010. 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
   	$li_monsol=0;
   	$li_nd=0;
   	$li_nc=0;
	$ls_sql="SELECT no.codope, so.monsol,no.monto ".
			"   FROM cxp_solicitudes so".
			"   INNER JOIN cxp_sol_dc no ON  so.codemp=no.codemp AND so.numsol=no.numsol".	
			"  WHERE so.codemp='".$ls_codemp."'".
			"	 AND so.numsol = '".$as_numsol."'";
	$rs_data=$this->io_sql->select($ls_sql);
	if ($rs_data===false)
	   {
		 $this->is_msg_error="Error en select pagado ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
	   }
	else
	   {
			while (!$rs_data->EOF)
			{
				if($rs_data->fields["codope"]=="NC")
				{
					$li_nc=$li_nc+$rs_data->fields["monto"];
				}
				if($rs_data->fields["codope"]=="ND")
				{
					$li_nd=$li_nd+$rs_data->fields["monto"];
				}
				$li_monsol=$rs_data->fields["monsol"];
				$rs_data->MoveNext();
			}
	   } 	
	$li_monsol = $li_monsol + $li_nd - $li_nc;  
	return $li_monsol;
}

function uf_validar_monto_cancelado($as_numsol,$as_numdoc,$as_codban,$as_ctaban,$ai_monpag)
{
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	     Function: uf_validar_monto_cancelado
	//		   Access: private
	//	    Arguments: $as_numsol = Numero de solicitud de pago
	//	      Returns: Monto de la solicitud
	//	  Description: Funcion que valida si la solicitud de pago ha sido cancelada en su totalidad.
	//	   Creado Por: Ing. Luis Anibal Lang
	// Fecha Creación: 15/03/2010. 								Fecha Última Modificación : 
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_origen=0;
	$li_montosolicitud=$this->uf_select_original($as_numsol);
	$li_montosolicitud=round($li_montosolicitud,2);
	if($li_montosolicitud>0)
	{
		$li_montopagado=$this->uf_select_pagados($as_numsol,$as_numdoc,$as_codban,$as_ctaban);
		if($li_montopagado>=$li_montosolicitud)
		{
			$lb_valido=false;
			$ls_origen=1;
		}
		else
		{
			$li_totalmovimiento=$li_montopagado+$ai_monpag;
			$li_totalmovimiento=round($li_totalmovimiento,2);
			if($li_totalmovimiento>$li_montosolicitud)
			{
				$lb_valido=false;	
				$ls_origen=2;
			}
		}
	}
	return $ls_origen;
}

function uf_verificar_fuentefinanciamiento($ai_estmodest,$as_codfuefin,$as_estruc1,$as_estruc2,$as_estruc3,$as_estruc4,$as_estruc5)
{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que verifica que el movimiento bancario no exista
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$dat=$_SESSION["la_empresa"];
		$ls_codemp=$dat["codemp"];
		if ($ai_estmodest=='2')
		{
			$ls_sql="SELECT codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla ".
				 " FROM   spg_dt_fuentefinanciamiento ".
				 " WHERE  codemp='".$ls_codemp."' ".
				 " AND codestpro1 ='".$as_estruc1."' ". 
				 " AND codestpro2 ='".$as_estruc2."' ".
				 " AND codestpro3 ='".$as_estruc3."' ".
				 " AND codestpro4 ='".$as_estruc4."' ".
				 " AND codestpro5 ='".$as_estruc5."' ".
				 " AND codfuefin ='".$as_codfuefin."' ";
		}
		else
		{
			$ls_sql=" SELECT codestpro1,codestpro2,codestpro3,estcla ".
				 " FROM   spg_dt_fuentefinanciamiento ".
				 " WHERE  codemp='".$ls_codemp."' ".
				 " AND codestpro1 ='".$as_estruc1."' ". 
				 " AND codestpro2 ='".$as_estruc2."' ".
				 " AND codestpro3 ='".$as_estruc3."' ".
				 " AND codfuefin ='".$as_codfuefin."' ";
		}
		
		$rs_mov=$this->io_sql->select($ls_sql);
		if(($rs_mov===false))
		{
			$this->is_msg_error="Error en select movimiento,".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_mov))
			{
				return true;
			}
			else
			{
				return false;
			}	
		}
	}

	function uf_datos_proveedor($codpro) {
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que obtiene datos del proveedor
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$arrDataProv = array();
		
		$ls_sql="SELECT nompro, rifpro, ctaban
				FROM   rpc_proveedor 
				WHERE  codemp ='{$_SESSION["la_empresa"]["codemp"]}' 
				AND cod_pro ='{$codpro}'";
		
		$rs_proveedor=$this->io_sql->select($ls_sql);
		if($rs_proveedor===false){
			$this->is_msg_error="Error en select movimiento,".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else {
			$arrDataProv["nompro"] = $rs_proveedor->fields["nompro"];		
			$arrDataProv["rifpro"] = $rs_proveedor->fields["rifpro"];
			$arrDataProv["ctaban"] = $rs_proveedor->fields["ctaban"];
		}
		unset($rs_proveedor);
		
		return $arrDataProv;
	}
	
	function uf_datos_beneficiario($ls_cedbene) {
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que obtiene datos del proveedor
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		$arrDataProv = array();
	
		$ls_sql="SELECT  nacben, nombene, apebene
					FROM   rpc_beneficiario
					WHERE  codemp ='{$_SESSION["la_empresa"]["codemp"]}'
					AND ced_bene ='{$ls_cedbene}'";
	
		$rs_proveedor = $this->io_sql->select($ls_sql);
		if($rs_proveedor===false){
			$this->is_msg_error="Error en select movimiento,".$this->uf_convertirmsg($this->io_sql->message);
			return false;
		}
		else {
			$cedbene = trim($rs_proveedor->fields["nacben"]);
			$cedbene = $cedbene.'-'.$ls_cedbene;
			$arrDataProv["nompro"] = $rs_proveedor->fields["apebene"].' '.$rs_proveedor->fields["nombene"];		
			$arrDataProv["rifpro"] = $cedbene;
		}
		unset($rs_proveedor);
	
		return $arrDataProv;
	}
	
	function uf_actualizar_estatus_ch2($ls_codban,$ls_ctaban,$ls_numdoc,$ls_numchequera)
	{
		if (!empty($ls_numdoc)&(!empty($ls_numchequera)))
		{
			$ls_sql = "SELECT numche
				    FROM scb_cheques
				   WHERE codban='".$ls_codban."'
				     AND ctaban='".$ls_ctaban."'
					 AND numche='".$ls_numdoc."'
					 AND numchequera='".$ls_numchequera."'";
			$rs_data=$this->io_sql->select($ls_sql);
			if ($rs_data===false)
			{
				$this->is_msg_error="Error en actualizar estatus Cheque.".$this->fun->uf_convertirmsg($this->io_sql->message);
				return false;
			}
			else
			{
				if ($row=$this->io_sql->fetch_row($rs_data))
				{
					$ls_sql = "UPDATE scb_cheques
						       SET estche=1
						     WHERE codban='".$ls_codban."'
							   AND ctaban='".$ls_ctaban."'
							   AND numche='".$ls_numdoc."'
							   AND numchequera='".$ls_numchequera."'";
	
					$rs_data = $this->io_sql->execute($ls_sql);
					if ($rs_data===false)
					{
						$this->is_msg_error="Error en actualizar estatus Cheque.".$this->fun->uf_convertirmsg($this->io_sql->message);
						return false;
					}
					else
					{
						return true;
					}
				}
				else
				{
					return true;
				}
			}
		}
		else
		{
			return true;
		}
	}
	
	function uf_update_numconint($ls_numdoc,$ls_codope,$ls_codban,$ls_ctaban,$ls_estmov,$ad_fecha)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que actualiza el numero de control interno
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
	    $valido = true;
		$ad_fecha=$this->fun->uf_convertirdatetobd($ad_fecha);
		$ls_sql="UPDATE scb_movbco
					SET numconint = null
				 WHERE  codemp='".$_SESSION["la_empresa"]["codemp"]."' AND codban ='".$ls_codban."' AND ctaban='".$ls_ctaban."'
				 AND    numdoc='".$ls_numdoc."' AND fecmov='".$ad_fecha."' ";
		$rs_mov=$this->io_sql->select($ls_sql);
		if(($rs_mov===false))
		{
			$this->is_msg_error="Error en uf_update_numconint,".$this->fun->uf_convertirmsg($this->io_sql->message);
			$valido = false;
		}
		
		return $valido;
	}
function uf_select_cuenta_contable_banco($ls_codban)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_cuenta_contable_banco
		//		   Access: private
		//	    Arguments: ls_codemp // COdigo de Empresa
		//	    		   ls_codban // conexión del archivo que se desea abrir
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que busca la cuenta contable del banco 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 15/09/2017 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$ls_scgcta = "";
	$ls_sql="SELECT TRIM(sc_cuenta) as sc_cuenta
	           FROM scb_banco 
			  WHERE codemp='".$ls_codemp."' 	 
			    AND codban = '".$ls_codban."'" ;
	$rs_data = $this->io_sql->select($ls_sql);				  
	if ($rs_data===false)	
	   {
	     $this->is_msg_error="Error al buscar cuenta, ".$this->fun->uf_convertirmsg($this->io_sql->message);
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  $ls_scgcta = $row["sc_cuenta"];
		    }
	   }
    return $ls_scgcta;
}

function uf_cargar_detalle($as_numdoc,$as_codban,$as_ctaban,$as_codope,$as_codforpag,$as_sccuenta,$as_monto,$ldec_montoislr)
{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//
		// -Funcion que carga todos los detalles del movimiento de banco en los object 
		//	requeridos por la clase grid_param.
		//
		///////////////////////////////////////////////////////////////////////////////////////////////
		
		require_once("class_folder/sigesp_scb_c_comision.php");
		$io_co= new sigesp_scb_c_comision();
		require_once("class_folder/sigesp_scb_c_asientos.php");
		$io_ca= new sigesp_scb_c_asientos();
		require_once("class_folder/sigesp_scb_c_formapago.php");
		$io_fp= new sigesp_scb_c_formapago();
		$ls_codded="0000";
		$li_row_scg=1;
		$objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
		$objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
		$objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
		$objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
		$objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
		$objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
		$objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
		$objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";

		$li_temp_spg=1;
		$objectSpg[$li_temp_spg][1]  = "<input type=text name=txtcuenta".$li_temp_spg."       id=txtcuenta".$li_temp_spg."       value='' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
		$objectSpg[$li_temp_spg][2]  = "<input type=text name=txtprogramatico".$li_temp_spg." id=txtprogramatico".$li_temp_spg." value='' class=sin-borde readonly style=text-align:center size=30 maxlength=129><input type=hidden name=hidestcla".$li_temp_spg." id=hidestcla".$li_temp_spg." value=''>"; 
		$objectSpg[$li_temp_spg][3]  = "<input type=text name=txtdocumento".$li_temp_spg."    id=txtdocumento".$li_temp_spg."    value='' class=sin-borde readonly style=text-align:center size=13 maxlength=15>";
		$objectSpg[$li_temp_spg][4]  = "<input type=text name=txtdescripcion".$li_temp_spg."  id=txtdescripcion".$li_temp_spg."  value='' class=sin-borde readonly style=text-align:left>";
		$objectSpg[$li_temp_spg][5]  = "<input type=text name=txtprocede".$li_temp_spg."      id=txtprocede".$li_temp_spg."      value='' class=sin-borde readonly style=text-align:center size=5 maxlength=6>";
		$objectSpg[$li_temp_spg][6]  = "<input type=text name=txtoperacion".$li_temp_spg."    id=txtoperacion".$li_temp_spg."    value='' class=sin-borde readonly style=text-align:center size=5 maxlength=3>";
		$objectSpg[$li_temp_spg][7]  = "<input type=text name=txtmonto".$li_temp_spg."        id=txtmonto".$li_temp_spg."        value='' class=sin-borde readonly style=text-align:right size=15 maxlength=19>";		
		$objectSpg[$li_temp_spg][8]  = "<a href=javascript:uf_delete_Spg('".$li_temp_spg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Gasto' width=15 height=15 border=0></a>";	

		$li_temp_spi=1;
		$objectSpi[$li_temp_spi][1] = "<input type=text name=txtcuentaspi".$li_temp_spi." value='' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
		$objectSpi[$li_temp_spi][1] = "<input type=text name=txtcuentaspi".$li_temp_spi." value='' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
		$objectSpi[$li_temp_spi][2] = "<input type=text name=txtdescspi".$li_temp_spi."   value='' class=sin-borde readonly style=text-align:center size=15 maxlength=15>"; 
		$objectSpi[$li_temp_spi][3] = "<input type=text name=txtprocspi".$li_temp_spi."   value='' class=sin-borde readonly style=text-align:center size=32 maxlength=45>";
		$objectSpi[$li_temp_spi][4] = "<input type=text name=txtdocspi".$li_temp_spi."    value='' class=sin-borde readonly style=text-align:center>";
		$objectSpi[$li_temp_spi][5] = "<input type=text name=txtopespi".$li_temp_spi."    value='' class=sin-borde readonly style=text-align:center size=7 maxlength=6>";
		$objectSpi[$li_temp_spi][6] = "<input type=text name=txtmontospi".$li_temp_spi."  value='' class=sin-borde readonly style=text-align:center size=15 maxlength=19>";
		$objectSpi[$li_temp_spi][7] = "<img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Ingreso' width=15 height=15 border=0>";	
		
		switch($as_codope)
		{
			case"DP":
				$ls_concepto="Deposito ";
				$ls_operacion="DP";
				$ls_procede="SCBBDP";
			break;
			case"ND":
				$ls_concepto="Comision ";
				$ls_operacion="ND";
				$ls_procede="SCBBND";
			break;
		}	

		$arrFormaPago=$io_fp->uf_select_formapago($as_codforpag);
		if(!$arrFormaPago->EOF)
		{
			$ls_denforpag=$arrFormaPago->fields["denforpag"];
		}
		$ls_concepto=$ls_concepto.$ls_denforpag." (".$as_codforpag.") No.".$as_numdoc;
		$ls_datosasientos=false;
		$ls_datoscomision=true;
		
		$arrParametros["codban"]=$as_codban;
		$arrParametros["ctaban"]=$as_ctaban;
		$arrParametros["codforpag"]=$as_codforpag;
		$arrResultado=$io_ca->uf_select_asientos($arrParametros);
		if(!$arrResultado->EOF)
		{
			$ls_spicta=$arrResultado->fields["spi_cuenta"];
			$ls_spicon=$arrResultado->fields["spi_contable"];
			$ls_codestpro1=$arrResultado->fields["codestpro1"];
			$ls_codestpro2=$arrResultado->fields["codestpro2"];
			$ls_codestpro3=$arrResultado->fields["codestpro3"];
			$ls_codestpro4=$arrResultado->fields["codestpro4"];
			$ls_codestpro5=$arrResultado->fields["codestpro5"];
			$ls_estcla=$arrResultado->fields["estcla"];
			$ls_spgcta=$arrResultado->fields["spg_cuenta"];
			$ls_spgcon=$arrResultado->fields["spg_contable"];
			$ls_islrcon=$arrResultado->fields["islr_contable"];
			$ls_datosasientos=true;
		}
	$ls_datoscomision=true;
		if(($ls_datosasientos)&&($ls_datoscomision))
		{
		
			switch($as_codope)
			{
				case"DP":
				  $li_temp_spi=1;
				  $objectSpi[$li_temp_spi][1] = "<input type=text name=txtcuentaspi".$li_temp_spi." value='".$ls_spicta."' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
				  $objectSpi[$li_temp_spi][2] = "<input type=text name=txtdescspi".$li_temp_spi."   value='".$ls_concepto."' title='".$ls_concepto."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>"; 
				  $objectSpi[$li_temp_spi][3] = "<input type=text name=txtprocspi".$li_temp_spi."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=32 maxlength=45>";
				  $objectSpi[$li_temp_spi][4] = "<input type=text name=txtdocspi".$li_temp_spi."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center>";
				  $objectSpi[$li_temp_spi][5] = "<input type=text name=txtopespi".$li_temp_spi."    value='".$ls_operacion."' class=sin-borde readonly style=text-align:center size=7 maxlength=6>";
				  $objectSpi[$li_temp_spi][6] = "<input type=text name=txtmontospi".$li_temp_spi."  value='".number_format($as_monto,2,",",".")."' class=sin-borde readonly style=text-align:center size=15 maxlength=19>";
				  $objectSpi[$li_temp_spi][7] = "<a href=javascript:uf_delete_Spi('".$li_temp_spi."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Ingreso' width=15 height=15 border=0></a>";	
				  $ldec_monto_spi=$as_monto;

				 $li_row_scg=1;
				 $objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$as_sccuenta."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
				 $objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
				 $objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_concepto."'  title='".$ls_concepto."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
				 $objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
				 $objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='D' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
				 $objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($as_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
				 $objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
				 $objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";


				 $li_row_scg++;
				 $objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$ls_spicon."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
				 $objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
				 $objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_concepto."'  title='".$ls_concepto."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
				 $objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
				 $objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='H' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
				 $objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($as_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
				 $objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
				 $objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
				  $ldec_mondeb=$as_monto;
				  $ldec_monhab=$as_monto;
				break;
				case "ND":
					$as_montohaber=$as_monto+$ldec_montoislr;
					$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
					
					$li_temp_spg=1;
					$objectSpg[$li_temp_spg][1]  = "<input type=text name=txtcuenta".$li_temp_spg."       id=txtcuenta".$li_temp_spg."       value='".$ls_spgcta."' class=sin-borde readonly style=text-align:center size=10 maxlength=25>";
					$objectSpg[$li_temp_spg][2]  = "<input type=text name=txtprogramatico".$li_temp_spg." id=txtprogramatico".$li_temp_spg." value='".$ls_codestpro."' class=sin-borde readonly style=text-align:center size=30 maxlength=129><input type=hidden name=hidestcla".$li_temp_spg." id=hidestcla".$li_temp_spg." value=''>"; 
					$objectSpg[$li_temp_spg][3]  = "<input type=text name=txtdocumento".$li_temp_spg."    id=txtdocumento".$li_temp_spg."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center size=13 maxlength=15>";
					$objectSpg[$li_temp_spg][4]  = "<input type=text name=txtdescripcion".$li_temp_spg."  id=txtdescripcion".$li_temp_spg."  value='".$ls_concepto."' class=sin-borde readonly style=text-align:left>";
					$objectSpg[$li_temp_spg][5]  = "<input type=text name=txtprocede".$li_temp_spg."      id=txtprocede".$li_temp_spg."      value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=5 maxlength=6>";
					$objectSpg[$li_temp_spg][6]  = "<input type=text name=txtoperacion".$li_temp_spg."    id=txtoperacion".$li_temp_spg."    value='".$ls_operacion."' class=sin-borde readonly style=text-align:center size=5 maxlength=3>";
					$objectSpg[$li_temp_spg][7]  = "<input type=text name=txtmonto".$li_temp_spg."        id=txtmonto".$li_temp_spg."        value='".number_format($as_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=15 maxlength=19>";		
					$objectSpg[$li_temp_spg][8]  = "<a href=javascript:uf_delete_Spg('".$li_temp_spg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle Presupuestario de Gasto' width=15 height=15 border=0></a>";	


				 $li_row_scg=1;
				 $objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$ls_spgcon."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
				 $objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
				 $objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_concepto."'  title='".$ls_concepto."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
				 $objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
				 $objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='D' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
				 $objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($as_monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
				 $objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
				 $objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
				 if($ldec_montoislr>0)
				 {
					 $li_row_scg++;
					 $objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$ls_islrcon."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
					 $objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
					 $objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_concepto."'  title='".$ls_concepto."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
					 $objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
					 $objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='D' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
					 $objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($ldec_montoislr,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
					 $objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
					 $objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
				 
				 }

				 $li_row_scg++;
				 $objectScg[$li_row_scg][1] = "<input type=text name=txtcontable".$li_row_scg." id=txtcontable".$li_row_scg."  value='".$as_sccuenta."' class=sin-borde readonly style=text-align:center size=15 maxlength=25>";		
				 $objectScg[$li_row_scg][2] = "<input type=text name=txtdocscg".$li_row_scg."    value='".$as_numdoc."' class=sin-borde readonly style=text-align:center size=15 maxlength=15>";
				 $objectScg[$li_row_scg][3] = "<input type=text name=txtdesdoc".$li_row_scg."    value='".$ls_concepto."'  title='".$ls_concepto."' class=sin-borde readonly style=text-align:left size=30 maxlength=254>";
				 $objectScg[$li_row_scg][4] = "<input type=text name=txtprocdoc".$li_row_scg."   value='".$ls_procede."' class=sin-borde readonly style=text-align:center size=8 maxlength=6>";
				 $objectScg[$li_row_scg][5] = "<input type=text name=txtdebhab".$li_row_scg."    value='H' class=sin-borde readonly style=text-align:center size=8 maxlength=1>"; 
				 $objectScg[$li_row_scg][6] = "<input type=text name=txtmontocont".$li_row_scg." value='".number_format($as_montohaber,2,",",".")."' class=sin-borde readonly style=text-align:right size=16 maxlength=22>";
				 $objectScg[$li_row_scg][7] = "<input type=text name=txtcodded".$li_row_scg." value='".$ls_codded."' class=sin-borde readonly style=text-align:right size=5 maxlength=5>";
				 $objectScg[$li_row_scg][8] = "<a href=javascript:uf_delete_Scg('".$li_row_scg."');><img src=../shared/imagebank/tools15/eliminar.gif alt='Eliminar detalle contable' width=15 height=15 border=0></a>";
				  $ldec_mondeb=$as_montohaber;
				  $ldec_monhab=$as_montohaber;
				break;
			}	
		
		}
		
		unset($arrResultado);
		$arrResultado['objectScg']=$objectScg;
		$arrResultado['li_row_scg']=$li_row_scg;
		$arrResultado['ldec_mondeb']=$ldec_mondeb;
		$arrResultado['ldec_monhab']=$ldec_monhab;
		$arrResultado['objectSpg']=$objectSpg;
		$arrResultado['li_temp_spg']=$li_temp_spg;
		$arrResultado['ldec_monto_spg']=$ldec_monto_spg;
		$arrResultado['objectSpi']=$objectSpi;
		$arrResultado['li_temp_spi']=$li_temp_spi;
		$arrResultado['ldec_monto_spi']=$ldec_monto_spi;
		return $arrResultado;
}
	
	function uf_select_oficina($as_codoficom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_oficina
		//         Access: public 
		//      Argumento: $as_codoficom    // codigo de oficina
		//	      Returns: Retorna un Booleano
		//    Description: 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación:  								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_denoficom="";
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT codoficom,denoficom".
				" FROM scb_oficinacomercial  ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codoficom='".$as_codoficom."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->OficinaComercial MÉTODO->uf_saf_select_sede ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if ($row=$this->io_sql->fetch_row($rs_data))
		{
			$ls_denoficom = $row["denoficom"];
		}
		return $ls_denoficom;

	}//fin de la function uf_select_oficina 

	function uf_select_formapago($as_codforpag)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_formapago
		//         Access: public 
		//      Argumento: $as_codforpag    // codigo de formapago
		//	      Returns: Retorna un Booleano
		//    Description: 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación:  								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_denforpag="";
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="SELECT codforpag,denforpag".
				" FROM scb_formapago  ".
				" WHERE codemp='".$ls_codemp."'".
				"   AND codforpag='".$as_codforpag."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->FormaPago MÉTODO->uf_saf_select_sede ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		if ($row=$this->io_sql->fetch_row($rs_data))
		{
			$ls_denforpag = $row["denforpag"];
		}
		return $ls_denforpag;

	}//fin de la function uf_select_formapago 

	
}
// fin de la clase scb_c_movbanco
?>