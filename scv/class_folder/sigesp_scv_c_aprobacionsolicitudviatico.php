<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scv_c_aprobacionsolicitudviatico
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
		//	     Function: sigesp_scv_c_aprobacionsolicitudviatico
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 13/04/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
	    require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");		
		$this->io_fecha= new class_fecha();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		require_once($as_path."shared/class_folder/class_sigesp_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_scg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spi.php");
        $this->io_sigesp_int=new class_sigesp_int_int();
		$this->io_sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_sigesp_int_scg=new class_sigesp_int_scg();		
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
	}// end function sigesp_scv_c_anulacionsolicitud
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
	function uf_load_solicitudes($as_codsolvia,$ad_fecregdes,$ad_fecreghas,$as_tipooperacion)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_recepciones
		//		   Access: public
		//		 Argument: as_codsolvia     // Numero de Solicitud de Viaticos
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: Ing. María Beatriz Unda
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		
		$ls_sql="SELECT scv_solicitudviatico.codsolvia, scv_solicitudviatico.fecsolvia,scv_misiones.denmis, scv_misiones.denmis AS desrut".
				"  FROM scv_solicitudviatico,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='".$this->ls_codemp."'".
				"   AND estsolvia='".$as_tipooperacion."'".
				"   AND fecsolvia>='".$ad_fecregdes."'".
				"   AND fecsolvia<='".$ad_fecreghas."'".
				"   AND codsolvia like '".$as_codsolvia."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmis=scv_misiones.codmis".
				"  GROUP BY scv_solicitudviatico.codsolvia,scv_solicitudviatico.fecsolvia,scv_misiones.denmis ".
				"  ORDER BY scv_solicitudviatico.codsolvia";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_load_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_scv_select_origensolicitud($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_origensolicitud
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codpai".
				"  FROM scv_solicitudviatico,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmis=scv_misiones.codmis"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{ 
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_origensolicitud ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codpai=$row["codpai"];
				if($ls_codpai=='058')
				{
					$lb_valido=true;
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_solicitudviaticos

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_recepcion_documento_viatico($as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_recepcion_documento_viatico
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y generar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_datos_solicitud($as_codsolvia);
		$ls_descripcion="Calculo de Viaticos de la solicitud ".$as_codsolvia;
		$ls_codcom="SCV-".$this->io_funciones->uf_cerosizquierda($as_codsolvia,11);
		$lb_origen=true;
		$lb_tcomp=$this->uf_buscar_compromiso_solicitud($as_codsolvia);
		$lb_trecp=$this->uf_buscar_recepcion_solicitud($as_codsolvia);
		while((!$rs_data->EOF)&& $lb_valido)
		{
			$ls_fecregvia=$rs_data->fields["fecsolvia"];
			$li_monsolvia=$rs_data->fields["monsolvia"];
			$ls_codfuefin=$rs_data->fields["codfuefin"];
			$ls_codtipdoc=$rs_data->fields["codtipdoc"];
			$ls_obssolvia=$rs_data->fields["obssolvia"];
			$li_repcajchi=$rs_data->fields["repcajchi"];//Agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica			
			$ls_tipvia=$rs_data->fields["tipvia"];
			$ls_codcla=$rs_data->fields["codcla"];
			$ls_descripcion=$ls_descripcion.". ".$ls_obssolvia;
			$rs_datapersonal=$this->uf_load_personalviaticos($as_codsolvia);
			/*if($ls_tipvia==1)
			{
				$lb_origen=$this->uf_scv_select_origensolicitud($ls_codemp,$as_codsolvia);
			}*/
			while((!$rs_datapersonal->EOF)&& $lb_valido)
			{
				$li_monpervia=$rs_datapersonal->fields["monpervia"];
				$ls_codper=$rs_datapersonal->fields["codper"];
				$ls_cedula=$rs_datapersonal->fields["cedper"];
				switch ($ls_tipvia)
				{
					case "1":
						if ($lb_tcomp)//Tiene Compromiso
						{
							$lb_valido=$this->uf_procesar_compromiso($ls_codcom,$ls_fecregvia,$ls_obssolvia,$ls_cedula,$as_codsolvia,$ls_codper,$aa_seguridad);
						}
						if (($lb_trecp)&&($lb_valido))//Tiene Recepcion
						{
							if($lb_origen)
							{
								if($ls_cedula=="")
								{
									$ls_cedula=$ls_codper;
								}
								$lb_valido=$this->uf_scv_validar_beneficiario($ls_cedula);
								if(!$lb_valido)
								{
									$this->io_mensajes->message("El Beneficiario ".$ls_cedula." no existe.");
								}
								$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
								if($lb_valido)
								{
									$lb_valido=$this->uf_scv_validar_recepcion_documentos($ls_codcom,$ls_cedula,$ls_codtipdoc);
									if($lb_valido)
									{
										$lb_valido=$this->uf_scv_procesar_recepcion_documento($as_codsolvia,$ls_codcom,$ls_cedula,$ls_codtipdoc,
																								  $ls_descripcion,$ls_fecregvia,$li_monpervia,
																								  $ls_codfuefin,$ls_codrecdoc,$aa_seguridad,
																								  $li_repcajchi,$ls_codcla);//Variable $li_repcajchi agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica);
										if($lb_valido)
										{
											$lb_valido=$this->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											if($lb_valido)
											{
												$lb_valido=$this->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											}
										}
									}
									else
									{
										$this->io_mensajes->message("La Recepcion de Documentos ".$ls_codcom." ya esta Registrada.");
									}
								}
							}
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
					
					case "2":
						if ($lb_tcomp)//Tiene Compromiso
						{
							$lb_valido=$this->uf_procesar_compromiso($ls_codcom,$ls_fecregvia,$ls_obssolvia,$ls_cedula,$as_codsolvia,$ls_codper,$aa_seguridad);
						}
						if (($lb_trecp)&&($lb_valido))//Tiene Recepcion
						{
							if($lb_origen)
							{
								if($ls_cedula=="")
								{
									$ls_cedula=$ls_codper;
								}
								$lb_valido=$this->uf_scv_validar_beneficiario($ls_cedula);
								if(!$lb_valido)
								{
									$this->io_mensajes->message("El Beneficiario ".$ls_cedula." no existe.");
								}
								$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
								if($lb_valido)
								{
									$lb_valido=$this->uf_scv_validar_recepcion_documentos($ls_codcom,$ls_cedula,$ls_codtipdoc);
									if($lb_valido)
									{
										$lb_valido=$this->uf_scv_procesar_recepcion_documento($as_codsolvia,$ls_codcom,$ls_cedula,$ls_codtipdoc,
																								  $ls_descripcion,$ls_fecregvia,$li_monpervia,
																								  $ls_codfuefin,$ls_codrecdoc,$aa_seguridad,
																								  $li_repcajchi,$ls_codcla);//Variable $li_repcajchi agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica);
										if($lb_valido)
										{
											$lb_valido=$this->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											if($lb_valido)
											{
												$lb_valido=$this->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											}
										}
									}
									else
									{
										$this->io_mensajes->message("La Recepcion de Documentos ".$ls_codcom." ya esta Registrada.");
									}
								}
							}
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
					
					case "3":
						$lb_valido=$this->uf_procesar_compromiso($ls_codcom,$ls_fecregvia,$ls_obssolvia,$ls_cedula,$as_codsolvia,$ls_codper,$aa_seguridad);
						if(!$lb_valido)
						{
							return false;
						}
					break;
					
					case "4":
						if ($lb_tcomp)//Tiene Compromiso
						{
							$lb_valido=$this->uf_procesar_compromiso($ls_codcom,$ls_fecregvia,$ls_obssolvia,$ls_cedula,$as_codsolvia,$ls_codper,$aa_seguridad);
						}
						if (($lb_trecp)&&($lb_valido))//Tiene Recepcion
						{
							if($lb_origen)
							{
								if($ls_cedula=="")
								{
									$ls_cedula=$ls_codper;
								}
								$lb_valido=$this->uf_scv_validar_beneficiario($ls_cedula);
								if(!$lb_valido)
								{
									$this->io_mensajes->message("El Beneficiario ".$ls_cedula." no existe.");
								}
								$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
								if($lb_valido)
								{
									$lb_valido=$this->uf_scv_validar_recepcion_documentos($ls_codcom,$ls_cedula,$ls_codtipdoc);
									if($lb_valido)
									{
										$lb_valido=$this->uf_scv_procesar_recepcion_documento($as_codsolvia,$ls_codcom,$ls_cedula,$ls_codtipdoc,
																								  $ls_descripcion,$ls_fecregvia,$li_monpervia,
																								  $ls_codfuefin,$ls_codrecdoc,$aa_seguridad,
																								  $li_repcajchi,$ls_codcla);//Variable $li_repcajchi agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica);
										if($lb_valido)
										{
											$lb_valido=$this->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											if($lb_valido)
											{
												$lb_valido=$this->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											}
										}
									}
									else
									{
										$this->io_mensajes->message("La Recepcion de Documentos ".$ls_codcom." ya esta Registrada.");
									}
								}
							}
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
					
					case "5":
						if (($lb_trecp)&&($lb_valido))//Tiene Recepcion
						{
							if($lb_origen)
							{
								if($ls_cedula=="")
								{
									$ls_cedula=$ls_codper;
								}
								$lb_valido=$this->uf_scv_validar_beneficiario($ls_cedula);
								if(!$lb_valido)
								{
									$this->io_mensajes->message("El Beneficiario ".$ls_cedula." no existe.");
								}
								$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
								if($lb_valido)
								{
									$lb_valido=$this->uf_scv_validar_recepcion_documentos($ls_codcom,$ls_cedula,$ls_codtipdoc);
									if($lb_valido)
									{
										$lb_valido=$this->uf_scv_procesar_recepcion_documento($as_codsolvia,$ls_codcom,$ls_cedula,$ls_codtipdoc,
																								  $ls_descripcion,$ls_fecregvia,$li_monpervia,
																								  $ls_codfuefin,$ls_codrecdoc,$aa_seguridad,
																								  $li_repcajchi,$ls_codcla);//Variable $li_repcajchi agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica);
										if($lb_valido)
										{
											$lb_valido=$this->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											if($lb_valido)
											{
												$lb_valido=$this->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
											}
										}
									}
									else
									{
										$this->io_mensajes->message("La Recepcion de Documentos ".$ls_codcom." ya esta Registrada.");
									}
								}
							}
						}
						elseif ($lb_tcomp)//Tiene Compromiso
						{
							$lb_valido=$this->uf_procesar_compromiso($ls_codcom,$ls_fecregvia,$ls_obssolvia,$ls_cedula,$as_codsolvia,$ls_codper,$aa_seguridad);
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
				}
				//Anterior
				if($ls_tipvia=="-")
				{
					if($lb_origen)
					{
						if($ls_cedula=="")
						{
							$ls_cedula=$ls_codper;
						}
						$lb_valido=$this->uf_scv_validar_beneficiario($ls_cedula);
						if(!$lb_valido)
						{
							$this->io_mensajes->message("El Beneficiario ".$ls_cedula." no existe.");
						}
						$ls_codrecdoc=$this->io_keygen->uf_generar_numero_nuevo("CXP","cxp_rd","codrecdoc","CXPRCD",15,"","","");
						if($lb_valido)
						{
							$lb_valido=$this->uf_scv_validar_recepcion_documentos($ls_codcom,$ls_cedula,$ls_codtipdoc);
							if($lb_valido)
							{
								$lb_valido=$this->uf_scv_procesar_recepcion_documento($as_codsolvia,$ls_codcom,$ls_cedula,$ls_codtipdoc,
																						  $ls_descripcion,$ls_fecregvia,$li_monpervia,
																						  $ls_codfuefin,$ls_codrecdoc,$aa_seguridad,
																						  $li_repcajchi,$ls_codcla);//Variable $li_repcajchi agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica);
								if($lb_valido)
								{
									$lb_valido=$this->uf_insert_recepcion_documento_gasto($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
									if($lb_valido)
									{
										$lb_valido=$this->uf_insert_recepcion_documento_contable($ls_codcom,$ls_codtipdoc,$ls_cedula,$li_monpervia);
									}
								}
							}
							else
							{
								$this->io_mensajes->message("La Recepcion de Documentos ".$ls_codcom." ya esta Registrada.");
							}
						}
					}
				}//Anterior
				$rs_datapersonal->MoveNext();
			}
			$rs_data->MoveNext();
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_scv_update_solivitud_viaticos($as_codsolvia,"P",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_datos_solicitud($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_datos_solicitud
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codsolvia, fecsolvia, monsolvia, codfuefin, codtipdoc, obssolvia, repcajchi, tipvia, codcla ".
				"  FROM scv_solicitudviatico".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_select_datos_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
		
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_personalviaticos($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_datos_solicitud
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT codper, monpervia,".
				"       (SELECT cedper FROM sno_personal".
				"         WHERE scv_dt_personal.codemp=sno_personal.codemp".
				"           AND scv_dt_personal.codper=sno_personal.codper) AS cedper".
				"  FROM scv_dt_personal".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_load_personalviaticos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
		
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_validar_recepcion_documentos($as_codcom,$as_cedula,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_validar_recepcion_documentos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numrecdoc".
				"  FROM cxp_rd".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numrecdoc='".$as_codcom."'".
				"   AND codtipdoc='".$as_codtipdoc."'".
				"   AND ced_bene='".$as_cedula."'".
				"   AND cod_pro='----------'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_scv_validar_recepcion_documentos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=false;			
			}
		}
		return $lb_valido;
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_validar_beneficiario($as_cedula)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_validar_beneficiario
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT ced_bene".
				"  FROM rpc_beneficiario".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND ced_bene='".$as_cedula."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_scv_validar_beneficiario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;			
			}
		}
		return $lb_valido;
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_recepcion_documento($as_codsolvia,$as_comprobante,$as_cedbene,$as_codtipdoc,
												 $as_descripcion,$ad_fecha,$ai_monto,$as_codfuefin,$as_codrecdoc,$aa_seguridad,
												 $ai_repcajchi,$as_codcla)//Campo repcajchi agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_recepcion_documento
		//		   Access: private
		//	    Arguments: $as_codsolvia    // codigo de solicitud de viaticos
		//                 $as_comprobante  // Codigo de Comprobante
		//				   $as_cedbene 		// cedula de beneficiario
		//				   $as_codtipdoc	// codigo de tipo de documento
		//				   $as_descripcion	// descripcion del documento
		//				   $ad_fecha  		// Fecha de contabilización
		//				   $ad_fecha  		// Fecha de contabilización
		//                 $as_codfuefin    // Código de la fuente de financiamiento
		//				   $aa_seguridad    // Arreglo de las variables de seguridad
		//				   $ai_repcajchi    // Si coresponde a reposicion  de caja chica-Agregado por OFIMATICA DE VENEZUELA el 25-05-2011, para el manejo de viaticos por reposicion de caja chica		
		//	      Returns: $lb_valido True si se genero la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
        $ls_tipodestino= "B";			
		$ls_codpro= "----------";
		$ad_fecha= $this->io_funciones->uf_convertirdatetobd($ad_fecha);
		//Nota de OFIMATICA DE VENEZUELA se agrega a la consulta el campo repcajchi para determinar si la recepcion de documento generada por el viatico corresponde a una reposicion de caja chica
		$ls_sql="INSERT INTO cxp_rd (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,dencondoc,fecemidoc, fecregdoc, fecvendoc,".
 		        "                    montotdoc, mondeddoc,moncardoc,tipproben,numref,estprodoc,procede,estlibcom,estaprord,".
				"                    fecaprord,usuaprord,estimpmun,codcla,codfuefin,codrecdoc,repcajchi)".
				"     VALUES ('".$this->ls_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$as_cedbene."',".
				"             '".$ls_codpro."','".$as_descripcion."','".$ad_fecha."','".$ad_fecha."','".$ad_fecha."',
				"               .$ai_monto.",0,0,'".$ls_tipodestino."','".$as_comprobante."','R','SCVSOV',0,0,'1900-01-01','',0,'".$as_codcla."','".$as_codfuefin."','".$as_codrecdoc."',".$ai_repcajchi.")";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{  
			$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_scv_procesar_recepcion_documento_viatico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			$lb_valido=false;
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Generó la Recepción de Documento Solicitud de Viáticos <b>".$as_codsolvia."</b>, ".
							"Comprobante <b>".$as_comprobante."</b>";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											  $aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											  $aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$li_mondeddoc=0;
			$li_moncardoc=0;
			
		}
		return $lb_valido;
	}  // end function uf_scv_procesar_recepcion_documento_viatico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_gasto($as_comprobante,$as_codtipdoc,$as_cedbene,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_gasto
		//		   Access: private
		//	    Arguments: $as_comprobante // Código de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // Cédula del Beneficiario
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles presupuestario en la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SCVSOV";
		
		$ls_sql="SELECT codemp, codsolvia, codcom, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, ".
		        " spg_cuenta, operacion, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus,codfuefin ".
				"  FROM scv_dt_spg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codcom='".$as_comprobante."'".
				"   AND ced_bene='".$as_cedbene."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_codestpro=$row["codestpro1"].$row["codestpro2"].$row["codestpro3"].$row["codestpro4"].$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_spg_cuenta= $row["spg_cuenta"];
				$ls_documento=  $row["codcom"];								 
				$ls_cedbene=    $row["ced_bene"];								 
				$ls_codpro=     $row["cod_pro"];
				$ls_codfuefin=  $row["codfuefin"];								 
				$ls_monto=  $row["monto"];								 
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante($ls_documento);
				$ls_sql="INSERT INTO cxp_rd_spg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,codestpro,".
						"						 spg_cuenta,monto,estcla,codfuefin)".
						"     VALUES ('".$this->ls_codemp."','".$as_comprobante."','".$as_codtipdoc."',".
						"             '".$ls_cedbene."','".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_codestpro."',".
						"             '".$ls_spg_cuenta."',".$ls_monto.",'".$ls_estcla."','".$ls_codfuefin."')";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
           			$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_gasto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
				   	$lb_valido=false;
				   	break;
				}
				
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_gasto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_recepcion_documento_contable($as_comprobante,$as_codtipdoc,$as_cedbene,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_recepcion_documento_contable
		//		   Access: private
		//	    Arguments: $as_comprobante // Código de Comprobante
		//				   $as_codtipdoc   // Tipo de Documento
		//				   $as_cedbene     // Cédula del Beneficiario
		//				   $ai_monto       // monto del comprobante
		//	      Returns: $lb_valido True si se inserto los detalles contables en la recepción de documento correctamente
		//	  Description: Retorna un Booleano
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_procede="SCVSOV";
		$ls_sql="SELECT codemp, codsolvia, codcom, sc_cuenta, debhab, cod_pro, ced_bene, tipo_destino, descripcion, monto, estatus".
				"  FROM scv_dt_scg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codcom='".$as_comprobante."'".
				"   AND ced_bene='".$as_cedbene."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   
           	$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{           
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
			{
				$ls_sccuenta= $row["sc_cuenta"];
				$ls_debhab=     $row["debhab"];				
				$ls_documento=  $row["codcom"];								 
				$ls_cedbene=    $row["ced_bene"];								 
				$ls_codpro=     $row["cod_pro"];								 
				$ls_monto=  $row["monto"];								 
				$ls_documento= $this->io_sigesp_int->uf_fill_comprobante($ls_documento);
				$ls_sql="INSERT INTO cxp_rd_scg (codemp,numrecdoc,codtipdoc,ced_bene,cod_pro,procede_doc,numdoccom,debhab,".
						"						 sc_cuenta,monto)".
						"     VALUES ('".$this->ls_codemp."','".$as_comprobante."','".$as_codtipdoc."','".$ls_cedbene."',".
						"             '".$ls_codpro."','".$ls_procede."','".$ls_documento."','".$ls_debhab."',".
						"             '".$ls_sccuenta."',".$ls_monto.")";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
		           	$this->io_mensajes->message("CLASE->sigesp_scv_c_calcularviaticos MÉTODO->uf_insert_recepcion_documento_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
				    $lb_valido=false;
				    break;
				}
				
			} // end while
		}
		$this->io_sql->free_result($rs_data);	 
		return $lb_valido;
    } // end function uf_insert_recepcion_documento_contable
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_update_solivitud_viaticos($as_codsolvia,$as_estsolvia,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_update_solivitud_viaticos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de poner en estado de registrada a una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql=" UPDATE scv_solicitudviatico".
				"    SET estsolvia='".$as_estsolvia."'".
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND codsolvia='".$as_codsolvia."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos METODO->uf_scv_update_solivitud_viaticos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion= "Reversó la solicitud de viaticos ".$as_codsolvia." Asociada a la empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               ///////////////////////////
			$lb_valido=true;
		}
		return $lb_valido;
	} // fin function uf_scv_update_solivitud_viaticos
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_reverso_recepcion_documento_viatico($as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_reverso_recepcion_documento_viatico
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y reversar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$rs_data=$this->uf_select_datos_solicitud($as_codsolvia);
		$ls_descripcion="Calculo de Viaticos de la solicitud ".$as_codsolvia;
		$ls_numrecdoc="SCV-".$this->io_funciones->uf_cerosizquierda($as_codsolvia,11);
		$lb_tcomp=$this->uf_buscar_compromiso_solicitud($as_codsolvia);
		$lb_trecp=$this->uf_buscar_recepcion_solicitud($as_codsolvia);
		while((!$rs_data->EOF)&& $lb_valido)
		{
			$ls_codtipdoc=$rs_data->fields["codtipdoc"];
			$ls_fecsolvia=$rs_data->fields["fecsolvia"];
			$ls_tipvia=$rs_data->fields["tipvia"];
			$rs_datapersonal=$this->uf_load_personalviaticos($as_codsolvia);
			while((!$rs_datapersonal->EOF)&& $lb_valido)
			{
				
				$ls_codper=$rs_datapersonal->fields["codper"];
				$ls_cedula=$rs_datapersonal->fields["cedper"];
				if($ls_cedula=="")
				{
					$ls_cedula=$ls_codper;
				}

				switch ($ls_tipvia)
				{
					case "1":
						if ($lb_tcomp)
						{
							$lb_valido=$this->uf_reverso_compromiso($ls_numrecdoc,$ls_cedula,$as_codsolvia,$ls_fecsolvia,$aa_seguridad);
						}
						if (($lb_trecp)&&($lb_valido))
						{
							$arrResultado=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$lb_registro);
							$lb_registro=$arrResultado['ab_registro'];
							$ls_codtipdoc=$arrResultado['as_codtipdoc'];
							$lb_valido=$arrResultado['lb_valido'];
							if($lb_valido)
							{
								$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
								if($lb_anulada)
								{
									$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." esta asociada a una solicitud de pago Anulada.");
									$lb_valido=false;
									break;
								}
								else
								{
									if($lb_registro)
									{
											$lb_valido=$this->uf_scv_delete_dt_rd_scg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
											if($lb_valido)
											{
												$lb_valido=$this->uf_scv_delete_dt_rd_spg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												if($lb_valido)
												{
													$lb_valido=$this->uf_scv_delete_rd($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												}
											}
									}
									else
									{
										$this->io_mensajes->message("Las Recepciones de Documentos asociadas deben estar en estatus de Registro - No Aprobada");
										$lb_valido=false;
										break;
									}
								}
							}
							else
							{
								$this->io_mensajes->message("No existe Recepcion de Documentos asociada");
								$lb_valido=false;
								break;
							}
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
					case "2":
						if ($lb_tcomp)
						{
							$lb_valido=$this->uf_reverso_compromiso($ls_numrecdoc,$ls_cedula,$as_codsolvia,$ls_fecsolvia,$aa_seguridad);
						}
						if (($lb_trecp)&&($lb_valido))
						{
							$arrResultado=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$lb_registro);
							$lb_registro=$arrResultado['ab_registro'];
							$ls_codtipdoc=$arrResultado['as_codtipdoc'];
							$lb_valido=$arrResultado['lb_valido'];
							if($lb_valido)
							{
								$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
								if($lb_anulada)
								{
									$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." esta asociada a una solicitud de pago Anulada.");
									$lb_valido=false;
									break;
								}
								else
								{
									if($lb_registro)
									{
											$lb_valido=$this->uf_scv_delete_dt_rd_scg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
											if($lb_valido)
											{
												$lb_valido=$this->uf_scv_delete_dt_rd_spg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												if($lb_valido)
												{
													$lb_valido=$this->uf_scv_delete_rd($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												}
											}
									}
									else
									{
										$this->io_mensajes->message("Las Recepciones de Documentos asociadas deben estar en estatus de Registro - No Aprobada");
										$lb_valido=false;
										break;
									}
								}
							}
							else
							{
								$this->io_mensajes->message("No existe Recepcion de Documentos asociada");
								$lb_valido=false;
								break;
							}
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
					case "3":
						$lb_valido=$this->uf_reverso_compromiso($ls_numrecdoc,$ls_cedula,$as_codsolvia,$ls_fecsolvia,$aa_seguridad);
						if(!$lb_valido)
						{
							return false;
						}
					break;
					case "4":
						if ($lb_tcomp)
						{
							$lb_valido=$this->uf_reverso_compromiso($ls_numrecdoc,$ls_cedula,$as_codsolvia,$ls_fecsolvia,$aa_seguridad);
						}
						if (($lb_trecp)&&($lb_valido))
						{
							$arrResultado=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$lb_registro);
							$lb_registro=$arrResultado['ab_registro'];
							$ls_codtipdoc=$arrResultado['as_codtipdoc'];
							$lb_valido=$arrResultado['lb_valido'];
							if($lb_valido)
							{
								$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
								if($lb_anulada)
								{
									$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." esta asociada a una solicitud de pago Anulada.");
									$lb_valido=false;
									break;
								}
								else
								{
									if($lb_registro)
									{
											$lb_valido=$this->uf_scv_delete_dt_rd_scg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
											if($lb_valido)
											{
												$lb_valido=$this->uf_scv_delete_dt_rd_spg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												if($lb_valido)
												{
													$lb_valido=$this->uf_scv_delete_rd($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												}
											}
									}
									else
									{
										$this->io_mensajes->message("Las Recepciones de Documentos asociadas deben estar en estatus de Registro - No Aprobada");
										$lb_valido=false;
										break;
									}
								}
							}
							else
							{
								$this->io_mensajes->message("No existe Recepcion de Documentos asociada");
								$lb_valido=false;
								break;
							}
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
					case "5":
						if (($lb_trecp)&&($lb_valido))
						{
							$arrResultado=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$lb_registro);
							$lb_registro=$arrResultado['ab_registro'];
							$ls_codtipdoc=$arrResultado['as_codtipdoc'];
							$lb_valido=$arrResultado['lb_valido'];
							if($lb_valido)
							{
								$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
								if($lb_anulada)
								{
									$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." esta asociada a una solicitud de pago Anulada.");
									$lb_valido=false;
									break;
								}
								else
								{
									if($lb_registro)
									{
											$lb_valido=$this->uf_scv_delete_dt_rd_scg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
											if($lb_valido)
											{
												$lb_valido=$this->uf_scv_delete_dt_rd_spg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												if($lb_valido)
												{
													$lb_valido=$this->uf_scv_delete_rd($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																					  $aa_seguridad);
												}
											}
									}
									else
									{
										$this->io_mensajes->message("Las Recepciones de Documentos asociadas deben estar en estatus de Registro - No Aprobada");
										$lb_valido=false;
										break;
									}
								}
							}
							else
							{
								$this->io_mensajes->message("No existe Recepcion de Documentos asociada");
								$lb_valido=false;
								break;
							}
						}
						elseif ($lb_tcomp)
						{
							$lb_valido=$this->uf_reverso_compromiso($ls_numrecdoc,$ls_cedula,$as_codsolvia,$ls_fecsolvia,$aa_seguridad);
						}
						if(!$lb_valido)
						{
							return false;
						}
					break;
				}
				if($ls_tipvia=="-")
				{
				
					$arrResultado=$this->uf_scv_select_estatus_recepcion($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$lb_registro);
					$lb_registro=$arrResultado['ab_registro'];
					$ls_codtipdoc=$arrResultado['as_codtipdoc'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$lb_anulada=$this->uf_load_solicitudesanuladas($ls_numrecdoc,$ls_cedula,$ls_codtipdoc);
						if($lb_anulada)
						{
							$this->io_mensajes->message("La Recepcion de Documentos ".$ls_numrecdoc." esta asociada a una solicitud de pago Anulada.");
							$lb_valido=false;
							break;
						}
						else
						{
							if($lb_registro)
							{
									$lb_valido=$this->uf_scv_delete_dt_rd_scg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																			  $aa_seguridad);
									if($lb_valido)
									{
										$lb_valido=$this->uf_scv_delete_dt_rd_spg($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																			  $aa_seguridad);
										if($lb_valido)
										{
											$lb_valido=$this->uf_scv_delete_rd($ls_numrecdoc,$ls_cedula,$ls_codtipdoc,$as_codsolvia,
																			  $aa_seguridad);
										}
									}
							}
							else
							{
								$this->io_mensajes->message("Las Recepciones de Documentos asociadas deben estar en estatus de Registro - No Aprobada");
								$lb_valido=false;
								break;
							}
						}
					}
					else
					{
						$this->io_mensajes->message("No existe Recepcion de Documentos asociada");
						$lb_valido=false;
						break;
					}
				}
				$rs_datapersonal->MoveNext();
			}
			$rs_data->MoveNext();
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_scv_update_solivitud_viaticos($as_codsolvia,"C",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_select_estatus_recepcion($as_recepcion,$as_cedula,$as_codtipdoc,$ab_registro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_estatus_recepcion
		//         Access: public  
		//      Argumento: $as_numrecdoc // Numero de recepcion de documentos
		//  			   $as_cedula    //  Cedula de  baneficiario
		//  			   $as_codtipdoc // codigo de tipo de documento
		//  			   $ab_registro  // indica si alguna de las recepciones de documentos ha sido pasada a otro estatus
		//  			   $as_numrecdoc // numeto de la recepcion de documento
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica el estatus que se encuentra la recepcion de documentos generada desde viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql = "SELECT estprodoc,estaprord,codtipdoc".
		          "  FROM cxp_rd  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND numrecdoc='".$as_recepcion."'".
				  "   AND ced_bene='".$as_cedula."'".
				  $ls_sqlaux.
				  "   AND procede='SCVSOV'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_select_estatus_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ab_registro=true;
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_estaprord=$row["estaprord"];
				$ls_estprodoc=$row["estprodoc"];
				$as_codtipdoc=$row["codtipdoc"];
				if(($ls_estprodoc!="R")||($ls_estaprord!=0))
				{
					$ab_registro=false;
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ab_registro']=$ab_registro;
		$arrResultado['as_codtipdoc']=$as_codtipdoc;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end  function uf_scv_select_estatus_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_delete_dt_rd_scg($as_numrecdoc,$as_cedula,$as_codtipdoc,$as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_delete_dt_rd_scg
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $as_numconrec // numero concecutivo de recepción
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un detalle contable de una recepcion de documentos generada por una solicitud de 
		//                 viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql="DELETE FROM cxp_rd_scg".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND numrecdoc='". $as_numrecdoc ."'".
				  $ls_sqlaux.
				"   AND ced_bene='". $as_cedula ."'".
				"   AND cod_pro='----------'".
				"   AND procede_doc='SCVSOV'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_delete_dt_rd_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Reversó el detalle contable de la recepcion de documento ".$as_numrecdoc." mediante el reverso de".
			                 " la solicitud de viaticos".$as_codsolvia." asociada a la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	} // end  function uf_scv_delete_dt_rd_scg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_delete_dt_rd_spg($as_numrecdoc,$as_cedula,$as_codtipdoc,$as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_delete_dt_rd_spg
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numordcom // numero de la orden de compra/factura
		//  			   $as_numconrec // numero concecutivo de recepción
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un detalle contable de una recepcion de documentos generada por una solicitud de 
		//                 viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql="DELETE FROM cxp_rd_spg".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND numrecdoc='". $as_numrecdoc ."'".
				  $ls_sqlaux.
				"   AND ced_bene='". $as_cedula ."'".
				"   AND cod_pro='----------'".
				"   AND procede_doc='SCVSOV'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_delete_dt_rd_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Reversó el detalle presupuestario de la recepcion de documento ".$as_numrecdoc." mediante el reverso".
			                 " de la solicitud de viaticos".$as_codsolvia." asociada a la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	} // end  function uf_scv_delete_dt_rd_spg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_delete_rd($as_numrecdoc,$as_cedula,$as_codtipdoc,$as_codsolvia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_delete_rd
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_numrecdoc // numero de recepcion de documentos
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina las recepciones de documentos originadas de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sqlaux="";
		if(trim($as_codtipdoc)!="")
		{
			$ls_sqlaux="   AND codtipdoc='".$as_codtipdoc."'";
		}
		$ls_sql="DELETE FROM cxp_rd".
				" WHERE codemp='". $this->ls_codemp ."'".
				"   AND numrecdoc='". $as_numrecdoc ."'".
				  $ls_sqlaux.
				"   AND ced_bene='". $as_cedula ."'".
				"   AND cod_pro='----------'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->revcalcularviaticos MÉTODO->uf_scv_delete_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Reversó  la recepcion de documento ".$as_numrecdoc." mediante el reverso".
			                 " de la solicitud de viaticos".$as_codsolvia." asociada a la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion); 
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$lb_valido=true;
		}
		return $lb_valido;
	}  // end  function uf_scv_delete_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_solicitudesanuladas($as_numrecdoc,$as_cedula,$as_codtipdoc)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_solicitudesanuladas
		//		   Access: public
		//		 Argument: as_numrecdoc     // Numero de Recepcion de Documentos
		//                 as_cedula     // Fecha (Emision) de inicio de la Busqueda
		//                 as_codtipdoc     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipproben     // tipo proveedor/ beneficiario
		//                 as_proben        // Codigo de proveedor/ beneficiario
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las recepciones  a aprobar o reversar aprobacion
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 05/05/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ab_anulada=false;
		$ls_sql="SELECT cxp_rd.numrecdoc ".
				"  FROM cxp_rd".
				" WHERE cxp_rd.codemp = '".$this->ls_codemp."'".
				"   AND cxp_rd.numrecdoc = '".$as_numrecdoc."' ".
				"   AND cxp_rd.codtipdoc = '".$as_codtipdoc."' ".
				"   AND cxp_rd.ced_bene = '".$as_cedula."' ".
				"   AND cxp_rd.cod_pro = '----------' ".
				"   AND (cxp_rd.estprodoc='R' OR cxp_rd.estprodoc='E')".
				"   AND cxp_rd.numrecdoc IN (SELECT cxp_dt_solicitudes.numrecdoc".
				"						       FROM cxp_solicitudes,cxp_dt_solicitudes".
				"						      WHERE cxp_dt_solicitudes.numrecdoc like '".$as_numrecdoc."'".
				"								AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"								AND (cxp_solicitudes.estprosol='A' OR cxp_solicitudes.estprosol='N'))";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Anulacion MÉTODO->uf_load_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ab_anulada=true;
			}
		}
		return $ab_anulada;
	}// end function uf_load_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_compromiso($as_codcom,$adt_fecha,$as_obssolvia,$as_cedula,$as_codsolvia,$as_codper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_contrato
		//		   Access: public
		//	    Arguments: as_codsolvia  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   adt_fecha  // Fecha del Contrato
		//	    		   ad_fechacontaasig  // Fecha de Contabilización de la Asignación
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin contabilizar en presupuesto el compromiso del contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp= $this->ls_codemp;
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcom);		
		$ldt_fecha=$this->io_funciones->uf_convertirfecmostrar($adt_fecha); 		
        // obtengo el monto de la Asignacion y la comparo con el monto de gasto acumulado		
		$ldt_feccon=$ldt_fecha;
		$ls_descripcion=$as_obssolvia; 
		$ls_codigo_destino=$as_cedula;	
        $ls_mensaje="O"; // Compromete
        $ls_tipo_destino="B";		
        $ls_procede="SCVINS"; // Procedencia Viaticos Instalacion

        $this->io_sigesp_int->uf_int_init_transaction_begin();

		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_funciones->uf_convertirdatetobd($ldt_fecha);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$ldt_montotarifa=$this->uf_scv_load_dt_personal_int($ls_codemp,$as_codsolvia,$as_codper);
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if (!$lb_valido)
		{   
			$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$lb_valido=$this->uf_procesar_detalles_gastos($as_codsolvia,$ls_mensaje,$ls_procede,$ls_descripcion,"PC",$ldt_montotarifa);
		if($lb_valido) 
		{
			if($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_contabilizacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_compromiso_internacional($as_codcom,$adt_fecha,$as_obssolvia,$as_cedula,$as_codsolvia,$as_codper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_contrato
		//		   Access: public
		//	    Arguments: as_codsolvia  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   adt_fecha  // Fecha del Contrato
		//	    		   ad_fechacontaasig  // Fecha de Contabilización de la Asignación
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin contabilizar en presupuesto el compromiso del contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp= $this->ls_codemp;
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcom);		
		$ldt_fecha=$this->io_funciones->uf_convertirfecmostrar($adt_fecha); 		
		
        // obtengo el monto de la Asignacion y la comparo con el monto de gasto acumulado		
		$ldt_feccon=$ldt_fecha;
		$ls_descripcion=$as_obssolvia; 
		$ls_codigo_destino=$as_cedula;	
        $ls_mensaje="O"; // Compromete
        $ls_tipo_destino="B";		
        $ls_procede="SCVINS"; // Procedencia Viaticos Instalacion

        $this->io_sigesp_int->uf_int_init_transaction_begin();

		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_funciones->uf_convertirdatetobd($ldt_fecha);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$ldt_montotarifa=$this->uf_scv_load_dt_personal_internacional($ls_codemp,$as_codsolvia,$as_codper);
		
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if (!$lb_valido)
		{   
			$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$lb_valido=$this->uf_procesar_detalles_gastos($as_codsolvia,$ls_mensaje,$ls_procede,$ls_descripcion,"PC",$ldt_montotarifa);
		if($lb_valido) 
		{
			if($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_contabilizacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_scv_load_dt_personal_int($as_codemp,$as_codsolvia,$as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_dt_personal_int
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT (CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.codasicar ELSE sno_cargo.codcar END) AS codcar,".
				"		(SELECT cedper FROM sno_personal".
				"		  WHERE sno_personal.codper=sno_personalnomina.codper) as cedper,MAX(scv_dt_personal.codnom) AS codnom".
				"  FROM sno_personalnomina, sno_nomina, sno_cargo, sno_asignacioncargo,sno_personal,scv_dt_personal".
				" WHERE scv_dt_personal.codemp='".$as_codemp."'".
				"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
				"   AND scv_dt_personal.codper='".$as_codper."'".
				"   AND scv_dt_personal.codemp=sno_personal.codemp".
				"   AND scv_dt_personal.codper=sno_personal.codper".
				"   AND scv_dt_personal.codemp=sno_personalnomina.codemp".
				"   AND scv_dt_personal.codnom=sno_personalnomina.codnom".
				"   AND sno_nomina.espnom='0'".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom".
				"   AND sno_personalnomina.codper = sno_personal.codper".
				"   AND sno_personalnomina.codemp = sno_cargo.codemp".
				"   AND sno_personalnomina.codnom = sno_cargo.codnom".
				"   AND sno_personalnomina.codcar = sno_cargo.codcar".
				"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
				"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
				"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
				" GROUP BY sno_personalnomina.codper,  sno_personalnomina.codper, sno_nomina.racnom,  ".
				" sno_asignacioncargo.denasicar,sno_asignacioncargo.codasicar, sno_cargo.descar, sno_cargo.codcar,scv_dt_personal.codclavia".
				" ORDER BY sno_personalnomina.codper"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_load_dt_personal_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codnom=$row["codnom"];
				$ls_cargo= $row["codcar"];
				$lb_monto=$this->uf_scv_select_tarifacargo($as_codemp,$ls_cargo,$ls_codnom);
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_monto;
	}  // end function uf_scv_load_dt_personal

	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_scv_load_dt_personal_internacional($as_codemp,$as_codsolvia,$as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_load_dt_personal_int
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_monto=0;
		$ls_sql="SELECT monbol".
				"  FROM scv_dt_misiones,scv_tarifas".
				" WHERE scv_dt_misiones.codemp='".$as_codemp."'".
				"   AND scv_dt_misiones.codsolvia='".$as_codsolvia."'".
				"   AND scv_dt_misiones.codemp=scv_tarifas.codemp".
				"   AND scv_dt_misiones.codmis=scv_tarifas.codmis".
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_load_dt_personal_int ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_monbol=$row["monbol"];
				$ls_monto=$ls_monto+$ls_monbol;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_monto;
	}  // end function uf_scv_load_dt_personal

	function uf_scv_select_tarifacargo($as_codemp,$as_codcar,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifacargo
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montarcar="";
		$ls_sql="SELECT montarcar".
				"  FROM scv_dt_tarifacargos".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codcar='". $as_codcar ."'".
				"   AND codnom= '".$as_codnom."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integración MÉTODO->uf_scv_select_tarifacargo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["montarcar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montarcar;
	}  // end function uf_scv_select_tarifacargo
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_scv_load_dt_tarifa($as_codemp,$as_codsolvia,$as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tarifacargo
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montarcar="";
		$ls_sql="SELECT montarcar".
				"  FROM scv_dt_tarifacargos".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codcar='". $as_codcar ."'".
				"   AND codnom= '".$as_codnom."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integración MÉTODO->uf_scv_select_tarifacargo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["montarcar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montarcar;
	}  // end function uf_scv_select_tarifacargo
	//-----------------------------------------------------------------------------------------------------------------------------------

    function uf_procesar_compromiso_ord($as_codcom,$adt_fecha,$as_obssolvia,$as_cedula,$as_codsolvia,$as_codper,$ai_monpervia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_contabilizacion_contrato
		//		   Access: public
		//	    Arguments: as_codsolvia  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   adt_fecha  // Fecha del Contrato
		//	    		   ad_fechacontaasig  // Fecha de Contabilización de la Asignación
		//	    		   aa_seguridad  // Arreglo de seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin contabilizar en presupuesto el compromiso del contrato
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_codemp= $this->ls_codemp;
        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcom);		
		$ldt_fecha=$this->io_funciones->uf_convertirfecmostrar($adt_fecha); 		
		
        // obtengo el monto de la Asignacion y la comparo con el monto de gasto acumulado		
		$ldt_feccon=$ldt_fecha;
		$ls_descripcion=$as_obssolvia; 
		$ls_codigo_destino=$as_cedula;	
        $ls_mensaje="O"; // Compromete
        $ls_tipo_destino="B";		
        $ls_procede="SCVINS"; // Procedencia Viaticos Instalacion

        $this->io_sigesp_int->uf_int_init_transaction_begin();

		$ls_codban="---";
		$ls_ctaban="-------------------------";
		$li_tipo_comp=1; // comprobante Normal
		$this->as_procede=$ls_procede;
		$this->as_comprobante=$ls_comprobante;
		$this->ad_fecha=$this->io_funciones->uf_convertirdatetobd($ldt_fecha);
		$this->as_codban=$ls_codban;
		$this->as_ctaban=$ls_ctaban;
		$ldt_montotarifa=$ai_monpervia;
		
		$lb_valido=$this->io_sigesp_int->uf_int_init($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_descripcion,
													 $ls_tipo_destino,$ls_codigo_destino,false,$ls_codban,$ls_ctaban,
													 $li_tipo_comp);
		if (!$lb_valido)
		{   
			$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
			$this->io_sigesp_int->uf_sql_transaction($lb_valido);
			return false;		   		   
		}
		$lb_valido=$this->uf_procesar_detalles_gastos_ord($as_codsolvia,$ls_mensaje,$ls_procede,$ls_descripcion,"PC",$ldt_montotarifa);
		if($lb_valido) 
		{
			if($lb_valido)
			{
				$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
				if(!$lb_valido)
				{
					$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
				}
			}
		}
		$this->io_sigesp_int->uf_sql_transaction($lb_valido);
		return  $lb_valido;
	}// end function uf_procesar_contabilizacion_contrato
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_detalles_gastos($as_codsolvia,$as_mensaje,$as_procede_doc,$as_descripcion,$as_process,$adt_montotarifa)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_gastos
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   as_mensaje  // Mensaje del precompromiso
		//	    		   as_procede_doc  // Procede del Documento
		//	    		   as_descripcion  // Descripcioón de la obre
		//	    		   as_process  // proceso si se va a precomprometer o se va a hacer el reverso del precompromiso
		//	      Returns: Retorna un boolean valido
		//	  Description: método que procesa los detalles de gastos de una asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_sql="SELECT scv_solicitudviatico.codestpro1,scv_solicitudviatico.codestpro2,scv_solicitudviatico.codestpro3,".
				"		scv_solicitudviatico.codestpro4,scv_solicitudviatico.codestpro5,scv_solicitudviatico.estcla,".
				"		scv_dt_spg.monto,scv_dt_spg.spg_cuenta ".
                "  FROM scv_solicitudviatico,scv_dt_spg ".
                " WHERE scv_solicitudviatico.codemp='".$this->ls_codemp."' ".
				"   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."'".
				"   AND scv_solicitudviatico.codemp=scv_dt_spg.codemp".
				"   AND scv_solicitudviatico.codsolvia=scv_dt_spg.codsolvia";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_mensajes->message("CLASE->Integración SOB MÉTODO->uf_procesar_detalles_gastos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		    {
			 	$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ldec_monto=$row["monto"];
				$ls_spg_cuenta=$row["spg_cuenta"];
                if($as_process=="PC")
				{// Se genera el precompromiso de la asignación	
					$ldec_monto=$ldec_monto;
				}
				else //"CO" Reverso del precompromiso
				{
  	 	 	 	   $ldec_monto=$ldec_monto*(-1);
				}
				$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($this->ls_codemp,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
									                                       $ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_spg_cuenta,$as_mensaje,
									                                       $ldec_monto,$ls_documento,$as_procede_doc,$as_descripcion);
				if ($lb_valido===false)
				{  
				   $this->io_msg->message($this->io_sigesp_int->is_msg_error);
				   break;
				}
			} 
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	}// end function uf_procesar_detalles_gastos_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_procesar_detalles_gastos_ord($as_codsolvia,$as_mensaje,$as_procede_doc,$as_descripcion,$as_process,$adt_montotarifa)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_detalles_gastos_ord
		//		   Access: private
		//	    Arguments: as_codasi  // Código de Asignacióna
		//	    		   as_mensaje  // Mensaje del precompromiso
		//	    		   as_procede_doc  // Procede del Documento
		//	    		   as_descripcion  // Descripcioón de la obre
		//	    		   as_process  // proceso si se va a precomprometer o se va a hacer el reverso del precompromiso
		//	      Returns: Retorna un boolean valido
		//	  Description: método que procesa los detalles de gastos de una asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 25/04/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;		
		$ls_sql="SELECT * ".
                "  FROM scv_solicitudviatico ".
                " WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
            $this->io_mensajes->message("CLASE->Integración SOB MÉTODO->uf_procesar_detalles_gastos_ord ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
			return false;
		}
		else
		{                 
			while($row=$this->io_sql->fetch_row($rs_data) and ($lb_valido))
		    {
			 	$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_estcla=$row["estcla"];
				$ls_spg_cuenta=$this->uf_scv_load_maxinter($this->ls_codemp,"SCV","CONFIG","INTERNACIONALES");
				$ls_documento=$this->io_sigesp_int->uf_fill_comprobante($as_codsolvia);		
				$ldec_montomax=$this->uf_scv_load_maxinter($this->ls_codemp,"SCV","CONFIG","MAXINTER");
				$ldec_monto=$adt_montotarifa;
                if($as_process=="PC")
				{// Se genera el precompromiso de la asignación	
					$ldec_monto=$ldec_monto;
				}
				else //"CO" Reverso del precompromiso
				{
  	 	 	 	   $ldec_monto=$ldec_monto*(-1);
				}
				$lb_valido = $this->io_sigesp_int->uf_spg_insert_datastore($this->ls_codemp,$ls_codestpro1,$ls_codestpro2,$ls_codestpro3,
									                                       $ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_spg_cuenta,$as_mensaje,
									                                       $ldec_monto,$ls_documento,$as_procede_doc,$as_descripcion);
				if ($lb_valido===false)
				{  
				   $this->io_msg->message($this->io_sigesp_int->is_msg_error);
				   break;
				}
			} 
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	}// end function uf_procesar_detalles_gastos_asignacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_load_maxinter($as_codemp,$as_codsis,$as_seccion,$as_entry) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	          Metodo:  uf_scv_load_maxinter
		//	          Access:  public
		//	       Arguments:  $as_codemp    // código de la Empresa.
		//        			   $as_codmis    //  código de la Misión.
		//	         Returns:  $lb_valido.
		//	     Description:  Función que se encarga de verificar si existe o no la configuracion de viaticos
		//     Elaborado Por:  Ing. Luis Anibal Lang
		// Fecha de Creación:  13/11/2006
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
		$as_valor="";
		if($as_entry=="MAXINTER")
		{
			$ls_sql=" SELECT value".
					"   FROM sigesp_config".
					"  WHERE codemp='".$as_codemp."'".
					"    AND codsis='".$as_codsis."'".
					"    AND seccion='".$as_seccion."'".
					"    AND entry='".$as_entry."'";
		}
		else
		{
			$ls_sql=" SELECT value".
					"   FROM sigesp_config".
					"  WHERE codemp='".$as_codemp."'".
					"    AND codsis='".$as_codsis."'".
					"    AND seccion='".$as_seccion."'".
					"    AND entry='".$as_entry."'";
		}

		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_scv_c_config METODO->uf_scv_load_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_valor=$row["value"];
			}
		}
		return $as_valor;
	} // fin de la function uf_scv_load_config

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_reverso_compromiso($as_codcom,$as_cedula,$as_codsolvia,$adt_fecha,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_reverso_contrato
		//		   Access: public
		//	    Arguments: as_codcon  // Código de Contrato
		//	    		   as_codasi  // Código de Asignación
		//	    		   ad_fechaconta  // Fecha de Contabilización
		//	    		   aa_seguridad  // Arreglo de Seguridad
		//	      Returns: Retorna un boolean valido
		//	  Description: Este metodo tiene como fin reversar la contabilizacion del contrato y restaurar el precompromiso de la asignación
		//	   Creado Por: Ing. Wilmer Briceño
		// Modificado Por: Ing. Yesenia Moreno								Fecha Última Modificación : 30/04/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;				
	    $ls_codemp= $this->ls_codemp;
        $ls_tipo_destino="B";		
        $ls_procede="SCVINS"; // Procedencia Viaticos Instalacion

        $ls_comprobante=$this->io_sigesp_int->uf_fill_comprobante($as_codcom);		

		$ls_cod_pro="----------";	
	    $ls_ced_bene=$as_cedula;

		$ldt_fecha=$adt_fecha;
		$ls_codban="---";
		$ls_ctaban="-------------------------";
	    $lb_valido=$this->io_sigesp_int->uf_obtener_comprobante($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,
																$ls_codban,$ls_ctaban,$ls_tipo_destino,$ls_ced_bene,$ls_cod_pro);
		if(!$lb_valido) 
		{ 
			$this->io_mensajes->message("ERROR-> No existe el comprobante Nº ".$ls_comprobante."-".$ls_procede.".");
			return false;
		}
		$lb_valido=$this->io_sigesp_int->uf_init_delete($ls_codemp,$ls_procede,$ls_comprobante,$ldt_fecha,$ls_tipo_destino,
														$ls_ced_bene,$ls_cod_pro,false,$ls_codban,$ls_ctaban);
		if(!$lb_valido)	
		{ 
			$this->io_mensajes->message("".$this->io_sigesp_int->is_msg_error);
			return false; 
		}
	    if($lb_valido)
		{
			$lb_valido=$this->io_sigesp_int->uf_init_end_transaccion_integracion($aa_seguridad); 
			if(!$lb_valido)
			{
				$this->io_mensajes->message($this->io_sigesp_int->is_msg_error);
			}
		}
		return  $lb_valido;
	}// end function uf_reverso_contrato_sob
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_compromiso_solicitud($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_compromiso_solicitud
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codsolvia".
				"  FROM scv_dt_spg".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_buscar_compromiso_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;			
			}
		}
		return $lb_valido;
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_recepcion_solicitud($as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_validar_recepcion_documentos
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $ls_codsolvia // codigo de solicitud de viaticos 
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos 
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codsolvia".
				"  FROM scv_dt_scg".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobar MÉTODO->uf_buscar_recepcion_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;			
			}
		}
		return $lb_valido;
				
	}
	//-----------------------------------------------------------------------------------------------------------------------------------



}
?>