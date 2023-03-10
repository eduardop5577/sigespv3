<?php
/***********************************************************************************
* @fecha de modificacion: 22/08/2022, para la version de php 8.1 
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
    $li_estmodest    =  $_SESSION["la_empresa"]["estmodest"];
	require_once("class_folder/class_funciones_soc.php");
	$io_fun_soc=new class_funciones_soc();
	$ls_permisos="";
	$la_seguridad=Array();
	$la_permisos=Array();
	$arrResultado = $io_fun_soc->uf_load_seguridad("SOC","sigesp_soc_p_registro_orden_compra.php",$ls_permisos,$la_seguridad,$la_permisos);
	$ls_permisos = $arrResultado['as_permisos'];
	$la_seguridad = $arrResultado['aa_seguridad'];
	$la_permisos = $arrResultado['aa_permisos'];

	$ls_reporte=$io_fun_soc->uf_select_config("SOC","REPORTE","FORMATO_SOC","sigesp_soc_rfs_registro_orden_compra.php","C");

		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
        $io_id_process_soc= new sigesp_c_generar_consecutivo();
	//////////////////////////////////////////////         SEGURIDAD               ///////////////////////////////////////////////
	$li_diasem = date('w');
	switch ($li_diasem)
	{
	  case '0': $ls_diasem='Domingo';
	  break;
	  case '1': $ls_diasem='Lunes';
	  break;
	  case '2': $ls_diasem='Martes';
	  break;
	  case '3': $ls_diasem='Mi&eacute;rcoles';
	  break;
	  case '4': $ls_diasem='Jueves';
	  break;
	  case '5': $ls_diasem='Viernes';
	  break;
	  case '6': $ls_diasem='S&aacute;bado';
	  break;
	 }

   //------------------------------------------------------------------------------------------------------------------------------
   function uf_limpiarvariables()
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_limpiarvariables
		//		   Access: private
		//	  Description: Funci?n que limpia todas las variables necesarias en la p?gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 15/04/2007					Fecha ?ltima Modificaci?n :
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $ls_estatus,$ld_fecordcom,$ls_numordcom,$ls_numordcomser,$ls_numordcombie,$ls_tipordcom,$ls_disbie,$ls_disser;
		global $ls_tipsol,$ls_codprov,$ls_coduniadm,$ls_denuniadm,$ls_codfuefin,$ls_denfuefin,$ls_forpag,$ld_antpag,$ls_rb_nacional;
		global $ls_rb_exterior,$ls_concom,$ls_codtipmod,$ls_denmodcla,$ls_conordcom,$ls_obscom,$ls_lugentnomdep,$ls_lugentdir,$ls_conordcomanu;
		global $ls_diaplacom,$ld_porsegcom,$ld_monsegcom,$ls_despai,$ls_desest,$ls_codpai,$ls_denmun,$ls_denpar,$ls_uniejeaso;
		global $ls_codest,$ls_codmun,$ls_codpar,$ls_codmon,$ls_denmon,$ld_tascamordcom,$ld_montotdiv,$ls_nomprov,$ls_tipo,$ls_readonly;

   		global $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_rb_lugcom,$ls_tipconpro,$ls_rnc;
		global $ls_operacion,$ls_existe,$io_fun_soc,$li_totrowbienes,$li_totrowservicios,$li_totrowcargos,$ls_estsegcom;
		global $ls_parametros,$ls_estcom,$ls_estapro,$ls_codtipsol,$li_totrowcuentascargo,$li_totrowcuentas,$li_estsegcom, $ls_pagele;
		global $ls_perentdesde,$ls_perenthasta,$ls_distipmat,$ls_distipact,$ls_chkmat,$ls_chkact,$ls_selbie,$ls_selser,$li_ressoc,$ls_chkressoc;
		global $ls_distipordcom,$li_estciespg,$li_estciespi,$ls_disabled,$ls_tipafeiva,$ls_rifpro,$ls_numanacot,$ls_controlguard,$ls_numdiacre,$ls_tipsiscam,$ls_codconobr;

		require_once("../shared/class_folder/sigesp_c_generar_consecutivo.php");
        $io_id_process_soc= new sigesp_c_generar_consecutivo();
		$ls_estatus="REGISTRO";
		$ls_estcom="R";
		$ld_fecordcom=date("d/m/Y");
		$ls_numordcom="";
		$ls_selbie = "";
		$ls_selser = "";
		$ls_distipordcom = "";
		$ls_logusr	  = $_SESSION["la_logusr"];
		$ls_codemp	  = $_SESSION["la_empresa"]["codemp"];
		$ls_tipafeiva = $_SESSION["la_empresa"]["confiva"];
		$ls_administrativo = "";
		$arrResultado=$io_fun_soc->uf_soc_select_administrador($ls_codemp,$ls_logusr,$ls_administrativo);
		$ls_administrativo = $arrResultado['as_administrativo'];
		$lb_valido=$arrResultado['lb_valido'];
		
	    if (($lb_valido)&&($ls_administrativo==1))
	       {
		     $ls_readonly = "";
	       }
	    else
	       {
		     $ls_readonly = "readonly";
	       }
		$ls_numordcombie = $io_id_process_soc->uf_generar_numero_nuevo2('SOC','soc_ordencompra','numordcom','SOCCOC',15,'numordcom','estcondat','B',$ls_logusr);
		$ls_numordcomser = $io_id_process_soc->uf_generar_numero_nuevo2('SOC','soc_ordencompra','numordcom','SOCCOS',15,'numordser','estcondat','S',$ls_logusr);
		$ls_tipordcom = "-";
		$ls_tipsol="";
		$ls_codprov="";
		$ls_nomprov="";
		$ls_coduniadm="";
		$ls_denuniadm="";
		$ls_codfuefin="";
		$ls_denfuefin="";
		$ls_forpag="";
		$ls_pagele='F';
		$ls_tipsiscam="";
		$ld_antpag="0,00";
		$ls_numdiacre="0";
        $ls_rb_nacional="checked";
		$ls_rb_exterior="";
        $ls_concom="";
        $ls_codtipmod="";
		$ls_denmodcla="";
		$ls_conordcom="";
		$ls_conordcomanu="";
		$ls_obscom="";
		$ls_lugentnomdep="";
		$ls_lugentdir="";
		$ls_diaplacom="";
		$ld_porsegcom="0,00";
		$ld_monsegcom="0,00";
		$ls_despai="";
		$ls_desest="";
		$ls_denmun="";
		$ls_denpar="";
		$ls_codpai="";
		$ls_codest="";
		$ls_codmun="";
		$ls_codpar="";
		$ls_codmon="";
		$ls_denmon="";
		$ld_tascamordcom="0,00";
		$ld_montotdiv="0,00";
		$ls_tipo="OC";
		$li_estsegcom="";

		$ls_codestpro1="";
		$ls_codestpro2="";
		$ls_codestpro3="";
		$ls_codestpro4="";
		$ls_codestpro5="";
		$ls_estcla="";
		$ls_codtipsol="";
		$ls_parametros="";
		$ls_operacion=$io_fun_soc->uf_obteneroperacion();
		$ls_existe=$io_fun_soc->uf_obtenerexiste();
		$li_totrowbienes=0;
		$li_totrowservicios=0;
		$li_totrowcargos=0;
		$li_totrowcuentas=0;
		$li_totrowcuentascargo=0;
		$ls_estapro="";
		$ls_codtipsol="";
		$ls_tipconpro="";
		$ls_rnc="";
		$ls_uniejeaso="";
		$ls_perentdesde="";
		$ls_perenthasta="";
		$ls_disbie = $ls_disser = "";
		$ls_distipmat = "disabled";
		$ls_distipact = "disabled";
		$ls_tipbieordcom = "-";//Tipo de Bienes Incluidos en la Orden de Compra, Materiales y/o Suministros ? Activos.
		if (!array_key_exists("radiotipbieordcom",$_POST))
		   {
			 $ls_chkmat = $ls_chkact = "";
		   }
		$li_estciespi = 0;
		$li_estciescg = 0;
		$arrResultado = $io_fun_soc->uf_load_estatus_cierre($li_estciespi,$li_estciescg);
		$li_estciespi = $arrResultado['li_estciespi'];
		$li_estciescg = $arrResultado['li_estciescg'];
		$li_estciespg = $arrResultado['li_estciespg'];
		$ls_disabled  = "";
		if ($li_estciespg==1 || $li_estciespi==1)
		   {
		     $ls_disabled = "disabled";
		   }
		$ls_rifpro="";
		$ls_numanacot="";
		$ls_controlguard="";
		$ls_codconobr="";
		$li_ressoc="";
		$ls_chkressoc="";
   }
   //------------------------------------------------------------------------------------------------------------------------------

   //------------------------------------------------------------------------------------------------------------------------------
   function uf_load_variables()
   {
		//////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Funci?n que carga todas las variables necesarias en la p?gina
		//	   Creado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 19/04/2007				Fecha ?ltima Modificaci?n :
		//////////////////////////////////////////////////////////////////////////////////////
   		global $ls_estatus,$ld_fecordcom,$ls_numordcom,$ls_selbie,$ls_selser,$ls_tipsol,$ls_codprov,$ls_nomprov,$ls_tipordcom;
		global $ls_coduniadm,$ls_denuniadm,$ls_codfuefin,$ls_denfuefin,$ls_forpag,$ld_antpag,$ls_rb_nacional,$ls_rb_exterior,$ls_pagele;
		global $ls_concom,$ls_codtipmod,$ls_denmodcla,$ls_conordcom,$ls_conordcomanu,$ls_obscom,$ls_lugentnomdep,$ls_lugentdir,$ls_diaplacom;
		global $ld_porsegcom,$ld_monsegcom,$ls_despai,$ls_desest,$ls_codpai,$ls_denmun,$ls_denpar,$ls_codpai,$ls_codest,$li_estciespi;
		global $ls_codmun,$ls_codpar,$ls_codmon,$ls_denmon,$ld_tascamordcom,$ld_montotdiv,$li_estsegcom,$ls_distipordcom;
		global $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_rb_lugcom,$ls_tipsol;
		global $ls_codtipsol,$li_totrowservicios,$li_totrowcargos,$li_subtotal,$li_cargos,$li_total,$li_totrowbienes,$ls_uniejeaso;
		global $li_totrowcuentas,$ls_estcom,$io_fun_soc,$li_totrowconceptos,$li_totrowcuentascargo,$ls_numsoldel,$ls_tipconpro,$ls_rnc;
		global $ls_perentdesde,$ls_perenthasta,$ls_distipmat,$ls_distipact,$ls_chkmat,$ls_chkact,$ls_tipbieordcom,$li_estciespg,$ls_tipafeiva,$ls_numdiacre,$ls_tipsiscam;
		global $ls_rifpro,$ls_numanacot,$ls_controlguard,$ls_codconobr,$li_ressoc,$ls_chkressoc;

		$ls_estcom=$_POST["txtestcom"];
		$ld_fecordcom=$io_fun_soc->uf_obtenervalor("txtfecordcom",$_POST["txtfecha"]);
		$ls_numordcom=$_POST["txtnumordcom"];
		$ls_tipsol=$_POST["txttipsol"];
		$ls_coduniadm=$_POST["txtcodunieje"];
		$ls_denuniadm=$_POST["txtdenunieje"];
		$ls_codfuefin=$_POST["txtcodfuefin"];
		$ls_denfuefin=$_POST["txtdenfuefin"];
		$ls_tipordcom = $_POST["tipord"];
		if ($ls_tipordcom=='B')
		   {
			 $ls_selbie = "selected";
			 $ls_selser = "";
			 $ls_distipordcom = "disabled";
		   }
		elseif($ls_tipordcom=='S')
		   {
			 $ls_selbie = "";
			 $ls_selser = "selected";
			 $ls_distipordcom = "disabled";
		   }
		else
		   {
			 $ls_selbie = "";
			 $ls_selser = "";
			 $ls_distipordcom = "";
		   }
		$ls_codprov=$_POST["txtcodprov"];
		$ls_nomprov=$_POST["txtnomprov"];
		$ls_forpag=$_POST["cmbforpag"];
		$ls_pagele=$_POST["cmbpagele"];
		$ls_tipsiscam=$_POST["cmbtipsiscam"];
		$ld_antpag=$_POST["txtantpag"];
        $ls_rb_lugcom=$_POST["rblugcom"];
        if($ls_rb_lugcom=="N")
		{
		   $ls_rb_nacional="checked";
		}
        if($ls_rb_lugcom=="E")
		{
		   $ls_rb_exterior="checked";
		}
        $ls_concom=$_POST["cbmconcom"];
        $ls_codtipmod=$_POST["txtcodtipmod"];
		$ls_denmodcla=$_POST["txtdenmodcla"];
		$ls_conordcom=$_POST["txtconordcom"];
		$ls_conordcomanu=$_POST["txtconordanu"];
		$ls_obscom=$_POST["txtobscom"];
		$ls_lugentnomdep=$_POST["txtlugentnomdep"];
		$ls_lugentdir=$_POST["txtlugentdir"];
		$ls_diaplacom=$_POST["txtdiaplacom"];
		$ld_porsegcom=$_POST["txtporsegcom"];
		$ld_monsegcom=$_POST["txtmonsegcom"];
		$ls_codpai=substr($_POST["cmbpais"],0,3);
		$ls_despai=$_POST["despai"];
		$ls_codest=substr($_POST["cmbestado"],0,3);
		$ls_desest=$_POST["desest"];
		$ls_codmun=substr($_POST["cmbmunicipio"],0,3);
		$ls_denmun=$_POST["desmun"];
		$ls_codpar=substr($_POST["cmbparroquia"],0,3);
		$ls_denpar=$_POST["cmbparroquia"];
		$ls_codmon=$_POST["txtcodmon"];
		$ls_denmon=$_POST["txtdenmon"];
		$ld_tascamordcom=$_POST["txttascamordcom"];
		$ld_montotdiv=$_POST["txtmontotdiv"];

		if (array_key_exists("chkbestsegcom",$_POST))
	    {
			 $li_estsegcom      = $_POST["chkbestsegcom"];
			 $ls_chkbestsegcom  = "checked";
	    }
		else
	    {
			 $li_estsegcom      = 0;
			 $ls_chkbestsegcom  = "";
	    }
		
		if (array_key_exists("chkressoc",$_POST))
	    {
			 $li_ressoc      = "1";
			 $ls_chkressoc  = "checked";
	    }
		else
	    {
			 $li_ressoc      = "0";
			 $ls_chkressoc  = "";
	    }
		$ls_tipsol     = $_POST["txttipsol"];
		$ls_codestpro1 = $_POST["txtcodestpro1"];
		$ls_codestpro2 = $_POST["txtcodestpro2"];
		$ls_codestpro3 = $_POST["txtcodestpro3"];
		$ls_codestpro4 = $_POST["txtcodestpro4"];
		$ls_codestpro5 = $_POST["txtcodestpro5"];
		$ls_estcla     = $_POST["hidestcla"];
		$li_totrowbienes=$_POST["totrowbienes"];
		$li_totrowservicios=$_POST["totrowservicios"];
		$li_totrowcargos=$_POST["totrowcargos"];
		$li_totrowcuentas=$_POST["totrowcuentas"];
		$li_totrowcuentascargo=$_POST["totrowcuentascargo"];
		if (array_key_exists("txtsubtotal",$_POST))
	    {
			$li_subtotal=$_POST["txtsubtotal"];
	    }
		else
	    {
	    	$li_subtotal="";
		}
	    if (array_key_exists("txtcargos",$_POST))
	    {
			$li_cargos=$_POST["txtcargos"];
	    }
		else
	    {
	    	$li_cargos="";
		}
		if (array_key_exists("txttotal",$_POST))
	    {
			$li_total=$_POST["txttotal"];
	    }
		else
	    {
	    	$li_total="";
		}

		$ls_numsoldel=$_POST["numsoldel"];
		$ls_tipconpro=$_POST["tipconpro"];
		$ls_rnc=$_POST["txtrnc"];
		$ls_uniejeaso=$_POST["txtuniejeaso"];
		$ls_perentdesde = $_POST["txtperentdesde"];
		$ls_perenthasta = $_POST["txtperenthasta"];
		$ls_distipmat = $ls_distipact = "";
		if (array_key_exists("radiotipbieordcom",$_POST))
		   {
			 $ls_tipbieordcom = $_POST["radiotipbieordcom"];
			 $ls_chkmat = $ls_chkact = "";
			 if ($ls_tipbieordcom=='M')
				{
				  $ls_chkmat = 'checked';
				}
			 elseif($ls_tipbieordcom=='A')
				{
				  $ls_chkact = 'checked';
				}
		   }
		else
		   {
			 $ls_tipbieordcom = "-";
		   }
        $li_estciespg = $_POST["hidestciespg"];//Estatus del Cierre Presupuestario de Gasto.
		$li_estciespi = $_POST["hidestciespi"];//Estatus del Presupuestario de Ingresos.
		$ls_tipafeiva = $_POST["hidtipafeiva"];
		$ls_rifpro= $_POST["txtrifpro"];
		$ls_numanacot=$_POST["numanacot"];
		$ls_controlguard=$_POST["controlguard"];
		$ls_numdiacre=$_POST["txtnumdiacre"];
		$ls_codconobr=$_POST["txtcodconobr"];
   }
   //------------------------------------------------------------------------------------------------------------------------------

   //------------------------------------------------------------------------------------------------------------------------------
   function uf_load_data($as_parametros)
   {
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_variables
		//		   Access: private
		//	  Description: Funci?n que carga todas las variables necesarias en la p?gina
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		//Modificador Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 17/03/2007								Fecha ?ltima Modificaci?n : 19/04/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////
   		global $li_subtotal,$li_cargos,$li_total,$li_totrowbienes,$li_totrowservicios,$li_totrowcargos,$li_totrowcuentas;
		global $li_totrowservicios,$li_totrowconceptos,$li_totrowcuentascargo;
		for($li_i=1;($li_i<$li_totrowbienes);$li_i++)
		{
			$ls_codart=$_POST["txtcodart".$li_i];
			$ls_denart=$_POST["txtdenart".$li_i];
			$li_canart=$_POST["txtcanart".$li_i];
			$ls_unidad=$_POST["cmbunidad".$li_i];
			$li_preart=$_POST["txtpreart".$li_i];
			$li_subtotart=$_POST["txtsubtotart".$li_i];
			$li_carart=$_POST["txtcarart".$li_i];
			$li_totart=$_POST["txttotart".$li_i];
			$ls_spgcuenta=$_POST["txtspgcuenta".$li_i];
			$ls_unidadfisica=$_POST["txtunidad".$li_i];
			$ls_numsolord=$_POST["txtnumsolord".$li_i];
			$ls_coduniadmsep=$_POST["txtcoduniadmsep".$li_i];
			$ls_codestpro=$_POST["hidcodestpro".$li_i];
			$ls_estcla=$_POST["estcla".$li_i];
			$ls_codfuefin=$_POST["txtcodfuefin".$li_i];

			$as_parametros=$as_parametros."&txtcodart".$li_i."=".$ls_codart."&txtdenart".$li_i."=".$ls_denart."".
					   					  "&txtcanart".$li_i."=".$li_canart."&cmbunidad".$li_i."=".$ls_unidad."".
										  "&txtpreart".$li_i."=".$li_preart."&txtsubtotart".$li_i."=".$li_subtotart."".
										  "&txtcarart".$li_i."=".$li_carart."&txttotart".$li_i."=".$li_totart."".
										  "&hidcodestpro".$li_i."=".$ls_codestpro."&estcla".$li_i."=".$ls_estcla."&txtcodfuefin".$li_i."=".$ls_codfuefin."".
										  "&txtspgcuenta".$li_i."=".$ls_spgcuenta."&txtunidad".$li_i."=".$ls_unidadfisica."".
										  "&txtnumsolord".$li_i."=".$ls_numsolord."&txtcoduniadmsep".$li_i."=".$ls_coduniadmsep;
		}
		$as_parametros=$as_parametros."&totalbienes=".$li_totrowbienes."";
		for($li_i=1;($li_i<$li_totrowservicios);$li_i++)
		{
			$ls_codser=$_POST["txtcodser".$li_i];
			$ls_denser=$_POST["txtdenser".$li_i];
			$li_canser=$_POST["txtcanser".$li_i];
			$li_preser=$_POST["txtpreser".$li_i];
			$li_subtotser=$_POST["txtsubtotser".$li_i];
			$li_carser=$_POST["txtcarser".$li_i];
			$li_totser=$_POST["txttotser".$li_i];
			$ls_spgcuenta=$_POST["txtspgcuenta".$li_i];
			$ls_numsolord=$_POST["txtnumsolord".$li_i];
			$ls_coduniadmsep=$_POST["txtcoduniadmsep".$li_i];
			$ls_codestpro=$_POST["hidcodestpro".$li_i];
			$ls_estcla=$_POST["estcla".$li_i];
			$ls_codfuefin=$_POST["txtcodfuefin".$li_i];

			$as_parametros=$as_parametros."&txtcodser".$li_i."=".$ls_codser."&txtdenser".$li_i."=".$ls_denser."".
					  					  "&txtcanser".$li_i."=".$li_canser."&txtpreser".$li_i."=".$li_preser."".
					  					  "&txtsubtotser".$li_i."=".$li_subtotser."&txtcarser".$li_i."=".$li_carser."".
					   					  "&txttotser".$li_i."=".$li_totser."&txtspgcuenta".$li_i."=".$ls_spgcuenta."".
										  "&hidcodestpro".$li_i."=".$ls_codestpro."&estcla".$li_i."=".$ls_estcla."&txtcodfuefin".$li_i."=".$ls_codfuefin."".
										  "&txtnumsolord".$li_i."=".$ls_numsolord."&txtcoduniadmsep".$li_i."=".$ls_coduniadmsep;
		}
		$as_parametros=$as_parametros."&totalservicios=".$li_totrowservicios."";
		for($li_i=1;($li_i<=$li_totrowcargos);$li_i++)
		{
			$ls_codart		 = $_POST["txtcodservic".$li_i];
			$ls_codcar		 = $_POST["txtcodcar".$li_i];
			$ls_dencar		 = $_POST["txtdencar".$li_i];
			$li_bascar		 = $_POST["txtbascar".$li_i];
			$li_moncar		 = $_POST["txtmoncar".$li_i];
			$li_subcargo	 = $_POST["txtsubcargo".$li_i];
			$ls_formulacargo = $_POST["formulacargo".$li_i];
			$ls_cuentacargo  = $_POST["cuentacargo".$li_i];
			$ls_numsep       = $_POST["hidnumsepcar".$li_i];
			$ls_codprog = $_POST["codprogcargo".$li_i];
		    $ls_estcla = $_POST["estclacargo".$li_i];
		    $ls_codfuefin = $_POST["txtcodfuefincre".$li_i];

			$as_parametros=$as_parametros."&txtcodservic".$li_i."=".$ls_codart."&txtcodcar".$li_i."=".$ls_codcar.
					   					  "&txtdencar".$li_i."=".$ls_dencar."&txtbascar".$li_i."=".$li_bascar.
					   					  "&txtmoncar".$li_i."=".$li_moncar."&txtsubcargo".$li_i."=".$li_subcargo.
					  					  "&cuentacargo".$li_i."=".$ls_cuentacargo."&formulacargo".$li_i."=".$ls_formulacargo.
										  "&hidnumsepcar".$li_i."=".$ls_numsep."&codprogcargo".$li_i."=".$ls_codprog.
										  "&estclacargo".$li_i."=".$ls_estcla."&txtcodfuefincre".$li_i."=".$ls_codfuefin;
		}
		$as_parametros=$as_parametros."&totalcargos=".$li_totrowcargos;
		for($li_i=1;($li_i<$li_totrowcuentas);$li_i++)
		{
			$ls_codpro=$_POST["txtcodprogas".$li_i];
			$ls_cuenta=$_POST["txtcuentagas".$li_i];
			$li_moncue=$_POST["txtmoncuegas".$li_i];
			$ls_estcla=$_POST["estclapre".$li_i];

			$as_parametros=$as_parametros."&txtcodprogas".$li_i."=".$ls_codpro."&txtcuentagas".$li_i."=".$ls_cuenta.
					   "&txtmoncuegas".$li_i."=".$li_moncue."&estclapre".$li_i."=".$ls_estcla;
		}
		$as_parametros=$as_parametros."&totalcuentas=".$li_totrowcuentas;
		for($li_i=1;($li_i<$li_totrowcuentascargo);$li_i++)
		{
			$ls_codcargo=$_POST["txtcodcargo".$li_i];
			$ls_codpro=$_POST["txtcodprocar".$li_i];
			$ls_cuenta=$_POST["txtcuentacar".$li_i];
			$li_moncue=$_POST["txtmoncuecar".$li_i];
			$ls_estcla=$_POST["estclacar".$li_i];

			$as_parametros=$as_parametros."&txtcodcargo".$li_i."=".$ls_codcargo."&txtcodprocar".$li_i."=".$ls_codpro.
						   "&txtcuentacar".$li_i."=".$ls_cuenta."&txtmoncuecar".$li_i."=".$li_moncue."&estclacar".$li_i."=".$ls_estcla;
		}
		$as_parametros=$as_parametros."&totalcuentascargo=".$li_totrowcuentascargo;
		$as_parametros=$as_parametros."&subtotal=".$li_subtotal."&cargos=".$li_cargos."&total=".$li_total;
		return $as_parametros;
   }
   //------------------------------------------------------------------------------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title >Registro de Orden de Compra</title>
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="imagetoolbar" content="text/html; charset=iso-8859-1">
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
<script type="text/javascript"     src="../shared/js/js_intra/datepickercontrol.js"></script>
<script type="text/javascript"  src="js/funcion_soc.js"></script>
<script type="text/javascript"  src="../shared/js/number_format.js"></script>
<script type="text/javascript"  src="../shared/js/valida_tecla.js"></script>
<script type="text/javascript"  src="../shared/js/disabled_keys.js"></script>
<script type="text/javascript"  src="../shared/js/validaciones.js"></script>
<link href="css/soc.css" 								  rel="stylesheet" type="text/css">
<link href="../shared/css/tablas.css"                     rel="stylesheet" type="text/css">
<link href="../shared/css/ventanas.css"                   rel="stylesheet" type="text/css">
<link href="../shared/css/cabecera.css"                   rel="stylesheet" type="text/css">
<link href="../shared/css/general.css"                    rel="stylesheet" type="text/css">
<link href="../shared/js/css_intra/datepickercontrol.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
#Layer1 {
	position:absolute;
	left:9px;
	top:151px;
	width:214px;
	height:28px;
	z-index:1;
}
-->
</style>
</head>
<body onLoad="writetostatus('<?php print "Base de Datos: ".$_SESSION["ls_database"].". Usuario: ".$_SESSION["la_logusr"];?>')">
<?php
	require_once("class_folder/sigesp_soc_c_registro_orden_compra.php");
	$io_soc=new sigesp_soc_c_registro_orden_compra("../");
	$ls_estpref="";
	uf_limpiarvariables();
	switch ($ls_operacion)
	{
			case "GUARDAR":
				uf_load_variables();
				$ls_tipsol= $_POST["txttipsol"];
				$ls_numordcom=$_POST["txtnumordcom"];
				$ls_uniejeaso=$_POST["txtuniejeaso"];
				$ls_uniejeaso_sep=substr($ls_uniejeaso,10,15);
				$ls_conordcom=$_POST["txtconordcom"];
				$ls_tipord=$_POST["tipord"];
				$ls_prefijo=$_POST["cmbprefijo"];
				$ls_uniejeasoaux="";
				$existe=0;
				$lb_valido=true;
				if($ls_rnc=="")
				{
					$lb_valido=$io_soc->uf_validacion_anual($ls_codprov,$ls_obscom,$li_total);
				}
				if($lb_valido)
				{
					$arrResultado=$io_soc->uf_guardar($ls_estcom,$ld_fecordcom,$li_estsegcom,$ls_numordcom,$ls_coduniadm,$ls_codfuefin,
												      $ls_tipordcom,$ls_codprov,$ls_forpag,$ld_antpag,$ls_rb_lugcom,$ls_concom,$ls_codtipmod,
												      $ls_conordcom,$ls_obscom,$ls_lugentnomdep,$ls_lugentdir,$ls_diaplacom,$ld_porsegcom,
												      $ld_monsegcom,$ls_codpai,$ls_codest,$ls_codmun,$ls_codpar,$ls_codmon,$ld_tascamordcom,
												      $ld_montotdiv,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
												      $ls_codestpro5,$ls_estcla,$li_totrowbienes,$li_totrowservicios,$li_totrowcargos,$li_totrowcuentas,
												      $li_totrowcuentascargo,$li_subtotal,$li_cargos,$li_total,$la_seguridad,$ls_existe,
												      $ls_tipsol,$ls_numsoldel,$ls_uniejeaso,$ls_perentdesde,$ls_perenthasta,$ls_tipbieordcom,
												      $ls_numdiacre,$ls_tipsiscam,$ls_codconobr,$li_ressoc,$la_permisos["administrador"],$ls_pagele,$ls_prefijo);
					$ls_estcom = $arrResultado['as_estcom'];
					$ls_numordcom = $arrResultado['as_numordcom'];
					$lb_valido = $arrResultado['lb_valido'];
					$ls_parametros = uf_load_data($ls_parametros);
					switch($ls_estcom)
					{
						case "0": // R
							$ls_estatus="REGISTRO";
							break;
						case "1": // E
							$ls_estatus="EMITIDA";
							break;
					}
					if($lb_valido)
					{
						$ls_existe="TRUE";
						$ls_controlguard="";
					}
				}
				else
				{
					$ls_parametros = uf_load_data($ls_parametros);
					$ls_controlguard="";
				}
			break;

			case "ELIMINAR";
				uf_load_variables();
				$lb_valido=$io_soc->uf_delete_orden_compra($ls_numordcom,$ls_tipordcom,$la_seguridad,$la_permisos["administrador"]);
				if(!$lb_valido)
				{
					$ls_parametros = uf_load_data($ls_parametros);
					switch($ls_estcom)
					{
						case "0": // R
							$ls_estatus="REGISTRO";
							break;
						case "1": // E
							$ls_estatus="EMITIDA";
							break;
					}
					$ls_existe="TRUE";
				}
				else
				{
					uf_limpiarvariables();
					$ls_chkmat = $ls_chkact = "";
					$ls_existe="FALSE";
				}
			break;
			case "PREFIJO";
				uf_limpiarvariables();
				$ls_estpref="1";
				$ls_tipordcom=$io_fun_soc->uf_obtenervalor("",$_POST["tipord"]);
				$ls_codpre=$io_fun_soc->uf_obtenervalor("",$_POST["cmbprefijo"]);
				$ld_fecordcom=$io_fun_soc->uf_obtenervalor("",$_POST["txtfecordcom"]);
				if ($ls_tipordcom=='B')
				   {
					 $ls_selbie = "selected";
					 $ls_selser = "";
					 $ls_distipordcom = "disabled";
					 $ls_procedepre="SOCCOC";
					 if($ls_codpre=="")
					 {
						$ls_numordcom = $io_id_process_soc->uf_generar_numero_nuevo2('SOC','soc_ordencompra','numordcom','SOCCOC',15,'numordcom','estcondat','B',$ls_logusr);
					 
					 }
					 else
					 {
					 	$ls_numordcom = $io_id_process_soc->uf_generar_numero_nuevo3('SOC','soc_ordencompra','numordcom','SOCCOC',15,'numordcom','estcondat','B',$ls_logusr,$ls_codpre);
					 }
				   }
				elseif($ls_tipordcom=='S')
				   {
					 $ls_selbie = "";
					 $ls_selser = "selected";
					 $ls_distipordcom = "disabled";
					 $ls_procedepre="SOCCOS";

					 if($ls_codpre=="")
					 {
						$ls_numordcom = $io_id_process_soc->uf_generar_numero_nuevo2('SOC','soc_ordencompra','numordcom','SOCCOS',15,'numordser','estcondat','S',$ls_logusr);
					 }
					 else
					 {
						 $ls_numordcom = $io_id_process_soc->uf_generar_numero_nuevo3('SOC','soc_ordencompra','numordcom','SOCCOS',15,'numordser','estcondat','S',$ls_logusr,$ls_codpre);
					 }
				   }
				else
				   {
					 $ls_selbie = "";
					 $ls_selser = "";
					 $ls_distipordcom = "";
				   }
			break;
	}
?>
<table width="850" border="0" align="center" cellpadding="0" cellspacing="0" class="contorno">
  <tr>
    <td height="30" colspan="8" class="cd-logo"><img src="../shared/imagebank/header.jpg" width="850" height="40"></td>
  </tr>
  <tr>
  <td height="20" colspan="12" bgcolor="#E7E7E7">
    <table width="850" border="0" align="center" cellpadding="0" cellspacing="0">
      <td width="500" height="20" bgcolor="#E7E7E7" class="descripcion_sistema">Ordenes de Compra</td>
	  <td width="350" bgcolor="#E7E7E7"><div align="right"><span class="letras-pequenas"><b><?php print $ls_diasem." ".date("d/m/Y")." - ".date("h:i a ");?></b></span></div></td>
	  <tr>
	    <td height="20" bgcolor="#E7E7E7" class="descripcion_sistema">&nbsp;</td>
	  	<td bgcolor="#E7E7E7"><div align="right" class="letras-pequenas"><b><?php print $_SESSION["la_nomusu"]." ".$_SESSION["la_apeusu"];?></b></div></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td height="20" colspan="8" bgcolor="#E7E7E7" class="cd-menu"><script type="text/javascript"  src="js/menu.js"></script></td>
  </tr>
  <tr>
    <td height="13" colspan="8" class="toolbar"></td>
  </tr>
  <tr>
    <td height="20" width="22" class="toolbar"><div align="center"><a href="javascript: ue_nuevo();"><img src="../shared/imagebank/tools20/nuevo.gif" alt="Nuevo" width="20" height="20" border="0" title="Nuevo"></a></div></td>
    <td class="toolbar" width="21"><div align="center"><a href="javascript: ue_guardar();"><img src="../shared/imagebank/tools20/grabar.gif" alt="Guardar" width="20" height="20" border="0" title="Guardar"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_buscar();"><img src="../shared/imagebank/tools20/buscar.gif" alt="Buscar" width="20" height="20" border="0" title="Buscar"></a></div></td>
    <td class="toolbar" width="22"><div align="center"><a href="javascript: ue_eliminar();"><img src="../shared/imagebank/tools20/eliminar.gif" alt="Eliminar" width="20" height="20" border="0" title="Eliminar"></a></div></td>
    <td class="toolbar" width="22"><a href="javascript: ue_imprimir();"><img src="../shared/imagebank/tools20/imprimir.gif" alt="Imprimir" width="20" height="20" border="0" title="Imprimir"></a></td>
    <td class="toolbar" width="22"><div align="center"><a href="../soc/sigespwindow_blank.php"><img src="../shared/imagebank/tools20/salir.gif" alt="Salir" width="20" height="20" border="0" title="Salir"></a></div></td>
    <td class="toolbar" width="26"><a href="javascript: ue_ayuda();"><img src="../shared/imagebank/tools20/ayuda.gif" alt="Ayuda" width="20" height="20" border="0" title="Ayuda"></a></td>
    <td class="toolbar" width="693">&nbsp;</td>
  </tr>
</table>

<p>&nbsp;</p>
<form action="" method="post" name="formulario" id="orden_compra">
  <?php
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
	$io_fun_soc->uf_print_permisos($ls_permisos,$la_permisos,$ls_logusr,"location.href='sigespwindow_blank.php'");
	unset($io_fun_soc);
//////////////////////////////////////////////         SEGURIDAD               /////////////////////////////////////////////
?>
  <input name="operacion" type="hidden" id="operacion" value="<?php print $ls_operacion;?>">
  <table width="850" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
    <tr>
    <td width="760" height="136"><p>&nbsp;</p>
      <table width="850" height="573" border="0" align="center" cellpadding="0" cellspacing="0" class="formato-blanco">
          <tr>
            <td colspan="6" class="titulo-ventana">Registro de  Orden de Compra
            <input name="hidestciespg" type="hidden" id="hidestciespg" value="<?php echo $li_estciespg ?>" />
            <input name="hidestciespi" type="hidden" id="hidestciespi" value="<?php echo $li_estciespi ?>" />
            <input name="hidtipafeiva" type="hidden" id="hidtipafeiva" value="<?php echo $ls_tipafeiva ?>">
			<input name="controlguard" type="hidden" id="controlguard" value="<?php echo $ls_controlguard ?>"></td>
          </tr>
          <tr>
            <td height="22" style="visibility:hidden"><div align="left">Reporte en
              <select name="cmbbsf" id="cmbbsf">
                <option value="0" selected="selected">Bs.</option>
                <option value="1">Bs.F.</option>
              </select>
            </div></td>
            <td colspan="2">&nbsp;</td>
            <td colspan="2">&nbsp;</td>
            <td><input name="txtorigen" type="text" class="sin-borde3" id="txtorigen" size="30" readonly></td>
          </tr>
          <tr>
            <td width="154" height="22" style="text-align:right">Estatus</td>
            <td colspan="2">
                <input name="txtestatus" type="text" class="sin-borde2" id="txtestatus" value="<?php print $ls_estatus; ?>" size="30" readonly>            </td>
            <td colspan="2" style="text-align:right">Fecha</td>
            <td width="217"><input name="txtfecordcom" type="text" id="txtfecordcom" style="text-align:center" value="<?php print $ld_fecordcom;?>" size="15"  datepicker="true" <?php echo $ls_disabled;?> onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);" onBlur="javascript: ue_validar_formatofecha(this);"></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Tipo de Orden</td>
            <td height="22" colspan="5"><label>
              <select name="cmbtipordcom" id="cmbtipordcom" style="width:110px" onChange="javascript: ue_generar_numero('<?php echo $ls_readonly ?>');" <?php echo $ls_distipordcom; echo $ls_disabled; ?>>
                <option value="-">---seleccione---</option>
                <option value="B" <?php echo $ls_selbie; ?>>Bienes</option>
                <option value="S" <?php echo $ls_selser; ?>>Servicios</option>
              </select>
              <input name="radiotipbieordcom" type="radio" class="sin-borde" <?php echo $ls_distipmat ?> value="M" <?php print $ls_chkmat; ?> onchange="uf_load_catalogo();" />
Materiales y/o Suministros.
<input name="radiotipbieordcom" type="radio" class="sin-borde" <?php echo $ls_distipact ?> value="A" <?php print $ls_chkact; ?> onchange="uf_load_catalogo();" />
Bienes.</label></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Prefijo</td>
            <td height="22" colspan="5"><?php $io_id_process_soc->uf_prefijo($ls_codpre,"SOC",$ls_procedepre,$ls_logusr); ?></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">N&uacute;mero de Orden </td>
            <td height="22" colspan="4"><input name="txtnumordcom" type="text" id="txtnumordcom" value="<?php print $ls_numordcom;?>" size="18" maxlength="15"   style="text-align:center" <?php if(($la_permisos["administrador"]!=1)||($ls_operacion!="NUEVO")){print "readonly";} ?>  onBlur="javascript: ue_rellenarcampo(this,15);"></td>
            <td height="22">&nbsp;</td>
          </tr>
          <tr>
            <td height="27" style="text-align:right">Proveedor</td>
            <td height="27" colspan="5"><input name="txtcodprov" type="text" id="txtcodprov" value="<?php print $ls_codprov;?>" size="18" maxlength="10" readonly style="text-align:center">
            <a href="javascript: ue_catalogo_proveedor();"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a><input name="txtnomprov" type="text" class="sin-borde" id="txtnomprov" value="<?php print $ls_nomprov;?>" size="80" maxlength="50" readonly>
            <input name="tipconpro"     type="hidden" id="tipconpro"     value="<?php print $ls_tipconpro; ?>">
            <input name="txtrnc" type="hidden" id="txtrnc" value="<?php print $ls_rnc; ?>"></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Rif</td>
            <td height="22" colspan="5"><input name="txtrifpro" type="text" id="txtrifpro" size="15" value="<?php print $ls_rifpro; ?>" readonly></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right"><a href="javascript: ue_cargar_solicitud();"><img src="../shared/imagebank/tools20/presupuestaria.gif"  alt="Buscar" width="20" height="20" border="0"></a></td>
            <td height="22" colspan="5"><span class="sin-borde3"><a href="javascript: ue_cargar_solicitud();">Solicitud de Ejecuci?n Presupuestaria</a></span></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Unidad Ejecutora</td>
            <td height="22" colspan="5"><input name="txtcodunieje" type="text" id="txtcodunieje" style="text-align:center" value="<?php print $ls_coduniadm;?>" size="15" maxlength="10" readonly>
                <a href="javascript: ue_catalogo('sigesp_cat_unidad_ejecutora.php');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a><input name="txtdenunieje" type="text" class="sin-borde" id="txtdenunieje" value="<?php print $ls_denuniadm;?>" size="80" readonly>
                <a href="javascript: ue_catalogo('sigesp_soc_cat_modalidad_clausulas.php');"></a> </td>
          </tr>

          <tr>
            <td height="22" style="text-align:right">Fuente de Financiamiento</td>
            <td height="22" colspan="5"><input name="txtcodfuefin" type="text" id="txtcodfuefin" style="text-align:center" value="<?php print $ls_codfuefin;?>" size="5" maxlength="2" readonly>
                <a href="javascript: ue_catalogo('sigesp_soc_cat_fuente_financiamiento.php');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
                <input name="txtdenfuefin" type="text" class="sin-borde" id="txtdenfuefin" value="<?php print $ls_denfuefin;?>" size="50" readonly></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Lugar de la Compra</td>
            <td width="169" height="22"><input name="rblugcom" type="radio" class="sin-borde" value="N" <?php print $ls_rb_nacional; ?> <?php echo $ls_disabled;?>>
            <span class="sin-borde3">Nacional
            <input name="rblugcom" type="radio" class="sin-borde" value="E" <?php print $ls_rb_exterior; ?> <?php echo $ls_disabled;?>>
            Extranjero</span></td>
            <td height="22" style="text-align:right">Condici&oacute;n</td>
            <td height="22"><select name="cbmconcom"  style="width:120px" id="cbmconcom" <?php echo $ls_disabled;?>>
              <option value="-">---seleccione---</option>
              <option value="CIF"  <?php if($ls_concom=="FAS"){ print 'selected';}  ?>>CIF</option>
              <option value="FAS"  <?php if($ls_concom=="FAS"){ print 'selected';}  ?>>FAS</option>
              <option value="FOB"  <?php if($ls_concom=="FOB"){ print 'selected';}  ?>>FOB</option>
              <option value="OTRO" <?php if($ls_concom=="OTRO"){ print 'selected';} ?>>OTROS</option>
            </select></td>
            <td>&nbsp;</td>
            <td height="22">&nbsp;</td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Forma de Pago</td>
            <td height="22">
              <select name="cmbforpag"  style="width:120px" id="cmbforpag" onChange="javascript: ue_validar();" <?php echo $ls_disabled;?>>
                <option value="-">---seleccione---</option>
                <option value="CONTADO" <?php if($ls_forpag=="CONTADO"){ print 'selected';} ?>>CONTADO</option>
                <option value="CREDITO" <?php if($ls_forpag=="CREDITO"){ print 'selected';} ?>>CREDITO</option>
                <option value="CHEQUE"  <?php if($ls_forpag=="CHEQUE"){ print 'selected';} ?>>CHEQUE</option>
                <option value="CARCRE"  <?php if($ls_forpag=="CARCRE"){ print 'selected';} ?>>CARTA DE CREDITO</option>
                <option value="ABOCUE"  <?php if($ls_forpag=="ABOCUE"){ print 'selected';} ?>>ABONO EN CUENTA</option>
                <option value="OTROS"   <?php if($ls_forpag=="OTROS"){ print 'selected';}  ?>>OTROS</option>
			  </select>            </td>
            <td width="70" height="22" style="text-align:right">Plazo en dias </td>
            <td width="141" height="22"><input name="txtnumdiacre"  style="text-align:right" type="text" id="txtnumdiacre" value="<?php print $ls_numdiacre; ?>" size="10" maxlength="25" onKeyPress=" return keyRestrict(event,'1234567890'); " onKeyUp="javascript: ue_validar();"<?php echo $ls_disabled;?>></td>
            <td width="97"><div align="right"><span style="text-align:right">Anticipo de Pago</span></div></td>
            <td height="22"><input name="txtantpag"  style="text-align:right" type="text" id="txtantpag" onKeyPress="return(ue_formatonumero(this,'.',',',event));" value="<?php  print $ld_antpag; ?>" size="25" maxlength="25" <?php echo $ls_disabled;?>></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Pago Electr&oacute;nico </td>
            <td height="22" colspan="5" style="text-align:left">
			<select name="cmbpagele"  style="width:120px" id="cmbpagele" onChange="javascript: ue_formapago();">
              <option value="F" <?php if($ls_pagele=="F"){ print 'selected';} ?>>NO</option>
              <option value="E" <?php if($ls_pagele=="E"){ print 'selected';} ?>>SI</option>
            </select></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Modalidad de la Clausula</td>
            <td height="22" colspan="5" style="text-align:left">
              <input name="txtcodtipmod" type="text" id="txtcodtipmod" value="<?php  print $ls_codtipmod; ?>" size="20" readonly style="text-align:center">
              <a href="javascript: ue_catalogo('sigesp_soc_cat_modalidad_clausulas.php');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
              <input name="txtdenmodcla" type="text"  style="text-align:left "class="sin-borde" id="txtdenmodcla" value="<?php  print  $ls_denmodcla; ?>" size="95" readonly>            </td>
          </tr>
          <tr>
            <td height="35" style="text-align:right">Concepto</td>
            <td height="35" colspan="5"><textarea <?php echo $ls_disabled;?> name="txtconordcom" cols="90" wrap="physical" id="txtconordcom" style="text-align:left" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz????? '+'?!:;_?#@/??%&$*-,.+(){}[]='); "><?php print $ls_conordcom; ?></textarea></td>
          </tr>
          <tr>
            <td height="35" style="text-align:right">Unidades Asociadas</td>
            <td height="35" colspan="5"><textarea readonly name="txtuniejeaso" cols="90" id="txtuniejeaso" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn&ntilde;opqrstuvwxyz&aacute;&eacute;&iacute;&oacute;&uacute; '+'&iexcl;!:;_&deg;#@/?&iquest;%&$*-,.+(){}[]='); "><?php print $ls_uniejeaso; ?></textarea></td>
          </tr>
          <tr>
            <td height="35" style="text-align:right">Observaci&oacute;n</td>
            <td height="35" colspan="5"><label>
              <textarea name="txtobscom" cols="90" <?php echo $ls_disabled;?> id="txtobscom" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz????? '+'?!:;_?#@/??%&$*-,.+(){}[]='); "><?php print $ls_obscom ?></textarea>
            </label></td>
          </tr>
		  <tr>
            <td height="35" style="text-align:right">Concepto de Anulaci&oacute;n</td>
            <td height="35" colspan="5"><label>
              <textarea name="txtconordanu" cols="90" id="txtconordanu" onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz????? '+'?!:;_?#@/??%&$*-,.+(){}[]='); " disabled><?php print $ls_conordcomanu ?></textarea>
            </label></td>
          </tr>
          <tr>
            <td height="13" colspan="3" class="titulo-celdanew">CONDICIONES DE LA ENTREGA </td>
            <td height="13" colspan="3" class="titulo-celdanew"><div align="center">SEGURO</div></td>
          </tr>
          <tr>
            <td height="22" style="text-align:right">Dependencia</td>
            <td height="22" colspan="2" style="text-align:left"><input name="txtlugentnomdep" type="text" id="txtlugentnomdep" value="<?php print $ls_lugentnomdep; ?>" size="25" maxlength="25" <?php echo $ls_disabled;?>></td>
            <td height="22">&nbsp;</td>
            <td height="22" style="text-align:right">Seguro</td>
            <td height="22" style="text-align:left"><input name="chkbestsegcom" type="checkbox" class="sin-borde" id="chkbestsegcom" value="1" <?php echo $ls_disabled;?>></td>
          </tr>
          <tr>
            <td height="20" style="text-align:right">Direcci&oacute;n</td>
            <td height="20" colspan="2"><input name="txtlugentdir" type="text" id="txtlugentdir" value="<?php print $ls_lugentdir; ?>" size="40" maxlength="100" <?php echo $ls_disabled;?>></td>
            <td height="20">&nbsp;</td>
            <td height="20" style="text-align:right">Porcentaje</td>
            <td height="20" style="text-align:left"><input name="txtporsegcom" type="text" id="txtporsegcom"  style="text-align:right" onKeyPress="return(ue_formatonumero(this,'.',',',event));" value="<?php  print $ld_porsegcom;  ?>" size="15" maxlength="10" <?php echo $ls_disabled;?>></td>
          </tr>
          <tr>
            <td height="20" style="text-align:right">Plazo en D&iacute;as</td>
            <td height="20" colspan="2"><input name="txtdiaplacom"  style="text-align:right" type="text" id="txtdiaplacom" value="<?php print $ls_diaplacom; ?>" size="25" maxlength="25" onKeyPress="return keyRestrict(event,'1234567890'); " <?php echo $ls_disabled;?>></td>
            <td height="20">&nbsp;</td>
            <td height="20" style="text-align:right">Monto</td>
            <td height="20"><input name="txtmonsegcom"  style="text-align:right" type="text" id="txtmonsegcom" onKeyPress="return(ue_formatonumero(this,'.',',',event));" value="<?php  print  $ld_monsegcom; ?>" size="25" maxlength="25" <?php echo $ls_disabled;?>></td>
          </tr>
		    <?php
			  if(($ls_perentdesde=="")||($ls_perenthasta==""))
			  {
			      $ls_perentdesde=$ld_fecordcom;
				  $ls_perenthasta=$ld_fecordcom;
			  }
		  ?>
          <tr>
            <td height="20" style="text-align:right">Per&iacute;odo de Entrega Desde</td>
            <td height="20"><input name="txtperentdesde" type="text" id="txtperentdesde" style="text-align:center"  value="<?php echo $ls_perentdesde ?>" size="15" maxlength="10" datepicker="true" <?php echo $ls_disabled; ?> onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);"  onFocus="DiferenciaFechas();"></td>
            <td height="20" style="text-align:right">Hasta</td>
            <td height="20"><input name="txtperenthasta" type="text" id="txtperenthasta" style="text-align:center"  value="<?php echo $ls_perenthasta ?>" size="15" maxlength="10" datepicker="true" <?php echo $ls_disabled; ?> onKeyDown="javascript:ue_formato_fecha(this,'/',patron,true,event);"  onFocus="DiferenciaFechas();"></td>
            <td height="20">&nbsp;</td>
            <td height="20">&nbsp;</td>
          </tr>
          <tr>
            <td height="20" style="text-align:right">No. Contrato Asociado </td>
            <td height="20"><input name="txtcodconobr" type="text" id="txtcodconobr" value="<?php print $ls_codconobr;?>" size="24" maxlength="20"   style="text-align:center"  onKeyPress="return keyRestrict(event,'1234567890'+'abcdefghijklmn?opqrstuvwxyz????? '+'?!:;_?#@/??%&$*-,.+(){}[]='); "></td>
            <td height="20" style="text-align:right">&nbsp;</td>
            <td height="20" colspan="2"><div align="right">Pago de 3% por concepto de R.Social </div></td>
            <td height="20"><input name="chkressoc" type="checkbox" class="sin-borde" id="chkressoc" value="1" <?php echo $ls_chkressoc;?>></td>
          </tr>
          <tr>
            <td height="14" colspan="3" class="titulo-celdanew">UBICACION GEOGRAFICA </td>
            <td height="14" colspan="3" class="titulo-celdanew"><div align="center">MONEDA EXTRANJERA </div></td>
          </tr>
          <tr>
            <td height="22"><div align="right">
              <input name="despai"        type="hidden" id="despai"        value="<?php print $ls_despai; ?>">
              Pais</div></td>
            <td height="22" colspan="2"><div  id="pais" align="left">
              <?php $io_soc->uf_soc_combo_paises($ls_codpai."-".$ls_despai); ?>
            </div></td>
            <td height="22" colspan="2" style="text-align:right">Tipo Moneda</td>
            <td height="22"><div align="left">
              <input name="txtcodmon" type="text" id="txtcodmon" value="<?php  print $ls_codmon;  ?>" size="5" maxlength="3" readonly>
              <a href="javascript: ue_catalogo('sigesp_soc_cat_moneda.php');"><img src="../shared/imagebank/tools15/buscar.gif" alt="Buscar" width="15" height="15" border="0"></a>
              <input name="txtdenmon" type="text" class="sin-borde" id="txtdenmon" value="<?php  print $ls_denmon;  ?>" size="22" maxlength="50" readonly>
              </div></td>
          </tr>
          <tr>
            <td height="22"><div align="right">
              <input name="desest"        type="hidden" id="desest"        value="<?php print $ls_desest; ?>">
              Estado</div></td>
            <td height="22" colspan="2"><div id="estado" align="left">
              <?php $io_soc->uf_soc_combo_estado($ls_desest,$ls_codpai);?>
            </div></td>
            <td height="22" colspan="2" style="text-align:right">Tasa de Cambio</td>
            <td height="22"><input name="txttascamordcom"  style="text-align:right" type="text" id="txttascamordcom" value="<?php  print $ld_tascamordcom; ?>" size="15" maxlength="10" readonly>            </td>
          </tr>
          <tr>
            <td height="22"><div align="right">
              <input name="desmun"        type="hidden" id="desmun"        value="<?php print $ls_denmun; ?>">
              Municipio</div></td>
            <td height="22" colspan="2"><div  id="municipio" align="left">
              <?php $io_soc->uf_soc_combo_municipio($ls_denmun,$ls_codpai,$ls_codest);?>
            </div></td>
            <td height="22" colspan="2" style="text-align:right">Monto en Divisas</td>
            <td height="22"><input name="txtmontotdiv" style="text-align:right" type="text" id="txtmontotdiv" onKeyPress="return(ue_formatonumero(this,'.',',',event));" value="<?php  print $ld_montotdiv;  ?>" size="25" maxlength="25" <?php echo $ls_disabled;?>></td>
          </tr>
          <tr>
            <td height="21"><div align="right">Parroquia</div></td>
            <td height="21" colspan="2"><div id="parroquia" align="left">
              <?php $io_soc->uf_soc_combo_parroquia($ls_denpar,$ls_codpai,$ls_codest,$ls_codmun);?>
            </div></td>
            <td height="21" colspan="2"><div align="right">Sistema Cambiario </div></td>
            <td height="21"><select name="cmbtipsiscam" id="cmbtipsiscam">
              <option value="-"<?php if($ls_tipsiscam==""){ print 'selected';} ?>>-- Seleccione Uno--</option>
              <option value="SIMADI" <?php if($ls_tipsiscam=="SIMADI"){ print 'selected';} ?>>SIMADI</option>
              <option value="CENCOEX" <?php if($ls_tipsiscam=="CENCOEX"){ print 'selected';} ?>>CENCOEX</option>
            </select>            </td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="2">&nbsp;</td>
            <td height="21">&nbsp;</td>
            <td height="21">&nbsp;</td>
            <td height="21">&nbsp;</td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="4"><div align="center">
              <input name="btncopiar" type="button" class="boton" id="btncopiar" value="Obtener datos de Orden de Compra" onClick="javascript: ue_copiar_orden();">
              <a href="javascript: ue_verusuarios();">
              <input name="btnver" type="button" class="boton" id="btnver" value="Ver Usuarios">
              </a></div></td>
            <td height="21">&nbsp;</td>
          </tr>
          <tr>
            <td height="21">&nbsp;</td>
            <td height="21" colspan="4"><div id="usuarios"></div></td>
            <td height="21">&nbsp;</td>
          </tr>
        </table>
        <table width="850" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center"><div id="bienesservicios"></div></td>
          </tr>
        </table>
        <p>
          <input name="existe" type="hidden" id="existe" value="<?php print $ls_existe;?>">
          <input name="numordcomser"  type="hidden" id="numordcomser"  value="<?php print $ls_numordcomser; ?>">
          <input name="numordcombie"  type="hidden" id="numordcombie"  value="<?php print $ls_numordcombie; ?>">
		  <input name="txtcodestpro1" type="hidden" id="txtcodestpro1" value="<?php print $ls_codestpro1;?>">
		  <input name="txtcodestpro2" type="hidden" id="txtcodestpro2" value="<?php print $ls_codestpro2;?>">
		  <input name="txtcodestpro3" type="hidden" id="txtcodestpro3" value="<?php print $ls_codestpro3;?>">
          <input name="txtcodestpro4" type="hidden" id="txtcodestpro4" value="<?php print $ls_codestpro4;?>">
          <input name="txtcodestpro5" type="hidden" id="txtcodestpro5" value="<?php print $ls_codestpro5;?>">
          <input name="hidestcla"     type="hidden" id="hidestcla" value="<?php echo $ls_estcla ?>">
          <input name="totrowbienes"  type="hidden" id="totrowbienes"     value="<?php print $li_totrowbienes;?>">
          <input name="totrowservicios"  type="hidden" id="totrowservicios"  value="<?php print $li_totrowservicios;?>">
          <input name="totrowcargos"  type="hidden" id="totrowcargos"  value="<?php print $li_totrowcargos;?>">
          <input name="totrowcuentas" type="hidden" id="totrowcuentas" value="<?php print $li_totrowcuentas;?>">
          <input name="totrowcuentascargo" type="hidden" id="totrowcuentascargo" value="<?php print $li_totrowcuentascargo;?>">
          <input name="parametros"    type="hidden" id="parametros"    value="<?php print $ls_parametros;?>">
          <input name="txtestcom"     type="hidden" id="txtestcom"     value="<?php print $ls_estcom;?>">
          <input name="txtestapro"    type="hidden" id="txtestapro"    value="<?php print $ls_estapro; ?>">
          <input name="txttipsol"     type="hidden" id="txttipsol"     value="<?php print $ls_tipsol; ?>">
          <input name="tipord"        type="hidden" id="tipord"        value="<?php print $ls_tipordcom; ?>">
          <input name="txtfecha"      type="hidden" id="txtfecha"      value="<?php print $ld_fecordcom; ?>">
          <input name="formato"       type="hidden" id="formato"       value="<?php print $ls_reporte; ?>">
          <input name="tipo"          type="hidden" id="tipo"          value="<?php print $ls_tipo; ?>">
          <input name="numanacot"     type="hidden" id="numanacot"     value="<?php print $ls_numanacot; ?>">
          <input name="numsoldel"     type="hidden" id="numsoldel">
          <input name="crearasiento" type="hidden" id="crearasiento" value="0">
          <input name="recalcular" type="hidden" id="recalcular" value="1">
        </p></td>
    </tr>
</table>
</form>
<?php
	$io_soc->uf_destructor();
	unset($io_soc);
?>
<p>&nbsp;</p>
</body>
<script >
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);
f = document.formulario;
function ue_formapago()
{
	f=document.formulario;
	f.crearasiento.value=0;
}

function ue_copiar_orden()
{
    ls_tipordcom = f.tipord.value;
	tipsol="SOC";
	ls_tipo="COPIAR";
	f.recalcular.value=0;
	totalservicios=ue_calcular_total_fila_local("txtcodser");
	totalbienes=ue_calcular_total_fila_local("txtcodart");
	if((totalservicios>1)||(totalbienes>1))
	{
		alert("No se pueden tener detalles registrados para copiar una Orden de Compra");
	}
	else
	{
		if (ls_tipordcom=='B' || ls_tipordcom=='S')
		{
			ls_codpro = f.txtcodprov.value;
			if(ls_codpro!="")
			{
				ls_codunieje = f.txtcodunieje.value;
				if(ls_codunieje)
				{
					window.open("sigesp_soc_cat_orden_compra.php?origen=OC&tipsol="+tipsol+"&tipordcom="+ls_tipordcom+"&tipo="+ls_tipo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=650,height=400,left=50,location=no,resizable=yes");
				}
				else
				{
					alert("Debe seleccionar la Unidad Ejecutora");
				}
			}
			else
			{
				alert("Debe seleccionar el Proveedor");
			}
		}
		else
		{
			alert("Debe seleccionar Tipo de Orden, Bienes y/o Servicios !!!");
		}
	}

}
function ue_cargar_solicitud()
{
  if (uf_evaluate_cierre())
     {
	   li_estapro = f.txtestapro.value;
	   if (li_estapro==1)
		  {
		    alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		  }
	   else
		  {
		    ls_codpro    = f.txtcodprov.value;
		    ls_tipconpro = f.tipconpro.value;
		    ls_tipordcom = f.tipord.value;
		    ls_tipsol    = f.txttipsol.value;
			ld_fecemioc  = f.txtfecordcom.value;
		    if ((ls_tipsol=="SEP")||(ls_tipsol==""))
			   {
				 if (ls_tipordcom=='B' || ls_tipordcom=='S')
				    {
					  if (ls_codpro!="")
						 {
						   if (ls_tipconpro!="")
							  {
							    window.open("sigesp_soc_cat_solicitud_presupuestaria.php?tipord="+ls_tipordcom+"&fecemioc="+ld_fecemioc,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=650,height=400,left=50,top=50,location=no,resizable=no");
							  }
						   else
							  {
							    alert("El Proveedor no tiene Tipo de Contribuyente asociado !!!");
							  }
						 }
					  else
						 {
						   alert("Debe seleccionar un Proveedor !!!");
						 }
				    }
				 else
				    {
					  alert("Debe seleccionar Tipo de Orden, Bienes y/o Servicios !!!");
				    }
			   }
		    else
			   {
			     alert("Si desea procesar Solicitudes de Ejecuci?n Presupuestaria, No indique Unidad Ejecutora ya que estas vienen definidas desde las mismas !!!");
			   }
		  }
	 }
}

function ue_catalogo(ls_catalogo)
{
  li_fila=0;
  if (uf_evaluate_cierre())
     {
	   li_estapro = f.txtestapro.value;
	   if (li_estapro==1)
	      {
	        alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
 	      }
	   else
	      {
	        if (ls_catalogo=='sigesp_cat_unidad_ejecutora.php')
		       {
			     ls_tipsol = f.txttipsol.value;
			     ls_tipo   = f.tipo.value;
			     lb_existe = f.existe.value;
				 ls_concepto=f.txtconordcom.value;
				 ls_unidedeje=f.txtuniejeaso.value;
				 ls_tipordcom = f.tipord.value;
				 if(ls_tipordcom=="B")
				 {
				   li_fila=f.totrowbienes.value;
				 }
				 else
				 {
				   li_fila=f.totrowservicios.value;
				 }
	   		     if ((ls_tipsol=="SEP")||(lb_existe=="TRUE")||(li_fila>1))
				    {
					    alert("No puede modificarse la Unidad Ejecutora !!!");
					}
			     else
				    {
					  if ((f.totrowbienes.value>1)||(f.totrowservicios.value>1)||(f.totrowcargos.value>0)||(f.totrowcuentas.value>1)||(f.totrowcuentascargo.value>1))
						 {
						   alert("No puede modificarse la Unidad Ejecutora, existen movimientos en la solicitud !!!");
						 }
					  else
						 {
						   window.open(ls_catalogo,"_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
						 }
				    }
			   }
		    else
			{
				// abre el catalogo que se paso por parametros
				estmodest=<?php print $li_estmodest;?>;
				if (estmodest==1)
				{
					codestpro1=f.txtcodestpro1.value;
					codestpro2=f.txtcodestpro2.value;
					codestpro3=f.txtcodestpro3.value;
					codestpro4="0000000000000000000000000";
					codestpro5="0000000000000000000000000";
				}
				else
				{
					codestpro1=f.txtcodestpro1.value;
					codestpro2=f.txtcodestpro2.value;
					codestpro3=f.txtcodestpro3.value;
					codestpro4=f.txtcodestpro4.value;
					codestpro5=f.txtcodestpro5.value;
				}
				estcla=f.hidestcla.value;
				if(codestpro1!="")
				 {
					window.open(ls_catalogo+"?codestpro1="+codestpro1+"&codestpro2="+codestpro2+"&codestpro3="+codestpro3+"&codestpro4="+codestpro4+"&codestpro5="+codestpro5+"&estcla="+estcla+"","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
				 }
				 else
				  {
					alert("Debe seleccionar la Estructura Programtica");
				  }
			}

	      }
	 }
}

function ue_catalogo_proveedor()
{
  if (uf_evaluate_cierre())
     {
	   // abre el catalogo que se paso por parametros
	   li_estapro = f.txtestapro.value;
	   
	   if (li_estapro==1)
	      {
		    alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		  }
	   else
		  {
		    lb_existe    = f.existe.value;
		    f.cmbtipordcom.disabled = false;
		    ls_tipordcom = f.cmbtipordcom.value;
		    f.cmbtipordcom.disabled = true;
		    if (ls_tipordcom=="-")
			   {
			     alert("Debe seleccionar Tipo de Orden, Bienes y/o Servicios !!!");
			   }
		    else
		 	   {
			     lb_valido = true;
			     /*if (ls_tipordcom=="B")
				    {
				      Comentado por Organismos que cargan data previa a la adquisici?n del SIGESP y manejan OC mezcladas.
					  if (f.radiotipbieordcom[0].checked==false && f.radiotipbieordcom[1].checked==false)
					     {
						   lb_valido = false;
						   alert("Seleccione tipo de Items a Incorporar, Materiales y/o Suministros ? Activos !!!");
						   f.radiotipbieordcom[0].focus();
					     }
				    }*/
			     if (lb_existe!="TRUE" && lb_valido)
				    {
					  fecordcom=f.txtfecordcom.value;
					  f.txtfecha.value=fecordcom;
	  				  f.txtfecordcom.disabled=true;
				      window.open("sigesp_soc_cat_proveedor.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
				    }
			   }
	      }
	 }
}

