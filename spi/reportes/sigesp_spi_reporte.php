<?php
/***********************************************************************************
* @fecha de modificacion: 11/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../../shared/class_folder/class_sigesp_int.php");
require_once("../../shared/class_folder/class_sigesp_int_scg.php");
require_once("../../shared/class_folder/class_sigesp_int_spg.php");	
require_once("../../shared/class_folder/class_sigesp_int_spi.php");
require_once("sigesp_spi_funciones_reportes.php");	


ini_set('max_execution_time','0');
/********************************************************************************************************************************/	
class sigesp_spi_reporte
{
    //conexion	
	var $sqlca;
	
	//Instancia de la clase funciones.
	var $is_msg_error;
	var $dts_empresa; // datastore empresa
	var $dts_reporte;
	var $dts_cab;
	var $obj="";
	var $io_sql;
	var $io_include;
	var $io_connect;
	var $io_function;	
	var $io_msg;
	var $io_fecha;
	var $sigesp_int_spg;
	var $sigesp_int_spi;
	var $io_spg_report_funciones;	
/********************************************************************************************************************************/	
    public function __construct()
    {
		$this->io_function=new class_funciones() ;
		$this->io_include=new sigesp_include();
		$this->io_connect=$this->io_include->uf_conectar();
		//$this->io_connect->debug = true;
		$this->io_sql=new class_sql($this->io_connect);		
		$this->obj=new class_datastore();
		$this->data_est=new class_datastore();
		$this->dts_empresa=$_SESSION["la_empresa"];		
		$this->dts_cab=new class_datastore();
		$this->dts_reporte=new class_datastore();
		$this->io_fecha = new class_fecha();
		$this->io_msg=new class_mensajes();
		$this->sigesp_int_spg=new class_sigesp_int_spg();
		$this->sigesp_int_spi=new class_sigesp_int_spi();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->li_estmodest=$_SESSION["la_empresa"]["estmodest"];
		$this->io_spg_report_funciones=new sigesp_spi_funciones_reportes();
    }
/********************************************************************************************************************************/	
	//////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  " COMPROBANTES FORMATO 1 Y FORMATO 2" //
	////////////////////////////////////////////////////////////////
    function uf_spi_reporte_comprobante_formato1($as_procede_ori,$as_procede_des,$as_comprobante_ori,$as_comprobante_des,
	                                             $adt_fecini,$adt_fecfin,$as_orden)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_comprobante_formato1
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
	 // Fecha Creaci?n :    28/09/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
         $ls_gestor=$_SESSION["ls_gestor"];
		$lb_valido = false;
		$ls_codemp = $this->dts_empresa["codemp"];
	    $this->dts_reporte->reset_ds();

