<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_spg_class_ejecucionpresupuestaria
{
	private $datemp;
	private $conexion;
	private $io_sql;
	private $io_funciones_reporte;
	private $sigesp_int_spg;

	public function __construct()
	{
		require_once("../../../shared/class_folder/class_sigesp_int.php");
		require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../../../shared/class_folder/class_sigesp_int_spg.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("sigesp_spg_funciones_reportes.php");

		$in                         = new sigesp_include();
		$this->conexion             = $in->uf_conectar();
		$this->io_sql               = new class_sql($this->conexion);
		$this->sigesp_int_spg       = new class_sigesp_int_spg();
		$this->io_funciones_reporte = new sigesp_spg_funciones_reportes();
		$this->datemp               = $_SESSION["la_empresa"];
	}

	public function uf_obtener_rango_programatica($ls_CodEstPro1_desde,$ls_CodEstPro2_desde,$ls_CodEstPro3_desde,$ls_CodEstPro4_desde,$ls_CodEstPro5_desde,$as_estclades,
	$ls_CodEstPro1_hasta,$ls_CodEstPro2_hasta,$ls_CodEstPro3_hasta,$ls_CodEstPro4_hasta,$ls_CodEstPro5_hasta,$as_estclahas) {
		if((strtoupper($_SESSION["ls_gestor"])=="MYSQLT") || (strtoupper($_SESSION["ls_gestor"])=="MYSQLI"))
		{
			$ls_concat="CONCAT";
			$ls_cadena=",";
		}
		else{
			$ls_concat="";
			$ls_cadena="||";
		}
		 
		// Nivel 1
		if (($ls_CodEstPro1_desde!="") && ($ls_CodEstPro1_hasta!="")){
			$ls_str_w1  = " ".$ls_concat."(CUE.codestpro1".$ls_cadena." ";
			$ls_str_w1f = $ls_CodEstPro1_desde;
			$ls_str_w1t = $ls_CodEstPro1_hasta;
		}
		else{
			$ls_str_w1  = "";
			$ls_str_w1f = "";
			$ls_str_w1t = "";
		}
	  
		// Nivel 2
		if (($ls_CodEstPro2_desde!="") && ($ls_CodEstPro2_hasta!="")){
			if($ls_str_w1!=""){
				$ls_str_w2  = "CUE.codestpro2".$ls_cadena." ";
				$ls_str_w2f = $ls_CodEstPro2_desde;
				$ls_str_w2t = $ls_CodEstPro2_hasta;
			}
			else{
				$ls_str_w2  = "(CUE.codestpro2".$ls_cadena." ";
				$ls_str_w2f = $ls_CodEstPro2_desde;
				$ls_str_w2t = $ls_CodEstPro2_hasta;
			}
		}
		else{
			$ls_str_w2  = "";
			$ls_str_w2f = "";
			$ls_str_w2t = "";
		}

		// Nivel 3
		if (($ls_CodEstPro3_desde!="") && ($ls_CodEstPro3_hasta!="")){
			if ($ls_str_w2!="") {
				$ls_str_w3  = "CUE.codestpro3".$ls_cadena." ";
				$ls_str_w3f = $ls_CodEstPro3_desde;
				$ls_str_w3t = $ls_CodEstPro3_hasta;
			}
			else{
				$ls_str_w3  = "(CUE.codestpro3".$ls_cadena." ";
				$ls_str_w3f = $ls_CodEstPro3_desde;
				$ls_str_w3t = $ls_CodEstPro3_hasta;
			}
		}
		else{
			$ls_str_w3  = "";
			$ls_str_w3f = "";
			$ls_str_w3t = "";
		}

		if ($_SESSION["la_empresa"]["estmodest"]==2) {
			// Nivel 4
			if (($ls_CodEstPro4_desde!="") and ($ls_CodEstPro4_hasta!="")){
				if ($ls_str_w3!="") {
					$ls_str_w4  = "CUE.codestpro4".$ls_cadena." ";
					$ls_str_w4f = $ls_CodEstPro4_desde;
					$ls_str_w4t = $ls_CodEstPro4_hasta;
				}
				else{
					$ls_str_w4  = "(CUE.codestpro4".$ls_cadena." ";
					$ls_str_w4f = $ls_CodEstPro4_desde;
					$ls_str_w4t = $ls_CodEstPro4_hasta;
				}
			}
			else{
				$ls_str_w4  = "";
				$ls_str_w4f = "";
				$ls_str_w4t = "";
			}
			 
			// Nivel 5
			if (($ls_CodEstPro5_desde!="0000000000000000000000000") and ($ls_CodEstPro5_hasta!="0000000000000000000000000")){
				if ($ls_str_w4!="") {
					$ls_str_w5  = "CUE.codestpro5".$ls_cadena." ";
					$ls_str_w5f = $ls_CodEstPro5_desde;
					$ls_str_w5t = $ls_CodEstPro5_hasta;
				}
				else{
					$ls_str_w5  = "(CUE.codestpro5".$ls_cadena." ";
					$ls_str_w5f = $ls_CodEstPro5_desde;
					$ls_str_w5t = $ls_CodEstPro5_hasta;
				}
			}
			else{
				$ls_str_w5  = "";
				$ls_str_w5f = "";
				$ls_str_w5t = "";
			}
		}

		//estatus de clasificacion
		if (($as_estclades!='') and ($as_estclahas!='')){
			$ls_str_estcla  = "CUE.estcla))";
			$ls_str_estclaf = $as_estclades;
			$ls_str_estclat = $as_estclahas;
		}
		else{
			$ls_str_estcla  = "";
			$ls_str_estclaf = "";
			$ls_str_estclat = "";
		}

		if (!(empty($ls_str_w1) and empty($ls_str_w2) and empty($ls_str_w3) and empty($ls_str_w4) and empty($ls_str_w5) and empty($ls_str_estcla))){
			$ls_str_estructura = $ls_str_w1.$ls_str_w2.$ls_str_w3.$ls_str_w4.$ls_str_w5.$ls_str_estcla;
			$li_lent= strlen($ls_str_estructura)-1;
			$ls_str_estructura = substr( $ls_str_estructura ,0,$li_lent);
			$as_str_estructura_from = $ls_str_w1f.$ls_str_w2f.$ls_str_w3f.$ls_str_w4f.$ls_str_w5f.$ls_str_estclaf;
			$as_str_estructura_to = $ls_str_w1t.$ls_str_w2t.$ls_str_w3t.$ls_str_w4t.$ls_str_w5t.$ls_str_estclat;
			$as_Sql_Where=$ls_str_estructura." between '".$as_str_estructura_from."' AND '".$as_str_estructura_to."' ";
		}
		else{
			$as_Sql_Where="";
			$as_str_estructura_to="";
			$as_str_estructura_from="";
		}
		 
		return $as_Sql_Where;
	}

	public function uf_obtener_cuentas($ls_CodEstPro1_desde, $ls_CodEstPro2_desde, $ls_CodEstPro3_desde, $ls_CodEstPro4_desde, $ls_CodEstPro5_desde, $as_estclades,
	$ls_CodEstPro1_hasta, $ls_CodEstPro2_hasta, $ls_CodEstPro3_hasta, $ls_CodEstPro4_hasta, $ls_CodEstPro5_hasta, $as_estclahas,
	$ai_nivel,$as_cuentades,$as_cuentahas,$as_codfuefindes,$as_codfuefinhas) {
		$ls_seguridad="";
		$ls_seguridad=$this->io_funciones_reporte->uf_filtro_seguridad_programatica('CUE',$ls_seguridad);
		$ls_programatica = $this->uf_obtener_rango_programatica($ls_CodEstPro1_desde, $ls_CodEstPro2_desde, $ls_CodEstPro3_desde, $ls_CodEstPro4_desde, $ls_CodEstPro5_desde, $as_estclades,
		$ls_CodEstPro1_hasta, $ls_CodEstPro2_hasta, $ls_CodEstPro3_hasta, $ls_CodEstPro4_hasta, $ls_CodEstPro5_hasta, $as_estclahas);

		if($_SESSION["la_empresa"]["estmodest"]==1){
			$ls_descestpro    = ',denestpro3 as descestpro';
			$ls_cadena_fuefin = "INNER JOIN spg_ep3 ON CUE.codemp=spg_ep3.codemp AND CUE.codestpro1=spg_ep3.codestpro1 AND CUE.codestpro2=spg_ep3.codestpro2 AND CUE.codestpro3=spg_ep3.codestpro3 AND CUE.estcla=spg_ep3.estcla";
		}
		elseif($_SESSION["la_empresa"]["estmodest"]==2){
			$ls_descestpro    = ',denestpro5 as descestpro';
			$ls_cadena_fuefin="INNER JOIN spg_ep5 ON CUE.codemp=spg_ep5.codemp AND CUE.codestpro1=spg_ep5.codestpro1 AND CUE.codestpro2=spg_ep5.codestpro2 AND CUE.codestpro3=spg_ep5.codestpro3 AND CUE.codestpro4=spg_ep5.codestpro4 AND CUE.codestpro5=spg_ep5.codestpro5 AND CUE.estcla=spg_ep5.estcla";
		}
			
		$cadenaSql = "SELECT CUE.spg_cuenta, CUE.denominacion,CUE.codestpro1, CUE.codestpro2, CUE.codestpro3, CUE.codestpro4,
       						 CUE.codestpro5, CUE.estcla".$ls_descestpro."  
  						FROM spg_cuentas CUE ".$ls_cadena_fuefin."
						WHERE CUE.codemp = '".$this->datemp['codemp']."' AND ".$ls_programatica." AND 
                              CUE.spg_cuenta BETWEEN '".$as_cuentades."' AND '".$as_cuentahas."' AND
                              CUE.nivel <= ".$ai_nivel." AND
                              codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".$ls_seguridad."
  						ORDER BY CUE.spg_cuenta, CUE.codestpro1, CUE.codestpro2, CUE.codestpro3, 
           						 CUE.codestpro4, CUE.codestpro5, CUE.estcla";

		return $this->io_sql->select($cadenaSql);
	}

	public function uf_obtener_ejecucion_cuenta($as_spg_cuenta, $as_codestpro1, $as_codestpro2, $as_codestpro3,
	$as_codestpro4, $as_codestpro5, $as_estcla, $adt_fecfin) {
		$arrtotales = array();
		$as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";

		$ls_sql=	" SELECT MV.spg_cuenta,  ".
                  	" CASE MV.operacion  ".
					" WHEN 'AAP' THEN sum(MV.monto) ".
					" END as asignar, ".
					" CASE MV.operacion ".
					" WHEN 'AU' THEN sum(MV.monto) ".
					" END as aumento, ".
					" CASE MV.operacion  ".
					" WHEN 'CCP' THEN sum(MV.monto) ".
					" WHEN 'CG' THEN sum(MV.monto) ".
					" WHEN 'CS' THEN sum(MV.monto) ".
					" END as compromiso,".
					" CASE MV.operacion".
					" WHEN 'DI' THEN sum(MV.monto) ".
					" END as disminucion, ".
					" CASE MV.operacion ".
					" WHEN 'GC' THEN sum(MV.monto) ".
					" WHEN 'CCP' THEN sum(MV.monto) ".
					" WHEN 'CP' THEN sum(MV.monto) ".
					" WHEN 'CG' THEN sum(MV.monto) ".
					" END as causado, ".
					" CASE MV.operacion ".
					" WHEN 'PC' THEN sum(MV.monto) ".
					" END as precompromiso, ".
					" CASE MV.operacion ".
					" WHEN 'CCP' THEN sum(MV.monto) ".
					" WHEN 'CP' THEN sum(MV.monto) ".
					" WHEN 'PG' THEN sum(MV.monto) ".
					" END as pagado ".
                  	" FROM spg_dt_cmp as MV ".
					" WHERE MV.spg_cuenta LIKE '{$as_spg_cuenta}' AND ".
					"       MV.fecha <= '{$adt_fecfin}' AND ".
                    "       MV.codestpro1='{$as_codestpro1}' AND MV.codestpro2='{$as_codestpro2}' AND ".
                    "       MV.codestpro3='{$as_codestpro3}' AND MV.codestpro4='{$as_codestpro4}' AND ".
                    "       MV.codestpro5='{$as_codestpro5}' AND MV.estcla='{$as_estcla}' ".
					" GROUP BY MV.spg_cuenta, MV.operacion".
                  	" ORDER BY MV.spg_cuenta ";

		$data_ejecucion = $this->io_sql->select($ls_sql);

		$ld_asignado        = 0;
		$ld_aumento         = 0;
		$ld_disminucion     = 0;
		$ld_precomprometido = 0;
		$ld_comprometido    = 0;
		$ld_causado         = 0;
		$ld_pagado          = 0;
		while (!$data_ejecucion->EOF) {
			$ld_asignado         = $ld_asignado + $data_ejecucion->fields['asignar'];
			$ld_aumento          = $ld_aumento  + $data_ejecucion->fields['aumento'];
			$ld_disminucion      = $ld_disminucion  + $data_ejecucion->fields['disminucion'];
			$ld_precomprometido  = $ld_precomprometido  + $data_ejecucion->fields['precompromiso'];
			$ld_comprometido     = $ld_comprometido  + $data_ejecucion->fields['compromiso'];
			$ld_causado          = $ld_causado  + $data_ejecucion->fields['causado'];
			$ld_pagado           = $ld_pagado  + $data_ejecucion->fields['pagado'];
				
				
			$data_ejecucion->MoveNext();
		}

		$arrtotales['montoactualizado'] = ($ld_asignado + $ld_aumento) - $ld_disminucion;
		$arrtotales['precomprometido']  = $ld_precomprometido;
		$arrtotales['comprometido']     = $ld_comprometido;
		if($ld_precomprometido>0){
			$arrtotales['porcentcomp']  = ($ld_comprometido*100)/$ld_precomprometido;
		}
		else{
			if((($ld_asignado + $ld_aumento) - $ld_disminucion)>0){
				$arrtotales['porcentcomp']  = ($ld_comprometido*100)/(($ld_asignado + $ld_aumento) - $ld_disminucion);
			}
			else{
				$arrtotales['porcentcomp']  = 0;
			}
		}
		$arrtotales['causado']          = $ld_causado;
		if($ld_comprometido>0){
			$arrtotales['porcentcaus']  = ($ld_causado*100)/$ld_comprometido;
		}
		else{
			$arrtotales['porcentcaus']  = 0;
		}
		$arrtotales['pagado']           = $ld_pagado;
		if($ld_causado>0){
			$arrtotales['porcentpaga']  = ($ld_pagado*100)/$ld_causado;
		}
		else{
			$arrtotales['porcentpaga']  = 0;
		}


		unset($data_ejecucion);
		return $arrtotales;
	}


}