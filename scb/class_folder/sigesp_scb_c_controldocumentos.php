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

class sigesp_scb_c_controldocumentos {
	
	public function __construct($as_path)
	{
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
	  	require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");
	  	require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
      	require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
	  	require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
	  	require_once($as_path."base/librerias/php/general/sigesp_lib_datastore.php");
      	require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
	  	  
	  	$io_include 		  = new sigesp_include();
	  	$ls_connect         = $io_include->uf_conectar();
	  	$this->io_sql       = new class_sql($ls_connect);	
	  	$this->io_function  = new class_funciones();
	  	$this->io_msg       = new class_mensajes();
	  	$this->io_fecha     = new class_fecha();
	  	$this->ls_codemp	= $_SESSION["la_empresa"]["codemp"];
	  	$this->ls_codusu    = $_SESSION["la_logusr"];
	  	$this->io_seguridad = new sigesp_c_seguridad();
	}
	
	public function uf_load_documentos($as_codban,$as_ctaban,$as_fecdes,$as_fechas,$as_estatus){
		$ls_filtro = "";
		$as_fecdes = $this->io_function->uf_convertirdatetobd($as_fecdes);
		$as_fechas = $this->io_function->uf_convertirdatetobd($as_fechas);
	  
	 	if (!empty($as_fecdes) && !empty($as_fechas)){
	    	$ls_filtro = $ls_filtro." AND M.fecmov BETWEEN '".$as_fecdes."' AND '".$as_fechas."'";
	  	}
	  
		if (!empty($as_codban)){
			$ls_filtro = $ls_filtro." AND M.codban='".$as_codban."' ";
		}
		else{
			$ls_codbanco = "  ";
		}
		
		if (!empty($as_codban)){
			if (empty($as_ctaban)){
	            $ls_filtro = $ls_filtro."  ";
	        }
	        else{
	            $ls_filtro = $ls_filtro." AND M.ctaban='".$as_ctaban."'";
	        }
		}
		else{
			$ls_filtro = $ls_filtro."  ";
		}
		
		if(!empty($as_estatus)){
			$ls_filtro = $ls_filtro." AND M.estcondoc='".$as_estatus."'";
		}
		else{
			$ls_filtro = $ls_filtro."  ";	
		}
	
		$ls_sql  = "SELECT M.codemp,M.codban,M.ctaban,M.numdoc,M.codope,M.estmov,M.nomproben as nombre,(M.monto - M.monret) as monto,M.fecmov
					FROM 	scb_movbco M
					WHERE 	M.codemp='".$this->ls_codemp."' AND(M.procede = 'SCBBCH' or M.procede = 'SCBCOR') AND 
							((M.codope='CH' OR M.codope='ND') AND (M.codope<>'DP' AND M.codope<>'OP')) AND M.estmov <>'A' AND M.estmov <>'O'
				   ".$ls_filtro."
					ORDER BY M.fecmov,M.numdoc ";
		return $this->io_sql->select($ls_sql); 
	}
	
	public function uf_actualizar_estcondoc($as_codemp,$as_codban,$as_ctaban,$as_numdoc,$as_codope,$as_estmov,$as_estatus,$aa_seguridad){
		$lb_valido = true;
		$ls_actfec = "";
		switch ($as_estatus){
    		case "F";
    			$ls_actfec = ", fecenvfir='".date('Y-m-d')."',fecenvcaj='1900-01-01'";
    			break;
    		case "C";
    			$ls_actfec = ", fecenvcaj='".date('Y-m-d')."'";
    			break;
    		case "S";
    			$ls_actfec = ", fecenvfir='1900-01-01',fecenvcaj='1900-01-01'";
    			break;
    	}
		
    	$ls_sql="UPDATE scb_movbco ".
		        "   SET estcondoc='".$as_estatus."' ".$ls_actfec.
				" WHERE codemp='".$as_codemp."' ".
				"	AND codban='".$as_codban."' ".
				"   AND ctaban='".$as_ctaban."' ".
				"   AND numdoc='".$as_numdoc."' ".
				"   AND codope='".$as_codope."' ".
				"   AND estmov='".$as_estmov."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
            $this->io_msg->message("CLASE->Control documentos SCB MÉTODO->uf_actualizar_estcondoc ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		else{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			switch ($as_estatus){
				case "S":
					$ls_descripcion="Coloco en estado emitido el Documento";
					break;
				case "F":
					$ls_descripcion="Coloco en estado enviado a la firma el Documento";
					break;
				case "C":
					$ls_descripcion="Coloco en estado enviado a caja el Documento";
					break;
			}
			
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
//			$ls_descripcion=$ls_descripcion."<b>".$as_numdoc."</b>, Banco <b>".$as_codban."</b>, ".
//							"Cuenta Banco <b>".$as_ctaban."</b>";
//			$lb_valido1= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
//											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
//											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		return $lb_valido;
	}
	