		if((!empty($as_procede_ori))&&(!empty($as_procede_des)))
		{
			   $ls_cad_where1=" MV.procede between '".$as_procede_ori."' AND  '".$as_procede_des."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if((!empty($as_comprobante_ori))&&(!empty($as_comprobante_des)))
		{
			   $ls_cad_where2=" MV.comprobante between '".$as_comprobante_ori."' AND  '".$as_comprobante_des."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if((!empty($adt_fecini))&&(!empty($adt_fecfin)))
		{
			   $ls_cad_where3=" MV.fecha between '".$adt_fecini."' AND  '".$adt_fecfin."' ";
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
	   if($as_orden=="F")
       {
	     //$ls_orden_select="rep1.fecha,rep1.spi_cuenta";
		 $ls_orden_select="rep1.fecha";
	   }
	   if($as_orden=="C")
	   {
	     $ls_orden_select="rep1.spi_cuenta,rep1.fecha";
	   }
	   
	   if ($ls_gestor=="INFORMIX")
	   {	  
	      $ls_sql=" SELECT *  ".
                  " FROM table (multiset(SELECT MV.*, CC.denominacion as dencuenta, OP.denominacion as denoperacion ".
                  "       FROM  spi_dt_cmp MV, spi_cuentas CC, spi_operaciones OP ".
                  "       WHERE MV.codemp=CC.codemp AND CC.codemp='".$this->ls_codemp."'  AND ".
                  "             MV.spi_cuenta = CC.spi_cuenta AND MV.operacion=OP.operacion  ".$ls_cad_where." )) rep1 ".
                  " left join table (multiset(SELECT CMP.codemp,CMP.procede,CMP.comprobante,CMP.fecha, CMP.tipo_destino, CMP.cod_pro, ".
		   	      "                   CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
                  "            FROM  sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
                  "            WHERE CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$this->ls_codemp."' AND  ".
			      "                  CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene)) rep2 ".
                  " on rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
                  "    rep1.fecha=rep2.fecha ".
                  " ORDER BY  ".$ls_orden_select." ";   ///////print $ls_sql;
		}
		else
		{	  
	      $ls_sql=" SELECT *  ".
                  " FROM (SELECT MV.*, CC.denominacion as dencuenta, OP.denominacion as denoperacion ".
                  "       FROM  spi_dt_cmp MV, spi_cuentas CC, spi_operaciones OP ".
                  "       WHERE MV.codemp=CC.codemp AND CC.codemp='".$this->ls_codemp."'  AND ".
                  "             MV.spi_cuenta = CC.spi_cuenta AND MV.operacion=OP.operacion  ".$ls_cad_where." ) rep1 ".
                  " left join (SELECT CMP.codemp,CMP.procede,CMP.comprobante,CMP.fecha, CMP.tipo_destino, CMP.cod_pro, ".
		   	      "                   CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
                  "            FROM  sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
                  "            WHERE CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$this->ls_codemp."' AND  ".
			      "                  CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene) rep2 ".
                  " on rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
                  "    rep1.fecha=rep2.fecha ".
                  " ORDER BY  ".$ls_orden_select." "; //print $ls_sql."<br>";
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->io_msg->message("CLASE->sigesp_spi_reporte
									M?TODO->uf_spi_reporte_comprobante_formato1 
									ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido = false;
		}
		else
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{/////print "entro en el while";
               $ls_procede=$row["procede"];
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_spi_cuenta=$row["spi_cuenta"];
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
			   $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
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
			$this->io_sql->free_result($rs_data);
		}//else
	 return $lb_valido;
  }// fin uf_spg_reporte_comprobante_formato1
/********************************************************************************************************************************/
    function uf_spi_reporte_select_comprobante_formato1($as_procede_ori,$as_procede_des,$as_comprobante_ori,$as_comprobante_des,
	                                                    $adt_fecini,$adt_fecfin,$as_orden)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_select_comprobante_formato1
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
	 // Fecha Creaci?n :    28/09/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_gestor=$_SESSION["ls_gestor"];
		$lb_valido = true;
	    $this->dts_reporte->reset_ds();

		if((!empty($as_procede_ori))&&(!empty($as_procede_des)))
		{
			   $ls_cad_where1=" MV.procede between '".$as_procede_ori."' AND  '".$as_procede_des."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if((!empty($as_comprobante_ori))&&(!empty($as_comprobante_des)))
		{
			   $ls_cad_where2=" MV.comprobante between '".$as_comprobante_ori."' AND  '".$as_comprobante_des."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if((!empty($adt_fecini))&&(!empty($adt_fecfin)))
		{
			   $ls_cad_where3=" MV.fecha between '".$adt_fecini."' AND  '".$adt_fecfin."' ";
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
	   if($as_orden=="F")
       {
	     //$ls_orden_select="rep1.fecha,rep1.spi_cuenta";
		 $ls_orden_select="rep1.fecha";
	   }
	   if($as_orden=="C")
	   {
	     $ls_orden_select="rep1.spi_cuenta,rep1.fecha";
	   }	
	   
	   if ($ls_gestor=="INFORMIX")
	   {
	     $ls_sql=" SELECT distinct rep1.comprobante,rep1.procede,rep1.fecha,rep2.ced_bene,rep2.cod_pro,nompro, rep2.apebene, ".
                 "        rep2.nombene,rep2.tipo_destino, rep1.fecha,rep1.spi_cuenta ".
                 " FROM table (MULTISET(SELECT MV.*, CC.denominacion as dencuenta, OP.denominacion as denoperacion ".
                 "       FROM  spi_dt_cmp MV, spi_cuentas CC, spi_operaciones OP ".
                 "       WHERE MV.codemp=CC.codemp AND CC.codemp='".$this->ls_codemp."' AND MV.spi_cuenta = CC.spi_cuenta AND ".
                 "             MV.operacion=OP.operacion  ".$ls_cad_where." )) rep1 ".
                 " left join table (MULTISET(SELECT CMP.codemp, CMP.procede, CMP.comprobante, CMP.fecha, CMP.tipo_destino, ".
                 "                   CMP.cod_pro, CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
                 "            FROM   sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
                 "            WHERE  CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$this->ls_codemp."'AND ".
                 "                   CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene))rep2 ".
                 " on rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
                 "    rep1.fecha=rep2.fecha ".
                 " ORDER BY  ".$ls_orden_select."  "; ////print $ls_sql;
		}
		else
		{
	     $ls_sql=" SELECT distinct rep1.comprobante,rep1.procede,rep1.fecha,rep2.ced_bene,rep2.cod_pro,nompro, rep2.apebene, ".
                 "        rep2.nombene,rep2.tipo_destino ".
                 " FROM (SELECT MV.*, CC.denominacion as dencuenta, OP.denominacion as denoperacion ".
                 "       FROM  spi_dt_cmp MV, spi_cuentas CC, spi_operaciones OP ".
                 "       WHERE MV.codemp=CC.codemp AND CC.codemp='".$this->ls_codemp."' AND MV.spi_cuenta = CC.spi_cuenta AND ".
                 "             MV.operacion=OP.operacion  ".$ls_cad_where." ) rep1 ".
                 " left join (SELECT CMP.codemp, CMP.procede, CMP.comprobante, CMP.fecha, CMP.tipo_destino, ".
                 "                   CMP.cod_pro, CMP.ced_bene, PRV.nompro, BEN.apebene, BEN.nombene ".
                 "            FROM   sigesp_cmp CMP, rpc_proveedor PRV,rpc_beneficiario BEN ".
                 "            WHERE  CMP.codemp=PRV.codemp AND PRV.codemp=BEN.codemp AND BEN.codemp='".$this->ls_codemp."'AND ".
                 "                   CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene)rep2 ".
                 " on rep1.codemp=rep2.codemp AND rep1.procede=rep2.procede AND rep1.comprobante=rep2.comprobante AND ".
                 "    rep1.fecha=rep2.fecha ".
                 " ORDER BY  ".$ls_orden_select."  ";// print $ls_sql;
		}
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->io_msg->message("CLASE->sigesp_spi_reporte
									M?TODO->uf_spi_reporte_select_comprobante_formato1 
									ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido = false;
		}
		else
		{
			$li_numrows=$this->io_sql->num_rows($rs_data);
			if($li_numrows<=0)
			{
			   $lb_valido = false;
			}
			else
			{
				while($row=$this->io_sql->fetch_row($rs_data))
				{
				  $ls_comprobante=$row["comprobante"];
				  $ls_procede=$row["procede"];
				  $ldt_fecha=$row["fecha"];
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
					  $this->dts_cab->insertRow("fecha",$ldt_fecha);
					  $this->dts_cab->insertRow("ced_bene",$ls_ced_bene);
					  $this->dts_cab->insertRow("cod_pro",$ls_cod_pro);
					  $this->dts_cab->insertRow("nompro",$ls_nompro);
					  $this->dts_cab->insertRow("apebene",$ls_apebene);
					  $this->dts_cab->insertRow("nombene",$ls_nombene);
					  $this->dts_cab->insertRow("tipo_destino",$ls_tipo_destino);
			          $lb_valido = true;
				  }
			   }
			}	
			$this->io_sql->free_result($rs_data);
	    }//else
		return $lb_valido;
  }//uf_spi_reporte_select_comprobante_formato1
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
	/////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "LISTADO DE APERTURAS DE CUENTAS DE INGRESO " // 
	////////////////////////////////////////////////////////////////////////
    function uf_spi_reporte_apertura($adt_fecini,$adt_fecfin,$as_cuentades,$as_cuentahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_apertura
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del listado de apertura  
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    27/09/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=false;
	  $this->dts_reporte->reset_ds();
	  $ls_sql=" SELECT  spi_dt_cmp.*, spi_cuentas.denominacion ".
              " FROM    spi_dt_cmp, spi_cuentas ".
              " WHERE   spi_dt_cmp.codemp=spi_cuentas.codemp AND spi_cuentas.codemp='".$this->ls_codemp."'  AND ".
			  "         spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta AND spi_dt_cmp.procede='SPIAPR' AND ".
              "         spi_cuentas.spi_cuenta BETWEEN '".$as_cuentades."' AND '".$as_cuentahas."' AND ".
              "         spi_dt_cmp.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ".
		  " ORDER  BY spi_dt_cmp.spi_cuenta "; //print $ls_sql;	 
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte
			  					  M?TODO->uf_spi_reporte_apertura 
								  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	 }
     else
	 {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
			   $ls_codemp=$row["codemp"]; 
			   $ls_procede=$row["procede"]; 
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_spi_cuenta=$row["spi_cuenta"]; 
			   $ls_procede_doc=$row["procede_doc"]; 
			   $ls_documento=$row["documento"]; 
			   $ls_operacion=$row["operacion"]; 
			   $ls_descripcion=$row["descripcion"];
			   $ld_monto=$row["monto"]; 
			   $ls_orden=$row["orden"]; 
			   $ls_denominacion=$row["denominacion"];
			
               $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
               $this->dts_reporte->insertRow("denominacion",$ls_denominacion);			
               $this->dts_reporte->insertRow("descripcion",$ls_descripcion);			
	           $this->dts_reporte->insertRow("documento",$ls_documento);			
			   $this->dts_reporte->insertRow("monto",$ld_monto);
	  		   $lb_valido=true;
			}
	 }
	  $this->io_sql->free_result($rs_data);	 
	  return $lb_valido;
}//fin uf_spg_reporte_apertura
/********************************************************************************************************************************/	
	/////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "ACUMULADO POR CUENTAS"   // 
	/////////////////////////////////////////////////////
    function uf_spi_reporte_acumulado_cuentas($adt_fecini,$adt_fecfin,$ai_nivel,$ab_subniveles,$ai_MenorNivel,$as_spi_cuentades,$as_spi_cuentahas,$as_saldocero)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_acumulado_cuentas
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels 
	 //                     ai_MenorNivel  // variabvle que determina el menor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...) 
	 //     Creado por :    Ing. Yozelin Barragan 
	 // Fecha Creaci?n :    27/09/2006         Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_existe = false;	 
		$lb_valido = false;
		$lb_ok = true;
	    $ab_subniveles=true;
	    $this->dts_reporte->reset_ds();
		$dts_cuentas=new class_datastore();
		$ls_sql=" SELECT PCT.* ".
                " FROM  spi_cuentas PCT ".
                " WHERE codemp='".$this->ls_codemp."' AND nivel<='".$ai_nivel."'".
				" AND spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'".
                " ORDER BY  spi_cuenta "; 
		$rs_cuentas=$this->io_sql->select($ls_sql);
		if($rs_cuentas===false)
		{   //error interno sql
			$this->io_msg->message("CLASE->sigesp_spi_reporte
									M?TODO->uf_spg_reporte_acumulado_cuentas 
									ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
           $lb_valido = false;
		}
		else
        {
		   if($row=$this->io_sql->fetch_row($rs_cuentas))
		   {
              $dts_cuentas->data=$this->io_sql->obtener_datos($rs_cuentas);
              $lb_existe=true;
           }
		   $this->io_sql->free_result($rs_cuentas);   
           if($lb_existe==false)
           {
              //return false; // no hay registro
			  $lb_ok=false;
		   }
		   if($lb_ok)
		   {
           $li_total_row=$dts_cuentas->getRowCount("spi_cuenta");			
		   for ($li_i=1;$li_i<=$li_total_row;$li_i++)
  	       {   
			   $lb_si_va = false;
			   $ls_spi_cuenta = $dts_cuentas->getValue("spi_cuenta",$li_i);
			   $ls_denominacion = $dts_cuentas->getValue("denominacion",$li_i);
			   $li_nivel = $dts_cuentas->getValue("nivel",$li_i);
			   $ls_status = $dts_cuentas->getValue("status",$li_i);
			   $ls_previsto = $dts_cuentas->getValue("previsto",$li_i);
			   if ($ai_nivel=$li_nivel)
			   {
		          $lb_si_va = true;
			   }
			   if ( $ab_subniveles and ($ai_nivel<=$li_nivel) )
			   {  
			      $lb_si_va = true;
			   }
			   //$lb_si_va = true;
			   if ($lb_si_va==true)
			   {
		      	  if ($li_nivel < $ai_MenorNivel) { $ai_MenorNivel = $li_nivel; }
				  // Calculo lo Ejecutado y acumulado
				    $ld_monto_aumento=0;
				    $ld_monto_disminucion=0;
				    $ld_monto_devengado=0;
				    $ld_monto_cobrado=0;
				    $ld_cobrado_anticipado=0;
					$ld_previsto=0;
					$arrResultado = $this->uf_calcular_acumulado_operaciones_por_cuenta($adt_fecini,$adt_fecfin,$ld_previsto,$ld_monto_aumento,$ld_monto_disminucion,
																						$ld_monto_devengado,$ld_monto_cobrado,$ld_cobrado_anticipado,$ls_spi_cuenta);					
					$ld_previsto=$arrResultado['ad_previsto'];
					$ld_monto_aumento=$arrResultado['ad_aumento'];
					$ld_monto_disminucion=$arrResultado['ad_disminucion'];
					$ld_monto_devengado=$arrResultado['ad_devengado'];
					$ld_monto_cobrado=$arrResultado['ad_cobrado'];
					$ld_cobrado_anticipado=$arrResultado['ad_cobrado_anticipado'];
					$lb_valido = $arrResultado['lb_valido'];
					if (!$lb_valido)
					{
					   return false; 
					}
					$ab_agregar=true;
					if ($as_saldocero=='1')
					{
						if(($ld_previsto==0)&&($ld_monto_aumento==0)&&($ld_monto_disminucion==0)&&($ld_monto_devengado==0)&&($ld_monto_cobrado==0)&&($ld_cobrado_anticipado==0))
						{
							$ab_agregar=false;
						}
					}
					if ($ab_agregar)
					{
						$ll_row_found = $this->dts_reporte->find("spi_cuenta",$ls_spi_cuenta);
						if ($ll_row_found == 0)
						{  
							$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
							$this->dts_reporte->insertRow("denominacion",$ls_denominacion);							
							$this->dts_reporte->insertRow("nivel",$li_nivel);							
							$this->dts_reporte->insertRow("previsto",$ld_previsto);
							$this->dts_reporte->insertRow("aumento",$ld_monto_aumento);							
							$this->dts_reporte->insertRow("disminucion",$ld_monto_disminucion);
							$this->dts_reporte->insertRow("devengado",$ld_monto_devengado);
							$this->dts_reporte->insertRow("cobrado",$ld_monto_cobrado);							
							$this->dts_reporte->insertRow("cobrado_anticipado",$ld_cobrado_anticipado);							
							$this->dts_reporte->insertRow("status",$ls_status);		
							$lb_valido = true;
						
						} 
						else
						{
							$ldec_monto = $this->dts_reporte->getValue("previsto",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_previsto;
							$this->dts_reporte->updateRow("asignado",$ldec_monto,$ll_row_found);	
							$ldec_monto = $this->dts_reporte->getValue("aumento",$ll_row_found );
							$ldec_monto = $ldec_monto + $ldec_monto_aumento;
							$this->dts_reporte->updateRow("aumento",$ldec_monto,$ll_row_found);						
							$ldec_monto = $this->dts_reporte->getValue("disminucion",$ll_row_found );
							$ldec_monto = $ldec_monto + $ldec_monto_disminucion;						
							$this->dts_reporte->updateRow("disminucion",$ldec_monto,$ll_row_found);
							$ldec_monto = $this->dts_reporte->getValue("devengado",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_monto_devengado;																						
							$this->dts_reporte->updateRow("devengado",$ldec_monto,$ll_row_found);
							$ldec_monto = $this->dts_reporte->getValue("cobrado",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_monto_cobrado;																								
							$this->dts_reporte->updateRow("cobrado",$ldec_monto,$ll_row_found);							
							$ldec_monto = $this->dts_reporte->getValue("cobrado_anticipado",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_cobrado_anticipado;									
							$this->dts_reporte->updateRow("cobrado_anticipado",$ldec_monto,$ll_row_found);	
							$this->dts_reporte->updateRow("status",$ls_status,$ll_row_found);		
							$lb_valido = true;
						}// else
					}
		   } // end if 
		 } // end for
	   }//if($lb_ok)	
	 } //else
		$arrResultado['ai_MenorNivel']=$ai_MenorNivel;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  } // fin function uf_spg_reporte_acumulado_cuentas
/********************************************************************************************************************************/	
	function uf_calcular_acumulado_operaciones_por_cuenta($adt_fecini,$adt_fecfin,$ad_previsto,$ad_aumento,
	                                                      $ad_disminucion,$ad_devengado,$ad_cobrado,
														  $ad_cobrado_anticipado,$as_spi_cuenta)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_acumulado_operaciones_por_cuenta -> proviene de uf_spg_reporte_acumulado_cuentas
     //	    Returns :	$lb_valido true si se realizo la funcion con exito o false en caso contrario
	 //	Description :	M?todo  que ejecuta todas funciones de acumulado para asi sacar el acumulado por cuentas
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true; 
	  $as_spi_cuenta=$this->sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_cadena_fecha = "";
	  switch(strtoupper($ls_gestor))
	  {
	   case "POSTGRES" : $ls_cadena_fecha = " AND substr(CAST(fecha AS TEXT),1,10) <='".$adt_fecfin."'";
	   break;
	   case "MYSQLT"   : $ls_cadena_fecha = " AND substr(fecha,1,10) <='".$adt_fecfin."'";
	   break;
	   case "MYSQLI"   : $ls_cadena_fecha = " AND substr(fecha,1,10) <='".$adt_fecfin."'";
	   break;
	  }
	  $ls_sql=" SELECT * ".
			  " FROM   spi_dt_cmp ".
			  " WHERE  codemp='".$this->ls_codemp."'  AND  spi_cuenta like '".$as_spi_cuenta."' ".$ls_cadena_fecha;	   
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spi_reporte
		                        M?TODO->uf_calcular_acumulado_operaciones_por_cuenta 
								ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_data))
		{
		  $ls_operacion=$row["operacion"];
		  $ld_monto=$row["monto"];  // print "ld_monto ".$ld_monto;
		  $ldt_fecha_db=$row["fecha"];
		  $ldt_fecha=substr($ldt_fecha_db,0,10);
		  $ldt_fecha=str_replace("-","",$ldt_fecha);
		  $ldt_mesdes=str_replace("-","",$adt_fecini);
		  $ldt_meshas=str_replace("-","",$adt_fecfin);
		  $ls_opera=$this->sigesp_int_spi->uf_operacion_codigo_mensaje($ls_operacion);
		  $ls_mensaje=strtoupper($ls_opera); // devuelve cadena en MAYUSCULAS
		  $li_pos_i=strpos($ls_mensaje,"I"); 
		  if (!($li_pos_i===false)) 
		  {
		    $ad_previsto=$ad_previsto+$ld_monto;
		  }
		  if(($ldt_fecha>=$ldt_mesdes)&&($ldt_fecha<=$ldt_meshas))
		  {		
			  $li_pos_e=strpos($ls_mensaje,"E"); 
			  if (!($li_pos_e===false)) 
			  { 
				 $ad_devengado=$ad_devengado+$ld_monto;
			  }
			  $li_pos_c=strpos($ls_mensaje,"C"); 
			  if (!($li_pos_c===false)) 
			  {	
				 $ad_cobrado=$ad_cobrado+$ld_monto;
			  }
			  $li_pos_n=strpos($ls_mensaje,"N"); 
			  if (!($li_pos_n===false))
			  {	
			    $ad_cobrado_anticipado = $ad_cobrado_anticipado+$ld_monto; 
			  }
			  $li_pos_a=strpos($ls_mensaje,"A"); 
			  if (!($li_pos_a===false))
			  {	
			    $ad_aumento = $ad_aumento+$ld_monto; 
			  }
			  $li_pos_d=strpos($ls_mensaje,"D"); 
			  if (!($li_pos_d===false))
			  {	
			    $ad_disminucion = $ad_disminucion+$ld_monto;
			  }
	          $lb_valido = true;
		  }//if
		}//while
	   $this->io_sql->free_result($rs_data);
	  }//else
		$arrResultado['ad_previsto']=$ad_previsto;
		$arrResultado['ad_aumento']=$ad_aumento;
		$arrResultado['ad_disminucion']=$ad_disminucion;
		$arrResultado['ad_devengado']=$ad_devengado;
		$arrResultado['ad_cobrado']=$ad_cobrado;
		$arrResultado['ad_cobrado_anticipado']=$ad_cobrado_anticipado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin uf_calcular_acumulado_operaciones_por_cuenta 
	
	//------------------------------------------------------------------------------------------------------------------------
	function uf_calcular_acumulado_operaciones_por_cuenta2($adt_fecini,$adt_fecfin,$ad_previsto,$ad_aumento,
	                                                      $ad_disminucion,$ad_devengado,$ad_cobrado,
														  $ad_cobrado_anticipado,$as_spi_cuenta,
														  $as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                                      $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											              $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_acumulado_operaciones_por_cuenta -> proviene de uf_spg_reporte_acumulado_cuentas
     //	    Returns :	$lb_valido true si se realizo la funcion con exito o false en caso contrario
	 //	Description :	M?todo  que ejecuta todas funciones de acumulado para asi sacar el acumulado por cuentas
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true; 
	  $as_spi_cuenta=$this->sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
	  $ls_estructura_dstino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
		if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		{
			$ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)";
		}
		else
		{
			$ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
		}

	  $ls_sql=" SELECT * ".
			  " FROM   spi_dt_cmp,spi_cuentas_estructuras ".
			  " WHERE  spi_dt_cmp.codemp='".$this->ls_codemp."'  AND  spi_dt_cmp.spi_cuenta like '".$as_spi_cuenta."' ".
			  " AND  spi_dt_cmp.spi_cuenta=spi_cuentas_estructuras.spi_cuenta".
              " AND spi_dt_cmp.codemp=spi_cuentas_estructuras.codemp".
			  " AND spi_cuentas_estructuras.codestpro1=spi_dt_cmp.codestpro1 and spi_cuentas_estructuras.codestpro2=spi_dt_cmp.codestpro2 ".
              " AND spi_cuentas_estructuras.codestpro3=spi_dt_cmp.codestpro3 and spi_cuentas_estructuras.codestpro4=spi_dt_cmp.codestpro4 ".
              " AND spi_cuentas_estructuras.codestpro5=spi_dt_cmp.codestpro5 and spi_cuentas_estructuras.estcla=spi_dt_cmp.estcla ".
			  " AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
			  " AND substr(cast(spi_dt_cmp.fecha as char(10)),1,10) <='".$adt_fecfin."'";
			 
	  //print $ls_sql; 
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spi_reporte
		                        M?TODO->uf_calcular_acumulado_operaciones_por_cuenta 
								ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_data))
		{
		  $ls_operacion=$row["operacion"];
		  $ld_monto=$row["monto"];
		  $ldt_fecha_db=$row["fecha"];
		  $ldt_fecha =substr($ldt_fecha_db,0,10);
		  $ldt_fecha =str_replace("-","",$ldt_fecha);
		  $ldt_mesdes=str_replace("-","",$adt_fecini);
		  $ldt_meshas=str_replace("-","",$adt_fecfin);
		  $ls_opera=$this->sigesp_int_spi->uf_operacion_codigo_mensaje($ls_operacion);
		  
		  $ls_mensaje=strtoupper($ls_opera); // devuelve cadena en MAYUSCULAS
		  $li_pos_i=strpos($ls_mensaje,"I"); 
		  if (!($li_pos_i===false)) 
		  { 
		    $ad_previsto=$ad_previsto+$ld_monto; 
		  }		
			  $li_pos_e=strpos($ls_mensaje,"E"); 
			  if (!($li_pos_e===false)) 
			  { 
				 $ad_devengado=$ad_devengado+$ld_monto;
			  }
			  $li_pos_c=strpos($ls_mensaje,"C"); 
			  if (!($li_pos_c===false)) 
			  {	
				 $ad_cobrado=$ad_cobrado+$ld_monto;
			  }
			  $li_pos_n=strpos($ls_mensaje,"N"); 
			  if (!($li_pos_n===false))
			  {	
			    $ad_cobrado_anticipado = $ad_cobrado_anticipado+$ld_monto; 
			  }
			  $li_pos_a=strpos($ls_mensaje,"A"); 
			  if (!($li_pos_a===false))
			  {	
			    $ad_aumento = $ad_aumento+$ld_monto; 
			  }
			  $li_pos_d=strpos($ls_mensaje,"D"); 
			  if (!($li_pos_d===false))
			  {	
			    $ad_disminucion = $ad_disminucion+$ld_monto;
			  }
	          $lb_valido = true;
		}//if 
	   $this->io_sql->free_result($rs_data);
	  }//else	
		$arrResultado['ad_previsto']=$ad_previsto;
		$arrResultado['ad_aumento']=$ad_aumento;
		$arrResultado['ad_disminucion']=$ad_disminucion;
		$arrResultado['ad_devengado']=$ad_devengado;
		$arrResultado['ad_cobrado']=$ad_cobrado;
		$arrResultado['ad_cobrado_anticipado']=$ad_cobrado_anticipado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin uf_calcular_acumulado_operaciones_por_cuenta 

/********************************************************************************************************************************/	
    /////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "LISTADO DE  CUENTAS  "           // 
	////////////////////////////////////////////////////////////
    function uf_spi_reporte_listado_cuentas($as_spi_cuentades,$as_spi_cuentahas,$as_sc_cuentades,$as_sc_cuentahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_apertura
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del listado de apertura  
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    27/09/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=false;
	  $this->dts_reporte->reset_ds();
	  if(($as_sc_cuentades!="")&&($as_sc_cuentahas!=""))
	  {
	    $ls_cadena=" AND scg_cuentas.sc_cuenta BETWEEN '".$as_sc_cuentades."' AND '".$as_sc_cuentahas."' ";
	  }
	  else
	  {
	    $ls_cadena="";
	  }
	  $ls_sql=" SELECT spi_cuentas.spi_cuenta,scg_cuentas.sc_cuenta,spi_cuentas.denominacion  ".
              " FROM   spi_cuentas, scg_cuentas ".
              " WHERE  spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."' AND ".
              "        spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta  ".$ls_cadena." ".
              " ORDER BY spi_cuentas.spi_cuenta"; //print $ls_sql;
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte
			  					  M?TODO->uf_spi_reporte_listado_cuentas 
								  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	 }
     else
	 {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
			   $ls_spi_cuenta=$row["spi_cuenta"]; 
			   $ls_sc_cuenta=$row["sc_cuenta"]; 
			   $ls_denominacion=$row["denominacion"];
			
               $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
               $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
               $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $lb_valido=true;			
			}
	 }
	  $this->io_sql->free_result($rs_data);	 
	  return $lb_valido;
}//fin uf_spg_reporte_apertura
/********************************************************************************************************************************/	
	/////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "MAYOR AN?LITICO DE CUENTAS" // 
	////////////////////////////////////////////////////////
	function uf_spi_reporte_mayor_analitico($adt_fecini,$adt_fecfin,$as_cuenta_from,$as_cuenta_to,$as_orden)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_mayor_analitico
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
	 //                     as_cuenta_from ... as_cuenta_to // de la cuenta de gasto ...hasta la cuenta de gasto
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels 
	 //                     ai_MenorNivel  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...) 
	 //     Creado por :    Ing.Yozelin Barragan
	 // Fecha Creaci?n :    28/09/2006          Fecha ?ltima Modificacion :10/11/2008        Hora :2:42 pm
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $lb_valido = false;	 
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $as_cuenta_from=trim($as_cuenta_from);
	  $as_cuenta_to=trim($as_cuenta_to);
	  $this->dts_reporte->reset_ds();
      if($as_orden=='F')
	  {
         $ls_ordenar=",TA.fecha";	  
	  }
	  elseif($as_orden=='D')
	  {
         $ls_ordenar=",TA.Documento";	  
	  }
	  
	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
		 $ls_cadena = "CONCAT(RTRIM(rpc_beneficiario.apebene),', ',rpc_beneficiario.nombene) ";
	  }
	  else
	  {
		 $ls_cadena = "RTRIM(rpc_beneficiario.apebene)||', '||rpc_beneficiario.nombene ";	  
	  }
	  if(($as_cuenta_from!="")&&($as_cuenta_to!=""))
	  {
	    $ls_cadena2=" AND trim(TA.spi_cuenta) BETWEEN  '".trim($as_cuenta_from)."'  AND  '".trim($as_cuenta_to)."' ";
	  }
	  else
	  {
	    $ls_cadena2="";
	  }
	  if ($ls_gestor=="INFORMIX")
	  {
	     $ls_cadena = "TRIM(rpc_beneficiario.apebene)||', '||rpc_beneficiario.nombene ";	
	     $ls_sql=" SELECT *  ".
		  	     " FROM table(MULTISET( SELECT spi_dt_cmp.* , spi_cuentas.denominacion,spi_dt_cmp.monto as monto_mov ".
			     "        FROM  spi_dt_cmp ,spi_operaciones ,spi_cuentas ".
			     "        WHERE spi_dt_cmp.codemp=spi_cuentas.codemp AND  spi_dt_cmp.codemp='".$this->ls_codemp."' AND ".
			     "              spi_dt_cmp.operacion=spi_operaciones.operacion AND spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta ".
			     "        ORDER BY spi_dt_cmp.spi_cuenta )) TA, ".
			     "  table(MULTISET(  SELECT DISTINCT sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.fecha,sigesp_cmp.descripcion, ".
			     "                        sigesp_cmp.total,sigesp_cmp.tipo_destino,sigesp_cmp.cod_pro,rpc_proveedor.nompro, ".
			     "                        sigesp_cmp.ced_bene, ".$ls_cadena." as nombene ".
			     "        FROM sigesp_cmp , spi_dt_cmp , rpc_proveedor , rpc_beneficiario ".
			     "        WHERE rpc_proveedor.cod_pro=sigesp_cmp.cod_pro AND rpc_beneficiario.ced_bene=sigesp_cmp.ced_bene AND ".
			     "              sigesp_cmp.codemp='".$this->ls_codemp."' AND sigesp_cmp.procede=spi_dt_cmp.procede AND  ".
			     "              sigesp_cmp.comprobante=spi_dt_cmp.comprobante AND sigesp_cmp.fecha=spi_dt_cmp.fecha )) TB ".
			     " WHERE TA.procede=TB.procede AND TA.comprobante=TB.comprobante AND TA.fecha=TB.fecha AND ".
                 "       TB.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ".$ls_cadena2." ".
                 " ORDER BY TA.spi_cuenta ".$ls_ordenar." ";  ////print $ls_sql;
	  }	
	  else
	  {
	     $ls_sql=" SELECT *  ".
		  	     " FROM ( SELECT spi_dt_cmp.* , spi_cuentas.denominacion,spi_dt_cmp.monto as monto_mov ".
			     "        FROM  spi_dt_cmp ,spi_operaciones ,spi_cuentas ".
			     "        WHERE spi_dt_cmp.codemp=spi_cuentas.codemp AND  spi_dt_cmp.codemp='".$this->ls_codemp."' AND ".
			     "              spi_dt_cmp.operacion=spi_operaciones.operacion AND spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta ".
			     "        ORDER BY spi_dt_cmp.spi_cuenta ) TA, ".
			     "      ( SELECT DISTINCT sigesp_cmp.codban,sigesp_cmp.ctaban,sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.fecha,sigesp_cmp.descripcion, ".
			     "                        sigesp_cmp.total,sigesp_cmp.tipo_destino,sigesp_cmp.cod_pro,rpc_proveedor.nompro, ".
			     "                        sigesp_cmp.ced_bene, ".$ls_cadena." as nombene ".
			     "        FROM sigesp_cmp , spi_dt_cmp , rpc_proveedor , rpc_beneficiario ".
			     "        WHERE rpc_proveedor.cod_pro=sigesp_cmp.cod_pro AND rpc_beneficiario.ced_bene=sigesp_cmp.ced_bene AND ".
			     "              sigesp_cmp.codemp='".$this->ls_codemp."' AND sigesp_cmp.procede=spi_dt_cmp.procede AND  ".
			     "              sigesp_cmp.comprobante=spi_dt_cmp.comprobante AND sigesp_cmp.fecha=spi_dt_cmp.fecha". 
	             "              AND sigesp_cmp.codban=spi_dt_cmp.codban AND sigesp_cmp.ctaban=spi_dt_cmp.ctaban ) TB ".
			     " WHERE TA.procede=TB.procede AND TA.comprobante=TB.comprobante AND TA.fecha=TB.fecha AND ".
	             "       TA.codban=TB.codban AND TA.ctaban=TB.ctaban AND ".
                 "       TB.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ".$ls_cadena2." ".
                 " ORDER BY TA.spi_cuenta ".$ls_ordenar." "; //print $ls_sql; 
	  }
	  $rs_mov_spg=$this->io_sql->select($ls_sql);
	  if($rs_mov_spg===false)
	  {   // error interno sql
		 $this->io_msg->message("CLASE->sigesp_spi_reporte
		                         M?TODO->uf_spi_reporte_mayor_analitico 
							 	 ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
          $lb_valido = false;	 
   	  }
	  else
	  {
		  $ldec_monto_previsto = 0;
		  $ldec_monto_aumento  = 0;		  
		  $ldec_monto_disminucion = 0;		 
		  $ldec_monto_devengado = 0;		 		   
		  $ldec_monto_cobrado = 0;		 		   		  
		  $ldec_monto_cobrado_anticipado = 0;		 		   		  		  
		  $ldec_monto_previsto_a = 0;
		  $ldec_monto_aumento_a  = 0;		  
		  $ldec_monto_disminucion_a = 0;	
		  $ldec_monto_devengado_a = 0;		 		   
		  $ldec_monto_cobrado_a = 0;		 		   		  
		  $ldec_monto_cobrado_anticipado_a = 0;		 		   		  		  
		  $ldec_monto_por_comprometer = 0;		 		   		  		  		  		  
		  $ls_cuenta_actual = "";		 		   		  		  		  		  
		  $ls_descripcion = "";
		  $lb_previo = false;
	  	  while($row=$this->io_sql->fetch_row($rs_mov_spg))
		  {
 	 	      $ls_spi_cuenta=trim($row["spi_cuenta"]);
			  $ls_denominacion=$row["denominacion"];
			  $ls_operacion=$row["operacion"];
			  $ldec_monto_operacion=$row["monto"];
			  $ls_procede=$row["procede"];
			  $ls_procede_doc=$row["procede_doc"];
			  $ls_comprobante=$row["comprobante"];			  
			  $ls_documento =$row["documento"];			   
			  $ls_descripcion =$row["descripcion"];			   
			  $ls_tipo_destino=$row["tipo_destino"];			 
			  $ls_nombene=$row["nombene"];			   
			  $ls_nompro=$row["nompro"];			
			  $ldt_fecha=$row["fecha"];
			  $ls_cod_pro=$row["cod_pro"];
			  $ls_nombre_prog=$row["descripcion"];
		      if ($ls_cuenta_actual!=$ls_spi_cuenta)
			  {
				  $ldec_monto_previsto_a = 0;
				  $ldec_monto_aumento_a  = 0;		  
				  $ldec_monto_disminucion_a = 0;	
				  $ldec_monto_devengado_a = 0;		 		   
				  $ldec_monto_cobrado_a = 0;		 		   		  
				  $ldec_monto_cobrado_anticipado_a = 0;		 		   		  		  
				  $ldec_monto_por_comprometer = 0;		
				  $lb_previo = true; 		   		  		  		  		  
				  $ls_cuenta_actual = $ls_spi_cuenta;
			  } 
			  $ldt_fecha_movimiento = $this->io_function->uf_convertirdatetobd($ldt_fecha);
			  $ldt_fecha_movimiento=substr($ldt_fecha_movimiento,0,10);
			  if ($ldt_fecha_movimiento < $adt_fecini )
			  {
				  $ldec_monto_previsto = 0;
				  $ldec_monto_aumento  = 0;		  
				  $ldec_monto_disminucion = 0;		 
				  $ldec_monto_devengado = 0;		 		   
				  $ldec_monto_cobrado = 0;		 		   		  
				  $ldec_monto_cobrado_anticipado = 0;		 		   		  		  
				  $arrResultado = $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_previsto,$ldec_monto_aumento,$ldec_monto_disminucion,
									                   $ldec_monto_devengado,$ldec_monto_cobrado,$ldec_monto_cobrado_anticipado);				  
				  $ldec_monto_previsto = $arrResultado['ad_previsto'];
				  $ldec_monto_aumento = $arrResultado['ad_aumento'];
				  $ldec_monto_disminucion = $arrResultado['ad_disminucion'];
				  $ldec_monto_devengado = $arrResultado['ad_devengado'];
				  $ldec_monto_cobrado = $arrResultado['ad_cobrado'];
				  $ldec_monto_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];

				  $ldec_monto_por_comprometer = $ldec_monto_por_comprometer+($ldec_monto_asignado+
				                                $ldec_monto_aumento-$ldec_monto_disminucion);		
				  
				  $ldec_monto_previsto_a = $ldec_monto_previsto_a+$ldec_monto_previsto;
				  $ldec_monto_aumento_a  = $ldec_monto_aumento_a+$ldec_monto_aumento;		  
				  $ldec_monto_disminucion_a = $ldec_monto_disminucion_a+$ldec_monto_disminucion;		 
				  $ldec_monto_devengado_a = $ldec_monto_devengado_a+$ldec_monto_devengado;		 		   
				  $ldec_monto_cobrado_a = $ldec_monto_cobrado_a+$ldec_monto_cobrado;		 		   		  
				  $ldec_monto_cobrado_anticipado_a = $ldec_monto_cobrado_anticipado_a+$ldec_monto_cobrado_anticipado;		 		   		  		  
			  } 
			  if (($ldt_fecha_movimiento >= $adt_fecini ) and ($ldt_fecha_movimiento <= $adt_fecfin) and 
			      ($ls_spi_cuenta>=$as_cuenta_from) and ($ls_spi_cuenta<=$as_cuenta_to))
			  {
				  $ldec_monto_previsto = 0;
				  $ldec_monto_aumento  = 0;		  
				  $ldec_monto_disminucion = 0;		 
				  $ldec_monto_devengado = 0;		 		   
				  $ldec_monto_cobrado = 0;		 		   		  
				  $ldec_monto_cobrado_anticipado = 0;		 		   		  		  
				  $arrResultado = $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_previsto,$ldec_monto_aumento,$ldec_monto_disminucion,
									                   				   $ldec_monto_devengado,$ldec_monto_cobrado,$ldec_monto_cobrado_anticipado);				  
				  $ldec_monto_previsto = $arrResultado['ad_previsto'];
				  $ldec_monto_aumento = $arrResultado['ad_aumento'];
				  $ldec_monto_disminucion = $arrResultado['ad_disminucion'];
				  $ldec_monto_devengado = $arrResultado['ad_devengado'];
				  $ldec_monto_cobrado = $arrResultado['ad_cobrado'];
				  $ldec_monto_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];
													   
				  if ($lb_previo==true)
				  {		
					 $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
					 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					 $this->dts_reporte->insertRow("fecha","");
					 $this->dts_reporte->insertRow("procede","");
					 $this->dts_reporte->insertRow("procede_doc","");
					 $this->dts_reporte->insertRow("comprobante","");
					 $this->dts_reporte->insertRow("documento","");
					 $this->dts_reporte->insertRow("descripcion",'SALDOS ANTERIORES');
					 $this->dts_reporte->insertRow("previsto",$ldec_monto_previsto_a);
					 $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento_a);
					 $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion_a);
					 $this->dts_reporte->insertRow("devengado",$ldec_monto_devengado_a);
					 $this->dts_reporte->insertRow("cobrado",$ldec_monto_cobrado_a);
                     $this->dts_reporte->insertRow("cobrado_anticipado",$ldec_monto_cobrado_anticipado_a);					 
					 $this->dts_reporte->insertRow("tipo_destino","");
					 $this->dts_reporte->insertRow("cod_pro","");
					 $this->dts_reporte->insertRow("nompro","");
					 $this->dts_reporte->insertRow("nombene","");
					 $this->dts_reporte->insertRow("operacion","");
					 $lb_previo=false;
			      }
				 $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
				 $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				 $this->dts_reporte->insertRow("fecha",$ldt_fecha_movimiento);
				 $this->dts_reporte->insertRow("procede",$ls_procede);
				 $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
				 $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				 $this->dts_reporte->insertRow("documento",$ls_documento);
				 $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
				 $this->dts_reporte->insertRow("previsto",$ldec_monto_previsto);
				 $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento);
				 $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion);
				 $this->dts_reporte->insertRow("devengado",$ldec_monto_devengado);
				 $this->dts_reporte->insertRow("cobrado",$ldec_monto_cobrado);
				 $this->dts_reporte->insertRow("cobrado_anticipado",$ldec_monto_cobrado_anticipado);					 
				 $this->dts_reporte->insertRow("tipo_destino","");
				 $this->dts_reporte->insertRow("cod_pro","");
				 $this->dts_reporte->insertRow("nompro","");
				 $this->dts_reporte->insertRow("nombene","");
				 $this->dts_reporte->insertRow("operacion",$ls_operacion);
			  }//if
	      }// fin while  
 	  }//else
	  $this->io_sql->free_result($rs_mov_spg);	 
	  return true;
    } // end function uf_spg_reporte_mayor_analitico