function ue_nuevo()
{
  if (uf_evaluate_cierre())
     {
	   li_incluir=f.incluir.value;
	   if (li_incluir==1)
		  {
		    f.operacion.value="NUEVO";
			f.existe.value="FALSE";
			f.action="sigesp_soc_p_registro_orden_compra.php";
	 		f.submit();
		  }
	   else
		  {
		    alert("No tiene permiso para realizar esta operacion");
		  }
	 }
}

function ue_cambiar_estado()
{
	estapro=f.txtestapro.value;
	if(estapro=="1")
	{
		alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	}
	else
	{
		// Cargamos las variables para pasarlas al AJAX
		cmbpais=f.cmbpais.value;
		tipo="ESTADOS";
		despai=f.despai.value;
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById('estado');
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde est?n los m?todos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divgrid.innerHTML = ajax.responseText
			}
		}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("tipo="+tipo+"&cmbpais="+cmbpais+"&despai="+despai+"&proceso=CARGAR-COMBO");
	}
}

function ue_cambiar_municipio()
{
	estapro=f.txtestapro.value;
	if(estapro=="1")
	{
		alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	}
	else
	{
		// Cargamos las variables para pasarlas al AJAX
		cmbpais=f.cmbpais.value;
		cmbestado=f.cmbestado.value;
		tipo="MUNICIPIOS";
		desest=f.desest.value;
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById('municipio');
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde est?n los m?todos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divgrid.innerHTML = ajax.responseText
			}
		}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("tipo="+tipo+"&cmbpais="+cmbpais+"&cmbestado="+cmbestado+"&desest="+desest+"&proceso=CARGAR-COMBO");
	}
}

