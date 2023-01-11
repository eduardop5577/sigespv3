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

class sigesp_snorh_c_fideicomiso_garantia
{
	var $io_sql;
	var $io_mensajes;
	var $io_funciones;
	var $io_seguridad;
	var $io_fun_nomina;
	var $io_fideiconfigurable;
	var $io_personal;
	var $io_sno;
	var $ls_codemp;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_c_diaferiado
		//		   Access: public (sigesp_snorh_d_fideicomiso)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 01/01/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad=new sigesp_c_seguridad();
		require_once("class_folder/class_funciones_nomina.php");
		$this->io_fun_nomina=new class_funciones_nomina();
		require_once("sigesp_snorh_c_fideiconfigurable.php");
		$this->io_fideiconfigurable=new sigesp_snorh_c_fideiconfigurable();
		require_once("sigesp_snorh_c_personal.php");
		$this->io_personal=new sigesp_snorh_c_personal();
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("sigesp_snorh_c_fideicomiso.php");
		$this->io_fideicomiso=new sigesp_snorh_c_fideicomiso();
		$this->DS=new class_datastore();		
                $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->forcalpres = 0;
	}// end function sigesp_snorh_c_fideicomiso_garantia
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_fideicomiso)
		//	  Description: Destructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 01/01/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		unset($io_include);
		unset($io_conexion);
		unset($this->io_sql);	
		unset($this->io_mensajes);		
		unset($this->io_funciones);		
		unset($this->io_seguridad);
		unset($this->io_fun_nomina);
		unset($this->io_fideiconfigurable);
		unset($this->io_personal);
		unset($this->io_sno);
        unset($this->ls_codemp);
        
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_nomina($aa_nominas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_nomina
		//		   Access: public (sigesp_snorh_p_fideicomiso.php)
		//	    Arguments: aa_nominas  // arreglo de Nominas 
		//	      Returns: lb_valido True si se ejecuto el select o False si hubo error en el select
		//	  Description: Funcion que obtiene las nominas creadas en el sistema
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 11/04/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codnom, desnom ".
				"  FROM sno_nomina ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"	AND espnom = '0' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_load_nomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$li_i=0;
			while(!$rs_data->EOF)
			{
				$aa_nominas["codnom"][$li_i]=$rs_data->fields["codnom"];
				$aa_nominas["desnom"][$li_i]=$rs_data->fields["desnom"];
				$li_i=$li_i+1;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);		
		}
		$arrResultado['aa_nominas']=$aa_nominas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_load_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_fideiperiodo($ai_anocurper,$as_mescurper,$aa_nominas,$ai_totrows,$ao_object,$ai_totrows2,$ao_object2,$as_sueint)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_fideiperiodo
		//		   Access: public (sigesp_snorh_d_tablavacacion)
		//	    Arguments: ai_anocurper  // codigo de la tabla de vacacion
		//				   as_mescurper  // total de filas del detalle
		//				   aa_nominas  // objetos del detalle
		//				   ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//                 as_sueint  // denominacion del sueldo integral
		//	      Returns: lb_valido True si se ejecuto el buscar o False si hubo error en el buscar
		//	  Description: Funcion que obtiene el fideicomiso del peroodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 01/01/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if ($as_sueint=="")
		{
			$as_sueint='Salario Normal';
		}
		else
		{
			$as_sueint=strtoupper($as_sueint);
		}
		
		$li_totnom=count((Array)$aa_nominas);
		for($li_i=0;$li_i<$li_totnom;$li_i++)
		{
			if($li_i==0)
			{
				$ls_codnom=" AND ((sno_fideiperiodo.codnom='".$aa_nominas[$li_i]."')";
			}
			else
			{
				$ls_codnom=$ls_codnom." OR (sno_fideiperiodo.codnom='".$aa_nominas[$li_i]."')";
			}
		}
		$ls_codnom=$ls_codnom.") ";
		$ls_sql="SELECT sno_fideiperiodo.codemp, sno_fideiperiodo.codnom, sno_fideiperiodo.codper, sno_fideiperiodo.anocurper, ".
				"	    sno_fideiperiodo.mescurper, sno_fideiperiodo.bonvacper, sno_fideiperiodo.bonfinper, sno_fideiperiodo.sueintper, ".
				"		sno_fideiperiodo.apoper, sno_fideiperiodo.bonextper, sno_fideiperiodo.diafid, sno_fideiperiodo.diaadi, sno_personal.cedper, ".
				"		sno_personal.nomper, sno_personal.apeper, sno_fideiperiodo.bonvacadiper, sno_fideiperiodo.bonfinadiper, sno_fideiperiodo.sueintadiper, ".
				"		sno_fideiperiodo.apopreper, sno_fideiperiodo.apoadiper,sno_personal.fecingper, sno_fideiperiodo.metodopre, sno_fideiperiodo.metodoadi  ".
				"  FROM sno_fideiperiodo, sno_personal ".
				" WHERE sno_fideiperiodo.codemp='".$this->ls_codemp."' ".
				"   AND sno_fideiperiodo.anocurper='".$ai_anocurper."'".
				"   AND sno_fideiperiodo.mescurper=".$as_mescurper." ".
				$ls_codnom.
				"   AND sno_fideiperiodo.codemp=sno_personal.codemp ".
				"   AND sno_fideiperiodo.codper=sno_personal.codper ".
				" ORDER BY sno_fideiperiodo.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_load_fideiperiodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			$ai_totrows2=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_nomper=$rs_data->fields["apeper"].", ".$rs_data->fields["nomper"];
				$li_sueintper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["sueintper"]);
				$li_bonvacper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["bonvacper"]);
				$li_bonfinper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["bonfinper"]);
				$li_apoper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["apoper"]);
				$li_bonexpter=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["bonextper"]);
				$li_diasfid=$rs_data->fields["diafid"];
				$li_diasadi=$rs_data->fields["diaadi"];

				$li_metodopre=$rs_data->fields["metodopre"];
				$li_metodoadi=$rs_data->fields["metodoadi"];

				$li_sueintadiper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["sueintadiper"]);
				$li_bonvacadiper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["bonvacadiper"]);
				$li_bonfinadiper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["bonfinadiper"]);
				$li_apoadiper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["apoadiper"]);
				$li_apopreper=$this->io_fun_nomina->uf_formatonumerico($rs_data->fields["apopreper"]);
				$ld_fecingper=$this->io_funciones->uf_convertirfecmostrar($rs_data->fields["fecingper"]);
				if ($li_diasfid > 0)
				{
                                        $ai_totrows=$ai_totrows+1;
					$ao_object[$ai_totrows][1]="<div align='center'>".$ls_codper."</div>";
					$ao_object[$ai_totrows][2]="<div align='center'>".$ls_cedper."</div>";
					$ao_object[$ai_totrows][3]="<div align='left'>".$ld_fecingper."</div>";
					$ao_object[$ai_totrows][4]="<div align='left'>".$ls_nomper."</div>";
					$ao_object[$ai_totrows][5]="<div align='right'>".$li_sueintper."</div>";
					$ao_object[$ai_totrows][6]="<div align='right'>".$li_bonexpter."</div>";
					$ao_object[$ai_totrows][7]="<div align='right'>".$li_bonvacper."</div>";
					$ao_object[$ai_totrows][8]="<div align='right'>".$li_bonfinper."</div>";
					$ao_object[$ai_totrows][9]="<div align='right'>".$li_diasfid."</div>";
					$ao_object[$ai_totrows][10]="<div align='right'>".$li_apopreper."</div>";
					$ao_object[$ai_totrows][11]="<a href=javascript:ue_mostrar_sueldo('".$ls_codper."','".$ai_anocurper."','".$as_mescurper."','".$li_metodopre."','P');    align=center><img src=../shared/imagebank/tools15/buscar.gif width=15 height=15 border=0 align=center title='".$as_sueint."'></a>";
				}
				if ($li_diasadi > 0)
				{
					$ai_totrows2=$ai_totrows2+1;
					$ao_object2[$ai_totrows2][1]="<div align='center'>".$ls_codper."</div>";
					$ao_object2[$ai_totrows2][2]="<div align='center'>".$ls_cedper."</div>";
					$ao_object2[$ai_totrows2][3]="<div align='left'>".$ld_fecingper."</div>";
					$ao_object2[$ai_totrows2][4]="<div align='left'>".$ls_nomper."</div>";
					$ao_object2[$ai_totrows2][5]="<div align='right'>".$li_sueintadiper."</div>";
					$ao_object2[$ai_totrows2][6]="<div align='right'>".$li_bonexpter."</div>";
					$ao_object2[$ai_totrows2][7]="<div align='right'>".$li_bonvacadiper."</div>";
					$ao_object2[$ai_totrows2][8]="<div align='right'>".$li_bonfinadiper."</div>";
					$ao_object2[$ai_totrows2][9]="<div align='right'>".$li_diasadi."</div>";
					$ao_object2[$ai_totrows2][10]="<div align='right'>".$li_apoadiper."</div>";
					$ao_object2[$ai_totrows2][11]="<a href=javascript:ue_mostrar_sueldo('".$ls_codper."','".$ai_anocurper."','".$as_mescurper."','".$li_metodoadi."','A');    align=center><img src=../shared/imagebank/tools15/buscar.gif width=15 height=15 border=0 align=center title='".$as_sueint."'></a>";
				}
				$rs_data->MoveNext();
			}
			if ($ai_totrows2==0)
			{
				$ai_totrows2=1;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['ai_totrows2']=$ai_totrows2;
		$arrResultado['ao_object2']=$ao_object2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_fideiperiodo
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_fideicomiso_periodo($ai_anocurper,$as_mescurper,$aa_nomsele,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_delete_fideicomiso_periodo
		//		   Access: private
		//	    Arguments: ai_anocurper  // aoo en curso seleccionado
		//	    		   as_mescurper  // mes en curso seleccionado
		//	    		   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el delete o False si hubo error en el delete
		//	  Description: Funcion que elimina en la tabla de fideiperiodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 01/01/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $li_totnom=count((Array)$aa_nomsele);
		for($li_i=0;$li_i<$li_totnom;$li_i++)
		{
			$ls_codnom=$aa_nomsele[$li_i];
			$ls_comprobante=$ai_anocurper."-".$ls_codnom."-".str_pad($as_mescurper,3,"0",0)."-P"; // Comprobante de Fideicomiso
			$lb_valido2=$this->io_fideicomiso->uf_select_comprobante_aprobado($ls_comprobante);
			if ($lb_valido2)
			{
				if ($this->io_fideicomiso->uf_integridad($as_mescurper,$ai_anocurper)===true)
				{
					$ls_sql="DELETE ".
							"  FROM sno_fideiperiodo ".
							" WHERE codemp='".$this->ls_codemp."'".
							"   AND codnom='".$ls_codnom."'".
							"   AND anocurper='".$ai_anocurper."'".
							"   AND mescurper=".$as_mescurper."";
					$li_row=$this->io_sql->execute($ls_sql);
					if($li_row===false)
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_delete_fideicomiso_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					}
					else
					{
						/////////////////////////////////         SEGURIDAD               /////////////////////////////		
						$ls_evento="DELETE";
						$ls_descripcion ="Eliminó el Fideicomiso asociado al Año ".$ai_anocurper." Mes ".$as_mescurper;
						$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               /////////////////////////////	
						$ls_comprobante=$ai_anocurper."-".$ls_codnom."-".str_pad($as_mescurper,3,"0",0)."-P"; // Comprobante de Fideicomiso
						$lb_valido=$this->io_fideicomiso->uf_delete_contabilizacion($ls_comprobante);
						if($lb_valido)
						{	
							$this->io_mensajes->message("El Fideicomiso de la Nomina ".$ls_codnom." fue Eliminado.");
						}
						else
						{
							$lb_valido=false;
							$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_delete_fideicomiso_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
						}
					}
				}
				else
				{
					$this->io_mensajes->message("No se puede eliminar la Prestacion Antiguedad de la Nomina ".$ls_codnom.". Ya se calcularon los intereses de la misma.");
					$lb_valido=false;
				}
			}
			else
			{
				$this->io_mensajes->message("La prestacion de antiguedad de la nomina ".$ls_codnom." esta aprobada, por favor reversela.");
				$lb_valido=false;
			}
		}
		
		return $lb_valido;
    }// end function uf_delete_fideicomiso_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_fideicomiso($ai_anocurper,$as_mescurper,$aa_nominas,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_fideicomiso
		//		   Access: public (sigesp_snorh_p_fideicomiso_garantia.php)
		//	    Arguments: ai_anocurper  // aoo en curso seleccionado
		//	    		   as_mescurper  // mes en curso seleccionado
		//	    		   aa_nominas  // arreglo de Nominas seleccionadas
		//	    		   aa_seguridad  // arreglo de las variables de seguridad
		//	      Returns: lb_valido True si se ejecuto el proceso o False si hubo error en el proceso
		//	  Description: Funcion que obtiene el fideicomiso LOTTT 2012
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 11/04/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
                $this->io_sql->begin_transaction();
		$ld_fecgen=""; //Fecha de Generar el fideicomiso
		if(!$this->io_fideicomiso->uf_integridad($as_mescurper,$ai_anocurper))
		{
			$lb_valido=false;
			$this->io_mensajes->message("Existen Intereses Calculados previamente.");
		}
		if($lb_valido)
		{
			$ld_fecgen=$this->io_fideicomiso->uf_load_fecha_gen($ai_anocurper,$as_mescurper,$ld_fecgen);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fideicomiso->uf_select_fideiconfigurable($ai_anocurper);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fideicomiso->uf_delete_fideiperiodo($ai_anocurper,$as_mescurper,$aa_nominas);
		}
		if($lb_valido)
		{
			$lb_valido=$this->uf_procesar_personal($ai_anocurper,$as_mescurper,$aa_nominas,$ld_fecgen,"",0);
		}
		if($lb_valido)
		{
			$lb_valido=$this->io_fideicomiso->uf_generar_data_contabilizacion($ai_anocurper,$as_mescurper,$aa_nominas,$ld_fecgen);
		}
		if($lb_valido)
		{
			/////////////////////////////////         SEGURIDAD               /////////////////////////////		
			$ls_evento="PROCESS";
			$ls_descripcion ="Procesó el Fideicomiso asociado al Año ".$ai_anocurper." Mes ".$as_mescurper;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("El Fideicomiso fue procesado.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un error al procesar el fideicomiso."); 
			$this->io_sql->rollback();
		}
		return $lb_valido;
	}// end function uf_procesar_fideicomiso
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_procesar_personal($ai_anocurper,$as_mescurper,$aa_nominas,$ad_fecgen,$as_codper,$ai_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_procesar_personal
		//		   Access: public (sigesp_snorh_p_fideicomiso.php)
		//	    Arguments: ai_anocurper // aoo en curso del periodo
		//	    		   as_mescurper // mes en curso del peroodo
		//	    		   aa_nominas // arreglo de Nominas 
		//	    		   ad_fecgen // fecha a generar el fideicomiso
		//	      Returns: lb_valido True si se ejecuto el proceso de fideicomiso o False si hubo error en el proceso
		//	  Description: Funcion que procesa el fideicomiso a todas las personas que eston en las nominas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 11/04/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_desincorporar=$this->io_sno->uf_select_config("SNO","NOMINA","DESINCORPORAR DE NOMINA","0","C");
		$ls_metalibonvac=$this->io_sno->uf_select_config("SNO","CONFIG","MET ALI BONO VAC","INTEGRAL","C");
		$this->forcalpres=$this->io_sno->uf_select_config("SNO","NOMINA","FORMA_CALCULO_PRES","0","C");		
		$ls_meses='';
                $ls_criterio="	AND (sno_hpersonalnomina.staper = '1' OR sno_hpersonalnomina.staper = '2') ";
		if($li_desincorporar=='1')
		{
		  $ls_criterio="	AND (sno_hpersonalnomina.staper = '1') ";
		}
		$li_activos=$this->io_sno->uf_select_config("SNO","NOMINA","CALCULAR_PERSONAL_ACTIVO","0","I");
		if($li_activos=='1')
		{
		  $ls_criterio= $ls_criterio."   AND (sno_personal.estper = '1' ".
									 "    OR (sno_personal.estper <> '1' ".
						             "   AND  substr(cast(sno_personal.fecegrper as char(10)),6,2) > '".str_pad($as_mescurper,2,"0",0)."' ".
									 "   AND  substr(cast(sno_personal.fecegrper as char(10)),1,4) >= '".$ai_anocurper."')) ";
		}
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		if ($this->forcalpres==1)
		{
			if (($ai_mes == 1)||($ai_mes == 2)||($ai_mes == 4)||($ai_mes == 5)||($ai_mes == 7)||($ai_mes == 8)||($ai_mes == 10)||($ai_mes == 11))
			{
				$this->io_mensajes->message("ERROR->Segon su configuracion de calculo de Prestacion de Antiguedad, el mes seleccionado no es volido."); 
				$lb_valido=false;
			}
		}
		if ($lb_valido)
		{
			$ai_fin=3;
			if ($this->forcalpres==2)
			{
				$ai_fin=1;
			}
			for($i=1;$i<=$ai_fin;$i++)
			{
				if($ai_mes==0)
				{
					$ai_mes=12;
					$ai_anio=intval($ai_anio)-1;
				}
				$ls_meses.="'".$ai_anio."-".str_pad($ai_mes,2,'0',0)."',";
				$ai_mes=intval($ai_mes)-1;
			}
			$ls_meses='('.substr($ls_meses,0,strlen($ls_meses)-1).')';
			$ls_bonovacacional='';
			switch($ai_tipo)
			{
				case 0: // Calculo normal de la prestacion antiguedad por nomina mes a mes
					$li_totnom=count((Array)$aa_nominas);
					for($li_i=0;$li_i<$li_totnom;$li_i++)
					{
						if($li_i==0)
						{
							$ls_codnom=" AND ((sno_hpersonalnomina.codnom='".$aa_nominas[$li_i]."')";
						}
						else
						{
							$ls_codnom=$ls_codnom." OR (sno_hpersonalnomina.codnom='".$aa_nominas[$li_i]."')";
						}
					}
					$ls_codnom=$ls_codnom.") ";
					// Sentencia modificada por Ofimatica de Venezuela el 02-06-2011 para agregar el campo sno_hpersonalnomina.codtabvac en los campos del select y en el group by
					$ls_sql="SELECT sno_hpersonalnomina.codper, sno_hpersonalnomina.codnom, MIN(sno_hpersonalnomina.codded) AS codded, MIN(sno_hpersonalnomina.codtipper) AS codtipper, MAX(sno_personal.fecingper) AS fecingper, MAX(sno_hpersonalnomina.codtabvac) AS codtabvac, ".
							$ls_bonovacacional.
							"		(SELECT sno_fideicomiso.capantcom  ".
							"          FROM sno_fideicomiso ".
							"         WHERE sno_fideicomiso.codemp = sno_hpersonalnomina.codemp ".
							"           AND sno_fideicomiso.codper = sno_hpersonalnomina.codper ".
							"         GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codper, sno_fideicomiso.capantcom ) AS capantcom ".
							"  FROM sno_hpersonalnomina, sno_hnomina, sno_hperiodo, sno_personal ".
							" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."' ".
							$ls_codnom.
							"	AND sno_hpersonalnomina.anocur = '".$ai_anocurper."' ".
							$ls_criterio.
							"	AND substr(cast(sno_hperiodo.fecdesper as char(10)),1,7) IN ".$ls_meses." ".
							"	AND sno_hnomina.espnom = '0' ".
							"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
							"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
							"   AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
							"	AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
							"	AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
							"	AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
							"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
							"	AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
							"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
							"	AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
							" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codper, sno_hpersonalnomina.codnom ".
							" ORDER BY sno_hpersonalnomina.codper ";
				break;
				
				case 1: // Calculo normal de la prestacion antiguedad anterior
					$ls_sql="SELECT sno_sueldoshistoricos.codper, '".$aa_nominas[0]."' AS codnom, sno_sueldoshistoricos.codded, sno_sueldoshistoricos.codtipper, sno_personal.fecingper, ''  AS codtabvac,".
							"		sno_sueldoshistoricos.sueint AS sueintper, 0 AS asifidper, 0 AS asifidpat, sno_sueldoshistoricos.sueint AS sueldobonvac, ".
							"		(SELECT sno_fideicomiso.capantcom  ".
							"          FROM sno_fideicomiso ".
							"         WHERE sno_fideicomiso.codemp = sno_personal.codemp ".
							"           AND sno_fideicomiso.codper = sno_personal.codper) AS capantcom ".
							"  FROM sno_sueldoshistoricos ".
							" INNER JOIN sno_personal ".
							"    ON sno_sueldoshistoricos.codemp = '".$this->ls_codemp."' ".
							"	AND sno_sueldoshistoricos.fecsue IN ".$ls_meses." ".
							"	AND sno_personal.estper = '1'".
							"   AND sno_sueldoshistoricos.codemp = sno_personal.codemp ".
							"   AND sno_sueldoshistoricos.codper = sno_personal.codper ";
				break;
			}
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_procesar_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				$li_nprocesados=0;
				$li_integrar=trim($this->io_sno->uf_select_config("SNO","NOMINA","INT_ASIG_EXTRA","0","I"));
				$ls_fraccion=trim($this->io_sno->uf_select_config("SNO","NOMINA","FRACCION ALICUOTA","0","I"));
				$ls_incvacagui=trim($this->io_sno->uf_select_config("SNO","NOMINA","INC_VACACIONES_AGUINALDO","0","I"));
				$ls_complemento=trim($this->io_sno->uf_select_config("SNO","NOMINA","COMPLEMENTO ANTIGUEDAD","0","I"));
				$ls_fps=trim($this->io_sno->uf_select_config("SNO","FPS VENEZUELA","COD PLAN"," ","C"));	
				$li_acumintdiasadi=$this->io_sno->uf_select_config("SNO","NOMINA","CALCULO_ACUM_INT_DIAS_ADICIONALES","0","I");
				$li_desincorporar=trim($this->io_sno->uf_select_config("SNO","NOMINA","DESINCORPORAR DE NOMINA","0","C"));
				$li_antprimeranio=trim($this->io_sno->uf_select_config("SNO","NOMINA","ANTIGUEDAD_PRIMER_AÑO","0","I"));
				
				// Para el calculo de fideicomiso garantia
				$ls_tipdepqui=trim($this->io_sno->uf_select_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO QUINCENA","0","C"));
				$ls_tipdepadi=trim($this->io_sno->uf_select_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO ADICIONAL","0","C"));
				$ls_tipdepvac=trim($this->io_sno->uf_select_config("SNO","NOMINA","SALARIO NORMAL DEPOSITO VACACION","0","C"));
				// Para el calculo de fideicomiso garantia
				
				// Agregado por Ofimatica de Venezuela el 02-06-2011, para cargar la configuracion de si se aplica o no los dias adicionales de bono vacacional para el calculo del fideicomiso.
				$ls_diasadicionalesBV=trim($this->io_sno->uf_select_config("SNO","NOMINA","DIAS_ADICIONALES_BV","0","I"));			
				// Fin de los agregado
				
				if($li_desincorporar==1)
				{
					if(($ls_tipdepqui<>1)||($ls_tipdepadi<>1)||($ls_tipdepvac<>1))
					{
						$this->io_mensajes->message("ERROR-> Hay un error en la configuracion, si se desincorpora de la nomina al salir de vacaciones,debe configurar las prestaciones con Promedio mensual integral."); 
						$lb_valido=false;
					}
				}
				while((!$rs_data->EOF)&&($lb_valido))
				{
					$li_nprocesados=$li_nprocesados+1;
					$li_diaagui=0; // Doas de Aguinaldo
					$li_diainc=0; // Doas de Incidencia
					$li_diaadic=0; // Doas Adicinales
					$li_diainc_agui=0; // Doas de Incidencia Aguinaldo
					$li_diainc_vac=0; // Doas de Incidencia Vacaciones
					$li_diacal=30; // Doas de Colculo
					$li_mescal=12; // Mes de Colculo
					$li_diabonvac=0; // Doas de bono vacacional
					$li_diaagui=0; // Dias de Aguinaldo
					$li_diafide=0; // Dias de Fideicomiso
					$lb_calcular=false; // si se debe calcular el fideicomiso para el personal
					$li_monto_vaca=0; // Monto de la alicuota de Vacaciones
					$li_monto_agui=0; // Monto de la alicuota de Aguinaldo
					$li_monto_aporte=0; // Monto del Aporte
					$li_antiguedad=0; // Monto de Antiguedad
					$li_sueintper=0;
					$li_bonextper=0;
					$li_suediaadiper=0;
					$li_monto_vaca_adi=0;
					$li_monto_agui_adi=0;
					$li_monto_aporte_adicional=0;
					$li_diasadicionalesBV=0; // Variable para guardar los dias de bono vacacional que le toca a cada persona de la nomina.
					$li_capantcom=trim($rs_data->fields["capantcom"]);
					if(strtotime($rs_data->fields["fecingper"])<=strtotime("1997-06-19"))
					{
						$li_fecha_ingreso=substr("1997-06-19",5,2);	
					}
					else
					{
						$li_fecha_ingreso=substr($rs_data->fields["fecingper"],5,2);				
					}
					$ls_codper=$rs_data->fields["codper"];
					$ls_codnom=$rs_data->fields["codnom"];
					$ls_codded=$rs_data->fields["codded"];
					$ls_codtipper=$rs_data->fields["codtipper"];
					$li_codtabvac=$rs_data->fields["codtabvac"];
					$ls_mensaje='';
					if($li_capantcom=="")
					{
						$li_capantcom="0";
					}
					if($lb_valido)
					{
						$lb_activo=$this->uf_activo_ultimoperiodo($ls_codnom,$ls_codper,$ai_anocurper,$as_mescurper);		
					}	
					if($lb_valido)
					{
                                            $ld_fecingper=$rs_data->fields["fecingper"];
                                            $lb_activo=$this->uf_eliminar_prestacionanterior($ls_codper,$ld_fecingper);
                                        }
					if(($lb_valido)&&($lb_activo))
					{
						$arrResultado=$this->uf_verificar_personal($ls_codper,$ad_fecgen,$li_diabonvac,$li_diaagui,$lb_calcular,$li_diainc_vac,$li_diainc_agui,$li_diaadic,
																   $li_diafide, $li_antiguedad,$li_antprimeranio,$ls_codnom,$ls_mensaje);
						$lb_calcular=$arrResultado['ab_calcular'];
						$li_diainc_vac=$arrResultado['ai_diainc_vac'];
						$li_diainc_agui=$arrResultado['ai_diainc_agui'];
						$li_diaadic=$arrResultado['ai_diaadic'];
						$li_diafide=$arrResultado['ai_diafide'];
						$li_antiguedad=$arrResultado['ai_antiguedad'];
						$ls_mensaje=$arrResultado['as_mensaje'];
						$lb_valido=$arrResultado['lb_valido'];
					}
					if(($lb_valido)&&(!$lb_calcular)&&($ls_mensaje!=''))
					{
						$this->io_mensajes->message("INFORMACION->".$ls_mensaje); 
					}
					if(($lb_valido)&&($lb_calcular))
					{
						$arrResultado=$this->obtenerSueldo($ls_codper,$ai_anocurper,$as_mescurper,$ls_tipdepqui,$li_sueintper,$li_bonextper,'S',$aa_nominas);
						$li_sueintper=$arrResultado['ai_sueintper'];
						$li_bonextper=$arrResultado['ai_bonextper'];
						$lb_valido=$arrResultado['lb_valido'];
						if($lb_valido)
						{					
							$li_suediaper=round((($li_sueintper)/$li_diacal),2);
							if($li_integrar=='1')
							{
								$li_suediaper=round($li_suediaper+$li_bonextper,2);
							}
						}
					}														  
					if(($lb_valido)&&($lb_calcular))
					{
						$arrResultado=$this->obtenerSueldo($ls_codper,$ai_anocurper,$as_mescurper,$ls_tipdepvac,$li_sueldobonvac,$li_bonextper,'V',$aa_nominas);
						$li_sueldobonvac=$arrResultado['ai_sueintper'];
						$li_bonextper=$arrResultado['ai_bonextper'];
						$lb_valido=$arrResultado['lb_valido'];
						$li_sueldobonvac=round((($li_sueldobonvac)/$li_diacal),2);
						$arrResultado=$this->io_fideiconfigurable->uf_load_dias_vacaagui($ai_anocurper,$ls_codded,$ls_codtipper,$li_diabonvac,$li_diaagui);
						$li_diabonvac=$arrResultado['ai_diavac'];
						$li_diaagui=$arrResultado['ai_diaagui'];
						$lb_valido=$arrResultado['lb_valido'];
					}
					if(($lb_valido)&&($lb_calcular))
					{
						if($ls_fraccion=="1")
						{
							if(($li_diainc_vac!=0)||($li_diainc_agui!=0))
							{
								$li_diabonvac=$li_diainc_vac;
								$li_diaagui=$li_diainc_agui;
							}
						}
						if ($ls_diasadicionalesBV=='1')
						{
						   $arrResultado=$this->io_fideicomiso->uf_obtener_dias_adicionales_BV($li_codtabvac, $li_antiguedad, $li_diasadicionalesBV);
						   $li_diasadicionalesBV=$arrResultado['ai_diasadicionalesBV'];
						   $lb_valido=$arrResultado['lb_valido'];
						   if ($lb_valido)
						   {
							   $li_diabonvac=$li_diabonvac+$li_diasadicionalesBV;
						   }    
						}
						$li_monto_vaca=((($li_sueldobonvac*$li_diabonvac)/12)/$li_diacal);
						$li_monto_agui=((($li_suediaper*$li_diaagui)/12)/$li_diacal);					
						//Fin - Metodo para actualizar sueldo integral anterior
						if ($ls_incvacagui=='1') // Se incluye la alicuota de Vacaciones en los Aguinaldos
						{
							$li_monto_agui=(((($li_suediaper+$li_monto_vaca)*$li_diaagui)/12)/$li_diacal);
						}				
						if($li_integrar=='0')
						{
							$li_monto_aporte=(($li_monto_vaca+$li_monto_agui+$li_suediaper+$li_bonextper)*$li_diafide);
						}
						else
						{
							$li_monto_aporte=(($li_monto_vaca+$li_monto_agui+$li_suediaper)*$li_diafide);
						}
						if($ls_complemento=="1")
						{
							if(($li_diaadic!=0)&&($li_capantcom=="1")&&(($as_mescurper==$li_fecha_ingreso)||((($as_mescurper>=$li_fecha_ingreso)&&($this->forcalpres==1)))))
							{
								$arrResultado=$this->obtenerSueldoDiasAdicionales($ls_codper,$ai_anocurper,$as_mescurper,$ls_tipdepadi,$li_sueldointadi,'S',$aa_nominas);
								$li_sueldointadi=$arrResultado['ai_sueintper'];
								$lb_valido=$arrResultado['lb_valido'];
								if($lb_valido)
								{
									$arrResultado=$this->obtenerSueldoDiasAdicionales($ls_codper,$ai_anocurper,$as_mescurper,$ls_tipdepadi,$li_sueldobonvacadi,'V',$aa_nominas);
									$li_sueldobonvacadi=$arrResultado['ai_sueintper'];
									$lb_valido=$arrResultado['lb_valido'];
								}
								if($lb_valido)
								{
									$li_suediaadiper=round((($li_sueldointadi)/$li_diacal),2);
									$li_suediabonvac=round((($li_sueldobonvacadi)/$li_diacal),2);
									$li_monto_vaca_adi=((($li_suediabonvac*$li_diabonvac)/12)/$li_diacal);
									$li_monto_agui_adi=((($li_suediaadiper*$li_diaagui)/12)/$li_diacal);
									if ($ls_incvacagui=='1') // Se incluye la alicuota de Vacaciones en los Aguinaldos
									{
										$li_monto_agui_adi=(((($li_suediaadiper+$li_monto_vaca_adi)*$li_diaagui)/12)/$li_diacal);
									}				
									$li_monto_aporte_adicional=(($li_suediaadiper +$li_monto_vaca_adi+$li_monto_agui_adi)*$li_diaadic);
								}
							}
							else
							{
								$li_diaadic=0;
							}
						}
						$li_monto_aporte=round($li_monto_aporte,2);
						if($this->io_fideicomiso->uf_select_fideicomiso($ls_codper)==false)
						{
							$ld_fecha=$ad_fecgen;
							$ls_sql="INSERT INTO sno_fideicomiso(codemp,codper,codfid,ficfid,ubifid,cuefid,fecingfid,capfid,capantcom,scg_cuentafid,scg_cuentaintfid)VALUES".
									"('".$this->ls_codemp."','".$ls_codper."','".$ls_fps."','0000000000','0000000000',' ','".$ld_fecha."','S','0','','')";
							$li_row=$this->io_sql->execute($ls_sql);
							if($li_row===false)
							{
								$lb_valido=false;
								$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_procesar_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
							}
						}
						if($lb_valido)
						{
							if($this->io_fideicomiso->uf_select_fideiperiodo($ls_codnom,$ls_codper,$ai_anocurper,$as_mescurper)==false)
							{
								$ls_sql="INSERT INTO sno_fideiperiodo ".
										"(codemp,codnom,codper,anocurper,mescurper,bonvacper,bonfinper,sueintper,apoper,bonextper,diafid,diaadi,bonvacadiper, ".
										" bonfinadiper, sueintadiper, apopreper, apoadiper,metodopre,metodoadi)VALUES ".
										"('".$this->ls_codemp."','".$ls_codnom."','".$ls_codper."','".$ai_anocurper."',".$as_mescurper.",".
										"".number_format($li_monto_vaca,2,'.','').",".number_format($li_monto_agui,2,'.','').",".number_format($li_sueintper,2,'.','').",".
										"".number_format(($li_monto_aporte+$li_monto_aporte_adicional),2,'.','').",".number_format($li_bonextper,2,'.','').",".$li_diafide.",".$li_diaadic.",".
										"".number_format($li_monto_vaca_adi,2,'.','').",".number_format($li_monto_agui_adi,2,'.','').",".number_format($li_sueldointadi,2,'.','').",".
										"".number_format($li_monto_aporte,2,'.','').",".number_format($li_monto_aporte_adicional,2,'.','').",'".$ls_tipdepqui."','".$ls_tipdepadi."')";
								$li_row=$this->io_sql->execute($ls_sql);
								if($li_row===false)
								{
									$lb_valido=false;
									$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_procesar_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
								}
							}
						}
					}
					$rs_data->MoveNext();
				}
				$this->io_sql->free_result($rs_data);	
				if($li_nprocesados==0)	
				{
					$this->io_mensajes->message("No hay personal para procesar."); 
				}
			}
		}
		return $lb_valido;
	}// end function uf_procesar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_verificar_personal($as_codper,$ad_fecgen,$ai_diabonvac,$ai_diaagui,$ab_calcular,$ai_diainc_vac,$ai_diainc_agui,$ai_diaadic,
								   $ai_diafide,$ai_antiguedad,$ai_antprimeranio,$as_codnom,$as_mensaje)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_verificar_personal
		//		   Access: private
		//	    Arguments: as_codper  // Codigo de personal
		//	    		   ad_fecgen  // Fecha de generar
		//	    		   ai_diabonvac  // Doa de Bono Vacacional
		//	    		   ai_diaagui  // Doa de Aguinaldo
		//	    		   ab_calcular  // si se debe calcular el fideicomiso del personal
		//	    		   ai_diainc_vac  // Doas de Incidencia Vacaciones
		//	    		   ai_diainc_agui  // Doas de Incidencia de Aguinaldo
		//	    		   ai_diaadic  // Doas Adicinales
		//	    		   ai_diafide  // Doas de Fideicomiso
		//               Agregado por Ofimatica de Venezuela el 02-06-2011 para el manejo de los dias adicionales de Bono Vacacional
		//                 ai_antiguedad // Antiguedad en aoos    
		//               Fin de lo agregado por Ofimatica de Venezuela
		//	      Returns: lb_valido True si se ejecuto el proceso  o False si hubo error en el proceso 
		//	  Description: Funcion que verifica que el personal se le debe calcular el fideicomiso 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 12/04/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ab_calcular=false;
		$ld_fecingper="";
		$li_meses=0;
		$as_mensaje='';
		$ai_maxmes=1;
		if($this->forcalpres==0)
		{
			$ai_maxmes=3;
		}	
		$arrResultado=$this->io_personal->uf_load_fechaingreso($as_codper,$ld_fecingper);
		$ld_fecingper=$arrResultado['ad_fecingper'];
		$lb_valido=$arrResultado['lb_valido'];
                unset($arrResultado);
		if($lb_valido)
		{
			//------------------------------Calculamos los Meses en la Institucion---------------------------------------
			$li_diap=intval(substr($ad_fecgen,8,2));
			$li_diai=intval(substr($ld_fecingper,8,2));
			$li_mesp=intval(substr($ad_fecgen,5,2));
			$li_mesi=intval(substr($ld_fecingper,5,2));
			$li_anop=intval(substr($ad_fecgen,0,4));
			$li_anoi=intval(substr($ld_fecingper,0,4));
			$li_antiguedad=$li_anop - $li_anoi;         
			$ai_antiguedad=intval($li_antiguedad);
			$lb_procesar=false;
			$li_mescal=0;
			if($li_anoi==$li_anop)
			{
				if($li_mesi==$li_mesp)
				{
					$li_meses=1;
				}
				else
				{
					$li_meses=(($li_mesp-$li_mesi)+1);
				}
			}
			else
			{
				if($li_mesi<$li_mesp)
				{
					$li_meses=((12*($li_anop-$li_anoi))+(($li_mesp-$li_mesi)));
				}
				elseif($li_mesi==$li_mesp)
				{
					$li_meses=(12*($li_anop-$li_anoi));
				}
				elseif($li_mesi>$li_mesp)
				{			 
					$li_meses=((12*($li_anop-$li_anoi))+($li_mesp-$li_mesi));
				}
			}
			if($this->forcalpres==1)
			{
				if($li_anoi==$li_anop)
				{
					if(($li_mesp==3)&&(($li_mesi==1)||($li_mesi==2)||($li_mesi==3)))
					{
						$lb_procesar=true;
						$li_mescal=(3-$li_mesi)+1;
					}
					if(($li_mesp==6)&&(($li_mesi==4)||($li_mesi==5)||($li_mesi==6)))
					{
						$lb_procesar=true;
						$li_mescal=(6-$li_mesi)+1;
					}
					if(($li_mesp==9)&&(($li_mesi==7)||($li_mesi==8)||($li_mesi==9)))
					{
						$lb_procesar=true;
						$li_mescal=(9-$li_mesi)+1;;
					}
					if(($li_mesp==12)&&(($li_mesi==10)||($li_mesi==11)||($li_mesi==12)))
					{
						$lb_procesar=true;
						$li_mescal=(12-$li_mesi)+1;
					}
				}
				else
				{
					$li_mescal=3;
				}
			}
			if($li_meses>=$ai_maxmes)
			{
				if($lb_valido)
				{
					$arrResultado=$this->uf_load_ultimocalculo($as_codnom,$as_codper,$ad_fecgen,$ld_ultcal);
					$ld_ultcal=$arrResultado['ad_ultcal'];
					$lb_valido=$arrResultado['lb_valido'];
                                        unset($arrResultado);
				}
				if($ld_ultcal=='')
				{
					if($li_meses==$ai_maxmes)
					{		
						$ab_calcular=true;
						$ai_diafide=15;
						if($this->forcalpres==1)
						{
							if($li_meses==1)
							{
								$ai_diafide=5;
							}
							if($li_meses==2)
							{
								$ai_diafide=10;
							}
						}
						if($this->forcalpres==2)
						{
							$ai_diafide=5;
						}
						if($li_meses<12)
						{
							$ai_diainc_vac=round((($li_meses*$ai_diabonvac)/12),1);
							$ai_diainc_agui=round((($li_meses*$ai_diaagui)/12),1);
						}
						else
						{
							$ai_diaadic=$this->uf_load_dias_adicionales($li_meses,$ai_diaadic,$ai_antprimeranio);
						}				
					}
					else
					{
						if(($this->forcalpres==1)&&($li_mescal>=1)&&($li_mescal<=3))
						{
							$ab_calcular=true;
							$ai_diafide=15;
							if($this->forcalpres==1)
							{
								if($li_mescal==1)
								{
									$ai_diafide=5;
								}
								if($li_mescal==2)
								{
									$ai_diafide=10;
								}
							}
							if($this->forcalpres==2)
							{
								$ai_diafide=5;
							}
							if($li_meses<12)
							{
								$ai_diainc_vac=round((($li_meses*$ai_diabonvac)/12),1);
								$ai_diainc_agui=round((($li_meses*$ai_diaagui)/12),1);
							}
							else
							{
								$ai_diaadic=$this->uf_load_dias_adicionales($li_meses,$ai_diaadic,$ai_antprimeranio);
							}
						}
						else
						{
							if(($li_mescal>$ai_maxmes)&&($ld_ultcal==''))
							{
								$as_mensaje='La persona de Codigo '.$as_codper.'. No tiene Deuda Anterior o Prestaciones Anteriores Calculadas';
								$ab_calcular=false;
							}
						}
					}
				}
				else
				{
					if($this->forcalpres==1)
					{
						$ai_maxmes=3;
					}
					list($year,$mon,$day) = explode('-',$ad_fecgen);
					$fechaanterior = date('Y-m-d',mktime(0,0,0,$mon-$ai_maxmes,'01',$year));  
					if($fechaanterior===$ld_ultcal)
					{
						$ab_calcular=true;
						$ai_diafide=15;
						if($this->forcalpres==1)
						{
							if($li_meses==1)
							{
								$ai_diafide=5;
							}
							if($li_meses==2)
							{
								$ai_diafide=10;
							}
						}
						if($this->forcalpres==2)
						{
							$ai_diafide=5;
						}
						if($li_meses<12)
						{
							$ai_diainc_vac=round((($li_meses*$ai_diabonvac)/12),1);
							$ai_diainc_agui=round((($li_meses*$ai_diaagui)/12),1);
						}
						else
						{
							$ai_diaadic=$this->uf_load_dias_adicionales($li_meses,$ai_diaadic,$ai_antprimeranio);
						}
					}
					else
					{
						$ai_diaadic=$this->uf_load_dias_adicionales($li_meses,$ai_diaadic,$ai_antprimeranio);
						if ($ai_diaadic>0)
						{
							$ab_calcular=true;
						}
					}
				}
			}
		}                
		$arrResultado['ab_calcular']=$ab_calcular;
		$arrResultado['ai_diainc_vac']=$ai_diainc_vac;
		$arrResultado['ai_diainc_agui']=$ai_diainc_agui;
		$arrResultado['ai_diaadic']=$ai_diaadic;
		$arrResultado['ai_diafide']=$ai_diafide;
		$arrResultado['ai_antiguedad']=$ai_antiguedad;
		$arrResultado['as_mensaje']=$as_mensaje;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_verificar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_dias_adicionales($ai_meses,$ai_diaadic,$ai_antprimeranio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_dias_adicionales
		//		   Access: private
		//	    Arguments: ai_meses  // Meses en la Institucion
		//	    		   ai_diaadic  // Doas Adicinales que le corresponden
		//	      Returns: lb_valido True si no ocurrio algon error False si hubo errores
		//	  Description: Funcion que obtiene los doas adicinales de acuerdo a los meses laborados
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 18/04/2006 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_i=0;
		if($ai_antprimeranio==0)
		{
			$li_i=2;
		}
		if ($this->forcalpres==1)
		{
			$resto=fmod($ai_meses, 12);
			if ($resto<=2)
			{
				$ai_meses=$ai_meses-$resto;
			}
		}
		switch($ai_meses)
		{
			case 12:
				$ai_diaadic=2-$li_i;
				break;
			case 24:
				$ai_diaadic=4-$li_i;
				break;
			case 36:
				$ai_diaadic=6-$li_i;
				break;
			case 48:
				$ai_diaadic=8-$li_i;
				break;
			case 60:
				$ai_diaadic=10-$li_i;
				break;
			case 72:
				$ai_diaadic=12-$li_i;
				break;
			case 84:
				$ai_diaadic=14-$li_i;
				break;
			case 96:
				$ai_diaadic=16-$li_i;
				break;
			case 108:
				$ai_diaadic=18-$li_i;
				break;
			case 120:
				$ai_diaadic=20-$li_i;
				break;
			case 132:
				$ai_diaadic=22-$li_i;
				break;
			case 144:
				$ai_diaadic=24-$li_i;
				break;
			case 156:
				$ai_diaadic=26-$li_i;
				break;
			case 168:
				$ai_diaadic=28-$li_i;
				break;
			case 180:
				$ai_diaadic=30-$li_i;
				break;
			case 192:
				$ai_diaadic=30;
				break;
		}
		if($ai_meses>192)
		{
			$li_resto = $ai_meses%12; 
			if($li_resto!=0)
			{
				$ai_diaadic=0;
			}
			else
			{
				$ai_diaadic=30;
			}
		}
		
		return $ai_diaadic;
	}// end function uf_load_dias_adicionales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function obtenerSueldo($codper,$ai_anocurper,$as_mescurper,$as_tipdepqui,$ai_sueintper,$ai_bonextper,$ai_tipo,$aa_nominas)
	{
	   	$lb_valido=true;
		$anio=$ai_anocurper;
		$mes=intval($as_mescurper);
		$ls_criterio='';
        $ls_sql='';
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		for($i=1;$i<=6;$i++)
		{
			if($ai_mes==0)
			{
				$ai_mes=12;
				$ai_anio=intval($ai_anio)-1;
			}
			$ls_meses.="'".$ai_anio."-".str_pad($ai_mes,2,'0',0)."',";
			$ai_mes=intval($ai_mes)-1;
		}
		$ls_meses='('.substr($ls_meses,0,strlen($ls_meses)-1).')';
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		$ls_codnom='';
		$ls_codnom2='';
		$li_totnom=count((Array)$aa_nominas);
		for($li_i=0;$li_i<$li_totnom;$li_i++)
		{
				if($li_i==0)
				{
						$ls_codnom=" AND ((sno_hsalida.codnom='".$aa_nominas[$li_i]."')";
						$ls_codnom2=" AND ((sno_periodo.codnom='".$aa_nominas[$li_i]."')";
				}
				else
				{
						$ls_codnom=$ls_codnom." OR (sno_hsalida.codnom='".$aa_nominas[$li_i]."')";
						$ls_codnom2=$ls_codnom2." OR (sno_periodo.codnom='".$aa_nominas[$li_i]."')";
				}
		}
		$ls_codnom=$ls_codnom.") ";      
		$ls_codnom2=$ls_codnom2.") ";      
                
		switch ($as_tipdepqui)
		{
			case 0://PROMEDIO MENSUAL VARIABLE
				$ls_criterio="(SELECT SUM(convar)/count(codemp) ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS sueldovariable, ".
					     "(SELECT confij ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS sueldofijo, ".
					     "(SELECT comsue ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS compensacion ";
			break;
			
			case 1://PROMEDIO MENSUAL INTEGRAL
				$ls_criterio="(SELECT SUM(convar)/count(codemp) ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS sueldovariable, ".
					     "(SELECT SUM(confij)/count(codemp) ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS sueldofijo, ".
					     "(SELECT SUM(comsue)/count(codemp) ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS compensacion ";
			break;
			
			case 2://ULTIMO MES EFECTIVO
				$ls_criterio="(SELECT convar ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS sueldovariable, ".
					     "(SELECT confij ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS sueldofijo, ".
					     "(SELECT comsue ".
					     "   FROM sno_sueldoshistoricos ".
					     "  WHERE codemp = '".$this->ls_codemp."' ".
					     "    AND codper = '".$codper."' ".
					     "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS compensacion ";
			break;
                    
			case 3://ULTIMA QUINCENA EFECTIVA
				$ls_sql="SELECT SUM(valsal*2) AS sueldovariable, 0 AS sueldofijo, 0 AS compensacion ".
                                        "  FROM sno_hsalida ".
                                        "  WHERE codemp = '".$this->ls_codemp."' ".
                                        "    AND codper = '".$codper."' ".
                                        $ls_codnom.
                                        "    AND codperi IN (SELECT MAX(codperi) FROM sno_periodo ".
                                        "                     WHERE SUBSTR(CAST(fechasper AS char(10)),1,7)='".$ai_anio."-".str_pad($ai_mes,2,'0',0)."'".
                                        "                      ". $ls_codnom2.") ".
                                        "    AND codconc IN (SELECT CODCONC FROM sno_hconcepto ".
                                        "                     WHERE sno_hsalida.codemp=sno_hconcepto.codemp ".
                                        "                       AND sno_hsalida.codnom=sno_hconcepto.codnom ". 
                                        "                       AND sno_hsalida.anocur=sno_hconcepto.anocur ". 
                                        "                       AND sno_hsalida.codperi=sno_hconcepto.codperi ".
                                        "                       AND sno_hsalida.codconc=sno_hconcepto.codconc ".
                                        "                       AND salnor IN ('F','V'))";
			break;
		}
		if ($ls_sql=="")
		{
			$ls_sql="SELECT ".$ls_criterio.
							"  FROM sno_sueldoshistoricos ".
							" WHERE codemp = '".$this->ls_codemp."' ".
							"   AND codper = '".$codper."' ".
							" GROUP BY codemp, codper";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->FIDEICOMISO METODO->obtenerSueldo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				if($ai_tipo=='S')
				{
					$ai_sueintper = number_format($rs_data->fields["sueldovariable"] + $rs_data->fields["sueldofijo"] + $rs_data->fields["compensacion"],2,'.','');
				}
				else
				{
					$ai_sueintper = number_format($rs_data->fields["sueldovariable"] + $rs_data->fields["sueldofijo"] + $rs_data->fields["compensacion"],2,'.','');
				}
				$lb_valido=true;
			}
		}
		$arrResultado['ai_sueintper']=$ai_sueintper;
		$arrResultado['ai_bonextper']=$ai_bonextper;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function obtenerSueldo	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function obtenerSueldoDiasAdicionales($codper,$ai_anocurper,$as_mescurper,$as_tipdepqui,$ai_sueintper,$ai_tipo,$aa_nominas)
	{
	   	$lb_valido=true;
		$anio=$ai_anocurper;
		$mes=intval($as_mescurper);
		$ls_criterio='';
        $ls_sql='';
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		for($i=1;$i<=12;$i++)
		{
			$ai_mes=intval($ai_mes)-1;
			if($ai_mes==0)
			{
				$ai_mes=12;
				$ai_anio=intval($ai_anio)-1;
			}
			$ls_meses.="'".$ai_anio."-".str_pad($ai_mes,2,'0',0)."',";
		}
		$ls_meses='('.substr($ls_meses,0,strlen($ls_meses)-1).')';
		$ai_anio=intval($ai_anocurper);
		$ai_mes=intval($as_mescurper);
		$ls_codnom='';
		$ls_codnom2='';
		$li_totnom=count((Array)$aa_nominas);
		for($li_i=0;$li_i<$li_totnom;$li_i++)
		{
				if($li_i==0)
				{
						$ls_codnom=" AND ((sno_hsalida.codnom='".$aa_nominas[$li_i]."')";
						$ls_codnom2=" AND ((sno_periodo.codnom='".$aa_nominas[$li_i]."')";
				}
				else
				{
						$ls_codnom=$ls_codnom." OR (sno_hsalida.codnom='".$aa_nominas[$li_i]."')";
						$ls_codnom2=$ls_codnom2." OR (sno_periodo.codnom='".$aa_nominas[$li_i]."')";
				}
		}
		$ls_codnom=$ls_codnom.") ";      
		$ls_codnom2=$ls_codnom2.") ";      
                
		switch ($as_tipdepqui)
		{
			case 0://PROMEDIO MENSUAL VARIABLE
				$ls_criterio="(SELECT SUM(convar)/count(codemp)".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS sueldovariable, ".
                                             "(SELECT confij ".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS sueldofijo, ".
                                             "(SELECT comsue ".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS compensacion ";
			break;
			
			case 1://PROMEDIO MENSUAL INTEGRAL
				$ls_criterio="(SELECT SUM(convar)/count(codemp) ".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS sueldovariable, ".
                                             "(SELECT SUM(confij)/count(codemp)".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS sueldofijo, ".
                                             "(SELECT SUM(comsue)/count(codemp)".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) IN ".$ls_meses.") AS compensacion ";
			break;
			
			case 2://ULTIMO MES EFECTIVO
				$ls_criterio="(SELECT convar ".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS sueldovariable, ".
                                             "(SELECT confij ".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS sueldofijo, ".
                                             "(SELECT comsue ".
                                             "   FROM sno_sueldoshistoricos ".
                                             "  WHERE codemp = '".$this->ls_codemp."' ".
                                             "    AND codper = '".$codper."' ".
                                             "    AND substr(cast(fecsue as char(10)),1,7) = '".$ai_anio."-".str_pad($ai_mes,2,'0',0)."') AS compensacion ";                            
			break;
                            
			case 3://ULTIMA QUINCENA EFECTIVA
				$ls_sql="SELECT SUM(valsal*2) AS sueldovariable, 0 AS sueldofijo, 0 AS compensacion ".
                                        "  FROM sno_hsalida ".
                                        "  WHERE codemp = '".$this->ls_codemp."' ".
                                        "    AND codper = '".$codper."' ".
                                        $ls_codnom.
                                        "    AND codperi IN (SELECT MAX(codperi) FROM sno_periodo ".
                                        "                     WHERE SUBSTR(CAST(fechasper AS char(10)),1,7)='".$ai_anio."-".str_pad($ai_mes,2,'0',0)."'".
                                        "                      ". $ls_codnom2.") ".
                                        "    AND codconc IN (SELECT CODCONC FROM sno_hconcepto ".
                                        "                     WHERE sno_hsalida.codemp=sno_hconcepto.codemp ".
                                        "                       AND sno_hsalida.codnom=sno_hconcepto.codnom ". 
                                        "                       AND sno_hsalida.anocur=sno_hconcepto.anocur ". 
                                        "                       AND sno_hsalida.codperi=sno_hconcepto.codperi ".
                                        "                       AND sno_hsalida.codconc=sno_hconcepto.codconc ".
                                        "                       AND salnor IN ('F','V'))";
			break;
		}
		if ($ls_sql=="")
		{                
			$ls_sql="SELECT ".$ls_criterio.
							"  FROM sno_sueldoshistoricos ".
							" WHERE codemp = '".$this->ls_codemp."' ".
							"   AND codper = '".$codper."' ".
							" GROUP BY codemp, codper";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->FIDEICOMISO METODO->obtenerSueldoDiasAdicionales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				if($ai_tipo=='S')
				{
					$ai_sueintper = number_format($rs_data->fields["sueldovariable"] + $rs_data->fields["sueldofijo"] + $rs_data->fields["compensacion"],2,'.','');
				}
				else
				{
					$ai_sueintper = number_format($rs_data->fields["sueldofijo"] + $rs_data->fields["compensacion"],2,'.','');
				}
				$lb_valido=true;
			}
		}		
		$arrResultado['ai_sueintper']=$ai_sueintper;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}//
	//-----------------------------------------------------------------------------------------------------------------------------------	 

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_ultimocalculo($as_codnom,$as_codper,$ad_fecgen,$ad_ultcal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_ultimocalculo
		//		   Access: private
		//	    Arguments: as_codnom  // Codigo de Nomina
		//	    		   as_codper  // Codigo de Personal
		//	    		   ad_ultcal  // fecha del ultimo calculo
		//	      Returns: lb_valido True si existe o False si no existe
		//	  Description: Funcion que obtiene el ultimo calculo de las prestaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 10/10/2014 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_ultcal="";
		$ls_sql="SELECT anocurper, mescurper ".
				"  FROM sno_fideiperiodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND diafid>0 ".
				" ORDER BY anocurper DESC, mescurper DESC";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_load_asignaciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_ultcal=$rs_data->fields["anocurper"]."-".str_pad($rs_data->fields["mescurper"],2,'0',0)."-01";
			}
			else
			{
				$ls_sql="SELECT anocurper, mescurper ".
						"  FROM sno_fideiperiodo ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND codper='".$as_codper."' ".
						"   AND diafid>0 ".
						" ORDER BY anocurper DESC, mescurper DESC";
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_load_asignaciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$lb_valido=false;
				}
				else
				{
					if(!$rs_data->EOF)
					{
						$ad_ultcal=$rs_data->fields["anocurper"]."-".str_pad($rs_data->fields["mescurper"],2,'0',0)."-01";
					}
					else
					{
						$ls_sql="SELECT feccordeu ".
								"  FROM sno_deudaanterior ".
								" WHERE codemp='".$this->ls_codemp."' ".
								"   AND codper='".$as_codper."' ";
						$rs_data=$this->io_sql->select($ls_sql);
						if($rs_data===false)
						{
							$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_load_asignaciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
							$lb_valido=false;
						}
						else
						{
							if(!$rs_data->EOF)
							{
								list($year,$mon,$day) = explode('-',$ad_fecgen);
								$fechaanterior = date('Y-m-d',mktime(0,0,0,$mon-3,'01',$year));   
								$ad_ultcal=$rs_data->fields["feccordeu"];
								if($this->io_fecha->uf_comparar_fecha($ad_ultcal,$fechaanterior))
								{
									if($ad_ultcal!=$fechaanterior)
									{
										$ad_ultcal='';
									}
								}
							}
						}
					}
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		if (($this->forcalpres==2)&&($ad_ultcal==''))
		{
			list($year,$mon,$day) = explode('-',$ad_fecgen);
			$mon = intval($mon-1);
			if ($mon==0)
			{
				$mon='12';
				$year=$year-1;
			}
			$ad_ultcal=$year."-".str_pad($mon,2,'0',0)."-01";		
		}
		$arrResultado['ad_ultcal']=$ad_ultcal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_ultimocalculo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_activo_ultimoperiodo($as_codnom,$as_codper,$ai_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_activo_ultimoperiodo
		//		   Access: private
		//	    Arguments: as_codnom  // Codigo de Nomina
		//	    		   as_codper  // Codigo de Personal
		//	    		   ad_ultcal  // fecha del ultimo calculo
		//	      Returns: lb_valido True si existe o False si no existe
		//	  Description: Funcion que obtiene el ultimo calculo de las prestaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 10/10/2014 								Fecha oltima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_ultcal="";
		$ls_sql="SELECT staper ".
				"  FROM sno_hpersonalnomina,sno_hperiodo ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom='".$as_codnom."' ".
				"   AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2') ".
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),1,7) IN ('".$ai_anocurper."-".$as_mescurper."')  ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
        	$this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_load_asignaciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_activo_ultimoperiodo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_eliminar_prestacionanterior($as_codper,$ad_fecingper)
	{
		$lb_valido=true;
		$ad_fecingper=SUBSTR($ad_fecingper,0,4)."-".SUBSTR($ad_fecingper,5,2)."-01";
		$ls_sql="DELETE ". 
                        "  FROM sno_fideiperiodo ".
                        " WHERE sno_fideiperiodo.codemp='".$this->ls_codemp."' ".
                        "   AND sno_fideiperiodo.codper='".$as_codper."' ".
                        "   AND CAST(anocurper||'-'||mescurper||'-01' AS DATE) < CAST('".$ad_fecingper."' AS DATE)  ";
		$rs_data=$this->io_sql->execute($ls_sql);
		if($rs_data===false)
		{
                        $this->io_mensajes->message("CLASE->Fideicomiso MoTODO->uf_eliminar_prestacionanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_eliminar_prestacionanterior
	//-----------------------------------------------------------------------------------------------------------------------------------
        
}
?>
