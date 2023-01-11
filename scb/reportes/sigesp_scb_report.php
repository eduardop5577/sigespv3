<?php
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scb_report
{
	public function __construct($conn)
	{
	  require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	  require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
  	  require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
  	  require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
  	  require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
	  require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	  $this->fun = new class_funciones();
	  $this->SQL= new class_sql($conn);
	  $this->SQL_aux= new class_sql($conn);
	  $this->io_msg= new class_mensajes();
	  $this->dat_emp=$_SESSION["la_empresa"];
	  $this->ds_disponibilidad=new class_datastore();
	  $this->ds_documentos=new class_datastore();
	  $this->ds_voucher1=new class_datastore();
	  $this->ds_colocacion=new class_datastore();
	  $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	  $in=new sigesp_include();
	  $this->con=$in->uf_conectar();
	 //$this->con->debug=true;	
	}
	function uf_cargar_chq_voucher($ls_numdoc,$ls_voucher,$ls_codban,$ls_ctaban,$ls_codope,$li_impanulado=0)
	{
		$ls_concat=$this->con->Concat('rpc_beneficiario.apebene','rpc_beneficiario.nombene');
		$ls_conced=$this->con->Concat('rpc_beneficiario.nacben',"'-'",'rpc_beneficiario.ced_bene');
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(rpc_beneficiario.apebene,', ',rpc_beneficiario.nombene)";
				break;
			case "MYSQLI":
				$ls_cadena="CONCAT(rpc_beneficiario.apebene,', ',rpc_beneficiario.nombene)";
				break;
			case "POSTGRES":
				$ls_cadena="rpc_beneficiario.apebene||', '||rpc_beneficiario.nombene";
				break;
			case "INFORMIX":
				$ls_cadena="rpc_beneficiario.apebene||', '||rpc_beneficiario.nombene";
				break;
		    case "oci8po":
				$ls_cadena=$ls_concat;
			break;
		}
                $ls_criterio = " AND scb_movbco.estmov<>'A' ";
                if ($li_impanulado == 0)
                {
                    $ls_criterio .= " AND scb_movbco.estmov<>'O' ";
                } 
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($ls_voucher=="")
		{
		   $ls_sql="SELECT scb_movbco.*, scb_ctabanco.dencta,nomban,scb_ctabanco.codtipcta,scb_ctabanco.ctabanext,".
		           "       (SELECT rpc_proveedor.rifpro ".
				   "	    FROM rpc_proveedor ".
				   "		WHERE rpc_proveedor.codemp='".$ls_codemp."' ".
				   "		AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) as rifpro,".
				   "       (SELECT {$ls_conced} ".
				   "	    FROM rpc_beneficiario ".
				   "		WHERE rpc_beneficiario.codemp='".$ls_codemp."' AND ".
				   "		rpc_beneficiario.codemp=scb_movbco.codemp AND ".
				   "        rpc_beneficiario.ced_bene=scb_movbco.ced_bene) as cedben,".
				   "       (SELECT rpc_beneficiario.rifben ".
				   "	    FROM rpc_beneficiario ".
				   "		WHERE rpc_beneficiario.codemp='".$ls_codemp."' AND ".
				   "		rpc_beneficiario.codemp=scb_movbco.codemp AND ".
				   "        rpc_beneficiario.ced_bene=scb_movbco.ced_bene) as rifben,".
		   		   "       (SELECT nomusu ".
				   "	    FROM sss_usuarios ".
				   "		WHERE sss_usuarios.codemp='".$ls_codemp."' ".
				   "		AND sss_usuarios.codusu=scb_movbco.codusu) as nomusureg,".
				   "       (SELECT apeusu ".
				   "	    FROM sss_usuarios ".
				   " 		WHERE sss_usuarios.codemp='".$ls_codemp."' ".
				   "		AND sss_usuarios.codusu=scb_movbco.codusu) as apeusureg,".
				   "		(CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				   "                                        FROM rpc_proveedor ".
				   "                                       WHERE rpc_proveedor.codemp=scb_movbco.codemp ".
				   "                                         AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) ".
				   "                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				   "                                        FROM rpc_beneficiario ".
				   "                                       WHERE rpc_beneficiario.codemp=scb_movbco.codemp ".
				   "                                         AND rpc_beneficiario.ced_bene=scb_movbco.ced_bene) ".
				   "                       ELSE 'NINGUNO' END ) AS nombre,".
				   "		   (CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.codban ".
				   "										FROM rpc_proveedor ".
				   "										WHERE rpc_proveedor.codemp=scb_movbco.codemp ".
				   "										AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) ".
				   "							WHEN 'B' THEN (SELECT rpc_beneficiario.codban ".
				   "										FROM rpc_beneficiario ".
				   "										WHERE rpc_beneficiario.codemp=scb_movbco.codemp ".
				   "										AND rpc_beneficiario.ced_bene=scb_movbco.ced_bene) ". 
				   "							ELSE '---' END ) AS codbanben, ".
				   "						(CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.ctaban ".
				   "										FROM rpc_proveedor ".
				   "										WHERE rpc_proveedor.codemp=scb_movbco.codemp ".
				   "										AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) ".
				   "							WHEN 'B' THEN (SELECT rpc_beneficiario.ctaban ".
				   "										FROM rpc_beneficiario ".
				   "										WHERE rpc_beneficiario.codemp=scb_movbco.codemp ".
				   "										AND rpc_beneficiario.ced_bene=scb_movbco.ced_bene) ". 
				   "							ELSE '---' END ) AS codctabanben ".
				   " FROM scb_movbco, scb_ctabanco,scb_banco".
				   " WHERE scb_movbco.codemp='".$ls_codemp."'".
				   " AND scb_movbco.numdoc='".$ls_numdoc."' ".
				   " AND (scb_movbco.chevau='".$ls_voucher."' OR scb_movbco.chevau=' ' OR scb_movbco.chevau is NULL)".
				   " AND scb_movbco.codban='".$ls_codban."' ".
				   " AND scb_movbco.ctaban='".$ls_ctaban."' ".
                                   $ls_criterio.
				   " AND scb_movbco.ctaban=scb_ctabanco.ctaban".
				   " AND scb_movbco.codban=scb_ctabanco.codban".
				   " AND scb_banco.codban=scb_movbco.codban".
				   " AND scb_banco.codemp=scb_movbco.codemp".
				   " AND scb_movbco.codope='".$ls_codope."'";//print $ls_sql;
		}
	    else
	    {
			$ls_sql="SELECT scb_movbco.*,scb_ctabanco.dencta,nomban,scb_ctabanco.codtipcta,scb_ctabanco.ctabanext,
						   (SELECT rpc_proveedor.rifpro
							   FROM rpc_proveedor
							  WHERE rpc_proveedor.codemp='".$ls_codemp."'
								AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) as rifpro,".
					 "       (SELECT {$ls_conced} ".
				     "	    FROM rpc_beneficiario ".
				     "		WHERE rpc_beneficiario.codemp='".$ls_codemp."' AND ".
				     "		rpc_beneficiario.codemp=scb_movbco.codemp AND ".
				     "       rpc_beneficiario.ced_bene=scb_movbco.ced_bene) as cedben,".
				     "       (SELECT rpc_beneficiario.rifben ".
				     "	    FROM rpc_beneficiario ".
				     "		WHERE rpc_beneficiario.codemp='".$ls_codemp."' AND ".
				     "		rpc_beneficiario.codemp=scb_movbco.codemp AND ".
				     "        rpc_beneficiario.ced_bene=scb_movbco.ced_bene) as rifben,".
					 "	       (SELECT nomusu 
				   	    FROM sss_usuarios 
				   		WHERE sss_usuarios.codemp='".$ls_codemp."' 
				   		AND sss_usuarios.codusu=scb_movbco.codusu) as nomusureg,
				          (SELECT apeusu 
				   	    FROM sss_usuarios 
				    		WHERE sss_usuarios.codemp='".$ls_codemp."' 
				   		AND sss_usuarios.codusu=scb_movbco.codusu) as apeusureg,
						(CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.nompro 
														   FROM rpc_proveedor 
														  WHERE rpc_proveedor.codemp=scb_movbco.codemp 
															AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) 
										  WHEN 'B' THEN (SELECT ".$ls_cadena." 
														   FROM rpc_beneficiario 
														  WHERE rpc_beneficiario.codemp=scb_movbco.codemp 
															AND rpc_beneficiario.ced_bene=scb_movbco.ced_bene) 
										  ELSE 'NINGUNO' END ) AS nombre,
						(CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.codban 
													FROM rpc_proveedor 
													WHERE rpc_proveedor.codemp=scb_movbco.codemp 
													AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) 
										WHEN 'B' THEN (SELECT rpc_beneficiario.codban 
													FROM rpc_beneficiario 
													WHERE rpc_beneficiario.codemp=scb_movbco.codemp 
													AND rpc_beneficiario.ced_bene=scb_movbco.ced_bene) 
										ELSE '---' END ) AS codbanben,
									(CASE tipo_destino WHEN 'P' THEN (SELECT rpc_proveedor.ctaban 
													FROM rpc_proveedor 
													WHERE rpc_proveedor.codemp=scb_movbco.codemp 
													AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) 
										WHEN 'B' THEN (SELECT rpc_beneficiario.ctaban 
													FROM rpc_beneficiario 
													WHERE rpc_beneficiario.codemp=scb_movbco.codemp 
													AND rpc_beneficiario.ced_bene=scb_movbco.ced_bene) 
										ELSE '---' END ) AS codctabanben
					 FROM scb_movbco, scb_ctabanco, scb_banco
					WHERE scb_movbco.codemp='".$ls_codemp."'
					  AND numdoc='".$ls_numdoc."'
					  AND scb_movbco.chevau='".$ls_voucher."'
					  AND scb_movbco.codban='".$ls_codban."'
					  AND scb_movbco.ctaban='".$ls_ctaban."'
					  ".$ls_criterio."
					  AND scb_movbco.ctaban=scb_ctabanco.ctaban
					  AND scb_movbco.codban=scb_ctabanco.codban
					  AND scb_banco.codban=scb_movbco.codban
					  AND scb_banco.codemp=scb_movbco.codemp
					  AND scb_movbco.codope='".$ls_codope."'";//print $ls_sql;
		}
		$rs_data=$this->SQL->select($ls_sql);
		$this->rs_cheque = $rs_data;
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data)){$data=$this->SQL->obtener_datos($rs_data);	}
			else{$data=array();}
			$this->SQL->free_result($rs_data);
		}

		return $data;
	}

	function uf_cargar_chq_voucher_ret($ls_numdoc,$ls_voucher,$ls_codban,$ls_ctaban,$ls_codope)
	{

		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($ls_voucher=="")
		{
		   $ls_sql="SELECT scb_movbco_scg.*
		   			FROM scb_movbco_scg,scb_movbco
					WHERE scb_movbco.codemp='".$ls_codemp."'
					  AND scb_movbco_scg.numdoc='".$ls_numdoc."'
				      AND scb_movbco.chevau='".$ls_voucher."'
					  AND scb_movbco.codban='".$ls_codban."'
					  AND scb_movbco.ctaban='".$ls_ctaban."'";//print $ls_sql;
		}
	    else
	    {
			$ls_sql="SELECT scb_movbco_scg.*
		   			FROM scb_movbco_scg,scb_movbco
					WHERE scb_movbco.codemp='".$ls_codemp."'
					  AND scb_movbco_scg.numdoc='".$ls_numdoc."'
				      AND scb_movbco.chevau='".$ls_voucher."'
					  AND scb_movbco.codban='".$ls_codban."'
					  AND scb_movbco.ctaban='".$ls_ctaban."'";//print $ls_sql;
		}

		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data)){$data=$this->SQL->obtener_datos($rs_data);	}
			else{$data=array();}
			$this->SQL->free_result($rs_data);
		}

		return $data;
	}

	function uf_cargar_chq_voucher_ret_spg($ls_numdoc,$ls_voucher,$ls_codban,$ls_ctaban,$ls_codope)
	{

		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($ls_voucher=="")
		{
		   $ls_sql="SELECT scb_movbco_spg.*
		   			FROM scb_movbco_spg,scb_movbco
					WHERE scb_movbco.codemp='".$ls_codemp."'
					  AND scb_movbco_spg.numdoc='".$ls_numdoc."'
				      AND scb_movbco.chevau='".$ls_voucher."'
					  AND scb_movbco.codban='".$ls_codban."'
					  AND scb_movbco.ctaban='".$ls_ctaban."'";//print $ls_sql;
		}
	    else
	    {
			$ls_sql="SELECT scb_movbco_spg.*
		   			FROM scb_movbco_spg,scb_movbco
					WHERE scb_movbco.codemp='".$ls_codemp."'
					  AND scb_movbco_spg.numdoc='".$ls_numdoc."'
				      AND scb_movbco.chevau='".$ls_voucher."'
					  AND scb_movbco.codban='".$ls_codban."'
					  AND scb_movbco.ctaban='".$ls_ctaban."'";//print $ls_sql;
		}

		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data)){$data=$this->SQL->obtener_datos($rs_data);	}
			else{$data=array();}
			$this->SQL->free_result($rs_data);
		}

		return $data;
	}

	function uf_cargar_chq_voucher_anulado($as_numdoc,$as_voucher,$as_codban,$as_ctaban,$as_codope)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cargar_chq_voucher_anulado
		//         Access: public
		//	    Arguments: as_numdoc    // Numero de Documento
		//                 as_voucher   // Numero de Voucher
		//                 as_codban    // Codigo de Banco
		//                 as_ctaban    // Cuenta de Banco
		//                 as_codope    // Codigo de Operacion
		//	      Returns: La data obtenida en el select.
		//    Description: Funcion que busca la informacion necesaria para imprimir un cheque Anulado.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/03/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		if($as_voucher=="")
		{
			$ls_sql="SELECT scb_movbco.numdoc,scb_movbco.fecmov,scb_movbco.codban,scb_movbco.monto,scb_movbco.nomproben,".
					"		scb_movbco.conanu,scb_movbco.fechaanula,scb_movbco.chevau,scb_movbco.monret,scb_movbco.conmov,scb_movbco.ctaban".
					"  FROM scb_movbco".
					" WHERE codemp='".$ls_codemp."'".
					"   AND numdoc='".$as_numdoc."'".
					"   AND (chevau='".$as_voucher."' OR chevau=' ')".
					"   AND codban='".$as_codban."'".
					"   AND ctaban='".$as_ctaban."'".
					"   AND estmov='A'".
					"   AND codope='".$as_codope."'";
		}
	    else
	    {
			$ls_sql="SELECT scb_movbco.numdoc,scb_movbco.fecmov,scb_movbco.codban,scb_movbco.monto,scb_movbco.nomproben,".
					"		scb_movbco.conanu,scb_movbco.fechaanula,scb_movbco.chevau,scb_movbco.monret,scb_movbco.conmov,scb_movbco.ctaban".
					"  FROM scb_movbco".
					" WHERE codemp='".$ls_codemp."'".
					"   AND numdoc='".$as_numdoc."'".
					"   AND chevau='".$as_voucher."'".
					"   AND codban='".$as_codban."'".
					"   AND ctaban='".$as_ctaban."'".
					"   AND estmov='A'".
					"   AND codope='".$as_codope."'";
		}
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data)){$data=$this->SQL->obtener_datos($rs_data);	}
			else{$data=array();}
			$this->SQL->free_result($rs_data);
		}

		return $data;
	}

	function uf_actualizar_status_impreso($ls_numdoc,$ls_voucher,$ls_codban,$ls_ctaban,$ls_codope)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql="UPDATE scb_movbco SET estimpche=1
				 WHERE codemp='".$ls_codemp."' AND numdoc='".$ls_numdoc."' AND chevau='".$ls_voucher."' AND codban='".$ls_codban."'
				 AND ctaban='".$ls_ctaban."' AND codope='".$ls_codope."'";
		$li_result=$this->SQL->execute($ls_sql);
		if($li_result===false)
		{
			$this->is_msg_error="Error al actualizar status de impresión, ".$this->fun->uf_convertirmsg($this->SQL->message);
			return false;
		}
		else
		{
			return true;
		}
	}

	function uf_select_solicitudes($ls_numdoc,$ls_codban,$ls_ctaban)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_solicitudes="";	$i=0;
		$ls_sql="SELECT *
				 FROM cxp_sol_banco
				 WHERE codemp='".$ls_codemp."' AND numdoc='".$ls_numdoc."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'";
		
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$i=$i+1;
				if($i==1){$ls_solicitudes=$row["numsol"];}
				else{$ls_solicitudes=$ls_solicitudes."-".$row["numsol"];}
			}
			$this->SQL->free_result($rs_data);
		}
		return $ls_solicitudes;
	}
	
	function uf_select_dt_deducciones($ls_numsol)
	{
		$arrDed = array();
		$arrSol = explode('-', $ls_numsol);
		$numSol = count((array)$arrSol);
		$filSol = '';
		for ($i = 0; $i < $numSol; $i++) {
			if ($i == 0) {
				$filSol .= " SOL.numsol = '{$arrSol[$i]}' ";
			}
			else {
				$filSol .= " OR SOL.numsol = '{$arrSol[$i]}' ";
			}
		}
		$ls_sql = "SELECT DED.sc_cuenta,CUE.denominacion, SUM(DED.monret) AS monret
					FROM cxp_rd_deducciones DED
						INNER JOIN cxp_dt_solicitudes SOL ON DED.codemp=SOL.codemp AND  DED.numrecdoc=SOL.numrecdoc AND DED.codtipdoc=SOL.codtipdoc AND DED.ced_bene=SOL.ced_bene AND DED.cod_pro=SOL.cod_pro
						INNER JOIN scg_cuentas CUE ON DED.codemp=CUE.codemp AND DED.sc_cuenta=CUE.sc_cuenta
					WHERE DED.codemp = '{$_SESSION["la_empresa"]["codemp"]}' AND {$filSol}
					GROUP BY DED.sc_cuenta,CUE.denominacion";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
		}
		else
		{
			$i = 0;
			while (!$rs_data->EOF) {
				$arrDed[$i] = array('sc_cuenta'=>'<b>'.$rs_data->fields['sc_cuenta'].'</b>','denominacion'=>'<b>'.$rs_data->fields['denominacion'].'</b>',
									'monret'=>'<b>'.number_format($rs_data->fields['monret'],2,',','.').'</b>');
				
				$i++;
				$rs_data->MoveNext();
			}
			unset($rs_data);
		}
		
		return $arrDed;
	}
	
	function uf_select_solicitudes_completa($ls_numdoc,$ls_codban,$ls_ctaban)
	{

		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_solicitudes="";
		$ls_fechas="";
		$i=0;
		$ls_sql="SELECT cxp_sol_banco.*, cxp_solicitudes.fecemisol as fecha
				 FROM cxp_sol_banco
				 INNER JOIN cxp_solicitudes USING (codemp,numsol)
				 WHERE cxp_sol_banco.codemp='".$ls_codemp."' AND cxp_sol_banco.numdoc='".$ls_numdoc."' AND cxp_sol_banco.codban='".$ls_codban."' AND cxp_sol_banco.ctaban='".$ls_ctaban."'";

		$rs_data=$this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$i=$i+1;
				if($i==1)
				{
					$ls_solicitudes=$row["numsol"];
					$ls_ano=substr($row["fecha"],0,4);
					$ls_mes=substr($row["fecha"],5,2);
					$ls_dia=substr($row["fecha"],8,2);
					$ls_fechas=$ls_dia.'/'.$ls_mes.'/'.$ls_ano;
				}
				else
				{
					$ls_solicitudes=$ls_solicitudes."-".$row["numsol"];
					$ls_ano=substr($row["fecha"],0,4);
					$ls_mes=substr($row["fecha"],5,2);
					$ls_dia=substr($row["fecha"],8,2);
					$ls_fecha=$ls_dia.'/'.$ls_mes.'/'.$ls_ano;
					$ls_fechas=$ls_fechas."-".$ls_fechas;
				}
			}
			$this->SQL->free_result($rs_data);
		}
		return $ls_solicitudes.'@'.$ls_fechas;
	}
	
	function uf_select_guarderias($ls_cedbene,$ls_codguard)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_nombene="";
		$i=0;
		$ls_sql="SELECT sno_guarderias.nombene as nombene
				 FROM sno_guarderias
				 WHERE sno_guarderias.codemp='".$ls_codemp."' 
				 AND sno_guarderias.codper='".$ls_cedbene."' 
				 AND sno_guarderias.codguar='".$ls_codguard."'"; 
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$i=$i+1;
				if($i==1){$ls_nombene=$row["nombene"];}
				else{$ls_nombene=$row["nombene"];}
			}
			$this->SQL->free_result($rs_data);
		}
		return $ls_nombene;
	}
	function uf_select_codguarderias($ls_cedbene,$ls_solicitudes)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_codguar="";	
		$i=0;
		$ls_sql=" SELECT cxp_dt_solicitudes.numrecdoc as numrecdoc ".
				" FROM cxp_rd, cxp_dt_solicitudes ".
				" WHERE cxp_rd.codemp='".$ls_codemp."' ".
				" AND cxp_rd.ced_bene='".$ls_cedbene."' ".
				" AND cxp_dt_solicitudes.numsol='".$ls_solicitudes."'".
				" AND cxp_rd.codemp=cxp_dt_solicitudes.codemp ".
				" AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc ".
				" AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc ".
				" AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene";
				 

		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$i=$i+1;
				if($i==1){$ls_codguar=$row["numrecdoc"];}
				else{$ls_codguar=$row["numrecdoc"];}
			}
			$this->SQL->free_result($rs_data);
		}
		return $ls_codguar;
	}
	function uf_select_solicitudes_con_enter($ls_numdoc,$ls_codban,$ls_ctaban)
	{
		//*Igual que la anterior pero coloca un \n entre las solicitudes*/
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_solicitudes="";	$i=0;
		$ls_sql="SELECT *
				 FROM cxp_sol_banco
				 WHERE codemp='".$ls_codemp."' AND numdoc='".$ls_numdoc."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'";

		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error al cargar cheque voucher, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$i=$i+1;
				if($i==1){$ls_solicitudes=$row["numsol"];}
				else{$ls_solicitudes=$ls_solicitudes."-"."\n".$row["numsol"];}
			}
			$this->SQL->free_result($rs_data);
		}
		return $ls_solicitudes;
	}

	function uf_select_data($SQL,$ls_cadena,$ls_campo)
	{
		$data=$SQL->select($ls_cadena);
		if($row=$SQL->fetch_row($data)){	$ls_result=$row[$ls_campo];}
		else{$ls_result="";	}
		$SQL->free_result($data);
		return $ls_result;
	}

	function uf_select_rowdata($SQL,$ls_cadena)
	{
		$data=$SQL->select($ls_cadena);
		if($row=$SQL->fetch_row($data)){	$la_result=$row;}
		else{$la_result=array();	}
		$SQL->free_result($data);
		return $la_result;
	}

	function uf_cargar_dt_scg($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$dt_scg=array();
		$y=0;

		if(!empty($as_codope)){$ls_cad=" AND a.codope='".$as_codope."'";}
		else{$ls_cad="";}

		$ls_sql="SELECT   a.scg_cuenta,a.debhab,a.monto,a.desmov,b.denominacion as denominacion,a.codded as codded
				 FROM     scb_movbco_scg a,scg_cuentas b
				 WHERE    a.codemp='".$ls_codemp ."' AND a.numdoc ='".$as_numdoc."' and a.codban='".$as_codban."' and a.ctaban='".$as_ctaban."' AND a.scg_cuenta=b.sc_cuenta ".$ls_cad."
				 ORDER BY a.debhab asc,a.scg_cuenta asc ";


		$rs_scg=$this->SQL->select($ls_sql);
		if(($rs_scg===false))
		{
			$lb_valido=false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_scg))
			{
				$y=$y+1;
				$dt_scg["scg_cuenta"][$y]=$row["scg_cuenta"];
				$dt_scg["denominacion"][$y]=$row["denominacion"];
				$dt_scg["debhab"][$y]=$row["debhab"];
				$dt_scg["monto"][$y]=$row["monto"];
				$dt_scg["desmov"][$y]=$row["desmov"];
				$dt_scg["codded"][$y]=$row["codded"];
			}
			$this->SQL->free_result($rs_scg);
		}
		return $dt_scg;

	}

	function uf_cargar_dt_spi($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$dt_spi=array();
		$y=0;

		if(!empty($as_codope)){$ls_cad=" AND a.codope='".$as_codope."'";}
		else{$ls_cad="";}

		$ls_sql="SELECT   a.spi_cuenta,a.monto,a.desmov,b.denominacion as denominacion ".
				"FROM     scb_movbco_spi a,spi_cuentas b ".
				"WHERE    a.codemp='".$ls_codemp ."' AND a.numdoc ='".$as_numdoc."' ".
				"AND a.codban='".$as_codban."' AND a.ctaban='".$as_ctaban."' AND a.spi_cuenta=b.spi_cuenta ".$ls_cad." ".
				"ORDER BY a.spi_cuenta asc ";

		$rs_spi=$this->SQL->select($ls_sql);
		if(($rs_spi===false))
		{
			$lb_valido=false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_spi))
			{
				$y=$y+1;
				$dt_spi["spi_cuenta"][$y]=$row["spi_cuenta"];
				$dt_spi["denominacion"][$y]=$row["denominacion"];
				$dt_spi["monto"][$y]=$row["monto"];
				$dt_spi["desmov"][$y]=$row["desmov"];
			}
			$this->SQL->free_result($rs_spi);
		}
		return $dt_spi;

	}

	function uf_cargar_dt_spg($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_cadena=""	;
		$dt_spg=array();
		$y=0;
		$ls_concat=$this->con->Concat('b.codestpro1','b.codestpro2','b.codestpro3','b.codestpro4','b.codestpro5');
		if(!empty($as_codope)){$ls_cad=" AND codope='".$as_codope."'";}
		else{$ls_cad="";}
		switch ($ls_gestor){
		  case 'MYSQLT':
		    $ls_cadena="CONCAT(b.codestpro1,b.codestpro2,b.codestpro3,b.codestpro4,b.codestpro5)";
		  break;
		  case 'MYSQLI':
		    $ls_cadena="CONCAT(b.codestpro1,b.codestpro2,b.codestpro3,b.codestpro4,b.codestpro5)";
		  break;
		  case 'POSTGRES':
		    $ls_cadena="(b.codestpro1 || b.codestpro2 || b.codestpro3 || b.codestpro4 || b.codestpro5)";
		  break;
		  case 'INFORMIX':
		    $ls_cadena="(b.codestpro1 || b.codestpro2 || b.codestpro3 || b.codestpro4 || b.codestpro5)";
		  break;
		  case "oci8po":
				$ls_cadena=$ls_concat;
		  break;
		}

		$ls_sql="SELECT   a.desmov,a.codestpro as codestpro,a.spg_cuenta as spg_cuenta,a.monto as monto,b.denominacion as denominacion
				 FROM     scb_movbco_spg a,spg_cuentas b
				 WHERE    a.codemp='".$ls_codemp ."' AND a.numdoc ='".$as_numdoc."'
				 and a.codban='".$as_codban."' and a.ctaban='".$as_ctaban."'
				 AND a.spg_cuenta=b.spg_cuenta
				 AND a.codestpro=$ls_cadena
				 AND a.estcla=b.estcla
				  ".$ls_cad."
				 ORDER BY a.codestpro,a.spg_cuenta asc";
		$rs_spg=$this->SQL->select($ls_sql);

		if(($rs_spg===false))
		{
			$lb_valido=false;
			print "Ocurrio error uf_cargar_dt_spg";
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_spg))
			{
				$y=$y+1;
				$dt_spg["spg_cuenta"][$y]=trim($row["spg_cuenta"]);
				$ls_programatica = $row["codestpro"];
				$ls_codestpro1   = substr(substr($ls_programatica,0,25),-$_SESSION["la_empresa"]["loncodestpro1"]);
				$ls_codestpro2   = substr(substr($ls_programatica,25,25),-$_SESSION["la_empresa"]["loncodestpro2"]);
				$ls_codestpro3   = substr(substr($ls_programatica,50,25),-$_SESSION["la_empresa"]["loncodestpro3"]);
				if ($ls_estmodest==2)
				   {
					 $ls_codestpro4   = substr(substr($ls_programatica,75,25),-$_SESSION["la_empresa"]["loncodestpro4"]);
					 $ls_codestpro5   = substr(substr($ls_programatica,100,25),-$_SESSION["la_empresa"]["loncodestpro5"]);
					 $ls_programatica = $ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3."-".$ls_codestpro4."-".$ls_codestpro5;
				   }
				else
				   {
				     $ls_programatica=$ls_codestpro1."-".$ls_codestpro2."-".$ls_codestpro3;
				   }
				$dt_spg["estpro"][$y]		= $ls_programatica;
				$dt_spg["denominacion"][$y] = $row["denominacion"];
				$dt_spg["monto"][$y]		= $row["monto"];
				$dt_spg["desmov"][$y]		= $row["desmov"];
			}
			$this->SQL->free_result($rs_spg);
		}
		return $dt_spg;
	}

	function uf_cargar_dt_spg_voucher($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$ls_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_cadena=""	;
		$dt_spg=array();
		$y=0;
		if(!empty($as_codope)){$ls_cad=" AND codope='".$as_codope."'";}
		else{$ls_cad="";}
		switch ($ls_gestor){
		  case 'MYSQLT':
		    $ls_cadena="CONCAT(b.codestpro1,b.codestpro2,b.codestpro3,b.codestpro4,b.codestpro5)";
		  break;
		  case 'MYSQLI':
		    $ls_cadena="CONCAT(b.codestpro1,b.codestpro2,b.codestpro3,b.codestpro4,b.codestpro5)";
		  break;
		  case 'POSTGRES':
		    $ls_cadena="(b.codestpro1 || b.codestpro2 || b.codestpro3 || b.codestpro4 || b.codestpro5)";
		  break;
		  case 'INFORMIX':
		    $ls_cadena="(b.codestpro1 || b.codestpro2 || b.codestpro3 || b.codestpro4 || b.codestpro5)";
		  break;
		}

		$ls_sql="SELECT   a.desmov,a.codestpro as codestpro,a.spg_cuenta as spg_cuenta,a.monto as monto,b.denominacion as denominacion
				 FROM     scb_movbco_spg a,spg_cuentas b
				 WHERE    a.codemp='".$ls_codemp ."' AND a.numdoc ='".$as_numdoc."'
				 and a.codban='".$as_codban."' and a.ctaban='".$as_ctaban."'
				 AND a.spg_cuenta=b.spg_cuenta
				 AND a.codestpro=$ls_cadena
				  ".$ls_cad."
				 ORDER BY a.codestpro,a.spg_cuenta asc";
		$rs_spg=$this->SQL->select($ls_sql);

		if(($rs_spg===false))
		{
			$lb_valido=false;
			print "Ocurrio error uf_cargar_dt_spg";
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_spg))
			{
				$y=$y+1;
				$dt_spg["spg_cuenta"][$y]=trim($row["spg_cuenta"]);
				$ls_programatica = $row["codestpro"];
				$ls_codestpro1   = substr(substr($ls_programatica,0,25),-$_SESSION["la_empresa"]["loncodestpro1"]);
				$ls_codestpro2   = substr(substr($ls_programatica,25,25),-$_SESSION["la_empresa"]["loncodestpro2"]);
				$ls_codestpro3   = substr(substr($ls_programatica,50,25),-$_SESSION["la_empresa"]["loncodestpro3"]);
				if ($ls_estmodest==2)
				   {
					 $ls_codestpro4   = substr(substr($ls_programatica,75,25),-$_SESSION["la_empresa"]["loncodestpro4"]);
					 $ls_codestpro5   = substr(substr($ls_programatica,100,25),-$_SESSION["la_empresa"]["loncodestpro5"]);
					 $ls_programatica = $ls_codestpro1."     ".$ls_codestpro2."     ".$ls_codestpro3."     ".$ls_codestpro4."     ".$ls_codestpro5;
				   }
				else
				   {
				     $ls_programatica=$ls_codestpro1."     ".$ls_codestpro2."     ".$ls_codestpro3;
				   }
				$dt_spg["estpro"][$y]		= $ls_programatica;
				$dt_spg["denominacion"][$y] = $row["denominacion"];
				$dt_spg["monto"][$y]		= $row["monto"];
				$dt_spg["desmov"][$y]		= $row["desmov"];
			}
			$this->SQL->free_result($rs_spg);
		}
		return $dt_spg;
	}

	function uf_cargar_dt_spgop($as_numdoc,$as_codban,$as_ctaban,$as_codope)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$dt_spg=array();
		$y=0;
		if(!empty($as_codope)){$ls_cad=" AND codope='".$as_codope."'";}
		else{$ls_cad="";}
		$ls_gestor   = $_SESSION["ls_gestor"];
		  switch ($ls_gestor){
			case 'MYSQLT':
			  $ls_straux = " CONCAT(b.codestpro1,b.codestpro2,b.codestpro3,b.codestpro4,b.codestpro5) ";
			break;
			case 'MYSQLI':
			  $ls_straux = " CONCAT(b.codestpro1,b.codestpro2,b.codestpro3,b.codestpro4,b.codestpro5) ";
			break;
			case 'POSTGRES':
			  $ls_straux = " b.codestpro1||b.codestpro2||b.codestpro3||b.codestpro4||b.codestpro5 ";
			break;
			case 'INFORMIX':
			  $ls_straux = " b.codestpro1||b.codestpro2||b.codestpro3||b.codestpro4||b.codestpro5 ";
			break;
		  }

		$ls_sql="SELECT   a.codestpro as codestpro,a.spg_cuenta as spg_cuenta,a.monto as monto,b.denominacion as denominacion,a.coduniadm as coduniadm
				 FROM     scb_movbco_spgop a,spg_cuentas b
				 WHERE    a.codemp='".$ls_codemp ."' AND a.numdoc ='".$as_numdoc."' and a.codban='".$as_codban."' and a.ctaban='".$as_ctaban."' AND a.spg_cuenta=b.spg_cuenta AND a.codestpro=$ls_straux ".$ls_cad."
				 ORDER BY a.codestpro,a.spg_cuenta asc";
		$rs_spg=$this->SQL->select($ls_sql);

		if(($rs_spg===false))
		{
			$lb_valido=false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_spg))
			{
				$y=$y+1;
				$dt_spg["spg_cuenta"][$y]=$row["spg_cuenta"];
				$dt_spg["estpro"][$y]=substr($row["codestpro"],0,20).substr($row["codestpro"],20,6).substr($row["codestpro"],26,3);
				$dt_spg["denominacion"][$y]=$row["denominacion"];
				$dt_spg["coduniadm"][$y]=$row["coduniadm"];
				$dt_spg["monto"][$y]=$row["monto"];
			}
			$this->SQL->free_result($rs_spg);
		}
		return $dt_spg;
	}


	function uf_generar_estado_cuenta($ls_codemp,$ls_codban,$ls_ctaban,$ls_orden,$ld_fecdesde,$ld_fechasta,$ldec_saldoanterior,$ldec_total_debe,$ldec_total_haber,$lb_libro_banco,$as_tiprep,$as_codconmov)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//            Function: uf_generar_estado_cuenta
		//		        Access: private
		//	    	 Arguments:
		//      	 $ls_codemp //Código de la Empresa.
		//      	 $ls_codban //Código del Banco.
		//      	 $ls_ctaban //Número de la Cuenta Bancaria asociada al Banco.
		//            $ls_orden //Parámetro de Orden para la presentación del reporte.
		//         $ld_fecdesde //Fecha a partir del cual se buscaran los registros.
		//         $ld_fechasta //Fecha hasta el cual se buscaran los registros.
		//  $ldec_saldoanterior //Monto total del Saldo anterior de esa cuenta en ese Banco.
		//     $ldec_total_debe //Monto total de movimientos afectando el debe.
		//    $ldec_total_haber //Monto total de movimientos afectando el haber.
		//      $lb_libro_banco
		//         Description: Función que carga los detalles de los movimientos Bancarios para ser reportados en el Libro a Banco.
		//	        Creado Por: Ing. Néstor Falcón.                         Modificado Por: Ing. Néstor Falcón.
		//      Fecha Creación: 24/07/2007                       Fecha Última Modificación: 08/08/2007.
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$ldec_creditostmp    = 0;
		$ldec_creditostmpneg = 0;
		$ldec_debitostmp     = 0;
		$ldec_debitostmpneg  = 0;
		$ldec_debitosant     = 0;
		$ldec_creditosant    = 0;
		$ldec_saldoanterior  = 0;
		$ldec_total_debe	 = 0;
		$ldec_total_haber	 = 0;

		$ds_edocta   = new  class_datastore();
		$ls_gestor   = $_SESSION["ls_gestor"];
		$ld_fecdesde = $this->fun->uf_convertirdatetobd($ld_fecdesde);
		$ld_fechasta = $this->fun->uf_convertirdatetobd($ld_fechasta);

		$ls_sql="SELECT codope ,(monto-monret) as monto, estmov
				   FROM scb_movbco
				  WHERE codemp='".$ls_codemp."'
				    AND codban='".$ls_codban."'
					AND ctaban='".$ls_ctaban."'
					AND fecmov < '".$ld_fecdesde."'";
		$rs_data = $this->SQL->select($ls_sql);
		if ($rs_data===false)
		   {
			 print $this->SQL->message;
			 return false;
		   }
		else
		   {
			 while($row=$this->SQL->fetch_row($rs_data))
			 {
				    $ls_estmov = $row["estmov"];
				    $ls_codope = $row["codope"];
				    $ldec_monto=number_format($row["monto"],2,".","");
			 	    if((($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE"))&&($ls_estmov!="A"))	
					{
						$ldec_creditostmp=number_format($ldec_creditostmp+$ldec_monto,2,".","");
					}
				    if((($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE"))&&($ls_estmov=="A"))
					{
						$ldec_creditostmpneg=number_format($ldec_creditostmpneg+$ldec_monto,2,".","");
					}
				    if((($ls_codope=="DP")||($ls_codope=="NC"))&&($ls_estmov!="A"))
					{
						$ldec_debitostmp=number_format($ldec_debitostmp+$ldec_monto,2,".","");
					}
				    if((($ls_codope=="DP")||($ls_codope=="NC"))&&($ls_estmov=="A"))
					{
						$ldec_debitostmpneg=number_format($ldec_debitostmpneg+$ldec_monto,2,".","");
					}
			 }
			 $this->SQL->free_result($rs_data);
			 $ldec_debitosant    = number_format($ldec_debitostmp-$ldec_debitostmpneg,2,".","");
			 $ldec_creditosant   = number_format($ldec_creditostmp-$ldec_creditostmpneg,2,".","");
			 $ldec_saldoanterior = number_format($ldec_debitosant-$ldec_creditosant,2,".","");
		   }
		switch ($ls_orden){
			case 'D':
				$ls_sql_orden=' ORDER BY scb_movbco.numdoc ASC';
				break;
			case 'F':
				$ls_sql_orden=' ORDER BY scb_movbco.fecmov ASC';
				break;
			case 'B':
				$ls_sql_orden=' ORDER BY scb_movbco.codban ASC';
				break;
			case 'C':
				$ls_sql_orden=' ORDER BY scb_movbco.ctaban ASC';
				break;
			case 'O':
				$ls_sql_orden=' ORDER BY scb_movbco.codope ASC';
				break;
		}
		  switch ($ls_gestor){
			case 'MYSQLT':
			  $ls_straux = " CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5) ";
			break;
			case 'MYSQLI':
			  $ls_straux = " CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5) ";
			break;
			case 'POSTGRES':
			  $ls_straux = " codestpro1||codestpro2||codestpro3||codestpro4||codestpro5 ";
			break;
			case 'INFORMIX':
			  $ls_straux = " codestpro1||codestpro2||codestpro3||codestpro4||codestpro5 ";
			break;
		  }

		$ls_tabla   = "";
		$ls_straux2 = "";
	    $ls_cadaux  = "";

		if ($as_codconmov!='---')
		   {
		     $ls_tabla  = ', scb_concepto';
			 $ls_straux2 = " AND scb_movbco.codconmov = '".$as_codconmov."'
			                 AND scb_movbco.codconmov=scb_concepto.codconmov";
		   }
		switch ($as_tiprep){
		  case 'C':
			   $ls_sql = "SELECT scb_movbco.fecmov,scb_movbco.codope,scb_movbco.numdoc,scb_movbco.cod_pro,scb_movbco.ced_bene,
								 scb_movbco.nomproben, scb_movbco.estmov, scb_movbco.conmov as concepto,
								 (scb_movbco.monto-scb_movbco.monret) as montot
							FROM scb_movbco, scb_banco, scb_ctabanco, scb_tipocuenta
						   WHERE scb_movbco.codemp = '".$ls_codemp."'
							 AND scb_movbco.codban = '".$ls_codban."'
							 AND scb_movbco.ctaban = '".$ls_ctaban."'
							 AND scb_movbco.fecmov BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."'
							 AND scb_movbco.codemp = scb_banco.codemp
							 AND scb_movbco.codban = scb_banco.codban
							 AND scb_movbco.codemp = scb_ctabanco.codemp
							 AND scb_movbco.codban = scb_ctabanco.codban
							 AND scb_movbco.ctaban = scb_ctabanco.ctaban
							 AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta $ls_sql_orden ";
		  break;
		  case 'D':
			   $ls_sql = "SELECT scb_movbco.fecmov,scb_movbco.codope,scb_movbco.numdoc,scb_movbco.nomproben, scb_movbco_spg.estmov,
								 scb_movbco_spg.spg_cuenta,spg_cuentas.denominacion as denctaspg, scb_movbco_spg.codestpro,scb_movbco.conmov as concepto,
								 scb_movbco_spg.monto, scb_movbco.monret, (scb_movbco.monto - scb_movbco.monret) as montot, scb_movbco.cod_pro,
								 scb_movbco.ced_bene, scb_movbco.tipo_destino
						    FROM scb_movbco, scb_movbco_spg, spg_cuentas $ls_tabla
						   WHERE scb_movbco.codemp = '".$ls_codemp."'
							 AND scb_movbco.codban = '".$ls_codban."'
							 AND scb_movbco.ctaban = '".$ls_ctaban."'
							 AND scb_movbco.fecmov BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' $ls_straux2
							 AND scb_movbco.codemp = scb_movbco_spg.codemp
							 AND scb_movbco.codban = scb_movbco_spg.codban
							 AND scb_movbco.ctaban = scb_movbco_spg.ctaban
							 AND scb_movbco.numdoc = scb_movbco_spg.numdoc
							 AND scb_movbco.codope = scb_movbco_spg.codope
							 AND scb_movbco.estmov = scb_movbco_spg.estmov
							 AND scb_movbco_spg.codemp = spg_cuentas.codemp
							 AND scb_movbco_spg.spg_cuenta = spg_cuentas.spg_cuenta
							 AND scb_movbco_spg.codestpro = $ls_straux
							 $ls_sql_orden";
		  break;
		}
		//print $ls_sql;
		$rs_data = $this->SQL->select($ls_sql);
		if ($rs_data===false)
		   {
			 print $this->SQL->message;
			 return false;
		   }
		else
		   {
			 while($row=$this->SQL->fetch_row($rs_data))
			      {
				    $ldec_creditos    = 0;
					$ldec_creditosneg = 0;
					$ldec_debitos     = 0;
					$ldec_debitosneg  = 0;

					$ls_codope    = $row["codope"];
				    $ls_estmov    = $row["estmov"];
				    $ls_conmov	  = $row["concepto"];
				    $ls_numdoc	  = $row["numdoc"];
				    $ls_nomproben = $row["nomproben"];
				    $ld_fecmov    = $row["fecmov"];
					$ld_montot    = $row["montot"];
				    if ($as_tiprep=='D')
					   {
					     $ls_codpro    = $row["cod_pro"];
						 $ls_cedben    = $row["ced_bene"];
						 $ls_region    = "";
						 $ls_tipproben = $row["tipo_destino"];
						 if ($ls_tipproben=='P')
						    {
							  $ls_codproben = $ls_codpro;
							}
						 elseif($ls_tipproben=='B')
						    {
							  $ls_codproben = $ls_codpro;
							}
						 if ($ls_tipproben=='B' || $ls_tipproben=='P')
						    {
							  $ls_region = $this->uf_load_region($ls_codemp,$ls_tipproben,$ls_codproben);
							}
						 $ls_codestpro = $row["codestpro"];
					     $ls_spgcta    = $row["spg_cuenta"];
					     $ls_denctaspg = $row["denctaspg"];
	   					 $ld_monspg    = $row["monto"];
					   }
				    if ((($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE"))&&($ls_estmov!="A"))
				       {
				         if ($as_tiprep=='C')
						    {
							  $ldec_creditos = ($ldec_creditos+$ld_montot);
							}
						 else
						    {
							  $ldec_creditos = ($ldec_creditos+$ld_monspg);
							}
				       }
				    elseif($ls_estmov=="A")
				       {
					     if (($ls_codope!="CH")&&($ls_codope!="ND")&&($ls_codope!="RE"))
					        {
					          if ($as_tiprep=='C')
						         {
							       $ldec_creditos = ($ldec_creditos+$ld_montot);
							     }
						      else
						         {
							       $ldec_creditos = ($ldec_creditos+$ld_monspg);
							     }
						    }
				       }
				    if ((($ls_codope=="DP")||($ls_codope=="NC"))&&($ls_estmov!="A"))
				       {
				         if ($as_tiprep=='C')
						    {
						      $ldec_debitos=$ldec_debitos+$ld_montot;
							}
						 else
						    {
							  $ldec_debitos = ($ldec_debitos+$ld_monspg);
							}
					   }
				    elseif($ls_estmov=="A")
				       {
					     if (($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE"))
					        {
				              if ($as_tiprep=='C')
						         {
						           $ldec_debitos = ($ldec_debitos+$ld_montot);
						         }
						      else
						         {
							       $ldec_debitos = ($ldec_debitos+$ld_monspg);
							     }
				            }
					   }
				    $ldec_total_debe  = ($ldec_total_debe+$ldec_debitos);
				    $ldec_total_haber = ($ldec_total_haber+$ldec_creditos);
				    if (!$lb_libro_banco)
				       {
				 	     $ls_operacion = $this->fun->iif_string("'".$ls_codope."'=='CH'","Cheque",$this->fun->iif_string("'".$ls_codope."'=='ND'",'Nota Debito',$this->fun->iif_string("'".$ls_codope."'=='RE'",'Retiro',$this->fun->iif_string("'".$ls_codope."'=='NC'",'Nota Credito',$this->fun->iif_string("'".$ls_codope."'=='DP'",'Deposito','')))));
				       }
				    else
				       {
					     $ls_operacion=$ls_codope;
				       }
				    $ds_edocta->insertRow("operacion",$ls_operacion);
				    $ds_edocta->insertRow("conmov",$ls_conmov);
				    $ds_edocta->insertRow("codope",$ls_codope);
				    $ds_edocta->insertRow("estmov",$ls_estmov);
				    $ds_edocta->insertRow("numdoc",$ls_numdoc);
				    $ds_edocta->insertRow("fecmov",$ld_fecmov);
				    $ds_edocta->insertRow("beneficiario",$ls_nomproben);
				    $ds_edocta->insertRow("debitos",$ldec_debitos);
				    $ds_edocta->insertRow("creditos",$ldec_creditos);
			        if ($as_tiprep=='D')
					   {
				         $ds_edocta->insertRow("codestpro",$ls_codestpro);
				         $ds_edocta->insertRow("spg_cuenta",$ls_spgcta);
				         $ds_edocta->insertRow("denctaspg",$ls_denctaspg);
						 $ds_edocta->insertRow("monto",$ld_monspg);
					     $ds_edocta->insertRow("region",$ls_region);
					   }
				  }
			 $this->SQL->free_result($rs_data);
		   }
		$arrResultado["ldec_saldoanterior"]=number_format($ldec_saldoanterior,2,".","");
		$arrResultado["ldec_total_debe"]=number_format($ldec_total_debe,2,".","");
		$arrResultado["ldec_total_haber"]=number_format($ldec_total_haber,2,".","");
		$arrResultado["data"]=$ds_edocta->data;
		return $arrResultado;
	}

	function uf_generar_estado_cuenta_colocacion($as_codemp,$as_codbandes,$as_ctabandes,$as_codbanhas,$as_ctabanhas,$ad_fecdesde,$ad_fechasta,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//            Function: uf_generar_estado_cuenta
		//		        Access: private
		//	    	 Arguments:
		//      	 $ls_codemp //Código de la Empresa.
		//      	 $ls_codban //Código del Banco.
		//      	 $ls_ctaban //Número de la Cuenta Bancaria asociada al Banco.
		//            $ls_orden //Parámetro de Orden para la presentación del reporte.
		//         $ld_fecdesde //Fecha a partir del cual se buscaran los registros.
		//         $ld_fechasta //Fecha hasta el cual se buscaran los registros.
		//  $ldec_saldoanterior //Monto total del Saldo anterior de esa cuenta en ese Banco.
		//     $ldec_total_debe //Monto total de movimientos afectando el debe.
		//    $ldec_total_haber //Monto total de movimientos afectando el haber.
		//      $lb_libro_banco
		//         Description: Función que carga los detalles de los movimientos Bancarios para ser reportados en el Libro a Banco.
		//	        Creado Por: Ing. Néstor Falcón.                         Modificado Por: Ing. Néstor Falcón.
		//      Fecha Creación: 24/07/2007                       Fecha Última Modificación: 08/08/2007.
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		 $io_funcion = new class_funciones();
		 $as_codemp=$_SESSION["la_empresa"]["codemp"];
		 $li_bco=0;
		 $li_cta=0;
		 $li_cta_ban=0;
		 $ls_sqlaux = "";
		 $ls_sql_orden="";
		 $ad_fecdesde = $io_funcion->uf_convertirdatetobd($ad_fecdesde);
		 $ad_fechasta = $io_funcion->uf_convertirdatetobd($ad_fechasta);
		 if(($as_codbandes!="")&&($as_codbanhas!=""))
		 {	
			$ls_sqlaux=" AND a.codban between '".$as_codbandes."' and '".$as_codbanhas."' ";
			$li_bco=1;
		 }
		 
		 if(($as_ctabandes!="")&&($as_ctabanhas!=""))
		 {	
		 	$ls_sqlaux=$ls_sqlaux." AND a.ctaban between '".$as_ctabandes."' and '".$as_ctabanhas."'";
			$li_cta=1;
		 }
		 $li_cta_ban=$li_bco+$li_cta;
		 
		 switch ($as_orden){
			case 'D':
				$ls_sql_orden=' ORDER BY a.numcol ASC';
				break;
			case 'F':
				$ls_sql_orden=' ORDER BY a.numcol ASC';
				break;
			case 'O':
				$ls_sql_orden=' ORDER BY a.numcol ASC';
				break;
			case 'P':
				$ls_sql_orden=' ORDER BY a.codban ASC';
				break;
			case 'FE':
				$ls_sql_orden=' ORDER BY a.feccol ASC';
				break;
			case 'FV':
				$ls_sql_orden=' ORDER BY a.fecvencol ASC';
				break;
			case 'C':
				$ls_sql_orden=' ORDER BY a.ctaban ASC';
				break;
				
		}
		 //AND scb_movcol.numdoc like '%".$ls_documento."%' No Borrar!!
		 //AND scb_movcol.numcol like '%".$ls_numcol."%' No Borrar!!
			if ($li_cta_ban==0)
			{
				$ls_sql=   " SELECT a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.numcol as numcol, ".
						   " a.dencol as dencol ,a.codtipcol as codtipcol,d.nomtipcol as nomtipcol,a.feccol as feccol, a.diacol as diacol, ".
						   " a.tascol as tascol,a.monto as monto,a.fecvencol as fecvencol,a.monint as monint, a.sc_cuenta as sc_cuenta, ".
						   " e.denominacion as denominacion,a.spi_cuenta as spi_cuenta, a.estreicol as estreicol,a.codconmov,a.cod_pro, ".
						   " a.ced_bene,a.sc_cuentacob, a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla, ".
						   " (SELECT denconmov from scb_concepto where scb_concepto.codconmov=a.codconmov) as denconmov, ".
						   " (SELECT nompro from rpc_proveedor where rpc_proveedor.codemp=a.codemp and rpc_proveedor.cod_pro=a.cod_pro) as nompro, ".
						   " (SELECT nombene from rpc_beneficiario where rpc_beneficiario.codemp=a.codemp and rpc_beneficiario.ced_bene=a.ced_bene) as nombene, ".
						   " (SELECT denominacion from spi_cuentas where spi_cuentas.codemp=a.codemp and spi_cuentas.spi_cuenta=a.spi_cuenta)as denospi, ".
						   " (SELECT denominacion from scg_cuentas where a.codemp=scg_cuentas.codemp and a.sc_cuentacob=scg_cuentas.sc_cuenta)as denocob ".
						   " FROM scb_colocacion a,scb_banco b,scb_ctabanco c,scb_tipocolocacion d,scg_cuentas e ".
						   " WHERE a.codemp=b.codemp ".
						   " AND a.codemp=c.codemp ".
						   " AND a.codemp=e.codemp ".
						   " AND a.codban=b.codban ".
						   " AND a.codban=c.codban ".
						   " AND a.ctaban=c.ctaban ".
						   " AND a.codtipcol=d.codtipcol ".
						   " AND a.codemp='".$as_codemp."' ".
						   " AND a.sc_cuenta=e.sc_cuenta ".
						   " AND a.feccol BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' $ls_sql_orden ";		   
		     }
			 elseif($li_cta_ban==1)
			 {
			 	$ls_sql=   " SELECT a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.numcol as numcol, ".
						   " a.dencol as dencol ,a.codtipcol as codtipcol,d.nomtipcol as nomtipcol,a.feccol as feccol, a.diacol as diacol, ".
						   " a.tascol as tascol,a.monto as monto,a.fecvencol as fecvencol,a.monint as monint, a.sc_cuenta as sc_cuenta, ".
						   " e.denominacion as denominacion,a.spi_cuenta as spi_cuenta, a.estreicol as estreicol,a.codconmov,a.cod_pro, ".
						   " a.ced_bene,a.sc_cuentacob, a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla, ".
						   " (SELECT denconmov from scb_concepto where scb_concepto.codconmov=a.codconmov) as denconmov, ".
						   " (SELECT nompro from rpc_proveedor where rpc_proveedor.codemp=a.codemp and rpc_proveedor.cod_pro=a.cod_pro) as nompro, ".
						   " (SELECT nombene from rpc_beneficiario where rpc_beneficiario.codemp=a.codemp and rpc_beneficiario.ced_bene=a.ced_bene) as nombene, ".
						   " (SELECT denominacion from spi_cuentas where spi_cuentas.codemp=a.codemp and spi_cuentas.spi_cuenta=a.spi_cuenta)as denospi, ".
						   " (SELECT denominacion from scg_cuentas where a.codemp=scg_cuentas.codemp and a.sc_cuentacob=scg_cuentas.sc_cuenta)as denocob ".
						   " FROM scb_colocacion a,scb_banco b,scb_ctabanco c,scb_tipocolocacion d,scg_cuentas e ".
						   " WHERE a.codemp=b.codemp ".
						   " AND a.codemp=c.codemp ".
						   " AND a.codemp=e.codemp ".
						   " AND a.codban=b.codban ".
						   " AND a.codban=c.codban ".
						   " AND a.ctaban=c.ctaban ".
						   " AND a.codtipcol=d.codtipcol ".
						   " AND a.codemp='".$as_codemp."' ".
						   "$ls_sqlaux ".
						   " AND a.sc_cuenta=e.sc_cuenta ".
						   " AND a.feccol BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' $ls_sql_orden ";
			 }
			 else
			 {
			 	$ls_sql=   " SELECT a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.numcol as numcol, ".
						   " a.dencol as dencol ,a.codtipcol as codtipcol,d.nomtipcol as nomtipcol,a.feccol as feccol, a.diacol as diacol, ".
						   " a.tascol as tascol,a.monto as monto,a.fecvencol as fecvencol,a.monint as monint, a.sc_cuenta as sc_cuenta, ".
						   " e.denominacion as denominacion,a.spi_cuenta as spi_cuenta, a.estreicol as estreicol,a.codconmov,a.cod_pro, ".
						   " a.ced_bene,a.sc_cuentacob, a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla, ".
						   " (SELECT denconmov from scb_concepto where scb_concepto.codconmov=a.codconmov) as denconmov, ".
						   " (SELECT nompro from rpc_proveedor where rpc_proveedor.codemp=a.codemp and rpc_proveedor.cod_pro=a.cod_pro) as nompro, ".
						   " (SELECT nombene from rpc_beneficiario where rpc_beneficiario.codemp=a.codemp and rpc_beneficiario.ced_bene=a.ced_bene) as nombene, ".
						   " (SELECT denominacion from spi_cuentas where spi_cuentas.codemp=a.codemp and spi_cuentas.spi_cuenta=a.spi_cuenta)as denospi, ".
						   " (SELECT denominacion from scg_cuentas where a.codemp=scg_cuentas.codemp and a.sc_cuentacob=scg_cuentas.sc_cuenta)as denocob ".
						   " FROM scb_colocacion a,scb_banco b,scb_ctabanco c,scb_tipocolocacion d,scg_cuentas e ".
						   " WHERE a.codemp=b.codemp ".
						   " AND a.codemp=c.codemp ".
						   " AND a.codemp=e.codemp ".
						   " AND a.codban=b.codban ".
						   " AND a.codban=c.codban ".
						   " AND a.ctaban=c.ctaban ".
						   " AND a.codtipcol=d.codtipcol ".
						   " AND a.codemp='".$as_codemp."' ".
						   "$ls_sqlaux ".
						   " AND a.sc_cuenta=e.sc_cuenta ".
						   " AND a.feccol BETWEEN '".$ad_fecdesde."' AND '".$ad_fechasta."' $ls_sql_orden ";
			 }
			 
		  //print $ls_sql."<br>";
		   $rs_colocacion=$this->SQL->select($ls_sql);
		   if (!$rs_colocacion){
				$this->is_msg_error="Error en uf_generar_estado_cuenta_colocacion,".$this->fun->uf_convertirmsg($this->SQL->message);
				print $this->is_msg_error;
		   }
		   
		   return $rs_colocacion;
	}

	function uf_generar_estado_cuenta_colocacion_cfg($as_codemp,$as_numcol,$as_codban,$as_ctaban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//            Function: uf_generar_estado_cuenta
		//		        Access: private
		//	    	 Arguments:
		//      	 $ls_codemp //Código de la Empresa.
		//      	 $ls_codban //Código del Banco.
		//      	 $ls_ctaban //Número de la Cuenta Bancaria asociada al Banco.
		//         Description: Función que carga los detalles de los movimientos Bancarios para ser reportados en el Libro a Banco.
		//	        Creado Por: Ing. Carlos Zambrano                         
		//      Fecha Creación:30/06/2010                       Fecha Última Modificación: 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $as_codemp=$_SESSION["la_empresa"]["codemp"];
			$ls_sql=   " SELECT a.codban as codban,b.nomban as nomban,a.ctaban as ctaban,c.dencta as dencta,a.numcol as numcol, ".
					   " a.dencol as dencol ,a.codtipcol as codtipcol,d.nomtipcol as nomtipcol,a.feccol as feccol, a.diacol as diacol, ".
					   " a.tascol as tascol,a.monto as monto,a.fecvencol as fecvencol,a.monint as monint, a.sc_cuenta as sc_cuenta, ".
					   " e.denominacion as denominacion,a.spi_cuenta as spi_cuenta, a.estreicol as estreicol,a.codconmov,a.cod_pro, ".
					   " a.ced_bene,a.sc_cuentacob, a.codestpro1,a.codestpro2,a.codestpro3,a.codestpro4,a.codestpro5,a.estcla, ".
					   " (SELECT denconmov from scb_concepto where scb_concepto.codconmov=a.codconmov) as denconmov, ".
					   " (SELECT nompro from rpc_proveedor where rpc_proveedor.codemp=a.codemp and rpc_proveedor.cod_pro=a.cod_pro) as nompro, ".
					   " (SELECT nombene from rpc_beneficiario where rpc_beneficiario.codemp=a.codemp and rpc_beneficiario.ced_bene=a.ced_bene) as nombene, ".
					   " (SELECT denominacion from spi_cuentas where spi_cuentas.codemp=a.codemp and spi_cuentas.spi_cuenta=a.spi_cuenta)as denospi, ".
					   " (SELECT denominacion from scg_cuentas where a.codemp=scg_cuentas.codemp and a.sc_cuentacob=scg_cuentas.sc_cuenta)as denocob ".
					   " FROM scb_colocacion a,scb_banco b,scb_ctabanco c,scb_tipocolocacion d,scg_cuentas e ".
					   " WHERE a.codemp=b.codemp ".
					   " AND a.codemp=c.codemp ".
					   " AND a.codemp=e.codemp ".
					   " AND a.codban=b.codban ".
					   " AND a.codban=c.codban ".
					   " AND a.ctaban=c.ctaban ".
					   " AND a.codtipcol=d.codtipcol ".
					   " AND a.codemp='".$as_codemp."' ".
					   " AND a.numcol='".$as_numcol."' ".
					   " AND a.codban='".$as_codban."' ".
					   " AND a.ctaban='".$as_ctaban."' ".
					   " AND a.sc_cuenta=e.sc_cuenta ".
					   " ORDER BY a.numcol ASC ";		   
		  //print $ls_sql."<br>";
		   $rs_colocacion=$this->SQL->select($ls_sql);
		   if (($rs_colocacion===false))
		   {
				$lb_valido=false;
				$this->is_msg_error="Error en uf_generar_estado_cuenta_colocacion_cfg,".$this->fun->uf_convertirmsg($this->SQL->message);
				print $this->is_msg_error;
		   }
		   else
		   {
			   $lb_valido=true;
			   while($row=$this->SQL->fetch_row($rs_colocacion))
			   {
					$this->ds_colocacion->insertRow("ctaban",$row["ctaban"]);
					$this->ds_colocacion->insertRow("numcol",$row["numcol"]);
					$this->ds_colocacion->insertRow("nomban",$row["nomban"]);
					$this->ds_colocacion->insertRow("dencta",$row["dencta"]);
					$this->ds_colocacion->insertRow("sc_cuenta",$row["sc_cuenta"]);
					$this->ds_colocacion->insertRow("dencol",$row["dencol"]);
					$this->ds_colocacion->insertRow("feccol",$row["feccol"]);
					$this->ds_colocacion->insertRow("fecvencol",$row["fecvencol"]);
					$this->ds_colocacion->insertRow("monint",$row["monint"]);
					$this->ds_colocacion->insertRow("diacol",$row["diacol"]);
					$this->ds_colocacion->insertRow("tascol",$row["tascol"]);
					$this->ds_colocacion->insertRow("monto",$row["monto"]);
					$this->ds_colocacion->insertRow("nomtipcol",$row["nomtipcol"]);
					$this->ds_colocacion->insertRow("denominacion",$row["denominacion"]);
			   }
		   	$this->SQL->free_result($rs_colocacion);
		   }
		   return $this->ds_colocacion->data;
	}

    function uf_load_region($as_codemp,$as_tipproben,$as_codproben)
    {
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//            Function: uf_generar_estado_cuenta
	//		        Access: private
	//	    	 Arguments:
	//      	 $ls_codemp //Código de la Empresa.
	//        $as_tipproben //Tipo de P=Proveedor o B=Beneficiario.
	//        $as_codproben //Código del proveedor o cedula del beneficiario
	//         Description: Función que carga el estado de localización de un proveedor o beneficiario determinado.
	//	        Creado Por: Ing. Néstor Falcón.                         Modificado Por: Ing. Néstor Falcón.
	//      Fecha Creación: 24/07/2007                       Fecha Última Modificación: 08/08/2007.
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      if ($as_tipproben=='P')
	     {
		   $ls_campo = 'cod_pro';
		   $ls_tabla = 'rpc_proveedor';
		 }
	  else
	     {
		   $ls_campo = 'ced_bene';
		   $ls_tabla = 'rpc_beneficiario';
		 }
	  $ls_sql = "SELECT sigesp_estados.desest
				   FROM sigesp_pais, sigesp_estados, $ls_tabla
				  WHERE $ls_tabla.codemp='".$as_codemp."'
				    AND $ls_tabla.$ls_campo='".$as_codproben."'
				    AND $ls_tabla.codpai=sigesp_pais.codpai
				    AND $ls_tabla.codest=sigesp_estados.codest";
      $rs_data = $this->SQL->select($ls_sql);
	  if ($rs_data===false)
	     {
		   $lb_valido = false;
		 }
      else
	     {
		   if ($row=$this->SQL->fetch_row($rs_data))
		      {
			    $ls_region = $row["desest"];
			  }
		 }
	  return $ls_region;
	}

function uf_generar_estado_cuenta_resumido($ls_codban,$ls_ctaban,$ld_fecdesde,$ld_fechasta,$ldec_saldoanterior,$ldec_total_debe,$ldec_total_haber,$ls_nomban,$ls_nomtipcta)
	{
		$ldec_creditostmp=0; $ldec_creditostmpneg=0; $ldec_debitostmp=0; $ldec_debitostmpneg=0;	$ldec_debitosant=0; $ldec_creditosant=0; $ldec_saldoanterior=0;
		$ldec_total_debe=0;	$ldec_total_haber=0;
		$ds_edocta=new class_datastore();
		$ld_fecdesde=$this->fun->uf_convertirdatetobd($ld_fecdesde);
		$ld_fechasta=$this->fun->uf_convertirdatetobd($ld_fechasta);
		$ls_sql="SELECT scb_movbco.codope as codope ,(scb_movbco.monto-scb_movbco.monret) as monto, scb_movbco.estmov as estmov,
		                scb_banco.nomban as nomban,scb_tipocuenta.nomtipcta as nomtipcta
				   FROM scb_movbco ,scb_banco ,scb_ctabanco, scb_tipocuenta
				  WHERE scb_movbco.codban='".$ls_codban."'
				    AND scb_movbco.ctaban='".$ls_ctaban."'
					AND scb_movbco.fecmov < '".$ld_fecdesde."'
				    AND scb_movbco.codban=scb_banco.codban
					AND scb_movbco.codban=scb_ctabanco.codban
					AND scb_movbco.ctaban=scb_ctabanco.ctaban
					AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta";
		$rs_data=$this->SQL->select($ls_sql);//echo "1=>".$ls_sql.'<br>';
		if( $rs_data===false)
		{
			print $this->SQL->message;
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_estmov=$row["estmov"];
				$ls_codope=$row["codope"];
				$ldec_monto=$row["monto"];
				if((($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE"))&&($ls_estmov!="A")){$ldec_creditostmp=$ldec_creditostmp+$ldec_monto;}
				if((($ls_codope=="CH")||($ls_codope=="ND")||($ls_codope=="RE"))&&($ls_estmov=="A")){$ldec_creditostmpneg=$ldec_creditostmpneg+$ldec_monto;}
				if((($ls_codope=="DP")||($ls_codope=="NC"))&&($ls_estmov!="A")){$ldec_debitostmp=$ldec_debitostmp+$ldec_monto;}
				if((($ls_codope=="DP")||($ls_codope=="NC"))&&($ls_estmov=="A")){$ldec_debitostmpneg=$ldec_debitostmpneg+$ldec_monto;}
			}
			$this->SQL->free_result($rs_data);
			$ldec_debitosant = $ldec_debitostmp-$ldec_debitostmpneg;
			$ldec_creditosant = $ldec_creditostmp-$ldec_creditostmpneg;
			$ldec_saldoanterior = $ldec_debitosant-$ldec_creditosant;
		}

		$ls_sql="SELECT scb_banco.nomban as nomban,scb_tipocuenta.nomtipcta as nomtipcta
				   FROM scb_banco ,scb_ctabanco ,scb_tipocuenta
				  WHERE scb_banco.codban=scb_ctabanco.codban
				    AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta
				    AND scb_banco.codban='".$ls_codban."'
				    AND scb_ctabanco.ctaban='".$ls_ctaban."'";
		$rs_data=$this->SQL->select	($ls_sql);//echo "echo 2 => ".$ls_sql.'<br>';
		if($rs_data==false)
		{
			return false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_nomtipcta=$row["nomtipcta"];
				$ls_nomban=$row["nomban"];
			}
		}

		$ls_sql="SELECT (CASE codope
						 WHEN 'CH' THEN 'Cheque'
					  	 WHEN 'ND' THEN 'Nota de Debito'
					 	 WHEN 'RE' then 'Retiro'
						 ELSE ' ' END) AS operacion,0 as debitos,sum(monto-monret) as creditos ,count(*) as cantidad, fecmov
				    FROM scb_movbco
				   WHERE codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'
				     AND fecmov >= '".$ld_fecdesde."'
					 AND fecmov <= '".$ld_fechasta."'
				     AND (codope='CH' OR codope='ND' OR codope='RE')
				   GROUP BY codope,fecmov
				   ORDER BY fecmov,operacion";
		$rs_data=$this->SQL->select($ls_sql);
		if( $rs_data===false)
		{
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ldec_creditos=0; $ldec_creditosneg=0; $ldec_debitos=0; $ldec_debitosneg=0;
				$ldec_debitos=$row["debitos"];
				$ldec_creditos=$row["creditos"];
				$ldec_total_debe = $ldec_total_debe+$ldec_debitos;
				$ldec_total_haber= $ldec_total_haber+$ldec_creditos;
				$ls_operacion=$row["operacion"];
				$li_cantidad=$row["cantidad"];
				$ds_edocta->insertRow("operacion",$ls_operacion);
				$ds_edocta->insertRow("debitos",$ldec_debitos);
				$ds_edocta->insertRow("creditos",$ldec_creditos);
				$ds_edocta->insertRow("cantidad",$li_cantidad);
			}
			$this->SQL->free_result($rs_data);
		}
		$ls_sql2="SELECT (CASE codope
						  WHEN 'NC' THEN 'Nota de Credito'
						  WHEN 'DP' THEN 'Deposito'
						  ELSE ' '
						   END) AS operacion,sum(monto-monret) as debitos,0 as creditos ,count(*) as cantidad, fecmov
				   FROM scb_movbco
				  WHERE codban='".$ls_codban."'
				    AND ctaban='".$ls_ctaban."'
				    AND fecmov >= '".$ld_fecdesde."' AND fecmov <= '".$ld_fechasta."'
				    AND (codope='DP' OR codope='NC')
				  GROUP BY codope, fecmov
				  ORDER BY fecmov, operacion";
		$rs_data=$this->SQL->select($ls_sql2);
		if( $rs_data===false)
		{
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ldec_creditos=0; $ldec_creditosneg=0; $ldec_debitos=0; $ldec_debitosneg=0;
				$ldec_debitos=$row["debitos"];
				$ldec_creditos=$row["creditos"];
				$ldec_total_debe = $ldec_total_debe+$ldec_debitos;
				$ldec_total_haber= $ldec_total_haber+$ldec_creditos;
				$ls_operacion=$row["operacion"];
				$li_cantidad=$row["cantidad"];
				$ds_edocta->insertRow("operacion",$ls_operacion);
				$ds_edocta->insertRow("debitos",$ldec_debitos);
				$ds_edocta->insertRow("creditos",$ldec_creditos);
				$ds_edocta->insertRow("cantidad",$li_cantidad);
			}
			$this->SQL->free_result($rs_data);
		}
		$ds_edocta->group_by(array('0'=>'operacion'),array('0'=>'cantidad','1'=>'creditos','2'=>'debitos'),'cantidad');
	
		$arrResultado["ldec_saldoanterior"]=$ldec_saldoanterior;
		$arrResultado["ldec_total_debe"]=$ldec_total_debe;
		$arrResultado["ldec_total_haber"]=$ldec_total_haber;
		$arrResultado["data"]=$ds_edocta->data;
		$arrResultado["ls_nomban"]=$ls_nomban;
		$arrResultado["ls_nomtipcta"]=$ls_nomtipcta;
		return $arrResultado;
	}
function uf_obtener_mov_conciliacion($ls_mesano,$ls_codban,$ls_ctaban,$ldec_salseglib,$ldec_salsegbco)
	{
		$io_fecha=new class_fecha();
		$ds_mov=new class_datastore();
		$ds_movimientos=new class_datastore();
		$ls_codemp=$this->dat_emp["codemp"];
		$ld_fechasta=$io_fecha->uf_last_day(substr($ls_mesano,0,2),substr($ls_mesano,2,4));
		$ld_fechasta=$this->fun->uf_convertirdatetobd($ld_fechasta);
		$ld_fecdesde="01/".substr($ls_mesano,0,2)."/".substr($ls_mesano,2,4);
		$ld_fecdesde=$this->fun->uf_convertirdatetobd($ld_fecdesde);

		$ls_sql="SELECT *
				 FROM scb_movbco
				 WHERE codemp='".$ls_codemp."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND fecmov <='".$ld_fechasta."' AND (estreglib='' OR (estreglib<>'' AND feccon<>'".$ld_fecdesde."'))";

		$rs_data=$this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_codban=$row["codban"];
				$ds_mov->insertRow("codban",$ls_codban);
				$ls_ctaban=$row["ctaban"];
				$ds_mov->insertRow("ctaban",$ls_ctaban);
				$ls_numdoc=$row["numdoc"];
				$ds_mov->insertRow("numdoc",$ls_numdoc);
				$ls_nomproben=$row["nomproben"];
				$ds_mov->insertRow("nomproben",$ls_nomproben);
				$ld_fecmov=$row["fecmov"];
				$ds_mov->insertRow("fecmov",$ld_fecmov);
				$ldec_monto=$row["monto"];
				$ds_mov->insertRow("monto",$ldec_monto);
				$ls_conmov=$row["conmov"];
				$ds_mov->insertRow("conmov" ,$ls_conmov);
				$ls_estmov=$row["estmov"];
				$ds_mov->insertRow("estmov" ,$ls_estmov);
			}
			$this->SQL->free_result($rs_data);
		}
		$ldec_saldo_ant=$this->uf_calcular_saldolibro($ls_codban,$ls_ctaban,$ld_fechasta);

		if(abs($ldec_saldo_ant-$ldec_salseglib)>0.01)
		{
			$this->io_msg->message("Vuelva a modulo conciliación ya que hay movimientos no registrados");
			return false;
		}
		else
		{
			$this->io_msg->message("Todo Bien");
		}

			$ls_sql= "SELECT '01' as tipo, '-' as suma, numdoc , nomproben, fecmov , monto-monret as monto, codope
					  FROM scb_movbco
					  WHERE fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND
					        estreglib=''  AND ((feccon > '".$ld_fecdesde."'  ) OR (feccon='1900-01-01')) AND
					       (((codope='CH' OR codope='ND' OR codope='RE') AND estmov<>'A') OR ((codope='DP' OR codope='NC') AND estmov='A'))";



		$rs_data= $this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message($this->uf_convertirmsg($this->SQL->message));
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);
		}


			$ls_sql= "SELECT '02' as tipo, '+' as suma, numdoc, nomproben, fecmov, monto-monret as monto, codope
					  FROM   scb_movbco
					  WHERE  fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND estreglib=''
					  AND ((feccon > '".$ld_fecdesde."' ) OR (feccon='1900-01-01'))
					  AND (((codope='CH' OR codope='ND' OR codope='RE') AND estmov='A') OR ((codope='DP' OR codope='NC') AND estmov<>'A'))";

		$rs_data= $this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message($this->uf_convertirmsg($this->SQL->message));
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);
		}

		// No Registradas en Libros

		   $ls_sql = "SELECT 'A1' as tipo, '+' as suma, numdoc, conmov as nomproben,fecmov, monto-monret as monto, codope
					  FROM   scb_movbco
					  WHERE  fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'
					  AND  feccon='".$ld_fecdesde."' AND estreglib='A' AND (((codope='CH' OR codope='ND' OR codope='RE') AND estmov<>'A') OR
					  ((codope='DP' OR codope='NC') AND estmov='A'))";

		$rs_data= $this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);

		}

		$ls_sql="SELECT 'A2' as tipo, '-' as suma, numdoc, conmov as nomproben, fecmov, monto-monret as monto, codope
				 FROM  scb_movbco
				 WHERE fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'
				 AND feccon='".$ld_fecdesde."' AND estreglib='A'
				 AND (((codope='CH' OR codope='ND' OR codope='RE') AND estmov='A') OR ((codope='DP' OR codope='NC') AND estmov<>'A'))";

		$rs_data= $this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			print $this->SQL->message;
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);
		}

		// Error Libro
		$ls_sql="SELECT 'B1' as tipo, '+' as suma, numdoc, conmov as nomproben, fecmov , monto-monret as monto, codope
				FROM scb_movbco
				WHERE fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'
				AND feccon='".$ld_fecdesde."' AND estreglib='B'
				AND (((codope='CH' OR codope='ND' OR codope='RE') AND estmov<>'A') OR ((codope='DP' OR codope='NC') AND estmov='A')) ";

		$rs_data= $this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			print $this->SQL->message;
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);
		}

		$ls_sql="SELECT 'B2' as tipo, '-' as suma, numdoc, conmov as nomproben, fecmov , monto-monret as monto, codope
				FROM scb_movbco
				WHERE fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'
				AND feccon='".$ld_fecdesde."' AND estreglib='B'
				AND  (((codope='CH' OR codope='ND' OR codope='RE') AND estmov='A') OR ((codope='DP' OR codope='NC') AND estmov<>'A')) ";

		$rs_data= $this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			print $this->SQL->message;
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);

		}

		// Error Banco
		$ls_sql="SELECT 'C1' as tipo, '-' as suma, numdoc, conmov as nomproben, fecmov, monmov as monto, codope
				 FROM scb_errorconcbco
				 WHERE fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND
				 fecmesano='".$ld_fecdesde."' AND esterrcon='C' AND
				 (((codope='CH' OR codope='ND' OR codope='RE') AND estmov<>'A') OR ((codope='DP' OR codope='NC') AND estmov='A')) ";

		$rs_data= $this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			print $this->SQL->message;
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);
		}

		$ls_sql="SELECT 'C2' as tipo, '+' as suma, numdoc, conmov as nomproben, fecmov , monmov as monto, codope
				 FROM  scb_errorconcbco
		 		 WHERE fecmov <='".$ld_fechasta."' AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."' AND
				 fecmesano='".$ld_fecdesde."' and esterrcon='C' AND
				 (((codope='CH' OR codope='ND' OR codope='RE') AND estmov='A') OR ((codope='DP' OR codope='NC') AND estmov<>'A')) ";

		$rs_data= $this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			print $this->SQL->message;
			return false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo"];
				$ds_movimientos->insertRow("tipo",$ls_tipo);
				$ls_suma=$row["suma"];
				$ds_movimientos->insertRow("suma",$ls_suma);
				$ls_numdoc=$row["numdoc"];
				$ds_movimientos->insertRow("numdoc",$ls_numdoc);
				$ls_cedbene=$row["nomproben"];
				$ds_movimientos->insertRow("nomproben",$ls_cedbene);
				$ls_fecha=$this->fun->uf_convertirfecmostrar($row["fecmov"]);
				$ds_movimientos->insertRow("fecmov",$ls_fecha);
				$ldec_monto=$row["monto"];
				$ds_movimientos->insertRow("monto",$ldec_monto);
				$ls_codope=$row["codope"];
				$ds_movimientos->insertRow("codope",$ls_tipo);
			}
			$this->SQL->free_result($rs_data);
		}
		$arrResultado["data"]=	$ds_movimientos->data;
		$arrResultado["ldec_salsegbco"]= $ldec_salsegbco;			
		return $arrResultado;	
	}

	function uf_calcular($data,$ls_mesano)
	{
		$ds_mov=new class_datastore();
		$ds_mov->data=$data;
		$li_total=$ds_mov->getRowCount("numdoc");
		$ldec_CreditosTmp=0;
		$ldec_CreditosTmpNeg=0;
		$ldec_DebitosTmp=0;
		$ldec_DebitosTmpNeg=0;
		for($li_i=1;$li_i<=$li_total;$li_i++)
		{
			$ls_codope=$ds_mov->getValue("codope",$li_i);
			$ls_estmov=$ds_mov->getValue("estmov",$li_i);
			$ldec_monto=$ds_mov->getValue("monto",$li_i);
			if((($ls_codope=='CH')||($ls_codope=='ND')||($ls_codope=='RE'))&&($ls_estmov<>'A')){$ldec_CreditosTmp=$ldec_CreditosTmp+$ldec_monto;}
			if((($ls_codope=='CH')||($ls_codope=='ND')||($ls_codope=='RE'))&&($ls_estmov=='A')){$ldec_CreditosTmpNeg=$ldec_CreditosTmpNeg+$ldec_monto;}
			if((($ls_codope=='DP')||($ls_codope=='NC'))&&($ls_estmov<>'A'))	{$ldec_DebitosTmp=$ldec_DebitosTmp+$ldec_monto;	}
			if((($ls_codope=='DP')||($ls_codope=='NC'))&&($ls_estmov=='A'))	{$ldec_DebitosTmpNeg=$ldec_DebitosTmpNeg+$ldec_monto;}
		}
		$ldec_DebitosAnt = $ldec_DebitosTmp-$ldec_DebitosTmpNeg;
		$ldec_CreditosAnt = $ldec_CreditosTmp-$ldec_CreditosTmpNeg;
		$ldec_SaldoAnterior = $ldec_DebitosAnt - $ldec_CreditosAnt;
		return round($ldec_SaldoAnterior,2);
	}

	function uf_calcular_saldolibro($as_codban,$as_ctaban,$ad_fecha)
	{
	/////////////////////////////////////////////////////////////////////////////
	// Funtion	    :  uf_calcular_saldolibro
	//
	//	Return	    :  ldec_saldo
	//
	//	Descripcion :  Fucnion que se encarga de obtener el saldo de los movimientos registrdos en libro
	/////////////////////////////////////////////////////////////////////////////
	$ldec_monto_haber=0;$ldec_monto_debe=0;$ldec_saldo=0;

	$ls_codemp = $this->dat_emp["codemp"];
	$ld_fecha = $this->fun->uf_convertirdatetobd($ad_fecha);

	$ls_sql="SELECT monhab,mondeb,(mondeb - monhab) As saldo
			 FROM ( SELECT COALESCE( SUM(monto - monret),0) As monhab
				   FROM  scb_movbco
				   WHERE codban='".$as_codban."' AND ctaban='".$as_ctaban."' AND
	  			   (codope='RE' OR codope='ND' OR codope='CH') AND  estmov<>'A' AND estmov<>'O' AND (estreglib<>'A' or (estreglib='A' AND estcon=1)) AND codemp='".$ls_codemp."' AND fecmov<='".$ld_fecha."') D,
				 ( SELECT COALESCE( SUM(monto - monret),0) As mondeb
				   FROM scb_movbco
				   WHERE codban='".$as_codban."' AND ctaban='".$as_ctaban."' AND
	 			   (codope='NC' OR codope='DP') AND estmov<>'A' AND estmov<>'O' AND (estreglib<>'A' or (estreglib='A' AND estcon=1)) AND codemp='".$ls_codemp."' AND fecmov<='".$ld_fecha."') H ";

	$rs_saldos=$this->SQL->select($ls_sql);
		if(($rs_saldos==false)&&($this->SQL->message!=""))
		{
			print "Saldolibro".$this->SQL->message;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_saldos))
			{
				$ldec_mondeb=$row["mondeb"];
				$ldec_monhab=$row["monhab"];
				$ldec_saldo=$row["saldo"];
				if(is_null($ldec_saldo)){$ldec_saldo=0;}
				if( (is_null($ldec_monto_debe) )&&($ldec_monto_haber>0) ){$ldec_saldo=$ldec_monto_haber;}
				if( (is_null($ldec_monto_haber))&&($ldec_monto_debe>0) ){$ldec_saldo=$ldec_monto_debe;}
			}
			$this->SQL->free_result($rs_saldos);
		}
		return  $ldec_saldo;
	}

	function uf_listado_cheques($ls_codban,$ls_cuenta,$ls_chequera)
	{
		/////////////////////////////////////////////////////////////////////////////
		// Funtion	    :  uf_listado_cheques
		//	Return	    :  la_data
		//	Descripcion :  Fucnion que se encarga de retornar los cheques pertenecnientes a una cuenta y a un banco
		/////////////////////////////////////////////////////////////////////////////
		$ls_codemp = $this->dat_emp["codemp"];
		$la_data=array();
		$ls_sql="SELECT a.codban,a.ctaban,a.numchequera,a.numche,b.nomproben,b.fecmov,a.estche,b.monto,b.numdoc
				   FROM scb_cheques a
				   LEFT OUTER JOIN scb_movbco b
				     ON a.codban=b.codban
					AND a.ctaban=b.ctaban
					AND a.numche=b.numdoc
					AND b.codope='CH'
				  WHERE a.codban='$ls_codban'
				    AND a.ctaban='$ls_cuenta'
					AND a.numchequera='$ls_chequera'";

		    $rs_data=$this->SQL->select($ls_sql);
			if(($rs_data==false)&&($this->SQL->message!=""))
			{
				print $this->SQL->message;
			}
			else
			{
				if($row=$this->SQL->fetch_row($rs_data))
				{
					$la_data=$this->SQL->obtener_datos($rs_data);
					print_r($la_data);
				}
				$this->SQL->free_result($rs_data);
			}
			return  $la_data;
	}

	function uf_comprobante_retencion($ls_numcom,$ls_fechainicio,$ls_fechafin)
	{
		/////////////////////////////////////////////////////////////////////////////
		// Funtion	    :  uf_comprobante_retencion
		//	Return	    :  la_data
		//	Descripcion :  Funcion que se encarga de retornar los datos de los comprobantes de retencion asociados al codigo dado
		//  Autor       :  Ing. Laura Cabré
		//  Fecha       :  03/10/2006
		/////////////////////////////////////////////////////////////////////////////
		$ls_codemp = $this->dat_emp["codemp"];
		$la_data=array();
		$ls_sql = "SELECT scb_cmp_ret.nomsujret, scb_cmp_ret.rif, scb_cmp_ret.estcmpret, scb_cmp_ret.numlic,scb_cmp_ret.fecrep,
				 	      scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.fecfac, scb_dt_cmp_ret.numcon, scb_dt_cmp_ret.numsop,
					      scb_dt_cmp_ret.basimp, scb_dt_cmp_ret.porimp, scb_dt_cmp_ret.iva_ret, scb_dt_cmp_ret.totcmp_con_iva
					 FROM scb_cmp_ret, scb_dt_cmp_ret
				    WHERE scb_cmp_ret.codemp='".$ls_codemp."'
				      AND scb_cmp_ret.numcom='".$ls_numcom."'
					  AND scb_cmp_ret.codret='0000000003'
					  AND scb_cmp_ret.fecrep BETWEEN '".$ls_fechainicio."' AND '".$ls_fechafin."'
					  AND scb_cmp_ret.codemp=scb_dt_cmp_ret.codemp
					  AND scb_cmp_ret.codret=scb_dt_cmp_ret.codret
					  AND scb_cmp_ret.numcom=scb_dt_cmp_ret.numcom";

			$rs_data=$this->SQL->select($ls_sql);

			if ($rs_data===false)
			   {
				print $this->SQL->message;
			   }
			else
			   {
			     if ($row=$this->SQL->fetch_row($rs_data))
				    {
					  $la_data=$this->SQL->obtener_datos($rs_data);
				    }
				 $this->SQL->free_result($rs_data);
			   }
			return  $la_data;
	}



	function uf_formato_cartaorden($as_codigo,$la_data)
	{
		/////////////////////////////////////////////////////////////////////////////
		// Funtion	    :  uf_comprobante_retencion
		//	Return	    :  la_data
		//	Descripcion :  Funcion que se encarga de retornar los datos de los comprobantes de retencion asociados al codigo dado
		//  Autor       :  Ing. Laura Cabré
		//  Fecha       :  03/10/2006
		/////////////////////////////////////////////////////////////////////////////
		$ls_codemp = $this->dat_emp["codemp"];
		$la_data=array();
		$ls_sql="SELECT *
				FROM scb_cartaorden
				WHERE codemp='$ls_codemp' and codigo='$as_codigo'";
			$rs_data=$this->SQL->select($ls_sql);
			if(($rs_data==false)&&($this->SQL->message!=""))
			{
				print $this->SQL->message;
			}
			else
			{
				if($row=$this->SQL->fetch_row($rs_data))
				{
					$la_data=$this->SQL->obtener_datos($rs_data);
				}
				$this->SQL->free_result($rs_data);
			}
		return $la_data;
	}
	function uf_select_cartaorden($as_numdoc,$as_codban,$as_ctaban)
	{
	  /*--------------------------------------------------------------
		Function:	    uf_select_cartaorden
		   Description: Funcion que se buscar los datos de una carta orden especifica
		         Fecha: 26/12/2006
		         Autor: Ing. Laura Cabre
		Modificado Por: Ing. Néstor Falcón.    Fecha Última Modificación: 22/08/2007.
	  ----------------------------------------------------------------------------------*/
		$ls_codemp=$this->dat_emp["codemp"];

		$ls_sql = "SELECT max(scb_movbco.numcarord) as numcarord,scb_movbco.numdoc,max(scb_movbco.ctaban) as ctaban, max(scb_movbco.monto) as monto,
						  max(scb_banco.nomban) as nomban,max(scb_banco.gerban) as gerban,max(scb_tipocuenta.nomtipcta) as nomtipcta, 
						  max(scb_movbco.tipo_destino) as tipo_destino, max(scb_ctabanco.ctabanext) as ctabanext, max(scb_movbco.cod_pro) as cod_pro,
						  max(scb_movbco.ced_bene) as ced_bene,max(scb_movbco.conmov) as conmov, max(scb_movbco.nomproben) as nomproben,
						  max(scb_prog_pago.fecpropag) as fecpropag	
					 FROM scb_movbco, scb_banco, scb_ctabanco , scb_tipocuenta, scb_dt_movbco, scb_prog_pago
					WHERE scb_movbco.codemp='".$ls_codemp."'
					  AND scb_movbco.numcarord='".$as_numdoc."'
					  AND scb_movbco.codban='".$as_codban."'
					  AND scb_movbco.ctaban='".$as_ctaban."'
					  AND scb_movbco.estmov<>'A'
					  AND scb_movbco.estmov<>'O'
					  AND scb_movbco.codope='ND'
					  AND scb_movbco.codemp=scb_banco.codemp
					  AND scb_movbco.codban=scb_banco.codban
					  AND scb_movbco.codemp=scb_ctabanco.codemp
					  AND scb_movbco.codban=scb_ctabanco.codban
					  AND scb_movbco.ctaban=scb_ctabanco.ctaban
					  AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta
					  AND scb_movbco.codemp=scb_dt_movbco.codemp
					  AND scb_movbco.codban=scb_dt_movbco.codban
					  AND scb_movbco.ctaban=scb_dt_movbco.ctaban
					  AND scb_movbco.numdoc=scb_dt_movbco.numdoc
					  AND scb_movbco.codope=scb_dt_movbco.codope
					  AND scb_movbco.estmov=scb_dt_movbco.estmov
					  AND scb_dt_movbco.codban=scb_prog_pago.codban
					  AND scb_dt_movbco.ctaban=scb_prog_pago.ctaban
					  AND scb_dt_movbco.numsolpag=scb_prog_pago.numsol		
					GROUP BY scb_movbco.numdoc";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error uf_select_cartaorden, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
			return false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			  $data=$this->SQL->obtener_datos($rs_data);
			}
			else
			{
			  $data=array();
			}
			$this->SQL->free_result($rs_data);
		}
		return $data;
	}

	function uf_select_banco_cuenta($as_tabla,$as_criterio,$as_nomban,$as_ctaban,$as_rif)
	{
		$lb_valido = true;
		if ($as_tabla=="rpc_proveedor")
		{
			$ls_sql="SELECT scb_banco.nomban, ".$as_tabla.".ctaban, ".$as_tabla.".rifpro  ".
					"  FROM ".$as_tabla.", scb_banco ".
					$as_criterio.
					"   AND ".$as_tabla.".codemp = scb_banco.codemp ".
					"   AND ".$as_tabla.".codban = scb_banco.codban ";
		}
		else
		{
			$ls_sql="SELECT scb_banco.nomban, ".$as_tabla.".ctaban, ".$as_tabla.".rifben  ".
					"  FROM ".$as_tabla.", scb_banco ".
					$as_criterio.
					"   AND ".$as_tabla.".codemp = scb_banco.codemp ".
					"   AND ".$as_tabla.".codban = scb_banco.codban ";
		}
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error uf_select_banco_cuenta, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   if ($as_tabla=='rpc_proveedor')
			   {
				   $as_nomban=$row["nomban"];
				   $as_ctaban=$row["ctaban"];
				   $as_rif=$row["rifpro"];
			   }
			   else
			   {
			   	   $as_nomban=$row["nomban"];
				   $as_ctaban=$row["ctaban"];
				   $as_rif=$row["rifben"];
			   }
			}
			$this->SQL->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["as_nomban"]=$as_nomban;
		$arrResultado["as_ctaban"]=$as_ctaban;
		$arrResultado["as_rif"]=$as_rif;
		return $arrResultado;
	}


	function uf_select_rif($as_tabla,$as_codigo)
	{
		$as_rif="";
		$lb_valido = true;
		if ($as_tabla=="rpc_proveedor")
		{
			$ls_sql="SELECT ".$as_tabla.".rifpro  ".
					"  FROM ".$as_tabla." ".
					" WHERE cod_pro='".$as_codigo."'";
		}
		else
		{
			$ls_sql="SELECT ".$as_tabla.".rifben  ".
					"  FROM ".$as_tabla." ".
					" WHERE ced_bene='".$as_codigo."'";
		}
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error uf_select_rif, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   if ($as_tabla=='rpc_proveedor')
			   {
				   $as_rif=$row["rifpro"];
			   }
			   else
			   {
				   $as_rif=$row["rifben"];
			   }
			}
			$this->SQL->free_result($rs_data);
		}
		return $as_rif;
	}


	function uf_check_tipo_cartaorden($as_numdoc,$as_codban,$as_ctaban,$as_documento)
	{
	  /*--------------------------------------------------------------
		Function:	    uf_select_cartaorden
		Description:	Funcion que se buscar los datos de una carta orden especifica
		Fecha: 26/12/2006
		Autor: Ing. Laura Cabre

	----------------------------------------------------------------------------*/
		$as_documento="";
		$ls_codemp=$this->dat_emp["codemp"];
		$ls_sql="SELECT numdoc
				   FROM   scb_movbco 
				 WHERE codemp='".$ls_codemp."'
				   AND numcarord='$as_numdoc'
				   AND codban='$as_codban'
				   AND ctaban='$as_ctaban'
				   AND codope='ND'";
		$rs_data=$this->SQL->select($ls_sql);

		if($rs_data===false)
		{
			$data=array();
			$this->is_msg_error="Error uf_check_tipo_cartaorden, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->is_msg_error;
			return false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $lb_existe=true;
			   $as_documento=$row["numdoc"];
			}
			else
			{
			   $lb_existe=false;
			}
			$this->SQL->free_result($rs_data);
		}
		$arrResultado["lb_existe"]=$lb_existe;
		$arrResultado["as_documento"]=$as_documento;
		return $arrResultado;
	}



	function uf_select_dt_cartaorden($as_numdoc,$as_codban,$as_ctaban,$as_tipproben)
	{
	  /*--------------------------------------------------------------
		Function:	    uf_select_cartaorden
		Description:	Funcion que se buscar los datos de una carta orden especifica
		Fecha: 26/12/2006
		Autor: Ing. Laura Cabre

	----------------------------------------------------------------------------*/
		$ls_straux = "";
		$ls_codemp=$this->dat_emp["codemp"];
		if ($as_tipproben=='P')
		   {
		     $ls_campo  = "cod_pro";
		     $ls_tabla  = "rpc_proveedor";
		     $ls_straux = ", max(rpc_proveedor.nompro) as nompro, max(rpc_proveedor.ctaban) as ctaban2 ";
			 $ls_group1 = "GROUP BY scb_dt_movbco.cod_pro,scb_dt_movbco.ctaban";
		     $ls_group2 = " GROUP BY scb_movbco.cod_pro,scb_movbco.ctaban";
		   }
		else
		   {
		     $ls_campo  = "ced_bene";
		     $ls_tabla  = "rpc_beneficiario";
		     $ls_straux = ", max(rpc_beneficiario.nombene) as nombene,max(rpc_beneficiario.apebene) as apebene,max(rpc_beneficiario.ctaban) as ctaban2  ";
		     $ls_group1 = " GROUP BY scb_dt_movbco.ced_bene,scb_dt_movbco.ctabanbene";
		     $ls_group2 = " GROUP BY scb_movbco.ced_bene,scb_movbco.ctaban";
		   }
		   $arrResultado="";
		   $lb_existe="";
		   $ls_documento="";
		   $arrResultado=$this->uf_check_tipo_cartaorden($as_numdoc,$as_codban,$as_ctaban,$ls_documento);
		   $lb_existe=$arrResultado["lb_existe"];
		   $ls_documento=$arrResultado["as_documento"];
		if($lb_existe)
		{
			$ls_sql=  "SELECT max(scb_dt_movbco.ctaban) as ctaban,scb_dt_movbco.$ls_campo,".
			          "       sum(scb_dt_movbco.monsolpag) as monto $ls_straux          ".
					  "  FROM scb_dt_movbco, $ls_tabla			 						".
					  " WHERE scb_dt_movbco.codemp='".$ls_codemp."' 					".
					  "   AND scb_dt_movbco.numdoc='".$ls_documento."' 					".
					  "   AND scb_dt_movbco.codban='".$as_codban."' 					".
					  "   AND scb_dt_movbco.ctaban='".$as_ctaban."' 					".
					  "   AND scb_dt_movbco.codemp=$ls_tabla.codemp 					".
					  "   AND scb_dt_movbco.$ls_campo=$ls_tabla.$ls_campo $ls_group1	";
		}
		else
		{
			$ls_sql = "  SELECT max($ls_tabla.ctaban) as ctaban,scb_movbco.$ls_campo, ".
			          "         sum(scb_movbco.monto) as monto $ls_straux   	      ".
					  "    FROM scb_movbco, $ls_tabla 					    	      ".
					  "   WHERE scb_movbco.codemp='".$ls_codemp."' 				      ".
					  "     AND scb_movbco.numcarord='".$as_numdoc."' 			      ".
					  "     AND scb_movbco.codban='".$as_codban."' 				      ".
					  "     AND scb_movbco.ctaban='".$as_ctaban."' 				      ".
					  "     AND scb_movbco.codemp=$ls_tabla.codemp 				      ".
					  "     AND scb_movbco.$ls_campo=$ls_tabla.$ls_campo $ls_group2 ";
		}
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
			$la_data=array();
			$this->is_msg_error="Error uf_select_dt_cartaorden, ".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
			return false;
		}
		else
		{
			$li=0;
			if ($as_tipproben=='B')
			   {
			     while ($row=$this->SQL->fetch_row($rs_data))
			           {
					   	 $ls_rif=$this->uf_select_rif("rpc_beneficiario",$row["ced_bene"]);
				         $li++;
				         $la_data[$li]=array('cedbene'=>$row["ced_bene"],'nombene'=>$row["nombene"].$row["apebene"],'rif'=>$ls_rif,'ctaban'=>$row["ctaban"],'ctaban2'=>$row["ctaban2"],'monto'=>number_format($row["monto"],2,",","."));
			           }
			   }
			elseif($as_tipproben=='P')
			   {
			     while ($row=$this->SQL->fetch_row($rs_data))
					   {
					   	 $ls_rif=$this->uf_select_rif("rpc_proveedor",$row["cod_pro"]);
					     $li++;
						 $la_data[$li]=array('cod_pro'=>$row["cod_pro"],'nompro'=>$row["nompro"],'rif'=>$ls_rif,'ctaban'=>$row["ctaban"],'ctaban2'=>$row["ctaban2"],'monto'=>number_format($row["monto"],2,",","."));
					   }
			   }
			$this->SQL->free_result($rs_data);
		}

		return $la_data;
	}

