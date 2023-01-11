<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_ins_c_docs_a_libcompra
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_ins_c_docs_a_libcompra
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011 								Fecha Última Modificación : 
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
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."/base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function sigesp_sep_c_aprobacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_ins_p_docs_a_libcompra.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fecha);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_estatus_recepcion($as_numrecdoc,$as_estsol,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_estatus_solicitud
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//				   as_estsol     //  Estatus de la Solicitud
		//				   as_codpro     //  Codigo de Proveedor
		//				   as_cedben     //  Codigo de Beneficiario
		//				   as_codtipdoc  //  Codigo de Tipo de Documento
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la recepcion de documentos
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numrecdoc ".
				"  FROM cxp_rd ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numrecdoc='".$as_numrecdoc."' ".
				"   AND cod_pro='".$as_codpro."' ".
				"   AND ced_bene='".$as_cedben."' ".
				"   AND codtipdoc='".$as_codtipdoc."' ".
				"   AND estlibcom=".$as_estsol."";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_estatus_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_validar_estatus_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_estatus_recepciones($as_numrecdoc,$as_estrd,$as_codpro,$as_cedben,$as_codtipdoc,$ad_fecaprord,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_estatus_recepciones
		//		   Access: private
		//	    Arguments: as_numrecdoc  //  Número de Recepcion de Documentos
		//                 as_estrd      //  Estatus en que se desea colocar la Recepcion de Documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//                 ad_fecaprord  //  Fecha de aprobacion de la Recepcion de Documentos
		//                 aa_seguridad  //  Arreglo que contiene informacion de seguridad
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que actualiza el estatus de aprobacion de la recepcion de documentos
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=$this->io_fecha->uf_valida_fecha_periodo($ad_fecaprord,$this->ls_codemp);
		if (!$lb_valido)
		{
			$this->io_mensajes->message($this->io_fecha->is_msg_error);           
			return false;
		}
		$ls_usuario=$_SESSION["la_logusr"];
		if($as_estrd==0)
		{
			$ad_fecaprsep="1900-01-01";
			$ls_usuario="";
		}
		$ad_fecaprord=$this->io_funciones->uf_convertirdatetobd($ad_fecaprord);
		$ls_sql="UPDATE cxp_rd ".
				"   SET estlibcom = ".$as_estrd." ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND numrecdoc = '".$as_numrecdoc."' ".
				"	AND cod_pro = '".$as_codpro."' ".
				"	AND ced_bene = '".$as_cedben."' ".
				"	AND codtipdoc = '".$as_codtipdoc."' ";
		$this->io_sql->begin_transaction();			
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_update_estatus_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			if($as_estrd==1)
			{
				$ls_descripcion ="Agrego la Recepcion de Documentos <b>".$as_numrecdoc."</b> al libro de compra, Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			else
			{
				$ls_descripcion ="Quito la Recepcion de Documentos <b>".$as_numrecdoc."</b> del libro de compra, Asociado a la Empresa <b>".$this->ls_codemp."<b>";
			}
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
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
	}// end function uf_update_estatus_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_recepciones($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_recepciones
		//		   Access: public
		//		 Argument: as_numrecdoc  // Numero de la recepcion de documentos
		//                 as_codpro     //  Codigo de Proveedor
		//                 as_cedben     //  Codigo de Beneficiario
		//                 as_codtipdoc  //  Codigo de Tipo de Documento
		//	  Description: Función que verifica que una recepcion de documentos este en estatus de registro
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numrecdoc".
				"  FROM cxp_rd".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND numrecdoc = '".$as_numrecdoc."'".
				"   AND cod_pro = '".$as_codpro."'".
				"   AND ced_bene = '".$as_cedben."'".
				"   AND codtipdoc = '".$as_codtipdoc."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_validar_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}// end function uf_validar_recepciones
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
			//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
			// Fecha Creación: 25/05/2011 								Fecha Última Modificación : 
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$lb_existe=true;
			$as_codniv="";
			$ls_sql="SELECT codniv ".
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
					$as_codniv=$row["codniv"];
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
			//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
			// Fecha Creación: 25/05/2011 								Fecha Última Modificación : 
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
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz 
		// Fecha Creación: 25/05/2011 								Fecha Última Modificación : 
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
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_recepciones($as_numrecdoc,$ad_fecregdes,$ad_fecreghas,$as_tipproben,$as_proben,$as_tipooperacion)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_numrecdoc        // Numero de la solicitud de ejecucion presupuestaria
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipproben     // tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las recepciones  a aprobar o reversar aprobacion
		//	   Creado Por: OFIMATICA DE VENEZUELA, C.A.  - Ing. Nelson Barraéz
		// Fecha Creación: 25/05/2011								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="CONCAT(nombene,' ',apebene)";
				break;
			case "MYSQLI":
				$ls_cadena="CONCAT(nombene,' ',apebene)";
				break;
			case "POSTGRES":
				$ls_cadena="nombene||' '||apebene";
				break;
			case "INFORMIX":
				$ls_cadena="nombene||' '||apebene";
				break;
		}
		$ls_sql="SELECT cxp_rd.numrecdoc,cxp_rd.fecregdoc,cxp_rd.estlibcom,cxp_rd.montotdoc,cxp_rd.tipproben,".
				"       cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.codtipdoc,".
				"       (CASE WHEN cxp_rd.tipproben='B' THEN (SELECT ".$ls_cadena." ".
				"                                               FROM rpc_beneficiario".
				"                                              WHERE cxp_rd.codemp=rpc_beneficiario.codemp".
				"                                                AND cxp_rd.ced_bene=rpc_beneficiario.ced_bene)".
				"             WHEN cxp_rd.tipproben='P' THEN (SELECT nompro".
				"                                               FROM rpc_proveedor".
				"                                              WHERE cxp_rd.codemp=rpc_proveedor.codemp".
				"                                                AND cxp_rd.cod_pro=rpc_proveedor.cod_pro)".
				"                                       ELSE 'NINGUNO'".
				"         END) AS nombre,".
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
				" WHERE cxp_rd.codemp = '".$this->ls_codemp."'".
				"   AND cxp_rd.numrecdoc LIKE '".$as_numrecdoc."' ".
				"   AND cxp_rd.fecregdoc >= '".$ad_fecregdes."' ".
				"   AND cxp_rd.fecregdoc <= '".$ad_fecreghas."' ".
				"   AND cxp_rd.estlibcom='".$as_tipooperacion."'";
		if($as_tipproben=="B")
		{
			$ls_sql= $ls_sql." AND cxp_rd.ced_bene LIKE '".$as_proben."'";
		}
		else
		{
			$ls_sql= $ls_sql." AND cxp_rd.cod_pro LIKE'".$as_proben."'";
		}
		$ls_sql= $ls_sql." ORDER BY cxp_rd.numrecdoc "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------


}
?>