/********************************************************************************************************************************/	
	function uf_calcular_monto_operaciones($as_operacion,$adec_monto_operacion,$ad_previsto,$ad_aumento,
	                                       $ad_disminucion,$ad_devengado,$ad_cobrado,$ad_cobrado_anticipado)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_acumulado_operaciones->uf_spg_reporte_mayor_analitico
     //	    Returns :	Retorna campos calculados 
	 //	Description :	M?todo que mediante la operacion de gasto suma o resta los monto de las operaciones
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ld_monto=$adec_monto_operacion;
	  $ls_opera=$this->sigesp_int_spi->uf_operacion_codigo_mensaje($as_operacion);
	  $ls_mensaje=strtoupper($ls_opera); // devuelve cadena en MAYUSCULAS
	  $li_pos_i=strpos($ls_mensaje,"I"); 
	  if (!($li_pos_i===false)) 
	  { 
		$ad_previsto=$ad_previsto+$ld_monto; 
	  }
	  $li_pos_e=strpos($ls_mensaje,"E"); 
	  if (!($li_pos_e===false)) 
	  { 
		 $ad_devengado=$ad_devengado+$ld_monto;
	  }
	  $li_pos_c=strpos($ls_mensaje,"C"); 
	  if (!($li_pos_c===false)) 
	  {	
		 $ad_cobrado=$ad_cobrado+$ld_monto;
	  }
	  $li_pos_n=strpos($ls_mensaje,"N"); 
	  if (!($li_pos_n===false))
	  {	
		$ad_cobrado_anticipado = $ad_cobrado_anticipado+$ld_monto; 
	  }
	  $li_pos_a=strpos($ls_mensaje,"A"); 
	  if (!($li_pos_a===false))
	  {	
		$ad_aumento = $ad_aumento+$ld_monto; 
	  }
	  $li_pos_d=strpos($ls_mensaje,"D"); 
	  if (!($li_pos_d===false))
	  {	
		$ad_disminucion = $ad_disminucion+$ld_monto; 
	  }
		$arrResultado['ad_previsto']=$ad_previsto;
		$arrResultado['ad_aumento']=$ad_aumento;
		$arrResultado['ad_disminucion']=$ad_disminucion;
		$arrResultado['ad_devengado']=$ad_devengado;
		$arrResultado['ad_cobrado']=$ad_cobrado;
		$arrResultado['ad_cobrado_anticipado']=$ad_cobrado_anticipado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
    } // end uf_calcular_monto_operaciones
/********************************************************************************************************************************/	
	//////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "MODIFICACIONES PRESUPUESTARIAS " // 
	////////////////////////////////////////////////////////////
    function uf_spi_reporte_modificaciones_presupuestarias_aprobadas($ai_aumento,$ai_disminucion,$adt_fecini,$adt_fecfin,
	                                                                 $as_comprobante,$as_procede,$adt_fecha)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_modificaciones_presupuestarias_aprobadas
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso 
	 //	   Description :	Reporte que genera la salida para las modificaciones presupuestarias 
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    28/11/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_gestor=$_SESSION["ls_gestor"];
	  $this->dts_reporte->reset_ds();
      $ls_cad=$this->uf_spi_reporte_chequear_modificaciones($ai_aumento,$ai_disminucion);
      $ls_cadena=str_replace('procede','MOV.procede',$ls_cad);
	  if (empty($as_comprobante))
	  {
	    $ls_cadena_2="";
	  }
	  else
	  {
	    $ls_cadena_2=" AND CMP.comprobante='".$as_comprobante."' AND CMP.procede='".$as_procede."'  AND MOV.fecha='".$adt_fecha."' ";
	  }
	  if ($ls_gestor=="INFORMIX")
	  {
	    $ls_sql=" SELECT CMP.descripcion as cmp_descripcion, CMP.fecha as fecha, MOV.*, ".
                "        (monto-monto) as aumento, (monto-monto) as disminucion, ".
	            "        CTA.denominacion ".
			    " FROM   spi_dt_cmp MOV, sigesp_cmp CMP,spi_cuentas CTA ".
                " WHERE  CMP.codemp='".$this->ls_codemp."' AND CMP.codemp=MOV.codemp AND ".
                "        CMP.procede=MOV.procede AND CMP.comprobante=MOV.comprobante AND CMP.fecha=MOV.fecha AND ".
			    "        MOV.spi_cuenta = CTA.spi_cuenta AND (".$ls_cadena.")  AND ".
	            "        MOV.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND CMP.tipo_comp=2  ".$ls_cadena_2." ".
                " ORDER BY  MOV.comprobante ";//// print $ls_sql;
	 }
	 else
	 {
	    $ls_sql=" SELECT CMP.descripcion as cmp_descripcion, CMP.fecha as fecha, MOV.*, ".
                "        (monto-monto) as aumento, (monto-monto) as disminucion, ".
	            "        CTA.denominacion ".
			    " FROM   spi_dt_cmp MOV, sigesp_cmp CMP,spi_cuentas CTA ".
                " WHERE  CMP.codemp='".$this->ls_codemp."' AND CMP.codemp=MOV.codemp AND  MOV.codemp=CTA.codemp AND ".
                "        CMP.procede=MOV.procede AND CMP.comprobante=MOV.comprobante AND CMP.fecha=MOV.fecha AND ".
			    "        MOV.spi_cuenta = CTA.spi_cuenta AND (".$ls_cadena.")  AND ".
	            "        MOV.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND CMP.tipo_comp=2  ".$ls_cadena_2." ".
                " ORDER BY  MOV.comprobante "; ////print $ls_sql;
	 }
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte
		                          M?TODO->uf_spi_reporte_modificaciones_presupuestarias_aprobadas 
							  	  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	 } 
	 else 
	 {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				  $ls_cmp_descripcion=$row["cmp_descripcion"]; 
				  $ls_procede=$row["procede"]; 
				  $ls_comprobante=$row["comprobante"]; 
				  $ldt_fecha=$row["fecha"]; 
				  $ls_spi_cuenta=$row["spi_cuenta"]; 
				  $ls_procede_doc=$row["procede_doc"]; 
				  $ls_documento=$row["documento"]; 
				  $ls_operacion=$row["operacion"]; 
				  $ls_descripcion=$row["descripcion"]; 
				  $ld_monto=$row["monto"]; 
				  $ls_orden=$row["orden"]; 
				  $ld_aumento=$row["aumento"]; 
				  $ld_disminucion=$row["disminucion"];
				  $ls_denominacion=$row["denominacion"]; 
			      $ld_previsto = 0;
	              $ld_aumento = 0;
	              $ld_disminucion = 0;
	              $ld_devengado = 0;
	              $ld_cobrado_anticipado = 0;
				  
                  $arrResultado = $this->uf_calcular_monto_operaciones($ls_operacion,$ld_monto,$ld_previsto,$ld_aumento,$ld_disminucion,$ld_devengado,$ld_cobrado,$ld_cobrado_anticipado);			
				  $ld_previsto = $arrResultado['ad_previsto'];
				  $ld_aumento = $arrResultado['ad_aumento'];
				  $ld_disminucion = $arrResultado['ad_disminucion'];
				  $ld_devengado = $arrResultado['ad_devengado'];
				  $ld_cobrado = $arrResultado['ad_cobrado'];
				  $ld_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];
				  $ld_aumento=$ld_aumento;
				  $ld_disminucion=$ld_disminucion;
				 
				$this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				$this->dts_reporte->insertRow("cmp_descripcion",$ls_cmp_descripcion);	
				$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
				$this->dts_reporte->insertRow("documento",$ls_documento);			
				$this->dts_reporte->insertRow("fecha",$ldt_fecha);			
				$this->dts_reporte->insertRow("aumento",$ld_aumento);			
				$this->dts_reporte->insertRow("disminucion",$ld_disminucion);			
				$this->dts_reporte->insertRow("procede",$ls_procede);
			    $lb_valido=true;
			}//while
			$li_tot=$this->dts_reporte->getRowCount("spi_cuenta");
			if($li_tot==0)
			{$lb_valido=false;}
	  $this->io_sql->free_result($rs_data);	 
	 }//else
  return $lb_valido;
}//fin uf_spg_reporte_modificaciones_presupuestarias
/********************************************************************************************************************************/	
    function uf_spi_reporte_chequear_modificaciones( $ai_aumento,$ai_disminucion)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_chequear_modificaciones
	 //         Access :	private
	 //     Argumentos :    $ai_aumento   // chequear aumento
     //              	    $ai_disminucion // chequear disminucion
     //	       Returns :	Retorna una cadena con las opciones de las modificaciones presupuestarias seelccionadas 
	 //	   Description :	Verifica segun los parametros y construye una cadena para construir el reporte 
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    27/11/2006          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	 if($ai_aumento==1) { $ls_cadena1="procede ='SPIAUM' OR "; }
	 else{ $ls_cadena1="";}
	 if($ai_disminucion==1) { $ls_cadena2="procede ='SPIDIS' OR "; }
	 else{ $ls_cadena2="";}
	 $ls_cadena=$ls_cadena1.$ls_cadena2;
	 if(!empty($ls_cadena))
	 {
	   $ls_cadena=substr($ls_cadena,0,strlen($ls_cadena)-3);
	 }
	 return $ls_cadena;
   }//uf_spi_reporte_chequear_modificaciones