function uf_generar_cmpret_ordendepagodirecta($ls_codemp,$ls_numdoc,$ls_codban,$ls_ctaban,$ls_tipodestino,$ls_codpro,$ls_cedben,$ls_fecrep,$la_data_cmpret)
	{
		$lb_valido=false;

		if($_SESSION["ls_gestor"]=="MYSQL")
			{
				$ls_conc=" concat(a.codemp,a.numdoc,a.codban,a.ctaban,a.codope)";
				$ls_conc2=" concat(codemp,numdoc,codban,ctaban,codope)";
			}
			else
			{
				$ls_conc="(a.codemp||a.numdoc||a.codban||a.ctaban||a.codope)";
				$ls_conc2=" codemp||numdoc||codban||ctaban||codope";
			}
		
		$ls_sql="SELECT a.numdoc,a.codban,a.ctaban,a.codope,a.baseimp,b.monobjret as monobjret,a.monto as monto_iva,b.monto as monto_ret,a.codcar,b.codded,d.porcar,c.porded,
						c.estretmun,c.islr,c.iva,a.documento
				FROM scb_movbco_spgop a,scb_movbco_scg b,sigesp_deducciones c,sigesp_cargos d
				WHERE a.codemp='".$ls_codemp."' AND a.numdoc='".$ls_numdoc."' AND a.codban='".$ls_codban."' AND a.ctaban='".$ls_ctaban."' AND a.codope='OP' AND a.numdoc=b.numdoc AND a.codope=b.codope AND a.codban=b.codban AND a.ctaban=b.ctaban
				AND a.codcar<>'' AND b.codded<>'0000' AND b.codded<>'00000' AND b.codded=c.codded AND a.codcar=d.codcar AND $ls_conc not in (SELECT $ls_conc2 FROM scb_dt_cmp_ret_op)";
		//print $ls_sql."<br>";
		$rs_data=$this->SQL_aux->select($ls_sql);
		if($rs_data===false)
		{
			$this->is_msg_error="Error uf_generar_cmpret_ordendepagodirecta, ".$this->fun->uf_convertirmsg($this->SQL_aux->message);
			print $this->SQL_aux->message;
			return false;
		}
		else
		{
			$li_control_mun=0;
			$li_control_iva=0;
			$li_control_islr=0;
			$ls_tipocomp="";
			$this->SQL->begin_transaction();
			while($row=$this->SQL_aux->fetch_row($rs_data))
			{

				$li_estretmun=$row["estretmun"];
				$li_iva=$row["iva"];
				$li_islr=$row["islr"];
				$ldec_baseimp=$row["baseimp"];
				$ldec_monobjret=$row["monobjret"];
				$ldec_porded=$row["porded"];
				$ldec_porcar=$row["porcar"];
				$ldec_monret=$row["monto_ret"];
				$ldec_iva=$row["monto_iva"];
				$ldec_monto=$la_data_cmpret['monto'];
				$ldec_totcmpsiniva=($ldec_monto-$ldec_iva);
				$ls_numdocres=$la_data_cmpret['numdocres'];
				$ls_fecdocres=$la_data_cmpret['fecdocres'];
				$ls_nrocontrol=$la_data_cmpret['nrocontrol'];
				$ls_desope=$la_data_cmpret["desope"];
				$ls_numsop=$row["documento"];
				if($li_estretmun==1)
				{
					$li_control_iva=0;
					$li_control_islr=0;
					$li_control_mun++;
					if($li_control_mun==1)
					{
						$ls_tipocomp=" MUNICIPAL ";
						$ls_retid='0000000003';
						$ls_nrocomp="";
						$arrResultado="";
						$arrResultado=$this->uf_guardar_cmpret($ls_codemp,$ls_retid,$ls_nrocomp,$ls_fecrep,$ls_tipodestino,$ls_codpro,$ls_cedben,$ls_tipocomp,$ls_numdoc);
						$lb_valido=$arrResultado["lb_valido"];
						$ls_nrocomp=$arrResultado["ls_nrocomp"];
					}

					$this->uf_guardar_dt_cmpret($ls_codemp,$ls_retid,$ls_nrocomp,$li_control_mun,$ls_fecdocres,$ls_numdocres,$ls_nrocontrol,'',
								  '', '01-reg',$ldec_totcmpsiniva,$ldec_monto,$ldec_monobjret,$ldec_porded,
								  $ldec_monret,$ldec_monret,$ls_desope,$ls_numsop,$ls_codban,$ls_ctaban,$ls_numdoc,'OP');
				}
				if($li_iva==1)
				{
					$li_control_mun=0;
					$li_control_islr=0;
					$li_control_iva++;
					if($li_control_iva==1)
					{
						$ls_tipocomp=" IVA ";
						$ls_retid='0000000001';
						$ls_nrocomp="";
						$arrResultado="";
						$arrResultado=$this->uf_guardar_cmpret($ls_codemp,$ls_retid,$ls_nrocomp,$ls_fecrep,$ls_tipodestino,$ls_codpro,$ls_cedben,$ls_tipocomp,$ls_numdoc);
						$lb_valido=$arrResultado["lb_valido"];
						$ls_nrocomp=$arrResultado["ls_nrocomp"];
					}
					$lb_valido=$this->uf_guardar_dt_cmpret($ls_codemp,$ls_retid,$ls_nrocomp,$li_control_iva,$ls_fecdocres,$ls_numdocres,$ls_nrocontrol,'',
								  '', '01-reg',$ldec_totcmpsiniva,$ldec_monto,$ldec_baseimp,$ldec_porcar,
								  $ldec_iva,$ldec_monret,$ls_desope,$ls_numsop,$ls_codban,$ls_ctaban,$ls_numdoc,'OP');
				}

			}//End while
			if($lb_valido)
			{
				$this->SQL->commit();
			}
			else
			{
				$this->SQL->rollback();
			}
		}
	}

	function uf_guardar_dt_cmpret($ls_codemp,$ls_codret,$ls_numcom,$ls_numope,$ld_fecfac,$ls_numfac,$ls_numcon,$ls_numnd,
								  $ls_numnc,$ls_tiptrans,$ldec_totcmpsiniva,$ldec_totcmpconiva,$ldec_basimp,$ldec_porimp,
								  $ldec_totimp,$ldec_ivaret,$ls_desope,$ls_numsop,$ls_codban,$ls_ctaban,$ls_numdoc,$ls_codope)
	{
		$ls_sql="INSERT INTO scb_dt_cmp_ret_op(codemp, codret, numcom, numope, fecfac, numfac, numcon, numnd,
											  numnc, tiptrans, totcmp_sin_iva, totcmp_con_iva, basimp, porimp,
											   totimp, iva_ret, desope, numsop, codban, ctaban, numdoc, codope)
				 VALUES('".$ls_codemp."','".$ls_codret."','".$ls_numcom."','".$ls_numope."','".$this->fun->uf_convertirdatetobd($ld_fecfac)."','".$ls_numfac."','".$ls_numcon."','".$ls_numnd."',
						'".$ls_numnc."','".$ls_tiptrans."','".$ldec_totcmpsiniva."','".$ldec_totcmpconiva."','".$ldec_basimp."','".$ldec_porimp."',
						'".$ldec_totimp."','".$ldec_ivaret."','".$ls_desope."','".$ls_numsop."','".$ls_codban."','".$ls_ctaban."','".$ls_numdoc."','OP')";

		$li_result=$this->SQL->execute($ls_sql);
		if($li_result===false)
		{
			$this->is_msg_error="Error en guardar_dt_cmp".$this->fun->uf_convertirmsg($this->SQL->message);
			print $this->SQL->message;
			return false;
		}
		else
		{
			return true;
		}
	}

	function uf_select_dt_sujret($ls_tipodestino,$ls_codproben,$ls_codemp)
	{
		if($ls_tipodestino=='P')
		{
			$rs_provben=$this->uf_select_rowdata($this->SQL,"SELECT nompro as nomproben,dirpro as dirproben,rifpro as rifproben,nitpro as nitproben FROM rpc_proveedor WHERE cod_pro ='".$ls_codproben."' AND codemp='".$ls_codemp."'");
		}
		else
		{
			if($_SESSION["ls_gestor"]=="MYSQL")
			{
				$ls_aux=" concat(nombene,apebene) as nomproben";
			}
			else
			{
				$ls_aux=" nombene||apebene as nomproben";
			}
			$rs_provben=$this->uf_select_rowdata($this->SQL,"SELECT ".$ls_aux.",dirbene as dirproben,rifbene as rifproben,ced_bene as nitproben FROM rpc_beneficiario WHERE ced_bene ='".$ls_codproben."' AND codemp='".$ls_codemp."'");
		}
	    return $rs_provben;
	}


	function uf_guardar_cmpret($ls_codemp,$ls_codret,$ls_nrocomp,$ls_fecrep,$ls_tipodestino,$ls_codpro,$ls_cedben,$ls_tipocomp,$ls_numdoc)
	{
		$ls_perfiscal=substr($ls_fecrep,-4).substr($ls_fecrep,3,2);
		$ld_fecha=$this->fun->uf_convertirdatetobd($ls_fecrep);
	    require_once("../../shared/class_folder/sigesp_c_seguridad.php");
	    $this->io_seguridad= new sigesp_c_seguridad();
		if($ls_tipodestino=='P')
		{
			$ls_destino=" Proveedor " ;
			$ls_codproben=$ls_codpro;
		}
		else
		{
			$ls_destino=" Beneficiario ";
			$ls_codproben=$ls_cedben;
		}
		$la_provbene=$this->uf_select_dt_sujret($ls_tipodestino,$ls_codproben,$ls_codemp);
		$arrResultado="";
		$ls_nrocomp="";
		$arrResultado=$this->uf_ccr_get_nro($ls_perfiscal,$ls_nrocomp,$ls_codret);
		$ls_nrocomp=$arrResultado["ls_nrocomp"];
		$ls_sql="INSERT INTO scb_cmp_ret_op(codemp, codret, numcom, fecrep, perfiscal, codsujret,
											nomsujret, dirsujret, rif, nit, estcmpret, codusu, numlic,origen)
				 VALUES('".$ls_codemp."','".$ls_codret."','".$ls_nrocomp."','".$ld_fecha."','".$ls_perfiscal."','".$ls_codproben."',
				 		'".$la_provbene["nomproben"]."','".$la_provbene["dirproben"]."','".$la_provbene["rifproben"]."','".$la_provbene["nitproben"]."',
						1,'".$_SESSION["la_logusr"]."','','A')";
		$li_result=$this->SQL->execute($ls_sql);
		if(($li_result===false))
		{
			$this->is_msg_error="Fallo insercion de movimiento detalle de orden de pago, ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido= false;
		}
		else
		{
			$this->is_msg_error="El movimiento fue registrado";
			///////////////////////////////////Parametros de seguridad/////////////////////////////////////////////////
			$ls_evento="INSERT";
			$ls_descripcion="Genero el Comprobante de Retencion ".$ls_tipocomp." para la Orden de Pago Directa numero ".$ls_numdoc." para el ".$ls_destino.$la_provbene["nomproben"]." de codigo ".$ls_codproben;
			$lb_valido = $this->io_seguridad->uf_sss_insert_eventos_ventana($ls_codemp,'SCB',"INSERT",$_SESSION["la_logusr"],"sigesp_scb_p_opd_causapaga.php",$ls_descripcion);
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$lb_valido= true;
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["ls_nrocomp"]=$ls_nrocomp;
		return $arrResultado;
	}

	function uf_ccr_get_nro($ls_periodofiscal,$ls_nrocomp,$ls_retid)
	{
		$ls_codemp=$this->dat_emp["codemp"];
		$ls_sql="SELECT substr(numcom,7,14) as numcom
				 FROM   scb_cmp_ret_op
				 WHERE  codemp='".$ls_codemp."' AND codret = '".$ls_retid."' AND perfiscal = '".$ls_periodofiscal."'
				 ORDER  by numcom desc ";

		$rs_result=$this->SQL->select($ls_sql);
		if($rs_result===false)
		{
			$this->is_msg_error=$this->fun->uf_convertirmsg($this->SQL->message);
			 $lb_valido= false;
		}
		else
		{
			if ($row=$this->SQL->fetch_row($rs_result))
			{
			   $codigo=$row["numcom"];
			   settype($codigo,'int');                             // Asigna el tipo a la variable.
			   $codigo = $codigo + 1;                              // Le sumo uno al entero.
			   settype($codigo,'string');                          // Lo convierto a varchar nuevamente.
			   $ls_nrocomp=$ls_periodofiscal.$this->fun->uf_cerosizquierda($codigo,8);
			   $this->SQL->free_result($rs_result);
			    $lb_valido= true;
		    }
		    else
		    {
			   $codigo="1";
			   $ls_nrocomp=$ls_periodofiscal.$this->fun->uf_cerosizquierda($codigo,8);
			   $this->SQL->free_result($rs_result);
   			   $lb_valido= true;
		    }
		}
		$arrResultado["lb_valido"]= $lb_valido;
		$arrResultado["ls_nrocomp"]= $ls_nrocomp;
		return $arrResultado;
	}//fin uf_ccr_get_nro

	function uf_comprobante_retencion_mun($ls_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////
		// Funtion	    :  uf_comprobante_retencion
		//	Return	    :  la_data
		//	Descripcion :  Funcion que se encarga de retornar los datos de los comprobantes de retencion asociados al codigo dado
		//  Autor       :  Ing. Laura Cabré
		//  Fecha       :  03/10/2006
		/////////////////////////////////////////////////////////////////////////////
		$ls_codemp = $this->dat_emp["codemp"];
		$la_data=array();
		$ls_sql="SELECT c.*,d.*
				FROM scb_cmp_ret_op c, scb_dt_cmp_ret_op d
				WHERE c.codret='0000000003' AND
				c.codemp='$ls_codemp' AND c.codemp=d.codemp AND c.codret=d.codret AND d.numdoc='$ls_numcom' AND c.numcom=d.numcom";
			$rs_data=$this->SQL->select($ls_sql);
			if(($rs_data==false)&&($this->SQL->message!=""))
			{
				print $this->SQL->message;
			}
			else
			{
				if($row=$this->SQL->fetch_row($rs_data))
				{
					$la_data=$this->SQL->obtener_datos($rs_data);
				}
				$this->SQL->free_result($rs_data);
			}
			return  $la_data;
	}