	public function uf_buscar_documentos($as_codproben,$as_tipoproben,$as_fecdes,$as_fechas,$as_estcondoc,$as_estsol,$as_codban='',$as_cuenta='', $as_orden=''){
		$ls_filtroA="";
		$ls_filtroB="";
		$ls_filbanco  = '';
		$ls_filcuenta = '';
		$ls_orden = '';
		$as_fecdes = $this->io_function->uf_convertirdatetobd($as_fecdes);
		$as_fechas = $this->io_function->uf_convertirdatetobd($as_fechas);
		
		if($as_orden != '') {
			if ($as_orden == 'F') {
				$ls_orden = ' ORDER BY 2, 3 DESC';
			}
			else if ($as_orden == 'D'){
				$ls_orden = ' ORDER BY 1, 3 DESC';
			}
		}
		else {
			$ls_orden = 'ORDER BY 3 DESC';
		}
		
		if ($as_codban != '') {
			$ls_filbanco = " AND codban = '{$as_codban}'";
		}
		
		if ($as_cuenta != '') {
			$ls_filcuenta = " AND ctaban = '{$as_cuenta}'";
		}
		
		switch ($_SESSION["ls_gestor"]){
			case "MYSQLT":
				$ls_cadenaA="CONCAT(rpc_beneficiario.ced_bene,' - ',rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				$ls_cadenaB="CONCAT(rpc_proveedor.cod_pro,' - ',rpc_proveedor.nompro)";
				break;
			case "MYSQLI":
				$ls_cadenaA="CONCAT(rpc_beneficiario.ced_bene,' - ',rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				$ls_cadenaB="CONCAT(rpc_proveedor.cod_pro,' - ',rpc_proveedor.nompro)";
				break;
			case "POSTGRES":
				$ls_cadenaA="rpc_beneficiario.ced_bene||' - '||rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				$ls_cadenaB="rpc_proveedor.cod_pro||' - '||rpc_proveedor.nompro";
				break;
			case "INFORMIX":
				$ls_cadenaA="rpc_beneficiario.ced_bene||' - '||rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				$ls_cadenaB="rpc_proveedor.cod_pro||' - '||rpc_proveedor.nompro";
				break;
			case "oci8po":
				$ls_cadenaA="rpc_beneficiario.ced_bene||' - '||rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				$ls_cadenaB="rpc_proveedor.cod_pro||' - '||rpc_proveedor.nompro";
				break;
		}
		
		if($as_tipoproben=='P'){
			$ls_filtroA=" AND tipproben='P' AND sol.cod_pro='".$as_codproben."'";
			$ls_filtroB=" AND tipo_destino='P' AND M.cod_pro='".$as_codproben."'";
		}
		else if($as_tipoproben=='B'){
			$ls_filtroA=" AND tipproben='B' AND sol.ced_bene='".$as_codproben."'";
			$ls_filtroB=" AND tipo_destino='B' AND M.ced_bene='".$as_codproben."'";
		}
		else if($as_tipoproben=='N'){
			$ls_filtroA=" AND sol.cod_pro='".$as_codproben."' AND sol.ced_bene='".$as_codproben."'";
			$ls_filtroB=" AND M.cod_pro='".$as_codproben."' AND M.ced_bene='".$as_codproben."'";
		}
		
		if ($as_estcondoc!='T') {
			$ls_filtroB.=" AND M.estcondoc='".$as_estcondoc."'";
		}
		
		if ($as_estsol==0) {
			$ls_sql  = "SELECT 	M.numdoc as numero,M.fecmov AS fecha,CASE M.codope WHEN 'CH' THEN 'CH' ELSE 'CO' END AS tipodoc,
						(M.monto - M.monret) as monto,M.estcondoc as estado,M.fecenvfir,M.fecenvcaj,
						(CASE WHEN tipo_destino='B' 
								THEN (SELECT ".$ls_cadenaA."
					                  FROM rpc_beneficiario
					                  WHERE M.ced_bene=rpc_beneficiario.ced_bene
					                  group by rpc_beneficiario.ced_bene,  rpc_beneficiario.nombene, rpc_beneficiario.apebene)
		                      WHEN tipo_destino='P' 
								THEN (SELECT ".$ls_cadenaB."
					                  FROM rpc_proveedor
					                  WHERE M.cod_pro=rpc_proveedor.cod_pro
					                  group by rpc_proveedor.cod_pro, rpc_proveedor.nompro) 
					          ELSE 'NINGUNO' END) AS nombre
						FROM 	scb_movbco M
						WHERE 	M.codemp='0001' AND(M.procede = 'SCBBCH' OR M.procede = 'SCBCOR') AND M.estmov <> 'A' AND M.estmov <> 'O' AND
						((M.codope='CH' OR M.codope='ND') AND (M.codope<>'DP' AND M.codope<>'OP')) AND M.fecmov between '".$as_fecdes."' AND '".$as_fechas."'".
						$ls_filtroB." {$ls_filbanco} {$ls_filcuenta}
						{$ls_orden}";
		}
		else {
			$ls_sql  = "SELECT sol.numsol as numero,sol.fecemisol as fecha,'SP' as tipodoc,
						sol.monsol as monto,coalesce(pro.estmov,'X') AS estado,'1900-01-01' as fecenvfir,'1900-01-01' as fecenvcaj,
						(CASE WHEN tipproben='B' 
								THEN (SELECT ".$ls_cadenaA."
					                  FROM rpc_beneficiario
					                  WHERE sol.ced_bene=rpc_beneficiario.ced_bene
					                  group by rpc_beneficiario.ced_bene,  rpc_beneficiario.nombene, rpc_beneficiario.apebene)
		                      WHEN tipproben='P' 
								THEN (SELECT ".$ls_cadenaB."
					                  FROM rpc_proveedor
					                  WHERE sol.cod_pro=rpc_proveedor.cod_pro
					                  group by rpc_proveedor.cod_pro, rpc_proveedor.nompro) 
					          ELSE 'NINGUNO' END) AS nombre 
						FROM cxp_solicitudes sol
						LEFT JOIN scb_prog_pago pro
						ON sol.numsol=pro.numsol
						WHERE sol.fecemisol between '".$as_fecdes."' AND '".$as_fechas."'".
						$ls_filtroA." {$ls_filbanco} {$ls_filcuenta}
						UNION
						SELECT 	M.numdoc as numero,M.fecmov AS fecha,CASE M.codope WHEN 'CH' THEN 'CH' ELSE 'CO' END AS tipodoc,
						(M.monto - M.monret) as monto,M.estcondoc as estado,M.fecenvfir,M.fecenvcaj,
						(CASE WHEN tipo_destino='B' 
								THEN (SELECT ".$ls_cadenaA."
					                  FROM rpc_beneficiario
					                  WHERE M.ced_bene=rpc_beneficiario.ced_bene
					                  group by rpc_beneficiario.ced_bene,  rpc_beneficiario.nombene, rpc_beneficiario.apebene)
		                      WHEN tipo_destino='P' 
								THEN (SELECT ".$ls_cadenaB."
					                  FROM rpc_proveedor
					                  WHERE M.cod_pro=rpc_proveedor.cod_pro
					                  group by rpc_proveedor.cod_pro, rpc_proveedor.nompro) 
					          ELSE 'NINGUNO' END) AS nombre
						FROM 	scb_movbco M
						WHERE 	M.codemp='0001' AND(M.procede = 'SCBBCH' OR M.procede = 'SCBCOR') AND M.estmov <> 'A' AND M.estmov <> 'O' AND
						((M.codope='CH' OR M.codope='ND') AND (M.codope<>'DP' AND M.codope<>'OP')) AND M.fecmov between '".$as_fecdes."' AND '".$as_fechas."'".
						$ls_filtroB." {$ls_filbanco} {$ls_filcuenta}
						{$ls_orden}";
		}
		//echo $ls_sql;
		return $this->io_sql->select($ls_sql); 
	}
}