<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_cxp_c_aprobacionsolicitudpago
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	private $io_conexion;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_cxp_c_aprobacionsolicitudpago
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion = $io_include->uf_conectar();
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once("class_funciones_cxp.php");
		$this->io_cxp= new class_funciones_cxp();
	}// end function sigesp_sep_c_aprobacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($this->io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_solicitudes($as_numsol,$ad_fecemides,$ad_fecemihas,$as_tipproben,$as_proben,$as_tipooperacion,$as_repcon='0')
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_solicitudes
		//		   Access: public
		//		 Argument: as_numsol        // Numero de la solicitud de ejecucion presupuestaria
		//                 ad_fecemides     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecemihas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipproben     // tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes de ordenes de pago a aprobar o reversar aprobacion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cadena = $this->io_conexion->Concat('nombene',"' '",'apebene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1' && $as_repcon=='0') 
		{
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ".
							" AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		
		//FILTRO RECEPCIONES CONTABLE
		if($as_repcon=='1') {
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd, cxp_documento";
			$ls_filtroest = " AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
							" AND cxp_rd.codtipdoc = cxp_documento.codtipdoc AND cxp_documento.estcon=1 AND cxp_documento.estpre=4";
		}
		//FILTRO RECEPCIONES CONTABLE
		$ls_sql="SELECT DISTINCT cxp_solicitudes.numsol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,".
				"       cxp_solicitudes.estaprosol,cxp_solicitudes.fecemisol,".
				"       (CASE WHEN cxp_solicitudes.tipproben='B' THEN (SELECT ".$ls_cadena." ".
				"                                                        FROM rpc_beneficiario".
				"                                                       WHERE cxp_solicitudes.codemp=rpc_beneficiario.codemp".
				"                                                         AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene)".
				"             WHEN cxp_solicitudes.tipproben='P' THEN (SELECT nompro".
				"                                                        FROM rpc_proveedor".
				"                                                       WHERE cxp_solicitudes.codemp=rpc_proveedor.codemp".
				"                                                         AND cxp_solicitudes.cod_pro=rpc_proveedor.cod_pro)".
				"                                                ELSE 'NINGUNO'".
				"         END) AS nombre".
				"  FROM cxp_solicitudes {$ls_filtrofrom} ".
				" WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."'".
				"   AND cxp_solicitudes.numsol LIKE '".$as_numsol."' ".
				"   AND cxp_solicitudes.fecemisol >= '".$ad_fecemides."' ".
				"   AND cxp_solicitudes.fecemisol <= '".$ad_fecemihas."' ".
				"   AND cxp_solicitudes.estprosol='E'".
				"   AND cxp_solicitudes.estaprosol='".$as_tipooperacion."'";
		if($as_tipproben=="B")
		{
			$ls_sql= $ls_sql." AND cxp_solicitudes.ced_bene LIKE '".$as_proben."'";
		}
		else
		{
			$ls_sql= $ls_sql." AND cxp_solicitudes.cod_pro LIKE'".$as_proben."'";
		}
		$ls_sql= $ls_sql.$ls_filtroest." ORDER BY cxp_solicitudes.numsol ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_estatus_solicitud($as_numsol,$as_estsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numsol ".
				"  FROM cxp_solicitudes ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."' ".
				"   AND estaprosol=".$as_estsol."";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_validar_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_solicitud($as_numsol,$as_estsol,$ad_fecaprosol,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol    //  Número de Solicitud
		//                 as_estsol    //  Estatus en que se desea colocar la solicitud
		//                 ad_fecaprosol //  Fecha de aprobacion de la solicitud
		//                 aa_seguridad //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecaprosol,$this->ls_codemp);
		if (!$lb_valido)
		{
			$this->io_mensajes->message($this->io_fecha->is_msg_error);           
			return false;
		}
		$ls_usuario=$_SESSION["la_logusr"];
		if($as_estsol==0)
		{
			$ad_fecaprsep="1900-01-01";
			$ls_usuario="";
		}
		$ad_fecaprosol=$this->io_funciones->uf_convertirdatetobd($ad_fecaprosol);
		$ls_sql="UPDATE cxp_solicitudes ".
				"   SET estaprosol = ".$as_estsol.", ".
				"       fecaprosol = '".$ad_fecaprosol."', ".
				"		usuaprosol = '".$ls_usuario."' ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND numsol = '".$as_numsol."' ";
		$this->io_sql->begin_transaction();				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_update_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			if($as_estsol==1)
			{
				$ls_descripcion ="Aprobó la Solicitud de Pago <b>".$as_numsol."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			else
			{
				$ls_descripcion ="Reversó la Aprobacion de la Solicitud de Pago <b>".$as_numsol."</b> Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$this->ls_supervisor=$_SESSION["la_empresa"]["envcorsup"];
			if($this->ls_supervisor!=0)
			{
				if($as_estsol==1)
				{
					$ls_fromname="Aprobación de Solicitud de Pago";
				}
				else
				{
					$ls_fromname="Reverso de Aprobación de Solicitud de Pago";
				}
				$ls_bodyenv="Se le envia la notificación de actualización en el modulo de CXP, se actualizó la solicitud de pago  N°.. ";
				$ls_nomper=$_SESSION["la_nomusu"];
				$lb_valido_3= $this->io_seguridad->uf_envio_correo_activo($ls_fromname,$as_numsol,$ls_bodyenv,$ls_nomper);
			}
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			if($lb_valido)
			{
				$this->io_sql->commit();
			}
			else
			{
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_update_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_solicitudes($as_numsol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_solicitudes
		//		   Access: public
		//		 Argument: as_numsol        // Numero de la solicitud de orden de pago
		//	  Description: Función que verifica que una solicitud este en estatus de registro
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numsol".
				"  FROM cxp_solicitudes".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numsol = '".$as_numsol."'".
				"   AND estprosol = 'E' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_validar_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_recepciones($as_numsol,$lb_imprimir)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 02/05/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_imprimir=true;
		$lb_valido=true;
		$ls_sql="SELECT numrecdoc,codtipdoc,cod_pro,ced_bene ".
				"  FROM cxp_dt_solicitudes ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numsol='".$as_numsol."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_estatus_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_numrecdoc=$rs_data->fields["numrecdoc"];
				$ls_codtipdoc=$rs_data->fields["codtipdoc"];
				$ls_codpro=$rs_data->fields["cod_pro"];
				$ls_cedbene=$rs_data->fields["ced_bene"];
				$lb_cierre=$this->uf_verificar_cierre($ls_numrecdoc,$ls_codtipdoc,$ls_cedbene,$ls_codpro);
				if($lb_cierre)
				{
					$lb_imprimir=false;
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["lb_imprimir"]=$lb_imprimir;
		return $arrResultado;
	}// end function uf_validar_estatus_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_verificar_cierre($as_numrecdoc,$as_codtipdoc,$as_cedbene,$as_codpro)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_cierre
		//		   Access: private
		//	    Arguments: as_numrecdoc  // Número de Recepcion de Documentos
		//				   as_codtipdoc  // Codigo de tipo de documento
		//				   as_cedbene    // Cedula de Beneficiario
		//				   as_codpro     // Código Proveedor
		//                 ad_fecemisol  // Fecha de emision de la solicitud
		//                 as_estatus    // Estatus del registro de R.D.
		//	  Description: Función que verifica si existe un registro en el historico de la recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 01/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT cxp_rd.numrecdoc,".
				"		(SELECT count(cxp_rd_spg.numrecdoc) ".
				"		   FROM cxp_rd_spg ".
				"		  WHERE cxp_rd.codemp=cxp_rd_spg.codemp ".
				"			AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc ".
				"			AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc ".
				"			AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro".
				"			AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) as rowspg,".
				"		(SELECT count(cxp_rd_scg.numrecdoc) ".
				"		   FROM cxp_rd_scg ".
				"		  WHERE cxp_rd.codemp=cxp_rd_scg.codemp ".
				"			AND cxp_rd.numrecdoc=cxp_rd_scg.numrecdoc ".
				"			AND cxp_rd.codtipdoc=cxp_rd_scg.codtipdoc ".
				"			AND cxp_rd.cod_pro=cxp_rd_scg.cod_pro".
				"			AND cxp_rd.ced_bene=cxp_rd_scg.ced_bene) as rowscg ".
				"  FROM cxp_rd".
				" WHERE cxp_rd.codemp= '".$this->ls_codemp."'".
				"   AND cxp_rd.numrecdoc= '".$as_numrecdoc."' ".
				"   AND cxp_rd.codtipdoc= '".$as_codtipdoc."' ".
				"   AND cxp_rd.cod_pro= '".$as_codpro."' ".
				"   AND cxp_rd.ced_bene='".$as_cedbene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_verificar_cierre ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ls_rowspg=$rs_data->fields["rowspg"];
				$ls_rowscg=$rs_data->fields["rowscg"];
				if($ls_rowspg>=1)
				{
					$arrResultado=$this->io_cxp->uf_verificar_cierre_spg("../../",$ls_estciespg);
					$ls_estciespg=$arrResultado["as_estciespg"];
					if($ls_estciespg=="1")
					{
						$this->io_mensajes->message("Esta procesado el cierre presupuestario");
						$lb_valido= true;
					}
					
				}
				if($ls_rowscg>=1)
				{
					$arrResultado=$this->io_cxp->uf_verificar_cierre_scg("../../",$ls_estciescg);
					$ls_estciescg=$arrResultado["as_estciescg"];
					if($ls_estciescg=="1")
					{
						$this->io_mensajes->message("Esta procesado el cierre contable");
						$lb_valido= true;
					}
					
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_verificar_cierre
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas($as_numsol)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_cuentas
		//		   Access: private
		//		 Argument: as_numsol // Número de solicitud
		//	  Description: Función que busca que las cuentas presupuestarias estén en la programática seleccionada
		//				   de ser asi puede aprobar la solicitud de pago de lo contrario no la apruebas
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 17/03/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		require_once("../shared/class_folder/class_sigesp_int.php");
		require_once("../shared/class_folder/class_sigesp_int_int.php");
		require_once("../shared/class_folder/class_sigesp_int_scg.php");
		require_once("../shared/class_folder/class_sigesp_int_spg.php");
		$io_int_spg=new class_sigesp_int_spg();

		$ls_sql="SELECT SUBSTR(cxp_rd_spg.codestpro,1,25) as codestpro1, SUBSTR(cxp_rd_spg.codestpro,26,25) as codestpro2, SUBSTR(cxp_rd_spg.codestpro,51,25) as codestpro3, ".
				"		SUBSTR(cxp_rd_spg.codestpro,76,25) as codestpro4, SUBSTR(cxp_rd_spg.codestpro,101,25) as codestpro5, cxp_rd_spg.estcla, ".
				"		cxp_rd_spg.spg_cuenta, cxp_rd_spg.monto, cxp_solicitudes.fecemisol, cxp_documento.estpre, cxp_documento.estcon, ".
				"		(SELECT COUNT(codemp) ".
				"		   FROM spg_cuentas ".
				"		  WHERE spg_cuentas.codemp = cxp_rd_spg.codemp ".
				"			AND spg_cuentas.codestpro1 = SUBSTR(cxp_rd_spg.codestpro,1,25) ".
				"		    AND spg_cuentas.codestpro2 = SUBSTR(cxp_rd_spg.codestpro,26,25) ".
				"		    AND spg_cuentas.codestpro3 = SUBSTR(cxp_rd_spg.codestpro,51,25) ".
				"		    AND spg_cuentas.codestpro4 = SUBSTR(cxp_rd_spg.codestpro,76,25) ".
				"		    AND spg_cuentas.codestpro5 = SUBSTR(cxp_rd_spg.codestpro,101,25) ".
				"		    AND spg_cuentas.estcla = cxp_rd_spg.estcla ".
				"			AND spg_cuentas.spg_cuenta = cxp_rd_spg.spg_cuenta) AS existe ".		
				"  FROM cxp_solicitudes  ".
				" INNER JOIN (cxp_dt_solicitudes  ".
				"       INNER JOIN (cxp_rd_spg ".
				"			  INNER JOIN cxp_documento ".
				"				 ON cxp_rd_spg.codemp='".$this->ls_codemp."' ".
				"               AND cxp_rd_spg.codtipdoc = cxp_documento.codtipdoc) ".
				"          ON cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"         AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"         AND cxp_dt_solicitudes.codemp=cxp_rd_spg.codemp".
				"         AND cxp_dt_solicitudes.numrecdoc=cxp_rd_spg.numrecdoc ".
				"         AND cxp_dt_solicitudes.codtipdoc=cxp_rd_spg.codtipdoc".
				"         AND cxp_dt_solicitudes.ced_bene=cxp_rd_spg.ced_bene ".
				"         AND cxp_dt_solicitudes.cod_pro=cxp_rd_spg.cod_pro) ".
				"    ON cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_cuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ls_estaprcxp=$_SESSION["la_empresa"]["estaprcxp"];
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$li_existe=$rs_data->fields["existe"];
				$ls_cuenta=$rs_data->fields["spg_cuenta"];
				$ls_codestpro1=substr($rs_data->fields["codestpro1"],(25-$_SESSION["la_empresa"]["loncodestpro1"]),$_SESSION["la_empresa"]["loncodestpro1"]);
				$ls_codestpro2=substr($rs_data->fields["codestpro2"],(25-$_SESSION["la_empresa"]["loncodestpro2"]),$_SESSION["la_empresa"]["loncodestpro2"]);
				$ls_codestpro3=substr($rs_data->fields["codestpro3"],(25-$_SESSION["la_empresa"]["loncodestpro3"]),$_SESSION["la_empresa"]["loncodestpro3"]);
				$ls_codestpro4=substr($rs_data->fields["codestpro4"],(25-$_SESSION["la_empresa"]["loncodestpro4"]),$_SESSION["la_empresa"]["loncodestpro4"]);
				$ls_codestpro5=substr($rs_data->fields["codestpro5"],(25-$_SESSION["la_empresa"]["loncodestpro5"]),$_SESSION["la_empresa"]["loncodestpro5"]);
				$ls_estcla=$rs_data->fields["estcla"];
				$_SESSION["fechacomprobante"]=$rs_data->fields["fecemisol"];
				if($li_existe>0)
				{
					$li_estpre=number_format($rs_data->fields["estpre"],0,"","");
					$li_estcon=number_format($rs_data->fields["estcon"],0,"","");
					if(($li_estpre==2)&&($li_estcon==1))
					{
						$ls_estprog[0]=$rs_data->fields["codestpro1"];
						$ls_estprog[1]=$rs_data->fields["codestpro2"];
						$ls_estprog[2]=$rs_data->fields["codestpro3"];
						$ls_estprog[3]=$rs_data->fields["codestpro4"];
						$ls_estprog[4]=$rs_data->fields["codestpro5"];
						$ls_estprog[5]=$rs_data->fields["estcla"];
						$ls_vali_nivel=$_SESSION["la_empresa"]["vali_nivel"];
						if($ls_vali_nivel==5)
						{
							$ls_formpre=str_replace("-","",$_SESSION["la_empresa"]["formpre"]);
							$ls_vali_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_formpre);
						}
						if($_SESSION["la_empresa"]["estvaldis"]==0)
						{
							$ls_vali_nivel=0;
						}
						$li_nivel=$io_int_spg->uf_spg_obtener_nivel($ls_cuenta);
						if ($li_nivel <= $ls_vali_nivel)
						{
							$ls_status="";
							$li_asignado=0;
							$li_aumento=0;
							$li_disminucion=0;
							$li_precomprometido=0;
							$li_comprometido=0;
							$li_causado=0;
							$li_pagado=0;
							$arrResultado="";
							$arrResultado=$io_int_spg->uf_spg_saldo_select($this->ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
															 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'ACTUAL');
							$ls_status = $arrResultado['as_status'];
							$li_asignado = $arrResultado['adec_asignado'];
							$li_aumento = $arrResultado['adec_aumento'];
							$li_disminucion = $arrResultado['adec_disminucion'];
							$li_precomprometido = $arrResultado['adec_precomprometido'];
							$li_comprometido = $arrResultado['adec_comprometido'];
							$li_causado = $arrResultado['adec_causado'];
							$li_pagado = $arrResultado['adec_pagado'];
							$lb_valido = $arrResultado['lb_valido'];
							$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
							if(round($rs_data->fields["monto"],2) > round($li_disponibilidad,2))
							{
								$li_monto=number_format($rs_data->fields["monto"],2,",",".");
								$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
								$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
								if($ls_estaprcxp!="1")
								{
									$lb_valido=false;
								}
							}
							elseif (round($rs_data->fields["monto"],2) == round($li_disponibilidad,2)){
								$this->io_mensajes->message("El saldo de la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." quedara en 0");
							}
							
							if($lb_valido)
							{
								$ls_status="";
								$li_asignado=0;
								$li_aumento=0;
								$li_disminucion=0;
								$li_precomprometido=0;
								$li_comprometido=0;
								$li_causado=0;
								$li_pagado=0;
								$arrResultado="";
								$arrResultado=$io_int_spg->uf_spg_saldo_select($this->ls_codemp,$ls_estprog,$ls_cuenta,$ls_status,$li_asignado,$li_aumento,$li_disminucion,
																 $li_precomprometido,$li_comprometido,$li_causado,$li_pagado,'COMPROBANTE');
								$ls_status = $arrResultado['as_status'];
								$li_asignado = $arrResultado['adec_asignado'];
								$li_aumento = $arrResultado['adec_aumento'];
								$li_disminucion = $arrResultado['adec_disminucion'];
								$li_precomprometido = $arrResultado['adec_precomprometido'];
								$li_comprometido = $arrResultado['adec_comprometido'];
								$li_causado = $arrResultado['adec_causado'];
								$li_pagado = $arrResultado['adec_pagado'];
								$lb_valido = $arrResultado['lb_valido'];
								$li_disponibilidad=(($li_asignado + $li_aumento) - ( $li_disminucion + $li_comprometido + $li_precomprometido));
								if(round($rs_data->fields["monto"],2) > round($li_disponibilidad,2))
								{
									$li_monto=number_format($rs_data->fields["monto"],2,",",".");
									$li_disponibilidad=number_format($li_disponibilidad,2,",",".");
									$this->io_mensajes->message("No hay Disponibilidad en la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." Disponible=[".$li_disponibilidad."] Cuenta=[".$li_monto."]"); 
									if($ls_estaprcxp!="1")
									{
										$lb_valido=false;
									}
								}
								elseif (round($rs_data->fields["monto"],2) == round($li_disponibilidad,2)){
									$this->io_mensajes->message("El saldo de la cuenta ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla." ".$ls_cuenta." quedara en 0");
								}
							}				
						} 
					}	
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("La cuenta ".$ls_cuenta." No Existe en la Estructura ".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla.""); 
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_validar_cuentas
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel_aprobacion_usu($as_codusu,$as_codtipniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_codniv="";
		$ls_sql="SELECT codasiniv ".
				"  FROM sss_niv_usuarios ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codusu='".$as_codusu."' ".
				"   AND codtipniv='".$as_codtipniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php->uf_nivel_aprobacion_usu ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codniv=$row["codasiniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_codniv;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel_aprobacion_montohasta($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ai_monhas=0;
		$ls_sql="SELECT monnivhas ".
				"  FROM sigesp_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_monhas=$row["monnivhas"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ai_monhas;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nivel($as_codniv)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$as_nivel="";
		$ls_sql="SELECT codniv ".
				"  FROM sigesp_asig_nivel ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codasiniv='".$as_codniv."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_aprobacion_analisis_cotizacion.php-> ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_nivel=$row["codniv"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_nivel;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------


}
?>