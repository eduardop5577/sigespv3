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

class sigesp_snorh_c_permiso
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_sno;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 	     Function: sigesp_snorh_c_permiso
		//		   Access: public (sigesp_snorh_d_permiso)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
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
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_permiso)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
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
	function uf_select_permiso($as_codper, $ai_numper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_permiso
		//		   Access: private
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ai_numper  // n?mero del permiso
		//	      Returns: lb_existe True si existe ? False si no existe
		//	  Description: Funcion que verifica si el permiso est? registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT numper ".
				"  FROM sno_permiso ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND numper='".$ai_numper."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_select_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
	}// end function uf_select_permiso
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_correlativo($as_codper, $ai_numper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_correlativo
		//		   Access: private (uf_guardar) 
		//	    Arguments: as_codper  // c?digo del personal
		//				   ai_numper  // c?digo del permiso
		//	      Returns: lb_valido True si lo obtuvo correctamente ? False si hubo error
		//	  Description: Funcion que busca el correlativo del ?ltimo permiso  y genera el nuevo correlativo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/03/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_numper=1;
		$ls_sql="SELECT numper as codigo ".
				"  FROM sno_permiso ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				" ORDER BY numper DESC ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_load_correlativo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_numper=intval($row["codigo"]+1);
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['ai_numper']=$ai_numper;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_correlativo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_permiso($as_codper,$ai_numper,$ad_feciniper,$ad_fecfinper,$ai_numdiaper,$ai_afevacper,$ai_tipper,$as_obsper,$as_remper,$as_numhoras,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_permiso
		//		   Access: private
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ai_numper  // N?mero del Permiso
		//				   ad_feciniper  // fecha inicio
		//				   ad_fecfinper  // fecha fin
		//				   ai_numdiaper  // n?mero de d?as
		//				   ai_afevacper  // afecta vacaciones
		//				   ai_tipper  // tipo
		//				   as_obsper  // observaci?n
		//				   as_remper  // Si el permiso es remunerado ? no
		//				   as_numhoras // numero de horas
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla de permiso
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_permiso".
				"(codemp,codper,numper,feciniper,fecfinper,numdiaper,afevacper,tipper,obsper,remper,tothorper)VALUES".
				"('".$this->ls_codemp."','".$as_codper."',".$ai_numper.",'".$ad_feciniper."','".$ad_fecfinper."',".
				"".$ai_numdiaper.",'".$ai_afevacper."',".$ai_tipper.",'".$as_obsper."','".$as_remper."','".$as_numhoras."')";
		
		$this->io_sql->begin_transaction()	;
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_insert_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insert? el Permiso ".$ai_numper." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Permiso fue Registrado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_insert_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_permiso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_permiso($as_codper,$ai_numper,$ad_feciniper,$ad_fecfinper,$ai_numdiaper,$ai_afevacper,$ai_tipper,$as_obsper,$as_remper,$as_numhoras, $aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_permiso
		//		   Access: private
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ai_numper  // N?mero del Permiso
		//				   ad_feciniper  // fecha inicio
		//				   ad_fecfinper  // fecha fin
		//				   ai_numdiaper  // n?mero de d?as
		//				   ai_afevacper  // afecta vacaciones
		//				   ai_tipper  // tipo
		//				   as_obsper  // observaci?n
		//				   as_remper  // Si el permiso es remunerado ? no
		// 				   as_numhoras // numero de horas
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ? False si hubo error en el update
		//	  Description: Funcion que actualiza en la tabla de permiso
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_permiso ".
				"	SET feciniper='".$ad_feciniper."', ".
				"		fecfinper='".$ad_fecfinper."', ".
				"		numdiaper=".$ai_numdiaper.", ".
				"		afevacper=".$ai_afevacper.", ".
				"		tipper=".$ai_tipper.", ".
				"		obsper='".$as_obsper."', ".
				"		remper='".$as_remper."', ".
				"		tothorper='".$as_numhoras."' ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND numper=".$ai_numper."";
		
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_update_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualiz? el Permiso ".$ai_numper." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Permiso fue Actualizado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_update_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	}// end function uf_insert_permiso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codper,$ai_numper,$ad_feciniper,$ad_fecfinper,$ai_numdiaper,$ai_afevacper,$ai_tipper,$as_obsper,$as_remper,$as_numhoras,$aa_seguridad)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar
		//		   Access: public (sigesp_snorh_d_permiso)
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ai_numper  // N?mero del Permiso
		//				   ad_feciniper  // fecha inicio
		//				   ad_fecfinper  // fecha fin
		//				   ai_numdiaper  // n?mero de d?as
		//				   ai_afevacper  // afecta vacaciones
		//				   ai_tipper  // tipo
		//				   as_obsper  // observaci?n
		//				   as_remper  // Si el permiso es remunerado ? no
		//				   as_numhoras // numero de horas
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ? False si hubo error en el guardar
		//	  Description: Funcion que guarda en la tabla de permiso
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ad_feciniper=$this->io_funciones->uf_convertirdatetobd($ad_feciniper);
		$ad_fecfinper=$this->io_funciones->uf_convertirdatetobd($ad_fecfinper);
		$lb_valido=false;		
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_permiso($as_codper,$ai_numper)===false)
				{
					$arrResultado=$this->uf_load_correlativo($as_codper,$ai_numper);
					$ai_numper=$arrResultado['ai_numper'];
					$lb_valido=$arrResultado['lb_valido'];
					if($lb_valido)
					{
						$lb_valido=$this->uf_insert_permiso($as_codper,$ai_numper,$ad_feciniper,$ad_fecfinper,$ai_numdiaper,
															$ai_afevacper,$ai_tipper,$as_obsper,$as_remper,$as_numhoras,
															$aa_seguridad);
					}
				}
				else
				{
					$this->io_mensajes->message("El Permiso ya existe, no lo puede incluir.");
				}
				break;
							
			case "TRUE":
				if(($this->uf_select_permiso($as_codper,$ai_numper)))
				{
					$lb_valido=$this->uf_update_permiso($as_codper,$ai_numper,$ad_feciniper,$ad_fecfinper,$ai_numdiaper,
														$ai_afevacper,$ai_tipper,$as_obsper,$as_remper,$as_numhoras,
														$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("El Permiso no existe, no lo puede actualizar.");
				}
				break;
		}		
		return $lb_valido;
	}// end function uf_guardar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_permiso($as_codper,$ai_numper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_permiso
		//		   Access: public (sigesp_snorh_d_permiso)
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ai_numper  // N?mero del Permiso
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete ? False si hubo error en el delete
		//	  Description: Funcion que elimina en la tabla de permiso
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_permiso ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND numper=".$ai_numper."";
				
       	$this->io_sql->begin_transaction();
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_delete_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="elimin? el Permiso ".$ai_numper." asociado al personal ".$as_codper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("El Permiso fue Eliminado.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_delete ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
    }// end function uf_delete_permiso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_totaldiaspermiso($as_codper,$ad_fecdes,$ad_fechas,$ai_dias)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_totaldiaspermiso
		//		   Access: public sigesp_sno_c_vacacion
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ad_fecdes  // Fecha Desde
		//				   ad_fechas  // Fecha Hasta
		//				   ai_dias  // n?mero de d?as
		//	      Returns: lb_valido True si el select se realiz? con ?xito ? False si hubo error
		//	  Description: Funcion que obtiene el total de permisos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 15/03/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ai_dias=0;
		$lb_valido=true;
		$ls_sql="SELECT sum(numdiaper) as  total ".
				"  FROM sno_permiso ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'".
				"   AND feciniper>='".$ad_fecdes."'".
				"   AND feciniper<='".$ad_fechas."'".
				"   AND afevacper=0";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_load_totaldiaspermiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				if ($row["total"]=="")
				{
					$ai_dias=0;
				}
				else
				{				
					$ai_dias=$row["total"];
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_dias']=$ai_dias;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_totaldiaspermiso
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_permisosvacaciones($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_permisosvacaiones
		//		   Access: public sigesp_sno_c_vacacion
		//	    Arguments: as_codper  // C?digo de Personal
		//	      Returns: lb_valido True si el select se realiz? con ?xito ? False si hubo error
		//	  Description: Funcion que obtiene el total de permisos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 15/03/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_permisos='';
		$ls_sql="SELECT permisos ".
				"  FROM sno_vacacpersonal  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Vacaci?n M?TODO->uf_load_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_permisos .= $rs_data->fields["permisos"];
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
		}
		$ls_permisos = str_replace('-',',',$ls_permisos);
		if($ls_permisos!='')
		{
			$ls_sql="UPDATE sno_permiso ".
					"   SET desvacper = '1' ".
					" WHERE codemp='".$this->ls_codemp."'".
					"   AND codper='".$as_codper."'".
					"   AND numper IN (".$ls_permisos.")";
	
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_delete_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
			if($lb_valido)
			{
				$ls_sql="UPDATE sno_permiso ".
						"   SET desvacper = '0' ".
						" WHERE codemp='".$this->ls_codemp."'".
						"   AND codper='".$as_codper."'".
						"   AND numper NOT IN (".$ls_permisos.")";
		
				$li_row=$this->io_sql->execute($ls_sql);
				if($li_row===false)
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_delete_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
			}
		}
		return $lb_valido;						
	}// end function uf_update_permisosvacaiones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_diaspermisos($as_codper,$ad_fecdes,$ad_fechas,$as_remper,$ai_tipper,$ai_diascontinuos=0)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_diaspermisos
		//		   Access: private
		//	    Arguments: as_codper  // C?digo de Personal
		//				   ad_fecdes  // Fecha Desde
		//				   ad_fechas  // Fecha Hasta
		//	      Returns: li_total Total de permisos dado un rango de fecha
		//	  Description: Funcion que verifica todos los permisos que tuvo el personal para un rango de fecha
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 07/09/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_total=0;
		$ls_criterio="";
		switch($as_remper)
		{
			case '0': // NO REMUNERADO
				$ls_criterio=$ls_criterio."   AND remper='0' ";
			break;
				
			case '1': // REMUNERADO
				$ls_criterio=$ls_criterio."   AND remper='1' ";
			break;
		}
		switch($ai_tipper)
		{
			case 1: // ESTUDIO
				$ls_criterio=$ls_criterio."   AND tipper=1 ";
			break;
				
			case 2: // M?DICO
				$ls_criterio=$ls_criterio."   AND tipper=2 ";
			break;
				
			case 3: // TR?MITES
				$ls_criterio=$ls_criterio."   AND tipper=3 ";
			break;
				
			case 4: // OTRO
				$ls_criterio=$ls_criterio."   AND tipper=4 ";
			break;
				
			case 5: // REPOSO
				$ls_criterio=$ls_criterio."   AND tipper=5 ";
			break;
				
			case 6: // REPOSO LABORAL
				$ls_criterio=$ls_criterio."   AND tipper=6 ";
			break;

			case 7: // AUSENCIAS
				$ls_criterio=$ls_criterio."   AND tipper=7 ";
			break;

			case 8: // SINDICAL
				$ls_criterio=$ls_criterio."   AND tipper=8 ";
			break;

			case 9: // COMPENSATORIO
				$ls_criterio=$ls_criterio."   AND tipper=9 ";
			break;
		}
		
		$ls_sql="SELECT feciniper, numdiaper ".
				"  FROM sno_permiso ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND ((('".$ad_fecdes."' BETWEEN feciniper AND fecfinper) ".
   				"    OR ('".$ad_fechas."' BETWEEN feciniper AND fecfinper)) ".
				"    OR (feciniper>='".$ad_fecdes."' and feciniper <='".$ad_fechas."'))".
				$ls_criterio.
				"   ORDER BY feciniper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Permiso M?TODO->uf_select_diaspermisos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$li_numdiaper=$rs_data->fields["numdiaper"];
				$ld_feciniper=$rs_data->fields["feciniper"];
				if($this->io_fecha->uf_comparar_fecha($ld_feciniper,$ad_fecdes))
				{
					$li_diferencia=$this->io_fecha->uf_restar_fechas($ld_feciniper,$ad_fecdes);
					$ld_feciniper=$ad_fecdes;
					$li_numdiaper=$li_numdiaper-$li_diferencia;
				}
				$ld_feciniper=$this->io_funciones->uf_convertirfecmostrar($ld_feciniper);
				for($li_i=1;$li_i<=$li_numdiaper;$li_i++)
				{
					if($this->io_fecha->uf_comparar_fecha($ld_feciniper,$ad_fechas))
					{
						if($ai_diascontinuos===0)
						{
							if($this->io_sno->uf_nro_sabydom($ld_feciniper,$ld_feciniper)==0)
							{
								$li_total=$li_total+1;
							}
						}
						else
						{
							$li_total=$li_total+1;
						}
					}
					$ld_feciniper=$this->io_sno->uf_suma_fechas($ld_feciniper,1);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_total;
	}// end function uf_select_diaspermisos
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>