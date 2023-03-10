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

class sigesp_snorh_c_archivotxt
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_personal;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_archivotxt
		//		   Access: public (sigesp_snorh_d_archivotxt)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
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
		$this->io_seguridad=new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function sigesp_snorh_c_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_archivostxt)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_archivotxt($as_codarch)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_archivotxt
		//		   Access: private
		//	    Arguments: as_codarch  // c?digo de archivo txt
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el archivo txt esta registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codarch ".
				"  FROM sno_archivotxt ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codarch='".$as_codarch."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_select_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_archivotxt($as_codarch,$as_denarch,$as_tiparch, $as_acumon,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_archivotxt
		//		   Access: private
		//	    Arguments: as_codarch  // c?digo del archivo txt
		//				   as_denarch  // Denominaci?n del archivo txt
		//                 as_tiparch //  Tipo del archivo (importar o exportar datos)
		//                 as_acumon  //  Valor que define si se va a acumular el valor de la constante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_archivotxt
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_archivotxt (codemp,codarch,denarch,tiparch,acumon)".
		        "VALUES('".$this->ls_codemp."','".$as_codarch."','".$as_denarch."','".$as_tiparch."','".$as_acumon."')";
				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_insert_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el archivo txt ".$as_codarch;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}		
		return $lb_valido;
	}// end function uf_insert_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_archivotxt($as_codarch,$as_denarch,$as_tiparch, $as_acumon,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_archivotxt
		//		   Access: private
		//	    Arguments: as_codarch  // c?digo del archivo txt
		//				   as_denarch  // Denominaci?n del archivo txt
		//                 as_tiparch //  Tipo del archivo (importar o exportar datos)
		//                 as_acumon  //  Valor que define si se va a acumular el valor de la constante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ? False si hubo error en el update
		//	  Description: Funcion que actualiza en la tabla sno_archivotxt
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_archivotxt ".
				"   SET denarch = '".$as_denarch."', ".
				"       acumon = '".$as_acumon."' ".  				
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codarch='".$as_codarch."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_update_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		} 		
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el archivo txt ".$as_codarch;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_update_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codarch,$as_denarch,$as_tiparch,$as_acumon,$aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_snorh_d_archivotxt)
		//	    Arguments: as_codarch  // c?digo del archivo txt
		//				   as_denarch  // Denominaci?n del archivo txt
		//                 as_tiparch //  Tipo del archivo (importar o exportar datos)
		//                 as_acumon  //  Valor que define si se va a acumular el valor de la constante
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ? False si hubo error en el guardar
		//	  Description: Funcion que guarda en la tabla sno_archivotxt
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_archivotxt($as_codarch)===false)
				{
					$lb_valido=$this->uf_insert_archivotxt($as_codarch,$as_denarch,$as_tiparch,$as_acumon,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("el archivo txt ya existe, no lo puede incluir.");
				}
				break;
				
			case "TRUE":
				if(($this->uf_select_archivotxt($as_codarch)))
				{
					$lb_valido=$this->uf_update_archivotxt($as_codarch,$as_denarch,$as_tiparch,$as_acumon,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La archivo txt no existe, no la puede actualizar.");
				}
				break;
		}
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_archivotxt($as_codarch, $aa_seguridad)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_archivotxt
		//		   Access: public (sigesp_snorh_d_archivotxt)
		//	    Arguments: as_codarch  // c?digo de la tabla de vacaci?n
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete ? False si hubo error en el delete
		//    Sescription: Funcion que elimina el archivo junto con sus campos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();
		$lb_valido=$this->uf_delete_campos($as_codarch, $aa_seguridad);
		if($lb_valido)
		{
			$ls_sql="DELETE ".
					"  FROM sno_archivotxt ".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND codarch='".$as_codarch."'";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_delete_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
		} 
		
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion ="Elimin? el archivo txt ".$as_codarch." y todos los campos asociados";
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_sql->commit();
				$this->io_mensajes->message("El archivo txt fue Eliminado.");
			}
			else
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_delete_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$this->io_sql->rollback();
			}
		}
		else
		{
			$this->io_sql->rollback();
		}
		return $lb_valido;
    }// end function uf_delete_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_archivotxt_campos($as_codarch,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_archivotxt_campos
		//		   Access: public (sigesp_snorh_d_archivotxt)
		//	    Arguments: as_codarch  // c?digo del archivo txt
		//				   ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//	      Returns: lb_valido True si se ejecuto el buscar ? False si hubo error en el buscar
		//	  Description: Funcion que obtiene todos los campos de un archivo txt
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codemp, codarch, codcam, descam, inicam, loncam, edicam, clacam, actcam, tabrelcam, iterelcam, cricam, tipcam, ".
				" (SELECT tiparch FROM sno_archivotxt ".
				"  WHERE sno_archivotxt.codemp=sno_archivotxtcampo.codemp ".
				"  AND sno_archivotxt.codarch=sno_archivotxtcampo.codarch) AS tiparch".
				"  FROM sno_archivotxtcampo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codarch='".$as_codarch."'".		
				" ORDER BY codarch,codcam ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_load_archivotxt_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows++;
				$li_codcam=$row["codcam"];
				$ls_descam=$row["descam"];
				$li_inicam=$row["inicam"];
				$li_loncam=$row["loncam"];
				$ls_cricam=$row["cricam"];
				$ls_edicam=$row["edicam"];
				$la_edicam[0]="";
				$la_edicam[1]="";
				$ls_clacam=$row["clacam"];
				$la_clacam[0]="";
				$la_clacam[1]="";
				$ls_actcam=$row["actcam"];
				$la_actcam[0]="";
				$la_actcam[1]="";
				$ls_tabrelcam=$row["tabrelcam"];
				$ls_iterelcam=$row["iterelcam"];
				$ls_tabla=$row["tabrelcam"];
				$ls_item=$row["iterelcam"];
				$la_iterelcam[0]="";
				$la_iterelcam[1]="";
				$la_iterelcam[2]="";
				$la_iterelcam[3]="";
				$ls_tipcam=$row["tipcam"];
				$la_tipcam[0]="";
				$la_tipcam[1]="";
				$ls_tiparch=$row["tiparch"];
				
				switch($ls_tipcam)
				{
					case "C":
						$la_tipcam[0]="selected";
						break;
					case "N":
						$la_tipcam[1]="selected";
						break;
				}
				switch($ls_edicam)
				{
					case "0":
						$la_edicam[0]="selected";
						break;
					case "1":
						$la_edicam[1]="selected";
						break;
				}
				switch($ls_clacam)
				{
					case "0":
						$la_clacam[0]="selected";
						break;
					case "1":
						$la_clacam[1]="selected";
						break;
				}
				switch($ls_actcam)
				{
					case "0":
						$la_actcam[0]="selected";
						break;
					case "1":
						$la_actcam[1]="selected";
						break;
				}
				switch($ls_iterelcam)
				{
					case "codcons":
						$la_iterelcam[0]="selected";
						break;
					case "codper":
						$la_iterelcam[1]="selected";
						break;
					case "moncon":
						$la_iterelcam[2]="selected";
						break;
					case "codnom":
						$la_iterelcam[3]="selected";
						break;
				}
				switch($ls_tabla)
				{
					case "sno_personal":
						$ls_tabla="Personal";
					break;
					
					case "sno_constantepersonal":
						$ls_tabla="Concepto Persona";
					break;
					
					case "sno_periodo":
						$ls_tabla="Nomina";
					break;					
				}
				if ($ls_tiparch!='E')
				{
					switch($ls_tabla)
					{
						case "sno_constantepersonal":
							$ls_tabla="Constante Persona";
						break;
					}
				}
				switch($ls_item)
				{
					case "codnom":
						$ls_item="Codigo Nomina";
					break;

					case "codcons":
						$ls_item="Codigo Concepto";
					break;
					
					case "codper":
						$ls_item="Codigo Personal";
					break;
					
					case "moncon":
						$ls_item="Monto Concepto";
					break;
					
					case "monpat":
						$ls_item="Monto Patrono";
					break;
					
					case "nacper":
						$ls_item="Nacionalidad";
					break;
					
					case "cedper":
						$ls_item="C?dula";
					break;
					
					case "fecdesper":
						$ls_item="Fecha Inicio";
					break;
					
					case "fechasper":
						$ls_item="Fecha Fin";
					break;					
				}
				if ($ls_tiparch!='E')
				{
					switch($ls_item)
					{
						case "codcons":
							$ls_item="Codigo Constante";
						break;
					}
				}
				
				
				$ao_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value='".$li_codcam."'>";
				$ao_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value='".$ls_descam."'>";
				$ao_object[$ai_totrows][3]="<input name=txtinicam".$ai_totrows." type=text id=txtinicam".$ai_totrows." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);' value='".$li_inicam."'>";
				$ao_object[$ai_totrows][4]="<input name=txtloncam".$ai_totrows." type=text id=txtloncam".$ai_totrows." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);' value='".$li_loncam."'>";
				$ao_object[$ai_totrows][5]="<select name=cmbedicam".$ai_totrows." id=cmbedicam".$ai_totrows."><option value='0' ".$la_edicam[0]." >No</option><option value='1' ".$la_edicam[1].">Si</option></select>";
				$ao_object[$ai_totrows][6]="<select name=cmbclacam".$ai_totrows." id=cmbclacam".$ai_totrows."><option value='0' ".$la_clacam[0]." >No</option><option value='1' ".$la_clacam[1].">Si</option></select>";
				$ao_object[$ai_totrows][7]="<select name=cmbactcam".$ai_totrows." id=cmbactcam".$ai_totrows."><option value='0' ".$la_actcam[0]." >No</option><option value='1' ".$la_actcam[1].">Si</option></select>";
				$ao_object[$ai_totrows][8]="<textarea name=txtcricam".$ai_totrows." id=txtcricam".$ai_totrows." class=sin-borde cols='30' rows='2' onKeyUp='javascript: ue_validarcomillas(this);'>".$ls_cricam."</textarea>";
				$ao_object[$ai_totrows][9]="<select name=cmbtipcam".$ai_totrows." id=cmbtipcam".$ai_totrows."><option value='C' ".$la_tipcam[0].">Caracter</option><option value='N' ".$la_tipcam[1].">Numerico</option></select>";
				
				if ($ls_tiparch=='E')
				{
					$ao_object[$ai_totrows][10]="<input name=txttabrelcam".$ai_totrows." type=text id=txttabrelcam".$ai_totrows." class=sin-borde size=15 value='".$ls_tabla."' readonly>".
										  "<input name=valortabrelcam".$ai_totrows." type=hidden id=valortabrelcam".$ai_totrows." value='".$ls_tabrelcam."'>";
					$ao_object[$ai_totrows][11]="<input name=txtiterelcam".$ai_totrows." type=text id=txtiterelcam".$ai_totrows." class=sin-borde size=15 value='".$ls_item."' readonly>".
										  "<input name=valoriterelcam".$ai_totrows." type=hidden id=valoriterelcam".$ai_totrows." value='".$ls_iterelcam."'>";
				}
				else
				{				
					$ao_object[$ai_totrows][10]="<input name=txttabrelcam".$ai_totrows." type=text id=txttabrelcam".$ai_totrows." class=sin-borde size=15 value='".$ls_tabla."' readonly>".
										  "<input name=valortabrelcam".$ai_totrows." type=hidden id=valortabrelcam".$ai_totrows." value='".$ls_tabrelcam."'>";
					$ao_object[$ai_totrows][11]="<input name=txtiterelcam".$ai_totrows." type=text id=txtiterelcam".$ai_totrows." class=sin-borde size=15 value='".$ls_item."' readonly>".
										  "<input name=valoriterelcam".$ai_totrows." type=hidden id=valoriterelcam".$ai_totrows." value='".$ls_iterelcam."'>";
				}						
											
				$ao_object[$ai_totrows][12]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
				$ao_object[$ai_totrows][13]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Eliminar width=15 height=15 border=0></a>";			
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_archivotxt_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_delete_campos($as_codarch,$aa_seguridad)
    {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_campos
		//		   Access: public (sigesp_snorh_d_archivotxt)
		//	    Arguments: as_codarch  // c?digo de la tabla de vacacion
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete ? False si hubo error en el delete
		//	  Description: Funcion que elimina los campos de un archivo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="DELETE FROM sno_archivotxtcampo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codarch='".$as_codarch."'";		
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_delete_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion ="Elimin? los campos del archivo ".$as_codarch;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_campos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_archivotxt_campos($as_codarch,$ai_codcam,$as_descam,$ai_inicam,$ai_loncam,$as_edicam,$as_clacam,$as_actcam,
										 $as_tabrelcam,$as_iterelcam,$as_cricam,$as_tipcam,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_archivotxt_campos
		//		   Access: private
		//	    Arguments: as_codarch  // c?digo archivo
		//				   ai_codcam  //  c?digo de campo
		//				   as_descam  // descripci?n del campo
		//				   ai_inicam  // Inicio del campo
		//				   ai_loncam  // longitud del campo
		//				   as_edicam  // campo editable
		//				   as_clacam  // campo clave
		//				   as_actcam  // actualizar campo
		//				   as_tabrelcam  // tabla relacionada
		//				   as_iterelcam  // item relacionado
		//				   as_cricam  // criterio campo
		//				   as_tipcam  // tipo de campo
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla de archivos txt por campo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 06/11/2007 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_archivotxtcampo (codemp,codarch,codcam,descam,inicam,loncam,edicam,clacam,actcam,tabrelcam,iterelcam,cricam, tipcam) VALUES ".
				"('".$this->ls_codemp."','".$as_codarch."',".$ai_codcam.",'".$as_descam."',".$ai_inicam.",".$ai_loncam.",".$as_edicam.",'".$as_clacam."',".
				"'".$as_actcam."','".$as_tabrelcam."','".$as_iterelcam."','".$as_cricam."','".$as_tipcam."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt M?TODO->uf_insert_archivotxt_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////					
			$ls_evento="INSERT";
			$ls_descripcion="Insert? el campo ".$ai_codcam." asociado al archivo ".$as_codarch;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_insert_archivotxt_periodo	
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_nuevo_codigo()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_nuevo_codigo()
		//		   Access: private
		//	    Arguments: as_codarch  // c?digo de archivo txt
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que trae el m?ximo c?digo de archivo registrado en la tabla sno_archivotxt
		//	   Creado Por: Ing. Mar?a Beatriz Unda
		// Fecha Creaci?n: 26/02/2009 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT MAX(codarch) AS numero ".
				"  FROM sno_archivotxt ".
				" WHERE codemp='".$this->ls_codemp."'";
				
		$data = $this->io_sql->execute($ls_sql);
		if (!$data->EOF){
			$ls_nroreg = $data->fields['numero']+1;
		}
		else {
			$ls_nroreg = '1';
		}
		unset($data);
		
		$ls_nroreg= str_pad ($ls_nroreg,4,"0",0);
		return $ls_nroreg;
	}// end function uf_nuevo_codigo()
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>