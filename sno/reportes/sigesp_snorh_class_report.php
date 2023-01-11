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

class sigesp_snorh_class_report
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_snorh_class_report
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		global $ruta;
		$ruta='../../';

		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
                //$this->io_conexion->debug=true;
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->ds_componente=new class_datastore();
		$this->DS_detalle=new class_datastore();
                $this->DS_detalle2=new class_datastore();
                $this->rs_data_detalle=new class_datastore();
		$this->DS_detper=new class_datastore();
		$this->DS_pension=new class_datastore();
		$this->DS_depositos=new class_datastore();
		$this->DS_depositos2=new class_datastore();
		$this->DS_nominas=new class_datastore();	
		$this->DS_cargos=new class_datastore();		
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
		require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();	
		require_once($ruta."base/librerias/php/general/sigesp_lib_conexiones.php");
		$this->io_conexiones=new conexiones();	
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->rs_data="";
		$this->rs_detalle="";
		$this->rs_detalle2="";
		$this->rs_detalle3="";
	}// end function sigesp_snorh_class_report
	//-----------------------------------------------------------------------------------------------------------------------------------

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
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsis='".$as_sistema."'".
				"   AND seccion='".$as_seccion."'".
				"   AND entry='".$as_variable."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report Contable MÉTODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		return $ls_valor;
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_nombrenomina($as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_nombrenomina
		//		   Access: public
		//	    Arguments: as_codnom  // código de nómina
		//	      Returns: $ls_desnom variable buscado
		//	  Description: Función que obtiene la descripción de una nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_desnom="";
		$ls_sql="SELECT desnom ".
				"  FROM sno_nomina ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report Contable MÉTODO->uf_select_nombrenomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_desnom=$row["desnom"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $ls_desnom;
	}// end function uf_select_nombrenomina
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_fechaperiodo($as_codnom,$as_codperi,$as_campo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_fechaperiodo
		//		   Access: public
		//	    Arguments: as_codnom  // código de nómina
		//	      Returns: $ls_desnom variable buscado
		//	  Description: Función que obtiene la descripción de una nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/08/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_fecha="";
		$ls_sql="SELECT $as_campo AS campo ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$as_codnom."'".
				"   AND codperi='".$as_codperi."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report  MÉTODO->uf_select_fechaperiodo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_fecha=$row["campo"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $ls_fecha;
	}// end function uf_select_fechaperiodo
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Sección a la que pertenece la variable
		//				   as_variable  // Variable a buscar
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
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codsis='".$as_sistema."'".
				"   AND seccion='".$as_seccion."'".
				"   AND entry='".$as_variable."'";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report Contable MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
				$this->io_mensajes->message("CLASE->Report Contable MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_listadopersonal_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
										 $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										 $as_masculino,$as_femenino,$as_codubifis,$as_codpai,$as_codest,$as_codmun,
										 $as_codpar,$as_orden,$as_uniadmin,$as_codestper,$as_codmunper,$as_codparper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_masculino //  Sexo del Personal
		//	  			   as_femenino // Sexo del Personal
		//	  			   as_codubifis //Código de Ubicación Física
		//	  			   as_codpai // Código de Pais
		//	  			   as_codest // Código de Estado
		//	  			   as_codmun // Código del Municipio
		//	  			   as_codpar // Código de Parroquia
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
			if(!empty($as_codubifis))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codubifis='".$as_codubifis."'";
			}
			else
			{
				if(!empty($as_codest))
				{
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				}
				if(!empty($as_codmun))
				{
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				}
				if(!empty($as_codpar))
				{
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				}
			}
			if(!empty($as_uniadmin))
			{
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "MYSQLI":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "POSTGRES":
						$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
												  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
												  "sno_personalnomina.prouniadm>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
				}
	
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codestper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpai='".$as_codpai."'";
			$ls_criterio= $ls_criterio." AND sno_personal.codest='".$as_codestper."'";
		}
		if(!empty($as_codmunper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codmun='".$as_codmunper."'";
		}
		if(!empty($as_codparper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpar='".$as_codparper."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{ // si busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.fecingper,sno_personal.estper, ".
					"		sno_personal.nivacaper, sno_profesion.despro, sno_personalnomina.staper AS estnom, sno_nomina.desnom,   ".
					"		sno_personalnomina.fecingper AS fecingnom, sno_personal.cedper, sno_personal.sexper, sno_personal.fecnacper, ".
					"		sno_nomina.racnom,  sno_personalnomina.sueper, sno_dedicacion.desded, sno_tipopersonal.destipper, ".
					"		sno_ubicacionfisica.desubifis, ".
					"		(SELECT despai FROM sigesp_pais ".
					"		  WHERE sigesp_pais.codpai = sno_ubicacionfisica.codpai) AS despai, ".
					"		(SELECT desest FROM sigesp_estados ".
					"		  WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
					"			AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
					"		(SELECT denmun FROM sigesp_municipio ".
					"		  WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
					"			AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
					"			AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS desmun, ".
					"		(SELECT denpar FROM sigesp_parroquia ".
					"		  WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
					"			AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
					"			AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
					"			AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS despar, ".
					"		(SELECT despai FROM sigesp_pais ".
					"		  WHERE sigesp_pais.codpai = sno_personal.codpai) AS despaiper, ".
					"		(SELECT desest FROM sigesp_estados ".
					"		  WHERE sigesp_estados.codpai = sno_personal.codpai ".
					"			AND sigesp_estados.codest = sno_personal.codest) AS desestper, ".
					"		(SELECT denmun FROM sigesp_municipio ".
					"		  WHERE sigesp_municipio.codpai = sno_personal.codpai ".
					"			AND sigesp_municipio.codest = sno_personal.codest ".
					"			AND sigesp_municipio.codmun = sno_personal.codmun) AS desmunper, ".
					"		(SELECT denpar FROM sigesp_parroquia ".
					"		  WHERE sigesp_parroquia.codpai = sno_personal.codpai ".
					"			AND sigesp_parroquia.codest = sno_personal.codest ".
					"			AND sigesp_parroquia.codmun = sno_personal.codmun ".
					"			AND sigesp_parroquia.codpar = sno_personal.codpar) AS desparper, ".
					"       (SELECT denasicar FROM sno_asignacioncargo ".
					"   	  WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
					"		    AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
					"           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) AS denasicar, ".
					"       (SELECT descar FROM sno_cargo ".
					"   	  WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"		    AND sno_personalnomina.codemp = sno_cargo.codemp ".
					"		    AND sno_personalnomina.codnom = sno_cargo.codnom ".
					"           AND sno_personalnomina.codcar = sno_cargo.codcar) AS descar, ".
					"       (SELECT codcom FROM sno_componente ".
					"		  WHERE sno_componente.codemp=sno_personal.codemp ".
					"           and sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
       				"       (SELECT descom FROM sno_componente ".
					"		  WHERE sno_componente.codemp=sno_personal.codemp ".
					"           AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
       				"       (SELECT codran FROM sno_rango ".
					"         WHERE sno_rango.codemp=sno_personal.codemp ".
					"			AND sno_rango.codcom=sno_personal.codcom ".
					"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
       				"       (SELECT desran FROM sno_rango ".
					"         WHERE sno_rango.codemp=sno_personal.codemp ".
					"			AND sno_rango.codcom=sno_personal.codcom ".
					"           AND sno_rango.codran=sno_personal.codran) as desrango ".
					"  FROM sno_personal, sno_profesion, sno_personalnomina, sno_nomina, sno_dedicacion, sno_tipopersonal, sno_ubicacionfisica ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_profesion.codemp ".
					"	AND sno_personal.codpro = sno_profesion.codpro ".
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
					"	AND sno_personalnomina.codnom = sno_nomina.codnom  ".
					"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
					"	AND sno_personalnomina.codded = sno_dedicacion.codded  ".
					"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
					"	AND sno_personalnomina.codded = sno_tipopersonal.codded  ".
					"	AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper  ".
					"   AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
					"	AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.fecingper,sno_personal.estper, ".
					"		sno_personal.nivacaper, sno_profesion.despro, '---' AS estnom, '---' AS desnom, '---' AS fecingnom, ".
					"		sno_personal.cedper, sno_personal.sexper, sno_personal.fecnacper, '0' AS racnom, '0' AS sueper, ".
					"		'---' AS desded, '---' AS destipper, '---' AS desubifis, '---' AS despai, '---' AS desest, '---' AS desmun, ".
					"		 '---' AS despar, '---' AS denasicar, '---' AS descar, ".
					"		(SELECT despai FROM sigesp_pais ".
					"		  WHERE sigesp_pais.codpai = sno_personal.codpai) AS despaiper, ".
					"		(SELECT desest FROM sigesp_estados ".
					"		  WHERE sigesp_estados.codpai = sno_personal.codpai ".
					"			AND sigesp_estados.codest = sno_personal.codest) AS desestper, ".
					"		(SELECT denmun FROM sigesp_municipio ".
					"		  WHERE sigesp_municipio.codpai = sno_personal.codpai ".
					"			AND sigesp_municipio.codest = sno_personal.codest ".
					"			AND sigesp_municipio.codmun = sno_personal.codmun) AS desmunper, ".
					"		(SELECT denpar FROM sigesp_parroquia ".
					"		  WHERE sigesp_parroquia.codpai = sno_personal.codpai ".
					"			AND sigesp_parroquia.codest = sno_personal.codest ".
					"			AND sigesp_parroquia.codmun = sno_personal.codmun ".
					"			AND sigesp_parroquia.codpar = sno_personal.codpar) AS desparper, ".
					"       (SELECT codcom FROM sno_componente ".
					"		  WHERE sno_componente.codemp=sno_personal.codemp ".
					"           and sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
       				"       (SELECT descom FROM sno_componente ".
					"		  WHERE sno_componente.codemp=sno_personal.codemp ".
					"           AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
       				"       (SELECT codran FROM sno_rango ".
					"         WHERE sno_rango.codemp=sno_personal.codemp ".
					"			AND sno_rango.codcom=sno_personal.codcom ".
					"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
       				"       (SELECT desran FROM sno_rango ".
					"         WHERE sno_rango.codemp=sno_personal.codemp ".
					"			AND sno_rango.codcom=sno_personal.codcom ".
					"           AND sno_rango.codran=sno_personal.codran) as desrango ".
					"  FROM sno_personal, sno_profesion ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_profesion.codemp ".
					"	AND sno_personal.codpro = sno_profesion.codpro ".
					$ls_orden;
		} 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadopersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonal_personal_rac_rec ($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,
												  $as_anio,$as_mes,$as_peri,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_masculino //  Sexo del Personal
		//	  			   as_femenino // Sexo del Personal
		//	  			   as_codubifis //Código de Ubicación Física
		//	  			   as_codpai // Código de Pais
		//	  			   as_codest // Código de Estado
		//	  			   as_codmun // Código del Municipio
		//	  			   as_codpar // Código de Parroquia
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
			$lb_anterior=true;
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{ // si busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.fecingper,sno_personal.estper, ".
					"sno_personal.nivacaper,sno_profesion.despro, sno_personalnomina.staper AS estnom, sno_nomina.desnom, ".
					"sno_personalnomina.fecingper AS fecingnom, sno_personal.cedper,sno_personal.sexper, sno_personal.fecnacper, ".
					"sno_nomina.racnom, sno_personalnomina.sueper, sno_dedicacion.desded, sno_tipopersonal.destipper, ".
					"sno_ubicacionfisica.codubifis,sno_ubicacionfisica.desubifis, sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm, ".
					"sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,sno_personalnomina.prouniadm,sno_personalnomina.codnom, ".
					"sno_personalnomina.codgra, sno_personalnomina.descasicar, ".
					"(SELECT codasicar FROM sno_asignacioncargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
					"AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
					"AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) AS codcarnomina, ".
					"(SELECT denasicar FROM sno_asignacioncargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
					"AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
					"AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) AS denasicar, ".
					"(SELECT claasicar FROM sno_asignacioncargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
					"AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
					"AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) AS claasicar, ".
					"(SELECT codcar FROM sno_cargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					"AND sno_personalnomina.codemp = sno_cargo.codemp AND sno_personalnomina.codnom = sno_cargo.codnom ".
					"AND sno_personalnomina.codcar = sno_cargo.codcar) AS codigocar, ".
					"(SELECT descar FROM sno_cargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ". 
					"AND sno_personalnomina.codemp = sno_cargo.codemp AND sno_personalnomina.codnom = sno_cargo.codnom ".
					"AND sno_personalnomina.codcar = sno_cargo.codcar) AS descar, ".
					"(SELECT codcom FROM sno_componente WHERE sno_componente.codemp=sno_personal.codemp ".
					"AND sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
					"(SELECT descom FROM sno_componente WHERE sno_componente.codemp=sno_personal.codemp ".
					"AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
					"(SELECT codran FROM sno_rango WHERE sno_rango.codemp=sno_personal.codemp ".
					"AND sno_rango.codcom=sno_personal.codcom AND sno_rango.codran=sno_personal.codran) as codrango, ".
					"(SELECT desran FROM sno_rango WHERE sno_rango.codemp=sno_personal.codemp ".
					"AND sno_rango.codcom=sno_personal.codcom AND sno_rango.codran=sno_personal.codran) as desrango, ".
					"(SELECT desuniadm FROM sno_unidadadmin WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					" AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					" AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
					" AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm  ".
					" AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm  ".
					" AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm  ".
					" AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm) AS desuniadm, ".
			        "      (SELECT sno_grado.moncomgra FROM sno_asignacioncargo, sno_grado ".
				   "   	     WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				   "           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
				   "		   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
				   "           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
				   "           AND sno_asignacioncargo.codemp = sno_grado.codemp ".
				   " 	       AND	sno_asignacioncargo.codnom = sno_grado.codnom ".
				   " 	       AND	sno_asignacioncargo.codtab = sno_grado.codtab ".
				   " 	       AND	sno_asignacioncargo.codpas = sno_grado.codpas ".
				   " 	       AND	sno_asignacioncargo.codgra = sno_grado.codgra) as compensacion ".
					"FROM sno_personal, sno_profesion, sno_personalnomina, sno_nomina, sno_dedicacion, sno_tipopersonal, ".
					"sno_ubicacionfisica ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_profesion.codemp ".
					"	AND sno_personal.codpro = sno_profesion.codpro ".
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
					"	AND sno_personalnomina.codnom = sno_nomina.codnom  ".
					"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
					"	AND sno_personalnomina.codded = sno_dedicacion.codded  ".
					"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
					"	AND sno_personalnomina.codded = sno_tipopersonal.codded  ".
					"	AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper  ".
					"   AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
					"	AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.fecingper,sno_personal.estper, ".
					"		sno_personal.nivacaper, sno_profesion.despro, '---' AS estnom, '---' AS desnom, '---' AS fecingnom, ".
					"		sno_personal.cedper, sno_personal.sexper, sno_personal.fecnacper, '0' AS racnom, '0' AS sueper, ".
					"		'---' AS desded, '---' AS destipper, '---' AS codubifis, '---' AS desubifis, '---' AS despai, '---' AS desest, '---' AS desmun, ".
					"		 '---' AS despar, '---' AS denasicar, '---' AS descar, '' AS descasicar, 0 AS compensacion, '' AS claasicar ".
					"       (SELECT codcom FROM sno_componente ".
					"		  WHERE sno_componente.codemp=sno_personal.codemp ".
					"           and sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
       				"       (SELECT descom FROM sno_componente ".
					"		  WHERE sno_componente.codemp=sno_personal.codemp ".
					"           AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
       				"       (SELECT codran FROM sno_rango ".
					"         WHERE sno_rango.codemp=sno_personal.codemp ".
					"			AND sno_rango.codcom=sno_personal.codcom ".
					"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
       				"       (SELECT desran FROM sno_rango ".
					"         WHERE sno_rango.codemp=sno_personal.codemp ".
					"			AND sno_rango.codcom=sno_personal.codcom ".
					"           AND sno_rango.codran=sno_personal.codran) as desrango, ".
					"        '' AS desuniadm".
					"  FROM sno_personal, sno_profesion ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_profesion.codemp ".
					"	AND sno_personal.codpro = sno_profesion.codpro ".
					$ls_orden;
		} 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonal_personal_rac_rec ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadopersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0506_programado($as_rango)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0506_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0506)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 19/01/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codrep,codded,codtipper,monene,monfeb,monmar,monabr,monmay,monjun,monjul,monago,monsep,monoct,monnov,mondic, ".
				"		carene,carfeb,carmar,carabr,carmay,carjun,carjul,carago,carsep,caroct,carnov,cardic, ".
				"		(SELECT sno_dedicacion.desded FROM  sno_dedicacion ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
				"			AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
				"		(SELECT sno_tipopersonal.destipper FROM  sno_tipopersonal ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
				"			AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
				"			AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
				"  FROM sno_programacionreporte ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codrep = '0506'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0506_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_comparado0506_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0506_real($as_rango,$as_codded,$as_codtipper,$ai_cargoreal,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0506_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0506)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/01/2007								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_codtipper=="0000")
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded ";
		}
		else
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'".
						 " AND sno_hpersonalnomina.codtipper='".$as_codtipper."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		}

		$ls_sql="SELECT sno_hpersonalnomina.codper ".
				"  FROM sno_hpersonalnomina, sno_hperiodo, sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				"   AND sno_hnomina.espnom = 0 ".
				"   AND sno_hnomina.ctnom = 0 ".
				$ls_criterio.
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
				"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hnomina.codperi = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_groupcargos;

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0506_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_cargoreal=$ai_cargoreal+1;
			}
			$this->io_sql->free_result($rs_data);
		}
		if($lb_valido)
		{
			$ls_sql="SELECT sum(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hperiodo ".
					" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					$ls_criterio.
					"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
					"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0506_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_montoreal=round($row["monto"],2);
				}
				$this->io_sql->free_result($rs_data);
			}
		}		
		$arrResultado['ai_cargoreal']=$ai_cargoreal;
		$arrResultado['ai_montoreal']=$ai_montoreal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_comparado0506_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0506_gasto_programado($as_rango,$as_cuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0506_gasto_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0506)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_cuenta // cuenta presupuestaria por el que se quiere filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del gasto programado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/01/2007								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		switch(substr($as_rango,0,2))
		{
			case "01":
				$ls_campo=" enero ";
				break;
			case "02":
				$ls_campo=" febrero ";
				break;
			case "03":
				$ls_campo=" marzo ";
				break;
			case "04":
				$ls_campo=" abril ";
				break;
			case "05":
				$ls_campo=" mayo ";
				break;
			case "06":
				$ls_campo=" junio ";
				break;
			case "07":
				$ls_campo=" julio ";
				break;
			case "08":
				$ls_campo=" agosto ";
				break;
			case "09":
				$ls_campo=" septiembre ";
				break;
			case "10":
				$ls_campo=" octubre ";
				break;
			case "11":
				$ls_campo=" noviembre ";
				break;
			case "12":
				$ls_campo=" diciembre ";
				break;
		}
		$ls_anocur=substr($_SESSION["la_empresa"]["periodo"],0,4);
		$ls_sql="SELECT ".$ls_anocur." AS anocur, spg_plantillareporte.spg_cuenta AS cuenta, ".
				"		 MAX(spg_plantillareporte.".$ls_campo.") AS programado, MAX(spg_cuentas.status) AS status ".
				"  FROM spg_plantillareporte, spg_cuentas ".
				" WHERE spg_plantillareporte.codemp = '".$this->ls_codemp."' ".
				"	AND spg_plantillareporte.codrep = '0506' ".
				"   AND spg_plantillareporte.spg_cuenta LIKE '".$as_cuenta."%' ".
				"   AND spg_plantillareporte.codemp = spg_cuentas.codemp  ".
				"   AND spg_plantillareporte.spg_cuenta = spg_cuentas.spg_cuenta ".
				" GROUP BY spg_plantillareporte.codemp, spg_plantillareporte.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0506_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
				$this->DS_detalle->group_by(array('0'=>'cuenta'),array('0'=>'programado'),'programado');
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_comparado0506_gasto_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0506_gasto_real($as_rango,$as_cuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0506_gasto_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0506)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_cuenta // cuenta presupuestaria por el que se quiere filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del gasto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/01/2007								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_anocur=substr($_SESSION["la_empresa"]["periodo"],0,4);
		$ls_sql="SELECT MAX(sno_hperiodo.anocur) AS anocur, SUM(sno_hsalida.valsal) AS total, sno_hconcepto.cueprecon AS cuenta ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				" WHERE sno_hperiodo.codemp = '".$this->ls_codemp."' ".
				"	AND sno_hperiodo.anocur = '".$ls_anocur."' ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)>='".substr($as_rango,0,2)."' ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)<='".substr($as_rango,0,2)."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
				"   AND sno_hconcepto.cueprecon LIKE '".$as_cuenta."%' ".
				"	AND sno_hconcepto.cueprecon <> '' ".
				"   AND sno_hperiodo.codemp = sno_hsalida.codemp ".
				"   AND sno_hperiodo.codnom = sno_hsalida.codnom ".
				"   AND sno_hperiodo.anocur = sno_hsalida.anocur ".
				"   AND sno_hperiodo.codperi = sno_hsalida.codperi ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.cueprecon ".
				" UNION ".
				"SELECT MAX(sno_hperiodo.anocur) AS anocur, SUM(sno_hsalida.valsal) AS total, sno_hconcepto.cueprepatcon AS cuenta ".
		 		"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				" WHERE sno_hperiodo.codemp = '".$this->ls_codemp."' ".
				"	AND sno_hperiodo.anocur = '".$ls_anocur."' ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)>='".substr($as_rango,0,2)."' ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)<='".substr($as_rango,0,2)."' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND (sno_hsalida.tipsal = 'P2' OR sno_hsalida.tipsal = 'V4' OR sno_hsalida.tipsal = 'W4')".
				"   AND sno_hconcepto.cueprepatcon LIKE '".$as_cuenta."%' ".
				"	AND sno_hconcepto.cueprepatcon <> '' ".
				"   AND sno_hperiodo.codemp = sno_hsalida.codemp ".
				"   AND sno_hperiodo.codnom = sno_hsalida.codnom ".
				"   AND sno_hperiodo.anocur = sno_hsalida.anocur ".
				"   AND sno_hperiodo.codperi = sno_hsalida.codperi ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.cueprepatcon ".
				" ORDER BY cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0506_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
				$this->DS_detalle->group_by(array('0'=>'cuenta'),array('0'=>'total'),'total');
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_comparado0506_gasto_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0711_programado($as_rango)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0711_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0711)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT codrep,codded,codtipper,monene,monfeb,monmar,monabr,monmay,monjun,monjul,monago,monsep,monoct,monnov,mondic, ".
				"		carene,carfeb,carmar,carabr,carmay,carjun,carjul,carago,carsep,caroct,carnov,cardic, ".
				"		(SELECT sno_dedicacion.desded FROM  sno_dedicacion ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
				"			AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
				"		(SELECT sno_tipopersonal.destipper FROM  sno_tipopersonal ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
				"			AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
				"			AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
				"  FROM sno_programacionreporte ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codrep = '0711'";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0711_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_comparado0711_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0711_real($as_rango,$as_codded,$as_codtipper,$ai_cargoreal,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0711_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0711)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_codtipper=="0000")
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded ";
		}
		else
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'".
						 " AND sno_hpersonalnomina.codtipper='".$as_codtipper."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		}

		$ls_sql="SELECT COUNT(sno_hpersonalnomina.codper) as total ".
				"  FROM sno_hpersonalnomina, sno_hperiodo, sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				$ls_criterio.
				"   AND sno_hnomina.tipnom <> 7 ".
				"   AND sno_hnomina.espnom = '0' ".
				"   AND sno_hnomina.ctnom = '0' ".
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
				"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0711_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ai_cargoreal=$rs_data->fields["total"];
			}
			$this->io_sql->free_result($rs_data);
		}
		if($lb_valido)
		{
			$ls_sql="SELECT sum(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hperiodo, sno_hnomina ".
					" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					$ls_criterio.
					"   AND sno_hnomina.tipnom <> 7 ".
					"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
					"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
					"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
					"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0711_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if(!$rs_data->EOF)
				{
					$ai_montoreal=$rs_data->fields["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}
		}		
		$arrResultado['ai_cargoreal']=$ai_cargoreal;
		$arrResultado['ai_montoreal']=$ai_montoreal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_comparado0711_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0712_programado($as_rango)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0712_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0712)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT codrep,codded,codtipper,monene,monfeb,monmar,monabr,monmay,monjun,monjul,monago,monsep,monoct,monnov, ".
				"		mondic,carene,carfeb,carmar,carabr,carmay,carjun,carjul,carago,carsep,caroct,carnov,cardic ".
				"  FROM sno_programacionreporte ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codrep = '0712'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0712_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_comparado0712_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0712_real($as_rango,$as_catjub,$as_conjub,$ai_cargoreal,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0712_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0712)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_catjub // Categoría de Jubilación
		//	   			   as_conjub // Condición de Jubilación
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_conjub=="0000")
		{
			$ls_criterio=" AND sno_hpersonalnomina.catjub='".$as_catjub."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.catjub, sno_hpersonalnomina.conjub ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.catjub ";
		}
		else
		{
			$ls_criterio=" AND sno_hpersonalnomina.catjub='".$as_catjub."'".
						 " AND sno_hpersonalnomina.conjub='".$as_conjub."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.catjub, sno_hpersonalnomina.conjub ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.catjub, sno_hpersonalnomina.conjub ";
		}
		$ls_sql="SELECT sno_hpersonalnomina.codper ".
				"  FROM sno_hpersonalnomina, sno_hperiodo, sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				$ls_criterio.
				"   AND sno_hnomina.tipnom = 7 ".
				"   AND sno_hnomina.espnom = '0' ".
				"   AND sno_hnomina.ctnom = '0' ".
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
				"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0712_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_cargoreal=$ai_cargoreal+1;
			}
			$this->io_sql->free_result($rs_data);
		}
		if($lb_valido)
		{
			$ls_sql="SELECT sum(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hperiodo, sno_hnomina ".
					" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					$ls_criterio.
					"   AND sno_hnomina.tipnom = 7 ".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1') ".
					"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
					"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
					"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
					"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0712_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_montoreal=$row["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}
		}		
		$arrResultado['ai_cargoreal']=$ai_cargoreal;
		$arrResultado['ai_montoreal']=$ai_montoreal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_comparado0712_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestacionantiguedad_personal($as_codnomdes,$as_codnomhas,$as_anocurperdes,$as_mescurperdes,$as_anocurperhas,$as_mescurperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedad_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_prestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mesdes=str_pad($as_mescurperdes,2,"0",0);
		$li_meshas=str_pad($as_mescurperhas,2,"0",0);
		$ls_sql="SELECT sno_personal.cedper,sno_personal.nomper,sno_personal.apeper, sno_personal.nacper, sno_fideiperiodo.sueintper, ".
				"		sno_fideiperiodo.bonvacper, sno_fideiperiodo.bonfinper, (sno_fideiperiodo.apoper) AS apoper, sno_fideiperiodo.diafid, ".
				"		sno_fideiperiodo.diaadi, sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, sno_personal.fecingper, sno_fideiperiodo.codper  ".
				"  FROM sno_personal, sno_fideiperiodo ".
				" WHERE sno_fideiperiodo.codemp = '".$this->ls_codemp."' ".
				"   AND sno_fideiperiodo.codnom >= '".$as_codnomdes."' ".
				"   AND sno_fideiperiodo.codnom <= '".$as_codnomhas."' ".
				"   AND sno_fideiperiodo.anocurper >= '".$as_anocurperdes."' ".
				"   AND sno_fideiperiodo.mescurper >= '".$li_mesdes."' ".
				"   AND sno_fideiperiodo.anocurper <= '".$as_anocurperhas."' ".
				"   AND sno_fideiperiodo.mescurper <= '".$li_meshas."' ".
				"   AND sno_personal.codemp = sno_fideiperiodo.codemp ".
				"	AND sno_personal.codper = sno_fideiperiodo.codper ".
				" ORDER BY sno_personal.codper, sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestacionantiguedad_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_prestacionantiguedad_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_constancia($as_codcont,$as_codnom,$as_codperdes,$as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_constancia
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codcont // Código de la Constancia
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la constancia
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio=" sno_personalnomina.codnom = '".$as_codnom."' ";
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codper<='".$as_codperhas."'";
		}
		$ls_sql="SELECT codcont, descont, concont, tamletcont, intlincont, marinfcont, marsupcont, titcont, piepagcont, ".
				"		tamletpiecont, arcrtfcont, consumcont ".
				"  FROM sno_constanciatrabajo ".
				" WHERE codemp = '".$this->ls_codemp."' ".
				"   AND codcont = '".$as_codcont."' ".
				"   AND codemp IN (SELECT codemp FROM sno_personalnomina ".
				"					WHERE ".$ls_criterio.")";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_constancia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_constanciatrabajo_constancia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_personal($as_codnom,$ai_rac,$as_codperdes,$as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   ai_rac // Código de personal donde se empieza a filtrar
		//	   			   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   ai_tipoperiodo // Tipo de Período de la Nómina
		//	  			   as_peractnom // Período Actual de la Nómina
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterioperiodo="";
		$ls_comp = '';
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if($ai_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_asignacioncargo ".
					   "   	     WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_personalnomina.codnom='".$as_codnom."' ".
					   "           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
					   "		   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
				       "           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) as descar ";
			
			$ls_comp  =",       (SELECT sno_grado.moncomgra FROM sno_asignacioncargo, sno_grado ".
					   "   	     WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_personalnomina.codnom='".$as_codnom."' ".
					   "           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
					   "		   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
				       "           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
					   "           AND sno_asignacioncargo.codemp = sno_grado.codemp ".
					   " 	       AND	sno_asignacioncargo.codnom = sno_grado.codnom ".
					   " 	       AND	sno_asignacioncargo.codtab = sno_grado.codtab ".
					   " 	       AND	sno_asignacioncargo.codpas = sno_grado.codpas ".
					   " 	       AND	sno_asignacioncargo.codgra = sno_grado.codgra) as compen ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_cargo ".
					   "   	     WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_personalnomina.codnom='".$as_codnom."' ".
					   "		   AND sno_personalnomina.codemp = sno_cargo.codemp ".
					   "		   AND sno_personalnomina.codnom = sno_cargo.codnom ".
				       "           AND sno_personalnomina.codcar = sno_cargo.codcar) as descar ";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_personal.dirper, sno_personal.fecnacper, sno_personal.edocivper, sno_personal.nacper, sno_personal.telhabper, ".
				"		sno_personal.telmovper, sno_personalnomina.sueper, ".$ls_descar.", sno_personalnomina.horper, ".
				"		sno_personalnomina.sueintper, sno_personalnomina.sueproper, sno_personal.fecegrper, ".
				"		sno_unidadadmin.desuniadm, sno_dedicacion.desded, sno_tipopersonal.destipper, sno_nomina.tipnom,  ".
				"       sno_personal.fecjubper,sno_personalnomina.salnorper, srh_gerencia.denger,sno_personalnomina.descasicar, ".
			    "       sno_personalnomina.fecingper AS fecingnom, MAX(sno_personalnomina.fecculcontr) AS fecculcontr ".$ls_comp.
				"  FROM sno_personal, sno_personalnomina, sno_unidadadmin, sno_dedicacion, sno_tipopersonal, sno_nomina, ".
				"       srh_gerencia ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."' ".
				"   AND sno_personalnomina.codnom = '".$as_codnom."' ".
				$ls_criterio.
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"   AND sno_personal.codper = sno_personalnomina.codper ".
				"	AND sno_personalnomina.codemp = sno_nomina.codemp ".			
				"	AND sno_personalnomina.codnom = sno_nomina.codnom ".			
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personal.codemp = srh_gerencia.codemp ".
				"   AND sno_personal.codger = srh_gerencia.codger ".
				" GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		   sno_personal.dirper, sno_personal.fecnacper, sno_personal.edocivper, sno_personal.nacper, sno_personal.telhabper,".
				"		   sno_personal.telmovper, sno_personalnomina.sueper, sno_personalnomina.horper, sno_personalnomina.sueintper, ".
				"		   sno_personalnomina.sueproper, sno_unidadadmin.desuniadm, sno_dedicacion.desded, sno_tipopersonal.destipper, ".
				"		   sno_personalnomina.codemp,sno_personalnomina.codnom, sno_personalnomina.codcar, sno_personalnomina.codasicar, ".
				"		   sno_personal.fecegrper, sno_nomina.tipnom, sno_personal.fecjubper,sno_personalnomina.salnorper,	".
				"          srh_gerencia.denger,sno_personalnomina.descasicar,sno_personalnomina.fecingper ".
				" ORDER BY sno_personal.cedper ";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_detalle->EOF)
			{
				$lb_valido=false;
				$this->io_mensajes->message("No hay personal que mostrar");
			}
		}		
		return $lb_valido;
	}// end function uf_constanciatrabajo_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_sumaconceptos($as_codnom,$as_codper,$as_mes,$as_anocurnom,$as_concepto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_sumaconceptos
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   as_codper // Código de personal 
		//	  			   as_mes // Mes donde se va a buscar el sueldo integral
		//	  			   as_anocurnom // Año en curso de la Nómina
		//	  			   ai_sueintper // Sueldo Integral Mensual 
		//	  			   ai_sueproper // Sueldo Promedio Mensual
		//	      Returns: lb_valido True si se no ocrrio ningún error
		//    Description: función que busca la información del sueldo integral ni del sueldo promedio del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sumaconcepto=0;
		$li_difconpnom=rtrim($this->uf_select_config("SNO","NOMINA","DIFERENCIAR CONCEPTOS NOMINA","0","I"));
		if ($li_difconpnom==1)
		{
			$as_concepto=str_replace(";","",$as_concepto);
			$as_concepto=str_replace("-","','",$as_concepto);
			$as_concepto="'".$as_concepto."'";
			switch($_SESSION["ls_gestor"])
			{
				case "MYSQLT":				
					$ls_concatconc=" CONCAT(sno_hsalida.codnom,sno_hsalida.codconc) ";
				break;
				case "MYSQLI":				
					$ls_concatconc=" CONCAT(sno_hsalida.codnom,sno_hsalida.codconc) ";
				break;
				case "POSTGRES":
					$ls_concatconc=" sno_hsalida.codnom||sno_hsalida.codconc ";
				break;
			}
			$ls_sql="SELECT SUM(valsal) AS conceptos".
					"  FROM sno_hpersonalnomina, sno_hperiodo , sno_hnomina, sno_hsalida ".
					" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					"   AND sno_hpersonalnomina.anocur='".$as_anocurnom."' ".
					"   AND sno_hpersonalnomina.codper='".$as_codper."' ".
					"   AND sno_hpersonalnomina.staper IN ('1','2') ".
					"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ".
					"   AND sno_hnomina.espnom='0' ".
					"   AND $ls_concatconc IN (".$as_concepto.") ".
					"   AND sno_hpersonalnomina.codemp= sno_hperiodo.codemp".
					"   AND sno_hpersonalnomina.codnom= sno_hperiodo.codnom".
					"   AND sno_hpersonalnomina.anocur= sno_hperiodo.anocur".
					"   AND sno_hpersonalnomina.codperi= sno_hperiodo.codperi".
					"   AND sno_hpersonalnomina.codemp= sno_hsalida.codemp".
					"   AND sno_hpersonalnomina.codnom= sno_hsalida.codnom".
					"   AND sno_hpersonalnomina.anocur= sno_hsalida.anocur".
					"   AND sno_hpersonalnomina.codperi= sno_hsalida.codperi".
					"   AND sno_hpersonalnomina.codper= sno_hsalida.codper".
					"   AND sno_hnomina.codemp= sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom= sno_hperiodo.codnom ".
					"   AND sno_hnomina.anocurnom= sno_hperiodo.anocur".
					"   AND sno_hnomina.peractnom= sno_hperiodo.codperi ".
					" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codper ";
		}
		else
		{
			$as_concepto=str_replace("-","','",$as_concepto);
			$as_concepto="'".$as_concepto."'";
			$ls_sql="SELECT SUM(valsal) AS conceptos".
					"  FROM sno_hpersonalnomina, sno_hperiodo , sno_hnomina, sno_hsalida ".
					" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					"   AND sno_hpersonalnomina.anocur='".$as_anocurnom."' ".
					"   AND sno_hpersonalnomina.codper='".$as_codper."' ".
					"   AND sno_hpersonalnomina.staper IN ('1','2') ".
					"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ".
					"   AND sno_hnomina.espnom='0' ".
					"   AND sno_hpersonalnomina.codnom='".$as_codnom."' ".
					"   AND sno_hsalida.codconc IN (".$as_concepto.") ".
					"   AND sno_hpersonalnomina.codemp= sno_hperiodo.codemp".
					"   AND sno_hpersonalnomina.codnom= sno_hperiodo.codnom".
					"   AND sno_hpersonalnomina.anocur= sno_hperiodo.anocur".
					"   AND sno_hpersonalnomina.codperi= sno_hperiodo.codperi".
					"   AND sno_hpersonalnomina.codemp= sno_hsalida.codemp".
					"   AND sno_hpersonalnomina.codnom= sno_hsalida.codnom".
					"   AND sno_hpersonalnomina.anocur= sno_hsalida.anocur".
					"   AND sno_hpersonalnomina.codperi= sno_hsalida.codperi".
					"   AND sno_hpersonalnomina.codper= sno_hsalida.codper".
					"   AND sno_hnomina.codemp= sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom= sno_hperiodo.codnom ".
					"   AND sno_hnomina.anocurnom= sno_hperiodo.anocur".
					"   AND sno_hnomina.peractnom= sno_hperiodo.codperi ".
					" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codper ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_integralpromedio_mensual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_sumaconcepto=$row["conceptos"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ai_sumaconcepto']=$ai_sumaconcepto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_constanciatrabajo_sumaconceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_integralpromedio_mensual($as_codnom,$as_codper,$as_mes,$as_anocurnom,$ai_sueintper,$ai_sueproper,$ai_salnorper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_integralpromedio_mensual
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   as_codper // Código de personal 
		//	  			   as_mes // Mes donde se va a buscar el sueldo integral
		//	  			   as_anocurnom // Año en curso de la Nómina
		//	  			   ai_sueintper // Sueldo Integral Mensual 
		//	  			   ai_sueproper // Sueldo Promedio Mensual
		//	      Returns: lb_valido True si se no ocrrio ningún error
		//    Description: función que busca la información del sueldo integral ni del sueldo promedio del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueintper=0;
		$ai_sueproper=0;
		$ls_sql="SELECT SUM(sueintper) AS sueintper, SUM(sueproper) AS sueproper, SUM(salnorper) AS salnorper".
				"  FROM sno_hpersonalnomina, sno_hperiodo , sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.anocur='".$as_anocurnom."' ".
				"   AND sno_hpersonalnomina.codper='".$as_codper."' ".
				"   AND sno_hpersonalnomina.codnom='".$as_codnom."' ".
				"   AND sno_hpersonalnomina.staper IN ('1','2') ".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ".
				"   AND sno_hnomina.espnom='0' ".
				"   AND sno_hpersonalnomina.codemp= sno_hperiodo.codemp".
				"   AND sno_hpersonalnomina.codnom= sno_hperiodo.codnom".
				"   AND sno_hpersonalnomina.anocur= sno_hperiodo.anocur".
				"   AND sno_hpersonalnomina.codperi= sno_hperiodo.codperi".
				"   AND sno_hnomina.codemp= sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom= sno_hperiodo.codnom ".
				"   AND sno_hnomina.anocurnom= sno_hperiodo.anocur".
				"   AND sno_hnomina.peractnom= sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_integralpromedio_mensual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_sueintper=$row["sueintper"];
				$ai_sueproper=$row["sueproper"];
				$ai_salnorper=$row["salnorper"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ai_sueintper']=$ai_sueintper;
		$arrResultado['ai_sueproper']=$ai_sueproper;
		$arrResultado['ai_salnorper']=$ai_salnorper;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_constanciatrabajo_integralpromedio_mensual
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_concepto_mensual($as_codnom,$as_codper,$as_mes,$as_anocurnom,$as_codconc,$as_nombre,$ai_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_integralpromedio_mensual
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   as_codper // Código de personal 
		//	  			   as_mes // Mes donde se va a buscar el sueldo integral
		//	  			   as_anocurnom // Año en curso de la Nómina
		//	  			   ai_sueintper // Sueldo Integral Mensual 
		//	  			   ai_sueproper // Sueldo Promedio Mensual
		//	      Returns: lb_valido True si se no ocrrio ningún error
		//    Description: función que busca la información del sueldo integral ni del sueldo promedio del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_nombre="";
		$ai_monto=0;
		$ls_sql="SELECT SUM(valsal) AS valsal, MAX(nomcon) AS nomcon ".
				"  FROM sno_hconcepto".
				" INNER JOIN sno_hperiodo ".
				"    ON sno_hconcepto.codemp='".$this->ls_codemp."' ".
				"   AND sno_hconcepto.anocur='".$as_anocurnom."'".
				"   AND sno_hconcepto.codconc='".trim($as_codconc)."'".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."'".
				"   AND sno_hconcepto.codemp= sno_hperiodo.codemp ".
				"   AND sno_hconcepto.codnom= sno_hperiodo.codnom ".
				"   AND sno_hconcepto.anocur= sno_hperiodo.anocur ".
				"   AND sno_hconcepto.codperi= sno_hperiodo.codperi ".
				"  LEFT JOIN sno_hsalida ".
				"    ON sno_hconcepto.codemp='".$this->ls_codemp."' ".
				"   AND sno_hconcepto.anocur='".$as_anocurnom."' ".
				"   AND sno_hconcepto.codconc='".trim($as_codconc)."'".   
				"   AND sno_hsalida.codper='".$as_codper."'".   
				"   AND sno_hconcepto.codemp= sno_hsalida.codemp ".
				"   AND sno_hconcepto.codnom= sno_hsalida.codnom". 
				"   AND sno_hconcepto.anocur= sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi= sno_hsalida.codperi ".
				"   AND sno_hconcepto.codconc= sno_hsalida.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_integralpromedio_mensual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$as_nombre=$rs_data->fields["nomcon"];
				$ai_monto=$rs_data->fields["valsal"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['as_nombre']=$as_nombre;
		$arrResultado['ai_monto']=$ai_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_constanciatrabajo_concepto_mensual
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_asignacion_mensual($as_codnom,$as_codper,$as_mes,$as_anocurnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_asignacion_mensual
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   as_codper // Código de personal 
		//	  			   as_mes // Mes donde se va a buscar el sueldo integral
		//	  			   as_anocurnom // Año en curso de la Nómina
		//	  			   ai_sueintper // Sueldo Integral Mensual 
		//	  			   ai_sueproper // Sueldo Promedio Mensual
		//	      Returns: lb_valido True si se no ocrrio ningún error
		//    Description: función que busca la información del sueldo integral ni del sueldo promedio del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_monto=0;
		$ls_sql="SELECT SUM(valsal) AS valsal ".
				"  FROM sno_hconcepto".
				" INNER JOIN sno_hperiodo ".
				"    ON sno_hconcepto.codemp='".$this->ls_codemp."' ".
				"   AND sno_hconcepto.anocur='".$as_anocurnom."'".
				"   AND sno_hconcepto.codnom='".$as_codnom."'".
				"   AND sno_hconcepto.sigcon='A'".
				"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."'".
				"   AND sno_hconcepto.codemp= sno_hperiodo.codemp ".
				"   AND sno_hconcepto.codnom= sno_hperiodo.codnom ".
				"   AND sno_hconcepto.anocur= sno_hperiodo.anocur ".
				"   AND sno_hconcepto.codperi= sno_hperiodo.codperi ".
				"  LEFT JOIN sno_hsalida ".
				"    ON sno_hconcepto.codemp='".$this->ls_codemp."' ".
				"   AND sno_hconcepto.anocur='".$as_anocurnom."' ".
				"   AND sno_hconcepto.codnom='".$as_codnom."'".
				"   AND sno_hconcepto.sigcon='A'".
				"   AND sno_hsalida.codper='".trim($as_codper)."'".   
				"   AND sno_hsalida.tipsal='A'".
				"   AND sno_hconcepto.codemp= sno_hsalida.codemp ".
				"   AND sno_hconcepto.codnom= sno_hsalida.codnom". 
				"   AND sno_hconcepto.anocur= sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi= sno_hsalida.codperi ".
				"   AND sno_hconcepto.codconc= sno_hsalida.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_integralpromedio_mensual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ai_monto=$rs_data->fields["valsal"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ai_monto']=$ai_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_constanciatrabajo_asignacion_mensual
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_anticipos($as_codper,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_anticipos
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   as_codper // Código de personal 
		//	  			   as_mes // Mes donde se va a buscar el sueldo integral
		//	  			   as_anocurnom // Año en curso de la Nómina
		//	  			   ai_sueintper // Sueldo Integral Mensual 
		//	  			   ai_sueproper // Sueldo Promedio Mensual
		//	      Returns: lb_valido True si se no ocrrio ningún error
		//    Description: función que busca la información del sueldo integral ni del sueldo promedio del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_sueintper=0;
		$ai_sueproper=0;
		$ls_sql="SELECT codant, estant, fecant, monpreant, monintant, monantant, monantint, monant, monint, motant, obsant ".
				"  FROM sno_anticipoprestaciones ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_integralpromedio_mensual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_constanciatrabajo_anticipos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_constanciatrabajo_conceptos_fijos_variable($as_codper,$as_mesanterior,$as_anocurnom,$ai_confij,$ai_convar)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_constanciatrabajo_anticipos
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)  
		//	    Arguments: as_codnom // Código de la Nómina
		//	   			   as_codper // Código de personal 
		//	  			   as_mes // Mes donde se va a buscar el sueldo integral
		//	  			   as_anocurnom // Año en curso de la Nómina
		//	  			   ai_sueintper // Sueldo Integral Mensual 
		//	  			   ai_sueproper // Sueldo Promedio Mensual
		//	      Returns: lb_valido True si se no ocrrio ningún error
		//    Description: función que busca la información del sueldo integral ni del sueldo promedio del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_confij=0;
		$ai_convar=0;
		$as_mes1='';
		$as_ano1='';
		if ($as_mesanterior=='01')
		{
			$as_mes1='12';
			$as_ano1=$as_anocurnom-1;	
		}
		else
		{
			$as_mes1=str_pad($as_mesanterior-1,2,'0',0);
			$as_ano1=$as_anocurnom;	
		}
		if ($as_mes1=='01')
		{
			$as_mes1='12';
			$as_ano1=$as_ano1-1;	
		}
		else
		{
			$as_mes1=$as_mes1-1;
			$as_ano1=$as_ano1;	
		}
		$as_mes1=str_pad($as_mes1,2,'0',0);
		$ls_sql="SELECT confij, 0 as convar ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND fecsue='".$as_anocurnom."-".$as_mesanterior."-01'".
				" UNION ".
				"SELECT 0 AS confij, SUM(convar)/3 AS convar ".
				"  FROM sno_sueldoshistoricos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND fecsue>='".$as_ano1."-".$as_mes1."-01'".
				"   AND fecsue<='".$as_anocurnom."-".$as_mesanterior."-01'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_conceptos_fijos_variable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ai_confij=round($ai_confij + $rs_data->fields["confij"],2);
				$ai_convar=round($ai_convar + $rs_data->fields["convar"],2);
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['ai_confij']=$ai_confij;
		$arrResultado['ai_convar']=$ai_convar;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_constanciatrabajo_conceptos_fijos_variable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------- 
    function uf_constanciatrabajo_constancia_lote($as_codcont,$as_codnom) 
    { 
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        //       Function: uf_constanciatrabajo_constancia_lote 
        //         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)   
        //        Arguments: as_codcont // Código de la Constancia 
        //          Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo 
        //    Description: función que busca la información de la constancia 
        //       Creado Por: Ing. Yesenia Moreno 
        // Fecha Creación: 06/07/2006                                 Fecha Última Modificación :   
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        $lb_valido=true;         
		$ls_sql="SELECT codcont, descont, concont, tamletcont, intlincont, marinfcont, ". 
				"       marsupcont, titcont, piepagcont, tamletpiecont, arcrtfcont, consumcont ".                       
				"  FROM sno_constanciatrabajo                   ".     
				" WHERE codemp = '".$this->ls_codemp."' ". 
				"   AND codcont = '".$as_codcont."'     ";           
           
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_constancia_lote ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
        return $lb_valido; 
    }// end function uf_constanciatrabajo_constancia_lote 
	//------------------------------------------------------------------------------------------------------------------------------------------ 

	//---------------------------------------------------------------------------------------------------------------------------------- 
	function uf_constanciatrabajo_personal_lote($as_codnom,$ai_rac,$arr_codper,$li_totcodper) 
	{ 
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        //       Function: uf_constanciatrabajo_personal_lote 
        //         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo)   
        //        Arguments: as_codnom // Código de la Nómina 
        //                      ai_rac // Código de personal donde se empieza a filtrar 
        //                      as_codperdes // Código de personal donde se empieza a filtrar 
        //                     as_codperhas // Código de personal donde se termina de filtrar           
        //                     ai_tipoperiodo // Tipo de Período de la Nómina 
        //                     as_peractnom // Período Actual de la Nómina 
        //          Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo 
        //    Description: función que busca la información del personal 
        //       Creado Por: Ing. Yesenia Moreno 
        // Fecha Creación: 06/07/2006                                 Fecha Última Modificación :   
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
        $lb_valido=true; 
        $ls_criterio=""; 
        $ls_criterioperiodo=""; 
        for ($j=1;$j<$li_totcodper;$j++) 
        { 
           $as_codperdes=$arr_codper[$j]; 
           $as_codperhas=$arr_codper[$j]; 
           if(!empty($as_codperdes)) 
            { 
                $ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'"; 
            } 
            if(!empty($as_codperhas)) 
            { 
                $ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'"; 
            } 
            if($ai_rac=="1") // Utiliza RAC 
            { 
                $ls_descar="       (SELECT denasicar FROM sno_asignacioncargo ". 
                           "            WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ". 
                           "           AND sno_personalnomina.codnom='".$as_codnom."' ". 
                           "           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ". 
                           "           AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ". 
                           "           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) as descar "; 
            } 
            else // No utiliza RAC 
            { 
                $ls_descar="       (SELECT descar FROM sno_cargo ". 
                           "            WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ". 
                           "           AND sno_personalnomina.codnom='".$as_codnom."' ". 
                           "           AND sno_personalnomina.codemp = sno_cargo.codemp ". 
                           "           AND sno_personalnomina.codnom = sno_cargo.codnom ". 
                           "           AND sno_personalnomina.codcar = sno_cargo.codcar) as descar "; 
            } 
           if ($j==1) 
           {     
                 
                $ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ". 
                        "        sno_personal.dirper, sno_personal.fecnacper, sno_personal.edocivper, sno_personal.nacper, sno_personal.telhabper, ". 
                        "        sno_personal.telmovper, sno_personalnomina.sueper, ".$ls_descar.", sno_personalnomina.horper, ". 
                        "        sno_personalnomina.sueintper, sno_personalnomina.sueproper, sno_personal.fecegrper, ". 
                        "        sno_unidadadmin.desuniadm, sno_dedicacion.desded, sno_tipopersonal.destipper, ". 
                        "       sno_nomina.tipnom, sno_personal.fecjubper, srh_gerencia.denger  ". 
                        "  FROM sno_personal, sno_personalnomina, sno_unidadadmin, sno_dedicacion, sno_tipopersonal, ". 
                        "       sno_nomina, srh_gerencia ". 
                        " WHERE sno_personal.codemp = '".$this->ls_codemp."' ". 
                        "   AND sno_personalnomina.codnom = '".$as_codnom."' ". 
                        $ls_criterio. 
                        "   AND sno_personal.codemp = sno_personalnomina.codemp ". 
                        "   AND sno_personal.codper = sno_personalnomina.codper ". 
                        "    AND sno_personalnomina.codemp = sno_nomina.codemp ".             
                        "    AND sno_personalnomina.codnom = sno_nomina.codnom ".             
                        "   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ". 
                        "   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ". 
                        "   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ". 
                        "   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ". 
                        "   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ". 
                        "   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ". 
                        "   AND sno_personalnomina.codemp = sno_dedicacion.codemp ". 
                        "   AND sno_personalnomina.codded = sno_dedicacion.codded ". 
                        "   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ". 
                        "   AND sno_personalnomina.codded = sno_tipopersonal.codded ". 
                        "   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ". 
                        "   AND sno_personal.codemp = srh_gerencia.codemp ". 
                        "   AND sno_personal.codger = srh_gerencia.codger "; 
            } 
            else 
            { 
                $ls_sql=$ls_sql." UNION "."SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ". 
                        "        sno_personal.dirper, sno_personal.fecnacper, sno_personal.edocivper, sno_personal.nacper, sno_personal.telhabper, ". 
                        "        sno_personal.telmovper, sno_personalnomina.sueper, ".$ls_descar.", sno_personalnomina.horper, ". 
                        "        sno_personalnomina.sueintper, sno_personalnomina.sueproper, sno_personal.fecegrper, ". 
                        "        sno_unidadadmin.desuniadm, sno_dedicacion.desded, sno_tipopersonal.destipper, ". 
                        "       sno_nomina.tipnom, sno_personal.fecjubper, srh_gerencia.denger  ". 
                        "  FROM sno_personal, sno_personalnomina, sno_unidadadmin, sno_dedicacion, sno_tipopersonal, ". 
                        "       sno_nomina, srh_gerencia ". 
                        " WHERE sno_personal.codemp = '".$this->ls_codemp."' ". 
                        "   AND sno_personalnomina.codnom = '".$as_codnom."' ". 
                        $ls_criterio. 
                        "   AND sno_personal.codemp = sno_personalnomina.codemp ". 
                        "   AND sno_personal.codper = sno_personalnomina.codper ". 
                        "    AND sno_personalnomina.codemp = sno_nomina.codemp ".             
                        "    AND sno_personalnomina.codnom = sno_nomina.codnom ".             
                        "   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ". 
                        "   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ". 
                        "   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ". 
                        "   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ". 
                        "   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ". 
                        "   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ". 
                        "   AND sno_personalnomina.codemp = sno_dedicacion.codemp ". 
                        "   AND sno_personalnomina.codded = sno_dedicacion.codded ". 
                        "   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ". 
                        "   AND sno_personalnomina.codded = sno_tipopersonal.codded ". 
                        "   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ". 
                        "   AND sno_personal.codemp = srh_gerencia.codemp ". 
                        "   AND sno_personal.codger = srh_gerencia.codger "; 
            } 
        }// fin del for 
        $ls_sql=$ls_sql." ORDER BY cedper ";   
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_constanciatrabajo_personal_lote ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_detalle->EOF)
			{
				$lb_valido=false;
			}
		}		
        return $lb_valido; 
    }// end function uf_constanciatrabajo_personal_lote
	//---------------------------------------------------------------------------------------------------------------------------------- 

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_aportepatronal_personal($as_codnomdes,$as_codnomhas,$as_anodes,$as_mesdes,$as_anohas,$as_meshas,
									    $as_perdes,$as_perhas,$as_codconc,$as_conceptocero,$as_subnomdes,$as_subnomhas,$as_orden,$lb_anio_dist)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_aportepatronal_personal
		//		   Access: public (desde la clase sigesp_snorh_rpp_aportepatronal)  
		//	    Arguments: as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_anodes // Año en curso Desde
		//	  			   as_mesdes // mes Desde
		//	    		   as_anohas // Año en curso Hasta
		//	  			   as_meshas // mes Hasta
		//	    		   as_perdes // Período desde
		//	  			   as_perhas // Período hasta
		//	    		   as_codconc // Código del concepto del que se desea busca el personal
		//				   as_conceptocero // Conceptos en cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   en las nóminas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_orden="";
		$ls_anioes=$as_anohas-1;
		$ls_concbanavih=rtrim($this->uf_select_config("SNO","NOMINA","CONCEPTOS_BANAVIH","","C"));
		$ls_concbanavih=str_replace("-","','",$ls_concbanavih);
		$ls_concbanavih="'".$ls_concbanavih."'";
		if ($lb_anio_dist==1)
		{
			$as_anodes=$ls_anioes;
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_anodes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur>='".$as_anodes."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_hsalida.anocur>='".$as_anodes."' ";
		}
		if(!empty($as_anohas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur<='".$as_anohas."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_hsalida.anocur<='".$as_anohas."' ";
		}
		if(!empty($as_perdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codperi>='".$as_perdes."' ";
			$ls_criterio2 = $ls_criterio2." AND sno_hsalida.codperi>='".$as_perdes."' ";
		}
		if(!empty($as_mesdes))
		{
			if ($lb_anio_dist=1)
			{
				$ls_criterio = $ls_criterio." AND ((substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)>='".$as_mesdes."' ";
				$ls_criterio2 = $ls_criterio2." AND ((substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)>='".$as_mesdes."' ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)>='".$as_mesdes."' ";
				$ls_criterio2 = $ls_criterio2." AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)>='".$as_mesdes."' ";
			}
		}
		if(!empty($as_meshas))
		{
			$ls_criterio = $ls_criterio." AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)<='".$as_meshas."' ";
			$ls_criterio2 = $ls_criterio2." AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)<='".$as_meshas."' ";
		}
		if ($lb_anio_dist=1)
		{
			$ls_criterio = $ls_criterio. " AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),1,4)='".$as_anohas."') ".
										 " OR  (substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)='12' ".	
										 " AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),1,4)='".$ls_anioes."')) ";
			
			$ls_criterio2 = $ls_criterio2. " AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),1,4)='".$as_anohas."') ".
										 " OR (substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)='12' ".	
										 " AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),1,4)='".$ls_anioes."')) ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criterio2= $ls_criterio2."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criterio2= $ls_criterio2."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		}
		$ls_sueldointegralbanavih=" 0  as sueintbanavih, ";
		if($ls_concbanavih<>"")
		{
			$ls_sueldointegralbanavih="       (SELECT SUM(valsal) ".
									  " 		 FROM sno_hsalida ".
					 				  "     	WHERE sno_hsalida.codconc IN (".$ls_concbanavih.") ".
									  $ls_criterio.
									  "           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
									  "   		  AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
									  "   		  AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
									  "   		  AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
									  "   		  AND sno_hpersonalnomina.codper = sno_hsalida.codper) as sueintbanavih, ";
		}
		if(!empty($as_codconc))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc='".$as_codconc."' ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper,sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, MAX(sno_hpersonalnomina.sueintper) as sueintper, sno_hpersonalnomina.codnom, ".
				$ls_sueldointegralbanavih.
		        "       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1') ".
				$ls_criterio2.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as ingresos, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR sno_hsalida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as personal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR sno_hsalida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as patron ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hperiodo ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hpersonalnomina.anocur>='".$as_anodes."' ".
				"   AND sno_hpersonalnomina.anocur<='".$as_anohas."' ".
				"   AND sno_hpersonalnomina.codperi>='".$as_perdes."' ".
				"   AND sno_hpersonalnomina.codperi<='".$as_perhas."' ".
				"   AND ((substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)>='".$as_mesdes."' ". 
				"   AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)<='".$as_meshas."' ".
				"   AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),1,4)='".$as_anohas."') ".
				"   OR (substr(CAST(sno_hperiodo.fecdesper AS char(10)),6,2)='12' ".
				"   AND substr(CAST(sno_hperiodo.fecdesper AS char(10)),1,4)='".$ls_anioes."')) ".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2') ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.codper, sno_personal.codper,".
				" 		   sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, 	sno_hpersonalnomina.codsubnom,sno_hperiodo.fecdesper ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_aportepatronal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);			
				$this->DS->group_by(array('0'=>'codper'),array('0'=>'personal','1'=>'patron'),'codper');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_aportepatronal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_afiliado($as_codperdes,$as_codperhas,$as_tiptra,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_afiliado
		//         Access: public (desde la clase sigesp_snorh_rpp_ipasme_afiliado)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_tiptra // Tipo de Transacción  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_ipasme_afiliado.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_ipasme_afiliado.codper<='".$as_codperhas."'";
		}
		if(!empty($as_tiptra))
		{
			$ls_criterio= $ls_criterio." AND sno_ipasme_afiliado.tiptraafi='".$as_tiptra."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper,sno_personal.apeper, ".
				"		sno_personal.fecingper, sno_ipasme_afiliado.tiptraafi, sno_ipasme_afiliado.tipafiafi ".
				"  FROM sno_personal, sno_ipasme_afiliado ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_ipasme_afiliado.codemp ".
				"	AND sno_personal.codper = sno_ipasme_afiliado.codper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_afiliado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_afiliado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_beneficiario_personal($as_codperdes,$as_codperhas,$as_tiptra,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_beneficiario_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_ipasme_beneficiario)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_tiptra // Tipo de Transacción  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_ipasme_afiliado.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_ipasme_afiliado.codper<='".$as_codperhas."'";
		}
		if(!empty($as_tiptra))
		{
			$ls_criterio= $ls_criterio." AND sno_ipasme_beneficiario.tiptraben='".$as_tiptra."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper,sno_personal.apeper ".
				"  FROM sno_personal, sno_ipasme_afiliado, sno_ipasme_beneficiario ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_ipasme_afiliado.codemp ".
				"	AND sno_personal.codper = sno_ipasme_afiliado.codper ".
				"	AND sno_ipasme_afiliado.codemp = sno_ipasme_beneficiario.codemp ".
				"	AND sno_ipasme_afiliado.codper = sno_ipasme_beneficiario.codper ".
				" GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.nomper,sno_personal.apeper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_beneficiario_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_beneficiario_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_beneficiario_bene($as_codper,$as_tiptra)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_beneficiario_bene
		//         Access: public (desde la clase sigesp_snorh_rpp_ipasme_beneficiario)  
		//	    Arguments: as_codper // Código de personal
		//	  			   as_tiptra // Tipo de Transacción  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del Beneficario del personal 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_tiptra))
		{
			$ls_criterio= $ls_criterio." AND tiptraben='".$as_tiptra."'";
		}
		$ls_sql="SELECT codben, cedben, prinomben, priapeben, tiptraben, codpare  ".
				"  FROM sno_ipasme_beneficiario ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codper = '".$as_codper."'".
				"   ".$ls_criterio." ".
				" ORDER BY codben ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_beneficiario_bene ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_beneficiario_bene
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_dependencia($as_coddepdes,$as_coddephas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_dependencia
		//         Access: public (desde la clase sigesp_snorh_rpp_ipasme_dependencia)  
		//	    Arguments: as_coddepdes // Código de Dependencia donde se empieza a filtrar
		//	  			   as_coddephas // Código de Dependencia donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las Dependencias
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_coddepdes))
		{
			$ls_criterio= " AND coddep>='".$as_coddepdes."'";
		}
		if(!empty($as_coddephas))
		{
			$ls_criterio= $ls_criterio." AND coddep<='".$as_coddephas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por código de Dependencia
				$ls_orden="ORDER BY coddep ";
				break;

			case "2": // Ordena por Descripción de Dependencia
				$ls_orden="ORDER BY desdep ";
				break;
		}
		$ls_sql="SELECT coddep,desdep ".
				"  FROM sno_ipasme_dependencias ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_dependencia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_dependencia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_aporte_concepto($as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_conceptocero)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_aporte_concepto
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_aporte)  
		//	    Arguments: as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_ano // Año en curso
		//	  			   as_mes // mes
		//				   as_conceptocero // Conceptos en cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos de aportes al ipasme 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_codconc_ahorro=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO AHORRO IPAS","XXXXXXXXXX","C"));
		$ls_codconc_servicio=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO SERVICIO IPAS","XXXXXXXXXX","C"));
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.anocur='".$as_ano."' ";
		}
		if(!empty($as_mes))
		{
			$ls_criterio = $ls_criterio." AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		}
		$ls_criterio = $ls_criterio." AND (sno_hsalida.codconc='".$ls_codconc_ahorro."' OR sno_hsalida.codconc='".$ls_codconc_servicio."')";
									
		$ls_sql="SELECT sno_hconcepto.codconc, sno_hconcepto.nomcon ".
				"  FROM sno_personal, sno_ipasme_afiliado, sno_hpersonalnomina, sno_hconcepto, sno_hsalida, sno_hperiodo ".
				" WHERE sno_hconcepto.codemp='".$this->ls_codemp."' ".
				$ls_criterio.
				"	AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"   AND sno_hconcepto.codemp = sno_hperiodo.codemp ".
				"   AND sno_hconcepto.anocur = sno_hperiodo.anocur ".
				"   AND sno_hconcepto.codperi = sno_hperiodo.codperi ".
				"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"	AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_ipasme_afiliado.codemp ".
				"   AND sno_personal.codper = sno_ipasme_afiliado.codper ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.nomcon ".
				" ORDER BY sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_aporte_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);			
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_aporte_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_aporte_personal($as_codconc,$as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_conceptocero,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_aporte_personal
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_aporte)  
		//	    Arguments: as_codconc // Código del concepto del que se desea busca el personal
		//	    		   as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_ano // Año en curso
		//	  			   as_mes // mes
		//				   as_conceptocero // Conceptos en cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   en las nóminas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur='".$as_ano."' ";
		}
		if(!empty($as_mes))
		{
			$ls_criterio = $ls_criterio." AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ";
		}
		if(!empty($as_codconc))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc='".$as_codconc."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper,".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR sno_hsalida.tipsal='Q1' OR sno_hsalida.tipsal='D') ".
				$ls_criterio.
				"           AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as personal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_hsalida ".
				"   	  WHERE (sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR sno_hsalida.tipsal='Q2') ".
				$ls_criterio.
				" 			AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   		AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   		AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   		AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   		AND sno_hpersonalnomina.codper = sno_hsalida.codper) as patron ".
				"  FROM sno_personal, sno_ipasme_afiliado, sno_hpersonalnomina, sno_hsalida, sno_hperiodo ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hpersonalnomina.anocur='".$as_ano."' ".
				"   AND sno_hpersonalnomina.staper='1' ".
				$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"	AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"	AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"	AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"	AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"	AND sno_hsalida.codperi  = sno_hperiodo.codperi ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_ipasme_afiliado.codemp ".
				"   AND sno_personal.codper = sno_ipasme_afiliado.codper ".
				"   GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codper, sno_personal.cedper, ".
				"          sno_personal.nacper,sno_ipasme_afiliado.coddep,  sno_hpersonalnomina.codperi".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_aporte_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);			
				$this->DS_detalle->group_by(array('0'=>'cedper'),array('0'=>'personal','1'=>'patron'),'personal');

			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_aporte_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_cobranza_concepto($as_codnomdes,$as_codnomhas,$as_ano,$as_mes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_cobranza_concepto
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_cobranza)  
		//	    Arguments: as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_ano // Año en curso
		//	  			   as_mes // mes
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos de cobranzas al ipasme 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_hip_especial=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO ESPECIAL IPAS","XXXXXXXXXX","C"));
		$ls_hip_ampliacion=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO AMLIACION IPAS","XXXXXXXXXX","C"));
		$ls_hip_construccion=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO CONSTRUCCION IPAS","XXXXXXXXXX","C"));
		$ls_hip_hipoteca=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO HIPOTECA IPAS","XXXXXXXXXX","C"));
		$ls_hip_lph=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO LPH IPAS","XXXXXXXXXX","C"));
		$ls_hip_vivienda=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO HIPOTECARIO VIVIENDA IPAS","XXXXXXXXXX","C"));
		$ls_personal=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO PERSONAL IPAS","XXXXXXXXXX","C"));
		$ls_turistico=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO TURISTICOS IPAS","XXXXXXXXXX","C"));
		$ls_proveeduria=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO PROVEEDURIA IPAS","XXXXXXXXXX","C"));
		$ls_asistencial=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO ASISTENCIALES IPAS","XXXXXXXXXX","C"));
		$ls_vehiculo=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VEHICULOS IPAS","XXXXXXXXXX","C"));
		$ls_comercial=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO COMERCIALES IPAS","XXXXXXXXXX","C"));		
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.anocur='".$as_ano."' ";
		}
		if(!empty($as_mes))
		{
			$ls_criterio = $ls_criterio." AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ";
		}
		$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		$ls_criterio = $ls_criterio." AND (sno_hsalida.codconc='".$ls_hip_especial."' OR sno_hsalida.codconc='".$ls_hip_ampliacion."'".
									"  OR  sno_hsalida.codconc='".$ls_hip_construccion."' OR sno_hsalida.codconc='".$ls_hip_hipoteca."'".
									"  OR  sno_hsalida.codconc='".$ls_hip_lph."' OR sno_hsalida.codconc='".$ls_hip_vivienda."'".
									"  OR  sno_hsalida.codconc='".$ls_personal."' OR sno_hsalida.codconc='".$ls_turistico."'".
									"  OR  sno_hsalida.codconc='".$ls_proveeduria."' OR sno_hsalida.codconc='".$ls_asistencial."'".
									"  OR  sno_hsalida.codconc='".$ls_vehiculo."' OR sno_hsalida.codconc='".$ls_comercial."')";
		$ls_sql="SELECT sno_hconcepto.codconc, sno_hconcepto.nomcon ".
				"  FROM sno_hconcepto, sno_hsalida, sno_hperiodo, sno_ipasme_afiliado ".
				" WHERE sno_hconcepto.codemp='".$this->ls_codemp."' ".
				$ls_criterio.
				"   AND sno_hconcepto.codemp = sno_hperiodo.codemp ".
				"   AND sno_hconcepto.anocur = sno_hperiodo.anocur ".
				"   AND sno_hconcepto.codperi = sno_hperiodo.codperi ".
				"   AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"   AND sno_hsalida.codemp = sno_ipasme_afiliado.codemp ".
				"   AND sno_hsalida.codper = sno_ipasme_afiliado.codper ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.nomcon ".
				" ORDER BY sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_cobranza_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);			
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_cobranza_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_ipasme_cobranza_personal($as_codconc,$as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_ipasme_cobranza_personal
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_cobranza)  
		//	    Arguments: as_codconc // Código del concepto del que se desea busca el personal
		//	    		   as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_ano // Año en curso
		//	  			   as_mes // mes
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el concepto	de cobranza 
		//				   en las nóminas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur='".$as_ano."' ";
		}
		if(!empty($as_mes))
		{
			$ls_criterio = $ls_criterio." AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."' ";
		}
		if(!empty($as_codconc))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc='".$as_codconc."' ";
		}
		$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, SUM(sno_hsalida.valsal) as valsal ".
				"  FROM sno_personal, sno_ipasme_afiliado, sno_hpersonalnomina, sno_hsalida, sno_hperiodo ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hpersonalnomina.anocur='".$as_ano."' ".
				"   AND sno_hpersonalnomina.staper='1' ".
				$ls_criterio.
				"	AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"	AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"	AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"	AND sno_hsalida.codperi  = sno_hperiodo.codperi ".
				"	AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"	AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"	AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_ipasme_afiliado.codemp ".
				"   AND sno_personal.codper = sno_ipasme_afiliado.codper ".
				" GROUP BY sno_personal.cedper,sno_personal.apeper,sno_personal.nomper,sno_personal.codper  ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_ipasme_cobranza_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);			
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_ipasme_cobranza_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_ingreso($as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_ingreso
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 26/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecnacper, sno_personal.fecingper, ".
				"		(SELECT SUM(sueper) ".
				"		   FROM sno_personalnomina, sno_nomina ".
				"		  WHERE (sno_personalnomina.staper = '1' OR sno_personalnomina.staper = '2') ".
				"			AND sno_personalnomina.codemp = sno_personal.codemp ".
				"			AND sno_personalnomina.codper = sno_personal.codper ".
				"			AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"			AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"			AND sno_nomina.espnom = '0' ) as sueldo, ".
				"		(SELECT COUNT(codper) ".
				"		   FROM sno_personalnomina, sno_nomina ".
				"		  WHERE (sno_personalnomina.staper = '1' OR sno_personalnomina.staper = '2') ".
				"			AND sno_personalnomina.codemp = sno_personal.codemp ".
				"			AND sno_personalnomina.codper = sno_personal.codper ".
				"			AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"			AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"			AND sno_nomina.espnom = '0' ) as total ".
				"  FROM sno_personal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_ingreso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_ingreso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_retiro($as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_retiro
		//         Access: public (desde la clase sigesp_snorh_r_sane_retiro)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND estper='3'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT codper, cedper, nomper, apeper, fecegrper, cauegrper ".
				"  FROM sno_personal ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_retiro ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_retiro
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_salario($as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_salario
		//         Access: public (desde la clase sigesp_snorh_rpp_sane_salario)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 28/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		(SELECT SUM(sueper) ".
				"		   FROM sno_personalnomina, sno_nomina ".
				"		  WHERE (sno_personalnomina.staper = '1' OR sno_personalnomina.staper = '2') ".
				"			AND sno_personalnomina.codemp = sno_personal.codemp ".
				"			AND sno_personalnomina.codper = sno_personal.codper ".
				"			AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"			AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"			AND sno_nomina.espnom = '0' ) as sueldo, ".
				"		(SELECT COUNT(codper) ".
				"		   FROM sno_personalnomina, sno_nomina ".
				"		  WHERE (sno_personalnomina.staper = '1' OR sno_personalnomina.staper = '2') ".
				"			AND sno_personalnomina.codemp = sno_personal.codemp ".
				"			AND sno_personalnomina.codper = sno_personal.codper ".
				"			AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"			AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"			AND sno_nomina.espnom = '0' ) as total ".
				"  FROM sno_personal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_salario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_salario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_centromedico($as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_centromedico
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT codper, cedper, nomper, apeper, cenmedper ".
				"  FROM sno_personal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_centromedico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_centromedico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_modificacion($as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_modificacion
		//         Access: public (desde la clase sigesp_snorh_rpp_sane_modificacion)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 29/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT codper, nacper, cedper, nomper, apeper, fecnacper, sexper ".
				"  FROM sno_personal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_modificacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_modificacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_reposos($as_codperdes,$as_codperhas,$ad_fecdes,$ad_fechas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_reposos
		//         Access: public (desde la clase sigesp_snorh_rpp_sane_reposos)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	    		   ad_fecdes // fecha Desde del reposo
		//	  			   ad_fechas // Fecha Hasta del Reposo		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que tiene reposo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 31/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($ad_fecdes))
		{
			$ls_criterio= " AND sno_permiso.feciniper>='".$ad_fecdes."'";
		}
		if(!empty($ad_fechas))
		{
			$ls_criterio= $ls_criterio." AND sno_permiso.feciniper<='".$ad_fechas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_permiso.feciniper, sno_permiso.fecfinper ".
				"  FROM sno_personal, sno_permiso ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"	AND sno_permiso.tipper = 2 ".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_permiso.codemp ".
				"   AND sno_personal.codper = sno_permiso.codper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_reposos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_reposos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_sane_permisos($as_codperdes,$as_codperhas,$ad_fecdes,$ad_fechas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sane_permisos
		//         Access: public (desde la clase sigesp_snorh_rpp_sane_reposos)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	    		   ad_fecdes // fecha Desde del reposo
		//	  			   ad_fechas // Fecha Hasta del Reposo		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que tiene reposo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($ad_fecdes))
		{
			$ls_criterio= " AND sno_permiso.feciniper>='".$ad_fecdes."'";
		}
		if(!empty($ad_fechas))
		{
			$ls_criterio= $ls_criterio." AND sno_permiso.feciniper<='".$ad_fechas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_permiso.feciniper, sno_permiso.fecfinper ".
				"  FROM sno_personal, sno_permiso ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"	AND sno_permiso.remper = 0 ".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_permiso.codemp ".
				"	AND sno_personal.codper = sno_permiso.codper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sane_permisos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_sane_permisos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionislr_personal($as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_conceptocero,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionislr_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_retencion_islr)  
		//	    Arguments: as_codnomdes // Código de Nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de nómina donde se termina de filtrar		  
		//	  			   as_ano // Año 
		//	  			   as_mes // Mes
		//	  			   as_conceptocero // Si el concepto es Cero
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_concepto="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= " AND sno_hperiodo.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio." AND sno_hperiodo.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_concepto= $ls_concepto." AND sno_hsalida.valsal<>0";
		}
		switch($as_orden)
		{
			case "1": // Ordena por código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
				
			case "4": // Ordena por cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personalisr.porisr, ".
		        "	  (SELECT SUM(valsal) FROM sno_hsalida, sno_hconcepto ".
		        "		WHERE (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' ".
		        "		   OR  sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' ".
		        "		   OR  sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
		        "		  AND sno_hconcepto.aplarccon = 1 ".
		        $ls_concepto.
		        "		  AND sno_hsalida.codemp = sno_hconcepto.codemp ".
		        "		  AND sno_hsalida.anocur = sno_hconcepto.anocur ".
		        "		  AND sno_hsalida.codperi = sno_hconcepto.codperi ".
		        "		  AND sno_hsalida.codnom = sno_hconcepto.codnom ".
		        "		  AND sno_hsalida.codconc = sno_hconcepto.codconc ".
		        "		  AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
		        "		  AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
		        "		  AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
		        "		  AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
		        "		  AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
		        "		  AND sno_hsalida.codemp  = sno_hperiodo.codemp ".
		        "		  AND sno_hsalida.anocur  = sno_hperiodo.anocur ".
		        "		  AND sno_hsalida.codperi  = sno_hperiodo.codperi ".
		        "		  AND sno_hsalida.codnom  = sno_hperiodo.codnom ".
		        "		GROUP BY sno_hsalida.codper ) AS arc, ".
		        "	  (SELECT SUM(valsal) FROM sno_hsalida, sno_hconcepto ".
		        "		WHERE (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' ".
		        "		   OR  sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' ".
		        "		   OR  sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
		        $ls_concepto.
		        "		  AND sno_hconcepto.aplisrcon = 1 ".
		        "		  AND sno_hsalida.codemp = sno_hconcepto.codemp ".
		        "		  AND sno_hsalida.anocur = sno_hconcepto.anocur ".
		        "		  AND sno_hsalida.codperi = sno_hconcepto.codperi ".
		        "		  AND sno_hsalida.codnom = sno_hconcepto.codnom ".
		        "		  AND sno_hsalida.codconc = sno_hconcepto.codconc ".
		        "		  AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
		        "		  AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
		        "		  AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
		        "		  AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
		        "		  AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
		        "		  AND sno_hsalida.codemp  = sno_hperiodo.codemp ".
		        "		  AND sno_hsalida.anocur  = sno_hperiodo.anocur ".
		        "		  AND sno_hsalida.codperi  = sno_hperiodo.codperi ".
		        "		  AND sno_hsalida.codnom  = sno_hperiodo.codnom ".
		        "		GROUP BY sno_hsalida.codper ) AS islr ".
		        "  FROM sno_personal, sno_personalisr, sno_hpersonalnomina, sno_hperiodo ".
		        " WHERE sno_personal.codemp = '".$this->ls_codemp."'".
		        "   AND sno_hperiodo.anocur = '".$as_ano."' ".
		        "   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) = '".$as_mes."' ".
		        "   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_ano."' ".
		        "   AND sno_personalisr.codisr = '".$as_mes."' ".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_personalisr.codemp ".
		        "   AND sno_personal.codper = sno_personalisr.codper ".
		        "   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
		        "   AND sno_personal.codper = sno_hpersonalnomina.codper ".
		        "   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
		        "   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
		        "   AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
		        "   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionislr_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
				$this->DS->group_by(array('0'=>'codper'),array('0'=>'arc','1'=>'islr'),'arc');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_retencionislr_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionarc_personal($aa_nominas,$ai_total,$as_ano,$as_orden,$as_codperdes,$as_codperhas,
                                          $as_codubifisdes,$as_codubifishas,$as_coduniadmdes,$as_coduniadmhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionarc_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_retencion_arc)  
		//	    Arguments: aa_nominas // Arreglo de Nóminas por el cual se debe filtrar
		//	  			   ai_total // total de Nómina por el cual se va a filtrar	  
		//	  			   as_ano // Año 
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio=" AND (";
		$ls_orden="";
		$lb_ok=false;
		for($li_i=1;$li_i<=$ai_total;$li_i++)
		{
			$ls_codnom=$aa_nominas[$li_i];
			$ls_criterio= $ls_criterio." sno_hpersonalnomina.codnom='".$ls_codnom."' ";
			if($li_i<$ai_total)
			{
				$ls_criterio= $ls_criterio." OR ";
			}
		}
		$ls_criterio=$ls_criterio." )";
		if (!empty($as_codperdes) && !empty($as_codperhas))
		   {
		     $ls_criterio = $ls_criterio." AND sno_personal.codper BETWEEN '".$as_codperdes."' AND '".$as_codperhas."'";
		   }
		if (!empty($as_codubifisdes) && !empty($as_codubifishas))
		   {
		     $ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codubifis BETWEEN '".$as_codubifisdes."' AND '".$as_codubifishas."'";
		   }
		if (!empty($as_coduniadmdes) && !empty($as_coduniadmhas))
		   {
                     $ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.minorguniadm||sno_hpersonalnomina.ofiuniadm||".
					       "sno_hpersonalnomina.uniuniadm||sno_hpersonalnomina.depuniadm||".
					       "sno_hpersonalnomina.prouniadm BETWEEN '".substr($as_coduniadmdes,0,4).substr($as_coduniadmdes,5,2).substr($as_coduniadmdes,8,2).substr($as_coduniadmdes,11,2).substr($as_coduniadmdes,14,2)."' ".
					       " AND  '".substr($as_coduniadmhas,0,4).substr($as_coduniadmhas,5,2).substr($as_coduniadmhas,8,2).substr($as_coduniadmhas,11,2).substr($as_coduniadmhas,14,2)."' ";
		   }
                   
		switch($as_orden)
		{
			case "1": // Ordena por código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
				
			case "4": // Ordena por cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.nacper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hunidadadmin.desuniadm) AS desuniadm ".
				"  FROM sno_personal, sno_personalisr, sno_hpersonalnomina, sno_hperiodo, sno_ubicacionfisica, sno_hunidadadmin ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."' ".
				"   AND sno_hperiodo.anocur = '".$as_ano."' ".
		        "   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_ano."' ".
				"   ".$ls_criterio." ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_ubicacionfisica.codemp ".
				"   AND sno_hpersonalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND sno_personal.codemp = sno_personalisr.codemp ".
				"   AND sno_personal.codper = sno_personalisr.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
		        " GROUP BY sno_personal.codper, sno_personal.nacper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper ".
				$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionarc_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_retencionarc_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionarc_meses($as_codper,$aa_nominas,$ai_total,$as_ano)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionarc_meses
		//         Access: public (desde la clase sigesp_snorh_rpp_retencion_islr)  
		//	    Arguments: as_codper // Código de Personal
		//	    		   aa_nominas // Arreglo de Nóminas por el cual se debe filtrar
		//	  			   ai_total // total de Nómina por el cual se va a filtrar	  	  
		//	  			   as_ano // Año 
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 04/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio=" AND (";
		$ls_concepto="";
		$ls_orden="";
		$lb_ok=false;
		for($li_i=1;$li_i<=$ai_total;$li_i++)
		{
			$ls_codnom=$aa_nominas[$li_i];
			$ls_criterio= $ls_criterio." sno_hsalida.codnom='".$ls_codnom."' ";
			if($li_i<$ai_total)
			{
				$ls_criterio= $ls_criterio." OR ";
			}
		}
		$ls_criterio=$ls_criterio." )";

		if(!empty($as_conceptocero))
		{
			$ls_concepto= $ls_concepto." AND sno_hsalida.valsal<>0";
		}
		$ls_sql="SELECT sno_personalisr.codper, sno_personalisr.codisr, sno_personalisr.porisr, sno_hsalida.anocur, ".
				"       (SELECT SUM(valsal) ".
				"          FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				"         WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
				"           AND sno_hsalida.codper = '".$as_codper."' ".
				"           AND sno_hperiodo.anocur = '".$as_ano."' ".
		        "   		AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_ano."' ".
				"           AND sno_personalisr.codisr = SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) ".
				"           AND sno_hconcepto.aplarccon = 1 ".
				"           AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' ".
				"            OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' ".
				"            OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
				"           ".$ls_criterio." ".$ls_concepto.
				"           AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"           AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"           AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"           AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"           AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"           AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"           AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"           AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"           AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"           AND sno_hsalida.codemp = sno_personalisr.codemp ".
				"           AND sno_hsalida.codper = sno_personalisr.codper ".
				"         GROUP BY sno_hsalida.codper, sno_hperiodo.anocur, sno_personalisr.codisr) as arc, ".
				"       (SELECT SUM(valsal) ".
				"          FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				"         WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
				"           AND sno_hsalida.codper = '".$as_codper."' ".
				"           AND sno_hperiodo.anocur = '".$as_ano."' ".
		        "   		AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_ano."' ".
				"           AND sno_personalisr.codisr = SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) ".
				"           AND sno_hconcepto.aplisrcon = 1 ".
				"           AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1' ".
				"            OR sno_hsalida.tipsal = 'D' OR sno_hsalida.tipsal = 'V2' OR sno_hsalida.tipsal = 'W2' ".
				"            OR sno_hsalida.tipsal = 'P1' OR sno_hsalida.tipsal = 'V3' OR sno_hsalida.tipsal = 'W3') ".
				"           ".$ls_criterio." ".$ls_concepto.
				"           AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"           AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"           AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"           AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"           AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"           AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"           AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"           AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"           AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"           AND sno_hsalida.codemp = sno_personalisr.codemp ".
				"           AND sno_hsalida.codper = sno_personalisr.codper ".
				"         GROUP BY sno_hsalida.codper, sno_hperiodo.anocur, sno_personalisr.codisr) as islr ".
				"  FROM sno_hsalida, sno_personalisr  ".
				" WHERE sno_hsalida.codper = '".$as_codper."' ".
				"   AND sno_hsalida.codemp = '".$this->ls_codemp."' ".
				"   AND sno_hsalida.anocur = '".$as_ano."' ".
				"   ".$ls_criterio." ".
				"   AND sno_hsalida.codemp = sno_personalisr.codemp ".
				"   AND sno_hsalida.codper = sno_personalisr.codper ".
				"   GROUP BY sno_hsalida.codper, sno_hsalida.anocur, sno_personalisr.codisr, sno_personalisr.porisr, ".
				"			 sno_personalisr.codemp, sno_personalisr.codper ".
				"   ORDER BY sno_personalisr.codisr ";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionarc_meses ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_retencionarc_meses
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionarc_aporte($as_codper,$aa_nominas,$ai_total,$as_ano)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionarc_aporte
		//		   Access: public (desde la clase sigesp_snorh_rpp_aportepatronal)  
		//	    Arguments: as_codper // Código de Personal
		//	    		   aa_nominas // Arreglo de Nóminas por el cual se debe filtrar
		//	  			   ai_total // total de Nómina por el cual se va a filtrar	  	  
		//	    		   as_ano // Año en curso
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado los concepto de tipo aporte patronal 
		//				   en las nóminas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 07/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$lb_valido=true;
		$ls_orden="";
		$ls_criterio=" AND (";
		for($li_i=1;$li_i<=$ai_total;$li_i++)
		{
			$ls_codnom=$aa_nominas[$li_i];
			$ls_criterio= $ls_criterio." sno_hsalida.codnom='".$ls_codnom."' ";
			if($li_i<$ai_total)
			{
				$ls_criterio= $ls_criterio." OR ";
			}
		}
		$ls_criterio=$ls_criterio." )";
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur='".$as_ano."' ";
		}
		$ls_criterio = $ls_criterio." AND sno_hsalida.codper='".$as_codper."' ";
		$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		$ls_sql="SELECT sno_hconcepto.codconc, sno_hconcepto.nomcon, SUM(sno_hsalida.valsal) as monto ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				" WHERE (sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR sno_hsalida.tipsal='Q1')".
				$ls_criterio.
		        "   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_ano."' ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"  	AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc, sno_hconcepto.nomcon ";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionarc_aporte ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_retencionarc_aporte
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_vacaciones_personal($as_codperdes,$as_codperhas,$as_vencida,$as_programada,$as_vacacion,$as_disfrutada,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_vacaciones_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_vacaciones)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	    		   as_vencida // Si la vacación esta vencida
		//	  			   as_programada // Si la vacación esta programada	  
		//	  			   as_vacacion //  Si la vacación esta en vacación
		//	  			   as_disfrutada //  Si la vacación esta disfrutada
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que tiene vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_anterior=false;
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_vencida))
		{
			$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=1 ";
			$lb_anterior=true;
		}
		if(!empty($as_programada))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio.= " OR sno_vacacpersonal.stavac=2 ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=2 ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_vacacion))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_vacacpersonal.stavac=3 ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=3 ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_disfrutada))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_vacacpersonal.stavac=4 ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=4 ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.apeper, sno_personal.nomper,sno_personal.fecingper ".
				"  FROM sno_personal, sno_vacacpersonal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				"	AND sno_personal.codemp = sno_vacacpersonal.codemp ".
				"   AND sno_personal.codper = sno_vacacpersonal.codper ".
				" GROUP BY sno_personal.codper,sno_personal.apeper, sno_personal.nomper,sno_personal.fecingper ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_vacaciones_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_vacaciones_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_vacaciones_vacacion($as_codper,$as_vencida,$as_programada,$as_vacacion,$as_disfrutada,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_vacaciones_vacacion
		//         Access: public (desde la clase sigesp_snorh_rpp_vacaciones)  
		//	    Arguments: as_codper // Código de personal
		//	    		   as_vencida // Si la vacación esta vencida
		//	  			   as_programada // Si la vacación esta programada	  
		//	  			   as_vacacion //  Si la vacación esta en vacación
		//	  			   as_disfrutada //  Si la vacación esta disfrutada
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que tiene vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/08/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_anterior=false;
		if(!empty($as_vencida))
		{
			$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=1 ";
			$lb_anterior=true;
		}
		if(!empty($as_programada))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio.= " OR sno_vacacpersonal.stavac=2 ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=2 ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_vacacion))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_vacacpersonal.stavac=3 ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_vacacpersonal.stavac=3 ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_disfrutada))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_vacacpersonal.stavac=4 ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND ( sno_vacacpersonal.stavac=4 ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$ls_criterio= $ls_criterio." AND sno_personal.estper='1'";
		$ls_sql="SELECT sno_vacacpersonal.codvac, sno_vacacpersonal.fecvenvac, sno_vacacpersonal.fecdisvac, sno_vacacpersonal.fecreivac, ".
				"		sno_vacacpersonal.diavac, sno_vacacpersonal.stavac, sno_vacacpersonal.sueintvac, sno_vacacpersonal.diabonvac, ".
				"		sno_vacacpersonal.obsvac, sno_vacacpersonal.dianorvac, sno_vacacpersonal.diaadivac, sno_vacacpersonal.diaadibon, ".
				"		sno_vacacpersonal.diafer, sno_vacacpersonal.sabdom ".
				"  FROM sno_personal, sno_vacacpersonal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"	AND sno_personal.codper = '".$as_codper."'".
				"   ".$ls_criterio." ".
				"	AND sno_personal.codemp = sno_vacacpersonal.codemp ".
				"   AND sno_personal.codper = sno_vacacpersonal.codper ".
				" ORDER BY sno_vacacpersonal.codvac ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_vacaciones_vacacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_vacaciones_vacacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------------------------------------
	function uf_cestaticket_personal($as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_codperi,$as_codconcdes,$as_codconchas,
									 $as_conceptocero,$as_subnomdes,$as_subnomhas,$as_orden,$rs_data=array(),$record_set='no')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cestaticket_personal
		//		   Access: public (desde la clase sigesp_snorh_rpp_cestaticket)  
		//	    Arguments: as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_ano // Año en curso
		//	  			   as_mes // mes
		//	    		   as_codperi // Código del periodo
		//	    		   as_codconcdes // Código del concepto Desde del que se desea busca el personal
		//	    		   as_codconchas // Código del concepto Hasta del que se desea busca el personal
		//				   as_conceptocero // Conceptos en cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   en las nóminas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur='".$as_ano."' ";
		}
		if(!empty($as_codperi))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codperi='".$as_codperi."' ";
		}
		if(!empty($as_codconcdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc>='".$as_codconcdes."' ";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc<='".$as_codconchas."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}		
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.cedper ";
				break;

			case "51": // Ordena por Unidad Administrativa y Código de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.codper ";
				break;
				
			case "52": // Ordena por Unidad Administrativa y Apellido de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.apeper ";
				break;
				
			case "53": // Ordena por Unidad Administrativa y Nombre de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.nomper ";
				break;
				
			case "54": // Ordena por Unidad Administrativa y Cédula de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper,sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, sno_hsalida.tipsal, ".
		        "       sno_hsalida.valsal, ".
				"		sno_cestaticunidadadm.est1cestic , sno_cestaticunidadadm.est2cestic, sno_cestaticket.moncestic, ".
				"		sno_hsalida.codconc, sno_hconcepto.nomcon, sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_cestaticket.mondesdia, ".
				"       sno_hpersonalnomina.uniuniadm, sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_hunidadadmin.desuniadm, ".
				"       (SELECT srh_departamento.dendep FROM srh_departamento                 ".
				"         WHERE srh_departamento.codemp=sno_hpersonalnomina.codemp             ".
				"           AND srh_departamento.coddep=sno_hpersonalnomina.coddep) AS dendep, ".
				"       (SELECT dentippersss FROM sno_tipopersonalsss WHERE codemp=sno_personal.codemp AND codtippersss=sno_personal.codtippersss) AS tipopersonal ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hnomina, sno_hconcepto, sno_cestaticunidadadm, sno_cestaticket, ".
				"		sno_hunidadadmin ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hpersonalnomina.anocur='".$as_ano."' ".
				"   AND sno_hpersonalnomina.codperi='".$as_codperi."' ".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
				"   AND sno_hnomina.espnom = '1' ".
				"   AND sno_hnomina.ctnom = '1' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_cestaticket.moncestic <> 0 ".
				$ls_criterio.
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_cestaticunidadadm.codemp ".
				"   AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"   AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hnomina.ctmetnom = sno_cestaticket.codcestic ".
				"   AND sno_cestaticket.codemp = sno_cestaticunidadadm.codemp ".
				"   AND sno_cestaticket.codcestic = sno_cestaticunidadadm.codcestic ".
				"   AND sno_hpersonalnomina.codemp = sno_cestaticunidadadm.codemp ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_cestaticunidadadm.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_cestaticunidadadm.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_cestaticunidadadm.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_cestaticunidadadm.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_cestaticunidadadm.prouniadm ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   ".$ls_orden;
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cestaticket_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		
		else if($record_set != 'si')
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);			
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_cestaticket_personal
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_familiar_personal($as_codperdes,$as_codperhas,$as_conyuge,$as_progenitor,$as_hijo,$as_hermano,$as_masculino,
								  $as_femenino,$ai_edaddesde,$ai_edadhasta,$as_codnomdes,$as_codnomhas,$as_activo,$as_egresado,
								  $as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,$as_personalmasculino,
								  $as_personalfemenino,$as_beca,$as_nivaca,$ai_juguete,$as_hcm,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_familiar_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_familiar)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	    		   as_conyuge // Si el familiar es Conyuge
		//	  			   as_progenitor // Si el familiar es Progenitor
		//	  			   as_hijo //  Si el familiar es Hijo
		//	  			   as_hermano //  Si el familiar es Hermano
		//	  			   as_masculino //  Si el familiar es Masculino
		//	  			   as_femenino //  Si el familiar es Femenino
		//	  			   ai_edaddesde //  Edad Desde
		//	  			   ai_edadhasta //  Edad Hasta
		//	  			   as_codnomdes //  Código de Nómina Desde
		//	  			   as_codnomhas //  Código de Nómina Hasta
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_personalmasculino //  Si el Personal es Masculino
		//	  			   as_personalfemenino //  Si el Personal es Femenino
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que tiene Familiares
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		$lb_anterior=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
			if(!empty($as_codperhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
			}
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR sno_personal.estper='3' ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		if(!empty($as_conyuge))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='C' ";
			$lb_anterior=true;
		}
		if(!empty($as_progenitor))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio.= " OR sno_familiar.nexfam='P' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='P' ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_hijo))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_familiar.nexfam='H' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='H' ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_hermano))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_familiar.nexfam='E' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='E' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.sexfam='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_familiar.sexfam='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.sexfam='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		$lb_anterior=false;
		if(!empty($as_personalmasculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_personalfemenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_beca))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.estbec='1' ";
			$lb_anterior=true;
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_hcm))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.hcmfam='1' ";
			$lb_anterior=true;
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_nivaca))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.nivaca='".$as_nivaca."' ";
			$lb_anterior=true;
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}			
		if(!empty($ai_edadhasta))
		{
			if($ai_edaddesde==$ai_edadhasta)
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$li_resta=$ai_edadhasta+1;
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$li_resta year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edaddesde year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam<'".$ld_fecha."' ";
			}
			else
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edadhasta year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
			}
		}
		if(!empty($ai_edaddesde))
		{
			if($ai_edaddesde!=$ai_edadhasta)
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edaddesde year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam<='".$ld_fecha."' ";
			}
		}
		if(!empty($ai_juguete))
		{
			$ls_criterio= $ls_criterio. " AND sno_familiar.estbonjug='".$ai_juguete."' ";
			
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;

			case "3": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "4": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{ // si busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, MAX(sno_personal.fecnacper) AS fecnacper  ".
					"  FROM sno_personal, sno_familiar, sno_personalnomina ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio." ".
					"	AND sno_personal.codemp = sno_personalnomina.codemp ".
					"   AND sno_personal.codper = sno_personalnomina.codper ".
					"	AND sno_personal.codemp = sno_familiar.codemp ".
					"   AND sno_personal.codper = sno_familiar.codper ".
					" GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper".
					"   ".$ls_orden;
		}
		else
		{ // si no busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, MAX(sno_personal.fecnacper) AS fecnacper  ".
					"  FROM sno_personal, sno_familiar ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio." ".
					"	AND sno_personal.codemp = sno_familiar.codemp ".
					"   AND sno_personal.codper = sno_familiar.codper ".
					" GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper ".
					"   ".$ls_orden;
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_familiar_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_familiar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_familiar_familiar($as_codper,$as_conyuge,$as_progenitor,$as_hijo,$as_hermano,$as_masculino,$as_femenino,$ai_edaddesde,$ai_edadhasta,$as_beca,$as_nivaca,$ai_juguete)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_familiar_familiar
		//         Access: public (desde la clase sigesp_snorh_rpp_vacaciones)  
		//	    Arguments: as_codper // Código de personal
		//	    		   as_conyuge // Si el familiar es Conyuge
		//	  			   as_progenitor // Si el familiar es Progenitor
		//	  			   as_hijo //  Si el familiar es Hijo
		//	  			   as_hermano //  Si el familiar es Hermano
		//	  			   as_masculino //  Si el familiar es Masculino
		//	  			   as_femenino //  Si el familiar es Femenino
		//	  			   ai_edaddesde //  Edad Desde
		//	  			   ai_edadhasta //  Edad Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los familiares del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$lb_anterior=false;
		if(!empty($as_conyuge))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='C' ";
			$lb_anterior=true;
		}
		if(!empty($as_progenitor))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio.= " OR sno_familiar.nexfam='P' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='P' ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_hijo))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_familiar.nexfam='H' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='H' ";
				$lb_anterior=true;
			}
		}
		if(!empty($as_hermano))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_familiar.nexfam='E' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.nexfam='E' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.sexfam='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_familiar.sexfam='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_familiar.sexfam='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_beca))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.estbec='1' ";
			$lb_anterior=true;
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_nivaca))
		{
			$ls_criterio= $ls_criterio. " AND (sno_familiar.nivaca='".$as_nivaca."' ";
			$lb_anterior=true;
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}		
		if(!empty($ai_edadhasta))
		{
			if($ai_edaddesde==$ai_edadhasta)
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$li_resta=$ai_edadhasta+1;
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$li_resta year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edaddesde year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam<'".$ld_fecha."' ";
			}
			else
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edadhasta year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
			}
		}
		if(!empty($ai_edaddesde))
		{
			if($ai_edaddesde!=$ai_edadhasta)
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edaddesde year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam<='".$ld_fecha."' ";
			}
		}
		if(!empty($ai_juguete))
		{
			$ls_criterio= $ls_criterio. " AND sno_familiar.estbonjug='".$ai_juguete."' ";
			
		}		
		$ls_sql="SELECT cedfam, nomfam, apefam, sexfam, fecnacfam, nexfam, estfam, cedula, estbec, nivaca ".
				"  FROM sno_familiar ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."'".
				"   ".$ls_criterio." ".
				" ORDER BY apefam,nomfam,nexfam  ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_familiar_familiar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_familiar_familiar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_consolidadoconcepto_conceptos($as_codnomdes,$as_codnomhas,$as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,
											  $as_tipconc,$as_conceptocero,$as_personaldes,$as_personalhas,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_consolidadoconcepto_conceptos
		//         Access: public (desde la clase sigesp_snorh_rpp_conceptos)  
		//	    Arguments: as_codnomdes // Código de la Nómina donde se empieza a filtrar
		//				   as_codnomhas // Código de la Nómina donde se termina de filtrar
		//	    		   as_codconcdes // Código del concepto donde se empieza a filtrar
		//				   as_codconchas // Código del concepto donde se termina de filtrar
		//				   as_codperdes // Código del período donde se empieza a filtrar
		//	  			   as_codperhas // Código del período donde se termina de filtrar		  
		//	  			   as_tipconc // Tipo de Concepto
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//				   as_personaldes // Código del personal donde se empieza a filtrar
		//	  			   as_personalhas // Código del personal donde se termina de filtrar		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos que se calcularon en la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_codconcdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codperi<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_personaldes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper>='".$as_personaldes."'";
		}
		if(!empty($as_personalhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper<='".$as_personalhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_tipconc))
		{
			$ls_criterio = $ls_criterio."   AND sno_hconcepto.sigcon='".$as_tipconc."' ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, sno_hconcepto.nomcon,  sno_hconcepto.sigcon ".
				"  FROM sno_hconcepto, sno_hsalida, sno_hpersonalnomina ".
				" WHERE sno_hconcepto.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hconcepto.sigcon, sno_hconcepto.codconc, sno_hconcepto.nomcon ".
				" ORDER BY sno_hconcepto.sigcon, sno_hconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_consolidadoconcepto_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_consolidadoconcepto_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_personas_conceptos($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,
											 $as_conceptocero,$as_personaldes,$as_personalhas,$as_subnomdes,$as_subnomhas,$as_orden,$as_anocurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_personas_conceptos
		//         Access: public (desde la clase sigesp_snorh_rpp_conceptos)  
		//	    Arguments: as_codnomdes // Código de la Nómina donde se empieza a filtrar
		//				   as_codnomhas // Código de la Nómina donde se termina de filtrar
		//	    		   as_codconc // Código del concepto
		//				   as_codconchas // Código del concepto donde se termina de filtrar
		//				   as_codperdes // Código del período donde se empieza a filtrar
		//	  			   as_codperhas // Código del período donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//				   as_personaldes // Código del personal donde se empieza a filtrar
		//	  			   as_personalhas // Código del personal donde se termina de filtrar		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos que se calcularon en la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_personaldes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper>='".$as_personaldes."'";
		}
		if(!empty($as_personalhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper<='".$as_personalhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_criterio = $ls_criterio."   AND sno_hsalida.tipsal<>'P2' ";
		$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.anocur='".$as_anocurper."'";
		$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.staper<>'3'";
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, SUM(sno_hsalida.valsal) AS total, ".
				"		sno_hpersonalnomina.codnom ".
				"  FROM sno_hpersonalnomina, sno_personal, sno_hsalida ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hpersonalnomina.codnom, sno_hpersonalnomina.codper, sno_personal.codper, sno_personal.cedper, ".
				"		   sno_personal.nomper, sno_personal.apeper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_personas_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detper->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_personas_conceptos

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_consolidadoconcepto_personal($as_codnomdes,$as_codnomhas,$as_codconc,$as_codperdes,$as_codperhas,
											 $as_conceptocero,$as_personaldes,$as_personalhas,$as_subnomdes,$as_subnomhas,$as_orden,$as_anocurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_consolidadoconcepto_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_conceptos)  
		//	    Arguments: as_codnomdes // Código de la Nómina donde se empieza a filtrar
		//				   as_codnomhas // Código de la Nómina donde se termina de filtrar
		//	    		   as_codconc // Código del concepto
		//				   as_codconchas // Código del concepto donde se termina de filtrar
		//				   as_codperdes // Código del período donde se empieza a filtrar
		//	  			   as_codperhas // Código del período donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//				   as_personaldes // Código del personal donde se empieza a filtrar
		//	  			   as_personalhas // Código del personal donde se termina de filtrar		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos que se calcularon en la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_codconc))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codconc='".$as_codconc."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_personaldes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper>='".$as_personaldes."'";
		}
		if(!empty($as_personalhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper<='".$as_personalhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_criterio = $ls_criterio."   AND sno_hsalida.tipsal<>'P2' ";
		$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.anocur='".$as_anocurper."'";
		$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.staper<>'3'";
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.cedper ";
				break;
		}		
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, SUM(sno_hsalida.valsal) AS total, ".
				"		sno_hpersonalnomina.codnom ".
				"  FROM sno_hpersonalnomina, sno_personal, sno_hsalida ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_hpersonalnomina.codnom, sno_hpersonalnomina.codper, sno_personal.codper, sno_personal.cedper, ".
				"		   sno_personal.nomper, sno_personal.apeper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_consolidadoconcepto_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_consolidadoconcepto_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_consolidadoconcepto_personal_unidad($as_codnomdes,$as_codnomhas,$as_codconc,$as_codperdes,$as_codperhas,$as_codper,$as_anocurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_consolidadoconcepto_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_conceptos)  
		//	    Arguments: as_codnomdes // Código de la Nómina donde se empieza a filtrar
		//				   as_codnomhas // Código de la Nómina donde se termina de filtrar
		//	    		   as_codconc // Código del concepto
		//				   as_codconchas // Código del concepto donde se termina de filtrar
		//				   as_codperdes // Código del período donde se empieza a filtrar
		//	  			   as_codperhas // Código del período donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//				   as_personaldes // Código del personal donde se empieza a filtrar
		//	  			   as_personalhas // Código del personal donde se termina de filtrar		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos que se calcularon en la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$as_unidad=0;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_codconc))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codconc='".$as_codconc."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codperi<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio."   AND sno_hconstantepersonal.codper='".$as_codper."'";
		$ls_criterio = $ls_criterio."   AND sno_hconcepto.anocur='".$as_anocurper."'";

		
		$ls_sql="SELECT SUM(moncon) AS moncon ".
				"  FROM sno_hconstantepersonal, sno_hconcepto ".
				" WHERE sno_hconcepto.repconsunicon='1' ".
				"    ".$ls_criterio.
				"	AND sno_hconstantepersonal.codemp = sno_hconcepto.codemp ".
				"	AND sno_hconstantepersonal.codnom = sno_hconcepto.codnom ".
				"	AND sno_hconstantepersonal.anocur = sno_hconcepto.anocur ".
				"	AND sno_hconstantepersonal.codperi = sno_hconcepto.codperi ".
				"	AND sno_hconstantepersonal.codcons = sno_hconcepto.consunicon ".
				" GROUP BY sno_hconstantepersonal.codemp, sno_hconstantepersonal.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_consolidadoconcepto_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_unidad=$rs_data->fields['moncon'];		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $as_unidad;
	}// end function uf_consolidadoconcepto_personal
	//-----------------------------------------------------------------------------------------------------------------------------------


	function uf_consolidadoconcepto_personal_detallado($as_codnomdes,$as_codnomhas,$as_codconc,$as_codperdes,$as_codperhas,
											 $as_conceptocero,$as_personaldes,$as_personalhas,$as_subnomdes,$as_subnomhas,$as_orden,$as_anocurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_consolidadoconcepto_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_conceptos)  
		//	    Arguments: as_codnomdes // Código de la Nómina donde se empieza a filtrar
		//				   as_codnomhas // Código de la Nómina donde se termina de filtrar
		//	    		   as_codconc // Código del concepto
		//				   as_codconchas // Código del concepto donde se termina de filtrar
		//				   as_codperdes // Código del período donde se empieza a filtrar
		//	  			   as_codperhas // Código del período donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//				   as_personaldes // Código del personal donde se empieza a filtrar
		//	  			   as_personalhas // Código del personal donde se termina de filtrar		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos que se calcularon en la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/10/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_codconc))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codconc='".$as_codconc."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_personaldes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper>='".$as_personaldes."'";
		}
		if(!empty($as_personalhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codper<='".$as_personalhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_criterio = $ls_criterio."   AND sno_hsalida.tipsal<>'P2' ";
		$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.anocur='".$as_anocurper."'";
		$ls_criterio = $ls_criterio."   AND sno_hpersonalnomina.staper<>'3'";
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.codper, sno_hsalida.codperi ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.apeper, sno_hsalida.codperi ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.nomper, sno_hsalida.codperi ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.codnom, sno_personal.cedper, sno_hsalida.codperi ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_hsalida.valsal,sno_hsalida.codperi, ".
				"		sno_hpersonalnomina.codnom, sno_hperiodo.fecdesper, sno_hperiodo.fechasper ".
				"  FROM sno_hpersonalnomina, sno_personal, sno_hsalida, sno_hperiodo ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"	AND sno_hperiodo.codnom = sno_hsalida.codnom ".
				"	AND sno_hperiodo.anocur = sno_hsalida.anocur ".
				"	AND sno_hperiodo.codperi = sno_hsalida.codperi ".  
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_consolidadoconcepto_personal_detallado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_consolidadoconcepto_personal
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadocumpleanos($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_mes,$as_activo,$as_egresado,
								  $as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,$as_nomnormal,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadocumpleanos
		//         Access: public (desde la clase sigesp_snorh_rpp_listadocumpleano)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_mes // Mes de Cumpleaños
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_nomnormal // Filtrar solo por nóminas normales
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/12/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_nomnormal))
			{
				$ls_criterio= $ls_criterio." AND sno_nomina.espnom='0'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_mes))
		{
			$ls_criterio= $ls_criterio." AND substr(cast(sno_personal.fecnacper as char(10)),6,2)='".$as_mes."'";
		}
		$lb_ok=false;
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR sno_personal.estper='3' ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3'  ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				$orden="sno_personal.codper";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				$orden="sno_personal.apeper";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				$orden="sno_personal.nomper";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{ // si busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.fecnacper) AS fecnacper, MAX(sno_unidadadmin.desuniadm) AS desuniadm ".
					"  FROM sno_personal, sno_personalnomina, sno_nomina, sno_unidadadmin ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
					"	AND sno_personalnomina.codnom = sno_nomina.codnom  ".
					"	AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
					"	AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm  ".
					"	AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm  ".
					"	AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm  ".
					"	AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
					" GROUP BY sno_personal.codper, ".$orden." ".// se comento en el group by desuniadm
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.fecnacper) AS fecnacper, (SELECT MAX(sno_unidadadmin.desuniadm)
						FROM sno_unidadadmin, sno_personalnomina, sno_nomina 
						WHERE sno_personal.codper = sno_personalnomina.codper
						AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm
						AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm
						AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm
						AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm
						AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm
						AND sno_nomina.espnom='0') AS desuniadm ".
					"  FROM sno_personal".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					" GROUP BY sno_personal.codper, ".$orden." ".// se comento en el group by desuniadm
					$ls_orden;

		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadocumpleanos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadocumpleanos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_fichapersonal_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
									   $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
									   $as_masculino,$as_femenino,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_fichapersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_fichapersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		$ls_campos="       (SELECT denasicar FROM sno_asignacioncargo ".
				   "   	     WHERE sno_personalnomina.codasicar<>'0000000000' ".
				   "           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
				   "		   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
				   "           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) as denasicar, ".
				   "       (SELECT descar FROM sno_cargo ".
				   "   	     WHERE sno_personalnomina.codcar<>'0000000000' ".
				   "		   AND sno_personalnomina.codemp = sno_cargo.codemp ".
				   "		   AND sno_personalnomina.codnom = sno_cargo.codnom ".
				   "           AND sno_personalnomina.codcar = sno_cargo.codcar) as descar ";
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"       sno_personal.fecingper, sno_personal.dirper, sno_personal.coreleper, sno_personal.sexper, ".
				"		sno_personal.telhabper, sno_personal.telmovper, sno_personal.fecnacper, sno_personal.estper, ".
				"		sno_personal.nacper, sno_personal.edocivper, sno_personal.numhijper, sno_personal.fecegrper,	".
				"		sno_profesion.despro, sno_unidadadmin.desuniadm, sigesp_estados.desest, sigesp_municipio.denmun, ".$ls_campos.
				"  FROM sno_personal, sno_profesion, sno_personalnomina, sno_nomina, sno_unidadadmin, sigesp_estados, sigesp_municipio ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio.
				"   AND sno_personal.codemp = sno_profesion.codemp ".
				"	AND sno_personal.codpro = sno_profesion.codpro ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"	AND sno_personalnomina.codnom = sno_nomina.codnom  ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"	AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
				"	AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm  ".
				"	AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm  ".
				"	AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm  ".
				"	AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
				"   AND sno_personal.codpai = sigesp_estados.codpai ".
				"	AND sno_personal.codest = sigesp_estados.codest ".
				"   AND sno_personal.codpai = sigesp_municipio.codpai ".
				"	AND sno_personal.codest = sigesp_municipio.codest ".
				"	AND sno_personal.codmun = sigesp_municipio.codmun ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_fichapersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_fichapersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_credencialespersonal_personal($as_codperdes,$as_codperhas,$as_activo,$as_egresado,$as_causaegreso,$as_masculino,
											  $as_femenino,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_credencialespersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_credencialespersonal)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 08/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_personal.estper, sno_personal.fecnacper, sno_personal.turper, sno_personal.horper, sno_personal.nivacaper, ".
				"		sno_personal.anoservpreper, sno_profesion.despro  ".
				"  FROM sno_personal, sno_profesion" .
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio.
				"   AND sno_personal.codemp = sno_profesion.codemp ".
				"	AND sno_personal.codpro = sno_profesion.codpro ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_credencialespersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_credencialespersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_credencialespersonal_educacionformal($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_credencialespersonal_educacionformal
		//         Access: public (desde la clase sigesp_snorh_rpp_credencialespersonal)  
		//	    Arguments: as_codper  // Código de personal 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la educación formal del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 09/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codestrea, tipestrea, insestrea, titestrea, feciniact, fecfinact, aprestrea, anoaprestrea ".
				"  FROM sno_estudiorealizado ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				"   AND (tipestrea='0' OR tipestrea='1' OR tipestrea='2' OR tipestrea='3' OR tipestrea='4' ".
				"	 OR tipestrea='5' OR tipestrea='6' OR tipestrea='7')".
				" ORDER BY tipestrea, feciniact ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_credencialespersonal_educacionformal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_credencialespersonal_educacionformal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_credencialespersonal_educacioninformal($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_credencialespersonal_educacioninformal
		//         Access: public (desde la clase sigesp_snorh_rpp_credencialespersonal)  
		//	    Arguments: as_codper  // Código de personal 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la educación informal del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codestrea, tipestrea, insestrea, titestrea, feciniact, fecfinact, aprestrea, horestrea ".
				"  FROM sno_estudiorealizado ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				"   AND (tipestrea='8' OR tipestrea='9')".
				" ORDER BY tipestrea, feciniact ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_credencialespersonal_educacioninformal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_credencialespersonal_educacioninformal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_credencialespersonal_trabajosanterior($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_credencialespersonal_trabajosanterior
		//         Access: public (desde la clase sigesp_snorh_rpp_credencialespersonal)  
		//	    Arguments: as_codper  // Código de personal 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los trabajos anteriores del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codtraant, emptraant, ultcartraant, fecingtraant, fecrettraant, emppubtraant, anolab, meslab, dialab ".
				"  FROM sno_trabajoanterior ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				" ORDER BY fecingtraant ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_credencialespersonal_trabajosanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_credencialespersonal_trabajosanterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_credencialespersonal_cargafamiliar($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_credencialespersonal_cargafamiliar
		//         Access: public (desde la clase sigesp_snorh_rpp_credencialespersonal)  
		//	    Arguments: as_codper  // Código de personal 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los hijos menores de 18 años del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_hoy=date('Y')."-".date('m')."-".date('d');
		$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -18 year"));
		$ls_sql="SELECT cedfam, nomfam, apefam, sexfam, fecnacfam, nexfam ".
				"  FROM sno_familiar ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				"	AND nexfam = 'H' ".
				"   AND fecnacfam >= '".$ld_fecha."' ".
				" ORDER BY fecnacfam ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_credencialespersonal_trabajosanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_credencialespersonal_cargafamiliar
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_banco($as_codban,$as_suspendidos,$as_sc_cuenta,$as_ctaban,$as_codnomdes,$as_codnomhas,$as_codperdes,
								   $as_codperhas,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_banco
		//		   Access: public (desde la clase sigesp_snorh_rpp_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_sc_cuenta // cuenta contable del banco
		//	    		   as_ctaban // cuenta del banco
		//	    		   as_codnomdes // Código de Nómina Desde
		//	    		   as_codnomhas // Código de Nómina Hasta
		//	    		   as_codperdes // Código de Periodo Desde
		//	    		   as_codperhas // Código de Periodo Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del banco seleccionado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 18/05/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi>='".$as_codperdes."' ";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi<='".$as_codperhas."' ";
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		$ls_sql="SELECT scb_banco.codban, scb_banco.nomban ".
				"  FROM sno_hpersonalnomina, sno_hresumen, scb_banco, sss_permisos_internos  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.anocur='".substr($_SESSION["la_empresa"]["periodo"],0,4)."' ".
				"   AND (sno_hpersonalnomina.pagbanper=1 OR sno_hpersonalnomina.pagbanper=1) ".
				"   AND sno_hpersonalnomina.pagefeper=0 ".
				"   AND sno_hresumen.monnetres > 0 ".
				$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"   AND sno_hpersonalnomina.codemp = scb_banco.codemp ".
				"   AND sno_hpersonalnomina.codban = scb_banco.codban ".
                                "    AND sss_permisos_internos.codsis='SNO'".
                                "    AND sss_permisos_internos.enabled=1".
                                "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
                                "    AND sno_hresumen.codemp = sss_permisos_internos.codemp ".
                                "    AND sno_hresumen.codnom = sss_permisos_internos.codintper ".
				" GROUP BY scb_banco.codban, scb_banco.nomban ".
				" ORDER BY scb_banco.nomban ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);	
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadobanco_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_personal($as_codban,$as_suspendidos,$as_tipcueban,$as_quincena,$as_codnomdes,$as_codnomhas,
								      $as_codperdes,$as_codperhas,$as_subnomdes,$as_subnomhas,$as_orden,$lb_pagotrban=false)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	    		   as_tipcueban // tipo de cuenta bancaria (Ahorro,  Corriente, Activos liquidos)
		//	  			   as_quincena // Quincena para el cual se quiere filtrar
		//	    		   as_codnomdes // Código de Nómina Desde
		//	    		   as_codnomhas // Código de Nómina Hasta
		//	    		   as_codperdes // Código de Periodo Desde
		//	    		   as_codperhas // Código de Periodo Hasta
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="SUM(sno_hresumen.priquires) as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="SUM(sno_hresumen.segquires) as monnetres";
				break;

			default: // Mes Completo
				$ls_monto="SUM(sno_hresumen.monnetres) as monnetres";
				break;
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi>='".$as_codperdes."' ";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi<='".$as_codperhas."' ";
		}
		if(($lb_pagotrban=="false")||($lb_pagotrban==false))
		{
			if(!empty($as_codban))
			{
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
			}
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		switch($as_tipcueban)
		{
			case "A": // Cuenta de Ahorro
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.tipcuebanper='A' ";
				break;
				
			case "C": // Cuenta corriente
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.tipcuebanper='C' ";
				break;

			case "L": // Cuenta Activos Líquidos
				$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.tipcuebanper='L' ";
				break;
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql=" SELECT sno_personal.codper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.nomper) AS nomper,MAX(sno_personal.apeper) AS apeper,  ".
			    "		 MAX(sno_hpersonalnomina.codcueban) AS codcueban, ".$ls_monto." ,MAX(sno_hnomina.divcon) as divcon, ".
				"		 MAX(sno_hresumen.priquires) AS priquires, MAX(sno_hresumen.segquires) AS segquires, ".
                "        SUM(sno_hresumen.monnetres) as montomen, ".$as_quincena." as quincena, MAX(sno_hnomina.codnom) AS codnom, ".
				"		 MAX(sno_hnomina.desnom) AS desnom ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen, sno_hnomina, sss_permisos_internos  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"	AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."' ".
				"   AND sno_hresumen.codperi>='".$as_codperdes."' ".
				"   AND sno_hresumen.codperi<='".$as_codperhas."' ".
				"   AND sno_hresumen.monnetres > 0 ".
				$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"	AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"	AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
                                "    AND sss_permisos_internos.codsis='SNO'".
                                "    AND sss_permisos_internos.enabled=1".
                                "    AND sss_permisos_internos.codusu='".$_SESSION["la_logusr"]."'".
                                "    AND sno_hresumen.codemp = sss_permisos_internos.codemp ".
                                "    AND sno_hresumen.codnom = sss_permisos_internos.codintper ".
				" GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobanco_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadobanco_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobancotaquilla_personal($as_codban,$as_suspendidos,$as_quincena,$as_codnomdes,$as_codnomhas,
								      		  $as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobancotaquilla_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // Código del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ó solo los activos
		//	  			   as_quincena // Quincena para el cual se quiere filtrar
		//	    		   as_codnomdes // Código de Nómina Desde
		//	    		   as_codnomhas // Código de Nómina Hasta
		//	    		   as_codperdes // Código de Periodo Desde
		//	    		   as_codperhas // Código de Periodo Hasta
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/09/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="SUM(sno_hresumen.priquires) as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="SUM(sno_hresumen.segquires) as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="SUM(sno_hresumen.monnetres) as monnetres";
				break;
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi>='".$as_codperdes."' ";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi<='".$as_codperhas."' ";
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".$ls_monto.", ".
				"       MAX(sno_hpersonalnomina.codcueban) AS codcueban, sno_personal.codper ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hresumen  ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.anocur='".substr($_SESSION["la_empresa"]["periodo"],0,4)."' ".
				"   AND sno_hpersonalnomina.pagbanper=0 ".
				"   AND sno_hpersonalnomina.pagefeper=0 ".
				"   AND sno_hpersonalnomina.pagtaqper=1 ".
				"   AND sno_hresumen.monnetres > 0 ".
				$ls_criterio.
				"	AND sno_hpersonalnomina.codemp = sno_hresumen.codemp ".
				"	AND sno_hpersonalnomina.anocur = sno_hresumen.anocur ".
				"	AND sno_hpersonalnomina.codperi = sno_hresumen.codperi ".
				"   AND sno_hpersonalnomina.codnom = sno_hresumen.codnom ".
				"   AND sno_hpersonalnomina.codper = sno_hresumen.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				" GROUP BY sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadobancotaquilla_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadobancotaquilla_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_antiguedadpersonal_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
										    $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										    $as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_antiguedadpersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_antiguedadpersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT DISTINCT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.fecingper, sno_personal.fecingadmpubper, sno_nomina.racnom, ".
				"       (SELECT denasicar FROM sno_asignacioncargo ".
				"   	  WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"           AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
				"		    AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
				"           AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) as denasicar, ".
				"       (SELECT descar FROM sno_cargo ".
				"   	  WHERE sno_personalnomina.codemp='".$this->ls_codemp."' ".
				"		    AND sno_personalnomina.codemp = sno_cargo.codemp ".
				"		    AND sno_personalnomina.codnom = sno_cargo.codnom ".
				"           AND sno_personalnomina.codcar = sno_cargo.codcar) as descar ".
				"  FROM sno_personal, sno_personalnomina, sno_nomina ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio.
				"	AND sno_nomina.espnom='0' ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"	AND sno_personalnomina.codnom = sno_nomina.codnom ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_antiguedadpersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_antiguedadpersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_datosjubilado_personal($as_codperdes,$as_codperhas,$as_activo,$as_egresado,$as_causaegreso,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_datosjubilado_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_antiguedadpersonal)  
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.carantper, sno_personal.gerantper ". 
				"  FROM sno_personal ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."' ". 
				"   AND sno_personal.codtippersss IN ('0000010','0000014') ".
				$ls_criterio.
				$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_datosjubilado_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_datosjubilado_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_datosjubilado_beneficiario($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_datosjubilado_beneficiario
		//         Access: public (desde la clase sigesp_snorh_rpp_antiguedadpersonal)  
		//	    Arguments: as_codper // Código de personal donde se empieza a filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT sno_beneficiario.cedben, sno_beneficiario.nomben, sno_beneficiario.apeben, sno_beneficiario.nexben ".
				"  FROM sno_beneficiario ".
				" WHERE sno_beneficiario.codemp = '".$this->ls_codemp."' ". 
				"   AND sno_beneficiario.codper = '".$as_codper."' ".
				" ORDER BY sno_beneficiario.cedben ";
		$this->rs_data_dt=$this->io_sql->select($ls_sql);
		if($this->rs_data_dt===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_datosjubilado_beneficiario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_datosjubilado_beneficiario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_datosjubilado_guarderias($as_codperdes,$as_codperhas,$as_activo,$as_egresado,
										    $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										    $as_orden,$as_nomdes,$as_nomhas,$as_anocur,$as_codperi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_antiguedadpersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_antiguedadpersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if (!empty($as_nomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codnom>='".$as_nomdes."'";
		}
		if (!empty($as_nomhas))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codnom<='".$as_nomhas."'";
		}
		if (!empty($as_anocur))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.anocur='".$as_anocur."'";
		}
		if (!empty($as_codperi))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codperi='".$as_codperi."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT DISTINCT sno_personal.codper,sno_personal.fecingper, sno_guarderias.nomper, ".
				" sno_guarderias.codguar, sno_guarderias.monto,sno_guarderias.cedbene, sno_guarderias.nombene ".
//				" ,(SELECT denasicar FROM sno_asignacioncargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' AND sno_personalnomina.codemp = sno_asignacioncargo.codemp AND sno_personalnomina.codnom = sno_asignacioncargo.codnom AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) as denasicar, ".
//				" (SELECT descar FROM sno_cargo WHERE sno_personalnomina.codemp='".$this->ls_codemp."' AND sno_personalnomina.codemp = sno_cargo.codemp AND sno_personalnomina.codnom = sno_cargo.codnom AND sno_personalnomina.codcar = sno_cargo.codcar) as descar ".
				" FROM sno_personal, sno_personalnomina, sno_guarderias, sno_hpersonalnomina ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."' ". 
				"   ".$ls_criterio.
				" AND sno_personal.codemp = sno_personalnomina.codemp ".
				" AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				" AND sno_personal.codper = sno_personalnomina.codper ".
				" AND sno_personal.codper = sno_hpersonalnomina.codper ".
				" AND sno_personal.codper = sno_guarderias.codper ".
				" AND sno_personal.codemp = sno_guarderias.codemp ".
				" AND sno_personalnomina.codemp = sno_guarderias.codemp ".
				" AND sno_hpersonalnomina.codemp = sno_guarderias.codemp ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_datosjubilado_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_datosjubilado_guarderias
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_permisospersonal_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
										  $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										  $as_masculino,$as_femenino,$as_orden,$ls_fec_des,$ls_fec_has,$ls_tipo_permiso='',$as_uniadmin,
										  $as_codestper,$as_codmunper,$as_codparper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_permisospersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopermisos)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
			if(!empty($as_uniadmin))
			{
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "MYSQLI":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "POSTGRES":
						$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
												  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
												  "sno_personalnomina.prouniadm='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
				}
	
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codestper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codest='".$as_codestper."'";
		}
		if(!empty($as_codmunper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codmun='".$as_codmunper."'";
		}
		if(!empty($as_codparper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpar='".$as_codparper."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		if($ls_tipo_permiso)
		{
			$ls_criterio= $ls_criterio." AND sno_permiso.tipper='".$ls_tipo_permiso."' ";
		}
		if($ls_fec_des)
		{
			$ls_criterio= $ls_criterio." AND feciniper>='".$this->io_funciones->uf_convertirdatetobd($ls_fec_des)."' AND fecfinper<='".$this->io_funciones->uf_convertirdatetobd($ls_fec_has)."' ";
		}		
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{	// si busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.estper) AS estper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.dirper) AS dirper, ".
					"		MAX(sno_personal.telhabper) AS telhabper, MAX(sno_personal.telmovper) AS telmovper, MAX(sno_personal.coreleper) AS coreleper ".
					"  FROM sno_personal, sno_personalnomina, sno_permiso  ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personal.codemp = sno_permiso.codemp ".
					"	AND sno_personal.codper = sno_permiso.codper ".
					" GROUP BY sno_personal.codper ".
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.estper) AS estper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.dirper) AS dirper, ".
					"		MAX(sno_personal.telhabper) AS telhabper, MAX(sno_personal.telmovper) AS telmovper, MAX(sno_personal.coreleper) AS coreleper ".
					"  FROM sno_personal, sno_permiso ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_permiso.codemp ".
					"	AND sno_personal.codper = sno_permiso.codper ".
					" GROUP BY sno_personal.codper ".
					$ls_orden;
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_permisospersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_permisospersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_permisospersonal_permiso($as_codper,$as_fec_des,$as_fec_has,$as_tipo_permiso='')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_permisospersonal_permiso
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopermisos)  
		//	    Arguments: as_codper // Código del personal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los permisos del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if($as_tipo_permiso)
		{
			$ls_criterio= $ls_criterio." AND sno_permiso.tipper='".$as_tipo_permiso."' ";
		}
		if($as_fec_des)
		{
			$ls_criterio= $ls_criterio." AND feciniper>='".$this->io_funciones->uf_convertirdatetobd($as_fec_des)."' AND fecfinper<='".$this->io_funciones->uf_convertirdatetobd($as_fec_has)."' ";
		}		
		$ls_sql="SELECT numper, feciniper, fecfinper, numdiaper, afevacper, tipper, obsper, remper, tothorper ".
				"  FROM sno_permiso ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				$ls_criterio.
				" ORDER BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_permisospersonal_permiso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_permisospersonal_permiso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_trabajosanteriorespersonal_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,		
													$as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										 			$as_masculino,$as_femenino,$as_orden,$as_uniadmin,$as_codestper,$as_codmunper,$as_codparper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_trabajosanteriorespersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadotrabajosanteriores)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
			if(!empty($as_uniadmin))
			{
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "MYSQLI":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "POSTGRES":
						$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
												  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
												  "sno_personalnomina.prouniadm>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
				}
	
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codestper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codest='".$as_codestper."'";
		}
		if(!empty($as_codmunper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codmun='".$as_codmunper."'";
		}
		if(!empty($as_codparper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpar='".$as_codparper."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{	// si busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.estper) AS estper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.dirper) AS dirper, ".
					"		MAX(sno_personal.telhabper) AS telhabper, MAX(sno_personal.telmovper) AS telmovper, MAX(sno_personal.coreleper) AS coreleper ".
					"  FROM sno_personal, sno_personalnomina, sno_trabajoanterior ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personal.codemp = sno_trabajoanterior.codemp ".
					"	AND sno_personal.codper = sno_trabajoanterior.codper ".
					" GROUP BY sno_personal.codper ".
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.estper) AS estper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.dirper) AS dirper, ".
					"		MAX(sno_personal.telhabper) AS telhabper, MAX(sno_personal.telmovper) AS telmovper, MAX(sno_personal.coreleper) AS coreleper ".
					"  FROM sno_personal, sno_trabajoanterior ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_trabajoanterior.codemp ".
					"	AND sno_personal.codper = sno_trabajoanterior.codper ".
					" GROUP BY sno_personal.codper ".
					$ls_orden;
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_trabajosanteriorespersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_trabajosanteriorespersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_trabajosanteriorespersonal_trabajo($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_trabajosanteriorespersonal_trabajo
		//         Access: public (desde la clase sigesp_snorh_rpp_listadotrabajosanteriores)  
		//	    Arguments: as_codper // Código del personal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los Trabajos anteriores del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codtraant, emptraant, ultcartraant, ultsuetraant, fecingtraant, fecrettraant, emppubtraant, anolab, meslab, dialab ".
				"  FROM sno_trabajoanterior ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				" ORDER BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_trabajosanteriorespersonal_trabajo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_trabajosanteriorespersonal_trabajo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_estudiospersonal_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,		
										  $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										  $as_masculino,$as_femenino,$as_orden,$as_uniadmin,$as_codestper,$as_codmunper,$as_codparper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_estudiospersonal_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadoestudios)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
			if(!empty($as_uniadmin))
			{
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "MYSQLI":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "POSTGRES":
						$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
												  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
												  "sno_personalnomina.prouniadm>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
				}
	
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codestper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codest='".$as_codestper."'";
		}
		if(!empty($as_codmunper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codmun='".$as_codmunper."'";
		}
		if(!empty($as_codparper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpar='".$as_codparper."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{	// si busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.estper) AS estper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.dirper) AS dirper, ".
					"		MAX(sno_personal.telhabper) AS telhabper, MAX(sno_personal.telmovper) AS telmovper, MAX(sno_personal.coreleper) AS coreleper, ".
					"		MAX(sno_unidadadmin.desuniadm) AS desuniadm  ".
					"  FROM sno_personal, sno_personalnomina, sno_estudiorealizado, sno_unidadadmin ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personal.codemp = sno_estudiorealizado.codemp ".
					"	AND sno_personal.codper = sno_estudiorealizado.codper ".
				    "   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
					" GROUP BY sno_personal.codper ".
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
					"		MAX(sno_personal.estper) AS estper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.dirper) AS dirper, ".
					"		MAX(sno_personal.telhabper) AS telhabper, MAX(sno_personal.telmovper) AS telmovper, MAX(sno_personal.coreleper) AS coreleper, ".
					"		'' AS desuniadm ".
					"  FROM sno_personal, sno_estudiorealizado ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_estudiorealizado.codemp ".
					"	AND sno_personal.codper = sno_estudiorealizado.codper ".
					" GROUP BY sno_personal.codper ".
					$ls_orden;
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_estudiospersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_estudiospersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_estudiospersonal_estudios($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_estudiospersonal_estudios
		//         Access: public (desde la clase sigesp_snorh_rpp_listadoestudios)  
		//	    Arguments: as_codper // Código del personal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los Trabajos anteriores del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/07/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codestrea, tipestrea, insestrea, desestrea, titestrea, calestrea, fecgraestrea, feciniact, fecfinact ".
				"  FROM sno_estudiorealizado ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."' ".
				" ORDER BY codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_estudiospersonal_estudios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_estudiospersonal_estudios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalunidadadm_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
										 		  $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										 		  $as_masculino,$as_femenino,$as_coduniadmdes,$as_coduniadmhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalunidadadm_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_masculino // Solo el personal masculino
		//	  			   as_femenino // Solo el personal femenino
		//	    		   as_coduniadmdes // Código de unidad administrativa donde se empieza a filtrar
		//	  			   as_coduniadmhas // Código de unidad administrativa donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_coduniadmdes))
		{
		 	switch($_SESSION["ls_gestor"])
	   		{
				case "MYSQLT":				
					$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
											  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
											  "              sno_personalnomina.prouniadm)>='".substr($as_coduniadmdes,0,4).substr($as_coduniadmdes,5,2).substr($as_coduniadmdes,8,2).substr($as_coduniadmdes,11,2).substr($as_coduniadmdes,14,2)."' ";
				break;
				case "MYSQLI":				
					$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
											  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
											  "              sno_personalnomina.prouniadm)>='".substr($as_coduniadmdes,0,4).substr($as_coduniadmdes,5,2).substr($as_coduniadmdes,8,2).substr($as_coduniadmdes,11,2).substr($as_coduniadmdes,14,2)."' ";
				break;
				case "POSTGRES":
					$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
											  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
											  "sno_personalnomina.prouniadm>='".substr($as_coduniadmdes,0,4).substr($as_coduniadmdes,5,2).substr($as_coduniadmdes,8,2).substr($as_coduniadmdes,11,2).substr($as_coduniadmdes,14,2)."' ";
				break;
			}

		}
		if(!empty($as_coduniadmhas))
		{
		 	switch($_SESSION["ls_gestor"])
	   		{
				case "MYSQLT":				
					$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
											  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
											  "              sno_personalnomina.prouniadm)<='".substr($as_coduniadmhas,0,4).substr($as_coduniadmhas,5,2).substr($as_coduniadmhas,8,2).substr($as_coduniadmhas,11,2).substr($as_coduniadmhas,14,2)."' ";
				break;
				case "MYSQLI":				
					$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
											  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
											  "              sno_personalnomina.prouniadm)<='".substr($as_coduniadmhas,0,4).substr($as_coduniadmhas,5,2).substr($as_coduniadmhas,8,2).substr($as_coduniadmhas,11,2).substr($as_coduniadmhas,14,2)."' ";
				break;
				case "POSTGRES":
					$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
											  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
											  "sno_personalnomina.prouniadm<='".substr($as_coduniadmhas,0,4).substr($as_coduniadmhas,5,2).substr($as_coduniadmhas,8,2).substr($as_coduniadmhas,11,2).substr($as_coduniadmhas,14,2)."' ";
				break;
			}
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				


		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT DISTINCT sno_personal.codper, sno_personal.nomper, sno_personal.apeper, sno_unidadadmin.desuniadm, ".
				"		sno_ubicacionfisica.desubifis, ".
				"		(SELECT despai FROM sigesp_pais ".
				"		  WHERE sigesp_pais.codpai = sno_ubicacionfisica.codpai) AS despai, ".
				"		(SELECT desest FROM sigesp_estados ".
				"		  WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		(SELECT denmun FROM sigesp_municipio ".
				"		  WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		(SELECT denpar FROM sigesp_parroquia ".
				"		  WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
				"		(SELECT despai FROM sigesp_pais ".
				"		  WHERE sigesp_pais.codpai = sno_personal.codpai) AS despaiper, ".
				"		(SELECT desest FROM sigesp_estados ".
				"		  WHERE sigesp_estados.codpai = sno_personal.codpai ".
				"			AND sigesp_estados.codest = sno_personal.codest) AS desestper, ".
				"		(SELECT denmun FROM sigesp_municipio ".
				"		  WHERE sigesp_municipio.codpai = sno_personal.codpai ".
				"			AND sigesp_municipio.codest = sno_personal.codest ".
				"			AND sigesp_municipio.codmun = sno_personal.codmun) AS denmunper, ".
				"		(SELECT denpar FROM sigesp_parroquia ".
				"		  WHERE sigesp_parroquia.codpai = sno_personal.codpai ".
				"			AND sigesp_parroquia.codest = sno_personal.codest ".
				"			AND sigesp_parroquia.codmun = sno_personal.codmun ".
				"			AND sigesp_parroquia.codpar = sno_personal.codpar) AS denparper, ".
				"       (SELECT codcom FROM sno_componente ".
				"		  WHERE sno_componente.codemp=sno_personal.codemp ".
				"           and sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
				"       (SELECT descom FROM sno_componente ".
				"		  WHERE sno_componente.codemp=sno_personal.codemp ".
				"           AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
				"       (SELECT codran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
				"       (SELECT desran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as desrango ".
				"  FROM sno_personal, sno_personalnomina, sno_unidadadmin, sno_ubicacionfisica  ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio.
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"	AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
				"	AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm  ".
				"	AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm  ".
				"	AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm  ".
				"	AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
				"   AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"	AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".
				$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonalunidadadm_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_listadopersonalunidadadm_personal
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalrelacion_rac($as_codnom,$as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalunidadadm_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnom// Código de la nómina 
		//	  			   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creación: 19/12/2011 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnom))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom='".$as_codnom."'";
			$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
			$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
			$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
			$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
			$lb_ok=true;
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_unidadadmin.codestpro1,sno_unidadadmin.codestpro2,sno_unidadadmin.codestpro5,sno_unidadadmin.desuniadm ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_unidadadmin.codestpro1,sno_unidadadmin.codestpro2,sno_unidadadmin.codestpro5,sno_unidadadmin.desuniadm ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_unidadadmin.codestpro1,sno_unidadadmin.codestpro2,sno_unidadadmin.codestpro5,sno_unidadadmin.desuniadm ";
				break;
		}
			$ls_sql=" SELECT DISTINCT sno_personal.codper, sno_personal.nomper, sno_personal.apeper, sno_unidadadmin.desuniadm,sno_personal.fecingper, ".
				" sno_personal.cedper,sno_unidadadmin.codestpro1,sno_unidadadmin.codestpro2,sno_unidadadmin.codestpro5, ".
				" spg_ep1.denestpro1,spg_ep2.denestpro2,spg_ep5.denestpro5, sno_asignacioncargo.denasicar, sno_personalnomina.codasicar, sno_personalnomina.sueper ".
				" FROM sno_personal, sno_personalnomina, sno_unidadadmin, sno_ubicacionfisica,spg_ep1,spg_ep2,spg_ep5,sno_asignacioncargo ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'". 
				"   ".$ls_criterio.
				" AND (sno_personal.estper='1' ) ". 
				" AND sno_personal.codemp = sno_personalnomina.codemp ".
				" AND sno_personal.codper = sno_personalnomina.codper ".
				" AND sno_personalnomina.codnom= sno_asignacioncargo.codnom ".
				" AND sno_personalnomina.codasicar= sno_asignacioncargo.codasicar ".
				" AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				" AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				" AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				" AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				" AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				" AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				" AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				" AND sno_unidadadmin.codestpro1 = spg_ep1.codestpro1 ".
				" AND sno_unidadadmin.codestpro1 = spg_ep2.codestpro1 ".
				" AND sno_unidadadmin.codestpro1 = spg_ep5.codestpro1 ".
				" AND sno_unidadadmin.codestpro2 = spg_ep2.codestpro2 ".
				" AND sno_unidadadmin.codestpro2 = spg_ep5.codestpro2 ".
				" AND sno_unidadadmin.codestpro3 = spg_ep5.codestpro3 ".
				" AND sno_unidadadmin.codestpro4 = spg_ep5.codestpro4 ".
				" AND sno_unidadadmin.codestpro5 = spg_ep5.codestpro5 ".
				$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonalrelacion_rac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_listadopersonalunidadadm_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalcontratado_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
										 		   $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
										 		   $as_masculino,$as_femenino,$ad_fecculcontrdes,$ad_fecculcontrhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalcontratado_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonalcontratado)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   ad_fecculcontrdes // Fecha de culminación de Contrato Desde
		//	  			   ad_fecculcontrhas // Fecha de culminación de Contrato Hasta
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 16/08/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}			
		if((!empty($ad_fecculcontrdes))&&($ad_fecculcontrdes!="1900-01-01"))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.fecculcontr>='".$ad_fecculcontrdes."'";
		}
		if((!empty($ad_fecculcontrhas))&&($ad_fecculcontrdes!="1900-01-01"))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.fecculcontr<='".$ad_fecculcontrhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT DISTINCT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_personal.estper, sno_personal.nivacaper, sno_profesion.despro, sno_nomina.desnom, ".
				"		sno_personalnomina.staper AS estnom, sno_personalnomina.fecingper AS fecingnom, sno_personalnomina.fecculcontr, ".
				"       (SELECT codcom FROM sno_componente ".
				"		  WHERE sno_componente.codemp=sno_personal.codemp ".
				"           and sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
				"       (SELECT descom FROM sno_componente ".
				"		  WHERE sno_componente.codemp=sno_personal.codemp ".
				"           AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
				"       (SELECT codran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
				"       (SELECT desran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as desrango ".
				"  FROM sno_personal, sno_profesion, sno_personalnomina, sno_nomina ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"	AND (sno_nomina.tipnom='2' OR sno_nomina.tipnom='4' OR sno_nomina.tipnom='6')".
				"   ".$ls_criterio.
				"   AND sno_personal.codemp = sno_profesion.codemp ".
				"	AND sno_personal.codpro = sno_profesion.codpro ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"	AND sno_personalnomina.codnom = sno_nomina.codnom  ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonalcontratado_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadopersonalcontratado_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadogenerico()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadogenerico
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonalgenerico)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/08/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_unidad=substr_count($_SESSION["ls_campo"], 'sno_unidadadmin.desuniadm');
		$ls_ger=substr_count($_SESSION["ls_campo"], 'srh_gerencia.denger'); 
		$ls_orden="";
		if(($ls_unidad==1)&&($ls_ger==1))
		{
			$ls_orden="sno_unidadadmin.desuniadm, sno_personal.codper";
		}
		if(($ls_unidad==1)&&($ls_ger==0))
		{
			$ls_orden="sno_unidadadmin.desuniadm, sno_personal.codper";
		}
		if(($ls_unidad==0)&&($ls_ger==1))
		{
			$ls_orden="srh_gerencia.denger, sno_personal.codper";
		}
		if(($ls_unidad==0)&&($ls_ger==0))
		{
			$ls_orden="sno_personal.codper";
		}
                $ls_codestpro=substr_count($_SESSION["ls_campo"], 'sno_unidadadmin.codestpro');
                if ($ls_codestpro==1)
                {
                    $ls_modalidad=$_SESSION["la_empresa"]["estmodest"];
                    $ai_len1=$_SESSION["la_empresa"]["loncodestpro1"];
                    $ai_len2=$_SESSION["la_empresa"]["loncodestpro2"];
                    $ai_len3=$_SESSION["la_empresa"]["loncodestpro3"];
                    $ai_len4=$_SESSION["la_empresa"]["loncodestpro4"];
                    $ai_len5=$_SESSION["la_empresa"]["loncodestpro5"];
                    switch($ls_modalidad)
                    {
                            case "1": // Modalidad por Proyecto
                                    $codest1 = "SUBSTR(sno_unidadadmin.codestpro1,25-{$ai_len1})";
                                    $codest2 = "SUBSTR(sno_unidadadmin.codestpro2,25-{$ai_len2})";
                                    $codest3 = "SUBSTR(sno_unidadadmin.codestpro3,25-{$ai_len3})";
                                    $cadenaEstructura = $this->io_conexion->Concat('sno_unidadadmin.estcla',"'-'",$codest1,"'-'",$codest2,"'-'",$codest3);
                                    break;

                            case "2": // Modalidad por Programatica
                                    $codest1 = "SUBSTR(sno_unidadadmin.codestpro1,25-{$ai_len1})";
                                    $codest2 = "SUBSTR(sno_unidadadmin.codestpro2,25-{$ai_len2})";
                                    $codest3 = "SUBSTR(sno_unidadadmin.codestpro3,25-{$ai_len3})";
                                    $codest4 = "SUBSTR(sno_unidadadmin.codestpro4,25-{$ai_len4})";
                                    $codest5 = "SUBSTR(sno_unidadadmin.codestpro5,25-{$ai_len5})";
                                    $cadenaEstructura = $this->io_conexion->Concat('sno_unidadadmin.estcla',"'-'",$codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
                                    break;
                    }
                    $_SESSION["ls_campo"] = str_replace('sno_unidadadmin.codestpro', $cadenaEstructura, $_SESSION["ls_campo"]);
                }
		if ($_SESSION["ls_criterio2"]==0)
		{
			$ls_sql="SELECT DISTINCT sno_personal.codper, ".$_SESSION["ls_campo"]." ".
                                "  FROM sno_personal ".
                                " INNER JOIN sno_profesion ".
                                "    ON sno_personal.codemp = sno_profesion.codemp ".
                                "	AND sno_personal.codpro = sno_profesion.codpro ".
                                " INNER JOIN sno_tipopersonalsss ".
                                "    ON sno_personal.codemp=sno_tipopersonalsss.codemp ".
                                "   AND sno_personal.codtippersss=sno_tipopersonalsss.codtippersss ".	
                                " INNER JOIN srh_gerencia ".
                                "    ON sno_personal.codemp = srh_gerencia.codemp ".
                                "	AND sno_personal.codger = srh_gerencia.codger ".
                                " INNER JOIN (sno_personalnomina ".
                                "       INNER JOIN sno_nomina ".
                                "          ON sno_personalnomina.codemp = sno_nomina.codemp ".
                                "	      AND sno_personalnomina.codnom = sno_nomina.codnom ".
                                "       INNER JOIN sno_cargo ".
                                "          ON sno_personalnomina.codemp = sno_cargo.codemp ".
                                "	      AND sno_personalnomina.codnom = sno_cargo.codnom ".
                                "	      AND sno_personalnomina.codcar = sno_cargo.codcar ".
                                "       INNER JOIN sno_asignacioncargo ".
                                "          ON sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
                                "	      AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
                                "	      AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar ".
                                "       INNER JOIN sno_grado ".
                                "          ON sno_personalnomina.codemp = sno_grado.codemp ".
                                "	      AND sno_personalnomina.codnom = sno_grado.codnom ".
                                "	      AND sno_personalnomina.codtab = sno_grado.codtab ".
                                "	      AND sno_personalnomina.codpas = sno_grado.codpas ".
                                "	      AND sno_personalnomina.codgra = sno_grado.codgra ".
                                "	INNER JOIN sno_dedicacion ".
                                "          ON sno_personalnomina.codemp = sno_dedicacion.codemp ".
                                "	      AND sno_personalnomina.codded = sno_dedicacion.codded ".
                                "	INNER JOIN sno_tipopersonal ".
                                "          ON sno_personalnomina.codemp = sno_tipopersonal.codemp ".
                                "	      AND sno_personalnomina.codded = sno_tipopersonal.codded ".
                                "	      AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
                                "	INNER JOIN sno_ubicacionfisica ".
                                "          ON sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
                                "	     AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis ".
                                "	INNER JOIN sno_unidadadmin ".
                                "          ON sno_personalnomina.codemp = sno_unidadadmin.codemp ".
                                "	      AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
                                "	      AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
                                "	      AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
                                "	      AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
                                "	      AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
                                "	 LEFT JOIN scb_banco ".
                                "          ON sno_personalnomina.codemp = scb_banco.codemp ".
                                "         AND sno_personalnomina.codban = scb_banco.codban ".
                                "        LEFT JOIN sno_clasificacionobrero ".
                                "          ON sno_personalnomina.codemp = sno_clasificacionobrero.codemp ".
                                "         AND sno_personalnomina.grado = sno_clasificacionobrero.grado) ".
                                "    ON sno_personal.codemp = sno_personalnomina.codemp ".
                                "	AND sno_personal.codper = sno_personalnomina.codper ".
                                " WHERE sno_personal.codemp = '".$this->ls_codemp."'".
                                "   ".$_SESSION["ls_criterio"].
                                " ORDER BY $ls_orden ";
		}
		else
		{
			$ls_sql="SELECT DISTINCT sno_personal.codper, ".$_SESSION["ls_campo"]." ".
                                "  FROM sno_personal ".
                                " INNER JOIN sno_profesion ".
                                "    ON sno_personal.codemp = sno_profesion.codemp ".
                                "	AND sno_personal.codpro = sno_profesion.codpro ".
                                " INNER JOIN sno_tipopersonalsss ".
                                "    ON sno_personal.codemp=sno_tipopersonalsss.codemp ".
                                "   AND sno_personal.codtippersss=sno_tipopersonalsss.codtippersss ".	
                                " INNER JOIN srh_gerencia ".
                                "    ON sno_personal.codemp = srh_gerencia.codemp ".
                                "	AND sno_personal.codger = srh_gerencia.codger ".
                                " WHERE sno_personal.codemp = '".$this->ls_codemp."'".
                                "   ".$_SESSION["ls_criterio"].
                                " ORDER BY $ls_orden ";
		}
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadogenerico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_listadogenerico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_personal($as_codnom,$ad_fecdesper,$ad_fechasper,$as_codperdes,$as_codperhas,$as_coduniadmdes,$as_coduniadmhas,$as_conceptocero,
									$as_conceptop2,$as_conceptoreporte,$as_subnomdes,$as_subnomhas,$as_consolidar,$as_orden,$as_codubifisdes,$as_codubifishas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibopago_personal
		//         Access: public (desde la clase sigesp_sno_r_recibopago)  
		//	    Arguments: as_codnom // Código del nómina donde se va a filtrar
		//	    		   ad_fecdesper // Fecha del periodo donde se empieza a filtrar
		//	  			   ad_fechasper // Fecha del periodo donde se termina de filtrar		  
		//	    		   as_codperdes // Código del personal donde se empieza a filtrar
		//	  			   as_codperhas // Código del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // Código de la unidad administrativa	  
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos de tipo reporte
		//	  			   as_orden // Orde a mostrar en el reporte		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/09/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_distinct="DISTINCT ";
		$ls_group="";
		$ls_campo="";
		if($as_consolidar=="0")
		{
			$ls_distinct=" ";
			$ls_group=" sno_hpersonalnomina.codperi, ";
			$ls_campo=" MAX(sno_hperiodo.fecdesper) AS fecdesper, MAX(sno_hperiodo.fechasper) AS fechasper, ";
			$ls_orden=", sno_hpersonalnomina.codperi ";
		}
		if(!empty($ad_fecdesper))
		{
			$ls_criterio= $ls_criterio."AND sno_hperiodo.fecdesper>='".$this->io_funciones->uf_convertirdatetobd($ad_fecdesper)."'";
		}
		if(!empty($ad_fechasper))
		{
			$ls_criterio= $ls_criterio."   AND sno_hperiodo.fechasper<='".$this->io_funciones->uf_convertirdatetobd($ad_fechasper)."'";
		}
		if(!empty($as_codubifisdes))
		{
			$ls_criterio= $ls_criterio."AND sno_hpersonalnomina.codubifis>='".$as_codubifisdes."'";
		}
		if(!empty($as_codubifishas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codubifis<='".$as_codubifishas."'";
		}
		if(!empty($as_codnom))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codnom='".$as_codnom."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if((!empty($as_coduniadmdes))&&(!empty($as_coduniadmhas)))
		{
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.minorguniadm BETWEEN '".substr($as_coduniadmdes,0,4)."' AND '".substr($as_coduniadmhas,0,4)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.ofiuniadm BETWEEN '".substr($as_coduniadmdes,5,2)."' AND '".substr($as_coduniadmhas,5,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.uniuniadm BETWEEN '".substr($as_coduniadmdes,8,2)."' AND '".substr($as_coduniadmhas,8,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.depuniadm BETWEEN '".substr($as_coduniadmdes,11,2)."' AND '".substr($as_coduniadmhas,11,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_hpersonalnomina.prouniadm BETWEEN '".substr($as_coduniadmdes,14,2)."' AND '".substr($as_coduniadmhas,14,2)."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptop2))
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
		}
		else
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ".$ls_orden;
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ".$ls_orden;
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ".$ls_orden;
				break;
		}
		$ls_sql="SELECT ".$ls_distinct." sno_personal.codper, MAX(sno_personal.cedper) as cedper, max(sno_personal.nomper) as nomper, ".
				"       MAX(sno_personal.apeper) as apeper, MAX(sno_personal.nacper) as nacper, MAX(sno_hpersonalnomina.codcueban) AS codcueban, MAX(sno_hpersonalnomina.tipcuebanper) as tipcuebanper, ".
				"       MAX(sno_personal.fecleypen) AS fecleypen, MAX(sno_personal.fecingper) as fecingper, ".$ls_group.$ls_campo.
				"       MAX(sno_personal.fecegrper) as fecegrper, MAX(sno_personal.rifper) as rifper,".
				"       sum(sno_hsalida.valsal) as total, MAX(sno_personal.coreleper) AS coreleper, MAX(sno_hpersonalnomina.salnorper) AS  salnorper, ".
				"       MAX(sno_hunidadadmin.desuniadm) as desuniadm, MAX(sno_hpersonalnomina.fecingper) AS fecingnom, MAX(sno_hpersonalnomina.fecculcontr) AS fecculcontr, ".
				"       MAX(sno_hunidadadmin.minorguniadm) as minorguniadm, MAX(sno_hunidadadmin.ofiuniadm) AS ofiuniadm, ".
				"       MAX(sno_hunidadadmin.uniuniadm) AS uniuniadm, MAX(sno_hunidadadmin.depuniadm) as depuniadm, MAX(sno_hunidadadmin.prouniadm) AS prouniadm, ".
				"       MAX(sno_hpersonalnomina.sueper) AS sueper, MAX(sno_hpersonalnomina.pagbanper) AS pagbanper, MAX(sno_hpersonalnomina.pagefeper) AS pagefeper,".
				"       MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_hnomina.desnom) AS desnom, MAX(sno_hnomina.racnom) AS racnom,  MAX(sno_personal.codorg) as codorg,".
				"		MAX(sno_hnomina.adenom) AS adenom,  MAX(sno_hpersonalnomina.sueintper) AS sueintper, MAX(sno_hpersonalnomina.codcar) AS codcar, MAX(sno_hpersonalnomina.codasicar) AS codasicar,MAX(sno_hpersonalnomina.descasicar) AS descasicar,".
				"		MAX(sno_hpersonalnomina.staper) AS staper,MAX(sno_hpersonalnomina.obsrecper) as obsrecper, ".
				"		MAX((SELECT destipper FROM sno_tipopersonal ".
				"		  WHERE sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"			AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper)) AS destipper, ".
				"		MAX((SELECT desest FROM sigesp_estados ".
				"		  WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_estados.codest = sno_ubicacionfisica.codest)) AS desest, ".
				"		MAX((SELECT tipnom FROM sno_hnomina ".
				" 			WHERE sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"			AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"			AND sno_hpersonalnomina.anocur=sno_hnomina.anocurnom ".
				"			AND sno_hpersonalnomina.codperi=sno_hnomina.peractnom)) AS tiponom,".
				"		MAX((SELECT suemin FROM sno_hclasificacionobrero ".
				"			WHERE sno_hclasificacionobrero.codnom = sno_hpersonalnomina.codnom ".
				"			AND sno_hclasificacionobrero.grado = sno_hpersonalnomina.grado ".
				"			AND sno_hclasificacionobrero.codemp = sno_hpersonalnomina.codemp ".
				"			AND sno_hclasificacionobrero.codperi = sno_hpersonalnomina.codperi ".
				"			AND sno_hclasificacionobrero.anocur = sno_hpersonalnomina.anocur)) AS sueobr, ".
				"		MAX((SELECT denmun FROM sigesp_municipio ".
				"		  WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun)) AS denmun, ".
				"		MAX((SELECT denpar FROM sigesp_parroquia ".
				"		  WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"		    AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar)) AS denpar, ".
				"		MAX((SELECT nomban FROM scb_banco ".
				"		   WHERE scb_banco.codemp = sno_hpersonalnomina.codemp ".
				" 			 AND scb_banco.codban = sno_hpersonalnomina.codban)) AS banco,".
				"		MAX((SELECT  MAX(nomage) FROM scb_agencias ".
				"		   WHERE scb_agencias.codemp = sno_hpersonalnomina.codemp ".
				" 			 AND scb_agencias.codban = sno_hpersonalnomina.codban ".
				"            AND scb_agencias.codage = sno_hpersonalnomina.codage)) AS agencia,".
				"       MAX((SELECT MAX(denasicar) FROM sno_hasignacioncargo  ".
                "          WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
                "            AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom  ".
				"  	         AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
				"  	         AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				"	         AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar)) as denasicar,  ".
			    "       MAX((SELECT MAX(descar) FROM sno_hcargo ".
				"	  WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
				"		AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
				"		AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
				"		AND sno_hpersonalnomina.codcar = sno_hcargo.codcar ".
				"		AND sno_hpersonalnomina.codperi = sno_hcargo.codperi)) as descar,   ".
				"           sno_hnomina.codnom,												".
				"      MAX((SELECT sno_categoria_rango.descat FROM sno_rango, sno_categoria_rango   ".
                "         WHERE sno_rango.codemp=sno_personal.codemp                             ".
                "           AND sno_rango.codcom=sno_personal.codcom                             ".
                "     AND sno_rango.codran=sno_personal.codran                                   ".
                "     AND sno_categoria_rango.codcat=sno_rango.codcat)) AS descat                ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin,   ".
				"       sno_ubicacionfisica, sno_hnomina, sno_hperiodo".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND (sno_hsalida.tipsal<>'P2' AND sno_hsalida.tipsal<>'V4' AND sno_hsalida.tipsal<>'W4' ) ".
				"   ".$ls_criterio." ".
				"   AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"   AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"   AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_ubicacionfisica.codemp ".
				"   AND sno_hpersonalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hpersonalnomina.codnom, sno_hnomina.codnom, ".$ls_group." sno_personal.codper,  ".        
				"          sno_hpersonalnomina.anocur  ".
				"   ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_recibopago_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_recibopago_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestamo_personal($as_codper,$as_codnom,$as_codconc,$as_sigcon){
		$ls_prestamo=0;
		$ls_sql="SELECT sno_prestamos.monpre as monto, sno_prestamos.monamopre as monamopre ".
				"  FROM sno_prestamos ".
				"  INNER JOIN sno_concepto USING (codemp,codconc) ".
				" WHERE sno_prestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_prestamos.codnom = '".$as_codnom."'  ".
				"   AND sno_prestamos.codper='".$as_codper."' ".
				"   AND sno_prestamos.codconc = '".$as_codconc."' ".
				"   AND sno_prestamos.stapre = '1' ".
				"   AND sno_concepto.sigcon = '".$as_sigcon."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestamo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_prestamo=$row["monto"]-$row["monamopre"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_prestamo;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_conceptopersonal($as_codnom,$ad_fecdesper,$ad_fechasper,$as_codper,$as_conceptocero,$as_conceptop2,
											$as_conceptoreporte,$as_tituloconcepto,$as_codperi)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibopago_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago)  
		//	    Arguments: as_codnom // Código del nómina donde se va a filtrar
		//	    		   ad_fecdesper // Fecha del periodo donde se empieza a filtrar
		//	  			   ad_fechasper // Fecha del periodo donde se termina de filtrar		  
		//	    		   as_codper // Código del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos de tipo reporte
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar los títulos de los conceptos
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 05/05/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_codperi))
		{
			$ls_criterio= $ls_criterio."   AND sno_hperiodo.codperi='".$as_codperi."'";
			$ls_criterio2= $ls_criterio2."   AND sno_hconstantepersonal.codperi='".$as_codperi."'";
		}
		if(!empty($ad_fecdesper))
		{
			$ls_criterio= $ls_criterio."   AND sno_hperiodo.fecdesper>='".$this->io_funciones->uf_convertirdatetobd($ad_fecdesper)."'";
		}
		if(!empty($ad_fechasper))
		{
			$ls_criterio= $ls_criterio."   AND sno_hperiodo.fechasper<='".$this->io_funciones->uf_convertirdatetobd($ad_fechasper)."'";
		}
		if(!empty($as_codnom))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codnom='".$as_codnom."'";
		}
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_conceptop2))
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
		}
		else
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"  	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"      sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
											"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		$ls_sql="SELECT sno_hconcepto.codconc, sno_hconcepto.codperi, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) AS valsal, MAX(sno_hsalida.tipsal) AS tipsal, ".
				"		0 AS acuemp, 0 AS acupat , MAX(sno_hconcepto.repacucon) as repacucon, MAX(sno_hconcepto.repconsunicon) AS repconsunicon , MAX(sno_hconcepto.consunicon) AS consunicon,MAX(sno_hconcepto.persalnor) AS persalnor, SUM(sno_hsalida.monacusal) AS monacusal,".
				"		(SELECT SUM(moncon) FROM sno_hconstantepersonal ".
				"		  WHERE sno_hconcepto.repconsunicon='1' ".
				"			AND sno_hconstantepersonal.codper = '".$as_codper."' ".
				$ls_criterio2.
				"			AND sno_hconstantepersonal.codemp = sno_hconcepto.codemp ".
				"			AND sno_hconstantepersonal.codnom = sno_hconcepto.codnom ".
				"			AND sno_hconstantepersonal.anocur = sno_hconcepto.anocur ".
				"			AND sno_hconstantepersonal.codperi = sno_hconcepto.codperi ".
				"			AND sno_hconstantepersonal.codcons = sno_hconcepto.consunicon ) AS unidad ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				" GROUP BY sno_hconcepto.codemp, sno_hconcepto.codnom,  sno_hconcepto.codconc, sno_hsalida.tipsal,  sno_hconcepto.anocur, sno_hconcepto.codperi, sno_hconcepto.consunicon,sno_hconcepto.repconsunicon  ".
				" UNION ".
				"SELECT sno_hconcepto.codconc, '' as codperi, MAX(".$ls_campo.") as nomcon, 0 AS valsal, MAX(sno_hsalida.tipsal) AS tipsal, ".
				"		MAX(abs(sno_hconceptopersonal.acuemp)) AS acuemp, MAX(abs(sno_hconceptopersonal.acupat)) AS acupat , MAX(sno_hconcepto.repacucon) as repacucon,MAX(sno_hconcepto.repconsunicon) AS repconsunicon , ".
				"		MAX(sno_hconcepto.consunicon) AS consunicon,MAX(sno_hconcepto.persalnor) AS persalnor, 0 AS unidad, 0 AS monacusal ".
				"  FROM sno_hsalida, sno_hconcepto, sno_hperiodo, sno_hconceptopersonal ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ".
				"   AND sno_hsalida.codemp = sno_hconceptopersonal.codemp ".
				"   AND sno_hsalida.codnom = sno_hconceptopersonal.codnom ".
				"   AND sno_hsalida.anocur = sno_hconceptopersonal.anocur ".
				"   AND sno_hsalida.codperi = sno_hconceptopersonal.codperi ".
				"   AND sno_hsalida.codconc = sno_hconceptopersonal.codconc ".
				"   AND sno_hsalida.codper = sno_hconceptopersonal.codper ".
				" GROUP BY sno_hconcepto.codemp, sno_hconcepto.codnom,  sno_hconcepto.codconc  ".
				" ORDER BY codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_recibopago_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'codconc','1'=>'tipsal'),array('0'=>'valsal','1'=>'unidad','2'=>'repacucon','3'=>'acuemp','4'=>'acupat'),'valsal');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_recibopago_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_informacionprestamos($as_codnom,$ad_fecdesper,$ad_fechasper,$as_codper,$as_codperi,$la_prestamo)
	{
		$lb_valido=false;
		$ls_criterio ="";
		$ls_criterio2 ="";
		$la_prestamo[1]=array('nombre'=>'','prestamo'=>0,'abono'=>0,'saldo'=>0);
		if(!empty($as_codnom))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codnom='".$as_codnom."'";
		}
		if(!empty($as_codperi))
		{
			$ls_criterio= $ls_criterio."   AND sno_hprestamos.codperi='".$as_codperi."'";
		}
		if(!empty($ad_fecdesper))
		{
			//$ls_criterio2= $ls_criterio2."   AND sno_hperiodo.fecdesper>='".$this->io_funciones->uf_convertirdatetobd($ad_fecdesper)."'";
		}
		if(!empty($ad_fechasper))
		{
			$ls_criterio2= $ls_criterio2."   AND sno_hperiodo.fechasper='".$this->io_funciones->uf_convertirdatetobd($ad_fechasper)."'";
		}

		$ls_sql="SELECT sno_hprestamos.codconc, MAX(sno_hconcepto.nomcon) AS nomcon, SUM(sno_hprestamos.monpre) as monto, SUM(sno_hprestamos.monamopre) as monamopre, ".
				"       (SELECT SUM(valsal) ".
				"          FROM sno_hsalida ".
				"         WHERE sno_hsalida.codemp = sno_hprestamos.codemp ".
				"           AND sno_hsalida.codnom = sno_hprestamos.codnom ".
				"           AND sno_hsalida.anocur = sno_hprestamos.anocur ".
				"           AND sno_hsalida.codperi = sno_hprestamos.codperi ".				
				"           AND sno_hsalida.codper = sno_hprestamos.codper ".
				"           AND sno_hsalida.codconc = sno_hprestamos.codconc ) AS actual ".
				"  FROM sno_hprestamos ".
				" INNER JOIN sno_hconcepto  ".
				"    ON sno_hprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_hprestamos.codper='".$as_codper."' ".
				"   AND sno_hprestamos.stapre = '1' ".
				$ls_criterio.
				"   AND sno_hprestamos.codemp = sno_hconcepto.codemp".
				"   AND sno_hprestamos.codnom = sno_hconcepto.codnom  ".
				"   AND sno_hprestamos.anocur = sno_hconcepto.anocur  ".
				"   AND sno_hprestamos.codperi = sno_hconcepto.codperi  ".
				"   AND sno_hprestamos.codconc = sno_hconcepto.codconc  ".
				" INNER JOIN sno_hperiodo  ".
				"    ON sno_hprestamos.codemp='".$this->ls_codemp."' ".
				$ls_criterio.
				$ls_criterio2.
				"   AND sno_hprestamos.codper='".$as_codper."' ".
				"   AND sno_hprestamos.stapre = '1' ".
				"   AND sno_hprestamos.codemp = sno_hperiodo.codemp".
				"   AND sno_hprestamos.codnom = sno_hperiodo.codnom  ".
				"   AND sno_hprestamos.anocur = sno_hperiodo.anocur  ".
				"   AND sno_hprestamos.codperi = sno_hperiodo.codperi  ".
				" GROUP BY sno_hprestamos.codemp, sno_hprestamos.codnom, sno_hprestamos.anocur, sno_hprestamos.codperi, sno_hprestamos.codper, sno_hprestamos.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_recibopago_informacionprestamos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$i=1;
			while(!$rs_data->EOF)
			{
				$lb_valido=true;
				$li_prestamo = number_format($rs_data->fields["monto"],2,",",".");
				$li_abono = number_format($rs_data->fields["monamopre"]+abs($rs_data->fields["actual"]),2,",",".");
				$li_saldo = number_format($rs_data->fields["monto"]-($rs_data->fields["monamopre"]+abs($rs_data->fields["actual"])),2,",",".");
				$la_prestamo[$i]=array('nombre'=>$rs_data->fields["nomcon"],'prestamo'=>$li_prestamo,'abono'=>$li_abono,'saldo'=>$li_saldo);
				$i++;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0406_programado($as_rango)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0406_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0711)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/09/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT codrep,codded,codtipper,monene,monfeb,monmar,monabr,monmay,monjun,monjul,monago,monsep,monoct,monnov,mondic, ".
				"		carene,carfeb,carmar,carabr,carmay,carjun,carjul,carago,carsep,caroct,carnov,cardic, ".
				"		(SELECT sno_dedicacion.desded FROM  sno_dedicacion ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
				"			AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
				"		(SELECT sno_tipopersonal.destipper FROM  sno_tipopersonal ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
				"			AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
				"			AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
				"  FROM sno_programacionreporte ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codrep = '0406'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0406_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_comparado0406_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_comparado0406_real($as_rango,$as_codded,$as_codtipper,$ai_cargoreal,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_comparado0406_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0711)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 06/09/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_codtipper=="0000")
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded ";
		}
		else
		{
			$ls_criterio=" AND sno_hpersonalnomina.codded='".$as_codded."'".
						 " AND sno_hpersonalnomina.codtipper='".$as_codtipper."'";
			$ls_groupcargos=" GROUP BY sno_hpersonalnomina.codper, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper ";
		}

		$ls_sql="SELECT sno_hpersonalnomina.codper ".
				"  FROM sno_hpersonalnomina, sno_hperiodo, sno_hnomina ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				$ls_criterio.
				"   AND sno_hnomina.tipnom <> 7 ".
				"   AND sno_hnomina.espnom = 0 ".
				"   AND sno_hnomina.ctnom = 0 ".
				"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
				"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
				"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
				"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0406_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ai_cargoreal=$ai_cargoreal+1;
			}
			$this->io_sql->free_result($rs_data);
		}
		if($lb_valido)
		{
			$ls_sql="SELECT sum(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina, sno_hsalida, sno_hperiodo, sno_hnomina ".
					" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					$ls_criterio.
					"   AND sno_hnomina.tipnom <> 7 ".
					"   AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) >= '".substr($as_rango,0,2)."'".
					"   AND substr(cast(sno_hperiodo.fechasper as char(10)),6,2) <= '".substr($as_rango,3,2)."'".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
					"   AND sno_hnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hnomina.anocurnom = sno_hperiodo.anocur ".
					"   AND sno_hnomina.peractnom = sno_hperiodo.codperi ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_comparado0406_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_montoreal=$row["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}
		}		
		$arrResultado['ai_cargoreal']=$ai_cargoreal;
		$arrResultado['ai_montoreal']=$ai_montoreal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_comparado0406_real
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_listadocomponente_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
									   $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
									   $as_masculino,$as_femenino,$as_codcomp,$as_orden)
	{		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadocomponente_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_masculino // Solo el personal masculino
		//	  			   as_femenino // Solo el personal femenino
		//	    		   as_codcompdes // Código del componente que se empieza a filtrar
		//	  			   as_codcomphas // Código del componente termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 18/04/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		
		if (!empty($as_codcomp))
		{ 
			$ls_criterio=$ls_criterio."   AND sno_personal.codcom='".$as_codcomp."'";
			
		}		 
		
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				


		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper,".
		        "       sno_componente.codcom, sno_componente.descom, ".						
				"       (SELECT codran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
				"       (SELECT desran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as desrango, ".
				"		(SELECT denasicar FROM sno_asignacioncargo ".
				"       WHERE sno_personalnomina.codemp=sno_personal.codemp ".
				"         AND sno_personalnomina.codemp = sno_asignacioncargo.codemp  ".
				"		  AND sno_personalnomina.codnom = sno_asignacioncargo.codnom  ".
				"         AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar) AS denasicar, ".
				"       (SELECT descar FROM sno_cargo ".
				"		WHERE sno_personalnomina.codemp=sno_personal.codemp ".
				"         AND sno_personalnomina.codemp = sno_cargo.codemp ".
				"         AND sno_personalnomina.codnom = sno_cargo.codnom ".
				"         AND sno_personalnomina.codcar = sno_cargo.codcar) AS descar, ". 
				"		(select racnom from sno_nomina ".
				"		where sno_nomina.codemp=sno_personalnomina.codemp ".
				"		  and sno_nomina.codnom=sno_personalnomina.codnom) as rac, sno_personal.numexpper ".
				"  FROM sno_personal, sno_personalnomina, sno_componente, sno_rango".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio.
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_componente.codemp=sno_personal.codemp ".
				"   AND sno_componente.codcom=sno_personal.codcom ".
				"   AND sno_rango.codemp= sno_personal.codemp     ".
				"   AND sno_rango.codcom= sno_personal.codcom     ".
				"   AND sno_rango.codran= sno_personal.codran     ".$ls_orden; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadocomponente_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadopersonalunidadadm_personal
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_buscar_componentes($as_codcompdes,$as_codcomphas)
    {
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_componentes
		//         Access: public (desde la clase sigesp_snorh_rpp_listadocomponente)  
		//	    Arguments: as_codcompdes // Código del componente donde se empieza a filtrar
		//	  			   as_codcomphas // Código del componente donde se termina de filtrar			//	    		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del componente
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 18/04/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     	$lb_valido=true;
		$ls_criterio="";
	 
	 	if (!empty($as_codcompdes))
		{ 
			$ls_criterio=$ls_criterio."   AND sno_componente.codcom between '".$as_codcompdes."'";			
		}
	 	if (!empty($as_codcomphas))
		{ 
			$ls_criterio=$ls_criterio."   AND '".$as_codcomphas."'";
		}
	 
		 $ls_sql="  SELECT sno_componente.codcom, sno_componente.descom ".
	             "    FROM sno_componente ".
	             "   WHERE sno_componente.codemp='".$this->ls_codemp."'".$ls_criterio; 
	 
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_componentes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{ 
				$this->ds_componente->data=$this->io_sql->obtener_datos($rs_data);		
			}		
        }
        return $lb_valido;
   	}//fin function uf_buscar_componentes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_salarios_ivss($as_codper,$ano1,$ano2,$rs_data)
	{
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_salarios_ivss
		//         Access: public (desde la clase sigesp_snorh_rpp_constanciatrabajo_seguro)  
		//	    Arguments: as_codperdes // Código del personal desde
		//	  			   as_codperhas // Código del personal hasta				
		//                 ano1 // año desde 
		//   		       ano2 // año hasta
		//                 rs_data // 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca los salarios mensuales en los ultimos 6 años
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 09/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       	$lb_valido=true;
		$ls_valor=$this->uf_select_config('SNO','NOMINA','NOMINAS_ESPECIALES_IVSS','0','C');	 
		$ls_criterio="";
		$ls_sql= "SELECT sno_personalnomina.codper, substr(cast(sno_sueldoshistoricos.fecsue as char(10)),1,4) AS anocur, ".
				 "		 substr(cast(sno_sueldoshistoricos.fecsue as char(10)),6,2) as mes,(30) as dias, ".
				 " 		 MAX(sno_sueldoshistoricos.sueint) as sueintper, MAX(sno_sueldoshistoricos.suebas) as suebas, MAX(sno_personal.nomper) as nomper, MAX(sno_personal.apeper) as apeper, ".
				 " 		 sno_personal.cedper as cedper, MAX(sno_personal.nacper) as nacper, MAX(sno_personal.fecingper) as fecingper, ".
				 " 		 MAX(sno_personal.fecegrper) as fecegrper ".
				 "  FROM sno_personalnomina, sno_sueldoshistoricos, sno_personal ".
				 " WHERE sno_personalnomina.codemp=sno_sueldoshistoricos.codemp ".
				 "   AND sno_personalnomina.codper=sno_sueldoshistoricos.codper ".
				 "   AND substr(cast(sno_sueldoshistoricos.fecsue as char(10)),1,4)>='".$ano2."' ".
				 "   AND substr(cast(sno_sueldoshistoricos.fecsue as char(10)),1,4)<='".$ano1."' ".
				 "   AND sno_personalnomina.codemp=sno_personal.codemp ".
				 "   AND sno_personalnomina.codper=sno_personal.codper ".
			 	 "   AND sno_personalnomina.codemp='".$this->ls_codemp."' ". 
				 "   AND sno_personalnomina.codper='".$as_codper."' ".
				 " GROUP BY sno_personalnomina.codper, sno_personal.cedper, substr(cast(sno_sueldoshistoricos.fecsue as char(10)),1,4), ".
				 "       substr(cast(sno_sueldoshistoricos.fecsue as char(10)),6,2)".
				 " ORDER BY substr(cast(sno_sueldoshistoricos.fecsue as char(10)),1,4),substr(cast(sno_sueldoshistoricos.fecsue as char(10)),6,2) ";		
		$rs_data=$this->io_sql->select($ls_sql);		
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_salarios_ivss ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}		
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// fin function uf_salarios_ivss
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestacionantiguedad_afectacionpresupuestaria($as_codnom,$as_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedad_afectacionpresupuestaria
		//         Access: public (desde la clase sigesp_snorh_rpp_contableprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la afectación presupuestaria del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mes=str_pad($as_mescurper,3,"0",0);
		$ls_sql="SELECT sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
				"		sno_dt_spg.spg_cuenta, spg_cuentas.denominacion, sno_dt_spg.monto ".
				"  FROM sno_dt_spg, spg_cuentas  ".  
				" WHERE sno_dt_spg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_spg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_spg.codperi = '".$li_mes."' ".
				"   AND sno_dt_spg.tipnom = 'P' ".
				"   AND sno_dt_spg.codemp = spg_cuentas.codemp ".
				"   AND sno_dt_spg.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_dt_spg.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_dt_spg.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_dt_spg.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_dt_spg.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_dt_spg.estcla = spg_cuentas.estcla ".
				"   AND sno_dt_spg.spg_cuenta = spg_cuentas.spg_cuenta ".
				" ORDER BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
				"		   sno_dt_spg.estcla, sno_dt_spg.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestacionantiguedad_afectacionpresupuestaria ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_prestacionantiguedad_afectacionpresupuestaria
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestacionantiguedad_afectacioncontable($as_codnom,$as_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedad_afectacioncontable
		//         Access: public (desde la clase sigesp_snorh_rpp_contableprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la afectación contable del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mes=str_pad($as_mescurper,3,"0",0);
		$ls_sql="SELECT sno_dt_scg.sc_cuenta, sno_dt_scg.debhab, scg_cuentas.denominacion, sno_dt_scg.monto ".
				"  FROM sno_dt_scg, scg_cuentas  ".  
				" WHERE sno_dt_scg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_scg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_scg.codperi = '".$li_mes."' ".
				"   AND sno_dt_scg.tipnom = 'P' ".
				"   AND sno_dt_scg.codemp = scg_cuentas.codemp ".
				"   AND sno_dt_scg.sc_cuenta = scg_cuentas.sc_cuenta ".
				" ORDER BY sno_dt_scg.debhab, sno_dt_scg.sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestacionantiguedad_afectacioncontable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_prestacionantiguedad_afectacioncontable
	//-----------------------------------------------------------------------------------------------------------------------------------
   
	//-----------------------------------------------------------------------------------------------------------------------------------
	 function uf_seleccionar_quincenas($as_codnom,$as_perides,$as_perihas,$as_codper,$as_priqui,$as_segqui)
	 {
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_ipsfa)  
		//	    Arguments: as_codnom //código de la nomina
		//				   as_perides // código del periodo desde
		//                 as_perihas // còdigo del periodo hasta
		//                 as_codper // código de la persona
		//                 as_priqui // valor que tiene el valor de primera quincena
		//				   as_segqui // valor que tiene el valor de segunda quincena 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la informaciòn de la primera y segunda quincena de la nomina de una persona
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 21/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;			
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];	
		$ls_sql="  SELECT sum(priquires) AS priquires, sum(segquires) AS segquires         ".
				"    FROM sno_hresumen                    ".
				"   WHERE sno_hresumen.codemp='".$ls_codemp."'         ". 
				"     AND sno_hresumen.codper='".$as_codper."'  ".
				"     AND sno_hresumen.codperi BETWEEN '".$as_perides."' AND '".$as_perihas."'".
				"     AND sno_hresumen.codnom='".$as_codnom."'"; 						
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_quincenas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_priqui=$row["priquires"];
				$as_segqui=$row["segquires"];		
			}
			else
			{
				$lb_valido=false;
				$as_priqui="";
				$as_segqui="";	
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['as_priqui']=$as_priqui;
		$arrResultado['as_segqui']=$as_segqui;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_seleccionar_quincenas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_obtener_valor_concepto($as_codnom,$as_perides,$as_perihas,$as_codper,$as_concepto,$as_valor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_obtener_valor_concepto
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_ipsfa)  
		//	    Arguments: as_codnom  // código de la nomina
		//				   as_perides // código periodo desde
		//				   as_perihas // código del periodo hasta
		//                 as_codper  // código del personal
		//				   as_concepto // còdigo del concepto
		//				   as_valor // valor obtenido
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la informaciòn de la primera y segunda quincena de la nomina de una persona
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 21/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_valor=0;				
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];		
		$ls_sql=" SELECT sno_hconcepto.codconc, sno_hconcepto.titcon as nomcon, sno_hsalida.valsal AS valsal ".
				"	FROM sno_hsalida, sno_hconcepto ".
				"  WHERE sno_hsalida.codemp='".$ls_codemp."' ". 
				"	 AND sno_hsalida.codnom='".$as_codnom."'  ". 
				"	 AND sno_hsalida.codperi BETWEEN '".$as_perides."' AND '".$as_perihas."'". 
				"	 AND sno_hconcepto.codconc='".$as_concepto."' ".
				"	 AND sno_hsalida.codper='".$as_codper."' ". 
				"	 AND sno_hsalida.valsal<>0 ".
				"	 AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"	 AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"	 AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"  GROUP BY sno_hconcepto.codconc, sno_hconcepto.titcon,sno_hsalida.valsal ".
				"  ORDER BY sno_hconcepto.codconc   "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_obtener_valor_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_valor=$row["valsal"];						
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_valor_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
     function uf_seleccionar_nominabanco($as_codnodes,$as_codnomhas, $as_perides, $as_perihas,$as_bandes,$as_banhas, $as_orden)	
	 { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
		//       Function: uf_seleccionar_nominabanco
		//         Access: public (desde la clase sigesp_snorh_rpp_depositobanco)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las nomina y los bancos de tales nominas
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 22/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_orden="";
		if ($as_orden==1)
		{
			$ls_orden="ORDER BY sno_hnomina.codnom,sno_banco.codban";
		}
		else
		{
			$ls_orden="ORDER BY  sno_banco.codban,sno_hnomina.peractnom";
		}		
	    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		 
		$ls_sql="  SELECT max(sno_hnomina.codnom) as codnom, ". 
				"         max(sno_hnomina.desnom) as desnom, max(sno_banco.codban) as codban, ".
				"         max(scb_banco.nomban) as nomban,  ". 
				"         max(sno_banco.codcueban) as codcueban ". 
				"    FROM sno_hnomina  ". 
				"    JOIN sno_banco on (sno_banco.codemp=sno_hnomina.codemp  ". 
				"	  AND sno_banco.codnom=sno_hnomina.codnom  ". 
				"	  AND sno_banco.codperi=sno_hnomina.peractnom)  ". 
			    "    JOIN scb_banco on (scb_banco.codemp=sno_banco.codemp  ". 
				"	  AND scb_banco.codban=sno_banco.codban)  ". 
			    "   WHERE sno_hnomina.codemp='".$ls_codemp."'".
			    "     AND sno_hnomina.codnom BETWEEN '".$as_codnodes."' AND '".$as_codnomhas."'".
			    "     AND sno_hnomina.codnom BETWEEN '".$as_codnodes."' AND '".$as_codnomhas."'".
				"	  AND sno_hnomina.peractnom BETWEEN '".$as_perides."' AND '".$as_perihas."'". 
				"     AND sno_banco.codban BETWEEN '".$as_bandes."' AND '".$as_banhas."'".
				"	  AND sno_hnomina.espnom='0'       ".                                   
			    "   GROUP BY sno_hnomina.codnom,sno_banco.codban,sno_banco.codcueban  ".$ls_orden;		    
		 $rs_data=$this->io_sql->select($ls_sql);
		 if($rs_data===false)
		 {
		 	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_nominabanco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		 else
		 {
		  	if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_nominas->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;	 
	 }////end function uf_seleccionar_nominabanco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_depositos_bancarios($as_codnom,$as_banc,$as_codperides,$as_codperihas,$as_curenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_depositos_bancarios
		//         Access: public (desde la clase sigesp_snorh_rpp_depositobancario)  
		//	    Arguments: as_codnom // codigo de la nómina
		// 				   as_banc // código del banco
		//                 as_codperides // código periodo desde
		//				   as_codperihas // còdigo perioso hasta
		//				   as_curenta // cuenta bancaria
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la informaciòn sobre los depositors bancarios por nomina, cuenta y periodo
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 18/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;	    
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_sql=" SELECT sno_hresumen.codnom, sno_hpersonalnomina.codban, sno_hresumen.codperi, ".
				"		 SUM(sno_hresumen.monnetres) as monnetresahorro,   ".
				"		 SUM(sno_hresumen.priquires) as priquiresahorro,   ".
				"		 SUM(sno_hresumen.segquires) as segquiresahorro,   ".
				"		 SUM(0) as monnetrescorriente,   ".
				"		 SUM(0) as priquirescorriente,   ".
				"		 SUM(0) as segquirescorriente    ".
				"	FROM sno_hresumen  ".
				"	JOIN sno_hpersonalnomina on (sno_hpersonalnomina.codemp=sno_hresumen.codemp   ".
				"	 AND sno_hpersonalnomina.codnom=sno_hresumen.codnom     ".
				"	 AND sno_hpersonalnomina.codperi=sno_hresumen.codperi   ".
				"	 AND sno_hpersonalnomina.codper=sno_hresumen.codper     ".
				"	 AND sno_hpersonalnomina.anocur=sno_hresumen.anocur)    ".
				"	JOIN sno_banco ON (sno_banco.codemp=sno_hpersonalnomina.codemp       ".
				"	 AND sno_banco.codban=sno_hpersonalnomina.codban            ".
				"	 AND sno_banco.codnom=sno_hpersonalnomina.codnom            ".
                "    AND sno_banco.codperi=sno_hpersonalnomina.codperi)   ".
				"  WHERE sno_hresumen.codemp='".$ls_codemp."'  ".
				"	 AND sno_hresumen.monnetres > 0  ".
				"	 AND sno_hpersonalnomina.tipcuebanper='A'   ".
				"	 AND sno_hresumen.codnom='".$as_codnom."' ".
				"	 AND sno_hresumen.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."'	 ".							
				"	 AND sno_hpersonalnomina.pagbanper=1    ".
				"	 AND sno_hpersonalnomina.pagefeper=0   ".
				"	 AND sno_hpersonalnomina.pagtaqper=0   ".
				"	 AND sno_hpersonalnomina.codban='".$as_banc."' ".
				"	 AND sno_banco.codcueban='".$as_curenta."'  ".
				"	GROUP BY sno_hresumen.codnom, sno_hpersonalnomina.codban,sno_hresumen.codperi	 ".				
				"	UNION ".
				"  SELECT max(sno_hresumen.codnom) as nomina,sno_hpersonalnomina.codban,sno_hresumen.codperi, ".
				"		   SUM(0) as monnetresahorro, ".
				"		   SUM(0) as priquiresahorro, ".
				"		   SUM(0) as segquiresahorro, ".
				"		   SUM(sno_hresumen.monnetres) as monnetrescorriente, ".
				"		   SUM(sno_hresumen.priquires) as priquirescorriente, ".
				"		   SUM(sno_hresumen.segquires) as segquirescorriente ".
				"	FROM sno_hresumen ".
				"	JOIN sno_hpersonalnomina on (sno_hpersonalnomina.codemp=sno_hresumen.codemp  ".
				"	 AND sno_hpersonalnomina.codnom=sno_hresumen.codnom  ".
				"	 AND sno_hpersonalnomina.codperi=sno_hresumen.codperi  ".
				"	 AND sno_hpersonalnomina.codper=sno_hresumen.codper  ".
				"	 AND sno_hpersonalnomina.anocur=sno_hresumen.anocur)  ".
				"	JOIN sno_banco ON (sno_banco.codemp=sno_hpersonalnomina.codemp ".
				"	 AND sno_banco.codban=sno_hpersonalnomina.codban ".
				"	 AND sno_banco.codnom=sno_hpersonalnomina.codnom ".
                "    AND sno_banco.codperi=sno_hpersonalnomina.codperi)  ".
				"  WHERE sno_hresumen.codemp='".$ls_codemp."' ".
				"	 AND sno_hresumen.monnetres > 0  ".
				"	 AND sno_hpersonalnomina.tipcuebanper='C'   ".
				"	 AND sno_hresumen.codnom='".$as_codnom."' ".
				"	 AND sno_hresumen.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."'	 ".						
				"	 AND sno_hpersonalnomina.pagbanper=1   ". 
				"	 AND sno_hpersonalnomina.pagefeper=0   ".
				"	 AND sno_hpersonalnomina.pagtaqper=0   ".
				"	 AND sno_hpersonalnomina.codban='".$as_banc."'  ".
				"	 AND sno_banco.codcueban='".$as_curenta."'  ".
				"  GROUP BY sno_hresumen.codnom, sno_hpersonalnomina.codban,sno_hresumen.codperi";  
		$rs_data=$this->io_sql->select($ls_sql); 
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_depositos_bancarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_depositos->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_depositos->group_by(array('0'=>'codnom','1'=>'codban'),
				                              array('0'=>'monnetresahorro','1'=>'monnetrescorriente',
				                                    '2'=>'priquiresahorro','3'=>'segquiresahorro',
													'4'=>'priquirescorriente','5'=>'segquirescorriente'),
											  'monnetresahorro');				
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_depositos_bancarios
	///----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_total_depositos_bancarios($as_codnomdes,$as_codnomhas,$as_bancdes,$as_banchas,$as_codperides,$as_codperihas)
	{		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_total_depositos_bancarios
		//         Access: public (desde la clase sigesp_snorh_rpp_depositobancario)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la informaciòn sobre los depositors bancarios por nomina, cuenta y periodo
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 23/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;	    
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];		
		$ls_sql=" SELECT sno_hpersonalnomina.codban,  ".
				"	     SUM(sno_hresumen.monnetres) as monnetresahorro, ".
				"	     SUM(sno_hresumen.priquires) as priquiresahorro, ".
				"	     SUM(sno_hresumen.segquires) as segquiresahorro, ".
				"	     SUM(0) as monnetrescorriente, SUM(0) as priquirescorriente, ".
				"	     SUM(0) as segquirescorriente ".
				"   FROM sno_hresumen ".
				"	JOIN sno_hpersonalnomina on (sno_hpersonalnomina.codemp=sno_hresumen.codemp ".
				"	 AND sno_hpersonalnomina.codnom=sno_hresumen.codnom ".
				"	 AND sno_hpersonalnomina.codperi=sno_hresumen.codperi ".
				"	 AND sno_hpersonalnomina.codper=sno_hresumen.codper   ".
				"	 AND sno_hpersonalnomina.anocur=sno_hresumen.anocur)  ".
				"	JOIN sno_banco ON (sno_banco.codemp=sno_hpersonalnomina.codemp  ".
				"	 AND sno_banco.codban=sno_hpersonalnomina.codban  ".
				"	 AND sno_banco.codnom=sno_hpersonalnomina.codnom  ".
				"	 AND sno_banco.codperi=sno_hpersonalnomina.codperi)  ".
				"  WHERE sno_hresumen.codemp='".$ls_codemp."' ".
				"	 AND sno_hresumen.monnetres > 0 ".
				"	 AND sno_hpersonalnomina.tipcuebanper='A'".
				"	 AND sno_hresumen.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."'". 
				"	 AND sno_hpersonalnomina.pagbanper=1 ".
				"	 AND sno_hpersonalnomina.pagefeper=0 ".
				"	 AND sno_hpersonalnomina.pagtaqper=0 ".
				"	 AND sno_hpersonalnomina.codban BETWEEN '".$as_bancdes."' AND '".$as_banchas."' ".
				"	 AND sno_hpersonalnomina.codnom BETWEEN '".$as_codnomdes."' AND '".$as_codnomhas."' ".
				"  GROUP BY sno_hpersonalnomina.codban ".
				"  UNION  ".
				"  SELECT sno_hpersonalnomina.codban, SUM(0) as monnetresahorro, ".
				"		  SUM(0) as priquiresahorro,".
				"		  SUM(0) as segquiresahorro,".
				"		  SUM(sno_hresumen.monnetres) as monnetrescorriente,".
				"		  SUM(sno_hresumen.priquires) as priquirescorriente,".
				"		  SUM(sno_hresumen.segquires) as segquirescorriente".
				"	 FROM sno_hresumen ".
				"	 JOIN sno_hpersonalnomina on (sno_hpersonalnomina.codemp=sno_hresumen.codemp ".
				"	  AND sno_hpersonalnomina.codnom=sno_hresumen.codnom ".
				"	  AND sno_hpersonalnomina.codperi=sno_hresumen.codperi ".
				"	  AND sno_hpersonalnomina.codper=sno_hresumen.codper".
				"	  AND sno_hpersonalnomina.anocur=sno_hresumen.anocur) ".
				"	 JOIN sno_banco ON (sno_banco.codemp=sno_hpersonalnomina.codemp  ".
				"	  AND sno_banco.codban=sno_hpersonalnomina.codban   ".
				"	  AND sno_banco.codnom=sno_hpersonalnomina.codnom   ".
				"	  AND sno_banco.codperi=sno_hpersonalnomina.codperi) ".
				"	WHERE sno_hresumen.codemp='".$ls_codemp."' ".
				"	  AND sno_hresumen.monnetres > 0 ".
				"	  AND sno_hpersonalnomina.tipcuebanper='C'".
				"	  AND sno_hresumen.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ".
				"	  AND sno_hpersonalnomina.pagbanper=1 ".
				"	  AND sno_hpersonalnomina.pagefeper=0 ".
				"	  AND sno_hpersonalnomina.pagtaqper=0 ".
				"	  AND sno_hpersonalnomina.codban BETWEEN '".$as_bancdes."' AND '".$as_banchas."' ".
				"	  AND sno_hpersonalnomina.codnom BETWEEN '".$as_codnomdes."' AND '".$as_codnomhas."' ".
				"	GROUP BY sno_hpersonalnomina.codban ";  
		$rs_data=$this->io_sql->select($ls_sql); 
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_total_depositos_bancarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_depositos2->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_depositos2->group_by(array('0'=>'codban'),
				                               array('0'=>'monnetresahorro','1'=>'monnetrescorriente',
				                                    '2'=>'priquiresahorro','3'=>'segquiresahorro',
													'4'=>'priquirescorriente','5'=>'segquirescorriente'),
											  'monnetresahorro');				
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_total_depositos_bancarios
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_cuadreprestacionantiguedad($as_codnom,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreprestacionantiguedad
		//         Access: public (desde la clase sigesp_snorh_r_cuadreprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan la prestación antiguedad
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mes=str_pad($as_mescurper,3,"0",0);
		$ls_sql="SELECT sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, sno_dt_spg.estcla, ".
				"		sno_dt_spg.spg_cuenta, sno_dt_spg.monto ".
				"  FROM sno_dt_spg  ".  
				" WHERE sno_dt_spg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_spg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_spg.codperi = '".$li_mes."' ".
				"   AND sno_dt_spg.tipnom = 'P' ".
				" ORDER BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
				"		   sno_dt_spg.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreprestacionantiguedad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codestpro1=$row["codestpro1"];
				$ls_codestpro2=$row["codestpro2"];
				$ls_codestpro3=$row["codestpro3"];
				$ls_codestpro4=$row["codestpro4"];
				$ls_codestpro5=$row["codestpro5"];
				$ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				$ls_estcla=$row["estcla"];
				$ls_cuentapresupuesto=$row["spg_cuenta"];
				$li_total=$row["monto"];
				$ls_sql="SELECT denominacion ".
						"  FROM spg_cuentas ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND status = 'C'".
						"   AND codestpro1 = '".$ls_codestpro1."'".
						"   AND codestpro2 = '".$ls_codestpro2."'".
						"   AND codestpro3 = '".$ls_codestpro3."'".
						"   AND codestpro4 = '".$ls_codestpro4."'".
						"   AND codestpro5 = '".$ls_codestpro5."'".
						"   AND estcla='".$ls_estcla."'".
						"   AND spg_cuenta = '".$ls_cuentapresupuesto."'";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreconceptoaporte_aportes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS->insertRow("programatica",$ls_programatica);
						$this->DS->insertRow("spg_cuenta",$ls_cuentapresupuesto);
						$this->DS->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreprestacionantuguedad
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_seleccionar_nominaunidad($as_codnomdes,$as_codnomhas,$as_codperides,$as_codperihas,$as_orden,$ad_aniodesde,$ad_aniohasta,
									     $as_coddeddes,$as_coddedhas,$as_codtipperdes,$as_codtipperhas)	
    {	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function:uf_seleccionar_nominaunidad
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnomdes // código de la nómina desde
		//				   as_codnomhas // código de la nómina hasta
		//				   as_codperides   // còdigo del periodo desde
		//				   as_codperihas   // código del periodo hasta
		//				   as_orden     // forma de ordenamiento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las nomina y las unidades Administrativas
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;
		$ls_criterio="";
		switch ($as_orden)
		{
			case "1":
				$ls_orden="ORDER BY sno_hnomina.codnom";
			break;
	
			case "3":
				$ls_orden="ORDER BY desnom ";
			break;
			
			default:
				$ls_orden="ORDER BY sno_hnomina.codnom";
			break;	
		}
		if (($as_coddeddes!="")&&($as_coddedhas!="")&&($as_codtipperdes!="")&&($as_codtipperhas!=""))
		{
			$ls_criterio="INNER JOIN sno_hpersonalnomina ".
						 "   ON sno_hpersonalnomina.codnom BETWEEN '".$as_codnomdes."' AND '".$as_codnomhas."' ".
						 "	AND sno_hpersonalnomina.anocur BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' ".				
						 "	AND sno_hpersonalnomina.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ".
						 "  AND sno_hpersonalnomina.codded BETWEEN '".$as_coddeddes."' AND '".$as_coddedhas."'".				
						 "  AND sno_hpersonalnomina.codtipper BETWEEN '".$as_codtipperdes."' AND '".$as_codtipperhas."'".	
						 "  AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".			
						 "  AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".			
						 "  AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".			
						 "  AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ";			
		}
		$ls_sql="SELECT sno_hnomina.codnom, max(sno_hnomina.desnom) as desnom, max(sno_hnomina.racnom) as racnom ".				
		   	    "  FROM sno_hnomina			 ".	
				$ls_criterio.							 
				" WHERE sno_hnomina.codemp='".$this->ls_codemp."'".
				"   AND sno_hnomina.codnom BETWEEN '".$as_codnomdes."' AND '".$as_codnomhas."'". 
				"	AND sno_hnomina.anocurnom BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' 	".				
				"	AND sno_hnomina.peractnom BETWEEN '".$as_codperides."' AND '".$as_codperihas."' 	".				
				"	AND sno_hnomina.espnom='0'".				 
				" GROUP BY sno_hnomina.codnom  ".
				$ls_orden; 
		 $this->rs_data=$this->io_sql->select($ls_sql);
		 if($this->rs_data===false)
		 {
		 	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_nominaunidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		 else
		 {
		 	if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		 }		
		 return $lb_valido;	 
	 }// end function uf_seleccionar_nominaunidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_pagos_unidad($as_codnom,$as_codperides,$as_codperihas,$as_unidaddes,$as_unidadhas,$ad_aniodesde,$ad_aniohasta,
							 $as_coddeddes,$as_coddedhas,$as_codtipperdes,$as_codtipperhas,$as_orden)	
    {	
	 	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagos_unidad
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnom // código de la nómina
		// 				   as_codperides // código del periodo desde
		//				   as_codperihas // código del periodo hasta
		//				   as_unidaddes // código de la unidad desde
		//				   as_unidadhas // código de la unidad hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca los pagos por nomina en cada unidad
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_criterio="";
		switch ($as_orden)
		{
			case "2":
				$ls_orden="ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
			break;
	
			case "4":
				$ls_orden="ORDER BY sno_hunidadadmin.desuniadm";
			break;
			
			default:
				$ls_orden="ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
			break;	
		}
		if (!empty($as_unidaddes))
		{
			if (!empty($as_unidadhas))
			{
			 	$ls_criterio="  AND (sno_hunidadadmin.minorguniadm ".
			                 "  BETWEEN '".substr($as_unidaddes,0,4)."' AND '".substr($as_unidadhas,0,4)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.ofiuniadm ".
			                              "  BETWEEN '".substr($as_unidaddes,5,2)."'  AND '".substr($as_unidadhas,5,2)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.uniuniadm ".
			                              "  BETWEEN '".substr($as_unidaddes,8,2)."'  AND '".substr($as_unidadhas,8,2)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.depuniadm ".
			                              "  BETWEEN '".substr($as_unidaddes,11,2)."' AND '".substr($as_unidadhas,11,2)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.prouniadm ".
			                              "  BETWEEN '".substr($as_unidaddes,14,2)."' AND '".substr($as_unidadhas,14,2)."')";
			 }
		}		 	
		if (($as_coddeddes!="")&&($as_coddedhas!="")&&($as_codtipperdes!="")&&($as_codtipperhas!=""))
		{
			$ls_criterio=$ls_criterio."  AND sno_hpersonalnomina.codded BETWEEN '".$as_coddeddes."' AND '".$as_coddedhas."'".				
									  "  AND sno_hpersonalnomina.codtipper BETWEEN '".$as_codtipperdes."' AND '".$as_codtipperhas."'";			
		}
		$ls_sql="  SELECT sno_hpersonalnomina.codnom,  SUM(sno_hresumen.monnetres) as monnetres, COUNT(sno_hresumen.codper) as totalpersonal, ".
				"         max(sno_hunidadadmin.desuniadm) as desuniadm	,".	
				"         max(sno_hunidadadmin.minorguniadm) as minorguniadm, ".
				"         max(sno_hunidadadmin.ofiuniadm) as ofiuniadm, ".
				"         max(sno_hunidadadmin.uniuniadm) as uniuniadm, ".
				"         max(sno_hunidadadmin.depuniadm) as depuniadm, ".
				"         max(sno_hunidadadmin.prouniadm) as prouniadm ".
			    "    FROM sno_hresumen ".
			    "    JOIN sno_hpersonalnomina ".
				"      ON sno_hpersonalnomina.codemp=sno_hresumen.codemp ".
				"	  AND sno_hpersonalnomina.codnom=sno_hresumen.codnom ".
				"	  AND sno_hpersonalnomina.codperi=sno_hresumen.codperi ".
				"	  AND sno_hpersonalnomina.codper=sno_hresumen.codper ".
				"	  AND sno_hpersonalnomina.anocur=sno_hresumen.anocur ".
			    "    JOIN sno_hunidadadmin ".
				"      ON sno_hunidadadmin.codemp=sno_hpersonalnomina.codemp ".
				"	  AND sno_hunidadadmin.codnom=sno_hpersonalnomina.codnom ".
				"	  AND sno_hunidadadmin.anocur=sno_hpersonalnomina.anocur ".
				"	  AND sno_hunidadadmin.codperi=sno_hpersonalnomina.codperi ".
				"	  AND sno_hunidadadmin.minorguniadm=sno_hpersonalnomina.minorguniadm ".
				"	  AND sno_hunidadadmin.ofiuniadm=sno_hpersonalnomina.ofiuniadm ".
				"	  AND sno_hunidadadmin.uniuniadm=sno_hpersonalnomina.uniuniadm ".
				"	  AND sno_hunidadadmin.depuniadm=sno_hpersonalnomina.depuniadm ".
				"	  AND sno_hunidadadmin.prouniadm=sno_hpersonalnomina.prouniadm ".
			    "   WHERE sno_hresumen.codemp='".$this->ls_codemp."'". 
				"	  AND sno_hresumen.monnetres > 0  ".				 		  
				"	  AND sno_hresumen.anocur BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' 	".				
				"	  AND sno_hresumen.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ".
				"	  AND sno_hpersonalnomina.codnom='".$as_codnom."' ".
				$ls_criterio.			  
			    "   GROUP BY sno_hpersonalnomina.codnom, sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm,sno_hunidadadmin.desuniadm ".
				$ls_orden;
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
		 	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagos_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;	 
	 }// end function uf_pagos_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_pagos_unidad_totales($as_codnomdes,$as_codnomhas,$as_codperides,$as_codperihas,$as_unidaddes,$as_unidadhas,$ad_aniodesde,
									 $ad_aniohasta,$as_coddeddes,$as_coddedhas,$as_codtipperdes,$as_codtipperhas,$as_orden)	
    {	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function:uf_pagos_unidad_totales
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnomdes // código de la nòmina desde
		//				   as_codnomhas // código de la nómina hasta
		//                 as_codperides // código del periodo desde
		// 				   as_codperihas  // código del periodo hasta
		//				   as_unidaddes // código de la unidad desde
		//				   as_unidadhas // código de la unidad hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca los pagos por nomina en cada unidad
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	    $lb_valido=true;
		$ls_criterio="";
		switch ($as_orden)
		{
			case "2":
				$ls_orden="ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
			break;
	
			case "4":
				$ls_orden="ORDER BY desuniadm";
			break;
			
			default:
				$ls_orden="ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ";
			break;	
		}
		if (!empty($as_unidaddes))
		{
			if (!empty($as_unidadhas))
			{
				$ls_criterio="  AND (sno_hunidadadmin.minorguniadm ".
			               "  BETWEEN '".substr($as_unidaddes,0,4)."' AND '".substr($as_unidadhas,0,4)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.ofiuniadm ".
			                            "  BETWEEN '".substr($as_unidaddes,5,2)."'  AND '".substr($as_unidadhas,5,2)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.uniuniadm ".
			                            "  BETWEEN '".substr($as_unidaddes,8,2)."'  AND '".substr($as_unidadhas,8,2)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.depuniadm ".
			                            "  BETWEEN '".substr($as_unidaddes,11,2)."' AND '".substr($as_unidadhas,11,2)."'";
			  	$ls_criterio=$ls_criterio."  OR sno_hunidadadmin.prouniadm ".
			                            "  BETWEEN '".substr($as_unidaddes,14,2)."' AND '".substr($as_unidadhas,14,2)."')";
			 }
		 }		   			
		if (($as_coddeddes!="")&&($as_coddedhas!="")&&($as_codtipperdes!="")&&($as_codtipperhas!=""))
		{
			$ls_criterio=$ls_criterio."  AND sno_hpersonalnomina.codded BETWEEN '".$as_coddeddes."' AND '".$as_coddedhas."'".				
									  "  AND sno_hpersonalnomina.codtipper BETWEEN '".$as_codtipperdes."' AND '".$as_codtipperhas."'";			
		}
	     $ls_sql="  SELECT SUM(sno_hresumen.monnetres) as monnetres, COUNT(sno_hresumen.codper) as totalpersonal, ".
				 "		   count(sno_hresumen.codper) as cantidad, max(sno_hunidadadmin.desuniadm) as desuniadm, ".
				 "         max(sno_hunidadadmin.minorguniadm) as minorguniadm, ".
				 "         max(sno_hunidadadmin.ofiuniadm) as ofiuniadm, ".
				 "         max(sno_hunidadadmin.uniuniadm) as uniuniadm, ".
				 "         max(sno_hunidadadmin.depuniadm) as depuniadm, ".
				 "         max(sno_hunidadadmin.prouniadm) as prouniadm ".
				 "   FROM sno_hresumen ".
				 "	 JOIN sno_hpersonalnomina ".
				 "     ON (sno_hpersonalnomina.codemp=sno_hresumen.codemp ".
				 "	  AND sno_hpersonalnomina.codnom=sno_hresumen.codnom ".
				 "	  AND sno_hpersonalnomina.codperi=sno_hresumen.codperi ".
				 "	  AND sno_hpersonalnomina.codper=sno_hresumen.codper ".
				 "    AND sno_hpersonalnomina.anocur=sno_hresumen.anocur) ".
				 "	 JOIN sno_hunidadadmin ".
				 "     ON (sno_hunidadadmin.codemp=sno_hpersonalnomina.codemp ".
				 "	  AND sno_hunidadadmin.codnom=sno_hpersonalnomina.codnom  ".
				 "	  AND sno_hunidadadmin.anocur=sno_hpersonalnomina.anocur ".
				 "	  AND sno_hunidadadmin.codperi=sno_hpersonalnomina.codperi  ".
				 "	  AND sno_hunidadadmin.minorguniadm=sno_hpersonalnomina.minorguniadm ".
				 "	  AND sno_hunidadadmin.ofiuniadm=sno_hpersonalnomina.ofiuniadm ".
				 "	  AND sno_hunidadadmin.uniuniadm=sno_hpersonalnomina.uniuniadm ".
				 "	  AND sno_hunidadadmin.depuniadm=sno_hpersonalnomina.depuniadm ".
				 "	  AND sno_hunidadadmin.prouniadm=sno_hpersonalnomina.prouniadm) ".
				 "	 JOIN sno_hnomina ".
				 "     ON (sno_hnomina.codemp=sno_hunidadadmin.codemp ".
				 "	  AND sno_hnomina.codnom=sno_hunidadadmin.codnom ".
				 "	  AND sno_hnomina.anocurnom=sno_hunidadadmin.anocur ".
				 "	  AND sno_hnomina.peractnom=sno_hunidadadmin.codperi) ".
				 "	WHERE sno_hresumen.codemp='".$this->ls_codemp."' ".
				 "	  AND sno_hresumen.monnetres > 0 ".
				"	  AND sno_hresumen.anocur BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' 	".				
				 "	  AND sno_hresumen.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ".
				 "    AND sno_hnomina.espnom='0' ".	
				 "	  AND sno_hpersonalnomina.codnom BETWEEN '".$as_codnomdes."' AND '".$as_codnomhas."' ".
				 $ls_criterio.
				 "	GROUP BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm ".
				 $ls_orden;  		    
		 $this->rs_detalle=$this->io_sql->select($ls_sql);
		 if($this->rs_detalle===false)
		 {
		 	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagos_unidad_totales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		 return $lb_valido;	 
	 }////end function uf_pagos_unidad_totales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_pagos_unidad_detallado($as_codnom,$as_codperides,$as_codperihas,$as_uni1,$as_uni2,$as_uni3,$as_uni4,$as_uni5,$as_conceptos,
									   $ad_aniodesde,$ad_aniohasta,$as_coddeddes,$as_coddedhas,$as_codtipperdes,$as_codtipperhas)	
	{   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagos_unidad_detallado
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnom // código de la nòmina 		
		//                 as_codperides // código del periodo desde
		// 				   as_codperihas  // código del periodo hasta	
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que cuenta la cantidad de personas en unidad adm por nomia 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/02/2010 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	    $lb_valido=true;
		$ls_criterio="";
		if (($as_coddeddes!="")&&($as_coddedhas!="")&&($as_codtipperdes!="")&&($as_codtipperhas!=""))
		{
			$ls_criterio=$ls_criterio."  AND sno_hpersonalnomina.codded BETWEEN '".$as_coddeddes."' AND '".$as_coddedhas."'".				
									  "  AND sno_hpersonalnomina.codtipper BETWEEN '".$as_codtipperdes."' AND '".$as_codtipperhas."'";			
		}
		$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.cedper) AS cedper, MAX(sno_personal.nomper) AS nomper, ".
				"		MAX(sno_personal.apeper) AS apeper, MAX(sno_personal.fecingper) AS fecingper, ".
				"       MAX(sno_hasignacioncargo.denasicar) AS denasicar, MAX(sno_hcargo.descar) AS descar, MAX(sno_hpersonalnomina.descasicar) AS descasicar ".
                "  FROM sno_hpersonalnomina ".
				" INNER JOIN sno_personal ".	
				"    ON sno_hpersonalnomina.codemp = sno_personal.codemp ".									
				"   AND sno_hpersonalnomina.codper = sno_personal.codper ".									
				" INNER JOIN sno_hsalida ".	
				"    ON sno_hpersonalnomina.codemp = sno_hsalida.codemp ".									
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".									
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".									
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".									
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				" INNER JOIN sno_hasignacioncargo ".	
				"    ON sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
				"   AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar ".
				"   AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi  ".				
				" INNER JOIN sno_hcargo ".	
				"    ON sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
				"   AND sno_hpersonalnomina.codcar = sno_hcargo.codcar ".
				"   AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".												
		   	    " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."'".  
                "   AND sno_hpersonalnomina.codnom='".$as_codnom."' ".
				"	AND sno_hpersonalnomina.anocur BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' 	".				
                "   AND sno_hpersonalnomina.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ". 
                "   AND sno_hpersonalnomina.minorguniadm= '".$as_uni1."'".              
                "   AND sno_hpersonalnomina.ofiuniadm='".$as_uni2."'".
				"   AND sno_hpersonalnomina.uniuniadm= '".$as_uni3."'".
				"   AND sno_hpersonalnomina.depuniadm= '".$as_uni4."'".
				"   AND sno_hpersonalnomina.prouniadm= '".$as_uni5."'".
				$ls_criterio.
				"   AND sno_hsalida.codconc IN (".$as_conceptos.") ".
				"   AND sno_hsalida.valsal <> 0 ".
				" GROUP BY sno_personal.codper ".  
				" ORDER BY sno_personal.codper ";   
		 $this->rs_detalle2=$this->io_sql->select($ls_sql);
		 if($this->rs_detalle2===false)
		 {
		  	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagos_unidad_detallado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		 return $lb_valido;	 
	 }// end function uf_pagos_unidad_detallado
	 //----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_pagos_unidad_conceptos($as_codnom,$as_codperides,$as_codperihas,$as_conceptos,$as_codper,$ad_aniodesde,$ad_aniohasta)	
	{   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagos_unidad_conceptos
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnom // código de la nòmina 		
		//                 as_codperides // código del periodo desde
		// 				   as_codperihas  // código del periodo hasta	
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que cuenta la cantidad de personas en unidad adm por nomia 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/02/2010 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	    $lb_valido=true;
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(sno_hconcepto.nomcon) AS nomcon, SUM(sno_hsalida.valsal) AS  valsal".
                "  FROM sno_hconcepto ".
				"  LEFT OUTER JOIN sno_hsalida ".	
                "    ON sno_hsalida.codper='".$as_codper."' ".
				"   AND sno_hconcepto.codemp = sno_hsalida.codemp ".									
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".									
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".									
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".									
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
		   	    " WHERE sno_hconcepto.codemp='".$this->ls_codemp."'".  
                "   AND sno_hconcepto.codnom='".$as_codnom."' ".
				"	AND sno_hconcepto.anocur BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' 	".				
                "   AND sno_hconcepto.codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ". 
				"   AND sno_hconcepto.codconc IN (".$as_conceptos.") ".
				" GROUP BY sno_hconcepto.codconc ".   
				" ORDER BY sno_hconcepto.codconc ";   
		 $this->rs_detalle3=$this->io_sql->select($ls_sql);
		 if($this->rs_detalle3===false)
		 {
		  	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagos_unidad_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		 return $lb_valido;	 
	 }// end function uf_pagos_unidad_conceptos
	 //----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagos_unidad_concepto_excel($as_codnom,$as_codperides,$as_codperihas,$as_conceptos,$ad_aniodesde,$ad_aniohasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagos_unidad_concepto_excel
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_conceptos // conceptos para el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/02/2010 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codconc,  MAX(nomcon) AS nomcon ".
				"  FROM sno_hconcepto ".
		   	    " WHERE codemp='".$this->ls_codemp."'".  
				"	AND anocur BETWEEN '".$ad_aniodesde."' AND '".$ad_aniohasta."' 	".				
                "   AND codperi BETWEEN '".$as_codperides."' AND '".$as_codperihas."' ". 
				"   AND codconc IN (".$as_conceptos.") ".
				" GROUP BY codconc "; 
				" ORDER BY codconc "; 
		$this->rs_detalle3=$this->io_sql->select($ls_sql);
		if($this->rs_detalle3===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagos_unidad_concepto_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagos_unidad_concepto_excel
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_clasificacion_obrero($as_orden)
    {	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_clasificacion_obrero
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: ls_orden // opción de ordenamiento	
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que cuenta la cantidad de personas en unidad adm por nomia 
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 04/06/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	     $lb_valido=true;
		 $ls_orden="";
		 if($as_orden==1)
		 {
		  	$ls_orden= "ORDER BY anovig ,nrogac, grado ";		 
		 }
		 else
		 {
		 	$ls_orden= "ORDER BY nrogac,anovig, grado ";
		 }		   			
	     $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		 
	     $ls_sql=" SELECT  sno_clasificacionobrero.grado AS grado,    ".
		         "         sno_clasificacionobrero.suemin AS suemin,   ".
				 "		   sno_clasificacionobrero.suemax AS suemax,   ".
				 "		   sno_clasificacionobrero.obscla AS obscla,   ".
				 "		   sno_clasificacionobrero.anovig AS anovig,   ".
				 "		   sno_clasificacionobrero.nrogac AS nrogac,   ".
				 "         sno_clasificacionobrero.tipcla AS tipcla    ".  
				 "	 FROM  sno_clasificacionobrero					   ".
				 "   WHERE sno_clasificacionobrero.grado<>'0000'       ".
				 "   UNION											   ".
				 "  SELECT sno_hclasificacionobrero.grado AS grado,    ".
				 "         sno_hclasificacionobrero.suemin AS suemin,  ".
				 "		   sno_hclasificacionobrero.suemax AS seumax,  ". 
				 "		   sno_hclasificacionobrero.obscla AS obscla,  ".
				 "		   sno_hclasificacionobrero.anovig AS anovig,  ".
				 "		   sno_hclasificacionobrero.nrogac AS nrogac,  ". 
				 "         sno_hclasificacionobrero.tipcla AS tipcla   ".   
				 "	 FROM  sno_hclasificacionobrero                    ".
				 "   WHERE sno_hclasificacionobrero.grado<>'0000'      ".$ls_orden;						
		    
		  $rs_data=$this->io_sql->select($ls_sql);
		  if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_clasificacion_obrero ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$this->DS->data=$this->io_sql->obtener_datos($rs_data);					
				}
				else
				{
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}		
		return $lb_valido;
	 
	 }////end function uf_clasificacion_obrero
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
   function uf_beneficiario_personal($as_codperdes, $as_codperhas, $as_cedbenedes, $as_cedbenehas, $as_orden,$rs_data)
    {	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_beneficiario_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_beneficiario_personal)  
		//	    Arguments: as_codperdes // código del personal desde
		//                 as_codperhas // código del personal hasta
		//                 as_codbenedes // código del beneficiario desde
		//                 as_codbenehas // código del beneficiario hasta
		//                 as_envio // modo de envio del recibo
		//                 ls_orden // opción de ordenamiento	
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que muestra los beneficiarios repetidos
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 13/06/2008								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	     $lb_valido=true;
		 $ls_orden="";
		 $ls_criterio="";	
		 $ls_criterio2="";	 
		 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
		 if($as_orden==1)
		 {
		 	switch($_SESSION["ls_gestor"])
	   		{
				case "MSQLT":				
					$ls_orden= "ORDER BY CONVERT( sno_beneficiario USING utf8)";
				break;
				case "POSTGRES":
				    $ls_orden= "ORDER BY  sno_beneficiario.cedben";
				break;
			}
		 }
		 if($as_orden==2)
		 {
		 	$ls_orden= "ORDER BY sno_beneficiario.apeben";
		 }
		 if($as_orden==3)
		 {
		 	$ls_orden= "ORDER BY sno_beneficiario.nomben";
		 }		 
		 if ((!empty($as_codperdes))&&(!empty($as_codperhas)))
		 {
		 	$ls_criterio= "    AND sno_beneficiario.codper  BETWEEN  '".$as_codperdes."' and '".$as_codperhas."'";
		 }
		 if ((!empty($as_cedbenedes))&&(!empty($as_cedbenehas)))
		 {
		 	switch($_SESSION["ls_gestor"])
	   		{
				case "MSQLT":				
					$ls_criterio2="  AND '$as_cedbenedes'>='$as_cedbenedes' ".
					              "  AND '$as_cedbenedes'<='$as_cedbenehas'";
				break;
				case "POSTGRES":
				    $ls_criterio2="  AND sno_beneficiario.cedben>='$as_cedbenedes' ".
					              "  AND sno_beneficiario.cedben<='$as_cedbenehas'";
				break;			
			}
		 }		
		 
		 $ls_sql=" SELECT sno_beneficiario.codper, sno_personal.cedper, sno_personal.nomper,sno_personal.apeper,  ".	        
		         "        sno_personal. dirper,    sno_beneficiario.tipcueben,                            ".
				 "        sno_beneficiario.codben, sno_beneficiario.cedben,sno_beneficiario.cedaut,        ".
                 "        sno_beneficiario.nomben, sno_beneficiario.apeben, sno_beneficiario.monpagben,    ".
				 "        sno_beneficiario.nexben, sno_beneficiario.nomcheben, sno_beneficiario.forpagben, ".
                 "        sno_beneficiario.codban, sno_beneficiario.ctaban, scb_banco.nomban              ".
				 "  FROM sno_beneficiario                                                                  ".
                 "  LEFT JOIN sno_personal on (sno_personal.codemp=sno_beneficiario.codemp                 ".
                 "   AND sno_personal.codper=sno_beneficiario.codper)                                      ".
                 "  LEFT JOIN scb_banco on (scb_banco.codemp=sno_beneficiario.codemp                       ".
                 "   AND scb_banco.codban=sno_beneficiario.codban)                                         ".              
                 "  WHERE sno_beneficiario.codemp='".$ls_codemp."' ".$ls_criterio.$ls_criterio2.$ls_orden;	
		 $rs_data=$this->io_sql->select($ls_sql); 
		 if($rs_data===false)
		 {
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_beneficiario_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }		
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	 }////end function uf_beneficiario_personal

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_modo_envio($as_codperdes, $as_codperhas, $as_orden, $as_envio,$rs_data)
    {	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_beneficiario_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_beneficiario_personal)  
		//	    Arguments: as_codperdes // código del personal desde
		//                 as_codperhas // código del personal hasta		
		//                 as_envio // modo de envio del recibo
		//                 ls_orden // opción de ordenamiento	
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que muestra los beneficiarios repetidos
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 13/06/2008								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	     $lb_valido=true;
		 $ls_orden="";
		 $ls_criterio="";
		 $ls_criterio2="";		 
		 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		
		 if($as_orden==1)
		 {
		 	$ls_orden= " ORDER BY sno_personal.cedper";
		 }
		 if($as_orden==2)
		 {
		 	$ls_orden= " ORDER BY sno_personal.apeper";
		 }
		 if($as_orden==3)
		 {
		 	$ls_orden= " ORDER BY sno_personal.nomper";
		 }		
		 
		 if (!empty($as_envio))
		 {
		 	$ls_criterio2="     AND sno_personal.enviorec ='".$as_envio."'  ";
		 }
		 	
		 if ((!empty($as_codperdes))&&(!empty($as_codperhas)))
		 {
		 	$ls_criterio= "    AND sno_personal.codper  BETWEEN  '".$as_codperdes."' and '".$as_codperhas."'";
		 }
		 
		 $ls_sql=" SELECT sno_personal.cedper,sno_personal.codper, sno_personal.nomper,sno_personal.apeper,  ".	        
		         "        sno_personal.dirper, sno_personal.enviorec                                     ".				 
				 "  FROM sno_personal                                                                      ".
                 "  WHERE sno_personal.codemp='".$ls_codemp."' ".$ls_criterio.$ls_criterio2.$ls_orden;		       
		 $rs_data=$this->io_sql->select($ls_sql); 
		 if($rs_data===false)
		 {
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_beneficiario_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }		
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	 }////end function uf_beneficiario_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_nexo_beneficiario_envio ($as_cedper, $as_nexo)
	{	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_nexo_beneficiario_envio
		//         Access: public (desde la clase sigesp_snorh_rpp_asignadocomponente)  
		//	    Arguments: as_cedper // cédula del personal
		//                 as_nexo  // parentezco
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: funciòn que busca el nexo de una persona
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 07/01/2009 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_nexo="";
	    $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql=" SELECT nexben  ".				 
				 "  FROM sno_beneficiario                                                                      ".
                 "  WHERE codemp='".$ls_codemp."' ".
				 "  AND cedben = '".$as_cedper."' ";	       
		 $rs_data2=$this->io_sql->select($ls_sql); 
		 if($rs_data2===false)
		 {
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_nexo_beneficiario_envio ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }	
		 else
		 {
		 	while(!$rs_data2->EOF)
			{
				 $as_nexben=$rs_data2->fields["nexben"];
				 switch ($as_nexben)
				 {
				 	case "-":
						$as_nexo="SIN PARENTESCO";
					break;
					case "C":
						$as_nexo="CONYUGUE";
					break;
					case "H":
						$as_nexo="HIJO";
					break;
					case "P":
						$as_nexo="PROGENITOR";
					break;
					case "E":
						$as_nexo="HERMANO";
					break;
				 }
				 $rs_data2->MoveNext();
			}
			if($rs_data2->RecordCount()==0)
			{
				$as_nexo="PENSIONADO";
			}
		 }	
		$arrResultado['as_nexo']=$as_nexo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}

	//------------------------------------------------------------------------------------------------------------------------------------
      function uf_asignacion_componente($as_codnom, $as_perides, $as_perihas,$ascodcomdes, $as_codcomhas)	
	  { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_asignacion_componente
		//         Access: public (desde la clase sigesp_snorh_rpp_asignadocomponente)  
		//	    Arguments: as_codnom // código de la nòmina 		
		//                 as_perides // código del periodo desde
		// 				   as_perihas  // código del periodo hasta	
		//                 as_codcomdes // código del componente desde
		//                 as_codcomhas // código del componente hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: funciòn que busca las asignaciones por componente
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 18/06/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	    $lb_valido=true;		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql=" SELECT sno_personal.codcom, sno_componente.descom, 						".
       			"		 sno_personal.codran, sno_rango.desran,sno_categoria_rango.descat,  ".
				"		 max(sno_hpersonalnomina.fecingper) as fecingper,					".
				"	     sum(valsal) as asignacion   										".   
				"    FROM sno_hpersonalnomina                                               ".
				"    JOIN sno_hsalida ON (sno_hsalida.codemp=sno_hpersonalnomina.codemp     ".
				"				 AND  sno_hsalida.codnom=sno_hpersonalnomina.codnom         ".
				"				 AND  sno_hsalida.codperi=sno_hpersonalnomina.codperi       ".
				"				 AND  sno_hsalida.codper=sno_hpersonalnomina.codper         ".
				"				 AND  sno_hsalida.anocur=sno_hpersonalnomina.anocur)        ".
				"    JOIN sno_personal ON (sno_personal.codemp=sno_hpersonalnomina.codemp   ".
				"				 AND  sno_personal.codper=sno_hpersonalnomina.codper)       ".
				"    JOIN sno_componente ON (sno_componente.codemp=sno_personal.codemp      ".
				"				   AND  sno_componente.codcom=sno_personal.codcom)          ".
				"    JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp                ".
				"			  AND  sno_rango.codcom=sno_personal.codcom                     ".
				"			  AND  sno_rango.codran=sno_personal.codran)                    ".
				"    JOIN sno_categoria_rango ON (sno_categoria_rango.codemp=sno_personal.codemp ".
				"						AND  sno_categoria_rango.codcat=sno_rango.codcat)   ".
				"   WHERE sno_hpersonalnomina.codemp='".$ls_codemp."'                       ".
				"     AND sno_hpersonalnomina.codnom= '".$as_codnom."'                      ".
				"     AND sno_hpersonalnomina.codperi BETWEEN '".$as_perides."' AND '".$as_perihas."' ".
				"     AND sno_personal.codcom BETWEEN '".$ascodcomdes."' AND '".$as_codcomhas."' ".
				"     AND sno_hsalida.tipsal='A'                                            ".
				"  GROUP BY sno_personal.codcom,sno_componente.descom, sno_personal.codran, sno_rango.desran, ".
				"           sno_categoria_rango.descat                                      ".
				"  ORDER BY  sno_personal.codcom, sno_personal.codran"; 
				    
		 $rs_data=$this->io_sql->select($ls_sql);
		 if($rs_data===false)
		 {
		  	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contar_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		  else
			{
				if($row=$this->io_sql->fetch_row($rs_data))
				{
					$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);					
				}
				else
				{
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}				
		 return $lb_valido;	 
	 }////end function uf_asignacion_componente
	//------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
      function uf_contar_rango($as_codnom, $as_perides, $as_perihas,$as_codcom,$as_codran, $rs_contar)	
	  { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contar_rango
		//         Access: public (desde la clase sigesp_snorh_rpp_asignadocomponente)  
		//	    Arguments: as_codnom // código de la nòmina 		
		//                 as_perides // código del periodo desde
		// 				   as_perihas  // código del periodo hasta
		//				   as_codcom // código del componente	
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que cuenta la cantidad de personas por rango
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 16/06/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  	    $lb_valido=true;		
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_sql=" SELECT sno_hpersonalnomina.codnom, sno_hpersonalnomina.codper,                              ".
				"  	     sno_personal.codcom, sno_personal.codran                                             ".
				"	FROM sno_hpersonalnomina                                                                  ".
				"	JOIN sno_personal ON (sno_personal.codemp=sno_hpersonalnomina.codemp                      ".
				"					 AND  sno_personal.codper=sno_hpersonalnomina.codper)                     ".									 				"	 WHERE sno_hpersonalnomina.codemp='".$ls_codemp."'                                        ".
				"	   AND sno_hpersonalnomina.codperi BETWEEN '".$as_perides."' AND '".$as_perihas."'        ".
				"	   AND sno_hpersonalnomina.codnom='".$as_codnom."'                                        ".
				"	   AND sno_personal.codcom='".$as_codcom."'                                               ".
				"      AND sno_personal.codran='".$as_codran."'                                                ".
				"	   AND sno_hpersonalnomina.codper in (SELECT sno_hsalida.codper                           ".
				"							FROM  sno_hsalida                                                 ".
				"						   WHERE sno_hsalida.codperi BETWEEN '".$as_perides."' AND '".$as_perihas."'        ".
				"													 AND sno_hsalida.valsal>0                 ".
				"																 AND sno_hsalida.tipsal='A')  ".
				"	 GROUP BY sno_hpersonalnomina.codnom,sno_hpersonalnomina.codper, sno_personal.codcom,sno_personal.codran ".
				"	 ORDER BY sno_personal.codcom, sno_personal.codran                                        ";  
				 
		 $rs_contar=$this->io_sql->select($ls_sql);
		 if($rs_contar===false)
		 {
		  	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contar_rango ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }				 
		$arrResultado['rs_contar']=$rs_contar;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}////end function uf_contar_rango
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_beneficiarios($as_codbendes, $as_codbenhas, $as_codperdes, $as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_beneficiarios
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_beneficiario)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la informaciòn de los beneficiarios
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 30/06/2008								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if (($as_codperdes!="")&&($as_codperhas!=""))		
		{
			$ls_criterio="   AND codper BETWEEN '".$as_codperdes."' AND '".$as_codperhas."'";
		}
		if (($as_codbendes!="")&&($as_codbenhas!=""))		
		{
			$ls_criterio=$ls_criterio. "   AND codben BETWEEN '".$as_codbendes."' AND '".$as_codbenhas."'";  
		}
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_sql=" SELECT sno_beneficiario.codper, sno_beneficiario.codben,  sno_beneficiario.cedben,        ".
                "        sno_beneficiario.nomben, sno_beneficiario.apeben,  sno_beneficiario.porpagben,     ".
                "        sno_beneficiario.codban, sno_beneficiario.ctaban,  sno_beneficiario.tipcueben,     ".
				"        sno_beneficiario.nexben, sno_beneficiario.nomcheben, sno_beneficiario.cedaut,       ".
				"        (SELECT sno_personal.fecnacper FROM sno_personal ".
				"          WHERE sno_personal.codemp='".$ls_codemp."'".
				"            AND sno_personal.cedper=sno_beneficiario.cedben) as fecnacben,        ".
				"        (SELECT scb_banco.nomban FROM scb_banco WHERE scb_banco.codemp='".$ls_codemp."'     ".
				"            AND scb_banco.codban=sno_beneficiario.codban) AS banco                          ".
                " FROM sno_beneficiario                                                                     ".
                " WHERE sno_beneficiario.codemp='".$ls_codemp."'".$ls_criterio.
				" ORDER BY sno_beneficiario.codper, sno_beneficiario.codben";              
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_beneficiarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else		
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_pension->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_buscar_beneficiarios
	//----------------------------------------------------------------------------------------------------------------------------------- 

	//----------------------------------------------------------------------------------------------------------------------------------- 
    function uf_recibo_nomina_oficiales($as_codper,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la informaciòn del personal oficial
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 14/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";		
		$as_codemp=$_SESSION["la_empresa"]["codemp"];			
			
				$ls_sql=" SELECT sno_personalpension.codemp, sno_personalpension.codnom, sno_personalpension.codper, ".
						"	     sno_personalpension.suebasper, sno_personalpension.pritraper, sno_personalpension.pridesper, ". 
						"	     sno_personalpension.prianoserper, sno_personalpension.prinoascper, ".
						"	     sno_personalpension.priespper, sno_personalpension.priproper, sno_personalpension.subtotper, ".
						"	     sno_personalpension.porpenper, sno_personalpension.monpenper, ".
						"	   (select sno_personal.nomper from sno_personal where codper=sno_personalpension.codper) as nomper,".
						"	   (select sno_personal.apeper from sno_personal where ".
						" sno_personal.codper=sno_personalpension.codper)  as apeper, ".
						"	   (select sno_personal.cedper from sno_personal  ".
						"      where sno_personal.codper=sno_personalpension.codper) as cedper, ".
						"	   (select sno_personal.fecingper from sno_personal ".
						"	   where sno_personal.codper=sno_personalpension.codper) as fecingper, ".
						"	   (select sno_personalnomina.fecingper from sno_personalnomina ".
						"       where sno_personalnomina.codper=sno_personalpension.codper ".
						"       and sno_personalnomina.codnom='".$as_codnom."') as fecingnom, ".
						"	    sno_componente.descom, sno_rango.desran ".
						"  FROM sno_personalpension ".
						"  JOIN sno_personal ON (sno_personal.codemp=sno_personalpension.codemp ".
						"				   AND  sno_personal.codper=sno_personalpension.codper) ".
						"  LEFT JOIN sno_componente ON (sno_componente.codemp= sno_personal.codemp ".
						"						   AND sno_componente.codcom= sno_personal.codcom) ".
						"  LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp ".
						"					 AND  sno_rango.codcom=sno_personal.codcom  ".
						"					 AND  sno_rango.codran=sno_personal.codran) ".
						" WHERE sno_personalpension.codemp='".$as_codemp."'".
						" AND	sno_personalpension.codper='".$as_codper."'".
						" AND	sno_personalpension.codnom='".$as_codnom."'";  			
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_recibo_nomina_oficiales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_pension->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_recibo_nomina_oficiales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_instructivo_07_cargos_programado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_instructivo_07_entes_sin_fin_lucro
		//         Access: public (desde la clase sigesp_snorh_rpp_isntructivo)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 16/07/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql=" SELECT codrep,codded,codtipper, ".
		        "        monene,monfeb,monmar,monabr,monmay,monjun, ".
				"        monjul,monago,monsep,monoct,monnov,mondic, ".
				"		 carene,carfeb,carmar,carabr,carmay,carjun, ".
				"        carjul,carago,carsep,caroct,carnov,cardic, ".
				"		 carenef,carfebf,carmarf,carabrf,carmayf,carjunf, ".
				"        carjulf,caragof,carsepf,caroctf,carnovf,cardicf, ".
				"		 carenem,carfebm,carmarm,carabrm,carmaym,carjunm, ".
				"        carjulm,caragom,carsepm,caroctm,carnovm,cardicm, ".
				"		 (SELECT sno_dedicacion.desded FROM  sno_dedicacion ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
				"			AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
				"		(SELECT sno_tipopersonal.destipper FROM  sno_tipopersonal ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
				"			AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
				"			AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
				"  FROM sno_programacionreporte ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"   AND codrep = '0711'".
				"   AND codtipper NOT IN ('0108','0109','0110') ".
				" ORDER BY codded, codtipper ";  
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_07_entes_sin_fin_lucro ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_instructivo_07_cargos_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_instructivo_07_cargos_real($as_rango,$as_codded,$as_codtipper,$ai_cargoreal,$ai_cargorealf,$ai_cargorealm,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_instructivo_07_cargos_real
		//         Access: public (desde la clase sigesp_snorh_rpp_isntructivo_07_cargos)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_anio=substr($_SESSION["la_empresa"]["periodo"],0,4);
		switch($as_rango)
		{
			case "01":
				$randes=$ls_anio."-01-01";
				$ranhas=$ls_anio."-03-31";
			break;
			case "02":
				$randes=$ls_anio."-04-01";
				$ranhas=$ls_anio."-06-30";
			break;
			case "03":
				$randes=$ls_anio."-07-01";
				$ranhas=$ls_anio."-09-30";
			break;
			case "04":
				$randes=$ls_anio."-10-01";
				$ranhas=$ls_anio."-12-31";
			break;
		}
		$ls_sql="SELECT sno_personal.sexper, COUNT(sno_hpersonalnomina.codper) as totalcargos ".
				"  FROM sno_hpersonalnomina ".
				" INNER JOIN (sno_hperiodo ".
				" 		INNER JOIN sno_hnomina ".
				"         ON sno_hperiodo.fecdesper >= '".$randes."'".
				"   	 AND sno_hperiodo.fechasper  <= '".$ranhas."'".
				"        AND sno_hnomina.tipnom <> 7 ".
				"        AND sno_hnomina.espnom = '0' ".
				"        AND sno_hnomina.ctnom = '0' ".
				"        AND sno_hperiodo.codemp = sno_hnomina.codemp ".
				"        AND sno_hperiodo.codnom = sno_hnomina.codnom ".
				"	     AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
				"        AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
				"   AND sno_hpersonalnomina.codded='".$as_codded."'".
				"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				" INNER JOIN sno_personal ".
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
				"   AND sno_hpersonalnomina.codded='".$as_codded."'".
				"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
				"   AND sno_hpersonalnomina.staper='1'".
				"   AND sno_personal.codemp=sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper=sno_hpersonalnomina.codper ".
				" GROUP BY sno_hpersonalnomina.codperi, sno_personal.sexper ".
				" ORDER BY sno_hpersonalnomina.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_07_cargos_real_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ai_cargoreal=0;
			$ai_cargorealf=0;
			$ai_cargorealm=0;
			while(!$rs_data->EOF)
			{
				switch($rs_data->fields["sexper"])
				{
					case "F";
						$ai_cargorealf=$rs_data->fields["totalcargos"];
					break;
					case "M";
						$ai_cargorealm=$rs_data->fields["totalcargos"];
					break;
				}
				$ai_cargoreal=$ai_cargorealf+$ai_cargorealm;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		if ($lb_valido)	
		{	
			$ls_sql="SELECT SUM(sno_hsalida.valsal) as monto ".
					"  FROM sno_hpersonalnomina ".
					" INNER JOIN (sno_hperiodo ".
					" 		INNER JOIN sno_hnomina ".
					"         ON sno_hperiodo.fecdesper >= '".$randes."'".
					"   	 AND sno_hperiodo.fechasper  <= '".$ranhas."'".
					"        AND sno_hnomina.tipnom <> 7 ".
					"        AND sno_hnomina.espnom = '0' ".
					"        AND sno_hnomina.ctnom = '0' ".
					"        AND sno_hperiodo.codemp = sno_hnomina.codemp ".
					"        AND sno_hperiodo.codnom = sno_hnomina.codnom ".
					"	     AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
					"        AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
					"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
					"   AND sno_hpersonalnomina.codded='".$as_codded."'".
					"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
					"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
					"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
					" INNER JOIN sno_hsalida ".
					"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
					"   AND sno_hpersonalnomina.codded='".$as_codded."'".
					"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
					"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
					"   AND sno_hsalida.valsal <> 0 ".
					"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_07_cargos_real_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$ai_montoreal=0;
				if(!$rs_data->EOF)
				{
					$ai_montoreal=$rs_data->fields["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}		
		}
		$arrResultado['ai_cargoreal']=$ai_cargoreal;
		$arrResultado['ai_cargorealf']=$ai_cargorealf;
		$arrResultado['ai_cargorealm']=$ai_cargorealm;
		$arrResultado['ai_montoreal']=$ai_montoreal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end instructivo_07_cargos_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_instructivo_07_monto_acumulado($as_rango,$as_codded,$as_codtipper,$ai_monto_acumulado)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_instructivo_07_monto_acumulado
		//         Access: public (desde la clase sigesp_snorh_rpp_isntructivo_07_cargos)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_anio=substr($_SESSION["la_empresa"]["periodo"],0,4);
		switch($as_rango)
		{
			case "01":
				$randes=$ls_anio."-01-01";
				$ranhas=$ls_anio."-03-31";
			break;
			case "02":
				$randes=$ls_anio."-04-01";
				$ranhas=$ls_anio."-06-30";
			break;
			case "03":
				$randes=$ls_anio."-07-01";
				$ranhas=$ls_anio."-09-30";
			break;
			case "04":
				$randes=$ls_anio."-10-01";
				$ranhas=$ls_anio."-12-31";
			break;
		}
		$ls_sql="SELECT SUM(sno_hsalida.valsal) as monto ".
				"  FROM sno_hpersonalnomina ".
				" INNER JOIN (sno_hperiodo ".
				" 		INNER JOIN sno_hnomina ".
				"         ON sno_hperiodo.fechasper  <= '".$ranhas."'".
				"        AND sno_hnomina.tipnom <> 7 ".
				"        AND sno_hnomina.espnom = '0' ".
				"        AND sno_hnomina.ctnom = '0' ".
				"        AND sno_hperiodo.codemp = sno_hnomina.codemp ".
				"        AND sno_hperiodo.codnom = sno_hnomina.codnom ".
				"	     AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
				"        AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
				"   AND sno_hpersonalnomina.codded='".$as_codded."'".
				"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				" INNER JOIN sno_hsalida ".
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
				"   AND sno_hpersonalnomina.codded='".$as_codded."'".
				"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_07_monto_acumulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ai_monto_acumulado=$rs_data->fields["monto"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_monto_acumulado']=$ai_monto_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end uf_instructivo_07_monto_acumulado
	//------------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_personalunidadadm($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
                                  $as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
                                  $as_masculino,$as_femenino)
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_personalunidadadm
        //         Access: public (desde la clase sigesp_snorh_rpp_familiar_ipsfa)  
        //        Arguments:   as_codnomdes // Código de la nómina donde se empieza a filtrar
        //                     as_codnomhas // Código de la nómina donde se termina de filtrar          
        //                     as_codperdes // Código de personal donde se empieza a filtrar
        //                     as_codperhas // Código de personal donde se termina de filtrar          
        //                     as_activo // Estatus Activo          
        //                     as_egresado // Estatus Egresado        
        //                     as_activono // Estatus Activo dentro de la nómina          
        //                     as_vacacionesno // Estatus Vaciones dentro de la nómina
        //                     as_suspendidono // Estatus Suspendido dentro de la nómina
        //                     as_egresadono // Estatus Egresado dentro de la nómina
        //                     as_masculino // Solo el personal masculino
        //                     as_femenino // Solo el personal femenino
                      //                     as_orden // Orden en que se quiere sacar el reporte
        //          Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
        //    Description: función que busca la información del personal
        //       Creado Por: Ing. Jennifer Rivero
        // Fecha Creación: 21/07/2008                                Fecha Última Modificación :  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_criterio="";
        $ls_orden="";
        $lb_ok=false;
        if(!empty($as_codnomdes))
        {
            $ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
            if(!empty($as_codnomhas))
            {
                $ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
            }
            if(!empty($as_activono))
            {
                $ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
                $lb_ok=true;
            }
            if(!empty($as_vacacionesno))
            {
                if($lb_ok)
                {
                    $ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
                }
                else
                {
                    $ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
                    $lb_ok=true;
                }
            }
            if(!empty($as_egresadono))
            {
                if($lb_ok)
                {
                    $ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
                }
                else
                {
                    $ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
                    $lb_ok=true;
                }
            }
            if(!empty($as_suspendidono))
            {
                if($lb_ok)
                {
                    $ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
                }
                else
                {
                    $ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
                    $lb_ok=true;
                }
            }
            if($lb_ok)
            {
                $ls_criterio= $ls_criterio." )";
                $lb_ok=false;
            }
        }
        if(!empty($as_codperdes))
        {
            $ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
        }
        if(!empty($as_codperhas))
        {
            $ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
        }
        
        if(!empty($as_activo))
        {
            $ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
            $lb_ok=true;
        }
        if(!empty($as_egresado))
        {
            if($lb_ok)
            {
                $ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
            }
            else
            {
                $ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
                $lb_ok=true;
            }
        }
        if($lb_ok)
        {
            $ls_criterio= $ls_criterio.")";
        }
        $lb_anterior=false;
        if(!empty($as_masculino))
        {
            $ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
            $lb_anterior=true;
        }
        if(!empty($as_femenino))
        {
            if($lb_anterior)
            {
                $ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
            }
            else
            {
                $ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
                $lb_anterior=true;
            }
        }
        if($lb_anterior)
        {
            $ls_criterio= $ls_criterio.")";
        }
                
        $ls_sql=" SELECT DISTINCT sno_personal.codper, sno_unidadadmin.desuniadm, ".
                " sno_unidadadmin.minorguniadm, sno_unidadadmin.ofiuniadm, ".
                " sno_unidadadmin.uniuniadm, sno_unidadadmin.depuniadm, sno_unidadadmin.prouniadm ".
                "  FROM sno_personal, sno_personalnomina, sno_unidadadmin ".
                " WHERE sno_personal.codemp = '".$this->ls_codemp."'".
                "   ".$ls_criterio.
                "   AND sno_personal.codemp = sno_personalnomina.codemp ".
                "    AND sno_personal.codper = sno_personalnomina.codper ".
                "   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
                "    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
                "    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm  ".
                "    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm  ".
                "    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm  ".
                "    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
                "   ORDER BY  sno_unidadadmin.minorguniadm, sno_unidadadmin.ofiuniadm, ".
                "             sno_unidadadmin.uniuniadm, sno_unidadadmin.depuniadm, sno_unidadadmin.prouniadm ";                
                $ls_orden; 
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report MÉTODO->uf_personalunidadadm ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
        else
        {
            if($row=$this->io_sql->fetch_row($rs_data))
            {
                $this->DS_nominas->data=$this->io_sql->obtener_datos($rs_data);        
            }
            else
            {
                $lb_valido=false;
            }
            $this->io_sql->free_result($rs_data);
        }        
        return $lb_valido;
    }// end function uf_personalunidadadm
	//------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_personaljub($as_codperdes,$as_codperhas,$as_codnondes,$as_codnomhas,$femenino,$masculino,$as_orden,$as_tipo)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_personaljub
		//         Access: public (desde la clase sigesp_sno_rpp_litadopersonaljubilado)  
		//      Arguments: 
		//        Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: 
		//     Creado Por: Ing. Jennifer Rivero
		// Modificado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/08/2008                                Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio=" sno_personal.codemp = '".$this->ls_codemp."' ".
			     " AND sno_personal.estper = '1' ".
			     " AND sno_personal.codtippersss IN (".$as_tipo.") ";
		$ls_criterionomina="  ON sno_personalnomina.codemp = '".$this->ls_codemp."' ".
				   " AND (sno_personalnomina.staper = '1' OR sno_personalnomina.staper = '2')".
				   " AND sno_nomina.espnom = '0' ";
		$ls_orden=""; 
		if (($as_codperdes!="")&&($as_codperhas!=""))
		{   
			$ls_criterio=$ls_criterio." AND sno_personal.codper BETWEEN '".$as_codperdes."' AND '".$as_codperhas."'";
		}
		if (($as_codnondes!="")&&($as_codnomhas!=""))
		{
			$ls_criterionomina= $ls_criterionomina." AND sno_personalnomina.codnom BETWEEN '".$as_codnondes."' AND '".$as_codnomhas."'";
		}
		if (($femenino!="")&&($masculino==""))
		{
			$ls_criterio=$ls_criterio. " AND sno_personal.sexper='F'";
		}
		if (($femenino=="")&&($masculino!=""))
		{
			$ls_criterio=$ls_criterio." AND sno_personal.sexper='M'";
		}
		if (($femenino!="")&&($masculino!=""))
		{
			$ls_criterio=$ls_criterio." AND (sno_personal.sexper='M' OR sno_personal.sexper='F')";
		}
		switch ($as_orden)
		{
			case 1:
				$ls_orden= " ORDER BY sno_personal.codper";
			break;
			case 2:
				$ls_orden= " ORDER BY sno_personal.nomper";
			break;
			case 3:
				$ls_orden= " ORDER BY sno_personal.apeper";
			break;
		}
		$ls_sql="SELECT DISTINCT (sno_personal.codper), sno_personal.nomper, sno_personal.apeper, sno_personal.sexper, sno_personal.anoperobr, ".
				"		sno_personal.fecingper, sno_personal.anoservpreper, sno_personal.fecnacper, sno_personal.fecjubper, ".
				"		sno_personal.fecingadmpubper, sno_nomina.desnom, sno_personal.anoservprefijo,sno_personal.codtippersss, ".
				"		sno_componente.descom, sno_rango.desran ".
				"  FROM sno_personal ".
				" INNER JOIN (sno_personalnomina ".
				"       INNER JOIN sno_nomina ".
				"	       ".$ls_criterionomina.
				"         AND sno_personalnomina.codemp=sno_nomina.codemp ".
				"         AND sno_personalnomina.codnom=sno_nomina.codnom) ".
				"    ON ".$ls_criterio.
				"   AND sno_personal.codemp=sno_personalnomina.codemp ".
				"   AND sno_personal.codper=sno_personalnomina.codper ".
				"  LEFT JOIN sno_componente ".
				"    ON ".$ls_criterio.
				"   AND sno_personal.codemp=sno_componente.codemp ".
				"   AND sno_personal.codcom= sno_componente.codcom".
				"  LEFT JOIN sno_rango ".
				"    ON ".$ls_criterio.
				"   AND sno_personal.codemp=sno_rango.codemp ".
				"   AND sno_personal.codcom= sno_rango.codcom ".
				"   AND sno_personal.codran= sno_rango.codran ".
				" WHERE ".$ls_criterio.
				$ls_orden; 
	 $this->rs_data=$this->io_sql->select($ls_sql);
     if($this->rs_data===false)
     {
     	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listado_personaljub ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        $lb_valido=false;
     }
	return $lb_valido;
	}//FIN uf_listado_personaljub
	//------------------------------------------------------------------------------------------------------------------------------------

	//-------------------------------------------------------------------------------------------------------------------------------------
    function uf_listado_personalegresado($as_codperdes,$as_codperhas,$as_codnondes,$as_codnomhas,$femenino,$masculino,
	                                     $as_fecdes,$as_fechas,$as_orden)
	{
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_listado_personalegresado
        //         Access: public (desde la clase sigesp_sno_rpp_litadopersonalegresado)  
        //      Arguments: 
        //        Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
        //    Description: 
        //     Creado Por: Ing. Jennifer Rivero
        // Fecha Creación: 26/08/2008                                Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_criterio="";
	 $ls_orden=""; 
	 if (($as_codperdes!="")&&($as_codperhas!=""))
	 {   
	 	$ls_criterio=" AND sno_personal.codper between '".$as_codperdes."' and '".$as_codperhas."'";
	 }
	 if (($as_codnondes!="")&&($as_codnomhas!=""))
	 {
	 	$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom between '".$as_codnondes."' and '".$as_codnomhas."'";
	 }
	 
	 if (($femenino!="")&&($masculino==""))
	 {
	 	$ls_criterio=$ls_criterio. " AND sno_personal.sexper='F'";
	 }
	 
	 if (($femenino=="")&&($masculino!=""))
	 {
	 	$ls_criterio=$ls_criterio." AND sno_personal.sexper='M'";
	 }
	 
	 if (($femenino!="")&&($masculino!=""))
	 {
	 	$ls_criterio=$ls_criterio." AND (sno_personal.sexper='M' OR sno_personal.sexper='F')";
	 }
	 
	 if (($as_fecdes!="")&&($as_fechas!=""))
	 {   
	 	$as_fecdes=$this->io_funciones->uf_convertirdatetobd($as_fecdes);
		$as_fechas=$this->io_funciones->uf_convertirdatetobd($as_fechas);
		$ls_criterio=$ls_criterio." AND sno_personalnomina.fecegrper between '".$as_fecdes."' and '".$as_fechas."'";
	 }
	 
	 if($as_orden==1)
	 {
		 $ls_orden= " ORDER BY sno_personal.codper";
	 }
	 if($as_orden==2)
	 {
		 $ls_orden= " ORDER BY sno_personal.nomper";
	 }		
	 if($as_orden==3)
	 {
		 $ls_orden= " ORDER BY sno_personal.apeper";
	 }		 
	 
		 $ls_sql=" SELECT DISTINCT (sno_personal.codper), sno_nomina.desnom, sno_personal.nomper, sno_personal.apeper, ".
		         "        sno_personal.cedper,sno_personal.nacper, sno_personalnomina.fecegrper as  fecegrnom,                             ".
				 "  	  sno_personal.fecegrper,sno_personal.cauegrper, sno_personal.obsegrper, ".
				 "		  sno_personalnomina.sueper,                ".
				 "		 (SELECT MAX(sno_hpersonalnomina.codunirac) ". 
				 "		    FROM sno_hpersonalnomina, sno_periodo ".
				 "		   WHERE sno_hpersonalnomina.staper= '1' ".
				 "		     AND substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,8) =  substr(CAST(sno_personalnomina.fecegrper AS CHAR(10)),0,8) ".
				 "		     AND sno_hpersonalnomina.codemp=sno_personalnomina.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_personalnomina.codnom ".
				 "		     AND sno_hpersonalnomina.codper=sno_personalnomina.codper ".
				 "		     AND sno_hpersonalnomina.codemp=sno_periodo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_periodo.codnom ".
				 "		     AND sno_hpersonalnomina.codperi=sno_periodo.codperi ".
				 "		     AND sno_hpersonalnomina.anocur=substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,5) ) As codunirac, ".
				 "		 (SELECT MAX(sno_hasignacioncargo.denasicar)           ".
				 "		    FROM sno_hpersonalnomina, sno_periodo, sno_hasignacioncargo ".
				 "		   WHERE sno_hpersonalnomina.staper= '1' ".
				 "		     AND substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,8) =  substr(CAST(sno_personalnomina.fecegrper AS CHAR(10)),0,8) ".
				 "		     AND sno_hpersonalnomina.codemp=sno_personalnomina.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_personalnomina.codnom ".
				 "		     AND sno_hpersonalnomina.codper=sno_personalnomina.codper ".
				 "		     AND sno_hpersonalnomina.codemp=sno_hasignacioncargo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_hasignacioncargo.codnom ".
				 "		     AND sno_hpersonalnomina.anocur=sno_hasignacioncargo.anocur ".
				 "		     AND sno_hpersonalnomina.codperi=sno_hasignacioncargo.codperi ".
				 "		     AND sno_hpersonalnomina.codasicar=sno_hasignacioncargo.codasicar ".
				 "		     AND sno_hpersonalnomina.codemp=sno_periodo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_periodo.codnom ".
				 "		     AND sno_hpersonalnomina.codperi=sno_periodo.codperi ".
				 "		     AND sno_hpersonalnomina.anocur=substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,5) ) As denasicar, ".
				 "		 (SELECT MAX(sno_hgrado.moncomgra)           ".
				 "		    FROM sno_hpersonalnomina, sno_periodo, sno_hgrado ".
				 "		   WHERE sno_hpersonalnomina.staper= '1' ".
				 "		     AND substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,8) =  substr(CAST(sno_personalnomina.fecegrper AS CHAR(10)),0,8) ".
				 "		     AND sno_hpersonalnomina.codemp=sno_personalnomina.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_personalnomina.codnom ".
				 "		     AND sno_hpersonalnomina.codper=sno_personalnomina.codper ".
				 "		     AND sno_hpersonalnomina.codemp=sno_hgrado.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_hgrado.codnom ".
				 "		     AND sno_hpersonalnomina.anocur=sno_hgrado.anocur ".
				 "		     AND sno_hpersonalnomina.codperi=sno_hgrado.codperi ".
				 "		     AND sno_hpersonalnomina.codtab=sno_hgrado.codtab ".
				 "		     AND sno_hpersonalnomina.codpas=sno_hgrado.codpas ".
				 "		     AND sno_hpersonalnomina.codgra=sno_hgrado.codgra ".
				 "		     AND sno_hpersonalnomina.codemp=sno_periodo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_periodo.codnom ".
				 "		     AND sno_hpersonalnomina.codperi=sno_periodo.codperi ".
				 "		     AND sno_hpersonalnomina.anocur=substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,5) ) As moncomgra, ".
				 "		 (SELECT MAX(sno_hcargo.descar)           ".
				 "		    FROM sno_hpersonalnomina, sno_periodo, sno_hcargo ".
				 "		   WHERE sno_hpersonalnomina.staper= '1' ".
				 "		     AND substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,8) =  substr(CAST(sno_personalnomina.fecegrper AS CHAR(10)),0,8) ".
				 "		     AND sno_hpersonalnomina.codemp=sno_personalnomina.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_personalnomina.codnom ".
				 "		     AND sno_hpersonalnomina.codper=sno_personalnomina.codper ".
				 "		     AND sno_hpersonalnomina.codemp=sno_hcargo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_hcargo.codnom ".
				 "		     AND sno_hpersonalnomina.anocur=sno_hcargo.anocur ".
				 "		     AND sno_hpersonalnomina.codperi=sno_hcargo.codperi ".
				 "		     AND sno_hpersonalnomina.codcar=sno_hcargo.codcar ".
				 "		     AND sno_hpersonalnomina.codemp=sno_periodo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_periodo.codnom ".
				 "		     AND sno_hpersonalnomina.codperi=sno_periodo.codperi ".
				 "		     AND sno_hpersonalnomina.anocur=substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,5) ) As descar, ".
				 "		 (SELECT MAX(sno_hunidadadmin.desuniadm)          ".
				 "		    FROM sno_hpersonalnomina, sno_periodo, sno_hunidadadmin ".
				 "		   WHERE sno_hpersonalnomina.staper= '1' ".
				 "		     AND substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,8) =  substr(CAST(sno_personalnomina.fecegrper AS CHAR(10)),0,8) ".
				 "		     AND sno_hpersonalnomina.codemp=sno_personalnomina.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_personalnomina.codnom ".
				 "		     AND sno_hpersonalnomina.codper=sno_personalnomina.codper ".
				 "		     AND sno_hpersonalnomina.codemp=sno_hunidadadmin.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_hunidadadmin.codnom ".
				 "		     AND sno_hpersonalnomina.anocur=sno_hunidadadmin.anocur ".
				 "		     AND sno_hpersonalnomina.codperi=sno_hunidadadmin.codperi ".
				 "		     AND sno_hpersonalnomina.minorguniadm=sno_hunidadadmin.minorguniadm ".
				 "		     AND sno_hpersonalnomina.ofiuniadm=sno_hunidadadmin.ofiuniadm ".
				 "		     AND sno_hpersonalnomina.uniuniadm=sno_hunidadadmin.uniuniadm ".
				 "		     AND sno_hpersonalnomina.depuniadm=sno_hunidadadmin.depuniadm ".
				 "		     AND sno_hpersonalnomina.prouniadm=sno_hunidadadmin.prouniadm ".
				 "		     AND sno_hpersonalnomina.codemp=sno_periodo.codemp ".
				 "		     AND sno_hpersonalnomina.codnom=sno_periodo.codnom ".
				 "		     AND sno_hpersonalnomina.codperi=sno_periodo.codperi ".
				 "		     AND sno_hpersonalnomina.anocur=substr(CAST(sno_periodo.fechasper AS CHAR(10)),0,5) ) As desuni, ".
                 "         (SELECT srh_departamento.dendep FROM srh_departamento                                         ".
				 "                                      WHERE srh_departamento.codemp=sno_personalnomina.codemp          ".
                 "                                       AND srh_departamento.coddep=sno_personalnomina.coddep) As desdep".
				 "  FROM sno_personal                                                                                    ".
				 "  LEFT JOIN sno_personalnomina ON (sno_personal.codemp=sno_personalnomina.codemp                       ".
				 "								     AND  sno_personal.codper=sno_personalnomina.codper)                 ".
				 "	INNER JOIN sno_nomina ON (sno_personalnomina.codemp=sno_nomina.codemp                                 ".
				 "	    			         AND  sno_personalnomina.codnom=sno_nomina.codnom)                           ".
				 "  WHERE sno_personal.codemp='".$this->ls_codemp."'                                                     ".				
				 "	  AND sno_personalnomina.staper='3'".$ls_criterio.$ls_orden;
	 $rs_data=$this->io_sql->select($ls_sql);
     if($rs_data===false)
     {
     	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listado_personalegresado ERROR->".
                                    $this->io_funciones->uf_convertirmsg($this->io_sql->message));
       // $lb_valido=false;
     }
     else
     {
     	if($row=$this->io_sql->fetch_row($rs_data))
        {
         	$this->DS->data=$this->io_sql->obtener_datos($rs_data);        
        }
        else
        {
        	$lb_valido=false;
        }
        $this->io_sql->free_result($rs_data);
      }  
	return $lb_valido;
	}//FIN uf_listado_personalegresado
	//------------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_pagonomina_concepto_excel($as_sigcon, $as_codperi, $as_codnomdes, $as_codnomhas, $as_codconcdes,$as_codconchas)
	{		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_concepto_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 08/09/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";	
		if(!empty($as_codconcdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.codconc>='".$as_codconcdes."' ";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio = $ls_criterio." AND sno_hconcepto.codconc<='".$as_codconchas."' ";
		}	
		$ls_sql="   SELECT codconc, titcon as nomcon     ".
				"	  FROM sno_hconcepto                 ".
				"	  WHERE codemp='".$this->ls_codemp."'". 
				"	   AND codnom between '".$as_codnomdes."'  AND '".$as_codnomhas."'".
				"	   AND codperi='".$as_codperi."'".$ls_criterio.   
					   $as_sigcon.	
			    "      AND sno_hconcepto.codconc IN (SELECT codconc ".
				"                                      FROM sno_hsalida ".
				"                                     WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"                                		AND sno_hsalida.codnom between '".$as_codnomdes."' AND '".$as_codnomhas."' ".
				"                              		    AND sno_hsalida.codperi='".$as_codperi."' ".
				"                                		AND sno_hsalida.valsal<>0)".			
				"  GROUP BY codconc, titcon ".
				"  ORDER BY codconc "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagonomina_concepto_excel ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//------------------------------------------------------------------------------------------------------------------------------------
	
	//------------------------------------------------------------------------------------------------------------------------------------
    function uf_pagonomina_conceptopersonal_excel($as_codper,$as_codperi,$as_codcomdes, $as_codcomhas,$signo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 08/09/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->DS_detalle->reset_ds();
		$lb_valido=true;
		$ls_criterio="";		
		  $ls_sql="  SELECT sno_hconcepto.codconc, MAX(sno_hconcepto.titcon) as nomcon, 
					        sno_hsalida.valsal, MAX(sno_hsalida.tipsal) AS tipsal 
					   FROM sno_hconcepto, sno_hsalida 
					  WHERE sno_hsalida.codemp='".$this->ls_codemp."' 
						AND sno_hsalida.codnom between '".$as_codcomdes."' AND '".$as_codcomhas."' 
						AND sno_hsalida.codperi='".$as_codperi."'
						AND sno_hsalida.codper='".$as_codper."' 
						AND sno_hsalida.valsal<>0 ".
						$signo.
					"	AND sno_hsalida.codemp = sno_hconcepto.codemp 
						AND sno_hsalida.codnom = sno_hconcepto.codnom 
						AND sno_hsalida.codconc = sno_hconcepto.codconc 
				      GROUP BY sno_hconcepto.codconc,sno_hsalida.valsal 					
				      ORDER BY codconc, tipsal"; //Le quite al valsal un SUM ojo, no quitar este comentario hasta validar
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagonomina_conceptopersonal_excel ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel	
	//-------------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
    function uf_buscar_fechaingtrabant($as_codper,$fecingtrabant)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_fechaingtrabant
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la minima fecha de ingreso de los trabajo anteriores en trabajos publicos como personal fijo
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 10/09/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->DS_detalle->reset_ds();
		$lb_valido=true;
		$ls_criterio="";		
		$ls_sql=" SELECT  MIN(fecingtraant) AS fecingtraant  ".
				"	FROM sno_trabajoanterior                 ".
				"  WHERE codper='".$as_codper."'             ".
				"	 AND emppubtraant='1'                    ".
				"	 AND (codded='100' or codded='200')      ";
				
		$rs_data=$this->io_sql->select($ls_sql);
		
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_fechaingtrabant ERROR->".
			                            $this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{			
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$fecingtrabant=$row["fecingtraant"];
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['fecingtrabant']=$fecingtrabant;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end uf_buscar_fechaingtrabant	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalunidad_vipladin_personal($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,
													    $as_activo,$as_egresado, $as_causaegreso,$as_activono,
														$as_vacacionesno,$as_suspendidono,$as_egresadono,
										 		        $as_masculino,$as_femenino,$as_codunivides,$as_codunivihas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalunidad_vipladin_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_masculino // Solo el personal masculino
		//	  			   as_femenino // Solo el personal femenino
		//	    		   as_codunivides // Código de unidad VIPLADIN donde se empieza a filtrar
		//	  			   as_codunivihas // Código de unidad VIPLADIN donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/08/2007 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codunivides))
		{
			$ls_criterio=$ls_criterio."   AND sno_personal.codunivipladin>='".$as_codunivides."' ";
			
		}
		if(!empty($as_codunivihas))
		{
			$ls_criterio=$ls_criterio."   AND sno_personal.codunivipladin<='".$as_codunivihas."' ";
			
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				


		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT DISTINCT sno_personal.codper, sno_personal.nomper, sno_personal.apeper,  ".
				"	   srh_unidadvipladin.denunivipladin, sno_personal.codunivipladin, ".
				"		sno_ubicacionfisica.desubifis, ".
				"		(SELECT despai FROM sigesp_pais ".
				"		  WHERE sigesp_pais.codpai = sno_ubicacionfisica.codpai) AS despai, ".
				"		(SELECT desest FROM sigesp_estados ".
				"		  WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		(SELECT denmun FROM sigesp_municipio ".
				"		  WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		(SELECT denpar FROM sigesp_parroquia ".
				"		  WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
				"       (SELECT codcom FROM sno_componente ".
				"		  WHERE sno_componente.codemp=sno_personal.codemp ".
				"           and sno_componente.codcom=sno_personal.codcom) as codcomponente, ".
				"       (SELECT descom FROM sno_componente ".
				"		  WHERE sno_componente.codemp=sno_personal.codemp ".
				"           AND sno_componente.codcom=sno_personal.codcom) as descomponente, ".
				"       (SELECT codran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as codrango, ".
				"       (SELECT desran FROM sno_rango ".
				"         WHERE sno_rango.codemp=sno_personal.codemp ".
				"			AND sno_rango.codcom=sno_personal.codcom ".
				"           AND sno_rango.codran=sno_personal.codran) as desrango ".
				"  FROM sno_personal, sno_personalnomina, srh_unidadvipladin, sno_ubicacionfisica  ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio.
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"	AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = srh_unidadvipladin.codemp ".
				"	AND sno_personal.codunivipladin = srh_unidadvipladin.codunivipladin  ".				
				"	AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonalunidad_vipladin_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadopersonalunidad_vipladin_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_personal_historial($as_codperdes,$as_codperhas,$as_orden,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_personal_historial
		//	    Arguments: as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 15/10/2008 								Fecha Última Modificación :  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;

		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		
		
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.nomper, sno_personal.apeper  ".
				"  FROM sno_personal  ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				 $ls_criterio.$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_personal_historial ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_select_personal_historial
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_historial($as_codper,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_historial
		//	    Arguments: as_codper // Código de personal 
		//                 rs_data  // resulset con la información del hisotrial del personal
	   //	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del historico de nómina del personal
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 15/10/2008 								Fecha Última Modificación :  		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		
		$ls_sql="SELECT sno_hpersonalnomina.codnom,sno_hpersonalnomina.codperi, sno_hpersonalnomina.codasicar, ".
				" sno_hpersonalnomina.codtab, sno_hpersonalnomina.codgra, sno_hpersonalnomina.codpas, ".
				" sno_hpersonalnomina.sueper,sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, ".
				" sno_hpersonalnomina.uniuniadm, sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, ".
				" sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, sno_hpersonalnomina.grado, ".
				" sno_hpersonalnomina.codcar, sno_nomina.racnom, ". 
				" (SELECT moncomgra FROM sno_hgrado ".
				"   WHERE sno_hgrado.codemp = sno_hpersonalnomina.codemp ".
				"	  AND sno_hgrado.anocur = sno_hpersonalnomina.anocur ".
				"	  AND sno_hgrado.codperi = sno_hpersonalnomina.codperi ".
				"	  AND sno_hgrado.codnom = sno_hpersonalnomina.codnom ".
				"	  AND sno_hgrado.codtab = sno_hpersonalnomina.codtab ".
				"	  AND sno_hgrado.codpas = sno_hpersonalnomina.codpas ".
				"	  AND sno_hgrado.codgra = sno_hpersonalnomina.codgra) as compensacion, ".
				" (SELECT desnom FROM sno_hnomina ".
				"   WHERE sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"     AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"     AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"     AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom) AS desnom,  ".				
				" (SELECT denasicar FROM sno_hasignacioncargo ".
				"   WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
				"     AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
				"     AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar ".
				"     AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
				"     AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi) AS denasicar,  ".				
				" (SELECT descar FROM sno_hcargo ".
				"   WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
				"     AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
				"     AND sno_hpersonalnomina.codcar = sno_hcargo.codcar ".
				"     AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
				"     AND sno_hpersonalnomina.codperi = sno_hcargo.codperi) AS descar, ".												
				" (SELECT fecdesper FROM sno_hperiodo ".
				"   WHERE sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"     AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".				
				"     AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"     AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi) AS fecdesper, ".				
				" (SELECT fechasper FROM sno_hperiodo ".
				"   WHERE sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"     AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".				
				"     AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"     AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi) AS fechasper, ".				
				" (SELECT desuniadm FROM sno_hunidadadmin ".
				"   WHERE sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"     AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".				
				"     AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"     AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"     AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm".
				"     AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"     AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"     AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"     AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm) AS desuniadm, ".
				" (SELECT desded FROM sno_dedicacion ".
				"   WHERE sno_hpersonalnomina.codemp = sno_dedicacion.codemp ".
				"     AND sno_hpersonalnomina.codded = sno_dedicacion.codded) AS desded, ".	
				" (SELECT destipper FROM sno_tipopersonal ".
				"   WHERE sno_hpersonalnomina.codemp = sno_tipopersonal.codemp ".
				"     AND sno_hpersonalnomina.codded = sno_tipopersonal.codded ".
				"     AND sno_hpersonalnomina.codtipper = sno_tipopersonal.codtipper) AS destipper ".				
				"  FROM sno_hpersonalnomina, sno_nomina  ".
				" WHERE sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.codper = '".$as_codper."' ".
				"   AND sno_hpersonalnomina.codemp = sno_nomina.codemp".
				"   AND sno_hpersonalnomina.codnom = sno_nomina.codnom".
				"   AND sno_nomina.espnom='0' ".
				"   ORDER BY sno_hpersonalnomina.codperi, sno_hpersonalnomina.codnom";
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_historial ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_select_historial
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonal_personal_observacion($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_activo,$as_egresado,
													 $as_causaegreso,$as_activono,$as_vacacionesno,$as_suspendidono,$as_egresadono,
													 $as_masculino,$as_femenino,$as_codubifis,$as_codpai,$as_codest,$as_codmun,
													 $as_codpar,$as_orden,$as_uniadmin,$as_codestper,$as_codmunper,$as_codparper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonal_personal_observacion
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_activo // Estatus Activo		  
		//	  			   as_egresado // Estatus Egresado
		//	  			   as_causaegreso // Causa del egreso
		//	  			   as_activono // Estatus Activo dentro de la nómina		  
		//	  			   as_vacacionesno // Estatus Vaciones dentro de la nómina
		//	  			   as_suspendidono // Estatus Suspendido dentro de la nómina
		//	  			   as_egresadono // Estatus Egresado dentro de la nómina
		//	  			   as_masculino //  Sexo del Personal
		//	  			   as_femenino // Sexo del Personal
		//	  			   as_codubifis //Código de Ubicación Física
		//	  			   as_codpai // Código de Pais
		//	  			   as_codest // Código de Estado
		//	  			   as_codmun // Código del Municipio
		//	  			   as_codpar // Código de Parroquia
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codnom<='".$as_codnomhas."'";
			}
			if(!empty($as_activono))
			{
				$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='1' ";
				$lb_ok=true;
			}
			if(!empty($as_vacacionesno))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='2' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='2' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_egresadono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='3' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='3' ";
					$lb_ok=true;
				}
			}
			if(!empty($as_suspendidono))
			{
				if($lb_ok)
				{
					$ls_criterio= $ls_criterio." OR sno_personalnomina.staper='4' ";
				}
				else
				{
					$ls_criterio= $ls_criterio." AND (sno_personalnomina.staper='4' ";
					$lb_ok=true;
				}
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." )";
				$lb_ok=false;
			}
			if(!empty($as_codubifis))
			{
				$ls_criterio= $ls_criterio." AND sno_personalnomina.codubifis='".$as_codubifis."'";
			}
			else
			{
				if(!empty($as_codest))
				{
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				}
				if(!empty($as_codmun))
				{
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				}
				if(!empty($as_codpar))
				{
					$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				}
			}
			if(!empty($as_uniadmin))
			{
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "MYSQLI":				
						$ls_criterio=$ls_criterio."   AND CONCAT(sno_personalnomina.minorguniadm,sno_personalnomina.ofiuniadm,".
												  "              sno_personalnomina.uniuniadm,sno_personalnomina.depuniadm,".
												  "              sno_personalnomina.prouniadm)>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
					case "POSTGRES":
						$ls_criterio=$ls_criterio."   AND sno_personalnomina.minorguniadm||sno_personalnomina.ofiuniadm||".
												  "sno_personalnomina.uniuniadm||sno_personalnomina.depuniadm||".
												  "sno_personalnomina.prouniadm>='".substr($as_uniadmin,0,4).substr($as_uniadmin,5,2).substr($as_uniadmin,8,2).substr($as_uniadmin,11,2).substr($as_uniadmin,14,2)."' ";
					break;
				}
	
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codestper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpai='".$as_codpai."'";
			$ls_criterio= $ls_criterio." AND sno_personal.codest='".$as_codestper."'";
		}
		if(!empty($as_codmunper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codmun='".$as_codmunper."'";
		}
		if(!empty($as_codparper))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codpar='".$as_codparper."'";
		}
		if(!empty($as_activo))
		{
			$ls_criterio= $ls_criterio." AND (sno_personal.estper='1' ";
			$lb_ok=true;
		}
		if(!empty($as_egresado))
		{
			$ls_causaegreso="";
			if(!empty($as_causaegreso))
			{
				$ls_causaegreso=" AND sno_personal.cauegrper='".$as_causaegreso."'";
			}
			if($lb_ok)
			{
				$ls_criterio= $ls_criterio." OR (sno_personal.estper='3' ".$ls_causaegreso.") ";
			}
			else
			{
				$ls_criterio= $ls_criterio." AND (sno_personal.estper='3' ".$ls_causaegreso." ";
				$lb_ok=true;
			}
		}
		if($lb_ok)
		{
			$ls_criterio= $ls_criterio.")";
		}
		$lb_anterior=false;
		if(!empty($as_masculino))
		{
			$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='M' ";
			$lb_anterior=true;
		}
		if(!empty($as_femenino))
		{
			if($lb_anterior)
			{
				$ls_criterio= $ls_criterio. " OR sno_personal.sexper='F' ";
			}
			else
			{
				$ls_criterio= $ls_criterio. " AND (sno_personal.sexper='F' ";
				$lb_anterior=true;
			}
		}
		if($lb_anterior)
		{
			$ls_criterio= $ls_criterio.")";
		}				
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if((!empty($as_codnomdes))&&(!empty($as_codnomhas)))
		{ // si busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.cedper,sno_personal.obsper".
					"  FROM sno_personal, sno_profesion, sno_personalnomina, sno_nomina, sno_dedicacion, sno_tipopersonal, sno_ubicacionfisica ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   ".$ls_criterio.
					"   AND sno_personal.codemp = sno_profesion.codemp ".
					"	AND sno_personal.codpro = sno_profesion.codpro ".
					"   AND sno_personal.codemp = sno_personalnomina.codemp ".
					"	AND sno_personal.codper = sno_personalnomina.codper ".
					"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
					"	AND sno_personalnomina.codnom = sno_nomina.codnom  ".
					"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
					"	AND sno_personalnomina.codded = sno_dedicacion.codded  ".
					"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
					"	AND sno_personalnomina.codded = sno_tipopersonal.codded  ".
					"	AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper  ".
					"   AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
					"	AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".
					"   AND trim(sno_personal.obsper)<>'' ".
					$ls_orden;
		}
		else
		{	// Si no busco por nómina
			$ls_sql="SELECT sno_personal.codper,sno_personal.nomper,sno_personal.apeper,sno_personal.cedper,sno_personal.obsper".
					"  FROM sno_personal ".
					" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
					"   AND trim(sno_personal.obsper)<>'' ".
					"   ".$ls_criterio.$ls_orden;
		} 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonal_personal_observacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_listadopersonal_personal_observacion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cestaticket_personal_excel($as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_codperi,$as_codconcdes,$as_codconchas,
									 $as_conceptocero,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cestaticket_personal_excel
		//		   Access: public (desde la clase sigesp_snorh_rpp_cestaticket)  
		//	    Arguments: as_codnomdes // Código nómina desde
		//	  			   as_codnomhas // Código nómina hasta
		//	    		   as_ano // Año en curso
		//	  			   as_mes // mes
		//	    		   as_codperi // Código del periodo
		//	    		   as_codconcdes // Código del concepto Desde del que se desea busca el personal
		//	    		   as_codconchas // Código del concepto Hasta del que se desea busca el personal
		//				   as_conceptocero // Conceptos en cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   en las nóminas seleccionadas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 10/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.anocur='".$as_ano."' ";
		}
		if(!empty($as_codperi))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codperi='".$as_codperi."' ";
		}
		if(!empty($as_codconcdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc>='".$as_codconcdes."' ";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.codconc<='".$as_codconchas."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}		
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_hsalida.codconc, sno_personal.cedper ";
				break;

			case "51": // Ordena por Unidad Administrativa y Código de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.codper ";
				break;
				
			case "52": // Ordena por Unidad Administrativa y Apellido de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.apeper ";
				break;
				
			case "53": // Ordena por Unidad Administrativa y Nombre de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.nomper ";
				break;
				
			case "54": // Ordena por Unidad Administrativa y Cédula de personal
				$ls_orden="ORDER BY sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_hpersonalnomina.uniuniadm, ".
						  "			sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper,sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, sno_hsalida.tipsal, ".
		        "       sno_hsalida.valsal, ".
				"		sno_cestaticunidadadm.est1cestic , sno_cestaticunidadadm.est2cestic, sno_cestaticket.moncestic, ".
				"		sno_hsalida.codconc, sno_hconcepto.nomcon, sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, sno_cestaticket.mondesdia, ".
				"       sno_hpersonalnomina.uniuniadm, sno_hpersonalnomina.depuniadm, sno_hpersonalnomina.prouniadm, sno_hunidadadmin.desuniadm, srh_gerencia.denger,sno_personal.fecnacper,sno_personal.nacper, ".
				"       (SELECT srh_departamento.dendep FROM srh_departamento                 ".
				"         WHERE srh_departamento.codemp=sno_hpersonalnomina.codemp             ".
				"           AND srh_departamento.coddep=sno_hpersonalnomina.coddep) AS dendep, ".
				"       (SELECT dentippersss FROM sno_tipopersonalsss WHERE codemp=sno_personal.codemp AND codtippersss=sno_personal.codtippersss) AS tipopersonal ".
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hnomina, sno_hconcepto, sno_cestaticunidadadm, sno_cestaticket, ".
				"		sno_hunidadadmin, srh_gerencia ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   AND sno_hpersonalnomina.anocur='".$as_ano."' ".
				"   AND sno_hpersonalnomina.codperi='".$as_codperi."' ".
				"   AND sno_hpersonalnomina.staper='1' ".
				"   AND sno_hnomina.espnom = '1' ".
				"   AND sno_hnomina.ctnom = '1' ".
				"   AND sno_cestaticket.moncestic <> 0 ".
				$ls_criterio.
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hconcepto.codemp = sno_hsalida.codemp ".
				"   AND sno_hconcepto.codnom = sno_hsalida.codnom ".
				"   AND sno_hconcepto.anocur = sno_hsalida.anocur ".
				"   AND sno_hconcepto.codperi = sno_hsalida.codperi ".
				"   AND sno_hconcepto.codconc = sno_hsalida.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_cestaticunidadadm.codemp ".
				"   AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"   AND sno_hpersonalnomina.codperi = sno_hnomina.peractnom ".
				"   AND sno_hnomina.ctmetnom = sno_cestaticket.codcestic ".
				"   AND sno_cestaticket.codemp = sno_cestaticunidadadm.codemp ".
				"   AND sno_cestaticket.codcestic = sno_cestaticunidadadm.codcestic ".
				"   AND sno_hpersonalnomina.codemp = sno_cestaticunidadadm.codemp ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_cestaticunidadadm.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_cestaticunidadadm.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_cestaticunidadadm.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_cestaticunidadadm.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_cestaticunidadadm.prouniadm ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND sno_personal.codemp= srh_gerencia.codemp ".
				"   AND sno_personal.codger= srh_gerencia.codger ".
				"   GROUP BY sno_personal.codper,sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, ".
				"            sno_hsalida.tipsal, sno_hsalida.valsal, sno_cestaticunidadadm.est1cestic , ".
				"            sno_cestaticunidadadm.est2cestic, sno_cestaticket.moncestic, sno_hsalida.codconc,  ".
				"            sno_hconcepto.nomcon, sno_hpersonalnomina.minorguniadm, sno_hpersonalnomina.ofiuniadm, ".
				"            sno_cestaticket.mondesdia, sno_hpersonalnomina.uniuniadm, sno_hpersonalnomina.depuniadm, ".
				"            sno_hpersonalnomina.prouniadm, sno_hunidadadmin.desuniadm, sno_hpersonalnomina.codemp, ".
				"            sno_hpersonalnomina.coddep,sno_personal.codemp,sno_personal.codtippersss, sno_personal.fecnacper,sno_personal.nacper,srh_gerencia.denger ".$ls_orden;	
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cestaticket_personal_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);			
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cestaticket_personal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_cuotas ($as_codcon,$as_codper,$as_codnom,$as_cuota)
    {   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_cuotas
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
        //    Description: función que busca la informaciòn de las códigos unicos asociados a una asignación de cargo
        //       Creado Por: Ing. María Beatriz Unda
        // Fecha Creación: 08/12/2008                                 Fecha Última Modificación :          
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$as_cuota="";            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
                    
        $ls_sql=" SELECT MAX(moncon) AS moncon, MAX(montopcon) AS montopcon   ".                
                "  FROM sno_hconstantepersonal ".                
                "  WHERE sno_hconstantepersonal.codemp='".$ls_codemp."'  ".
				"	  AND sno_hconstantepersonal.codnom='".$as_codnom."' ".
				"	  AND sno_hconstantepersonal.codcons='".$as_codcon."' ".
				"	  AND sno_hconstantepersonal.codper='".$as_codper."' ".
				" GROUP BY moncon, montopcon";  
       
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_cuotas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
		while(!$rs_data->EOF)
		{
			 $as_cuota=$rs_data->fields["moncon"]."/".$rs_data->fields["montopcon"];
			 
			 $rs_data->MoveNext();
		}
		$arrResultado['as_cuota']=$as_cuota;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_buscar_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------------------------------------
   	function uf_buscar_ubicacion_fisica($as_codorg)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_ubicacion_fisica
		//		   Access: public
		//	  Description: Función que obtiene ela ubicacion física del personal según el organigrama
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 09/01/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$lb_valido=true;
		$ls_ubifis="";
		$ls_sql="SELECT codorg, desorg, nivorg, padorg ".				
				"  FROM srh_organigrama ".
				" WHERE srh_organigrama.codemp='".$this->ls_codemp."' ".
				"   AND srh_organigrama.codorg='".$as_codorg."' ".
				"   AND srh_organigrama.codorg <> '----------' ";	
											
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			$lb_hay=$rs_data->RecordCount();
			$li_i=1;
			while(!$rs_data->EOF)
			{
				$ls_codorg=$rs_data->fields["codorg"];
				$ls_desorg=$rs_data->fields["desorg"];
				$ls_nivorg=$rs_data->fields["nivorg"];					
				$ls_padorg=$rs_data->fields["padorg"];
				$la_data[$li_i]=array('cod'=>$ls_codorg,'des'=>$ls_desorg);				
				if ($ls_nivorg<>0)
				{
					for($i=$ls_nivorg;($i>0);$i--)
					{
						$ls_codorgsup=$ls_padorg;
						$ls_despadorg="";
						$ls_nivpadorg="";
						$ls_padorg="";
						$arrResultado=$this->uf_buscar_padre($ls_codorgsup,$ls_despadorg,$ls_nivpadorg,$ls_padorg);
						$ls_despadorg=$arrResultado['as_desorg'];
						$ls_nivpadorg=$arrResultado['as_nivorg'];
						$ls_padorg=$arrResultado['as_padorg'];
						$li_i=$li_i+1;
						$la_data[$li_i]=array('cod'=>$ls_codorgsup,'des'=>$ls_despadorg);
					}	
				}							
				for($j=$li_i;$j>0;$j--)
				{
					if ($j==$li_i)
					{
						$ls_ubifis=$la_data[$j]['des'];
					}
					else
					{						
						$ls_ubifis=$ls_ubifis.' - '.$la_data[$j]['des'];
					}
				}
				$rs_data->MoveNext();
			}
		}
		return $ls_ubifis;
   	}
	//-----------------------------------------------------------------------------------------------------------------------------------
   
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_padre($as_codorg,$as_desorg,$as_nivorg,$as_padorg)
  	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function:uf_buscar_padre
		//		   Access: public
		//	  Description: Función que obtiene e imprime los conceptos a pagar por encargaduría
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 05/01/2009 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		$ls_sql="SELECT codorg, desorg, nivorg, padorg ".				
				"  FROM srh_organigrama ".
				" WHERE srh_organigrama.codemp='".$ls_codemp."' ".
				"   AND srh_organigrama.codorg='".$as_codorg."' ".
				"   AND srh_organigrama.codorg <> '----------' ";	
		$rs_data2=$this->io_sql->select($ls_sql);
		if($rs_data2===false)
		{
			$this->io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			
			while(!$rs_data2->EOF)
			{
				
				$ls_codorg=$rs_data2->fields["codorg"];
				$as_desorg=$rs_data2->fields["desorg"];
				$as_nivorg=$rs_data2->fields["nivorg"];					
				$as_padorg=$rs_data2->fields["padorg"];
				$rs_data2->MoveNext();
			}
		}
		$arrResultado['as_desorg']=$as_desorg;
		$arrResultado['as_nivorg']=$as_nivorg;
		$arrResultado['as_padorg']=$as_padorg;
		return $arrResultado;						
   	}// fin uf_buscar_padre
	//-----------------------------------------------------------------------------------------------------------------------------------

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
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_datos_correo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadogenerico_ipsfa($as_codnomdes,$as_codnomhas,$as_ano,$as_mes,$as_codperi,$as_codperdes,$as_codperhas,$as_orden,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadogenerico_ipsfa
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonalgenerico)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 03/02/2008 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codper>='".$as_codperdes."' ";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codper<='".$as_codperhas."' ";
		}
		if(!empty($as_ano))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.anocur='".$as_ano."' ";
		}
		if(!empty($as_codperi))
		{
			$ls_criterio = $ls_criterio." AND sno_hpersonalnomina.codperi='".$as_codperi."' ";
		}
		
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por Cédula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_hnomina.tipnom, sno_rango.nomabr AS nomabrrango, ".
				"		sno_componente.nomabr AS nomabrcomponente, sno_personal.nomper, sno_personal.apeper, sno_personal.sexper, ".
				"		sno_hcargo.codcar, sno_hcargo.descar, sno_hasignacioncargo.codasicar, sno_hasignacioncargo.denasicar, sno_personal.fecingper, ".
				"		sno_personal.anoservpreper, sno_personal.numhijper, sno_personal.nivacaper, sno_hpersonalnomina.codded, ".
				"       sno_hpersonalnomina.codtipper, sno_hpersonalnomina.fecingper as fecingpernom, sno_hpersonalnomina.fecculcontr, ".
				"		sno_hpersonalnomina.codnom, sno_hpersonalnomina.sueper, sno_hpersonalnomina.sueintper ".
				"  FROM sno_personal ".
				" INNER JOIN (sno_hpersonalnomina ".
				"		INNER JOIN sno_hnomina ".
				"          ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".													
				"         AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')  ".
				"		  AND sno_hnomina.espnom=0 ".					
				$ls_criterio.
				"	      AND sno_hpersonalnomina.codemp = sno_hnomina.codemp ".
				"	      AND sno_hpersonalnomina.codnom = sno_hnomina.codnom ".
				"	      AND sno_hpersonalnomina.anocur = sno_hnomina.anocurnom ".
				"	      AND sno_hpersonalnomina.codperi= sno_hnomina.peractnom ".
				"		INNER JOIN sno_hcargo ".
				"          ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".													
				"         AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')  ".
				$ls_criterio.
				"	      AND sno_hpersonalnomina.codemp = sno_hcargo.codemp  ".
				"	      AND sno_hpersonalnomina.codnom = sno_hcargo.codnom  ".
				"	      AND sno_hpersonalnomina.anocur = sno_hcargo.anocur  ".
				"	      AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
				"	      AND sno_hpersonalnomina.codcar = sno_hcargo.codcar  ".
				"		INNER JOIN sno_hasignacioncargo ".
				"          ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".													
				"         AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')  ".
				$ls_criterio.
				"	      AND sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp  ".
				"	      AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom  ".
				"	      AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur  ".
				"	      AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
				"	      AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar)  ".				
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".													
				$ls_criterio.
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"  LEFT JOIN sno_componente ".
				"    ON sno_personal.codemp = '".$this->ls_codemp."' ".
				"   AND sno_personal.codemp = sno_componente.codemp ".
				"	AND sno_personal.codcom = sno_componente.codcom ".
				"  LEFT JOIN sno_rango ".
				"    ON sno_personal.codemp = '".$this->ls_codemp."' ".
				"   AND sno_personal.codemp = sno_rango.codemp ".
				"	AND sno_personal.codcom = sno_rango.codcom ".
				"	AND sno_personal.codran = sno_rango.codran ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadogenerico_ipsfa ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_listadogenerico_ipsfa
	//-----------------------------------------------------------------------------------------------------------------------------------
  
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_valor_concepto_personal($as_codnom,$as_codper,$as_anio,$as_mes,$as_conceptos,$ad_valor)
  	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_conceptos_personal
		//		   Access: public
		//	  Description: Función que obtiene el valor de los conceptos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/04/2010 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];	
		$ad_valor=0;	
		$lb_valido=true;
		$ls_sql="SELECT SUM(valsal) AS valor ".				
				"  FROM sno_hsalida ".
				" INNER JOIN sno_hperiodo ".
				"    ON sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.codnom='".$as_codnom."'  ".
				"   AND sno_hsalida.anocur= '".$as_anio."' ".
				"   AND sno_hsalida.codper='".$as_codper."'  ".
				"   AND sno_hsalida.codconc IN (".$as_conceptos.") ".
				"   AND sno_hsalida.tipsal IN ('A','D','P1','V1') ".
				"	AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) = '".$as_mes."' ".
				"	AND substr(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_anio."' ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_valor_concepto_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			if(!$rs_data->EOF)
			{
				$ad_valor=(abs($rs_data->fields["valor"]));
			}
		}
		$arrResultado['ad_valor']=$ad_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
   	}// fin uf_buscar_valor_concepto_personal
	//-----------------------------------------------------------------------------------------------------------------------------------   
  
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_titulo_concepto($as_codnomdes,$as_codnomhas,$as_anio,$as_mes,$as_conceptos)
  	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_titulo_concepto
		//		   Access: public
		//	  Description: Función que obtiene el valor de los conceptos 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/04/2010 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];	
		$ad_valor=0;	
		$lb_valido=true;
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(sno_hnomina.tippernom) AS tippernom, MAX(sno_hconcepto.nomcon) AS nomcon  ".				
				"  FROM sno_hconcepto ".
				" INNER JOIN (sno_hperiodo ".
				"       INNER JOIN sno_hnomina ".
				"		   ON sno_hperiodo.codemp='".$ls_codemp."' ".
				"   	  AND sno_hperiodo.anocur= '".$as_anio."' ".
				"	      AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) = '".$as_mes."' ".
				"	      AND substr(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_anio."' ".
				"         AND sno_hperiodo.codemp = sno_hnomina.codemp ".
				"         AND sno_hperiodo.codnom = sno_hnomina.codnom ".
				"         AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
				"         AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
				"    ON sno_hconcepto.codemp='".$ls_codemp."' ".
				"   AND sno_hconcepto.codnom IN ('".$as_codnomdes."','".$as_codnomhas."')  ".
				"   AND sno_hconcepto.anocur= '".$as_anio."' ".
				"   AND sno_hconcepto.codconc IN (".$as_conceptos.") ".
				"   AND sno_hconcepto.codemp = sno_hperiodo.codemp ".
				"   AND sno_hconcepto.codnom = sno_hperiodo.codnom ".
				"   AND sno_hconcepto.anocur = sno_hperiodo.anocur ".
				"   AND sno_hconcepto.codperi = sno_hperiodo.codperi ".
				" GROUP BY sno_hconcepto.codconc ";
		$this->rs_data_conceptos=$this->io_sql->select($ls_sql);
		if($this->rs_data_conceptos===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_titulo_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		return $lb_valido;
   	}// fin uf_buscar_titulo_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------   

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_conceptos_personal($as_codper,$as_codnom,$as_ano,$as_codperi,$ad_comp,$ad_pripro,$ad_priant,$ad_prihij,$ad_bonvac,$ad_bonagu,$ad_sso,$ad_parfor,$ad_lph)
  	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_conceptos_personal
		//		   Access: public
		//	  Description: Función que obtiene los conceptos para el reporte de personal del IPSFA
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 03/02/2009 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];		
		$ad_comp=0;
		$ad_pripro=0;
		$ad_priant=0;
		$ad_prihij=0;
		$ad_bonvac=0;
		$ad_bonagu=0;
		$ad_sso=0;
		$ad_parfor=0;
		$ad_lph=0;	
		$lb_valido=true;
		$ls_sql="SELECT  codconc, valsal  ".				
				"  FROM sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				" AND   sno_hsalida.codnom='".$as_codnom."'  ".
				" AND   sno_hsalida.codper='".$as_codper."'  ".
				" AND   sno_hsalida.codconc IN ('0000000003','0000000004','0000000005','0000000013', ".
				"                               '0000000100','0000001002','0000020000','0000020001', '0000020004' ) ".
				" AND   sno_hsalida.anocur= '".$as_ano."' ".
				" AND   sno_hsalida.codperi='".$as_codperi."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_conceptos_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			while(!$rs_data->EOF)
			{
				
				
				switch($rs_data->fields["codconc"])
				{
					case '0000000003':
						$ad_comp=(abs($rs_data->fields["valsal"]));
						break;
					case '0000000004':
						$ad_prihij=(abs($rs_data->fields["valsal"]));
						break;
					case '0000000005':
						$ad_priant=(abs($rs_data->fields["valsal"]));
						break;
					case '0000000013':
						$ad_pripro=(abs($rs_data->fields["valsal"]));
						break;
					case '0000000100':
						$ad_bonvac=(abs($rs_data->fields["valsal"]));
						break;
					case '0000001002':
						$ad_bonagu=(abs($rs_data->fields["valsal"]));
						break;
					case '0000020000':
						$ad_sso=(abs($rs_data->fields["valsal"]));
						break;
					case '0000020001':
						$ad_parfor=(abs($rs_data->fields["valsal"]));
						break;
					case '0000020004':
						$ad_lph=(abs($rs_data->fields["valsal"]));
						break;
				}
								
				$rs_data->MoveNext();
			}
			
		}
		$arrResultado['ad_comp']=$ad_comp;
		$arrResultado['ad_pripro']=$ad_pripro;
		$arrResultado['ad_priant']=$ad_priant;
		$arrResultado['ad_prihij']=$ad_prihij;
		$arrResultado['ad_bonvac']=$ad_bonvac;
		$arrResultado['ad_bonagu']=$ad_bonagu;
		$arrResultado['ad_sso']=$ad_sso;
		$arrResultado['ad_parfor']=$ad_parfor;
		$arrResultado['ad_lph']=$ad_lph;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
   	}// fin uf_buscar_conceptos_personal
	//-----------------------------------------------------------------------------------------------------------------------------------   

	function uf_buscar_conceptos_personal_fideicomiso($as_codper,$as_codnomdes,$as_anocurperdes,$as_perides,$as_perihas)
  	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_conceptos_personal
		//		   Access: public
		//	  Description: Función que obtiene los conceptos para el reporte de personal del IPSFA
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 03/02/2009 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];		
		$ad_prisoc=0;
		$lb_valido=true;
		$ls_sql="SELECT  codconc, valsal  ".				
				"  FROM sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				" AND   sno_hsalida.codnom='".$as_codnomdes."'  ".
				" AND   sno_hsalida.codper='".$as_codper."'  ".
				" AND   sno_hsalida.codconc IN ('0000001170') ".
				" AND   sno_hsalida.anocur= '".$as_anocurperdes."' ".
				" AND   sno_hsalida.codperi BETWEEN '".$as_perides."' AND '".$as_perihas."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_conceptos_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			while(!$rs_data->EOF)
			{
				
				$ls_prisocial=$rs_data->fields["valsal"];
				$ad_prisoc=$ad_prisoc+$ls_prisocial;
				$rs_data->MoveNext();
			}
			
		}
		return $ad_prisoc;
   	}// fin uf_buscar_conceptos_personal
	//-----------------------------------------------------------------------------------------------------------------------------------   

	//-----------------------------------------------------------------------------------------------------------------------------------   
	function uf_buscar_cestaticket_personal($as_codper,$as_anio,$as_mes,$as_cestaticket,$ad_valor,$ad_cantidad,$ad_valoranual,$ad_cantidadanual)
  	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_cestaticket_personal
		//		   Access: public
		//	  Description: Función que obtiene los conceptos para el reporte de personal del IPSFA
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 03/02/2009 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ad_valoranual=0;	
		$ad_cantidadanual=0;
		$ad_valor=0;
		$ad_cantidad=0;							
		$lb_valido=true;
		$ls_sql="SELECT sno_hsalida.valsal, sno_cestaticket.moncestic, substr(cast(sno_hperiodo.fecdesper as char(10)),6,2) as mes  ".				
				"  FROM sno_hsalida ".
				" INNER JOIN (sno_hperiodo ".
				"       INNER JOIN (sno_hnomina ".
				"			  INNER JOIN sno_cestaticket	".
				"				 ON sno_hnomina.codemp='".$ls_codemp."' ".
				"   			AND sno_hnomina.anocurnom= '".$as_anio."' ".
				"   			AND sno_hnomina.espnom= '1' ".
				"   			AND sno_hnomina.ctnom= '1' ".
				"				AND sno_hnomina.codemp= sno_cestaticket.codemp ".
				"				AND sno_hnomina.ctmetnom= sno_cestaticket.codcestic) ".
				"		   ON sno_hperiodo.codemp='".$ls_codemp."' ".
				"   	  AND sno_hperiodo.anocur= '".$as_anio."' ".
				"	      AND substr(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_anio."' ".
				"         AND sno_hperiodo.codemp = sno_hnomina.codemp ".
				"         AND sno_hperiodo.codnom = sno_hnomina.codnom ".
				"         AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
				"         AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
				"    ON sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur= '".$as_anio."' ".
				"   AND sno_hsalida.codper='".$as_codper."'  ".
				"   AND sno_hsalida.codconc IN (".$as_cestaticket.") ".
				"	AND substr(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_anio."' ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_cestaticket_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		else
		{			
			while(!$rs_data->EOF)
			{
				$ls_valor=abs($rs_data->fields["valsal"]);
				$ls_moncestic=abs($rs_data->fields["moncestic"]);
				$ls_mes=abs($rs_data->fields["mes"]);
				$ad_valoranual=number_format($ad_valoranual+$ls_valor,2,".","");
				$ad_cantidadanual=number_format($ad_cantidadanual+($ls_valor/$ls_moncestic),0,"","");
				if($ls_mes==$as_mes)
				{
					$ad_valor=number_format($ls_valor,2,".","");
					$ad_cantidad=number_format(($ls_valor/$ls_moncestic),0,"","");							
				}
				$rs_data->MoveNext();
			}
		}
		$arrResultado['ad_valor']=$ad_valor;
		$arrResultado['ad_cantidad']=$ad_cantidad;
		$arrResultado['ad_valoranual']=$ad_valoranual;
		$arrResultado['ad_cantidadanual']=$ad_cantidadanual;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
   	}// fin uf_buscar_cestaticket_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadrect ($as_codperi,$as_ano,$as_codestpro,$as_codestpro2,$as_estcla,$as_estcla2,$rs_data)
  	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cuadrect
		//		   Access: public
		//	  Description: Función que obtiene la informacion presupuestaria para reflejar el gasto del 2%
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 03/02/2009 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		$ls_criterio1="";
		$ls_criterio2="";
		//Rango de busqueda para las estructuras
		$as_codestpro= str_pad($as_codestpro,125,'0',0);
		$ls_codestpro1= substr($as_codestpro,0,25);
		$ls_codestpro2= substr($as_codestpro,25,25);
		$ls_codestpro3= substr($as_codestpro,50,25);
		$ls_codestpro4= substr($as_codestpro,75,25);
		$ls_codestpro5= substr($as_codestpro,100,25);
		
		$as_codestpro2= str_pad($as_codestpro2,125,'0',0);
		$ls_codestpro21= substr($as_codestpro2,0,25);
		$ls_codestpro22= substr($as_codestpro2,25,25);
		$ls_codestpro23= substr($as_codestpro2,50,25);
		$ls_codestpro24= substr($as_codestpro2,75,25);
		$ls_codestpro25= substr($as_codestpro2,100,25);
		if(!empty($as_codestpro))
		{
			$ls_criterio1= $ls_criterio1."  AND sno_hunidadadmin.codestpro1>='".$ls_codestpro1."'".
										 "  AND sno_hunidadadmin.codestpro2>='".$ls_codestpro2."'".
										 "  AND sno_hunidadadmin.codestpro3>='".$ls_codestpro3."'".
										 "  AND sno_hunidadadmin.codestpro4>='".$ls_codestpro4."'".
										 "  AND sno_hunidadadmin.codestpro5>='".$ls_codestpro5."'";
		}
		if(!empty($as_codestpro2))
		{
			$ls_criterio1= $ls_criterio1."  AND sno_hunidadadmin.codestpro1<='".$ls_codestpro21."'".
										 "  AND sno_hunidadadmin.codestpro2<='".$ls_codestpro22."'".
										 "  AND sno_hunidadadmin.codestpro3<='".$ls_codestpro23."'".
										 "  AND sno_hunidadadmin.codestpro4<='".$ls_codestpro24."'".
										 "  AND sno_hunidadadmin.codestpro5<='".$ls_codestpro25."'";
		}
		if(!empty($as_codestpro))
		{
			$ls_criterio2= $ls_criterio2."  AND sno_hconcepto.codestpro1>='".$ls_codestpro1."'".
										 "  AND sno_hconcepto.codestpro2>='".$ls_codestpro2."'".
										 "  AND sno_hconcepto.codestpro3>='".$ls_codestpro3."'".
										 "  AND sno_hconcepto.codestpro4>='".$ls_codestpro4."'".
										 "  AND sno_hconcepto.codestpro5>='".$ls_codestpro5."'";
		}
		if(!empty($as_codestpro2))
		{
			$ls_criterio2= $ls_criterio2."  AND sno_hconcepto.codestpro1<='".$ls_codestpro21."'".
										 "  AND sno_hconcepto.codestpro2<='".$ls_codestpro22."'".
										 "  AND sno_hconcepto.codestpro3<='".$ls_codestpro23."'".
										 "  AND sno_hconcepto.codestpro4<='".$ls_codestpro24."'".
										 "  AND sno_hconcepto.codestpro5<='".$ls_codestpro25."'";
		}					  
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT SUM(sno_hsalida.valsal) as valsal, sno_hsalida.tipsal, sno_hnomina.codnom,  MAX(sno_hnomina.desnom) AS desnom, sno_hunidadadmin.codestpro1,  ".
				"		sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, sno_hnomina  ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur='".$as_ano."' ".
				"   AND sno_hsalida.codperi='".$as_codperi."' ".
				"   AND sno_hsalida.tipsal = 'A'".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hnomina.espnom='1' ".
				"   AND sno_hnomina.ctnom='1' ".
				$ls_criterio2.
				"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hnomina.peractnom = sno_hsalida.codperi  ".
				"   AND sno_hnomina.anocurnom = sno_hsalida.anocur  ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, sno_hnomina.codnom, sno_hsalida.tipsal ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT SUM(sno_hsalida.valsal) as valsal, sno_hsalida.tipsal, sno_hnomina.codnom,  MAX(sno_hnomina.desnom) AS desnom, sno_hunidadadmin.codestpro1,  ".
				"		sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, sno_hnomina  ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur='".$as_ano."' ".
				"   AND sno_hsalida.codperi='".$as_codperi."' ".
				"   AND sno_hsalida.tipsal = 'A'  ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hnomina.espnom='1' ".
				"   AND sno_hnomina.ctnom='1' ".
				$ls_criterio1.
				"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hnomina.peractnom = sno_hsalida.codperi  ".
				"   AND sno_hnomina.anocurnom = sno_hsalida.anocur  ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, sno_hnomina.codnom, sno_hsalida.tipsal ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT SUM(sno_hsalida.valsal) as valsal, sno_hsalida.tipsal, sno_hnomina.codnom,  MAX(sno_hnomina.desnom) AS desnom, sno_hunidadadmin.codestpro1,  ".
				"		sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, sno_hnomina  ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur='".$as_ano."' ".
				"   AND sno_hsalida.codperi='".$as_codperi."' ".
				"   AND sno_hsalida.tipsal = 'D' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hnomina.espnom='1' ".
				"   AND sno_hnomina.ctnom='1' ".
				$ls_criterio1.
				"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hnomina.peractnom = sno_hsalida.codperi  ".
				"   AND sno_hnomina.anocurnom = sno_hsalida.anocur  ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"          sno_hunidadadmin.codestpro5, sno_hnomina.codnom, sno_hsalida.tipsal ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, desnom, tipsal"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadrect ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// fin uf_cuadrect
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadrect_estructuras ($as_codperi,$as_ano,$as_codestpro,$as_codestpro2,$as_estcla,$as_estcla2,$rs_data2)
  	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cuadrect
		//		   Access: public
		//	  Description: Función que obtiene la informacion presupuestaria para reflejar el gasto del 2%
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 03/02/2009 								Fecha Última Modificación : 		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$lb_valido=true;
		$ls_criterio1="";
		$ls_criterio2="";
		//Rango de busqueda para las estructuras
		$as_codestpro= str_pad($as_codestpro,125,'0',0);
		$ls_codestpro1= substr($as_codestpro,0,25);
		$ls_codestpro2= substr($as_codestpro,25,25);
		$ls_codestpro3= substr($as_codestpro,50,25);
		$ls_codestpro4= substr($as_codestpro,75,25);
		$ls_codestpro5= substr($as_codestpro,100,25);
		
		$as_codestpro2= str_pad($as_codestpro2,125,'0',0);
		$ls_codestpro21= substr($as_codestpro2,0,25);
		$ls_codestpro22= substr($as_codestpro2,25,25);
		$ls_codestpro23= substr($as_codestpro2,50,25);
		$ls_codestpro24= substr($as_codestpro2,75,25);
		$ls_codestpro25= substr($as_codestpro2,100,25);
		if(!empty($as_codestpro))
		{
			$ls_criterio1= $ls_criterio1."  AND sno_hunidadadmin.codestpro1>='".$ls_codestpro1."'".
										 "  AND sno_hunidadadmin.codestpro2>='".$ls_codestpro2."'".
										 "  AND sno_hunidadadmin.codestpro3>='".$ls_codestpro3."'".
										 "  AND sno_hunidadadmin.codestpro4>='".$ls_codestpro4."'".
										 "  AND sno_hunidadadmin.codestpro5>='".$ls_codestpro5."'";
		}
		if(!empty($as_codestpro2))
		{
			$ls_criterio1= $ls_criterio1."  AND sno_hunidadadmin.codestpro1<='".$ls_codestpro21."'".
										 "  AND sno_hunidadadmin.codestpro2<='".$ls_codestpro22."'".
										 "  AND sno_hunidadadmin.codestpro3<='".$ls_codestpro23."'".
										 "  AND sno_hunidadadmin.codestpro4<='".$ls_codestpro24."'".
										 "  AND sno_hunidadadmin.codestpro5<='".$ls_codestpro25."'";
		}
		if(!empty($as_codestpro))
		{
			$ls_criterio2= $ls_criterio2."  AND sno_hconcepto.codestpro1>='".$ls_codestpro1."'".
										 "  AND sno_hconcepto.codestpro2>='".$ls_codestpro2."'".
										 "  AND sno_hconcepto.codestpro3>='".$ls_codestpro3."'".
										 "  AND sno_hconcepto.codestpro4>='".$ls_codestpro4."'".
										 "  AND sno_hconcepto.codestpro5>='".$ls_codestpro5."'";
		}
		if(!empty($as_codestpro2))
		{
			$ls_criterio2= $ls_criterio2."  AND sno_hconcepto.codestpro1<='".$ls_codestpro21."'".
										 "  AND sno_hconcepto.codestpro2<='".$ls_codestpro22."'".
										 "  AND sno_hconcepto.codestpro3<='".$ls_codestpro23."'".
										 "  AND sno_hconcepto.codestpro4<='".$ls_codestpro24."'".
										 "  AND sno_hconcepto.codestpro5<='".$ls_codestpro25."'";
		}					  
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		sno_hunidadadmin.codestpro5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, sno_hnomina  ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur='".$as_ano."' ".
				"   AND sno_hsalida.codperi='".$as_codperi."' ".
				"   AND sno_hsalida.tipsal = 'A'".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '1'".
				"   AND sno_hnomina.espnom='1' ".
				"   AND sno_hnomina.ctnom='1' ".
				$ls_criterio2.
				"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hnomina.peractnom = sno_hsalida.codperi  ".
				"   AND sno_hnomina.anocurnom = sno_hsalida.anocur  ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		   sno_hunidadadmin.codestpro5 ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		sno_hunidadadmin.codestpro5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, sno_hnomina  ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur='".$as_ano."' ".
				"   AND sno_hsalida.codperi='".$as_codperi."' ".
				"   AND sno_hsalida.tipsal = 'A'  ".
				"   AND sno_hsalida.valsal <> 0".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hnomina.espnom='1' ".
				"   AND sno_hnomina.ctnom='1' ".
				$ls_criterio1.
				"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hnomina.peractnom = sno_hsalida.codperi  ".
				"   AND sno_hnomina.anocurnom = sno_hsalida.anocur  ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		   sno_hunidadadmin.codestpro5 ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos según la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		sno_hunidadadmin.codestpro5 ".
				"  FROM sno_hpersonalnomina, sno_hunidadadmin, sno_hsalida, sno_hconcepto, sno_hnomina  ".
				" WHERE sno_hsalida.codemp='".$ls_codemp."' ".
				"   AND sno_hsalida.anocur='".$as_ano."' ".
				"   AND sno_hsalida.codperi='".$as_codperi."' ".
				"   AND sno_hsalida.tipsal = 'D' ".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hconcepto.intprocon = '0'".
				"   AND sno_hnomina.espnom='1' ".
				"   AND sno_hnomina.ctnom='1' ".
				$ls_criterio1.
				"   AND sno_hnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hnomina.peractnom = sno_hsalida.codperi  ".
				"   AND sno_hnomina.anocurnom = sno_hsalida.anocur  ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hunidadadmin.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				" GROUP BY sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, sno_hunidadadmin.codestpro4, ".
				"		   sno_hunidadadmin.codestpro5 ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5 ";
		$rs_data2=$this->io_sql->select($ls_sql);
		if($rs_data2===false)
		{
			  $this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadrect_estructuras ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			  $lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
   }// fin uf_cuadrect
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonal_rac($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_anio,$as_mes,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonal_rac
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_anio // Año	  
		//	  			   as_mes // Mes
		//	  			   as_orden // Orden en que se quiere sacar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal del RAC
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio1="";
		$ls_criterio2="";
		$ls_criterio3="";
		$ls_criterio4="";
		$ls_orden="";
		$lb_ok=false;
		$ls_concat="";
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
			case "MYSQLT":				
				$ls_concat=$ls_concat." CONCAT(sno_personal.apeper,', ',sno_personal.nomper)";
			break;
			case "MYSQLI":				
				$ls_concat=$ls_concat." CONCAT(sno_personal.apeper,', ',sno_personal.nomper)";
			break;
			case "POSTGRES":
				$ls_concat=$ls_concat."sno_personal.apeper||', '||sno_personal.nomper";
			break;
			case "OCI8PO":
				$ls_concat=$this->io_conexion->Concat('sno_personal.apeper','sno_personal.nomper');
			break;
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio1= $ls_criterio1." AND sno_hpersonalnomina.codnom>='".$as_codnomdes."'";
			$ls_criterio2= $ls_criterio2." AND sno_hasignacioncargo.codnom>='".$as_codnomdes."'";
			$ls_criterio4= $ls_criterio4." AND sno_hcodigounicorac.codnom>='".$as_codnomdes."'";
			if(!empty($as_codnomhas))
			{
				$ls_criterio1= $ls_criterio1." AND sno_hpersonalnomina.codnom<='".$as_codnomhas."'";
				$ls_criterio2= $ls_criterio2." AND sno_hasignacioncargo.codnom<='".$as_codnomhas."'";
				$ls_criterio4= $ls_criterio4." AND sno_hcodigounicorac.codnom<='".$as_codnomhas."'";
			}
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio3= $ls_criterio3." AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio3= $ls_criterio3." AND sno_personal.codper<='".$as_codperhas."'";
		}
		switch($as_orden)
		{
			case "UNIDAD": // Ordena por Código de personal
				$ls_orden=" ORDER BY sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm";
				break;

			case "CODIGONOMINA": // Ordena por Apellido de personal
				$ls_orden=" ORDER BY sno_hasignacioncargo.codded, sno_hcodigounicorac.codunirac ";
				break;
		}
		$ls_sql="SELECT sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm,".
				"       sno_hunidadadmin.desuniadm, sno_hcodigounicorac.codunirac, sno_hasignacioncargo.denasicar, sno_hasignacioncargo.codgra, sno_hasignacioncargo.claasicar,".
				"       sno_hgrado.monsalgra, sno_hgrado.moncomgra, sno_hasignacioncargo.codded, sno_hasignacioncargo.codtipper, sno_hasignacioncargo.codnom, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN  sno_personal.codper ".
				"                 ELSE '0' END) AS codper, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN  sno_personal.cedper ".
				"                 ELSE '0' END) AS cedula, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN  ".$ls_concat."".
				"                 ELSE 'VACANTE' END) AS nombre".
				"  FROM sno_hcodigounicorac ".
				" INNER JOIN (sno_hasignacioncargo".
				"       INNER JOIN sno_hperiodo ".
				"	       ON sno_hasignacioncargo.codemp='".$this->ls_codemp."'".
				$ls_criterio2.
				"	      AND sno_hasignacioncargo.anocur='".$as_anio."'".
				"	      AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."'".
				"	      AND sno_hasignacioncargo.codemp=sno_hperiodo.codemp".
				"	      AND sno_hasignacioncargo.codnom=sno_hperiodo.codnom".
				"	      AND sno_hasignacioncargo.anocur=sno_hperiodo.anocur".
				"	      AND sno_hasignacioncargo.codperi=sno_hperiodo.codperi".
				"       INNER JOIN sno_hunidadadmin ".
				"	       ON sno_hasignacioncargo.codemp='".$this->ls_codemp."'".
				$ls_criterio2.
				"   	  AND sno_hasignacioncargo.anocur='".$as_anio."'".
				"   	  AND sno_hasignacioncargo.codemp=sno_hunidadadmin.codemp".
				"   	  AND sno_hasignacioncargo.codnom=sno_hunidadadmin.codnom".
				"   	  AND sno_hasignacioncargo.anocur=sno_hunidadadmin.anocur".
				"   	  AND sno_hasignacioncargo.codperi=sno_hunidadadmin.codperi".
				"   	  AND sno_hasignacioncargo.minorguniadm=sno_hunidadadmin.minorguniadm".
				"   	  AND sno_hasignacioncargo.ofiuniadm=sno_hunidadadmin.ofiuniadm".
				"   	  AND sno_hasignacioncargo.uniuniadm=sno_hunidadadmin.uniuniadm".
				"   	  AND sno_hasignacioncargo.depuniadm=sno_hunidadadmin.depuniadm".
				"   	  AND sno_hasignacioncargo.prouniadm=sno_hunidadadmin.prouniadm ".
				"   	INNER JOIN sno_hgrado ".
				"   	   ON sno_hasignacioncargo.codemp='".$this->ls_codemp."'".
				$ls_criterio2.
				"   	  AND sno_hasignacioncargo.anocur='".$as_anio."'".
				"   	  AND sno_hasignacioncargo.codemp=sno_hgrado.codemp".
				"   	  AND sno_hasignacioncargo.codnom=sno_hgrado.codnom".
				"   	  AND sno_hasignacioncargo.anocur=sno_hgrado.anocur".
				"   	  AND sno_hasignacioncargo.codperi=sno_hgrado.codperi".
				"   	  AND sno_hasignacioncargo.codtab=sno_hgrado.codtab".
				"   	  AND sno_hasignacioncargo.codpas=sno_hgrado.codpas".
				"   	  AND sno_hasignacioncargo.codgra=sno_hgrado.codgra) ".
				"    ON sno_hcodigounicorac.codemp=sno_hasignacioncargo.codemp".
				"   AND sno_hcodigounicorac.codnom=sno_hasignacioncargo.codnom".
				"   AND sno_hcodigounicorac.anocur=sno_hasignacioncargo.anocur".
				"   AND sno_hcodigounicorac.codperi=sno_hasignacioncargo.codperi".
				"   AND sno_hcodigounicorac.codasicar=sno_hasignacioncargo.codasicar".
				"  LEFT JOIN (sno_hpersonalnomina".
				"       INNER JOIN sno_personal ".
				"   	   ON sno_hpersonalnomina.codemp='".$this->ls_codemp."'".
				$ls_criterio1.
				"   	  AND sno_hpersonalnomina.anocur='".$as_anio."'".
				$ls_criterio3.
				"   	  AND sno_hpersonalnomina.codemp=sno_personal.codemp".
				"   	  AND sno_hpersonalnomina.codper=sno_personal.codper) ".
				"    ON sno_hcodigounicorac.codemp='".$this->ls_codemp."'".
				"   AND sno_hcodigounicorac.anocur='".$as_anio."'".
				"   AND sno_hcodigounicorac.estcodunirac='1' ".
				$ls_criterio4.
				"   AND sno_hcodigounicorac.codemp=sno_hpersonalnomina.codemp".
				"   AND sno_hcodigounicorac.codnom=sno_hpersonalnomina.codnom".
				"   AND sno_hcodigounicorac.anocur=sno_hpersonalnomina.anocur".
				"   AND sno_hcodigounicorac.codperi=sno_hpersonalnomina.codperi".
				"   AND sno_hcodigounicorac.codasicar=sno_hpersonalnomina.codasicar".
				"   AND sno_hcodigounicorac.codunirac=sno_hpersonalnomina.codunirac".
				$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonal_rac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_listadopersonal_rac
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonal_personal_rap($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_anio,$as_mes,$as_peri)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonal_personal_rap
		//         Access: public (desde la clase sigesp_snorh_rpp_listadopersonal_excel_rap)  
		//	    Arguments: as_codnomdes // Código de la nómina donde se empieza a filtrar
		//	  			   as_codnomhas // Código de la nómina donde se termina de filtrar		  
		//	    		   as_codperdes // Código de personal donde se empieza a filtrar
		//	  			   as_codperhas // Código de personal donde se termina de filtrar		  
		//	  			   as_anio // Año		  
		//	  			   as_mes // Mes
		//	  			   as_peri // Periodo
		//	  			   as_orden // Estatus Activo dentro de la nómina		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 24/08/2010 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_concat="";
		$ls_criterio="";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":				
				$ls_concat=$ls_concat." CONCAT(sno_personal.apeper,', ',sno_personal.nomper)";
			break;
			case "MYSQLI":				
				$ls_concat=$ls_concat." CONCAT(sno_personal.apeper,', ',sno_personal.nomper)";
			break;
			case "POSTGRES":
				$ls_concat=$ls_concat."sno_personal.apeper||', '||sno_personal.nomper";
			break;
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codper<='".$as_codperhas."'";
		}
		$ls_sql="SELECT sno_hcodigounicorac.codunirac, sno_personal.fecingper, sno_hasignacioncargo.denasicar, sno_hasignacioncargo.claasicar, ".
				"		sno_hpersonalnomina.sueper, sno_hclasificacionobrero.suemin, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN (SELECT desuniadm ".
				"					      FROM sno_hunidadadmin ".
				"   	 				 WHERE sno_hpersonalnomina.codemp=sno_hunidadadmin.codemp".
				"   	                   AND sno_hpersonalnomina.codnom=sno_hunidadadmin.codnom".
				"   	                   AND sno_hpersonalnomina.anocur=sno_hunidadadmin.anocur".
				"   	                   AND sno_hpersonalnomina.codperi=sno_hunidadadmin.codperi".
				"   	                   AND sno_hpersonalnomina.minorguniadm=sno_hunidadadmin.minorguniadm".
				"   	                   AND sno_hpersonalnomina.ofiuniadm=sno_hunidadadmin.ofiuniadm".
				"   	                   AND sno_hpersonalnomina.uniuniadm=sno_hunidadadmin.uniuniadm".
				"   	                   AND sno_hpersonalnomina.depuniadm=sno_hunidadadmin.depuniadm".
				"   	                   AND sno_hpersonalnomina.prouniadm=sno_hunidadadmin.prouniadm) ".
				"                 ELSE sno_hunidadadmin.desuniadm END) AS desuniadm, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN sno_hpersonalnomina.minorguniadm ".
				"                 ELSE sno_hasignacioncargo.minorguniadm END) AS minorguniadm, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN sno_hpersonalnomina.ofiuniadm ".
				"                 ELSE sno_hasignacioncargo.ofiuniadm END) AS ofiuniadm, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN sno_hpersonalnomina.uniuniadm ".
				"                 ELSE sno_hasignacioncargo.uniuniadm END) AS uniuniadm, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN sno_hpersonalnomina.depuniadm ".
				"                 ELSE sno_hasignacioncargo.depuniadm END) AS depuniadm, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN sno_hpersonalnomina.prouniadm ".
				"                 ELSE sno_hasignacioncargo.prouniadm END) AS prouniadm, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN sno_hpersonalnomina.grado ".
				"                 ELSE sno_hasignacioncargo.grado END) AS grado, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN  sno_personal.cedper ".
				"                 ELSE '0' END) AS cedper, ".
				"       (CASE sno_hcodigounicorac.estcodunirac ".
				"        WHEN '1' THEN  ".$ls_concat."".
				"                 ELSE 'VACANTE' END) AS nomper ".
				"  FROM sno_hcodigounicorac ".
				"  INNER JOIN (sno_hasignacioncargo ".
				"       INNER JOIN sno_hperiodo ".
				"	       ON sno_hasignacioncargo.codemp='".$this->ls_codemp."'".
				"         AND sno_hasignacioncargo.codnom>='".$as_codnomdes."' ".
				"         AND sno_hasignacioncargo.codnom<='".$as_codnomhas."' ".
				"	      AND sno_hasignacioncargo.anocur='".$as_anio."'".
				"	      AND substr(cast(sno_hperiodo.fecdesper as char(10)),6,2)='".$as_mes."'".
				"	      AND sno_hasignacioncargo.codemp=sno_hperiodo.codemp".
				"	      AND sno_hasignacioncargo.codnom=sno_hperiodo.codnom".
				"	      AND sno_hasignacioncargo.anocur=sno_hperiodo.anocur".
				"	      AND sno_hasignacioncargo.codperi=sno_hperiodo.codperi".
				"       INNER JOIN sno_hunidadadmin ".
				"	       ON sno_hasignacioncargo.codemp='".$this->ls_codemp."'".
				"         AND sno_hasignacioncargo.codnom>='".$as_codnomdes."' ".
				"         AND sno_hasignacioncargo.codnom<='".$as_codnomhas."' ".
				"   	  AND sno_hasignacioncargo.anocur='".$as_anio."'".
				"   	  AND sno_hasignacioncargo.codemp=sno_hunidadadmin.codemp".
				"   	  AND sno_hasignacioncargo.codnom=sno_hunidadadmin.codnom".
				"   	  AND sno_hasignacioncargo.anocur=sno_hunidadadmin.anocur".
				"   	  AND sno_hasignacioncargo.codperi=sno_hunidadadmin.codperi".
				"   	  AND sno_hasignacioncargo.minorguniadm=sno_hunidadadmin.minorguniadm".
				"   	  AND sno_hasignacioncargo.ofiuniadm=sno_hunidadadmin.ofiuniadm".
				"   	  AND sno_hasignacioncargo.uniuniadm=sno_hunidadadmin.uniuniadm".
				"   	  AND sno_hasignacioncargo.depuniadm=sno_hunidadadmin.depuniadm".
				"   	  AND sno_hasignacioncargo.prouniadm=sno_hunidadadmin.prouniadm ".
				"       INNER JOIN sno_hclasificacionobrero ".
				"		   ON sno_hasignacioncargo.codemp = '".$this->ls_codemp."' ".
				"         AND sno_hasignacioncargo.codnom>='".$as_codnomdes."' ".
				"         AND sno_hasignacioncargo.codnom<='".$as_codnomhas."' ".
				"         AND sno_hasignacioncargo.anocur= '".$as_anio."'".
				"         AND sno_hasignacioncargo.codperi= '".$as_peri."'".
				"         AND sno_hasignacioncargo.codemp = sno_hclasificacionobrero.codemp ".
				"	      AND sno_hasignacioncargo.codnom = sno_hclasificacionobrero.codnom  ".
				"	      AND sno_hasignacioncargo.anocur = sno_hclasificacionobrero.anocur  ".
				"	      AND sno_hasignacioncargo.codperi = sno_hclasificacionobrero.codperi  ".
				"	      AND sno_hasignacioncargo.grado = sno_hclasificacionobrero.grado)  ".
				"	  ON sno_hcodigounicorac.codemp = '".$this->ls_codemp."' ".
				"    AND sno_hcodigounicorac.codnom>='".$as_codnomdes."' ".
				"    AND sno_hcodigounicorac.codnom<='".$as_codnomhas."' ".
				"    AND sno_hcodigounicorac.anocur= '".$as_anio."'".
				"    AND sno_hcodigounicorac.codemp = sno_hasignacioncargo.codemp ".
				"	 AND sno_hcodigounicorac.codnom = sno_hasignacioncargo.codnom  ".
				"	 AND sno_hcodigounicorac.anocur = sno_hasignacioncargo.anocur  ".
				"	 AND sno_hcodigounicorac.codperi = sno_hasignacioncargo.codperi  ".
				"	 AND sno_hcodigounicorac.codasicar = sno_hasignacioncargo.codasicar  ".
				"  LEFT JOIN (sno_hpersonalnomina".
				"       INNER JOIN sno_personal ".
				"   	   ON sno_hpersonalnomina.codemp='".$this->ls_codemp."'".
				"         AND sno_hpersonalnomina.codnom>='".$as_codnomdes."' ".
				"         AND sno_hpersonalnomina.codnom<='".$as_codnomhas."' ".
				"   	  AND sno_hpersonalnomina.anocur='".$as_anio."'".
				$ls_criterio.
				"   	  AND sno_hpersonalnomina.codemp=sno_personal.codemp".
				"   	  AND sno_hpersonalnomina.codper=sno_personal.codper) ".
				"    ON sno_hcodigounicorac.estcodunirac='1' ".
				"   AND sno_hcodigounicorac.codemp=sno_hpersonalnomina.codemp".
				"   AND sno_hcodigounicorac.codnom=sno_hpersonalnomina.codnom".
				"   AND sno_hcodigounicorac.anocur=sno_hpersonalnomina.anocur".
				"   AND sno_hcodigounicorac.codperi=sno_hpersonalnomina.codperi".
				"   AND sno_hcodigounicorac.codasicar=sno_hpersonalnomina.codasicar".
				"   AND sno_hcodigounicorac.codunirac=sno_hpersonalnomina.codunirac".
				" ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm,  cedper ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listadopersonal_personal_rap ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOD)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_listadopersonal_personal_rap
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestacionantiguedadintereses_personal($as_codnomdes,$as_codnomhas,$as_anocurperdes,$as_mescurperdes,$as_anocurperhas,$as_mescurperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedadintereses_personal
		//         Access: public (desde la clase sigesp_snorh_rpp_prestacionantiguedad_intereses)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 03/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mesdes=str_pad($as_mescurperdes,2,"0",0);
		$li_meshas=str_pad($as_mescurperhas,2,"0",0);
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, (sno_fideiperiodointereses.monantacu-sno_fideiperiodointereses.antpre) as monant,  ".
				"		sno_fideiperiodointereses.porint, sno_fideiperiodointereses.monint, sno_fideiperiodointereses.mescurper, ".
				"		sno_fideiperiodointereses.anocurper  ".
				"  FROM sno_personal, sno_fideiperiodointereses ".
				" WHERE sno_fideiperiodointereses.codemp = '".$this->ls_codemp."' ".
				"   AND sno_fideiperiodointereses.codnom >= '".$as_codnomdes."' ".
				"   AND sno_fideiperiodointereses.codnom <= '".$as_codnomhas."' ".
				"   AND sno_fideiperiodointereses.anocurper >= '".$as_anocurperdes."' ".
				"   AND sno_fideiperiodointereses.mescurper >= '".$li_mesdes."' ".
				"   AND sno_fideiperiodointereses.anocurper <= '".$as_anocurperhas."' ".
				"   AND sno_fideiperiodointereses.mescurper <= '".$li_meshas."' ".
				"   AND sno_personal.codemp = sno_fideiperiodointereses.codemp ".
				"	AND sno_personal.codper = sno_fideiperiodointereses.codper ".
				" ORDER BY sno_personal.codper, sno_fideiperiodointereses.anocurper, sno_fideiperiodointereses.mescurper ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestacionantiguedadintereses_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_prestacionantiguedadintereses_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestacionantiguedad_afectacionpresupuestaria_intereses($as_codnom,$as_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedad_afectacionpresupuestaria
		//         Access: public (desde la clase sigesp_snorh_rpp_contableprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la afectación presupuestaria del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/10/2010 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mes=str_pad($as_mescurper,3,"0",0);
		$ls_sql="SELECT sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
				"		sno_dt_spg.spg_cuenta, spg_cuentas.denominacion, sno_dt_spg.monto ".
				"  FROM sno_dt_spg, spg_cuentas  ".  
				" WHERE sno_dt_spg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_spg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_spg.codperi = '".$li_mes."' ".
				"   AND sno_dt_spg.tipnom = 'K' ".
				"   AND sno_dt_spg.codemp = spg_cuentas.codemp ".
				"   AND sno_dt_spg.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_dt_spg.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_dt_spg.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_dt_spg.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_dt_spg.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_dt_spg.estcla = spg_cuentas.estcla ".
				"   AND sno_dt_spg.spg_cuenta = spg_cuentas.spg_cuenta ".
				" ORDER BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
				"		   sno_dt_spg.estcla, sno_dt_spg.spg_cuenta ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestacionantiguedad_afectacionpresupuestaria_intereses ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_prestacionantiguedad_afectacionpresupuestaria_intereses
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestacionantiguedad_afectacioncontable_intereses($as_codnom,$as_anocurper,$as_mescurper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedad_afectacioncontable_intereses
		//         Access: public (desde la clase sigesp_snorh_rpp_contableprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la afectación contable del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 25/10/2010 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_mes=str_pad($as_mescurper,3,"0",0);
		$ls_sql="SELECT sno_dt_scg.sc_cuenta, sno_dt_scg.debhab, scg_cuentas.denominacion, sno_dt_scg.monto ".
				"  FROM sno_dt_scg, scg_cuentas  ".  
				" WHERE sno_dt_scg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_scg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_scg.codperi = '".$li_mes."' ".
				"   AND sno_dt_scg.tipnom = 'K' ".
				"   AND sno_dt_scg.codemp = scg_cuentas.codemp ".
				"   AND sno_dt_scg.sc_cuenta = scg_cuentas.sc_cuenta ".
				" ORDER BY sno_dt_scg.debhab, sno_dt_scg.sc_cuenta ";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_prestacionantiguedad_afectacioncontable_intereses ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_detalle->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_prestacionantiguedad_afectacioncontable_intereses
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_anio_real($as_codnomdes,$as_perdes)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_anio_real
		//		   Access: private
		//	    Arguments: ai_sueldominimo // Sueldo Mínimo
		//	      Returns: lb_valido True si se obtuvo el Sueldo mínimo ó False si no se obtuvo
		//	  Description: función que busca el sueldo mínimo vigente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/04/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_anio=0;
		$ls_sql="SELECT fecdesper ".
				"  FROM sno_periodo ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codnom='".$as_codnomdes."' ".
				"   AND codperi='".$as_perdes."' ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_obtener_anio_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_anio=substr($row["fecdesper"],0,4);
			}
			$this->io_sql->free_result($rs_data);	
		}
		return $as_anio;
	}// end function uf_obtener_anio_real

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_seleccionar_personal_garantia($as_codper,$as_periodo)	
    {	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function:uf_seleccionar_personal_garantia
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnomdes // código de la nómina desde
		//				   as_codnomhas // código de la nómina hasta
		//				   as_codperides   // còdigo del periodo desde
		//				   as_codperihas   // código del periodo hasta
		//				   as_orden     // forma de ordenamiento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca las nomina y las unidades Administrativas
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;

		$ls_sql=" SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, sno_fideiconfigurable.diabonvacfid, ".
				"        sno_fideiconfigurable.diabonfinfid, sno_personal.nacper, MAX(sno_personal.coreleper) AS coreleper ".
				"   FROM sno_personal, sno_personalnomina, sno_fideiconfigurable ".
				"  WHERE sno_personal.codemp='".$this->ls_codemp."' ".
				"    AND sno_personal.codper='".$as_codper."' ".
				"    AND sno_fideiconfigurable.anocurfid='".$as_periodo."' ".
				"    AND sno_personal.codper=sno_personalnomina.codper ".
				"    AND sno_personalnomina.codded=sno_fideiconfigurable.codded ".
				"    AND sno_personalnomina.codtipper=sno_fideiconfigurable.codtipper ".
				"  GROUP BY sno_personal.codper,sno_personal.cedper,sno_personal.nomper,sno_personal.apeper, sno_personal.fecingper, ".
				"        sno_fideiconfigurable.diabonvacfid,sno_fideiconfigurable.diabonfinfid,sno_personal.nacper"; 
		 $this->rs_data=$this->io_sql->select($ls_sql);
		 if($this->rs_data===false)
		 {
		 	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_personal_garantia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		 }
		 else
		 {
		 	if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		 }		
		 return $lb_valido;	 
	 }// end function uf_seleccionar_personal_garantia
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_deudaanterior($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_deudaanterior
		//		   Access: private
		//	    Arguments: as_codper // perosnal
		//	      Returns: lb_valido True si se obtuvo el Sueldo mínimo ó False si no se obtuvo
		//	  Description: función que busca el sueldo mínimo vigente
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 14/04/2008 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_feccordeu='';
		$ai_monpreant=0;
		$ai_monint=0;
		$ai_monant=0;
		$ai_monantint=0;
		$ls_sql="SELECT feccordeu, monpreant, monint, monant, monantint ".
				"  FROM sno_deudaanterior ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codper='".$as_codper."' ";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_obtener_deudaanterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_feccordeu=substr($row["feccordeu"],0,7);
				$ai_monpreant=$row["monpreant"];
				$ai_monint=$row["monint"];
				$ai_monant=$row["monant"];
				$ai_monantint=$row["monantint"];
			}
			$this->io_sql->free_result($rs_data);	
		}
		$arrResultado['feccordeu']=$as_feccordeu;
		$arrResultado['monpreant']=$ai_monpreant;
		$arrResultado['monint']=$ai_monint;
		$arrResultado['monant']=$ai_monant;
		$arrResultado['monantint']=$ai_monantint;
		$arrResultado['valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_deudaanterior
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_detalles_personal_garantia($as_codper,$as_mes_int,$as_anio)	
    {	
	 	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalles_personal_garantia
		//         Access: public (desde la clase sigesp_snorh_rpp_pagounidad)  
		//	    Arguments: as_codnom // código de la nómina
		// 				   as_codperides // código del periodo desde
		//				   as_codperihas // código del periodo hasta
		//				   as_unidaddes // código de la unidad desde
		//				   as_unidadhas // código de la unidad hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca los pagos por nomina en cada unidad
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 26/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_fecha_comp=$as_anio."-".$as_mes_int."-01";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":				
				$ls_criterio=$ls_criterio." AND CAST(CONCAT(sno_fideiperiodo.anocurper,'-',sno_fideiperiodo.mescurper,'-01) AS DATE)  <= '".$ls_fecha_comp."' ";
				$ls_criterio2=$ls_criterio2." AND CAST(CONCAT(sno_fideiperiodointereses.anocurper,'-',sno_fideiperiodointereses.mescurper,'-01) AS DATE)  <= '".$ls_fecha_comp."' ".
											"    AND  CONCAT(sno_fideiperiodointereses.codemp,sno_fideiperiodointereses.codnom,sno_fideiperiodointereses.codper,sno_fideiperiodointereses.anocurper,sno_fideiperiodointereses.mescurper) ".
										    " NOT IN (SELECT  CONCAT(sno_fideiperiodo.codemp,sno_fideiperiodo.codnom,sno_fideiperiodo.codper,sno_fideiperiodo.anocurper,sno_fideiperiodo.mescurper) FROM sno_fideiperiodo) ";
 
			break;
			case "MYSQLI":				
				$ls_criterio=$ls_criterio." AND CAST(CONCAT(sno_fideiperiodo.anocurper,'-',sno_fideiperiodo.mescurper,'-01) AS DATE)  <= '".$ls_fecha_comp."' ";
				$ls_criterio2=$ls_criterio2." AND CAST(CONCAT(sno_fideiperiodointereses.anocurper,'-',sno_fideiperiodointereses.mescurper,'-01) AS DATE)  <= '".$ls_fecha_comp."' ".
											"    AND  CONCAT(sno_fideiperiodointereses.codemp,sno_fideiperiodointereses.codnom,sno_fideiperiodointereses.codper,sno_fideiperiodointereses.anocurper,sno_fideiperiodointereses.mescurper) ".
										    " NOT IN (SELECT  CONCAT(sno_fideiperiodo.codemp,sno_fideiperiodo.codnom,sno_fideiperiodo.codper,sno_fideiperiodo.anocurper,sno_fideiperiodo.mescurper) FROM sno_fideiperiodo) ";
 
			break;
			case "POSTGRES":
				$ls_criterio=$ls_criterio." AND CAST (sno_fideiperiodo.anocurper||'-'||sno_fideiperiodo.mescurper||'-01' AS DATE)  <= '".$ls_fecha_comp."' ";
				$ls_criterio2=$ls_criterio2." AND CAST (sno_fideiperiodointereses.anocurper||'-'||sno_fideiperiodointereses.mescurper||'-01' AS DATE)  <= '".$ls_fecha_comp."' ".
											"  AND  (sno_fideiperiodointereses.codemp||sno_fideiperiodointereses.codnom||sno_fideiperiodointereses.codper||sno_fideiperiodointereses.anocurper||sno_fideiperiodointereses.mescurper) ".
											"NOT IN (SELECT  (sno_fideiperiodo.codemp||sno_fideiperiodo.codnom||sno_fideiperiodo.codper||sno_fideiperiodo.anocurper||sno_fideiperiodo.mescurper) FROM sno_fideiperiodo) ";
			break;
		}
		
		$ls_sql=" SELECT sno_fideiperiodo.codnom, sno_fideiperiodo.codper, sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper, SUM(sno_fideiperiodo.sueintper) AS sueintper, ".
				" 		 SUM(sno_fideiperiodo.bonvacper) AS bonvacper, SUM(sno_fideiperiodo.bonfinper) AS bonfinper, SUM(sno_fideiperiodo.diafid) AS diafid, SUM(sno_fideiperiodo.diaadi) AS diaadi, SUM(sno_fideiperiodo.apoper) AS apoper, ".
				" 		(SELECT monantacu FROM sno_fideiperiodointereses ".
				"         WHERE sno_fideiperiodointereses.codemp=sno_fideiperiodo.codemp ".
				"           AND sno_fideiperiodointereses.codper=sno_fideiperiodo.codper ".
				"           AND sno_fideiperiodointereses.anocurper=sno_fideiperiodo.anocurper ".
				"           AND sno_fideiperiodointereses.mescurper=sno_fideiperiodo.mescurper) AS antacu, ".
				"       (SELECT montasint FROM sno_fideiintereses ".
				"         WHERE sno_fideiintereses.anoint=sno_fideiperiodo.anocurper ".
				"           AND sno_fideiintereses.mesint=sno_fideiperiodo.mescurper) AS tasa_public, ".
				"       (SELECT monant FROM sno_anticipoprestaciones ".
				"         WHERE sno_anticipoprestaciones.codemp=sno_fideiperiodo.codemp ".
				"           AND sno_anticipoprestaciones.codper=sno_fideiperiodo.codper ".
				"           AND substr(CAST(sno_anticipoprestaciones.fecant as char(10)),0,5)=sno_fideiperiodo.anocurper ".
				"           AND CAST(substr(CAST(sno_anticipoprestaciones.fecant as char(10)),6,2)as integer)=sno_fideiperiodo.mescurper) AS anticipo_prestacion, ".
				"       (SELECT monint FROM sno_anticipoprestaciones ".
				"         WHERE sno_anticipoprestaciones.codper=sno_fideiperiodo.codper ".
				"           AND substr(CAST(sno_anticipoprestaciones.fecant as char(10)),0,5)=sno_fideiperiodo.anocurper ".
				"           AND CAST(substr(cast(sno_anticipoprestaciones.fecant as char(10)),6,2)as integer)=sno_fideiperiodo.mescurper) AS intereses_pagados, ".
				"       (SELECT monint FROM sno_fideiperiodointereses ".
				"         WHERE sno_fideiperiodointereses.codemp=sno_fideiperiodo.codemp ".
				"           AND sno_fideiperiodointereses.codper=sno_fideiperiodo.codper ".
				"           AND sno_fideiperiodointereses.anocurper=sno_fideiperiodo.anocurper ".
				"           AND sno_fideiperiodointereses.mescurper=sno_fideiperiodo.mescurper) AS intereses_mens ".
				"   FROM sno_fideiperiodo ".
				"  WHERE sno_fideiperiodo.codemp='".$this->ls_codemp."' ".
				"    AND sno_fideiperiodo.codper='".$as_codper."' ".
				"   ".$ls_criterio." ".
				"  GROUP BY sno_fideiperiodo.codemp, sno_fideiperiodo.codnom, sno_fideiperiodo.codper, sno_fideiperiodo.anocurper, sno_fideiperiodo.mescurper ".
				"  UNION ".
				" SELECT sno_fideiperiodointereses.codnom, sno_fideiperiodointereses.codper, sno_fideiperiodointereses.anocurper, sno_fideiperiodointereses.mescurper, ".
				"        0 as sueintper, 0 as bonvacper, 0 as bonfinper, 0 as diafid,0 as diaadi,0 as apoper, SUM(monantacu)  AS antacu, ".
				"       (SELECT montasint FROM sno_fideiintereses ".
				"         WHERE sno_fideiintereses.anoint=sno_fideiperiodointereses.anocurper ".
				"           AND sno_fideiintereses.mesint=sno_fideiperiodointereses.mescurper) AS tasa_public, ".
				"        (SELECT monant FROM sno_anticipoprestaciones ".
				"          WHERE sno_anticipoprestaciones.codemp=sno_fideiperiodointereses.codemp ".
				"            AND sno_anticipoprestaciones.codper=sno_fideiperiodointereses.codper ".
				"            AND substr(CAST(sno_anticipoprestaciones.fecant as char(10)),0,5)=sno_fideiperiodointereses.anocurper ".
				"            AND CAST(substr(CAST(sno_anticipoprestaciones.fecant as char(10)),6,2)as integer)=sno_fideiperiodointereses.mescurper) AS anticipo_prestacion, ". 
				"        (SELECT monint FROM sno_anticipoprestaciones ".
				"          WHERE sno_anticipoprestaciones.codper=sno_fideiperiodointereses.codper ".
				"            AND substr(CAST(sno_anticipoprestaciones.fecant as char(10)),0,5)=sno_fideiperiodointereses.anocurper ".
				"            AND CAST(substr(cast(sno_anticipoprestaciones.fecant as char(10)),6,2)as integer)=sno_fideiperiodointereses.mescurper) AS intereses_pagados, ".
				"         SUM(monint)  AS intereses_mens ".        
				"   FROM sno_fideiperiodointereses ".
				"  WHERE sno_fideiperiodointereses.codemp='".$this->ls_codemp."' ".
				"    AND sno_fideiperiodointereses.codper='".$as_codper."' ".
				"   ".$ls_criterio2." ".
				"  GROUP BY  sno_fideiperiodointereses.codemp, sno_fideiperiodointereses.codnom, sno_fideiperiodointereses.codper, sno_fideiperiodointereses.anocurper, sno_fideiperiodointereses.mescurper ". 
				"  ORDER BY anocurper,mescurper ";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
		 	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_detalles_personal_garantia ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;	 
	 }// end function uf_pagos_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_personalregistrado($as_codperdes,$as_codperhas,$as_codusu,$as_fecdes,$as_fechas,$as_orden)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_personalregistrado
		//         Access: public (desde la clase sigesp_sno_rpp_litadopersonaljubilado)  
		//      Arguments: 
		//        Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: 
		//     Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: 
		// Fecha Creación: 25/08/2008                                Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio=""; 
		$ls_orden=""; 
		if (($as_codperdes!="")&&($as_codperhas!=""))
		{   
			$ls_criterio=$ls_criterio." AND sno_personal.codper BETWEEN '".$as_codperdes."' AND '".$as_codperhas."'";
		}
		if (($as_fecdes!="")&&($as_fechas!=""))
		{   
			$ls_criterio=$ls_criterio." AND sno_personal.fecreg BETWEEN '".$as_fecdes."' AND '".$as_fechas."'";
		}
		if ($as_codusu!="")
		{   
			$ls_criterio=$ls_criterio." AND sno_personal.codusu = '".$as_codusu."'";
		}
		switch ($as_orden)
		{
			case 1:
				$ls_orden= " ORDER BY sno_personal.codper";
			break;
			case 2:
				$ls_orden= " ORDER BY sno_personal.nomper";
			break;
			case 3:
				$ls_orden= " ORDER BY sno_personal.apeper";
			break;
		}
		$ls_sql="SELECT DISTINCT (sno_personal.codper), sno_personal.nomper, sno_personal.apeper,sno_personal.fecingper,sno_profesion.despro, ".
				"		sno_tipopersonalsss.dentippersss,sno_personal.estper,sno_personal.codusu,sno_personal.fecreg ".
				"  FROM sno_personal ".
				"   LEFT JOIN sno_profesion ON sno_profesion.codemp = sno_personal.codemp AND sno_profesion.codpro = sno_personal.codpro".			   
				"   LEFT JOIN sno_tipopersonalsss ON sno_tipopersonalsss.codemp = sno_personal.codemp AND sno_tipopersonalsss.codtippersss = sno_personal.codtippersss".
				" WHERE sno_personal.codemp='".$this->ls_codemp."'".
				$ls_criterio.
				$ls_orden;
	 $this->rs_data=$this->io_sql->select($ls_sql);
     if($this->rs_data===false)
     {
     	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_listado_personalregistrado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        $lb_valido=false;
     }
	return $lb_valido;
	}//FIN uf_listado_personalregistrado
	//------------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_contableconceptosaportes_presupuesto($as_codnom,$as_codperides,$as_codperihas,$as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptosaportes_presupuesto
		//         Access: public (desde la clase sigesp_snorh_r_cuadreprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_codperides // Periodo Desde		  
		//	  			   as_codperihas // Periodo Hasta		  
		//	  			   as_tipo // Tipo		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan la prestación antiguedad
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_filtro='';
		if ($as_tipo == 'N')
		{
			$ls_filtro=" AND sno_dt_spg.tipnom = 'N'";
		}
		elseif($as_tipo == 'A')
		{
			$ls_filtro=" AND sno_dt_spg.tipnom = 'A'";
		}
		
		$ls_sql="SELECT sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, sno_dt_spg.estcla, ".
				"		sno_dt_spg.spg_cuenta, MAX(spg_cuentas.denominacion) AS denominacion, SUM(sno_dt_spg.monto) AS monto ".
				"  FROM sno_dt_spg, spg_cuentas  ".  
				" WHERE sno_dt_spg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_spg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_spg.codperi >= '".$as_codperides."' ".
				"   AND sno_dt_spg.codperi <= '".$as_codperihas."' ".
				"   ".$ls_filtro." ".
				"   AND sno_dt_spg.codemp=spg_cuentas.codemp ".  
				"   AND sno_dt_spg.codestpro1=spg_cuentas.codestpro1 ".  
				"   AND sno_dt_spg.codestpro2=spg_cuentas.codestpro2 ".  
				"   AND sno_dt_spg.codestpro3=spg_cuentas.codestpro3 ".  
				"   AND sno_dt_spg.codestpro4=spg_cuentas.codestpro4 ".  
				"   AND sno_dt_spg.codestpro5=spg_cuentas.codestpro5 ".  
				"   AND sno_dt_spg.estcla=spg_cuentas.estcla ".  
				"   AND sno_dt_spg.spg_cuenta=spg_cuentas.spg_cuenta ".  
				" GROUP BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, sno_dt_spg.estcla, sno_dt_spg.spg_cuenta ".
				" ORDER BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, sno_dt_spg.estcla, sno_dt_spg.spg_cuenta ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreprestacionantiguedad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_contableconceptosaportes_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_contableconceptosaportes_contabilidad($as_codnom,$as_codperides,$as_codperihas,$as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptosaportes_contable
		//         Access: public (desde la clase sigesp_snorh_r_cuadreprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_codperides // Periodo Desde		  
		//	  			   as_codperihas // Periodo Hasta		  
		//	  			   as_tipo // Tipo		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las cuentas presupuestarias que afectan la prestación antiguedad
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 23/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_filtro='';
		if ($as_tipo == 'N')
		{
			$ls_filtro=" AND sno_dt_scg.tipnom = 'N'";
		}
		elseif($as_tipo == 'A')
		{
			$ls_filtro=" AND sno_dt_scg.tipnom = 'A'";
		}
		
		$ls_sql="SELECT sno_dt_scg.debhab, sno_dt_scg.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, SUM(sno_dt_scg.monto) AS monto ".
				"  FROM sno_dt_scg, scg_cuentas  ".  
				" WHERE sno_dt_scg.codemp = '".$this->ls_codemp."' ".
				"   AND sno_dt_scg.codnom = '".$as_codnom."' ".
				"   AND sno_dt_scg.codperi >= '".$as_codperides."' ".
				"   AND sno_dt_scg.codperi <= '".$as_codperihas."' ".
				"   ".$ls_filtro." ".
				"   AND sno_dt_scg.codemp=scg_cuentas.codemp ".  
				"   AND sno_dt_scg.sc_cuenta=scg_cuentas.sc_cuenta ".  
				" GROUP BY sno_dt_scg.debhab, sno_dt_scg.sc_cuenta ".
				" ORDER BY sno_dt_scg.debhab, sno_dt_scg.sc_cuenta ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_cuadreprestacionantiguedad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_contableconceptosaportes_contable
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_tipo_personal($criterioubicacion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_tipo_personal
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_aporte)  
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_sql=" SELECT sno_personalnomina.codubifis, MAX(sno_ubicacionfisica.desubifis) as desubifis, COUNT(sno_personalnomina.codper) AS empleadosfijos, 0 AS obrerosfijos, 0 AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".     
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				$criterioubicacion.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0101','0102','0103','0104','0105','0106','0108','0201','0202','0203','0204','0205','0206') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper  ".
				"  GROUP BY sno_personalnomina.codubifis ".
				"  UNION ".
				" SELECT sno_personalnomina.codubifis, MAX(sno_ubicacionfisica.desubifis) as desubifis, 0 AS empleadosfijos, COUNT(sno_personalnomina.codper) AS obrerosfijos,  0 AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".      
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				$criterioubicacion.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".    
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0107','0207') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper      ".
				"  GROUP BY sno_personalnomina.codubifis ".
				"  UNION ".
				" SELECT sno_personalnomina.codubifis, MAX(sno_ubicacionfisica.desubifis) as desubifis, 0 AS empleadosfijos, 0 AS obrerosfijos, COUNT(sno_personalnomina.codper) AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".      
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				$criterioubicacion.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".    
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0301','0302','0303','0304','0305','0306','0307','0308') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".     
				"  GROUP BY sno_personalnomina.codubifis  ".
				"  UNION  ".
				" SELECT sno_personalnomina.codubifis, MAX(sno_ubicacionfisica.desubifis) as desubifis, 0 AS empleadosfijos, 0 AS obrerosfijos, 0 AS empleadoscontratados, COUNT(sno_personalnomina.codper) AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".      
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				$criterioubicacion.
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".    
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0307') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper      ".
				"  GROUP BY sno_personalnomina.codubifis ".    
				"  ORDER BY  codubifis ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_tipo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);	
				$this->DS_detalle->group_by(array('0'=>'codubifis'),array('0'=>'empleadosfijos','1'=>'obrerosfijos','2'=>'empleadoscontratados','3'=>'obreroscontratados'),'empleadosfijos');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_tipo_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_tipo_personalunidadadmin($criterioubicacion,$criterionomina)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_tipo_personal
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_aporte)  
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_sql=" SELECT sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm, ".
				"         MAX(sno_unidadadmin.desuniadm) as desubifis, COUNT(sno_personalnomina.codper) AS empleadosfijos, 0 AS obrerosfijos, 0 AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    ".$criterionomina.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".     
				"  INNER JOIN sno_unidadadmin ".
				"     ON sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".     
				"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".     
				"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".     
				"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".     
				"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".     
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				$criterioubicacion.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0101','0102','0103','0104','0105','0106','0108','0201','0202','0203','0204','0205','0206') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper  ".
				"  GROUP BY sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm ".
				"  UNION ".
				" SELECT sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm, ".
				"         MAX(sno_unidadadmin.desuniadm) as desubifis, 0 AS empleadosfijos, COUNT(sno_personalnomina.codper) AS obrerosfijos, 0 AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".      
				"  INNER JOIN sno_unidadadmin ".
				"     ON sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".     
				"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".     
				"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".     
				"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".     
				"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".     
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				$criterioubicacion.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".    
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0107','0207') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper      ".
				"  GROUP BY sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm ".
				"  UNION ".
				" SELECT sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm, ".
				"         MAX(sno_unidadadmin.desuniadm) as desubifis, 0 AS empleadosfijos, 0 AS obrerosfijos, COUNT(sno_personalnomina.codper) AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".      
				"  INNER JOIN sno_unidadadmin ".
				"     ON sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".     
				"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".     
				"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".     
				"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".     
				"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".     
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				$criterioubicacion.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".    
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0301','0302','0303','0304','0305','0306','0307','0308') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".     
				"  GROUP BY sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm ".
				"  UNION ".
				" SELECT sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm, ".
				"         MAX(sno_unidadadmin.desuniadm) as desubifis, 0 AS empleadosfijos, 0 AS obrerosfijos, 0 AS empleadoscontratados, COUNT(sno_personalnomina.codper) AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    ".$criterionomina.
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".      
				"  INNER JOIN sno_unidadadmin ".
				"     ON sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".     
				"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".     
				"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".     
				"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".     
				"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".     
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				$criterioubicacion.
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis  ".    
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0307') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper      ".
				"  GROUP BY sno_personalnomina.minorguniadm, sno_personalnomina.ofiuniadm, sno_personalnomina.uniuniadm, sno_personalnomina.depuniadm, sno_personalnomina.prouniadm ".
				"  ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_tipo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);	
				$this->DS_detalle->group_by(array('0'=>'minorguniadm','1'=>'ofiuniadm','2'=>'uniuniadm','3'=>'depuniadm','4'=>'prouniadm'),array('0'=>'empleadosfijos','1'=>'obrerosfijos','2'=>'empleadoscontratados','3'=>'obreroscontratados'),'empleadosfijos');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_tipo_personalunidadadmin
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_tipo_personaljubilado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_tipo_personaljubilado
		//		   Access: public (desde la clase sigesp_snorh_rpp_ipasme_aporte)  
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 21/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);
		$lb_valido=true;
		$ls_sql=" SELECT sno_personalnomina.codubifis, 1 AS tipo, 'Jubilados y Pensionados' as desubifis, COUNT(sno_personalnomina.codper) AS empleadosfijos, 0 AS obrerosfijos, 0 AS empleadoscontratados, 0 AS obreroscontratados ".
				"   FROM sno_personalnomina ".
				"  INNER JOIN sno_nomina ".
				"     ON sno_nomina.espnom = '0' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".     
				"  INNER JOIN sno_ubicacionfisica ".
				"     ON sno_ubicacionfisica.codubifis<>'0000' ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_ubicacionfisica.codemp ".
				"    AND sno_personalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"  INNER JOIN sno_tipopersonal ".
				"     ON sno_tipopersonal.codtipper IN ('0109','0110') ".
				"    AND sno_personalnomina.staper IN ('1','2') ".
				"    AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"    AND sno_personalnomina.codded = sno_tipopersonal.codded ".     
				"    AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper  ".
				"  GROUP BY sno_personalnomina.codubifis ".
				"  ORDER BY  codubifis ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_tipo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);	
				$this->DS_detalle->group_by(array('0'=>'tipo'),array('0'=>'empleadosfijos','1'=>'obrerosfijos','2'=>'empleadoscontratados','3'=>'obreroscontratados'),'empleadosfijos');
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_tipo_personaljubilado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//------------------------------------------------------------------------------------------------------------------------------------
	function uf_resumennomina($as_codnomdes,$as_codnomhas)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumennomina
		//         Access: public (desde la clase sigesp_sno_rpp_litadopersonaljubilado)  
		//      Arguments: 
		//        Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: 
		//     Creado Por: Ing. Luis Anibal Lang
		// Modificado Por: 
		// Fecha Creación: 25/08/2008                                Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio=""; 
		$ls_orden=""; 
		if (($as_codnomdes!="")&&($as_codnomhas!=""))
		{   
			$ls_criterio=$ls_criterio." AND sno_periodo.codnom BETWEEN '".$as_codnomdes."' AND '".$as_codnomhas."'";
		}
	    $ls_sql="SELECT sno_periodo.codperi, sno_periodo.fecdesper, sno_periodo.fechasper, sno_periodo.cerper, sno_periodo.conper, sno_periodo.apoconper, ".
				"       sno_periodo.totper, sno_nomina.codnom, sno_nomina.desnom ".
	            "  FROM sno_periodo ".
				" INNER JOIN sno_nomina ".
				"    ON sno_periodo.codemp='".$this->ls_codemp."' ".
				"   AND sno_periodo.codperi <> '000' ".
				$ls_criterio.
				"   AND sno_periodo.codemp=sno_nomina.codemp ".
				"   AND sno_periodo.codnom=sno_nomina.codnom ".
				" ORDER BY sno_periodo.codnom, sno_periodo.codperi ";
	 $this->rs_data=$this->io_sql->select($ls_sql);
     if($this->rs_data===false)
     {
     	$this->io_mensajes->message("CLASE->Report MÉTODO->uf_resumennomina ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        $lb_valido=false;
     }
	return $lb_valido;
	}//FIN uf_resumennomina
	//------------------------------------------------------------------------------------------------------------------------------------
   
	//-----------------------------------------------------------------------------------------------------------------------------------
        function uf_instructivo_0205_programado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_instructivo_0205_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_isntructivo)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación:  								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql=" SELECT codrep,codded,codtipper, monene,monfeb,monmar,monabr,monmay,monjun, ".
                        "        monjul,monago,monsep,monoct,monnov,mondic, carene,carfeb,carmar,carabr,carmay,carjun, ".
                        "        carjul,carago,carsep,caroct,carnov,cardic, carenef,carfebf,carmarf,carabrf,carmayf,carjunf, ".
                        "        carjulf,caragof,carsepf,caroctf,carnovf,cardicf, carenem,carfebm,carmarm,carabrm,carmaym,carjunm, ".
                        "        carjulm,caragom,carsepm,caroctm,carnovm,cardicm, ".
                        "        (SELECT sno_dedicacion.desded FROM  sno_dedicacion ".
                        "          WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
                        "            AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
                        "        (SELECT sno_tipopersonal.destipper FROM  sno_tipopersonal ".
                        "	   WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
                        "	     AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
                        "	     AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
                        "  FROM sno_programacionreporte ".
                        " WHERE codemp = '".$this->ls_codemp."'".
                        "   AND codrep = '0205'".
                        "   AND codtipper NOT IN ('0108','0109','0110') ".
                        " ORDER BY codded, codtipper ";  
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_0205_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data->EOF)
			{
				$lb_valido=false;
			}
		}		
		return $lb_valido;
	}// end function uf_instructivo_07_cargos_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

        //-----------------------------------------------------------------------------------------------------------------------------------
	function uf_instructivo_0205_cargos_real($as_rango,$as_codded,$as_codtipper,$ai_cargoreal,$ai_cargorealf,$ai_cargorealm,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_instructivo_0205_cargos_real
		//         Access: public (desde la clase sigesp_snorh_rpp_isntructivo_07_cargos)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_anio=substr($_SESSION["la_empresa"]["periodo"],0,4);
		switch($as_rango)
		{
			case "01-01":
				$randes=$ls_anio."-01-01";
				$ranhas=$ls_anio."-01-31";
			break;
			case "02-02":
				$randes=$ls_anio."-02-01";
				$ranhas=$ls_anio."-02-28";
			break;
			case "03-03":
				$randes=$ls_anio."-03-01";
				$ranhas=$ls_anio."-03-31";
			break;
			case "04-04":
				$randes=$ls_anio."-04-01";
				$ranhas=$ls_anio."-04-30";
			break;
			case "05-05":
				$randes=$ls_anio."-05-01";
				$ranhas=$ls_anio."-05-31";
			break;
			case "06-06":
				$randes=$ls_anio."-06-01";
				$ranhas=$ls_anio."-06-30";
			break;
			case "07-07":
				$randes=$ls_anio."-07-01";
				$ranhas=$ls_anio."-07-31";
			break;
			case "08-08":
				$randes=$ls_anio."-08-01";
				$ranhas=$ls_anio."-08-31";
			break;
			case "09-09":
				$randes=$ls_anio."-09-01";
				$ranhas=$ls_anio."-09-30";
			break;
			case "10-10":
				$randes=$ls_anio."-10-01";
				$ranhas=$ls_anio."-10-31";
			break;
			case "11-11":
				$randes=$ls_anio."-11-01";
				$ranhas=$ls_anio."-11-30";
			break;
			case "12-12":
				$randes=$ls_anio."-12-01";
				$ranhas=$ls_anio."-12-31";
			break;
			case "01-03":
				$randes=$ls_anio."-01-01";
				$ranhas=$ls_anio."-03-31";
			break;
			case "02-04":
				$randes=$ls_anio."-02-01";
				$ranhas=$ls_anio."-04-30";
			break;
			case "03-05":
				$randes=$ls_anio."-03-01";
				$ranhas=$ls_anio."-05-31";
			break;
			case "04-06":
				$randes=$ls_anio."-04-01";
				$ranhas=$ls_anio."-06-30";
			break;
			case "05-07":
				$randes=$ls_anio."-05-01";
				$ranhas=$ls_anio."-07-31";
			break;
			case "06-08":
				$randes=$ls_anio."-06-01";
				$ranhas=$ls_anio."-08-31";
			break;
			case "07-09":
				$randes=$ls_anio."-07-01";
				$ranhas=$ls_anio."-09-30";
			break;
			case "08-10":
				$randes=$ls_anio."-08-01";
				$ranhas=$ls_anio."-10-31";
			break;
			case "09-11":
				$randes=$ls_anio."-09-01";
				$ranhas=$ls_anio."-11-30";
			break;
			case "10-12":
				$randes=$ls_anio."-10-01";
				$ranhas=$ls_anio."-12-31";
			break;
		}
		$ls_sql="SELECT sno_personal.sexper, COUNT(sno_hpersonalnomina.codper) as totalcargos ".
                        "  FROM sno_hpersonalnomina ".
                        " INNER JOIN (sno_hperiodo ".
                        " 		INNER JOIN sno_hnomina ".
                        "         ON sno_hperiodo.fecdesper >= '".$randes."'".
                        "   	 AND sno_hperiodo.fechasper  <= '".$ranhas."'".
                        "        AND sno_hnomina.tipnom <> 7 ".
                        "        AND sno_hnomina.espnom = '0' ".
                        "        AND sno_hnomina.ctnom = '0' ".
                        "        AND sno_hperiodo.codemp = sno_hnomina.codemp ".
                        "        AND sno_hperiodo.codnom = sno_hnomina.codnom ".
                        "	     AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
                        "        AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
                        "    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
                        "   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
                        "   AND sno_hpersonalnomina.codded='".$as_codded."'".
                        "   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
                        "   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
                        "   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
                        "   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
                        "	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
                        "   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
                        " INNER JOIN sno_personal ".
                        "    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
                        "   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
                        "   AND sno_hpersonalnomina.codded='".$as_codded."'".
                        "   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
                        "   AND sno_hpersonalnomina.staper='1'".
                        "   AND sno_personal.codemp=sno_hpersonalnomina.codemp ".
                        "   AND sno_personal.codper=sno_hpersonalnomina.codper ".
                        " GROUP BY sno_hpersonalnomina.codperi, sno_personal.sexper ".
                        " ORDER BY sno_hpersonalnomina.codperi ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_0205_cargos_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ai_cargoreal=0;
			$ai_cargorealf=0;
			$ai_cargorealm=0;
			while(!$rs_data->EOF)
			{
				switch($rs_data->fields["sexper"])
				{
					case "F";
						$ai_cargorealf=$rs_data->fields["totalcargos"];
					break;
					case "M";
						$ai_cargorealm=$rs_data->fields["totalcargos"];
					break;
				}
				$ai_cargoreal=$ai_cargorealf+$ai_cargorealm;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
		if ($lb_valido)	
		{	
			$ls_sql="SELECT SUM(sno_hsalida.valsal) as monto ".
                                "  FROM sno_hpersonalnomina ".
                                " INNER JOIN (sno_hperiodo ".
                                " 		INNER JOIN sno_hnomina ".
                                "         ON sno_hperiodo.fecdesper >= '".$randes."'".
                                "   	 AND sno_hperiodo.fechasper  <= '".$ranhas."'".
                                "        AND sno_hnomina.tipnom <> 7 ".
                                "        AND sno_hnomina.espnom = '0' ".
                                "        AND sno_hnomina.ctnom = '0' ".
                                "        AND sno_hperiodo.codemp = sno_hnomina.codemp ".
                                "        AND sno_hperiodo.codnom = sno_hnomina.codnom ".
                                "	     AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
                                "        AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
                                "    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
                                "   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
                                "   AND sno_hpersonalnomina.codded='".$as_codded."'".
                                "   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
                                "   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
                                "   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
                                "   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
                                "	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
                                "   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
                                " INNER JOIN sno_hsalida ".
                                "    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
                                "   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
                                "   AND sno_hpersonalnomina.codded='".$as_codded."'".
                                "   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
                                "   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
                                "   AND sno_hsalida.valsal <> 0 ".
                                "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
                                "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
                                "	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
                                "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
                                "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_0205_cargos_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else
			{
				$ai_montoreal=0;
				if(!$rs_data->EOF)
				{
					$ai_montoreal=$rs_data->fields["monto"];
				}
				$this->io_sql->free_result($rs_data);
			}		
		}
		$arrResultado['ai_cargoreal']=$ai_cargoreal;
		$arrResultado['ai_cargorealf']=$ai_cargorealf;
		$arrResultado['ai_cargorealm']=$ai_cargorealm;
		$arrResultado['ai_montoreal']=$ai_montoreal;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end uf_instructivo_0205_cargos_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_instructivo_0205_monto_acumulado($as_rango,$as_codded,$as_codtipper,$ai_monto_acumulado)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_instructivo_0205_monto_acumulado
		//         Access: public (desde la clase sigesp_snorh_rpp_isntructivo_07_cargos)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	   			   as_codded // código de dedicación
		//	   			   as_codtipper // código de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la programación de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/06/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_anio=substr($_SESSION["la_empresa"]["periodo"],0,4);
		switch($as_rango)
		{
			case "01-01":
				$randes=$ls_anio."-01-01";
				$ranhas=$ls_anio."-01-31";
			break;
			case "02-02":
				$randes=$ls_anio."-02-01";
				$ranhas=$ls_anio."-02-28";
			break;
			case "03-03":
				$randes=$ls_anio."-03-01";
				$ranhas=$ls_anio."-03-31";
			break;
			case "04-04":
				$randes=$ls_anio."-04-01";
				$ranhas=$ls_anio."-04-30";
			break;
			case "05-05":
				$randes=$ls_anio."-05-01";
				$ranhas=$ls_anio."-05-31";
			break;
			case "06-06":
				$randes=$ls_anio."-06-01";
				$ranhas=$ls_anio."-06-30";
			break;
			case "07-07":
				$randes=$ls_anio."-07-01";
				$ranhas=$ls_anio."-07-31";
			break;
			case "08-08":
				$randes=$ls_anio."-08-01";
				$ranhas=$ls_anio."-08-31";
			break;
			case "09-09":
				$randes=$ls_anio."-09-01";
				$ranhas=$ls_anio."-09-30";
			break;
			case "10-10":
				$randes=$ls_anio."-10-01";
				$ranhas=$ls_anio."-10-31";
			break;
			case "11-11":
				$randes=$ls_anio."-11-01";
				$ranhas=$ls_anio."-11-30";
			break;
			case "12-12":
				$randes=$ls_anio."-12-01";
				$ranhas=$ls_anio."-12-31";
			break;
			case "01-03":
				$randes=$ls_anio."-01-01";
				$ranhas=$ls_anio."-03-31";
			break;
			case "02-04":
				$randes=$ls_anio."-02-01";
				$ranhas=$ls_anio."-04-30";
			break;
			case "03-05":
				$randes=$ls_anio."-03-01";
				$ranhas=$ls_anio."-05-31";
			break;
			case "04-06":
				$randes=$ls_anio."-04-01";
				$ranhas=$ls_anio."-06-30";
			break;
			case "05-07":
				$randes=$ls_anio."-05-01";
				$ranhas=$ls_anio."-07-31";
			break;
			case "06-08":
				$randes=$ls_anio."-06-01";
				$ranhas=$ls_anio."-08-31";
			break;
			case "07-09":
				$randes=$ls_anio."-07-01";
				$ranhas=$ls_anio."-09-30";
			break;
			case "08-10":
				$randes=$ls_anio."-08-01";
				$ranhas=$ls_anio."-10-31";
			break;
			case "09-11":
				$randes=$ls_anio."-09-01";
				$ranhas=$ls_anio."-11-30";
			break;
			case "10-12":
				$randes=$ls_anio."-10-01";
				$ranhas=$ls_anio."-12-31";
			break;
		}
		$ls_sql="SELECT SUM(sno_hsalida.valsal) as monto ".
				"  FROM sno_hpersonalnomina ".
				" INNER JOIN (sno_hperiodo ".
				" 		INNER JOIN sno_hnomina ".
				"         ON sno_hperiodo.fechasper  <= '".$ranhas."'".
				"        AND sno_hnomina.tipnom <> 7 ".
				"        AND sno_hnomina.espnom = '0' ".
				"        AND sno_hnomina.ctnom = '0' ".
				"        AND sno_hperiodo.codemp = sno_hnomina.codemp ".
				"        AND sno_hperiodo.codnom = sno_hnomina.codnom ".
				"	     AND sno_hperiodo.anocur = sno_hnomina.anocurnom ".
				"        AND sno_hperiodo.codperi = sno_hnomina.peractnom) ".
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
				"   AND sno_hpersonalnomina.codded='".$as_codded."'".
				"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
				"   AND (sno_hpersonalnomina.staper='1' OR sno_hpersonalnomina.staper='2')".
				"   AND sno_hpersonalnomina.codemp = sno_hperiodo.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hperiodo.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hperiodo.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hperiodo.codperi ".
				" INNER JOIN sno_hsalida ".
				"    ON sno_hpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_hpersonalnomina.anocur = '".$ls_anio."'".
				"   AND sno_hpersonalnomina.codded='".$as_codded."'".
				"   AND sno_hpersonalnomina.codtipper='".$as_codtipper."'".
				"   AND (sno_hsalida.tipsal = 'A' OR sno_hsalida.tipsal = 'V1' OR sno_hsalida.tipsal = 'W1')".
				"   AND sno_hsalida.valsal <> 0 ".
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"	AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_instructivo_0205_monto_acumulado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ai_monto_acumulado=$rs_data->fields["monto"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ai_monto_acumulado']=$ai_monto_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end uf_instructivo_0205_monto_acumulado
	//------------------------------------------------------------------------------------------------------------------------------------

	function uf_obtener_anio_trabajado_anterior($as_codper)
	{	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_ultimo_sueldo_integral
		//		   Access: private
		//	    Arguments: as_codper // código de personal
		//				   as_periodo // Periodo del que se quiere calcular el sueldo
		//				   as_anopre // Ano previo
		//				   ai_sueldo // Sueldo Anterior
		//	      Returns: lb_valido True si se obtuvo el concepto ó False si no se obtuvo
		//	  Description: función que dado el código de personal y busca el sueldo Anterior de acuerdo al periodo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 02/04/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_anio=0;
		$ai_mes=0;
		$ai_dia=0;
		$ls_sql="SELECT anolab, meslab, dialab ".
				"  FROM sno_trabajoanterior ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codper='".$as_codper."' ".
				"   AND emppubtraant='1' ".
				" ORDER BY codtraant ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_obtener_anio_trabajado_anterior ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ai_anio=$rs_data->fields["anolab"]+$ai_anio;
				$ai_mes=$rs_data->fields["meslab"]+$ai_mes;
				$ai_dia=$rs_data->fields["dialab"]+$ai_dia;
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);	
			$ai_dias=floor($ai_dia/30);
			if($ai_dias>0)
			{
				$ai_mes=$ai_mes+$ai_dias;
				$ai_dia = $ai_dia -($ai_dias*30);
			}
			$ai_meses=floor($ai_mes/12);
			if($ai_meses>0)
			{
				$ai_anio=$ai_anio+$ai_meses;
				$ai_mes = $ai_mes -($ai_meses*12);
			}
		}
		$arrResultado['ai_anio']=$ai_anio;
		$arrResultado['ai_mes']=$ai_mes;
		$arrResultado['ai_dia']=$ai_dia;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_ultimo_sueldo_integral_promedio
        
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_seleccionar_personal_auditoria($as_codperdes,$as_codperhas,$as_discapacitado,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_seleccionar_personal_auditoria
		//         Access: public (desde la clase sigesp_snorh_rpp_ipasme_beneficiario)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 20/07/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$lb_ok=false;
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_personal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_discapacitado))
		{
			$ls_criterio= $ls_criterio." AND sno_personal.tienedis='".$as_discapacitado."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por codigo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.nacper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.sexper, ".
		        "       sno_personal.fecnacper, sno_tipopersonalsss.dentippersss AS codtippersss, sno_personal.fecingper, sno_personal.telmovper, sno_personal.telhabper, ".
				"       sno_personal.dirper, sno_personal.coreleper, sno_personal.coreleins, sno_personal.tienedis, sno_personal.desdis, sno_personal.nrocardis, ".
				"       sno_personal.contraorg, sno_personal.talzapper, sno_personal.talcamper, sno_personal.talpanper, sno_personal.obsper, sno_personal.carantper     ".
				"  FROM sno_personal, sno_tipopersonalsss ".
				" WHERE sno_personal.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_tipopersonalsss.codemp ".
				"   AND sno_personal.codtippersss = sno_tipopersonalsss.codtippersss ".
				$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_personal_auditoria ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_seleccionar_personal_auditoria
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_seleccionar_personal_auditoria_hijo($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_seleccionar_personal_auditoria_hijo
		//         Access: public (desde la clase sigesp_snorh_rpp_vacaciones)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los familiares del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio1 = "AND sno_familiar.nexfam='H' ";
		$ld_hoy=date('Y')."-".date('m')."-".date('d');
		$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -18 year"));
		$ls_criterio2= " AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
		$li_nrohijos=0;
		$li_nrohijosescolar=0;
		
		$ls_sql="SELECT COUNT(cedfam) AS nrohijos, 0 nrohijosescolar ".
				"  FROM sno_familiar ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."'".
				"   ".$ls_criterio1." ".
				" UNION ".
		        "SELECT 0 AS nrohijos, COUNT(cedfam) nrohijosescolar ".
				"  FROM sno_familiar ".
				" WHERE codemp = '".$this->ls_codemp."'".
				"	AND codper = '".$as_codper."'".
				"   ".$ls_criterio1." ".
				"   ".$ls_criterio2." ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_personal_auditoria_hijo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				if ($rs_data->fields["nrohijos"]<>0)
				{
					$li_nrohijos=$rs_data->fields["nrohijos"];
				}
				if ($rs_data->fields["nrohijosescolar"]<>0)
				{
					$li_nrohijosescolar=$rs_data->fields["nrohijosescolar"];
				}
				$rs_data->MoveNext();	
			}
			$this->io_sql->free_result($rs_data);
		}	
		$arrResultado['nrohijos']=$li_nrohijos;
		$arrResultado['nrohijosescolar']=$li_nrohijosescolar;
		
		return $arrResultado;
	}// end function uf_seleccionar_personal_auditoria_hijo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_seleccionar_personal_auditoria_enfermedad($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_seleccionar_personal_auditoria_enfermedad
		//         Access: public (desde la clase sigesp_snorh_rpp_vacaciones)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los familiares del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 22/09/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_enfermedad=" ";
		
		$ls_sql="SELECT desenf ".
				"  FROM sno_personalenfermedad, sno_enfermedad ".
				" WHERE sno_personalenfermedad.codemp = '".$this->ls_codemp."'".
				"	AND sno_personalenfermedad.codper = '".$as_codper."'".
				"   AND sno_enfermedad.enfcro='1' ".
		        "   AND sno_personalenfermedad.codemp =  sno_enfermedad.codemp ".
				"   AND sno_personalenfermedad.codenf =  sno_enfermedad.codenf ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_seleccionar_personal_auditoria_enfermedad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_enfermedad .= $rs_data->fields["desenf"]." ";
				$rs_data->MoveNext();	
			}
			$this->io_sql->free_result($rs_data);
		}	
		$arrResultado['enfermedad']=$ls_enfermedad;
		
		return $arrResultado;
	}// end function uf_seleccionar_personal_auditoria_enfermedad
	//-----------------------------------------------------------------------------------------------------------------------------------

        
	//-----------------------------------------------------------------------------------------------------------------------------------
	//FUNCIONES AGREGADAS POR EDAGR QUINTERO
	//-----------------------------------------------------------------------------------------------------------------------------------
   function desc_sexo($codsex){
	
			switch($codsex)
			{
				case "M":
					return "Masculino";
					break;
				case "F":
					return "Femenino";
					break;
			}
			
			return 'Ninguno';	
	}
	
	
	function desc_nexo($codnexo){
	
			switch($codnexo)
			{
				case "C":
					return "Conyuge";
					break;
				case "H":
					return "Hijo";
					break;
				case "P":
					return "Progenitor";
					break;
				case "E":
					return "Hermano";
					break;
			}
			
			return 'Ninguno';
	
	}
	
	function generar_cadena_in($arreglo=array()){
		
		$i=0;
		$cadena = "(";
		foreach($arreglo as $elemento){
			if($elemento){		    
				if($i==0){$cadena .= "'".$elemento."'";}
				if($i>0){$cadena .= ",'".$elemento."'";}
				$i++;	
			}	
		}
		$cadena .= ") ";
		return $cadena;	
	}
	
	function consulta_familiares($prop=array())
	{
		
		$ls_criterio="";
		
		$estatus[0] = $prop['activono'];
		$estatus[1] = $prop['vacacionesno'];
		$estatus[2] = $prop['egresadono'];
		$estatus[3] = $prop['suspendidono'];		
		$estatusper[0] = $prop['activo'];
		$estatusper[1] = $prop['egresado'];		
		$nexo[0] = $prop['conyuge'];
		$nexo[1] = $prop['progenitor'];
		$nexo[2] = $prop['hijo'];
		$nexo[3] = $prop['hermano'];		
		$sexof[0] = $prop['masculino'];
		$sexof[1] = $prop['femenino'];		
		$sexop[0] = $prop['personalmasculino'];
		$sexop[1] = $prop['personalfemenino'];
		
		if($prop['codnomhas']){$ls_criterio= $ls_criterio." AND pn.codnom<='".$prop['codnomhas']."'";}		
		if($prop['codnomdes']){$ls_criterio= $ls_criterio." AND pn.codnom>='".$prop['codnomdes']."'";}
		if($prop['activono'] or $prop['vacacionesno'] or $prop['egresadono'] or $prop['suspendidono'])
		{$ls_criterio= $ls_criterio." AND pn.staper IN ".$this->generar_cadena_in($estatus)." ";}
		if($prop['codperdes']){$ls_criterio= $ls_criterio." AND p.codper>='".$prop['codperdes']."'";}
		if($prop['codperhas']){$ls_criterio= $ls_criterio." AND p.codper<='".$prop['codperhas']."'";}
		if($prop['activo'] or $prop['egresado']){$ls_criterio= $ls_criterio." AND p.estper IN ".$this->generar_cadena_in($estatusper)." ";}
		if($prop['conyugue'] or $prop['progenitor'] or $prop['hijo'] or $prop['hermano'])
		{$ls_criterio= $ls_criterio." AND f.nexfam IN ".$this->generar_cadena_in($nexo)." ";}
		if($prop['masculino'] or $prop['femenino']){$ls_criterio= $ls_criterio." AND f.sexfam IN ".$this->generar_cadena_in($sexof)." ";}
		if($prop['personalmasculino'] or $prop['personalfemenino']){$ls_criterio= $ls_criterio." AND p.sexper IN ".$this->generar_cadena_in($sexop)." ";}
		
		$ai_edadhasta = $prop['edadhasta'];	
		$ai_edaddesde = $prop['edaddesde'];			
		if(!empty($ai_edadhasta))
		{
			if($ai_edaddesde==$ai_edadhasta)
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$li_resta=$ai_edadhasta+1;
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$li_resta year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edaddesde year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam<'".$ld_fecha."' ";
			}
			else
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edadhasta year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam>='".$ld_fecha."' ";
			}
		}
		if(!empty($ai_edaddesde))
		{
			if($ai_edaddesde!=$ai_edadhasta)
			{
				$ld_hoy=date('Y')."-".date('m')."-".date('d');
				$ld_fecha=date("Y-m-d", strtotime("$ld_hoy -$ai_edaddesde year"));
				$ls_criterio= $ls_criterio." AND sno_familiar.fecnacfam<='".$ld_fecha."' ";
			}
		}
		switch($prop['orden'])
		{
			case "": // Ordena por Código de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.codper ";
				break;
			
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.codper ";
				break;

			case "2": // Ordena por Cédula de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.cedper ";
				break;

			case "3": // Ordena por Apellido de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.apeper ";
				break;

			case "4": // Ordena por Nombre de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.nomper ";
				break;
		}
		
		$ls_sql="SELECT ua.desuniadm,pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,
					    p.codper,p.apeper, p.nomper, p.fecnacper, f.cedfam, f.nomfam, f.apefam, 
					    f.sexfam, f.fecnacfam, f.nexfam, 
					    f.estfam, f.cedula 
		           FROM sno_personal p
				   INNER JOIN sno_familiar f ON p.codemp = f.codemp AND p.codper = f.codper
				   LEFT JOIN sno_personalnomina pn ON p.codemp = pn.codemp AND p.codper = pn.codper
				   LEFT JOIN sno_unidadadmin ua ON pn.codemp = ua.codemp AND 					   
													pn.minorguniadm = ua.minorguniadm AND 
													pn.ofiuniadm = ua.ofiuniadm AND 
													pn.uniuniadm = ua.uniuniadm AND 
													pn.depuniadm = ua.depuniadm AND  
													pn.prouniadm = ua.prouniadm 
				   WHERE p.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".			
				" GROUP BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,ua.desuniadm,
						   p.codper, p.apeper, p.nomper, p.fecnacper,f.cedfam, f.nomfam, f.apefam, f.sexfam, 
						   f.fecnacfam, f.nexfam, f.estfam, f.cedula ".
				"   ".$ls_orden;
		
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->familiar_personal ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $rs_data;
	}// end function uf_familiar_personal
   
   
   function consulta_oficinas($prop=array())
	{
		
		
		$ls_sql=" SELECT * 
				  FROM sno_unidadadmin 
				  WHERE  codemp = '".$this->ls_codemp."'
				    AND  minorguniadm = '0000'
				    AND  ofiuniadm = '".$prop['ofiuniadm']."'
					AND  uniuniadm = '00'
					AND	 depuniadm = '00'
					AND	 prouniadm = '00'			
				  ".$ls_orden;
		
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->consulta_oficinas ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $rs_data;
	}// end function uf_familiar_personal



	function consulta_cumpleanos($prop=array())
	{
		
		$ls_criterio="";
		
		$estatus[0] = $prop['activono'];
		$estatus[1] = $prop['vacacionesno'];
		$estatus[2] = $prop['egresadono'];
		$estatus[3] = $prop['suspendidono'];		
		$estatusper[0] = $prop['activo'];
		$estatusper[1] = $prop['egresado'];	
				
		if($prop['codnomhas']){$ls_criterio= $ls_criterio." AND pn.codnom<='".$prop['codnomhas']."'";}		
		if($prop['codnomdes']){$ls_criterio= $ls_criterio." AND pn.codnom>='".$prop['codnomdes']."'";}
		if($prop['activono'] or $prop['vacacionesno'] or $prop['egresadono'] or $prop['suspendidono'])
		{$ls_criterio= $ls_criterio." AND pn.staper IN ".$this->generar_cadena_in($estatus)." ";}
		if($prop['codperdes']){$ls_criterio= $ls_criterio." AND p.codper>='".$prop['codperdes']."'";}
		if($prop['codperhas']){$ls_criterio= $ls_criterio." AND p.codper<='".$prop['codperhas']."'";}
		if($prop['activo'] or $prop['egresado']){$ls_criterio= $ls_criterio." AND p.estper IN ".$this->generar_cadena_in($estatusper)." ";}
		if($prop['mes']){$ls_criterio= $ls_criterio." AND substr(cast(p.fecnacper as char(10)),6,2)='".$prop['mes']."'";}	
		if($prop['nominasnormales']){$ls_criterio= $ls_criterio." AND n.espnom='0'";}
		
		switch($prop['orden'])
		{
			case "": // Ordena por Código de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.codper ";
				break;
			
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.codper ";
				break;

			case "2": // Ordena por Cédula de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.cedper ";
				break;

			case "3": // Ordena por Apellido de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.apeper ";
				break;

			case "4": // Ordena por Nombre de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.nomper ";
				break;
		}
		
		$ls_sql="SELECT ua.desuniadm,pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,
					    p.codper,p.cedper,p.apeper, p.nomper, p.fecnacper,p.sexper
		           FROM sno_personal p				  
				   LEFT JOIN sno_personalnomina pn ON p.codemp = pn.codemp AND p.codper = pn.codper
				   LEFT JOIN sno_unidadadmin ua ON pn.codemp = ua.codemp AND 					   
													pn.minorguniadm = ua.minorguniadm AND 
													pn.ofiuniadm = ua.ofiuniadm AND 
													pn.uniuniadm = ua.uniuniadm AND 
													pn.depuniadm = ua.depuniadm AND  
													pn.prouniadm = ua.prouniadm 
					LEFT JOIN sno_nomina n ON pn.codemp = n.codemp AND pn.codnom = n.codnom
				   WHERE p.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".			
				" GROUP BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,ua.desuniadm,
						   p.codper, p.cedper,p.apeper, p.nomper, p.fecnacper,p.sexper ".
				"   ".$ls_orden;
		
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->consulta_cumpleanos ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $rs_data;
	}// end function uf_familiar_personal
	//-----------------------------------------------------------------------------------------------------------------------------------


	function consulta_personal_uniadm($prop=array())
	{
		
		$ls_criterio="";
		
		$estatus[0] = $prop['activono'];
		$estatus[1] = $prop['vacacionesno'];
		$estatus[2] = $prop['egresadono'];
		$estatus[3] = $prop['suspendidono'];		
		$estatusper[0] = $prop['activo'];
		$estatusper[1] = $prop['egresado'];	
		
		$prop['coduniadmhas'] = explode('-',$prop['coduniadmhas']);
		$prop['coduniadmdes'] = explode('-',$prop['coduniadmdes']);		
		$prop['minorguniadmhas'] = $prop['coduniadmhas'][0];
		$prop['minorguniadmdes'] = $prop['coduniadmdes'][0];
		$prop['ofiuniadmdes'] = $prop['coduniadmdes'][1];
		$prop['ofiuniadmhas'] = $prop['coduniadmhas'][1];		
			
		if($prop['codnomhas']){$ls_criterio= $ls_criterio." AND pn.codnom<='".$prop['codnomhas']."' ";}		
		if($prop['codnomdes']){$ls_criterio= $ls_criterio." AND pn.codnom>='".$prop['codnomdes']."' ";}
		if(count((Array)$prop['coduniadmdes'])>1){$ls_criterio= $ls_criterio." AND pn.minorguniadm>='".$prop['minorguniadmdes']."' AND pn.minorguniadm<='".$prop['minorguniadmhas']."' ";}
		if(count((Array)$prop['coduniadmdes'])>1){$ls_criterio= $ls_criterio." AND pn.ofiuniadm>='".$prop['ofiuniadmdes']."' AND pn.ofiuniadm<='".$prop['ofiuniadmhas']."' ";}		
		if($prop['activono'] or $prop['vacacionesno'] or $prop['egresadono'] or $prop['suspendidono'])
		{$ls_criterio= $ls_criterio." AND pn.staper IN ".$this->generar_cadena_in($estatus)." ";}
		if($prop['codperdes']){$ls_criterio= $ls_criterio." AND p.codper>='".$prop['codperdes']."' ";}
		if($prop['codperhas']){$ls_criterio= $ls_criterio." AND p.codper<='".$prop['codperhas']."' ";}
		if($prop['activo'] or $prop['egresado']){$ls_criterio= $ls_criterio." AND p.estper IN ".$this->generar_cadena_in($estatusper)." ";}
		if($prop['causaegreso']){$ls_criterio= $ls_criterio." AND p.cauegrper='".$prop['causaegreso']."' ";}
			
		switch($prop['orden'])
		{
			case "": // Ordena por Código de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.codper ";
				break;
			
			case "1": // Ordena por Código de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.codper ";
				break;

			case "2": // Ordena por Códula de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.cedper ";
				break;

			case "3": // Ordena por Apellido de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.apeper ";
				break;

			case "4": // Ordena por Nombre de personal
				$ls_orden="ORDER BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,p.nomper ";
				break;
		}
		
		
		
		
		$ls_sql="SELECT ua.desuniadm,pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,
			        p.codper,p.cedper,p.apeper, p.nomper, p.fecnacper, uf.desubifis,p.sexper,tp.destipper,
                    p.fecingper,tps.dentippersss,ca.descar,p.carantper,ac.codasicar,ac.denasicar,
					p.nivacaper, pro.despro, pn.staper,p.estper
		           FROM sno_personal p				  
				   INNER JOIN sno_personalnomina pn ON p.codemp = pn.codemp AND p.codper = pn.codper
				   INNER JOIN sno_cargo ca ON ca.codemp = pn.codemp AND ca.codcar = pn.codcar AND  ca.codnom = pn.codnom
                                   INNER JOIN sno_asignacioncargo ac ON ac.codemp = pn.codemp AND ac.codasicar = pn.codasicar AND  ac.codnom = pn.codnom
				   INNER JOIN sno_ubicacionfisica uf ON uf.codemp = pn.codemp AND uf.codubifis = pn.codubifis
				   INNER JOIN sno_tipopersonal tp ON tp.codemp = pn.codemp AND tp.codded = pn.codded AND pn.codtipper = tp.codtipper
				   INNER JOIN sno_tipopersonalsss tps ON tps.codemp = p.codemp AND tps.codtippersss = p.codtippersss
				   LEFT JOIN sno_profesion pro ON pro.codemp = p.codemp AND pro.codpro = p.codpro				   
				   INNER JOIN sno_unidadadmin ua ON pn.codemp = ua.codemp AND 					   
													pn.minorguniadm = ua.minorguniadm AND 
													pn.ofiuniadm = ua.ofiuniadm AND 
													pn.uniuniadm = ua.uniuniadm AND 
													pn.depuniadm = ua.depuniadm AND  
													pn.prouniadm = ua.prouniadm 
				   INNER JOIN sno_nomina n ON pn.codemp = n.codemp AND pn.codnom = n.codnom
				   WHERE p.codemp = '".$this->ls_codemp."'".
				"   ".$ls_criterio." ".			
				" GROUP BY pn.minorguniadm,pn.ofiuniadm,pn.uniuniadm,pn.depuniadm,pn.prouniadm,ua.desuniadm,
					       p.codper, p.cedper,p.apeper, p.nomper, p.fecnacper, uf.desubifis,p.sexper,tp.destipper,p.fecingper,
                           tps.dentippersss,ca.descar,p.carantper,ac.codasicar,ac.denasicar,p.nivacaper, pro.despro, pn.staper,p.estper ".
				"   ".$ls_orden;
		
		$rs_data=$this->io_sql->select($ls_sql);
		//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MóTODO->consulta_cumpleanos ERROR->".$this->io_sql->message);
			$lb_valido=false;
		}
		
		return $rs_data;
	}// end function uf_familiar_personal

	function desc_estatusper($cod){
	
			switch($cod)
			{
				case "0":
					return "Pre-Ing";
					break;
				case "1":
					return "Act";
					break;
				case "2":
					return "N/A";
					break;
				case "3":
					return "Egre";
					break;
			}
			
			return 'Ninguno';
	
	}
	function desc_estatusnom($cod){
	
			switch($cod)
			{
				case "0":
					return "N/A";
					break;
				case "1":
					return "Act";
					break;
				case "2":
					return "Vac";
					break;
				case "3":
					return "Egre";
					break;
				case "4":
					return "Suspo";
					break;
			}
			
			return 'Ninguno';
	
	}
	function desc_nivelacadem($cod){
	
			switch($cod)
			{
				case "0":
					return "Ninguno";
					break;
				case "1":
					return "Primaria";
					break;
				case "2":
					return "Bachiller";
					break;
				case "3":
					return "Tócnico Superior";
					break;
				case "4":
					return "Universitario";
					break;
				case "5":
					return "Maestria";
					break;
				case "6":
					return "PostGrado";
					break;
				case "7":
					return "Doctorado";
					break;
			}
			
			return 'Ninguno';
	
	}
        
	function uf_contableconceptos_presupuesto($as_nominas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prestacionantiguedad_afectacionpresupuestaria
		//         Access: public (desde la clase sigesp_snorh_rpp_contableprestacionantiguedad)  
		//	    Arguments: as_codnom // Código de Nómina
		//	  			   as_anocurper // Año en curso
		//	  			   as_mescurper // Mes en curso		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la afectación presupuestaria del fideicomiso del  personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/05/2008 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_nominas= str_replace("*","'",$as_nominas);
		$as_nominas= str_replace("-",",",$as_nominas);
		$ls_sql="SELECT sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
                        "	sno_dt_spg.estcla, sno_dt_spg.spg_cuenta, MAX(spg_cuentas.denominacion) AS denominacion, SUM(sno_dt_spg.monto) AS monto ".
                        "  FROM sno_dt_spg, spg_cuentas  ".  
                        " WHERE sno_dt_spg.codemp = '".$this->ls_codemp."' ".
                        "   AND sno_dt_spg.codnom||sno_dt_spg.codperi IN (".$as_nominas.") ".
                        "   AND sno_dt_spg.codemp = spg_cuentas.codemp ".
                        "   AND sno_dt_spg.codestpro1 = spg_cuentas.codestpro1 ".
                        "   AND sno_dt_spg.codestpro2 = spg_cuentas.codestpro2 ".
                        "   AND sno_dt_spg.codestpro3 = spg_cuentas.codestpro3 ".
                        "   AND sno_dt_spg.codestpro4 = spg_cuentas.codestpro4 ".
                        "   AND sno_dt_spg.codestpro5 = spg_cuentas.codestpro5 ".
                        "   AND sno_dt_spg.estcla = spg_cuentas.estcla ".
                        "   AND sno_dt_spg.spg_cuenta = spg_cuentas.spg_cuenta ".
                        " GROUP BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
                        "       sno_dt_spg.estcla, sno_dt_spg.spg_cuenta ";
                        " ORDER BY sno_dt_spg.codestpro1, sno_dt_spg.codestpro2, sno_dt_spg.codestpro3, sno_dt_spg.codestpro4, sno_dt_spg.codestpro5, ".
                        "       sno_dt_spg.estcla, sno_dt_spg.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_personal ($as_codnomdes,$as_codnomhas,$as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,$as_codubifis,
					 $as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden,$as_pagobanco,
					 $as_pagocheque)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_personal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // Código del personal donde se empieza a filtrar
		//	  			   as_codperhas // Código del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información del personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2006 						Fecha Última Modificación :		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codnomdes))
		{
			$ls_criterio= " AND sno_hpersonalnomina.codnom>='".$as_codnomdes."'";
			$ls_criteriounion=" AND sno_hpersonalnomina.codnom>='".$as_codnomdes."'";
		}
		if(!empty($as_codnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codnom<='".$as_codnomhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codperi>='".$as_codperdes."'";
			$ls_criteriounion=$ls_criteriounion." AND sno_hpersonalnomina.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codperi<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hpersonalnomina.codperi<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."    AND sno_hpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_hpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_hpersonalnomina.codubifis='".$as_codubifis."'";
		}
		else
		{
			if(!empty($as_codest))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codest='".$as_codest."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpai='".$as_codpai."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codest='".$as_codest."'";
			}
			if(!empty($as_codmun))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codmun='".$as_codmun."'";
			}
			if(!empty($as_codpar))
			{
				$ls_criterio= $ls_criterio." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
				$ls_criteriounion = $ls_criteriounion." AND sno_ubicacionfisica.codpar='".$as_codpar."'";
			}
		}
		if(($as_pagobanco==1)&&($as_pagocheque==0))
		{
			$ls_criterio= $ls_criterio."   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1)".
									   "   AND sno_hpersonalnomina.pagefeper = 0 ";
			$ls_criteriounion= $ls_criteriounion."   AND (sno_hpersonalnomina.pagbanper = 1 OR sno_hpersonalnomina.pagtaqper = 1)".
									  			 "   AND sno_hpersonalnomina.pagefeper = 0 ";
		}							 
		if(($as_pagobanco==0)&&($as_pagocheque==1))
		{
			$ls_criterio= $ls_criterio."   AND (sno_hpersonalnomina.pagbanper = 0 OR sno_hpersonalnomina.pagtaqper = 0)".
									   "   AND sno_hpersonalnomina.pagefeper = 1 ";
			$ls_criteriounion= $ls_criteriounion."   AND (sno_hpersonalnomina.pagbanper = 0 OR sno_hpersonalnomina.pagtaqper = 0)".
									  			 "   AND sno_hpersonalnomina.pagefeper = 1 ";
		}							 
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
											"	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
											"	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							   				"      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		if(empty($as_orden))
		{
			$ls_orden=" ORDER BY codper ";
		}
		else
		{
			switch($as_orden)
			{
				case "1": // Ordena por unidad administrativa
					$ls_orden=" ORDER BY minorguniadm, ofiuniadm, uniuniadm, ".
							  "    	     depuniadm, prouniadm, codper ";
					break;

				case "2": // Ordena por Código de personal
					$ls_orden=" ORDER BY codper ";
					break;

				case "3": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY apeper ";
					break;

				case "4": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY nomper ";
					break;
				
				case "5": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY desubifis ";
					break;
			}
		}
                $ls_descar="       (SELECT denasicar FROM sno_hasignacioncargo ".
                           "   	     WHERE sno_hpersonalnomina.codemp = sno_hasignacioncargo.codemp ".
                           "	       AND sno_hpersonalnomina.codnom = sno_hasignacioncargo.codnom ".
                           "	       AND sno_hpersonalnomina.anocur = sno_hasignacioncargo.anocur ".
                           "	       AND sno_hpersonalnomina.codperi = sno_hasignacioncargo.codperi ".
                           "           AND sno_hpersonalnomina.codasicar = sno_hasignacioncargo.codasicar) as desasicar, ".
			   "       (SELECT descar FROM sno_hcargo ".
			   "   	     WHERE sno_hpersonalnomina.codemp = sno_hcargo.codemp ".
			   "	       AND sno_hpersonalnomina.codnom = sno_hcargo.codnom ".
			   "	       AND sno_hpersonalnomina.anocur = sno_hcargo.anocur ".
			   "	       AND sno_hpersonalnomina.codperi = sno_hcargo.codperi ".
		           "           AND sno_hpersonalnomina.codcar = sno_hcargo.codcar) as descar ";
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
					  "		  sno_personal.fecingper, sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper as fecingnom,".
					  "       sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_personal.fecegrper, ".
					  "       sno_personal.fecsitu, sno_personal.fecnacper, MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
					  "		  sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, ".
					  "		  sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, MAX(sno_hpersonalnomina.sueper) AS sueper, ".
					  "		  sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "       sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, MAX(sno_hpersonalnomina.codpas) AS codpas, MAX(sno_hpersonalnomina.codgra) AS codgra, ".
					  "       MAX(sno_personal.nacper) AS nacper, MAX(sno_hpersonalnomina.obsrecper) As obsrecper, ".
					  "       MAX(sno_ubicacionfisica.desubifis) AS desubifis,sno_personal.sexper, MAX(sno_personal.fecingadmpubper) AS fecingadmpubper,".
					  "		  (SELECT dessubnom FROM sno_hsubnomina ".
					  "			WHERE sno_hsubnomina.codemp = sno_hpersonalnomina.codemp ".
					  "			  AND sno_hsubnomina.codnom = sno_hpersonalnomina.codnom ".
					  "			  AND sno_hsubnomina.anocur = sno_hpersonalnomina.anocur ".
					  "			  AND sno_hsubnomina.codperi = sno_hpersonalnomina.codperi ".
					  "			  AND sno_hsubnomina.codsubnom = sno_hpersonalnomina.codsubnom) AS dessubnom, ".
					  "		  (SELECT desded FROM sno_dedicacion ".
					  "			WHERE sno_dedicacion.codemp = sno_hpersonalnomina.codemp ".
					  "			  AND sno_dedicacion.codded = sno_hpersonalnomina.codded) AS desded, ".
					  "		  (SELECT destipper FROM sno_tipopersonal ".
					  "			WHERE sno_tipopersonal.codemp = sno_hpersonalnomina.codemp ".
					  "			  AND sno_tipopersonal.codded = sno_hpersonalnomina.codded ".
					  "			  AND sno_tipopersonal.codtipper = sno_hpersonalnomina.codtipper) AS destipper, ".
					  "		  (SELECT desest FROM sigesp_estados ".
					  "			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
					  "		  (SELECT denmun FROM sigesp_municipio ".
					  "			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
					  "		  (SELECT denpar FROM sigesp_parroquia ".
					  "			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
					  "			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
					  "		  (SELECT SUM(asires) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
					  "		  (SELECT SUM(dedres + apoempres) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
					  "		  (SELECT SUM(apopatres) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
					  "		  (SELECT SUM(priquires) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi ".
					  "			 AND sno_hresumen.codper = sno_hsalida.codper) AS priquires, ".
					  "		  (SELECT SUM(segquires) FROM sno_hresumen ".
					  "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
					  "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
					  "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
					  "			 AND sno_hresumen.codperi = sno_hsalida.codperi ".
					  "			 AND sno_hresumen.codper = sno_hsalida.codper) AS segquires, ".
					  "		 (SELECT sno_componente.descom FROM sno_componente ".
					  "        WHERE sno_componente.codemp='".$this->ls_codemp."'".
					  "          AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
					  "		 (SELECT sno_escaladocente.desescdoc FROM sno_escaladocente ".
					  "          WHERE sno_escaladocente.codemp='".$this->ls_codemp."'".
					  "			   AND sno_escaladocente.codemp=sno_hpersonalnomina.codemp	".
					  "            AND sno_escaladocente.codescdoc=sno_hpersonalnomina.codescdoc) AS desescdoc, ".
					  "		 (SELECT sno_clasificaciondocente.descladoc FROM sno_clasificaciondocente ".
					  "          WHERE sno_clasificaciondocente.codemp='".$this->ls_codemp."'".
					  "			   AND sno_clasificaciondocente.codemp=sno_hpersonalnomina.codemp	".
					  "            AND sno_clasificaciondocente.codcladoc=sno_hpersonalnomina.codcladoc ".
				 	  "			   AND sno_clasificaciondocente.codescdoc=sno_hpersonalnomina.codescdoc) AS descladoc, ".
					  "		 (SELECT sno_rango.desran FROM sno_rango ".
					  "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
					  "          AND sno_rango.codcom=sno_personal.codcom".
					  "          AND sno_rango.codran=sno_personal.codran) AS denran, MAX(sno_personal.situacion) AS situacion, ".
					  "        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				      "            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
				"        (SELECT max(sno_nomina.desnom) FROM sno_nomina WHERE sno_nomina.codemp='".$this->ls_codemp."'".
				"            AND sno_nomina.espnom = '0' ".
				"            AND sno_nomina.codemp=sno_hpersonalnomina.codemp ".
				"            AND sno_nomina.codnom=sno_hpersonalnomina.codnom) AS nomina, ".
  					  $ls_descar.
					  "  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
					  " WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "	  AND sno_hpersonalnomina.staper = '2' ".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
					  "   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
					  "   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
					  "   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
					  "   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_hpersonalnomina.codper ".
					  "   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
					  "   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
					  "   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
					  "   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
					  "   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
					  "   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
					  "   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
					  "   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
					  "   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".
					  "	  AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
					  " GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom, sno_hsalida.codperi, sno_hpersonalnomina.codper, ".
					  "		   sno_hsalida.codper,sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, sno_personal.sexper,".
					  "        sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper, ".
					  "		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.desuniadm, ".
					  "		   sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, ".
					  "		   sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
					  "		   sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
					  "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
					  "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar, ".
					  "        sno_personal.codcom,sno_personal.codran, sno_personal.cauegrper, sno_personal.codcausa, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, ".
					  "        sno_personal.fecegrper, sno_personal.fecsitu, sno_personal.fecnacper,sno_hpersonalnomina.codescdoc,sno_hpersonalnomina.codcladoc ";
		}
		$ls_sql="SELECT sno_hpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		sno_personal.fecingper, sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper as fecingnom, ".
				"       sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_personal.fecegrper, sno_personal.fecsitu, ".
				"       sno_personal.fecnacper, MAX(sno_hpersonalnomina.descasicar) AS descasicar, ".
				"		sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, ".
				"		sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, MAX(sno_hpersonalnomina.sueper) AS sueper, sno_hunidadadmin.minorguniadm, ".
				"		sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, ".
				"       MAX(sno_hpersonalnomina.codpas) AS codpas, MAX(sno_hpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_hpersonalnomina.obsrecper) As obsrecper, ".
				"       MAX(sno_ubicacionfisica.desubifis) AS desubifis, sno_personal.sexper,MAX(sno_personal.fecingadmpubper) AS fecingadmpubper,".
                                "		  (SELECT dessubnom FROM sno_hsubnomina ".
                                "			WHERE sno_hsubnomina.codemp = sno_hpersonalnomina.codemp ".
                                "			  AND sno_hsubnomina.codnom = sno_hpersonalnomina.codnom ".
                                "			  AND sno_hsubnomina.anocur = sno_hpersonalnomina.anocur ".
                                "			  AND sno_hsubnomina.codperi = sno_hpersonalnomina.codperi ".
                                "			  AND sno_hsubnomina.codsubnom = sno_hpersonalnomina.codsubnom) AS dessubnom, ".
				"		  (SELECT desded FROM sno_dedicacion ".
				"			WHERE sno_dedicacion.codemp = sno_hpersonalnomina.codemp ".
				"			  AND sno_dedicacion.codded = sno_hpersonalnomina.codded) AS desded, ".
				"		  (SELECT destipper FROM sno_tipopersonal ".
				"			WHERE sno_tipopersonal.codemp = sno_hpersonalnomina.codemp ".
				"			  AND sno_tipopersonal.codded = sno_hpersonalnomina.codded ".
				"			  AND sno_tipopersonal.codtipper = sno_hpersonalnomina.codtipper) AS destipper, ".
				"		  (SELECT desest FROM sigesp_estados ".
				"			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		  (SELECT denmun FROM sigesp_municipio ".
				"			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		  (SELECT denpar FROM sigesp_parroquia ".
				"			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
				"		  (SELECT SUM(asires) FROM sno_hresumen ".
				"			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
				"			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
				"			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
				"			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalasignacion, ".
				"		  (SELECT SUM(dedres + apoempres) FROM sno_hresumen ".
				"			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
				"			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
				"			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
				"			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totaldeduccion, ".
				"		  (SELECT SUM(apopatres) FROM sno_hresumen ".
				"			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
				"			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
				"			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
				"			 AND sno_hresumen.codperi = sno_hsalida.codperi) AS totalaporte, ".
			    "		  (SELECT SUM(priquires) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    "			 AND sno_hresumen.codperi = sno_hsalida.codperi ".
		    	    "			 AND sno_hresumen.codper = sno_hsalida.codper) AS priquires, ".
			    "		  (SELECT SUM(segquires) FROM sno_hresumen ".
			    "			WHERE sno_hresumen.codemp = sno_hsalida.codemp ".
			    "			 AND sno_hresumen.codnom = sno_hsalida.codnom ".
			    "			 AND sno_hresumen.anocur = sno_hsalida.anocur ".
			    "			 AND sno_hresumen.codperi = sno_hsalida.codperi ".
			    "			 AND sno_hresumen.codper = sno_hsalida.codper) AS segquires, ".
			    "		 (SELECT sno_componente.descom FROM sno_componente ".
				"          WHERE sno_componente.codemp='".$this->ls_codemp."'".
				"            AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
				"		 (SELECT sno_escaladocente.desescdoc FROM sno_escaladocente ".
				"          WHERE sno_escaladocente.codemp='".$this->ls_codemp."'".
				"			 AND sno_escaladocente.codemp=sno_hpersonalnomina.codemp	".
				"            AND sno_escaladocente.codescdoc=sno_hpersonalnomina.codescdoc) AS desescdoc, ".
				"		 (SELECT sno_clasificaciondocente.descladoc FROM sno_clasificaciondocente ".
				"          WHERE sno_clasificaciondocente.codemp='".$this->ls_codemp."'".
			    "			 AND sno_clasificaciondocente.codemp=sno_hpersonalnomina.codemp	".
				"            AND sno_clasificaciondocente.codcladoc=sno_hpersonalnomina.codcladoc ".
				"			 AND sno_clasificaciondocente.codescdoc=sno_hpersonalnomina.codescdoc) AS descladoc, ".
				"		 (SELECT sno_rango.desran FROM sno_rango ".
			    "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				"          AND sno_rango.codcom=sno_personal.codcom".
				"          AND sno_rango.codran=sno_personal.codran) AS denran, MAX(sno_personal.situacion) AS situacion, ".
				"        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				"            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
				"        (SELECT max(sno_nomina.desnom) FROM sno_nomina WHERE sno_nomina.codemp='".$this->ls_codemp."'".
				"            AND sno_nomina.espnom = '0' ".
				"            AND sno_nomina.codemp=sno_hpersonalnomina.codemp ".
				"            AND sno_nomina.codnom=sno_hpersonalnomina.codnom) AS nomina, ".
				$ls_descar.
				"  FROM sno_personal, sno_hpersonalnomina, sno_hsalida, sno_hunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_hpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio.
				"   AND sno_hpersonalnomina.codemp = sno_hsalida.codemp ".
				"   AND sno_hpersonalnomina.codnom = sno_hsalida.codnom ".
				"   AND sno_hpersonalnomina.anocur = sno_hsalida.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hsalida.codperi ".
				"   AND sno_hpersonalnomina.codper = sno_hsalida.codper ".
				"   AND sno_personal.codemp = sno_hpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_hpersonalnomina.codper ".
				"   AND sno_hpersonalnomina.codemp = sno_hunidadadmin.codemp ".
				"   AND sno_hpersonalnomina.anocur = sno_hunidadadmin.anocur ".
				"   AND sno_hpersonalnomina.codperi = sno_hunidadadmin.codperi ".
				"   AND sno_hpersonalnomina.minorguniadm = sno_hunidadadmin.minorguniadm ".
				"   AND sno_hpersonalnomina.ofiuniadm = sno_hunidadadmin.ofiuniadm ".
				"   AND sno_hpersonalnomina.uniuniadm = sno_hunidadadmin.uniuniadm ".
				"   AND sno_hpersonalnomina.depuniadm = sno_hunidadadmin.depuniadm ".
				"   AND sno_hpersonalnomina.prouniadm = sno_hunidadadmin.prouniadm ".
				"   AND sno_ubicacionfisica.codemp = sno_hpersonalnomina.codemp ".				
				"	AND sno_ubicacionfisica.codubifis = sno_hpersonalnomina.codubifis ".
				" GROUP BY sno_hpersonalnomina.codemp, sno_hsalida.codemp, sno_hpersonalnomina.codnom, sno_hsalida.codnom, sno_hsalida.anocur, sno_hpersonalnomina.anocur, sno_hpersonalnomina.codperi,sno_hsalida.codperi, sno_hpersonalnomina.codper, ".
				"		   sno_hsalida.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"          sno_hpersonalnomina.fecculcontr, sno_hpersonalnomina.fecingper, sno_personal.sexper,".
				"		   sno_hpersonalnomina.codcueban, sno_hunidadadmin.desuniadm, sno_hunidadadmin.desuniadm, ".
				"		   sno_hunidadadmin.codestpro1, sno_hunidadadmin.codestpro2, sno_hunidadadmin.codestpro3, ".
				"		  sno_hunidadadmin.codestpro4, sno_hunidadadmin.codestpro5, sno_hpersonalnomina.codcar, sno_hpersonalnomina.codasicar, ".
				"		   sno_hunidadadmin.minorguniadm, sno_hunidadadmin.ofiuniadm, sno_hunidadadmin.uniuniadm, ".
			    "    	   sno_hunidadadmin.depuniadm, sno_hunidadadmin.prouniadm, sno_ubicacionfisica.codpai,  ".
				"          sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar,".
				"          sno_personal.codcom,sno_personal.codran, sno_personal.codcausa, sno_hpersonalnomina.codded, sno_hpersonalnomina.codtipper, ".
				"          sno_personal.fecegrper, sno_personal.fecsitu, sno_personal.fecnacper,sno_hpersonalnomina.codescdoc,sno_hpersonalnomina.codcladoc ".
				"   ".$ls_union.
				"   ".$ls_orden;  
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagonomina_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_personal

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaconsolidado_conceptopersonal_excel($as_codper,$as_tituloconcepto,$as_tipsal,$as_codnomdes,$as_codperdes,$as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // Código del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el título del concepto ó el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->DS_detalle->reset_ds();
		$lb_valido=true;
		$ls_criterio="";
		$ls_criteriounion="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hsalida.codnom='".$as_codnomdes."'";
			$ls_criteriounion=$ls_criteriounion." AND sno_hsalida.codnom='".$as_codnomdes."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hsalida.codperi>='".$as_codperdes."'";
			$ls_criteriounion=$ls_criteriounion." AND sno_hsalida.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hsalida.codperi<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hsalida.codperi<='".$as_codperhas."'";
		}
                
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) as valsal, MAX(sno_hsalida.tipsal) AS tipsal ".
					  "  FROM sno_hsalida, sno_hconcepto, sno_hpersonalnomina ".
					  " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hsalida.codper='".$as_codper."'".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."'".
					  "   AND sno_hpersonalnomina.staper = '2' ".
                                          $ls_criteriounion.
					  "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					  "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					  "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					  "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					  "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					  "   AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
					  "   AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
					  "   AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
					  "   AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
					  "   AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
					  " GROUP BY sno_hconcepto.codconc ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) as valsal, MAX(sno_hsalida.tipsal) AS tipsal ".
				"  FROM sno_hconcepto, sno_hsalida ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$as_tipsal.
                                $ls_criteriounion.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				" GROUP BY sno_hconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc, tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagonomina_conceptopersonal_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------
        
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaconsolidado_concepto_excel($as_tituloconcepto,$as_sigcon,$as_codnomdes,$as_codperdes,$as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_concepto_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // Código del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el título del concepto ó el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="nomcon";
                $ls_criteriounion = "";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "titcon";
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hconcepto.codnom='".$as_codnomdes."'";
			$ls_criteriounion=$ls_criteriounion." AND sno_hsalida.codnom='".$as_codnomdes."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hconcepto.codperi>='".$as_codperdes."'";
			$ls_criteriounion= $ls_criteriounion." AND sno_hsalida.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codperi<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hsalida.codperi<='".$as_codperhas."'";
		}
		$ls_sql="SELECT codconc, ".$ls_campo." as nomcon, cueprecon, cueconcon, cueconpatcon, cueprepatcon ".
				"  FROM sno_hconcepto ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   ".$as_sigcon." ".
                                $ls_criterio.
				"   AND codconc IN (SELECT codconc FROM sno_hsalida WHERE codemp='".$this->ls_codemp."' ".$ls_criteriounion." )".
				" ORDER BY codconc "; 
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagonomina_concepto_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------
        
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_conceptopersonal($as_codper,$as_conceptocero,$as_tituloconcepto,$as_conceptoreporte,$as_conceptop2,$as_codnomdes,$as_codperdes,$as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // Código del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos en cero
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el título del concepto ó el nombre
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de los conceptos asociados al personal que se le calculó la nómina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 01/02/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
                $ls_criteriounion="";
		$ls_campo="sno_hconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_hconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_codnomdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hconcepto.codnom='".$as_codnomdes."'";
			$ls_criteriounion= $ls_criteriounion." AND sno_hsalida.codnom='".$as_codnomdes."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio." AND sno_hconcepto.codperi>='".$as_codperdes."'";
			$ls_criteriounion= $ls_criteriounion." AND sno_hsalida.codperi>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_hconcepto.codperi<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_hsalida.codperi<='".$as_codperhas."'";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
                                                            "	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							    "      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
							    "	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4' OR ".
							    "	   sno_hsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
							    "	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							    "      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
							    "	   sno_hsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
							    "	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							    "      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3' OR ".
							    "	   sno_hsalida.tipsal='P2' OR sno_hsalida.tipsal='V4' OR sno_hsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_hsalida.tipsal='A' OR sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1' OR ".
							    "	   sno_hsalida.tipsal='D' OR sno_hsalida.tipsal='V2' OR sno_hsalida.tipsal='W2' OR ".
							    "      sno_hsalida.tipsal='P1' OR sno_hsalida.tipsal='V3' OR sno_hsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
				  "SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) AS valsal, MAX(sno_hsalida.tipsal) AS tipsal, MAX(sno_hconcepto.frevarcon) AS frevarcon, MAX(sno_hconcepto.repconsunicon) AS repconsunicon, MAX(sno_hconcepto.consunicon) AS consunicon, ".
				  "		(0 ) AS unidad ".
					  "  FROM sno_hsalida, sno_hconcepto, sno_personalnomina ".
					  " WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_hsalida.codper='".$as_codper."'".
					  "   AND sno_hsalida.codconc='".$ls_vac_codconvac."' ".
                                           $ls_criteriounion.
					  "   AND sno_personalnomina.staper = '2' ".
					  "   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
					  "   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
					  "   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
					  "   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
					  "   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
					  "   AND sno_hsalida.codemp = sno_personalnomina.codemp ".
					  "   AND sno_hsalida.codnom = sno_personalnomina.codnom ".
					  "   AND sno_hsalida.anocur = sno_personalnomina.anocur ".
					  "   AND sno_hsalida.codperi = sno_personalnomina.codperi ".
					  "   AND sno_hsalida.codper = sno_personalnomina.codper ".
                                            " GROUP BY sno_hconcepto.codconc ";
		}
		$ls_sql="SELECT sno_hconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_hsalida.valsal) AS valsal, MAX(sno_hsalida.tipsal) AS tipsal, MAX(sno_hconcepto.frevarcon) AS frevarcon, MAX(sno_hconcepto.repconsunicon) AS repconsunicon, MAX(sno_hconcepto.consunicon) AS consunicon, ".
				"		(0) AS unidad ".
				"  FROM sno_hsalida, sno_hconcepto ".
				" WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_hsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc ".
				"   ".$ls_union.
                                " GROUP BY sno_hconcepto.codconc ".
				" ORDER BY codconc, tipsal  ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_pagonomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------
        
}
?>