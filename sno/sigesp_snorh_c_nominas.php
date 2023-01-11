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

class sigesp_snorh_c_nominas
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
		//	     Function: sigesp_sno_c_cargo
		//		   Access: public (sigesp_sno_d_nominas)
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 15/02/2006 								Fecha Última Modificación : 
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
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();		
		require_once("sigesp_snorh_c_dedicacion.php");
		$this->io_dedicacion=new sigesp_snorh_c_dedicacion();		
		require_once("sigesp_snorh_c_tipopersonal.php");
		$this->io_tipopersonal=new sigesp_snorh_c_tipopersonal();		
		require_once("sigesp_snorh_c_escaladocente.php");
		$this->io_escaladocente=new sigesp_snorh_c_escaladocente();		
		require_once("sigesp_snorh_c_clasifidocente.php");
		$this->io_clasificaciondocente=new sigesp_snorh_c_clasifidocente();		
		require_once("sigesp_snorh_c_ubicacionfisica.php");
		$this->io_ubicacion=new sigesp_snorh_c_ubicacionfisica();		
		require_once("sigesp_snorh_c_clasificacionobreros.php");
		$this->io_obreros=new sigesp_snorh_c_clasificacionobreros();		
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad=new sigesp_c_seguridad();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_destructor()
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_destructor
		//		   Access: public (sigesp_snorh_d_nominas)
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
		unset($this->io_fecha);
		unset($this->io_dedicacion);
		unset($this->io_tipopersonal);
		unset($this->io_escaladocente);
		unset($this->io_clasificaciondocente);
		unset($this->io_ubicacion);
		unset($this->io_sno);
        unset($this->ls_codemp);
	}// end function uf_destructor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_select_nomina($as_codigo)
	{    
		////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:    uf_select_nomina
		//	Arguments:   $as_codigo // codigo de la nomina 
		//	Returns:	 $lb_valido // True si realizo el select correctamente o False en caso contrario
		//	Description: Funcion que selecciona los datos de la nomina segun el codigo pasado por  parametros
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codnom ".
				"  FROM sno_nomina ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codigo."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->nomina MÉTODO->uf_select_profesion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		}// end function uf_select_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_nomina($as_codnom,$as_desnom,$as_tippernom,$as_despernom,$as_anocurnom,$ad_fecininom,$as_peractnom,
							  $as_tipnom,$as_subnom,$as_racnom,$as_adenom,$as_espnom,$as_ctnom,$as_ctmetnom,$as_consulnom,
							  $as_descomnom,$as_codpronom,$as_codbennom,$as_conaponom,$as_cueconnom,$as_notdebnom,$as_numvounom,
							  $as_recdocnom,$as_tipdocnom,$as_recdocapo,$as_tipdocapo,$as_conpernom,$as_conpronom,$as_titrepnom,
							  $as_codorgcestic,$as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,$as_cueconfid,
							  $as_divcon,$as_informa,$ai_genrecdocpagperche,$as_tipdocpagperche,$ai_estctaalt,
							  $as_racobrnom,$as_nomliq,$as_hojtienom,$as_tiksuel,$as_codfuefin,$as_cersindis,$as_estagrapo,$as_recdoccaunom,
							  $as_tipdoccaunom,$as_codclanom,$as_codclaapo,$as_codclafid,$as_codclaper,$as_codclacau,
                                                          $ai_estctaaltapo,$as_pasape,$aa_seguridad)

	{
		////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_insert_nomina
		//	Arguments:    $ar_datos  // arreglos con los datos de la  nomina 
		//	Returns:	  $lb_valido    True si el inserto correctamente o False en caso contrario
		//	Description:  Funcion que inserta los datos de una nomina  nueva
		//////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_diabonvacnom=0;
		$li_diainivacnom=0;
		$li_diareivacnom=0;
		$li_diatopvacnom=0;
		$li_diaincvacnom=0;
		$ls_perresnom="000";
		$li_numpernom=0;
		switch($as_tippernom)
		{
			case "0": // Semanal
				$li_numpernom=52;
				break;
			case "1": // Quincenal
				$li_numpernom=24;
				break;
			case "2": // Mensual
				$li_numpernom=12;
				break;
			case "3": // Anual
				$li_numpernom=1;
				break;
		}

		$ls_sql="INSERT INTO sno_nomina(codemp,codnom,desnom,tippernom,despernom,anocurnom,fecininom,peractnom,numpernom,tipnom, ".
				"subnom,racnom,adenom,espnom,ctnom,ctmetnom,diabonvacnom,diareivacnom,diainivacnom,diatopvacnom, ".
				"diaincvacnom,consulnom,descomnom,codpronom,codbennom,conaponom,cueconnom,notdebnom,numvounom,recdocnom,".
				"tipdocnom,recdocapo,tipdocapo,perresnom,conpernom,conpronom,titrepnom,codorgcestic,confidnom,recdocfid,tipdocfid,".
				"codbenfid,cueconfid,divcon,informa,recdocpagperche,tipdocpagperche,estctaalt,racobrnom,nomliq,hojtienom,cestiksuel,".
				"codfuefin,cersindis,estagrapo,recdoccaunom,tipdoccaunom,codclanom,codclaapo,codclafid,codclaper,codclacau,estctaaltapo,pasape) VALUES ".
				"('".$this->ls_codemp."','".$as_codnom."','".$as_desnom."',".
				"'".$as_tippernom."','".$as_despernom."','".$as_anocurnom."','".$ad_fecininom."','".$as_peractnom."',".
				"".$li_numpernom.",'".$as_tipnom."',".$as_subnom.",".$as_racnom.",".$as_adenom.",".$as_espnom.",".$as_ctnom.", ".
				"'".$as_ctmetnom."','".$li_diabonvacnom."','".$li_diareivacnom."','".$li_diainivacnom."','".$li_diatopvacnom."', ".
				"'".$li_diaincvacnom."','".$as_consulnom."','".$as_descomnom."','".$as_codpronom."','".$as_codbennom."','".$as_conaponom."', ".
				" '".$as_cueconnom."','".$as_notdebnom."','".$as_numvounom."','".$as_recdocnom."','".$as_tipdocnom."','".$as_recdocapo."', ".
				"'".$as_tipdocapo."','".$ls_perresnom."','".$as_conpernom."','".$as_conpronom."','".$as_titrepnom."','".$as_codorgcestic."',".
				"'".$as_confidnom."','".$as_recdocfid."','".$as_tipdocfid."','".$as_codbenfid."','".$as_cueconfid."','".$as_divcon."',
				'".$as_informa."' ,'".$ai_genrecdocpagperche."' ,'".$as_tipdocpagperche."', '".$ai_estctaalt."','".$as_racobrnom."','".$as_nomliq."',".
				"'".$as_hojtienom."','".$as_tiksuel."','".$as_codfuefin."','".$as_cersindis."','".$as_estagrapo."','".$as_recdoccaunom."','".$as_tipdoccaunom."',".
				"'".$as_codclanom."','".$as_codclaapo."','".$as_codclafid."','".$as_codclaper."','".$as_codclacau."','".$ai_estctaaltapo."',".$as_pasape.")";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_nomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			if($lb_valido)
			{
				$lb_valido=$this->uf_sno_fill_periodo($as_tippernom,$ad_fecininom,$as_codnom);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_subnomina_default($as_codnom);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_cargo_default($as_codnom);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_tabulador_default($as_codnom);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_grado_default($as_codnom);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_dedicacion_default();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_tipopersonal_default();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_asignacioncargo_default($as_codnom);
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_escaladocente_default();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_clasificaciondocente_default();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_ubicacionfisica_default();
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_insert_clasificacionobreros_default();
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó la Nómina ".$as_codnom;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			}
		}
		if($lb_valido)
		{	
			$this->io_mensajes->message("La Nómina fue Registrada.");
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_mensajes->message("Ocurrio un error al Registrar la Nómina"); 
			$this->io_sql->rollback();
		}
		return $lb_valido;
	}// end function uf_insert_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_sno_fill_periodo($as_tippernom,$ad_fecininom,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_sno_fill_periodo
		//	Arguments:    $as_tippernom //  tipo de periodo (quincenal,semanal,anual,semanal) 
		//                $adt_fecininom  //	 fecha del inicio de la nomina 
		//                $as_codigo  //  codigo de la nomina 
		//	Returns:	  $lb_valido True si genero los periodos False en caso contrario
		//	Description:  Funcion que genera los periodos por nomina al momento de guardar la nomina 
		/////////////////////////////////////////////////////////////////////////////////////////////
		switch($as_tippernom)
		{
			case "0": // Semanal
				$li_periodos=52;
				break;
			case "1": // Quincenal
				$li_periodos=24;
				break;
			case "2": // Mensual
				$li_periodos=12;
				break;
			case "3": // Anual
				$li_periodos=1;
				break;
		}
		$li_per=(365/$li_periodos);
		$li_lenper=round($li_per,0);
		$lb_valido=true;
		$ldt_fecha=$ad_fecininom;
		$ld_anoinicial=substr($_SESSION["la_empresa"]["periodo"],0,4);
		for($i=1;($i<=$li_periodos)&&($lb_valido);$i++)
		{
			$ls_codperi=$this->io_funciones->uf_cerosizquierda($i,3);
			$ldt_fec=$this->uf_final_periodo($ldt_fecha,$li_lenper);
			$ldt_final=$this->io_funciones->uf_convertirdatetobd($ldt_fec);
			$li_totper=0;
			$li_cerper=0;
			$li_conper=0;
			$li_apoconper=0;
			$ls_obsper="";
			$ls_sql="INSERT INTO sno_periodo(codemp,codnom,codperi,fecdesper,fechasper,totper,cerper,conper,apoconper,obsper) ".
			        "  VALUES ('".$this->ls_codemp."','".$as_codnom."','".$ls_codperi."','".$ldt_fecha."','".$ldt_final."', ".
					" ".$li_totper.",".$li_cerper.",".$li_conper.",".$li_apoconper.",'".$ls_obsper."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if(($li_row===false))
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_sno_fill_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
				$lb_valido=true;
				$ldt_final=$this->io_funciones->uf_convertirfecmostrar($ldt_final);
				$ldt_fecha=$this->io_sno->uf_suma_fechas($ldt_final,1);
				$ldt_fecha=$this->io_funciones->uf_convertirdatetobd($ldt_fecha);
			}
			$ld_anoactual=substr($ldt_fecha,0,4);
			if($ld_anoinicial!=$ld_anoactual)
			{
				break;
			}
		}
		if($lb_valido)
		{
			$ls_sql="INSERT INTO sno_periodo(codemp,codnom,codperi,fecdesper,fechasper,totper,cerper,conper,apoconper,obsper) ".
			        " VALUES ('".$this->ls_codemp."','".$as_codnom."','000','1900-01-01','1900-01-01',0,0,0,0,'Periodo Nulo')";
			$li_row=$this->io_sql->execute($ls_sql);
			if(($li_row===false))
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_sno_fill_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		
		}
		return $lb_valido;
	}// end function uf_sno_fill_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_final_periodo($adt_fecha,$ai_lenper)
	{   ///////////////////////////////////////////////////////////////////
		//	Function:     uf_final_periodo
		//	Arguments:    $adt_fecha //  fecha del periodo
		//                $ai_lenper //	cantidad de dias del periodo         
		//	Returns:	  $ld_final // fecha final del periodo
		//	Description:  calcula la fecha final del periodo  
		/////////////////////////////////////////////////////////////////////
		$ldt_fecha=$this->io_funciones->uf_convertirfecmostrar($adt_fecha);
		if ((($ai_lenper==15)&&(substr($ldt_fecha,0,2)=="16"))||($ai_lenper==30))
		{
			$ld_final = $this->io_fecha->uf_last_day(substr($ldt_fecha,3,2),substr($ldt_fecha,6,4));
		}
		else
		{
			$li_dias=($ai_lenper-1);
			$ld_final = $this->io_sno->uf_suma_fechas($ldt_fecha,$li_dias);
		}
		if($ai_lenper==365)
		{
			$ld_final="31/12/".substr($ldt_fecha,6,4);
		}
		return $ld_final;
	}// end function uf_final_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_subnomina_default($as_codnom)
	{
		////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_insert_subnomina_default
		//	Arguments:    $as_codnom  // codigo de la nomina
		//	Returns:      $lb_valido  //  true si hizo el insert correctamente  o false en otro caso
		//	Description:  Funcion que se usa para  insertar la subnomina por default en la nomina 
		////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_subnomina(codemp,codnom,codsubnom,dessubnom) ". 
				" VALUES ('".$this->ls_codemp."','".$as_codnom."','0000000000','Sin Subnomina') ";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_subnomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_subnomina_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_cargo_default($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_cargo_default
		//		   Access: private
		//	    Arguments: as_codnom  // código de nómina
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el cargo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_cargo(codemp,codnom,codcar,descar)VALUES".
				"('".$this->ls_codemp."','".$as_codnom."','0000000000','Sin Cargo')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_cargo_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_cargo_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_tabulador_default($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_tabulador_default
		//		   Access: private
		//	    Arguments: as_codnom  // código de nómina
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta el tabulador
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_tabulador(codemp,codnom,codtab,destab,maxpasgra,tabmed)VALUES".
				"('".$this->ls_codemp."','".$as_codnom."','00000000000000000000','Sin Tabulador',0,0)";
				
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_tabulador_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		return $lb_valido;
	}// end function uf_insert_tabulador_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_grado_default($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_grado_default
		//		   Access: private
		//	    Arguments: as_codnom  // código de nómina
		//	      Returns: lb_valido True si se ejecuto el guardar ó False si hubo error en el guardar
		//	  Description: Funcion que inserta en la tabla de grado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO sno_grado (codemp,codnom,codtab,codgra,codpas,monsalgra,moncomgra,aniodes,aniohas)VALUES".
				"('".$this->ls_codemp."','".$as_codnom."','00000000000000000000','00','00',0.00,0.00,0,0)";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_grado_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido;
	}// end function uf_insert_grado_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_asignacioncargo_default($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_asignacioncargo_default
		//		   Access: private
		//	    Arguments: as_codnom  // código de nómina
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta la asignación de cargo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/03/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	
		$ls_sql="INSERT INTO sno_asignacioncargo (codemp,codnom,codasicar,denasicar,minorguniadm,ofiuniadm,uniuniadm,depuniadm,prouniadm,".
				"								  claasicar,codtab,codpas,codgra,codded,codtipper,numvacasicar,numocuasicar,codestpro1,".
				"								  codestpro2,codestpro3,codestpro4,codestpro5,estcla) ".
				" VALUES ('".$this->ls_codemp."','".$as_codnom."','0000000000','Sin Asignación de Cargo','0000','00','00','00','00','0',".
				"'00000000000000000000','00','00','000','0000',0,0,'0000000000000000000000000','0000000000000000000000000','0000000000000000000000000',".
				"'0000000000000000000000000','0000000000000000000000000','-')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Asignación Cargo MÉTODO->uf_insert_asignacioncargo_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		return $lb_valido;
	}// end function uf_insert_asignacioncargo_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_dedicacion_default()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_dedicacion_default
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_dedicacion
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->io_dedicacion->uf_select_dedicacion("000")===false)
		{
			$ls_sql="INSERT INTO sno_dedicacion(codemp,codded,desded) VALUES('".$this->ls_codemp."','000','Sin dedicación')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_dedicacion_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_dedicacion_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_tipopersonal_default()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_tipopersonal_default
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_tipopersonal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->io_tipopersonal->uf_select_tipopersonal("000","0000")===false)
		{
			$ls_sql="INSERT INTO sno_tipopersonal(codemp,codded,codtipper,destipper)VALUES".
					"('".$this->ls_codemp."','000','0000','Sin Tipo de Personal')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_tipopersonal_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_tipopersonal_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_escaladocente_default()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_escaladocente_default
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_dedicacion
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->io_escaladocente->uf_select_escaladocente("0000")===false)
		{
			$ls_sql="INSERT INTO sno_escaladocente(codemp,codescdoc,desescdoc,tipescdoc)VALUES('".$this->ls_codemp."','0000','Sin Escala Docente', '0')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_escaladocente_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_escaladocente_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_clasificaciondocente_default()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_clasificaciondocente_default
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_tipopersonal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->io_clasificaciondocente->uf_select_clasifidocente("0000","0000")===false)
		{
			$ls_sql="INSERT INTO sno_clasificaciondocente(codemp,codescdoc,codcladoc,descladoc,tiesercladoc,suesupcladoc,suedircladoc,suedoccladoc)".
					"VALUES('".$this->ls_codemp."','0000','0000','Sin clasificación Docente','-',0,0,0)";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_clasificaciondocente_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_clasificaciondocente_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_ubicacionfisica_default()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_ubicacionfisica_default
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_dedicacion
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->io_ubicacion->uf_select_ubicacionfisica("0000")===false)
		{
			$ls_sql="INSERT INTO sno_ubicacionfisica(codemp,codubifis,desubifis)VALUES('".$this->ls_codemp."','0000','Sin Ubicacion')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_ubicacionfisica_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_ubicacionfisica_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_clasificacionobreros_default()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_clasificacionobreros_default
		//		   Access: private
		//	      Returns: lb_valido True si se ejecuto el insert ó False si hubo error en el insert
		//	  Description: Funcion que inserta en la tabla sno_dedicacion
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->io_obreros->uf_select_clasificacionobrero("0000")===false)
		{
			$ls_sql="INSERT INTO sno_clasificacionobrero(codemp,grado,suemin,suemax,tipcla,obscla,anovig) VALUES('".$this->ls_codemp."','0000',0.00,0.00,'01','','1900')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_insert_clasificacionobreros_default ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
		}
		return $lb_valido;
	}// end function uf_insert_clasificacionobreros_default
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_nomina($as_codnom,$as_consulnom,$as_descomnom,$as_codpronom,$as_codbennom,$as_conaponom,$as_cueconnom,
							  $as_notdebnom,$as_numvounom,$as_recdocnom,$as_tipdocnom,$as_recdocapo,$as_tipdocapo,$as_conpernom,
							  $as_titrepnom,$as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,$as_cueconfid,$as_divcon,
							  $as_informa,$as_ctmetnom,$as_codorgcestic,$ai_genrecdocpagperche,$as_tipdocpagperche,
							  $ai_estctaalt,$as_racobrnom,$as_tiksuel,$as_codfuefin,$as_cersindis,$as_estagrapo,$as_recdoccaunom,
							  $as_tipdoccaunom,$as_codclanom,$as_codclaapo,$as_codclafid,$as_codclaper,$as_codclacau,$ai_estctaaltapo,$as_pasape,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_update_nomina
		//	Arguments:    $ar_datos  // arreglos con los datos de la nomina 
		//	Returns:	  $lb_valido True si hizo el update correctamente o False en caso contrario
		//	Description:  Funcion actualiza los datos de la nomina 
		/////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "UPDATE sno_nomina  ".
		         "   SET consulnom='".$as_consulnom."', ".
				 "       descomnom='".$as_descomnom."', ".
				 "       codpronom='".$as_codpronom."', ".
				 "       codbennom='".$as_codbennom."', ".
				 "       conaponom='".$as_conaponom."', ".
				 "       cueconnom='".$as_cueconnom."', ".
				 "       notdebnom='".$as_notdebnom."', ".
				 "       recdocnom='".$as_recdocnom."', ".
				 "       tipdocnom='".$as_tipdocnom."', ".
				 "       recdocapo='".$as_recdocapo."', ".
				 "       tipdocapo='".$as_tipdocapo."', ".
				 "		 conpernom='".$as_conpernom."', ".
				 "		 titrepnom='".$as_titrepnom."', ".
				 "       confidnom='".$as_confidnom."', ".
				 "		 recdocfid='".$as_recdocfid."', ".
				 "       tipdocfid='".$as_tipdocfid."', ".
				 "		 codbenfid='".$as_codbenfid."', ".
				 "		 cueconfid='".$as_cueconfid."', ".
				 "		 informa='".$as_informa."', ".
				 "		 ctmetnom='".$as_ctmetnom."', ".
				 "		 codorgcestic='".$as_codorgcestic."', ".
				 "       recdocpagperche='".$ai_genrecdocpagperche."', ".
				 "       tipdocpagperche='".$as_tipdocpagperche."', ".
				 "       estctaalt= '".$ai_estctaalt."', ".
				 "       estctaaltapo= '".$ai_estctaaltapo."', ".
				 "		 cestiksuel= '".$as_tiksuel."', ".
				 "		 codfuefin= '".$as_codfuefin."', ".
				 "		 cersindis= '".$as_cersindis."', ".	
				 "		 estagrapo= '".$as_estagrapo."', ".
				 "		 recdoccaunom= '".$as_recdoccaunom."', ".
				 "		 tipdoccaunom= '".$as_tipdoccaunom."', ".
				 "		 codclanom= '".$as_codclanom."', ".	
				 "		 codclaapo= '".$as_codclaapo."', ".	
				 "		 codclafid= '".$as_codclafid."', ".	
				 "		 codclaper= '".$as_codclaper."', ".	
				 "		 codclacau= '".$as_codclacau."', ".	
                                 "               pasape = ".$as_pasape." ".
				 " WHERE codemp='".$this->ls_codemp."' ".
				 "   AND codnom='".$as_codnom."' ";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_sql->rollback();
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_update_nomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			////////////////////////////////         SEGURIDAD               //////////////////////////////
			$ls_evento="UPDATE";
			$ls_descripcion ="Actualizó la Nómina ".$as_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
			if($lb_valido)
			{	
				$this->io_mensajes->message("La Nómina fue Actualizada.");
				$this->io_sql->commit();
			}
			else
			{
				$lb_valido=false;
        		$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_update_nomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
		}
		return $lb_valido;
	
	}// end function uf_update_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	
	function uf_guardar_nomina($as_existe,$as_codnom,$as_desnom,$as_tippernom,$as_despernom,$as_anocurnom,$ad_fecininom,$as_peractnom,
							  $as_tipnom,$as_subnom,$as_racnom,$as_adenom,$as_espnom,$as_ctnom,$as_ctmetnom,$as_consulnom,
							  $as_descomnom,$as_codpronom,$as_codbennom,$as_conaponom,$as_cueconnom,$as_notdebnom,$as_numvounom,
							  $as_recdocnom,$as_tipdocnom,$as_recdocapo,$as_tipdocapo,$as_conpernom,$as_conpronom,$as_titrepnom,
							  $as_codorgcestic,$as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,$as_cueconfid,$as_divcon,
							  $as_informa,$ai_genrecdocpagperche,$as_tipdocpagperche,$ai_estctaalt,$as_racobrnom,$as_nomliq,
							  $as_hojtienom,$as_tiksuel,$as_codfuefin,$as_cersindis,$as_estagrapo,$as_recdoccaunom,$as_tipdoccaunom,
							  $as_codclanom,$as_codclaapo,$as_codclafid,$as_codclaper,$as_codclacau,$ai_estctaaltapo,$as_pasape,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_guardar_nomina
		//	Arguments:    $ar_datos //  arreglos con los datos de la nomina 
		//                $ab_vali_inser //  variable que me dice si es un nuevo registro
		//	Returns:	  $lb_valido True si hizo correctamente
		//	Description:  Funcion que guarda los datos de la nomina 
		//////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$ad_fecininom=$this->io_funciones->uf_convertirdatetobd($ad_fecininom);
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_nomina($as_codnom)===false)
				{
					$lb_valido=$this->uf_insert_nomina($as_codnom,$as_desnom,$as_tippernom,$as_despernom,$as_anocurnom,
													   $ad_fecininom,$as_peractnom,$as_tipnom,$as_subnom,$as_racnom,
							  						   $as_adenom,$as_espnom,$as_ctnom,$as_ctmetnom,$as_consulnom,
							  						   $as_descomnom,$as_codpronom,$as_codbennom,$as_conaponom,$as_cueconnom,
													   $as_notdebnom,$as_numvounom,$as_recdocnom,$as_tipdocnom,$as_recdocapo,
							  						   $as_tipdocapo,$as_conpernom,$as_conpronom,$as_titrepnom,$as_codorgcestic,
													   $as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,
													   $as_cueconfid,$as_divcon,$as_informa,$ai_genrecdocpagperche,
													   $as_tipdocpagperche,$ai_estctaalt,$as_racobrnom,$as_nomliq,$as_hojtienom,$as_tiksuel,
													   $as_codfuefin,$as_cersindis,$as_estagrapo,$as_recdoccaunom,$as_tipdoccaunom,
													   $as_codclanom,$as_codclaapo,$as_codclafid,$as_codclaper,$as_codclacau,$ai_estctaaltapo,
													   $as_pasape,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Nómina ya existe, no la puede incluir.");
				}
				break;

			case "TRUE":
				if(($this->uf_select_nomina($as_codnom)))
				{
					$lb_valido=$this->uf_update_nomina($as_codnom,$as_consulnom,$as_descomnom,$as_codpronom,$as_codbennom,
													   $as_conaponom,$as_cueconnom,$as_notdebnom,$as_numvounom,$as_recdocnom,
							  						   $as_tipdocnom,$as_recdocapo,$as_tipdocapo,$as_conpernom,$as_titrepnom,
													   $as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,$as_cueconfid,
													   $as_divcon,$as_informa,$as_ctmetnom,$as_codorgcestic,
													   $ai_genrecdocpagperche,$as_tipdocpagperche,$ai_estctaalt,$as_racobrnom,$as_tiksuel,
													   $as_codfuefin,$as_cersindis,$as_estagrapo,$as_recdoccaunom,$as_tipdoccaunom,
													   $as_codclanom,$as_codclaapo,$as_codclafid,$as_codclaper,$as_codclacau,$ai_estctaaltapo,
													   $as_pasape,$aa_seguridad);
				}
				else
				{
					$this->io_mensajes->message("La Nómina no existe, no la puede actualizar.");
				}
				break;
		}
		return $lb_valido;
	}// end function uf_guardar_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function load_nomina($ai_conta_global,$as_existe,$as_codnom,$as_desnom,$as_tippernom,$as_despernom,$as_anocurnom,$ad_fecininom,$as_peractnom,
						 $as_tipnom,$as_subnom,$as_racnom,$as_adenom,$as_espnom,$as_ctnom,$as_ctmetnom,$as_consulnom,
						 $as_descomnom,$as_codpronom,$as_codbennom,$as_conaponom,$as_cueconnom,$as_notdebnom,$as_numvounom,
						 $as_recdocnom,$as_tipdocnom,$as_recdocapo,$as_tipdocapo,$ai_total,$as_conpernom,$as_conpronom,
						 $as_titrepnom,$as_codorgcestic,$as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,$as_cueconfid,$as_divcon,
						 $as_informa,$ai_genrecdocpagperche,$as_tipdocpagperche,$ai_estctaalt,$as_racobrnom,$as_nomliq,$as_hojtienom,$as_cestiksuel,
						 $as_codfuefin,$as_denfuefin,$as_cersindis,$as_estagrapo,$as_recdoccaunom,$as_tipdoccaunom)
	{
		/////////////////////////////////////////////////////////////////////////////////
		//	Function:   select_nomina_completa
		//	Arguments:  $as_codigo// codigo de la nomina
		//              $as_denominacion // denominacion de la nomina
		//              $ai_cmbtipoperiodo // codigo tipo de período
		//              $as_despernom //  descripción del período
		//              $as_anocurnom  //  año en curso
		//              $adt_fecininom //  fecha de inicio de la nómina
		//              $as_peractnom  //  período actual de la nómina
		//              $ai_cmbtipnom // tipo de nómina
		//              $as_cmbmet //  método del cesta ticket
		//              $as_diabonvacnom //  días de bono de salida de vacaciones
		//              $as_diainivacnom //  días iniciales de vacaciones
		//              $as_diareivacnom  //  días de bono reingreso de vacaciones
		//              $as_diatopvacnom  // días de tope de vacaciones
		//              $as_diaincvacnom // dias de incremento por año de vacaciones
		//              $as_subnom  //  sub nómina 
		//              $as_adenom //  utiliza el adelanto de pago
		//              $as_racnom //  registro de asignación de cargos
		//              $as_espnom //  nómina especial
		//              $as_ctnom //   nómina de cesta ticket
		//              $as_cueconnom  //  cuenta contable
		//              $as_provee //   código del proveedor
		//              $as_benef //   código del beneficiario
		//              $as_numvounom  //  voucher nota de débito
		//              $as_notdebnom //  nota de débito
		//              $as_cmbconaponom  //  método de contabilizar los aportes de la nómina
		//              $as_conpernom  //  Validar la Contabilización del período anterior
		//				$as_confidnom  // Contabilización del Fideicomiso
		//				$as_recdocfid  // Generar Rececpión de Documento 
		//				$as_tipdocfid  // Tipo de Documento del Fideicomiso
		//				$as_codbenfid  // Código del Beneficiario
		//				$as_cueconfid  // Cuenta Contable para el fiedicomiso
		//	Returns:	True si hizo el select correctamente o False en caso contrario
		//	Description:  Funcion que me devuelve todos los datos de  la nomina
		//////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codemp, codnom, desnom, tippernom, despernom, anocurnom, fecininom, peractnom, numpernom, tipnom, ".
			    "		subnom, racnom, adenom, espnom, ctnom, ctmetnom, diabonvacnom, diareivacnom, diainivacnom, diatopvacnom, ".
			    "		diaincvacnom, consulnom, descomnom, codpronom, codbennom, conaponom, cueconnom, notdebnom, numvounom, ".
			    "		perresnom, recdocnom, tipdocnom, recdocapo, tipdocapo, conpernom, conpronom, titrepnom, codorgcestic, ".
				"       confidnom, recdocfid, tipdocfid, codbenfid, cueconfid, divcon, informa, recdoccaunom, tipdoccaunom, ".
				"       recdocpagperche, tipdocpagperche, estctaalt, racobrnom, nomliq, hojtienom,cestiksuel,codfuefin,cersindis,estagrapo, ".
				"       codclanom, codclaapo, codclafid, codclaper, codclacau, estctaaltapo, pasape,". 
				"		 (SELECT denfuefin FROM sigesp_fuentefinanciamiento ".
                " 		   WHERE codemp=sno_nomina.codemp ".
				"   		 AND codfuefin=sno_nomina.codfuefin) AS denfuefin, ".
				"		 (SELECT count(codperi) FROM sno_periodo ".
                " 		   WHERE codemp='".$this->ls_codemp."' ".
				"   		 AND codnom='".$as_codnom."' ".
				"   		 AND cerper=1) AS total ".
			    "  FROM sno_nomina  ".
			    " WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ";
	   $rs_data=$this->io_sql->select($ls_sql);
	   if($rs_data===false)
	   {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->load_nomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	   }
	   else
	   {
		   if(!$rs_data->EOF)
		   {
				$as_existe="TRUE";
				$as_codnom=$rs_data->fields["codnom"];
				$as_desnom=$rs_data->fields["desnom"];
				$as_tippernom=$rs_data->fields["tippernom"];
				$as_despernom=$rs_data->fields["despernom"];
				$as_anocurnom=$rs_data->fields["anocurnom"];
				$ad_fecininom=$rs_data->fields["fecininom"];
				$ad_fecininom=$this->io_funciones->uf_convertirfecmostrar($ad_fecininom);
				$as_peractnom=$rs_data->fields["peractnom"];
				$as_tipnom=$rs_data->fields["tipnom"];
				$as_subnom=$rs_data->fields["subnom"];
				$as_racnom=$rs_data->fields["racnom"];
				$as_racobrnom=$rs_data->fields["racobrnom"];
				$as_adenom=$rs_data->fields["adenom"];
				$as_espnom=$rs_data->fields["espnom"];
				$as_ctnom=$rs_data->fields["ctnom"];
				$as_ctmetnom=$rs_data->fields["ctmetnom"];
				$as_conpernom=$rs_data->fields["conpernom"];
				$ai_total=$rs_data->fields["total"];
				$as_conpronom=$rs_data->fields["conpronom"];
				$as_titrepnom=$rs_data->fields["titrepnom"];
				$as_codorgcestic=$rs_data->fields["codorgcestic"];
				$as_divcon=$rs_data->fields["divcon"];
				$as_informa=$rs_data->fields["informa"];
				$as_nomliq=$rs_data->fields["nomliq"];
				$as_hojtienom=$rs_data->fields["hojtienom"];
				$as_cestiksuel=$rs_data->fields["cestiksuel"];
				$as_codfuefin=$rs_data->fields["codfuefin"];
				$as_denfuefin=$rs_data->fields["denfuefin"];
				$as_cersindis=$rs_data->fields["cersindis"];
				$as_estagrapo=$rs_data->fields["estagrapo"];
				$as_codclanom=$rs_data->fields["codclanom"];
				$as_codclaapo=$rs_data->fields["codclaapo"];
				$as_codclafid=$rs_data->fields["codclafid"];
				$as_codclaper=$rs_data->fields["codclaper"];
				$as_codclacau=$rs_data->fields["codclacau"];
                                $as_pasape=$rs_data->fields["pasape"];
				if($ai_conta_global!="0")
				{
					$ai_estctaalt=trim($rs_data->fields["estctaalt"]);
					$ai_estctaaltapo=trim($rs_data->fields["estctaaltapo"]);
					$ai_genrecdocpagperche=trim($rs_data->fields["recdocpagperche"]);
					$as_tipdocpagperche=trim($rs_data->fields["tipdocpagperche"]);				
					$as_consulnom=trim($rs_data->fields["consulnom"]);
					$as_descomnom=$rs_data->fields["descomnom"];
					$as_codpronom=$rs_data->fields["codpronom"];
					$as_codbennom=$rs_data->fields["codbennom"];
					$as_conaponom=trim($rs_data->fields["conaponom"]);
					$as_cueconnom=$rs_data->fields["cueconnom"];
					$as_notdebnom=$rs_data->fields["notdebnom"];
					$as_recdocnom=$rs_data->fields["recdocnom"];
					$as_tipdocnom=$rs_data->fields["tipdocnom"];
					$as_recdocapo=$rs_data->fields["recdocapo"];
					$as_tipdocapo=$rs_data->fields["tipdocapo"];
					$as_confidnom=$rs_data->fields["confidnom"];
					$as_recdocfid=$rs_data->fields["recdocfid"];
					$as_tipdocfid=$rs_data->fields["tipdocfid"];
					$as_codbenfid=$rs_data->fields["codbenfid"];
					$as_cueconfid=$rs_data->fields["cueconfid"];
					$as_recdoccaunom=$rs_data->fields["recdoccaunom"];
					$as_tipdoccaunom=$rs_data->fields["tipdoccaunom"];
				}
		   }
	   }
		$arrResultado['as_existe']=$as_existe;
		$arrResultado['as_codnom']=$as_codnom;
		$arrResultado['as_desnom']=$as_desnom;
		$arrResultado['as_tippernom']=$as_tippernom;
		$arrResultado['as_despernom']=$as_despernom;
		$arrResultado['as_anocurnom']=$as_anocurnom;
		$arrResultado['ad_fecininom']=$ad_fecininom;
		$arrResultado['as_peractnom']=$as_peractnom;
		$arrResultado['as_tipnom']=$as_tipnom;
		$arrResultado['as_subnom']=$as_subnom;
		$arrResultado['as_racnom']=$as_racnom;
		$arrResultado['as_adenom']=$as_adenom;
		$arrResultado['as_espnom']=$as_espnom;
		$arrResultado['as_ctnom']=$as_ctnom;
		$arrResultado['as_ctmetnom']=$as_ctmetnom;
		$arrResultado['as_consulnom']=$as_consulnom;
		$arrResultado['as_descomnom']=$as_descomnom;
		$arrResultado['as_codpronom']=$as_codpronom;
		$arrResultado['as_codbennom']=$as_codbennom;
		$arrResultado['as_conaponom']=$as_conaponom;
		$arrResultado['as_cueconnom']=$as_cueconnom;
		$arrResultado['as_notdebnom']=$as_notdebnom;
		$arrResultado['as_numvounom']=$as_numvounom;
		$arrResultado['as_recdocnom']=$as_recdocnom;
		$arrResultado['as_tipdocnom']=$as_tipdocnom;
		$arrResultado['as_recdocapo']=$as_recdocapo;
		$arrResultado['as_tipdocapo']=$as_tipdocapo;
		$arrResultado['ai_total']=$ai_total;
		$arrResultado['as_conpernom']=$as_conpernom;
		$arrResultado['as_conpronom']=$as_conpronom;
		$arrResultado['as_titrepnom']=$as_titrepnom;
		$arrResultado['as_codorgcestic']=$as_codorgcestic;
		$arrResultado['as_confidnom']=$as_confidnom;
		$arrResultado['as_recdocfid']=$as_recdocfid;
		$arrResultado['as_tipdocfid']=$as_tipdocfid;
		$arrResultado['as_codbenfid']=$as_codbenfid;
		$arrResultado['as_cueconfid']=$as_cueconfid;
		$arrResultado['as_divcon']=$as_divcon;
		$arrResultado['as_informa']=$as_informa;
		$arrResultado['ai_genrecdocpagperche']=$ai_genrecdocpagperche;
		$arrResultado['as_tipdocpagperche']=$as_tipdocpagperche;
		$arrResultado['ai_estctaalt']=$ai_estctaalt;
		$arrResultado['ai_estctaaltapo']=$ai_estctaaltapo;
		$arrResultado['as_racobrnom']=$as_racobrnom;
		$arrResultado['as_nomliq']=$as_nomliq;
		$arrResultado['as_hojtienom']=$as_hojtienom;
		$arrResultado['as_cestiksuel']=$as_cestiksuel;
		$arrResultado['as_codfuefin']=$as_codfuefin;
		$arrResultado['as_denfuefin']=$as_denfuefin;
		$arrResultado['as_cersindis']=$as_cersindis;
		$arrResultado['as_estagrapo']=$as_estagrapo;
		$arrResultado['as_recdoccaunom']=$as_recdoccaunom;
		$arrResultado['as_tipdoccaunom']=$as_tipdoccaunom;
		$arrResultado['as_codclanom']=$as_codclanom;
		$arrResultado['as_codclaapo']=$as_codclaapo;
		$arrResultado['as_codclafid']=$as_codclafid;
		$arrResultado['as_codclaper']=$as_codclaper;
		$arrResultado['as_codclacau']=$as_codclacau;
                $arrResultado['as_pasape']=$as_pasape;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function load_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------	 

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_delete_nomina($as_codnom,$aa_seguridad)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_delete_nomina
		//	Arguments:    $as_codnom  // codigo de la nomina 
		//	Returns:	  $lb_valido True si se hizo correctamente el delete de la nomina o False en caso contrario
		//	Description:  Funcion que elimina una nomina especifica de la tabla de sno_nomina  segun ciertas condiciones 
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($this->uf_select_hnomina($as_codnom))
		{
			$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
			return false;
		} 
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_personalnomina",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>0))
			{
				$this->io_mensajes->message(" Esta nomina tiene personal asignado. No se puede Eliminar ");
				return false;
			}
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_subnomina",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>1))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_cargo",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>1))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_tabulador",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>1))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_grado",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>1))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_asignacioncargo",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>1))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_concepto",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>0))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_constante",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			if(($lb_valido===false)||($li_total>0))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$li_total=0;
			$arrResultado=$this->uf_select_nomina_en_tabla($as_codnom,"sno_tipoprestamo",$li_total);
			$li_total=$arrResultado['ai_total'];
			$lb_valido=$arrResultado['lb_valido'];
			
			if(($lb_valido===false)||($li_total>0))
			{
				$this->io_mensajes->message(" Esta nomina ya tiene movimientos. No se puede Eliminar ");
				return false;
			} 
		}
		if($lb_valido)
		{
			$this->io_sql->begin_transaction();
		  	$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_periodo"); 
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_subnomina");
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_asignacioncargo"); 
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_grado");									  
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_tabulador");									  
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_cargo");									  
			}
			if($lb_valido)
			{
				$lb_valido=$this->uf_delete_nomina_en_tabla($as_codnom,"sno_nomina");
			}
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó la Nómina ".$as_codnom;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				if($lb_valido)
				{	
					$this->io_mensajes->message("La Nómina fue Eliminada.");
					$this->io_sql->commit();
				}
				else
				{
					$lb_valido=false;
        			$this->io_mensajes->message("CLASE->nómina MÉTODO->uf_delete_nomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
			}
		}
		return $lb_valido;  
	}// end function uf_delete_nomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_hnomina($as_codnom)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_select_hnomina
		//	Arguments:    $as_codnom  //  codigo de la nomina 
		//                $li_veces  //  cuantos encontro (referencia)
		//	Returns:	  $lb_valido true si se realizo el select correctamente, false en caso contrario
		//	Description:  Funcion que cuenta los codigo de la nomina en la tabla historico de salida 
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codnom ".
				"  FROM sno_hnomina ".
				" WHERE codnom='".$as_codnom."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_existe=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_select_hnomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_existe=true;
			}
			else
			{
				$lb_existe=false;
			}
		}   
		return $lb_existe; 
	 }// end function uf_select_hnomina
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------  
 	function uf_select_nomina_en_tabla($as_codnom,$as_tabla,$ai_total)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:    uf_select_nomina_en_tabla
		//	Arguments:   $as_codnom  codigo de la nomina 
		//               $li_veces  variable integer 
		//               $as_tabla  tabla a buscar la nomina para ver si existe ese codigo en la tabla 
		//	Returns:	 $b_valido -> True si el select se realizo correctamente o false en caso contrario
		//	Description: cuenta los codigo de la nomina en la tabla personal nomina
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_total=0;
		$ls_sql="SELECT count(codnom) as totnom ".
			    "  FROM ".$as_tabla."  ".
			    " WHERE codemp='".$this->ls_codemp."' ".
			    "   AND codnom='".$as_codnom."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_select_nomina_en_tabla ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
		   if($row=$this->io_sql->fetch_row($rs_data))
		   {
				$ai_total=$row["totnom"];
		   }
		   else
		   {
				$ai_total=0;
		   }
		}   
		$arrResultado['ai_total']=$ai_total;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	 }// end function uf_select_nomina_en_tabla
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------  
 	function uf_delete_nomina_en_tabla($as_codnom,$as_tabla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:    uf_delete_nomina_en_tabla
		//	Arguments:   $as_codnom  codigo de la nomina 
		//               $li_veces  variable integer 
		//               $as_tabla  tabla a buscar la nomina para ver si existe ese codigo en la tabla 
		//	Returns:	 $b_valido -> True si el select se realizo correctamente o false en caso contrario
		//	Description: cuenta los codigo de la nomina en la tabla personal nomina
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE FROM ".$as_tabla."  ".
			   " WHERE codemp='".$this->ls_codemp."' ".
			   "   AND codnom='".$as_codnom."' ";
		$li_numrow=$this->io_sql->select($ls_sql);
		if ($li_numrow===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_delete_nomina_en_tabla ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		return $lb_valido; 
	 }// end function uf_select_nomina_en_tabla
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_periodo($as_codnom,$as_codperi,$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	      Function:    uf_update_periodo
		//	     Arguments:    $as_codperi_est // codigo del periodo establecer
		//                     $as_codnom   // codigo de la nomina            
		//	       Returns:	   $lb_valido true si es correcto la funcion o false en caso contrario
		//	   Description:    Función que se encarga de actualizar el periodo actual de la nomina al momento de establecerlo 
	    //     Creado por :    Ing. Yozelin Barragán
	    // Fecha Creación :    01/03/2006          Fecha última Modificacion : 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="UPDATE sno_nomina ".
				"   SET peractnom='".$as_codperi."' ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ";
		$li_numrow=$this->io_sql->execute($ls_sql);
		if($li_numrow===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->NOMINA MÉTODO->uf_update_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			////////////////////////////////         SEGURIDAD               //////////////////////////////
			$ls_evento="UPDATE";
			$ls_descripcion ="Establecio el Periodo ".$as_codperi." a la Nómina ".$as_codnom;
			$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
											$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			/////////////////////////////////         SEGURIDAD               /////////////////////////////	
		}
		return $lb_valido;
   }// end function uf_update_periodo		
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_periodo($as_codnom,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_periodo
		//		   Access: public (sigesp_snorh_d_tablavacacion)
		//	    Arguments: as_codnom  //  código de la nómina
		//	    		   ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//	      Returns: lb_valido True si se ejecuto el buscar ó False si hubo error en el buscar
		//	  Description: Funcion que obtiene todos los períodos de una tabla de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	    $ls_sql="SELECT codperi, fecdesper, fechasper, cerper, conper, apoconper, totper, ingconper, fidconper, fidintconper ".
	            "  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ".
				"   AND codperi <> '000' ".
				" ORDER BY codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_load_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codperi=$row["codperi"];  
				$ls_fecdesper=$this->io_funciones->uf_convertirfecmostrar($row["fecdesper"]);
				$ls_fechasper=$this->io_funciones->uf_convertirfecmostrar($row["fechasper"]);
				$li_cerper=$row["cerper"];
				$li_conper=$row["conper"];
				$li_apoconper=$row["apoconper"];
				$li_ingconper=$row["ingconper"];
				$li_fidconper=$row["fidconper"];
				$li_fidintconper=$row["fidintconper"];
				$ld_totper=number_format($row["totper"],2,",",".");
				$ls_cerper="";
			    $ls_conper="";
			    $ls_apoconper="";
				$ls_ingconper="";
				$ls_fidconper="";
				$ls_fidintconper="";
				if($li_cerper==1)
				{
				  $ls_cerper="checked";
				}
				if($li_conper==1)
				{
				  $ls_conper="checked";
				}
				if($li_apoconper==1)
				{
				  $ls_apoconper="checked";
				}
				if($li_ingconper==1)
				{
				  $ls_ingconper="checked";
				}
				if($li_fidconper==1)
				{
				  $ls_fidconper="checked";
				}
				if($li_fidintconper==1)
				{
				  $ls_fidintconper="checked";
				}
				$ao_object[$ai_totrows][1]="<input type=text name=txtcodperi".$ai_totrows." value='".$ls_codperi."' class=sin-borde style=text-align:center size=4 readonly>";
				$ao_object[$ai_totrows][2]="<input type=text name=txtfecdesper".$ai_totrows." value='".$ls_fecdesper."' size=13 class=sin-borde style=text-align:center readonly >";
				$ao_object[$ai_totrows][3]="<input type=text name=txtfechasper".$ai_totrows." class=sin-borde value='".$ls_fechasper."' size=13  style=text-align:center readonly>";
				$ao_object[$ai_totrows][4]="<div align='center'><input name=chkcerper".$ai_totrows." type=checkbox id=chkcerper".$ai_totrows."value=1 class=sin-borde style=text-align:center ".$ls_cerper." disabled></div>";
				$ao_object[$ai_totrows][5]="<div align='center'><input name=chkconper".$ai_totrows." type=checkbox id=chkconper".$ai_totrows." value=1 class=sin-borde style=text-align:center ".$ls_conper." disabled></div>";
				$ao_object[$ai_totrows][6]="<div align='center'><input name=chkapoconper".$ai_totrows." type=checkbox id=chkapoconper".$ai_totrows." value=1 class=sin-borde size=10 style=text-align:center ".$ls_apoconper." disabled></div>";
				$ao_object[$ai_totrows][7]="<div align='center'><input name=chkingconper".$ai_totrows." type=checkbox id=chkingconper".$ai_totrows." value=1 class=sin-borde size=10 style=text-align:center ".$ls_ingconper." disabled></div>";
				$ao_object[$ai_totrows][8]="<div align='center'><input name=chkfidconper".$ai_totrows." type=checkbox id=chkfidconper".$ai_totrows." value=1 class=sin-borde size=10 style=text-align:center ".$ls_fidconper." disabled></div>";
				$ao_object[$ai_totrows][9]="<div align='center'><input name=chkfidintconper".$ai_totrows." type=checkbox id=chkfidintconper".$ai_totrows." value=1 class=sin-borde size=10 style=text-align:center ".$ls_fidintconper." disabled></div>";
				$ao_object[$ai_totrows][10]="<input type=text name=txttotper".$ai_totrows." value='".$ld_totper."' class=sin-borde size=12 style=text-align:right readonly>";
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_periodo_establecer($as_codnom,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_periodo_establecer
		//		   Access: public (sigesp_snorh_d_establecer_periodo)
		//	    Arguments: as_codnom  //  código de la nómina
		//	    		   ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//	      Returns: lb_valido True si se ejecuto el buscar ó False si hubo error en el buscar
		//	  Description: Funcion que obtiene todos los períodos de una tabla de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
	    $ls_sql="SELECT codperi, fecdesper, fechasper ".
	            "  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ".
				"   AND codperi <> '000' ".
				" ORDER BY codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_load_periodo_establecer ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codperi=$row["codperi"];  
				$ls_fecdesper=$this->io_funciones->uf_convertirfecmostrar($row["fecdesper"]);
				$ls_fechasper=$this->io_funciones->uf_convertirfecmostrar($row["fechasper"]);
				$ao_object[$ai_totrows][1]="<div align='center'><input name=chkaplica".$ai_totrows." type=checkbox id=chkaplica".$ai_totrows." value='1' class=sin-borde style=text-align:center></div>";
				$ao_object[$ai_totrows][2]="<input type=text name=txtcodperi".$ai_totrows." value='".$ls_codperi."' class=sin-borde style=text-align:center size=10 readonly>";
				$ao_object[$ai_totrows][3]="<input type=text name=txtfecdesper".$ai_totrows." value='".$ls_fecdesper."' size=25 class=sin-borde style=text-align:center readonly >";
				$ao_object[$ai_totrows][4]="<input type=text name=txtfechasper".$ai_totrows." class=sin-borde value='".$ls_fechasper."' size=25  style=text-align:center readonly>";
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_periodo_establecer
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function load_nomina_historico($ai_conta_global,$as_existe,$as_codnom,$as_desnom,$as_consulnom,$as_descomnom,						
								   $as_codpronom,$as_codbennom,$as_conaponom,$as_cueconnom,$as_notdebnom,$as_numvounom,
						           $as_recdocnom,$as_tipdocnom,$as_recdocapo,$as_tipdocapo,$as_confidnom,$as_recdocfid,
								   $as_tipdocfid,$as_codbenfid,$as_cueconfid,$as_informa,$ai_genrecdocpagperche,
								   $as_tipdocpagperche,$ai_estctaalt,$as_estagrapo,$as_recdoccaunom,$as_tipdoccaunom)
	{
		/////////////////////////////////////////////////////////////////////////////////
		//	Function:   load_nomina_historico
		//	Arguments:  $as_codigo// codigo de la nomina
		//              $as_denominacion // denominacion de la nomina
		//              $ai_cmbtipoperiodo // codigo tipo de período
		//              $as_despernom //  descripción del período
		//              $as_anocurnom  //  año en curso
		//              $adt_fecininom //  fecha de inicio de la nómina
		//              $as_peractnom  //  período actual de la nómina
		//              $ai_cmbtipnom // tipo de nómina
		//              $as_cmbmet //  método del cesta ticket
		//              $as_diabonvacnom //  días de bono de salida de vacaciones
		//              $as_diainivacnom //  días iniciales de vacaciones
		//              $as_diareivacnom  //  días de bono reingreso de vacaciones
		//              $as_diatopvacnom  // días de tope de vacaciones
		//              $as_diaincvacnom // dias de incremento por año de vacaciones
		//              $as_subnom  //  sub nómina 
		//              $as_adenom //  utiliza el adelanto de pago
		//              $as_racnom //  registro de asignación de cargos
		//              $as_espnom //  nómina especial
		//              $as_ctnom //   nómina de cesta ticket
		//              $as_cueconnom  //  cuenta contable
		//              $as_provee //   código del proveedor
		//              $as_benef //   código del beneficiario
		//              $as_numvounom  //  voucher nota de débito
		//              $as_notdebnom //  nota de débito
		//              $as_cmbconaponom  //  método de contabilizar los aportes de la nómina
		//	Returns:	True si hizo el select correctamente o False en caso contrario
		//	Description:  Funcion que me devuelve todos los datos de  la nomina
		//////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codemp, codnom, desnom, tippernom, despernom, anocurnom, fecininom, peractnom, numpernom, tipnom, ".
			    "		subnom, racnom, adenom, espnom, ctnom, ctmetnom, diabonvacnom, diareivacnom, diainivacnom, diatopvacnom, ".
			    "		diaincvacnom, consulnom, descomnom, codpronom, codbennom, conaponom, cueconnom, notdebnom, numvounom, ".
			    "		perresnom, recdocnom, tipdocnom, recdocapo, tipdocapo, confidnom, recdocfid, tipdocfid, codbenfid, cueconfid, informa, ".
				"       recdocpagperche, tipdocpagperche, estctaalt,estagrapo, recdoccaunom, tipdoccaunom, estctaaltapo, ".
				"       codclanom, codclaapo, codclafid, codclaper, codclacau, ". 
				"		 (SELECT count(codperi) FROM sno_periodo ".
                " 		   WHERE codemp='".$this->ls_codemp."' ".
				"   		 AND codnom='".$as_codnom."' ".
				"   		 AND cerper=1) AS total ".
			    "  FROM sno_hnomina  ".
			    " WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ".
				"	AND anocurnom='".$_SESSION["la_nomina"]["anocurnom"]."'".
				"	AND peractnom='".$_SESSION["la_nomina"]["peractnom"]."'";
	   $rs_data=$this->io_sql->select($ls_sql);
	   if($rs_data===false)
	   {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->load_nomina_historico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	   }
	   else
	   {
		   if($row=$this->io_sql->fetch_row($rs_data))
		   {
				$as_existe="TRUE";
				$as_codnom=$row["codnom"];
				$as_desnom=$row["desnom"];
				$as_informa=$row["informa"];
				$as_codclanom=$rs_data->fields["codclanom"];
				$as_codclaapo=$rs_data->fields["codclaapo"];
				$as_codclafid=$rs_data->fields["codclafid"];
				$as_codclaper=$rs_data->fields["codclaper"];
				$as_codclacau=$rs_data->fields["codclacau"];
				if($ai_conta_global!="0")
				{
					$as_consulnom=trim($row["consulnom"]);
					$as_descomnom=$row["descomnom"];
					$as_codpronom=$row["codpronom"];
					$as_codbennom=$row["codbennom"];
					$as_conaponom=trim($row["conaponom"]);
					$as_cueconnom=$row["cueconnom"];
					$as_notdebnom=$row["notdebnom"];
					$as_recdocnom=$row["recdocnom"];
					$as_tipdocnom=$row["tipdocnom"];
					$as_recdocapo=$row["recdocapo"];
					$as_tipdocapo=$row["tipdocapo"];
					$as_confidnom=trim($row["confidnom"]);
					$as_recdocfid=$row["recdocfid"];
					$as_tipdocfid=$row["tipdocfid"];
					$as_codbenfid=$row["codbenfid"];
					$as_cueconfid=$row["cueconfid"];
					$ai_genrecdocpagperche=$row["recdocpagperche"];
					$as_tipdocpagperche=$row["tipdocpagperche"];
					$ai_estctaalt=$row["estctaalt"];
					$ai_estctaaltapo=$row["estctaaltapo"];
					$as_estagrapo=$row["estagrapo"];
					$as_recdoccaunom=$row["recdoccaunom"];
					$as_tipdoccaunom=$row["tipdoccaunom"];
				}
		   }
	   }
		$arrResultado['as_existe']=$as_existe;
		$arrResultado['as_codnom']=$as_codnom;
		$arrResultado['as_desnom']=$as_desnom;
		$arrResultado['as_consulnom']=$as_consulnom;
		$arrResultado['as_descomnom']=$as_descomnom;
		$arrResultado['as_codpronom']=$as_codpronom;
		$arrResultado['as_codbennom']=$as_codbennom;
		$arrResultado['as_conaponom']=$as_conaponom;
		$arrResultado['as_cueconnom']=$as_cueconnom;
		$arrResultado['as_notdebnom']=$as_notdebnom;
		$arrResultado['as_numvounom']=$as_numvounom;
		$arrResultado['as_recdocnom']=$as_recdocnom;
		$arrResultado['as_tipdocnom']=$as_tipdocnom;
		$arrResultado['as_recdocapo']=$as_recdocapo;
		$arrResultado['as_tipdocapo']=$as_tipdocapo;
		$arrResultado['as_confidnom']=$as_confidnom;
		$arrResultado['as_recdocfid']=$as_recdocfid;
		$arrResultado['as_tipdocfid']=$as_tipdocfid;
		$arrResultado['as_codbenfid']=$as_codbenfid;
		$arrResultado['as_cueconfid']=$as_cueconfid;
		$arrResultado['as_informa']=$as_informa;
		$arrResultado['ai_genrecdocpagperche']=$ai_genrecdocpagperche;
		$arrResultado['as_tipdocpagperche']=$as_tipdocpagperche;
		$arrResultado['ai_estctaalt']=$ai_estctaalt;
		$arrResultado['ai_estctaaltapo']=$ai_estctaaltapo;
		$arrResultado['as_estagrapo']=$as_estagrapo;
		$arrResultado['as_recdoccaunom']=$as_recdoccaunom;
		$arrResultado['as_tipdoccaunom']=$as_tipdoccaunom;
		$arrResultado['as_codclanom']=$as_codclanom;
		$arrResultado['as_codclaapo']=$as_codclaapo;
		$arrResultado['as_codclafid']=$as_codclafid;
		$arrResultado['as_codclaper']=$as_codclaper;
		$arrResultado['as_codclacau']=$as_codclacau;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function load_nomina_historico
	//-----------------------------------------------------------------------------------------------------------------------------------	 

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_nomina_historico($as_codnom,$as_consulnom,$as_descomnom,$as_codpronom,$as_codbennom,$as_conaponom,
										$as_cueconnom,$as_notdebnom,$as_numvounom,$as_recdocnom,$as_tipdocnom,$as_recdocapo,
										$as_tipdocapo,$as_confidnom,$as_recdocfid,$as_tipdocfid,$as_codbenfid,$as_cueconfid,
										$ai_genrecdocpagperche ,$as_tipdocpagperche,$ai_estctaalt,$as_estagrapo,$as_recdoccaunom,
										$as_tipdoccaunom,$as_codclanom,$as_codclaapo,$as_codclafid,$as_codclaper,$as_codclacau,$ai_estctaaltapo,
										$aa_seguridad)
	{
		///////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_update_nomina_historico
		//	Arguments:    $ar_datos  // arreglos con los datos de la nomina 
		//	Returns:	  $lb_valido True si hizo el update correctamente o False en caso contrario
		//	Description:  Funcion actualiza los datos de la nomina 
		/////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "UPDATE sno_hnomina  ".
		         "   SET consulnom='".$as_consulnom."', ".
				 "       descomnom='".$as_descomnom."', ".
				 "       codpronom='".$as_codpronom."', ".
				 "       codbennom='".$as_codbennom."', ".
				 "       conaponom='".$as_conaponom."', ".
				 "       cueconnom='".$as_cueconnom."', ".
				 "       notdebnom='".$as_notdebnom."', ".
				 "       recdocnom='".$as_recdocnom."', ".
				 "       tipdocnom='".$as_tipdocnom."', ".
				 "       recdocapo='".$as_recdocapo."', ".
				 "       tipdocapo='".$as_tipdocapo."', ".
				 "       confidnom='".$as_confidnom."', ".
				 "		 recdocfid='".$as_recdocfid."', ".
				 "       tipdocfid='".$as_tipdocfid."', ".
				 "		 codbenfid='".$as_codbenfid."', ".
				 "		 cueconfid='".$as_cueconfid."', ".
				 "       recdocpagperche='".$ai_genrecdocpagperche."', ".
				 "       tipdocpagperche='".$as_tipdocpagperche."', ".
				 "       estctaalt = '".$ai_estctaalt."', ".		
				 "       estctaaltapo = '".$ai_estctaaltapo."', ".		
				 "       estagrapo = '".$as_estagrapo."', ".	
				 "       recdoccaunom = '".$as_recdoccaunom."', ".
				 "       tipdoccaunom = '".$as_tipdoccaunom."', ".	
				 "		 codclanom= '".$as_codclanom."', ".	
				 "		 codclaapo= '".$as_codclaapo."', ".	
				 "		 codclafid= '".$as_codclafid."', ".	
				 "		 codclaper= '".$as_codclaper."', ".	
				 "		 codclacau= '".$as_codclacau."' ".	
				 " WHERE codemp='".$this->ls_codemp."' ".
				 "   AND codnom='".$as_codnom."' ".
				 "	 AND anocurnom='".$_SESSION["la_nomina"]["anocurnom"]."'".
				 "	 AND peractnom='".$_SESSION["la_nomina"]["peractnom"]."'"; 
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$lb_valido=false;
			$this->io_sql->rollback();
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_update_nomina_historico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$ls_sql= "UPDATE sno_thnomina  ".
					 "   SET consulnom='".$as_consulnom."', ".
					 "       descomnom='".$as_descomnom."', ".
					 "       codpronom='".$as_codpronom."', ".
					 "       codbennom='".$as_codbennom."', ".
					 "       conaponom='".$as_conaponom."', ".
					 "       cueconnom='".$as_cueconnom."', ".
					 "       notdebnom='".$as_notdebnom."', ".
					 "       recdocnom='".$as_recdocnom."', ".
					 "       tipdocnom='".$as_tipdocnom."', ".
					 "       recdocapo='".$as_recdocapo."', ".
					 "       confidnom='".$as_confidnom."', ".
					 "		 recdocfid='".$as_recdocfid."', ".
					 "       tipdocfid='".$as_tipdocfid."', ".
					 "		 codbenfid='".$as_codbenfid."', ".
					 "		 cueconfid='".$as_cueconfid."', ".
					 "       recdocpagperche='".$ai_genrecdocpagperche."', ".
				     "       tipdocpagperche='".$as_tipdocpagperche."', ".
					 "       estctaalt = '".$ai_estctaalt."', ".	
					 "       estctaaltapo = '".$ai_estctaaltapo."', ".	
					 "       recdoccaunom = '".$as_recdoccaunom."', ".
					 "       tipdoccaunom = '".$as_tipdoccaunom."', ".	
					 "		 codclanom= '".$as_codclanom."', ".	
					 "		 codclaapo= '".$as_codclaapo."', ".	
					 "		 codclafid= '".$as_codclafid."', ".	
					 "		 codclaper= '".$as_codclaper."', ".	
					 "		 codclacau= '".$as_codclacau."' ".	
					 " WHERE codemp='".$this->ls_codemp."' ".
					 "   AND codnom='".$as_codnom."' ".
					 "	 AND anocurnom='".$_SESSION["la_nomina"]["anocurnom"]."'".
					 "	 AND peractnom='".$_SESSION["la_nomina"]["peractnom"]."'"; 
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_sql->rollback();
				$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_update_nomina_historico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			}
			else
			{
			
				////////////////////////////////         SEGURIDAD               //////////////////////////////
				$ls_evento="UPDATE";
				$ls_descripcion ="Actualizó la Configuración de contabilización de la Nómina ".$as_codnom.", Año ".$_SESSION["la_nomina"]["anocurnom"].
								 ", Periodo ".$_SESSION["la_nomina"]["peractnom"];
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////	
				if($lb_valido)
				{	
					$this->io_mensajes->message("La Nómina fue Actualizada.");
					$this->io_sql->commit();
				}
				else
				{
					$lb_valido=false;
					$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_update_nomina_historico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$this->io_sql->rollback();
				}
			}
		}
		return $lb_valido;
	}// end function uf_update_nomina_historico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_select_periodo($as_codnom,$as_codperi)
	{    
		////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:    uf_select_nomina
		//	Arguments:   $as_codnom // codigo de la nomina 
		//				 $as_codperi // codigo del periodo
		//	Returns:	 $lb_valido // True si realizo el select correctamente o False en caso contrario
		//	Description: Funcion que selecciona los datos de la nomina segun el codigo pasado por  parametros
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=true;
		$ls_sql="SELECT codperi ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ".
				"   AND codperi='".$as_codperi."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Nomina MÉTODO->uf_select_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		}// end function uf_select_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_guardar_periodoadicional($as_existe,$as_codnom,$as_codperi,$ad_fecdesper,$ad_fechasper,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_sno_fill_periodo
		//	Arguments:    as_codnom //  código de Nómina
		//                as_codperi  //	 codigo de Periodo
		//                ad_fecdesper  //  Fecha desde
		//                ad_fechasper  //  Fecha Hasta
		//                aa_seguridad  //  arreglo de seguridad
		//	Returns:	  $lb_valido True si genero el periodo adicional
		//	Description:  Funcion que genera un periodo adicional
		/////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;		
		$this->io_sql->begin_transaction();		
		$ad_fecdesper=$this->io_funciones->uf_convertirdatetobd($ad_fecdesper);
		$ad_fechasper=$this->io_funciones->uf_convertirdatetobd($ad_fechasper);
		switch ($as_existe)
		{
			case "FALSE":
				if($this->uf_select_periodo($as_codnom,$as_codperi)===false)
				{
					$ls_sql="INSERT INTO sno_periodo(codemp,codnom,codperi,fecdesper,fechasper,totper,cerper,conper,apoconper,obsper,peradi) ".
							"  VALUES ('".$this->ls_codemp."','".$as_codnom."','".$as_codperi."','".$ad_fecdesper."','".$ad_fechasper."', ".
							" 0,0,0,0,'',1)";
					$li_row=$this->io_sql->execute($ls_sql);
					if(($li_row===false))
					{
						$lb_valido=false;
						$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_guardar_periodoadicional ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					}
					else
					{
						////////////////////////////////         SEGURIDAD               //////////////////////////////
						$ls_evento="INSERT";
						$ls_descripcion ="Insertó el Periodo Adicional ".$as_codperi.", Fecha desde ".$ad_fecdesper.", Fecha Hasta ".$ad_fechasper."  a la Nómina ".$as_codnom;
						$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
														$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
														$aa_seguridad["ventanas"],$ls_descripcion);
						/////////////////////////////////         SEGURIDAD               /////////////////////////////	
						if($lb_valido)
						{
							$lb_valido=$this->uf_update_periodo($as_codnom,$as_codperi,$aa_seguridad);
						}
						
					}
				}
				else
				{
					$this->io_mensajes->message("El Periodo ya existe, no lo puede incluir.");
				}
		}
		if($lb_valido)
		{	
			$this->io_sql->commit();
		}
		else
		{
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		
		return $lb_valido;
	}// end function uf_guardar_periodoadicional
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_nominas_reportar($ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_nominas_reportar
		//		   Access: public (sigesp_snorh_r_retencion_arc)
		//	    Arguments: ai_totrows  // total de filas del detalle
		//				   ao_object  // objetos del detalle
		//	      Returns: lb_valido True si se ejecuto el buscar ó False si hubo error en el buscar
		//	  Description: Funcion que obtiene todas las nóminas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/02/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codnom, desnom ".
				"  FROM sno_nomina ".
				" WHERE codemp='".$this->ls_codemp."'".
				" ORDER BY codnom";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Nómina MÉTODO->uf_load_nominas_reportar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codnom=$row["codnom"];
				$ls_desnom=$row["desnom"];

				$ao_object[$ai_totrows][1]="<input name=txtcodnom".$ai_totrows." type=text id=txtcodnom".$ai_totrows." class=sin-borde size=6 value='".$ls_codnom."' readonly>";
				$ao_object[$ai_totrows][2]="<input name=txtdesnom".$ai_totrows." type=text id=txtdesnom".$ai_totrows." class=sin-borde size=100 value='".$ls_desnom."' readonly>";
				$ao_object[$ai_totrows][3]="<input name=chknomsel".$ai_totrows." type=checkbox class=sin-borde id=chknomsel".$ai_totrows." value='".$ls_codnom."'>";
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_load_nominas_reportar
	//-----------------------------------------------------------------------------------------------------------------------------------	
	
//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_seleccionar_periodoadicional($as_codnom,$as_tippernom,$as_codperi,$ad_fecdesper,$ad_fechasper)
	{    
		////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:    uf_seleccionar_periodoadicional
		//	Arguments:   $as_codnom // codigo de la nomina 
		//               $ad_fecdesper // fecha inicial del nuevo periodo
		//               $ad_fechasper // fecha final del nuevo periodo
		//               $as_tippernom // tipo del periodo de la nómina 
		//               $as_codperi  // código del nuevo periodo
		//	Returns:	 $lb_valido // True si realizo el select correctamente o False en caso contrario
		//	Description: Funcion que selecciona los datos de la nomina segun el codigo pasado por  parametros
		//               y calcula la fecha de inicio y de finalizacion del periodo adicioal
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=1;//true;
		$as_codperi="";
		$ad_fecdesper="";
		$ad_fechasper="";
		
		$ls_sql="SELECT MAX(codperi) as codperi ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ";
			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Nomina MÉTODO->uf_seleccionar_periodoadicional ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=0;//false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codperi=$row["codperi"];
				
				$ls_sql="SELECT fechasper ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ".
				"   AND codperi='".$ls_codperi."' ";
								
			  	$rs_data2=$this->io_sql->select($ls_sql);
				
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Nomina MÉTODO->uf_seleccionar_periodoadicional ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
					$lb_existe=0;//false;
				}
				else
				{
					while($row=$this->io_sql->fetch_row($rs_data2))
					{
						
						$ls_codperi=intval($ls_codperi)+1;
						$as_codperi=$this->io_funciones->uf_cerosizquierda($ls_codperi,3);				
						$ld_fechasper=$this->io_funciones->uf_convertirfecmostrar($row["fechasper"]);
						$ad_fecdesper=$this->io_fecha->suma_fechas($ld_fechasper,1);				
						switch($as_tippernom)
						{
							case "0": // Semanal
								$li_periodos=52;
								break;
							case "1": // Quincenal
								$li_periodos=24;
								break;
							case "2": // Mensual
								$li_periodos=12;
								break;					
						}
						$li_per=(365/$li_periodos);
						$li_lenper=round($li_per,0);				
						$ad_fechasper=$this->uf_final_periodo($ad_fecdesper,$li_lenper);
					}
				}
			}			
		}
		$arrResultado['as_codperi']=$as_codperi;
		$arrResultado['ad_fecdesper']=$ad_fecdesper;
		$arrResultado['ad_fechasper']=$ad_fechasper;
		$arrResultado['lb_existe']=$lb_existe;
		return $arrResultado;						
	}// end function uf_seleccionar_periodoadicional
	//-----------------------------------------------------------------------------------------------------------------------------------	

