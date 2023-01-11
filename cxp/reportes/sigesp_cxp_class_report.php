<?php
/***********************************************************************************
* @fecha de modificacion: 24/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_cxp_class_report
{
	
	private $io_conexion;
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct($as_path="../../")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sep_class_report
		//		   Access: public
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. Yesenia Moreno /Ing. Luis Lang
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once($as_path."base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$this->io_conexion=$io_include->uf_conectar();
		//$this->io_conexion->debug=true;
		require_once($as_path."base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($this->io_conexion);
		$this->DS=new class_datastore();
		$this->DS_ISLR=new class_datastore();
		$this->ds_detalle=new class_datastore();
		$this->ds_detalle_solpago1x1000=new class_datastore();
		require_once($as_path."base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();
		require_once($as_path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();
		require_once($as_path."base/librerias/php/general/sigesp_lib_fecha.php");
		$this->io_fecha=new class_fecha();
		require_once($as_path."shared/class_folder/sigesp_c_seguridad.php");
		$this->io_seguridad=new sigesp_c_seguridad();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->rs_data ="";
		$this->rs_detalle ="";
		$this->rs_solprevias ="";
		$this->rs_clasificador ="";
		$this->rs_solicitudes ="";
		$this->rs_ndnc ="";
		$this->rs_pagactuales ="";
	}// end function sigesp_sep_class_report
	//-----------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitud($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitud
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ".
							" AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_solicitudes.numsol, cxp_solicitudes.cod_pro, cxp_solicitudes.ced_bene, cxp_solicitudes.codfuefin,".
				"       cxp_solicitudes.tipproben, cxp_solicitudes.fecemisol, cxp_solicitudes.consol, cxp_solicitudes.estprosol,".
				"       cxp_solicitudes.monsol, cxp_solicitudes.obssol, cxp_solicitudes.estaprosol, cxp_solicitudes.nombenaltcre,".
		        "       cxp_solicitudes.codusureg, cxp_solicitudes.usuaprosol, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.rifpro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.rifben ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE '-' END ) AS rifpro, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.dirpro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.dirbene ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE '-' END ) AS dirproben, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.emailrep ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.email ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE '-' END ) AS correo, ".
				"       (SELECT denfuefin".
				"		   FROM sigesp_fuentefinanciamiento".
				"         WHERE sigesp_fuentefinanciamiento.codemp=cxp_solicitudes.codemp".
				"           AND sigesp_fuentefinanciamiento.codfuefin=cxp_solicitudes.codfuefin) AS denfuefin,".
				"       (SELECT MAX(nomban)".
				"		   FROM scb_banco,cxp_sol_banco".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codban=scb_banco.codban GROUP BY cxp_sol_banco.numsol) AS nomban,".
				"       (SELECT MAX(ctaban)".
				"		   FROM cxp_sol_banco".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol GROUP BY cxp_sol_banco.numsol) AS ctaban,".
				"       (SELECT MAX(nomtipcta)".
				"		   FROM cxp_sol_banco,scb_ctabanco,scb_tipocuenta".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.ctaban=scb_ctabanco.ctaban".
				"           AND cxp_sol_banco.codban=scb_ctabanco.codban".
				"           AND scb_ctabanco.codtipcta=scb_tipocuenta.codtipcta GROUP BY cxp_sol_banco.numsol) AS tipcta,".
				"       (SELECT nomban ".
				"		   FROM rpc_proveedor, scb_banco ".
				"         WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"           AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro ".
				"           AND rpc_proveedor.codemp=scb_banco.codemp ".
				"           AND rpc_proveedor.codban=scb_banco.codban) AS nomban_prov,".
				"       (SELECT ctaban ".
				"		   FROM rpc_proveedor".
				"         WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp".
				"           AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) AS ctaban_prov,".
				"       (SELECT nomban ".
				"		   FROM rpc_beneficiario, scb_banco ".
				"         WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"           AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene ".
				"           AND rpc_beneficiario.codemp=scb_banco.codemp ".
				"           AND rpc_beneficiario.codban=scb_banco.codban) AS nomban_bene,".
				"       (SELECT ctaban ".
				"		   FROM rpc_beneficiario ".
				"         WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp".
				"           AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) AS ctaban_bene,".
				"       (SELECT MAX(denuniadm)".
				"		   FROM spg_unidadadministrativa,cxp_dt_solicitudes, cxp_rd ".
				"         WHERE cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"           AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"           AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"           AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				"           AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"           AND cxp_rd.coduniadm=spg_unidadadministrativa.coduniadm".
				"           AND cxp_rd.coduniadm=spg_unidadadministrativa.coduniadm) AS denuniadm, ".
				"       (SELECT MAX(dencla) ".
				"          FROM cxp_clasificador_rd,cxp_dt_solicitudes, cxp_rd ".
				"         WHERE cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"           AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"           AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"           AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				"           AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"         	AND cxp_rd.codcla = cxp_clasificador_rd.codcla) as dencla,".
				"		(SELECT nomusu FROM sss_usuarios".
				"		 WHERE trim(sss_usuarios.codusu)=trim(cxp_solicitudes.codusureg))AS nomusureg,".
				"		(SELECT apeusu FROM sss_usuarios".
				"		 WHERE trim(sss_usuarios.codusu)=trim(cxp_solicitudes.codusureg))AS apeusureg".
				"  FROM cxp_solicitudes {$ls_filtrofrom} ".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_solicitudes.numsol='".$as_numsol."' {$ls_filtroest} ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitud_tipodocumento($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitud_tipodocumento
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la una solicitud de pago y los tipos de documento que presentan
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT DISTINCT(cxp_solicitudes.numsol), cxp_solicitudes.cod_pro, cxp_solicitudes.ced_bene, cxp_solicitudes.codfuefin,".
				"       cxp_solicitudes.tipproben, cxp_solicitudes.fecemisol, cxp_solicitudes.consol, cxp_solicitudes.estprosol,".
				"       cxp_solicitudes.monsol, cxp_solicitudes.obssol, cxp_solicitudes.estaprosol,cxp_rd.codtipdoc,".
				"       cxp_documento.dentipdoc,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                      				  	 FROM rpc_proveedor ".
				"                                       				WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         				  AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       				WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                       				 FROM rpc_beneficiario ".
				"                                       				WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                       				  AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       				ELSE 'NINGUNO' END ) AS nombre, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.rifpro ".
				"                                        				 FROM rpc_proveedor ".
				"                                       				WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         				  AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       				WHEN 'B' THEN (SELECT rpc_beneficiario.rifben ".
				"                                       				 FROM rpc_beneficiario ".
				"                                       				WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         				  AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       				ELSE '-' END ) AS rifpro, ".
		        "		(SELECT nomusu FROM sss_usuarios".
				"		 WHERE trim(sss_usuarios.codusu)=trim(cxp_rd.codusureg))AS nomusureg, ".
				"		(SELECT apeusu FROM sss_usuarios".
				"		 WHERE trim(sss_usuarios.codusu)=trim(cxp_rd.codusureg))AS apeusureg, ".
				"       (SELECT denfuefin".
				"		   FROM sigesp_fuentefinanciamiento".
				"         WHERE sigesp_fuentefinanciamiento.codemp=cxp_solicitudes.codemp".
				"           AND sigesp_fuentefinanciamiento.codfuefin=cxp_solicitudes.codfuefin) AS denfuefin,".
				"       (SELECT dencla".
				"		   FROM cxp_clasificador_rd".
				"         WHERE cxp_clasificador_rd.codcla=cxp_rd.codcla) AS dencla".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd,cxp_documento ".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_solicitudes.numsol='".$as_numsol."' ".
				"   AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_documento.codtipdoc".
				"   AND cxp_rd.codtipdoc=cxp_documento.codtipdoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_rec_doc_solicitud($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_rec_doc_solicitud
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT cxp_dt_solicitudes.numsol,MAX(cxp_dt_solicitudes.codtipdoc) AS codtipdoc ,MAX(cxp_dt_solicitudes.cod_pro) AS cod_pro,".
				"       MAX(cxp_dt_solicitudes.ced_bene) AS ced_bene,cxp_rd.numrecdoc, cxp_documento.dentipdoc,".
				"       cxp_rd.montotdoc,cxp_rd.mondeddoc, cxp_rd.moncardoc,cxp_rd.fecemidoc,cxp_rd.procede,cxp_rd.numref,".
				"       (SELECT MAX(procede_doc)".
				"          FROM cxp_rd_scg".
				"         WHERE cxp_rd_scg.codemp=cxp_rd.codemp".
				"           AND cxp_rd_scg.numrecdoc=cxp_rd.numrecdoc".
				"           AND cxp_rd_scg.codtipdoc=cxp_rd.codtipdoc".
				"           AND cxp_rd_scg.ced_bene=cxp_rd.ced_bene".
				"           AND cxp_rd_scg.cod_pro=cxp_rd.cod_pro ".
				"           AND cxp_rd_scg.debhab='D') AS procede_docscg,".
				"       (SELECT MAX(procede_doc)".
				"          FROM cxp_rd_spg".
				"         WHERE cxp_rd_spg.codemp=cxp_rd.codemp".
				"           AND cxp_rd_spg.numrecdoc=cxp_rd.numrecdoc".
				"           AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc".
				"           AND cxp_rd_spg.ced_bene=cxp_rd.ced_bene".
				"           AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro) AS procede_docspg,".
				"       (SELECT MAX(numdoccom)".
				"          FROM cxp_rd_scg".
				"         WHERE cxp_rd_scg.codemp=cxp_rd.codemp".
				"           AND cxp_rd_scg.numrecdoc=cxp_rd.numrecdoc".
				"           AND cxp_rd_scg.codtipdoc=cxp_rd.codtipdoc".
				"           AND cxp_rd_scg.ced_bene=cxp_rd.ced_bene".
				"           AND cxp_rd_scg.cod_pro=cxp_rd.cod_pro ".
				"           AND cxp_rd_scg.debhab='D') AS numdoccomscg,".
				"       (SELECT MAX(numdoccom)".
				"          FROM cxp_rd_spg".
				"         WHERE cxp_rd_spg.codemp=cxp_rd.codemp".
				"           AND cxp_rd_spg.numrecdoc=cxp_rd.numrecdoc".
				"           AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc".
				"           AND cxp_rd_spg.ced_bene=cxp_rd.ced_bene".
				"           AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro) AS numdoccomspg".
				"  FROM cxp_dt_solicitudes, cxp_solicitudes, cxp_rd, cxp_documento".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_documento.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				" GROUP BY cxp_rd.codemp, cxp_dt_solicitudes.numsol,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene, cxp_documento.dentipdoc,".
				"       cxp_rd.montotdoc,cxp_rd.mondeddoc, cxp_rd.moncardoc,cxp_rd.fecemidoc,cxp_rd.procede,cxp_rd.numref".
				" ORDER BY cxp_rd.numrecdoc ASC";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_rec_doc_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_rec->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_rec_doc_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//------>Carlos Zambrano
		//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_compromiso_afectado($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_compromiso_afectado
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//$this->ds_comp_afec = new class_datastore();
		$ls_sql="SELECT MAX(cxp_rd_spg.numdoccom) as numdoccom".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd_spg,cxp_documento".
				" WHERE cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_documento.estcon='1'".
				"   AND cxp_documento.estpre='1'".
				"   AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"   AND cxp_rd_spg.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd_spg.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd_spg.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				"   AND cxp_rd_spg.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd_spg.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd_spg.codtipdoc=cxp_documento.codtipdoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_compromiso_afectado ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_comafec=$row["numdoccom"];
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $ls_comafec;
	}// end function uf_select_detalle_spg
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_porcentaje_eval($as_cargo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_compromiso_afectado
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT tipo_iva ".
				" FROM sigesp_cargos ". 
				" WHERE codcar='".$as_cargo."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_porcentaje_eval ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_tipo=$row["tipo_iva"];
			}
			else
			{
				$ls_tipo='0';
			}
		}
		return $ls_tipo;
	}// end function uf_porcentaje_eval

	//------>Carlos Zambrano
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalle_spg($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalle_spg
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_spg = new class_datastore();
		$ls_sql="SELECT cxp_dt_solicitudes.numsol, cxp_rd_spg.codestpro, cxp_rd_spg.spg_cuenta, cxp_rd_spg.estcla, ".
				"		sum(cxp_rd_spg.monto) AS monto ,max(spg_cuentas.denominacion) as denominacion, ".
				"       (SELECT denestpro3 ".
				"          FROM spg_ep3 ".
				"         WHERE spg_ep3.codemp = cxp_rd_spg.codemp ".
				"           AND spg_ep3.codestpro1 = SUBSTR(cxp_rd_spg.codestpro,1,25) ".
				"           AND spg_ep3.codestpro2 = SUBSTR(cxp_rd_spg.codestpro,26,25) ".
				"           AND spg_ep3.codestpro3 = SUBSTR(cxp_rd_spg.codestpro,51,25) ".
				"           AND spg_ep3.estcla = cxp_rd_spg.estcla ) AS denestpro3".
				"  FROM cxp_rd_spg,cxp_dt_solicitudes,spg_cuentas".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_rd_spg.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd_spg.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd_spg.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd_spg.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd_spg.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				"   AND spg_cuentas.codemp=cxp_rd_spg.codemp".
				"   AND spg_cuentas.spg_cuenta=cxp_rd_spg.spg_cuenta".
				"   AND SUBSTR(cxp_rd_spg.codestpro,1,25)=spg_cuentas.codestpro1 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,26,25)=spg_cuentas.codestpro2 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,51,25)=spg_cuentas.codestpro3 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,76,25)=spg_cuentas.codestpro4 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,101,25)=spg_cuentas.codestpro5 ".
				"   AND spg_cuentas.estcla=cxp_rd_spg.estcla".
				" GROUP BY cxp_rd_spg.codemp,cxp_dt_solicitudes.numsol, cxp_rd_spg.codestpro, cxp_rd_spg.estcla, cxp_rd_spg.spg_cuenta".
				" ORDER BY cxp_rd_spg.codemp,cxp_dt_solicitudes.numsol, cxp_rd_spg.codestpro, cxp_rd_spg.estcla, cxp_rd_spg.spg_cuenta ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_detalle_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_spg->data=$this->io_sql->obtener_datos($rs_data);
			}
		}
		return $lb_valido;
	}// end function uf_select_detalle_spg
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalle_scg($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalle_scg
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_scg = new class_datastore();
		$ls_sql="SELECT cxp_rd_scg.sc_cuenta,SUM(cxp_rd_scg.monto) AS monto,cxp_rd_scg.debhab,MAX(scg_cuentas.denominacion) as denominacion".
				" FROM cxp_rd_scg,cxp_dt_solicitudes,scg_cuentas".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_rd_scg.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd_scg.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd_scg.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd_scg.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd_scg.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				"   AND scg_cuentas.codemp=cxp_rd_scg.codemp".
				"   AND trim(scg_cuentas.sc_cuenta)=trim(cxp_rd_scg.sc_cuenta)".
				" GROUP BY cxp_rd_scg.sc_cuenta,cxp_rd_scg.debhab".
				" ORDER BY cxp_rd_scg.debhab";
//		echo $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_detalle_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_scg->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_detalle_scg
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_verificar_detalle_spg($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_verificar_detalle_spg
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT cxp_dt_solicitudes.numsol,cxp_rd_spg.codestpro,cxp_rd_spg.spg_cuenta, sum(cxp_rd_spg.monto) AS monto ,max(spg_cuentas.denominacion) as denominacion".
				"  FROM cxp_rd_spg,cxp_dt_solicitudes,spg_cuentas".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_rd_spg.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd_spg.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd_spg.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd_spg.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd_spg.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				"   AND spg_cuentas.codemp=cxp_rd_spg.codemp".
				"   AND spg_cuentas.spg_cuenta=cxp_rd_spg.spg_cuenta".
				"   AND SUBSTR(cxp_rd_spg.codestpro,1,25)=spg_cuentas.codestpro1 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,26,25)=spg_cuentas.codestpro2 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,51,25)=spg_cuentas.codestpro3 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,76,25)=spg_cuentas.codestpro4 ".
				"   AND SUBSTR(cxp_rd_spg.codestpro,101,25)=spg_cuentas.codestpro5 ".
				" GROUP BY cxp_dt_solicitudes.numsol,cxp_rd_spg.codestpro,cxp_rd_spg.spg_cuenta";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_verificar_detalle_spg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_verificar_detalle_spg
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalleconrecdoc_scg($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalle_scg
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_scg = new class_datastore();
		$ls_sql="SELECT cxp_solicitudes_scg.sc_cuenta,cxp_solicitudes_scg.monto,cxp_solicitudes_scg.debhab,scg_cuentas.denominacion".
				" FROM cxp_solicitudes_scg,cxp_dt_solicitudes,scg_cuentas".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_solicitudes_scg.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_solicitudes_scg.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes_scg.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_solicitudes_scg.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_solicitudes_scg.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				"   AND scg_cuentas.codemp=cxp_solicitudes_scg.codemp".
				"   AND trim(scg_cuentas.sc_cuenta)=trim(cxp_solicitudes_scg.sc_cuenta)".
				" ORDER BY cxp_solicitudes_scg.debhab";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_detalleconrecdoc_scg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_scg->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_detalleconrecdoc_scg
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepcion($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_recepcion
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de solicitud
		//                 as_codpro     // Codigo del Proveedor
		//                 as_cedben     // Cedula del Beneficiario
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de la una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_rd.*,cxp_documento.dentipdoc, ".
				"       (CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre, ".
				"       (CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.rifpro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.ced_bene ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS rif, ".
		        "		(SELECT nomusu FROM sss_usuarios".
				"		 WHERE trim(sss_usuarios.codusu)=trim(cxp_rd.codusureg))AS nomusureg,".
				"		(SELECT apeusu FROM sss_usuarios".
				"		 WHERE trim(sss_usuarios.codusu)=trim(cxp_rd.codusureg))AS apeusureg".
				"  FROM cxp_rd,cxp_documento ".$ls_filtrofrom.
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.numrecdoc='".$as_numrecdoc."' ".
				"   AND cxp_rd.codtipdoc='".$as_codtipdoc."' ".
				"   AND cxp_rd.cod_pro='".$as_codpro."' ".
				"   AND cxp_rd.ced_bene='".$as_cedben."' ".$ls_filtroest.
				"   AND cxp_rd.codtipdoc=cxp_documento.codtipdoc";
		$rs_data=$this->io_sql->select($ls_sql); 
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalle_recepcionspg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalle_recepcionspg
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de solicitud
		//                 as_codpro     // Codigo del Proveedor
		//                 as_cedben     // Cedula del Beneficiario
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 22/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_spg = new class_datastore();
		$ls_cadena1 = $this->io_conexion->Concat('codestpro1','codestpro2','codestpro3','codestpro4','codestpro5');
		$ls_cadsub = "(select codemp,spg_cuenta,denominacion,estcla,".$ls_cadena1." as codestpro
						FROM spg_cuentas) as curP ";

		$ls_cadrel = "	AND	cxp_rd_spg.codemp=curP.codemp
						AND cxp_rd_spg.codestpro=curP.codestpro
						AND cxp_rd_spg.estcla=curP.estcla
						AND cxp_rd_spg.spg_cuenta=curP.spg_cuenta ";

		$ls_sql="SELECT cxp_rd_spg.codestpro,cxp_rd_spg.numrecdoc,cxp_rd_spg.spg_cuenta,cxp_rd_spg.monto,cxp_rd_spg.numdoccom,curP.denominacion".
				" FROM cxp_rd_spg,".$ls_cadsub." ".
				" WHERE cxp_rd_spg.codemp='".$this->ls_codemp."' ".$ls_cadrel.
				"   AND cxp_rd_spg.numrecdoc='".$as_numrecdoc."' ".
				"   AND cxp_rd_spg.codtipdoc='".$as_codtipdoc."' ".
				"   AND cxp_rd_spg.cod_pro='".$as_codpro."' ".
				"   AND cxp_rd_spg.ced_bene='".$as_cedben."' ";
		$rs_data=$this->io_sql->select($ls_sql);// print $ls_sql;
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_detalle_recepcionspg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_spg->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_detalle_recepcionspg
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalle_recepcionscg($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalle_recepcionscg
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de solicitud
		//                 as_codpro     // Codigo del Proveedor
		//                 as_cedben     // Cedula del Beneficiario
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información presupuestaria asociada a una Recepcion de documentos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 22/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_scg = new class_datastore();
		$ls_cadrel=" AND cxp_rd_scg.codemp = scg_cuentas.codemp
					 AND cxp_rd_scg.sc_cuenta = scg_cuentas.sc_cuenta ";

		$ls_sql="SELECT cxp_rd_scg.sc_cuenta,cxp_rd_scg.numrecdoc,cxp_rd_scg.debhab,cxp_rd_scg.monto,cxp_rd_scg.numdoccom,scg_cuentas.denominacion ".
				" FROM cxp_rd_scg,scg_cuentas ".
				" WHERE cxp_rd_scg.codemp='".$this->ls_codemp."' ".$ls_cadrel.
				"   AND cxp_rd_scg.numrecdoc='".$as_numrecdoc."' ".
				"   AND cxp_rd_scg.codtipdoc='".$as_codtipdoc."' ".
				"   AND cxp_rd_scg.cod_pro='".$as_codpro."' ".
				"   AND cxp_rd_scg.ced_bene='".$as_cedben."' ".
				" ORDER BY cxp_rd_scg.debhab";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_detalle_recepcionscg ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_scg->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_detalle_recepcionscg
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepciones($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas,$as_codtipdoc,
								   $ai_registrada,$ai_anulada,$ai_procesada,$as_orden,$as_numexprel)
	{

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_recepcion
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//                 as_codtipdoc     // Codigo de Tipo de Documento
		//                 as_registrada    // Estatus de la Recepcion Registrada
		//                 ai_anulada       // Estatus de la Recepcion Anulada
		//                 ai_procesada     // Estatus de la Recepcion Procesada
		//                 ai_orden         // Orden de los Datos en el Reporte Numero/Fecha
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_rd.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}

		if(!empty($as_codtipdoc) &&($as_codtipdoc!="-"))
		{
			$as_codtipdoc=substr($as_codtipdoc,0,5);
			$ls_criterio= $ls_criterio."   AND cxp_rd.codtipdoc='".$as_codtipdoc."'";
		}

		if(($ai_registrada==1)||($ai_procesada==1)||($ai_anulada==1))
		{
			$lb_anterior=false;
			if($ai_registrada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_rd.estprodoc='R'";
					$lb_anterior=true;
				}
			}
			if($ai_procesada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_rd.estprodoc='C'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_rd.estprodoc='C'";
				}
			}
			if($ai_anulada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_rd.estprodoc='A'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_rd.estprodoc='A'";
				}
			}
			if($lb_anterior)
			{
				$ls_criterio=$ls_criterio.")";
			}
		}
		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="cxp_rd.numrecdoc ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="cxp_rd.fecregdoc ";
				break;

		}
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		if(!empty($as_numexprel))
		{
			$ls_criterio= $ls_criterio."   AND cxp_rd.numexprel='".$as_numexprel."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_rd.numrecdoc,cxp_rd.fecemidoc,cxp_rd.fecregdoc,MAX(cxp_rd.montotdoc) AS montotdoc,".
				"       MAX(cxp_rd.mondeddoc) AS mondeddoc,MAX(cxp_rd.moncardoc) AS moncardoc,".
				"		MAX(cxp_documento.dentipdoc) AS dentipdoc,MAX(cxp_rd.numexprel) AS numexprel,".
				"		(SELECT MAX(numdoccom) FROM cxp_rd_spg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_spg.codemp AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) AS numdoccom,".
				"       (SELECT MAX(procede_doc) FROM cxp_rd_spg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_spg.codemp AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) AS procede_doc,".
				"		(SELECT MAX(numdoccom) FROM cxp_rd_scg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_scg.codemp AND cxp_rd.numrecdoc=cxp_rd_scg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_scg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_scg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_scg.ced_bene) AS numdoccont,".
				"       (SELECT MAX(procede_doc) FROM cxp_rd_scg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_scg.codemp AND cxp_rd.numrecdoc=cxp_rd_scg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_scg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_scg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_scg.ced_bene) AS procede_cont,".
				"       (CASE MAX(tipproben) WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_rd,cxp_documento ".$ls_filtrofrom.
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				"   AND cxp_rd.codtipdoc=cxp_documento.codtipdoc".
				" GROUP BY cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.fecemidoc,cxp_rd.fecregdoc".
		        " ORDER BY ".$ls_orden;
		//echo $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS=new class_datastore();
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_cxp_f2($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas,$as_orden)
	{

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_cxp_f2
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//                 ai_orden         // Orden de los Datos en el Reporte Numero/Fecha
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_rd.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="cxp_rd.numrecdoc ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="cxp_rd.fecregdoc ";
				break;

		}
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_rd.numrecdoc,cxp_rd.fecemidoc,cxp_rd.fecregdoc,MAX(cxp_rd.montotdoc) AS montotdoc,MAX(cxp_rd.codusureg) AS codusureg,cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.codtipdoc,".
				"       MAX(cxp_rd.mondeddoc) AS mondeddoc,MAX(cxp_rd.moncardoc) AS moncardoc,".
				"		MAX(cxp_documento.dentipdoc) AS dentipdoc,MAX(cxp_rd.numexprel) AS numexprel,".
				"		(SELECT MAX(numsol) FROM cxp_dt_solicitudes".
				" 		  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene) AS numsol,".
				"		(SELECT MAX(estprosol) FROM cxp_dt_solicitudes,cxp_solicitudes".
				" 		  WHERE cxp_rd.codemp=cxp_dt_solicitudes.codemp AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene AND cxp_rd.codemp=cxp_solicitudes.codemp ".
				"           AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"           AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol) AS estprosol,".
				"		(SELECT MAX(numdoccom) FROM cxp_rd_spg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_spg.codemp AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) AS numdoccom,".
				"       (SELECT MAX(procede_doc) FROM cxp_rd_spg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_spg.codemp AND cxp_rd.numrecdoc=cxp_rd_spg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_spg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_spg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_spg.ced_bene) AS procede_doc,".
				"		(SELECT MAX(numdoccom) FROM cxp_rd_scg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_scg.codemp AND cxp_rd.numrecdoc=cxp_rd_scg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_scg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_scg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_scg.ced_bene) AS numdoccont,".
				"       (SELECT MAX(procede_doc) FROM cxp_rd_scg".
				" 		  WHERE cxp_rd.codemp=cxp_rd_scg.codemp AND cxp_rd.numrecdoc=cxp_rd_scg.numrecdoc".
				"           AND cxp_rd.codtipdoc=cxp_rd_scg.codtipdoc AND cxp_rd.cod_pro=cxp_rd_scg.cod_pro".
				"           AND cxp_rd.ced_bene=cxp_rd_scg.ced_bene) AS procede_cont,".
				"       (CASE MAX(tipproben) WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_rd,cxp_documento ".$ls_filtrofrom.
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.estprodoc <> 'A' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				"   AND cxp_rd.codtipdoc=cxp_documento.codtipdoc".
				" GROUP BY cxp_rd.codemp,cxp_rd.numrecdoc,cxp_rd.codtipdoc,cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.fecemidoc,cxp_rd.fecregdoc".
		        " ORDER BY ".$ls_orden;
		//echo $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_cxp_f2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS=new class_datastore();
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retenciones_factura($as_codpro,$as_cedbene,$as_numrecdoc,$as_codtipdoc,$as_tiporet)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_retenciones_factura
		//         Access: public
		//      Argumento: $as_codigo // codigo del proveedor ó beneficario desde
		//				   $as_tipo // Si buscamos proveedores, beneficiarios ó ambos
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene la retencion de una factura
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_monret=0;
		switch($as_tiporet)
		{
			case'estretmil':
				$ls_retencion="sigesp_deducciones.".$as_tiporet." = '1'";
			break;
			default:
				$ls_retencion="sigesp_deducciones.".$as_tiporet." = 1";
			break;
		}
		
		$ls_sql="SELECT cxp_rd.fecemidoc, cxp_rd.montotdoc, cxp_rd_deducciones.monobjret, ".
				"		cxp_rd_deducciones.porded, cxp_rd_deducciones.monret, cxp_rd_deducciones.cod_pro ".
				"  FROM cxp_rd, cxp_rd_deducciones, sigesp_deducciones ".
				" WHERE  cxp_rd.codemp='".$this->ls_codemp."'".
				"   AND $ls_retencion".
				"   AND cxp_rd.numrecdoc='".$as_numrecdoc."'".
				"   AND cxp_rd.codtipdoc='".$as_codtipdoc."'".
				"   AND cxp_rd.cod_pro='".$as_codpro."'".
				"   AND cxp_rd.ced_bene='".$as_cedbene."'".
				"   AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"   AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
				"	AND cxp_rd.estprodoc <> 'A' ".
				" ORDER BY cxp_rd.fecemidoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
		//print $ls_sql."<br>";
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retenciones_factura ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_monret=$row["monret"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_monret;
	}// end function uf_arc_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudes($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas,$as_scgcta_desde,$as_scgcta_hasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudes
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_critcta_p="";
		$ls_critcta_b="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecreghas."'";
		}
		if ((!empty($as_scgcta_desde))&&(!empty($as_scgcta_hasta)))
		{
			if ($as_scgcta_desde!=$as_scgcta_hasta)
			{
				$ls_critcta_p=$ls_critcta_p. " AND rpc_proveedor.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."' ";
				$ls_critcta_b=$ls_critcta_b. " AND rpc_beneficiario.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."' ";
				$ls_criterio=$ls_criterio." AND ((rpc_beneficiario.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."') OR (rpc_proveedor.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."')) ";
			}
			else
			{
				$ls_critcta_p=$ls_critcta_p. " AND rpc_proveedor.sc_cuenta='".$as_scgcta_desde."' ";
				$ls_critcta_b=$ls_critcta_b. " AND rpc_beneficiario.sc_cuenta='".$as_scgcta_desde."' ";
				$ls_criterio=$ls_criterio." AND ((rpc_beneficiario.sc_cuenta='".$as_scgcta_desde."') OR (rpc_proveedor.sc_cuenta='".$as_scgcta_desde."')) ";
			}
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT MAX(cxp_solicitudes.tipproben) AS tipproben,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre, ".
				"		(CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.sc_cuenta ".
				"									FROM rpc_proveedor ".
				"									WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"									AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro ".
				"									".$ls_critcta_p." )".
				"						WHEN 'B' THEN (SELECT rpc_beneficiario.sc_cuenta ".
				"									FROM rpc_beneficiario ".
				"									WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"									AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene ".
				"                                   ".$ls_critcta_b." )". 
				"						ELSE '-------------------------' END ) AS sc_cuenta ".
				"  FROM cxp_solicitudes,cxp_historico_solicitud,rpc_beneficiario,rpc_proveedor ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"   AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"   AND cxp_solicitudes.cod_pro=rpc_proveedor.cod_pro ".
				"   AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene ".
				"   AND cxp_historico_solicitud.estprodoc='C'".
				"   AND cxp_solicitudes.estprosol<>'A'".
				"   ".$ls_criterio." ".$ls_filtroest.
				" GROUP BY cxp_solicitudes.codemp,cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.tipproben".
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesanteriores($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesanteriores
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_criterio3="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if($as_tipproben=="P")
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro='".$as_codpro."'";
		}
		else
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene='".$as_cedbene."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio2=$ls_criterio2. "  AND cxp_solicitudes.fecemisol<'".$ad_fecregdes."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio3=$ls_criterio3. "  AND cxp_solicitudes.fecemisol>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio3=$ls_criterio3. "  AND cxp_solicitudes.fecemisol<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc "; 
			$ls_filtrofrom = " , scb_dt_cmp_ret, cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT MAX(cxp_solicitudes.tipproben) AS tipproben,MAX(cxp_solicitudes.monsol) AS monsol,cxp_solicitudes.numsol, MAX(cxp_solicitudes.estprosol) AS estatus,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes,cxp_historico_solicitud ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"   AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"   AND cxp_historico_solicitud.estprodoc='C'".
				"   AND cxp_solicitudes.estprosol<>'A' ".$ls_filtroest.
				"   ".$ls_criterio." ".
				"   ".$ls_criterio2." ".
				"   AND cxp_solicitudes.numsol NOT IN ".
				"       (SELECT  cxp_solicitudes.numsol".
				"          FROM cxp_solicitudes,cxp_historico_solicitud ".
				"         WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"           AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"           AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"           AND cxp_historico_solicitud.estprodoc='C'".
				"           AND cxp_solicitudes.estprosol<>'A'".
				"           ".$ls_criterio."".
				"           ".$ls_criterio3.")".
				" GROUP BY cxp_solicitudes.codemp,cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.tipproben,cxp_solicitudes.numsol".
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";
		$this->rs_solanteriores=$this->io_sql->select($ls_sql);//print $ls_sql."<br>";
		if($this->rs_solanteriores===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesanteriores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_solanteriores->EOF)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_solicitudesanteriores
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_saldosprevios($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesanteriores
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_criterio3="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio2=$ls_criterio2. "  AND cxp_solicitudes.fecemisol<'".$ad_fecregdes."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio3=$ls_criterio3. "  AND cxp_solicitudes.fecemisol>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio3=$ls_criterio3. "  AND cxp_solicitudes.fecemisol<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_cadena2 = $this->io_conexion->Concat('cxp_solicitudes.cod_pro',"' '",'cxp_solicitudes.ced_bene');
		$ls_sql="SELECT MAX(cxp_solicitudes.tipproben) AS tipproben, cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes,cxp_historico_solicitud ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"   AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"   AND cxp_historico_solicitud.estprodoc='C'".
				"   AND cxp_solicitudes.estprosol<>'A' ".$ls_filtroest.
				"   ".$ls_criterio." ".
				"   ".$ls_criterio2." ".
				"   AND ".$ls_cadena2." NOT IN ".
				"       (SELECT  ".$ls_cadena2."".
				"          FROM cxp_solicitudes,cxp_historico_solicitud ".
				"         WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"           AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"           AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"           AND cxp_historico_solicitud.estprodoc='C'".
				"           AND cxp_solicitudes.estprosol<>'A'".
				"           ".$ls_criterio."".
				"           ".$ls_criterio3.")".
				" GROUP BY cxp_solicitudes.codemp,cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.tipproben".
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";//print $ls_sql;
		$this->rs_provanteriores=$this->io_sql->select($ls_sql);
		if($this->rs_provanteriores===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesanteriores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_provanteriores->EOF)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_solicitudesanteriores
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_proveedores($as_tipproben,$as_codproben)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_proveedores
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los proveedores.
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 09/10/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_nombre="";
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		if($as_tipproben=="P")
		{
			$ls_sql="SELECT rpc_proveedor.nompro AS nombre".
					"  FROM rpc_proveedor ".
					" WHERE rpc_proveedor.codemp='".$this->ls_codemp."' ".
					"   AND rpc_proveedor.cod_pro='".$as_codproben."' ";
		}
		else
		{
			$ls_sql="SELECT ".$ls_cadena." AS nombre ".
					"  FROM rpc_beneficiario ".
					" WHERE rpc_beneficiario.codemp='".$this->ls_codemp."' ".
					"   AND rpc_beneficiario.ced_bene='".$as_codproben."'";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_proveedores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nombre=$row["nombre"];
				//$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_nombre;
	}// end function uf_select_proveedores
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudes_previas($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudes_previas
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$this->ds_solprevias= new class_datastore();
		$ls_cadena="";
		if($ad_fecregdes!="")
		{
			$ls_cadena="AND cxp_historico_solicitud.fecha < '".$ad_fecregdes."'";
		}
		$as_tipproben="%".$as_tipproben."%";
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql=" SELECT  cxp_solicitudes.numsol, cxp_solicitudes.monsol, cxp_historico_solicitud.estprodoc AS estatus, cxp_historico_solicitud.fecha".
				"   FROM  cxp_solicitudes, cxp_historico_solicitud ".$ls_filtrofrom.
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND  cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"    AND cxp_solicitudes.tipproben LIKE'".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."' ".$ls_filtroest.
				"    AND (cxp_historico_solicitud.estprodoc='C' OR cxp_historico_solicitud.estprodoc='A')".
				$ls_cadena.
				"  ORDER  BY cxp_solicitudes.ced_bene, cxp_solicitudes.cod_pro";//print $ls_sql."<br>";
		$this->rs_solprevias=$this->io_sql->select($ls_sql);
		if ($this->rs_solprevias===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudes_previas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_solicitudes_previas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_pagosprevios($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas,$ad_pagosprevios,$ad_retencionesprevios)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_pagosprevios
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$ls_cadena="";
		$ad_pagosprevios=0;
		if($ad_fecregdes!="")
		{
			$ls_cadena="AND cxp_historico_solicitud.fecha < '".$ad_fecregdes."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_sol_banco.numsol, CASE WHEN scb_movbco.estmov ='A' THEN  -cxp_sol_banco.monto ELSE cxp_sol_banco.monto END  as pagos,scb_movbco.estmov".
				"  FROM cxp_sol_banco ".
				" INNER JOIN cxp_solicitudes ".
				"    ON cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   AND cxp_sol_banco.codemp = cxp_solicitudes.codemp ".
				"   AND cxp_sol_banco.numsol = cxp_solicitudes.numsol , scb_movbco ".$ls_filtrofrom.
				" WHERE scb_movbco.codemp='".$this->ls_codemp."'".
				"   AND (scb_movbco.estmov='C' OR scb_movbco.estmov='O'OR scb_movbco.estmov='A')".
				"   AND scb_movbco.fecmov<'".$ad_fecregdes."'".
				"   AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"   AND cxp_sol_banco.codban=scb_movbco.codban".
				"   AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"   AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"   AND cxp_sol_banco.codope=scb_movbco.codope ".$ls_filtroest;

/*		$ls_sql="SELECT cxp_sol_banco.numsol, -cxp_sol_banco.monto as pagos,scb_movbco.estmov".
				"  FROM cxp_sol_banco ".
				" INNER JOIN cxp_solicitudes ".
				"    ON cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   AND cxp_sol_banco.codemp = cxp_solicitudes.codemp ".
				"   AND cxp_sol_banco.numsol = cxp_solicitudes.numsol , scb_movbco ".$ls_filtrofrom.
				" WHERE cxp_sol_banco.codemp='".$this->ls_codemp."'".
				"   AND scb_movbco.estmov='A'".
				"   AND scb_movbco.fecmov<'".$ad_fecregdes."'".
				"   AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"   AND cxp_sol_banco.codban=scb_movbco.codban".
				"   AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"   AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"   AND cxp_sol_banco.codope=scb_movbco.codope ".$ls_filtroest.
				" UNION ".
				"SELECT cxp_sol_banco.numsol, cxp_sol_banco.monto as pagos,scb_movbco.estmov".
				"  FROM cxp_sol_banco ".
				" INNER JOIN cxp_solicitudes ".
				"    ON cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   AND cxp_sol_banco.codemp = cxp_solicitudes.codemp ".
				"   AND cxp_sol_banco.numsol = cxp_solicitudes.numsol , scb_movbco ".$ls_filtrofrom.
				" WHERE scb_movbco.codemp='".$this->ls_codemp."'".
				"   AND (scb_movbco.estmov='C' OR scb_movbco.estmov='O')".
				"   AND scb_movbco.fecmov<'".$ad_fecregdes."'".
				"   AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"   AND cxp_sol_banco.codban=scb_movbco.codban".
				"   AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"   AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"   AND cxp_sol_banco.codope=scb_movbco.codope ".$ls_filtroest;*/

