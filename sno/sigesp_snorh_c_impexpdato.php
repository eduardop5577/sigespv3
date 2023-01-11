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

class sigesp_snorh_c_impexpdato
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_personal;
	var $io_personalnomina;
	var $io_sno;
	var $ls_codemp;
	var $ls_logusr;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_impexpdato
		//		   Access: public (sigesp_sno_p_impexpdato)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad= new sigesp_c_seguridad();
		require_once("sigesp_snorh_c_personal.php");
		$this->io_personal= new sigesp_snorh_c_personal();
		require_once("sigesp_sno_c_personalnomina.php");
		$this->io_personalnomina= new sigesp_sno_c_personalnomina();
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();		
		require_once("../shared/class_folder/evaluate_formula.php");
		$this->io_eval=new evaluate_formula();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_logusr=$_SESSION["la_logusr"];
		
	}// end function sigesp_snorh_c_impexpdato
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sno_p_impexpdato)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_personal);
		unset($this->io_personalnomina);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_abrir_archivo($as_nombrearchivo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_abrir_archivo
		//		   Access: private
		//	    Arguments: as_nombrearchivo // Ruta donde se debe abrir el archivo
		//	    		   ao_archivo // conexión del archivo que se desea abrir
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que abre un archivo de texto dada una ruta 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ao_archivo='';
		if (file_exists("$as_nombrearchivo"))
		{
			$ao_archivo=@file("$as_nombrearchivo");
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Importar/Exportar Datos MÉTODO->uf_abrir_archivo ERROR->el archivo no existe."); 
		}
		$arrResultado['ao_archivo']=$ao_archivo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_abrir_archivo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_crear_archivo($as_ruta,$as_nombre)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_crear_archivo
		//		   Access: private
		//	    Arguments: as_ruta // Ruta donde se debe crear el archivo
		//	    		   ao_archivo // conexión del archivo que se desea crear
		//	    		   as_tipo // tipo de archivo que se quiere crear
		// 	      Returns: lb_valido True si se creo el archivo ó False si no se creo
		//	  Description: Funcion que crea un archivo de texto dada una ruta 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta.'/'.$as_nombre.'.txt';
		$as_tipo="C";
		if (file_exists("$ls_nombrearchivo"))
		{
			unlink ("$ls_nombrearchivo");//Borrar el archivo de texto existente para crearlo nuevo.
			$ao_archivo=@fopen("$ls_nombrearchivo","a+");
		}
		else
		{
			$ao_archivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
		}
		if (file_exists("$ls_nombrearchivo")===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Importar Datos MÉTODO->uf_crear_archivo ERROR->No Se pudo crear el archivo."); 
		}
		$arrResultado['ao_archivo']=$ao_archivo;
		$arrResultado['as_tipo']=$as_tipo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_crear_archivo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_importardatos($as_arctxt,$as_codarch)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_importardatos
		//		   Access: public (sigesp_sno_p_impexpdato)
		//	    Arguments: as_arctxt  // Archivo txt que se desea importar
		//				   as_codarch // Código de Archivo
		//				   ao_title // Arreglo de Titulos
		//				   ao_campos // Arreglo de Campos
		//				   ai_nrofilas // Número de Filas
		//	    		   aa_seguridad  // arreglo de las variables de seguridad
		// 	      Returns: lb_valido True si se importó correctamente la información al sistema ó False si hubo algún error
		//	  Description: Funcion que importa la información de un txt al sistema
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_arctxt;
		$arrResultado=$this->uf_abrir_archivo($ls_nombrearchivo);
		$lo_archivo=$arrResultado['ao_archivo'];
		$lb_valido=$arrResultado['lb_valido'];
		if($lb_valido)
		{
			$arrResultado=$this->uf_load_configuracion_campos($as_codarch);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];			
			if($lb_valido)
			{
				$arrResultado=$this->uf_load_archivotxt_campos($lo_archivo,$lo_object,$li_totrows);
				$ai_nrofilas=$arrResultado['ai_nrofila'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			unset($lo_archivo);
		}
		if($lb_valido)
		{
			$this->io_mensajes->message("La información fue Importada.");
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un error al importar la información");
		}
		$arrResultado['ai_nrofilas']=$ai_nrofilas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_importardatos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_configuracion_campos($as_codarch)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_configuracion_campos
		//		   Access: privates
		//	    Arguments: as_codarch  // código del archivo txt
		//				   ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//	      Returns: lb_valido True si se ejecuto el buscar ó False si hubo error en el buscar
		//	  Description: Funcion que obtiene todos los campos de un archivo txt
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/11/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codemp, codarch, codcam, descam, inicam, loncam, edicam, clacam, actcam, tabrelcam, iterelcam, cricam, tipcam ".
				"  FROM sno_archivotxtcampo".
				" WHERE sno_archivotxtcampo.codemp='".$this->ls_codemp."'".	
				" AND codarch = '".$as_codarch."' ".	
				" ORDER BY sno_archivotxtcampo.codcam,inicam ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_load_configuracion_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while(!$rs_data->EOF)
			{
				$ai_totrows++;
				$li_codcam=$rs_data->fields["codcam"];
				$ls_descam=$rs_data->fields["descam"];
				$li_inicam=$rs_data->fields["inicam"];
				$li_loncam=$rs_data->fields["loncam"];
				$ls_cricam=$rs_data->fields["cricam"];
				$ls_edicam=$rs_data->fields["edicam"];
				$ls_clacam=$rs_data->fields["clacam"];
				$ls_actcam=$rs_data->fields["actcam"];
				$ls_tabrelcam=$rs_data->fields["tabrelcam"];
				$ls_iterelcam=$rs_data->fields["iterelcam"];
				$ls_tipcam=$rs_data->fields["tipcam"];
				$ao_object["codcam"][$ai_totrows]=$li_codcam;
				$ao_object["descam"][$ai_totrows]=$ls_descam;
				$ao_object["inicam"][$ai_totrows]=$li_inicam;
				$ao_object["loncam"][$ai_totrows]=$li_loncam;
				$ao_object["cricam"][$ai_totrows]=$ls_cricam;
				$ao_object["edicam"][$ai_totrows]=$ls_edicam;
				$ao_object["clacam"][$ai_totrows]=$ls_clacam;
				$ao_object["actcam"][$ai_totrows]=$ls_actcam;
				$ao_object["tabrelcam"][$ai_totrows]=$ls_tabrelcam;
				$ao_object["iterelcam"][$ai_totrows]=$ls_iterelcam;
				$ao_object["tipcam"][$ai_totrows]=$ls_tipcam;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_configuracion_campos
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_archivotxt_campos($ao_archivo,$ao_object,$ai_totrows)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_archivotxt_campos
		//		   Access: private
		//	    Arguments: ao_archivo // conexión del archivo que se desea leer
		//	    		   ai_totrows  // Total de filas del arreglo de campos
		//	    		   ao_object  // arreglo de campos
		//				   ao_title // Arreglo de Titulos
		//				   ao_campos // Arreglo de Campos
		//				   ai_nrofilas // Número de Filas
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que carga un archivo txt según la ruta y la configuración dada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_fila=0;
		$li_total=count((Array)$ao_archivo);
		$ls_ruta="txt/importar/resultado";
		$ls_nombrearchivo='Campos_por_actualizar_'.$this->ls_logusr;
		$arrResultado=$this->uf_crear_archivo($ls_ruta,$ls_nombrearchivo);
		$lo_archivoescribir=$arrResultado['ao_archivo'];
		$ls_tipo=$arrResultado['as_tipo'];
		$lb_valido=$arrResultado['lb_valido'];
		for($li_i=0;($li_i<$li_total);$li_i++)
		{
			$li_fila++;
			$ls_cadena="";		
			for($li_z=1;($li_z<=$ai_totrows);$li_z++)
			{
				$li_codcam=$ao_object["codcam"][$li_z];
				$ls_descam=$ao_object["descam"][$li_z];
				$li_inicam=$ao_object["inicam"][$li_z];
				$li_loncam=$ao_object["loncam"][$li_z];
				$ls_cricam=ltrim(rtrim($ao_object["cricam"][$li_z]));
				$ls_edicam=$ao_object["edicam"][$li_z];
				$ls_clacam=$ao_object["clacam"][$li_z];
				$ls_actcam=$ao_object["actcam"][$li_z];
				$ls_tabrelcam=$ao_object["tabrelcam"][$li_z];
				$ls_iterelcam=$ao_object["iterelcam"][$li_z];
				$ls_tipcam=$ao_object["tipcam"][$li_z];
				$ls_campo=substr($ao_archivo[$li_i],$li_inicam,$li_loncam);
				if($ls_tipcam=="N")
				{
					$ls_campo=doubleval($ls_campo);
					$ls_campo=number_format($ls_campo,2,".","");
				}
				if(($ls_cricam!="")&&($ls_tipcam!="C"))
				{
					if($ls_tipcam=="N")
					{
						$ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
						$ls_campo=$this->io_eval->uf_evaluar_formula($ls_cricam,$ls_campo);
					}
					else
					{
						$ls_campo="'".ltrim(rtrim($ls_campo))."'";
						$ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
						$ls_campo=@eval(" return $ls_cricam;");
					}
				}
				if($ls_tipcam=="N")
				{
					$ls_campo=number_format($ls_campo,2,",",".");
				}
				$ls_cadena=$ls_cadena.$ls_campo."|";
			}
			$ls_cadena=$ls_cadena." POR ACTUALIZAR\r\n";
			if ($lo_archivoescribir)
			{
				if (@fwrite($lo_archivoescribir,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
			}
			else
			{
				$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
				$lb_valido = false;
			}		
		}
		$ai_nrofilas=$li_i;
		$arrResultado['ai_nrofila']=$ai_nrofilas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_importar_data
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesarimportardatos($as_codarch,$as_acumon,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesarimportardatos
		//		   Access: public (sigesp_sno_p_impexpdato)
		//	    Arguments: as_codarch // Código de Archivo
		//				   as_codcons // Código de la constantes
		//				   ai_nrofilas // total de filas 
		//	    		   aa_seguridad  // arreglo de las variables de seguridad
		// 	      Returns: lb_valido True si se importó correctamente la información al sistema ó False si hubo algún error
		//	  Description: Funcion que importa la información de un txt al sistema
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/11/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		if($lb_valido)
		{
			$arrResultado=$this->uf_load_configuracion_campos($as_codarch);
			$li_totrows=$arrResultado['ai_totrows'];
			$lo_object=$arrResultado['ao_object'];
			$lb_valido=$arrResultado['lb_valido'];			
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_load_personalconstante($as_acumon,$lo_object,$aa_seguridad);
		}
		if($lb_valido)
		{
			$this->io_sql->commit();
			$this->io_mensajes->message("La información de las constantes fue Actualizada.");
		}
		else
		{
			$this->io_sql->rollback();
			$this->io_mensajes->message("Ocurrio un error al Actualizar la información de las constantes");
		}
		return $lb_valido;
	}// end function uf_procesarimportardatos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_personalconstante($as_acumon,$ao_object,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_personalconstante
		//		   Access: private
		//	    Arguments: as_codcons // Código de la constantes
		//				   ai_nrofilas // Nro de filas a actualizar
		//				   ai_totrow // total de filas 
		//	    		   aa_seguridad  // arreglo de las variables de seguridad
		// 	      Returns: lb_valido True si actualizó correctamente ó falso si ocurro algún error
		//	  Description: Funcion que actualiza el valor de una constante según lo cargado en los txt
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/11/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_ruta="txt/importar/resultado";
		$ls_nombrearchivo='Resultados_'.$this->ls_logusr;
		$arrResultado=$this->uf_crear_archivo($ls_ruta,$ls_nombrearchivo);
		$lo_archivoescribir=$arrResultado['ao_archivo'];
		$ls_tipo=$arrResultado['as_tipo'];
		$lb_valido=$arrResultado['lb_valido'];

		$nombrearchivo = 'txt/importar/procesar/PROCESAR_'.$this->ls_logusr.'.txt';
		if (file_exists("$nombrearchivo"))
		{
			$archivoprocesar=@file("$nombrearchivo");		
			$campoactualizable=false;
			$li_total=count((Array)$archivoprocesar);			
			for($li_i=0;($li_i<$li_total);$li_i++)
			{
				$la_updates="";
				$la_filtros="";
				$li_filtros=0;
				$li_updates=0;
				$lb_contante=false;
				$lb_nomina=false;
				$lb_personal=false;
				$li_filtros++;
				$la_filtros[$li_filtros]="codemp='".$this->ls_codemp."'";
				$ai_totrow=count((Array)$ao_object);
				$as_codnom="";
				$as_codcons="";				
				$as_codper="";			
				$ls_cadena="";					
				for($li_z=1;($li_z<=$ai_totrow);$li_z++)
				{
					$li_inicam=$ao_object["inicam"][$li_z];
					$li_loncam=$ao_object["loncam"][$li_z];
					$ls_cricam=ltrim(rtrim($ao_object["cricam"][$li_z]));
					$ls_edicam=$ao_object["edicam"][$li_z];
					$ls_clacam=$ao_object["clacam"][$li_z];
					$ls_actcam=$ao_object["actcam"][$li_z];
					$ls_tabrelcam=$ao_object["tabrelcam"][$li_z];
					$ls_iterelcam=$ao_object["iterelcam"][$li_z];
					$ls_tipcam=$ao_object["tipcam"][$li_z];
					$ls_campo=substr($archivoprocesar[$li_i],$li_inicam,$li_loncam);
					if($ls_tipcam=="N")
					{
						$ls_campo=doubleval($ls_campo);
						$ls_campo=number_format($ls_campo,2,".","");
					}
					else
					{
						$ls_campo="'".ltrim(rtrim($ls_campo))."'";					
					}
					if(($ls_cricam!="")&&($ls_tipcam!="C"))
					{
						if($ls_tipcam=="N")
						{
							$ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
							$ls_campo=$this->io_eval->uf_evaluar_formula($ls_cricam,$ls_campo);
						}
						else
						{
							$ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
							$ls_campo=@eval(" return $ls_cricam;");
						}
					}
					if($ls_clacam=="1")
					{
						$li_filtros++;
						$la_filtros[$li_filtros]=$ls_iterelcam."=".$ls_campo;
					}
					if($ls_actcam=="1")
					{
						$li_updates++;
						if (($ls_iterelcam=="moncon") && ($as_acumon=='1'))
						{
							$la_updates[$li_updates]=$ls_iterelcam."=(moncon+".$ls_campo.")";
						}
						else
						{
							$la_updates[$li_updates]=$ls_iterelcam."=".$ls_campo;
						}
						
					}
					if($ls_iterelcam=="codcons")
					{
						$lb_contante=true;
						$as_codcons=$ls_campo;
					}
					if($ls_iterelcam=="codnom")
					{
						$lb_nomina=true;
						$as_codnom=$ls_campo;
					}
					if($ls_iterelcam=="codper")
					{
						$lb_personal=true;
						$as_codper=$ls_campo;
					}
					if (($ls_campo<>"''") && ($ls_campo<>""))
					{
						$ls_cadena=$ls_cadena.$ls_campo."|";
					}
				}
				$ls_cadenaerror="";
				if($lb_nomina)
				{
					$ls_consulta= "SELECT codnom FROM sno_nomina WHERE codemp='".$this->ls_codemp."' AND codnom = ".$as_codnom."";
					$lb_existe=$this->uf_buscar_campo($ls_consulta);
					if(!$lb_existe)	
					{
						$ls_cadenaerror .="La nomina ".$as_codnom." no existe ";					
					}
					else
					{
						$ls_consulta= "SELECT codemp FROM sno_salida WHERE codemp='".$this->ls_codemp."' AND codnom = ".$as_codnom."";
						$lb_existe=$this->uf_buscar_campo($ls_consulta);
						if($lb_existe)	
						{
							$ls_cadenaerror .="La nomina ".$as_codnom." esta calculada ";					
						}
						else
						{							
							$ls_consulta= "SELECT codcons FROM sno_constante WHERE codemp='".$this->ls_codemp."' AND codnom = ".$as_codnom." AND codcons=".$as_codcons."";
							$lb_existe=$this->uf_buscar_campo($ls_consulta);
							if(!$lb_existe)	
							{
								$ls_cadenaerror .="La Constante ".$as_codcons." no existe en la nomina ".$as_codnom." ";					
							}
							else
							{
								$ls_consulta= "SELECT codper FROM sno_personalnomina WHERE codemp='".$this->ls_codemp."' AND codnom = ".$as_codnom." AND codper=".$as_codper."";
								$lb_existe=$this->uf_buscar_campo($ls_consulta);
								if(!$lb_existe)	
								{
									$ls_cadenaerror .="El personal ".$as_codper." no existe en la nomina ".$as_codnom." ";					
								}
								else
								{
									$ls_consulta= "SELECT codper FROM sno_constantepersonal WHERE codemp='".$this->ls_codemp."' AND codnom = ".$as_codnom." AND codcons=".$as_codcons." AND codper=".$as_codper."";
									$lb_existe=$this->uf_buscar_campo($ls_consulta);
									if(!$lb_existe)	
									{
										$ls_cadenaerror .="El personal ".$as_codper." no tiene asociada la constante ".$as_codcons." ";					
									}
								}
							}
						}
					}
				}
				if ($ls_cadenaerror=="")
				{
					$ls_sql="UPDATE sno_constantepersonal SET ";	
					// CARGAMOS LOS CAMPOS A ACTUALIZAR 	
					for($li_z=1;($li_z<=$li_updates);$li_z++)
					{
						$ls_update=$la_updates[$li_z];
						$ls_sql=$ls_sql." ".$ls_update." ";
						if($li_z<$li_updates)
						{
							$ls_sql=$ls_sql.", ";
						}
						$campoactualizable=true;
					}	
					$ls_sql=$ls_sql." WHERE ";
					// CARGAMOS LOS FILTROS DE LA SENTENCIA 
					for($li_z=1;($li_z<=$li_filtros);$li_z++)
					{
						$ls_filtro=$la_filtros[$li_z];
						if($li_z>1)
						{
							$ls_sql=$ls_sql." AND ";
						}
						$ls_sql=$ls_sql." ".$ls_filtro." ";
					}

					if(!$campoactualizable)
					{
						$ls_cadena=$ls_cadena."No Hay campos para actualizar. Error en definicion de TXT\r\n";					
						if ($lo_archivoescribir)  //Chequea que el archivo este abierto
						{
							if (@fwrite($lo_archivoescribir,$ls_cadena)===false)//Escritura
							{
							}							
						}
						$lb_valido=false;
						$this->io_mensajes->message("No Hay campos para actualizar. Error en definicion de TXT");
					}
					else
					{
						$lb_valido=$this->uf_update_constantepersonal($ls_sql,$aa_seguridad);
						if($lb_valido)
						{
							$ls_cadena=$ls_cadena." ACTUALIZADO \r\n";					
							if ($lo_archivoescribir)  //Chequea que el archivo este abierto
							{
								if (@fwrite($lo_archivoescribir,$ls_cadena)===false)//Escritura
								{
								}							
							}														
						}
					}
				}
				else
				{
					$ls_cadena=$ls_cadena." ".$ls_cadenaerror."  \r\n";					
					if ($lo_archivoescribir)  //Chequea que el archivo este abierto
					{
						if (@fwrite($lo_archivoescribir,$ls_cadena)===false)//Escritura
						{
						}							
					}								
				}
			}
		}
		else
		{
			$ls_cadena="El Archivo a procesar no existe o no tiene permiso ".$nombrearchivo."\r\n";					
			if ($lo_archivoescribir)  //Chequea que el archivo este abierto
			{
				if (@fwrite($lo_archivoescribir,$ls_cadena)===false)//Escritura
				{
					$this->io_mensajes->message("No se puede escribir el archivo ".$nombrearchivo);
				}							
			}
		}
		return $lb_valido;
	}// end function uf_load_personalconstante
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_constantepersonal($as_sql,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_constantepersonal
		//		   Access: private
		//	    Arguments: as_sql // sentencia sql que se va a ejecutar
		//	    		   aa_seguridad  // arreglo de las variables de seguridad
		// 	      Returns: lb_valido True si se abrio el archivo ó False si no se abrio
		//	  Description: Funcion que abre un archivo de texto dada una ruta 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_row=$this->io_sql->execute($as_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Importar Datos MÉTODO->uf_update_constantepersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_sql=str_replace("'","",$as_sql);
			$ls_descripcion =" Ejecuto la sentencia ".$ls_sql." en la nómina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////								
		}
		return $lb_valido;
	}// end function uf_update_constantepersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_campo($as_sql)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_personalnomina
		//		   Access: private
		//	    Arguments: as_codper // código del personal		
		//	      Returns: lb_valido 
		//	  Description: Funcion que verifica que un personal este en la tabla sno_personalnomina.
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 19/01/2009						Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=false;
		$rs_data=$this->io_sql->select($as_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Importar MÉTODO->uf_buscar_campo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=true;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$lb_existe=true;
				
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $lb_existe;
	}//end function uf_buscar_personalnomina
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>
