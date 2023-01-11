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

class sigesp_snorh_c_tabulador
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_fun_nomina;
	var $io_asignacioncargo;
	var $ls_codemp;
	var $ls_codnom;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_tabulador
		//		   Access: public (sigesp_sno_d_tabla)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
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
		require_once("class_folder/class_funciones_nomina.php");
		$this->io_fun_nomina=new class_funciones_nomina();
		require_once("sigesp_sno_c_asignacioncargo.php");
		$this->io_asignacioncargo= new sigesp_sno_c_asignacioncargo();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom="";
	}// end function sigesp_snorh_c_tabulador
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sno_d_tabla)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fun_nomina);
		unset($this->io_asignacioncargo);
        unset($this->ls_codemp);
        unset($this->ls_codnom);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_tabulador($as_codtab)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_tabulador
		//		   Access: private
		//	    Arguments: as_codtab  // C�digo de Tabla
		//	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que verifica si la tabla est� registrada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codtab ".
				"  FROM sno_tabulador ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_select_tabulador ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_tabulador
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_tabulador($as_codtab,$as_destab,$ai_maxpasgra,$ai_tabmed,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_tabulador
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_destab  // descripci�n
		//				   ai_maxpasgra // Maximo de Pasos por Grado
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert � False si hubo error en el insert
		//	  Description: Funcion que inserta el tabulador
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_tabulador(codemp,codnom,codtab,destab,maxpasgra,tabmed)VALUES".
				"('".$this->ls_codemp."','".$this->ls_codnom."','".$as_codtab."','".$as_destab."',".$ai_maxpasgra.",".$ai_tabmed.")";
				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_insert_tabulador ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert� la Tabla ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_insert_tabulador
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_tabulador($as_codtab,$as_destab,$ai_maxpasgra,$ai_tabmed,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_tabulador
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_destab  // descripci�n
		//				   ai_maxpasgra // Maximo de Pasos por Grado
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//     	  Returns: lb_valido True si se ejecuto el update � False si hubo error en el update
		//	  Description: Funcion que actualiza la tabla
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_tabulador ".
				"	SET destab='".$as_destab."', ".
				"		maxpasgra=".$ai_maxpasgra.", ".
				"		tabmed=".$ai_tabmed."".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'";

		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_update_tabulador ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz� la Tabla ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_update_tabulador
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codtab,$as_destab,$ai_maxpasgra,$as_codnom,$ai_tabmed,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_sno_d_tabla)
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_destab  // descripci�n
		//				   ai_maxpasgra // Maximo de Pasos por Grado
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar � False si hubo error en el guardar
		//	  Description: Funcion que guarda la tabla
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$this->ls_codnom=$as_codnom;
		switch ($as_existe)
		{
			case "FALSE":
				if(!($this->uf_select_tabulador($as_codtab)))
				{
					$lb_valido=$this->uf_insert_tabulador($as_codtab,$as_destab,$ai_maxpasgra,$ai_tabmed,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El tabulador ya existe, no lo puede incluir.");
				}
				break;

			case "TRUE":
				if(($this->uf_select_tabulador($as_codtab)))
				{
					$lb_valido=$this->uf_update_tabulador($as_codtab,$as_destab,$ai_maxpasgra,$ai_tabmed,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El tabulador no existe, no lo puede actualizar.");
				}
				break;
		}
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_tabulador($as_codtab,$as_codnom,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_tabulador
		//		   Access: public (sigesp_sno_d_tabla)
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete � False si hubo error en el delete
		//	  Description: Funcion que elimina la tabla
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ls_codnom=$as_codnom;
        if (!$this->io_asignacioncargo->uf_select_asignacioncargo("codtab",$as_codtab,"0"))
		{
			$this->io_sql->begin_transaction();
			$lb_valido=$this->uf_delete_primagrado_lote($as_codtab,"","",$aa_seguridad);
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_grado_lote($as_codtab,$aa_seguridad);
			}
			if($lb_valido)
			{
				$ls_sql="DELETE ".
						"  FROM sno_tabulador ".
						" WHERE codemp='".$this->ls_codemp."'".
						"   AND codnom='".$this->ls_codnom."'".
						"   AND codtab='".$as_codtab."'";
						
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_tabulador ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				}
			} 
			
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="DELETE";
				$ls_descripcion ="Elimin� la tabla ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				if($lb_valido)
				{	
					$this->io_mensajes->message("El Tabulador fue Eliminado.");
					$this->io_sql->commit();
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_tabulador ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
			}
			else
			{
				$this->io_sql->rollback();
			}
		} 
		else
		{
			$this->io_mensajes->message("No se puede eliminar el tabulador. Hay Asignaci�n de Cargo asociado a este Tabulador.");
		}       
		return $lb_valido;
    }// end function uf_delete_tabulador
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_grado_lote($as_codtab,$aa_seguridad)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_grado_lote
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete � False si hubo error en el delete
		//	  Description: Funcion que elimina los grados en lote
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_grado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'";
		
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabla M�TODO->uf_delete_grado_lote ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimin� todos los grados asociados al tabulador ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_delete_grado_lote
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_grado($as_codtab,$as_comauto,$ai_maxpasgra,$as_codnom,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_grado
		//		   Access: public (sigesp_sno_d_tabla)
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   ai_totrows  // total de fila
		//				   ao_object  // arreglo de objetos
		//	      Returns: lb_valido True si se encontro � False si no se encontr�
		//	  Description: Funcion que obtiene todos los grados de la tabla
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_totrows=0;
		$this->ls_codnom=$as_codnom;
		$ls_sql="SELECT codemp, codnom, codtab, codpas, codgra, monsalgra, moncomgra, aniodes, aniohas ".
				"  FROM sno_grado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				" ORDER BY codemp, codnom, codtab, codgra, codpas, moncomgra ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_load_grado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_codgraant="";
			$li_contador=0;
			while(!$rs_data->EOF)
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codgra=trim($rs_data->fields["codgra"]);
				$ls_codpas=trim($rs_data->fields["codpas"]);
				$li_monsalgra=$rs_data->fields["monsalgra"];
				$li_moncomgra=$rs_data->fields["moncomgra"];
				$li_aniodes=$rs_data->fields["aniodes"];
				$li_aniohas=$rs_data->fields["aniohas"];
				$li_sueldo=$li_monsalgra+$li_moncomgra;
				$li_monsalgra=$this->io_fun_nomina->uf_formatonumerico($li_monsalgra);
				$li_moncomgra=$this->io_fun_nomina->uf_formatonumerico($li_moncomgra);
				$li_sueldo=$this->io_fun_nomina->uf_formatonumerico($li_sueldo);
				$ls_readonly="";
				if($as_comauto=="1")
				{
					$ls_readonly="readonly";
				}
				$ls_estilo = "sin-borde";
				if($ai_maxpasgra>0)
				{
					if($ls_codgra!=$ls_codgraant)
					{
						$ls_codgraant=$ls_codgra;
						$li_contador=1;
					}
					else
					{
						$li_contador++;
						if($li_contador>$ai_maxpasgra)
						{
							$ls_readonly="";
							$ls_estilo = "sin-borderesaltado";
						}
					}
				}
				$ao_object[$ai_totrows][1]="<input class=".$ls_estilo." name=txtcodgra".$ai_totrows." type=text id=txtcodgra".$ai_totrows." size=18 maxlength=15 onKeyUp='javascript: ue_validarcomillas(this);' value='".$ls_codgra."' readOnly>";
				$ao_object[$ai_totrows][2]="<input class=".$ls_estilo." name=txtcodpas".$ai_totrows." type=text id=txtcodpas".$ai_totrows." size=18 maxlength=15 onKeyUp='javascript: ue_validarcomillas(this);' value='".$ls_codpas."' readOnly><input name='existe".$ai_totrows."' type='hidden' id='existe".$ai_totrows."' value='1'>";
				$ao_object[$ai_totrows][3]="<input class=".$ls_estilo." name=txtmonsalgra".$ai_totrows." type=text id=txtmonsalgra".$ai_totrows." size=16 maxlength=13 onKeyPress=return(ue_formatonumero(this,'.',',',event)) value='".$li_monsalgra."' onBlur='javascript: ue_sumarcompensacion(".$ai_totrows.");' style=text-align:right>";
				$ao_object[$ai_totrows][4]="<input class=".$ls_estilo." name=txtmoncomgra".$ai_totrows." type=text id=txtmoncomgra".$ai_totrows." size=16 maxlength=13 onKeyPress=return(ue_formatonumero(this,'.',',',event)) value='".$li_moncomgra."' style=text-align:right onBlur='javascript: uf_sumarsueldo(".$ai_totrows.");'  ".$ls_readonly.">";
				$ao_object[$ai_totrows][5]="<input class=".$ls_estilo." name=txtsueldo".$ai_totrows." type=text id=txtsueldo".$ai_totrows." class=sin-borde size=16 maxlength=13 onKeyPress=return(ue_formatonumero(this,'.',',',event)) value='".$li_sueldo."' style=text-align:right readonly>";
				$ao_object[$ai_totrows][6]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
				$ao_object[$ai_totrows][7]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Aceptar width=15 height=15 border=0></a>";			
				$ao_object[$ai_totrows][8]="<div align='center'><a href=javascript:uf_abrir_prima('".$ai_totrows."');><img src=../shared/imagebank/mas.gif alt=Definir primas border=0></a></div>";			
				$ao_object[$ai_totrows][9]="<div align='center'><a href=javascript:uf_abrir_rango('".$ai_totrows."');><img src=../shared/imagebank/mas.gif title=Abrir alt=Definir Rango A�os border=0></a></div>".			
										"<input name=txtaniodes".$ai_totrows." type=hidden id=txtaniodes".$ai_totrows."  value='".$li_aniodes."' >".			
										"<input name=txtaniohas".$ai_totrows." type=hidden id=txtaniohas".$ai_totrows."  value='".$li_aniohas."' >";			
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_grado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_grado($as_codtab,$as_codgra,$as_codpas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_grado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // c�digo de grado
		//				   as_codpas  // c�digo de paso
		//	      Returns: lb_existe True si se encontro � False si no se encontr�
		//	  Description: Funcion que verifica si el grado est� registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codgra ".
				"  FROM sno_grado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpas='".$as_codpas."'";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_select_grado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_grado
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_grado($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$ai_moncomgra,$ai_aniodes,$ai_aniohas,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_grado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   ai_monsalgra  // Monto Salario
		//				   ai_moncomgra  // Monto compensaci�n
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar � False si hubo error en el guardar
		//	  Description: Funcion que inserta en la tabla de grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if(empty($ai_aniodes))
		{
			$ai_aniodes=0;
		}
		if(empty($ai_aniohas))
		{
			$ai_aniohas=0;
		}
		$ls_sql="INSERT INTO sno_grado".
				"(codemp,codnom,codtab,codgra,codpas,monsalgra,moncomgra,aniodes,aniohas)VALUES".
				"('".$this->ls_codemp."','".$this->ls_codnom."','".$as_codtab."','".$as_codgra."',".
				"'".$as_codpas."',".$ai_monsalgra.",".$ai_moncomgra.",".$ai_aniodes.",".$ai_aniohas.")";
				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_insert_grado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert� el paso ".$as_codpas." grado ".$as_codgra." asociados al tabulador ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_insert_grado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_grado($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$ai_moncomgra,$ai_aniodes,$ai_aniohas,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_grado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   ai_monsalgra  // Monto Salario
		//				   ai_moncomgra  // Monto compensaci�n
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update � False si hubo error en el update
		//	  Description: Funcion que actualiza la tabla de grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_grado ".
				"	SET monsalgra = ".$ai_monsalgra.", ".
				"		moncomgra = ".$ai_moncomgra.", ".
				"		aniodes = ".$ai_aniodes.", ".
				"       aniohas = ".$ai_aniohas." ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpas='".$as_codpas."'";

		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_update_grado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		} 		
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz� el paso ".$as_codpas." grado ".$as_codgra." asociados al tabulador ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_update_grado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_personalnomina($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_personalnomina
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   ai_monsalgra  // Monto Salario
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update � False si hubo error en el update
		//	  Description: Funcion que actualiza el sueldo en personal n�mina a todo personal que tenga asociada esa tabla, paso y grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_personalnomina ".
				"	SET sueper = ".$ai_monsalgra." ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpas='".$as_codpas."'";

		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_update_personalnomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		} 		
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz� el sueldo del personal n�mina que est� asociado a el paso ".$as_codpas.",grado ".$as_codgra.", tabla ".$as_codtab.", n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_update_personalnomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar_grado($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$ai_moncomgra,$as_codnom,$ai_aniodes,$ai_aniohas,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_grado
		//		   Access: public (sigesp_sno_d_tabla)
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   ai_monsalgra  // Monto Salario
		//				   ai_moncomgra  // Monto compensaci�n
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar � False si hubo error en el guardar
		//	  Description: Funcion que almacena el grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_monsalgra=str_replace(".","",$ai_monsalgra);
		$ai_monsalgra=str_replace(",",".",$ai_monsalgra);
		$ai_moncomgra=str_replace(".","",$ai_moncomgra);
		$ai_moncomgra=str_replace(",",".",$ai_moncomgra);
		$this->ls_codnom=$as_codnom;
		if(!($this->uf_select_grado($as_codtab,$as_codgra,$as_codpas)))
		{
			$lb_valido=$this->uf_insert_grado($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$ai_moncomgra,$ai_aniodes,$ai_aniohas,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_update_grado($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$ai_moncomgra,$ai_aniodes,$ai_aniohas,$aa_seguridad);
			if($lb_valido)
			{
				$lb_valido=$this->uf_update_personalnomina($as_codtab,$as_codgra,$as_codpas,$ai_monsalgra,$aa_seguridad);
			}
		}
		return $lb_valido;
	}// end function uf_guardar_grado
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_integridad_asignacioncargo_grado($as_codtab,$as_codgra,$as_codpas)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_integridad_asignacioncargo_grado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // c�digo de grado
		//				   as_codpas  // c�digo de paso
		//	      Returns: lb_existe True si existe � False si no existe
		//	  Description: Funcion que valida que ning�n asignaci�n de cargo tenga asociada este paso y grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		$lb_existe=true;
       	$ls_sql="SELECT codtab ".
				"  FROM sno_asignacioncargo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpas='".$as_codpas."'";
				
       	$rs_data=$this->io_sql->select($ls_sql);
       	if ($rs_data===false)
       	{
			$this->msg->message("CLASE->Tabulador M�TODO->uf_integridad_asignacioncargo_grado ERROR->".$this->fun->uf_convertirmsg($this->SQL->message)); 
       	}
       	else
       	{
			if(!$row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);	
       	}
		return $lb_existe ;    
	}// end function uf_integridad_asignacioncargo_grado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_grado($as_codtab,$as_codgra,$as_codpas,$as_codnom,$aa_seguridad)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_grado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete � False si hubo error en el delete
		//	  Description: Funcion que elimina el grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$this->ls_codnom=$as_codnom;
        if ($this->uf_integridad_asignacioncargo_grado($as_codtab,$as_codgra,$as_codpas)===false)
		{
			$this->io_sql->begin_transaction();
			$lb_valido=$this->uf_delete_primagrado_lote($as_codtab,$as_codgra,$as_codpas,$aa_seguridad);
			if($lb_valido)
			{
				$ls_sql="DELETE ".
						"  FROM sno_grado ".
						" WHERE codemp='".$this->ls_codemp."'".
						"   AND codnom='".$this->ls_codnom."'".
						"   AND codtab='".$as_codtab."'".
						"   AND codgra='".$as_codgra."'".
						"   AND codpas='".$as_codpas."'";
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_grado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				
				}
				else
				{
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
					$ls_evento="DELETE";
					$ls_descripcion ="Elimin� el grado ".$as_codgra.", paso ".$as_codpas." asociado al tabulador ".$as_codtab." asociado a la n�mina n�mina ".$this->ls_codnom;
					$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
													$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
													$aa_seguridad["ventanas"],$ls_descripcion);
					/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				}
			}
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Grado fue Eliminado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_grado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}			
		}
		else
		{
			$this->io_mensajes->message("No se puede eliminar el grado. Hay Asignaci�n de Cargo asociado a este grado.");
		}
		return $lb_valido;
	}// end function uf_delete_grado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_primagrado_lote($as_codtab,$as_codgra,$as_codpas,$aa_seguridad)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_primagrado_lote
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete � False si hubo error en el delete
		//	  Description: Funcion que elimina las primas del grado, paso y tabla seleccionada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="DELETE ".
				"  FROM sno_primagrado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'";
		if(($as_codpas<>"")&&($as_codgra<>""))
		{
			$ls_sql=$ls_sql."   AND codpas='".$as_codpas."'".
							"   AND codgra='".$as_codgra."'";
		}
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_primagrado_lote ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimin� las primas del grado ".$as_codgra.", paso ".$as_codpas." asociado al tabulador ".$as_codtab." asociado a la n�mina n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_delete_primagrado_lote
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_primagrado($as_codtab,$as_codpas,$as_codgra,$as_codnom,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_primagrado
		//		   Access: public (sigesp_sno_pdt_primagrado)
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codpas  // C�digo de paso
		//				   as_codgra  // C�digo de grado
		//				   ai_totrows  // total de fila
		//				   ao_object  // arreglo de objetos
		//	      Returns: lb_valido True si se encontro � False si no se encontr�
		//	  Description: Funcion que obtiene todas las primasgrados de la tabla, paso y grado seleccionado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ls_codnom=$as_codnom;
		$ls_sql="SELECT codtab, codpas, codgra, codpri, despri, monpri ".
				"  FROM sno_primagrado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codpas='".$as_codpas."'".
				"   AND codgra='".$as_codgra."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_load_primagrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codpri=$row["codpri"];
				$ls_despri=$row["despri"];
				$li_monpri=$row["monpri"];
				$li_monpri=$this->io_fun_nomina->uf_formatonumerico($li_monpri);
				$ao_object[$ai_totrows][1]="<input name=txtcodpri".$ai_totrows." type=text id=txtcodpri".$ai_totrows." class=sin-borde size=15 maxlength=15 onKeyUp='javascript: ue_validarnumero(this);' value='".$ls_codpri."' readOnly>";
				$ao_object[$ai_totrows][2]="<input name=txtdespri".$ai_totrows." type=text id=txtdespri".$ai_totrows." class=sin-borde size=50 maxlength=100 value='".$ls_despri."'>";
				$ao_object[$ai_totrows][3]="<input name=txtmonpri".$ai_totrows." type=text id=txtmonpri".$ai_totrows." class=sin-borde size=15 maxlength=20 onKeyPress=return(ue_formatonumero(this,'.',',',event)) value='".$li_monpri."') style=text-align:right>";
				$ao_object[$ai_totrows][4]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
				$ao_object[$ai_totrows][5]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";			
			}
			$ai_totrows=$ai_totrows+1;
			$ao_object[$ai_totrows][1]="<input name=txtcodpri".$ai_totrows." type=text id=txtcodpri".$ai_totrows." class=sin-borde size=15 maxlength=15 onKeyUp='javascript: ue_validarnumero(this);'>";
			$ao_object[$ai_totrows][2]="<input name=txtdespri".$ai_totrows." type=text id=txtdespri".$ai_totrows." class=sin-borde size=50 maxlength=100 >";
			$ao_object[$ai_totrows][3]="<input name=txtmonpri".$ai_totrows." type=text id=txtmonpri".$ai_totrows." class=sin-borde size=15 maxlength=20 onKeyPress=return(ue_formatonumero(this,'.',',',event)) style=text-align:right>";
			$ao_object[$ai_totrows][4]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
			$ao_object[$ai_totrows][5]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";			
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_grado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_primagrado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // c�digo de grado
		//				   as_codpas  // c�digo de paso
		//				   as_codpri  // c�digo de prima
		//	      Returns: lb_existe True si se encontro � False si no se encontr�
		//	  Description: Funcion que verifica si la primagrado est� registrada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codpri ".
				"  FROM sno_primagrado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codpas='".$as_codpas."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpri='".$as_codpri."'";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_select_primagrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_primagrado
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri,$as_despri,$ai_monpri,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_primagrado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   as_codpri  // c�digo de prima
		//				   as_despri  // descripci�n de la prima
		//				   ai_monpri  // Monto de la prima
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert � False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla de primagrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_primagrado".
				"(codemp,codnom,codtab,codpas,codgra,codpri,despri,monpri)VALUES('".$this->ls_codemp."','".$this->ls_codnom."',".
				"'".$as_codtab."','".$as_codpas."','".$as_codgra."','".$as_codpri."','".$as_despri."',".$ai_monpri.")";
				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_insert_primagrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert� la prima grado ".$as_codpri." del paso ".$as_codpas." grado ".$as_codgra." asociados al tabulador ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_insert_primagrado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri,$as_despri,$ai_monpri,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_primagrado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   as_codpri  // c�digo de prima
		//				   as_despri  // descripci�n de la prima
		//				   ai_monpri  // Monto de la prima
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update � False si hubo error en el update
		//	  Description: Funcion que actualiza la tabla de primagrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_primagrado ".
				"	SET despri = '".$as_despri."', ".
				"		monpri = ".$ai_monpri." ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codpas='".$as_codpas."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpri='".$as_codpri."'";

		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_update_primagrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		} 		
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz� la prima grado ".$as_codpri." del paso ".$as_codpas." grado ".$as_codgra." asociados al tabulador ".$as_codtab." asociado a la n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_update_grado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri,$as_despri,$ai_monpri,$as_codnom,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_grado
		//		   Access: public (sigesp_sno_d_tabla)
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   as_codpri  // c�digo de prima
		//				   as_despri  // descripci�n de la prima
		//				   ai_monpri  // Monto de la prima
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar � False si hubo error en el guardar
		//	  Description: Funcion que almacena la primagrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_monpri=str_replace(".","",$ai_monpri);
		$ai_monpri=str_replace(",",".",$ai_monpri);
		$this->ls_codnom=$as_codnom;
		if($this->uf_select_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri)===false)
		{
			$lb_valido=$this->uf_insert_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri,$as_despri,$ai_monpri,$aa_seguridad);
		}
		else
		{
			$lb_valido=$this->uf_update_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri,$as_despri,$ai_monpri,$aa_seguridad);
		}
		return $lb_valido;
	}// end function uf_guardar_grado
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_primagrado($as_codtab,$as_codgra,$as_codpas,$as_codpri,$as_codnom,$aa_seguridad)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_primagrado
		//		   Access: private
		//	    Arguments: as_codtab  // c�digo de tabla
		//				   as_codgra  // C�digo de Grado
		//				   as_codpas  // C�digo de Paso
		//				   as_codpri  // C�digo de Prima
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete � False si hubo error en el delete
		//	  Description: Funcion que elimina las primas del grado, paso y tabla seleccionada
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 10/03/2006 								Fecha �ltima Modificaci�n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$this->ls_codnom=$as_codnom;
		$ls_sql="DELETE ".
				"  FROM sno_primagrado ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$this->ls_codnom."'".
				"   AND codtab='".$as_codtab."'".
				"   AND codpas='".$as_codpas."'".
				"   AND codgra='".$as_codgra."'".
				"   AND codpri='".$as_codpri."'";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_primagrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Elimin� la prima ".$as_codpri." del grado ".$as_codgra.", paso ".$as_codpas." asociado al tabulador ".$as_codtab." asociado a la n�mina n�mina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Prima fue Eliminada.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Tabulador M�TODO->uf_delete_primagrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_delete_primagrado
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------
	function uf_cargarnomina($as_codnom,$ai_calculada)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cargarnomina
		//		   Access: private
		//	  Description: Funci�n que obtiene todas las n�minas y las carga en un 
		//				   combo para seleccionarlas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 15/02/2008 								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		switch($as_codnom)
		{
			case "":
				$ls_selected="selected";
				$ls_disabled="";
				break;
			default:
				$ls_selected="";
				$ls_disabled="disabled";
				break;
		}
		$ls_sql="SELECT sno_nomina.codnom, sno_nomina.desnom ".
				"  FROM sno_nomina, sss_permisos_internos ".
				" WHERE sno_nomina.codemp='".$this->ls_codemp."'".
				"   AND sss_permisos_internos.codsis='SNO'".
				"   AND sss_permisos_internos.enabled=1".
				"   AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
				"   AND sno_nomina.codemp = sss_permisos_internos.codemp ".
				"   AND sno_nomina.codnom = sss_permisos_internos.codintper ".
				" GROUP BY sno_nomina.codnom, sno_nomina.desnom ".
				" ORDER BY sno_nomina.codnom, sno_nomina.desnom ";
		$rs_data=$this->io_sql->select($ls_sql);
		print "<select name='cmbnomina' id='cmbnomina' style='width:380px' onChange=uf_cambiarnomina(); ".$ls_disabled.">";
		print " <option value='' ".$ls_selected.">--Seleccione Una--</option>";
		if($rs_data===false)
		{
			$io_mensajes->message("Clase->Seleccionar N�mina M�todo->uf_cargarnomina Error->".$io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_calculada="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codnom=$row["codnom"];
				$ls_desnom=$row["desnom"];
				$ls_selected="";
				if($as_codnom==$ls_codnom)
				{
					$ls_selected="selected";
				}
				print "<option value='".$ls_codnom."' ".$ls_selected.">".$ls_codnom."-".$ls_desnom."</option>";				
				$li_calculada=str_pad($this->uf_existesalida($ls_codnom),1,"0");
				$ls_calculada=$ls_calculada."   <input name='calculada".$ls_codnom."' type='hidden' id='calculada".$ls_codnom."' value='".$li_calculada."'>";
			}
			$this->io_sql->free_result($rs_data);
		}
		print "</select>";
		print "<input name='txtcodnom' type='hidden' id='txtcodnom' value='".$as_codnom."'>";
		print $ls_calculada;
	}
	//--------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_existesalida($as_codnom)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_existesalida
		//		   Access: public 
		//	      Returns: lb_valido True si existe alguna salida y false si no existe Salida
		//	  Description: Funcion que verifica si hay registros en salida
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci�n: 16/02/2006 								Fecha �ltima Modificaci�n : 
		//////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT count(sno_resumen.codper) as total".
				"  FROM sno_resumen, sno_nomina ".
				" WHERE sno_resumen.codemp = '".$this->ls_codemp."' ".
				"   AND sno_resumen.codnom = '".$as_codnom."' ".
				"   AND sno_resumen.codemp = sno_nomina.codemp ".
				"   AND sno_resumen.codnom = sno_nomina.codnom ".
				"   AND sno_resumen.codperi = sno_nomina.peractnom ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=true;
			$this->io_mensajes->message("CLASE->Cargo M�TODO->uf_existesalida ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				if($row["total"]>0)
				{
					$lb_valido=true;
				}
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	
}
?>