//-----------------------------------------------------------------------------------------------------------------------------------		
	function uf_buscar_prox_periodo($as_codnom,$as_codperi,$ad_fecdesper,$ad_fechasper)
	{    
		////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function:    uf_buscar_prox_periodo
		//	Arguments:   $as_codnom // codigo de la nomina 
		//               $ad_fecdesper // fecha inicial del nuevo periodo
		//               $ad_fechasper // fecha final del nuevo periodo	
		//               $as_codperi  // código del periodo
		//	Returns:	 $lb_valido // True si realizo el select correctamente o False en caso contrario
		//	Description: Funcion que busca la fecha de inicio y de fin de un periodo
		//               y calcula la fecha de inicio y de finalizacion del periodo adicioal
		//////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe=1;//true;
		$ad_fecdesper="";
		$ad_fechasper="";		
		$ls_sql="SELECT fecdesper,fechasper ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$as_codnom."' ".
				"   AND codperi='".$as_codperi."' ";			
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Nomina MÉTODO->uf_buscar_prox_periodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_existe=0;//false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ad_fecdesper=$this->io_funciones->uf_convertirfecmostrar($row["fecdesper"]);	
				$ad_fechasper=$this->io_funciones->uf_convertirfecmostrar($row["fechasper"]);
			}			
		}
		$arrResultado['ad_fecdesper']=$ad_fecdesper;
		$arrResultado['ad_fechasper']=$ad_fechasper;
		$arrResultado['lb_existe']=$lb_existe;
		return $arrResultado;						
	}// end function uf_buscar_prox_periodo
	//-----------------------------------------------------------------------------------------------------------------------------------	
}
?>