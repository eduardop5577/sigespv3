<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start();
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "window.close();";
		print "</script>";		
	}
	$ls_logusr=$_SESSION["la_logusr"];
	require_once("class_folder/class_funciones_nomina.php");
	$io_fun_nomina=new class_funciones_nomina();
	$ls_permisos="";
	$la_seguridad = Array();
	$la_permisos = Array();	
	$arrResultado=$io_fun_nomina->uf_load_seguridad("SNR","sigesp_snorh_p_configuracion.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos=$arrResultado['as_permisos'];
	$la_seguridad=$arrResultado['aa_seguridad'];
	$la_permisos=$arrResultado['aa_permisos'];
	//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////

   //--------------------------------------------------------------
   function uf_select_campos()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_campos
		//		   Access: private
		//	  Description: Funci?n que selecciona todos los campos de configuraci?n
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/04/2006 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
   		global $io_sno,$io_fun_nomina,$li_vac_reportar,$ls_vac_codconvac,$la_vac_metban,$ls_vac_metban,$li_vac_desincorporar,$li_salvacper,$li_presalvacper;
		global $ls_est_codconcsuec,$la_est_estnom,$ls_est_estnom,$la_est_ordcons,$ls_est_ordcons,$la_est_ordconc,$ls_est_ordconc;
		global $la_est_estrec,$ls_est_estrec,$li_est_numlin,$la_est_prilpt,$ls_est_prilpt,$la_est_agrsem,$ls_est_agrsem,$li_con_parnom,$li_estctaalt;
		global $la_con_consue,$ls_con_consue,$ls_con_cuecon,$la_con_conapo,$ls_con_conapo,$la_con_conpro,$ls_con_conpro, $li_con_agrcon;
		global $li_con_gennotdeb,$li_con_genvou,$la_con_descon,$ls_con_descon,$li_par_excpersus,$li_par_perrep,$ld_par_fecfinano;
		global $la_par_metcalfid,$ls_par_metcalfid,$ls_fpj_codorgfpj,$ls_fpj_codconcfpj,$la_fpj_metfpj,$ls_fpj_metfpj,$ls_lph_codconclph;
		global $la_lph_metlph,$ls_lph_metlph,$ls_fpa_codconcfpa,$la_fpa_metfpa,$ls_fpa_metfpa,$li_fps_antcom,$li_fps_fraali;
		global $la_fps_metfps,$ls_fps_metfps,$ls_man_cueconc,$ls_man_cueconccaj,$li_man_actblofor,$li_man_actblocalnom;
		global $la_man_metrescon,$ls_man_metrescon,$la_dis_metdisnom,$ls_dis_metdisnom,$li_con_genrecdoc,$li_con_genrecdocapo;
		global $ls_con_tipdocnom,$ls_con_tipdocapo,$ls_ipas_codorgipas,$ls_ipas_codconcahoipas,$ls_ipas_codconcseripas;  
		global $ls_ipas_conhipespipas,$ls_ipas_conhipampipas,$ls_ipas_conhipconipas,$ls_ipas_conhiphipipas,$ls_ipas_conhiplphipas;
		global $ls_ipas_conhipvivipas,$ls_ipas_conperipas,$ls_ipas_conturipas,$ls_ipas_conproipas,$ls_ipas_conasiipas;
		global $ls_ipas_convehipas,$ls_ipas_concomipas,$ls_ivss_numemp,$li_vac_desincorporar,$ls_par_concsuelant;
		global $la_par_confpre,$ls_par_confpre,$li_par_camuniadm,$li_par_campasogrado,$li_par_incperben,$ls_par_cueconben,$li_par_codunirac,$li_par_camdedtipper;
		global $li_par_comautrac,$li_par_ajusuerac, $li_par_modpensiones,$li_par_loncueban, $li_par_valloncueban, $li_par_valporpre;
		global $ls_con_confidnom,$la_con_confidnom,$ls_con_recdocfid,$ls_con_recdocguar,$ls_con_tipdocfid,$ls_con_tipdocguar,$ls_con_cueconfid,$ls_con_codbenfid,$ls_con_codguarcontper,$ls_con_codguarcontobr;
		global $ls_ivss_metodo,$la_ivss_metodo, $li_par_alfnumcodper, $li_con_parfpj, $ls_edadM, $ls_edadF, $ls_anoM, $ls_anoT,$ls_con_codguarcontpercon,$ls_con_codguarcontobrcon;
		global $li_prestamo, $li_par_campsuerac, $li_fps_intasiextra,$ls_sueint,$li_persobregiro,$li_genrecdocpagperche,$ls_tipdocpagperche,$ls_readonly;
		global $li_fps_incvacagui,$ls_codban,$ls_cuenta_banco,$li_chkgenpagperche,$ls_codbanperche,$ls_cuenta_bancoperche, $ls_codconcbanavih;
		global $ls_codorgvipladin,$ls_grupovipladin,$ls_codubivipladin,$ls_distritovipladin,$ls_municipiovipladin, $ls_vigenciavipladin,$li_ivss_nomesp;
		global $li_fps_calintpreant,$li_fps_pormaxant,$ls_fps_tipdocant, $li_fps_calintpercon,$li_fps_acuintdiaadi,$li_fps_presocdiaadi, $li_fps_diasadicionalesBV, $li_fps_calperact; /// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente.
		global $ls_con_benrecdocguar,$li_difconpnom,$la_depsalnorqui,$ls_depsalnorqui,$la_depsalnoradi,$ls_depsalnoradi,$la_depsalnorvac,$ls_depsalnorvac;
		global $ls_fps_metcalalibonvac, $la_fps_metcalalibonvac,$li_fps_antprimeranio, $la_fps_forcalpres, $ls_fps_forcalpres,$ls_estagrapo,$ls_ivss_dirtalhumivss,$ls_ivss_ceddirtalhumivss;
		global $li_con_recdoccaunom, $ls_con_tipdoccaunom, $ls_codconcsalbasfpj, $ls_codconcantfpj, $ls_codconcefifpj, $ls_codconcotrprifpj, $li_percobmoncer, $li_chkmancueban;
	    global $li_chkpercargoalfa, $li_fps_uniestpre, $ls_fps_codestpro1, $ls_fps_estcla, $ls_fps_codestpro2, $ls_fps_codestpro3, $ls_fps_codestpro4, $ls_fps_codestpro5;
		
		
		//-------------------------------------SUELDO INTEGRAL--------------------------------------------------
		$ls_sueint=trim($io_sno->uf_select_config("SNO","NOMINA","DENOMINACION SUELDO INTEGRAL","-","I"));		
		if ($ls_sueint!="-")
		{
			$ls_readonly="readonly";
		}
		else
		{
			$ls_readonly="";
		}
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------SALARIO NORMAL--------------------------------------------------
		$la_depsalnorqui[0]="";
		$la_depsalnorqui[1]="";
		$la_depsalnorqui[2]="";
        $la_depsalnorqui[3]="";
		$ls_depsalnorqui=trim($io_sno->uf_select_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO QUINCENA","0","C"));
		$la_depsalnorqui=$io_fun_nomina->uf_seleccionarcombo("0-1-2-3",$ls_depsalnorqui,$la_depsalnorqui,4);
		$la_depsalnoradi[0]="";
		$la_depsalnoradi[1]="";
		$la_depsalnoradi[2]="";
        $la_depsalnoradi[3]="";
		$ls_depsalnoradi=trim($io_sno->uf_select_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO ADICIONAL","0","C"));
		$la_depsalnoradi=$io_fun_nomina->uf_seleccionarcombo("0-1-2-3",$ls_depsalnoradi,$la_depsalnoradi,4);
		$la_depsalnorvac[0]="";
		$la_depsalnorvac[1]="";
		$la_depsalnorvac[2]="";
        $la_depsalnorvac[3]="";
		$ls_depsalnorvac=trim($io_sno->uf_select_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO VACACION","0","C"));
		$la_depsalnorvac=$io_fun_nomina->uf_seleccionarcombo("0-1-2-3",$ls_depsalnorvac,$la_depsalnorvac,4);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------VACACIONES------------------------------------------------------
		$li_vac_reportar=trim($io_sno->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$li_salvacper=trim($io_sno->uf_select_config("SNO","NOMINA","SALIDA VACACION","0","C"));
		$li_presalvacper=trim($io_sno->uf_select_config("SNO","NOMINA","PRESTAMO SALIDA VACACION","0","C"));
		$ls_vac_codconvac=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","-","C"));
		$li_vac_desincorporar=trim($io_sno->uf_select_config("SNO","NOMINA","DESINCORPORAR DE NOMINA","0","C"));
		$la_vac_metban[0]="";
		$la_vac_metban[1]="";
		$la_vac_metban[2]="";
		$la_vac_metban[3]="";
		$la_vac_metban[4]="";
		$ls_vac_metban=trim($io_sno->uf_select_config("SNO","CONFIG","METODO_VACACIONES","0","C"));
		$la_vac_metban=$io_fun_nomina->uf_seleccionarcombo("0-1-2-3-4",$ls_vac_metban,$la_vac_metban,5);
		//-----------------------------------------------------------------------------------------------------

		//--------------------------------------ESTILO DE N?MINA-----------------------------------------------
		$ls_est_codconcsuec=trim($io_sno->uf_select_config("SNO","NOMINA","SNO COD SUELDO","0000SUELDO","C"));
		$la_est_estnom[0]="";
		$la_est_estnom[1]="";
		$la_est_estnom[2]="";
		$ls_est_estnom=trim($io_sno->uf_select_config("SNO","NOMINA","REP NOMINA","NORMAL","C"));
		$la_est_estnom=$io_fun_nomina->uf_seleccionarcombo("NORMAL-CNU-SEAM",$ls_est_estnom,$la_est_estnom,3);
		$la_est_ordcons[0]="";
		$la_est_ordcons[1]="";
		$la_est_ordcons[2]="";
		$la_est_ordcons[3]="";
		$ls_est_ordcons=trim($io_sno->uf_select_config("SNO","CONFIG","ORDEN CONSTANTE","CODIGO","C"));
		$la_est_ordcons=$io_fun_nomina->uf_seleccionarcombo("CODIGO-NOMBRE-APELLIDO-UNIDAD",$ls_est_ordcons,$la_est_ordcons,3);
		$la_est_ordconc[0]="";
		$la_est_ordconc[1]="";
		$la_est_ordconc[2]="";
		$la_est_ordconc[3]="";
		$ls_est_ordconc=trim($io_sno->uf_select_config("SNO","CONFIG","ORDEN CONCEPTO","CODIGO","C"));
		$la_est_ordconc=$io_fun_nomina->uf_seleccionarcombo("CODIGO-NOMBRE-APELLIDO-UNIDAD",$ls_est_ordconc,$la_est_ordconc,3);
		$la_est_estrec[0]="";
		$la_est_estrec[1]="";
		$la_est_estrec[2]="";
		$ls_est_estrec=trim($io_sno->uf_select_config("SNO","NOMINA","REP RECIBOS","NORMAL","C"));
		$la_est_estrec=$io_fun_nomina->uf_seleccionarcombo("NORMAL-CNU-SEAM",$ls_est_estrec,$la_est_estrec,3);
		$li_est_numlin=trim($io_sno->uf_select_config("SNO","NOMINA","REP RECIBO LINEAS","-","C"));
		$la_est_prilpt[0]="";
		$la_est_prilpt[1]="";
		$ls_est_prilpt=trim($io_sno->uf_select_config("SNO","PRINT","RECIBOS","WINDOWS","C"));
		$la_est_prilpt=$io_fun_nomina->uf_seleccionarcombo("WINDOWS-GOBERNACION PORTUGUESA",$ls_est_prilpt,$la_est_prilpt,2);
		$la_est_agrsem[0]="";
		$la_est_agrsem[1]="";
		$la_est_agrsem[2]="";
		$ls_est_agrsem=trim($io_sno->uf_select_config("SNO","NOMINA","NOM_SEM_SR","2","C"));
		$la_est_agrsem=$io_fun_nomina->uf_seleccionarcombo("1-2-3",$ls_est_agrsem,$la_est_agrsem,3);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------CONTABILIZACI?N-------------------------------------------------
		$li_con_parnom=trim($io_sno->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		$la_con_consue[0]="";
		$la_con_consue[1]="";
		$la_con_consue[2]="";
		$la_con_consue[3]="";
		$ls_con_consue=trim($io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
		$la_con_consue=$io_fun_nomina->uf_seleccionarcombo("CP-OCP-OC-O",$ls_con_consue,$la_con_consue,4);
		$ls_con_cuecon=trim($io_sno->uf_select_config("SNO","CONFIG","CTA.CONTA","XXXXXXXXXXXXX","C"));
		$la_con_conapo[0]="";
		$la_con_conapo[1]="";
		$la_con_conapo[2]="";
		$la_con_conapo[3]="";
		$ls_con_conapo=trim($io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C"));
		$la_con_conapo=$io_fun_nomina->uf_seleccionarcombo("CP-OCP-OC-O",$ls_con_conapo,$la_con_conapo,4);
		$la_con_conpro[0]="";
		$la_con_conpro[1]="";
		$ls_con_conpro=trim($io_sno->uf_select_config("SNO","SPG","CONTABILIZACION","UBICACION ADMINISTRATIVA","C"));
		$la_con_conpro=$io_fun_nomina->uf_seleccionarcombo("UBICACION ADMINISTRATIVA-CONCEPTOS",$ls_con_conpro,$la_con_conpro,2);
		$li_con_agrcon=trim($io_sno->uf_select_config("SNO","NOMINA","AGRUPARCONTA","0","I"));
		$li_con_gennotdeb=trim($io_sno->uf_select_config("SNO","CONFIG","GENERAR NOTA DEBITO","1","I"));
		$li_con_genvou=trim($io_sno->uf_select_config("SNO","CONFIG","VOUCHER GENERAR","1","I"));
		$li_con_genrecdoc=trim($io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"));
		
		$li_con_recdoccaunom=trim($io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO CAUSA","0","I"));
		$ls_con_tipdoccaunom=trim($io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO CAUSADO","-","C"));
		
		$ls_estagrapo=trim($io_sno->uf_select_config("SNO","CONFIG","AGRUPAR APORTES","0","I"));
		$li_con_genrecdocapo=trim($io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"));
		$ls_con_tipdocnom=trim($io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO NOMINA","-","C"));
		$ls_con_tipdocapo=trim($io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO APORTE","-","C"));
		$la_con_descon[0]="";
		$la_con_descon[1]="";
		$la_con_descon[2]="";
		$ls_con_descon=trim($io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION DESTINO","-","C"));
		switch (substr($ls_con_descon,0,1))
		{
			case "P":
				$ls_con_descon=substr($ls_con_descon,1,strlen($ls_con_descon)-1);
				$ls_destino="P";
				break;
				
			case "B":
				$ls_con_descon=substr($ls_con_descon,1,strlen($ls_con_descon)-1);
				$ls_destino="B";
				break;
				
			default:
				$ls_con_descon=substr($ls_con_descon,1,strlen($ls_con_descon)-1);
				$ls_destino=" ";
		}
		$la_con_descon=$io_fun_nomina->uf_seleccionarcombo(" -P-B",$ls_destino,$la_con_descon,3);
		$ls_con_confidnom=$io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION FIDEICOMISO","OC","C");
		$la_con_confidnom[0]="";		
		$la_con_confidnom[1]="";		
		$la_con_confidnom=$io_fun_nomina->uf_seleccionarcombo("OC-OCP",$ls_con_confidnom,$la_con_confidnom,2);
		$ls_con_recdocfid=$io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO FIDEICOMISO","0" ,"I");			
		$ls_con_tipdocfid=$io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO FIDEICOMISO","-","C");			
		$ls_con_recdocguar=$io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO GUARDERIA","0" ,"I");
		$ls_con_benrecdocguar=$io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO BENEFICIARIO GUARDERIA","0","I");
		$ls_con_tipdocguar=$io_sno->uf_select_config("SNO","CONFIG","GUARDERIA","-","C");
		$ls_con_cueconfid=trim($io_sno->uf_select_config("SNO","CONFIG","CTA.CONTABLE_FIDEICOMISO","XXXXXXXXXXXXX","C"));
		$ls_con_codbenfid=trim($io_sno->uf_select_config("SNO","NOMINA","DESTINO FIDEICOMISO","----------","C"));
		$ls_con_codguarcontper=trim($io_sno->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA PERSONAL","----------","C"));
		$ls_con_codguarcontobr=trim($io_sno->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA OBRERO","----------","C"));
		$ls_con_codguarcontpercon=trim($io_sno->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA PERSONAL CONTRATADO","----------","C"));
		$ls_con_codguarcontobrcon=trim($io_sno->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA OBRERO CONTRATADO","----------","C"));
		$li_genrecdocpagperche=trim($io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO PAGO PERSONAL CHEQUE","0","I"));
		$ls_tipdocpagperche=trim($io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO PAGO PERSONAL CHEQUE","-","C"));	
		$li_estctaalt=trim($io_sno->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));		
		$ls_codban=trim($io_sno->uf_select_config("SNO","NOMINA","BANCO BANAVIH 2.0","---","C"));//Carlos Zambrano
		$ls_cuenta_banco=trim($io_sno->uf_select_config("SNO","NOMINA","CTA. BANAVIH 2.0","-------------------------","C"));//Carlos Zambrano
		$ls_codbanperche=trim($io_sno->uf_select_config("SNO","NOMINA","BANCO PERSONAL CHEQUE","---","C"));//Carlos Zambrano
		$ls_cuenta_bancoperche=trim($io_sno->uf_select_config("SNO","NOMINA","CTA. PERSONAL CHEQUE","-------------------------","C"));//Carlos Zambrano
		$ls_codconcbanavih=trim($io_sno->uf_select_config("SNO","NOMINA","CONCEPTOS_BANAVIH","-","C"));		
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------PAR?METROS------------------------------------------------------
		$li_par_excpersus=trim($io_sno->uf_select_config("SNO","CONFIG","EXCLUIR_SUSPENDIDOS","0","I"));
		$li_par_perrep=trim($io_sno->uf_select_config("SNO","CONFIG","NOPERMITIR_REPETIDOS","1","I"));
		$ld_par_fecfinano=trim($io_sno->uf_select_config("SNO","ANTIGUEDAD","FECHA_TOPE","-","C"));
		$la_par_metcalfid[0]="";
		$la_par_metcalfid[1]="";
		$ls_par_metcalfid=trim($io_sno->uf_select_config("SNO","CONFIG","METODO FIDECOMISO","VERSION 2","C"));
		$la_par_metcalfid=$io_fun_nomina->uf_seleccionarcombo("VERSION 2-VERSION CONSEJO",$ls_par_metcalfid,$la_par_metcalfid,2);
		$ls_par_concsuelant=trim($io_sno->uf_select_config("SNO","CONFIG","CONCEPTO_SUELDO_ANT","XXXXXXXXXX","C"));
		$la_par_confpre[0]="";
		$la_par_confpre[1]="";
		$ls_par_confpre=trim($io_sno->uf_select_config("SNO","CONFIG","CONFIGURACION_PRESTAMO","CUOTAS","C"));
		$la_par_confpre=$io_fun_nomina->uf_seleccionarcombo("CUOTAS-MONTO",$ls_par_confpre,$la_par_confpre,2);
		$li_par_camuniadm=trim($io_sno->uf_select_config("SNO","CONFIG","CAMBIAR_UNIDAD_ADM_RAC","0","I"));
		$li_par_campasogrado=trim($io_sno->uf_select_config("SNO","CONFIG","CAMBIAR_PASO_GRADO_RAC","0","I"));
		$li_par_incperben=trim($io_sno->uf_select_config("SNO","CONFIG","INCLUIR_A_BENEFICIARIO","0","I"));
		$ls_par_cueconben=trim($io_sno->uf_select_config("SNO","CONFIG","CUENTA_CONTABLE_BENEFICIARIO","-","C"));
		$li_par_codunirac=trim($io_sno->uf_select_config("SNO","CONFIG","CODIGO_UNICO_RAC","0","I"));
		$li_par_comautrac=trim($io_sno->uf_select_config("SNO","CONFIG","COMPENSACION_AUTOMATICA_RAC","1","I"));
		$li_par_ajusuerac=trim($io_sno->uf_select_config("SNO","CONFIG","AJUSTAR_SUELDO_RAC","0","I"));
		$li_par_modpensiones=trim($io_sno->uf_select_config("SNO","CONFIG","CAMBIAR_PENSIONES","0","I"));
		$li_par_loncueban=trim($io_sno->uf_select_config("SNO","CONFIG","LONGITUD_CUENTA_BANCO","0","I"));
		$li_par_valloncueban=trim($io_sno->uf_select_config("SNO","CONFIG","VALIDAR_LONGITUD_CUEBANCO","0","I"));
		$li_par_valporpre=trim($io_sno->uf_select_config("SNO","CONFIG","VAL_PORCENTAJE_PRESTAMO","1","I"));
		$li_par_alfnumcodper=trim($io_sno->uf_select_config("SNO","CONFIG","ALFNUM_CODPER","0","I"));
		$li_prestamo=trim($io_sno->uf_select_config("SNO","CONFIG","VAL_TIPO_PRESTAMO","0","I"));
		$li_par_campsuerac=trim($io_sno->uf_select_config("SNO","CONFIG","CAMBIAR_SUELDO_RAC","0","I"));
		$li_par_camdedtipper=trim($io_sno->uf_select_config("SNO","CONFIG","CAMBIAR_DEDICACION_TIPO_PERSONAL_RAC","0","I"));
		$li_persobregiro=trim($io_sno->uf_select_config("SNO","CONFIG","SOBREGIRO_CUENTAS_TRABAJADOR","0","I"));		
		$li_chkgenpagperche=trim($io_sno->uf_select_config("SNO","CONFIG","PAGO_DIRECTO_PERSONAL_CHEQUE","0","I"));
		$li_percobmoncer=trim($io_sno->uf_select_config("SNO","CONFIG","TRABAJADOR_MONTO_CERO","0","I"));
        $li_chkmancueban=trim($io_sno->uf_select_config("SNO","CONFIG","MANTENER_CUENTA_BANCO","0","I"));
	    $li_chkpercargoalfa=trim($io_sno->uf_select_config("SNO","CONFIG","PERMITIR_CARGO_ALFANUMERICO","0","I"));
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes FPJ-----------------------------------------------------
		$ls_fpj_codorgfpj=trim($io_sno->uf_select_config("SNO","NOMINA","COD ORGANISMO FPJ","XXXXXXXX","C"));
		$ls_fpj_codconcfpj=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO FPJ","XXXXXXXX","C"));
		$la_fpj_metfpj[0]="";
		$la_fpj_metfpj[1]="";
		$ls_fpj_metfpj=trim($io_sno->uf_select_config("SNO","CONFIG","METODO FPJ","SUELDO NORMAL","C"));
		$la_fpj_metfpj=$io_fun_nomina->uf_seleccionarcombo("SUELDO NORMAL-SUELDO INTEGRAL",$ls_fpj_metfpj,$la_fpj_metfpj,2);
		$li_con_parfpj=trim($io_sno->uf_select_config("SNO","CONFIG","CONF JUB","0","I"));
		$ls_edadM=trim($io_sno->uf_select_config("SNO","NOMINA","EDADM","0","C"));
		$ls_edadF=trim($io_sno->uf_select_config("SNO","NOMINA","EDADF","0","C"));
		$ls_anoM=trim($io_sno->uf_select_config("SNO","NOMINA","ANOM","0","C"));
		$ls_anoT=trim($io_sno->uf_select_config("SNO","NOMINA","ANOT","0","C"));
		$ls_codconcsalbasfpj=trim($io_sno->uf_select_config("SNO","CONFIG","SALARIO_BASE_FPJ","","C"));
		$ls_codconcantfpj=trim($io_sno->uf_select_config("SNO","CONFIG","ANTIGUEDAD_FPJ","","C"));
		$ls_codconcefifpj=trim($io_sno->uf_select_config("SNO","CONFIG","EFICIENCIA_FPJ","","C"));
		$ls_codconcotrprifpj=trim($io_sno->uf_select_config("SNO","CONFIG","OTRAS_PRIMAS_FPJ","","C"));
		
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes LPH-----------------------------------------------------
		$ls_lph_codconclph=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO LPH","XXXXXXXXXX","C"));
		$la_lph_metlph[0]="";
		$la_lph_metlph[1]="";
		$la_lph_metlph[2]="";
		$la_lph_metlph[3]="";
		$la_lph_metlph[4]="";
		$la_lph_metlph[5]="";
		$la_lph_metlph[6]="";
		$la_lph_metlph[7]="";
		$la_lph_metlph[8]="";
		$la_lph_metlph[9]="";
		$la_lph_metlph[10]="";
		$la_lph_metlph[11]="";
		$la_lph_metlph[12]="";
		$la_lph_metlph[13]="";
		$la_lph_metlph[14]="";
		$la_lph_metlph[15]="";
		$la_lph_metlph[16]="";
		$la_lph_metlph[17]="";
		$la_lph_metlph[18]="";
        $la_lph_metlph[19]="";
		$ls_lph_metlph=trim($io_sno->uf_select_config("SNO","NOMINA","METODO LPH","SIN METODO","C"));
		$la_lph_metlph=$io_fun_nomina->uf_seleccionarcombo("SIN METODO-VIVIENDA-CASA PROPIA-MERENAP-MIRANDA-FONDO MUTUAL HABITACIONAL-BANESCO-MI CASA EAP-CANARIAS-VENEZUELA-DELSUR-MERCANTIL-CENTRAL-CAJA FAMILIA-FONDO_COMUN_EAP-BOD-BANAVIH-BANAVIH2-BANAVIHSUNACRIP-BANAVIH_FISCALIZACION",
								    $ls_lph_metlph,$la_lph_metlph,19);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes FPA-----------------------------------------------------
		$ls_fpa_codconcfpa=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO FPA","XXXXXXXXXX","C"));
		$la_fpa_metfpa[0]="";
		$la_fpa_metfpa[1]="";
		$la_fpa_metfpa[2]="";
		$la_fpa_metfpa[3]="";
		$la_fpa_metfpa[4]="";
		$la_fpa_metfpa[5]="";
		$la_fpa_metfpa[6]="";
		$ls_fpa_metfpa=trim($io_sno->uf_select_config("SNO","NOMINA","METODO FPA","SIN METODO","C"));
		$la_fpa_metfpa=$io_fun_nomina->uf_seleccionarcombo("SIN METODO-VENEZUELA-MERCANTIL-CENTRAL-FONACIT-CAPREMINFRA-GENERICO",$ls_fpa_metfpa,$la_fpa_metfpa,6);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes FPS-----------------------------------------------------
		$li_difconpnom=trim($io_sno->uf_select_config("SNO","NOMINA","DIFERENCIAR CONCEPTOS NOMINA","0","I"));
		$li_fps_antcom=trim($io_sno->uf_select_config("SNO","NOMINA","COMPLEMENTO ANTIGUEDAD","0","I"));
		$li_fps_fraali=trim($io_sno->uf_select_config("SNO","NOMINA","FRACCION ALICUOTA","0","I"));
		$li_fps_intasiextra=trim($io_sno->uf_select_config("SNO","NOMINA","INT_ASIG_EXTRA","0","I"));
		$li_fps_incvacagui=trim($io_sno->uf_select_config("SNO","NOMINA","INC_VACACIONES_AGUINALDO","0","I"));
		$li_fps_calintpreant=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULO_INT_FIDEICOISO","0","I"));
		$li_fps_calintpercon=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULO_INT_PERSONAL_CONF","0","I"));
		$li_fps_acuintdiaadi=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULO_ACUM_INT_DIAS_ADICIONALES","0","I"));
		$li_fps_presocdiaadi=trim($io_sno->uf_select_config("SNO","NOMINA","REGIMEN_PREST_SOCIALES_97","0","I"));
		$li_fps_antprimeranio=trim($io_sno->uf_select_config("SNO","NOMINA","ANTIGUEDAD_PRIMER_A?O","0","I"));
	    $li_fps_uniestpre=trim($io_sno->uf_select_config("SNO","NOMINA","UNIFICAR_ESTRUCTURA_PRESUPUESTARIA","0","I"));
		$ls_fps_codestpro1=trim($io_sno->uf_select_config("SNO","NOMINA","FPS_CODESTPRO1","","C"));
		$ls_fps_estcla=trim($io_sno->uf_select_config("SNO","NOMINA","FPS_ESTCLA","","C"));
		$ls_fps_codestpro2=trim($io_sno->uf_select_config("SNO","NOMINA","FPS_CODESTPRO2","","C"));
		$ls_fps_codestpro3=trim($io_sno->uf_select_config("SNO","NOMINA","FPS_CODESTPRO3","","C"));
		$ls_fps_codestpro4=trim($io_sno->uf_select_config("SNO","NOMINA","FPS_CODESTPRO4","","C"));
		$ls_fps_codestpro5=trim($io_sno->uf_select_config("SNO","NOMINA","FPS_CODESTPRO5","","C"));

		/// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente.
		$li_fps_diasadicionalesBV=trim($io_sno->uf_select_config("SNO","NOMINA","DIAS_ADICIONALES_BV","0","I"));
		/// fin de lo agregado.
		$li_fps_pormaxant=trim($io_sno->uf_select_config("SNO","NOMINA","POR_MAX_ANTICIPO","0","I"));
		$ls_fps_tipdocant=trim($io_sno->uf_select_config("SNO","NOMINA","TIPO_DOC_ANTICIPO","-","C"));
		$li_fps_calperact=trim($io_sno->uf_select_config("SNO","NOMINA","CALCULAR_PERSONAL_ACTIVO","0","I"));
		$la_fps_metfps[0]="";
		$la_fps_metfps[1]="";
		$la_fps_metfps[2]="";
		$la_fps_metfps[3]="";
		$la_fps_metfps[4]="";
		$la_fps_metfps[5]="";
		$la_fps_metfps[6]="";
		$la_fps_metfps[7]="";
		$la_fps_metfps[8]="";	
		$la_fps_metfps[9]="";		
		$la_fps_metfps[10]="";	
		$la_fps_metfps[11]="";
		$la_fps_metfps[12]="";	
		$la_fps_metfps[13]="";	
		$la_fps_metfps[14]="";	
		$la_fps_metfps[15]="";	
		$la_fps_metfps[16]="";
		$la_fps_metfps[17]="";
		$ls_fps_metfps=trim($io_sno->uf_select_config("SNO","CONFIG","METODO FPS","SIN METODO","C"));
		$la_fps_metfps=$io_fun_nomina->uf_seleccionarcombo("SIN METODO-CARIBE-UNION-MERCANTIL-VENEZOLANO DE CREDITO-BANCO DE VENEZUELA-VENEZUELA-BANCO PROVINCIAL-BANESCO-CENTRAL BANCO UNIVERSAL-DEL SUR-BANCO INDUSTRIAL-CASA PROPIA-BANCO DEL TESORO-BANCO AGRICOLA VENEZUELA-BANCO EXTERIOR-BANCO NACIONAL DE CREDITO-BOD",$ls_fps_metfps,$la_fps_metfps,17);

		$la_fps_metcalalibonvac[0]="";
		$la_fps_metcalalibonvac[1]="";
		$la_fps_metcalalibonvac[2]="";
		$ls_fps_metcalalibonvac=trim($io_sno->uf_select_config("SNO","CONFIG","MET ALI BONO VAC","INTEGRAL","C"));
		$la_fps_metcalalibonvac=$io_fun_nomina->uf_seleccionarcombo("INTEGRAL-NORMAL-VACACION",$ls_fps_metcalalibonvac,$la_fps_metcalalibonvac,2);
		
		$la_fps_forcalpres[0]="";
		$la_fps_forcalpres[1]="";
		$la_fps_forcalpres[2]="";
		$ls_fps_forcalpres=trim($io_sno->uf_select_config("SNO","NOMINA","FORMA_CALCULO_PRES","0","C"));
		$la_fps_forcalpres=$io_fun_nomina->uf_seleccionarcombo("0-1-2",$ls_fps_forcalpres,$la_fps_forcalpres,3);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Mantenimiento---------------------------------------------------
		$ls_man_cueconc=trim($io_sno->uf_select_config("SNO","NOMINA","SPGCUENTA","401","C"));
		$ls_man_cueconccaj=trim($io_sno->uf_select_config("SNO","NOMINA","CTACAJA","0","C"));
		$li_man_actblofor=trim($io_sno->uf_select_config("SNO","CONFIG","ACTIVAR_BLOQUEO","0","I"));
		$li_man_actblocalnom=trim($io_sno->uf_select_config("SNO","CONFIG","BLOQUEO_ACTIVAR","0","I"));
		$la_man_metrescon[0]="";
		$la_man_metrescon[1]="";
		$ls_man_metrescon=trim($io_sno->uf_select_config("SNO","CONFIG","METODO RESUMEN CONTABLE","SIN METODO","C"));
		$la_man_metrescon=$io_fun_nomina->uf_seleccionarcombo("SIN METODO-METODO CTA_ABONO",$ls_man_metrescon,$la_man_metrescon,2);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Disco N?mina----------------------------------------------------
		$la_dis_metdisnom[0]="";
		$la_dis_metdisnom[1]="";
		$la_dis_metdisnom[2]="";
		$la_dis_metdisnom[3]="";
		$la_dis_metdisnom[4]="";
		$ls_dis_metdisnom=trim($io_sno->uf_select_config("SNO","CONFIG","METODO GD NOMINA","SIN METODO","C"));
		$la_dis_metdisnom=$io_fun_nomina->uf_seleccionarcombo("SIN METODO-Excel-Cultura Excel-Metodo #2-Excel #2",$ls_dis_metdisnom,$la_dis_metdisnom,5);
		//-----------------------------------------------------------------------------------------------------

		//-------------------------------------Aportes IPASME-----------------------------------------------------
		$ls_ipas_codorgipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD ORGANISMO IPAS","XXX","C"));
		$ls_ipas_codconcahoipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO AHORRO IPAS","XXXXXXXXXX","C"));
		$ls_ipas_codconcseripas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO SERVICIO IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conhipespipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO ESPECIAL IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conhipampipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO AMLIACION IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conhipconipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO CONSTRUCCION IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conhiphipipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO HIPOTECA IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conhiplphipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO LPH IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conhipvivipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO VIVIENDA IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conperipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO PERSONAL IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conturipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO TURISTICOS IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conproipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO PROVEEDURIA IPAS","XXXXXXXXXX","C"));
		$ls_ipas_conasiipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO ASISTENCIALES IPAS","XXXXXXXXXX","C"));
		$ls_ipas_convehipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO VEHICULOS IPAS","XXXXXXXXXX","C"));
		$ls_ipas_concomipas=trim($io_sno->uf_select_config("SNO","NOMINA","COD CONCEPTO COMERCIALES IPAS","XXXXXXXXXX","C"));		
		//-----------------------------------------------------------------------------------------------------
		
		//-------------------------------------IVSS-----------------------------------------------------
		$ls_ivss_numemp=trim($io_sno->uf_select_config("SNO","NOMINA","COD ORGANISMO IVSS","XXXXXXXXX","C"));
		$ls_ivss_dirtalhumivss=trim($io_sno->uf_select_config("SNO","NOMINA","DIR_TALENTO_HUMANO","","C"));
		$ls_ivss_ceddirtalhumivss=trim($io_sno->uf_select_config("SNO","NOMINA","CED_DIR_TALENTO_HUMANO","","C"));
		$li_ivss_nomesp=trim($io_sno->uf_select_config("SNO","NOMINA","NOMINAS_ESPECIALES_IVSS","0","C"));			
		if ($ls_ivss_numemp=="XXXXXXXXX")
		{
			$ls_ivss_numemp=$io_sno->uf_numero_IVSS();					
		}		
		$la_ivss_metodo[0]="";
		$la_ivss_metodo[1]="";
		$ls_ivss_metodo=trim($io_sno->uf_select_config("SNO","CONFIG","METODO IVSS","SUELDO NORMAL","C"));
		$la_ivss_metodo=$io_fun_nomina->uf_seleccionarcombo("SUELDO NORMAL-SUELDO INTEGRAL",$ls_ivss_metodo,$la_ivss_metodo,2);
		//-----------------------------------------------------------------------------------------------------
		
		//-------------------------------------VIPLADIN--------------------------------------------------------
		$ls_codorgvipladin=trim($io_sno->uf_select_config("SNO","CONFIG","COD_ORGANISMO_VIPLADIN","-","C"));
		$ls_grupovipladin=trim($io_sno->uf_select_config("SNO","CONFIG","GRUPO_VIPLADIN","-","C"));
		$ls_codubivipladin=trim($io_sno->uf_select_config("SNO","CONFIG","COD_UBICACION_VIPLADIN","-","C"));
		$ls_distritovipladin=trim($io_sno->uf_select_config("SNO","CONFIG","DISTRITO_VIPLADIN","-","C"));
		$ls_municipiovipladin=trim($io_sno->uf_select_config("SNO","CONFIG","MUNICIPIO_VIPLADIN","-","C"));
		$ls_vigenciavipladin=trim($io_sno->uf_select_config("SNO","CONFIG","VIGENCIA_VIPLADIN","-","C"));
		//-----------------------------------------------------------------------------------------------------
   }// end function uf_select_campos
   //--------------------------------------------------------------

   //--------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Funci?n que obtiene el valor de los campos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 05/04/2006 								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////
		global $li_vac_reportar,$ls_vac_codconvac,$ls_vac_metvac,$li_salvacper,$li_presalvacper,$ls_est_codconcsue,$ls_est_estnom,$ls_est_ordcons,$ls_est_ordconc;
		global $ls_est_estrec,$li_est_numlin,$ls_est_prilpt,$ls_est_agrsem,$li_con_parnom,$ls_con_consue,$ls_con_cuecon,$ls_con_conapo;
		global $ls_con_conpro,$li_con_agrcon,$li_con_gennotdeb,$li_con_genvou,$ls_con_descon,$li_par_excpersus,$li_par_perrep;
		global $ld_par_fecfinano,$ls_par_metcalfid,$ls_fpj_codorgfpj,$ls_fpj_codconcfpj,$ls_fpj_metfpj,$ls_lph_codconclph,$ls_lph_metlph;
		global $ls_fpa_codconcfpa,$ls_fpa_metfpa,$li_fps_antcom,$li_fps_fraali,$ls_fps_metfps,$ls_man_cueconc,$ls_man_cueconccaj;
		global $li_man_actblofor,$li_man_actblocalnom,$ls_man_metrescon,$ls_dis_metdisnom,$li_con_genrecdoc,$li_con_genrecdocapo;
		global $ls_con_tipdocnom,$ls_con_tipdocapo,$io_fun_nomina,$ls_ipas_codorgipas,$ls_ipas_codconcahoipas,$ls_ipas_codconcseripas;
		global $ls_ipas_conhipespipas,$ls_ipas_conhipampipas,$ls_ipas_conhipconipas,$ls_ipas_conhiphipipas,$ls_ipas_conhiplphipas;
		global $ls_ipas_conhipvivipas,$ls_ipas_conperipas,$ls_ipas_conturipas,$ls_ipas_conproipas,$ls_ipas_conasiipas;
		global $ls_ipas_convehipas,$ls_ipas_concomipas,$ls_ivss_numemp,$li_vac_desincorporar,$ls_par_concsuelant,$ls_par_confpre;
		global $li_par_camuniadm,$li_par_campasogrado,$li_par_incperben,$ls_par_cueconben,$li_par_codunirac,$li_par_comautrac,$li_par_ajusuerac,$li_par_camdedtipper;
		global $li_par_loncueban, $li_par_valloncueban,$li_par_modpensiones,$li_par_valporpre;
		global $ls_con_confidnom,$ls_con_recdocfid,$ls_con_recdocguar,$ls_con_tipdocfid,$ls_con_tipdocguar,$ls_con_cueconfid,$ls_con_codbenfid,$ls_con_codguarcontper,$ls_con_codguarcontobr,$ls_ivss_metodo, $li_par_alfnumcodper;
		global $li_con_parfpj, $ls_edadM, $ls_edadF, $ls_anoM, $ls_anoT, $li_prestamo, $li_par_campsuerac,$ls_con_codguarcontpercon,$ls_con_codguarcontobrcon;
		global $li_fps_intasiextra,$ls_sueint,$li_persobregiro, $li_genrecdocpagperche,$ls_tipdocpagperche,$li_estctaalt,$li_ivss_nomesp,$li_percobmoncer;
		global $li_fps_incvacagui,$ls_codban,$ls_cuenta_banco,$li_chkgenpagperche,$ls_codbanperche,$ls_cuenta_bancoperche,$ls_codconcbanavih;
		global $ls_codorgvipladin,$ls_grupovipladin,$ls_codubivipladin,$ls_distritovipladin,$ls_municipiovipladin,$ls_vigenciavipladin; 
		global $li_fps_calintpreant,$li_fps_pormaxant,$ls_fps_tipdocant,$li_fps_calintpercon,$li_fps_acuintdiaadi,$li_fps_presocdiaadi, $li_fps_diasadicionalesBV, $li_fps_calperact; /// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente.
		global $ls_con_benrecdocguar,$li_difconpnom,$ls_depsalnorqui,$ls_depsalnoradi,$ls_depsalnorvac,$ls_fps_metcalalibonvac,$li_fps_antprimeranio,$ls_fps_forcalpres,$ls_estagrapo;
		global $ls_ivss_dirtalhumivss,$ls_ivss_ceddirtalhumivss,$li_con_recdoccaunom, $ls_con_tipdoccaunom, $ls_codconcsalbasfpj,$ls_codconcantfpj,$ls_codconcefifpj,$ls_codconcotrprifpj, $li_chkmancueban;
	    global $li_chkpercargoalfa, $li_fps_uniestpre, $ls_fps_codestpro1, $ls_fps_estcla, $ls_fps_codestpro2, $ls_fps_codestpro3, $ls_fps_codestpro4, $ls_fps_codestpro5;

		$li_vac_reportar=$io_fun_nomina->uf_obtenervalor("chkvacreportar","0");
		$li_salvacper=$io_fun_nomina->uf_obtenervalor("chksalvacper","0");	
		$li_presalvacper=$io_fun_nomina->uf_obtenervalor("chkpresalvacper","0");	
		$li_vac_desincorporar=$io_fun_nomina->uf_obtenervalor("chkvacdesincorporar","0");	
		$ls_vac_codconvac=$io_fun_nomina->uf_obtenervalor("txtcodconvac","-");	
		$ls_vac_metvac=$io_fun_nomina->uf_obtenervalor("cmbmetvac","-");	
		$ls_est_codconcsue=$io_fun_nomina->uf_obtenervalor("txtcodconcsue","0000SUELDO");	
		$ls_est_estnom=$io_fun_nomina->uf_obtenervalor("cmbestnom","NORMAL");	
		$ls_est_ordcons=$io_fun_nomina->uf_obtenervalor("cmbordcons","CODIGO");	
		$ls_est_ordconc=$io_fun_nomina->uf_obtenervalor("cmbordconc","CODIGO");	
		$ls_est_estrec=$io_fun_nomina->uf_obtenervalor("cmbestrec","NORMAL");	
		$li_est_numlin=$io_fun_nomina->uf_obtenervalor("txtnumlin","NORMAL");	
		$ls_est_prilpt=$io_fun_nomina->uf_obtenervalor("cmbprilpt","WINDOWS");	
		$ls_est_agrsem=$io_fun_nomina->uf_obtenervalor("cmbagrsem","2");	
		$li_con_parnom=$io_fun_nomina->uf_obtenervalor("chkparnom","0");	
		$ls_con_consue=$io_fun_nomina->uf_obtenervalor("cmbconsue","OCP");	
		$ls_con_cuecon=$io_fun_nomina->uf_obtenervalor("txtcuecon","XXXXXXXXXXXXX");	
		$ls_con_conapo=$io_fun_nomina->uf_obtenervalor("cmbconapo","OCP");	
		$ls_con_conpro=$io_fun_nomina->uf_obtenervalor("cmbconpro","UBICACION ADMINISTRATIVA");	
		$li_con_agrcon=$io_fun_nomina->uf_obtenervalor("chkagrcon","0");	
		$li_con_gennotdeb=$io_fun_nomina->uf_obtenervalor("chkgennotdeb","0");	
		$li_con_genrecdoc=$io_fun_nomina->uf_obtenervalor("chkgenrecdoc","0");
		$li_con_recdoccaunom=$io_fun_nomina->uf_obtenervalor("chkrecdoccaunom","0");
		$ls_con_tipdoccaunom=$io_fun_nomina->uf_obtenervalor("txttipdoccaunom","-");		
		$ls_estagrapo	=$io_fun_nomina->uf_obtenervalor("chkestagrapo","0");
		$li_con_genrecdocapo=$io_fun_nomina->uf_obtenervalor("chkgenrecdocapo","0");	
		$ls_con_tipdocnom=$io_fun_nomina->uf_obtenervalor("txttipdocnom","-");
		$ls_con_tipdocapo=$io_fun_nomina->uf_obtenervalor("txttipdocapo","-");
		$li_con_genvou=$io_fun_nomina->uf_obtenervalor("chkgenvou","0");	
		$ls_con_descon=$io_fun_nomina->uf_obtenervalor("cmbdescon","-").$io_fun_nomina->uf_obtenervalor("txtcodproben","-");
		$ls_con_confidnom=$io_fun_nomina->uf_obtenervalor("cmbconfidnom","OC");
		$ls_con_recdocfid=$io_fun_nomina->uf_obtenervalor("chkrecdocfid","0");
		$ls_con_tipdocfid=$io_fun_nomina->uf_obtenervalor("txttipdocfid","-");
		$ls_con_recdocguar=$io_fun_nomina->uf_obtenervalor("chkrecdocguar","0");
		$ls_con_benrecdocguar=$io_fun_nomina->uf_obtenervalor("chkbenrecdocgua","0");
		$ls_con_tipdocguar=$io_fun_nomina->uf_obtenervalor("txttipdocguar","-");
		$ls_con_cueconfid=$io_fun_nomina->uf_obtenervalor("txtcueconfid","-");
		$ls_con_codbenfid=$io_fun_nomina->uf_obtenervalor("txtcodbenfid","-");
		$ls_codban=$io_fun_nomina->uf_obtenervalor("txtcodban","-");		
		$ls_cuenta_banco=$io_fun_nomina->uf_obtenervalor("txtcuenta","-");
		$ls_codconcbanavih=$io_fun_nomina->uf_obtenervalor("txtcodconcbanavih","-");		
		$ls_codbanperche=$io_fun_nomina->uf_obtenervalor("txtcodbanperche","-");		
		$ls_cuenta_bancoperche=$io_fun_nomina->uf_obtenervalor("txtcuentaperche","-");		
		$ls_con_codguarcontper=$io_fun_nomina->uf_obtenervalor("txtctaguarper","-");
		$ls_con_codguarcontobr=$io_fun_nomina->uf_obtenervalor("txtctaguarobr","-");
		$ls_con_codguarcontpercon=$io_fun_nomina->uf_obtenervalor("txtctaguarpercon","-");
		$ls_con_codguarcontobrcon=$io_fun_nomina->uf_obtenervalor("txtctaguarobrcon","-");
		$li_par_excpersus=$io_fun_nomina->uf_obtenervalor("chkexcpersus","0");	
		$li_par_perrep=$io_fun_nomina->uf_obtenervalor("chkperrep","0");	
		$ld_par_fecfinano=$io_fun_nomina->uf_obtenervalor("txtfecfinano","-");	
		$ls_par_metcalfid=$io_fun_nomina->uf_obtenervalor("cmbmetcalfid","VERSION 2");	
		$ls_par_confpre=$io_fun_nomina->uf_obtenervalor("cmbconfpre","CUOTAS");	
		$li_par_camuniadm=$io_fun_nomina->uf_obtenervalor("chkcamuniadm","0");
		$li_par_camdedtipper=$io_fun_nomina->uf_obtenervalor("chkcamdedtipper","0");
		$li_par_campasogrado=$io_fun_nomina->uf_obtenervalor("chkcampasogrado","0");
		$li_par_incperben=$io_fun_nomina->uf_obtenervalor("chkincperben","0");
		$li_par_codunirac=$io_fun_nomina->uf_obtenervalor("chkcodunirac","0");
		$li_par_comautrac=$io_fun_nomina->uf_obtenervalor("chkcomautrac","0");
		$li_par_ajusuerac=$io_fun_nomina->uf_obtenervalor("chkajusuerac","0");
		$li_par_modpensiones=$io_fun_nomina->uf_obtenervalor("chkmodpensiones","0");
		$ls_par_cueconben=$io_fun_nomina->uf_obtenervalor("txtcueconben","-");
		$li_par_valporpre=$io_fun_nomina->uf_obtenervalor("chkvalporpre","0");
		$li_par_campsuerac=$io_fun_nomina->uf_obtenervalor("chkcamsuerac","0");
		$ls_fpj_codorgfpj=$io_fun_nomina->uf_obtenervalor("txtcodorgfpj","XXXXXXXX");	
		$ls_fpj_codconcfpj=$io_fun_nomina->uf_obtenervalor("txtcodconcfpj","XXXXXXXX");	
		$ls_fpj_metfpj=$io_fun_nomina->uf_obtenervalor("cmbmetfpj","SUELDO NORMAL");	
		$ls_codconcsalbasfpj=$io_fun_nomina->uf_obtenervalor("txtcodconcsalbasfpj","");
		$ls_codconcantfpj=$io_fun_nomina->uf_obtenervalor("txtcodconcantfpj","");
		$ls_codconcefifpj=$io_fun_nomina->uf_obtenervalor("txtcodconcefifpj","");
		$ls_codconcotrprifpj=$io_fun_nomina->uf_obtenervalor("txtcodconcotrprifpj","");		
		$ls_lph_codconclph=$io_fun_nomina->uf_obtenervalor("txtcodconclph","XXXXXXXXXX");	
		$ls_lph_metlph=$io_fun_nomina->uf_obtenervalor("cmbmetlph","SIN METODO");	
		$ls_fpa_codconcfpa=$io_fun_nomina->uf_obtenervalor("txtcodconcfpa","XXXXXXXXXX");	
		$ls_fpa_metfpa=$io_fun_nomina->uf_obtenervalor("cmbmetfpa","SIN METODO");	
		$li_fps_antcom=$io_fun_nomina->uf_obtenervalor("chkantcom","0");
		$li_difconpnom=$io_fun_nomina->uf_obtenervalor("chkdifconpnom","0");
		$li_fps_incvacagui=$io_fun_nomina->uf_obtenervalor("chkincvacagui","0");
		$li_fps_fraali=$io_fun_nomina->uf_obtenervalor("chkfraali","0");
		$li_fps_intasiextra=$io_fun_nomina->uf_obtenervalor("chkintasiextra","0");
		$li_fps_calintpreant=$io_fun_nomina->uf_obtenervalor("chkcalintpreant","0");
		$li_fps_calintpercon=$io_fun_nomina->uf_obtenervalor("chkcalintpercon","0");
		$li_fps_acuintdiaadi=$io_fun_nomina->uf_obtenervalor("chkacuintdiaadi","0");
		$li_fps_presocdiaadi=$io_fun_nomina->uf_obtenervalor("chkpresocdiaadi","0");
		$li_fps_antprimeranio=$io_fun_nomina->uf_obtenervalor("chkantprimeranio","0");
	    $li_fps_uniestpre=$io_fun_nomina->uf_obtenervalor("chkuniestpre","0");
		$ls_fps_codestpro1=$io_fun_nomina->uf_obtenervalor("txtcodestpro1","");
		$ls_fps_estcla=$io_fun_nomina->uf_obtenervalor("txtestcla","");
		$ls_fps_codestpro2=$io_fun_nomina->uf_obtenervalor("txtcodestpro2","");
		$ls_fps_codestpro3=$io_fun_nomina->uf_obtenervalor("txtcodestpro3","");
		$ls_fps_codestpro4=$io_fun_nomina->uf_obtenervalor("txtcodestpro4","");
		$ls_fps_codestpro5=$io_fun_nomina->uf_obtenervalor("txtcodestpro5","");

		
		/// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente.
		$li_fps_diasadicionalesBV=$io_fun_nomina->uf_obtenervalor("chkdiasadicionalesBV","0");
		/// fin de lo agregado
		$li_fps_pormaxant=$io_fun_nomina->uf_obtenervalor("txtpormaxant","0");
		$ls_fps_tipdocant=$io_fun_nomina->uf_obtenervalor("txttipdocant","-");	
		$li_fps_calperact=$io_fun_nomina->uf_obtenervalor("chkcalperact","0");
		$ls_fps_metfps=$io_fun_nomina->uf_obtenervalor("cmbmetfps","SIN METODO");	
		$ls_man_cueconc=$io_fun_nomina->uf_obtenervalor("txtcueconc","401");	
		$ls_man_cueconccaj=$io_fun_nomina->uf_obtenervalor("txtcueconccaj","0");	
		$li_man_actblofor=$io_fun_nomina->uf_obtenervalor("chkactblofor","0");	
		$li_man_actblocalnom=$io_fun_nomina->uf_obtenervalor("chkactblocalnom","0");	
		$ls_man_metrescon=$io_fun_nomina->uf_obtenervalor("cmbmetrescon","SIN METODO");	
		$ls_dis_metdisnom=$io_fun_nomina->uf_obtenervalor("cmbmetdisnom","SIN METODO");	
		$ls_ipas_codorgipas=$io_fun_nomina->uf_obtenervalor("txtcodorgipas","XXX");
		$ls_ipas_codconcahoipas=$io_fun_nomina->uf_obtenervalor("txtcodconcahoipas","XXXXXXXXXX");
		$ls_ipas_codconcseripas=$io_fun_nomina->uf_obtenervalor("txtcodconcseripas","XXXXXXXXXX");
		$ls_ipas_conhipespipas=$io_fun_nomina->uf_obtenervalor("txtconhipespipas","XXXXXXXXXX");
		$ls_ipas_conhipampipas=$io_fun_nomina->uf_obtenervalor("txtconhipampipas","XXXXXXXXXX");
		$ls_ipas_conhipconipas=$io_fun_nomina->uf_obtenervalor("txtconhipconipas","XXXXXXXXXX");
		$ls_ipas_conhiphipipas=$io_fun_nomina->uf_obtenervalor("txtconhiphipipas","XXXXXXXXXX");
		$ls_ipas_conhiplphipas=$io_fun_nomina->uf_obtenervalor("txtconhiplphipas","XXXXXXXXXX");
		$ls_ipas_conhipvivipas=$io_fun_nomina->uf_obtenervalor("txtconhipvivipas","XXXXXXXXXX");
		$ls_ipas_conperipas=$io_fun_nomina->uf_obtenervalor("txtconperipas","XXXXXXXXXX");
		$ls_ipas_conturipas=$io_fun_nomina->uf_obtenervalor("txtconturipas","XXXXXXXXXX");
		$ls_ipas_conproipas=$io_fun_nomina->uf_obtenervalor("txtconproipas","XXXXXXXXXX");
		$ls_ipas_conasiipas=$io_fun_nomina->uf_obtenervalor("txtconasiipas","XXXXXXXXXX");
		$ls_ipas_convehipas=$io_fun_nomina->uf_obtenervalor("txtconvehipas","XXXXXXXXXX");
		$ls_ipas_concomipas=$io_fun_nomina->uf_obtenervalor("txtconcomipas","XXXXXXXXXX");
		$ls_ivss_numemp=$io_fun_nomina->uf_obtenervalor("txtnumempivss","XXXXXXXXX");
		$ls_ivss_dirtalhumivss=$io_fun_nomina->uf_obtenervalor("txtdirtalhumivss","");
		$ls_ivss_ceddirtalhumivss=$io_fun_nomina->uf_obtenervalor("txtceddirtalhumivss","");
		$li_ivss_nomesp=$io_fun_nomina->uf_obtenervalor("chknomespivss","0"); 
		$ls_par_concsuelant=$io_fun_nomina->uf_obtenervalor("txtcodconcsuelant","XXXXXXXXXX");
		$li_par_loncueban=$io_fun_nomina->uf_obtenervalor("txtloncueban","0");
		$li_par_alfnumcodper=$io_fun_nomina->uf_obtenervalor("chkalfnumcodper","0");
		$li_par_valloncueban=$io_fun_nomina->uf_obtenervalor("chkvalloncueban","0");
		$ls_ivss_metodo=$io_fun_nomina->uf_obtenervalor("cmbmetivss","SIN METODO");
		$li_con_parfpj=$io_fun_nomina->uf_obtenervalor("chkparfpj","0");
		$ls_edadM=$io_fun_nomina->uf_obtenervalor("txtedadM","0");
		$ls_edadF=$io_fun_nomina->uf_obtenervalor("txtedadF","0");
		$ls_anoM=$io_fun_nomina->uf_obtenervalor("txtanoM","0");
		$ls_anoT=$io_fun_nomina->uf_obtenervalor("txtanoT","0");
		$li_prestamo=$io_fun_nomina->uf_obtenervalor("chkprestamos","0");
		$ls_sueint=$io_fun_nomina->uf_obtenervalor("txtsueint","-");
		$li_persobregiro=$io_fun_nomina->uf_obtenervalor("chkpersobregiro","0");
		$li_percobmoncer=$io_fun_nomina->uf_obtenervalor("chkpercobmoncer","0");
		$li_chkgenpagperche=$io_fun_nomina->uf_obtenervalor("chkgenpagperche","0");
	    $li_chkpercargoalfa=$io_fun_nomina->uf_obtenervalor("chkpercargoalfa","0");
        $li_chkmancueban=$io_fun_nomina->uf_obtenervalor("chkmancueban","0");
		$ls_tipdocpagperche=$io_fun_nomina->uf_obtenervalor("txttipdocpagper","-");
		$li_genrecdocpagperche=$io_fun_nomina->uf_obtenervalor("chkgenrecdocpagper","0");
		$li_estctaalt=$io_fun_nomina->uf_obtenervalor("chkestctaalt","0");
		$ls_codorgvipladin=$io_fun_nomina->uf_obtenervalor("txtcodorgvipladin","-");
		$ls_grupovipladin=$io_fun_nomina->uf_obtenervalor("txtgrupovipladin","-");
		$ls_codubivipladin=$io_fun_nomina->uf_obtenervalor("txtcodubivipladin","-");
		$ls_distritovipladin=$io_fun_nomina->uf_obtenervalor("txtdistritovipladin","-");
		$ls_municipiovipladin=$io_fun_nomina->uf_obtenervalor("txtmunicipiovipladin","-");
		$ls_vigenciavipladin=$io_fun_nomina->uf_obtenervalor("txtvigenciavipladin","-");
		$ls_depsalnorqui=$io_fun_nomina->uf_obtenervalor("cmbdepsalnorqui","0");
		$ls_depsalnoradi=$io_fun_nomina->uf_obtenervalor("cmbdepsalnoradi","0");
		$ls_depsalnorvac=$io_fun_nomina->uf_obtenervalor("cmbdepsalnorvac","0");	
		$ls_fps_forcalpres=$io_fun_nomina->uf_obtenervalor("cmbforcalpres","0");	
		$ls_fps_metcalalibonvac=$io_fun_nomina->uf_obtenervalor("cmbmetcalalibonvac","INTEGRAL");	
		if ($ls_sueint!="-")
		{
			$ls_readonly="readonly";
		}
		else
		{
			$ls_readonly="";
		}
   }// end function uf_load_variables
   //--------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script >
	if(document.all)
	{ //ie 
		document.onkeydown = function(){ 
		if(window.event && (window.event.keyCode == 122 || window.event.keyCode == 116 || window.event.ctrlKey)){
		window.event.keyCode = 505; 
		}
		if(window.event.keyCode == 505){ 
		return false; 
		} 
		} 
	}
</script>
<title >Configuraci&oacute;n</title>
<meta http-equiv="imagetoolbar" content="no"> 
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #EFEBEF;
}

a:link {
	color: #006699;
}
a:visited {
	color: #006699;
}
a:active {
	color: #006699;
}

-->
</style>
<script type="text/javascript"  src="js/stm31.js"></script>
<script type="text/javascript"  src="js/funcion_nomina.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<script  src="../shared/js/js_intra/datepickercontrol.js"></script>
<script  src="../shared/js/valida_tecla.js"></script>
<link href="css/nomina.css" rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css" rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css" rel="stylesheet" type="text/css">
<link href="../shared/css/general.css" rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.Estilo1 {font-size: 12px}
-->
</style>
</head>
<body>
<?php 
	$ls_loncodestpro1=$_SESSION["la_empresa"]["loncodestpro1"];
	$ls_loncodestpro2=$_SESSION["la_empresa"]["loncodestpro2"];
	$ls_loncodestpro3=$_SESSION["la_empresa"]["loncodestpro3"];
	$ls_loncodestpro4=$_SESSION["la_empresa"]["loncodestpro4"];
	$ls_loncodestpro5=$_SESSION["la_empresa"]["loncodestpro5"];	
	$ls_nomestpro1=$_SESSION["la_empresa"]["nomestpro1"];		
	$ls_nomestpro2=$_SESSION["la_empresa"]["nomestpro2"];		
	$ls_nomestpro3=$_SESSION["la_empresa"]["nomestpro3"];		
	$ls_nomestpro4=$_SESSION["la_empresa"]["nomestpro4"];		
	$ls_nomestpro5=$_SESSION["la_empresa"]["nomestpro5"];		
	
	require_once("sigesp_sno.php");
	$io_sno=new sigesp_sno();
	$ls_operacion=$io_fun_nomina->uf_obteneroperacion();
	switch ($ls_operacion) 
	{
		case "REPARARSUBNOMINAS":
			$lb_valido=$io_sno->uf_reparar_subnominas($la_seguridad);
			break;
			
		case "REPARARCONCEPTOPERSONAL":
			$lb_valido=$io_sno->uf_reparar_conceptopersonal($la_seguridad);
			break;
			
		case "RECALCULARSUELDOINTEGRAL":
			$lb_valido=$io_sno->uf_recalcular_sueldointegral($la_seguridad);
			break;
			
		case "RECALCULARCONCEPTOS":
			$lb_valido=$io_sno->uf_recalcular_conceptos($la_seguridad);
			break;
		
		case "MANTENIMIENTOHISTORICOS":
			$lb_valido=$io_sno->uf_mantenimiento_historicos($la_seguridad);
			break;
		
		case "REPARARACUMULADOCONCEPTOS":
			$lb_valido=$io_sno->uf_mantenimiento_repararacumuladoconceptos($la_seguridad);
			break;
		
		case "GUARDAR":
			uf_load_variables();
			$lb_valido=$io_sno->uf_guardar_configuracion($li_vac_reportar,$ls_vac_codconvac,$ls_vac_metvac,$ls_est_codconcsue,
														 $ls_est_estnom,$ls_est_ordcons,$ls_est_ordconc,$ls_est_estrec,$li_est_numlin,
									  					 $ls_est_prilpt,$ls_est_agrsem,$li_con_parnom,$ls_con_consue,$ls_con_cuecon,
									  					 $ls_con_conapo,$ls_con_conpro,$li_con_agrcon,$li_con_gennotdeb,$li_con_genvou,
									  					 $ls_con_descon,$li_par_excpersus,$li_par_perrep,$ld_par_fecfinano,$ls_par_metcalfid,
									 					 $ls_fpj_codorgfpj,$ls_fpj_codconcfpj,$ls_fpj_metfpj,$ls_lph_codconclph,$ls_lph_metlph,
														 $ls_fpa_codconcfpa,$ls_fpa_metfpa,$li_fps_antcom,$li_fps_fraali,$ls_fps_metfps,
									  					 $ls_man_cueconc,$ls_man_cueconccaj,$li_man_actblofor,$li_man_actblocalnom,
									  					 $ls_man_metrescon,$ls_dis_metdisnom,$li_con_genrecdoc,$li_con_genrecdocapo,
														 $ls_con_tipdocnom,$ls_con_tipdocapo,$ls_ipas_codorgipas,$ls_ipas_codconcahoipas,
														 $ls_ipas_codconcseripas,$ls_ipas_conhipespipas,$ls_ipas_conhipampipas,$ls_ipas_conhipconipas,
														 $ls_ipas_conhiphipipas,$ls_ipas_conhiplphipas,$ls_ipas_conhipvivipas,$ls_ipas_conperipas,
														 $ls_ipas_conturipas,$ls_ipas_conproipas,$ls_ipas_conasiipas,$ls_ipas_convehipas,
														 $ls_ipas_concomipas,$ls_ivss_numemp,$li_vac_desincorporar,$ls_par_concsuelant,
														 $ls_par_confpre,$li_par_camuniadm,$li_par_campasogrado,$li_par_incperben,$ls_par_cueconben,
														 $li_par_codunirac,$li_par_comautrac,$li_par_ajusuerac,$li_par_loncueban,$li_par_modpensiones,
														 $li_par_valloncueban,$li_par_valporpre,$ls_con_confidnom,$ls_con_recdocfid,$ls_con_recdocguar,
														 $ls_con_tipdocfid,$ls_con_tipdocguar,$ls_con_cueconfid,$ls_con_codbenfid,$ls_con_codguarcontper,
														 $ls_con_codguarcontobr,$ls_con_codguarcontpercon,$ls_con_codguarcontobrcon,$ls_ivss_metodo,
														 $li_par_alfnumcodper,$li_con_parfpj,$ls_edadM, $ls_edadF,$ls_anoM,$ls_anoT,$li_prestamo, 
														 $li_par_campsuerac,$li_fps_intasiextra,$ls_sueint,$li_par_camdedtipper,$li_persobregiro,
														 $li_genrecdocpagperche,$ls_tipdocpagperche,$li_salvacper,$li_presalvacper,$li_estctaalt,$li_fps_incvacagui,
														 $ls_codban,$ls_cuenta_banco,$li_chkgenpagperche,$ls_codbanperche,$ls_cuenta_bancoperche,
														 $ls_codconcbanavih,$ls_codorgvipladin,$ls_grupovipladin,$ls_codubivipladin,$ls_distritovipladin,
														 $ls_municipiovipladin,$ls_vigenciavipladin,$li_fps_calintpreant,$li_fps_pormaxant,$ls_fps_tipdocant,
														 $li_fps_calintpercon,$li_fps_diasadicionalesBV,$li_fps_calperact,$li_ivss_nomesp,$ls_con_benrecdocguar,
														 $li_fps_acuintdiaadi,$li_fps_presocdiaadi,$li_difconpnom,$ls_depsalnorqui,$ls_depsalnoradi,$ls_depsalnorvac,
														 $ls_fps_metcalalibonvac,$li_fps_antprimeranio,$ls_fps_forcalpres,$ls_estagrapo,$ls_ivss_dirtalhumivss,
														 $ls_ivss_ceddirtalhumivss,$li_con_recdoccaunom,$ls_con_tipdoccaunom,$ls_codconcsalbasfpj,$ls_codconcantfpj,
														 $ls_codconcefifpj,$ls_codconcotrprifpj,$li_percobmoncer,$li_chkmancueban,$li_chkpercargoalfa,$li_fps_uniestpre,
														 $ls_fps_codestpro1,$ls_fps_estcla,$ls_fps_codestpro2,$ls_fps_codestpro3,$ls_fps_codestpro4,$ls_fps_codestpro5,
														 $la_seguridad); /// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente.
			
			if($lb_valido)
			{
				$io_sno->io_mensajes->message("La configuraci?n fue registrada.");
			}
			else
			{
				$io_sno->io_mensajes->message("Ocurrio un error al guardar la configuraci?n.");
			}
			break;
	}
	uf_select_campos();
	unset($io_sno);
?>
<table width="762" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td width="780" height="30" colspan="11" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="778" height="40"></td>
  </tr>
  <tr>
    <td width="432" height="20" colspan="11" bgcolor="#E7E7E7">
		<table width="762" border="0" align="center" cellpadding="0" cellspacing="0">
			<td width="432" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Sistema de N?mina</td>
			<td width="346" bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print date("j/n/Y")." - ".date("h:i a");?></b></div></td>
	  	    <tr>
	  	      <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	      <td bgcolor="#E7E7E7" class="letras-pequenas"><div align="right"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td></tr>
        </table>
	 </td>
  </tr>
  <tr>
    <td height="20" colspan="11" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td width="780" height="13" colspan="11" class="toolbar"></td>
  </tr>
  <tr>
    <td width="25" height="20" class="toolbar"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif"  title="Guardar" alt="Grabar" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><a href="javascript: ue_cerrar();"><img src="../shared/imagebank/tools20/salir.gif"  title="Salir" alt="Salir" width="20" height="20" border="0"></a></div></td>
    <td class="toolbar" width="25"><div align="center"><img src="../shared/imagebank/tools20/ayuda.gif"  title="Ayuda" alt="Ayuda" width="20" height="20"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="25"><div align="center"></div></td>
    <td class="toolbar" width="530">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form name="form1" method="post" action="">
<?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_nomina->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_nomina);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>		  
<table width="760" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
  <tr>
    <td height="136">
      <p>&nbsp;</p>
      <table width="710" border="0" align="center" cellpadding="1" cellspacing="0" class="formato-blanco">
        <tr>
            <td height="20" colspan="4">
		<div id=transferir style="visibility:hidden;height:50" align="center"><img src="../shared/imagebank/cargando.gif">Procesando Configuracion... </div>		  
            </td>
          </tr>
        <tr class="titulo-ventana">
          <td height="20" colspan="4" class="titulo-ventana">Configuraci&oacute;n</td>
        </tr>
        <tr>
          <td height="22" colspan="4" class="titulo-celdanew">Sueldo Integral</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Cambiar denominaci&oacute;n Sueldo Integral</div></td>
          <td colspan="4"><div align="left">
            <input name="chksueint" type="checkbox" class="sin-borde" value="1" <?php if($ls_sueint!="-"){print "checked";} ?>   onClick="javascript: activar_denominacion();">
              <input name="txtsueint" type="text" id="txtsueint" value="<?php print $ls_sueint;?>" size="60" maxlength="100" onKeyUp="ue_validarcomillas(this);"  <?php print $ls_readonly;?> >
          </div></td>
        </tr>
		<tr class="titulo-celdanew">
          <td height="20" colspan="4"><div align="center">Vacaciones</div></td>
        </tr>
        <tr>
          <td width="150" height="22"><div align="right">M&eacute;todo Vacaci&oacute;n </div></td>
          <td><div align="left">
              <select name="cmbmetvac" id="cmbmetvac">
                <option value="0" <?php print $la_vac_metban[0]; ?>>Sin M&eacute;todo</option>
                <option value="1" <?php print $la_vac_metban[1]; ?>>M&eacute;todo #0</option>
              </select>
          </div></td>
          <td><div align="right">Desincorporar de la N&oacute;mina </div></td>
          <td><div align="left">
              <input name="chkvacdesincorporar" type="checkbox" class="sin-borde" id="chkvacdesincorporar" value="1" <?php if($li_vac_desincorporar!="0"){print "checked";} ?> onChange="javascript: ue_bloqueardesincorpora();">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Reportar</div></td>
          <td width="193"><div align="left">
            <input name="chkvacreportar" type="checkbox" class="sin-borde" value="1" <?php if($li_vac_reportar!="0"){print "checked";} ?>>
          </div></td>
          <td width="141"><div align="right">C&oacute;digo Concepto Vacaci&oacute;n</div></td>
          <td width="200"><div align="left">
            <input name="txtcodconvac" type="text" id="txtcodconvac" value="<?php print $ls_vac_codconvac;?>" size="13" maxlength="10" onKeyUp="javascript: ue_validarnumero(this);" onBlur="javascript: ue_rellenarcampo(this,10);">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Permitir salida del Personal a partir del mes anterior a la Fecha de Vencimiento</div></td>
          <td width="193"><div align="left">
            <input name="chksalvacper" type="checkbox" class="sin-borde" value="1" <?php if($li_salvacper!="0"){print "checked";} ?>>
          </div></td>
          <td height="22"><div align="right">Pr?stamo para el personal que se desincorpora de la nomina en periodo de vacaciones</div></td>
          <td width="200"><div align="left">
            <input name="chkpresalvacper" type="checkbox" class="sin-borde" value="1" <?php if($li_presalvacper!="0"){print "checked";}if($li_vac_desincorporar=="0"){print "disabled";} ?>>
          </div></td>
        </tr>
        <tr class="titulo-celdanew">
          <td height="20" colspan="4"><div align="center">Contabilizaci&oacute;n</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Par&aacute;metros por n&oacute;mina </div></td>
          <td><div align="left">
            <input name="chkparnom" type="checkbox" class="sin-borde" id="chkparnom" value="1" <?php if($li_con_parnom!="0"){print "checked";} ?> onChange="javascript: ue_bloquear();">
          </div></td>
          <td><div align="right">Agrupar Contable</div></td>
          <td><div align="left">
              <input name="chkagrcon" type="checkbox" class="sin-borde" id="chkagrcon" value="1" <?php if($li_con_agrcon!="0"){print "checked";} if($li_con_parnom!="0"){print "disabled";} ?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Destino Contabilizaci&oacute;n</div></td>
          <td colspan="3"><div align="left">
              <select name="cmbdescon" id="cmbdescon" <?php if($li_con_parnom!="0"){print "disabled";} ?> onChange="javascript: ue_limpiar();">
                <option value="-" <?php print $la_con_descon[0]; ?>> </option>
                <option value="P" <?php print $la_con_descon[1]; ?>>PROVEEDOR</option>
                <option value="B" <?php print $la_con_descon[2]; ?>>BENEFICIARIO</option>
              </select>
              <input name="txtcodproben" type="text" id="txtcodproben" value="<?php print $ls_con_descon;?>" readonly>
              <a href="javascript: ue_buscardestino();"><img  src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
              <input name="txtnombre" type="text" class="sin-borde" id="txtnombre" size="50" maxlength="30" readonly>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Utilizar Cuenta Contable para el registro del Gasto por pagar </div></td>
          <td><div align="left">
              <input name="chkestctaalt" type="checkbox" class="sin-borde" id="chkestctaalt" value="1" <?php  if($li_estctaalt=="1"){print "checked";} if($li_con_parnom!="0"){print "disabled";} ?>  onClick="javascript:ue_chequear_nomina_beneficiario();" >
          </div></td>
        <tr>
          <td height="22" colspan="2"><div align="center" class="titulo-conect Estilo1">N&oacute;mina</div></td>
          <td colspan="2"><div align="center" class="titulo-conect Estilo1">Aportes</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">N&oacute;mina</div></td>
          <td><div align="left">
              <select name="cmbconsue" id="cmbconsue" onChange="javascript: ue_contabilizacionnomina();" <?php if($li_con_parnom!="0"){print "disabled";} ?>>
                <option value="CP" <?php print $la_con_consue[0]; ?>>Causar y Pagar</option>
                <option value="OCP" <?php print $la_con_consue[1]; ?>>Compromete, Causa y Paga</option>
                <option value="OC" <?php print $la_con_consue[2]; ?>>Compromete y Causa</option>
                <option value="O" <?php print $la_con_consue[3]; ?>>Compromete</option>
              </select>
          </div></td>
          <td><div align="right">Aportes</div></td>
          <td><select name="cmbconapo" id="cmbconapo" onChange="javascript: ue_contabilizacionaportes();" <?php if($li_con_parnom!="0"){print "disabled";} ?>>
              <option value="CP" <?php print $la_con_conapo[0]; ?>>Causar y Pagar</option>
              <option value="OCP" <?php print $la_con_conapo[1]; ?>>Compromete, Causa y Paga</option>
              <option value="OC" <?php print $la_con_conapo[2]; ?>>Compromete y Causa</option>
              <option value="O" <?php print $la_con_conapo[3]; ?>>Compromete</option>
          </select></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Generar Recepci&oacute;n de Documento a la N&oacute;mina </div></td>
          <td><div align="left">
              <input name="chkgenrecdoc" type="checkbox" class="sin-borde" id="chkgenrecdoc" value="1" onChange="javascript: ue_recepcionnomina();" <?php if($li_con_genrecdoc!="0"){print "checked";} if($li_con_parnom!="0"){print "disabled";} ?>>
          </div></td>
          <td><div align="right">Generar Recepci&oacute;n de Documento a los aportes </div></td>
          <td><div align="left">
              <input name="chkgenrecdocapo" type="checkbox" class="sin-borde" id="chkgenrecdocapo" value="1" onChange="javascript: ue_recepcionaportes();" <?php if($li_con_genrecdocapo!="0"){print "checked";} if($li_con_parnom!="0"){print "disabled";} ?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tipo de Documento N&oacute;mina </div></td>
          <td><div align="left">
              <input name="txttipdocnom" type="text" id="txttipdocnom" value="<?php print $ls_con_tipdocnom;?>" readonly>
          <a href="javascript: ue_buscartipodocumento('NOMINA');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </div></td>
          <td><div align="right">Tipo de Documento Aporte </div></td>
          <td><div align="left">
              <input name="txttipdocapo" type="text" id="txttipdocapo" value="<?php print $ls_con_tipdocapo;?>" readonly>
          <a href="javascript: ue_buscartipodocumento('APORTE');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Cuenta Contable</div></td>
          <td><div align="left">
              <input name="txtcuecon" type="text" id="txtcuecon" value="<?php print $ls_con_cuecon;?>" readonly>
          <a href="javascript: ue_buscarcuentacontable('CONFIGURACION');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Agrupar retenciones y Aportes</div></td>
          <td><div align="left">
            <input name="chkestagrapo" type="checkbox" class="sin-borde" id="chkestagrapo" value="1"  <?php if($ls_estagrapo=="1"){ print " checked ";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Generar Nota D&eacute;bito en bancos</div></td>
          <td><div align="left">
              <input name="chkgennotdeb" type="checkbox" class="sin-borde" id="chkgennotdeb" value="1"  onChange="javascript: ue_notadebito();"  <?php if($li_con_gennotdeb!="0"){print "checked";}  if($li_con_parnom!="0"){print "disabled";} ?>>
          </div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Generar Recepcion de Documentos Causa</div></td>
          <td><div align="left">
            <input name="chkrecdoccaunom" type="checkbox" class="sin-borde" id="chkrecdoccaunom" value="1" onChange="javascript: ue_recepcioncausanomina();" <?php if($li_con_recdoccaunom=="1"){ print " checked ";} print $ls_activo_contabilizacion;?>>
          </div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tipo de Documento Causado</div></td>
          <td><div align="left">
            <input name="txttipdoccaunom" type="text" id="txttipdoccaunom" value="<?php print $ls_con_tipdoccaunom;?>" readonly>
            <a href="javascript: ue_buscartipodocumento('CAUSADO');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Generar Recepci&oacute;n de Documento Autmaticamente para el Pago del Personal con Cheque</div></td>
          <td><div align="left">
              <input name="chkgenrecdocpagper" type="checkbox" class="sin-borde" id="chkgenrecdocpagper" value="1" <?php if($li_genrecdocpagperche!="0"){print "checked";} if($li_con_parnom!="0"){print "disabled";} ?>>
          </div></td>
          <td height="22"><div align="right">Tipo de Documento del Pago de Personal</div></td>
          <td><input name="txttipdocpagper" type="text" id="txttipdocpagper" value="<?php print $ls_tipdocpagperche;?>" readonly>
            <a href="javascript: ue_buscartipodocumento('PAGOPERSONAL');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
        </tr>
        <tr>
          <td height="22" colspan="2"><div align="center"><span class="titulo-conect Estilo1">Prestaci&oacute;n Antiguedad </span></div></td>
          <td height="22" colspan="2"><div align="center"><span class="titulo-conect Estilo1">Guarderias </span></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Prestaci&oacute;n Antiguedad</div></td>
          <td><select name="cmbconfidnom" id="cmbconfidnom" onChange="javascript: ue_contabilizacionfideicomiso();" <?php  if($li_con_parnom!="0"){print "disabled";} ?>>
              <option value="OC" <?php print $la_con_confidnom[0]; ?>>Compromete y Causa</option>
              <option value="OCP" <?php print $la_con_confidnom[1]; ?>>Compromete, Causa Y Paga</option>
            </select>          </td>
          <td height="22"><div align="right">Guarderia</div></td>
          <td><select name="cmbconguarnom" id="cmbconguarnom">
              <option value="OC" <?php print $la_con_confidnom[0]; ?>>Compromete y Causa</option>
            </select>          </td>
        </tr>
        <tr>
          <td height="22"><div align="right">Generar Recepcion de Documentos</div></td>
          <td><input name="chkrecdocfid" type="checkbox" class="sin-borde" id="chkrecdocfid" value="1" onChange="javascript: ue_recepcionfideicomiso();" <?php if($ls_con_recdocfid=="1"){ print " checked ";}  if($li_con_parnom!="0"){print "disabled";} ?>>          </td>
          <td height="22"><div align="right">Generar Recepcion de Documentos</div></td>
          <td><div align="left">
            <input name="chkrecdocguar" type="checkbox" class="sin-borde" id="chkrecdocguar" value="1"	onChange="javascript: ue_recepcionguarderia();" <?php if($ls_con_recdocguar=="1"){ print " checked ";}?>>          
          </div></td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td>&nbsp;</td>
          <td height="22"><div align="right">Generar Recepci&oacute;n a nombre de la Guarderia </div></td>
          <td><div align="left">
            <input name="chkbenrecdocgua" type="checkbox" class="sin-borde" id="chkbenrecdocgua" value="1"	onChange="javascript: ue_recepcionbeneficiarioguarderia();" <?php if($ls_con_benrecdocguar=="1"){ print " checked ";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Tipo de Documento </div></td>
          <td><input name="txttipdocfid" type="text" id="txttipdocfid" value="<?php print $ls_con_tipdocfid;?>" readonly>
            <a href="javascript: ue_buscartipodocumento('FIDEICOMISO');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
          <td height="22"><div align="right">Tipo de Documento </div></td>
          <td><input name="txttipdocguar" type="text" id="txttipdocguar" value="<?php print $ls_con_tipdocguar;?>" readonly>
            <a href="javascript: ue_buscartipodocumento('GUARDERIA');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
        </tr>
        <tr>
          <td height="22"><div align="right">Beneficiario</div></td>
          <td><input name="txtcodbenfid" type="text" id="txtcodbenfid" value="<?php print $ls_con_codbenfid; ?>" readonly>
            <a href="javascript: ue_buscarbeneficiario();"><img  src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
          <td height="22"><div align="right">Cta. Presup.Empleado </div></td>
          <td><input name="txtctaguarper" type="text" id="txtctaguarper" value="<?php print $ls_con_codguarcontper; ?>" readonly>
            <a href="javascript: ue_buscarcuentaguarderia('per');"><img  src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td><input name="txtcueconfid" type="hidden" id="txtcueconfid" value="<?php print $ls_con_cueconfid;?>" readonly>
            <a href="javascript: ue_buscarcuentacontable('FIDEICOMISO');"></a></td>
          <td height="22"><div align="right">Cta. Presup. Obrero </div></td>
          <td><input name="txtctaguarobr" type="text" id="txtctaguarobr" value="<?php print $ls_con_codguarcontobr; ?>" readonly>
            <a href="javascript: ue_buscarcuentaguarderia('obr');"><img  src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td>&nbsp;</td>
          <td height="22"><div align="right">Cta. Presup.Emp.Contrat </div></td>
          <td><input name="txtctaguarpercon" type="text" id="txtctaguarpercon" value="<?php print $ls_con_codguarcontpercon; ?>" readonly>
            <a href="javascript: ue_buscarcuentaguarderia('percon');"><img  src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
        </tr>
        <tr>
          <td height="22">&nbsp;</td>
          <td>&nbsp;</td>
          <td height="22"><div align="right">Cta. Presup.Obr.Contrat </div></td>
          <td><input name="txtctaguarobrcon" type="text" id="txtctaguarobrcon" value="<?php print $ls_con_codguarcontobrcon; ?>" readonly>
            <a href="javascript: ue_buscarcuentaguarderia('obrcon');"><img  src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a> </td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew"><div align="center">Par&aacute;metros</div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Ordenar Constantes </div></td>
          <td><div align="left">
              <select name="cmbordcons" id="cmbordcons">
                <option value="CODIGO" <?php print $la_est_ordcons[0]; ?>>C&oacute;digo</option>
                <option value="NOMBRE" <?php print $la_est_ordcons[1]; ?>>Nombre</option>
                <option value="APELLIDO" <?php print $la_est_ordcons[2]; ?>>Apellido</option>
                <option value="UNIDAD" <?php print $la_est_ordcons[3]; ?>>Unidad</option>
              </select>
          </div></td>
          <td><div align="right">Ordenar Conceptos </div></td>
          <td><div align="left">
              <select name="cmbordconc" id="cmbordconc">
                <option value="CODIGO" <?php print $la_est_ordconc[0]; ?>>C&oacute;digo</option>
                <option value="NOMBRE" <?php print $la_est_ordconc[1]; ?>>Nombre</option>
                <option value="APELLIDO" <?php print $la_est_ordconc[2]; ?>>Apellido</option>
                <option value="UNIDAD" <?php print $la_est_ordconc[3]; ?>>Unidad</option>
              </select>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Excluir personas suspendidas post calculos de los reportes </div></td>
          <td><div align="left">
            <input name="chkexcpersus" type="checkbox" class="sin-borde" id="chkexcpersus" value="1" <?php if($li_par_excpersus!="0"){print "checked";}?>>
          </div></td>
          <td><div align="right">No permitir personas repetidas ente n&oacute;minas NORMALES</div></td>
          <td><div align="left">
            <input name="chkperrep" type="checkbox" class="sin-borde" id="chkperrep" value="1" <?php if($li_par_perrep!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Fecha Tope Fin de a&ntilde;o </div></td>
          <td><div align="left">
            <input name="txtfecfinano" type="text" id="txtfecfinano" onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);" value="<?php print $ld_par_fecfinano;?>" maxlength="10" datepicker="true">
          </div></td>
          <td><div align="right">M&eacute;todo Calculo Prestaci&oacute;n Antiguedad </div></td>
          <td><div align="left">
              <select name="cmbmetcalfid" id="cmbmetcalfid">
                <option value="VERSION 2" <?php print $la_par_metcalfid[0]; ?>>VERSI&Oacute;N 2 </option>
                <option value="VERSION CONSEJO" <?php print $la_par_metcalfid[1]; ?>>VERSI&Oacute;N CONSEJO</option>
              </select>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo Concepto Sueldo Anterior </div></td>
          <td><input name="txtcodconcsuelant" type="text" id="txtcodconcsuelant" onKeyUp="javascript: ue_validarnumero(this);" value="<?php print $ls_par_concsuelant;?>" maxlength="10" readonly >
            <a href="javascript: ue_buscarconcepto('concsuelant');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></td>
          <td><div align="right">Configurar Prestamo </div></td>
          <td><select name="cmbconfpre" id="cmbconfpre">
              <option value="CUOTAS" <?php print $la_par_confpre[0]; ?>>CUOTAS</option>
              <option value="MONTO" <?php print $la_par_confpre[1]; ?>>MONTO</option>
          </select></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Cambiar la Unidad Administrativa cuando la n&oacute;mina sea con RAC </div></td>
          <td><div align="left">
              <input name="chkcamuniadm" type="checkbox" class="sin-borde" id="chkcamuniadm" value="1" <?php if($li_par_camuniadm!="0"){print "checked";}?>>
          </div></td>
          <td><div align="right">Cambiar el Paso y Grado cuando la n&oacute;mina sea con RAC</div></td>
          <td><div align="left">
              <input name="chkcampasogrado" type="checkbox" class="sin-borde" id="chkcampasogrado" value="1" <?php if($li_par_campasogrado!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Cambiar la Dedicaci&oacute;n y el Tipo de Personal cuando la n&oacute;mina sea con RAC </div></td>
          <td><div align="left">
              <input name="chkcamdedtipper" type="checkbox" class="sin-borde" id="chkcamdedtipper" value="1" <?php if($li_par_camdedtipper!="0"){print "checked";}?>>
          </div></td>
          <td height="22"><div align="right">Cambiar el sueldo cuando la n&oacute;mina de Obreros sea con RAC</div></td>
          <td><div align="left">
              <input name="chkcamsuerac" type="checkbox" class="sin-borde" id="chkcamsuerac" value="1" <?php if($li_par_campsuerac!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Incluir el personal automaticamente en el m&oacute;dulo de Beneficiarios </div></td>
          <td><div align="left">
              <input name="chkincperben" type="checkbox" class="sin-borde" id="chkincperben" value="1" onChange="javascript: ue_personalbeneficiario();" <?php if($li_par_incperben!="0"){print "checked";}?>>
          </div></td>
          <td><div align="right">Cuenta contable para los Beneficiarios </div></td>
          <td><div align="left">
              <input name="txtcueconben" type="text" id="txtcueconben" value="<?php print $ls_par_cueconben;?>" readonly>
          <a href="javascript: ue_buscarcuentacontablebene();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Implementar el c&oacute;digo &uacute;nico de RAC </div></td>
          <td><div align="left">
              <input name="chkcodunirac" type="checkbox" class="sin-borde" id="chkcodunirac" value="1" <?php if($li_par_codunirac!="0"){print "checked";}?>>
          </div></td>
          <td><div align="right">Compensaci&oacute;n automatica en los tabuladores del rac </div></td>
          <td><div align="left">
              <input name="chkcomautrac" type="checkbox" class="sin-borde" id="chkcomautrac" value="1" <?php if($li_par_comautrac!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Ajustar sueldo seg&uacute;n compensaci&oacute;n </div></td>
          <td><div align="left">
              <input name="chkajusuerac" type="checkbox" class="sin-borde" id="chkajusuerac" value="1" <?php if($li_par_ajusuerac!="0"){print "checked";}?>>
          </div></td>
          <td><div align="right">Modificar Datos Pensiones </div></td>
          <td><div align="left">
              <input name="chkmodpensiones" type="checkbox" class="sin-borde" id="chkmodpensiones" value="1" <?php if($li_par_modpensiones!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Validar Cuotas de Prestamos No mayores al 30% del Sueldo</div></td>
          <td><div align="left">
              <input name="chkvalporpre" type="checkbox" class="sin-borde" id="chkvalporpre" value="1" <?php if($li_par_valporpre!="0"){print "checked";}?>>
          </div></td>
          <td><div align="right">Permitir Alfanumericos en el C&oacute;digo de personal</div></td>
          <td><div align="left">
              <input name="chkalfnumcodper" type="checkbox" class="sin-borde" id="chkalfnumcodper" value="1" <?php if($li_par_alfnumcodper!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Validar Longitud de Cuenta Bancaria </div></td>
          <td><div align="left">
              <input name="chkvalloncueban" type="checkbox" class="sin-borde" id="chkvalloncueban" value="1" <?php if($li_par_valloncueban!="0"){print "checked";}?>>
            Longitud
            <input name="txtloncueban" type="text" id="txtloncueban" size="6" maxlength="2" value="<?php print $li_par_loncueban;?>">
          </div></td>
          <td height="22"><div align="right">No permitir m&uacute;ltiples Pr&eacute;stamos del mismo Tipo</div></td>
          <td><input name="chkprestamos" type="checkbox" class="sin-borde" id="chkprestamos" value="1" <?php if($li_prestamo!="0"){print "checked";}?>></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Permitir sobregiro en las Cuentas del Personal en el c&aacute;lculo de la n&oacute;mina</div></td>
          <td><div align="left">
              <input name="chkpersobregiro" type="checkbox" class="sin-borde" id="chkpersobregiro" value="1" <?php if($li_persobregiro!="0"){print "checked";}?>>
          </div></td>
          <td height="22"><div align="right">Permitir que el personal cobre monto cero </div></td>
          <td><input name="chkpercobmoncer" type="checkbox" class="sin-borde" id="chkpercobmoncer" value="1" <?php if($li_percobmoncer!="0"){print "checked";}?>></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Generar Pago Directo a Personal que cobra por cheques</div></td>
          <td><div align="left">
              <input name="chkgenpagperche" type="checkbox" class="sin-borde" id="chkgenpagperche" value="1" <?php if($li_chkgenpagperche!="0"){print "checked";}?>>
          </div></td>
          <td height="22"><div align="right">Mantener cuenta de banco</div></td>
          <td><div align="left">
              <input name="chkmancueban" type="checkbox" class="sin-borde" id="chkmancueban" value="1" <?php if($li_chkmancueban!="0"){print "checked";}?>>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="22"><div align="right">Banco </div></td>
          <td height="22" colspan="3"><div align="left">
              <input name="txtcodbanperche" type="text" id="txtcodbanperche"  style="text-align:center" value="<?php print $ls_codbanperche;?>" size="10" readonly>
              <a href="javascript:cat_bancos('pagdirche');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat?logo de Bancos"></a>
              <input name="txtdenbanperche" type="text" id="txtdenbanperche" size="65" class="sin-borde" readonly>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20"><div align="right">Cuenta </div></td>
          <td height="20" colspan="3"><div align="left">
              <input name="txtcuentaperche" type="text" id="txtcuentaperche"  style="text-align:center" value="<?php print $ls_cuenta_bancoperche;?>" size="25" maxlength="25" readonly>
              <a href="javascript:catalogo_cuentabancoperche();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat?logo de Bancos"></a>
              <input name="txtdenominacionperche" type="text" id="txtdenominacionperche" size="65" class="sin-borde" readonly>
          </div></td>
        </tr>
        <tr class="formato-blanco">
          <td height="20"><div align="right">Permitir caracteres alfanumericos en el codigo de cargo</div></td>
          <td height="20"><div align="left">
               <input name="chkpercargoalfa" type="checkbox" class="sin-borde" id="chkpercargoalfa" value="1" <?php if($li_chkpercargoalfa!="0"){print "checked";}?>>
               </div>
          </td>
          <td height="20">&nbsp;</td>
          <td height="20">&nbsp;</td>
        </tr>
        <tr>
          <td height="22" colspan="4" class="titulo-celdanew">IVSS</td>
        </tr>
        <tr>
          <td height="22"><div align="right">N&uacute;mero de Empresa IVSS </div></td>
          <td><div align="left">
              <label>
              <input name="txtnumempivss" type="text" id="txtnumempivss" value="<?php print $ls_ivss_numemp;?>" maxlength="9">
              </label>
          </div></td>
          <td height="22"><div align="right">M&eacute;todo de IVSS </div></td>
          <td><div align="left">
              <select name="cmbmetivss" id="cmbmetivss">
                <option value="SUELDO NORMAL" <?php print $la_ivss_metodo[0];?>>SUELDO BASICO</option>
                <option value="SUELDO INTEGRAL" <?php print $la_ivss_metodo[1];?>>
                  <?php if ($ls_sueint==""){print "SUELDO INTEGRAL"; } else { print (strtoupper($ls_sueint));}?>
                  </option>
              </select>
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">No tomar en cuenta n&oacute;minas especiales </div></td>
          <td><input name="chknomespivss" type="checkbox" class="sin-borde" id="chknomespivss" value="1" <?php if($li_ivss_nomesp!="0"){print "checked";}?>></td>
          <td height="22">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">Director de Talento Humano </div></td>
          <td><div align="left">
            <input name="txtdirtalhumivss" type="text" id="txtdirtalhumivss" value="<?php print $ls_ivss_dirtalhumivss;?>" size="50" maxlength="80">
          </div></td>
          <td height="22"> <div align="right">C&eacute;dula Director de Talento Humano </div></td>
          <td><input name="txtceddirtalhumivss" type="text" id="txtceddirtalhumivss" value="<?php print $ls_ivss_ceddirtalhumivss;?>" size="12" maxlength="10"></td>
        </tr>
        <tr class="titulo-celdanew">
          <td height="22" colspan="4">VIPLADIN</td>
          </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo de Organismo </div></td>
          <td><input name="txtcodorgvipladin" type="text" id="txtcodorgvipladin" value="<?php print $ls_codorgvipladin;?>" maxlength="20" onKeyUp="javascript: ue_validarnumero(this);"></td>
          <td height="22"><div align="right">Grupo</div></td>
          <td><input name="txtgrupovipladin" type="text" id="txtgrupovipladin" value="<?php print $ls_grupovipladin;?>" maxlength="20" onKeyUp="javascript: ue_validarnumero(this);"></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo Ubicaci&oacute;n Geografica </div></td>
          <td><input name="txtcodubivipladin" type="text" id="txtcodubivipladin" value="<?php print $ls_codubivipladin;?>" maxlength="20" onKeyUp="javascript: ue_validarnumero(this);"></td>
          <td height="22"><div align="right">Distrito</div></td>
          <td><input name="txtdistritovipladin" type="text" id="txtdistritovipladin" value="<?php print $ls_distritovipladin;?>" size="35" maxlength="50" onKeyUp="javascript: ue_validartexto(this);"></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Municipio</div></td>
          <td><input name="txtmunicipiovipladin" type="text" id="txtmunicipiovipladin" value="<?php print $ls_municipiovipladin;?>" size="35" maxlength="50" onKeyUp="javascript: ue_validartexto(this);"></td>
          <td height="22"><div align="right">Fecha de Vigencia</div></td>
          <td><input name="txtvigenciavipladin" type="text" id="txtvigenciavipladin" value="<?php print $ls_vigenciavipladin;?>" size="12" maxlength="10" onKeyUp="javascript: ue_validartexto(this);"></td>
        </tr>
        <tr class="titulo-celdanew">
          <td height="22" colspan="4" class="titulo-celdanew">Aporte - IPASME </td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo de Organismo </div></td>
          <td><div align="left">
              <label>
              <input name="txtcodorgipas" type="text" id="txtcodorgipas" value="<?php print $ls_ipas_codorgipas;?>" maxlength="3">
              </label>
          </div></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo Concepto de Ahorro </div></td>
          <td><div align="left">
              <label>
              <input name="txtcodconcahoipas" type="text" id="txtcodconcahoipas" value="<?php print $ls_ipas_codconcahoipas;?>" maxlength="10" readonly>
              </label>
            <a href="javascript: ue_buscarconcepto('cajaahorro');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div>
            <a href="javascript: ue_buscarconcepto();"></a></td>
          <td><div align="right">C&oacute;digo Concepto de Servicio Asistencial </div></td>
          <td><div align="left">
              <input name="txtcodconcseripas" type="text" id="txtcodconcseripas" value="<?php print $ls_ipas_codconcseripas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('servasi');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22" colspan="4"><div align="center"><span class="titulo-conect Estilo1">Conceptos para las Cobranzas </span></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Concepto Hipotecario (Especial) </div></td>
          <td><div align="left">
              <input name="txtconhipespipas" type="text" id="txtconhipespipas" value="<?php print $ls_ipas_conhipespipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conhipes');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Concepto Hipotecario (Refacci&oacute;n &oacute; Ampliaci&oacute;n) </div></td>
          <td><div align="left">
              <input name="txtconhipampipas" type="text" id="txtconhipampipas" value="<?php print $ls_ipas_conhipampipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conhipamp');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Concepto Hipotecario (Construcci&oacute;n) </div></td>
          <td><div align="left">
              <input name="txtconhipconipas" type="text" id="txtconhipconipas" value="<?php print $ls_ipas_conhipconipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conhipcon');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Concepto Hipotecario (Cancelar Hipoteca) </div></td>
          <td><div align="left">
              <input name="txtconhiphipipas" type="text" id="txtconhiphipipas" value="<?php print $ls_ipas_conhiphipipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conhiphip');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Concepto Hipotecario (L.P.H.) </div></td>
          <td><div align="left">
              <input name="txtconhiplphipas" type="text" id="txtconhiplphipas" value="<?php print $ls_ipas_conhiplphipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conhiplph');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Concepto Hipotecario (Adquirir Vivienda) </div></td>
          <td><div align="left">
              <input name="txtconhipvivipas" type="text" id="txtconhipvivipas" value="<?php print $ls_ipas_conhipvivipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conhipvivi');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Concepto Personal </div></td>
          <td><div align="left">
              <input name="txtconperipas" type="text" id="txtconperipas" value="<?php print $ls_ipas_conperipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conper');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Concepto Tur&iacute;sticos </div></td>
          <td><div align="left">
              <input name="txtconturipas" type="text" id="txtconturipas" value="<?php print $ls_ipas_conturipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conturi');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Concepto Proveduria </div></td>
          <td><div align="left">
              <input name="txtconproipas" type="text" id="txtconproipas" value="<?php print $ls_ipas_conproipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conpro');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Concepto Asistenciales </div></td>
          <td><div align="left">
              <input name="txtconasiipas" type="text" id="txtconasiipas" value="<?php print $ls_ipas_conasiipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('conasi');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Concepto Veh&iacute;culos </div></td>
          <td><div align="left">
              <input name="txtconvehipas" type="text" id="txtconvehipas" value="<?php print $ls_ipas_convehipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('convehi');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">Concepto Comerciales </div></td>
          <td><div align="left">
              <input name="txtconcomipas" type="text" id="txtconcomipas" value="<?php print $ls_ipas_concomipas;?>" maxlength="10" readonly>
          <a href="javascript: ue_buscarconcepto('concomi');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="20" colspan="4" class="titulo-celdanew"><div align="center">Aporte - Fondo de Pensiones de Jubilaciones </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo de Organismo </div></td>
          <td><div align="left">
              <input name="txtcodorgfpj" type="text" id="txtcodorgfpj" value="<?php print $ls_fpj_codorgfpj;?>" onKeyUp="javascript: ue_validarnumero(this);">
          </div></td>
          <td><div align="right">C&oacute;digo de Concepto FPJ</div></td>
          <td><div align="left">
              <input name="txtcodconcfpj" type="text" id="txtcodconcfpj" value="<?php print $ls_fpj_codconcfpj;?>" maxlength="21" onKeyUp="javascript: ue_validartexto(this);" >
          <a href="javascript: ue_buscarconcepto('concfpj');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">M&eacute;todo de FPJ</div></td>
          <td><div align="left">
              <select name="cmbmetfpj" id="cmbmetfpj">
                <option value="SUELDO NORMAL" <?php print $la_fpj_metfpj[0];?>>SUELDO BASICO</option>
                <option value="SUELDO INTEGRAL" <?php print $la_fpj_metfpj[1];?>>
                  <?php if ($ls_sueint==""){print "SUELDO INTEGRAL"; } else { print (strtoupper($ls_sueint));}?>
                  </option>
              </select>
          </div></td>
          <td height="22"><div align="right">Par&aacute;metros de FPJ      (Edad y a&ntilde;os Servicios) </div></td>
          <td><div align="left">
              <input name="chkparfpj" type="checkbox" class="sin-borde" id="chkparfpj" 
			value="1" <?php if($li_con_parfpj!="0"){print "checked";} ?> 
			onChange="javascript: ue_bloquear2();">
          </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">Edad (Personal Masculino)</div></td>
          <td><input name="txtedadM" type="text" id="txtedadM"		      
			   value="<?php print $ls_edadM;?>" size="6" maxlength="2"
			   <?php if($li_con_parfpj=="0"){print "disabled";} ?> ></td>
          <td height="22"><div align="right">Edad (Personal Femenino)</div></td>
          <td><input name="txtedadF" type="text" id="txtedadF"  
		      <?php if($li_con_parfpj=="0"){print "disabled";} ?>   
			  value="<?php print $ls_edadF;?>" size="6" maxlength="2" ></td>
        </tr>
        <tr>
          <td height="22"><div align="right">A&ntilde;os de Servicios (Minimos)</div></td>
          <td><input name="txtanoM" type="text" id="txtanoM"		      
			   value="<?php print $ls_anoM;?>" size="6" maxlength="2"
			   <?php if($li_con_parfpj=="0"){print "disabled";} ?> ></td>
          <td><div align="right">A&ntilde;os de Servicios (M&aacute;ximo)</div></td>
          <td><input name="txtanoT" type="text" id="txtanoT"		      
			   value="<?php print $ls_anoT; ?>" size="6" maxlength="2"
			   <?php if($li_con_parfpj!="0"){print "disabled";} ?> ></td>
        </tr>
        <tr class="titulo-celdanew">
          <td height="20" colspan="4"><div align="center">Aporte - R&eacute;gimen Prestacional de Vivienda y H&aacute;bitat </div></td>
        </tr>
        <tr>
          <td height="22"><div align="right">C&oacute;digo de Concepto RPVYH </div></td>
          <td><div align="left">
              <input name="txtcodconclph" type="text" id="txtcodconclph" value="<?php print $ls_lph_codconclph;?>" onKeyUp="javascript: ue_validartexto(this);" maxlength="21" >
          <a href="javascript: ue_buscarconcepto('conclph');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
          <td><div align="right">M&eacute;todo de RPVYH </div></td>
          <td><div align="left">
              <select name="cmbmetlph" id="cmbmetlph">
                <option value="SIN METODO" <?php print $la_lph_metlph[0];?>>SIN METODO</option>
                <option value="VIVIENDA" <?php print $la_lph_metlph[1];?>>VIVIENDA</option>
                <option value="CASA PROPIA" <?php print $la_lph_metlph[2];?>>CASA PROPIA</option>
                <option value="MERENAP" <?php print $la_lph_metlph[3];?>>MERENAP</option>
                <option value="MIRANDA" <?php print $la_lph_metlph[4];?>>MIRANDA</option>
                <option value="FONDO MUTUAL HABITACIONAL" <?php print $la_lph_metlph[5];?>>FONDO MUTUAL HABITACIONAL</option>
                <option value="BANESCO" <?php print $la_lph_metlph[6];?>>BANESCO</option>
                <option value="MI CASA EAP" <?php print $la_lph_metlph[7];?>>MI CASA EAP</option>
                <option value="CANARIAS" <?php print $la_lph_metlph[8];?>>CANARIAS</option>
                <option value="VENEZUELA" <?php print $la_lph_metlph[9];?>>VENEZUELA</option>
                <option value="DELSUR" <?php print $la_lph_metlph[10];?>>DELSUR</option>
                <option value="MERCANTIL" <?php print $la_lph_metlph[11];?>>MERCANTIL</option>
                <option value="CENTRAL" <?php print $la_lph_metlph[12];?>>CENTRAL</option>
                <option value="CAJA FAMILIA" <?php print $la_lph_metlph[13];?>>CAJA FAMILIA</option>
                <option value="FONDO_COMUN_EAP" <?php print $la_lph_metlph[14];?>>FONDO COM?N EAP</option>
                <option value="FONDO_COMUN_MRE" <?php print $la_lph_metlph[14];?>>FONDO COM?N MRE</option>
                <option value="BOD" <?php print $la_lph_metlph[15];?>>BOD</option>
                <option value="BANAVIH" <?php print $la_lph_metlph[16];?>>BANAVIH</option>
                <option value="BANAVIH2" <?php print $la_lph_metlph[17];?>>BANAVIH 2.0</option>
                <option value="BANAVIHSUNACRIP" <?php print $la_lph_metlph[18];?>>BANAVIH SUNACRIP</option>
		<option value="BANAVIH_FISCALIZACION" <?php print $la_lph_metlph[19];?>>BANAVIH FISCALIZACION</option>
              </select>
          </div></td>
        </tr>
  <td><div align="right" class="sin-borde3">Parametros Banavih 2.0 </div></td>
  <tr class="formato-blanco">
    <td height="22"><div align="right">Banco</div></td>
    <td height="22" colspan="3"><div align="left">
      <input name="txtcodban" type="text" id="txtcodban"  style="text-align:center" value="<?php print $ls_codban;?>" size="10" readonly>
      <a href="javascript:cat_bancos('config');"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat?logo de Bancos"></a>
      <input name="txtdenban" type="text" id="txtdenban" size="65" class="sin-borde" readonly>
    </div></td>
  </tr>
  <tr class="formato-blanco">
    <td height="20"><div align="right">Cuenta </div></td>
    <td height="20" colspan="3"><div align="left">
      <input name="txtcuenta" type="text" id="txtcuenta"  style="text-align:center" value="<?php print $ls_cuenta_banco;?>" size="25" maxlength="25" readonly>
      <a href="javascript:catalogo_cuentabanco();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Cat?logo de Bancos"></a>
      <input name="txtdenominacion" type="text" id="txtdenominacion" size="65" class="sin-borde" readonly>
    </div></td>
  </tr>
  <tr>
   <td height="22"><div align="right">Diferenciar Conceptos por Nomina </div></td>
   <td><div align="left">
	<input name="chkdifconpnom" type="checkbox" class="sin-borde" id="chkdifconpnom" value="1" <?php if($li_difconpnom!="0"){print "checked";}?>>
	  </div></td>
   <td height="22">&nbsp;</td>
   <td>&nbsp;</td>
  </tr>
  <tr class="formato-blanco">
    <td height="20"><div align="right">Conceptos de Sueldo Integral para Banavih </div></td>
    <td height="20" colspan="3"><p><img src="../shared/imagebank/mas.gif" width="9" height="17" border="0"> <a href="javascript:uf_selectconcepto('txtcodconcbanavih');"> Buscar Conceptos por lote</a></p>
        <p>
          <textarea name="txtcodconcbanavih" cols="100" readonly id="txtcodconcbanavih"><?php print $ls_codconcbanavih;?></textarea>
      </p></td>
  </tr>
  <tr class="formato-blanco">
    <td height="20" colspan="4" class="titulo-celdanew">Aporte - Fondo de jubilaci&oacute;n de la Tesoreria de Seguridad Social a partir del 2017 </td>
    </tr>
  <tr class="formato-blanco">
    <td height="20"><div align="right">Conceptos de Salario Base </div></td>
    <td height="20" colspan="3"><p><a href="javascript:uf_selectconcepto('txtcodconcsalbasfpj');">Buscar Conceptos por lote</a></p>
      <p>
        <textarea name="txtcodconcsalbasfpj" cols="100" readonly id="txtcodconcsalbasfpj"><?php print $ls_codconcsalbasfpj;?></textarea>
      </p></td>
  </tr>
  <tr class="formato-blanco">
    <td height="20"><div align="right">Conceptos de Comensaci&oacute;n por Antiguedad </div></td>
    <td height="20" colspan="3"><p><a href="javascript:uf_selectconcepto('txtcodconcantfpj');">Buscar Conceptos por lote</a></p>
      <p>
        <textarea name="txtcodconcantfpj" cols="100" readonly id="txtcodconcantfpj"><?php print $ls_codconcantfpj;?></textarea>
      </p></td>
  </tr>
  <tr class="formato-blanco">
    <td height="20"><div align="right">Conceptos de Compensaci&oacute;n por eficiencia </div></td>
    <td height="20" colspan="3"><p><a href="javascript:uf_selectconcepto('txtcodconcefifpj');">Buscar Conceptos por lote</a></p>
      <p>
        <textarea name="txtcodconcefifpj" cols="100" readonly id="txtcodconcefifpj"><?php print $ls_codconcefifpj;?></textarea>
      </p></td>
  </tr>
  <tr class="formato-blanco">
    <td height="20"><div align="right">Conceptos de Otras primas </div></td>
    <td height="20" colspan="3"><p><a href="javascript:uf_selectconcepto('txtcodconcotrprifpj');">Buscar Conceptos por lote</a></p>
      <p>
        <textarea name="txtcodconcotrprifpj" cols="100" readonly id="txtcodconcotrprifpj"><?php print $ls_codconcotrprifpj;?></textarea>
      </p></td>
  </tr>
  <tr class="formato-blanco">
    <td height="20">&nbsp;</td>
    <td height="20" colspan="3">&nbsp;</td>
  </tr>
  <tr>
    <td height="20" colspan="4" class="titulo-celdanew"><div align="center">Aporte - Plan de Ahorro </div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">C&oacute;digo de Concepto FPA </div></td>
    <td><div align="left">
      <input name="txtcodconcfpa" type="text" id="txtcodconcfpa" value="<?php print $ls_fpa_codconcfpa;?>" onKeyUp="javascript: ue_validartexto(this);" maxlength="21" >
      <a href="javascript: ue_buscarconcepto('concfpa');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
    <td><div align="right">M&eacute;todo de FPA </div></td>
    <td><div align="left">
      <select name="cmbmetfpa" id="cmbmetfpa">
        <option value="SIN METODO" <?php print $la_fpa_metfpa[0];?>>SIN METODO</option>
        <option value="VENEZUELA" <?php print $la_fpa_metfpa[1];?>>VENEZUELA</option>
        <option value="MERCANTIL" <?php print $la_fpa_metfpa[2];?>>MERCANTIL</option>
        <option value="CENTRAL" <?php print $la_fpa_metfpa[3];?>>CENTRAL BANCO UNIVERSAL</option>
		<option value="FONACIT" <?php print $la_fpa_metfpa[4];?>>FONACIT CAJA DE AHORRO</option>
		<option value="CAPREMINFRA" <?php print $la_fpa_metfpa[5];?>>CAPREMINFRA</option>
		<option value="GENERICO" <?php print $la_fpa_metfpa[6];?>>GENERICO</option>
      </select>
    </div></td>
  </tr>
  <tr>
    <td height="20" colspan="4" class="titulo-celdanew">Aporte  - Prestaci&oacute;n Antiguedad
      <div align="center"></div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">Antiguedad Complementaria </div></td>
    <td><div align="left">
      <input name="chkantcom" type="checkbox" class="sin-borde" id="chkantcom" value="1" <?php if($li_fps_antcom!="0"){print "checked";}?>>
    </div></td>
    <td><div align="right">Fracci&oacute;n Alicuota</div></td>
    <td><div align="left">
      <input name="chkfraali" type="checkbox" class="sin-borde" id="chkfraali" value="1" <?php if($li_fps_fraali!="0"){print "checked";}?>>
    </div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">Incluir Alicuota de Bono Vacacional en Alicutoa de Bono de Fin de A&ntilde;o </div></td>
    <td><input name="chkincvacagui" type="checkbox" class="sin-borde" id="chkincvacagui" value="1" <?php if($li_fps_incvacagui!="0"){print "checked";}?>></td>
    <td><div align="right">Aplicar la Asignaci&oacute;n extra al Sueldo Diario </div></td>
    <td><div align="left">
      <input name="chkintasiextra" type="checkbox" class="sin-borde" id="chkintasiextra" value="1" <?php if($li_fps_intasiextra!="0"){print "checked";}?>>
    </div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">M&eacute;todo de FPS</div></td>
    <td><div align="left">
      <select name="cmbmetfps" id="cmbmetfps">
        <option value="SIN METODO" <?php print $la_fps_metfps[0];?>>SIN METODO</option>
        <option value="CARIBE" <?php print $la_fps_metfps[1];?>>CARIBE</option>
        <option value="UNION" <?php print $la_fps_metfps[2];?>>UNION</option>
        <option value="MERCANTIL" <?php print $la_fps_metfps[3];?>>MERCANTIL</option>
        <option value="VENEZOLANO DE CREDITO" <?php print $la_fps_metfps[4];?>>VENEZOLANO DE CREDITO</option>
        <option value="BANCO DE VENEZUELA" <?php print $la_fps_metfps[5];?>>BANCO DE VENEZUELA</option>
        <option value="VENEZUELA" <?php print $la_fps_metfps[6];?>>VENEZUELA</option>
        <option value="BANCO PROVINCIAL" <?php print $la_fps_metfps[7];?>>BANCO PROVINCIAL</option>
        <option value="BANESCO" <?php print $la_fps_metfps[8];?>>BANESCO</option>
        <option value="CENTRAL BANCO UNIVERSAL" <?php print $la_fps_metfps[9];?>>CENTRAL BANCO UNIVERSAL</option>
        <option value="DEL SUR" <?php print $la_fps_metfps[10];?>>DEL SUR</option>
        <option value="BANCO INDUSTRIAL" <?php print $la_fps_metfps[11];?>>BANCO INDUSTRIA</option>
        <option value="CASA PROPIA" <?php print $la_fps_metfps[12];?>>BANCO CASA PROPIA</option>
        <option value="BANCO DEL TESORO" <?php print $la_fps_metfps[13];?>>BANCO DEL TESORO</option>
        <option value="BANCO AGRICOLA VENEZUELA" <?php print $la_fps_metfps[14];?>>BANCO AGRICOLA VENEZUELA</option>
        <option value="BANCO EXTERIOR" <?php print $la_fps_metfps[15];?>>BANCO EXTERIOR</option>
        <option value="BANCO NACIONAL DE CREDITO" <?php print $la_fps_metfps[16];?>>BANCO NACIONAL DE CREDITO</option>
        <option value="BOD" <?php print $la_fps_metfps[17];?>>BOD</option>
      </select>
    </div></td>
	<!-- /// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente. -->
    <td><div align="right">Incluir dias adicionales de Bono Vacacional </div></td>
    <td><div align="left">
      <input name="chkdiasadicionalesBV" type="checkbox" class="sin-borde" id="chkdiasadicionalesBV" value="1" <?php if($li_fps_diasadicionalesBV!="0"){print "checked";}?>>
    </div></td>
	<!-- /// Agregado por Ofimatica de Venezuela el 02-06-2011, para el manejo o no de los dias adicionales de Bono Vacacional, obligatorio segun la LOT y su reglamente. -->
  </tr>
  <tr>
    <td height="22"><div align="right">Calcular Intereses Prestaci&oacute;n Antiguedad</div></td>
    <td><div align="left">
      <input name="chkcalintpreant" type="checkbox" class="sin-borde" id="chkcalintpreant" value="1" <?php if($li_fps_calintpreant!="0"){print "checked";}?> onChange="javascript: activar_metodo_fps();">
    </div></td>
    <td><div align="right">Calcular Intereses solo para el personal configurado</div></td>
    <td><div align="left">
      <input name="chkcalintpercon" type="checkbox" class="sin-borde" id="chkcalintpercon" value="1" <?php if($li_fps_calintpercon!="0"){print "checked";}?> onChange="javascript: verificar_intereses();">
    </div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">% M&aacute;ximoAnticipo Prestaciones Sociales </div></td>
    <td><div align="left">
       <input name="txtpormaxant" type="text" id="txtpormaxant" size="4" maxlength="3" onKeyPress="return keyRestrict(event,'1234567890');" value="<?php print $li_fps_pormaxant;?>">
    %</div></td>
    <td><div align="right">Tipo de Documento para el Anticipo </div></td>
    <td><div align="left">
      <input name="txttipdocant" type="text" id="txttipdocant" value="<?php print $ls_fps_tipdocant;?>" readonly>
      <a href="javascript: ue_buscartipodocumento('ANTICIPO');"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a></div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">Calcular solo a personas activas en el mes aunque tengan calculos en la n?mina</div></td>
    <td><div align="left">
      <input name="chkcalperact" type="checkbox" class="sin-borde" id="chkcalperact" value="1" <?php if($li_fps_calperact!="0"){print "checked";}?>>
    </div></td>
    <td><div align="right">Tomar acumulado integral para d&iacute;as adicionales </div></td>
    <td><div align="left">
      <input name="chkacuintdiaadi" type="checkbox" class="sin-borde" id="chkacuintdiaadi" value="1" <?php if($li_fps_acuintdiaadi!="0"){print "checked";}?>>
    </div></td>
	</tr>
  <tr>
    <td height="22"><div align="right">Prestaciones sociales a partir del a&ntilde;o 97 para dias adicionales </div></td>
    <td><div align="left">
      <input name="chkpresocdiaadi" type="checkbox" class="sin-borde" id="chkpresocdiaadi" value="1" <?php if($li_fps_presocdiaadi!="0"){print "checked";}?>>
    </div></td>
	<td><div align="right">M&eacute;todo para calcular la alcuota del Bono vacacional </div></td>
	<td><div align="right">
	  <select name="cmbmetcalalibonvac" id="cmbmetcalalibonvac">
        <option value="INTEGRAL" <?php print $la_fps_metcalalibonvac[0];?>>SALARIO INTEGRAL</option>
        <option value="NORMAL" <?php print $la_fps_metcalalibonvac[1];?>>SALARIO NORMAL</option>
        <option value="VACACION" <?php print $la_fps_metcalalibonvac[2];?>>SALARIO INTEGRAL DE VACACIONES</option>
      </select>
	</div></td>
    </tr>
  <tr>
    <td height="22"><div align="right">Antiguedad complementaria a partir del 1er a&ntilde;o</div> </td>
    <td><div align="left">
      <input name="chkantprimeranio" type="checkbox" class="sin-borde" id="chkantprimeranio" value="1" <?php if($li_fps_antprimeranio!="0"){print "checked";}?>>
    </div></td>
    <td><div align="right">Forma de C&aacute;lculo  </div></td>
    <td><div align="left">
      <select name="cmbforcalpres" id="cmbforcalpres">
        <option value="0" <?php print $la_fps_forcalpres[0];?>>TRIMESTRAL FECHA INGRESO</option>
        <option value="1" <?php print $la_fps_forcalpres[1];?>>TRIMESTRAL FIJO</option>
        <option value="2" <?php print $la_fps_forcalpres[2];?>>MENSUAL</option>
      </select>
    </div></td>
  </tr>
  <tr>
    <td rowspan="5"><div align="right">Al Contabilizar Unificar en una sola estructura presupuestaria</div></td>
    <td rowspan="5"><div align="left">
      <input name="chkuniestpre" type="checkbox" class="sin-borde" id="chkuniestpre" value="1" <?php if($li_fps_uniestpre!="0"){print "checked";}?> onChange="javascript:limpiar_estructura();">
    </div></td>
    <td height="22"><div align="right"><?php print $ls_nomestpro1;?></div></td>
    <td>				 
		<div align="left">
            <input name="txtcodestpro1" type="text" id="txtcodestpro1" value="<?php print $ls_fps_codestpro1;?>" size="<?php print $ls_loncodestpro1+10; ?>" maxlength="<?php print $ls_loncodestpro1; ?>" readonly>
            <a href="javascript:ue_estructura1();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
            <input name="txtestcla" type="hidden" id="txtestcla" size="2" value="<?php print $ls_fps_estcla;?>">
		</div>
	</td>
  </tr>
  <tr>
    <td height="22"><div align="right"><?php print $ls_nomestpro2;?></div></td>
    <td>				 
		<div align="left">
            <input name="txtcodestpro2" type="text" id="txtcodestpro2" value="<?php print $ls_fps_codestpro2;?>" size="<?php print $ls_loncodestpro2+10; ?>" maxlength="<?php print $ls_loncodestpro2; ?>" readonly>
            <a href="javascript:ue_estructura2();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
		</div>
	</td>
  </tr>
  <tr>
    <td height="22"><div align="right"><?php print $ls_nomestpro3;?></div></td>
    <td>				
		<div align="left">
            <input name="txtcodestpro3" type="text" id="txtcodestpro3" value="<?php print $ls_fps_codestpro3;?>" size="<?php print $ls_loncodestpro3+10; ?>" maxlength="<?php print $ls_loncodestpro3; ?>" readonly>
            <a href="javascript:ue_estructura3();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
		</div>
	</td>
  </tr>
  <tr>
    <td height="22"><div align="right"><?php print $ls_nomestpro4;?></div></td>
    <td>				 
		<div align="left">
            <input name="txtcodestpro4" type="text" id="txtcodestpro4" value="<?php print $ls_fps_codestpro4;?>" size="<?php print $ls_loncodestpro4+10; ?>" maxlength="<?php print $ls_loncodestpro4; ?>" readonly>
            <a href="javascript:ue_estructura4();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
		</div>
	</td>
  </tr>
  <tr>
    <td height="22"><div align="right"><?php print $ls_nomestpro5;?></div></td>
    <td>				
		<div align="left">
            <input name="txtcodestpro5" type="text" id="txtcodestpro5" value="<?php print $ls_fps_codestpro5;?>" size="<?php print $ls_loncodestpro5+10; ?>" maxlength="<?php print $ls_loncodestpro5; ?>" readonly>
            <a href="javascript:ue_estructura5();"><img src="../shared/imagebank/tools15/buscar.gif" width="15" height="15" border="0" alt="Buscar"></a>
		</div>
	</td>
  </tr>
   <tr class="titulo-celdanew">
          <td height="20" colspan="4"><div align="center">Salario normal para el calculo del deposito de garantia y vacaciones</div></td>
        </tr>
        <tr>
          <td width="150" height="22"><div align="right">Dep&oacute;sito (15 d&iacute;as) </div></td>
          <td>
            <div align="right">
              <select name="cmbdepsalnorqui" id="cmbdepsalnorqui">
                <option value="0" <?php print $la_depsalnorqui[0]; ?>>Promedio mensual por conceptos variables</option>
                <option value="1" <?php print $la_depsalnorqui[1]; ?>>Promedio mensual integral</option>
                <option value="2" <?php print $la_depsalnorqui[2]; ?>>Ultimo mes efectivo</option>
                <option value="3" <?php print $la_depsalnorqui[3]; ?>>Ultima quincena efectiva</option>
              </select>
                </div></td></tr>
		<tr>
          <td width="150" height="22"><div align="right">Dep&oacute;sito (D&iacute;as adicionales) </div></td>
          <td>
            <div align="right">
              <select name="cmbdepsalnoradi" id="cmbdepsalnoradi">
                <option value="0" <?php print $la_depsalnoradi[0]; ?>>Promedio mensual por conceptos variables</option>
                <option value="1" <?php print $la_depsalnoradi[1]; ?>>Promedio mensual integral</option>
                <option value="2" <?php print $la_depsalnoradi[2]; ?>>Ultimo mes efectivo</option>
                <option value="3" <?php print $la_depsalnoradi[3]; ?>>Ultima quincena efectiva</option>
              </select>
                </div></td></tr>
		<tr>
          <td width="150" height="22"><div align="right">Vacaciones </div></td>
          <td>
            <div align="right">
              <select name="cmbdepsalnorvac" id="cmbdepsalnorvac">
                <option value="0" <?php print $la_depsalnorvac[0]; ?>>Promedio mensual por conceptos variables</option>
                <option value="1" <?php print $la_depsalnorvac[1]; ?>>Promedio mensual integral</option>
                <option value="2" <?php print $la_depsalnorvac[2]; ?>>Ultimo mes efectivo</option>
                <option value="3" <?php print $la_depsalnorvac[3]; ?>>Ultima quincena efectiva</option>
              </select>
                </div></td></tr>  
  <tr class="titulo-celdanew">
    <td height="22" colspan="4"><div align="center">Mantenimiento</div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">Cuentas de Conceptos </div></td>
    <td><div align="left">
      <input name="txtcueconc" type="text" id="txtcueconc" value="<?php print $ls_man_cueconc;?>" onKeyPress="return keyRestrict(event,'1234567890'+',');">
    </div></td>
    <td><div align="right">Activar Bloqueo de F&oacute;rmula de Conceptos</div></td>
    <td><div align="left"><a href="javascript: ue_buscarcuentacaja();"></a>
            <input name="chkactblofor" type="checkbox" class="sin-borde" id="chkactblofor" value="1" <?php if($li_man_actblofor!="0"){print "checked";}?>>
    </div></td>
  </tr>
  <tr>
    <td height="22"><div align="right">M&eacute;todo Resumen Contable </div></td>
    <td><div align="left">
      <select name="cmbmetrescon" id="cmbmetrescon">
        <option value="SIN METODO" <?php print $la_man_metrescon[0];?>>SIN METODO</option>
        <option value="METODO CTA_ABONO" <?php print $la_man_metrescon[1];?>>METODO CTA_ABONO</option>
      </select>
    </div></td>
    <td><div align="right"></div></td>
    <td><div align="left"></div></td>
  </tr>
  <tr>
    <td height="22" colspan="2"><div align="center">
      <input name="btnrepsubnomina" type="button" class="boton" id="btnrepsubnomina" value="Reparar Subn&oacute;minas"  style="width: 180px;" onClick="javascript: ue_repararsubnominas();" >
    </div></td>
    <td colspan="2"><div align="center">
      <input name="btnrepconceptopersonal" type="button" class="boton" id="btnrepconceptopersonal" style="width: 180px;" onClick="javascript: ue_repararconceptopersonal();" value="Reparar Concepto-Personal" >
    </div></td>
  </tr>
  <tr>
    <td height="22" colspan="2"><div align="center">
      <input name="btnrecsueldointegral" type="button" class="boton" id="btnrecsueldointegral" style="width: 180px;" value="<?php if ($ls_sueint==""){print "Recalcular Sueldo Integral"; } else { print "Recalcular ".$ls_sueint;}?>" onClick="javascript: ue_recalcularsueldointegral();" >
    </div></td>
    <td colspan="2"><div align="center">
      <input name="btnmanhistoricos" type="button" class="boton" id="btnmanhistoricos"style="width: 180px;" onClick="javascript: ue_mantenimientohistoricos();" value="Mantenimiento Hist&oacute;ricos" >
    </div></td>
  </tr>
  <tr>
    <td height="22" colspan="2"><div align="center">
      <input name="btnrepacuconc" type="button" class="boton" id="btnrepacuconc" style="width: 180px;" value="Reparar Acumulado Conceptos" onClick="javascript: ue_repararacumuladoconceptos();" >
    </div></td>
    <td colspan="2"><div align="center">
      <input name="btnrecconceptosfijo" type="button" class="boton" id="btnrecconceptosfijo" style="width: 220px;" value="Recalcular Conceptos Fijos y variables" onClick="javascript: ue_recalcularconceptos();" >
    </div></td>
  </tr>
  <tr>
    <td><div align="right"></div></td>
    <td colspan="3"><div align="left">
      <input name="operacion" type="hidden" id="operacion">
      <input name="tipo" type="hidden" id="tipo" value="">
    </div></td>
  </tr>
      </table>
      <p>&nbsp;</p></td>
  </tr>
</table>
</form>
<p>&nbsp;</p>
</body>
<script >
activar_metodo_fps();
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
function ue_guardar()
{
	f=document.form1;
	li_cambiar=f.cambiar.value;
	ls_sueint=f.txtsueint.value;
	if(li_cambiar==1)
	{
		if ((f.chksueint.checked)&&(ls_sueint==""))
		{
			alert ('Seleccion? la opci?n Cambiar denominaci?n Sueldo Integral. Debe ingresar la nueva denominaci?n');
		}
		else if ((f.chkgenrecdocpagper.checked)&&(f.txttipdocpagper.value==""))
		{
			alert('Debe seleccionar el Tipo de Documento para el Pago del Personal');
		}
		else
		{
                        mostrar('transferir');
			f=document.form1;
			f.operacion.value="GUARDAR";
			f.action="sigesp_snorh_p_configuracion.php";
			f.submit();
		}
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function ue_cerrar()
{
	location.href="sigespwindow_blank.php";
}

function ue_repararsubnominas()
{
        mostrar('transferir');
	f=document.form1;
	f.operacion.value="REPARARSUBNOMINAS";
	f.action="sigesp_snorh_p_configuracion.php";
	f.submit();
}

function ue_repararconceptopersonal()
{
        mostrar('transferir');
	f=document.form1;
	f.operacion.value="REPARARCONCEPTOPERSONAL";
	f.action="sigesp_snorh_p_configuracion.php";
	f.submit();
}

function ue_recalcularsueldointegral()
{
        mostrar('transferir');
	f=document.form1;
	f.operacion.value="RECALCULARSUELDOINTEGRAL";
	f.action="sigesp_snorh_p_configuracion.php";
	f.submit();
}

function ue_mantenimientohistoricos()
{
        mostrar('transferir');
	f=document.form1;
	f.operacion.value="MANTENIMIENTOHISTORICOS";
	f.action="sigesp_snorh_p_configuracion.php";
	f.submit();
}

function ue_repararacumuladoconceptos()
{
	if(confirm("?Este proceso actualizar? todos los acumulados de los Conceptos seg?n el c?lculo de la n?mina. Lo desea ejecutar?"))
	{
                mostrar('transferir');
		f=document.form1;
		f.operacion.value="REPARARACUMULADOCONCEPTOS";
		f.action="sigesp_snorh_p_configuracion.php";
		f.submit();
	}
}
function ue_recalcularconceptos()
{
        mostrar('transferir');
	f=document.form1;
	f.operacion.value="RECALCULARCONCEPTOS";
	f.action="sigesp_snorh_p_configuracion.php";
	f.submit();
}

function ue_buscarcuentacontable(tipo)
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		if(tipo=="CONFIGURACION")
		{
			if(f.chkgenrecdoc.checked==false)
			{
				consue=ue_validarvacio(f.cmbconsue.value);
				if(consue=="OC")
				{
					window.open("sigesp_sno_cat_cuentacontable.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
				}
			}
		}
		else
		{
			if(f.chkrecdocfid.checked==false)
			{
				window.open("sigesp_sno_cat_cuentacontable.php?tipo="+tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
	}
}

function ue_buscarcuentacontablebene()
{
	f=document.form1;
	if(f.chkincperben.checked==true)
	{
		window.open("sigesp_sno_cat_cuentacontable.php?tipo=CONFIGURACIONPARAMETRO","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
}

function ue_buscardestino()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		descon=ue_validarvacio(f.cmbdescon.value);
		if(descon!="")
		{
			if(descon=="P")
			{
				window.open("sigesp_catdinamic_prove.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
			else
			{
				window.open("sigesp_catdinamic_bene.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}	
		}
		else
		{
			alert("Debe seleccionar un destino de Contabilizaci?n.");
		}
	}
}

function ue_limpiar()
{
	f=document.form1;
	f.txtcodproben.value="";
	f.txtnombre.value="";
}

function ue_bloquear()
{
	f=document.form1;
	if(f.chkparnom.checked)
	{
		f.cmbconsue.disabled=true;
		f.cmbconapo.disabled=true;
		f.chkagrcon.disabled=true;
		f.chkgennotdeb.disabled=true;
		f.cmbdescon.disabled=true;
		f.chkgenrecdoc.disabled=true;
		f.chkgenrecdocapo.disabled=true;
		f.txttipdocnom.disabled=true;
		f.txttipdocapo.disabled=true;
		f.cmbconfidnom.disabled=true;
		f.chkrecdocfid.disabled=true;
		f.txttipdocfid.disabled=true;
		f.txtcueconfid.disabled=true;
		f.txtcodbenfid.disabled=true;
		f.chkgenrecdocpagper.disabled=true;
		f.txttipdocpagper.disabled=true;
		f.chkestctaalt.disabled=true;
		
		
	}
	else
	{
		f.cmbconsue.disabled=false;
		f.cmbconapo.disabled=false;
		f.chkagrcon.disabled=false;
		f.chkgennotdeb.disabled=false;
		f.cmbdescon.disabled=false;
		f.chkgenrecdoc.disabled=false;
		f.chkgenrecdocapo.disabled=false;
		f.txttipdocnom.disabled=false;
		f.txttipdocapo.disabled=false;
		f.cmbconfidnom.disabled=false;
		f.chkrecdocfid.disabled=false;
		f.txttipdocfid.disabled=false;
		f.txtcueconfid.disabled=false;
		f.txtcodbenfid.disabled=false;
		f.chkgenrecdocpagper.disabled=false;
		f.txttipdocpagper.disabled=false;
		f.chkestctaalt.disabled=false;
	}
}

function ue_bloqueardesincorpora()
{
	f=document.form1;
	if(f.chkvacdesincorporar.checked)
	{
		f.chkpresalvacper.disabled=false;
	}
	else
	{
		f.chkpresalvacper.disabled=true;
	}
}

function ue_bloquear2()
{
	f=document.form1;
	if(f.chkparfpj.checked)
	{
	    f.txtedadM.disabled="";
		f.txtedadM.disabled=false;
		f.txtedadF.disabled="";
		f.txtedadF.disabled=false;
		f.txtanoM.disabled="";
		f.txtanoM.disabled=false;
		f.txtanoT.disabled="";
		f.txtanoT.disabled=true;
		f.txtanoT.value=0;
	}
	else
	{
	    f.txtedadM.disabled="";
		f.txtedadM.disabled=true;
		f.txtedadM.value=0;
		f.txtedadF.disabled="";
		f.txtedadF.disabled=true;
		f.txtedadF.value=0;
		f.txtanoM.disabled="";
		f.txtanoM.disabled=true;
		f.txtanoM.value=0;
		f.txtanoT.disabled="";
		f.txtanoT.disabled=false;
	}
	
}
function ue_buscarcuentacaja()
{
	window.open("sigesp_sno_cat_cuentacontable.php?tipo=CONFIGURACIONCAJA","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_contabilizacionnomina()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		f.chkgenrecdoc.checked=false;
		f.txttipdocnom.value="";
		f.txtcuecon.value="";
		f.chkgennotdeb.checked=false;
		f.chkrecdoccaunom.checked=false;
		f.txttipdoccaunom.value="";		
	}
}

function ue_recepcionnomina()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		consulnom=ue_validarvacio(f.cmbconsue.value);
		if((consulnom!="OC"))
		{
			f.chkgenrecdoc.checked=false;
		}
		else
		{
			f.txttipdocnom.value="";
			f.txtcuecon.value="";
		}
	}
}

function ue_recepcioncausanomina()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		consulnom=ue_validarvacio(f.cmbconsue.value);
		if((consulnom!="O"))
		{
			f.chkrecdoccaunom.checked=false;
		}
		else
		{
			f.txttipdoccaunom.value="";
		}
	}
}

function ue_buscartipodocumento(tipo)
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		if(tipo=="NOMINA")
		{
			if(f.chkgenrecdoc.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
		if(tipo=="APORTE")
		{
			if(f.chkgenrecdocapo.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
		if(tipo=="FIDEICOMISO")
		{
			if(f.chkrecdocfid.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
		if(tipo=="PAGOPERSONAL")
		{
			if(f.chkgenrecdocpagper.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
		if(tipo=="GUARDERIA")
		{
			if(f.chkrecdocguar.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
		if(tipo=="CAUSADO")
		{
			if(f.chkrecdoccaunom.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}		
	}
	else
	{
		if(tipo=="ANTICIPO")
		{
			if(f.chkcalintpreant.checked)
			{
				window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
		if(tipo=="GUARDERIA")
		{
			if(f.chkrecdocguar.checked)
			{
					window.open("sigesp_snorh_cat_tipodocumento.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
			}
		}
	}
}

function ue_notadebito()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		consulnom=ue_validarvacio(f.cmbconsue.value);
		if((consulnom=="OCP")||(consulnom=="CP"))
		{
			//f.chkgennotdeb.checked=true;
		}
		else
		{
			f.chkgennotdeb.checked=false;	
		}
	}
}

function ue_contabilizacionaportes()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		f.chkgenrecdocapo.checked=false;
		f.txttipdocapo.value="";
	}
}

function ue_recepcionaportes()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		conaponom=ue_validarvacio(f.cmbconapo.value);
		if((conaponom!="OC"))
		{
			f.chkgenrecdocapo.checked=false;
			f.txttipdocapo.value="";
		}
		else
		{
			f.txttipdocapo.value="";
		}
	}
}

function ue_personalbeneficiario()
{
	f=document.form1;
	if(f.chkincperben.checked==false)
	{
		f.txtcueconben.value="";
	}
}


function ue_contabilizacionfideicomiso()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		f.chkrecdocfid.checked=false;
		f.txttipdocfid.value="";
	}
}

function ue_recepcionfideicomiso()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		confidnom=ue_validarvacio(f.cmbconfidnom.value);
		if((confidnom!="OC"))
		{
			f.chkrecdocfid.checked=false;
		}
		else
		{
			f.txttipdocfid.value="";
			f.txtcueconfid.value="";
		}
	}
}

function ue_recepcionguarderia()
{
	f=document.form1;
	if(f.chkrecdocguar.checked==false)
	{
		f.txttipdocguar.value="";
		f.txtctaguarper.value="";
		f.txtctaguarobr.value="";
		f.txtctaguarpercon.value="";
		f.txtctaguarobrcon.value="";
	}
}

function ue_recepcionbeneficiarioguarderia()
{
	f=document.form1;
	if(f.chkrecdocguar.checked==false)
	{
		alert('Debe configurar Generar Recepci?n de Documentos a Guarder?as.');
		f.chkbenrecdocgua.checked=false;
	}
}

function ue_buscarbeneficiario()
{
	f=document.form1;
	if(f.chkparnom.checked==false)
	{
		window.open("sigesp_catdinamic_bene.php?tipo=FIDEICOMISO","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
}

function ue_buscarcuentaguarderia(tipo)
{
	window.open("sigesp_sno_cat_cuentapresupuesto.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function ue_buscarconcepto(tipo)
{
	window.open("sigesp_sno_cat_concepto.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function cat_bancos(tipo)
{
	window.open("sigesp_snorh_cat_banco.php?tipo="+tipo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function catalogo_cuentabanco()
{
	f=document.form1;
	if(f.txtcodban.value!="")
	{
		window.open("sigesp_snorh_cat_cuentabanco.php?codban="+f.txtcodban.value+"&tipo=config","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un banco.");
	}
}

function catalogo_cuentabancoperche()
{
	f=document.form1;
	if(f.txtcodbanperche.value!="")
	{
		window.open("sigesp_snorh_cat_cuentabanco.php?codban="+f.txtcodbanperche.value+"&tipo=pagdirche","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
	}
	else
	{
		alert("Debe seleccionar un banco.");
	}
}

function activar_denominacion()
{
	f=document.form1;
	if(f.chksueint.checked==true)
	{
		f.txtsueint.readOnly=false;
		
	}
	else
	{
		f.txtsueint.value="";
		f.txtsueint.readOnly=true;
	}
}

function ue_chequear_nomina_beneficiario()
{
	f=document.form1;
	if(((f.cmbconsue.value!="OC")||(f.chkgenrecdoc.checked==false))&&(f.chkestctaalt.checked))
	{
		alert("Esta Opci?n es valida solo para N?minas Compromete y Causa que Generen Recepci?n de Documento.");
		f.chkestctaalt.checked=false;
	}
}

function uf_selectconcepto(campo)
{   
	window.open("sigesp_snorh_sel_catconcepto.php?campo="+campo+"","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,left=50,top=50,location=no,resizable=no");
}

function activar_metodo_fps()
{
	f=document.form1;
	if(f.chkcalintpreant.checked==true)
	{
		f.cmbmetfps.disabled=true;
	}
	else
	{
		f.chkcalintpercon.checked=false;
		f.cmbmetfps.disabled=false;
		f.txttipdocant.value="";
	}
}

function verificar_intereses()
{
	f=document.form1;
	if(f.chkcalintpreant.checked==false)
	{
		f.chkcalintpercon.checked=false;
	}
}

function mostrar(nombreCapa)
{
	capa= document.getElementById(nombreCapa) ;
	capa.style.visibility="visible"; 
} 

function limpiar_estructura()
{
	f=document.form1;
	if(f.chkuniestpre.checked==false)
	{
		f.txtcodestpro1.value="";
		f.txtestcla.value="";
		f.txtcodestpro2.value="";
		f.txtcodestpro3.value="";
		f.txtcodestpro4.value="";
		f.txtcodestpro5.value="";
	}
}

function ue_estructura1()
{
	f=document.form1;
	if(f.chkuniestpre.checked)
	{
	   window.open("sigesp_snorh_cat_estpre1.php?tipo=config","_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Debe Marcar Unficar Estructura Presupuestaria");
	}
}

function ue_estructura2()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=" ";
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!=""))
	{
		pagina="sigesp_snorh_cat_estpre2.php?tipo=config&codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&estcla1="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura nivel 1");
	}
}

function ue_estructura3()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=" ";
	codestpro2=f.txtcodestpro2.value;
	denestpro2=" ";
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!="")&&(codestpro2!="")&&(denestpro2!=""))
	{
    	pagina="sigesp_snorh_cat_estpre3.php?tipo=config&codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&codestpro2="+codestpro2+"&denestpro2="+denestpro2+"&estcla2="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura de nivel Anterior");
	}
}

function ue_estructura4()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=" ";
	codestpro2=f.txtcodestpro2.value;
	denestpro2=" ";
	codestpro3=f.txtcodestpro3.value;
	denestpro3=" ";
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!="")&&(codestpro2!="")&&(denestpro2!="")&&(codestpro3!="")&&(denestpro3!=""))
	{
    	pagina="sigesp_snorh_cat_estpre4.php?tipo=config&codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&codestpro2="+codestpro2+"&denestpro2="+denestpro2+"&codestpro3="+codestpro3+"&denestpro3="+denestpro3+"&estcla3="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura de nivel Anterior");
	}
}

function ue_estructura5()
{
	f=document.form1;
	codestpro1=f.txtcodestpro1.value;
	denestpro1=" ";
	codestpro2=f.txtcodestpro2.value;
	denestpro2=" ";
	codestpro3=f.txtcodestpro3.value;
	denestpro3=" ";
	codestpro4=f.txtcodestpro4.value;
	denestpro4=" ";
	estcla=f.txtestcla.value;
	if((codestpro1!="")&&(denestpro1!="")&&(codestpro2!="")&&(denestpro2!="")&&(codestpro3!="")&&(denestpro3!="")&&(codestpro4!="")&&(denestpro4!=""))
	{
    	pagina="sigesp_snorh_cat_estpre5.php?tipo=config&codestpro1="+codestpro1+"&denestpro1="+denestpro1+"&codestpro2="+codestpro2+"&denestpro2="+denestpro2+"&codestpro3="+codestpro3+"&denestpro3="+denestpro3+"&codestpro4="+codestpro4+"&denestpro4="+denestpro4+"&estcla4="+estcla;
		window.open(pagina,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=530,height=400,resizable=yes,location=no");
	}
	else
	{
		alert("Seleccione la Estructura de nivel Anterior");
	}
}

</script> 
</html>