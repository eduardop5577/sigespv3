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

class sigesp_ins_c_traspasohistoricosno
{
	
	public function __construct()
	{	
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_ins_c_traspasohistoricosno
		//		   Access: 
		//	  Description: Constructor de la Clase
		//	   Creado Por: 
		// Fecha Creación:  								
		// Modificado Por: 						Fecha Última Modificación : 29/05/2006
		//////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$this->io_include=new sigesp_include();
		$this->io_conexion=$this->io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
   		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();				
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->tablas = Array();
		$this->tablas[0] = "sno_hnomina";
		$this->tablas[1] = "sno_hhorario";
		$this->tablas[2] = "sno_hperiodo";
		$this->tablas[3] = "sno_hsubnomina";
		$this->tablas[4] = "sno_hcargo";
		$this->tablas[5] = "sno_hclasificacionobrero";
		$this->tablas[6] = "sno_htabulador";
		$this->tablas[7] = "sno_hgrado";
		$this->tablas[8] = "sno_hprimagrado";
		$this->tablas[9] = "sno_hasignacioncargo";
		$this->tablas[10] = "sno_hcodigounicorac";
		$this->tablas[11] = "sno_hunidadadmin";
		$this->tablas[12] = "sno_hproyecto";
		$this->tablas[13] = "sno_hpersonalnomina";
		$this->tablas[14] = "sno_hhojatiempo";
		$this->tablas[15] = "sno_hpersonalpension";
		$this->tablas[16] = "sno_hproyectopersonal";
		$this->tablas[17] = "sno_hvacacpersonal";
		$this->tablas[18] = "sno_hconstante";
		$this->tablas[19] = "sno_hconstantepersonal";
		$this->tablas[20] = "sno_hconcepto";
		$this->tablas[21] = "sno_hconceptopersonal";
		$this->tablas[22] = "sno_hconceptovacacion";
		$this->tablas[23] = "sno_hprimaconcepto";
		$this->tablas[24] = "sno_htipoprestamo";
		$this->tablas[25] = "sno_hprestamos";
		$this->tablas[26] = "sno_hprestamosperiodo";
		$this->tablas[27] = "sno_hprestamosamortizado";
		$this->tablas[28] = "sno_hencargaduria";
		$this->tablas[29] = "sno_hprimasdocentes";
		$this->tablas[30] = "sno_hprimadocentepersonal";
		$this->tablas[31] = "sno_hprenomina";
		$this->tablas[32] = "sno_hsalida";
		$this->tablas[33] = "sno_hresumen";
		$this->_table="";
		$this->criterio="";	
		$this->mensaje="";
		$this->campos = Array();
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
			$this->io_mensajes->message("CLASE->Integracion Historicos Nomina MÉTODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
			$this->io_mensajes->message("CLASE->Historicos Nomina MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
		$io_conexionhistorico=$this->io_include->uf_conectar_otra_bd($as_servidor_int,$as_login_int,$as_password_int,$as_basedatos_int,$as_gestor_int,$as_puerto_int);
		$io_conexionhistorico->io_sql=new class_sql($io_conexionhistorico);	
		$ls_sql="SELECT sno_hperiodo.codnom, MAX(sno_hnomina.desnom) AS desnom, sno_hperiodo.codperi, sno_hperiodo.fecdesper, sno_hperiodo.fechasper ".
				"  FROM sno_hnomina  ".
				" INNER JOIN sno_hperiodo ".
				"     ON sno_hnomina.codemp = sno_hperiodo.codemp ".
				"    AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				" WHERE sno_hperiodo.codemp='".$this->ls_codemp."'".
				" GROUP BY sno_hperiodo.codnom, sno_hperiodo.codperi, sno_hperiodo.fecdesper, sno_hperiodo.fechasper".
				" ORDER BY sno_hperiodo.codnom, sno_hperiodo.fecdesper, sno_hperiodo.codperi ";
		$rs_data=$io_conexionhistorico->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Integracion Historicos Nómina MÉTODO->uf_load_datos ERROR->".$this->io_funciones->uf_convertirmsg($io_conexionhistorico->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while(!$rs_data->EOF)
			{
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_codperi=$rs_data->fields["codperi"];
				$ls_desnom=$rs_data->fields["desnom"];
				$ls_fecdesper=$rs_data->fields["fecdesper"];
				$ls_fechasper=$rs_data->fields["fechasper"];
				$lb_existe=$this->uf_verificar_existencia($ls_codnom,$ls_codperi,$ls_fecdesper,$ls_fechasper);
				if(!$lb_existe)
				{
					$ai_totrows=$ai_totrows+1;
					$ls_fecdesper=$this->io_funciones->uf_convertirfecmostrar($ls_fecdesper);
					$ls_fechasper=$this->io_funciones->uf_convertirfecmostrar($ls_fechasper);
					$ao_object[$ai_totrows][1]="<input type=checkbox name=chksel".$ai_totrows." id=chksel".$ai_totrows." value=1 style=width:15px;height:15px>";		
					$ao_object[$ai_totrows][2]="<input name=txtcodnom".$ai_totrows." type=text id=txtcodnom".$ai_totrows." class=sin-borde size=6 value='".$ls_codnom."' readonly>";
					$ao_object[$ai_totrows][3]="<input name=txtdescripcion".$ai_totrows." type=text id=txtdescripcion".$ai_totrows." class=sin-borde size=50 value='".$ls_desnom."' readonly> ";
					$ao_object[$ai_totrows][4]="<input name=txtcodperi".$ai_totrows." type=text id=txtcodperi".$ai_totrows." class=sin-borde size=4 value='".$ls_codperi."' readonly>";
					$ao_object[$ai_totrows][5]="<input name=txtfecdes".$ai_totrows." type=text id=txtfecdes".$ai_totrows." class=sin-borde size=12 value='".$ls_fecdesper."' readonly>";
					$ao_object[$ai_totrows][6]="<input name=txtfechas".$ai_totrows." type=text id=txtfechas".$ai_totrows." class=sin-borde size=12 value='".$ls_fechasper."' readonly>";
				}
				$rs_data->MoveNext();
			}
			$io_conexionhistorico->io_sql->free_result($rs_data);
		}
		unset($io_conexionhistorico);
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_load_datos
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_existencia($as_codnom,$as_codperi,$as_fecdesper,$as_fechasper)
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
		$ls_sql="SELECT sno_hnomina.codnom  ".
				"  FROM sno_hnomina  ".
				" INNER JOIN sno_hperiodo ".
				"    ON sno_hperiodo.codemp='".$this->ls_codemp."' ".
				"   AND sno_hperiodo.codnom='".$as_codnom."' ".
				"   AND sno_hperiodo.codperi ='".$as_codperi."' ".
				"   AND sno_hperiodo.fecdesper ='".$as_fecdesper."' ".
				"   AND sno_hperiodo.fechasper ='".$as_fechasper."' ".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Integracion HHistoricos Nómina MÉTODO->uf_verificar_existencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=true;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_verificar_existencia
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_historicos_nomina($as_gestor_int,$as_puerto_int,$as_servidor_int,$as_basedatos_int,$as_login_int,$as_password_int,$as_codnom,$as_codperi,$as_fecdes,
									       $as_fechas,$aa_seguridad)
	{
		$lb_valido=true;
		$io_conexionhistorico=$this->io_include->uf_conectar_otra_bd($as_servidor_int,$as_login_int,$as_password_int,$as_basedatos_int,$as_gestor_int,$as_puerto_int);
		$io_conexionhistorico->io_sql=new class_sql($io_conexionhistorico);
		
		$as_fecdes=$this->io_funciones->uf_convertirdatetobd($as_fecdes);
		$as_fechas=$this->io_funciones->uf_convertirdatetobd($as_fechas);
			
		$ls_sql="SELECT sno_hperiodo.codnom, MAX(sno_hnomina.desnom) AS desnom, sno_hperiodo.codperi, sno_hperiodo.fecdesper, sno_hperiodo.fechasper ".
				"  FROM sno_hnomina  ".
				" INNER JOIN sno_hperiodo ".
				"     ON sno_hnomina.codemp = sno_hperiodo.codemp ".
				"    AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"    AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				" WHERE sno_hperiodo.codemp='".$this->ls_codemp."'".
				"   AND sno_hperiodo.codnom='".$as_codnom."'".
				"   AND sno_hperiodo.codperi='".$as_codperi."'".
				"   AND sno_hperiodo.fecdesper='".$as_fecdes."'".
				"   AND sno_hperiodo.fechasper='".$as_fechas."'".
				" GROUP BY sno_hperiodo.codnom, sno_hperiodo.codperi, sno_hperiodo.fecdesper, sno_hperiodo.fechasper".
				" ORDER BY sno_hperiodo.codnom, sno_hperiodo.fecdesper, sno_hperiodo.codperi ";
		$rs_data=$io_conexionhistorico->io_sql->select($ls_sql);
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
				$lb_existe=$this->uf_verificar_existencia($as_codnom,$as_codperi,$as_fecdes,$as_fechas);
				if(!$lb_existe)
				{
					// Se recorre el arreglo de tablas por sistema.
					$total=count((array)$this->tablas);
					for ( $contador = 0; (($contador < $total) && $lb_valido); $contador++ )
					{
						$this->_table=$this->tablas[$contador];
						if ($this->_table==='sno_hnomina')
						{
							$this->criterio=" WHERE codemp='".$this->ls_codemp."'".
											"   AND codnom='".$as_codnom."'".
											"   AND peractnom='".$as_codperi."'";
						}
						else
						{
							$this->criterio=" WHERE codemp='".$this->ls_codemp."'".
											"   AND codnom='".$as_codnom."'".
											"   AND codperi='".$as_codperi."'";
						}
						// Verifico que la tabla Exista en el origen.
						$existe = $this->verificarExistenciaTabla($io_conexionhistorico);
						if (($existe)&& $lb_valido)
						{
							// Obtengo los datos de la base de datos de origen según la configuració cargada
							$arrResultado = $this->obtenerDatosOrigen($io_conexionhistorico,$lb_valido);
							$lb_valido = $arrResultado['lb_valido'];
							$result = $arrResultado['result'];
							
							if ((!$result->EOF) && $lb_valido)
							{
								$this->cargarCampos($result,$this->io_conexion);
								$result->MoveFirst();
								$totcolumna=count((array)$result->FetchRow());
								$result->MoveFirst();
								while ((!$result->EOF) && $lb_valido)
								{
									$cadenacampos  = '';
									$cadenavalores = '';
									$consulta      = '';							
									for ($columna = 0; (($columna < $totcolumna)); $columna++)
									{
										$tipodato   = '';
										$valor      = '';
										$objeto     = $result->FetchField($columna);
										$campo      = $objeto->name;
										$tipodato   = $result->MetaType($objeto->type);
										$valor = $result->fields[$objeto->name];		
										$clave = array_search($campo, $this->campos);
										if (is_numeric($clave))
										{		
											// Actualizo el valor según el tipo de dato
											$valor = $this->actualizarValor($tipodato,$valor);
											$cadenacampos.=','.$this->campos[$columna];
											$cadenavalores.=','.$valor;
										}
									}
									$consulta='INSERT INTO '.$this->_table.' ('.substr($cadenacampos,1).')'.
												  ' VALUES ('.substr($cadenavalores,1).')';
									$resultado = $this->io_conexion->Execute($consulta);
									$result->MoveNext();								
								}
							}
							if(!$lb_valido)
							{
								$this->io_mensajes->message($this->mensaje);
							}
						}						
						if(!$lb_valido)
						{
							$this->io_mensajes->message($this->mensaje);
						}
					}
				}
				$rs_data->MoveNext();
			}
			$io_conexionhistorico->io_sql->free_result($rs_data);
		}
		unset($io_conexionhistorico);
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

/***********************************************************************************
* @Función que verifica si la tabla existe
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  verificarExistenciaTabla($conexionorigen)
	{
		$tablas =$conexionorigen->MetaTables('TABLES');
		$clave = array_search($this->_table, $tablas);
		if (is_numeric($clave))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

/***********************************************************************************
* @Función que Obtiene los registros de la Base de Datos Origen
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* 
* @descripción:
* @autor:
***********************************************************************************/
	function  obtenerDatosOrigen($conexionorigen,$lb_valido)
	{
		$consulta = 'SELECT * '.
					'  FROM '.$this->_table.' '.
					$this->criterio;
		$result = $conexionorigen->Execute($consulta);
		if($conexionorigen->HasFailedTrans())
		{
			$lb_valido=false;
			$this->mensaje .=' Ocurrio un error en la Transferencia.'.$conexionorigen->ErrorMsg();
		}
		$arrResultado['lb_valido']=$lb_valido;
		$arrResultado['result']=$result;
		return $arrResultado;		
	}	

/***********************************************************************************
* @Función que Obtiene y validad los campos de la base de datos origen
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  cargarCampos($result,$conexionbd)
	{
		$existe=true;
		$totcolumna=count((array)$result->FetchRow());
		$this->campos = Array();
		for ($columna = 0; (($columna < $totcolumna)&& $existe); $columna++)
		{
			$campo = '';
			$objeto = $result->FetchField($columna);
			$campo  = $objeto->name;		
			$existe = $this->verificarExistenciaCampo($campo,$conexionbd);
			if ($existe)
			{
				$this->campos[$columna] = $campo;
			}
			else
			{
				$this->mensaje .=' Ocurrio un error en la Transferencia. El campo '.$campo.' No existe en la tabla '.$this->_table;
			}
		}
	}	

/***********************************************************************************
* @Función que verifica si el campo a insertar existe en el modelo nuevo
* @parametros: 
* @retorno: 
* @fecha de creación: 22/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  verificarExistenciaCampo($campo,$conexionbd)
	{
		$campos =$conexionbd->MetaColumnNames($this->_table);
		if ($campos[strtoupper($campo)]===$campo)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

/***********************************************************************************
* @Función que actualiza el valor según su tipo de datos
* @parametros: 
* @retorno: 
* @fecha de creación: 21/10/2008
* @autor: Ing. Yesenia Moreno de Lang
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/
	function  actualizarValor($tipodato,$valor)
	{
		switch($tipodato)
		{
			case 'C':		
				$valor=rtrim($valor);		
				if($valor=='')
				{
					$valor="''";
				}
				elseif($valor=='(null)')
				{
					$valor="''";
				}
				elseif(is_string($valor)===false)
				{
					$valor="''";
				}
				else
				{
					$valor = str_replace("'","`",$valor);
					$valor = str_replace("\\","",$valor);
					$valor="'".$valor."'";
				}
			break;

			case 'D':
				$valor=str_replace('/','-',$valor);
				if($valor=='')
				{
					$valor="1900-01-01";
				}
				elseif($valor=='(null)')
				{
					$valor="1900-01-01";
				}
				$ls_dia=substr($valor,8,2);
				$ls_mes=substr($valor,5,2);
				$ls_ano=substr($valor,0,4);
				if(checkdate($ls_mes,$ls_dia,$ls_ano)===false)
				{
					 $valor="'1900-01-01'";
				}
				else
				{
					$valor="'".$valor."'";
				}
			break;
					
			case 'T':
				$valor=str_replace('/','-',$valor);
				if($valor=='')
				{
					$valor="1900-01-01";
				}
				elseif($valor=='(null)')
				{
					$valor="1900-01-01";
				}
				$dia=substr($valor,8,2);
				$mes=substr($valor,5,2);
				$anio=substr($valor,0,4);
				if(checkdate($mes,$dia,$anio)===false)
				{
					 $valor="'1900-01-01'";
				}
				else
				{
					$valor="'".substr($valor,0,10)."'";
				}
			break;
			
			case 'I':
				if($valor=='')
				{
					$valor='0';
				}
				elseif($valor=='(null)')
				{
					$valor='0';
				}
				elseif(is_numeric($valor)===false)
				{
					$valor='0';
				}
			break;
					
			case 'X':
				$valor=rtrim($valor);		
				if($valor=='')
				{
					$valor="''";
				}
				elseif($valor=='(null)')
				{
					$valor="''";
				}
				elseif(is_string($valor)===false)
				{
					$valor="''";
				}
				else
				{
					$valor = str_replace("'","`",$valor);
					$valor = str_replace("\\","",$valor);
					$valor="'".$valor."'";
				}
			break;
			
			case 'N':
				if($valor=='')
				{
					$valor='0';
				}
				elseif($valor=='(null)')
				{
					$valor='0';
				}
				elseif(is_numeric($valor)===false)
				{
					$valor='0';
				}
			break;
		}
		return $valor;
	}

}
?>
