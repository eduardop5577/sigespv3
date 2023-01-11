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

class sigesp_lib_generar_consecutivo
 {
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $ls_codemp;
	var $ls_logusr;
	var $io_dscuentas;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_c_generar_consecutivo
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
               // $io_conexion->debug=true;
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones_db.php");
		$this->io_function_db=new class_funciones_db($io_conexion);
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_datastore.php");
		$this->io_dscuentas=new class_datastore();
		$this->io_dscargos=new class_datastore();
                $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_logusr=$_SESSION["la_logusr"];
	  	$this->ls_gestor    = $_SESSION["ls_gestor"];
	}// end function sigesp_c_generar_consecutivo
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sep_p_solicitud.php)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_fecha);
                unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_prefijo($as_codsis,$as_procede)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_prefijo
		//		   Access: private
		//		 Argument: $as_codsis   // Codigo de Sistema
		//				   $as_procede  // Procedencia del Documento
		//	  Description: Función que Obtiene el prefijo del numero de documento (en caso de poseerlo)
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_prefijo="00";
		$ls_sql="SELECT sigesp_prefijos.codsis, sigesp_prefijos.procede, sigesp_prefijos.id, sigesp_prefijos.prefijo, sigesp_dt_prefijos.codusu".
				"  FROM sigesp_prefijos,sigesp_dt_prefijos ".
				" WHERE sigesp_prefijos.codemp='".$this->ls_codemp."'".
				"   AND sigesp_prefijos.codsis='".$as_codsis."'".
				"   AND sigesp_prefijos.procede='".$as_procede."'".
				"   AND sigesp_dt_prefijos.codusu='".$this->ls_logusr."'".
				"   AND sigesp_prefijos.estact=1".
				"   AND sigesp_prefijos.codemp=sigesp_dt_prefijos.codemp".
				"   AND sigesp_prefijos.id=sigesp_dt_prefijos.id".
				"   AND sigesp_prefijos.codsis=sigesp_dt_prefijos.codsis".
				"   AND sigesp_prefijos.procede=sigesp_dt_prefijos.procede".
				"   AND sigesp_prefijos.prefijo=sigesp_dt_prefijos.prefijo".
				" ORDER BY prefijo";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_load_prefijo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			$lb_ok=false;
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i=$li_i+1;
				$ls_codusu=rtrim($row["codusu"]);
				if($ls_codusu!="--")
				{
					if($ls_codusu==$this->ls_logusr)
					{
						$ls_prefijo=$row["prefijo"];
						$lb_ok=true;
						break;
					}
				}
				else
				{
						$lb_ok=true;
						$ls_prefijo=$row["prefijo"];
				}
			}
			if($li_i==0)
			{
				$lb_ok=true;
			}
			if(!$lb_ok)
			{
				if(($as_procede!="SOCCOC")&&($as_procede!="SOCCOS"))
				{
					$this->io_mensajes->message("Este documento está configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor diríjase al Administrador del Sistema");
				}
				return false;
			}
			
			$this->io_sql->free_result($rs_data);	
		}
		return $ls_prefijo;
	}// end function uf_load_prefijo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_numero_inicial($as_campo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_numero_inicial
		//		   Access: private
		//		 Argument: $as_campo   // Nombre del Campo que Contiene el Valor Inicial del Documento
		//	  Description: Función que el Valor Inicial del Documento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nroini="";
		if($as_campo=="")
		{
			return $ls_nroini;
		}
		$ls_sql="SELECT ".$as_campo."".
				"  FROM sigesp_empresa ".
				" WHERE codemp='".$this->ls_codemp."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_load_numero_inicial ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nroini=$row[$as_campo];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ls_nroini;
	}// end function uf_load_numero_inicial
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_data_existente($as_tabla,$as_campo,$as_filtro,$as_valor,$as_prefijo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_data_existente
		//		   Access: private
		//		 Argument: $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $as_prefijo // Prefijo del numero de comprobante
		//	  Description: Función que obtiene el ultimo valor de la tabla indicada
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nroact="";
		$ls_criterio="";
		$lb_valido=$this->io_function_db->uf_select_column($as_tabla,'codemp');	
		if($lb_valido==false)
		{
			if(!empty($as_filtro))
			{
				$ls_criterio=$ls_criterio. " WHERE ".$as_filtro."='".$as_valor."'";
			}
			if($ls_criterio=="")
			{
				$ls_criterio=" WHERE ".$as_campo." LIKE '".$as_prefijo."%'";
			}
			else
			{
				$ls_criterio=$ls_criterio. " AND ".$as_campo." LIKE '".$as_prefijo."%'";
			}
			if($ls_criterio=="")
			{
				$ls_criterio=" WHERE substr(".$as_campo.", 1, 1) != '-'";
			}
			else
			{
				$ls_criterio=$ls_criterio. " AND substr(".$as_campo.", 1, 1) != '-'";
			}
		   switch (strtoupper($this->ls_gestor))
		   {
				case "INFORMIX":
					$ls_sql="SELECT LIMIT 1 ".$as_campo." as campo".
							"  FROM ".$as_tabla." ".
							" ".$ls_criterio." ".
							" ORDER BY ".$as_campo." DESC ";
				break;
				
				case "OCI8PO":
					$ls_sql="SELECT * FROM".
							"(SELECT ".$as_campo." as campo".
							"  FROM ".$as_tabla." ".
							" ".$ls_criterio." ".
							" ORDER BY ".$as_campo." DESC)".
							" WHERE rownum<=1 ";
				break;
				
				default: // POSTGRES
					$ls_sql="SELECT ".$as_campo." as campo".
							"  FROM ".$as_tabla." ".
							" ".$ls_criterio." ".
							" ORDER BY ".$as_campo." DESC LIMIT 1";
				break;
		   }
		}
		else
		{
			if(!empty($as_filtro))
			{
				$ls_criterio=$ls_criterio. " AND ".$as_filtro."='".$as_valor."'";
			}
	
			$ls_criterio=$ls_criterio. " AND ".$as_campo." LIKE '".$as_prefijo."%'";
			if($ls_criterio=="")
			{
				$ls_criterio=" WHERE substr(".$as_campo.", 1, 1) != '-'";
			}
			else
			{
				$ls_criterio=$ls_criterio. " AND substr(".$as_campo.", 1, 1) != '-'";
			}
		   switch (strtoupper($this->ls_gestor))
			{
				case "INFORMIX":
					$ls_sql="SELECT LIMIT 1 ".$as_campo." as campo".
							"  FROM ".$as_tabla." ".
							" WHERE codemp='".$this->ls_codemp."'".
							" ".$ls_criterio." ".
							" ORDER BY ".$as_campo." DESC ";
				break;
				
				case "OCI8PO":
					$ls_sql="SELECT * FROM".
							"(SELECT ".$as_campo." as campo".
							"  FROM ".$as_tabla." ".
							" WHERE codemp='".$this->ls_codemp."'".
							" ".$ls_criterio." ".
							" ORDER BY ".$as_campo." DESC)".
							" WHERE rownum<=1 ";
				break;

				default: // POSTGRES
					$ls_sql="SELECT ".$as_campo." as campo".
							"  FROM ".$as_tabla." ".
							" WHERE codemp='".$this->ls_codemp."'".
							" ".$ls_criterio." ".
							" ORDER BY ".$as_campo." DESC LIMIT 1";
				break;
			
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_load_data_existente ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nroact=$row["campo"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $ls_nroact;
	}// end function uf_load_data_existente
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_numero_generado($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,$as_valor,$as_numero)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_numero_generado
		//		   Access: private
		//		 Argument: $as_codsis  // Codigo de Sistema
		//				   $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $ai_loncam  // Longitud del Campo
		//				   $as_camini  // Nombre del campo que tiene el valor inicial del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $as_numero  // Valor a verificar
		//				   $ai_estgen  // Indica si se esta Generando el Numero por que el Actual ya existe o no.
		//	  Description: Función que verifica si un numero generado esta disponible
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_nroact="";
		$ls_nroant=$as_numero;
		$ls_criterio="";
		if(!empty($as_filtro))
		{
			$ls_criterio= "AND ".$as_filtro."='".$as_valor."'";
		}
		$lb_valido=$this->io_function_db->uf_select_column($as_tabla,'codemp');	
		if($lb_valido===false)
		{
			$ls_sql="SELECT ".$as_campo."".
					"  FROM ".$as_tabla."".
					" WHERE ".$as_campo."='".$as_numero."'".
					" ".$ls_criterio." ";
		}
		else
		{
			$ls_sql="SELECT ".$as_campo."".
					"  FROM ".$as_tabla."".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND ".$as_campo."='".$as_numero."'".
					" ".$ls_criterio." ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_verificar_numero_generado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_numero=$this->uf_generar_numero_nuevo($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,
														  $as_filtro,$as_valor);
			}
			else
			{
				$lb_valido=true;
				if($ls_nroant!=$as_numero)
				{
					$this->io_mensajes->message("Se le Asignó un nuevo número de documento el cual es :".$as_numero);
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['as_numero']=$as_numero;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_verificar_numero_generado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_numero_nuevo($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,$as_valor)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_numero_nuevo
		//		   Access: private
		//		 Argument: $as_codsis  // Codigo de Sistema
		//				   $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $ai_loncam  // Longitud del Campo
		//				   $as_camini  // Nombre del campo que tiene el valor inicial del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $ai_estgen  // Indica si se esta Generando el Numero por que el Actual ya existe o no.
		//	  Description: Función que verifica si un numero generado esta disponible
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_nvonro="";
		if($ai_loncam>10)
		{
			$ls_prefijo=$this->uf_load_prefijo($as_codsis,$as_procede);
		}
		else
		{
			$ls_prefijo="";
		}
		if($ls_prefijo===false)
		{
			return false;
		}
		$ls_nroact=$this->uf_load_data_existente($as_tabla,$as_campo,$as_filtro,$as_valor,$ls_prefijo);
		$li_lonpre=strlen($ls_prefijo);
		if($ls_nroact!="")
		{
			if($ls_prefijo!="")
			{
				$li_nrolen=$ai_loncam-$li_lonpre;
				$ls_numpre=substr($ls_nroact,0,$li_lonpre);
				$ls_nro=substr($ls_nroact,$li_lonpre,$li_nrolen);
			}
			else
			{
				$ls_nro=$ls_nroact;
				$li_nrolen=$ai_loncam;
			}
		}
		else
		{
			$ls_nro=$this->uf_load_numero_inicial($as_camini);
			if(($ls_nro=="")||(($ls_nro==0)))
			{
				$ls_nro=0;
			}
			else
			{
				$ls_nro=$ls_nro-1;
			}
		}
		if(str_word_count($ls_nro))
		{
			return $ls_nvonro;
		}
		settype($ls_nro,'int');
		$li_nvonro=$ls_nro + 1;
		if($ls_prefijo!="")
		{
			$ls_nvonro= $this->io_funciones->uf_cerosizquierda($li_nvonro,$ai_loncam-$li_lonpre);
			$ls_nvonro= $ls_prefijo.$ls_nvonro;
		}
		else
		{
			$ls_nvonro= $this->io_funciones->uf_cerosizquierda($li_nvonro,$ai_loncam);
		}
		$arrResultado=$this->uf_verificar_numero_generado($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,
										 			   $as_valor,$ls_nvonro);
		$ls_nvonro = $arrResultado['as_numero'];
		$lb_valido = $arrResultado['lb_valido'];
		return $ls_nvonro;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prefijo($as_seleccionado,$as_codsis,$as_procede,$as_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_prefijo
		//		   Access: private
		//		 Argument: $as_seleccionado // Valor del campo que va a ser seleccionado
		//	  Description: Función que busca en la tabla los paises registrados
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_prefijo="";
		$ls_sql="SELECT sigesp_prefijos.codsis, sigesp_prefijos.procede, sigesp_prefijos.id, sigesp_prefijos.prefijo, sigesp_dt_prefijos.codusu".
				"  FROM sigesp_prefijos,sigesp_dt_prefijos ".
				" WHERE sigesp_prefijos.codemp='".$this->ls_codemp."'".
				"   AND sigesp_prefijos.codsis='".$as_codsis."'".
				"   AND sigesp_prefijos.procede='".$as_procede."'".
				"   AND sigesp_dt_prefijos.codusu='".$this->ls_logusr."'".
				"   AND sigesp_prefijos.estact=1".
				"   AND sigesp_prefijos.codemp=sigesp_dt_prefijos.codemp".
				"   AND sigesp_prefijos.id=sigesp_dt_prefijos.id".
				"   AND sigesp_prefijos.codsis=sigesp_dt_prefijos.codsis".
				"   AND sigesp_prefijos.procede=sigesp_dt_prefijos.procede".
				"   AND sigesp_prefijos.prefijo=sigesp_dt_prefijos.prefijo".
				" ORDER BY prefijo";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->sigesp_soc_c_registro_orden_compra.php;MÉTODO->uf_soc_combo_paises ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			print "<select class='form-select form-select-sm' name='cmbprefijo' id='cmbprefijo' onChange='javascript: ue_cambiar_prefijo();'>";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_seleccionado="";
				$ls_prefijo=trim($row["prefijo"]);
				if($as_seleccionado==$ls_prefijo)
				{
					$ls_seleccionado="selected";
				}
				print "<option value='".$ls_prefijo."' ".$ls_seleccionado.">".$ls_prefijo."</option>";
			}
			$this->io_sql->free_result($rs_data);	
			print "</select>";
		}
		return $ls_prefijo;
	}// end function uf_soc_combo_paises
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prefijogrid($as_seleccionado,$as_codsis,$as_procede,$as_codusu,$ls_fila)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_prefijogrid
		//		   Access: private
		//		 Argument: $as_seleccionado // Valor del campo que va a ser seleccionado
		//	  Description: Función que busca en la tabla los paises registrados
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 14/04/2007								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_cadena="";
		$ls_sql="SELECT sigesp_prefijos.codsis, sigesp_prefijos.procede, sigesp_prefijos.id, sigesp_prefijos.prefijo, sigesp_dt_prefijos.codusu".
				"  FROM sigesp_prefijos,sigesp_dt_prefijos ".
				" WHERE sigesp_prefijos.codemp='".$this->ls_codemp."'".
				"   AND sigesp_prefijos.codsis='".$as_codsis."'".
				"   AND sigesp_prefijos.procede='".$as_procede."'".
				"   AND sigesp_dt_prefijos.codusu='".$this->ls_logusr."'".
				"   AND sigesp_prefijos.estact=1".
				"   AND sigesp_prefijos.codemp=sigesp_dt_prefijos.codemp".
				"   AND sigesp_prefijos.id=sigesp_dt_prefijos.id".
				"   AND sigesp_prefijos.codsis=sigesp_dt_prefijos.codsis".
				"   AND sigesp_prefijos.procede=sigesp_dt_prefijos.procede".
				"   AND sigesp_prefijos.prefijo=sigesp_dt_prefijos.prefijo".
				" ORDER BY prefijo";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->generar consecutivo.php;MÉTODO->uf_prefijogrid ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ls_cadena=$ls_cadena. "<select name='cmbprefijo".$ls_fila."' id='cmbprefijo".$ls_fila."' style='width:120px'>";
			//print " <option value='---'>---seleccione---</option>";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_seleccionado="";
				$ls_prefijo=trim($row["prefijo"]);
				if($as_seleccionado==$ls_prefijo)
				{
					$ls_seleccionado="selected";
				}
				$ls_cadena=$ls_cadena. "<option value='".$ls_prefijo."' ".$ls_seleccionado.">".$ls_prefijo."</option>";
			}
			$this->io_sql->free_result($rs_data);	
			$ls_cadena=$ls_cadena. "</select>";
		}
		return $ls_cadena;
	}// end function uf_soc_combo_paises
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_numero_nuevo2($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,$as_valor,$ls_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_numero_nuevo2
		//		   Access: private
		//		 Argument: $as_codsis  // Codigo de Sistema
		//				   $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $ai_loncam  // Longitud del Campo
		//				   $as_camini  // Nombre del campo que tiene el valor inicial del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $ai_estgen  // Indica si se esta Generando el Numero por que el Actual ya existe o no.
		//	  Description: Función que verifica si un numero generado esta disponible
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_nvonro="";
		if($ai_loncam>10)
		{
			$ls_prefijo=$this->uf_load_prefijo2($as_codsis,$as_procede,$ls_codusu);
		}
		else
		{
			$ls_prefijo="";
		}
		if($ls_prefijo===false)
		{
			return false;
		}
		$ls_nroact=$this->uf_load_data_existente($as_tabla,$as_campo,$as_filtro,$as_valor,$ls_prefijo);
		$li_lonpre=strlen($ls_prefijo);
		if($ls_nroact!="")
		{
			if($ls_prefijo!="")
			{
				$li_nrolen=$ai_loncam-$li_lonpre;
				$ls_numpre=substr($ls_nroact,0,$li_lonpre);
				$ls_nro=substr($ls_nroact,$li_lonpre,$li_nrolen);
			}
			else
			{
				$ls_nro=$ls_nroact;
				$li_nrolen=$ai_loncam;
			}
		}
		else
		{
			$ls_nro=$this->uf_load_numero_inicial($as_camini);
			if(($ls_nro=="")||(($ls_nro==0)))
			{
				$ls_nro=0;
			}
			else
			{
				$ls_nro=$ls_nro-1;
			}
		}
		if(str_word_count($ls_nro))
		{
			return $ls_nvonro;
		}
		settype($ls_nro,'int');
		$li_nvonro=$ls_nro + 1;
		if($ls_prefijo!="")
		{
			$ls_nvonro= $this->io_funciones->uf_cerosizquierda($li_nvonro,$ai_loncam-$li_lonpre);
			$ls_nvonro= $ls_prefijo.$ls_nvonro;
		}
		else
		{
			$ls_nvonro= $this->io_funciones->uf_cerosizquierda($li_nvonro,$ai_loncam);
		}
		$arrResultado=$this->uf_verificar_numero_generado2($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,
										 			   $as_valor,$ls_nvonro,$ls_codusu);
		$ls_nvonro = $arrResultado['as_numero'];
		$lb_valido = $arrResultado['lb_valido'];
		return $ls_nvonro;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_numero_generado2($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,$as_valor,$as_numero,$ls_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_numero_generado
		//		   Access: private
		//		 Argument: $as_codsis  // Codigo de Sistema
		//				   $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $ai_loncam  // Longitud del Campo
		//				   $as_camini  // Nombre del campo que tiene el valor inicial del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $as_numero  // Valor a verificar
		//				   $ai_estgen  // Indica si se esta Generando el Numero por que el Actual ya existe o no.
		//	  Description: Función que verifica si un numero generado esta disponible
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_nroact="";
		$ls_nroant=$as_numero;
		$ls_criterio="";
		if(!empty($as_filtro))
		{
			$ls_criterio= "AND ".$as_filtro."='".$as_valor."'";
		}
		$lb_valido=$this->io_function_db->uf_select_column($as_tabla,'codemp');	
		if($lb_valido===false)
		{
			$ls_sql="SELECT ".$as_campo."".
					"  FROM ".$as_tabla."".
					" WHERE ".$as_campo."='".$as_numero."'".
					" ".$ls_criterio." ";
		}
		else
		{
			$ls_sql="SELECT ".$as_campo."".
					"  FROM ".$as_tabla."".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND ".$as_campo."='".$as_numero."'".
					" ".$ls_criterio." ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_verificar_numero_generado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_numero=$this->uf_generar_numero_nuevo($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,
														  $as_filtro,$as_valor);
			}
			else
			{
				$lb_valido=true;
				if($ls_nroant!=$as_numero)
				{
					$this->io_mensajes->message("Se le Asignó un nuevo número de documento el cual es :".$as_numero);
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['as_numero']=$as_numero;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_verificar_numero_generado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_prefijo2($as_codsis,$as_procede,$as_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_prefijo
		//		   Access: private
		//		 Argument: $as_codsis   // Codigo de Sistema
		//				   $as_procede  // Procedencia del Documento
		//	  Description: Función que Obtiene el prefijo del numero de documento (en caso de poseerlo)
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_prefijo="00";
		$ls_sql="SELECT sigesp_prefijos.codsis, sigesp_prefijos.procede, sigesp_prefijos.id, sigesp_prefijos.prefijo, sigesp_dt_prefijos.codusu".
				"  FROM sigesp_prefijos,sigesp_dt_prefijos ".
				" WHERE sigesp_prefijos.codemp='".$this->ls_codemp."'".
				"   AND sigesp_prefijos.codsis='".$as_codsis."'".
				"   AND sigesp_prefijos.procede='".$as_procede."'".
				"   AND sigesp_dt_prefijos.codusu='".$this->ls_logusr."'".
				"   AND sigesp_prefijos.estact=1".
				"   AND sigesp_prefijos.codemp=sigesp_dt_prefijos.codemp".
				"   AND sigesp_prefijos.id=sigesp_dt_prefijos.id".
				"   AND sigesp_prefijos.codsis=sigesp_dt_prefijos.codsis".
				"   AND sigesp_prefijos.procede=sigesp_dt_prefijos.procede".
				"   AND sigesp_prefijos.prefijo=sigesp_dt_prefijos.prefijo".
				" ORDER BY prefijo";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_load_prefijo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			$lb_ok=false;
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_i=$li_i+1;
				$ls_codusu=rtrim($row["codusu"]);
				if($ls_codusu!="--")
				{
					if($ls_codusu==$this->ls_logusr)
					{
						$ls_prefijo=$row["prefijo"];
						$lb_ok=true;
						break;
					}
				}
				else
				{
						$lb_ok=true;
						$ls_prefijo=$row["prefijo"];
				}
			}
			if($li_i==0)
			{
				$lb_ok=true;
			}
			if(!$lb_ok)
			{
				if(($as_procede!="SOCCOC")&&($as_procede!="SOCCOS"))
				{
					$this->io_mensajes->message("Este documento está configurado para el manejo de Prefijos, y en este momento Ud. No tiene acceso a ninguno. Por favor diríjase al Administrador del Sistema");
				}
				return false;
			}
			
			$this->io_sql->free_result($rs_data);	
		}
		return $ls_prefijo;
	}// end function uf_load_prefijo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_generar_numero_nuevo3($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,$as_valor,$ls_codusu,$ls_prefijo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_generar_numero_nuevo3
		//		   Access: private
		//		 Argument: $as_codsis  // Codigo de Sistema
		//				   $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $ai_loncam  // Longitud del Campo
		//				   $as_camini  // Nombre del campo que tiene el valor inicial del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $ai_estgen  // Indica si se esta Generando el Numero por que el Actual ya existe o no.
		//	  Description: Función que verifica si un numero generado esta disponible
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$ls_nvonro="";
		if($ls_prefijo===false)
		{
			return false;
		}
		$ls_nroact=$this->uf_load_data_existente($as_tabla,$as_campo,$as_filtro,$as_valor,$ls_prefijo);
		$li_lonpre=strlen($ls_prefijo);
		if($ls_nroact!="")
		{
			if($ls_prefijo!="")
			{
				$li_nrolen=$ai_loncam-$li_lonpre;
				$ls_numpre=substr($ls_nroact,0,$li_lonpre);
				$ls_nro=substr($ls_nroact,$li_lonpre,$li_nrolen);
			}
			else
			{
				$ls_nro=$ls_nroact;
				$li_nrolen=$ai_loncam;
			}
		}
		else
		{
			$ls_nro=$this->uf_load_numero_inicial($as_camini);
			if(($ls_nro=="")||(($ls_nro==0)))
			{
				$ls_nro=0;
			}
			else
			{
				$ls_nro=$ls_nro-1;
			}
		}
		if(str_word_count($ls_nro))
		{
			return $ls_nvonro;
		}
		settype($ls_nro,'int');
		$li_nvonro=$ls_nro + 1;
		if($ls_prefijo!="")
		{
			$ls_nvonro= $this->io_funciones->uf_cerosizquierda($li_nvonro,$ai_loncam-$li_lonpre);
			$ls_nvonro= $ls_prefijo.$ls_nvonro;
		}
		else
		{
			$ls_nvonro= $this->io_funciones->uf_cerosizquierda($li_nvonro,$ai_loncam);
		}
		$arrResultado=$this->uf_verificar_numero_generado2($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,
										 			   $as_valor,$ls_nvonro,$ls_codusu);
		$ls_nvonro = $arrResultado['as_numero'];
		$lb_valido = $arrResultado['lb_valido'];
		return $ls_nvonro;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_numero_generado3($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,$as_filtro,$as_valor,$as_numero,$ls_codusu,$ls_prefijo)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_numero_generado3
		//		   Access: private
		//		 Argument: $as_codsis  // Codigo de Sistema
		//				   $as_tabla   // Nombre de la Tabla de registro del documento
		//				   $as_campo   // Nombre del Campo que Contiene el id del documento
		//				   $ai_loncam  // Longitud del Campo
		//				   $as_camini  // Nombre del campo que tiene el valor inicial del documento
		//				   $as_filtro   // Nombre del Campo que Contiene el filtro
		//				   $as_valor   // Valor del Filtro
		//				   $as_numero  // Valor a verificar
		//				   $ai_estgen  // Indica si se esta Generando el Numero por que el Actual ya existe o no.
		//	  Description: Función que verifica si un numero generado esta disponible
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/07/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_nroact="";
		$ls_nroant=$as_numero;
		$ls_criterio="";
		if(!empty($as_filtro))
		{
			$ls_criterio= "AND ".$as_filtro."='".$as_valor."'";
		}
		$lb_valido=$this->io_function_db->uf_select_column($as_tabla,'codemp');	
		if($lb_valido===false)
		{
			$ls_sql="SELECT ".$as_campo."".
					"  FROM ".$as_tabla."".
					" WHERE ".$as_campo."='".$as_numero."'".
					" ".$ls_criterio." ";
		}
		else
		{
			$ls_sql="SELECT ".$as_campo."".
					"  FROM ".$as_tabla."".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND ".$as_campo."='".$as_numero."'".
					" ".$ls_criterio." ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Generar_Consecutivo MÉTODO->uf_verificar_numero_generado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_numero=$this->uf_generar_numero_nuevo3($as_codsis,$as_tabla,$as_campo,$as_procede,$ai_loncam,$as_camini,
														  $as_filtro,$as_valor,$ls_codusu,$ls_prefijo);
			}
			else
			{
				$lb_valido=true;
				if($ls_nroant!=$as_numero)
				{
					$this->io_mensajes->message("Se le Asignó un nuevo número de documento el cual es :".$as_numero);
				}
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['as_numero']=$as_numero;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_verificar_numero_generado
	//-----------------------------------------------------------------------------------------------------------------------------------


}
?>