/*		$ls_sql=" SELECT SUM(CASE WHEN cxp_solicitudes.monsol is null THEN 0 ELSE cxp_solicitudes.monsol END) AS pagos".
				"   FROM cxp_solicitudes ".
				"  INNER JOIN cxp_historico_solicitud ".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_historico_solicitud.estprodoc='P'".
				$ls_cadena.
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol";
/*				"  INNER JOIN cxp_sol_banco".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol";*/
/*				"   FROM cxp_solicitudes, cxp_sol_banco".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"    AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				$ls_cadena.
				"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
			"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol";*///print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosprevios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ad_pagosprevios=$ad_pagosprevios + $rs_data->fields["pagos"];
				$rs_data->MoveNext();
			}
			$this->io_sql->free_result($rs_data);
		}
			$ls_sql=" SELECT SUM(cxp_rd_deducciones.monret) as retenciones".
					"   FROM  cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco,cxp_rd_deducciones, sigesp_deducciones,cxp_dt_solicitudes".
					"  WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
					"    AND cxp_historico_solicitud.estprodoc='P'".
					"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
					"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
					"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
					$ls_cadena.
					"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
					"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
					"    AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
					"    AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
					"    AND cxp_rd_deducciones.codemp =cxp_dt_solicitudes.codemp".
					"    AND cxp_rd_deducciones.numrecdoc =cxp_dt_solicitudes.numrecdoc".
					"    AND cxp_rd_deducciones.codtipdoc =cxp_dt_solicitudes.codtipdoc".
					"    AND cxp_rd_deducciones.cod_pro =cxp_dt_solicitudes.cod_pro".
					"    AND cxp_rd_deducciones.ced_bene =cxp_dt_solicitudes.ced_bene".
					"    AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
					"	 AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
					"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
					"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
					"    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.islr=1)";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosprevios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_retencionesprevios=$rs_data->fields["retenciones"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["ad_pagosprevios"]=$ad_pagosprevios;
		$arrResultado["ad_retencionesprevios"]=$ad_retencionesprevios;
		return $arrResultado;
	}// end function uf_select_pagosprevios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_pagosanteriores($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas,$ad_pagosprevios)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_pagosanteriores
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$ls_cadena="";
		if($ad_fecregdes!="")
		{
			$ls_cadena="AND cxp_historico_solicitud.fecha < '".$ad_fecregdes."'";
		}
		$ls_monto=0;
		$lb_valido= true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_criterio3="";
		$ad_pagosprevios=0;
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
//			$ls_criterio2=$ls_criterio2. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
			$ls_criterio3=$ls_criterio3. "  AND scb_movbco.fecmov<'".$ad_fecregdes."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_sol_banco.numsol, -cxp_sol_banco.monto as pagos,scb_movbco.estmov".
				"  FROM cxp_sol_banco ".
				" INNER JOIN cxp_solicitudes ".
				"    ON cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   AND cxp_sol_banco.codemp = cxp_solicitudes.codemp ".
				"   AND cxp_sol_banco.numsol = cxp_solicitudes.numsol , scb_movbco ".$ls_filtrofrom.
				" WHERE cxp_sol_banco.codemp='".$this->ls_codemp."'".
				"   AND scb_movbco.estmov='A'".
				"   AND scb_movbco.fecmov<'".$ad_fecregdes."'".
				"   AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"   AND cxp_sol_banco.codban=scb_movbco.codban".
				"   AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"   AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"   AND cxp_sol_banco.codope=scb_movbco.codope ".$ls_filtroest.
				" UNION ".
				"SELECT cxp_sol_banco.numsol, cxp_sol_banco.monto as pagos,scb_movbco.estmov".
				"  FROM cxp_sol_banco ".
				" INNER JOIN cxp_solicitudes ".
				"    ON cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   AND cxp_sol_banco.codemp = cxp_solicitudes.codemp ".
				"   AND cxp_sol_banco.numsol = cxp_solicitudes.numsol , scb_movbco ".$ls_filtrofrom.
				" WHERE scb_movbco.codemp='".$this->ls_codemp."'".
				"   AND (scb_movbco.estmov='C' OR scb_movbco.estmov='O')".
				"   AND scb_movbco.fecmov<'".$ad_fecregdes."'".
				"   AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"   AND cxp_sol_banco.codban=scb_movbco.codban".
				"   AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"   AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"   AND cxp_sol_banco.codope=scb_movbco.codope ".$ls_filtroest;
/*		$ls_sql=" SELECT SUM(cxp_sol_banco.monto) AS pagos ".
				"   FROM cxp_solicitudes ".
				"  INNER JOIN cxp_historico_solicitud ".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_solicitudes.estprosol<>'A'".
				"    AND cxp_historico_solicitud.estprodoc='S'".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"  INNER JOIN (cxp_sol_banco".
				"    INNER JOIN scb_movbco".
				"    ON cxp_sol_banco.codemp=scb_movbco.codemp".
				"    AND cxp_sol_banco.codban=scb_movbco.codban".
				"    AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"    AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"    AND cxp_sol_banco.codope=scb_movbco.codope".
				"    AND cxp_sol_banco.estmov=scb_movbco.estmov)".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
 				"	 AND scb_movbco.estmov='C'".
 				" ".$ls_criterio3." ".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol";*/
//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosanteriores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ad_pagosprevios=$ad_pagosprevios + $rs_data->fields["pagos"];
				$rs_data->MoveNext();
			}