/********************************************************************************************************************************/	
    function uf_spi_reporte_modificaciones_presupuestarias_no_aprobadas($ai_aumento,$ai_disminucion,$adt_fecini,$adt_fecfin,
                                                                 	    $as_comprobante,$as_procede,$adt_fecha)
    {////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_modificaciones_presupuestarias_no_aprobadas
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
     //	       Returns :	Retorna true en caso de exito de la consulta o false en otro caso 
	 //	   Description :	Reporte que genera la salida para las modificaciones presupuestarias 
	 //     Creado por :    Ing. Yozelin Barrag?n.
	 // Fecha Creaci?n :    01/12/2006          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;
	  $ls_gestor= $_SESSION["ls_gestor"];
	  $this->dts_reporte->reset_ds();
      $ls_cad=$this->uf_spi_reporte_chequear_modificaciones($ai_aumento,$ai_disminucion);
      $ls_cadena=str_replace('procede','MOV.procede',$ls_cad);
	  if (empty($as_comprobante))
	  {
	    $ls_cadena_2="";
	  }
	  else
	  {
	    $ls_cadena_2=" AND CMP.comprobante='".$as_comprobante."' AND CMP.procede='".$as_procede."'  AND MOV.fecha='".$adt_fecha."' ";
	  }
	  if($ls_gestor=="INFORMIX")
	  {
    	  $ls_sql=" SELECT CMP.descripcion as cmp_descripcion, CMP.fecha as fecha, MOV.*, ".
                  "        (monto-monto) as aumento, (monto-monto) as disminucion, ".
	              "        CTA.denominacion ".
			      " FROM   spi_dtmp_cmp MOV, sigesp_cmp_md CMP,spi_cuentas CTA ".
                  " WHERE  CMP.codemp='".$this->ls_codemp."' AND CMP.codemp=MOV.codemp AND ".
                  "        CMP.procede=MOV.procede AND CMP.comprobante=MOV.comprobante AND CMP.fecha=MOV.fecha AND ".
			      "        MOV.spi_cuenta = CTA.spi_cuenta AND (".$ls_cadena.")  AND  CMP.tipo_comp=2 AND ".
	              "        MOV.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND  CMP.estapro=0  ".$ls_cadena_2."  ".
                  " ORDER BY  MOV.comprobante ";
	  }
	 else
	  {
    	  $ls_sql=" SELECT CMP.descripcion as cmp_descripcion, CMP.fecha as fecha, MOV.*, ".
                  "        (monto-monto) as aumento, (monto-monto) as disminucion, ".
	              "        CTA.denominacion ".
			      " FROM   spi_dtmp_cmp MOV, sigesp_cmp_md CMP,spi_cuentas CTA ".
                  " WHERE  CMP.codemp='".$this->ls_codemp."' AND CMP.codemp=MOV.codemp AND  MOV.codemp=CTA.codemp AND ".
                  "        CMP.procede=MOV.procede AND CMP.comprobante=MOV.comprobante AND CMP.fecha=MOV.fecha AND ".
			      "        MOV.spi_cuenta = CTA.spi_cuenta AND (".$ls_cadena.")  AND  CMP.tipo_comp=2 AND ".
	              "        MOV.fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND  CMP.estapro=0  ".$ls_cadena_2."  ".
                  " ORDER BY  MOV.comprobante ";
	  }
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte
		                          M?TODO->uf_spi_reporte_modificaciones_presupuestarias_no_aprobadas 
							  	  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	 } 
	 else 
	 {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				  $ls_cmp_descripcion=$row["cmp_descripcion"]; 
				  $ls_procede=$row["procede"]; 
				  $ls_comprobante=$row["comprobante"]; 
				  $ldt_fecha=$row["fecha"]; 
				  $ls_spi_cuenta=$row["spi_cuenta"]; 
				  $ls_procede_doc=$row["procede_doc"]; 
				  $ls_documento=$row["documento"]; 
				  $ls_operacion=$row["operacion"]; 
				  $ls_descripcion=$row["descripcion"]; 
				  $ld_monto=$row["monto"]; 
				  $ls_orden=$row["orden"]; 
				  $ld_aumento=$row["aumento"]; 
				  $ld_disminucion=$row["disminucion"];
				  $ls_denominacion=$row["denominacion"]; 
			      $ld_previsto = 0;
	              $ld_aumento = 0;
	              $ld_disminucion = 0;
	              $ld_devengado = 0;
	              $ld_cobrado_anticipado = 0;
				  
                  $arrResultado = $this->uf_calcular_monto_operaciones($ls_operacion,$ld_monto,$ld_previsto,$ld_aumento,$ld_disminucion,$ld_devengado,$ld_cobrado,$ld_cobrado_anticipado);			
				  $ld_previsto = $arrResultado['ad_previsto'];
				  $ld_aumento = $arrResultado['ad_aumento'];
				  $ld_disminucion = $arrResultado['ad_disminucion'];
				  $ld_devengado = $arrResultado['ad_devengado'];
				  $ld_cobrado = $arrResultado['ad_cobrado'];
				  $ld_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];
				  $ld_aumento=$ld_aumento;
				  $ld_disminucion=$ld_disminucion;
				 
				$this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				$this->dts_reporte->insertRow("cmp_descripcion",$ls_cmp_descripcion);	
				$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
				$this->dts_reporte->insertRow("documento",$ls_documento);			
				$this->dts_reporte->insertRow("fecha",$ldt_fecha);			
				$this->dts_reporte->insertRow("aumento",$ld_aumento);			
				$this->dts_reporte->insertRow("disminucion",$ld_disminucion);			
				$this->dts_reporte->insertRow("procede",$ls_procede);
			    $lb_valido=true;
			}//while
			$li_tot=$this->dts_reporte->getRowCount("spi_cuenta");
			if($li_tot==0)
			{$lb_valido=false;}
	  $this->io_sql->free_result($rs_data);	 
	 }//else
  return $lb_valido;
}//fin uf_spi_reporte_modificaciones_presupuestarias_no_aprobadas
/********************************************************************************************************************************/	
	/////////////////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "ACUMULADO POR CUENTAS con estructuras presupuestarias"   // 
	/////////////////////////////////////////////////////////////////////////////////////
    function uf_spi_reporte_acumulado_cuentas2($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                          $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											  $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas,$adt_fecini,
											  $adt_fecfin,$ai_nivel,$ab_subniveles,$ai_MenorNivel,$ls_modalidad,$as_spi_cuentades,
											  $as_spi_cuentahas,$as_saldocero)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_acumulado_cuentas2
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
	 //                     ab_subniveles  // variable boolean si lo desea con subnivels 
	 //                     ai_MenorNivel  // variabvle que determina el menor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...) 
	 //     Creado por :    Ing. Yozelin Barragan 
	 // Fecha Creaci?n :    27/09/2006         Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_existe = false;	 
		$lb_valido = false;
		$lb_ok = true;
	    $ab_subniveles=true;
		$ls_gestor = $_SESSION["ls_gestor"];
		$this->dts_reporte->reset_ds();
		$ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
		$ls_estructura_dstino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
		if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		{
			$ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)";
		}
		else
		{
			$ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
		}
		
		$dts_cuentas=new class_datastore();
    	/*$ls_sql=" SELECT spi_cuentas.spi_cuenta,spi_cuentas.denominacion,spi_cuentas.nivel,spi_cuentas.status,spi_cuentas_estructuras.previsto".
                " FROM  spi_cuentas_estructuras,spi_cuentas".
                " WHERE spi_cuentas_estructuras.codemp='".$this->ls_codemp."' ".
				" AND spi_cuentas_estructuras.spi_cuenta=spi_cuentas.spi_cuenta ".
				" AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
                " ORDER BY spi_cuentas_estructuras.spi_cuenta ";*/
				
		//print $ls_sql."<br>";
		$ls_sql = "SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status,spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,3) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
				AND   spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."' 
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,3) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 1
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status, spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,5) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,5) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 2
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status, spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,7) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,7) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 3
				AND spi_cuentas.spi_cuenta NOT IN (SELECT spi_cuentas_estructuras.spi_cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1)
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status, spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."' 
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' 
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,9) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 4
				AND spi_cuentas.spi_cuenta NOT IN (SELECT spi_cuentas_estructuras.spi_cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1)
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT spi_cuentas_estructuras.spi_cuenta as cuenta, MAX(spi_cuentas.denominacion) as denominacion, MAX(spi_cuentas.status) as status, MAX(spi_cuentas.nivel) as nivel 
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND   spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'
				AND spi_cuentas.nivel <= ".$ai_nivel." 
				GROUP BY 1
				ORDER BY 1";
		$rs_cuentas=$this->io_sql->select($ls_sql);
		if($rs_cuentas===false)
		{   //error interno sql
			$this->io_msg->message("CLASE->sigesp_spi_reporte
									M?TODO->uf_spg_reporte_acumulado_cuentas 
									ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
           $lb_valido = false;
		}
		else
        {
		   if($row=$this->io_sql->fetch_row($rs_cuentas))
		   {
              $dts_cuentas->data=$this->io_sql->obtener_datos($rs_cuentas);
              $lb_existe=true;
           }
		   $this->io_sql->free_result($rs_cuentas);   
           if($lb_existe==false)
           {
              //return false; // no hay registro
			  $lb_ok=false;
		   }
		   if($lb_ok)
		   {
           //$li_total_row=$dts_cuentas->getRowCount("spi_cuenta");			
		   $li_total_row=$dts_cuentas->getRowCount("cuenta");			
		   for ($li_i=1;$li_i<=$li_total_row;$li_i++)
  	       {   
			   $lb_si_va = false;
			   //$ls_spi_cuenta = $dts_cuentas->getValue("spi_cuenta",$li_i);
			   $ls_spi_cuenta = $dts_cuentas->getValue("cuenta",$li_i);
			   $ls_denominacion = $dts_cuentas->getValue("denominacion",$li_i);
			   $li_nivel = $dts_cuentas->getValue("nivel",$li_i);
			   $ls_status = $dts_cuentas->getValue("status",$li_i);
			   //$ls_previsto = $dts_cuentas->getValue("previsto",$li_i);
			   $ls_previsto = 0;
			   if ($ai_nivel=$li_nivel)
			   {
		          $lb_si_va = true;
			   }
			   if ( $ab_subniveles and ($ai_nivel<=$li_nivel) )
			   {  
			      $lb_si_va = true;
			   }
			   //$lb_si_va = true;
			   if ($lb_si_va==true)
			   {
		      	  if ($li_nivel < $ai_MenorNivel) { $ai_MenorNivel = $li_nivel; }
				  // Calculo lo Ejecutado y acumulado
				    $ld_monto_aumento=0;
				    $ld_monto_disminucion=0;
				    $ld_monto_devengado=0;
				    $ld_monto_cobrado=0;
				    $ld_cobrado_anticipado=0;
					$ld_previsto=0;
					$arrResultado = $this->uf_calcular_acumulado_operaciones_por_cuenta2($adt_fecini,$adt_fecfin,$ld_previsto,
					                                                         $ld_monto_aumento,$ld_monto_disminucion,
																			 $ld_monto_devengado,$ld_monto_cobrado,
														                     $ld_cobrado_anticipado,$ls_spi_cuenta,
																			 $as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
																			  $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
																			  $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas);
					$ld_previsto=$arrResultado['ad_previsto'];
					$ld_monto_aumento=$arrResultado['ad_aumento'];
					$ld_monto_disminucion=$arrResultado['ad_disminucion'];
					$ld_monto_devengado=$arrResultado['ad_devengado'];
					$ld_monto_cobrado=$arrResultado['ad_cobrado'];
					$ld_cobrado_anticipado=$arrResultado['ad_cobrado_anticipado'];
					$lb_valido = $arrResultado['lb_valido'];
					if (!$lb_valido)
					{
					   return false; 
					} 
					$ab_agregar=true;
					if ($as_saldocero=='1')
					{
						if(($ld_previsto==0)&&($ld_monto_aumento==0)&&($ld_monto_disminucion==0)&&($ld_monto_devengado==0)&&($ld_monto_cobrado==0)&&($ld_cobrado_anticipado==0))
						{
							$ab_agregar=false;
						}
					}
					if ($ab_agregar)
					{
						$ll_row_found = $this->dts_reporte->find("spi_cuenta",$ls_spi_cuenta);
						if ($ll_row_found == 0)
						{  
							$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
							$this->dts_reporte->insertRow("denominacion",$ls_denominacion);							
							$this->dts_reporte->insertRow("nivel",$li_nivel);							
							$this->dts_reporte->insertRow("previsto",$ld_previsto);
							$this->dts_reporte->insertRow("aumento",$ld_monto_aumento);							
							$this->dts_reporte->insertRow("disminucion",$ld_monto_disminucion);
							$this->dts_reporte->insertRow("devengado",$ld_monto_devengado);
							$this->dts_reporte->insertRow("cobrado",$ld_monto_cobrado);							
							$this->dts_reporte->insertRow("cobrado_anticipado",$ld_cobrado_anticipado);							
							$this->dts_reporte->insertRow("status",$ls_status);		
							$lb_valido = true;
						
						} 
						else
						{
							$ldec_monto = $this->dts_reporte->getValue("previsto",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_previsto;
							$this->dts_reporte->updateRow("asignado",$ldec_monto,$ll_row_found);	
							$ldec_monto = $this->dts_reporte->getValue("aumento",$ll_row_found );
							$ldec_monto = $ldec_monto + $ldec_monto_aumento;
							$this->dts_reporte->updateRow("aumento",$ldec_monto,$ll_row_found);						
							$ldec_monto = $this->dts_reporte->getValue("disminucion",$ll_row_found );
							$ldec_monto = $ldec_monto + $ldec_monto_disminucion;						
							$this->dts_reporte->updateRow("disminucion",$ldec_monto,$ll_row_found);
							$ldec_monto = $this->dts_reporte->getValue("devengado",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_monto_devengado;																						
							$this->dts_reporte->updateRow("devengado",$ldec_monto,$ll_row_found);
							$ldec_monto = $this->dts_reporte->getValue("cobrado",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_monto_cobrado;																								
							$this->dts_reporte->updateRow("cobrado",$ldec_monto,$ll_row_found);							
							$ldec_monto = $this->dts_reporte->getValue("cobrado_anticipado",$ll_row_found );
							$ldec_monto = $ldec_monto + $ld_cobrado_anticipado;									
							$this->dts_reporte->updateRow("cobrado_anticipado",$ldec_monto,$ll_row_found);	
							$this->dts_reporte->updateRow("status",$ls_status,$ll_row_found);		
							$lb_valido = true;
						}// else
					}
		   } // end if 
		 } // end for
	   }//if($lb_ok)	
	 } //else
		$arrResultado['ai_MenorNivel']=$ai_MenorNivel;
		$arrResultado['lb_valido']=$lb_valido;
	 	return $arrResultado;
   } // fin function uf_spg_reporte_acumulado_cuentas
/********************************************************************************************************************************/	
	/////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "MAYOR AN?LITICO DE CUENTAS"  // 
	//   manejando estructuras presupuestarias             //
	////////////////////////////////////////////////////////
	function uf_spi_reporte_mayor_analitico2($adt_fecini,$adt_fecfin,$as_cuenta_from,$as_cuenta_to,$as_orden,
	                                        $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
	                                        $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
	                                        $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //	     Function :	uf_spi_reporte_mayor_analitico2
    //         Access :	private
    //     Argumentos :    adt_fecini  				// fecha  desde 
    //              	   adt_fecfin  				// fecha hasta 
    //                     as_cuenta_from ... as_cuenta_to 	// de la cuenta de gasto ...hasta la cuenta de gasto
    //                     ab_subniveles  			// variable boolean si lo desea con subnivels 
    //                     ai_MenorNivel  			// variable que determina el mor nivel de la cuenta
    //	      Returns :	Retorna estructuras ordenadas para la consulta sql
    //	  Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...) 
    //     Creado por : Ing.Yozelin Barragan
    // Fecha Creaci?n : 28/09/2006          Fecha ?ltima Modificacion :10/11/2008        Hora :2:42 pm
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ini_set('memory_limit','1024M');
      $lb_valido = true;	 
      $ls_gestor = $_SESSION["ls_gestor"];
      $this->dts_reporte->reset_ds();
      $ls_estructura_origen=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
      $ls_estructura_dstino=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;
      if($as_orden=='F')
	  {
         $ls_ordenar=",TA.fecha";	  
	  }
	  elseif($as_orden=='D')
	  {
         $ls_ordenar=",TA.Documento";	  
	  }

	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
		 $ls_cadena = "CONCAT(RTRIM(rpc_beneficiario.apebene),', ',rpc_beneficiario.nombene) ";
		 $ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)";
	  }
	  else
	  {
		 $ls_cadena = "RTRIM(rpc_beneficiario.apebene)||', '||rpc_beneficiario.nombene ";
		 $ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
	  }
	  if(($as_cuenta_from!="")&&($as_cuenta_to!=""))
	  {
	    $as_cuenta_from=trim($as_cuenta_from);
		$as_cuenta_to=trim($as_cuenta_to);
	    $ls_cadena2=" AND trim(TA.spi_cuenta) BETWEEN  '".$as_cuenta_from."'  AND  '".$as_cuenta_to."' ";
	  }
	  else
	  {
	    $ls_cadena2="";
	  }
	  if ($ls_gestor=="INFORMIX")
	  {
	     $ls_cadena = "TRIM(rpc_beneficiario.apebene)||', '||rpc_beneficiario.nombene ";	
	     $ls_sql=" SELECT *  ".
		  	     " FROM table(MULTISET( SELECT spi_dt_cmp.* , spi_cuentas.denominacion,spi_dt_cmp.monto as monto_mov ".
			     "        FROM  spi_dt_cmp ,spi_operaciones ,spi_cuentas ".
			     "        WHERE spi_dt_cmp.codemp=spi_cuentas.codemp AND  spi_dt_cmp.codemp='".$this->ls_codemp."' AND ".
			     "              spi_dt_cmp.operacion=spi_operaciones.operacion AND spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta ".
			     "        ORDER BY spi_dt_cmp.spi_cuenta )) TA, ".
			     "  table(MULTISET(  SELECT DISTINCT sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.fecha,sigesp_cmp.descripcion, ".
			     "                        sigesp_cmp.total,sigesp_cmp.tipo_destino,sigesp_cmp.cod_pro,rpc_proveedor.nompro, ".
			     "                        sigesp_cmp.ced_bene, ".$ls_cadena." as nombene ".
			     "        FROM sigesp_cmp , spi_dt_cmp , rpc_proveedor , rpc_beneficiario ".
			     "        WHERE rpc_proveedor.cod_pro=sigesp_cmp.cod_pro AND rpc_beneficiario.ced_bene=sigesp_cmp.ced_bene AND ".
			     "              sigesp_cmp.codemp='".$this->ls_codemp."' AND sigesp_cmp.procede=spi_dt_cmp.procede AND  ".
			     "              sigesp_cmp.comprobante=spi_dt_cmp.comprobante AND sigesp_cmp.fecha=spi_dt_cmp.fecha )) TB ".
			     " WHERE TA.procede=TB.procede AND TA.comprobante=TB.comprobante AND TA.fecha=TB.fecha AND ".
                 "       TB.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ".$ls_cadena2." ".
                 " ORDER BY TA.spi_cuenta ".$ls_ordenar." ";
	  }	
	  else
	  {
	     $ls_sql=" SELECT *  ".
		  	     " FROM ( SELECT spi_dt_cmp.* , spi_cuentas.denominacion,spi_dt_cmp.monto as monto_mov ".
			     "        FROM  spi_dt_cmp ,spi_operaciones ,spi_cuentas,spi_cuentas_estructuras  ".
			     "        WHERE spi_dt_cmp.codemp=spi_cuentas.codemp AND  spi_dt_cmp.codemp='".$this->ls_codemp."' AND ".
			     "              spi_dt_cmp.operacion=spi_operaciones.operacion AND spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta ".
				 "              AND spi_cuentas_estructuras.codemp=spi_dt_cmp.codemp ". 
                 "              AND spi_cuentas_estructuras.codestpro1=spi_dt_cmp.codestpro1 and spi_cuentas_estructuras.codestpro2=spi_dt_cmp.codestpro2 ".
                 "              AND spi_cuentas_estructuras.codestpro3=spi_dt_cmp.codestpro3 and spi_cuentas_estructuras.codestpro4=spi_dt_cmp.codestpro4 ".
                 "              AND spi_cuentas_estructuras.codestpro5=spi_dt_cmp.codestpro5 and spi_cuentas_estructuras.estcla=spi_dt_cmp.estcla ".
                 "              AND spi_cuentas_estructuras.spi_cuenta=spi_dt_cmp.spi_cuenta ".
				 "              AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
                 "              AND spi_cuentas_estructuras.codestpro1<>'-------------------------' and spi_cuentas_estructuras.codestpro2<>'-------------------------' ".
                 "              AND spi_cuentas_estructuras.codestpro3<>'-------------------------' and spi_cuentas_estructuras.codestpro4<>'-------------------------'".
                 "              AND spi_cuentas_estructuras.codestpro5<>'-------------------------' and spi_cuentas_estructuras.estcla<>'-'".
			     "        ORDER BY spi_dt_cmp.spi_cuenta ) TA, ".
			     "      ( SELECT DISTINCT sigesp_cmp.procede,sigesp_cmp.comprobante,sigesp_cmp.fecha,sigesp_cmp.descripcion, ".
			     "                        sigesp_cmp.total,sigesp_cmp.tipo_destino,sigesp_cmp.cod_pro,rpc_proveedor.nompro, sigesp_cmp.codban, sigesp_cmp.ctaban, ".
			     "                        sigesp_cmp.ced_bene, ".$ls_cadena." as nombene ".
			     "        FROM sigesp_cmp , spi_dt_cmp , rpc_proveedor , rpc_beneficiario".
			     "        WHERE rpc_proveedor.cod_pro=sigesp_cmp.cod_pro AND rpc_beneficiario.ced_bene=sigesp_cmp.ced_bene AND ".
			     "              sigesp_cmp.codemp='".$this->ls_codemp."' AND sigesp_cmp.procede=spi_dt_cmp.procede AND  ".
			     "              sigesp_cmp.comprobante=spi_dt_cmp.comprobante AND sigesp_cmp.fecha=spi_dt_cmp.fecha) TB ".
			     " WHERE TA.procede=TB.procede AND TA.comprobante=TB.comprobante AND TA.fecha=TB.fecha AND  TA.codban=TB.codban AND TA.ctaban=TB.ctaban AND".
                 "       TB.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ".$ls_cadena2." ".
                 " ORDER BY TA.spi_cuenta ".$ls_ordenar." "; 
				 
	  }	
	  $rs_mov_spi=$this->io_sql->select($ls_sql); 	  
	  if($rs_mov_spi===false)
	  {   // error interno sql
		 $this->io_msg->message("CLASE->sigesp_spi_reporte M?TODO->uf_spi_reporte_mayor_analitico2 ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
          $lb_valido = false;	
   	  }
	  else
	  {
		  $ldec_monto_previsto = 0;
		  $ldec_monto_aumento  = 0;		  
		  $ldec_monto_disminucion = 0;		 
		  $ldec_monto_devengado = 0;		 		   
		  $ldec_monto_cobrado = 0;		 		   		  
		  $ldec_monto_cobrado_anticipado = 0;		 		   		  		  
		  $ldec_monto_previsto_a = 0;
		  $ldec_monto_aumento_a  = 0;		  
		  $ldec_monto_disminucion_a = 0;	
		  $ldec_monto_devengado_a = 0;		 		   
		  $ldec_monto_cobrado_a = 0;		 		   		  
		  $ldec_monto_cobrado_anticipado_a = 0;		 		   		  		  
		  $ldec_monto_por_comprometer = 0;		 		   		  		  		  		  
		  $ls_cuenta_actual = "";		 		   		  		  		  		  
		  $ls_descripcion = "";
		  $lb_previo = false;
		  
	  	  while($row=$this->io_sql->fetch_row($rs_mov_spi))
		  { 
 	 	      $ls_spi_cuenta=$row["spi_cuenta"]; 
			  $ls_denominacion=$row["denominacion"];
			  //print "ls_spi_cuenta: $ls_spi_cuenta , ls_denominacion: $ls_denominacion  <br>";
			  $ls_operacion=$row["operacion"];
			  $ldec_monto_operacion=$row["monto"];
			  $ls_procede=$row["procede"];
			  $ls_procede_doc=$row["procede_doc"];
			  $ls_comprobante=$row["comprobante"];			  
			  $ls_documento =$row["documento"];			   
			  $ls_descripcion =$row["descripcion"];			   
			  $ls_tipo_destino=$row["tipo_destino"];			 
			  $ls_nombene=$row["nombene"];			   
			  $ls_nompro=$row["nompro"];			
			  $ldt_fecha=$row["fecha"];
			  $ls_cod_pro=$row["cod_pro"];
			  $ls_nombre_prog=$row["descripcion"];
			  $ls_estcla=$row["estcla"];
			  $ls_codestpro1=$row["codestpro1"];
			  $ls_codestpro2=$row["codestpro2"];
			  $ls_codestpro3=$row["codestpro3"];
			  $ls_codestpro4=$row["codestpro4"];
			  $ls_codestpro5=$row["codestpro5"];
		      if (trim($ls_cuenta_actual)!=trim($ls_spi_cuenta))
			  { 
				  $ldec_monto_previsto_a = 0;
				  $ldec_monto_aumento_a  = 0;		  
				  $ldec_monto_disminucion_a = 0;	
				  $ldec_monto_devengado_a = 0;		 		   
				  $ldec_monto_cobrado_a = 0;		 		   		  
				  $ldec_monto_cobrado_anticipado_a = 0;		 		   		  		  
				  $ldec_monto_por_comprometer = 0;		
				  $lb_previo = true; 		   		  		  		  		  
				  $ls_cuenta_actual = $ls_spi_cuenta;
			  } 
			  $ldt_fecha_movimiento = $this->io_function->uf_convertirdatetobd($ldt_fecha);
			  $ldt_fecha_movimiento=substr($ldt_fecha_movimiento,0,10);
			  $ldt_fecha_movimiento=trim($ldt_fecha_movimiento);
			  $adt_fecini=trim($adt_fecini);
			  $adt_fecfin=trim($adt_fecfin);
			  $ls_spi_cuenta=trim($ls_spi_cuenta);
			  $as_cuenta_from=trim($as_cuenta_from);
			  $as_cuenta_to=trim($as_cuenta_to);
			  if ($ldt_fecha_movimiento < $adt_fecini )
			  { //print "entro aqui 2 "."<br>";
				  $ldec_monto_previsto = 0;
				  $ldec_monto_aumento  = 0;		  
				  $ldec_monto_disminucion = 0;		 
				  $ldec_monto_devengado = 0;		 		   
				  $ldec_monto_cobrado = 0;		 		   		  
				  $ldec_monto_cobrado_anticipado = 0;		 		   		  		  
				  $arrResultado = $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_previsto, $ldec_monto_aumento,$ldec_monto_disminucion,
									                  				 $ldec_monto_devengado,$ldec_monto_cobrado,$ldec_monto_cobrado_anticipado);				  
				  $ldec_monto_previsto = $arrResultado['ad_previsto'];
				  $ldec_monto_aumento = $arrResultado['ad_aumento'];
				  $ldec_monto_disminucion = $arrResultado['ad_disminucion'];
				  $ldec_monto_devengado = $arrResultado['ad_devengado'];
				  $ldec_monto_cobrado = $arrResultado['ad_cobrado'];
				  $ldec_monto_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];

				  $ldec_monto_por_comprometer = $ldec_monto_por_comprometer+($ldec_monto_asignado+
				                                $ldec_monto_aumento-$ldec_monto_disminucion);		
				  
				  $ldec_monto_previsto_a = $ldec_monto_previsto_a+$ldec_monto_previsto;
				  $ldec_monto_aumento_a  = $ldec_monto_aumento_a+$ldec_monto_aumento;		  
				  $ldec_monto_disminucion_a = $ldec_monto_disminucion_a+$ldec_monto_disminucion;		 
				  $ldec_monto_devengado_a = $ldec_monto_devengado_a+$ldec_monto_devengado;		 		   
				  $ldec_monto_cobrado_a = $ldec_monto_cobrado_a+$ldec_monto_cobrado;		 		   		  
				  $ldec_monto_cobrado_anticipado_a = $ldec_monto_cobrado_anticipado_a+$ldec_monto_cobrado_anticipado;		 		   		  		  
			  } 
			  if (($ldt_fecha_movimiento >= $adt_fecini ) and ($ldt_fecha_movimiento <= $adt_fecfin) and 
			      ($ls_spi_cuenta>=$as_cuenta_from) and ($ls_spi_cuenta<=$as_cuenta_to))
			  { 
				  $ldec_monto_previsto = 0;
				  $ldec_monto_aumento  = 0;		  
				  $ldec_monto_disminucion = 0;		 
				  $ldec_monto_devengado = 0;		 		   
				  $ldec_monto_cobrado = 0;		 		   		  
				  $ldec_monto_cobrado_anticipado = 0;		 		   		  		  
				  $arrResultado = $this->uf_calcular_monto_operaciones($ls_operacion,$ldec_monto_operacion,$ldec_monto_previsto, $ldec_monto_aumento,$ldec_monto_disminucion,
									                   $ldec_monto_devengado,$ldec_monto_cobrado,$ldec_monto_cobrado_anticipado);				  
				  $ldec_monto_previsto = $arrResultado['ad_previsto'];
				  $ldec_monto_aumento = $arrResultado['ad_aumento'];
				  $ldec_monto_disminucion = $arrResultado['ad_disminucion'];
				  $ldec_monto_devengado = $arrResultado['ad_devengado'];
				  $ldec_monto_cobrado = $arrResultado['ad_cobrado'];
				  $ldec_monto_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];
				  
				    $ldec_posicion=$this->dts_reporte->getRowCount("spi_cuenta");
				  
				    if ($lb_previo==true)
				    {
					    $ldec_posicion++;
					    
					    $this->dts_reporte->data["spi_cuenta"][$ldec_posicion] = $ls_spi_cuenta;
					    $this->dts_reporte->data["denominacion"][$ldec_posicion] = $ls_denominacion;
					    $this->dts_reporte->data["fecha"][$ldec_posicion] = "";
					    $this->dts_reporte->data["procede"][$ldec_posicion] = "";
					    $this->dts_reporte->data["procede_doc"][$ldec_posicion] = "";
					    $this->dts_reporte->data["comprobante"][$ldec_posicion] = "";
					    $this->dts_reporte->data["documento"][$ldec_posicion] = "";
					    $this->dts_reporte->data["descripcion"][$ldec_posicion] = 'SALDOS ANTERIORES';
					    $this->dts_reporte->data["previsto"][$ldec_posicion] = $ldec_monto_previsto_a;
					    $this->dts_reporte->data["aumento"][$ldec_posicion] = $ldec_monto_aumento_a;
					    $this->dts_reporte->data["disminucion"][$ldec_posicion] = $ldec_monto_disminucion_a;
					    $this->dts_reporte->data["devengado"][$ldec_posicion] = $ldec_monto_devengado_a;
					    $this->dts_reporte->data["cobrado"][$ldec_posicion] = $ldec_monto_cobrado_a;
					    $this->dts_reporte->data["cobrado_anticipado"][$ldec_posicion] = $ldec_monto_cobrado_anticipado_a;
					    $this->dts_reporte->data["tipo_destino"][$ldec_posicion] = "";
					    $this->dts_reporte->data["cod_pro"][$ldec_posicion] = "";
					    $this->dts_reporte->data["nompro"][$ldec_posicion] = "";
					    $this->dts_reporte->data["nombene"][$ldec_posicion] = "";
					    $this->dts_reporte->data["operacion"][$ldec_posicion] = "";
					    $this->dts_reporte->data["estcla"][$ldec_posicion] = "";
					    $this->dts_reporte->data["codestpro1"][$ldec_posicion] = $ls_codestpro1;
					    $this->dts_reporte->data["codestpro2"][$ldec_posicion] = $ls_codestpro2;
					    $this->dts_reporte->data["codestpro3"][$ldec_posicion] = $ls_codestpro3;
					    $this->dts_reporte->data["codestpro4"][$ldec_posicion] = $ls_codestpro4;
					    $this->dts_reporte->data["codestpro5"][$ldec_posicion] = $ls_codestpro5;
					    
					    /*$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
					    $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					    $this->dts_reporte->insertRow("fecha","");
					    $this->dts_reporte->insertRow("procede","");
					    $this->dts_reporte->insertRow("procede_doc","");
					    $this->dts_reporte->insertRow("comprobante","");
					    $this->dts_reporte->insertRow("documento","");
					    $this->dts_reporte->insertRow("descripcion",'SALDOS ANTERIORES');
					    $this->dts_reporte->insertRow("previsto",$ldec_monto_previsto_a);
					    $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento_a);
					    $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion_a);
					    $this->dts_reporte->insertRow("devengado",$ldec_monto_devengado_a);
					    $this->dts_reporte->insertRow("cobrado",$ldec_monto_cobrado_a);
					    $this->dts_reporte->insertRow("cobrado_anticipado",$ldec_monto_cobrado_anticipado_a);					 
					    $this->dts_reporte->insertRow("tipo_destino","");
					    $this->dts_reporte->insertRow("cod_pro","");
					    $this->dts_reporte->insertRow("nompro","");
					    $this->dts_reporte->insertRow("nombene","");
					    $this->dts_reporte->insertRow("operacion","");
					    $this->dts_reporte->insertRow("estcla",$ls_estcla);
					    $this->dts_reporte->insertRow("codestpro1",$ls_codestpro1);
					    $this->dts_reporte->insertRow("codestpro2",$ls_codestpro2);
					    $this->dts_reporte->insertRow("codestpro3",$ls_codestpro3);
					    $this->dts_reporte->insertRow("codestpro4",$ls_codestpro4);
					    $this->dts_reporte->insertRow("codestpro5",$ls_codestpro5);*/
					    $lb_previo=false;
				    }
				    $ldec_posicion++;
				    
				    $this->dts_reporte->data["spi_cuenta"][$ldec_posicion] = $ls_spi_cuenta;
				    $this->dts_reporte->data["denominacion"][$ldec_posicion] = $ls_denominacion;
				    $this->dts_reporte->data["fecha"][$ldec_posicion] = $ldt_fecha_movimiento;
				    $this->dts_reporte->data["procede"][$ldec_posicion] = $ls_procede;
				    $this->dts_reporte->data["procede_doc"][$ldec_posicion] = $ls_procede_doc;
				    $this->dts_reporte->data["comprobante"][$ldec_posicion] = $ls_comprobante;
				    $this->dts_reporte->data["documento"][$ldec_posicion] = $ls_documento;
				    $this->dts_reporte->data["descripcion"][$ldec_posicion] = $ls_descripcion;
				    $this->dts_reporte->data["previsto"][$ldec_posicion] = $ldec_monto_previsto;
				    $this->dts_reporte->data["aumento"][$ldec_posicion] = $ldec_monto_aumento;
				    $this->dts_reporte->data["disminucion"][$ldec_posicion] = $ldec_monto_disminucion;
				    $this->dts_reporte->data["devengado"][$ldec_posicion] = $ldec_monto_devengado;
				    $this->dts_reporte->data["cobrado"][$ldec_posicion] = $ldec_monto_cobrado;
				    $this->dts_reporte->data["cobrado_anticipado"][$ldec_posicion] = $ldec_monto_cobrado_anticipado;
				    $this->dts_reporte->data["tipo_destino"][$ldec_posicion] = "";
				    $this->dts_reporte->data["cod_pro"][$ldec_posicion] = "";
				    $this->dts_reporte->data["nompro"][$ldec_posicion] = "";
				    $this->dts_reporte->data["nombene"][$ldec_posicion] = "";
				    $this->dts_reporte->data["operacion"][$ldec_posicion] = $ls_operacion;
				    $this->dts_reporte->data["estcla"][$ldec_posicion] = $ls_estcla;
				    $this->dts_reporte->data["codestpro1"][$ldec_posicion] = $ls_codestpro1;
				    $this->dts_reporte->data["codestpro2"][$ldec_posicion] = $ls_codestpro2;
				    $this->dts_reporte->data["codestpro3"][$ldec_posicion] = $ls_codestpro3;
				    $this->dts_reporte->data["codestpro4"][$ldec_posicion] = $ls_codestpro4;
				    $this->dts_reporte->data["codestpro5"][$ldec_posicion] = $ls_codestpro5;
				    
				    /*$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
				    $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				    $this->dts_reporte->insertRow("fecha",$ldt_fecha_movimiento);
				    $this->dts_reporte->insertRow("procede",$ls_procede);
				    $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
				    $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
				    $this->dts_reporte->insertRow("documento",$ls_documento);
				    $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
				    $this->dts_reporte->insertRow("previsto",$ldec_monto_previsto);
				    $this->dts_reporte->insertRow("aumento",$ldec_monto_aumento);  
				    $this->dts_reporte->insertRow("disminucion",$ldec_monto_disminucion);
				    $this->dts_reporte->insertRow("devengado",$ldec_monto_devengado);
				    $this->dts_reporte->insertRow("cobrado",$ldec_monto_cobrado);
				    $this->dts_reporte->insertRow("cobrado_anticipado",$ldec_monto_cobrado_anticipado);					 
				    $this->dts_reporte->insertRow("tipo_destino","");
				    $this->dts_reporte->insertRow("cod_pro","");
				    $this->dts_reporte->insertRow("nompro","");
				    $this->dts_reporte->insertRow("nombene","");
				    $this->dts_reporte->insertRow("operacion",$ls_operacion);
				    $this->dts_reporte->insertRow("estcla",$ls_estcla);
				    $this->dts_reporte->insertRow("codestpro1",$ls_codestpro1);
				    $this->dts_reporte->insertRow("codestpro2",$ls_codestpro2);
				    $this->dts_reporte->insertRow("codestpro3",$ls_codestpro3);
				    $this->dts_reporte->insertRow("codestpro4",$ls_codestpro4);
				    $this->dts_reporte->insertRow("codestpro5",$ls_codestpro5);*/
				
			  }//if
			  $lb_valido=true;
	      }// fin while
 	  }//else
	  //print_r($this->dts_reporte);
	  //$this->io_sql->free_result($rs_mov_spi); 
	  return $lb_valido;
    } // end function uf_spg_reporte_mayor_analitico2
