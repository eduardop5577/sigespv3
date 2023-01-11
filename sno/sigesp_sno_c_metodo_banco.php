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

class sigesp_sno_c_metodo_banco
{
	var $io_metodo1;
	var $io_metodo2;
	var $io_funciones;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_c_metodo_banco
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. María Roa
		// Fecha Creación: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
		$this->DS=new class_datastore();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();
		require_once("sigesp_sno_c_metodo_banco_1.php");
		$this->io_metodo1=new sigesp_sno_c_metodo_banco_1();
		require_once("sigesp_sno_c_metodo_banco_2.php");
		$this->io_metodo2=new sigesp_sno_c_metodo_banco_2();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_codnom="0000";
		$this->ls_peractnom="000";
		if(array_key_exists("la_nomina",$_SESSION))
		{
			if(array_key_exists("codnom",$_SESSION["la_nomina"]))
			{
				$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
				$this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
			}
		}
	}// end function sigesp_sno_c_metodo_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_codemp($codmet)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk
		//		   Access: public (desde la clase sigesp_sno_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true; 
		$codigo="0000"; 
		$ls_sql="SELECT sno_metodobanco.codempnom ".
				"FROM sno_metodobanco ".
				"WHERE sno_metodobanco.codemp='".$this->ls_codemp."' ".
				"   	   AND sno_metodobanco.codmet='".$codmet."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$codigo=$row["codempnom"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $codigo;
	}// end function uf_listadobanco_gendisk
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco($as_ruta,$as_metodo,$ac_codperi,$ad_fdesde,$ad_fhasta,$ad_fecproc,$adec_montot,$as_codcueban,
							 $rs_data,$as_codmetban,$as_desope,$as_quincena,$as_ref,$as_numref,$aa_seguridad,$as_tipope='',
			                 $aa_credito=array())
	{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco	
		//	    Arguments: as_ruta   // ruta donde se va aguardar el archivo
		//	    		   as_metodo   // Código del metodo a banco
		//                 ac_codperi  // codigo del periodo
		//                 ad_fdesde   // fecha desde
		//                 ad_fhasta   // fecha hasta
		//                 adec_montot // Monto total
		//                 as_codcueban // Código de la cuenta bancaria a debitar 
		//                 aa_ds_banco // arreglo (datastore) datos banco      
		//                 as_codmetban // código de método a banco 
		//                 as_desope // descripción de operación
		//                 as_quincena // Quincena  apagar
		//				   aa_seguridad // arreglo de seguridad
		//	      Returns: lb_valido True 
		//	  Description: Funcion que segun el banco, genera un archivo txt a disco para cancelación de nomina
		//	   Creado Por: Ing. María Roa
		// Fecha Creación: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$codempnom=$this->uf_buscar_codemp($as_codmetban);
		$codempnom=substr($codempnom,0,4);
	  	switch ($as_metodo)
		{
			case "BANESCO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banesco($as_ruta,$rs_data,$ad_fecproc);
				break;

			case "BANESCO_PAYMUL":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banesco_paymul($as_ruta,$rs_data,$ad_fecproc,$adec_montot,
				                                                             $as_codcueban,$as_ref);
				break;

			case "BANESCO_PAYMUL_TERCEROS":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banesco_paymul_terceros($as_ruta,$rs_data,$ad_fecproc,$adec_montot,
				                                                             $as_codcueban,$as_ref);
				break;

			case "BANFOANDES":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banfoandes($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;
				
			case "BANFOANDES 2":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banfoandes2($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$codempnom);
				break;
				
			case "BANFOANDES_IPSFA":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banfoandes_ipsfa($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;
				
			case "BIV VERSION 2":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_biv_version_2($as_ruta,$rs_data,$as_codmetban);
				break;

			case "BOD NUEVO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bod($as_ruta,$ac_codperi,$ad_fdesde,$ad_fhasta,$as_numref,$ad_fecproc,$rs_data);
				break;
				
			case "BOD VERSION 2":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bod_version_2($as_ruta,$ac_codperi,$ad_fdesde,$ad_fhasta,$as_numref,$rs_data);
				break;
				
			case "BOD VERSION 3":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bod_version_3($as_ruta,$rs_data,$ad_fecproc,$as_codmetban);
				break;
				
			case "BOD VERSION 4":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bod_version_4($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$as_codcueban,$as_numref);
				break;

			case "BOD VIEJO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bod_viejo($as_ruta,$rs_data,$ad_fecproc,$as_codmetban);
				break;

			case "CANARIAS":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_canarias($as_ruta,$rs_data,$ad_fhasta);
				break;

			case "CARACAS":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_caracas($as_ruta,$rs_data,$adec_montot,$as_codcueban);
				break;

			case "CARIBE":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_caribe($as_ruta,$rs_data,$adec_montot,$ad_fecproc);
				break;
				
			case "CARONI":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_caroni($as_ruta,$rs_data);
				break;

			case "CASA PROPIA":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_casapropia($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "CASA PROPIA 2003":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_casa_propia_2003($as_ruta,$rs_data);
				break;
				
			case "CENTRAL":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_central($as_ruta,$rs_data,$as_codcueban,$adec_montot);
				break;

			case "CENTRAL VERSION 1":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_central_v1($as_ruta,$rs_data,$as_codcueban,$adec_montot);
				break;

			case "CONFEDERADO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_confederado($as_ruta,$rs_data);
				break;
				
			case "DEL SUR E.A.P.":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_del_sur_eap($as_ruta,$rs_data,$ad_fhasta,$as_codmetban);
				break;

			case "EAP_MICASA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_eap_micasa($as_ruta,$rs_data);
				break;

			case "FONDO COMUN":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_fondo_comun($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope);
				break;

			case "FONDO COMUN 01":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_fondo_comun_01($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope,$adec_montot);
				break;

			case "INDUSTRIAL":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_industrial($as_ruta,$rs_data);
				break;

			case "LARA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_lara($as_ruta,$rs_data,$as_codcueban,$adec_montot);
				break;

			case "MERCANTIL":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_mercantil($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "MI CASA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_mi_casa($as_ruta,$rs_data);
				break;

			case "e-PROVINCIAL":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_e_provincial($as_ruta,$rs_data);
				break;
				
			case "e-PROVINCIAL_02":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_e_provincial_02($as_ruta,$rs_data);
				break;
				
			case "e-PROVINCIAL_03":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_e_provincial_03($as_ruta,$rs_data);
				break;
				
			case "e-PROVINCIAL_04":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_e_provincial_04($as_ruta,$rs_data);
				break;

			case "PROVINCIAL GUANARE":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_provincial_guanare($as_ruta,$rs_data,$ad_fecproc);
				break;

			case "PROVINCIAL NUEVO":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_provincial($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "PROVINCIAL VIEJO": 
				$lb_valido=$this->io_metodo2->uf_metodo_banco_lara($as_ruta,$rs_data,$as_codcueban,$adec_montot);
				break;
				
			case "PROVINCIAL_ALTAMIRA": 
			    $lb_valido=$this->io_metodo1->uf_metodo_banco_provincial_altamira($as_ruta,$rs_data,$as_codmetban,$as_codcueban,$adec_montot,$ad_fecproc);
				break;
				
			case "PROVINCIAL PENSIONES":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_provincial_pensiones($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;
				
			case "PROVINCIAL BBVAcash":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_provincial_BBVAcash($as_ruta,$rs_data);
				break;
				
			case "PROVINCIAL BBVAcash-1":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_provincial_BBVAcash_1($as_ruta,$rs_data);
				break;		
			
			case "SOFITASA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_sofitasa($as_ruta,$rs_data,$ad_fecproc,$as_codmetban);
				break;

			case "V2_CARONI":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_caroni_v_2($as_ruta,$rs_data,$ad_fecproc);
				break;

			case "VENEZUELA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "VENEZUELA CTA. ELECTRONICAS":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_pagotaquilla($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "VENEZUELA_SNG":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_sng($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;
				
			case "VENEZUELA PENSIONES":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_pensiones($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "VENEZUELA_2020":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_2020($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "BANPRO":
				$lb_valido=$this->io_metodo2->uf_metodo_banpro($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "BANFOTRAN":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_banfotran($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codmetban);
				break;

			case "BANFOTRAN_02":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_banfotran_02($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codmetban);
				break;

			case "VENEZUELA PAGO TAQUILLA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_pagotaquilla($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;
				
			case "VENEZUELA ESPECIAL":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuelaespecial($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "ONLINE_MERCANTIL":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_mercantilonline($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codmetban);
				break;

			case "VENEZUELA TARJETA PREPAGADA Y CUENTA ABONO":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_prepagoabono($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "BICENTENARIO":
				$lb_valido=$this->io_metodo2->uf_bicentenario($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;

			case "BANCO FEDERAL":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_federal($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope,
																	  $ad_fdesde,$ad_fhasta,$adec_montot,$as_quincena);
				break;
				
			case "BANCO FEDERAL CONSOLIDADO":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_federal_consolidado($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope,
																	  $ad_fdesde,$ad_fhasta,$adec_montot,$as_quincena);
				break;

			case "BANCO AGRICOLA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_agricola($as_ruta,$rs_data);
				break;
				
            case "CORPBANCA":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_corp_banca($as_ruta,$rs_data,$adec_montot,$ls_codperi,$ls_perides,$ls_perihas);
				break;
				
			case "CORP. BANCA":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_corp_banca_nuevo($as_ruta,$rs_data,$as_numref);
				break;
			
			case "BANCO_DEL_TESORO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
			
			case "BANCO DEL TESORO ESPECIAL":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_esp($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
			
			case "BANCO_DEL_TESORO_2008":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_2008($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
				
			case "BANCO DEL TESORO NOMINA":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_nomina($as_ruta,$rs_data,$as_numref);
				break;
				
			////APORTE8%///
			case "FONZ03":
				$lb_valido=$this->io_metodo2->uf_metodo_fonz03($as_ruta,$rs_data);
				break;
				
			////APORTE12%///
			case "FONZ03_1":
				$lb_valido=$this->io_metodo2->uf_metodo_fonz03_1($as_ruta,$rs_data);
				break;
				
			///////////////////////
			case "FONZ03 NOMINA MILITAR":
				$lb_valido=$this->io_metodo2->uf_metodo_fonz03_militar($as_ruta,$rs_data);
				break;
			
			case "VENEZUELA TARJETAS PREPAGADAS":
				$lb_valido=$this->io_metodo2->uf_metodo_tarjeta_prepagada($as_ruta,$rs_data,$ad_fecproc);
				break;

			case "BANCO BICENTENARIO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bicentenario($as_ruta,$rs_data);
				break;
			
			case "BANCO DEL TESORO EXCEL":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_excel($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
			
			case "BANCO DEL TESORO 2012":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_2012($as_ruta,$rs_data);
				break;
			
			case "BCO. VENEZUELA VIATICO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_venezuela_viatico($as_ruta,$rs_data,$adec_montot,$ad_fecproc,$as_codcueban,$as_numref,$ac_codperi);
				break;	
			
			case "BANCO DEL PUEBLO":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_pueblo($as_ruta,$rs_data);
				break;

			case "BANCO DEL PUEBLO 2":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_pueblo_2($as_ruta,$rs_data);
				break;
				
			case "BOD INTERNET":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bod_internet($as_ruta,$ac_codperi,$ad_fdesde,$ad_fhasta,$as_numref,$ad_fecproc,$rs_data);
				break;
			
			case "BANCO DEL TESORO 2012-2":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_2012_2($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
				
			case "BANCO NACIONAL DE CREDITO":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_bnc($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
			
			case "BNC2":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_bnc2($as_ruta, $rs_data, $ad_fecproc, $as_codcueban, $codempnom, $adec_montot, $as_numref, $aa_credito);
				break;
			
			case "PROVINCIAL BBVAcash-2":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_provincial_BBVAcash_2($as_ruta,$rs_data);
				break;
				
			case "BICENTENARIO 2":
				$lb_valido=$this->io_metodo2->uf_bicentenario2($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$codempnom,$adec_montot);
				break;
				
			case "BICENTENARIO_2019":
				$lb_valido=$this->io_metodo2->uf_bicentenario2019($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$codempnom,$adec_montot);
				break;
                            
			case "MERCANTIL 2":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_mercantil2($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$codempnom,$adec_montot);
				break;
				
			case "MERCANTIL 3":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_mercantil3($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$codempnom,$adec_montot,$as_codmetban);
				break;
				
			case "MERCANTIL PROVEEDORES":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_mercantil_proveedores($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$codempnom,$adec_montot,$as_tipope);
				break;

			
			case "VENEZUELA PROVEEDORES":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_proveedores($as_ruta, $rs_data, $ad_fecproc, $as_codcueban, $codempnom, $adec_montot, $as_numref, $aa_credito);
				break; 


			case "VENEZUELA PROVEEDORES 2019":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_venezuela_proveedores2019($as_ruta, $rs_data, $ad_fecproc, $as_codcueban, $codempnom, $adec_montot, $as_numref, $aa_credito);
				break; 


			case "BANCO_DEL_TESORO_2014":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_deltesoro_2014($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;

			case "BANFANB":
				$lb_valido=$this->io_metodo1->uf_metodo_banco_banfanb($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
				
			case "BOD PROVEEDORES":
				$lb_valido=$this->io_metodo2->uf_metodo_bod_proveedores($as_ruta, $rs_data, $ad_fecproc, $as_codcueban, $codempnom, $adec_montot, $as_numref, $aa_credito);
				break;
				
			case "BANESCO PROVEEDORES":
				$lb_valido=$this->io_metodo2->uf_metodo_banesco_proveedores($as_ruta, $rs_data, $ad_fecproc, $as_codcueban, $codempnom, $adec_montot, $as_numref, $aa_credito);
				break;
				
			case "PATRIA":
				$lb_valido=$this->io_metodo2->uf_metodo_patria($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;
								
			case "BANCAMIGA":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_bancamiga($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban);
				break;

			case "BANCO_GENERICO":
				$lb_valido=$this->io_metodo2->uf_metodo_banco_generico($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot);
				break;
				
			default:
				$this->io_mensajes->message("El método seleccionado no esta disponible.");
				break;

			
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generó el disco al banco Método ".$as_metodo." Período ".$ac_codperi." nómina ".$this->ls_codnom." ";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_metodo_banco
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_generar_txtmintra($as_metodo,$as_ruta,$as_store,$aa_seguridad)
	{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco	
		//	    Arguments: as_ruta   // ruta donde se va aguardar el archivo
		//	    		   as_metodo   // Código del metodo a banco
		//                 ac_codperi  // codigo del periodo
		//                 ad_fdesde   // fecha desde
		//	      Returns: lb_valido True 
		//	  Description: Funcion que segun el banco, genera un archivo txt a disco para cancelación de nomina
		//	   Creado Por: Ing. María Roa
		// Fecha Creación: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha Última Modificación : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
	  	switch ($as_metodo)
		{
			case "MINTRA":
				$lb_valido=$this->io_metodo1->uf_metodo_mintra_txt($as_ruta,$as_store);
				break;

			default:
				$this->io_mensajes->message("El método seleccionado no esta disponible.");
				break;

			
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generó el Método Mintra de RRHH ";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_metodo_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_gendisk($as_codban,$as_suspendidos,$as_quincena,$as_pagtaqnom,$rs_data,$as_tipocuenta='',$pago_otros_bancos='',$as_codmet='')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk
		//		   Access: public (desde la clase sigesp_sno_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_monto="";
		$ls_montoaux="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="round( CAST(sno_resumen.priquires as numeric), 2) as monnetres";
				$ls_montoaux="round( CAST(sno_resumen.priquires as numeric), 2)";
				break;

			case 2: // Segunda Quincena
				$ls_monto="round( CAST(sno_resumen.segquires as numeric), 2) as monnetres";
				$ls_montoaux="round( CAST(sno_resumen.segquires as numeric), 2)";
				break;

			case 3: // Mes Completo
				$ls_monto="round( CAST(sno_resumen.monnetres as numeric), 2) as monnetres";
				$ls_montoaux="round( CAST(sno_resumen.monnetres as numeric), 2)";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_personalnomina.pagbanper=1 ".
										    "   AND sno_personalnomina.pagtaqper=0 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				
				$ls_criterio2 = $ls_criterio2."   AND sno_personalnomina.pagbanper=0 ".
										    "   AND sno_personalnomina.pagtaqper=1 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				break;

			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_personalnomina.pagbanper=0 ".
										    "   AND sno_personalnomina.pagtaqper=1 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
											
				$ls_criterio2 = $ls_criterio2."   AND sno_personalnomina.pagbanper=1 ".
										    "   AND sno_personalnomina.pagtaqper=0 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban) && empty($pago_otros_bancos))
		{
			$ls_criterio = $ls_criterio." AND sno_personalnomina.codban='".$as_codban."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_personalnomina.codban='".$as_codban."' ";
		}
		if ($as_codmet=='0137')
		{
			$ls_criterio = $ls_criterio." AND sno_personalnomina.codban='".$as_codban."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_personalnomina.codban='".$as_codban."' ";
		}
		
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2')";
		}
		if(!empty($as_tipocuenta))
		{
			$ls_criterio = $ls_criterio."   AND sno_personalnomina.tipcuebanper='".$as_tipocuenta."'";
		}		
		$ls_sql="SELECT sno_personal.codper, sno_personalnomina.codban, sno_personal.cedper, sno_personal.coreleper as correo, sno_personal.nomper, sno_personal.apeper, sno_personal.nacper, ".
				"		sno_personalnomina.codcueban, sno_personalnomina.tipcuebanper, ".$ls_monto.", sno_nomina.desnom,sno_nomina.codnom, sno_nomina.tippernom,sno_personalnomina.pagbanper,".
				"		sno_personal.telmovper,sno_personal.rifper,scb_banco.codsudeban,scb_banco.codswift, ".
				"		(SELECT SUM(".$ls_montoaux.") ".
				"		  FROM sno_personalnomina, sno_resumen ".
				"  		 WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   	   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   	   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   	   AND sno_resumen.monnetres > 0 ".
				$ls_criterio.
				" 	 	  AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"         AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"         AND sno_personalnomina.codper = sno_resumen.codper ) AS totalabono, ".
				"		(SELECT SUM(".$ls_montoaux.") ".
				"		  FROM sno_personalnomina, sno_resumen ".
				"  		 WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   	   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   	   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   	   AND sno_resumen.monnetres > 0 ".
				$ls_criterio2.
				" 	 	  AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"         AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"         AND sno_personalnomina.codper = sno_resumen.codper ) AS totalprepago, ".
				"		(SELECT COUNT(sno_personalnomina.codper) ".
				"		  FROM sno_personalnomina, sno_personal, sno_resumen ".
				"  		 WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   	   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   	   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   	   AND sno_resumen.monnetres > 0 ".
				$ls_criterio.
				" 	 	  AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"         AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"         AND sno_personalnomina.codper = sno_resumen.codper ".
				"         AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	      AND sno_personal.codper = sno_personalnomina.codper ".
				"         AND sno_personal.cedper = sno_personalnomina.codcueban) AS nroprepago ".
				"  FROM sno_personal, sno_personalnomina, sno_resumen, sno_nomina,scb_banco  ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   AND sno_resumen.monnetres > 0 ".
				$ls_criterio.
				"	AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"   AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"   AND sno_personalnomina.codper = sno_resumen.codper ".
				"	AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"	AND sno_personalnomina.codemp = scb_banco.codemp ".
				"   AND sno_personalnomina.codban = scb_banco.codban ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"ORDER BY sno_personalnomina.tipcuebanper, sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_gendisk_consolidado($as_codban,$as_suspendidos,$as_quincena,$as_codnomdes,$as_codnomhas,
												 $as_codperdes,$as_codperhas,$as_pagtaqnom,$as_anocurnom,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk_consolidado
		//		   Access: public (desde la clase sigesp_snorh_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   as_codnomdes // Código de nómina desde el cual se quiere filtrar
		//	    		   as_codnomhas // Código de nómina hasta el cual se quiere filtrar
		//	    		   as_codperdes // Período desde el cual se quiere filtrar
		//	    		   as_codperhas // Período hasta el cual se quiere filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=1 ".
										    "   AND sno_hpersonalnomina.pagtaqper=0 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;

			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=0 ".
										    "   AND sno_hpersonalnomina.pagtaqper=1 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.coreleper as correo, sno_personal.nacper, ".
				"		sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, ".$ls_monto.",sno_hnomina.codnom,sno_hnomina.desnom, sno_hnomina.tippernom, ".
				"		sno_personal.telmovper,sno_personal.rifper,scb_banco.codsudeban,scb_banco.codswift ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen, sno_hnomina, scb_banco, sss_permisos_internos  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"	AND sno_hpersonalnomina.anocur = '".$as_anocurnom."'".
				"   AND sno_hresumen.codperi>='".$as_codperdes."' ".
				"   AND sno_hresumen.codperi<='".$as_codperhas."' ".
				"   AND sno_hresumen.monnetres > 0 ".
				$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"	AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"	AND sno_hpersonalnomina.codemp = scb_banco.codemp ".
				"   AND sno_hpersonalnomina.codban = scb_banco.codban ".
			 "    AND sss_permisos_internos.codsis='SNO'".
			 "    AND sss_permisos_internos.enabled=1".
			 "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
			 "    AND sno_hnomina.codemp = sss_permisos_internos.codemp ".
			 "    AND sno_hnomina.codnom = sss_permisos_internos.codintper ";                        
				"ORDER BY sno_hpersonalnomina.tipcuebanper, sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk_consolidado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk_consolidado
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_txtmintra($as_nomina,$as_perdes,$as_perhas,$as_anocurper,$as_mescurper,$as_codubifisdes,$as_codubifishas,$as_coduniadmdes,$as_coduniadmhas,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_txtmintra
		//		   Access: public (desde la clase sigesp_snorh_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   as_codnomdes // Código de nómina desde el cual se quiere filtrar
		//	    		   as_codnomhas // Código de nómina hasta el cual se quiere filtrar
		//	    		   as_codperdes // Período desde el cual se quiere filtrar
		//	    		   as_codperhas // Período hasta el cual se quiere filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		if ($as_nomina!="")
		{
			$ls_criterio .="   AND sno_hnomina.codnom='".$as_nomina."' ";
		}
		if (($as_perdes!="")&&($as_perhas!=""))
		{
			$ls_criterio .="   AND sno_hpersonalnomina.codper BETWEEN '".$as_perdes."' AND '".$as_perhas."'   ";
		}
		if (($as_codubifisdes!="")&&($as_codubifishas!=""))
		{
			$ls_criterio .="   AND sno_hpersonalnomina.codubifis BETWEEN '".$as_codubifisdes."' AND '".$as_codubifishas."'   ";
		}
		if(!empty($as_coduniadmdes))
		{
			$campos = $this->io_conexion->Concat('sno_hpersonalnomina.minorguniadm','sno_hpersonalnomina.ofiuniadm','sno_hpersonalnomina.uniuniadm','sno_hpersonalnomina.depuniadm','sno_hpersonalnomina.prouniadm');
			$ls_criterio .="   AND ".$campos.">='".substr($as_coduniadmdes,0,4).substr($as_coduniadmdes,5,2).substr($as_coduniadmdes,8,2).substr($as_coduniadmdes,11,2).substr($as_coduniadmdes,14,2)."' ";
		}
		if(!empty($as_coduniadmhas))
		{
			$campos = $this->io_conexion->Concat('sno_hpersonalnomina.minorguniadm','sno_hpersonalnomina.ofiuniadm','sno_hpersonalnomina.uniuniadm','sno_hpersonalnomina.depuniadm','sno_hpersonalnomina.prouniadm');
			$ls_criterio .="   AND ".$campos."<='".substr($as_coduniadmhas,0,4).substr($as_coduniadmhas,5,2).substr($as_coduniadmhas,8,2).substr($as_coduniadmhas,11,2).substr($as_coduniadmhas,14,2)."' ";
		}
		$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
				"		MAX(sno_personal.sexper) AS sexper, MAX(sno_personal.nacper) AS nacper, MAX(sno_personal.fecnacper) AS fecnacper, MAX(sno_hcargo.descar) AS descar,".
				"       MAX(sno_hnomina.tipnom) AS tipnom, MAX(sno_personal.fecingper) AS fecingper, MAX(sno_personal.estper) AS estper, SUM(sno_hpersonalnomina.sueintper) AS sueintper, ".
				"       MAX(sno_hpersonalnomina.codnom) AS codnom, MAX(sno_hasignacioncargo.denasicar) AS denasicar, MAX(sno_hnomina.racnom) AS racnom ".
				"  FROM sno_personal, sno_hcargo, sno_hnomina, sno_hpersonalnomina, sno_hasignacioncargo, sno_hperiodo ".
				" WHERE sno_personal.codemp='".$ls_codemp."' ".
				"   AND (sno_hpersonalnomina.staper <> '0' OR sno_hpersonalnomina.staper <> '3')".
				"   AND substr(cast(sno_hperiodo.fechasper as char(10)),1,4) = '".$as_anocurper."'".
				"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) = '".$as_mescurper."'".
				$ls_criterio.
				"   AND sno_hperiodo.codemp=sno_hnomina.codemp".
				"   AND sno_hperiodo.codnom=sno_hnomina.codnom".
				"   AND sno_hperiodo.anocur=sno_hnomina.anocurnom".
				"   AND sno_hperiodo.codperi=sno_hnomina.peractnom".
				"   AND sno_personal.codemp=sno_hpersonalnomina.codemp".
				"   AND sno_personal.codper=sno_hpersonalnomina.codper".
				"   AND sno_hpersonalnomina.codemp=sno_hcargo.codemp".
				"   AND sno_hpersonalnomina.codnom=sno_hcargo.codnom".
				"   AND sno_hpersonalnomina.anocur=sno_hcargo.anocur".
				"   AND sno_hpersonalnomina.codperi=sno_hcargo.codperi".
				"   AND sno_hpersonalnomina.codcar=sno_hcargo.codcar".
				"   AND sno_hpersonalnomina.codemp=sno_hnomina.codemp".
				"   AND sno_hpersonalnomina.codnom=sno_hnomina.codnom".
				"   AND sno_hpersonalnomina.anocur=sno_hnomina.anocurnom".
				"   AND sno_hpersonalnomina.codperi=sno_hnomina.peractnom".
				"   AND sno_hpersonalnomina.codemp=sno_hasignacioncargo.codemp".
				"   AND sno_hpersonalnomina.codnom=sno_hasignacioncargo.codnom".
				"   AND sno_hpersonalnomina.anocur=sno_hasignacioncargo.anocur".
				"   AND sno_hpersonalnomina.codperi=sno_hasignacioncargo.codperi".
				"   AND sno_hpersonalnomina.codasicar=sno_hasignacioncargo.codasicar".
				" GROUP BY sno_personal.codper ".
				" ORDER BY sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_txtmintra ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_txtmintra

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_gendisk_beneficiarios($as_codban,$as_suspendidos,$as_quincena,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk_beneficiarios
		//		   Access: public (desde la clase sigesp_sno_r_listadobeneficiario)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_resumen.priquires";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_resumen.segquires";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_resumen.monnetres";
				break;
		}
		$ls_criterio = $ls_criterio."   AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2')";
		$ls_criterio = $ls_criterio."   AND sno_beneficiario.forpagben='1' ";
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_beneficiario.codban='".$as_codban."' ";
		}
		$ls_sql="SELECT (sno_beneficiario.codben) AS codper, (sno_beneficiario.cedben) AS cedper, (sno_beneficiario.nomben) as nomper, (sno_beneficiario.apeben) as apeper, ".
				"		(sno_beneficiario.nacben) AS nacper, (sno_beneficiario.ctaban) AS codcueban, (sno_beneficiario.tipcueben) AS tipcuebanper, sno_nomina.desnom, sno_nomina.tippernom,".
				"		(CASE sno_beneficiario.monpagben WHEN 0 ".
				"										 THEN ((".$ls_monto.")*sno_beneficiario.porpagben)/100 ".
				"										 ELSE monpagben END) AS monnetres ".
				"  FROM sno_personal, sno_personalnomina, sno_resumen, sno_nomina, sno_beneficiario  ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   AND sno_resumen.monnetres > 0 ".
				$ls_criterio.
				"	AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"   AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"   AND sno_personalnomina.codper = sno_resumen.codper ".
				"	AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personal.codemp = sno_beneficiario.codemp ".
				"   AND sno_personal.codper = sno_beneficiario.codper ".
				"ORDER BY sno_personalnomina.tipcuebanper, sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk_beneficiarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk_beneficiarios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_montototal($as_codban,$as_suspendidos,$as_quincena,$as_pagtaqnom,$ad_monto,$as_tipocuenta='',$pago_otros_bancos='',$as_codmet='')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_montototal
		//		   Access: public (desde la clase sigesp_sno_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   ad_monto // monto total a pagar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="round( CAST(sno_resumen.priquires as numeric), 2)";
				break;

			case 2: // Segunda Quincena
				$ls_monto="round( CAST(sno_resumen.segquires as numeric), 2)";
				break;

			case 3: // Mes Completo
				$ls_monto="round( CAST(sno_resumen.monnetres as numeric), 2)";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_personalnomina.pagbanper=1 ".
										    "   AND sno_personalnomina.pagtaqper=0 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				break;

			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_personalnomina.pagbanper=0 ".
										    "   AND sno_personalnomina.pagtaqper=1 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban)and empty($pago_otros_bancos))
		{
			$ls_criterio = $ls_criterio." AND sno_personalnomina.codban='".$as_codban."' ";
		}
		if($as_codmet=="0137") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND sno_personalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2')";
		}
		if(!empty($as_tipocuenta))
		{
			$ls_criterio = $ls_criterio."   AND sno_personalnomina.tipcuebanper='".$as_tipocuenta."'";
		}		
		$ls_sql="SELECT sum(".$ls_monto.") as total ".
				"  FROM sno_personalnomina, sno_resumen  ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   AND ".$ls_monto." > 0 ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"   AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"   AND sno_personalnomina.codper = sno_resumen.codper ".
				" GROUP BY sno_resumen.codperi";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_load_montototal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ad_monto=$row["total"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ad_monto']=$ad_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_montototal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_montototal_consolidado($as_codban,$as_suspendidos,$as_quincena,$as_codnomdes,$as_codnomhas,$as_codperdes,
											$as_codperhas,$as_pagtaqnom,$as_anocurnom,$ad_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_montototal_consolidado
		//		   Access: public (desde la clase sigesp_sno_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   as_codnomdes // Código de nómina desde el cual se quiere filtrar
		//	    		   as_codnomhas // Código de nómina hasta el cual se quiere filtrar
		//	    		   as_codperdes // Período desde el cual se quiere filtrar
		//	    		   as_codperhas // Período hasta el cual se quiere filtrar
		//	    		   ad_monto // monto total a pagar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=1 ".
										    "   AND sno_hpersonalnomina.pagtaqper=0 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;

			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=0 ".
										    "   AND sno_hpersonalnomina.pagtaqper=1 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		$ls_sql="SELECT sum(".$ls_monto.") as total ".
				"  FROM sno_hpersonalnomina, sno_hresumen , sss_permisos_internos ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"	AND sno_hpersonalnomina.anocur = '".$as_anocurnom."'".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hresumen.codperi>='".$as_codperdes."' ".
				"   AND sno_hresumen.codperi<='".$as_codperhas."' ".
				"   AND ".$ls_monto." > 0 ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
                                "    AND sss_permisos_internos.codsis='SNO'".
                                "    AND sss_permisos_internos.enabled=1".
                                "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
                                "    AND sno_hpersonalnomina.codemp = sss_permisos_internos.codemp ".
                                "    AND sno_hpersonalnomina.codnom = sss_permisos_internos.codintper ";                        
				" GROUP BY sno_hresumen.anocur ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_load_montototal_consolidado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ad_monto=$row["total"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ad_monto']=$ad_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_montototal_consolidado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_montototal_beneficiarios($as_codban,$as_suspendidos,$as_quincena,$ad_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_montototal_beneficiarios
		//		   Access: public (desde la clase sigesp_sno_r_listadobeneficiarios)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   ad_monto // monto total a pagar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los beneficiarios que tiene el personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/11/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_resumen.priquires";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_resumen.segquires";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_resumen.monnetres";
				break;
		}
		$ls_criterio = $ls_criterio."   AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2')";
		$ls_criterio = $ls_criterio."   AND sno_beneficiario.forpagben='1' ";
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_beneficiario.codban='".$as_codban."' ";
		}
		$ls_sql="SELECT SUM(CASE sno_beneficiario.monpagben WHEN 0 ".
				"										 THEN ((".$ls_monto.")*sno_beneficiario.porpagben)/100 ".
				"										 ELSE monpagben END) AS monnetres ".
				"  FROM sno_personalnomina, sno_resumen, sno_beneficiario  ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   AND ".$ls_monto." > 0 ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"   AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"   AND sno_personalnomina.codper = sno_resumen.codper ".
				"   AND sno_personalnomina.codemp = sno_beneficiario.codemp ".
				"   AND sno_personalnomina.codper = sno_beneficiario.codper ".
				" GROUP BY sno_resumen.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_load_montototal_beneficiarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ad_monto=$row["monnetres"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ad_monto']=$ad_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_montototal
	//--------------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_gendisk2($as_codban,$as_suspendidos,$as_quincena,$as_pagtaqnom, $as_codconc,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk2
		//		   Access: public (desde la clase sigesp_sno_r_metodo_fonz)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//                 as_codconc // codigo del concepto
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 30/01/2009 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		$ls_montoaux="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_resumen.priquires as monnetres";
				$ls_montoaux="sno_resumen.priquires";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_resumen.segquires as monnetres";
				$ls_montoaux="sno_resumen.segquires";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_resumen.monnetres as monnetres";
				$ls_montoaux="sno_resumen.monnetres";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_personalnomina.pagbanper=1 ".
										    "   AND sno_personalnomina.pagtaqper=0 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				break;

			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_personalnomina.pagbanper=0 ".
										    "   AND sno_personalnomina.pagtaqper=1 ".
										    "   AND sno_personalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_personalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_personalnomina.staper='1' OR sno_personalnomina.staper='2')";
		}
		
		if (!empty($as_codconc))
		{
			$ls_criterio=" AND sno_salida.codconc='".$as_codconc."'";
		}
		$ls_sql="  SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, 
				   sno_personal.nacper, sno_personalnomina.codcueban, sno_personalnomina.tipcuebanper, 
				   sno_salida.codconc, sno_salida.valsal as monto,sno_resumen.monnetres as monnetres, 
				   sno_nomina.desnom,sno_nomina.codnom, 
				   sno_nomina.tippernom                ".
				"  FROM sno_personal, sno_personalnomina, sno_resumen, sno_nomina, sno_salida  ".
				" WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_resumen.codperi='". $this->ls_peractnom."' ".
				"   AND sno_resumen.monnetres > 0 ".
				"   AND sno_salida.valsal <> 0 ".
				$ls_criterio.
				"	AND sno_personalnomina.codemp = sno_resumen.codemp ".
				"   AND sno_personalnomina.codnom = sno_resumen.codnom ".
				"   AND sno_personalnomina.codper = sno_resumen.codper ".
				"	AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_resumen.codemp= sno_salida.codemp".
				"   AND sno_resumen.codper= sno_salida.codper".
				"   AND sno_resumen.codnom= sno_salida.codnom".
				"   AND sno_resumen.codperi= sno_salida.codperi ".
				"ORDER BY sno_personalnomina.tipcuebanper, sno_personal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk2 ERROR->".
			                           $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk2
	//---------------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------------
    function uf_listadobanco_gendisk_consolidado2($as_codban,$as_suspendidos,$as_quincena,$as_codnomdes,$as_codnomhas,
												 $as_codperdes,$as_codperhas,$as_pagtaqnom,$as_anocurnom,$as_codconc,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk_consolidado
		//		   Access: public (desde la clase sigesp_snorh_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   as_codnomdes // Código de nómina desde el cual se quiere filtrar
		//	    		   as_codnomhas // Código de nómina hasta el cual se quiere filtrar
		//	    		   as_codperdes // Período desde el cual se quiere filtrar
		//	    		   as_codperhas // Período hasta el cual se quiere filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Jennifer Rivero 
		// Fecha Creación: 30/01/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		$ls_groupby="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				$ls_groupby= " GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, ".
					    	 "          sno_personal.apeper, sno_personal.nacper, sno_hpersonalnomina.codcueban, ".
 					     	 "          sno_hpersonalnomina.tipcuebanper, sno_hresumen.priquires, ".
                         	 "          sno_hresumen.priquires, sno_hnomina.codnom,sno_hnomina.desnom, ".
                         	 "          sno_hnomina.tippernom,sno_hsalida.valsal, sno_hsalida.codconc  ";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				$ls_groupby= " GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, ".
					     	 "          sno_personal.apeper, sno_personal.nacper, sno_hpersonalnomina.codcueban, ".
 					     	 "          sno_hpersonalnomina.tipcuebanper, sno_hresumen.segquires, ".
                             "          sno_hresumen.priquires, sno_hnomina.codnom,sno_hnomina.desnom, ".
                             "          sno_hnomina.tippernom,sno_hsalida.valsal, sno_hsalida.codconc  ";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				$ls_groupby= " GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, ".
					     	 "          sno_personal.apeper, sno_personal.nacper, sno_hpersonalnomina.codcueban, ".
 					     	 "          sno_hpersonalnomina.tipcuebanper, sno_hresumen.monnetres, ".
                             "          sno_hresumen.priquires, sno_hnomina.codnom,sno_hnomina.desnom, ".
                             "          sno_hnomina.tippernom,sno_hsalida.valsal, sno_hsalida.codconc  ";
				break;
		}
		
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=1 ".
										    "   AND sno_hpersonalnomina.pagtaqper=0 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;

			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=0 ".
										    "   AND sno_hpersonalnomina.pagtaqper=1 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		if (!empty($as_codconc))
		{
			$ls_criterio=" AND sno_hsalida.codconc='".$as_codconc."'";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
		        "       sno_personal.nacper, ".
				"		sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, ".$ls_monto.",sno_hnomina.codnom,sno_hnomina.desnom, sno_hnomina.tippernom, ".
				"       sno_hsalida.valsal as monto, sno_hsalida.codconc ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen, sno_hnomina, sno_hsalida   ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"	AND sno_hpersonalnomina.anocur = '".$as_anocurnom."'".
				"   AND sno_hresumen.codperi>='".$as_codperdes."' ".
				"   AND sno_hresumen.codperi<='".$as_codperhas."' ".
				"   AND sno_hresumen.monnetres > 0 ".
				"   AND sno_hsalida.valsal <> 0 ".
				$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"	AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hsalida.codemp= sno_hresumen.codemp ".
      			"   AND sno_hsalida.codnom = sno_hresumen.codnom ".
      			"   AND sno_hsalida.codper= sno_hresumen.codper ".
      			"   AND sno_hsalida.codperi= sno_hresumen.codperi ".
      			"   AND sno_hsalida.anocur =  sno_hresumen.anocur ".$ls_groupby;
				"ORDER BY sno_hpersonalnomina.tipcuebanper, sno_personal.codper "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk_consolidado2 ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk_consolidado2
	//------------------------------------------------------------------------------------------------------------------------------------	
//---------------------------------------------------------------------------------------------------------------------------------------
    function uf_listadobanco_gendisk_consolidado2_1($as_codban,$as_suspendidos,$as_quincena,$as_codnomdes,$as_codnomhas,
												 $as_codperdes,$as_codperhas,$as_pagtaqnom,$as_anocurnom,$as_codconc,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_gendisk_consolidado2_1///// P2 APORTE DEL 12%
		//		   Access: public (desde la clase sigesp_snorh_r_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_quincena // Quincena para el cual se quiere filtar
		//	    		   as_codnomdes // Código de nómina desde el cual se quiere filtrar
		//	    		   as_codnomhas // Código de nómina hasta el cual se quiere filtrar
		//	    		   as_codperdes // Período desde el cual se quiere filtrar
		//	    		   as_codperhas // Período hasta el cual se quiere filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Karina Puertas 
		// Fecha Creación: 05/02/2020 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_monto="";
		$ls_groupby="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				$ls_groupby= " GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, ".
					    	 "          sno_personal.apeper, sno_personal.nacper, sno_hpersonalnomina.codcueban, ".
 					     	 "          sno_hpersonalnomina.tipcuebanper, sno_hresumen.priquires, ".
                         	 "          sno_hresumen.priquires, sno_hnomina.codnom,sno_hnomina.desnom, ".
                         	 "          sno_hnomina.tippernom,sno_hsalida.valsal, sno_hsalida.codconc  ";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				$ls_groupby= " GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, ".
					     	 "          sno_personal.apeper, sno_personal.nacper, sno_hpersonalnomina.codcueban, ".
 					     	 "          sno_hpersonalnomina.tipcuebanper, sno_hresumen.segquires, ".
                             "          sno_hresumen.priquires, sno_hnomina.codnom,sno_hnomina.desnom, ".
                             "          sno_hnomina.tippernom,sno_hsalida.valsal, sno_hsalida.codconc  ";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				$ls_groupby= " GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, ".
					     	 "          sno_personal.apeper, sno_personal.nacper, sno_hpersonalnomina.codcueban, ".
 					     	 "          sno_hpersonalnomina.tipcuebanper, sno_hresumen.monnetres, ".
                             "          sno_hresumen.priquires, sno_hnomina.codnom,sno_hnomina.desnom, ".
                             "          sno_hnomina.tippernom,sno_hsalida.valsal, sno_hsalida.codconc  ";
				break;
		}
		
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_hresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_hresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_hresumen.monnetres as monnetres";
				break;
		}
		switch($as_pagtaqnom)
		{
			case "0": // Depósito a banco
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=1 ".
										    "   AND sno_hpersonalnomina.pagtaqper=0 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;


			case "1": // Pago por Taquilla
				$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.pagbanper=0 ".
										    "   AND sno_hpersonalnomina.pagtaqper=1 ".
										    "   AND sno_hpersonalnomina.pagefeper=0 ";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		if (!empty($as_codconc))
		{
			$ls_criterio=" AND sno_hsalida.codconc='".$as_codconc."'";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
		        "       sno_personal.nacper, ".
				"		sno_hpersonalnomina.codcueban, sno_hpersonalnomina.tipcuebanper, ".$ls_monto.",sno_hnomina.codnom,sno_hnomina.desnom, sno_hnomina.tippernom, ".
				"       sno_hsalida.valsal as monto, sno_hsalida.codconc ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen, sno_hnomina, sno_hsalida   ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"	AND sno_hpersonalnomina.anocur = '".$as_anocurnom."'".
				"   AND sno_hresumen.codperi>='".$as_codperdes."' ".
				"   AND sno_hresumen.codperi<='".$as_codperhas."' ".
				"   AND sno_hresumen.monnetres > 0 ".
				"   AND sno_hsalida.valsal <> 0 ".
				$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"	AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hsalida.codemp= sno_hresumen.codemp ".
      			"   AND sno_hsalida.codnom = sno_hresumen.codnom ".
      			"   AND sno_hsalida.codper= sno_hresumen.codper ".
      			"   AND sno_hsalida.codperi= sno_hresumen.codperi ".
      			"   AND sno_hsalida.anocur =  sno_hresumen.anocur ".
      			"   AND sno_hsalida.tipsal =  'P2' ". /// P2 APORTE DEL 12%
      			$ls_groupby;
				"ORDER BY sno_hpersonalnomina.tipcuebanper, sno_personal.codper "; 

				//echo $ls_sql;
				//die;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_gendisk_consolidado2_1 ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk_consolidado2_1
	//------------------------------------------------------------------------------------------------------------------------------------	

	//------------------------------------------------------------------------------------------------------------------------------------	

	//---------------------------------------------------------------------------------------------------------------------------------------
    function uf_listadobanco_gendisk_tarjeta_prepagada($as_tipper,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//           Function: uf_listadobanco_gendisk_tarjeta_prepagada
		//	       Access: public (desde la clase sigesp_snorh_r_listadobanco)  
		//	    Arguments: as_tipper // Busca Tipo de Personal
		//	   Creado Por: Ramon Tineo y Yolenis Gamez 
		//     Fecha Creacion: 02-06-2010 								Fecha ultima Modificacion :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_sql="SELECT nacper, lpad(cedper,10,0) as cedper, apeper, nomper, ".
                "       (select MAX(desciu) from scv_ciudades where sno_personal.codpai = scv_ciudades.codpai and sno_personal.codest= scv_ciudades.codest) as ciudad,".
			    "       (select desest from sigesp_estados where sno_personal.codest= sigesp_estados.codest) as estado,".
			    "        sexper, replace(fecnacper,'-','') as fecnacper, ".
			    "       (select dentippersss from sno_tipopersonalsss where codtippersss = '".$as_tipper."') as descripcion ".
			    "  FROM sno_personal ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"   AND codtippersss = '".$as_tipper."' "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report METODO->uf_listadobanco_gendisk_tarjeta_prepagada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_listadobanco_gendisk_tarjeta_prepagada
	//------------------------------------------------------------------------------------------------------------------------------------
}
?>