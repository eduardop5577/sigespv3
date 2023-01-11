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

	class sigesp_c_seguridad
	{
		public function __construct()
		{
			ini_set('max_execution_time ','0');
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_sql.php");
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_include.php");
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_mensajes.php");
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones_db.php");
			require_once($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb']."/base/librerias/php/general/sigesp_lib_funciones2.php");
			$in=new sigesp_include();
			$this->con=$in->uf_conectar();
			$this->io_msg=     new class_mensajes();
			$this->io_funcion= new class_funciones();
			$this->io_sql=     new class_sql($this->con);
			$this->io_fun=     new class_funciones_db($this->con);
		}	
		
		function uf_sss_select_eventos($as_evento,$ls_descripcion)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_sss_select_eventos
			//         Access: public 
			//      Argumento: $as_evento        // codigo de evento
			//                 $ls_descripcion   // descripcion  de evento
			//	      Returns: Retorna un Booleano
			//    Description: Funcion que verifica la existencia de un evento en la tabla sss_eventos
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 01/11/2005 								Fecha Última Modificación : 
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$lb_valido=true;
			$ls_sql="";
			$ls_enabled= 1;
			$ls_sql="SELECT * FROM sss_eventos".
					" WHERE evento='".$as_evento."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_msg->message("CLASE->seguridad MÉTODO->uf_sss_select_eventos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ls_descripcion=$row["deseve"];
					$lb_valido=true;
				}
				else
				{
					$ls_descripcion="";
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
			return $lb_valido;
		} // end function uf_sss_select_eventos
		
		function uf_sss_insert_eventos_ventana($as_empresa,$as_sistema,$as_evento,$as_usuario,$as_ventana,$as_descripcion)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_sss_insert_eventos_ventana
			//         Access: public 
			//      Argumento: $as_empresa      // codigo de empresa
			//                 $as_sistema      // codigo de sistema
			//                 $as_evento       // codigo de evento
			//                 $as_usuario      // codigo de usuario
			//                 $as_ventana      // codigo de ventana
			//                 $as_descripcion  // descripcion  de evento
			//	      Returns: Retorna un Booleano
			//    Description: Funcion que inserta un evento que se origina en alguna operación  de INSERT, UPDATE ó DELETE 
			//				   dentro del Sistema y lo inserta en la tabla sss_registro_eventos
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 01/11/2005 								Fecha Última Modificación : 
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			if($as_usuario!="PSEGIS")
			{
				$lb_valido=true;
				$ls_sql="";
			
				if(strtoupper($_SESSION["ls_gestor"])=='OCI8PO')
				{
					$ld_fecha = $this->con->sysTimeStamp;
				}
				else
				{
					$ld_fecha = "'".date("Y-m-d H:i:s")."'";
				}
				$ls_ip=$this->getip();
				$ls_descripcion="";
				$ls_tabla="sss_registro_eventos";
				$ls_columna="numeve";
				$ls_codintper="---------------------------------";
				$ls_sisope="N/D";
				$campo = "";
				$arrResultado= $this->obtenerCodigoMenu($as_sistema,$as_ventana,$campo);
				$ls_codmenu=$arrResultado['codmenu'];
				$campo=$arrResultado['campo'];
				
				$ls_sql= "INSERT INTO sss_registro_eventos (codemp, codusu, codsis, evento, $campo, fecevetra, equevetra,".
						 " 									desevetra, ususisoper)". 
						 " VALUES ('".$as_empresa."','".$as_usuario."','".$as_sistema."','".$as_evento."',".
						 " 		   '".$ls_codmenu."',".$ld_fecha.",'".$ls_ip."','".$as_descripcion."','".$ls_sisope."')" ;
				$this->io_sql->begin_transaction();
				$li_row=$this->io_sql->execute($ls_sql);
				if ($li_row===false)
				{
					$this->io_sql->rollback();
				}	
			}
			else
			{
				$lb_valido=true;
			}
		  	return $lb_valido;
		} // end  function uf_sss_insert_eventos_ventana		
		
		function uf_sss_select_permisos($as_empresa,$as_usuario,$as_sistema,$as_ventana)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_sss_select_permisos
			//         Access: public 
			//      Argumento: $as_empresa      // codigo de empresa
			//                 $as_usuario      // codigo de usuario
			//                 $as_sistema      // codigo de sistema
			//                 $as_ventana      // codigo de ventana
			//	      Returns: Retorna un Booleano
			//    Description: Funcion que verifica si un usuario tiene permiso en determinada pantalla o no comparando que el campo
			//				   "enabled" sea igual a 1 para "permiso otorgado". En la tabla sss_derechos_usuarios
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 01/11/2005 								Fecha Última Modificación : 
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$lb_valido=true;
			$ls_sql="";
			$ls_enabled= 1;
			$arrResultado= $this->obtenerCodigoMenu($as_sistema,$as_ventana,$campo);
			$as_ventana=$arrResultado['codmenu'];
			$campo=$arrResultado['campo'];
			
			$ls_sql="SELECT * FROM sss_derechos_usuarios".
					" WHERE codemp='".$as_empresa."'".
					"   AND codusu='".$as_usuario."'".
					"   AND codsis='".$as_sistema."'".
					"   AND $campo='".$as_ventana."'".
					"   AND enabled=".$ls_enabled." ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_msg->message("CLASE->seguridad MÉTODO->uf_sss_select_permisos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$lb_valido=true;
				}
				else
				{
					$this->io_msg->message("NO TIENE PERMISO");
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
			return $lb_valido;
		} // end  function uf_sss_select_permisos


		function uf_sss_load_permisos($as_empresa,$as_usuario,$as_sistema,$as_ventana,$aa_permisos)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_sss_select_permisos
			//         Access: public 
			//      Argumento: $as_empresa       // codigo de empresa
			//                 $as_usuario       // codigo de usuario
			//                 $as_sistema       // codigo de sistema
			//                 $as_ventana       // codigo de ventana
			//                 $aa_permisos      // arreglo que contiene los permisos de la barra de herramienta
			//	      Returns: Retorna un Booleano
			//    Description: Funcion que verifica si un usuario tiene permiso en determinada pantalla o no comparando que el campo
			//				   "enabled" sea igual a 1 para "permiso otorgado" y carga en un arreglo todos los permisos de la barra
			//				   de herramientas En la tabla sss_derechos_usuarios
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 01/11/2005 								Fecha Última Modificación : 19/03/2007
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$lb_valido=true;
			$aa_permisos = array();
			$ls_sql="";
			$ls_enabled= 1;
			$campo = "";
			$arrResultado= $this->obtenerCodigoMenu($as_sistema,$as_ventana,$campo);
			$as_ventana=$arrResultado['codmenu'];
			$campo=$arrResultado['campo'];
			
			$ls_sql="SELECT * FROM sss_derechos_usuarios".
					" WHERE codemp='".$as_empresa."'".
					"   AND codusu='".$as_usuario."'".
					"   AND codsis='".$as_sistema."'".
					"   AND $campo='".$as_ventana."'".
					"   AND enabled=".$ls_enabled." ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_msg->message("CLASE->seguridad MÉTODO->uf_sss_select_permisos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$lb_valido=true;
					$aa_permisos["leer"]=     $row["leer"];
					$aa_permisos["incluir"]=  $row["incluir"];
					$aa_permisos["cambiar"]=  $row["cambiar"];
					$aa_permisos["eliminar"]= $row["eliminar"];
					$aa_permisos["imprimir"]= $row["imprimir"];
					$aa_permisos["anular"]=   $row["anular"];
					$aa_permisos["ejecutar"]= $row["ejecutar"];
					$aa_permisos["administrador"]= $row["administrativo"];
				}
				else
				{
					$this->io_msg->message("NO TIENE PERMISO");
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
			$arrResultado['aa_permisos']=$aa_permisos;
			$arrResultado['lb_valido']=$lb_valido;
			return $arrResultado;
		} // end  function uf_sss_select_permisos
		
		function uf_imprimir_permisos($ab_permisos,$aa_permisos,$as_logusr,$as_accion) {
			if (($ab_permisos)||($as_logusr=="PSEGIS"))
			{
				print("<input type=hidden name=permisos id=permisos value='$as_permisos'>");
				print("<input type=hidden name=leer id=leer value='$aa_permisos[leer]'>");
				print("<input type=hidden name=incluir id=incluir value='$aa_permisos[incluir]'>");
				print("<input type=hidden name=cambiar id=cambiar value='$aa_permisos[cambiar]'>");
				print("<input type=hidden name=eliminar id=eliminar value='$aa_permisos[eliminar]'>");
				print("<input type=hidden name=imprimir id=imprimir value='$aa_permisos[imprimir]'>");
				print("<input type=hidden name=anular id=anular value='$aa_permisos[anular]'>");
				print("<input type=hidden name=ejecutar id=ejecutar value='$aa_permisos[ejecutar]'>");
				print("<input type=hidden name=administrador id=administrador value='$aa_permisos[administrador]'>");
			}
			else
			{
				print("<script language=JavaScript>");
				print("".$as_accion."");
				print("</script>");
			}
		}

		function uf_sss_load_permisosinternos($as_empresa,$as_usuario,$as_sistema,$as_ventana,$as_codintper,$aa_permisos)
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_sss_load_permisosinternos
			//         Access: public 
			//      Argumento: $as_empresa       // codigo de empresa
			//                 $as_usuario       // codigo de usuario
			//                 $as_sistema       // codigo de sistema
			//                 $as_ventana       // codigo de ventana
			//                 $as_codintper     // codigo interno de permisologia
			//                 $aa_permisos      // arreglo que contiene los permisos de la barra de herramienta
			//	      Returns: Retorna un Booleano
			//    Description: Funcion que verifica si un usuario tiene permiso en determinada pantalla o no comparando que el campo
			//				   "enabled" sea igual a 1 para "permiso otorgado" y carga en un arreglo todos los permisos de la barra
			//				   de herramientas En la tabla sss_derechos_usuarios, en los casos de SNO y SPG verificando igualmente 
			//				   el codigo interno de permisologia
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 26/10/2006 								Fecha Última Modificación : 19/03/2007
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$lb_valido=true;
			$ls_sql="";
			$ls_enabled= 1;
			$campo="";
			$arrResultado= $this->obtenerCodigoMenu($as_sistema,$as_ventana,$campo);
			$as_ventana=$arrResultado['codmenu'];
			$campo=$arrResultado['campo'];
			
			$ls_sql="SELECT * FROM sss_derechos_usuarios".
					" WHERE codemp='".$as_empresa."'".
					"   AND codusu='".$as_usuario."'".
					"   AND codsis='".$as_sistema."'".
					"   AND $campo='".$as_ventana."'".
					"   AND enabled=".$ls_enabled." ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_msg->message("CLASE->seguridad MÉTODO->uf_sss_load_permisosinternos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$lb_valido=true;
					$aa_permisos["leer"]=     $row["leer"];
					$aa_permisos["incluir"]=  $row["incluir"];
					$aa_permisos["cambiar"]=  $row["cambiar"];
					$aa_permisos["eliminar"]= $row["eliminar"];
					$aa_permisos["imprimir"]= $row["imprimir"];
					$aa_permisos["anular"]=   $row["anular"];
					$aa_permisos["ejecutar"]= $row["ejecutar"];
					$aa_permisos["administrador"]= $row["administrativo"];
				}
				else
				{
					$this->io_msg->message("NO TIENE PERMISO");
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
				$arrResultado['aa_permisos']=$aa_permisos;
				$arrResultado['lb_valido']=$lb_valido;
				return $arrResultado;
		} // end  function uf_sss_load_permisosinternos


		function uf_sss_load_permisossigesp()
		{
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//	     Function: uf_sss_load_permisossigesp
			//         Access: public 
			//      Argumento: $aa_permisos      // arreglo que contiene los permisos de la barra de herramienta
			//	      Returns: Retorna un Booleano
			//    Description: Funcion que otorga todos los permisos al usuario SIGESP
			//	   Creado Por: Ing. Luis Anibal Lang
			// Fecha Creación: 01/11/2005 								Fecha Última Modificación : 19/03/2007
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$aa_permisos["leer"]=1;
			$aa_permisos["incluir"]=1;
			$aa_permisos["cambiar"]=1;
			$aa_permisos["eliminar"]=1;
			$aa_permisos["imprimir"]=1;
			$aa_permisos["anular"]=1;
			$aa_permisos["ejecutar"]=1;
			$aa_permisos["administrador"]= 1;
			return $aa_permisos;
		} // end  function uf_sss_select_permisossigesp

		function getip()
		{
		   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
				   $ip = getenv("HTTP_CLIENT_IP");
		   else if (getenv("HTTP_X_FORWARDED_FOR ") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR "), "unknown"))
				   $ip = getenv("HTTP_X_FORWARDED_FOR ");
		   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
				   $ip = getenv("REMOTE_ADDR");
		   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
				   $ip = $_SERVER['REMOTE_ADDR'];
		   else
				   $ip = "unknown";
		   
		   return($ip);
		}
		
		/***********************************************************************************
		* @Función que busca el código del sistema ventana
		* @parametros: 
		* @retorno: 
		* @fecha de creación: 09/10/2008
		* @autor: Ing. Yesenia Moreno de Lang
		************************************************************************************
		* @fecha modificación:
		* @descripción:
		* @autor:
		***********************************************************************************/		
			function obtenerCodigoMenu($codsis,$nomfisico,$campo)
			{
				global $conexionbd;
				if (array_key_exists('session_activa',$_SESSION))
				{	
					$codmenu=0;
					$_SESSION['session_activa'] = time();			
					$consulta = "SELECT codmenu ".
								"  FROM sss_sistemas_ventanas ".
								" WHERE codsis = '$codsis' ".
								"	AND nomfisico ='$nomfisico' ";
					
					$result = $this->io_sql->Execute($consulta); 
					if($result === false)
					{
						$this->valido  = false;
					}
					else
					{
						if(!$result->EOF)
						{   
							$codmenu=$result->fields["codmenu"];
						}
						$result->Close();
					}
					$campo= "codmenu";
				}
				else
				{
					$codmenu = $nomfisico;
					$campo= "nomven";
				}
				$arrResultado['campo']=$campo;
				$arrResultado['codmenu']=$codmenu;
				return $arrResultado;
			}		
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_buscar_correo_fichapersona($as_cedusr,$as_coereleper,$as_codnivorg)
{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_datos_correo
		//		   Access: public
		//	  Description: Función que busca la informacion para enviar los recibos por correo electronico
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 05/01/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		$ls_sql="SELECT coreleper, codorg ".				
				"  FROM sno_personal ".
				" WHERE sno_personal.codemp='".$ls_codemp."' ".
				" AND sno_personal.cedper='".$as_cedusr."' ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_msg->message("CLASE->seguridad MÉTODO->uf_buscar_correo_fichapersona ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			while(!$rs_data->EOF)
			{
				
				$as_coereleper=$rs_data->fields["coreleper"];
				$as_codnivorg=$rs_data->fields["codorg"];
				$rs_data->MoveNext();
			}
			
		}
		$arrResultado['as_coereleper']=$as_coereleper;
		$arrResultado['as_codnivorg']=$as_codnivorg;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
   	}// fin uf_buscar_datos_correo
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_buscar_correo_jefe($as_padord)
{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_datos_correo
		//		   Access: public
		//	  Description: Función que busca la informacion para enviar los recibos por correo electronico
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 05/01/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$as_corrjefe="";
		$ls_sql="SELECT coreleper ".
				"  FROM sno_personal ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codorg='".$as_padord."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->seguridad MÉTODO->uf_buscar_correo_jefe ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_corrjefe=$row["coreleper"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_corrjefe;   	
}// fin uf_buscar_datos_correo
//-----------------------------------------------------------------------------------------------------------------------------------