/********************************************************************************************************************************/
	/////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "LISTADO DE APERTURAS DE CUENTAS DE INGRESO " //
	//   CON MANEJO DE ESTRUCTURAS PRESUPUESTARIAS                         // 
	////////////////////////////////////////////////////////////////////////
    function uf_spi_reporte_apertura2($adt_fecini,$adt_fecfin,$as_cuentades,$as_cuentahas,
	                                  $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
									  $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
									  $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_apertura2
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del listado de apertura  
	 //     Creado por :    Ing. Gloriely Fr?itez.
	 // Fecha Creaci?n :    11/11/2008          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=false;
	  $this->dts_reporte->reset_ds();
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_estructura_origen=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
      $ls_estructura_dstino=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;	

	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5.spi_cuentas_estructuras.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
	  }


	  $ls_sql=" SELECT  spi_dt_cmp.*, spi_cuentas.denominacion , spi_cuentas_estructuras.previsto as monto_estructura ".
              " FROM    spi_dt_cmp, spi_cuentas, spi_cuentas_estructuras ".
              " WHERE   spi_dt_cmp.codemp=spi_cuentas.codemp AND spi_cuentas.codemp='".$this->ls_codemp."' ".
			  "         AND spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta ".
			  "         AND spi_cuentas_estructuras.codemp=spi_dt_cmp.codemp ".
			  "	        AND spi_cuentas_estructuras.codestpro1=spi_dt_cmp.codestpro1 ".
			  "	        AND spi_cuentas_estructuras.codestpro2=spi_dt_cmp.codestpro2 ".
			  "	        AND spi_cuentas_estructuras.codestpro3=spi_dt_cmp.codestpro3 ".
			  "	        AND spi_cuentas_estructuras.codestpro4=spi_dt_cmp.codestpro4 ".
			  "         AND spi_cuentas_estructuras.codestpro5=spi_dt_cmp.codestpro5 ".
			  "         AND spi_cuentas_estructuras.estcla=spi_dt_cmp.estcla ".
			  "         AND spi_cuentas_estructuras.spi_cuenta=spi_dt_cmp.spi_cuenta ".
			  "         AND spi_dt_cmp.spi_cuenta=spi_cuentas.spi_cuenta ".
			  "	        AND spi_cuentas_estructuras.codestpro1<>'-------------------------' ".
			  "         AND spi_cuentas_estructuras.codestpro2<>'-------------------------' ".
			  "         AND spi_cuentas_estructuras.codestpro3<>'-------------------------' ".
			  "         AND spi_cuentas_estructuras.codestpro4<>'-------------------------' ".
			  "         AND spi_cuentas_estructuras.codestpro5<>'-------------------------' ".
			  "         AND spi_cuentas_estructuras.estcla<>'-' ".
			  "         AND spi_cuentas_estructuras.spi_cuenta<>'-----------' ".
			  "         AND spi_dt_cmp.procede='SPIAPR' ".
              "         AND spi_cuentas.spi_cuenta BETWEEN '".$as_cuentades."' AND '".$as_cuentahas."'".
			  "         AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
              "         AND spi_dt_cmp.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ".
              " ORDER  BY spi_dt_cmp.spi_cuenta "; //print $ls_sql."<br><br>"; 
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
		 /* $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte
			  					  M?TODO->uf_spi_reporte_apertura2 
								  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));*/
	 }
     else
	 {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
			   $ls_codemp=$row["codemp"]; 
			   $ls_procede=$row["procede"]; 
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_spi_cuenta=$row["spi_cuenta"]; 
			   $ls_procede_doc=$row["procede_doc"]; 
			   $ls_documento=$row["documento"]; 
			   $ls_operacion=$row["operacion"]; 
			   $ls_descripcion=$row["descripcion"];
			   $ld_monto=$row["monto_estructura"]; 
			   $ls_orden=$row["orden"]; 
			   $ls_denominacion=$row["denominacion"];
			
               $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
               $this->dts_reporte->insertRow("denominacion",$ls_denominacion);			
               $this->dts_reporte->insertRow("descripcion",$ls_descripcion);			
	           $this->dts_reporte->insertRow("documento",$ls_documento);			
			   $this->dts_reporte->insertRow("monto",$ld_monto);
	  		   $lb_valido=true;
			}
	 } 
	  $this->io_sql->free_result($rs_data);	 
	  return $lb_valido;
}//fin uf_spg_reporte_apertura2
/********************************************************************************************************************************/	
    ////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPG  "LISTADO DE  CUENTAS  "          //
	//   MANEJANDO ESTRUCTURA PRESUPUESTARIA                  // 
	////////////////////////////////////////////////////////////
    function uf_spi_reporte_listado_cuentas2($as_spi_cuentades,$as_spi_cuentahas,$as_sc_cuentades,$as_sc_cuentahas,
	                                         $ls_codestpro1,$ls_codestpro2,$ls_codestpro3,$ls_codestpro4,
											 $ls_codestpro5,$ls_codestpro1h,$ls_codestpro2h,$ls_codestpro3h,
											 $ls_codestpro4h,$ls_codestpro5h,$ls_estclades,$ls_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_listado_cuentas2
	 //         Access :	private
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del listado de apertura  
	 //     Creado por :    Ing. Gloriely Fr?itez
	 // Fecha Creaci?n :    11/11/2008          Fecha ?ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=false;
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $this->dts_reporte->reset_ds();
	  $ls_estructura_origen=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
      $ls_estructura_dstino=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;	
	  if(($as_sc_cuentades!="")&&($as_sc_cuentahas!=""))
	  {
	    $ls_cadena=" AND scg_cuentas.sc_cuenta BETWEEN '".$as_sc_cuentades."' AND '".$as_sc_cuentahas."' ";
	  }
	  else
	  {
	    $ls_cadena="";
	  }
	  
	  if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	  {
	     $ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5.spi_cuentas_estructuras.estcla)";
	  }
	  else
	  {
	     $ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
	  }

	  $ls_sql=" SELECT DISTINCT (spi_cuentas.spi_cuenta),scg_cuentas.sc_cuenta,spi_cuentas.denominacion  ".
              " FROM   spi_cuentas, scg_cuentas,spi_cuentas_estructuras, spg_ep5 ".
			  " WHERE  spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'  ".
			  " AND spi_cuentas_estructuras.codestpro2<>'-------------------------' ".
			  " AND spi_cuentas_estructuras.codestpro3<>'-------------------------' ".
			  " AND spi_cuentas_estructuras.codestpro4<>'-------------------------' ".
			  " AND spi_cuentas_estructuras.codestpro5<>'-------------------------' ".
			  " AND spi_cuentas_estructuras.estcla<>'-' ".
			  " AND spi_cuentas_estructuras.spi_cuenta<>'-----------' ".
              " AND spi_cuentas_estructuras.codemp=spg_ep5.codemp ".
			  "	AND spi_cuentas_estructuras.codestpro1=spg_ep5.codestpro1 ".
			  "	AND spi_cuentas_estructuras.codestpro2=spg_ep5.codestpro2 ".
			  "	AND spi_cuentas_estructuras.codestpro3=spg_ep5.codestpro3 ".
			  "	AND spi_cuentas_estructuras.codestpro4=spg_ep5.codestpro4 ".
			  " AND spi_cuentas_estructuras.codestpro5=spg_ep5.codestpro5 ".
			  " AND spi_cuentas_estructuras.estcla=spg_ep5.estcla ".
			  "	AND spi_cuentas_estructuras.codestpro1<>'-------------------------' ".
			  " AND spi_cuentas_estructuras.spi_cuenta=spi_cuentas.spi_cuenta".
			  " AND spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta  ".$ls_cadena." ".
			  " AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
              " ORDER BY spi_cuentas.spi_cuenta"; 
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte
			  					  M?TODO->uf_spi_reporte_listado_cuentas2
								  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	 }
     else
	 {
			while($row=$this->io_sql->fetch_row($rs_data))
			{
			   $ls_spi_cuenta=$row["spi_cuenta"]; 
			   $ls_sc_cuenta=$row["sc_cuenta"]; 
			   $ls_denominacion=$row["denominacion"];
               $this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
               $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
               $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $lb_valido=true;			
			}
	 }
	  $this->io_sql->free_result($rs_data);	 
	  return $lb_valido;
}//fin uf_spg_reporte_apertura2