function uf_load_retenciones_scb($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_tipretislr,$as_tipret)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_load_retenciones_islr_scb
//		   Access: public
//		 Argument: $as_codban = Código del Banco.
//                 $as_ctaban = Número de la Cuenta Bancaria.
//                 $as_numdoc = Número del Movimiento Bancario.
//                 $as_codope = Código de la Operación CH=Cheque,DP=Depósito,ND=Nota de Débito,NC= Nota de Crédito,RE=Retiro.
//                 $as_estmov = Estatus del Movimiento.
//	  Description: Función que extrae la sumatoria de las retenciones de impuesto sobre la renta aplicadas desde Banco.
//	   Creado Por: Ing. Néstor Falcón.
// Fecha Creación: 13/06/2008
////////////////////////////////////////////////////////////////////////////////////////////////////////

  if ($as_tipret=='IVA')
     {
	   $ls_straux = "";
	   $ls_sqlaux = " AND sigesp_deducciones.islr=0 AND sigesp_deducciones.iva=1";
	   $ls_group  = "iva";
	 }
  elseif($as_tipret=='ISLR')
     {
	   $ls_straux = " , sigesp_deducciones.tipopers";
	   $ls_sqlaux = " AND sigesp_deducciones.islr=1 AND sigesp_deducciones.iva=0";
	   $ls_group  = "islr,tipopers";
	 }

  $ld_montotislr = 0;
  $ls_sql = "SELECT COALESCE(sum(scb_movbco_scg.monto),0) as montotret $ls_straux
			   FROM scb_movbco_scg, sigesp_deducciones
			  WHERE scb_movbco_scg.codemp='".$as_codemp."'
			    AND scb_movbco_scg.codban='".$as_codban."'
			    AND scb_movbco_scg.ctaban='".$as_ctaban."'
			    AND scb_movbco_scg.numdoc='".$as_numdoc."'
			    AND scb_movbco_scg.codope='".$as_codope."'
			    AND scb_movbco_scg.estmov='".$as_estmov."'
			    AND scb_movbco_scg.debhab='H'
			    AND scb_movbco_scg.codded<>'00000' $ls_sqlaux
			    AND sigesp_deducciones.estretmun=0
			    AND sigesp_deducciones.otras=0
			    AND scb_movbco_scg.codemp=sigesp_deducciones.codemp
			    AND scb_movbco_scg.codded=sigesp_deducciones.codded
			  GROUP BY $ls_group";
  $rs_data = $this->SQL->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("CLASS->sigesp_scb_report.php;MÉTODO->uf_load_retenciones_scb;ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	   echo $this->SQL->message;
	 }
  else
     {
	   $li_numrows = $this->SQL->num_rows($rs_data);
	   if ($li_numrows>0)
	      {
		    while($row=$this->SQL->fetch_row($rs_data))
				 {
				   $ld_montotret = $row["montotret"];
				   if ($as_tipret=='ISLR')
                      {
				        $as_tipretislr = $row["tipopers"];
					  }
				 }
		  }
	 }
	 $arrResultado["ld_montotret"]=$ld_montotret;
	 $arrResultado["as_tipretislr"]=$as_tipretislr;
  return $arrResultado;
}