function uf_buscar_datos_correo($as_serv,$as_port,$as_remitente)
{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_datos_correo
		//		   Access: public
		//	  Description: Función que busca la informacion para enviar los recibos por correo electronico
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 05/01/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$as_serv="";
		$as_port="";
		$as_remitente="";	
		$lb_valido=true;
		$ls_sql="SELECT msjservidor,msjpuerto,msjremitente ".				
				"  FROM sigesp_correo ".
				" WHERE sigesp_correo.codemp='".$ls_codemp."' ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_msg->message("CLASE->seguridad MÉTODO->uf_buscar_datos_correo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			while(!$rs_data->EOF)
			{
				
				$as_serv=$rs_data->fields["msjservidor"];
				$as_port=$rs_data->fields["msjpuerto"];
				$as_remitente=$rs_data->fields["msjremitente"];					
				$rs_data->MoveNext();
			}
			
		}
		$arrResultado['as_serv']=$as_serv;
		$arrResultado['as_port']=$as_port;
		$arrResultado['as_remitente']=$as_remitente;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
   	}// fin uf_buscar_datos_correo
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_buscar_nivel_superior($ls_codnivorg)
{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_nivel_superior
		//		   Access: private
		//	    Arguments: as_numsol  //  Número de Solicitud
		//				   as_estsol  //  Estatus de la Solicitud
		// 	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que valida el estatus de aprobacion de la solicitud 
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 26/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$as_padorg="";
		$ls_sql="SELECT padorg ".
				"  FROM srh_organigrama ".
				" WHERE codemp='".$ls_codemp."' ".
				"   AND codorg='".$ls_codnivorg."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->seguridad MÉTODO->uf_buscar_nivel_superior ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_existe=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_padorg=$row["padorg"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_padorg;
	}// end function uf_validar_estatus_solicitud
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_envio_correo_activo($as_fromname,$as_numdoc,$as_bodyenv,$as_nomper)
{ 	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_datos_correo
		//		   Access: public
		//	  Description: Función que busca la informacion para enviar los recibos por correo electronico
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 05/01/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("phpMailer_v2.1/class.phpmailer.php");
		$io_mail=new PHPMailer();  
		$io_mail->IsSMTP(true); 
		$io_mail->IsHTML(true);			
		$lb_valido=false;
		$ls_logusr=$_SESSION["la_logusr"];
		$ls_cedusr=$_SESSION["la_cedusu"];
		$ls_nomper=$_SESSION["la_nomusu"];
		$lb_valido=$this->uf_buscar_correo_fichapersona($ls_cedusr,$ls_coereleper,$ls_codnivorg);		
		if ($ls_coereleper!="")
		{
			if ($ls_codnivorg!="")
			{
				$ls_padord=$this->uf_buscar_nivel_superior($ls_codnivorg);
				if ($ls_padord!="")
				{
					$ls_corrjefe=$this->uf_buscar_correo_jefe($ls_padord);
					if($ls_corrjefe!="")
					{
						$lb_valido=$this->uf_buscar_datos_correo($ls_servidor,$ls_puerto,$ls_remitente);
						if (($lb_valido)&&($ls_servidor!="")&&($ls_puerto!="")&&($ls_remitente!=""))
						{
							$io_mail->Host = $ls_servidor;
							$io_mail->Port = $ls_puerto;
							$io_mail->From = $ls_remitente;
							$io_mail->FromName = $as_fromname;
							$io_mail->Subject = $as_fromname;
							$io_mail->AddAddress($ls_corrjefe,'');
							$body  = $as_bodyenv."  ".$as_numdoc." realizada por el usuario ".$as_nomper;
							$io_mail->Body = $body;
							//$io_mail->AddAttachment($ls_ruta.'/Recibo_Pago_'.$ls_codper.'.pdf', 'Recibo_Pago_'.$ls_codper.'.pdf');
							if(!$io_mail->Send())
							{
								print("<script language=JavaScript>");
								print(" alert('Ocurrio un error al enviar el correo al supervisor');");
								print("</script>");
							}
							else
							{
								print("<script language=JavaScript>");
								print(" alert('Correo enviado al respectivo supervisor');");
								print("</script>");			
							}
							unset($io_mail);
						}
						else
						{
							$lb_valido=false;
							print("<script language=JavaScript>");
							print(" alert('Error en la Configuración de los Datos del Correo de la Empresa.');");
							print("</script>");
						}
					}
				}
			}
			else
			{
				print("<script language=JavaScript>");
				print(" alert('La persona ".$ls_cedusr." - ".$ls_nomper." no se encuentra en el organigrama.');");
				print("</script>");
			}
		}
		else
		{
				print("<script language=JavaScript>");
				print(" alert('La persona ".$ls_cedusr." - ".$ls_nomper." no tiene cuenta de correo asociada.');");
				print("</script>");
		}
}
//-----------------------------------------------------------------------------------------------------------------------------------

	}//  end class sigesp_c_seguridad
?>