/********************************************************************************************************************************/	



	

	function uf_spi_reporte_detalle_acumulado_cuentas($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
	                                              $as_codestpro5,$as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,
	                                              $as_codestpro5h,$as_estclades,$as_estclahas,
												  $as_spg_cuenta,$adt_fecfin,$j,$as_saldocero)
					  
	{
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//       Function :	uf_spg_reporte_detalle_acumulado_cuentas
	//         Access :	private
	//     Argumentos :     as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	//                      as_spg_cuenta   // cuenta presupestaria
	//        Returns :	Retorna estructuras ordenadas para la consulta sql
	//    Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...) 
	//     Creado por :     Ing. Mar?a Beatriz Unda
	// Fecha Creaci?n :     13/08/08          		Fecha ?ltima Modificacion :
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
	$lb_valido = true;
	if (($_SESSION["ls_gestor"]=="MYSQLT") || (strtoupper($_SESSION["ls_gestor"])=="MYSQLI"))
	{
		$criterio=" AND (CONCAT(MV.codestpro1,MV.codestpro2,MV.codestpro3,MV.codestpro4,MV.codestpro5,MV.estcla) 
		BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades' 
					AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
	}
	if ($_SESSION["ls_gestor"]=="POSTGRES")
	{
		$criterio=" AND (MV.codestpro1||MV.codestpro2||MV.codestpro3||MV.codestpro4||MV.codestpro5||MV.estcla 
		BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades' 
					AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
	}
				
	$as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
				
        $ls_sql=	" SELECT MV.spi_cuenta,  ".
                  	" CASE MV.operacion  ".
					" WHEN 'PRE' THEN sum(MV.monto) ".
					" END as previsto, ".			
					" CASE MV.operacion ".
					" WHEN 'AU' THEN sum(MV.monto) ".
					" END as aumento, ".			
					" CASE MV.operacion".
					" WHEN 'DI' THEN sum(MV.monto) ".
					" END as disminucion, ".
					" CASE MV.operacion ".
					" WHEN 'DEV' THEN sum(MV.monto) ".
					" WHEN 'DC' THEN sum(MV.monto) ".
					" END as devengado, ".
					" CASE MV.operacion ".
					" WHEN 'DC' THEN sum(MV.monto) ".
        			" WHEN 'COB' THEN sum(MV.monto) ".
					" END as cobrado".
                  	" FROM spi_dt_cmp as MV ".
					" WHERE MV.spi_cuenta LIKE '$as_spg_cuenta'".
					" AND MV.fecha <='$adt_fecfin'".$criterio.						
					" GROUP BY MV.spi_cuenta, MV.operacion".
                  	" ORDER BY MV.spi_cuenta "; 
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false)
	{
		$lb_valido=false;
		$this->io_msg->message("CLASE->sigesp_spi_reporte M?TODO->uf_spi_reporte_detalle_acumulado_cuentas ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	}
	else
	{
		$ld_saumento=0;
		$ld_sdisminucion=0;
		$ld_sdevengado=0;
		$ld_scobrado=0;
		$li_totfila=$this->io_sql->num_rows($rs_data);
		if($li_totfila>0)
		{
			while($row=$this->io_sql->fetch_row($rs_data))
			{	
			    $ld_saumento	= $ld_saumento+$row["aumento"];
			    $ld_sdisminucion	= $ld_sdisminucion+$row["disminucion"];
			    $ld_sdevengado	= $ld_sdevengado+$row["devengado"];
			    $ld_scobrado	= $ld_scobrado+$row["cobrado"];
			    $lb_valido=true;			
			}
			$ab_agregar=true;
			if ($as_saldocero=='1')
			{
				if(($ld_saumento==0)&&($ld_sdisminucion==0)&&($ld_sdevengado==0)&&($ld_scobrado==0))
				{
					$ab_agregar=false;
				}
			}
			if ($ab_agregar)
			{
				$this->dts_reporte->data["aumento"][$j]		= $ld_saumento;
				$this->dts_reporte->data["disminucion"][$j]	= $ld_sdisminucion;
				$this->dts_reporte->data["devengado"][$j]	= $ld_sdevengado;
				$this->dts_reporte->data["cobrado"][$j]		= $ld_scobrado;
			}
			
		}
		else
		{
			$lb_valido=true;	
		}
	}
	$this->io_sql->free_result($rs_data);	 		
	return $lb_valido;
   } // fin uf_spg_reporte_detalle_acumulado_cuentas

    function uf_spi_reporte_acum_cuentas2($as_spi_cuentades,$as_spi_cuentahas,$ls_codestpro1,$ls_codestpro2,
	                                      $ls_codestpro3,$ls_codestpro4,$ls_codestpro5,$ls_codestpro1h,
										  $ls_codestpro2h,$ls_codestpro3h, $ls_codestpro4h,$ls_codestpro5h,
										  $ls_estclades,$ls_estclahas,$ai_nivel)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //	      Function :	uf_spi_reporte_listado_cuentas2
    //          Access :	private
    //      Argumentos :    	adt_fecini  // fecha  desde 
    //              	    	adt_fecfin  // fecha hasta 
    //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
    //	   Description :	Reporte que genera salida  del listado de apertura  
    //      Creado por :    	Ing. Gloriely Fr?itez
    //  Fecha Creaci?n :    	11/11/2008          Fecha ?ltima Modificacion :      Hora :
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido=false;
	$ls_gestor = $_SESSION["ls_gestor"];
	$this->dts_reporte->reset_ds();
	$ls_estructura_origen=$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estclades;
	$ls_estructura_dstino=$ls_codestpro1h.$ls_codestpro2h.$ls_codestpro3h.$ls_codestpro4h.$ls_codestpro5h.$ls_estclahas;
	
	if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	{
	    $ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5.spi_cuentas_estructuras.estcla)";
	}
	else
	{
	    $ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
	}
	$ls_sql = "SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status,spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,3) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
				AND   spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."' 
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,3) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 1
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status, spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,5) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,5) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 2
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status, spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,7) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,7) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 3
				AND spi_cuentas.spi_cuenta NOT IN (SELECT spi_cuentas_estructuras.spi_cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1)
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT DISTINCT spi_cuentas.spi_cuenta as cuenta, spi_cuentas.denominacion, spi_cuentas.status, spi_cuentas.nivel  FROM spi_cuentas, (SELECT SUBSTR(spi_cuentas_estructuras.spi_cuenta,1,9) as cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta 
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."' 
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' 
				GROUP BY 1
				ORDER BY 1) AS CUENTAESTRUCTURA
				WHERE SUBSTR(spi_cuentas.spi_cuenta,1,9) = CUENTAESTRUCTURA.cuenta AND spi_cuentas.nivel = 4
				AND spi_cuentas.spi_cuenta NOT IN (SELECT spi_cuentas_estructuras.spi_cuenta
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'  
				GROUP BY 1)
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND spi_cuentas.nivel <= ".$ai_nivel."
				UNION
				SELECT spi_cuentas_estructuras.spi_cuenta as cuenta, MAX(spi_cuentas.denominacion) as denominacion, MAX(spi_cuentas.status) as status, MAX(spi_cuentas.nivel) as nivel 
				FROM spi_cuentas, spi_cuentas_estructuras, spg_ep5 
				WHERE spi_cuentas_estructuras.codemp     = spg_ep5.codemp 
				AND   spi_cuentas_estructuras.codestpro1 = spg_ep5.codestpro1 
				AND   spi_cuentas_estructuras.codestpro2 = spg_ep5.codestpro2 
				AND   spi_cuentas_estructuras.codestpro3 = spg_ep5.codestpro3 
				AND   spi_cuentas_estructuras.codestpro4 = spg_ep5.codestpro4 
				AND   spi_cuentas_estructuras.codestpro5 = spg_ep5.codestpro5 
				AND   spi_cuentas_estructuras.estcla     = spg_ep5.estcla 
				AND   spi_cuentas_estructuras.spi_cuenta = spi_cuentas.spi_cuenta  
				AND   spi_cuentas_estructuras.codemp = '".$this->dts_empresa["codemp"]."'
				AND spi_cuentas.spi_cuenta BETWEEN '".$as_spi_cuentades."' AND '".$as_spi_cuentahas."'
				AND ".$ls_str_sql_1." BETWEEN '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."'
				AND spi_cuentas.nivel <= ".$ai_nivel." 
				GROUP BY 1
				ORDER BY 1";
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false)
	{
		  $lb_valido=false;
		  $this->io_msg->message("CLASE->sigesp_spi_reporte M?TODO->uf_spi_reporte_listado_cuentas2 ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	}
	else
	{
		while(!$rs_data->EOF)
		{
			$ls_spi_cuenta=$rs_data->fields["cuenta"]; 
			$ls_denominacion=$rs_data->fields["denominacion"];
			$ld_previsto = 0;
			$arrResultado = $this->uf_spi_calcular_previsto_estructura($ls_spi_cuenta,$ls_estructura_origen,$ls_estructura_dstino,$ld_previsto);
			$ld_previsto = $arrResultado['ad_previsto'];
			$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);			
			$this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			$this->dts_reporte->insertRow("status",$rs_data->fields["status"]);
			$this->dts_reporte->insertRow("nivel",$rs_data->fields["nivel"]);
			$this->dts_reporte->insertRow("previsto",$ld_previsto);
			$this->dts_reporte->insertRow("aumento",0);
			$this->dts_reporte->insertRow("disminucion",0);
			$this->dts_reporte->insertRow("devengado",0);
			$this->dts_reporte->insertRow("cobrado",0);
			$lb_valido=true;
			$rs_data->MoveNext();			
		}
	}
	
	$this->io_sql->free_result($rs_data);	 
	return $lb_valido;
}//fin uf_spg_reporte_apertura2

/********************************************************************************************************************************/	

























//------------------------------------------------------------------------------------------------------------------------------------
    function select_estructuras ($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                             $as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,$as_codestpro5h,                                 $as_estclades,$as_estclahas)
	{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//	      Function :	select_estructuras
	//         Access :	private
	//     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	//                     as_spg_cuenta   // cuenta presupestaria
	//	  Returns :	
	//	scription :	
	//     Creado por :    Ing. Jennifer Rivero
	// Fecha Creaci?n :    17/11/08          Fecha ?ltima Modificacion :
  	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	     $lb_valido=true;
	     $criterio="";
		 $ls_programatica = "";
		 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		 
		 if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		 {
			$criterio=" AND (CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) 
							BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades' 
						AND         '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		 }
		 if ($_SESSION["ls_gestor"]=="POSTGRES")
		 {
			$criterio=" AND (codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla  BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades' 
					    AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		 }
		 
		$ls_sql="  SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla ".
		        "   FROM  spg_ep5 ".
				"   WHERE codemp='".$ls_codemp."'".$criterio; //print $ls_sql."<br>";
		 $rs_data=$this->io_sql->select($ls_sql);
		 if($rs_data===false)
		 {   // error interno sql
				 $this->io_msg->message("CLASE->sigesp_spi_reporte
			  					  M?TODO->select_estructuras
								  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
				$lb_valido = false;	
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
				 $ls_estcla=$row["estcla"];
				 $ls_programatica = trim($ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla);
				 $this->data_est->insertRow("programatica",$ls_programatica);	
				 $this->data_est->insertRow("codestpro1",  $ls_codestpro1);	
				 $this->data_est->insertRow("codestpro2",  $ls_codestpro2);
				 $this->data_est->insertRow("codestpro3",  $ls_codestpro3);
				 $this->data_est->insertRow("codestpro4",  $ls_codestpro4);
				 $this->data_est->insertRow("codestpro5",  $ls_codestpro5);
				 $this->data_est->insertRow("estcla",  $ls_estcla);
			
			}//fin del while
			
			$this->io_sql->free_result($rs_data);	 
		 }//fin del else
		 return $lb_valido;
	}//fin de la funcion
//------------------------------------------------------------------------------------------------------------------------------------- 
    function uf_spg_reporte_select_denestpro1($as_codestpro1,$as_denestpro1,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Jennifer rivero.
	 // Fecha Creaci?n :    17/11/2007          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro1 ".
             " FROM   spg_ep1 ".
             " WHERE  codemp='".$ls_codemp."' AND codestpro1='".$as_codestpro1."' AND estcla='".$as_estcla."' ";
			
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro1 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_denestpro1=$row["denestpro1"];
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	 $arrResultado['as_denestpro']=$as_denestpro1;
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
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        estcla='".$as_estcla."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro2 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_denestpro2=$row["denestpro2"];
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	 $arrResultado['as_denestpro']=$as_denestpro2;
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
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro3 ".
             " FROM   spg_ep3 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        codestpro3='".$as_codestpro3."' AND ".
			 "        estcla='".$as_estcla."' ";
			
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro3 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_denestpro3=$row["denestpro3"];
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	 $arrResultado['as_denestpro']=$as_denestpro3;
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
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro4 ".
             " FROM   spg_ep4 ".
             " WHERE  codemp='".$ls_codemp."' AND  ".
			 "        codestpro1='".$as_codestpro1."' AND ".
			 "        codestpro2='".$as_codestpro2."' AND ".
			 "        codestpro3='".$as_codestpro3."' AND ".
			 "        codestpro4='".$as_codestpro4."' AND ".
			 "        estcla='".$as_estcla."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro4 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_denestpro4=$row["denestpro4"];
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	 $arrResultado['as_denestpro']=$as_denestpro4;
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
	 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
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
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro5 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_denestpro5=$row["denestpro5"];
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	 $arrResultado['as_denestpro']=$as_denestpro5;
	 $arrResultado['lb_valido']=$lb_valido;
	 return $arrResultado;		
  }//uf_spg_reporte_select_denestpro5
  
  
 //------------------------------------------------------------------------------------------------------------------------------------
    function select_estructuras_spi ($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,
	                             $as_codestpro1h,$as_codestpro2h,$as_codestpro3h,$as_codestpro4h,$as_codestpro5h,$as_estclades,$as_estclahas)
	{
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	select_estructuras
	 //         Access :	private
	 //     Argumentos :    as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria origen
	 //                     as_spg_cuenta   // cuenta presupestaria
     //	       Returns :	
	 //	   Description :	
	 //     Creado por :    Ing. Jennifer Rivero
	 // Fecha Creaci?n :    17/11/08          Fecha ?ltima Modificacion :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	     $lb_valido=true;
	     $criterio="";
		 $criterio2="";
		 $ls_programatica = "";
		 $ls_codemp=$_SESSION["la_empresa"]["codemp"];
		 
		 if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		 {
			$criterio=" AND (CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) 
							BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades' 
						AND         '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		 }
		 if ($_SESSION["ls_gestor"]=="POSTGRES")
		 {
			$criterio=" AND (codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla  BETWEEN '$as_codestpro1$as_codestpro2$as_codestpro3$as_codestpro4$as_codestpro5$as_estclades' 
					    AND     '$as_codestpro1h$as_codestpro2h$as_codestpro3h$as_codestpro4h$as_codestpro5h$as_estclahas') ";
		 }
		 
		 if (($_SESSION["ls_gestor"]=="MYSQLT") || ($_SESSION["ls_gestor"]=="MYSQLI"))
		 {
			$criterio2=" AND (CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla) 
							IN (SELECT CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla)
							    FROM spi_cuentas_estructuras
								WHERE codemp='".$ls_codemp."'))";
		 }
		 if ($_SESSION["ls_gestor"]=="POSTGRES")
		 {
			$criterio2=" AND (codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla)
			                IN (SELECT codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla
							    FROM spi_cuentas_estructuras
								WHERE codemp='".$ls_codemp."')
						 AND (codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla)
						   IN (SELECT codestpro1||codestpro2||codestpro3||codestpro4||codestpro5||estcla 
						   	  FROM spi_dt_cmp WHERE codemp='".$ls_codemp."') ";
		 }
		 
		$ls_sql="  SELECT codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla ".
		        "   FROM  spg_ep5 ".
				"   WHERE codemp='".$ls_codemp."'".$criterio.$criterio2;
		 $rs_data=$this->io_sql->select($ls_sql);
		 if($rs_data===false)
		 {   // error interno sql
				 $this->io_msg->message("CLASE->sigesp_spi_reporte
			  					  M?TODO->select_estructuras
								  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
				$lb_valido = false;	
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
				 $ls_estcla=$row["estcla"];
				 $ls_programatica = trim($ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla);
				 $this->data_est->insertRow("programatica",$ls_programatica);	
				 $this->data_est->insertRow("codestpro1",  $ls_codestpro1);	
				 $this->data_est->insertRow("codestpro2",  $ls_codestpro2);
				 $this->data_est->insertRow("codestpro3",  $ls_codestpro3);
				 $this->data_est->insertRow("codestpro4",  $ls_codestpro4);
				 $this->data_est->insertRow("codestpro5",  $ls_codestpro5);
				 $this->data_est->insertRow("estcla",  $ls_estcla);
			
			}//fin del while
			$this->io_sql->free_result($rs_data);	 
		 }//fin del else
		 return $lb_valido;
	}//fin de la funcion
	
	