function ue_cambiar_parroquia()
{
	estapro=f.txtestapro.value;
	if(estapro=="1")
	{
		alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	}
	else
	{
		// Cargamos las variables para pasarlas al AJAX
		cmbpais=f.cmbpais.value;
		cmbestado=f.cmbestado.value;
		cmbmunicipio=f.cmbmunicipio.value;
		tipo="PARROQUIAS";
		desmun=f.desmun.value;
		// Div donde se van a cargar los resultados
		divgrid = document.getElementById('parroquia');
		// Instancia del Objeto AJAX
		ajax=objetoAjax();
		// Pagina donde est?n los m?todos para buscar y pintar los resultados
		ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divgrid.innerHTML = ajax.responseText
			}
		}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		// Enviar todos los campos a la pagina para que haga el procesamiento
		ajax.send("tipo="+tipo+"&cmbpais="+cmbpais+"&cmbestado="+cmbestado+"&cmbmunicipio="+cmbmunicipio+"&desmun="+desmun+"&proceso=CARGAR-COMBO");
	}
}

function uf_load_catalogo()
{
  if (uf_evaluate_cierre())
     {
	   lb_existe  = f.existe.value;
	   if (lb_existe!="TRUE")
		  {
		    ls_codpro = f.txtcodprov.value;
		    if (ls_codpro=='')
			   {
				 window.open("sigesp_soc_cat_proveedor.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
			   }
		  }
	   tipordcom=f.tipord.value;
	   f.totrowbienes.value=1;
	   f.totrowservicios.value=1;
	   f.totrowcargos.value=0;
	   f.totrowcuentas.value=1;
	   f.totrowcuentascargo.value=1;
	   // Div donde se van a cargar los resultados
	   divgrid = document.getElementById('bienesservicios');
	   // Instancia del Objeto AJAX
	   ajax=objetoAjax();
	   // Pagina donde est?n los m?todos para buscar y pintar los resultados
	   ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
	   ajax.onreadystatechange=function() {
	   if (ajax.readyState==4) {
		   divgrid.innerHTML = ajax.responseText
		  }
	   }
	   ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	   // Enviar todos los campos a la pagina para que haga el procesamiento
	   ajax.send("tipo="+tipordcom+"&totalbienes=1&totalservicios=1&totalcargos=0"+"&totalcuentas=1&totalcuentascargo=1&proceso=LIMPIAR");
	 }
}

function ue_generar_numero(as_readonly)
{
    li_estapro = f.txtestapro.value;
	if (li_estapro==1)
	   {
	     alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
 	   }
	else
	   {
	     if (f.cmbtipordcom[1].selected==true)
			{
			  f.tipord.value="B";
			  numordcombie=f.numordcombie.value;
			  if (numordcombie==false)
			     {
				   alert("Este documento est? configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor dir?jase al Administrador del Sistema");
				   location.href='sigespwindow_blank.php';
				 }
			  else
			     {
			       f.txtnumordcom.value=numordcombie;
				   if (as_readonly=='')
					  {
					    f.txtnumordcom.readOnly = false;
					  }
				   else
					  {
					    f.txtnumordcom.readOnly = true;
					  }
				   f.cmbtipordcom.disabled = true;
			       f.radiotipbieordcom[0].disabled = false;
			       f.radiotipbieordcom[1].disabled = false;
				 }
			}
		 if (f.cmbtipordcom[2].selected==true)
			{
			  f.tipord.value="S";
			  numordcomser=f.numordcomser.value;
			  if (numordcomser==false)
			     {
				   alert("Este documento est? configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor dir?jase al Administrador del Sistema");
				   location.href='sigespwindow_blank.php';
				 }
			  else
			     {
				   f.txtnumordcom.value=numordcomser;
				   if (as_readonly=='')
					  {
					    f.txtnumordcom.readOnly = false;
					  }
				   else
					  {
					    f.txtnumordcom.readOnly = true;
					  }
				   f.cmbtipordcom.disabled = true;
				   lb_existe  = f.existe.value;
				   if (lb_existe!="TRUE")
					  {
					    ls_codpro = f.txtcodprov.value;
					    if (ls_codpro=='')
						   {
							// window.open("sigesp_soc_cat_proveedor.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
						   }
					  }
				 }
			}
			tipordcom=f.tipord.value;
			f.totrowbienes.value=1;
			f.totrowservicios.value=1;
			f.totrowcargos.value=0;
			f.totrowcuentas.value=1;
			f.totrowcuentascargo.value=1;
			// Div donde se van a cargar los resultados
			divgrid = document.getElementById('bienesservicios');
			// Instancia del Objeto AJAX
			ajax=objetoAjax();
			// Pagina donde est?n los m?todos para buscar y pintar los resultados
			ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divgrid.innerHTML = ajax.responseText
					f.operacion.value="PREFIJO";
					f.action="sigesp_soc_p_registro_orden_compra.php";
					f.submit();
				}
			}
			ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			// Enviar todos los campos a la pagina para que haga el procesamiento
			ajax.send("tipo="+tipordcom+"&totalbienes=1&totalservicios=1&totalcargos=0"+"&totalcuentas=1&totalcuentascargo=1&proceso=LIMPIAR");
	      }
}

