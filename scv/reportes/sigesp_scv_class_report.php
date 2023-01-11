<?php
/***********************************************************************************
* @fecha de modificacion: 14/11/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scv_class_report
{
	var $obj="";
	var $io_sql;
	var $ds;
	var $ds_detalle;
	var $siginc;
	var $con;

	public function __construct()
	{
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_msg=new class_mensajes();
		$this->dat_emp=$_SESSION["la_empresa"];
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		//$this->con->debug=true;
		$this->io_sql=new class_sql($this->con);
		$this->io_funcion = new class_funciones();
		$this->ds=new class_datastore();
		$this->ds_detalle=new class_datastore();
		$this->ds_detpersonal=new class_datastore();
		$this->ds_detcontable=new class_datastore();
		$this->ds_detpresup=new class_datastore();
		$this->ds_solicitud=new class_datastore();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////         Funciones del formato de salida de la solicitud de viaticos          ///////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function uf_select_solicitudviaticos($as_codemp,$as_codsolvia,$ad_fecdes,$ad_fechas,$as_codsoldes,$as_codsolhas,
										 $as_coduniadm,$as_codper,$as_codestpro1,$as_codestpro2,$as_codestpro3,
										 $as_codestpro4,$as_codestpro5,$as_estcla,$ai_orden,$ls_tipvia="0")	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_solicitudviaticos
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // numero de la solicitud de viaticos
		//  			       $ad_fecdes     // fecha de inicio del periodo de busqueda
		//  			       $ad_fechas     // fecha de cierre del periodo de busqueda
		//  			       $as_codsoldes  // numero de la solicitud de viaticos Desde
		//  			       $as_codsolhas  // numero de la solicitud de viaticos Hasta
		//  			       $as_coduniadm  // codigo de unidad ejecutora
		//  			       $as_codper     // codigo de personal / beneficiario
		//        			   $as_codestpro1 // codigo de estructura programatica nivel 1
		//        			   $as_codestpro2 // codigo de estructura programatica nivel 2
		//        			   $as_codestpro3 // codigo de estructura programatica nivel 3
		//        			   $as_codestpro4 // codigo de estructura programatica nivel 4
		//        			   $as_codestpro5 // codigo de estructura programatica nivel 5
		//                     $as_estcla     // estatus de la estructura programatica
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de la busqueda de un maestro de solititud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 29/11/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		
		$ls_sql="SELECT scv_solicitudviatico.codsolvia, denuniadm, MAX(scv_solicitudviatico.codmis) AS codmis, MAX(scv_solicitudviatico.codrut) AS codrut, ".
		        "       MAX(scv_solicitudviatico.coduniadm) AS coduniadm, MAX(scv_solicitudviatico.fecsalvia) AS fecsalvia, MAX(scv_solicitudviatico.fecregvia) AS fecregvia, ".
				"       MAX(scv_solicitudviatico.fecsolvia) AS fecsolvia, MAX(scv_solicitudviatico.numdiavia) AS numdiavia, MAX(scv_solicitudviatico.solviaext) AS solviaext, ".
				"       MAX(scv_solicitudviatico.obssolvia) AS obssolvia, MAX(scv_solicitudviatico.estsolvia) AS estsolvia, MAX(scv_solicitudviatico.monsolvia) AS monsolvia,".
				"       (SELECT denmis".
				"          FROM scv_misiones".
				"         WHERE scv_solicitudviatico.codemp=scv_misiones.codemp".
				"           AND scv_solicitudviatico.codmis=scv_misiones.codmis) AS denmis, ".
				"       (SELECT desrut".
				"          FROM scv_rutas".
				"         WHERE scv_solicitudviatico.codemp=scv_rutas.codemp".
				"           AND scv_solicitudviatico.codrut=scv_rutas.codrut".
				"         GROUP BY codrut,desrut) AS desrut ".				
				"  FROM scv_solicitudviatico,scv_dt_personal,spg_unidadadministrativa,spg_dt_unidadadministrativa".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codemp=scv_dt_personal.codemp".
				"   AND scv_solicitudviatico.codsolvia=scv_dt_personal.codsolvia".
				"   AND (scv_solicitudviatico.estsolvia='P' OR scv_solicitudviatico.estsolvia='C') ". // Modificado por Ofimatica de venezuela 09-05-2011, ya que la impresion de las solicitudes de pago de viaticos deb hacerse asi no este aprobado el calculo del viatico. Esto con la finalidad de poder tener el fisico para su posterior aprobacion.
				"   AND scv_solicitudviatico.codemp=spg_unidadadministrativa.codemp".
				 "  AND scv_solicitudviatico.coduniadm=spg_unidadadministrativa.coduniadm ".
				 "  AND spg_dt_unidadadministrativa.codemp=scv_solicitudviatico.codemp ".
				 "  AND spg_dt_unidadadministrativa.estcla=scv_solicitudviatico.estcla ".
				 "  AND spg_dt_unidadadministrativa.codestpro1=scv_solicitudviatico.codestpro1 ".
				 "  AND spg_dt_unidadadministrativa.codestpro2=scv_solicitudviatico.codestpro2 ".
				 "  AND spg_dt_unidadadministrativa.codestpro3=scv_solicitudviatico.codestpro3 ".
				 "  AND spg_dt_unidadadministrativa.codestpro4=scv_solicitudviatico.codestpro4 ".
				 "  AND spg_dt_unidadadministrativa.codestpro5=scv_solicitudviatico.codestpro5";
		if(!empty($as_codsolvia))
		{
			$ls_sql=$ls_sql."   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'";
		}
		if((!empty($ad_fecdes))&&(!empty($ad_fechas)))
		{
			$ad_fecdes=$this->io_funcion->uf_convertirdatetobd($ad_fecdes);
			$ad_fechas=$this->io_funcion->uf_convertirdatetobd($ad_fechas);
			$ls_sql=$ls_sql." AND scv_solicitudviatico.fecsolvia >= '".$ad_fecdes."'".
							" AND scv_solicitudviatico.fecsolvia <='".$ad_fechas."'";
		}
		if((!empty($as_codsoldes))&&(!empty($as_codsolhas)))
		{
			$ls_sql=$ls_sql."   AND scv_solicitudviatico.codsolvia>='".$as_codsoldes."' ".
					  		"   AND scv_solicitudviatico.codsolvia<='".$as_codsolhas."' ";
		}
		if(!empty($as_coduniadm))
		{
			$ls_sql=$ls_sql." AND scv_solicitudviatico.coduniadm='".$as_coduniadm."'";
			$ls_sql=$ls_sql." AND scv_solicitudviatico.codestpro1='".$as_codestpro1."'";
			$ls_sql=$ls_sql." AND scv_solicitudviatico.codestpro2='".$as_codestpro2."'";
			$ls_sql=$ls_sql." AND scv_solicitudviatico.codestpro3='".$as_codestpro3."'";
			$ls_sql=$ls_sql." AND scv_solicitudviatico.codestpro4='".$as_codestpro4."'";
			$ls_sql=$ls_sql." AND scv_solicitudviatico.codestpro5='".$as_codestpro5."'";
			$ls_sql=$ls_sql." AND scv_solicitudviatico.estcla='".$as_estcla."'";
		}
		if(!empty($as_codper))
		{
			$ls_sql=$ls_sql." AND scv_dt_personal.codper='".str_pad(trim($as_codper),10,'0',0)."'";
		}
		if($ls_tipvia!="0")
		{
			$ls_sql=$ls_sql." AND scv_solicitudviatico.tipvia='".$ls_tipvia."'";
		}
		
		$ls_sql=$ls_sql." GROUP BY scv_solicitudviatico.codsolvia, scv_solicitudviatico.codemp, ".
		                "           scv_solicitudviatico.codmis, scv_solicitudviatico.codrut, ".
                        "           scv_solicitudviatico.coduniadm, denuniadm,scv_solicitudviatico.fecsolvia";
		if(!empty($ai_orden))
		{
			if($ai_orden==1)
				$ls_sql=$ls_sql." ORDER BY scv_solicitudviatico.fecsolvia DESC";
			else
				$ls_sql=$ls_sql." ORDER BY scv_solicitudviatico.fecsolvia ";
				
		}
		else
		{
			$ls_sql=$ls_sql." ORDER BY scv_solicitudviatico.codsolvia ";
		}	
		$rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_solicitudviaticos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds->data=$data;
				$lb_valido=true;
			}
			
		}
		return $lb_valido; 
	} //fin  function uf_select_solicitudviaticos

	function uf_select_dt_asignaciones($as_codemp,$as_codsolvia,$ad_fecdes,$ad_fechas,$ai_orden)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_dt_asignaciones
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//  			       $ad_fecdes     // fecha de inicio del periodo de busqueda
		//  			       $ad_fechas     // fecha de cierre del periodo de busqueda
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las asignaciones de una solicitud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT scv_dt_asignaciones.codemp, scv_dt_asignaciones.codsolvia, scv_dt_asignaciones.codasi,".
				 "       scv_dt_asignaciones.proasi, scv_dt_asignaciones.canasi, ".
				 "       (CASE scv_dt_asignaciones.proasi".
				 "        WHEN 'TVS' THEN (SELECT scv_tarifas.dentar".
				 "                           FROM scv_tarifas".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_tarifas.codemp".
				 " 							  AND scv_dt_asignaciones.codasi=scv_tarifas.codtar)".
				 "        WHEN 'TRP' THEN (SELECT scv_transportes.dentra".
				 "                           FROM scv_transportes".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_transportes.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_transportes.codtra)".
				 "        WHEN 'TOA' THEN (SELECT scv_otrasasignaciones.denotrasi".
				 "                           FROM scv_otrasasignaciones".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_otrasasignaciones.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_otrasasignaciones.codotrasi)".
				 "		  ELSE (SELECT scv_tarifakms.dentar".
				 "                FROM scv_tarifakms".
				 "               WHERE scv_dt_asignaciones.codemp=scv_tarifakms.codemp".
				 "                 AND scv_dt_asignaciones.codasi=scv_tarifakms.codtar) END) AS denasi".
				 "  FROM scv_solicitudviatico,scv_dt_asignaciones".
				 " WHERE scv_solicitudviatico.codemp='".$as_codemp."'".
				 "   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."'".
				 "   AND scv_solicitudviatico.codsolvia=scv_dt_asignaciones.codsolvia";
	
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_dt_asignaciones ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds_detalle->data=$data;
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	} //fin  function uf_select_dt_asignaciones

	function uf_select_dt_personal($as_codemp,$as_codsolvia,$ad_fecdes,$ad_fechas,$ai_orden,$lb_existe)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_dt_personal
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//  			       $ad_fecdes     // fecha de inicio del periodo de busqueda
		//  			       $ad_fechas     // fecha de cierre del periodo de busqueda
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las asignaciones de una solicitud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$lb_existe=$this->uf_scv_select_categoria_personal($as_codemp,$as_codsolvia);
		if($lb_existe)
		{
			$ls_sql="SELECT scv_dt_personal.codclavia,sno_personalnomina.codper,".
					"		(SELECT descar FROM sno_cargo ".
					"		   WHERE sno_cargo.codemp = sno_personalnomina.codemp ".
					"			 AND sno_cargo.codnom = sno_personalnomina.codnom ".
					"			 AND sno_cargo.codcar = sno_personalnomina.codcar) as descar, ".
					"		(SELECT denasicar FROM sno_asignacioncargo ".
					"		   WHERE sno_asignacioncargo.codemp = sno_personalnomina.codemp ".
					"			 AND sno_asignacioncargo.codnom = sno_personalnomina.codnom ".
					"			 AND sno_asignacioncargo.codasicar = sno_personalnomina.codasicar) as denasicar, ".
					"		(SELECT nomper FROM sno_personal".
					"  		  WHERE sno_personal.codper=sno_personalnomina.codper) as nomper,".
					"		(SELECT apeper FROM sno_personal".
					"   	  WHERE sno_personal.codper=sno_personalnomina.codper) as apeper,".
					"		(SELECT cedper FROM sno_personal".
					"		  WHERE sno_personal.codper=sno_personalnomina.codper) as cedper".
					"  FROM sno_personalnomina, sno_nomina, sno_cargo, sno_asignacioncargo,sno_personal,scv_dt_personal".
					" WHERE scv_dt_personal.codemp='".$as_codemp."'".
					"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
					"   AND scv_dt_personal.codemp=sno_personal.codemp".
					"   AND scv_dt_personal.codper=sno_personal.codper".
					"   AND sno_nomina.espnom='0'".
					"   AND sno_personalnomina.codnom = scv_dt_personal.codnom".
					"   AND sno_personalnomina.codemp = sno_nomina.codemp".
					"   AND sno_personalnomina.codnom = sno_nomina.codnom".
					"   AND sno_personalnomina.codper = sno_personal.codper".
					"   AND sno_personalnomina.codemp = sno_cargo.codemp".
					"   AND sno_personalnomina.codnom = sno_cargo.codnom".
					"   AND sno_personalnomina.codcar = sno_cargo.codcar".
					"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
					"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
					"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
					" GROUP BY sno_personalnomina.codper, sno_nomina.racnom, ".
					"          sno_asignacioncargo.denasicar,sno_cargo.descar, ".
                    "          scv_dt_personal.codclavia,sno_personalnomina.codper,sno_personalnomina.codemp,sno_personalnomina.codnom,sno_personalnomina.codcar,sno_personalnomina.codasicar ".
					" ORDER BY sno_personalnomina.codper,codclavia";
					
		}
		else
		{
			$ls_sql="SELECT scv_dt_personal.codper,rpc_beneficiario.ced_bene,".
					"       (SELECT nombene ".
					"          FROM rpc_beneficiario".
					"         WHERE scv_dt_personal.codemp=rpc_beneficiario.codemp".
					"           AND scv_dt_personal.codper=rpc_beneficiario.ced_bene) AS nombene,".
					"       (SELECT apebene ".
					"          FROM rpc_beneficiario".
					"         WHERE scv_dt_personal.codemp=rpc_beneficiario.codemp".
					"           AND scv_dt_personal.codper=rpc_beneficiario.ced_bene) AS apebene".
					"  FROM scv_dt_personal,rpc_beneficiario".
					" WHERE scv_dt_personal.codemp='".$as_codemp."'".
					"   AND scv_dt_personal.codsolvia='".$as_codsolvia."'".
					"   AND scv_dt_personal.codper=rpc_beneficiario.ced_bene";
		}
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{print $this->io_sql->message;
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_dt_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds_detpersonal->data=$data;
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['lb_valido']=$lb_valido;
		$arrResultado['lb_existe']=$lb_existe;
		return $arrResultado;		
	} //fin  function uf_select_dt_personal

	function uf_select_dt_spg($as_codemp,$as_codsolvia)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_dt_spg
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las asignaciones de una solicitud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT codemp,codsolvia, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, monto, spg_cuenta".
				 "  FROM scv_dt_spg ".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codsolvia='".$as_codsolvia."'";
				 
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_dt_spg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds_detpresup->data=$data;
				$this->ds_detpresup->group_by(array('0'=>'codemp','1'=>'codsolvia','2'=>'spg_cuenta'),array('0'=>'monto'),'monto');	
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	} //fin  function uf_select_dt_spg

	function uf_select_dt_scg($as_codemp,$as_codsolvia)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_dt_scg
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las asignaciones de una solicitud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT codemp, codsolvia, sc_cuenta, debhab, monto".
				 "  FROM scv_dt_scg".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codsolvia='".$as_codsolvia."'";
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_dt_scg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds_detcontable->data=$data;
				$this->ds_detcontable->group_by(array('0'=>'codemp','1'=>'codsolvia','2'=>'sc_cuenta','3'=>'debhab'),array('0'=>'monto'),'monto');	
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	} //fin  function uf_select_dt_scg

	function uf_scv_select_categoria_personal($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_categoria_personal
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica la existencia de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codclavia".
		        "  FROM scv_dt_personal".
				" WHERE codemp='". $as_codemp ."'".
				"   AND codsolvia='". $as_codsolvia ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_categoria_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codclavia=$row["codclavia"];
				if($ls_codclavia!="")
				{$lb_valido=true;}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}  // end function uf_scv_select_categoria_personal

	function uf_select_solicitudpago_personal($as_codemp,$as_codsoldes,$as_codsolhas,$ad_fecdes,$ad_fechas,$ai_orden,$as_codsolvia,$rs_data)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_solicitudpago_personal
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsoldes  // numero de la solicitud de viaticos Desde
		//  			       $as_codsolhas  // numero de la solicitud de viaticos Hasta
		//  			       $ad_fecdes     // fecha de inicio del periodo de busqueda
		//  			       $ad_fechas     // fecha de cierre del periodo de busqueda
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//  			       $as_codsolvia  // codigo de solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de la busqueda del personal asociado a una solicitud de viatico
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 29/11/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_criterio="";
		if((!empty($as_codsoldes))&&(!empty($as_codsolhas)))
		{
			$ls_criterio=$ls_criterio."   AND scv_solicitudviatico.codsolvia>='".$as_codsoldes."' ".
					  				  "   AND scv_solicitudviatico.codsolvia<='".$as_codsolhas."' ";
		}
		if((!empty($ad_fecdes))&&(!empty($ad_fechas)))
		{
			$ad_fecdes=$this->io_funcion->uf_convertirdatetobd($ad_fecdes);
			$ad_fechas=$this->io_funcion->uf_convertirdatetobd($ad_fechas);
			$ls_criterio=$ls_criterio." AND scv_solicitudviatico.fecsolvia >= '".$ad_fecdes."'".
									  " AND scv_solicitudviatico.fecsolvia <='".$ad_fechas."'";
		}
		if(!empty($as_codsolvia))
		{
			$ls_criterio=$ls_criterio."   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."' ";
		}
		$ls_sql="SELECT DISTINCT(sno_personal.cedper) as cedper, sno_personal.nomper, sno_personal.apeper, sno_unidadadmin.desuniadm, ".
				"		sno_personal.telhabper,sno_personal.telmovper,sno_personalnomina.sueper,".
				"       sno_personalnomina.codcueban, sno_personalnomina.tipcuebanper, sno_dedicacion.desded, ".
				"       sno_tipopersonal.destipper, scv_dt_personal.codclavia, scv_solicitudviatico.fecsolvia,scv_solicitudviatico.fecsalvia, ".
				"       scv_solicitudviatico.fecregvia, scv_solicitudviatico.fecsolvia, scv_solicitudviatico.numdiavia, scv_misiones.denmis, ".
				"		scv_solicitudviatico.codsolvia,scv_solicitudviatico.solviaext,scv_solicitudviatico.obssolvia,scv_solicitudviatico.tascam1, sno_nomina.racnom,scv_solicitudviatico.coduniadm,scv_solicitudviatico.tipvia, ".
				"		scv_solicitudviatico.tipodoc,scv_solicitudviatico.monsolvia,scv_solicitudviatico.codestpro1,scv_solicitudviatico.codestpro2,".
				"       scv_solicitudviatico.codestpro3,scv_solicitudviatico.codestpro4,scv_solicitudviatico.codestpro5,". 
				"       scv_solicitudviatico.mondolsol,scv_solicitudviatico.tascamsol,scv_solicitudviatico.numaut,scv_solicitudviatico.fecaut,scv_solicitudviatico.codmon,scv_solicitudviatico.tascam1,scv_solicitudviatico.estopediv,". 
			    "       (SELECT MAX(denmon) FROM sigesp_moneda ".
				"		  WHERE sigesp_moneda.codemp = scv_solicitudviatico.codemp  ".
				"			AND sigesp_moneda.codmon = scv_solicitudviatico.codmon ) AS denmon,  ".
				"       (SELECT COUNT(codper) FROM scv_dt_personal ".
				"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
				"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS acompanante,  ".
			    "       (SELECT MAX(codnom) FROM scv_dt_personal ".
				"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
				"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS mppre,  ".
				"       (SELECT denmis FROM scv_misiones ".
				"		  WHERE scv_solicitudviatico.codemp = scv_misiones.codemp  ".
				"			AND scv_solicitudviatico.codmisdes = scv_misiones.codmis ) AS mision_d,  ".
				"       (CASE sno_nomina.racnom ".
			    "        WHEN '1' THEN (SELECT denasicar FROM sno_asignacioncargo ".
			    "   	                 WHERE sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
			    "		                   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
			    "           			   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar)".
				"        WHEN '0' THEN (SELECT descar FROM sno_cargo ".
				"   	                 WHERE sno_personalnomina.codemp = sno_cargo.codemp ".
				"		                   AND sno_personalnomina.codnom = sno_cargo.codnom ".
				"                          AND sno_personalnomina.codcar = sno_cargo.codcar) END) AS cargo,".
				"       (CASE sno_nomina.racnom ".
			    "        WHEN '1' THEN (SELECT codasicar FROM sno_asignacioncargo ".
			    "   	                 WHERE sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
			    "		                   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
			    "           			   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar)".
				"        WHEN '0' THEN (SELECT codcar FROM sno_cargo ".
				"   	                 WHERE sno_personalnomina.codemp = sno_cargo.codemp ".
				"		                   AND sno_personalnomina.codnom = sno_cargo.codnom ".
				"                          AND sno_personalnomina.codcar = sno_cargo.codcar) END) AS codcar,".
				"  (SELECT denuniadm FROM spg_unidadadministrativa".
				"    WHERE scv_solicitudviatico.codemp = spg_unidadadministrativa.codemp".
				"      AND scv_solicitudviatico.coduniadm = spg_unidadadministrativa.coduniadm) AS denuniadm".
				"  FROM scv_dt_personal, sno_personal, sno_personalnomina, sno_nomina, sno_unidadadmin, sno_dedicacion, ".
				"       sno_tipopersonal, scv_solicitudviatico, scv_misiones ".
				" WHERE scv_solicitudviatico.codemp='".$as_codemp."' ".
				$ls_criterio.
				"   AND sno_nomina.espnom = '0' ".
				"   AND scv_dt_personal.codemp = scv_solicitudviatico.codemp ".
				"   AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ".
				"   AND scv_dt_personal.codemp = sno_personalnomina.codemp ".
				"   AND scv_dt_personal.codnom = sno_personalnomina.codnom ".
				"   AND scv_solicitudviatico.codemp = scv_misiones.codemp ".
				"   AND scv_solicitudviatico.codmis = scv_misiones.codmis ".
				"   AND scv_dt_personal.codemp = sno_personal.codemp ".
				"   AND scv_dt_personal.codper = sno_personal.codper ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"   AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom ".
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
				"   ORDER BY scv_solicitudviatico.codsolvia";//print $ls_sql;
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows==0)
		{
			$this->io_sql->free_result($rs_data);
			$ls_sql="SELECT rpc_beneficiario.ced_bene AS cedper, MAX(rpc_beneficiario.nombene) AS nomper, ".
			        "       MAX(rpc_beneficiario.apebene) AS apeper, '' AS desuniadm, MAX(rpc_beneficiario.telbene) AS telhabper,".
                    "       '' AS telmovper,'' AS sueper, '' AS codcueban, '' AS tipcuebanper, '' AS desded, '' AS destipper, ".
                    "       '' AS codclavia, MAX(scv_solicitudviatico.fecsalvia) AS fecsalvia,MAX(scv_solicitudviatico.tipvia) as tipvia, ".
					"       MAX(scv_solicitudviatico.fecsolvia) AS fecsolvia, MAX(scv_solicitudviatico.fecregvia) AS fecregvia, ".
                    "       MAX(scv_solicitudviatico.numdiavia) AS numdiavia, MAX(scv_misiones.denmis) AS denmis, ".
                    "       MAX(scv_misiones.denmis) AS denmis, MAX(scv_solicitudviatico.codsolvia) AS codsolvia, ".
					"       MAX(scv_solicitudviatico.solviaext) AS solviaext, ".
                    "       MAX(scv_solicitudviatico.obssolvia) AS obssolvia, '' AS racnom,  ".
					"		MAX(scv_solicitudviatico.tipodoc) AS tipodoc,".
					"		MAX(scv_solicitudviatico.tipodoc) AS numaut,".
					"		MAX(scv_solicitudviatico.tascam1) AS tascam1,".
			        "		MAX(scv_solicitudviatico.tipodoc) AS fecaut,scv_solicitudviatico.codmon,scv_solicitudviatico.tascam1,scv_solicitudviatico.estopediv,".
					"       (SELECT MAX(denmon) FROM sigesp_moneda ".
					"		  WHERE sigesp_moneda.codemp = scv_solicitudviatico.codemp  ".
					"			AND sigesp_moneda.codmon = scv_solicitudviatico.codmon ) AS denmon,  ".
					"       (SELECT COUNT(codper) FROM scv_dt_personal ".
					"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
					"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS acompanante,  ".
					"       '' AS cargo,".
					"       (SELECT codnom FROM scv_dt_personal ".
					"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
					"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia LIMIT 1) AS mppre,  ".
					"       (SELECT denmis FROM scv_misiones ".
					"		  WHERE scv_solicitudviatico.codemp = scv_misiones.codemp  ".
					"			AND scv_solicitudviatico.codmisdes = scv_misiones.codmis ) AS mision_d,  ".
					"  (SELECT denuniadm FROM spg_unidadadministrativa".
					"    WHERE scv_solicitudviatico.codemp = spg_unidadadministrativa.codemp".
					"      AND scv_solicitudviatico.coduniadm = spg_unidadadministrativa.coduniadm) AS denuniadm".
					"  FROM scv_dt_personal,  scv_solicitudviatico, scv_misiones, rpc_beneficiario ".
					" WHERE scv_solicitudviatico.codemp='".$as_codemp."' ".
					$ls_criterio.
					"   AND scv_dt_personal.codemp = scv_solicitudviatico.codemp ".
					"   AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ".
					"   AND scv_solicitudviatico.codemp = scv_misiones.codemp ".
					"   AND scv_solicitudviatico.codmis = scv_misiones.codmis ".
					"   AND scv_dt_personal.codemp = rpc_beneficiario.codemp ".
					"   AND scv_dt_personal.codper = rpc_beneficiario.ced_bene ".
					" GROUP BY rpc_beneficiario.ced_bene, scv_solicitudviatico.codemp, scv_solicitudviatico.codsolvia, scv_solicitudviatico.coduniadm, scv_solicitudviatico.codmisdes   ".
					"   ORDER BY scv_solicitudviatico.codsolvia";
			$rs_data=$this->io_sql->select($ls_sql);
			$li_numrows=$this->io_sql->num_rows($rs_data);	
		}
//print $ls_sql."<br>";
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_solicitudpago_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				
				$lb_valido=true;
			}
			
		}
		$arrResultado['lb_valido']=$lb_valido;
		$arrResultado['rs_data']=$rs_data;
		return $arrResultado;		
	} //fin  function uf_select_pagosolicitud_personal

	function uf_select_solicitudpago_personal_baer($as_codemp,$as_codsoldes,$as_codsolhas,$ad_fecdes,$ad_fechas,$ai_orden,$as_codsolvia,$rs_data)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_solicitudpago_personal
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsoldes  // numero de la solicitud de viaticos Desde
		//  			       $as_codsolhas  // numero de la solicitud de viaticos Hasta
		//  			       $ad_fecdes     // fecha de inicio del periodo de busqueda
		//  			       $ad_fechas     // fecha de cierre del periodo de busqueda
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//  			       $as_codsolvia  // codigo de solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de la busqueda del personal asociado a una solicitud de viatico
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 29/11/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_criterio="";
		if((!empty($as_codsoldes))&&(!empty($as_codsolhas)))
		{
			$ls_criterio=$ls_criterio."   AND scv_solicitudviatico.codsolvia>='".$as_codsoldes."' ".
					  				  "   AND scv_solicitudviatico.codsolvia<='".$as_codsolhas."' ";
		}
		if((!empty($ad_fecdes))&&(!empty($ad_fechas)))
		{
			$ad_fecdes=$this->io_funcion->uf_convertirdatetobd($ad_fecdes);
			$ad_fechas=$this->io_funcion->uf_convertirdatetobd($ad_fechas);
			$ls_criterio=$ls_criterio." AND scv_solicitudviatico.fecsolvia >= '".$ad_fecdes."'".
									  " AND scv_solicitudviatico.fecsolvia <='".$ad_fechas."'";
		}
		if(!empty($as_codsolvia))
		{
			$ls_criterio=$ls_criterio."   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."' ";
		}
		$ls_sql="SELECT DISTINCT(sno_personal.cedper) as cedper, sno_personal.nomper, sno_personal.apeper, sno_unidadadmin.desuniadm, ".
				"		sno_personal.telhabper,sno_personal.telmovper,sno_personalnomina.sueper,".
				"       sno_personalnomina.codcueban, sno_personalnomina.tipcuebanper, sno_dedicacion.desded, ".
				"       sno_tipopersonal.destipper, scv_dt_personal.codclavia, scv_solicitudviatico.fecsolvia,scv_solicitudviatico.fecsalvia, ".
				"       scv_solicitudviatico.fecregvia, scv_solicitudviatico.fecsolvia, scv_solicitudviatico.numdiavia, scv_misiones.denmis, ".
				"		scv_solicitudviatico.codsolvia,scv_solicitudviatico.solviaext,scv_solicitudviatico.obssolvia, sno_nomina.racnom,scv_solicitudviatico.coduniadm,scv_solicitudviatico.tipvia, ".
				"		scv_solicitudviatico.tipodoc,scv_solicitudviatico.monsolvia,scv_solicitudviatico.codestpro1,scv_solicitudviatico.codestpro2,".
				"       scv_solicitudviatico.codestpro3,scv_solicitudviatico.codestpro4,scv_solicitudviatico.codestpro5,". 
				"       scv_solicitudviatico.mondolsol,scv_solicitudviatico.tascamsol,scv_solicitudviatico.numaut,scv_solicitudviatico.fecaut,". 
				"       (SELECT COUNT(codper) FROM scv_dt_personal ".
				"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
				"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS acompanante,  ".
				"       (CASE sno_nomina.racnom ".
			    "        WHEN '1' THEN (SELECT denasicar FROM sno_asignacioncargo ".
			    "   	                 WHERE sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
			    "		                   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
			    "           			   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar)".
				"        WHEN '0' THEN (SELECT descar FROM sno_cargo ".
				"   	                 WHERE sno_personalnomina.codemp = sno_cargo.codemp ".
				"		                   AND sno_personalnomina.codnom = sno_cargo.codnom ".
				"                          AND sno_personalnomina.codcar = sno_cargo.codcar) END) AS cargo,".
				"       (CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.codasicar ELSE sno_cargo.codcar END) AS codcar,".
				"  (SELECT denuniadm FROM spg_unidadadministrativa".
				"    WHERE scv_solicitudviatico.codemp = spg_unidadadministrativa.codemp".
				"      AND scv_solicitudviatico.coduniadm = spg_unidadadministrativa.coduniadm) AS denuniadm".
				"  FROM scv_dt_personal, sno_personal, sno_personalnomina, sno_nomina, sno_unidadadmin, sno_dedicacion, ".
				"       sno_tipopersonal, scv_solicitudviatico, scv_misiones,sno_asignacioncargo,sno_cargo ".
				" WHERE scv_solicitudviatico.codemp='".$as_codemp."' ".
				$ls_criterio.
				"   AND sno_nomina.espnom = '0' ".
				"   AND sno_personalnomina.staper = '1' ".
				"   AND scv_dt_personal.codemp = scv_solicitudviatico.codemp ".
				"   AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ".
				"   AND scv_dt_personal.codemp = sno_personalnomina.codemp ".
				"   AND scv_dt_personal.codnom = sno_personalnomina.codnom ".
				"   AND scv_solicitudviatico.codemp = scv_misiones.codemp ".
				"   AND scv_solicitudviatico.codmis = scv_misiones.codmis ".
				"   AND scv_dt_personal.codemp = sno_personal.codemp ".
				"   AND scv_dt_personal.codper = sno_personal.codper ".
				"   AND sno_personal.codemp = sno_personalnomina.codemp ".
				"   AND sno_personal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"   AND sno_personalnomina.codnom = sno_nomina.codnom ".
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
				"   AND sno_personalnomina.codemp = sno_asignacioncargo.codemp".
				"   AND sno_personalnomina.codnom = sno_asignacioncargo.codnom".
				"   AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar".
				"   AND sno_personalnomina.codemp = sno_cargo.codemp".
				"   AND sno_personalnomina.codnom = sno_cargo.codnom".
				"   AND sno_personalnomina.codcar = sno_cargo.codcar".
				"   ORDER BY scv_solicitudviatico.codsolvia";//print $ls_sql;
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($li_numrows==0)
		{
			$this->io_sql->free_result($rs_data);
			$ls_sql="SELECT rpc_beneficiario.ced_bene AS cedper, MAX(rpc_beneficiario.nombene) AS nomper, ".
			        "       MAX(rpc_beneficiario.apebene) AS apeper, '' AS desuniadm, MAX(rpc_beneficiario.telbene) AS telhabper,".
                    "       '' AS telmovper,'' AS sueper, '' AS codcueban, '' AS tipcuebanper, '' AS desded, '' AS destipper, ".
                    "       '' AS codclavia, MAX(scv_solicitudviatico.fecsalvia) AS fecsalvia, ".
					"       MAX(scv_solicitudviatico.fecsolvia) AS fecsolvia, MAX(scv_solicitudviatico.fecregvia) AS fecregvia, ".
                    "       MAX(scv_solicitudviatico.numdiavia) AS numdiavia, MAX(scv_misiones.denmis) AS denmis, ".
                    "       MAX(scv_misiones.denmis) AS denmis, MAX(scv_solicitudviatico.codsolvia) AS codsolvia, ".
					"       MAX(scv_solicitudviatico.solviaext) AS solviaext, ".
                    "       MAX(scv_solicitudviatico.obssolvia) AS obssolvia, '' AS racnom,  ".
					"		MAX(scv_solicitudviatico.tipodoc) AS tipodoc,".
					"		MAX(scv_solicitudviatico.tipodoc) AS numaut,".
			        "		MAX(scv_solicitudviatico.tipodoc) AS fecaut,".
					"       (SELECT COUNT(codper) FROM scv_dt_personal ".
					"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
					"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS acompanante,  ".
					"       '' AS cargo,".
					"       (SELECT codnom FROM scv_dt_personal ".
					"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
					"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS mppre,  ".
					"       (SELECT denmis FROM scv_misiones ".
					"		  WHERE scv_solicitudviatico.codemp = scv_misiones.codemp  ".
					"			AND scv_solicitudviatico.codmisdes = scv_misiones.codmis ) AS mision_d,  ".
					"  (SELECT denuniadm FROM spg_unidadadministrativa".
					"    WHERE scv_solicitudviatico.codemp = spg_unidadadministrativa.codemp".
					"      AND scv_solicitudviatico.coduniadm = spg_unidadadministrativa.coduniadm) AS denuniadm".
					"  FROM scv_dt_personal,  scv_solicitudviatico, scv_misiones, rpc_beneficiario ".
					" WHERE scv_solicitudviatico.codemp='".$as_codemp."' ".
					$ls_criterio.
					"   AND scv_dt_personal.codemp = scv_solicitudviatico.codemp ".
					"   AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ".
					"   AND scv_solicitudviatico.codemp = scv_misiones.codemp ".
					"   AND scv_solicitudviatico.codmis = scv_misiones.codmis ".
					"   AND scv_dt_personal.codemp = rpc_beneficiario.codemp ".
					"   AND scv_dt_personal.codper = rpc_beneficiario.ced_bene ".
					" GROUP BY rpc_beneficiario.ced_bene, scv_solicitudviatico.codemp, scv_solicitudviatico.codsolvia, scv_solicitudviatico.coduniadm".
					"   ORDER BY scv_solicitudviatico.codsolvia";
			$rs_data=$this->io_sql->select($ls_sql);
			$li_numrows=$this->io_sql->num_rows($rs_data);	
		}
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_solicitudpago_personal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				
				$lb_valido=true;
			}
			
		}
		$arrResultado['lb_valido']=$lb_valido;
		$arrResultado['rs_data']=$rs_data;
		return $arrResultado;		
	} //fin  function uf_select_pagosolicitud_personal

	function uf_select_solicitudpago_beneficiario($as_codemp,$as_codsoldes,$as_codsolhas,$ad_fecdes,$ad_fechas,$ai_orden,$as_codsolvia)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_solicitudpago_beneficiario
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsoldes  // numero de la solicitud de viaticos Desde
		//  			       $as_codsolhas  // numero de la solicitud de viaticos Hasta
		//  			       $ad_fecdes     // fecha de inicio del periodo de busqueda
		//  			       $ad_fechas     // fecha de cierre del periodo de busqueda
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//  			       $as_codsolvia  // codigo de solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de la busqueda del personal asociado a una solicitud de viatico
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 29/11/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_criterio="";
		if((!empty($as_codsoldes))&&(!empty($as_codsolhas)))
		{
			$ls_criterio=$ls_criterio."   AND scv_solicitudviatico.codsolvia>='".$as_codsoldes."' ".
					  				  "   AND scv_solicitudviatico.codsolvia<='".$as_codsolhas."' ";
		}
		if((!empty($ad_fecdes))&&(!empty($ad_fechas)))
		{
			$ad_fecdes=$this->io_funcion->uf_convertirdatetobd($ad_fecdes);
			$ad_fechas=$this->io_funcion->uf_convertirdatetobd($ad_fechas);
			$ls_criterio=$ls_criterio." AND scv_solicitudviatico.fecsolvia >= '".$ad_fecdes."'".
									  " AND scv_solicitudviatico.fecsolvia <='".$ad_fechas."'";
		}
		if(!empty($as_codsolvia))
		{
			$ls_criterio=$ls_criterio."   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."' ";
		}
		$ls_sql="SELECT rpc_beneficiario.ced_bene AS cedper, MAX(rpc_beneficiario.nombene) AS nomper, MAX(rpc_beneficiario.apebene) AS apeper, '' AS desuniadm, ".
				"		MAX(rpc_beneficiario.telbene) AS telhabper,'' AS telmovper,'' AS sueper,".
				"       '' AS codcueban, '' AS tipcuebanper, '' AS desded, ".
				"       '' AS destipper, '' AS codclavia, MAX(scv_solicitudviatico.fecsalvia) AS fecsalvia, ".
				"       MAX(scv_solicitudviatico.fecregvia) AS fecregvia, MAX(scv_solicitudviatico.fecsolvia) AS fecsolvia, MAX(scv_solicitudviatico.numdiavia) AS numdiavia, ".
				"		MAX(scv_misiones.denmis) AS denmis, ".
				"		MAX(scv_solicitudviatico.codsolvia) AS codsolvia, MAX(scv_solicitudviatico.solviaext) AS solviaext, MAX(scv_solicitudviatico.obssolvia) AS obssolvia, ".
				"		'' AS racnom, MAX(scv_solicitudviatico.tipvia) AS tipvia,".
				"       (SELECT COUNT(codper) FROM scv_dt_personal ".
				"		  WHERE scv_dt_personal.codemp = scv_solicitudviatico.codemp  ".
				"			AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ) AS acompanante,  ".
			    "       '' AS cargo".
				"  FROM scv_dt_personal,  scv_solicitudviatico, scv_misiones, rpc_beneficiario ".
				" WHERE scv_solicitudviatico.codemp='".$as_codemp."' ".
				$ls_criterio.
				"   AND scv_dt_personal.codemp = scv_solicitudviatico.codemp ".
				"   AND scv_dt_personal.codsolvia = scv_solicitudviatico.codsolvia ".
				"   AND scv_solicitudviatico.codemp = scv_misiones.codemp ".
				"   AND scv_solicitudviatico.codmis = scv_misiones.codmis ".
				"   AND scv_dt_personal.codemp = rpc_beneficiario.codemp ".
				"   AND scv_dt_personal.codper = rpc_beneficiario.ced_bene ".
				" GROUP BY rpc_beneficiario.ced_bene, scv_solicitudviatico.codemp, scv_solicitudviatico.codsolvia  ";
		
	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_solicitudpago_beneficiario ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds->data=$data;
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	} //fin  function uf_select_solicitudpago_beneficiario

	function uf_select_solicitudpago_asignaciones($as_codemp,$as_codsolvia)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_solicitudpago_asignaciones
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//  			       $ai_orden      // parametro por el cual vamos a ordenar los resultados
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las asignaciones de una solicitud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT scv_dt_asignaciones.codemp, scv_dt_asignaciones.codsolvia, scv_dt_asignaciones.codasi,".
				 "       scv_dt_asignaciones.proasi, scv_dt_asignaciones.canasi, ".
				 "       (CASE scv_dt_asignaciones.proasi ".
				 "        WHEN 'TVS' THEN (SELECT scv_tarifas.dentar".
				 "                           FROM scv_tarifas".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_tarifas.codemp".
				 " 							  AND scv_dt_asignaciones.codasi=scv_tarifas.codtar)".
				 "        WHEN 'TRP' THEN (SELECT scv_transportes.dentra".
				 "                           FROM scv_transportes".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_transportes.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_transportes.codtra)".
				 "        WHEN 'TOA' THEN (SELECT scv_otrasasignaciones.denotrasi".
				 "                           FROM scv_otrasasignaciones".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_otrasasignaciones.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_otrasasignaciones.codotrasi)".
				 "		  ELSE (SELECT scv_tarifakms.dentar".
				 "                FROM scv_tarifakms".
				 "               WHERE scv_dt_asignaciones.codemp=scv_tarifakms.codemp".
				 "                 AND scv_dt_asignaciones.codasi=scv_tarifakms.codtar) END) AS denasi, ".
				 "       (CASE scv_dt_asignaciones.proasi ".
				 "        WHEN 'TVS' THEN (SELECT (scv_tarifas.monbol+scv_tarifas.monpas+scv_tarifas.monhos+scv_tarifas.monali+scv_tarifas.monmov)".
				 "                           FROM scv_tarifas".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_tarifas.codemp".
				 " 							  AND scv_dt_asignaciones.codasi=scv_tarifas.codtar)".
				 "        WHEN 'TRP' THEN (SELECT scv_transportes.tartra".
				 "                           FROM scv_transportes".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_transportes.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_transportes.codtra)".
				 "        WHEN 'TOA' THEN (SELECT scv_otrasasignaciones.tarotrasi".
				 "                           FROM scv_otrasasignaciones".
				 "                          WHERE scv_dt_asignaciones.codemp=scv_otrasasignaciones.codemp".
				 "                            AND scv_dt_asignaciones.codasi=scv_otrasasignaciones.codotrasi)".
				 "		  ELSE (SELECT scv_tarifakms.montar".
				 "                FROM scv_tarifakms".
				 "               WHERE scv_dt_asignaciones.codemp=scv_tarifakms.codemp".
				 "                 AND scv_dt_asignaciones.codasi=scv_tarifakms.codtar) END) AS monto, 
						 (CASE scv_dt_asignaciones.proasi 
							WHEN 'TRP' THEN (SELECT scv_transportes.codtiptra 
											FROM 	scv_transportes 
											WHERE 	scv_dt_asignaciones.codemp=scv_transportes.codemp AND 
												scv_dt_asignaciones.codasi=scv_transportes.codtra) 	 
							ELSE (	'---' ) 
							END) AS codtiptra, 
						 (CASE scv_dt_asignaciones.proasi 
							WHEN 'TOA' THEN (SELECT scv_otrasasignaciones.codmon 
											FROM 	scv_otrasasignaciones 
											WHERE 	scv_dt_asignaciones.codemp=scv_otrasasignaciones.codemp AND 
												scv_dt_asignaciones.codasi=scv_otrasasignaciones.codotrasi) 	 
							ELSE (	'001' ) 
							END) AS codmon						".
				 "  FROM scv_solicitudviatico,scv_dt_asignaciones".
				 " WHERE scv_solicitudviatico.codemp='".$as_codemp."'".
				 "   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."'".
				 "   AND scv_solicitudviatico.codsolvia=scv_dt_asignaciones.codsolvia";
	    $rs_data=$this->io_sql->select($ls_sql); //print $ls_sql;
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_dt_asignaciones ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds_detalle->data=$data;
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	} //fin  function uf_select_solicitudpago_asignaciones

	function uf_select_solicitudpago_spg($as_codemp,$as_codsolvia)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_solicitudpago_spg
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar las asignaciones de una solicitud de viaticos
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql= "SELECT codemp,codsolvia,codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, spg_cuenta, estcla, MAX(monto) as monto, ".
				 "      (SELECT denestpro1 ".
				 "		   FROM spg_ep1 ".
				 "        WHERE spg_ep1.codestpro1 = scv_dt_spg.codestpro1 ".
				 "          AND spg_ep1.estcla = scv_dt_spg.estcla) AS denestpro1, ".
				 "      (SELECT denestpro2 ".
				 "		  FROM spg_ep2 ".
				 "        WHERE spg_ep2.codestpro1 = scv_dt_spg.codestpro1 ".
				 "		    AND spg_ep2.codestpro2 = scv_dt_spg.codestpro2 ".
				 "          AND spg_ep2.estcla = scv_dt_spg.estcla) AS denestpro2, ".
				 "      (SELECT denestpro3 ".
				 "		  FROM spg_ep3 ".
				 "       WHERE spg_ep3.codestpro1 = scv_dt_spg.codestpro1 ".
				 "		   AND spg_ep3.codestpro2 = scv_dt_spg.codestpro2 ".
				 "         AND spg_ep3.codestpro3 = scv_dt_spg.codestpro3 ".
				 "         AND spg_ep3.estcla = scv_dt_spg.estcla) AS denestpro3, ".
				 "      (SELECT denestpro4 ".
				 "		  FROM spg_ep4 ".
				 "       WHERE spg_ep4.codestpro1 = scv_dt_spg.codestpro1 ".
				 "		   AND spg_ep4.codestpro2 = scv_dt_spg.codestpro2 ".
				 "         AND spg_ep4.codestpro3 = scv_dt_spg.codestpro3 ".
				 "         AND spg_ep4.codestpro4 = scv_dt_spg.codestpro4 ".
				 "         AND spg_ep4.estcla = scv_dt_spg.estcla) AS denestpro4, ".
				 "      (SELECT denestpro5 ".
				 "		  FROM spg_ep5 ".
				 "       WHERE spg_ep5.codestpro1 = scv_dt_spg.codestpro1 ".
				 "		   AND spg_ep5.codestpro2 = scv_dt_spg.codestpro2 ".
				 "         AND spg_ep5.codestpro3 = scv_dt_spg.codestpro3 ".
				 "         AND spg_ep5.codestpro4 = scv_dt_spg.codestpro4 ".
				 "         AND spg_ep5.codestpro5 = scv_dt_spg.codestpro5 ".
				 "         AND spg_ep5.estcla = scv_dt_spg.estcla) AS denestpro5 ".
				 "  FROM scv_dt_spg ".
				 " WHERE codemp='".$as_codemp."'".
				 "   AND codsolvia='".$as_codsolvia."'".
				 " GROUP BY codemp,codsolvia,codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta ";

	    $rs_data=$this->io_sql->select($ls_sql);
	    $li_numrows=$this->io_sql->num_rows($rs_data);	
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_solicitudpago_spg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if ($li_numrows>0)
			{
				$data=$this->io_sql->obtener_datos($rs_data);
				$this->ds_detpresup->data=$data;
				$this->ds_detpresup->group_by(array('0'=>'codemp','1'=>'codsolvia'),array('0'=>'monto'),'monto');	
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido; 
	} //fin  function uf_select_solicitudpago_spg

	function uf_select_ruta($as_codemp,$as_codsolvia)	
	{
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	         Function: uf_select_ruta
		//	           Access: public
		//  		Arguments: $as_codemp     // codigo de empresa
		//  			       $as_codsolvia  // codigo de la solicitud de viaticos
		//	         Returns : $lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//	      Description: Función que se encarga de buscar la rutaque presenta 
		//         Creado por: Ing. Luis Anibal Lang           
		//   Fecha de Cracion: 13/09/2006							Fecha de Ultima Modificación:   
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_denrut="";
		$ls_sql= "SELECT scv_rutas.desrut ".
				 "  FROM scv_rutas,scv_solicitudviatico ".
				 " WHERE scv_solicitudviatico.codemp='".$as_codemp."'".
				 "   AND scv_solicitudviatico.codsolvia='".$as_codsolvia."'".
				 "   AND scv_solicitudviatico.codemp=scv_rutas.codemp".
				 "   AND scv_solicitudviatico.codrut=scv_rutas.codrut".
				 " GROUP BY scv_rutas.desrut ";
	    $rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
		//	$this->io_msg->message("ERROR");
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_ruta ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_denrut=$row["desrut"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_denrut; 
	} //fin  function uf_select_ruta
	
	function uf_scv_load_codigopersonal($as_codemp,$as_cedper,$as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_categoria_personal
		//         Access: public 
		//      Argumento: $as_codemp  // codigo de empresa
		//  			   $as_cedper  // cedula de personal
		//  			   $as_codper  // codigo de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que obtiene el codigo de un personal dado su cedula
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 17/04/2007								Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sql="SELECT codper".
		        "  FROM sno_personal".
				" WHERE codemp='". $as_codemp ."'".
				"   AND cedper='". $as_cedper ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_load_codigopersonal ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codper=$row["codper"];
				$lb_valido=true;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_codper']=$as_codper;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end function uf_scv_load_codigopersonal

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_listadosolicitudes($ad_fecregdes,$ad_fecreghas,$as_coduniadm,$as_orden,$as_tipvia,$as_codmis,$ls_codmisdes,
										  $as_codsoldes,$as_codsolhas,$as_codtipdoc,$as_continente,$as_estatus,$as_codben)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_listadosolicitudes
		//         Access: public  
		//	    Arguments: ad_fecregdes // Inicio del Intervalo de Fecha del Reporte
		//	    		   ad_fecreghas // Fin del Intervalo de Fecha del Reporte
		//	    		   as_coduniadm // Codigo de unidad administrativa
		//	    		   as_orden     // Variable de orden
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Funcion que busca la informacion basica de los beneficiarios de un viatico en un 
		//                 lapso de tiempo indicado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/06/2007									Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funcion->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.fecsolvia>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funcion->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.fecsolvia<='".$ad_fecreghas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.coduniadm='".$as_coduniadm."'";
		}
		if(!empty($as_codmis))
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.codmis='".$as_codmis."'";
		}
		if(!empty($as_codmisdes))
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.codmisdes='".$as_codmisdes."'";
		}
		if(!empty($as_codsoldes))
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.codsolvia>='".$as_codsoldes."'";
		}
		if(!empty($as_codsolhas))
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.codsolvia<='".$as_codsolhas."'";
		}
		if($as_tipvia!='0')
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.tipvia='".$as_tipvia."'";
		}
		if(!empty($as_codtipdoc))
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.codtipdoc='".$as_codtipdoc."'";
		}
		if($as_estatus!='-')
		{
			$ls_criterio=$ls_criterio. "  AND scv_solicitudviatico.estsolvia='".$as_estatus."'";
		}
		if(!empty($as_codben))
		{
			$ls_criterio=$ls_criterio. "  AND scv_dt_personal.codper='".$as_codben."'";
		}
		switch ($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadenaben="CONCAT(rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				$ls_cadenaper="CONCAT(sno_personal.nomper,' ',sno_personal.apeper)";
				break;
			case "MYSQLI":
				$ls_cadenaben="CONCAT(rpc_beneficiario.nombene,' ',rpc_beneficiario.apebene)";
				$ls_cadenaper="CONCAT(sno_personal.nomper,' ',sno_personal.apeper)";
				break;
			case "POSTGRES":
				$ls_cadenaben="rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				$ls_cadenaper="sno_personal.nomper||' '||sno_personal.apeper";
				break;
			case "oci8po":
				$ls_cadenaben="rpc_beneficiario.nombene||' '||rpc_beneficiario.apebene";
				$ls_cadenaper="sno_personal.nomper||' '||sno_personal.apeper";
				break;
		}
		if($as_continente=="---")
		{
			$ls_sql="SELECT scv_solicitudviatico.codsolvia,scv_solicitudviatico.fecsalvia,MAX(scv_solicitudviatico.fecsolvia) AS fecsolvia,scv_solicitudviatico.fecregvia,".
					"       scv_dt_personal.codper,MAX(scv_solicitudviatico.monsolvia) AS monsolvia,MAX(scv_solicitudviatico.mondolsol) AS mondolsol,scv_solicitudviatico.tipvia, ".
					"       (SELECT max(scv_rutas.desrut)".
					"          FROM scv_rutas".
					"         WHERE scv_rutas.codemp=scv_solicitudviatico.codemp ".
					"           AND scv_rutas.codrut=scv_solicitudviatico.codrut) AS desrut,".
					"       (CASE scv_dt_personal.codclavia WHEN '' THEN (SELECT ".$ls_cadenaben." ".
					"                                                       FROM rpc_beneficiario".
					"                                                      WHERE rpc_beneficiario.codemp=scv_dt_personal.codemp".
					"                                                        AND rpc_beneficiario.ced_bene=scv_dt_personal.codper)".
					"                                       ELSE (SELECT ".$ls_cadenaper." ".
					"                                               FROM sno_personal".
					"                                              WHERE sno_personal.codemp=scv_dt_personal.codemp".
					"                                                AND sno_personal.codper=scv_dt_personal.codper) END ) AS nombre,".
					"       (CASE scv_dt_personal.codclavia WHEN '' THEN (SELECT ced_bene".
					"                                                       FROM rpc_beneficiario".
					"                                                      WHERE rpc_beneficiario.codemp=scv_dt_personal.codemp".
					"                                                        AND rpc_beneficiario.ced_bene=scv_dt_personal.codper)".
					"                                       ELSE (SELECT cedper".
					"                                               FROM sno_personal".
					"                                              WHERE sno_personal.codemp=scv_dt_personal.codemp".
					"                                                AND sno_personal.codper=scv_dt_personal.codper) END ) AS cedula, ".
					"       (SELECT SUM(monto)".
					"          FROM scv_dt_spg".
					"         WHERE scv_dt_spg.codemp=scv_dt_personal.codemp".
					"           AND scv_dt_spg.ced_bene=(CASE scv_dt_personal.codclavia WHEN '' THEN ".
					"                                   (SELECT ced_bene FROM rpc_beneficiario WHERE  ".
					"                                    rpc_beneficiario.codemp=scv_dt_personal.codemp ".
					"                                    AND rpc_beneficiario.ced_bene=scv_dt_personal.codper) ".
					"                                    ELSE (SELECT cedper FROM sno_personal WHERE ".
					"                                    sno_personal.codemp=scv_dt_personal.codemp ".
					"                                    AND sno_personal.codper=scv_dt_personal.codper) END ) ".
					"           AND scv_dt_spg.codsolvia=scv_dt_personal.codsolvia) AS monto,".
					" (SELECT scv_ciudades.desciu".
					"    FROM scv_ciudades,scv_misiones".
					"   WHERE scv_misiones.codemp=scv_solicitudviatico.codemp".
					"     AND MAX(scv_solicitudviatico.codmis)=scv_misiones.codmis".
					"     AND scv_misiones.codpai=scv_ciudades.codpai".
					"     AND scv_misiones.codest=scv_ciudades.codest".
					"     AND scv_misiones.codciu=scv_ciudades.codciu) AS desciuori,".
					" (SELECT scv_ciudades.desciu".
					"    FROM scv_ciudades,scv_misiones".
					"   WHERE scv_misiones.codemp=scv_solicitudviatico.codemp".
					"     AND MAX(scv_solicitudviatico.codmisdes)=scv_misiones.codmis".
					"     AND scv_misiones.codpai=scv_ciudades.codpai".
					"     AND scv_misiones.codest=scv_ciudades.codest".
					"     AND scv_misiones.codciu=scv_ciudades.codciu) AS desciudes".
					"  FROM scv_solicitudviatico,scv_rutas,scv_dt_personal".
					" WHERE scv_solicitudviatico.codemp='".$this->ls_codemp."'".
					"   ".$ls_criterio." ".
					"   AND scv_solicitudviatico.codemp=scv_dt_personal.codemp".
					"   AND scv_solicitudviatico.codsolvia=scv_dt_personal.codsolvia".
					" GROUP BY scv_solicitudviatico.codemp,scv_solicitudviatico.codsolvia,scv_dt_personal.codper, ".
					" scv_solicitudviatico.fecsalvia, scv_solicitudviatico.fecregvia,scv_solicitudviatico.tipvia,  ".
					" scv_dt_personal.codper,scv_dt_personal.codclavia,scv_dt_personal.codemp, ".
					" scv_dt_personal.codsolvia, scv_solicitudviatico.codrut ".
					" ORDER BY ".$as_orden."";
		}
		else
		{
			$ls_sql="SELECT scv_solicitudviatico.codsolvia,scv_solicitudviatico.fecsalvia,MAX(scv_solicitudviatico.fecsolvia) AS fecsolvia,scv_solicitudviatico.fecregvia,scv_solicitudviatico.tipvia,".
					"       scv_dt_personal.codper,MAX(scv_solicitudviatico.mondolsol) AS mondolsol,MAX(scv_solicitudviatico.monsolvia) AS monsolvia,".
					"       (SELECT max(scv_rutas.desrut)".
					"          FROM scv_rutas".
					"         WHERE scv_rutas.codemp=scv_solicitudviatico.codemp ".
					"           AND scv_rutas.codrut=scv_solicitudviatico.codrut) AS desrut,".
					"       (CASE scv_dt_personal.codclavia WHEN '' THEN (SELECT ".$ls_cadenaben." ".
					"                                                       FROM rpc_beneficiario".
					"                                                      WHERE rpc_beneficiario.codemp=scv_dt_personal.codemp".
					"                                                        AND rpc_beneficiario.ced_bene=scv_dt_personal.codper)".
					"                                       ELSE (SELECT ".$ls_cadenaper." ".
					"                                               FROM sno_personal".
					"                                              WHERE sno_personal.codemp=scv_dt_personal.codemp".
					"                                                AND sno_personal.codper=scv_dt_personal.codper) END ) AS nombre,".
					"       (CASE scv_dt_personal.codclavia WHEN '' THEN (SELECT ced_bene".
					"                                                       FROM rpc_beneficiario".
					"                                                      WHERE rpc_beneficiario.codemp=scv_dt_personal.codemp".
					"                                                        AND rpc_beneficiario.ced_bene=scv_dt_personal.codper)".
					"                                       ELSE (SELECT cedper".
					"                                               FROM sno_personal".
					"                                              WHERE sno_personal.codemp=scv_dt_personal.codemp".
					"                                                AND sno_personal.codper=scv_dt_personal.codper) END ) AS cedula, ".
					"       (SELECT SUM(monto)".
					"          FROM scv_dt_spg".
					"         WHERE scv_dt_spg.codemp=scv_dt_personal.codemp".
					"           AND scv_dt_spg.ced_bene=(CASE scv_dt_personal.codclavia WHEN '' THEN ".
					"                                   (SELECT ced_bene FROM rpc_beneficiario WHERE  ".
					"                                    rpc_beneficiario.codemp=scv_dt_personal.codemp ".
					"                                    AND rpc_beneficiario.ced_bene=scv_dt_personal.codper) ".
					"                                    ELSE (SELECT cedper FROM sno_personal WHERE ".
					"                                    sno_personal.codemp=scv_dt_personal.codemp ".
					"                                    AND sno_personal.codper=scv_dt_personal.codper) END ) ".
					"           AND scv_dt_spg.codsolvia=scv_dt_personal.codsolvia) AS monto,".
					" (SELECT scv_ciudades.desciu".
					"    FROM scv_ciudades,scv_misiones".
					"   WHERE scv_misiones.codemp=scv_solicitudviatico.codemp".
					"     AND MAX(scv_solicitudviatico.codmis)=scv_misiones.codmis".
					"     AND scv_misiones.codpai=scv_ciudades.codpai".
					"     AND scv_misiones.codest=scv_ciudades.codest".
					"     AND scv_misiones.codciu=scv_ciudades.codciu) AS desciuori,".
					" (SELECT scv_ciudades.desciu".
					"    FROM scv_ciudades,scv_misiones".
					"   WHERE scv_misiones.codemp=scv_solicitudviatico.codemp".
					"     AND MAX(scv_solicitudviatico.codmisdes)=scv_misiones.codmis".
					"     AND scv_misiones.codpai=scv_ciudades.codpai".
					"     AND scv_misiones.codest=scv_ciudades.codest".
					"     AND scv_misiones.codciu=scv_ciudades.codciu) AS desciudes".
					"  FROM scv_solicitudviatico,scv_rutas,scv_dt_personal,scv_misiones,sigesp_pais".
					" WHERE scv_solicitudviatico.codemp='".$this->ls_codemp."'".
					"   ".$ls_criterio." ".
					"   AND sigesp_pais.codcont='".$as_continente."'".
					"   AND scv_solicitudviatico.codemp=scv_dt_personal.codemp".
					"   AND scv_solicitudviatico.codsolvia=scv_dt_personal.codsolvia".
					"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
					"   AND scv_solicitudviatico.codmis=scv_misiones.codmis".
					"   AND scv_misiones.codpai=sigesp_pais.codpai".
					"   AND scv_solicitudviatico.codsolvia=scv_dt_personal.codsolvia".
					" GROUP BY scv_solicitudviatico.codemp,scv_solicitudviatico.codsolvia,scv_dt_personal.codper, ".
					" scv_solicitudviatico.fecsalvia,scv_solicitudviatico.tipvia, scv_solicitudviatico.fecregvia,  ".
					" scv_dt_personal.codper,scv_dt_personal.codclavia,scv_dt_personal.codemp, ".
					" scv_dt_personal.codsolvia, scv_solicitudviatico.codrut ".
					" ORDER BY ".$as_orden."";
		}
				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report MÉTODO->uf_select_listadosolicitudes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_solicitud->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_select_listadosolicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_scv_select_tarifacargo($as_codemp,$as_codcar,$as_codnom,$as_tipvia='-')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montarcar="";
		$ls_sql="SELECT montarcar".
				"  FROM scv_tarifacargos,scv_dt_tarifacargos".
				" WHERE scv_tarifacargos.codemp='". $as_codemp ."'".
				"   AND scv_tarifacargos.tipvia='". $as_tipvia ."'".
				"   AND scv_dt_tarifacargos.codcar='". $as_codcar ."'".
				"   AND scv_dt_tarifacargos.codnom= '".$as_codnom."'".
				"   AND scv_tarifacargos.codemp=scv_dt_tarifacargos.codemp".
				"   AND scv_tarifacargos.codtar=scv_dt_tarifacargos.codtar";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_tarifacargo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["montarcar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montarcar;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_select_tasacambio($as_codemp,$as_codcar,$as_codnom,$as_tipvia='-')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_solicitudviaticos
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_tascam="";
		$ls_sql="SELECT sigesp_dt_moneda.codmon,MAX(tascam1) AS tascam".
				"  FROM scv_tarifacargos,scv_dt_tarifacargos,sigesp_dt_moneda".
				" WHERE scv_tarifacargos.codemp='". $as_codemp ."'".
				"   AND scv_tarifacargos.tipvia='". $as_tipvia ."'".
				"   AND scv_dt_tarifacargos.codcar='". $as_codcar ."'".
				"   AND scv_dt_tarifacargos.codnom= '".$as_codnom."'".
				"   AND scv_tarifacargos.codemp=scv_dt_tarifacargos.codemp".
				"   AND scv_tarifacargos.codtar=scv_dt_tarifacargos.codtar".
				"   AND scv_tarifacargos.codmon=sigesp_dt_moneda.codmon".
				" GROUP BY sigesp_dt_moneda.codmon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_tarifacargo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_tascam=$row["tascam"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_tascam;
	}  // end function uf_scv_select_solicitudviaticos

	function uf_scv_select_destino($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_destino
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_codreg="";
		$ls_sql="SELECT codreg".
				"  FROM scv_solicitudviatico,scv_misiones,scv_dt_regiones_int".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmisdes=scv_misiones.codmis".
				"   AND scv_misiones.codemp=scv_dt_regiones_int.codemp".
				"   AND scv_misiones.codpai=scv_dt_regiones_int.codpai"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_destino ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codreg=$row["codreg"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_codreg;
	}  // end function uf_scv_select_destino
	
	function uf_scv_select_mision_destino($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_destino
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_denmis="";
		$ls_sql="SELECT denmis".
				"  FROM scv_solicitudviatico,scv_misiones".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmisdes=scv_misiones.codmis"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_destino ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_denmis=$row["denmis"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_denmis;
	}  // end function uf_scv_select_destino
	
	function uf_scv_select_cargafamiliar($as_codemp,$as_codsolvia)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_cargafamiliar
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_datos="";
		$la_datos[1]=0;
		$la_datos[2]="";
		$ls_sql="SELECT porcar,dencar".
				"  FROM scv_solicitudviatico,scv_cargafamiliar".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_cargafamiliar.codemp".
				"   AND scv_solicitudviatico.codcar=scv_cargafamiliar.codcar"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_cargafamiliar ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montarcar=$row["porcar"];
				$li_dencar=$row["dencar"];
				$la_datos[1]=$li_montarcar;
				$la_datos[2]=$li_dencar;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $la_datos;
	}  // end function uf_scv_select_solicitudviaticos
	
	function uf_scv_select_incrementoorden($as_codemp,$as_codsolvia,$as_codregdes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_incrementoorden
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$la_data="";
		$la_data[1]=0;
		$la_data[2]="";
		$ls_sql="SELECT codreg AS codregori".
				"  FROM scv_solicitudviatico,scv_misiones,scv_dt_regiones_int".
				" WHERE scv_solicitudviatico.codemp='". $as_codemp ."'".
				"   AND scv_solicitudviatico.codsolvia='". $as_codsolvia ."'".
				"   AND scv_solicitudviatico.codemp=scv_misiones.codemp".
				"   AND scv_solicitudviatico.codmis=scv_misiones.codmis".
				"   AND scv_misiones.codemp=scv_dt_regiones_int.codemp".
				"   AND scv_misiones.codpai=scv_dt_regiones_int.codpai";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_incrementoorden ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codregori=$row["codregori"];
				$ls_sql="SELECT porinc,deninc".
						"  FROM scv_incremento,scv_dt_incremento".
						" WHERE scv_incremento.codemp='". $as_codemp ."'".
						"   AND scv_incremento.codregori='". $ls_codregori ."'".
						"   AND scv_dt_incremento.codregdes='".$as_codregdes."'".
						"   AND scv_incremento.codemp=scv_dt_incremento.codemp".
						"   AND scv_incremento.codinc=scv_dt_incremento.codinc"; 	
				$rs_data2=$this->io_sql->select($ls_sql);
				if($row=$this->io_sql->fetch_row($rs_data2))
				{
					$li_montarcar=$row["porinc"];
					$ls_deninc=$row["deninc"];
					$la_data[1]=$li_montarcar;
					$la_data[2]=$ls_deninc;
				}
				$this->io_sql->free_result($rs_data2);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $la_data;
	}  // end function uf_scv_select_solicitudviaticos
	
	function uf_load_misiones_internacionales($as_codemp,$as_codsolvia,$as_cargo,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_misiones_internacionales
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codsolvia // codigo de solicitud de viaticos
		//  			   $ai_totrows   // total de lineas del grid
		//  			   $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que carga el grid con el personal de una solicitud de viaticos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 07/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$li_i=0;
		$li_totol=0;
		$li_totolgeneral=0;
		$ls_sql="SELECT codmis,cantidad,".
				"       (SELECT denmis FROM scv_misiones".
				"		  WHERE scv_dt_misiones.codemp=scv_misiones.codemp".
				"           AND scv_dt_misiones.codmis=scv_misiones.codmis) AS denmis".
				"  FROM scv_dt_misiones".
				" WHERE scv_dt_misiones.codemp='".$as_codemp."'".
				"   AND scv_dt_misiones.codsolvia='".$as_codsolvia."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_load_misiones_internacionales ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codmis=$row["codmis"];
				$li_cantidad=$row["cantidad"];
				$ls_denmis=$row["denmis"];
				$li_tarifa=$this->uf_scv_tarifas_internacionales($as_codemp,$ls_codmis,$as_cargo,$as_codnom);
				$li_totol=($li_cantidad*$li_tarifa);
				$li_totolgeneral=$li_totolgeneral+$li_totol;
				$lb_valido=true;
				
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_totolgeneral;
	}  // end function uf_scv_load_dt_personal

	function uf_scv_tarifas_internacionales($as_codemp,$as_codmis,$as_cargo,$as_codnom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_tarifas_internacionales
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_montar="";
		if($as_codnom!="")
		{
			$ls_sql="SELECT codcat".
					"  FROM scv_catcargos,scv_dt_catcargos".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_dt_catcargos.codcar='". $as_cargo ."'".
					"   AND scv_dt_catcargos.codnom= '".$as_codnom."'".
					"   AND scv_catcargos.codemp=scv_dt_catcargos.codemp".
					"   AND scv_catcargos.codcatcar=scv_dt_catcargos.codcatcar";
		}
		else
		{
			$ls_sql="SELECT codcat".
					"  FROM scv_catcargos".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_catcargos.foraneo='1'";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_tarifas_internacionales ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codcat=$row["codcat"];
				$ls_sql="SELECT mondol".
						"  FROM scv_tarifas".
						" WHERE scv_tarifas.codemp='". $as_codemp ."'".
						"   AND scv_tarifas.codmis='". $as_codmis ."'".
						"   AND scv_tarifas.codcat='".$ls_codcat."'"; 
				$rs_data2=$this->io_sql->select($ls_sql);
				if($row=$this->io_sql->fetch_row($rs_data2))
				{
					$li_montar=$row["mondol"];
				}
				$this->io_sql->free_result($rs_data2);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montar;
	}  // end function uf_scv_select_solicitudviaticos
	
	function uf_scv_select_tasacambio_internacional($as_codemp,$as_codcar,$as_codnom,$as_tipvia='-')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_scv_select_tasacambio_internacional
		//         Access: public 
		//      Argumento: $as_codemp    // codigo de empresa
		//  			   $as_codtar    // codigo de tarifa
		//  			   $as_codcatper // codigo de categoria de personal
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que la tarifa de viaticos se corresponda con la categoria del personal
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/11/2006 								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_tascam="";
		if($as_codnom!="")
		{
			$ls_sql="SELECT sigesp_dt_moneda.codmon,MAX(tascam1) AS tascam".
					"  FROM scv_catcargos,scv_dt_catcargos,sigesp_dt_moneda".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_dt_catcargos.codcar='". $as_codcar ."'".
					"   AND scv_dt_catcargos.codnom= '".$as_codnom."'".
					"   AND scv_catcargos.codemp=scv_dt_catcargos.codemp".
					"   AND scv_catcargos.codcatcar=scv_dt_catcargos.codcatcar".
					"   AND scv_catcargos.codmon=sigesp_dt_moneda.codmon".
					" GROUP BY sigesp_dt_moneda.codmon";
		}
		else
		{
			$ls_sql="SELECT sigesp_dt_moneda.codmon,MAX(tascam1) AS tascam".
					"  FROM scv_catcargos,sigesp_dt_moneda".
					" WHERE scv_catcargos.codemp='". $as_codemp ."'".
					"   AND scv_catcargos.foraneo='1'".
					"   AND scv_catcargos.codmon=sigesp_dt_moneda.codmon".
					" GROUP BY sigesp_dt_moneda.codmon";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->solicitud_viaticos MÉTODO->uf_scv_select_tasacambio_internacional ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_tascam=$row["tascam"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_tascam;
	}  // end function uf_scv_select_tasacambio_internacional



} //fin  class sigesp_siv_class_report
?>