function uf_load_retenciones_cxp($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_tipretislr,$as_tipret)
{
///////////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_load_retenciones_cxp
//		   Access: public
//		 Argument: $as_codban = Código del Banco.
//                 $as_ctaban = Número de la Cuenta Bancaria.
//                 $as_numdoc = Número del Movimiento Bancario.
//                 $as_codope = Código de la Operación CH=Cheque,DP=Depósito,ND=Nota de Débito,NC= Nota de Crédito,RE=Retiro.
//                 $as_estmov = Estatus del Movimiento.
//	  Description: Función que extrae la sumatoria de las retenciones de IVA e ISLR aplicadas desde Cuentas Por Pagar, dependiendo
//                 del tipo de retención indicada por el parámetro $as_tipret.
//	   Creado Por: Ing. Néstor Falcón.
// Fecha Creación: 16/06/2008
////////////////////////////////////////////////////////////////////////////////////////////////////////

  $ld_montotret = 0;
  if ($as_tipret=='IVA')
     {
	   $ls_straux = "";
	   $ls_sqlaux = " AND sigesp_deducciones.islr=0 AND sigesp_deducciones.iva=1";
	   $ls_group  = "iva";
	 }
  elseif($as_tipret=='ISLR')
     {
	   $ls_straux = " , sigesp_deducciones.tipopers";
	   $ls_sqlaux = " AND sigesp_deducciones.islr=1 AND sigesp_deducciones.iva=0";
	   $ls_group  = "islr,tipopers";
	 }
  $ls_sql = "SELECT COALESCE(sum(cxp_rd_deducciones.monret),0) as montotret $ls_straux
			   FROM scb_movbco, cxp_sol_banco, cxp_dt_solicitudes, cxp_solicitudes, cxp_rd_deducciones, cxp_rd, sigesp_deducciones
			  WHERE scb_movbco.codemp='".$as_codemp."'
			    AND scb_movbco.codban='".$as_codban."'
			    AND scb_movbco.ctaban='".$as_ctaban."'
			    AND scb_movbco.numdoc='".$as_numdoc."'
			    AND scb_movbco.estmov='".$as_estmov."'
			    AND scb_movbco.codope='".$as_codope."' $ls_sqlaux
			    AND sigesp_deducciones.estretmun=0
			    AND sigesp_deducciones.otras=0
			    AND scb_movbco.codemp=cxp_sol_banco.codemp
			    AND scb_movbco.codban=cxp_sol_banco.codban
			    AND scb_movbco.ctaban=cxp_sol_banco.ctaban
			    AND scb_movbco.numdoc=cxp_sol_banco.numdoc
			    AND scb_movbco.codope=cxp_sol_banco.codope
			    AND scb_movbco.estmov=cxp_sol_banco.estmov
			    AND cxp_sol_banco.codemp=cxp_solicitudes.codemp
			    AND cxp_sol_banco.numsol=cxp_solicitudes.numsol
			    AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
			    AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
			    AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
			    AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
			    AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
			    AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
			    AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
			    AND cxp_dt_solicitudes.codemp=cxp_rd_deducciones.codemp
			    AND cxp_dt_solicitudes.numrecdoc=cxp_rd_deducciones.numrecdoc
			    AND cxp_dt_solicitudes.codtipdoc=cxp_rd_deducciones.codtipdoc
			    AND cxp_dt_solicitudes.ced_bene=cxp_rd_deducciones.ced_bene
			    AND cxp_dt_solicitudes.cod_pro=cxp_rd_deducciones.cod_pro
			    AND cxp_rd.codemp=cxp_rd_deducciones.codemp
			    AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc
			    AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc
			    AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
			    AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
			    AND sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
			    AND sigesp_deducciones.codded=cxp_rd_deducciones.codded
			  GROUP BY sigesp_deducciones.$ls_group";
  $rs_data = $this->SQL->select($ls_sql);
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("CLASS->sigesp_scb_report.php;MÉTODO->uf_load_retenciones_cxp;ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	   echo $this->SQL->message;
	 }
  else
     {
	   $li_numrows = $this->SQL->num_rows($rs_data);
	   if ($li_numrows>0)
	      {
		    while($row=$this->SQL->fetch_row($rs_data))
				 {
				   $ld_montotret = $row["montotret"];
				   if ($as_tipret=='ISLR')
                      {
				        $as_tipretislr = $row["tipopers"];
					  }
				 }
		  }
	 }
	 $arrResultado["ld_montotret"]=$ld_montotret;
	 $arrResultado["as_tipretislr"]=$as_tipretislr;
  return $arrResultado;
}

 function uf_buscar_cheques_vouchers($arr_documentos,$arr_fechas,$arr_operaciones,$ls_codban,$ls_ctaban)
{
	////////////////////////////////////////////////////////////////////////////////////////////////
	//	Function: uf_buscar_cheques_vouchers
	//	Arguments:  $arr_documentos // arreglo de documentos
	//				$arr_fechas // areglo de fechas
	//				$arr_operaciones // $arreglo de operaciones
	//				$ls_codban // código del banco
	//				$ls_ctaban // cuenta bancaria
	//  Description: Metodo que se encarga de retornar los cheques vouchers filtrados segun los param,etros
	//				 de busqaueda enviados
	//  Creado por: Ing. María Beatriz Unda
	// Fecha Creación: 08/08/2008
	///////////////////////////////////////////////////////////////////////////////////////////////

	$ls_codemp=$this->dat_emp["codemp"];
	$lb_valido = true;
	$ls_aux="";
	$this->ds_documentos->reset_ds();
	$li_totdoc=count((array)$arr_documentos);
	$li_totfec=count((array)$arr_fechas);
	for($li_i=0;$li_i<$li_totdoc;$li_i++)
	{
		$ld_fecmov=$this->fun->uf_convertirdatetobd($arr_fechas[$li_i]);
		$ls_numdoc=$arr_documentos[$li_i];
		$ls_codope=$arr_operaciones[$li_i];


		 $ls_sql="SELECT *
				 FROM scb_movbco
				 WHERE codemp='".$ls_codemp."' AND numdoc='".$ls_numdoc."'
				 AND codban='".$ls_codban."' AND ctaban='".$ls_ctaban."'
				 AND fecmov='".$ld_fecmov."'
				 AND estmov<>'A' AND estmov<>'O' AND codope='".$ls_codope."'";


		$rs_data = $this->SQL->select($ls_sql);
		if ($rs_data===false)
		   {
		     $lb_valido=false;
		   }
		else
		   {
		     $lb_valido=true;
			 while($row=$this->SQL->fetch_row($rs_data))
				  {
				   	$this->ds_voucher1->insertRow("numdoc",$row["numdoc"]);
				    $this->ds_voucher1->insertRow("fecmov",$arr_fechas[$li_i]);
				    $this->ds_voucher1->insertRow("chevau",$row["chevau"]);
				  }
			  $this->SQL->free_result($rs_data);
		   }
		}
		return $rs_data;
}// fin function uf_buscar_cheques_vouchers