//------------------------------------------------------------------------------------------------------------------------------------- 
    function uf_spg_reporte_select_denestpro_global($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_denestpro1,$as_denestpro2,$as_denestpro3,$as_denestpro4,$as_denestpro5,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //	      Function :	uf_spg_reporte_select_denestpro_global
    //          Access :	private
    //      Argumentos :    	$as_procede_ori  // procede origen
    //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
    //	   Description :	devuelve la descripcion de la cada componente de la estructura programatica que se solicite
    //      Creado por :    Ing. Jennifer rivero.
    //  Fecha Creaci?n :    07/09/2009          Fecha ?ltima Modificacion :      Hora :
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$lb_valido=true;
	$ls_sql= "SELECT 
		    spg_ep1.denestpro1, 
		    spg_ep2.denestpro2, 
		    spg_ep3.denestpro3, 
		    spg_ep4.denestpro4, 
		    spg_ep5.denestpro5
		  FROM 
		    spg_ep1, 
		    spg_ep2, 
		    spg_ep3, 
		    spg_ep4, 
		    spg_ep5
		  WHERE spg_ep1.codemp = '$ls_codemp ' AND spg_ep1.estcla = '$as_estcla' AND
		    spg_ep1.codemp = spg_ep2.codemp AND
		    spg_ep1.codestpro1 = '$as_codestpro1' AND
		    spg_ep1.codestpro1 = spg_ep2.codestpro1 AND
		    spg_ep1.estcla = spg_ep2.estcla AND
		    spg_ep2.codemp = spg_ep3.codemp AND
		    spg_ep2.codestpro1 = spg_ep3.codestpro1 AND
		    spg_ep2.codestpro2 = spg_ep3.codestpro2 AND
		    spg_ep2.estcla = spg_ep3.estcla AND
		    spg_ep3.codestpro1 = '$as_codestpro1' AND
		    spg_ep3.codestpro2 = '$as_codestpro2' AND
		    spg_ep3.codestpro3 = '$as_codestpro3' AND
		    spg_ep3.codemp = spg_ep4.codemp AND
		    spg_ep3.codestpro1 = spg_ep4.codestpro1 AND
		    spg_ep3.codestpro2 = spg_ep4.codestpro2 AND
		    spg_ep3.codestpro3 = spg_ep4.codestpro3 AND
		    spg_ep3.estcla = spg_ep4.estcla AND
		    spg_ep4.codestpro1 = spg_ep5.codestpro1 AND
		    spg_ep4.codestpro2 = spg_ep5.codestpro2 AND
		    spg_ep4.codestpro3 = spg_ep5.codestpro3 AND
		    spg_ep4.codestpro4 = spg_ep5.codestpro4 AND
		    spg_ep4.estcla = spg_ep5.estcla";
	$rs_data=$this->io_sql->select($ls_sql); //print "$ls_sql <br><br><br>";
	if($rs_data===false)
	{   	// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro1 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	}
	else
	{
		if($row=$this->io_sql->fetch_row($rs_data))
		{
			$as_denestpro1=$row["denestpro1"];
			$as_denestpro2=$row["denestpro2"];
			$as_denestpro3=$row["denestpro3"];
			$as_denestpro4=$row["denestpro4"];
			$as_denestpro5=$row["denestpro5"];
		}
		$this->io_sql->free_result($rs_data);
	}//else
	$arrResultado['as_denestpro1']=$as_denestpro1;
	$arrResultado['as_denestpro2']=$as_denestpro2;
	$arrResultado['as_denestpro3']=$as_denestpro3;
	$arrResultado['as_denestpro4']=$as_denestpro4;
	$arrResultado['as_denestpro5']=$as_denestpro5;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
  }//uf_spg_reporte_select_denestpro1	
//------------------------------------------------------------------------------------------------------------------------------------- 
//------------------------------------------------------------------------------------------------------------------------------------- 
    function uf_spg_reporte_select_estpro_blanco($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_topbottom,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //	      Function :	uf_spg_reporte_select_denestpro_global
    //          Access :	private
    //      Argumentos :    	$as_procede_ori  // procede origen
    //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
    //	   Description :	devuelve la descripcion de la cada componente de la estructura programatica que se solicite
    //      Creado por :    Ing. Jennifer rivero.
    //  Fecha Creaci?n :    07/09/2009          Fecha ?ltima Modificacion :      Hora :
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$ls_codemp=$_SESSION["la_empresa"]["codemp"];
	$lb_valido=true;
	
	switch($as_topbottom)
	{
		case 'TOP'	: $ls_cab_sql = ' MIN(curTabla.codestpro1) as codestpro1,MIN(curTabla.codestpro2) as codestpro2,MIN(curTabla.codestpro3) as codestpro3,MIN(curTabla.codestpro4) as codestpro4,MIN(curTabla.codestpro5) as codestpro5,MIN(curTabla.estcla) as estcla ';
				  $ls_orden = ' ASC '; 
		break;
		
		case 'BOTTOM'	: $ls_cab_sql = ' MAX(curTabla.codestpro1) as codestpro1,MAX(curTabla.codestpro2) as codestpro2,MAX(curTabla.codestpro3) as codestpro3,MAX(curTabla.codestpro4) as codestpro4,MAX(curTabla.codestpro5) as codestpro5,MAX(curTabla.estcla) as estcla ';
				    $ls_orden = ' DESC '; 
		break;
	}
	
	//$ls_sql = " SELECT $ls_cab_sql
	//	    FROM (
	//	    SELECT distinct spg_ep5.estcla,spg_ep5.codestpro1 ,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5
	//	    FROM spg_ep5
	//	    WHERE spg_ep5.codemp = '$ls_codemp' 
	//	    GROUP BY spg_ep5.estcla,spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5
	//	    ORDER BY spg_ep5.estcla ,spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3 ) as curtabla";
	
	$ls_sql = " SELECT 
		      spg_ep5.codestpro1, 
		      spg_ep5.codestpro2, 
		      spg_ep5.codestpro3, 
		      spg_ep5.codestpro4, 
		      spg_ep5.codestpro5, 
		      spg_ep5.codemp, 
		      spg_ep5.estcla
		    FROM 
		      spg_ep5, 
		      spg_ep4, 
		      spg_ep3, 
		      spg_ep2, 
		      spg_ep1
		    WHERE
		      spg_ep5.codemp = '$ls_codemp' and
		      spg_ep5.codemp = spg_ep4.codemp AND
		      spg_ep5.codestpro1 = spg_ep4.codestpro1 AND
		      spg_ep5.codestpro2 = spg_ep4.codestpro2 AND
		      spg_ep5.codestpro3 = spg_ep4.codestpro3 AND
		      spg_ep5.codestpro4 = spg_ep4.codestpro4 AND
		      spg_ep5.estcla = spg_ep4.estcla AND
		      spg_ep4.codemp = spg_ep3.codemp AND
		      spg_ep4.codestpro1 = spg_ep3.codestpro1 AND
		      spg_ep4.codestpro2 = spg_ep3.codestpro2 AND
		      spg_ep4.codestpro3 = spg_ep3.codestpro3 AND
		      spg_ep4.estcla = spg_ep3.estcla AND
		      spg_ep3.codemp = spg_ep2.codemp AND
		      spg_ep3.codestpro1 = spg_ep2.codestpro1 AND
		      spg_ep3.codestpro2 = spg_ep2.codestpro2 AND
		      spg_ep3.estcla = spg_ep2.estcla AND
		      spg_ep2.codemp = spg_ep1.codemp AND
		      spg_ep2.codestpro1 = spg_ep1.codestpro1 AND
			  spg_ep5.codestpro1 <> '-------------------------'
		    ORDER BY
		      spg_ep5.codestpro1 $ls_orden, 
		      spg_ep5.codestpro2 $ls_orden, 
		      spg_ep5.codestpro3 $ls_orden, 
		      spg_ep5.codestpro4 $ls_orden, 
		      spg_ep5.codestpro5 $ls_orden, 
		      spg_ep5.estcla $ls_orden ";

	//echo $ls_sql."<br>";
	$rs_data=$this->io_sql->select($ls_sql); //print "$ls_sql <br><br><br>";
	if($rs_data===false)
	{   	// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro1 ".$this->io_function->uf_convertirmsg($this->io_sql->message);
		$lb_valido = false;
	}
	else
	{
		if($row=$this->io_sql->fetch_row($rs_data))
		{
			$as_codestpro1=$row["codestpro1"];
			$as_codestpro2=$row["codestpro2"];
			$as_codestpro3=$row["codestpro3"];
			$as_codestpro4=$row["codestpro4"];
			$as_codestpro5=$row["codestpro5"];
			$as_estcla=$row["estcla"];
			//print " $as_topbottom  $as_codestpro1, $as_codestpro2, $as_codestpro3, $as_codestpro4, $as_codestpro5, $as_estcla <br> <br> ";
		}
		$this->io_sql->free_result($rs_data);
	}//else
	$arrResultado['as_codestpro1']=$as_codestpro1;
	$arrResultado['as_codestpro2']=$as_codestpro2;
	$arrResultado['as_codestpro3']=$as_codestpro3;
	$arrResultado['as_codestpro4']=$as_codestpro4;
	$arrResultado['as_codestpro5']=$as_codestpro5;
	$arrResultado['as_estcla']=$as_estcla;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
  }//uf_spg_reporte_select_denestpro1	




	
//------------------------------------------------------------------------------------------------------------------------------------- 

	//////////////////////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SPI  "EJECUCI?N PRESUPUESTARIA MENSUAL DE INGRESO CON ESTRUCTURA"   // 
	//////////////////////////////////////////////////////////////////////////////////////////
    function uf_spi_reporte_ejecucion_financiera_mensual_estructura($as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                          $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											  $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas,$adt_fecini,
											  $adt_fecfin,$ai_nivel,$ls_modalidad)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_ejecucion_financiera_mensual_estructura
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
	//	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte de la ejecucion financiera mensual por estructura
	 //     Creado por :    Ing. Arnaldo Su?rez
	 // Fecha Creaci?n :    16/03/2009         Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_existe = false;	 
		$lb_valido = false;
		$lb_ok = true;
	    $ab_subniveles=true;
		$ls_gestor = $_SESSION["ls_gestor"];
		$this->dts_reporte->reset_ds();
		$ls_seguridad="";
	    $ls_seguridad=$this->io_spg_report_funciones->uf_filtro_seguridad_programatica('spi_cuentas_estructuras',$ls_seguridad);
		$ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
		$ls_estructura_dstino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
		if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		{
			$ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5.spi_cuentas_estructuras.estcla)";
		}
		else
		{
			$ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
		}
		
		$dts_cuentas=new class_datastore();
    	$ls_sql=" SELECT spi_cuentas.spi_cuenta,spi_cuentas.denominacion,spi_cuentas.nivel,spi_cuentas.status,spi_cuentas_estructuras.previsto".
                " FROM  spi_cuentas_estructuras,spi_cuentas".
                " WHERE spi_cuentas_estructuras.codemp='".$this->ls_codemp."' ".
				" AND spi_cuentas_estructuras.spi_cuenta=spi_cuentas.spi_cuenta ".
				" AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
				" AND nivel <= ".$ai_nivel." ".$ls_seguridad.
                " ORDER BY spi_cuentas_estructuras.spi_cuenta "; 
		$rs_cuentas=$this->io_sql->select($ls_sql);
		if($rs_cuentas===false)
		{   //error interno sql
			$this->io_msg->message("CLASE->sigesp_spi_reporte
									M?TODO->uf_spi_reporte_ejecucion_financiera_mensual 
									ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
           $lb_valido = false;
		}
		else
        {
		   if($row=$this->io_sql->fetch_row($rs_cuentas))
		   {
              $dts_cuentas->data=$this->io_sql->obtener_datos($rs_cuentas);
              $lb_existe=true;
           }
		   $this->io_sql->free_result($rs_cuentas);   
           if($lb_existe==false)
           {
              //return false; // no hay registro
			  $lb_ok=false;
		   }
		   if($lb_ok)
		   {
           $li_total_row=$dts_cuentas->getRowCount("spi_cuenta");			
		   for ($li_i=1;$li_i<=$li_total_row;$li_i++)
  	       {   
			   $lb_si_va = false;
			   $ls_spi_cuenta = $dts_cuentas->getValue("spi_cuenta",$li_i);
			   $li_nivel = $dts_cuentas->getValue("nivel",$li_i);
			   $ls_denominacion = $dts_cuentas->getValue("denominacion",$li_i);
			   $ls_status = $dts_cuentas->getValue("status",$li_i);
			   $ls_previsto = $dts_cuentas->getValue("previsto",$li_i);
		 
				  // Calculo lo Ejecutado y acumulado
				    $ld_monto_aumento=0;
				    $ld_monto_disminucion=0;
				    $ld_monto_devengado=0;
				    $ld_monto_cobrado=0;
				    $ld_cobrado_anticipado=0;
					$ld_previsto=0;
					$arrResultado = $this->uf_calcular_operaciones_por_cuenta2($adt_fecini,$adt_fecfin,$ld_previsto,$ld_monto_aumento,$ld_monto_disminucion,$ld_monto_devengado,$ld_monto_cobrado,
														       $ld_cobrado_anticipado,$ls_spi_cuenta,$as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
															   $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
															   $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas);
					$ld_previsto = $arrResultado['ad_previsto'];
					$ld_monto_aumento = $arrResultado['ad_aumento'];
					$ld_monto_disminucion = $arrResultado['ad_disminucion'];
					$ld_monto_devengado = $arrResultado['ad_devengado'];
					$ld_monto_cobrado = $arrResultado['ad_cobrado'];
					$ld_cobrado_anticipado = $arrResultado['ad_cobrado_anticipado'];
					$lb_valido = $arrResultado['lb_valido'];
					if (!$lb_valido)
					{
					   return false; 
					} 
					$ll_row_found = $this->dts_reporte->find("spi_cuenta",$ls_spi_cuenta);
					if ($ll_row_found == 0)
					{  
						$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
						$this->dts_reporte->insertRow("denominacion",$ls_denominacion);							
						$this->dts_reporte->insertRow("nivel",$li_nivel);							
						$this->dts_reporte->insertRow("previsto",$ld_previsto);
						$this->dts_reporte->insertRow("aumento",$ld_monto_aumento);							
						$this->dts_reporte->insertRow("disminucion",$ld_monto_disminucion);
						$this->dts_reporte->insertRow("devengado",$ld_monto_devengado);
						$this->dts_reporte->insertRow("cobrado",$ld_monto_cobrado);							
						$this->dts_reporte->insertRow("cobrado_anticipado",$ld_cobrado_anticipado);							
						$this->dts_reporte->insertRow("status",$ls_status);		
		                $lb_valido = true;
					
					} 
					else
					
					{
						$ldec_monto = $this->dts_reporte->getValue("previsto",$ll_row_found );
						$ldec_monto = $ldec_monto + $ld_previsto;
						$this->dts_reporte->updateRow("asignado",$ldec_monto,$ll_row_found);	
						$ldec_monto = $this->dts_reporte->getValue("aumento",$ll_row_found );
						$ldec_monto = $ldec_monto + $ldec_monto_aumento;
						$this->dts_reporte->updateRow("aumento",$ldec_monto,$ll_row_found);						
						$ldec_monto = $this->dts_reporte->getValue("disminucion",$ll_row_found );
						$ldec_monto = $ldec_monto + $ldec_monto_disminucion;						
						$this->dts_reporte->updateRow("disminucion",$ldec_monto,$ll_row_found);
						$ldec_monto = $this->dts_reporte->getValue("devengado",$ll_row_found );
						$ldec_monto = $ldec_monto + $ld_monto_devengado;																						
						$this->dts_reporte->updateRow("devengado",$ldec_monto,$ll_row_found);
						$ldec_monto = $this->dts_reporte->getValue("cobrado",$ll_row_found );
						$ldec_monto = $ldec_monto + $ld_monto_cobrado;																								
						$this->dts_reporte->updateRow("cobrado",$ldec_monto,$ll_row_found);							
						$ldec_monto = $this->dts_reporte->getValue("cobrado_anticipado",$ll_row_found );
						$ldec_monto = $ldec_monto + $ld_cobrado_anticipado;									
						$this->dts_reporte->updateRow("cobrado_anticipado",$ldec_monto,$ll_row_found);	
						$this->dts_reporte->updateRow("status",$ls_status,$ll_row_found);		
		                $lb_valido = true;
					}// else
		 } // end for
	   }//if($lb_ok)	
	 } //else
	// print_r($this->dts_reporte);
	 return $lb_valido;
   } // fin function uf_spi_reporte_ejecucion_financiera_mensual_estructura
   
    function uf_spi_reporte_ejecucion_financiera_mensual($adt_fecini,$adt_fecfin,$ai_nivel,$ai_MenorNivel)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_ejecucion_financiera_mensual
	 //         Access :	private
	 //     Argumentos :    as_codestpro1_ori ... $as_codestpro5_ori //rango nivel estructura presupuestaria origen
	 //                     as_codestpro1_des ... $as_codestpro5_des //rango nivel estructura presupuestaria destino
	 //                     adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
	 //                     ai_MenorNivel  // variabvle que determina el menor nivel de la cuenta
     //	       Returns :	Retorna estructuras ordenadas para la consulta sql
	 //	   Description :	Reporte que genera el reporte del acumulado por cuentas(asignacion,aumneto,disminucion ...) 
	 //     Creado por :    Ing. Yozelin Barragan 
	 // Fecha Creaci?n :    27/09/2006         Fecha ?ltima Modificacion : 01/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_existe = false;	 
		$lb_valido = false;
		$lb_ok = true;
	    $this->dts_reporte->reset_ds();
		$dts_cuentas=new class_datastore();
		$ls_sql=" SELECT PCT.* ".
                " FROM  spi_cuentas PCT ".
                " WHERE PCT.codemp='".$this->ls_codemp."' AND PCT.nivel<='".$ai_nivel."' ".
                " ORDER BY  PCT.spi_cuenta "; 
		$rs_cuentas=$this->io_sql->select($ls_sql);
		if($rs_cuentas===false)
		{   //error interno sql
			$this->io_msg->message("CLASE->sigesp_spi_reporte
									M?TODO->uf_spg_reporte_acumulado_cuentas 
									ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
           $lb_valido = false;
		}
		else
        {
		   if($row=$this->io_sql->fetch_row($rs_cuentas))
		   {
              $dts_cuentas->data=$this->io_sql->obtener_datos($rs_cuentas);
              $lb_existe=true;
           }
		   $this->io_sql->free_result($rs_cuentas);   
           if($lb_existe==false)
           {
              //return false; // no hay registro
			  $lb_ok=false;
		   }
		   if($lb_ok)
		   {
           $li_total_row=$dts_cuentas->getRowCount("spi_cuenta");			
		   for ($li_i=1;$li_i<=$li_total_row;$li_i++)
  	       {   
			   $lb_si_va = false;
			   $ls_spi_cuenta = $dts_cuentas->getValue("spi_cuenta",$li_i);
			   $ls_denominacion = $dts_cuentas->getValue("denominacion",$li_i);
			   $li_nivel = $dts_cuentas->getValue("nivel",$li_i);
			   $ls_status = $dts_cuentas->getValue("status",$li_i);
			   $ls_previsto = number_format($dts_cuentas->getValue("previsto",$li_i),2,".",""); 
			
				  // Calculo lo Ejecutado y acumulado
				    $ld_monto_aumento=0;
				    $ld_monto_disminucion=0;
				    $ld_monto_devengado=0;
				    $ld_monto_cobrado=0;
				    $ld_cobrado_anticipado=0;
					$ld_previsto=0;
					$arrResultado = $this->uf_calcular_operaciones_por_cuenta($adt_fecini,$adt_fecfin,$ld_previsto,
					                                                         $ld_monto_aumento,$ld_monto_disminucion,
																			 $ld_monto_devengado,$ld_monto_cobrado,
														                     $ld_cobrado_anticipado,$ls_spi_cuenta);
					$ld_previsto = number_format($arrResultado['ad_previsto'],2,".",""); 
					$ld_monto_aumento = number_format($arrResultado['ad_aumento'],2,".",""); 
					$ld_monto_disminucion = number_format($arrResultado['ad_disminucion'],2,".",""); 
					$ld_monto_devengado = number_format($arrResultado['ad_devengado'],2,".",""); 
					$ld_monto_cobrado = number_format($arrResultado['ad_cobrado'],2,".",""); 
					$ld_cobrado_anticipado = number_format($arrResultado['ad_cobrado_anticipado'],2,".",""); 
					$lb_valido = $arrResultado['lb_valido'];
					
					if (!$lb_valido)
					{
					   return false; 
					} 
					$ll_row_found = $this->dts_reporte->find("spi_cuenta",$ls_spi_cuenta);
					if ($ll_row_found == 0)
					{  
						$this->dts_reporte->insertRow("spi_cuenta",$ls_spi_cuenta);
						$this->dts_reporte->insertRow("denominacion",$ls_denominacion);							
						$this->dts_reporte->insertRow("nivel",$li_nivel);							
						$this->dts_reporte->insertRow("previsto",$ld_previsto);
						$this->dts_reporte->insertRow("aumento",$ld_monto_aumento);							
						$this->dts_reporte->insertRow("disminucion",$ld_monto_disminucion);
						$this->dts_reporte->insertRow("devengado",$ld_monto_devengado);
						$this->dts_reporte->insertRow("cobrado",$ld_monto_cobrado);							
						$this->dts_reporte->insertRow("cobrado_anticipado",$ld_cobrado_anticipado);							
						$this->dts_reporte->insertRow("status",$ls_status);		
		                $lb_valido = true;
					
					} 
					else
					
					{
						$ldec_monto = $this->dts_reporte->getValue("previsto",$ll_row_found );
						$ldec_monto = number_format($ldec_monto + $ld_previsto,2,".",""); 
						$this->dts_reporte->updateRow("asignado",$ldec_monto,$ll_row_found);	
						$ldec_monto = $this->dts_reporte->getValue("aumento",$ll_row_found );
						$ldec_monto = number_format($ldec_monto + $ldec_monto_aumento,2,".",""); 
						$this->dts_reporte->updateRow("aumento",$ldec_monto,$ll_row_found);						
						$ldec_monto = $this->dts_reporte->getValue("disminucion",$ll_row_found );
						$ldec_monto = number_format($ldec_monto + $ldec_monto_disminucion,2,".",""); 						
						$this->dts_reporte->updateRow("disminucion",$ldec_monto,$ll_row_found);
						$ldec_monto = $this->dts_reporte->getValue("devengado",$ll_row_found );
						$ldec_monto = number_format($ldec_monto + $ld_monto_devengado,2,".",""); 																						
						$this->dts_reporte->updateRow("devengado",$ldec_monto,$ll_row_found);
						$ldec_monto = $this->dts_reporte->getValue("cobrado",$ll_row_found );
						$ldec_monto = number_format($ldec_monto + $ld_monto_cobrado,2,".",""); 																								
						$this->dts_reporte->updateRow("cobrado",$ldec_monto,$ll_row_found);							
						$ldec_monto = $this->dts_reporte->getValue("cobrado_anticipado",$ll_row_found );
						$ldec_monto = number_format($ldec_monto + $ld_cobrado_anticipado,2,".",""); 									
						$this->dts_reporte->updateRow("cobrado_anticipado",$ldec_monto,$ll_row_found);	
						$this->dts_reporte->updateRow("status",$ls_status,$ll_row_found);		
		                $lb_valido = true;
					}// else
		 } // end for
	   }//if($lb_ok)	
	 } //else
		$arrResultado['ai_MenorNivel']=$ai_MenorNivel;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   } // fin function uf_spi_reporte_ejecucion_financiera_mensual
   
   function uf_calcular_operaciones_por_cuenta($adt_fecini,$adt_fecfin,$ad_previsto,$ad_aumento,$ad_disminucion,$ad_devengado,$ad_cobrado,$ad_cobrado_anticipado,$as_spi_cuenta)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_operaciones_por_cuenta
     //	    Returns :	$lb_valido true si se realizo la funcion con exito o false en caso contrario
	 //	Description :	M?todo  que ejecuta todas funciones de calcular las operaciones de Ingreso
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true; 
	  $as_spi_cuenta=$this->sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_cadena_fecha = "";
	  switch(strtoupper($ls_gestor))
	  {
	   case "POSTGRES" : $ls_cadena_fecha = " AND substr(CAST(fecha AS TEXT),1,10) <='".$adt_fecfin."'";
	   break;
	   case "MYSQLT"   : $ls_cadena_fecha = " AND substr(fecha,1,10) <='".$adt_fecfin."'";
	   break;
	   case "MYSQLI"   : $ls_cadena_fecha = " AND substr(fecha,1,10) <='".$adt_fecfin."'";
	   break;
	  }
	  
	  $ls_sql=" SELECT * ".
			  " FROM   spi_dt_cmp ".
			  " WHERE  codemp='".$this->ls_codemp."'  AND  spi_cuenta like '".$as_spi_cuenta."' ".$ls_cadena_fecha;	   
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spi_reporte
		                        M?TODO->uf_calcular_operaciones_por_cuenta 
								ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_data))
		{
		  $ls_operacion=$row["operacion"];
		  $ld_monto=number_format($row["monto"],2,".","");
		  $ldt_fecha_db=$row["fecha"];
		  $ldt_fecha=substr($ldt_fecha_db,0,10);
		  $ldt_fecha=str_replace("-","",$ldt_fecha);
		  $ldt_mesdes=str_replace("-","",$adt_fecini);
		  $ldt_meshas=str_replace("-","",$adt_fecfin);
		  $ls_opera=$this->sigesp_int_spi->uf_operacion_codigo_mensaje($ls_operacion);
		  $ls_mensaje=strtoupper($ls_opera); // devuelve cadena en MAYUSCULAS
		  $li_pos_i=strpos($ls_mensaje,"I"); 
		  if (!($li_pos_i===false)) 
		  {
		    $ad_previsto=number_format($ad_previsto+$ld_monto,2,".","");
		  }
		  if(($ldt_fecha>=$ldt_mesdes)&&($ldt_fecha<=$ldt_meshas))
		  {		
			  $li_pos_e=strpos($ls_mensaje,"E"); 
			  if (!($li_pos_e===false)) 
			  { 
				 $ad_devengado=number_format($ad_devengado+$ld_monto,2,".","");
			  }
			  $li_pos_c=strpos($ls_mensaje,"C"); 
			  if (!($li_pos_c===false)) 
			  {	
				 $ad_cobrado=number_format($ad_cobrado+$ld_monto,2,".","");
			  }
			  $li_pos_n=strpos($ls_mensaje,"N"); 
			  if (!($li_pos_n===false))
			  {	
				$ad_cobrado_anticipado = number_format($ad_cobrado_anticipado+$ld_monto,2,".",""); 
			  }
			  $li_pos_a=strpos($ls_mensaje,"A"); 
			  if (!($li_pos_a===false))
			  {	
				$ad_aumento = number_format($ad_aumento+$ld_monto,2,".","");
			  }
			  $li_pos_d=strpos($ls_mensaje,"D"); 
			  if (!($li_pos_d===false))
			  {	
				$ad_disminucion = number_format($ad_disminucion+$ld_monto,2,".","");
			  }
		 }//if
	          $lb_valido = true;
		}//while
	   $this->io_sql->free_result($rs_data);
	  }//else
		$arrResultado['ad_previsto']=$ad_previsto;
		$arrResultado['ad_aumento']=$ad_aumento;
		$arrResultado['ad_disminucion']=$ad_disminucion;
		$arrResultado['ad_devengado']=$ad_devengado;
		$arrResultado['ad_cobrado']=$ad_cobrado;
		$arrResultado['ad_cobrado_anticipado']=$ad_cobrado_anticipado;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // fin uf_calcular_operaciones_por_cuenta
	
	function uf_calcular_operaciones_por_cuenta2($adt_fecini,$adt_fecfin,$ad_previsto,$ad_aumento,
	                                                      $ad_disminucion,$ad_devengado,$ad_cobrado,
														  $ad_cobrado_anticipado,$as_spi_cuenta,
														  $as_codestpro1_ori,$as_codestpro2_ori,$as_codestpro3_ori,$as_codestpro4_ori,
	                                                      $as_codestpro5_ori,$as_codestpro1_des,$as_codestpro2_des,$as_codestpro3_des,
											              $as_codestpro4_des,$as_codestpro5_des,$as_estclades,$as_estclahas)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	   Function :	uf_calcular_operaciones_por_cuenta2
     //	    Returns :	$lb_valido true si se realizo la funcion con exito o false en caso contrario
	 //	Description :	M?todo  que ejecuta todas funciones de calculo de operaciones
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true; 
	  $as_spi_cuenta=$this->sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_estructura_origen=$as_codestpro1_ori.$as_codestpro2_ori.$as_codestpro3_ori.$as_codestpro4_ori.$as_codestpro5_ori.$as_estclades;
	  $ls_estructura_dstino=$as_codestpro1_des.$as_codestpro2_des.$as_codestpro3_des.$as_codestpro4_des.$as_codestpro5_des.$as_estclahas;
		if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		{
			$ls_str_sql_1 = "CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5.spi_cuentas_estructuras.estcla)";
		}
		else
		{
			$ls_str_sql_1 = "spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
		}

	  $ls_sql=" SELECT * ".
			  " FROM   spi_dt_cmp,spi_cuentas_estructuras ".
			  " WHERE  spi_dt_cmp.codemp='".$this->ls_codemp."'  AND  spi_dt_cmp.spi_cuenta like '".$as_spi_cuenta."' ".
			  " AND  spi_dt_cmp.spi_cuenta=spi_cuentas_estructuras.spi_cuenta".
              " AND spi_dt_cmp.codemp=spi_cuentas_estructuras.codemp".
			  " AND spi_cuentas_estructuras.codestpro1=spi_dt_cmp.codestpro1 and spi_cuentas_estructuras.codestpro2=spi_dt_cmp.codestpro2 ".
              " AND spi_cuentas_estructuras.codestpro3=spi_dt_cmp.codestpro3 and spi_cuentas_estructuras.codestpro4=spi_dt_cmp.codestpro4 ".
              " AND spi_cuentas_estructuras.codestpro5=spi_dt_cmp.codestpro5 and spi_cuentas_estructuras.estcla=spi_dt_cmp.estcla ".
			  " AND  ".$ls_str_sql_1."  between '".$ls_estructura_origen."' AND '".$ls_estructura_dstino."' ".
			  " AND substr(cast(spi_dt_cmp.fecha as char(10)),1,10) <='".$adt_fecfin."'";;
