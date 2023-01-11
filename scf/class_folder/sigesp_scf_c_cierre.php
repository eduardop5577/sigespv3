<?php
/***********************************************************************************
* @fecha de modificacion: 09/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scf_c_cierre
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_id_process;
	var $ls_codemp;
	var $io_dscuentas;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_scf_c_cierre
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."/base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once($as_path."/base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once($as_path."/base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."/base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();
		require_once("class_funciones_scf.php");
		$this->io_funciones_scf=new class_funciones_scf($as_path);		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."/base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
		require_once($as_path."shared/class_folder/class_sigesp_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_scg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spi.php");
		$this->io_intint=new class_sigesp_int_int();
		$this->io_intscg=new class_sigesp_int_scg();
		$this->io_intint->int_scg->is_codemp = $_SESSION["la_empresa"]["codemp"];
		$this->io_intscg->is_codemp = $_SESSION["la_empresa"]["codemp"];
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_anoperiodo=substr($_SESSION["la_empresa"]["periodo"],0,4);
	}// end function sigesp_scf_c_cierre
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public 
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
		unset($this->io_intscg);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generarcomprobantecierremensual($as_mes,$as_procede,$as_codban,$as_ctaban,$as_tipodestino,$as_codprovben,
												$as_comprobante,$ad_fecha,$as_descripcion,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generarcomprobantecierremensual
		//		   Access: public
		//		 Argument: as_mes	// Mes para el cual se quiere hacer el cierre
		//		 		   as_procede	// Procede del documento de Cierre 
		//		 		   as_codban	// Código de Banco
		//		 		   as_ctaban	// Cuenta de Banco
		//		 		   as_tipodestino	// Tipo destino
		//		 		   as_codprovben	// Código de Proveedor ó Beneficiario
		//		 		   as_comprobante	// Número de comprobante
		//		 		   ad_fecha	// Fecha del Comprobante
		//		 		   as_descripcion	// descripción del Comprobante
		//		 		   aa_seguridad	// Arreglo de Seguridasd
		//	  Description: Función que genera un comprobante de cierre dado un mes
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/06/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_saldofinanciera=0;
		$li_saldofiscal=0;	
		$li_saldoactivo=0;
		$li_saldopasivo=0;
		$li_saldoresultado=0;
		$this->io_sql->begin_transaction();
		$as_comprobante="CIERREMES".$as_mes.$this->ls_anoperiodo;
		$ad_fecha=$this->io_fecha->uf_last_day($as_mes,$this->ls_anoperiodo);
		
		// VERIFICAMOS QUE EL COMPROBANTE ANTERIOR EXISTA
		if($as_mes != "01")
		{
			 $as_mesant = intval($as_mes)-1;
			 $as_mesant = str_pad($as_mesant,2,"0",STR_PAD_LEFT);
			 $as_comprobante_ant="CIERREMES".$as_mesant.$this->ls_anoperiodo;
			 $ad_fecha_ant=$this->io_fecha->uf_last_day($as_mesant,$this->ls_anoperiodo);
			 $arrResultado=$this->io_intscg->uf_obtener_comprobante($this->ls_codemp,$as_procede,$as_comprobante_ant,$ad_fecha_ant,$as_codban,
																		 $as_ctaban,$as_tipodestino,$as_codprovben,$as_codprovben);
				 $lb_encontrado_ant=$arrResultado['lb_existe'];																			 
																		 
			if(!$lb_encontrado_ant)
			{
			  $this->io_mensajes->message("El Comprobante de Cierre para el mes de ".$this->io_funciones_scf->uf_obtener_nombre_mes($as_mesant)." no existe, debe cerrar dicho mes antes de cerrar ".$this->io_funciones_scf->uf_obtener_nombre_mes($as_mes).", verifique por favor");
			  return false;
			}
		}
						
		
		// VERIFICAMOS QUE EL COMPROBANTE NO EXISTA
		$arrResultado=$this->io_intscg->uf_obtener_comprobante($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_codban,
																$as_ctaban,$as_tipodestino,$as_codprovben,$as_codprovben);
		$lb_encontrado=$arrResultado['lb_existe'];																			 

		if($lb_encontrado)
		{
			$this->io_mensajes->message("El Comprobante de Cierre para el mes ".$as_mes." ya existe, no lo puede volver a procesar.");
			$lb_valido=false;
		}
		// VERIFICAMOS QUE ESTÉN DEFINIDAS LAS CUENTAS DE FINANCIERA Y FISCAL
		if($lb_valido)
		{
			$ls_cfinanciera=trim($_SESSION["la_empresa"]["c_financiera"]);
			$ls_cfiscal=trim($_SESSION["la_empresa"]["c_fiscal"]);
			if(($ls_cfinanciera=="")||($ls_cfiscal==""))
			{
				$this->io_mensajes->message("No se han definido las Cuentas Financiera y Fiscal de la Situación del Tesoro.");
				$lb_valido=false;
			}
		}
		// OBTENEMOS LOS SALDOS DE LA CUENTA FINANCIERA
		if($lb_valido)
		{ 
			$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cfinanciera,$li_saldofinanciera,$ad_fecha);
			$li_saldofinanciera = $arrResultado['adec_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de la Ejecución Financiera.");
			}
		}
		// OBTENEMOS LOS SALDOS DE LA CUENTA FISCAL
		if($lb_valido)
		{
			$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cfiscal,$li_saldofiscal,$ad_fecha);
			$li_saldofiscal = $arrResultado['adec_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de la Ejecución Fiscal.");
			}
		}
		
		// VERIFICAMOS QUE ESTÉN DEFINIDAS LAS CUENTAS DE ACTIVO Y PASIVO DE TESORO
		if($lb_valido)
		{
			$ls_cactivo=trim($_SESSION["la_empresa"]["activo_t"]);
			$ls_cpasivo=trim($_SESSION["la_empresa"]["pasivo_t"]);
			if(($ls_cactivo=="")||($ls_cpasivo==""))
			{
				$this->io_mensajes->message("No se han definido las Cuentas de Activo y Pasivo.");
				$lb_valido=false;
			}
		}
		// OBTENEMOS LOS SALDOS DE LOS ACTIVOS
		if($lb_valido)
		{    
			$arrResultado=$this->uf_load_saldo_cuentas($ls_cactivo,$ad_fecha,$li_saldoactivo);			
			$li_saldoactivo = $arrResultado['ai_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
			}
		}
		// OBTENEMOS LOS SALDOS DE LOS PASIVOS
		if($lb_valido)
		{
			$arrResultado=$this->uf_load_saldo_cuentas($ls_cpasivo,$ad_fecha,$li_saldopasivo);
			$li_saldopasivo = $arrResultado['ai_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de los Pasivos.");
			}
		}
		
		// OBTENEMOS EL RESULTADO DEL EJERCICIO
		if($lb_valido)
		{
			$li_saldoresultado=$li_saldoactivo+$li_saldopasivo;
			if($li_saldoresultado==0)
			{
				$this->io_mensajes->message("No hay información para este mes.");
				$lb_valido=false;
			}
		}
		// CREAMOS LA CABECERA DEL COMPROBANTE
		if($lb_valido)
		{
			$as_descripcion="CIERRE MENSUAL AL ".$ad_fecha;
			$li_tipo_comp=1;
			$lb_valido = $this->io_intint->uf_int_init($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_descripcion,
													   $as_tipodestino,$as_codprovben,true,$as_codban,$as_ctaban,$li_tipo_comp);
			if(!$lb_valido)
			{   
				$this->io_mensajes->message($this->io_intint->is_msg_error); 
			}
		
		}
		// CREAMOS EL ASIENTO DEL AJUSTE DEL TESORO
		if($lb_valido)
		{
			if($li_saldofinanciera<>0)
			{
				
				if(abs($li_saldofinanciera) != abs($li_saldofiscal))
				{
				 $this->io_mensajes->message("Los saldos de la Situación Financiera: ".number_format($li_saldofinanciera,2,',','.')." y Fiscal: ".number_format($li_saldofiscal,2,',','.')." no coinciden para realizar asiento de ajuste, verifique por favor");
				 return false;
				}
				
				$ls_descripcion="AJUSTES DEL RESULTADO DEL TESORO AL ".$ad_fecha;
				$ls_debhab="D";
				if($li_saldofinanciera>=0)
				{
					$ls_debhab="H";
				}
				$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfinanciera,$ls_debhab,abs($li_saldofinanciera),
																	 $as_comprobante,$as_procede,$ls_descripcion);
				if($lb_valido===false)
				{  
					$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				}
				if($lb_valido)
				{
					$ls_debhab="D";
					if($li_saldofiscal>=0)
					{
						$ls_debhab="H";
					}
					$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfiscal,$ls_debhab,abs($li_saldofiscal),
																		 $as_comprobante,$as_procede,$ls_descripcion);
					if($lb_valido===false)
					{  
						$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}
				}
			}
		}
		// CREAMOS EL ASIENTO DE SITUACIÓN DEL TESORO
		if($lb_valido)
		{
			$ls_descripcion="SITUACIÓN DEL TESORO AL ".$ad_fecha;
			$ls_debhab="H";
			if($li_saldoresultado>=0)
			{
				$ls_debhab="D";
			}
			$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfiscal,$ls_debhab,abs($li_saldoresultado),
																 $as_comprobante,$as_procede,$ls_descripcion);			
			if($lb_valido===false)
			{  
				$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);			
			}
			if($lb_valido)
			{
				$ls_debhab="D";
				if($li_saldoresultado>=0)
				{
					$ls_debhab="H";
				}
				$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfinanciera,$ls_debhab,abs($li_saldoresultado),
																	 $as_comprobante,$as_procede,$ls_descripcion);
				if($lb_valido===false)
				{  
					$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				}
			}
		}
		// GUARDAMOS EL COMPROBANTE
	    if($lb_valido)
	    {
	        $lb_valido=$this->io_intint->uf_init_end_transaccion_integracion($aa_seguridad); 
	        if($lb_valido===false)
		    {
				$this->io_mensajes->message("ERROR-> ".$this->io_intint->is_msg_error);//.$this->io_sigesp_int->is_msg_error);
		    }		   
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generó el Comprobante Contable de cierre ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$as_cedbene." Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("El Comprobante Contable de Cierre fue registrado.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Registrar el Comprobante Contable de cierre."); 
			$this->io_sql->rollback();
		}
		$arrResultado['as_comprobante']=$as_comprobante;
		$arrResultado['ad_fecha']=$ad_fecha;
		$arrResultado['as_descripcion']=$as_descripcion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_generarcomprobantecierremensual
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generarcomprobantecierremensual_metodo2($as_mes,$as_procede,$as_codban,$as_ctaban,$as_tipodestino,$as_codprovben,
														$as_comprobante,$ad_fecha,$as_descripcion,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generarcomprobantecierremensual_metodo2
		//		   Access: public
		//		 Argument: as_mes	// Mes para el cual se quiere hacer el cierre
		//		 		   as_procede	// Procede del documento de Cierre 
		//		 		   as_codban	// Código de Banco
		//		 		   as_ctaban	// Cuenta de Banco
		//		 		   as_tipodestino	// Tipo destino
		//		 		   as_codprovben	// Código de Proveedor ó Beneficiario
		//		 		   as_comprobante	// Número de comprobante
		//		 		   ad_fecha	// Fecha del Comprobante
		//		 		   as_descripcion	// descripción del Comprobante
		//		 		   aa_seguridad	// Arreglo de Seguridasd
		//	  Description: Función que genera un comprobante de cierre dado un mes
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/06/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_saldofinanciera=0;
		$li_saldofiscal=0;	
		$li_saldoactivo=0;
		$li_saldopasivo=0;
		$li_saldoresultado=0;
		$this->io_sql->begin_transaction();				
		$as_comprobante="CIERREMES".$as_mes.$this->ls_anoperiodo;
		$ad_fecha=$this->io_fecha->uf_last_day($as_mes,$this->ls_anoperiodo);
		// VERIFICAMOS QUE EL COMPROBANTE NO EXISTA
		$arrResultado=$this->io_intscg->uf_obtener_comprobante($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_codban,
																$as_ctaban,$as_tipodestino,$as_codprovben,$as_codprovben);
		$lb_encontrado=$arrResultado['lb_existe'];																			 
		if($lb_encontrado)
		{
			$this->io_mensajes->message("El Comprobante de Cierre para el mes ".$as_mes." ya existe, no lo puede volver a procesar.");
			$lb_valido=false;
		}
		// VERIFICAMOS QUE ESTÉN DEFINIDAS LAS CUENTAS DE FINANCIERA Y FISCAL
		if($lb_valido)
		{
			$ls_cfinanciera=trim($_SESSION["la_empresa"]["c_financiera"]);
			$ls_cfiscal=trim($_SESSION["la_empresa"]["c_fiscal"]);
			if(($ls_cfinanciera=="")||($ls_cfiscal==""))
			{
				$this->io_mensajes->message("No se han definido las Cuentas Financiera y Fiscal de la Situación del Tesoro.");
				$lb_valido=false;
			}
		}
		// OBTENEMOS LOS SALDOS DE LA CUENTA FINANCIERA
		if($lb_valido)
		{
			$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cfinanciera,$li_saldofinanciera,$ad_fecha);
			$li_saldofinanciera = $arrResultado['adec_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de la Ejecución Financiera.");
			}
		}
		// OBTENEMOS LOS SALDOS DE LA CUENTA FISCAL
		if($lb_valido)
		{
			$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cfiscal,$li_saldofiscal,$ad_fecha);
			$li_saldofiscal = $arrResultado['adec_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de la Ejecución Fiscal.");
			}
		}
		// VERIFICAMOS QUE ESTÉN DEFINIDAS LAS CUENTAS DE ACTIVO Y PASIVO
		if($lb_valido)
		{
			$ls_cactivo=trim($_SESSION["la_empresa"]["activo"]);
			$ls_cpasivo=trim($_SESSION["la_empresa"]["pasivo"]);
			if(($ls_cactivo=="")||($ls_cpasivo==""))
			{
				$this->io_mensajes->message("No se han definido las Cuentas de Activo y Pasivo.");
				$lb_valido=false;
			}
		}
		// OBTENEMOS LOS SALDOS DE LOS ACTIVOS
		if($lb_valido)
		{
			// Cuentas de los activos
			//110200000000,111000000000,112000000000,112200000000,112600000000,112800000000,113200000000,113003000000
			$li_saldo=0;
			$arrResultado=$this->uf_load_saldo_cuentas("1102",$ad_fecha,$li_saldo);
			$li_saldo = $arrResultado['ai_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("111",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("112",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("1122",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("1126",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("1128",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("1132",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("113003",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Activos.");
				}
			}
			if($lb_valido)
			{
				$li_saldoactivo=$li_saldoactivo+$li_saldo;
			}
		}
		// OBTENEMOS LOS SALDOS DE LOS PASIVOS
		if($lb_valido)
		{
			// Cuentas de los pasivos
			//210100000000,213300000000,219901000000
			$li_saldo=0;
			$arrResultado=$this->uf_load_saldo_cuentas("2101",$ad_fecha,$li_saldo);
			$li_saldo = $arrResultado['ai_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo de los Pasivos.");
			}
			if($lb_valido)
			{
				$li_saldopasivo=$li_saldopasivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("2133",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Pasivos.");
				}
			}
			if($lb_valido)
			{
				$li_saldopasivo=$li_saldopasivo+$li_saldo;
				$li_saldo=0;
				$arrResultado=$this->uf_load_saldo_cuentas("219901",$ad_fecha,$li_saldo);
				$li_saldo = $arrResultado['ai_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{
					$this->io_mensajes->message("No se pudo calcular el saldo de los Pasivos.");
				}
			}
		}
		// OBTENEMOS EL RESULTADO DEL EJERCICIO
		if($lb_valido)
		{
			$li_saldoresultado=$li_saldoactivo+$li_saldopasivo;
			if($li_saldoresultado==0)
			{
				$this->io_mensajes->message("No hay información para este mes.");
				$lb_valido=false;
			}
		}
		// CREAMOS LA CABECERA DEL COMPROBANTE
		if($lb_valido)
		{
			$as_descripcion="CIERRE MENSUAL AL ".$ad_fecha;
			$li_tipo_comp=1;
			$lb_valido = $this->io_intint->uf_int_init($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_descripcion,
													   $as_tipodestino,$as_codprovben,true,$as_codban,$as_ctaban,$li_tipo_comp);
			if(!$lb_valido)
			{   
				$this->io_mensajes->message($this->io_intint->is_msg_error); 
			}
		
		}
		// CREAMOS EL ASIENTO DEL AJUSTE DEL TESORO
		if($lb_valido)
		{
			if($li_saldofinanciera<>0)
			{
				$ls_descripcion="AJUSTES DEL RESULTADO DEL TESORO AL ".$ad_fecha;
				$ls_debhab="D";
				if($li_saldofinanciera>=0)
				{
					$ls_debhab="H";
				}
				$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfinanciera,$ls_debhab,abs($li_saldofinanciera),
																	 $as_comprobante,$as_procede,$ls_descripcion);
				if($lb_valido===false)
				{  
					$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				}
				if($lb_valido)
				{
					$ls_debhab="D";
					if($li_saldofiscal>=0)
					{
						$ls_debhab="H";
					}
					$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfiscal,$ls_debhab,abs($li_saldofiscal),
																		 $as_comprobante,$as_procede,$ls_descripcion);
					if($lb_valido===false)
					{  
						$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}
				}
			}
		}
		// CREAMOS EL ASIENTO DE SITUACIÓN DEL TESORO
		if($lb_valido)
		{
			$ls_descripcion="SITUACIÓN DEL TESORO AL ".$ad_fecha;
			$ls_debhab="H";
			if($li_saldoresultado>=0)
			{
				$ls_debhab="D";
			}
			$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfiscal,$ls_debhab,abs($li_saldoresultado),
																 $as_comprobante,$as_procede,$ls_descripcion);
			if($lb_valido===false)
			{  
				$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
			}
			if($lb_valido)
			{
				$ls_debhab="D";
				if($li_saldoresultado>=0)
				{
					$ls_debhab="H";
				}
				$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cfinanciera,$ls_debhab,abs($li_saldoresultado),
																	 $as_comprobante,$as_procede,$ls_descripcion);
				if($lb_valido===false)
				{  
					$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				}
			}
		}
		// GUARDAMOS EL COMPROBANTE
	    if($lb_valido)
	    {
	        $lb_valido=$this->io_intint->uf_init_end_transaccion_integracion($aa_seguridad); 
	        if($lb_valido===false)
		    {
				$this->io_msg->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
		    }		   
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generó el Comprobante Contable de cierre ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$as_cedbene." Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("El Comprobante Contable de Cierre fue registrado.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Registrar el Comprobante Contable de cierre."); 
			$this->io_sql->rollback();
		}
		$arrResultado['as_comprobante']=$as_comprobante;
		$arrResultado['ad_fecha']=$ad_fecha;
		$arrResultado['as_descripcion']=$as_descripcion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_generarcomprobantecierremensual_metodo2
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generarcomprobantecierreanual($as_procede,$as_codban,$as_ctaban,$as_tipodestino,$as_codprovben,
											  $as_comprobante,$ad_fecha,$as_descripcion,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generarcomprobantecierreanual
		//		   Access: public
		//		 Argument: as_procede	// Procede del documento de Cierre 
		//		 		   as_codban	// Código de Banco
		//		 		   as_ctaban	// Cuenta de Banco
		//		 		   as_tipodestino	// Tipo destino
		//		 		   as_codprovben	// Código de Proveedor ó Beneficiario
		//		 		   as_comprobante	// Número de comprobante
		//		 		   ad_fecha	// Fecha del Comprobante
		//		 		   as_descripcion	// Descripción del Comprobante
		//		 		   aa_seguridad	// Arreglo de Seguridasd
		//	  Description: Función que genera un comprobante de cierre Anual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_saldoresultado=0;
		$li_saldogasto=0;
		$li_saldoingreso=0;
		$li_diferencia=0;
		$ls_status_spg = 0;
		$ls_status_spi = 0;
		$arrResultado = $this->io_intscg->uf_scg_select_estatus_cierre_presupuesto($this->ls_codemp,$ls_status_spg,$ls_status_spi);
		$ls_status_spg=$arrResultado['as_status_spg'];
		$ls_status_spi=$arrResultado['as_status_spi'];
		$lb_val_cierre = $arrResultado['lb_valido'];
	   if($lb_val_cierre)
	   {
		if ($ls_status_spg==0)
		{
			$this->io_mensajes->message("No puede Ejecutar el Cierre Contable. Debe Procesar el Cierre Presupuestario de Gasto. Contacte al Administrador del Sistema !!!");
			return false;
		}
		else if ($ls_status_spi==0)
		{
			$this->io_mensajes->message("No puede Ejecutar el Cierre Contable. Debe Procesar el Cierre Presupuestario de Ingreso. Contacte al Administrador del Sistema !!!");
			return false;
		}
	   }
		
		$this->io_sql->begin_transaction();				
		$as_comprobante=$this->io_intscg->uf_fill_comprobante("CIERRE-".$this->ls_anoperiodo);
		$ad_fecha=$this->io_fecha->uf_last_day("12",$this->ls_anoperiodo);
		// VERIFICAMOS QUE EL COMPROBANTE DE CIERRE MENSUAL DE DICIEMBRE EXISTA
		$as_mesant = "12";
		$as_comprobante_ant="CIERREMES".$as_mesant.$this->ls_anoperiodo;
		$ad_fecha_ant=$this->io_fecha->uf_last_day($as_mesant,$this->ls_anoperiodo);
		$arrResultado=$this->io_intscg->uf_obtener_comprobante($this->ls_codemp,$as_procede,$as_comprobante_ant,$ad_fecha_ant,$as_codban,
																$as_ctaban,$as_tipodestino,$as_codprovben,$as_codprovben);
		$lb_encontrado_ant=$arrResultado['lb_existe'];																			 
	    if(!$lb_encontrado_ant)
		{
		  $this->io_mensajes->message("El Comprobante de Cierre Mensual para el mes de ".$this->io_funciones_scf->uf_obtener_nombre_mes($as_mesant)." no existe, debe cerrar dicho mes antes de proceder al cierre anual, verifique por favor");
		  return false;
		}
		
		// VERIFICAMOS QUE EL COMPROBANTE NO EXISTA
		$arrResultado=$this->io_intscg->uf_obtener_comprobante($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_codban,
																$as_ctaban,$as_tipodestino,$as_codprovben,$as_codprovben);
		$lb_encontrado=$arrResultado['lb_existe'];																			 
		if($lb_encontrado)
		{
			$this->io_mensajes->message("El Comprobante de Cierre del Ejercicio ".$this->ls_anoperiodo." ya existe, no lo puede volver a procesar.");
			$lb_valido=false;
		}
		// VERIFICAMOS QUE ESTÉN DEFINIDAS LAS CUENTAS DE RESULTADOS Y RESULTADOS ANTERIOR
		if($lb_valido)
		{
			$ls_cresultado=trim($_SESSION["la_empresa"]["c_resultad"]);
			$ls_cresultadoanterior=trim($_SESSION["la_empresa"]["c_resultan"]);
			if(($ls_cresultado=="")||($ls_cresultadoanterior==""))
			{
				$this->io_mensajes->message("No se han definido las Cuentas de Resultado y Resultado Anterior.");
				$lb_valido=false;
			}
		}
		// OBTENEMOS LOS SALDOS DE LA CUENTA DE RESULTADOS
		if($lb_valido)
		{
			$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cresultado,$li_saldoresultado,$ad_fecha);
			$li_saldoresultado = $arrResultado['adec_saldo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido===false)
			{
				$this->io_mensajes->message("No se pudo calcular el saldo del Resultado.");
			}
		}
		// VERIFICAMOS QUE ESTÉN DEFINIDAS LAS CUENTAS DE GASTOS E INGRESOS
		if($lb_valido)
		{
			$ls_cgasto=trim($_SESSION["la_empresa"]["gasto_f"]);
			$ls_cingreso=trim($_SESSION["la_empresa"]["ingreso_f"]);
			if(($ls_cgasto=="")||($ls_cingreso==""))
			{
				$this->io_mensajes->message("No se han definido las Cuentas de Gasto e Ingreso.");
				$lb_valido=false;
			}
		}
		
		// VERIFICAMOS QUE ESTÉ DEFINIDA LA CUENTA DE EJECUCIÓN DEL PRESUPUESTO
		if($lb_valido)
		{
			$ls_ctaejeprecie=trim($_SESSION["la_empresa"]["ctaejeprecie"]);
			if($ls_ctaejeprecie=="")
			{
				$this->io_mensajes->message("No se ha definido la cuenta de Ejecución del Presupuesto, verifique por favor");
				return false;
			}
		}
		
		// CREAMOS LA CABECERA DEL COMPROBANTE
		if($lb_valido)
		{
			$as_descripcion="CIERRE DEL EJERCICIO";
			$li_tipo_comp=1;
			$lb_valido = $this->io_intint->uf_int_init($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_descripcion,
													   $as_tipodestino,$as_codprovben,true,$as_codban,$as_ctaban,$li_tipo_comp);
			if(!$lb_valido)
			{   
				$this->io_mensajes->message($this->io_intint->is_msg_error); 
			}
		
		}
		// CREAMOS TRASLADO DE RESULTADOS
		if($lb_valido)
		{
			if($li_saldoresultado<>0)
			{
				$ls_descripcion="TRASLADO DE RESULTADOS";
				$ls_documento="1";
				$ls_debhab="D";
				if($li_saldoresultado>0)
				{
					$ls_debhab="H";
				}
				$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cresultado,$ls_debhab,abs($li_saldoresultado),
																	 $ls_documento,$as_procede,$ls_descripcion);
				if($lb_valido===false)
				{  
					$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				}
				if($lb_valido)
				{
					$ls_descripcion="TRASLADO DE RESULTADOS ANTERIORES";
					$ls_debhab="H";
					if($li_saldoresultado>0)
					{
						$ls_debhab="D";
					}
					$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cresultadoanterior,$ls_debhab,
																		 abs($li_saldoresultado),$ls_documento,$as_procede,
																		 $ls_descripcion);
					if($lb_valido===false)
					{  
						$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}
				}
			}
		}
		// CIERRE DE LAS CUENTAS DE GASTO
		if($lb_valido)
		{
			$ls_sql="SELECT sc_cuenta ".
					"  FROM scg_cuentas ".
					" WHERE status = 'C' ".
					"   AND sc_cuenta LIKE '".trim($ls_cgasto)."%' ";	
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Cierre MÉTODO->uf_generarcomprobantecierreanual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_cuenta=$rs_data->fields["sc_cuenta"];
					$li_saldo=0;
					$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cuenta,$li_saldo,$ad_fecha);					
					$li_saldo = $arrResultado['adec_saldo'];
					$lb_valido = $arrResultado['lb_valido'];
					if($lb_valido===false)
					{
						$this->io_mensajes->message("No se pudo calcular el saldo para la cuenta ".$ls_cuenta.".");
					}
					else
					{
						$li_saldogasto=doubleval($li_saldogasto)+doubleval($li_saldo);
						if($li_saldo<>0)
						{
							$ls_documento="2";
							$ls_descripcion="CIERRE DEL GASTO EJERCICIO AÑO ".$this->ls_anoperiodo;
							$ls_debhab="D";
							if($li_saldo>0)
							{
								$ls_debhab="H";
							}
							$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cuenta,$ls_debhab,abs($li_saldo),
																				 $ls_documento,$as_procede,$ls_descripcion);
							if($lb_valido===false)
							{  
								$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
							}
						}
					}
				  $rs_data->MoveNext();
				}
				$this->io_sql->free_result($rs_data);	
			}
			if($lb_valido)
			{
				if($li_saldogasto<>0)
				{
					$ls_descripcion="CIERRE DEL GASTO EJERCICIO AÑO ".$this->ls_anoperiodo;
					$ls_documento="2";
					$ls_debhab="H";
					if($li_saldogasto>0)
					{
						$ls_debhab="D";
					}
					/*$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cresultado,$ls_debhab,abs($li_saldogasto),
																		 $ls_documento,$as_procede,$ls_descripcion);*/
					$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_ctaejeprecie,$ls_debhab,abs($li_saldogasto),
																		 $ls_documento,$as_procede,$ls_descripcion);
					if($lb_valido===false)
					{  
						$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}
				}
			}
		}
		// CIERRE DE LAS CUENTAS DE INGRESO
		if($lb_valido)
		{
			$ls_sql="SELECT sc_cuenta ".
					"  FROM scg_cuentas ".
					" WHERE status = 'C' ".
					"   AND sc_cuenta LIKE '".trim($ls_cingreso)."%' ";	
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Cierre MÉTODO->uf_generarcomprobantecierreanual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$ls_cuenta=$rs_data->fields["sc_cuenta"];
					$li_saldo=0;
					$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cuenta,$li_saldo,$ad_fecha);
					$li_saldo = $arrResultado['adec_saldo'];
					$lb_valido = $arrResultado['lb_valido'];
					if($lb_valido===false)
					{
						$this->io_mensajes->message("No se pudo calcular el saldo para la cuenta ".$ls_cuenta.".");
					}
					else
					{
						$li_saldoingreso=$li_saldoingreso+$li_saldo;
						if($li_saldo<>0)
						{
							$ls_documento="3";
							$ls_descripcion="CIERRE DEL INGRESO EJERCICIO AÑO ".$this->ls_anoperiodo;
							$ls_debhab="D";
							if($li_saldo>0)
							{
								$ls_debhab="H";
							}
							$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cuenta,$ls_debhab,abs($li_saldo),
																				 $ls_documento,$as_procede,$ls_descripcion);
							if($lb_valido===false)
							{  
								$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
							}
						}
					}
				 $rs_data->MoveNext();
				}
				$this->io_sql->free_result($rs_data);	
			}
			if($lb_valido)
			{
				if($li_saldoingreso<>0)
				{
					$ls_documento="3";
					$ls_descripcion="CIERRE DEL INGRESO EJERCICIO AÑO ".$this->ls_anoperiodo;
					$ls_debhab="H";
					if($li_saldoingreso>0)
					{
						$ls_debhab="D";
					}
					/*$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cresultado,$ls_debhab,abs($li_saldoingreso),
																		 $ls_documento,$as_procede,$ls_descripcion);*/
					$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_ctaejeprecie,$ls_debhab,abs($li_saldoingreso),
																		 $ls_documento,$as_procede,$ls_descripcion);
					if($lb_valido===false)
					{  
						$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
					}
				}
			}
		}
		
		// TRASLADO DE SUPERAVIT O DEFICIT
		if($lb_valido)
		{
		 	$ld_resultado = 0;
		 	$ld_resultado = abs($li_saldoingreso) - abs($li_saldogasto);
		 	if($ld_resultado<>0)
			{
				$ls_documento="4";
				$ls_formcont=$_SESSION["la_empresa"]["formcont"];
				$ls_cuenta=$this->io_intscg->uf_pad_scg_cuenta($ls_formcont,$ls_ctaejeprecie);
				$ls_descripcion="DEFICIT AL CIERRE AÑO ".$this->ls_anoperiodo;
				$ls_debhab="H";
				if($ld_resultado>0)
				{
					$ls_debhab="D";
					$ls_descripcion="SUPERAVIT AL CIERRE AÑO ".$this->ls_anoperiodo;
				}
				$lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cuenta,$ls_debhab,abs($ld_resultado),
																	 $ls_documento,$as_procede,$ls_descripcion);
				if($lb_valido===false)
				{  
					$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				}
				else
				{
				 if($ls_debhab=="D")
				 {
				  $ls_debhab = "H";
				 }
				 elseif($ls_debhab=="H")
				 {
				  $ls_debhab = "D";
				 }
				 $lb_valido=$this->io_intint->uf_scg_insert_datastore($this->ls_codemp,$ls_cresultado,$ls_debhab,abs($ld_resultado),
																	 $ls_documento,$as_procede,$ls_descripcion);
				 if($lb_valido===false)
				 {  
					$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
				 }
				}
			}
			else
			{
				$this->io_mensajes->message("ERROR-> El resultado es Cero.");
			}
		}
		
		// GUARDAMOS EL COMPROBANTE
	    if($lb_valido)
	    {
	        $lb_valido=$this->io_intint->uf_init_end_transaccion_integracion($aa_seguridad); 
	        if($lb_valido===false)
		    {
				$this->io_mensajes->message("ERROR-> ".$this->io_sigesp_int->is_msg_error);
		    }		   
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Generó el Comprobante Contable de Cierre del Ejercicio ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$as_cedbene." Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{	
			$lb_actualizar=$this->io_intscg->uf_scg_update_estciescg($this->ls_codemp,1);
			if($lb_actualizar)
			{
			 $this->io_mensajes->message("El Comprobante Contable de Cierre del Ejercicio fue registrado exitosamente.");
			 $this->io_sql->commit();
			}
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Registrar el Comprobante Contable de Cierre del Ejercicio."); 
			$this->io_sql->rollback();
		}
		$arrResultado['as_comprobante']=$as_comprobante;
		$arrResultado['ad_fecha']=$ad_fecha;
		$arrResultado['as_descripcion']=$as_descripcion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_generarcomprobantecierreanual
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_saldo_cuentas($as_cuenta,$ad_fecha,$ai_saldo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_saldo_cuentas
		//		   Access: public
		//		 Argument: as_cuenta // Cuenta por la cual se quiere buscar el saldo
		//				   ad_fecha // Fecha hasta donde se va a calcular el saldo
		//				   ai_saldo // Saldo de todas las cuentas
		//	  Description: Función que busca en la tabla de cuentas los saldos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_saldo=0;
		$ls_sql="SELECT sc_cuenta ".
				"  FROM scg_cuentas ".
				" WHERE status = 'C' ".
				"   AND sc_cuenta LIKE '".$as_cuenta."%' ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre MÉTODO->uf_load_saldo_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			//while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cuenta=$rs_data->fields["sc_cuenta"];
				$li_saldo=0;
				$arrResultado=$this->io_intscg->uf_scg_saldo($ls_cuenta,$li_saldo,$ad_fecha);
				$li_saldo = $arrResultado['adec_saldo'];
				$lb_valido = $arrResultado['lb_valido'];
				if($lb_valido===false)
				{  
					$this->io_mensajes->message("No se pudo calcular el saldo para la cuenta ".$ls_cuenta.".");
				}
				else
				{
					$ai_saldo=$ai_saldo+$li_saldo;
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_saldo']=$ai_saldo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_saldo_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_detallesscg($as_comprobante,$ad_fecha,$as_procede,$as_codpro,$as_cedbene,$as_codban,$as_ctaban,$ai_tipcom,
								   $aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_detallesscg
		//		   Access: public
		//	    Arguments: as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_procede  // Procede del comprobante
		//				   as_codpro  // Código proveedor 
		//				   as_cedbene  // Código beneficiario
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   ai_tipcom  // Tipo de Comprobante
		//				   ai_totrowscg  // total de filas de Contabilidad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	  Description: Función que busca los detalles de un comprobante y los elimina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecha=$this->io_funciones->uf_convertirdatetobd($ad_fecha);
		$ls_sql="SELECT sc_cuenta, procede_doc, documento, debhab, monto ".
				"  FROM scg_dt_cmp ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND procede = '".$as_procede."' ".
				"	AND comprobante = '".$as_comprobante."' ".
				"	AND fecha = '".$ad_fecha."' ".
				"	AND codban = '".$as_codban."' ".
				"	AND ctaban = '".$as_ctaban."' ".
				" ORDER BY orden ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre MÉTODO->uf_delete_detallesscg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			//while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido) )
			while((!$rs_data->EOF)&&($lb_valido) )
			{
				$ls_cuenta=$rs_data->fields["sc_cuenta"];
				$ls_procededoc=$rs_data->fields["procede_doc"];
				$ls_documento=$rs_data->fields["documento"];
				$ls_debhab=$rs_data->fields["debhab"];
				$li_monto=$rs_data->fields["monto"];
				$lb_valido=$this->io_intscg->uf_scg_procesar_delete_movimiento($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,
																			   $ls_cuenta,$ls_procededoc,$ls_documento,$ls_debhab,
																			   $li_monto,$as_codban,$as_ctaban);
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion="Elimino la cuenta ".$ls_cuenta." a el Comprobante Contable de Cierre ".$as_comprobante." Procede ".$as_procede.
									" Fecha ".$ad_fecha." Beneficiario ".$as_cedbene." Proveedor ".$as_codpro.
									" Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_delete_detallesscg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete($as_comprobante,$ad_fecha,$as_procede,$as_codprovben,$as_tipodestino,$as_codban,$as_ctaban,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_scf_p_cierre.php)
		//	    Arguments: as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_procede  // Procede del comprobante
		//				   as_codprovben  // Código proveedor / beneficiario
		//				   as_tipodestino  // Tipo de Destino
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el eliminar ó False si hubo error en el eliminar
		//	  Description: Funcion que elimina el comprobante
		//	   Creado Por: Ing. Yesenia Moreno 
		// Fecha Creación: 03/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();				
		$this->io_intscg->is_codemp=$this->ls_codemp;
		$this->io_intscg->is_procedencia=$as_procede;
		$this->io_intscg->is_comprobante=$as_comprobante;
		$this->io_intscg->id_fecha=$ad_fecha;
		$this->io_intscg->as_codban=$as_codban;
		$this->io_intscg->as_ctaban=$as_ctaban;
		$ls_codpro="----------";
		$ls_cedbene="----------";
		$li_tipcom=1;
		switch($as_tipodestino)
		{
			case "P":
				 $ls_codpro=$as_codprovben;
				 break;
			case "B":
				 $ls_cedbene=$as_codprovben;
				 break;
		}
		if($lb_valido)
		{	// Eliminamos todos los detalles que tiene el comprobante
			$lb_valido=$this->uf_delete_detallesscg($as_comprobante,$ad_fecha,$as_procede,$ls_codpro,$ls_cedbene,$as_codban,
													$as_ctaban,$li_tipcom,$aa_seguridad);
		}					
		if($lb_valido)
		{		
			$lb_valido=$this->io_intscg->uf_sigesp_delete_comprobante();
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimino el Comprobante Contable de Cierre ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$ls_cedbene." Proveedor ".$ls_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{	
			$lb_actualizar=$this->io_intscg->uf_scg_update_estciescg($this->ls_codemp,0);
			if($lb_actualizar)
			{
			 $this->io_mensajes->message("El Comprobante Contable de Cierre fue eliminado.");
			 $this->io_sql->commit();
			}
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Eliminar el Comprobante Contable de Cierre."); 
			$this->io_sql->rollback();
		}

		return $lb_valido;
	}// end function uf_delete
	//-----------------------------------------------------------------------------------------------------------------------------------

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// PARA LA CONVERSIÓN MONETARIA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
?>