function ue_catalogo_bienes()
{
  numanacot=f.numanacot.value;
  if(numanacot=="-" || numanacot=="")
  {
	  if (uf_evaluate_cierre())
		 {
		   li_estapro=f.txtestapro.value;
		   if (li_estapro=="1")
			  {
				alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
			  }
		   else
			  {
				//Se carga el catalogo de Bienes, Si no se ha elegido la unidad ejecutora no se carga
				ls_codestpro1 = f.txtcodestpro1.value;
				ls_codestpro2 = f.txtcodestpro2.value;
				ls_codestpro3 = f.txtcodestpro3.value;
				ls_codestpro4 = f.txtcodestpro4.value;
				ls_codestpro5 = f.txtcodestpro5.value;
				ls_estcla     = f.hidestcla.value;
				ls_tipo       = f.tipo.value;
				ls_codpro     = f.txtcodprov.value;
				ls_tipconpro  = f.tipconpro.value;
				ls_tipsol=f.txttipsol.value;
				if (ls_codpro=="" || ls_tipconpro=="")
				   {
					 alert("Debe seleccionar un Proveedor !!!");
				   }
				else
				   {
					 if ((ls_codestpro1!="")&&(ls_codestpro2!="")&&(ls_codestpro3!="")&&(ls_codestpro4!="")&&(ls_codestpro5!="")&&(ls_estcla!=""))
						{
						  window.open("sigesp_soc_cat_bienes.php?tipo="+ls_tipo+"&tipsol="+ls_tipsol,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");
						}
					 else
						{
						  alert("Debe seleccionar una Unidad Ejecutora !!!");
						}
				   }
			  }
		 }
	}
	else
	{
		alert("La orden de compra proviene de un analisis de cotizacion, este campo no se puede modificar");
	}
}

function ue_catalogo_servicios()
{
  numanacot=f.numanacot.value;
  if(numanacot=="-" || numanacot=="")
  {
	  if (uf_evaluate_cierre())
		 {
		   li_estapro=f.txtestapro.value;
		   if (li_estapro=="1")
			  {
				alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
			  }
		   else
			  {
				// Se carga el catalogo de Bienes, Si no se ha elegido la unidad ejecutora no se carga
				ls_codestpro1 = f.txtcodestpro1.value;
				ls_codestpro2 = f.txtcodestpro2.value;
				ls_codestpro3 = f.txtcodestpro3.value;
				ls_codestpro4 = f.txtcodestpro4.value;
				ls_codestpro5 = f.txtcodestpro5.value;
				ls_estcla     = f.hidestcla.value;
				ls_tipo       = f.tipo.value;
				ls_codpro     = f.txtcodprov.value;
				ls_tipconpro  = f.tipconpro.value;
				ls_tipsol=f.txttipsol.value;
				if (ls_codpro=="" || ls_tipconpro=="")
				   {
					 alert("Debe seleccionar un Proveedor !!!");
				   }
				else
				   {
					 if ((ls_codestpro1!="")&&(ls_codestpro2!="")&&(ls_codestpro3!="")&&(ls_codestpro4!="")&&(ls_codestpro5!="")&&(ls_estcla!=""))
						{
						  window.open("sigesp_soc_cat_servicios.php?tipo="+ls_tipo+"&tipsol="+ls_tipsol,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");
						}
					 else
						{
						  alert("Debe seleccionar una Unidad Ejecutora !!!");
						}
				   }
			  }
		 }
	}
	else
	{
		alert("La orden de compra proviene de un analisis de cotizacion, este campo no se puede modificar");
	}
}

function ue_procesar_monto(tipo,fila)
{
if (uf_evaluate_cierre())
   {
	estapro=0;//f.txtestapro.value;
	if(estapro=="1")
	{
		alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	}
	else
	{
		// Obtenemos el total de filas de los servicios
		total=ue_calcular_total_fila_local("txtcodser");
		f.totrowservicios.value=total;
		// Obtenemos el total de filas de los bienes
		total=ue_calcular_total_fila_local("txtcodart");
		f.totrowbienes.value=total;
		// Obtenemos el total de filas de los cargos
		total=ue_calcular_total_fila_local("txtcodservic");
		f.totrowcargos.value=total;
		// Obtenemos el total de filas de las cuentas
		total=ue_calcular_total_fila_local("txtcuentagas");
		f.totrowcuentas.value=total;
		// Obtenemos el total de filas de las cuentas
		total=ue_calcular_total_fila_local("txtcuentacar");
		f.totrowcuentascargo.value=total;
		f.crearasiento.value=0;
		if(tipo=="B")
		{
			// Cargamos los valores de la fila indicada
			codart=eval("f.txtcodart"+fila+".value");
			canart=eval("f.txtcanart"+fila+".value");
			preart=eval("f.txtpreart"+fila+".value");
			unidad=eval("f.cmbunidad"+fila+".value");
			canunidad=eval("f.txtunidad"+fila+".value");
			spgcuenta=eval("f.txtspgcuenta"+fila+".value");
			canart=ue_formato_calculo(canart);
			preart=ue_formato_calculo(preart);
			// Si es una fila que tiene Art?culos
			if(codart!="")
			{
				// Si la cantidad de art?culos ? el precio es mayor que cero calculamos
				if((canart>0)||(preart>0))
				{
/*					if(unidad=="M")
					{
						// si es al mayor multiplicamos la cantidad tipeada por la cantidad de la unidad
						canart=eval(canart+"*"+canunidad);
					}
*/					monnet=canart*preart;
					totalarticulo=0;
					monnet=redondear(monnet,2);
					eval("f.txtsubtotart"+fila+".value='"+uf_convertir(monnet)+"'");
					totalcargo=0;
					// Actualizamos los cr?ditos de ese art?culo si tiene
					ue_actualizar_creditos(codart,monnet,fila);
					totalarticulo=eval(monnet+"+"+totalcargo);
					totalarticulo=redondear(totalarticulo,2);
					totalcargo=redondear(totalcargo,2);
					eval("f.txtcarart"+fila+".value='"+uf_convertir(totalcargo)+"'");
					eval("f.txttotart"+fila+".value='"+uf_convertir(totalarticulo)+"'");
					// Actualizamos las cuentas presupuestarias
					//ue_actualizar_cuentas(tipo,spgcuenta);
					// Actualizamos los totales de la solicitud
					//ue_actualizar_totales(tipo);
				}
			}
		}
		if(tipo=="S")
		{
			// Cargamos los valores de la fila indicada
			codser=eval("f.txtcodser"+fila+".value");
			canser=eval("f.txtcanser"+fila+".value");
			preser=eval("f.txtpreser"+fila+".value");
			canser=ue_formato_calculo(canser);
			preser=ue_formato_calculo(preser);
			spgcuenta=eval("f.txtspgcuenta"+fila+".value");
			// Si es una fila que tiene Servicios
			if(codser!="")
			{
				// Si la cantidad de servicios ? el precio es mayor que cero calculamos
				if((canser>0)||(preser>0))
				{
					monnet=canser*preser;
					totalservicio=0;
					monnet=redondear(monnet,2);
					eval("f.txtsubtotser"+fila+".value='"+uf_convertir(monnet)+"'");
					totalcargo=0;
					// Actualizamos los cr?ditos de ese art?culo si tiene
					ue_actualizar_creditos(codser,monnet,fila);
					totalservicio=eval(monnet+"+"+totalcargo);
					totalservicio=redondear(totalservicio,2);
					eval("f.txtcarser"+fila+".value='"+uf_convertir(totalcargo)+"'");
					eval("f.txttotser"+fila+".value='"+uf_convertir(totalservicio)+"'");
					// Actualizamos las cuentas presupuestarias
					//ue_actualizar_cuentas(tipo,spgcuenta);
					// Actualizamos los totales de la solicitud
					//ue_actualizar_totales(tipo);
				}
			}
		}
	}
   }
}

//----------------------------------------------------
function lTrim(sStr)
{
 while (sStr.charAt(0) == " ")
     sStr = sStr.substr(1, sStr.length - 1);
 return sStr;
}

function rTrim(sStr)
{
 while (sStr.charAt(sStr.length - 1) == " ")
     sStr = sStr.substr(0, sStr.length - 1);
 return sStr;
}
function allTrim(sStr){
  return rTrim(lTrim(sStr));
}  
//----------------------------------------------------
function ue_validar()
{
	tipopago=f.cmbforpag.value;
	if(tipopago!="CREDITO")
	{
		f.txtnumdiacre.value="0";	
	}
}

function ue_actualizar_creditos(codigo,monto,fila)
{
	rowcargo=f.totrowcargos.value;
	for(fila_cargo=1;(fila_cargo<=rowcargo);fila_cargo++)
	{
		codartcargo=eval("f.txtcodservic"+fila_cargo+".value");
		ls_numsep= eval("f.hidnumsepcar"+fila_cargo+".value");
		ls_numsolord=eval("f.txtnumsolord"+fila+".value");
		// Si el codigo del art?culo que se esta actualizando es igual al actual
		codartcargo=allTrim(codartcargo);
		codigo=allTrim(codigo);
		if(codartcargo==codigo)
		{
		   if(ls_numsep==ls_numsolord)
		   {
				cuentacargo=eval("f.cuentacargo"+fila_cargo+".value");
				formula=eval("f.formulacargo"+fila_cargo+".value");
				formula=formula.replace("$LD_MONTO",monto);
				cargo		   = eval(formula);
				cargo		   = redondear(cargo,2);
				totalcargo=eval(totalcargo+"+"+cargo);
				subtotalcargo=eval(monto+"+"+cargo);
				subtotalcargo=redondear(subtotalcargo,2);
				eval("f.txtbascar"+fila_cargo+".value='"+uf_convertir(monto)+"'");
				eval("f.txtmoncar"+fila_cargo+".value='"+uf_convertir(cargo)+"'");
				eval("f.txtsubcargo"+fila_cargo+".value='"+uf_convertir(subtotalcargo)+"'");
		   }
		}
	}
}

function ue_actualizar_cuentas(tipo,spgcuentaact)
{
    f = document.formulario;
	rowcuentas=f.totrowcuentas.value;
	lb_viene=f.txttipsol.value;   //alert(tipo+"  "+spgcuentaact);
	for(fila_cuenta=1;(fila_cuenta<rowcuentas);fila_cuenta++)
	{
		ls_estpre = eval("f.txtcodprogas"+fila_cuenta+".value");
		cuenta    = eval("f.txtcuentagas"+fila_cuenta+".value");
		moncueact = eval("f.txtmoncuegas"+fila_cuenta+".value");
		moncue=0;
		lb_entro=false;
		if(tipo=="B")
		{
			// Recorremos los Bienes para colocar el total de las cuentas
			rowbienes=f.totrowbienes.value;
			for(fila_bienes=1;fila_bienes<rowbienes;fila_bienes++)
			{
				spgcuenta    = eval("f.txtcodspg"+fila_bienes+".value");
				ls_codestpro = eval("f.txtcodgas"+fila_bienes+".value");
                if(lb_viene=='SEP')
				{
					if((cuenta==spgcuenta)&&(cuenta==spgcuentaact)&&(ls_estpre==ls_codestpro))
					{
						montobienes=eval("f.txtsubtotart"+fila_bienes+".value");
						montobienes=ue_formato_calculo(montobienes)
						moncue=eval(moncue+"+"+montobienes);
						moncue=redondear(moncue,2);
						lb_entro=true;
					}
				}
				else
				{   /*alert("FILACUENTA-> "+fila_cuenta+"  "+cuenta+" FILABIEN"+fila_bienes+"  "+spgcuenta);
				alert("FILACUENTA-> "+fila_cuenta+"  "+cuenta+" PARAMETRO"+spgcuentaact);
				alert("FILACUENTA-> "+fila_cuenta+"  "+ls_estpre+" FILABIEN"+fila_bienes+"  "+ls_codestpro);*/
					if((cuenta==spgcuenta)&&(cuenta==spgcuentaact)&&(ls_estpre==ls_codestpro))
					{
						montobienes=eval("f.txtsubtotart"+fila_bienes+".value");
						montobienes=ue_formato_calculo(montobienes);
						moncue=eval(moncue+"+"+montobienes);
						moncue=redondear(moncue,2);
						lb_entro=true;
					}
				}
			}
		}
		if(tipo=="S")
		{
			// Recorremos los Bienes para colocar el total de las cuentas
			totrowservicios=f.totrowservicios.value;
			for(fila_servicios=1;fila_servicios<totrowservicios;fila_servicios++)
			{
				ls_codestpro = eval("f.hidcodestpro"+fila_servicios+".value");
				spgcuenta=eval("f.txtspgcuenta"+fila_servicios+".value");

                if(lb_viene=='SEP')
				{
					if((cuenta==spgcuenta)&&(cuenta==spgcuentaact)&&(ls_estpre==ls_codestpro))
					{
						montoservicios=eval("f.txtsubtotser"+fila_servicios+".value");
						montoservicios=ue_formato_calculo(montoservicios);
						moncue=eval(moncue+"+"+montoservicios);
						moncue=redondear(moncue,2);
						lb_entro=true;
					}
				}
				else
				{
				   if((cuenta==spgcuenta)&&(cuenta==spgcuentaact)&&(ls_estpre==ls_codestpro))
					{
						montoservicios=eval("f.txtsubtotser"+fila_servicios+".value");
						montoservicios=ue_formato_calculo(montoservicios);
						moncue=eval(moncue+"+"+montoservicios);
						moncue=redondear(moncue,2);
						lb_entro=true;
					}
				}
			}
		}
		if(lb_entro)
		{
			eval("f.txtmoncuegas"+fila_cuenta+".value='"+uf_convertir2(moncue)+"'");
		}
		else
		{
			eval("f.txtmoncuegas"+fila_cuenta+".value='"+moncueact+"'");
		}
	}
	rowcuentas=f.totrowcuentascargo.value;
	for(fila_cuenta=1;(fila_cuenta<rowcuentas);fila_cuenta++)
	{
		cuenta=eval("f.txtcuentacar"+fila_cuenta+".value");
		cargo=eval("f.txtcodcargo"+fila_cuenta+".value");
		moncueact=eval("f.txtmoncuecar"+fila_cuenta+".value");
		moncue=0;
		lb_entro=false;
		ls_estpre=eval("f.txtcodprocar"+fila_cuenta+".value");
		ls_estclacar=eval("f.estclacar"+fila_cuenta+".value");
		// Recorremos los Cargos para colocar el total de las cuentas
		rowcargos=f.totrowcargos.value;
		for(fila_cargos=1;fila_cargos<=rowcargos;fila_cargos++)
		{
			spgcuenta=eval("f.cuentacargo"+fila_cargos+".value");
			codcar=eval("f.txtcodcar"+fila_cargos+".value");
			ls_codestpro=eval("f.codprogcargo"+fila_cargos+".value");
			ls_estclacargo=eval("f.estclacargo"+fila_cargos+".value");
			cuenta=allTrim(cuenta);
			spgcuenta=allTrim(spgcuenta);
			cargo=allTrim(cargo);
			codcar=allTrim(codcar);

		    if(lb_viene=='SEP')
		    {
				if((cuenta==spgcuenta)&&(cargo==codcar)&&(ls_estpre==ls_codestpro))
				{
					montocargo=eval("f.txtmoncar"+fila_cargos+".value");
					montocargo=ue_formato_calculo(montocargo);
					moncue=eval(moncue+"+"+montocargo);
					moncue=redondear(moncue,2);
					lb_entro=true;
				}
			}
			else
			{
			  if((cuenta==spgcuenta)&&(cargo==codcar)&&(ls_estpre==ls_codestpro))
				{
					montocargo=eval("f.txtmoncar"+fila_cargos+".value");
					montocargo=ue_formato_calculo(montocargo);
					moncue=eval(moncue+"+"+montocargo);
					moncue=redondear(moncue,2);
					lb_entro=true;
				}
			}

		}
		if(lb_entro)
		{
			eval("f.txtmoncuecar"+fila_cuenta+".value='"+uf_convertir2(moncue)+"'");
		}
		else
		{
			eval("f.txtmoncuecar"+fila_cuenta+".value='"+moncueact+"'");
		}
	}
}

function uf_convertir2(obj)
{
	var valor=new String(obj);
	if(valor<0)
	{
		li_temp="-";
		valor = Math.abs(valor);
		valor = new String(valor);
	}
	else
	{
		li_temp="";
	}
	li_coma=valor.indexOf(',');
	if(li_coma>0)
	{
		while(valor.indexOf('.')>0)
		{
			valor=valor.replace(".","");
		}
		valor=valor.replace(",",".");
	}
	valor=roundNumber(valor);
	var valor=new String(valor);
	li_punto=valor.indexOf('.');
	li_longitud=valor.length;
	if(li_punto>=0)
	{
		ls_new=valor.substr(0,li_punto);
		ldec_monto=roundNumber(valor);
		var aux=new String(ldec_monto);
		ls_dec=aux.substr(li_punto+1,li_longitud-li_punto);
	}
	else
	{
		ls_new=valor;
		ls_dec="00";
	}
	li_long_new=ls_new.length;
	if(li_long_new>3)
	{
		ls_new_int=uf_convertir_entero(ls_new);
	}
	else
	{
		ls_new_int=ls_new;
	}
	if(ls_dec.length<2)
	{
		while(ls_dec.length<2)
		{
			ls_dec=ls_dec+"0";
		}
	}
	else
	{
		ls_dec=ls_dec.substr(0,2);
	}
	return li_temp+ls_new_int+","+ls_dec;
}

function roundNumber(obj)
{
	//var numberField = obj; // Field where the number appears
	var rnum = obj;
	var rlength = 2; // The number of decimal places to round to
	var cantidad = parseFloat(obj);
	var decimales = parseFloat(rlength);
	decimales = (!decimales ? 2 : decimales);
return Math.round(cantidad * Math.pow(10, decimales)) / Math.pow(10, decimales);
}
function rellenar_cad(cadena,longitud)
{
	if (cadena!="")
	{
		var mystring=new String(cadena);
		cadena_ceros="";
		lencad=mystring.length;

		total=longitud-lencad;
		for(i=1;i<=total;i++)
		{
			cadena_ceros=cadena_ceros+"0";
		}
		cadena=cadena_ceros+cadena;
	 }
	 return cadena;
}

function ue_actualizar_totales(tipo)
{
	subtotal=0;
	cargos=0;
	total=0;
	if(tipo=="B")
	{
		rowbienes=f.totrowbienes.value;
		// Recorremos los bienes y sumamos para colocarlo en los totales
		for(fila_bienes=1;fila_bienes<rowbienes;fila_bienes++)
		{
			montobienes=eval("f.txtsubtotart"+fila_bienes+".value");
			montobienes=ue_formato_calculo(montobienes);
			subtotal=eval(subtotal+"+"+montobienes);
			montocargos=eval("f.txtcarart"+fila_bienes+".value");
			montocargos=ue_formato_calculo(montocargos);
			cargos=eval(cargos+"+"+montocargos);
			cargos=redondear(cargos,2);
			montoarticulos=eval("f.txttotart"+fila_bienes+".value");
			montoarticulos=ue_formato_calculo(montoarticulos);
			total=eval(total+"+"+montoarticulos);
		}
	}
	if(tipo=="S")
	{
		rowservicios=f.totrowservicios.value;
		// Recorremos los servicios y sumamos para colocarlo en los totales
		for(fila_servicios=1;fila_servicios<rowservicios;fila_servicios++)
		{
			montoservicios=eval("f.txtsubtotser"+fila_servicios+".value");
			montoservicios=ue_formato_calculo(montoservicios);
			subtotal=eval(subtotal+"+"+montoservicios);
			montocargos=eval("f.txtcarser"+fila_servicios+".value");
			montocargos=ue_formato_calculo(montocargos);
			cargos=eval(cargos+"+"+montocargos);
			cargos=redondear(cargos,2);
			montoservicios=eval("f.txttotser"+fila_servicios+".value");
			montoservicios=ue_formato_calculo(montoservicios);
			total=eval(total+"+"+montoservicios);
		}
	}
	subtotal=redondear(subtotal,2);
	cargos=redondear(cargos,2);
	total=redondear(total,2);
	f.txtsubtotal.value=uf_convertir(subtotal);
	f.txtcargos.value=uf_convertir(cargos);
	f.txttotal.value=uf_convertir(total);
}

function ue_catalogo_cuentas_spg(fila)
{
  scg_cuenta=eval("document.formulario.txtspgcuenta"+fila+".value");
  if (uf_evaluate_cierre())
     {
	   estapro=f.txtestapro.value;
	   if (estapro=="1")
	      {
		    alert("La Orden de Compra est? aprobada no la puede modificar !!!");
 	      }
	   else
	      {
		    //Se carga el catalogo de Cuentas Presupuestarias, Si no se ha elegido la unidad ejecutora no se carga
		    codestpro1 = f.txtcodestpro1.value;
		    codestpro2 = f.txtcodestpro2.value;
		    codestpro3 = f.txtcodestpro3.value;
		    codestpro4 = f.txtcodestpro4.value;
		    codestpro5 = f.txtcodestpro5.value;
		    ls_estcla  = f.hidestcla.value;

		    if ((codestpro1!="")&&(codestpro2!="")&&(codestpro3!="")&&(codestpro4!="")&&(codestpro5!="")&&(ls_estcla!=""))
		       {
			     window.open("sigesp_soc_cat_cuentas_spg.php?scg_cuenta="+scg_cuenta,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");
		       }
		    else
	  	       {
			     alert("Debe seleccionar una Unidad Ejecutora !!!");
		       }
	      }
	 }
}

function ue_catalogo_cuentas_cargos()
{
  if (uf_evaluate_cierre())
     {
	   li_estapro = f.txtestapro.value;
	   if (li_estapro=="1")
	      {
		    alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	      }
	   else
	      {
		    //Se carga el catalogo de Cuentas Presupuestarias de los cargos, Si no se ha elegido la unidad ejecutora no se carga
		    codestpro1 = f.txtcodestpro1.value;
		    codestpro2 = f.txtcodestpro2.value;
		    codestpro3 = f.txtcodestpro3.value;
		    codestpro4 = f.txtcodestpro4.value;
		    codestpro5 = f.txtcodestpro5.value;
		    ls_estcla  = f.hidestcla.value;
		    if ((codestpro1!="")&&(codestpro2!="")&&(codestpro3!="")&&(codestpro4!="")&&(codestpro5!="")&&(ls_estcla!=""))
		       {
			     window.open("sigesp_soc_cat_cuentas_cargos.php","catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50,dependent=yes");
		       }
		    else
		       {
			     alert("Debe seleccionar una Unidad Ejecutora !!!");
		       }
	      }
	 }
}

function ue_buscar()
{
	li_leer=f.leer.value;
	tipsol="SOC";
	if (li_leer==1)
   	{
		window.open("sigesp_soc_cat_orden_compra.php?origen=OC&tipsol="+tipsol,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=650,height=400,left=50,location=no,resizable=yes");
   	}
	else
   	{
 		alert("No tiene permiso para realizar esta operacion");
   	}
}

function writetostatus(input)
{
    window.status=input
    return true
}

function ue_delete_bienes(fila)
{
  numanacot=f.numanacot.value;
  if(numanacot=="-" || numanacot=="")
  {
	tipsol=f.txttipsol.value;
	if (uf_evaluate_cierre())
	   {
		estapro=f.txtestapro.value;
		ls_codunieje = f.txtcodunieje.value;
		ls_concepto=f.txtconordcom.value;
		if(estapro=="1")
		{
			alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		}
		else
		{
			if(confirm("?Desea eliminar el Registro actual?"))
			{
				valido=true;
				parametros="";
				montobien=0;
				montocargo=0;
				cuentacargo="";
				codigo="";
				cuentabien="";
				f.crearasiento.value=0;
				//---------------------------------------------------------------------------------
				// Cargar los Bienes y eliminar el seleccionado
				//---------------------------------------------------------------------------------
				// Obtenemos el total de filas de los bienes
				total = ue_calcular_total_fila_local("txtcodart");
				f.totrowbienes.value = total;
				rowbienes = f.totrowbienes.value;
				li_i = 0;
				lb_unieje = true
				ls_uniejeaso = "";
				ls_coduniadmsep  = "";
				ls_denant    = "";
				ls_uniant    = "";
				ls_conordcom = "";
				totfila=0;
				totfila3=0;
				filaeli=0;
				codproeli= eval("document.formulario.txtcodgas"+fila+".value");
				spgcueeli= eval("document.formulario.txtcodspg"+fila+".value");
				estclaeli= eval("document.formulario.txtstatus"+fila+".value");
				coincidencias=false;
				for(j=1;(j<rowbienes)&&(valido);j++)
				{
					totfila++;
					if(j!=fila)
					{
						li_i=li_i+1;
						filaeli=fila;
						numsolord		= eval("document.formulario.txtnumsolord"+j+".value");
						codart			= eval("document.formulario.txtcodart"+j+".value");
						denart			= eval("document.formulario.txtdenart"+j+".value");
						canart			= eval("document.formulario.txtcanart"+j+".value");
						unidad			= eval("document.formulario.cmbunidad"+j+".value");
						preart			= eval("document.formulario.txtpreart"+j+".value");
						subtotart		= eval("document.formulario.txtsubtotart"+j+".value");
						carart			= eval("document.formulario.txtcarart"+j+".value");
						totart			= eval("document.formulario.txttotart"+j+".value");
						spgcuenta		= eval("document.formulario.txtcodspg"+j+".value");
						unidadfisica	= eval("document.formulario.txtunidad"+j+".value");
						ls_coduniadmsep = eval("document.formulario.txtcoduniadmsep"+j+".value");
						ls_denuniadmsep = eval("document.formulario.txtdenuniadmsep"+j+".value");
						ls_codestpro 	= eval("document.formulario.txtcodgas"+j+".value");
						ls_estcla 		= eval("document.formulario.txtstatus"+j+".value");
						ls_codfuefin 	= eval("document.formulario.txtcodfuefin"+j+".value");
						if((codproeli==ls_codestpro)&&(spgcueeli==spgcuenta)&&(estclaeli==ls_estcla))
						{
							coincidencias=true;
						}
						if (li_i==1)
						{
							  ls_uniant = ls_coduniadmsep;
							  ls_denant = ls_denuniadmsep;
						}
						else
						{
							  if (ls_uniant!=ls_coduniadmsep && lb_unieje==true && ls_coduniadmsep!="")
							  {
								 lb_unieje = false;
							  }
						}
						ls_denuniadmsep = eval("f.txtdenuniadmsep"+j+".value");
						if (ls_coduniadmsep!="" && ls_coduniadmsep!='----------' && numsolord!='---------------')
						{
						  ls_uniejeaso = ls_uniejeaso+" "+"Nro. SEP:"+numsolord+". Unidad Ejecutora: "+ls_coduniadmsep+" - "+ls_denuniadmsep+";";
						}
						parametros=parametros+"&txtcodart"+li_i+"="+codart+"&txtdenart"+li_i+"="+denart+""+
								   "&txtcanart"+li_i+"="+canart+"&cmbunidad"+li_i+"="+unidad+""+
								   "&txtpreart"+li_i+"="+preart+"&txtsubtotart"+li_i+"="+subtotart+""+
								   "&txtcarart"+li_i+"="+carart+"&txttotart"+li_i+"="+totart+""+
								   "&txtspgcuenta"+li_i+"="+spgcuenta+"&txtunidad"+li_i+"="+unidadfisica+""+
								   "&txtnumsolord"+li_i+"="+numsolord+"&txtcoduniadmsep"+li_i+"="+ls_coduniadmsep+""+
								   "&txtdenuniadmsep"+li_i+"="+ls_denuniadmsep+"&hidcodestpro"+li_i+"="+ls_codestpro+"&estcla"+li_i+"="+ls_estcla+"&txtcodfuefin"+li_i+"="+ls_codfuefin;
					}
					else
					{
						filaeli=fila;
						codigo=eval("document.formulario.txtcodart"+j+".value");
						cuentabien=eval("document.formulario.txtspgcuenta"+j+".value");
						montobien=eval("document.formulario.txtsubtotart"+j+".value");
						montobien=ue_formato_calculo(montobien);
						numsolord=eval("document.formulario.txtnumsolord"+j+".value");
						if(numsolord!="")
						{
						  numsoldel=eval("document.formulario.numsoldel.value");
						  if(numsoldel!="")
						  {
							ls_numsoldel=numsoldel+","+numsolord+"-"+codigo+",";
						  }
						  else
						  {
							ls_numsoldel=numsolord+"-"+codigo;
						  }
						  f.numsoldel.value=ls_numsoldel;
						}
	
					}
				}
				if (!lb_unieje && ls_codunieje!="")
				{
					f.txtcodunieje.value = "----------";
					f.txtdenunieje.value = "NINGUNA";
				}
				else
				{
					if (ls_uniant!="" && rowbienes>1)
					   {
						 f.txtcodunieje.value = ls_uniant;
						 f.txtdenunieje.value = ls_denant;
					   }
				}
				li_i=li_i+1;
				parametros=parametros+"&totalbienes="+li_i+"";
				f.totrowbienes.value=li_i;
				f.txtuniejeaso.value = ls_uniejeaso;
				/////agregado 09/09/08////
				totfila3=ue_calcular_total_fila_local("txtcodart");
				if(tipsol=="SEP")
				 {
					if(totfila3==1)
					{
						f.txttipsol.value="SOC";
						f.txtconordcom.value="";
						f.txtcodunieje.value="";
						f.txtdenunieje.value="";
					}
				 }
				//////////////////////////
				//---------------------------------------------------------------------------------
				// Cargar los Cargos del opener y eliminar el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodservic");
				f.totrowcargos.value=total;
				rowcargos=f.totrowcargos.value;
				li_i=0;
				filaelicargo=0;
				for(j=1;(j<=rowcargos)&&(valido);j++)
				{
					codservic=eval("document.formulario.txtcodservic"+j+".value");
					ls_numsep    = eval("document.formulario.hidnumsepcar"+j+".value");
					//numsolord=eval("document.formulario.txtnumsolord"+j+".value");
					if(codservic!=codigo)
					{
						li_i=li_i+1;
						filaelicargo=fila;
						codcar=eval("document.formulario.txtcodcar"+j+".value");
						dencar=eval("document.formulario.txtdencar"+j+".value");
						bascar=eval("document.formulario.txtbascar"+j+".value");
						moncar=eval("document.formulario.txtmoncar"+j+".value");
						subcargo	 = eval("document.formulario.txtsubcargo"+j+".value");
						spgcargo	 = eval("document.formulario.txtcodspgcre"+j+".value");
						formulacargo = eval("document.formulario.formulacargo"+j+".value");
						codprogcargo=eval("document.formulario.txtcodgascre"+j+".value");
						estclacargo=eval("document.formulario.txtstatuscre"+j+".value");
						codfuefincargo=eval("document.formulario.txtcodfuefincre"+j+".value");
						parametros=parametros+"&txtcodservic"+li_i+"="+codservic+"&txtcodcar"+li_i+"="+codcar+
								   "&txtdencar"+li_i+"="+dencar+"&txtbascar"+li_i+"="+bascar+
								   "&txtmoncar"+li_i+"="+moncar+"&txtsubcargo"+li_i+"="+subcargo+
								   "&cuentacargo"+li_i+"="+spgcargo+"&formulacargo"+li_i+"="+formulacargo+
								   "&hidnumsepcar"+li_i+"="+ls_numsep+"&codprogcargo"+li_i+"="+codprogcargo+
								   "&estclacargo"+li_i+"="+estclacargo+"&txtcodfuefincre"+li_i+"="+codfuefincargo;
					}
					else
					{
					  filaelicargo=fila;
					  /*if(numsolord==ls_numsep)
					  {
						  cuentacargo=eval("document.formulario.cuentacargo"+j+".value");
						  codcargo=eval("document.formulario.txtcodcar"+j+".value");
						  montocargo=eval("document.formulario.txtmoncar"+j+".value");
						  montocargo=ue_formato_calculo(montocargo);
					  }
					  else
					  {
						li_i=li_i+1;
						codcar=eval("document.formulario.txtcodcar"+j+".value");
						dencar=eval("document.formulario.txtdencar"+j+".value");
						bascar=eval("document.formulario.txtbascar"+j+".value");
						moncar=eval("document.formulario.txtmoncar"+j+".value");
						subcargo	 = eval("document.formulario.txtsubcargo"+j+".value");
						spgcargo	 = eval("document.formulario.cuentacargo"+j+".value");
						formulacargo = eval("document.formulario.formulacargo"+j+".value");
						codprogcargo=eval("document.formulario.codprogcargo"+j+".value");
						estclacargo=eval("document.formulario.estclacargo"+j+".value");
	
						parametros=parametros+"&txtcodservic"+li_i+"="+codservic+"&txtcodcar"+li_i+"="+codcar+
								   "&txtdencar"+li_i+"="+dencar+"&txtbascar"+li_i+"="+bascar+
								   "&txtmoncar"+li_i+"="+moncar+"&txtsubcargo"+li_i+"="+subcargo+
								   "&cuentacargo"+li_i+"="+spgcargo+"&hidnumsepcar"+li_i+"="+ls_numsep+
								   +"&codprogcargo"+li_i+"="+codprogcargo+"&estclacargo"+li_i+"="+estclacargo;
					  }*/
					}
				}
				f.totrowcargos.value=li_i;
				parametros=parametros+"&totalcargos="+li_i;
				parametros=parametros+"&tipsol="+tipsol;
				if((parametros!="")&&(valido))
				{
					divgrid = document.getElementById("bienesservicios");
					ajax=objetoAjax();
					ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
					ajax.onreadystatechange=function() {
						if (ajax.readyState==4) {
							divgrid.innerHTML = ajax.responseText
						}
					}
					ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					ajax.send("proceso=AGREGARBIENES&cargarcargos=0"+parametros);
				}
			}
		}
	  }
	}
	else
	{
		alert("La orden de compra proviene de un analisis de cotizacion, no se puede modificar esta opcion");
	}
}

function ue_delete_servicios(fila)
{
  numanacot=f.numanacot.value;
  if(numanacot=="-" || numanacot=="")
  {
	tipsol=f.txttipsol.value;
	if (uf_evaluate_cierre())
	   {
		estapro=f.txtestapro.value;
		if(estapro=="1")
		{
			alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		}
		else
		{
			if(confirm("?Desea eliminar el Registro actual?"))
			{
				valido=true;
				parametros="";
				montoservicios=0;
				montocargo=0;
				cuentacargo="";
				codigo="";
				cuentaservicios="";
				//---------------------------------------------------------------------------------
				// Cargar los Servicios y eliminar el seleccionado
				//---------------------------------------------------------------------------------
				// Obtenemos el total de filas de los Servicios
				total=ue_calcular_total_fila_local("txtcodser");
				f.totrowservicios.value=total;
				rowservicios=f.totrowservicios.value;
				ls_codunieje=f.txtcodunieje.value;
				li_i = 0;
				lb_unieje 		= true
				ls_uniejeaso    = "";
				ls_coduniadmsep = "";
				ls_denant       = "";
				ls_uniant       = "";
				totfila=0;
				filaeli=0;
				totfila2=0;
				i=0;
				codproeli= eval("document.formulario.txtcodgas"+fila+".value");
				spgcueeli= eval("document.formulario.txtcodspg"+fila+".value");
				estclaeli= eval("document.formulario.txtstatus"+fila+".value");
				for(j=1;(j<rowservicios)&&(valido);j++)
				{
					if(j!=fila)
					{
						totfila++;
						li_i=li_i+1;
						filaeli=fila;
						numsolord		= eval("document.formulario.txtnumsolord"+j+".value");
						ls_coduniadmsep = eval("document.formulario.txtcoduniadmsep"+j+".value");
						ls_denuniadmsep = eval("document.formulario.txtdenuniadmsep"+j+".value");
						codser			= eval("document.formulario.txtcodser"+j+".value");
						denser			= eval("document.formulario.txtdenser"+j+".value");
						canser		 	= eval("document.formulario.txtcanser"+j+".value");
						preser		 	= eval("document.formulario.txtpreser"+j+".value");
						subtotser	 	= eval("document.formulario.txtsubtotser"+j+".value");
						carser		 	= eval("document.formulario.txtcarser"+j+".value");
						totser		 	= eval("document.formulario.txttotser"+j+".value");
						spgcuenta	 	= eval("document.formulario.txtcodspg"+j+".value");
						ls_codestpro 	= eval("document.formulario.txtcodgas"+j+".value");
						ls_estcla    	= eval("document.formulario.txtstatus"+j+".value");
						if((codproeli==ls_codestpro)&&(spgcueeli==spgcuenta)&&(estclaeli==ls_estcla))
						{
							coincidencias=true;
						}
						if (li_i==1)
						{
						  ls_uniant = ls_coduniadmsep;
						  ls_denant = ls_denuniadmsep;
						}
						else
						{
						  if (ls_uniant!=ls_coduniadmsep && lb_unieje==true && ls_coduniadmsep!="")
							 {
							   lb_unieje = false;
							 }
						}
						if (ls_coduniadmsep!="" && ls_coduniadmsep!='----------' && numsolord!='---------------')
						   {
							 ls_uniejeaso = ls_uniejeaso+" "+"Nro. SEP:"+numsolord+". Unidad Ejecutora: "+ls_coduniadmsep+" - "+ls_denuniadmsep+";";
						   }
						parametros=parametros+"&txtcodser"+li_i+"="+codser+"&txtdenser"+li_i+"="+denser+""+
								   "&txtcanser"+li_i+"="+canser+"&txtpreser"+li_i+"="+preser+""+
								   "&txtsubtotser"+li_i+"="+subtotser+"&txtcarser"+li_i+"="+carser+""+
								   "&txttotser"+li_i+"="+totser+"&txtspgcuenta"+li_i+"="+spgcuenta+""+
								   "&txtnumsolord"+li_i+"="+numsolord+"&txtcoduniadmsep"+li_i+"="+ls_coduniadmsep+""+
								   "&txtdenuniadmsep"+li_i+"="+ls_denuniadmsep+"&hidcodestpro"+li_i+"="+ls_codestpro+"&estcla"+li_i+"="+ls_estcla;
					}
					else
					{
						filaeli=fila;
						codigo			= eval("document.formulario.txtcodser"+j+".value");
						cuentaservicios = eval("document.formulario.txtspgcuenta"+j+".value");
						montoservicios  = eval("document.formulario.txtsubtotser"+j+".value");
						montoservicios	= ue_formato_calculo(montoservicios);
						numsolord		= eval("document.formulario.txtnumsolord"+j+".value");
						if(numsolord!="")
						{
						  numsoldel=eval("document.formulario.numsoldel.value");
						  if(numsoldel!="")
						  {
							ls_numsoldel=numsoldel+","+numsolord+"-"+codigo+",";
						  }
						  else
						  {
							ls_numsoldel=numsolord+"-"+codigo;
						  }
						  f.numsoldel.value=ls_numsoldel;
						}
					}
				}
				if (!lb_unieje && ls_codunieje!="")
				{
					f.txtcodunieje.value = "----------";
					f.txtdenunieje.value = "NINGUNA";
				}
				else
				{
					if (ls_uniant!="" && rowservicios>1)
					   {
						 f.txtcodunieje.value = ls_uniant;
						 f.txtdenunieje.value = ls_denant;
	
						 ls_codestpro1aux=ls_codestpro.substring(0,25);
						 ls_codestpro2aux=ls_codestpro.substring(25,50);
						 ls_codestpro3aux=ls_codestpro.substring(50,75);
						 ls_codestpro4aux=ls_codestpro.substring(75,100);
						 ls_codestpro5aux=ls_codestpro.substring(100,125);
						 ls_estclaux=ls_estcla;
	
						 f.txtcodestpro1.value=ls_codestpro1aux;
						 f.txtcodestpro2.value=ls_codestpro2aux;
						 f.txtcodestpro3.value=ls_codestpro3aux;
						 f.txtcodestpro4.value=ls_codestpro4aux;
						 f.txtcodestpro5.value=ls_codestpro5aux;
						 f.hidestcla.value=ls_estclaux;
					   }
	
				}
				li_i=li_i+1;
				parametros=parametros+"&totalservicios="+li_i+"";
				f.totrowservicios.value=li_i;
				/////agregado 09/09/08////
				totfila2=ue_calcular_total_fila_local("txtcodser");
				if(tipsol=="SEP")
				 {
					if(totfila2==1)
					{
					  f.txttipsol.value="SOC";
					  f.txtcodunieje.value="";
					  f.txtdenunieje.value="";
					  f.txtconordcom.value="";
					}
				 }
				//////////////////////////
				//---------------------------------------------------------------------------------
				// Cargar los Cargos del opener y el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodservic");
				f.totrowcargos.value=total;
				rowcargos=f.totrowcargos.value;
				li_i=0;
				for(j=1;(j<=rowcargos)&&(valido);j++)
				{
					codservic=eval("document.formulario.txtcodservic"+j+".value");
					ls_numsep= eval("document.formulario.hidnumsepcar"+j+".value");
					//numsolord=eval("document.formulario.txtnumsolord"+j+".value");
					if(codservic!=codigo)
					{
						li_i=li_i+1;
						filaelicargo=fila;
						codcar=eval("document.formulario.txtcodcar"+j+".value");
						dencar=eval("document.formulario.txtdencar"+j+".value");
						bascar=eval("document.formulario.txtbascar"+j+".value");
						moncar=eval("document.formulario.txtmoncar"+j+".value");
						subcargo=eval("document.formulario.txtsubcargo"+j+".value");
						spgcargo     = eval("document.formulario.cuentacargo"+j+".value");
						formulacargo = eval("document.formulario.formulacargo"+j+".value");
						codprogcargo=eval("document.formulario.codprogcargo"+j+".value");
						estclacargo=eval("document.formulario.estclacargo"+j+".value");
	
						parametros=parametros+"&txtcodservic"+li_i+"="+codservic+"&txtcodcar"+li_i+"="+codcar+
								   "&txtdencar"+li_i+"="+dencar+"&txtbascar"+li_i+"="+bascar+
								   "&txtmoncar"+li_i+"="+moncar+"&txtsubcargo"+li_i+"="+subcargo+
								   "&cuentacargo"+li_i+"="+spgcargo+"&formulacargo"+li_i+"="+formulacargo+
								   "&hidnumsepcar"+li_i+"="+ls_numsep+"&codprogcargo"+li_i+"="+codprogcargo+
								   "&estclacargo"+li_i+"="+estclacargo;
					   cuentacargo=spgcargo;
					   codcargo=codcar;
					}
					else
					{
						filaelicargo=fila;
					   /* if(numsolord==ls_numsep)
						{
							cuentacargo=eval("document.formulario.cuentacargo"+j+".value");
							codcargo=eval("document.formulario.txtcodcar"+j+".value");
							montocargo=eval("document.formulario.txtmoncar"+j+".value");
							montocargo=ue_formato_calculo(montocargo);
						}
					   else
						{
							li_i=li_i+1;
							codcar=eval("document.formulario.txtcodcar"+j+".value");
							dencar=eval("document.formulario.txtdencar"+j+".value");
							bascar=eval("document.formulario.txtbascar"+j+".value");
							moncar=eval("document.formulario.txtmoncar"+j+".value");
							subcargo	 = eval("document.formulario.txtsubcargo"+j+".value");
							spgcargo	 = eval("document.formulario.cuentacargo"+j+".value");
							formulacargo = eval("document.formulario.formulacargo"+j+".value");
							codprogcargo=eval("document.formulario.codprogcargo"+j+".value");
							estclacargo=eval("document.formulario.estclacargo"+j+".value");
	
							parametros=parametros+"&txtcodservic"+li_i+"="+codservic+"&txtcodcar"+li_i+"="+codcar+
									   "&txtdencar"+li_i+"="+dencar+"&txtbascar"+li_i+"="+bascar+
									   "&txtmoncar"+li_i+"="+moncar+"&txtsubcargo"+li_i+"="+subcargo+
									   "&cuentacargo"+li_i+"="+spgcargo+"&formulacargo"+li_i+"="+formulacargo+
									   "&hidnumsepcar"+li_i+"="+ls_numsep+"&codprogcargo"+j+"="+codprogcargo+
									   "&estclacargo"+j+"="+estclacargo;
						 }*/
					}
				}
				f.totrowcargos.value=li_i;
				parametros=parametros+"&totalcargos="+li_i;
				valido=true;
				parametros=parametros+"&tipsol="+tipsol;
				if((parametros!="")&&(valido))
				{
					divgrid = document.getElementById("bienesservicios");
					ajax=objetoAjax();
					ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
					ajax.onreadystatechange=function() {
						if (ajax.readyState==4) {
							divgrid.innerHTML = ajax.responseText
						}
					}
					ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					ajax.send("proceso=AGREGARSERVICIOS&cargarcargos=0"+parametros);
				}
			}
		}
	  }
	}
	else
	{
		alert("La orden de compra proviene de un analisis de cotizacion, no se puede modificar esta opcion");
	}
}

function ue_delete_cargos(fila,tipo)
{
  numanacot=f.numanacot.value;
  if(numanacot=="-" || numanacot=="")
  {
	tipsol=f.txttipsol.value;
	if (uf_evaluate_cierre())
	   {
		estapro=f.txtestapro.value;
		if(estapro=="1")
		{
			alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		}
		else
		{
			if(confirm("?Desea eliminar el Registro actual?"))
			{
				valido=true;
				parametros="";
				montobien=0;
				montoservicio=0;
				montoconcepto=0;
				montocargo=0;
				//---------------------------------------------------------------------------------
				// Cargar los Cargos del opener y el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodservic");
				f.totrowcargos.value=total;
				rowcargos=f.totrowcargos.value;
				li_i=0;
				for(j=1;(j<=rowcargos)&&(valido);j++)
				{
					if(fila!=j)
					{
						li_i=li_i+1;
						codservic	 = eval("document.formulario.txtcodservic"+j+".value");
						codcar		 = eval("document.formulario.txtcodcar"+j+".value");
						dencar		 = eval("document.formulario.txtdencar"+j+".value");
						bascar		 = eval("document.formulario.txtbascar"+j+".value");
						moncar		 = eval("document.formulario.txtmoncar"+j+".value");
						subcargo	 = eval("document.formulario.txtsubcargo"+j+".value");
						spgcargo	 = eval("document.formulario.cuentacargo"+j+".value");
						formulacargo = eval("document.formulario.formulacargo"+j+".value");
						ls_numsep    = eval("document.formulario.hidnumsepcar"+j+".value");
						parametros=parametros+"&txtcodservic"+li_i+"="+codservic+"&txtcodcar"+li_i+"="+codcar+
								   "&txtdencar"+li_i+"="+dencar+"&txtbascar"+li_i+"="+bascar+
								   "&txtmoncar"+li_i+"="+moncar+"&txtsubcargo"+li_i+"="+subcargo+
								   "&cuentacargo"+li_i+"="+spgcargo+"&formulacargo"+li_i+"="+formulacargo+"&hidnumsepcar"+li_i+"="+ls_numsep;
					}
					else
					{
						codigo		= eval("document.formulario.txtcodservic"+j+".value");
						cuentacargo = eval("document.formulario.cuentacargo"+j+".value");
						codcargo	= eval("document.formulario.txtcodcar"+j+".value");
						montocargo  = eval("document.formulario.txtmoncar"+j+".value");
						ls_numsep   = eval("document.formulario.hidnumsepcar"+j+".value");
						montocargo  = ue_formato_calculo(montocargo);
					}
				}
				f.totrowcargos.value=li_i;
				parametros=parametros+"&totalcargos="+li_i;
				if(tipo=="B") // si es un Bien
				{
					proceso="AGREGARBIENES";
					//---------------------------------------------------------------------------------
					// Cargar los Bienes y eliminar el seleccionado
					//---------------------------------------------------------------------------------
					total=ue_calcular_total_fila_local("txtcodart");
					f.totrowbienes.value=total;
					rowbienes=f.totrowbienes.value;
					for(j=1;(j<rowbienes)&&(valido);j++)
					{
						numsolord=eval("document.formulario.txtnumsolord"+j+".value");
						ls_coduniadmsep=eval("document.formulario.txtcoduniadmsep"+j+".value");
						ls_denuniadmsep=eval("document.formulario.txtdenuniadmsep"+j+".value");
						codart=eval("document.formulario.txtcodart"+j+".value");
						denart=eval("document.formulario.txtdenart"+j+".value");
						canart=eval("document.formulario.txtcanart"+j+".value");
						unidad=eval("document.formulario.cmbunidad"+j+".value");
						preart=eval("document.formulario.txtpreart"+j+".value");
						subtotart=eval("document.formulario.txtsubtotart"+j+".value");
						carart=eval("document.formulario.txtcarart"+j+".value");
						totart=eval("document.formulario.txttotart"+j+".value");
						spgcuenta=eval("document.formulario.txtspgcuenta"+j+".value");
						unidadfisica=eval("document.formulario.txtunidad"+j+".value");
						ls_codestpro = eval("document.formulario.hidcodestpro"+j+".value");
						ls_estcla    = eval("document.formulario.estcla"+j+".value");
						if(codart==codigo)
						{
							carart=ue_formato_calculo(carart);
							carart=carart-montocargo;
							subtotart=ue_formato_calculo(subtotart);
							totart=subtotart+carart;
							carart=uf_convertir(carart);
							subtotart=uf_convertir(subtotart);
							totart=uf_convertir(totart);
						}
						parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
								   "&txtcanart"+j+"="+canart+"&cmbunidad"+j+"="+unidad+""+
								   "&txtpreart"+j+"="+preart+"&txtsubtotart"+j+"="+subtotart+""+
								   "&txtcarart"+j+"="+carart+"&txttotart"+j+"="+totart+""+
								   "&txtspgcuenta"+j+"="+spgcuenta+"&txtunidad"+j+"="+unidadfisica+""+
								   "&txtnumsolord"+j+"="+numsolord+"&txtcoduniadmsep"+j+"="+ls_coduniadmsep+""+
								   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
								   "&txtdenuniadmsep"+j+"="+ls_denuniadmsep+"";
					}
					parametros=parametros+"&totalbienes="+rowbienes+"";
				}
				if(tipo=="S")
				{
					proceso="AGREGARSERVICIOS";
					//---------------------------------------------------------------------------------
					// Cargar los Servicios del opener y el seleccionado
					//---------------------------------------------------------------------------------
					total=ue_calcular_total_fila_local("txtcodser");
					f.totrowservicios.value=total;
					rowservicios=f.totrowservicios.value;
					for(j=1;(j<rowservicios)&&(valido);j++)
					{
						numsolord=eval("document.formulario.txtnumsolord"+j+".value");
						ls_coduniadmsep=eval("document.formulario.txtcoduniadmsep"+j+".value");
						ls_denuniadmsep=eval("document.formulario.txtdenuniadmsep"+j+".value");
						codser=eval("document.formulario.txtcodser"+j+".value");
						denser=eval("document.formulario.txtdenser"+j+".value");
						canser=eval("document.formulario.txtcanser"+j+".value");
						preser=eval("document.formulario.txtpreser"+j+".value");
						subtotser=eval("document.formulario.txtsubtotser"+j+".value");
						carser=eval("document.formulario.txtcarser"+j+".value");
						totser=eval("document.formulario.txttotser"+j+".value");
						spgcuenta=eval("document.formulario.txtspgcuenta"+j+".value");
						ls_codestpro = eval("document.formulario.hidcodestpro"+j+".value");
						ls_estcla    = eval("document.formulario.estcla"+j+".value");
	
						if(codser==codigo)
						{
							carser=ue_formato_calculo(carser);
							carser=carser-montocargo;
							subtotser=ue_formato_calculo(subtotser);
							totser=subtotser+carser;
							carser=uf_convertir(carser);
							subtotser=uf_convertir(subtotser);
							totser=uf_convertir(totser);
						}
						parametros=parametros+"&txtcodser"+j+"="+codser+"&txtdenser"+j+"="+denser+""+
								   "&txtcanser"+j+"="+canser+"&txtpreser"+j+"="+preser+""+
								   "&txtsubtotser"+j+"="+subtotser+"&txtcarser"+j+"="+carser+""+
								   "&txttotser"+j+"="+totser+"&txtspgcuenta"+j+"="+spgcuenta+""+
								   "&txtnumsolord"+j+"="+numsolord+"&txtcoduniadmsep"+j+"="+ls_coduniadmsep+""+
								   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
								   "&txtdenuniadmsep"+j+"="+ls_denuniadmsep;
					}
					parametros=parametros+"&totalservicios="+rowservicios+"";
				}
				//---------------------------------------------------------------------------------
				// Cargar las Cuentas Presupuestarias del opener y el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcuentagas");
				f.totrowcuentas.value=total;
				rowcuentas=f.totrowcuentas.value;
				for(j=1;(j<rowcuentas)&&(valido);j++)
				{
					cuenta=eval("document.formulario.txtcuentagas"+j+".value");
					codpro=eval("document.formulario.txtcodprogas"+j+".value");
					moncue=eval("document.formulario.txtmoncuegas"+j+".value");
					estcla=eval("document.formulario.estclapre"+j+".value");
					parametros=parametros+"&txtcodprogas"+j+"="+codpro+"&txtcuentagas"+j+"="+cuenta+
							   "&txtmoncuegas"+j+"="+moncue+"&estclapre"+j+"="+estcla;
				}
				totalcuentas=eval(rowcuentas);
				f.totrowcuentas.value=totalcuentas;
				parametros=parametros+"&totalcuentas="+totalcuentas;
				//---------------------------------------------------------------------------------
				// Cargar las Cuentas Presupuestarias del Cargo del opener y el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcuentacar");
				f.totrowcuentascargo.value=total;
				rowcuentas=f.totrowcuentascargo.value;
				for(j=1;(j<rowcuentas)&&(valido);j++)
				{
					cargo=eval("document.formulario.txtcodcargo"+j+".value");
					cuenta=eval("document.formulario.txtcuentacar"+j+".value");
					codpro=eval("document.formulario.txtcodprocar"+j+".value");
					moncue=eval("document.formulario.txtmoncuecar"+j+".value");
					estcla=eval("document.formulario.estclacar"+j+".value");
					if((cuenta==cuentacargo)&&(codcargo==cargo))
					{
						moncue=ue_formato_calculo(moncue);
						moncue=eval(moncue+"-"+montocargo);
						if(moncue<0)
						{
							moncue=0;
						}
						moncue=uf_convertir(moncue);
					}
					parametros=parametros+"&txtcodcargo"+j+"="+cargo+"&txtcodprocar"+j+"="+codpro+"&txtcuentacar"+j+"="+cuenta+
							   "&txtmoncuecar"+j+"="+moncue+"&estclacar"+j+"="+estcla;
				}
				totalcuentas=eval(rowcuentas);
				f.totrowcuentascargo.value=totalcuentas;
				parametros=parametros+"&totalcuentascargo="+totalcuentas;
				//---------------------------------------------------------------------------------
				// Cargar los totales
				//---------------------------------------------------------------------------------
				subtotal=f.txtsubtotal.value;
				cargos=f.txtcargos.value;
				total=f.txttotal.value;
				subtotal=ue_formato_calculo(subtotal);
				cargos=ue_formato_calculo(cargos);
				cargos=eval(cargos+"-"+montocargo);
				total=ue_formato_calculo(total);
				total=eval(subtotal+"+"+cargos);
				subtotal=uf_convertir2(subtotal);
				cargos=uf_convertir2(cargos);
				total=uf_convertir2(total);
				parametros=parametros+"&subtotal="+subtotal+"&cargos="+cargos+"&total="+total+"&tipsol="+tipsol;
				if((parametros!="")&&(valido))
				{
					divgrid = document.getElementById("bienesservicios");
					ajax=objetoAjax();
					ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
					ajax.onreadystatechange=function() {
						if (ajax.readyState==4) {
							divgrid.innerHTML = ajax.responseText
						}
					}
					ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
					ajax.send("proceso="+proceso+"&cargarcargos=0"+parametros);
				}
			}
		}
	   }
	}
	else
	{
		alert("La orden de compra proviene de un analisis de cotizacion, no se puede modificar esta opcion");
	}
}

function ue_delete_cuenta_gasto(fila,tipo)
{
tipsol=f.txttipsol.value;
if (uf_evaluate_cierre())
   {
	estapro=f.txtestapro.value;
	if(estapro=="1")
	{
		alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	}
	else
	{
		if(confirm("?Desea eliminar el Registro actual?"))
		{
			valido=true;
			parametros="";
			montobien=0;
			montocargo=0;
			//---------------------------------------------------------------------------------
			// Cargar los Cargos del opener y el seleccionado
			//---------------------------------------------------------------------------------
			total=ue_calcular_total_fila_local("txtcodservic");
			f.totrowcargos.value=total;
			rowcargos=f.totrowcargos.value;
			for(j=1;(j<=rowcargos)&&(valido);j++)
			{
				codservic=eval("document.formulario.txtcodservic"+j+".value");
				codcar=eval("document.formulario.txtcodcar"+j+".value");
				dencar=eval("document.formulario.txtdencar"+j+".value");
				bascar=eval("document.formulario.txtbascar"+j+".value");
				moncar=eval("document.formulario.txtmoncar"+j+".value");
				subcargo=eval("document.formulario.txtsubcargo"+j+".value");
				spgcargo=eval("document.formulario.cuentacargo"+j+".value");
				formulacargo=eval("document.formulario.formulacargo"+j+".value");
				ls_numsep    = eval("document.formulario.hidnumsepcar"+j+".value");
				parametros=parametros+"&txtcodservic"+j+"="+codservic+"&txtcodcar"+j+"="+codcar+
						   "&txtdencar"+j+"="+dencar+"&txtbascar"+j+"="+bascar+
						   "&txtmoncar"+j+"="+moncar+"&txtsubcargo"+j+"="+subcargo+
						   "&cuentacargo"+j+"="+spgcargo+"&formulacargo"+j+"="+formulacargo+"&hidnumsepcar"+j+"="+ls_numsep;
			}
			parametros=parametros+"&totalcargos="+rowcargos;
			if(tipo=="B") // si es un Bien
			{
				proceso="AGREGARBIENES";
				//---------------------------------------------------------------------------------
				// Cargar los Bienes y eliminar el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodart");
				f.totrowbienes.value=total;
				rowbienes=f.totrowbienes.value;
				for(j=1;(j<rowbienes)&&(valido);j++)
				{
					numsolord=eval("document.formulario.txtnumsolord"+j+".value");
					ls_coduniadmsep=eval("document.formulario.txtcoduniadmsep"+j+".value");
					ls_denuniadmsep=eval("document.formulario.txtdenuniadmsep"+j+".value");
					codart=eval("document.formulario.txtcodart"+j+".value");
					denart=eval("document.formulario.txtdenart"+j+".value");
					canart=eval("document.formulario.txtcanart"+j+".value");
					unidad=eval("document.formulario.cmbunidad"+j+".value");
					preart=eval("document.formulario.txtpreart"+j+".value");
					subtotart=eval("document.formulario.txtsubtotart"+j+".value");
					carart=eval("document.formulario.txtcarart"+j+".value");
					totart=eval("document.formulario.txttotart"+j+".value");
					spgcuenta=eval("document.formulario.txtspgcuenta"+j+".value");
					unidadfisica=eval("document.formulario.txtunidad"+j+".value");
				    ls_codestpro = eval("document.formulario.hidcodestpro"+j+".value");
					ls_estcla    = eval("document.formulario.estcla"+j+".value");
					parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
							   "&txtcanart"+j+"="+canart+"&cmbunidad"+j+"="+unidad+""+
							   "&txtpreart"+j+"="+preart+"&txtsubtotart"+j+"="+subtotart+""+
							   "&txtcarart"+j+"="+carart+"&txttotart"+j+"="+totart+""+
							   "&txtspgcuenta"+j+"="+spgcuenta+"&txtunidad"+j+"="+unidadfisica+""+
							   "&txtnumsolord"+j+"="+numsolord+"&txtcoduniadmsep"+j+"="+ls_coduniadmsep+""+
							   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
							   "&txtdenuniadmsep"+j+"="+ls_denuniadmsep+"";
				}
				parametros=parametros+"&totalbienes="+rowbienes+"";
			}
			if(tipo=="S")
			{
				proceso="AGREGARSERVICIOS";
				//---------------------------------------------------------------------------------
				// Cargar los Servicios del opener y el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodser");
				f.totrowservicios.value=total;
				rowservicios=f.totrowservicios.value;
				for(j=1;(j<rowservicios)&&(valido);j++)
				{
					numsolord=eval("document.formulario.txtnumsolord"+j+".value");
					ls_coduniadmsep=eval("document.formulario.txtcoduniadmsep"+j+".value");
					ls_denuniadmsep=eval("document.formulario.txtdenuniadmsep"+j+".value");
					codser=eval("document.formulario.txtcodser"+j+".value");
					denser=eval("document.formulario.txtdenser"+j+".value");
					canser=eval("document.formulario.txtcanser"+j+".value");
					preser=eval("document.formulario.txtpreser"+j+".value");
					subtotser=eval("document.formulario.txtsubtotser"+j+".value");
					carser=eval("document.formulario.txtcarser"+j+".value");
					totser=eval("document.formulario.txttotser"+j+".value");
					spgcuenta=eval("document.formulario.txtspgcuenta"+j+".value");
				    ls_codestpro = eval("document.formulario.hidcodestpro"+j+".value");
					ls_estcla    = eval("document.formulario.estcla"+j+".value");
					parametros=parametros+"&txtcodser"+j+"="+codser+"&txtdenser"+j+"="+denser+""+
							   "&txtcanser"+j+"="+canser+"&txtpreser"+j+"="+preser+""+
							   "&txtsubtotser"+j+"="+subtotser+"&txtcarser"+j+"="+carser+""+
							   "&txttotser"+j+"="+totser+"&txtspgcuenta"+j+"="+spgcuenta+""+
							   "&txtnumsolord"+j+"="+numsolord+"&txtcoduniadmsep"+j+"="+ls_coduniadmsep+""+
							   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
							   "&txtdenuniadmsep"+j+"="+ls_denuniadmsep;
				}
				parametros=parametros+"&totalservicios="+rowservicios+"";
			}
			//---------------------------------------------------------------------------------
			// Cargar las Cuentas Presupuestarias del opener y el seleccionado
			//---------------------------------------------------------------------------------
			total=ue_calcular_total_fila_local("txtcuentagas");
			f.totrowcuentas.value=total;
			rowcuentas=f.totrowcuentas.value;
			li_i=0;
			for(j=1;(j<rowcuentas)&&(valido);j++)
			{
				if(j!=fila)
				{
					li_i=li_i+1;
					cuenta=eval("document.formulario.txtcuentagas"+j+".value");
					codpro=eval("document.formulario.txtcodprogas"+j+".value");
					moncue=eval("document.formulario.txtmoncuegas"+j+".value");
					estcla=eval("document.formulario.estclapre"+j+".value");
					parametros=parametros+"&txtcodprogas"+li_i+"="+codpro+"&txtcuentagas"+li_i+"="+cuenta+
							   "&txtmoncuegas"+li_i+"="+moncue+"&estclapre"+li_i+"="+estcla;
				}
			}
			totalcuentas=eval(li_i);
			f.totrowcuentas.value=totalcuentas;
			parametros=parametros+"&totalcuentas="+totalcuentas;
			//---------------------------------------------------------------------------------
			// Cargar las Cuentas Presupuestarias del Cargo del opener y el seleccionado
			//---------------------------------------------------------------------------------
			total=ue_calcular_total_fila_local("txtcuentacar");
			f.totrowcuentascargo.value=total;
			rowcuentas=f.totrowcuentascargo.value;
			li_i=0;
			for(j=1;(j<rowcuentas)&&(valido);j++)
			{
				cargo=eval("document.formulario.txtcodcargo"+j+".value");
				cuenta=eval("document.formulario.txtcuentacar"+j+".value");
				codpro=eval("document.formulario.txtcodprocar"+j+".value");
				moncue=eval("document.formulario.txtmoncuecar"+j+".value");
				estcla=eval("document.formulario.estclacar"+j+".value");
				parametros=parametros+"&txtcodcargo"+j+"="+cargo+"&txtcodprocar"+j+"="+codpro+"&txtcuentacar"+j+"="+cuenta+
						              "&txtmoncuecar"+j+"="+moncue+"&estclacar"+j+"="+estcla;
			}
			parametros=parametros+"&totalcuentascargo="+rowcuentas;
			//---------------------------------------------------------------------------------
			// Cargar los totales
			//---------------------------------------------------------------------------------
			subtotal=f.txtsubtotal.value;
			cargos=f.txtcargos.value;
			total=f.txttotal.value;
			parametros=parametros+"&subtotal="+subtotal+"&cargos="+cargos+"&total="+total+"&tipsol="+tipsol;
			if((parametros!="")&&(valido))
			{
				divgrid = document.getElementById("bienesservicios");
				ajax=objetoAjax();
				ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
				ajax.onreadystatechange=function() {
					if (ajax.readyState==4) {
						divgrid.innerHTML = ajax.responseText
					}
				}
				ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				ajax.send("proceso="+proceso+"&cargarcargos=0"+parametros);
			}
		}
	}
   }
}

function ue_delete_cuenta_cargo(fila,tipo)
{
if (uf_evaluate_cierre())
   {
	estapro=f.txtestapro.value;
	if(estapro=="1")
	{
		alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
	}
	else
	{
		if(confirm("?Desea eliminar el Registro actual?"))
		{
			valido=true;
			parametros="";
			montobien=0;
			montocargo=0;
			//---------------------------------------------------------------------------------
			// Cargar los Cargos del opener y el seleccionado
			//---------------------------------------------------------------------------------
			total=ue_calcular_total_fila_local("txtcodservic");
			f.totrowcargos.value=total;
			rowcargos=f.totrowcargos.value;
			for(j=1;(j<=rowcargos)&&(valido);j++)
			{
				codservic=eval("document.formulario.txtcodservic"+j+".value");
				codcar=eval("document.formulario.txtcodcar"+j+".value");
				dencar=eval("document.formulario.txtdencar"+j+".value");
				bascar=eval("document.formulario.txtbascar"+j+".value");
				moncar=eval("document.formulario.txtmoncar"+j+".value");
				subcargo=eval("document.formulario.txtsubcargo"+j+".value");
				spgcargo=eval("document.formulario.cuentacargo"+j+".value");
				formulacargo=eval("document.formulario.formulacargo"+j+".value");
				ls_numsep    = eval("document.formulario.hidnumsepcar"+j+".value");
				parametros=parametros+"&txtcodservic"+j+"="+codservic+"&txtcodcar"+j+"="+codcar+
						   "&txtdencar"+j+"="+dencar+"&txtbascar"+j+"="+bascar+
						   "&txtmoncar"+j+"="+moncar+"&txtsubcargo"+j+"="+subcargo+
						   "&cuentacargo"+j+"="+spgcargo+"&formulacargo"+j+"="+formulacargo+"&hidnumsepcar"+j+"="+ls_numsep;
			}
			parametros=parametros+"&totalcargos="+rowcargos;
			if(tipo=="B") // si es un Bien
			{
				proceso="AGREGARBIENES";
				//---------------------------------------------------------------------------------
				// Cargar los Bienes y eliminar el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodart");
				f.totrowbienes.value=total;
				rowbienes=f.totrowbienes.value;
				for(j=1;(j<rowbienes)&&(valido);j++)
				{
					numsolord=eval("document.formulario.txtnumsolord"+j+".value");
					ls_coduniadmsep=eval("document.formulario.txtcoduniadmsep"+j+".value");
					ls_denuniadmsep=eval("document.formulario.txtdenuniadmsep"+j+".value");
					codart=eval("document.formulario.txtcodart"+j+".value");
					denart=eval("document.formulario.txtdenart"+j+".value");
					canart=eval("document.formulario.txtcanart"+j+".value");
					unidad=eval("document.formulario.cmbunidad"+j+".value");
					preart=eval("document.formulario.txtpreart"+j+".value");
					subtotart=eval("document.formulario.txtsubtotart"+j+".value");
					carart=eval("document.formulario.txtcarart"+j+".value");
					totart=eval("document.formulario.txttotart"+j+".value");
					spgcuenta=eval("document.formulario.txtspgcuenta"+j+".value");
					unidadfisica=eval("document.formulario.txtunidad"+j+".value");
					ls_codestpro=eval("document.formulario.hidcodestpro"+j+".value");
					ls_estcla=eval("document.formulario.estcla"+j+".value");

					parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
									   "&txtcanart"+j+"="+canart+"&cmbunidad"+j+"="+unidad+""+
									   "&txtpreart"+j+"="+preart+"&txtsubtotart"+j+"="+subtotart+""+
									   "&txtcarart"+j+"="+carart+"&txttotart"+j+"="+totart+""+
									   "&txtspgcuenta"+j+"="+spgcuenta+"&txtunidad"+j+"="+unidadfisica+""+
									   "&txtnumsolord"+j+"="+numsolord+"&txtcoduniadmsep"+j+"="+ls_coduniadmsep+""+
									   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
									   "&txtdenuniadmsep"+j+"="+ls_denuniadmsep+"";
				}
				parametros=parametros+"&totalbienes="+rowbienes+"";
			}
			if(tipo=="S")
			{
				proceso="AGREGARSERVICIOS";
				//---------------------------------------------------------------------------------
				// Cargar los Servicios del opener y el seleccionado
				//---------------------------------------------------------------------------------
				total=ue_calcular_total_fila_local("txtcodser");
				f.totrowservicios.value=total;
				rowservicios=f.totrowservicios.value;
				for(j=1;(j<rowservicios)&&(valido);j++)
				{
					numsolord=eval("document.formulario.txtnumsolord"+j+".value");
					ls_coduniadmsep=eval("document.formulario.txtcoduniadmsep"+j+".value");
					ls_denuniadmsep=eval("document.formulario.txtdenuniadmsep"+j+".value");
					codser=eval("document.formulario.txtcodser"+j+".value");
					denser=eval("document.formulario.txtdenser"+j+".value");
					canser=eval("document.formulario.txtcanser"+j+".value");
					preser=eval("document.formulario.txtpreser"+j+".value");
					subtotser=eval("document.formulario.txtsubtotser"+j+".value");
					carser=eval("document.formulario.txtcarser"+j+".value");
					totser=eval("document.formulario.txttotser"+j+".value");
					spgcuenta=eval("document.formulario.txtspgcuenta"+j+".value");
					ls_codestpro=eval("document.formulario.hidcodestpro"+j+".value");
					ls_estcla=eval("document.formulario.estcla"+j+".value");
					parametros=parametros+"&txtcodser"+j+"="+codser+"&txtdenser"+j+"="+denser+""+
							   "&txtcanser"+j+"="+canser+"&txtpreser"+j+"="+preser+""+
							   "&txtsubtotser"+j+"="+subtotser+"&txtcarser"+j+"="+carser+""+
							   "&txttotser"+j+"="+totser+"&txtspgcuenta"+j+"="+spgcuenta+""+
							   "&txtnumsolord"+j+"="+numsolord+"&txtcoduniadmsep"+j+"="+ls_coduniadmsep+""+
							   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
							   "&txtdenuniadmsep"+j+"="+ls_denuniadmsep;
				}
				parametros=parametros+"&totalservicios="+rowservicios+"";
			}
			//---------------------------------------------------------------------------------
			// Cargar las Cuentas Presupuestarias del Cargo del opener y el seleccionado
			//---------------------------------------------------------------------------------
			total=ue_calcular_total_fila_local("txtcuentacar");
			f.totrowcuentascargo.value=total;
			rowcuentas=f.totrowcuentascargo.value;
			li_i=0;
			for(j=1;(j<rowcuentas)&&(valido);j++)
			{
				if(j!=fila)
				{
					li_i=li_i+1;
					cargo=eval("document.formulario.txtcodcargo"+j+".value");
					cuenta=eval("document.formulario.txtcuentacar"+j+".value");
					codpro=eval("document.formulario.txtcodprocar"+j+".value");
					moncue=eval("document.formulario.txtmoncuecar"+j+".value");
					estcla=eval("document.formulario.estclacar"+j+".value");
					parametros=parametros+"&txtcodcargo"+li_i+"="+cargo+"&txtcodprocar"+li_i+"="+codpro+"&txtcuentacar"+li_i+"="+cuenta+
							   "&txtmoncuecar"+li_i+"="+moncue+"&estclacar"+li_i+"="+estcla;
				}
			}
			totalcuentas=eval(li_i);
			f.totrowcuentascargo.value=totalcuentas;
			parametros=parametros+"&totalcuentascargo="+totalcuentas;
			//---------------------------------------------------------------------------------
			// Cargar las Cuentas Presupuestarias del opener y el seleccionado
			//---------------------------------------------------------------------------------
			total=ue_calcular_total_fila_local("txtcuentagas");
			f.totrowcuentas.value=total;
			rowcuentas=f.totrowcuentas.value;
			li_i=0;
			for(j=1;(j<rowcuentas)&&(valido);j++)
			{
				cuenta=eval("document.formulario.txtcuentagas"+j+".value");
				codpro=eval("document.formulario.txtcodprogas"+j+".value");
				moncue=eval("document.formulario.txtmoncuegas"+j+".value");
				estcla=eval("document.formulario.estclapre"+j+".value");
				parametros=parametros+"&txtcodprogas"+j+"="+codpro+"&txtcuentagas"+j+"="+cuenta+
						   "&txtmoncuegas"+j+"="+moncue+"&estclapre"+j+"="+estcla;
			}
			parametros=parametros+"&totalcuentas="+rowcuentas;
			//---------------------------------------------------------------------------------
			// Cargar los totales
			//---------------------------------------------------------------------------------
			subtotal=f.txtsubtotal.value;
			cargos=f.txtcargos.value;
			total=f.txttotal.value;
			parametros=parametros+"&subtotal="+subtotal+"&cargos="+cargos+"&total="+total;
			if((parametros!="")&&(valido))
			{
				divgrid = document.getElementById("bienesservicios");
				ajax=objetoAjax();
				ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
				ajax.onreadystatechange=function() {
					if (ajax.readyState==4) {
						divgrid.innerHTML = ajax.responseText
					}
				}
				ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
				ajax.send("proceso="+proceso+"&cargarcargos=0"+parametros);
			}
		}
	}
   }
}
function ue_cambiar_prefijo()
{
   lb_existe=f.existe.value;
   if (lb_existe=="TRUE")
   {
		alert("La Orden de Compra est? guardada y no se puede modificar su prefijo");
   }
   else
   {
		f.operacion.value="PREFIJO";
		f.action="sigesp_soc_p_registro_orden_compra.php";
		f.submit();
   }
}
function ue_guardar()
{
  if (uf_evaluate_cierre())
     {
	   li_incluir=f.incluir.value;
	   li_cambiar=f.cambiar.value;
	   lb_existe=f.existe.value;
	   if (((lb_existe=="TRUE")&&(li_cambiar==1))||(lb_existe=="FALSE")&&(li_incluir==1))
	      {
		    valido=true;
		    estapro=f.txtestapro.value;
  		    if (estapro=="1")
	 	       {
			     valido=false;
			     alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		       }
		    else
		       {
				 crearasiento=f.crearasiento.value;
				 numanacot=f.numanacot.value;
				 if(numanacot!="-" && numanacot!="")
				 {
				 	crearasiento=1;
				 }
				 if(crearasiento!=1)
				 {
					alert("Antes de Guardar debe primero Crear/Actualizar el asiento Presupuestario.");
				 }
				 else
				 {
				 // Obtenemos el total de filas de los bienes
				 total=ue_calcular_total_fila_local("txtcodart");
				 f.totrowbienes.value=total;
				 // Obtenemos el total de filas de los Servicios
				 total=ue_calcular_total_fila_local("txtcodser");
				 f.totrowservicios.value=total;
				 // Obtenemos el total de filas de los cargos
				 total=ue_calcular_total_fila_local("txtcodservic");
				 f.totrowcargos.value=total;
				 // Obtenemos el total de filas de las cuentas
				 total=ue_calcular_total_fila_local("txtcuentagas");
				 f.totrowcuentas.value=total;
				 // Obtenemos el total de filas de las cuentas
				 total=ue_calcular_total_fila_local("txtcuentacar");
				 f.totrowcuentascargo.value=total;

				 tipord=f.tipord.value;
				 tipsol=f.txttipsol.value;
				 numordcom=ue_validarvacio(f.txtnumordcom.value);
				 coduniadm=ue_validarvacio(f.txtcodunieje.value);
				 fecordcom=ue_validarvacio(f.txtfecordcom.value);
				 codprov=ue_validarvacio(f.txtcodprov.value);
				 conordcom=ue_validarvacio(f.txtconordcom.value);
				 totalgeneral=0;
				 totalcuenta=0;
				 totalcuentacargo=0;

			     if (valido)
					{
					  valido=ue_validarcampo(numordcom,"El N?mero de la Orden de Compra no puede estar vacio.",f.txtnumordcom);
					}
				 if (valido)
					{
					  if (tipsol=='SOC')
						 {
						   valido=ue_validarcampo(coduniadm,"La Unidad Ejecutora no puede estar vacia.",f.txtcodunieje);
						 }
					}
				 if (valido)
					{
					  valido=ue_validarcampo(fecordcom,"La Fecha de la Orden de Compra no puede estar vacia.",f.txtfecordcom);
					}
				 if (valido)
					{
					  valido=ue_validarcampo(codprov,"El codigo del Proveedor no puede estar vacio.",f.txtcodprov);
					}
				 if (valido)
					{
					  valido=ue_validarcampo(conordcom,"El concepto no puede estar vacio.",f.txtconordcom);
					}
				 if (valido)
				    {
					  ls_perentdesde = f.txtperentdesde.value;
					  if (ls_perentdesde!='')
						 {
						   valido = ue_comparar_fechas(fecordcom,ls_perentdesde);
						   if (!valido)
							  {
							    alert("El Per?odo Entrega desde debe ser mayor o igual a la Fecha de Emisi?n de la Orden de Compra !!!");
							  }
						 }
				    }
			     if (valido)
				    {
					  ls_perenthasta = f.txtperenthasta.value;
					  if (ls_perenthasta!='')
						 {
						   valido = ue_comparar_fechas(fecordcom,ls_perenthasta);
						   if (!valido)
							  {
							    alert("El Per?odo Entrega hasta debe ser mayor o igual a la Fecha de Emisi?n de la Orden de Compra !!!");
							  }
						 }
				    }
				 if (valido)
				    {
					  if (tipord=="B") // Si la orden de compra es de Bienes
					     {
						   rowbienes=f.totrowbienes.value;
						   if (rowbienes>1)
						      {
							    for (j=1;(j<rowbienes)&&(valido);j++)
							        {
									  codart=eval("document.formulario.txtcodart"+j+".value");
									  canart=eval("document.formulario.txtcanart"+j+".value");
									  preart=eval("document.formulario.txtpreart"+j+".value");
									  totart=eval("document.formulario.txttotart"+j+".value");
									  canart=ue_formato_calculo(canart);
									  preart=ue_formato_calculo(preart);
									  totart=ue_formato_calculo(totart);
									  totalgeneral=eval(totalgeneral+"+"+totart);
									  totalgeneral=redondear(totalgeneral,2);
								      if ((canart<=0)||(preart<=0))
								         {
										   alert("El Precio y La Cantidad del Bien "+codart+" Deben ser mayor que Cero.")
										   valido=false;
								         }
							        }
						      }
						   else
						      {
							    alert("Debe Tener al menos un Bien Seleccionado.");
							    valido=false;
							  }
					     }
					  if (tipord=="S") // Si la orden de compra es de Servicios
					     {
						   rowservicios=f.totrowservicios.value;
						   if (rowservicios>1)
						      {
							    for (j=1;(j<rowservicios)&&(valido);j++)
							        {
									  codser=eval("document.formulario.txtcodser"+j+".value");
									  canser=eval("document.formulario.txtcanser"+j+".value");
									  preser=eval("document.formulario.txtpreser"+j+".value");
									  totser=eval("document.formulario.txttotser"+j+".value");
									  canser=ue_formato_calculo(canser);
									  preser=ue_formato_calculo(preser);
									  totser=ue_formato_calculo(totser);
									  totalgeneral=eval(totalgeneral+"+"+totser);
									  totalgeneral=redondear(totalgeneral,2);
								      if ((canser<=0)||(preser<=0))
								         {
									       alert("El Precio y La Cantidad del Servicio "+codser+" Deben ser mayor que Cero.")
									       valido=false;
								         }
							        }
						      }
						   else
						      {
							    alert("Debe Tener al menos un Servicio Seleccionado.");
							    valido=false;
						      }
					     }
				    }
			     if (valido)  //  Verificar los creditos
					{
					  total=ue_calcular_total_fila_local("txtcodservic");
					  f.totrowcargos.value=total;
					  rowcargos=f.totrowcargos.value;
					  for (j=1;(j<=rowcargos)&&(valido);j++)
						  {
						    codservic=eval("document.formulario.txtcodservic"+j+".value");
						    codcar=eval("document.formulario.txtcodcar"+j+".value");
							moncar=eval("document.formulario.txtmoncar"+j+".value");
							moncar=ue_formato_calculo(moncar);
							if (moncar<=0)
							   {
								 alert("El Monto del Cargo "+codcar+" del item "+codservic+" Debe ser mayor que Cero.");
								 valido=false;
							   }
						  }
					}
			     if (valido)
					{
					  total=ue_calcular_total_fila_local("txtcuentagas");
					  f.totrowcuentas.value=total;
					  rowcuentas=document.formulario.totrowcuentas.value;
					  for (j=1;(j<rowcuentas)&&(valido);j++)
						  {
						    codpro=eval("document.formulario.txtcodprogas"+j+".value");
						    cuenta=eval("document.formulario.txtcuentagas"+j+".value");
							moncue=eval("document.formulario.txtmoncuegas"+j+".value");
							moncue=ue_formato_calculo(moncue);
							totalcuenta=eval(totalcuenta+"+"+moncue);
							totalcuenta=redondear(totalcuenta,2);
							if (moncue<=0)
							   {
								 alert("El Monto de la Cuenta Presupuestaria "+cuenta+" Debe ser mayor que Cero.");
								 valido=false;
							   }
							if (codpro=="")
							   {
								 alert("La Cuenta Presupuestaria "+cuenta+", Debe estar asignada a una Estructura.");
								 valido=false;
							   }
						  }
					}
				 ls_tipafeiva =document.formulario.hidtipafeiva.value;
			     if(valido && ls_tipafeiva=='P')
			     {
					  total=ue_calcular_total_fila_local("txtcuentacar");
					  f.totrowcuentascargo.value=total;
					  rowcuentas=document.formulario.totrowcuentascargo.value;
					  for (j=1;(j<rowcuentas)&&(valido);j++)
						  {
						    codpro=eval("document.formulario.txtcodprocar"+j+".value");
							cuenta=eval("document.formulario.txtcuentacar"+j+".value");
							moncue=eval("document.formulario.txtmoncuecar"+j+".value");
							moncue=ue_formato_calculo(moncue);
							totalcuentacargo=eval(totalcuentacargo+"+"+moncue);
							totalcuentacargo=redondear(totalcuentacargo,2);
							if (moncue<=0)
							   {
								 alert("El Monto del la Cuenta Presupuestaria del Cargo "+cuenta+" Debe ser mayor que Cero.");
								 valido=false;
							   }
							if (codpro=="")
							   {
								 alert("La Cuenta Presupuestaria del "+cuenta+", Debe estar asignada a una Estructura.");
								 valido=false;
							   }

						  }
					}
			     if (valido)
					{
					  subtotal=f.txtsubtotal.value;
					  subtotal=ue_formato_calculo(subtotal);
					  cargos=f.txtcargos.value;
					  cargos=ue_formato_calculo(cargos);
					  if (totalcuenta!=subtotal)
						 {
						   alert("El Total de las Cuentas Presupuestarias es distinto al Subtotal.");
						   valido=false;
						 }
					  if (totalcuentacargo!=cargos && ls_tipafeiva=='P')
						 {
						   alert("El Total de las Cuentas Presupuestarias de los cargos es distinto a los Otros Cr?ditos.");
						   valido=false;
						 }
				      if (ls_tipafeiva=='P')
				       {
						  total=eval(totalcuenta+"+"+totalcuentacargo);
						  total=redondear(total,2);
						  if (totalgeneral!=total)
							 {
							   alert("La suma del Total de los Items es distinto al total General.");
							   valido=false;
							 }
					   }
					}
				 if(valido)
				 {
				 	if(f.controlguard.value=="1")
					{
						valido=false;
					}
				 }
			     if (valido)
					{
						f.controlguard.value=1;
					    f.operacion.value="GUARDAR";
					    f.action="sigesp_soc_p_registro_orden_compra.php";
					    f.submit();
					}
				 }
		       }
	      }
	   else
	      {
		    alert("No tiene permiso para realizar esta operaci?n.");
	      }
	 }
}

function ue_eliminar()
{
  if (uf_evaluate_cierre())
     {
	   li_eliminar=f.eliminar.value;
	   lb_existe=f.existe.value;
       if ((lb_existe=='TRUE')&&(li_eliminar==1))
 	      {
			li_estapro=f.txtestapro.value;
			ls_estatus=f.txtestatus.value;
			ls_numordcom=f.txtnumordcom.value;
		    ls_tipordcom = f.cmbtipordcom.value;
			if (ls_numordcom=="")
			   {
				 alert("No ha seleccionada ningun registro a eliminar.");
			   }
			else
			   {
			     if ((li_estapro=="1")&&((ls_estatus!='REGISTRO')||(ls_estatus!='EMITIDA')))
			        {
				      alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
			        }
			     else
			        {
					  if(ls_tipordcom=="B")
					  {
						total=ue_calcular_total_fila_local("txtcodart");
						f.totrowbienes.value=total;
					  }
					  else
					  {
						total=ue_calcular_total_fila_local("txtcodser");
						f.totrowservicios.value=total;
					  }
						total=ue_calcular_total_fila_local("txtcodservic");
						f.totrowcargos.value=total;
				      lb_borrar = confirm(" Esta seguro de eliminar este registro ?");
					  if (lb_borrar==true)
					     {
						   f.operacion.value="ELIMINAR";
						   f.action="sigesp_soc_p_registro_orden_compra.php";
						   f.submit();
					     }
					  else
					     {
					       alert("Eliminacion Cancelada !!!");
					     }
			        }
		       }
	      }
	   else
 	      {
		    alert("No tiene permiso para realizar esta operaci?n.");
	      }
     }
}

function ue_imprimir()
{
	li_imprimir=f.imprimir.value;
    tipord=f.tipord.value;
	lb_existe=f.existe.value;
	ls_codmon=f.txtcodmon.value;
	ls_codpai=f.cmbpais.value;
	ls_codest=f.cmbestado.value;
    ls_codmun=f.cmbmunicipio.value;
    ls_codpar=f.cmbparroquia.value;
	ls_tasa=f.txttascamordcom.value;
	ls_montodivi=f.txtmontotdiv.value;
	ls_codmon=f.txtcodmon.value;
	ls_ressoc="0";
	if(f.chkressoc.checked==true)
	{
		ls_ressoc="1";
	}
	if(li_imprimir==1)
	{
		if(lb_existe=="TRUE")
		{
			numordcom=f.txtnumordcom.value;
			formato=f.formato.value;
			tiporeporte=f.cmbbsf.value;
			window.open("reportes/"+formato+"?numordcom="+numordcom+"&tipord="+tipord+"&tiporeporte="+tiporeporte+"&codmon="+ls_codmon+"&codpai="+ls_codpai+"&codest="+ls_codest+"&codmun="+ls_codmun+"&codpar="+ls_codpar+"&tasa="+ls_tasa+"&montodivi="+ls_montodivi+"&codmon="+ls_codmon+"&ressoc="+ls_ressoc,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=800,height=600,left=0,top=0,location=no,resizable=yes");
		}
		else
		{
			alert("Debe existir un documento a imprimir");
		}
	}
	else
	{
		alert("No tiene permiso para realizar esta operacion");
	}
}

function ue_reload()
{
	parametros=f.parametros.value;
    tipord=f.tipord.value;
	tipsol=f.txttipsol.value;
	parametros=parametros+"&tipsol="+tipsol;
	if(tipord=="B")
	{
		proceso="AGREGARBIENES";
	}
	if(tipord=="S")
	{
		proceso="AGREGARSERVICIOS";
	}
	if(parametros!="")
	{
		divgrid = document.getElementById("bienesservicios");
		ajax=objetoAjax();
		ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
		ajax.onreadystatechange=function() {
			if (ajax.readyState==4) {
				divgrid.innerHTML = ajax.responseText
			}
		}
		ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		ajax.send("proceso="+proceso+"&cargarcargos=0"+parametros);
	}
}
//----------------------------------------------------------------------------------------------------
function ue_cambiar_partida_bien(fila, codprog, codspg, status, lugar)
 {
    f=document.formulario;
	estapro=f.txtestapro.value;
	codart=eval("document.formulario.txtcodart"+fila+".value");
	denart=eval("document.formulario.txtdenart"+fila+".value");
	codgas=eval("document.formulario.txtcodgas"+fila+".value");
	codspg=eval("document.formulario.txtcodspg"+fila+".value");
	estatus=eval("document.formulario.txtstatus"+fila+".value");
	tipsol=f.txttipsol.value;
	codgascre="";
	codspgcre="";
	estatuscre="";
	if(estapro=="1")
	{
		alert("La solicitud esta aprobada no la puede modificar.");
	}
	else
	{
	    if (uf_validar_unidad_ejecutora())
		 {
			f.crearasiento.value=0;
			window.open("sigesp_soc_cat_cuentas_spg.php?codgas="+codgas+"&codspg="+codspg+"&codart="+codart+"&denart="+denart+"&estatus="+estatus+"&lugar="+lugar+"&codgascre="+codgascre+"&codspgcre="+codspgcre+"&estatuscre="+estatuscre+"&fila="+fila+"&tipsol="+tipsol,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");
		 }
    }
 }

 function ue_cambiar_partida_servicio(fila, codprog, codspg, status, lugar)
 {

	f=document.formulario;
	estapro=f.txtestapro.value;
	codart=eval("document.formulario.txtcodser"+fila+".value");
	denart=eval("document.formulario.txtdenser"+fila+".value");
	codgas=eval("document.formulario.txtcodgas"+fila+".value");
	codspg=eval("document.formulario.txtcodspg"+fila+".value");
	estatus=eval("document.formulario.txtstatus"+fila+".value");
	tipsol=f.txttipsol.value;
	codgascre="";
	codspgcre="";
	estatuscre="";
	if(estapro=="1")
	{
		alert("La solicitud esta aprobada no la puede modificar.");
	}
	else
	{
		if (uf_validar_unidad_ejecutora())
		{
			f.crearasiento.value=0;
			window.open("sigesp_soc_cat_cuentas_spg.php?codgas="+codgas+"&codspg="+codspg+"&codart="+codart+"&denart="+denart+"&estatus="+estatus+"&lugar="+lugar+"&codgascre="+codgascre+"&codspgcre="+codspgcre+"&estatuscre="+estatuscre+"&fila="+fila+"&tipsol="+tipsol,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");
		}
    }
 }


function ue_cambiar_creditos(fila, codprog, codspg, status, lugar)
 {
    f=document.formulario;
	estapro=f.txtestapro.value;
//	codgas="";
//	codspg="";
//	estatus="";
//	codgascre=eval("document.formulario.txtcodgascre"+fila+".value");
//	codspgcre=eval("document.formulario.txtcodspgcre"+fila+".value");
//	estatuscre=eval("document.formulario.txtstatuscre"+fila+".value");
	tipord=f.tipord.value;
	tipsol=f.txttipsol.value;
	if(tipord=="S")
	{
		codart=eval("document.formulario.txtcodser"+fila+".value");
	    denart=eval("document.formulario.txtdenser"+fila+".value");
	}
	if (tipord=="B")
	{
		codart=eval("document.formulario.txtcodart"+fila+".value");
	    denart=eval("document.formulario.txtdenart"+fila+".value");
	}
	if(estapro=="1")
	{
		alert("La solicitud esta aprobada no la puede modificar.");
	}
	else
	{
		codcargo=eval("document.formulario.txtcodcar"+fila+".value");
		if (uf_validar_unidad_ejecutora())
		{
			f.crearasiento.value=0;
			window.open("sigesp_soc_cat_cuentas_spg.php?codart="+codart+"&denart="+denart+"&lugar="+lugar+"&codgascre="+codprog+"&codspgcre="+codspg+"&estatuscre="+status+"&fila="+fila+"&tipsol="+tipsol+"&codcargo="+codcargo,"catalogo","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,resizable=yes,location=no,left=50,top=50");
		}
    }
 }

function uf_validar_unidad_ejecutora()
{
  lb_valido = true;
  ls_codunieje = f.txtcodunieje.value;
  if (ls_codunieje!="")
     {
	    lb_valido = true;
	 }
  else
     {
	   lb_valido = false;
	   alert("Debe seleccionar una Unidad Ejecutora !!!");
	 }
  return lb_valido;
}

function uf_evaluate_cierre()
{
  lb_valido = true;
  li_estciespg = f.hidestciespg.value;
  li_estciespi = f.hidestciespi.value;
  if (li_estciespg==1 || li_estciespi==1)
	 {
	   lb_valido = false;
	   alert("Ya fu? procesado el Cierre Presupuestario, No pueden efectuarse movimientos, Contacte al Administrador del Sistema !!!");
	 }
  return lb_valido;
}

function ue_crear_asiento()
{
    f=document.formulario;
	valido=true;
	tipord=f.tipord.value;
    tipconpro = f.tipconpro.value;
    ls_tipsol    = f.txttipsol.value;
 	ls_codunieje = f.txtcodunieje.value;
 	ls_codfuefin = f.txtcodfuefin.value;
	numanacot=f.numanacot.value;
	codestpre=f.txtcodestpro1.value+f.txtcodestpro2.value+f.txtcodestpro3.value+f.txtcodestpro4.value+f.txtcodestpro5.value;
	estclauni=f.hidestcla.value;
	forpag=f.cmbpagele.value;
	if(numanacot!="-" && numanacot!="")
	{
		alert("Ya el asiento esta creado!!!");
	}
	else
	{
	if (tipord=="B") // Si la orden de compra es de Bienes
	 {
		recalcular=f.recalcular.value;
		if(recalcular==1)
		{
			rowbienes=ue_calcular_total_fila_local("txtcodart");
			if (rowbienes>1)
			{
				for (j=1;(j<rowbienes)&&(valido);j++)
				{
					codart=eval("document.formulario.txtcodart"+j+".value");
					canart=eval("document.formulario.txtcanart"+j+".value");
					preart=eval("document.formulario.txtpreart"+j+".value");
					totart=eval("document.formulario.txttotart"+j+".value");
					canart=ue_formato_calculo(canart);
					preart=ue_formato_calculo(preart);
					totart=ue_formato_calculo(totart);
					spgcuenta= trim(eval("document.formulario.txtcodspg"+j+".value"));
					ls_codestpro= trim(eval("document.formulario.txtcodgas"+j+".value"));
					ls_estcla= trim(eval("document.formulario.txtstatus"+j+".value"));
					if ((canart<=0)||(preart<=0))
					{
						alert("El Precio y La Cantidad del Bien "+codart+" Deben ser mayor que Cero.")
						valido=false;
					}
					if((spgcuenta=="")||(ls_codestpro=="")||(ls_estcla==""))
					{
						alert("El Bien "+codart+" debe tener una cuenta y estructura valida")
						valido=false;
					}
				}
			}
			else
			{
				alert("Debe Tener al menos un Bien Seleccionado.");
				valido=false;
			}
		}
		else
		{
			alert("Debe recalcular los montos desde el catalogo de Obtener Datos de Orden de Compra");
			valido=false;
		}
	}
	if (tipord=="S") // Si la orden de compra es de Servicios
	{
		recalcular=f.recalcular.value;
		if(recalcular==1)
		{
			rowservicios=ue_calcular_total_fila_local("txtcodser");
			if (rowservicios>1)
			{
				for (j=1;(j<rowservicios)&&(valido);j++)
				{
					codser=eval("document.formulario.txtcodser"+j+".value");
					canser=eval("document.formulario.txtcanser"+j+".value");
					preser=eval("document.formulario.txtpreser"+j+".value");
					totser=eval("document.formulario.txttotser"+j+".value");
					canser=ue_formato_calculo(canser);
					preser=ue_formato_calculo(preser);
					totser=ue_formato_calculo(totser);
					spgcuenta= trim(eval("document.formulario.txtcodspg"+j+".value"));
					ls_codestpro= trim(eval("document.formulario.txtcodgas"+j+".value"));
					ls_estcla= trim(eval("document.formulario.txtstatus"+j+".value"));
					if ((canser<=0)||(preser<=0))
					{
						alert("El Precio y La Cantidad del Servicio "+codser+" Deben ser mayor que Cero.")
						valido=false;
					}
					if((spgcuenta=="")||(ls_codestpro=="")||(ls_estcla==""))
					{
						alert("El Servicio "+codser+" debe tener una cuenta y estructura valida")
						valido=false;
					}
				}
			}
			else
			{
				alert("Debe Tener al menos un Servicio Seleccionado.");
				valido=false;
			}
		}
		else
		{
			alert("Debe recalcular los montos desde el catalogo de Obtener Datos de Orden de Compra");
			valido=false;
		}
	}

	rowcargos=ue_calcular_total_fila_local("txtcodservic");
	for (j=1;(j<=rowcargos)&&(valido);j++)
	{
		codservic=eval("document.formulario.txtcodservic"+j+".value");
		bascar=eval("document.formulario.txtbascar"+j+".value");
		moncar=eval("document.formulario.txtmoncar"+j+".value");
		cuentacargo=trim(eval("document.formulario.cuentacargo"+j+".value"));
		codprogcargo=trim(eval("document.formulario.codprogcargo"+j+".value"));
		estclacargo=trim(eval("document.formulario.estclacargo"+j+".value"));
		bascar=ue_formato_calculo(bascar);
		moncar=ue_formato_calculo(moncar);
		if ((bascar<=0))
		{
			alert("La Base Imponible para el Cargo del Bien/Servicio "+codservic+" Debe ser mayor que Cero.")
			valido=false;
		}
		if((cuentacargo=="")||(codprogcargo=="")||(estclacargo==""))
		{
			alert("El Cargo del Bien/Servicio "+codservic+" debe tener una cuenta y estructura valida")
			valido=false;
		}
	}
	if(valido)
	{
	//---------------------------------------------------------------------------------
	// Cargar los Bienes del opener y el seleccionado
	//---------------------------------------------------------------------------------
		parametros="";
		if (tipord=="B") // Si la orden de compra es de Servicios
		{
			for(j=1;(j<rowbienes)&&(valido);j++)
			{
				codart		 = eval("document.formulario.txtcodart"+j+".value");
				denart		 = eval("document.formulario.txtdenart"+j+".value");
				canart		 = eval("document.formulario.txtcanart"+j+".value");
				unidad		 = eval("document.formulario.cmbunidad"+j+".value");
				preart		 = eval("document.formulario.txtpreart"+j+".value");
				subtotart    = eval("document.formulario.txtsubtotart"+j+".value");
				carart       = eval("document.formulario.txtcarart"+j+".value");
				totart       = eval("document.formulario.txttotart"+j+".value");
				unidadfisica = eval("document.formulario.txtunidad"+j+".value");
				codunieje = eval("document.formulario.txtcoduniadmsep"+j+".value");
				denunieje = eval("document.formulario.txtdenuniadmsep"+j+".value");
				spgcuenta    = eval("document.formulario.txtcodspg"+j+".value");
				ls_codestpro = eval("document.formulario.txtcodgas"+j+".value");
				ls_estcla    = eval("document.formulario.txtstatus"+j+".value");
				ls_numsep    = eval("document.formulario.txtnumsolord"+j+".value");
				ls_codfuefin= trim(eval("document.formulario.txtcodfuefin"+j+".value"));

				if(ls_tipsol=='SOC')
				{
				   codunieje='';
				}
				parametros=parametros+"&txtcodart"+j+"="+codart+"&txtdenart"+j+"="+denart+""+
						   "&txtcanart"+j+"="+canart+"&cmbunidad"+j+"="+unidad+""+
						   "&txtpreart"+j+"="+preart+"&txtsubtotart"+j+"="+subtotart+""+
						   "&txtcarart"+j+"="+carart+"&txttotart"+j+"="+totart+""+
						   "&txtspgcuenta"+j+"="+spgcuenta+"&txtunidad"+j+"="+unidadfisica+"&txtdenuniadmsep"+j+"="+denunieje+""+
						   "&txtnumsolord"+j+"="+ls_numsep+"&txtcoduniadmsep"+j+"="+codunieje+""+
						   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+"&txtcodfuefin"+j+"="+ls_codfuefin;
			}
		parametros=parametros+"&totalbienes="+rowbienes+"&tipo="+tipord+"&tipconpro="+tipconpro+"&txtcodfuefin="+ls_codfuefin;
		}
		else
		{
			for(j=1;(j<rowservicios)&&(valido);j++)
			{
				codser		 = eval("document.formulario.txtcodser"+j+".value");
				denser		 = eval("document.formulario.txtdenser"+j+".value");
				canser		 = eval("document.formulario.txtcanser"+j+".value");
				preser		 = eval("document.formulario.txtpreser"+j+".value");
				subtotser	 = eval("document.formulario.txtsubtotser"+j+".value");
				carser		 = eval("document.formulario.txtcarser"+j+".value");
				totser       = eval("document.formulario.txttotser"+j+".value");
				spgcuenta	 = eval("document.formulario.txtcodspg"+j+".value");
				ls_codestpro = eval("document.formulario.txtcodgas"+j+".value");
				codunieje = eval("document.formulario.txtcoduniadmsep"+j+".value");
				denunieje = eval("document.formulario.txtdenuniadmsep"+j+".value");
				ls_estcla    = eval("document.formulario.txtstatus"+j+".value");
				ls_numsep    = eval("document.formulario.txtnumsolord"+j+".value");
				ls_codfuefin= trim(eval("document.formulario.txtcodfuefin"+j+".value"));

				parametros=parametros+"&txtcodser"+j+"="+codser+"&txtdenser"+j+"="+denser+""+
						   "&txtcanser"+j+"="+canser+"&txtpreser"+j+"="+preser+""+
						   "&txtsubtotser"+j+"="+subtotser+"&txtcarser"+j+"="+carser+""+
						   "&hidcodestpro"+j+"="+ls_codestpro+"&estcla"+j+"="+ls_estcla+""+
						   "&txtdenuniadmsep"+j+"="+denunieje+"&txtnumsolord"+j+"="+ls_numsep+""+
						   "&txttotser"+j+"="+totser+"&txtspgcuenta"+j+"="+spgcuenta+"&txtcoduniadmsep"+j+"="+codunieje+"&txtcodfuefin"+j+"="+ls_codfuefin;
			}
			parametros=parametros+"&totalservicios="+rowservicios+"&tipo="+tipord+"&tipconpro="+tipconpro;
		}
		//---------------------------------------------------------------------------------
		// Cargar los Cargos del opener y el seleccionado
		//---------------------------------------------------------------------------------
		//obtener el numero de filas real de los cargos y asignarlo al total row
		total=ue_calcular_total_fila_local("txtcodservic");
		document.formulario.totrowcargos.value=total;
		rowcargos=document.formulario.totrowcargos.value;
		for(j=1;(j<=rowcargos)&&(valido);j++)
		{
			codservic=eval("document.formulario.txtcodservic"+j+".value");
			codcar=eval("document.formulario.txtcodcar"+j+".value");
			dencar=eval("document.formulario.txtdencar"+j+".value");
			bascar=eval("document.formulario.txtbascar"+j+".value");
			moncar=eval("document.formulario.txtmoncar"+j+".value");
			subcargo=eval("document.formulario.txtsubcargo"+j+".value");
			cuentacargo=eval("document.formulario.cuentacargo"+j+".value");
			formulacargo = eval("document.formulario.formulacargo"+j+".value");
			ls_numsep    = eval("document.formulario.hidnumsepcar"+j+".value");
			codprogcargo=eval("document.formulario.codprogcargo"+j+".value");
			estclacargo=eval("document.formulario.estclacargo"+j+".value");
			ls_codfuefin=eval("document.formulario.txtcodfuefincre"+j+".value");

			parametros=parametros+"&txtcodservic"+j+"="+codservic+"&txtcodcar"+j+"="+codcar+
					   "&txtdencar"+j+"="+dencar+"&txtbascar"+j+"="+bascar+
					   "&txtmoncar"+j+"="+moncar+"&txtsubcargo"+j+"="+subcargo+
					   "&cuentacargo"+j+"="+cuentacargo+"&formulacargo"+j+"="+formulacargo+
					   "&hidnumsepcar"+j+"="+ls_numsep+"&codprogcargo"+j+"="+codprogcargo+
					   "&estclacargo"+j+"="+estclacargo+ "&txtcodfuefincre"+j+"="+ls_codfuefin;
		}
		totalcargos=eval(rowcargos);
		parametros=parametros+"&totalcargos="+totalcargos;
		parametros=parametros+"&codunieje="+ls_codunieje;
		parametros=parametros+"&tipsol="+ls_tipsol;
		parametros=parametros+"&codestpre="+codestpre+"";
		parametros=parametros+"&estclauni="+estclauni+"";
		parametros=parametros+"&forpag="+forpag+"";
		parametros=parametros+"&cargarcargos=1";
		if((parametros!="")&&(valido))
		{
			// Div donde se van a cargar los resultados
			divgrid = document.getElementById("bienesservicios");
			// Instancia del Objeto AJAX
			ajax=objetoAjax();
			// Pagina donde est?n los m?todos para buscar y pintar los resultados
			ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
			ajax.onreadystatechange=function(){
				if(ajax.readyState==1)
				{
					//divgrid.innerHTML = "";//<-- aqui iria la precarga en AJAX
				}
				else
				{
					if(ajax.readyState==4)
					{
						if(ajax.status==200)
						{//mostramos los datos dentro del contenedor
							divgrid.innerHTML = ajax.responseText
							f.crearasiento.value=1;
						}
						else
						{
							if(ajax.status==404)
							{
								divgrid.innerHTML = "La p?gina no existe";
							}
							else
							{//mostramos el posible error
								divgrid.innerHTML = "Error:".ajax.status;
							}
						}

					}
				}
			}
			ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			// Enviar todos los campos a la pagina para que haga el procesamiento
			ajax.send("proceso=AGREGARCUENTAS"+parametros);
		}
	}
	}//Validacion de Analisis de Cotizacion.
}

function ue_recalcularmontos()
{

	if (uf_evaluate_cierre())
	{
		estapro=0;//f.txtestapro.value;
		if(estapro=="1")
		{
			alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
		}
		else
		{
			numanacot=f.numanacot.value;
			if(numanacot!="-" && numanacot!="")
			{
				alert("No se puede modificar el contenido de esta Orden");
			}
			else
			{
				// Obtenemos el total de filas de los servicios
				totalser=ue_calcular_total_fila_local("txtcodser");
				f.totrowservicios.value=totalser;
				// Obtenemos el total de filas de los bienes
				totalart=ue_calcular_total_fila_local("txtcodart");
				f.totrowbienes.value=totalart;
				// Obtenemos el total de filas de los cargos
				totalservic=ue_calcular_total_fila_local("txtcodservic");
				f.totrowcargos.value=totalservic;
				// Obtenemos el total de filas de las cuentas
				f.crearasiento.value=0;
				tipo= f.cmbtipordcom.value;
				if(tipo=="B")
				{
					// Cargamos los valores de la fila indicada
					for(fila=1;fila<totalart;fila++)
					{
						codart=eval("f.txtcodart"+fila+".value");
						canart=eval("f.txtcanart"+fila+".value");
						preart=eval("f.txtpreart"+fila+".value");
						unidad=eval("f.cmbunidad"+fila+".value");
						canunidad=eval("f.txtunidad"+fila+".value");
						spgcuenta=eval("f.txtspgcuenta"+fila+".value");
						canart=ue_formato_calculo(canart);
						preart=ue_formato_calculo(preart);
						// Si es una fila que tiene Art?culos
						if(codart!="")
						{
							// Si la cantidad de art?culos ? el precio es mayor que cero calculamos
							if((canart>0)||(preart>0))
							{
	/*							if(unidad=="M")
								{
									// si es al mayor multiplicamos la cantidad tipeada por la cantidad de la unidad
									canart=eval(canart+"*"+canunidad);
								}
	*/							monnet=canart*preart;
								totalarticulo=0;
								monnet=redondear(monnet,2);
								eval("f.txtsubtotart"+fila+".value='"+uf_convertir(monnet)+"'");
								totalcargo=0;
								// Actualizamos los cr?ditos de ese art?culo si tiene
								ue_actualizar_creditos(codart,monnet,fila);
								totalarticulo=eval(monnet+"+"+totalcargo);
								totalarticulo=redondear(totalarticulo,2);
								totalcargo=redondear(totalcargo,2);
								eval("f.txtcarart"+fila+".value='"+uf_convertir(totalcargo)+"'");
								eval("f.txttotart"+fila+".value='"+uf_convertir(totalarticulo)+"'");
								// Actualizamos las cuentas presupuestarias
								//ue_actualizar_cuentas(tipo,spgcuenta);
								// Actualizamos los totales de la solicitud
								//ue_actualizar_totales(tipo);
							}
						}
					}
				}
				if(tipo=="S")
				{
					// Cargamos los valores de la fila indicada
					for(fila=1;fila<totalser;fila++)
					{
						codser=eval("f.txtcodser"+fila+".value");
						canser=eval("f.txtcanser"+fila+".value");
						preser=eval("f.txtpreser"+fila+".value");
						canser=ue_formato_calculo(canser);
						preser=ue_formato_calculo(preser);
						spgcuenta=eval("f.txtspgcuenta"+fila+".value");
						// Si es una fila que tiene Servicios
						if(codser!="")
						{
							// Si la cantidad de servicios ? el precio es mayor que cero calculamos
							if((canser>0)||(preser>0))
							{
								monnet=canser*preser;
								totalservicio=0;
								monnet=redondear(monnet,2);
								eval("f.txtsubtotser"+fila+".value='"+uf_convertir(monnet)+"'");
								totalcargo=0;
								// Actualizamos los cr?ditos de ese art?culo si tiene
								ue_actualizar_creditos(codser,monnet,fila);
								totalservicio=eval(monnet+"+"+totalcargo);
								totalservicio=redondear(totalservicio,2);
								eval("f.txtcarser"+fila+".value='"+uf_convertir(totalcargo)+"'");
								eval("f.txttotser"+fila+".value='"+uf_convertir(totalservicio)+"'");
								// Actualizamos las cuentas presupuestarias
								//ue_actualizar_cuentas(tipo,spgcuenta);
								// Actualizamos los totales de la solicitud
								//ue_actualizar_totales(tipo);
							}
						}
					}
				}
				f.recalcular.value=1;
		   }
		 }//Validacion analisis
	}
}
var patron = new Array(2,2,4);
var patron2 = new Array(1,3,3,3,3);

function ue_ayuda()
{
	width=(screen.width);
	height=(screen.height);
	window.open("ayudas/sigesp_ayu_soc_registro_orden_compra.pdf","Ayuda","menubar=no,toolbar=no,scrollbars=yes,width="+width+",height="+height+",resizable=yes,location=no");
}
function ue_verusuarios()
{
	f=document.formulario;
	numordcom=f.txtnumordcom.value;
	estcondat=f.cmbtipordcom.value
	// Div donde se van a cargar los resultados
	divgrid = document.getElementById('usuarios');
	// Instancia del Objeto AJAX
	ajax=objetoAjax();
	// Pagina donde est?n los m?todos para buscar y pintar los resultados
	ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
	ajax.onreadystatechange=function() {
		if (ajax.readyState==4) {
			divgrid.innerHTML = ajax.responseText
		}
	}
	ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	// Enviar todos los campos a la pagina para que haga el procesamiento
	ajax.send("numordcom="+numordcom+"&tipo="+estcondat+"&proceso=BUSCARUSUARIOS");
}

function ue_generar_numeroprefijo(as_readonly)
{
    li_estapro = f.txtestapro.value;
	if (li_estapro==1)
	   {
	     alert("La Orden de Compra est? aprobada no puede ser modificada !!!");
 	   }
	else
	   {
	     if (f.cmbtipordcom[1].selected==true)
			{
			  f.tipord.value="B";
			  numordcombie=f.numordcombie.value;
			  if (numordcombie==false)
			     {
				   alert("Este documento est? configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor dir?jase al Administrador del Sistema");
				   location.href='sigespwindow_blank.php';
				 }
			  else
			     {
				   if (as_readonly=='')
					  {
					    f.txtnumordcom.readOnly = false;
					  }
				   else
					  {
					    f.txtnumordcom.readOnly = true;
					  }
				   f.cmbtipordcom.disabled = true;
			       f.radiotipbieordcom[0].disabled = false;
			       f.radiotipbieordcom[1].disabled = false;
				 }
			}
		 if (f.cmbtipordcom[2].selected==true)
			{
			  f.tipord.value="S";
			  numordcomser=f.numordcomser.value;
			  if (numordcomser==false)
			     {
				   alert("Este documento est? configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor dir?jase al Administrador del Sistema");
				   location.href='sigespwindow_blank.php';
				 }
			  else
			     {
				   if (as_readonly=='')
					  {
					    f.txtnumordcom.readOnly = false;
					  }
				   else
					  {
					    f.txtnumordcom.readOnly = true;
					  }
				   f.cmbtipordcom.disabled = true;
				   lb_existe  = f.existe.value;
				   if (lb_existe!="TRUE")
					  {
					    ls_codpro = f.txtcodprov.value;
					    if (ls_codpro=='')
						   {
							 window.open("sigesp_soc_cat_proveedor.php","_blank","menubar=no,toolbar=no,scrollbars=yes,width=550,height=400,left=50,top=50,location=no,resizable=yes");
						   }
					  }
				 }
			}
			tipordcom=f.tipord.value;
			f.totrowbienes.value=1;
			f.totrowservicios.value=1;
			f.totrowcargos.value=0;
			f.totrowcuentas.value=1;
			f.totrowcuentascargo.value=1;
			// Div donde se van a cargar los resultados
			divgrid = document.getElementById('bienesservicios');
			// Instancia del Objeto AJAX
			ajax=objetoAjax();
			// Pagina donde est?n los m?todos para buscar y pintar los resultados
			ajax.open("POST","class_folder/sigesp_soc_c_registro_orden_compra_ajax.php",true);
			ajax.onreadystatechange=function() {
				if (ajax.readyState==4) {
					divgrid.innerHTML = ajax.responseText
				}
			}
			ajax.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			// Enviar todos los campos a la pagina para que haga el procesamiento
			ajax.send("tipo="+tipordcom+"&totalbienes=1&totalservicios=1&totalcargos=0"+"&totalcuentas=1&totalcuentascargo=1&proceso=LIMPIAR");
	      }
}

function DiferenciaFechas ()
{
    //Obtiene los datos del formulario
	f=document.formulario;
    CadenaFecha1 = f.txtperenthasta.value;
    CadenaFecha2 = f.txtperentdesde.value;
	if((CadenaFecha1!="")&&(CadenaFecha2!=""))
	{
		
		//Obtiene dia, mes y a?o
		var fecha1 = new fecha( CadenaFecha1 )   
		var fecha2 = new fecha( CadenaFecha2 )
		
		//Obtiene objetos Date
		var miFecha1 = new Date( fecha1.anio, fecha1.mes, fecha1.dia )
		var miFecha2 = new Date( fecha2.anio, fecha2.mes, fecha2.dia )
	
		//Resta fechas y redondea
		var diferencia = miFecha1.getTime() - miFecha2.getTime()
		var dias = Math.floor(diferencia / (1000 * 60 * 60 * 24))
//		dias=(parseFloat(dias) + 1);
		f.txtdiaplacom.value=dias;
		if(dias<0)
		{
		//	f.txtdiaplacom.value="";
		//	f.txtperenthasta.value="";
		} 
		return false
	}
	
}

function fecha( cadena ) {

    //Separador para la introduccion de las fechas
    var separador = "/"

    //Separa por dia, mes y a?o
    if ( cadena.indexOf( separador ) != -1 ) {
         var posi1 = 0
         var posi2 = cadena.indexOf( separador, posi1 + 1 )
         var posi3 = cadena.indexOf( separador, posi2 + 1 )
         this.dia = cadena.substring( posi1, posi2 )
         this.mes = cadena.substring( posi2 + 1, posi3 )
		 this.mes= this.mes-1;
         this.anio = cadena.substring( posi3 + 1, cadena.length )
    } else {
         this.dia = 0
         this.mes = 0
         this.anio = 0   
    }
}

</script>
<?php
if(($ls_operacion=="GUARDAR")||(($ls_operacion=="ELIMINAR")&&(!$lb_valido)))
{
	print "<script language=JavaScript>";
	print "   ue_reload();";
	print "</script>";
}
if($ls_operacion=="PREFIJO")
{
	print "<script language=JavaScript>";
	print "   ue_generar_numeroprefijo('".$ls_readonly."');";
	print "</script>";
}
?>
</html>