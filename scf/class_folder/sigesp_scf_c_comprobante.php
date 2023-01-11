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

class sigesp_scf_c_comprobante
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
		//	     Function: sigesp_scf_c_comprobante
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."/base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
                //$io_conexion->debug=true;
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
		require_once($as_path."shared/class_folder/sigesp_c_generar_consecutivo.php");
		$this->io_keygen= new sigesp_c_generar_consecutivo();
		require_once($as_path."shared/class_folder/class_sigesp_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_int.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_scg.php");
		require_once($as_path."shared/class_folder/class_sigesp_int_spi.php");
		$this->io_intint=new class_sigesp_int_int();
		$this->io_intscg=new class_sigesp_int_scg();
                $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function sigesp_scf_c_comprobante
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
	function uf_load_procedencias($as_procede)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_procedencias
		//		   Access: public
		//		 Argument: as_procede	// Procede del comprobante
		//	  Description: Función que busca en la tabla de procedencias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT procede ".
				"  FROM sigesp_procedencias ".
				" WHERE procede like '%".$as_procede."%' ".
				" ORDER BY procede ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Comprobante MÉTODO->uf_load_procedencias ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			print "<select name='cmbprocede' id='cmbprocede'>";
			if($as_procede=="")
			{
				print " <option value=''>-- Seleccione Una --</option>";
			}
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_procede=$row["procede"];
				print "<option value='".$ls_procede."' >".$ls_procede."</option>";
			}
			$this->io_sql->free_result($rs_data);	
			print "</select>";
		}
		return $lb_valido;
	}// end function uf_load_procedencias
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_comprobante($as_comprobante,$ad_fecha,$as_procede,$as_descripcion,$as_codpro,$as_cedbene,$as_tipodestino,
								   $as_codban,$as_ctaban,$ai_tipcom,$ai_totrowscg,$as_prefijo,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_comprobante
		//		   Access: private
		//	    Arguments: as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_procede  // Procede del comprobante
		//				   as_descripcion  // Descripción del comprobante
		//				   as_codpro  // Código proveedor 
		//				   as_cedbene  // Código beneficiario
		//				   as_tipodestino  // Tipo de Destino
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   ai_tipcom  // Tipo de Comprobante
		//				   ai_totrowscg  // total de filas de Contabilidad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta un comprobante contable y sus detalles
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql->begin_transaction();				
		$ad_fecha=$this->io_funciones->uf_convertirdatetobd($ad_fecha);
                $ls_comprobanteaux=$as_comprobante; 
                $arrResultado= $this->io_keygen->uf_verificar_numero_generado3("SCG","sigesp_cmp","comprobante","SCGCMP",15,"","procede","SCGCMP",$as_comprobante,$aa_seguridad["logusr"],$as_prefijo);
                $as_comprobante = $arrResultado['as_numero'];
		$lb_valido = $arrResultado['lb_valido'];
		
                $lb_valido=$this->io_intscg->uf_sigesp_insert_comprobante($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,
									  $ai_tipcom,$as_descripcion,$as_tipodestino,$as_codpro,
									  $as_cedbene,$as_codban,$as_ctaban);
																  
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el Comprobante Contable ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$as_cedbene." Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{	// Insertamos los Detalles
			$lb_valido=$this->uf_insert_detallesscg($as_comprobante,$ad_fecha,$as_procede,$as_codpro,$as_cedbene,$as_codban,$as_ctaban,
													$ai_tipcom,$ai_totrowscg,$aa_seguridad);
		}			
		if($lb_valido)
		{	
                        if($ls_comprobanteaux!=$as_comprobante)
                        {
                                $this->io_mensajes->message("Se Asigno el Numero de Comprobante: ".$as_comprobante);
                        }
			$this->io_mensajes->message("El Comprobante Contable fue registrado.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Registrar el Comprobante Contable."); 
			$this->io_sql->rollback();
		}
                $arrResultado['lb_valido']=$lb_valido;
                $arrResultado['as_comprobante']=$as_comprobante;
		return $arrResultado;
	}// end function uf_insert_comprobante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_detallesscg($as_comprobante,$ad_fecha,$as_procede,$as_codpro,$as_cedbene,$as_codban,$as_ctaban,$ai_tipcom,
								  $ai_totrowscg,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_detallesscg
		//		   Access: private
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
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta un comprobante contable y sus detalles
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		for($li_i=1;($li_i<$ai_totrowscg)&&($lb_valido);$li_i++)
		{
			$ls_cuenta=$_POST["txtcuenta".$li_i];
			$ls_descripcion=$_POST["txtdescripcion".$li_i];
			$ls_procede_doc=$_POST["txtprocede".$li_i];
			$ls_documento=$_POST["txtdocumento".$li_i];
			$li_mondeb=$_POST["txtmondeb".$li_i];
			$li_monhab=$_POST["txtmonhab".$li_i];
			$ls_debhab=$_POST["txtdebhab".$li_i];	
			if($ls_debhab=="D")
			{
				$li_monto=$_POST["txtmondeb".$li_i];					
				$li_monto=str_replace(".","",$li_monto);
				$li_monto=str_replace(",",".",$li_monto);
			}
			else
			{
				$li_monto=$_POST["txtmonhab".$li_i];					
				$li_monto=str_replace(".","",$li_monto);
				$li_monto=str_replace(",",".",$li_monto);
			}
			$lb_valido=$this->io_intscg->uf_scg_procesar_insert_movimiento($this->ls_codemp,$as_procede,$as_comprobante,
																		   $ad_fecha,$ai_tipcom,$as_codpro,$as_cedbene,
																		   $ls_cuenta,$ls_procede_doc,$ls_documento,
																		   $ls_debhab,$ls_descripcion,0,$li_monto,
																		   $as_codban,$as_ctaban);
																		   
			if($lb_valido)
			{
				if($ls_debhab=="D")
				{
					$lb_valido=$this->io_intscg->uf_scg_comprobante_update($li_monto);
				}
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion="Insertó la cuenta ".$ls_cuenta." Monto ".$li_monto." a el Comprobante Contable ".$as_comprobante.
								" Procede ".$as_procede." Fecha ".$ad_fecha." Beneficiario ".$as_cedbene." Proveedor ".$as_codpro.
								" Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			}
		}
		return $lb_valido;
	}// end function uf_insert_detallesscg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_comprobante($as_comprobante,$ad_fecha,$as_procede,$as_descripcion,$as_codpro,$as_cedbene,$as_tipodestino,
								   $as_codban,$as_ctaban,$ai_tipcom,$ai_totaldebe,$ai_totrowscg,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_comprobante
		//		   Access: private
		//	    Arguments: as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_procede  // Procede del comprobante
		//				   as_descripcion  // Descripción del comprobante
		//				   as_codpro  // Código proveedor 
		//				   as_cedbene  // Código beneficiario
		//				   as_tipodestino  // Tipo de Destino
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   ai_tipcom  // Tipo de Comprobante
		//				   ai_totaldebe  // Monto Total por el debe
		//				   ai_totrowscg  // total de filas de Contabilidad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta un comprobante contable y sus detalles
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql->begin_transaction();				
		$ad_fecha=$this->io_funciones->uf_convertirdatetobd($ad_fecha);
		$lb_valido=$this->io_intscg->uf_sigesp_update_comprobante($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,
																  $ai_tipcom,$as_descripcion,$as_tipodestino,$as_codpro,
																  $as_cedbene,$as_codban,$as_ctaban);
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el Comprobante Contable ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$as_cedbene." Proveedor ".$as_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_intscg->uf_scg_comprobante_update_cero();
		}
		if($lb_valido)
		{	// Eliminamos todos los detalles que tiene el comprobante
			$lb_valido=$this->uf_delete_detallesscg($as_comprobante,$ad_fecha,$as_procede,$as_codpro,$as_cedbene,$as_codban,
													$as_ctaban,$ai_tipcom,$aa_seguridad);
		}					
		if($lb_valido)
		{	// Insertamos los Detalles
			$lb_valido=$this->uf_insert_detallesscg($as_comprobante,$ad_fecha,$as_procede,$as_codpro,$as_cedbene,$as_codban,
													$as_ctaban,$ai_tipcom,$ai_totrowscg,$aa_seguridad);
		}			
		if($lb_valido)
		{	
			$this->io_mensajes->message("El Comprobante Contable fue registrado.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Registrar el Comprobante Contable."); 
			$this->io_sql->rollback();
		}
		return $lb_valido;
	}// end function uf_update_comprobante
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
		// Fecha Creación: 28/06/2007								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Comprobante MÉTODO->uf_delete_detallesscg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido) )
			{
				$ls_cuenta=$row["sc_cuenta"];
				$ls_procededoc=$row["procede_doc"];
				$ls_documento=$row["documento"];
				$ls_debhab=$row["debhab"];
				$li_monto=$row["monto"];
				$lb_valido=$this->io_intscg->uf_scg_procesar_delete_movimiento($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,
																			   $ls_cuenta,$ls_procededoc,$ls_documento,$ls_debhab,
																			   $li_monto,$as_codban,$as_ctaban);
				if($lb_valido)
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion="Elimino la cuenta ".$ls_cuenta." a el Comprobante Contable ".$as_comprobante." Procede ".$as_procede.
									" Fecha ".$ad_fecha." Beneficiario ".$as_cedbene." Proveedor ".$as_codpro.
									" Asociado a la empresa ".$this->ls_codemp;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_valido;
	}// end function uf_delete_detallesscg
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_comprobante,$ad_fecha,$as_procede,$as_descripcion,$as_codprovben,$as_tipodestino,
						$as_codban,$as_ctaban,$ai_totaldebe,$ai_totrowscg,$as_prefijo,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_scf_p_comprobante.php)
		//	    Arguments: as_existe  //  Si el registro exite ó si es nuevo
		//				   as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_procede  // Procede del comprobante
		//				   as_descripcion  // Descripción del comprobante
		//				   as_codprovben  // Código proveedor / beneficiario
		//				   as_tipodestino  // Tipo de Destino
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   ai_totaldebe  // Monto total del Debe
		//				   ai_totrowscg  // total de filas de Contabilidad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que valida y guarda el comprobante
		//	   Creado Por: Ing. Yesenia Moreno 
		// Fecha Creación: 27/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_fuente="----------";
		$ls_codpro="----------";
		$ls_cedbene="----------";
		$li_tipcom=1;
		switch($as_tipodestino)
		{
			case "P":
				 $ls_codpro=$as_codprovben;
				 $ls_fuente=$ls_codpro;
				 break;
			case "B":
				 $ls_cedbene=$as_codprovben;
				 $ls_fuente=$ls_cedbene;
				 break;
		}
		$arrResultado=$this->io_intscg->uf_obtener_comprobante($this->ls_codemp,$as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$as_tipodestino,$ls_cedbene,$ls_codpro);
		$ad_fecha = $arrResultado['adt_fecha'];
		$as_tipodestino = $arrResultado['as_tipo_destino'];
		$ls_cedbene = $arrResultado['as_ced_bene'];
		$ls_codpro = $arrResultado['as_cod_pro'];
		$lb_encontrado = $arrResultado['lb_existe'];
		$this->io_intscg->is_codemp=$this->ls_codemp;
		$this->io_intscg->is_procedencia=$as_procede;
		$this->io_intscg->is_comprobante=$as_comprobante;
		$this->io_intscg->id_fecha=$ad_fecha;
		$this->io_intscg->as_codban=$as_codban;
		$this->io_intscg->as_ctaban=$as_ctaban;
		switch ($as_existe)
		{
			case "FALSE":
				//if(!($lb_encontrado))
				//{
					$lb_valido=$this->io_intint->uf_init_valida_parametros_comprobante($this->ls_codemp,$as_procede,
                                                                                                           $as_comprobante,$ad_fecha,$as_descripcion,
                                                                                                            $as_tipodestino,$ls_fuente,$as_codban,
                                                                                                            $as_ctaban);
					if(!$lb_valido)
					{
						return false;
					}                 
					  
					$arrResultado=$this->uf_insert_comprobante($as_comprobante,$ad_fecha,$as_procede,$as_descripcion,$ls_codpro,
														    $ls_cedbene,$as_tipodestino,$as_codban,$as_ctaban,$li_tipcom,
															$ai_totrowscg,$as_prefijo,$aa_seguridad);
                                        $lb_valido=$arrResultado['lb_valido'];
                                        $as_comprobante=$arrResultado['as_comprobante'];

					 										
				//}
				//else
				//{
				//	$this->io_mensajes->message("El Comprobante ya existe, no la puede incluir.");
				//	return false;
				//}
				break;

			case "TRUE":
				if($lb_encontrado)
				{
					$lb_valido=$this->io_intint->uf_init_valida_parametros_comprobante($this->ls_codemp,$as_procede,
																					   $as_comprobante,$ad_fecha,$as_descripcion,
														  							   $as_tipodestino,$ls_fuente,$as_codban,
																					   $as_ctaban);
					if(!$lb_valido)
					{
						return false;
					}                    
					$lb_valido=$this->uf_update_comprobante($as_comprobante,$ad_fecha,$as_procede,$as_descripcion,$ls_codpro,
														    $ls_cedbene,$as_tipodestino,$as_codban,$as_ctaban,$li_tipcom,
															$ai_totaldebe,$ai_totrowscg,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El comprobante no existe, no lo puede actualizar.");
				}
				break;
		}
                $arrResultado['lb_valido']=$lb_valido;
                $arrResultado['as_comprobante']=$as_comprobante;
		return $arrResultado;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete($as_comprobante,$ad_fecha,$as_procede,$as_codprovben,$as_tipodestino,$as_codban,$as_ctaban,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_scf_p_comprobante.php)
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
		// Fecha Creación: 27/06/2007 								Fecha Última Modificación : 
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
			$ls_descripcion ="Elimino el Comprobante Contable ".$as_comprobante." Procede ".$as_procede." Fecha ".$ad_fecha.
							 " Beneficiario ".$ls_cedbene." Proveedor ".$ls_codpro." Asociado a la empresa ".$this->ls_codemp;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("El Comprobante Contable fue eliminado.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un Error al Eliminar el Comprobante Contable."); 
			$this->io_sql->rollback();
		}

		return $lb_valido;
	}// end function uf_delete
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_cuentas_contables($as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$io_ds_scgcuentas)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_cuentas_contables
		//		   Access: public
		//	    Arguments: as_procede  // Procede del comprobante
		//				   as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   as_tipdes  // Tipo de Destino
		//				   as_cedbene  // Código beneficiario
		//				   as_codpro  // Código proveedor 
		//				   io_ds_scgcuentas  // Datastored donde se van a guardar las cuentas
		//	  Description: Función que busca las cuentas contables de un comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/06/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecha=$this->io_funciones->uf_convertirdatetobd($ad_fecha);
		$ls_sql="SELECT sc_cuenta AS cuenta, descripcion, procede_doc As procede, documento, debhab, ".
				"       monto AS mondeb, monto AS monhab, orden  ".
				"  FROM scg_dt_cmp ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND procede = '".$as_procede."' ".
				"	AND comprobante = '".$as_comprobante."' ".
				"	AND fecha = '".$ad_fecha."' ".
				"	AND codban = '".$as_codban."' ".
				"	AND ctaban = '".$as_ctaban."' ".
				"   AND debhab = 'H'".
				" UNION ".
				"SELECT sc_cuenta AS cuenta, descripcion, procede_doc As procede, documento, debhab, ".
				"       monto AS mondeb, monto AS monhab, orden  ".
				"  FROM scg_dt_cmp ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND procede = '".$as_procede."' ".
				"	AND comprobante = '".$as_comprobante."' ".
				"	AND fecha = '".$ad_fecha."' ".
				"	AND codban = '".$as_codban."' ".
				"	AND ctaban = '".$as_ctaban."' ".
				"   AND debhab = 'D'".
				" ORDER BY debhab, orden ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Comprobante MÉTODO->uf_load_sccuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$io_ds_scgcuentas->data=$this->io_sql->obtener_datos($rs_data);
			}
		}
		$arrResultado['io_ds_scgcuentas']=$io_ds_scgcuentas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_load_cuentas_contables
	
	function uf_obtener_cuentas_contables($as_procede,$as_comprobante,$ad_fecha,$as_codban,$as_ctaban,$rs_data)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_cuentas_contables
		//		   Access: public
		//	    Arguments: as_procede  // Procede del comprobante
		//				   as_comprobante  // Número de Comprobante
		//				   ad_fecha  // Fecha del comprobante
		//				   as_codban  // código de banco
		//				   as_ctaban  // cuenta de banco
		//				   as_tipdes  // Tipo de Destino
		//				   as_cedbene  // Código beneficiario
		//				   as_codpro  // Código proveedor 
		//                 rs_data    // Resultset con las cuentas contables
		//	  Description: Función que busca las cuentas contables de un comprobante
		//	   Creado Por: Ing. Arnaldo Suarez
		// Fecha Creación: 19/03/2010							Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecha=$this->io_funciones->uf_convertirdatetobd($ad_fecha);
		$ls_sql="SELECT sc_cuenta AS cuenta, descripcion, procede_doc As procede, documento, debhab, ".
				"       monto AS mondeb, monto AS monhab, orden  ".
				"  FROM scg_dt_cmp ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND procede = '".$as_procede."' ".
				"	AND comprobante = '".$as_comprobante."' ".
				"	AND fecha = '".$ad_fecha."' ".
				"	AND codban = '".$as_codban."' ".
				"	AND ctaban = '".$as_ctaban."' ".
				"   AND debhab = 'H'".
				" UNION ".
				"SELECT sc_cuenta AS cuenta, descripcion, procede_doc As procede, documento, debhab, ".
				"       monto AS mondeb, monto AS monhab, orden  ".
				"  FROM scg_dt_cmp ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"	AND procede = '".$as_procede."' ".
				"	AND comprobante = '".$as_comprobante."' ".
				"	AND fecha = '".$ad_fecha."' ".
				"	AND codban = '".$as_codban."' ".
				"	AND ctaban = '".$as_ctaban."' ".
				"   AND debhab = 'D'".
				" ORDER BY debhab DESC, orden ";	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Comprobante MÉTODO->uf_load_sccuentas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_load_cuentas_contables
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>