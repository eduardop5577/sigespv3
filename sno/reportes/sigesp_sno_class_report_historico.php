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

class sigesp_sno_class_report_historico
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_class_report_historico
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 02/02/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		//$this->io_conexion->debug=true;
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_detalle=new class_datastore();
		$this->DS_detalle2=new class_datastore();
		$this->DS_asigna=new class_datastore();
		$this->DS_pension=new class_datastore();
		$this->DS_pension2=new class_datastore();	
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
        $this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
        $this->ls_anocurnom=$_SESSION["la_nomina"]["anocurnom"];
		$this->li_rac=$_SESSION["la_nomina"]["racnom"];
		$this->rs_data="";
		$this->rs_data_detalle="";
		$this->rs_data_detalle2="";
	}// end function sigesp_sno_class_report_historico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci?n a la que pertenece la variable
		//				   as_variable  // Variable nombre de la variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci?n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql="SELECT value ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report Contable M?TODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
		return rtrim($ls_valor);
	}// end function uf_select_config
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_insert_config
		//		   Access: public
		//	    Arguments: as_sistema  // Sistema al que pertenece la variable
		//				   as_seccion  // Secci?n a la que pertenece la variable
		//				   as_variable  // Variable a buscar
		//				   as_valor  // valor por defecto que debe tener la variable
		//				   as_tipo  // tipo de la variable
		//	      Returns: $lb_valido True si se ejecuto el insert ? False si hubo error en el insert
		//	  Description: Funci?n que inserta la variable de configuraci?n
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/01/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();		
		$ls_sql="DELETE ".
				"  FROM sigesp_config ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codsis='".$as_sistema."' ".
				"   AND seccion='".$as_seccion."' ".
				"   AND entry='".$as_variable."' ";		
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report Contable M?TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
				$this->io_mensajes->message("CLASE->Report Contable M?TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_prenomina_personal($as_codperdes,$as_codperhas,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prenomina_personal
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_orden // Orde a mostrar en el reporte		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que se le calcul? la pren?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 26/04/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper,sno_personal.nomper, sno_personal.apeper ".
				"  FROM sno_personal, sno_thpersonalnomina, sno_thprenomina, sno_thconcepto ".
				" WHERE sno_thprenomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thprenomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thprenomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thprenomina.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_thpersonalnomina.codemp = sno_thprenomina.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thprenomina.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thprenomina.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thprenomina.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thprenomina.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_thpersonalnomina.codper = sno_personal.codper ".
				"   AND sno_thprenomina.codemp = sno_thconcepto.codemp ".
				"   AND sno_thprenomina.codnom = sno_thconcepto.codnom ".
				"   AND sno_thprenomina.anocur = sno_thconcepto.anocur ".
				"   AND sno_thprenomina.codperi = sno_thconcepto.codperi ".
				"   AND sno_thprenomina.codconc = sno_thconcepto.codconc ".
				" GROUP BY sno_personal.codper,sno_personal.nomper, sno_personal.apeper ".
				"   ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_prenomina_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_prenomina_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prenomina_conceptopersonal($as_codper,$as_conceptocero,$as_conceptop2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_prenomina_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: as_codper // C?digo de Personal
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados al personal que se le calcul? la pren?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 26/04/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_thprenomina.valprenom<>0 ";
		}
		if(empty($as_conceptop2))
		{
			$ls_criterio = $ls_criterio." AND (sno_thprenomina.tipprenom<>'P2' AND sno_thprenomina.tipprenom<>'V4' AND sno_thprenomina.tipprenom<>'W4')";
		}
		$ls_sql="SELECT sno_thprenomina.codconc, sno_thconcepto.nomcon, sno_thprenomina.tipprenom, sno_thprenomina.valprenom, sno_thprenomina.valhis ".
				"  FROM sno_thprenomina, sno_thconcepto ".
				" WHERE sno_thprenomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thprenomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thprenomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thprenomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thprenomina.codper='".$as_codper."' ".
				"     ".$ls_criterio.
				"   AND sno_thprenomina.codemp = sno_thconcepto.codemp ".
				"   AND sno_thprenomina.codnom = sno_thconcepto.codnom ".
				"   AND sno_thprenomina.anocur = sno_thconcepto.anocur ".
				"   AND sno_thprenomina.codperi = sno_thconcepto.codperi ".
				"   AND sno_thprenomina.codconc = sno_thconcepto.codconc ".
				" ORDER BY sno_thprenomina.codconc ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_prenomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_prenomina_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_personal($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,$as_codubifis,
									$as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden,$as_pagobanco,
									$as_pagocheque)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_personal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/02/2006								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_thpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_thpersonalnomina.codubifis='".$as_codubifis."'";
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
			$ls_criterio= $ls_criterio."   AND (sno_thpersonalnomina.pagbanper = 1 OR sno_thpersonalnomina.pagtaqper = 1)".
									   "   AND sno_thpersonalnomina.pagefeper = 0 ";
			$ls_criteriounion= $ls_criteriounion."   AND (sno_thpersonalnomina.pagbanper = 1 OR sno_thpersonalnomina.pagtaqper = 1)".
									   			 "   AND sno_thpersonalnomina.pagefeper = 0 ";
		}							 
		if(($as_pagobanco==0)&&($as_pagocheque==1))
		{
			$ls_criterio= $ls_criterio."   AND (sno_thpersonalnomina.pagbanper = 0 OR sno_thpersonalnomina.pagtaqper = 0)".
									   "   AND sno_thpersonalnomina.pagefeper = 1 ";
			$ls_criteriounion= $ls_criteriounion."   AND (sno_thpersonalnomina.pagbanper = 0 OR sno_thpersonalnomina.pagtaqper = 0)".
									   			 "   AND sno_thpersonalnomina.pagefeper = 1 ";
		}							 
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
			}
		}
		if(empty($as_orden))
		{
			$ls_orden=" ORDER BY sno_personal.codper ";
		}
		else
		{
			switch($as_orden)
			{
				case "1": // Ordena por unidad administrativa
					$ls_orden=" ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm, codper ";
					break;

				case "2": // Ordena por C?digo de personal
					$ls_orden=" ORDER BY codper ";
					break;

				case "3": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY apeper ";
					break;

				case "4": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "		sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, ".
					  "		sno_thunidadadmin.codestpro3, sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, MAX(sno_thpersonalnomina.sueper) AS sueper, ".
					  "		sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, ".
					  "	        MAX(sno_thpersonalnomina.codpas) AS codpas, MAX(sno_thpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_thpersonalnomina.descasicar) AS descasicar, ".
					  "		MAX(sno_thpersonalnomina.obsrecper) As obsrecper, sno_thpersonalnomina.fecingper as fecingnom, sno_personal.sexper, MAX(sno_personal.fecingadmpubper) AS fecingadmpubper,".
					  "		  (SELECT dessubnom FROM sno_thsubnomina ".
					  "			WHERE sno_thsubnomina.codemp = sno_thpersonalnomina.codemp ".
					  "			  AND sno_thsubnomina.codnom = sno_thpersonalnomina.codnom ".
					  "			  AND sno_thsubnomina.anocur = sno_thpersonalnomina.anocur ".
					  "			  AND sno_thsubnomina.codperi = sno_thpersonalnomina.codperi ".
					  "			  AND sno_thsubnomina.codsubnom = sno_thpersonalnomina.codsubnom) AS dessubnom, ".
					  "		  (SELECT desded FROM sno_dedicacion ".
					  "			WHERE sno_dedicacion.codemp = sno_thpersonalnomina.codemp ".
					  "			  AND sno_dedicacion.codded = sno_thpersonalnomina.codded) AS desded, ".
					  "		  (SELECT destipper FROM sno_tipopersonal ".
					  "			WHERE sno_tipopersonal.codemp = sno_thpersonalnomina.codemp ".
					  "			  AND sno_tipopersonal.codded = sno_thpersonalnomina.codded ".
					  "			  AND sno_tipopersonal.codtipper = sno_thpersonalnomina.codtipper) AS destipper, ".
					  "		  (SELECT desest FROM sigesp_estados".
					  "			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
					  "		  (SELECT denmun FROM sigesp_municipio ".
					  "			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
					  "		  (SELECT denpar FROM sigesp_parroquia  ".
					  "			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
					  "			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
					  "			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
					  "			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
					  "		  (SELECT SUM(asires) FROM sno_thresumen ".
					  "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
					  "			 AND sno_thresumen.codperi = sno_thsalida.codperi) AS totalasignacion, ".
					  "		  (SELECT SUM(dedres + apoempres) FROM sno_thresumen ".
					  "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
					  "			 AND sno_thresumen.codperi = sno_thsalida.codperi) AS totaldeduccion, ".
					  "		  (SELECT SUM(apopatres) FROM sno_thresumen ".
					  "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
					  "			 AND sno_thresumen.codperi = sno_thsalida.codperi) AS totalaporte, ".
					  "		  (SELECT SUM(priquires) FROM sno_thresumen ".
					  "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
					  "			 AND sno_thresumen.codperi = sno_thsalida.codperi ".
					  "			 AND sno_thresumen.codper = sno_thsalida.codper) AS priquires, ".
					  "		  (SELECT SUM(segquires) FROM sno_thresumen ".
					  "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
					  "			 AND sno_thresumen.codperi = sno_thsalida.codperi ".
					  "			 AND sno_thresumen.codper = sno_thsalida.codper) AS segquires, ".
					  "		 (SELECT sno_componente.descom FROM sno_componente ".
					  "        WHERE sno_componente.codemp='".$this->ls_codemp."'".
					  "          AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
					  "		 (SELECT sno_escaladocente.desescdoc FROM sno_escaladocente ".
					  "          WHERE sno_escaladocente.codemp='".$this->ls_codemp."'".
					  "            AND sno_escaladocente.codemp=sno_thpersonalnomina.codemp  ".
					  "            AND sno_escaladocente.codescdoc=sno_thpersonalnomina.codescdoc) AS desescdoc, ".
					  "		 (SELECT sno_clasificaciondocente.descladoc FROM sno_clasificaciondocente ".
					  "          WHERE sno_clasificaciondocente.codemp='".$this->ls_codemp."'".
					  "            AND sno_clasificaciondocente.codemp=sno_thpersonalnomina.codemp ".
					  "            AND sno_clasificaciondocente.codcladoc=sno_thpersonalnomina.codcladoc ".
				 	  "			   AND sno_clasificaciondocente.codescdoc=sno_thpersonalnomina.codescdoc) AS descladoc, ".
					  "		 (SELECT sno_rango.desran FROM sno_rango ".
					  "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
					  "          AND sno_rango.codcom=sno_personal.codcom".
					  "          AND sno_rango.codran=sno_personal.codran) AS denran, MAX(sno_personal.situacion) AS situacion, ".
					  "        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				      "            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
                                            "        (SELECT max(sno_thnomina.desnom) FROM sno_thnomina WHERE sno_thnomina.codemp='".$this->ls_codemp."'".
                                            "            AND sno_thnomina.espnom = '0' ".
                                            "            AND sno_thnomina.codemp=sno_thpersonalnomina.codemp ".
                                            "            AND sno_thnomina.codnom=sno_thpersonalnomina.codnom) AS nomina, ".
					  "".$ls_descar.
					  "  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin, sno_ubicacionfisica  ".
					  " WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_thsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
					  "   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
					  "   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
					  "   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
					  "   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
					  "   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_thpersonalnomina.codper ".
					  "   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
					  "   AND sno_thpersonalnomina.codnom = sno_thunidadadmin.codnom ".
					  "   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
					  "   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
					  "   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
					  "   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
					  "   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
					  "   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
					  "   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
					  "   AND sno_ubicacionfisica.codemp = sno_thpersonalnomina.codemp ".
					  "	  AND sno_ubicacionfisica.codubifis = sno_thpersonalnomina.codubifis ".
					  " GROUP BY sno_thpersonalnomina.codemp, sno_thsalida.codemp, sno_thpersonalnomina.codnom, sno_thsalida.codnom,  sno_thpersonalnomina.anocur, sno_thsalida.anocur, sno_thpersonalnomina.codperi, sno_thsalida.codperi,".
					  "		   sno_personal.codper,  sno_thsalida.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.sexper,".
				  	  "		   sno_personal.fecingper, sno_thpersonalnomina.codcar, sno_thpersonalnomina.codasicar, ".
					  "		   sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, ".
					  "		   sno_thunidadadmin.codestpro3, sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, ".
					  "        sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thpersonalnomina.codded, sno_thpersonalnomina.codtipper,  ".
					  "    	   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_ubicacionfisica.codpai, sno_personal.codcom, sno_personal.codran, sno_personal.codcausa, ".
					  "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar, sno_thpersonalnomina.fecingper,sno_thpersonalnomina.codescdoc,sno_thpersonalnomina.codcladoc, sno_thpersonalnomina.codsubnom  ";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, ".
					  "		sno_thunidadadmin.codestpro3, sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, MAX(sno_thpersonalnomina.sueper) AS sueper, ".
			    "		sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, ".
				"	 MAX(sno_thpersonalnomina.codpas) AS codpas,	MAX(sno_thpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_thpersonalnomina.descasicar) AS descasicar, ".
			    "		MAX(sno_thpersonalnomina.obsrecper) As obsrecper, sno_thpersonalnomina.fecingper as fecingnom, sno_personal.sexper, MAX(sno_personal.fecingadmpubper) AS fecingadmpubper,".
                                "		  (SELECT dessubnom FROM sno_thsubnomina ".
                                "			WHERE sno_thsubnomina.codemp = sno_thpersonalnomina.codemp ".
                                "			  AND sno_thsubnomina.codnom = sno_thpersonalnomina.codnom ".
                                "			  AND sno_thsubnomina.anocur = sno_thpersonalnomina.anocur ".
                                "			  AND sno_thsubnomina.codperi = sno_thpersonalnomina.codperi ".
                                "			  AND sno_thsubnomina.codsubnom = sno_thpersonalnomina.codsubnom) AS dessubnom, ".
			    "		  (SELECT desded FROM sno_dedicacion ".
				"			WHERE sno_dedicacion.codemp = sno_thpersonalnomina.codemp ".
				"			  AND sno_dedicacion.codded = sno_thpersonalnomina.codded) AS desded, ".
				"		  (SELECT destipper FROM sno_tipopersonal ".
				"			WHERE sno_tipopersonal.codemp = sno_thpersonalnomina.codemp ".
				"			  AND sno_tipopersonal.codded = sno_thpersonalnomina.codded ".
				"			  AND sno_tipopersonal.codtipper = sno_thpersonalnomina.codtipper) AS destipper, ".
				"		  (SELECT desest FROM sigesp_estados  ".
				"			WHERE sigesp_estados.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_estados.codest = sno_ubicacionfisica.codest) AS desest, ".
				"		  (SELECT denmun FROM sigesp_municipio  ".
				"			WHERE sigesp_municipio.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_municipio.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_municipio.codmun = sno_ubicacionfisica.codmun) AS denmun, ".
				"		  (SELECT denpar FROM sigesp_parroquia  ".
				"			WHERE sigesp_parroquia.codpai = sno_ubicacionfisica.codpai ".
				"			 AND sigesp_parroquia.codest = sno_ubicacionfisica.codest ".
				"			 AND sigesp_parroquia.codmun = sno_ubicacionfisica.codmun ".
				"			 AND sigesp_parroquia.codpar = sno_ubicacionfisica.codpar) AS denpar, ".
			    "		  (SELECT SUM(asires) FROM sno_thresumen ".
			    "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
			    "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
			    "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
			    " 		 AND sno_thresumen.codperi = sno_thsalida.codperi) AS totalasignacion, ".
			    "		  (SELECT SUM(dedres + apoempres) FROM sno_thresumen ".
			    "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
			    "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
			    "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
			    "			 AND sno_thresumen.codperi = sno_thsalida.codperi) AS totaldeduccion, ".
			    "		  (SELECT SUM(apopatres) FROM sno_thresumen ".
			    "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
			    "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
			    "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
			    "			 AND sno_thresumen.codperi = sno_thsalida.codperi) AS totalaporte, ".
			    "		  (SELECT SUM(priquires) FROM sno_thresumen ".
			    "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
			    "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
			    "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
			    "			 AND sno_thresumen.codperi = sno_thsalida.codperi ".
			    "			 AND sno_thresumen.codper = sno_thsalida.codper) AS priquires, ".
			    "		  (SELECT SUM(segquires) FROM sno_thresumen ".
			    "			WHERE sno_thresumen.codemp = sno_thsalida.codemp ".
			    "			 AND sno_thresumen.codnom = sno_thsalida.codnom ".
			    "			 AND sno_thresumen.anocur = sno_thsalida.anocur ".
			    "			 AND sno_thresumen.codperi = sno_thsalida.codperi ".
			    "			 AND sno_thresumen.codper = sno_thsalida.codper) AS segquires, ".
			    "		 (SELECT sno_componente.descom FROM sno_componente ".
			    "        WHERE sno_componente.codemp='".$this->ls_codemp."'".
			    "          AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
			    "		 (SELECT sno_escaladocente.desescdoc FROM sno_escaladocente ".
			    "          WHERE sno_escaladocente.codemp='".$this->ls_codemp."'".
			    "            AND sno_escaladocente.codemp=sno_thpersonalnomina.codemp  ".
			    "            AND sno_escaladocente.codescdoc=sno_thpersonalnomina.codescdoc) AS desescdoc, ".
			    "		 (SELECT sno_clasificaciondocente.descladoc FROM sno_clasificaciondocente ".
			    "          WHERE sno_clasificaciondocente.codemp='".$this->ls_codemp."'".
			    "            AND sno_clasificaciondocente.codemp=sno_thpersonalnomina.codemp ".
			    "            AND sno_clasificaciondocente.codcladoc=sno_thpersonalnomina.codcladoc ".
			    "			   AND sno_clasificaciondocente.codescdoc=sno_thpersonalnomina.codescdoc) AS descladoc, ".
			    "		 (SELECT sno_rango.desran FROM sno_rango ".
			    "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
			    "          AND sno_rango.codcom=sno_personal.codcom".
			    "          AND sno_rango.codran=sno_personal.codran) AS denran, MAX(sno_personal.situacion) AS situacion, ".
			    "        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
			    "            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
                            "        (SELECT max(sno_thnomina.desnom) FROM sno_thnomina WHERE sno_thnomina.codemp='".$this->ls_codemp."'".
                            "            AND sno_thnomina.espnom = '0' ".
                            "            AND sno_thnomina.codemp=sno_thpersonalnomina.codemp ".
                            "            AND sno_thnomina.codnom=sno_thpersonalnomina.codnom) AS nomina, ".
				"  ".$ls_descar.
				"  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thunidadadmin.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
			    "   AND sno_ubicacionfisica.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_ubicacionfisica.codubifis = sno_thpersonalnomina.codubifis ".
				" GROUP BY sno_thpersonalnomina.codemp, sno_thsalida.codemp, sno_thpersonalnomina.codnom, sno_thsalida.codnom,  sno_thpersonalnomina.anocur, sno_thsalida.anocur, sno_thpersonalnomina.codperi, sno_thsalida.codperi,".
				"		   sno_personal.codper, sno_thsalida.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		   sno_personal.fecingper, sno_thpersonalnomina.codcar, sno_thpersonalnomina.codasicar, sno_personal.sexper,".
				"		   sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, ".
			    "		   sno_thunidadadmin.codestpro3, sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, ".
				"          sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thpersonalnomina.codded, sno_thpersonalnomina.codtipper,  ".
				"    	   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_ubicacionfisica.codpai, sno_personal.codcom, sno_personal.codran, sno_personal.codcausa, ".
			    "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar, sno_personal.codcom, sno_personal.codran,sno_personal.codcausa,sno_thpersonalnomina.fecingper,sno_thpersonalnomina.codescdoc,sno_thpersonalnomina.codcladoc, sno_thpersonalnomina.codsubnom   ".
				"   ".$ls_union.
				"   ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonomina_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_personal_pensionado($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,
	                                           $as_conceptop2,$as_codubifis,
									           $as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_personal_pensionado
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Mar?a Beatriz Unda
		// Fecha Creaci?n: 29/09/2008 							Fecha ?ltima Modificaci?n :		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_thpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_thpersonalnomina.codubifis='".$as_codubifis."'";
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
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
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

				case "2": // Ordena por C?digo de personal
					$ls_orden=" ORDER BY codper ";
					break;

				case "3": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY apeper ";
					break;

				case "4": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_asignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_asignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_asignacioncargo.codnom ".
				       "           AND sno_thpersonalnomina.codasicar = sno_asignacioncargo.codasicar) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_cargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_cargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_cargo.codnom ".
				       "           AND sno_thpersonalnomina.codcar = sno_cargo.codcar) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_thpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
					  "		  sno_personal.fecingper, sno_thpersonalnomina.fecculcontr, sno_thpersonalnomina.fecingper as fecingnom,".
					  "       sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_personal.fecegrper, ".
					  "       sno_personal.fecsitu, sno_personal.fecnacper, ".
					  "		  sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
					  "       sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, MAX(sno_thpersonalnomina.sueper) AS sueper, ".
					  "		  sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
					  "       sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, MAX(sno_thpersonalnomina.codgra) AS codgra, ".
					  "       MAX(sno_personal.nacper) AS nacper,  ".
					  "       MAX(sno_ubicacionfisica.desubifis) AS desubifis,".
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
					  "		  (SELECT SUM(asires) FROM sno_resumen ".
					  "			WHERE sno_resumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_resumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_resumen.codperi = sno_thsalida.codperi) AS totalasignacion, ".
					  "		  (SELECT SUM(dedres + apoempres) FROM sno_resumen ".
					  "			WHERE sno_resumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_resumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_resumen.codperi = sno_thsalida.codperi) AS totaldeduccion, ".
					  "		  (SELECT SUM(apopatres) FROM sno_resumen ".
					  "			WHERE sno_resumen.codemp = sno_thsalida.codemp ".
					  "			 AND sno_resumen.codnom = sno_thsalida.codnom ".
					  "			 AND sno_resumen.codperi = sno_thsalida.codperi) AS totalaporte, ".
					  "		 (SELECT sno_componente.descom FROM sno_componente ".
					  "        WHERE sno_componente.codemp='".$this->ls_codemp."'".
					  "          AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
					  "		 (SELECT sno_rango.desran FROM sno_rango ".
					  "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
					  "          AND sno_rango.codcom=sno_personal.codcom".
					  "          AND sno_rango.codran=sno_personal.codran) AS denran, sno_personal.situacion, ".
					  "        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				      "            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
  					  $ls_descar.
					  "  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin, sno_ubicacionfisica ".
					  " WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "	  AND sno_thpersonalnomina.staper = '2' ".
					  "   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_thsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
					  "   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
					  "   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
					  "   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_thpersonalnomina.codper ".
					  "   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
					  "   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
					  "   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
					  "   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
					  "   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
					  "   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
					  "   AND sno_ubicacionfisica.codemp = sno_thpersonalnomina.codemp ".
					  "	  AND sno_ubicacionfisica.codubifis = sno_thpersonalnomina.codubifis ".
					  " GROUP BY sno_thpersonalnomina.codemp, sno_thsalida.codemp, sno_thpersonalnomina.codnom, sno_thsalida.codnom, sno_thsalida.codperi, sno_thpersonalnomina.codper, ".
					  "		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "        sno_thpersonalnomina.fecculcontr, sno_thpersonalnomina.fecingper, ".
					  "		   sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.desuniadm, ".
					  "		   sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
					  "        sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, sno_thpersonalnomina.codcar, sno_thpersonalnomina.codasicar, ".
					  "		   sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
					  "    	   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_ubicacionfisica.codpai, ".
					  "        sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar, ".
					  "        sno_personal.codcom,sno_personal.codran, sno_personal.cauegrper, sno_personal.codcausa,".
					  "        sno_personal.fecegrper, sno_personal.fecsitu, sno_personal.fecnacper ";
		}
		$ls_sql="SELECT sno_thpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".
				"		sno_personal.fecingper, sno_thpersonalnomina.fecculcontr, sno_thpersonalnomina.fecingper as fecingnom, ".
				"       sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_personal.fecegrper, sno_personal.fecsitu, ".
				"       sno_personal.fecnacper, ".
				"		sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
				"       sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, MAX(sno_thpersonalnomina.sueper) AS sueper, sno_thunidadadmin.minorguniadm, ".
				"		sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, ".
				"       MAX(sno_thpersonalnomina.codgra) AS codgra, MAX(sno_personal.nacper) AS nacper, ".
				"       MAX(sno_ubicacionfisica.desubifis) AS desubifis, ".
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
				"		  (SELECT SUM(asires) FROM sno_resumen ".
				"			WHERE sno_resumen.codemp = sno_thsalida.codemp ".
				"			 AND sno_resumen.codnom = sno_thsalida.codnom ".
				"			 AND sno_resumen.codperi = sno_thsalida.codperi) AS totalasignacion, ".
				"		  (SELECT SUM(dedres + apoempres) FROM sno_resumen ".
				"			WHERE sno_resumen.codemp = sno_thsalida.codemp ".
				"			 AND sno_resumen.codnom = sno_thsalida.codnom ".
				"			 AND sno_resumen.codperi = sno_thsalida.codperi) AS totaldeduccion, ".
				"		  (SELECT SUM(apopatres) FROM sno_resumen ".
				"			WHERE sno_resumen.codemp = sno_thsalida.codemp ".
				"			 AND sno_resumen.codnom = sno_thsalida.codnom ".
				"			 AND sno_resumen.codperi = sno_thsalida.codperi) AS totalaporte, ".
			    "		 (SELECT sno_componente.descom FROM sno_componente ".
				"          WHERE sno_componente.codemp='".$this->ls_codemp."'".
				"            AND sno_componente.codcom=sno_personal.codcom) AS dencom, ".
				"		 (SELECT sno_rango.desran FROM sno_rango ".
			    "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				"          AND sno_rango.codcom=sno_personal.codcom".
				"          AND sno_rango.codran=sno_personal.codran) AS denran, sno_personal.situacion, ".
				"        (SELECT sno_causales.dencausa FROM sno_causales WHERE sno_causales.codemp='".$this->ls_codemp."'".
				"            AND sno_causales.codcausa=sno_personal.codcausa) AS dencausa, ".
				$ls_descar.
				"  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				"   AND sno_ubicacionfisica.codemp = sno_thpersonalnomina.codemp ".				
				"	AND sno_ubicacionfisica.codubifis = sno_thpersonalnomina.codubifis ".
				"   AND sno_personal.cedper NOT IN (SELECT sno_beneficiario.cedben FROM sno_beneficiario ".
				"                                    WHERE sno_beneficiario.codemp='".$this->ls_codemp."')".
				" GROUP BY sno_thpersonalnomina.codemp, sno_thsalida.codemp, sno_thpersonalnomina.codnom, sno_thsalida.codnom, sno_thsalida.codperi, sno_thpersonalnomina.codper, ".
				"		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"          sno_thpersonalnomina.fecculcontr, sno_thpersonalnomina.fecingper, ".
				"		   sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.desuniadm, ".
				"		   sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
				"          sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, sno_thpersonalnomina.codcar, sno_thpersonalnomina.codasicar, ".
				"		   sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
			    "    	   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_ubicacionfisica.codpai,  ".
				"          sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar,".
				"          sno_personal.codcom,sno_personal.codran, sno_personal.codcausa, ".
				"          sno_personal.fecegrper, sno_personal.situacion, sno_personal.fecsitu, sno_personal.fecnacper ".
				"   ".$ls_union.
				"   ".$ls_orden;  
		$this->rs_data=$this->io_sql->select($ls_sql);
		
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonomina_personal_pensionado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_personal_pensionado
	//-----------------------------------------------------------------------------------------------------------------------------------//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_conceptopersonal($as_codper,$as_conceptocero,$as_tituloconcepto,$as_conceptoreporte,$as_conceptop2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C?digo del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos en cero
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t?tulo del concepto ? el nombre
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados al personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/02/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_thconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_thconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".					  
					  "SELECT sno_thconcepto.codconc, ".$ls_campo." as nomcon, sno_thsalida.valsal, sno_thsalida.tipsal, sno_thconcepto.frevarcon, sno_thconcepto.repconsunicon,sno_thconcepto.consunicon, ".
					  "		(SELECT moncon FROM sno_thconstantepersonal ".
					  "		  WHERE sno_thconcepto.repconsunicon='1' ".
				      "			AND sno_thconstantepersonal.codper = '".$as_codper."' ".
				      "			AND sno_thconstantepersonal.codemp = sno_thconcepto.codemp ".
				      "			AND sno_thconstantepersonal.codnom = sno_thconcepto.codnom ".
				      "         AND sno_thconstantepersonal.anocur = sno_thconcepto.anocur ".
				      "         AND sno_thconstantepersonal.codperi = sno_thconcepto.codperi ".
				      "			AND sno_thconstantepersonal.codcons = sno_thconcepto.consunicon ) AS unidad ".
				      "  FROM sno_thsalida, sno_thconcepto, sno_thpersonalnomina ".
	 	 		      " WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				      "   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				      "   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				      "   AND sno_thsalida.codperi='".$this->ls_peractnom."'".
				      "   AND sno_thsalida.codper='".$as_codper."'".
				      "   AND sno_thsalida.codconc='".$ls_vac_codconvac."'".
				      "   AND sno_thpersonalnomina.staper = '2' ".
				      "   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				      "   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				      "   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				      "   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				      "   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
					  "   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
					  "   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
					  "   AND sno_thsalida.codper = sno_thpersonalnomina.codper ";
		}
		$ls_sql="SELECT sno_thconcepto.codconc, ".$ls_campo." as nomcon, sno_thsalida.valsal, sno_thsalida.tipsal, sno_thconcepto.frevarcon, sno_thconcepto.repconsunicon,sno_thconcepto.consunicon, ".
			    "		(SELECT moncon FROM sno_thconstantepersonal ".
			    "		  WHERE sno_thconcepto.repconsunicon='1' ".
			    "			AND sno_thconstantepersonal.codper = '".$as_codper."' ".
			    "			AND sno_thconstantepersonal.codemp = sno_thconcepto.codemp ".
			    "			AND sno_thconstantepersonal.codnom = sno_thconcepto.codnom ".
			    "           AND sno_thconstantepersonal.anocur = sno_thconcepto.anocur ".
			    "           AND sno_thconstantepersonal.codperi = sno_thconcepto.codperi ".
			    "			AND sno_thconstantepersonal.codcons = sno_thconcepto.consunicon ) AS unidad ".
				"  FROM sno_thsalida, sno_thconcepto ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."'".
				"   AND sno_thsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc, tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_concepto_excel($as_tituloconcepto,$as_sigcon)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_concepto_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C?digo del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t?tulo del concepto ? el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados al personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/02/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "titcon";
		}
		$ls_sql="SELECT codconc, ".$ls_campo." as nomcon, cueprecon, cueconcon, cueconpatcon, cueprepatcon ".
				"  FROM sno_thconcepto ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   ".$as_sigcon." ".
				"   AND codconc IN (SELECT codconc FROM sno_thsalida WHERE codemp='".$this->ls_codemp."' AND codnom='".$this->ls_codnom."')".
				" ORDER BY codconc ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonomina_concepto_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_conceptopersonal_excel($as_codper,$as_tituloconcepto,$as_tipsal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_conceptopersonal_excel
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C?digo del personal que se desea buscar la salida
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t?tulo del concepto ? el nombre
		//	  			   as_tipsal // Tipo de salida que voy a reportar
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados al personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/02/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->DS_detalle->reset_ds();
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_thconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_thconcepto.titcon";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_thconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_thsalida.valsal) as valsal, MAX(sno_thsalida.tipsal) AS tipsal ".
					  "  FROM sno_thsalida, sno_thconcepto, sno_thpersonalnomina ".
					  " WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
					  "   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_thsalida.codperi='".$this->ls_peractnom."'".
					  "   AND sno_thsalida.codper='".$as_codper."'".
					  "   AND sno_thsalida.codconc='".$ls_vac_codconvac."'".
					  "   AND sno_thpersonalnomina.staper = '2' ".
					  "   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
					  "   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
					  "   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
					  "   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
					  "   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
					  "   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
					  "   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
					  "   AND sno_thsalida.anocur = sno_thpersonalnomina.anocur ".
					  "   AND sno_thsalida.codperi = sno_thpersonalnomina.codperi ".
					  "   AND sno_thsalida.codper = sno_thpersonalnomina.codper ".
					  " GROUP BY sno_thconcepto.codconc ";
		}
		$ls_sql="SELECT sno_thconcepto.codconc, MAX(".$ls_campo.") as nomcon, SUM(sno_thsalida.valsal) as valsal, MAX(sno_thsalida.tipsal) AS tipsal ".
				"  FROM sno_thconcepto, sno_thsalida ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."'".
				"   AND sno_thsalida.codper='".$as_codper."'".
				"   ".$as_tipsal.
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				" GROUP BY sno_thconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc, tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonomina_conceptopersonal_excel ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonomina_conceptopersonal_excel
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonomina_prestamoamortizado($as_codper,$as_concepto,$as_valor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonomina_prestamoamortizado
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codper // C?digo del personal que se desea buscar el prestamo
		//	  			   as_concepto // c?digo del concepto 
		//	  			   as_valor // Valor del Amortizado
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los prestamos asociados a estas personas
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/02/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$as_valor="";
		$lb_valido=true;
		$ls_sql="SELECT monamopre ".
				"  FROM sno_thprestamos ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND anocur='".$this->ls_anocurnom."' ".
				"   AND codperi='".$this->ls_peractnom."'".
				"   AND codconc='".$as_concepto."' ".				
				"   AND codper='".$as_codper."'".
				"   AND stapre=1";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonomina_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_total=0;
			$lb_entro=false;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_total=$ls_total+$row["monamopre"];
				$lb_entro=true;
			}
			if($lb_entro)
			{
				$as_valor=number_format($ls_total,2,",",".");
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_pagonomina_prestamoamortizado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_personal($as_codperdes,$as_codperhas,$as_coduniadmdes,$as_coduniadmhas,$as_conceptocero,$as_conceptop2,$as_conceptoreporte,
									$as_codubifis,$as_codpai,$as_codest,$as_codmun,$as_codpar,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibopago_personal
		//         Access: public (desde la clase sigesp_sno_r_recibopago)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // C?digo de la unidad administrativa	  
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos de tipo reporte
		//	  			   as_orden // Orde a mostrar en el reporte		  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 05/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."AND sno_thsalida.valsal<>0 ";
		}
		if((!empty($as_coduniadmdes))&&(!empty($as_coduniadmhas)))
		{
			$ls_criterio=$ls_criterio."   AND sno_thpersonalnomina.minorguniadm BETWEEN '".substr($as_coduniadmdes,0,4)."' AND '".substr($as_coduniadmhas,0,4)."' ";
			$ls_criterio=$ls_criterio."   AND sno_thpersonalnomina.ofiuniadm BETWEEN '".substr($as_coduniadmdes,5,2)."' AND '".substr($as_coduniadmhas,5,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_thpersonalnomina.uniuniadm BETWEEN '".substr($as_coduniadmdes,8,2)."' AND '".substr($as_coduniadmhas,8,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_thpersonalnomina.depuniadm BETWEEN '".substr($as_coduniadmdes,11,2)."' AND '".substr($as_coduniadmhas,11,2)."' ";
			$ls_criterio=$ls_criterio."   AND sno_thpersonalnomina.prouniadm BETWEEN '".substr($as_coduniadmdes,14,2)."' AND '".substr($as_coduniadmhas,14,2)."' ";
		}
		if(!empty($as_conceptop2))
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"  	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
		}
		else
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"  	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
			}
		}
		if(!empty($as_codubifis))
		{
			$ls_criterio= $ls_criterio." AND sno_thpersonalnomina.codubifis='".$as_codubifis."'";
			$ls_criteriounion = $ls_criteriounion." AND sno_thpersonalnomina.codubifis='".$as_codubifis."'";
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
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as descar ";
					   
			$ls_codcar="       (SELECT codasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as codcar, ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar) as descar ";
					   
			$ls_codcar="       (SELECT codcar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar) as codcar, ";
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper,  sno_personal.rifper, MAX(sno_thpersonalnomina.fecingper) as fecingnom,  ".
		        "  sno_personal.nacper, sno_personal.fecegrper, sno_personal.fecleypen,sno_personal.codorg, sno_thpersonalnomina.obsrecper, ".
				"		sno_thpersonalnomina.codcueban, sno_thpersonalnomina.tipcuebanper, sno_personal.fecingper, sum(sno_thsalida.valsal) as total, sno_thunidadadmin.desuniadm,".
				"		sno_thunidadadmin.minorguniadm,sno_thunidadadmin.ofiuniadm,sno_thunidadadmin.uniuniadm,sno_thunidadadmin.depuniadm,".
				"		sno_thunidadadmin.prouniadm, MAX(sno_thpersonalnomina.sueper) AS sueper,  MAX(sno_thpersonalnomina.pagbanper) AS pagbanper, MAX(sno_thpersonalnomina.salnorper) AS salnorper, ".
				"		MAX(sno_thpersonalnomina.pagefeper) AS pagefeper, MAX(sno_ubicacionfisica.desubifis) AS desubifis, MAX(sno_thpersonalnomina.fecculcontr) AS fecculcontr, ".
				"		MAX(sno_thpersonalnomina.descasicar) AS descasicar,MAX(sno_thpersonalnomina.sueintper) AS sueintper, MAX(sno_thpersonalnomina.sueproper) as sueproper, ".
				"		  (SELECT tipnom FROM sno_thnomina ".
				"			WHERE sno_thpersonalnomina.codemp = sno_thnomina.codemp ".
				"			 AND sno_thpersonalnomina.codnom = sno_thnomina.codnom  ".
				"			 AND sno_thpersonalnomina.anocur = sno_thnomina.anocurnom  ".
				"			 AND sno_thpersonalnomina.codperi = sno_thnomina.peractnom) AS tiponom, ".
				"		  (SELECT ctnom FROM sno_thnomina ".
				"			WHERE sno_thpersonalnomina.codemp = sno_thnomina.codemp ".
				"			 AND sno_thpersonalnomina.codnom = sno_thnomina.codnom  ".
				"			 AND sno_thpersonalnomina.anocur = sno_thnomina.anocurnom  ".
				"			 AND sno_thpersonalnomina.codperi = sno_thnomina.peractnom) AS ctnom, ".
				"		  (SELECT suemin FROM sno_thclasificacionobrero ".
				"			WHERE sno_thclasificacionobrero.codemp = sno_thpersonalnomina.codemp ".
				"			 AND sno_thclasificacionobrero.codnom = sno_thpersonalnomina.codnom  ".
				"			 AND sno_thclasificacionobrero.anocur = sno_thpersonalnomina.anocur  ".
				"			 AND sno_thclasificacionobrero.codperi = sno_thpersonalnomina.codperi  ".
				"			 AND sno_thclasificacionobrero.grado = sno_thpersonalnomina.grado) AS sueobr, ".
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
				"		(SELECT nomban FROM scb_banco ".
				"		   WHERE scb_banco.codemp = sno_thpersonalnomina.codemp ".
				" 			 AND scb_banco.codban = sno_thpersonalnomina.codban) AS banco,".
				"		(SELECT  nomage FROM scb_agencias ".
				"		   WHERE scb_agencias.codemp = sno_thpersonalnomina.codemp ".
				" 			 AND scb_agencias.codban = sno_thpersonalnomina.codban ".
				"            AND scb_agencias.codage = sno_thpersonalnomina.codage) AS agencia,".
				"       (SELECT sno_categoria_rango.descat FROM sno_rango, sno_categoria_rango   ".
                "         WHERE sno_rango.codemp=sno_personal.codemp                             ".
                "           AND sno_rango.codcom=sno_personal.codcom                             ".
                "     AND sno_rango.codran=sno_personal.codran                                   ".
                "     AND sno_categoria_rango.codcat=sno_rango.codcat) AS descat,                ".
				"		(SELECT MAX(denestpro2) FROM spg_ep2 ".
				"		WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"		AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"		AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"		AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"		AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"		AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"		AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"		AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				"		AND sno_thunidadadmin.codestpro1 = spg_ep2.codestpro1 ".
				"		AND sno_thunidadadmin.codestpro2 = spg_ep2.codestpro2) AS denestpro2, ".
				$ls_codcar.$ls_descar.
				"  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin, sno_ubicacionfisica ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_thsalida.tipsal<>'P2' AND  sno_thsalida.tipsal<>'V4' AND sno_thsalida.tipsal<>'W4') ".
				"   ".$ls_criterio." ".
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_ubicacionfisica.codemp ".
				"   AND sno_thpersonalnomina.codubifis = sno_ubicacionfisica.codubifis ".
				"   AND sno_thpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_thpersonalnomina.codper = sno_personal.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thunidadadmin.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				" GROUP BY sno_thpersonalnomina.codemp, sno_thpersonalnomina.codnom, sno_thpersonalnomina.anocur, sno_thpersonalnomina.codperi, sno_personal.codemp,sno_personal.codcom, sno_personal.codran, ".
				"		   sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.rifper, ".
				"		   sno_personal.nacper,sno_personal.fecingper, sno_personal.fecegrper, sno_personal.fecleypen, sno_thpersonalnomina.codcueban, sno_thpersonalnomina.tipcuebanper, sno_personal.fecingper, ".
				"		   sno_thunidadadmin.desuniadm, sno_thpersonalnomina.codasicar, sno_thpersonalnomina.codcar, ".
				"		   sno_thpersonalnomina.codban, sno_thunidadadmin.minorguniadm,sno_thunidadadmin.ofiuniadm, ".
				"		   sno_thunidadadmin.uniuniadm,sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_ubicacionfisica.codpai,  ".
				"          sno_ubicacionfisica.codest,sno_ubicacionfisica.codmun,sno_ubicacionfisica.codpar,sno_thpersonalnomina.codage,sno_personal.codorg,sno_thpersonalnomina.grado, sno_thpersonalnomina.obsrecper, ".
				"		   sno_thunidadadmin.codemp,sno_thpersonalnomina.minorguniadm,sno_thpersonalnomina.ofiuniadm,sno_thpersonalnomina.uniuniadm, ".
				"		   sno_thpersonalnomina.depuniadm,sno_thpersonalnomina.prouniadm,sno_thunidadadmin.codestpro1,sno_thunidadadmin.codestpro2 ".
				"   ".$ls_orden; 				
		$this->rs_data=$this->io_sql->select($ls_sql);		
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_recibopago_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibopago_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_conceptopersonal($as_codper,$as_conceptocero,$as_conceptop2,$as_conceptoreporte,$as_tituloconcepto,$as_quincena)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibopago_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago)  
		//	    Arguments: as_codper // C?digo del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos de tipo reporte
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar los t?tulos de los conceptos
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados al personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 05/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_thconcepto.nomcon";
		$ls_campomonto=" sno_thsalida.valsal ";
		if(($_SESSION["la_nomina"]["divcon"]==1)&&($_SESSION["la_nomina"]["tippernom"]==2))
		{
			if($as_quincena!="3")
			{
				$ls_criterio = $ls_criterio."   AND (sno_thconcepto.quirepcon = '".$as_quincena."' ".
											"	 OR  sno_thconcepto.quirepcon = '3')";
				switch($as_quincena)
				{
					case "1":
						$ls_campomonto=" sno_thsalida.priquisal as valsal ";
						break;
					case "2":
						$ls_campomonto=" sno_thsalida.segquisal as valsal ";
						break;
				}
			}
		}
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_thconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_conceptop2))
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"  	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
		}
		else
		{
			if(!empty($as_conceptoreporte))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"  	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
			}
		}
		$ls_sql="SELECT sno_thconcepto.codconc, ".$ls_campo." as nomcon, ".$ls_campomonto.", sno_thsalida.tipsal, abs(sno_thconceptopersonal.acuemp) AS acuemp, ".
				"		abs(sno_thconceptopersonal.acupat) AS acupat , sno_thconcepto.repacucon,  sno_thconcepto.repconsunicon, sno_thconcepto.consunicon, ".
				"		(SELECT moncon FROM sno_thconstantepersonal ".
				"		  WHERE sno_thconcepto.repconsunicon='1' ".
				"			AND sno_thconstantepersonal.codper = '".$as_codper."' ".
				"			AND sno_thconstantepersonal.codemp = sno_thconcepto.codemp ".
				"			AND sno_thconstantepersonal.codnom = sno_thconcepto.codnom ".
				"			AND sno_thconstantepersonal.anocur = sno_thconcepto.anocur ".
				"			AND sno_thconstantepersonal.codperi = sno_thconcepto.codperi ".
				"			AND sno_thconstantepersonal.codcons = sno_thconcepto.consunicon ) AS unidad ".
				"  FROM sno_thsalida, sno_thconcepto, sno_thconceptopersonal ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."'".
				"   AND sno_thsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				"   AND sno_thsalida.codemp = sno_thconceptopersonal.codemp ".
				"   AND sno_thsalida.codnom = sno_thconceptopersonal.codnom ".
				"   AND sno_thsalida.anocur = sno_thconceptopersonal.anocur ".
				"   AND sno_thsalida.codperi = sno_thconceptopersonal.codperi ".
				"   AND sno_thsalida.codconc = sno_thconceptopersonal.codconc ".
				"   AND sno_thsalida.codper = sno_thconceptopersonal.codper ".
				" ORDER BY sno_thconcepto.codconc, sno_thsalida.tipsal ";
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_recibopago_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibopago_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_recibopago_informacionprestamos($as_codper,$la_prestamo)
	{
		$lb_valido=false;
		$la_prestamo[1]=array('nombre'=>'','prestamo'=>0,'abono'=>0,'saldo'=>0);
		$ls_sql="SELECT sno_thprestamos.codconc, MAX(sno_thconcepto.nomcon) AS nomcon, SUM(sno_thprestamos.monpre) as monto, SUM(sno_thprestamos.monamopre) as monamopre, ".
				"       (SELECT SUM(valsal) ".
				"          FROM sno_thsalida ".
				"         WHERE sno_thsalida.codemp = sno_thprestamos.codemp ".
				"           AND sno_thsalida.codnom = sno_thprestamos.codnom ".
				"           AND sno_thsalida.anocur = sno_thprestamos.anocur ".
				"           AND sno_thsalida.codperi = sno_thprestamos.codperi ".				
				"           AND sno_thsalida.codper = sno_thprestamos.codper ".
				"           AND sno_thsalida.codconc = sno_thprestamos.codconc ) AS actual ".
				"  FROM sno_thprestamos ".
				" INNER JOIN sno_thconcepto  ".
				"    ON sno_thprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_thprestamos.codnom = '".$this->ls_codnom."'  ".
				"   AND sno_thprestamos.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thprestamos.codperi='".$this->ls_peractnom."'".
				"   AND sno_thprestamos.codper='".$as_codper."' ".
				"   AND sno_thprestamos.stapre = '1' ".
				"   AND sno_thprestamos.codemp = sno_thconcepto.codemp".
				"   AND sno_thprestamos.codnom = sno_thconcepto.codnom  ".
				"   AND sno_thprestamos.anocur = sno_thconcepto.anocur  ".
				"   AND sno_thprestamos.codperi = sno_thconcepto.codperi  ".
				"   AND sno_thprestamos.codconc = sno_thconcepto.codconc  ".
				" GROUP BY sno_thprestamos.codemp, sno_thprestamos.codnom, sno_thprestamos.anocur, sno_thprestamos.codperi, sno_thprestamos.codper, sno_thprestamos.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_recibopago_informacionprestamos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_listadoconcepto_conceptos($as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,$as_coduniadm,$as_conceptocero,
										  $as_subnomdes,$as_subnomhas,$as_codente)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoconcepto_conceptos
		//         Access: public (desde la clase sigesp_sno_rpp_listadoconceptos)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // C?digo de la unidad administrativa que se desea filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos que se calcularon en la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/02/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thconcepto.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thconcepto.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		if(!empty($as_codente))
		{
			$ls_criterio= $ls_criterio." AND sno_thconcepto.codente='".$as_codente."'";
		}
		$ls_sql="SELECT sno_thconcepto.codconc, sno_thconcepto.nomcon, count(sno_thsalida.codper) as total, sum(sno_thsalida.valsal) as monto ".
				"  FROM sno_thpersonalnomina, sno_thsalida, sno_thconcepto, sno_thresumen ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thresumen.monnetres >= 0 ".
				"   AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
				"		 sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
				"		 sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3') ".
				"   ".$ls_criterio." ".
				"   AND sno_thsalida.codemp = sno_thresumen.codemp ".
				"   AND sno_thsalida.codnom = sno_thresumen.codnom ".
				"   AND sno_thsalida.anocur = sno_thresumen.anocur ".
				"   AND sno_thsalida.codperi = sno_thresumen.codperi ".
				"   AND sno_thsalida.codper = sno_thresumen.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				" GROUP BY sno_thconcepto.codconc, sno_thconcepto.nomcon ".
				" ORDER BY sno_thconcepto.codconc ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoconcepto_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadoconcepto_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoconcepto_personalconcepto($as_codconc,$as_codperdes,$as_codperhas,$as_conceptocero,$as_coduniadm,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoconcepto_personalconcepto
		//		   Access: public (desde la clase sigesp_sno_rpp_listadonomina)  
		//	    Arguments: as_codconc // C?digo del concepto del que se desea busca el personal
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal asociado al concepto que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/02/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula de personal
				$ls_orden="ORDER BY sno_componente.codcom, sno_rango.codran, sno_personal.cedper ";
				break;
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as descar, ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar) as descar, ";
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, sno_thsalida.valsal, ".$ls_descar.
				"       sno_componente.descom, sno_rango.desran,                                             ".
				"		(SELECT moncon FROM sno_thconstantepersonal, sno_thconcepto ".
				"		  WHERE sno_thconcepto.repconsunicon='1' ".
				"           AND sno_thconcepto.codemp = sno_thsalida.codemp ".
				"           AND sno_thconcepto.codnom = sno_thsalida.codnom ".
				"           AND sno_thconcepto.anocur = sno_thsalida.anocur ".
				"           AND sno_thconcepto.codperi = sno_thsalida.codperi ".
				"           AND sno_thconcepto.codconc = sno_thsalida.codconc ".
				"			AND sno_thconstantepersonal.codemp = sno_thsalida.codemp ".
				"			AND sno_thconstantepersonal.codnom = sno_thsalida.codnom ".
				"			AND sno_thconstantepersonal.anocur = sno_thsalida.anocur ".
				"			AND sno_thconstantepersonal.codperi = sno_thsalida.codperi ".
				"			AND sno_thconstantepersonal.codper = sno_thsalida.codper".
				"			AND sno_thconstantepersonal.codemp = sno_thconcepto.codemp ".
				"			AND sno_thconstantepersonal.codnom = sno_thconcepto.codnom ".
				"			AND sno_thconstantepersonal.anocur = sno_thconcepto.anocur ".
				"			AND sno_thconstantepersonal.codperi = sno_thconcepto.codperi ".
				"			AND sno_thconstantepersonal.codcons = sno_thconcepto.consunicon ) AS unidad ".
				"   FROM sno_personal                                                                       ".
				"   JOIN sno_thpersonalnomina ON (sno_thpersonalnomina.codemp=sno_personal.codemp           ".
				"							 AND  sno_thpersonalnomina.codper=sno_personal.codper)          ".
				"   JOIN sno_thsalida ON (sno_thpersonalnomina.codemp = sno_thsalida.codemp                 ".        
				"				     AND sno_thpersonalnomina.codnom = sno_thsalida.codnom                  ".
				"			         AND sno_thpersonalnomina.anocur = sno_thsalida.anocur                  ".
				"			         AND sno_thpersonalnomina.codperi = sno_thsalida.codperi                ".
				"			         AND sno_thpersonalnomina.codper = sno_thsalida.codper)                 ".
				"   LEFT JOIN sno_componente ON (sno_componente.codemp=sno_personal.codemp                  ".
				"						    AND  sno_componente.codcom=sno_personal.codcom)                 ".
				"   LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp                            ".
				"					   AND sno_rango.codcom=sno_personal.codcom                             ".
				"					   AND sno_rango.codran=sno_personal.codran)                            ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thsalida.codconc='".$as_codconc."' ".
				"   AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
				"		 sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
				"		 sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3') ".
				"   ".$ls_criterio.$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoconcepto_personalconcepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;

	}// end function uf_listadoconcepto_personalconcepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalcheque_unidad($as_codban,$as_suspendidos,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalcheque_unidad
		//		   Access: public (desde la clase sigesp_sno_rpp_listadopersonalcheque)  
		//	    Arguments: as_codban // C?digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ? solo los activos
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que cobran con cheque
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 02/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_thpersonalnomina.staper='1' OR sno_thpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
				"   	sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_thunidadadmin.desuniadm ".
				"  FROM sno_thpersonalnomina, sno_thunidadadmin, sno_thresumen ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thpersonalnomina.pagefeper=1 ".
				"   AND sno_thpersonalnomina.pagbanper=0 ".
				"   AND sno_thpersonalnomina.pagtaqper=0 ".
				"   AND sno_thresumen.monnetres > 0 ".
				"     ".$ls_criterio.
				"	AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thresumen.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thresumen.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thunidadadmin.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				" GROUP BY sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
				"   	    sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_thunidadadmin.desuniadm ".
				" ORDER BY sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
				"   	    sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadopersonalcheque_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadopersonalcheque_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadopersonalcheque_personal($as_codban,$as_minorguniadm,$as_ofiuniadm,$as_uniuniadm,$as_depuniadm,
											   $as_prouniadm,$as_suspendidos,$as_quincena,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadopersonalcheque_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadopersonalcheque)  
		//	    Arguments: as_codban // C?digo del banco del que se desea busca el personal
		//	    		   as_minorguniadm // C?digo del Ministerio ? Organismo
		//	    		   as_ofiuniadm // C?digo de la Oficina
		//	    		   as_uniuniadm // C?digo de la Unidad
		//	    		   as_depuniadm // C?digo del departamento
		//	    		   as_prouniadm // C?digo del programa
		//	    		   as_suspendidos // si se busca a toto del personal ? solo los activos
		//	    		   as_quincena // quincena que se quiere mostrar
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que tienen asociado el banco y la unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 02/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_thresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_thresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_thresumen.monnetres as monnetres";
				break;
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_thpersonalnomina.staper='1' OR sno_thpersonalnomina.staper='2')";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".$ls_monto." ".
				"  FROM sno_personal, sno_thpersonalnomina, sno_thresumen ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thpersonalnomina.pagefeper=1 ".
				"   AND sno_thpersonalnomina.pagbanper=0 ".
				"   AND sno_thpersonalnomina.pagtaqper=0 ".
				"   AND sno_thresumen.monnetres > 0 ".
				"	AND sno_thpersonalnomina.minorguniadm = '".$as_minorguniadm."' ".
				"   AND sno_thpersonalnomina.ofiuniadm = '".$as_ofiuniadm."' ".
				"   AND sno_thpersonalnomina.uniuniadm = '".$as_uniuniadm."' ".
				"   AND sno_thpersonalnomina.depuniadm = '".$as_depuniadm."' ".
				"   AND sno_thpersonalnomina.prouniadm = '".$as_prouniadm."' ".
				"	".$ls_criterio.
				"	AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thresumen.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thresumen.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_thpersonalnomina.codper ".
				$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadopersonalcheque_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadopersonalcheque_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_banco($as_codban,$as_suspendidos,$as_sc_cuenta,$as_ctaban,$as_subnomdes,$as_subnomhas,$as_codperdes,$as_codperhas,$pago_otros_bancos='')
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_banco
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // C?digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ? solo los activos
		//	    		   as_sc_cuenta // cuenta contable del banco
		//	    		   as_ctaban // cuenta del banco
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del banco seleccionado
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codban) && (empty($pago_otros_bancos) || $pago_otros_bancos===false))
		{
			$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_thpersonalnomina.staper='1' OR sno_thpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		$ls_sql="SELECT scb_banco.codban, scb_banco.nomban ".
				"  FROM sno_thpersonalnomina, sno_thresumen, scb_banco  ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_thpersonalnomina.pagbanper=1 OR sno_thpersonalnomina.pagtaqper=1) ".
				"   AND sno_thresumen.monnetres > 0".
				"   ".$ls_criterio.
				"   AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thresumen.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thresumen.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				"   AND sno_thpersonalnomina.codemp = scb_banco.codemp ".
				"   AND sno_thpersonalnomina.codban = scb_banco.codban ".
				" GROUP BY scb_banco.codban, scb_banco.nomban ".
				" ORDER BY scb_banco.nomban ";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadobanco_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$this->rs_data->EOF)
			{
				$lb_valido=$this->uf_update_banco($as_codban,$as_sc_cuenta,$as_ctaban);	
			}
		}		
		return $lb_valido;
	}// end function uf_listadobanco_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_update_banco($as_codban,$as_sc_cuenta,$as_ctaban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_update_banco
		//		   Access: private
		//	    Arguments: as_codban  // c?digo de cargo
		//	    		   as_sc_cuenta // cuenta contable del banco
		//	    		   as_ctaban // cuenta del banco
		//	      Returns: lb_valido True si se ejecuto el update ? False si hubo error en el update
		//	  Description: Funcion que actualiza si se gener? el listado al banco
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 11/05/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="DELETE ".
				"  FROM sno_banco ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codban='".$as_codban."'";
		$this->io_sql->begin_transaction();
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
 			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_update_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$this->io_sql->rollback();
		}
		else
		{
			$ls_sql="INSERT INTO sno_banco(codemp,codnom,codperi,codban,codcueban,codcuecon) VALUES ('".$this->ls_codemp."',".
					"'".$this->ls_codnom."','".$this->ls_peractnom."','".$as_codban."','".$as_ctaban."','".$as_sc_cuenta."')";
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$lb_valido=false;
				$this->io_mensajes->message("CLASE->Report M?TODO->uf_update_banco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$this->io_sql->rollback();
			}
			else
			{
				$this->io_sql->commit();
			}
		}
		return $lb_valido;
	}// end function uf_update_banco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobanco_personal($as_codban,$as_suspendidos,$as_tipcueban,$as_quincena,$as_subnomdes,$as_subnomhas,$as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobanco_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // C?digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ? solo los activos
		//	    		   as_tipcueban // tipo de cuenta bancaria (Ahorro,  Corriente, Activos liquidos)
		//	  			   as_quincena // Quincena para el cual se quiere filtrar
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_thresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_thresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_thresumen.monnetres as monnetres";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_thpersonalnomina.staper='1' OR sno_thpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_tipcueban)
		{
			case "A": // Cuenta de Ahorro
				$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.tipcuebanper='A' ";
				break;
				
			case "C": // Cuenta corriente
				$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.tipcuebanper='C' ";
				break;

			case "L": // Cuenta Activos L?quidos
				$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.tipcuebanper='L' ";
				break;
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
				
			case "5": // Ordena por Rango del Personal
				$ls_orden="ORDER BY  sno_personal.codran, sno_personal.codcom DESC";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".$ls_monto.", sno_thpersonalnomina.codcueban, sno_personal.codran,sno_thpersonalnomina.fecingper, ".
				"		sno_personal.fecingper AS fecingins, ".
				 "		 (SELECT sno_rango.desran FROM sno_rango ".
				 "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				 "          AND sno_rango.codcom=sno_personal.codcom".
				 "          AND sno_rango.codran=sno_personal.codran) AS denran ".
				"  FROM sno_personal, sno_thpersonalnomina, sno_thresumen  ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thpersonalnomina.pagbanper=1 ".
				"   AND sno_thpersonalnomina.pagefeper=0 ".
				"   AND sno_thpersonalnomina.pagtaqper=0 ".
				"   AND sno_thresumen.monnetres > 0 ".
				"	".$ls_criterio.
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"	AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thresumen.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thresumen.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadobanco_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadobanco_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadobancotaquilla_personal($as_codban,$as_suspendidos,$as_quincena,$as_subnomdes,$as_subnomhas,$as_codperdes,$as_codperhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadobancotaquilla_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadobanco)  
		//	    Arguments: as_codban // C?digo del banco del que se desea busca el personal
		//	    		   as_suspendidos // si se busca a toto del personal ? solo los activos
		//	    		   as_tipcueban // tipo de cuenta bancaria (Ahorro,  Corriente, Activos liquidos)
		//	  			   as_quincena // Quincena para el cual se quiere filtrar
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que tienen asociado el banco 
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_monto="";
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_thresumen.priquires as monnetres";
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_thresumen.segquires as monnetres";
				break;

			case 3: // Mes Completo
				$ls_monto="sno_thresumen.monnetres as monnetres";
				break;
		}
		if(!empty($as_codban))
		{
			$ls_criterio = $ls_criterio." AND sno_thpersonalnomina.codban='".$as_codban."' ";
		}
		if($as_suspendidos=="1") // Mostrar solo el personal suspendido
		{
			$ls_criterio = $ls_criterio." AND (sno_thpersonalnomina.staper='1' OR sno_thpersonalnomina.staper='2')";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."    AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula del Personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
				
			case "5": // Ordena por Rango del Personal
				$ls_orden="ORDER BY  sno_personal.codran, sno_personal.codcom DESC";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, ".$ls_monto.", sno_thpersonalnomina.codcueban  , sno_personal.codran, ".
				 "		 (SELECT sno_rango.desran FROM sno_rango ".
				 "        WHERE sno_rango.codemp='".$this->ls_codemp."'".
				 "          AND sno_rango.codcom=sno_personal.codcom".
				 "          AND sno_rango.codran=sno_personal.codran) AS denran ".
				"  FROM sno_personal, sno_thpersonalnomina, sno_thresumen  ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thpersonalnomina.pagbanper=0 ".
				"   AND sno_thpersonalnomina.pagefeper=0 ".
				"   AND sno_thpersonalnomina.pagtaqper=1 ".
				"   AND sno_thresumen.monnetres > 0 ".
				"	".$ls_criterio.
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"	AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"	AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thresumen.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thresumen.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadobancotaquilla_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_listadobancotaquilla_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_aportepatronal_personal($as_codconc,$as_conceptocero,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_aportepatronal_personal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadonomina)  
		//	    Arguments: as_codconc // C?digo del concepto del que se desea busca el personal
		//	  			   as_conceptocero // concepto cero
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que tienen asociado el concepto	de tipo aporte patronal 
		//				   y se calcul? en la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 19/04/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_group=",";
		if(!empty($as_codconc))
		{
			$ls_criterio = $ls_criterio." AND sno_thsalida.codconc='".$as_codconc."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio." AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_group=",sno_thpersonalnomina.codsubnom,";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_group=",sno_thpersonalnomina.codsubnom,";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_thpersonalnomina.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, count(sno_personal.cedper) as total, sno_thpersonalnomina.fecingper, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_thsalida ".
				"   	  WHERE (sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR sno_thsalida.tipsal='Q1') ".
				$ls_criterio.
				"           AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   		AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   		AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   		AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   		AND sno_thpersonalnomina.codper = sno_thsalida.codper) as personal, ".
				"       (SELECT SUM(valsal) ".
				"		   FROM sno_thsalida ".
				"   	  WHERE (sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR sno_thsalida.tipsal='Q2') ".
				$ls_criterio.
				"           AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   		AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   		AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   		AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   		AND sno_thpersonalnomina.codper = sno_thsalida.codper) as patron ".
				"  FROM sno_personal, sno_thpersonalnomina, sno_thsalida ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				$ls_criterio.
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"	AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"	AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"	AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"	AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"	AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				" GROUP BY sno_thpersonalnomina.codemp, sno_thpersonalnomina.codnom, sno_thpersonalnomina.anocur, sno_thpersonalnomina.codperi ".$ls_group." ".
				"		   sno_thpersonalnomina.codper, sno_personal.cedper, sno_personal.apeper, sno_thpersonalnomina.fecingper, ".
				"		   sno_personal.nomper, sno_thsalida.codemp, sno_thsalida.codnom, sno_thsalida.codperi, sno_thsalida.codper   ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_aportepatronal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_aportepatronal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_resumenconcepto_conceptos($as_codconcdes,$as_codconchas,$as_aportepatronal,$as_conceptocero,$as_subnomdes,$as_subnomhas,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumenconcepto_conceptos
		//         Access: public (desde la clase sigesp_sno_rpp_resumenconceptos)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//				   as_aportepatronal // criterio que me indica si se quiere mostrar el aporte patronal
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos que se calcularon en la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 27/04/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thconcepto.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thconcepto.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_aportepatronal))
		{
			$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
										"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
										"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
										"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4')";
		}
		else
		{
			$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
										"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
										"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
		}
		$ls_sql="SELECT sno_thconcepto.codconc, MAX(sno_thconcepto.nomcon) AS nomcon, sno_thsalida.tipsal, sum(sno_thsalida.valsal) as monto, ".
				"		COUNT(sno_thsalida.codper) AS total, MAX(sno_thconcepto.cueprecon) AS cueprecon, MAX(sno_thconcepto.cueprepatcon) AS cueprepatcon  ".
				"  FROM sno_thsalida, sno_thconcepto, sno_thpersonalnomina ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				"   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thsalida.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thsalida.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thsalida.codper = sno_thpersonalnomina.codper ".
				" GROUP BY sno_thconcepto.codconc, sno_thsalida.tipsal ".
				" ORDER BY sno_thconcepto.codconc, sno_thsalida.tipsal ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_resumenconcepto_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_resumenconcepto_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_resumenconceptounidad_unidad($as_codconcdes,$as_codconchas,$as_coduniadm,$as_conceptocero,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumenconceptounidad_unidad
		//         Access: public (desde la clase sigesp_sno_r_resumenconceptounidad)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//				   as_coduniadm // C?digo de la unidad administrativa 
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las unidades administrativas asociadas a los conceptos	
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 27/04/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thsalida.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thsalida.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_minorguniadm=substr($as_coduniadm,0,4);
			$ls_ofiuniadm=substr($as_coduniadm,5,2);
			$ls_uniuniadm=substr($as_coduniadm,8,2);
			$ls_depuniadm=substr($as_coduniadm,11,2);
			$ls_prouniadm=substr($as_coduniadm,14,2);
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.minorguniadm = '".$ls_minorguniadm."' ".
										"   AND sno_thpersonalnomina.ofiuniadm = '".$ls_ofiuniadm."' ".
										"   AND sno_thpersonalnomina.uniuniadm = '".$ls_uniuniadm."' ".
										"   AND sno_thpersonalnomina.depuniadm = '".$ls_depuniadm."' ".
										"   AND sno_thpersonalnomina.prouniadm = '".$ls_prouniadm."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		$ls_sql="SELECT sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, ".
				"		sno_thunidadadmin.prouniadm, MAX(sno_thunidadadmin.desuniadm) AS desuniadm ".
				"  FROM sno_thsalida, sno_thpersonalnomina, sno_thunidadadmin ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
				"        sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
				"        sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
				"	     sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ".
				"   ".$ls_criterio." ".
				"   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thsalida.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thsalida.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thsalida.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				" GROUP BY sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, ".
				"		sno_thunidadadmin.prouniadm  ".
				" ORDER BY sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, ".
				"		sno_thunidadadmin.prouniadm";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_resumenconceptounidad_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_resumenconceptounidad_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_resumenconceptounidad_concepto($as_codconcdes,$as_codconchas,$as_coduniadm,$as_conceptocero,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_resumenconceptounidad_concepto
		//         Access: public (desde la clase sigesp_sno_r_resumenconceptounidad)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//				   as_coduniadm // C?digo de la unidad administrativa 
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	  			   as_orden // Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados a la unidad administrativa
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 28/04/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_minorguniadm=substr($as_coduniadm,0,4);
		$ls_ofiuniadm=substr($as_coduniadm,5,2);
		$ls_uniuniadm=substr($as_coduniadm,8,2);
		$ls_depuniadm=substr($as_coduniadm,11,2);
		$ls_prouniadm=substr($as_coduniadm,14,2);
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thsalida.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thsalida.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por Tipo de Salida y C?digo del Concepto
				$ls_orden="ORDER BY sno_thsalida.tipsal, sno_thconcepto.codconc ";
				break;

			case "2": // Ordena por Tipo de Salida y descripci?n del Concepto
				$ls_orden="ORDER BY sno_thsalida.tipsal,  sno_thconcepto.nomcon ";
				break;
		}
		$ls_sql="SELECT sno_thconcepto.codconc, MAX(sno_thconcepto.nomcon) AS nomcon, sno_thsalida.tipsal, sum(sno_thsalida.valsal) as monto, ".
				"		COUNT(sno_thsalida.codper) AS total, MAX(sno_thconcepto.cueprecon) AS cueprecon, MAX(sno_thconcepto.cueprepatcon) AS cueprepatcon  ".
				"  FROM sno_thsalida, sno_thpersonalnomina, sno_thconcepto ".
				" WHERE sno_thpersonalnomina.minorguniadm = '".$ls_minorguniadm."' ".
				"   AND sno_thpersonalnomina.ofiuniadm = '".$ls_ofiuniadm."' ".
				"   AND sno_thpersonalnomina.uniuniadm = '".$ls_uniuniadm."' ".
				"   AND sno_thpersonalnomina.depuniadm = '".$ls_depuniadm."' ".
				"   AND sno_thpersonalnomina.prouniadm = '".$ls_prouniadm."' ".
				"   AND sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
				"        sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
				"        sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
				"	     sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ".
				"   ".$ls_criterio." ".
				"   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thsalida.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thsalida.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thsalida.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				" GROUP BY sno_thconcepto.codconc, sno_thsalida.tipsal ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_resumenconceptounidad_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_resumenconceptounidad_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadrenomina_periodo_previo($ai_anoprev,$ai_periprev)
    {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cuadrenomina_periodo_previo
		//		   Access: public
		//	    Arguments: ai_anoprev // A?o Previo
		//                 ai_periprev // periodo previo          
		//	      Returns: lb_valido True si se ejecuto correctamente la funaci?n y false si hubo error
		//	  Description: funci?n que busca la informaci?n del per?odo previo a la n?mina actual
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 02/05/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ai_anoprev=$_SESSION["la_nomina"]["anocurnom"];
		$ai_periprev=(intval($_SESSION["la_nomina"]["peractnom"])-1);
		if($ai_periprev<1)
		{
			$ai_anoprev=(intval($ai_anoprev)-1);
			$ls_sql="SELECT numpernom ".
					"  FROM sno_hnomina ".
					" WHERE codemp='".$this->ls_codemp."' ".
					"   AND codnom='".$this->ls_codnom."' ".
					"   AND anocurnom='".$ai_anoprev."' ";
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->SNO M?TODO->uf_cuadrenomina_periodo_previo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
				$lb_valido=false;
			}
			else
			{
				while($row=$this->io_sql->fetch_row($rs_data))
				{
					$ai_periprev=$row["numpernom"];
				}
				if($ai_periprev<1)
				{
					$ai_periprev="0";
				}
				$this->io_sql->free_result($rs_data);
			}
		}
		$ai_periprev=str_pad($ai_periprev,3,"0",0);
		$arrResultado['ai_anoprev']=$ai_anoprev;
		$arrResultado['ai_periprev']=$ai_periprev;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_cuadrenomina_periodo_previo	
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadrenomina_concepto($as_codconcdes,$as_codconchas,$as_conceptocero,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadrenomina_concepto
		//         Access: public (desde la clase sigesp_sno_r_cuadrenomina)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos que se calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 02/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_hcriterio="";
		$li_anoprev="";
		$li_periprev="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thsalida.codconc>='".$as_codconcdes."'";
			$ls_hcriterio= "AND sno_hsalida.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thsalida.codconc<='".$as_codconchas."'";
			$ls_hcriterio= $ls_hcriterio."   AND sno_hsalida.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
			$ls_hcriterio = $ls_hcriterio."   AND sno_hsalida.valsal<>0 ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_hcriterio= $ls_hcriterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_hcriterio= $ls_hcriterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$arrResultado=$this->uf_cuadrenomina_periodo_previo($li_anoprev,$li_periprev);
		$li_anoprev=$arrResultado['ai_anoprev'];
		$li_periprev=$arrResultado['ai_periprev'];
		$lb_valido=$arrResultado['lb_valido'];
		$ls_sql="SELECT sno_thsalida.codconc, sno_thconcepto.nomcon, sno_thsalida.tipsal, sum(COALESCE(sno_thsalida.valsal,0)) as actual, ".
				"		COALESCE((SELECT sum(COALESCE(sno_hsalida.valsal,0)) as previo ".
				"		   			FROM sno_hsalida,sno_hpersonalnomina ".
				"		 		   WHERE sno_hsalida.codemp='".$this->ls_codemp."' ".
				"					 AND sno_hsalida.codnom='".$this->ls_codnom."' ".
				"					 AND sno_hsalida.anocur='".$li_anoprev."' ".
				"					 AND sno_hsalida.codperi='".$li_periprev."' ".
				"   				 AND (sno_hsalida.tipsal='A' OR  sno_hsalida.tipsal='V1' OR sno_hsalida.tipsal='W1')".
				"					 ".$ls_hcriterio.
				"   				 AND sno_hsalida.codconc=sno_thsalida.codconc ".
				"   				 AND sno_hsalida.tipsal=sno_thsalida.tipsal ".
				"   				 AND sno_hsalida.codemp = sno_hpersonalnomina.codemp ".
				"  					 AND sno_hsalida.codnom = sno_hpersonalnomina.codnom ".
				"  					 AND sno_hsalida.anocur = sno_hpersonalnomina.anocur ".
				"  					 AND sno_hsalida.codperi = sno_hpersonalnomina.codperi ".
				"   				 AND sno_hsalida.codper = sno_hpersonalnomina.codper ".
				" 				   GROUP BY sno_hsalida.codconc, sno_hsalida.tipsal),0) as previo ".
				"  FROM sno_thsalida, sno_thconcepto, sno_thpersonalnomina ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_thsalida.tipsal='A' OR  sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1')".
				"   ".$ls_criterio." ".
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				"   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thsalida.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thsalida.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thsalida.codper = sno_thpersonalnomina.codper ".
				" GROUP BY sno_thsalida.codconc, sno_thsalida.tipsal, sno_thconcepto.nomcon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadrenomina_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_cuadrenomina_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejetipocargo_programado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejetipocargo_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_monejetipocargo)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de la programaci?n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 30/06/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT sno_programacionreporte.codrep, sno_programacionreporte.codded, sno_programacionreporte.codtipper, ".
				"		(SELECT desded FROM  sno_dedicacion ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_dedicacion.codemp ".
				"			AND sno_programacionreporte.codded = sno_dedicacion.codded) as desded, ".
				"		(SELECT destipper FROM  sno_tipopersonal ".
				"	 	  WHERE sno_programacionreporte.codemp = sno_tipopersonal.codemp ".
				"			AND sno_programacionreporte.codded = sno_tipopersonal.codded ".
				"			AND sno_programacionreporte.codtipper = sno_tipopersonal.codtipper) as destipper ".
				"  FROM sno_programacionreporte ".
				" WHERE sno_programacionreporte.codemp = '".$this->ls_codemp."'".
				"   AND sno_programacionreporte.codrep = '0711'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_monejetipocargo_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_monejetipocargo_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejetipocargo_real($as_codded,$as_codtipper,$ai_cargoreal,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejetipocargo_real
		//         Access: public (desde la clase sigesp_snorh_rpp_comparado0711)  
		//	    Arguments: as_codded // c?digo de dedicaci?n
		//	   			   as_codtipper // c?digo de tipo de personal
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de la programaci?n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 30/06/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_codtipper=="0000")
		{
			$ls_criterio=" AND sno_thpersonalnomina.codded='".$as_codded."'";
			$ls_groupcargos=" GROUP BY sno_thpersonalnomina.codper, sno_thpersonalnomina.codded, sno_thpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_thpersonalnomina.codded ";
		}
		else
		{
			$ls_criterio=" AND sno_thpersonalnomina.codded='".$as_codded."'".
						 " AND sno_thpersonalnomina.codtipper='".$as_codtipper."'";
			$ls_groupcargos=" GROUP BY sno_thpersonalnomina.codper, sno_thpersonalnomina.codded, sno_thpersonalnomina.codtipper ";
			$ls_groupmontos=" GROUP BY sno_thpersonalnomina.codded, sno_thpersonalnomina.codtipper ";
		}

		$ls_sql="SELECT sno_thpersonalnomina.codper ".
				"  FROM sno_thpersonalnomina, sno_thperiodo, sno_thnomina ".
				" WHERE sno_thpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_thpersonalnomina.codnom = '".$this->ls_codnom."'".
				"   AND sno_thpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				"   AND sno_thpersonalnomina.codperi = '".$this->ls_peractnom."'".
				"   ".$ls_criterio.
				"   AND sno_thnomina.tipnom <> 7 ".
				"   AND sno_thnomina.espnom = 0 ".
				"   AND sno_thnomina.ctnom = 0 ".
				"   AND sno_thnomina.codemp = sno_thperiodo.codemp ".
				"   AND sno_thnomina.codnom = sno_thperiodo.codnom ".
				"	AND sno_thnomina.anocurnom = sno_thperiodo.anocur ".
				"   AND sno_thnomina.peractnom = sno_thperiodo.codperi ".
				"   AND sno_thpersonalnomina.codemp = sno_thperiodo.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thperiodo.codnom ".
				"	AND sno_thpersonalnomina.anocur = sno_thperiodo.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_comparado0711_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
			$ls_sql="SELECT sum(sno_thsalida.valsal) as monto ".
					"  FROM sno_thpersonalnomina, sno_thsalida, sno_thperiodo, sno_thnomina ".
					" WHERE sno_thpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_thpersonalnomina.codnom = '".$this->ls_codnom."'".
					"   AND sno_thpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					"   AND sno_thpersonalnomina.codperi = '".$this->ls_peractnom."'".
					$ls_criterio.
					"   AND sno_thsalida.tipsal = 'A' ".
					"   AND sno_thnomina.tipnom <> 7 ".
					"   AND sno_thnomina.codemp = sno_thperiodo.codemp ".
					"   AND sno_thnomina.codnom = sno_thperiodo.codnom ".
					"	AND sno_thnomina.anocurnom = sno_thperiodo.anocur ".
					"   AND sno_thnomina.peractnom = sno_thperiodo.codperi ".
					"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
					"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
					"	AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
					"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
					"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
					"   AND sno_thpersonalnomina.codemp = sno_thperiodo.codemp ".
					"   AND sno_thpersonalnomina.codnom = sno_thperiodo.codnom ".
					"	AND sno_thpersonalnomina.anocur = sno_thperiodo.anocur ".
					"   AND sno_thpersonalnomina.codperi = sno_thperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report M?TODO->uf_comparado0711_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_monejetipocargo_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejepensionado_programado()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejepensionado_programado
		//         Access: public (desde la clase sigesp_snorh_rpp_monejepensionado)  
		//	    Arguments: as_rango // rango de meses a sumar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de la programaci?n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 29/06/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;

		$ls_sql="SELECT sno_programacionreporte.codrep, sno_programacionreporte.codded, sno_programacionreporte.codtipper ".
				"  FROM sno_programacionreporte ".
				" WHERE sno_programacionreporte.codemp = '".$this->ls_codemp."'".
				"   AND sno_programacionreporte.codrep = '0712'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_monejepensionado_programado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_monejepensionado_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_monejepensionado_real($as_catjub,$as_conjub,$ai_cargoreal,$ai_montoreal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_monejepensionado_real
		//         Access: public (desde la clase sigesp_snorh_rpp_monejepensionado)  
		//	    Arguments: as_catjub // Categor?a de Jubilaci?n
		//	   			   as_conjub // Condici?n de Jubilaci?n
		//	   			   ai_cargoreal // Cargo Real
		//	   			   ai_montoreal // Monto Real
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de la programaci?n de reporte
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 29/06/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_groupcargos="";
		$ls_groupmontos="";
		if($as_conjub=="0000")
		{
			$ls_criterio=" AND sno_thpersonalnomina.catjub='".$as_catjub."'";
			$ls_groupcargos=" GROUP BY sno_thpersonalnomina.codper, sno_thpersonalnomina.catjub, sno_thpersonalnomina.conjub ";
			$ls_groupmontos=" GROUP BY sno_thpersonalnomina.catjub ";
		}
		else
		{
			$ls_criterio=" AND sno_thpersonalnomina.catjub='".$as_catjub."'".
						 " AND sno_thpersonalnomina.conjub='".$as_conjub."'";
			$ls_groupcargos=" GROUP BY sno_thpersonalnomina.codper, sno_thpersonalnomina.catjub, sno_thpersonalnomina.conjub ";
			$ls_groupmontos=" GROUP BY sno_thpersonalnomina.catjub, sno_thpersonalnomina.conjub ";
		}
		$ls_sql="SELECT sno_thpersonalnomina.codper ".
				"  FROM sno_thpersonalnomina, sno_thperiodo, sno_thnomina ".
				" WHERE sno_thpersonalnomina.codemp = '".$this->ls_codemp."'".
				"   AND sno_thpersonalnomina.codnom = '".$this->ls_codnom."'".
				"   AND sno_thpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
				"   AND sno_thpersonalnomina.codperi = '".$this->ls_peractnom."'".
				"   AND sno_thnomina.tipnom = 7 ".
				"   AND sno_thnomina.espnom = 0 ".
				"   AND sno_thnomina.ctnom = 0 ".
				$ls_criterio.
				"   AND sno_thnomina.codemp = sno_thperiodo.codemp ".
				"   AND sno_thnomina.codnom = sno_thperiodo.codnom ".
				"	AND sno_thnomina.anocurnom = sno_thperiodo.anocur ".
				"   AND sno_thnomina.peractnom = sno_thperiodo.codperi ".
				"   AND sno_thpersonalnomina.codemp = sno_thperiodo.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thperiodo.codnom ".
				"	AND sno_thpersonalnomina.anocur = sno_thperiodo.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thperiodo.codperi ".
				$ls_groupcargos;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_monejepensionado_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
					"  FROM sno_thpersonalnomina, sno_hsalida, sno_thperiodo, sno_thnomina ".
					" WHERE sno_thpersonalnomina.codemp = '".$this->ls_codemp."'".
					"   AND sno_thpersonalnomina.codnom = '".$this->ls_codnom."'".
					"   AND sno_thpersonalnomina.anocur = '".substr($_SESSION["la_empresa"]["periodo"],0,4)."'".
					"   AND sno_thperiodo.codperi = '".$this->ls_peractnom."'".
					$ls_criterio.
					"   AND sno_thnomina.tipnom = 7 ".
					"   AND sno_thnomina.espnom = 0 ".
					"   AND sno_thnomina.ctnom = 0 ".
					"   AND sno_hsalida.tipsal = 'A' ".
					"   AND sno_thnomina.codemp = sno_thperiodo.codemp ".
					"   AND sno_thnomina.codnom = sno_thperiodo.codnom ".
					"	AND sno_thnomina.anocurnom = sno_thperiodo.anocur ".
					"   AND sno_thnomina.peractnom = sno_thperiodo.codperi ".
					"   AND sno_thpersonalnomina.codemp = sno_hsalida.codemp ".
					"   AND sno_thpersonalnomina.codnom = sno_hsalida.codnom ".
					"	AND sno_thpersonalnomina.anocur = sno_hsalida.anocur ".
					"   AND sno_thpersonalnomina.codperi = sno_hsalida.codperi ".
					"   AND sno_thpersonalnomina.codper = sno_hsalida.codper ".
					"   AND sno_thpersonalnomina.codemp = sno_thperiodo.codemp ".
					"   AND sno_thpersonalnomina.codnom = sno_thperiodo.codnom ".
					"	AND sno_thpersonalnomina.anocur = sno_thperiodo.anocur ".
					"   AND sno_thpersonalnomina.codperi = sno_thperiodo.codperi ".
					$ls_groupmontos;
			$rs_data=$this->io_sql->select($ls_sql);
			if($rs_data===false)
			{
				$this->io_mensajes->message("CLASE->Report M?TODO->uf_monejepensionado_real ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_monejepensionado_real
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_relacionvacacion_personal($as_codper,$as_codvac,$as_conceptocero,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_relacionvacacion_personal
		//         Access: public (desde la clase sigesp_sno_rpp_relacionvacacion)  
		//	    Arguments: as_codper // C?digo del personal 
		//	  			   as_codvac // C?digo de la vacaci?n 
		//	  			   as_conceptocero // si se desean mostrar los conceptos en cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que sale de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/07/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_thsalida.valsal<>0 ";
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as descar ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar) as descar ";
		}
		$ls_sql="SELECT sno_thpersonalnomina.codemp, sno_personal.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_thunidadadmin.desuniadm, sno_thvacacpersonal.sueintvac, sno_thvacacpersonal.fecdisvac, sno_thvacacpersonal.fecvenvac, ".
				"		sno_thvacacpersonal.fecreivac, sno_thvacacpersonal.diavac, sno_thvacacpersonal.codvac, ".$ls_descar.
				"       ,sno_thvacacpersonal.dianorvac, sno_thvacacpersonal.persalvac, sno_thvacacpersonal.peringvac, ".
				"       sno_thvacacpersonal.quisalvac, sno_thvacacpersonal.quireivac, sno_thvacacpersonal.diabonvac, ".
				"       sno_thvacacpersonal.sabdom, sno_thvacacpersonal.diafer,sno_thvacacpersonal.obsvac, sno_thvacacpersonal.diaadibon,".
				"       sno_thvacacpersonal.diapenvac, sno_thvacacpersonal.diapervac,sno_thvacacpersonal.diaadivac, MAX(sno_dedicacion.desded) as desded,  ".				
				"		MAX(sno_personal.anoservpreper) as anoservpreper, MAX(sno_thvacacpersonal.codusu) as codusu  ".
				"  FROM sno_personal ".
				" INNER JOIN (sno_thpersonalnomina  ".
				"		INNER JOIN sno_thunidadadmin ".
				"          ON sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_thpersonalnomina.codper='".$as_codper."' ".
				"         AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"         AND sno_thpersonalnomina.codnom = sno_thunidadadmin.codnom ".
				"         AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"         AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"         AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"         AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"         AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"         AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"         AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				"		INNER JOIN sno_dedicacion ".
				"          ON sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_thpersonalnomina.codper='".$as_codper."' ".
				"         AND sno_thpersonalnomina.codemp = sno_dedicacion.codemp ".
				"         AND sno_thpersonalnomina.codded = sno_dedicacion.codded ".
				"		INNER JOIN sno_thvacacpersonal ".
				"          ON sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_thpersonalnomina.codper='".$as_codper."' ".
				"  		  AND sno_thvacacpersonal.codvac='".$as_codvac."' ".
				"         AND sno_thpersonalnomina.codemp = sno_thvacacpersonal.codemp ".
				"         AND sno_thpersonalnomina.anocur = sno_thvacacpersonal.anocur ".
				"         AND sno_thpersonalnomina.codperi = sno_thvacacpersonal.codperi ".
				"         AND sno_thpersonalnomina.codper = sno_thvacacpersonal.codper ".
				"		INNER JOIN sno_thsalida ".
				"          ON sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"         AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"         AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"         AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"         AND sno_thpersonalnomina.codper='".$as_codper."' ".
				"         AND ((sno_thsalida.tipsal = 'V1' OR sno_thsalida.tipsal = 'V2' OR sno_thsalida.tipsal = 'V3' OR sno_thsalida.tipsal = 'V4') ".
				"          OR (sno_thsalida.tipsal = 'W1' OR sno_thsalida.tipsal = 'W2' OR sno_thsalida.tipsal = 'W3' OR sno_thsalida.tipsal = 'W4')) ".
				$ls_criterio.
				"         AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"         AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"         AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"         AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"         AND sno_thpersonalnomina.codper = sno_thsalida.codper) ".
				"    ON sno_thpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_thpersonalnomina.codper = sno_personal.codper ".
				" WHERE sno_personal.codemp='".$this->ls_codemp."' ".
				"   AND sno_personal.codper='".$as_codper."' ".
				" GROUP BY sno_thpersonalnomina.codemp, sno_thpersonalnomina.anocur, sno_thpersonalnomina.codperi, sno_personal.codper, ".
				"  sno_thvacacpersonal.codvac, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"  sno_thunidadadmin.desuniadm, sno_thvacacpersonal.sueintvac, sno_thvacacpersonal.fecdisvac, sno_thvacacpersonal.fecreivac, ".
				"  sno_thvacacpersonal.diavac, sno_thvacacpersonal.dianorvac, sno_thvacacpersonal.persalvac, sno_thvacacpersonal.peringvac, ".
				"  sno_thvacacpersonal.quisalvac, sno_thvacacpersonal.quireivac, sno_thvacacpersonal.diabonvac, sno_thvacacpersonal.sabdom, ".
				"  sno_thvacacpersonal.diafer,sno_thvacacpersonal.obsvac, sno_thvacacpersonal.diaadibon, sno_thvacacpersonal.diapenvac, ".
				"  sno_thvacacpersonal.diapervac,sno_thvacacpersonal.diaadivac,sno_thpersonalnomina.codnom,sno_thpersonalnomina.codcar,sno_thpersonalnomina.codasicar,sno_thvacacpersonal.fecvenvac ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_relacionvacacion_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_relacionvacacion_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_relacionvacacion_concepto($as_codper,$as_codvac,$as_conceptocero,$as_tituloconcepto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_relacionvacacion_concepto
		//         Access: public (desde la clase sigesp_sno_rpp_relacionvacacion)  
		//	    Arguments: as_codper // C?digo del personal 
		//	  			   as_codvac // C?digo de vacaci?n
		//	  			   as_conceptocero // si se desean mostrar los conceptos en cero
		//	  			   as_tituloconcepto // si se desea mostrar el nombre del concepto ? el t?tulo
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que sale de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/07/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_thconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_thconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_thsalida.valsal<>0 ";
		}
		$ls_sql="SELECT sno_thconcepto.codconc, ".$ls_campo." as nomcon, sno_thsalida.valsal, ".
				"		sno_thsalida.tipsal, sno_thvacacpersonal.persalvac, sno_thvacacpersonal.peringvac ".
				"  FROM sno_thpersonalnomina, sno_thconcepto, sno_thsalida, sno_thvacacpersonal ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thpersonalnomina.codper='".$as_codper."' ".
				"   AND sno_thvacacpersonal.codvac='".$as_codvac."' ".
				$ls_criterio.
				"   AND ((sno_thsalida.tipsal = 'V1' OR sno_thsalida.tipsal = 'V2' OR sno_thsalida.tipsal = 'V3' OR sno_thsalida.tipsal = 'V4') ".
				"    OR (sno_thsalida.tipsal = 'W1' OR sno_thsalida.tipsal = 'W2' OR sno_thsalida.tipsal = 'W3' OR sno_thsalida.tipsal = 'W4')) ".
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thvacacpersonal.codemp ".
				"   AND sno_thpersonalnomina.anocur = sno_thvacacpersonal.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thvacacpersonal.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thvacacpersonal.codper ".
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_relacionvacacion_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_relacionvacacion_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_programacionvacaciones_personal($as_estvac,$ad_fecdisdes,$ad_fecdishas,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_programacionvacaciones_personal
		//         Access: public (desde la clase sigesp_sno_rpp_resumenconceptos)  
		//	    Arguments: as_estvac // Estatus de las vacaciones
		//				   ad_fecdisdes // Fecha de Disfrute Desde
		//				   ad_fecdishas // Fecha de Disfrute Hasta
		//	  			   as_orden // Orden de la salida
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las vacaciones programadas del personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 23/08/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_estvac))
		{
			$ls_criterio= "AND sno_thvacacpersonal.stavac = ".$as_estvac."";
		}
		else
		{
			$ls_criterio= "AND (sno_thvacacpersonal.stavac = 1 OR sno_thvacacpersonal.stavac = 2) ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($ad_fecdisdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thvacacpersonal.fecdisvac>='".$this->io_funciones->uf_convertirdatetobd($ad_fecdisdes)."'";
		}
		if(!empty($ad_fecdishas))
		{
			$ls_criterio = $ls_criterio."   AND sno_thvacacpersonal.fecdisvac<='".$this->io_funciones->uf_convertirdatetobd($ad_fecdishas)."' ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de Personal 
				$ls_orden="ORDER BY sno_personal.codper, sno_thvacacpersonal.codvac ";
				break;

			case "2": // Ordena por Apellido de Personal
				$ls_orden="ORDER BY sno_personal.apeper, sno_thvacacpersonal.codvac ";
				break;

			case "3": // Ordena por Nombre de Personal
				$ls_orden="ORDER BY sno_personal.nomper, sno_thvacacpersonal.codvac ";
				break;

			case "4": // Ordena por Fecha de Vencimiento
				$ls_orden="ORDER BY sno_thvacacpersonal.fecvenvac, sno_thvacacpersonal.codvac ";
				break;

			case "5": // Ordena por Fecha de Disfrute
				$ls_orden="ORDER BY sno_thvacacpersonal.fecdisvac, sno_thvacacpersonal.codvac ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, sno_thvacacpersonal.codvac, ".
		        "		sno_thvacacpersonal.fecvenvac, sno_thvacacpersonal.fecdisvac, sno_thvacacpersonal.stavac, sno_thcargo.codcar, sno_thcargo.descar, ".
				"		sno_thasignacioncargo.denasicar,sno_personal.fecingper, sno_thvacacpersonal.sueintbonvac, sno_thvacacpersonal.dianorvac, ".
				"		sno_thvacacpersonal.diafer, sno_thvacacpersonal.sabdom, sno_thvacacpersonal.diaadivac, sno_thvacacpersonal.diavac, ".
				"		sno_thvacacpersonal.diabonvac, sno_thvacacpersonal.diaadibon, sno_thvacacpersonal.fecreivac".
 				"  FROM sno_personal, sno_thpersonalnomina, sno_thvacacpersonal ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio." ".
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"   AND sno_personal.codemp = sno_thvacacpersonal.codemp ".
				"   AND sno_personal.codper = sno_thvacacpersonal.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				"   AND sno_thpersonalnomina.codcar = sno_thcargo.codcar ".
				"   AND sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				"   AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_programacionvacaciones_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_programacionvacaciones_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_trabajo_anterior($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_relacionvacacion_concepto
		//         Access: public (desde la clase sigesp_sno_rpp_relacionvacacion)  
		//	    Arguments: as_codper // C?digo del personal 
		//	  			   as_codvac // C?digo de vacaci?n
		//	  			   as_conceptocero // si se desean mostrar los conceptos en cero
		//	  			   as_tituloconcepto // si se desea mostrar el nombre del concepto ? el t?tulo
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que sale de vacaciones
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 03/07/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT emptraant, ultcartraant, fecingtraant, fecrettraant, anolab, meslab, dialab".
				"  FROM sno_trabajoanterior ".
				" WHERE sno_trabajoanterior.codemp='".$this->ls_codemp."' ".
				"   AND sno_trabajoanterior.codper='".$as_codper."' ".
				"   AND sno_trabajoanterior.emppubtraant='1' ".
				"UNION ".
				"SELECT '' AS emptraant,(CASE sno_nomina.racnom WHEN '1' THEN sno_asignacioncargo.denasicar ELSE sno_cargo.descar END) AS ultcartraant, ".
				"      MAX (sno_personal.fecingper) AS fecingtraant, '1900-01-01' AS fecrettraant, 0 AS anolab,0 AS meslab,0 AS dialab". 
				"  FROM sno_personalnomina ".
				" INNER JOIN sno_nomina ".
				"    ON sno_personalnomina.staper ='1' ".
				"    AND sno_nomina.espnom='0' ".
				"    AND sno_personalnomina.codemp = sno_nomina.codemp ".
				"    AND sno_personalnomina.codnom = sno_nomina.codnom ".
				"  INNER JOIN sno_cargo".
				"     ON sno_personalnomina.staper ='1' ".
				"    AND sno_personalnomina.codemp = sno_cargo.codemp ".
				"    AND sno_personalnomina.codnom = sno_cargo.codnom ".
				"    AND sno_personalnomina.codcar = sno_cargo.codcar ".
				"  INNER JOIN sno_asignacioncargo ".
				"     ON sno_personalnomina.staper ='1' ".
				"    AND sno_personalnomina.codemp = sno_asignacioncargo.codemp ".
				"    AND sno_personalnomina.codnom = sno_asignacioncargo.codnom ".
				"    AND sno_personalnomina.codasicar = sno_asignacioncargo.codasicar ".
				" INNER JOIN sno_personal ON sno_personalnomina.staper ='1' ".
				"   AND sno_personalnomina.codemp = sno_personal.codemp ".
				"   AND sno_personalnomina.codper = sno_personal.codper ".
				"   WHERE sno_personalnomina.codper ='".$as_codper."' ".
				" GROUP BY sno_personalnomina.codper,sno_nomina.racnom,sno_asignacioncargo.denasicar,sno_cargo.descar,codclavia ORDER BY fecingtraant";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_relacionvacacion_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_relacionvacacion_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------
	function uf_select_usuario($as_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sep_select_usuario
		//		   Access: private
		//	    Arguments: as_codemp // codigo de la empresa
		//	   			   as_codusu // codigo del articulo
		//                 as_nomusu // codigo unidad de medida (referencia)
		//    Description: Function que devuelve el codigo de la unidad de medida que tiene asociada el articulo
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creaci?n: 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $as_nomusu="";
		 $ls_sql ="SELECT nomusu,apeusu ".
				  "  FROM sss_usuarios ".
				  " WHERE codemp='".$this->ls_codemp."'".
				  "   AND codusu='".$as_codusu."' ";
		 $rs=$this->io_sql->select($ls_sql);
		 if ($rs===false)
		 {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_select_usuario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 }
		 else
		 {
			 if($row=$this->io_sql->fetch_row($rs))
			 {
				$as_nomusu=$row["nomusu"]." ".$row["apeusu"];
				$lb_valido=true;
			 }
		 }
		 return $as_nomusu;
	}//fin 	uf_select_usuario
    //---------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cargo_usuario($as_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sep_select_usuario
		//		   Access: private
		//	    Arguments: as_codemp // codigo de la empresa
		//	   			   as_codusu // codigo del articulo
		//                 as_nomusu // codigo unidad de medida (referencia)
		//    Description: Function que devuelve el codigo de la unidad de medida que tiene asociada el articulo
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creaci?n: 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $as_descar="";
		 $ls_sql ="SELECT MAX(descar) AS descar ".
				  "  FROM sno_personal, sno_personalnomina, sss_usuarios, sno_cargo ".
				  " WHERE sss_usuarios.codemp='".$this->ls_codemp."'".
				  "   AND sss_usuarios.codusu='".$as_codusu."' ".
				  "   AND sss_usuarios.codemp=sno_personal.codemp ".
				  "   AND sss_usuarios.cedusu=sno_personal.cedper ".
				  "   AND sno_personal.codemp=sno_personalnomina.codemp ".
				  "   AND sno_personal.codper=sno_personalnomina.codper ".
				  "   AND sno_personalnomina.codemp=sno_cargo.codemp ".
				  "   AND sno_personalnomina.codnom=sno_cargo.codnom ".
				  "   AND sno_personalnomina.codcar=sno_cargo.codcar ";
		 $rs=$this->io_sql->select($ls_sql);
		 if ($rs===false)
		 {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_select_usuario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 }
		 else
		 {
			 if($row=$this->io_sql->fetch_row($rs))
			 {
				$as_descar=$row["descar"];
				$lb_valido=true;
			 }
		 }
		 return $as_descar;
	}//fin 	uf_select_usuario
    //---------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadofirmas($as_codperdes,$as_codperhas,$as_personalcero,$as_quincena,$as_tipopago,$as_coduniadm,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadofirmas
		//		   Access: public (desde la clase sigesp_sno_rpp_listadofirmas)  
		//	    Arguments: as_codperdes // C?digo del personal Desde
		//	    		   as_codperhas // c?digo del personal Hasta
		//	    		   as_personalcero // Si se quiere filtrar por el personal con monto cero
		//	    		   as_quincena // si se busca a toto del personal ? solo los activos
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas para que firmen lo que se les pago
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/11/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio = $ls_criterio."   AND sno_thpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		switch($as_tipopago)
		{
			case "1": // Pago en efectivo
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagefeper=1 ";
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagbanper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagtaqper=0 ";
				break;
				
			case "2": // Pago en banco
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagefeper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagbanper=1 ";
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagtaqper=0 ";
				break;
				
			case "3": // Pago por taquilla
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagefeper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagbanper=0 ";
				$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.pagtaqper=1 ";
				break;
		}
		switch($as_quincena)
		{
			case 1: // Primera Quincena
				$ls_monto="sno_thresumen.priquires as monnetres";
				if(!empty($as_personalcero))
				{
					$ls_criterio = $ls_criterio."AND sno_thresumen.priquires<>0 ";
				}
				break;

			case 2: // Segunda Quincena
				$ls_monto="sno_thresumen.segquires as monnetres";
				if(!empty($as_personalcero))
				{
					$ls_criterio = $ls_criterio."AND sno_thresumen.segquires<>0 ";
				}
				break;

			case 3: // Mes Completo
				$ls_monto="sno_thresumen.monnetres as monnetres";
				if(!empty($as_personalcero))
				{
					$ls_criterio = $ls_criterio."AND sno_thresumen.monnetres<>0 ";
				}
				break;
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
				       "           AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
				       "           AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thcargo.codemp=sno_thpersonalnomina.codemp ".
					   "           AND sno_thcargo.codnom=sno_thpersonalnomina.codnom ".
					   "           AND sno_thcargo.anocur = sno_thpersonalnomina.anocur ".
					   "           AND sno_thcargo.codperi = sno_thpersonalnomina.codperi ".
					   "	       AND sno_thcargo.codcar=sno_thpersonalnomina.codcar ) as descar ";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo del Personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido del Personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del Personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;
		}
		$ls_sql="SELECT sno_personal.codper, sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, ".$ls_monto.
				"       ,sno_thunidadadmin.desuniadm, sno_thresumen.asires, ".
				"       sno_thresumen.dedres, sno_thresumen.apoempres, ".$ls_descar.
				"  FROM sno_personal, sno_thpersonalnomina,  sno_thresumen, sno_thunidadadmin ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				$ls_criterio. 
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"	AND sno_thpersonalnomina.codemp = sno_thresumen.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thresumen.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thresumen.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thresumen.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thresumen.codper ".
				"	AND sno_thunidadadmin.codemp=sno_thpersonalnomina.codemp ".
				"   AND sno_thunidadadmin.codnom=sno_thpersonalnomina.codnom ".
				"   AND sno_thunidadadmin.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thunidadadmin.codperi = sno_thpersonalnomina.codperi ".
				"	AND sno_thunidadadmin.minorguniadm=sno_thpersonalnomina.minorguniadm ".
				"	AND sno_thunidadadmin.ofiuniadm=sno_thpersonalnomina.ofiuniadm ".
				"	AND sno_thunidadadmin.uniuniadm=sno_thpersonalnomina.uniuniadm ".
				"	AND sno_thunidadadmin.depuniadm=sno_thpersonalnomina.depuniadm ".
				"	AND sno_thunidadadmin.prouniadm=sno_thpersonalnomina.prouniadm ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadofirmas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadofirmas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoprestamo_conceptos($as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,
										  $as_codtippredes,$as_codtipprehas,$as_subnomdes,$as_subnomhas,$as_estatus)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoprestamo_conceptos
		//         Access: public (desde la clase sigesp_sno_rpp_listadoprestamo)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_codtippredes // C?digo del tipo de prestamo desde
		//	  			   as_codtipprehas // C?digo del tipo de prestamo hasta
		//	  			   as_estatus // Estatus del prestamo
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 04/12/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thprestamos.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codtippredes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codtippre>='".$as_codtippredes."'";
		}
		if(!empty($as_codtipprehas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codtippre<='".$as_codtipprehas."'";
		}
		if(!empty($as_estatus))
		{
			$ls_criterio = $ls_criterio."   AND sno_thprestamos.stapre='".$as_estatus."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_thprestamos.codconc, sno_thconcepto.nomcon ".
				"  FROM sno_thprestamos, sno_thconcepto, sno_thpersonalnomina ".
				" WHERE sno_thprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_thprestamos.codnom='".$this->ls_codnom."' ".
				"   AND sno_thprestamos.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thprestamos.codperi='".$this->ls_peractnom."' ".
				$ls_criterio.
				"   AND sno_thprestamos.codemp = sno_thconcepto.codemp ".
				"   AND sno_thprestamos.codnom = sno_thconcepto.codnom ".
				"   AND sno_thprestamos.anocur = sno_thconcepto.anocur ".
				"   AND sno_thprestamos.codperi = sno_thconcepto.codperi ".
				"   AND sno_thprestamos.codconc = sno_thconcepto.codconc ".
				"   AND sno_thprestamos.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thprestamos.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thprestamos.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thprestamos.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thprestamos.codper = sno_thpersonalnomina.codper ".
				" GROUP BY sno_thprestamos.codconc, sno_thconcepto.nomcon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoprestamo_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadoprestamo_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoprestamo_personalconcepto($as_codconc,$as_codperdes,$as_codperhas,
										         $as_codtippredes,$as_codtipprehas,$as_estatus,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoprestamo_personalconcepto
		//		   Access: public (desde la clase sigesp_sno_rpp_listadoprestamo)  
		//	    Arguments: as_codconc // C?digo del concepto del que se desea busca el personal
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_codtippredes // C?digo del tipo de prestamo desde
		//	  			   as_codtipprehas // C?digo del tipo de prestamo hasta
		//	  			   as_estatus // Estatus del prestamo
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal asociado al concepto que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 04/12/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codtippredes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codtippre>='".$as_codtippredes."'";
		}
		if(!empty($as_codtipprehas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codtippre<='".$as_codtipprehas."'";
		}
		if(!empty($as_estatus))
		{
			$ls_criterio = $ls_criterio."   AND sno_thprestamos.stapre='".$as_estatus."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_thprestamos.codper, sno_personal.nomper, sno_personal.apeper, sno_thtipoprestamo.destippre, ".
			    "		sno_thprestamos.fecpre, sno_thprestamos.monpre,  sno_thprestamos.monamopre, sno_thprestamos.stapre, ".
				"		(SELECT COUNT(codper) FROM sno_thprestamosperiodo ".
				"         WHERE sno_thprestamosperiodo.estcuo = 0 ".
				"			AND sno_thprestamos.codemp = sno_thprestamosperiodo.codemp ".
				" 			AND sno_thprestamos.codnom = sno_thprestamosperiodo.codnom ".
				"			AND sno_thprestamos.anocur = sno_thprestamosperiodo.anocur ".
				"			AND sno_thprestamos.codperi = sno_thprestamosperiodo.codperi ".
				"			AND sno_thprestamos.codper = sno_thprestamosperiodo.codper ".
				"			AND sno_thprestamos.numpre = sno_thprestamosperiodo.numpre ".
				"			AND sno_thprestamos.codtippre = sno_thprestamosperiodo.codtippre) AS numcuopre ".
			    "  FROM sno_thprestamos, sno_personal, sno_thtipoprestamo, sno_thpersonalnomina ".
			    " WHERE sno_thprestamos.codemp='".$this->ls_codemp."' ".
			    "   AND sno_thprestamos.codnom='".$this->ls_codnom."' ".
			    "   AND sno_thprestamos.anocur='".$this->ls_anocurnom."' ".
			    "   AND sno_thprestamos.codperi='".$this->ls_peractnom."' ".
				"	AND sno_thprestamos.codconc='".$as_codconc."' ".
				$ls_criterio.
			    "   AND sno_thprestamos.codemp = sno_personal.codemp ".
			    "   AND sno_thprestamos.codper = sno_personal.codper ".
				"   AND sno_thprestamos.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thprestamos.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thprestamos.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thprestamos.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thprestamos.codper = sno_thpersonalnomina.codper ".
			    "   AND sno_thprestamos.codemp = sno_thtipoprestamo.codemp ".
			    "   AND sno_thprestamos.codnom = sno_thtipoprestamo.codnom ".
			    "   AND sno_thprestamos.anocur = sno_thtipoprestamo.anocur ".
			    "   AND sno_thprestamos.codperi = sno_thtipoprestamo.codperi ".
			    "   AND sno_thprestamos.codtippre = sno_thtipoprestamo.codtippre ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoprestamo_personalconcepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadoprestamo_personalconcepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_detalleprestamo_personal($as_codconcdes,$as_codconchas,$as_codperdes,$as_codperhas,
										  $as_codtippredes,$as_codtipprehas,$as_estatus,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalleprestamo_personal
		//         Access: public (desde la clase sigesp_sno_rpp_detalleoprestamo)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_codtippredes // C?digo del tipo de prestamo desde
		//	  			   as_codtipprehas // C?digo del tipo de prestamo hasta
		//	  			   as_estatus // Estatus del prestamo
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 04/12/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= "AND sno_thprestamos.codconc>='".$as_codconcdes."'";
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codconc<='".$as_codconchas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codper<='".$as_codperhas."'";
		}
		if(!empty($as_codtippredes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codtippre>='".$as_codtippredes."'";
		}
		if(!empty($as_codtipprehas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thprestamos.codtippre<='".$as_codtipprehas."'";
		}
		if(!empty($as_estatus))
		{
			$ls_criterio = $ls_criterio."   AND sno_thprestamos.stapre='".$as_estatus."' ";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden=" ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden=" ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden=" ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula de personal
				$ls_orden=" ORDER BY sno_personal.cedper ";
				break;
		}
		$ls_sql="SELECT sno_thprestamos.codper, sno_thprestamos.numpre, sno_thprestamos.codtippre, sno_thprestamos.codconc, ".
				"		sno_thprestamos.monpre, sno_thprestamos.numcuopre, sno_thprestamos.monamopre, sno_thprestamos.stapre, ".
				"		sno_thprestamos.fecpre, sno_thprestamos.perinipre, sno_personal.nomper, sno_personal.apeper, ".
				"		sno_thconcepto.nomcon, sno_thtipoprestamo.destippre, sno_personal.cedper, sno_personal.fecingper ".
				"  FROM sno_thprestamos, sno_personal, sno_thconcepto, sno_thtipoprestamo, sno_thpersonalnomina ".
				" WHERE sno_thprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_thprestamos.codnom='".$this->ls_codnom."' ".
				"   AND sno_thprestamos.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thprestamos.codperi='".$this->ls_peractnom."' ".
				$ls_criterio.
				"   AND sno_thprestamos.codemp = sno_personal.codemp ".
				"   AND sno_thprestamos.codper = sno_personal.codper ".
				"   AND sno_thprestamos.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thprestamos.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thprestamos.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thprestamos.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thprestamos.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thprestamos.codemp = sno_thconcepto.codemp ".
				"   AND sno_thprestamos.codnom = sno_thconcepto.codnom ".
				"   AND sno_thprestamos.anocur = sno_thconcepto.anocur ".
				"   AND sno_thprestamos.codperi = sno_thconcepto.codperi ".
				"   AND sno_thprestamos.codconc = sno_thconcepto.codconc ".
				"   AND sno_thprestamos.codemp = sno_thtipoprestamo.codemp ".
				"   AND sno_thprestamos.codnom = sno_thtipoprestamo.codnom ".
				"   AND sno_thprestamos.anocur = sno_thtipoprestamo.anocur ".
				"   AND sno_thprestamos.codperi = sno_thtipoprestamo.codperi ".
				"   AND sno_thprestamos.codtippre = sno_thtipoprestamo.codtippre ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_detalleprestamo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_detalleprestamo_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_detalleprestamo_cuotas($as_codper,$ai_numpre,$as_codtippre)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalleprestamo_cuotas
		//         Access: public (desde la clase sigesp_sno_rpp_detalleoprestamo)  
		//	    Arguments: as_codper // C?digo del personal
		//				   ai_numpre // N?mero del Prestamo
		//				   as_codtippre // C?digo del tipo de prestamo
		//				   as_codconc // C?digo de concepto
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 04/12/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numcuo, percob, feciniper, fecfinper, moncuo, estcuo ".
				"  FROM sno_thprestamosperiodo ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND anocur='".$this->ls_anocurnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND numpre='".$ai_numpre."' ".
				"   AND codtippre='".$as_codtippre."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_detalleprestamo_cuotas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->reset_ds();
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_detalleprestamo_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_detalleprestamo_amortizado($as_codper,$ai_numpre,$as_codtippre)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_detalleprestamo_amortizado
		//         Access: public (desde la clase sigesp_sno_rpp_detalleoprestamo)  
		//	    Arguments: as_codper // C?digo del personal
		//				   ai_numpre // N?mero del Prestamo
		//				   as_codtippre // C?digo del tipo de prestamo
		//				   as_codconc // C?digo de concepto
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las personas que se tienen asociados prestamos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 04/12/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numamo, peramo, fecamo, monamo, desamo ".
				"  FROM sno_thprestamosamortizado ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND codnom='".$this->ls_codnom."' ".
				"   AND anocur='".$this->ls_anocurnom."' ".
				"   AND codperi='".$this->ls_peractnom."' ".
				"   AND codper='".$as_codper."' ".
				"   AND numpre='".$ai_numpre."' ".
				"   AND codtippre='".$as_codtippre."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_detalleprestamo_amortizado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->reset_ds();
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_detalleprestamo_amortizado
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyecto_proyectos($as_codproydes,$as_codproyhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyecto_proyectos
		//         Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codproydes // C?digo del proyecto donde se empieza a filtrar
		//				   as_codproyhas // C?digo del proyecto donde se termina de filtrar
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos que se calcularon en la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/08/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codproydes))
		{
			$ls_criterio= "AND sno_thproyecto.codproy>='".$as_codproydes."'";
		}
		if(!empty($as_codproyhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thproyecto.codproy<='".$as_codproyhas."'";
		}
		$ls_sql="SELECT sno_thproyecto.codproy, MAX(sno_thproyecto.nomproy) AS nomproy, count(sno_thproyectopersonal.codper) as total, ".
				"		sum(sno_thproyectopersonal.pordiames*100) as monto ".
				"  FROM sno_thproyectopersonal, sno_thproyecto ".
				" WHERE sno_thproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_thproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_thproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_thproyectopersonal.codemp = sno_thproyecto.codemp ".
				"   AND sno_thproyectopersonal.anocur = sno_thproyecto.anocur ".
				"   AND sno_thproyectopersonal.codperi = sno_thproyecto.codperi ".
				"   AND sno_thproyectopersonal.codproy = sno_thproyecto.codproy ".
				" GROUP BY sno_thproyecto.codproy  ".
				" ORDER BY sno_thproyecto.codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoproyecto_proyectos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadoproyecto_proyectos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyecto_proyectospersonal($as_codproy,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyecto_proyectospersonal
		//		   Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codproy // C?digo del proyecto del que se desea busca el personal
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal asociado al proyecto
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/08/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->io_sql=new class_sql($this->io_conexion);	
		$lb_valido=true;
		$ls_orden="";
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		if($this->li_rac=="1")// Utiliza RAC
		{
			$ls_descar="       (SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar) as descar ";
		}
		else// No utiliza RAC
		{
			$ls_descar="       (SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar) as descar ";
		}
		$ls_sql="SELECT sno_personal.cedper, sno_personal.apeper, sno_personal.nomper, (sno_thproyectopersonal.pordiames*100) AS pordiames, ".$ls_descar.
				"  FROM sno_personal, sno_thpersonalnomina, sno_thproyectopersonal ".
				" WHERE sno_thproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_thproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_thproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thproyectopersonal.codproy='".$as_codproy."' ".
				"   AND sno_thpersonalnomina.codemp = sno_thproyectopersonal.codemp ".
				"   AND sno_thpersonalnomina.anocur = sno_thproyectopersonal.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thproyectopersonal.codperi ".
				"   AND sno_thpersonalnomina.codnom = sno_thproyectopersonal.codnom ".
				"   AND sno_thpersonalnomina.codper = sno_thproyectopersonal.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_thpersonalnomina.codper = sno_personal.codper ".
				"   ".$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoproyecto_proyectospersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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

	}// end function uf_listadoproyecto_proyectospersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyectopersonal_personal($as_codperdes,$as_codperhas,$as_subnomdes,$as_subnomhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyectopersonal_personal
		//         Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//				   as_codperhas // C?digo del personal donde se termina de filtrar
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que tiene asociado proyectos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/08/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_personal.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre de personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por C?dula de personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio= "AND sno_thproyectopersonal.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thproyectopersonal.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_personal.codper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.apeper) AS apeper, ".
				"		count(sno_thproyectopersonal.codproy) as total, sum(sno_thproyectopersonal.pordiames*100) as monto ".
				"  FROM sno_thproyectopersonal, sno_thproyecto, sno_personal, sno_thpersonalnomina ".
				" WHERE sno_thproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_thproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_thproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_thproyectopersonal.codemp = sno_thproyecto.codemp ".
				"   AND sno_thproyectopersonal.codnom = sno_thproyecto.codnom ".
				"   AND sno_thproyectopersonal.anocur = sno_thproyecto.anocur ".
				"   AND sno_thproyectopersonal.codperi = sno_thproyecto.codperi ".
				"   AND sno_thproyectopersonal.codproy = sno_thproyecto.codproy ".
				"   AND sno_thproyectopersonal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thproyectopersonal.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thproyectopersonal.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thproyectopersonal.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thproyectopersonal.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_personal.codemp ".
				"   AND sno_thpersonalnomina.codper = sno_personal.codper ".
				" GROUP BY sno_personal.codper  ".
				$ls_orden;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoproyectopersonal_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadoproyectopersonal_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadoproyectopersonal_proyecto($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadoproyectopersonal_proyecto
		//         Access: public (desde la clase sigesp_sno_rpp_listadoproyecto)  
		//	    Arguments: as_codper // C?digo del personal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los proyectos asociados al personal
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 01/08/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_sql="SELECT sno_thproyecto.codproy, sno_thproyecto.nomproy, (sno_thproyectopersonal.pordiames*100) AS pordiames ".
				"  FROM sno_thproyectopersonal, sno_thproyecto ".
				" WHERE sno_thproyectopersonal.codemp='".$this->ls_codemp."' ".
				"   AND sno_thproyectopersonal.codnom='".$this->ls_codnom."' ".
				"   AND sno_thproyectopersonal.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thproyectopersonal.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thproyectopersonal.codper='".$as_codper."' ".
				"   AND sno_thproyectopersonal.codemp = sno_thproyecto.codemp ".
				"   AND sno_thproyectopersonal.codnom = sno_thproyecto.codnom ".
				"   AND sno_thproyectopersonal.anocur = sno_thproyecto.anocur ".
				"   AND sno_thproyectopersonal.codperi = sno_thproyecto.codperi ".
				"   AND sno_thproyectopersonal.codproy = sno_thproyecto.codproy ".
				" ORDER BY sno_thproyecto.codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadoproyectopersonal_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadoproyectopersonal_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaunidad_unidad($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,
										  $as_coduniadmdes,$as_coduniadmhas,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonominaunidad_unidad
		//         Access: public (desde la clase sigesp_sno_rpp_pagonominaunidadadmin)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	    		   as_coduniadmdes // C?digo de Unidad Administrativa donde se empieza a filtrar
		//	  			   as_coduniadmhas // C?digo de Unidad Administrativa donde se termina de filtrar		  
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las unidades administrativas del personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 07/08/2007								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
			$ls_criteriounion= $ls_criteriounion."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";
		}
		if(!empty($as_coduniadmdes))
		{
			$ls_criterio= $ls_criterio." AND sno_thpersonalnomina.minorguniadm>='".substr($as_coduniadmdes,0,4)."'".
						  			   " AND sno_thpersonalnomina.ofiuniadm>='".substr($as_coduniadmdes,5,2)."' ".
						               " AND sno_thpersonalnomina.uniuniadm>='".substr($as_coduniadmdes,8,2)."' ".
						               " AND sno_thpersonalnomina.depuniadm>='".substr($as_coduniadmdes,11,2)."' ".
						               " AND sno_thpersonalnomina.prouniadm>='".substr($as_coduniadmdes,14,2)."' ";
			$ls_criteriounion= $ls_criteriounion." AND sno_thpersonalnomina.minorguniadm>='".substr($as_coduniadmdes,0,4)."'".
						  	   					 " AND sno_thpersonalnomina.ofiuniadm>='".substr($as_coduniadmdes,5,2)."' ".
						       					 " AND sno_thpersonalnomina.uniuniadm>='".substr($as_coduniadmdes,8,2)."' ".
						       					 " AND sno_thpersonalnomina.depuniadm>='".substr($as_coduniadmdes,11,2)."' ".
						       					 " AND sno_thpersonalnomina.prouniadm>='".substr($as_coduniadmdes,14,2)."' ";
		}
		if(!empty($as_coduniadmhas))
		{
			$ls_criterio= $ls_criterio." AND sno_thpersonalnomina.minorguniadm<='".substr($as_coduniadmhas,0,4)."'".
						  			   " AND sno_thpersonalnomina.ofiuniadm<='".substr($as_coduniadmhas,5,2)."' ".
						               " AND sno_thpersonalnomina.uniuniadm<='".substr($as_coduniadmdes,8,2)."' ".
						               " AND sno_thpersonalnomina.depuniadm<='".substr($as_coduniadmhas,11,2)."' ".
						               " AND sno_thpersonalnomina.prouniadm<='".substr($as_coduniadmhas,14,2)."' ";
			$ls_criteriounion= $ls_criteriounion." AND sno_thpersonalnomina.minorguniadm<='".substr($as_coduniadmhas,0,4)."'".
						  	   					 " AND sno_thpersonalnomina.ofiuniadm<='".substr($as_coduniadmhas,5,2)."' ".
						       					 " AND sno_thpersonalnomina.uniuniadm<='".substr($as_coduniadmhas,8,2)."' ".
						       					 " AND sno_thpersonalnomina.depuniadm<='".substr($as_coduniadmhas,11,2)."' ".
						       					 " AND sno_thpersonalnomina.prouniadm<='".substr($as_coduniadmhas,14,2)."' ";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
					  "    	  sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, MAX(sno_thunidadadmin.desuniadm) AS desuniadm ".
					  "  FROM sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin ".
					  " WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
					  "	  AND sno_thpersonalnomina.staper = '2' ".
					  "   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_thsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
					  "   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
					  "   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
					  "   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
					  "   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
					  "   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
					  "   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
					  "   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
					  "   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
					  "   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
					  "   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
					  "   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
					  "   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
					  " GROUP BY sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
					  "		   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm ";
		}
		$ls_sql="SELECT sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm,sno_thunidadadmin.depuniadm,  ".
				"    	sno_thunidadadmin.prouniadm, MAX(sno_thunidadadmin.desuniadm) AS desuniadm   ".
				"  FROM sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				" GROUP BY sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
				"		   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm  ".
				"   ".$ls_union.
				" ORDER BY minorguniadm, ofiuniadm, uniuniadm, depuniadm, prouniadm "; 
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonominaunidad_unidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonominaunidad_unidad
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaunidad_personal($as_codperdes,$as_codperhas,$as_conceptocero,$as_conceptoreporte,$as_conceptop2,
										  $as_minorguniadm,$as_ofiuniadm,$as_uniuniadm,$as_depuniadm,$as_prouniadm,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonominaunidad_personal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonomina)  
		//	    Arguments: as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos cuyo valor es cero
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	    		   as_minorguniadm // C?digo de la unidad
		//	   			   as_ofiuniadm // C?digo de la unidad
		//	   			   as_uniuniadm // C?digo de la unidad
		//	   			   as_depuniadm // C?digo de la unidad
		//	   			   as_prouniadm // C?digo de la unidad
		//	   			   as_desuniadm // Descripci?n de la unidad
		//	  			   as_orden // orden por medio del cual se desea que salga el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 07/08/2007								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_orden="";
		$ls_criteriounion="";
		if(!empty($as_codperdes))
		{
			$ls_criterio= " AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
			$ls_criteriounion=" AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
			$ls_criteriounion = $ls_criteriounion."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		$ls_criterio= $ls_criterio." AND sno_thpersonalnomina.minorguniadm='".$as_minorguniadm."'".
								   " AND sno_thpersonalnomina.ofiuniadm='".$as_ofiuniadm."' ".
								   " AND sno_thpersonalnomina.uniuniadm='".$as_uniuniadm."' ".
								   " AND sno_thpersonalnomina.depuniadm='".$as_depuniadm."' ".
								   " AND sno_thpersonalnomina.prouniadm='".$as_prouniadm."' ";
		$ls_criteriounion= $ls_criteriounion." AND sno_thpersonalnomina.minorguniadm='".$as_minorguniadm."'".
											 " AND sno_thpersonalnomina.ofiuniadm='".$as_ofiuniadm."' ".
											 " AND sno_thpersonalnomina.uniuniadm='".$as_uniuniadm."' ".
											 " AND sno_thpersonalnomina.depuniadm='".$as_depuniadm."' ".
											 " AND sno_thpersonalnomina.prouniadm='".$as_prouniadm."' ";
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
							   				"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
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
				case "1": // Ordena por C?digo de personal
					$ls_orden=" ORDER BY codper ";
					break;

				case "2": // Ordena por Apellido de personal
					$ls_orden=" ORDER BY apeper ";
					break;

				case "3": // Ordena por Nombre de personal
					$ls_orden=" ORDER BY nomper ";
					break;
			}
		}
		if($this->li_rac=="1") // Utiliza RAC
		{
			$ls_descar="       MAX((SELECT denasicar FROM sno_thasignacioncargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_thpersonalnomina.codemp = sno_thasignacioncargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thasignacioncargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thasignacioncargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thasignacioncargo.codperi ".
				       "           AND sno_thpersonalnomina.codasicar = sno_thasignacioncargo.codasicar)) as descar ";
		}
		else // No utiliza RAC
		{
			$ls_descar="      MAX((SELECT descar FROM sno_thcargo ".
					   "   	     WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					   "           AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					   "		   AND sno_thpersonalnomina.codemp = sno_thcargo.codemp ".
					   "		   AND sno_thpersonalnomina.codnom = sno_thcargo.codnom ".
					   "		   AND sno_thpersonalnomina.anocur = sno_thcargo.anocur ".
					   "		   AND sno_thpersonalnomina.codperi = sno_thcargo.codperi ".
				       "           AND sno_thpersonalnomina.codcar = sno_thcargo.codcar)) as descar ";
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_thpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "   	  sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm,  sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
					  "       sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, MAX(sno_thpersonalnomina.sueper) AS sueper, ".
					  "		  sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, ".
					  "		  MAX(sno_thpersonalnomina.codgra) AS codgra, ".
  					  $ls_descar.
					  "  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin ".
					  " WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
					  "   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
					  "   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
					  "	  AND sno_thpersonalnomina.staper = '2' ".
					  "   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_thsalida.codconc='".$ls_vac_codconvac."' ".
					  "   ".$ls_criteriounion.
					  "   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
					  "   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
					  "   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
					  "   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
					  "   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
					  "   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
					  "   AND sno_personal.codper = sno_thpersonalnomina.codper ".
					  "   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
					  "   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
					  "   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
					  "   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
					  "   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
					  "   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
					  "   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
					  "   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
					  " GROUP BY sno_thpersonalnomina.codemp, sno_thpersonalnomina.codnom, sno_thpersonalnomina.codper, ".
					  "		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
					  "		   sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.desuniadm, ".
					  "		   sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
					  "        sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, sno_thpersonalnomina.codcar, sno_thpersonalnomina.codasicar, ".
					  "		   sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
					  "    	   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm ";
		}
		$ls_sql="SELECT sno_thpersonalnomina.codper, sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
				"       sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, MAX(sno_thpersonalnomina.sueper) AS sueper, ".
				"		sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, ".
			    "		  MAX(sno_thpersonalnomina.codgra) AS codgra, ".
				$ls_descar.
				"  FROM sno_personal, sno_thpersonalnomina, sno_thsalida, sno_thunidadadmin ".
				" WHERE sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"   AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"   AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   ".$ls_criterio.
				"   AND sno_thpersonalnomina.codemp = sno_thsalida.codemp ".
				"   AND sno_thpersonalnomina.codnom = sno_thsalida.codnom ".
				"   AND sno_thpersonalnomina.anocur = sno_thsalida.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thsalida.codperi ".
				"   AND sno_thpersonalnomina.codper = sno_thsalida.codper ".
				"   AND sno_personal.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_personal.codper = sno_thpersonalnomina.codper ".
				"   AND sno_thpersonalnomina.codemp = sno_thunidadadmin.codemp ".
				"   AND sno_thpersonalnomina.anocur = sno_thunidadadmin.anocur ".
				"   AND sno_thpersonalnomina.codperi = sno_thunidadadmin.codperi ".
				"   AND sno_thpersonalnomina.minorguniadm = sno_thunidadadmin.minorguniadm ".
				"   AND sno_thpersonalnomina.ofiuniadm = sno_thunidadadmin.ofiuniadm ".
				"   AND sno_thpersonalnomina.uniuniadm = sno_thunidadadmin.uniuniadm ".
				"   AND sno_thpersonalnomina.depuniadm = sno_thunidadadmin.depuniadm ".
				"   AND sno_thpersonalnomina.prouniadm = sno_thunidadadmin.prouniadm ".
				" GROUP BY sno_thpersonalnomina.codemp, sno_thpersonalnomina.codnom, sno_thpersonalnomina.codper, ".
				"		   sno_personal.cedper, sno_personal.nomper, sno_personal.apeper, sno_personal.fecingper, ".
				"		   sno_thpersonalnomina.codcueban, sno_thunidadadmin.desuniadm, sno_thunidadadmin.desuniadm, ".
				"		   sno_thunidadadmin.codestpro1, sno_thunidadadmin.codestpro2, sno_thunidadadmin.codestpro3, ".
				"          sno_thunidadadmin.codestpro4, sno_thunidadadmin.codestpro5, sno_thpersonalnomina.codcar, sno_thpersonalnomina.codasicar, ".
				"		   sno_thunidadadmin.minorguniadm, sno_thunidadadmin.ofiuniadm, sno_thunidadadmin.uniuniadm, ".
			    "    	   sno_thunidadadmin.depuniadm, sno_thunidadadmin.prouniadm, sno_personal.codper ".
				"   ".$ls_union.
				"   ".$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonominaunidad_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonominaunidad_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_pagonominaunidad_conceptopersonal($as_codper,$as_conceptocero,$as_tituloconcepto,$as_conceptoreporte,$as_conceptop2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_pagonominaunidad_conceptopersonal
		//         Access: public (desde la clase sigesp_sno_rpp_pagonominaunidadadmin)  
		//	    Arguments: as_codper // C?digo del personal que se desea buscar la salida
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos en cero
		//	  			   as_tituloconcepto // criterio que me indica si se desea mostrar el t?tulo del concepto ? el nombre
		//	  			   as_conceptoreporte // criterio que me indica si se desea mostrar los conceptos tipo reporte
		//	  			   as_conceptop2 // criterio que me indica si se desea mostrar los conceptos de tipo aporte patronal
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos asociados al personal que se le calcul? la n?mina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 08/08/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_campo="sno_thconcepto.nomcon";
		if(!empty($as_tituloconcepto))
		{
			$ls_campo = "sno_thconcepto.titcon";
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = "AND sno_thsalida.valsal<>0 ";
		}
		if(!empty($as_conceptoreporte))
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='R')";
			}
		}
		else
		{
			if(!empty($as_conceptop2))
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3' OR ".
											"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4') ";
			}
			else
			{
				$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
											"	   sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".
											"      sno_thsalida.tipsal='P1' OR sno_thsalida.tipsal='V3' OR sno_thsalida.tipsal='W3')";
			}
		}
		$ls_union="";
		$li_vac_reportar=trim($this->uf_select_config("SNO","NOMINA","MOSTRAR VACACION","0","C"));
		$ls_vac_codconvac=trim($this->uf_select_config("SNO","NOMINA","COD CONCEPTO VACACION","","C"));
		if(($li_vac_reportar==1)&&($ls_vac_codconvac!=""))
		{
			$ls_union="UNION ".
					  "SELECT sno_thconcepto.codconc, ".$ls_campo." as nomcon, sno_thsalida.valsal, sno_thsalida.tipsal ".
					  "  FROM sno_thsalida, sno_thconcepto, sno_thpersonalnomina ".
					  " WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
					  "   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
					  "   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
					  "   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
					  "   AND sno_thsalida.codper='".$as_codper."'".
					  "   AND sno_thsalida.codconc='".$ls_vac_codconvac."'".
					  "   AND sno_thpersonalnomina.staper = '2' ".
					  "   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
					  "   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
					  "   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
					  "   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
					  "   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
					  "   AND sno_thsalida.codemp = sno_thpersonalnomina.codemp ".
					  "   AND sno_thsalida.codnom = sno_thpersonalnomina.codnom ".
					  "   AND sno_thsalida.anocur = sno_thpersonalnomina.anocur ".
					  "   AND sno_thsalida.codperi = sno_thpersonalnomina.codperi ".
					  "   AND sno_thsalida.codper = sno_thpersonalnomina.codper ";
		}
		$ls_sql="SELECT sno_thconcepto.codconc, ".$ls_campo." as nomcon, sno_thsalida.valsal, sno_thsalida.tipsal ".
				"  FROM sno_thsalida, sno_thconcepto ".
				" WHERE sno_thsalida.codemp='".$this->ls_codemp."' ".
				"   AND sno_thsalida.codnom='".$this->ls_codnom."' ".
				"   AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thsalida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thsalida.codper='".$as_codper."'".
				"   ".$ls_criterio.
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp ".
				"   AND sno_thsalida.codnom = sno_thconcepto.codnom ".
				"   AND sno_thsalida.anocur = sno_thconcepto.anocur ".
				"   AND sno_thsalida.codperi = sno_thconcepto.codperi ".
				"   AND sno_thsalida.codconc = sno_thconcepto.codconc ".
				"   ".$ls_union.
				" ORDER BY codconc ";
		$this->rs_data_detalle2=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle2===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_pagonominaunidad_conceptopersonal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_pagonominaunidad_conceptopersonal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listado_asignaciocargo($as_coddes,$as_codhas,$as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listado_asignaciocargo
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los cargos asigandos por n?mina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 29/04/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
			
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"];		
		if(!empty($as_coddes))
		{
		  if (!empty($as_codhas))
		   {
		     	$ls_criterio = " and sno_thasignacioncargo.codasicar BETWEEN '".$as_coddes."' and '".$as_codhas."'";
		   }
		}		
		
		switch($as_orden)
			{
				case "1": // Ordena por C?digo de Asignaci?n de Cargo
					$ls_orden=" ORDER BY sno_thasignacioncargo.codasicar ";
					break;

				case "2": // Ordena por el Nombre de la Asignaci?n de Cargo
					$ls_orden=" ORDER BY sno_thasignacioncargo.denasicar ";
					break;

				
			}
			
				$ls_sql=" SELECT sno_thasignacioncargo.codasicar, sno_thasignacioncargo.codnom,sno_thasignacioncargo.denasicar, ".
		        		" sno_thasignacioncargo.codtab, ".
       					" sno_thasignacioncargo.codgra, sno_thasignacioncargo.codpas, sno_thasignacioncargo.grado, ". 
       					" sno_thunidadadmin.minorguniadm,sno_thunidadadmin.ofiuniadm,sno_thunidadadmin.uniuniadm, ".
						" sno_thunidadadmin.depuniadm, ".
               		    " sno_thunidadadmin.prouniadm, sno_thunidadadmin.desuniadm, ".
               		    " sno_thtabulador.destab,sno_thasignacioncargo.numvacasicar, ".
                		" (SELECT count (sno_thpersonalnomina.codasicar) from sno_thpersonalnomina ".  
                		"         WHERE sno_thpersonalnomina.codasicar=sno_thasignacioncargo.codasicar ".
                		"         AND sno_thpersonalnomina.codnom=sno_thasignacioncargo.codnom ".
                		"         AND sno_thpersonalnomina.codemp=sno_thasignacioncargo.codemp) as ocupado ".
                		"  FROM sno_thasignacioncargo   ".
                		"  JOIN sno_thunidadadmin on (sno_thasignacioncargo.codemp=sno_thunidadadmin.codemp  ".
                        "        AND sno_thasignacioncargo.codnom=sno_thunidadadmin.codnom  ".
                        "        AND sno_thasignacioncargo.anocur=sno_thunidadadmin.anocur   ".
                        "        AND sno_thasignacioncargo.uniuniadm=sno_thunidadadmin.uniuniadm  ".
                        "        AND sno_thasignacioncargo.minorguniadm=sno_thunidadadmin.minorguniadm  ".
                        "        AND sno_thasignacioncargo.ofiuniadm=sno_thunidadadmin.ofiuniadm  ".
                        "        AND sno_thasignacioncargo.depuniadm=sno_thunidadadmin.depuniadm  ".
                        "        AND sno_thasignacioncargo.prouniadm=sno_thunidadadmin.prouniadm)  ".
         				" JOIN sno_thtabulador on (sno_thasignacioncargo.codtab=sno_thtabulador.codtab  ".
                        "      AND sno_thasignacioncargo.codemp=sno_thtabulador.codemp     ".
                        "      AND sno_thasignacioncargo.codnom=sno_thtabulador.codnom     ".
                        "      AND sno_thasignacioncargo.codperi=sno_thtabulador.codperi   ".
                        "      and sno_thasignacioncargo.anocur=sno_thtabulador.anocur)    ".
   						" WHERE sno_thasignacioncargo.codnom='".$ls_codnom."'". 
						"   and  sno_thasignacioncargo.codemp='".$ls_codemp."'".
						"   and  sno_thasignacioncargo.anocur='".$this->ls_anocurnom."' ".
						"   and  sno_thasignacioncargo.codperi='".$this->ls_peractnom."' ".$ls_criterio.$ls_orden; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listado_asignaciocargo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_asigna->data=$this->io_sql->obtener_datos($rs_data);		
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_prenomina_conceptopersonal
	
//--------------------------------------------------------------------------------------------------------------------------------	
function uf_seleccionar_quincenas($as_codper,$as_priqui,$as_segqui)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_ipsfa)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de la primera y segunda quincena de la nomina de una persona
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 21/05/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"]; 	
		
				$ls_sql=" SELECT priquires, segquires         ".
				        " FROM sno_thresumen                    ".
						" WHERE sno_thresumen.codemp='".$ls_codemp."'         ". 
						" AND sno_thresumen.codper='".$as_codper."'  ".
						" AND sno_thresumen.codperi='".$ls_peractnom."'       ".
						" AND sno_thresumen.codnom='".$ls_codnom."'       ";  
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_seleccionar_quincenas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
//--------------------------------------------------------------------------------------------------------------------------------
     function uf_obtener_valor_concepto($as_codper,$as_concepto,$as_valor)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_obtener_valor_concepto
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_ipsfa)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de la primera y segunda quincena de la nomina de una persona
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 21/05/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
		$ls_peractnom=$_SESSION["la_nomina"]["peractnom"]; 	
		
				$ls_sql=" SELECT sno_thconcepto.codconc, sno_thconcepto.titcon as nomcon, sno_thsalida.valsal  ".
						"	FROM sno_thsalida, sno_thconcepto ".
						"		WHERE sno_thsalida.codemp='".$ls_codemp."' ". 
						"		AND sno_thsalida.codnom='".$ls_codnom."'  ". 
						"		AND sno_thsalida.codperi='".$ls_peractnom."' ". 
						"		AND sno_thconcepto.codconc='".$as_concepto."' ".
						"		AND sno_thsalida.codper='".$as_codper."' ". 
						"		AND sno_thsalida.valsal<>0 ".
						"		AND sno_thsalida.codemp = sno_thconcepto.codemp ".
						"		AND sno_thsalida.codnom = sno_thconcepto.codnom ".
						"		AND sno_thsalida.codconc = sno_thconcepto.codconc ".
						"		ORDER BY sno_thconcepto.codconc   ";  
       
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_obtener_valor_concepto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_valor=$row["valsal"];
						
			}
			else
			{
				$lb_valido=false;
				$as_valor="";				
			}
			$this->io_sql->free_result($rs_data);
		}		
		$arrResultado['as_valor']=$as_valor;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_obtener_valor_concepto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_recibo_nomina_oficiales($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal oficial
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 14/05/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
			
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
						"       and sno_personalnomina.codnom='".$ls_codnom."') as fecingnom, ".
						"	    sno_componente.descom, sno_rango.desran ".
						"  FROM sno_personalpension ".
						"  JOIN sno_personal ON (sno_personal.codemp=sno_personalpension.codemp ".
						"				   AND  sno_personal.codper=sno_personalpension.codper) ".
						"  LEFT JOIN sno_componente ON (sno_componente.codemp= sno_personal.codemp ".
						"						   AND sno_componente.codcom= sno_personal.codcom) ".
						"  LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp ".
						"					 AND  sno_rango.codcom=sno_personal.codcom  ".
						"					 AND  sno_rango.codran=sno_personal.codran) ".
						" WHERE sno_personalpension.codemp='".$ls_codemp."'".
						" AND	sno_personalpension.codper='".$as_codper."'".
						" AND	sno_personalpension.codnom='".$ls_codnom."'";       
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_recibo_nomina_oficiales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibo_nomina_oficiales

	//--------------------------------------------------------------------------------------------------------------------------------	
	function uf_recibo_nomina_oficiales_2($as_codper)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_recibo_nomina_oficiales_2
		//         Access: public (desde la clase sigesp_sno_rpp_prenomina)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n del personal oficial
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 14/05/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_codnom=$_SESSION["la_nomina"]["codnom"];
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];			
			
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
						"       and sno_personalnomina.codnom='".$ls_codnom."') as fecingnom, ".
						"	    sno_componente.descom, sno_rango.desran, ".
						"      (SELECT sno_categoria_rango.descat FROM sno_categoria_rango    ".
						"        WHERE sno_categoria_rango.codemp=sno_rango.codemp            ".
						"          AND sno_categoria_rango.codcat=sno_rango.codcat) as descat ".
						"  FROM sno_personalpension ".
						"  JOIN sno_personal ON (sno_personal.codemp=sno_personalpension.codemp ".
						"				   AND  sno_personal.codper=sno_personalpension.codper) ".
						"  LEFT JOIN sno_componente ON (sno_componente.codemp= sno_personal.codemp ".
						"						   AND sno_componente.codcom= sno_personal.codcom) ".
						"  LEFT JOIN sno_rango ON (sno_rango.codemp=sno_personal.codemp ".
						"					 AND  sno_rango.codcom=sno_personal.codcom  ".
						"					 AND  sno_rango.codran=sno_personal.codran) ".
						" WHERE sno_personalpension.codemp='".$ls_codemp."'".
						" AND	sno_personalpension.codper='".$as_codper."'".
						" AND	sno_personalpension.codnom='".$ls_codnom."'";       
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_recibo_nomina_oficiales_2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_recibo_nomina_oficiales

	 //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_buscar_beneficiarios($as_codbendes, $as_codbenhas, $as_codperdes, $as_codperhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_beneficiarios
		//         Access: public (desde la clase sigesp_sno_rpp_recibopago_beneficiario)  
		//	    Arguments: 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los beneficiarios
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 26/06/2008								Fecha ?ltima Modificaci?n :  
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
		$ls_sql=" SELECT sno_beneficiario.codper, sno_beneficiario.codben,  sno_beneficiario.cedben,         ".
                "        sno_beneficiario.nomben, sno_beneficiario.apeben,  sno_beneficiario.porpagben,      ".
                "        sno_beneficiario.codban, sno_beneficiario.ctaban,  sno_beneficiario.tipcueben,      ".
				"        sno_beneficiario.nexben, sno_beneficiario.nomcheben, sno_beneficiario.cedaut,       ".
				"        (SELECT sno_personal.fecnacper FROM sno_personal ".
				"          WHERE sno_personal.codemp='".$ls_codemp."'".
				"            AND sno_personal.cedper=sno_beneficiario.cedben) as fecnacben,        ".
				"        (SELECT scb_banco.nomban FROM scb_banco WHERE scb_banco.codemp='".$ls_codemp."'     ".
				"            AND scb_banco.codban=sno_beneficiario.codban) AS banco                          ".
                " FROM sno_beneficiario                                                                      ".
                " WHERE sno_beneficiario.codemp='".$ls_codemp."'".$ls_criterio.
				" ORDER BY sno_beneficiario.codper, sno_beneficiario.codben";           
       
		$this->rs_data_detalle2=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle2===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_buscar_beneficiarios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_buscar_beneficiarios
	//----------------------------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------------------------------
    function uf_cuadre_concepto_pensiones($as_codconcdes,$as_codconchas,$as_conceptocero,$as_subnomdes,$as_subnomhas,$fecha,$criteriodefecha)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadre_concepto_pensiones
		//         Access: public (desde la clase sigesp_sno_r_cuadrenomina_pensiones)  
		//	    Arguments: as_codconcdes // C?digo del concepto donde se empieza a filtrar
		//				   as_codconchas // C?digo del concepto donde se termina de filtrar
		//	  			   as_conceptocero // criterio que me indica si se desea quitar los conceptos que tienen monto cero
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de los conceptos que se calcul? la n?mina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creaci?n: 18/07/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";		
		$ls_criteriopersonalnomina="";		
		$ls_criterio= $ls_criterio."	     ON sno_thsalida.codemp='".$this->ls_codemp."'  ".
								   "		AND sno_thsalida.codnom='".$this->ls_codnom."'  ".
								   "        AND sno_thsalida.anocur='".$this->ls_anocurnom."' ".
								   "		AND sno_thsalida.codperi='".$this->ls_peractnom."'  ";
		if(!empty($as_codconcdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thsalida.codconc>='".$as_codconcdes."'";			
		}
		if(!empty($as_codconchas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thsalida.codconc<='".$as_codconchas."'";			
		}
		if(!empty($as_conceptocero))
		{
			$ls_criterio = $ls_criterio."   AND sno_thsalida.valsal<>0 ";			
		}
		if(!empty($as_aportepatronal))
		{
			$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1' OR ".
										"      sno_thsalida.tipsal='D' OR sno_thsalida.tipsal='V2' OR sno_thsalida.tipsal='W2' OR ".		
										"	   sno_thsalida.tipsal='P2' OR sno_thsalida.tipsal='V4' OR sno_thsalida.tipsal='W4')";
		}
		else
		{
			$ls_criterio = $ls_criterio." AND (sno_thsalida.tipsal='A' OR sno_thsalida.tipsal='V1' OR sno_thsalida.tipsal='W1')";
		}		
		if(!empty($as_subnomdes))
		{
			$ls_criteriopersonalnomina= $ls_criteriopersonalnomina."   AND sno_thpersonalnomina.codsubnom>='".$as_subnomdes."'";			
		}
		if(!empty($as_subnomhas))
		{
			$ls_criteriopersonalnomina= $ls_criteriopersonalnomina."   AND sno_thpersonalnomina.codsubnom<='".$as_subnomhas."'";			
		}
		$ls_sql="SELECT  sno_thconcepto.codconc, MAX(sno_thconcepto.nomcon) AS nomcon, sno_thsalida.tipsal, sum(sno_thsalida.valsal) as monto, COUNT(sno_thsalida.codper) AS total						    ".	
				"  FROM sno_thsalida ".
				" INNER JOIN sno_thconcepto ".
				"  ".$ls_criterio.
				"   AND sno_thsalida.codemp = sno_thconcepto.codemp                     ".
				"	AND sno_thsalida.codnom = sno_thconcepto.codnom                     ".
				"	AND sno_thsalida.anocur = sno_thconcepto.anocur                     ".
				"	AND sno_thsalida.codnom = sno_thconcepto.codnom                     ".
				"	AND sno_thsalida.codperi = sno_thconcepto.codperi                   ".
				" INNER JOIN (sno_thpersonalnomina ".
				"           INNER JOIN sno_personal  ". 
				"		       ON  ".$criteriodefecha.
				"             AND sno_personal.codemp = sno_thpersonalnomina.codemp   ".
				"			  AND sno_personal.codper = sno_thpersonalnomina.codper)  ".
				"	".$ls_criterio.
				"        AND sno_thsalida.codemp = sno_thpersonalnomina.codemp               ".
				"		 AND sno_thsalida.codnom = sno_thpersonalnomina.codnom               ".
				"	     AND sno_thsalida.anocur = sno_thpersonalnomina.anocur                     ".
				"	     AND sno_thsalida.codnom = sno_thpersonalnomina.codnom                     ".
				"		 AND sno_thsalida.codper = sno_thpersonalnomina.codper               ".
				$ls_criteriopersonalnomina.
				" GROUP BY sno_thconcepto.codconc, sno_thsalida.tipsal  ".
				" ORDER BY sno_thconcepto.codconc, sno_thsalida.tipsal                "; 
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadre_concepto_pensiones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end uf_cuadrenomina_concepto_pensiones	
	//---------------------------------------------------------------------------------------------------------------------
	
	//---------------------------------------------------------------------------------------------------------------------
	function uf_buscar_codigos_unico_rac($as_codasicar,$rs_data)
    {  
	    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_codigos_unico_rac
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
        //    Description: funci?n que busca la informaci?n de las c?digos unicos asociados a una asignaci?n de cargo
        //       Creado Por: Ing. Mar?a Beatriz Unda
        // Fecha Creaci?n: 03/11/2008                                 Fecha ?ltima Modificaci?n :          
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
        $ls_sql="SELECT codunirac, estcodunirac    ".                
                "  FROM sno_thcodigounicorac ".                
                " WHERE sno_thcodigounicorac.codemp='".$ls_codemp."'  ".
				"   AND sno_thcodigounicorac.codnom='".$this->ls_codnom."' ".
				"   AND sno_thcodigounicorac.codperi='".$this->ls_peractnom."'  ". 
				"   AND sno_thcodigounicorac.codasicar='".$as_codasicar."' ".
                " ORDER BY codunirac";  
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report M?TODO->uf_buscar_codigos_unico_rac ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_buscar_codigos_unico_rac
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_cuotas ($as_codcon,$as_codper,$as_cuota)
    {   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_cuotas
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
        //    Description: funci?n que busca la informaci?n de las c?digos unicos asociados a una asignaci?n de cargo
        //       Creado Por: Ing. Mar?a Beatriz Unda
        // Fecha Creaci?n: 08/12/2008                                 Fecha ?ltima Modificaci?n :          
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$as_cuota="";            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
                    
        $ls_sql=" SELECT moncon, montopcon   ".                
                "  FROM sno_thconstantepersonal ".                
                "  WHERE sno_thconstantepersonal.codemp='".$ls_codemp."'  ".
				"	  AND sno_thconstantepersonal.codnom='".$this->ls_codnom."' ".
				"	  AND sno_thconstantepersonal.codperi='".$this->ls_peractnom."'  ". 
				"	  AND sno_thconstantepersonal.codcons='".$as_codcon."' ".
				"	  AND sno_thconstantepersonal.codper='".$as_codper."' ";  
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report M?TODO->uf_buscar_cuotas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
		else
		{
			if(!$rs_data->EOF)
			{
				 $as_cuota=$rs_data->fields["moncon"]."/".$rs_data->fields["montopcon"];
				 
				 $rs_data->MoveNext();
			}
		}
		$arrResultado['as_cuota']=$as_cuota;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_buscar_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_ubicacion_fisica($as_codorg)
   	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_ubicacion_fisica
		//		   Access: public
		//	  Description: Funci?n que obtiene ela ubicacion f?sica del personal seg?n el organigrama
		//	   Creado Por: Ing. Mar?a Beatriz Unda
		// Fecha Creaci?n: 09/01/2009 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_ubifis="";
		$lb_valido=true;
		
		$ls_sql="SELECT codorg, desorg, nivorg, padorg ".				
				"  FROM srh_organigrama ".
				" WHERE srh_organigrama.codemp='".$this->ls_codemp."' ".
				"   AND srh_organigrama.codorg='".$as_codorg."' ".
				"   AND srh_organigrama.codorg <> '----------' ";	
											
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("ERROR->".$io_funciones->uf_convertirmsg($io_sql->message)); 
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
		//	  Description: Funci?n que obtiene e imprime los conceptos a pagar por encargadur?a
		//	   Creado Por: Ing. Mar?a Beatriz Unda
		// Fecha Creaci?n: 05/01/2009 								Fecha ?ltima Modificaci?n : 
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
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadohojatiempo($as_codhordes,$as_codhorhas,$as_codperdes,$as_codperhas,$as_coduniadm,$ad_fecdes,$ad_fechas,$as_esthojtie,
								  $as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadohojatiempo
		//         Access: public (desde la clase sigesp_sno_rpp_listadohojatiempo)  
		//	    Arguments: as_codhordes // C?digo del concepto donde se empieza a filtrar
		//				   as_codhorhas // C?digo del concepto donde se termina de filtrar
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // C?digo de la unidad administrativa que se desea filtrar
		//	  			   as_orden //  Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las hojas de tiempo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 07/02/2011 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_aux="";
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		if($as_esthojtie!="")
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.esthojtie='".$as_esthojtie."'";
		}
		if(!empty($as_codhordes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.codhor>='".$as_codhordes."'";
		}
		if(!empty($as_codhorhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.codhor<='".$as_codhorhas."'";
		}
		if(!empty($as_codperdes))
		{
			$ls_criterio2= $ls_criterio2."   AND sno_thpersonalnomina.codper>='".$as_codperdes."'";
		}
		if(!empty($as_codperhas))
		{
			$ls_criterio2= $ls_criterio2."   AND sno_thpersonalnomina.codper<='".$as_codperhas."'";
		}
		if(!empty($as_coduniadm))
		{
			$ls_criterio2 = $ls_criterio2."   AND sno_thpersonalnomina.minorguniadm='".substr($as_coduniadm,0,4)."' ";
			$ls_criterio2 = $ls_criterio2."   AND sno_thpersonalnomina.ofiuniadm='".substr($as_coduniadm,5,2)."' ";
			$ls_criterio2 = $ls_criterio2."   AND sno_thpersonalnomina.uniuniadm='".substr($as_coduniadm,8,2)."' ";
			$ls_criterio2 = $ls_criterio2."   AND sno_thpersonalnomina.depuniadm='".substr($as_coduniadm,11,2)."' ";
			$ls_criterio2 = $ls_criterio2."   AND sno_thpersonalnomina.prouniadm='".substr($as_coduniadm,14,2)."' ";
		}
		$ls_orden="ORDER BY sno_thhojatiempo.fechojtie";
		switch($as_orden)
		{
			case "1": // Ordena por C?digo de personal
				$ls_orden="ORDER BY sno_thhojatiempo.codper ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="ORDER BY sno_personal.apeper ";
				break;

			case "3": // Ordena por Nombre del personal
				$ls_orden="ORDER BY sno_personal.nomper ";
				break;

			case "4": // Ordena por c?dula del personal
				$ls_orden="ORDER BY sno_personal.cedper ";
				break;
	 	}
		$ls_sql="SELECT sno_thhojatiempo.codper, MAX(sno_personal.apeper) AS apeper, MAX(sno_personal.nomper) AS nomper, MAX(sno_personal.cedper) AS cedper ".
				"  FROM sno_thhojatiempo ".
				" INNER JOIN (sno_thpersonalnomina ".
				"      INNER JOIN sno_personal ".
				"         ON sno_thpersonalnomina.codemp='".$this->ls_codemp."' ".
				"        AND sno_thpersonalnomina.codnom='".$this->ls_codnom."' ".
				"        AND sno_thpersonalnomina.anocur='".$this->ls_anocurnom."' ".
				"        AND sno_thpersonalnomina.codperi='".$this->ls_peractnom."' ".
				$ls_criterio2.
				"        AND sno_thpersonalnomina.codemp=sno_personal.codemp ".
				"        AND sno_thpersonalnomina.codper=sno_personal.codper) ".
				"    ON sno_thhojatiempo.codemp='".$this->ls_codemp."' ".
				"   AND sno_thhojatiempo.codnom='".$this->ls_codnom."' ".
				"   AND sno_thhojatiempo.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thhojatiempo.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thhojatiempo.fechojtie>='".$ad_fecdes."' ".
				"   AND sno_thhojatiempo.fechojtie<='".$ad_fechas."' ".
				$ls_criterio.
				"   AND sno_thhojatiempo.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thhojatiempo.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thhojatiempo.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thhojatiempo.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thhojatiempo.codper = sno_thpersonalnomina.codper ".
				" GROUP BY sno_thhojatiempo.codper ".
				"  ".$ls_orden;
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadohojatiempo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_listadohojatiempo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_listadohojatiempo_personal($as_codhordes,$as_codhorhas,$as_codper,$ad_fecdes,$ad_fechas,$as_esthojtie)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_listadohojatiempo_personal
		//         Access: public (desde la clase sigesp_sno_rpp_listadohojatiempo)  
		//	    Arguments: as_codhordes // C?digo del concepto donde se empieza a filtrar
		//				   as_codhorhas // C?digo del concepto donde se termina de filtrar
		//				   as_codperdes // C?digo del personal donde se empieza a filtrar
		//	  			   as_codperhas // C?digo del personal donde se termina de filtrar		  
		//	  			   as_coduniadm // C?digo de la unidad administrativa que se desea filtrar
		//	  			   as_orden //  Orden del reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las hojas de tiempo
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 07/02/2011 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_aux="";
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		if($as_esthojtie!="")
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.esthojtie='".$as_esthojtie."'";
		}
		if(!empty($as_codhordes))
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.codhor>='".$as_codhordes."'";
		}
		if(!empty($as_codhorhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.codhor<='".$as_codhorhas."'";
		}
		if(!empty($as_codper))
		{
			$ls_criterio= $ls_criterio."   AND sno_thhojatiempo.codper>='".$as_codper."'";
		}
		$ls_orden="ORDER BY sno_thhojatiempo.fechojtie";
		$ls_sql="SELECT sno_thhojatiempo.semhojtie, sno_thhojatiempo.fechojtie, sno_thhojatiempo.esthojtie, sno_thhojatiempo.codhor, ".
				"		sno_thhojatiempo.horlab, sno_thhojatiempo.horextlab, sno_thhojatiempo.trasub, sno_thhojatiempo.traesc, sno_thhojatiempo.repcom, ".
				"		sno_thhorario.tiphor, sno_thhorario.horini, sno_thhorario.horfin  ".
				"  FROM sno_thhojatiempo ".
				" INNER JOIN sno_thhorario ".
				"    ON sno_thhojatiempo.codemp='".$this->ls_codemp."' ".
				"   AND sno_thhojatiempo.codnom='".$this->ls_codnom."' ".
				"   AND sno_thhojatiempo.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thhojatiempo.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thhojatiempo.fechojtie>='".$ad_fecdes."' ".
				"   AND sno_thhojatiempo.fechojtie<='".$ad_fechas."' ".
				$ls_criterio.
				"   AND sno_thhojatiempo.codemp = sno_thhorario.codemp ".
				"   AND sno_thhojatiempo.codnom = sno_thhorario.codnom ".
				"   AND sno_thhojatiempo.anocur = sno_thhorario.anocur ".
				"   AND sno_thhojatiempo.codperi = sno_thhorario.codperi ".
				"   AND sno_thhojatiempo.codhor = sno_thhorario.codhor ".
				" INNER JOIN sno_thpersonalnomina ".
				"    ON sno_thhojatiempo.codemp='".$this->ls_codemp."' ".
				"   AND sno_thhojatiempo.codnom='".$this->ls_codnom."' ".
				"   AND sno_thhojatiempo.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_thhojatiempo.codperi='".$this->ls_peractnom."' ".
				"   AND sno_thhojatiempo.fechojtie>='".$ad_fecdes."' ".
				"   AND sno_thhojatiempo.fechojtie<='".$ad_fechas."' ".
				$ls_criterio.
				"   AND sno_thhojatiempo.codemp = sno_thpersonalnomina.codemp ".
				"   AND sno_thhojatiempo.codnom = sno_thpersonalnomina.codnom ".
				"   AND sno_thhojatiempo.anocur = sno_thpersonalnomina.anocur ".
				"   AND sno_thhojatiempo.codperi = sno_thpersonalnomina.codperi ".
				"   AND sno_thhojatiempo.codper = sno_thpersonalnomina.codper ".
				"  ".$ls_orden;
		$this->rs_data_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_data_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_listadohojatiempo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_data_detalle->EOF)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_listadohojatiempo_personal
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_cuotas_general ($as_codcon,$as_codper,$as_cuota)
    {   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_cuotas
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
        //    Description: funci?n que busca la informaci?n de las cuotas asociadas a las constantes de los conceptos 
        //       Creado Por: Ing. Mar?a Beatriz Unda
        // Fecha Creaci?n: 08/12/2008                                 Fecha ?ltima Modificaci?n :          
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$as_cuota="";            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
        $ls_sql=" SELECT moncon, montopcon   ".                
                "   FROM sno_constantepersonal ".                
                "  WHERE sno_constantepersonal.codemp='".$ls_codemp."'  ".
				"	 AND sno_constantepersonal.codnom='".$this->ls_codnom."' ".
				"	 AND sno_constantepersonal.codcons='".$as_codcon."' ".
				"	 AND sno_constantepersonal.codper='".$as_codper."' "; 
		$rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report M?TODO->uf_buscar_cuotas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
		else
		{
			while(!$rs_data->EOF)
			{
				 $as_cuota=$rs_data->fields["montopcon"];
				 $rs_data->MoveNext();
			}
		}
		$arrResultado['as_cuota']=$as_cuota;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_buscar_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_valor_conc ($as_codcon,$as_codper,$as_cuota)
    {   
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //       Function: uf_buscar_cuotas
        //        Arguments: 
        //          Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
        //    Description: funci?n que busca la informaci?n de las cuotas asociadas a las constantes de los conceptos 
        //       Creado Por: Ing. Mar?a Beatriz Unda
        // Fecha Creaci?n: 08/12/2008                                 Fecha ?ltima Modificaci?n :          
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
		$as_cuota="";            
        $ls_codemp=$_SESSION["la_empresa"]["codemp"];            
        $ls_sql=" SELECT moncon, montopcon   ".                
                "   FROM sno_hconstantepersonal ".                
                "  WHERE sno_hconstantepersonal.codemp='".$ls_codemp."'  ".
				"	 AND sno_hconstantepersonal.codnom='".$this->ls_codnom."' ".
				"    AND sno_hconstantepersonal.anocur='".$this->ls_anocurnom."' ".
				"    AND sno_hconstantepersonal.codperi='".$this->ls_peractnom."' ".
				"	 AND sno_hconstantepersonal.codcons='".$as_codcon."' ".
				"	 AND sno_hconstantepersonal.codper='".$as_codper."' "; 
		$rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Report M?TODO->uf_buscar_valor_conc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $lb_valido=false;
        }
		else
		{
			while(!$rs_data->EOF)
			{
				 $as_cuota=$rs_data->fields["moncon"];
				 $rs_data->MoveNext();
			}
		}
		$arrResultado['as_cuota']=$as_cuota;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
    }// end function uf_buscar_cuotas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_prestamo_personal($as_codper,$as_codconc,$as_sigcon){
		$ls_prestamo=0;
		$ls_sql="SELECT sno_hprestamos.monpre as monto, sno_hprestamos.monamopre as monamopre ".
				"  FROM sno_hprestamos ".
				"  INNER JOIN sno_hconcepto USING (codemp,anocur,codperi,codconc) ".
				" WHERE sno_hprestamos.codemp='".$this->ls_codemp."' ".
				"   AND sno_hprestamos.codnom = '".$this->ls_codnom."'  ".
				"   AND sno_hprestamos.anocur='".$this->ls_anocurnom."' ".
				"   AND sno_hprestamos.codperi='".$this->ls_peractnom."' ".
				"   AND sno_hprestamos.codper='".$as_codper."' ".
				"   AND sno_hprestamos.codconc = '".$as_codconc."' ".
				"   AND sno_hprestamos.stapre = '1' ".
				"   AND sno_hconcepto.sigcon = '".$as_sigcon."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_prestamo_personal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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

}
?>
