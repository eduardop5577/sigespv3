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

class sigesp_snorh_c_hojatiempo
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_sno;
	var $io_fun_nomina;
	var $ls_codemp;
	var $ls_codnom;

	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_hojatiempo
		//		   Access: public (sigesp_sno_d_conceptopersonal)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
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
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom="";		
        $this->ld_fecdesper="";		
	}// end function sigesp_snorh_c_hojatiempo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_sno_d_conceptopersonal)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fun_nomina);
        unset($this->ls_codemp);
        unset($this->ls_codnom);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_hojatiempo($as_codper,$ai_totrows,$aa_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_hojatiempo
		//		   Access: public (sigesp_sno_d_persxconce.php)
		//	    Arguments: as_codper  // Código de personal
		//				   ai_totrows  // Total de Filas
		//				   aa_object  //  Arreglo de objectos que se van a imprimir
		//	      Returns: $lb_valido True si se ejecuto el select ó False si hubo error en el select
		//	  Description: Función que obtiene el sueldo de un personal dado un ó sueldo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/07/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechas=$this->ld_fecdesper;
		if(substr($ld_fechas,5,2)=='01')
		{
			$li_anio=substr($ld_fechas,0,4)-1;
			$ld_fecdes=$li_anio."-12-01";
		}
		else
		{
			$li_mes=str_pad(substr($ld_fechas,5,2)-1,2,"0",0);
			$ld_fecdes=substr($ld_fechas,0,4)."-".$li_mes."-01";
		}
		$ls_sql="SELECT sno_hojatiempo.fechojtie, sno_hojatiempo.esthojtie, sno_hojatiempo.semhojtie, sno_hojatiempo.codhor, ".
				"		sno_hojatiempo.horlab, sno_hojatiempo.horextlab, sno_hojatiempo.trasub, sno_hojatiempo.traesc, ".
				"		sno_hojatiempo.repcom, sno_horario.denhor ".
				"  FROM sno_hojatiempo ".
				" INNER JOIN sno_horario ".
				"    ON sno_hojatiempo.codemp='".$this->ls_codemp."' ".
				"   AND sno_hojatiempo.codnom='".$this->ls_codnom."' ".
				"   AND sno_hojatiempo.codper='".$as_codper."' ".
				"   AND sno_hojatiempo.fechojtie >= '".$ld_fecdes."'".
				"   AND sno_hojatiempo.codemp = sno_horario.codemp ".
				"   AND sno_hojatiempo.codhor = sno_horario.codhor ".
				" ORDER BY sno_hojatiempo.fechojtie ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Hoja Tiempo MÉTODO->uf_load_hojatiempo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$ai_totrows=0;
			while(!$rs_data->EOF)
			{
				$ai_totrows++;
				$ld_fechojtie=$this->io_funciones->uf_convertirfecmostrar($rs_data->fields["fechojtie"]);
				$li_semhojtie=$rs_data->fields["semhojtie"];
				$ls_codhor=$rs_data->fields["codhor"];
				$ls_denhor=$rs_data->fields["denhor"];
				$li_horlab=$rs_data->fields["horlab"];
				$li_horextlab=$rs_data->fields["horextlab"];
				$li_trasub=$rs_data->fields["trasub"];
				$li_traesc=$rs_data->fields["traesc"];
				$li_repcom=$rs_data->fields["repcom"];
				$li_esthojtie=$rs_data->fields["esthojtie"];
				$ls_trasub="";
				if($li_trasub=="1")
				{
					$ls_trasub="checked";
				}
				$ls_traesc="";
				if($li_traesc=="1")
				{
					$ls_traesc="checked";
				}
				$ls_repcom="";
				if($li_repcom=="1")
				{
					$ls_repcom="checked";
				}
				$ls_esthojtie="";
				if($li_esthojtie=="1")
				{
					$ls_esthojtie="checked";
				}
				$aa_object[$ai_totrows][1]="<input name=txtfechojtie".$ai_totrows." type=text id=txtfechojtie".$ai_totrows." value=".$ld_fechojtie." class=sin-borde size=12 maxlength=10 onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=ue_validar_formatofecha(this);>";
				$aa_object[$ai_totrows][2]="<input name=txtsemhojtie".$ai_totrows." type=text id=txtsemhojtie".$ai_totrows." value=".$li_semhojtie." class=sin-borde size=4 maxlength=2 onKeyPress=return(ue_formatonumero(this,'','',event))>";
				$aa_object[$ai_totrows][3]="<input name=txtcodhor".$ai_totrows." type=hidden id=txtcodhor".$ai_totrows." value=".$ls_codhor.">".
									       "<input name=txtdenhor".$ai_totrows." type=text id=txtdenhor".$ai_totrows." value=".$ls_denhor." class=sin-borde size=25 readonly>".
									       "<a href='javascript:ue_buscarhorario(".$ai_totrows.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Buscar' width='15' height='15' border='0'></a>";
				$aa_object[$ai_totrows][4]="<input name=txthorlab".$ai_totrows." type=text id=txthorlab".$ai_totrows." value=".$li_horlab." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
				$aa_object[$ai_totrows][5]="<input name=txthorextlab".$ai_totrows." type=text id=txthorextlab".$ai_totrows." value=".$li_horextlab." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
				$aa_object[$ai_totrows][6]="<input name=chktrasub".$ai_totrows." type=checkbox id=chktrasub".$ai_totrows." value=1 class=sin-borde ".$ls_trasub.">";
				$aa_object[$ai_totrows][7]="<input name=chktraesc".$ai_totrows." type=checkbox id=chktraesc".$ai_totrows." value=1 class=sin-borde ".$ls_traesc.">";
				$aa_object[$ai_totrows][8]="<input name=chkrepcom".$ai_totrows." type=checkbox id=chkrepcom".$ai_totrows." value=1 class=sin-borde ".$ls_repcom.">";
				$aa_object[$ai_totrows][9]="<input name=txtesthojtie".$ai_totrows." type=hidden id=txtesthojtie".$ai_totrows." value=".$li_esthojtie." >".
										   "<input name=chkesthojtie".$ai_totrows." type=checkbox id=chkesthojtie".$ai_totrows." value=1 class=sin-borde ".$ls_esthojtie." disabled>";
				$aa_object[$ai_totrows][10]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
				$aa_object[$ai_totrows][11]="<a href=javascript:uf_eliminar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";	
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
			$ai_totrows++;
			$aa_object[$ai_totrows][1]="<input name=txtfechojtie".$ai_totrows." type=text id=txtfechojtie".$ai_totrows." class=sin-borde size=12 maxlength=10 onKeyDown=javascript:ue_formato_fecha(this,'/',patron,true,event); onBlur=ue_validar_formatofecha(this);>";
			$aa_object[$ai_totrows][2]="<input name=txtsemhojtie".$ai_totrows." type=text id=txtsemhojtie".$ai_totrows." class=sin-borde size=4 maxlength=2 onKeyPress=return(ue_formatonumero(this,'','',event))>";
			$aa_object[$ai_totrows][3]="<input name=txtcodhor".$ai_totrows." type=hidden id=txtcodhor".$ai_totrows." >".
									   "<input name=txtdenhor".$ai_totrows." type=text id=txtdenhor".$ai_totrows." class=sin-borde size=25 readonly>".
									   "<a href='javascript:ue_buscarhorario(".$ai_totrows.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Buscar' width='15' height='15' border='0'></a>";
			$aa_object[$ai_totrows][4]="<input name=txthorlab".$ai_totrows." type=text id=txthorlab".$ai_totrows." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
			$aa_object[$ai_totrows][5]="<input name=txthorextlab".$ai_totrows." type=text id=txthorextlab".$ai_totrows." class=sin-borde size=4 maxlength=2 onKeyDown=javascript:ue_validarnumero(this); style=text-align:right>";
			$aa_object[$ai_totrows][6]="<input name=chktrasub".$ai_totrows." type=checkbox id=chktrasub".$ai_totrows." value=1 class=sin-borde>";
			$aa_object[$ai_totrows][7]="<input name=chktraesc".$ai_totrows." type=checkbox id=chktraesc".$ai_totrows." value=1 class=sin-borde>";
			$aa_object[$ai_totrows][8]="<input name=chkrepcom".$ai_totrows." type=checkbox id=chkrepcom".$ai_totrows." value=1 class=sin-borde>";
			$aa_object[$ai_totrows][9]="<input name=txtesthojtie".$ai_totrows." type=hidden id=txtesthojtie".$ai_totrows." value=0 >".
									   "<input name=chkesthojtie".$ai_totrows." type=checkbox id=chkesthojtie".$ai_totrows." value=1 class=sin-borde disabled>";
			$aa_object[$ai_totrows][10]="<a href=javascript:uf_agregar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/aprobado.gif alt=Aceptar width=15 height=15 border=0></a>";
			$aa_object[$ai_totrows][11]="<a href=javascript:uf_eliminar_dt(".$ai_totrows.");><img src=../shared/imagebank/tools15/deshacer.gif alt=Deshacer width=15 height=15 border=0></a>";	
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['aa_object']=$aa_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_hojatiempo	
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_hojatiempo($as_codper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_hojatiempo
		//		   Access: private
		//	    Arguments: as_codper  // código de personal
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el update ó False si hubo error en el update
		//    Description: Funcion que elimina la hoja de ruta
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_hojatiempo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND codnom='".$this->ls_codnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND esthojtie='0' ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Hoja Tiempo MÉTODO->uf_delete_hojatiempo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		} 
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="DELETE";
			$ls_descripcion ="Eliminó las hojas de tiempo no aprobadas del personal ".$as_codper." asociado a la nómina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////				
		}
		return $lb_valido;
	}// end function uf_update_conceptopersonal	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guardar_hojatiempo($as_codper,$ad_fechojtie,$ai_semhojtie,$as_codhor,$ai_horlab,$ai_horextlab,$ai_trasub,$ai_traesc,$ai_repcom,
								   $aa_seguridad)
	{		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_guardar_hojatiempo
		//		   Access: public (sigesp_sno_d_hojatiempo)
		//	    Arguments: as_codper  // código de personal
		//				   ad_fechojtie  // Fecha
		//				   ai_semhojtie  // semana
		//				   as_codhor  // horario
		//				   ai_horlab  // horas laboradas
		//				   ai_horextlab  // horas extra
		//				   ai_trasub  // trabajo subterraneo
		//				   ai_traesc  // trabajo escalera
		//				   ai_repcom  // reposo comida
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que gurada la hoja de tiempo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fechojtie=$this->io_funciones->uf_convertirdatetobd($ad_fechojtie);
		$ls_sql="INSERT INTO sno_hojatiempo (codemp,codnom,codper,fechojtie,esthojtie,semhojtie,codhor,horlab,horextlab,trasub,traesc,repcom)".
				"VALUES ('".$this->ls_codemp."','".$this->ls_codnom."','".$as_codper."','".$ad_fechojtie."','0','".$ai_semhojtie."','".$as_codhor."',".
				"".$ai_horlab.",".$ai_horextlab.",'".$ai_trasub."','".$ai_traesc."','".$ai_repcom."') ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Hoja Tiempo MÉTODO->uf_guardar_hojatiempo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		} 
		else
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="INSERT";
			$ls_descripcion ="Insertó la Hoja de tiempo del personal ".$as_codper." de fecha ".$ad_fechojtie." asociado a la nómina ".$this->ls_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		return $lb_valido;
	}// end function uf_guardar_hojatiempo	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//--------------------------------------------------------------
	function uf_cargarnomina($as_codnom,$ai_calculada)
	{
		//////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cargarnomina
		//		   Access: private
		//	  Description: Función que obtiene todas las nóminas y las carga en un 
		//				   combo para seleccionarlas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/02/2008 								Fecha Última Modificación : 
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
				"   AND sno_nomina.hojtienom='1'".
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
			$io_mensajes->message("Clase->Seleccionar Nómina Método->uf_cargarnomina Error->".$io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_calculada="";
			while(!$rs_data->EOF)
			{
				$ls_codnom=$rs_data->fields["codnom"];
				$ls_desnom=$rs_data->fields["desnom"];
				$ls_selected="";
				if($as_codnom==$ls_codnom)
				{
					$ls_selected="selected";
				}
				print "<option value='".$ls_codnom."' ".$ls_selected.">".$ls_codnom."-".$ls_desnom."</option>";				
				$li_calculada=str_pad($this->uf_existesalida($ls_codnom),1,"0");
				$ls_calculada=$ls_calculada."   <input name='calculada".$ls_codnom."' type='hidden' id='calculada".$ls_codnom."' value='".$li_calculada."'>";
				$rs_data->MoveNext();
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
		// Fecha Creación: 16/02/2006 								Fecha Última Modificación : 
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
			$this->io_mensajes->message("CLASE->Cargo MÉTODO->uf_existesalida ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			while(!$rs_data->EOF)
			{
				if($rs_data->fields["total"]>0)
				{
					$lb_valido=true;
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	
}
?>