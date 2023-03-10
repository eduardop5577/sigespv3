<?php
/***********************************************************************************
* @fecha de modificacion: 04/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once("../../../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../../../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../../../shared/class_folder/class_sigesp_int.php");
require_once("../../../shared/class_folder/class_sigesp_int_scg.php");
require_once("../../../shared/class_folder/class_sigesp_int_spg.php");
require_once("../../../shared/class_folder/class_sigesp_int_spi.php");
require_once("sigesp_spg_funciones_reportes.php");

/********************************************************************************************************************************/
class sigesp_spg_reporte
{
    //conexion
	var $sqlca;
	//Instancia de la clase funciones.
    var $is_msg_error;
	var $dts_empresa; // datastore empresa
	var $dts_reporte;
	var $dts_cab;
	var $obj="";
	var $SQL;
	var $siginc;
	var $con;
	var $fun;
	var $io_msg;
	var $sigesp_int_spg;
	var $io_spg_report_funciones;
	
	/********************************************************************************************************************************/
    public function __construct()
    {
		$this->fun=new class_funciones() ;
		$this->siginc=new sigesp_include();
		$this->con=$this->siginc->uf_conectar();
		//$this->con->debug=true;
		$this->SQL=new class_sql($this->con);
		$this->obj=new class_datastore();
		$this->dts_empresa=$_SESSION["la_empresa"];
		$this->dts_cab=new class_datastore();
		$this->dts_reporte=new class_datastore();
		$this->data_mod=new class_datastore();
		$this->data_est=new class_datastore();
		$this->io_msg=new class_mensajes();
		$this->sigesp_int_spg=new class_sigesp_int_spg();
		$this->io_spg_report_funciones=new sigesp_spg_funciones_reportes();
		$this->io_fecha=new class_fecha();
    }
/********************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  " COMPROBANTES FORMATO 1 Y FORMATO 2" //
	////////////////////////////////////////////////////////////////
    function uf_spg_reporte_comprobante_formato1($as_procede_ori,$as_procede_des,$as_comprobante_ori,$as_comprobante_des,
	                                             $adt_fecini,$adt_fecfin,$as_codban='---',$as_ctaban='-------------------------')
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_comprobante_formato1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
	 //                     $as_procede_des  // procede destino
	 //                     $as_comprobante_ori  // comprobante origen
	 //                     $as_comprobante_des  //  comprobante destino
	 //                     $adt_fecini  // fecha  desde
     //              	    $adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 1
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido = false;
		$ls_codemp = $this->dts_empresa["codemp"];
	    $this->dts_reporte->resetds("spg_cuenta");
		if((!empty($as_procede_ori))&&(!empty($as_procede_des)))
		{
			   $ls_cad_where1=" spg_dt_cmp.procede between '".$as_procede_ori."' AND  '".$as_procede_des."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if((!empty($as_comprobante_ori))&&(!empty($as_comprobante_des)))
		{
			   $ls_cad_where2=" spg_dt_cmp.comprobante between '".$as_comprobante_ori."' AND  '".$as_comprobante_des."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if((!empty($adt_fecini))&&(!empty($adt_fecfin)))
		{
			   $ls_cad_where3=" spg_dt_cmp.fecha between '".$adt_fecini."' AND  '".$adt_fecfin."' ";
		}
		else
		{
		 	   $ls_cad_where3="";
		}

		$ls_cadena_concat=$ls_cad_where1.$ls_cad_where2.$ls_cad_where3;
		if (!empty($ls_cadena_concat))
		{
			$ls_cad_where=" AND ";
			if(!empty($ls_cad_where1))
			{
				$ls_cad_concat=$ls_cad_where2.$ls_cad_where3;
				$ls_cond_iif=$this->iif(!empty($ls_cad_concat)," AND ", "");
				$ls_cad_where=$ls_cad_where.$ls_cad_where1.$ls_cond_iif;
			}
			if(!empty($ls_cad_where2))
			{
				$ls_cond_iif=$this->iif(!empty($ls_cad_where3)," AND ", "");
				$ls_cad_where=$ls_cad_where.$ls_cad_where2.$ls_cond_iif;
			}
			if(!empty($ls_cad_where3))
			{
				$ls_cad_where=$ls_cad_where.$ls_cad_where3;
			}
	   }
	   else
	   {
	        $ls_cad_where=" ";
	   }
	   $ls_sql=" SELECT spg_dt_cmp.*, CC.denominacion as dencuenta, OP.denominacion as denoperacion,".
               "        PR.denestpro5, CMP.tipo_destino, CMP.cod_pro, CMP.ced_bene, ".
      		   "	    (SELECT nompro  ".
               "		 FROM rpc_proveedor  ".
       		   "	     WHERE rpc_proveedor.codemp=CMP.codemp AND rpc_proveedor.cod_pro=CMP.cod_pro) AS nompro, ".
      		   "	    (SELECT nombene  ".
               "         FROM rpc_beneficiario ".
               "         WHERE rpc_beneficiario.codemp=CMP.codemp AND rpc_beneficiario.ced_bene=CMP.ced_bene) AS nombene, ".
               "        (SELECT apebene ".
               "         FROM rpc_beneficiario ".
       		   "	     WHERE rpc_beneficiario.codemp=CMP.codemp AND rpc_beneficiario.ced_bene=CMP.ced_bene) AS apebene ".
			   " FROM  spg_dt_cmp spg_dt_cmp, spg_cuentas CC, spg_operaciones OP, spg_ep5 PR, sigesp_cmp CMP ".
			   " WHERE spg_dt_cmp.codemp='".$ls_codemp."' ".$ls_cad_where."  AND  ".
			   "       spg_dt_cmp.codban='".$as_codban."' AND ".
			   "       spg_dt_cmp.ctaban='".$as_ctaban."' AND ".
               "       spg_dt_cmp.codemp=CC.codemp AND CC.codemp=PR.codemp AND PR.codemp=CMP.codemp AND ".
               "       spg_dt_cmp.spg_cuenta = CC.spg_cuenta AND spg_dt_cmp.codestpro1=CC.codestpro1 AND ".
      		   "	   spg_dt_cmp.codestpro2=CC.codestpro2 AND spg_dt_cmp.codestpro3=CC.codestpro3 AND   ".
      		   "       spg_dt_cmp.codestpro4=CC.codestpro4 AND spg_dt_cmp.codestpro5=CC.codestpro5 AND   ".
               "       spg_dt_cmp.operacion=OP.operacion   AND spg_dt_cmp.codestpro1=PR.codestpro1 AND   ".
      		   "	   spg_dt_cmp.codestpro2=PR.codestpro2 AND spg_dt_cmp.codestpro3=PR.codestpro3 AND   ".
      		   "	   spg_dt_cmp.codestpro4=PR.codestpro4 AND spg_dt_cmp.codestpro5=PR.codestpro5 AND   ".
      		   "	   spg_dt_cmp.estcla=CC.estcla AND CC.estcla=PR.estcla AND spg_dt_cmp.procede=CMP.procede AND ".
      		   "       spg_dt_cmp.comprobante=CMP.comprobante AND spg_dt_cmp.fecha=CMP.fecha AND ".
               "	   spg_dt_cmp.codban=CMP.codban AND spg_dt_cmp.ctaban=CMP.ctaban ";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spg_reporte_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			while($row=$this->SQL->fetch_row($rs_data))
			{
               $ls_procede=$row["procede"];
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_codestpro1=$row["codestpro1"];
			   $ls_estcla=$row["estcla"];
			   $ls_denestpro1="";
			   $arrResultado=$this->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
				$ls_denestpro1=$arrResultado['as_denestpro1'];
				$lb_valido=$arrResultado['lb_valido'];
			   if($lb_valido)
			   {
			     $ls_denestpro1=$ls_denestpro1;
			   }
			   $ls_codestpro2=$row["codestpro2"];
			   if($lb_valido)
			   {
			     $ls_denestpro2="";
			     $arrResultado=$this->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
				 $ls_denestpro2=$arrResultado['as_denestpro2'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro2=$ls_denestpro2;
			   }
			   $ls_codestpro3=$row["codestpro3"];
			   if($lb_valido)
			   {
			     $ls_denestpro3="";
			     $arrResultado=$this->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
				 $ls_denestpro3=$arrResultado['as_denestpro3'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro3=$ls_denestpro3;
			   }
			   $ls_codestpro4=$row["codestpro4"];
			   if($lb_valido)
			   {
			     $ls_denestpro4="";
			     $arrResultado=$this->io_spg_report_funciones->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
				 $ls_denestpro4=$arrResultado['as_denestpro4'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro4=$ls_denestpro4;
			   }
			   $ls_codestpro5=$row["codestpro5"];
			   if($lb_valido)
			   {
			     $ls_denestpro5="";
			     $arrResultado=$this->io_spg_report_funciones->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
				 $ls_denestpro5=$arrResultado['as_denestpro5'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro5=$ls_denestpro5;
			   }
			   $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			   $ls_spg_cuenta=$row["spg_cuenta"];
			   $ls_procede_doc=$row["procede_doc"];
			   $ls_documento=$row["documento"];
			   $ls_operacion=$row["operacion"];
			   $ls_descripcion=$row["descripcion"];
			   $ld_monto=$row["monto"];
			   $ls_orden=$row["orden"];
			   $ls_dencuenta=$row["dencuenta"];
			   $ls_denoperacion=$row["denoperacion"];
			   $ls_tipo_destino=$row["tipo_destino"];
			   $ls_cod_pro=$row["cod_pro"];
			   $ls_ced_bene=$row["ced_bene"];
			   $ls_nompro=$row["nompro"];
			   $ls_apebene=$row["apebene"];
			   $ls_nombene=$row["nombene"];

			   $this->dts_reporte->insertRow("procede",$ls_procede);
			   $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
			   $this->dts_reporte->insertRow("fecha",$ldt_fecha);
			   $this->dts_reporte->insertRow("programatica",$ls_programatica);
			   $this->dts_reporte->insertRow("estcla",$ls_estcla);
			   $this->dts_reporte->insertRow("denestpro1",$ls_denestpro1);
			   $this->dts_reporte->insertRow("denestpro2",$ls_denestpro2);
			   $this->dts_reporte->insertRow("denestpro3",$ls_denestpro3);
			   $this->dts_reporte->insertRow("denestpro4",$ls_denestpro4);
			   $this->dts_reporte->insertRow("denestpro5",$ls_denestpro5);
			   $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
			   $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
			   $this->dts_reporte->insertRow("documento",$ls_documento);
			   $this->dts_reporte->insertRow("operacion",$ls_operacion);
			   $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
			   $this->dts_reporte->insertRow("monto",$ld_monto);
			   $this->dts_reporte->insertRow("orden",$ls_orden);
			   $this->dts_reporte->insertRow("dencuenta",$ls_dencuenta);
			   $this->dts_reporte->insertRow("denoperacion",$ls_denoperacion);
			   $this->dts_reporte->insertRow("tipo_destino",$ls_tipo_destino);
			   $this->dts_reporte->insertRow("cod_pro",$ls_cod_pro);
			   $this->dts_reporte->insertRow("ced_bene",$ls_ced_bene);
			   $this->dts_reporte->insertRow("nompro",$ls_nompro);
			   $this->dts_reporte->insertRow("apebene",$ls_apebene);
			   $this->dts_reporte->insertRow("nombene",$ls_nombene);
			   $lb_valido = true;
			}//while
			$this->SQL->free_result($rs_data);
		}//else
  return $lb_valido;
  }// fin uf_spg_reporte_comprobante_formato1
/****************************************************************************************************************************************/
    function uf_spg_reporte_select_comprobante_formato1($as_procede_ori,$as_procede_des,$as_comprobante_ori,$as_comprobante_des,
	                                                    $adt_fecini,$adt_fecfin)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_comprobante_formato1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
	 //                     $as_procede_des  // procede destino
	 //                     $as_comprobante_ori  // comprobante origen
	 //                     $as_comprobante_des  //  comprobante destino
	 //                     $adt_fecini  // fecha  desde
     //              	    $adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 1
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
	    $this->dts_reporte->resetds("spg_cuenta");
		$ls_seguridad="";
	 	$ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spg_dt_cmp',$ls_seguridad);

		

		if((!empty($as_procede_ori))&&(!empty($as_procede_des)))
		{
			   $ls_cad_where1=" spg_dt_cmp.procede between '".$as_procede_ori."' AND  '".$as_procede_des."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if((!empty($as_comprobante_ori))&&(!empty($as_comprobante_des)))
		{
			   $ls_cad_where2=" spg_dt_cmp.comprobante between '".$as_comprobante_ori."' AND  '".$as_comprobante_des."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if((!empty($adt_fecini))&&(!empty($adt_fecfin)))
		{
			   $ls_cad_where3=" spg_dt_cmp.fecha between '".$adt_fecini."' AND  '".$adt_fecfin."' ";
		}
		else
		{
		 	   $ls_cad_where3="";
		}

		$ls_cadena_concat=$ls_cad_where1.$ls_cad_where2.$ls_cad_where3;
		if (!empty($ls_cadena_concat))
		{
			$ls_cad_where=" AND ";

			if(!empty($ls_cad_where1))
			{
				$ls_cad_concat=$ls_cad_where2.$ls_cad_where3;
				$ls_cond_iif=$this->iif(!empty($ls_cad_concat)," AND ", "");
				$ls_cad_where=$ls_cad_where.$ls_cad_where1.$ls_cond_iif;
			}
			if(!empty($ls_cad_where2))
			{
				$ls_cond_iif=$this->iif(!empty($ls_cad_where3)," AND ", "");
				$ls_cad_where=$ls_cad_where.$ls_cad_where2.$ls_cond_iif;
			}
			if(!empty($ls_cad_where3))
			{
				$ls_cad_where=$ls_cad_where.$ls_cad_where3;
			}
	   }
	   else
	   {
	        $ls_cad_where=" ";
	   }
	   if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
	   {
	    $ls_sql=" SELECT distinct rep1.comprobante,rep1.procede,rep1.fecha,rep2.ced_bene,rep2.cod_pro,nompro,".
              "        rep2.apebene,rep2.nombene,rep2.tipo_destino, rep2.codban, rep2.ctaban, rep2.numconcom ".
              " FROM ".
	          "       (SELECT spg_dt_cmp.*, CC.denominacion as dencuenta, OP.denominacion as denoperacion,PR.denestpro5 ".
              "        FROM   spg_dt_cmp spg_dt_cmp, spg_cuentas CC, spg_operaciones OP, spg_ep5 PR ".
              "        WHERE  spg_dt_cmp.codemp=CC.codemp AND CC.codemp=PR.codemp AND PR.codemp='".$ls_codemp."' AND ".
              "               spg_dt_cmp.spg_cuenta = CC.spg_cuenta AND spg_dt_cmp.codestpro1=CC.codestpro1 AND ".
              "               spg_dt_cmp.codestpro2=CC.codestpro2 AND spg_dt_cmp.codestpro3=CC.codestpro3   AND ".
              "               spg_dt_cmp.codestpro4=CC.codestpro4 AND spg_dt_cmp.codestpro5=CC.codestpro5   AND ".
              "               spg_dt_cmp.operacion=OP.operacion AND spg_dt_cmp.codestpro1=PR.codestpro1     AND ".
              "               spg_dt_cmp.codestpro2=PR.codestpro2 AND spg_dt_cmp.codestpro3=PR.codestpro3   AND ".
              "               spg_dt_cmp.codestpro4=PR.codestpro4 AND spg_dt_cmp.codestpro5=PR.codestpro5   AND ".
			  "               spg_dt_cmp.estcla=CC.estcla  AND CC.estcla=PR.estcla  ".
              "               ".$ls_cad_where." ".$ls_seguridad." ".
	          "        ) rep1 ".
	          " left join ".
	          " (SELECT CMP.codemp, CMP.procede, CMP.comprobante, CMP.fecha, CMP.tipo_destino, CMP.cod_pro, CMP.ced_bene, ".
		      "         PRV.nompro, BEN.apebene, BEN.nombene, CMP.codban, CMP.ctaban, CMP.numconcom ".
	          " FROM 	sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
	          " WHERE 	CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$ls_codemp."'AND  ".
              "         CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene) rep2 ".
	          "  on 	rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
			  "         rep1.fecha=rep2.fecha ".
              " ORDER BY rep1.comprobante,rep1.procede,rep1.fecha ";
	   }
	   else
	   {
	    $ls_sql = "SELECT distinct spg_dt_cmp.comprobante, spg_dt_cmp.procede, spg_dt_cmp.fecha, CC.denominacion as dencuenta, ".
                  "                OP.denominacion as denoperacion, ".
                  "                PR.denestpro5, ".
                  "                CMP.tipo_destino, CMP.cod_pro, CMP.numconcom, ".
                  "                CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene, CMP.codban, CMP.ctaban ".
                  " FROM spg_dt_cmp, spg_cuentas CC, spg_operaciones OP, spg_ep5 PR, ".
                  "      sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
                  " WHERE spg_dt_cmp.codemp=CC.codemp AND ".
                  "       CC.codemp=PR.codemp AND ".
                  "       CMP.codemp=PRV.codemp AND ".
                  "       PRV.codemp=BEN.codemp AND ".
                  "       PR.codemp='".$ls_codemp."' AND ".
                  "       spg_dt_cmp.spg_cuenta = CC.spg_cuenta AND ".
                  "       spg_dt_cmp.codestpro1=CC.codestpro1 AND ".
                  "       spg_dt_cmp.codestpro2=CC.codestpro2 AND ".
                  "       spg_dt_cmp.codestpro3=CC.codestpro3 AND ".
                  "       spg_dt_cmp.codestpro4=CC.codestpro4 AND ".
                  "       spg_dt_cmp.codestpro5=CC.codestpro5 AND ".
                  "       spg_dt_cmp.operacion=OP.operacion AND ".
                  "       spg_dt_cmp.codestpro1=PR.codestpro1 AND ".
                  "       spg_dt_cmp.codestpro2=PR.codestpro2 AND ".
                  "       spg_dt_cmp.codestpro3=PR.codestpro3 AND ".
                  "       spg_dt_cmp.codestpro4=PR.codestpro4 AND ".
                  "       spg_dt_cmp.codestpro5=PR.codestpro5 AND ".
      			  "       spg_dt_cmp.estcla=CC.estcla AND ".
                  "       CC.estcla=PR.estcla AND ".
                  "       spg_dt_cmp.procede = CMP.procede AND ".
                  "       spg_dt_cmp.comprobante = CMP.comprobante AND ".
                  "       spg_dt_cmp.fecha = CMP.fecha AND ".
                  "       BEN.codemp='".$ls_codemp."' AND ".
                  "       CMP.cod_pro=PRV.cod_pro AND ".
                  "       CMP.ced_bene=BEN.ced_bene".
				  "       ".$ls_cad_where." ".
				  " ORDER BY spg_dt_cmp.comprobante,spg_dt_cmp.procede,spg_dt_cmp.fecha ";;
	   }
	 
	   // echo  $ls_sql;
		//die();
		$rs_data=$this->SQL->select($ls_sql);
		//var_dump($rs_data);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			$li_numrows=$this->SQL->num_rows($rs_data);
			if($li_numrows<=0)
			{
			   $lb_valido = false;
			}
			else
			{
				while($row=$this->SQL->fetch_row($rs_data))
				{
					//var_dump($row);
				  $ls_comprobante=$row["comprobante"];
				  $ls_procede=$row["procede"];
				  $ldt_fecha=$row["fecha"];
				  $ls_ced_bene=$row["ced_bene"];
				  $ls_cod_pro=$row["cod_pro"];
				  $ls_nompro=$row["nompro"];
				  $ls_apebene=$row["apebene"];
				  $ls_nombene=$row["nombene"];
				  $ls_tipo_destino=$row["tipo_destino"];
				  $ls_codban=$row["codban"];
				  $ls_ctaban=$row["ctaban"];
				  $ls_numconcom=$row["numconcom"];
				  if($ls_comprobante!="0000000APERTURA")
				  {
					  //echo $ls_comprobante;	
				  	  $this->dts_cab->insertRow("comprobante",$ls_comprobante);
					  $this->dts_cab->insertRow("procede",$ls_procede);
					  $this->dts_cab->insertRow("fecha",$ldt_fecha);
					  $this->dts_cab->insertRow("ced_bene",$ls_ced_bene);
					  $this->dts_cab->insertRow("cod_pro",$ls_cod_pro);
					  $this->dts_cab->insertRow("nompro",$ls_nompro);
					  $this->dts_cab->insertRow("apebene",$ls_apebene);
					  $this->dts_cab->insertRow("nombene",$ls_nombene);
					  $this->dts_cab->insertRow("tipo_destino",$ls_tipo_destino);
					  $this->dts_cab->insertRow("codban",$ls_codban);
					  $this->dts_cab->insertRow("ctaban",$ls_ctaban);
					  $this->dts_cab->insertRow("numconcom",$ls_numconcom);
				  }
			    }
			     $li_rows=$this->dts_cab->getRowCount("comprobante");
			      
				  if($li_rows<=0)
				  {
			        $lb_valido = false;
				  }
				  
			}
			$this->SQL->free_result($rs_data);
	    }//else
		return $lb_valido;
  }//uf_spg_reporte_select_comprobante_formato1
/********************************************************************************************************************************/
	function iif($ad_condicional,$ad_true,$ad_false)
	{
		if(eval("return $ad_condicional;"))
		{
			$ad_return=$ad_true;
		}
		else
		{
			$ad_return=($ad_false);
		}
		return $ad_return;
	}
/********************************************************************************************************************************/
    function uf_spg_reporte_select_denestpro1($as_codestpro1,$as_denestpro1,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    27/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro1 ".
             " FROM   spg_ep1 ".
             " WHERE  codemp='".$ls_codemp."' AND codestpro1='".$as_codestpro1."' AND estcla='".$as_estcla."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro1 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		//if($row=$this->SQL->fetch_row($rs_data))
		if(!$rs_data->EOF)
		{
		   $as_denestpro1=$rs_data->fields["denestpro1"];
		}
		else
		{
		 $as_denestpro1="";
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_denestpro1']=$as_denestpro1;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_denestpro1
/********************************************************************************************************************************/
    function uf_spg_reporte_select_denestpro2($as_codestpro1,$as_codestpro2,$as_denestpro2,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro2
	 //         Access :	private
	 //     Argumentos :    $as_codestpro2 // codigo
	 //                     $as_denestpro2  // denominacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    27/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        estcla='".$as_estcla."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro2 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		//if($row=$this->SQL->fetch_row($rs_data))
		if(!$rs_data->EOF)
		{
		   $as_denestpro2=$rs_data->fields["denestpro2"];
		}
		else
		{
		 $as_denestpro2="";
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_denestpro2']=$as_denestpro2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_denestpro1
/********************************************************************************************************************************/
    function uf_spg_reporte_select_denestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_denestpro3,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro3
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3 // codigo
	 //                     $as_denestpro3  // denominacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    27/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro3 ".
             " FROM   spg_ep3 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        codestpro3='".$as_codestpro3."' AND ".
			 "        estcla='".$as_estcla."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro3 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		//if($row=$this->SQL->fetch_row($rs_data))
		if(!$rs_data->EOF)
		{
		   $as_denestpro3=$rs_data->fields["denestpro3"];
		}
		else
		{
		  $as_denestpro3= "";
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_denestpro3']=$as_denestpro3;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_denestpro3
/********************************************************************************************************************************/
    function uf_spg_reporte_select_denestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_denestpro4,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro4
	 //         Access :	private
	 //     Argumentos :    $as_codestpro4 // codigo
	 //                     $as_denestpro4  // denominacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 4
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    31/10/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro4 ".
             " FROM   spg_ep4 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        codestpro3='".$as_codestpro3."' AND ".
			 "        codestpro4='".$as_codestpro4."' AND ".
			 "        estcla='".$as_estcla."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro4 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		//if($row=$this->SQL->fetch_row($rs_data))
		if(!$rs_data->EOF)
		{
		  $as_denestpro4=$rs_data->fields["denestpro4"];
		}
		else
		{
		  $as_denestpro4="";
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_denestpro4']=$as_denestpro4;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_denestpro4
/********************************************************************************************************************************/
    function uf_spg_reporte_select_denestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_denestpro5,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro5
	 //         Access :	private
	 //     Argumentos :    $as_codestpro5 // codigo
	 //                     $as_denestpro5  // denominacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 5
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    31/10/2006         Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro5 ".
             " FROM   spg_ep5 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        codestpro3='".$as_codestpro3."' AND ".
			 "        codestpro4='".$as_codestpro4."' AND ".
			 "        codestpro5='".$as_codestpro5."' AND ".
			 "        estcla='".$as_estcla."'";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro5 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		//if($row=$this->SQL->fetch_row($rs_data))
		if(!$rs_data->EOF)
		{
		   $as_denestpro5=$rs_data->fields["denestpro5"];
		}
		else
		{
		  $as_denestpro5="";
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_denestpro5']=$as_denestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_denestpro5
/********************************************************************************************************************************/
   function  uf_spg_reporte_comprobante_formato2($as_spg_cuenta_ori,$as_spg_cuenta_des,$adt_fecini,$adt_fecfin,$as_comprobante,$as_procede,$rs_data)
   {
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_comprobante_formato2
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta_ori  // cuenta origen
	 //                     $as_spg_cuenta_des  // cuenta destino
	 //                     $adt_fecini  // fecha  desde
     //              	    $adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 2
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	  $lb_valido = false;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");

	  if (empty($as_spg_cuenta_ori) && (!empty($as_spg_cuenta_des)))
	  {
	      $this->io_msg("Debe especificar cuenta DESDE....");
	      return false;
	  }
	  if (!empty($as_spg_cuenta_ori) && empty($as_spg_cuenta_des))
	  {
	      $this->io_msg("Debe especificar cuenta HASTA....");
	      return false;
	  }
      if (!empty($as_spg_cuenta_ori) && (!empty($as_spg_cuenta_des)))
	  {
         if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
		 {
		  $ls_cad_filtro1=" , (SELECT cmp.comprobante,cmp.fecha,cmp.procede ".
                         "    FROM   sigesp_cmp cmp, spg_dt_cmp MOV ".
                         "    WHERE  cmp.codemp=MOV.codemp AND cmp.codemp='".$ls_codemp."' AND cmp.procede=MOV.procede AND ".
                         "           cmp.comprobante=MOV.comprobante AND  MOV.comprobante='".$as_comprobante."' AND ".
	                     "           MOV.procede='".$as_procede."' AND   ".
						 "           cmp.fecha=MOV.fecha AND  MOV.spg_cuenta Between '".$as_spg_cuenta_ori."' AND ".
						 "           '".$as_spg_cuenta_des."' ".
                         "   GROUP BY cmp.comprobante,cmp.fecha,cmp.procede) as curFiltrado ";

		  $ls_cad_filtro2=" AND spg_dt_cmp.comprobante=curFiltrado.comprobante ".
                          " AND spg_dt_cmp.fecha=curFiltrado.fecha ".
                          " AND spg_dt_cmp.procede=curFiltrado.procede ";
		}
		else
		{
		 $ls_cad_filtro1=" , TABLE(MULTISET(SELECT cmp.comprobante,cmp.fecha,cmp.procede ".
                         "    FROM   sigesp_cmp cmp, spg_dt_cmp MOV ".
                         "    WHERE  cmp.codemp=MOV.codemp AND cmp.codemp='".$ls_codemp."' AND cmp.procede=MOV.procede AND ".
                         "           cmp.comprobante=MOV.comprobante AND  MOV.comprobante='".$as_comprobante."' AND ".
	                     "           MOV.procede='".$as_procede."' AND   ".
						 "           cmp.fecha=MOV.fecha AND  MOV.spg_cuenta Between '".$as_spg_cuenta_ori."' AND ".
						 "           '".$as_spg_cuenta_des."' ".
                         "   GROUP BY cmp.comprobante,cmp.fecha,cmp.procede)) curFiltrado ";

		  $ls_cad_filtro2=" AND spg_dt_cmp.comprobante=curFiltrado.comprobante ".
                          " AND spg_dt_cmp.fecha=curFiltrado.fecha ".
                          " AND spg_dt_cmp.procede=curFiltrado.procede ";
		}
	  }
      else
	  {
	     if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
		 {
		  $ls_cad_filtro1=" , (SELECT cmp.comprobante,cmp.fecha,cmp.procede ".
                         "    FROM   sigesp_cmp cmp, spg_dt_cmp MOV ".
                         "    WHERE  cmp.codemp=MOV.codemp AND cmp.codemp='".$ls_codemp."' AND cmp.procede=MOV.procede AND ".
                         "           cmp.comprobante=MOV.comprobante AND ".
	                     "           MOV.procede='".$as_procede."' AND   ".
						 "           cmp.fecha=MOV.fecha ".
                         "    GROUP BY  cmp.comprobante,cmp.fecha,cmp.procede) as curFiltrado ";

		  $ls_cad_filtro2=" AND spg_dt_cmp.comprobante=curFiltrado.comprobante ".
                          " AND spg_dt_cmp.fecha=curFiltrado.fecha ".
                          " AND spg_dt_cmp.procede=curFiltrado.procede ";
		 }
		 else
		 {
		  $ls_cad_filtro1=" , TABLE(MULTISET(SELECT cmp.comprobante,cmp.fecha,cmp.procede ".
                         "    FROM   sigesp_cmp cmp, spg_dt_cmp MOV ".
                         "    WHERE  cmp.codemp=MOV.codemp AND cmp.codemp='".$ls_codemp."' AND cmp.procede=MOV.procede AND ".
                         "           cmp.comprobante=MOV.comprobante AND ".
	                     "           MOV.procede='".$as_procede."' AND   ".
						 "           cmp.fecha=MOV.fecha ".
                         "    GROUP BY  cmp.comprobante,cmp.fecha,cmp.procede)) as curFiltrado ";

		  $ls_cad_filtro2=" AND spg_dt_cmp.comprobante=curFiltrado.comprobante ".
                          " AND spg_dt_cmp.fecha=curFiltrado.fecha ".
                          " AND spg_dt_cmp.procede=curFiltrado.procede ";
		 }
	  }

	  if ((!empty($adt_fecini)) && (!empty($adt_fecfin)))
	  {
	     $ls_cad_where3=" AND spg_dt_cmp.fecha between '".$adt_fecini."' and '".$adt_fecfin."' ";
	  }
      else
	  {
	     $ls_cad_where3="";
	  }
      $ls_cad_where=$ls_cad_where3;

	  if (empty($adt_fecini) && empty($adt_fecfin))
	  {
	    $ls_cad_where= $ls_cad_where;
	  }
	  if ( (!empty($adt_fecini) && empty($adt_fecfin))||(empty($adt_fecini) && !empty($adt_fecfin)))
	  {
	      $ls_cad_where= $ls_cad_where."";
      }
	  if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
	  {
	   $ls_sql=" SELECT * ".
               " FROM (SELECT  spg_dt_cmp.*,  CC.denominacion,  OP.denominacion as denoperacion, PR.denestpro5 ".
               "       FROM 	  spg_dt_cmp spg_dt_cmp, spg_cuentas CC, spg_operaciones OP, spg_ep5 PR ".$ls_cad_filtro1." ".
               "       WHERE   spg_dt_cmp.codemp=CC.codemp AND CC.codemp='".$ls_codemp."' AND  spg_dt_cmp.spg_cuenta = CC.spg_cuenta AND  ".
			   "               spg_dt_cmp.codestpro1=CC.codestpro1 AND spg_dt_cmp.codestpro2=CC.codestpro2 AND spg_dt_cmp.codestpro3=CC.codestpro3  AND ".
               "               spg_dt_cmp.codestpro4=CC.codestpro4 AND spg_dt_cmp.codestpro5=CC.codestpro5 AND spg_dt_cmp.operacion = OP.operacion AND ".
	           "               spg_dt_cmp.codestpro1=PR.codestpro1 AND spg_dt_cmp.codestpro2=PR.codestpro2 AND spg_dt_cmp.codestpro3=PR.codestpro3 AND ".
               "               spg_dt_cmp.codestpro4=PR.codestpro4 AND spg_dt_cmp.codestpro5=PR.codestpro5 AND spg_dt_cmp.estcla=CC.estcla  AND ".
			   "               CC.estcla=PR.estcla  ".$ls_cad_where3."   ".$ls_cad_filtro2." ".
               "       ORDER BY spg_dt_cmp.procede,spg_dt_cmp.comprobante,spg_dt_cmp.fecha, spg_dt_cmp.orden) rep1 ".
               " LEFT JOIN (SELECT CMP.codemp,CMP.procede,CMP.comprobante,CMP.fecha, CMP.tipo_destino, CMP.cod_pro, ".
               "                   CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
	           "            FROM   sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
	           "            WHERE  CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$ls_codemp."' AND ".
			   "                   CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene) rep2 ".
               " on 	rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
               "     rep1.fecha=rep2.fecha ".
               " ORDER BY rep1.procede,rep1.comprobante,rep1.fecha ";
	 }
	 else
	 {
	  $ls_sql= " SELECT rep1.*,rep2.tipo_destino,rep2.cod_pro,rep2.nompro,rep2.ced_bene,rep2.apebene,rep2.nombene ".
               " FROM TABLE(MULTISET(SELECT  spg_dt_cmp.*,  CC.denominacion,  OP.denominacion as denoperacion, PR.denestpro5 ".
               "       FROM 	  spg_dt_cmp spg_dt_cmp, spg_cuentas CC, spg_operaciones OP, spg_ep5 PR ".$ls_cad_filtro1." ".
               "       WHERE   spg_dt_cmp.codemp=CC.codemp AND CC.codemp='".$ls_codemp."' AND  spg_dt_cmp.spg_cuenta = CC.spg_cuenta AND  ".
			   "               spg_dt_cmp.codestpro1=CC.codestpro1 AND spg_dt_cmp.codestpro2=CC.codestpro2 AND spg_dt_cmp.codestpro3=CC.codestpro3  AND ".
               "               spg_dt_cmp.codestpro4=CC.codestpro4 AND spg_dt_cmp.codestpro5=CC.codestpro5 AND spg_dt_cmp.operacion = OP.operacion AND ".
	           "               spg_dt_cmp.codestpro1=PR.codestpro1 AND spg_dt_cmp.codestpro2=PR.codestpro2 AND spg_dt_cmp.codestpro3=PR.codestpro3 AND ".
               "               spg_dt_cmp.codestpro4=PR.codestpro4 AND spg_dt_cmp.codestpro5=PR.codestpro5 AND spg_dt_cmp.estcla=CC.estcla  AND ".
			   "               CC.estcla=PR.estcla  ".$ls_cad_where3."   ".$ls_cad_filtro2." ".
               "       ORDER BY spg_dt_cmp.procede,spg_dt_cmp.comprobante,spg_dt_cmp.fecha, spg_dt_cmp.orden)) rep1 ".
               " LEFT JOIN TABLE(MULTISET(SELECT CMP.codemp,CMP.procede,CMP.comprobante,CMP.fecha, CMP.tipo_destino, CMP.cod_pro, ".
               "                   CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
	           "            FROM   sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
	           "            WHERE  CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$ls_codemp."' AND ".
			   "                   CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene)) rep2 ".
               " on 	rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
               "     rep1.fecha=rep2.fecha ".
               " ORDER BY rep1.procede,rep1.comprobante,rep1.fecha ";
	 }
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spg_reporte_comprobante_formato2 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			$lb_valido = true;

			/*
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_procede=$row["procede"];
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_codestpro1=$row["codestpro1"];
			   $ls_estcla=$row["estcla"];
			   $ls_denestpro1="";
			   $arrResultado=$this->uf_spg_reporte_select_denestpro1($ls_codestpro1,$ls_denestpro1,$ls_estcla);
				$ls_denestpro1=$arrResultado['as_denestpro1'];
				$lb_valido=$arrResultado['lb_valido'];
			   if($lb_valido)
			   { //print "PASO 01";
			     $ls_denestpro1=$ls_denestpro1;
			   }
			   $ls_codestpro2=$row["codestpro2"];
			   if($lb_valido)
			   { //print "PASO 02";
			     $ls_denestpro2="";
			     $arrResultado=$this->uf_spg_reporte_select_denestpro2($ls_codestpro1,$ls_codestpro2,$ls_denestpro2,$ls_estcla);
				 $ls_denestpro2=$arrResultado['as_denestpro2'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro2=$ls_denestpro2;
			   }
			   $ls_codestpro3=$row["codestpro3"];
			   if($lb_valido)
			   { //print "PASO 03";
			     $ls_denestpro3="";
			     $arrResultado=$this->uf_spg_reporte_select_denestpro3($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_denestpro3,$ls_estcla);
				 $ls_denestpro3=$arrResultado['as_denestpro3'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro3=$ls_denestpro3;
			   }
			   $ls_codestpro4=$row["codestpro4"];
			   if($lb_valido)
			   { //print "PASO 04";
			     $ls_denestpro4="";
			     $arrResultado=$this->io_spg_report_funciones->uf_spg_reporte_select_denestpro4($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_denestpro4,$ls_estcla);
				 $ls_denestpro4=$arrResultado['as_denestpro4'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro4=$ls_denestpro4;
			   }
			   $ls_codestpro5=$row["codestpro5"];
			   if($lb_valido)
			   { //print "PASO 05";
			     $ls_denestpro5="";
			     $arrResultado=$this->io_spg_report_funciones->uf_spg_reporte_select_denestpro5($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_denestpro5,$ls_estcla);
				 $ls_denestpro5=$arrResultado['as_denestpro5'];
				 $lb_valido=$arrResultado['lb_valido'];
				 $ls_denestpro5=$ls_denestpro5;
			   }
			   $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			   $ls_spg_cuenta=$row["spg_cuenta"];
			   $ls_procede_doc=$row["procede_doc"];
			   $ls_documento=$row["documento"];
			   $ls_operacion=$row["operacion"];
			   $ls_descripcion=$row["descripcion"];
			   $ld_monto=$row["monto"];
			   $ls_orden=$row["orden"];
			   $ls_denominacion=$row["denominacion"];
			   $ls_denoperacion=$row["denoperacion"];
			   $ls_tipo_destino=$row["tipo_destino"];
			   $ls_cod_pro=$row["cod_pro"];
			   $ls_ced_bene=$row["ced_bene"];
			   $ls_nompro=$row["nompro"];
			   $ls_apebene=$row["apebene"];
			   $ls_nombene=$row["nombene"];

			   $this->dts_reporte->insertRow("procede",$ls_procede);
			   $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
			   $this->dts_reporte->insertRow("fecha",$ldt_fecha);
			   $this->dts_reporte->insertRow("programatica",$ls_programatica);
			   $this->dts_reporte->insertRow("denestpro1",$ls_denestpro1);
			   $this->dts_reporte->insertRow("denestpro2",$ls_denestpro2);
			   $this->dts_reporte->insertRow("denestpro3",$ls_denestpro3);
			   $this->dts_reporte->insertRow("denestpro4",$ls_denestpro4);
			   $this->dts_reporte->insertRow("denestpro5",$ls_denestpro5);
			   $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
			   $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
			   $this->dts_reporte->insertRow("documento",$ls_documento);
			   $this->dts_reporte->insertRow("operacion",$ls_operacion);
			   $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
			   $this->dts_reporte->insertRow("monto",$ld_monto);
			   $this->dts_reporte->insertRow("orden",$ls_orden);
			   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte->insertRow("denoperacion",$ls_denoperacion);
			   $this->dts_reporte->insertRow("denestpro5",$ls_denestpro5);
			   $this->dts_reporte->insertRow("tipo_destino",$ls_tipo_destino);
			   $this->dts_reporte->insertRow("cod_pro",$ls_cod_pro);
			   $this->dts_reporte->insertRow("ced_bene",$ls_ced_bene);
			   $this->dts_reporte->insertRow("nompro",$ls_nompro);
			   $this->dts_reporte->insertRow("apebene",$ls_apebene);
			   $this->dts_reporte->insertRow("nombene",$ls_nombene);
			   $this->dts_reporte->insertRow("estcla",$ls_estcla);
			   $lb_valido = true;
			*/
			//}//while
			//$this->SQL->free_result($rs_data);

		}//else
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
 }//uf_spg_reporte_comprobante_formato2
/********************************************************************************************************************************/
   function  uf_spg_reporte_select_comprobante_formato2($as_spg_cuenta_ori,$as_spg_cuenta_des,$adt_fecini,$adt_fecfin,$rs_data)
   {
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_comprobante_formato2
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta_ori  // cuenta origen
	 //                     $as_spg_cuenta_des  // cuenta destino
	 //                     $adt_fecini  // fecha  desde
     //              	    $adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 2
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_cab->resetds("comprobante");
	  $ls_seguridad="";
	  $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('DT',$ls_seguridad);
	  if (empty($as_spg_cuenta_ori) && (!empty($as_spg_cuenta_des)))
	  {
	      $this->io_msg("Debe especificar cuenta DESDE....");
	      return false;
	  }
	  if (!empty($as_spg_cuenta_ori) && empty($as_spg_cuenta_des))
	  {
	      $this->io_msg("Debe especificar cuenta HASTA....");
	      return false;
	  }
      if (!empty($as_spg_cuenta_ori) && (!empty($as_spg_cuenta_des)))
	  {
         $ls_cad_filtro=" AND DT.spg_cuenta between '".$as_spg_cuenta_ori."' AND '".$as_spg_cuenta_des."' ";
	  }
      else
	  {
	     $ls_cad_filtro="  ";
	  }

	  if ((!empty($adt_fecini)) && (!empty($adt_fecfin)))
	  {
	     $ls_cad_where3=" AND DT.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' ";
	  }
      else
	  {
	     $ls_cad_where3="";
	  }
      $ls_cad_where=$ls_cad_where3;

	  if (empty($adt_fecini) && empty($adt_fecfin))
	  {
	    $ls_cad_where= $ls_cad_where;
	  }
	  if ( (!empty($adt_fecini) && empty($adt_fecfin))||(empty($adt_fecini) && !empty($adt_fecfin)))
	  {
	      $ls_cad_where= $ls_cad_where."";
      }
	/*  if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
	  {
	  $ls_sql=" SELECT distinct rep1.comprobante,rep1.procede,rep1.tipo_destino, rep1.cod_pro, ".
              "                 rep1.ced_bene, rep1.nompro, rep1.apebene, rep1.nombene ".
              " FROM spg_dt_cmp DT, (SELECT CMP.codemp,CMP.procede,CMP.comprobante,CMP.fecha, CMP.tipo_destino, CMP.cod_pro, ".
              "                             CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
              "                      FROM  sigesp_cmp CMP, rpc_proveedor PRV, rpc_beneficiario BEN ".
              "                      WHERE CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$ls_codemp."' AND ".
              "                            CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene)rep1 ".
              " WHERE DT.codemp=rep1.codemp  AND rep1.codemp='".$ls_codemp."' ".$ls_cad_filtro." AND ".
              "       DT.comprobante=rep1.comprobante AND DT.procede=rep1.procede  ".$ls_cad_where." ".$ls_seguridad." order by rep1.comprobante ";
	   }
	   else
	   {*/
		$ls_sql = "SELECT distinct CMP.comprobante, ".
                "         CMP.procede, ".
                "         CMP.tipo_destino, ".
                "         CMP.cod_pro, ".
                "         CMP.ced_bene, PRV.nompro, BEN.apebene, ".
                "         BEN.nombene, ".
                "         CMP.fecha,CMP.numconcom ".
                "    FROM spg_dt_cmp DT, sigesp_cmp CMP, rpc_proveedor PRV, ".
                "          rpc_beneficiario BEN ".
                "     WHERE CMP.codemp=PRV.codemp AND ".
                "           PRV.codemp=BEN.codemp AND ".
                "           BEN.codemp='".$ls_codemp."' AND ".
                "           CMP.cod_pro=PRV.cod_pro AND ".
                "           CMP.ced_bene=BEN.ced_bene AND ".
				"           CMP.codemp = '".$ls_codemp."'".$ls_cad_filtro." AND".
                "           DT.codemp = CMP.codemp AND ".
                "           DT.comprobante = CMP.comprobante AND ".
                "           DT.procede = CMP.procede ".
				"           ".$ls_cad_where.
                " ORDER BY CMP.comprobante";
		/*}*/

		$rs_data=$this->SQL->select($ls_sql);
		//var_dump($rs_data->fields);
		//die();


		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_comprobante_formato2 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			$lb_valido = true;

			/*

			$li_numrows=$this->SQL->num_rows($rs_data);
			if($li_numrows<=0)
			{
			   $lb_valido = false;
			}
			else
			{
				while($row=$this->SQL->fetch_row($rs_data))
				{
				  $ls_comprobante=$row["comprobante"];
				  $ls_procede=$row["procede"];
				  $ls_ced_bene=$row["ced_bene"];
				  $ls_cod_pro=$row["cod_pro"];
				  $ls_nompro=$row["nompro"];
				  $ls_apebene=$row["apebene"];
				  $ls_nombene=$row["nombene"];
				  $ls_tipo_destino=$row["tipo_destino"];
				  if($ls_comprobante!="0000000APERTURA")
				  {
					  $this->dts_cab->insertRow("comprobante",$ls_comprobante);
					  $this->dts_cab->insertRow("procede",$ls_procede);
					  $this->dts_cab->insertRow("ced_bene",$ls_ced_bene);
					  $this->dts_cab->insertRow("cod_pro",$ls_cod_pro);
					  $this->dts_cab->insertRow("nompro",$ls_nompro);
					  $this->dts_cab->insertRow("apebene",$ls_apebene);
					  $this->dts_cab->insertRow("nombene",$ls_nombene);
					  $this->dts_cab->insertRow("tipo_destino",$ls_tipo_destino);
				  }
			      $li_rows=$this->dts_cab->getRowCount("comprobante");
				  if($li_rows<=0)
				  {
			        $lb_valido = false;
				  }
			   }

			}
			$this->SQL->free_result($rs_data);

			*/

	    }//else
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
 }//uf_spg_reporte_select_comprobante_formato2
/********************************************************************************************************************************/
	//////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "LISTADO DE APERTURAS DE CUENTAS" //
	////////////////////////////////////////////////////////////
    function uf_spg_reporte_apertura($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,                                      $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
	                                 $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,$as_codfuefindes,
									 $as_codfuefinhas,$as_estcla,$rs_data)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_apertura
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del listado de apertura
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    16/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	  $lb_valido=true;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estcla;
      $ls_estructura_dstino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estcla;
	  $ls_procede="SPGAPR";

	  if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
			  if($li_estmodest==1)
			  {
				 $ls_tabla="spg_ep3";
				 $ls_cad_DM="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla,DM.spg_cuenta)";
				 $ls_cad_GC="CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.estcla,GC.spg_cuenta)";
				 $ls_cad_DM_SC="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.estcla)";
				 $ls_cad_EP="CONCAT(EP.codestpro1,EP.codestpro2,EP.codestpro3,EP.estcla)";
				 $ls_programatica="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";
                 $ls_cad_DMGC = "   DM.codestpro1 = GC.codestpro1 AND 
                        DM.codestpro2 = GC.codestpro2 AND 
                        DM.codestpro3 = GC.codestpro3 AND 
                        DM.codestpro4 = GC.codestpro4 AND 
                        DM.codestpro5 = GC.codestpro5 AND 
                        DM.estcla = GC.estcla AND 
                        DM.spg_cuenta  = GC.spg_cuenta";
                 $ls_cad_DMSCEP = "(DM.codestpro1 = EP.codestpro1 AND 
                        DM.codestpro2 = EP.codestpro2 AND 
                        DM.codestpro3 = EP.codestpro3 AND 
                        DM.estcla =  EP.estcla)"; 
			  }
			  elseif($li_estmodest==2)
			  {
				 $ls_tabla="spg_ep5";
				 $ls_cad_DM="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla,DM.spg_cuenta)";
				 $ls_cad_GC="CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.estcla,GC.spg_cuenta)";
				 $ls_cad_DM_SC="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";
				 $ls_cad_EP="CONCAT(EP.codestpro1,EP.codestpro2,EP.codestpro3,EP.codestpro4,EP.codestpro5,EP.estcla)";
				 $ls_programatica="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";
				 $ls_cad_DMGC = "   DM.codestpro1 = GC.codestpro1 AND 
                                    DM.codestpro2 = GC.codestpro2 AND 
                                    DM.codestpro3 = GC.codestpro3 AND 
                                    DM.codestpro4 = GC.codestpro4 AND 
                                    DM.codestpro5 = GC.codestpro5 AND 
                                    DM.estcla = GC.estcla AND 
                                    DM.spg_cuenta  = GC.spg_cuenta";
				 $ls_cad_DMSCEP = "(DM.codestpro1 = EP.codestpro1 AND 
                                    DM.codestpro2 = EP.codestpro2 AND 
                                    DM.codestpro3 = EP.codestpro3 AND
									DM.codestpro4 = EP.codestpro4 AND 
                                    DM.codestpro5 = EP.codestpro5 AND  
                                    DM.estcla =  EP.estcla)";
			  }


			 /*$ls_cad_DM="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.spg_cuenta)";
			 $ls_cad_GC="CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.spg_cuenta)";
			 $ls_cad_DM_SC="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5)";
			 $ls_cad_EP="CONCAT(EP.codestpro1,EP.codestpro2,EP.codestpro3,EP.codestpro4,EP.codestpro5)";	  */
	  }
	  /*elseif($ls_gestor=="sybase")
	  {
			 $ls_cad_DM="DM.codestpro1+DM.codestpro2+DM.codestpro3+DM.codestpro4+DM.codestpro5+DM.spg_cuenta";
			 $ls_cad_GC="GC.codestpro1+GC.codestpro2+GC.codestpro3+GC.codestpro4+GC.codestpro5+GC.spg_cuenta";
			 $ls_cad_DM_SC="DM.codestpro1+DM.codestpro2+DM.codestpro3+DM.codestpro4+DM.codestpro5";
			 $ls_cad_EP="EP.codestpro1+EP.codestpro2+EP.codestpro3+EP.codestpro4+EP.codestpro5";
	  }*/
	  else
	  {
			  if($li_estmodest==1)
			  {
				 $ls_tabla="spg_ep3";
				 $ls_cad_DM="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla||DM.spg_cuenta";
				 $ls_cad_GC="GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.estcla||GC.spg_cuenta";
                 $ls_cad_DMGC = "   DM.codestpro1 = GC.codestpro1 AND 
                                    DM.codestpro2 = GC.codestpro2 AND 
                                    DM.codestpro3 = GC.codestpro3 AND 
                                    DM.codestpro4 = GC.codestpro4 AND 
                                    DM.codestpro5 = GC.codestpro5 AND 
                                    DM.estcla = GC.estcla AND 
                                    DM.spg_cuenta  = GC.spg_cuenta";
                 
				 $ls_cad_DM_SC="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.estcla";
				 $ls_cad_EP="EP.codestpro1||EP.codestpro2||EP.codestpro3||EP.estcla";
                 $ls_cad_DMSCEP = "(DM.codestpro1 = EP.codestpro1 AND 
                                    DM.codestpro2 = EP.codestpro2 AND 
                                    DM.codestpro3 = EP.codestpro3 AND 
                                    DM.estcla =  EP.estcla)";
                 $ls_cad_DMCF   = "(DM.codestpro1 = CF.codestpro1 AND
                                    DM.codestpro2 = CF.codestpro2 AND
                                    DM.codestpro3 = CF.codestpro3 AND
									DM.codestpro4 = CF.codestpro4 AND
                                    DM.codestpro5 = CF.codestpro5 AND
                                    DM.estcla =  CF.estcla AND
                                    DM.spg_cuenta =  CF.spg_cuenta)";
				 $ls_programatica="(DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla)";
			  }
			  elseif($li_estmodest==2)
			  {
				 $ls_tabla="spg_ep5";
				 $ls_cad_DM="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla||DM.spg_cuenta";
				 $ls_cad_GC="GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.estcla||GC.spg_cuenta";
				 $ls_cad_DMGC = "   DM.codestpro1 = GC.codestpro1 AND 
                                    DM.codestpro2 = GC.codestpro2 AND 
                                    DM.codestpro3 = GC.codestpro3 AND 
                                    DM.codestpro4 = GC.codestpro4 AND 
                                    DM.codestpro5 = GC.codestpro5 AND 
                                    DM.estcla = GC.estcla AND 
                                    DM.spg_cuenta  = GC.spg_cuenta";
				 $ls_cad_DMSCEP = "(DM.codestpro1 = EP.codestpro1 AND 
                                    DM.codestpro2 = EP.codestpro2 AND 
                                    DM.codestpro3 = EP.codestpro3 AND
									DM.codestpro4 = EP.codestpro4 AND 
                                    DM.codestpro5 = EP.codestpro5 AND  
                                    DM.estcla =  EP.estcla)";
				 $ls_cad_DMCF   = "(DM.codestpro1 = CF.codestpro1 AND
                                    DM.codestpro2 = CF.codestpro2 AND
                                    DM.codestpro3 = CF.codestpro3 AND
									DM.codestpro4 = CF.codestpro4 AND
                                    DM.codestpro5 = CF.codestpro5 AND
                                    DM.estcla =  CF.estcla AND
                                    DM.spg_cuenta =  CF.spg_cuenta)";
                 $ls_cad_DM_SC="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla";
				 $ls_cad_EP="EP.codestpro1||EP.codestpro2||EP.codestpro3||EP.codestpro4||EP.codestpro5||EP.estcla";
				 $ls_programatica="(DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla)";
			  }

			 /*$ls_cad_DM="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.spg_cuenta";
			 $ls_cad_GC="GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.spg_cuenta";
			 $ls_cad_DM_SC="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5";
			 $ls_cad_EP="EP.codestpro1||EP.codestpro2||EP.codestpro3||EP.codestpro4||EP.codestpro5";	  */
	  }
	  
	  if ($as_codfuefindes == '--'){
	  	$ls_sql=" SELECT DM.*, GC.denominacion ".
	  			" FROM   spg_dt_cmp DM, spg_cuentas GC, ".$ls_tabla." EP ".
	  			" WHERE  DM.codemp='".$ls_codemp."' AND DM.procede = '".$ls_procede."' AND ".
	  			"        DM.fecha between '".$adt_fecini."' AND '".$adt_fecfin."'  AND ".
	  			"        ".$ls_programatica."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' AND ".
	  			"        EP.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' AND  ".
	  			"        DM.codemp=GC.codemp AND GC.codemp=EP.codemp AND    ".
	  			"        ".$ls_cad_DMGC." AND  ".$ls_cad_DMSCEP."  ".
	  			" ORDER BY DM.codestpro1, DM.codestpro2, DM.codestpro3, DM.codestpro4, DM.codestpro5, DM.fecha, DM.spg_cuenta ";
	  }
	  else {
	  	$ls_sql=" SELECT DM.codemp,DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla,".
	  			"        DM.procede,DM.comprobante,DM.fecha,DM.spg_cuenta,SUM(CF.monto) AS monto, GC.denominacion, ".
	  			"        DM.procede_doc,DM.documento,DM.operacion,DM.descripcion,DM.orden,DM.descripcion ".
	  			" FROM   spg_dt_cmp DM, spg_cuenta_fuentefinanciamiento CF, spg_cuentas GC, ".$ls_tabla." EP ".
	  			" WHERE  DM.codemp='".$ls_codemp."' AND DM.procede = '".$ls_procede."' AND ".
	  			"        DM.fecha between '".$adt_fecini."' AND '".$adt_fecfin."'  AND ".
	  			"        ".$ls_programatica."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' AND ".
	  			"        CF.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' AND  ".
	  			"        DM.codemp=GC.codemp AND GC.codemp=EP.codemp AND    ".
	  			"        ".$ls_cad_DMGC." AND  ".$ls_cad_DMSCEP." AND ".$ls_cad_DMCF.
	  			" GROUP BY DM.codemp,DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla,".
	  			"        DM.procede,DM.comprobante,DM.fecha,DM.spg_cuenta,GC.denominacion, ".
	  			"        DM.procede_doc,DM.documento,DM.operacion,DM.descripcion,DM.orden,DM.descripcion ".
	  			" ORDER BY DM.codestpro1, DM.codestpro2, DM.codestpro3, DM.codestpro4, DM.codestpro5, DM.fecha, DM.spg_cuenta ";
	  }
	  
/* ORIGINAL
      $ls_sql=" SELECT DM.*, GC.denominacion ".
              " FROM   spg_dt_cmp DM, spg_cuentas GC, ".$ls_tabla." EP ".
              " WHERE  DM.codemp='".$ls_codemp."' AND DM.procede = '".$ls_procede."' AND ".
                 "        DM.fecha between '".$adt_fecini."' AND '".$adt_fecfin."'  AND ".
               "        ".$ls_programatica."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' AND ".
              "        EP.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' AND  ".
              "        DM.codemp=GC.codemp AND GC.codemp=EP.codemp AND    ".
              "        ".$ls_cad_DM." = ".$ls_cad_GC." AND  ".$ls_cad_DM_SC." = ".$ls_cad_EP."  ".
              " ORDER BY DM.codestpro1, DM.codestpro2, DM.codestpro3, DM.codestpro4, DM.codestpro5, DM.fecha, DM.spg_cuenta ";*/


	  /*$ls_sql=" SELECT DM.*, GC.denominacion,EP.denestpro5 ".
              " FROM   spg_dt_cmp DM, spg_cuentas GC, spg_ep5 EP ".
              " WHERE  DM.codemp='".$ls_codemp."' AND DM.codemp=GC.codemp AND GC.codemp=EP.codemp AND ".
			  "        ".$ls_cad_DM." = ".$ls_cad_GC." AND  ".$ls_cad_DM_SC." = ".$ls_cad_EP." AND ".
			  "        DM.procede = '".$ls_procede."' AND ".
		   	  "        DM.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND ".
		 	  "        ".$ls_cad_DM_SC."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
              " ORDER BY DM.codestpro1, DM.codestpro2, DM.codestpro3, DM.codestpro4, DM.codestpro5, DM.fecha, DM.spg_cuenta ";	 */
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report M?TODO->uf_spg_reporte_apertura ERROR->".
		  $this->fun->uf_convertirmsg($this->SQL->message));
	 }
     /*else
	 {
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_codemp=$row["codemp"];
			   $ls_procede=$row["procede"];
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_codestpro1=$row["codestpro1"];
			   $ls_codestpro2=$row["codestpro2"];
			   $ls_codestpro3=$row["codestpro3"];
			   $ls_codestpro4=$row["codestpro4"];
			   $ls_codestpro5=$row["codestpro5"];
			   $ls_estructura_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
			   $ls_spg_cuenta=$row["spg_cuenta"];
			   $ls_procede_doc=$row["procede_doc"];
			   $ls_documento=$row["documento"];
			   $ls_operacion=$row["operacion"];
			   $ls_descripcion=$row["descripcion"];
			   $ld_monto=$row["monto"];
			   $ls_orden=$row["orden"];
			   $ls_denominacion=$row["denominacion"];
			   $ls_estcla=$row["estcla"];

			   $this->dts_reporte->insertRow("programatica",$ls_estructura_programatica);
               $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
               $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
               $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
	           $this->dts_reporte->insertRow("documento",$ls_documento);
			   $this->dts_reporte->insertRow("monto",$ld_monto);
			   $this->dts_reporte->insertRow("estcla",$ls_estcla);
			}
	 }*/
	 // $this->SQL->free_result($rs_data);
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
}//fin uf_spg_reporte_apertura
/********************************************************************************************************************************/
    function uf_spg_reporte_select_apertura($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                        $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
	                                        $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,
											$as_codfuefindes,$as_codfuefinhas,$rs_data,$as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_apertura
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del listado de apertura
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    09/05/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	  $lb_valido=true;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
	  $this->dts_cab->resetds("spg_cuenta");
     // $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_estclades;//.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
    //  $ls_estructura_destino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_estclahas;//.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
	  $ls_procede="SPGAPR";
	  $ls_seguridad="";
     // print "antes de entrar al armado de la consulta, antes de uf_filtro_seguridad_programatica <br> ";
	 $ls_seguridad= $this->io_spg_report_funciones->uf_filtro_seguridad_programatica('DM',$ls_seguridad);
	  if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
			  if($li_estmodest==1)
			  {
				/* $ls_tabla="spg_ep3";
				 $ls_cad_DM="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla,DM.spg_cuenta)";
				 $ls_cad_GC="CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.estcla,GC.spg_cuenta)";
				 $ls_cad_DM_SC="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.estcla)";
				 $ls_cad_EP="CONCAT(EP.codestpro1,EP.codestpro2,EP.codestpro3,EP.estcla)";
				 $ls_programatica="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";*/
				 $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_estclades;
				 $ls_estructura_destino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_estclahas;
				 $ls_tabla       = "spg_ep3";
				 $ls_cad_FI      = "CONCAT(FI.codestpro1,FI.codestpro2,FI.codestpro3,FI.estcla)";
				 $ls_cad_DM      = "CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.estcla)";
				 $ls_cad_DT      = "CONCAT(DT.codestpro1,DT.codestpro2,DT.codestpro3,DM.estcla)";
				 $ls_cad_GC      = "CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.estcla)";
				 $ls_cad_GC_C    = "CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.estcla,GC.spg_cuenta)";
				 $ls_cad_DT_C   =  "CONCAT(DT.codestpro1,DT.codestpro2,DT.codestpro3,DT.estcla,DT.spg_cuenta)";
                 $ls_cad_DMGC   =   "DM.codestpro1 =  GC.codestpro1 AND
                                     DM.codestpro2 =  GC.codestpro2 AND
                                     DM.codestpro3 =  GC.codestpro3 AND 
                                     DM.estcla     =  GC.estcla ";
                 
                 $ls_cad_GCDT_C  =  "GC.codestpro1 =  DT.codestpro1 AND 
                                     GC.codestpro2 =  DT.codestpro2 AND
                                     GC.codestpro3 =  DT.codestpro3 AND 
                                     GC.estcla     =  DT.estcla AND
                                     GC.spg_cuenta =  DT.spg_cuenta ";                 
				 $ls_programatica= "CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.estcla)";
			  }
			  elseif($li_estmodest==2)
			  {
				 /*$ls_tabla="spg_ep5";
				 $ls_cad_DM="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla,DM.spg_cuenta)";
				 $ls_cad_GC="CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.estcla,GC.spg_cuenta)";
				 $ls_cad_DM_SC="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";
				 $ls_cad_EP="CONCAT(EP.codestpro1,EP.codestpro2,EP.codestpro3,EP.codestpro4,EP.codestpro5,EP.estcla)";
				 $ls_programatica="CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";*/
				 $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
				 $ls_estructura_destino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
				 $ls_tabla       = "spg_ep5";
				 $ls_cad_FI      = "CONCAT(FI.codestpro1,FI.codestpro2,FI.codestpro3,FI.codestpro4,FI.codestpro5,FI.estcla)";
				 $ls_cad_DM      = "CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";
				 $ls_cad_DT      = "CONCAT(DT.codestpro1,DT.codestpro2,DT.codestpro3,DT.codestpro4,DT.codestpro5,DM.estcla)";
				 $ls_cad_GC      = "CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.estcla)";
				 $ls_cad_GC_C    = "CONCAT(GC.codestpro1,GC.codestpro2,GC.codestpro3,GC.codestpro4,GC.codestpro5,GC.estcla,GC.spg_cuenta)";
				 $ls_cad_DT_C   =  "CONCAT(DT.codestpro1,DT.codestpro2,DT.codestpro3,DT.codestpro4,DT.codestpro5,DT.estcla,DT.spg_cuenta)";
                 $ls_cad_DMGC   =   "DM.codestpro1 =  GC.codestpro1 AND
                                     DM.codestpro2 =  GC.codestpro2 AND
                                     DM.codestpro3 =  GC.codestpro3 AND 
                                     DM.codestpro4 =  GC.codestpro4 AND 
                                     DM.codestpro5 =  GC.codestpro5 AND 
                                     DM.estcla     =  GC.estcla ";
                 
                 $ls_cad_GCDT_C  =  "GC.codestpro1 =  DT.codestpro1 AND 
                                     GC.codestpro2 =  DT.codestpro2 AND
                                     GC.codestpro3 =  DT.codestpro3 AND 
                                     GC.codestpro4 =  DT.codestpro4 AND 
                                     GC.codestpro5 =  DT.codestpro5 AND 
                                     GC.estcla     =  DT.estcla     AND
                                     GC.spg_cuenta =  DT.spg_cuenta ";                  
				 $ls_programatica= "CONCAT(DM.codestpro1,DM.codestpro2,DM.codestpro3,DM.codestpro4,DM.codestpro5,DM.estcla)";

			  }
	  }
	  else
	  {
			  if($li_estmodest==1)
			  {
				/* $ls_tabla="spg_ep3";
				 $ls_cad_DM="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla||DM.spg_cuenta";
				 $ls_cad_GC="GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.estcla||GC.spg_cuenta";
				 $ls_cad_DM_SC="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.estcla";
				 $ls_cad_EP="EP.codestpro1||EP.codestpro2||EP.codestpro3||EP.estcla";
				 $ls_programatica="(DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla)";*/
				 $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_estclades;
				 $ls_estructura_destino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_estclahas;
				 $ls_tabla       =  "spg_ep3";
				 $ls_cad_DM      =  "DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.estcla";
				 $ls_cad_DT      =  "DT.codestpro1||DT.codestpro2||DT.codestpro3||DT.estcla";
				 $ls_cad_FI      =  "FI.codestpro1||FI.codestpro2||FI.codestpro3||FI.estcla";
				 $ls_cad_GC      =  "GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.estcla";
				 $ls_cad_GC_C    =  "GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.estcla||GC.spg_cuenta";
				 $ls_cad_DT_C   =   "DT.codestpro1||DT.codestpro2||DT.codestpro3||DT.estcla||DT.spg_cuenta";
                 $ls_cad_DMGC   =   "DM.codestpro1 =  GC.codestpro1 AND
                                     DM.codestpro2 =  GC.codestpro2 AND
                                     DM.codestpro3 =  GC.codestpro3 AND 
                                     DM.estcla     =  GC.estcla ";
                 
                 $ls_cad_GCDT_C  =  "GC.codestpro1 =  DT.codestpro1 AND 
                                     GC.codestpro2 =  DT.codestpro2 AND
                                     GC.codestpro3 =  DT.codestpro3 AND 
                                     GC.estcla     =  DT.estcla AND
                                     GC.spg_cuenta =  DT.spg_cuenta ";
				 $ls_programatica= "DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.estcla";
  
			  }
			  elseif($li_estmodest==2)
			  {
				 /*$ls_tabla="spg_ep5";
				 $ls_cad_DM="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla||DM.spg_cuenta";
				 $ls_cad_GC="GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.estcla||GC.spg_cuenta";
				 $ls_cad_DM_SC="DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla";
				 $ls_cad_EP="EP.codestpro1||EP.codestpro2||EP.codestpro3||EP.codestpro4||EP.codestpro5||EP.estcla";
				 $ls_programatica="(DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla)";*/
				 $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
				 $ls_estructura_destino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
				 $ls_tabla       = "spg_ep5";
				 $ls_cad_DM      = "DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla";
				 $ls_cad_DT      = "DT.codestpro1||DT.codestpro2||DT.codestpro3||DT.codestpro4||DT.codestpro5||DM.estcla";
				 $ls_cad_FI      = "FI.codestpro1||FI.codestpro2||FI.codestpro3||FI.codestpro4||FI.codestpro5||FI.estcla";
				 $ls_cad_GC      = "GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.estcla";
				 $ls_cad_GC_C    = "GC.codestpro1||GC.codestpro2||GC.codestpro3||GC.codestpro4||GC.codestpro5||GC.estcla||GC.spg_cuenta";
				 $ls_cad_DT_C    = "DT.codestpro1||DT.codestpro2||DT.codestpro3||DT.codestpro4||DT.codestpro5||DT.estcla||DT.spg_cuenta";
                 $ls_cad_DMGC    = "DM.codestpro1 =  GC.codestpro1 AND
                                    DM.codestpro2 =  GC.codestpro2 AND
                                    DM.codestpro3 =  GC.codestpro3 AND 
                                    DM.codestpro4 =  GC.codestpro4 AND 
                                    DM.codestpro5 =  GC.codestpro5 AND 
                                    DM.estcla     =  GC.estcla ";
                 
                 $ls_cad_GCDT_C  = "GC.codestpro1 =  DT.codestpro1 AND 
                                    GC.codestpro2 =  DT.codestpro2 AND
                                    GC.codestpro3 =  DT.codestpro3 AND 
                                    GC.codestpro4 =  DT.codestpro4 AND 
                                    GC.codestpro5 =  DT.codestpro5 AND 
                                    GC.estcla     =  DT.estcla     AND
                                    GC.spg_cuenta =  DT.spg_cuenta ";                 
				 $ls_programatica= "DM.codestpro1||DM.codestpro2||DM.codestpro3||DM.codestpro4||DM.codestpro5||DM.estcla";
			  }

	  }
          
	   $ls_sql = " SELECT  ".$ls_cad_DM." as programatica FROM  ".$ls_tabla." DM ".
                 "    WHERE ".$ls_cad_DM." ".
                 "        IN (SELECT ".$ls_cad_DT." ".
                 "                FROM spg_dt_cmp DT ".
                 "            JOIN spg_cuentas GC ON DT.codemp=GC.codemp AND ".$ls_cad_GCDT_C." ".
                 "                WHERE DT.procede = '".$ls_procede."' AND DT.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' ".
                 "                      AND ".$ls_cad_DMGC." ".
                 "                      AND ".$ls_programatica." between '".$ls_estructura_origen."' AND '".$ls_estructura_destino."') ".
                 "    AND ".$ls_cad_DM." IN (SELECT ".$ls_cad_FI." FROM spg_dt_fuentefinanciamiento FI ".
                 "                              WHERE  FI.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."') ".$ls_seguridad.
	             "    ORDER BY programatica";

     //echo $ls_sql."<br>";
	 $rs_data=$this->SQL->select($ls_sql); 
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report
		                          M?TODO->uf_spg_reporte_select_apertura
								  ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	 }
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
   }//	uf_spg_reporte_select_apertura
/********************************************************************************************************************************/
	//////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "ACUMULADO POR CUENTAS"   //
	////////////////////////////////////////////////////
    function uf_spg_reporte_acumulado_cuentas($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                          $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											  $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,$ai_nivel,
											  $ab_subniveles,$ai_MenorNivel,$as_cuentades,$as_cuentahas,$as_codfuefindes,
											  $as_codfuefinhas,$as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_acumulado_cuentas
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     ai_nivel    // nivel de la cuenta
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el menor nivel de la cuenta
	 //                     as_codfuefindes  // codigo fuente financiamiento desde solicitado por Gobernacion de Apure
	 //                     as_codfuefinhas  // codigo fuente financiamiento hasta solicitado por Gobernacion de Apure
	 //                     as_estclades   // estatus desde de clasificaicones de la estructura presupuestaria IPSFA
	 //                     as_estclahas   // estatus hasta de clasificaicones de la estructura presupuestaria IPSFA
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Wilmer Brice?o
	 // Fecha Creaci?n :    01/02/2006          Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$lb_existe = false;
		$lb_valido = false;
		$lb_ok = true;
        $ld_total=0;
		$asignado_total=0;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_seguridad="";
	    $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('PCT',$ls_seguridad);
	    $this->dts_reporte->resetds("spg_cuenta");
        $ls_str_sql_where="";
		$dts_cuentas=new class_datastore();
		$arrResultado=$this->uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,                                              $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
                                             $as_codestpro4_des,$as_codestpro5_des,$ls_Sql_Where,$ls_str_estructura_from,
											 $ls_str_estructura_to,$as_estclades,$as_estclahas);
		$ls_Sql_Where=$arrResultado['as_Sql_Where'];
		$ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
		$ls_str_estructura_to=$arrResultado['as_str_estructura_to'];

		$ls_str_sql_where="WHERE PCT.codemp='".$ls_codemp."'";
		$ls_Sql_Where = trim($ls_Sql_Where);

        if (!empty($ls_Sql_Where) )
        {
           $ls_str_sql_where="WHERE PCT.codemp='".$ls_codemp."' AND ".$ls_Sql_Where;
        }
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_ep3";
		  $ls_cadena_fuefin="AND PCT.codestpro1=spg_ep3.codestpro1 AND PCT.codestpro2=spg_ep3.codestpro2 AND PCT.codestpro3=spg_ep3.codestpro3 AND PCT.estcla=spg_ep3.estcla";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_ep5";
		  $ls_cadena_fuefin="AND PCT.codestpro1=spg_ep5.codestpro1 AND PCT.codestpro2=spg_ep5.codestpro2 AND PCT.codestpro3=spg_ep5.codestpro3 AND PCT.codestpro4=spg_ep5.codestpro4 AND PCT.codestpro5=spg_ep5.codestpro5 AND PCT.estcla=spg_ep5.estcla";
		}
        $ls_mysql=" SELECT DISTINCT PCT.spg_cuenta, PCT.nivel, PCT.denominacion,   ".
                  "                 PCT.asignado, PCT.status                       ".
                  " FROM spg_cuentas PCT, ".$ls_tabla."  ".$ls_str_sql_where." AND ".
                  "      PCT.spg_cuenta BETWEEN '".trim($as_cuentades)."' AND '".trim($as_cuentahas)."' AND  ".
                  "      ".$ls_tabla.".codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' AND ".
				  "      (PCT.nivel<='".$ai_nivel."') ".$ls_cadena_fuefin."    ".$ls_seguridad." ".
                  " ORDER BY PCT.spg_cuenta ";
		/*$ls_mysql = " SELECT DISTINCT spg_cuenta, nivel, denominacion, asignado, status ".
		            " FROM   spg_cuentas PCT ".$ls_str_sql_where." AND ".
					"        spg_cuenta BETWEEN  '".$as_cuentades."' AND '".$as_cuentahas."' AND ".
					"        (nivel<='".$ai_nivel."') ".
					" ORDER BY spg_cuenta ";*/

			//		print ($ls_mysql);
		$rs_cuentas=$this->SQL->select($ls_mysql);
		if($rs_cuentas===false)
		{   //error interno sql
		   $this->io_msg->message("Error en Reporte Acumulado Por Cuentas  ".$this->fun->uf_convertirmsg($this->SQL->message));
           return false;
		}
		else
        {
		   while($row=$this->SQL->fetch_row($rs_cuentas))
		   {
			   $ls_spg_cuenta = $row["spg_cuenta"];
			   $ls_denominacion = $row["denominacion"];
			   $li_nivel = $row["nivel"];
			   $ls_status = $row["status"];
			   $ls_asignado = $row["asignado"];
			   if ($ai_nivel=$li_nivel)
			   {
		          $lb_si_va = true;
			   }
			   if ( $ab_subniveles and ($ai_nivel<$li_nivel) )
			   {
			      $lb_si_va = true;
			   }
			   if ($lb_si_va==true)
			   {
		      	  if ($li_nivel < $ai_MenorNivel) { $ai_MenorNivel = $li_nivel; }
				  // Calculo lo Ejecutado y acumulado
					$arrResultado=$this->uf_calcular_acumulado_operaciones_por_cuenta($ls_str_sql_where,$ls_str_estructura_from,				                                                                              $ls_str_estructura_to,$ls_spg_cuenta,
																			 $adt_fecini,$adt_fecfin,$ldec_monto_asignado,$ldec_monto_aumento,$ldec_monto_disminucion,
																			 $ldec_monto_precompromiso,$ldec_monto_compromiso,$ldec_monto_causado,$ldec_monto_pagado,
																			 $ldec_monto_aumento_a,$ldec_monto_disminucion_a,$ldec_monto_precompromiso_a,
																			 $ldec_monto_compromiso_a,$ldec_monto_causado_a,
																			 $ldec_monto_pagado_a,$as_codfuefindes,
																			 $as_codfuefinhas);
					$ldec_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ldec_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ldec_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ldec_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ldec_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ldec_monto_causado=$arrResultado['adec_monto_causado'];
					$ldec_monto_pagado=$arrResultado['adec_monto_pagado'];
					$ldec_monto_aumento_a=$arrResultado['adec_monto_aumento_a'];
					$ldec_monto_disminucion_a=$arrResultado['adec_monto_disminucion_a'];
					$ldec_monto_precompromiso_a=$arrResultado['adec_monto_precompromiso_a'];
					$ldec_monto_compromiso_a=$arrResultado['adec_monto_compromiso_a'];
					$ldec_monto_causado_a=$arrResultado['adec_monto_causado_a'];
					$ldec_monto_pagado_a=$arrResultado['adec_monto_pagado_a'];
					$lb_valido = $arrResultado['lb_valido'];
				  
					if (!$lb_valido)
					{
					   return false;
					}
					$ll_row_found = $this->dts_reporte->find("spg_cuenta",$ls_spg_cuenta);

					if ($ll_row_found == 0)
					{
						$ldec_monto_actualizado = ($ldec_monto_asignado+$ldec_monto_aumento_a+$ldec_monto_aumento-                                                   $ldec_monto_disminucion_a-$ldec_monto_disminucion);
						$ldec_saldo_comprometer = ($ldec_monto_asignado+$ldec_monto_aumento_a+$ldec_monto_aumento-                                                   $ldec_monto_disminucion_a-$ldec_monto_disminucion-
						                           $ldec_monto_precompromiso-$ldec_monto_compromiso);
						//$ldec_por_pagar = ($ldec_monto_causado+$ldec_monto_causado_a)-($ldec_monto_pagado-$ldec_monto_pagado_a);
						$ldec_por_pagar = ($ldec_monto_causado)-($ldec_monto_pagado);
						$this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
						$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
						$this->dts_reporte->insertRow("nivel",$li_nivel);
						$this->dts_reporte->insertRow("asignado",$ldec_monto_asignado);
						$this->dts_reporte->insertRow("aumento",$ldec_monto_aumento);
						$this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion);
						$this->dts_reporte->insertRow("precompromiso",$ldec_monto_precompromiso);
						$this->dts_reporte->insertRow("compromiso",$ldec_monto_compromiso);
						$this->dts_reporte->insertRow("causado",$ldec_monto_causado);
						$this->dts_reporte->insertRow("pagado",$ldec_monto_pagado);
						$this->dts_reporte->insertRow("aumento_a",$ldec_monto_aumento_a);
						$this->dts_reporte->insertRow("disminucion_a",$ldec_monto_disminucion_a);
						$this->dts_reporte->insertRow("precompromiso_a",$ldec_monto_precompromiso_a);
						$this->dts_reporte->insertRow("compromiso_a",$ldec_monto_compromiso_a);
						$this->dts_reporte->insertRow("causado_a",$ldec_monto_causado_a);
						$this->dts_reporte->insertRow("pagado_a",$ldec_monto_pagado_a);
						$this->dts_reporte->insertRow("monto_actualizado",$ldec_monto_actualizado);
						$this->dts_reporte->insertRow("saldo_comprometer",$ldec_saldo_comprometer);
						$this->dts_reporte->insertRow("por_pagar",$ldec_por_pagar);
						$this->dts_reporte->insertRow("status",$ls_status);
		                $lb_valido = true;
					}
		   } // end if
		   if($li_nivel==1)
		   {
			  $ld_total=$ld_total+$ldec_saldo_comprometer;
		   }//if($li_nivel==1)
		 } // end WHILE
	 } //else
	$arrResultado['ai_MenorNivel']=$ai_MenorNivel;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   } // fin function uf_spg_reporte_acumulado_cuentas
/********************************************************************************************************************************/
    function uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
                                           $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
                                           $as_codestpro4_des,$as_codestpro5_des,$as_Sql_Where,$as_str_estructura_from,
                                           $as_str_estructura_to,$as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_obtener_rango_programatica -> proviene de uf_spg_reporte_acumulado_cuentas
	 //       Access :	private
	 //   Argumentos :  as_codestpro1_ori ... as_estprepro5_ori,as_codestpro1_des ... as_estprepro5_des
	 //                 as_estclades   // estatus desde de clasificaicones de la estructura presupuestaria IPSFA
	 //                 as_estclahas   // estatus hasta de clasificaicones de la estructura presupuestaria IPSFA
     //	    Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	Description :	M?todo que determina y ordena el minimo por niveles de la estructuras presupuestarias
     //                 para luego concatenar en una variables de origen y una de destino
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $ls_gestor = $_SESSION["ls_gestor"];
		 if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		 {
		   $ls_concat="CONCAT";
		   $ls_cadena=",";
		 }
		 else
		 {
		   $ls_concat="";
		   $ls_cadena="||";
		 }
		 $ls_CodEstPro1_desde = $as_codestpro1_ori;
		 $ls_CodEstPro1_hasta = $as_codestpro1_des;
		 $ls_CodEstPro2_desde = $as_codestpro2_ori;
		 $ls_CodEstPro2_hasta = $as_codestpro2_des;
		 $ls_CodEstPro3_desde = $as_codestpro3_ori;
		 $ls_CodEstPro3_hasta = $as_codestpro3_des;
		 $ls_CodEstPro4_desde = $as_codestpro4_ori;
		 $ls_CodEstPro4_hasta = $as_codestpro4_des;
		 $ls_CodEstPro5_desde = $as_codestpro5_ori;
		 $ls_CodEstPro5_hasta = $as_codestpro5_des;

         // Nivel 1
		 if (($ls_CodEstPro1_desde!="") && ($ls_CodEstPro1_hasta!="")){
		 	$ls_str_w1  = " ".$ls_concat."(PCT.codestpro1 ";
			$ls_str_w1f = $ls_CodEstPro1_desde;
			$ls_str_w1t = $ls_CodEstPro1_hasta;
		 }
		 else{
			$ls_str_w1  = "";
			$ls_str_w1f = "";
			$ls_str_w1t = "";
		 }
         // Nivel 2
		 if (($ls_CodEstPro2_desde!="") && ($ls_CodEstPro2_hasta!="")){
		 	if($ls_str_w1!=""){
		 		$ls_str_w2  = "".$ls_cadena."PCT.codestpro2 ";
				$ls_str_w2f = $ls_CodEstPro2_desde;
				$ls_str_w2t = $ls_CodEstPro2_hasta;
			}
			else{
				$ls_str_w2  = " ".$ls_concat."(PCT.codestpro2 ";
				$ls_str_w2f = $ls_CodEstPro2_desde;
				$ls_str_w2t = $ls_CodEstPro2_hasta;
			}
		 }
		 else{
		 	$ls_str_w2  = "";
			$ls_str_w2f = "";
			$ls_str_w2t = "";
		 }
         
		 // Nivel 3
		 if (($ls_CodEstPro3_desde!="") && ($ls_CodEstPro3_hasta!="")){
			if ($ls_str_w2!="") {
				$ls_str_w3  = "".$ls_cadena."PCT.codestpro3 ";
				$ls_str_w3f = $ls_CodEstPro3_desde;			
				$ls_str_w3t = $ls_CodEstPro3_hasta;
			}
			else{
				$ls_str_w3  = " ".$ls_concat."(PCT.codestpro3 ";
				$ls_str_w3f = $ls_CodEstPro3_desde;
				$ls_str_w3t = $ls_CodEstPro3_hasta;
			}
		 }
		 else{
			$ls_str_w3  = "";
			$ls_str_w3f = "";
			$ls_str_w3t = "";
		 }
         
		 if ($_SESSION["la_empresa"]["estmodest"]==2) {
			 // Nivel 4
			 if (($ls_CodEstPro4_desde!="") and ($ls_CodEstPro4_hasta!="")){
				if ($ls_str_w3!="") {
					$ls_str_w4  = "".$ls_cadena."PCT.codestpro4 ";
					$ls_str_w4f = $ls_CodEstPro4_desde;
					$ls_str_w4t = $ls_CodEstPro4_hasta;
				}
				else{
					$ls_str_w4  = " ".$ls_concat."(PCT.codestpro4 ";
					$ls_str_w4f = $ls_CodEstPro4_desde;
					$ls_str_w4t = $ls_CodEstPro4_hasta;
				}
			 }
			 else{
				$ls_str_w4  = "";
				$ls_str_w4f = "";
				$ls_str_w4t = "";
			 }
	         // Nivel 5
			 if (($ls_CodEstPro5_desde!="") and ($ls_CodEstPro5_hasta!="")){
			 	if ($ls_str_w4!="") {
					$ls_str_w5  = "".$ls_cadena."PCT.codestpro5 ";
					$ls_str_w5f = $ls_CodEstPro5_desde;
					$ls_str_w5t = $ls_CodEstPro5_hasta;
				}
				else{
					$ls_str_w5  = " ".$ls_concat."(PCT.codestpro5 ";
					$ls_str_w5f = $ls_CodEstPro5_desde;
					$ls_str_w5t = $ls_CodEstPro5_hasta;
				}
			 }
			 else
			 {
				$ls_str_w5  = "";
				$ls_str_w5f = "";
				$ls_str_w5t = "";
			 }
		 }
		 
		 //estatus de clasificacion
		 if (($as_estclades!='') and ($as_estclahas!=''))
		 {
			$ls_str_estcla  = "".$ls_cadena."PCT.estcla)";
			$ls_str_estclaf = $as_estclades;
			$ls_str_estclat = $as_estclahas;
		 }
		 else
		 {
			$ls_str_estcla  = "";
			$ls_str_estclaf = "";
			$ls_str_estclat = "";
		 }

         if (!(empty($ls_str_w1) and empty($ls_str_w2) and empty($ls_str_w3) and empty($ls_str_w4) and empty($ls_str_w5) and empty($ls_str_estcla)))
         {
			 $ls_str_estructura = $ls_str_w1.$ls_str_w2.$ls_str_w3.$ls_str_w4.$ls_str_w5.$ls_str_estcla;
             $li_lent= strlen($ls_str_estructura)-1;
             $ls_str_estructura = substr( $ls_str_estructura ,0,$li_lent);
             $as_str_estructura_from = $ls_str_w1f.$ls_str_w2f.$ls_str_w3f.$ls_str_w4f.$ls_str_w5f.$ls_str_estclaf;
             $as_str_estructura_to = $ls_str_w1t.$ls_str_w2t.$ls_str_w3t.$ls_str_w4t.$ls_str_w5t.$ls_str_estclat;
             $as_Sql_Where=$ls_str_estructura.") >= '".$as_str_estructura_from."' AND ".$ls_str_estructura.") <= '".$as_str_estructura_to."' ";
         }
         else
		 {
             $as_Sql_Where="";
             $as_str_estructura_to="";
             $as_str_estructura_from="";
		 }
		$arrResultado['as_Sql_Where']=$as_Sql_Where;
		$arrResultado['as_str_estructura_from']=$as_str_estructura_from;
		$arrResultado['as_str_estructura_to']=$as_str_estructura_to;
		return $arrResultado;		
    } // fin function uf_obtener_rango_programatica
/********************************************************************************************************************************/
		function uf_calcular_acumulado_operaciones_por_cuenta($as_str_sql_where,$as_str_estructura_from,$as_str_estructura_to,$as_spg_cuenta,
															  $adt_fecini,$adt_fecfin,$adec_monto_asignado,$adec_monto_aumento,$adec_monto_disminucion,
															  $adec_monto_precompromiso,$adec_monto_compromiso,$adec_monto_causado,$adec_monto_pagado,
															  $adec_monto_aumento_a,$adec_monto_disminucion_a,
															  $adec_monto_precompromiso_a,$adec_monto_compromiso_a,
															  $adec_monto_causado_a,$adec_monto_pagado_a,$as_codfuefindes,
															  $as_codfuefinhas)
		{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //	   Function :	uf_calcular_acumulado_operaciones_por_cuenta -> proviene de uf_spg_reporte_acumulado_cuentas
		 //	    Returns :	$lb_valido true si se realizo la funcion con exito o false en caso contrario
		 //	Description :	M?todo  que ejecuta todas funciones de acumulado para asi sacar el acumulado por cuentas
		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		   $lb_valido = true;
		   $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
		   // Global
		   $arrResultado=$this->uf_calcular_acumulado_operacion_asignacion($as_str_sql_where,$as_spg_cuenta,$adec_monto_asignado,$as_codfuefindes,$as_codfuefinhas);
			$adec_monto_asignado=$arrResultado['adec_monto'];
			$lb_valido=$arrResultado['lb_valido'];
		   // acumulado Anteriores
		   if ($lb_valido)
		   {
			  $ls_operacion="aumento";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto_aumento_a,
																		 $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_aumento_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="disminucion";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto_disminucion_a,
																		 $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_disminucion_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="precomprometer";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto_precompromiso_a,
																		 $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_precompromiso_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="comprometer";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto_compromiso_a,
																		 $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_compromiso_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="causar";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto_causado_a,
																		 $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_causado_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="pagar";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto_pagado_a,
																		 $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_pagado_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   // En el Rango
		   if ($lb_valido)
		   {
			  $ls_operacion="aumento";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_aumento,
																		  $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_aumento=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="disminucion";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_disminucion,
																		  $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_disminucion=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="precomprometer";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_precompromiso,
																		  $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_precompromiso=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="comprometer";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_compromiso,
																		  $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_compromiso=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="causar";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_causado,
																		  $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_causado=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="pagar";
			  $arrResultado=$this->uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_pagado,
																		  $ls_operacion,$as_codfuefindes,$as_codfuefinhas);
			  $adec_monto_pagado=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		$arrResultado['adec_monto_asignado']=$adec_monto_asignado;
		$arrResultado['adec_monto_aumento']=$adec_monto_aumento;
		$arrResultado['adec_monto_disminucion']=$adec_monto_disminucion;
		$arrResultado['adec_monto_precompromiso']=$adec_monto_precompromiso;
		$arrResultado['adec_monto_compromiso']=$adec_monto_compromiso;
		$arrResultado['adec_monto_causado']=$adec_monto_causado;
		$arrResultado['adec_monto_pagado']=$adec_monto_pagado;
		$arrResultado['adec_monto_aumento_a']=$adec_monto_aumento_a;
		$arrResultado['adec_monto_disminucion_a']=$adec_monto_disminucion_a;
		$arrResultado['adec_monto_precompromiso_a']=$adec_monto_precompromiso_a;
		$arrResultado['adec_monto_compromiso_a']=$adec_monto_compromiso_a;
		$arrResultado['adec_monto_causado_a']=$adec_monto_causado_a;
		$arrResultado['adec_monto_pagado_a']=$adec_monto_pagado_a;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
		} // fin uf_calcular_acumulado_operaciones_por_cuenta
/****************************************************************************************************************************************/
	    function uf_calcular_acumulado_operacion_asignacion($as_str_sql_where,$as_spg_cuenta,$adec_monto,$as_codfuefindes,$as_codfuefinhas)
		{/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_calcular_acumulado_operacion_asignacion( -> proviene de uf_calcular_acumulado_operaciones_por_cuenta
		//	    Returns :	Retorna monto asignado
		//	Description :	M?todo que consulta y suma lo asignado por cuenta
		// modificado por: Jennifer Rivero
		// Fecha de modificaci?n: 01/02/2008
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_ep3";
		  $ls_cadena_fuefin="AND PCT.codestpro1=spg_ep3.codestpro1 AND PCT.codestpro2=spg_ep3.codestpro2 AND PCT.codestpro3=spg_ep3.codestpro3 AND PCT.estcla=spg_ep3.estcla";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_ep5";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=spg_ep5.codestpro1 AND PCT.codestpro2=spg_ep5.codestpro2 AND PCT.codestpro3=spg_ep5.codestpro3 AND PCT.codestpro4=spg_ep5.codestpro4 AND PCT.codestpro5=spg_ep5.codestpro5 AND PCT.estcla=spg_ep5.estcla";
		}
		$lb_valido = true;
		$ldec_monto=0;
		$ls_codemp = $this->dts_empresa["codemp"];
		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
		  $ls_mysql  = "SELECT Case SUM(monto) WHEN NULL  THEN  0
      						   ELSE SUM(monto)
      					 	   END monto		   ".
                       "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";
		}
		else
		 {
		   $ls_mysql  = "SELECT COALESCE(SUM(monto),0) As monto 			   ".
                        "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";
		 }
        if(!empty($as_str_sql_where))
        {
		  $ls_concat_sql = $as_str_sql_where." AND PCT.operacion=O.operacion AND O.asignar=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
		                    "       ".$ls_tabla.".codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".
							"       ".$ls_cadena_fuefin." ";
        }
		else
		{
           $ls_concat_sql = " WHERE PCT.operacion=O.operacion AND  O.asignar=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
		                    "       spg_ep5.codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".
							"       ".$ls_cadena_fuefin."  ";
		}
		$ls_mysql = $ls_mysql.$ls_concat_sql;
		$rs_data=$this->SQL->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
			$this->io_msg->message("Error en metodo uf_calcular_acumulado_operacion_asignacion----".$this->fun->uf_convertirmsg($this->SQL->message));
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ldec_monto = $row["monto"];
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto = $ldec_monto;
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin function uf_calcular_acumulado_operacion_asignacion
/********************************************************************************************************************************/
	    function uf_calcular_acumulado_operacion_por_rango($as_str_sql_where,$adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto,
													       $as_operacion,$as_codfuefindes,$as_codfuefinhas)
		{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_calcular_acumulado_operacion_por_rango( -> proviene de uf_calcular_acumulado_operaciones_por_cuenta
		//	    Returns :	Retorna monto asignado
		//	Description :	M?todo que consulta y suma dependiando de la operacion(aumento,disminucion,precompromiso,compromiso)
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_ep3";
		  $ls_cadena_fuefin="AND PCT.codestpro1=spg_ep3.codestpro1 AND PCT.codestpro2=spg_ep3.codestpro2 AND PCT.codestpro3=spg_ep3.codestpro3 AND PCT.estcla=spg_ep3.estcla";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_ep5";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=spg_ep5.codestpro1 AND PCT.codestpro2=spg_ep5.codestpro2 AND PCT.codestpro3=spg_ep5.codestpro3 AND PCT.codestpro4=spg_ep5.codestpro4 AND PCT.codestpro5=spg_ep5.codestpro5 AND PCT.estcla=spg_ep5.estcla";
		}
		$lb_valido  = true;
		$ldec_monto = 0;
		$ls_codemp  = $this->dts_empresa["codemp"];

		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
		  $ls_mysql  = "SELECT Case SUM(monto) WHEN NULL  THEN  0
      						   ELSE SUM(monto)
      					 	   END monto		   ".
                       "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";
		}
		else
		 {
		   $ls_mysql  = "SELECT COALESCE(SUM(monto),0) As monto 			   ".
                        "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";
		 }
		/*$ls_mysql   = "SELECT COALESCE(SUM(monto),0) as monto ".
                      "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";*/
        if(!empty($as_str_sql_where))
        {
           $ls_concat_sql = $as_str_sql_where." AND PCT.operacion=O.operacion AND O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
                                    	      " fecha >='".$adt_fecini."' AND fecha <='".$adt_fecfin."'  AND ".
		                    "       ".$ls_tabla.".codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".
							"       ".$ls_cadena_fuefin." ";
        }
		else
		{
           $ls_concat_sql = " WHERE codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
                            "       O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					        "       fecha >='".$adt_fecini."' AND fecha <='".$adt_fecfin."' AND ".
		                    "       ".$ls_tabla.".codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".
							"       ".$ls_cadena_fuefin." ";

		}
        $ls_mysql = $ls_mysql.$ls_concat_sql;
		$rs_data=$this->SQL->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
            $this->io_msg->message("Error en uf_calcular_acumulado_operacion_por_rango ".$this->fun->uf_convertirmsg($this->SQL->message));
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ldec_monto = $row["monto"];
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto = $ldec_monto;
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin function uf_calcular_acumulado_operacion_rango
/********************************************************************************************************************************/
		function uf_calcular_acumulado_operacion_anterior($as_str_sql_where,$adt_fecini,$as_spg_cuenta,$adec_monto,$as_operacion,$as_codfuefindes,$as_codfuefinhas)
		{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	   Function :	uf_calcular_acumulado_operacion_anterior( -> proviene de uf_calcular_acumulado_operaciones_por_cuenta
		//	    Returns :	Retorna monto aumento
		//	Description :	M?todo que consulta y suma el aumento de la cuenta
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_ep3";
		  $ls_cadena_fuefin="AND PCT.codestpro1=spg_ep3.codestpro1 AND PCT.codestpro2=spg_ep3.codestpro2 AND PCT.codestpro3=spg_ep3.codestpro3";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_ep5";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=spg_ep5.codestpro1 AND PCT.codestpro2=spg_ep5.codestpro2 AND PCT.codestpro3=spg_ep5.codestpro3 AND PCT.codestpro4=spg_ep5.codestpro4 AND PCT.codestpro5=spg_ep5.codestpro5";
		}
		$lb_valido = true;
		$ldec_monto=0;
		$ls_codemp = $this->dts_empresa["codemp"];

		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
		  $ls_mysql  = "SELECT Case SUM(monto) WHEN NULL  THEN  0
      						   ELSE SUM(monto)
      					 	   END monto		   ".
                       "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";
		}
		else
		 {
		   $ls_mysql  = "SELECT COALESCE(SUM(monto),0) As monto 			   ".
                        "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." ";
		 }
		/*$ls_mysql  = "SELECT COALESCE(SUM(monto),0) as monto ".
                     "FROM spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." " ;*/
        if(!empty($as_str_sql_where))
        {
           $ls_concat_sql = $as_str_sql_where." AND PCT.operacion=O.operacion AND O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
                                    	      " fecha <'".$adt_fecini."' AND fecha <='".$adt_fecini."' AND ".
		                    "       ".$ls_tabla.".codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".
							"       ".$ls_cadena_fuefin." ";
        }
		else
		{
           $ls_concat_sql = " WHERE codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
                            "       O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					        "       fecha <'".$adt_fecini."' AND ".
		                    "       ".$ls_tabla.".codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' ".
							"       ".$ls_cadena_fuefin." ";

		}
		$ls_mysql = $ls_mysql.$ls_concat_sql;
		$rs_data=$this->SQL->select($ls_mysql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_calcular_acumulado_operacion_anterior ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ldec_monto = $row["monto"];
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto = $ldec_monto;
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // fin function uf_calcular_acumulado_operacion_anterior
/********************************************************************************************************************************/
	/////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "MAYOR AN?LITICO DE CUENTAS" //
	////////////////////////////////////////////////////////
	function uf_spg_reporte_select_mayor_analitico($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                               $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
												   $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,$as_cuenta_from,
												   $as_cuenta_to,$as_orden,$rs_data,$as_codfuefindes,$as_codfuefinhas,
												   $as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing.Yozelin Barragan
	 // Fecha Creaci?n :    16/05/2006          Fecha ?ltima Modificacion :         Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	 $ls_Sql_Where = "";
	 $ls_str_estructura_from = "";
	 $ls_str_estructura_to = "";
	 $ls_seguridad="";
	 $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spg_dt_cmp',$ls_seguridad);
	 if($li_estmodest==1)
	 {
	    $ls_tabla="spg_dt_fuentefinanciamiento";
	    $ls_cadena_fuefin="AND spg_dt_cmp.codestpro1=".$ls_tabla.".codestpro1 AND spg_dt_cmp.codestpro2=".$ls_tabla.".codestpro2 AND ".
						  "    spg_dt_cmp.codestpro3=".$ls_tabla.".codestpro3 AND spg_dt_cmp.estcla=".$ls_tabla.".estcla";
	 }
	 elseif($li_estmodest==2)
	 {
	    $ls_tabla="spg_dt_fuentefinanciamiento";
	    $ls_cadena_fuefin="AND spg_dt_cmp.codestpro1=".$ls_tabla.".codestpro1 AND spg_dt_cmp.codestpro2=".$ls_tabla.".codestpro2 AND ".
		 				  "    spg_dt_cmp.codestpro3=".$ls_tabla.".codestpro3 AND spg_dt_cmp.codestpro4=".$ls_tabla.".codestpro4 AND ".
						  "    spg_dt_cmp.codestpro5=".$ls_tabla.".codestpro5 AND spg_dt_cmp.estcla=".$ls_tabla.".estcla";
	 }
	 $lb_valido = true;
	 $ls_gestor = $_SESSION["ls_gestor"];
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $this->dts_cab->resetds("comprobante");

	 $arrResultado= $this->uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                      $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
										  $as_codestpro4_des,$as_codestpro5_des,$ls_Sql_Where,$ls_str_estructura_from,
										  $ls_str_estructura_to,$as_estclades,$as_estclahas);
	$ls_Sql_Where=$arrResultado['as_Sql_Where'];
	$ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
	$ls_str_estructura_to=$arrResultado['as_str_estructura_to'];
	 $ls_Sql_Where = trim($ls_Sql_Where);
			if ( !empty($ls_Sql_Where) )
			{
			   $ls_str_sql_where=$ls_Sql_Where." AND ";
			   $ls_str_sql_where = str_replace("PCT","spg_dt_cmp",$ls_str_sql_where);
			}
			else
			{
			   $ls_str_sql_where="";
			}

	 $ls_estructura_desde=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
	 $ls_estructura_hasta=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
	 if($as_orden=='F')
	 {
			 $ls_ordenar="TA.fecha";
	 }
	 elseif($as_orden=='D')
	 {
			 $ls_ordenar="TA.Documento";
	 }
	if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	{
		 $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
		 $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
		 $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";

	}
	else
	{
		 $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
		 $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
		 $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";

	}
	if (!empty($ls_estructura_desde)&&!empty($ls_estructura_hasta))
	{
	$ls_sql=" SELECT distinct (".$ls_str_sql_2.") as programatica ".
	 		" FROM  spg_dt_cmp spg_dt_cmp, spg_operaciones OPE, ".$ls_tabla." ".
  			" WHERE spg_dt_cmp.codemp='".$ls_codemp."' AND  spg_dt_cmp.operacion=OPE.operacion AND ".
			"       ".$ls_str_sql_where." ".
			"       spg_dt_cmp.fecha <= '".$adt_fecfin."' AND ".
			"       spg_dt_cmp.codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' AND ".
			"       spg_dt_cmp.codfuefin=".$ls_tabla.".codfuefin ".
	 		"       ".$ls_cadena_fuefin."    ".$ls_seguridad.
			" ORDER BY programatica";
	}
	else
	{
		$ls_sql=" SELECT distinct (".$ls_str_sql_2.") as programatica ".
	 		" FROM  spg_dt_cmp spg_dt_cmp, spg_operaciones OPE, ".$ls_tabla." ".
  			" WHERE spg_dt_cmp.codemp='".$ls_codemp."' AND  spg_dt_cmp.operacion=OPE.operacion AND ".
			"       spg_dt_cmp.codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' AND  ".
			"       spg_dt_cmp.codfuefin=".$ls_tabla.".codfuefin ".
	 		"       ".$ls_cadena_fuefin."    ".$ls_seguridad.
			" ORDER BY programatica";
	}
	//echo $ls_sql.'<br><br>';
	$rs_data=$this->SQL->select($ls_sql);
	if($rs_data===false)
	{   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_mayor_analitico ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	}
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
 }//uf_spg_reporte_select_mayor_analitico
/********************************************************************************************************************************/

function uf_spg_reporte_mayor_analitico2($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$as_codestpro5_ori,
	                                    $as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,$as_codestpro5_des,
	                                    $adt_fecini,$adt_fecfin,$as_cuenta_from,$as_cuenta_to,$as_orden,$as_estcla,$as_codfuefindes,
		 								$as_codfuefinhas,$rs_mov_spg)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Wilmer Brice?o
	 // Fecha Creaci?n :    01/02/2006          Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $lb_valido= true;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");

      $ls_estructura_desde=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estcla;
      $ls_estructura_hasta=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estcla;
      if($as_orden=='F')
	  {
         $ls_ordenar="TA.fecha";
	  }
	  elseif($as_orden=='D')
	  {
         $ls_ordenar="TA.Documento";
	  }

	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
	     $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
		 $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
	     $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
	     $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
	  }


	$ls_sql=" SELECT spg_dt_cmp.procede, spg_dt_cmp.comprobante, spg_dt_cmp.fecha,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
			" spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, ".
			" spg_dt_cmp.descripcion as nombre_prog, C.denominacion, ".
			" CASE spg_dt_cmp.operacion  ".
			" WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
			" END as asignar, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
			" END as aumento, ".
			" CASE spg_dt_cmp.operacion  ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
			" END as compromiso,".
			" CASE spg_dt_cmp.operacion".
			" WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
			" END as disminucion, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
			" END as causado, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
			" END as precompromiso, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
			" END as pago, ".
			" CM.tipo_destino, BE.apebene, BE.nombene, PR.nompro ".
			" FROM spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C, sigesp_cmp CM, rpc_beneficiario BE, rpc_proveedor PR  ".
			" WHERE  spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
			"        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
			"        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
			"        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
			" 	     (".$ls_str_sql_2." = '".$ls_estructura_desde."' ".
			"    	 AND spg_dt_cmp.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."') AND".
			"        spg_dt_cmp.fecha >= '".$adt_fecini."' AND  spg_dt_cmp.fecha <= '".$adt_fecfin."'".
			"    	 AND spg_dt_cmp.codfuefin between '".$as_codfuefindes."' AND '".$as_codfuefinhas."'".
			"		 AND spg_dt_cmp.codemp=CM.codemp ".
			"		 AND spg_dt_cmp.procede=CM.procede ".
			"		 AND spg_dt_cmp.comprobante=CM.comprobante ".
			"		 AND spg_dt_cmp.fecha=CM.fecha ".
			"		 AND spg_dt_cmp.codban=CM.codban ".
			"		 AND spg_dt_cmp.ctaban=CM.ctaban ".
			"		 AND CM.cod_pro=PR.cod_pro ".
			"		 AND CM.ced_bene=BE.ced_bene ".
			" GROUP BY spg_dt_cmp.fecha, spg_dt_cmp.procede, spg_dt_cmp.comprobante,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
			" spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, spg_dt_cmp.descripcion, ".
			" spg_dt_cmp.monto, C.denominacion,OPE.asignar,OPE.aumento,OPE.disminucion,OPE.comprometer,OPE.causar,OPE.pagar, ".
			" CM.tipo_destino, BE.apebene, BE.nombene, PR.nompro".
			" ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
			"          spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
			"          OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento"; //print $ls_sql;
	  //echo $ls_sql.'<br><br><br>';	
	  $rs_mov_spg=$this->SQL->select($ls_sql);
	  if($rs_mov_spg===false)
	  {   // error interno sql
	     $this->io_msg->message("Error en Reporte 1".$this->fun->uf_convertirmsg($this->SQL->message));
         return ;
		 $lb_valido= false;
   	  }
		$arrResultado['rs_data']=$rs_mov_spg;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
}
/********************************************************************************************************************************/


function uf_spg_reporte_mayor_analitico3($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$as_codestpro5_ori,
	                                        $as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,$as_codestpro5_des,
	                                        $adt_fecini,$adt_fecfin,$as_cuenta_from,$as_cuenta_to,$as_orden,$as_estcla, $rs_mov_spg)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Wilmer Brice?o
	 // Fecha Creaci?n :    01/02/2006          Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $lb_valido= true;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");

      $ls_estructura_desde=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estcla;
      $ls_estructura_hasta=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estcla;
      if($as_orden=='F')
	  {
         $ls_ordenar="TA.fecha";
	  }
	  elseif($as_orden=='D')
	  {
         $ls_ordenar="TA.Documento";
	  }

	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
	     $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
		 $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
	     $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
	     $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
	  }


	$ls_sql=" SELECT spg_dt_cmp.procede, spg_dt_cmp.comprobante, spg_dt_cmp.fecha,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
			" spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, ".
			" spg_dt_cmp.descripcion as nombre_prog, C.denominacion, ".
			" CASE spg_dt_cmp.operacion  ".
			" WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
			" END as asignar, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
			" END as aumento, ".
			" CASE spg_dt_cmp.operacion  ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
			" END as compromiso,".
			" CASE spg_dt_cmp.operacion".
			" WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
			" END as disminucion, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
			" END as causado, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
			" END as precompromiso, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
			" END as pago, ".
			" CM.tipo_destino, BE.apebene, BE.nombene, PR.nompro ".
			" FROM spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C, sigesp_cmp CM, rpc_beneficiario BE, rpc_proveedor PR  ".
			" WHERE  spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
			"        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
			"        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
			"        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
			" 	     (".$ls_str_sql_2." = '".$ls_estructura_desde."' ".
			"    	 AND spg_dt_cmp.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."') AND".
			"        spg_dt_cmp.fecha >= '".$adt_fecini."' AND  spg_dt_cmp.fecha <= '".$adt_fecfin."'".
			"		 AND spg_dt_cmp.codemp=CM.codemp ".
			"		 AND spg_dt_cmp.procede=CM.procede ".
			"		 AND spg_dt_cmp.comprobante=CM.comprobante ".
			"		 AND spg_dt_cmp.fecha=CM.fecha ".
			"		 AND spg_dt_cmp.codban=CM.codban ".
			"		 AND spg_dt_cmp.ctaban=CM.ctaban ".
			"		 AND CM.cod_pro=PR.cod_pro ".
			"		 AND CM.ced_bene=BE.ced_bene ".
			" GROUP BY spg_dt_cmp.fecha, spg_dt_cmp.procede, spg_dt_cmp.comprobante,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
			" spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, spg_dt_cmp.descripcion, ".
			" spg_dt_cmp.monto, C.denominacion,OPE.asignar,OPE.aumento,OPE.disminucion,OPE.comprometer,OPE.causar,OPE.pagar, ".
			" CM.tipo_destino, BE.apebene, BE.nombene, PR.nompro".
			" ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
			"          spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
			"          OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento"; //print $ls_sql;

	  $rs_mov_spg=$this->SQL->select($ls_sql);
	  if($rs_mov_spg===false)
	  {   // error interno sql
	     $this->io_msg->message("Error en Reporte 1".$this->fun->uf_convertirmsg($this->SQL->message));
         return ;
		 $lb_valido= false;
   	  }
 	  
     if($rs_mov_spg->RecordCount()==0)
	  {
	  	$sql = "select spg_dt_cmp.* ,
			  	'' as comprobante,
			  	'' as nombre_prog,
			  	'' as fecha,
			  	'' as comprobante,
			  	'' as documento,
				'' as procede,
				'' as procede_doc,	  	
	  			0  as asignar ,
	  			0 as aumento,
	  			0 as compromiso,
				0 as disminucion,
				0 as causado,
				0 as precompromiso,
				0 as pago
	  			from spg_cuentas 
	  			where {$ls_str_sql_2}='{$ls_estructura_desde}'
	  			AND spg_dt_cmp.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."'
	  			AND status='C' order by spg_cuenta asc";	
	  	$rs_mov_spg = $this->SQL->select($sql);
	  //	var_dump($rs_mov_spg);
	  //	die();	
	  }
		$arrResultado['rs_data']=$rs_mov_spg;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
}



function uf_spg_calcular_saldo_anterior ($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$adt_fecdes,
										  $as_spg_cuenta,$as_estcla,$rs_mov_spg1)

    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Wilmer Brice?o
	 // Fecha Creaci?n :    01/02/2006          Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $lb_valido= true;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_estructura_desde=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estcla;


	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
	     $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
		 $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
	     $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
	     $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
	  }


	$ls_sql=" SELECT spg_dt_cmp.procede, spg_dt_cmp.comprobante, spg_dt_cmp.fecha,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
			" spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, ".
			" spg_dt_cmp.descripcion as nombre_prog, C.denominacion, ".
			" CASE spg_dt_cmp.operacion  ".
			" WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
			" END as asignar, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
			" END as aumento, ".
			" CASE spg_dt_cmp.operacion  ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
			" END as compromiso,".
			" CASE spg_dt_cmp.operacion".
			" WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
			" END as disminucion, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
			" END as causado, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
			" END as precompromiso, ".
			" CASE spg_dt_cmp.operacion ".
			" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
			" WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
			" END as pago ".
			" FROM spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C ".
			" WHERE  spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
			"        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
			"        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
			"        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
			" 	     (".$ls_str_sql_2." = '".$ls_estructura_desde."' ".
			"    	 AND spg_dt_cmp.spg_cuenta  = '".$as_spg_cuenta."' ) AND".
			"        spg_dt_cmp.fecha < '".$adt_fecdes."' ".
			" GROUP BY spg_dt_cmp.fecha, spg_dt_cmp.procede, spg_dt_cmp.comprobante,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
			" spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, spg_dt_cmp.descripcion, ".
			" spg_dt_cmp.monto, C.denominacion,OPE.asignar,OPE.aumento,OPE.disminucion,OPE.comprometer,OPE.causar,OPE.pagar ".
			" ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
			"          spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
			"          OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento";


	  $rs_mov_spg1=$this->SQL->select($ls_sql);
	  if($rs_mov_spg1===false)
	  {   // error interno sql
	     $this->io_msg->message("Error en Reporte 2".$this->fun->uf_convertirmsg($this->SQL->message));
         return ;
		 $lb_valido= false;
   	  }

		$arrResultado['rs_data']=$rs_mov_spg1;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;

}


/********************************************************************************************************************************/

	function uf_spg_reporte_mayor_analitico($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$as_codestpro5_ori,
	                                        $as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,$as_codestpro5_des,
	                                        $adt_fecini,$adt_fecfin,$as_cuenta_from,$as_cuenta_to,$as_orden,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Wilmer Brice?o
	 // Fecha Creaci?n :    01/02/2006          Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $lb_existe = false;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_estructura_desde=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estcla;
      $ls_estructura_hasta=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estcla;
      if($as_orden=='F')
	  {
         $ls_ordenar="TA.fecha";
	  }
	  elseif($as_orden=='D')
	  {
         $ls_ordenar="TA.Documento";
	  }

	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
	     $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
		 $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
	     $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
	     $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
	  }


	  /*$ls_sql=" SELECT   spg_dt_cmp.* , C.denominacion,spg_dt_cmp.monto as monto_mov ".
                " FROM   spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C ".
                " WHERE  spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
                "        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
				"        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
				"        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
                " 	     (".$ls_str_sql_2." BETWEEN '".$ls_estructura_desde."' AND '".$ls_estructura_hasta."' ".
				"    	 AND spg_dt_cmp.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."') AND".		//Agregado el rango de cuentas por Ing. Nelson Barraez 20-12-2006
				"        spg_dt_cmp.fecha <= '".$adt_fecfin."'".
                " ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
                "          spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
                "          OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento";*/

	$ls_sql=" SELECT   spg_dt_cmp.* , C.denominacion,spg_dt_cmp.monto as monto_mov ".
                " FROM   spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C ".
                " WHERE  spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
                "        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
				"        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
				"        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
                " 	     (".$ls_str_sql_2." = '".$ls_estructura_desde."' ".
				"    	 AND spg_dt_cmp.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."') AND".		//Agregado el rango de cuentas por Ing. Nelson Barraez 20-12-2006
				"        spg_dt_cmp.fecha <= '".$adt_fecfin."' ".
                " ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
                "          spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
                "          OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento";

	  $rs_mov_spg=$this->SQL->select($ls_sql);
	  if($rs_mov_spg===false)
	  {   // error interno sql
	     $this->io_msg->message("Error en Reporte".$this->fun->uf_convertirmsg($this->SQL->message));
         return ;
   	  }
	  else
	  {
		  $ldec_monto_asignado = 0;
		  $ldec_monto_aumento  = 0;
		  $ldec_monto_disminucion = 0;
		  $ldec_monto_precompromiso = 0;
		  $ldec_monto_compromiso = 0;
		  $ldec_monto_causado = 0;
		  $ldec_monto_pagado = 0;
		  $ldec_monto_asignado_a = 0;
		  $ldec_monto_aumento_a  = 0;
		  $ldec_monto_disminucion_a = 0;
		  $ldec_monto_precompromiso_a = 0;
		  $ldec_monto_compromiso_a = 0;
		  $ldec_monto_causado_a = 0;
		  $ldec_monto_pagado_a = 0;
		  $ldec_monto_por_comprometer = 0;
		  $ls_cuenta_actual = "";
		  $ls_descripcion = "";
		  $lb_previo = false;
	  	  //while($row=$this->SQL->fetch_row($rs_mov_spg))
		  while(!$rs_mov_spg->EOF)
		  {
		      $ls_codestpro1=$rs_mov_spg->fields["codestpro1"];
			  $ls_codestpro2=$rs_mov_spg->fields["codestpro2"];
			  $ls_codestpro3=$rs_mov_spg->fields["codestpro3"];
			  $ls_codestpro4=$rs_mov_spg->fields["codestpro4"];
			  $ls_codestpro5=$rs_mov_spg->fields["codestpro5"];
		      $ls_estructura_programatica = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
 	 	      $ls_spg_cuenta=$rs_mov_spg->fields["spg_cuenta"];
			  $ls_denominacion=$rs_mov_spg->fields["denominacion"];
			  $ls_operacion=$rs_mov_spg->fields["operacion"];
			  $ldec_monto_operacion=$rs_mov_spg->fields["monto"];
			  $ls_procede=$rs_mov_spg->fields["procede"];
			  $ls_procede_doc=$rs_mov_spg->fields["procede_doc"];
			  $ls_comprobante=$rs_mov_spg->fields["comprobante"];
			  $ls_documento =$rs_mov_spg->fields["documento"];
			  $ls_descripcion =$rs_mov_spg->fields["descripcion"];
			  $ldt_fecha=$rs_mov_spg->fields["fecha"];
			  $ls_nombre_prog=$rs_mov_spg->fields["descripcion"];
		      if ($ls_cuenta_actual!=$ls_spg_cuenta)
			  {
				  $ld_monto_actualizado=0;
				  $ldec_monto_asignado_a = 0;
				  $ldec_monto_aumento_a  = 0;
				  $ldec_monto_disminucion_a = 0;
				  $ldec_monto_precompromiso_a = 0;
				  $ldec_monto_compromiso_a = 0;
				  $ldec_monto_causado_a = 0;
				  $ldec_monto_pagado_a = 0;
				  $ldec_monto_por_comprometer = 0;
				  $lb_previo = true;
				  $ls_cuenta_actual = $ls_spg_cuenta;
			  }
			  $ldt_fecha_movimiento = $this->fun->uf_convertirdatetobd($ldt_fecha);
			  $ldt_fecha_movimiento=substr($ldt_fecha_movimiento,0,10);
			  if ($ldt_fecha_movimiento < $adt_fecini )
			  {
				  $ldec_monto_asignado = 0;
				  $ldec_monto_aumento  = 0;
				  $ldec_monto_disminucion = 0;
				  $ldec_monto_precompromiso = 0;
				  $ldec_monto_compromiso = 0;
				  $ldec_monto_causado = 0;
				  $ldec_monto_pagado = 0;
				  $arrResultado=$this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_asignado,$ldec_monto_aumento,$ldec_monto_disminucion,
									                   $ldec_monto_precompromiso,$ldec_monto_compromiso,$ldec_monto_causado,$ldec_monto_pagado);
					$ldec_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ldec_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ldec_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ldec_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ldec_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ldec_monto_causado=$arrResultado['adec_monto_causado'];
					$ldec_monto_pagado=$arrResultado['adec_monto_pagado'];
				  $ldec_monto_por_comprometer = $ldec_monto_por_comprometer+($ldec_monto_asignado+$ldec_monto_aumento-$ldec_monto_disminucion-$ldec_monto_precompromiso-$ldec_monto_compromiso);
				  $ldec_monto_asignado_a = $ldec_monto_asignado_a+$ldec_monto_asignado;
				  $ldec_monto_aumento_a  = $ldec_monto_aumento_a+$ldec_monto_aumento;
				  $ldec_monto_disminucion_a = $ldec_monto_disminucion_a+$ldec_monto_disminucion;
				  $ldec_monto_precompromiso_a = $ldec_monto_precompromiso_a+$ldec_monto_precompromiso;
				  $ldec_monto_compromiso_a = $ldec_monto_compromiso_a+$ldec_monto_compromiso;
				  $ldec_monto_causado_a = $ldec_monto_causado_a+$ldec_monto_causado;
   			      $ldec_monto_pagado_a = $ldec_monto_pagado_a+$ldec_monto_pagado;
			  }

			  if (($ldt_fecha_movimiento >= $adt_fecini) and ($ldt_fecha_movimiento <= $adt_fecfin)
			        and ($ls_spg_cuenta>=$as_cuenta_from) and ($ls_spg_cuenta<=$as_cuenta_to))
			  {
				  $ldec_monto_asignado = 0;
				  $ldec_monto_aumento  = 0;
				  $ldec_monto_disminucion = 0;
				  $ldec_monto_precompromiso = 0;
				  $ldec_monto_compromiso = 0;
				  $ldec_monto_causado     = 0;
				  $ldec_monto_pagado = 0;
				  $arrResultado= $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_asignado,$ldec_monto_aumento,$ldec_monto_disminucion,
									                   $ldec_monto_precompromiso,$ldec_monto_compromiso,$ldec_monto_causado,$ldec_monto_pagado);
					$ldec_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ldec_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ldec_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ldec_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ldec_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ldec_monto_causado=$arrResultado['adec_monto_causado'];
					$ldec_monto_pagado=$arrResultado['adec_monto_pagado'];

				  if ($lb_previo==true)
				  {
					 $ld_monto_actualizado=$ld_monto_actualizado+($ldec_monto_asignado_a+$ldec_monto_aumento_a-$ldec_monto_disminucion_a);//Modificado por Ing Nelson Barraez el 20-12-2006
					 $this->dts_reporte->insertRow("programatica",$ls_estructura_programatica);
					 $this->dts_reporte->insertRow("nombre_prog",$ls_nombre_prog);
					 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("fecha","");
					 $this->dts_reporte->insertRow("procede","");
					 $this->dts_reporte->insertRow("procede_doc","");
					 $this->dts_reporte->insertRow("comprobante","");
					 $this->dts_reporte->insertRow("documento","");
					 $this->dts_reporte->insertRow("descripcion",'SALDOS ANTERIORES');
					 $this->dts_reporte->insertRow("asignado",$ldec_monto_asignado_a);
					 $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento_a);
					 $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion_a);
					 $this->dts_reporte->insertRow("precompromiso",$ldec_monto_precompromiso_a);
					 $this->dts_reporte->insertRow("compromiso",$ldec_monto_compromiso_a);
                     $this->dts_reporte->insertRow("causado",$ldec_monto_causado_a);
					 $this->dts_reporte->insertRow("pagado",$ldec_monto_pagado_a);
					 $this->dts_reporte->insertRow("monto_actualizado",$ld_monto_actualizado);
					 $lb_previo=false;
			      }
					 $this->dts_reporte->insertRow("programatica",$ls_estructura_programatica);
					 $this->dts_reporte->insertRow("nombre_prog",$ls_nombre_prog);
					 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("fecha",$ldt_fecha_movimiento);
					 $this->dts_reporte->insertRow("procede",$ls_procede);
					 $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
					 $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
					 $this->dts_reporte->insertRow("documento",$ls_documento);
					 $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
					 $this->dts_reporte->insertRow("asignado",$ldec_monto_asignado);
					 if($ls_procede=="SPGAPR")
					 {
					   $ldec_monto_asignado_apertura=$ldec_monto_asignado;
					 }
					 else
					 {
					   $ldec_monto_asignado_apertura=$ldec_monto_asignado;
					 }
				     $ld_monto_actualizado=$ld_monto_actualizado+($ldec_monto_asignado_apertura+$ldec_monto_aumento-$ldec_monto_disminucion);//Modificado por Ing Nelson Barraez el 20-12-2006
					 $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento);
					 $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion);
					 $this->dts_reporte->insertRow("precompromiso",$ldec_monto_precompromiso);
					 $this->dts_reporte->insertRow("compromiso",$ldec_monto_compromiso);
                     $this->dts_reporte->insertRow("causado",$ldec_monto_causado);
					 $this->dts_reporte->insertRow("pagado",$ldec_monto_pagado);
					 $this->dts_reporte->insertRow("monto_actualizado",$ld_monto_actualizado);
			  }
		  $rs_mov_spg->MoveNext();
	      }// fin while
 	  }
	  $this->SQL->free_result($rs_mov_spg);
	  return true;
    }  // end function uf_spg_reporte_mayor_analitico
/********************************************************************************************************************************/

/* function uf_spg_reporte_mayor_analitico($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$as_codestpro5_ori,
	                                        $as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,$as_codestpro5_des,
	                                        $adt_fecini,$adt_fecfin,$as_cuenta_from,$as_cuenta_to,$as_orden,$as_estcla,$rs_mov_spg)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Wilmer Brice?o
	 // Fecha Creaci?n :    01/02/2006          Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $lb_existe = false;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_estructura_desde=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estcla;
      $ls_estructura_hasta=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estcla;
      if($as_orden=='F')
	  {
         $ls_ordenar="TA.fecha";
	  }
	  elseif($as_orden=='D')
	  {
         $ls_ordenar="TA.Documento";
	  }

	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
	     $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
		 $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
	     $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
	     $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
	  }


	  $ls_sql=" SELECT   spg_dt_cmp.* , C.denominacion,spg_dt_cmp.monto as monto_mov ".
                " FROM   spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C ".
                " WHERE  spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
                "        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
				"        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
				"        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
                " 	     (".$ls_str_sql_2." BETWEEN '".$ls_estructura_desde."' AND '".$ls_estructura_hasta."' ".
				"    	 AND spg_dt_cmp.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."') ".		//Agregado el rango de cuentas por Ing. Nelson Barraez 20-12-2006
                " ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
                "          spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
                "          OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento";

	  $rs_mov_spg=$this->SQL->select($ls_sql);
	  if($rs_mov_spg===false)
	  {   // error interno sql
	     $this->io_msg->message("Error en Reporte".$this->fun->uf_convertirmsg($this->SQL->message));
         return false;
   	  }
	  else
	  {   return true;
		  $ldec_monto_asignado = 0;
		  $ldec_monto_aumento  = 0;
		  $ldec_monto_disminucion = 0;
		  $ldec_monto_precompromiso = 0;
		  $ldec_monto_compromiso = 0;
		  $ldec_monto_causado = 0;
		  $ldec_monto_pagado = 0;
		  $ldec_monto_asignado_a = 0;
		  $ldec_monto_aumento_a  = 0;
		  $ldec_monto_disminucion_a = 0;
		  $ldec_monto_precompromiso_a = 0;
		  $ldec_monto_compromiso_a = 0;
		  $ldec_monto_causado_a = 0;
		  $ldec_monto_pagado_a = 0;
		  $ldec_monto_por_comprometer = 0;
		  $ls_cuenta_actual = "";
		  $ls_descripcion = "";
		  $lb_previo = false;
	  	  //while($row=$this->SQL->fetch_row($rs_mov_spg))
		  while(!$rs_mov_spg->EOF)
		  {
		      $ls_codestpro1=$rs_mov_spg->fields["codestpro1"];
			  $ls_codestpro2=$rs_mov_spg->fields["codestpro2"];
			  $ls_codestpro3=$rs_mov_spg->fields["codestpro3"];
			  $ls_codestpro4=$rs_mov_spg->fields["codestpro4"];
			  $ls_codestpro5=$rs_mov_spg->fields["codestpro5"];
		      $ls_estructura_programatica = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
 	 	      $ls_spg_cuenta=$rs_mov_spg->fields["spg_cuenta"];
			  $ls_denominacion=$rs_mov_spg->fields["denominacion"];
			  $ls_operacion=$rs_mov_spg->fields["operacion"];
			  $ldec_monto_operacion=$rs_mov_spg->fields["monto"];
			  $ls_procede=$rs_mov_spg->fields["procede"];
			  $ls_procede_doc=$rs_mov_spg->fields["procede_doc"];
			  $ls_comprobante=$rs_mov_spg->fields["comprobante"];
			  $ls_documento =$rs_mov_spg->fields["documento"];
			  $ls_descripcion =$rs_mov_spg->fields["descripcion"];
			  $ldt_fecha=$rs_mov_spg->fields["fecha"];
			  $ls_nombre_prog=$rs_mov_spg->fields["descripcion"];
		      if ($ls_cuenta_actual!=$ls_spg_cuenta)
			  {
				  $ld_monto_actualizado=0;
				  $ldec_monto_asignado_a = 0;
				  $ldec_monto_aumento_a  = 0;
				  $ldec_monto_disminucion_a = 0;
				  $ldec_monto_precompromiso_a = 0;
				  $ldec_monto_compromiso_a = 0;
				  $ldec_monto_causado_a = 0;
				  $ldec_monto_pagado_a = 0;
				  $ldec_monto_por_comprometer = 0;
				  $lb_previo = true;
				  $ls_cuenta_actual = $ls_spg_cuenta;
			  }
			  $ldt_fecha_movimiento = $this->fun->uf_convertirdatetobd($ldt_fecha);
			  $ldt_fecha_movimiento=substr($ldt_fecha_movimiento,0,10);
			  if ($ldt_fecha_movimiento < $adt_fecini )
			  {
				  $ldec_monto_asignado = 0;
				  $ldec_monto_aumento  = 0;
				  $ldec_monto_disminucion = 0;
				  $ldec_monto_precompromiso = 0;
				  $ldec_monto_compromiso = 0;
				  $ldec_monto_causado = 0;
				  $ldec_monto_pagado = 0;
				  $arrResultado= $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_asignado,$ldec_monto_aumento,$ldec_monto_disminucion,
									                   $ldec_monto_precompromiso,$ldec_monto_compromiso,$ldec_monto_causado,$ldec_monto_pagado);
					$ldec_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ldec_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ldec_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ldec_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ldec_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ldec_monto_causado=$arrResultado['adec_monto_causado'];
					$ldec_monto_pagado=$arrResultado['adec_monto_pagado'];
				  $ldec_monto_por_comprometer = $ldec_monto_por_comprometer+($ldec_monto_asignado+$ldec_monto_aumento-$ldec_monto_disminucion-$ldec_monto_precompromiso-$ldec_monto_compromiso);
				  $ldec_monto_asignado_a = $ldec_monto_asignado_a+$ldec_monto_asignado;
				  $ldec_monto_aumento_a  = $ldec_monto_aumento_a+$ldec_monto_aumento;
				  $ldec_monto_disminucion_a = $ldec_monto_disminucion_a+$ldec_monto_disminucion;
				  $ldec_monto_precompromiso_a = $ldec_monto_precompromiso_a+$ldec_monto_precompromiso;
				  $ldec_monto_compromiso_a = $ldec_monto_compromiso_a+$ldec_monto_compromiso;
				  $ldec_monto_causado_a = $ldec_monto_causado_a+$ldec_monto_causado;
   			      $ldec_monto_pagado_a = $ldec_monto_pagado_a+$ldec_monto_pagado;
			  }

			  if (($ldt_fecha_movimiento >= $adt_fecini) and ($ldt_fecha_movimiento <= $adt_fecfin)
			        and ($ls_spg_cuenta>=$as_cuenta_from) and ($ls_spg_cuenta<=$as_cuenta_to))
			  {
				  $ldec_monto_asignado = 0;
				  $ldec_monto_aumento  = 0;
				  $ldec_monto_disminucion = 0;
				  $ldec_monto_precompromiso = 0;
				  $ldec_monto_compromiso = 0;
				  $ldec_monto_causado     = 0;
				  $ldec_monto_pagado = 0;
				 $arrResultado= $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_asignado,$ldec_monto_aumento,$ldec_monto_disminucion,
									                   $ldec_monto_precompromiso,$ldec_monto_compromiso,$ldec_monto_causado,$ldec_monto_pagado);

					$ldec_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ldec_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ldec_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ldec_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ldec_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ldec_monto_causado=$arrResultado['adec_monto_causado'];
					$ldec_monto_pagado=$arrResultado['adec_monto_pagado'];
				  if ($lb_previo==true)
				  {
					 $ld_monto_actualizado=$ld_monto_actualizado+($ldec_monto_asignado_a+$ldec_monto_aumento_a-$ldec_monto_disminucion_a);//Modificado por Ing Nelson Barraez el 20-12-2006
					 $this->dts_reporte->insertRow("programatica",$ls_estructura_programatica);
					 $this->dts_reporte->insertRow("nombre_prog",$ls_nombre_prog);
					 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("fecha","");
					 $this->dts_reporte->insertRow("procede","");
					 $this->dts_reporte->insertRow("procede_doc","");
					 $this->dts_reporte->insertRow("comprobante","");
					 $this->dts_reporte->insertRow("documento","");
					 $this->dts_reporte->insertRow("descripcion",'SALDOS ANTERIORES');
					 $this->dts_reporte->insertRow("asignado",$ldec_monto_asignado_a);
					 $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento_a);
					 $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion_a);
					 $this->dts_reporte->insertRow("precompromiso",$ldec_monto_precompromiso_a);
					 $this->dts_reporte->insertRow("compromiso",$ldec_monto_compromiso_a);
                     $this->dts_reporte->insertRow("causado",$ldec_monto_causado_a);
					 $this->dts_reporte->insertRow("pagado",$ldec_monto_pagado_a);
					 $this->dts_reporte->insertRow("monto_actualizado",$ld_monto_actualizado);
					 $lb_previo=false;
			      }
					 $this->dts_reporte->insertRow("programatica",$ls_estructura_programatica);
					 $this->dts_reporte->insertRow("nombre_prog",$ls_nombre_prog);
					 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("fecha",$ldt_fecha_movimiento);
					 $this->dts_reporte->insertRow("procede",$ls_procede);
					 $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
					 $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
					 $this->dts_reporte->insertRow("documento",$ls_documento);
					 $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
					 $this->dts_reporte->insertRow("asignado",$ldec_monto_asignado);
					 if($ls_procede=="SPGAPR")
					 {
					   $ldec_monto_asignado_apertura=$ldec_monto_asignado;
					 }
					 else
					 {
					   $ldec_monto_asignado_apertura=$ldec_monto_asignado;
					 }
				     $ld_monto_actualizado=$ld_monto_actualizado+($ldec_monto_asignado_apertura+$ldec_monto_aumento-$ldec_monto_disminucion);//Modificado por Ing Nelson Barraez el 20-12-2006
					 $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento);
					 $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion);
					 $this->dts_reporte->insertRow("precompromiso",$ldec_monto_precompromiso);
					 $this->dts_reporte->insertRow("compromiso",$ldec_monto_compromiso);
                     $this->dts_reporte->insertRow("causado",$ldec_monto_causado);
					 $this->dts_reporte->insertRow("pagado",$ldec_monto_pagado);
					 $this->dts_reporte->insertRow("monto_actualizado",$ld_monto_actualizado);
			  }
		  $rs_mov_spg->MoveNext();
	      }// fin while
 	  }
	  //$this->SQL->free_result($rs_mov_spg);
	  return true;
    } */ // end function uf_spg_reporte_mayor_analitico
/********************************************************************************************************************************/
	function uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$adec_monto_asignado,$adec_monto_aumento,$adec_monto_disminucion,
									       $adec_monto_precompromiso,$adec_monto_compromiso,$adec_monto_causado,$adec_monto_pagado)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_acumulado_operaciones->uf_spg_reporte_mayor_analitico
     //	    Returns :	Retorna campos calculados
	 //	Description :	M?todo que mediante la operacion de gasto suma o resta los monto de las operaciones
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $ls_mensaje = $this->sigesp_int_spg->uf_operacion_codigo_mensaje($ls_operacion);

 	 $li_pos_a=strpos($ls_mensaje,"I"); // I-Asignacion
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_asignado = $adec_monto_asignado + $ldec_monto_operacion;
	 }
 	 $li_pos_a=strpos($ls_mensaje,"A"); // A-Aumento
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_aumento = $adec_monto_aumento + $ldec_monto_operacion;
	 }
 	 $li_pos_a=strpos($ls_mensaje,"D"); // D-Disminucion
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_disminucion = $adec_monto_disminucion + $ldec_monto_operacion;
	 }
 	 $li_pos_a=strpos($ls_mensaje,"R"); // R-Pre-Comprometer
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_precompromiso = $adec_monto_precompromiso + $ldec_monto_operacion;
	 }
 	 $li_pos_a=strpos($ls_mensaje,"O"); // O-Comprometer
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_compromiso = $adec_monto_compromiso + $ldec_monto_operacion;
	 }
 	 $li_pos_a=strpos($ls_mensaje,"C"); // C-Causar
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_causado = $adec_monto_causado + $ldec_monto_operacion;
	 }
 	 $li_pos_a=strpos($ls_mensaje,"P"); // P-Pagar
	 if (!($li_pos_a===false))
	 {
	   $adec_monto_pagado = $adec_monto_pagado + $ldec_monto_operacion;
	 }
		$arrResultado['adec_monto_asignado']=$adec_monto_asignado;
		$arrResultado['adec_monto_aumento']=$adec_monto_aumento;
		$arrResultado['adec_monto_disminucion']=$adec_monto_disminucion;
		$arrResultado['adec_monto_precompromiso']=$adec_monto_precompromiso;
		$arrResultado['adec_monto_compromiso']=$adec_monto_compromiso;
		$arrResultado['adec_monto_causado']=$adec_monto_causado;
		$arrResultado['adec_monto_pagado']=$adec_monto_pagado;
		return $arrResultado;		
    } // end uf_calcular_monto_operaciones
/********************************************************************************************************************************/
	//////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  " DISPONIBILIDAD PRESUPUESTARIA " //
	////////////////////////////////////////////////////////////
    function uf_spg_reporte_select_cuenta($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$rs_data,
	                                      $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
										  $as_codestpro4_des,$as_codestpro5_des,$as_cuenta_from,$as_cuenta_to,$as_ckbctasinmov,
										  $as_codfuefindes,$as_codfuefinhas,$as_estclades,$as_estclahas,$ab_nuevoorden=false)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_cuenta
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori .. $as_codestpro5_ori  // codigo de la estructura programatica origen
     //              	    as_codestpro1_des .. $as_codestpro5_des  // codigo de la estructura programatica destino
	 //                     $as_cuenta_from  // cuenta desde
	 //                     $as_cuenta_to  // cuenta hasta
	 //                     $as_ckbctasinmov  //chequear cuentas sin o con movimiento
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso
	 //	   Description :	Seleciona las cuentas con o sin movimiento segun el la condicion enviada por parametro
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    18/04/2006          Fecha ?ltima Modificacion : 07/12/2006          Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_struc_programatica_ori=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
	 $ls_struc_programatica_des=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $ls_gestor = $_SESSION["ls_gestor"];
	 $ls_seguridad="";
	 $ls_orden = '';
	 $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('PCT',$ls_seguridad);
	 if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	 {
	    $ls_concat_programatica="CONCAT(PCT.codestpro1,PCT.codestpro2,PCT.codestpro3,PCT.codestpro4,PCT.codestpro5,PCT.estcla)";
		$ls_cadena_montos="cast(0 as UNSIGNED) as aumentos_a, cast(0 as UNSIGNED) as disminuciones_a, cast(0 as UNSIGNED) as precompromisos_a, cast(0 as UNSIGNED) as compromisos_a";
	 }
	 else
	 {
	    $ls_concat_programatica="PCT.codestpro1||PCT.codestpro2||PCT.codestpro3||PCT.codestpro4||PCT.codestpro5||PCT.estcla";
		$ls_cadena_montos="cast(0 as float) as aumentos_a, cast(0 as float) as disminuciones_a, cast(0 as float) as precompromisos_a, cast(0 as float) as compromisos_a";
	 }
	 
     if($ab_nuevoorden){
	 	$ls_orden = ' ORDER BY PCT.spg_cuenta';
	 }
	 else{
	 	$ls_orden = ' ORDER BY PCT.codestpro1,PCT.codestpro2,PCT.codestpro3,PCT.codestpro4,PCT.codestpro5,PCT.spg_cuenta';
	 }
	 
	 $filtroFuente = '';
	 if (!empty($as_codfuefindes) && !empty($as_codfuefinhas)) {
	 	$filtroFuente = " AND  Pspg_dt_cmp.codfuefin between '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
	 }
	 
	 if($as_ckbctasinmov)
	 {
	    $ls_sql=" SELECT distinct PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4,PCT.codestpro5,PCT.spg_cuenta, ".
                "                 PCT.denominacion, PCT.status,PCT.asignado, PCT.precomprometido, PCT.comprometido,PCT.causado, ".
				"                 PCT.pagado,PCT.aumento, PCT.disminucion,PAT.denestpro5 as denestpro5,PCT.estcla, ".$ls_cadena_montos." ".
                " FROM            spg_cuentas PCT, spg_ep5 PAT, spg_dt_cmp Pspg_dt_cmp ".
                " WHERE           PCT.codestpro1=PAT.codestpro1 AND PCT.codestpro2=PAT.codestpro2 AND PCT.codestpro3=PAT.codestpro3 AND ".
                "                 PCT.codestpro4=PAT.codestpro4 AND PCT.codestpro5=PAT.codestpro5 AND PCT.codestpro1=Pspg_dt_cmp.codestpro1 AND ".
                "                 PCT.codestpro2=Pspg_dt_cmp.codestpro2 AND PCT.codestpro3=Pspg_dt_cmp.codestpro3 AND PCT.codestpro4=Pspg_dt_cmp.codestpro4 AND ".
                "                 PCT.codestpro5=Pspg_dt_cmp.codestpro5 AND PCT.spg_cuenta=Pspg_dt_cmp.spg_cuenta AND ".
	            "                 ".$ls_concat_programatica." ".
                "                 between '".$ls_struc_programatica_ori."' AND '".$ls_struc_programatica_des."' ".$filtroFuente.
	    		"                 AND PCT.spg_cuenta between '".$as_cuenta_from."' AND '".$as_cuenta_to."' ".$ls_seguridad.$ls_orden;
	 }
	 else
	 {
	    $ls_sql=" SELECT distinct PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4,PCT.codestpro5, PCT.spg_cuenta, ".
		        "                 PCT.denominacion, PCT.status,PCT.asignado, PCT.precomprometido, PCT.comprometido, PCT.causado, ".
				"                 PCT.pagado,PCT.aumento,PCT.disminucion,PAT.denestpro5 ,cast(0 as int) as aumentosA, ".
				"                 cast(0 as int) as disminucionesA,cast(0 as int) as precompromisosA, ".
				"                 cast(0 as int) as compromisosA,PCT.estcla ".
                " FROM            spg_cuentas PCT, spg_ep5 PAT ".
                " WHERE           PCT.codestpro1=PAT.codestpro1 AND PCT.codestpro2=PAT.codestpro2 AND ".
				"                 PCT.codestpro3=PAT.codestpro3 AND PCT.codestpro4=PAT.codestpro4 AND ".
				"                 PCT.codestpro5=PAT.codestpro5 AND ".
				"                 ".$ls_concat_programatica." ".
                "                 between '".$ls_struc_programatica_ori."' AND '".$ls_struc_programatica_des."' ".$filtroFuente.
				"                 AND PCT.spg_cuenta between  '".$as_cuenta_from."' AND '".$as_cuenta_to."' ".$ls_seguridad.$ls_orden;
	 }
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {
		$lb_valido=false;
		$this->io_msg->message("CLASE->sigesp_spg_class_report M?TODO->uf_spg_reporte_select_cuenta ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
   	 }
		$arrResultado['rs_data']=$rs_data;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}//fin
/********************************************************************************************************************************/
    function uf_spg_reporte_disponibilidad_cuenta($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                              $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
												  $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,
												  $as_cuenta_from,$as_cuenta_to,$as_ckbctasinmov,$ai_ckbhasfec,
												  $as_codfuefindes,$as_codfuefinhas,$as_estclades,$as_estclahas,$ab_nuevoorden=false)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_disponibilidad_presupuestaria
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori .. $as_codestpro5_ori  // codigo de la estructura programatica origen
     //              	    as_codestpro1_des .. $as_codestpro5_des  // codigo de la estructura programatica destino
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso
	 //	   Description :	Reporte que genera la salida para el disponible presupuestario
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    17/04/2006                         Fecha ?ltima Modificacion :  27/12/2007    Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	 $lb_existe = false;
	 $lb_valido = true;
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $this->dts_reporte->resetds("spg_cuenta");
	 $ls_seguridad="";
	 $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('PCT',$ls_seguridad);
     $ls_str_sql_where="";
	 $dts_disponible=new class_datastore();
	 $rs_data=0;

	 if($ai_ckbhasfec==0)
	 {	
			$arrResultado=$this->uf_spg_reporte_select_cuenta($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$rs_data,
	                                      $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,
										  $as_codestpro5_des,$as_cuenta_from,$as_cuenta_to,$as_ckbctasinmov,$as_codfuefindes,$as_codfuefinhas,
										  $as_estclades,$as_estclahas,$ab_nuevoorden);
			$rs_data=$arrResultado['rs_data'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
		     $li_numrows=$this->SQL->num_rows($rs_data);
		     if($li_numrows<=0)
		     {
			   $lb_valido=false;
		     }
		     else
		     {
			   while($row=$this->SQL->fetch_row($rs_data))
			   {
					 $ls_codestpro1=$row["codestpro1"];
					 $ls_codestpro2=$row["codestpro2"];
					 $ls_codestpro3=$row["codestpro3"];
					 $ls_codestpro4=$row["codestpro4"];
					 $ls_codestpro5=$row["codestpro5"];
					 $ls_estcla=$row["estcla"];
					 $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla;
					 $ls_spg_cuenta=$row["spg_cuenta"];
					 $ls_denominacion=$row["denominacion"];
					 $ls_status=$row["status"];
					 $ld_monto_asignado=$row["asignado"];
					 $ld_monto_precomprometido=$row["precomprometido"];
					 $ld_monto_compromiso=$row["comprometido"];
					 $ld_monto_causado=$row["causado"];
					 $ld_monto_pagado=$row["pagado"];
					 $ld_monto_aumento=$row["aumento"];
					 $ld_monto_disminucion=$row["disminucion"];
					 $ls_denestpro5=$row["denestpro5"];
					 $ld_monto_aumento_a=$row["aumentos_a"];
					 $ld_monto_disminucion_a=$row["disminuciones_a"];
					 $ld_monto_precompromisos_a=$row["precompromisos_a"];
					 $ld_monto_compromisos_a=$row["compromisos_a"];

				     $ld_asignado=$ld_monto_asignado;
				     $ld_actualizado=$ld_monto_asignado + ($ld_monto_aumento-$ld_monto_disminucion);
					 $ld_disponible=$ld_monto_asignado+($ld_monto_aumento-$ld_monto_disminucion)-($ld_monto_compromiso+$ld_monto_precomprometido);
//					 print " $ld_monto_asignado mas ( $ld_monto_aumento  menos $ld_monto_disminucion ) menos ( $ld_monto_compromiso mas $ld_monto_precomprometido ) <br> ";
//					 print "$ld_disponible <br> ";
//					 print_r($ld_disponible);
					 $this->dts_reporte->insertRow("programatica",$ls_programatica);
				     $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
				     $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				     $this->dts_reporte->insertRow("status",$ls_status);
				     $this->dts_reporte->insertRow("denestpro5",$ls_denestpro5);
				     $this->dts_reporte->insertRow("asignado",$ld_asignado);
				     $this->dts_reporte->insertRow("actualizado",$ld_actualizado);
				     $this->dts_reporte->insertRow("disponible",$ld_disponible);
				     $lb_valido = true;
	    	    }//while
			  } //else
			}//if
			else
			{
			  $lb_valido=false;
			}
		}
	 else
	 {
			$arrResultado=$this->uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$as_codestpro5_ori,
												 $as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,$as_codestpro5_des,
												 $ls_Sql_Where,$ls_str_estructura_from,$ls_str_estructura_to,$as_estclades,$as_estclahas);
			$ls_Sql_Where=$arrResultado['as_Sql_Where'];
			$ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
			$ls_str_estructura_to=$arrResultado['as_str_estructura_to'];
			$ls_Sql_Where = trim($ls_Sql_Where);
			if ( !empty($ls_Sql_Where) )
			{
			   $ls_str_sql_where=$ls_Sql_Where." AND ";
			}
			else
			{
			   $ls_str_sql_where="";
			}
			$ls_gestor = $_SESSION["ls_gestor"];
			if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
			{
			   $ls_concat="CONCAT(PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4,PCT.codestpro5,PCT.estcla)";
			}
			else
			{
			   $ls_concat="(PCT.codestpro1||PCT.codestpro2||PCT.codestpro3||PCT.codestpro4||PCT.codestpro5||PCT.estcla)";
			}
			if($li_estmodest==1)
			{
			  $ls_tabla="spg_dt_fuentefinanciamiento";
			  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2 AND PCT.codestpro3=EP.codestpro3 AND PCT.estcla=EP.estcla";
			}
			elseif($li_estmodest==2)
			{
			  $ls_tabla="spg_dt_fuentefinanciamiento";
			  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2 AND PCT.codestpro3=EP.codestpro3 AND PCT.codestpro4=EP.codestpro4 AND PCT.codestpro5=EP.codestpro5 AND PCT.estcla=EP.estcla ";
			}
			$ls_orden = '';
			if($ab_nuevoorden){
				$ls_orden = ' ORDER BY PCT.spg_cuenta';
			}
			else{
				$ls_orden = ' ORDER BY PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4,PCT.codestpro5, PCT.estcla, PCT.spg_cuenta';
			}
			$filtroFuente = '';
			if (!empty($as_codfuefindes) && !empty($as_codfuefinhas)) {
				$filtroFuente = " AND  EP.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
			}
						
			$ls_sql=" SELECT  DISTINCT ".$ls_concat." AS programatica, PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, ".
                    "         PCT.codestpro4, PCT.codestpro5, PCT.spg_cuenta, PCT.denominacion, PCT.status, PCT.nivel, PCT.estcla ".
                    " FROM    spg_cuentas PCT, ".$ls_tabla." EP ".
                    " WHERE   PCT.codemp='".$ls_codemp."' AND ".$ls_str_sql_where."   ".
                    "         PCT.spg_cuenta between '".trim($as_cuenta_from)."' AND '".trim($as_cuenta_to)."' ".$filtroFuente.
                    "         AND PCT.codemp=EP.codemp  ".$ls_cadena_fuefin." ".$ls_seguridad.$ls_orden;
		   $rs_data=$this->SQL->select($ls_sql);
		   if($rs_data===false)
		   {
			  $lb_valido=false;
			  $this->io_msg->message("CLASE->sigesp_spg_class_report M?TODO->uf_spg_reporte_disponibilidad_presupuestaria ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
		   }
		   else
		   {
			   while($row=$this->SQL->fetch_row($rs_data))
			   {
				   $ls_codestpro1 = $row["codestpro1"];
				   $ls_codestpro2 = $row["codestpro2"];
				   $ls_codestpro3 = $row["codestpro3"];
				   $ls_codestpro4 = $row["codestpro4"];
				   $ls_codestpro5 = $row["codestpro5"];
				   $ls_estcla = $row["estcla"];
				   $ls_programatica = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla;
				   $ls_spg_cuenta = $row["spg_cuenta"];
				   $ls_denominacion = $row["denominacion"];
				   $ls_status = $row["status"];
				   $ls_nivel = $row["nivel"];

				   $ld_monto_asignado=0;
				   $ld_monto_aumento=0;
				   $ld_monto_disminucion=0;
				   $ld_monto_precompromiso=0;
				   $ld_monto_compromiso=0;
				   $ld_monto_causado=0;
				   $ld_monto_pagado=0;
				   $ld_monto_aumento_a=0;
				   $ld_monto_disminucion_a=0;
				   $ld_monto_precompromiso_a=0;
				   $ld_monto_compromiso_a=0;

				   $arrResultado = $this->uf_calcular_disponible_operacion_por_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																		  $ls_codestpro5,$ls_spg_cuenta,$ls_nivel,$adt_fecini,$adt_fecfin,
																		  $ld_monto_asignado,$ld_monto_aumento,$ld_monto_disminucion,
																		  $ld_monto_precompromiso,$ld_monto_compromiso,$ld_monto_causado,
																		  $ld_monto_pagado,$ld_monto_aumento_a,$ld_monto_disminucion_a,
																		  $ld_monto_precompromiso_a,$ld_monto_compromiso_a,
																		  $as_codfuefindes,$as_codfuefinhas,$ls_estcla);
					$ld_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ld_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ld_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ld_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ld_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ld_monto_causado=$arrResultado['adec_monto_causado'];
					$ld_monto_pagado=$arrResultado['adec_monto_pagado'];
					$ld_monto_aumento_a=$arrResultado['adec_monto_aumento_a'];
					$ld_monto_disminucion_a=$arrResultado['adec_monto_disminucion_a'];
					$ld_monto_precompromiso_a=$arrResultado['adec_monto_precompromiso_a'];
					$ld_monto_compromiso_a=$arrResultado['adec_monto_compromiso_a'];
					$lb_valido=$arrResultado['lb_valido'];

				   if(!$lb_valido)
					{
					  return false;
					}
					$ld_asignado=$ld_monto_asignado;
					$ld_actualizado=$ld_monto_asignado + ($ld_monto_aumento-$ld_monto_disminucion);
					$ld_disponible=$ld_monto_asignado+($ld_monto_aumento-$ld_monto_disminucion)-($ld_monto_compromiso+$ld_monto_precompromiso);
//					print " $ld_monto_asignado  mas  ( $ld_monto_aumento menos $ld_monto_disminucion )  menos  ( $ld_monto_compromiso  mas  $ld_monto_precompromiso ) <br> ";
//					print $ld_disponible;
					if($as_ckbctasinmov)
					{
						  if(($ld_monto_asignado<>0)||($ld_monto_aumento<>0)||($ld_monto_disminucion<>0)||($ld_monto_precompromiso<>0)||
							 ($ld_monto_compromiso<>0)||($ld_monto_causado<>0)||($ld_monto_pagado<>0)||($ld_monto_aumento_a<>0)||
							 ($ld_monto_disminucion_a<>0)||($ld_monto_precompromiso_a<>0)||($ld_monto_compromiso_a<>0))
						  {
							  $lb_ok=true;
						  }
						  else
						  {
							 $lb_ok=false;
						  }
					}
					else
					{
					  $lb_ok=true;
					}
					if($lb_ok)
					{
					   $this->dts_reporte->insertRow("programatica",$ls_programatica);
					   $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
					   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					   $this->dts_reporte->insertRow("status",$ls_status);
					   $this->dts_reporte->insertRow("asignado",$ld_asignado);
					   $this->dts_reporte->insertRow("actualizado",$ld_actualizado);
					   $this->dts_reporte->insertRow("disponible",$ld_disponible);
					   $lb_valido = true;
					}//if
			   }//for
		   }//else
	   }//else
  return $lb_valido;
}//fin uf_spg_reporte_disponibilidad
/********************************************************************************************************************************/
    function uf_spg_reporte_disponibilidad_formato2($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                                $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
												    $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,
												    $as_cuenta_from,$as_cuenta_to,$as_codfuefindes,$as_codfuefinhas,
													$as_estclades,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_disponibilidad_formato2
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori .. $as_codestpro5_ori  // codigo de la estructura programatica origen
     //              	    as_codestpro1_des .. $as_codestpro5_des  // codigo de la estructura programatica destino
	 //                     $adt_fecini   //  fecha de inicio
	 //                     $adt_fecfin  // fecha de  fin
	 //                     $as_cuenta_from   //  cuenta desde
	 //                     $as_cuenta_to    //  desde hasta
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso
	 //	   Description :	Reporte que genera la salida para el disponible presupuestario acumulada segun fecha seleccionada
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    26/01/2007          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_existe = false;
		$lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_seguridad="";
	    $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('PCT',$ls_seguridad);
	    $this->dts_reporte->reset_ds();
        $ls_str_sql_where="";
		$dts_disponible=new class_datastore();
		$rs_data=0;

		$ls_estructura_desde=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
		$ls_estructura_hasta=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
		$arrResultado=$this->uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,$as_codestpro5_ori,
											 $as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,$as_codestpro4_des,$as_codestpro5_des,
											 $ls_Sql_Where,$ls_str_estructura_from,$ls_str_estructura_to,$as_estclades,$as_estclahas);
		$ls_Sql_Where=$arrResultado['as_Sql_Where'];
		$ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
		$ls_str_estructura_to=$arrResultado['as_str_estructura_to'];

		$ls_Sql_Where = trim($ls_Sql_Where);
		if ( !empty($ls_Sql_Where) )
		{
		   $ls_str_sql_where=$ls_Sql_Where." AND ";
		}
		else
		{
		   $ls_str_sql_where="";
		}
		$ls_gestor = $_SESSION["ls_gestor"];
		if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		{
		   $ls_concat="CONCAT(PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4,PCT.codestpro5,PCT.estcla)";
		}
		else
		{
		   $ls_concat="(PCT.codestpro1||PCT.codestpro2||PCT.codestpro3||PCT.codestpro4||PCT.codestpro5||PCT.estcla)";
		}
        /// fuente financiamineto
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_dt_fuentefinanciamiento";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2 AND PCT.codestpro3=EP.codestpro3 AND PCT.estcla=EP.estcla ";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_dt_fuentefinanciamiento";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2 AND PCT.codestpro3=EP.codestpro3 AND PCT.codestpro4=EP.codestpro4 AND PCT.codestpro5=EP.codestpro5 AND PCT.estcla=EP.estcla ";
		}
		
		$filtroFuente = '';
		if (!empty($as_codfuefindes) && !empty($as_codfuefinhas)) {
			$filtroFuente = " AND  EP.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
		}
		
		$ls_sql=" SELECT ".$ls_concat." AS programatica, PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4, ".
				"        PCT.codestpro5, PCT.spg_cuenta, MAX(PCT.denominacion) AS denominacion, MAX(PCT.status) AS status, PCT.estcla, MAX(PCT.nivel) AS nivel ".
				" FROM   spg_cuentas PCT, ".$ls_tabla." EP ".
				" WHERE  PCT.codemp='".$ls_codemp."' AND ".$ls_str_sql_where."  ".
				"        PCT.spg_cuenta between '".trim($as_cuenta_from)."' AND  '".trim($as_cuenta_to)."'  ".
                "        $filtroFuente AND  ".
				"        PCT.codemp=EP.codemp  ".$ls_cadena_fuefin."   ".$ls_seguridad.
				" GROUP BY PCT.codestpro1, PCT.codestpro2, PCT.codestpro3, PCT.codestpro4,PCT.codestpro5,PCT.estcla,PCT.spg_cuenta".
				" ORDER BY PCT.codestpro1,PCT.codestpro2,PCT.codestpro3,PCT.codestpro4,PCT.codestpro5,PCT.estcla,PCT.spg_cuenta ";
	   $rs_data=$this->SQL->select($ls_sql);
	   if($rs_data===false)
	   {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report M?TODO->uf_spg_reporte_disponibilidad_presupuestaria ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	   }
	   else
	   {
		   if($row=$this->SQL->fetch_row($rs_data))
		   {
			  $dts_disponible->data=$this->SQL->obtener_datos($rs_data);
			  $lb_existe=true;
		   }
		   $this->SQL->free_result($rs_data);
		   if($lb_existe==false)
		   {
			  return false; // no hay registro
		   }
		   $li_total_row=$dts_disponible->getRowCount("spg_cuenta");
		   for ($li_i=1;$li_i<=$li_total_row;$li_i++)
		   {
			   $ls_codestpro1 = $dts_disponible->getValue("codestpro1",$li_i);
			   $ls_codestpro2 = $dts_disponible->getValue("codestpro2",$li_i);
			   $ls_codestpro3 = $dts_disponible->getValue("codestpro3",$li_i);
			   $ls_codestpro4 = $dts_disponible->getValue("codestpro4",$li_i);
			   $ls_codestpro5 = $dts_disponible->getValue("codestpro5",$li_i);
			   $ls_estcla = $dts_disponible->getValue("estcla",$li_i);
			   $ls_programatica = $ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla;
			   $ls_spg_cuenta = $dts_disponible->getValue("spg_cuenta",$li_i);
			   $ls_denominacion = $dts_disponible->getValue("denominacion",$li_i);
			   $ls_status = $dts_disponible->getValue("status",$li_i);
			   $ls_nivel = $dts_disponible->getValue("nivel",$li_i);

			   $ld_monto_asignado=0;
			   $ld_monto_aumento=0;
			   $ld_monto_disminucion=0;
			   $ld_monto_precompromiso=0;
			   $ld_monto_compromiso=0;
			   $ld_monto_causado=0;
			   $ld_monto_pagado=0;
			   $ld_monto_aumento_a=0;
			   $ld_monto_disminucion_a=0;
			   $ld_monto_precompromiso_a=0;
			   $ld_monto_compromiso_a=0;
			   $adt_fecini=$this->fun->uf_convertirdatetobd($adt_fecini);
			   $adt_fecfin=$this->fun->uf_convertirdatetobd($adt_fecfin);
			   $arrResultado=$this->uf_calcular_disponible_operacion_por_cuenta($ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
																	  $ls_codestpro5,$ls_spg_cuenta,$ls_nivel,$adt_fecini,$adt_fecfin,
																	  $ld_monto_asignado,$ld_monto_aumento,$ld_monto_disminucion,
																	  $ld_monto_precompromiso,$ld_monto_compromiso,$ld_monto_causado,
																	  $ld_monto_pagado,$ld_monto_aumento_a,$ld_monto_disminucion_a,
																	  $ld_monto_precompromiso_a,$ld_monto_compromiso_a,
																	   $as_codfuefindes,$as_codfuefinhas,$ls_estcla);
					$ld_monto_asignado=$arrResultado['adec_monto_asignado'];
					$ld_monto_aumento=$arrResultado['adec_monto_aumento'];
					$ld_monto_disminucion=$arrResultado['adec_monto_disminucion'];
					$ld_monto_precompromiso=$arrResultado['adec_monto_precompromiso'];
					$ld_monto_compromiso=$arrResultado['adec_monto_compromiso'];
					$ld_monto_causado=$arrResultado['adec_monto_causado'];
					$ld_monto_pagado=$arrResultado['adec_monto_pagado'];
					$ld_monto_aumento_a=$arrResultado['adec_monto_aumento_a'];
					$ld_monto_disminucion_a=$arrResultado['adec_monto_disminucion_a'];
					$ld_monto_precompromiso_a=$arrResultado['adec_monto_precompromiso_a'];
					$ld_monto_compromiso_a=$arrResultado['adec_monto_compromiso_a'];
					$lb_valido=$arrResultado['lb_valido'];

			   if(!$lb_valido)
				{
				  return false;
				}
				else
				{
					$ld_asignado=$ld_monto_asignado+($ld_monto_aumento_a+$ld_monto_aumento)-($ld_monto_disminucion_a-$ld_monto_disminucion);
					$ld_disponible=$ld_monto_asignado+($ld_monto_aumento-$ld_monto_disminucion)-($ld_monto_compromiso+$ld_monto_precompromiso);

					$ld_monto_ejecutado=0;
					$ld_monto_acumulado=0;
					$ldt_fecini=$this->fun->uf_convertirdatetobd($adt_fecini);
			        $ldt_fecfin=$this->fun->uf_convertirdatetobd($adt_fecfin);
					$arrResultado=$this->uf_spg_reporte_calcular_ejecutado($ls_spg_cuenta,$ls_programatica,$ls_programatica,
					                                                    $ldt_fecini,$ldt_fecfin,$ld_monto_ejecutado,
																		$ld_monto_acumulado,$as_codfuefindes,$as_codfuefinhas,
																		$ls_estcla);
					$ld_monto_ejecutado=$arrResultado['ad_monto_ejecutado'];
					$ld_monto_acumulado=$arrResultado['ad_monto_acumulado'];
					$lb_valido=$arrResultado['lb_valido'];
					$this->dts_reporte->insertRow("programatica",$ls_programatica);
					$this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
					$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					$this->dts_reporte->insertRow("status",$ls_status);
					$this->dts_reporte->insertRow("asignado",$ld_asignado);
					$this->dts_reporte->insertRow("disponible",$ld_disponible);
					$this->dts_reporte->insertRow("monto_ejecutado",$ld_monto_ejecutado);
					$this->dts_reporte->insertRow("monto_acumulado",$ld_monto_acumulado);
					$lb_valido = true;
			    }
		   }//for
	   }//else
  return $lb_valido;
}//fin uf_spg_reporte_disponibilidad_formato2
/****************************************************************************************************************************************/
     function uf_spg_reporte_calcular_ejecutado($as_spg_cuenta,$as_estructura_desde,$as_estructura_hasta,$adt_fecini,
	                                           $adt_fecfin,$ad_monto_ejecutado,$ad_monto_acumulado,
											   $as_codfuefindes,$as_codfuefinhas,$ls_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_calcular_ejecutado
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta
	 //                     $as_estructura_desde  // codigo programatico desde
	 //                     $as_estructura_hasta  //  codigo programatico hasta
	 //                     $adt_fecfin  //
     //              	    $adt_fecini  //
	 //                     $ad_monto_ejecutado // monto ejecutado (referencia)
	 //                     $ad_monto_acumulado // monto acumulado (referencia)
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	Reporte que genera salida para  la ejecucucion financiera
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 //    Modificado por:  Ing. Jennifer Rivero
	 // Fecha Creaci?n :    26/01/2007
	 // Fecha de Modificaci?n: 24/10/2008
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $ai_mesdes=$this->fun->uf_convertirdatetobd($adt_fecini);
	  $ai_meshas=$this->fun->uf_convertirdatetobd($adt_fecfin);
	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
		   $ls_cadena="CONCAT(DT.codestpro1,DT.codestpro2,DT.codestpro3,DT.codestpro4,DT.codestpro5,DT.estcla)";
	  }
	  else
	  {
		   $ls_cadena="DT.codestpro1||DT.codestpro2||DT.codestpro3||DT.codestpro4||DT.codestpro5||DT.estcla";
	  }
	  $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	  if($li_estmodest==1)
	  {
	    $ls_tabla="spg_dt_fuentefinanciamiento";
	    $ls_cadena_fuefin=" AND DT.codestpro1=EP.codestpro1 AND DT.codestpro2=EP.codestpro2 AND DT.codestpro3=EP.codestpro3 AND DT.estcla=EP.estcla AND DT.codfuefin=EP.codfuefin ";
	  }
	  elseif($li_estmodest==2)
	  {
	    $ls_tabla="spg_dt_fuentefinanciamiento";
	    $ls_cadena_fuefin=" AND DT.codestpro1=EP.codestpro1 AND DT.codestpro2=EP.codestpro2 AND DT.codestpro3=EP.codestpro3 AND DT.codestpro4=EP.codestpro4 AND DT.codestpro5=EP.codestpro5 AND DT.estcla=EP.estcla AND DT.codfuefin=EP.codfuefin ";
	  }
	  
	  $filtroFuente = '';
	  if (!empty($as_codfuefindes) && !empty($as_codfuefinhas)) {
	  	$filtroFuente = " AND  EP.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
	  }
	  
	  $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	  $ls_sql=" SELECT DT.fecha, DT.monto, OP.aumento, OP.disminucion, OP.precomprometer,OP.comprometer, OP.causar, OP.pagar ".
              " FROM   spg_dt_cmp DT, spg_operaciones OP , ".$ls_tabla." EP ".
              " WHERE  DT.codemp='".$ls_codemp."' AND (DT.operacion = OP.operacion) AND ".
              "        ".$ls_cadena." between  '".$as_estructura_desde."' AND '".$as_estructura_hasta."'  AND ".
              "        DT.spg_cuenta like '".$as_spg_cuenta."' ".
			  "        {$filtroFuente}   ".
			  "        AND DT.fecha <='".$adt_fecfin."' AND ".
			  "        DT.codemp=EP.codemp  ".$ls_cadena_fuefin."   ";
	 // echo $ls_sql;
	  //DT.fecha >='".$adt_fecini."'
	  $rs_ejec=$this->SQL->select($ls_sql);
	  if($rs_ejec===false)
	  { // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_calcular_ejecutado".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->SQL->fetch_row($rs_ejec))
		{
		  $li_precomprometer=$row["precomprometer"];
		  $li_comprometer=$row["comprometer"];
		  $ld_monto=$row["monto"];
		  $ldt_fecha_db=$row["fecha"];
		  
		  $ld_fecdes = $this->fun->uf_convertirfecmostrar($adt_fecini);
		  $ld_fechas = $this->fun->uf_convertirfecmostrar($ldt_fecha_db);
		  $ndias     = $this->io_fecha->uf_restar_fechas($ld_fechas, $ld_fecdes , true);
		  //echo "fecha inicio->".$ld_fecdes."fecha fechadb->".$ld_fechas." dias->".$ndias."<br>";	
		  if((($li_precomprometer)||($li_comprometer))&&($ndias>=0))
		  {
		  	$ad_monto_ejecutado=$ad_monto_ejecutado+$ld_monto;
		  }//if
		  if((($li_precomprometer)||($li_comprometer)))
		  {
		 	 $ad_monto_acumulado=$ad_monto_acumulado+$ld_monto;
		  }//if
		}//while
	   $this->SQL->free_result($rs_ejec);
	  }//else
		$arrResultado['ad_monto_ejecutado']=$ad_monto_ejecutado;
		$arrResultado['ad_monto_acumulado']=$ad_monto_acumulado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   }//fin uf_spg_reporte_calcular_ejecutado
/****************************************************************************************************************************************/
	function uf_calcular_disponible_operacion_por_cuenta($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                                     $as_spg_cuenta,$as_nivel,$adt_fecini,$adt_fecfin,$adec_monto_asignado,
														 $adec_monto_aumento,$adec_monto_disminucion,$adec_monto_precompromiso,
														 $adec_monto_compromiso,$adec_monto_causado,$adec_monto_pagado,
														 $adec_monto_aumento_a,$adec_monto_disminucion_a,$adec_monto_precompromiso_a,
														 $adec_monto_compromiso_a,$as_codfuefindes,$as_codfuefinhas,$as_estcla)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_disponible_operacion_por_cuenta -> proviene de uf_spg_reporte_disponibilidad_presupuestaria
     //	    Returns :	$lb_valido true si se realizo la funcion con exito o false en caso contrario
	 //	Description :	M?todo  que ejecuta todas funciones de acumulado para asi sacar el acumulado por cuentas
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	   $lb_valido = true;
	   $ls_estmodprog=$this->dts_empresa["estmodprog"];// si esta configurado para realizar la modificaci?n al monto programado
	   $as_spg_cuenta2=$as_spg_cuenta;
	   $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
   	   // Global
	   if ($ls_estmodprog==0)
	   {
       		$arrResultado=$this->uf_calcular_disponible_asignacion($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
			                                                    $as_codestpro5,$as_spg_cuenta,$as_nivel,$adec_monto_asignado,
																$as_codfuefindes,$as_codfuefinhas, $as_estcla);
			$adec_monto_asignado=$arrResultado['adec_monto'];
			$lb_valido=$arrResultado['lb_valido'];
	   }
	   else
	   {
	   		$arrResultado=$this->uf_buscar_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                    $as_estcla, $adt_fecini,$adt_fecfin,$as_spg_cuenta2,$adec_monto_asignado);
			$adec_monto_asignado=$arrResultado['adec_monto'];
			$lb_valido=$arrResultado['lb_valido'];
			$mes1= substr($adt_fecfin,5,2);
			$ano = substr($adt_fecfin,0,4);
			$adt_fecini = $ano."-".$mes1."-01";
	   }
	   // acumulado Anteriores
	   if ($ls_estmodprog==0) // se calculan los montos anteriores solo cuando no se trabaja seg?n el programado
	   {
		   if ($lb_valido)
		   {
			  $ls_operacion="aumento";
			  $arrResultado=$this->uf_calcular_disponible_anterior($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																	$adt_fecini,$as_spg_cuenta,$adec_monto_aumento_a,$ls_operacion,
																	$as_codfuefindes,$as_codfuefinhas,$as_estcla);
			  $adec_monto_aumento_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="disminucion";
			  $arrResultado=$this->uf_calcular_disponible_anterior($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																$adt_fecini,$as_spg_cuenta,$adec_monto_disminucion_a,$ls_operacion,
																$as_codfuefindes,$as_codfuefinhas,$as_estcla);
			  $adec_monto_disminucion_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="precomprometer";
			  $arrResultado=$this->uf_calcular_disponible_anterior($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																$adt_fecini,$as_spg_cuenta,$adec_monto_precompromiso_a,
																$ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
			  $adec_monto_precompromiso_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		   if ($lb_valido)
		   {
			  $ls_operacion="comprometer";
			  $arrResultado=$this->uf_calcular_disponible_anterior($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																$adt_fecini,$as_spg_cuenta,$adec_monto_compromiso_a,$ls_operacion,
																$as_codfuefindes,$as_codfuefinhas,$as_estcla);
			  $adec_monto_compromiso_a=$arrResultado['adec_monto'];
			  $lb_valido=$arrResultado['lb_valido'];
		   }
		}
	   // En el Rango
       if ($lb_valido)
  	   {
			$ls_operacion="aumento";
			if ($ls_estmodprog==0) // si esta configurado para realizar la modificaci?n al monto programado
			{
				$arrResultado=$this->uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																 $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_aumento,
																 $ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
				$adec_monto_aumento=$arrResultado['adec_monto'];
				$lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				$arrResultado=$this->uf_cacular_programado_mp($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
													       $as_spg_cuenta,$adec_monto_aumento,$ls_operacion,$adt_fecini);
				$adec_monto_aumento=$arrResultado['adec_monto'];
				$lb_valido=$arrResultado['lb_valido'];
			}
	   }
       if ($lb_valido)
  	   {                 
	      $ls_operacion="disminucion";
			if ($ls_estmodprog==0) // si esta configurado para realizar la modificaci?n al monto programado
			{
				  $arrResultado=$this->uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																	 $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_disminucion,
																	 $ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
				  $adec_monto_disminucion=$arrResultado['adec_monto'];
				  $lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				$arrResultado=$this->uf_cacular_programado_mp($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
													       $as_spg_cuenta,$adec_monto_disminucion,$ls_operacion,$adt_fecini);
				$adec_monto_disminucion=$arrResultado['adec_monto'];
				$lb_valido=$arrResultado['lb_valido'];
			}
	   }
       
	   
	   if ($lb_valido)
  	   {
	      $ls_operacion="precomprometer";
			if ($ls_estmodprog==0) // si esta configurado para realizar la modificaci?n al monto programado
			{
				  $arrResultado=$this->uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																	 $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_precompromiso,
																	 $ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
				  $adec_monto_precompromiso=$arrResultado['adec_monto'];
				  $lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				 $arrResultado=$this->uf_cacular_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
														$as_spg_cuenta,$adec_monto_precompromiso,$ls_operacion,$adt_fecini);
				 $adec_monto_precompromiso=$arrResultado['adec_monto'];
				 $lb_valido=$arrResultado['lb_valido'];
			}
	   }
       if ($lb_valido)
  	   {
	      $ls_operacion="comprometer";
			if ($ls_estmodprog==0) // si esta configurado para realizar la modificaci?n al monto programado
			{
				  $arrResultado=$this->uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																	 $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_compromiso,
																	 $ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
				  $adec_monto_compromiso=$arrResultado['adec_monto'];
				  $lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				 $arrResultado=$this->uf_cacular_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
														$as_spg_cuenta,$adec_monto_compromiso,$ls_operacion,$adt_fecini);
				 $adec_monto_compromiso=$arrResultado['adec_monto'];
				 $lb_valido=$arrResultado['lb_valido'];
			}
	   }
       if ($lb_valido)
  	   {
	      $ls_operacion="causar";
			if ($ls_estmodprog==0) // si esta configurado para realizar la modificaci?n al monto programado
			{
				  $arrResultado=$this->uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																	 $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_causado,
																	 $ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
				  $adec_monto_causado=$arrResultado['adec_monto'];
				  $lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				 $arrResultado=$this->uf_cacular_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
														$as_spg_cuenta,$adec_monto_causado,$ls_operacion,$adt_fecini);
				 $adec_monto_causado=$arrResultado['adec_monto'];
				 $lb_valido=$arrResultado['lb_valido'];
			}
	   }
       if ($lb_valido)
  	   {
	      $ls_operacion="pagar";
			if ($ls_estmodprog==0) // si esta configurado para realizar la modificaci?n al monto programado
			{
				  $arrResultado=$this->uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
																	 $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_pagado,
																	 $ls_operacion,$as_codfuefindes,$as_codfuefinhas,$as_estcla);
				  $adec_monto_pagado=$arrResultado['adec_monto'];
				  $lb_valido=$arrResultado['lb_valido'];
			}
			else
			{
				 $arrResultado=$this->uf_cacular_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
														$as_spg_cuenta,$adec_monto_causado,$ls_operacion,$adt_fecini);
				 $adec_monto_causado=$arrResultado['adec_monto'];
				 $lb_valido=$arrResultado['lb_valido'];
			}
	   }
		$arrResultado['adec_monto_asignado']=$adec_monto_asignado;
		$arrResultado['adec_monto_aumento']=$adec_monto_aumento;
		$arrResultado['adec_monto_disminucion']=$adec_monto_disminucion;
		$arrResultado['adec_monto_precompromiso']=$adec_monto_precompromiso;
		$arrResultado['adec_monto_compromiso']=$adec_monto_compromiso;
		$arrResultado['adec_monto_causado']=$adec_monto_causado;
		$arrResultado['adec_monto_pagado']=$adec_monto_pagado;
		$arrResultado['adec_monto_aumento_a']=$adec_monto_aumento_a;
		$arrResultado['adec_monto_disminucion_a']=$adec_monto_disminucion_a;
		$arrResultado['adec_monto_precompromiso_a']=$adec_monto_precompromiso_a;
		$arrResultado['adec_monto_compromiso_a']=$adec_monto_compromiso_a;
		$arrResultado['lb_valido']=$lb_valido;
	   return $arrResultado;
	} // fin uf_calcular_acumulado_operaciones_por_cuenta
/********************************************************************************************************************************/


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cacular_programado_mp($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
	                                  $as_spg_cuenta,$adec_monto,$as_operacion,$ad_fechavalidacion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cacular_programado_mp
		//		   Access: public
		//       Argument: as_codestpro1 // C?digo de Estructura Programatica 1
		//       		   as_codestpro2 // C?digo de Estructura Programatica 2
		//       		   as_codestpro3 // C?digo de Estructura Programatica 3
		//       		   as_codestpro4 // C?digo de Estructura Programatica 4
		//       		   as_codestpro5 // C?digo de Estructura Programatica 5
		//       		   as_estcla // Estatus de Clasificaci?n
		//       		   as_spg_cuenta // cuenta Presupuestaria
		//       		   adec_monto // Monto del Movimiento
		//       		   as_operacion // Operaci?n del movimiento
		//	  Description: M?todo que consulta y suma dependiando de la operacion(aumento,disminucion,precompromiso,compromiso)
		//	      Returns: Retorna monto asignado
		//	   Creado Por: Ing. wilmer Brice?o
		// Modificado Por: Ing. Yesenia Moreno								Fecha ?ltima Modificaci?n : 31/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	    $lb_valido=true;
		$ldec_monto=0;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_estmodape=$_SESSION["la_empresa"]["estmodape"];
		$ls_mes=substr($ad_fechavalidacion,5,2);
		$ls_anio=substr($ad_fechavalidacion,0,4);
		$ls_sql="SELECT SUM(enero+febrero+marzo) as trimestre1, SUM(abril+mayo+junio) as trimestre2,".
				"       SUM(julio+agosto+septiembre) as trimestre3, SUM(octubre+noviembre+diciembre) as trimestre4,".
				"       SUM(enero) as enero, SUM(febrero) as febrero, SUM(marzo) as marzo, SUM(abril) as abril, SUM(mayo) as mayo,".
				"       SUM(junio) as junio, SUM(julio) as julio, SUM(agosto) as agosto, SUM(septiembre) as septiembre,".
				"       SUM(octubre) as octubre, SUM(noviembre) as noviembre, SUM(diciembre) as diciembre".
				"  FROM spg_dtmp_mensual, spg_operaciones, sigesp_cmp_md  ".
				" WHERE spg_dtmp_mensual.codemp='".$ls_codemp."' ".
				"   AND spg_operaciones.".$as_operacion."=1 ".
				"   AND spg_dtmp_mensual.spg_cuenta like '".$as_spg_cuenta."' ".
				"   AND spg_dtmp_mensual.codestpro1='".$as_codestpro1."' ".
				"   AND spg_dtmp_mensual.codestpro2='".$as_codestpro2."' ".
				"   AND spg_dtmp_mensual.codestpro3='".$as_codestpro3."' ".
				"   AND spg_dtmp_mensual.codestpro4='".$as_codestpro4."' ".
				"   AND spg_dtmp_mensual.codestpro5='".$as_codestpro5."' ".
				"   AND spg_dtmp_mensual.estcla='".$as_estcla."' ".
				"   AND sigesp_cmp_md.estapro=1".
				"   AND sigesp_cmp_md.codemp=spg_dtmp_mensual.codemp".
				"   AND sigesp_cmp_md.procede=spg_dtmp_mensual.procede".
				"   AND sigesp_cmp_md.comprobante=spg_dtmp_mensual.comprobante".
				"   AND sigesp_cmp_md.fecha=spg_dtmp_mensual.fecha".
				"   AND spg_dtmp_mensual.operacion=spg_operaciones.operacion ";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
            $this->io_msg->message("Error en uf_cacular_programado_mp ".$this->io_function->uf_convertirmsg($this->SQL->message));
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
				$ldec_trimestre1 = number_format($row["trimestre1"],2,".","");
				$ldec_trimestre2 = number_format($row["trimestre2"],2,".","");
				$ldec_trimestre3 = number_format($row["trimestre3"],2,".","");
				$ldec_trimestre4 = number_format($row["trimestre4"],2,".","");
				$ldec_enero = number_format($row["enero"],2,".","");
				$ldec_febrero = number_format($row["febrero"],2,".","");
				$ldec_marzo = number_format($row["marzo"],2,".","");
				$ldec_abril = number_format($row["abril"],2,".","");
				$ldec_mayo = number_format($row["mayo"],2,".","");
				$ldec_junio = number_format($row["junio"],2,".","");
				$ldec_julio = number_format($row["julio"],2,".","");
				$ldec_agosto = number_format($row["agosto"],2,".","");
				$ldec_septiembre = number_format($row["septiembre"],2,".","");
				$ldec_octubre = number_format($row["octubre"],2,".","");
				$ldec_noviembre = number_format($row["noviembre"],2,".","");
				$ldec_diciembre = number_format($row["diciembre"],2,".","");
				switch($ls_mes)
				{
					case"01":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_enero;}
						else
						{$adec_monto=$ldec_trimestre1;}
					break;
					case"02":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_febrero;}
						else
						{$adec_monto=$ldec_trimestre1;}
					break;
					case"03":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_marzo;}
						else
						{$adec_monto=$ldec_trimestre1;}
					break;
					case"04":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_abril;}
						else
						{$adec_monto=$ldec_trimestre2;}
					break;
					case"05":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_mayo;}
						else
						{$adec_monto=$ldec_trimestre2;}
					break;
					case"06":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_junio;}
						else
						{$adec_monto=$ldec_trimestre2;}
					break;
					case"07":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_julio;}
						else
						{$adec_monto=$ldec_trimestre3;}
					break;
					case"08":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_agosto;}
						else
						{$adec_monto=$ldec_trimestre3;}
					break;
					case"09":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_septiembre;}
						else
						{$adec_monto=$ldec_trimestre3;}
					break;
					case"10":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_octubre;}
						else
						{$adec_monto=$ldec_trimestre4;}
					break;
					case"11":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_noviembre;}
						else
						{$adec_monto=$ldec_trimestre4;}
					break;
					case"12":
						if($ls_estmodape==0)
						{$adec_monto=$ldec_diciembre;}
						else
						{$adec_monto=$ldec_trimestre4;}
					break;
				}
			}
			$this->SQL->free_result($rs_data);
		}
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
	   return $arrResultado;
	} // fin function uf_cacular_programado_mp
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_cacular_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
	                                          $as_spg_cuenta,$adec_monto,$as_operacion,$ad_fechavalidacion)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cacular_programado
		//		   Access: public
		//       Argument: as_codestpro1 // C?digo de Estructura Programatica 1
		//       		   as_codestpro2 // C?digo de Estructura Programatica 2
		//       		   as_codestpro3 // C?digo de Estructura Programatica 3
		//       		   as_codestpro4 // C?digo de Estructura Programatica 4
		//       		   as_codestpro5 // C?digo de Estructura Programatica 5
		//       		   as_estcla // Estatus de Clasificaci?n
		//       		   as_spg_cuenta // cuenta Presupuestaria
		//       		   adec_monto // Monto del Movimiento
		//       		   as_operacion // Operaci?n del movimiento
		//	  Description: M?todo que consulta y suma dependiando de la operacion(aumento,disminucion,precompromiso,compromiso)
		//	      Returns: Retorna monto asignado
		//	   Creado Por: Ing. wilmer Brice?o
		// Modificado Por: Ing. Yesenia Moreno								Fecha ?ltima Modificaci?n : 31/05/2007
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	    $lb_valido=true;
		$ldec_monto=0;
		$ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$ls_estmodape=$_SESSION["la_empresa"]["estmodape"];
		$ls_mes=substr($ad_fechavalidacion,5,2);
		$ls_anio=substr($ad_fechavalidacion,0,4);
		switch($ls_mes)
		{
			case"01":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-01-01";
					$ls_lastday=$this->io_fecha->uf_last_day("03",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_lastday."-03-".$ls_anio;
				}
			break;
			case"02":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-01-01";
					$ls_lastday=$this->io_fecha->uf_last_day("03",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_lastday."-03-".$ls_anio;
				}
			break;
			case"03":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-01-01";
					$ls_lastday=$this->io_fecha->uf_last_day("03",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-03-".$ls_lastday;
				}
			break;
			case"04":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-04-01";
					$ls_lastday=$this->io_fecha->uf_last_day("06",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-06-".$ls_lastday;
				}
			break;
			case"05":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-04-01";
					$ls_lastday=$this->io_fecha->uf_last_day("06",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-06-".$ls_lastday;
				}
			break;
			case"06":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-04-01";
					$ls_lastday=$this->io_fecha->uf_last_day("06",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-06-".$ls_lastday;
				}
			break;
			case"07":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-07-01";
					$ls_lastday=$this->io_fecha->uf_last_day("09",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-09-".$ls_lastday;
				}
			break;
			case"08":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-07-01";
					$ls_lastday=$this->io_fecha->uf_last_day("09",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-09-".$ls_lastday;
				}
			break;
			case"09":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-07-01";
					$ls_lastday=$this->io_fecha->uf_last_day("09",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-09-".$ls_lastday;
				}
			break;
			case"10":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-10-01";
					$ls_lastday=$this->io_fecha->uf_last_day("12",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-12-".$ls_lastday;
				}
			break;
			case"11":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-10-01";
					$ls_lastday=$this->io_fecha->uf_last_day("12",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-12-".$ls_lastday;
				}
			break;
			case"12":
				if($ls_estmodape==0)
				{
					$ls_fechainicio=$ls_anio."-".$ls_mes."-01";
					$ls_lastday=$this->io_fecha->uf_last_day($ls_mes,$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-".$ls_mes."-".$ls_lastday;
				}
				else
				{
					$ls_fechainicio=$ls_anio."-10-01";
					$ls_lastday=$this->io_fecha->uf_last_day("12",$ls_anio);
					$ls_lastday=substr($ls_lastday,0,2);
					$ls_fechafin=$ls_anio."-12-".$ls_lastday;
				}
			break;
		}
		$ls_sql="SELECT SUM(CASE WHEN monto is null then 0 else monto end)  As monto ".
                "  FROM spg_dt_cmp, spg_operaciones  ".
                " WHERE codemp='".$ls_codemp."' ".
                "   AND spg_operaciones.".$as_operacion."=1 ".
				"   AND spg_dt_cmp.spg_cuenta like '".$as_spg_cuenta."' ".
				"   AND fecha >='".$ls_fechainicio."' AND fecha <='".$ls_fechafin."' ".
				"   AND spg_dt_cmp.codestpro1='".$as_codestpro1."' ".
				"   AND spg_dt_cmp.codestpro2='".$as_codestpro2."' ".
			    "   AND spg_dt_cmp.codestpro3='".$as_codestpro3."' ".
				"   AND spg_dt_cmp.codestpro4='".$as_codestpro4."' ".
				"   AND spg_dt_cmp.codestpro5='".$as_codestpro5."' ".
				"   AND spg_dt_cmp.estcla='".$as_estcla."' ".
				"   AND spg_dt_cmp.operacion=spg_operaciones.operacion ";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
            $this->io_msg->message("Error en uf_cacular_programado ".$this->io_function->uf_convertirmsg($this->SQL->message));
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ldec_monto = number_format($row["monto"],2,".","");
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto = $ldec_monto;
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
	   return $arrResultado;
	} // fin function uf_cacular_programado
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_calcular_disponible_asignacion($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                           $as_spg_cuenta,$as_nivel,$adec_monto,$as_codfuefindes,$as_codfuefinhas,$as_estcla)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_disponible_asignacion( -> proviene de uf_calcular_acumulado_operaciones_por_cuenta
     //	    Returns :	Retorna monto asignado
	 //	Description :	M?todo que consulta y suma lo asignado por cuenta
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	    $lb_valido = true;
		$ldec_monto=0;
		$ls_codemp = $this->dts_empresa["codemp"];
        if (($as_codfuefindes=='')&&($as_codfuefinhas=='')) {
			$ls_sql    = " SELECT COALESCE(SUM(monto),0) As monto ".
							 " FROM   spg_dt_cmp PCT, spg_operaciones O".
							 " WHERE  PCT.codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND  O.asignar=1 AND ".
							 "        PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND PCT.codestpro1='".$as_codestpro1."' AND ".
							 "        PCT.codestpro2='".$as_codestpro2."' AND PCT.codestpro3='".$as_codestpro3."' AND ".
							 "        PCT.codestpro4='".$as_codestpro4."' AND PCT.codestpro5='".$as_codestpro5."' AND ".
							 "        PCT.estcla='".$as_estcla."'";
			$rs_data=$this->SQL->select($ls_sql);
			if($rs_data===false)
			{   // error interno sql
				$this->io_msg->message("Error en metodo uf_calcular_disponible_asignacion".$this->fun->uf_convertirmsg($this->SQL->message));
				$lb_valido = false;
			}
			else
			{
				if($row=$this->SQL->fetch_row($rs_data))
				{
				   $adec_monto = $row["monto"];
				}
				$this->SQL->free_result($rs_data);
			}
		}
		else {
			$adec_monto = $this->uf_buscar_asignado_fuente($as_codestpro1, $as_codestpro2, $as_codestpro3, $as_codestpro4, 
					                                       $as_codestpro5, $as_codestpro1, $as_codestpro2, $as_codestpro3, 
					                                       $as_codestpro4, $as_codestpro5, $as_estcla, $as_estcla, 
					                                       $as_codfuefindes, $as_codfuefinhas, $as_spg_cuenta, $as_nivel);
		}
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin function uf_calcular_disponible_asignacion
/********************************************************************************************************************************/
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function uf_buscar_programado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,
	                              $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto_asignado)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_buscar_programado
     //	    Returns :
	 //	Description :	M?todo que consulta el programado de la cuenta y estructura dada
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$adec_monto_asignado=0;
		$mes= substr($adt_fecfin,5,2);
		switch ($mes)
		{
			case "1":
				$ls_mes="enero";
			break;
			case "2":
				$ls_mes="febrero";
			break;
			case "3":
				$ls_mes="marzo";
			break;
			case "4":
				$ls_mes="abril";
			break;
			case "5":
				$ls_mes="mayo";
			break;
			case "6":
				$ls_mes="junio";
			break;
			case "7":
				$ls_mes="julio";
			break;
			case "8":
				$ls_mes="agosto";
			break;
			case "9":
				$ls_mes="septiembre";
			break;
			case "10":
				$ls_mes="octubre";
			break;
			case "11":
				$ls_mes="noviembre";
			break;
			case "12":
				$ls_mes="diciembre";
			break;
		}
		$ls_sql=" select asignado as monto from spg_cuentas ".
		        "  where spg_cuentas.codestpro1='".$as_codestpro1."'".
				"    and spg_cuentas.codestpro2='".$as_codestpro2."'".
				"    and spg_cuentas.codestpro3='".$as_codestpro3."'".
				"    and spg_cuentas.codestpro4='".$as_codestpro4."'".
				"    and spg_cuentas.codestpro5='".$as_codestpro5."'".
				"    and spg_cuentas.estcla='".$as_estcla."'".
				"    and spg_cuentas.spg_cuenta='".trim($as_spg_cuenta)."'";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
            $this->io_msg->message("Error en uf_buscar_programado ".
			                       $this->fun->uf_convertirmsg($this->SQL->message));
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_monto = $row["monto"];
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto_asignado = $ls_monto;

		$arrResultado['adec_monto']=$adec_monto_asignado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// fin de uf_buscar_programado

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function uf_calcular_disponible_por_rango($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                          $adt_fecini,$adt_fecfin,$as_spg_cuenta,$adec_monto,$as_operacion,$as_codfuefindes,
											  $as_codfuefinhas,$as_estcla)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_disponible_por_rango( -> proviene de uf_calcular_disponible_operaciones_por_cuenta
     //	    Returns :	Retorna monto asignado
	 //	Description :	M?todo que consulta y suma dependiando de la operacion(aumento,disminucion,precompromiso,compromiso)
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	    $lb_valido = true;
		$ldec_monto=0;
		$ls_codemp = $this->dts_empresa["codemp"];
        $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_dt_fuentefinanciamiento";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2  AND PCT.codestpro3=EP.codestpro3 AND PCT.estcla=EP.estcla";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_dt_fuentefinanciamiento";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2  AND PCT.codestpro3=EP.codestpro3 AND PCT.codestpro4=EP.codestpro4  AND PCT.codestpro5=EP.codestpro5 AND PCT.estcla=EP.estcla";
		}
		
		$filtroFuente = '';
		if (!empty($as_codfuefindes) && !empty($as_codfuefinhas)) {
			$filtroFuente = " AND  PCT.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
		}
		
	    $ls_gestor = $_SESSION["ls_gestor"];
		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
			$ls_sql    = " SELECT CASE SUM(monto) WHEN NULL  THEN  0 ".
      					 "	      ELSE SUM(monto)                    ".
      					 " 	      END monto                          ".
						 " FROM  spg_dt_cmp PCT, spg_operaciones O, ".$ls_tabla." EP   ".
						 " WHERE PCT.codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
						 "       O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
						 "       fecha >='".$adt_fecini."' AND fecha <='".$adt_fecfin."' AND ".
						 "       PCT.codestpro1='".$as_codestpro1."' AND PCT.codestpro2='".$as_codestpro2."' AND ".
						 "       PCT.codestpro3='".$as_codestpro3."' AND PCT.codestpro4='".$as_codestpro4."' AND ".
						 "       PCT.codestpro5='".$as_codestpro5."' AND PCT.estcla='".$as_estcla."'    ".
						 "       $filtroFuente AND ".
						 "       PCT.codfuefin = EP.codfuefin AND ".
						 "       PCT.codemp=EP.codemp  ".$ls_cadena_fuefin."  ";
		}
		else
		{
			$ls_sql  = " SELECT COALESCE(SUM(monto),0) As monto ".
						 " FROM  spg_dt_cmp PCT, spg_operaciones O, ".$ls_tabla." EP   ".
						 " WHERE PCT.codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
						 "       O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
						 "       fecha >='".$adt_fecini."' AND fecha <='".$adt_fecfin."' AND ".
						 "       PCT.codestpro1='".$as_codestpro1."' AND PCT.codestpro2='".$as_codestpro2."' AND ".
						 "       PCT.codestpro3='".$as_codestpro3."' AND PCT.codestpro4='".$as_codestpro4."' AND ".
						 "       PCT.codestpro5='".$as_codestpro5."' AND PCT.estcla='".$as_estcla."'   ".
						 "       $filtroFuente AND ".
						 "       PCT.codfuefin = EP.codfuefin AND ".
						 "       PCT.codemp=EP.codemp  ".$ls_cadena_fuefin."  ";
		}
		/*$ls_mysql  = " SELECT COALESCE(SUM(monto),0) As monto ".
                     " FROM  spg_dt_cmp PCT,spg_operaciones O  ".
                     " WHERE codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
                     "       O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					 "       fecha >='".$adt_fecini."' AND fecha <='".$adt_fecfin."' AND ".
					 "       PCT.codestpro1='".$as_codestpro1."' AND PCT.codestpro2='".$as_codestpro2."' AND ".
					 "       PCT.codestpro3='".$as_codestpro3."' AND PCT.codestpro4='".$as_codestpro4."' AND ".
					 "       PCT.codestpro5='".$as_codestpro5."' ";	*/
		$rs_data=$this->SQL->select($ls_sql);        //print "$ls_sql <br>";
		if($rs_data===false)
		{   // error interno sql
            $this->io_msg->message("Error en uf_calcular_disponible_por_rango ".$this->fun->uf_convertirmsg($this->SQL->message));
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ldec_monto = $row["monto"];
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto = $ldec_monto;
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin function uf_calcular_disponible_por_rango
/********************************************************************************************************************************/
	function uf_calcular_disponible_anterior($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                         $adt_fecini,$as_spg_cuenta,$adec_monto,$as_operacion,$as_codfuefindes,
											 $as_codfuefinhas,$as_estcla)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_disponible_anterior( -> proviene de uf_calcular_disponible_operaciones_por_cuenta
     //	    Returns :	Retorna monto aumento
	 //	Description :	M?todo que consulta y suma el aumento de la cuenta
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   	    $lb_valido = true;
		$ldec_monto=0;
		$ls_codemp = $this->dts_empresa["codemp"];
        $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		if($li_estmodest==1)
		{
		  $ls_tabla="spg_dt_fuentefinanciamiento";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2  AND PCT.codestpro3=EP.codestpro3 AND PCT.estcla=EP.estcla";
		}
		elseif($li_estmodest==2)
		{
		  $ls_tabla="spg_dt_fuentefinanciamiento";
		  $ls_cadena_fuefin=" AND PCT.codestpro1=EP.codestpro1 AND PCT.codestpro2=EP.codestpro2  AND PCT.codestpro3=EP.codestpro3 AND PCT.codestpro4=EP.codestpro4  AND PCT.codestpro5=EP.codestpro5 AND PCT.estcla=EP.estcla";
		}
	    $ls_gestor = $_SESSION["ls_gestor"];
	    
	    $filtroFuente = '';
	    if (!empty($as_codfuefindes) && !empty($as_codfuefinhas)) {
	    	$filtroFuente = " AND  PCT.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
	    }
	    
		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
			$ls_sql    = " SELECT CASE SUM(monto) WHEN NULL  THEN  0 ".
      					 "	      ELSE SUM(monto)                    ".
      					 " 	      END monto                          ".
						 " FROM   spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." EP ".
						 " WHERE  PCT.codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
						 "        O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
						 "        PCT.fecha <'".$adt_fecini."' AND PCT.codestpro1='".$as_codestpro1."' AND ".
						 "        PCT.codestpro2='".$as_codestpro2."' AND PCT.codestpro3='".$as_codestpro3."' AND ".
						 "        PCT.codestpro4='".$as_codestpro4."' AND PCT.codestpro5='".$as_codestpro5."' AND ".
						 "        PCT.estcla='".$as_estcla."' ".
						 "        $filtroFuente AND ".
						 "        PCT.codfuefin=EP.codfuefin  AND ".
						 "        PCT.codemp=EP.codemp  ".$ls_cadena_fuefin."  ";
		}
		else
		{
			$ls_sql  = " SELECT COALESCE(SUM(monto),0) As monto ".
						 " FROM   spg_dt_cmp PCT,spg_operaciones O, ".$ls_tabla." EP ".
						 " WHERE  PCT.codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
						 "        O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
						 "        PCT.fecha <'".$adt_fecini."' AND PCT.codestpro1='".$as_codestpro1."' AND ".
						 "        PCT.codestpro2='".$as_codestpro2."' AND PCT.codestpro3='".$as_codestpro3."' AND ".
						 "        PCT.codestpro4='".$as_codestpro4."' AND PCT.codestpro5='".$as_codestpro5."' AND ".
						 "        PCT.estcla='".$as_estcla."' ".
						 "        $filtroFuente AND ".
						 "        PCT.codfuefin=EP.codfuefin  AND ".
						 "        PCT.codemp=EP.codemp  ".$ls_cadena_fuefin."  ";
		}
		/*$ls_mysql  = " SELECT COALESCE(SUM(monto),0) As monto ".
                     " FROM   spg_dt_cmp PCT,spg_operaciones O ".
					 " WHERE  PCT.codemp='".$ls_codemp."' AND PCT.operacion=O.operacion AND ".
                     "        O.".$as_operacion."=1 AND PCT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					 "        PCT.fecha <'".$adt_fecini."' AND PCT.codestpro1='".$as_codestpro1."' AND ".
					 "        PCT.codestpro2='".$as_codestpro2."' AND PCT.codestpro3='".$as_codestpro3."' AND ".
					 "        PCT.codestpro4='".$as_codestpro4."' AND PCT.codestpro5='".$as_codestpro5."' " ;*/
		//echo $ls_sql;
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_calcular_disponible_anterior ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ldec_monto = $row["monto"];
			}
			$this->SQL->free_result($rs_data);
		}
		$adec_monto = $ldec_monto;
		$arrResultado['adec_monto']=$adec_monto;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin function uf_calcular_disponible_anterior
/********************************************************************************************************************************/
	//////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "MODIFICACIONES PRESUPUESTARIAS " //
	////////////////////////////////////////////////////////////
    function uf_spg_reporte_modificaciones_presupuestarias($ai_rect,$ai_trans,$ai_insub,$ai_cred,$adt_fecini,$adt_fecfin,
	                                                       $as_comprobante,$as_procede,$adt_fecha)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_modificaciones_presupuestarias
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso
	 //	   Description :	Reporte que genera la salida para las modificaciones presupuestarias
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    15/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;

	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_cad=$this->uf_spg_reporte_chequear_modificaciones($ai_rect,$ai_insub,$ai_trans,$ai_cred);
      $ls_cadena=str_replace('procede','MOV.procede',$ls_cad);
     // $ls_cadena="";
	  if (empty($as_comprobante))
	  {
	    $ls_cadena_2="";
	  }
	  else
	  {
	    $ls_cadena_2=" AND CMP.comprobante='".$as_comprobante."' AND CMP.procede='".$as_procede."'  AND MOV.fecha='".$adt_fecha."' ";

	  }
	  $ls_sql="SELECT CMP.comprobante, CMP.procede, CMP.descripcion AS cmp_descripcion, CMP.fecha as fecaprmod, MOV.codestpro1,MOV.codestpro2,
       				  MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.spg_cuenta,CTA.denominacion ,MOV.operacion, MOV.monto, MP.fecha 
				FROM sigesp_cmp CMP 
					INNER JOIN spg_dt_cmp MOV ON CMP.codemp = MOV.codemp AND CMP.procede = MOV.procede AND CMP.comprobante = MOV.comprobante AND CMP.codban = MOV.codban AND CMP.ctaban = MOV.ctaban
					INNER JOIN spg_cuentas CTA ON MOV.codemp = CTA.codemp  AND MOV.codestpro1 = CTA.codestpro1 AND MOV.codestpro2 = CTA.codestpro2 AND MOV.codestpro3 = CTA.codestpro3 AND 
	                                              MOV.codestpro4 = CTA.codestpro4 AND MOV.codestpro5 = CTA.codestpro5 AND MOV.estcla = CTA.estcla AND MOV.spg_cuenta = CTA.spg_cuenta
					INNER JOIN sigesp_cmp_md MP ON CMP.codemp = MP.codemp AND CMP.procede = MP.procede AND CMP.comprobante = MP.comprobante 
	  			WHERE CMP.codemp='".$ls_codemp."' AND (".$ls_cadena.") AND MOV.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' 
	  			  AND CMP.tipo_comp   = 2  ".$ls_cadena_2."
	  			  ORDER BY  CMP.comprobante,CMP.procede, MOV.spg_cuenta";

	 // echo $ls_sql;
	  //die();
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report
		                          M?TODO->uf_spg_reporte_modificaciones_presupuestarias
								  ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	 }
	 else
	 {
			while($row=$this->SQL->fetch_row($rs_data))
			{
				  $ls_cmp_descripcion=$row["cmp_descripcion"];
				  $ls_procede=$row["procede"];
				  $ls_comprobante=$row["comprobante"];
				  $ldt_fecha=$row["fecha"];
				  $ldt_fecaprmod = $row["fecaprmod"];
				  $ls_codestpro1=$row["codestpro1"];
				  $ls_codestpro2=$row["codestpro2"];
				  $ls_codestpro3=$row["codestpro3"];
				  $ls_codestpro4=$row["codestpro4"];
				  $ls_codestpro5=$row["codestpro5"];
				  $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				  $ls_spg_cuenta=$row["spg_cuenta"];
				  $ls_denominacion=$row["denominacion"];
				  $ls_procede_doc=$row["procede_doc"];
				  $ls_documento=$row["documento"];
				  $ls_operacion=$row["operacion"];
				  $ls_descripcion=$row["descripcion"];
				  $ld_monto=$row["monto"];
				  $ls_orden=$row["orden"];
			      $ld_asignado = 0;
	              $ld_aumentos = 0;
	              $ld_disminuciones = 0;
	              $ld_precompromisos = 0;
	              $ld_compromisos = 0;
	              $ld_causado = 0;
	              $ld_pagado = 0;

                  $arrResultado=$this->uf_spg_reporte_calcular_monto_operacion($ld_asignado,$ld_aumentos,$ld_disminuciones,
				                                                 $ld_precompromisos,$ld_compromisos,$ld_causado,
																 $ld_pagado,$ls_operacion,$ld_monto);
				 $ld_asignado=$arrResultado['ad_asignado'];
				 $ld_aumentos=$arrResultado['ad_aumentos'];
				 $ld_disminuciones=$arrResultado['ad_disminuciones'];
				 $ld_precompromisos=$arrResultado['ad_precompromisos'];
				 $ld_compromisos=$arrResultado['ad_compromisos'];
				 $ld_causado=$arrResultado['ad_causado'];
				 $ld_pagado=$arrResultado['ad_pagado'];
				 $ld_aumento=$ld_aumentos;
				 $ld_disminucion=$ld_disminuciones;
 				 $ls_procomp=$ls_procede.$ls_comprobante;
				 $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				 $this->dts_reporte->insertRow("cmp_descripcion",$ls_cmp_descripcion);
				 $this->dts_reporte->insertRow("programatica",$ls_programatica);
				 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
				 $this->dts_reporte->insertRow("documento",$ls_documento);
				 $this->dts_reporte->insertRow("fecha",$ldt_fecha);
				 $this->dts_reporte->insertRow("aumento",$ld_aumento);
				 $this->dts_reporte->insertRow("disminucion",$ld_disminucion);
				 $this->dts_reporte->insertRow("fecaprmod",$ldt_fecaprmod);
				 $this->dts_reporte->insertRow("procede",$ls_procede);
				 $this->dts_reporte->insertRow("procomp",$ls_procomp);
				 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			}//while
			$li_tot=$this->dts_reporte->getRowCount("spg_cuenta");
			if($li_tot==0)
			{$lb_valido=false;}
	  $this->SQL->free_result($rs_data);
	 }//else
	 
  return $lb_valido;
}//fin uf_spg_reporte_modificaciones_presupuestarias
/********************************************************************************************************************************/
	//////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "MODIFICACIONES PRESUPUESTARIAS NO APROBADAS " //
	/////////////////////////////////////////////////////////////////////////
    function uf_spg_reporte_modificaciones_presupuestarias_no_aprobadas($ai_rect,$ai_trans,$ai_insub,$ai_cred,$adt_fecini,
																		$adt_fecfin,$as_comprobante,$as_procede,$adt_fecha)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_modificaciones_presupuestarias_no_aprobadas
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso
	 //	   Description :	Reporte que genera la salida para las modificaciones presupuestarias
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    15/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_cad=$this->uf_spg_reporte_chequear_modificaciones($ai_rect,$ai_insub,$ai_trans,$ai_cred);
      $ls_cadena=str_replace('procede','MOV.procede',$ls_cad);
	  if (empty($as_comprobante))
	  {
	    $ls_cadena_2="";
	  }
	  else
	  {
	    $ls_cadena_2=" AND CMP.comprobante='".$as_comprobante."' AND CMP.procede='".$as_procede."'  AND MOV.fecha='".$adt_fecha."' ";
	  }
	  $ls_sql=" SELECT CMP.descripcion as cmp_descripcion, CMP.fecha as fecha, MOV.*,CTA.denominacion".
              "   FROM spg_dtmp_cmp MOV, sigesp_cmp_md CMP,spg_cuentas CTA ".
              "  WHERE CMP.codemp='".$ls_codemp."' ".
			  "    AND (".$ls_cadena.")  ".
			  "    AND MOV.fecha between '".$adt_fecini."' ".
			  "    AND '".$adt_fecfin."' ".
			  "    AND CMP.tipo_comp=2  ".
			  "    AND CMP.estapro=0  ".$ls_cadena_2." ".
			  "    AND CMP.codemp=MOV.codemp ".
			  "    AND MOV.codemp=CTA.codemp ".
			  "    AND CMP.procede=MOV.procede ".
			  "    AND CMP.comprobante=MOV.comprobante ".
			  "    AND CMP.fecha=MOV.fecha ".
			  "    AND MOV.codestpro1 = CTA.codestpro1 ".
			  "    AND MOV.codestpro2 = CTA.codestpro2 ".
			  "    AND MOV.codestpro3 = CTA.codestpro3 ".
			  "    AND MOV.codestpro4 = CTA.codestpro4 ".
			  "    AND MOV.codestpro5 = CTA.codestpro5 ".
			  "    AND MOV.spg_cuenta = CTA.spg_cuenta ".
              " ORDER BY  MOV.comprobante ";

	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report M?TODO->uf_spg_reporte_modificaciones_presupuestarias_no_aprobadas ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	 }
	 else
	 {
			while($row=$this->SQL->fetch_row($rs_data))
			{
				  $ls_cmp_descripcion=$row["cmp_descripcion"];
				  $ls_procede=$row["procede"];
				  $ls_comprobante=$row["comprobante"];
				  $ldt_fecha=$row["fecha"];
				  $ls_codestpro1=$row["codestpro1"];
				  $ls_codestpro2=$row["codestpro2"];
				  $ls_codestpro3=$row["codestpro3"];
				  $ls_codestpro4=$row["codestpro4"];
				  $ls_codestpro5=$row["codestpro5"];
				  $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				  $ls_spg_cuenta=$row["spg_cuenta"];
				  $ls_procede_doc=$row["procede_doc"];
				  $ls_documento=$row["documento"];
				  $ls_operacion=$row["operacion"];
				  $ls_descripcion=$row["descripcion"];
				  $ld_monto=$row["monto"];
				  $ls_orden=$row["orden"];
				  $ls_denominacion=$row["denominacion"];
			      $ld_asignado = 0;
	              $ld_aumentos = 0;
	              $ld_disminuciones = 0;
	              $ld_precompromisos = 0;
	              $ld_compromisos = 0;
	              $ld_causado = 0;
	              $ld_pagado = 0;

                 $arrResultado= $this->uf_spg_reporte_calcular_monto_operacion($ld_asignado,$ld_aumentos,$ld_disminuciones,
				                                                 $ld_precompromisos,$ld_compromisos,$ld_causado,
																 $ld_pagado,$ls_operacion,$ld_monto);
				 $ld_asignado=$arrResultado['ad_asignado'];
				 $ld_aumentos=$arrResultado['ad_aumentos'];
				 $ld_disminuciones=$arrResultado['ad_disminuciones'];
				 $ld_precompromisos=$arrResultado['ad_precompromisos'];
				 $ld_compromisos=$arrResultado['ad_compromisos'];
				 $ld_causado=$arrResultado['ad_causado'];
				 $ld_pagado=$arrResultado['ad_pagado'];
				 $ld_aumento=$ld_aumentos;
				 $ld_disminucion=$ld_disminuciones;
                 $ls_procomp=$ls_procede.$ls_comprobante;
				 $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				 $this->dts_reporte->insertRow("cmp_descripcion",$ls_cmp_descripcion);
				 $this->dts_reporte->insertRow("programatica",$ls_programatica);
				 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
				 $this->dts_reporte->insertRow("documento",$ls_documento);
				 $this->dts_reporte->insertRow("fecha",$ldt_fecha);
				 $this->dts_reporte->insertRow("aumento",$ld_aumento);
				 $this->dts_reporte->insertRow("disminucion",$ld_disminucion);
				 $this->dts_reporte->insertRow("procede",$ls_procede);
				 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
   				 $this->dts_reporte->insertRow("procomp",$ls_procomp);

			}//while
			$li_tot=$this->dts_reporte->getRowCount("spg_cuenta");
			if($li_tot==0)
			{$lb_valido=false;}
	  $this->SQL->free_result($rs_data);
	 }//else
  return $lb_valido;
}//fin uf_spg_reporte_modificaciones_presupuestarias_no_aprobadas
/********************************************************************************************************************************/
	function uf_spg_reporte_chequear_modificaciones( $ai_rect,$ai_insub,$ai_trans,$ai_cred )
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_chequear_modificaciones
	 //         Access :	private
	 //     Argumentos :    $ai_rect   // chequear rectificaciones
     //              	    $ai_insub // chequear insubsistencias
	 //                     $ai_trans //  chequear transpaso
	 //                     $ai_cred  // chequear credito
     //	       Returns :	Retorna una cadena con las opciones de las modificaciones presupuestarias seelccionadas
	 //	   Description :	Verifica segun los parametros y construye una cadena para construir el reporte
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    15/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	 if($ai_rect==1) { $ls_cadena1="procede ='SPGREC' OR "; }
	 else{ $ls_cadena1="";}
	 if($ai_insub==1) { $ls_cadena2="procede ='SPGINS' OR "; }
	 else{ $ls_cadena2="";}
	 if($ai_trans==1) { $ls_cadena3="procede ='SPGTRA' OR "; }
	 else{ $ls_cadena3="";}
	 if($ai_cred==1) { $ls_cadena4="procede ='SPGCRA' OR "; }
	 else{ $ls_cadena4="";}

	 $ls_cadena=$ls_cadena1.$ls_cadena2.$ls_cadena3.$ls_cadena4;
	 if(!empty($ls_cadena))
	 {
	   $ls_cadena=substr($ls_cadena,0,strlen($ls_cadena)-3);
	 }
	 return $ls_cadena;
}//uf_spg_reporte_chequear_modificaciones
/********************************************************************************************************************************/
    function uf_spg_reporte_calcular_monto_operacion($ad_asignado,$ad_aumentos,$ad_disminuciones,$ad_precompromisos,
	                                                 $ad_compromisos,$ad_causado,$ad_pagado,$as_operacion,$ad_monto)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_calcular_monto_operacion
	 //         Access :	private
	 //     Argumentos :    $ad_asignado // monto asignado (referencia)
	 //                     $ad_aumentos // monto aumento (referencia)
	 //                     $ad_disminuciones // monto disminuciones (referencia)
	 //                     $ad_precompromisos // monto precompromiso (referencia)
	 //                     $ad_compromisos  // monto compromiso (referencia)
     //	                    $ad_causado //  monto causado (referencia)
	 //                     $ad_pagado  // monto pagado  (referencia)
	 //                     $as_operacion // operacion
	 //                     $ad_monto  //  monto ed la operacion
     //	       Returns :	Retorna  por referencia los valores de cada monto
	 //	   Description :	Verifica segun la operacion y calcula el monto
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    16/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

     $ls_operacion = $this->sigesp_int_spg->uf_operacion_codigo_mensaje($as_operacion);
     $li_pos_i=strpos($ls_operacion,"I"); //I-Asignacion
     if (!($li_pos_i===false))
     {
	   $ad_asignado = $ad_asignado + $ad_monto;
     }
     $li_pos_a=strpos($ls_operacion,"A"); //A-Aumentos
     if (!($li_pos_a===false))
     {
	   $ad_aumentos = $ad_aumentos + $ad_monto;
     }
     $li_pos_d=strpos($ls_operacion,"D"); //D-Disminuci?n
     if (!($li_pos_d===false))
     {
	   $ad_disminuciones = $ad_disminuciones + $ad_monto;
     }
	 $li_pos_r=strpos($ls_operacion,"R"); //R-PreComprometer
	 if (!($li_pos_r===false))
     {
	   $ad_precompromisos = $ad_precompromisos + $ad_monto;
     }
	 $li_pos_o=strpos($ls_operacion,"O"); //	O-Comprometer
	 if (!($li_pos_o===false))
	 {
	   $ad_compromisos = $ad_compromisos + $ad_monto;
     }
	 $li_pos_c=strpos($ls_operacion,"C"); //	C-Causar
	 if (!($li_pos_c===false))
	 {
	   $ad_causado = $ad_causado + $ad_monto;
     }
	 $li_pos_p=strpos($ls_operacion,"P");  // P-Pagar
	 if (!($li_pos_p===false))
	 {
	   $ad_pagado = $ad_pagado + $ad_monto;
     }
		$arrResultado['ad_asignado']=$ad_asignado;
		$arrResultado['ad_aumentos']=$ad_aumentos;
		$arrResultado['ad_disminuciones']=$ad_disminuciones;
		$arrResultado['ad_precompromisos']=$ad_precompromisos;
		$arrResultado['ad_compromisos']=$ad_compromisos;
		$arrResultado['ad_causado']=$ad_causado;
		$arrResultado['ad_pagado']=$ad_pagado;
		return $arrResultado;		
}// fin uf_spg_reporte_calcular_monto_operacion
/********************************************************************************************************************************/
    function uf_spg_reporte_chequear_autorizacion_traspaso($as_procede, $as_comprobante, $adt_fecha, $as_doc_autor, $as_autorizante,
	                                                       $adt_fecha_aut, $as_observacion,$as_codemp)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_chequear_autorizacion_traspaso
	 //         Access :	private
	 //     Argumentos :    $as_procede // procede
	 //                     $as_comprobante // numero de comprobante
	 //                     $adt_fecha // fecha
	 //                     $as_doc_autor // documento del autorizado (referencia)
	 //                     $as_autorizante  // autorizante (referencia)
     //	                    $adt_fecha_aut //  fecha autorizacion (referencia)
	 //                     $as_observacion  // observacion (referencia)
	 //                     $as_codemp  //  codigo de la empresa
     //	       Returns :	Retorna  por referencia los valores para la autorizacion de traspaso
	 //	   Description :	Verifica si existe la autorizacion de traspaso
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    16/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $lb_valido=true;
	 $ls_sql=" SELECT * ".
             " FROM   spg_aut_comp ".
             " WHERE  codemp='".$as_codemp."'  AND procede='".$as_procede."' AND ".
			 "        comprobante='".$as_comprobante."' AND fecha='".$adt_fecha."' ";
	 $rs_sel=$this->SQL->select($ls_sql);
	 if($rs_sel===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report M?TODO->uf_spg_reporte_chequear_autorizacion_traspaso ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	 }
	 else
	 {
			if($row=$this->SQL->fetch_row($rs_sel))
			{
			  $as_doc_autor=$row["docauttra"];
			  $as_autorizante=$row["auttra"];
			  $adt_fecha_aut=$row["fecauttra"];
			  $as_observacion=$row["obstra"];
			}
			else
			{
			  $as_doc_autor="";
			  $as_autorizante="";
			  $adt_fecha_aut="";
			  $as_observacion="";
			}
	}
	  $this->SQL->free_result($rs_sel);
		$arrResultado['as_doc_autor']=$as_doc_autor;
		$arrResultado['as_autorizante']=$as_autorizante;
		$arrResultado['adt_fecha_aut']=$adt_fecha_aut;
		$arrResultado['as_observacion']=$as_observacion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
}//fin uf_spg_reporte_chequear_autorizacion_traspaso
/********************************************************************************************************************************/
    function uf_spg_reporte_select_min_cuenta($as_spg_cuenta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_cuenta
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta minima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(spg_cuenta) as spg_cuenta ".
             " FROM spg_cuentas ".
             " WHERE codemp = '".$ls_codemp."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_cuenta ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_spg_cuenta=$row["spg_cuenta"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
	$arrResultado['as_spg_cuenta']=$as_spg_cuenta;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
  }//uf_spg_reporte_select_min_cuenta
/********************************************************************************************************************************/
    function uf_spg_reporte_select_max_cuenta($as_spg_cuenta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_cuenta
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta maxima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(spg_cuenta) as spg_cuenta ".
             " FROM spg_cuentas ".
             " WHERE codemp = '".$ls_codemp."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_max_cuenta ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_spg_cuenta=$row["spg_cuenta"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
	$arrResultado['as_spg_cuenta']=$as_spg_cuenta;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
  }//uf_spg_reporte_select_max_cuenta
/********************************************************************************************************************************/
    function uf_spg_reporte_select_min_programatica($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_programatica
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta maxima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $arrResultado=$this->uf_spg_reporte_select_min_codestpro1($as_codestpro1);
	 $as_codestpro1=$arrResultado['as_codestpro1'];
	 $as_estclades=$arrResultado['estcla'];	 
	 $lb_valido=$arrResultado['lb_valido'];
	 if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro2($as_codestpro1,$as_estclades,$as_codestpro2);
		 $as_codestpro2=$arrResultado['as_codestpro2'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro3($as_codestpro1,$as_estclades,$as_codestpro2,$as_codestpro3);
		 $as_codestpro3=$arrResultado['as_codestpro3'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro4($as_codestpro1,$as_estclades,$as_codestpro2,$as_codestpro3,$as_codestpro4);
		 $as_codestpro4=$arrResultado['as_codestpro4'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro5($as_codestpro1,$as_estclades,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5);
		 $as_codestpro5=$arrResultado['as_codestpro5'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estclades']=$as_estclades;
		$arrResultado['lb_valido']=$lb_valido;
		
		return $arrResultado;		
  }//uf_spg_reporte_select_max_cuenta
/********************************************************************************************************************************/
    function uf_spg_reporte_select_min_codestpro1($as_codestpro1)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro1
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 (referencia)
	 //                     $as_estcla      // Estatus de Clasificacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $concatenar = $this->con->concat('codestpro1','estcla');
	 $lb_valido=true;
	 $ls_sql=" SELECT min(".$concatenar."), codestpro1, estcla ".
             " FROM   spg_ep1 ".
             " WHERE  codemp = '".$ls_codemp."' ".
			 " AND codestpro1<>'-------------------------'";

	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro1 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro1=$row["codestpro1"];
		   $as_estcla=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro1
/********************************************************************************************************************************/
    function uf_spg_reporte_select_min_codestpro2($as_codestpro1,$as_estclades,$as_codestpro2)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro2
	 //         Access :	private
	 //     Argumentos :    $as_codestpro2  // codigo de estructura programatica 2 (referencia)
	 //                     $as_codestpro1  // codigo de estructura programatica 1
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro2) as codestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1= '".$as_codestpro1."' AND estclades= '".$as_estclades."' ".
			 " AND codestpro1<>'-------------------------' AND AND codestpro2<>'-------------------------'";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro2 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro2=$row["codestpro2"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro2
/********************************************************************************************************************************/
    function uf_spg_reporte_select_min_codestpro3($as_codestpro1,$as_estclades,$as_codestpro2,$as_codestpro3)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro3
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3 (referencia)
	 //                     $as_codestpro1  // codigo de estructura programatica 1
	 //                     $as_codestpro2  // codigo de estructura programatica 2
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro3) as codestpro3 ".
             " FROM   spg_ep3 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1= '".$as_codestpro1."' AND codestpro2= '".$as_codestpro2."'  AND estclades= '".$as_estclades."' ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------' ".
			 " AND codestpro3<>'-------------------------' ";

	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro3 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro3=$row["codestpro3"];
		   //print "PROGRAMATICA MINIMA NIVEL 3: ".$as_codestpro3."<br>";
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro3

  function uf_spg_reporte_select_min_codestpro4($as_codestpro1,$as_estclades,$as_codestpro2,$as_codestpro3,$as_codestpro4)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro4
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3
	 //                     $as_codestpro1  // codigo de estructura programatica 1
	 //                     $as_codestpro2  // codigo de estructura programatica 2
	 //                     $as_codestpro4  // codigo de estructura programatica 4 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    12/02/2007          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro4) as codestpro4 ".
             " FROM   spg_ep4 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1= '".$as_codestpro1."' AND codestpro2= '".$as_codestpro2."' AND codestpro3= '".$as_codestpro3."' AND estclades= '".$as_estclades."' ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------' ".
			 " AND codestpro3<>'-------------------------' AND codestpro4<>'-------------------------' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro4 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro4=$row["codestpro4"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro4

   function uf_spg_reporte_select_min_codestpro5($as_codestpro1,$as_estclades,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro4
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3
	 //                     $as_codestpro1  // codigo de estructura programatica 1
	 //                     $as_codestpro2  // codigo de estructura programatica 2
	 //                     $as_codestpro4  // codigo de estructura programatica 4
	 //                     $as_codestpro5  // codigo de estructura programatica 5 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    12/02/2007          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro5) as codestpro5 ".
             " FROM   spg_ep5 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1= '".$as_codestpro1."' AND codestpro2= '".$as_codestpro2."' AND codestpro3= '".$as_codestpro3."' AND codestpro4= '".$as_codestpro4."'  AND estclades= '".$as_estclades."' ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------' ".
			 " AND codestpro3<>'-------------------------' AND codestpro4<>'-------------------------' ".
			 " AND codestpro5<>'-------------------------' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro4 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro5=$row["codestpro5"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro5
/********************************************************************************************************************************/
    function uf_spg_reporte_select_max_programatica($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_programatica
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta maxima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $arrResultado=$this->uf_spg_reporte_select_max_codestpro1($as_codestpro1);
	 $as_codestpro1=$arrResultado['as_codestpro1'];
	 $lb_valido=$arrResultado['lb_valido'];
	 if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro2($as_codestpro1,$as_codestpro2);
		 $as_codestpro2=$arrResultado['as_codestpro2'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3);
		 $as_codestpro3=$arrResultado['as_codestpro3'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	  if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4);
		 $as_codestpro4=$arrResultado['as_codestpro4'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	  if($lb_valido)
	 {
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5);
		 $as_codestpro5=$arrResultado['as_codestpro5'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_programatica
/********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro1($as_codestpro1)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro1
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Modificado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion : 12/02/2008     Hora : 01:29 pm
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
	 {
	  $ls_sql="SELECT * FROM spg_ep1 WHERE codemp='".$ls_codemp."' AND codestpro1<>'-------------------------' ORDER BY codestpro1  desc limit 1 ";
	 }
	 else
	 {
	  $ls_sql="SELECT first 1 * FROM spg_ep1 WHERE codemp='".$ls_codemp."' AND codestpro1<>'-------------------------' ORDER BY codestpro1 desc";
	 }
	 //print $ls_sql."<br>";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_max_codestpro1 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro1=$row["codestpro1"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro1
/********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro2($as_codestpro1,$as_codestpro2)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro2
	 //         Access :	private
	 //     Argumentos :    $as_codestpro2  // codigo de estructura programatica 2 (referencia)
	 //                     $as_codestpro1  // codigo de estructura programatica 1
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro2) as codestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1='".$as_codestpro1."' ".
			 "  AND codestpro1<>'-------------------------' AND AND codestpro2<>'-------------------------' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_max_codestpro2 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro2=$row["codestpro2"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro2
/********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro3
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3 (referencia)
	 //                     $as_codestpro1  // codigo de estructura programatica 1
	 //                     $as_codestpro2  // codigo de estructura programatica 2
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    19/07/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro3) as codestpro3 ".
             " FROM   spg_ep3 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1='".$as_codestpro1."' AND codestpro2='".$as_codestpro2."'  ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------' ".
			 " AND codestpro3<>'-------------------------'";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_max_codestpro3 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro3=$row["codestpro3"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro3

 function uf_spg_reporte_select_max_codestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro4
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3
	 //                     $as_codestpro1  // codigo de estructura programatica 1
	 //                     $as_codestpro2  // codigo de estructura programatica 2
	 //                     $as_codestpro4  // codigo de estructura programatica 4 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    12/02/2007          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro4) as codestpro4 ".
             " FROM   spg_ep4 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1='".$as_codestpro1."' AND codestpro2='".$as_codestpro2."' AND codestpro3='".$as_codestpro3."' ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------'".
			 " AND codestpro3<>'-------------------------' AND codestpro4<>'-------------------------'";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro4 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro4=$row["codestpro4"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro4

   function uf_spg_reporte_select_max_codestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro5
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3
	 //                     $as_codestpro1  // codigo de estructura programatica 1
	 //                     $as_codestpro2  // codigo de estructura programatica 2
	 //                     $as_codestpro4  // codigo de estructura programatica 4
	 //                     $as_codestpro5  // codigo de estructura programatica 5 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    12/02/2007          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro5) as codestpro5 ".
             " FROM   spg_ep5 ".
             " WHERE  codemp = '".$ls_codemp."' AND codestpro1='".$as_codestpro1."' AND codestpro2='".$as_codestpro2."' AND codestpro3='".$as_codestpro3."' AND codestpro4='".$as_codestpro4."'  ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------'".
			 " AND codestpro3<>'-------------------------' AND codestpro4<>'-------------------------'".
			 " AND codestpro5<>'-------------------------'";
	//		 print $ls_sql."<br>";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_min_codestpro4 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_codestpro5=$row["codestpro5"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro5
/********************************************************************************************************************************/

function uf_spg_reporte_acumulado_cuentas2($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                          $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											  $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,$ai_nivel,
											  $ab_subniveles,$ai_MenorNivel,$as_cuentades,$as_cuentahas,$as_codfuefindes,
											  $as_codfuefinhas,$as_estclades,$as_estclahas,$rs_cuentas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_acumulado_cuentas2
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     ai_nivel    // nivel de la cuenta
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels
	 //                     ai_MenorNivel  // variabvle que determina el menor nivel de la cuenta
	 //                     as_codfuefindes  // codigo fuente financiamiento desde solicitado por Gobernacion de Apure
	 //                     as_codfuefinhas  // codigo fuente financiamiento hasta solicitado por Gobernacion de Apure
	 //                     as_estclades   // estatus desde de clasificaicones de la estructura presupuestaria IPSFA
	 //                     as_estclahas   // estatus hasta de clasificaicones de la estructura presupuestaria IPSFA
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Mar?a Beatriz Unda
	 // Fecha Creaci?n :    13/08/08          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$lb_existe = false;
		$lb_valido = true;
		$lb_ok = true;
        $ld_total=0;
		$asignado_total=0;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_seguridad="";
	    $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('PCT',$ls_seguridad);
	    $this->dts_reporte->resetds("spg_cuenta");
        $ls_str_sql_where="";
		$dts_cuentas=new class_datastore();
        $arrResultado=$this->uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,                                              $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
                                             $as_codestpro4_des,$as_codestpro5_des,$ls_Sql_Where,$ls_str_estructura_from,
											 $ls_str_estructura_to,$as_estclades,$as_estclahas);
		$ls_Sql_Where=$arrResultado['as_Sql_Where'];
		$ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
		$ls_str_estructura_to=$arrResultado['as_str_estructura_to'];

		$ls_str_sql_where="WHERE PCT.codemp='".$ls_codemp."'";
		$ls_Sql_Where = trim($ls_Sql_Where);

        if (!empty($ls_Sql_Where) )
        {
           $ls_str_sql_where="WHERE PCT.codemp='".$ls_codemp."' AND ".$ls_Sql_Where;
        }
        
       
        $ls_mysql=" SELECT DISTINCT PCT.spg_cuenta,PCT.status,PCT.nivel, MIN(PCT.denominacion) AS denominacion,  ".
                  "                 sum(PCT.asignado) as asignado ".
                  " FROM spg_cuentas PCT ".$ls_str_sql_where." AND ".
                  "      PCT.spg_cuenta BETWEEN '".trim($as_cuentades)."' AND '".trim($as_cuentahas)."' ".
                  "       AND (PCT.nivel<='".$ai_nivel."') ".$ls_seguridad." ".
				  " GROUP BY PCT.spg_cuenta, PCT.nivel,PCT.status ".
                  " ORDER BY PCT.spg_cuenta ";
			//print $ls_mysql."<br><br>";
			//die();
		$rs_cuentas=$this->SQL->select($ls_mysql);
		if($rs_cuentas===false)
		{   //error interno sql
		   $this->io_msg->message("Error en Reporte Acumulado Por Cuentas  ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}

	$arrResultado['ai_MenorNivel']=$ai_MenorNivel;
	$arrResultado['rs_cuentas']=$rs_cuentas;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   } // fin function uf_spg_reporte_acumulado_cuentas2
   
   function uf_obtener_digito_cuenta($as_spgcuenta, $as_nivel) {
   		$formato = $_SESSION['la_empresa']['formpre'];
   		$arrFormato = explode('-', $formato);
   		$digitos = '';
   		for ($i = 0; $i <= $as_nivel - 1; $i++) {
   			$digitos .= $arrFormato[$i];
   		}
   		$numDigitos = strlen($digitos);
   		
   		return substr($as_spgcuenta, 0, $numDigitos);
   }
   
   function uf_buscar_asignado_fuente($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                  $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
									  $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas,$as_codfuefindes,
									  $as_codfuefinhas,$as_spgcuenta, $as_nivel) {
   		$asignado = 0;
   		$cuenta = $this->uf_obtener_digito_cuenta($as_spgcuenta, $as_nivel);
   		$ls_sql= "SELECT SUM (monto) AS monto
  					FROM spg_cuenta_fuentefinanciamiento CF
  					WHERE CF.codfuefin BETWEEN '{$as_codfuefindes}' AND '{$as_codfuefinhas}' 
   				    AND (CF.codestpro1|| CF.codestpro2|| CF.codestpro3|| CF.codestpro4|| CF.codestpro5|| CF.estcla) 
   				    BETWEEN '".$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades."' 
   				    AND '".$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas."' 
  					AND CF.spg_cuenta LIKE '{$cuenta}%'";
   		//echo $ls_sql;
   		//die();
		$rs_asignado=$this->SQL->select($ls_sql);
		if (!$rs_asignado->EOF) {
			$asignado = $rs_asignado->fields['monto'];
		}
		unset($rs_asignado);
		
		return $asignado;
   }

/********************************************************************************************************************************/

function uf_spg_reporte_detalle_acumulado_cuentas($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
	                                              $as_codestpro5,$as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,
	                                              $as_codestpro5h,$as_estclades,$as_estclahas,
												  $as_spg_cuenta,$adt_fecfin,$as_codfuefindes,$as_codfuefinhas,$rs_data2)

{

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_detalle_acumulado_cuentas
	 //         Access :	private
	 //     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	 //                     as_spg_cuenta   // cuenta presupestaria
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...)
	 //     Creado por :    Ing. Mar?a Beatriz Unda
	 // Fecha Creaci?n :    13/08/08          Fecha ?ltima Modificacion : 20/08/2009
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$lb_valido = true;
		if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		{
			 $criterio=" AND (CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)
                         BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						 AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}
		if ($_SESSION["ls_gestor"]=="POSTGRES" || $_SESSION["ls_gestor"]=="oci8po")
		{
			 $criterio=" AND (spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla
                       	 BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						 AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}
		

	    $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";

        $ls_sql=	" SELECT spg_dt_cmp.spg_cuenta,  ".
                  	" (CASE spg_dt_cmp.operacion  ".
					" WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
					" END) as asignar, ".
					" (CASE spg_dt_cmp.operacion ".
					" WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
					" END) as aumento, ".
					" (CASE spg_dt_cmp.operacion  ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
					" END) as compromiso,".
					" (CASE spg_dt_cmp.operacion".
					" WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
					" END) as disminucion, ".
					" (CASE spg_dt_cmp.operacion ".
					" WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
					" END) as causado, ".
					" (CASE spg_dt_cmp.operacion ".
					" WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
					" END) as precompromiso, ".
					" (CASE spg_dt_cmp.operacion ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
					" END) as pagado ".
                  	" FROM spg_dt_cmp".
					" WHERE spg_dt_cmp.spg_cuenta LIKE '$as_spg_cuenta'".
					" AND spg_dt_cmp.fecha <= '$adt_fecfin'".$criterio.
        			" AND spg_dt_cmp.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ".
					" GROUP BY spg_dt_cmp.spg_cuenta, spg_dt_cmp.operacion".
                  	" ORDER BY spg_dt_cmp.spg_cuenta ";
		//echo $ls_sql.'<br>';
		//die();
		$rs_data2=$this->SQL->select($ls_sql);
		if($rs_data2===false)
		{   //error interno sql
		   $this->io_msg->message("Error en Reporte Acumulado Por Cuentas  ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}

	$arrResultado['rs_data']=$rs_data2;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   } // fin uf_spg_reporte_detalle_acumulado_cuentas
//--------------------------------------------------------------------------------------------------------------------------------------
///-----------------------------------------------------------------------------------------------------------------------------------
     function uf_buscar_monedas ($as_codmon, $as_fechades, $as_fechahas)
	 {
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_buscar_monedas
	 //         Access :	private
	 //     Argumentos :
     //	       Returns :	Retorna las tasas 1 y la tasa 2
	 //	   Description :	Reporte las tasas 1 y la tasa 2
	 //     Creado por :    Ing. Jennifer Rivero
	 // Fecha Creaci?n :    21/11/08          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido = true;
		$ls_tasa=0;
		$ls_criterio="";
		if ($as_codmon!="")
		{
			$ls_criterio="	   AND sigesp_dt_moneda.codmon='".$as_codmon."'            ";
		}
		else
		{
			$ls_criterio="     AND sigesp_moneda.estmonpri='1' ";
		}
		$ls_sql=" select MAX(sigesp_dt_moneda.fecha),sigesp_dt_moneda.tascam1, sigesp_dt_moneda.tascam2 ".
				"	  from  sigesp_dt_moneda, sigesp_moneda               ".
				"	 where sigesp_dt_moneda.codmon= sigesp_moneda.codmon  ".
				"	   AND sigesp_dt_moneda.fecha between '".$as_fechades."' AND '".$as_fechahas."' ".$ls_criterio.
				"	 group by sigesp_dt_moneda.codmon, sigesp_dt_moneda.fecha, ".
				"          sigesp_dt_moneda.tascam1, sigesp_dt_moneda.tascam2  ".
				"	 order by sigesp_dt_moneda.codmon		                          ";
		//print $ls_sql."<br>";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
		   $this->io_msg->message("Error en uf_buscar_monedas  ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_tasa=$row["tascam1"];
			}
			else
			{
			   $ls_tasa = $this->uf_buscar_tasas ($as_codmon);
			}
			$this->SQL->free_result($rs_data);
		}// fin del else
		return $ls_tasa;
	 }// fin uf_buscar_monedas
//---------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------
    function uf_modificacion_por_fuente_finan($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                                          $as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,$as_codestpro5h,                                              $as_estclades,$as_estclahas,$as_fechades, $as_fechahas,
											  $as_codfuefindes, $as_codfuefinhas, $as_codmon, $as_cuentades, $as_cuentahas)
	{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_modificacion_por_fuente_finan
	 //         Access :	private
	 //     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	 //                     as_spg_cuenta   // cuenta presupestaria
     //	       Returns :	Retorna las cuentas y estructuras que poseean modificaciones presupuesatrias
	 //	   Description :	Reporte que muestra las estructuras y cuentas que poseen modificaciones presupuestarias
	 //     Creado por :    Ing. Jennifer Rivero
	 // Fecha Creaci?n :    12/11/08          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   $lb_valido = true;
	   $criterio="";
	   $criterio2="";
	   $criterio3="";
	   $criterio4="";
	   $criterio5="";
		if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		{
			$criterio=" AND (CONCAT(spg_dtmp_cmp.codestpro1,spg_dtmp_cmp.codestpro2,spg_dtmp_cmp.codestpro3,spg_dtmp_cmp.codestpro4,spg_dtmp_cmp.codestpro5,spg_dtmp_cmp.estcla)
                       	BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";

			$criterio5=" AND (CONCAT(spg_cuentas.codestpro1,spg_cuentas.codestpro2,spg_cuentas.codestpro3,spg_cuentas.codestpro4,spg_cuentas.codestpro5,spg_cuentas.estcla)
                       	BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}
		if ($_SESSION["ls_gestor"]=="POSTGRES")
		{
			$criterio=" AND (spg_dtmp_cmp.codestpro1||spg_dtmp_cmp.codestpro2||spg_dtmp_cmp.codestpro3||spg_dtmp_cmp.codestpro4||spg_dtmp_cmp.codestpro5||spg_dtmp_cmp.estcla  BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
				 AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";

			$criterio5=" AND (spg_cuentas.codestpro1||spg_cuentas.codestpro2||spg_cuentas.codestpro3||spg_cuentas.codestpro4||spg_cuentas.codestpro5||spg_cuentas.estcla  BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
			 	 AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}

		if ((!empty($as_fechades))&&(!empty($as_fechahas)))
		{
			$criterio2=" and sigesp_cmp.fecha between '".$as_fechades."' and '".$as_fechahas."'";
		}
		else
		{
		    $anoact=$this->dts_empresa["periodo"];
			$as_fechades=substr($anoact,0,3)."01-01";
		    $as_fechahas=date();
			$criterio2=" and sigesp_cmp.fecha between '".$as_fechades."' and '".$as_fechahas."'";
		}

		if ((!empty($as_codfuefindes))&&(!empty($as_codfuefinhas)))
		{
			 $criterio3=" AND sigesp_cmp_md.codfuefin between '".$as_codfuefindes."' AND '".$as_codfuefinhas."'";
		}

		if ((!empty($as_cuentades))&&(!empty($as_cuentahas)))
		{
			$criterio4=" AND spg_cuentas.spg_cuenta between '".$as_cuentades."' AND '".$as_cuentahas."' ";
		}
		$ls_sql=" select  sum(spg_dtmp_cmp.monto) as monto1, spg_dtmp_cmp.operacion as operacion1, ".
		        "         spg_dtmp_cmp.spg_cuenta as cuenta1,spg_cuentas.denominacion as denno1,   ".
                "         spg_dtmp_cmp.procede as procede1, sigesp_cmp_md.codfuefin as codfuefin1, ".
				"         spg_cuentas.asignado as asignado, ".
                "         0.00 as monto2, '' as operacion2, '' as cuenta2, '' as denno2, '' as procede2, '--' as codfuefin2, ".
				"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				"         spg_cuentas.codestpro4, spg_cuentas.codestpro5, spg_cuentas.estcla         ".
                "   from  sigesp_cmp, spg_dtmp_cmp, spg_cuentas, sigesp_cmp_md ".
                "  where  sigesp_cmp.codemp=spg_dtmp_cmp.codemp ".
                "    and sigesp_cmp.comprobante=spg_dtmp_cmp.comprobante ".
				"    and spg_cuentas.codemp=spg_dtmp_cmp.codemp          ".
				"    and spg_cuentas.codestpro1=spg_dtmp_cmp.codestpro1  ".
				"    and spg_cuentas.estcla=spg_dtmp_cmp.estcla          ".
				"    and spg_cuentas.codestpro2=spg_dtmp_cmp.codestpro2  ".
				"    and spg_cuentas.codestpro3=spg_dtmp_cmp.codestpro3  ".
				"    and spg_cuentas.codestpro4=spg_dtmp_cmp.codestpro4  ".
				"    and spg_cuentas.codestpro5=spg_dtmp_cmp.codestpro5  ".
				"    and spg_cuentas.spg_cuenta=spg_dtmp_cmp.spg_cuenta  ".
				"    and spg_cuentas.status='C'                          ".
				"    and sigesp_cmp.procede='SPGTRA'                     ".
				"    and sigesp_cmp_md.codemp=sigesp_cmp.codemp          ".
				"    and sigesp_cmp_md.procede=sigesp_cmp.procede        ".
				"    and sigesp_cmp_md.comprobante=sigesp_cmp.comprobante".
				"    and sigesp_cmp_md.codfuefin='01'                    ".
				$criterio.$criterio2.$criterio4.
				"    AND spg_dtmp_cmp.comprobante not in (select a.comprobante       ".
				"									 from spg_dtmp_cmp a,spg_ep3 b   ".
				"									where a.procede='SPGTRA'         ".
				"									  and a.comprobante=spg_dtmp_cmp.comprobante ".
				"									  and a.operacion='DI'   ".
				"									  and b.codemp=a.codemp  ".
				"									  and b.codestpro1=a.codestpro1 ".
				"									  and b.codestpro2=a.codestpro2 ".
				"									  and b.codestpro3=a.codestpro3 ".
				"									  and b.estcla=a.estcla         ".
				"									  and b.estreradi='1')          ".
                "  group by spg_dtmp_cmp.operacion,                                 ".
				"           spg_dtmp_cmp.spg_cuenta,spg_cuentas.denominacion,spg_dtmp_cmp.procede, ".
				"           sigesp_cmp_md.codfuefin, spg_cuentas.asignado, ".
				"           spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				"           spg_cuentas.codestpro4, spg_cuentas.codestpro5, spg_cuentas.estcla      ";

		$ls_sql=$ls_sql. "  UNION  ".
				" select  0.00 as monto1, '' as operacion1, '' as cuenta1, '' as denno1, '' as procede1,'--' as codfuefin1,".
				"         spg_cuentas.asignado as asignado, ".
				"  		  sum(spg_dtmp_cmp.monto) as monto2, spg_dtmp_cmp.operacion as operacion2, ".
				"         spg_dtmp_cmp.spg_cuenta as cuenta2,spg_cuentas.denominacion as denno2,   ".
				"		  spg_dtmp_cmp.procede as procede2,sigesp_cmp_md.codfuefin as codfuefin2,   ".
				"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				"         spg_cuentas.codestpro4, spg_cuentas.codestpro5 , spg_cuentas.estcla        ".
				"   from  sigesp_cmp, spg_dtmp_cmp, spg_cuentas, sigesp_cmp_md                     ".
				"  where  sigesp_cmp.codemp=spg_dtmp_cmp.codemp                                    ".
				"    and sigesp_cmp.comprobante=spg_dtmp_cmp.comprobante ".
				"    and spg_cuentas.codemp=spg_dtmp_cmp.codemp          ".
				"    and spg_cuentas.codestpro1=spg_dtmp_cmp.codestpro1  ".
				"    and spg_cuentas.estcla=spg_dtmp_cmp.estcla          ".
				"    and spg_cuentas.codestpro2=spg_dtmp_cmp.codestpro2  ".
				"    and spg_cuentas.codestpro3=spg_dtmp_cmp.codestpro3  ".
				"    and spg_cuentas.codestpro4=spg_dtmp_cmp.codestpro4  ".
				"    and spg_cuentas.codestpro5=spg_dtmp_cmp.codestpro5  ".
				"    and spg_cuentas.spg_cuenta=spg_dtmp_cmp.spg_cuenta  ".
				"    and spg_cuentas.status='C'                          ".
				"    and sigesp_cmp.procede='SPGTRA'                     ".
				"    and sigesp_cmp_md.codemp=sigesp_cmp.codemp          ".
				"    and sigesp_cmp_md.procede=sigesp_cmp.procede        ".
				"    and sigesp_cmp_md.comprobante=sigesp_cmp.comprobante".
				"    and sigesp_cmp_md.codfuefin='01'				     ".
				"    AND spg_dtmp_cmp.operacion='AU'                     ".
				$criterio.$criterio2.$criterio4.
				"    AND spg_dtmp_cmp.comprobante in (select a.comprobante           ".
				"									 from spg_dtmp_cmp a,spg_ep3 b   ".
				"									where a.procede='SPGTRA'         ".
				"									  and a.comprobante=spg_dtmp_cmp.comprobante ".
				"									  and a.operacion='DI'           ".
				"									  and b.codemp=a.codemp          ".
				"									  and b.codestpro1=a.codestpro1  ".
				"									  and b.codestpro2=a.codestpro2  ".
				"									  and b.codestpro3=a.codestpro3  ".
				"									  and b.estcla=a.estcla          ".
				"									  and b.estreradi='1')           ".
                "    group by spg_dtmp_cmp.operacion, ".
				"             spg_dtmp_cmp.spg_cuenta,spg_cuentas.denominacion,spg_dtmp_cmp.procede,sigesp_cmp_md.codfuefin,".
				"             spg_cuentas.asignado,  ".
				"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				"         spg_cuentas.codestpro4, spg_cuentas.codestpro5, spg_cuentas.estcla ";

 			$ls_sql=$ls_sql. "  UNION  ".
					"  select  sum(spg_dtmp_cmp.monto) as monto1, spg_dtmp_cmp.operacion as operacion1, ".
					"          spg_dtmp_cmp.spg_cuenta as cuenta1,spg_cuentas.denominacion as denno1,   ".
					"          spg_dtmp_cmp.procede as procede1, sigesp_cmp_md.codfuefin as codfuefin1, ".
					"          spg_cuentas.asignado as asignado, ".
					"		   0.00 as monto2, '' as operacion2, '' as cuenta2, '' as denno2, '' as procede2, ".
					"          '--' as codfuefin2, ".
					"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				    "         spg_cuentas.codestpro4, spg_cuentas.codestpro5, spg_cuentas.estcla         ".
					"	  from  sigesp_cmp, spg_dtmp_cmp, spg_cuentas, sigesp_cmp_md ".
					"	where sigesp_cmp.codemp=spg_dtmp_cmp.codemp                  ".
					"	  and sigesp_cmp.comprobante=spg_dtmp_cmp.comprobante        ".
					"	  and spg_cuentas.codemp=spg_dtmp_cmp.codemp                 ".
					"	  and spg_cuentas.codestpro1=spg_dtmp_cmp.codestpro1         ".
					"	  and spg_cuentas.estcla=spg_dtmp_cmp.estcla                 ".
					"	  and spg_cuentas.codestpro2=spg_dtmp_cmp.codestpro2         ".
					"	  and spg_cuentas.codestpro3=spg_dtmp_cmp.codestpro3         ".
					"	  and spg_cuentas.codestpro4=spg_dtmp_cmp.codestpro4         ".
					"	  and spg_cuentas.codestpro5=spg_dtmp_cmp.codestpro5         ".
					"	  and spg_cuentas.spg_cuenta=spg_dtmp_cmp.spg_cuenta         ".
					"	  and sigesp_cmp_md.codemp=sigesp_cmp.codemp                 ".
					"	  and sigesp_cmp_md.procede=sigesp_cmp_md.procede            ".
					"	  and sigesp_cmp_md.comprobante=sigesp_cmp.comprobante       ".
					"	  and sigesp_cmp_md.codfuefin<>'01'                          ".
					"	  and spg_cuentas.status='C'                                 ".
					"	  and sigesp_cmp.procede='SPGCRA'                            ".
					$criterio.$criterio2. $criterio3.$criterio4.
					"  group by spg_dtmp_cmp.operacion,                              ".
					"           spg_dtmp_cmp.spg_cuenta,spg_cuentas.denominacion,spg_dtmp_cmp.procede,sigesp_cmp_md.codfuefin,".
					"           spg_cuentas.asignado, ".
					"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				    "         spg_cuentas.codestpro4, spg_cuentas.codestpro5 , spg_cuentas.estcla        ";

	         $ls_sql=$ls_sql. "  UNION  ".
					"  select  0.00 as monto1, '' as operacion1, '' as cuenta1, '' as denno1, '' as procede1,'' as codfuefin1,".
					"          spg_cuentas.asignado as asignado, ".
					"		   sum(spg_dtmp_cmp.monto) as monto2, spg_dtmp_cmp.operacion as operacion2,  ".
					"          spg_dtmp_cmp.spg_cuenta as cuenta2,spg_cuentas.denominacion as denno2,    ".
					"		   spg_dtmp_cmp.procede as procede2,sigesp_cmp_md.codfuefin as codfuefin2,    ".
					"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				    "         spg_cuentas.codestpro4, spg_cuentas.codestpro5 , spg_cuentas.estcla        ".
					"    from  sigesp_cmp, spg_dtmp_cmp, spg_cuentas, sigesp_cmp_md       ".
					"   where  sigesp_cmp.codemp=spg_dtmp_cmp.codemp                      ".
					"	  and sigesp_cmp.comprobante=spg_dtmp_cmp.comprobante             ".
					"	  and sigesp_cmp.procede=spg_dtmp_cmp.procede                     ".
					"	  and spg_cuentas.codemp=spg_dtmp_cmp.codemp                      ".
					"	  and spg_cuentas.codestpro1=spg_dtmp_cmp.codestpro1              ".
					"	  and spg_cuentas.estcla=spg_dtmp_cmp.estcla                      ".
					"	  and spg_cuentas.codestpro2=spg_dtmp_cmp.codestpro2              ".
					"	  and spg_cuentas.codestpro3=spg_dtmp_cmp.codestpro3              ".
					"	  and spg_cuentas.codestpro4=spg_dtmp_cmp.codestpro4              ".
					"	  and spg_cuentas.codestpro5=spg_dtmp_cmp.codestpro5              ".
					"	  and spg_cuentas.spg_cuenta=spg_dtmp_cmp.spg_cuenta              ".
					"	  and spg_cuentas.status='C'                                      ".
					"	  and sigesp_cmp.procede='SPGTRA'                                 ".
					"	  and sigesp_cmp_md.codemp=sigesp_cmp.codemp                      ".
					"	  and sigesp_cmp_md.procede=sigesp_cmp.procede                    ".
					"	  and sigesp_cmp_md.comprobante=sigesp_cmp.comprobante            ".
                    "     and sigesp_cmp_md.codfuefin<>'01'                               ".
					 $criterio.$criterio3.$criterio4.
                    "   group by spg_dtmp_cmp.operacion,                                  ".
					"            spg_dtmp_cmp.spg_cuenta,spg_cuentas.denominacion,        ".
					"            spg_dtmp_cmp.procede,sigesp_cmp_md.codfuefin,			  ".
					"         spg_cuentas.asignado, ".
					"         spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, ".
				    "         spg_cuentas.codestpro4, spg_cuentas.codestpro5 , spg_cuentas.estcla        ".
					"  UNION ".
					" select 0.00 as monto1, '' as operacion1, spg_cuentas.spg_cuenta as cuenta1, spg_cuentas.denominacion as denno1, ".
					" 	'' as procede1,'' as codfuefin1, spg_cuentas.asignado as asignado, 0.00 as monto2, '' as operacion2,  spg_cuentas.spg_cuenta as cuenta2, ".
					"	spg_cuentas.denominacion as denno2, '' as procede2, '' as codfuefin2, ".
					"	spg_cuentas.codestpro1, spg_cuentas.codestpro2, spg_cuentas.codestpro3, spg_cuentas.codestpro4, spg_cuentas.codestpro5 , ".
					"	spg_cuentas.estcla ".
					" from spg_cuentas, spg_ep5 ".
					" where spg_cuentas.codemp = spg_ep5.codemp ".
					"	and spg_cuentas.codestpro1 = spg_ep5.codestpro1 ".
					"	and spg_cuentas.codestpro2 = spg_ep5.codestpro2 ".
					"	and spg_cuentas.codestpro3 = spg_ep5.codestpro3 ".
					"	and spg_cuentas.codestpro4 = spg_ep5.codestpro4 ".
					"	and spg_cuentas.codestpro5 = spg_ep5.codestpro5 ".
					"	and spg_cuentas.estcla     = spg_ep5.estcla     ".
					"	and spg_cuentas.status = 'C' ".$criterio5.
					"   order by 3,7,9,12                                                 ";
		 //print $ls_sql."<br><br>";
		 $rs_data=$this->SQL->select($ls_sql);
		 if($rs_data===false)
		 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_modificacion_por_fuente_finan ".
			                    $this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		 }
		 else
		 {
		    $filas=$this->SQL->num_rows($rs_data);
			if ($filas>0)
			{
				$ls_tasa = $this->uf_buscar_monedas($as_codmon,$as_fechades,$as_fechahas);
				while($row=$this->SQL->fetch_row($rs_data))
				{
					 $ls_monto1		= $row["monto1"];
					 if($ls_tasa>0)
					 	$ls_monto1		= $ls_monto1/$ls_tasa;
					 $ls_operacion1 = $row ["operacion1"];
					 $ls_cuenta1	= $row["cuenta1"];
					 $ls_denno1		= $row["denno1"];
					 $ls_procede1	= $row["procede1"];
					 $ls_codfuefin1 = $row["codfuefin1"];
					 $ls_monto2		= $row["monto2"];
					 if($ls_tasa>0)
					 	$ls_monto2		= $ls_monto2/$ls_tasa;
					 $ls_operacion2 = $row ["operacion2"];
					 $ls_cuenta2	= $row["cuenta2"];
					 $ls_denno2		= $row["denno2"];
					 $ls_procede2	= $row["procede2"];
					 $ls_codfuefin2 = $row["codfuefin2"];
					 $ls_asignado	= $row["asignado"];
					 if($ls_tasa>0)
					 	$ls_asignado		= $ls_asignado/$ls_tasa;

					 $ls_codestpro1=$row["codestpro1"];
					 $ls_codestpro2=$row["codestpro2"];
					 $ls_codestpro3=$row["codestpro3"];
					 $ls_codestpro4=$row["codestpro4"];
					 $ls_codestpro5=$row["codestpro5"];
					 $ls_estcla=$row["estcla"];

					 $this->data_mod->insertRow("codestpro1",$ls_codestpro1);
					 $this->data_mod->insertRow("codestpro2",$ls_codestpro2);
					 $this->data_mod->insertRow("codestpro3",$ls_codestpro3);
					 $this->data_mod->insertRow("codestpro4",$ls_codestpro4);
					 $this->data_mod->insertRow("codestpro5",$ls_codestpro5);
					 $this->data_mod->insertRow("estcla",$ls_estcla);


					if (($ls_codfuefin1=='01')&&($ls_procede1=='SPGTRA'))
					{
						if (trim($ls_operacion1)=="DI")
						{
							$this->data_mod->insertRow("cedente1", $ls_monto1);
						}
						else
						{
							$this->data_mod->insertRow("cedente1", '0.00');
						}
						if (trim($ls_operacion1)=="AU")
						{
							$this->data_mod->insertRow("traspaso1", $ls_monto1);
						}
						else
						{
							$this->data_mod->insertRow("traspaso1", '0.00');
						}
						$this->data_mod->insertRow("cuenta", $ls_cuenta1);
						$this->data_mod->insertRow("denominacion", $ls_denno1);
						$this->data_mod->insertRow("denominacion2", $ls_denno1);
						$this->data_mod->insertRow("cedente2", '0.00');
						$this->data_mod->insertRow("traspaso2", '0.00');
						$this->data_mod->insertRow("incremento1", '0.00');
						$this->data_mod->insertRow("incremento2", '0.00');
						$this->data_mod->insertRow("asignado",  $ls_asignado);
					}

					if (($ls_codfuefin2=='01')&&($ls_procede2=='SPGTRA'))
					{
						if (trim($ls_operacion2)=="AU")
						{
							$this->data_mod->insertRow("incremento1", $ls_monto2);
						}
						else
						{
							$this->data_mod->insertRow("incremento1", '0.00');
						}
						$this->data_mod->insertRow("cuenta", $ls_cuenta2);
						$this->data_mod->insertRow("denominacion", $ls_denno2);
						$this->data_mod->insertRow("denominacion2", $ls_denno2);
						$this->data_mod->insertRow("incremento2", '0.00');
						$this->data_mod->insertRow("cedente1", '0.00');
						$this->data_mod->insertRow("traspaso1", '0.00');
						$this->data_mod->insertRow("cedente2", '0.00');
						$this->data_mod->insertRow("traspaso2", '0.00');
						$this->data_mod->insertRow("asignado",  $ls_asignado);
					}

					if (($ls_codfuefin1!='01')&&($ls_procede1=='SPGCRA')&&($ls_codfuefin1!=''))
					{
						if (trim($ls_operacion1)=="AU")
						{
							$this->data_mod->insertRow("incremento2", $ls_monto1);
						}
						else
						{
							$this->data_mod->insertRow("incremento2", '0.00');
						}

						$this->data_mod->insertRow("cuenta", $ls_cuenta1);
						$this->data_mod->insertRow("denominacion", $ls_denno1);
						$this->data_mod->insertRow("denominacion2", $ls_denno1);
						$this->data_mod->insertRow("incremento1", '0.00');
						$this->data_mod->insertRow("cedente1", '0.00');
						$this->data_mod->insertRow("traspaso1", '0.00');
						$this->data_mod->insertRow("cedente2", '0.00');
						$this->data_mod->insertRow("traspaso2", '0.00');
						$this->data_mod->insertRow("asignado",  $ls_asignado);
					}

					if (($ls_codfuefin2!='01')&&($ls_procede2=='SPGTRA')&&($ls_codfuefin2!=''))
					{
						if (trim($ls_operacion2)=="DI")
						{
							$this->data_mod->insertRow("cedente2", $ls_monto2);
						}
						else
						{
							$this->data_mod->insertRow("cedente2", '0.00');
						}
						if (trim($ls_operacion2)=="AU")
						{
							$this->data_mod->insertRow("traspaso2", $ls_monto2);
						}
						else
						{
							$this->data_mod->insertRow("traspaso2", '0.00');
						}
						$this->data_mod->insertRow("cuenta", $ls_cuenta2);
						$this->data_mod->insertRow("denominacion", $ls_denno2);
						$this->data_mod->insertRow("denominacion2", $ls_denno2);
						$this->data_mod->insertRow("cedente1", '0.00');
						$this->data_mod->insertRow("traspaso1", '0.00');
						$this->data_mod->insertRow("incremento1", '0.00');
						$this->data_mod->insertRow("incremento2", '0.00');
						$this->data_mod->insertRow("asignado",  $ls_asignado);

					}

					if (($ls_codfuefin1=='')&&($ls_codfuefin2=='')&&($ls_procede1=='')&&($ls_procede2=='')&&($ls_monto1==0.00)&&($ls_monto2==0.00))
					{
						$this->data_mod->insertRow("cuenta", $ls_cuenta2);
						$this->data_mod->insertRow("denominacion", $ls_denno2);
						$this->data_mod->insertRow("denominacion2", $ls_denno2);
						$this->data_mod->insertRow("cedente1", '0.00');
						$this->data_mod->insertRow("cedente2", '0.00');
						$this->data_mod->insertRow("traspaso1", '0.00');
						$this->data_mod->insertRow("traspaso2", '0.00');
						$this->data_mod->insertRow("incremento1", '0.00');
						$this->data_mod->insertRow("incremento2", '0.00');
						$this->data_mod->insertRow("asignado",  $ls_asignado);

					}
					//print_r($this->data_mod);

				}
				$this->SQL->free_result($rs_data);
			}
			else
			{
				$lb_valido = false;
			}
		 }//else
	return $lb_valido;
	}// fin de uf_modificacion_por_fuente_finan
//-------------------------------------------------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------------------------------------------------
    function select_estructuras ($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                             $as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,$as_codestpro5h,                                 $as_estclades,$as_estclahas)
	{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	select_estructuras
	 //         Access :	private
	 //     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	 //                     as_spg_cuenta   // cuenta presupestaria
     //	       Returns :
	 //	   Description :
	 //     Creado por :    Ing. Jennifer Rivero
	 // Fecha Creaci?n :    13/11/08          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	     $lb_valido=true;
	     $criterio="";
		 $ls_codemp = $this->dts_empresa["codemp"];
		 if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		 {
			$criterio=" AND (CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla)
							BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		 }
		 if ($_SESSION["ls_gestor"]=="POSTGRES")
		 {
			$criterio=" AND (codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla  BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
					    AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		 }

		$ls_sql="  SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla ".
		        "   FROM  spg_ep5 ".
				"   WHERE codemp='".$ls_codemp."'".$criterio;

		 $rs_data=$this->SQL->select($ls_sql);//echo $ls_sql.'<br>';
		 if($rs_data===false)
		 {   // error interno sql
				$this->is_msg_error="Error en consulta metodo select_estructuras ".
			                   		 $this->fun->uf_convertirmsg($this->SQL->message);
				$lb_valido = false;
		 }
		 else
		 {
		 	while($row=$this->SQL->fetch_row($rs_data))
			{
			     $ls_codestpro1=$row["codestpro1"];
				 $ls_codestpro2=$row["codestpro2"];
				 $ls_codestpro3=$row["codestpro3"];
				 $ls_codestpro4=$row["codestpro4"];
				 $ls_codestpro5=$row["codestpro5"];
				 $ls_estcla=$row["estcla"];
				 $this->data_est->insertRow("codestpro1",  $ls_codestpro1);
				 $this->data_est->insertRow("codestpro2",  $ls_codestpro2);
				 $this->data_est->insertRow("codestpro3",  $ls_codestpro3);
				 $this->data_est->insertRow("codestpro4",  $ls_codestpro4);
				 $this->data_est->insertRow("codestpro5",  $ls_codestpro5);
				 $this->data_est->insertRow("estcla",  $ls_estcla);

			}//fin del while
			$this->SQL->free_result($rs_data);
		 }//fin del else
		 return $lb_valido;
	}//fin de la funcion
//-------------------------------------------------------------------------------------------------------------------------------------

function uf_buscar_tasas ($as_codmon)
	 {
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_buscar_tasa
	 //         Access :	private
	 //     Argumentos :
     //	       Returns :	Retorna las tasas 1 y la tasa 2
	 //	   Description :	Retorna las Tasas de las Monedas en caso de que en el periodo seleccionado no haya una definicion de moneda
	 //     Creado por :    Ing. Arnaldo Suarez
	 // Fecha Creaci?n :    09/01/09          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 	$lb_valido = true;
		$ls_tasa=0;
		$ls_criterio="";
		if ($as_codmon!="")
		{
			$ls_criterio="	   AND sigesp_dt_moneda.codmon='".$as_codmon."'            ";
		}
		else
		{
			$ls_criterio="     AND sigesp_moneda.estmonpri='1' ";
		}
		$ls_sql=" select MAX(sigesp_dt_moneda.fecha),sigesp_dt_moneda.tascam1, sigesp_dt_moneda.tascam2 ".
				"	  from  sigesp_dt_moneda, sigesp_moneda               ".
				"	 where sigesp_dt_moneda.codmon= sigesp_moneda.codmon  ".$ls_criterio.
				"	 group by sigesp_dt_moneda.codmon, sigesp_dt_moneda.fecha, ".
				"          sigesp_dt_moneda.tascam1, sigesp_dt_moneda.tascam2  ".
				"	 order by sigesp_dt_moneda.codmon		                          ";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{
		   $this->io_msg->message("Error en uf_buscar_tasas  ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_tasa=$row["tascam1"];
			}
			$this->SQL->free_result($rs_data);
		}// fin del else
		return $ls_tasa;
	 }// fin uf_buscar_tasas

	 function uf_spg_reporte_ejecucion_financiera_mensual($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                          $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											  $as_codestpro4_des,$as_codestpro5_des,$adt_fecini,$adt_fecfin,$ai_nivel,$as_cuentades,$as_cuentahas,$as_codfuefindes,
											  $as_codfuefinhas,$as_estclades,$as_estclahas,$rs_cuentas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_ejecucion_financiera_mensual
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
	 //                     ai_nivel    // nivel de la cuenta
	 //                     ai_MenorNivel  // variabvle que determina el menor nivel de la cuenta
	 //                     as_codfuefindes  // codigo fuente financiamiento desde solicitado por Gobernacion de Apure
	 //                     as_codfuefinhas  // codigo fuente financiamiento hasta solicitado por Gobernacion de Apure
	 //                     as_estclades   // estatus desde de clasificaicones de la estructura presupuestaria IPSFA
	 //                     as_estclahas   // estatus hasta de clasificaicones de la estructura presupuestaria IPSFA
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte de Ejecucion Financiera Mensual de gasto
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    16/03/09          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$lb_existe = false;
		$lb_valido = true;
		$lb_ok = true;
        $ld_total=0;
		$asignado_total=0;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_seguridad="";
	    $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('PCT',$ls_seguridad);
	    $this->dts_reporte->resetds("spg_cuenta");
        $ls_str_sql_where="";
		$dts_cuentas=new class_datastore();
        $arrResultado=$this->uf_obtener_rango_programatica($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,                                              $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
                                             $as_codestpro4_des,$as_codestpro5_des,$ls_Sql_Where,$ls_str_estructura_from,
											 $ls_str_estructura_to,$as_estclades,$as_estclahas);
		$ls_Sql_Where=$arrResultado['as_Sql_Where'];
		$ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
		$ls_str_estructura_to=$arrResultado['as_str_estructura_to'];

		$ls_str_sql_where="WHERE PCT.codemp='".$ls_codemp."'";
		$ls_Sql_Where = trim($ls_Sql_Where);

        if (!empty($ls_Sql_Where) )
        {
           $ls_str_sql_where="WHERE PCT.codemp='".$ls_codemp."' AND ".$ls_Sql_Where;
        }
		
        $ls_sql=" SELECT DISTINCT PCT.spg_cuenta, PCT.nivel, MIN(PCT.denominacion) AS denominacion,  ".
                  "                 sum(PCT.asignado) as asignado ".
                  " FROM spg_cuentas PCT  ".$ls_str_sql_where." AND ".
                  "      PCT.spg_cuenta BETWEEN '".trim($as_cuentades)."' AND '".trim($as_cuentahas)."' AND ".
				  "      (PCT.nivel<='".$ai_nivel."')  ".$ls_seguridad." ".
				  " GROUP BY PCT.spg_cuenta, PCT.nivel ".
                  " ORDER BY PCT.spg_cuenta ";
       
		$rs_cuentas=$this->SQL->select($ls_sql);
		if($rs_cuentas===false)
		{   //error interno sql
		   $this->io_msg->message("Error en Reporte Ejecucion Financiera Mensual  ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}
	 $arrResultado['rs_data']=$rs_cuentas;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
   } // fin function uf_spg_reporte_ejecucion_financiera_mensual

   function uf_spg_reporte_detalle_ejecucion_financiera_mensual($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
															  $as_codestpro5,$as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,
															  $as_codestpro5h,$as_estclades,$as_estclahas,
															  $as_spg_cuenta,$adt_fecini,$adt_fecfin,$as_codfuefindes,$as_codfuefinhas,$rs_data2)

	{

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_detalle_ejecucion_financiera_mensual
	 //         Access :	private
	 //     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	 //                     as_spg_cuenta   // cuenta presupestaria
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte de la Ejecucion Financiara de Gasto
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    16/03/09          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$lb_valido = true;
		if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		{
			$criterio=" AND (CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)
                       	BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}
		if ($_SESSION["ls_gestor"]=="POSTGRES")
		{
			$criterio=" AND (spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla
                       	BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}

	    $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";

        $ls_sql=	" SELECT spg_dt_cmp.spg_cuenta,  ".
                  	" CASE spg_dt_cmp.operacion  ".
					" WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
					" END as asignar, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
					" END as aumento, ".
					" CASE spg_dt_cmp.operacion  ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
					" END as compromiso,".
					" CASE spg_dt_cmp.operacion".
					" WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
					" END as disminucion, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
					" END as causado, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
					" END as precompromiso, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
					" END as pagado ".
                  	" FROM spg_dt_cmp  ".
					" WHERE spg_dt_cmp.spg_cuenta LIKE '$as_spg_cuenta'".
					" AND spg_dt_cmp.fecha BETWEEN '$adt_fecini' AND '$adt_fecfin' ".$criterio.
        			" AND spg_dt_cmp.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ".
					" GROUP BY spg_dt_cmp.spg_cuenta, spg_dt_cmp.operacion".
                  	" ORDER BY spg_dt_cmp.spg_cuenta ";
		$rs_data2=$this->SQL->select($ls_sql); 
		if($rs_data2===false)
		{   //error interno sql
		   $this->io_msg->message("Error en Reporte Ejecuci?n Financiera Mensual ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}

	 $arrResultado['rs_data']=$rs_data2;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
   } // fin uf_spg_reporte_detalle_ejecucion_financiera_mensual
   
   function uf_spg_reporte_detalle_ejecucion_financiera_mensual_acumulado($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
	                                              $as_codestpro5,$as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,
	                                              $as_codestpro5h,$as_estclades,$as_estclahas,
												  $as_spg_cuenta,$adt_fecini,$adt_fecfin,$as_codfuefindes,$as_codfuefinhas,$rs_data2)

{

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_detalle_ejecucion_financiera_mensual
	 //         Access :	private
	 //     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	 //                     as_spg_cuenta   // cuenta presupestaria
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte de la Ejecucion Financiara de Gasto
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    16/03/09          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$lb_valido = true;
		if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		{
			$criterio=" AND (CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)
                       	BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}
		if ($_SESSION["ls_gestor"]=="POSTGRES")
		{
			$criterio=" AND (spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla
                       	BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades'
						AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		}

	    $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";

        $ls_sql=	" SELECT spg_dt_cmp.spg_cuenta,  ".
                  	" CASE spg_dt_cmp.operacion  ".
					" WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
					" END as asignar, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
					" END as aumento, ".
					" CASE spg_dt_cmp.operacion  ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
					" END as compromiso,".
					" CASE spg_dt_cmp.operacion".
					" WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
					" END as disminucion, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
					" END as causado, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
					" END as precompromiso, ".
					" CASE spg_dt_cmp.operacion ".
					" WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
					" WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
					" END as pagado ".
                  	" FROM spg_dt_cmp  ".
					" WHERE spg_dt_cmp.spg_cuenta LIKE '$as_spg_cuenta'".
					" AND spg_dt_cmp.fecha<'$adt_fecini' ".$criterio.
					" AND spg_dt_cmp.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ".        
					" GROUP BY spg_dt_cmp.spg_cuenta, spg_dt_cmp.operacion".
                  	" ORDER BY spg_dt_cmp.spg_cuenta ";
        //echo $ls_sql;
		$rs_data2=$this->SQL->select($ls_sql); 
		if($rs_data2===false)
		{   //error interno sql
		   $this->io_msg->message("Error en Reporte Ejecuci?n Financiera Mensual ".$this->fun->uf_convertirmsg($this->SQL->message));
           $lb_valido = false;
		}

	 $arrResultado['rs_data']=$rs_data2;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
   } // fin uf_spg_reporte_detalle_ejecucion_financiera_mensual
/*************************************************************************************************************************************************
                                                              ACUMULADO POR CUENTA DETALLADO
 *************************************************************************************************************************************************/

 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 //	      Function :	obtener_estructuras_presupuestarias
 //         Access :	private
 //     Argumentos :    as_codestpro1  ... $as_codestpro5  // rango nivel estructura presupuestaria origen
 //                     as_codestpro1h ... $as_codestpro5h // rango nivel estructura presupuestaria destino
 //                     as_estclades                       // Estatus de la Estructura Presupuesatria de Origen
 //                     as_estclahas                       // Estatus de la Estructura Presupuesatria de Destino
 //                     rs_estructuras                     // Recorset con las estrucuturas
 //	       Returns :	Retorna estructuras ordenadas para la consulta sql
 //	   Description :	Funcion para retornar las estructuras a mostrar en el reporte de Acumulado por Cuenta Detallado
 //     Creado por :    Ing. Arnaldo Su?rez
 // Fecha Creaci?n :    11/04/2010          Fecha ?ltima Modificacion :
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function obtener_estructuras_presupuestarias($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
											  $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											  $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas,$rs_estructuras)
 {
  $ls_seguridad="";
  $ls_sql = "";
  $ls_criterio = "";
  $lb_valido = true;
  
  $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spg_ep5',$ls_seguridad);
  if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
  {
	$ls_criterio=" AND (CONCAT(spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5,spg_ep5.estcla)
				BETWEEN '$as_codestpro1_ori$as_codestpro2_ori$as_codestpro3_ori$as_codestpro4_ori$as_codestpro5_ori$as_estclades'
				AND     '$as_codestpro1_des$as_codestpro2_des$as_codestpro3_des$as_codestpro4_des$as_codestpro5_des$as_estclahas') ";
  }
  elseif ($_SESSION["ls_gestor"]=="POSTGRES")
  {
	$ls_criterio=" AND (spg_ep5.codestpro1||spg_ep5.codestpro2||spg_ep5.codestpro3||spg_ep5.codestpro4||spg_ep5.codestpro5||spg_ep5.estcla
				BETWEEN '$as_codestpro1_ori$as_codestpro2_ori$as_codestpro3_ori$as_codestpro4_ori$as_codestpro5_ori$as_estclades'
				AND     '$as_codestpro1_des$as_codestpro2_des$as_codestpro3_des$as_codestpro4_des$as_codestpro5_des$as_estclahas') ";
  }
  
  $ls_sql = "SELECT spg_ep5.codestpro1, ".
			"	    spg_ep1.denestpro1, ".
			"	    spg_ep5.codestpro2, ".
			"	    spg_ep2.denestpro2, ".
			"	    spg_ep5.codestpro3, ".
			"	    spg_ep3.denestpro3, ".
			"	    spg_ep5.codestpro4, ".
			"	    spg_ep4.denestpro4, ".
			"	    spg_ep5.codestpro5, ".
			"	    spg_ep5.denestpro5, ".
			"	    spg_ep5.estcla ".
			"FROM spg_ep5 ".
			"JOIN spg_ep4 ON spg_ep4.codemp = spg_ep5.codemp AND ".
			"				 spg_ep4.codestpro1 = spg_ep5.codestpro1 AND ".
			"			     spg_ep4.codestpro2 = spg_ep5.codestpro2 AND ".
			"			     spg_ep4.codestpro3 = spg_ep5.codestpro3 AND ".
			"			     spg_ep4.codestpro4 = spg_ep5.codestpro4 AND ".
			"			     spg_ep4.estcla     = spg_ep5.estcla ".
			"JOIN spg_ep3 ON spg_ep3.codemp = spg_ep5.codemp AND ".
			"				 spg_ep3.codestpro1 = spg_ep5.codestpro1 AND ".
			"			     spg_ep3.codestpro2 = spg_ep5.codestpro2 AND ".
			"			     spg_ep3.codestpro3 = spg_ep5.codestpro3 AND ".
			"			     spg_ep3.estcla     = spg_ep5.estcla ".
			"JOIN spg_ep2 ON spg_ep2.codemp = spg_ep5.codemp AND ".
			"				 spg_ep2.codestpro1 = spg_ep5.codestpro1 AND ".
			"			     spg_ep2.codestpro2 = spg_ep5.codestpro2 AND ".
			"			     spg_ep2.estcla     = spg_ep5.estcla ".
			"JOIN spg_ep1 ON spg_ep1.codemp = spg_ep5.codemp AND ".
			"				 spg_ep1.codestpro1 = spg_ep5.codestpro1 AND ".
			"			     spg_ep1.estcla     = spg_ep5.estcla ".
			" WHERE spg_ep5.codemp = '".$this->dts_empresa["codemp"]."' ".$ls_criterio." ".$ls_seguridad.
			" ORDER BY spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5";
			
  $rs_estructuras=$this->SQL->select($ls_sql); 
  if($rs_estructuras===false)
  {   //error interno sql
	   $this->io_msg->message("Error en Reporte Acumulado Por Cuentas  ".$this->fun->uf_convertirmsg($this->SQL->message));
	   $lb_valido = false;
  }
	 $arrResultado['rs_estructuras']=$rs_estructuras;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
 }

 
 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 //	      Function :	obtener_cuentas_presupuestarias
 //         Access :	private
 //     Argumentos :    as_codestpro1  ... $as_codestpro5  // rango nivel estructura presupuestaria origen
 //                     as_codestpro1h ... $as_codestpro5h // rango nivel estructura presupuestaria destino
 //                     as_estclades                       // Estatus de la Estructura Presupuesatria de Origen
 //                     as_estclahas                       // Estatus de la Estructura Presupuesatria de Destino
 //                     rs_cuentas                         // Recorset con las cuentas
 //	       Returns :	Retorna estructuras ordenadas para la consulta sql
 //	   Description :	Funcion para retornar las cuentas a mostrar en el reporte de Acumulado por Cuenta Detallado
 //     Creado por :    Ing. Arnaldo Su?rez
 // Fecha Creaci?n :    11/04/2010          Fecha ?ltima Modificacion :
 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function obtener_cuentas_presupuestarias($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
										  $as_codestpro5,$ai_nivel,$as_cuentades,$as_cuentahas,$as_codfuefindes,
										  $as_codfuefinhas,$as_estcla,$rs_cuentas)
 {
  $ls_seguridad="";
  $ls_sql = "";
  $ls_criterio = "";
  $lb_valido = true;
  $ls_codfuefin = "";
  if ($_SESSION["la_empresa"]["estmodest"]==1) {
  	$ls_sql = "SELECT spg_cuentas.codestpro1,spg_cuentas.codestpro2, ".
			"	    spg_cuentas.codestpro3,spg_cuentas.codestpro4, ".
			"	    spg_cuentas.codestpro5,spg_cuentas.estcla, ".
			"	    spg_cuentas.spg_cuenta,spg_cuentas.denominacion, ".
			"	    spg_cuentas.asignado,spg_cuentas.status, spg_cuentas.nivel ".
			"FROM spg_cuentas ".
			" JOIN spg_ep3 ON spg_ep3.codemp = spg_cuentas.codemp AND ".
			"		spg_ep3.codestpro1 = spg_cuentas.codestpro1 AND ".
			"		spg_ep3.codestpro2 = spg_cuentas.codestpro2 AND ".
			"		spg_ep3.codestpro3 = spg_cuentas.codestpro3 AND ".
			"		spg_ep3.estcla     = spg_cuentas.estcla ".
			" WHERE spg_cuentas.codemp = '".$this->dts_empresa["codemp"]."' ".
			" AND spg_ep3.codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' AND ".
			"      spg_cuentas.spg_cuenta BETWEEN '".trim($as_cuentades)."' AND '".trim($as_cuentahas)."' AND  ".
			"      spg_cuentas.codestpro1 = '".$as_codestpro1."' AND ".
			"      spg_cuentas.codestpro2 = '".$as_codestpro2."' AND ".
			"      spg_cuentas.codestpro3 = '".$as_codestpro3."' AND ".
			"      spg_cuentas.codestpro4 = '".$as_codestpro4."' AND ".
			"      spg_cuentas.codestpro5 = '".$as_codestpro5."' AND ".
			"      spg_cuentas.estcla = '".$as_estcla."' AND ".														
			"      spg_cuentas.nivel <= ".$ai_nivel." ".$ls_seguridad.
			" ORDER BY spg_cuentas.codestpro1,spg_cuentas.codestpro2, ".
			"	       spg_cuentas.codestpro3,spg_cuentas.codestpro4, ".
			"	       spg_cuentas.codestpro5,spg_cuentas.spg_cuenta ";
  }
  else{
  	$ls_sql = "SELECT spg_cuentas.codestpro1,spg_cuentas.codestpro2, ".
			"	    spg_cuentas.codestpro3,spg_cuentas.codestpro4, ".
			"	    spg_cuentas.codestpro5,spg_cuentas.estcla, ".
			"	    spg_cuentas.spg_cuenta,spg_cuentas.denominacion, ".
			"	    spg_cuentas.asignado,spg_cuentas.status, spg_cuentas.nivel ".
			"FROM spg_cuentas ".
			" JOIN spg_ep5 ON spg_ep5.codemp = spg_cuentas.codemp AND ".
			"		spg_ep5.codestpro1 = spg_cuentas.codestpro1 AND ".
			"		spg_ep5.codestpro2 = spg_cuentas.codestpro2 AND ".
			"		spg_ep5.codestpro3 = spg_cuentas.codestpro3 AND ".
			"		spg_ep5.codestpro4 = spg_cuentas.codestpro4 AND ".
			"		spg_ep5.codestpro5 = spg_cuentas.codestpro5 AND ".
			"		spg_ep5.estcla     = spg_cuentas.estcla ".
			" WHERE spg_cuentas.codemp = '".$this->dts_empresa["codemp"]."' ".
			" AND spg_ep5.codfuefin BETWEEN '".trim($as_codfuefindes)."' AND '".trim($as_codfuefinhas)."' AND ".
			"      spg_cuentas.spg_cuenta BETWEEN '".trim($as_cuentades)."' AND '".trim($as_cuentahas)."' AND  ".
			"      spg_cuentas.codestpro1 = '".$as_codestpro1."' AND ".
			"      spg_cuentas.codestpro2 = '".$as_codestpro2."' AND ".
			"      spg_cuentas.codestpro3 = '".$as_codestpro3."' AND ".
			"      spg_cuentas.codestpro4 = '".$as_codestpro4."' AND ".
			"      spg_cuentas.codestpro5 = '".$as_codestpro5."' AND ".
			"      spg_cuentas.estcla = '".$as_estcla."' AND ".														
			"      spg_cuentas.nivel <= ".$ai_nivel." ".$ls_seguridad.
			" ORDER BY spg_cuentas.codestpro1,spg_cuentas.codestpro2, ".
			"	       spg_cuentas.codestpro3,spg_cuentas.codestpro4, ".
			"	       spg_cuentas.codestpro5,spg_cuentas.spg_cuenta ";
  }
  //echo $ls_sql;
  $rs_cuentas=$this->SQL->select($ls_sql); 
  if($rs_cuentas===false)
  {   //error interno sql
	   $this->io_msg->message("Error en Reporte Acumulado Por Cuentas  ".$this->fun->uf_convertirmsg($this->SQL->message));
	   $lb_valido = false;
  }
  
	 $arrResultado['rs_cuentas']=$rs_cuentas;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
 }
 
 function uf_spg_ejecutado_cuenta($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
								  $as_codestpro5,$as_estcla,$as_spg_cuenta,$adt_fecdes,$adt_fechas,$ad_precomprometer,
								  $ad_comprometer,$ad_causado,$ad_pagado,$ad_aumento,$ad_disminucion)
{///////////////////////////////////////////////////////////////////////////////////////////////////////
 //	      Function :	uf_spg_ejecutado_cuenta
 //         Access :	private
 //     Argumentos :    $as_spg_cuenta  -----> cuenta 
 //                     $adt_fecdes  -----> fechas desde 
 //                     $adt_fechas  -----> fechas hasta  
 //                     $ad_precomprometer  -----> monto precomprometer referencia   
 //                     $ad_comprometer  -----> monto comprometer referencia   
 //                     $ad_causado  -----> monto causado referencia   
 //                     $ad_pagado  -----> monto pagado referencia   
 //                     $ad_aumento  -----> monto aumento referencia   
 //                     $ad_disminucion  -----> monto disminucion referencia   
 //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
 //	   Description :	Reporte que genera los montos ejecutados para un cuenta y estructura
 //     Creado por :    Ing. Arnaldo Su?rez
 // Fecha Creaci?n :    11/04/2010          Fecha ?ltima Modificacion :      Hora :
 ////////////////////////////////////////////////////////////////////////////////////////////////////////
 $lb_valido = true;	 
 $ad_precomprometer=0;
 $ad_comprometer=0;  
 $ad_causado=0;  
 $ad_pagado=0;
 $ad_aumento=0;  
 $ad_disminucion=0;
 $as_spg_cuenta = $this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
 
 // PRECOMPROMISO
 
 $ls_sql_precompromiso = "SELECT COALESCE(SUM(DT.monto),0.00) as precompromiso ".
					  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
					  "	WHERE  DT.codemp='".$this->dts_empresa["codemp"]."' AND ".
					  "		   DT.operacion = OP.operacion AND ".
					  "		   OP.precomprometer = 1 AND  ".
					  "        DT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					  "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' AND ".
					  "        DT.codestpro1 = '".$as_codestpro1."' AND ".
					  "        DT.codestpro2 = '".$as_codestpro2."' AND ".
					  "        DT.codestpro3 = '".$as_codestpro3."' AND ".
					  "        DT.codestpro4 = '".$as_codestpro4."' AND ".
					  "        DT.codestpro5 = '".$as_codestpro5."' AND ".
					  "        DT.estcla = '".$as_estcla."'";
					  
 $rs_precompromiso=$this->SQL->select($ls_sql_precompromiso);
 if($rs_precompromiso===false)
 { // error interno sql
	$this->io_mensajes->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_spg_ejecutado_cuenta ".
								"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
 }
 else
 {
  if(!$rs_precompromiso->EOF)
  {
   $ad_precomprometer = $rs_precompromiso->fields["precompromiso"];
  }
 }
 
 // COMPROMISO
 
 $ls_sql_compromiso = "SELECT COALESCE(SUM(DT.monto),0.00) as compromiso ".
					  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
					  "	WHERE  DT.codemp='".$this->dts_empresa["codemp"]."' AND ".
					  "		   DT.operacion = OP.operacion AND ".
					  "		   OP.comprometer = 1 AND  ".
					  "        DT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					  "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' AND ".
					  "        DT.codestpro1 = '".$as_codestpro1."' AND ".
					  "        DT.codestpro2 = '".$as_codestpro2."' AND ".
					  "        DT.codestpro3 = '".$as_codestpro3."' AND ".
					  "        DT.codestpro4 = '".$as_codestpro4."' AND ".
					  "        DT.codestpro5 = '".$as_codestpro5."' AND ".
					  "        DT.estcla = '".$as_estcla."'";
 $rs_compromiso=$this->SQL->select($ls_sql_compromiso);
 if($rs_compromiso===false)
 { // error interno sql
	$this->io_mensajes->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_spg_ejecutado_cuenta ".
								"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
 }
 else
 {
  if(!$rs_compromiso->EOF)
  {
   $ad_comprometer = $rs_compromiso->fields["compromiso"];
  }
 }
 
 // CAUSADO
 
 $ls_sql_causado = "SELECT COALESCE(SUM(DT.monto),0.00) as causado ".
				   " FROM   spg_dt_cmp DT, spg_operaciones OP ".
				   "	WHERE  DT.codemp='".$this->dts_empresa["codemp"]."' AND ".
				   "		   DT.operacion = OP.operacion AND ".
				   "		   OP.causar = 1 AND  ".
				   "        DT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
				   "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' AND ".
				   "        DT.codestpro1 = '".$as_codestpro1."' AND ".
				   "        DT.codestpro2 = '".$as_codestpro2."' AND ".
				   "        DT.codestpro3 = '".$as_codestpro3."' AND ".
				   "        DT.codestpro4 = '".$as_codestpro4."' AND ".
				   "        DT.codestpro5 = '".$as_codestpro5."' AND ".
				   "        DT.estcla = '".$as_estcla."'";
 $rs_causado=$this->SQL->select($ls_sql_causado);
 if($rs_causado===false)
 { // error interno sql
	$this->io_mensajes->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_spg_ejecutado_cuenta ".
								"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
 }
 else
 {
  if(!$rs_causado->EOF)
  {
   $ad_causado = $rs_causado->fields["causado"];
  }
 }
 
 // PAGADO
 
 $ls_sql_pagado = "SELECT COALESCE(SUM(DT.monto),0.00) as pagado ".
				  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
				  "	WHERE  DT.codemp='".$this->dts_empresa["codemp"]."' AND ".
				  "		   DT.operacion = OP.operacion AND ".
				  "		   OP.pagar = 1 AND  ".
				  "        DT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
				  "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' AND ".
				  "        DT.codestpro1 = '".$as_codestpro1."' AND ".
				  "        DT.codestpro2 = '".$as_codestpro2."' AND ".
				  "        DT.codestpro3 = '".$as_codestpro3."' AND ".
				  "        DT.codestpro4 = '".$as_codestpro4."' AND ".
				  "        DT.codestpro5 = '".$as_codestpro5."' AND ".
				  "        DT.estcla = '".$as_estcla."'";
 $rs_pagado=$this->SQL->select($ls_sql_pagado);
 if($rs_pagado===false)
 { // error interno sql
	$this->io_mensajes->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_spg_ejecutado_cuenta ".
								"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
 }
 else
 {
  if(!$rs_pagado->EOF)
  {
   $ad_pagado = $rs_pagado->fields["pagado"];
  }
 }
 
  // AUMENTO
 
 $ls_sql_aumento = "SELECT COALESCE(SUM(DT.monto),0.00) as aumento ".
				   " FROM   spg_dt_cmp DT, spg_operaciones OP ".
				   "	WHERE  DT.codemp='".$this->dts_empresa["codemp"]."' AND ".
				   "		   DT.operacion = OP.operacion AND ".
				   "		   OP.aumento = 1 AND  ".
				   "        DT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
				   "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' AND ".
				   "        DT.codestpro1 = '".$as_codestpro1."' AND ".
				   "        DT.codestpro2 = '".$as_codestpro2."' AND ".
				   "        DT.codestpro3 = '".$as_codestpro3."' AND ".
				   "        DT.codestpro4 = '".$as_codestpro4."' AND ".
				   "        DT.codestpro5 = '".$as_codestpro5."' AND ".
				   "        DT.estcla = '".$as_estcla."'";
 $rs_aumento=$this->SQL->select($ls_sql_aumento);
 if($rs_aumento===false)
 { // error interno sql
	$this->io_mensajes->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_spg_ejecutado_cuenta ".
								"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
 }
 else
 {
  if(!$rs_aumento->EOF)
  {
   $ad_aumento = $rs_aumento->fields["aumento"];
  }
 }
 
 // DISMINUCION
 
 $ls_sql_disminucion = "SELECT COALESCE(SUM(DT.monto),0.00) as disminucion ".
					   " FROM   spg_dt_cmp DT, spg_operaciones OP ".
					   "	WHERE  DT.codemp='".$this->dts_empresa["codemp"]."' AND ".
					   "		   DT.operacion = OP.operacion AND ".
					   "		   OP.disminucion = 1 AND  ".
					   "        DT.spg_cuenta LIKE '".$as_spg_cuenta."' AND ".
					   "        DT.fecha BETWEEN '".$adt_fecdes."' AND  '".$adt_fechas."' AND ".
					   "        DT.codestpro1 = '".$as_codestpro1."' AND ".
					   "        DT.codestpro2 = '".$as_codestpro2."' AND ".
					   "        DT.codestpro3 = '".$as_codestpro3."' AND ".
					   "        DT.codestpro4 = '".$as_codestpro4."' AND ".
					   "        DT.codestpro5 = '".$as_codestpro5."' AND ".
					   "        DT.estcla = '".$as_estcla."'";
 $rs_disminucion=$this->SQL->select($ls_sql_disminucion);
 if($rs_disminucion===false)
 { // error interno sql
	$this->io_mensajes->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_spg_ejecutado_cuenta ".
								"ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
 }
 else
 {
  if(!$rs_disminucion->EOF)
  {
   $ad_disminucion = $rs_disminucion->fields["disminucion"];
  }
 }
 
 $this->SQL->free_result($rs_precompromiso);
 $this->SQL->free_result($rs_compromiso);
 $this->SQL->free_result($rs_causado);
 $this->SQL->free_result($rs_pagado);
 $this->SQL->free_result($rs_aumento);
 $this->SQL->free_result($rs_disminucion);

	 $arrResultado['ad_precomprometer']=$ad_precomprometer;
	 $arrResultado['ad_comprometer']=$ad_comprometer;
	 $arrResultado['ad_causado']=$ad_causado;
	 $arrResultado['ad_pagado']=$ad_pagado;
	 $arrResultado['ad_aumento']=$ad_aumento;
	 $arrResultado['ad_disminucion']=$ad_disminucion;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
 }//fin uf_spg_ejecutado_trimestral_estado_resultado
 
 function uf_reporte_listado_cuenta_estructura($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
                                               $as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,$as_codestpro5h,
											   $as_estclades,$as_estclahas,$as_codfuefindes,$as_codfuefinhas,$as_spg_cuenta,$as_spg_cuentah,
											   $as_sc_cuenta,$as_sc_cuentah,$rs_estructuras=NULL)
 {
 	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_reporte_listado_cuenta_estructura
	 //         Access :	private
	 //     Argumentos :    as_codestpro1  ... $as_codestpro5  // rango nivel estructura presupuestaria origen
	 //                     as_codestpro1h ... $as_codestpro5h // rango nivel estructura presupuestaria destino
	 //                     as_estclades                       // Estatus de la Estructura Presupuesatria de Origen
	 //                     as_estclahas                       // Estatus de la Estructura Presupuesatria de Destino
	 //                     as_codfuefindes                    // Codigo de la Fuentes de Financiamiento de Origen
	 //                     as_codfuefinhas                    // Codigo de la Fuente de Financiamiento de Destino
	 //                     as_spg_cuenta                      // Cuenta de Gasto desde
	 //                     as_spg_cuentah                     // Cuenta de Gasto hasta
     //                     as_sc_cuenta                       // Cuenta Contable desde
	 //                     as_sc_cuentah                      // Cuenta Contable hasta
	 //                     rs_estructuras                         // Recorset con las cuentas
	 //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Funcion para retornar las estructuras presupuestarias a mostrar en el reporte de Listado de Cuentas Presupuestarias
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    25/06/2010          Fecha ?ltima Modificacion :
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_sql = "";
	 $lb_valido = true;
	 $ls_seguridad="";
	 $ls_filtro_spg="";
	 $ls_filtro_scg="";
	 $ls_filtro_fuefin="";
	 $ls_filtro_estructura = "";
	 $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spg_cuentas',$ls_seguridad);
	 
	 if((!empty($as_spg_cuenta))&&(!empty($as_spg_cuentah)))
	 {
	  $ls_filtro_spg=" AND spg_cuentas.spg_cuenta BETWEEN '".$as_spg_cuenta."' AND '".$as_spg_cuentah."' ";
	 }
	 
	 if((!empty($as_sc_cuenta))&&(!empty($as_sc_cuentah)))
	 {
	  $ls_filtro_scg=" AND spg_cuentas.sc_cuenta BETWEEN '".$as_sc_cuenta."' AND '".$as_sc_cuentah."' ";
	 }
	 
	 switch($this->dts_empresa["estmodest"])
	 {
	  case 1:
	         $ls_sql = "SELECT DISTINCT ".
					   "  spg_ep3.codestpro1, ".
					   "  spg_ep3.codestpro2, ".
					   "  spg_ep3.codestpro3, ".
					   "  spg_ep3.estcla ".
					   " FROM ".
					   "	  spg_ep3, ".
                       "      spg_cuentas ".
					   "	WHERE ". 
					   "	  spg_cuentas.codemp = '".$this->dts_empresa["codemp"]."' AND ".
					   "      spg_cuentas.codemp = spg_ep3.codemp AND ".
					   "      spg_cuentas.estcla = spg_ep3.estcla AND ".
					   "      spg_cuentas.codestpro1 = spg_ep3.codestpro1 AND ".
					   "      spg_cuentas.codestpro2 = spg_ep3.codestpro2 AND ".
					   "      spg_cuentas.codestpro3 = spg_ep3.codestpro3 ";
					  
					   
			 if((!empty($as_codfuefindes))&&(!empty($as_codfuefinhas)))
			 {
			   $ls_filtro_fuefin= " AND spg_ep3.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
			 }
			 
			 if((!empty($as_codestpro1))&&(!empty($as_estclades))&&(!empty($as_codestpro1h))&&(!empty($as_estclahas)))
			 {
			  switch(strtoupper($_SESSION["ls_gestor"]))
			  {
			   case 'POSTGRES':
					$ls_filtro_estructura = " AND spg_ep3.codestpro1||spg_ep3.codestpro2||spg_ep3.codestpro3||spg_ep3.estcla BETWEEN '".$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_estclades."' AND '".$as_codestpro1h.$as_codestpro2h.$as_codestpro3h.$as_estclades."' ";
			   break;
				 
			   case 'MYSQLT':
					$ls_filtro_estructura = " AND CONCAT(spg_ep3.codestpro1,spg_ep3.codestpro2,spg_ep3.codestpro3,spg_ep3.estcla) BETWEEN '".$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_estclades."' AND '".$as_codestpro1h.$as_codestpro2h.$as_codestpro3h.$as_estclades."' ";
			   break;
				 
			   case 'MYSQLI':
					$ls_filtro_estructura = " AND CONCAT(spg_ep3.codestpro1,spg_ep3.codestpro2,spg_ep3.codestpro3,spg_ep3.estcla) BETWEEN '".$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_estclades."' AND '".$as_codestpro1h.$as_codestpro2h.$as_codestpro3h.$as_estclades."' ";
			   break;
			  }
			 }
	         break;
	  case 2:
	         $ls_sql = "SELECT DISTINCT ".
					   "  spg_ep5.codestpro1, ".
					   "  spg_ep5.codestpro2, ". 
					   "  spg_ep5.codestpro3, ". 
					   "  spg_ep5.codestpro4, ". 
					   "  spg_ep5.codestpro5, ". 
					   "  spg_ep5.estcla      ".
					   " FROM  ".
					   "	spg_ep5, ".
                       "    spg_cuentas  ".
					   " WHERE ".
					   "   spg_cuentas.codemp = '".$this->dts_empresa["codemp"]."' AND ".
					   "   spg_cuentas.codemp = spg_ep5.codemp AND ".
					   "   spg_cuentas.estcla = spg_ep5.estcla AND ".
					   "   spg_cuentas.codestpro1 = spg_ep5.codestpro1 AND ".
					   "   spg_cuentas.codestpro2 = spg_ep5.codestpro2 AND ".
					   "   spg_cuentas.codestpro3 = spg_ep5.codestpro3 AND ".
					   "   spg_cuentas.codestpro4 = spg_ep5.codestpro4 AND ".
					   "   spg_cuentas.codestpro5 = spg_ep5.codestpro5 ";
					   
			 if((!empty($as_codfuefindes))&&(!empty($as_codfuefinhas)))
			 {
			   $ls_filtro_fuefin= " AND spg_ep5.codfuefin BETWEEN '".$as_codfuefindes."' AND '".$as_codfuefinhas."' ";
			 }
			 
			 if((!empty($as_codestpro1))&&(!empty($as_estclades))&&(!empty($as_codestpro1h))&&(!empty($as_estclahas)))
			 {
			  switch(strtoupper($_SESSION["ls_gestor"]))
			  {
			   case 'POSTGRES':
					$ls_filtro_estructura = " AND spg_ep5.codestpro1||spg_ep5.codestpro2||spg_ep5.codestpro3||spg_ep5.codestpro4||spg_ep5.codestpro5||spg_ep5.estcla BETWEEN '".$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estclades."' AND '".$as_codestpro1h.$as_codestpro2h.$as_codestpro3h.$as_codestpro4h.$as_codestpro5h.$as_estclades."' ";
			   break;
				 
			   case 'MYSQLT':
					$ls_filtro_estructura = " AND CONCAT(spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5,spg_ep5.estcla) BETWEEN '".$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estclades."' AND '".$as_codestpro1h.$as_codestpro2h.$as_codestpro3h.$as_codestpro4h.$as_codestpro5h.$as_estclades."' ";
			   break;
				 
			   case 'MYSQLI':
					$ls_filtro_estructura = " AND CONCAT(spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5,spg_ep5.estcla) BETWEEN '".$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estclades."' AND '".$as_codestpro1h.$as_codestpro2h.$as_codestpro3h.$as_codestpro4h.$as_codestpro5h.$as_estclades."' ";
			   break;
			  }
			 }
			 
	         break;
	 }
	 $ls_sql .= $ls_filtro_spg.$ls_filtro_scg.$ls_filtro_fuefin.$ls_filtro_estructura.$ls_seguridad;
	 if($this->dts_empresa["estmodest"] == 1)
	 {
	  $ls_sql .=  " ORDER BY spg_ep3.codestpro1,spg_ep3.codestpro2,spg_ep3.codestpro3"; 
	 }
	 else
	 {
	  $ls_sql .= " ORDER BY spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5"; 
	 }
	 
	 $rs_estructuras=$this->SQL->select($ls_sql);
	 if($rs_estructuras===false)
	 { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spg_reporte ". 
									"M?TODO->uf_reporte_listado_cuenta_estructura ".
									"ERROR->".$this->fun->uf_convertirmsg($this->io_sql->message)); 
		$lb_valido = false;
	 }
	 else
	 {
	  if($rs_estructuras->EOF)
	  {
	   $lb_valido = false;
	  }
	 }
	
	 $arrResultado['rs_estructuras']=$rs_estructuras;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
 }
 
 function uf_reporte_listado_cuenta($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
								    $as_estcla,$as_spg_cuenta,$as_spg_cuentah,$as_sc_cuenta,$as_sc_cuentah,
									$rs_cuentas=NULL,$ab_cueart=false)
 {
     //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_reporte_listado_cuenta
	 //         Access :	private
	 //     Argumentos :    as_codestpro1  ... $as_codestpro5  // rango nivel estructura presupuestaria
	 //                     as_estcla                          // Estatus de la Estructura Presupuestaria
	 //                     as_spg_cuenta                      // Cuenta de Gasto desde
	 //                     as_spg_cuentah                     // Cuenta de Gasto hasta
     //                     as_sc_cuenta                       // Cuenta Contable desde
	 //                     as_sc_cuentah                      // Cuenta Contable hasta
	 //                     rs_estructuras                     // Recorset con las cuentas
	 //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Funcion para retornar las cuentas a mostrar en el reporte de Listado de Cuentas Presupuestarias
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    28/06/2010          Fecha ?ltima Modificacion :
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido = true;
	 $ls_filtro_spg="";
	 $ls_filtro_scg="";
	 $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spg_cuentas',$ls_seguridad);
	 
	 if((!empty($as_spg_cuenta))&&(!empty($as_spg_cuentah)))
	 {
	  $ls_filtro_spg=" AND spg_cuentas.spg_cuenta BETWEEN '".$as_spg_cuenta."' AND '".$as_spg_cuentah."' ";
	 }
	 
	 if((!empty($as_sc_cuenta))&&(!empty($as_sc_cuentah)))
	 {
	  $ls_filtro_scg=" AND spg_cuentas.sc_cuenta BETWEEN '".$as_sc_cuenta."' AND '".$as_sc_cuentah."' ";
	 }
	 
	 if(!$ab_cueart){
		 $ls_sql = "SELECT ".
				   "      spg_cuentas.spg_cuenta, ".
				   "	  spg_cuentas.denominacion, ".
				   "	  spg_cuentas.sc_cuenta, ".
				   "	  scg_cuentas.denominacion as denominacion_scg, ".
				    "	  spg_cuentas.status ".
				   "	FROM ".
				   "	spg_cuentas,  ".
				   "	scg_cuentas   ".
				   "	WHERE ".
				   "	  spg_cuentas.codemp = scg_cuentas.codemp AND ".
				   "	  spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta AND ".
				   "	  spg_cuentas.codemp = '".$this->dts_empresa["codemp"]."' AND ".
				   "	  spg_cuentas.codestpro1 = '".$as_codestpro1."' AND ".
				   "	  spg_cuentas.codestpro2 = '".$as_codestpro2."' AND ".
				   "	  spg_cuentas.codestpro3 = '".$as_codestpro3."' AND ".
				   "	  spg_cuentas.codestpro4 = '".$as_codestpro4."' AND ".
				   "	  spg_cuentas.codestpro5 = '".$as_codestpro5."' AND ".
				   "	  spg_cuentas.estcla     = '".$as_estcla."'";
	 }
	 else{
	 	 $ls_sql = "SELECT ".
				   "      spg_cuentas.spg_cuenta, ".
				   "	  spg_cuentas.denominacion, ".
				   "	  spg_cuentas.sc_cuenta_art as sc_cuenta, ".
				   "	  scg_cuentas.denominacion as denominacion_scg, ".
				    "	  spg_cuentas.status ".
				   "	FROM ".
				   "	spg_cuentas  ".
				   "	LEFT JOIN scg_cuentas  ON ".
	 	 		   "	  spg_cuentas.codemp = scg_cuentas.codemp AND ".
				   "	  spg_cuentas.sc_cuenta_art = scg_cuentas.sc_cuenta ".	
				   "	WHERE ".
				   "	  spg_cuentas.codemp = '".$this->dts_empresa["codemp"]."' AND ".
				   "	  spg_cuentas.codestpro1 = '".$as_codestpro1."' AND ".
				   "	  spg_cuentas.codestpro2 = '".$as_codestpro2."' AND ".
				   "	  spg_cuentas.codestpro3 = '".$as_codestpro3."' AND ".
				   "	  spg_cuentas.codestpro4 = '".$as_codestpro4."' AND ".
				   "	  spg_cuentas.codestpro5 = '".$as_codestpro5."' AND ".
				   "	  spg_cuentas.estcla     = '".$as_estcla."'";
	 }
			   
   $ls_sql .= $ls_filtro_spg.$ls_filtro_scg;
   
   $ls_sql .=  "    ORDER BY spg_cuentas.spg_cuenta ";
   
   $rs_cuentas=$this->SQL->select($ls_sql);
   if($rs_cuentas===false)
   {  // error interno sql
	 $this->io_msg->message("CLASE->sigesp_spg_reporte ". 
								"M?TODO->uf_reporte_listado_cuenta ".
								"ERROR->".$this->fun->uf_convertirmsg($this->io_sql->message)); 
	$lb_valido = false;
   }
   else
   {
    if($rs_cuentas->EOF)
    {
     $lb_valido = false;
    }
   }
	
	 $arrResultado['rs_cuentas']=$rs_cuentas;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;
  
 }
	public function buscarAsignadoFuente($codestpro, $spg_cuenta, $nivel, $codfuefinD, $codfuefinH) {
		$asignado  = 0;
		$filtroSQL = '';
		if(!empty($codestpro)) {
			if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
			{
				$filtroSQL = " AND (CONCAT(CF.codestpro1,CF.codestpro2,CF.codestpro3,CF.codestpro4,CF.codestpro5,CF.estcla) = '{$codestpro}') ";
			}
			else {
				$filtroSQL = " AND ((CF.codestpro1||CF.codestpro2||CF.codestpro3||CF.codestpro4||CF.codestpro5||CF.estcla) = '{$codestpro}') ";
			}
		}
	
		$spg_cuenta = $this->obtenerDigitoCuenta($spg_cuenta);
		$cadenaSQL  = "SELECT SUM (monto) AS monto
						FROM spg_cuenta_fuentefinanciamiento CF
						WHERE CF.codfuefin BETWEEN '{$codfuefinD}' AND '{$codfuefinH}' {$filtroSQL}
						AND CF.spg_cuenta LIKE '{$spg_cuenta}%'";
		$dataAsig = $this->SQL->select($cadenaSQL);
		if (!$dataAsig->EOF) {
			$asignado = $dataAsig->fields['monto'];
		}
		unset($dataAsig);
	
		return $asignado;
	}
	function formatearCuenta($spg_cuenta) {
		$formato = $_SESSION['la_empresa']['formpre'];
		$arrFormato = explode('-', $formato);
		$numNiv     = count($arrFormato);
		$arrCuenta  = array();
		$posicion   = 0;
		for ($i = 0; $i < $numNiv; $i++) {
			$digitos = strlen($arrFormato[$i]);
			$arrCuenta[$i] = substr($spg_cuenta, $posicion, $digitos);
			$posicion = $posicion + $digitos;
		}
		
		return $arrCuenta;
	}

	function obtenerDigitoCuenta($spg_cuenta) {
		$arrCuenta = $this->formatearCuenta($spg_cuenta);
		$numNiv  = count($arrCuenta);
		$cuentaFiltro = '';
		for ($i = 0; $i < $numNiv; $i++) {
			if (($i+1) == ($numNiv)) {
				if (intval($arrCuenta[$i])!=0) {
					$cuentaFiltro .= $arrCuenta[$i];
				}
			}
			else {
				if (intval($arrCuenta[$i])!=0) {
					$cuentaFiltro .= $arrCuenta[$i];
				}
				else {
					if (intval($arrCuenta[$i+1])!=0) {
						$cuentaFiltro .= $arrCuenta[$i];
					}
				}
			}
		}
		
		
		return $cuentaFiltro;
	}

    function uf_spg_reporte_modificaciones_presupuestarias_md($ai_rect,$ai_trans,$ai_insub,$ai_cred,$adt_fecini,$adt_fecfin,
	                                                       $as_comprobante,$as_procede,$adt_fecha)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_modificaciones_presupuestarias
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde
     //              	    adt_fecfin  // fecha hasta
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso
	 //	   Description :	Reporte que genera la salida para las modificaciones presupuestarias
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    15/04/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;

	  $ls_gestor = $_SESSION["ls_gestor"];
      $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("spg_cuenta");
      $ls_cad=$this->uf_spg_reporte_chequear_modificaciones($ai_rect,$ai_insub,$ai_trans,$ai_cred);
      $ls_cadena=str_replace('procede','MOV.procede',$ls_cad);

	  if (empty($as_comprobante))
	  {
	    $ls_cadena_2="";
	  }
	  else
	  {
	    $ls_cadena_2=" AND CMP.comprobante='".$as_comprobante."' AND CMP.procede='".$as_procede."'  AND MOV.fecha='".$adt_fecha."' ";

	  }
	  $ls_sql="SELECT CMP.comprobante, CMP.procede, CMP.descripcion AS cmp_descripcion, CMP.fecha as fecaprmod, MOV.codestpro1,MOV.codestpro2, ".
       		  "		  MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.spg_cuenta,CTA.denominacion ,MOV.operacion, MOV.monto, CMP.fecha ".
			  "	 FROM sigesp_cmp_md CMP ".
			  " INNER JOIN spg_dtmp_cmp MOV ".
	          "    ON CMP.codemp = MOV.codemp ".
	  		  "   AND CMP.procede = MOV.procede ".
	          "   AND CMP.comprobante = MOV.comprobante ". 
	          "   AND CMP.fecha = MOV.fecha ". 
	          " INNER JOIN spg_cuentas CTA ".
	          "    ON MOV.codemp = CTA.codemp  ".
	          "   AND MOV.codestpro1 = CTA.codestpro1 ".
	          "   AND MOV.codestpro2 = CTA.codestpro2 ".
	          "   AND MOV.codestpro3 = CTA.codestpro3 ".
	          "   AND MOV.codestpro4 = CTA.codestpro4 ".
	          "   AND MOV.codestpro5 = CTA.codestpro5 ".
	          "   AND MOV.estcla = CTA.estcla ".
	          "   AND MOV.spg_cuenta = CTA.spg_cuenta ".
	  		  " WHERE CMP.codemp='".$ls_codemp."' ".
	          "   AND (".$ls_cadena.") AND MOV.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' ".
	  		  "   AND CMP.tipo_comp   = 2  ".$ls_cadena_2." ".
	  		  "  ORDER BY  MOV.operacion,MOV.spg_cuenta";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spg_class_report
		                          M?TODO->uf_spg_reporte_modificaciones_presupuestarias
								  ERROR->".$this->fun->uf_convertirmsg($this->SQL->message));
	 }
	 else
	 {
			while($row=$this->SQL->fetch_row($rs_data))
			{
				  $ls_cmp_descripcion=$row["cmp_descripcion"];
				  $ls_procede=$row["procede"];
				  $ls_comprobante=$row["comprobante"];
				  $ldt_fecha=$row["fecha"];
				  $ldt_fecaprmod = $row["fecaprmod"];
				  $ls_codestpro1=$row["codestpro1"];
				  $ls_codestpro2=$row["codestpro2"];
				  $ls_codestpro3=$row["codestpro3"];
				  $ls_codestpro4=$row["codestpro4"];
				  $ls_codestpro5=$row["codestpro5"];
				  $ls_programatica=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5;
				  $ls_spg_cuenta=$row["spg_cuenta"];
				  $ls_denominacion=$row["denominacion"];
				  $ls_procede_doc=$row["procede_doc"];
				  $ls_documento=$row["documento"];
				  $ls_operacion=$row["operacion"];
				  $ls_descripcion=$row["descripcion"];
				  $ld_monto=$row["monto"];
				  $ls_orden=$row["orden"];
			      $ld_asignado = 0;
	              $ld_aumentos = 0;
	              $ld_disminuciones = 0;
	              $ld_precompromisos = 0;
	              $ld_compromisos = 0;
	              $ld_causado = 0;
	              $ld_pagado = 0;

                  $arrResultado=$this->uf_spg_reporte_calcular_monto_operacion($ld_asignado,$ld_aumentos,$ld_disminuciones,
				                                                 $ld_precompromisos,$ld_compromisos,$ld_causado,
																 $ld_pagado,$ls_operacion,$ld_monto);
				 $ld_asignado=$arrResultado['ad_asignado'];
				 $ld_aumentos=$arrResultado['ad_aumentos'];
				 $ld_disminuciones=$arrResultado['ad_disminuciones'];
				 $ld_precompromisos=$arrResultado['ad_precompromisos'];
				 $ld_compromisos=$arrResultado['ad_compromisos'];
				 $ld_causado=$arrResultado['ad_causado'];
				 $ld_pagado=$arrResultado['ad_pagado'];
				 $ld_aumento=$ld_aumentos;
				 $ld_disminucion=$ld_disminuciones;
 				 $ls_procomp=$ls_procede.$ls_comprobante;
				 $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				 $this->dts_reporte->insertRow("cmp_descripcion",$ls_cmp_descripcion);
				 $this->dts_reporte->insertRow("programatica",$ls_programatica);
				 $this->dts_reporte->insertRow("spg_cuenta",$ls_spg_cuenta);
				 $this->dts_reporte->insertRow("documento",$ls_documento);
				 $this->dts_reporte->insertRow("fecha",$ldt_fecha);
				 $this->dts_reporte->insertRow("aumento",$ld_aumento);
				 $this->dts_reporte->insertRow("disminucion",$ld_disminucion);
				 $this->dts_reporte->insertRow("fecaprmod",$ldt_fecaprmod);
				 $this->dts_reporte->insertRow("procede",$ls_procede);
				 $this->dts_reporte->insertRow("procomp",$ls_procomp);
				 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			}//while
			$li_tot=$this->dts_reporte->getRowCount("spg_cuenta");
			if($li_tot==0)
			{$lb_valido=false;}
	  $this->SQL->free_result($rs_data);
	 }//else
	 
  return $lb_valido;
}//fin uf_spg_reporte_modificaciones_presupuestarias
	

function uf_spg_reporte_sigeproden_ejecucion_proyectos($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
	                                               $as_codestpro5,$adt_fecini,$adt_fecfin,$as_estcla,$as_codfuefin,$as_orden)
{
    $li_estmodest=$_SESSION["la_empresa"]["estmodest"];
    $ls_Sql_Where = "";
    $ls_str_estructura_from = "";
    $ls_str_estructura_to = "";
    $ls_seguridad="";
    $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spg_dt_cmp',$ls_seguridad);
    if($li_estmodest==1)
    {
       $ls_tabla="spg_dt_fuentefinanciamiento";
       $ls_cadena_fuefin=" AND spg_dt_cmp.codestpro1=".$ls_tabla.".codestpro1 AND spg_dt_cmp.codestpro2=".$ls_tabla.".codestpro2 AND ".
                         "    spg_dt_cmp.codestpro3=".$ls_tabla.".codestpro3 AND spg_dt_cmp.estcla=".$ls_tabla.".estcla";
    }
    elseif($li_estmodest==2)
    {
       $ls_tabla="spg_dt_fuentefinanciamiento";
       $ls_cadena_fuefin="AND spg_dt_cmp.codestpro1=".$ls_tabla.".codestpro1 AND spg_dt_cmp.codestpro2=".$ls_tabla.".codestpro2 AND ".
                         "    spg_dt_cmp.codestpro3=".$ls_tabla.".codestpro3 AND spg_dt_cmp.codestpro4=".$ls_tabla.".codestpro4 AND ".
                         "    spg_dt_cmp.codestpro5=".$ls_tabla.".codestpro5 AND spg_dt_cmp.estcla=".$ls_tabla.".estcla";
    }
    $lb_valido = true;
    $ls_gestor = $_SESSION["ls_gestor"];
    $ls_codemp = $this->dts_empresa["codemp"];
    $this->dts_cab->resetds("comprobante");

    $arrResultado= $this->uf_obtener_rango_programatica($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_codestpro1,
                                                        $as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$ls_Sql_Where,$ls_str_estructura_from,
							$ls_str_estructura_to,$as_estclades,$as_estclahas);
    $ls_Sql_Where=$arrResultado['as_Sql_Where'];
    $ls_str_estructura_from=$arrResultado['as_str_estructura_from'];
    $ls_str_estructura_to=$arrResultado['as_str_estructura_to'];
    $ls_Sql_Where = trim($ls_Sql_Where);
    if ( !empty($ls_Sql_Where) )
    {
        $ls_str_sql_where=$ls_Sql_Where." AND ";
	$ls_str_sql_where = str_replace("PCT","spg_dt_cmp",$ls_str_sql_where);
    }
    else
    {
        $ls_str_sql_where="";
    }

    $ls_estructura_desde=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estcla;
    $ls_estructura_hasta=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estcla;
    if($as_orden=='F')
    {
        $ls_ordenar="TA.fecha";
    }
    elseif($as_orden=='D')
    {
        $ls_ordenar="TA.Documento";
    }
    if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
    {
        $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
        $ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
        $ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
    }
    else
    {
        $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
        $ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
        $ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
    }
    if (!empty($ls_estructura_desde)&&!empty($ls_estructura_hasta))
    {
        $ls_sql="SELECT distinct (".$ls_str_sql_2.") as programatica ".
                "  FROM  spg_dt_cmp spg_dt_cmp, spg_operaciones OPE, ".$ls_tabla." ".
                " WHERE spg_dt_cmp.codemp='".$ls_codemp."' AND  spg_dt_cmp.operacion=OPE.operacion AND ".
                "       ".$ls_str_sql_where." ".
                "       spg_dt_cmp.fecha <= '".$adt_fecfin."' AND ".
                "       spg_dt_cmp.codfuefin BETWEEN '".trim($as_codfuefin)."' AND '".trim($as_codfuefin)."' AND ".
                "       spg_dt_cmp.codfuefin=".$ls_tabla.".codfuefin ".
                "       ".$ls_cadena_fuefin."    ".$ls_seguridad.
                " ORDER BY programatica";
    }
    else
    {
        $ls_sql="SELECT distinct (".$ls_str_sql_2.") as programatica ".
                "  FROM  spg_dt_cmp spg_dt_cmp, spg_operaciones OPE, ".$ls_tabla." ".
                " WHERE spg_dt_cmp.codemp='".$ls_codemp."' AND  spg_dt_cmp.operacion=OPE.operacion AND ".
                "       spg_dt_cmp.codfuefin BETWEEN '".trim($as_codfuefin)."' AND '".trim($as_codfuefin)."' AND  ".
                "       spg_dt_cmp.codfuefin=".$ls_tabla.".codfuefin ".
                "       ".$ls_cadena_fuefin."    ".$ls_seguridad.
                " ORDER BY programatica";
    }
    $rs_data=$this->SQL->select($ls_sql);
    if($rs_data===false)
    {   // error interno sql
            $this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_mayor_analitico ".$this->fun->uf_convertirmsg($this->SQL->message);
            $lb_valido = false;
    }
    $arrResultado['rs_data']=$rs_data;
    $arrResultado['lb_valido']=$lb_valido;
    return $arrResultado;
 }//uf_spg_reporte_sigeproden_ejecucion_proyectos
/********************************************************************************************************************************/

function uf_spg_reporte_sigeproden_ejecucion_proyectos2($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
	                                               $as_codestpro5,$adt_fecini,$adt_fecfin,$as_estcla,$as_codfuefin,$as_orden)
{
    $lb_valido= true;
    $ls_gestor = $_SESSION["ls_gestor"];
    $ls_codemp = $this->dts_empresa["codemp"];
    $this->dts_reporte->resetds("spg_cuenta");

    $ls_estructura_desde=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estcla;
    $ls_estructura_hasta=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5.$as_estcla;
    
    if($as_orden=='F')
    {
        $ls_ordenar="TA.fecha";
    }
    elseif($as_orden=='D')
    {
        $ls_ordenar="TA.Documento";
    }

    if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
    {
        $ls_str_sql_1 = "CONCAT(RTRIM(BEN.apebene),', ',BEN.nombene) ";
	$ls_str_sql_2 = "CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla)";
	$ls_str_sql_3 = "CONCAT(MOV.codestpro1,MOV.codestpro2,MOV.codestpro3,MOV.codestpro4,MOV.codestpro5,MOV.estcla)";
    }
    else
    {
        $ls_str_sql_1 = "RTRIM(BEN.apebene)||', '||BEN.nombene ";
	$ls_str_sql_2 = "spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla";
	$ls_str_sql_3 = "MOV.codestpro1||MOV.codestpro2||MOV.codestpro3||MOV.codestpro4||MOV.codestpro5||MOV.estcla";
    }


    $ls_sql=" SELECT spg_dt_cmp.procede, spg_dt_cmp.comprobante, spg_dt_cmp.fecha,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
	    "        spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, ".
	    "        spg_dt_cmp.descripcion as nombre_prog, C.denominacion, ".
	    "        CASE spg_dt_cmp.operacion  ".
	    "        WHEN 'AAP' THEN sum(spg_dt_cmp.monto) ".
	    "        END as asignar, ".
	    "        CASE spg_dt_cmp.operacion ".
	    "        WHEN 'AU' THEN sum(spg_dt_cmp.monto) ".
	    "        END as aumento, ".
	    "        CASE spg_dt_cmp.operacion  ".
	    "        WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'CS' THEN sum(spg_dt_cmp.monto) ".
	    "        END as compromiso,".
	    "        CASE spg_dt_cmp.operacion".
	    "        WHEN 'DI' THEN sum(spg_dt_cmp.monto) ".
	    "        END as disminucion, ".
	    "        CASE spg_dt_cmp.operacion ".
	    "        WHEN 'GC' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'CG' THEN sum(spg_dt_cmp.monto) ".
	    "        END as causado, ".
	    "        CASE spg_dt_cmp.operacion ".
	    "        WHEN 'PC' THEN sum(spg_dt_cmp.monto) ".
	    "        END as precompromiso, ".
	    "        CASE spg_dt_cmp.operacion ".
	    "        WHEN 'CCP' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'CP' THEN sum(spg_dt_cmp.monto) ".
	    "        WHEN 'PG' THEN sum(spg_dt_cmp.monto) ".
	    "        END as pago, ".
	    "        CM.tipo_destino, BE.apebene, BE.nombene, PR.nompro ".
	    "   FROM spg_dt_cmp spg_dt_cmp,spg_operaciones OPE,spg_cuentas C, sigesp_cmp CM, rpc_beneficiario BE, rpc_proveedor PR  ".
	    "  WHERE spg_dt_cmp.codemp='".$ls_codemp."' AND (spg_dt_cmp.operacion=OPE.operacion) AND ".
	    "        spg_dt_cmp.codestpro1=C.codestpro1 AND spg_dt_cmp.codestpro2=C.codestpro2 AND ".
	    "        spg_dt_cmp.codestpro3=C.codestpro3 AND spg_dt_cmp.codestpro4=C.codestpro4 AND ".
	    "        spg_dt_cmp.codestpro5=C.codestpro5 AND spg_dt_cmp.spg_cuenta=C.spg_cuenta AND".
	    "        (".$ls_str_sql_2." = '".$ls_estructura_desde."') ".
	    "        AND spg_dt_cmp.fecha >= '".$adt_fecini."' AND  spg_dt_cmp.fecha <= '".$adt_fecfin."'".
	    "        AND spg_dt_cmp.codfuefin between '".$as_codfuefin."' AND '".$as_codfuefin."' ".
            "        AND (OPE.comprometer=1 OR OPE.causar=1 OR OPE.pagar=1) ".
	    "        AND spg_dt_cmp.codemp=CM.codemp ".
	    "        AND spg_dt_cmp.procede=CM.procede ".
	    "        AND spg_dt_cmp.comprobante=CM.comprobante ".
	    "        AND spg_dt_cmp.fecha=CM.fecha ".
	    "        AND spg_dt_cmp.codban=CM.codban ".
	    "        AND spg_dt_cmp.ctaban=CM.ctaban ".
	    "        AND CM.cod_pro=PR.cod_pro ".
	    "        AND CM.ced_bene=BE.ced_bene ".
	    "  GROUP BY spg_dt_cmp.fecha, spg_dt_cmp.procede, spg_dt_cmp.comprobante,spg_dt_cmp.estcla,spg_dt_cmp.codestpro1, spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3, ".
	    "        spg_dt_cmp.codestpro4, spg_dt_cmp.codestpro5, spg_dt_cmp.spg_cuenta, spg_dt_cmp.procede_doc, spg_dt_cmp.documento, spg_dt_cmp.operacion, spg_dt_cmp.descripcion, ".
	    "        spg_dt_cmp.monto, C.denominacion,OPE.asignar,OPE.aumento,OPE.disminucion,OPE.comprometer,OPE.causar,OPE.pagar, ".
	    "        CM.tipo_destino, BE.apebene, BE.nombene, PR.nompro".
	    "  ORDER BY spg_dt_cmp.spg_cuenta,spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5, ".
	    "        spg_dt_cmp.fecha,OPE.asignar DESC, OPE.aumento DESC, OPE.disminucion DESC, OPE.comprometer DESC, ".
	    "        OPE.causar DESC, OPE.pagar DESC, spg_dt_cmp.Documento";
    $rs_mov_spg=$this->SQL->select($ls_sql);
    if($rs_mov_spg===false)
    {   // error interno sql
        $this->io_msg->message("Error en Reporte 1".$this->fun->uf_convertirmsg($this->SQL->message));
        return;
        $lb_valido= false;
    }
    $arrResultado['rs_data']=$rs_mov_spg;
    $arrResultado['lb_valido']=$lb_valido;
    return $arrResultado;
}
/********************************************************************************************************************************/
 
}//fin de clase
?>