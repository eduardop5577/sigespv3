<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scb_c_archivoconciliacion
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
		//	     Function: sigesp_scb_c_archivoconciliacion
		//		   Access: public (sigesp_snorh_d_archivotxt)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/11/2007 								Fecha Última Modificación : 
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
	}// end function sigesp_snorh_c_archivoconciliacion
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_archivostxt)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/11/2007 								Fecha Última Modificación : 
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
	function uf_nuevo_codigo()
	{
		$ls_sql="SELECT MAX(codarc) AS numero ".
				"  FROM scb_archivoconciliacion ".
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
	//----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_archivotxt($as_codarc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_archivotxt
		//		   Access: private
		//	    Arguments: as_codarc  // código de archivo txt
		//	      Returns: lb_existe True si existe ó False si no existe
		//	  Description: Funcion que verifica si el archivo txt esta registrado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/11/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codarc ".
			"  FROM scb_archivoconciliacion ".
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND codarc='".$as_codarc."'";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_select_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=false;
		}
		else
		{
			if($rs_data->EOF)
			{
				$lb_existe=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_existe;
	}// end function uf_select_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_archivotxt($as_codarc,$as_denarc,$as_tiparc,$as_separc,$ai_filiniarc,$as_ndequarc,$as_ncequarc,$as_dpequarc,
                                      $as_rtequarc,$as_chequarc,$as_codban,$aa_seguridad)
	{
		$lb_valido=true;
		$ls_sql="INSERT INTO scb_archivoconciliacion (codemp,codarc,denarc,codban,tiparc,separc,filiniarc,ndequarc,ncequarc,".
                        "chequarc,dpequarc,rtequarc)VALUES('".$this->ls_codemp."','".$as_codarc."','".$as_denarc."','".$as_codban."','".$as_tiparc."',".
                        "'".$as_separc."',".$ai_filiniarc.",'".$as_ndequarc."','".$as_ncequarc."','".$as_chequarc."','".$as_dpequarc."',".
                        "'".$as_rtequarc."')";
				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_insert_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));			
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó el archivo txt ".$as_codarc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad[1],$aa_seguridad[2],$ls_evento,$aa_seguridad[3],
											$aa_seguridad[4],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}		
		return $lb_valido;
	}// end function uf_insert_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_archivotxt($as_codarc,$as_denarc,$as_tiparc,$as_separc,$ai_filiniarc,$as_ndequarc,$as_ncequarc,$as_dpequarc,
                                      $as_rtequarc,$as_chequarc,$as_codban,$aa_seguridad)
	{
		$lb_valido=true;
		$ls_sql="UPDATE scb_archivoconciliacion ".
                        "   SET denarc = '".$as_denarc."', ".
                        "       codban='".$as_codban."', ".
                        "       tiparc = '".$as_tiparc."', ".  				
                        "       separc = '".$as_separc."', ".  				
                        "       filiniarc = ".$ai_filiniarc.", ".  				
                        "       ndequarc = '".$as_ndequarc."', ".  				
                        "       ncequarc = '".$as_ncequarc."', ".  				
                        "       chequarc = '".$as_chequarc."', ".  				
                        "       dpequarc = '".$as_dpequarc."', ".  				
                        "       rtequarc = '".$as_rtequarc."' ".  				
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND codarc='".$as_codarc."'";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_update_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		} 		
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó el archivo txt ".$as_codarc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad[1],$aa_seguridad[2],$ls_evento,$aa_seguridad[3],
											$aa_seguridad[4],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_update_archivotxt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar($as_existe,$as_codarc,$as_denarc,$as_tiparc,$as_separc,$ai_filiniarc,
                            $as_ndequarc,$as_ncequarc,$as_dpequarc,$as_rtequarc,$as_chequarc,$as_codban,$aa_seguridad)
	{		
		$lb_valido=false;		
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_archivotxt($as_codarc)===false)
				{
					$lb_valido=$this->uf_insert_archivotxt($as_codarc,$as_denarc,$as_tiparc,$as_separc,$ai_filiniarc,
                                                                               $as_ndequarc,$as_ncequarc,$as_dpequarc,$as_rtequarc,
                                                                               $as_chequarc,$as_codban,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("el archivo txt ya existe, no lo puede incluir.");
				}
				break;
				
			case "TRUE":
				if(($this->uf_select_archivotxt($as_codarc)))
				{
					$lb_valido=$this->uf_update_archivotxt($as_codarc,$as_denarc,$as_tiparc,$as_separc,$ai_filiniarc,
                                                                               $as_ndequarc,$as_ncequarc,$as_dpequarc,$as_rtequarc,
                                                                               $as_chequarc,$as_codban,$aa_seguridad);
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
        function uf_delete_campos($as_codarc,$aa_seguridad)
        {
		$lb_valido=false;
		$ls_sql="DELETE FROM scb_dt_archivoconciliacion ".
			" WHERE codemp='".$this->ls_codemp."'".
			"   AND codarc='".$as_codarc."'";		
	   	$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_delete_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$this->io_sql->rollback();
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó los campos del archivo ".$as_codarc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad[1],$aa_seguridad[2],$ls_evento,$aa_seguridad[3],
											$aa_seguridad[4],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_delete_campos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_archivotxt_campos($as_codarc,$ai_codcam,$as_descam,$ai_inicam,$ai_loncam,$as_colcam,$as_camrel,$as_forcam,
                                             $as_cricam,$aa_seguridad)
	{
		$lb_valido=true;
		$ls_sql="INSERT INTO scb_dt_archivoconciliacion (codemp,codarc,codcam,descam,inicam,loncam,colcam,camrel,forcam,cricam) VALUES ".
				"('".$this->ls_codemp."','".$as_codarc."',".$ai_codcam.",'".$as_descam."',".$ai_inicam.",".$ai_loncam.",".
                                " '".$as_colcam."','".$as_camrel."','".$as_forcam."','".$as_cricam."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_insert_archivotxt_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////					
			$ls_evento="INSERT";
			$ls_descripcion="Insertó el campo ".$ai_codcam." asociado al archivo ".$as_codarc;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad[1],$aa_seguridad[2],$ls_evento,$aa_seguridad[3],
											$aa_seguridad[4],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
	}// end function uf_insert_archivotxt_periodo	
	//-----------------------------------------------------------------------------------------------------------------------------------
        
	//-----------------------------------------------------------------------------------------------------------------------------------
        function uf_delete_archivotxt($as_codarc, $aa_seguridad)
        {
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    //	     Function: uf_delete_archivotxt
                    //		   Access: public (sigesp_snorh_d_archivotxt)
                    //	    Arguments: as_codarc  // código de la tabla de vacación
                    //				   aa_seguridad  // arreglo de las variables de seguridad
                    //	      Returns: lb_valido True si se ejecuto el delete ó False si hubo error en el delete
                    //    Sescription: Funcion que elimina el archivo junto con sus campos
                    //	   Creado Por: Ing. Yesenia Moreno
                    // Fecha Creación: 06/11/2007 								Fecha Última Modificación : 
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    $lb_valido=true;
                    $this->io_sql->begin_transaction();
                    $lb_valido=$this->uf_delete_campos($as_codarc, $aa_seguridad);
                    if($lb_valido)
                    {
                            $ls_sql="DELETE ".
                                    "  FROM scb_archivoconciliacion ".
                                    " WHERE codemp='".$this->ls_codemp."'".
                                    "   AND codarc='".$as_codarc."'";
                            $li_row=$this->io_sql->execute($ls_sql);
                            if($li_row===false)
                            {
                                    $lb_valido=false;
                                    $this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_delete_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                            }
                    } 

                    if($lb_valido)
                    {
                            /////////////////////////////////         SEGURIDAD               /////////////////////////////
                            $ls_evento="DELETE";
                            $ls_descripcion ="Eliminó el archivo txt ".$as_codarc." y todos los campos asociados";
                            $lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad[1],$aa_seguridad[2],$ls_evento,$aa_seguridad[3],
											$aa_seguridad[4],$ls_descripcion);
                            /////////////////////////////////         SEGURIDAD               /////////////////////////////	
                            if($lb_valido)
                            {	
                                    $this->io_sql->commit();
                                    $this->io_mensajes->message("El archivo txt fue Eliminado.");
                            }
                            else
                            {
                                    $lb_valido=false;
                                    $this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_delete_archivotxt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_load_archivotxt_campos($as_codarc,$as_tiparc)
	{
		$lb_valido=true;
		$ls_sql="SELECT codemp,codarc,codcam,descam,inicam,loncam,colcam,camrel,forcam,cricam".
                        "  FROM scb_dt_archivoconciliacion ".
                        " WHERE codemp='".$this->ls_codemp."'".
                        "   AND codarc='".$as_codarc."'".		
                        " ORDER BY codarc,codcam ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Archivo txt MÉTODO->uf_load_archivotxt_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
				$ls_colcam=$rs_data->fields["colcam"];
				$ls_camrel=$rs_data->fields["camrel"];
				$ls_cricam=$rs_data->fields["cricam"];
				$ls_forcam=$rs_data->fields["forcam"];
                                $la_camrel[0]="";
                                $la_camrel[1]="";
                                $la_camrel[2]="";
                                $la_camrel[3]="";
                                $la_camrel[4]="";
                                $la_camrel[5]="";
                                $la_camrel[6]="";
                                $la_camrel[7]="";
                                $la_camrel[8]="";
                                $la_camrel[9]="";                                
                                switch($ls_camrel)
                                {
                                    case "fecmov":
                                            $la_camrel[0]="selected";
                                            break;
                                    case "fecmovs":
                                            $la_camrel[1]="selected";
                                            break;
                                    case "dia":
                                            $la_camrel[2]="selected";
                                            break;
                                    case "desmov":
                                            $la_camrel[3]="selected";
                                            break;
                                    case "numdoc":
                                            $la_camrel[4]="selected";
                                            break;
                                    case "monto":
                                            $la_camrel[5]="selected";
                                            break;
                                    case "cargo":
                                            $la_camrel[6]="selected";
                                            break;
                                    case "abono":
                                            $la_camrel[7]="selected";
                                            break;
                                    case "codope":
                                            $la_camrel[8]="selected";
                                            break;
                                    case "ninguno":
                                            $la_camrel[9]="selected";
                                            break;
                                }
                                switch ($as_tiparc)
                                {
                                        case '0':
                                                $lo_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_codcam."'>";
                                                $lo_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_descam."'>";
                                                $lo_object[$ai_totrows][3]="<input name=txtinicam".$ai_totrows." type=text id=txtinicam".$ai_totrows." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_inicam."'>";
                                                $lo_object[$ai_totrows][4]="<input name=txtloncam".$ai_totrows." type=text id=txtloncam".$ai_totrows." class=sin-borde size=4 maxlength=3 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_loncam."'>";
                                                $lo_object[$ai_totrows][5]="<select name=cmbcamrel".$ai_totrows." id=cmbcamrel".$ai_totrows."><option value=''>--Seleccione--</option>".
                                                                           "<option value='fecmov' ".$la_camrel[0].">Fecha</option>".
                                                                           "<option value='fecmovs' ".$la_camrel[1].">Fecha Sin Separador</option>".
                                                                           "<option value='dia' ".$la_camrel[2].">Dia</option>".
                                                                           "<option value='desmov' ".$la_camrel[3].">Descripcion</option>".
                                                                           "<option value='numdoc' ".$la_camrel[4].">Documento</option>".
                                                                           "<option value='monto' ".$la_camrel[5].">Monto</option>".
                                                                           "<option value='cargo' ".$la_camrel[6].">Cargo</option>".
                                                                           "<option value='abono' ".$la_camrel[7].">Abono</option>".
                                                                           "<option value='codope' ".$la_camrel[8].">Operacion</option>".
                                                                           "<option value='ninguno' ".$la_camrel[9].">Ninguno</option></select>".
                                                                           "<input name=txtcolcam".$ai_totrows." type=hidden id=txtcolcam".$ai_totrows." value='0'>";
                                                $lo_object[$ai_totrows][6]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                                $lo_object[$ai_totrows][7]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
                                        break;

                                        case '1':
                                                $lo_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_codcam."'>";
                                                $lo_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_descam."'>";
                                                $lo_object[$ai_totrows][3]="<select name=cmbcamrel".$ai_totrows." id=cmbcamrel".$ai_totrows."><option value=''>--Seleccione--</option>".
                                                                           "<option value='fecmov' ".$la_camrel[0].">Fecha</option>".
                                                                           "<option value='fecmovs' ".$la_camrel[1].">Fecha Sin Separador</option>".
                                                                           "<option value='dia' ".$la_camrel[2].">Dia</option>".
                                                                           "<option value='desmov' ".$la_camrel[3].">Descripcion</option>".
                                                                           "<option value='numdoc' ".$la_camrel[4].">Documento</option>".
                                                                           "<option value='monto' ".$la_camrel[5].">Monto</option>".
                                                                           "<option value='cargo' ".$la_camrel[6].">Cargo</option>".
                                                                           "<option value='abono' ".$la_camrel[7].">Abono</option>".
                                                                           "<option value='codope' ".$la_camrel[8].">Operacion</option>".
                                                                           "<option value='ninguno' ".$la_camrel[9].">Ninguno</option></select>".
                                                                           "<input name=txtinicam".$ai_totrows." type=hidden id=txtinicam".$ai_totrows." value='0'>".
                                                                           "<input name=txtloncam".$ai_totrows." type=hidden id=txtloncam".$ai_totrows." value='0'>".
                                                                           "<input name=txtcolcam".$ai_totrows." type=hidden id=txtcolcam".$ai_totrows." value='0'>";
                                                $lo_object[$ai_totrows][4]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                                $lo_object[$ai_totrows][5]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
                                        break;

                                        case '2':
                                                $lo_object[$ai_totrows][1]="<input name=txtcodcam".$ai_totrows." type=text id=txtcodcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarnumero(this);' value = '".$li_codcam."'>";
                                                $lo_object[$ai_totrows][2]="<input name=txtdescam".$ai_totrows." type=text id=txtdescam".$ai_totrows." class=sin-borde size=10 maxlength=20 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_descam."'>";
                                                $lo_object[$ai_totrows][3]="<input name=txtcolcam".$ai_totrows." type=text id=txtcolcam".$ai_totrows." class=sin-borde size=3 maxlength=2 onKeyUp='javascript: ue_validarcomillas(this);' value = '".$ls_colcam."'>";
                                                $lo_object[$ai_totrows][4]="<select name=cmbcamrel".$ai_totrows." id=cmbcamrel".$ai_totrows."><option value=''>--Seleccione--</option>".
                                                                           "<option value='fecmov' ".$la_camrel[0].">Fecha</option>".
                                                                           "<option value='fecmovs' ".$la_camrel[1].">Fecha Sin Separador</option>".
                                                                           "<option value='dia' ".$la_camrel[2].">Dia</option>".
                                                                           "<option value='desmov' ".$la_camrel[3].">Descripcion</option>".
                                                                           "<option value='numdoc' ".$la_camrel[4].">Documento</option>".
                                                                           "<option value='monto' ".$la_camrel[5].">Monto</option>".
                                                                           "<option value='cargo' ".$la_camrel[6].">Cargo</option>".
                                                                           "<option value='abono' ".$la_camrel[7].">Abono</option>".
                                                                           "<option value='codope' ".$la_camrel[8].">Operacion</option>".
                                                                           "<option value='ninguno' ".$la_camrel[9].">Ninguno</option></select>".
                                                                           "<input name=txtinicam".$ai_totrows." type=hidden id=txtinicam".$ai_totrows." value='0'>".
                                                                           "<input name=txtloncam".$ai_totrows." type=hidden id=txtloncam".$ai_totrows." value='0'>";
                                                $lo_object[$ai_totrows][5]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif title=Agregar alt=Aceptar width=15 height=15 border=0></a>";
                                                $lo_object[$ai_totrows][6]="<a href=javascript:uf_delete_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif title=Eliminar alt=Eliminar width=15 height=15 border=0></a>";			
                                        break;
                                }
                            $rs_data->MoveNext();
                        }
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$lo_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_archivotxt_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	
	

}
?>