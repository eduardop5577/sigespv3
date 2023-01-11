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

class sigesp_ins_c_integracionsigefirrhh
{	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_ins_c_integracionsigefirrhh
		//		   Access: 
		//	  Description: Constructor de la Clase
		//	   Creado Por: 
		// Fecha Creación:  								
		// Modificado Por: 						Fecha Última Modificación : 29/05/2006
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$this->io_include=new sigesp_include();
		$io_conexion=$this->io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();				
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function 

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRH MÉTODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["value"];
				$li_i=$li_i+1;
			}
			if($li_i==0)
			{
				$lb_valido=$this->uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				if ($lb_valido)
				{
					$ls_valor=$this->uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Función que inserta la variable de configuración
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->SNO MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			switch ($as_tipo)
			{
				case "C"://Caracter
					$valor = $as_valor;
					break;

				case "D"://Double
					$as_valor=str_replace(".","",$as_valor);
					$as_valor=str_replace(",",".",$as_valor);
					$valor = $as_valor;
					break;

				case "B"://Boolean
					$valor = $as_valor;
					break;

				case "I"://Integer
					$valor = intval($as_valor);
					break;
			}
			$ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type)VALUES ".
					"('".$this->ls_codemp."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$valor."','".$as_tipo."')";
					
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Integracion SIGEFIRRH MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_insert_config	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_datos($as_gestor_int,$as_puerto_int,$as_servidor_int,$as_basedatos_int,$as_login_int,$as_password_int,$ai_totrows,$ao_object)
	{
		$lb_valido=true;
		$io_sigefirrhh=$this->io_include->uf_conectar_otra_bd($as_servidor_int,$as_login_int,$as_password_int,$as_basedatos_int,$as_gestor_int,$as_puerto_int);
		$io_sigefirrhh->io_sql=new class_sql($io_sigefirrhh);	
		$ls_sql="SELECT codnom, codperi, codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'N' ".
				" GROUP BY codnom, codperi, codcom ".
				" UNION ".
				"SELECT codnom, codperi, codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg_ne  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'N' ".
				" GROUP BY codnom, codperi, codcom ".
				" UNION ".
				"SELECT codnom, codperi, codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg_obreros  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'N' ".
				" GROUP BY codnom, codperi, codcom ".
				" UNION ".
				"SELECT codnom, codperi, codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg_obreros_ne  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'N' ".
				" GROUP BY codnom, codperi, codcom ".
				" UNION ".
				"SELECT codnom, codperi, (codcomapo) AS codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'P' ".
				" GROUP BY codnom, codperi, codcomapo ".
				" UNION ".
				"SELECT codnom, codperi, (codcomapo) AS codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg_ne  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'P' ".
				" GROUP BY codnom, codperi, codcomapo ".
				" UNION ".
				"SELECT codnom, codperi, (codcomapo) AS codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg_obreros  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'P' ".
				" GROUP BY codnom, codperi, codcomapo ".
				" UNION ".
				"SELECT codnom, codperi, (codcomapo) AS codcom, Max(descripcion) as descripcion, max(tipo_concepto) as tipo_concepto ".
				"  FROM v_sno_dt_spg_obreros_ne  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND tipo_concepto = 'P' ".
				" GROUP BY codnom, codperi, codcomapo ".
				" ORDER BY codnom, codperi, codcom ";
		$rs_data=$io_sigefirrhh->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_load_datos ERROR->".$this->io_funciones->uf_convertirmsg($io_sigefirrhh->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while(!$rs_data->EOF)
			{
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_codperi=$rs_data->fields["codperi"];
				$ls_codcom=$rs_data->fields["codcom"];
				$ls_descripcion=$rs_data->fields["descripcion"];
				$ls_tipoconcepto=$rs_data->fields["tipo_concepto"];
				$lb_existe=$this->uf_verificar_existencia($ls_codcom);
				if(!$lb_existe)
				{
					$ai_totrows=$ai_totrows+1;
					$ao_object[$ai_totrows][1]="<input type=checkbox name=chksel".$ai_totrows." id=chksel".$ai_totrows." value=1 style=width:15px;height:15px>";		
					$ao_object[$ai_totrows][2]="<input name=txtcodnom".$ai_totrows." type=text id=txtcodnom".$ai_totrows." class=sin-borde size=6 value='".$ls_codnom."' readonly>";
					$ao_object[$ai_totrows][3]="<input name=txtcodperi".$ai_totrows." type=text id=txtcodperi".$ai_totrows." class=sin-borde size=4 value='".$ls_codperi."' readonly>";
					$ao_object[$ai_totrows][4]="<input name=txtcodcom".$ai_totrows." type=text id=txtcodcom".$ai_totrows." class=sin-borde size=17 value='".$ls_codcom."' readonly>";
					$ao_object[$ai_totrows][5]="<input name=txtdescripcion".$ai_totrows." type=text id=txtdescripcion".$ai_totrows." class=sin-borde size=50 value='".$ls_descripcion."' readonly> ";
					$ao_object[$ai_totrows][6]="<div align='center'><a href=javascript:uf_verdetalle('".$ls_codcom."','".$ls_tipoconcepto."','".$as_gestor_int."','".$as_puerto_int."','".$as_servidor_int."','".$as_basedatos_int."','".$as_login_int."','".$as_password_int."');><img src=../shared/imagebank/mas.gif alt=Detalle width=12 height=24 border=0></a></div>".
											   "<input type=hidden name=txttipoconcepto".$ai_totrows."  id=txttipoconcepto".$ai_totrows." value='".$ls_tipoconcepto."'>";
				}
				$rs_data->MoveNext();
			}
			$io_sigefirrhh->io_sql->free_result($rs_data);
		}
		unset($io_sigefirrhh);
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_load_datos
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_existencia($as_codcom,$ab_eliminar=false)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_existencia
		//		   Access: public
		//	    Arguments: as_codcom  // COMPROBANTE
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que verifica que se contabilizo un comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/112/2013 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codcom ".
				"  FROM sno_dt_spg ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codcom='".$as_codcom."' ".
				"   AND estatus = 1 ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_existencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=true;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_valido=false;
				if($ab_eliminar)
				{
					$ls_sql="DELETE ".
							"  FROM sno_dt_spg ".
							" WHERE codemp='".$this->ls_codemp."' ".
							"   AND codcom='".$as_codcom."' ".
							"   AND estatus = 0 ";
					$rs_row=$this->io_sql->select($ls_sql);
					if($rs_row===false)
					{
						$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_existencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						$lb_valido=true;
					}
					$ls_sql="DELETE ".
							"  FROM sno_dt_scg ".
							" WHERE codemp='".$this->ls_codemp."' ".
							"   AND codcom='".$as_codcom."' ".
							"   AND estatus = 0 ";
					$rs_row=$this->io_sql->select($ls_sql);
					if($rs_row===false)
					{
						$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_existencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						$lb_valido=true;
					}
				}
			}
		}
		return $lb_valido;
	}// end function uf_verificar_existencia
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_integracion($as_gestor_int,$as_puerto_int,$as_servidor_int,$as_basedatos_int,$as_login_int,$as_password_int,$as_codnom,$as_codperi,$as_codcom,
									 $as_tipoconcepto,$aa_seguridad)
	{
		$lb_valido=true;
		$io_sigefirrhh=$this->io_include->uf_conectar_otra_bd($as_servidor_int,$as_login_int,$as_password_int,$as_basedatos_int,$as_gestor_int,$as_puerto_int);
		$io_sigefirrhh->io_sql=new class_sql($io_sigefirrhh);	
		$campo="codcom";
		if($as_tipoconcepto=='P')
		{
			$campo="codcomapo";
		}
		$ls_sql="SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo." ".
				" UNION ".
				"SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg_ne  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo." ".
				" UNION ".
				"SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg_obreros  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo." ".
				" UNION ".
				"SELECT ".$campo.", MAX(cod_pro) AS cod_pro, MAX(ced_bene) AS ced_bene, MAX(tipo_destino) AS tipo_destino, Max(descripcion) as descripcion ".
				"  FROM v_sno_dt_spg_obreros_ne  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY ".$campo."".
				" ORDER BY ".$campo."";				
		$rs_data=$io_sigefirrhh->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_procesar_integracion ERROR->".$this->io_funciones->uf_convertirmsg($io_sigefirrhh->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while((!$rs_data->EOF)&($lb_valido))
			{
				$ls_descripcion=$rs_data->fields["descripcion"];
				$ls_cod_pro=$rs_data->fields["cod_pro"];
				$ls_ced_bene=$rs_data->fields["ced_bene"];
				$ls_tipo_destino=$rs_data->fields["tipo_destino"];
				$lb_existe=$this->uf_verificar_existencia($as_codcom,true);
				if(!$lb_existe)
				{
					$lb_valido=$this->uf_verificar_destino($ls_tipo_destino,$ls_cod_pro,$ls_ced_bene);
					if($lb_valido)
					{
						$lb_valido=$this->uf_procesar_spg($io_sigefirrhh->io_sql,$as_codnom,$as_tipoconcepto,$as_codperi,
														  $as_codcom,$ls_tipo_destino,$ls_cod_pro,$ls_ced_bene,
														  $ls_descripcion,$aa_seguridad);
					}
					if($lb_valido)
					{
						$lb_valido=$this->uf_procesar_scg($io_sigefirrhh->io_sql,$as_codnom,$as_tipoconcepto,$as_codperi,
														  $as_codcom,$ls_tipo_destino,$ls_cod_pro,$ls_ced_bene,
														  $ls_descripcion,$aa_seguridad);
					}
					
				}
				$rs_data->MoveNext();
			}
			$io_sigefirrhh->io_sql->free_result($rs_data);
		}
		unset($io_sigefirrhh);
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion="Integro de SIGEFIRRHH el comprobante ".$as_codcom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
		}
		
		return $lb_valido;
	}// end function uf_procesar_integracion
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_destino($as_tipo_destino,$as_cod_pro,$as_ced_bene)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_destino
		//		   Access: public
		//	    Arguments: as_codcom  // COMPROBANTE
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que verifica que se contabilizo un comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/112/2013 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		switch($as_tipo_destino)
		{
			case 'P':		
				$ls_campo='cod_pro';
				$ls_tabla='rpc_provedor';
				$ls_valor=$as_cod_pro;
			break;
			
			case 'B':		
				$ls_campo='ced_bene';
				$ls_tabla='rpc_beneficiario';
				$ls_valor=$as_ced_bene;
			break;
		}
		$ls_sql="SELECT codemp ".
				"  FROM ".$ls_tabla." ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND ".$ls_campo."='".$ls_valor."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_destino ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=true;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$lb_valido=true;
			}
			else
			{
				$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_destino ERROR-> NO EXISTE EL PROVEEDOR/BENEFICIARIO ".$ls_valor); 
			}
		}
		return $lb_valido;
	}// end function uf_verificar_destino
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_spg($io_sigefirrhh_io_sql,$as_codnom,$as_tipoconcepto,$as_codperi,$as_codcom,$as_tipo_destino,$as_cod_pro,$as_ced_bene,$as_descripcion,$aa_seguridad)
	{
		$lb_valido=true;
		$campo="codcom";
		$as_tipnom='N';
		if($as_tipoconcepto=='P')
		{
			$campo="codcomapo";
			$as_tipnom='A';
		}
		$ls_sql="SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
				"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_spg  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".
				" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
				" UNION ".
				"SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
				"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_spg_ne  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
				" UNION ".
				"SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
				"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_spg_obreros  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
				" UNION ".
				"SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal, ".
				"		SUM(monto_asigna) as monto_asigna, SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_spg_obreros_ne  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, spg_cuenta_patronal ";
		$rs_data=$io_sigefirrhh_io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_procesar_spg ERROR->".$this->io_funciones->uf_convertirmsg($io_sigefirrhh->io_sql->message));
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
				if((trim($rs_data->fields["spg_cuenta_patronal"])=='')||(empty($rs_data->fields["spg_cuenta_patronal"])))
				{
					$ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
					$li_monto=number_format($rs_data->fields["monto_asigna"] +$rs_data->fields["monto_deduce"],2,'.','');
				}
				else
				{
					$ls_spg_cuenta=$rs_data->fields["spg_cuenta"];
					$li_monto=number_format($rs_data->fields["monto_deduce"],2,'.','');
				}
				$ls_spg_cuenta=str_replace('.','',$ls_spg_cuenta);
				$lb_valido=$this->uf_verificar_cuentas_spg($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_estcla,$ls_spg_cuenta);
				if($lb_valido)
				{
					$ls_sql="INSERT INTO sno_dt_spg(codemp,codnom,codperi,codcom,tipnom,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,".
							"spg_cuenta,operacion,codconc,cod_pro,ced_bene,tipo_destino,descripcion,monto,estatus,estrd,codtipdoc,estnumvou,".
							"estnotdeb,codcomapo,estcla, codfuefin) VALUES ('".$this->ls_codemp."','".$as_codnom."','".$as_codperi."','".$as_codcom."',".
							"'".$as_tipnom."','".$ls_codestpro1."','".$ls_codestpro2."','".$ls_codestpro3."','".$ls_codestpro4."','".$ls_codestpro5."',".
							"'".$ls_spg_cuenta."','OCP','0000000001','".$as_cod_pro."','".$as_ced_bene."','".$as_tipo_destino."',".
							"'".$as_descripcion."',".$li_monto.",0,0,'".$ai_tipdoc."',0,0,'0000000001','".$ls_estcla."','--')";	
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_procesar_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					}
				}
				$rs_data->MoveNext();
			}
			$io_sigefirrhh_io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_procesar_spg
	//-----------------------------------------------------------------------------------------------------------------------------------	
		
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_cuentas_spg($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$as_spg_cuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_cuentas_spg
		//		   Access: public
		//	    Arguments: as_codcom  // COMPROBANTE
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que verifica que se contabilizo un comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/112/2013 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codemp ".
				"  FROM spg_cuentas ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codestpro1='".$as_codestpro1."' ".
				"   AND codestpro2='".$as_codestpro2."' ".
				"   AND codestpro3='".$as_codestpro3."' ".
				"   AND codestpro4='".$as_codestpro4."' ".
				"   AND codestpro5='".$as_codestpro5."' ".
				"   AND estcla='".$as_estcla."' ".
				"   AND spg_cuenta='".$as_spg_cuenta."' ".
				"   AND status='C' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_destino ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=true;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$lb_valido=true;
			}
			else
			{
				$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_cuentas_spg ERROR-> NO EXISTE LA CUENTA PRESUPUESTARIA ".$as_spg_cuenta." Ò NO ES DE MOVIMIENTO."); 
			}
		}
		return $lb_valido=true;
	}// end function uf_verificar_cuentas_spg
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_scg($io_sigefirrhh_io_sql,$as_codnom,$as_tipoconcepto,$as_codperi,$as_codcom,$as_tipo_destino,$as_cod_pro,$as_ced_bene,$as_descripcion,$aa_seguridad)
	{
		$lb_valido=true;
		$campo="codcom";
		$as_tipnom='N';
		if($as_tipoconcepto=='P')
		{
			$campo="codcomapo";
			$as_tipnom='A';
		}
		$ls_sql="SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
				"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_scg  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
				" UNION ".
				"SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
				"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_scg_ne  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
				" UNION ".
				"SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
				"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_scg_obreros  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
				" UNION ".
				"SELECT sc_cuenta, sc_cuenta_patronal, debhab, SUM(monto_asigna) as monto_asigna,  ".
				"		SUM(monto_deduce) as monto_deduce, SUM(monto_aporte) as monto_aporte  ".
				"  FROM v_sno_dt_scg_obreros_ne  ".
				" WHERE codemp='". $this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'".
				"   AND ".$campo."='".$as_codcom."'".
				"   AND tipo_concepto = '".$as_tipoconcepto."'".				
				" GROUP BY sc_cuenta, sc_cuenta_patronal, debhab ".
				" ORDER BY sc_cuenta, sc_cuenta_patronal, debhab ";
		$rs_data=$io_sigefirrhh_io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_procesar_scg ERROR->".$this->io_funciones->uf_convertirmsg($io_sigefirrhh->io_sql->message));
		}
		else
		{
			$li_monto_debe=0;
			$li_monto_haber=0;
			while((!$rs_data->EOF)&&($lb_valido))
			{
				if((trim($rs_data->fields["sc_cuenta_patronal"])=='')||(empty($rs_data->fields["sc_cuenta_patronal"])))
				{
					$ls_sc_cuenta=$rs_data->fields["sc_cuenta"];
					$li_monto=number_format($rs_data->fields["monto_asigna"] +$rs_data->fields["monto_deduce"],2,'.','');
				}
				else
				{
					$ls_sc_cuenta=$rs_data->fields["sc_cuenta"];
					$li_monto=number_format($rs_data->fields["monto_deduce"],2,'.','');
				}
				$ls_sc_cuenta=str_replace('.','',$ls_sc_cuenta);
				$ls_debhab=$rs_data->fields["debhab"];
				$lb_valido=$this->uf_verificar_cuentas_scg($ls_sc_cuenta);
				switch($ls_debhab)
				{
					case 'D':
						$li_monto_debe=$li_monto_debe+$li_monto;
					break;
					case 'H':
						$li_monto_haber=$li_monto_haber+$li_monto;
					break;
				}
				if($lb_valido)
				{
					$ls_sql="INSERT INTO sno_dt_scg(codemp,codnom,codperi,codcom,tipnom,sc_cuenta,debhab,codconc,cod_pro,ced_bene,tipo_destino,".
							"descripcion,monto,estatus,estrd,codtipdoc,estnumvou,estnotdeb,codcomapo) VALUES ('".$this->ls_codemp."','".$as_codnom."',".
							"'".$as_codperi."','".$as_codcom."','".$as_tipnom."','".$ls_sc_cuenta."','".$ls_debhab."','0000000001',".
							"'".$as_cod_pro."','".$as_ced_bene."','".$as_tipo_destino."','".$as_descripcion."',".$li_monto.",0,".
							"'0','','0','0','0000000001')";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_procesar_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					}
				}
				$rs_data->MoveNext();
			}
			if(number_format($li_monto_debe,2,'.','')<>number_format($li_monto_haber,2,'.',''))
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_procesar_scg ERROR->El Monto del Debe ".number_format($li_monto_debe,2,'.','')." No cuadra con el haber ".number_format($li_monto_haber,2,'.','')); 
			}
			$io_sigefirrhh_io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_procesar_scg
	//-----------------------------------------------------------------------------------------------------------------------------------	
		
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_cuentas_scg($as_sc_cuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_cuentas_scg
		//		   Access: public
		//	    Arguments: as_codcom  // COMPROBANTE
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Función que verifica que se contabilizo un comprobante
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/112/2013 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codemp ".
				"  FROM scg_cuentas ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND sc_cuenta='".$as_sc_cuenta."' ".
				"   AND status='C' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_cuentas_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=true;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$lb_valido=true;
			}
			else
			{
				$this->io_mensajes->message("CLASE->Integracion SIGEFIRRHH MÉTODO->uf_verificar_cuentas_scg ERROR-> NO EXISTE LA CUENTA CONTABLE ".$as_sc_cuenta." Ò NO ES DE MOVIMIENTO."); 
			}
		}
		return $lb_valido=true;
	}// end function uf_verificar_cuentas_scg
	//-----------------------------------------------------------------------------------------------------------------------------------	

}
?>
