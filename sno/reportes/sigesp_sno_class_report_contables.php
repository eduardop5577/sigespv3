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

class sigesp_sno_class_report_contables
{
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_class_report_contables
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/05/2006 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);	
		$this->DS=new class_datastore();
		$this->DS_detalle=new class_datastore();
		$this->DS_detalle_2=new class_datastore();
		$this->DS_print=new class_datastore();
		require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();		
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
        $this->ls_peractnom=$_SESSION["la_nomina"]["peractnom"];
		$this->li_rac=$_SESSION["la_nomina"]["racnom"];
	}// end function sigesp_sno_class_report_contables
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
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
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
    function uf_select_provbene_ctacestatik($as_codprovben_cest,$as_benprov)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_provbene_ctacestatik
		//		   Access: public
		//	    Arguments: as_codprovben_cest  // Codigo del proveedor o beneficiario
		//				   as_benprov  // Estatus para consultar en tabla de proveedores o en tabla de beneficiarios
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci?n que obtiene una variable de la tabla rpc_proveedor o rpc_beneficiario
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creaci?n: 27/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		if($as_benprov=='P')
		{
			$ls_sql=" SELECT sc_cuenta ".
					" FROM rpc_proveedor ".
					" WHERE codemp='".$this->ls_codemp."' ".
					" AND cod_pro='".$as_codprovben_cest."' ";
		}
		else
		{
			$ls_sql=" SELECT sc_cuenta ".
					" FROM rpc_beneficiario ".
					" WHERE codemp='".$this->ls_codemp."' ".
					" AND ced_bene='".$as_codprovben_cest."' ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_select_provbene_ctacestatik ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["sc_cuenta"];
				$li_i=$li_i+1;
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $ls_valor;
	}// end function uf_select_provbene_ctacestatik
	
	//-----------------------------------------------------------------------------------------------------------------------------------	
    function uf_select_clasificadorconcepto($as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_clasificadorconcepto
		//		   Access: public
		//	    Arguments: as_codcla  // Codigo del clasificador
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci?n que obtiene una variable de la tabla rpc_proveedor o rpc_beneficiario
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creaci?n: 27/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sc_cuenta="";
		$ls_denominacion="";
		$lb_valido=true;
		$ls_sql="SELECT sc_cuenta, denominacion ".
				"  FROM cxp_clasificador_rd ".
				" INNER JOIN scg_cuentas ".
				"    ON cxp_clasificador_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_clasificador_rd.codcla='".$as_codcla."' ".
				"   AND scg_cuentas.status='C' ".
				"   AND cxp_clasificador_rd.codemp=scg_cuentas.codemp ".
				"   AND cxp_clasificador_rd.sc_cuenta=scg_cuentas.sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_select_provbene_ctacestatik ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ls_sc_cuenta=$rs_data->fields["sc_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
			}
			$this->io_sql->free_result($rs_data);		
		}
		$arrResultado['sc_cuenta']=$ls_sc_cuenta;
		$arrResultado['denominacion']=$ls_denominacion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;						
	}// end function uf_select_clasificadorconcepto
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_provbene_denctacestatik($as_ctaprovben_cest)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_provbene_denctacestatik
		//		   Access: public
		//	    Arguments: as_ctaprovben_cest  // Codigo de la cuenta
		//	      Returns: $ls_resultado variable buscado
		//	  Description: Funci?n que obtiene una variable de la tabla config
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creaci?n: 27/04/2010 								Fecha ?ltima Modificaci?n : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_valor="";
		$ls_sql=" SELECT denominacion ".
				" FROM scg_cuentas ".
				" WHERE codemp='".$this->ls_codemp."' ".
				" AND sc_cuenta='".$as_ctaprovben_cest."' ";
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_select_provbene_denctacestatik ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
		}
		else
		{
			$li_i=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_valor=$row["denominacion"];
				$li_i=$li_i+1;
			}
			$this->io_sql->free_result($rs_data);		
		}
		return $ls_valor;
	}// end function uf_select_provbene_denctacestatik
	
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
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
				$this->io_mensajes->message("CLASE->Report M?TODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	function uf_contableaportes_presupuesto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 11/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla,  spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ";
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 11/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		switch($li_parametros)
		{
			case 0: // La contabilizaci?n es global
				$ls_modoaporte=$this->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$li_genrecapo=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN_APORTE","0","I"));
				$ai_estagrapo=$this->io_sno->uf_select_config("SNO","CONFIG","AGRUPAR APORTES","0","I");
				break;
			
			case 1: // La contabilizaci?n es por n?mina
				$ls_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$li_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaaltapo"]);
				$ai_estagrapo=trim($_SESSION["la_nomina"]["estagrapo"]);			
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
			$ls_sql=" SELECT cuenta, denoconta, operacion, total ".
					"   FROM conapo_contable            ".
					"  WHERE codemp='".$this->ls_codemp."'       ".
					"    AND codnom='".$this->ls_codnom."'       ".
					"    AND codperi='".$this->ls_peractnom."'   ".
					"  UNION                                     ".  
					" SELECT cuenta, denoconta, operacion, total ".
					"   FROM conapo_contable_int     ".
					"  WHERE codemp='".$this->ls_codemp."'       ".
					"    AND codnom='".$this->ls_codnom."'       ".
					"    AND codperi='".$this->ls_peractnom."'    "; 
		}
		else
		{
			$ls_sql=" SELECT cuenta, denoconta, operacion, total ".
					"   FROM contableaportes_contable            ".
					"  WHERE codemp='".$this->ls_codemp."'       ".
					"    AND codnom='".$this->ls_codnom."'       ".
					"    AND codperi='".$this->ls_peractnom."'   ".
					"  UNION                                     ".  
					" SELECT cuenta, denoconta, operacion, total ".
					"   FROM contableaportes_contable_intcom     ".
					"  WHERE codemp='".$this->ls_codemp."'       ".
					"    AND codnom='".$this->ls_codnom."'       ".
					"    AND codperi='".$this->ls_peractnom."'    "; 
		}
		if($ai_estagrapo=='1') // gererar la parte contable de las retenciones
		{
			$ls_sql=$ls_sql." UNION ".
			        "SELECT scg_cuentas.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) as denoconta, CAST('D' AS CHAR(1)) AS operacion,SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) AS total  ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas  ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND (sno_salida.tipsal='P1' OR sno_salida.tipsal='V3' OR sno_salida.tipsal='W3')  ".
					"   AND sno_concepto.sigcon='P'  ".
					"   AND sno_salida.valsal <> 0  ". 
					"   AND scg_cuentas.status = 'C'  ". 
					"   AND sno_salida.codemp = sno_concepto.codemp   ".
					"   AND sno_salida.codnom = sno_concepto.codnom   ".
					"   AND sno_salida.codconc = sno_concepto.codconc   ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp   ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon  ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp   ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom   ".
					"   AND sno_personalnomina.codper = sno_salida.codper   ".
					" GROUP BY scg_cuentas.sc_cuenta  ";
		}
		if(($ls_modoaporte=="OC")&&($li_genrecapo=="1"))
		{
			if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
			{
				// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, sno_nomina, cxp_clasificador_rd ".
						" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
						"   AND sno_salida.codnom='".$this->ls_codnom."' ".
						"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
						"   AND scg_cuentas.status = 'C' ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_concepto.codemp ".
						"   AND sno_salida.codnom = sno_concepto.codnom ".
						"   AND sno_salida.codconc = sno_concepto.codconc ".
						"	AND sno_nomina.codemp = sno_personalnomina.codemp ".
						"	AND sno_nomina.codnom = sno_personalnomina.codnom ".
						"	AND sno_nomina.codemp = cxp_clasificador_rd.codemp ".
						"	AND sno_nomina.codclaapo = cxp_clasificador_rd.codcla ".
						"   AND scg_cuentas.codemp = cxp_clasificador_rd.codemp ".
						"   AND scg_cuentas.sc_cuenta = cxp_clasificador_rd.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else
			{
				// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_proveedor ".
						" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
						"   AND sno_salida.codnom='".$this->ls_codnom."' ".
						"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
						"   AND scg_cuentas.status = 'C' ".
						"   AND sno_concepto.codprov <> '----------' ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_concepto.codemp ".
						"   AND sno_salida.codnom = sno_concepto.codnom ".
						"   AND sno_salida.codconc = sno_concepto.codconc ".
						"	AND sno_concepto.codemp = rpc_proveedor.codemp ".
						"	AND sno_concepto.codprov = rpc_proveedor.cod_pro ".
						"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
						"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_beneficiario ".
						" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
						"   AND sno_salida.codnom='".$this->ls_codnom."' ".
						"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
						"   AND scg_cuentas.status = 'C' ".
						"   AND sno_concepto.cedben <> '----------' ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_concepto.codemp ".
						"   AND sno_salida.codnom = sno_concepto.codnom ".
						"   AND sno_salida.codconc = sno_concepto.codconc ".
						"	AND sno_concepto.codemp = rpc_beneficiario.codemp ".
						"	AND sno_concepto.cedben = rpc_beneficiario.ced_bene ".
						"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
						"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				}
				if($ai_estagrapo=='1') // gererar la parte contable de las retenciones
				{
					$ls_sql=$ls_sql." UNION ".
						   "SELECT scg_cuentas.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion)||' ' as denoconta, CAST('H' AS CHAR(1)) AS operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) AS total ".
						   "  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_beneficiario  ".
						   " WHERE sno_salida.codemp='".$this->ls_codemp."' ".
						   "   AND sno_salida.codnom='".$this->ls_codnom."' ".
						   "   AND sno_salida.codperi='".$this->ls_peractnom."' ".
						   "   AND (sno_salida.tipsal='P1' OR sno_salida.tipsal='V3' OR sno_salida.tipsal='W3')  ".
						   "   AND sno_concepto.sigcon='P' ".
						   "   AND sno_salida.valsal <> 0  ".
						   "   AND scg_cuentas.status = 'C'  ".
						   "   AND sno_salida.codemp = sno_concepto.codemp  ".
						   "   AND sno_salida.codnom = sno_concepto.codnom  ".
						   "   AND sno_salida.codconc = sno_concepto.codconc  ".
						   "   AND rpc_beneficiario.codemp = sno_concepto.codemp ". 
						   "   AND rpc_beneficiario.ced_bene = sno_concepto.cedben   ".
						   "   AND scg_cuentas.codemp = rpc_beneficiario.codemp  ".
						   "   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
						   "   AND sno_personalnomina.codemp = sno_salida.codemp  ".
						   "   AND sno_personalnomina.codnom = sno_salida.codnom  ".
						   "   AND sno_personalnomina.codper = sno_salida.codper  ".
						   " GROUP BY scg_cuentas.sc_cuenta ";
						   " UNION ".
						   "SELECT scg_cuentas.sc_cuenta AS cuenta, MAX(scg_cuentas.denominacion) as denoconta, CAST('H' AS CHAR(1)) AS operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) AS total ".
						   "  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_proveedor  ".
						   " WHERE sno_salida.codemp='".$this->ls_codemp."' ".
						   "   AND sno_salida.codnom='".$this->ls_codnom."' ".
						   "   AND sno_salida.codperi='".$this->ls_peractnom."' ".
						   "   AND (sno_salida.tipsal='P1' OR sno_salida.tipsal='V3' OR sno_salida.tipsal='W3')  ".
						   "   AND sno_concepto.sigcon='P' ".
						   "   AND sno_salida.valsal <> 0  ".
						   "   AND scg_cuentas.status = 'C'  ".
						   "   AND sno_salida.codemp = sno_concepto.codemp  ".
						   "   AND sno_salida.codnom = sno_concepto.codnom  ".
						   "   AND sno_salida.codconc = sno_concepto.codconc  ".
						   "   AND rpc_proveedor.codemp = sno_concepto.codemp ". 
						   "   AND rpc_proveedor.cod_pro = sno_concepto.codprov   ".
						   "   AND scg_cuentas.codemp = rpc_proveedor.codemp  ".
						   "   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
						   "   AND sno_personalnomina.codemp = sno_salida.codemp  ".
						   "   AND sno_personalnomina.codnom = sno_salida.codnom  ".
						   "   AND sno_personalnomina.codper = sno_salida.codper  ".
						   " GROUP BY scg_cuentas.sc_cuenta";
				}	
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconpatcon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
					
					if($ai_estagrapo=='1') // gererar la parte contable de las retenciones
					{
						$ls_sql=$ls_sql." UNION ".
							"SELECT scg_cuentas.sc_cuenta as cuenta, CAST('H' AS char(1)) as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, ".
							"		sno_concepto.codprov, sno_concepto.cedben, sno_concepto.codconc ".
							"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
							" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
							"   AND sno_salida.codnom='".$this->ls_codnom."' ".
							"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
							"   AND (sno_salida.tipsal='P1' OR sno_salida.tipsal='V3' OR sno_salida.tipsal='W3')  ".
							"   AND sno_concepto.sigcon='P' ".
							"   AND sno_salida.valsal <> 0  ".
							"   AND scg_cuentas.status = 'C'  ".
							"   AND sno_personalnomina.codemp = sno_salida.codemp ".
							"   AND sno_personalnomina.codnom = sno_salida.codnom ".
							"   AND sno_personalnomina.codper = sno_salida.codper ".
							"   AND sno_salida.codemp = sno_concepto.codemp ".
							"   AND sno_salida.codnom = sno_concepto.codnom ".
							"   AND sno_salida.codconc = sno_concepto.codconc ".
							"   AND scg_cuentas.codemp = sno_concepto.codemp ".
							"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconpatcon ".
							" GROUP BY sno_concepto.codconc, scg_cuentas.sc_cuenta, sno_concepto.codprov, sno_concepto.cedben "; 
					}
				}
		$ls_sql=$ls_sql." ORDER BY operacion, cuenta"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto($as_codper="")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if($as_codper!="")
		{
			$ls_criterio= " AND sno_salida.codper='".$as_codper."'";
		}
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A , que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable($as_codper="")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 31/05/2006
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$ls_clactacon = $_SESSION["la_empresa"]["clactacon"];
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_criterio="";
		$ls_criterio2="";
		if($as_codper!="")
		{
			$ls_criterio= " AND sno_salida.codper='".$as_codper."'";
			$ls_criterio2= " AND codper='".$as_codper."'";
		}
		$ls_group="  GROUP BY scg_cuentas.sc_cuenta ";
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		$ls_codpronom=$_SESSION["la_nomina"]["codpronom"];
		$ls_codbennom=$_SESSION["la_nomina"]["codbennom"];
		$ls_espnom=$_SESSION["la_nomina"]["espnom"];
		$ls_ctnom=$_SESSION["la_nomina"]["ctnom"];
		switch($li_parametros)
		{
			case 0: // La contabilizaci?n es global
				$ls_cuentapasivo=trim($this->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C"));
				$ls_modo=trim($this->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
				$li_genrecdoc=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));
				break;
				
			case 1: // La contabilizaci?n es por n?mina
				$ls_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$ls_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				$li_genrecdoc=str_pad(trim($_SESSION["la_nomina"]["recdocnom"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		if($as_codper=="")
		{
			if ($_SESSION["ls_gestor"] == 'oci8po')
			{
				$ls_sql= " SELECT cuenta, denominacion, operacion, total ".
						 "   FROM contableconceptos_contable             ".
						 "  WHERE codemp='".$this->ls_codemp."'          ".
						 "	 AND codnom='".$this->ls_codnom."'           ".
						 "	 AND codperi='".$this->ls_peractnom."'       ".
						 " UNION                                         ".
						 " SELECT cuenta, denominacion, operacion, total ".
						 "   FROM concon_contable_intercom    ".
						  "  WHERE codemp='".$this->ls_codemp."'         ".
						 "	 AND codnom='".$this->ls_codnom."'           ".
						 "	 AND codperi='".$this->ls_peractnom."'       ";
			}
			else
			{
				$ls_sql= " SELECT cuenta, denominacion, operacion, total ".
						 "   FROM contableconceptos_contable             ".
						 "  WHERE codemp='".$this->ls_codemp."'          ".
						 "	 AND codnom='".$this->ls_codnom."'           ".
						 "	 AND codperi='".$this->ls_peractnom."'       ".
						 " UNION                                         ".
						 " SELECT cuenta, denominacion, operacion, total ".
						 "   FROM contableconceptos_contable_intercom    ".
						  "  WHERE codemp='".$this->ls_codemp."'         ".
						 "	 AND codnom='".$this->ls_codnom."'           ".
						 "	 AND codperi='".$this->ls_peractnom."'       ";
			}
		}
		else
		{
			if ($_SESSION["ls_gestor"] == 'oci8po')
			{
				$ls_sql= " SELECT cuenta, denominacion, operacion, total       ".
						 "   FROM concon_contable_liquidacion ".
						 "  WHERE codemp='".$this->ls_codemp."'          ".
						 "	 AND codnom='".$this->ls_codnom."'           ".
						 "	 AND codperi='".$this->ls_peractnom."'       ".
						 $ls_criterio2;
			}
			else
			{
				$ls_sql= " SELECT cuenta, denominacion, operacion, total       ".
						 "   FROM contableconceptos_contable_liquidacion ".
						 "  WHERE codemp='".$this->ls_codemp."'          ".
						 "	 AND codnom='".$this->ls_codnom."'           ".
						 "	 AND codperi='".$this->ls_peractnom."'       ".
						 $ls_criterio2;
			}
		}
		if($ls_modo=="OC") // Si el modo de contabilizar la n?mina es Compromete y Causa tomamos la cuenta pasivo de la n?mina.
		{
			if($li_genrecdoc=="0") // No se genera Recepci?n de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "MYSQLI":
						$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_banco.codemp ".
						"   AND sno_salida.codnom = sno_banco.codnom ".
						"   AND sno_salida.codperi = sno_banco.codperi ".
						"   AND sno_personalnomina.codemp = sno_banco.codemp ".
						"   AND sno_personalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else
			{
				if($_SESSION["la_nomina"]["nomliq"]=='0')
				{
					if($ls_ctnom!=1)
					{
						if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
						{
							$ls_sql=$ls_sql." UNION ".
									"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
									"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, cxp_clasificador_rd ".
									" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
									"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
									"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
									"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
									"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
									"   AND sno_salida.valsal <> 0 ".
									"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
									"   AND sno_personalnomina.pagefeper = 0 ".
									"   AND scg_cuentas.status = 'C'".
									"   AND sno_nomina.codemp = sno_salida.codemp ".
									"   AND sno_nomina.codnom = sno_salida.codnom ".
									"   AND sno_nomina.peractnom = sno_salida.codperi ".
									"   AND sno_personalnomina.codemp = sno_salida.codemp ".
									"   AND sno_personalnomina.codnom = sno_salida.codnom ".
									"   AND sno_personalnomina.codper = sno_salida.codper ".
									"   AND sno_nomina.codemp = cxp_clasificador_rd.codemp ".
									"   AND sno_nomina.codclanom = cxp_clasificador_rd.codcla ".
									"   AND cxp_clasificador_rd.codemp = scg_cuentas.codemp ".
									"   AND cxp_clasificador_rd.sc_cuenta = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta ";
						}
						else
						{
							$ls_sql=$ls_sql." UNION ".
									"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
									"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_proveedor ".
									" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
									"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
									"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
									"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
									"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
									"   AND sno_salida.valsal <> 0 ".
									"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
									"   AND sno_personalnomina.pagefeper = 0 ".
									"   AND scg_cuentas.status = 'C'".
									"   AND sno_nomina.descomnom = 'P'".
									"   AND sno_nomina.codemp = sno_salida.codemp ".
									"   AND sno_nomina.codnom = sno_salida.codnom ".
									"   AND sno_nomina.peractnom = sno_salida.codperi ".
									"   AND sno_personalnomina.codemp = sno_salida.codemp ".
									"   AND sno_personalnomina.codnom = sno_salida.codnom ".
									"   AND sno_personalnomina.codper = sno_salida.codper ".
									"   AND sno_nomina.codemp = rpc_proveedor.codemp ".
									"   AND sno_nomina.codpronom = rpc_proveedor.cod_pro ".
									"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
									"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta ";
							$ls_sql=$ls_sql." UNION ".
									"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
									"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_beneficiario ".
									" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
									"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
									"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
									"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
									"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
									"   AND sno_salida.valsal <> 0 ".
									"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
									"   AND sno_personalnomina.pagefeper = 0 ".
									"   AND scg_cuentas.status = 'C'".
									"   AND sno_nomina.descomnom = 'B'".
									"   AND sno_nomina.codemp = sno_salida.codemp ".
									"   AND sno_nomina.codnom = sno_salida.codnom ".
									"   AND sno_nomina.peractnom = sno_salida.codperi ".
									"   AND sno_personalnomina.codemp = sno_salida.codemp ".
									"   AND sno_personalnomina.codnom = sno_salida.codnom ".
									"   AND sno_personalnomina.codper = sno_salida.codper ".
									"   AND sno_nomina.codemp = rpc_beneficiario.codemp ".
									"   AND sno_nomina.codbennom = rpc_beneficiario.ced_bene ".
									"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
									"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
									" GROUP BY scg_cuentas.sc_cuenta ";
						}
					}
					else
					{
						if(($ls_espnom==1)&&($ls_ctnom==1))
						{
							if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
							{
								$ls_sql=$ls_sql." UNION ".
										" SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
										" FROM sno_personalnomina, sno_salida, cxp_clasificador_rd, sno_nomina, scg_cuentas ".
										" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
										" AND sno_salida.codnom = '".$this->ls_codnom."' ".
										" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
										" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
										" AND sno_salida.valsal <> 0 ".
										" AND sno_personalnomina.codemp = sno_salida.codemp ".
										" AND sno_personalnomina.codnom = sno_salida.codnom ".
										" AND sno_personalnomina.codper = sno_salida.codper ".
										" AND sno_nomina.codemp = sno_salida.codemp ".
										" AND sno_nomina.codnom = sno_salida.codnom ".
										" AND sno_nomina.peractnom = sno_salida.codperi ".
										" AND sno_nomina.codemp = cxp_clasificador_rd.codemp ".
										" AND sno_nomina.codclanom = cxp_clasificador_rd.codcla ".
										" AND scg_cuentas.codemp = cxp_clasificador_rd.codemp ".
										" AND scg_cuentas.sc_cuenta = cxp_clasificador_rd.sc_cuenta ".
										" GROUP BY scg_cuentas.sc_cuenta  ";
							}
							else
							{
								$ls_sql=$ls_sql." UNION ".
										" SELECT ".$ls_scctaprov." as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
										" FROM sno_personalnomina, sno_salida, rpc_proveedor, sno_nomina, scg_cuentas ".
										" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
										" AND sno_salida.codnom = '".$this->ls_codnom."' ".
										" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
										" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
										" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
										" AND sno_salida.valsal <> 0 ".
										" AND sno_nomina.descomnom = 'P'".
										" AND sno_personalnomina.codemp = sno_salida.codemp ".
										" AND sno_personalnomina.codnom = sno_salida.codnom ".
										" AND sno_personalnomina.codper = sno_salida.codper ".
										" AND sno_nomina.codemp = sno_salida.codemp ".
										" AND sno_nomina.codnom = sno_salida.codnom ".
										" AND sno_nomina.peractnom = sno_salida.codperi ".
										" AND sno_nomina.codemp = rpc_proveedor.codemp ".
										" AND sno_nomina.codpronom = rpc_proveedor.cod_pro ".
										" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
										" AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
										" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaprov." ";
								$ls_sql=$ls_sql." UNION ".
										" SELECT ".$ls_scctaben." as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
										" FROM sno_personalnomina, sno_salida, rpc_beneficiario, sno_nomina, scg_cuentas ".
										" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
										" AND sno_salida.codnom = '".$this->ls_codnom."' ".
										" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
										" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
										" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
										" AND sno_salida.valsal <> 0 ".
										" AND sno_nomina.descomnom = 'B'".
										" AND sno_personalnomina.codemp = sno_salida.codemp ".
										" AND sno_personalnomina.codnom = sno_salida.codnom ".
										" AND sno_personalnomina.codper = sno_salida.codper ".
										" AND sno_nomina.codemp = sno_salida.codemp ".
										" AND sno_nomina.codnom = sno_salida.codnom ".
										" AND sno_nomina.peractnom = sno_salida.codperi ".
										" AND sno_nomina.codemp = rpc_beneficiario.codemp ".
										" AND sno_nomina.codbennom = rpc_beneficiario.ced_bene ".
										" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
										" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
										" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaben." ";
							}
						}
					}
				}
				else
				{
					$ls_sql=$ls_sql." UNION ".
							"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
							"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_personal, rpc_beneficiario ".
							" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
							"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
							"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
							"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
							"   AND sno_salida.valsal <> 0 ".
							"   AND scg_cuentas.status = 'C'".
							$ls_criterio.
							"   AND sno_personalnomina.codemp = sno_salida.codemp ".
							"   AND sno_personalnomina.codnom = sno_salida.codnom ".
							"   AND sno_personalnomina.codper = sno_salida.codper ".
							"   AND sno_personalnomina.codemp = sno_personal.codemp ".
							"   AND sno_personalnomina.codper = sno_personal.codper ".
							"   AND sno_personal.codemp = rpc_beneficiario.codemp ".
							"   AND sno_personal.cedper = rpc_beneficiario.ced_bene ".
							"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
							"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
							" GROUP BY scg_cuentas.sc_cuenta";
				}
			}
			if(($_SESSION["la_nomina"]["nomliq"]=='0')&&($ls_ctnom==0))
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
						"   AND sno_salida.valsal <> 0".
						"   AND sno_personalnomina.pagbanper = 0 ".
						"   AND sno_personalnomina.pagtaqper = 0 ".
						"   AND sno_personalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
		}
		else
		{
			if($ls_clactacon == '1') // se trabaja con el clasificador de conceptos en cuentas por pagar
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, cxp_clasificador_rd ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = cxp_clasificador_rd.codemp ".
						"   AND sno_nomina.codclacau = cxp_clasificador_rd.codcla ".
						"   AND cxp_clasificador_rd.codemp = scg_cuentas.codemp ".
						"   AND cxp_clasificador_rd.sc_cuenta = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
						"   AND sno_salida.valsal <> 0".
						"   AND sno_personalnomina.pagbanper = 0 ".
						"   AND sno_personalnomina.pagtaqper = 0 ".
						"   AND sno_personalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else
			{
				if(($ls_espnom==1)&&($ls_ctnom==1))
				{
					$ls_sql=$ls_sql." UNION ".
							" SELECT ".$ls_scctaprov." as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
							" FROM sno_personalnomina, sno_salida, rpc_proveedor,  sno_nomina, scg_cuentas ".
							" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
							" AND sno_salida.codnom = '".$this->ls_codnom."' ".
							" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
							" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
							" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
							" AND sno_salida.valsal <> 0 ".
							" AND sno_nomina.descomnom = 'P'".
							" AND sno_personalnomina.codemp = sno_salida.codemp ".
							" AND sno_personalnomina.codnom = sno_salida.codnom ".
							" AND sno_personalnomina.codper = sno_salida.codper ".
							" AND sno_nomina.codemp = sno_salida.codemp ".
							" AND sno_nomina.codnom = sno_salida.codnom ".
							" AND sno_nomina.peractnom = sno_salida.codperi ".
							" AND sno_nomina.codemp = rpc_proveedor.codemp ".
							" AND sno_nomina.codpronom = rpc_proveedor.cod_pro ".
							" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
							" AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
							" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaprov." ";
					$ls_sql=$ls_sql." UNION ".
							" SELECT ".$ls_scctaben." as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
							" FROM sno_personalnomina, sno_salida, rpc_beneficiario,  sno_nomina, scg_cuentas ".
							" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
							" AND sno_salida.codnom = '".$this->ls_codnom."' ".
							" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
							" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
							" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
							" AND sno_salida.valsal <> 0 ".
							" AND sno_nomina.descomnom = 'B'".
							" AND sno_personalnomina.codemp = sno_salida.codemp ".
							" AND sno_personalnomina.codnom = sno_salida.codnom ".
							" AND sno_personalnomina.codper = sno_salida.codper ".
							" AND sno_nomina.codemp = sno_salida.codemp ".
							" AND sno_nomina.codnom = sno_salida.codnom ".
							" AND sno_nomina.peractnom = sno_salida.codperi ".
							" AND sno_nomina.codemp = rpc_beneficiario.codemp ".
							" AND sno_nomina.codbennom = rpc_beneficiario.ced_bene ".
							" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
							" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
							" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaben." ";
				}
				else
				{		
					// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
					$ls_sql=$ls_sql." UNION ".
							"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
							"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas ".
							" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
							"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
							"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
							"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
							"   AND sno_salida.valsal <> 0".
							"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
							"   AND sno_personalnomina.pagefeper = 0 ".
							"   AND scg_cuentas.status = 'C'".
							"   AND sno_personalnomina.codemp = sno_salida.codemp ".
							"   AND sno_personalnomina.codnom = sno_salida.codnom ".
							"   AND sno_personalnomina.codper = sno_salida.codper ".
							"   AND sno_salida.codemp = sno_banco.codemp ".
							"   AND sno_salida.codnom = sno_banco.codnom ".
							"   AND sno_salida.codperi = sno_banco.codperi ".
							"   AND sno_personalnomina.codemp = sno_banco.codemp ".
							"   AND sno_personalnomina.codban = sno_banco.codban ".
							"   AND scg_cuentas.codemp = sno_banco.codemp ".
							"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
							" GROUP BY scg_cuentas.sc_cuenta ";
					$ls_sql=$ls_sql." UNION ".
							"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
							"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
							" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
							"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
							"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
							"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
							"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
							"   AND sno_salida.valsal <> 0".
							"   AND sno_personalnomina.pagbanper = 0 ".
							"   AND sno_personalnomina.pagtaqper = 0 ".
							"   AND sno_personalnomina.pagefeper = 1 ".
							"   AND scg_cuentas.status = 'C'".
							"   AND sno_personalnomina.codemp = sno_salida.codemp ".
							"   AND sno_personalnomina.codnom = sno_salida.codnom ".
							"   AND sno_personalnomina.codper = sno_salida.codper ".
							"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
							"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
							" GROUP BY scg_cuentas.sc_cuenta ";
				}
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableconceptos_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadreconceptoaporte_aportes()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreconceptoaporte_aportes
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 15/09/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprepatcon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_concepto.codconc, sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprepatcon  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprepatcon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" GROUP BY sno_concepto.codconc, sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprepatcon ";
				" ORDER BY codconc, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprepatcon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_aportes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cuentapresupuesto=$rs_data->fields["cueprepatcon"];
				$li_total=$rs_data->fields["total"];
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
					$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_aportes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS->insertRow("codestpro1",$ls_codestpro1);
						$this->DS->insertRow("codestpro2",$ls_codestpro2);
						$this->DS->insertRow("codestpro3",$ls_codestpro3);
						$this->DS->insertRow("codestpro4",$ls_codestpro4);
						$this->DS->insertRow("codestpro5",$ls_codestpro5);
						$this->DS->insertRow("cueprepatcon",$ls_cuentapresupuesto);
						$this->DS->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreconceptoaporte_aportes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadreconceptoaporte_conceptos()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreconceptoaporte_conceptos
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 15/09/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_concepto.sigcon = 'A' ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_concepto.sigcon = 'A' ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprecon ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codestpro1=trim($rs_data->fields["codestpro1"]);
				$ls_codestpro2=trim($rs_data->fields["codestpro2"]);
				$ls_codestpro3=trim($rs_data->fields["codestpro3"]);
				$ls_codestpro4=trim($rs_data->fields["codestpro4"]);
				$ls_codestpro5=trim($rs_data->fields["codestpro5"]);
				$ls_estcla=trim($rs_data->fields["estcla"]);
				$ls_cuentapresupuesto=trim($rs_data->fields["cueprecon"]);
				$li_total=$rs_data->fields["total"];
				$ls_sql="SELECT denominacion ".
						"  FROM spg_cuentas ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND status = 'C'".
						"   AND codestpro1 = '".$ls_codestpro1."'".
						"   AND codestpro2 = '".$ls_codestpro2."'".
						"   AND codestpro3 = '".$ls_codestpro3."'".
						"   AND codestpro4 = '".$ls_codestpro4."'".
						"   AND codestpro5 = '".$ls_codestpro5."'".
						"   AND estcla = '".$ls_estcla."'".
						"   AND spg_cuenta = '".$ls_cuentapresupuesto."'";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_conceptos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS_detalle->insertRow("codestpro1",$ls_codestpro1);
						$this->DS_detalle->insertRow("codestpro2",$ls_codestpro2);
						$this->DS_detalle->insertRow("codestpro3",$ls_codestpro3);
						$this->DS_detalle->insertRow("codestpro4",$ls_codestpro4);
						$this->DS_detalle->insertRow("codestpro5",$ls_codestpro5);
						$this->DS_detalle->insertRow("cueprecon",$ls_cuentapresupuesto);
						$this->DS_detalle->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS_detalle->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreconceptoaporte_conceptos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_enmohca()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 22/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A , que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon =  spg_cuentas.spg_cuenta".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1  = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprecon, spg_cuentas.denominacion ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprecon'),
									array('0'=>'total'),array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprecon'));
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_enmohca
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 17/07/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ";
				" ORDER BY codestpro1, codestpro2, codestpro3, , codestpro5, estcla, cueprepatcon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_presupuesto_proyecto_dt();
			$this->DS->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprepatcon'),
								array('0'=>'total'),'total');
		}
		return $lb_valido;
	}// end function uf_contableaportes_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_presupuesto_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableaportes_presupuesto_proyecto_dt 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos de aportes por proyecto
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT MAX(sno_proyecto.codestpro1) AS codestpro1, MAX(sno_proyecto.codestpro2) AS codestpro2, MAX(sno_proyecto.codestpro3) AS codestpro3, ".
				"		MAX(sno_proyecto.codestpro4) AS codestpro4, MAX(sno_proyecto.codestpro5) AS codestpro5, sno_proyecto.estcla, spg_cuentas.spg_cuenta, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) AS total, MAX(sno_concepto.codprov) AS codprov, ".$ls_cadena." AS montoparcial, ".
				"		MAX(sno_concepto.cedben) AS cedben, sno_concepto.codconc, sno_proyecto.codproy, sno_proyectopersonal.codper, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, MAX(sno_proyectopersonal.pordiames) as pordiames  ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_proyectopersonal.codemp = sno_salida.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_salida.codnom ".
				"   AND sno_proyectopersonal.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_proyecto.codemp = spg_cuentas.codemp ".
				"   AND sno_proyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_proyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_proyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_proyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_proyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_proyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_proyectopersonal.codper, sno_proyecto.codproy, spg_cuentas.spg_cuenta, sno_concepto.codconc,".
				"          sno_proyecto.estcla ".
				" ORDER BY sno_proyectopersonal.codper, sno_proyecto.codproy, spg_cuentas.spg_cuenta, sno_concepto.codconc ";
		$rs_data=$this->io_sql->select($ls_sql); 
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_codestpro1ant="";
			$ls_codestpro2ant="";
			$ls_codestpro3ant="";
			$ls_codestpro4ant="";
			$ls_codestpro5ant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_conceptoant="";
			$ls_conta=0;
			$ls_conta2=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_cedben=$rs_data->fields["cedben"];
				$ls_codproy=$rs_data->fields["codproy"];
				$li_montoparcial=round($rs_data->fields["montoparcial"],3);
				$li_total=round($rs_data->fields["total"],3);
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||(($ls_spgcuenta!=$ls_cuentaant)&&($ls_codconc!=$ls_conceptoant)))
				{
					if($li_acumulado!=0)
					{
						
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiames<1))
						{							
							$ls_conta++;
							$li_montoparcial=round($rs_data->fields["montoparcial"],3);
							$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
							$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
							$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
							$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
							$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
							$this->DS->insertRow("estcla",$ls_estclaproy);
							$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacion);
							$this->DS->insertRow("cedben",$li_cedben);
							$this->DS->insertRow("codper",$ls_codper);
							$this->DS->insertRow("codconc",$ls_codconc);
							$this->DS->insertRow("codproy",$ls_codproy);
						}
					}
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$ls_conceptoant=$ls_codconc;
					$li_totalant=$li_total;
				}
				if(($li_acumulado==0)||($li_pordiames==1))
				{
					$ls_conta2++;
					$this->DS->insertRow("codestpro1",$ls_codestpro1);
					$this->DS->insertRow("codestpro2",$ls_codestpro2);
					$this->DS->insertRow("codestpro3",$ls_codestpro3);
					$this->DS->insertRow("codestpro4",$ls_codestpro4);
					$this->DS->insertRow("codestpro5",$ls_codestpro5);
					$this->DS->insertRow("estcla",$ls_estclaproy);
					$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
					$this->DS->insertRow("total",$li_montoparcial);
					$this->DS->insertRow("denominacion",$ls_denominacion);
					$this->DS->insertRow("cedben",$li_cedben);
					$this->DS->insertRow("codper",$ls_codper);
					$this->DS->insertRow("codconc",$ls_codconc);
					$this->DS->insertRow("codproy",$ls_codproy);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableaportes_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_contable_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 17/07/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		switch($li_parametros)
		{
			case 0: // La contabilizaci?n es global
				$ls_modoaporte=$this->uf_select_config("SNO","NOMINA","CONTABILIZACION APORTES","OCP","C");
				$li_genrecapo=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO APORTE","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN_APORTE","0","I"));
				break;
			
			case 1: // La contabilizaci?n es por n?mina
				$ls_modoaporte=trim($_SESSION["la_nomina"]["conaponom"]);
				$li_genrecapo=str_pad(trim($_SESSION["la_nomina"]["recdocapo"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaaltapo"]);
				break;
		}
		
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
			$ls_sql=" SELECT  cuenta,  denoconta, operacion, total    ".
					 "    FROM conapo_contable_proy       ".
					 "   WHERE codemp='".$this->ls_codemp."'           ".
					 "     AND codnom='".$this->ls_codnom."'           ".
					 "     AND codperi='".$this->ls_peractnom."'       ".
					 "  UNION                                          ".
					 " SELECT cuenta,  denoconta, operacion, total     ".
					 "	 FROM conapo_contable_proy_int ".
					  "   WHERE codemp='".$this->ls_codemp."'           ".
					 "     AND codnom='".$this->ls_codnom."'            ".
					 "     AND codperi='".$this->ls_peractnom."'        ";
		}
		else
		{
			 $ls_sql=" SELECT  cuenta,  denoconta, operacion, total    ".
					 "    FROM contableaportes_contable_proyecto       ".
					 "   WHERE codemp='".$this->ls_codemp."'           ".
					 "     AND codnom='".$this->ls_codnom."'           ".
					 "     AND codperi='".$this->ls_peractnom."'       ".
					 "  UNION                                          ".
					 " SELECT cuenta,  denoconta, operacion, total     ".
					 "	 FROM contableaportes_contable_proyecto_intcom ".
					  "   WHERE codemp='".$this->ls_codemp."'           ".
					 "     AND codnom='".$this->ls_codnom."'            ".
					 "     AND codperi='".$this->ls_peractnom."'        ";
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		if(($ls_modoaporte=="OC")&&($li_genrecapo=="1"))
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_proveedor ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_concepto.codprov <> '----------' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"	AND sno_concepto.codemp = rpc_proveedor.codemp ".
					"	AND sno_concepto.codprov = rpc_proveedor.cod_pro ".
					"   AND scg_cuentas.codemp = rpc_proveedor.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaprov." ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, rpc_beneficiario ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C' ".
					"   AND sno_concepto.cedben <> '----------' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"	AND sno_concepto.codemp = rpc_beneficiario.codemp ".
					"	AND sno_concepto.cedben = rpc_beneficiario.ced_bene ".
					"   AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
					"   AND scg_cuentas.sc_cuenta = ".$ls_scctaben." ".
					" GROUP BY scg_cuentas.sc_cuenta "; //print $ls_sql;
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'H' as operacion, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconpatcon ".
					" GROUP BY scg_cuentas.sc_cuenta ".
					" ORDER BY operacion, cuenta ";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_contable_proyecto_dt();
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');	
			
			$ls_contar=$this->DS_detalle->getRowCount("operacion");
			if ($ls_contar>0)
			{
				$this->DS_detalle->sortData('operacion');
			}
			
		}	
		return $lb_valido;
	}// end function uf_contableaportes_contable_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_contable_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableaportes_contable_proyecto_dt 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los aportes
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);		
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
						$ls_sql=" SELECT sc_cuenta, operacion, total, montoparcial, codprov, cedben,  ". 
								"		   codconc, codper, codproy,  denoconta,pordiames             ".
								"	FROM conapo_contable_proy_dt                         ".
								"  WHERE codemp='".$this->ls_codemp."'                                ".
								"	 AND codnom='".$this->ls_codnom."'                                ".
								"	 AND codperi='".$this->ls_peractnom."'                            ".
								"	UNION                                                             ".
								"  SELECT sc_cuenta, operacion, total, montoparcial, codprov, cedben, ".
								"		   codconc, codper, codproy,  denoconta,pordiames             ".
								"	 FROM conapo_contable_proy_dt_int                 ".
								"   WHERE codemp='".$this->ls_codemp."'                               ".
								"	 AND codnom='".$this->ls_codnom."'                                ".
								"	 AND codperi='".$this->ls_peractnom."'                            ".                       
								"  ORDER BY codper, codconc                            ";                       
		}
		else
		{		
						$ls_sql=" SELECT sc_cuenta, operacion, total, montoparcial, codprov, cedben,  ". 
								"		   codconc, codper, codproy,  denoconta,pordiames             ".
								"	FROM contableaportes_contable_proyecto_dt                         ".
								"  WHERE codemp='".$this->ls_codemp."'                                ".
								"	 AND codnom='".$this->ls_codnom."'                                ".
								"	 AND codperi='".$this->ls_peractnom."'                            ".
								"	UNION                                                             ".
								"  SELECT sc_cuenta, operacion, total, montoparcial, codprov, cedben, ".
								"		   codconc, codper, codproy,  denoconta,pordiames             ".
								"	 FROM contableaportes_contable_proyecto_dt_intcom                 ".
								"   WHERE codemp='".$this->ls_codemp."'                               ".
								"	 AND codnom='".$this->ls_codnom."'                                ".
								"	 AND codperi='".$this->ls_peractnom."'                            ".                       
								"  ORDER BY codper, codconc                            ";                       
		}				
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_contable_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_operacionant="";
			$ls_denominacionant="";
			$ls_codconcant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_cuenta=$rs_data->fields["sc_cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$ls_denominacion=$rs_data->fields["denoconta"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_codconc!=$ls_codconcant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS_detalle->insertRow("operacion",$ls_operacionant);
							$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
							$this->DS_detalle->insertRow("total",$li_montoparcial);
							$this->DS_detalle->insertRow("denoconta",$ls_denominacionant);
						}
					}
					$li_acumulado=round($rs_data->fields["montoparcial"],3);
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_totalant=$li_total;
					$li_pordiamesant=$li_pordiames;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS_detalle->insertRow("operacion",$ls_operacion);
				$this->DS_detalle->insertRow("cuenta",$ls_cuenta);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denoconta",$ls_denominacion);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS_detalle->insertRow("operacion",$ls_operacionant);
				$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denoconta",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}	
		return  $lb_valido;    
	}// end function uf_contableaportes_contable_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 17/07/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A , que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla,spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_presupuesto_proyecto_dt();
			$this->DS->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprecon'),
			                    array('0'=>'total'),'total');		
		}
		return $lb_valido;
	}// end function uf_contableconceptos_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_presupuesto_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_presupuesto_proyecto_dt 
		//	    Arguments:
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos que son por proyectos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D
		$ls_sql="SELECT sno_proyectopersonal.codper, sno_proyectopersonal.codproy, MAX(sno_proyecto.codestpro1) AS codestpro1, MAX(sno_proyecto.codestpro2) AS codestpro2, MAX(sno_proyecto.codestpro3) AS codestpro3, ".
				"		MAX(sno_proyecto.codestpro4) AS codestpro4, MAX(sno_proyecto.codestpro5) AS codestpro5,  MAX(sno_proyecto.estcla) AS estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) AS total, MAX(spg_cuentas.denominacion) AS denominacion, MAX(sno_proyectopersonal.pordiames) AS pordiames ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_proyectopersonal.pordiames <> 0 ".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND sno_proyectopersonal.codemp = sno_salida.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_salida.codnom ".
				"   AND sno_proyectopersonal.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_proyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_proyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_proyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_proyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_proyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_proyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_proyectopersonal.codper, sno_proyectopersonal.codproy,  spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_proyectopersonal.codper, sno_proyectopersonal.codproy, MAX(sno_proyecto.codestpro1) AS codestpro1, MAX(sno_proyecto.codestpro2) AS codestpro2, MAX(sno_proyecto.codestpro3) AS codestpro3, ".
				"		MAX(sno_proyecto.codestpro4) AS codestpro4, MAX(sno_proyecto.codestpro5) AS codestpro5,  MAX(sno_proyecto.estcla) AS estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) AS total, MAX(spg_cuentas.denominacion) AS denominacion, MAX(sno_proyectopersonal.pordiames) AS pordiames ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_proyectopersonal.pordiames <> 0 ".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND sno_proyectopersonal.codemp = sno_salida.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_salida.codnom ".
				"   AND sno_proyectopersonal.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_proyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_proyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_proyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_proyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_proyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_proyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_proyectopersonal.codper, sno_proyectopersonal.codproy, spg_cuentas.spg_cuenta ".
				" ORDER BY codper, spg_cuenta, codproy ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_codestpro1ant="";
			$ls_codestpro2ant="";
			$ls_codestpro3ant="";
			$ls_codestpro4ant="";
			$ls_codestpro5ant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_codproyant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$li_montoparcial=round($rs_data->fields["montoparcial"],3);
				$li_total=round($rs_data->fields["total"],3);
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				$ls_codproy=$rs_data->fields["codproy"];
				if(($ls_codper!=$ls_codant)||($ls_spgcuenta!=$ls_cuentaant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
							$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
							$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
							$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
							$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprecon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
						}
					}
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$li_acumulado=$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_pordiamesant=$li_pordiames;
					$ls_codant=$ls_codper;
					$ls_codproyant=$ls_codproy;
					$ls_denominacionant=$ls_denominacion;
					$li_totalant=$li_total;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS->insertRow("codestpro1",$ls_codestpro1);
				$this->DS->insertRow("codestpro2",$ls_codestpro2);
				$this->DS->insertRow("codestpro3",$ls_codestpro3);
				$this->DS->insertRow("codestpro4",$ls_codestpro4);
				$this->DS->insertRow("codestpro5",$ls_codestpro5);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprecon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
				$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
				$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
				$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
				$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprecon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;    
	}// end function uf_contableconceptos_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable_proyecto 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_group="  GROUP BY scg_cuentas.sc_cuenta ";
		$li_parametros=trim($this->uf_select_config("SNO","CONFIG","CONTA GLOBAL","0","I"));
		$ls_codpronom=$_SESSION["la_nomina"]["codpronom"];
		$ls_codbennom=$_SESSION["la_nomina"]["codbennom"];
		$ls_espnom=$_SESSION["la_nomina"]["espnom"];
		$ls_ctnom=$_SESSION["la_nomina"]["ctnom"];
		switch($li_parametros)
		{
			case 0: // La contabilizaci?n es global
				$ls_cuentapasivo=trim($this->uf_select_config("SNO","CONFIG","CTA.CONTA","-------------------------","C"));
				$ls_modo=trim($this->uf_select_config("SNO","NOMINA","CONTABILIZACION","OCP","C"));
				$li_genrecdoc=str_pad($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO","0","I"),1,"0");
				$ls_estctaalt=trim($this->uf_select_config("SNO","CONFIG","UTILIZAR_CTA.CONTA_REC_DOC_PROV_BEN","0","I"));
				break;
				
			case 1: // La contabilizaci?n es por n?mina
				$ls_cuentapasivo=trim($_SESSION["la_nomina"]["cueconnom"]);
				$ls_modo=trim($_SESSION["la_nomina"]["consulnom"]);
				$li_genrecdoc=str_pad(trim($_SESSION["la_nomina"]["recdocnom"]),1,"0");
				$ls_estctaalt=trim($_SESSION["la_nomina"]["estctaalt"]);
				break;
		}
		if ($ls_estctaalt=='1')
		{
			$ls_scctaprov='rpc_proveedor.sc_cuentarecdoc';
			$ls_scctaben='rpc_beneficiario.sc_cuentarecdoc';
		}
		else
		{
			$ls_scctaprov='rpc_proveedor.sc_cuenta';
			$ls_scctaben='rpc_beneficiario.sc_cuenta';
		}
		// Buscamos todas aquellas cuentas contables que estan ligadas a las presupuestarias de los conceptos A y D, que se 
		// integran directamente con presupuesto, estas van por el debe de contabilidad
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
			$ls_sql="  SELECT cuenta, denominacion, operacion, total                ".
				        "    FROM concon_contable_proy                   ".
						"	WHERE  codemp='".$this->ls_codemp."'                        ". 
						"	   AND codnom='".$this->ls_codnom."'                        ".
						"	   AND codperi='".$this->ls_peractnom."'                    ".
						"	 UNION                                                      ".
						"	SELECT cuenta, denominacion, operacion, total               ".
						"     FROM concon_contable_proy_int         ".
						"	WHERE  codemp='".$this->ls_codemp."'                        ". 
						"	   AND codnom='".$this->ls_codnom."'                        ".
						"	   AND codperi='".$this->ls_peractnom."'                    ";
		}
		else
		{
				$ls_sql="  SELECT cuenta, denominacion, operacion, total                ".
				        "    FROM contableconceptos_contable_proyecto                   ".
						"	WHERE  codemp='".$this->ls_codemp."'                        ". 
						"	   AND codnom='".$this->ls_codnom."'                        ".
						"	   AND codperi='".$this->ls_peractnom."'                    ".
						"	 UNION                                                      ".
						"	SELECT cuenta, denominacion, operacion, total               ".
						"     FROM contableconceptos_contable_proyecto_intercom         ".
						"	WHERE  codemp='".$this->ls_codemp."'                        ". 
						"	   AND codnom='".$this->ls_codnom."'                        ".
						"	   AND codperi='".$this->ls_peractnom."'                    ";
		}
		if($ls_modo=="OC") // Si el modo de contabilizar la n?mina es Compromete y Causa tomamos la cuenta pasivo de la n?mina.
		{
			if($li_genrecdoc=="0") // No se genera Recepci?n de Documentos
			{
				// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
				switch($_SESSION["ls_gestor"])
				{
					case "MYSQLT":
						$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "MYSQLI":
						$ls_cadena="CONVERT('".$ls_cuentapasivo."' USING utf8) as cuenta";
						break;
					case "POSTGRES":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
					case "INFORMIX":
						$ls_cadena="CAST('".$ls_cuentapasivo."' AS char(25)) as cuenta";
						break;					
				}
				$ls_sql=$ls_sql." UNION ".
						"SELECT ".$ls_cadena.", MAX(scg_cuentas.denominacion) AS denominacion, ".
						"		CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1) ".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_salida.codemp = sno_banco.codemp ".
						"   AND sno_salida.codnom = sno_banco.codnom ".
						"   AND sno_salida.codperi = sno_banco.codperi ".
						"   AND sno_personalnomina.codemp = sno_banco.codemp ".
						"   AND sno_personalnomina.codban = sno_banco.codban ".
						"   AND scg_cuentas.codemp = sno_banco.codemp ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			else // Se genera Recepci?n de documentos
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_proveedor ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.descomnom = 'P'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = rpc_proveedor.codemp ".
						"   AND sno_nomina.codpronom = rpc_proveedor.cod_pro ".
						"   AND rpc_proveedor.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaprov." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas, sno_nomina, rpc_beneficiario ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
						"   AND sno_salida.valsal <> 0 ".
						"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1)".
						"   AND sno_personalnomina.pagefeper = 0 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_nomina.descomnom = 'B'".
						"   AND sno_nomina.codemp = sno_salida.codemp ".
						"   AND sno_nomina.codnom = sno_salida.codnom ".
						"   AND sno_nomina.peractnom = sno_salida.codperi ".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND sno_nomina.codemp = rpc_beneficiario.codemp ".
						"   AND sno_nomina.codbennom = rpc_beneficiario.ced_bene ".
						"   AND rpc_beneficiario.codemp = scg_cuentas.codemp ".
						"   AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
			if(($ls_espnom==1)&&($ls_ctnom==1))
			{
					if($ls_codpronom!='----------')
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT rpc_proveedor.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
								" FROM sno_personalnomina, sno_salida, rpc_proveedor, scg_cuentas ".
								" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
								" AND sno_salida.codnom = '".$this->ls_codnom."' ".
								" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
								" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
								" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
								" AND sno_salida.valsal <> 0 ".
								" AND sno_personalnomina.pagbanper = 0 ".
								" AND sno_personalnomina.pagtaqper = 0 ".
								" AND sno_personalnomina.pagefeper = 1 ".
								" AND sno_personalnomina.codemp = sno_salida.codemp ".
								" AND sno_personalnomina.codnom = sno_salida.codnom ".
								" AND sno_personalnomina.codper = sno_salida.codper ".
								" AND rpc_proveedor.codemp = sno_personalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
								" AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta, rpc_proveedor.sc_cuenta ";
					}
					else
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT ".$ls_scctaben." as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
								" FROM sno_personalnomina, sno_salida, rpc_beneficiario, scg_cuentas ".
								" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
								" AND sno_salida.codnom = '".$this->ls_codnom."' ".
								" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
								" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
								" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
								" AND sno_salida.valsal <> 0 ".
								" AND sno_personalnomina.pagbanper = 0 ".
								" AND sno_personalnomina.pagtaqper = 0 ".
								" AND sno_personalnomina.pagefeper = 1 ".
								" AND sno_personalnomina.codemp = sno_salida.codemp ".
								" AND sno_personalnomina.codnom = sno_salida.codnom ".
								" AND sno_personalnomina.codper = sno_salida.codper ".
								" AND rpc_beneficiario.codemp = sno_personalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
								" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta, ".$ls_scctaben." ";
					}
			}
			else
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
						"		CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
						"   AND sno_salida.valsal <> 0".
						"   AND sno_personalnomina.pagbanper = 0 ".
						"   AND sno_personalnomina.pagtaqper = 0 ".
						"   AND sno_personalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.sc_cuenta = '".$ls_cuentapasivo."' ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
		}
		else
		{
			// Buscamos todas aquellas cuentas contables de los conceptos A y D, estas van por el haber de contabilidad
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
					"		CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_banco, scg_cuentas ".
					" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
					"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
					"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
					"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
					"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
					"   AND sno_salida.valsal <> 0".
					"   AND (sno_personalnomina.pagbanper = 1 OR sno_personalnomina.pagtaqper = 1) ".
					"   AND sno_personalnomina.pagefeper = 0 ".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_banco.codemp ".
					"   AND sno_salida.codnom = sno_banco.codnom ".
					"   AND sno_salida.codperi = sno_banco.codperi ".
					"   AND sno_personalnomina.codemp = sno_banco.codemp ".
					"   AND sno_personalnomina.codban = sno_banco.codban ".
					"   AND scg_cuentas.codemp = sno_banco.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_banco.codcuecon ".
					" GROUP BY scg_cuentas.sc_cuenta ";
			
			if(($ls_espnom==1)&&($ls_ctnom==1))
			{
					if($ls_codpronom!='----------')
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT rpc_proveedor.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
								" FROM sno_personalnomina, sno_salida, rpc_proveedor, scg_cuentas ".
								" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
								" AND sno_salida.codnom = '".$this->ls_codnom."' ".
								" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
								" AND rpc_proveedor.cod_pro = '".$ls_codpronom."' ".
								" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
								" AND sno_salida.valsal <> 0 ".
								" AND sno_personalnomina.pagbanper = 0 ".
								" AND sno_personalnomina.pagtaqper = 0 ".
								" AND sno_personalnomina.pagefeper = 1 ".
								" AND sno_personalnomina.codemp = sno_salida.codemp ".
								" AND sno_personalnomina.codnom = sno_salida.codnom ".
								" AND sno_personalnomina.codper = sno_salida.codper ".
								" AND rpc_proveedor.codemp = sno_personalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_proveedor.codemp ".
								" AND rpc_proveedor.sc_cuenta = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta,rpc_proveedor.sc_cuenta ";
					}
					else
					{
						$ls_sql=$ls_sql." UNION ".
								" SELECT ".$ls_scctaben." as cuenta, MAX(scg_cuentas.denominacion) as denominacion, CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
								" FROM sno_personalnomina, sno_salida, rpc_beneficiario, scg_cuentas ".
								" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
								" AND sno_salida.codnom = '".$this->ls_codnom."' ".
								" AND sno_salida.codperi = '".$this->ls_peractnom."' ".
								" AND rpc_beneficiario.ced_bene = '".$ls_codbennom."' ".
								" AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3') ".
								" AND sno_salida.valsal <> 0 ".
								" AND sno_personalnomina.pagbanper = 0 ".
								" AND sno_personalnomina.pagtaqper = 0 ".
								" AND sno_personalnomina.pagefeper = 1 ".
								" AND sno_personalnomina.codemp = sno_salida.codemp ".
								" AND sno_personalnomina.codnom = sno_salida.codnom ".
								" AND sno_personalnomina.codper = sno_salida.codper ".
								" AND rpc_beneficiario.codemp = sno_personalnomina.codemp ".
								" AND scg_cuentas.codemp = rpc_beneficiario.codemp ".
								" AND ".$ls_scctaben." = scg_cuentas.sc_cuenta ".
								" GROUP BY scg_cuentas.sc_cuenta,".$ls_scctaben." ";
					}
			}
			else
			{
				$ls_sql=$ls_sql." UNION ".
						"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
						"		CAST('H' AS char(1)) as operacion, -SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
						"  FROM sno_personalnomina, sno_salida, scg_cuentas ".
						" WHERE sno_salida.codemp = '".$this->ls_codemp."' ".
						"   AND sno_salida.codnom = '".$this->ls_codnom."' ".
						"   AND sno_salida.codperi = '".$this->ls_peractnom."' ".
						"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1' OR sno_salida.tipsal = 'D' ".
						"    OR  sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
						"   AND sno_salida.valsal <> 0".
						"   AND sno_personalnomina.pagbanper = 0 ".
						"   AND sno_personalnomina.pagtaqper = 0 ".
						"   AND sno_personalnomina.pagefeper = 1 ".
						"   AND scg_cuentas.status = 'C'".
						"   AND sno_personalnomina.codemp = sno_salida.codemp ".
						"   AND sno_personalnomina.codnom = sno_salida.codnom ".
						"   AND sno_personalnomina.codper = sno_salida.codper ".
						"   AND scg_cuentas.codemp = sno_personalnomina.codemp ".
						"   AND scg_cuentas.sc_cuenta = sno_personalnomina.cueaboper ".
						" GROUP BY scg_cuentas.sc_cuenta ";
			}
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_contable_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_contable_proyecto_dt();
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');		
		}
		return  $lb_valido;    
	}// end function uf_contableconceptos_contable_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_contable_proyecto_dt()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_contable_proyecto_dt 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos por proyecto
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		if ($_SESSION["ls_gestor"] == 'oci8po')
		{
			$ls_sql=" SELECT cuenta, operacion, total, montoparcial,            ".
							"	     codper, codproy, denominacion, pordiames, codconc  ".
							"    FROM concon_contable_proy_dt            ".
							"   WHERE codemp='".$this->ls_codemp."'                     ".
							"	  AND codnom='".$this->ls_codnom."'                     ".
							"	  AND codperi='".$this->ls_peractnom."'                 ".
							"	UNION                                                   ".
							" SELECT cuenta, operacion, total, montoparcial,            ".
							"	     codper, codproy, denominacion, pordiames, codconc  ".
							"	FROM concon_contable_proy_dt_int         ".
							"   WHERE codemp='".$this->ls_codemp."'                     ".
							"	  AND codnom='".$this->ls_codnom."'                     ".
							"	  AND codperi='".$this->ls_peractnom."'                 ".						
							"   ORDER BY codper, codconc ";						
		}
		else
		{
					$ls_sql=" SELECT cuenta, operacion, total, montoparcial,            ".
							"	     codper, codproy, denominacion, pordiames, codconc  ".
							"    FROM contableconceptos_contable_proyecto_dt            ".
							"   WHERE codemp='".$this->ls_codemp."'                     ".
							"	  AND codnom='".$this->ls_codnom."'                     ".
							"	  AND codperi='".$this->ls_peractnom."'                 ".
							"	UNION                                                   ".
							" SELECT cuenta, operacion, total, montoparcial,            ".
							"	     codper, codproy, denominacion, pordiames, codconc  ".
							"	FROM contableconceptos_contable_proyecto_dt_int         ".
							"   WHERE codemp='".$this->ls_codemp."'                     ".
							"	  AND codnom='".$this->ls_codnom."'                     ".
							"	  AND codperi='".$this->ls_peractnom."'                 ".
							"   ORDER BY codper, codconc ";						
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_contable_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_cuentaant="";
			$ls_codconcant="";
			$ls_operacionant="";
			$ls_denominacionant="";
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_cuenta=$rs_data->fields["cuenta"];
				$ls_operacion=$rs_data->fields["operacion"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||($ls_codconc!=$ls_codconcant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS_detalle->insertRow("operacion",$ls_operacionant);
							$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
							$this->DS_detalle->insertRow("total",$li_montoparcial);
							$this->DS_detalle->insertRow("denominacion",$ls_denominacionant);
						}
					}
					$li_acumulado=$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$li_totalant=$li_total;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_operacionant=$ls_operacion;
					$ls_cuentaant=$ls_cuenta;
					$ls_codconcant=$ls_codconc;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
				}
				$this->DS_detalle->insertRow("operacion",$ls_operacion);
				$this->DS_detalle->insertRow("cuenta",$ls_cuenta);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denominacion",$ls_denominacion);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS_detalle->insertRow("operacion",$ls_operacionant);
				$this->DS_detalle->insertRow("cuenta",$ls_cuentaant);
				$this->DS_detalle->insertRow("total",$li_montoparcial);
				$this->DS_detalle->insertRow("denominacion",$ls_denominacionant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;    
	}// end function uf_contableconceptos_contable_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableingresos_ingreso()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableingresos_ingreso
		//         Access: public (desde la clase sigesp_sno_r_contableingresos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas de ingresos
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 25/03/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		$this->io_sql=new class_sql($this->io_conexion);
		if ($ls_estpreing==0) ///no se maneja estructuras con los ingresos
		{
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
			$ls_sql="SELECT spi_cuentas.spi_cuenta AS cuenta, MAX(spi_cuentas.denominacion) AS denominacion, ".
					"		sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
					"   AND sno_concepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C' ".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND spi_cuentas.codemp = sno_concepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ".
					" GROUP BY spi_cuentas.spi_cuenta ";
		}
		else
		{
			$ls_sql=" SELECT spi_cuentas.spi_cuenta AS cuenta,                             ".
					"	     MAX(spi_cuentas.denominacion) AS denominacion,                ".
					"	     sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total, ".        
					"	     spi_cuentas_estructuras.codestpro1,                           ".
					"	 	 spi_cuentas_estructuras.codestpro2,                           ".
					"	 	 spi_cuentas_estructuras.codestpro3,                           ".
					"	 	 spi_cuentas_estructuras.codestpro4,                           ". 
					"	 	 spi_cuentas_estructuras.codestpro5                            ". 
				  	"  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas,     ".
					"       spi_cuentas_estructuras, sno_unidadadmin                       ".
				 	"  WHERE sno_salida.codemp='".$this->ls_codemp."'                      ".
					"    AND sno_salida.codnom='".$this->ls_codnom."'                      ".
					"    AND sno_salida.codperi='".$this->ls_peractnom."'                  ".
					"    AND sno_salida.valsal <> 0                                        ".
					"    AND (sno_salida.tipsal = 'D'                                      ".
					"     OR sno_salida.tipsal = 'V2'                                      ". 
					"     OR sno_salida.tipsal = 'W2'                                      ".
					"     OR sno_salida.tipsal = 'P1'                                      ". 
					"     OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )        ".
					"    AND sno_concepto.intingcon = '1'                                  ".
					"    AND spi_cuentas.status = 'C'                                      ".  
					"    AND sno_personalnomina.codemp = sno_salida.codemp                 ".
					"    AND sno_personalnomina.codnom = sno_salida.codnom                 ".
					"    AND sno_personalnomina.codper = sno_salida.codper                 ".
					"    AND sno_salida.codemp = sno_concepto.codemp                       ".
					"    AND sno_salida.codnom = sno_concepto.codnom                       ". 
					"    AND sno_salida.codconc = sno_concepto.codconc                     ".
					"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp            ". 
					"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm      ".
					"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm      ".
					"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm      ".
					"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm      ".
					"    AND spi_cuentas.codemp = sno_concepto.codemp                      ".
					"    AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta              ".  
					"    AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp             ". 
					"    AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta    ".
					"    AND sno_concepto.codestpro1  = spi_cuentas_estructuras.codestpro1 ".
					"    AND sno_concepto.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
					"    AND sno_concepto.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
					"    AND sno_concepto.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
					"    AND sno_concepto.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"    AND sno_concepto.estcla = spi_cuentas_estructuras.estcla             ".
					"  GROUP BY spi_cuentas.spi_cuenta,spi_cuentas_estructuras.codestpro1,    ".
					"		    spi_cuentas_estructuras.codestpro2, spi_cuentas_estructuras.codestpro3, ".
					" 		    spi_cuentas_estructuras.codestpro4, spi_cuentas_estructuras.codestpro5  ".
				    "    UNION                                                                          ".
				    "  SELECT spi_cuentas.spi_cuenta AS cuenta,                                         ".
					"	      MAX(spi_cuentas.denominacion) AS denominacion,                            ".
					" 	      sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total,             ".
					" 	      spi_cuentas_estructuras.codestpro1,                                       ".
					"	      spi_cuentas_estructuras.codestpro2,                                       ".
					"  	      spi_cuentas_estructuras.codestpro3,                                       ".
					"	      spi_cuentas_estructuras.codestpro4,                                       ".
					"	      spi_cuentas_estructuras.codestpro5                                        ".
					"   FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas,                 ".
					"        spi_cuentas_estructuras, sno_unidadadmin                                   ".
					"  WHERE sno_salida.codemp='".$this->ls_codemp."'                                   ".
					"    AND sno_salida.codnom='".$this->ls_codnom."'                                   ".
					"    AND sno_salida.codperi='".$this->ls_peractnom."'                               ". 
					"    AND sno_salida.valsal <> 0                                                     ".
					"    AND (sno_salida.tipsal = 'D'                                                   ".     
					"     OR sno_salida.tipsal = 'V2'                                                   ".
					"     OR sno_salida.tipsal = 'W2'                                                   ".
					"     OR sno_salida.tipsal = 'P1'                                                   ". 
					"     OR sno_salida.tipsal = 'V3'                                                   ".
					"     OR sno_salida.tipsal = 'W3' )                                                 ".
					"    AND sno_concepto.intingcon = '1'                                               ". 
					"    AND spi_cuentas.status = 'C'                                                   ".
					"    AND sno_personalnomina.codemp = sno_salida.codemp                              ".
					"    AND sno_personalnomina.codnom = sno_salida.codnom                              ".
					"    AND sno_personalnomina.codper = sno_salida.codper                              ".
					"    AND sno_salida.codemp = sno_concepto.codemp                                    ".
					"    AND sno_salida.codnom = sno_concepto.codnom                                    ".  
					"    AND sno_salida.codconc = sno_concepto.codconc                                  ".
					"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp                         ".
					"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm             ".
					"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm                   ".
					"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm                   ".
					"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm                   ".
					"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm                   ".
					"    AND spi_cuentas.codemp = sno_concepto.codemp                                   ".
					"    AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta                           ".
					"    AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp                          ".
					"    AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta                 ".
					"    AND sno_unidadadmin.codestpro1 =  spi_cuentas_estructuras.codestpro1   ". 
					"    AND sno_unidadadmin.codestpro2 = spi_cuentas_estructuras.codestpro2   ".
					"    AND sno_unidadadmin.codestpro3 = spi_cuentas_estructuras.codestpro3   ".
					"    AND sno_unidadadmin.codestpro4 = spi_cuentas_estructuras.codestpro4   ".
					"    AND sno_unidadadmin.codestpro5 = spi_cuentas_estructuras.codestpro5  ".
					"    AND sno_unidadadmin.estcla = spi_cuentas_estructuras.estcla        ".
					"   GROUP BY spi_cuentas.spi_cuenta,spi_cuentas_estructuras.codestpro1, ".
					"		     spi_cuentas_estructuras.codestpro2,  spi_cuentas_estructuras.codestpro3, ".
					" 		     spi_cuentas_estructuras.codestpro4, spi_cuentas_estructuras.codestpro5	  ";
		
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableingresos_ingreso ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableingresos_ingreso
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableingresos_contable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableingresos_contable
		//         Access: public (desde la clase sigesp_sno_r_contableingresos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas contables que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 11/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_estpreing=$_SESSION["la_empresa"]["estpreing"];
		if ($ls_estpreing==0)
		{
			// Buscamos todas aquellas cuentas contables que estan ligadas a las de ingreso de los conceptos que se 
			// integran directamente con presupuesto estas van por el haber de contabilidad
			$ls_sql="SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion, 'H' as operacion, ".
					"		sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, scg_cuentas ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
					"   AND sno_concepto.intingcon = '1'".
					"   AND spi_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND spi_cuentas.codemp = sno_concepto.codemp ".
					"   AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta ".
					"   AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta".
					"   GROUP BY spi_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta, 'D' as operacion, ".
					"		sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total ".
					"  FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas ".
					" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
					"   AND sno_salida.codnom='".$this->ls_codnom."' ".
					"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
					"   AND sno_salida.valsal <> 0 ".
					"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3' )".
					"   AND sno_concepto.intingcon = '1'".
					"   AND scg_cuentas.status = 'C'".
					"   AND sno_personalnomina.codemp = sno_salida.codemp ".
					"   AND sno_personalnomina.codnom = sno_salida.codnom ".
					"   AND sno_personalnomina.codper = sno_salida.codper ".
					"   AND sno_salida.codemp = sno_concepto.codemp ".
					"   AND sno_salida.codnom = sno_concepto.codnom ".
					"   AND sno_salida.codconc = sno_concepto.codconc ".
					"   AND scg_cuentas.codemp = sno_concepto.codemp ".
					"   AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon  ".
					"   GROUP BY scg_cuentas.sc_cuenta ";
		}
		else
		{
			$ls_sql=" SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion,  ".
					"	    'H' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total    ".
					"   FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, scg_cuentas,          ".
					"        spi_cuentas_estructuras, sno_unidadadmin                                         ".
					"  WHERE sno_salida.codemp='".$this->ls_codemp."'                                         ".
					"    AND sno_salida.codnom='".$this->ls_codnom."'                                         ".
					"    AND sno_salida.codperi='".$this->ls_peractnom."'  ".
					"    AND sno_salida.valsal <> 0  ".
					"    AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' ".
					"         OR sno_salida.tipsal = 'W2'  ".
					"         OR sno_salida.tipsal = 'P1'  ".
					"         OR sno_salida.tipsal = 'V3'  ".
					"         OR sno_salida.tipsal = 'W3') ". 
					"    AND sno_concepto.intingcon = '1'  ".
					"    AND spi_cuentas.status = 'C'      ".
					"    AND sno_personalnomina.codemp = sno_salida.codemp  ".
					"    AND sno_personalnomina.codnom = sno_salida.codnom  ".
					"    AND sno_personalnomina.codper = sno_salida.codper  ".
					"    AND sno_salida.codemp = sno_concepto.codemp        ". 
					"    AND sno_salida.codnom = sno_concepto.codnom        ". 
					"    AND sno_salida.codconc = sno_concepto.codconc      ".  
					"    AND sno_personalnomina.codemp = sno_unidadadmin.codemp ". 
					"    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
					"    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm  ".
					"    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm  ".
					"    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm  ".
					"    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm  ".
					"    AND spi_cuentas.codemp = sno_concepto.codemp  ".
					"    AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta   ".
					"    AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta      ".
					"    AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp  ".
					"    AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta  ".
					"    AND sno_concepto.codestpro1  = spi_cuentas_estructuras.codestpro1  ".
					"    AND sno_concepto.codestpro2 = spi_cuentas_estructuras.codestpro2  ".
					"    AND sno_concepto.codestpro3 = spi_cuentas_estructuras.codestpro3  ".
					"    AND sno_concepto.codestpro4 = spi_cuentas_estructuras.codestpro4  ".
					"    AND sno_concepto.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"    AND sno_concepto.estcla = spi_cuentas_estructuras.estcla ".
					"    GROUP BY spi_cuentas.sc_cuenta ";
			$ls_sql=$ls_sql." UNION ".
					"    SELECT spi_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denominacion,  ".
					"	        'H' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total   ".
					"      FROM sno_personalnomina, sno_salida, sno_concepto, spi_cuentas, scg_cuentas,          ".
					"           spi_cuentas_estructuras, sno_unidadadmin                                         ".
					"     WHERE sno_salida.codemp='".$this->ls_codemp."'                                         ".
					"       AND sno_salida.codnom='".$this->ls_codnom."'                                         ".
					"       AND sno_salida.codperi='".$this->ls_peractnom."'          ".
					"       AND sno_salida.valsal <> 0                                ".
					"       AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2'  ".
					"            OR sno_salida.tipsal = 'W2'  ".
					"            OR sno_salida.tipsal = 'P1'  ".
					"            OR sno_salida.tipsal = 'V3'  ".
					"            OR sno_salida.tipsal = 'W3') ". 
					"       AND sno_concepto.intingcon = '1'  ".
					"       AND spi_cuentas.status = 'C'      ".
					"       AND sno_personalnomina.codemp = sno_salida.codemp  ".
					"       AND sno_personalnomina.codnom = sno_salida.codnom  ".
					"       AND sno_personalnomina.codper = sno_salida.codper  ".
					"       AND sno_salida.codemp = sno_concepto.codemp  ".
					"       AND sno_salida.codnom = sno_concepto.codnom  ".
					" 	    AND sno_salida.codconc = sno_concepto.codconc  ".
					" 	    AND sno_personalnomina.codemp = sno_unidadadmin.codemp  ".
					" 	    AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm  ".
					" 	    AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm        ".
					"	    AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm        ".
					"	    AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm        ".
					"	    AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm        ".
					"	    AND spi_cuentas.codemp = sno_concepto.codemp                        ".
					"	    AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta                ".
					"	    AND spi_cuentas.sc_cuenta = scg_cuentas.sc_cuenta                   ".
					"	    AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp               ".
					"	    AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta       ".
					"	    AND sno_unidadadmin.codestpro1 =  spi_cuentas_estructuras.codestpro1 ".
					"	    AND sno_unidadadmin.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
					"	    AND sno_unidadadmin.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
					"	    AND sno_unidadadmin.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
					"	    AND sno_unidadadmin.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"	    AND sno_unidadadmin.estcla = spi_cuentas_estructuras.estcla ".
					"	  GROUP BY spi_cuentas.sc_cuenta ";
				$ls_sql=$ls_sql." UNION ".
					"     SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta,    ".
					"		     'D' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total  ".
					"       FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, ".
					"            spi_cuentas, spi_cuentas_estructuras, sno_unidadadmin  ".
					"     WHERE sno_salida.codemp='".$this->ls_codemp."'                                         ".
					"       AND sno_salida.codnom='".$this->ls_codnom."'                                         ".
					"       AND sno_salida.codperi='".$this->ls_peractnom."'          ".
					"		 AND sno_salida.valsal <> 0   ".
					"		 AND (sno_salida.tipsal = 'D'  ".
					"             OR sno_salida.tipsal = 'V2' ". 
					"             OR sno_salida.tipsal = 'W2' ".
					"             OR sno_salida.tipsal = 'P1' ".
					"             OR sno_salida.tipsal = 'V3' ".
					"             OR sno_salida.tipsal = 'W3')". 
					"		 AND sno_concepto.intingcon = '1' ".
					"		 AND scg_cuentas.status = 'C' ".
					"		 AND sno_personalnomina.codemp = sno_salida.codemp ".
					"		 AND sno_personalnomina.codnom = sno_salida.codnom ".
					"		 AND sno_personalnomina.codper = sno_salida.codper ".
					"		 AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"		 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"		 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"		 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"		 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"		 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
					"		 AND sno_salida.codemp = sno_concepto.codemp ".
					"		 AND sno_salida.codnom = sno_concepto.codnom ".
					"		 AND sno_salida.codconc = sno_concepto.codconc ".
					"		 AND scg_cuentas.codemp = sno_concepto.codemp ".
					"		 AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon ".
					"		 AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta  ".
					"		 AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"		 AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"		 AND sno_concepto.codestpro1  = spi_cuentas_estructuras.codestpro1 ".
					"		 AND sno_concepto.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
					"		 AND sno_concepto.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
					"		 AND sno_concepto.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
					"		 AND sno_concepto.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"		 AND sno_concepto.estcla = spi_cuentas_estructuras.estcla ".
					"	  GROUP BY scg_cuentas.sc_cuenta  ";
				$ls_sql=$ls_sql." UNION ".
					"     SELECT scg_cuentas.sc_cuenta as cuenta, MAX(scg_cuentas.denominacion) as denoconta,    ".
					" 		     'D' as operacion, sum((sno_salida.valsal*sno_concepto.poringcon)/100) as total  ".
					"     FROM sno_personalnomina, sno_salida, sno_concepto, scg_cuentas, ".
					"          spi_cuentas, spi_cuentas_estructuras, sno_unidadadmin  ".
					"     WHERE sno_salida.codemp='".$this->ls_codemp."'                                         ".
					"       AND sno_salida.codnom='".$this->ls_codnom."'                                         ".
					"       AND sno_salida.codperi='".$this->ls_peractnom."'          ".
					"	 AND sno_salida.valsal <> 0   ".
					"	 AND (sno_salida.tipsal = 'D' ".
					"         OR sno_salida.tipsal = 'V2' ".
					"         OR sno_salida.tipsal = 'W2' ".
					"         OR sno_salida.tipsal = 'P1' ".
					"         OR sno_salida.tipsal = 'V3' ".
					"         OR sno_salida.tipsal = 'W3') ".
					"	 AND sno_concepto.intingcon = '1' ".
					"	 AND scg_cuentas.status = 'C' ".
					"	 AND sno_personalnomina.codemp = sno_salida.codemp  ".
					"	 AND sno_personalnomina.codnom = sno_salida.codnom  ".
					"	 AND sno_personalnomina.codper = sno_salida.codper  ".
					"	 AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
					"	 AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
					"	 AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
					"	 AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
					"	 AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
					"	 AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
					"	 AND sno_salida.codemp = sno_concepto.codemp ".
					"	 AND sno_salida.codnom = sno_concepto.codnom ".
					"	 AND sno_salida.codconc = sno_concepto.codconc  ".
					"	 AND scg_cuentas.codemp = sno_concepto.codemp   ".
					"	 AND scg_cuentas.sc_cuenta = sno_concepto.cueconcon  ".
					"	 AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"	 AND spi_cuentas.codemp = sno_concepto.codemp ".
					"	 AND spi_cuentas.spi_cuenta = sno_concepto.spi_cuenta  ".
					"	 AND spi_cuentas_estructuras.codemp=spi_cuentas.codemp ".
					"	 AND spi_cuentas_estructuras.spi_cuenta= spi_cuentas.spi_cuenta ".
					"	 AND sno_unidadadmin.codestpro1 =  spi_cuentas_estructuras.codestpro1 ".
					"	 AND sno_unidadadmin.codestpro2 = spi_cuentas_estructuras.codestpro2 ".
					"	 AND sno_unidadadmin.codestpro3 = spi_cuentas_estructuras.codestpro3 ".
					"	 AND sno_unidadadmin.codestpro4 = spi_cuentas_estructuras.codestpro4 ".
					"	 AND sno_unidadadmin.codestpro5 = spi_cuentas_estructuras.codestpro5 ".
					"	 AND sno_unidadadmin.estcla = spi_cuentas_estructuras.estcla ".
					" GROUP BY scg_cuentas.sc_cuenta ";
		
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_contable ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_detalle->data=$this->io_sql->obtener_datos($rs_data);
				$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),array('0'=>'cuenta','1'=>'operacion'));
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableingresos_contable
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_especifico_presupuesto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
														 $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_especifico_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 09/04/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A , que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableconceptos_especifico_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_especifico_presupuesto_proyecto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
																  $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_especifico_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 17/07/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A , que se integran directamente con presupuesto
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom<='".$as_subnomhas."'";
		}
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_concepto.conprocon = '0' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_concepto.conprocon = '0' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla,spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableconceptos_especifico_presupuesto_proyecto_dt($ls_criterio);
			$this->DS->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprecon','7'=>'codded','8'=>'codtipper'),
								array('0'=>'total'),array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprecon','7'=>'codded','8'=>'codtipper'));		
		}
		return $lb_valido;
	}// end function uf_contableconceptos_especifico_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas_disponibilidad($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
											   $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableconceptos_especifico_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableconceptos)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 09/04/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A , que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_validar_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_validar_cuentas_disponibilidad_general($ls_desnom)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_validar_cuentas
		//		   Access: private
		//		 Argument: as_numsol // N?mero de solicitud
		//				   as_estsol  // Estatus de la solicitud
		//	  Description: Funci?n que busca que las cuentas presupuestarias est?n en la program?tica seleccionada
		//				   de ser asi coloca la sep en emitida sino la coloca en registrada
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Modificado Por: Ing. Yozelin Barragan
		// Fecha Creaci?n: 17/03/2007								Fecha ?ltima Modificaci?n : 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"       SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprecon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta as cueprecon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta ".
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_validar_cuentas_disponibilidad ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_validar_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableconceptos_especifico_presupuesto_proyecto_dt($as_criterio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableconceptos_especifico_presupuesto_proyecto_dt 
		//	    Arguments:
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos que son por proyectos
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D
		$ls_sql="SELECT sno_proyectopersonal.codper, sno_proyectopersonal.codproy, sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, spg_cuentas.spg_cuenta,".
				"		".$ls_cadena." as montoparcial, ".$ls_cadena." AS total, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		MAX(sno_proyectopersonal.pordiames) As pordiames, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto, spg_cuentas, sno_personalnomina, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_proyectopersonal.pordiames <> 0 ".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$as_criterio.
				"   AND sno_proyectopersonal.codemp = sno_personalnomina.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_personalnomina.codnom ".
				"   AND sno_proyectopersonal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_proyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_proyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_proyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_proyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_proyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_proyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_proyectopersonal.codper, sno_proyectopersonal.codproy, sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		   sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
		$ls_sql="SELECT sno_proyectopersonal.codper, sno_proyectopersonal.codproy, sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, spg_cuentas.spg_cuenta, ".
				"		".$ls_cadena." as montoparcial, ".$ls_cadena." AS total, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		MAX(sno_proyectopersonal.pordiames) As pordiames, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto, spg_cuentas, sno_personalnomina, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_proyectopersonal.pordiames <> 0 ".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$as_criterio.
				"   AND sno_proyectopersonal.codemp = sno_personalnomina.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_personalnomina.codnom ".
				"   AND sno_proyectopersonal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprecon ".
				"   AND sno_proyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_proyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_proyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_proyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_proyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_proyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_proyectopersonal.codper, sno_proyectopersonal.codproy, sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		   sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ".
				" ORDER BY codper, spg_cuenta, codproy, codded, codtipper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableconceptos_especifico_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_codestpro1ant="";
			$ls_codestpro2ant="";
			$ls_codestpro3ant="";
			$ls_codestpro4ant="";
			$ls_codestpro5ant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_codproyant="";
			$ls_coddedant="";
			$ls_codtipperant="";
			$ls_desdedant="";
			$ls_destipperant="";
			$li_totalpersonalant=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codded=$rs_data->fields["codded"];
				$ls_codtipper=$rs_data->fields["codtipper"];
				$ls_desded=$rs_data->fields["desded"];
				$ls_destipper=$rs_data->fields["destipper"];
				$li_totalpersonal=$rs_data->fields["totalpersonal"];
				$li_montoparcial=round($rs_data->fields["montoparcial"],3);
				$li_total=round($rs_data->fields["total"],2);
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				$ls_codproy=$rs_data->fields["codproy"];
				if(($ls_codper!=$ls_codant)||($ls_spgcuenta!=$ls_cuentaant))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiamesant<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
							$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
							$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
							$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
							$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprecon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
							$this->DS->insertRow("codded",$ls_coddedant);
							$this->DS->insertRow("codtipper",$ls_codtipperant);
							$this->DS->insertRow("desded",$ls_desdedant);
							$this->DS->insertRow("destipper",$ls_destipperant);
							$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
						}
					}
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$li_acumulado=$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$li_pordiamesant=$li_pordiames;
					$ls_codant=$ls_codper;
					$ls_codproyant=$ls_codproy;
					$ls_denominacionant=$ls_denominacion;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				$this->DS->insertRow("codestpro1",$ls_codestpro1);
				$this->DS->insertRow("codestpro2",$ls_codestpro2);
				$this->DS->insertRow("codestpro3",$ls_codestpro3);
				$this->DS->insertRow("codestpro4",$ls_codestpro4);
				$this->DS->insertRow("codestpro5",$ls_codestpro5);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprecon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
				$this->DS->insertRow("codded",$ls_codded);
				$this->DS->insertRow("codtipper",$ls_codtipper);
				$this->DS->insertRow("desded",$ls_desded);
				$this->DS->insertRow("destipper",$ls_destipper);
				$this->DS->insertRow("totalpersonal",$li_totalpersonal);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiamesant<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
				$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
				$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
				$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
				$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprecon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
				$this->DS->insertRow("codded",$ls_coddedant);
				$this->DS->insertRow("codtipper",$ls_codtipperant);
				$this->DS->insertRow("desded",$ls_desdedant);
				$this->DS->insertRow("destipper",$ls_destipperant);
				$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;    
	}// end function uf_contableconceptos_especifico_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_especifico_presupuesto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
													   $as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_especifico_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 11/05/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal   ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla,  spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal   ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper  ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_especifico_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_contableaportes_especifico_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_especifico_presupuesto_proyecto($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
															    $as_estcla,$as_subnomdes,$as_subnomhas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_contableaportes_presupuesto_proyecto
		//         Access: public (desde la clase sigesp_sno_r_contableaportes)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 17/07/2007 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		$ls_criterio="";
		if(!empty($as_estcla))
		{
			$ls_criterio="   AND spg_cuentas.estcla = '".$as_estcla."'".
						 "   AND spg_cuentas.codestpro1 = '".str_pad($as_codestpro1,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro2 = '".str_pad($as_codestpro2,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro3 = '".str_pad($as_codestpro3,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro4 = '".str_pad($as_codestpro4,25,"0","0")."'".
						 "   AND spg_cuentas.codestpro5 = '".str_pad($as_codestpro5,25,"0","0")."'";
		}
		if(!empty($as_subnomdes))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom>='".$as_subnomdes."'";
		}
		if(!empty($as_subnomhas))
		{
			$ls_criterio= $ls_criterio."   AND sno_personalnomina.codsubnom<='".$as_subnomhas."'";
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total, sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND spg_cuentas.status = 'C'".
				"   AND sno_concepto.conprocon = '0' ".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_concepto.codemp = spg_cuentas.codemp ".
				"   AND sno_concepto.cueprepatcon = spg_cuentas.spg_cuenta ".
				"   AND sno_concepto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_concepto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_concepto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_concepto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_concepto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_concepto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, spg_cuentas.spg_cuenta AS cueprepatcon, MAX(spg_cuentas.denominacion) AS denominacion, ".
				"		SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total , sno_personalnomina.codded, sno_personalnomina.codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto, spg_cuentas, sno_dedicacion, sno_tipopersonal ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND spg_cuentas.status = 'C'".
				$ls_criterio.
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_unidadadmin.codemp = spg_cuentas.codemp ".
				"   AND sno_unidadadmin.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_unidadadmin.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_unidadadmin.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_unidadadmin.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_unidadadmin.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_unidadadmin.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, spg_cuentas.spg_cuenta, sno_personalnomina.codded, sno_personalnomina.codtipper ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_especifico_presupuesto_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}		
		if($lb_valido)
		{
			$lb_valido=$this->uf_contableaportes_especifico_presupuesto_proyecto_dt($ls_criterio);
			$this->DS->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprepatcon','7'=>'codded','8'=>'codtipper'),
							    array('0'=>'total'),array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'estcla','6'=>'cueprepatcon','7'=>'codded','8'=>'codtipper'));
		}
		return $lb_valido;
	}// end function uf_contableaportes_especifico_presupuesto_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_contableaportes_especifico_presupuesto_proyecto_dt($as_criterio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_contableaportes_presupuesto_proyecto_dt 
		//	    Arguments: 
		//	      Returns: lb_valido true si es correcto la funcion o false en caso contrario
		//	  Description: Funci?n que se encarga de procesar la data para la contabilizaci?n de los conceptos de aportes por proyecto
	    //     Creado por: Ing. Yesenia Moreno
	    // Fecha Creaci?n: 17/07/2007
		///////////////////////////////////////////////////////////////////////////////////////////////////
	   	$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),3) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS NUMERIC),3) ";
				break;					
			case "INFORMIX":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS FLOAT),3) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT MAX(sno_proyecto.codestpro1) AS codestpro1, MAX(sno_proyecto.codestpro2) AS codestpro2, MAX(sno_proyecto.codestpro3) AS codestpro3, ".
				"		MAX(sno_proyecto.codestpro4) AS codestpro4, MAX(sno_proyecto.codestpro5) AS codestpro5, sno_proyecto.estcla, MAX(spg_cuentas.spg_cuenta) AS spg_cuenta, ".
				"		".$ls_cadena." AS total, MAX(sno_concepto.codprov) AS codprov, ".$ls_cadena." AS montoparcial, ".
				"		MAX(sno_concepto.cedben) AS cedben, sno_concepto.codconc, sno_proyecto.codproy, sno_proyectopersonal.codper, ".
				"		MAX(sno_personalnomina.codtipper) AS codtipper, COUNT(sno_personalnomina.codper) AS totalpersonal, ".
				"		MAX(spg_cuentas.denominacion) AS denominacion, MAX(sno_proyectopersonal.pordiames) AS pordiames, MAX(sno_personalnomina.codded) AS codded, ".
				"		MAX(sno_dedicacion.desded) AS desded, MAX(sno_tipopersonal.destipper) AS destipper  ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto, spg_cuentas, sno_personalnomina, sno_dedicacion, sno_tipopersonal  ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND spg_cuentas.status = 'C' ".
				$as_criterio.
				"   AND sno_proyectopersonal.codemp = sno_personalnomina.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_personalnomina.codnom ".
				"   AND sno_proyectopersonal.codper = sno_personalnomina.codper ".
				"   AND sno_personalnomina.codemp = sno_dedicacion.codemp ".
				"   AND sno_personalnomina.codded = sno_dedicacion.codded ".
				"   AND sno_personalnomina.codemp = sno_tipopersonal.codemp ".
				"   AND sno_personalnomina.codded = sno_tipopersonal.codded ".
				"   AND sno_personalnomina.codtipper = sno_tipopersonal.codtipper ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND spg_cuentas.codemp = sno_concepto.codemp ".
				"   AND spg_cuentas.spg_cuenta = sno_concepto.cueprepatcon ".
				"   AND sno_proyecto.codestpro1 = spg_cuentas.codestpro1 ".
				"   AND sno_proyecto.codestpro2 = spg_cuentas.codestpro2 ".
				"   AND sno_proyecto.codestpro3 = spg_cuentas.codestpro3 ".
				"   AND sno_proyecto.codestpro4 = spg_cuentas.codestpro4 ".
				"   AND sno_proyecto.codestpro5 = spg_cuentas.codestpro5 ".
				"   AND sno_proyecto.estcla = spg_cuentas.estcla ".
				" GROUP BY sno_proyectopersonal.codper, sno_proyecto.codproy, spg_cuentas.spg_cuenta, sno_concepto.codconc, sno_personalnomina.codded, sno_personalnomina.codtipper ".
				" ORDER BY sno_proyectopersonal.codper, spg_cuentas.spg_cuenta, sno_proyecto.codproy, sno_concepto.codconc, sno_personalnomina.codded, sno_personalnomina.codtipper  ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_contableaportes_especifico_presupuesto_proyecto_dt ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_codant="";
			$li_acumulado=0;
			$li_totalant=0;
			$ls_codestpro1ant="";
			$ls_codestpro2ant="";
			$ls_codestpro3ant="";
			$ls_codestpro4ant="";
			$ls_codestpro5ant="";
			$ls_estclaproyant="";
			$ls_cuentaant="";
			$ls_denominacionant="";
			$ls_conceptoant="";
			$ls_coddedant="";
			$ls_codtipperant="";
			$ls_desdedant="";
			$ls_destipperant="";
			$li_totalpersonalant=0;
			while(!$rs_data->EOF)
			{
				$ls_codper=$rs_data->fields["codper"];
				$ls_codded=$rs_data->fields["codded"];
				$ls_codtipper=$rs_data->fields["codtipper"];
				$ls_desded=$rs_data->fields["desded"];
				$ls_destipper=$rs_data->fields["destipper"];
				$li_totalpersonal=$rs_data->fields["totalpersonal"];
				$ls_codconc=$rs_data->fields["codconc"];
				$li_montoparcial=$rs_data->fields["montoparcial"];
				$li_total=$rs_data->fields["total"];
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estclaproy=$rs_data->fields["estcla"];
				$ls_spgcuenta=$rs_data->fields["spg_cuenta"];
				$ls_denominacion=$rs_data->fields["denominacion"];
				$li_pordiames=$rs_data->fields["pordiames"];
				if(($ls_codper!=$ls_codant)||(($ls_spgcuenta!=$ls_cuentaant)&&($ls_codconc!=$ls_conceptoant)))
				{
					if($li_acumulado!=0)
					{
						if((round($li_acumulado,3)!=round($li_totalant,3))&&($li_pordiames<1))
						{
							$li_montoparcial=round(($li_totalant-$li_acumulado),3);
							$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
							$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
							$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
							$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
							$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
							$this->DS->insertRow("estcla",$ls_estclaproyant);
							$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
							$this->DS->insertRow("total",$li_montoparcial);
							$this->DS->insertRow("denominacion",$ls_denominacionant);
							$this->DS->insertRow("codded",$ls_coddedant);
							$this->DS->insertRow("codtipper",$ls_codtipperant);
							$this->DS->insertRow("desded",$ls_desdedant);
							$this->DS->insertRow("destipper",$ls_destipperant);
							$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
						}
					}
					$li_acumulado=$rs_data->fields["montoparcial"];
					$li_montoparcial=round($rs_data->fields["montoparcial"],3);
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$ls_codant=$ls_codper;
					$ls_denominacionant=$ls_denominacion;
					$li_pordiamesant=$li_pordiames;
					$ls_conceptoant=$ls_codconc;
					$li_totalant=$li_total;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				else
				{
					$li_acumulado=$li_acumulado+$li_montoparcial;
					$ls_codestpro1ant=$ls_codestpro1;
					$ls_codestpro2ant=$ls_codestpro2;
					$ls_codestpro3ant=$ls_codestpro3;
					$ls_codestpro4ant=$ls_codestpro4;
					$ls_codestpro5ant=$ls_codestpro5;
					$ls_estclaproyant=$ls_estclaproy;
					$ls_cuentaant=$ls_spgcuenta;
					$li_totalant=$li_total;
					$ls_denominacionant=$ls_denominacion;
					$ls_coddedant=$ls_codded;
					$ls_codtipperant=$ls_codtipper;
					$ls_desdedant=$ls_desded;
					$ls_destipperant=$ls_destipper;
					$li_totalpersonalant=$li_totalpersonal;
				}
				$this->DS->insertRow("codestpro1",$ls_codestpro1);
				$this->DS->insertRow("codestpro2",$ls_codestpro2);
				$this->DS->insertRow("codestpro3",$ls_codestpro3);
				$this->DS->insertRow("codestpro4",$ls_codestpro4);
				$this->DS->insertRow("codestpro5",$ls_codestpro5);
				$this->DS->insertRow("estcla",$ls_estclaproy);
				$this->DS->insertRow("cueprepatcon",$ls_spgcuenta);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacion);
				$this->DS->insertRow("codded",$ls_codded);
				$this->DS->insertRow("codtipper",$ls_codtipper);
				$this->DS->insertRow("desded",$ls_desded);
				$this->DS->insertRow("destipper",$ls_destipper);
				$this->DS->insertRow("totalpersonal",$li_totalpersonal);
				$rs_data->MoveNext();
			}
			if((number_format($li_acumulado,3,".","")!=number_format($li_totalant,3,".",""))&&($li_pordiames<1))
			{
				$li_montoparcial=round(($li_totalant-$li_acumulado),3);
				$this->DS->insertRow("codestpro1",$ls_codestpro1ant);
				$this->DS->insertRow("codestpro2",$ls_codestpro2ant);
				$this->DS->insertRow("codestpro3",$ls_codestpro3ant);
				$this->DS->insertRow("codestpro4",$ls_codestpro4ant);
				$this->DS->insertRow("codestpro5",$ls_codestpro5ant);
				$this->DS->insertRow("estcla",$ls_estclaproyant);
				$this->DS->insertRow("cueprepatcon",$ls_cuentaant);
				$this->DS->insertRow("total",$li_montoparcial);
				$this->DS->insertRow("denominacion",$ls_denominacionant);
				$this->DS->insertRow("codded",$ls_coddedant);
				$this->DS->insertRow("codtipper",$ls_codtipperant);
				$this->DS->insertRow("desded",$ls_desdedant);
				$this->DS->insertRow("destipper",$ls_destipperant);
				$this->DS->insertRow("totalpersonal",$li_totalpersonalant);
			}
			$this->io_sql->free_result($rs_data);
		}		
		return  $lb_valido;    
	}// end function uf_contableaportes_especifico_presupuesto_proyecto_dt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadreconceptoaporte_aportes_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreconceptoaporte_aportes_proyecto
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 12/05/2008 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprepatcon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '1'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_concepto.codconc, sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		    sno_concepto.estcla, sno_concepto.cueprepatcon  ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprepatcon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" GROUP BY sno_concepto.codconc, sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprepatcon ";
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, sno_concepto.cueprepatcon, ".$ls_cadena." AS total ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND (sno_salida.tipsal = 'P2' OR sno_salida.tipsal = 'V4' OR sno_salida.tipsal = 'W4')".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND sno_proyectopersonal.codemp = sno_salida.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_salida.codnom ".
				"   AND sno_proyectopersonal.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				" GROUP BY sno_concepto.codconc, sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		   sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, sno_concepto.cueprepatcon ";
				" ORDER BY codconc, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprepatcon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_aportes_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cuentapresupuesto=$rs_data->fields["cueprepatcon"];
				$li_total=$rs_data->fields["total"];
				$ls_sql="SELECT denominacion ".
						"  FROM spg_cuentas ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND status = 'C'".
						"   AND estcla = '".$ls_estcla."'".
						"   AND codestpro1 = '".$ls_codestpro1."'".
						"   AND codestpro2 = '".$ls_codestpro2."'".
						"   AND codestpro3 = '".$ls_codestpro3."'".
						"   AND codestpro4 = '".$ls_codestpro4."'".
						"   AND codestpro5 = '".$ls_codestpro5."'".
						"   AND spg_cuenta = '".$ls_cuentapresupuesto."'";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_aportes_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS->insertRow("codestpro1",$ls_codestpro1);
						$this->DS->insertRow("codestpro2",$ls_codestpro2);
						$this->DS->insertRow("codestpro3",$ls_codestpro3);
						$this->DS->insertRow("codestpro4",$ls_codestpro4);
						$this->DS->insertRow("codestpro5",$ls_codestpro5);
						$this->DS->insertRow("cueprepatcon",$ls_cuentapresupuesto);
						$this->DS->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreconceptoaporte_aportes_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cuadreconceptoaporte_conceptos_proyecto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_cuadreconceptoaporte_conceptos_proyecto
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo A, D, P1
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 15/09/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql=new class_sql($this->io_conexion);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),2) ";
				break;
			case "MYSQLI":
				$ls_cadena=" ROUND((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)),2) ";
				break;
			case "POSTGRES":
				$ls_cadena=" ROUND(CAST((SUM(ROUND( CAST(sno_salida.valsal as numeric), 2))*MAX(sno_proyectopersonal.pordiames)) AS NUMERIC),2) ";
				break;					
		}
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que se integran directamente con presupuesto
		$ls_sql="SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla,sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '1'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos A y D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0".
				"   AND sno_concepto.intprocon = '0'".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		sno_concepto.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '1' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_concepto.codestpro1, sno_concepto.codestpro2, sno_concepto.codestpro3, sno_concepto.codestpro4, sno_concepto.codestpro5, ".
				"		   sno_concepto.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos  D, que no se integran directamente con presupuesto
		// entonces las buscamos seg?n la estructura de la unidad administrativa a la que pertenece el personal
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		sno_unidadadmin.estcla, sno_concepto.cueprecon, SUM(ROUND( CAST(sno_salida.valsal as numeric), 2)) as total ".
				"  FROM sno_personalnomina, sno_unidadadmin, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_concepto.intprocon = '0' ".
				"   AND sno_concepto.conprocon = '0' ".
				"   AND sno_personalnomina.codemp = sno_salida.codemp ".
				"   AND sno_personalnomina.codnom = sno_salida.codnom ".
				"   AND sno_personalnomina.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				"   AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				"   AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				"   AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				"   AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				"   AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				"   AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" GROUP BY sno_unidadadmin.codestpro1, sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				"		   sno_unidadadmin.estcla, sno_concepto.cueprecon ";
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, sno_concepto.cueprecon, ".$ls_cadena." as total ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'A' OR sno_salida.tipsal = 'V1' OR sno_salida.tipsal = 'W1') ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_proyectopersonal.pordiames <> 0 ".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND sno_proyectopersonal.codemp = sno_salida.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_salida.codnom ".
				"   AND sno_proyectopersonal.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		   sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, sno_concepto.cueprecon ";
		// Buscamos todas aquellas cuentas presupuestarias de los conceptos D , que se integran directamente con presupuesto
		$ls_sql=$ls_sql." UNION ".
				"SELECT sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, sno_concepto.cueprecon, ".$ls_cadena." as total ".
				"  FROM sno_proyectopersonal, sno_proyecto, sno_salida, sno_concepto ".
				" WHERE sno_salida.codemp='".$this->ls_codemp."' ".
				"   AND sno_salida.codnom='".$this->ls_codnom."' ".
				"   AND sno_salida.codperi='".$this->ls_peractnom."' ".
				"   AND (sno_salida.tipsal = 'D' OR sno_salida.tipsal = 'V2' OR sno_salida.tipsal = 'W2' OR sno_salida.tipsal = 'P1' OR sno_salida.tipsal = 'V3' OR sno_salida.tipsal = 'W3')".
				"   AND sno_concepto.sigcon = 'E' ".
				"   AND sno_salida.valsal <> 0 ".
				"   AND sno_proyectopersonal.pordiames <> 0 ".
				"   AND sno_concepto.conprocon = '1' ".
				"   AND sno_proyectopersonal.codemp = sno_proyecto.codemp ".
				"   AND sno_proyectopersonal.codproy = sno_proyecto.codproy ".
				"   AND sno_proyectopersonal.codemp = sno_salida.codemp ".
				"   AND sno_proyectopersonal.codnom = sno_salida.codnom ".
				"   AND sno_proyectopersonal.codper = sno_salida.codper ".
				"   AND sno_salida.codemp = sno_concepto.codemp ".
				"   AND sno_salida.codnom = sno_concepto.codnom ".
				"   AND sno_salida.codconc = sno_concepto.codconc ".
				" GROUP BY sno_proyecto.codestpro1, sno_proyecto.codestpro2, sno_proyecto.codestpro3, ".
				"		   sno_proyecto.codestpro4, sno_proyecto.codestpro5, sno_proyecto.estcla, sno_concepto.cueprecon ";
				" ORDER BY codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, cueprecon ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_conceptos_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_cuentapresupuesto=$rs_data->fields["cueprecon"];
				$li_total=$rs_data->fields["total"];
				$ls_sql="SELECT denominacion ".
						"  FROM spg_cuentas ".
						" WHERE codemp='".$this->ls_codemp."' ".
						"   AND status = 'C'".
						"   AND estcla = '".$ls_estcla."'".
						"   AND codestpro1 = '".$ls_codestpro1."'".
						"   AND codestpro2 = '".$ls_codestpro2."'".
						"   AND codestpro3 = '".$ls_codestpro3."'".
						"   AND codestpro4 = '".$ls_codestpro4."'".
						"   AND codestpro5 = '".$ls_codestpro5."'".
						"   AND spg_cuenta = '".$ls_cuentapresupuesto."'";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_conceptos_proyecto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if(!$row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS_detalle->insertRow("codestpro1",$ls_codestpro1);
						$this->DS_detalle->insertRow("codestpro2",$ls_codestpro2);
						$this->DS_detalle->insertRow("codestpro3",$ls_codestpro3);
						$this->DS_detalle->insertRow("codestpro4",$ls_codestpro4);
						$this->DS_detalle->insertRow("codestpro5",$ls_codestpro5);
						$this->DS_detalle->insertRow("cueprecon",$ls_cuentapresupuesto);
						$this->DS_detalle->insertRow("denominacion","No Existe la cuenta en la Estructura.");
						$this->DS_detalle->insertRow("total",$li_total);
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}		
		return $lb_valido;
	}// end function uf_cuadreconceptoaporte_conceptos_proyecto
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_guarderiaconcepto_presupuesto()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_guarderiaconcepto_presupuesto
		//         Access: public (desde la clase sigesp_sno_r_cuadreconceptoaporte)  
		//	      Returns: lb_valido True si se creo el Data stored correctamente ? False si no se creo
		//    Description: funci?n que busca la informaci?n de las cuentas presupuestarias que afectan los conceptos de tipo P2
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creaci?n: 15/09/2006 								Fecha ?ltima Modificaci?n :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$lb_checkin=0;
		$this->io_sql=new class_sql($this->io_conexion);
		$this->ls_tipnom=$_SESSION["la_nomina"]["tipnom"];
		$this->ls_estctaalt=$_SESSION["la_nomina"]["estctaalt"];
		$ls_codtipdoc = trim($this->uf_select_config("SNO","CONFIG","GUARDERIA","C","")); 
		$ls_cod_pro="----------";
		$ls_tipo_destino="B";
		$ls_spg_cuentaobrero= trim($this->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA OBRERO","C","")); 
		$ls_spg_cuentapersonal= trim($this->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA PERSONAL","C","")); 
		$ls_spg_cuentapersonalcontratado=trim($this->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA PERSONAL CONTRATADO","----------","C"));
		$ls_spg_cuentaobrerocontratado=trim($this->uf_select_config("SNO","NOMINA","DESTINO GUARDERIA OBRERO CONTRATADO","----------","C"));
		$ls_beneguarderia=trim($this->uf_select_config("SNO","CONFIG","GENERAR RECEPCION DOCUMENTO BENEFICIARIO GUARDERIA","0","I"));
		switch($ls_beneguarderia)
		{
			case "0":
				$ls_campo = ",sno_personal.cedper AS beneficiario  "; 
			break;
			
			case "1":
				$ls_campo = ",sno_guarderias.cedbene AS beneficiario "; 
			break;
		}
		if ($this->ls_estctaalt=='1')
		{
			$ls_campo2 = ",rpc_beneficiario.sc_cuentarecdoc AS sc_cuenta,  "; 
		}
		else
		{
			$ls_campo2 = ",rpc_beneficiario.sc_cuenta AS sc_cuenta,  "; 
		}
		
		$ls_sql=" SELECT sno_guarderias.cedbene, sno_guarderias.nombene, sno_personal.codper, sno_guarderias.monto as valsal, sno_personal.cedper, ".
				" SUBSTR(CAST(sno_guarderias.codguar AS CHAR(10)),7,4) AS codguar, sno_unidadadmin.codestpro1, ".
				" sno_unidadadmin.codestpro2, sno_unidadadmin.codestpro3, sno_unidadadmin.codestpro4, sno_unidadadmin.codestpro5, ".
				" sno_unidadadmin.estcla ".$ls_campo2."sno_nomina.tipnom, scg_cuentas.denominacion as denominacion_cont ".$ls_campo. 
				" FROM sno_concepto, sno_salida, sno_personalnomina, sno_guarderias, sno_personal,sno_unidadadmin,sno_nomina,rpc_beneficiario,scg_cuentas ".
				" WHERE sno_concepto.codemp='".$this->ls_codemp."' ".
				" AND sno_concepto.codnom='".$this->ls_codnom."' ".
				" AND sno_concepto.guarrepcon='1' ".
				" AND sno_concepto.sigcon='R' ".
				" AND sno_concepto.codemp = sno_salida.codemp ".
				" AND sno_concepto.codnom = sno_salida.codnom ".
				" AND sno_concepto.codconc = sno_salida.codconc ".
				" AND sno_salida.codemp = sno_personalnomina.codemp ".
				" AND sno_salida.codnom = sno_personalnomina.codnom ".
				" AND sno_salida.codper = sno_personalnomina.codper ".
				" AND sno_personalnomina.codemp = sno_guarderias.codemp ".
				" AND sno_personalnomina.codper = sno_guarderias.codper ".
				" AND sno_personalnomina.codemp = sno_personal.codemp ".
				" AND sno_personalnomina.codper = sno_personal.codper ".
				" AND sno_personalnomina.codemp = sno_unidadadmin.codemp ".
				" AND sno_personalnomina.minorguniadm = sno_unidadadmin.minorguniadm ".
				" AND sno_personalnomina.ofiuniadm = sno_unidadadmin.ofiuniadm ".
				" AND sno_personalnomina.uniuniadm = sno_unidadadmin.uniuniadm ".
				" AND sno_personalnomina.depuniadm = sno_unidadadmin.depuniadm ".
				" AND sno_personalnomina.prouniadm = sno_unidadadmin.prouniadm ".
				" AND sno_concepto.codemp = sno_nomina.codemp ".
				" AND sno_concepto.codnom = sno_nomina.codnom ".
				" AND sno_guarderias.cedbene = rpc_beneficiario.ced_bene ".
				" AND rpc_beneficiario.sc_cuenta = scg_cuentas.sc_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Report M?TODO->uf_guarderiaconcepto_presupuesto ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_beneficiario=$rs_data->fields["beneficiario"];
				$ls_nombene=$rs_data->fields["nombene"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_codguar=$rs_data->fields["codguar"];
				$ldec_monto=$rs_data->fields["valsal"];
				$ls_codestpro1=$rs_data->fields["codestpro1"];
				$ls_codestpro2=$rs_data->fields["codestpro2"];
				$ls_codestpro3=$rs_data->fields["codestpro3"];
				$ls_codestpro4=$rs_data->fields["codestpro4"];
				$ls_codestpro5=$rs_data->fields["codestpro5"];
				$ls_codestpro=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				$ls_estcla=$rs_data->fields["estcla"];
				$ls_tippernom=$rs_data->fields["tipnom"];
				$ls_ctaben=$rs_data->fields["sc_cuenta"];
				$ls_denom_cont=$rs_data->fields["denominacion_cont"];
				$ls_spgcuenta="";
				switch ($ls_tippernom)
				{
					case "1":
						$ls_spgcuenta=$ls_spg_cuentapersonal;
					break;
					case "5":
						$ls_spgcuenta=$ls_spg_cuentapersonal;
					break;
					case "9":
						$ls_spgcuenta=$ls_spg_cuentapersonal;
					break;
					case "10":
						$ls_spgcuenta=$ls_spg_cuentapersonal;
					break;
					case "2":
						$ls_spgcuenta=$ls_spg_cuentapersonalcontratado;
					break;
					case "6":
						$ls_spgcuenta=$ls_spg_cuentapersonalcontratado;
					break;
					case "13":
						$ls_spgcuenta=$ls_spg_cuentapersonalcontratado;
					break;
					case "14":
						$ls_spgcuenta=$ls_spg_cuentapersonalcontratado;
					break;
					case "3":
						$ls_spgcuenta=$ls_spg_cuentaobrero;
					break;
					case "4":
						$ls_spgcuenta=$ls_spg_cuentaobrerocontratado;
					break;
				}
				$this->DS_detalle->insertRow("cuenta",$ls_ctaben);
				$this->DS_detalle->insertRow("operacion",'H');
				$this->DS_detalle->insertRow("denominacion_cont",$ls_denom_cont);
				$this->DS_detalle->insertRow("total",$ldec_monto);
				if($ls_spgcuenta=="")
				{
					$this->io_msg->message("No estan definidas las cuentas presupuestarias para las Guarderias.");
					return false;
				}
				$ls_sql="SELECT spg_cuentas.denominacion, spg_cuentas.sc_cuenta as contable, scg_cuentas.denominacion as denominacion_cont ".
						"  FROM spg_cuentas,scg_cuentas ".
						" WHERE spg_cuentas.codemp='".$this->ls_codemp."' ".
						"   AND spg_cuentas.status = 'C'".
						"   AND spg_cuentas.codestpro1 = '".$ls_codestpro1."'".
						"   AND spg_cuentas.codestpro2 = '".$ls_codestpro2."'".
						"   AND spg_cuentas.codestpro3 = '".$ls_codestpro3."'".
						"   AND spg_cuentas.codestpro4 = '".$ls_codestpro4."'".
						"   AND spg_cuentas.codestpro5 = '".$ls_codestpro5."'".
						"   AND spg_cuentas.estcla='".$ls_estcla."'".
						"   AND spg_cuentas.spg_cuenta = '".$ls_spgcuenta."' ".
						"   AND spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta";
				$rs_data2=$this->io_sql->select($ls_sql);
				if($rs_data2===false)
				{
					$this->io_mensajes->message("CLASE->Report M?TODO->uf_cuadreconceptoaporte_aportes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					$lb_valido=false;
				}
				else
				{
					if($row=$this->io_sql->fetch_row($rs_data2))
					{
						$this->DS->insertRow("codestpro1",$ls_codestpro1);
						$this->DS->insertRow("codestpro2",$ls_codestpro2);
						$this->DS->insertRow("codestpro3",$ls_codestpro3);
						$this->DS->insertRow("codestpro4",$ls_codestpro4);
						$this->DS->insertRow("codestpro5",$ls_codestpro5);
						$this->DS->insertRow("spgcuenta",$ls_spgcuenta);
						$this->DS->insertRow("denominacion",$rs_data2->fields["denominacion"]);
						$this->DS->insertRow("total",$ldec_monto);

						$this->DS_detalle->insertRow("cuenta",$rs_data2->fields["contable"]);
						$this->DS_detalle->insertRow("operacion",'D');
						$this->DS_detalle->insertRow("denominacion_cont",$rs_data2->fields["denominacion_cont"]);
						$this->DS_detalle->insertRow("total",$ldec_monto);
						$lb_checkin++;
					}
					$this->io_sql->free_result($rs_data2);
				}
				$rs_data->MoveNext();
			}
		}
		if(($lb_valido)&&($lb_checkin<>0))
		{
			$this->DS->group_by(array('0'=>'codestpro1','1'=>'codestpro2','2'=>'codestpro3','3'=>'codestpro4','4'=>'codestpro5','5'=>'spgcuenta','6'=>'denominacion'),
							    array('0'=>'total'),'total');
			
			$this->DS_detalle->group_by(array('0'=>'cuenta','1'=>'operacion'),array('0'=>'total'),'total');
		}
		else
		{
			$lb_valido=false;
		}

		return $lb_valido;
	}// end function uf_guarderiaconcepto_presupuesto
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>