//		print $ls_sql."<br />"; 
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spi_reporte
		                        M?TODO->uf_calcular_acumulado_operaciones_por_cuenta 
								ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_data))
		{
		  $ls_operacion=$row["operacion"];
		  $ld_monto=$row["monto"];
		  $ldt_fecha_db=$row["fecha"];
		  $ldt_fecha=substr($ldt_fecha_db,0,10);
		  $ldt_fecha=str_replace("-","",$ldt_fecha);
		  $ldt_mesdes=str_replace("-","",$adt_fecini);
		  $ldt_meshas=str_replace("-","",$adt_fecfin);
		  $ls_opera=$this->sigesp_int_spi->uf_operacion_codigo_mensaje($ls_operacion);
		  
		  $ls_mensaje=strtoupper($ls_opera); // devuelve cadena en MAYUSCULAS
		  $li_pos_i=strpos($ls_mensaje,"I"); 
		  if (!($li_pos_i===false)) 
		  { 
		    $ad_previsto=$ad_previsto+$ld_monto; 
		  }
		  if(($ldt_fecha>=$ldt_mesdes)&&($ldt_fecha<=$ldt_meshas))
		  {		
			  $li_pos_e=strpos($ls_mensaje,"E"); 
			  if (!($li_pos_e===false)) 
			  { 
				 $ad_devengado=$ad_devengado+$ld_monto;
			  }
			  $li_pos_c=strpos($ls_mensaje,"C"); 
			  if (!($li_pos_c===false)) 
			  {	
				 $ad_cobrado=$ad_cobrado+$ld_monto;
			  }
			  $li_pos_n=strpos($ls_mensaje,"N"); 
			  if (!($li_pos_n===false))
			  {	
			    $ad_cobrado_anticipado = $ad_cobrado_anticipado+$ld_monto; 
			  }
			  $li_pos_a=strpos($ls_mensaje,"A"); 
			  if (!($li_pos_a===false))
			  {	
			    $ad_aumento = $ad_aumento+$ld_monto; 
			  }
			  $li_pos_d=strpos($ls_mensaje,"D"); 
			  if (!($li_pos_d===false))
			  {	
			    $ad_disminucion = $ad_disminucion+$ld_monto;
			  }
	          $lb_valido = true;
		  }//if
		}//if 
	   $this->io_sql->free_result($rs_data);
	  }//else	
		$arrResultado['ad_previsto']=$ad_previsto;
		$arrResultado['ad_aumento']=$ad_aumento;
		$arrResultado['ad_disminucion']=$ad_disminucion;
		$arrResultado['ad_devengado']=$ad_devengado;
		$arrResultado['ad_cobrado']=$ad_cobrado;
		$arrResultado['ad_cobrado_anticipado']=$ad_cobrado_anticipado;
		$arrResultado['lb_valido']=$lb_valido;//print_r($arrResultado);
		return $arrResultado;
	} // fin uf_calcular_operaciones_por_cuenta2  
	
	function uf_spi_calcular_previsto_estructura($as_spi_cuenta,$as_codestpro_desde,$as_codestpro_hasta,$ad_previsto)
	{///////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_calcular_previsto_estructura
	 //         Access :	private
	 //     Argumentos :    $as_spi_cuenta       // Cuenta de Ingreso
	 //                     $aa_codestpro_desde  // Estructura Presupuestaria Desde
	 //                     $aa_codestpro_hasta, // Estructura Presupuestaria Hasta
	 //                     $ad_previsto,      // Total Previsto de Cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Funcion que retorna el total previsto para la cuenta segun la estructura presupuestaria
	 //     Creado por :    Ing. Arnaldo Suarez
	 // Fecha Creaci?n :    24/05/2010          Fecha ?ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ld_previsto=0;
	  $ld_acumtrimestre=0;
	  $ls_estructura="";
	  $ls_codestpro="";
	  $ls_gestor = $_SESSION["ls_gestor"];
		
		
		
		switch($ls_gestor)
		{
		 case 'MYSQLT':   $ls_codestpro="CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)"; 
						  break;
						
		 case 'MYSQLI':   $ls_codestpro="CONCAT(spi_cuentas_estructuras.codestpro1,spi_cuentas_estructuras.codestpro2,spi_cuentas_estructuras.codestpro3,spi_cuentas_estructuras.codestpro4,spi_cuentas_estructuras.codestpro5,spi_cuentas_estructuras.estcla)"; 
						  break;
						
		 case 'POSTGRES': $ls_codestpro="spi_cuentas_estructuras.codestpro1||spi_cuentas_estructuras.codestpro2||spi_cuentas_estructuras.codestpro3||spi_cuentas_estructuras.codestpro4||spi_cuentas_estructuras.codestpro5||spi_cuentas_estructuras.estcla";
		                  
						  break;
		
		}
		
	  $ls_estructura = "AND $ls_codestpro BETWEEN '".$as_codestpro_desde."' AND '".$as_codestpro_hasta."'";
	  $as_spi_cuenta=$this->sigesp_int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta)."%";
	  $ls_sql=" SELECT SUM(previsto) AS previsto ".
		      " FROM spi_cuentas_estructuras ".
              "  WHERE  spi_cuentas_estructuras.codemp='".$this->ls_codemp."' AND ".
              "        spi_cuentas_estructuras.spi_cuenta like '".$as_spi_cuenta."' ".$ls_estructura;		  	    	  
	  $rs_ejec=$this->io_sql->select($ls_sql);
	  if($rs_ejec===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spi_reporte
		                        M?TODO->uf_spi_calcular_previsto_estructura 
								ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		if(!$rs_ejec->EOF)
		{
		  $ld_previsto = $rs_ejec->fields["previsto"];
		}//if
		$ad_previsto = $ld_previsto;
	   $this->io_sql->free_result($rs_ejec);
	  }//else	
		$arrResultado['ad_previsto']=$ad_previsto;
		$arrResultado['lb_valido']=$lb_valido;
		return $lb_valido;
	}
  
}//fin de clase
?>