function uf_load_beneficiario_alterno($as_codban,$as_ctaban,$as_numdoc,$as_codope)
{
////////////////////////////////////////////////////////////////////////////////////////////////
//	     Function: uf_load_beneficiario_alterno
//	    Arguments: $as_codban = Código del Banco.
//                 $as_ctaban = Cuenta Bancaria.
//                 $as_numdoc = Número del Documento.
//                 $as_codope = Código de la Operación.
//                 $as_estmov = Estatus del Movimiento Bancario.
//    Description: Metodo que se encarga de retornar el nombre del Beneficiario alterno para aquellas sep
//                 que manejen la ayuda económica.
//     Creado por: Ing. Néstor Falcón.
// Fecha Creación: 26/08/2008
///////////////////////////////////////////////////////////////////////////////////////////////

  $ls_nombenalt = "";
  $ls_sql = "SELECT DISTINCT LTRIM(sep_solicitud.nombenalt) as nombenalt
			   FROM sep_solicitud, sep_tiposolicitud, scb_movbco, scb_movbco_spg, cxp_solicitudes,
			        cxp_dt_solicitudes, cxp_rd, cxp_rd_spg
			  WHERE scb_movbco.codemp = '".$this->dat_emp["codemp"]."'
			    AND scb_movbco.codban = '".$as_codban."'
			    AND scb_movbco.ctaban = '".$as_ctaban."'
			    AND scb_movbco.numdoc = '".$as_numdoc."'
			    AND scb_movbco.codope = '".$as_codope."'
			    AND scb_movbco_spg.procede_doc = 'CXPSOP'
			    AND cxp_rd_spg.procede_doc = 'SEPSPC'
			    AND sep_tiposolicitud.estope = 'O'
			    AND sep_tiposolicitud.modsep = 'O'
			    AND sep_tiposolicitud.estayueco = 'A'
			    AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol
			    AND scb_movbco.codemp=scb_movbco_spg.codemp
			    AND scb_movbco.codban=scb_movbco_spg.codban
			    AND scb_movbco.ctaban=scb_movbco_spg.ctaban
			    AND scb_movbco.numdoc=scb_movbco_spg.numdoc
				AND scb_movbco.codope=scb_movbco_spg.codope
			    AND scb_movbco.estmov=scb_movbco_spg.estmov
			    AND cxp_solicitudes.codemp=scb_movbco_spg.codemp
			    AND cxp_solicitudes.numsol=scb_movbco_spg.documento
			    AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
			    AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
			    AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
			    AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
			    AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
			    AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
			    AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
			    AND cxp_rd_spg.codemp=cxp_rd.codemp
			    AND cxp_rd_spg.numrecdoc=cxp_rd.numrecdoc
			    AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc
			    AND cxp_rd_spg.ced_bene=cxp_rd.ced_bene
			    AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro
			    AND cxp_rd_spg.codemp=sep_solicitud.codemp
			    AND cxp_rd_spg.ced_bene=sep_solicitud.ced_bene
			    AND cxp_rd_spg.cod_pro=sep_solicitud.cod_pro
			    AND cxp_rd_spg.numdoccom=sep_solicitud.numsol
			    AND cxp_rd.codemp=sep_solicitud.codemp
			    AND cxp_rd.ced_bene=sep_solicitud.ced_bene
			    AND cxp_rd.cod_pro=sep_solicitud.cod_pro";

  $rs_data = $this->SQL->select($ls_sql);//echo $ls_sql.'<br>';
  if ($rs_data===false)
     {
	   $lb_valido = false;
	   $this->io_msg->message("CLASS->sigesp_scb_report.php;MÉTODO->uf_load_beneficiario_alterno;ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	   echo $this->SQL->message;
	 }
  else
     {
	   if ($row=$this->SQL->fetch_row($rs_data))
	      {
		    $ls_nombenalt = $row["nombenalt"];
		  }
	 }
  return $ls_nombenalt;
}//End function uf_load_beneficiario_alterno.


function buscar_benef($parametros=array()){

		$ls_sql="SELECT * ".
				"  FROM rpc_beneficiario ".
				" WHERE rpc_beneficiario.codemp='".$this->ls_codemp."' ".				
				"   AND rpc_beneficiario.ced_bene='".$parametros['ced_bene']."' ";	
				 
		$this->rs_data_ben=$this->SQL->select($ls_sql);
		if($this->rs_data_ben===false)
		{
			$mensaje = "CLASE->sigesp_scb_report SNO MÉTODO->buscar_benef ERROR->".$this->SQL->message; 
			$this->io_msg->message($mensaje);			
		   	return false;
		}
		
		return $this->rs_data_ben;

}

function buscar_nombre_benef($parametros=array()){

		$ls_sql="SELECT * ".
				"  FROM rpc_beneficiario ".
				" WHERE rpc_beneficiario.codemp='".$this->ls_codemp."' ".				
				"   AND rpc_beneficiario.ced_bene='".$parametros['ced_bene']."' ";	
				 
		$this->rs_data_ben=$this->SQL->select($ls_sql);
		if($this->rs_data_ben===false)
		{
			$mensaje = "CLASE->sigesp_scb_report SNO MÉTODO->buscar_benef ERROR->".$this->SQL->message; 
			$this->io_msg->message($mensaje);			
		   	return false;
		}
		
		return $this->rs_data_ben->fields['nombene'].' '.$this->rs_data_ben->fields['apebene'];

}


function buscar_cheques_periodo($parametros=array()){

		$ls_sql="SELECT *  FROM scb_movbco
		          WHERE codemp='".$this->ls_codemp."' 
				  AND codperi='".$parametros['codperi']."' 
				  AND codper!='' ";	
				 
		$rs_cheq_pen=$this->SQL->select($ls_sql);
		if($rs_cheq_pen===false)
		{
			$mensaje = "CLASE->sigesp_scb_report SNO MÉTODO->buscar_cheques_periodo ERROR->".$this->SQL->message; 
			$this->io_msg->message($mensaje);			
		   	return false;
		}
		
		return $rs_cheq_pen;

}

function buscar_pensionado($parametros=array()){
		
			$ls_sql  = " SELECT  mb.*, titular.cedper AS cedtit, causante.cedper AS cedcau, 
								 titular.nomper AS nomtit, titular.apeper AS apetit,titular.tipnip AS tipniptit,
								 causante.nomper AS nomcau, causante.apeper AS apecau, causante.tipnip AS tipnipcau,
								 titular.tipnip AS tipniptit,causante.tipnip AS tipnipcau, 
								 nombene,apebene,titular.cedaut, titular.nomaut,titular.tipautor  							    		   			  
						  FROM scb_movbco mb
						  LEFT JOIN rpc_beneficiario b ON b.ced_bene = mb.ced_bene
						  LEFT JOIN sno_personal titular ON titular.codper = mb.codper
						  LEFT JOIN sno_personal causante ON causante.cedper = titular.cedmil						 
						  LEFT JOIN sno_tipo_pensionado tpt ON tpt.tippensionado = titular.tippensionado
						  LEFT JOIN sno_tipo_pensionado tpc ON tpc.tippensionado = causante.tippensionado					   
						  WHERE mb.numdoc='".$parametros["numdoc"]."' AND mb.ctaban='".$parametros["ctaban"]."' ";
							
						 
		$this->rs_pensinado=$this->SQL->select($ls_sql);
		if($this->rs_pensinado===false)
		{
			$mensaje = "CLASE->sigesp_scb_report SNO MÉTODO->buscar_pensionado ERROR->".$this->SQL->message; 
			$this->io_msg->message($mensaje);			
		   	return false;
		}
		
		return $this->rs_pensinado;

}


function buscar_cheque($ls_numdoc,$ls_voucher,$ls_codban,$ls_ctaban,$ls_codope){
		
		if($ls_voucher=="")
		{
		   $ls_sql="SELECT scb_movbco.*, scb_ctabanco.dencta,nomban,".
		           "       (SELECT rpc_proveedor.rifpro ".
				   "	     FROM rpc_proveedor ".
				   "		WHERE rpc_proveedor.codemp='".$this->ls_codemp."' ".
				   "		AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) as rifpro".
				   " FROM scb_movbco, scb_ctabanco, scb_banco ".
				   " WHERE scb_movbco.codemp='".$this->ls_codemp."'".
				   " AND scb_movbco.numdoc='".$ls_numdoc."' ".
				   " AND (scb_movbco.chevau='".$ls_voucher."' OR scb_movbco.chevau=' ')".
				   " AND scb_movbco.codban='".$ls_codban."' ".
				   " AND scb_movbco.ctaban='".$ls_ctaban."' ".
				   " AND scb_movbco.estmov<>'A' ".
				   " AND scb_movbco.estmov<>'O' ".
				   " AND scb_movbco.ctaban=scb_ctabanco.ctaban".
				   " AND scb_banco.codban=scb_movbco.codban".
				   " AND scb_banco.codemp=scb_movbco.codemp".
				   " AND scb_movbco.codope='".$ls_codope."'";//print $ls_sql;
		}
	    else
	    {
			$ls_sql="SELECT scb_movbco.*,scb_ctabanco.dencta,nomban,
			               (SELECT rpc_proveedor.rifpro
							   FROM rpc_proveedor
							  WHERE rpc_proveedor.codemp='".$this->ls_codemp."'
							    AND rpc_proveedor.cod_pro=scb_movbco.cod_pro) as rifpro
				     FROM scb_movbco, scb_ctabanco, scb_banco 
				    WHERE scb_movbco.codemp='".$this->ls_codemp."'
					  AND numdoc='".$ls_numdoc."'
				      AND scb_movbco.chevau='".$ls_voucher."'
					  AND scb_movbco.codban='".$ls_codban."'
					  AND scb_movbco.ctaban='".$ls_ctaban."'
				      AND scb_movbco.estmov<>'A'
					  AND scb_movbco.estmov<>'O'
					  AND scb_movbco.ctaban=scb_ctabanco.ctaban
					  AND scb_banco.codban=scb_movbco.codban
				      AND scb_banco.codemp=scb_movbco.codemp
					  AND scb_movbco.codope='".$ls_codope."'";
		}
							
						 
		$this->rs_cheque=$this->SQL->select($ls_sql);
		//echo $ls_sql.'<br>';
		if($this->rs_cheque===false)
		{
			$mensaje = "CLASE->sigesp_scb_report SNO MÉTODO->buscar_pensionado ERROR->".$this->SQL->message; 
			$this->io_msg->message($mensaje);			
		   	return false;
		}
		
		return $this->rs_cheque;

}
//------------------------------------------------------------------------------------------------------------------------------
function uf_select_factura($aa_solicitudes,$ai_cnsol)
	{
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_solicitudes="";	
		$i=0;
		
		if($ai_cnsol==1)
		{
			$ls_sql="SELECT numrecdoc
					 FROM cxp_dt_solicitudes
					 WHERE codemp='".$ls_codemp."' 
					 AND numsol='".$aa_solicitudes[0]."'";
			$rs_data=$this->SQL->select($ls_sql);
			if($rs_data===false)
			{
				$this->is_msg_error="Error en uf_select_factura, ".$this->fun->uf_convertirmsg($this->SQL->message);
				print $this->SQL->message;
			}
			else
			{
				while($row=$this->SQL->fetch_row($rs_data))
				{
					$i=$i+1;
					if($i==1)
					{
						$ls_solicitudes=$row["numrecdoc"];
					}
					else
					{
						$ls_solicitudes=$ls_solicitudes."-".$row["numrecdoc"];
					}
				}
				$this->SQL->free_result($rs_data);
			}
			return $ls_solicitudes;
		}
		if ($ai_cnsol > 1)
		{
			for($a=0;$a<=$ai_cnsol;$a++)
			{
				$ls_sql="SELECT numrecdoc
					 FROM cxp_dt_solicitudes
					 WHERE codemp='".$ls_codemp."' 
					 AND numsol='".$aa_solicitudes[$a]."'";
	
					$rs_data=$this->SQL->select($ls_sql);
					if($rs_data===false)
					{
						$this->is_msg_error="Error en uf_select_factura, ".$this->fun->uf_convertirmsg($this->SQL->message);
						print $this->SQL->message;
					}
					else
					{
						while($row=$this->SQL->fetch_row($rs_data))
						{
							$i=$i+1;
							if($i==1)
							{
								$ls_solicitudes=$row["numrecdoc"];
							}
							else
							{
								$ls_solicitudes=$ls_solicitudes."- ".$row["numrecdoc"];
							}
						}
						$this->SQL->free_result($rs_data);
					}
			}
		return $ls_solicitudes;
		}
		
	}
//------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_seleccionado()
	{
	    /*----------------------------------------------------------------
		Function: uf_select_cartaorden
		Descripción: funcion que retorna true si existe la carta orden
		Autor: Ing. Laura Cabré
		Fecha: 19/12/2006
		------------------------------------------------------------------*/
		$ls_codemp = $this->dat_emp["codemp"];
		$ls_sql="SELECT codigo
				FROM scb_cartaorden 
				where codemp='$ls_codemp' and status=1";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data==false)
		{
			print "Error".$this->io_sql->message;
			return false;			
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{								
				
				return $row["codigo"];		
			}
			else
			{
				return false;
			}			
			$this->SQL->free_result($rs_data);
		}
	}

}
?>
