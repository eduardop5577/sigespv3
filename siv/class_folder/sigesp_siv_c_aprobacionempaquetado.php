<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_siv_c_aprobacionempaquetado
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
		//	     Function: sigesp_siv_c_aprobacionrecepcion
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
	function uf_load_empaquetado($ls_codemppro,$ad_fecregdes,$ad_fecreghas,$ls_codartemp,$as_tipooperacion)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_empaquetado
		//		   Access: public
		//		 Argument: ls_codemppro     // Numero de Solicitud de Viaticos
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$ls_codusu=$_SESSION["la_logusr"];
		$lb_valido = true;
		$ls_sql="SELECT codemppro, codartemp, denartemp, fecemppro, estemppro".
				"  FROM siv_empaquetado".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codemppro like '".$ls_codemppro."'".
				"   AND codartemp like '".$ls_codartemp."'".
				"   AND fecemppro>='".$ad_fecregdes."'".
				"   AND fecemppro<='".$ad_fecreghas."'".
				"   AND estemppro='".$as_tipooperacion."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_load_empaquetado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_movimiento($as_nummov,$ad_fecmov,$as_nomsol,$as_codusu,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_movimiento
		//         Access: public 
		//      Argumento: $as_nummov    // numero de movimiento
		//                 $as_fecmov    // fecha de movimiento
		//                 $as_nomsol    // nombre del solicitante
		//                 $as_codusu    // codigo del usuario
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un maestro de movimiento en la tabla de  siv_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_nummov=$this->io_keygen->uf_generar_numero_nuevo("SIV","siv_movimiento","nummov","SIVRCP",15,"","","");

		$ls_sql="INSERT INTO siv_movimiento ( nummov, fecmov, nomsol, codusu)".
				" VALUES ('".$as_nummov."','".$ad_fecmov."','".$as_nomsol."','".$as_codusu."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Aprobacion MÉTODO->uf_siv_insert_movimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		$arrResultado['as_nummov']=$as_nummov;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_insert_movimiento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_aprobacion($ls_codemppro,$ls_codartemp,$ld_fecemppro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_aprobacion
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y generar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_siv_c_articuloxalmacen.php");
		$io_art= new sigesp_siv_c_articuloxalmacen();
		require_once("sigesp_siv_c_movimientoinventario.php");
		$io_mov= new sigesp_siv_c_movimientoinventario();
		$lb_valido=true;
		$as_nomsol="Empaquetado";
			$ls_codprodoc="EMP";
		$ld_fecemppro= $this->io_funciones->uf_convertirdatetobd($ld_fecemppro);
		$rs_exis=$this->uf_select_dt_empaquetado($ls_codemppro);
		while((!$rs_exis->EOF)&& $lb_valido)
		{
			$ls_codart=$rs_exis->fields["codart"];
			$ls_codalmsal=$rs_exis->fields["codalmsal"];
			$ls_codalment=$rs_exis->fields["codalment"];
			$ls_opeinv=$rs_exis->fields["opeinv"];
			$ls_unidad=$rs_exis->fields["unidad"];
			$li_cantidad=$rs_exis->fields["cantidad"];
			$li_cosuni=$rs_exis->fields["cosuni"];
			if($ls_opeinv=="S")
			{
				$lb_valido=$io_art->uf_siv_chequear_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalmsal,$li_cantidad);
				if(!$lb_valido)
				{
					return false;
				}
			}
			$rs_exis->MoveNext();
		}
		$lb_valido=$this->uf_registrar_articulo($ls_codemppro,$ls_codartemp,$ld_fecemppro,$aa_seguridad);
		if($lb_valido)
		{
			$arrResultado=$this->uf_siv_insert_movimiento("",$ld_fecemppro,$as_nomsol,$aa_seguridad["logusr"],$aa_seguridad);
			$as_nummov = $arrResultado['as_nummov'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$rs_data=$this->uf_select_dt_empaquetado($ls_codemppro);
				while((!$rs_data->EOF)&& $lb_valido)
				{
					$ls_codart=$rs_data->fields["codart"];
					$ls_codalmsal=$rs_data->fields["codalmsal"];
					$ls_codalment=$rs_data->fields["codalment"];
					$ls_opeinv=$rs_data->fields["opeinv"];
					$ls_unidad=$rs_data->fields["unidad"];
					$li_cantidad=$rs_data->fields["cantidad"];
					$li_cosuni=$rs_data->fields["cosuni"];
					if($ls_opeinv=="S")
					{
						$lb_valido=$io_art->uf_siv_chequear_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalmsal,$li_cantidad);
						if ($lb_valido)
						{
							$lb_valido=$io_art->uf_siv_disminuir_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalmsal,$li_cantidad,$aa_seguridad);
							if($lb_valido)
							{
								$lb_valido=$this->uf_siv_procesar_dt_movimientotransferencia($this->ls_codemp,$as_nummov,$ls_codart,
																							   $ls_codalmsal,$ls_unidad,$li_cantidad,
																							   $li_cosuni,$ld_fecemppro,$ls_codemppro,
																							   $aa_seguridad);
							}
						}
						else
						{
							$this->io_sql->rollback;
							return false;	
						}
					}
					else
					{
						$lb_valido=$io_art->uf_siv_aumentar_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalment,$li_cantidad,$aa_seguridad);
						if($lb_valido)
						{
							$ls_opeinvent="ENT";
							$ls_codprodoc="ALM";
							$ls_promov="EMP";
							$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($this->ls_codemp,$as_nummov,$ld_fecemppro,
																			$ls_codart,$ls_codalment,$ls_opeinvent,
																			$ls_codprodoc,$ls_codemppro,$li_cantidad,
																			$li_cosuni,$ls_promov,$ls_codemppro,
																			$li_cantidad,$ld_fecemppro,$aa_seguridad);
						}
					}
					if($lb_valido===false)
					{
						return false;
					}
					$rs_data->MoveNext();
				}
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_empaquetado($ls_codemppro,"1",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_procesar_reverso_empaquetado($ls_codemppro,$ls_codartemp,$ld_fecemppro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_procesar_reverso_empaquetado
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y generar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_siv_c_articuloxalmacen.php");
		$io_art= new sigesp_siv_c_articuloxalmacen();
		require_once("sigesp_siv_c_movimientoinventario.php");
		$io_mov= new sigesp_siv_c_movimientoinventario();
		$lb_valido=true;
		$as_nomsol="Reverso de Empaquetado";
		$ls_codprodoc="REV";
		$ld_fecemppro= $this->io_funciones->uf_convertirdatetobd($ld_fecemppro);
		$rs_exis=$this->uf_select_dt_empaquetado($ls_codemppro);
		while((!$rs_exis->EOF)&& $lb_valido)
		{
			$ls_codart=$rs_exis->fields["codart"];
			$ls_codalmsal=$rs_exis->fields["codalmsal"];
			$ls_codalment=$rs_exis->fields["codalment"];
			$ls_opeinv=$rs_exis->fields["opeinv"];
			$ls_unidad=$rs_exis->fields["unidad"];
			$li_cantidad=$rs_exis->fields["cantidad"];
			$li_canartemp=$rs_exis->fields["canartemp"];
			$li_cosuni=$rs_exis->fields["cosuni"];
			break;
			$rs_exis->MoveNext();
		}
		$lb_valido=$io_art->uf_siv_chequear_articuloxalmacen($this->ls_codemp,$ls_codartemp,$ls_codalment,$li_canartemp);

		if($lb_valido)
		{
			$arrResultado=$this->uf_siv_insert_movimiento("",$ld_fecemppro,$as_nomsol,$aa_seguridad["logusr"],$aa_seguridad);
			$as_nummov = $arrResultado['as_nummov'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$rs_data=$this->uf_select_dt_empaquetado($ls_codemppro);
				while((!$rs_data->EOF)&& $lb_valido)
				{
					$ls_codart=$rs_data->fields["codart"];
					$ls_codalmsal=$rs_data->fields["codalmsal"];
					$ls_codalment=$rs_data->fields["codalment"];
					$ls_opeinv=$rs_data->fields["opeinv"];
					$ls_unidad=$rs_data->fields["unidad"];
					$li_cantidad=$rs_data->fields["cantidad"];
					$li_cosuni=$rs_data->fields["cosuni"];
					if($ls_opeinv=="E")
					{ 
						$lb_valido=$io_art->uf_siv_chequear_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalment,$li_cantidad);
						if ($lb_valido)
						{
							$lb_valido=$io_art->uf_siv_disminuir_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalment,$li_cantidad,$aa_seguridad);
							if($lb_valido)
							{
								$ls_opeinvent="SAL";
								$ls_codprodoc="REV";
								$ls_promov="EMP";
								$ls_candes=0;
								$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($this->ls_codemp,$as_nummov,$ld_fecemppro,
																				$ls_codart,$ls_codalment,$ls_opeinvent,
																				$ls_codprodoc,$ls_codemppro,$li_cantidad,
																				$li_cosuni,$ls_promov,$ls_codemppro,
																				$ls_candes,$ld_fecemppro,$aa_seguridad);
							}
						}
						else
						{
							$this->io_sql->rollback;
							return false;	
						}
					}
					else
					{
						$lb_valido=$io_art->uf_siv_aumentar_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalmsal,$li_cantidad,$aa_seguridad);
						if($lb_valido)
						{
							$ls_opeinvent="ENT";
							$ls_codprodoc="REV";
							$ls_promov="EMP";
							$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($this->ls_codemp,$as_nummov,$ld_fecemppro,
																			$ls_codart,$ls_codalmsal,$ls_opeinvent,
																			$ls_codprodoc,$ls_codemppro,$li_cantidad,
																			$li_cosuni,$ls_promov,$ls_codemppro,
																			$li_cantidad,$ld_fecemppro,$aa_seguridad);
						}
					}
					if($lb_valido===false)
					{
						return false;
					}
					$rs_data->MoveNext();
				}
			}
		}
		else
		{
			return false;
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_empaquetado($ls_codemppro,"0",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------



	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_reverso($ls_codemppro,$ls_codartemp,$ld_fecemppro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_aprobacion
		//         Access: public  
		//      Argumento: $ls_codsolvia // codigo de solicitud de viaticos 
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga obtener los datos de la solicitud de viaticos y generar la recepcion de documentos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/08/2009							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_siv_c_articuloxalmacen.php");
		$io_art= new sigesp_siv_c_articuloxalmacen();
		require_once("sigesp_siv_c_movimientoinventario.php");
		$io_mov= new sigesp_siv_c_movimientoinventario();
		$lb_valido=true;
		$as_nomsol="Empaquetado";
		if($as_estpro==0)
		{
			$ls_codprodoc="ORD";
		}
		else
		{
			$ls_codprodoc="FAC";
		}
		$ld_fecemppro= $this->io_funciones->uf_convertirdatetobd($ld_fecemppro);
		$lb_valido=$this->uf_registrar_articulo($ls_codemppro,$ls_codartemp,$ld_fecemppro,$aa_seguridad);
		if($lb_valido)
		{
			$arrResultado=$this->uf_siv_insert_movimiento("",$ld_fecemppro,$as_nomsol,$aa_seguridad["logusr"],$aa_seguridad);
			$as_nummov = $arrResultado['as_nummov'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				$rs_data=$this->uf_select_dt_empaquetado($ls_codemppro);
				while((!$rs_data->EOF)&& $lb_valido)
				{
					$ls_codart=$rs_data->fields["codart"];
					$ls_codalmsal=$rs_data->fields["codalmsal"];
					$ls_codalment=$rs_data->fields["codalment"];
					$ls_opeinv=$rs_data->fields["opeinv"];
					$ls_unidad=$rs_data->fields["unidad"];
					$li_cantidad=$rs_data->fields["cantidad"];
					$li_cosuni=$rs_data->fields["cosuni"];
					if($ls_opeinv=="E")
					{
						$lb_valido=$io_art->uf_siv_chequear_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalmsal,$li_cantidad);
						if ($lb_valido)
						{
							$lb_valido=$io_art->uf_siv_disminuir_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalmsal,$li_cantidad,$aa_seguridad);
							if($lb_valido)
							{
								$lb_valido=$this->uf_siv_procesar_dt_movimientotransferencia($this->ls_codemp,$as_nummov,$ls_codart,
																							   $ls_codalmsal,$ls_unidad,$li_cantidad,
																							   $li_cosuni,$ld_fecemppro,$ls_codemppro,
																							   $aa_seguridad);
							}
						}
					}
					else
					{
						$lb_valido=$io_art->uf_siv_aumentar_articuloxalmacen($this->ls_codemp,$ls_codart,$ls_codalment,$li_cantidad,$aa_seguridad);
						if($lb_valido)
						{
							$ls_opeinvent="ENT";
							$ls_codprodoc="ALM";
							$ls_promov="EMP";
							$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($this->ls_codemp,$as_nummov,$ld_fecemppro,
																			$ls_codart,$ls_codalment,$ls_opeinvent,
																			$ls_codprodoc,$ls_codemppro,$li_cantidad,
																			$li_cosuni,$ls_promov,$ls_codemppro,
																			$li_cantidad,$ld_fecemppro,$aa_seguridad);
						}
					}
					$rs_data->MoveNext();
				}
			}
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_update_empaquetado($ls_codemppro,"1",$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_dt_empaquetado($ls_codemppro)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_empaquetado
		//		   Access: public
		//		 Argument: as_codsolvia     // Numero de Solicitud de Viaticos
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_sql="SELECT codart, opeinv,siv_empaquetado.fecemppro, unidad, cantidad, cosuni, costot, codalmsal, codalment,canartemp".
				"  FROM siv_dt_empaquetado,siv_empaquetado ".
				" WHERE siv_dt_empaquetado.codemp='".$this->ls_codemp."'".
				"   AND siv_dt_empaquetado.codemppro='".$ls_codemppro."'".
				"   AND siv_dt_empaquetado.codemp=siv_empaquetado.codemp".
				"   AND siv_dt_empaquetado.codemppro=siv_empaquetado.codemppro";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_select_dt_empaquetado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		return $rs_data;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_registrar_articulo($ls_codemppro,$ls_codartemp,$ld_fecemppro,$aa_seguridad)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_registrar_articulo
		//		   Access: public
		//		 Argument: as_codsolvia     // Numero de Solicitud de Viaticos
		//                 ad_fecregdes     // Fecha (Emision) de inicio de la Busqueda
		//                 ad_fecreghas     // Fecha (Emision) de fin de la Busqueda
		//                 as_tipooperacion // Codigo de la Unidad Ejecutora
		//	  Description: Función que busca las solicitudes  a aanular o reversar anulacion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 13/04/2008								Fecha Última Modificación : 05/02/2009
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido = true;
		$ls_sql="SELECT codartemp, denartemp,siv_empaquetado.codtipart, codunimed, sc_cuenta, spg_cuenta".
				"  FROM siv_empaquetado,siv_tipoarticulo ".
				" WHERE siv_empaquetado.codemp='".$this->ls_codemp."'".
				"   AND siv_empaquetado.codemppro='".$ls_codemppro."'".
				"   AND siv_empaquetado.codtipart=siv_tipoarticulo.codtipart";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_registrar_articulo2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		while((!$rs_data->EOF)&& $lb_valido)
		{
			$ls_codartemp=$rs_data->fields["codartemp"];
			$ls_denartemp=$rs_data->fields["denartemp"];
			$ls_codunimed=$rs_data->fields["codunimed"];
			$ls_codtipart=$rs_data->fields["codtipart"];
			$ls_sccuenta=$rs_data->fields["sc_cuenta"];
			$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
			$ls_sccuentainv="";
			$lb_existe=$this->uf_siv_select_articulo($ls_codartemp);
			if(!$lb_existe)
			{
			
				$lb_valido=$this->uf_siv_insert_articulo($ls_codartemp,$ls_denartemp,$ls_codunimed,$ls_sccuenta,$ls_spgcuenta,
																$ls_sccuentainv,$ls_codtipart,$ld_fecemppro,$aa_seguridad);
			}
			$rs_data->MoveNext();
		}
		return $lb_valido;
	}// end function uf_load_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function  uf_siv_insert_articulo($ls_codartemp,$ls_denartemp,$ls_codunimed,$ls_sccuenta,$ls_spgcuenta,$ls_sccuentainv,$ls_codtipart,$ld_fecemppro,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_articulo
		//         Access: public (sigesp_siv_d_articulo)
		//     Argumentos: $as_codemp     //codigo de empresa                 $as_codart    // codigo de articulo
		//				   $as_denart     // denominacion del articulo        $as_codtipart // codigo de tipo de articulo
		//			       $as_codunimed  // codigo de unidad de medida       $ad_feccreart // fecha de creacion del articulo
		//				   $as_obsart     // observacion del articulo		  $ai_exiart    // existencia del articulo
		//				   $ai_exiiniart  // existencia inicial del articulo  $ai_minart    // existencia minima del articulo
		//				   $ai_maxart     // existencia maxima del articulo   $ai_prearta   // precio A del articulo
		//				   $ai_preartb    // precio B del articulo		      $ai_preartc   // precio C del articulo
		//				   $ai_preartd    // precio D del articulo			  $ad_fecvenart // fecha de vencimiento del articulo
		//				   $as_spg_cuenta // numero de cuenta presupuestaria  $ai_pesart    // peso del articulo
		//				   $ai_altart     // altura del articulo			  $ai_ancart    // ancho del articulo
		//				   $ai_proart     // profundidad del articulo		  $as_codcatsig // codigo del catalogo sigecof
		//				   $as_sccuenta   // cuenta contable de gasto         $aa_seguridad // arreglo de registro de seguridad
		//                 $as_codmil     // codigo del catalogo milco
		//				   $as_serart     // serial del articulo			  $as_fabart    // fabricante del articulo
		//				   $as_ubiart     // ubicacion del  articulo		  $as_docart    // documento del articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un articulo en la tabla de  siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 30/08/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_exiart=0;
		$ai_minart=0;
		$ai_maxart=0;
		$ai_exiiniart=0;
		$ai_prearta=0;
		$ai_preartb=0;
		$ai_preartc=0;
		$ai_preartd=0;
		$ai_pesart=0;
		$ai_altart=0;
		$ai_ancart=0;
		$ai_proart=0;
		$ai_reoart=0;
		$ad_fecvenart="1900-01-01";
		$ad_fecvenart="1900-01-01";
		$as_codartpri="--------------------";
		$as_codcatsig="---------------";
		$as_obsart="ARTICULO GENERADO EN PROCESO DE EMPAQUETADO";
		$ls_sql="INSERT INTO siv_articulo (codemp,codart,denart,codtipart,codunimed,feccreart,obsart,exiart,exiiniart, ".
				"                          minart,maxart,prearta,preartb,preartc,preartd,fecvenart,spg_cuenta,pesart,altart,".
				"                          ancart, proart,fotart,codcatsig,sc_cuenta,reoart,".
				"                          estartgen,codartpri,lote,carcom,cod_pro,sc_cuentainv,estproter)".
				" VALUES ('".$this->ls_codemp."','".$ls_codartemp."','".$ls_denartemp."','".$ls_codtipart."','".$ls_codunimed."',".
				"         '".$ld_fecemppro."','".$as_obsart."',".$ai_exiart.",".$ai_exiiniart.",".$ai_minart.",".$ai_maxart.",".
				"          ".$ai_prearta.",".$ai_preartb.",".$ai_preartc.",".$ai_preartd.",'".$ad_fecvenart."','".$ls_spgcuenta."',".
				"          ".$ai_pesart.",".$ai_altart.",".$ai_ancart.",".$ai_proart.",'".$as_fotart."','".$as_codcatsig."',".
				"         '".$ls_sccuenta."',".$ai_reoart.",".
				"         '0','".$as_codartpri."','','0','----------','".$ls_sccuentainv."','1')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_insert_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
			$lb_valido=false;
		}
		else
		{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó el Articulo ".$as_codart." Mediante el proceso de Empaquetado ";
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;		
	} // end  function  uf_siv_insert_articulo
	//-----------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_dt_movimiento($as_nummov,$ad_fecmov,$as_codart,$as_codalm,$as_opeinv,$as_codprodoc,$as_numdoc,
										 $ai_canart,$ai_cosart,$as_promov,$as_numdocori,$ai_candesart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_movimiento
		//         Access: public 
		//      Argumento: $ai_canart    // cantidad de articulos
		//                 $as_nummov    // numero de movimiento				$ai_cosart    // costo del articulo
		//                 $ad_fecmov    // fecha de movimiento					$as_promov    // procedencia del documento
		//                 $as_codart    // codigo de articulo					$as_numdocori // numero de documento original
		//                 $as_codalm    // codigo de almacen					$as_numdoc    // numero de documento
		//                 $as_opeinv    // codigo de operacion de inventario	$ad_fecdesart // fecha de el ultimo despacho del articulo
		//                 $as_codprodoc // codigo de procedencia del documento	$aa_seguridad // arreglo de registro de seguridad	
		//                 $ai_candesart // cantidad de articulos que restan por despachar
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de movimiento generado en cualquiera de los procesos de inventario,
		//				   en la tabla de  siv_dt_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_movimiento (codemp,nummov,fecmov,codart,codalm,opeinv,codprodoc,numdoc,canart,cosart,promov,".
				"                               numdocori,candesart,fecdesart)".
				" VALUES ('".$this->ls_codemp."','".$as_nummov."','".$ad_fecmov."','".$as_codart."','".$as_codalm."','".$as_opeinv."',".
				"         '".$as_codprodoc."','".$as_numdoc."','".$ai_canart."','".$ai_cosart."','".$as_promov."','".$as_numdocori."',".
				"         '".$ai_candesart."','".$ad_fecmov."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_mensajes->message("CLASE->Aprobacion MÉTODO->uf_siv_insert_dt_movimiento ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		return $lb_valido;
	} // end function uf_siv_insert_dt_movimiento
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_aumentar_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_aumentar_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codemp      //codigo de empresa 
		//                 $as_codart      // codigo de articulo
		//                 $as_codalm      //codigo de almacen
		//                 $ai_cantidad    // cantidad de articulos
		//                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que deacuerdo a los resultados de una busqueda (select), inserta o actualiza cierta cantidad de
		//				    articulos en un almacen determinado en la tabla de  siv_articuloalmacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if(!($this->uf_siv_select_articuloxalmacen($as_codart,$as_codalm)))
		{
			$lb_valido=$this->uf_siv_insert_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_siv_sumar_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_articuloxalmacen($as_codart,$as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codart // codigo de articulo
		//                 $as_codalm //codigo de almacen
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que verifica si existe un articulo en un determinado almacen en la tabla de  siv_articuloalmacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT codart FROM siv_articuloalmacen  ".
				  " WHERE codemp='".$this->ls_codemp."' ".
				  " AND codart='".$as_codart."' ".
				  " AND codalm='".$as_codalm."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_select_articuloxalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	} // end  function uf_siv_select_articuloxalmacen
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_insert_articuloxalmacen($as_codart,$as_codalm,$as_existencia,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codart      // codigo de articulo
		//                 $as_codalm      //codigo de almacen
		//                 $as_existencia  // codigo del usuario
		//                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que registra cierta cantidad de un articulo en determinado almacen en la tabla siv_articuloalmacen
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_articuloalmacen (codemp, codart, codalm, existencia)".
				" VALUES ('".$this->ls_codemp."','".$as_codart."','".$as_codalm."','".$as_existencia."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_insert_articuloxalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} // end function uf_siv_insert_articuloxalmacen
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_sumar_articuloxalmacen($as_codart,$as_codalm,$ai_cantidad,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_sumar_articuloxalmacen
		//         Access: public 
		//      Argumento: $as_codart      // codigo de articulo
		//                 $as_codalm      //codigo de almacen
		//                 $ai_cantidad    // cantidad de articulos
		//                 $aa_seguridad   // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que aumenta la cantidad de un articulo en un almacen determinado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql= "UPDATE siv_articuloalmacen ".
		 		  "   SET existencia= (existencia + '".$ai_cantidad."') ".
				  " WHERE codemp='".$this->ls_codemp."' ".
				  "   AND codart='".$as_codart."' ".
				  "   AND codalm='".$as_codalm."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_sumar_articuloxalmacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();

		}
		else
		{
			$lb_valido=true;
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Aumentó ".$ai_cantidad." Articulos ".$as_codart." del Almacén ".$as_codalm." de la Empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
	    return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_actualizar_cantidad_articulos($as_codart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_actualizar_cantidad_articulos
		//         Access: public 
		//      Argumento: $as_codemp     //codigo de empresa 
		//                 $as_codart     // codigo de articulo
		//                 $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que calcula la cantidad total de un articulo entre todos los almacenes para luego actualizar dicha cantidad
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_exec=-1;
		$li_totart=0;
		$ls_sql = "SELECT existencia FROM siv_articuloalmacen  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codart='".$as_codart."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		while($row=$this->io_sql->fetch_row($rs_data))
		{
			$li_cantalm=$row["existencia"];
			$li_totart=$li_totart + $li_cantalm;
		}
		$lb_valido=$this->uf_siv_update_total_articulo($as_codart,$li_totart,$aa_seguridad);		
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_update_total_articulo($as_codart,$ai_cantidad,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_total_articulo
		//         Access: public 
		//      Argumento: $as_codart     // codigo de articulo
		//                 $as_codalm     // codigo de almacen
		//                 $as_cantidad   // cantidad de articulos
		//                 $aa_seguridad  // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que actualiza la cantidad de un articulo en un almacen determinado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_existe=$this->uf_siv_select_articulo($as_codart);
		if($lb_existe)
		{
			 $ls_sql= "UPDATE siv_articulo ".
			 		  "   SET exiart='".$ai_cantidad."' ".
					  " WHERE codemp='".$this->ls_codemp."' ".
					  "   AND codart='".$as_codart."'";
				$li_row = $this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_update_total_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					$lb_valido=true;
				}
		} 
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_select_articulo($as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_articulo
		//         Access: public 
		//      Argumento: $as_codart // codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que busca un articulo en la tabla de  siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql = "SELECT codart FROM siv_articulo  ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codart='".$as_codart."'" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articuloxalmacen MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_empaquetado($as_codemppro,$ls_estemppro,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_empaquetado
		//         Access: public  
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codemppro // codigo de empaquetado
		//        		   $aa_seguridad // arreglo de seguridad
		//	      Returns: Retorna un Booleano
		//	  Description: Función que se encarga de poner en estado de registrada a una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/11/2006							Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql=" UPDATE siv_empaquetado".
				"    SET estemppro='".$ls_estemppro."'".
				"  WHERE codemp='".$this->ls_codemp."'".
				"    AND codemppro='".$as_codemppro."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		{
			$this->io_mensajes->message("CLASE->aprobacion METODO->uf_update_empaquetado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion= "Modificó el Estatus del empaquedato ".$as_codemppro." a ".$ls_estemppro." Asociada a la empresa ".$this->ls_codemp;
			$ls_variable= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               ///////////////////////////
			$lb_valido=true;
		}
		return $lb_valido;
	} // fin function uf_scv_update_rutas
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_procesar_dt_movimientotransferencia($as_codemp,$as_nummov,$as_codart,$as_codalm,$as_unidad,$ai_canart,
	                                                    $ai_preuniart,$ad_fecemi,$as_numtra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_dt_movimientotransferencia
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa							$as_numorddes // numero de orden de despacho
		//                 $as_codart    // codigo de articulo							$as_codalm    // codigo de almacén								
		//                 $as_unidad    // codigo de unidad M-->Mayor D->Detal		 	$ai_canorisolsep // cantidad de articulos de la SEP
		//                 $ai_canart    // cantidad despachada de articulos			$ai_preuniart    // precio unitario del articulo
		//                 $ai_canoriart // codigo de procedencia del documento			$as_nummov       // numero de movimiento
		//                 $ad_fecdesaux // fecha del despacho							$as_numsol      // numero de la SEP
		//                 $as_numconrec // comprobante (numero concecutivo para hacer unica la recepcion)
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Función que verifica que metodo de inventario se esta utilizando y además va buscando los precios unitarios 
	    //				   en caso de que no existan suficientes artiulos al mismo precio y procede a llamar al metodo de insert_dt_movimientos
	    //				   y al insert_dt_despacho para ingresarlo en la tabla siv_dt_despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("sigesp_siv_c_movimientoinventario.php");
		$io_mov= new sigesp_siv_c_movimientoinventario();
		$lb_valido=true;
		$ls_metodo="";
		$rs_metodo="";
		$arrResultado=$this->uf_select_metodo($ls_metodo);
		$ls_metodo = $arrResultado['ls_metodo'];
		$lb_valido = $arrResultado['lb_valido'];
		if ($lb_valido)
		{
			$arrResultado=$this->uf_select_movimiento($ls_metodo,$rs_metodo,$as_codart,$as_codalm);
			$rs_metodo = $arrResultado['rs_metodo'];
			$lb_valido = $arrResultado['lb_valido'];
			if($lb_valido)
			{
				if($ls_metodo!="CPP")
				{
					$lb_break=false;
					$li_diferencia=0;
					$li_i=0;
					while(($row=$this->io_sql->fetch_row($rs_metodo))&&(!$lb_break))
					{
						$li_preuniart=$row["cosart"];
						$ls_numdocori=$row["numdocori"];
						$ls_nummov=$row["nummov"];
						$ls_codalm=$row["codalm"];
						
						if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) total FROM siv_dt_movimiento".
									" WHERE codemp='".$as_codemp."'".
									"   AND codart='".$as_codart."'".
									"   AND codalm='".$as_codalm."'".
									"   AND numdocori='".$ls_numdocori."'".
									"   AND CONCAT(promov,numdocori) NOT IN".
									" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
									"   WHERE opeinv ='REV')".
									" ORDER BY nummov";
						}
						if($this->ls_gestor=="INFORMIX")
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS total FROM siv_dt_movimiento".
									" WHERE codemp='".$as_codemp."'".
									"   AND codart='".$as_codart."'".
									"   AND codalm='".$as_codalm."'".
									"   AND numdocori='".$ls_numdocori."'".
									"   AND promov  || numdocori NOT IN".
									" (SELECT promov || numdocori FROM siv_dt_movimiento".
									"   WHERE opeinv ='REV')".
									" GROUP BY nummov"; 
						}
						else
						  {
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS total FROM siv_dt_movimiento".
									" WHERE codemp='".$as_codemp."'".
									"   AND codart='".$as_codart."'".
									"   AND codalm='".$as_codalm."'".

									"   AND numdocori='".$ls_numdocori."'".
									"   AND promov  || numdocori NOT IN".
									" (SELECT promov || numdocori FROM siv_dt_movimiento".
									"   WHERE opeinv ='REV')".
									" GROUP BY nummov".
									" ORDER BY nummov"; 
						  }
						$rs_data1=$this->io_sql->select($ls_sql);
						if($row1=$this->io_sql->fetch_row($rs_data1))
						{
							$li_existencia=$row1["total"];
							if ($li_existencia > 0)
							{
								$lb_encontrado=true;
								$li_i=$li_i + 1;

								if ($li_existencia < $ai_canart)
								{
									$ai_canart= $ai_canart-$li_existencia;

									$lb_valido=$this->uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$ls_codalm,$ls_nummov,
																							$ls_numdocori,$li_existencia);
									if ($lb_valido)
									{
										$ls_opeinv="SAL";
										$ls_promov="PRO";
										$ls_codprodoc="ALM";
										$li_candesart="0.00";
										$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecemi,
																						  	  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																							  $as_numtra,$li_existencia,$li_preuniart,$ls_promov,
																						  	  $as_numtra,$li_candesart,$ad_fecemi,
																							  $aa_seguridad);
									}			
															
								}  // fin  if ($li_existencia < $ai_canart)
								elseif($li_existencia >= $ai_canart)
								{
									$lb_valido=$this->uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$ls_codalm,
																						   $ls_nummov,$ls_numdocori,$ai_canart);
									if ($lb_valido)
									{
										$ls_opeinv="SAL";
										$ls_promov="PRO";
										$ls_codprodoc="ALM";
										$li_candesart="0.00";
										$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecemi,
																						  	  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																							  $as_numtra,$ai_canart,$li_preuniart,$ls_promov,
																						  	  $as_numtra,$li_candesart,$ad_fecemi,
																							  $aa_seguridad);
										if($lb_valido)
										{
											$lb_break=true;
										}
									}
								}
								if(!$lb_valido)
								{
									$lb_break=true;
								}
							}  // fin  ($li_existencia > 0)
						}  //fin  if($row1=$io_sql->fetch_row($rs_datas1))
					}// fin  while(($row=$io_sql->fetch_row($rs_metodo))&&(!$lb_break))
				}// fin  if($ls_metodo!="CPP")
				else
				{
					if($row=$this->io_sql->fetch_row($rs_metodo))
					{
						$li_preuniart=$row["cosart"];
						$ls_numdocori="";   
						$ls_opeinv="SAL";
						$ls_promov="PRO";
						$ls_codprodoc="ALM";
						$li_candesart="0.00";
						$lb_valido=$io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecemi,
																			  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																			  $as_numtra,$ai_canart,$li_preuniart,$ls_promov,
																			  $as_numtra,$li_candesart,$ad_fecemi,
																			  $aa_seguridad);
					}// fin  if($row=$this->io_sql->fetch_row($rs_metodo))
				}// fin  else($ls_metodo!="CPP")
			}
		}
		return $lb_valido;
	}// end  function uf_siv_procesar_dt_movimientotransferencia
	function uf_select_metodo($ls_metodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_metodo
		//         Access: private
		//      Argumento: $ls_metodo    // metodo de inventario
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que metodo de inventario esta siendo utilizado actualmente.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT * FROM siv_config";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->transferencia MÉTODO->uf_select_metodo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_metodo=$row["metodo"];
			}
			else
			{
				$lb_valido=false;
				$this->io_msg->message("No se ha definido la configuración de inventario");
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ls_metodo']=$ls_metodo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end  function uf_select_metodo
	
	function uf_select_movimiento($ls_metodo,$rs_metodo,$as_codart,$as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_movimiento
		//         Access: private
		//      Argumento: $ls_metodo    // metodo de inventario
		//                 $rs_metodo    // result set de la operacion del select
		//                 $as_codart    // codigo de articulo
		//                 $as_codalm    // codigo de almacén
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los movimientos que no han sido reversados y los ordena segun sea el el metodo 
	    //				   de inventario (en caso de ser FIFO ó LIFO), o saca el promedio si es Costo Promedio Ponderado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($ls_metodo=="FIFO")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov";
			}
			else
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov"; 
			}
			
			$rs_metodo=$this->io_sql->select($ls_sql);
		}
		if($ls_metodo=="LIFO")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			else
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			$rs_metodo=$this->io_sql->select($ls_sql);
		}	
		if($ls_metodo=="CPP")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT Avg(cosart) as cosart".
						" FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						" (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			if($this->ls_gestor=="INFORMIX")
			{
				$ls_sql="SELECT Avg(cosart) as cosart, nummov".
						" FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" GROUP BY cosart,nummov".
						" ORDER BY nummov DESC"; 
			}
			else
			{
				$ls_sql="SELECT Avg(cosart) as cosart".
						" FROM siv_dt_movimiento".
						" WHERE  codart='".$as_codart."'".
						"   AND codalm='".$as_codalm."'".
						"   AND promov || numdocori NOT IN".
						" (SELECT promov || numdocori FROM siv_dt_movimiento".
						"   WHERE opeinv ='REV')".
						" GROUP BY cosart,nummov".
						" ORDER BY nummov DESC"; 
			}
			$rs_metodo=$this->io_sql->select($ls_sql);
		}	
		if($rs_metodo===false)
		{
			$this->io_msg->message("CLASE->transferencias MÉTODO->uf_select_movimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_metodo']=$rs_metodo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end function uf_select_movimiento

	function uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$as_codalm,$as_nummov,$ls_numdocori,$ai_cantidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_disminuir_articuloxmovimiento
		//         Access: private
		//      Argumento: $as_codemp       // codigo de empresa
		//                 $as_codart       // codigo de articulo
		//                 $as_codalm       // codigo de almacen
		//                 $ls_numdocori    // numero original de la entrada de suministros a almacén
		//                 $as_nummov       // numero de movimiento
		//                 $as_cantidad     // cantidad de articulos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que disminuye la cantidad de articulos proveniente de un movimiento en la tabla siv_dt_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $rs_disart=-1;
		 $ld_date= date("Y-m-d");
		 $ls_sql= "UPDATE siv_dt_movimiento".
		 		  "   SET candesart= (candesart - '". $ai_cantidad ."'), ".
		 		  "       fecdesart='".$ld_date."'".
				  " WHERE codemp='".$as_codemp."'".
				  " AND   opeinv='ENT'".
				  " AND   nummov='".$as_nummov."'".
				  " AND   codart='".$as_codart."'".
				  " AND   codalm='".$as_codalm."'".
				  " AND   numdocori='" . $ls_numdocori ."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->transferencia MÉTODO->uf_siv_disminuir_articuloxmovimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} // end  function uf_siv_disminuir_articuloxmovimiento



}
?>