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

class sigesp_sno_c_ajustarcontabilizacion
{
	var $io_sql;
	var $io_mensajes;
	var $io_seguridad;
	var $io_funciones;
	var $io_sno;
	var $ls_codemp;
	var $ls_codnom;
	var $DS_R;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_c_ajustarcontabilizacion
		//		   Access: public (sigesp_sno_p_manejoperiodo)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno	
		// Fecha Creación: 15/02/2006 								
		// Modificado Por: 					Fecha Última Modificación : 08/11/2006
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_R=new class_datastore();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("class_folder/class_funciones_nomina.php");
		$this->io_fun_nomina=new class_funciones_nomina();
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();				
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
        $this->ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$this->ls_codfuefin=$_SESSION["la_nomina"]["codfuefin"];	
		
	}// end function sigesp_sno_c_ajustarcontabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sno_p_manejoperiodo)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 08/11/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_cierre_periodo2);
		unset($this->io_cierre_periodo3);
		unset($this->io_cierre_periodo4);
		unset($this->io_vacacion);
		unset($this->io_sno);
        unset($this->ls_codemp);
        unset($this->ls_codnom);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizado 
		//	    Arguments: as_codperi_actual  //  codigo del periodo actual
		//	      Returns: lb_contabilizado devuelve si esta contabilizado el perído anterior
		//	  Description: Función que se encarga de verificar si el período anterior está contabilizado
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 30/05/2006          Fecha última Modificacion : 
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_contabilizado=1;
		$ls_sql="SELECT conper, apoconper, ingconper, fidconper, fidintconper, ".
				"		(SELECT COUNT(sno_hsalida.codconc) ".
				"  		   FROM sno_hsalida ".
				" 		  WHERE sno_hsalida.codemp=sno_periodo.codemp ".
				"   		AND sno_hsalida.codnom=sno_periodo.codnom ".
				"   		AND sno_hsalida.codperi=sno_periodo.codperi ".
				"   		AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='P2') ".
				"   		AND sno_hsalida.valsal<>0) AS existeaporte, ".
				"		(SELECT COUNT(codperi) ".
				"  		   FROM sno_hperiodo ".
				" 		  WHERE codemp='".$this->ls_codemp."' ".
				"  			AND codnom='".$this->ls_codnom."' ".
				"   		AND codperi='".$this->ls_peractnom."') AS existehistorico ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_contabilizado=0;
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_contabilizado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$li_conper=$rs_data->fields["conper"];
				$li_apoconper=$rs_data->fields["apoconper"];
				$li_ingconper=$rs_data->fields["ingconper"];
				$li_fidconper=$rs_data->fields["fidconper"];
				$li_fidintconper=$rs_data->fields["fidintconper"];
				if(($li_conper=="1")||($li_apoconper=="1")||($li_ingconper=="1")||($li_fidconper=="1")||($li_fidintconper=="1"))
				{
					$lb_contabilizado=1;
				}
				else
				{
					$lb_contabilizado=0;
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
	   	return $lb_contabilizado;
    }// end function uf_contabilizado
	//---------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_ajustecontabilizacion($aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_ajustecontabilizacion 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización del período
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql->begin_transaction();
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$this->ls_codnom;
		$ls_codperi=$this->ls_peractnom;
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
		$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
		$ls_codcom=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-N"; // Comprobante de Conceptos
		$ls_codcoming=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-I"; // Comprobante de Ingresos
		$ls_codcomapo=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-A"; // Comprobante de Aportes
		$ls_descripcion=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Conceptos
		$ls_descripcioning=$ls_desnom." INGRESOS - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Conceptos
		$ls_descripcionapo=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Aportes
		
		$ls_descripcion_int=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper."Asiento Intercompañia"; // Descripción de Conceptos
		$ls_descripcionapo_int=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper."Asiento Intercompañia"; // Descripción de Aportes
		
		$ls_cuentapasivo="";
		$ls_operacionnomina="";
		$ls_operacionaporte="";
		$ls_tipodestino="";
		$ls_codpro="";
		$ls_codben="";
		$li_gennotdeb="";
		$li_genvou="";
		$li_genrecdoc="";
		$li_genrecapo="";
		$li_tipdocnom="";
		$li_tipdocapo="";
		$li_estagrapo="";
		$li_recdoccaunom="";
		$ls_tipdoccaunom="";		
		// Obtenemos la configuración de la contabilización de la nómina
		$arrResultado=$this->uf_load_configuracion_contabilizacion($ls_cuentapasivo,$ls_operacionnomina,$ls_operacionaporte,
																$ls_tipodestino,$ls_codpro,$ls_codben,$li_gennotdeb,$li_genvou,
																$li_genrecdoc,$li_genrecapo,$li_tipdocnom,$li_tipdocapo,$li_estagrapo,
																$li_recdoccaunom,$ls_tipdoccaunom);
		$ls_cuentapasivo=$arrResultado['as_cuentapasivo'];
		$ls_operacionnomina=$arrResultado['as_modo'];
		$ls_operacionaporte=$arrResultado['as_modoaporte'];
		$ls_tipodestino=$arrResultado['as_destino'];
		$ls_codpro=$arrResultado['as_codpro'];
		$ls_codben=$arrResultado['as_codben'];
		$li_gennotdeb=$arrResultado['ai_gennotdeb'];
		$li_genvou=$arrResultado['ai_genvou'];
		$li_genrecdoc=$arrResultado['ai_genrecdoc'];
		$li_genrecapo=$arrResultado['ai_genrecapo'];
		$li_tipdocnom=$arrResultado['ai_tipdocnom'];
		$li_tipdocapo=$arrResultado['ai_tipdocapo'];
		$li_estagrapo=$arrResultado['ai_estagrapo'];
		$li_recdoccaunom=$arrResultado['ai_recdoccaunom'];
		$ls_tipdoccaunom=$arrResultado['as_tipdoccaunom'];		
		$ls_codclanom=$arrResultado['as_codclanom'];		
		$ls_codclaapo=$arrResultado['as_codclaapo'];
		$ls_codclacau=$arrResultado['as_codclacau'];
		$ls_codclafid=$arrResultado['as_codclafid'];
		$ls_codclaper=$arrResultado['as_codclaper'];		
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{	// eliminamos la contabilización anterior 
			$lb_valido=$this->uf_delete_contabilizacion($ls_codperi);
		}														
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de conceptos
			$lb_valido=$this->uf_contabilizar_conceptos_spg($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
															$ls_tipodestino,$ls_descripcion,$li_genrecdoc,$li_tipdocnom,
															$li_gennotdeb,$li_genvou,$ls_codclanom);
		}
		if($lb_valido)
		{// insertamos la contabilización de contabilidad de conceptos
			if(($ls_operacionnomina!="O")||(($ls_operacionnomina=="O")&&($li_recdoccaunom=="1")))// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_contabilizar_conceptos_scg($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																$ls_tipodestino,$ls_descripcion,$ls_cuentapasivo,$li_genrecdoc,
																$li_tipdocnom,$li_gennotdeb,$li_genvou,$ls_codclanom,$ls_codclacau);
				$ls_estempcon=trim($_SESSION["la_empresa"]["estempcon"]);
				$ls_basdatcon=trim($_SESSION["la_empresa"]["basdatcon"]);
				if(($lb_valido)&&($ls_estempcon==0)&&($ls_basdatcon!=""))
				{
					// asiento contable de intercomapñias
					$lb_valido=$this->uf_contabilizar_conceptos_scg_int($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																	$ls_tipodestino,$ls_descripcion_int,$ls_cuentapasivo,
																	$li_genrecdoc,$li_tipdocnom,$li_gennotdeb,$li_genvou);
				}
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de aportes
			$lb_valido=$this->uf_contabilizar_aportes_spg($ls_codcomapo,$ls_operacionaporte,$ls_codpro,$ls_codben,
														  $ls_tipodestino,$ls_descripcionapo,$ls_cuentapasivo,
														  $li_genrecapo,$li_tipdocapo,$li_gennotdeb,$li_genvou,$ls_codclaapo);
		}
		if($lb_valido)
		{// insertamos la contabilización de contabilidad de aportes
			if($ls_operacionaporte!="O")// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_contabilizar_aportes_scg($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
				                                              $ls_descripcionapo,$li_genrecapo,$li_tipdocapo,$li_gennotdeb,
															  $li_genvou,$ls_operacionaporte,$li_estagrapo,$ls_codclaapo);
				$ls_estempcon=trim($_SESSION["la_empresa"]["estempcon"]);
				$ls_basdatcon=trim($_SESSION["la_empresa"]["basdatcon"]);
				if(($lb_valido)&&($ls_estempcon==0)&&($ls_basdatcon!=""))
				{
					// asiento contable de intercomapñias											  
					$lb_valido=$this->uf_contabilizar_aportes_scg_int($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
																  $ls_descripcionapo_int,$li_genrecapo,$li_tipdocapo,$li_gennotdeb,
																  $li_genvou,$ls_operacionaporte);
				}
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de ingresos
			$lb_valido=$this->uf_contabilizar_ingresos_spi($ls_codcoming,$ls_descripcioning);
		}
		if($lb_valido)
		{ // insertamos la contabilización de ingresos
			$lb_valido=$this->uf_contabilizar_ingresos_scg($ls_codcoming,$ls_descripcioning);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcom); // Nómina
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcomapo); // Aportes
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcoming); // Ingreso
		}
		if($lb_valido)
		{
			$ls_recdocpagperche=$_SESSION["la_nomina"]["recdocpagperche"];
			$ls_tipdocpagperche=$_SESSION["la_nomina"]["tipdocpagperche"];
			if(($ls_recdocpagperche=='1')&&($ls_tipdocpagperche!=""))
			{
				$lb_valido=$this->uf_procesar_rec_doc_pago_personal_cheque();	
			}
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Ajustó la Contabilización del Año ".$this->ls_anocurnom." período ".$this->ls_peractnom." asociado a la nómina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
		}
		if($lb_valido)
		{
			$this->io_sql->commit(); 
			$this->io_mensajes->message("El Ajuste de la contabilización fue procesado.");
		}
		else
		{
			$this->io_sql->rollback();
			$this->io_mensajes->message("Ocurrio un error al ajustar la contabilización.");
		}
		return  $lb_valido;    
	}// end function uf_procesar_ajustecontabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_configuracion_contabilizacion($as_cuentapasivo,$as_modo,$as_modoaporte,$as_destino,$as_codpro,$as_codben,
												   $ai_gennotdeb,$ai_genvou,$ai_genrecdoc,$ai_genrecapo,$ai_tipdocnom,$ai_tipdocapo,
												   $ai_estagrapo,$ai_recdoccaunom,$as_tipdoccaunom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_configuracion_contabilizacion 
		//	    Arguments: as_cuentapasivo  //  cuenta pasivo a la que va la nómina
		//	    		   as_modo  //  modo de contabilización de la nómina
		//	    		   as_modoaporte  //  modo de contabilización de los aportes
		//	    		   as_destino  //  destino de la contabilización
		//	    		   as_codpro  //  código de proveedor
		//	    		   as_codben  // código de beneficiario
		//	    		   ai_gennotdeb  // generar nota de débito
		//	    		   ai_genvou  // generar voucher
		//	    		   ai_genrecdoc  // generar recepción de documento
		//	    		   ai_genrecapo  // generar recepción de documento de aporte
		//	    		   ai_tipdocnom  // Tipo de documento de nómina
		//	    		   ai_tipdocapo  // Tipo de documento de aporte
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que busca los datos de la configuración de la contabilización de la nómina
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$as_codclanom=trim($_SESSION["la_nomina"]["codclanom"]);
		$as_codclaapo=trim($_SESSION["la_nomina"]["codclaapo"]);
		$as_codclacau=trim($_SESSION["la_nomina"]["codclacau"]);
		$as_codclafid=trim($_SESSION["la_nomina"]["codclafid"]);
		$as_codclaper=trim($_SESSION["la_nomina"]["codclaper"]);
		$li_parametros=$this->io_sno->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I");
		switch($li_parametros)
		{
			case 0: // La contabilización es global
				$as_cuentapasivo=$this->io_sno->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C");
				$as_modo=$this->io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C");
				$as_modoaporte=$this->io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$as_destino=$this->io_sno->uf_select_config("SNO","NOMINA","CONTABILIZACION DESTINO","","C");
				$ai_gennotdeb=$this->io_sno->uf_select_config("SNO","CONFIG","GENERAR NOTA DEBITO","1","I");
				$ai_genvou=$this->io_sno->uf_select_config("SNO","CONFIG","VOUCHER GENERAR","1","I");
				$ai_genrecdoc=str_pad($this->io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"),1,"0");
				$ai_genrecapo=str_pad($this->io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				$ai_tipdocnom=$this->io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO NOMINA","","C");
				$ai_tipdocapo=$this->io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO APORTE","","C");
				$ai_estagrapo=$this->io_sno->uf_select_config("SNO","CONFIG","AGRUPAR APORTES","0","I");
				$ai_recdoccaunom=$this->io_sno->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO CAUSA","0","I");
				$as_tipdoccaunom=$this->io_sno->uf_select_config("SNO","CONFIG","TIPO DOCUMENTO CAUSADO","-","C");
				switch (substr($as_destino,0,1))
				{
					case "P":
						$as_codpro=substr($as_destino,1,strlen($as_destino)-1);
						$as_destino="P";
						$as_codben="----------";
						break;
						
					case "B":
						$as_codben=substr($as_destino,1,strlen($as_destino)-1);
						$as_codpro="----------";
						$as_destino="B";
						break;
						
					default:
						$ls_con_descon=substr($as_destino,1,strlen($as_destino)-1);
						$as_destino=" ";
						$as_codpro="----------";
						$as_codben="----------";
				}
				break;
				
			case 1: // La contabilización es por nómina
				$as_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$as_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				$as_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$as_destino=trim($_SESSION["la_nomina"]["descomnom"]);
				$as_codpro=str_pad(trim($_SESSION["la_nomina"]["codpronom"]),10,"-");
				$as_codben=trim($_SESSION["la_nomina"]["codbennom"]);
				if(trim($as_codben)=="")
				{
					$as_codben=str_pad(trim($_SESSION["la_nomina"]["codbennom"]),10,"-");			
				}
				$ai_gennotdeb=trim($_SESSION["la_nomina"]["notdebnom"]);
				$ai_genvou=trim($_SESSION["la_nomina"]["numvounom"]);
				$ai_genrecdoc=str_pad(trim($_SESSION["la_nomina"]["recdocnom"]),1,"0");
				$ai_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				$ai_tipdocnom=trim($_SESSION["la_nomina"]["tipdocnom"]);
				$ai_tipdocapo=trim($_SESSION["la_nomina"]["tipdocapo"]);
				$ai_estagrapo=trim($_SESSION["la_nomina"]["estagrapo"]);			
				$ai_recdoccaunom=trim($_SESSION["la_nomina"]["recdoccaunom"]);
				$as_tipdoccaunom=trim($_SESSION["la_nomina"]["tipdoccaunom"]);
				break;
		}
		if(trim($as_destino)=="")
		{
			$lb_valido=false;
			$this->io_mensajes->message("ERROR-> La nómina debe tener una Destino de Contabilización (Proveedor ó Beneficiario).");
		}
		else
		{
			if($as_destino=="P") // Es un proveedor
			{
				if(trim($as_codpro)=="")
				{
					$lb_valido=false;
					$this->io_mensajes->message("ERROR-> Debe Seleccionar un Proveedor.");
				}
			}
			if($as_destino=="B") // Es un Beneficiario
			{
				if(trim($as_codpro)=="")
				{
					$lb_valido=false;
					$this->io_mensajes->message("ERROR-> Debe Seleccionar un Beneficiario. ");
				}
			}
		}
		if($ai_genrecdoc=="1") // Genera recepción de Documento de la Nómina.
		{
			if(trim($ai_tipdocnom)=="")
			{
				$lb_valido=false;
				$this->io_mensajes->message("ERROR-> Debe Seleccionar un Tipo de Documento,Para la Recepción de Documento de la Nómina. ");
			}
		}
		if($ai_recdoccaunom=="1") // Genera recepción de Documento de la Nómina.
		{
			if(trim($as_tipdoccaunom)=="")
			{
				$lb_valido=false;
				$this->io_mensajes->message("ERROR-> Debe Seleccionar un Tipo de Documento,Para la Recepción de Documento Causa. ");
			}
		}
		if($ai_genrecapo=="1") // Genera recepción de Documento de los aportes
		{
			if(trim($ai_tipdocapo)=="")
			{
				$lb_valido=false;
				$this->io_mensajes->message("ERROR-> Debe Seleccionar un Tipo de Documento,Para la Recepción de Documento de los Aportes. ");
			}
		}
		$arrResultado['as_cuentapasivo']=$as_cuentapasivo;
		$arrResultado['as_modo']=$as_modo;
		$arrResultado['as_modoaporte']=$as_modoaporte;
		$arrResultado['as_destino']=$as_destino;
		$arrResultado['as_codpro']=$as_codpro;
		$arrResultado['as_codben']=$as_codben;
		$arrResultado['ai_gennotdeb']=$ai_gennotdeb;
		$arrResultado['ai_genvou']=$ai_genvou;
		$arrResultado['ai_genrecdoc']=$ai_genrecdoc;
		$arrResultado['ai_genrecapo']=$ai_genrecapo;
		$arrResultado['ai_tipdocnom']=$ai_tipdocnom;
		$arrResultado['ai_tipdocapo']=$ai_tipdocapo;
		$arrResultado['ai_estagrapo']=$ai_estagrapo;
		$arrResultado['ai_recdoccaunom']=$ai_recdoccaunom;
		$arrResultado['as_tipdoccaunom']=$as_tipdoccaunom;
		$arrResultado['as_codclanom']=$as_codclanom;
		$arrResultado['as_codclaapo']=$as_codclaapo;
		$arrResultado['as_codclacau']=$as_codclacau;
		$arrResultado['as_codclafid']=$as_codclafid;
		$arrResultado['as_codclaper']=$as_codclaper;		
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_configuracion_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_contabilizacion($as_peractnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_contabilizacion
		//	      Returns: lb_valido True si se ejecuto el delete ó False si hubo error en el delete
		//	  Description: Funcion que elimina la contabilización de los conceptos en spg
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_dt_spg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$as_peractnom."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_delete_conceptos_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_dt_scg ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codperi='".$as_peractnom."' "; 
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_delete_conceptos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}		
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_dt_spi ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codperi='".$as_peractnom."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_conceptos_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}		
		}
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_dt_scg_int ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND codperi='".$as_peractnom."' ";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_delete_conceptos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}		
		}
		return $lb_valido;
    }// end function uf_delete_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_contabilizacion_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
									 	   $as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$as_cueprecon,
										   $ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,$ai_gennotdeb,$ai_genvou,$as_codcomapo,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_spg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 08/11/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		if ($as_codconc=="")
		{
			$as_codconc='----------';
		}
		$ls_sql="INSERT INTO sno_dt_spg(codemp,codnom,codperi,codcom,tipnom,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,".
				"spg_cuenta,operacion,codconc,cod_pro,ced_bene,tipo_destino,descripcion,monto,estatus,estrd,codtipdoc,estnumvou,".
				"estnotdeb,codcomapo,codfuefin,codcla)VALUES('".$this->ls_codemp."','".$this->ls_codnom."','".$this->ls_peractnom."','".$as_codcom."',".
				"'".$as_tipnom."','".$as_codestpro1."','".$as_codestpro2."','".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."',".
				"'".$as_estcla."','".$as_cueprecon."','".$as_operacionnomina."','".$as_codconc."','".$as_codpro."','".$as_codben."','".$as_tipodestino."',".
				"'".$as_descripcion."',".$ai_monto.",".$li_estatus.",".$ai_genrecdoc.",'".$ai_tipdoc."',".$ai_genvou.",".$ai_gennotdeb.",".
				"'".$as_codcomapo."','".$this->ls_codfuefin."','".$as_codcla."')";	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_insert_contabilizacion_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_contabilizacion_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$as_cuenta,$as_operacion,
									 	   $ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,$ai_gennotdeb,$ai_genvou,$as_codcomapo,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilización es aporte ó de conceptos
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   as_codconc  //  Código de concepto
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 08/11/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_sql="INSERT INTO sno_dt_scg(codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
				"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo,codcla)VALUES('".$this->ls_codemp."','".$this->ls_codnom."',".
				"'".$this->ls_peractnom."','".$as_codcom."','".$as_tipnom."','".$as_cuenta."','".$as_operacion."','".$as_codconc."',".
				"'".$as_codpro."','".$as_codben."','".$as_tipodestino."','".$as_descripcion."',".$ai_monto.",".$li_estatus.",".
				"".$ai_genrecdoc.",'".$ai_tipdoc."',".$ai_genvou.",".$ai_gennotdeb.",'".$as_codcomapo."','".$as_codcla."')";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_insert_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_contabilizacion_spi($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
									 	   $as_spicuenta,$ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,
										   $ai_gennotdeb,$ai_genvou,$as_codcomapo,$as_codestpro1,$as_codestpro2,$as_codestpro3,
										   $as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_spi
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_spicuenta  //  cuenta de ingresos
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado		
		$ai_monto=abs($ai_monto);
		$ls_sql="INSERT INTO sno_dt_spi(codemp,codnom,codperi,codcom,tipnom,spi_cuenta,operacion,codconc,cod_pro,ced_bene,tipo_destino,".
				"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo, ".
				"codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) VALUES ('".$this->ls_codemp."',".
				"'".$this->ls_codnom."','".$this->ls_peractnom."','".$as_codcom."','".$as_tipnom."','".$as_spicuenta."','".$as_operacionnomina."',".
				"'".$as_codconc."','".$as_codpro."','".$as_codben."','".$as_tipodestino."','".$as_descripcion."',".$ai_monto.",".$li_estatus.",".
				"".$ai_genrecdoc.",'".$ai_tipdoc."',".$ai_genvou.",".$ai_gennotdeb.",'".$as_codcomapo."',".
				" '".$as_codestpro1."', '".$as_codestpro2."', '".$as_codestpro3."', '".$as_codestpro4."', '".$as_codestpro5."', ".
				" '".$as_estcla."')";	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_insert_contabilizacion_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_spi
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_conceptos_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										   $ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_conceptos 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N"; // tipo de contabilización
		$ls_group="";
		$ls_order="";
		if($_SESSION["la_nomina"]["nomliq"]=='1')
		{
			$ls_tipnom="L"; // tipo de contabilización
			$ls_group=", sno_hsalida.codper ";
			$ls_order=", codper";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, sno_hconcepto.estcla ".$ls_group.
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, ".
				"		   sno_hconcepto.codestpro5, sno_hconcepto.estcla, spg_cuentas.spg_cuenta ".$ls_group;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"		spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, sno_hunidadadmin.estcla ".$ls_group.
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		   sno_hunidadadmin.codestpro5, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta ".$ls_group;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, sno_hconcepto.estcla ".$ls_group.
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		   sno_hconcepto.estcla, spg_cuentas.spg_cuenta ".$ls_group;
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"       spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, sno_hunidadadmin.estcla  ".$ls_group.
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		   sno_hunidadadmin.codestpro5, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta ".$ls_group.
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_contabilizar_conceptos_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cueprecon=$rs_data->fields["cueprecon"];
				$li_total=round($rs_data->fields["total"],2);
				if($_SESSION["la_nomina"]["nomliq"]=='1')
				{
					$ls_codconc=$rs_data->fields["codper"];			
				}
				$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
				$lb_existe=$this->uf_select_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																$ls_codestpro5,$ls_estcla,$ls_cueprecon,$as_operacionnomina,$ls_codconc);
				if (!$lb_existe)
				{
				
					$lb_valido=$this->uf_insert_contabilizacion_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,
																	$as_descripcion,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	$ls_codestpro5,$ls_estcla,$ls_cueprecon,$li_total,$ls_tipnom,$ls_codconc,
																	$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	$ls_codestpro5,$ls_estcla,$ls_cueprecon,$as_operacionnomina,$ls_codconc,$li_total);						
				}
				$rs_data->MoveNext();
			}
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_conceptos_scg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										   $as_cuentapasivo,$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$as_codcla,$as_codclacau)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_conceptos_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N";
		$ls_group="";
		$ls_order="";
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		if($_SESSION["la_nomina"]["nomliq"]=='1')
		{
			$ls_tipnom="L"; // tipo de contabilización
			$ls_group=", sno_hsalida.codper ";
			$ls_order=", codper";
		}
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}		
		$ls_codpronom=$_SESSION["la_nomina"]["codpronom"];
		$ls_codbennom=$_SESSION["la_nomina"]["codbennom"];
		$ls_espnom=$_SESSION["la_nomina"]["espnom"];
		$ls_ctnom=$_SESSION["la_nomina"]["ctnom"];
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		if($ls_tipnom=="N")
		{
			if ($_SESSION["ls_gestor"] == 'oci8po')
			{
				$ls_sql="SELECT cuenta,  operacion,  total        ".
						"  FROM conceptos_scg_ajuste  ".
						" WHERE codemp='".$this->ls_codemp."'     ".
						"   AND codnom='".$this->ls_codnom."'      ".
						"	AND anocur='".$this->ls_anocurnom."'   ".
						"	AND codperi='".$this->ls_peractnom."'  ".
						" UNION                                    ".
						"SELECT cuenta,  operacion,  total       ".
						"  FROM conceptos_scg_ajuste_int ".
						" WHERE codemp='".$this->ls_codemp."'     ".
						"   AND codnom='".$this->ls_codnom."'      ".
						"	AND anocur='".$this->ls_anocurnom."'   ".
						"	AND codperi='".$this->ls_peractnom."'  ";
			}
			else
			{
				$ls_sql="SELECT cuenta,  operacion,  total        ".
						"  FROM contabilizar_conceptos_scg_ajuste  ".
						" WHERE codemp='".$this->ls_codemp."'     ".
						"   AND codnom='".$this->ls_codnom."'      ".
						"	AND anocur='".$this->ls_anocurnom."'   ".
						"	AND codperi='".$this->ls_peractnom."'  ".
						" UNION                                    ".
						"SELECT cuenta,  operacion,  total       ".
						"  FROM contabilizar_conceptos_scg_ajuste_int ".
						" WHERE codemp='".$this->ls_codemp."'     ".
						"   AND codnom='".$this->ls_codnom."'      ".
						"	AND anocur='".$this->ls_anocurnom."'   ".
						"	AND codperi='".$this->ls_peractnom."'  ";
			}
		}
		else
		{
			if ($_SESSION["ls_gestor"] == 'oci8po')
			{
				$ls_sql="SELECT cuenta,  operacion,  total, codper        ".
						"  FROM con_scg_ajuste_liqui  ".
						" WHERE codemp='".$this->ls_codemp."'     ".
						"   AND codnom='".$this->ls_codnom."'      ".
						"	AND anocur='".$this->ls_anocurnom."'   ".
						"	AND codperi='".$this->ls_peractnom."'  ";
			}
			else
			{
				$ls_sql="SELECT cuenta,  operacion,  total, codper        ".
						"  FROM contabilizar_conceptos_scg_ajuste_liquidacion  ".
						" WHERE codemp='".$this->ls_codemp."'     ".
						"   AND codnom='".$this->ls_codnom."'      ".
						"	AND anocur='".$this->ls_anocurnom."'   ".
						"	AND codperi='".$this->ls_peractnom."'  ";
			}
		}
		if($as_operacionnomina=="O") // Si el modo de contabilizar la nómina es Compromete .
		{
			if($ls_tipnom=="N")
			{				
				if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
				{
					$as_codcla=$as_codclacau;
					$ls_sql=$ls_sql." UNION ".
							"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
							"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, cxp_clasificador_rd ".
							" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
							"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
							"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
							"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
							"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
							"   AND sno_hsalida.valsal <> 0 ".
							"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
							"   AND sno_hpersonalnomina.pagefeper = 0 ".
							"   AND scg_cuentas.status = 'C'".
							"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
							"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
							"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
							"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
							"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
							"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
							"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
							"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
							"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
							"   AND sno_hnomina.codemp = cxp_clasificador_rd.codemp ".
							"   AND sno_hnomina.codclacau = cxp_clasificador_rd.codcla ".
							"   AND cxp_clasificador_rd.codemp = scg_cuentas.codemp ".
							"   AND cxp_clasificador_rd.sc_cuenta = scg_cuentas.sc_cuenta ".
							" GROUP BY scg_cuentas.sc_cuenta ";				
					$ls_sql=$ls_sql." UNION ".
							"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
							"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
							" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
							"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
							"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
							"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
							"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
							"   AND sno_hsalida.valsal <> 0".
							"   AND sno_hpersonalnomina.pagbanper = 0 ".
							"   AND sno_hpersonalnomina.pagtaqper = 0 ".
							"   AND sno_hpersonalnomina.pagefeper = 1 ".
							"   AND scg_cuentas.status = 'C'".
							"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
							"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
							"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
							"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
							"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
							"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
							"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
							" GROUP BY scg_cuentas.sc_cuenta ";
				}
				else
				{
					if($ls_ctnom!=1)
					{
						$ls_sql=$ls_sql." UNION ".
								"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
								"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_proveedor ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
								"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
								"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
								"   AND sno_hsalida.valsal <> 0 ".
								"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
								"   AND sno_hpersonalnomina.pagefeper = 0 ".
								"   AND scg_cuentas.status = 'C'".
								"   AND sno_hnomina.descomnom = 'P'".
								"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
								"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
								"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								"   AND sno_hnomina.codemp = rpc_proveedor.codemp ".
								"   AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
								"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
								"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta ";
						$ls_sql=$ls_sql." UNION ".
								"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
								"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_beneficiario ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
								"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
								"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
								"   AND sno_hsalida.valsal <> 0 ".
								"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
								"   AND sno_hpersonalnomina.pagefeper = 0 ".
								"   AND scg_cuentas.status = 'C'".
								"   AND sno_hnomina.descomnom = 'B'".
								"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
								"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
								"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								"   AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
								"   AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
								"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
								"   AND  ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta ";
						$ls_sql=$ls_sql." UNION ".
								"SELECT scg_cuentas.sc_cuenta as cuenta,CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
								"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
								"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
								"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
								"   AND sno_hsalida.valsal <> 0".
								"   AND sno_hpersonalnomina.pagbanper = 0 ".
								"   AND sno_hpersonalnomina.pagtaqper = 0 ".
								"   AND sno_hpersonalnomina.pagefeper = 1 ".
								"   AND scg_cuentas.status = 'C'".
								"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
								"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
								"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
								" GROUP BY scg_cuentas.sc_cuenta ";
					}
					else
					{
						if(($ls_espnom==1)&&($ls_ctnom==1))
						{
							$ls_sql=$ls_sql." UNION ".
									" SELECT ".$ls_scctaprov." as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
									" FROM sno_hsalida, rpc_proveedor, sno_hnomina, scg_cuentas ".
									" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
									" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
									" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
									" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
									" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
									" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
									" AND sno_hsalida.valsal <> 0 ".
									" AND sno_hnomina.descomnom = 'P'".
									" AND sno_hnomina.codemp = sno_hsalida.codemp ".
									" AND sno_hnomina.codnom = sno_hsalida.codnom ".
									" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
									" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
									" AND sno_hnomina.codemp = rpc_proveedor.codemp ".
									" AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
									" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
									" AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaprov." ";
							$ls_sql=$ls_sql." UNION ".
									" SELECT ".$ls_scctaben." as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
									" FROM sno_hsalida, rpc_beneficiario, sno_hnomina, scg_cuentas ".
									" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
									" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
									" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
									" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
									" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
									" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
									" AND sno_hsalida.valsal <> 0 ".
									" AND sno_hnomina.descomnom = 'B'".
									" AND sno_hnomina.codemp = sno_hsalida.codemp ".
									" AND sno_hnomina.codnom = sno_hsalida.codnom ".
									" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
									" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
									" AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
									" AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
									" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
									" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaben." ";
						}
					}
				}
			}
		}
		elseif($as_operacionnomina=="OC") // Si el modo de contabilizar la nómina es Compromete y Causa tomamos la cuenta pasivo de la nómina.
		{
			if($ai_genrecdoc=="0") // No se genera Recepción de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$as_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "MYSQLI":
						$ls_cadena="CONVERT('".$as_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.",  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$as_cuentapasivo."' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_banco.codemp ".
						"   AND sno_hsalida.codnom = sno_banco.codnom ".
						"   AND sno_hsalida.codperi = sno_banco.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
						"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepción de documentos
			{
				if($ls_tipnom=="N")
				{				
					if($ls_ctnom!=1)
					{
						if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
						{
							$ls_sql=$ls_sql." UNION ".
									"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
									"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, cxp_clasificador_rd ".
									" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
									"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
									"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
									"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
									"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
									"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
									"   AND sno_hsalida.valsal <> 0 ".
									"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
									"   AND sno_hpersonalnomina.pagefeper = 0 ".
									"   AND scg_cuentas.status = 'C'".
									"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
									"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
									"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
									"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
									"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
									"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
									"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
									"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
									"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
									"   AND sno_hnomina.codemp = cxp_clasificador_rd.codemp ".
									"   AND sno_hnomina.codclanom = cxp_clasificador_rd.codcla ".
									"   AND cxp_clasificador_rd.codemp = scg_cuentas.codemp ".
									"   AND cxp_clasificador_rd.sc_cuenta = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta ";
						}
						else
						{
							$ls_sql=$ls_sql." UNION ".
									"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
									"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_proveedor ".
									" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
									"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
									"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
									"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
									"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
									"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
									"   AND sno_hsalida.valsal <> 0 ".
									"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
									"   AND sno_hpersonalnomina.pagefeper = 0 ".
									"   AND scg_cuentas.status = 'C'".
									"   AND sno_hnomina.descomnom = 'P'".
									"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
									"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
									"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
									"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
									"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
									"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
									"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
									"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
									"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
									"   AND sno_hnomina.codemp = rpc_proveedor.codemp ".
									"   AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
									"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
									"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta ";
							$ls_sql=$ls_sql." UNION ".
									"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
									"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_beneficiario ".
									" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
									"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
									"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
									"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
									"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
									"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
									"   AND sno_hsalida.valsal <> 0 ".
									"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
									"   AND sno_hpersonalnomina.pagefeper = 0 ".
									"   AND scg_cuentas.status = 'C'".
									"   AND sno_hnomina.descomnom = 'B'".
									"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
									"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
									"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
									"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
									"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
									"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
									"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
									"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
									"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
									"   AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
									"   AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
									"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
									"   AND  ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta ";
						}
					}
					else
					{
						if(($ls_espnom==1)&&($ls_ctnom==1))
						{
							if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
							{
								$ls_sql=$ls_sql." UNION ".
										" SELECT scg_cuentas.sc_cuentaas cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
										" FROM sno_hsalida, cxp_clasificador_rd, sno_hnomina, scg_cuentas ".
										" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
										" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
										" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
										" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
										" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
										" AND sno_hsalida.valsal <> 0 ".
										" AND sno_hnomina.descomnom = 'P'".
										" AND sno_hnomina.codemp = sno_hsalida.codemp ".
										" AND sno_hnomina.codnom = sno_hsalida.codnom ".
										" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
										" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
										" AND sno_hnomina.codemp = cxp_clasificador_rd.codemp ".
										" AND sno_hnomina.codclanom = cxp_clasificador_rd.codcla ".
										" AND scg_cuentas.codemp = cxp_clasificador_rd.codemp ".
										" AND scg_cuentas.sc_cuenta = cxp_clasificador_rd.sc_cuenta ".
										" GROUP BY scg_cuentas.sc_cuenta ";
							}
							else
							{
								$ls_sql=$ls_sql." UNION ".
										" SELECT ".$ls_scctaprov." as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
										" FROM sno_hsalida, rpc_proveedor, sno_hnomina, scg_cuentas ".
										" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
										" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
										" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
										" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
										" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
										" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
										" AND sno_hsalida.valsal <> 0 ".
										" AND sno_hnomina.descomnom = 'P'".
										" AND sno_hnomina.codemp = sno_hsalida.codemp ".
										" AND sno_hnomina.codnom = sno_hsalida.codnom ".
										" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
										" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
										" AND sno_hnomina.codemp = rpc_proveedor.codemp ".
										" AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
										" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
										" AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
										" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaprov." ";
								$ls_sql=$ls_sql." UNION ".
										" SELECT ".$ls_scctaben." as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
										" FROM sno_hsalida, rpc_beneficiario, sno_hnomina, scg_cuentas ".
										" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
										" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
										" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
										" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
										" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
										" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
										" AND sno_hsalida.valsal <> 0 ".
										" AND sno_hnomina.descomnom = 'B'".
										" AND sno_hnomina.codemp = sno_hsalida.codemp ".
										" AND sno_hnomina.codnom = sno_hsalida.codnom ".
										" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
										" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
										" AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
										" AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
										" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
										" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
										" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaben." ";
							}
						}
					}
				}
				else
				{		
					if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
					{
						$ls_sql=$ls_sql." UNION ".
								"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, sno_hsalida.codper ".
								"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_personal, sno_hnomina, cxp_clasificador_rd ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
								"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
								"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
								"   AND sno_hsalida.valsal <> 0 ".
								"   AND scg_cuentas.status = 'C'".
								"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
								"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
								"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
								"   AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
								"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
								"   AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
								"   AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
								"   AND sno_hnomina.codemp = cxp_clasificador_rd.codemp ".
								"   AND sno_hnomina.codclafid = cxp_clasificador_rd.codclafid ".
								"   AND cxp_clasificador_rd.codemp = scg_cuentas.codemp ".
								"   AND cxp_clasificador_rd.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta, sno_hsalida.codper ";
					}
					else
					{
						$ls_sql=$ls_sql." UNION ".
								"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, sno_hsalida.codper ".
								"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_personal, rpc_beneficiario ".
								" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
								"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
								"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
								"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
								"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
								"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
								"   AND sno_hsalida.valsal <> 0 ".
								"   AND scg_cuentas.status = 'C'".
								"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
								"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
								"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
								"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
								"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
								"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
								"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
								"   AND sno_personal.codemp = rpc_beneficiario.codemp ".
								"   AND sno_personal.cedper = rpc_beneficiario.ced_bene ".
								"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
								"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta, sno_hsalida.codper ";
					}	
				}
			}
			if(($ls_tipnom=="N")&&($ls_ctnom==0))
			{				
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
						"   AND sno_hsalida.valsal <> 0".
						"   AND sno_hpersonalnomina.pagbanper = 0 ".
						"   AND sno_hpersonalnomina.pagtaqper = 0 ".
						"   AND sno_hpersonalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				}
		}
		else
		{
			if(($ls_espnom==1)&&($ls_ctnom==1))
			{
				$ls_sql=$ls_sql." UNION ".
						" SELECT ".$ls_scctaprov." as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						" FROM sno_hsalida, rpc_proveedor, sno_hnomina, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
						" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
						" AND sno_hsalida.valsal <> 0 ".
					    " AND sno_hnomina.descomnom = 'P'".
						" AND sno_hnomina.codemp = sno_hsalida.codemp ".
						" AND sno_hnomina.codnom = sno_hsalida.codnom ".
						" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						" AND sno_hnomina.codemp = rpc_proveedor.codemp ".
						" AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
						" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
						" AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaprov."";
				$ls_sql=$ls_sql." UNION ".
						" SELECT ".$ls_scctaben." as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						" FROM sno_hsalida, rpc_beneficiario, sno_hnomina, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						" AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						" AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						" AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
						" AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
						" AND sno_hsalida.valsal <> 0 ".
					    " AND sno_hnomina.descomnom = 'B'".
						" AND sno_hnomina.codemp = sno_hsalida.codemp ".
						" AND sno_hnomina.codnom = sno_hsalida.codnom ".
						" AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						" AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						" AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
						" AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
						" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
						" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaben." ";
			}
			else
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
						"   AND sno_hsalida.valsal <> 0".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_banco.codemp ".
						"   AND sno_hsalida.codnom = sno_banco.codnom ".
						"   AND sno_hsalida.codperi = sno_banco.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
						"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
						"   AND sno_hsalida.valsal <> 0".
						"   AND sno_hpersonalnomina.pagbanper = 0 ".
						"   AND sno_hpersonalnomina.pagtaqper = 0 ".
						"   AND sno_hpersonalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
		}
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_contabilizar_conceptos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cuenta=$rs_data->fields["cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$li_total=number_format($rs_data->fields["total"],2,".","");
				if ($li_total<0)
				{
					$li_total=number_format($rs_data->fields["total"]*-1,2,".","");
				}
				$ls_codconc="0000000001";
				if($ls_tipnom=="L")
				{
					$ls_codconc=$rs_data->fields["codper"];
				}
				$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;

				$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
				if (!$lb_existe)
				{
					$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																	$as_descripcion,$ls_cuenta,$ls_operacion,$li_total,
																	$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																	$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																	$ls_codconc,$li_total);						
				}
				$rs_data->MoveNext();
			}	
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_conceptos_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_aportes_spg($as_codcom,$as_operacionaporte,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										 $as_cuentapasivo,$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_aportes_spg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionaporte  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="A"; // tipo de contabilización
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		spg_cuentas.spg_cuenta AS cueprepatcon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total,sno_hconcepto.estcla , ".
				"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, ".
				"		   sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, sno_hconcepto.estcla, spg_cuentas.spg_cuenta, ".
				"		   sno_hconcepto.codprov, sno_hconcepto.cedben ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"       spg_cuentas.spg_cuenta AS cueprepatcon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, sno_hunidadadmin.estcla, ".
				"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hunidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codconc, sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, ".
				"          sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta, ".
				"          sno_hconcepto.codprov, sno_hconcepto.cedben ".
				" ORDER BY codconc, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprepatcon, codprov, cedben ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_contabilizar_aportes_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cueprepatcon=$rs_data->fields["cueprepatcon"];
				$li_total=abs(round($rs_data->fields["total"],2));
				$ls_codpro=$rs_data->fields["codprov"];
				$ls_cedben=$rs_data->fields["cedben"];
				$ls_codconc=$rs_data->fields["codconc"];
				if ($ai_tipdocapo=="")
				{
					$ai_tipdocapo="--";
				}
				if($ls_codpro=="----------")
				{
					$ls_tipodestino="B";
				}
				if($ls_cedben=="----------")
				{
					$ls_tipodestino="P";
				}
				$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
				
				
				$lb_existe=$this->uf_select_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																$ls_codestpro5,$ls_estcla,$ls_estcla,$ls_cueprepatcon,$as_operacionaporte,$ls_codconc);
				if (!$lb_existe)
				{
					$lb_valido=$this->uf_insert_contabilizacion_spg($as_codcom,$as_operacionaporte,$ls_codpro,$ls_cedben,$ls_tipodestino,
																	$as_descripcion,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	$ls_codestpro5,$ls_estcla,$ls_cueprepatcon,$li_total,$ls_tipnom,
																	$ls_codconc,$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	$ls_codestpro5,$ls_estcla,$ls_cueprepatcon,$as_operacionaporte,$ls_codconc,$li_total);						
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_aportes_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_aportes_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$ai_genrecapo,$ai_tipdocapo,
										 $ai_gennotdeb,$ai_genvou,$as_operacionaporte,$ai_estagrapo,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_aportes_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_operacionaporte  //  operación del aporte
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 08/11/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="A";
		
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaaltapo"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad		
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
			$ls_sql="SELECT cuenta,operacion, total, codprov, cedben, codconc ".
					"  FROM aportes_scg_ajuste                   ".
					" WHERE codemp='".$this->ls_codemp."'                     ".
					"   AND codnom='".$this->ls_codnom."'                     ".
					"	AND anocur='".$this->ls_anocurnom."'                  ".
					"	AND codperi='".$this->ls_peractnom."'                 ".
					" UNION                                                   ".
					"SELECT cuenta,operacion, total, codprov, cedben, codconc ".
					"  FROM aportes_scg_ajuste_int               ".
					" WHERE codemp='".$this->ls_codemp."'                     ".
					"   AND codnom='".$this->ls_codnom."'                     ".
					"   AND anocur='".$this->ls_anocurnom."'                  ".
					"   AND codperi='".$this->ls_peractnom."'                 ";
		}
		else
		{
			$ls_sql="SELECT cuenta,operacion, total, codprov, cedben, codconc ".
					"  FROM contabilizar_aportes_scg_ajuste                   ".
					" WHERE codemp='".$this->ls_codemp."'                     ".
					"   AND codnom='".$this->ls_codnom."'                     ".
					"	AND anocur='".$this->ls_anocurnom."'                  ".
					"	AND codperi='".$this->ls_peractnom."'                 ".
					" UNION                                                   ".
					"SELECT cuenta,operacion, total, codprov, cedben, codconc ".
					"  FROM contabilizar_aportes_scg_ajuste_int               ".
					" WHERE codemp='".$this->ls_codemp."'                     ".
					"   AND codnom='".$this->ls_codnom."'                     ".
					"   AND anocur='".$this->ls_anocurnom."'                  ".
					"   AND codperi='".$this->ls_peractnom."'                 ";
		}
		if($ai_estagrapo=='1') // gererar la parte contable de las retenciones
		{
			$ls_sql=$ls_sql." UNION ".
			        "SELECT scg_cuentas.sc_cuenta AS cuenta, CAST('D' AS CHAR(1)) AS operacion,SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, sno_hconcepto.codprov AS codprov, ". 
				    "	    sno_hconcepto.cedben AS cedben, sno_hconcepto.codconc  ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas  ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')  ".
					"   AND sno_hconcepto.sigcon='P'  ".
					"   AND sno_hsalida.valsal <> 0  ". 
					"   AND scg_cuentas.status = 'C'  ". 
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp   ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom   ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp   ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon  ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
                        // esto es muy particular de la alcaldia del hatillo
			/*$ls_sql=$ls_sql." UNION ".
			        "SELECT scg_cuentas.sc_cuenta AS cuenta, CAST('H' AS CHAR(1)) AS operacion,SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, sno_hconcepto.codprov AS codprov, ". 
				    "	    sno_hconcepto.cedben AS cedben, sno_hconcepto.codconc  ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas  ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')  ".
					"   AND sno_hconcepto.sigcon='P'  ".
					"   AND sno_hsalida.valsal <> 0  ". 
					"   AND scg_cuentas.status = 'C'  ". 
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp   ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom   ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp   ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon  ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";*/
		}
		if(($as_operacionaporte=="OC")&&($ai_genrecapo=="1"))
		{
			if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
			{
				// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
						"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, sno_hnomina, cxp_clasificador_rd ".
						" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
						"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
						"   AND scg_cuentas.status = 'C' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"   AND sno_hsalida.codemp = sno_hnomina.codemp ".
						"   AND sno_hsalida.codnom = sno_hnomina.codnom ".
						"   AND sno_hsalida.anocur = sno_hnomina.anocurnom ".
						"   AND sno_hsalida.codperi = sno_hnomina.peractnom ".
						"	AND sno_hnomina.codemp = cxp_clasificador_rd.codemp ".
						"	AND sno_hnomina.codclaapo = cxp_clasificador_rd.codcla ".
						"   AND scg_cuentas.codemp = cxp_clasificador_rd.codemp ".
						"   AND scg_cuentas.sc_cuenta = cxp_clasificador_rd.sc_cuenta ".
						" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
			}
			else
			{
				// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
						"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor ".
						" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
						"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
						"   AND scg_cuentas.status = 'C' ".
						"   AND sno_hconcepto.codprov <> '----------' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"	AND sno_hconcepto.codemp = rpc_proveedor.codemp ".
						"	AND sno_hconcepto.codprov = rpc_proveedor.cod_pro ".
						"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
						"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
						" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
				// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
						"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario ".
						" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
						"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
						"   AND scg_cuentas.status = 'C' ".
						"   AND sno_hconcepto.cedben <> '----------' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"	AND sno_hconcepto.codemp = rpc_beneficiario.codemp ".
						"	AND sno_hconcepto.cedben = rpc_beneficiario.ced_bene ".
						"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
						"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
						" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
			}
			if($ai_estagrapo=='1') // gererar la parte contable de las retenciones
			{
				$ls_sql=$ls_sql." UNION ".
					   "SELECT scg_cuentas.sc_cuenta AS cuenta, CAST('H' AS CHAR(1)) AS operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, ".
					   "	   sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					   "  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario  ".
					   " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					   "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					   "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					   "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					   "   AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')  ".
					   "   AND sno_hconcepto.sigcon='P' ".
					   "   AND sno_hsalida.valsal <> 0  ".
					   "   AND scg_cuentas.status = 'C'  ".
					   "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					   "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					   "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					   "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					   "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					   "   AND rpc_beneficiario.codemp = sno_hconcepto.codemp ". 
					   "   AND rpc_beneficiario.ced_bene = sno_hconcepto.cedben   ".
					   "   AND scg_cuentas.codemp = rpc_beneficiario.codemp  ".
					   "   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
					   "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					   "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					   "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					   "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					   "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					   " GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
				       " UNION ".
					   "SELECT scg_cuentas.sc_cuenta AS cuenta, CAST('H' AS CHAR(1)) AS operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, ".
					   "	   sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					   "  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor  ".
					   " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					   "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					   "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					   "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					   "   AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')  ".
					   "   AND sno_hconcepto.sigcon='P' ".
					   "   AND sno_hsalida.valsal <> 0  ".
					   "   AND scg_cuentas.status = 'C'  ".
					   "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					   "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					   "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					   "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					   "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					   "   AND rpc_proveedor.codemp = sno_hconcepto.codemp ". 
					   "   AND rpc_proveedor.cod_pro = sno_hconcepto.codprov   ".
					   "   AND scg_cuentas.codemp = rpc_proveedor.codemp  ".
					   "   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
					   "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					   "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					   "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					   "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					   "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					   " GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
			}
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
					"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben ";
			if($ai_estagrapo=='1') // gererar la parte contable de las retenciones
			{
				$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
					"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')  ".
					"   AND sno_hconcepto.sigcon='P' ".
					"   AND sno_hsalida.valsal <> 0  ".
					"   AND scg_cuentas.status = 'C'  ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben "; 
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_contabilizar_aportes_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cuenta=$rs_data->fields["cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$li_total=abs(round($rs_data->fields["total"],2));
				$ls_codpro=$rs_data->fields["codprov"];
				$ls_cedben=$rs_data->fields["cedben"];
				$ls_codconc=$rs_data->fields["codconc"];
				$ls_tipodestino="";
				if ($ai_tipdocapo=="")
				{
					$ai_tipdocapo="--";
				}
				if($ls_codpro=="----------")
				{
					$ls_tipodestino="B";
				}
				if($ls_cedben=="----------")
				{
					$ls_tipodestino="P";
				}
				$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
				
				$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
				if (!$lb_existe)
				{
					$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
																$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
																$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																	$ls_codconc,$li_total);						
				}
				
				$rs_data->MoveNext();
			}
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_aportes_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_ingresos_spi($as_codcom,$as_descripcion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_ingresos_spi 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_descripcion  //  descripción del comprobante
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 25/03/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$ls_tipnom="I"; // tipo de contabilización
		if ($ls_estpreing==0)
		{
			$ls_sql="SELECT spi_cuentas.spi_cuenta AS cuenta, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hconcepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					" GROUP BY spi_cuentas.spi_cuenta ";			
		}
		else
		{
			$ls_sql=" SELECT spi_cuentas.spi_cuenta AS cuenta, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total, ".
					"        spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2, ".
					"        spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					"        spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla ".
					"   FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas,".
					"        spi_cuentas_estructuras, sno_hunidadadmin   ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' ".
					"        OR sno_hsalida.tipsal = 'V2' ".
					"        OR sno_hsalida.tipsal = 'W2' ".
					"        OR sno_hsalida.tipsal = 'P1' ".
					"        OR sno_hsalida.tipsal = 'V3' ".
					"        OR sno_hsalida.tipsal = 'W3')". 
					"  AND sno_hconcepto.intingcon = '1' ".
					"  AND spi_cuentas.status = 'C' ".
					"  AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"  AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"  AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"  AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"  AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"  AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"  AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"  AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"  AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"  AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"  AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"  AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					"  AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"  AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"  AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"  AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"  AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"  AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"  AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"  AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"  AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"  AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"  AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"  AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta   ".   
					"  AND sno_hconcepto.codemp = spi_cuentas_estructuras.codemp ".
					"  AND sno_hconcepto.codestpro1 = spi_cuentas_estructuras.codestpro1 ".
					"  AND sno_hconcepto.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
					"  AND sno_hconcepto.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
					"  AND sno_hconcepto.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
					"  AND sno_hconcepto.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"  AND sno_hconcepto.estcla = spi_cuentas_estructuras.estcla ".
					" GROUP BY spi_cuentas.spi_cuenta, spi_cuentas_estructuras.codestpro1, ".
					"          spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,".
					" 	       spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,".
					"          spi_cuentas_estructuras.estcla";				
			$ls_sql=$ls_sql."	UNION ".					   
				    " SELECT   spi_cuentas.spi_cuenta AS cuenta, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total,".
					"		   spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,".
					"		   spi_cuentas_estructuras.codestpro3,".
					"		   spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,".
					"		   spi_cuentas_estructuras.estcla ".
					"   FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, ".
					"        spi_cuentas_estructuras, sno_hunidadadmin   ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' ".
					"        OR sno_hsalida.tipsal = 'V2' ".
					"        OR sno_hsalida.tipsal = 'W2' ".
					"        OR sno_hsalida.tipsal = 'P1' ".
					"        OR sno_hsalida.tipsal = 'V3' ".
					"        OR sno_hsalida.tipsal = 'W3') ".
					"   AND sno_hconcepto.intingcon = '1' ".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ". 
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
					"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta      ".
					"   AND sno_hunidadadmin.codestpro1 = spi_cuentas_estructuras.codestpro1 ".
					"   AND sno_hunidadadmin.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
					"   AND sno_hunidadadmin.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
					"   AND sno_hunidadadmin.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
					"   AND sno_hunidadadmin.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"   AND sno_hunidadadmin.estcla = spi_cuentas_estructuras.estcla   ".
					" GROUP BY spi_cuentas.spi_cuenta, spi_cuentas_estructuras.codestpro1,".
					"           spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,".
					"	       spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,".
					"	       spi_cuentas_estructuras.estcla";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_contabilizar_ingresos_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cuenta=$rs_data->fields["cuenta"];
				$li_total=round(abs($rs_data->fields["total"]),2);
				$ls_codconc="0000000001";
				$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
				$as_operacionnomina="DC";
				$as_codpro="----------";
				$as_codben="----------";
				$as_tipodestino="-";
				$ai_genrecdoc="0";
				$ai_tipdocnom="";
				$ai_gennotdeb="0";
				$ai_genvou="0";
				if ($ls_estpreing==1)
				{
					$ls_codestpro1=$rs_data->fields["codestpro1"];
					$ls_codestpro2=$rs_data->fields["codestpro2"];
					$ls_codestpro3=$rs_data->fields["codestpro3"];
					$ls_codestpro4=$rs_data->fields["codestpro4"];
					$ls_codestpro5=$rs_data->fields["codestpro5"];
					$ls_estcla=$rs_data->fields["estcla"];
				}
				else
				{
					$ls_codestpro1='-------------------------';
					$ls_codestpro2='-------------------------';
					$ls_codestpro3='-------------------------';
					$ls_codestpro4='-------------------------';
					$ls_codestpro5='-------------------------';
					$ls_estcla='-';					
				}
			
				$lb_existe=$this->uf_select_contabilizacion_spi($as_codcom,$ls_tipnom,$ls_cuenta,$as_operacionnomina,$ls_codconc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																$ls_codestpro5,$ls_estcla);
				if (!$lb_existe)
				{
					$lb_valido=$this->uf_insert_contabilizacion_spi($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$ls_cuenta,$li_total,$ls_tipnom,$ls_codconc,$ai_genrecdoc,
																$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
																$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																$ls_codestpro5,$ls_estcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_spi($as_codcom,$ls_tipnom,$ls_cuenta,$as_operacionnomina,$ls_codconc,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$li_total);						
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_ingresos_spi
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_ingresos_scg($as_codcom,$as_descripcion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_ingresos_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_descripcion  //  descripción del comprobante
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 25/03/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$ls_tipnom="I"; // tipo de contabilización
		$as_codcla="--";
		if ($ls_estpreing==0)
		{
			// Buscamos todas aquellas cuentas contables que estan ligadas a las de ingreso de los conceptos que se 
			// integran directamente con presupuesto estas van por el haber de contabilidad
			$ls_sql="SELECT spi_cuentas.sc_cuenta as cuenta, 'H' as operacion, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hconcepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
					"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
					"   GROUP BY spi_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que NO se 
			// integran directamente con presupuesto entonces las buscamos según la estructura de la unidad administrativa a 
			// la que pertenece el personal, estas van por el debe de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, 'D' as operacion, sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
					"   AND sno_hconcepto.intingcon = '1'".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon  ".
					"   GROUP BY scg_cuentas.sc_cuenta "; 
		}
		else
		{
				$ls_sql=" SELECT spi_cuentas.sc_cuenta as cuenta, 'H' as operacion, ".
						"		 sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total,".
						"        spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
						"        spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
						"        spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla  ".
					    "   FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, scg_cuentas,".
						"        spi_cuentas_estructuras, sno_hunidadadmin ".
					    "  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					    "    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					    "    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					    "    AND sno_hsalida.codperi='".$this->ls_peractnom."' ". 
					    "    AND sno_hsalida.valsal <> 0 ".
					    "    AND (sno_hsalida.tipsal = 'D' ".
						"         OR sno_hsalida.tipsal = 'V2' ". 
						"         OR sno_hsalida.tipsal = 'W2' ".
						"         OR sno_hsalida.tipsal = 'P1' ".
						"         OR sno_hsalida.tipsal = 'V3' ".
						"         OR sno_hsalida.tipsal = 'W3') ".
						"   AND sno_hconcepto.intingcon = '1' ".
						"   AND spi_cuentas.status = 'C' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
						"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
						"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ".
						"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
						"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
						"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
						"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
						"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
						"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
						"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
						"   AND sno_hconcepto.codemp  = spi_cuentas_estructuras.codemp ".
						"   AND sno_hconcepto.codestpro1  = spi_cuentas_estructuras.codestpro1 ".
						"   AND sno_hconcepto.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
						"   AND sno_hconcepto.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
						"   AND sno_hconcepto.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
						"   AND sno_hconcepto.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
						"   AND sno_hconcepto.estcla = spi_cuentas_estructuras.estcla      ".
						" GROUP BY spi_cuentas.sc_cuenta,  spi_cuentas_estructuras.codestpro1, ".
						"      spi_cuentas_estructuras.codestpro2,".
						"	   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4, ".
						"	   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ";
				$ls_sql=$ls_sql."   UNION  ".
					    "  SELECT spi_cuentas.sc_cuenta as cuenta, 'H' as operacion, ".
						"         sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total,".
						"         spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
						"         spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
						"         spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla  ".
					    "    FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spi_cuentas, scg_cuentas, ".
						"         spi_cuentas_estructuras, sno_hunidadadmin ".
					    "  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					    "    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					    "    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					    "    AND sno_hsalida.codperi='".$this->ls_peractnom."' ". 
					    "    AND sno_hsalida.valsal <> 0 ".
					    "    AND (sno_hsalida.tipsal = 'D' ".
						"         OR sno_hsalida.tipsal = 'V2' ".
						"         OR sno_hsalida.tipsal = 'W2' ".
						"         OR sno_hsalida.tipsal = 'P1' ".
						"         OR sno_hsalida.tipsal = 'V3' ".
						"         OR sno_hsalida.tipsal = 'W3') ". 
						"   AND sno_hconcepto.intingcon = '1' ".
						"   AND spi_cuentas.status = 'C' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ". 
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"   AND spi_cuentas.codemp = sno_hconcepto.codemp ".
						"   AND spi_cuentas.spi_cuenta = sno_hconcepto.spi_cuenta ".
						"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta ".
						"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
						"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
						"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
						"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
						"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
						"   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
						"   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
						"   AND sno_hunidadadmin.codestpro1 =  spi_cuentas_estructuras.codestpro1 ".
						"   AND sno_hunidadadmin.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
						"   AND sno_hunidadadmin.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
						"   AND sno_hunidadadmin.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
						"   AND sno_hunidadadmin.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
						"   AND sno_hunidadadmin.estcla = spi_cuentas_estructuras.estcla      ".
						" GROUP BY spi_cuentas.sc_cuenta,  spi_cuentas_estructuras.codestpro1,".
						"       spi_cuentas_estructuras.codestpro2,".
						"	   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
						"	   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla  ";
			$ls_sql=$ls_sql."   UNION  ".
					    "  SELECT scg_cuentas.sc_cuenta as cuenta, 'D' as operacion, ".
					    "         sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total,".
					    " 	     spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
					    "	     spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
					    "		 spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ".
					    "   FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ,spi_cuentas,".
					    "        spi_cuentas_estructuras, sno_hunidadadmin ".
					    "  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					    "    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					    "    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					    "    AND sno_hsalida.codperi='".$this->ls_peractnom."' ". 
					    "    AND sno_hsalida.valsal <> 0 ".
					    "    AND (sno_hsalida.tipsal = 'D' ".
					    "         OR sno_hsalida.tipsal = 'V2' ".
					    "         OR sno_hsalida.tipsal = 'W2' ".
					    "         OR sno_hsalida.tipsal = 'P1' ".
					    "         OR sno_hsalida.tipsal = 'V3' ".
					    "         OR sno_hsalida.tipsal = 'W3')". 
					    "   AND sno_hconcepto.intingcon = '1' ".
					    "	  AND scg_cuentas.status = 'C' ".
					    "	  AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"	  AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"	  AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"	  AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"	  AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"	  AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"	  AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"	  AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"	  AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"	  AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"	  AND scg_cuentas.codemp = sno_hconcepto.codemp ".
						"	  AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
						"	  AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"	   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
						"	   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
						"	   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
						"	   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"	   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
						"	   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
						"	   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
						"	   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
						"	   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
						"	   AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
						"	   AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
						"	   AND sno_hconcepto.codemp  = spi_cuentas_estructuras.codemp ".
						"	   AND sno_hconcepto.codestpro1  = spi_cuentas_estructuras.codestpro1 ".
						"	   AND sno_hconcepto.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
						"	   AND sno_hconcepto.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
						"	   AND sno_hconcepto.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
						"	   AND sno_hconcepto.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
						"	   AND sno_hconcepto.estcla = spi_cuentas_estructuras.estcla ".     
						"	 GROUP BY scg_cuentas.sc_cuenta, spi_cuentas_estructuras.codestpro1, ".
						"          spi_cuentas_estructuras.codestpro2,".
						"		   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
						"		   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla";
				$ls_sql=$ls_sql."   UNION  ".
					    " SELECT scg_cuentas.sc_cuenta as cuenta, 'D' as operacion, ".
						"	     sum((sno_hsalida.valsal*sno_hconcepto.poringcon)/100) as total,".
						"	     spi_cuentas_estructuras.codestpro1, spi_cuentas_estructuras.codestpro2,".
						"	     spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
						"	     spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ,".
						"       spi_cuentas,  spi_cuentas_estructuras, sno_hunidadadmin ".
						"  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
						"    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
						"    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"    AND sno_hsalida.codperi='".$this->ls_peractnom."' ". 
						"    AND sno_hsalida.valsal <> 0 ".
						"    AND (sno_hsalida.tipsal = 'D' ".
						"         OR sno_hsalida.tipsal = 'V2' ".
						"         OR sno_hsalida.tipsal = 'W2' ".
						"         OR sno_hsalida.tipsal = 'P1' ".
						"         OR sno_hsalida.tipsal = 'V3' ".
						"         OR sno_hsalida.tipsal = 'W3') ".
						"  AND sno_hconcepto.intingcon = '1' ".
						"  AND scg_cuentas.status = 'C' ".
						"  AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"  AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"  AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"  AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"  AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"  AND sno_hsalida.codemp = sno_hconcepto.codemp ".
						"  AND sno_hsalida.codnom = sno_hconcepto.codnom ".
						"  AND sno_hsalida.anocur = sno_hconcepto.anocur ".
						"  AND sno_hsalida.codperi = sno_hconcepto.codperi ".
						"  AND sno_hsalida.codconc = sno_hconcepto.codconc ".
						"  AND scg_cuentas.codemp = sno_hconcepto.codemp ".
						"  AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
						"  AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"  AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
						"  AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
						"  AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
						"  AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
						"  AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
						"  AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
						"  AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
						"  AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
						"  AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm   ". 
						"  AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
						"  AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
						"  AND sno_hunidadadmin.codestpro1 =  spi_cuentas_estructuras.codestpro1 ".
						"  AND sno_hunidadadmin.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
						"  AND sno_hunidadadmin.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
						"  AND sno_hunidadadmin.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
						"  AND sno_hunidadadmin.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
						"  AND sno_hunidadadmin.estcla = spi_cuentas_estructuras.estcla    ".
						" GROUP BY scg_cuentas.sc_cuenta, spi_cuentas_estructuras.codestpro1, ".
						"      spi_cuentas_estructuras.codestpro2,".
						"	   spi_cuentas_estructuras.codestpro3, spi_cuentas_estructuras.codestpro4,".
						"	   spi_cuentas_estructuras.codestpro5, spi_cuentas_estructuras.estcla ";
		
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_contabilizar_ingresos_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cuenta=$rs_data->fields["cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$li_total=abs(round($rs_data->fields["total"],2));
				$ls_codconc="0000000001";
				$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
				$ls_codpro="----------";
				$ls_cedben="----------";
				$ls_tipodestino="-";
				$ai_genrecdoc="0";
				$ai_tipdocnom="";
				$ai_gennotdeb="0";
				$ai_genvou="0";
				
				
				$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
				if (!$lb_existe)
				{
					$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
																$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
																$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																	$ls_codconc,$li_total);						
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_ingresos_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_contabilizacion($as_codcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_contabilizacion 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar que lo mismo que esta por el debe tambien este por el haber en contabilidad
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 29/06/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_sql="SELECT debhab, SUM(ROUND( CAST(monto as numeric), 2)) as total ".
				"  FROM sno_dt_scg ".
				" WHERE codcom = '".$as_codcom."' ".
				" GROUP BY debhab ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_verificar_contabilizacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$li_debe=0;
			$li_haber=0;
			while(!$rs_data->EOF)
			{
				$li_operacion=$rs_data->fields["debhab"];
				if($li_operacion=="D")
				{
					$li_debe=number_format($rs_data->fields["total"],2,".","");
				}
				else
				{
					$li_haber=number_format($rs_data->fields["total"],2,".","");
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
			if($li_debe!=$li_haber)
			{
				$lb_valido=false;
				if(substr($as_codcom,14,1)=="A")
				{
					$ls_texto=" Aportes";
				}
				else
				{
					$ls_texto=" Nómina";
				}
				$this->io_mensajes->message("Los Monto en la Contabilización de ".$ls_texto." no cuadran. Debe=".$this->io_fun_nomina->uf_formatonumerico($li_debe)." Haber ".$this->io_fun_nomina->uf_formatonumerico($li_haber).". Verifique la información ");
			}
		}		
		return  $lb_valido;    
	}// end function uf_verificar_contabilizacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_ajustecontabilizacion_proceso($aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_ajustecontabilizacion_proceso 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización del período
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql->begin_transaction();
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$this->ls_codnom;
		$ls_codperi=$this->ls_peractnom;
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
		$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
		$ls_codcom=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-N"; // Comprobante de Conceptos
		$ls_codcomapo=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-A"; // Comprobante de Aportes
		$ls_descripcion=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Conceptos
		$ls_descripcionapo=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Aportes
		
		$ls_descripcion_int=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper." Asiento de Intercompañias"; // Descripción de Conceptos
		
		$ls_descripcionapo_int=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper." Asiento de Intercompañias"; // Descripción de Aportes
		
		$ls_cuentapasivo="";
		$ls_operacionnomina="";
		$ls_operacionaporte="";
		$ls_tipodestino="";
		$ls_codpro="";
		$ls_codben="";
		$li_gennotdeb="";
		$li_genvou="";
		$li_genrecdoc="";
		$li_genrecapo="";
		$li_tipdocnom="";
		$li_tipdocapo="";
		$li_estagrapo="";
		$li_recdoccaunom="";
		$ls_tipdoccaunom="";
		// Obtenemos la configuración de la contabilización de la nómina
		$arrResultado=$this->uf_load_configuracion_contabilizacion($ls_cuentapasivo,$ls_operacionnomina,$ls_operacionaporte,
																$ls_tipodestino,$ls_codpro,$ls_codben,$li_gennotdeb,$li_genvou,
																$li_genrecdoc,$li_genrecapo,$li_tipdocnom,$li_tipdocapo,$li_estagrapo,
																$li_recdoccaunom,$ls_tipdoccaunom);
		$ls_cuentapasivo=$arrResultado['as_cuentapasivo'];
		$ls_operacionnomina=$arrResultado['as_modo'];
		$ls_operacionaporte=$arrResultado['as_modoaporte'];
		$ls_tipodestino=$arrResultado['as_destino'];
		$ls_codpro=$arrResultado['as_codpro'];
		$ls_codben=$arrResultado['as_codben'];
		$li_gennotdeb=$arrResultado['ai_gennotdeb'];
		$li_genvou=$arrResultado['ai_genvou'];
		$li_genrecdoc=$arrResultado['ai_genrecdoc'];
		$li_genrecapo=$arrResultado['ai_genrecapo'];
		$li_tipdocnom=$arrResultado['ai_tipdocnom'];
		$li_tipdocapo=$arrResultado['ai_tipdocapo'];
		$li_estagrapo=$arrResultado['ai_estagrapo'];
		$li_recdoccaunom=$arrResultado['ai_recdoccaunom'];
		$ls_tipdoccaunom=$arrResultado['as_tipdoccaunom'];		
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{	// eliminamos la contabilización anterior 
			$lb_valido=$this->uf_delete_contabilizacion($ls_codperi);
		}														
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de conceptos
			$lb_valido=$this->uf_load_conceptos_spg_normales();
			if($lb_valido)
			{
				$lb_valido=$this->uf_load_conceptos_spg_proyecto();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_conceptos_spg_proyectos($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																    $ls_tipodestino,$ls_descripcion,$li_genrecdoc,$li_tipdocnom,
																    $li_gennotdeb,$li_genvou);
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de contabilidad de conceptos
			if(($ls_operacionnomina!="O")||(($ls_operacionnomina=="O")&&($li_recdoccaunom=="1")))// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_load_conceptos_scg_normales($ls_operacionnomina,$ls_cuentapasivo,$li_genrecdoc);
				if($lb_valido)
				{
					$lb_valido=$this->uf_load_conceptos_scg_proyecto();
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_conceptos_scg_proyectos($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																		$ls_tipodestino,$ls_descripcion,$ls_cuentapasivo,
																		$li_genrecdoc,$li_tipdocnom,$li_gennotdeb,$li_genvou);
				}
				$ls_estempcon=trim($_SESSION["la_empresa"]["estempcon"]);
				$ls_basdatcon=trim($_SESSION["la_empresa"]["basdatcon"]);
				if(($lb_valido)&&($ls_estempcon==0)&&($ls_basdatcon!=""))
				{
					//----asiento contable de cuentas de intercompañias--------------------------------------------------------
					$lb_valido=$this->uf_load_conceptos_scg_normales_int($ls_operacionnomina,$ls_cuentapasivo,$li_genrecdoc);
					if($lb_valido)
					{
						$lb_valido=$this->uf_load_conceptos_scg_proyecto_int();
					}
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_conceptos_scg_proyectos_int($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																			$ls_tipodestino,$ls_descripcion_int,$ls_cuentapasivo,
																			$li_genrecdoc,$li_tipdocnom,$li_gennotdeb,$li_genvou);
					}
					//------------------------------------------------------------------------------------------------------------
				}
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de los aportes
			$lb_valido=$this->uf_load_aportes_spg_normales();
			if($lb_valido)
			{
				$lb_valido=$this->uf_load_aportes_spg_proyecto();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_aportes_spg_proyectos($ls_codcomapo,$ls_operacionaporte,$ls_codpro,$ls_codben,
															  	  $ls_tipodestino,$ls_descripcionapo,$ls_cuentapasivo,
															  	  $li_genrecapo,$li_tipdocapo,$li_gennotdeb,$li_genvou);
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de contabilidad de los aportes
			if($ls_operacionaporte!="O")// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_load_aportes_scg_normales($ls_operacionaporte);
				if($lb_valido)
				{
					$lb_valido=$this->uf_load_aportes_scg_proyecto();
				}
				if($lb_valido)
				{
					$lb_valido=$this->uf_insert_aportes_scg_proyectos($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
																	  $ls_descripcionapo,$li_genrecapo,$li_tipdocapo,
																	  $li_gennotdeb, $li_genvou,$ls_operacionaporte);
				}
				$ls_estempcon=trim($_SESSION["la_empresa"]["estempcon"]);
				$ls_basdatcon=trim($_SESSION["la_empresa"]["basdatcon"]);
				if(($lb_valido)&&($ls_estempcon==0)&&($ls_basdatcon!=""))
				{
					//--------cuentas contables de intercompañias------------------------------------------------------------------
					$lb_valido=$this->uf_load_aportes_scg_normales_int($ls_operacionaporte);
					if($lb_valido)
					{
						$lb_valido=$this->uf_load_aportes_scg_proyecto_int();
					}
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_aportes_scg_proyectos_int($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
																		  $ls_descripcionapo_int,$li_genrecapo,$li_tipdocapo,
																		  $li_gennotdeb,$li_genvou,$ls_operacionaporte);
					}
					//--------------------------------------------------------------------------------------------------------------
				}
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcom); // Nómina
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcomapo); // Aportes
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Ajustó la Contabilización del Año ".$this->ls_anocurnom." período ".$this->ls_peractnom." asociado a la nómina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
		}
		if($lb_valido)
		{
			$this->io_sql->commit(); 
			$this->io_mensajes->message("El Ajuste de la contabilización fue procesado.");
		}
		else
		{
			$this->io_sql->rollback();
			$this->io_mensajes->message("Ocurrio un error al ajustar la contabilización.");
		}
		return  $lb_valido;    
	}// end function uf_procesar_ajustecontabilizacion_proceso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos_spg_normales()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_spg_normales 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos Normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, sno_hconcepto.estcla  ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		   sno_hconcepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"		spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total,sno_hunidadadmin.estcla ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		   sno_hunidadadmin.codestpro5, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total,sno_hconcepto.estcla ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		   sno_hconcepto.estcla, spg_cuentas.spg_cuenta  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"       spg_cuentas.spg_cuenta as cueprecon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, sno_hunidadadmin.estcla  ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"          sno_hunidadadmin.codestpro5, sno_hunidadadmin.estcla,  spg_cuentas.spg_cuenta ";
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla,  cueprecon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_conceptos_spg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_spg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos_spg_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_spg_proyecto 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos por proyecto
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, ".
				"		sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, spg_cuentas.spg_cuenta, sno_hproyecto.estcla,".
				"		".$ls_cadena." as montoparcial, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, MAX(sno_hproyectopersonal.pordiames) AS pordiames  ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"	AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, ".
				"		   sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, sno_hproyecto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, ".
				"		sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, spg_cuentas.spg_cuenta,sno_hproyecto.estcla, ".
				"		".$ls_cadena." as montoparcial, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, MAX(sno_hproyectopersonal.pordiames) AS pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hconcepto.sigcon = 'E'".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"	AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, ".
				"		sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, sno_hproyecto.estcla, spg_cuentas.spg_cuenta ".
				" ORDER BY codper, codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_conceptos_spg_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_codestpro1ant="";
			$ls_codestpro2ant="";
			$ls_codestpro3ant="";
			$ls_codestpro4ant="";
			$ls_codestpro5ant="";
			$ls_estclaant="";
			$ls_cuentaant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_estcla=$rs_data->fields["estcla"];//del proyecto
				$li_pordiames=$rs_data->fields["pordiames"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,2)!=round($li_totalant,2))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
							$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
							$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
							$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
							$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
							$this->DS->insertRow("estcla",$ls_estclaant);
							$this->DS->insertRow("cueprecon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],2);
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaant=$ls_estcla;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
				}
				$this->DS->insertRow("codestpro1",$ls_codestpro1);
				$this->DS->insertRow("codestpro2",$ls_codestpro2);
				$this->DS->insertRow("codestpro3",$ls_codestpro3);
				$this->DS->insertRow("codestpro4",$ls_codestpro4);
				$this->DS->insertRow("codestpro5",$ls_codestpro5);
				$this->DS->insertRow("estcla",$ls_estcla);
				$this->DS->insertRow("cueprecon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,2,".","")!=number_format($li_totalant,2,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
				$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
				$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
				$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
				$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
				$this->DS->insertRow("estcla",$ls_estclaant);
				$this->DS->insertRow("cueprecon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_spg_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_conceptos_spg_proyectos($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										   	   $ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_conceptos_spg_proyectos 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se lee el datastored y lo manda a insertar
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_tipnom="N"; // tipo de contabilización
		$li_totrow=$this->DS->getRowCount("cueprecon");
		$as_codcla="--";
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_codestpro1=$this->DS->data["codestpro1"][$li_i];
			$ls_codestpro2=$this->DS->data["codestpro2"][$li_i];
			$ls_codestpro3=$this->DS->data["codestpro3"][$li_i];
			$ls_codestpro4=$this->DS->data["codestpro4"][$li_i];
			$ls_codestpro5=$this->DS->data["codestpro5"][$li_i];
			$ls_estcla=$this->DS->data["estcla"][$li_i];
			$ls_cueprecon=$this->DS->data["cueprecon"][$li_i];
			$li_total=round($this->DS->data["total"][$li_i],2);
			$ls_codconc="0000000001";
			$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
			if($li_total>0)
			{
				$lb_existe=$this->uf_select_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																$ls_codestpro5,$ls_estcla,$ls_cueprecon,$as_operacionnomina,$ls_codconc);
				if (!$lb_existe)
				{
				
					$lb_valido=$this->uf_insert_contabilizacion_spg($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,
																$as_tipodestino,$as_descripcion,$ls_codestpro1,$ls_codestpro2,
																$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,
																$ls_cueprecon,$li_total,$ls_tipnom,$ls_codconc,$ai_genrecdoc,
																$ai_tipdocnom,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_cueprecon,$as_operacionnomina,
																$ls_codconc,$li_total);						
				}		
			}
		}
		$this->DS->reset_ds();
		return $lb_valido;    
	}// end function uf_insert_conceptos_spg_proyectos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos_scg_normales($as_operacionnomina,$as_cuentapasivo,$ai_genrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_scg_normales 
		//	    Arguments: as_operacionnomina  //  Operación de la contabilización
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		
		 if ($_SESSION["ls_gestor"] == 'oci8po')
		 {
		 	$ls_sql="  SELECT cuenta, operacion,  total                     ".
					   "    FROM con_scg_nor_ajuste_proy   ".
					   "   WHERE codemp='".$this->ls_codemp."'                 ".
					   "	AND codnom='".$this->ls_codnom."'                  ".
					   "	AND anocur='".$this->ls_anocurnom."'               ".
					   "	AND codperi='".$this->ls_peractnom."'			   ".
					   "   UNION							                   ".
					   "  SELECT cuenta, operacion,  total                     ".
					   "	FROM con_scg_norm_ajuste_proy_int ".
					   "    WHERE codemp='".$this->ls_codemp."'                  ".
					   "	 AND codnom='".$this->ls_codnom."'                   ".
					   "	 AND anocur='".$this->ls_anocurnom."'                ".
					   "	 AND codperi='".$this->ls_peractnom."'               ";		
		 }
		 else
		 {
		       $ls_sql="  SELECT cuenta, operacion,  total                     ".
					   "    FROM load_conceptos_scg_normales_ajuste_proyecto   ".
					   "   WHERE codemp='".$this->ls_codemp."'                 ".
					   "	AND codnom='".$this->ls_codnom."'                  ".
					   "	AND anocur='".$this->ls_anocurnom."'               ".
					   "	AND codperi='".$this->ls_peractnom."'			   ".
					   "   UNION							                   ".
					   "  SELECT cuenta, operacion,  total                     ".
					   "	FROM load_conceptos_scg_normales_ajuste_proyecto_int ".
					   "    WHERE codemp='".$this->ls_codemp."'                  ".
					   "	 AND codnom='".$this->ls_codnom."'                   ".
					   "	 AND anocur='".$this->ls_anocurnom."'                ".
					   "	 AND codperi='".$this->ls_peractnom."'               ";		
		  }
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND spg_ep1.estint = 0       ".
				"	AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 ".
				"	AND spg_ep1.estcla = sno_hconcepto.estcla           ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY scg_cuentas.sc_cuenta ";
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D que NO se 
		// integran directamente con presupuesto entonces las buscamos según la estructura de la unidad administrativa a 
		// la que pertenece el personal, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.intprocon = '0' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND spg_ep1.estint = 0      ".
				"	AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 ".
				"   AND sno_hunidadadmin.estcla = spg_ep1.estcla           ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY scg_cuentas.sc_cuenta ";
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.sigcon = 'B' ".
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				" GROUP BY scg_cuentas.sc_cuenta ";
		// Para las nóminas de prestaciones sociales
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, sno_fideicomiso ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.sigcon = 'X' ". // concepto de tipo prestación antiguedad
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_fideicomiso.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_fideicomiso.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_fideicomiso.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_fideicomiso.scg_cuentafid ".
				" GROUP BY scg_cuentas.sc_cuenta ";
		// Para las nóminas de prestaciones sociales
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, sno_fideicomiso ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.sigcon = 'I' ". // concepto de tipo intereses prestación antiguedad
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_fideicomiso.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_fideicomiso.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_fideicomiso.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_fideicomiso.scg_cuentaintfid ".
				" GROUP BY scg_cuentas.sc_cuenta ";
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND scg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconcon ".
				" GROUP BY scg_cuentas.sc_cuenta ";
		if($as_operacionnomina=="OC") // Si el modo de contabilizar la nómina es Compromete y Causa tomamos la cuenta pasivo de la nómina.
		{
			if($ai_genrecdoc=="0") // No se genera Recepción de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$as_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "MYSQLI":
						$ls_cadena="CONVERT('".$as_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$as_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.",  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$as_cuentapasivo."' ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hsalida.codemp = sno_banco.codemp ".
						"   AND sno_hsalida.codnom = sno_banco.codnom ".
						"   AND sno_hsalida.codperi = sno_banco.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
						"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepción de documentos
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_proveedor ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hnomina.descomnom = 'P'".
						"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hnomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_hnomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
						"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas, sno_hnomina, rpc_beneficiario ".
						" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
						"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
						"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3' )".
						"   AND sno_hsalida.valsal <> 0 ".
						"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
						"   AND sno_hpersonalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_hnomina.descomnom = 'B'".
						"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hnomina.anocurnom = sno_hsalida.anocur ".
						"   AND sno_hnomina.peractnom = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
						"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
						"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
						"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
						"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
						"   AND sno_hnomina.codemp = rpc_beneficiario.codemp ".
						"   AND sno_hnomina.codbennom = rpc_beneficiario.ced_bene ".
						"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_banco, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".

					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1) ".
					"   AND sno_hpersonalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_banco.codemp ".
					"   AND sno_hsalida.codnom = sno_banco.codnom ".
					"   AND sno_hsalida.codperi = sno_banco.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_banco.codemp ".
					"   AND sno_hpersonalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta,  CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
					"  FROM sno_hpersonalnomina, sno_hsalida, scg_cuentas ".
					" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' OR sno_hsalida.tipsal = 'D' ".
					"    OR  sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3')".
					"   AND sno_hsalida.valsal <> 0".
					"   AND sno_hpersonalnomina.pagbanper = 0 ".
					"   AND sno_hpersonalnomina.pagtaqper = 0 ".
					"   AND sno_hpersonalnomina.pagefeper = 1 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND scg_cuentas.codemp = sno_hpersonalnomina.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hpersonalnomina.cueaboper ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_conceptos_scg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_scg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_conceptos_scg_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_scg_proyecto 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos por proyectos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_ep1.estint = 0       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla  ".				
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc , sno_hproyectopersonal.codproy, scg_cuentas.sc_cuenta "; 
		// CUENTAS CONTABLES DE INTERCOMPAÑIAS
		$ls_sql=$ls_sql."   UNION   ".
		        "SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_ep1.estint = 1            ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla             ".
				"   AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint          ". 
                "   AND spg_cuentas.scgctaint  = scg_cuentas.sc_cuenta    ".				
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc , sno_hproyectopersonal.codproy, ".
				"          scg_cuentas.sc_cuenta "; 
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc , sno_hproyectopersonal.codproy, scg_cuentas.sc_cuenta ".
				" ORDER BY codper, codproy "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_conceptos_scg_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_cuenta=$rs_data->fields["cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,2)!=round($li_totalant,2))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("operacion",$ls_operacionant);
							$this->DS->insertRow("cuenta",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],2);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codant=$ls_codper;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$li_totalant=$li_total;
				}
				$this->DS->insertRow("operacion",$ls_operacion);
				$this->DS->insertRow("cuenta",$ls_cuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,2,".","")!=number_format($li_totalant,2,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("operacion",$ls_operacionant);
				$this->DS->insertRow("cuenta",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_scg_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_conceptos_scg_proyectos($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										   	   $as_cuentapasivo,$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,$ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_conceptos_scg_proyectos 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N";
		$as_codcla="--";
		$li_totrow=$this->DS->getRowCount("cuenta");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cuenta=$this->DS->data["cuenta"][$li_i];
			$ls_operacion=$this->DS->data["operacion"][$li_i];
			$li_total=abs(round($this->DS->data["total"][$li_i],2));
			$ls_codconc="0000000001";
			$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
			if($li_total>0)
			{
				$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
				if (!$lb_existe)
				{
					$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																	$as_descripcion,$ls_cuenta,$ls_operacion,$li_total,
																	$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																	$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																	$ls_codconc,$li_total);						
				}								

			}
		}
		$this->DS->reset_ds();
		return  $lb_valido;    
	}// end function uf_insert_conceptos_scg_proyectos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_spg_normales()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_spg_normales 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, ".
				"		spg_cuentas.spg_cuenta AS cueprepatcon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc, sno_hconcepto.estcla ".
				"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '1' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.codestpro1, sno_hconcepto.codestpro2, sno_hconcepto.codestpro3, ".
				"		   sno_hconcepto.codestpro4, sno_hconcepto.codestpro5, sno_hconcepto.estcla, spg_cuentas.spg_cuenta, ".
				"		   sno_hconcepto.codprov, sno_hconcepto.cedben ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, ".
				"       spg_cuentas.spg_cuenta AS cueprepatcon, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc, sno_hunidadadmin.estcla ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hconcepto.codconc, sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, ".
				"          sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, sno_hunidadadmin.estcla, spg_cuentas.spg_cuenta, ".
				"          sno_hconcepto.codprov, sno_hconcepto.cedben ".
				" ORDER BY codconc, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprepatcon, codprov, cedben ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_aportes_spg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_spg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_spg_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_spg_proyecto 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, ".
				"		spg_cuentas.spg_cuenta, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." AS montoparcial, MAX(sno_hconcepto.codprov) AS codprov, MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, sno_hproyecto.codproy, ".
				"		sno_hproyectopersonal.codper, MAX(sno_hproyectopersonal.pordiames) as pordiames,".
				"       sno_hproyecto.estcla ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur = '".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, ".
				"		   spg_cuentas.spg_cuenta, sno_hconcepto.codconc, sno_hproyecto.estcla ".
				" ORDER BY sno_hproyectopersonal.codper, sno_hproyecto.codproy, sno_hproyecto.codestpro1, sno_hproyecto.codestpro2, sno_hproyecto.codestpro3, sno_hproyecto.codestpro4, sno_hproyecto.codestpro5, ".
				"		   spg_cuentas.spg_cuenta, sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_aportes_spg_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_codestpro1ant="";
			$ls_codestpro2ant="";
			$ls_codestpro3ant="";
			$ls_codestpro4ant="";
			$ls_codestpro5ant="";
			$ls_cuentaant="";
			$ls_codconcant="";
			$ls_codprovant="";
			$ls_cedbenant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_codprov=$rs_data->fields["codprov"];
				$ls_cedben=$rs_data->fields["cedben"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,2)!=round($li_totalant,2))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
							$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
							$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
							$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
							$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
							$this->DS->insertRow("estcla",$ls_estclaant);
							$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("codprov",$ls_codprovant);
							$this->DS->insertRow("cedben",$ls_cedbenant);
							$this->DS->insertRow("codconc",$ls_codconcant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],2);
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaant=$ls_estcla;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_codconcant=$ls_codconc;
					$ls_codprovant=$ls_codprov;
					$ls_cedbenant=$ls_cedben;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaant=$ls_estcla;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_codconcant=$ls_codconc;
					$ls_codprovant=$ls_codprov;
					$ls_cedbenant=$ls_cedben;
				}
				$this->DS->insertRow("codestpro1",$ls_codestpro1);
				$this->DS->insertRow("codestpro2",$ls_codestpro2);
				$this->DS->insertRow("codestpro3",$ls_codestpro3);
				$this->DS->insertRow("codestpro4",$ls_codestpro4);
				$this->DS->insertRow("codestpro5",$ls_codestpro5);
				$this->DS->insertRow("estcla",$ls_estcla);
				$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("codprov",$ls_codprov);
				$this->DS->insertRow("cedben",$ls_cedben);
				$this->DS->insertRow("codconc",$ls_codconc);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,2,".","")!=number_format($li_totalant,2,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
				$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
				$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
				$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
				$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
				$this->DS->insertRow("estcla",$ls_estclaant);
				$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("codprov",$ls_codprovant);
				$this->DS->insertRow("cedben",$ls_cedbenant);
				$this->DS->insertRow("codconc",$ls_codconcant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_spg_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_aportes_spg_proyectos($as_codcom,$as_operacionaporte,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
										     $as_cuentapasivo,$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_aportes_spg_proyectos 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionaporte  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_tipnom="A"; // tipo de contabilización
		$li_totrow=$this->DS->getRowCount("cueprepatcon");
		$as_codcla="--";
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_codestpro1=$this->DS->data["codestpro1"][$li_i];
			$ls_codestpro2=$this->DS->data["codestpro2"][$li_i];
			$ls_codestpro3=$this->DS->data["codestpro3"][$li_i];
			$ls_codestpro4=$this->DS->data["codestpro4"][$li_i];
			$ls_codestpro5=$this->DS->data["codestpro5"][$li_i];
			$ls_estcla=$this->DS->data["estcla"][$li_i];
			$ls_cueprepatcon=$this->DS->data["cueprepatcon"][$li_i];
			$li_total=abs(round($this->DS->data["total"][$li_i],2));
			$ls_codpro=$this->DS->data["codprov"][$li_i];
			$ls_cedben=$this->DS->data["cedben"][$li_i];
			$ls_codconc=$this->DS->data["codconc"][$li_i];
			if($ls_codpro=="----------")
			{
				$ls_tipodestino="B";
			}
			if($ls_cedben=="----------")
			{
				$ls_tipodestino="P";
			}
			$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
			if($li_total>0)
			{
				$lb_existe=$this->uf_select_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																$ls_codestpro5,$ls_estcla,$ls_cueprepatcon,$as_operacionaporte,$ls_codconc);
				if (!$lb_existe)
				{
				
					$lb_valido=$this->uf_insert_contabilizacion_spg($as_codcom,$as_operacionaporte,$ls_codpro,$ls_cedben,
																$ls_tipodestino,$as_descripcion,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
																$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_cueprepatcon,$li_total,$ls_tipnom,
																$ls_codconc,$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
				}
				else
				{
					$lb_valido=$this->uf_update_contabilizacion_spg($as_codcom,$ls_tipnom,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
															    $ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_cueprepatcon,$as_operacionaporte,
																$ls_codconc,$li_total);						
				}
			}
		}
		$this->DS->reset_ds();
		return  $lb_valido;    
	}// end function uf_insert_aportes_spg_proyectos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_scg_normales($as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_scg_normales 
		//	    Arguments: as_operacionaporte  //  operación del aporte
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaaltapo"]);
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}		
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
				$ls_sql="SELECT cuenta,  operacion, total, codprov, cedben, codconc   ".
					"  FROM apo_scg_norm_ajuste_proy              ".
					" WHERE codemp='".$this->ls_codemp."'                         ".
					"   AND codnom='".$this->ls_codnom."'                         ".
					"	AND anocur='".$this->ls_anocurnom."'                      ".
					"	AND codperi='".$this->ls_peractnom."'                     ".
					" UNION                                                         ".
					"SELECT cuenta,  operacion, total, codprov, cedben, codconc   ".
					"  FROM apo_scg_norm_ajuste_proy_int          ".
					" WHERE codemp='".$this->ls_codemp."'                         ".
					"	AND codnom='".$this->ls_codnom."'                         ".
					"	AND anocur='".$this->ls_anocurnom."'                      ".
					"	AND codperi='".$this->ls_peractnom."'                     ";	
		}
		else
		{
			$ls_sql="SELECT cuenta,  operacion, total, codprov, cedben, codconc   ".
					"  FROM load_aportes_scg_normales_ajuste_proyecto              ".
					" WHERE codemp='".$this->ls_codemp."'                         ".
					"   AND codnom='".$this->ls_codnom."'                         ".
					"	AND anocur='".$this->ls_anocurnom."'                      ".
					"	AND codperi='".$this->ls_peractnom."'                     ".
					" UNION                                                         ".
					"SELECT cuenta,  operacion, total, codprov, cedben, codconc   ".
					"  FROM load_aportes_scg_normales_ajuste_proyecto_int          ".
					" WHERE codemp='".$this->ls_codemp."'                         ".
					"	AND codnom='".$this->ls_codnom."'                         ".
					"	AND anocur='".$this->ls_anocurnom."'                      ".
					"	AND codperi='".$this->ls_peractnom."'                     ";	
		}
		if(($as_operacionaporte=="OC")&&($ai_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
					"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.codprov <> '----------' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND sno_hconcepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_hconcepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
					"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_hconcepto.cedben <> '----------' ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"	AND sno_hconcepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_hconcepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben  ";
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
					"		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hconcepto, scg_cuentas ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND scg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_hconcepto.cueconpatcon ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_aportes_scg_normales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_scg_normales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_aportes_scg_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_scg_proyecto 
		//	    Arguments:
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta, CAST('D' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
				"		".$ls_cadena." AS montoparcial, MAX(sno_hconcepto.codprov) AS codprov, MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, ".
				"		sno_hproyectopersonal.codper, sno_hproyecto.codproy, MAX(sno_hproyectopersonal.pordiames) as pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND spg_ep1.estint = 0       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla  ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyecto.codproy, scg_cuentas.sc_cuenta ";				
			//buscamos las cuentas contables de intercompañias
			$ls_sql=$ls_sql."   UNION   ".
			    "SELECT scg_cuentas.sc_cuenta, CAST('D' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
				"		".$ls_cadena." AS montoparcial, MAX(sno_hconcepto.codprov) AS codprov, MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, ".
				"		sno_hproyectopersonal.codper, sno_hproyecto.codproy, MAX(sno_hproyectopersonal.pordiames) as pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND spg_ep1.estint = 1       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla             ".
				"   AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint          ". 
                "   AND spg_cuentas.scgctaint  = scg_cuentas.sc_cuenta    ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyecto.codproy, scg_cuentas.sc_cuenta ".
				" ORDER BY codper, codconc, codproy, sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_aportes_scg_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			$ls_codconcant="";
			$ls_codprovant="";
			$ls_cedbenant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_cuenta=$rs_data->fields["sc_cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$ls_codprov=$rs_data->fields["codprov"];
				$ls_cedben=$rs_data->fields["cedben"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_codconc!=$ls_codconcant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,2)!=round($li_totalant,2))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("operacion",$ls_operacionant);
							$this->DS->insertRow("cuenta",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("codprov",$ls_codprovant);
							$this->DS->insertRow("cedben",$ls_cedbenant);
							$this->DS->insertRow("codconc",$ls_codconcant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],2);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codant=$ls_codper;
					$ls_codconcant=$ls_codconc;
					$ls_codprovant=$ls_codprov;
					$ls_cedbenant=$ls_cedben;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$li_totalant=$li_total;
					$ls_codconcant=$ls_codconc;
					$ls_codprovant=$ls_codprov;
					$ls_cedbenant=$ls_cedben;
				}
				$this->DS->insertRow("operacion",$ls_operacion);
				$this->DS->insertRow("cuenta",$ls_cuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("codprov",$ls_codprov);
				$this->DS->insertRow("cedben",$ls_cedben);
				$this->DS->insertRow("codconc",$ls_codconc);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,2,".","")!=number_format($li_totalant,2,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("operacion",$ls_operacionant);
				$this->DS->insertRow("cuenta",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("codprov",$ls_codprovant);
				$this->DS->insertRow("cedben",$ls_cedbenant);
				$this->DS->insertRow("codconc",$ls_codconcant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_scg_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_aportes_scg_proyectos($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$ai_genrecapo,$ai_tipdocapo,
											 $ai_gennotdeb,$ai_genvou,$as_operacionaporte)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_aportes_scg_proyectos  
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_operacionaporte  //  Operación con que se va a contabilizar los aportes
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 13/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_tipnom="A";
		$as_codcla="--";
		$li_totrow=$this->DS->getRowCount("cuenta");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cuenta=$this->DS->data["cuenta"][$li_i];
			$ls_operacion=$this->DS->data["operacion"][$li_i];
			$li_total=abs(round($this->DS->data["total"][$li_i],2));
			$ls_codpro=$this->DS->data["codprov"][$li_i];
			$ls_cedben=$this->DS->data["cedben"][$li_i];
			$ls_codconc=$this->DS->data["codconc"][$li_i];
			$ls_tipodestino="";
			if($ls_codpro=="----------")
			{
				$ls_tipodestino="B";
			}
			if($ls_cedben=="----------")
			{
				$ls_tipodestino="P";
			}
			$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
			$lb_existe=$this->uf_select_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,$ls_codconc);
			if (!$lb_existe)
			{
				$lb_valido=$this->uf_insert_contabilizacion_scg($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
															$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
															$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,$as_codcla);
			}
			else
			{
				$lb_valido=$this->uf_update_contabilizacion_scg($ls_tipnom,$ls_cuenta,$ls_operacion,$as_codcom,
																$ls_codconc,$li_total);						
			}
		}
		$this->DS->reset_ds();
		return $lb_valido;    
	}// end function uf_insert_aportes_scg_proyectos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contabilizacion_spg($as_codcom,$as_tipnom,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
										   $as_estcla,$as_cueprecon,$as_operacionnomina,$as_codconc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_spg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en sno_dt_spg
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if ($as_codconc=="")
		{
			$as_codconc='----------';
		}
		$li_estatus=0; // No contabilizado
		$ls_sql="SELECT codcom ".
				"  FROM sno_dt_spg ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"  AND  codnom= '".$this->ls_codnom."' ".
				"  AND  codperi='".$this->ls_peractnom."' ".
				"  AND  codcom= '".$as_codcom."' ".
				"  AND  codestpro1= '".$as_codestpro1."' ".
				"  AND  codestpro2= '".$as_codestpro2."' ".
				"  AND  codestpro3= '".$as_codestpro3."' ".
				"  AND  codestpro4= '".$as_codestpro4."' ".
				"  AND  codestpro5= '".$as_codestpro5."' ". 
				"  AND  estcla= '".$as_estcla."' ".
				"  AND  spg_cuenta = '".$as_cueprecon."' ".
				"  AND  operacion= '".$as_operacionnomina."'  ".
				"  AND  codconc= '".$as_codconc."' ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_select_contabilizacion_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if ($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_spg
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizacion_spg($as_codcom,$as_tipnom,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
										   $as_estcla,$as_cueprecon,$as_operacionnomina,$as_codconc,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_contabilizacion_spg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualiza el total des las cuentas presupuestarias
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		if ($as_codconc=="")
		{
			$as_codconc='----------';
		}
		$ls_sql="UPDATE  sno_dt_spg ".
		        " SET monto = (monto + (".$ai_monto.")) ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"  AND  codnom= '".$this->ls_codnom."' ".
				"  AND  codperi='".$this->ls_peractnom."' ".
				"  AND  codcom= '".$as_codcom."' ".
				"  AND  codestpro1= '".$as_codestpro1."' ".
				"  AND  codestpro2= '".$as_codestpro2."' ".
				"  AND  codestpro3= '".$as_codestpro3."' ".
				"  AND  codestpro4= '".$as_codestpro4."' ".
				"  AND  codestpro5= '".$as_codestpro5."' ". 
				"  AND  estcla= '".$as_estcla."' ".
				"  AND  spg_cuenta = '".$as_cueprecon."' ".
				"  AND  operacion= '".$as_operacionnomina."'  ".
				"  AND  codconc= '".$as_codconc."' ";	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_update_contabilizacion_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_update_contabilizacion_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contabilizacion_scg($as_tipnom,$as_cuenta,$as_operacion,$as_codcom,$as_codconc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en sno_dt_scg
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_sql="SELECT codcom FROM sno_dt_scg ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"  AND     codnom= '".$this->ls_codnom."' ".
				"  AND     codperi='".$this->ls_peractnom."' ".
				"  AND     codcom= '".$as_codcom."' ".
				"  AND     tipnom= '".$as_tipnom."' ".				
				"  AND     sc_cuenta = '".$as_cuenta."' ".
				"  AND     debhab= '".$as_operacion."'  ".
				"  AND     codconc= '".$as_codconc."' ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_select_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if ($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_scg
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizacion_scg($as_tipnom,$as_cuenta,$as_operacion,$as_codcom,$as_codconc,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_contabilizacion_scg
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualiza el total de las cuentas contables
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_sql="UPDATE  sno_dt_scg ".
		        "   SET monto = (ROUND( CAST((monto + ".$ai_monto.") as numeric), 2)) ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"   AND codnom= '".$this->ls_codnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codcom= '".$as_codcom."' ".
				"   AND tipnom= '".$as_tipnom."' ".				
				"   AND sc_cuenta = '".$as_cuenta."' ".
				"   AND debhab= '".$as_operacion."'  ".
				"   AND codconc= '".$as_codconc."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_update_contabilizacion_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_update_contabilizacion_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contabilizacion_spi($as_codcom,$as_tipnom,$as_cuenta,$as_operacionnomina,$as_codconc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_contabilizacion_spi
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que busca si el registro  existe en sno_dt_scg
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codcom ".
				"  FROM sno_dt_spi ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"   AND codnom= '".$this->ls_codnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codcom= '".$as_codcom."' ".
				"   AND tipnom= '".$as_tipnom."' ".
				"   AND codestpro1= '".$as_codestpro1."' ".
				"   AND codestpro2= '".$as_codestpro2."' ".
				"   AND codestpro3= '".$as_codestpro3."' ".
				"   AND codestpro4= '".$as_codestpro4."' ".
				"   AND codestpro5= '".$as_codestpro5."' ". 
				"   AND estcla= '".$as_estcla."' ".
				"   AND spi_cuenta = '".$as_cueprecon."' ".
				"   AND operacion= '".$as_operacionnomina."'  ".
				"   AND codconc= '".$as_codconc."' ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_select_contabilizacion_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if ($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_contabilizacion_spi
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_contabilizacion_spi($as_codcom,$as_tipnom,$as_cuenta,$as_operacionnomina,$as_codconc,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_contabilizacion_spi
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacion  //  Operación de la contabilización		
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación		
		//	    		   as_cuenta  //  cuenta contable
		//	    		   as_tipnom  //  Tipo de contabilizacion si es de nómina ó de aporte
		//			       as_codconc // código del concepto
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que actualiza el total de las cuentas contables
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/02/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		
		$ls_sql="UPDATE  sno_dt_spi ".
		        "   SET monto = (monto + ".$ai_monto.") ".
		        " WHERE codemp= '".$this->ls_codemp."' ".
				"   AND codnom= '".$this->ls_codnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codcom= '".$as_codcom."' ".
				"   AND tipnom= '".$as_tipnom."' ".
				"   AND codestpro1= '".$as_codestpro1."' ".
				"   AND codestpro2= '".$as_codestpro2."' ".
				"   AND codestpro3= '".$as_codestpro3."' ".
				"   AND codestpro4= '".$as_codestpro4."' ".
				"   AND codestpro5= '".$as_codestpro5."' ". 
				"   AND estcla= '".$as_estcla."' ".
				"   AND spi_cuenta = '".$as_cueprecon."' ".
				"   AND operacion= '".$as_operacionnomina."'  ".
				"   AND codconc= '".$as_codconc."' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_update_contabilizacion_spi ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_update_contabilizacion_spi
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//------------------------------------------------------------------------------------------------------------------------------------
    function uf_validarcierre_gastos_ingreso($as_statusg,$as_statusi)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validarcierre_gastos_ingreso
		//		   Access: private
		//     Argumentos: 
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que buscas los estatus de cierre del presuepuesto de gastos i de ingreso
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 28/08/2008 								Fecha Última Modificación : 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT estciespg, estciespi ".
				"  FROM sigesp_empresa ".
				" WHERE codemp='".$this->ls_codemp."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_mensajes->message("CLASE->Cierre Periodo MÉTODO->SELECT->uf_validarcierre_gastos_ingreso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$as_statusg= $rs_data->fields["estciespg"];
				$as_statusi= $rs_data->fields["estciespi"];				
			}
		}
		$arrResultado['as_statusg']=$as_statusg;
		$arrResultado['as_statusi']=$as_statusi;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}//fin de uf_validarcierre_gastos_ingreso
	//------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_ajustecontabilizacion_liquidacion($aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_ajustecontabilizacion_liquidacion 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización del período
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creación: 06/12/2010
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql->begin_transaction();
		$ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$ls_codnom=$this->ls_codnom;
		$ls_codperi=$this->ls_peractnom;
		$ls_desnom=$_SESSION["la_nomina"]["desnom"];
		$ld_fecdesper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fecdesper"]);
		$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($_SESSION["la_nomina"]["fechasper"]);
		$ls_codcom=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-L"; // Comprobante de Conceptos
		$ls_codcomapo=$ls_anocurnom."-".$ls_codnom."-".$ls_codperi."-A"; // Comprobante de Aportes
		$ls_descripcion=$ls_desnom."- Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Conceptos
		$ls_descripcionapo=$ls_desnom." APORTES - Período ".$ls_codperi." del ".$ld_fecdesper." al ".$ld_fechasper; // Descripción de Aportes
				
		$ls_cuentapasivo="";
		$ls_operacionnomina="";
		$ls_operacionaporte="";
		$ls_tipodestino="";
		$ls_codpro="";
		$ls_codben="";
		$li_gennotdeb="";
		$li_genvou="";
		$li_genrecdoc="";
		$li_genrecapo="";
		$li_tipdocnom="";
		$li_tipdocapo="";
		$li_estagrapo="";
		$li_recdoccaunom="";
		$ls_tipdoccaunom="";		
		// Obtenemos la configuración de la contabilización de la nómina
		$arrResultado=$this->uf_load_configuracion_contabilizacion($ls_cuentapasivo,$ls_operacionnomina,$ls_operacionaporte,
																$ls_tipodestino,$ls_codpro,$ls_codben,$li_gennotdeb,$li_genvou,
																$li_genrecdoc,$li_genrecapo,$li_tipdocnom,$li_tipdocapo,$li_estagrapo,
																$li_recdoccaunom,$ls_tipdoccaunom);
		$ls_cuentapasivo=$arrResultado['as_cuentapasivo'];
		$ls_operacionnomina=$arrResultado['as_modo'];
		$ls_operacionaporte=$arrResultado['as_modoaporte'];
		$ls_tipodestino=$arrResultado['as_destino'];
		$ls_codpro=$arrResultado['as_codpro'];
		$ls_codben=$arrResultado['as_codben'];
		$li_gennotdeb=$arrResultado['ai_gennotdeb'];
		$li_genvou=$arrResultado['ai_genvou'];
		$li_genrecdoc=$arrResultado['ai_genrecdoc'];
		$li_genrecapo=$arrResultado['ai_genrecapo'];
		$li_tipdocnom=$arrResultado['ai_tipdocnom'];
		$li_tipdocapo=$arrResultado['ai_tipdocapo'];
		$li_estagrapo=$arrResultado['ai_estagrapo'];
		$li_recdoccaunom=$arrResultado['ai_recdoccaunom'];
		$ls_tipdoccaunom=$arrResultado['as_tipdoccaunom'];		
		$lb_valido=$arrResultado['lb_valido'];
		$ls_tipodestino="-";
		$ls_codpro="----------";
		$ls_codben="----------";
                $ls_codclanom=$arrResultado['as_codclanom'];		
		$ls_codclaapo=$arrResultado['as_codclaapo'];
		$ls_codclacau=$arrResultado['as_codclacau'];
		$ls_codclafid=$arrResultado['as_codclafid'];
		$ls_codclaper=$arrResultado['as_codclaper'];                
		if($lb_valido)
		{	// eliminamos la contabilización anterior 
			$lb_valido=$this->uf_delete_contabilizacion($ls_codperi);
		}														
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de conceptos
			$lb_valido=$this->uf_contabilizar_conceptos_spg($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
															$ls_tipodestino,$ls_descripcion,$li_genrecdoc,$li_tipdocnom,
															$li_gennotdeb,$li_genvou,$ls_codclanom);
		}
		if($lb_valido)
		{// insertamos la contabilización de contabilidad de conceptos
			if(($ls_operacionnomina!="O")||(($ls_operacionnomina=="O")&&($li_recdoccaunom=="1")))// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_contabilizar_conceptos_scg($ls_codcom,$ls_operacionnomina,$ls_codpro,$ls_codben,
																$ls_tipodestino,$ls_descripcion,$ls_cuentapasivo,$li_genrecdoc,
																$li_tipdocnom,$li_gennotdeb,$li_genvou,$ls_codclanom,$ls_codclacau);
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de presupuesto de aportes
			$lb_valido=$this->uf_contabilizar_aportes_spg($ls_codcomapo,$ls_operacionaporte,$ls_codpro,$ls_codben,
														  $ls_tipodestino,$ls_descripcionapo,$ls_cuentapasivo,
														  $li_genrecapo,$li_tipdocapo,$li_gennotdeb,$li_genvou,$ls_codclaapo);
		}
		if($lb_valido)
		{// insertamos la contabilización de contabilidad de aportes
			if($ls_operacionaporte!="O")// Si es compromete no genero detalles contables
			{
				$lb_valido=$this->uf_contabilizar_aportes_scg($ls_codcomapo,$ls_codpro,$ls_codben,$ls_tipodestino,
				                                              $ls_descripcionapo,$li_genrecapo,$li_tipdocapo,$li_gennotdeb,
															  $li_genvou,$ls_operacionaporte,$li_estagrapo,$ls_codclaapo);
			}
		}
		if($lb_valido)
		{ // insertamos la contabilización de ingresos
			$lb_valido=$this->uf_contabilizar_ingresos_spi($ls_codcoming,$ls_descripcioning);
		}
		if($lb_valido)
		{ // insertamos la contabilización de ingresos
			$lb_valido=$this->uf_contabilizar_ingresos_scg($ls_codcoming,$ls_descripcioning);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcom); // Nómina
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_verificar_contabilizacion($ls_codcomapo); // Aportes
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Ajustó la Contabilización del Año ".$this->ls_anocurnom." período ".$this->ls_peractnom." asociado a la nómina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
		}
		if($lb_valido)
		{
			$this->io_sql->commit(); 
			$this->io_mensajes->message("El Ajuste de la contabilización fue procesado.");
		}
		else
		{
			$this->io_sql->rollback();
			$this->io_mensajes->message("Ocurrio un error al ajustar la contabilización.");
		}
		return  $lb_valido;    
	}// end function uf_procesar_ajustecontabilizacion_liquidacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_rec_doc_pago_personal_cheque()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_rec_doc_pago_personal_cheque  
		//	    Arguments: 	  
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la informacion para la recepcion de documento del 
		//                 personal que cobra con cheque
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		$ls_codcla="--";
		$ls_ctahaberclasificador="";
		if ($ls_clactacon == '1')
		{
			$ls_codcla=$_SESSION["la_nomina"]["codclaper"];
			$arrResultado=$this->uf_chequear_clasificador($ls_codcla);
			$ls_ctahaberclasificador=$arrResultado['as_ctahaber'];
			$lb_valido=$arrResultado['lb_valido'];
		}
		if ($lb_valido)
		{
			$lb_valido=$this->uf_delete_sno_rd();				
		}
		if ($lb_valido)
		{
			$ls_sql="SELECT sno_hresumen.codper, sno_hresumen.monnetres, sno_hpersonalnomina.cueaboper ".
					"  FROM sno_hresumen, sno_hpersonalnomina ".
					" WHERE sno_hresumen.codemp='".$this->ls_codemp."'        ". 
					"   AND sno_hresumen.codnom='".$this->ls_codnom."'        ".
					"   AND sno_hresumen.anocur='".$this->ls_anocurnom."'    ".
					"   AND sno_hresumen.codperi='".$this->ls_peractnom."'    ".
					"   AND sno_hpersonalnomina.pagefeper=1                   ".
					"   AND sno_hpersonalnomina.codemp=sno_hresumen.codemp     ".
					"   AND sno_hpersonalnomina.codnom=sno_hresumen.codnom     ".
					"   AND sno_hpersonalnomina.anocur=sno_hresumen.anocur     ".
					"   AND sno_hpersonalnomina.codperi=sno_hresumen.codperi     ".
					"   AND sno_hpersonalnomina.codper=sno_hresumen.codper     ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_procesar_rec_doc_pago_personal_cheque ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				while ((!$rs_data->EOF)&&($lb_valido))
				{		 
					$ls_codper=$rs_data->fields["codper"];
					$ld_monpagper=$rs_data->fields["monnetres"];
					$ls_ctadebe=$rs_data->fields["cueaboper"];
					$arrResultado=$this->uf_chequear_beneficiario($ls_codper,$ls_ctahaber);
					$ls_ctahaber=$arrResultado['as_ctahaber'];
					$lb_valido=$arrResultado['lb_valido'];
					if (($lb_valido)&&($ls_ctahaber==""))
					{
						$this->io_mensajes->message("El personal ".$ls_codper." no tiene asocida cuenta contable como beneficiario. No se puede realizar la recepción de Documento al personal");
						$lb_valido=false;
					}
					elseif ($lb_valido)
					{
						$lb_exist_cta=$this->uf_chequear_ctascg($ls_ctadebe);
						if ($lb_exist_cta)
						{
							$lb_valido=$this->uf_insert_sno_rd($ls_codper,$ld_monpagper,$ls_ctadebe,'D',$ls_codcla);
						}
						else
						{
							$this->io_mensajes->message("Por favor chequee que el personal ".$ls_codper." tenga una cuenta correcta de beneficiario asociada al plan de cuentas");
							$lb_valido=false;
						}						
						if ($lb_valido)
						{
							if ($ls_clactacon == '1')
							{
								$ls_ctahaber=$ls_ctahaberclasificador;
							}
							$lb_exist_cta=$this->uf_chequear_ctascg($ls_ctahaber);
							if($lb_exist_cta)
							{
								$lb_valido=$this->uf_insert_sno_rd($ls_codper,$ld_monpagper,$ls_ctahaber,'H',$ls_codcla);
							}
							else
							{
								$this->io_mensajes->message("Por favor chequee que el personal ".$ls_codper." tenga una cuenta correcta de beneficiario asociada al plan de cuentas");
								$lb_valido=false;
							}
	
						}
						
					}
					$rs_data->MoveNext();
				}//fin del while
			}	
		}
		return $lb_valido;
	}// end function uf_procesar_rec_doc_pago_personal_cheque
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_sno_rd()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_sno_rd
		//	    Arguments: $
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que elimina la data de recpeciones de documento de un periodo
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codtipdoc=$_SESSION["la_nomina"]["tipdocpagperche"];
		$ls_sql= "DELETE ".
				 "  FROM sno_rd ".
				 " WHERE codemp = '".$this->ls_codemp."' ".
				 "   AND codnom = '".$this->ls_codnom."' ".
				 "   AND codperi = '".$this->ls_peractnom."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Ajustar Contabilizacion MÉTODO->uf_delete_sno_rd ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_delete_sno_rd
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
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
				" WHERE sno_personal.codemp='".$this->ls_codemp."'  ". 
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

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_chequear_clasificador($as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_chequear_clasificador
		//	    Arguments: $as_codcla // código del personal
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar si el personal existe como benficiario y buscar
		//                 su cuenta contable
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
 		$as_ctahaber="";
		$ls_sql="SELECT sc_cuenta ".
		        "  FROM cxp_clasificador_rd  ".
				" WHERE cxp_clasificador_rd.codemp='".$this->ls_codemp."'  ". 
				"   AND cxp_clasificador_rd.codcla='".$as_codcla."' ";
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
				$as_ctahaber=trim($rs_data->fields["sc_cuenta"]);				
			}
			else
			{
				$this->io_mensajes->message("El Clasificador ".$as_codcla." No se encuentra registrado. No se puede realizar la recepción de Documento al personal");
				$lb_valido=false;
			}
			if($as_ctahaber=='')
			{
				$this->io_mensajes->message("El Clasificador ".$as_codcla." tiene una cuenta contable invalida. No se puede realizar la recepción de Documento al personal");
				$lb_valido=false;
			}
		}	
		$arrResultado['as_ctahaber']=$as_ctahaber;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_chequear_clasificador
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_chequear_ctascg($as_ctascg)
	{
		//////////////////////////////////////////////////////////////////////
		// Function :  uf_chequear_ctascg
		//
		// Descripcion : Metodo que se encarga de verificar si una cuenta existe o no
		// 					 
		// Return :  Boolean    
		//
		/////////////////////////////////////////////////////////////////////////
		$lb_valido = false;
		$ls_sql="SELECT sc_cuenta
			 	   FROM scg_cuentas
				  WHERE codemp ='".$this->ls_codemp."' 
				    AND sc_cuenta ='".$as_ctascg."' ";
		$rs_data = $this->io_sql->select($ls_sql);
		if ($rs_data===false)
		   {
			 $lb_valido=false;
			 $this->io_mensajes->message("CLASE->Ajustar Contabilizacion MÉTODO->uf_chequear_ctascg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		   }
		else
		   {
		     if ($row=$this->io_sql->fetch_row($rs_data))
			    {
				  $lb_valido=true;	
			    }
		   } 
		return $lb_valido;
	}
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_sno_rd($as_codper,$ad_monpagper,$as_sc_cuenta,$as_debhab,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_sno_rd
		//	    Arguments: $as_codper // código del personal
		//                 $ad_monpagper // monto de la recepción de documento
	    //                 $as_sc_cuenta // cuenta contable para la recepción de documentos
		//                 $as_debhab // tipo de movimiento (debe -  haber)
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de verificar si el personal existe como benficiario y buscar
		//                 su cuenta contable
	    //     Creado por: Ing. María Beatriz Unda
	    // Fecha Creación: 22/12/2009
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_codtipdoc=$_SESSION["la_nomina"]["tipdocpagperche"];
		$ls_sql=" INSERT INTO sno_rd (codemp, codnom, codperi, codper, sc_cuenta, debhab,monpagper,codtipdoc,estcon,codcla) ".
		        " VALUES ('".$this->ls_codemp."', '".$this->ls_codnom."', '".$this->ls_peractnom."', '".$as_codper."' , ".
				"         '".$as_sc_cuenta."', '".$as_debhab."', '".$ad_monpagper."', '".$ls_codtipdoc."','0','".$as_codcla."') ";	
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Ajustar Contabilizacion MÉTODO->uf_insert_sno_rd ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		return $lb_valido;
	}// end function uf_insert_sno_rd
	//------------------------------------------------------------------------------------------------------------------------------------








///PARA CUANDO SE INTEGRAN CON OTRAS BD NO SE SI FUNCIONA

	//------------------------------------------------------------------------------------------------------------------------------------
    function uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,$as_cuenta,
	                                            $as_operacion,$ai_monto,$as_tipnom,$as_codconc,$ai_genrecdoc,$ai_tipdoc,
												$ai_gennotdeb,$ai_genvou,$as_codcomapo, $as_codest1_G, $as_estcla_G)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_contabilizacion_scg_int
		//		   Access: private
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_programatica  //  Programática
		//	    		   as_cueprecon  //  cuenta presupuestaria
		//	    		   ai_monto  //  monto total
		//	    		   as_tipnom  //  Tipo de contabilización es aporte ó de conceptos
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   as_codconc  //  Código de concepto
		//	    		   ai_tipdoc  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_codcomapo  //  Código del comprobante de aporte
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el total des las cuentas presupuestarias
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 14/08/2008								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_estatus=0; // No contabilizado
		$ls_sql="INSERT INTO sno_dt_scg_int (codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
				"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo,   ".
				"codestpro1, estcla) VALUES ('".$this->ls_codemp."','".$this->ls_codnom."',".
				"'".$this->ls_peractnom."','".$as_codcom."','".$as_tipnom."','".$as_cuenta."','".$as_operacion."','".$as_codconc."',".
				"'".$as_codpro."','".$as_codben."','".$as_tipodestino."','".$as_descripcion."',".$ai_monto.",".$li_estatus.",".
				"'".$ai_genrecdoc."','".$ai_tipdoc."','".$ai_genvou."','".$ai_gennotdeb."','".$as_codcomapo."','".$as_codest1_G."','".$as_estcla_G."')"; //print $ls_sql."<br><br>";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_insert_contabilizacion_scg_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_contabilizacion_scg_int
	//-------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_concepto_reintegro_int()
	{
/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_concepto_reintegro_int
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 05/09/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		
		$ls_sql="  SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total, ".
		        "         spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint                    ".					
                    "   FROM  sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,               ".
					"	      spg_cuentas, scg_cuentas, spg_ep1                                      ".
                    "  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                               ".
					"	 AND sno_hsalida.codnom='".$this->ls_codnom."'                                ".
					"	 AND sno_hsalida.anocur='".$this->ls_anocurnom."'                             ".
					"	 AND sno_hsalida.codperi='".$this->ls_peractnom."'                            ".
                    "    AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
					"    AND sno_hsalida.valsal <> 0                                   ".
					"	 AND sno_hconcepto.sigcon = 'E'                               ".
					"	 AND sno_hconcepto.intprocon = '1'                            ".
					"	 AND spg_cuentas.status = 'C'                                 ".
					"	 AND spg_ep1.estint = 1                                       ".
					"    AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 ".
                    "    AND spg_ep1.estcla = sno_hconcepto.estcla                    ".
                    "    AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint                 ".
					"	 AND sno_hpersonalnomina.codemp = sno_hsalida.codemp          ".
					"	 AND sno_hpersonalnomina.codnom = sno_hsalida.codnom          ".
					"	 AND sno_hpersonalnomina.anocur = sno_hsalida.anocur          ".
					"	 AND sno_hpersonalnomina.codperi = sno_hsalida.codperi        ".
					"	 AND sno_hpersonalnomina.codper = sno_hsalida.codper          ".
					"	 AND sno_hsalida.codemp = sno_hconcepto.codemp                ".
					"	 AND sno_hsalida.codnom = sno_hconcepto.codnom                ".
					"	 AND sno_hsalida.anocur = sno_hconcepto.anocur                ".
					"	 AND sno_hsalida.codperi = sno_hconcepto.codperi              ".
					"	 AND sno_hsalida.codconc = sno_hconcepto.codconc              ".
					"	 AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp     ".
					"	 AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom     ".
					"	 AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur     ".
					"	 AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi   ".
					"	 AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  ".
					"	 AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        ".  
					"	 AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm        ".
					"	 AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm        ".
					"	 AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm        ".
					"	 AND spg_cuentas.codemp = sno_hconcepto.codemp                         ".
					"	 AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon                  ".
					"	 AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                     ".
					"	 AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1      ".
					"	 AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2     ".
					"	 AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3     ".
 					"	 AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4     ".
					"	 AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5    ".
					"	 AND sno_hconcepto.estcla= spg_cuentas.estcla				           ".
                    "  GROUP BY scg_cuentas.sc_cuenta, sno_hsalida.codemp, sno_hsalida.codnom, ".
				    "        sno_hsalida.anocur, sno_hsalida.codperi,spg_cuentas.codestpro1,   ".
					"        spg_cuentas.estcla, spg_cuentas.scgctaint                         ";
              $ls_sql=$ls_sql." UNION ".
                   "  SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) AS total,  ".
				   "         spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint                     ".			  
                   "    FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,                    ".
				   "         spg_cuentas, scg_cuentas, spg_ep1                                        ".
                   "   WHERE sno_hsalida.codemp='".$this->ls_codemp."'                                ".
				   "	 AND sno_hsalida.codnom='".$this->ls_codnom."'                                ".
				   "	 AND sno_hsalida.anocur='".$this->ls_anocurnom."'                             ".
				   "	 AND sno_hsalida.codperi='".$this->ls_peractnom."'                            ".
                   "     AND  (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				   "     AND sno_hsalida.valsal <> 0                                ".
				   "     AND sno_hconcepto.sigcon = 'E'                             ".
				   "     AND sno_hconcepto.intprocon = '0'                          ".
				   "     AND spg_cuentas.status = 'C'                               ".
				   "     AND spg_ep1.estint = 1 ".
				   "     AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 ".
                   "     AND sno_hunidadadmin.estcla = spg_ep1.estcla                ".
                   "     AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint                ".
	               "     AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta           ".
				   "     AND sno_hpersonalnomina.codemp = sno_hsalida.codemp          ".
				   "     AND sno_hpersonalnomina.codnom = sno_hsalida.codnom          ".
				   "     AND sno_hpersonalnomina.anocur = sno_hsalida.anocur          ".
				   "     AND sno_hpersonalnomina.codperi = sno_hsalida.codperi        ".
				   "     AND sno_hpersonalnomina.codper = sno_hsalida.codper          ".
				   "     AND sno_hsalida.codemp = sno_hconcepto.codemp                ".
				   "     AND sno_hsalida.codnom = sno_hconcepto.codnom                ".
				   "     AND sno_hsalida.anocur = sno_hconcepto.anocur                ".
				   "     AND sno_hsalida.codperi = sno_hconcepto.codperi              ".
				   "     AND sno_hsalida.codconc = sno_hconcepto.codconc              ".
				   "     AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp     ".
				   "      AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom    ".
					"	  AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur    ".
					"	  AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi  ".
					"	  AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  ".
					"	  AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        ".
					"	  AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm        ".
					"	  AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm        ".
					"	  AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm        ".
					"	  AND spg_cuentas.codemp = sno_hconcepto.codemp                         ".
					"	  AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon                  ".
					"	  AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                     ".
					"	  AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
					"	  AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
					"	  AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
					"	  AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
					"	  AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
					"	  AND sno_hunidadadmin.estcla= spg_cuentas.estcla					          ".
					" GROUP BY scg_cuentas.sc_cuenta, sno_hsalida.codemp, sno_hsalida.codnom,         ".
					"	   sno_hsalida.anocur, sno_hsalida.codperi, spg_cuentas.codestpro1,           ".
					"      spg_cuentas.estcla, spg_cuentas.scgctaint                                  ";		
		$rs_datos=$this->io_sql->select($ls_sql);
		if($rs_datos===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_concepto_reintegro_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return $rs_datos="";
		}
		else
		{
			return $rs_datos;
		}
	}// fin uf_concepto_reintegro_int
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contabilizar_conceptos_scg_int($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,
	                                           $as_descripcion,$as_cuentapasivo,$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,
											   $ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_conceptos_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 15/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N";
		$rs_datos=array();
		$ls_monto=0;
		//Buscamos las cuentas contables relacionadas al presupeusto estan van por el debe
		$rs_datos=$this->uf_concepto_reintegro_int();							
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion,     
					   SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint        
				  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,       
					   spg_cuentas, scg_cuentas, spg_ep1                                       
				  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
					AND sno_hsalida.codnom='".$this->ls_codnom."'                               
					AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
					AND sno_hsalida.codperi='".$this->ls_peractnom."'                         
					AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') 
					AND sno_hsalida.valsal <> 0            
					AND sno_hconcepto.intprocon = '0'      
					AND spg_cuentas.status = 'C'           
					AND spg_ep1.estint = 1                 
					AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 
					AND sno_hunidadadmin.estcla = spg_ep1.estcla        
					AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint        
					AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta  
					AND sno_hpersonalnomina.codemp = sno_hsalida.codemp 
					AND sno_hpersonalnomina.codnom = sno_hsalida.codnom 
					AND sno_hpersonalnomina.anocur = sno_hsalida.anocur 
					AND sno_hpersonalnomina.codperi = sno_hsalida.codperi 
					AND sno_hpersonalnomina.codper = sno_hsalida.codper   
					AND sno_hsalida.codemp = sno_hconcepto.codemp         
					AND sno_hsalida.codnom = sno_hconcepto.codnom         
					AND sno_hsalida.anocur = sno_hconcepto.anocur         
					AND sno_hsalida.codperi = sno_hconcepto.codperi       
					AND sno_hsalida.codconc = sno_hconcepto.codconc       
					AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp 
					AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom 
					AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur 
					AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi  
					AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  
					AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  
					AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  
					AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm 
					AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  
					AND spg_cuentas.codemp = sno_hconcepto.codemp                   
					AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon            
					AND sno_hunidadadmin.codemp = spg_cuentas.codemp   
					AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1  
					AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 
					AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 
					AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 
					AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5   
					AND sno_hunidadadmin.estcla = spg_cuentas.estcla   
				GROUP BY scg_cuentas.sc_cuenta,sno_hsalida.codemp,spg_cuentas.codestpro1, 
				         spg_cuentas.estcla, spg_cuentas.scgctaint
				UNION				
				SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, 
					SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total,
					spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint                                   
					FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, 
					 spg_cuentas, scg_cuentas, spg_ep1                                  
				   WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
					AND sno_hsalida.codnom='".$this->ls_codnom."'                               
					AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
					AND sno_hsalida.codperi='".$this->ls_peractnom."'                           
					AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') 
					AND sno_hsalida.valsal <> 0          
					AND sno_hconcepto.intprocon = '1'    
					AND spg_cuentas.status = 'C'         
					AND spg_ep1.estint = 1               
					AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 
					AND spg_ep1.estcla = sno_hconcepto.estcla           
					AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint        
					AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta  
					AND sno_hpersonalnomina.codemp = sno_hsalida.codemp 
					AND sno_hpersonalnomina.codnom = sno_hsalida.codnom 
					AND sno_hpersonalnomina.anocur = sno_hsalida.anocur 
					AND sno_hpersonalnomina.codperi = sno_hsalida.codperi  
					AND sno_hpersonalnomina.codper = sno_hsalida.codper  
					AND sno_hsalida.codemp = sno_hconcepto.codemp        
					AND sno_hsalida.codnom = sno_hconcepto.codnom           
					AND sno_hsalida.anocur = sno_hconcepto.anocur           
					AND sno_hsalida.codperi = sno_hconcepto.codperi         
					AND sno_hsalida.codconc = sno_hconcepto.codconc         
					AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp 
					AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom
					AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur 
					AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi 
					AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  
					AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  
					AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  
					AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm  
					AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  
					AND spg_cuentas.codemp = sno_hconcepto.codemp                   
					AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon            
					AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1  
					AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 
					AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3  
					AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4  
					AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 
					AND sno_hconcepto.estcla = spg_cuentas.estcla 
				  GROUP BY scg_cuentas.sc_cuenta, spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint 
                 UNION
				SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion,     
					   SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total,spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint        
				  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,       
					   spg_cuentas, scg_cuentas, spg_ep1                                       
				  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
					AND sno_hsalida.codnom='".$this->ls_codnom."'                               
					AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
					AND sno_hsalida.codperi='".$this->ls_peractnom."'                           
					AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') 
					AND sno_hsalida.valsal <> 0            
					AND sno_hconcepto.intprocon = '0'      
					AND spg_cuentas.status = 'C'           
					AND spg_ep1.estint = 1                 
					AND sno_hunidadadmin.codestpro1  = spg_ep1.codestpro1 
					AND sno_hunidadadmin.estcla = spg_ep1.estcla        
					AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint        
					AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta  
					AND sno_hpersonalnomina.codemp = sno_hsalida.codemp 
					AND sno_hpersonalnomina.codnom = sno_hsalida.codnom 
					AND sno_hpersonalnomina.anocur = sno_hsalida.anocur 
					AND sno_hpersonalnomina.codperi = sno_hsalida.codperi 
					AND sno_hpersonalnomina.codper = sno_hsalida.codper   
					AND sno_hsalida.codemp = sno_hconcepto.codemp         
					AND sno_hsalida.codnom = sno_hconcepto.codnom         
					AND sno_hsalida.anocur = sno_hconcepto.anocur         
					AND sno_hsalida.codperi = sno_hconcepto.codperi       
					AND sno_hsalida.codconc = sno_hconcepto.codconc       
					AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp 
					AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom 
					AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur 
					AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi  
					AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  
					AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  
					AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  
					AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm 
					AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  
					AND spg_cuentas.codemp = sno_hconcepto.codemp                   
					AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon            
					AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1  
					AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 
					AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 
					AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 
					AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5   
					AND sno_hunidadadmin.estcla = spg_cuentas.estcla   
				GROUP BY scg_cuentas.sc_cuenta,sno_hsalida.codemp,spg_cuentas.codestpro1, 
				         spg_cuentas.estcla, spg_cuentas.scgctaint  
					UNION
				 SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, 
					SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total,
					spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint                                   
					FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, 
					 spg_cuentas, scg_cuentas, spg_ep1                                  
				   WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
					AND sno_hsalida.codnom='".$this->ls_codnom."'                               
					AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
					AND sno_hsalida.codperi='".$this->ls_peractnom."'                             
					AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') 
					AND sno_hsalida.valsal <> 0          
					AND sno_hconcepto.intprocon = '1'    
					AND spg_cuentas.status = 'C'         
					AND spg_ep1.estint = 1               
					AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 
					AND spg_ep1.estcla = sno_hconcepto.estcla           
					AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint        
					AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta  
					AND sno_hpersonalnomina.codemp = sno_hsalida.codemp 
					AND sno_hpersonalnomina.codnom = sno_hsalida.codnom 
					AND sno_hpersonalnomina.anocur = sno_hsalida.anocur 
					AND sno_hpersonalnomina.codperi = sno_hsalida.codperi  
					AND sno_hpersonalnomina.codper = sno_hsalida.codper  
					AND sno_hsalida.codemp = sno_hconcepto.codemp        
					AND sno_hsalida.codnom = sno_hconcepto.codnom           
					AND sno_hsalida.anocur = sno_hconcepto.anocur           
					AND sno_hsalida.codperi = sno_hconcepto.codperi         
					AND sno_hsalida.codconc = sno_hconcepto.codconc         
					AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp 
					AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom
					AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur 
					AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi 
					AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  
					AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  
					AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  
					AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm  
					AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  
					AND spg_cuentas.codemp = sno_hconcepto.codemp                   
					AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon            
					AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1  
					AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 
					AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3  
					AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4  
					AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 
					AND sno_hconcepto.estcla = spg_cuentas.estcla 
				  GROUP BY scg_cuentas.sc_cuenta, spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint";			 		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Cierre Periodo 4 MÉTODO->uf_contabilizar_conceptos_scg_int ERROR->"
			                            .$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				if ($rs_datos=="")
				{   
					$this->DS->data=$this->io_sql->obtener_datos($rs_data);
					$this->DS->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
					$li_totrow=$this->DS->getRowCount("cuenta");
					for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
					{
						$ls_cuenta=$this->DS->data["cuenta"][$li_i];
						$ls_operacion=$this->DS->data["operacion"][$li_i];
						$li_total=abs(round($this->DS->data["total"][$li_i],2));
												
						$ls_codest1_G=$this->DS->data["codestpro1"][$li_i];
						$ls_estcla_G=$this->DS->data["estcla"][$li_i];
						$ls_ctaint_G=$this->DS->data["scgctaint"][$li_i];
						
						if ($ls_operacion=="H")
						{
							$ls_cuenta=$ls_ctaint_G;
						}
						$ls_codconc="0000000001";
						$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
						$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																			$as_descripcion,$ls_cuenta,$ls_operacion,$li_total,
																			$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																			$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
																			$ls_codest1_G, $ls_estcla_G);
					}
					$this->DS->resetds("cuenta");
				}
				else
				{
				    
					$this->DS->data=$this->io_sql->obtener_datos($rs_data);
					$this->DS->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
					$li_totrow=$this->DS->getRowCount("cuenta");/// total de conceptos
					
					$this->DS_R->data=$this->io_sql->obtener_datos($rs_datos);
					$this->DS_R->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
					$ls_total=$this->DS_R->getRowCount("cuenta");// total conceptos de reintegro
					
					    for($li_j=1;($li_j<=$ls_total);$li_j++)
						{   
					    	$ls_ctaint=$this->DS_R->data["scgctaint"][$li_j];
						    $ls_codest1=$this->DS_R->data["codestpro1"][$li_j];
						    $ls_estcla=$this->DS_R->data["estcla"][$li_j];						
						    $total=abs(round($this->DS_R->data["total"][$li_j],2));
							$ls_cuenta_R=$this->DS_R->data["cuenta"][$li_j];
						}// fin del for($li_j=1;($li_j<=$ls_total);$li_j++)
						
					for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
					{ 	
						
						$ls_cuenta=$this->DS->data["cuenta"][$li_i];
						$ls_ctaint_G=$this->DS->data["scgctaint"][$li_i];						
						$ls_operacion=$this->DS->data["operacion"][$li_i];
						if ($ls_operacion=="H")
						{
							$ls_cuenta2=$ls_ctaint_G;
						}
						else
						{
							$ls_cuenta2=$ls_cuenta;
						}
						$li_total=abs(round($this->DS->data["total"][$li_i],2));						
						$ls_codest1_G=$this->DS->data["codestpro1"][$li_i];
						$ls_estcla_G=$this->DS->data["estcla"][$li_i];						
						
						if (($ls_ctaint==$ls_ctaint_G)&&($ls_codest1==$ls_codest1_G)&&($ls_estcla==$ls_estcla_G)&&($ls_cuenta_R==$ls_cuenta))
						{   
						    $ls_monto=$li_total; 
							$li_total=$li_total-$total;
						}
						elseif (($ls_monto==$li_total)&&($ls_operacion=="H")&&($ls_ctaint==$ls_ctaint_G)&&($ls_codest1==$ls_codest1_G)&&($ls_estcla==$ls_estcla_G))
						{ 
							$li_total=$li_total-$total;
						}
													
						$ls_codconc="0000000001";
						$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
						$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
																			$as_descripcion,$ls_cuenta2,$ls_operacion,$li_total,
																			$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																			$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
																			$ls_codest1_G, $ls_estcla_G);							
					}//fin del for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)						
					
					$this->DS->resetds("cuenta");
					$this->DS_R->resetds("cuenta");
				}
			}
			$this->io_sql->free_result($rs_data);
		}	
		return $lb_valido;	  

	}// end function uf_contabilizar_conceptos_scg_int
	//------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_contabilizar_aportes_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
                                            $ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contabilizar_aportes_scg 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_operacionaporte  //  operación del aporte
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 15/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="A";		
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion,    
					   sum(abs(sno_hsalida.valsal)) as total,                                
					   sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc,
					   spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint      
				   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,     
					   spg_cuentas, scg_cuentas, spg_ep1                                      
				  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
					AND sno_hsalida.codnom='".$this->ls_codnom."'                               
					AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
					AND sno_hsalida.codperi='".$this->ls_peractnom."'                         
					AND sno_hsalida.valsal <> 0                                                
					AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4') 
					AND sno_hconcepto.intprocon = '1'       
					AND spg_cuentas.status = 'C'            
					AND spg_ep1.estint = 1                  
					AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 
					AND spg_ep1.estcla = sno_hconcepto.estcla                 
					AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint              
					AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta        
					AND sno_hpersonalnomina.codemp = sno_hsalida.codemp       
					AND sno_hpersonalnomina.codnom = sno_hsalida.codnom       
					AND sno_hpersonalnomina.anocur = sno_hsalida.anocur       
					AND sno_hpersonalnomina.codperi = sno_hsalida.codperi     
					AND sno_hpersonalnomina.codper = sno_hsalida.codper       
					AND sno_hsalida.codemp = sno_hconcepto.codemp             
					AND sno_hsalida.codnom = sno_hconcepto.codnom             
					AND sno_hsalida.anocur = sno_hconcepto.anocur             
					AND sno_hsalida.codperi = sno_hconcepto.codperi           
					AND sno_hsalida.codconc = sno_hconcepto.codconc           
					AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp  
					AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom  
					AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur  
					AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi 
					AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm 
					AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm 
					AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm 
					AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm 
					AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm 
					AND spg_cuentas.codemp = sno_hconcepto.codemp                  
					AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon        
					AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 
					AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 
					AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 
					AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 
					AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 
					AND sno_hconcepto.estcla = spg_cuentas.estcla 
				   GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben,
							spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
						UNION
						SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion,    
					   sum(abs(sno_hsalida.valsal)) as total,                                
					   sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc,
					   spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint      
				   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,     
					   spg_cuentas, scg_cuentas, spg_ep1                                      
				  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
					AND sno_hsalida.codnom='".$this->ls_codnom."'                               
					AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
					AND sno_hsalida.codperi='".$this->ls_peractnom."'                           
					AND sno_hsalida.valsal <> 0                                                
					AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4') 
					AND sno_hconcepto.intprocon = '1'       
					AND spg_cuentas.status = 'C'            
					AND spg_ep1.estint = 1                  
					AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 
					AND spg_ep1.estcla = sno_hconcepto.estcla                 
					AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint              
					AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta        
					AND sno_hpersonalnomina.codemp = sno_hsalida.codemp       
					AND sno_hpersonalnomina.codnom = sno_hsalida.codnom       
					AND sno_hpersonalnomina.anocur = sno_hsalida.anocur       
					AND sno_hpersonalnomina.codperi = sno_hsalida.codperi     
					AND sno_hpersonalnomina.codper = sno_hsalida.codper       
					AND sno_hsalida.codemp = sno_hconcepto.codemp             
					AND sno_hsalida.codnom = sno_hconcepto.codnom             
					AND sno_hsalida.anocur = sno_hconcepto.anocur             
					AND sno_hsalida.codperi = sno_hconcepto.codperi           
					AND sno_hsalida.codconc = sno_hconcepto.codconc           
					AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp  
					AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom  
					AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur  
					AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi 
					AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm 
					AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm 
					AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm 
					AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm 
					AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm 
					AND spg_cuentas.codemp = sno_hconcepto.codemp                  
					AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon        
					AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 
					AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 
					AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 
					AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 
					AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 
					AND sno_hconcepto.estcla = spg_cuentas.estcla 
				   GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben,
							spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
			UNION
		SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion,    
				   sum(abs(sno_hsalida.valsal)) as total,                                
				   sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc,
					spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint    
			   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,     
				spg_cuentas, scg_cuentas,spg_ep1                                       
			 WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
				AND sno_hsalida.codnom='".$this->ls_codnom."'                               
				AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
				AND sno_hsalida.codperi='".$this->ls_peractnom."'                        
				AND sno_hsalida.valsal <> 0                                                 
				AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')  
				AND sno_hconcepto.intprocon = '0'                 
				AND spg_cuentas.status = 'C'                      
				AND spg_ep1.estint = 1                            
				AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 
				AND sno_hunidadadmin.estcla = spg_ep1.estcla      
				AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint      
				AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta   
				AND sno_hpersonalnomina.codemp = sno_hsalida.codemp  
				AND sno_hpersonalnomina.codnom = sno_hsalida.codnom  
				AND sno_hpersonalnomina.anocur = sno_hsalida.anocur  
				AND sno_hpersonalnomina.codperi = sno_hsalida.codperi 
				AND sno_hpersonalnomina.codper = sno_hsalida.codper   
				AND sno_hsalida.codemp = sno_hconcepto.codemp         
				AND sno_hsalida.codnom = sno_hconcepto.codnom         
				AND sno_hsalida.anocur = sno_hconcepto.anocur         
				AND sno_hsalida.codperi = sno_hconcepto.codperi       
				AND sno_hsalida.codconc = sno_hconcepto.codconc       
				AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp 
				AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom 
				AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur 
				AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi 
				AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  
				AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        
				AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm        
				AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm        
				AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm        
				AND spg_cuentas.codemp = sno_hconcepto.codemp                         
				AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon               
				AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 
				AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 
				AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 
				AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 
				AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 
				AND sno_hunidadadmin.estcla = spg_cuentas.estcla 
			   GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben,
						spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint
					UNION
			   SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion,    
				   sum(abs(sno_hsalida.valsal)) as total,                                
				   sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc,
					spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint    
			   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,     
				spg_cuentas, scg_cuentas,spg_ep1                                       
			 WHERE sno_hsalida.codemp='".$this->ls_codemp."'                             
				AND sno_hsalida.codnom='".$this->ls_codnom."'                               
				AND sno_hsalida.anocur='".$this->ls_anocurnom."'                      
				AND sno_hsalida.codperi='".$this->ls_peractnom."'                            
				AND sno_hsalida.valsal <> 0                                                 
				AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')  
				AND sno_hconcepto.intprocon = '0'                 
				AND spg_cuentas.status = 'C'                      
				AND spg_ep1.estint = 1                            
				AND sno_hunidadadmin.codestpro1  = spg_ep1.codestpro1 
				AND sno_hunidadadmin.estcla = spg_ep1.estcla      
				AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint      
				AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta   
				AND sno_hpersonalnomina.codemp = sno_hsalida.codemp  
				AND sno_hpersonalnomina.codnom = sno_hsalida.codnom  
				AND sno_hpersonalnomina.anocur = sno_hsalida.anocur  
				AND sno_hpersonalnomina.codperi = sno_hsalida.codperi 
				AND sno_hpersonalnomina.codper = sno_hsalida.codper   
				AND sno_hsalida.codemp = sno_hconcepto.codemp         
				AND sno_hsalida.codnom = sno_hconcepto.codnom         
				AND sno_hsalida.anocur = sno_hconcepto.anocur         
				AND sno_hsalida.codperi = sno_hconcepto.codperi       
				AND sno_hsalida.codconc = sno_hconcepto.codconc       
				AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp 
				AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom 
				AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur 
				AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi 
				AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  
				AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        
				AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm        
				AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm        
				AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm        
				AND spg_cuentas.codemp = sno_hconcepto.codemp                         
				AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon               
				AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 
				AND sno_hunidadadmin.codestpro2  = spg_cuentas.codestpro2 
				AND sno_hunidadadmin.codestpro3  = spg_cuentas.codestpro3 
				AND sno_hunidadadmin.codestpro4  = spg_cuentas.codestpro4 
				AND sno_hunidadadmin.codestpro5  = spg_cuentas.codestpro5 
				AND sno_hunidadadmin.estcla  = spg_cuentas.estcla 
			   GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben,
						spg_cuentas.codestpro1, spg_cuentas.estcla, spg_cuentas.scgctaint";								
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_contabilizar_aportes_scg_int ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS->group_by(array('0'=>'codconc','1'=>'cuenta','2'=>'operacion'),array('0'=>'total'),array('0'=>'codconc','1'=>'cuenta','2'=>'operacion'));
				$li_totrow=$this->DS->getRowCount("cuenta");
				for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
				{
					$ls_cuenta=$this->DS->data["cuenta"][$li_i];
					$ls_operacion=$this->DS->data["operacion"][$li_i];
					$li_total=abs(round($this->DS->data["total"][$li_i],2));
					$ls_codpro=$this->DS->data["codprov"][$li_i];
					$ls_cedben=$this->DS->data["cedben"][$li_i];
					$ls_codconc=$this->DS->data["codconc"][$li_i];
					
					$ls_codest1_G=$this->DS->data["codestpro1"][$li_i];
					$ls_estcla_G=$this->DS->data["estcla"][$li_i];
					$ls_ctaint_G=$this->DS->data["scgctaint"][$li_i];
					if ($ls_operacion=="H")
					{
						$ls_cuenta=$ls_ctaint_G;
					}
					$ls_tipodestino="";
					if($ls_codpro=="----------")
					{
						$ls_tipodestino="B";
					}
					if($ls_cedben=="----------")
					{
						$ls_tipodestino="P";
					}
					$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
					$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
																	$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
																	$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,
																	$ls_codcomapo, $ls_codest1_G, $ls_estcla_G);
				}
				$this->DS->resetds("cuenta");
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contabilizar_aportes_scg_int
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
   function uf_load_conceptos_scg_normales_int($as_operacionnomina,$as_cuentapasivo,$ai_genrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_scg_normales 
		//	    Arguments: as_operacionnomina  //  Operación de la contabilización
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 18/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		///CUENTAS CONTABLES DE INTERCOMPAÑIS QUE VAN POR EL DEBE
			  $ls_sql=" SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion,     ".
				      "         SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total                                       ".
					  "	  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,      ".
					  "	       spg_cuentas, scg_cuentas, spg_ep1                                       ".
					  "  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                               ".
					  "	   AND sno_hsalida.codnom='".$this->ls_codnom."'                               ".
					  "	   AND sno_hsalida.anocur='".$this->ls_anocurnom."'                            ".
					  "	   AND sno_hsalida.codperi='".$this->ls_peractnom."'                           ". 
					  "	   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
					  "	   AND sno_hsalida.valsal <> 0                                                 ".
					  "	   AND sno_hconcepto.intprocon = '1'                                           ".
					  "	   AND sno_hconcepto.conprocon = '0'                                           ".
					  "	   AND spg_cuentas.status = 'C'                                                ".
					  "	   AND spg_ep1.estint = 1                                                      ".
					  "	   AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1                ".
					  "	   AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta                          ".
					  "	   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp                         ".
					  "	   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom                         ".
					  "	   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur                         ".
					  "	   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi                       ".
					  "	   AND sno_hpersonalnomina.codper = sno_hsalida.codper                         ".
					  "	   AND sno_hsalida.codemp = sno_hconcepto.codemp                               ".
					  "	   AND sno_hsalida.codnom = sno_hconcepto.codnom                               ".
					  "	   AND sno_hsalida.anocur = sno_hconcepto.anocur                               ".
					  "	   AND sno_hsalida.codperi = sno_hconcepto.codperi                             ".
					  "	   AND sno_hsalida.codconc = sno_hconcepto.codconc                             ".
					  "	   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp                    ".
					  "	   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom                    ".
					  "	   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur                    ".
					  "	   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi                  ". 
					  "	   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm        ".
					  "	   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm              ".
					  "	   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm              ".
					  "	   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm              ".
					  "	   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm              ".
					  "	   AND spg_cuentas.codemp = sno_hconcepto.codemp                               ".
					  "	   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon                        ".
					  "	   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                           ".
					  "	   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1              ". 
					  "	   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2             ".
					  "	   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3             ".
					  "	   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4             ".
					  "	   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5            ".
					  "	   AND sno_hconcepto.estcla = spg_cuentas.estcla                               ".
					  "	 GROUP BY scg_cuentas.sc_cuenta                                                ".
					  "	  UNION                                                                        ".
					  "	 SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion,    ".
					  "         SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total                                       ".
					  "	   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,     ".
					  "         spg_cuentas, scg_cuentas, spg_ep1                                      ".
					  "  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                               ".    
					  "	    AND sno_hsalida.codnom='".$this->ls_codnom."'                              ".
					  "	    AND sno_hsalida.anocur='".$this->ls_anocurnom."'                           ".
					  "	    AND sno_hsalida.codperi='".$this->ls_peractnom."'                          ".  
					  "	    AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
					  "		AND sno_hsalida.valsal <> 0                                                ".          
					  "		AND sno_hconcepto.intprocon = '0'                                          ".
					  "		AND sno_hconcepto.conprocon = '0'                                          ".
					  "		AND spg_cuentas.status = 'C'                                               ".
					  "		AND spg_ep1.estint = 1                                                     ".
					  "		AND sno_hunidadadmin.codestpro1  = spg_ep1.codestpro1      ".
					  "		AND sno_hunidadadmin.estcla = spg_ep1.estcla                               ".
					  "		AND sno_hpersonalnomina.codemp = sno_hsalida.codemp                        ". 
					  "	    AND sno_hpersonalnomina.codnom = sno_hsalida.codnom                        ".
					  "		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur                        ".
					  "		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi                      ".
					  "		AND sno_hpersonalnomina.codper = sno_hsalida.codper                        ".
					  "		AND sno_hsalida.codemp = sno_hconcepto.codemp                              ".
					  "		AND sno_hsalida.codnom = sno_hconcepto.codnom                              ".
					  "		AND sno_hsalida.anocur = sno_hconcepto.anocur                              ".
					  "		AND sno_hsalida.codperi = sno_hconcepto.codperi                            ".
					  "		AND sno_hsalida.codconc = sno_hconcepto.codconc                            ".
					  "		AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp                   ".
					  "		AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom                   ".
					  "		AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur                   ".
					  "		AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi                 ".
					  "		AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm       ".
					  "		AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm             ".
					  "		AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm             ".
					  "		AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm             ".
					  "		AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm             ".
					  "		AND spg_cuentas.codemp = sno_hconcepto.codemp                              ".
					  "		AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon                       ".
					  "		AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                          ".
					  "		AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1    ".
					  "		AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2   ".
					  "		AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3   ". 
					  "		AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4   ".
					  "		AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5  ".
					  "		AND sno_hunidadadmin.estcla = spg_cuentas.estcla                           ". 
					  "	  GROUP BY scg_cuentas.sc_cuenta                                               ";
				//se buscan las cuentas contables de intercompañias que van por el haber
				$ls_sql=$ls_sql." UNION ".
				      " SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion,     ".
					  "        SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total                                        ".
					  "	  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida,                     ".
					  "	        sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1                       ".
					  "  WHERE sno_hsalida.codemp='".$this->ls_codemp."'                               ".
					  "	   AND sno_hsalida.codnom='".$this->ls_codnom."'                               ".
					  "	   AND sno_hsalida.anocur='".$this->ls_anocurnom."'                            ".
					  "	   AND sno_hsalida.codperi='".$this->ls_peractnom."'                           ". 
					  "	   and (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')  ".
					  "	   AND sno_hsalida.valsal <> 0                ".
					  "	   AND sno_hconcepto.intprocon = '1'          ".
					  "	   AND sno_hconcepto.conprocon = '0'          ".
					  "	   AND spg_cuentas.status = 'C'               ". 
					  "	   AND spg_ep1.estint = 1                     ".
					  "	   AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 ".
					  "	   AND spg_ep1.estcla = sno_hconcepto.estcla                    ".
					  "	   AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint                 ".
					  "	   AND spg_cuentas.scgctaint  = scg_cuentas.sc_cuenta           ". 
					  "	   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp          ".
					  "	   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom          ". 
					  "	   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur          ".
					  "	   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi        ".
					  "	   AND sno_hpersonalnomina.codper = sno_hsalida.codper          ".
					  "	   AND sno_hsalida.codemp = sno_hconcepto.codemp                ". 
					  "	   AND sno_hsalida.codnom = sno_hconcepto.codnom                ".
					  "	   AND sno_hsalida.anocur = sno_hconcepto.anocur                ".
					  "	   AND sno_hsalida.codperi = sno_hconcepto.codperi              ".
					  "	   AND sno_hsalida.codconc = sno_hconcepto.codconc              ".
					  "	   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp     ".
					  "	   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom     ".
					  "	   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur     ".
					  "	   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi   ".
					  "	   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "	   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  ".
					  "	   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  ".
					  "	   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm  ".
					  "	   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  ". 
					  "	   AND spg_cuentas.codemp = sno_hconcepto.codemp                   ".
					  "	   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon            ".
					  "	   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1  ". 
					  "	   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
					  "	   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
					  "	   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ". 
					  "	   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".   
					  "	   AND sno_hconcepto.estcla = spg_cuentas.estcla ".   
					  "	 GROUP BY scg_cuentas.sc_cuenta ".
					  "	  UNION ".
					  "	 SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion,  ".
					  "         SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total                                     ". 
					  "	   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,   ".
					  "          spg_cuentas, scg_cuentas, spg_ep1                                   ".
				      "   WHERE sno_hsalida.codemp='".$this->ls_codemp."'                            ".
					  "	    AND sno_hsalida.codnom='".$this->ls_codnom."'                            ".
					  "	    AND sno_hsalida.anocur='".$this->ls_anocurnom."'                         ".
					  "	    AND sno_hsalida.codperi='".$this->ls_peractnom."'                        ". 
					  "		and (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
					  "		AND sno_hsalida.valsal <> 0                ".
					  "  	AND sno_hconcepto.intprocon = '0'          ".
					  "		AND sno_hconcepto.conprocon = '0'          ". 
					  "		AND spg_cuentas.status = 'C'               ".
					  "		AND spg_ep1.estint = 1                     ".
					  "		AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1".
					  "		AND sno_hunidadadmin.estcla = spg_ep1.estcla ".
					  "		AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint  ".
					  "		AND spg_cuentas.scgctaint  = scg_cuentas.sc_cuenta   ".
					  "		AND sno_hpersonalnomina.codemp = sno_hsalida.codemp  ".
					  "		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom  ".
					  "		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur  ".
					  "		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					  "		AND sno_hpersonalnomina.codper = sno_hsalida.codper   ".
					  "		AND sno_hsalida.codemp = sno_hconcepto.codemp         ". 
					  "		AND sno_hsalida.codnom = sno_hconcepto.codnom         ".     
					  "		AND sno_hsalida.anocur = sno_hconcepto.anocur         ".
					  "		AND sno_hsalida.codperi = sno_hconcepto.codperi       ".
					  "		AND sno_hsalida.codconc = sno_hconcepto.codconc       ".
					  "		AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "		AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom  ".
					  "		AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur  ". 
					  "		AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					  "		AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "		AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        ".
					  "		AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm       ".
					  "		AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm       ".
					  "		AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm       ".
					  "		AND spg_cuentas.codemp = sno_hconcepto.codemp                        ".
					  "		AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon                 ".
					  "		AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1  ".
					  "		AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2  ".
					  "		AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3  ".
					  "		AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4  ".
					  "		AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5   ".  
					  "		AND sno_hunidadadmin.estcla = spg_cuentas.estcla   ".  
					  "	  GROUP BY scg_cuentas.sc_cuenta                                                 ";
					  		  
				$ls_sql=$ls_sql." UNION ".
				   "SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
					" FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND sno_hconcepto.sigcon = 'E' ".
					"   AND sno_hconcepto.intprocon = '1' ".
					"   AND sno_hconcepto.conprocon = '0' ".
					"   AND spg_cuentas.status = 'C' ".
					"   AND spg_ep1.estint = 1       ".
					"	AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 ".
					"	AND spg_ep1.estcla = sno_hconcepto.estcla           ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
					"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
					"   AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
					"   AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
					"   AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
					"   AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
					"   AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
					"   AND sno_hconcepto.estcla = spg_cuentas.estcla ".
					" GROUP BY scg_cuentas.sc_cuenta ";		
			$ls_sql=$ls_sql." UNION ".
				" SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
					"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
					"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND sno_hconcepto.sigcon = 'E' ".
					"   AND sno_hconcepto.intprocon = '0' ".
					"   AND sno_hconcepto.conprocon = '0' ".
					"   AND spg_cuentas.status = 'C'".
					"   AND spg_ep1.estint = 1      ".
					"	AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 ".
					"   AND sno_hunidadadmin.estcla = spg_ep1.estcla           ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
					"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
					"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
					"   AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
					"   AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
					"   AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
					"   AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
					"   AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
					"   AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
					" GROUP BY scg_cuentas.sc_cuenta ";
					
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_conceptos_scg_normales_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{

				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_scg_normales_int
	//----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_load_conceptos_scg_proyecto_int()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_conceptos_scg_proyecto_int
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos por proyectos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 17/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta as cuenta, CAST('D' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_ep1.estint = 1       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla  ".				
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc , sno_hproyectopersonal.codproy, scg_cuentas.sc_cuenta "; 
		// CUENTAS CONTABLES DE INTERCOMPAÑIAS
		$ls_sql=$ls_sql."   UNION   ".
		        "SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_ep1.estint = 1            ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla             ".
				"   AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint          ". 
                "   AND spg_cuentas.scgctaint  = scg_cuentas.sc_cuenta    ".				
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc , sno_hproyectopersonal.codproy, ".
				"          scg_cuentas.sc_cuenta ";				
				$ls_sql=$ls_sql." UNION ".
				"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_hsalida.valsal as numeric), 2)) as total, ".
				"		".$ls_cadena." as montoparcial, sno_hproyectopersonal.codper, sno_hproyectopersonal.codproy, ".
				"		MAX(sno_hproyectopersonal.pordiames) as pordiames, sno_hconcepto.codconc ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2') ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.sigcon = 'E' ".
				"   AND sno_hconcepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND spg_ep1.estint = 1       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla  ".				
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprecon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_hproyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc , sno_hproyectopersonal.codproy, scg_cuentas.sc_cuenta ".
				" ORDER BY codper, codproy "; 		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_conceptos_scg_proyecto_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$li_montoparcial=$row["montoparcial"];
				$li_total=$row["total"];
				$ls_cuenta=$row["cuenta"];
				$ls_operacion=$row["operacion"];
				$li_pordiames=$row["pordiames"];
				if($ls_codper!=$ls_codant)
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,2)!=round($li_totalant,2))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("operacion",$ls_operacionant);
							$this->DS->insertRow("cuenta",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$row["montoparcial"];
					$li_montoparcial=round($row["montoparcial"],2);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codant=$ls_codper;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$li_totalant=$li_total;
				}
				$this->DS->insertRow("operacion",$ls_operacion);
				$this->DS->insertRow("cuenta",$ls_cuenta);
				$this->DS->insertRow("total",$li_montoparcial);
			}
			if((number_format($li_acumulado,2,".","")!=number_format($li_totalant,2,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("operacion",$ls_operacionant);
				$this->DS->insertRow("cuenta",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_conceptos_scg_proyecto_int
	//------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_insert_conceptos_scg_proyectos_int($as_codcom,$as_operacionnomina,$as_codpro,$as_codben,$as_tipodestino,
                                                  $as_descripcion,$as_cuentapasivo,$ai_genrecdoc,$ai_tipdocnom,$ai_gennotdeb,
												  $ai_genvou)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_conceptos_scg_proyectos 
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_operacionnomina  //  Operación de la contabilización
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   as_cuentapasivo  //  cuenta pasivo
		//	    		   ai_genrecdoc  //  Generar recepción de documento
		//	    		   ai_tipdocnom  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 17/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_tipnom="N";
		$this->DS->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
		$li_totrow=$this->DS->getRowCount("cuenta");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cuenta=$this->DS->data["cuenta"][$li_i];
			$ls_operacion=$this->DS->data["operacion"][$li_i];
			$li_total=abs(round($this->DS->data["total"][$li_i],2));
			$ls_codconc="0000000001";
			$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
			if($li_total>0)
			{
				$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,
				                                                    $as_descripcion, $ls_cuenta,$ls_operacion,$li_total,
																	$ls_tipnom,$ls_codconc,$ai_genrecdoc,$ai_tipdocnom,
																	$ai_gennotdeb,$ai_genvou,$ls_codcomapo,'','');
			}
		}
		$this->DS->reset_ds();
		return  $lb_valido;    
	}// end function uf_insert_conceptos_scg_proyectos_int
	//-----------------------------------------------------------------------------------------------------------------------------------

	  ///---------------------------------------------------------------------------------------------------------------------------------
     function uf_load_aportes_scg_normales_int($as_operacionaporte)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_scg_normales_int 
		//	    Arguments: as_operacionaporte  //  operación del aporte
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Jennifer Rivero 
	    // Fecha Creación: 17/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		//se buscan las cuetas de intercompañias que van por el debe
		    $ls_sql=" SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion,              ".
					"	     sum(abs(sno_hsalida.valsal)) AS total, sno_hconcepto.codprov,   ".
					"	     sno_hconcepto.cedben, sno_hconcepto.codconc                     ".
					"    FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,".
					"         spg_cuentas, scg_cuentas, spg_ep1       ".
					" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
			  	    "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				    "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				    "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"	AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4') ".
					"	AND sno_hconcepto.intprocon = '1'  ".
					"	AND sno_hconcepto.conprocon = '0'  ".
					"	AND spg_cuentas.status = 'C'       ".
					"	AND spg_ep1.estint = 1             ".
					"	AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 ".
					"	AND spg_ep1.estcla = sno_hconcepto.estcla             ".
					"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp   ".
					"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom   ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur   ".
					"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"	AND sno_hpersonalnomina.codper = sno_hsalida.codper   ".
					"	AND sno_hsalida.codemp = sno_hconcepto.codemp         ".
					"	AND sno_hsalida.codnom = sno_hconcepto.codnom         ".
					"	AND sno_hsalida.anocur = sno_hconcepto.anocur         ".
					"	AND sno_hsalida.codperi = sno_hconcepto.codperi       ".
					"	AND sno_hsalida.codconc = sno_hconcepto.codconc       ".
					"	AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp  ". 
					"	AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom  ".
					"	AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur  ".
					"	AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"	AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  ".
					"	AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  ".
					"	AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  ".
					"	AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm  ".
					"	AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  ".
					"	AND spg_cuentas.codemp = sno_hconcepto.codemp                   ".
					"	AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon         ".
					"	AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta              ".
					"	AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1 ".
					"	AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2 ".
					"	AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3 ".
					"	AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4 ".
					"	AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5 ".
					"	AND sno_hconcepto.estcla=spg_cuentas.estcla                        ".
					"  GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben ".
					"     UNION        ".
					"  SELECT scg_cuentas.sc_cuenta AS cuenta, 'D' AS operacion, sum(abs(sno_hsalida.valsal)) AS total, ".
					"		  sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc ".
					"    FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, ".
					"         spg_cuentas, scg_cuentas, spg_ep1       ".                           
				    " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				    "   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				    "   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				    "   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					"	AND(sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4') ".
					"	AND sno_hconcepto.intprocon = '0' ".
					"	AND sno_hconcepto.conprocon= '0'  ".
					"	AND spg_cuentas.status = 'C'      ".
					"	AND spg_ep1.estint = 1            ".
					"	AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 ".
					"	AND sno_hunidadadmin.estcla = spg_ep1.estcla              ".
					"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp       ".
					"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom       ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur       ".
					"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi     ".
					"	AND sno_hpersonalnomina.codper = sno_hsalida.codper       ".
					"	AND sno_hsalida.codemp = sno_hconcepto.codemp             ".
					"	AND sno_hsalida.codnom = sno_hconcepto.codnom             ".
					"	AND sno_hsalida.anocur = sno_hconcepto.anocur             ".
					"	AND sno_hsalida.codperi = sno_hconcepto.codperi           ".
					"	AND sno_hsalida.codconc = sno_hconcepto.codconc           ". 
					"	AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp  ".
					"	AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom  ".
					"	AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur  ".
					"	AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					"	AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					"	AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm  ".
					"	AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm  ".
					"	AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm  ".
					"	AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm  ".
					"	AND spg_cuentas.codemp = sno_hconcepto.codemp                   ".
					"	AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon         ".
					"	AND spg_cuentas.sc_cuenta  = scg_cuentas.sc_cuenta              ".
					"	AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
					"	AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2".
					"	AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3".
					"	AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4".
					"	AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5".
					"	AND sno_hunidadadmin.estcla=spg_cuentas.estcla                              ".
					" GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, ".
					"		   sno_hconcepto.cedben                                                 ";
				
				//Buscamos las cuentas de intercompañias que van por el haber
				
				$ls_sql=$ls_sql." UNION ".
				       " SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion,              ".
					   "	    sum(abs(sno_hsalida.valsal)) AS total, sno_hconcepto.codprov,   ".
					   "	    sno_hconcepto.cedben, sno_hconcepto.codconc                     ".
					   "   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, ".
					   "        spg_cuentas, scg_cuentas, spg_ep1         ".
					   "  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				       "    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				       "    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				       "    AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					   "    AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4') ".
					   "    AND sno_hconcepto.intprocon = '1' ".
					   "	AND sno_hconcepto.conprocon = '0' ".
					   "	AND spg_cuentas.status = 'C'      ".
					   "	AND spg_ep1.estint = 1            ".
					   "	AND sno_hconcepto.codestpro1 = spg_ep1.codestpro1 ".
					   "	AND spg_ep1.estcla = sno_hconcepto.estcla          ".
					   "	AND spg_ep1.sc_cuenta = spg_cuentas.scgctaint      ".
					   "	AND spg_cuentas.scgctaint = scg_cuentas.sc_cuenta  ".
					   "	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					   "	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					   "	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					   "	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					   "	AND sno_hpersonalnomina.codper = sno_hsalida.codper   ".
					   "	AND sno_hsalida.codemp = sno_hconcepto.codemp         ".
					   "	AND sno_hsalida.codnom = sno_hconcepto.codnom         ".
					   "	AND sno_hsalida.anocur = sno_hconcepto.anocur         ". 
					   "	AND sno_hsalida.codperi = sno_hconcepto.codperi       ".
					   "	AND sno_hsalida.codconc = sno_hconcepto.codconc       ".
					   "	AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ". 
					   "	AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
					   "	AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					   "	AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi  ".
					   "	AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  ".
					   "	AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        ".
					   "	AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm        ".
					   "	AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm        ".
					   "	AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm        ".
					   "	AND spg_cuentas.codemp = sno_hconcepto.codemp                         ".
					   "	AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon               ".
					   "	AND sno_hconcepto.codestpro1 = spg_cuentas.codestpro1      ".
					   "	AND sno_hconcepto.codestpro2 = spg_cuentas.codestpro2     ".
					   "	AND sno_hconcepto.codestpro3 = spg_cuentas.codestpro3     ".
					   "	AND sno_hconcepto.codestpro4 = spg_cuentas.codestpro4     ".
					   "	AND sno_hconcepto.codestpro5 = spg_cuentas.codestpro5    ".
					   "	AND sno_hconcepto.estcla = spg_cuentas.estcla    ".
					   "  GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, sno_hconcepto.cedben ".
					   "	UNION    ".
					   " SELECT scg_cuentas.sc_cuenta AS cuenta, 'H' AS operacion, sum(abs(sno_hsalida.valsal)) AS total, ".
					   "		sno_hconcepto.codprov, sno_hconcepto.cedben, sno_hconcepto.codconc                        ".
					   "   FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto,                        ".
					   "        spg_cuentas, scg_cuentas, spg_ep1         ".
					   "  WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				       "    AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				       "    AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				       "    AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
					   "    AND(sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')  ".
					   "	AND sno_hconcepto.intprocon = '0'   ".
					   "	AND sno_hconcepto.conprocon= '0'    ".
					   "	AND spg_cuentas.status = 'C'        ".
					   "	AND spg_ep1.estint = 1              ".
					   "	AND sno_hunidadadmin.codestpro1 = spg_ep1.codestpro1 ".
					   "	AND sno_hunidadadmin.estcla = spg_ep1.estcla                          ".
					   "	AND spg_ep1.sc_cuenta = spg_cuentas.scgctaint                         ".
					   "	AND spg_cuentas.scgctaint = scg_cuentas.sc_cuenta                     ".
					   "	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp                   ".
					   "	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom                   ".
					   " 	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur                   ".
					   "	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi                 ".
					   "	AND sno_hpersonalnomina.codper = sno_hsalida.codper                   ".
					   "	AND sno_hsalida.codemp = sno_hconcepto.codemp                         ".
					   "	AND sno_hsalida.codnom = sno_hconcepto.codnom                         ".
					   "	AND sno_hsalida.anocur = sno_hconcepto.anocur                         ".
					   "	AND sno_hsalida.codperi = sno_hconcepto.codperi                       ".
					   "	AND sno_hsalida.codconc = sno_hconcepto.codconc                       ".
					   "	AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp              ".
					   "	AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom              ".
					   "	AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur              ".
					   "	AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi            ".
					   "	AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm  ".
					   "	AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm        ".
					   "	AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm        ".
					   "	AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm        ".
					   "	AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm        ".
					   "	AND spg_cuentas.codemp = sno_hconcepto.codemp                         ".
					   "	AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon               ".
					   "	AND sno_hunidadadmin.codestpro1 = spg_cuentas.codestpro1  ".
					   "	AND sno_hunidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
					   "	AND sno_hunidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
					   "	AND sno_hunidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
					   "	AND sno_hunidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
					   "	AND sno_hunidadadmin.estcla = spg_cuentas.estcla ".
					   "  GROUP BY sno_hconcepto.codconc, scg_cuentas.sc_cuenta, sno_hconcepto.codprov, ".
					   "   	       sno_hconcepto.cedben                                                 ";
		
			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_aportes_scg_normales_int ERROR->"
			                            .$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_scg_normales_int //-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_load_aportes_scg_proyecto_int()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_aportes_scg_proyecto_int
		//	    Arguments:
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos normales
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 17/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((sum(abs(sno_hsalida.valsal))*MAX(sno_hproyectopersonal.pordiames)) AS FLOAT),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos que se 
		// integran directamente con presupuesto estas van por el debe de contabilidad
		$ls_sql="SELECT scg_cuentas.sc_cuenta, CAST('D' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
				"		".$ls_cadena." AS montoparcial, MAX(sno_hconcepto.codprov) AS codprov, MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, ".
				"		sno_hproyectopersonal.codper, sno_hproyecto.codproy, MAX(sno_hproyectopersonal.pordiames) as pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 1 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND spg_ep1.estint = 0       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla  ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyecto.codproy, scg_cuentas.sc_cuenta ";				
			//buscamos las cuentas contables de intercompañias
			$ls_sql=$ls_sql."   UNION   ".
			    "SELECT scg_cuentas.sc_cuenta, CAST('H' AS char(1)) as operacion, sum(abs(sno_hsalida.valsal)) as total, ".
				"		".$ls_cadena." AS montoparcial, MAX(sno_hconcepto.codprov) AS codprov, MAX(sno_hconcepto.cedben) AS cedben, sno_hconcepto.codconc, ".
				"		sno_hproyectopersonal.codper, sno_hproyecto.codproy, MAX(sno_hproyectopersonal.pordiames) as pordiames ".
				"  FROM sno_hproyectopersonal, sno_hproyecto, sno_hsalida, sno_hconcepto, spg_cuentas, scg_cuentas, spg_ep1 ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_hsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND spg_ep1.estint = 1       ".
				"   AND sno_hproyecto.codestpro1 = spg_ep1.codestpro1 ".
				"   AND spg_ep1.estcla = sno_hproyecto.estcla             ".
				"   AND spg_ep1.sc_cuenta= spg_cuentas.scgctaint          ". 
                "   AND spg_cuentas.scgctaint  = scg_cuentas.sc_cuenta    ".
				"   AND sno_hproyectopersonal.codemp = sno_hsalida.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hsalida.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hsalida.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hsalida.codperi ".
				"   AND sno_hproyectopersonal.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hproyectopersonal.codemp = sno_hproyecto.codemp ".
				"   AND sno_hproyectopersonal.codnom = sno_hproyecto.codnom ".
				"   AND sno_hproyectopersonal.anocur = sno_hproyecto.anocur ".
				"   AND sno_hproyectopersonal.codperi = sno_hproyecto.codperi ".
				"   AND sno_hproyectopersonal.codproy = sno_hproyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_hconcepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_hconcepto.cueprepatcon ".
				"   AND sno_hproyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_hproyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_hproyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_hproyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_hproyecto.codestpro5 = spg_cuentas.codestpro5 ".
				" GROUP BY sno_hproyectopersonal.codper, sno_hconcepto.codconc, sno_hproyecto.codproy, scg_cuentas.sc_cuenta ".
				" ORDER BY codper, codconc, codproy, sc_cuenta ";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Ajustar Contabilización MÉTODO->uf_load_aportes_scg_proyecto_dt_int ERROR->"
			                            .$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			$ls_codconcant="";
			$ls_codprovant="";
			$ls_cedbenant="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codper=$row["codper"];
				$li_montoparcial=$row["montoparcial"];
				$li_total=$row["total"];
				$ls_cuenta=$row["sc_cuenta"];
				$ls_operacion=$row["operacion"];
				$ls_codprov=$row["codprov"];
				$ls_cedben=$row["cedben"];
				$ls_codconc=$row["codconc"];
				$li_pordiames=$row["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_codconc!=$ls_codconcant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,2)!=round($li_totalant,2))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),2);
							$this->DS->insertRow("operacion",$ls_operacionant);
							$this->DS->insertRow("cuenta",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("codprov",$ls_codprovant);
							$this->DS->insertRow("cedben",$ls_cedbenant);
							$this->DS->insertRow("codconc",$ls_codconcant);
						}
						$li_acumulado=0;
					}
					$li_acumulado=$li_acumulado+$row["montoparcial"];
					$li_montoparcial=round($row["montoparcial"],2);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codant=$ls_codper;
					$ls_codconcant=$ls_codconc;
					$ls_codprovant=$ls_codprov;
					$ls_cedbenant=$ls_cedben;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$li_totalant=$li_total;
					$ls_codconcant=$ls_codconc;
					$ls_codprovant=$ls_codprov;
					$ls_cedbenant=$ls_cedben;
				}
				$this->DS->insertRow("operacion",$ls_operacion);
				$this->DS->insertRow("cuenta",$ls_cuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("codprov",$ls_codprov);
				$this->DS->insertRow("cedben",$ls_cedben);
				$this->DS->insertRow("codconc",$ls_codconc);
			}
			if((number_format($li_acumulado,2,".","")!=number_format($li_totalant,2,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),2);
				$this->DS->insertRow("operacion",$ls_operacionant);
				$this->DS->insertRow("cuenta",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("codprov",$ls_codprovant);
				$this->DS->insertRow("cedben",$ls_cedbenant);
				$this->DS->insertRow("codconc",$ls_codconcant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_load_aportes_scg_proyecto_int
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
     function uf_insert_aportes_scg_proyectos_int($as_codcom,$as_codpro,$as_codben,$as_tipodestino,$as_descripcion,
	                                              $ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$as_operacionaporte)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_aportes_scg_proyectos_int  
		//	    Arguments: as_codcom  //  Código de Comprobante
		//	    		   as_codpro  //  codigo del proveedor
		//	    		   as_codben  //  codigo del beneficiario
		//	    		   as_tipodestino  //  Tipo de destino de contabiliación
		//	    		   as_descripcion  //  descripción del comprobante
		//	    		   ai_genrecapo  //  Generar recepción de documento
		//	    		   ai_tipdocapo  //  Generar Tipo de documento
		//	    		   ai_gennotdeb  //  generar nota de débito
		//	    		   ai_genvou  //  generar número de voucher
		//	    		   as_operacionaporte  //  Operación con que se va a contabilizar los aportes
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Función que se encarga de procesar la data para la contabilización de los conceptos
	    //     Creado por: Ing. Jennifer Rivero
	    // Fecha Creación: 17/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_tipnom="A";
		$this->DS->group_by(array('0'=>'codconc','1'=>'cuenta','2'=>'operacion'),array('0'=>'total'),array('0'=>'codconc','1'=>'cuenta','2'=>'operacion'));
		$li_totrow=$this->DS->getRowCount("cuenta");
		for($li_i=1;(($li_i<=$li_totrow)&&($lb_valido));$li_i++)
		{
			$ls_cuenta=$this->DS->data["cuenta"][$li_i];
			$ls_operacion=$this->DS->data["operacion"][$li_i];
			$li_total=abs(round($this->DS->data["total"][$li_i],2));
			$ls_codpro=$this->DS->data["codprov"][$li_i];
			$ls_cedben=$this->DS->data["cedben"][$li_i];
			$ls_codconc=$this->DS->data["codconc"][$li_i];
			$ls_tipodestino="";
			if($ls_codpro=="----------")
			{
				$ls_tipodestino="B";
			}
			if($ls_cedben=="----------")
			{
				$ls_tipodestino="P";
			}
			$ls_codcomapo=substr($ls_codconc,2,8).$this->ls_peractnom.$this->ls_codnom;
			$lb_valido=$this->uf_insert_contabilizacion_scg_int($as_codcom,$ls_codpro,$ls_cedben,$ls_tipodestino,$as_descripcion,
															$ls_cuenta,$ls_operacion,$li_total,$ls_tipnom,$ls_codconc,
															$ai_genrecapo,$ai_tipdocapo,$ai_gennotdeb,$ai_genvou,$ls_codcomapo,
															'','');
		}
		$this->DS->reset_ds();
		return $lb_valido;    
	}// end function uf_insert_aportes_scg_proyectos_int
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>