/*			if(!$rs_data->EOF)
			{
				$ad_pagosprevios=$rs_data->fields["pagos"];
			}
*/			$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["ad_pagosprevios"]=$ad_pagosprevios;
		return $arrResultado;
	}// end function uf_select_pagosanteriores
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionpagosprevios($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas,$ad_totpagosprevios)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionpagosprevios
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
		$ls_sql=" SELECT COALESCE(SUM(cxp_solicitudes.monsol),0) AS pagos                                                                                    ".
				"   FROM cxp_solicitudes ".
				"  INNER JOIN cxp_historico_solicitud ".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_historico_solicitud.fecha <'".$ad_fecreghas."'".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"  INNER JOIN cxp_sol_banco".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol";
				/*
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"    AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
				"    AND cxp_historico_solicitud.fecha <'".$ad_fecreghas."'";*/
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagosprevios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_totpagosprevios=$rs_data->fields["pagos"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["ad_totpagosprevios"]=$ad_totpagosprevios;
		return $arrResultado;
	}// end function uf_select_informacionpagosprevios
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_detalles_pagosprevios($as_numsol,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_detalles_pagosprevios
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha<='".$ad_fecreghas."'";
		}
		$this->ds_detpagosprevios = new class_datastore();
		$ls_sql=" SELECT cxp_historico_solicitud.fecha, cxp_sol_banco.codban, cxp_sol_banco.ctaban,".
				"        cxp_sol_banco.numdoc, cxp_sol_banco.monto,scb_banco.nomban".
				"   FROM cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco,scb_banco ".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_sol_banco.codemp=scb_banco.codemp".
				"    AND cxp_sol_banco.codban=scb_banco.codban".
				"	 AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"	 AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
				"	 AND cxp_solicitudes.numsol='".$as_numsol."'".
				" ".$ls_criterio." ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_detalles_pagosprevios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detpagosprevios->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_detalles_pagosprevios
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesactuales($as_tipproben,$as_cedbene,$as_codpro,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesactuales
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$ls_criterio="";
		$this->ds_solactuales = new class_datastore();
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha<='".$ad_fecreghas."'";
		}
		$ls_sql=" SELECT cxp_solicitudes.numsol, cxp_solicitudes.monsol,cxp_solicitudes.consol, cxp_historico_solicitud.estprodoc, cxp_historico_solicitud.fecha ".
				"   FROM cxp_solicitudes, cxp_historico_solicitud".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"	 AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND (cxp_historico_solicitud.estprodoc='C' OR cxp_historico_solicitud.estprodoc='A') ".
				" ".$ls_criterio." ".
				" ORDER BY cxp_solicitudes.ced_bene, cxp_solicitudes.cod_pro ";
			//	print $ls_sql."<br><br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesactuales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_solactuales->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_solicitudesactuales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_pagosolicitudes($as_numsol,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_pagosolicitudes
		//         Access: public
		//	    Arguments: as_numsol    // Numero de Solicitud de Pago
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$ls_criterio="";
		$this->ds_detpagsolact = new class_datastore();
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha<='".$ad_fecreghas."'";
		}
		$ls_sql=" SELECT cxp_historico_solicitud.fecha, cxp_sol_banco.codban, cxp_sol_banco.ctaban, cxp_sol_banco.numdoc,".
				"	 	 cxp_sol_banco.monto,scb_banco.nomban,cxp_sol_banco.estmov,cxp_sol_banco.codope ".
				"   FROM cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco,scb_banco ".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_sol_banco.codemp=scb_banco.codemp".
				"    AND cxp_sol_banco.codban=scb_banco.codban".
				"	 AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"	 AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
				"	 AND cxp_solicitudes.numsol='".$as_numsol."'".
				" ".$ls_criterio." ";
		//		print $ls_sql."<br><br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosolicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detpagsolact->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_pagosolicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_otros_creditos($as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_otros_creditos
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_orden     // Parametro para ordenar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Funcion que busca el listado de otros creditos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codcar AS codigo, dencar AS denominacion, codestpro, spg_cuenta, porcar, formula".
				"  FROM sigesp_cargos".
				" WHERE codemp='".$this->ls_codemp."'".
				" ORDER BY ".$as_orden."";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_otros_creditos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_otros_creditos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_deducciones($as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_deducciones
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_orden     // Parametro para ordenar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Funcion que busca el listado de otros creditos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codded AS codigo, dended AS denominacion, sc_cuenta, porded, monded, formula".
				"  FROM sigesp_deducciones".
				" WHERE codemp='".$this->ls_codemp."'".
				" ORDER BY ".$as_orden."";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_deducciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_deducciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_documentos($as_orden)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_documentos
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_orden     // Parametro para ordenar el reporte
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Funcion que busca el listado de otros creditos
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codtipdoc AS codigo, dentipdoc AS denominacion, estcon, estpre".
				"  FROM cxp_documento".
				" ORDER BY ".$as_orden."";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_documentos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_documentos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesf1($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecemides,$ad_fecemihas,$ai_emitida,
									 $ai_contabilizada,$ai_anulada,$ai_propago,$ai_pagada)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_recepcion
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecemides     // Fecha de Emision Desde
		//                 ad_fecemihas     // Fecha de Emision Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$this->io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$this->io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecemihas."'";
		}
		if(($ai_emitida==1)||($ai_contabilizada==1)||($ai_anulada==1)||($ai_propago==1)||($ai_pagada==1))
		{
			$lb_anterior=false;
			if($ai_emitida==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_solicitudes.estprosol='E'";
					$lb_anterior=true;
				}
			}
			if($ai_contabilizada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_solicitudes.estprosol='C'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_solicitudes.estprosol='C'";
				}
			}
			if($ai_anulada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_solicitudes.estprosol='A'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_solicitudes.estprosol='A'";
				}
			}
			if($ai_propago==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_solicitudes.estprosol='S'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_solicitudes.estprosol='S'";
				}
			}
			if($ai_pagada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_solicitudes.estprosol='P'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_solicitudes.estprosol='P'";
				}
			}
			if($lb_anterior)
			{
				$ls_criterio=$ls_criterio.")";
			}
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT DISTINCT cxp_solicitudes.tipproben,cxp_solicitudes.numsol,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"        cxp_solicitudes.fecemisol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,cxp_solicitudes.consol,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				" ORDER BY cxp_solicitudes.numsol";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesf1 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_solicitudesf1
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudf2($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudf2
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud de orden de pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de una solicitud de pago en especifico
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.nombene',"' '",'rpc_beneficiario.apebene');
		$ls_sql="SELECT DISTINCT cxp_solicitudes.tipproben,cxp_solicitudes.numsol,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"        cxp_solicitudes.fecemisol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,cxp_solicitudes.consol,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_solicitudes.numsol='".$as_numsol."' ".$ls_filtroest.
				" ORDER BY cxp_solicitudes.numsol";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudf2 ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_solicitudf2
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesislr_cxp($as_numsol,$datastore=true)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesislr_cxp
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud de orden de pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las retenciones de una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $ls_gestor = $_SESSION["ls_gestor"];
	   $lb_valido=true;
	   $ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
	   //FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
	   $ls_sql="SELECT cxp_rd.numrecdoc AS numdoc, cxp_rd.numref, cxp_rd.fecemidoc, cxp_rd.fecregdoc, cxp_rd.tipproben, ".
		        "       rpc_proveedor.nitpro AS nit, ".
	   		    "	   rpc_proveedor.nompro AS proveedor, rpc_proveedor.telpro,rpc_proveedor.email as proemail, rpc_proveedor.dirpro, rpc_proveedor.rifpro, ".
			   $ls_cadena." AS beneficiario, rpc_beneficiario.dirbene, rpc_beneficiario.rifben, rpc_beneficiario.telbene,rpc_beneficiario.email as emailben, ".
			   "	   rpc_proveedor.cod_pro,rpc_proveedor.codmun as codmunpro,rpc_proveedor.codest AS codestpro, rpc_beneficiario.ced_bene,cxp_solicitudes.numsol,cxp_solicitudes.fecemisol,".
			   "	   rpc_beneficiario.codmun AS codmunben,rpc_beneficiario.codest AS codestben,cxp_solicitudes.consol, cxp_rd.montotdoc, cxp_rd_deducciones.monret AS retenido, ".
			   "	   (CASE WHEN cxp_rd_deducciones.monobjret is null THEN cxp_solicitudes.monsol ELSE cxp_rd_deducciones.monobjret END) AS monobjret, ".
			   "	   sigesp_deducciones.porded AS porcentaje,sigesp_deducciones.dended AS dended,".
               "      sigesp_deducciones.monded, cxp_rd.mondeddoc,cxp_rd.moncardoc, rpc_proveedor.tipconpro,rpc_beneficiario.tipconben,".
               "	   (SELECT MAX(cxp_sol_banco.numdoc)".
			   "		  FROM cxp_sol_banco".
			   "		 WHERE cxp_sol_banco.estmov<>'A' ".
			   "           AND cxp_sol_banco.estmov<>'O' ".
			   "           AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
			   "           AND cxp_solicitudes.numsol=cxp_sol_banco.numsol) AS cheque, ".
               "	   (SELECT MAX(scb_banco.nomban)".
			   "		  FROM cxp_sol_banco,scb_banco".
			   "		 WHERE cxp_sol_banco.numsol=cxp_solicitudes.numsol ".
			   "           AND cxp_sol_banco.codban=scb_banco.codban) AS banco, ".
			   "       cxp_dt_cmp_islr.numcmpislr,".
			   "	   (SELECT MAX(rpc_tipo_organizacion.dentipoorg)".
			   "		  FROM rpc_tipo_organizacion".
			   "		 WHERE rpc_tipo_organizacion.codtipoorg=rpc_proveedor.codtipoorg) AS tipper, ".
			   "	   (SELECT MAX(sigesp_conceptoretencion.desact)".
			   "		  FROM sigesp_conceptoretencion".
			   "		 WHERE sigesp_conceptoretencion.codemp=sigesp_deducciones.codemp ".
			   "		   AND sigesp_conceptoretencion.codconret=sigesp_deducciones.codconret) AS desact, ".
	           "    (SELECT MAX(scb_movbco.fecmov) from scb_movbco,cxp_sol_banco where cxp_sol_banco.numsol=cxp_solicitudes.numsol ".
               " and cxp_sol_banco.estmov<>'A' AND cxp_sol_banco.estmov<>'O' and cxp_sol_banco.numdoc=scb_movbco.numdoc ".
			   " and cxp_sol_banco.codban=scb_movbco.codban and cxp_sol_banco.ctaban=scb_movbco.ctaban and cxp_sol_banco.codope=scb_movbco.codope ".
               " and cxp_sol_banco.estmov=scb_movbco.estmov) as fecche ".
			   "	FROM cxp_solicitudes
			  	    join cxp_dt_solicitudes on  ( cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
									              AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
									              AND cxp_solicitudes.cod_pro=cxp_dt_solicitudes.cod_pro
									              AND cxp_solicitudes.ced_bene=cxp_dt_solicitudes.ced_bene)
					join cxp_rd on ( cxp_dt_solicitudes.codemp=cxp_rd.codemp
											        AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
													AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
													AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
													AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc )
					join cxp_rd_deducciones on (cxp_rd.codemp=cxp_rd_deducciones.codemp
												AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
												AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
												AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc
												AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc)
					join sigesp_deducciones on (sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
												AND sigesp_deducciones.codded=cxp_rd_deducciones.codded)
					join rpc_beneficiario on (rpc_beneficiario.codemp=cxp_solicitudes.codemp
											  AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene)
					join rpc_proveedor on (rpc_proveedor.codemp=cxp_solicitudes.codemp
					                        AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro)
					left join cxp_dt_cmp_islr on (cxp_dt_cmp_islr.codemp=cxp_solicitudes.codemp
					                              and cxp_dt_cmp_islr.numsol=cxp_solicitudes.numsol) $ls_filtrofrom ".
			   " WHERE sigesp_deducciones.islr=1 ".
			   "   AND sigesp_deducciones.iva=0 ".
			   "   AND sigesp_deducciones.estretmun=0 ".
			   "   AND cxp_solicitudes.estprosol<>'A' ".
			   "   AND cxp_solicitudes.codemp='".$this->ls_codemp."' ".
			   "   AND cxp_solicitudes.numsol='".$as_numsol."' ".$ls_filtroest.
			   " ORDER BY cxp_solicitudes.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($datastore) {
			if($rs_data===false){
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_cxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else{
				if($row=$this->io_sql->fetch_row($rs_data)){
					$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				}
				else{
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
			return $lb_valido;
		}
		else {
			if($rs_data===false){
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_cxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			return $rs_data;
		}
	}// end function uf_retencionesislr_cxp
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesislr_int($as_numsol,$datastore=true)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesislr_int
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud de orden de pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las retenciones de una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $ls_gestor = $_SESSION["ls_gestor"];
	   $lb_valido=true;
	   $ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
	   //FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = cxp_cmp_islr.codemp ".
							" AND cxp_dt_solicitudes.numsol = cxp_cmp_islr.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
	   $ls_sql="SELECT cxp_dt_cmp_islr.numrecdoc AS numdoc, cxp_dt_cmp_islr.numref, cxp_dt_cmp_islr.fecpag AS fecemidoc, '' AS tipproben, rpc_proveedor.nitpro AS nit, ".
	   		   "	   rpc_proveedor.nompro AS proveedor, rpc_proveedor.telpro, rpc_proveedor.dirpro, rpc_proveedor.rifpro, ".
			   $ls_cadena." AS beneficiario, rpc_beneficiario.dirbene, rpc_beneficiario.rifben, rpc_beneficiario.telbene, ".
			   "	   rpc_proveedor.cod_pro, rpc_beneficiario.ced_bene,cxp_cmp_islr.numsol,".
			   "	   cxp_cmp_islr.consol, cxp_dt_cmp_islr.totimpret AS retenido,cxp_dt_cmp_islr.monobjret, ".
			   "	   cxp_dt_cmp_islr.porded AS porcentaje,'' AS dended,".
               "       '' AS monded, '' AS montotdoc, '' AS mondeddoc, cxp_cmp_islr.numcmpislr, 0 AS mondeddoc".
			   "  FROM cxp_cmp_islr, cxp_dt_cmp_islr, rpc_beneficiario, rpc_proveedor ".$ls_filtrofrom.
			   " WHERE cxp_cmp_islr.codemp='".$this->ls_codemp."' ".
			   "   AND cxp_cmp_islr.numsol='".$as_numsol."' ".
			   "   AND cxp_cmp_islr.codemp=cxp_dt_cmp_islr.codemp ".
			   "   AND cxp_cmp_islr.numsol=cxp_dt_cmp_islr.numsol ".
			   "   AND cxp_cmp_islr.numcmpislr=cxp_dt_cmp_islr.numcmpislr ".
			   "   AND rpc_beneficiario.codemp=cxp_cmp_islr.codemp ".
			   "   AND rpc_beneficiario.ced_bene=cxp_cmp_islr.ced_bene ".
			   "   AND rpc_proveedor.codemp=cxp_cmp_islr.codemp ".
			   "   AND rpc_proveedor.cod_pro=cxp_cmp_islr.cod_pro $ls_filtroest ";//print "ENTRE=>  ".$ls_sql."<br><br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($datastore){
			if($rs_data===false){
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_cxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else{
				if($row=$this->io_sql->fetch_row($rs_data)){
					$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				}
				else{
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
			return $lb_valido;
		}
		else {
			if($rs_data===false){
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_cxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			return $rs_data;
		}
		
	}// end function uf_retencionesislr_cxp
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesislr_scb($as_numdoc,$datastore=true)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesislr_scb
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud de orden de pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las retenciones de banco
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 07/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $ls_gestor = $_SESSION["ls_gestor"];
	   $lb_valido=true;
	   $ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
	   $ls_sql="SELECT scb_movbco.numdoc, scb_movbco.chevau AS numref, scb_movbco.fecmov AS fecemidoc, scb_movbco.fecmov AS fecregdoc, scb_movbco.tipo_destino AS tipproben, ".
	   		   "	   rpc_proveedor.nitpro AS nit, rpc_proveedor.nompro AS proveedor, rpc_proveedor.telpro, rpc_proveedor.dirpro, ".
			   "	   rpc_proveedor.rifpro, ".$ls_cadena." AS beneficiario, rpc_beneficiario.dirbene, rpc_beneficiario.rifben, ".
			   "	   rpc_beneficiario.telbene, rpc_proveedor.cod_pro, rpc_beneficiario.ced_bene, scb_movbco.conmov AS consol,".
               "       scb_movbco.monto AS montotdoc, scb_movbco.monret AS retenido, scb_movbco.monobjret AS monobjret,'' AS numsol,        ".
			   "      sigesp_deducciones.porded AS porcentaje,sigesp_deducciones.dended AS dended,scb_movbco.numdoc AS cheque,  ".
			   "      '' as numcmpislr, scb_movbco.fecmov as fecche, rpc_proveedor.tipconpro,rpc_beneficiario.tipconben, 0 AS mondeddoc,".
			   "	   (SELECT MAX(rpc_tipo_organizacion.dentipoorg)".
			   "		  FROM rpc_tipo_organizacion".
			   "		 WHERE rpc_tipo_organizacion.codtipoorg=rpc_proveedor.codtipoorg) AS tipper,".
               "	   (SELECT MAX(scb_banco.nomban)".
			   "		  FROM scb_banco".
			   "		 WHERE scb_movbco.codemp=scb_banco.codemp ".
			   "           AND scb_movbco.codban=scb_banco.codban) AS banco ".
			   "  FROM scb_movbco, scb_movbco_scg, sigesp_deducciones, rpc_proveedor, rpc_beneficiario ".
			   " WHERE scb_movbco.codemp = '".$this->ls_codemp."' ".
			   "   AND scb_movbco.numdoc = '".$as_numdoc."' ".
			   "   AND scb_movbco.estmov<>'O' ".
			   "   AND scb_movbco.estmov<>'A' ".
			   "   AND sigesp_deducciones.islr = 1 ".
			   "   AND sigesp_deducciones.iva = 0 ".
			   "   AND sigesp_deducciones.estretmun = 0 ".
			   "   AND scb_movbco.codemp = scb_movbco_scg.codemp ".
			   "   AND scb_movbco.codban = scb_movbco_scg.codban ".
			   "   AND scb_movbco.ctaban = scb_movbco_scg.ctaban ".
			   "   AND scb_movbco.numdoc = scb_movbco_scg.numdoc ".
			   "   AND scb_movbco.codope = scb_movbco_scg.codope ".
			   "   AND scb_movbco.estmov = scb_movbco_scg.estmov ".
			   "   AND scb_movbco_scg.codemp = sigesp_deducciones.codemp ".
			   "   AND scb_movbco_scg.codded = sigesp_deducciones.codded ".
			   "   AND scb_movbco.codemp = rpc_proveedor.codemp ".
			   "   AND scb_movbco.cod_pro = rpc_proveedor.cod_pro ".
			   "   AND scb_movbco.codemp = rpc_beneficiario.codemp ".
			   "   AND scb_movbco.ced_bene = rpc_beneficiario.ced_bene ".
			   "  ORDER BY scb_movbco.numdoc ";
			  // print $ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
	    if($datastore){
			if($rs_data===false){
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_scb ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
			}
			else{
				if($row=$this->io_sql->fetch_row($rs_data)){
					$this->DS->data=$this->io_sql->obtener_datos($rs_data);
				}
				else{
					$lb_valido=false;
				}
				$this->io_sql->free_result($rs_data);
			}
			return $lb_valido;
		}
		else {
			if($rs_data===false){
				$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_scb ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			}
			return $rs_data;
		}
		
	}// end function uf_retencionesislr_scb
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesgeneral($ad_fecdes,$ad_fechas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesgeneral
		//         Access: public
		//	    Arguments: ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_gestor = $_SESSION["ls_gestor"];
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		$lb_valido=true;
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_solicitudes.numsol, MAX(rpc_proveedor.nitpro) AS nitpro, MAX(cxp_solicitudes.tipproben) AS tipproben, MAX(cxp_solicitudes.fecemisol) AS fecemisol, ".
			   "       MAX(rtrim(cxp_solicitudes.consol)) AS concepto, MAX(cxp_solicitudes.monsol) AS monsol, MAX(cxp_solicitudes.estprosol) AS estprosol, ".
			   "	   MAX(CAST(SUBSTR((CASE WHEN cxp_solicitudes.obssol is null THEN ' ' ELSE cxp_solicitudes.obssol END),1,250) AS CHAR(250))) AS observaciones, ".
			   "	   MAX(CASE WHEN cxp_rd_deducciones.monobjret is null THEN cxp_solicitudes.monsol ELSE cxp_rd_deducciones.monobjret END) AS mon_obj_ret, SUM(cxp_rd_deducciones.monret) AS monret, ".
			   "       (CASE MAX(cxp_solicitudes.tipproben) WHEN 'P' THEN MAX(rpc_proveedor.nompro) ".
			   "								       WHEN 'B' THEN ".$ls_cadena.
			   "				 	                   ELSE 'Nombre no N/D' END) AS nombre, ".
			   "  	   (CASE MAX(cxp_solicitudes.tipproben) WHEN 'P' THEN MAX(rpc_proveedor.rifpro) ".
			   "								 	   WHEN 'B' THEN MAX(rpc_beneficiario.ced_bene) ".
			   "     						  	       ELSE 'RIF. ó CI. N/D'END) AS cedula_rif ".
			   "   FROM cxp_solicitudes, cxp_dt_solicitudes, cxp_rd, cxp_rd_deducciones, sigesp_deducciones, rpc_beneficiario, rpc_proveedor ".$ls_filtrofrom.
			   "  WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."' ".
			   "    AND (cxp_solicitudes.estprosol = 'E' OR cxp_solicitudes.estprosol='C' OR cxp_solicitudes.estprosol='S' OR cxp_solicitudes.estprosol='P') ".
			   "    AND cxp_solicitudes.fecemisol >= '".$ad_fecdes."' ".
			   "    AND cxp_solicitudes.fecemisol <= '".$ad_fechas."' ".
			   "    AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
			   "    AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
			   "    AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
			   "    AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
			   "    AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
			   "    AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
			   "    AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
			   "    AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
			   "    AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
			   "    AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
			   "    AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
			   "    AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
			   "    AND	cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
			   "    AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
			   "    AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
			   "    AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
			   "    AND rpc_beneficiario.codemp = cxp_solicitudes.codemp ".
			   "    AND rpc_beneficiario.ced_bene = cxp_solicitudes.ced_bene ".
			   "    AND rpc_proveedor.codemp = cxp_solicitudes.codemp ".
			   "    AND rpc_proveedor.cod_pro = cxp_solicitudes.cod_pro ".$ls_filtroest.
			   "  GROUP BY cxp_solicitudes.numsol,rpc_beneficiario.apebene,rpc_beneficiario.nombene ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesgeneral ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesgeneral
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_comp_islr_gen($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_comp_islr
		//         Access: public
		//	    Arguments: ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT scb_cmp_ret.numcom, scb_cmp_ret.perfiscal,scb_cmp_ret.fecrep, scb_cmp_ret.codsujret,scb_cmp_ret.dirsujret,".
				"       scb_cmp_ret.nomsujret, scb_cmp_ret.rif, scb_cmp_ret.nit,scb_dt_cmp_ret.numfac,scb_dt_cmp_ret.numcon,".
				"       scb_dt_cmp_ret.fecfac,scb_dt_cmp_ret.totcmp_con_iva,scb_cmp_ret.numlic,".
				"       scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.iva_ret,scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.numsop,".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag,".
				"       (SELECT MAX(fecemisol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS fecemisol".
				"  FROM scb_dt_cmp_ret,scb_cmp_ret".
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."'".
				"   AND scb_cmp_ret.codret='0000000006'".
				"   AND scb_cmp_ret.numcom='".$as_numsol."'".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codret=scb_cmp_ret.codret".
				"   AND scb_dt_cmp_ret.numcom=scb_cmp_ret.numcom";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_comp_islr ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_ISLR->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesgeneral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesislr_detfact($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesislr_detfact($as_numcom)
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$arrResultado="";
		$ls_sql="SELECT max(cxp_rd.montotdoc) as montotdoc,".
				"       (SELECT dentipdoc FROM cxp_documento WHERE cxp_rd.codemp=cxp_documento.codemp AND max(cxp_rd.codtipdoc)=cxp_documento.codtipdoc) AS dentipdoc,
				        max(cxp_rd.codtipdoc) AS codtipdoc, max(dencondoc) AS dencondoc,
						(SELECT dended FROM sigesp_deducciones,cxp_rd_deducciones 
						  WHERE cxp_rd.codemp=cxp_rd_deducciones.codemp 
						    AND max(cxp_rd.numrecdoc)=cxp_rd_deducciones.numrecdoc 
						    AND max(cxp_rd.codtipdoc)=cxp_rd_deducciones.codtipdoc 
							AND max(cxp_rd.cod_pro)=cxp_rd_deducciones.cod_pro 
							AND max(cxp_rd.ced_bene)=cxp_rd_deducciones.ced_bene 
							AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp 
							AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
							AND sigesp_deducciones.estretmil='1') AS dended
				   FROM scb_dt_cmp_ret, cxp_rd, cxp_dt_solicitudes
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000006'
					AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp
					AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc
					AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
				  GROUP BY cxp_rd.codemp, scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detfact ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dencondoc=$row["dencondoc"];
				$ls_dended=$row["dended"];
				$arrResultado["montotdoc"]=$li_montotdoc;
				$arrResultado["dentipdoc"]=$ls_dentipdoc;
				$arrResultado["codtipdoc"]=$ls_codtipdoc;
				$arrResultado["dencondoc"]=$ls_dencondoc;
				$arrResultado["dended"]=$ls_dended;
			}
			$this->io_sql->free_result($rs_data);
		}
		
		return $arrResultado;
	}// end function uf_retencionesislr_detfact
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_comp_islr_especial($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_comp_islr
		//         Access: public
		//	    Arguments: ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT scb_cmp_ret.numcom, scb_cmp_ret.perfiscal,scb_cmp_ret.fecrep, scb_cmp_ret.codsujret,scb_cmp_ret.dirsujret,".
				"       scb_cmp_ret.nomsujret, scb_cmp_ret.rif, scb_cmp_ret.nit,scb_dt_cmp_ret.numfac".
				"  FROM scb_dt_cmp_ret,scb_cmp_ret".
				" WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'".
				"   AND scb_dt_cmp_ret.codret='0000000006'".
				"   AND scb_dt_cmp_ret.numsop='".$as_numsol."'".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codret=scb_cmp_ret.codret".
				"   AND scb_dt_cmp_ret.numcom=scb_cmp_ret.numcom";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_comp_islr ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->DS_ISLR->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesgeneral
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_comp_islr($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_comp_islr
		//         Access: public
		//	    Arguments: ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT scb_cmp_ret.numcom, scb_cmp_ret.perfiscal,scb_cmp_ret.fecrep, scb_cmp_ret.codsujret,".
				"       scb_cmp_ret.nomsujret, scb_cmp_ret.rif, scb_cmp_ret.nit, scb_cmp_ret.dirsujret,scb_dt_cmp_ret.fecfac".
				"  FROM scb_dt_cmp_ret,scb_cmp_ret".
				" WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'".
				"   AND scb_dt_cmp_ret.codret='0000000006'".
				"   AND scb_dt_cmp_ret.numsop='".$as_numsol."'".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codret=scb_cmp_ret.codret".
				"   AND scb_dt_cmp_ret.numcom=scb_cmp_ret.numcom";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_comp_islr ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesgeneral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_datos_retencion($as_numsol,$as_numrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_datos_retencion
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud de orden de pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las retenciones de una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	   $li_monded="";
	   $ls_sql="SELECT sigesp_deducciones.monded AS monded ".
			   "	FROM cxp_solicitudes
			  	    join cxp_dt_solicitudes on  ( cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
									              AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
									              AND cxp_solicitudes.cod_pro=cxp_dt_solicitudes.cod_pro
									              AND cxp_solicitudes.ced_bene=cxp_dt_solicitudes.ced_bene)
					join cxp_rd on ( cxp_dt_solicitudes.codemp=cxp_rd.codemp
											        AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
													AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
													AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
													AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc )
					join cxp_rd_deducciones on (cxp_rd.codemp=cxp_rd_deducciones.codemp
												AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
												AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
												AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc
												AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc)
					join sigesp_deducciones on (sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
												AND sigesp_deducciones.codded=cxp_rd_deducciones.codded) ".
			   " WHERE sigesp_deducciones.islr=1 ".
			   "   AND sigesp_deducciones.iva=0 ".
			   "   AND sigesp_deducciones.estretmun=0 ".
			   "   AND cxp_solicitudes.estprosol<>'A' ".
			   "   AND cxp_rd.numrecdoc='".$as_numrecdoc."'".
			   "   AND cxp_solicitudes.codemp='".$this->ls_codemp."' ".
			   "   AND cxp_solicitudes.numsol='".$as_numsol."' ".
			   " ORDER BY cxp_solicitudes.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesislr_cxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$li_monded=$rs_data->fields["monded"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_monded;
	}// end function uf_retencionesislr_cxp
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_dt_comp_islr($as_numcom,$as_numsol="")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_dt_comp_islr
		//         Access: public
		//	    Arguments: ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_cadena="";
		if($as_numsol!="")
			$ls_cadena=" AND scb_dt_cmp_ret.numsop='".$as_numsol."'";
		$ls_sql="SELECT fecfac, numfac, numcon, totcmp_con_iva, totcmp_sin_iva, basimp, porimp, iva_ret,numsop".
				"  FROM scb_dt_cmp_ret".
				" WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'".
				"   AND scb_dt_cmp_ret.codret='0000000006'".$ls_cadena.
				"   AND scb_dt_cmp_ret.numcom='".$as_numcom."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_dt_comp_islr ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesgeneral
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_retenciones($as_codded,$as_coddedhas,$as_tipded="T")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_retenciones
		//         Access: public
		//	    Arguments: as_codded     // Código de Deduccion
		//	    		   as_coddedhas     // Código de Deduccion final del intervalo
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de traer las deducciones en el intervalo indicado
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 20/06/2008									Fecha Última Modificación :  20/06/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->DS=new class_datastore();
		switch($as_tipded)
		{
			case "T":
				$ls_criterio="";
			break;
			case "S":
				$ls_criterio=" AND islr=1";
			break;
			case "I":
				$ls_criterio=" AND iva=1";
			break;
			case "M":
				$ls_criterio=" AND estretmun=1";
			break;
			case "A":
				$ls_criterio=" AND retaposol=1";
			break;
			case "1":
				$ls_criterio=" AND estretmil='1'";
			break;
			case "O":
				$ls_criterio=" AND otras=1";
			break;
		}
		if($as_codded!="")
		{
			$ls_criterio=$ls_criterio." AND codded='".$as_codded."'";
		}
		
		$ls_sql="SELECT codded, dended,islr,iva,estretmun,retaposol,estretmil".
				"  FROM sigesp_deducciones".
				" WHERE codemp='".$this->ls_codemp."'".
				$ls_criterio.
				" ORDER BY codded";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_retenciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesespecifico($as_codded,$as_coddedhas,$as_tipproben,$as_codprobenhas,$as_codprobendes,$ad_fecdes,
									  $ad_fechas,$as_tipper="")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesespecifico
		//         Access: public
		//	    Arguments: as_codded     // Código de Deduccion
		//	    		   as_coddedhas     // Código de Deduccion final del intervalo
		//	    		   as_tipproben     // Tipo de Proveedor ó beneficiario
		//	    		   as_codprobenhas     // código de Poveedor / Beneficiario Desde
		//	    		   as_codprobendes     // código de Poveedor / Beneficiario Hasta
		//	    		   ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas deducciones de las solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/07/2007									Fecha Última Modificación :  20/06/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_gestor = $_SESSION["ls_gestor"];
		$lb_valido=true;
		$ls_criterio="";
		$this->ds_detalle=new class_datastore();
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		if($as_codded!="")
		{
			$ls_criterio=$ls_criterio."	AND cxp_rd_deducciones.codded = '".$as_codded."'";
		}
		switch($as_tipproben)
		{
			case "P":
				if($as_codprobendes!="")
				{
					$ls_criterio=$ls_criterio."	AND cxp_solicitudes.cod_pro >= '".$as_codprobendes."'";
				}
				if($as_codprobenhas!="")
				{
					$ls_criterio=$ls_criterio."	AND cxp_solicitudes.cod_pro <= '".$as_codprobenhas."'";
				}
				if($as_tipper!="")
				{
					$ls_criterio=$ls_criterio." AND rpc_proveedor.tipperpro='".$as_tipper."'";
				}
				$ls_criterio=$ls_criterio." AND cxp_solicitudes.tipproben='".$as_tipproben."'";
				break;
			case "B":
				if($as_codprobendes!="")
				{
					$ls_criterio=$ls_criterio."	AND cxp_solicitudes.ced_bene >= '".$as_codprobendes."'";
				}
				if($as_codprobenhas!="")
				{
					$ls_criterio=$ls_criterio."	AND cxp_solicitudes.ced_bene <= '".$as_codprobenhas."'";
				}
				$ls_criterio=$ls_criterio." AND cxp_solicitudes.tipproben='".$as_tipproben."'";
				break;
		}
		$ls_cadena = $this->io_conexion->Concat('MAX(rpc_beneficiario.apebene)',"', '",'MAX(rpc_beneficiario.nombene)');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		if(strtoupper($ls_gestor)=='OCI8PO')
		{
			$ls_sql="SELECT cxp_solicitudes.numsol, rpc_proveedor.nitpro, cxp_solicitudes.tipproben,cxp_solicitudes.fecemisol, ".
				"       rtrim(cxp_solicitudes.consol) AS concepto, cxp_solicitudes.monsol, cxp_solicitudes.estprosol, ".
				"	   CAST(SUBSTR(COALESCE(cxp_solicitudes.obssol,' '),1,250) AS CHAR(250)) AS observaciones, MAX(cxp_rd.fecemidoc) AS fecemidoc, ".
				"	   cxp_rd_deducciones.monobjret AS mon_obj_ret, cxp_rd_deducciones.monret, cxp_rd.numrecdoc, cxp_rd.numref,".
				"       cxp_rd_deducciones.codded,cxp_rd_deducciones.porded,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN rpc_proveedor.nompro ".
				"								       WHEN 'B' THEN ".$ls_cadena.
				"				 	                   ELSE 'Nombre N/D' END) AS nombre, ".
				"  	   (CASE cxp_solicitudes.tipproben WHEN 'P' THEN rpc_proveedor.rifpro ".
				"								 	   WHEN 'B' THEN rpc_beneficiario.ced_bene ".
				"     						  	       ELSE 'RIF. ó CI. N/D'END) AS cedula_rif, ".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000001'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomiva,".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000003'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcommun,".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000004'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomapo,".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000006'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomislr, ".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000005'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcommil, ".
				"      ( 0 ) as ERROR ".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd_deducciones,rpc_proveedor,rpc_beneficiario,cxp_rd ".$ls_filtrofrom.
			   "  WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."' ".
			   "    AND (cxp_solicitudes.estprosol = 'E' OR cxp_solicitudes.estprosol='C' OR cxp_solicitudes.estprosol='S' OR cxp_solicitudes.estprosol='P') ".
			   "    AND cxp_solicitudes.fecemisol >= '".$ad_fecdes."' ".
			   "    AND cxp_solicitudes.fecemisol <= '".$ad_fechas."' ".
			   $ls_criterio.
				"   AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
				"   AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
				"   AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"   AND cxp_dt_solicitudes.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND rpc_beneficiario.codemp = cxp_solicitudes.codemp ".
				"   AND rpc_beneficiario.ced_bene = cxp_solicitudes.ced_bene ".
				"   AND rpc_proveedor.codemp = cxp_solicitudes.codemp ".
				"   AND rpc_proveedor.cod_pro = cxp_solicitudes.cod_pro ".
				"   AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene $ls_filtroest ".
				" GROUP BY cxp_solicitudes.numsol".
				" ORDER BY cxp_solicitudes.numsol";
		}
		else {
			$ls_sql="SELECT cxp_solicitudes.numsol, MAX(rpc_proveedor.nitpro) AS nitpro, MAX(cxp_solicitudes.tipproben) AS tipproben, MAX(cxp_solicitudes.fecemisol) AS fecemisol, ".
				"       MAX(rtrim(cxp_solicitudes.consol)) AS concepto, MAX(cxp_solicitudes.monsol) AS monsol, MAX(cxp_rd.fecemidoc) AS fecemidoc, ".
				"	   MAX(CAST(SUBSTRING(COALESCE(cxp_solicitudes.obssol,' '),1,250) AS CHAR(250))) AS observaciones, MAX(cxp_solicitudes.fecemisol) AS fecemisol, ".
				"	   SUM(cxp_rd_deducciones.monobjret) AS mon_obj_ret, SUM(cxp_rd_deducciones.monret) AS monret, MAX(cxp_rd.numrecdoc) AS numrecdoc, MAX(cxp_rd.numref) AS numref,".
				"       MAX(cxp_rd_deducciones.codded) as codded,MAX(cxp_rd_deducciones.porded) as porded,".
				"       (CASE MAX(cxp_solicitudes.tipproben) WHEN 'P' THEN MAX(rpc_proveedor.nompro) ".
				"								       WHEN 'B' THEN ".$ls_cadena.
				"				 	                   ELSE 'Nombre N/D' END) AS nombre, ".
				"  	   (CASE MAX(cxp_solicitudes.tipproben) WHEN 'P' THEN MAX(rpc_proveedor.rifpro) ".
				"								 	   WHEN 'B' THEN MAX(rpc_beneficiario.ced_bene) ".
				"     						  	       ELSE 'RIF. ó CI. N/D'END) AS cedula_rif, ".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000001'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomiva,".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000003'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcommun,".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000004'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomapo,".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000006'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomislr, ".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000005'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcommil, ".
				"      ( 0 ) as ERROR, ".
				"      (SELECT MAX(numcom) FROM scb_dt_cmp_ret".
				"        WHERE codret='0000000006'".
				"          AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"        GROUP BY scb_dt_cmp_ret.numsop ) as numcomislr1 ".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd_deducciones,rpc_proveedor,rpc_beneficiario,cxp_rd ".
			   "  WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."' ".
			   "    AND (cxp_solicitudes.estprosol = 'E' OR cxp_solicitudes.estprosol='C' OR cxp_solicitudes.estprosol='S' OR cxp_solicitudes.estprosol='P') ".
			   "    AND cxp_solicitudes.fecemisol >= '".$ad_fecdes."' ".
			   "    AND cxp_solicitudes.fecemisol <= '".$ad_fechas."' ".
			   $ls_criterio.
				"   AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
				"   AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
				"   AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"   AND cxp_dt_solicitudes.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND rpc_beneficiario.codemp = cxp_solicitudes.codemp ".
				"   AND rpc_beneficiario.ced_bene = cxp_solicitudes.ced_bene ".
				"   AND rpc_proveedor.codemp = cxp_solicitudes.codemp ".
				"   AND rpc_proveedor.cod_pro = cxp_solicitudes.cod_pro ".
				"   AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
				" GROUP BY cxp_solicitudes.numsol".
				" ORDER BY cxp_solicitudes.numsol";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesespecifico ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesespecifico
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesespecifico_detalle($as_codded,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesespecifico_detalle
		//         Access: public
		//	    Arguments: as_codded     // Código de Deduccion
		//	    		   as_numsol     // Nùmero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas recepciones de las solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/02/2010									Fecha Última Modificación :  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT cxp_rd_deducciones.monobjret, cxp_rd_deducciones.monret, cxp_rd.numrecdoc, cxp_rd.numref, cxp_rd_deducciones.porded, cxp_rd.fecregdoc, cxp_rd.fecemidoc ".
				"  FROM cxp_solicitudes,cxp_dt_solicitudes,cxp_rd_deducciones,cxp_rd ".
				" WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."' ".
				"   AND cxp_solicitudes.numsol = '".$as_numsol."' ".
				"   AND cxp_rd_deducciones.codded = '".$as_codded."' ".
				"   AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
				"   AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
				"   AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
				"   AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"   AND cxp_dt_solicitudes.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
				" ORDER BY cxp_rd.numrecdoc";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesespecifico_detalle ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_retencionesespecifico_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesespecificomanual($as_codret,$as_tipproben,$as_codprobenhas,$as_codprobendes,$ad_fecdes,
									  		$ad_fechas,$as_tipper="")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesespecificomanual
		//         Access: public
		//	    Arguments: as_codded     // Código de Deduccion
		//	    		   as_coddedhas     // Código de Deduccion final del intervalo
		//	    		   as_tipproben     // Tipo de Proveedor ó beneficiario
		//	    		   as_codprobenhas     // código de Poveedor / Beneficiario Desde
		//	    		   as_codprobendes     // código de Poveedor / Beneficiario Hasta
		//	    		   ad_fecdes     // Rango de fecha desde
		//	    		   ad_fechas     // Rango de fecha hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que se encarga de extraer todas aquellas deducciones de las solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 10/07/2007									Fecha Última Modificación :  20/06/2008
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_gestor = $_SESSION["ls_gestor"];
		$lb_valido=true;
		$ls_criterio="";
		$ls_filtrofrom = '';
		$this->ds_detalle=new class_datastore();
		$ad_fecdes=$this->io_funciones->uf_convertirdatetobd($ad_fecdes);
		$ad_fechas=$this->io_funciones->uf_convertirdatetobd($ad_fechas);
		switch($as_tipproben)
		{
			case "P":
				$ls_filtrofrom = $ls_filtrofrom." ,rpc_proveedor ";
//				$ls_criterio=$ls_criterio."	 INNER JOIN rpc_proveedor ".
//										  "    ON scb_cmp_ret.codemp = rpc_proveedor.codemp ".
//										  "   AND scb_cmp_ret.codsujret = rpc_proveedor.cod_pro ".
//										  "   AND scb_cmp_ret.rif = rpc_proveedor.rifpro";
				$ls_criterio=$ls_criterio."   AND scb_cmp_ret.codemp = rpc_proveedor.codemp ".
										  "   AND scb_cmp_ret.codsujret = rpc_proveedor.cod_pro ".
										  "   AND scb_cmp_ret.rif = rpc_proveedor.rifpro";
				if($as_codprobendes!="")
				{
					$ls_criterio=$ls_criterio."	AND rpc_proveedor.cod_pro >= '".$as_codprobendes."'";
				}
				if($as_codprobenhas!="")
				{
					$ls_criterio=$ls_criterio."	AND rpc_proveedor.cod_pro <= '".$as_codprobenhas."'";
				}
				if($as_tipper!="")
				{
					$ls_criterio=$ls_criterio." AND rpc_proveedor.tipperpro='".$as_tipper."'";
				}
				break;
			case "B":
				$ls_filtrofrom = $ls_filtrofrom." ,rpc_beneficiario ";
//				$ls_criterio=$ls_criterio."	 INNER JOIN rpc_beneficiario ".
//										  "    ON scb_cmp_ret.codemp = rpc_beneficiario.codemp ".
//										  "   AND scb_cmp_ret.codsujret = rpc_beneficiario.ced_bene ".
//										  "   AND scb_cmp_ret.rif = rpc_beneficiario.rifben";
				$ls_criterio=$ls_criterio." AND scb_cmp_ret.codemp = rpc_beneficiario.codemp ".
										  " AND scb_cmp_ret.codsujret = rpc_beneficiario.ced_bene ".
										  " AND scb_cmp_ret.rif = rpc_beneficiario.rifben";
				if($as_codprobendes!="")
				{
					$ls_criterio=$ls_criterio."	AND rpc_beneficiario.ced_bene >= '".$as_codprobendes."'";
				}
				if($as_codprobenhas!="")
				{
					$ls_criterio=$ls_criterio."	AND rpc_beneficiario.ced_bene <= '".$as_codprobenhas."'";
				}
				break;
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ";
			$ls_filtrofrom = $ls_filtrofrom." ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
//		$ls_sql="SELECT MAX(scb_cmp_ret.nomsujret) AS nomsujret, MAX(scb_cmp_ret.rif) AS rif, scb_cmp_ret.numcom, SUM(scb_dt_cmp_ret.basimp) AS basimp, ".
//				"       MAX(scb_dt_cmp_ret.porimp) AS porimp, SUM(scb_dt_cmp_ret.totimp) AS totimp, MAX(scb_cmp_ret.fecrep) AS fecrep ".
//				"  FROM scb_cmp_ret ".
//				" INNER JOIN scb_dt_cmp_ret ".
//				"    ON scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp ".
//				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
//				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".
//				$ls_criterio.
//				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
//				"   AND scb_cmp_ret.codret='".$as_codret."' ".
//				"   AND scb_cmp_ret.fecrep>='".$ad_fecdes."' ".
//				"   AND scb_cmp_ret.fecrep<='".$ad_fechas."' ".
//				"   AND scb_cmp_ret.origen='M' ".
//				"GROUP BY scb_cmp_ret.codemp, scb_cmp_ret.codret, scb_cmp_ret.numcom";
		$ls_sql="SELECT MAX(scb_cmp_ret.nomsujret) AS nomsujret, MAX(scb_cmp_ret.rif) AS rif, scb_cmp_ret.numcom, SUM(scb_dt_cmp_ret.basimp) AS basimp, ".
				"       MAX(scb_dt_cmp_ret.porimp) AS porimp, SUM(scb_dt_cmp_ret.totimp) AS totimp, MAX(scb_cmp_ret.fecrep) AS fecrep ".
				"  FROM scb_cmp_ret,scb_dt_cmp_ret ".$ls_filtrofrom.
				"  WHERE scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp ".
				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ".
				$ls_criterio.
				"   AND scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='".$as_codret."' ".
				"   AND scb_cmp_ret.fecrep>='".$ad_fecdes."' ".
				"   AND scb_cmp_ret.fecrep<='".$ad_fechas."' ".$ls_filtroest.
				"   AND scb_cmp_ret.origen='M' ".
				"GROUP BY scb_cmp_ret.codemp, scb_cmp_ret.codret, scb_cmp_ret.numcom";
		$this->rs_detalle=$this->io_sql->select($ls_sql);
		if($this->rs_detalle===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesespecificomanual ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_retencionesespecificomanual
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesiva_proveedor($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesiva_proveedor
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de iva
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ";
			$ls_filtrofrom = " ,scb_dt_cmp_ret,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
	    $ls_sql="SELECT scb_cmp_ret.numcom, scb_cmp_ret.codret, scb_cmp_ret.fecrep, scb_cmp_ret.perfiscal, ".
	            "       scb_cmp_ret.codsujret, scb_cmp_ret.nomsujret, scb_cmp_ret.rif, scb_cmp_ret.dirsujret, scb_cmp_ret.estcmpret, ".
			 	"        (SELECT telpro ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS telpro, ".
			 	"        (SELECT dirpro ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS dirpro, ".
			 	"        (SELECT email ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS email, ".
	    		"        (SELECT telemp ".
				" 		    FROM sigesp_empresa ".
				"		   WHERE sigesp_empresa.codemp=scb_cmp_ret.codemp) AS telemp ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."'".
				"   AND scb_cmp_ret.numcom = '".$as_numcom."' ".
				"   AND scb_cmp_ret.codret ='0000000001' $ls_filtroest ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesiva_proveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesiva_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_select_concepto_sop($as_ordenp)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesiva_proveedor
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de iva
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_concepto="";
	    $ls_sql="SELECT consol ".
			 	"  FROM cxp_solicitudes ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numsol = '".$as_ordenp."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_concepto_sop ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ls_concepto=$rs_data->fields["consol"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_concepto;
	}// end function uf_retencionesiva_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesiva_detalle($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesiva_detalle
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de iva
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT max(codret) as codret, max(numcom) as numcom, max(numope) as numope, max(fecfac) as fecfac, ".
				"		 max(numfac) as numfac, max(numcon) as numcon, max(numnd) as numnd, max(numnc) as numnc, ".
				"		 max(tiptrans) as tiptrans, max(totcmp_sin_iva) as totcmp_sin_iva , max(totcmp_con_iva) as totcmp_con_iva, ".
				"		 max(basimp) as basimp, porimp, max(totimp) as totimp, max(iva_ret) as iva_ret, max(desope) as desope, ".
				"		 max(numsop) as numsop, max(codban) as codban, max(ctaban) as ctaban, max(numdoc) as numdoc, max(codope) as codope, ".
				"       (SELECT MAX(nomban)".
				"		   FROM scb_banco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codban=scb_banco.codban".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS nomban,".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(monto)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS montopag,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag,".
				"       (SELECT MAX(fecemisol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS fecemisol".
				"  FROM scb_dt_cmp_ret ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numcom='".$as_numcom."' ".
				"	AND codret='0000000001' ".
				"	AND tipdoctesnac<>'1' ".
				" GROUP BY codemp, numfac, porimp, numnd,numnc, numsop".
				" ORDER BY numope ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesiva_detalle ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesiva_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesiva_municipal($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesiva_detalle
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de iva
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT max(codret) as codret, max(numcom) as numcom, max(numope) as numope, max(fecfac) as fecfac, ".
				"		 max(numfac) as numfac, max(numcon) as numcon, max(numnd) as numnd, max(numnc) as numnc, ".
				"		 max(tiptrans) as tiptrans, SUM(totcmp_sin_iva) as totcmp_sin_iva , max(totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(basimp) as basimp, porimp, SUM(totimp) as totimp, SUM(iva_ret) as iva_ret, max(desope) as desope, ".
				"		 max(numsop) as numsop, max(codban) as codban, max(ctaban) as ctaban, max(numdoc) as numdoc, max(codope) as codope, ".
				"       (SELECT MAX(nomban)".
				"		   FROM scb_banco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codban=scb_banco.codban".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS nomban,".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(monto)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS montopag,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag".
				"  FROM scb_dt_cmp_ret ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numcom='".$as_numcom."' ".
				"	AND codret='0000000003' ".
				" GROUP BY codemp, numfac, porimp, numnd,numnc, numsop".
				" ORDER BY numope ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesiva_detalle ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesiva_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesaporte_proveedor($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesaporte_proveedor
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de aporte social
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/10/08									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene =cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom "; 
			$ls_filtrofrom = " , scb_dt_cmp_ret, cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
	    $ls_sql="SELECT scb_cmp_ret.numcom,scb_cmp_ret.codret,scb_cmp_ret.fecrep,scb_cmp_ret.perfiscal,scb_cmp_ret.codsujret, ".
	            "       scb_cmp_ret.nomsujret,scb_cmp_ret.rif,scb_cmp_ret.dirsujret,scb_cmp_ret.estcmpret, ".
			 	"        (SELECT telpro ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS telpro ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."'".
				"   AND scb_cmp_ret.numcom = '".$as_numcom."'".
				"   AND scb_cmp_ret.codret ='0000000004' $ls_filtroest ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesaporte_proveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesaporte_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesaporte_detalle($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesaporte_detalle
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de aporte social
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/10/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT max(codret) as codret, max(numcom) as numcom, max(numope) as numope, max(fecfac) as fecfac, ".
				"		 max(numfac) as numfac, max(numcon) as numcon, max(numnd) as numnd, max(numnc) as numnc, ".
				"		 max(tiptrans) as tiptrans, SUM(totcmp_sin_iva) as totcmp_sin_iva , max(totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(basimp) as basimp, porimp, SUM(totimp) as totimp, SUM(iva_ret) as iva_ret, max(desope) as desope, ".
				"		 max(numsop) as numsop, max(codban) as codban, max(ctaban) as ctaban, max(numdoc) as numdoc, max(codope) as codope ".
				"  FROM scb_dt_cmp_ret ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numcom='".$as_numcom."' ".
				"	AND codret='0000000004' ".
				" GROUP BY codemp, numfac, porimp,numsop ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesaporte_detalle ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesaporte_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesaporte_detalle2($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesaporte_detalle
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de aporte social
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 21/10/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT max(codret) as codret, max(numcom) as numcom, max(numope) as numope, max(fecfac) as fecfac, ".
				"		 array_to_string(array_agg(trim(numfac)), ', ') as numfac, max(numcon) as numcon, max(numnd) as numnd, max(numnc) as numnc, ".
				"		 max(tiptrans) as tiptrans, SUM(totcmp_sin_iva) as totcmp_sin_iva , SUM(totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(basimp) as basimp, porimp, SUM(totimp) as totimp, SUM(iva_ret) as iva_ret, max(desope) as desope, ".
				"		 max(numsop) as numsop, max(codban) as codban, max(ctaban) as ctaban, max(numdoc) as numdoc, max(codope) as codope ".
				"  FROM scb_dt_cmp_ret ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numcom='".$as_numcom."' ".
				"	AND codret='0000000004' ".
				" GROUP BY codemp, porimp,numsop "; //print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesaporte_detalle ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesaporte_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesaporte_detfact($as_numsop,$as_numrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_detfact($as_numcom)
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$arrResultado="";
		$li_montotdoc=0; 
		$ls_sql="SELECT cxp_rd.montotdoc
				   FROM cxp_rd, cxp_dt_solicitudes
				  WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'
				    AND cxp_dt_solicitudes.numsol='".$as_numsop."'
				    AND cxp_dt_solicitudes.numrecdoc='".$as_numrecdoc."'
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detfact ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado["montotdoc"]=$li_montotdoc;
		return $arrResultado;
	}// end function uf_retencionesmunicipales_detfact
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracioninformativa($ls_fecemides,$ls_fecemihas,$as_anio,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracioninformativa
		//         Access: public
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // Año del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que genera los txt de la declaración informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
//		$ld_fechadesde=$as_anio."-".$as_mes."-01";
//		$ld_fechahasta=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
		$ld_fechadesde=$this->io_funciones->uf_convertirdatetobd($ls_fecemides);
		$ld_fechahasta=$this->io_funciones->uf_convertirdatetobd($ls_fecemihas);

		$ls_criterio="";
		$ls_archivo="declaracioninformativa/Retencion_IVA_".date("Y_m_d_H_i").".txt";
		$lo_archivo=fopen("$ls_archivo","a+");
		$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
								  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
//		switch($as_quincena)
//		{
//			case "1":
//				$ld_fechahasta=$as_anio."-".$as_mes."-15";
//				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
//										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
//				break;
//
//			case "2":
//				$ld_fechadesde=$as_anio."-".$as_mes."-16";
//				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
//										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
//				break;
//		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
	    $ls_periodofiscal=substr($ld_fechadesde,0,4).substr($ld_fechadesde,5,2);
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ";
			$ls_filtrofrom = " ,scb_dt_cmp_ret,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT scb_cmp_ret.* ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret ='0000000001' ".
				"   AND scb_cmp_ret.estcmpret=1 ".
//				"   AND scb_cmp_ret.perfiscal ='".$ls_periodofiscal."' ".
				$ls_criterio.$ls_filtroest;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_declaracioninformativa ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_agenteretencion=$_SESSION["la_empresa"]["nombre"];
			$ls_rifagenteret=str_replace('-','',$_SESSION["la_empresa"]["rifemp"]);
			$ls_direccionagenteret=$_SESSION["la_empresa"]["direccion"];
			$li_j=0;
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$li_j++;
				$ls_numcom=$row["numcom"];
				$ls_perfiscal=$row["perfiscal"];
				$ls_codsujret=$row["codsujret"];
				$ls_nomsujret=$row["nomsujret"];
				$ls_rif=str_replace('-','',$row["rif"]);
				$ls_dirsujret=$row["dirsujret"];
				$lb_valido=$this->uf_retencionesiva_detalle($ls_numcom);
				if($lb_valido)
				{
					if(strlen($ls_numcom)==15)
					{
						$ls_numcom1=substr($ls_numcom,0,6);
						$ls_numcom2=substr($ls_numcom,6,8);
						$ls_numcom =$ls_numcom1.$ls_numcom2;
					}
					$li_total=$this->ds_detalle->getRowCount("numfac");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numope=$this->ds_detalle->data["numope"][$li_i];
						$ls_numfac=trim($this->ds_detalle->data["numfac"][$li_i]);
						$ls_numref=trim($this->ds_detalle->data["numcon"][$li_i]);
						$ld_fecfac=substr($this->ds_detalle->data["fecfac"][$li_i],0,10);
						$li_siniva=number_format($this->ds_detalle->data["totcmp_sin_iva"][$li_i],2,".","");
						$li_coniva=number_format($this->ds_detalle->data["totcmp_con_iva"][$li_i],2,".","");
						$li_baseimp=number_format($this->ds_detalle->data["basimp"][$li_i],2,".","");
						$li_porimp=number_format($this->ds_detalle->data["porimp"][$li_i],2,".","");
						$li_totimp=number_format($this->ds_detalle->data["totimp"][$li_i],2,".","");
						$li_ivaret=number_format($this->ds_detalle->data["iva_ret"][$li_i],2,".","");
						$ls_numdoc=$this->ds_detalle->data["numdoc"][$li_i];
						$ls_tiptrans=$this->ds_detalle->data["tiptrans"][$li_i];
						$ls_numnotdeb=$this->ds_detalle->data["numnd"][$li_i];
						$ls_numnotcre=$this->ds_detalle->data["numnc"][$li_i];
						$li_monto=$li_baseimp + $li_totimp;
						$li_totdersiniva= number_format(abs($li_coniva - $li_monto),2,".","");
						$ls_numfacafec="0";
						$ls_tipope="C";
						$ls_tipdoc="01";
						$ls_numexp="0";
						if(trim($ls_numnotdeb)!="")
							$ls_tipdoc="02";
						if(trim($ls_numnotcre)!="")
							$ls_tipdoc="03";
						$ls_cadena=$ls_rifagenteret."\t".$ls_perfiscal."\t".$ld_fecfac."\t".$ls_tipope."\t".$ls_tipdoc."\t".
								   $ls_rif."\t".$ls_numfac."\t".$ls_numref."\t".$li_coniva."\t".$li_baseimp."\t".$li_ivaret."\t".
								   $ls_numfacafec."\t".$ls_numcom."\t".$li_totdersiniva."\t".$li_porimp."\t".$ls_numexp."\r\n";
						if ($lo_archivo)
						{
							@fwrite($lo_archivo,$ls_cadena);
						}
					}
				}
			}
			if($li_j==0)
			{
				$this->io_mensajes->message("No existen retenciones para el periodo indicado");
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="PROCESS";
				$ls_descripcion ="Genero el txt de Declaración Informativa Para el Año ".$as_anio." Mes ".$as_mes." Archivo ".$ls_archivo." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
			}
		}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracioninformativa_excel($ld_fechadesde,$ld_fechahasta,$as_anio,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracioninformativa
		//         Access: public
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // Año del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que genera los txt de la declaración informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
//		$ld_fechadesde=$as_anio."-".$as_mes."-01";
//		$ld_fechahasta=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
//	    $ls_periodofiscal=substr($ld_fechadesde,0,4).substr($ld_fechadesde,5,2);
//		$ls_criterio="";
//		switch($as_quincena)
//		{
//			case "1":
//				$ld_fechahasta=$as_anio."-".$as_mes."-15";
//				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
//										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
//				break;
//
//			case "2":
//				$ld_fechadesde=$as_anio."-".$as_mes."-16";
//				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
//										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
//				break;
//		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
								  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
	    $ls_periodofiscal=substr($ld_fechadesde,0,4).substr($ld_fechadesde,5,2);
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ";
			$ls_filtrofrom = " ,scb_dt_cmp_ret,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT scb_cmp_ret.* ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret ='0000000001' ".
				"   AND scb_cmp_ret.estcmpret=1 ".
				"   AND scb_cmp_ret.perfiscal ='".$ls_periodofiscal."' ".
				$ls_criterio.$ls_filtroest;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_declaracioninformativa ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
		}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracioninformativamunicipal($as_quincena,$as_mes,$as_anio,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracioninformativamunicipal
		//         Access: public
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // Año del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que genera los txt de la declaración informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$as_anio."-".$as_mes."-01";
		$ld_fechahasta=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
	    $ls_periodofiscal=substr($ld_fechadesde,0,4).substr($ld_fechadesde,5,2);
		$ls_criterio="";
		$ls_archivo="declaracionmunicipal/Retencion_Municipal_".date("Y_m_d_H_i").".txt";
		$lo_archivo=fopen("$ls_archivo","a+");
		switch($as_quincena)
		{
			case "1":
				$ld_fechahasta=$as_anio."-".$as_mes."-15";
				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
				break;

			case "2":
				$ld_fechadesde=$as_anio."-".$as_mes."-16";
				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
				break;
			case "3":
				$ls_criterio=$ls_criterio." AND scb_cmp_ret.fecrep >='".$ld_fechadesde."'".
										  " AND scb_cmp_ret.fecrep <='".$ld_fechahasta."'";
				break;
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom ";
			$ls_filtrofrom = " ,scb_dt_cmp_ret,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT scb_cmp_ret.* ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret ='0000000003' ".
				"   AND scb_cmp_ret.estcmpret=1 ".
				"   AND scb_cmp_ret.perfiscal ='".$ls_periodofiscal."' ".
				$ls_criterio.$ls_filtroest;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_declaracioninformativa ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$ls_agenteretencion=$_SESSION["la_empresa"]["nombre"];
			$ls_rifagenteret=str_replace('-','',$_SESSION["la_empresa"]["rifemp"]);
			$ls_direccionagenteret=$_SESSION["la_empresa"]["direccion"];
			$li_j=0;
			while(($row=$this->io_sql->fetch_row($rs_data))&&($lb_valido))
			{
				$li_j++;
				$ls_numcom=$row["numcom"];
				$ls_perfiscal=$row["perfiscal"];
				$ls_codsujret=$row["codsujret"];
				$ls_nomsujret=$row["nomsujret"];
				$ls_rif=str_replace('-','',$row["rif"]);
				$ls_dirsujret=$row["dirsujret"];
				$lb_existe=$this->uf_retencionesiva_municipal($ls_numcom);
				if($lb_existe)
				{
					if(strlen($ls_numcom)==15)
					{
						$ls_numcom1=substr($ls_numcom,0,6);
						$ls_numcom2=substr($ls_numcom,6,8);
						$ls_numcom =$ls_numcom1.$ls_numcom2;
					}
					$li_total=$this->ds_detalle->getRowCount("numfac");
					for($li_i=1;$li_i<=$li_total;$li_i++)
					{
						$ls_numope=$this->ds_detalle->data["numope"][$li_i];
						$ls_numfac=trim($this->ds_detalle->data["numfac"][$li_i]);
						$ls_numref=trim($this->ds_detalle->data["numcon"][$li_i]);
						$ld_fecfac=substr($this->ds_detalle->data["fecfac"][$li_i],0,10);
						$li_siniva=number_format($this->ds_detalle->data["totcmp_sin_iva"][$li_i],2,".","");
						$li_coniva=number_format($this->ds_detalle->data["totcmp_con_iva"][$li_i],2,".","");
						$li_baseimp=number_format($this->ds_detalle->data["basimp"][$li_i],2,".","");
						$li_porimp=number_format($this->ds_detalle->data["porimp"][$li_i],2,".","");
						$li_totimp=number_format($this->ds_detalle->data["totimp"][$li_i],2,".","");
						$li_ivaret=number_format($this->ds_detalle->data["iva_ret"][$li_i],2,".","");
						$ls_numdoc=$this->ds_detalle->data["numdoc"][$li_i];
						$ls_tiptrans=$this->ds_detalle->data["tiptrans"][$li_i];
						$ls_numnotdeb=$this->ds_detalle->data["numnd"][$li_i];
						$ls_numnotcre=$this->ds_detalle->data["numnc"][$li_i];
						$li_monto=$li_baseimp + $li_totimp;
						$li_totdersiniva= number_format(abs($li_coniva - $li_monto),2,".","");
						$ls_numfacafec="0";
						$ls_tipope="C";
						$ls_tipdoc="01";
						$ls_numexp="0";
						$ls_periodoimp=$ld_fechadesde." ".$ld_fechahasta;
						$ls_operacion="0738";
						$ls_cadena=$ls_rifagenteret."\t".$ls_periodoimp."\t".$ls_operacion."\t".$ls_rif."\t".$ls_numcom."\t".$li_ivaret."\r\n";
						if ($lo_archivo)
						{
							@fwrite($lo_archivo,$ls_cadena);
						}
					}
				}
			}
			if($li_j==0)
			{
				$this->io_mensajes->message("No existen retenciones para el periodo indicado");
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="PROCESS";
				$ls_descripcion ="Genero el txt de Declaración Informativa Para el Año ".$as_anio." Mes ".$as_mes." Archivo ".$ls_archivo." Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
			}
		}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesmunicipales_proveedor($as_numcom,$as_mes,$as_anio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_proveedor
		//         Access: public
		//	    Arguments: as_numcom // Numero de comprobante municipal
		//	    		   as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$as_anio."-".$as_mes."-01";
		$ld_fechahasta=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom "; 
			$ls_filtrofrom = " ,scb_dt_cmp_ret,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT scb_cmp_ret.numcom,scb_cmp_ret.codret,scb_cmp_ret.fecrep,scb_cmp_ret.perfiscal,scb_cmp_ret.codsujret, ".
		        " scb_cmp_ret.nomsujret,scb_cmp_ret.rif,nit,scb_cmp_ret.dirsujret,scb_cmp_ret.estcmpret,scb_cmp_ret.numlic, ".
			 	"        (SELECT telpro ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS telpro, ".
			 	"        (SELECT email ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS email ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='0000000003' ".
				"   AND scb_cmp_ret.fecrep>='".$ld_fechadesde."' ".
				"   AND scb_cmp_ret.fecrep<='".$ld_fechahasta."' ".
				"   AND scb_cmp_ret.numcom='".$as_numcom."' $ls_filtroest ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_proveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesmunicipales_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesunoxmil_proveedor($as_numcom,$as_mes,$as_anio)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_proveedor
		//         Access: public
		//	    Arguments: as_numcom // Numero de comprobante municipal
		//	    		   as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$as_anio."-".$as_mes."-01";
		$ld_fechahasta=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc ".
							" AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp  ".
							" AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
							" AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom "; 
			$ls_filtrofrom = " , scb_dt_cmp_ret, cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT scb_cmp_ret.numcom,scb_cmp_ret.codret,scb_cmp_ret.fecrep,scb_cmp_ret.perfiscal,scb_cmp_ret.codsujret, ".
		        "       scb_cmp_ret.nomsujret,scb_cmp_ret.rif,nit,scb_cmp_ret.dirsujret,scb_cmp_ret.estcmpret,scb_cmp_ret.numlic, ".
			 	"        (SELECT telpro ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS telpro, ".
			 	"        (SELECT dirpro ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS dirpro, ".
			 	"        (SELECT email ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS email, ".
			 	"        (SELECT codest ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS codest, ".
			 	"        (SELECT codmun ".
				" 		    FROM rpc_proveedor ".
				"		   WHERE rpc_proveedor.codemp=scb_cmp_ret.codemp ".
				"			 AND rpc_proveedor.cod_pro=scb_cmp_ret.codsujret) AS codmun ".
				"  FROM scb_cmp_ret ".$ls_filtrofrom.
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='0000000005' ".
//				"   AND scb_cmp_ret.fecrep>='".$ld_fechadesde."' ".
//				"   AND scb_cmp_ret.fecrep<='".$ld_fechahasta."' ".
				"   AND scb_cmp_ret.numcom='".$as_numcom."' $ls_filtroest ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_proveedor ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesmunicipales_proveedor
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesmunicipales_detalles($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_detalles
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codemp, numfac, porimp, numnd,numnc, max(codret) as codret, max(numcom) as numcom,max(numcon) as numcon, max(numope) as numope, ". 
				"       max(fecfac) as fecfac,max(tiptrans) as tiptrans, SUM(totcmp_sin_iva) as totcmp_sin_iva , max(totcmp_con_iva) as totcmp_con_iva, ".
				"       SUM(basimp) as basimp,SUM(totimp) as totimp, SUM(iva_ret) as iva_ret, max(desope) as desope, ".
				"       max(numsop) as numsop, max(codban) as codban, max(ctaban) as ctaban, max(numdoc) as numdoc, ".
				"       max(codope) as codope, ".
				"       max((SELECT fecemisol FROM cxp_solicitudes ".
				"             WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp ".
				"               AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol)) AS fecemisol, ".
				"       (SELECT MAX(nomban)".
				"		   FROM scb_banco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codban=scb_banco.codban".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS nomban,".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(monto)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS montopag,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag,".
				"       (SELECT MAX(consol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS consol".
				"  FROM scb_dt_cmp_ret ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numcom='".$as_numcom."' ".
				"	AND codret='0000000003' ".
				" GROUP BY codemp, numfac, porimp, numnd,numnc,numsop".
				" ORDER BY numope ";
		/*$ls_sql="SELECT max(scb_dt_cmp_ret.codret) as codret, ".
		        "       max(scb_dt_cmp_ret.numsop) as numsop, ".
				"		max(scb_dt_cmp_ret.fecfac) as fecfac, ".
				"		max(scb_dt_cmp_ret.numfac) as numfac, ".
				"		max(scb_dt_cmp_ret.numcon) as numcon, ".
				"	    scb_dt_cmp_ret.basimp, ".
				"		scb_dt_cmp_ret.porimp, ".
				"		scb_dt_cmp_ret.totimp, ".
				"		scb_dt_cmp_ret.iva_ret, ".
				"		scb_dt_cmp_ret.totcmp_con_iva, ".
				"		max(cxp_solicitudes.fecemisol) as fecemisol, ".
				"		max(cxp_solicitudes.consol) as descrip ".
				"  FROM scb_dt_cmp_ret, cxp_solicitudes, cxp_dt_solicitudes ".
				"  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."' ".
				"    AND scb_dt_cmp_ret.numcom='".$as_numcom."' ".
				"    AND scb_dt_cmp_ret.codret='0000000003' ".
				"	AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp ".
				//"	AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc ". Comentado Por Carlos Zambrano
				"	AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol ".
				"	AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp ".
				"	AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol ".
				"  GROUP BY scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret,scb_dt_cmp_ret.totcmp_con_iva ";*/
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		//print_r($rs_data);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesmunicipales_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesmunicipales_detfact($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_detfact($as_numcom)
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$arrResultado="";
		$ls_sql="SELECT max(cxp_rd.montotdoc) as montotdoc,".
				"       (SELECT dentipdoc FROM cxp_documento WHERE cxp_rd.codemp=cxp_documento.codemp AND max(cxp_rd.codtipdoc)=cxp_documento.codtipdoc) AS dentipdoc,
				        max(cxp_rd.codtipdoc) AS codtipdoc, max(dencondoc) AS dencondoc,
						(SELECT dended FROM sigesp_deducciones,cxp_rd_deducciones 
						  WHERE cxp_rd.codemp=cxp_rd_deducciones.codemp 
						    AND max(cxp_rd.numrecdoc)=cxp_rd_deducciones.numrecdoc 
						    AND max(cxp_rd.codtipdoc)=cxp_rd_deducciones.codtipdoc 
							AND max(cxp_rd.cod_pro)=cxp_rd_deducciones.cod_pro 
							AND max(cxp_rd.ced_bene)=cxp_rd_deducciones.ced_bene 
							AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp 
							AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
							AND sigesp_deducciones.estretmun='1') AS dended
				   FROM scb_dt_cmp_ret, cxp_rd, cxp_dt_solicitudes
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000003'
					AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp
					AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc
					AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
				  GROUP BY cxp_rd.codemp, scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detfact ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dencondoc=$row["dencondoc"];
				$ls_dended=$row["dended"];
				$arrResultado["montotdoc"]=$li_montotdoc;
				$arrResultado["dentipdoc"]=$ls_dentipdoc;
				$arrResultado["codtipdoc"]=$ls_codtipdoc;
				$arrResultado["dencondoc"]=$ls_dencondoc;
				$arrResultado["dended"]=$ls_dended;
			}
			$this->io_sql->free_result($rs_data);
		}
		
		return $arrResultado;
	}// end function uf_retencionesmunicipales_detfact
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesunoxmil_detalles($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesunoxmil_detalles
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			                "  						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
							"          				   AND codsis='CXP' ".
				            "                          AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1)" .
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ".
							" AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = scb_dt_cmp_ret.codemp ".
							" AND cxp_dt_solicitudes.numsol = scb_dt_cmp_ret.numsop ".
							" AND cxp_dt_solicitudes.numrecdoc = scb_dt_cmp_ret.numdoc "; 
			$ls_filtrofrom = " , cxp_dt_solicitudes, cxp_rd, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT max(codret) as codret, max(numcom) as numcom,max(numcon) as numcon, max(numope) as numope, max(fecfac) as fecfac, ".
				"		 max(numfac) as numfac, max(numnd) as numnd, max(numnc) as numnc, ".
				"		 max(tiptrans) as tiptrans, SUM(totcmp_sin_iva) as totcmp_sin_iva , max(totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(basimp) as basimp, porimp, SUM(totimp) as totimp, SUM(iva_ret) as iva_ret, max(desope) as desope, ".
				"		 numsop, max(codban) as codban, max(ctaban) as ctaban, max(numdoc) as numdoc, max(codope) as codope, ".
				"       MAX((SELECT fecemisol FROM cxp_solicitudes ".
				"             WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp ".
				"               AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol)) AS fecemisol, ".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag".
				"  FROM scb_dt_cmp_ret ".$ls_filtrofrom.
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND numcom='".$as_numcom."' ".
				"	AND codret='0000000005' ".$ls_filtroest.
				" GROUP BY codemp, numfac, porimp, numnd,numnc,numsop".
				" ORDER BY numope ";
		/*$ls_sql="SELECT max(scb_dt_cmp_ret.codret) as codret, ".
		        "       max(scb_dt_cmp_ret.numsop) as numsop, ".
				"		max(scb_dt_cmp_ret.fecfac) as fecfac, ".
				"		max(scb_dt_cmp_ret.numfac) as numfac, ".
				"		max(scb_dt_cmp_ret.numcon) as numcon, ".
				"	    scb_dt_cmp_ret.basimp, ".
				"		scb_dt_cmp_ret.porimp, ".
				"		scb_dt_cmp_ret.totimp, ".
				"		scb_dt_cmp_ret.iva_ret, ".
				"		scb_dt_cmp_ret.totcmp_con_iva, ".
				"		max(cxp_solicitudes.fecemisol) as fecemisol, ".
				"		max(cxp_solicitudes.consol) as descrip ".
				"  FROM scb_dt_cmp_ret, cxp_solicitudes, cxp_dt_solicitudes ".
				"  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."' ".
				"    AND scb_dt_cmp_ret.numcom='".$as_numcom."' ".
				"    AND scb_dt_cmp_ret.codret='0000000003' ".
				"	AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp ".
				//"	AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc ". Comentado Por Carlos Zambrano
				"	AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol ".
				"	AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp ".
				"	AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol ".
				"  GROUP BY scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret,scb_dt_cmp_ret.totcmp_con_iva ";*/
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		//print_r($rs_data);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesmunicipales_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesunoxmil_detfact($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesunoxmil_detfact($as_numcom)
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$arrResultado="";
		$li_montotdoc="";
		$ls_dentipdoc="";
		$ls_codtipdoc="";
		$ls_dencondoc="";
		$ls_dended="";
		$ls_sql="SELECT max(cxp_rd.montotdoc) as montotdoc,".
				"       (SELECT dentipdoc FROM cxp_documento WHERE cxp_rd.codemp=cxp_documento.codemp AND max(cxp_rd.codtipdoc)=cxp_documento.codtipdoc) AS dentipdoc,
				        max(cxp_rd.codtipdoc) AS codtipdoc, max(dencondoc) AS dencondoc,
						(SELECT dended FROM sigesp_deducciones,cxp_rd_deducciones 
						  WHERE cxp_rd.codemp=cxp_rd_deducciones.codemp 
						    AND max(cxp_rd.numrecdoc)=cxp_rd_deducciones.numrecdoc 
						    AND max(cxp_rd.codtipdoc)=cxp_rd_deducciones.codtipdoc 
							AND max(cxp_rd.cod_pro)=cxp_rd_deducciones.cod_pro 
							AND max(cxp_rd.ced_bene)=cxp_rd_deducciones.ced_bene 
							AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp 
							AND cxp_rd_deducciones.codded=sigesp_deducciones.codded
							AND sigesp_deducciones.estretmil='1') AS dended
				   FROM scb_dt_cmp_ret, cxp_rd, cxp_dt_solicitudes
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000005'
					AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp
					AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc
					AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
				  GROUP BY cxp_rd.codemp, scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detfact ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
				$ls_dentipdoc=$row["dentipdoc"];
				$ls_codtipdoc=$row["codtipdoc"];
				$ls_dencondoc=$row["dencondoc"];
				$ls_dended=$row["dended"];
				$arrResultado["montotdoc"]=$li_montotdoc;
				$arrResultado["dentipdoc"]=$ls_dentipdoc;
				$arrResultado["codtipdoc"]=$ls_codtipdoc;
				$arrResultado["dencondoc"]=$ls_dencondoc;
				$arrResultado["dended"]=$ls_dended;
			}
			$this->io_sql->free_result($rs_data);
		}
		
		return $arrResultado;
	}// end function uf_retencionesmunicipales_detfact
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesmunicipales_detalle_solpago($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_detalles
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT max(scb_dt_cmp_ret.codret) as codret,
		                max(scb_dt_cmp_ret.numsop) as numsop,
						max(scb_dt_cmp_ret.fecfac) as fecfac,
						max(scb_dt_cmp_ret.numfac) as numfac,
						max(scb_dt_cmp_ret.numcon) as numcon,
					    scb_dt_cmp_ret.basimp,
						scb_dt_cmp_ret.porimp,
						scb_dt_cmp_ret.totimp,
						scb_dt_cmp_ret.iva_ret,
						scb_dt_cmp_ret.totcmp_con_iva,
						max(cxp_solicitudes.fecemisol) as fecemisol,
						max(cxp_solicitudes.consol) as descrip,sigesp_cmp.fecha
				   FROM scb_dt_cmp_ret, cxp_solicitudes, cxp_dt_solicitudes,sigesp_cmp
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000003'
					AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp
					AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol
					AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
					AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
					AND sigesp_cmp.codemp = cxp_solicitudes.codemp
					AND sigesp_cmp.procede = 'CXPSOP'
					AND sigesp_cmp.comprobante = cxp_solicitudes.numsol
				  GROUP BY scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret,scb_dt_cmp_ret.totcmp_con_iva,sigesp_cmp.fecha ";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		//print_r($rs_data);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detalle_solpago ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_solpago->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesmunicipales_detalle_solpago
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencion1x1000_detalle_solpago($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencion1x1000_detalle_solpago
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: 
		// Fecha Creación: 11/11/2009									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT max(scb_dt_cmp_ret.codret) as codret,
		                max(scb_dt_cmp_ret.numsop) as numsop,
						max(scb_dt_cmp_ret.fecfac) as fecfac,
						max(scb_dt_cmp_ret.numfac) as numfac,
						max(scb_dt_cmp_ret.numcon) as numcon,
					    scb_dt_cmp_ret.basimp,
						scb_dt_cmp_ret.porimp,
						scb_dt_cmp_ret.totimp,
						scb_dt_cmp_ret.iva_ret,
						scb_dt_cmp_ret.totcmp_con_iva,
						max(cxp_solicitudes.fecemisol) as fecemisol,
						max(cxp_solicitudes.consol) as descrip,sigesp_cmp.fecha
				   FROM scb_dt_cmp_ret, cxp_solicitudes, cxp_dt_solicitudes,sigesp_cmp
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000005'
					AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp
					AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol
					AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
					AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
					AND sigesp_cmp.codemp = cxp_solicitudes.codemp
					AND sigesp_cmp.procede = 'CXPSOP'
					AND sigesp_cmp.comprobante = cxp_solicitudes.numsol
				  GROUP BY scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret,scb_dt_cmp_ret.totcmp_con_iva,sigesp_cmp.fecha ";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		//print_r($rs_data);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detalle_solpago ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle_solpago1x1000->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_retencionesmunicipales_detalle_solpago
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_datos_proveedores($as_rif)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_datos_proveedores
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los proveedores.
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 09/10/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_nombre="";
		$ls_sql="SELECT rpc_proveedor. telpro".
				"  FROM rpc_proveedor ".
				" WHERE rpc_proveedor.codemp='".$this->ls_codemp."' ".
				"   AND rpc_proveedor.rifpro='".$as_rif."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_proveedores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_telefono=$row["telpro"];
			}
			if($ls_telefono=="")
			{
				$ls_sql="SELECT telbene ".
						"  FROM rpc_beneficiario ".
						" WHERE rpc_beneficiario.codemp='".$this->ls_codemp."' ".
						"   AND rpc_beneficiario.rifben='".$as_rif."'";
				$rs_data=$this->io_sql->select($ls_sql);
				if($rs_data===false)
				{
					$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_proveedores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
					return false;
				}
				else
				{
					if($row=$this->io_sql->fetch_row($rs_data))
					{
						$ls_telefono=$row["telbene"];
					}
				}
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_telefono;
	}// end function uf_select_proveedores
	//-----------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesmunicipales_monfact($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_monfact
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_montotdoc=0;
		$ls_sql="SELECT max(cxp_rd.montotdoc) as montotdoc
				   FROM scb_dt_cmp_ret, cxp_rd, cxp_dt_solicitudes
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000003'
					AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp
					AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc
					AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
				  GROUP BY scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales_detalles ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montotdoc;
	}// end function uf_retencionesmunicipales_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retenciones1x1000_monfact($as_numcom)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesmunicipales_monfact
		//         Access: public
		//	    Arguments: as_numcom     // Numero de comprobante de iva
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_montotdoc=0;
		$ls_sql="SELECT max(cxp_rd.montotdoc) as montotdoc
				   FROM scb_dt_cmp_ret, cxp_rd, cxp_dt_solicitudes
				  WHERE scb_dt_cmp_ret.codemp='".$this->ls_codemp."'
				    AND scb_dt_cmp_ret.numcom='".$as_numcom."'
				    AND scb_dt_cmp_ret.codret='0000000005'
					AND scb_dt_cmp_ret.codemp=cxp_dt_solicitudes.codemp
					AND scb_dt_cmp_ret.numfac=cxp_dt_solicitudes.numrecdoc
					AND scb_dt_cmp_ret.numsop=cxp_dt_solicitudes.numsol
					AND cxp_dt_solicitudes.codemp=cxp_rd.codemp
					AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc
					AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
					AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
					AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
				  GROUP BY scb_dt_cmp_ret.codemp, scb_dt_cmp_ret.numfac, scb_dt_cmp_ret.porimp,scb_dt_cmp_ret.basimp,scb_dt_cmp_ret.totimp,scb_dt_cmp_ret.iva_ret";
		$rs_data=$this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retenciones1x1000_monfact ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_montotdoc;
	}// end function uf_retenciones1x1000_monfact
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_arc_cabecera($as_coddes,$as_codhas,$as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_arc_cabecera
		//         Access: public
		//      Argumento: as_coddes // codigo del proveedor ó beneficario desde
		//				   as_codhas // codigo del proveedor ó beneficario hasta
		//				   as_tipo // Si buscamos proveedores, beneficiarios ó ambos
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos de los proveedores ó beneficarios que tiene deducciones de ARC
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$lb_valido=true;
		switch($as_tipo)
		{
			case "P": // si es un proveedor
				$ls_codprodes=$as_coddes;
				$ls_codprohas=$as_codhas;
				$ls_cedbendes="";
				$ls_cedbenhas="";
				$ls_criterio=$ls_criterio." AND cxp_rd.tipproben='".$as_tipo."'";
				break;

			case "B": // si es un beneficiario
				$ls_codprodes="";
				$ls_codprohas="";
				$ls_cedbendes=$as_coddes;
				$ls_cedbenhas=$as_codhas;
				$ls_criterio=$ls_criterio." AND cxp_rd.tipproben='".$as_tipo."'";
				break;

			case "": // si son todos
				$ls_codprodes="";
				$ls_codprohas="";
				$ls_cedbendes="";
				$ls_cedbenhas="";
				break;
		}
		if($ls_codprodes!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.cod_pro>='".$ls_codprodes."'";
		}
		if($ls_codprohas!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.cod_pro<='".$ls_codprohas."'";
		}
		if($ls_cedbendes!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.ced_bene>='".$ls_cedbendes."'";
		}
		if($ls_cedbenhas!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.ced_bene<='".$ls_cedbenhas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT MAX(cxp_rd.tipproben) AS tipproben, MAX(rpc_proveedor.nompro) AS nompro, ".
				"		MAX(rpc_proveedor.nacpro) AS nacpro, MAX(rpc_proveedor.rifpro) AS rifpro, ".
				"		MAX(rpc_proveedor.nitpro) AS nitpro, MAX(rpc_proveedor.dirpro) AS dirpro, ".
				"       MAX(rpc_proveedor.telpro) AS telpro, MAX(rpc_beneficiario.nombene) AS nombene, ".
				"		MAX(rpc_beneficiario.apebene) AS apebene, MAX(rpc_beneficiario.nacben) AS nacben, ".
				"		MAX(rpc_beneficiario.ced_bene) AS ced_bene, MAX(rpc_beneficiario.numpasben) AS numpasben, ".
				"       MAX(rpc_beneficiario.dirbene) AS dirbene, MAX(rpc_beneficiario.telbene) AS telbene, ".
				"       MAX(cxp_rd.cod_pro) AS cod_pro, MAX(cxp_rd.ced_bene) AS ced_bene ".
				"  FROM cxp_rd, cxp_rd_deducciones, sigesp_deducciones, rpc_proveedor, rpc_beneficiario ".$ls_filtrofrom.
				" WHERE sigesp_deducciones.islr = 1 ".
				$ls_criterio.$ls_filtroest.
				"   AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"   AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
				"   AND cxp_rd.codemp = rpc_proveedor.codemp ".
				"   AND cxp_rd.cod_pro = rpc_proveedor.cod_pro ".
				"   AND cxp_rd.codemp = rpc_beneficiario.codemp ".
				"   AND cxp_rd.ced_bene = rpc_beneficiario.ced_bene ".
				" GROUP BY cxp_rd.cod_pro, cxp_rd.ced_bene ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_arc_cabecera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_arc_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_arc_detalle($as_codigo,$as_tipo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_arc_detalle
		//         Access: public
		//      Argumento: $as_codigo // codigo del proveedor ó beneficario desde
		//				   $as_tipo // Si buscamos proveedores, beneficiarios ó ambos
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene el detalle del arc dado el proveedor ó beneficario
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$lb_valido=true;
		switch($as_tipo)
		{
			case "P": // si es un proveedor
				$ls_criterio=$ls_criterio." AND cxp_rd.cod_pro='".$as_codigo."'";
				$ls_criterio=$ls_criterio." AND cxp_rd.tipproben='".$as_tipo."'";
				break;

			case "B": // si es un beneficiario
				$ls_criterio=$ls_criterio." AND cxp_rd.ced_bene='".$as_codigo."'";
				$ls_criterio=$ls_criterio." AND cxp_rd.tipproben='".$as_tipo."'";
				break;
		}
		$ls_sql="SELECT cxp_rd.fecemidoc, cxp_rd.montotdoc, cxp_rd_deducciones.monobjret, ".
				"		cxp_rd_deducciones.porded, cxp_rd_deducciones.monret, cxp_rd_deducciones.cod_pro ".
				"  FROM cxp_rd, cxp_rd_deducciones, sigesp_deducciones ".
				" WHERE sigesp_deducciones.islr = 1 ".
				$ls_criterio.
				"   AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"   AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
				"	AND cxp_rd.estprodoc <> 'A' ".
				" ORDER BY cxp_rd.fecemidoc";
		
		//Ajuste por Carlos Zambrano agregando ------->    "	AND cxp_rd.estprodoc <> 'A' ".
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_arc_detalle ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detalle->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_arc_detalle
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_probenrelacionfacturas($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_probenrelacionfacturas
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de proveedor
		//				   as_codprobendes  // Codigo proveedor/beneficiario Desde
		//				   as_codprobenhas  // Codigo proveedor/beneficiario Hasta
		//				   ad_fecregdes     // Fecha de registro Desde
		//				   ad_fecreghas     // Fecha de registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca los proveedores/beneficiarios que tienen facturas asociadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 02/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		if ((!empty($as_codprobendes))&&(!empty($as_codprobenhas)))
		{
			switch($as_tipproben)
			{
				case "P":
					$ls_criterio=" AND cxp_rd.cod_pro>='".$as_codprobendes."'".
								 " AND cxp_rd.cod_pro<='".$as_codprobenhas."'".
								 " AND cxp_rd.ced_bene='----------'";
				break;
				case "B":
					$ls_criterio=" AND cxp_rd.ced_bene>='".$as_codprobendes."'".
								 " AND cxp_rd.ced_bene<='".$as_codprobenhas."'".
								 " AND cxp_rd.cod_pro='----------'";
				break;
			}
		}
		if(!empty($as_numsol))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_dt_solicitudes.numsol='".$as_numsol."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT  DISTINCT(CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.cod_pro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.ced_bene ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS codigo, ".
				" (CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre,cxp_rd.tipproben ".
				"  FROM cxp_rd,cxp_dt_solicitudes ".
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".$ls_filtroest.
				" ".$ls_criterio." ";
				" GROUP BY codigo,cxp_rd.codemp,cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.tipproben".
				" ORDER BY codigo";
		//print $ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_probenrelacionfacturas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_probenrelacionfacturas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_facturasproben($as_tipproben,$as_codigo,$ad_fecregdes,$ad_fecreghas,$ai_ordendoc,$ai_ordenfec,$as_numsol2)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_probenrelacionfacturas
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de proveedor
		//				   as_codigo     // Codigo proveedor/beneficiario
		//				   ad_fecregdes  // Fecha de registro Desde
		//				   ad_fecreghas  // Fecha de registro Hasta
		//				   ai_ordendoc   // Indica si se desea ordenar por documento
		//				   ai_ordenfec   // Indica si se desea ordenar por fecha de Registro
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca los las facturas asociadas a un proveedor/beneficiario
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_order="";
		$this->ds_detrecdoc = new class_datastore();
		if($as_tipproben=="P")
		{
			$ls_criterio=" AND cxp_rd.cod_pro='".$as_codigo."'".
						 " AND cxp_rd.ced_bene='----------'";
		}
		else
		{
			$ls_criterio=" AND cxp_rd.ced_bene='".$as_codigo."'".
						 " AND cxp_rd.cod_pro='----------'";
		}
		if(!empty($as_numsol2))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_dt_solicitudes.numsol='".$as_numsol2."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}
		if($ai_ordendoc==1)
		{
			$ls_order=" ORDER BY cxp_rd.numrecdoc";
		}
		if($ai_ordenfec==1)
		{
			if($ls_order=="")
			{
				$ls_order=" ORDER BY cxp_rd.fecregdoc";
			}
			else
			{
				$ls_order=$ls_order.", cxp_rd.fecregdoc";
			}
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd.codestpro1','cxp_rd.codestpro2','cxp_rd.codestpro3','cxp_rd.codestpro4','cxp_rd.codestpro5','cxp_rd.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) 
			                  AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos 
			                  							        WHERE sss_permisos_internos.codemp='{$this->ls_codemp}'".
				"                                				  AND codsis='CXP'".
				"                                                 AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_rd.numrecdoc, cxp_rd.fecregdoc, cxp_rd.fecemidoc, cxp_rd.dencondoc,".
				" 		cxp_rd.montotdoc,cxp_rd.moncardoc,cxp_rd.mondeddoc,cxp_dt_solicitudes.numsol".
				"  FROM cxp_rd,cxp_dt_solicitudes ".
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				" ".$ls_criterio." ".
				" ".$ls_order." ";
		//print $ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_probenrelacionfacturas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detrecdoc->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_facturasproben
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_relacionsolicitudes($as_numsoldes,$as_numsolhas,$ad_fecemides,$ad_fecemihas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_probenrelacionfacturas
		//         Access: public
		//	    Arguments: as_numsoldes  // Numero de solicitud de orden de pago desde
		//				   as_numsolhas  // Numero de solicitud de orden de pago hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca los las solicitudes de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$this->ds_detsolicitudes = new class_datastore();
		if(!empty($as_numsoldes))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol>='".$as_numsoldes."'";
		}
		if(!empty($as_numsolhas))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol<='".$as_numsolhas."'";
		}
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$this->io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$this->io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecemihas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest1 = '';
		$ls_filtroest2 = '';
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		$ls_sql="SELECT cxp_solicitudes.numsol,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"       cxp_solicitudes.fecemisol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				" ".$ls_criterio." ".$ls_filtroest.
				" ORDER BY cxp_solicitudes.numsol";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_relacionsolicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_relacionsolicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesprobensaldos($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecemides,$ad_fecemihas)
	{
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesprobensaldos
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecemides     // Fecha de Emision Desde
		//                 ad_fecemihas     // Fecha de Emision Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca los proveedores/beneficiarios con solicitudes de pago asociadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$this->io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$this->io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecemihas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest1 = '';
		$ls_filtroest2 = '';
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		$ls_sql="SELECT cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene,cxp_solicitudes.tipproben, ".
				"   MAX((CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END )) AS nombre, ".
				"   MAX((CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.cod_pro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.ced_bene ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END )) AS codigo ".
				"  FROM cxp_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND (cxp_solicitudes.estprosol='E'".
				"    OR cxp_solicitudes.estprosol='C'".
				"    OR cxp_solicitudes.estprosol='S')".
				"   ".$ls_criterio." ".$ls_filtroest.
				" GROUP BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.tipproben";
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesprobensaldos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_solicitudesprobensaldos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionsaldos($as_tipproben,$as_codproben,$ad_fecemides,$ad_fecemihas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesprobensaldos
		//         Access: public
		//	    Arguments: as_tipproben // Tipo de Proveedor/Beneficiario
		//                 as_codproben // Codigo de Proveedor/Beneficiario Desde
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las solicitudes de pago asociadas a proveedores/beneficiarios
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$this->ds_detsolicitudes = new class_datastore();
		$lb_valido=true;
		$ls_criterio="";
		if($as_tipproben=="P")
		{
			$ls_criterio=$ls_criterio." AND cxp_solicitudes.cod_pro='".$as_codproben."'".
									  " AND cxp_solicitudes.ced_bene='----------'".
									  " AND cxp_solicitudes.tipproben='P'";
		}
		else
		{
			$ls_criterio=$ls_criterio." AND cxp_solicitudes.ced_bene='".$as_codproben."'".
									  " AND cxp_solicitudes.cod_pro='----------'".
									  " AND cxp_solicitudes.tipproben='B'";
		}
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$this->io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$this->io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecemihas."'";
		}

		$ls_sql="SELECT cxp_solicitudes.fecemisol, cxp_solicitudes.consol,cxp_solicitudes.numsol,".
				"       cxp_solicitudes.monsol".
				"  FROM cxp_solicitudes".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio." ".
				"   AND (cxp_solicitudes.estprosol='E'".
				"    OR cxp_solicitudes.estprosol='C'".
				"    OR cxp_solicitudes.estprosol='S')".
				" ORDER BY cxp_solicitudes.fecemisol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionsaldos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$this->ds_detsolicitudes->data=$this->io_sql->obtener_datos($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}// end function uf_select_informacionsaldos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionndnc($as_tipproben,$as_codproben,$ad_fecemides,$ad_fecemihas,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionndnc
		//         Access: public
		//	    Arguments: as_tipproben // Tipo de Proveedor/Beneficiario
		//                 as_codproben // Codigo de Proveedor/Beneficiario Desde
		//                 ad_fecemides // Fecha de Emision Desde
		//                 ad_fecemihas // Fecha de Emision
		//                 as_numsol    // Numero de la Solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las notas de Debito/Credito de una Solicitud de Pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 26/08/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if($as_tipproben=="P")
		{
			$ls_criterio=$ls_criterio." AND cxp_sol_dc.cod_pro='".$as_codproben."'".
									  " AND cxp_sol_dc.ced_bene='----------'";
		}
		else
		{
			$ls_criterio=$ls_criterio." AND cxp_sol_dc.ced_bene='".$as_codproben."'".
									  " AND cxp_sol_dc.cod_pro='----------'";
		}
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$this->io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.fecope>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$this->io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.fecope<='".$ad_fecemihas."'";
		}
		if(!empty($as_numsol))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.numsol='".$as_numsol."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_dt_solicitudes.codemp = cxp_sol_dc.codemp ".
							" AND cxp_dt_solicitudes.numsol = cxp_sol_dc.numsol ".
							" AND cxp_dt_solicitudes.numrecdoc = cxp_sol_dc.numrecdoc "; 
			$ls_filtrofrom = " , cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_sol_dc.numsol,cxp_sol_dc.monto, cxp_sol_dc.codope,cxp_sol_dc.numdc,".
				"		cxp_sol_dc.fecope,cxp_sol_dc.desope".
				"  FROM cxp_sol_dc".$ls_filtrofrom.
				" WHERE cxp_sol_dc.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				"   AND cxp_sol_dc.estnotadc='C'";
		$this->rs_ndnc=$this->io_sql->select($ls_sql);
		if($this->rs_ndnc===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionndnc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_informacionndnc
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionpagos($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionpagos
		//         Access: public
		//	    Arguments: as_numsol // Numero de Solicitud de Pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los Pagos de las solicitudes indicadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_monto=0;
		$ls_sql="SELECT SUM(monto) AS monto".
				"  FROM cxp_sol_banco".
				" WHERE cxp_sol_banco.codemp='".$this->ls_codemp."' ".
				"   AND cxp_sol_banco.numsol='".$as_numsol."'".
				"   AND cxp_sol_banco.estmov <> 'A'".
				"   AND cxp_sol_banco.estmov <> 'O'".
				" GROUP BY cxp_sol_banco.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_monto=$row["monto"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_monto;
	}// end function uf_select_informacionpagos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacioncheques($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacioncheques
		//         Access: public
		//	    Arguments: as_numsol // Numero de Solicitud de Pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los Pagos de las solicitudes indicadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_numdoc=0;
		$li_i=0;
		$ls_sql="SELECT numdoc".
				"  FROM cxp_sol_banco".
				" WHERE cxp_sol_banco.codemp='".$this->ls_codemp."' ".
				"   AND cxp_sol_banco.numsol='".$as_numsol."'".
				"   AND cxp_sol_banco.estmov <> 'A'".
				"   AND cxp_sol_banco.estmov <> 'O'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacioncheques ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				if($li_i<1)
					$ls_numdoc=$row["numdoc"];
				else
					$ls_numdoc=$ls_numdoc." - ".$row["numdoc"];
				$li_i++;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_numdoc;
	}// end function uf_select_informacionpagos
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_select_fechapagos($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_fechapagos
		//         Access: public
		//	    Arguments: as_numsol // Numero de Solicitud de Pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los Pagos de las solicitudes indicadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_fecmov="";
		$ls_sql="SELECT MAX(fecmov) AS fecmov".
				"  FROM cxp_sol_banco,scb_movbco".
				" WHERE cxp_sol_banco.codemp='".$this->ls_codemp."' ".
				"   AND cxp_sol_banco.numsol='".$as_numsol."'".
				"   AND cxp_sol_banco.estmov <> 'A'".
				"   AND cxp_sol_banco.estmov <> 'O'".
				"   AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"   AND cxp_sol_banco.codban=scb_movbco.codban".
				"   AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"   AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				" GROUP BY cxp_sol_banco.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_fecmov=$row["fecmov"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_fecmov;
	}// end function uf_select_informacionpagos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_relacionndnc($as_tipndnc,$as_numsoldes,$as_numsolhas,$as_ndncdes,$ad_ndnchas,
								    $ad_fecregdes,$ad_fecreghas,$ai_emitida,$ai_contabilizada,$ai_anulada)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_relacionndnc
		//         Access: public
		//	    Arguments: $as_tipndnc   // Tipo de nota Debito/Credito
		//                 $as_numsoldes // Numero de Solicitud Desde
		//                 $as_numsolhas // Numero de Solicitud Hasta
		//                 $as_ndncdes   // Numero de Nota Debito/Credito Desde
		//                 $ad_ndnchas   // Numero de Nota Debito/Credito Hasta
		//                 $ad_fecregdes // Fecha de Registro Desde
		//                 $ad_fecreghas // Fecha de Registro Hasta
		//                 $ai_emitida   // Estatus de Nota Emitida
		//                 $ai_contabilizada // Estatus de Nota Contabilizada
		//                 $ai_anulada   // Estatus de Nota Anulada
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las notas de Debito / Credito
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$lb_valido=true;
		if(!empty($as_numsoldes))
		{
			$ls_criterio= $ls_criterio."   AND cxp_sol_dc.numsol>='".$as_numsoldes."'";
		}
		if(!empty($as_numsolhas))
		{
			$ls_criterio= $ls_criterio."   AND cxp_sol_dc.numsol<='".$as_numsolhas."'";
		}
		if(!empty($as_ndncdes))
		{
			$ls_criterio= $ls_criterio."   AND cxp_sol_dc.numdc>='".$as_ndncdes."'";
		}
		if(!empty($ad_ndnchas))
		{
			$ls_criterio= $ls_criterio."   AND cxp_sol_dc.numdc<='".$ad_ndnchas."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.fecope>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.fecope<='".$ad_fecreghas."'";
		}
		switch($as_tipndnc)
		{
			case "D":
				$ls_criterio=$ls_criterio."AND cxp_sol_dc.codope='D'";
			break;
			case "C":
				$ls_criterio=$ls_criterio."AND cxp_sol_dc.codope='C'";
			break;
		}
		if(($ai_emitida==1)||($ai_contabilizada==1)||($ai_anulada==1))
		{
			$lb_anterior=false;
			if($ai_emitida==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_sol_dc.estnotadc='E'";
					$lb_anterior=true;
				}
			}
			if($ai_contabilizada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_sol_dc.estnotadc='C'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_sol_dc.estnotadc='C'";
				}
			}
			if($ai_anulada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_sol_dc.estnotadc='A'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_sol_dc.estnotadc='A'";
				}
			}
			if($lb_anterior)
			{
				$ls_criterio=$ls_criterio.")";
			}
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest1 = '';
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_dc_spg.codestpro','cxp_dc_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_dc_spg.codemp = cxp_sol_dc.codemp ".
							" AND cxp_dc_spg.numrecdoc = cxp_sol_dc.numrecdoc ".
							" AND cxp_dc_spg.codtipdoc = cxp_sol_dc.codtipdoc ".
							" AND cxp_dc_spg.ced_bene = cxp_sol_dc.ced_bene ".
							" AND cxp_dc_spg.cod_pro = cxp_sol_dc.cod_pro ";
			$ls_filtrofrom = " ,cxp_dc_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		$ls_sql="SELECT cxp_sol_dc.numsol, cxp_sol_dc.numdc, cxp_sol_dc.codtipdoc, cxp_sol_dc.numrecdoc, cxp_sol_dc.fecope,".
				"       cxp_sol_dc.codope, cxp_sol_dc.desope, cxp_sol_dc.monto, cxp_sol_dc.estapr, cxp_sol_dc.estnotadc,".
				"		cxp_documento.dentipdoc,".
				"		(CASE cxp_sol_dc.ced_bene WHEN '----------' THEN (SELECT rpc_proveedor.nompro ".
				"                                        				    FROM rpc_proveedor ".
				"                                       				   WHERE rpc_proveedor.codemp=cxp_sol_dc.codemp ".
				"                                         					 AND rpc_proveedor.cod_pro=cxp_sol_dc.cod_pro) ".
				"                       		  ELSE (SELECT ".$ls_cadena." ".
				"                                         FROM rpc_beneficiario ".
				"                                        WHERE rpc_beneficiario.codemp=cxp_sol_dc.codemp ".
				"                                          AND rpc_beneficiario.ced_bene=cxp_sol_dc.ced_bene) END) AS nombre ".
				"  FROM cxp_sol_dc,cxp_documento".$ls_filtrofrom.
				" WHERE cxp_sol_dc.codemp='".$this->ls_codemp."' ".
				" ".$ls_criterio." ".$ls_filtroest.
				"   AND cxp_sol_dc.codtipdoc=cxp_documento.codtipdoc";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_relacionndnc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_relacionndnc
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_select_report_libcompra($ld_fecdesde,$ld_fechasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_spg_nota
		//		   Access: public
		//	  Description:
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('DRD.codestpro','DRD.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND DRD.codemp = RD.codemp ".
							" AND DRD.numrecdoc = RD.numrecdoc ".
							" AND DRD.codtipdoc = RD.codtipdoc ".
							" AND DRD.ced_bene = RD.ced_bene ".
							" AND DRD.cod_pro = RD.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg DRD";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT RD.numrecdoc,RD.tipproben,RD.cod_pro,RD.ced_bene,MAX(RD.montotdoc) AS montotdoc,".
				"       MAX(RD.mondeddoc) AS mondeddoc,RD.codtipdoc,MAX(RD.fecemidoc) AS fecemidoc,MAX(RD.numref) AS numref, MAX(RDDED.porded) AS porded,".
//				"       SUM(COALESCE(RDDED.monret,0)) as monret,MAX(DED.iva) AS iva".
				"       MAX((SELECT cxp_rd_deducciones.monret".
				"		   FROM cxp_rd_deducciones".
				"         WHERE RD.numrecdoc=cxp_rd_deducciones.numrecdoc".
				"           AND RD.codtipdoc=cxp_rd_deducciones.codtipdoc".
				"           AND RD.cod_pro=cxp_rd_deducciones.cod_pro".
				"           AND RD.ced_bene=cxp_rd_deducciones.ced_bene".
				"           AND DED.codded=cxp_rd_deducciones.codded".
				"           AND DED.iva=1)) AS monret,MAX(DED.iva) AS iva".
				"  FROM sigesp_deducciones DED,cxp_rd RD".
				"  LEFT OUTER JOIN cxp_rd_deducciones RDDED".
				"    ON RD.numrecdoc=RDDED.numrecdoc".
				"   AND RD.codtipdoc=RDDED.codtipdoc".
				"   AND RD.cod_pro=RDDED.cod_pro".
				"   AND RD.ced_bene=RDDED.ced_bene ".$ls_filtrofrom.
				" WHERE RD.codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND estlibcom=1".
				"   AND fecregdoc between '".$ld_fecdesde."' AND '".$ld_fechasta."'".
				//"   AND DED.codded=RDDED.codded".
				"   AND RD.estprodoc='C'".$ls_filtroest.
				" GROUP BY RD.numrecdoc,RD.tipproben,RD.cod_pro,RD.ced_bene,RD.codtipdoc".
				" ORDER BY MAX(fecemidoc)";//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_reportLibCompra ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->io_sql->num_rows($rs_data)>0)
			{
			//	mysql_data_seek( $rs_data,0);//Devuelvo el puntero al comienzo
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			//$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		return $arrResultado;
	}

	function uf_select_report_libcompra_ocamar($ld_fecdesde,$ld_fechasta,$as_filtro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_spg_nota
		//		   Access: public
		//	  Description:
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('DRD.codestpro','DRD.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND DRD.codemp = RD.codemp ".
							" AND DRD.numrecdoc = RD.numrecdoc ".
							" AND DRD.codtipdoc = RD.codtipdoc ".
							" AND DRD.ced_bene = RD.ced_bene ".
							" AND DRD.cod_pro = RD.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg DRD";
		}
		if($as_filtro=="1")
		{
			$ls_filtro=	"   AND fecemidoc between '".$ld_fecdesde."' AND '".$ld_fechasta."'";
		}
		else
		{
			$ls_filtro=	"   AND fecemidoc < '".$ld_fecdesde."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT RD.numrecdoc,RD.tipproben,RD.cod_pro,RD.ced_bene,MAX(RD.montotdoc) AS montotdoc,".
				"       MAX(RD.mondeddoc) AS mondeddoc,RD.codtipdoc,MAX(RD.fecemidoc) AS fecemidoc,MAX(RD.numref) AS numref, MAX(RDDED.porded) AS porded,".
//				"       SUM(COALESCE(RDDED.monret,0)) as monret,MAX(DED.iva) AS iva".
				"       MAX((SELECT cxp_rd_deducciones.monret".
				"		   FROM cxp_rd_deducciones".
				"         WHERE RD.numrecdoc=cxp_rd_deducciones.numrecdoc".
				"           AND RD.codtipdoc=cxp_rd_deducciones.codtipdoc".
				"           AND RD.cod_pro=cxp_rd_deducciones.cod_pro".
				"           AND RD.ced_bene=cxp_rd_deducciones.ced_bene".
				"           AND DED.codded=cxp_rd_deducciones.codded".
				"           AND DED.iva=1)) AS monret,MAX(DED.iva) AS iva".
				"  FROM sigesp_deducciones DED,cxp_rd RD".
				"  LEFT OUTER JOIN cxp_rd_deducciones RDDED".
				"    ON RD.numrecdoc=RDDED.numrecdoc".
				"   AND RD.codtipdoc=RDDED.codtipdoc".
				"   AND RD.cod_pro=RDDED.cod_pro".
				"   AND RD.ced_bene=RDDED.ced_bene ".$ls_filtrofrom.
				" WHERE RD.codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND estlibcom=1".
				"   AND fecregdoc between '".$ld_fecdesde."' AND '".$ld_fechasta."'".
				//"   AND DED.codded=RDDED.codded".
				"   AND RD.estprodoc='C'".$ls_filtroest.$ls_filtro.
				" GROUP BY RD.numrecdoc,RD.tipproben,RD.cod_pro,RD.ced_bene,RD.codtipdoc".
				" ORDER BY MAX(fecemidoc)";//print $ls_sql."<br><br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_reportLibCompra ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->io_sql->num_rows($rs_data)>0)
			{
			//	mysql_data_seek( $rs_data,0);//Devuelvo el puntero al comienzo
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			//$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		return $arrResultado;
	}
	function uf_select_report_libcompra_ind($ld_fecdesde,$ld_fechasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_spg_nota
		//		   Access: public
		//	  Description:
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_sql="SELECT RD.numrecdoc,RD.tipproben,RD.cod_pro,RD.ced_bene,MAX(RD.montotdoc) AS montotdoc,".
				"       MAX(RD.mondeddoc) AS mondeddoc,RD.codtipdoc,MAX(RD.fecemidoc) AS fecemidoc,MAX(RD.numref) AS numref,".
//				"       SUM(COALESCE(RDDED.monret,0)) as monret,MAX(DED.iva) AS iva".
				"       MAX((SELECT cxp_rd_deducciones.monret".
				"		   FROM cxp_rd_deducciones".
				"         WHERE RD.numrecdoc=cxp_rd_deducciones.numrecdoc".
				"           AND RD.codtipdoc=cxp_rd_deducciones.codtipdoc".
				"           AND RD.cod_pro=cxp_rd_deducciones.cod_pro".
				"           AND RD.ced_bene=cxp_rd_deducciones.ced_bene".
				"           AND DED.codded=cxp_rd_deducciones.codded".
				"           AND DED.iva=1)) AS monret,MAX(DED.iva) AS iva".
				"  FROM cxp_solicitudes, cxp_dt_solicitudes,sigesp_deducciones DED,cxp_rd RD".
				"  LEFT OUTER JOIN cxp_rd_deducciones RDDED".
				"    ON RD.numrecdoc=RDDED.numrecdoc".
				"   AND RD.codtipdoc=RDDED.codtipdoc".
				"   AND RD.tipproben='P'".
				"   AND RD.cod_pro=RDDED.cod_pro".
				"   AND RD.ced_bene=RDDED.ced_bene".
				" WHERE RD.codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND estlibcom=1".
				"   AND cxp_solicitudes.fecemisol between '".$ld_fecdesde."' AND '".$ld_fechasta."'".
				"   AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=RD.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=RD.numrecdoc".
				"   AND RD.estprodoc='C'".
				" GROUP BY RD.numrecdoc,RD.tipproben,RD.cod_pro,RD.ced_bene,RD.codtipdoc".
				" ORDER BY MAX(fecemidoc)";//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{print $this->io_sql->message;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_reportLibCompra ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->io_sql->num_rows($rs_data)>0)
			{
			//	mysql_data_seek( $rs_data,0);//Devuelvo el puntero al comienzo
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			//$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		return $arrResultado;
	}


	function uf_select_data($io_sql,$ls_cadena,$ls_campo)
	{//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_data
		//		   Access: public
		//	  Description: Devuelve el valor del campo enviado como parametro
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$data=$io_sql->select($ls_cadena);
		if($row=$io_sql->fetch_row($data))
		{
			$ls_result=$row[$ls_campo];
			$io_sql->free_result($data);
		}
		else
		{
			$ls_result="";
		}
		return $ls_result;
	}

	function uf_select_rowdata($io_sql,$ls_cadena)
	{//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_rowdata
		//		   Access: public
		//	  Description: Devuelve la fila resultante del select realizado
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$data=$io_sql->select($ls_cadena);
		if($row=$io_sql->fetch_row($data))
		{
			$la_result=$row;
			$io_sql->free_result($data);
		}
		else
		{
			$la_result=array();
		}
		return $la_result;
	}
	
	function uf_select_arraydata($io_sql,$ls_cadena)//Agregado por Ofimatica de Venezuela el 23-05-2011
	{//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_arraydata
		//		   Access: public
		//	  Description: Devuelve el arreglo resultante del select realizado
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data=$io_sql->select($ls_cadena);
		$la_result=$this->io_sql->obtener_datos($rs_data);
		if(!$rs_data===false)
		{
			$io_sql->free_result($rs_data);
		}
		return $la_result;
	}

	function uf_select_rsdata($ls_cadena,$rs_data)
	{//
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_rsdata
		//		   Access: public
		//	  Description: Devuelve el resultset obtenido de la consulta
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data=$this->io_sql->select($ls_cadena);
		if($rs_data===false)
		{
			$this->io_mensajes->message(" ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->io_sql->num_rows($rs_data)>0)
			{
				mysql_data_seek($rs_data,0);//Devuelvo el puntero al comienzo
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			//$this->io_sql->free_result($rs_data);
		}
		return $lb_valido;
	}

	function uf_buscar_asientomanual($ls_numrecdoc,$ls_codtipdoc,$ls_cedbene,$ls_codpro)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_asientomnual
		//		   Access: public
		//	  Description:
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		 $ls_sql="SELECT monto
		 		  FROM cxp_rd_scg
				  WHERE codemp='".$ls_codemp."'    AND numrecdoc='".$ls_numrecdoc."' AND codtipdoc='".$ls_codtipdoc."'
				  AND 	ced_bene='".$ls_cedbene."' AND cod_pro='".$ls_codpro."'      AND estasicon='M' AND debhab='H'";
		 $ldec_monto = $this->uf_select_data($this->io_sql,$ls_sql,"monto");
		 if($ldec_monto=="")
		 {
		 	$ldec_monto=0;
		 }
		 return $ldec_monto;
	}

	function uf_select_dt_spg_nota($ls_numnota,$ls_numord,$ls_numrecdoc,$ls_codtipdoc,$ls_tipproben,$ls_codproben,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_spg_nota
		//		   Access: public
		//	  Description:
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($ls_tipproben=='P')
		{
			$ls_aux=" AND a.cod_pro='".$ls_codproben."' ";
		}
		else
		{
			$ls_aux=" AND a.ced_bene='".$ls_codproben."' ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('a.codestpro','a.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			                "  						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
							"          				   AND codsis='CXP' ".
				            "                          AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1)" .
							" AND a.codemp = cxp_rd.codemp ".
							" AND a.numrecdoc = cxp_rd.numrecdoc ".
							" AND a.codtipdoc = cxp_rd.codtipdoc ".
							" AND a.ced_bene = cxp_rd.ced_bene ".
							" AND a.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " , cxp_rd ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_aux_codestpro = $this->io_conexion->Concat('b.codestpro1','b.codestpro2','b.codestpro3','b.codestpro4','b.codestpro5');
		$ls_sql="SELECT a.spg_cuenta,a.monto,a.codestpro,b.denominacion".
				"  FROM cxp_dc_spg a,spg_cuentas b".$ls_filtrofrom.
				" WHERE a.codemp='".$_SESSION["la_empresa"]["codemp"]."'".
				"   AND a.numdc='".$ls_numnota."'".
				"   AND a.numsol='".$ls_numord."'".
				"   AND a.numrecdoc='".$ls_numrecdoc."'".
				"   AND a.codtipdoc='".$ls_codtipdoc."'".
				"   AND a.codestpro=".$ls_aux_codestpro."".
				"   AND a.spg_cuenta=b.spg_cuenta".
				"   AND a.codemp=b.codemp ".$ls_aux."".$ls_filtroest.
				" ORDER BY a.codestpro,a.spg_cuenta";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_dt_spg_nota ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->io_sql->num_rows($rs_data)>0)
			{
//				mysql_data_seek($rs_data,0);//Devuelvo el puntero al comienzo
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			//$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		return $arrResultado;

	}

	function uf_select_dt_scg_nota($ls_numnota,$ls_numord,$ls_numrecdoc,$ls_codtipdoc,$ls_tipproben,$ls_codproben,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_dt_scg_nota
		//		   Access: public
		//	  Description:
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 11/03/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($ls_tipproben=='P')
		{
			$ls_aux=" AND a.cod_pro='".$ls_codproben."' ";
		}
		else
		{
			$ls_aux=" AND a.ced_bene='".$ls_codproben."' ";
		}

		$ls_sql="SELECT a.*,b.denominacion
				 FROM cxp_dc_scg a,scg_cuentas b
				 WHERE a.codemp='".$_SESSION["la_empresa"]["codemp"]."' AND a.numdc='".$ls_numnota."' AND a.numsol='".$ls_numord."'
				 AND a.numrecdoc='".$ls_numrecdoc."' AND a.codtipdoc='".$ls_codtipdoc."'
				 AND a.sc_cuenta=b.sc_cuenta AND a.codemp=b.codemp ".$ls_aux."
				 ORDER BY a.debhab,a.sc_cuenta";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_dt_spg_nota ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->io_sql->num_rows($rs_data)>0)
			{
//				mysql_data_seek( $rs_data,0);//Devuelvo el puntero al comienzo
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			//$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		return $arrResultado;

	}
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesactualescxp($as_tipproben,$as_cedbene,$as_codpro,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesactualescxp
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pagos  en el intervalo indicado
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$ls_criterio="";
		$this->ds_solactuales = new class_datastore();
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql=" SELECT cxp_solicitudes.numsol,cxp_solicitudes.consol,cxp_solicitudes.monsol, cxp_solicitudes.estprosol AS estprodoc,".
				"		 cxp_solicitudes.fecemisol AS fecha ".
				"   FROM cxp_solicitudes".$ls_filtrofrom.
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"	 AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."' ".$ls_filtroest.
				"    AND (cxp_solicitudes.estprosol='C' OR cxp_solicitudes.estprosol='S' OR cxp_solicitudes.estprosol='P')".
				" ".$ls_criterio." ".
				" ORDER BY cxp_solicitudes.ced_bene, cxp_solicitudes.cod_pro,cxp_solicitudes.fecemisol ";
		$this->rs_solicitudes=$this->io_sql->select($ls_sql);
		if ($this->rs_solicitudes===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesactualescxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_solicitudes->EOF)
			{
	//			$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_solicitudesactualescxp
	//-----------------------------------------------------------------------------------------------------------------------------------

	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_recepciones_relacionadas($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_recepciones_relacionadas
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pagos  en el intervalo indicado
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_numrecaso="";
		$ls_sql=" SELECT numrecdoc ".
				"   FROM cxp_dt_solicitudes".
				"  WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"	 AND cxp_dt_solicitudes.numsol='".$as_numsol."'";
		$this->rs_rd=$this->io_sql->select($ls_sql);
		if ($this->rs_rd===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_recepciones_relacionadas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			while(!$this->rs_rd->EOF)
			{
				if($ls_numrecaso=="")
				{
					$ls_numrecaso=trim($this->rs_rd->fields["numrecdoc"]);
				}
				else
				{
					$ls_numrecaso=$ls_numrecaso.", ".trim($this->rs_rd->fields["numrecdoc"]);
				}
				$this->rs_rd->MoveNext();
			}
		}
		return $ls_numrecaso;
	}// end function uf_select_solicitudesactualescxp
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionpagoscxp($as_tipproben,$as_cedbene,$as_codpro,$ad_fecregdes,$ad_fecreghas,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionpagoscxp
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las solicitudes de pago asociadas a proveedores/beneficiarios
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_monto=0;
		$lb_valido= true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_criterio3="";
		if(!empty($as_numsol))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol='".$as_numsol."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
//			$ls_criterio2=$ls_criterio2. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
			$ls_criterio3=$ls_criterio3. "  AND scb_movbco.fecmov>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
//			$ls_criterio2=$ls_criterio2. "  AND cxp_historico_solicitud.fecha<='".$ad_fecreghas."'";
			$ls_criterio3=$ls_criterio3. "  AND scb_movbco.fecmov<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " WHERE {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql=" SELECT cxp_solicitudes.numsol,cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov, ".
				"        (CASE cxp_sol_banco.estmov ".
				"         WHEN 'O' THEN 1 ".
                "         WHEN 'A' THEN 2 ".
                "         ELSE 0 END ) as orden ".
				"   FROM cxp_solicitudes ".
				"  INNER JOIN cxp_historico_solicitud ".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_solicitudes.estprosol<>'A'".
				"    AND cxp_historico_solicitud.estprodoc='S'".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				" ".$ls_criterio." ".
				" ".$ls_criterio2." ".
				"  INNER JOIN (cxp_sol_banco".
				"    INNER JOIN scb_movbco".
				"    ON cxp_sol_banco.codemp=scb_movbco.codemp".
				"    AND cxp_sol_banco.codban=scb_movbco.codban".
				"    AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"    AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"    AND cxp_sol_banco.codope=scb_movbco.codope".
				"    AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"    AND scb_movbco.estmov<>'N')".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				" ".$ls_criterio." ".
				" ".$ls_criterio3." ".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol".$ls_filtrofrom.$ls_filtroest.
				" GROUP BY cxp_solicitudes.numsol, cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov ".
				" ORDER BY scb_movbco.fecmov,cxp_sol_banco.numdoc, cxp_solicitudes.numsol";
/*				"   FROM cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco, scb_movbco".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"	 AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_sol_banco.estmov='C' ".
				"    AND cxp_solicitudes.estprosol<>'A'".
				" ".$ls_criterio." ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol".
				"    AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"    AND cxp_sol_banco.codban=scb_movbco.codban".
				"    AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"    AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"    AND cxp_sol_banco.codope=scb_movbco.codope".
				"    AND cxp_sol_banco.estmov=scb_movbco.estmov ".
				" GROUP BY cxp_solicitudes.numsol, cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov, orden ".
				" ORDER BY cxp_solicitudes.numsol, cxp_sol_banco.numdoc, orden ";
*/				
//		echo $ls_sql."<br><br>";
		$this->rs_pagactuales=$this->io_sql->select($ls_sql);
		if($this->rs_pagactuales===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagoscxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_informacionpagoscxp
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionpagosactuales($as_tipproben,$as_cedbene,$as_codpro,$ad_fecregdes,$ad_fecreghas,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionpagosactuales
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las solicitudes de pago asociadas a proveedores/beneficiarios
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_monto=0;
		$lb_valido= true;
		$ls_criterio="";
		$ls_criterio2="";
		$ls_criterio3="";
		if(!empty($as_numsol))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol='".$as_numsol."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio2=$ls_criterio2. "  AND cxp_solicitudes.fecemisol<'".$ad_fecregdes."'";
			$ls_criterio3=$ls_criterio3. "  AND scb_movbco.fecmov>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio3=$ls_criterio3. "  AND scb_movbco.fecmov<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " WHERE {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql=" SELECT cxp_solicitudes.numsol,cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov, ".
				"        (CASE cxp_sol_banco.estmov ".
				"         WHEN 'O' THEN 1 ".
                "         WHEN 'A' THEN 2 ".
                "         ELSE 0 END ) as orden ".
				"   FROM cxp_solicitudes ".
				"  INNER JOIN cxp_historico_solicitud ".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_solicitudes.estprosol<>'A'".
				"    AND cxp_historico_solicitud.estprodoc='S'".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				" ".$ls_criterio." ".
				"  INNER JOIN (cxp_sol_banco".
				"    INNER JOIN scb_movbco".
				"    ON cxp_sol_banco.codemp=scb_movbco.codemp".
				"    AND cxp_sol_banco.codban=scb_movbco.codban".
				"    AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"    AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"    AND cxp_sol_banco.codope=scb_movbco.codope".
				"    AND cxp_sol_banco.estmov=scb_movbco.estmov) ".
				"     ON cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				" ".$ls_criterio." ".
				" ".$ls_criterio2." ".
				" ".$ls_criterio3." ".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol".$ls_filtrofrom.$ls_filtroest.
				" GROUP BY cxp_solicitudes.numsol, cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov ".
				" ORDER BY scb_movbco.fecmov,cxp_sol_banco.numdoc, cxp_solicitudes.numsol ";
		$this->rs_pagactuales=$this->io_sql->select($ls_sql);//print $ls_sql;
		if($this->rs_pagactuales===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagosactuales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_informacionpagosactuales
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_datos_comprobante($ls_codemp,$ls_numrecdoc,$ls_codpro,$ls_cedben,$ls_codproben)
	{//Utilizado en libro de compra

		$la_data=$this->uf_select_rowdata($this->io_sql,"SELECT a.numrecdoc as numrecdoc,a.monobjret as monobjret,a.monret as monret,a.porded as porded,
																              b.codret as codret,b.numcom as numcom,b.iva_ret as iva_ret,tiptrans
												  FROM cxp_rd_deducciones a,scb_dt_cmp_ret b,scb_cmp_ret cmp
												  WHERE a.codemp='".$ls_codemp."' AND a.numrecdoc='".$ls_numrecdoc."' AND a.cod_pro='".$ls_codpro."' AND a.ced_bene='".$ls_cedben."' AND cmp.codsujret='".$ls_codproben."'
												  AND a.codemp=b.codemp AND a.codemp=cmp.codemp AND a.numrecdoc=b.numfac AND b.codret=cmp.codret AND b.numcom=cmp.numcom
												  GROUP BY a.numrecdoc ");
		return $la_data;
	}

	function uf_select_cargos($ls_codemp,$ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedben)
	{//Utilizado en libro de compra.
		$la_data=$this->uf_select_rowdata($this->io_sql,"SELECT monobjret as basimp,porcar,monret as impiva".
															 "  FROM cxp_rd_cargos ".
															 " WHERE codemp='".$ls_codemp."'".
															 "   AND numrecdoc='".$ls_numrecdoc."'".
															 "   AND codtipdoc='".$ls_codtipdoc."'".
															 "   AND cod_pro='".$ls_codpro."'".
															 "   AND ced_bene='".$ls_cedben."'");
		return	$la_data;
	}

	function uf_select_dtnotas($ls_codemp,$ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedben,$rs_data)
	{
		$ls_cadena="SELECT *
					FROM cxp_sol_dc
					WHERE codemp='".$ls_codemp."' AND numrecdoc='".$ls_numrecdoc."' AND codtipdoc='".$ls_codtipdoc."'
					AND cod_pro='".$ls_codpro."' AND ced_bene='".$ls_cedben."'";
		$rs_data=$this->io_sql->select($ls_cadena);
		return $rs_data;
	}

	function uf_select_notaformatosalida($ls_codemp,$ls_numnota,$ls_tiponota,$ls_numord,$ls_numrecdoc,$ls_codtipdoc,$ls_aux)
	{
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest = '';
		$ls_filtrofrom = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $io_conexion->Concat('cxp_dc_spg.codestpro','cxp_dc_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_dc_spg.codemp = cxp_sol_dc.codemp ".
							" AND cxp_dc_spg.numrecdoc = cxp_sol_dc.numrecdoc ".
							" AND cxp_dc_spg.codtipdoc = cxp_sol_dc.codtipdoc ".
							" AND cxp_dc_spg.ced_bene = cxp_sol_dc.ced_bene ".
							" AND cxp_dc_spg.cod_pro = cxp_sol_dc.cod_pro ";
			$ls_filtrofrom = " , cxp_dc_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$la_nota=$this->uf_select_rowdata($this->io_sql,"SELECT cxp_sol_dc.* FROM cxp_sol_dc $ls_filtrofrom WHERE cxp_sol_dc.codemp='".$ls_codemp."'
												   AND cxp_sol_dc.numdc='".$ls_numnota."' AND cxp_sol_dc.codope='".$ls_tiponota."' AND cxp_sol_dc.numsol='".$ls_numord."'
												   AND cxp_sol_dc.numrecdoc='".$ls_numrecdoc."' AND cxp_sol_dc.codtipdoc='".$ls_codtipdoc."' ".$ls_aux.$ls_filtroest);
		return $la_nota;

	}

	function uf_select_notacargos($ls_codemp,$ls_numrecdoc,$ls_codtipdoc,$ls_codpro,$ls_cedben,$ls_codope,$ls_numnota,$ls_numsol,$ldec_porcar)
	{
		$ldec_monto=0;
		$ldec_porcar=0;
		$ls_aux_codestpro = $this->io_conexion->Concat('b.spg_cuenta','b.codestpro');
		$ls_aux_codestprob = $this->io_conexion->Concat('spg_cuenta','codestpro');
		$ls_sql="SELECT monto,porcar
				 FROM cxp_dc_spg a,cxp_rd_cargos c
				 WHERE a.numdc='".$ls_numnota."' AND a.codope='".$ls_codope."' AND a.numsol='".$ls_numsol."' AND a.numrecdoc='".$ls_numrecdoc."' AND a.codtipdoc='".$ls_codtipdoc."' AND a.cod_pro='".$ls_codpro."'
				 AND a.ced_bene='".$ls_cedben."' AND a.numrecdoc=c.numrecdoc AND a.codtipdoc=c.codtipdoc AND a.cod_pro=c.cod_pro AND a.ced_bene=c.ced_bene AND {$ls_aux_codestpro} IN (SELECT DISTINCT {$ls_aux_codestprob} FROM sigesp_cargos)";


		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_notacargos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ldec_monto=$row["monto"];
				$ldec_porcar=$row["porcar"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ldec_monto;
	}

	function uf_select_sol_cargos($as_codemp,$as_numero)
	{
     //////////////////////////////////////////////////////////////////////////////
     //	Funcion      uf_select_rec_doc_solicitud
     //	Access       public
     //	Arguments    $as_codemp,$as_numero
     //	Returns	     $rs (Resulset)
     //	Description  Devuelve un resulset para cargar las receptciones asociados a una
     //              Solicitud, se utiliza en el catalogo de principal del solicitud
     //              de Ejecucion Presupuestaria
     //////////////////////////////////////////////////////////////////////////////
     $this->ds_car_dt = new class_datastore();

	 $ls_sql=" SELECT C.numsol as numsol,MAX(C.numrecdoc) as numrecdoc,
	                  P.codcar as codcar,SUM(P.monobjret) as monobjretcar,
					  SUM(P.monret) as objretcar,
					  max(sigesp_cargos.dencar) as dencar, MAX(sigesp_cargos.porcar) AS porcar
                 FROM cxp_dt_solicitudes C, cxp_rd_cargos P, sigesp_cargos
                WHERE C.codemp='".$as_codemp."'
			      AND C.numsol ='".$as_numero."'
				  AND C.codemp=P.codemp
				  AND C.numrecdoc=P.numrecdoc
				  AND C.cod_pro=P.cod_pro
				  AND C.ced_bene=P.ced_bene
				  AND P.codemp=sigesp_cargos.codemp
				  AND P.codcar=sigesp_cargos.codcar
                GROUP BY P.codcar,C.numsol";
	 $this->ds_car_dt->resetds("numsol");
	 $rs=$this->io_sql->select($ls_sql);
	 if ($rs===false)
		 {
			 $this->io_mensajes->message("CLASE->SIGESP_CXP_CLASS_REPORT; METODO->uf_select_sol_cargos; ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			 $lb_valido=false;
		 }
         else
         {
		     if ($row=$this->io_sql->fetch_row($rs))
		     {
				  $lb_valido=true;
				  $datos=$this->io_sql->obtener_datos($rs);
				  $this->ds_car_dt->data=$datos;
		    	  $this->io_sql->free_result($rs);
			 }
             else
             {
                 $lb_valido=false;
             }
         }
    return $lb_valido;
	}

	function uf_select_sol_deducciones($as_codemp,$as_numero)
	{
     //////////////////////////////////////////////////////////////////////////////
     //	Funcion      uf_select_sol_deducciones
     //	Access       public
     //	Arguments    $as_codemp,$as_numero
     //	Returns	     $rs (Resulset)
     //	Description  Devuelve un resulset para cargar las receptciones asociados a una
     //              Solicitud, se utiliza en el catalogo de principal del solicitud
     //              de Ejecucion Presupuestaria
     //////////////////////////////////////////////////////////////////////////////
     $this->ds_ded_dt = new class_datastore();

	 $ls_sql=" SELECT C.numsol as numsol,MAX(C.numrecdoc) as numrecdoc,
	                  P.codded as codded,SUM(P.monobjret) as monobjretded,
					  SUM(P.monret) as objretded,
					  max(sigesp_deducciones.dended) as dended, max(sigesp_deducciones.porded) as porded
                 FROM cxp_dt_solicitudes C, cxp_rd_deducciones P, sigesp_deducciones
                WHERE C.codemp='".$as_codemp."'
			      AND C.numsol ='".$as_numero."'
				  AND C.codemp=P.codemp
				  AND C.numrecdoc=P.numrecdoc
				  AND C.cod_pro=P.cod_pro
		          AND C.ced_bene=P.ced_bene
                  AND P.codemp=sigesp_deducciones.codemp
				  AND P.codded=sigesp_deducciones.codded
				GROUP BY P.codded,C.numsol";
	 $this->ds_ded_dt->resetds("numsol");
	 $rs=$this->io_sql->select($ls_sql);
	 if ($rs===false)
		 {
			 $this->io_msg->message("Error en Sentencia->uf_select_sol_deducciones");
			 $lb_valido=false;
		 }
         else
         {
		     if ($row=$this->io_sql->fetch_row($rs))
		     {
				  $lb_valido=true;
				  $datos=$this->io_sql->obtener_datos($rs);
				  $this->ds_ded_dt->data=$datos;
		    	  $this->io_sql->free_result($rs);
			 }
             else
             {
                 $lb_valido=false;
             }
         }
    return $lb_valido;
	}
//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_nrocomprobante($as_fac,$as_nrocon,$as_fecha)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_nrocomprobante
		//         Access: public
		//	    Arguments: as_fac     // nurmero de la factura
		//      Arguments: as_nrocon     // numero de control
		//      Arguments: as_fecha     // fecha de la factura
		//	      Returns: lb_valido True si encontro un numero de comprobante
		//    Description: Función que busca el numero de  comprobantes ISRL
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 09/01/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nrocom=0;
		$as_fecha=$this->io_funciones->uf_convertirdatetobd($as_fecha);
		$ls_sql="select numcom from scb_dt_cmp_ret
                 where codemp= '".$this->ls_codemp."'
				 and numfac like '%".$as_fac."%'
				 and numcon like '%".$as_nrocon."%'
				 and fecfac='".$as_fecha."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_nrocomprobante ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nrocom=$row["numcom"];

			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_nrocom;
	}// end function uf_retencionesmunicipales_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_iva_retendio_ISLR($as_fac,$as_nrocon,$as_fecha)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_iva_retendio_ISLR
		//         Access: public
		//	    Arguments: as_fac     // nurmero de la factura
		//      Arguments: as_nrocon     // numero de control
		//      Arguments: as_fecha     // fecha de la factura
		//	      Returns: lb_valido True si encontro un numero de comprobante
		//    Description: Función que busca el iva retenido
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 09/01/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_iva=0;
		$as_fecha=$this->io_funciones->uf_convertirdatetobd($as_fecha);
		$ls_sql="select iva_ret as iva from scb_dt_cmp_ret where codemp= '".$this->ls_codemp."' and numfac like '%".$as_fac."%'
				 and numcon like '%".$as_nrocon."%'
				 and fecfac='".$as_fecha."'";
		$rs_data=$this->io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_iva_retendio_ISLR ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_iva=$row["iva"];

			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_iva;
	}// end function uf_retencionesmunicipales_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_fecha_ISLR($as_fac,$as_nrocon,$as_fecha)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_iva_retendio_ISLR
		//         Access: public
		//	    Arguments: as_fac     // nurmero de la factura
		//      Arguments: as_nrocon     // numero de control
		//      Arguments: as_fecha     // fecha de la factura
		//	      Returns: lb_valido True si encontro un numero de comprobante
		//    Description: Función que busca el iva retenido
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 09/01/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_fecha="";
		$as_fecha=$this->io_funciones->uf_convertirdatetobd($as_fecha);
		$ls_sql="select a.fecrep
                   from scb_cmp_ret  a
                   join scb_dt_cmp_ret b on (a.numcom=b.numcom)
                   where a.codemp= '".$this->ls_codemp."'
                   and b.numfac like '%".$as_fac."%'
                   and b.numcon like '%".$as_nrocon."%'
                   and b.fecfac='".$as_fecha."'";
		$rs_data=$this->io_sql->select($ls_sql);

		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_fecha_ISLR ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_fecha=$row["fecrep"];

			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_fecha;
	}// end function uf_retencionesmunicipales_detalles
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_det_deducciones($as_numrecdoc,$as_codpro,$as_cedben,$as_codtipdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_det_deducciones
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de Recepcion de Documentos
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca las retenciones de IVA.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/06/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_monret=0;
		$ls_sql=" SELECT SUM(cxp_rd_deducciones.monret) as monret".
				"   FROM  cxp_rd_deducciones, sigesp_deducciones".
				"   WHERE cxp_rd_deducciones.codemp='".$this->ls_codemp."'".
				"     AND cxp_rd_deducciones.numrecdoc ='".$as_numrecdoc."'".
				"     AND cxp_rd_deducciones.codtipdoc ='".$as_codtipdoc."'".
				"	  AND cxp_rd_deducciones.cod_pro='".$as_codpro."'".
				"     AND cxp_rd_deducciones.ced_bene='".$as_cedben."'".
				"     AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
				"	  AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
				"     AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.islr=1)";
		$rs=$this->io_sql->select($ls_sql);
		if ($rs===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_det_deducciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs))
			{
				$li_monret=$row["monret"];
			}
		}
    	return $li_monret;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_det_deducciones_solpag($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_det_deducciones_solpag
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de Recepcion de Documentos
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca las retenciones de IVA.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/06/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_monret=0;
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos ".
			                "  						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
							"          				   AND codsis='CXP' ".
				            "                          AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1)" .
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ".
							" AND cxp_rd.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="  SELECT SUM(cxp_rd_deducciones.monret) as monret".
				"    FROM  cxp_rd_deducciones, sigesp_deducciones,cxp_dt_solicitudes".$ls_filtrofrom.
				"   WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"     AND cxp_dt_solicitudes.numsol ='".$as_numsol."'".
				"     AND cxp_rd_deducciones.codemp =cxp_dt_solicitudes.codemp".
				"     AND cxp_rd_deducciones.numrecdoc =cxp_dt_solicitudes.numrecdoc".
				"     AND cxp_rd_deducciones.codtipdoc =cxp_dt_solicitudes.codtipdoc".
				"     AND cxp_rd_deducciones.cod_pro =cxp_dt_solicitudes.cod_pro".
				"     AND cxp_rd_deducciones.ced_bene =cxp_dt_solicitudes.ced_bene".$ls_filtroest.
				"     AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
				"	  AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
				"     AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.islr=1)";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_det_deducciones_solpag ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$li_monret=$rs_data->fields["monret"];
			}
			$this->io_sql->free_result($rs_data);
		}
    	return $li_monret;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_det_deducciones_municipales_solpag($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_det_deducciones_solpag
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de Recepcion de Documentos
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca las retenciones de IVA.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/06/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_dended="";
		$ls_sql="  SELECT sigesp_deducciones.dended as dended".
				"    FROM  cxp_rd_deducciones, sigesp_deducciones,cxp_dt_solicitudes".
				"   WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"     AND cxp_dt_solicitudes.numsol ='".$as_numsol."'".
				"     AND cxp_rd_deducciones.codemp =cxp_dt_solicitudes.codemp".
				"     AND cxp_rd_deducciones.numrecdoc =cxp_dt_solicitudes.numrecdoc".
				"     AND cxp_rd_deducciones.codtipdoc =cxp_dt_solicitudes.codtipdoc".
				"     AND cxp_rd_deducciones.cod_pro =cxp_dt_solicitudes.cod_pro".
				"     AND cxp_rd_deducciones.ced_bene =cxp_dt_solicitudes.ced_bene".
				"     AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
				"	  AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
				"     AND (sigesp_deducciones.estretmun=1)";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_det_deducciones_municipales_solpag ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ls_dended=$rs_data->fields["dended"];
			}
			$this->io_sql->free_result($rs_data);
		}
    	return $ls_dended;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_det_deducciones_1x1000_solpag($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_det_deducciones_1x1000_solpag
		//         Access: public
		//	    Arguments: as_numrecdoc  // Numero de Recepcion de Documentos
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_codtipdoc  // Codigo de Tipo de Documento
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca las retenciones de IVA.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 26/06/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_dended="";
		$ls_sql="  SELECT sigesp_deducciones.dended as dended".
				"    FROM  cxp_rd_deducciones, sigesp_deducciones,cxp_dt_solicitudes".
				"   WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
				"     AND cxp_dt_solicitudes.numsol ='".$as_numsol."'".
				"     AND cxp_rd_deducciones.codemp =cxp_dt_solicitudes.codemp".
				"     AND cxp_rd_deducciones.numrecdoc =cxp_dt_solicitudes.numrecdoc".
				"     AND cxp_rd_deducciones.codtipdoc =cxp_dt_solicitudes.codtipdoc".
				"     AND cxp_rd_deducciones.cod_pro =cxp_dt_solicitudes.cod_pro".
				"     AND cxp_rd_deducciones.ced_bene =cxp_dt_solicitudes.ced_bene".
				"     AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
				"	  AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
				"     AND (sigesp_deducciones.estretmil='1')";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_det_deducciones_1x1000_solpag ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ls_dended=$rs_data->fields["dended"];
			}
			$this->io_sql->free_result($rs_data);
		}
    	return $ls_dended;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_datos_cheque_retencion($as_numsol,$as_nummov,$ad_fecmov,$as_monto)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_datos_cheque_retencion
		//         Access: public
		//	    Arguments: $as_numsol  // Numero de solicitud
		//                 $as_nummov  // Numero del movimiento (cheque)
		//                 $ad_fecmov  // Fecha  del movimiento (cheque)
		//                 $as_monto   // Monto del movimiento (cheque)
		//    Description: Función que busca los datos del cheque de la retencion del impuesto de 1 x 100
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 31/10/2008									Fecha Última Modificación : 		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_nummov="";
		$ad_fecmov="";
		$as_monto=0;
		$ls_sql=" SELECT cxp_sol_banco.numdoc,scb_movbco.fecmov,scb_movbco.monto  ".
				"   FROM  cxp_sol_banco,scb_movbco ".
				"   WHERE cxp_sol_banco.codemp='".$this->ls_codemp."'".
				"     AND cxp_sol_banco.numsol ='".$as_numsol."'".
				"     AND scb_movbco.codemp =cxp_sol_banco.codemp".
				"     AND scb_movbco.numdoc =cxp_sol_banco.numdoc".
				"     AND scb_movbco.estmov <> 'A'".
				"     AND scb_movbco.estmov <> 'O'";
		$rs=$this->io_sql->select($ls_sql);
		if ($rs===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_det_deducciones_solpag ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs))
			{
				$as_nummov=$row["numdoc"];
				$ad_fecmov=$row["fecmov"];
				$as_monto=$row["monto"];
			}
		}
		$arrResultado["as_nummov"]=$as_nummov;
		$arrResultado["ad_fecmov"]=$ad_fecmov;
		$arrResultado["as_monto"]=$as_monto;
		$arrResultado["lb_valido"]=$lb_valido;
		
    	return $arrResultado;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_contribuyentes_libro_timbrefiscal($as_mes,$as_anio,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_contribuyentes_libro_timbrefiscal
		//         Access: public
		//	    Arguments:  as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 25/11/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$as_anio."-".$as_mes."-01";
		$ld_fechahasta=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
		$ls_sql="SELECT  scb_cmp_ret.nomsujret, scb_cmp_ret.rif, scb_cmp_ret.numcom, ".
			    " scb_dt_cmp_ret.basimp, scb_dt_cmp_ret.iva_ret, scb_dt_cmp_ret.fecfac,scb_dt_cmp_ret.numsop".
				"  FROM scb_cmp_ret,scb_dt_cmp_ret ".
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='0000000003' ".
				"   AND scb_cmp_ret.fecrep>='".$ld_fechadesde."' ".
				"   AND scb_cmp_ret.fecrep<='".$ld_fechahasta."' ".
				"   AND scb_cmp_ret.codemp = scb_dt_cmp_ret.codemp ".
				"   AND scb_cmp_ret.codret = scb_dt_cmp_ret.codret ".
				"   AND scb_cmp_ret.numcom = scb_dt_cmp_ret.numcom";

		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_contribuyentes_libro_timbrefiscal ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}

		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		
		return $arrResultado;
	}// end function uf_select_contribuyentes_libro_timbrefiscal

//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_beneficiarios_libro_islr($as_mes,$as_anio,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_beneficiarios_libro_islr
		//         Access: public
		//	    Arguments:  as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes de impuesto sobre la renta
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creación: 26/11/2008									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_criterio2="";
		$ld_fecdes=$as_anio."-".$as_mes."-01";
		$ld_fechas=$as_anio."-".$as_mes."-".substr($this->io_fecha->uf_last_day($as_mes,$as_anio),0,2);
		if($ld_fecdes!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol >= '".$ld_fecdes."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.fecmov >= '".$ld_fecdes."'";
		}
		if($ld_fechas!="")
		{
			$ls_criterio=$ls_criterio."		AND cxp_solicitudes.fecemisol <= '".$ld_fechas."'";
			$ls_criterio2=$ls_criterio2."		AND scb_movbco.fecmov <= '".$ld_fechas."'";
		}

		$ls_sql="SELECT DISTINCT cxp_solicitudes.numsol AS numero, cxp_solicitudes.consol AS concepto, cxp_rd.procede AS procede ".
				"  FROM cxp_solicitudes, cxp_dt_solicitudes, cxp_rd, cxp_rd_deducciones, sigesp_deducciones ".
			    " WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."' ".
				"   AND sigesp_deducciones.islr=1 ".
				$ls_criterio.
				"   AND cxp_solicitudes.estprosol<>'A'".
			    "   AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
			    "   AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ".
				"   AND cxp_solicitudes.cod_pro = cxp_dt_solicitudes.cod_pro ".
				"   AND cxp_solicitudes.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"	AND cxp_dt_solicitudes.codemp = cxp_rd.codemp ".
				"	AND cxp_dt_solicitudes.numrecdoc = cxp_rd.numrecdoc ".
				"	AND cxp_dt_solicitudes.codtipdoc = cxp_rd.codtipdoc ".
				"	AND cxp_dt_solicitudes.cod_pro = cxp_rd.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene = cxp_rd.ced_bene ".
				"	AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"	AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"	AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"	AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"	AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"	AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
				" UNION ".
				"SELECT scb_movbco.numdoc AS numero, MAX(scb_movbco.conmov) AS concepto, MAX(scb_movbco.procede) AS procede ".
			    "  FROM scb_movbco, sigesp_deducciones, scb_movbco_scg ".
				" WHERE scb_movbco.codemp = '".$this->ls_codemp."' ".
				"   AND scb_movbco.codope = 'CH' ".
				"   AND scb_movbco.estmov <> 'A' ".
				"   AND scb_movbco.estmov <> 'O' ".
				"   AND scb_movbco.monret <> 0 ".
				"   AND sigesp_deducciones.islr = 1".
				$ls_criterio2.
				"    AND scb_movbco.codemp = scb_movbco_scg.codemp ".
				"    AND scb_movbco.codban = scb_movbco_scg.codban ".
				"    AND scb_movbco.ctaban = scb_movbco_scg.ctaban ".
				"    AND scb_movbco.numdoc = scb_movbco_scg.numdoc ".
				"    AND scb_movbco.codope = scb_movbco_scg.codope ".
				"    AND scb_movbco.estmov = scb_movbco_scg.estmov ".
				"    AND scb_movbco_scg.codemp = sigesp_deducciones.codemp ".
				"    AND scb_movbco_scg.codded = sigesp_deducciones.codded ".
				"  GROUP BY scb_movbco.numdoc ".
				" UNION ".
				"SELECT cxp_cmp_islr.numsol AS numero, cxp_cmp_islr.consol AS concepto, 'INT' AS procede".
				"  FROM cxp_cmp_islr".
				" WHERE cxp_cmp_islr.codemp = '".$this->ls_codemp."' ".
			    "  ORDER BY numero ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_beneficiarios_libro_islr ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}

		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["rs_data"]=$rs_data;
		
		return $arrResultado;
	}// end function uf_select_beneficiarios_libro_islr
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_deducciones_recepcion($as_codemp,$as_numrecdoc,$as_codtipdoc,$as_codpro,$as_cedben)
	{
     //////////////////////////////////////////////////////////////////////////////
     //	Funcion      uf_select_deducciones_recepcion
     //	Access       public
     //	Arguments    $as_codemp,$as_numero
     //	Returns	     $rs (Resulset)
     //	Description  Devuelve un resulset para cargar las receptciones asociados a una
     //              Solicitud, se utiliza en el catalogo de principal del solicitud
     //              de Ejecucion Presupuestaria
     //////////////////////////////////////////////////////////////////////////////
     $this->ds_ded_rd = new class_datastore();

	 $ls_sql=" SELECT cxp_rd_deducciones.monret,
					  sigesp_deducciones.dended
		  	     FROM cxp_rd , cxp_rd_deducciones, sigesp_deducciones
                WHERE cxp_rd.codemp='".$as_codemp."'
			      AND cxp_rd.numrecdoc ='".$as_numrecdoc."'
				  AND cxp_rd.codtipdoc ='".$as_codtipdoc."'
				  AND cxp_rd.cod_pro   ='".$as_codpro."'
	 			  AND cxp_rd.ced_bene  ='".$as_cedben."'
                  AND cxp_rd.codemp=cxp_rd_deducciones.codemp
				  AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc
				  AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc
				  AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
				  AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
				  AND sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
				  AND sigesp_deducciones.codded=cxp_rd_deducciones.codded";//print $ls_sql;

	 $this->ds_ded_rd->resetds("numrecdoc");
	 $rs=$this->io_sql->select($ls_sql);
	 if ($rs===false)
		 {
			 $this->io_msg->message("Error en Sentencia->uf_select_deducciones_recepcion");
			 $lb_valido=false;
		 }
         else
         {
		     if ($row=$this->io_sql->fetch_row($rs))
		     {
				  $lb_valido=true;
				  $datos=$this->io_sql->obtener_datos($rs);
				  $this->ds_ded_rd->data=$datos;
		    	  $this->io_sql->free_result($rs);
			 }
             else
             {
                 $lb_valido=false;
             }
         }
    return $lb_valido;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesubicar($as_tipproben,$as_codprobendes,$as_codprobenhas,$as_numsoldes,$as_numsolhas,$lb_valido)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesubicar
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 as_numsoldes     // Numero de Solicitud Desde
		//                 as_numsolhas     // Numero de Solicitud Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pago en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($as_numsoldes))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol>='".$as_numsoldes."'";
		}
		if(!empty($as_numsolhas))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol<='".$as_numsolhas."'";
		}
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT DISTINCT cxp_solicitudes.tipproben,cxp_solicitudes.numsol,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"        cxp_solicitudes.fecemisol,cxp_solicitudes.estprosol,cxp_solicitudes.monsol,".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				" ORDER BY cxp_solicitudes.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesubicar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_solicitudesubicar
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_load_ubicacionsol($as_numsol,$lb_valido)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_load_ubicacionsol
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 as_numsoldes     // Numero de Solicitud Desde
		//                 as_numsolhas     // Numero de Solicitud Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pago en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT numsol,numdoc,codban,ctaban,estmov,".
				"       (SELECT nomban FROM scb_banco".
				"		  WHERE cxp_sol_banco.codemp=scb_banco.codemp".
				"           AND cxp_sol_banco.codban=scb_banco.codban) AS banco".
				"  FROM cxp_sol_banco".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND numsol='".$as_numsol."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_load_ubicacionsol ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_load_ubicacionsol
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_ubicacion_recepciones($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas,$as_codtipdoc,
								   			 $ai_registrada,$ai_anulada,$ai_procesada,$as_orden,$as_numrecdoc,$as_numexprel)
	{

		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_ubicacion_recepciones
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//                 as_codtipdoc     // Codigo de Tipo de Documento
		//                 as_registrada    // Estatus de la Recepcion Registrada
		//                 ai_anulada       // Estatus de la Recepcion Anulada
		//                 ai_procesada     // Estatus de la Recepcion Procesada
		//                 as_numrecdoc     // Nro de Recepción de documentos
		//                 ai_orden         // Orden de los Datos en el Reporte Numero/Fecha
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las ubicaciones de recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 31/03/2009									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_numrecdoc))
		{
			$ls_criterio= $ls_criterio."   AND cxp_rd.numrecdoc='".$as_numrecdoc."'";
		}
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_rd.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}

		if(!empty($as_codtipdoc) &&($as_codtipdoc!="-"))
		{
			$as_codtipdoc=substr($as_codtipdoc,0,5);
			$ls_criterio= $ls_criterio."   AND cxp_rd.codtipdoc='".$as_codtipdoc."'";
		}

		if(($ai_registrada==1)||($ai_procesada==1)||($ai_anulada==1))
		{
			$lb_anterior=false;
			if($ai_registrada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_rd.estprodoc='R'";
					$lb_anterior=true;
				}
			}
			if($ai_procesada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_rd.estprodoc='C'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_rd.estprodoc='C'";
				}
			}
			if($ai_anulada==1)
			{
				if(!$lb_anterior)
				{
					$ls_criterio=$ls_criterio."  AND (cxp_rd.estprodoc='A'";
					$lb_anterior=true;
				}
				else
				{
					$ls_criterio=$ls_criterio."  OR cxp_rd.estprodoc='A'";
				}
			}
			if($lb_anterior)
			{
				$ls_criterio=$ls_criterio.")";
			}
		}

		switch($as_orden)
		{
			case "1": // Ordena por Código de personal
				$ls_orden="cxp_rd.numrecdoc ";
				break;

			case "2": // Ordena por Apellido de personal
				$ls_orden="cxp_rd.fecregdoc ";
				break;

		}
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		if(!empty($as_numexprel))
		{
			$ls_criterio= $ls_criterio."   AND cxp_rd.numexprel='".$as_numexprel."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom=" ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_rd.numrecdoc,  MAX(cxp_documento.dentipdoc) AS dentipdoc, MAX(cxp_rd.estprodoc) AS estprodoc, ".
				"		cxp_solicitudes.numsol, MAX(cxp_solicitudes.estprosol) AS estprosol, MAX(cxp_rd.numexprel) AS numexprel,".
				"       (CASE MAX(cxp_rd.tipproben) WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_rd ".
				"  INNER JOIN cxp_documento ".
				"     ON cxp_rd.codtipdoc=cxp_documento.codtipdoc ".
				"  LEFT JOIN (cxp_dt_solicitudes ".
				"             INNER JOIN cxp_solicitudes ".
				"				 ON cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp ".
				"				AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol) ".
				"	  ON cxp_rd.codemp=cxp_dt_solicitudes.codemp ".
				"	 AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc ".
				"	 AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc ".
				"	 AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro ".
				"	 AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene ".$ls_filtrofrom.
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   ".$ls_criterio." ".$ls_filtroest.
				" GROUP BY cxp_rd.codemp,cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.numrecdoc, cxp_solicitudes.numsol,cxp_rd.fecregdoc".
				" ORDER BY ".$ls_orden."";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_ubicacion_recepciones ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			//$lb_valido=false;
		}
		else
		{
			if($this->rs_data->RecordCount()==0)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_ubicacion_recepciones
	//-----------------------------------------------------------------------------------------------------------------------------------

	//---------------------------------------------------------------------------------------------------------------------------------
	function uf_sep_select_usuario($as_codusu)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_sep_select_usuario
		//		   Access: private
		//	    Arguments: as_codemp // codigo de la empresa
		//	   			   as_codusu // codigo del articulo
		//                 as_nomusu // codigo unidad de medida (referencia)
		//    Description: Function que devuelve el codigo de la unidad de medida que tiene asociada el articulo
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creación: 10/04/2007
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
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sep_select_usuario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}//fin 	uf_sep_select_usuario
    //---------------------------------------------------------------------------------------------------------------------------------

    //---------------------------------------------------------------------------------------------------------------------------------
	function uf_load_beneficiario_alterno($as_numsol)
	{
		////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_beneficiario_alterno
		//	    Arguments: $as_codban = Código del Banco.
		//                 $as_ctaban = Cuenta Bancaria.
		//                 $as_numdoc = Número del Documento.
		//                 $as_codope = Código de la Operación.
		//                 $as_estmov = Estatus del Movimiento Bancario.
		//    Description: Metodo que se encarga de retornar el nombre del Beneficiario alterno para aquellas sep
		//                 que manejen la ayuda económica.
		//     Creado por: Ing. Néstor Falcón.
		// Fecha Creación: 26/08/2008
		///////////////////////////////////////////////////////////////////////////////////////////////
		$ls_nombenalt = "";
		$ls_sql="SELECT DISTINCT LTRIM(sep_solicitud.nombenalt) as nombenalt".
				"  FROM sep_solicitud, sep_tiposolicitud, cxp_solicitudes, cxp_dt_solicitudes, cxp_rd, cxp_rd_spg".
				" WHERE cxp_solicitudes.codemp = '".$this->ls_codemp."'".
				"   AND cxp_solicitudes.numsol = '".$as_numsol."'".
				"   AND cxp_rd_spg.procede_doc = 'SEPSPC'".
				"   AND sep_tiposolicitud.estope = 'O'".
				"   AND sep_tiposolicitud.modsep = 'O'".
				"   AND sep_tiposolicitud.estayueco = 'A'".
				"   AND sep_solicitud.codtipsol=sep_tiposolicitud.codtipsol ".
				"   AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_rd_spg.codemp=cxp_rd.codemp".
				"   AND cxp_rd_spg.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_rd_spg.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_rd_spg.ced_bene=cxp_rd.ced_bene".
				"   AND cxp_rd_spg.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_rd_spg.codemp=sep_solicitud.codemp".
				"   AND cxp_rd_spg.ced_bene=sep_solicitud.ced_bene".
				"   AND cxp_rd_spg.cod_pro=sep_solicitud.cod_pro".
				"   AND cxp_rd_spg.numdoccom=sep_solicitud.numsol".
				"   AND cxp_rd.codemp=sep_solicitud.codemp".
				"   AND cxp_rd.ced_bene=sep_solicitud.ced_bene".
				"   AND cxp_rd.cod_pro=sep_solicitud.cod_pro";
		$rs_data = $this->io_sql->select($ls_sql);//echo $ls_sql.'<br>';
		if ($rs_data===false)
		{
			$lb_valido = false;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_sep_select_usuario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			echo $this->io_sql->message;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_nombenalt = $row["nombenalt"];
			}
		}
		return $ls_nombenalt;
	}//End function uf_load_beneficiario_alterno.
    //---------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracionxml($as_mesdes,$as_meshas,$as_year,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_declaracionxml
		//         Access: public
		//	    Arguments: as_quincena // Quincena del cual se van a generar los txt
		//	    		   as_mes      // Mes del cual se van a generar los txt
		//	    		   as_anio     // Año del cual se van a generar los txt
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que genera los txt de la declaración informativa
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
        $ls_rifemp=str_replace("-","",$_SESSION["la_empresa"]["rifemp"]);
		//$ls_mesdesaux=intval($as_mesdes);
		//$ls_meshasaux=intval($as_meshas);
		$ld_fechadesde=$this->io_funciones->uf_convertirdatetobd($as_mesdes);
		$ld_fechahasta=$this->io_funciones->uf_convertirdatetobd($as_meshas);

	    $ls_periodo=substr($ld_fechadesde,0,4).substr($ld_fechadesde,5,2);
			//$ls_periodo=str_pad($li_i,2,"0",0);
//			$ld_fechadesde=$as_year."-".$ls_periodo."-01";
//			$ld_fechahasta=$as_year."-".$ls_periodo."-".substr($this->io_fecha->uf_last_day($ls_periodo,$as_year),0,2);
			$ls_ruta="declaracion";
			@mkdir($ls_ruta,0755);
			$ls_archivo="declaracion/Declaracion_Salarios_y_otras_R_".$ls_periodo."-".date("Y_m_d_H_i").".xml";
			$ls_archivo2="declaracion/ERROR_Declaracion_Salarios_y_otras_R_".$ls_periodo."-".date("Y_m_d_H_i").".txt";
			$lo_archivo=fopen("$ls_archivo","a+");
			$lo_archivo2=fopen("$ls_archivo2","a+");
			$rs_datac=$this->uf_declaracion_xml_cabecera($ld_fechadesde,$ld_fechahasta,$ls_periodo,$as_year);
			$ls_contenido='<?xml version="1.0" encoding="utf-8"?>';
			$ls_contenido.='<RelacionRetencionesISLR RifAgente="'.$ls_rifemp.'" Periodo="'.$as_year.$ls_periodo.'">';
			$ls_cadena="";
			while(!$rs_datac->EOF)
			{
				$ls_rifpro=str_replace("-","",trim($rs_datac->fields["rifpro"]));
				$ls_rifben=str_replace("-","",trim($rs_datac->fields["rifben"]));
				if($ls_rifpro!="")
				{
					$ls_rif=$ls_rifpro;
				}
				else
				{
					$ls_rif=$ls_rifben;
				}
				$ls_numrecdoc=trim($rs_datac->fields["numrecdoc"]);
				$ls_numref=trim($rs_datac->fields["numref"]);
				if($ls_numref=="")
				{
					$ls_numref="NA";
				}
				$ls_numref=str_replace("-","",trim($ls_numref));
				$ls_numrecdoc=str_replace("-","",trim($ls_numrecdoc));
				$li_baseimp=number_format($rs_datac->fields["baseimp"],2,'.','');
				$ls_codconret=trim($rs_datac->fields["codconret"]);
				$ls_codper=trim($rs_datac->fields["codper"]);
				$li_porded=number_format($rs_datac->fields["porded"],2,'.','');
				$ls_procedencia=trim($rs_datac->fields["procedencia"]);
				$ld_fecemidoc=$this->io_funciones->uf_convertirfecmostrar(trim($rs_datac->fields["fecemidoc"]));
				$correcto=true;
				$li_lenrif=strlen($ls_rif);
				if ($ls_procedencia=='CXP')
				{
					if ((trim($ls_rif)=="")||($li_lenrif<10))
					{
						$ls_cadena=$ls_cadena."La factura ".$ls_numrecdoc." no se pudo agregar ya que el proveedor/beneficiario asociado no posee rif. \r\n";
						$correcto=false;
					}
					if ((trim($ls_codconret)==""))
					{
						$ls_cadena=$ls_cadena."La factura ".$ls_numrecdoc." no se pudo agregar ya que la deducción no posee Concepto de Retención asociado. \r\n";
						$correcto=false;
					}
				}
				if ($ls_procedencia=='SNO')
				{
					if ((trim($ls_rif)=="")||($li_lenrif<10))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que no posee rif. \r\n";
						$correcto=false;
					}
					if ((trim($ls_codconret)==""))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que la deducción no posee Concepto de Retención asociado. \r\n";
						$correcto=false;
					}
/*VALIDACION ELIMINADA POR SOLICITUD DE LUIS CORDOVILLA Y APROBADO POR ANIBAL BARRAEZ
					if (($li_porded==0))
					{
						$ls_cadena=$ls_cadena."El personal  ".$ls_codper." no se pudo agregar ya que el porcentaje de deducción es cero. \r\n";
						$correcto=false;
					}
*/				}
				if($correcto)
				{
					$ls_contenido.='<DetalleRetencion>';
					$ls_contenido.='<RifRetenido>'.$ls_rif.'</RifRetenido>';
					$ls_contenido.='<NumeroFactura>'.$ls_numrecdoc.'</NumeroFactura>';
					$ls_contenido.='<NumeroControl>'.$ls_numref.'</NumeroControl>';
					$ls_contenido.='<FechaOperacion>'.$ld_fecemidoc.'</FechaOperacion>';
					$ls_contenido.='<CodigoConcepto>'.$ls_codconret.'</CodigoConcepto>';
					$ls_contenido.='<MontoOperacion>'.$li_baseimp.'</MontoOperacion>';
					$ls_contenido.='<PorcentajeRetencion>'.$li_porded.'</PorcentajeRetencion>';
					$ls_contenido.='</DetalleRetencion>';
				}
				$rs_datac->MoveNext();
			}
			$ls_contenido.='</RelacionRetencionesISLR>';
			@fwrite($lo_archivo,$ls_contenido);
			@fwrite($lo_archivo2,$ls_cadena);
			if($lb_valido)
			{
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="PROCESS";
				$ls_descripcion ="Genero el xml de Declaración de sueldos y otras remuneraciones para el periodo ".
								 " en el Archivo ".$ls_archivo.
								 " Asociado a la empresa ".$this->ls_codemp;
				$lb_valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
			}
		return $lb_valido;
	}// end function uf_declaracioninformativa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_declaracion_xml_cabecera($as_fecemidocdes,$as_fecemidochas,$as_periodo,$as_year)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_declaracion_xml_cabecera
		//         Access: public
		//      Argumento: as_fecemidocdes // Parametro de busqueda Fecha Desde
		//				   as_fecemidochas // Parametro de busqueda Fecha Hasta
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos para la declaracion de salarios y otras remuneraciones
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/06/2009									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_criterio="";
		$rs_data="";
		if($as_fecemidocdes!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.fecregdoc>='".$as_fecemidocdes."'";
		}
		if($as_fecemidochas!="")
		{
			$ls_criterio=$ls_criterio." AND cxp_rd.fecregdoc<='".$as_fecemidochas."'";
		}
		$ls_sql="SELECT '' AS codper, rpc_proveedor.rifpro, rpc_beneficiario.rifben,cxp_rd.numrecdoc, cxp_rd.numref,".
				" 		(cxp_rd_deducciones.monobjret) as baseimp,".
				"       sigesp_deducciones.codconret ,sigesp_deducciones.porded,sigesp_deducciones.codded, 'CXP' AS procedencia,".
				"       (SELECT MAX(fecemisol) FROM cxp_solicitudes,cxp_dt_solicitudes".
				"         WHERE cxp_rd.codemp = cxp_dt_solicitudes.codemp".
				"           AND cxp_rd.numrecdoc = cxp_dt_solicitudes.numrecdoc".
				"           AND cxp_rd.codtipdoc = cxp_dt_solicitudes.codtipdoc".
				"           AND cxp_rd.ced_bene = cxp_dt_solicitudes.ced_bene ".
				"           AND cxp_rd.cod_pro = cxp_dt_solicitudes.cod_pro".
				"           AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"           AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol) AS fecemidoc".
				"  FROM cxp_rd, cxp_rd_deducciones, sigesp_deducciones, rpc_proveedor, rpc_beneficiario ".
				" WHERE cxp_rd.codemp = '".$this->ls_codemp."' ".
				"   AND sigesp_deducciones.islr = 1 ".
				"   AND cxp_rd.estprodoc='C'".
				"   AND cxp_rd.tipdoctesnac='0'".
				$ls_criterio.
				"   AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"   AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"   AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"   AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"   AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"   AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"   AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ".
				"   AND cxp_rd.codemp = rpc_proveedor.codemp ".
				"   AND cxp_rd.cod_pro = rpc_proveedor.cod_pro ".
				"   AND cxp_rd.codemp = rpc_beneficiario.codemp ".
				"   AND cxp_rd.ced_bene = rpc_beneficiario.ced_bene ".
				" UNION ".
				"SELECT sno_personal.codper, MAX(sno_personal.rifper) AS rifpro,'' AS rifben,'0' AS numrecdoc,'' AS numref, SUM(sno_hsalida.valsal), ".
				"	   MAX(sno_personalisr.codconret) AS codconret, MAX(sno_personalisr.porisr) AS porded, sno_personalisr.codisr AS codded, 'SNO' AS procedencia,MAX(sno_hperiodo.fechasper) AS fecemidoc ".
				"  FROM sno_hsalida, sno_personalisr, sno_personal, sno_hperiodo,sno_hconcepto ".
				" WHERE sno_hsalida.codemp = '".$this->ls_codemp."' ".
				"   AND sno_hperiodo.fecdesper>='".$as_fecemidocdes."'".
				"   AND sno_hperiodo.fecdesper<='".$as_fecemidochas."'".
				//"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),1,4) = '".$as_year."' ".
				//"   AND SUBSTR(cast(sno_hperiodo.fecdesper as char(10)),6,2) = '".$as_periodo."' ".
				//"   AND sno_personalisr.codisr = '".$as_periodo."'  ".
				"   AND sno_hconcepto.aplarccon = 1  ".
				"   AND sno_hsalida.codemp = sno_hconcepto.codemp  ".
				"   AND sno_hsalida.anocur = sno_hconcepto.anocur  ".
				"   AND sno_hsalida.codperi = sno_hconcepto.codperi  ".
				"   AND sno_hsalida.codnom = sno_hconcepto.codnom  ".
				"   AND sno_hsalida.codconc = sno_hconcepto.codconc  ".
				"   AND sno_hsalida.codemp = sno_hperiodo.codemp  ".
				"   AND sno_hsalida.anocur = sno_hperiodo.anocur  ".
				"   AND sno_hsalida.codperi = sno_hperiodo.codperi  ".
				"   AND sno_hsalida.codnom = sno_hperiodo.codnom  ".
				"   AND sno_hsalida.codemp = sno_personalisr.codemp  ".
				"   AND sno_hsalida.codper = sno_personalisr.codper  ".
				"   AND sno_personal.codemp = sno_personalisr.codemp  ".
				"   AND sno_personal.codper = sno_personalisr.codper  ".
				" GROUP BY sno_personal.codper, sno_personalisr.codisr ";//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_declaracion_xml_cabecera ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
	}// end function uf_arc_cabecera
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_clasificador()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_obtener_clasificador
		//         Access: public
		//      Argumento: 
		//	      Returns: Retorna un Datastored
		//    Description: Funcion que obtiene los datos para la declaracion de salarios y otras remuneraciones
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 05/06/2009									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codcla,dencla".
				"  FROM cxp_clasificador_rd ";
		$this->rs_clasificador=$this->io_sql->select($ls_sql);
		if($this->rs_clasificador===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_obtener_clasificador ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if($this->rs_clasificador->EOF)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_obtener_clasificador
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesclasificador($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas,$as_codcla,$as_scgcta_desde,$as_scgcta_hasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesclasificador
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 ad_fecregdes     // Fecha de Registro Desde
		//                 ad_fecreghas     // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		$ls_critcta_p="";
		$ls_critcta_b="";
		$this->rs_data="";
		if(!empty($as_tipproben))
		{
			$ls_criterio= $ls_criterio."   AND cxp_solicitudes.tipproben='".$as_tipproben."'";
		}
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_solicitudes.ced_bene<='".$as_codprobenhas."'";
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.fecemisol<='".$ad_fecreghas."'";
		}
		if ((!empty($as_scgcta_desde))&&(!empty($as_scgcta_hasta)))
		{
			if ($as_scgcta_desde!=$as_scgcta_hasta)
			{
				$ls_critcta_p=$ls_critcta_p. " AND rpc_proveedor.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."' ";
				$ls_critcta_b=$ls_critcta_b. " AND rpc_beneficiario.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."' ";
				$ls_criterio=$ls_criterio." AND ((rpc_beneficiario.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."') OR (rpc_proveedor.sc_cuenta BETWEEN '".$as_scgcta_desde."' AND '".$as_scgcta_hasta."')) ";
			}
			else
			{
				$ls_critcta_p=$ls_critcta_p. " AND rpc_proveedor.sc_cuenta='".$as_scgcta_desde."' ";
				$ls_critcta_b=$ls_critcta_b. " AND rpc_beneficiario.sc_cuenta='".$as_scgcta_desde."' ";
				$ls_criterio=$ls_criterio." AND ((rpc_beneficiario.sc_cuenta='".$as_scgcta_desde."') OR (rpc_proveedor.sc_cuenta='".$as_scgcta_desde."')) ";
			}
		}
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		$ls_sql="SELECT SUM(cxp_rd.montotdoc) AS montot,MAX(cxp_solicitudes.tipproben) AS tipproben,cxp_solicitudes.cod_pro,cxp_solicitudes.ced_bene, ".
				"       (CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre, ".
				"		(CASE cxp_solicitudes.tipproben WHEN 'P' THEN (SELECT rpc_proveedor.sc_cuenta ".
				"									FROM rpc_proveedor ".
				"									WHERE rpc_proveedor.codemp=cxp_solicitudes.codemp ".
				"									AND rpc_proveedor.cod_pro=cxp_solicitudes.cod_pro ".
				"									".$ls_critcta_p." )".
				"						WHEN 'B' THEN (SELECT rpc_beneficiario.sc_cuenta ".
				"									FROM rpc_beneficiario ".
				"									WHERE rpc_beneficiario.codemp=cxp_solicitudes.codemp ".
				"									AND rpc_beneficiario.ced_bene=cxp_solicitudes.ced_bene ".
				"                                   ".$ls_critcta_b." )". 
				"						ELSE '-------------------------' END ) AS sc_cuenta ".
				"  FROM cxp_solicitudes,cxp_historico_solicitud,cxp_dt_solicitudes,cxp_rd,rpc_beneficiario,rpc_proveedor ".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_solicitudes.cod_pro=rpc_proveedor.cod_pro ".
				"   AND cxp_solicitudes.ced_bene=rpc_beneficiario.ced_bene ".
				"   AND cxp_rd.cod_pro=rpc_proveedor.cod_pro ".
				"   AND cxp_rd.ced_bene=rpc_beneficiario.ced_bene ".
				"   AND cxp_historico_solicitud.estprodoc='C'".
				"   AND cxp_solicitudes.estprosol<>'A'".
				"   AND cxp_rd.codcla='".$as_codcla."'".
				"   ".$ls_criterio." ".
				"   AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"   AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				" GROUP BY cxp_rd.codcla,cxp_solicitudes.codemp,cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.tipproben".
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";//print "AQUI->".$as_codcla."-->".$ls_sql."<br><br>";
		$this->rs_data=$this->io_sql->select($ls_sql);
		if($this->rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_select_solicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudesactualesclasificacion($as_tipproben,$as_cedbene,$as_codpro,$ad_fecregdes,$ad_fecreghas,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudesactuales
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$ls_criterio="";
		$this->ds_solactuales = new class_datastore();
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha<='".$ad_fecreghas."'";
		}
		$ls_sql="SELECT SUM(cxp_rd.montotdoc) AS montot,cxp_solicitudes.numsol, cxp_solicitudes.monsol,cxp_solicitudes.consol, cxp_historico_solicitud.estprodoc, cxp_historico_solicitud.fecha ".
				"  FROM cxp_solicitudes,cxp_historico_solicitud,cxp_dt_solicitudes,cxp_rd ".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_historico_solicitud.estprodoc='C'".
				"   AND cxp_solicitudes.estprosol<>'A'".
				"   AND cxp_rd.codcla='".$as_codcla."'".
				"	AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"   AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   AND (cxp_historico_solicitud.estprodoc='C' OR cxp_historico_solicitud.estprodoc='A') ".
				"   ".$ls_criterio." ".
				"   AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"   AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				" GROUP BY cxp_rd.codcla,cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.numsol, cxp_solicitudes.monsol,cxp_solicitudes.consol, cxp_historico_solicitud.estprodoc, cxp_historico_solicitud.fecha".
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";//print $ls_sql."<br><br>";

		$this->rs_solicitudes=$this->io_sql->select($ls_sql);
		if ($this->rs_solicitudes===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudesactualescxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($this->rs_solicitudes->EOF)
			{
				$lb_valido=false;
			}
		}
		return $lb_valido;
	}// end function uf_select_solicitudesactuales
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionndncclasificador($as_tipproben,$as_codproben,$ad_fecemides,$ad_fecemihas,$as_numsol,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionndnc
		//         Access: public
		//	    Arguments: as_tipproben // Tipo de Proveedor/Beneficiario
		//                 as_codproben // Codigo de Proveedor/Beneficiario Desde
		//                 ad_fecemides // Fecha de Emision Desde
		//                 ad_fecemihas // Fecha de Emision
		//                 as_numsol    // Numero de la Solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las notas de Debito/Credito de una Solicitud de Pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 26/08/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if($as_tipproben=="P")
		{
			$ls_criterio=$ls_criterio." AND cxp_sol_dc.cod_pro='".$as_codproben."'".
									  " AND cxp_sol_dc.ced_bene='----------'";
		}
		else
		{
			$ls_criterio=$ls_criterio." AND cxp_sol_dc.ced_bene='".$as_codproben."'".
									  " AND cxp_sol_dc.cod_pro='----------'";
		}
		if(!empty($ad_fecemides))
		{
			$ad_fecemides=$this->io_funciones->uf_convertirdatetobd($ad_fecemides);
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.fecope>='".$ad_fecemides."'";
		}
		if(!empty($ad_fecemihas))
		{
			$ad_fecemihas=$this->io_funciones->uf_convertirdatetobd($ad_fecemihas);
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.fecope<='".$ad_fecemihas."'";
		}
		if(!empty($as_numsol))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_sol_dc.numsol='".$as_numsol."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		$ls_filtrofrom="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT SUM(cxp_rd.montotdoc) AS montot,MAX(cxp_sol_dc.numsol) AS numsol,MAX(cxp_sol_dc.codope) AS codope,MAX(cxp_sol_dc.numdc) AS numdc,".
				"		MAX(cxp_sol_dc.fecope) AS fecope,MAX(cxp_sol_dc.desope) AS desope".
				"  FROM cxp_sol_dc,cxp_rd".$ls_filtrofrom.
				" WHERE cxp_sol_dc.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.codcla='".$as_codcla."'".
				"   ".$ls_criterio." ".$ls_filtroest.
				"   AND cxp_sol_dc.codemp=cxp_rd.codemp".
				"   AND cxp_sol_dc.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_sol_dc.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_sol_dc.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_sol_dc.ced_bene=cxp_rd.ced_bene".
				"   AND cxp_sol_dc.estnotadc='C'".
				" GROUP BY cxp_rd.codcla";
		$this->rs_ndnc=$this->io_sql->select($ls_sql);
		if($this->rs_ndnc===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionndnc ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_informacionndnc
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_informacionpagoscxpclasificador($as_tipproben,$as_cedbene,$as_codpro,$ad_fecregdes,$ad_fecreghas,$as_numsol,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_informacionpagoscxp
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_cedbene    // Codigo de Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las solicitudes de pago asociadas a proveedores/beneficiarios
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_monto=0;
		$lb_valido= true;
		$ls_criterio="";
		if(!empty($as_numsol))
		{
			$ls_criterio=$ls_criterio. "  AND cxp_solicitudes.numsol='".$as_numsol."'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_historico_solicitud.fecha<='".$ad_fecreghas."'";
		}
		$ls_sql=" SELECT cxp_solicitudes.numsol,cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov, ".
				"        (CASE cxp_sol_banco.estmov ".
				"         WHEN 'O' THEN 1 ".
                "         WHEN 'A' THEN 2 ".
                "         ELSE 0 END ) as orden ".
				"   FROM cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco, scb_movbco,cxp_dt_solicitudes,cxp_rd".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"	 AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_historico_solicitud.estprodoc='S'".
				"    AND cxp_sol_banco.estmov='C' ".
				"    AND cxp_solicitudes.estprosol<>'A'".
				"    AND cxp_rd.codcla='".$as_codcla."'".
				" ".$ls_criterio." ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"    AND cxp_solicitudes.codemp=cxp_sol_banco.codemp".
				"    AND cxp_solicitudes.numsol=cxp_sol_banco.numsol".
				"    AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"    AND cxp_sol_banco.codban=scb_movbco.codban".
				"    AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"    AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"    AND cxp_sol_banco.codope=scb_movbco.codope".
				"    AND cxp_sol_banco.estmov=scb_movbco.estmov ".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				" GROUP BY cxp_solicitudes.numsol, cxp_sol_banco.numdoc,cxp_sol_banco.monto,cxp_sol_banco.codope,scb_movbco.fecmov,scb_movbco.conmov, cxp_sol_banco.estmov, orden ".
				" ORDER BY  orden ";//print $ls_sql."<br><br>";

		$this->rs_pagactuales=$this->io_sql->select($ls_sql);
		if($this->rs_pagactuales===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagoscxp ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_informacionpagoscxp
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudes_previasclasificador($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas,$as_codcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudes_previasclasificador
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$this->ds_solprevias= new class_datastore();
		$ls_criterio="";
		if($ad_fecregdes!="")
		{
			$ls_criterio="AND cxp_historico_solicitud.fecha < '".$ad_fecregdes."'";
		}
		$ls_sql="SELECT SUM(cxp_rd.montotdoc) AS montot,cxp_solicitudes.numsol, cxp_solicitudes.monsol,cxp_solicitudes.consol, cxp_historico_solicitud.estprodoc AS estatus, cxp_historico_solicitud.fecha ".
				"  FROM cxp_solicitudes,cxp_historico_solicitud,cxp_dt_solicitudes,cxp_rd ".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_historico_solicitud.estprodoc='C'".
				"   AND cxp_rd.codcla='".$as_codcla."'".
				"	AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"   AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"   AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"   ".$ls_criterio." ".
				"   AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"   AND cxp_historico_solicitud.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene".
				" GROUP BY cxp_rd.codcla,cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro,cxp_solicitudes.numsol, cxp_solicitudes.monsol,cxp_solicitudes.consol, cxp_historico_solicitud.estprodoc, cxp_historico_solicitud.fecha".
				" ORDER BY cxp_solicitudes.ced_bene,cxp_solicitudes.cod_pro";//print $ls_sql."<br><br>";
		$this->rs_solprevias=$this->io_sql->select($ls_sql);
		if ($this->rs_solprevias===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudes_previas ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_solicitudes_previas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_pagospreviosclasificador($as_tipproben,$as_codpro,$as_cedbene,$ad_fecregdes,$ad_fecreghas,$as_codcla,$ad_pagosprevios,$ad_retencionesprevios)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_pagosprevios
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de Proveedor/Beneficiario
		//                 as_codpro     // Codigo de Proveedor
		//                 as_cedbene    // Codigo de Beneficiario
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
		$ls_cadena="";
		if($ad_fecregdes!="")
		{
			$ls_cadena="AND cxp_historico_solicitud.fecha < '".$ad_fecregdes."'";
		}
		$ls_sql=" SELECT SUM(CASE WHEN cxp_rd.montotdoc is null THEN 0 ELSE cxp_rd.montotdoc END) AS pagos".
				"   FROM cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco,cxp_dt_solicitudes,cxp_rd".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"    AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
				"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
				"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
				"    AND cxp_rd.codcla='".$as_codcla."'".
				$ls_cadena.
				"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosprevios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_pagosprevios=$rs_data->fields["pagos"];
			}
			$this->io_sql->free_result($rs_data);
		}
			$ls_sql=" SELECT SUM(cxp_rd_deducciones.monret) as retenciones".
					"   FROM  cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco,cxp_rd_deducciones, sigesp_deducciones,cxp_dt_solicitudes".
					"  WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."'".
					"    AND cxp_historico_solicitud.estprodoc='P'".
					"    AND cxp_solicitudes.tipproben='".$as_tipproben."'".
					"    AND cxp_solicitudes.ced_bene='".$as_cedbene."'".
					"    AND cxp_solicitudes.cod_pro='".$as_codpro."'".
					$ls_cadena.
					"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
					"    AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
					"    AND cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp".
					"    AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol".
					"    AND cxp_rd_deducciones.codemp =cxp_dt_solicitudes.codemp".
					"    AND cxp_rd_deducciones.numrecdoc =cxp_dt_solicitudes.numrecdoc".
					"    AND cxp_rd_deducciones.codtipdoc =cxp_dt_solicitudes.codtipdoc".
					"    AND cxp_rd_deducciones.cod_pro =cxp_dt_solicitudes.cod_pro".
					"    AND cxp_rd_deducciones.ced_bene =cxp_dt_solicitudes.ced_bene".
					"    AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
					"	 AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
					"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
					"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
					"    AND (sigesp_deducciones.iva=1 OR sigesp_deducciones.islr=1)";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosprevios ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$ad_retencionesprevios=$rs_data->fields["retenciones"];
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado["lb_valido"]=$lb_valido;
		$arrResultado["ad_pagosprevios"]=$ad_pagosprevios;
		$arrResultado["ad_retencionesprevios"]=$ad_retencionesprevios;
		return $arrResultado;
	}// end function uf_select_pagosprevios
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_imputacionpresupuestaria_solicitud($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_imputacionpresupuestaria_solicitud
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT cxp_rd_spg.numrecdoc,cxp_rd_spg.codestpro,cxp_rd_spg.estcla,cxp_rd_spg.spg_cuenta,MAX(cxp_rd_spg.monto) AS monto".
				"  FROM cxp_dt_solicitudes, cxp_solicitudes, cxp_rd_spg".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd_spg.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd_spg.numrecdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd_spg.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd_spg.ced_bene".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd_spg.codtipdoc".
				" GROUP BY cxp_rd_spg.numrecdoc,cxp_rd_spg.codestpro,cxp_rd_spg.estcla,cxp_rd_spg.spg_cuenta".
				" ORDER BY cxp_rd_spg.numrecdoc,cxp_rd_spg.codestpro,cxp_rd_spg.estcla,cxp_rd_spg.spg_cuenta ASC";
		$this->rs_imputacionpresupuestaria=$this->io_sql->select($ls_sql);
		if ($this->rs_imputacionpresupuestaria===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_imputacionpresupuestaria_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_imputacionpresupuestaria_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_imputacioncontable_solicitud($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_imputacionpresupuestaria_solicitud
		//         Access: public (sigesp_sep_p_solicitud)
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT cxp_rd_scg.numrecdoc,cxp_rd_scg.sc_cuenta,cxp_rd_scg.debhab,MAX(cxp_rd_scg.monto) AS monto".
				"  FROM cxp_dt_solicitudes, cxp_solicitudes, cxp_rd_scg".
				" WHERE cxp_solicitudes.codemp='".$this->ls_codemp."'".
				"   AND cxp_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_dt_solicitudes.codemp=cxp_solicitudes.codemp".
				"   AND cxp_dt_solicitudes.numsol=cxp_solicitudes.numsol".
				"   AND cxp_dt_solicitudes.codemp=cxp_rd_scg.codemp".
				"   AND cxp_dt_solicitudes.numrecdoc=cxp_rd_scg.numrecdoc".
				"   AND cxp_dt_solicitudes.cod_pro=cxp_rd_scg.cod_pro".
				"   AND cxp_dt_solicitudes.ced_bene=cxp_rd_scg.ced_bene".
				"   AND cxp_dt_solicitudes.codtipdoc=cxp_rd_scg.codtipdoc".
				" GROUP BY cxp_rd_scg.numrecdoc,cxp_rd_scg.debhab,cxp_rd_scg.sc_cuenta".
				" ORDER BY cxp_rd_scg.numrecdoc,cxp_rd_scg.debhab,cxp_rd_scg.sc_cuenta ASC";
		$this->rs_imputacioncontable=$this->io_sql->select($ls_sql);
		if ($this->rs_imputacioncontable===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_imputacioncontable_solicitud ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_select_imputacioncontable_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_solicitudessinretencion($as_tipproben,$as_codprobendes,$as_codprobenhas,$as_tipret,$lb_valido)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_solicitudessinretencion
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de Proveedor/Beneficiario
		//                 as_codprobendes  // Codigo de Proveedor/Beneficiario Desde
		//                 as_codprobenhas  // Codigo de Proveedor/Beneficiario Hasta
		//                 as_numsoldes     // Numero de Solicitud Desde
		//                 as_numsolhas     // Numero de Solicitud Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de las solicitudes de pago en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_criterio="";
		if(!empty($as_codprobendes))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro>='".$as_codprobendes."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene>='".$as_codprobendes."'";
			}
		}
		if(!empty($as_codprobenhas))
		{
			if($as_tipproben=="P")
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.cod_pro<='".$as_codprobenhas."'";
			}
			else
			{
				$ls_criterio= $ls_criterio."   AND cxp_rd.ced_bene<='".$as_codprobenhas."'";
			}
		}
		switch($as_tipret)
		{
			case"T":
				$ls_criterio=$ls_criterio." AND (sigesp_deducciones.iva='1' OR sigesp_deducciones.estretmun='1' OR sigesp_deducciones.estretmil='1')";
			break;
			case"I":
				$ls_criterio=$ls_criterio." AND sigesp_deducciones.iva='1'";
			break;
			case"M":
				$ls_criterio=$ls_criterio."AND sigesp_deducciones.estretmun='1'";
			break;
			case"1":
				$ls_criterio=$ls_criterio."AND sigesp_deducciones.estretmil='1'";
			break;
			case"R":
				$ls_criterio=$ls_criterio."AND sigesp_deducciones.islr='1'";
			break;
		}
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos ".
			                "  							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
			$ls_filtrofrom = " ,cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_dt_solicitudes.numsol,cxp_dt_solicitudes.numrecdoc,cxp_rd.fecemidoc,sigesp_deducciones.dended, ".
				"       (CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre ".
				"  FROM cxp_rd_deducciones,sigesp_deducciones,cxp_rd,cxp_dt_solicitudes ".$ls_filtrofrom.
				" WHERE cxp_rd_deducciones.codemp=sigesp_deducciones.codemp ".
				"   AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
				"   AND cxp_rd.codemp=cxp_rd_deducciones.codemp ".
				"   AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc".
				"   AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc".
				"   AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene".
				"   AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro".
				"   AND cxp_rd.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				"   AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_dt_solicitudes.numsol NOT IN (SELECT numsop FROM scb_dt_cmp_ret)".
				"   ".$ls_criterio." ".$ls_filtroest.
				"   ORDER BY cxp_dt_solicitudes.numsol,cxp_dt_solicitudes.numrecdoc";
		
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_solicitudessinretencion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_solicitudessinretencion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesunoxmil($ld_fechadesde,$ld_fechahasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesunoxmil
		//         Access: public
		//	    Arguments: as_numcom // Numero de comprobante municipal
		//	    		   as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$this->io_funciones->uf_convertirdatetobd($ld_fechadesde);
		$ld_fechahasta=$this->io_funciones->uf_convertirdatetobd($ld_fechahasta);
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT  scb_cmp_ret.numcom, sum(scb_dt_cmp_ret.totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(scb_dt_cmp_ret.basimp) as basimp,  SUM(scb_dt_cmp_ret.totimp) as totimp, SUM(scb_dt_cmp_ret.iva_ret) as iva_ret, max(scb_dt_cmp_ret.numsop) as numsop,  ".
				"        max(scb_cmp_ret.nomsujret) as nomsujret,max(scb_cmp_ret.rif) as rif,max(scb_cmp_ret.fecrep) as fecrep, MAX(scb_dt_cmp_ret.numfac) AS numfac,".
				"       (SELECT MAX(nomban)".
				"		   FROM scb_banco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codban=scb_banco.codban".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS nomban,".
				"       (SELECT MAX(docdestrans)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS docdestrans,".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(monto)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS montopag,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag,".
				"       (SELECT MAX(fecemisol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS fecemisol,".
				"       (SELECT MAX(consol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS consol".
				"  FROM scb_dt_cmp_ret,scb_cmp_ret ".
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='0000000005' ".
				"   AND scb_dt_cmp_ret.tipdoctesnac<>'1' ".
				"   AND scb_cmp_ret.fecrep>='".$ld_fechadesde."' ".
				"   AND scb_cmp_ret.fecrep<='".$ld_fechahasta."' ".
				"   AND scb_cmp_ret.estcmpret=1 ".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codret=scb_cmp_ret.codret".
				"   AND scb_dt_cmp_ret.numcom=scb_cmp_ret.numcom".
				" GROUP BY scb_cmp_ret.codemp, scb_cmp_ret.numcom,scb_dt_cmp_ret.codemp,scb_dt_cmp_ret.numsop".
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesunoxmil ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesunoxmil
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesaporte($ld_fechadesde,$ld_fechahasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesunoxmil
		//         Access: public
		//	    Arguments: as_numcom // Numero de comprobante municipal
		//	    		   as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$this->io_funciones->uf_convertirdatetobd($ld_fechadesde);
		$ld_fechahasta=$this->io_funciones->uf_convertirdatetobd($ld_fechahasta);
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT  scb_cmp_ret.numcom, sum(scb_dt_cmp_ret.totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(scb_dt_cmp_ret.basimp) as basimp,  SUM(scb_dt_cmp_ret.totimp) as totimp, SUM(scb_dt_cmp_ret.iva_ret) as iva_ret, max(scb_dt_cmp_ret.numsop) as numsop,  ".
				"        max(scb_cmp_ret.nomsujret) as nomsujret,max(scb_cmp_ret.rif) as rif,max(scb_cmp_ret.fecrep) as fecrep, MAX(scb_dt_cmp_ret.numfac) AS numfac,".
				"       (SELECT MAX(nomban)".
				"		   FROM scb_banco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codban=scb_banco.codban".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS nomban,".
				"       (SELECT MAX(docdestrans)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS docdestrans,".
				"       (SELECT MAX(fecmov)".
				"		   FROM scb_movbco,cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND cxp_sol_banco.codemp=scb_movbco.codemp".
				"           AND cxp_sol_banco.codban=scb_movbco.codban".
				"           AND cxp_sol_banco.ctaban=scb_movbco.ctaban".
				"           AND cxp_sol_banco.numdoc=scb_movbco.numdoc".
				"           AND cxp_sol_banco.codope=scb_movbco.codope".
				"           AND cxp_sol_banco.estmov=scb_movbco.estmov".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol".
				"		  GROUP BY cxp_sol_banco.numsol) AS fecmov,".
				"       (SELECT MAX(monto)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS montopag,".
				"       (SELECT MAX(numdoc)".
				"		   FROM cxp_sol_banco,cxp_solicitudes".
				"         WHERE cxp_sol_banco.codemp=cxp_solicitudes.codemp".
				"           AND cxp_sol_banco.numsol=cxp_solicitudes.numsol".
				"           AND scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_sol_banco.numsol) AS numdocpag,".
				"       (SELECT MAX(fecemisol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS fecemisol,".
				"       (SELECT MAX(consol)".
				"		   FROM cxp_solicitudes".
				"         WHERE scb_dt_cmp_ret.codemp=cxp_solicitudes.codemp".
				"           AND scb_dt_cmp_ret.numsop=cxp_solicitudes.numsol  GROUP BY cxp_solicitudes.numsol) AS consol".
				"  FROM scb_dt_cmp_ret,scb_cmp_ret ".
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='0000000004' ".
				"   AND scb_dt_cmp_ret.tipdoctesnac<>'1' ".
				"   AND scb_cmp_ret.fecrep>='".$ld_fechadesde."' ".
				"   AND scb_cmp_ret.fecrep<='".$ld_fechahasta."' ".
				"   AND scb_cmp_ret.estcmpret=1 ".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codret=scb_cmp_ret.codret".
				"   AND scb_dt_cmp_ret.numcom=scb_cmp_ret.numcom".
				" GROUP BY scb_cmp_ret.codemp, scb_cmp_ret.numcom,scb_dt_cmp_ret.codemp,scb_dt_cmp_ret.numsop".
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesunoxmil ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesunoxmil
	//-----------------------------------------------------------------------------------------------------------------------------------
	//---------------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_fechaOC($as_numordcom,$as_procede)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_buscar_fechaOC
		//		   Access: private
		//	    Arguments: as_codemp // codigo de la empresa
		//	   			   as_codusu // codigo del articulo
		//                 as_nomusu // codigo unidad de medida (referencia)
		//    Description: Function que devuelve el codigo de la unidad de medida que tiene asociada el articulo
		//	   Creado Por: Ing. Yozelin Barragan.
		// Fecha Creación: 10/04/2007
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=false;
		 $as_fecordcom="";
		 switch ($as_procede)
		 {
		 	case "SOCCOS":
				$as_estcondat="S";
				$ls_sql ="SELECT fecordcom ".
					  "  FROM soc_ordencompra ".
					  " WHERE codemp='".$this->ls_codemp."'".
					  "   AND numordcom='".$as_numordcom."'".
					  "   AND estcondat='".$as_estcondat."'";
			break;
		 	case "SOCCOC":
				$as_estcondat="C";
				$ls_sql ="SELECT fecordcom ".
					  "  FROM soc_ordencompra ".
					  " WHERE codemp='".$this->ls_codemp."'".
					  "   AND numordcom='".$as_numordcom."'".
					  "   AND estcondat='".$as_estcondat."'";
			break;
		 	case "SEPSPC":
				$ls_sql ="SELECT fecregsol AS fecordcom ".
					  "  FROM sep_solicitud ".
					  " WHERE codemp='".$this->ls_codemp."'".
					  "   AND numsol='".$as_numordcom."'";
			break;
		 }
		 $rs=$this->io_sql->select($ls_sql);
		 if ($rs===false)
		 {
			$lb_valido=false;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_buscar_fechaOC ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 }
		 else
		 {
			 if($row=$this->io_sql->fetch_row($rs))
			 {
				$as_fecordcom=$row["fecordcom"];
				$lb_valido=true;
			 }
		 }
		 return $as_fecordcom;
	}//fin 	uf_sep_select_usuario
    //---------------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_retencionesmunicipales($ld_fechadesde,$ld_fechahasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_retencionesunoxmil
		//         Access: public
		//	    Arguments: as_numcom // Numero de comprobante municipal
		//	    		   as_mes    // mes del comprobante
		//	    		   as_anio   // año del comprobante
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los comprobantes municipales
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 15/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ld_fechadesde=$this->io_funciones->uf_convertirdatetobd($ld_fechadesde);
		$ld_fechahasta=$this->io_funciones->uf_convertirdatetobd($ld_fechahasta);
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtrofrom = '';
		$ls_filtroest = '';
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') 
		{
			$ls_estconcat1 = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat1} IN (SELECT codintper FROM sss_permisos_internos ".
			                " 						   WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' ".
			                "     					   AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
							" AND cxp_rd_spg.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_dt_solicitudes.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_dt_solicitudes.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_dt_solicitudes.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_dt_solicitudes.cod_pro ".
							" AND cxp_solicitudes.codemp = cxp_dt_solicitudes.codemp ".
							" AND cxp_solicitudes.numsol = cxp_dt_solicitudes.numsol ";
			$ls_filtrofrom = " ,cxp_dt_solicitudes, cxp_rd_spg ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT  scb_cmp_ret.numcom, sum(scb_dt_cmp_ret.totcmp_con_iva) as totcmp_con_iva, ".
				"		 SUM(scb_dt_cmp_ret.basimp) as basimp,  SUM(scb_dt_cmp_ret.totimp) as totimp, SUM(scb_dt_cmp_ret.iva_ret) as iva_ret, max(scb_dt_cmp_ret.numsop) as numsop,  ".
				"        max(scb_cmp_ret.nomsujret) as nomsujret,max(scb_cmp_ret.rif) as rif, MAX(scb_dt_cmp_ret.porimp) AS porimp".
				"  FROM scb_dt_cmp_ret,scb_cmp_ret ".
				" WHERE scb_cmp_ret.codemp='".$this->ls_codemp."' ".
				"   AND scb_cmp_ret.codret='0000000003' ".
				"   AND scb_dt_cmp_ret.tipdoctesnac<>'1' ".
				"   AND scb_cmp_ret.fecrep>='".$ld_fechadesde."' ".
				"   AND scb_cmp_ret.fecrep<='".$ld_fechahasta."' ".
				"   AND scb_cmp_ret.estcmpret=1 ".
				"   AND scb_dt_cmp_ret.codemp=scb_cmp_ret.codemp".
				"   AND scb_dt_cmp_ret.codret=scb_cmp_ret.codret".
				"   AND scb_dt_cmp_ret.numcom=scb_cmp_ret.numcom".
				" GROUP BY scb_cmp_ret.codemp, scb_cmp_ret.numcom,scb_dt_cmp_ret.codemp,scb_dt_cmp_ret.numsop".
				" ORDER BY scb_cmp_ret.numcom ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_retencionesmunicipales ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
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
	}// end function uf_retencionesunoxmil
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_compromisos_relacionados($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_compromisos_relacionados
		//         Access: public 
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data="";
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT cxp_rd_spg.procede_doc,cxp_rd_spg.numdoccom".
				"  FROM cxp_dt_solicitudes, cxp_rd_spg ".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_dt_solicitudes.codemp= cxp_rd_spg.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc= cxp_rd_spg.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc= cxp_rd_spg.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro= cxp_rd_spg.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene= cxp_rd_spg.ced_bene ".
				" GROUP BY cxp_rd_spg.procede_doc,cxp_rd_spg.numdoccom ".
				" ORDER BY cxp_rd_spg.procede_doc,cxp_rd_spg.numdoccom";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_compromisos_relacionados ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
	}// end function uf_select_imputacioncontable_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_monto_compromisos($as_numdoccom,$as_procede)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_compromisos_relacionados
		//         Access: public 
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_monto="";
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT SUM(monto) AS total".
				"  FROM spg_dt_cmp ".
				" WHERE codemp='".$this->ls_codemp."' ".
				"   AND procede='".$as_procede."'".
				"   AND comprobante='".$as_numdoccom."'".
				" GROUP BY procede,comprobante";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{print $this->io_sql->message;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_monto_compromisos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_monto=$row["total"];
			}
		}
		return $li_monto;
	}// end function uf_select_imputacioncontable_solicitud
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_monto_recepcion($as_numsol,$as_numrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_monto_recepcion
		//         Access: public 
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_monto="";
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT SUM(cxp_rd_spg.monto) AS total".
				"  FROM cxp_dt_solicitudes, cxp_rd_spg ".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_dt_solicitudes.numrecdoc='".$as_numrecdoc."'".
				"   AND cxp_dt_solicitudes.codemp= cxp_rd_spg.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc= cxp_rd_spg.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc= cxp_rd_spg.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro= cxp_rd_spg.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene= cxp_rd_spg.ced_bene ".
				" GROUP BY cxp_dt_solicitudes.numsol ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{print $this->io_sql->message;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_monto_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_monto=$row["total"];
			}
		}
		return $li_monto;
	}// end function uf_select_monto_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_monto_recepcion_contable($as_numsol,$as_numrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_monto_recepcion
		//         Access: public 
		//	    Arguments: as_numsol     // Numero de solicitud
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: función que busca la información de recepciones de documentos asociadas a  una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 17/05/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_monto="";
		$this->ds_detalle_rec = new class_datastore();
		$ls_sql="SELECT SUM(cxp_rd.montotdoc) AS montotdoc,SUM(cxp_rd.mondeddoc) AS mondeddoc".
				"  FROM cxp_rd, cxp_dt_solicitudes ".
				" WHERE cxp_dt_solicitudes.codemp='".$this->ls_codemp."' ".
				"   AND cxp_dt_solicitudes.numsol='".$as_numsol."'".
				"   AND cxp_dt_solicitudes.numrecdoc='".$as_numrecdoc."'".
				"   AND cxp_dt_solicitudes.codemp= cxp_rd.codemp ".
				"   AND cxp_dt_solicitudes.numrecdoc= cxp_rd.numrecdoc ".
				"   AND cxp_dt_solicitudes.codtipdoc= cxp_rd.codtipdoc ".
				"   AND cxp_dt_solicitudes.cod_pro= cxp_rd.cod_pro ".
				"   AND cxp_dt_solicitudes.ced_bene= cxp_rd.ced_bene ".
				" GROUP BY cxp_dt_solicitudes.numsol "; 
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{print $this->io_sql->message;
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_monto_recepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		else
		{
			if ($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_montotdoc=$row["montotdoc"];
				$li_mondeddoc=$row["mondeddoc"];
				$li_monto=$li_montotdoc+$li_mondeddoc;
			}
		}
		return $li_monto;
	}// end function uf_select_monto_recepcion
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_probenrelacionanticipos($as_tipproben,$as_codprobendes,$as_codprobenhas,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_probenrelacionanticipos
		//         Access: public
		//	    Arguments: as_tipproben     // Tipo de proveedor
		//				   as_codprobendes  // Codigo proveedor/beneficiario Desde
		//				   as_codprobenhas  // Codigo proveedor/beneficiario Hasta
		//				   ad_fecregdes     // Fecha de registro Desde
		//				   ad_fecreghas     // Fecha de registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca los proveedores/beneficiarios que tienen facturas asociadas
		//	   Creado Por: Ing. Luis Lang
		// Fecha Creación: 09/09/2015									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data="";
		$ls_criterio="";
		$ls_cadena = $this->io_conexion->Concat('rpc_beneficiario.apebene',"', '",'rpc_beneficiario.nombene');
		if ((!empty($as_codprobendes))&&(!empty($as_codprobenhas)))
		{
			switch($as_tipproben)
			{
				case "P":
					$ls_criterio=" AND cxp_rd.cod_pro>='".$as_codprobendes."'".
								 " AND cxp_rd.cod_pro<='".$as_codprobenhas."'".
								 " AND cxp_rd.ced_bene='----------'";
				break;
				case "B":
					$ls_criterio=" AND cxp_rd.ced_bene>='".$as_codprobendes."'".
								 " AND cxp_rd.ced_bene<='".$as_codprobenhas."'".
								 " AND cxp_rd.cod_pro='----------'";
				break;
			}
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd_spg.codestpro','cxp_rd_spg.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ".
			                " AND cxp_rd_spg.codemp = cxp_rd.codemp ".
							" AND cxp_rd_spg.numrecdoc = cxp_rd.numrecdoc ".
							" AND cxp_rd_spg.codtipdoc = cxp_rd.codtipdoc ".
							" AND cxp_rd_spg.ced_bene = cxp_rd.ced_bene ".
							" AND cxp_rd_spg.cod_pro = cxp_rd.cod_pro ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT  DISTINCT(CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.cod_pro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT rpc_beneficiario.ced_bene ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS codigo, ".
				" (CASE tipproben WHEN 'P' THEN (SELECT rpc_proveedor.nompro ".
				"                                        FROM rpc_proveedor ".
				"                                       WHERE rpc_proveedor.codemp=cxp_rd.codemp ".
				"                                         AND rpc_proveedor.cod_pro=cxp_rd.cod_pro) ".
				"                       WHEN 'B' THEN (SELECT ".$ls_cadena." ".
				"                                        FROM rpc_beneficiario ".
				"                                       WHERE rpc_beneficiario.codemp=cxp_rd.codemp ".
				"                                         AND rpc_beneficiario.ced_bene=cxp_rd.ced_bene) ".
				"                       ELSE 'NINGUNO' END ) AS nombre,cxp_rd.tipproben ".
				"  FROM cxp_rd,cxp_dt_solicitudes ".
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.estprodoc='C' ".
				"   AND cxp_rd.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".$ls_filtroest.
				" ".$ls_criterio." ";
				" GROUP BY codigo,cxp_rd.codemp,cxp_rd.cod_pro,cxp_rd.ced_bene,cxp_rd.tipproben".
				" ORDER BY codigo";
		//print $ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_probenrelacionanticipos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_probenrelacionanticipos
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_facturasanticipos($as_tipproben,$as_codigo,$ad_fecregdes,$ad_fecreghas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_facturasanticipos
		//         Access: public
		//	    Arguments: as_tipproben  // Tipo de proveedor
		//				   as_codigo     // Codigo proveedor/beneficiario
		//				   ad_fecregdes  // Fecha de registro Desde
		//				   ad_fecreghas  // Fecha de registro Hasta
		//				   ai_ordendoc   // Indica si se desea ordenar por documento
		//				   ai_ordenfec   // Indica si se desea ordenar por fecha de Registro
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca los las facturas asociadas a un proveedor/beneficiario
		//	   Creado Por: Ing. Luis Lang
		// Fecha Creación: 09/09/2015									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$rs_data="";
		$lb_valido=true;
		$ls_order="";
		if($as_tipproben=="P")
		{
			$ls_criterio=" AND cxp_rd.cod_pro='".$as_codigo."'".
						 " AND cxp_rd.ced_bene='----------'";
		}
		else
		{
			$ls_criterio=" AND cxp_rd.ced_bene='".$as_codigo."'".
						 " AND cxp_rd.cod_pro='----------'";
		}
		if(!empty($ad_fecregdes))
		{
			$ad_fecregdes=$this->io_funciones->uf_convertirdatetobd($ad_fecregdes);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc>='".$ad_fecregdes."'";
		}
		if(!empty($ad_fecreghas))
		{
			$ad_fecreghas=$this->io_funciones->uf_convertirdatetobd($ad_fecreghas);
			$ls_criterio=$ls_criterio. "  AND cxp_rd.fecregdoc<='".$ad_fecreghas."'";
		}
		//FILTRO POR ESTRUCTURA CASO BAER 
		$ls_filtroest="";
		if($_SESSION["la_empresa"]["estfilpremod"]=='1') {
			$ls_estconcat = $this->io_conexion->Concat('cxp_rd.codestpro1','cxp_rd.codestpro2','cxp_rd.codestpro3','cxp_rd.codestpro4','cxp_rd.codestpro5','cxp_rd.estcla');
			$ls_filtroest = " AND {$ls_estconcat} IN (SELECT codintper FROM sss_permisos_internos 
			                   							WHERE sss_permisos_internos.codemp='{$this->ls_codemp}' 
			                     						  AND codsis='SPG' AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) 
			                  AND cxp_rd.coduniadm IN (SELECT codintper FROM sss_permisos_internos 
			                  							        WHERE sss_permisos_internos.codemp='{$this->ls_codemp}'".
				"                                				  AND codsis='CXP'".
				"                                                 AND codusu='{$_SESSION["la_logusr"]}' AND enabled=1) ";
		}
		//FILTRO POR ESTRUCTURA CASO BAER
		$ls_sql="SELECT cxp_rd.numrecdoc, cxp_rd.fecregdoc, cxp_rd.fecemidoc,cxp_rd.fecvendoc, cxp_rd.codtipdoc,".
				" 		cxp_rd.montotdoc,cxp_rd.moncardoc,cxp_rd.mondeddoc,cxp_dt_solicitudes.numsol,".
				"       (SELECT codamo FROM cxp_rd_amortizacion".
				"         WHERE cxp_rd.codemp=cxp_rd_amortizacion.codemp".
				"           AND  cxp_rd.numrecdoc=cxp_rd_amortizacion.numrecdoc".
				"           AND  cxp_rd.codtipdoc=cxp_rd_amortizacion.codtipdoc".
				"           AND  cxp_rd.ced_bene=cxp_rd_amortizacion.ced_bene".
				"           AND  cxp_rd.cod_pro=cxp_rd_amortizacion.cod_pro) AS codant,".
				"       (SELECT montotamo FROM cxp_rd_amortizacion".
				"         WHERE cxp_rd.codemp=cxp_rd_amortizacion.codemp".
				"           AND  cxp_rd.numrecdoc=cxp_rd_amortizacion.numrecdoc".
				"           AND  cxp_rd.codtipdoc=cxp_rd_amortizacion.codtipdoc".
				"           AND  cxp_rd.ced_bene=cxp_rd_amortizacion.ced_bene".
				"           AND  cxp_rd.cod_pro=cxp_rd_amortizacion.cod_pro) AS monant,".
				"       (SELECT codamo FROM cxp_dt_amortizacion".
				"         WHERE cxp_rd.codemp=cxp_dt_amortizacion.codemp".
				"           AND  cxp_rd.numrecdoc=cxp_dt_amortizacion.numrecdoc".
				"           AND  cxp_rd.codtipdoc=cxp_dt_amortizacion.codtipdoc".
				"           AND  cxp_rd.ced_bene=cxp_dt_amortizacion.ced_bene".
				"           AND  cxp_rd.cod_pro=cxp_dt_amortizacion.cod_pro) AS codantamo,".
				"       (SELECT monto FROM cxp_dt_amortizacion".
				"         WHERE cxp_rd.codemp=cxp_dt_amortizacion.codemp".
				"           AND  cxp_rd.numrecdoc=cxp_dt_amortizacion.numrecdoc".
				"           AND  cxp_rd.codtipdoc=cxp_dt_amortizacion.codtipdoc".
				"           AND  cxp_rd.ced_bene=cxp_dt_amortizacion.ced_bene".
				"           AND  cxp_rd.cod_pro=cxp_dt_amortizacion.cod_pro) AS monantamo".
				"  FROM cxp_rd,cxp_dt_solicitudes ".
				" WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"   AND cxp_rd.estprodoc='C' ".
				"   AND cxp_rd.codemp=cxp_dt_solicitudes.codemp".
				"   AND cxp_rd.numrecdoc=cxp_dt_solicitudes.numrecdoc".
				"   AND cxp_rd.cod_pro=cxp_dt_solicitudes.cod_pro".
				"   AND cxp_rd.ced_bene=cxp_dt_solicitudes.ced_bene".
				"   AND cxp_rd.codtipdoc=cxp_dt_solicitudes.codtipdoc".
				" ".$ls_criterio." ".
				" ORDER BY fecregdoc ";
//		print $ls_sql."<br>";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{

			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_facturasanticipos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_facturasanticipos
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_datos_deduccionrecepcion($as_numrecdoc,$as_codtipdoc,$as_codpro,$as_cedbene,$as_tipded)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_datos_deduccionrecepcion
		//         Access: public
		//	    Arguments: as_numrecdoc     // Numero de Recepcion de Documentos
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las retenciones de una solicitud de pago
		//	   Creado Por: Ing. Luis Lang
		// Fecha Creación: 09/09/2015									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	   $li_monded="";
	   switch($as_tipded)
	   {
	   		case "IVA":
				$ls_variable="AND sigesp_deducciones.iva=1 AND  sigesp_deducciones.islr=0 AND sigesp_deducciones.estretmun=0 AND sigesp_deducciones.estretmil='0'";
			break;
	   		case "ISLR":
				$ls_variable="AND sigesp_deducciones.iva=0 AND  sigesp_deducciones.islr=1 AND sigesp_deducciones.estretmun=0 AND sigesp_deducciones.estretmil='0'";
			break;
	   		case "MUNICIPAL":
				$ls_variable="AND sigesp_deducciones.iva=0 AND  sigesp_deducciones.islr=0 AND sigesp_deducciones.estretmun=1 AND sigesp_deducciones.estretmil='0'";
			break;
	   		case "MIL":
				$ls_variable="AND sigesp_deducciones.iva=0 AND  sigesp_deducciones.islr=0 AND sigesp_deducciones.estretmun=0 AND sigesp_deducciones.estretmil='1'";
			break;
	   }
	   $ls_sql="SELECT SUM(cxp_rd_deducciones.monret) AS totret ".
			   "  FROM cxp_rd_deducciones,sigesp_deducciones".
			   " WHERE cxp_rd_deducciones.codemp='".$this->ls_codemp."' ".$ls_variable.
			   "   AND cxp_rd_deducciones.numrecdoc='".$as_numrecdoc."'".
			   "   AND cxp_rd_deducciones.codtipdoc='".$as_codtipdoc."'".
			   "   AND cxp_rd_deducciones.ced_bene='".$as_cedbene."'".
			   "   AND cxp_rd_deducciones.cod_pro='".$as_codpro."'".
			   "   AND cxp_rd_deducciones.codemp=sigesp_deducciones.codemp".
			   "   AND cxp_rd_deducciones.codded=sigesp_deducciones.codded".
			   " GROUP BY cxp_rd_deducciones.codemp,cxp_rd_deducciones.numrecdoc,cxp_rd_deducciones.codtipdoc,cxp_rd_deducciones.ced_bene,cxp_rd_deducciones.cod_pro";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_datos_deduccionrecepcion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$li_monded=$rs_data->fields["totret"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $li_monded;
	}// end function uf_datos_deduccionrecepcion
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_select_pagosrelacionados($as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_pagosrelacionados
		//         Access: public
		//	    Arguments: as_numsol    // Numero de Solicitud de Pago
		//                 ad_fecregdes  // Fecha de Registro Desde
		//                 ad_fecreghas  // Fecha de Registro Hasta
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las recepciones de documentos en los intervalos indicados
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$la_pagos="";
		$ls_sql=" SELECT cxp_sol_banco.numdoc,cxp_sol_banco.monto".
				"   FROM cxp_solicitudes, cxp_historico_solicitud, cxp_sol_banco,scb_banco ".
				"  WHERE cxp_solicitudes.codemp='".$this->ls_codemp."' ".
				"    AND cxp_historico_solicitud.codemp=cxp_solicitudes.codemp".
				"    AND cxp_sol_banco.codemp=scb_banco.codemp".
				"    AND cxp_sol_banco.codban=scb_banco.codban".
				"	 AND cxp_solicitudes.numsol=cxp_historico_solicitud.numsol".
				"	 AND cxp_historico_solicitud.estprodoc='P'".
				"    AND cxp_historico_solicitud.codemp=cxp_sol_banco.codemp".
				"    AND cxp_historico_solicitud.numsol=cxp_sol_banco.numsol".
				"	 AND cxp_solicitudes.numsol='".$as_numsol."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosolicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				if($la_pagos=="")
				{
					$la_pagos["numdoc"]="";
					$la_pagos["monto"]=0;
				}
				$ls_numdoc=$rs_data->fields["numdoc"];
				$li_monto=$rs_data->fields["monto"];
				$la_pagos["numdoc"]=$la_pagos["numdoc"].$ls_numdoc.", ";
				$la_pagos["monto"]=$la_pagos["monto"]+$li_monto;
				
				$rs_data->MoveNext();	
			}
		}
		if($la_pagos!="")
			$la_pagos["numdoc"]=substr($la_pagos["numdoc"],0,-2);
		return $la_pagos;
	}// end function uf_select_pagosolicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_datos_deduccion($as_numsol,$as_numrecdoc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_datos_deduccion
		//         Access: public
		//	    Arguments: as_numsol     // Numero de solicitud de orden de pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las retenciones de una solicitud de pago
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 04/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido=true;
	   $la_deduccion="";
	   $ls_sql="SELECT sigesp_deducciones.codconret as codded,sigesp_deducciones.desserded ".
			   "	FROM cxp_solicitudes
			  	    join cxp_dt_solicitudes on  ( cxp_solicitudes.codemp=cxp_dt_solicitudes.codemp
									              AND cxp_solicitudes.numsol=cxp_dt_solicitudes.numsol
									              AND cxp_solicitudes.cod_pro=cxp_dt_solicitudes.cod_pro
									              AND cxp_solicitudes.ced_bene=cxp_dt_solicitudes.ced_bene)
					join cxp_rd on ( cxp_dt_solicitudes.codemp=cxp_rd.codemp
											        AND cxp_dt_solicitudes.cod_pro=cxp_rd.cod_pro
													AND cxp_dt_solicitudes.ced_bene=cxp_rd.ced_bene
													AND cxp_dt_solicitudes.codtipdoc=cxp_rd.codtipdoc
													AND cxp_dt_solicitudes.numrecdoc=cxp_rd.numrecdoc )
					join cxp_rd_deducciones on (cxp_rd.codemp=cxp_rd_deducciones.codemp
												AND cxp_rd.cod_pro=cxp_rd_deducciones.cod_pro
												AND cxp_rd.ced_bene=cxp_rd_deducciones.ced_bene
												AND cxp_rd.codtipdoc=cxp_rd_deducciones.codtipdoc
												AND cxp_rd.numrecdoc=cxp_rd_deducciones.numrecdoc)
					join sigesp_deducciones on (sigesp_deducciones.codemp=cxp_rd_deducciones.codemp
												AND sigesp_deducciones.codded=cxp_rd_deducciones.codded) ".
			   " WHERE sigesp_deducciones.islr=1 ".
			   "   AND sigesp_deducciones.iva=0 ".
			   "   AND sigesp_deducciones.estretmun=0 ".
			   "   AND cxp_solicitudes.estprosol<>'A' ".
			   "   AND cxp_rd.numrecdoc='".$as_numrecdoc."'".
			   "   AND cxp_solicitudes.codemp='".$this->ls_codemp."' ".
			   "   AND cxp_solicitudes.numsol='".$as_numsol."' ".
			   " ORDER BY cxp_solicitudes.numsol";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_datos_deduccion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if(!$rs_data->EOF)
			{
				$la_deduccion["codded"]=$rs_data->fields["codded"];
				$la_deduccion["desserded"]=$rs_data->fields["desserded"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $la_deduccion;
	}// end function uf_datos_deduccion
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_acta_retencion($as_codigo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_acta_retencion
		//         Access: public
		//	    Arguments: as_codigo    // 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las actas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$la_pagos="";
		$ls_sql=" SELECT codigo, encabezado, cuerpo, pie, archrtf".
				"   FROM cxp_confacta".
				"  WHERE codemp='".$this->ls_codemp."' ".
				"	 AND codigo='".$as_codigo."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_pagosolicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $rs_data;
	}// end function uf_select_pagosolicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_obtener_valores($as_numrecdoc,$as_codtipdoc,$as_codpro,$as_cedbene)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_acta_retencion
		//         Access: public
		//	    Arguments: as_codigo    // 
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de las actas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 03/06/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$la_pagos="";
		$ls_sql=" SELECT cxp_rd.dencondoc,cxp_rd.montotdoc,cxp_rd.moncardoc,cxp_rd.mondeddoc,cxp_rd_deducciones.monret, cxp_rd.numrecdoc, ".
				"        (SELECT nompro FROM rpc_proveedor".
				"          WHERE cxp_rd.codemp = rpc_proveedor.codemp AND cxp_rd.cod_pro = rpc_proveedor.cod_pro) AS nompro,".
				"        (SELECT dirpro FROM rpc_proveedor".
				"          WHERE cxp_rd.codemp = rpc_proveedor.codemp AND cxp_rd.cod_pro = rpc_proveedor.cod_pro) AS dirpro,".
				"        (SELECT cedrep FROM rpc_proveedor".
				"          WHERE cxp_rd.codemp = rpc_proveedor.codemp AND cxp_rd.cod_pro = rpc_proveedor.cod_pro) AS cedrep,".
				"        (SELECT nomreppro FROM rpc_proveedor".
				"          WHERE cxp_rd.codemp = rpc_proveedor.codemp AND cxp_rd.cod_pro = rpc_proveedor.cod_pro) AS nomreppro,".
				"        (SELECT carrep FROM rpc_proveedor".
				"          WHERE cxp_rd.codemp = rpc_proveedor.codemp AND cxp_rd.cod_pro = rpc_proveedor.cod_pro) AS carrep".
				"   FROM cxp_rd,cxp_rd_deducciones,sigesp_deducciones ".
				"  WHERE cxp_rd.codemp='".$this->ls_codemp."' ".
				"	 AND cxp_rd.numrecdoc='".$as_numrecdoc."'".
				"	 AND cxp_rd.codtipdoc='".$as_codtipdoc."'".
				"	 AND cxp_rd.cod_pro='".$as_codpro."'".
				"	 AND cxp_rd.ced_bene='".$as_cedbene."'".
				"	 AND sigesp_deducciones.retaposol='1'".
				"	 AND cxp_rd.codemp = cxp_rd_deducciones.codemp ".
				"	 AND cxp_rd.numrecdoc = cxp_rd_deducciones.numrecdoc ".
				"	 AND cxp_rd.codtipdoc = cxp_rd_deducciones.codtipdoc ".
				"    AND cxp_rd.cod_pro = cxp_rd_deducciones.cod_pro ".
				"	 AND cxp_rd.ced_bene = cxp_rd_deducciones.ced_bene ".
				"	 AND cxp_rd_deducciones.codemp = sigesp_deducciones.codemp ".
				"	 AND cxp_rd_deducciones.codded = sigesp_deducciones.codded ";
		$rs_data=$this->io_sql->select($ls_sql);
		if ($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_obtener_valores ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			return false;
		}
		return $rs_data;
	}// end function uf_select_pagosolicitudes
	//-----------------------------------------------------------------------------------------------------------------------------------
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_select_estado($as_codest)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_fechapagos
		//         Access: public
		//	    Arguments: as_numsol // Numero de Solicitud de Pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los Pagos de las solicitudes indicadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_desest="";
		$ls_sql="SELECT desest".
				"  FROM sigesp_estados".
				" WHERE sigesp_estados.codpai='058' ".
				"   AND sigesp_estados.codest='".$as_codest."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_desest=$row["desest"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_desest;
	}// end function uf_select_informacionpagos
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_select_municipio($as_codest,$as_codmun)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_select_fechapagos
		//         Access: public
		//	    Arguments: as_numsol // Numero de Solicitud de Pago
		//	      Returns: lb_valido True si se creo el Data stored correctamente ó False si no se creo
		//    Description: Función que busca la información de los Pagos de las solicitudes indicadas
		//	   Creado Por: Ing. Yesenia Moreno / Ing. Luis Lang
		// Fecha Creación: 14/07/2007									Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_denmun="";
		$ls_sql="SELECT denmun".
				"  FROM sigesp_municipio".
				" WHERE sigesp_municipio.codpai='058' ".
				"   AND sigesp_municipio.codest='".$as_codest."'".
				"   AND sigesp_municipio.codmun='".$as_codmun."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_mensajes->message("CLASE->Report MÉTODO->uf_select_informacionpagos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_denmun=$row["denmun"];
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_denmun;
	}// end function uf_select_informacionpagos
	//-----------------------------------------------------------------------------------------------------------------------------------


}
?>
