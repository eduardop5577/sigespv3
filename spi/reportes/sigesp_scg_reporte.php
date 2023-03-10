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

require_once("../../base/librerias/php/general/sigesp_lib_sql.php");	  
require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../../base/librerias/php/general/sigesp_lib_fecha.php");
require_once("../../base/librerias/php/general/sigesp_lib_include.php");
require_once("../../shared/class_folder/class_sigesp_int.php");
require_once("../../shared/class_folder/class_sigesp_int_scg.php");
class sigesp_scg_reporte
{
	var $SQL; 
	var $dts_empresa; // datastore empresa
	var $fun;
	var $io_msg;
	var $SQL_aux;
	var $ds_analitico;
	var $dts_reporte;
	var $dts_cab;
	var $dts_egresos;
	var $dts_Prebalance;
	var $dts_Balance1;
	var $sigesp_int_scg;
	
/****************************************************************************************************************************************/	
	public function __construct()
	{
	  $this->fun = new class_funciones();
	  $this->siginc=new sigesp_include();
	  $this->con=$this->siginc->uf_conectar();
	  $this->SQL= new class_sql($this->con);
	  $this->SQL_aux= new class_sql($this->con);
	  $this->io_msg= new class_mensajes();		
	  $this->dts_empresa=$_SESSION["la_empresa"];
	  $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	  $this->ds_analitico=new class_datastore();
	  $this->ds_analitico_info_cheque=new class_datastore();
	  $this->dts_reporte=new class_datastore();
	  $this->dts_cab=new class_datastore();
	  $this->dts_egresos=new class_datastore();
	  $this->dts_Prebalance=new class_datastore();
	  $this->dts_Balance1=new class_datastore();
      $this->sigesp_int_scg=new class_sigesp_int_scg();
	}
/****************************************************************************************************************************************/	
    //////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SCG  " COMPROBANTES FORMATO 1 Y FORMATO 2" // 
	////////////////////////////////////////////////////////////////
	function uf_scg_reporte_comprobante_formato1($ls_procede,$ls_comprobante,$ldt_fecha,$ai_orden,$as_codban='---',$as_ctaban='-------------------------')
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_comprobante_formato1
	 //         Access :	private
	 //     Argumentos :    $as_procede  // procede 
	 //                     $as_comprobante  // comprobante  
	 //                     $adt_fecha  // fecha 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 1  
	 //     Creado por :    Ing. Nelson Barraez
	 // Fecha Creaci??? :    04-07-2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
		$lb_valido = true;		
		$ls_codemp = $this->dts_empresa["codemp"];
	    $this->dts_reporte->resetds("sc_cuenta");
        
		if(!empty($ls_procede))
		{
			   $ls_cad_where1=" MV.procede = '".$ls_procede."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if(!empty($ls_comprobante))
		{
			   $ls_cad_where2=" MV.comprobante = '".$ls_comprobante."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if(!empty($ldt_fecha))
		{
			   $ls_cad_where3=" MV.fecha='".$ldt_fecha."' ";
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
	   if($ai_orden==1)  
	   {
    	  $ls_orden_cad="MV.debhab,MV.procede,MV.comprobante,MV.fecha,MV.orden";
	   }
	   if($ai_orden==2)  
	   {
    	  $ls_orden_cad="MV.debhab,MV.comprobante,MV.fecha,MV.procede,MV.orden";
	   }
	   if($ai_orden==3)  
	   {
          $ls_orden_cad="MV.debhab,MV.fecha,MV.procede,MV.comprobante,MV.orden";
	   }
	   $ls_sql=" SELECT  MV.procede, MV.comprobante, MV.sc_cuenta , MV.procede_doc,MV.documento, MV.debhab, MV.monto, ".
	   		   "		 CC.denominacion, descripcion  as cmp_descripcion ".
               " FROM    scg_dt_cmp MV, scg_cuentas CC ".
               " WHERE  (MV.sc_cuenta = CC.sc_cuenta) ".
			   "        ".$ls_cad_where." ".
			   /*/"   AND  MV.codban = '".$as_codban."' ".
			   "   AND  MV.ctaban = '".$as_ctaban."' "./*/
			   " ORDER BY  MV.debhab,MV.sc_cuenta ";
			  
			   	  
		$this->rs_data_comp=$this->SQL->select($ls_sql);
	
		if($this->rs_data_comp===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			return false;
		}
			
  return true;
  }// fin uf_spg_reporte_comprobante_formato1
/****************************************************************************************************************************************/	
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
/****************************************************************************************************************************************/
function uf_scg_reporte_comprobante_formato1_plus($ls_procede,$ls_comprobante,$ldt_fecha,$ai_orden,$as_codban='---',$as_ctaban='-------------------------',$data)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_comprobante_formato1
	 //         Access :	private
	 //     Argumentos :    $as_procede  // procede 
	 //                     $as_comprobante  // comprobante  
	 //                     $adt_fecha  // fecha 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 1  
	 //     Creado por :    Ing. Nelson Barraez
	 // Fecha Creaci??? :    04-07-2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
		$lb_valido = true;		
		$ls_codemp = $this->dts_empresa["codemp"];
	    if(!empty($ls_procede)){
			   $ls_cad_where1=" MV.procede = '".$ls_procede."' ";
		}
		else{
		 	   $ls_cad_where1="";
		}
		
		if(!empty($ls_comprobante)){
			   $ls_cad_where2=" MV.comprobante = '".$ls_comprobante."' ";
		}
		else{
		 	   $ls_cad_where2="";
		}
		
		if(!empty($ldt_fecha)){
			   $ls_cad_where3=" MV.fecha='".$ldt_fecha."' ";
		}
		else{
		 	   $ls_cad_where3="";
		}
		
		$ls_cadena_concat=$ls_cad_where1.$ls_cad_where2.$ls_cad_where3;
		if (!empty($ls_cadena_concat)){
			$ls_cad_where=" AND ";
			
			if(!empty($ls_cad_where1)){
				$ls_cad_concat=$ls_cad_where2.$ls_cad_where3;
				$ls_cond_iif=$this->iif(!empty($ls_cad_concat)," AND ", "");		    
				$ls_cad_where=$ls_cad_where.$ls_cad_where1.$ls_cond_iif;
			}
			
			if(!empty($ls_cad_where2)){
				$ls_cond_iif=$this->iif(!empty($ls_cad_where3)," AND ", "");		    
				$ls_cad_where=$ls_cad_where.$ls_cad_where2.$ls_cond_iif;
			}
			if(!empty($ls_cad_where3)){
				$ls_cad_where=$ls_cad_where.$ls_cad_where3;
			}
	   }
	   else{
	        $ls_cad_where=" ";
	   }
	   	
	   if($ai_orden==1){
    	  $ls_orden_cad="MV.debhab,MV.procede,MV.comprobante,MV.fecha,MV.orden";
	   }
	   if($ai_orden==2){
    	  $ls_orden_cad="MV.debhab,MV.comprobante,MV.fecha,MV.procede,MV.orden";
	   }
	   if($ai_orden==3){
          $ls_orden_cad="MV.debhab,MV.fecha,MV.procede,MV.comprobante,MV.orden";
	   }
	   
	   $ls_sql=" SELECT  MV.procede, MV.comprobante, MV.sc_cuenta , MV.procede_doc,MV.documento, MV.debhab, MV.monto, ".
	   		   "		 CC.denominacion, descripcion  as cmp_descripcion ".
               " FROM    scg_dt_cmp MV, scg_cuentas CC ".
               " WHERE  (MV.sc_cuenta = CC.sc_cuenta) ".
			   "        ".$ls_cad_where." ".
			   /*/"   AND  MV.codban = '".$as_codban."' ".
			   "   AND  MV.ctaban = '".$as_ctaban."' "./*/
			   " ORDER BY  MV.debhab,MV.sc_cuenta ";
			  
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false){// error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else{   
			$data = $rs_data;
		}//else		
  	return $lb_valido;
  }// fin uf_spg_reporte_comprobante_formato1
/****************************************************************************************************************************************/	
    function uf_scg_reporte_select_comprobante_formato1($as_procede_ori,$as_procede_des,$as_comprobante_ori,$as_comprobante_des,
	                                                    $adt_fecini,$adt_fecfin,$ai_orden,$rs='')
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_comprobante_formato1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
	 //                     $as_procede_des  // procede destino
	 //                     $as_comprobante_ori  // comprobante origen 
	 //                     $as_comprobante_des  //  comprobante destino
	 //                     $adt_fecini  // fecha  desde 
     //              	    $adt_fecfin  // fecha hasta 
	 //                     $ai_orden  //  orden la consulta  
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 1  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    23/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
		$lb_valido = true;
		$ls_codemp = $this->dts_empresa["codemp"];
        
		if((!empty($as_procede_ori))&&(!empty($as_procede_des)))
		{
			   $ls_cad_where1=" cmp.procede between '".$as_procede_ori."' AND  '".$as_procede_des."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if((!empty($as_comprobante_ori))&&(!empty($as_comprobante_des)))
		{
			   $ls_cad_where2=" cmp.comprobante between '".$as_comprobante_ori."' AND  '".$as_comprobante_des."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if((!empty($adt_fecini))&&(!empty($adt_fecfin)))
		{
			   $ls_cad_where3=" cmp.fecha between '".$adt_fecini."' AND  '".$adt_fecfin."' ";
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
	   if($ai_orden==1)  
	   {
    	  $ls_orden="cmp.procede,cmp.comprobante,cmp.fecha";
	   }
	   if($ai_orden==2)  
	   {
    	  $ls_orden="cmp.comprobante,cmp.fecha,cmp.procede";
	   }
	   if($ai_orden==3)  
	   {
    	  $ls_orden="cmp.fecha,cmp.procede,cmp.comprobante";
	   }
		
		
		if($_SESSION["ls_gestor"]=='MYSQLT'){
			$ls_sql="SELECT cmp.codemp,cmp.procede,
			                cmp.comprobante,cmp.descripcion,
							cmp.fecha,cmp.cod_pro,
							cmp.ced_bene,cmp.tipo_destino,
							SUM(dtcmp.monto) AS monto,cmp.codban,cmp.ctaban,
							(CASE   cmp.tipo_destino
								WHEN 'P'
                    				THEN
                        				(SELECT prov.nompro
                        				 FROM rpc_proveedor prov
                                         WHERE prov.codemp=cmp.codemp AND cmp.cod_pro=prov.cod_pro)
								WHEN 'B'
                    				THEN
                      					(SELECT CONCAT(RTRIM(xbf.apebene),',',xbf.nombene)
                      					 FROM rpc_beneficiario xbf
                      					 WHERE xbf.codemp=cmp.codemp AND cmp.cod_pro=xbf.ced_bene)
								ELSE 'Ninguno'
								END)  as  nombre
					 FROM sigesp_cmp cmp
					 INNER JOIN scg_dt_cmp dtcmp
					 ON  dtcmp.debhab = 'D' AND dtcmp.codemp=cmp.codemp
					 AND dtcmp.procede=cmp.procede AND dtcmp.comprobante=cmp.comprobante
					 AND dtcmp.fecha=cmp.fecha AND dtcmp.codban=cmp.codban
					 AND dtcmp.ctaban=cmp.ctaban
					 WHERE cmp.codemp='".$ls_codemp."' AND cmp.tipo_comp=1 ".$ls_cad_where." 
					 GROUP BY cmp.codemp,cmp.procede,cmp.comprobante,cmp.fecha,cmp.codban,cmp.ctaban 
					 ORDER BY ".$ls_orden;
		}
		if($_SESSION["ls_gestor"]=='POSTGRES'){
			$ls_sql="SELECT cmp.codemp,cmp.procede,
			                cmp.comprobante,cmp.descripcion,
							cmp.fecha,cmp.cod_pro,
							cmp.ced_bene,cmp.tipo_destino,
							SUM(dtcmp.monto) AS monto,cmp.codban,cmp.ctaban,
							(CASE   cmp.tipo_destino
								WHEN 'P'
                    				THEN
                        				(SELECT prov.nompro
                        				 FROM rpc_proveedor prov
                                         WHERE prov.codemp=cmp.codemp AND cmp.cod_pro=prov.cod_pro)
								WHEN 'B'
                    				THEN
                      					(SELECT TRIM(xbf.apebene)||','||xbf.nombene
                      					 FROM rpc_beneficiario xbf
                      					 WHERE xbf.codemp=cmp.codemp AND cmp.ced_bene=xbf.ced_bene)
								ELSE 'Ninguno'
								END)  as  nombre
					 FROM sigesp_cmp cmp
					 INNER JOIN scg_dt_cmp dtcmp
					 ON  dtcmp.debhab = 'D' AND dtcmp.codemp=cmp.codemp
					 AND dtcmp.procede=cmp.procede AND dtcmp.comprobante=cmp.comprobante
					 AND dtcmp.fecha=cmp.fecha AND dtcmp.codban=cmp.codban
					 AND dtcmp.ctaban=cmp.ctaban
					 WHERE cmp.codemp='".$ls_codemp."' AND cmp.tipo_comp=1 ".$ls_cad_where." 
					 GROUP BY cmp.codemp,cmp.procede,cmp.comprobante,cmp.descripcion,cmp.fecha,cmp.cod_pro, cmp.ced_bene,cmp.tipo_destino,cmp.codban,cmp.ctaban 
					 ORDER BY ".$ls_orden;
		}
		$this->rs_data=$this->SQL->select($ls_sql);
		//echo $ls_sql;
		if($this->rs_data===false){// error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else{
			$rs = $this->rs_data;
		}
		
		return $lb_valido;
  }//uf_scg_reporte_select_comprobante_formato1
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_comprobante_formato2($ls_comprobante,$ls_procede,$ldt_fecha,$ai_orden) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_comprobante_formato2
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta_ori  // cuenta origen
	 //                     $as_spg_cuenta_des  // cuenta destino
	 //                     $adt_fecini  // fecha  desde 
     //              	    $adt_fecfin  // fecha hasta 
	 //                     $as_comprobante  // comprobante
	 //                     $ai_orden  // orden de la consulta       
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 2  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    23/04/2006          Fecha ltima Modificacion :24/04/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("sc_cuenta");
	  /*
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
         /*$ls_cad_filtro1=" , (SELECT cmp1.comprobante,cmp1.fecha,cmp1.procede ".
                         "    FROM   sigesp_cmp cmp1, scg_dt_cmp mv1 ".
                         "    WHERE  cmp1.procede=mv1.procede AND cmp1.comprobante=mv1.comprobante AND ".
	                     "           cmp1.fecha=mv1.fecha AND mv1.comprobante='".$as_comprobante."' AND ".
						 "           mv1.procede='".$as_procede."' AND ".
						 "           mv1.sc_cuenta between '".$as_spg_cuenta_ori."' AND '".$as_spg_cuenta_des."' ".
                         "    GROUP BY cmp1.comprobante,cmp1.fecha,cmp1.procede) as curFiltro ";	  
		 
		 $ls_cad_filtro2=" AND MV.comprobante=curFiltro.comprobante ".
                         " AND MV.fecha=curFiltro.fecha ".
                         " AND MV.procede=curFiltro.procede ";			 
	  		$ls_cad_filtro2="MV.sc_cuenta between '".$as_spg_cuenta_ori."' AND '".$as_spg_cuenta_des."' ";
	  } 
      else
	  {
	  		$ls_cad_filtro2="";
	  /*   $ls_cad_filtro1=" , (SELECT cmp1.comprobante,cmp1.fecha,cmp1.procede ".
                         "    FROM   sigesp_cmp cmp1, scg_dt_cmp mv1 ".
                         "    WHERE cmp1.procede=mv1.procede and cmp1.comprobante=mv1.comprobante and cmp1.fecha=mv1.fecha  AND ".
                         "    mv1.comprobante='".$as_comprobante."' ".
						 "    GROUP BY cmp1.comprobante,cmp1.fecha,cmp1.procede) as curFiltro ";
        
		 $ls_cad_filtro2=" AND MV.comprobante=curFiltro.comprobante ".
                         " AND MV.fecha=curFiltro.fecha ".
                         " AND MV.procede=curFiltro.procede ";
	  }
       
	  if ((!empty($adt_fecini)) && (!empty($adt_fecfin)))
	  {
	     $ls_cad_where3=" AND MV.fecha between '".$adt_fecini."' and '".$adt_fecfin."' ";
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
      }*/
        
		if(!empty($ls_procede))
		{
			   $ls_cad_where1=" MV.procede = '".$ls_procede."' ";
		}
		else
		{
		 	   $ls_cad_where1="";
		}
		if(!empty($ls_comprobante))
		{
			   $ls_cad_where2=" MV.comprobante = '".$ls_comprobante."' ";
		}
		else
		{
		 	   $ls_cad_where2="";
		}
		if(!empty($ldt_fecha))
		{
			   $ls_cad_where3=" MV.fecha='".$ldt_fecha."' ";
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
	  
	  $ls_orden="MV.comprobante,MV.fecha,MV.procede,MV.orden";
	  if($ai_orden==1)  
	  {
    	  $ls_orden="MV.debhab,MV.procede,MV.comprobante,MV.fecha,MV.orden";
	  }
	  if($ai_orden==2)  
	  {
    	  $ls_orden="MV.debhab,MV.comprobante,MV.fecha,MV.procede,MV.orden";
	  }
	  if($ai_orden==3)  
	  {
    	  $ls_orden="MV.debhab,MV.fecha,MV.procede,MV.comprobante,MV.orden";
	  }
	  
	  $ls_sql=" SELECT MV.comprobante, MV.sc_cuenta, MV.procede_doc, MV.debhab, MV.descripcion, MV.monto, CC.denominacion, MV.descripcion as cmp_descripcion ".
              " FROM   scg_dt_cmp MV, scg_cuentas CC ".
              " WHERE (MV.sc_cuenta = CC.sc_cuenta)  ".
	          "        ".$ls_cad_where." ".
			  " ORDER BY MV.debhab,MV.sc_cuenta ";
///		print $ls_sql."<br><br><br>";
		$rs_data=$this->SQL->select($ls_sql);
		if($rs_data===false)
		{   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_data))
			{
			  $this->dts_reporte->data=$this->SQL->obtener_datos($rs_data);			
			}
/*			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_procede=$row["procede"]; 
			   $ls_comprobante=$row["comprobante"];
			   $ldt_fecha=$row["fecha"];
			   $ls_sc_cuenta=$row["sc_cuenta"];
			   $ls_procede_doc=$row["procede_doc"];
			   $ls_documento=$row["documento"];
			   $ls_debhab=$row["debhab"];
			   $ls_descripcion=$row["descripcion"];
			   $ld_monto=$row["monto"];
			   $ls_denominacion=$row["denominacion"];
			   $ls_tipo_destino=$row["tipo_destino"];
			   $ls_cod_pro=$row["cod_pro"];
			   $ls_ced_bene=$row["ced_bene"];
			   $ls_nompro=$row["nompro"];
			   $ls_apebene=$row["apebene"];
			   $ls_nombene=$row["nombene"];
			   $ls_CMP_descripcion=$row["cmp_descripcion"];
			   
			   $this->dts_reporte->insertRow("procede",$ls_procede);
			   $this->dts_reporte->insertRow("comprobante",$ls_comprobante);
			   $this->dts_reporte->insertRow("fecha",$ldt_fecha);
			   $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_reporte->insertRow("procede_doc",$ls_procede_doc);
			   $this->dts_reporte->insertRow("documento",$ls_documento);
			   $this->dts_reporte->insertRow("debhab",$ls_debhab);
			   $this->dts_reporte->insertRow("descripcion",$ls_descripcion);
			   $this->dts_reporte->insertRow("monto",$ld_monto);
			   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte->insertRow("tipo_destino",$ls_tipo_destino);
			   $this->dts_reporte->insertRow("cod_pro",$ls_cod_pro);
			   $this->dts_reporte->insertRow("ced_bene",$ls_ced_bene);
			   $this->dts_reporte->insertRow("nompro",$ls_nompro);
			   $this->dts_reporte->insertRow("apebene",$ls_apebene);
			   $this->dts_reporte->insertRow("nombene",$ls_nombene);
			   $this->dts_reporte->insertRow("CMP_descripcion",$ls_CMP_descripcion);
			   $lb_valido = true;
			}//while*/
			$this->SQL->free_result($rs_data);
		}//else
  return $lb_valido;
 }//uf_spg_reporte_comprobante_formato2
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_select_comprobante_formato2($as_spg_cuenta_ori,$as_spg_cuenta_des,$adt_fecini,$adt_fecfin,$ai_orden) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_comprobante_formato2
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta_ori  // cuenta origen
	 //                     $as_spg_cuenta_des  // cuenta destino
	 //                     $adt_fecini  // fecha  desde 
     //              	    $adt_fecfin  // fecha hasta 
	 //                     $ai_orden  // orden de la consulta       
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Comprobante Formato 2  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    23/04/2006          Fecha ltima Modificacion :24/04/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      
	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_cab->resetds("comprobante");
	  
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
        /* $ls_cad_filtro1=" , (SELECT cmp1.comprobante,cmp1.fecha,cmp1.procede ".
                         "    FROM   sigesp_cmp cmp1, scg_dt_cmp mv1 ".
                         "    WHERE  cmp1.procede=mv1.procede AND cmp1.comprobante=mv1.comprobante AND ".
	                     "           cmp1.fecha=mv1.fecha AND mv1.sc_cuenta between '".$as_spg_cuenta_ori."' AND '".$as_spg_cuenta_des."' ".
                         "    GROUP BY cmp1.comprobante,cmp1.fecha,cmp1.procede) as curFiltro ";	  
		 
		 $ls_cad_filtro2=" AND MV.comprobante=curFiltro.comprobante ".
                         " AND MV.fecha=curFiltro.fecha ".
                         " AND MV.procede=curFiltro.procede ";	*/			 
	  		$ls_cad_filtro2=" AND MV.sc_cuenta between '".$as_spg_cuenta_ori."' AND '".$as_spg_cuenta_des."' ";
	  } 
      else
	  {
	  		$ls_cad_filtro2="";
	   /*  $ls_cad_filtro1=" , (SELECT cmp1.comprobante,cmp1.fecha,cmp1.procede ".
                         "    FROM   sigesp_cmp cmp1, scg_dt_cmp mv1 ".
                         "    WHERE cmp1.procede=mv1.procede and cmp1.comprobante=mv1.comprobante and cmp1.fecha=mv1.fecha ".
                         "    GROUP BY cmp1.comprobante,cmp1.fecha,cmp1.procede) as curFiltro ";
        
		 $ls_cad_filtro2=" AND MV.comprobante=curFiltro.comprobante ".
                         " AND MV.fecha=curFiltro.fecha ".
                         " AND MV.procede=curFiltro.procede ";	*/
	  }
       
	  if ((!empty($adt_fecini)) && (!empty($adt_fecfin)))
	  {
	     $ls_cad_where3=" AND MV.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' ";
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
	  if($ai_orden==1)  
	  {
    	  $ls_orden="CMP.procede,CMP.comprobante,CMP.fecha";
	  }
	  if($ai_orden==2)  
	  {
    	  $ls_orden="CMP.comprobante,CMP.fecha,CMP.procede";
	  }
	  if($ai_orden==3)  
	  {
    	  $ls_orden="CMP.fecha,CMP.procede,CMP.comprobante";
	  }
	  
	  $ls_sql=" SELECT CMP.comprobante, CMP.procede, CMP.fecha, MAX(CMP.tipo_destino) AS tipo_destino,  MAX(CMP.cod_pro) AS cod_pro,  MAX(CMP.ced_bene) AS ced_bene, ".
              "         MAX(PRV.nompro) AS nompro,  MAX(BEN.apebene) AS apebene,  MAX(BEN.nombene) AS nombene,  MAX(MV.orden) AS orden, ".
			  "			MAX(CMP.descripcion) AS descripcion ".
              " FROM   scg_dt_cmp MV, scg_cuentas CC, sigesp_cmp CMP, rpc_proveedor PRV, rpc_beneficiario BEN  ".
              " WHERE CMP.codemp = '".$ls_codemp."' ".
	          "        ".$ls_cad_where3." ".$ls_cad_filtro2." ".
			  "    AND MV.codemp = CC.codemp ".
			  "    AND MV.sc_cuenta = CC.sc_cuenta ".
			  "    AND MV.codemp=CMP.codemp ".
			  "    AND MV.procede=CMP.procede ".
			  "    AND MV.comprobante=CMP.comprobante ".
			  "    AND MV.fecha=CMP.fecha ".
			  "    AND CMP.codemp=PRV.codemp ".
			  "    AND CMP.cod_pro=PRV.cod_pro ".
			  "    AND CMP.codemp=BEN.codemp ".
			  "    AND CMP.ced_bene=BEN.ced_bene ".
			  " GROUP BY CMP.comprobante,CMP.procede,CMP.fecha ".
			  " ORDER BY  ".$ls_orden." ";
	$rs_data=$this->SQL->select($ls_sql);
	if($rs_data===false)
	{   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_comprobante_formato2 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	}
	else
	{
		if($row=$this->SQL->fetch_row($rs_data))
		{
		  $datos=$this->SQL->obtener_datos($rs_data);
		  $this->dts_cab->data=$datos;
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);   
     }//else
		return $lb_valido;
}//uf_scg_reporte_select_comprobante_formato2
/****************************************************************************************************************************************/	
    //////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SCG  " ESTADO DE RESULTADO  "              // 
	////////////////////////////////////////////////////////////////
   function  uf_scg_reporte_estado_de_resultado_ingreso($adt_fecini,$adt_fecfin,$ai_nivel) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	  $lb_valido = false;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("sc_cuenta");
      $li_ingreso = trim($this->dts_empresa["ingreso"]);
      $li_gasto   = trim($this->dts_empresa["gasto"]);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;	
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {	   
		$ls_sql="SELECT SC.sc_cuenta, SC.status, SC.denominacion,  
                        (SELECT sum(haber_mes-debe_mes) as saldo 
          				  FROM scg_saldos 
                          WHERE codemp='".$ls_codemp."'
                          AND fecsal between  '".$adt_fecini."' AND '".$adt_fecfin."' 
                          AND sc_cuenta=SC.sc_cuenta
          				GROUP BY codemp, sc_cuenta) as saldo,". 
						$ls_cadena1. "as nivel,". 
						$ls_cadena2."as total_ingresos,". 
						$ls_cadena3."as total_egresos 
					FROM scg_cuentas SC
					where (SC.sc_cuenta like '".$li_ingreso."%') AND (SC.nivel<='".$ai_nivel."')
					ORDER BY SC.sc_cuenta";
	   }
	   else
	   {
		$ls_sql=" SELECT SC.sc_cuenta, SC.status, SC.denominacion,  curSaldo.saldo, ".
	          "        ".$ls_cadena." as nivel, ".$ls_cadena." as total_ingresos, ".
              "       ".$ls_cadena." as total_egresos ".
              " FROM   scg_cuentas SC, (SELECT sc_cuenta, codemp, sum(haber_mes-debe_mes) as saldo ".
		      "                         FROM   scg_saldos ".
		      "                         WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		      "                         GROUP BY codemp, sc_cuenta) as curSaldo ".
              " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp=curSaldo.codemp) AND ".
			  "       (SC.sc_cuenta like '".$li_ingreso."%') AND  (SC.nivel<='".$ai_nivel."') ".
              " ORDER BY SC.sc_cuenta ";
		}	

	
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
	    $ld_total_ingresos=0;
		$lb_valido=$this->uf_scg_reporte_select_saldo_ingreso($adt_fecini,$adt_fecfin,$li_ingreso,$ld_total_ingresos);
	    if($lb_valido)
	    {
			$lb_valido = false;
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_sc_cuenta=$row["sc_cuenta"];
			   $ls_status=$row["status"];
			   $ls_denominacion=$row["denominacion"];
			   $ld_saldo=$row["saldo"];
			   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);			   
			   $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_reporte->insertRow("status",$ls_status);
			   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte->insertRow("saldo",$ld_saldo);
			   $this->dts_reporte->insertRow("nivel",$ls_nivel);
			   $this->dts_reporte->insertRow("total_ingresos",$ld_total_ingresos);
			   $lb_valido = true;
		   }//while   
		 }//if
		$this->SQL->free_result($rs_data);
	}//else		   
	return $lb_valido;
   }


   function  uf_scg_reporte_estado_de_resultado_est_ingreso($adt_fecini,$adt_fecfin,$ai_nivel,$arrestructuras) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
   	
   	 //$ls_concat_programatica="PCT.codestpro1||PCT.codestpro2||PCT.codestpro3||PCT.codestpro4||PCT.codestpro5||PCT.estcla";
   	  $this->dts_reporte->reset_ds();
   	  $lb_valido=true;
	  $this->dts_egresos=new class_datastore();
	  $ls_codemp = $_SESSION["la_empresa"]["codemp"];
      //$li_gasto = trim($this->dts_empresa["gasto"]);
      $li_ingreso = "5";
      $li_gasto   = "6";
      
	  switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;	
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {	   
		$ls_sql="SELECT SC.sc_cuenta, SC.status, SC.denominacion,  
                        (SELECT sum(haber_mes-debe_mes) as saldo 
          				  FROM scg_saldos 
                          WHERE codemp='".$ls_codemp."'
                          AND fecsal between  '".$adt_fecini."' AND '".$adt_fecfin."' 
                          AND sc_cuenta=SC.sc_cuenta
          				GROUP BY codemp, sc_cuenta) as saldo,". 
						$ls_cadena1. "as nivel,". 
						$ls_cadena2."as total_ingresos,". 
						$ls_cadena3."as total_egresos 
					FROM scg_cuentas SC
					where (SC.sc_cuenta like '".$li_ingreso."%') AND (SC.nivel<='".$ai_nivel."')
					ORDER BY SC.sc_cuenta"; 
	   }
	   else
	   {
			
		$ls_sql="select '' as status,'' as nivel, sum(dtpre.monto) as saldo,scg_cuentas.sc_cuenta
					,scg_cuentas.denominacion as denominacion ,{$ls_cadena} as total_ingresos 
					from spi_dt_cmp as dtpre
					inner join
					spi_cuentas_estructuras on 
					dtpre.codestpro1 = spi_cuentas_estructuras.codestpro1 
					and dtpre.codestpro2 = spi_cuentas_estructuras.codestpro2
					and dtpre.codestpro3 = spi_cuentas_estructuras.codestpro3
					and dtpre.codestpro4 = spi_cuentas_estructuras.codestpro4 
					and dtpre.codestpro5 = spi_cuentas_estructuras.codestpro5   
					and dtpre.estcla = spi_cuentas_estructuras.estcla   
					and dtpre.spi_cuenta = spi_cuentas_estructuras.spi_cuenta
					inner join spi_cuentas on spi_cuentas_estructuras.spi_cuenta=spi_cuentas.spi_cuenta
					inner join scg_cuentas on spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					where dtpre.codestpro1='{$arrestructuras[0]}' 
					AND dtpre.codestpro2='{$arrestructuras[1]}' AND dtpre.codestpro3='{$arrestructuras[2]}' 
					AND dtpre.codestpro4='{$arrestructuras[3]}' AND dtpre.codestpro5='{$arrestructuras[4]}' 
					AND dtpre.estcla='{$arrestructuras[5]}' and fecha 
					between '{$adt_fecini}' AND '{$adt_fecfin}'
					AND (scg_cuentas.sc_cuenta like '{$li_ingreso}%')
					and operacion  in (select operacion from spi_operaciones where devengado=1)  
					AND spi_cuentas_estructuras.estcla='{$arrestructuras[5]}' AND spi_cuentas.status='C'
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion";	
				

	   }
	

	//echo $ls_sql;//INGRESOS EP INGRESO DETALLADO
//	die;



	 $rs_data=$this->SQL->select($ls_sql);
	// var_dump($rs_data);
	// die();
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
	 	
	 	if($rs_data->RecordCount()==0)
	 	{
	 		
	 		$rs_data="";
	 		$ls_sql="select '' as status,'' as nivel, 0 as saldo,scg_cuentas.sc_cuenta
					,scg_cuentas.denominacion as denominacion ,{$ls_cadena} as total_ingresos 
					from spi_cuentas_estructuras as dtpre  
					inner join spi_cuentas on dtpre.spi_cuenta=spi_cuentas.spi_cuenta
					inner join scg_cuentas on spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					where dtpre.codestpro1='{$arrestructuras[0]}' 
					AND dtpre.codestpro2='{$arrestructuras[1]}' AND dtpre.codestpro3='{$arrestructuras[2]}' 
					AND dtpre.codestpro4='{$arrestructuras[3]}' AND dtpre.codestpro5='{$arrestructuras[4]}' 
					AND dtpre.estcla='{$arrestructuras[5]}' 
					AND (scg_cuentas.sc_cuenta like '{$li_ingreso}%') and
					dtpre.estcla='{$arrestructuras[5]}' AND spi_cuentas.status='C'
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion"; 
	 			
				 	 $rs_data=$this->SQL->select($ls_sql);
				 	 
				// var_dump($rs_data);
				// die();
				 if($rs_data===false)
				 {   // error interno sql
						$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
						$lb_valido = false;
				 }		 
		}
	 	
	 	
	
	    $ld_total_ingresos=0;
	    $lb_valido=$this->uf_scg_reporte_select_saldo_ingreso_est($adt_fecini,$adt_fecfin,$li_ingreso,$ld_total_ingresos,$arrestructuras);
	    if($lb_valido)
	    {
			$lb_valido = false;
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_sc_cuenta=$row["sc_cuenta"];
			   $ls_status=$row["status"];
			   $ls_denominacion=$row["denominacion"];
			   $ld_saldo=$row["saldo"];
			   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);			   
			   $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_reporte->insertRow("status",$ls_status);
			   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte->insertRow("saldo",$ld_saldo);
			   $this->dts_reporte->insertRow("nivel",$ls_nivel);
			   $this->dts_reporte->insertRow("total_ingresos",$ld_total_ingresos);
			   $lb_valido = true;
		   }//while   
		 }//if
		// var_dumop
		$this->SQL->free_result($rs_data);
	}//else		   
	return $lb_valido;
   }

   
   
   
   function  uf_scg_reporte_estado_de_resultado_est_ingreso_consolidado($adt_fecini,$adt_fecfin,$ai_nivel,$ArJson,$todas) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
   	
   	  $lb_valido=true;
	  $this->dts_egresos=new class_datastore();
	  $ls_codemp = $_SESSION["la_empresa"]["codemp"];
      $li_ingreso = "5";
      $li_gasto   = "6";
      
	  switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;	
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}



		if($todas!=true)
		{

		$con1 = "";
		$con2 = "(";
		$con3 = "(";
		$con4 = "(";
		$con5 = "(";
		$con6 = "(";
		$auxEst="";
		$ls_sql= "(";		

		for($i=0;$i<count((array)$ArJson->estructuras);$i++)
		{

				if($i<(count((array)$ArJson->estructuras)) && $i>0)
				{
					$con1.=" OR ";	
				}
				$con1.="(dtpre.codestpro1='".str_pad($ArJson->estructuras[$i]->codestpro1,25,"0",STR_PAD_LEFT)."' and "; 
				$con1.="dtpre.codestpro2='".str_pad($ArJson->estructuras[$i]->codestpro2,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro3='".str_pad($ArJson->estructuras[$i]->codestpro3,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro4='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro5='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.estcla='".$ArJson->estructuras[$i]->estcla."' )";		
		}	

			$ls_sql.="(select '' as status,'' as nivel, sum(dtpre.monto) as saldo,scg_cuentas.sc_cuenta
					,scg_cuentas.denominacion as denominacion ,{$ls_cadena} as total_ingresos 
					from spi_dt_cmp as dtpre
					inner join
					spi_cuentas_estructuras on 
					dtpre.codestpro1 = spi_cuentas_estructuras.codestpro1 
					and dtpre.codestpro2 = spi_cuentas_estructuras.codestpro2
					and dtpre.codestpro3 = spi_cuentas_estructuras.codestpro3
					and dtpre.codestpro4 = spi_cuentas_estructuras.codestpro4 
					and dtpre.codestpro5 = spi_cuentas_estructuras.codestpro5   
					and dtpre.estcla = spi_cuentas_estructuras.estcla   
					and dtpre.spi_cuenta = spi_cuentas_estructuras.spi_cuenta
					inner join spi_cuentas on spi_cuentas_estructuras.spi_cuenta=spi_cuentas.spi_cuenta
					inner join scg_cuentas on spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					where 
					({$con1} )
					 and fecha 
					between '{$adt_fecini}' AND '{$adt_fecfin}'
					AND (scg_cuentas.sc_cuenta like '{$li_ingreso}%')
					and operacion  in (select operacion from spi_operaciones where devengado=1)  
					AND spi_cuentas.status='C'
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion)";	
				

			$ls_sql.=") as cursor";
    		 $ls_sql = "select sum(cursor.saldo) as saldo,cursor.sc_cuenta,
    			cursor.denominacion,'' as status,'' as nivel from {$ls_sql} group by 
			    cursor.sc_cuenta,cursor.denominacion
				order by cursor.sc_cuenta asc";
		}
			
		
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {	   
		$ls_sql="SELECT SC.sc_cuenta, SC.status, SC.denominacion,  
                        (SELECT sum(haber_mes-debe_mes) as saldo 
          				  FROM scg_saldos 
                          WHERE codemp='".$ls_codemp."'
                          AND fecsal between  '".$adt_fecini."' AND '".$adt_fecfin."' 
                          AND sc_cuenta=SC.sc_cuenta
          				GROUP BY codemp, sc_cuenta) as saldo,". 
						$ls_cadena1. "as nivel,". 
						$ls_cadena2."as total_ingresos,". 
						$ls_cadena3."as total_egresos 
					FROM scg_cuentas SC
					where (SC.sc_cuenta like '".$li_ingreso."%') AND (SC.nivel<='".$ai_nivel."')
					ORDER BY SC.sc_cuenta";
	   }
	   else
	   {
	   	if($todas===true)
	   	{
			$ls_sql="select '' as status,'' as nivel, sum(dtpre.monto) as saldo,scg_cuentas.sc_cuenta
					,scg_cuentas.denominacion as denominacion ,{$ls_cadena} as total_ingresos 
					from spi_dt_cmp as dtpre
					inner join
					spi_cuentas_estructuras on 
					dtpre.codestpro1 = spi_cuentas_estructuras.codestpro1 
					and dtpre.codestpro2 = spi_cuentas_estructuras.codestpro2
					and dtpre.codestpro3 = spi_cuentas_estructuras.codestpro3
					and dtpre.codestpro4 = spi_cuentas_estructuras.codestpro4 
					and dtpre.codestpro5 = spi_cuentas_estructuras.codestpro5   
					and dtpre.estcla = spi_cuentas_estructuras.estcla   
					and dtpre.spi_cuenta = spi_cuentas_estructuras.spi_cuenta
					inner join spi_cuentas on spi_cuentas_estructuras.spi_cuenta=spi_cuentas.spi_cuenta
					inner join scg_cuentas on spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					where fecha 
					between '{$adt_fecini}' AND '{$adt_fecfin}'
					AND (scg_cuentas.sc_cuenta like '{$li_ingreso}%')
					and operacion  in (select operacion from spi_operaciones where devengado=1)  
					AND spi_cuentas.status='C'
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion";	
	   	
	   	}	
	  }
	
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
	 	
	 	if($rs_data->RecordCount()==0 && $todas!=true)
	 	{
	 		
	 	$con1 = "";
		$con2 = "(";
		$con3 = "(";
		$con4 = "(";
		$con5 = "(";
		$con6 = "(";
		$auxEst="";
		$ls_sql= "(";
		for($i=0;$i<count((array)$ArJson->estructuras);$i++)
		{
				if($i<(count((array)$ArJson->estructuras)) && $i>0)
				{
					$ls_sql.=" union ";	
				}
				
				$con1="dtpre.codestpro1='".str_pad($ArJson->estructuras[$i]->codestpro1,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro2='".str_pad($ArJson->estructuras[$i]->codestpro2,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro3='".str_pad($ArJson->estructuras[$i]->codestpro3,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro4='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro5='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.estcla='".$ArJson->estructuras[$i]->estcla."' ";	
					
				
				$ls_sql.="(select '' as status,'' as nivel, 0 as saldo,scg_cuentas.sc_cuenta
					,scg_cuentas.denominacion as denominacion ,{$ls_cadena} as total_ingresos 
					from spi_cuentas_estructuras as dtpre  
					inner join spi_cuentas on dtpre.spi_cuenta=spi_cuentas.spi_cuenta
					inner join scg_cuentas on spi_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
					where {$con1}  
					AND (scg_cuentas.sc_cuenta like '{$li_ingreso}%') and
					 spi_cuentas.status='C'
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion)";	
				
				
		}	
			$ls_sql.=") as cursor";
    		$ls_sql = "select sum(cursor.saldo) as saldo,cursor.sc_cuenta,
    			cursor.denominacion,'' as status,'' as nivel from {$ls_sql} group by 
			    cursor.sc_cuenta,cursor.denominacion
				order by cursor.sc_cuenta asc";
    		
			 $rs_data=$this->SQL->select($ls_sql);//echo $ls_sql; die;
			// var_dump($rs_data);
			// die();
			 if($rs_data===false)
			 {   // error interno sql
					$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
					$lb_valido = false;
			 }
    		
	 		
	 	}
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	 	
	    $ld_total_ingresos=0;
	    $lb_valido=true;
	    //$lb_valido=$this->uf_scg_reporte_select_saldo_ingreso_est($adt_fecini,$adt_fecfin,$li_ingreso,$ld_total_ingresos,$arrestructuras);
	    if($lb_valido)
	    {
			$lb_valido = false;
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_sc_cuenta=$row["sc_cuenta"];
			   $ls_status=$row["status"];
			   $ls_denominacion=$row["denominacion"];
			   $ld_saldo=$row["saldo"];
			   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);			   
			   $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_reporte->insertRow("status",$ls_status);
			   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			   $this->dts_reporte->insertRow("saldo",$ld_saldo);
			   $this->dts_reporte->insertRow("nivel",$ls_nivel);
			   $this->dts_reporte->insertRow("total_ingresos",$ld_total_ingresos);
			   $lb_valido = true;
		   }//while   
		 }//if
		$this->SQL->free_result($rs_data);
	}//else		   
	return $lb_valido;
   }
   
   
      
   
   function uf_scg_reporte_estado_de_resultado_ingreso_consolidado($adt_fecini,$adt_fecfin) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. N??stor Falc??n
	 // Fecha Creaci? :    07/04/2009          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	  $lb_valido = false;
	  $this->dts_reporte->resetds("sc_cuenta");
      $li_ingreso = trim($this->dts_empresa["ingreso"]);
      $li_gasto   = trim($this->dts_empresa["gasto"]);
	  switch($_SESSION["ls_gestor"])
	  {
	    case "MYSQLT":
		  $ls_cadena = "cast(0 as UNSIGNED ) ";
		break;
		case "POSTGRES":
		  $ls_cadena = "CAST(0 AS int2) ";
		break;	
		case "INFORMIX":
		  $ls_cadena1 = "CAST(0 AS smallint) ";
		  $ls_cadena2 = "CAST(0 AS float) ";
	      $ls_cadena3 = "CAST(0 AS float) ";
		break;					
	  }
	  if ($_SESSION["ls_gestor"]=='INFORMIX')
	     {	   
		   $ls_sql = "SELECT trim(scg_cuentas_consolida.sc_cuenta) as sc_cuenta, scg_cuentas_consolida.denominacion,  
                             (SELECT sum(haber_mes-debe_mes) as saldo 
          				        FROM scg_saldos_consolida 
                               WHERE fecsal BETWEEN  '".$adt_fecini."' AND '".$adt_fecfin."' 
                                 AND sc_cuenta=scg_cuentas_consolida.sc_cuenta
          				       GROUP BY sc_cuenta) as saldo,
						     $ls_cadena1 as nivel,$ls_cadena2 as total_ingresos,$ls_cadena3 as total_egresos 
					    FROM scg_cuentas_consolida
					   WHERE scg_cuentas_consolida.sc_cuenta like '".$li_ingreso."%' 
					   ORDER BY sc_cuenta";
	     }
	  else
	     {
		   $ls_sql = "SELECT trim(scg_cuentas_consolida.sc_cuenta) as sc_cuenta, 
						     MAX(scg_cuentas_consolida.denominacion) as denominacion, 
						     MAX(curSaldo.saldo) AS saldo, 
						     CAST(0 AS int2) as nivel,
						     CAST(0 AS int2) as total_ingresos,
						     CAST(0 AS int2) as total_egresos 
					    FROM scg_cuentas_consolida, (SELECT sc_cuenta, sum(haber_mes-debe_mes) as saldo 
						                               FROM scg_saldos_consolida 
													  WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' 
													  GROUP BY sc_cuenta) as curSaldo 
					   WHERE scg_cuentas_consolida.sc_cuenta like '".$li_ingreso."%' 
					     AND trim(scg_cuentas_consolida.sc_cuenta) = trim(curSaldo.sc_cuenta) 
					   GROUP BY scg_cuentas_consolida.sc_cuenta 
					   ORDER BY scg_cuentas_consolida.sc_cuenta;";
		 }
	  
	  $rs_data=$this->SQL->select($ls_sql);
	  if ($rs_data===false)
	     {
		   $this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso_consolidado ".$this->fun->uf_convertirmsg($this->SQL->message);
		   $lb_valido = false;
	     }
	  else
	     {
	       $ld_total_ingresos=0;
		   $lb_valido = $this->uf_scg_reporte_select_saldo_ingreso_consolidado($adt_fecini,$adt_fecfin,$li_ingreso,$ld_total_ingresos);
	       if ($lb_valido)
	          {
			    $lb_valido = false;
				while($row=$this->SQL->fetch_row($rs_data))
				     {
					   $ls_sc_cuenta=$row["sc_cuenta"];
					   $ls_denominacion=$row["denominacion"];
					   $ld_saldo=$row["saldo"];
					   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);			   
					   $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
					   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
					   $this->dts_reporte->insertRow("saldo",$ld_saldo);
					   $this->dts_reporte->insertRow("nivel",$ls_nivel);
					   $this->dts_reporte->insertRow("total_ingresos",$ld_total_ingresos);
					   $lb_valido = true;
		             }
		      }
		   $this->SQL->free_result($rs_data);
	     }
	  return $lb_valido;
   }//fin uf_scg_reporte_estado_de_resultado_ingreso
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_estado_de_resultado_egreso($adt_fecini,$adt_fecfin,$ai_nivel) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado_egreso
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
      $li_gasto = trim($this->dts_empresa["gasto"]);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	     $ls_sql="SELECT SC.sc_cuenta, SC.status, SC.denominacion,  
                        (SELECT sum(haber_mes-debe_mes) as saldo 
          				  FROM scg_saldos 
                          WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."'  
                          AND sc_cuenta=SC.sc_cuenta
          				GROUP BY codemp, sc_cuenta) as saldo,". 
						$ls_cadena1. "as nivel,". 
						$ls_cadena2."as total_ingresos,". 
						$ls_cadena3."as total_egresos 
					FROM scg_cuentas SC
					where (SC.sc_cuenta like '".$li_gasto."%') AND (SC.nivel<='".$ai_nivel."')
					ORDER BY SC.sc_cuenta"; 
		}
	   else
	    {
	    $ls_sql=" SELECT SC.sc_cuenta, SC.status, SC.denominacion,  curSaldo.saldo, ".
	          "        ".$ls_cadena." as nivel,  ".$ls_cadena." as total_ingresos, ".
              "         ".$ls_cadena." as total_egresos ".
              " FROM   scg_cuentas SC, (SELECT sc_cuenta, codemp, sum(haber_mes-debe_mes) as saldo ".
		      "                         FROM   scg_saldos ".
		      "                         WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		      "                         GROUP BY codemp, sc_cuenta) as curSaldo ".
              " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp=curSaldo.codemp) AND ".
			  "       (SC.sc_cuenta like '".$li_gasto."%') AND (SC.nivel<='".$ai_nivel."') ".
              " ORDER BY SC.sc_cuenta ";
		}	 
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_egreso ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
		$ld_total_egresos=0;
		$lb_valido=$this->uf_scg_reporte_select_saldo_gasto($adt_fecini,$adt_fecfin,$li_gasto,$ld_total_egresos);
	    if($lb_valido)
	    {
			$lb_valido = false;
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_sc_cuenta=$row["sc_cuenta"];
			   $ls_status=$row["status"];
			   $ls_denominacion=$row["denominacion"];
			   $ld_saldo=$row["saldo"];
			   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);
			   $this->dts_egresos->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_egresos->insertRow("status",$ls_status);
			   $this->dts_egresos->insertRow("denominacion",$ls_denominacion);
			   $this->dts_egresos->insertRow("saldo",$ld_saldo);
			   $this->dts_egresos->insertRow("nivel",$ls_nivel);
			   $this->dts_egresos->insertRow("total_egresos",$ld_total_egresos);
			   $lb_valido = true;
		   }//while   
		}//if
		$this->SQL->free_result($rs_data);
	 }//else	  
	  return $lb_valido;
   }


   /****************************************************************************************************************************************/	
function  uf_scg_reporte_estado_de_resultado_est_egreso($adt_fecini,$adt_fecfin,$ai_nivel,$arrestructuras) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado_egreso
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $this->dts_egresos=new class_datastore();
	  $lb_valido = true;
	  $ls_codemp = $_SESSION["la_empresa"]["codemp"];
      //$li_gasto = trim($this->dts_empresa["gasto"]);
      $li_ingreso = "5";
      $li_gasto   = "6";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}
	 if($_SESSION["ls_gestor"]=='INFORMIX')
	 {
	     $ls_sql="SELECT SC.sc_cuenta, SC.status, SC.denominacion,  
                        (SELECT sum(haber_mes-debe_mes) as saldo 
          				  FROM scg_saldos 
                          WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."'  
                          AND sc_cuenta=SC.sc_cuenta
          				GROUP BY codemp, sc_cuenta) as saldo,". 
						$ls_cadena1. "as nivel,". 
						$ls_cadena2."as total_ingresos,". 
						$ls_cadena3."as total_egresos 
					FROM scg_cuentas SC
					where (SC.sc_cuenta like '".$li_gasto."%') AND (SC.nivel<='".$ai_nivel."')
					ORDER BY SC.sc_cuenta"; 
	 }
	 else
	 {
	 		
	 	
	 	/*
	    $ls_sql=" SELECT SC.sc_cuenta, SC.status, SC.denominacion, curSaldo.saldo, ".
	          "        ".$ls_cadena." as nivel,  ".$ls_cadena." as total_ingresos, ".
              "         ".$ls_cadena." as total_egresos ".
              " FROM   scg_cuentas SC, (SELECT sc_cuenta, codemp, sum(haber_mes-debe_mes) as saldo ".
		      "                         FROM   scg_saldos ".
		      "                         WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		      "                         GROUP BY codemp, sc_cuenta) as curSaldo ".
              " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp=curSaldo.codemp) AND ".
			  "       (SC.sc_cuenta like '".$li_gasto."%') AND (SC.nivel<='".$ai_nivel."') ".
              " ORDER BY SC.sc_cuenta ";  	    
		*/
	    
	   /* 
	    $ls_sql="SELECT spg_cuentas.*,SC.sc_cuenta, SC.status, SC.denominacion, curSaldo.saldo, 
				{$ls_cadena} as nivel, {$ls_cadena} as total_ingresos, 
				{$ls_cadena} as total_egresos FROM scg_cuentas SC, 
				(SELECT sc_cuenta, codemp, sum(haber_mes-debe_mes) as saldo 
				FROM scg_saldos WHERE codemp='{$ls_codemp}' 
				AND fecsal between '{$adt_fecini}' 
				AND '{$adt_fecfin}' GROUP BY codemp, sc_cuenta) as curSaldo,spg_cuentas 
				WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) 
				AND (SC.codemp=curSaldo.codemp)
				AND spg_cuentas.sc_cuenta=curSaldo.sc_cuenta  
				AND (SC.sc_cuenta like '{$li_gasto}%') 
				AND spg_cuentas.codestpro1='{$arrestructuras[0]}' 
				AND spg_cuentas.codestpro2='{$arrestructuras[1]}' 
				AND spg_cuentas.codestpro3='{$arrestructuras[2]}' 
				AND spg_cuentas.codestpro4='{$arrestructuras[3]}' 
				AND spg_cuentas.codestpro5='{$arrestructuras[4]}' 
				AND spg_cuentas.estcla='{$arrestructuras[5]}'
				AND spg_cuentas.status='C'
				ORDER BY SC.sc_cuenta";		
		*/
		$ls_sql="select '' as status,'' as nivel, sum(dtpre.monto) as saldo,scg_cuentas.sc_cuenta,scg_cuentas.denominacion as denominacion from spg_dt_cmp as dtpre
				inner join
				spg_cuentas on 
				dtpre.codestpro1 = spg_cuentas.codestpro1 
				and dtpre.codestpro2 = spg_cuentas.codestpro2
				and dtpre.codestpro3 = spg_cuentas.codestpro3
				and dtpre.codestpro4 = spg_cuentas.codestpro4 
				and dtpre.codestpro5 = spg_cuentas.codestpro5   
				and dtpre.estcla = spg_cuentas.estcla   
				and dtpre.spg_cuenta = spg_cuentas.spg_cuenta
				inner join scg_cuentas on spg_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
				where dtpre.codestpro1='{$arrestructuras[0]}' 
				AND dtpre.codestpro2='{$arrestructuras[1]}' AND dtpre.codestpro3='{$arrestructuras[2]}' 
				AND dtpre.codestpro4='{$arrestructuras[3]}' AND dtpre.codestpro5='{$arrestructuras[4]}' 
				AND dtpre.estcla='{$arrestructuras[5]}'
				 and fecha 
				between '{$adt_fecini}' AND '{$adt_fecfin}'
				and operacion  in (select operacion from spg_operaciones where causar=1)  
				 AND spg_cuentas.status='C'
				AND (scg_cuentas.sc_cuenta like '{$li_gasto}%')		
				group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion
				order by scg_cuentas.sc_cuenta";
					
	 }


  //echo $ls_sql; //KARINA EGRESO DETALLADO
	//die;
	 	
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_egreso ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
	 	if($rs_data->RecordCount()==0)
	 	{
		 		$ls_sql="select '' as status,'' as nivel, 0 as saldo,scg_cuentas.sc_cuenta,scg_cuentas.denominacion as denominacion 
		 			from spg_cuentas as dtpre  
					inner join scg_cuentas on dtpre.sc_cuenta=scg_cuentas.sc_cuenta
					where dtpre.codestpro1='{$arrestructuras[0]}' 
					AND dtpre.codestpro2='{$arrestructuras[1]}' AND dtpre.codestpro3='{$arrestructuras[2]}' 
					AND dtpre.codestpro4='{$arrestructuras[3]}' AND dtpre.codestpro5='{$arrestructuras[4]}' 
					AND dtpre.estcla='{$arrestructuras[5]}'
					AND dtpre.status='C'
					AND (scg_cuentas.sc_cuenta like '{$li_gasto}%')		
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion
					order by scg_cuentas.sc_cuenta";

		 
		 $rs_data=$this->SQL->select($ls_sql);
		 if($rs_data===false)
		 {   // error interno sql
				$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_egreso ".$this->fun->uf_convertirmsg($this->SQL->message);
				$lb_valido = false;
		 }
	}
	 	
	 	
		$ld_total_egresos=0;
		$lb_valido = $this->uf_scg_reporte_select_saldo_gasto_est($adt_fecini,$adt_fecfin,$li_gasto,$ld_total_egresos,$arrestructuras);
	    if($lb_valido)
	    {
			$lb_valido = false;
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_sc_cuenta=$row["sc_cuenta"];
			   $ls_status=$row["status"];
			   $ls_denominacion=$row["denominacion"];
			   $ld_saldo=$row["saldo"];
			   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);
			   $this->dts_egresos->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_egresos->insertRow("status",$ls_status);
			   $this->dts_egresos->insertRow("denominacion",$ls_denominacion);
			   $this->dts_egresos->insertRow("saldo",$ld_saldo);
			   $this->dts_egresos->insertRow("nivel",$ls_nivel);
			   $this->dts_egresos->insertRow("total_egresos",$ld_total_egresos);
			   $lb_valido = true;
		   }//while   
		}//if
		$this->SQL->free_result($rs_data);
	 }//else	  
	  return $lb_valido;
}




function  uf_scg_reporte_estado_de_resultado_est_egreso_consolidado($adt_fecini,$adt_fecfin,$ai_nivel,$ArJson,$todas) 
{				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado_egreso
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $this->dts_egresos=new class_datastore();
	  $lb_valido = true;
	  $ls_codemp = $_SESSION["la_empresa"]["codemp"];
      //$li_gasto = trim($this->dts_empresa["gasto"]);
      $li_ingreso = "5";
      $li_gasto   = "6";
      if($todas!=true)
      
      {
		$con1 = "";
		$con2 = "(";
		$con3 = "(";
		$con4 = "(";
		$con5 = "(";
		$con6 = "(";
		$auxEst="";
		$ls_sql= "(";
		$ls_codestpro1 = "";
		$ls_codestpro2 = "";
		$ls_codestpro3 = "";
		$ls_codestpro4 = "";
		$ls_codestpro5 = "";
		$ls_estcla = "";
		$ls_codestpro = "";
        $li_total = count((array)$ArJson->estructuras);
		for($i=0;$i<$li_total;$i++)
		{
				$ls_codestpro1 = str_pad($ArJson->estructuras[$i]->codestpro1,25,"0",STR_PAD_LEFT);
				$ls_codestpro2 = str_pad($ArJson->estructuras[$i]->codestpro2,25,"0",STR_PAD_LEFT);
				$ls_codestpro3 = str_pad($ArJson->estructuras[$i]->codestpro3,25,"0",STR_PAD_LEFT);
				$ls_codestpro4 = str_pad("",25,"0",STR_PAD_LEFT);
				$ls_codestpro5 = str_pad("",25,"0",STR_PAD_LEFT);
				$ls_estcla = $ArJson->estructuras[$i]->estcla;	
				if($i==0)
				{
				 $ls_codestpro .= "'".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla."'";
				}
				else
				{
				 $ls_codestpro .= ",'".$ls_codestpro1.$ls_codestpro2.$ls_codestpro3.$ls_codestpro4.$ls_codestpro5.$ls_estcla."'";
				}
				
				
				
				/*if($i<$li_total && $i>0)
				{
					$ls_sql.=" union ";	
				}
				echo $ArJson->estructuras[$i]->codestpro1." - ".$ArJson->estructuras[$i]->codestpro2." - ".$ArJson->estructuras[$i]->codestpro3." - "."<br>";
				$con1="dtpre.codestpro1='".str_pad($ArJson->estructuras[$i]->codestpro1,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro2='".str_pad($ArJson->estructuras[$i]->codestpro2,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro3='".str_pad($ArJson->estructuras[$i]->codestpro3,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro4='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro5='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.estcla='".$ArJson->estructuras[$i]->estcla."' ";
				$ls_sql.="(select '' as status,'' as nivel, sum(dtpre.monto) as saldo,scg_cuentas.sc_cuenta,scg_cuentas.denominacion as denominacion from spg_dt_cmp as dtpre
				inner join
				spg_cuentas on 
				dtpre.codestpro1 = spg_cuentas.codestpro1 
				and dtpre.codestpro2 = spg_cuentas.codestpro2
				and dtpre.codestpro3 = spg_cuentas.codestpro3
				and dtpre.codestpro4 = spg_cuentas.codestpro4 
				and dtpre.codestpro5 = spg_cuentas.codestpro5   
				and dtpre.estcla = spg_cuentas.estcla   
				and dtpre.spg_cuenta = spg_cuentas.spg_cuenta
				inner join scg_cuentas on spg_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
				where 
				{$con1} 
				 and fecha 
				between '{$adt_fecini}' AND '{$adt_fecfin}'
				and operacion  in (select operacion from spg_operaciones where causar=1) 
				AND (scg_cuentas.sc_cuenta like '{$li_gasto}%')		
				group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion
				order by scg_cuentas.sc_cuenta)";*/	
		}
		$ls_cadest = "";
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadest=" CONCAT(spg_dt_cmp.codestpro1,spg_dt_cmp.codestpro2,spg_dt_cmp.codestpro3,spg_dt_cmp.codestpro4,spg_dt_cmp.codestpro5,spg_dt_cmp.estcla) ";
				break;
			case "POSTGRES":
				$ls_cadest=" spg_dt_cmp.codestpro1||spg_dt_cmp.codestpro2||spg_dt_cmp.codestpro3||spg_dt_cmp.codestpro4||spg_dt_cmp.codestpro5||spg_dt_cmp.estcla ";
				break;
		}
		
		$ls_sql = "SELECT 
						  spg_cuentas.sc_cuenta, 
						  MAX(scg_cuentas.denominacion) as denominacion, 
						  SUM(spg_dt_cmp.monto) as saldo
						FROM 
						  spg_dt_cmp, 
						  spg_cuentas, 
						  scg_cuentas, 
						  spg_operaciones
						WHERE 
						  spg_dt_cmp.codemp = spg_cuentas.codemp AND
						  spg_dt_cmp.estcla = spg_cuentas.estcla AND
						  spg_cuentas.codemp = scg_cuentas.codemp AND
						  spg_dt_cmp.operacion = spg_operaciones.operacion AND
						  spg_cuentas.sc_cuenta = scg_cuentas.sc_cuenta AND
						  spg_dt_cmp.codestpro1 = spg_cuentas.codestpro1 AND
						  spg_dt_cmp.codestpro2 = spg_cuentas.codestpro2 AND
						  spg_dt_cmp.codestpro3 = spg_cuentas.codestpro3 AND
						  spg_dt_cmp.codestpro4 = spg_cuentas.codestpro4 AND
						  spg_dt_cmp.codestpro5 = spg_cuentas.codestpro5 AND
						  spg_dt_cmp.spg_cuenta = spg_cuentas.spg_cuenta AND
						  spg_dt_cmp.codemp = '".$_SESSION["la_empresa"]["codemp"]."' AND
						  scg_cuentas.sc_cuenta LIKE '".$li_gasto."%' AND
						  spg_operaciones.causar = 1 AND
						  spg_dt_cmp.fecha BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' AND
						  ".$ls_cadest." IN (".$ls_codestpro.") 
						GROUP BY spg_cuentas.sc_cuenta
						ORDER BY spg_cuentas.sc_cuenta ";
		
	/*$ls_sql.=") as cursor";
    $ls_sql = "select sum(cursor.saldo) as saldo,cursor.sc_cuenta,
    			cursor.denominacion,'' as status,'' as nivel from {$ls_sql} group by 
			    cursor.sc_cuenta,cursor.denominacion
				order by cursor.sc_cuenta asc";*/
    }

    
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}
	 if($_SESSION["ls_gestor"]=='INFORMIX')
	 {
	     $ls_sql="SELECT SC.sc_cuenta, SC.status, SC.denominacion,  
                        (SELECT sum(haber_mes-debe_mes) as saldo 
          				  FROM scg_saldos 
                          WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."'  
                          AND sc_cuenta=SC.sc_cuenta
          				GROUP BY codemp, sc_cuenta) as saldo,". 
						$ls_cadena1. "as nivel,". 
						$ls_cadena2."as total_ingresos,". 
						$ls_cadena3."as total_egresos 
					FROM scg_cuentas SC
					where (SC.sc_cuenta like '".$li_gasto."%') AND (SC.nivel<='".$ai_nivel."')
					ORDER BY SC.sc_cuenta"; 
	 }
	 else
	 {
	 	
	 	if($todas===true)
	 	{
	 		
		$ls_sql="select '' as status,'' as nivel, sum(dtpre.monto) as saldo,scg_cuentas.sc_cuenta,scg_cuentas.denominacion as denominacion from spg_dt_cmp as dtpre
				inner join
				spg_cuentas on 
				dtpre.codestpro1 = spg_cuentas.codestpro1 
				and dtpre.codestpro2 = spg_cuentas.codestpro2
				and dtpre.codestpro3 = spg_cuentas.codestpro3
				and dtpre.codestpro4 = spg_cuentas.codestpro4 
				and dtpre.codestpro5 = spg_cuentas.codestpro5   
				and dtpre.estcla = spg_cuentas.estcla   
				and dtpre.spg_cuenta = spg_cuentas.spg_cuenta
				inner join scg_cuentas on spg_cuentas.sc_cuenta=scg_cuentas.sc_cuenta
				where 
				 fecha 
				between '{$adt_fecini}' AND '{$adt_fecfin}'
				and operacion  in (select operacion from spg_operaciones where causar=1)  
				AND spg_cuentas.status='C'
				AND (scg_cuentas.sc_cuenta like '{$li_gasto}%')		
				group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion
				order by scg_cuentas.sc_cuenta";
	 	
	 	}			
	 }
	 $rs_data=$this->SQL->select($ls_sql); 
	 //echo $ls_sql; die; karina
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_egreso ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
	 	
	 	
	 	
	 	if($rs_data->RecordCount()==0 && $todas!=true)
	 	{
	 		
	 	$con1 = "";
		$con2 = "(";
		$con3 = "(";
		$con4 = "(";
		$con5 = "(";
		$con6 = "(";
		$auxEst="";
		$ls_sql= "(";
		$li_total = count((array)$ArJson->estructuras);
		for($i=0;$i<$li_total;$i++)
		{
				if($i<($li_total) && $i>0)
				{
					$ls_sql.=" union ";	
				}
				
				$con1="dtpre.codestpro1='".str_pad($ArJson->estructuras[$i]->codestpro1,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro2='".str_pad($ArJson->estructuras[$i]->codestpro2,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro3='".str_pad($ArJson->estructuras[$i]->codestpro3,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro4='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.codestpro5='".str_pad($auxEst,25,"0",STR_PAD_LEFT)."' and ";
				$con1.="dtpre.estcla='".$ArJson->estructuras[$i]->estcla."' ";	
					
				
				$ls_sql.="(select '' as status,'' as nivel, 0 as saldo,scg_cuentas.sc_cuenta,scg_cuentas.denominacion as denominacion 
		 			from spg_cuentas as dtpre  
					inner join scg_cuentas on dtpre.sc_cuenta=scg_cuentas.sc_cuenta
					where {$con1}
					AND dtpre.status='C'
					AND (scg_cuentas.sc_cuenta like '{$li_gasto}%')		
					group by scg_cuentas.sc_cuenta,scg_cuentas.denominacion
					order by scg_cuentas.sc_cuenta)";	
				
				
		}	
			$ls_sql.=") as cursor";
    		$ls_sql = "select sum(cursor.saldo) as saldo,cursor.sc_cuenta,
    			cursor.denominacion,'' as status,'' as nivel from {$ls_sql} group by 
			    cursor.sc_cuenta,cursor.denominacion
				order by cursor.sc_cuenta asc";
			 $rs_data=$this->SQL->select($ls_sql);
			// var_dump($rs_data);
			// die();
			 if($rs_data===false)
			 {   // error interno sql
					$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
					$lb_valido = false;
			 }	
	 		
	 	}
	 	
	 	
	
	 	
		$ld_total_egresos=0;
		$lb_valido=true;
		//$lb_valido = $this->uf_scg_reporte_select_saldo_gasto_est($adt_fecini,$adt_fecfin,$li_gasto,$ld_total_egresos,$arrestructuras);
	    if($lb_valido)
	    {
			$lb_valido = false;
			//while($row=$this->SQL->fetch_row($rs_data))
			while(!$rs_data->EOF)
			{
			   $ls_sc_cuenta=$rs_data->fields["sc_cuenta"];
			   $ls_denominacion=$rs_data->fields["denominacion"];
			   $ld_saldo=$rs_data->fields["saldo"];
			   $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);
			   $this->dts_egresos->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_egresos->insertRow("denominacion",$ls_denominacion);
			   $this->dts_egresos->insertRow("saldo",$ld_saldo);
			   $this->dts_egresos->insertRow("nivel",$ls_nivel);
			   $lb_valido = true;
			   $rs_data->MoveNext();
		   }//while   
		}//if
		$this->SQL->free_result($rs_data);
	 }//else	  
	  return $lb_valido;
}




   
   function  uf_scg_reporte_estado_de_resultado_egreso_consolidado($adt_fecini,$adt_fecfin) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado_egreso
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creacion :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
      $li_gasto = trim($this->dts_empresa["gasto"]);
		switch($_SESSION["ls_gestor"])
		{
			case "MYSQLT":
				$ls_cadena="cast(0 as UNSIGNED ) ";
				break;
			case "POSTGRES":
				$ls_cadena="CAST(0 AS int2) ";
				break;
			case "INFORMIX":
				$ls_cadena1="CAST(0 AS smallint) ";
				$ls_cadena2="CAST(0 AS float) ";
				$ls_cadena3="CAST(0 AS float) ";
				break;					
		}
	   if ($_SESSION["ls_gestor"]=='INFORMIX')
	      {
	        $ls_sql = "SELECT trim(scg_cuentas_consolida.sc_cuenta) as sc_cuenta, scg_cuentas_consolida.denominacion,  
                              (SELECT sum(haber_mes-debe_mes) as saldo 
          				  		 FROM scg_saldos_consolida 
                          		WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'  
                          		  AND sc_cuenta=scg_cuentas_consolida.sc_cuenta
          				        GROUP BY sc_cuenta) as saldo,
						      $ls_cadena1 as nivel, $ls_cadena2 as total_ingresos, $ls_cadena3 as total_egresos 
					     FROM scg_cuentas_consolida
						WHERE scg_cuentas_consolida.sc_cuenta like '".$li_gasto."%'
					    ORDER BY sc_cuenta"; 
		  }
	   else
	      {
	        $ls_sql = "SELECT trim(scg_cuentas_consolida.sc_cuenta) as sc_cuenta, 
			                  MAX(scg_cuentas_consolida.denominacion) as denominacion, 
			                  MAX(curSaldo.saldo) as saldo,
							  $ls_cadena as nivel,$ls_cadena as total_ingresos,$ls_cadena as total_egresos
                         FROM scg_cuentas_consolida, (SELECT trim(sc_cuenta) as sc_cuenta, sum(haber_mes-debe_mes) as saldo
		                                                FROM scg_saldos_consolida
		                               				   WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'
		                                               GROUP BY sc_cuenta) as curSaldo
                        WHERE scg_cuentas_consolida.sc_cuenta like '".$li_gasto."%'
						  AND trim(scg_cuentas_consolida.sc_cuenta) = trim(curSaldo.sc_cuenta)
               			GROUP BY scg_cuentas_consolida.sc_cuenta
						ORDER BY scg_cuentas_consolida.sc_cuenta";
		  }	 
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_scg_reporte_estado_de_resultado_egreso_consolidado ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
		$ld_total_egresos=0;
		$lb_valido=$this->uf_scg_reporte_select_saldo_gasto_consolidado($adt_fecini,$adt_fecfin,$li_gasto,$ld_total_egresos);
	    if($lb_valido)
	    {
			$lb_valido = false;
			while($row=$this->SQL->fetch_row($rs_data))
			{
			   $ls_sc_cuenta	= $row["sc_cuenta"];
			   $ls_denominacion = $row["denominacion"];
			   $ld_saldo		= $row["saldo"];
			   $ls_nivel        = $this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);
			   $this->dts_egresos->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_egresos->insertRow("denominacion",$ls_denominacion);
			   $this->dts_egresos->insertRow("saldo",$ld_saldo);
			   $this->dts_egresos->insertRow("nivel",$ls_nivel);
			   $this->dts_egresos->insertRow("total_egresos",$ld_total_egresos);
			   $lb_valido = true;
		   }//while   
		}//if
		$this->SQL->free_result($rs_data);
	}//else	  
	return $lb_valido;
}//fin uf_scg_reporte_estado_de_resultado_egreso
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_select_saldo_ingreso($adt_fecini,$adt_fecfin,$ai_ingreso,&$ad_total_ingresos) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_ingreso
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecfin  // fecha hasta
     //              	    $ai_ingreso  // numero de la cuenta de ingraso 
	 //                     $ad_total_ingresos  //  total de ingreso (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {		 
		$ls_sql="SELECT SC.sc_cuenta, (SELECT case sum(haber_mes-debe_mes) when null then 0
                                       else sum(haber_mes-debe_mes) end saldo
                                       FROM scg_saldos 
                                       WHERE codemp='".$ls_codemp."'
                                       AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
                                       " AND sc_cuenta=SC.sc_cuenta
                                       GROUP BY codemp, sc_cuenta) as total_ingresos
				FROM scg_cuentas SC     
				WHERE (SC.status='C') AND (SC.sc_cuenta like '".$ai_ingreso."%') ";
	   }
	   else 
	    {
	     $ls_sql=" SELECT COALESCE(sum(curSaldo.SALDO),0) as total_ingresos ".
             " FROM   scg_cuentas SC,(SELECT sc_cuenta, codemp, COALESCE(sum(haber_mes-debe_mes),0) as saldo ".
             "                        FROM   scg_saldos ".
		     "                        WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		     "                        GROUP BY codemp, sc_cuenta) as curSaldo ".
             " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp = curSaldo.codemp) AND (SC.status='C') AND ".
	         "       (SC.sc_cuenta like '".$ai_ingreso."%') AND curSaldo.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND sc_cuenta LIKE '".$ai_ingreso."%')";
	   }
	   //print $ls_sql;
	   //die();
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_total_ingresos=$row["total_ingresos"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_ingreso
 

   
   function  uf_scg_reporte_select_saldo_ingreso_est($adt_fecini,$adt_fecfin,$ai_ingreso,&$ad_total_ingresos,$arrestructuras) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_ingreso
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecfin  // fecha hasta
     //              	    $ai_ingreso  // numero de la cuenta de ingraso 
	 //                     $ad_total_ingresos  //  total de ingreso (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {		 
		$ls_sql="SELECT SC.sc_cuenta, (SELECT case sum(haber_mes-debe_mes) when null then 0
                                       else sum(haber_mes-debe_mes) end saldo
                                       FROM scg_saldos 
                                       WHERE codemp='".$ls_codemp."'
                                       AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
                                       " AND sc_cuenta=SC.sc_cuenta
                                       GROUP BY codemp, sc_cuenta) as total_ingresos
				FROM scg_cuentas SC     
				WHERE (SC.status='C') AND (SC.sc_cuenta like '".$ai_ingreso."%') ";
	   }
	   else 
	    {
	     $ls_sql=" SELECT COALESCE(sum(curSaldo.SALDO),0) as total_ingresos ".
             " FROM   scg_cuentas SC,(SELECT sc_cuenta, codemp, COALESCE(sum(haber_mes-debe_mes),0) as saldo ".
             "                        FROM   scg_saldos ".
		     "                        WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		     "                        GROUP BY codemp, sc_cuenta) as curSaldo,spi_cuentas,spi_cuentas_estructuras ".
             " 	WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp = curSaldo.codemp) AND (SC.status='C') AND ".
	         "  (SC.sc_cuenta like '".$ai_ingreso."%') and spi_cuentas.sc_cuenta=curSaldo.sc_cuenta 
				AND spi_cuentas.spi_cuenta=spi_cuentas_estructuras.spi_cuenta
				AND spi_cuentas_estructuras.codestpro1='{$arrestructuras[0]}'
				AND spi_cuentas_estructuras.codestpro2='{$arrestructuras[1]}'
				AND spi_cuentas_estructuras.codestpro3='{$arrestructuras[2]}'
				AND spi_cuentas_estructuras.codestpro4='{$arrestructuras[3]}'
				AND spi_cuentas_estructuras.codestpro5='{$arrestructuras[4]}'
				AND spi_cuentas_estructuras.estcla='{$arrestructuras[5]}'";
	   
	    
	    
	  }
	 //  print $ls_sql;
	  // die();
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_ingreso ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_total_ingresos=$row["total_ingresos"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
 }//fin uf_scg_reporte_obtener_saldo_ingreso
   

 
 
 
 
   
   function  uf_scg_reporte_select_saldo_ingreso_consolidado($adt_fecini,$adt_fecfin,$ai_ingreso,&$ad_total_ingresos) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_ingreso_consolidado
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecfin  // fecha hasta
     //              	    $ai_ingreso  // numero de la cuenta de ingraso 
	 //                     $ad_total_ingresos  //  total de ingreso (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 if ($_SESSION["ls_gestor"]=='INFORMIX')
	    {		 
		  $ls_sql = "SELECT trim(scg_cuentas_consolida.sc_cuenta) as sc_cuenta, 
		                    (SELECT case sum(haber_mes-debe_mes) 
		                            WHEN NULL THEN 0
                                    ELSE sum(haber_mes-debe_mes) END saldo
                               FROM scg_saldos_consolida
                              WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'
                                AND sc_cuenta=scg_cuentas_consolida.sc_cuenta
                              GROUP BY sc_cuenta) as total_ingresos
				       FROM scg_cuentas_consolida
				      WHERE scg_cuentas_consolida.sc_cuenta like '".$ai_ingreso."%'";
	    }
	 else 
	    {
	      /*$ls_sql = "SELECT COALESCE(sum(curSaldo.SALDO),0) as total_ingresos
                       FROM scg_cuentas_consolida,
					       (SELECT sc_cuenta, COALESCE(sum(haber_mes-debe_mes),0) as saldo
                              FROM scg_saldos_consolida
		                     WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'
		                     GROUP BY sc_cuenta) as curSaldo
                      WHERE scg_cuentas_consolida.sc_cuenta like '".$ai_ingreso."%'
					    AND trim(scg_cuentas_consolida.sc_cuenta) = trim(curSaldo.sc_cuenta)";*/
						
			$ls_sql = "SELECT COALESCE(sum(haber_mes-debe_mes),0) as total_ingresos 
                         FROM scg_saldos_consolida 
						WHERE sc_cuenta LIKE '".$ai_ingreso."%'
						  AND fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."' 
					    GROUP BY sc_cuenta
					    ORDER BY sc_cuenta ASC LIMIT 1;";
	    }
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_ingreso_consolidado ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_total_ingresos=$row["total_ingresos"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_ingreso
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_select_saldo_gasto($adt_fecini,$adt_fecfin,$ai_gasto,&$ad_total_gastos) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_gasto
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecfin  // fecha hasta
     //              	    $ai_gasto  // numero de la cuenta de gasto
	 //                     $ad_total_gastos  //  total de gastos (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 if($_SESSION["ls_gestor"]=='INFORMIX')
	   {		 
		$ls_sql="SELECT SC.sc_cuenta, (SELECT case sum(haber_mes-debe_mes) when null then 0
                                              else sum(haber_mes-debe_mes) end saldo
                                              FROM scg_saldos 
                                              WHERE codemp='".$ls_codemp."'
                                              AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
                                            " AND sc_cuenta=SC.sc_cuenta
                                              GROUP BY codemp, sc_cuenta) as total_gastos
				FROM scg_cuentas SC     
				WHERE (SC.status='C') AND (SC.sc_cuenta like '".$ai_gasto."%') ";
	   }
	   else 
	  { 
	   $ls_sql=" SELECT COALESCE(sum(curSaldo.SALDO),0) as total_gastos ".
             " FROM   scg_cuentas SC,(SELECT sc_cuenta, codemp, COALESCE(sum(haber_mes-debe_mes),0) as saldo ".
		     "                        FROM scg_saldos ".
		     "                        WHERE codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		     "                        GROUP BY codemp, sc_cuenta) as curSaldo ".
             " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp = curSaldo.codemp) AND ".
			 "       (SC.status='C') AND (SC.sc_cuenta like '".$ai_gasto."%') AND curSaldo.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE fecha between '".$adt_fecini."' AND '".$adt_fecfin."' AND sc_cuenta LIKE '".$ai_gasto."%')";
	  }
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_total_gastos=$row["total_gastos"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_gasto

   
   function  uf_scg_reporte_select_saldo_gasto_est($adt_fecini,$adt_fecfin,$ai_gasto,&$ad_total_gastos,$arrestructuras) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_gasto
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecfin  // fecha hasta
     //              	    $ai_gasto  // numero de la cuenta de gasto
	 //                     $ad_total_gastos  //  total de gastos (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 if($_SESSION["ls_gestor"]=='INFORMIX')
	 {		 
		$ls_sql="SELECT SC.sc_cuenta, (SELECT case sum(haber_mes-debe_mes) when null then 0
                                              else sum(haber_mes-debe_mes) end saldo
                                              FROM scg_saldos 
                                              WHERE codemp='".$ls_codemp."'
                                              AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
                                            " AND sc_cuenta=SC.sc_cuenta
                                              GROUP BY codemp, sc_cuenta) as total_gastos
				FROM scg_cuentas SC     
				WHERE (SC.status='C') AND (SC.sc_cuenta like '".$ai_gasto."%') ";
	 }
	 else 
	 { 
	   $ls_sql=" SELECT COALESCE(sum(curSaldo.SALDO),0) as total_gastos ".
             " FROM   scg_cuentas SC,(SELECT sc_cuenta, codemp, COALESCE(sum(haber_mes-debe_mes),0) as saldo ".
		     "                        FROM scg_saldos ".
		     "                        WHERE codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		     "                        GROUP BY codemp, sc_cuenta) as curSaldo,spg_cuentas ".
             " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp = curSaldo.codemp) AND ".
			 "       (SC.status='C') AND (SC.sc_cuenta like '".$ai_gasto."%') 
			 	AND spg_cuentas.sc_cuenta=curSaldo.sc_cuenta  
				AND (SC.sc_cuenta like '{$ai_gasto}%') 
				AND spg_cuentas.codestpro1='{$arrestructuras[0]}' 
				AND spg_cuentas.codestpro2='{$arrestructuras[1]}' 
				AND spg_cuentas.codestpro3='{$arrestructuras[2]}' 
				AND spg_cuentas.codestpro4='{$arrestructuras[3]}' 
				AND spg_cuentas.codestpro5='{$arrestructuras[4]}' 
				AND spg_cuentas.estcla='{$arrestructuras[5]}'"; 
	  }
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_total_gastos=$row["total_gastos"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_gasto
   
  
   
   
   
   function  uf_scg_reporte_select_saldo_gasto_consolidado($adt_fecini,$adt_fecfin,$ai_gasto,&$ad_total_gastos) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_gasto
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecfin  // fecha hasta
     //              	    $ai_gasto  // numero de la cuenta de gasto
	 //                     $ad_total_gastos  //  total de gastos (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 if ($_SESSION["ls_gestor"]=='INFORMIX')
	    {		 
	 	  $ls_sql = "SELECT scg_cuentas_consolida.sc_cuenta, (SELECT CASE sum(haber_mes-debe_mes) 
																	  WHEN NULL THEN 0
																	  ELSE sum(haber_mes-debe_mes) END saldo
																 FROM scg_saldos_consolida
																WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'
																  AND sc_cuenta=scg_cuentas_consolida.sc_cuenta
																GROUP BY sc_cuenta) as total_gastos
				       FROM scg_cuentas_consolida
				      WHERE scg_cuentas_consolida.sc_cuenta like '".$ai_gasto."%'";
	    }
	 else 
	    { 
	   /*$ls_sql = "SELECT COALESCE(sum(curSaldo.SALDO),0) as total_gastos
                    FROM scg_cuentas_consolida,(SELECT sc_cuenta, COALESCE(sum(haber_mes-debe_mes),0) as saldo
		                                          FROM scg_saldos_consolida
		                                         WHERE fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'
		                                         GROUP BY sc_cuenta) as curSaldo
                   WHERE scg_cuentas_consolida.sc_cuenta like '".$ai_gasto."%'
					 AND trim(scg_cuentas_consolida.sc_cuenta) = trim(curSaldo.sc_cuenta)";*/
	    			 
	      $ls_sql = "SELECT COALESCE(SUM(debe_mes-haber_mes),0) AS total_gastos
					   FROM scg_saldos_consolida 
					  WHERE sc_cuenta LIKE '".$ai_gasto."%'
					    AND fecsal BETWEEN '".$adt_fecini."' AND '".$adt_fecfin."'
					  GROUP BY sc_cuenta
					  ORDER BY sc_cuenta ASC LIMIT 1;";
	    }
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_total_gastos=$row["total_gastos"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_gasto
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_select_a(&$rs_agno) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_a???
	 //         Access :	private
	 //     Argumentos :    $rs_agno //result 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT distinct substring(fecsal,1,4) as anuales ".
             " FROM   scg_saldos ".
             " WHERE  codemp='".$ls_codemp."' ".
             " ORDER BY anuales DESC";
	 $rs_agno=$this->SQL->select($ls_sql);
	 if($rs_agno===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 return $lb_valido;   
  }//uf_scg_reporte_select_a???
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_estado_de_resultado($adt_fecini,$adt_fecfin,$ai_nivel) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_estado_de_resultado
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
	 //                     $adt_fecini  // fecha  desde 
	 //                     $ai_nivel   //  nivel de la  cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    24/04/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_reporte->resetds("sc_cuenta");
      $li_ingreso = $this->dts_empresa["ingreso"];
      $li_gasto = $this->dts_empresa["gasto"];
	  
	  $ls_sql=" SELECT SC.sc_cuenta, SC.status, SC.denominacion,  curSaldo.saldo, ".
	          "        cast(0 as UNSIGNED ) as nivel, cast(0 as UNSIGNED ) as total_ingresos, ".
              "        cast(0 as UNSIGNED ) as total_egresos ".
              " FROM   scg_cuentas SC, (SELECT sc_cuenta, codemp, sum(haber_mes-debe_mes) as saldo ".
		      "                         FROM   scg_saldos ".
		      "                         WHERE  codemp='".$ls_codemp."' AND fecsal between '".$adt_fecini."' AND '".$adt_fecfin."' ".
		      "                         GROUP BY sc_cuenta) as curSaldo ".
              " WHERE (SC.sc_cuenta = curSaldo.sc_cuenta) AND (SC.codemp=curSaldo.codemp) AND ".
			  "       (SC.sc_cuenta like '".$li_ingreso."%' OR SC.sc_cuenta like '".$li_gasto."%') AND ".
              "       (SC.nivel<='".$ai_nivel."') ".
              " ORDER BY SC.sc_cuenta ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
			$this->is_msg_error="Error en consulta metodo uf_spg_reporte_comprobante_formato1 ".$this->fun->uf_convertirmsg($this->SQL->message);
			$lb_valido = false;
	 }
	 else
	 {
	    $ld_total_ingresos=0;
		$ld_total_egresos=0;
		$lb_valido=$this->uf_scg_reporte_select_saldo_ingreso($adt_fecini,$adt_fecfin,$li_ingreso,$ld_total_ingresos);
	    if($lb_valido)
	    {
		  $lb_valido=$this->uf_scg_reporte_select_saldo_gasto($adt_fecini,$adt_fecfin,$li_gasto,$ld_total_egresos);
	    }
	   if($lb_valido)
	   {
			while($row=$this->SQL->fetch_row($rs_data))
			{
				   $ls_sc_cuenta=$row["sc_cuenta"];
				   $ls_status=$row["status"];
				   $ls_denominacion=$row["denominacion"];
				   $ld_saldo=$row["saldo"];
			       $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_sc_cuenta);
				   
				   $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
				   $this->dts_reporte->insertRow("status",$ls_status);
				   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
				   $this->dts_reporte->insertRow("saldo",$ld_saldo);
				   $this->dts_reporte->insertRow("nivel",$ls_nivel);
				   $this->dts_reporte->insertRow("total_ingresos",$ld_total_ingresos);
				   $this->dts_reporte->insertRow("total_egresos",$ld_total_egresos);
				   $lb_valido = true;
			   }//if   
		}//while
		$this->SQL->free_result($rs_data);
	}//else	  
	return $lb_valido;
}//fin uf_scg_reporte_estado_de_resultado
/****************************************************************************************************************************************/	
	//////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SCG  " BALANCE GENERAL   "                 // 
	////////////////////////////////////////////////////////////////
   function  uf_scg_reporte_balance_general($adt_feclimit,$ai_nivel) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_balance_general
	 //         Access :	private
	 //     Argumentos :    $adt_feclimit  // fecha  limite
     //              	    $ai_nivel  // nivel de la cuenta
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida del Balance General
	 //     Creado por :    Ing. Yozelin Barragan.
	 // Fecha Creaci??? :    03/05/2006          Fecha ltima Modificacion : 08/05/06     Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $this->dts_egresos->resetds("sc_cuenta");
	  $this->dts_reporte->resetds("sc_cuenta");
	  $dts_Balance2=new class_datastore();
      $li_activo = $this->dts_empresa["activo"];
      $li_pasivo = $this->dts_empresa["pasivo"];
      $li_resultado = $this->dts_empresa["resultado"];
      $li_capital = $this->dts_empresa["capital"];
      $li_orden_d = $this->dts_empresa["orden_d"];
      $li_orden_h = $this->dts_empresa["orden_h"];
	  $li_ingreso = $this->dts_empresa["ingreso"];
      $li_gasto = $this->dts_empresa["gasto"];
      $li_c_resultad = $this->dts_empresa["c_resultad"];
	  
	  $ls_sql=" SELECT SC.sc_cuenta,SC.denominacion,SC.status,SC.nivel as rnivel, ".
              "        coalesce(curSaldo.T_Debe,0) as total_debe, ".
              "        coalesce(curSaldo.T_Haber,0) as total_haber,0 as nivel ".
              " FROM scg_cuentas SC LEFT OUTER JOIN (SELECT codemp,sc_cuenta, coalesce(sum(debe_mes),0)as T_Debe, ".
			  "                                             coalesce(sum(haber_mes),0) as T_Haber ".
              "                                      FROM   scg_saldos ".
              "                                      WHERE  codemp='".$ls_codemp."' AND fecsal<='".$adt_feclimit."' ".
              "                                      GROUP BY sc_cuenta) curSaldo ".
              " ON SC.sc_cuenta=curSaldo.sc_cuenta ".
              " WHERE SC.codemp=curSaldo.codemp AND  curSaldo.codemp='".$ls_codemp."' AND ".
			  "       (SC.sc_cuenta like '".$li_activo."%' OR SC.sc_cuenta like '".$li_pasivo."%' OR ".
			  "        SC.sc_cuenta like '".$li_resultado."%' OR  SC.sc_cuenta like '".$li_capital."%' OR ".
			  "        SC.sc_cuenta like '".$li_orden_d."%' OR SC.sc_cuenta like '".$li_orden_h."%') ".
              " ORDER BY  SC.sc_cuenta ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_balance_general ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
        $ld_saldo_ganancia=0;
		while($row=$this->SQL->fetch_row($rs_data))
		{
		  $ls_sc_cuenta=$row["sc_cuenta"];
		  $ls_denominacion=$row["denominacion"];
		  $ls_status=$row["status"];
		  $ls_rnivel=$row["rnivel"];
		  $ld_total_debe=$row["total_debe"];
		  $ld_total_haber=$row["total_haber"];
		  if($ls_status=="C")
		  {
    		$ls_nivel="4";		
		  }//if
		  else
		  {
    		$ls_nivel=$ls_rnivel;		
		  }//else
		  if($ls_nivel<=$ai_nivel)
		  {
			  $this->dts_Prebalance->insertRow("sc_cuenta",$ls_sc_cuenta);
			  $this->dts_Prebalance->insertRow("denominacion",$ls_denominacion);
			  $this->dts_Prebalance->insertRow("status",$ls_status);
			  $this->dts_Prebalance->insertRow("nivel",$ls_nivel);
			  $this->dts_Prebalance->insertRow("rnivel",$ls_rnivel);
			  $this->dts_Prebalance->insertRow("total_debe",$ld_total_debe);
			  $this->dts_Prebalance->insertRow("total_haber",$ld_total_haber);
		      $lb_valido = true;
		  }//if
		}//while
	    $li=$this->dts_Prebalance->getRowCount("sc_cuenta");
		if($li==0)
		{
		  $lb_valido = false;
		  return false;
		}//if
	 } //else
	 $ld_saldo_i=0;
	 if($lb_valido)
	 {
	   $lb_valido=$this->uf_scg_reporte_select_saldo_ingreso_BG($adt_feclimit,$li_ingreso,$ld_saldo_i);
	 } 
     if($lb_valido)
	 {
       $ld_saldo_g=0;	 
	   $lb_valido=$this->uf_scg_reporte_select_saldo_gasto_BG($adt_feclimit,$li_gasto,$ld_saldo_g);  
	 }//if
	 if($lb_valido)
	 {
	   $ld_saldo_ganancia=$ld_saldo_ganancia+($ld_saldo_i+$ld_saldo_g);
	 }//if
	 $la_sc_cuenta=array();
	 $la_denominacion=array();
	 $la_saldo=array();
	 for($i=1;$i<=$ai_nivel;$i++)
	 {
		 $la_sc_cuenta[$i]="";
		 $la_denominacion[$i]="";
		 $la_saldo[$i]=0;
	 }//for
	 $li_nro_reg=0;
     $ld_saldo_resultado=0;
	 $li_row=$this->dts_Prebalance->getRowCount("sc_cuenta");
	 for($li_i=1;$li_i<=$li_row;$li_i++)
	 {
		  $ls_sc_cuenta=$this->dts_Prebalance->data["sc_cuenta"][$li_i];
		  $ls_status=$this->dts_Prebalance->data["status"][$li_i];
		  $ls_denominacion=$this->dts_Prebalance->data["denominacion"][$li_i];
		  $ls_rnivel=$this->dts_Prebalance->data["rnivel"][$li_i];
		  $ld_total_debe=$this->dts_Prebalance->data["total_debe"][$li_i];
		  $ld_total_haber=$this->dts_Prebalance->data["total_haber"][$li_i]; 
		  $ls_nivel=$this->dts_Prebalance->data["nivel"][$li_i]; 

		  $ls_tipo_cuenta=substr($ls_sc_cuenta,0,1);
		  if($ls_tipo_cuenta==$li_activo) {	$ls_orden="1"; }	
		  if($ls_tipo_cuenta==$li_pasivo) {	$ls_orden="2"; }	
		  if($ls_tipo_cuenta==$li_capital) { $ls_orden="3"; }	
		  if($ls_tipo_cuenta==$li_resultado) { $ls_orden="4"; }	
		  if($ls_tipo_cuenta==$li_orden_d) { $ls_orden="5"; }
		  if($ls_tipo_cuenta== $li_orden_h){ $ls_orden="6"; }
		 
		  $ld_saldo=abs($ld_total_debe-$ld_total_haber);
		  if((($ls_tipo_cuenta==$li_pasivo)||($ls_tipo_cuenta==$li_resultado)||($ls_tipo_cuenta==$li_capital))&&($ls_nivel==1))
		  {
			  $ld_saldo_resultado=$ld_saldo_resultado+$ld_saldo;
		  }//if
		  if($ls_nivel==4)
		  {
		    $li_nro_reg=$li_nro_reg+1; 
		    $this->dts_Balance1->insertRow("orden",$ls_orden);
		    $this->dts_Balance1->insertRow("num_reg",$li_nro_reg);
		    $this->dts_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);
		    $this->dts_Balance1->insertRow("denominacion",$ls_denominacion);
			$this->dts_Balance1->insertRow("nivel",$ls_nivel);
			$this->dts_Balance1->insertRow("saldo",$ld_saldo);
		  }//if
		  else
		  {
		    if (empty($la_sc_cuenta[$ls_nivel]))
			{
			   $la_sc_cuenta[$ls_nivel]=$ls_sc_cuenta;
			   $la_denominacion[$ls_nivel]=$ls_denominacion;
			   $la_saldo[$ls_nivel]=$ld_saldo;
		       $li_nro_reg=$li_nro_reg+1;
			   $this->dts_Balance1->insertRow("orden",$ls_orden);
			   $this->dts_Balance1->insertRow("num_reg",$li_nro_reg);
			   $this->dts_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_Balance1->insertRow("denominacion",$ls_denominacion);
			   $this->dts_Balance1->insertRow("nivel",-$ls_nivel);
			   $this->dts_Balance1->insertRow("saldo",$ld_saldo);
			}//if
            else
			{
			   $this->uf_scg_reporte_calcular_total_BG($li_nro_reg,$ls_prev_nivel,$ls_nivel,$la_sc_cuenta,$la_denominacion,$la_saldo,$li_activo,$li_pasivo,$li_capital,$li_resultado,$li_orden_d,$li_orden_h); 
			   $la_sc_cuenta[$ls_nivel]=$ls_sc_cuenta;
			   $la_denominacion[$ls_nivel]=$ls_denominacion;
			   $la_saldo[$ls_nivel]=$ld_saldo;
		       $li_nro_reg=$li_nro_reg+1;
			   $this->dts_Balance1->insertRow("orden",$ls_orden);
			   $this->dts_Balance1->insertRow("num_reg",$li_nro_reg);
			   $this->dts_Balance1->insertRow("sc_cuenta",$ls_sc_cuenta);
			   $this->dts_Balance1->insertRow("denominacion",$ls_denominacion);
			   $this->dts_Balance1->insertRow("nivel",-$ls_nivel);
			   $this->dts_Balance1->insertRow("saldo",$ld_saldo);
			}//else 			
          $ls_prev_nivel=$ls_nivel;		 
		}//else
	 }//for
	 $this->uf_scg_reporte_actualizar_resultado_BG($li_c_resultad,abs($ld_saldo_ganancia),$li_nro_reg,$ls_orden); 
	 if($ld_saldo_ganancia>0)
	 {
	 	$ld_saldo_resultado=$ld_saldo_resultado-$ld_saldo_ganancia;
	 }
	 else
	 {
	 	$ld_saldo_resultado=$ld_saldo_resultado+abs($ld_saldo_ganancia);
	 }
	 $li_total=$this->dts_Balance1->getRowCount("sc_cuenta");
	 for($li_i=1;$li_i<=$li_total;$li_i++)
	 {	
		  $ls_sc_cuenta=$this->dts_Balance1->data["sc_cuenta"][$li_i];
		  $ls_orden=$this->dts_Balance1->data["orden"][$li_i];
		  $li_nro_reg=$this->dts_Balance1->data["num_reg"][$li_i];
		  $ls_denominacion=$this->dts_Balance1->data["denominacion"][$li_i];
		  $ls_nivel=$this->dts_Balance1->data["nivel"][$li_i];
		  $ld_saldo=$this->dts_Balance1->data["saldo"][$li_i];
		  $li_pos=$this->dts_Prebalance->find("sc_cuenta",$ls_sc_cuenta);
		  if($li_pos>0)
		  { 
		    $ls_rnivel=$this->dts_Prebalance->data["rnivel"][$li_pos];
		  }
		  else
		  {
		    $ls_rnivel=0;
		  }
	      $dts_Balance2->insertRow("orden",$ls_orden);
	      $dts_Balance2->insertRow("num_reg",$li_nro_reg);
	      $dts_Balance2->insertRow("sc_cuenta",$ls_sc_cuenta);
	      $dts_Balance2->insertRow("denominacion",$ls_denominacion);
	      $dts_Balance2->insertRow("nivel",$ls_nivel);
	      $dts_Balance2->insertRow("saldo",abs($ld_saldo));
	      $dts_Balance2->insertRow("rnivel",$ls_rnivel);
		  $dts_Balance2->insertRow("total",abs($ld_saldo_resultado));
	 }//for
	 $li_tot=$dts_Balance2->getRowCount("sc_cuenta");
	 for($li_i=1;$li_i<=$li_tot;$li_i++)
	 { 
		  $ls_sc_cuenta=$dts_Balance2->data["sc_cuenta"][$li_i];
		  $ls_orden=$dts_Balance2->data["orden"][$li_i];
		  $li_nro_reg=$dts_Balance2->data["num_reg"][$li_i];
		  $ls_denominacion=$dts_Balance2->data["denominacion"][$li_i];
		  $ls_nivel=$dts_Balance2->data["nivel"][$li_i];
		  $ld_saldo=$dts_Balance2->data["saldo"][$li_i];
		  $ls_rnivel=$dts_Balance2->data["rnivel"][$li_i];
		  $ld_saldo_resultado=$dts_Balance2->data["total"][$li_i];
		  if($ls_rnivel<=$ai_nivel)
		  {
			  $this->dts_reporte->insertRow("orden",$ls_orden);
			  $this->dts_reporte->insertRow("num_reg",$li_nro_reg);
			  $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
			  $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
			  $this->dts_reporte->insertRow("nivel",$ls_nivel);
			  $this->dts_reporte->insertRow("saldo",$ld_saldo);
			  $this->dts_reporte->insertRow("rnivel",$ls_rnivel);
			  $this->dts_reporte->insertRow("total",$ld_saldo_resultado);
		  }//if	  
	 }//for
     unset($this->dts_Prebalance);
     unset($this->dts_Balance1);
     unset($dts_Balance2);
	 return $lb_valido;   
   }//uf_scg_reporte_balance_general
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_select_saldo_gasto_BG($adt_fecini,$ai_gasto,&$ad_saldo) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_gasto_BG
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
     //              	    $ai_gasto  // numero de la cuenta de gasto
	 //                     $ad_saldo  //  total saldo (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    02/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 
	 $ls_sql=" SELECT COALESCE(sum(SD.debe_mes-SD.haber_mes),0) as saldo ".
             " FROM   scg_cuentas SC, scg_saldos SD ".
             " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			 "        fecsal<='".$adt_fecini."' AND (SC.sc_cuenta like '".$ai_gasto."%') AND SC.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE sc_cuenta LIKE '".$ai_gasto."%' AND fecha<='".$adt_fecini."')";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_gasto_BG ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_saldo=$row["saldo"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_select_saldo_gasto_BG
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_select_saldo_ingreso_BG($adt_fecini,$ai_ingreso,&$ad_saldo) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_select_saldo_ingreso_BG
	 //         Access :	private
	 //     Argumentos :    $adt_fecini  // fecha  desde 
     //              	    $ai_ingreso  // numero de la cuenta de ingraso 
	 //                     $ad_saldo  //  total saldo (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte que genera salida  del Estado de Resultado  
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    02/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT COALESCE(sum(SD.debe_mes-SD.haber_mes),0) as saldo ".
             " FROM   scg_cuentas SC, scg_saldos SD ".
             " WHERE (SC.sc_cuenta = SD.sc_cuenta) AND (SC.codemp = SD.codemp) AND (SC.status='C') AND ".
			 "        fecsal<='".$adt_fecini."' AND (SC.sc_cuenta like '".$ai_ingreso."%')AND SC.sc_cuenta IN (SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE sc_cuenta LIKE '".$ai_gasto."%' AND fecha <='".$adt_fecini."') ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {// error interno sql
		$this->is_msg_error="Error en consulta metodo uf_scg_reporte_select_saldo_ingreso_BG ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $ad_saldo=$row["saldo"];
		}
		$this->SQL->free_result($rs_data);
	 } 
	 return $lb_valido;   
   }//fin uf_scg_reporte_obtener_saldo_ingreso
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_calcular_total_BG(&$ai_nro_regi,$as_prev_nivel,$as_nivel,&$aa_sc_cuenta,$aa_denominacion,$aa_saldo,
                                              $ai_activo,$ai_pasivo,$ai_capital,$ai_resultado,$ai_orden_d,$ai_orden_h) 
   {				 
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_calcular_total_BG
	 //         Access :	private
	 //     Argumentos :    $as_prev_nivel  // nivel de la cuenta anterior
     //              	    $as_nivel  // nivel de  la cuenta 
	 //                     $ai_nro_regi  //  numero de registro (referencia)
	 //                     $aa_sc_cuenta  // arreglo de cuentas (referencia)
	 //                     $aa_denominacion // arreglo de denominacion         
	 //                     $aa_saldo // arreglo de saldo         
     //	       Returns :	Retorna true o false si se realizo el calculo del total para el reporte
	 //	   Description :	Metodo que genera un monto total para la cuenta del balance general 
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    08/05/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $i=$as_prev_nivel-1;
	 $x=$as_nivel-1;
	 if($i>$x)
	 {
		  $ls_tipo_cuenta=substr($aa_sc_cuenta[$i],0,1);
		  if($ls_tipo_cuenta==$ai_activo) {	$ls_orden="1"; }	
		  if($ls_tipo_cuenta==$ai_pasivo) {	$ls_orden="2"; }	
		  if($ls_tipo_cuenta==$ai_capital) { $ls_orden="3"; }	
		  if($ls_tipo_cuenta==$ai_resultado) { $ls_orden="4"; }	
		  if($ls_tipo_cuenta==$ai_orden_d) { $ls_orden="5"; }
		  if($ls_tipo_cuenta== $ai_orden_h){ $ls_orden="6"; }
		  else{$ls_orden="7";}
          if(!empty($aa_sc_cuenta[$i]))
		  {
	 	    $ai_nro_regi=$ai_nro_regi+1;
		    $this->dts_Balance1->insertRow("orden",$ls_orden);
		    $this->dts_Balance1->insertRow("num_reg",$ai_nro_regi);
		    $this->dts_Balance1->insertRow("sc_cuenta",$aa_sc_cuenta[$i]);
		    $this->dts_Balance1->insertRow("denominacion","Total ".$aa_denominacion[$i]);
		    $this->dts_Balance1->insertRow("nivel",$i);
		    $this->dts_Balance1->insertRow("saldo",$aa_saldo[$i]);
			$aa_sc_cuenta[$i]="";
			$i--;
		  }//if
	 }//if
    }//uf_scg_reporte_calcular_total_BG
/****************************************************************************************************************************************/	
   function  uf_scg_reporte_actualizar_resultado_BG($ai_c_resultad,$ad_saldo_ganancia,$ai_nro_reg,$as_orden) 
   {				 
	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_actualizar_resultado_BG
	 //         Access :	private
	 //     Argumentos :    $ai_c_resultad  // cuenta de resultado
     //              	    $ad_saldo_ganancia  // saldo 
     //              	    $as_sc_cuenta  // cuenta
     //	       Returns :	Retorna true o false si se realizo el calculo para el reporte
	 //	   Description :	Metodo que genera un monto actualizado de la cuenta del resultado
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    08/05/2006          Fecha ltima Modificacion :      Hora :
  	 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
     $ls_next_cuenta=$ai_c_resultad;
	 $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_next_cuenta);
	 while($ls_nivel>=1)
	 {
		  $li_pos=$this->dts_Balance1->find("sc_cuenta",$ls_next_cuenta);
		  if($li_pos>0)
		  {
			  $ld_saldo=$this->dts_Balance1->getValue("saldo",$li_pos);
			  if($ad_saldo_ganancia>0)	
			  { 
			  	$ld_saldo=$ld_saldo-$ad_saldo_ganancia;
			  }
			  else
			  {
			   $ld_saldo=$ld_saldo+abs($ad_saldo_ganancia);
			  }
			  $this->dts_Balance1->updateRow("saldo",$ld_saldo,$li_pos);
		  }	 
		  else
		  {
                $lb_valido=$this->uf_select_denominacion($ls_next_cuenta,$ls_denominacion);			
			    if($lb_valido)
				{
                   $li_nro_reg=$ai_nro_reg+1;
				   $this->dts_Balance1->insertRow("orden",$as_orden);
				   $this->dts_Balance1->insertRow("num_reg",$li_nro_reg);
				   $this->dts_Balance1->insertRow("sc_cuenta",$ls_next_cuenta);
				   $this->dts_Balance1->insertRow("denominacion",$ls_denominacion);
				   $this->dts_Balance1->insertRow("nivel",$ls_nivel);
				   $this->dts_Balance1->insertRow("saldo",$ad_saldo_ganancia);
				}   
		  } 													
		  if($ls_nivel==1)
		  {
			 return;
		  }//if
		  $ls_next_cuenta=$this->sigesp_int_scg->uf_scg_next_cuenta_nivel($ls_next_cuenta);
		  $ls_nivel=$this->sigesp_int_scg->uf_scg_obtener_nivel($ls_next_cuenta);
	 }//while
   }//uf_scg_reporte_actualizar_resultado_BG
/****************************************************************************************************************************************/	
function uf_select_denominacion($as_sc_cuenta,&$as_denominacion)
{
	//////////////////////////////////////////////////////////////////////////////////////////////////
	//	      Function:  uf_select_denominacion 
	//	     Arguments:  $as_sc_cuenta  // codigo de la cuenta
	//                   $as_denominacion  // denominacion de la cuenta (referencia)
	//	       Returns:	 retorna un arreglo con las cuentas inferiores  
	//	   Description:  Busca la denominacion de la cuenta
	//     Creado por :  Ing. Yozelin Barrag???
	// Fecha Creaci??? :  14/08/2006                      Fecha ltima Modificacion : 
	///////////////////////////////////////////////////////////////////////////////////////////////////
	$lb_valido=true;
    $ls_codemp = $this->dts_empresa["codemp"];
	$ls_sql = "SELECT denominacion FROM scg_cuentas WHERE sc_cuenta='".$as_sc_cuenta."' AND codemp='".$ls_codemp."' ";
    $rs_data=$this->SQL->select($ls_sql);
	if($rs_data===false)
	{
	    $lb_valido=false;
		$this->is_msg_error="Error en consulta metodo uf_select_denominacion ".$this->fun->uf_convertirmsg($this->SQL->message);
	}
	else
	{
	   if($row=$this->SQL->fetch_row($rs_data))
	   {
	      $as_denominacion=$row["denominacion"];
	   }
	   $this->SQL->free_result($rs_data);
	}
    return  $lb_valido;
 }//uf_select_denominacion
/****************************************************************************************************************************************/	


	function uf_cargar_mayor_analitico($ld_fecdesde,$ld_fechasta,$ls_cuenta_desde,$ls_cuenta_hasta,$ls_orden) 
	{
	
	  $lb_valido=true;
	  $ls_codemp = $this->dts_empresa["codemp"];
	  $li_row=0;
	  $ld_fecdesde=	$this->fun->uf_convertirdatetobd($ld_fecdesde);  
  	  $ld_fechasta=	$this->fun->uf_convertirdatetobd($ld_fechasta);	
	  ////////////////////////////////////////////////////////////////////////////////////////////////////////////7
			
	  /* $ls_sql=" SELECT A.sc_cuenta as sc_cuenta,A.procede as procede,A.comprobante as comprobante,A.procede_doc as procede_doc, ".
	           "        A.documento as documento,A.fecha as fecha,A.debhab as debhab, A.descripcion as descripcion,A.monto as monto, ".
			   "        A.orden as orden,A.denominacion as denominacion, ".
			   "        A.des_comp as des_comp,COALESCE(curSA.saldo_ant,0) as saldo_ant". 	          
			   " FROM  ( SELECT 					mv.sc_cuenta,mv.procede,mv.comprobante,mv.procede_doc,mv.documento,mv.fecha,mv.debhab,mv.descripcion, 
             				 mv.monto,mv.orden,scu.denominacion,sco.descripcion as des_comp
					FROM scg_dt_cmp mv, sigesp_cmp sco,scg_cuentas scu
					WHERE ( sco.procede=mv.procede AND sco.comprobante=mv.comprobante AND 
             				    sco.fecha=mv.fecha ) AND ( mv.fecha between '".$ld_fecdesde."' AND '".$ld_fechasta."') AND 
	        			   ( mv.sc_cuenta between '".$ls_cuenta_desde."' AND '".$ls_cuenta_hasta."' ) AND mv.sc_cuenta=scu.sc_cuenta) A  ".
			   " LEFT OUTER JOIN  ".				  
			   "         ( SELECT SA.sc_cuenta,COALESCE(SUM(SA.debe_mes-SA.haber_mes),0) As saldo_ant  ".
			   "	       FROM scg_saldos SA   ".
			   "	       WHERE (SA.fecsal<'".$ld_fecdesde."') AND SA.sc_cuenta>='".$ls_cuenta_desde."' AND ".
			   "                  SA.sc_cuenta<='".$ls_cuenta_hasta."' ".
			   "           GROUP BY SA.sc_cuenta ) curSA ".
			   " ON A.sc_cuenta=curSA.sc_cuenta  ".
			   " ORDER BY ".$ls_orden; 

	   $ls_sql = " SELECT scg_dt_cmp.sc_cuenta, scg_dt_cmp.codban, scg_dt_cmp.ctaban,scg_dt_cmp.procede, scg_dt_cmp.comprobante,scg_dt_cmp.procede_doc, ".
				 "        scg_dt_cmp.documento,scg_dt_cmp.fecha, scg_dt_cmp.debhab,scg_dt_cmp.descripcion, scg_dt_cmp.monto, ".
				 "        scg_dt_cmp.orden,scg_cuentas.denominacion, sigesp_cmp.descripcion as des_comp, ".
				 "		  sigesp_cmp.cod_pro, sigesp_cmp.ced_bene, ".
				 "        (SELECT nompro FROM rpc_proveedor ".
				 "			WHERE rpc_proveedor.codemp = sigesp_cmp.codemp ".
				 "			  AND rpc_proveedor.cod_pro = sigesp_cmp.cod_pro ) AS nompro, ".
				 "        (SELECT nombene FROM rpc_beneficiario ".
				 "			WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp ".
				 "			  AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS nombene, ".
				 "        (SELECT apebene FROM rpc_beneficiario ".
				 "			WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp ".
				 "			  AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS apebene, ".				 
				 "        (SELECT  case sum(debe_mes-haber_mes) when
                                    null then 0
                                    else sum(debe_mes-haber_mes)
									end ".
				 "           FROM scg_saldos ".
				 "          WHERE scg_saldos.fecsal<'".$ld_fecdesde."' ".
				 "            AND scg_dt_cmp.codemp=scg_saldos.codemp ".
				 "            AND scg_dt_cmp.sc_cuenta=scg_saldos.sc_cuenta) As saldo_ant ".
				 "   FROM scg_dt_cmp, sigesp_cmp, scg_cuentas ".
				 "  WHERE scg_dt_cmp.codemp = '".$ls_codemp."' ".
				 "    AND scg_dt_cmp.fecha between '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				 "    AND scg_dt_cmp.sc_cuenta between '".$ls_cuenta_desde."' AND '".$ls_cuenta_hasta."' ".
				 "    AND scg_dt_cmp.codemp=scg_cuentas.codemp ".
				 "    AND scg_dt_cmp.sc_cuenta=scg_cuentas.sc_cuenta ".
				 "    AND sigesp_cmp.codemp=scg_dt_cmp.codemp ".
				 "    AND sigesp_cmp.procede=scg_dt_cmp.procede ".
				 "    AND sigesp_cmp.comprobante=scg_dt_cmp.comprobante ".
				 "    AND sigesp_cmp.fecha=scg_dt_cmp.fecha ".
				 "    AND sigesp_cmp.codban=scg_dt_cmp.codban ".
				 "    AND sigesp_cmp.ctaban=scg_dt_cmp.ctaban ".
			     " ORDER BY ".$ls_orden;*/

	   $ls_sql = " SELECT scg_cuentas.sc_cuenta, scg_dt_cmp.codban, scg_dt_cmp.ctaban,scg_dt_cmp.procede, scg_dt_cmp.comprobante,scg_dt_cmp.procede_doc, ".
		     " scg_dt_cmp.documento,scg_dt_cmp.fecha, scg_dt_cmp.debhab,scg_dt_cmp.descripcion, scg_dt_cmp.monto, ".
		     " scg_dt_cmp.orden,scg_cuentas.denominacion, sigesp_cmp.descripcion as des_comp, ".
		     " sigesp_cmp.cod_pro, sigesp_cmp.ced_bene, ".
		     " (SELECT nompro FROM rpc_proveedor ".
		     " WHERE rpc_proveedor.codemp = sigesp_cmp.codemp ".
		     " AND rpc_proveedor.cod_pro = sigesp_cmp.cod_pro ) AS nompro, ".
		     " (SELECT nombene FROM rpc_beneficiario ".
		     " WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp ".
		     " AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS nombene, ".
		     " (SELECT apebene FROM rpc_beneficiario ".
		     " WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp ".
		     " AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS apebene, ".				 
		     " (SELECT case sum(debe_mes-haber_mes) when null then 0 else sum(debe_mes-haber_mes) end ".
		     " FROM scg_saldos ".
		     " WHERE scg_saldos.fecsal<'".$ld_fecdesde."' ".
		     " AND scg_cuentas.codemp = scg_saldos.codemp ".
		     " AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta) As saldo_ant ".
		     " FROM scg_cuentas ".
		     " LEFT JOIN scg_dt_cmp ON (scg_dt_cmp.codemp = scg_cuentas.codemp AND scg_dt_cmp.fecha between '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
		     " AND scg_dt_cmp.sc_cuenta = scg_cuentas.sc_cuenta) ".
		     " LEFT JOIN sigesp_cmp ON (sigesp_cmp.codemp = scg_cuentas.codemp AND sigesp_cmp.procede=scg_dt_cmp.procede AND sigesp_cmp.comprobante=scg_dt_cmp.comprobante ".
		     " AND sigesp_cmp.fecha=scg_dt_cmp.fecha AND sigesp_cmp.codban=scg_dt_cmp.codban AND sigesp_cmp.ctaban=scg_dt_cmp.ctaban) ".
		     " WHERE scg_cuentas.codemp = '".$ls_codemp."' ".
		     " AND scg_cuentas.sc_cuenta between '".$ls_cuenta_desde."' AND '".$ls_cuenta_hasta."' ".
		     " ORDER BY ".$ls_orden;
 
	   $this->rs_analitico=$this->SQL->select($ls_sql);

	   if (($this->rs_analitico===false))
	   {
			$lb_valido=false;
			$this->io_msg="Error en consulta metodo uf_cargar_mayor_analitico ".$this->SQL->message;
       }
	  
	   return $lb_valido;         
	}//fin de uf_cargar_mayor analitico
/****************************************************************************************************************************************/	
 
 
 function mayor_analitico($propiedades=array()) 
	{
		  
	  $propiedades['fecdes']=	$this->fun->uf_convertirdatetobd($propiedades['fecdes']);  
  	  $propiedades['fechas']=	$this->fun->uf_convertirdatetobd($propiedades['fechas']);	
	 
		$ls_sql = " SELECT scg_cuentas.sc_cuenta, scg_dt_cmp.codban, scg_dt_cmp.ctaban,scg_dt_cmp.procede, scg_dt_cmp.comprobante,scg_dt_cmp.procede_doc, ".
		          " scg_dt_cmp.documento,scg_dt_cmp.fecha, scg_dt_cmp.debhab,scg_dt_cmp.descripcion, scg_dt_cmp.monto, ".
		          " scg_dt_cmp.orden,scg_cuentas.denominacion, sigesp_cmp.descripcion as des_comp, sigesp_cmp.cod_pro, sigesp_cmp.ced_bene, ".
		          " (SELECT nompro FROM rpc_proveedor WHERE rpc_proveedor.codemp = sigesp_cmp.codemp AND rpc_proveedor.cod_pro = sigesp_cmp.cod_pro ) AS nompro, ". 
					 " (SELECT nombene FROM rpc_beneficiario WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS nombene, ". 
					 " (SELECT apebene FROM rpc_beneficiario WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS apebene, ". 
					 " (SELECT case 
sum(debe_mes-haber_mes) when null then 0 else sum(debe_mes-haber_mes) 
end FROM scg_saldos WHERE scg_saldos.fecsal < 
'".$propiedades['fecdes']."' AND scg_cuentas.codemp = scg_saldos.codemp 
AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta) As saldo_ant ". 
					 " FROM scg_cuentas ".
					 " LEFT JOIN scg_dt_cmp ON scg_cuentas.codemp = scg_dt_cmp.codemp ". 
		 			 " AND scg_cuentas.sc_cuenta = scg_dt_cmp.sc_cuenta ".
					 " AND scg_dt_cmp.sc_cuenta = scg_cuentas.sc_cuenta ".
					 " AND scg_dt_cmp.fecha between '".$propiedades['fecdes']."' AND '".$propiedades['fechas']."' ".
					 " LEFT JOIN sigesp_cmp ON sigesp_cmp.codemp=scg_dt_cmp.codemp ".
					 " AND sigesp_cmp.procede=scg_dt_cmp.procede ".
					 " AND sigesp_cmp.comprobante=scg_dt_cmp.comprobante ". 
					 " AND sigesp_cmp.fecha=scg_dt_cmp.fecha ".
					 " AND sigesp_cmp.codban=scg_dt_cmp.codban ".
					 " AND sigesp_cmp.ctaban=scg_dt_cmp.ctaban ".
					 " WHERE scg_cuentas.codemp = '".$this->ls_codemp."' ".
					 " AND scg_cuentas.sc_cuenta = '".$propiedades['sc_cuenta_d']."' ".
					 " ORDER BY ".$propiedades['orden'];	
					    
/*	   $ls_sql = " SELECT scg_dt_cmp.sc_cuenta, scg_dt_cmp.codban, scg_dt_cmp.ctaban,scg_dt_cmp.procede, scg_dt_cmp.comprobante,scg_dt_cmp.procede_doc, ".
				 "        scg_dt_cmp.documento,scg_dt_cmp.fecha, scg_dt_cmp.debhab,scg_dt_cmp.descripcion, scg_dt_cmp.monto, ".
				 "        scg_dt_cmp.orden,scg_cuentas.denominacion, sigesp_cmp.descripcion as des_comp, ".
				 "		  sigesp_cmp.cod_pro, sigesp_cmp.ced_bene, ".
				 "        (SELECT nompro FROM rpc_proveedor ".
				 "			WHERE rpc_proveedor.codemp = sigesp_cmp.codemp ".
				 "			  AND rpc_proveedor.cod_pro = sigesp_cmp.cod_pro ) AS nompro, ".
				 "        (SELECT nombene FROM rpc_beneficiario ".
				 "			WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp ".
				 "			  AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS nombene, ".
				 "        (SELECT apebene FROM rpc_beneficiario ".
				 "			WHERE rpc_beneficiario.codemp = sigesp_cmp.codemp ".
				 "			  AND rpc_beneficiario.ced_bene = sigesp_cmp.ced_bene ) AS apebene, ".				 
				 "        (SELECT  case sum(debe_mes-haber_mes) when
                                    null then 0
                                    else sum(debe_mes-haber_mes)
									end ".
				 "           FROM scg_saldos ".
				 "          WHERE scg_saldos.fecsal<'".$propiedades['fecdes']."' ".
				 "            AND scg_dt_cmp.codemp=scg_saldos.codemp ".
				 "            AND scg_dt_cmp.sc_cuenta=scg_saldos.sc_cuenta) As saldo_ant ".
				 "   FROM scg_dt_cmp, sigesp_cmp, scg_cuentas ".
				 "  WHERE scg_dt_cmp.codemp = '".$this->ls_codemp."' ".
				 "    AND scg_dt_cmp.fecha between '".$propiedades['fecdes']."' AND '".$propiedades['fechas']."' ".
				 "    AND scg_dt_cmp.sc_cuenta = '".$propiedades['sc_cuenta_d']."' ".
				 "    AND scg_dt_cmp.codemp=scg_cuentas.codemp ".
				 "    AND scg_dt_cmp.sc_cuenta=scg_cuentas.sc_cuenta ".
				 "    AND sigesp_cmp.codemp=scg_dt_cmp.codemp ".
				 "    AND sigesp_cmp.procede=scg_dt_cmp.procede ".
				 "    AND sigesp_cmp.comprobante=scg_dt_cmp.comprobante ".
				 "    AND sigesp_cmp.fecha=scg_dt_cmp.fecha ".
				 "    AND sigesp_cmp.codban=scg_dt_cmp.codban ".
				 "    AND sigesp_cmp.ctaban=scg_dt_cmp.ctaban ".
			     " ORDER BY ".$propiedades['orden'];*/ 
	   
	   $this->rs_analitico=$this->SQL->select($ls_sql);
	   //echo $ls_sql; die;
	   if (($this->rs_analitico===false)){			
			$this->io_msg->message("CLASE->sigesp_scg_reporte M?ODO->mayor_analitico ERROR->".$this->SQL->message);
			echo $this->SQL->message;
			return false;
       }
	  
	   return true;         
	}//fin de uf_cargar_mayor analitico
/****************************************************************************************************************************************/	

 
 
 function cuentas_mayor($propiedades=array()) 
	{	
	    //////////////////////////////////////////////////////////////////////////////////////////////////
		//	      Function:  cuentas_mayor 
		//	     Arguments:  Array
		//	       Returns:	 true o false
		//	   Description:  retorna la fecha maxima de periodo y la denominaci? de la cuenta
		//     Creado por :  Lic. Edgar A. Quintero U.
		// Fecha Creaci? :  11/04/2010                    Fecha ltima Modificacion : 
		///////////////////////////////////////////////////////////////////////////////////////////////////

	   $ls_sql = "SELECT DISTINCT sc_cuenta FROM scg_dt_cmp WHERE sc_cuenta between '".$propiedades['sc_cuenta_d']."' AND '".$propiedades['sc_cuenta_h']."' ";
		 //echo $ls_sql; die;	   
	   $rs_cuentas=$this->SQL->select($ls_sql);
	  
	   if ($rs_cuentas===false){			
			$this->io_msg->message("CLASE->sigesp_scg_reporte M?ODO->cuentas_mayor ERROR->".$this->SQL->message);
			return false;
	   }
	   return $rs_cuentas;         
	}//fin de datos_mensual_mayor_analitico
 
  function datos_mensual_mayor_analitico($propiedades=array()) 
	{	
	    //////////////////////////////////////////////////////////////////////////////////////////////////
		//	      Function:  datos_mensual_mayor_analitico 
		//	     Arguments:  Array
		//	       Returns:	 true o false
		//	   Description:  retorna la fecha maxima de periodo y la denominaci? de la cuenta
		//     Creado por :  Lic. Edgar A. Quintero U.
		// Fecha Creaci? :  11/04/2010                    Fecha ltima Modificacion : 
		///////////////////////////////////////////////////////////////////////////////////////////////////

	   $ls_sql = "SELECT
				(SELECT max(fecha) AS fecha_max FROM scg_dt_cmp WHERE scg_dt_cmp.fecha  like '".$propiedades['year']."-%' AND sc_cuenta = '".$propiedades['sc_cuenta']."') AS periodo_maximo, 
				(SELECT denominacion FROM scg_cuentas WHERE sc_cuenta = '".$propiedades['sc_cuenta']."') AS denominacion ";
		 
	   $this->rs_datos_mensual=$this->SQL->select($ls_sql);
	
	   if (($this->rs_datos_mensual===false)){			
			$this->io_msg->message("CLASE->sigesp_scg_reporte M?ODO->datos_mensual_mayor_analitico ERROR->".$this->SQL->message);
			return false;
	   }
	   return true;         
	}//fin de datos_mensual_mayor_analitico
 
 function uf_resumen_mensual_mayor_analitico($propiedades=array()) 
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////
		//	      Function:  uf_resumen_mensual_mayor_analitico 
		//	     Arguments:  Array
		//	       Returns:	 true o false
		//	   Description:  retorna el resumen mensual del a? seleccionado
		//     Creado por :  Lic. Edgar A. Quintero U.
		// Fecha Creaci? :  11/04/2010                    Fecha ltima Modificacion : 
		///////////////////////////////////////////////////////////////////////////////////////////////////

	
	   $periodo = $propiedades['year'].'-'.$propiedades['mes'].'-';
	   
	   $ls_sql = "  SELECT (
							SELECT SUM(scg_dt_cmp.monto) AS debe     
							FROM scg_dt_cmp, sigesp_cmp, scg_cuentas 
							WHERE scg_dt_cmp.codemp = '".$this->ls_codemp."' 
							AND scg_dt_cmp.fecha  like '".$periodo."%'  
							AND scg_dt_cmp.sc_cuenta = '".$propiedades['sc_cuenta']."' 
							AND scg_dt_cmp.debhab = 'D'
							AND scg_dt_cmp.codemp=scg_cuentas.codemp 
							AND scg_dt_cmp.sc_cuenta=scg_cuentas.sc_cuenta 
							AND sigesp_cmp.codemp=scg_dt_cmp.codemp 
							AND sigesp_cmp.procede=scg_dt_cmp.procede 
							AND sigesp_cmp.comprobante=scg_dt_cmp.comprobante 
							AND sigesp_cmp.fecha=scg_dt_cmp.fecha 
							AND sigesp_cmp.codban=scg_dt_cmp.codban 
							AND sigesp_cmp.ctaban=scg_dt_cmp.ctaban 
							GROUP BY scg_dt_cmp.sc_cuenta, scg_dt_cmp.debhab, scg_cuentas.denominacion
							ORDER BY scg_dt_cmp.sc_cuenta
							) AS debe,
							(
							SELECT SUM(scg_dt_cmp.monto) AS haber      
							FROM scg_dt_cmp, sigesp_cmp, scg_cuentas 
							WHERE scg_dt_cmp.codemp = '".$this->ls_codemp."' 
							AND scg_dt_cmp.fecha  like '".$periodo."%'  
							AND scg_dt_cmp.sc_cuenta = '".$propiedades['sc_cuenta']."'
							AND scg_dt_cmp.debhab = 'H'
							AND scg_dt_cmp.codemp=scg_cuentas.codemp 
							AND scg_dt_cmp.sc_cuenta=scg_cuentas.sc_cuenta 
							AND sigesp_cmp.codemp=scg_dt_cmp.codemp 
							AND sigesp_cmp.procede=scg_dt_cmp.procede 
							AND sigesp_cmp.comprobante=scg_dt_cmp.comprobante 
							AND sigesp_cmp.fecha=scg_dt_cmp.fecha 
							AND sigesp_cmp.codban=scg_dt_cmp.codban 
							AND sigesp_cmp.ctaban=scg_dt_cmp.ctaban 
							GROUP BY scg_dt_cmp.sc_cuenta, scg_dt_cmp.debhab, scg_cuentas.denominacion
							ORDER BY scg_dt_cmp.sc_cuenta
							) AS haber,
							(SELECT case sum(debe_mes-haber_mes) when null then 0 else sum(debe_mes-haber_mes) end 
							FROM scg_saldos	 
							WHERE scg_saldos.fecsal < '".$periodo."01' 
							AND scg_saldos.codemp='".$this->ls_codemp."' 
							AND scg_saldos.sc_cuenta='".$propiedades['sc_cuenta']."') As saldo_ant "; 
	   
	   $this->rs_resumen=$this->SQL->select($ls_sql);
		
	   if (($this->rs_resumen===false)){			
			$this->io_msg->message("Error en consulta metodo uf_resumen_mensual_mayor_analitico ".$this->SQL->message);
			return false;
       }
	   return true;         
	}//fin de uf_cargar_mayor analitico
/****************************************************************************************************************************************/	

 
 
	function uf_scg_mayor_analitico_info_cheques($as_comprobante,$as_codban,$as_ctaban)
	{
		$lb_valido=true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$ls_sql=
				"	SELECT 	scb_movbco.numdoc,scb_movbco.fecmov,scb_banco.nomban
					  FROM 	scg_dt_cmp,scb_movbco,scb_banco
					 WHERE	scg_dt_cmp.codemp 		= '$ls_codemp'
					   AND 	scg_dt_cmp.codban 		= '$as_codban'
					   AND 	scg_dt_cmp.ctaban 		= '$as_ctaban'
					   AND	scg_dt_cmp.comprobante 	= '$as_comprobante'
					   AND	scg_dt_cmp.procede 		= 'SCBBCH' 
					   AND	(scg_dt_cmp.codemp 		= scb_movbco.codemp  
					   AND	scg_dt_cmp.codban 		= scb_movbco.codban  
					   AND	scg_dt_cmp.ctaban 		= scb_movbco.ctaban  
					   AND	scg_dt_cmp.comprobante 	= scb_movbco.numdoc)  
					   AND	scb_banco.codemp		= scb_movbco.codemp 
					   AND	scb_banco.codban		= scb_movbco.codban";
		
		$this->rs_info_cheques=$this->SQL->select($ls_sql); //print $ls_sql;
		
		if (($this->rs_info_cheques===false))
	   	{
			$lb_valido=false;
			$this->io_msg->message("Error en consulta metodo uf_scg_mayor_analitico_info_cheques ".$this->SQL->message);
	    }
	   		
		return $lb_valido;
		
		
	}
	



/****************************************************************************************************************************************/	
	/////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SCG  "BALANCE DE COMPROBACION FORMATO 1"   // 
	/////////////////////////////////////////////////////////////////

	function uf_scg_reporte_balance_comprobante($as_cuenta_desde,$as_cuenta_hasta,$ad_fecdesde,$ad_fechasta,$ai_nivel)
	{//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_balance_comprobante
	 //         Access :	public
	 //     Argumentos :    adt_fecini  // fecha  desde 
	//              	    adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
     //	       Returns :	Retorna un Boleano y genera un datastore con datos preparado
	 //	   Description :	Reporte que genera el balance de comprobanciona una detewrminada fecha y cuenta
	 //     Creado por :    Ing. Wilmer Brice??? 
	 // Fecha Creaci??? :    04/02/2006          Fecha ltima Modificacion : 04/02/2006 Hora :
	 	//  Modificado por: Jennifer Rivero
	//  Fecha de la Modificaci??n: 31/01/2008
  	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido = true;	 
	    $ls_codemp = $this->dts_empresa["codemp"];
		$ld_fecdesde=$this->fun->uf_convertirdatetobd($ad_fecdesde);
		$ld_fechasta=$this->fun->uf_convertirdatetobd($ad_fechasta);
		/*$ls_mysql= " SELECT DISTINCT B.sc_cuenta as sc_cuenta,B.denominacion as denominacion,B.saldo_Ant as saldo_ant,B.debe as debe,B.haber as haber,B.saldo_act as saldo_act,C.T_DEBE_MES as t_debe_mes,C.T_HABER_MES as t_haber_mes, ".
		           "       COALESCE(C.T_DEBE_MES,0) as BalDebe,COALESCE(C.T_HABER_MES,0) as BalHABER  ".
		           " FROM (  SELECT A.sc_cuenta,A.denominacion,saldo_ant,COALESCE(curSACT.T_DEBE_MES,0) as Debe,COALESCE(curSACT.T_HABER_MES,0) as Haber,  ".
		           "      	        (COALESCE(Saldo_Ant,0)+COALESCE(curSACT.T_DEBE_MES,0) - COALESCE(curSACT.T_HABER_MES,0)) as Saldo_Act  ".
		           "       	 FROM (SELECT CCT.sc_cuenta,CCT.denominacion,CCT.nivel,COALESCE(curSANT.SANT,0) as Saldo_Ant  ".
				   "  	           FROM scg_cuentas CCT   ".
				   "  	           LEFT OUTER JOIN ( SELECT CSD.sc_cuenta,SUM(debe_mes-haber_mes) as SANT  ".
                   "                                 FROM scg_saldos CSD   ".
				   "					             WHERE CSD.codemp='".$ls_codemp."' AND (CSD.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND CSD.fecsal < '".$ld_fecdesde."' ".
				   "  					             GROUP BY CSD.sc_cuenta ) curSANT    ".
				   "	           ON  CCT.sc_cuenta=curSANT.sc_cuenta ) A LEFT OUTER JOIN  ".
				   "	        ( SELECT CSD.sc_cuenta, COALESCE(SUM(debe_mes),0) As T_DEBE_MES, COALESCE(SUM(haber_mes),0) As T_HABER_MES   ".
				   "		      FROM scg_saldos CSD WHERE CSD.codemp='".$ls_codemp."' AND (CSD.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND CSD.fecsal between '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "		      GROUP BY CSD.sc_cuenta )  curSACT   ".
				   "         ON A.sc_cuenta=curSACT.sc_cuenta  ". 		  
				   "       WHERE  (A.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND (A.nivel<=".$ai_nivel.")) B, ". 
			       "      (  SELECT COALESCE(sum(DEBE_MES),0) as T_DEBE_MES, COALESCE(sum(HABER_MES),0) as T_HABER_MES  ".  
			       "         FROM scg_cuentas CCT, scg_saldos CSD  ".  
			       "         WHERE CCT.codemp='".$ls_codemp."' AND (CCT.sc_cuenta=CSD.sc_cuenta) AND (CSD.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND  ".
			       "               CSD.fecsal between '".$ld_fecdesde."' AND '".$ld_fechasta."' AND (CCT.nivel=1) )  C ".
                   " ORDER BY B.sc_cuenta ";*/ 
				   
		if($_SESSION["ls_gestor"]=='INFORMIX')
		{$ls_mysql= "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion,   MAX (scg_cuentas.nivel) AS nivel, MAX (scg_cuentas.status) AS status, 
         			 case SUM(scg_saldos.debe_mes) when null then 0
         			 else SUM(scg_saldos.debe_mes)
        			 end  debe_mes,
         			 Case SUM(scg_saldos.haber_mes) when null then 0
         			 else SUM(scg_saldos.haber_mes)
         			 end  haber_mes,
         			 0 as debe_mes_ant , 
        			 0 as haber_mes_ant, 
         			 0 as total_debe , 
        			 0 as total_haber   
					FROM scg_cuentas  ".
				   "  LEFT OUTER JOIN scg_saldos  ".
				   "    ON scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".				   
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_cuentas.nivel<=".$ai_nivel."".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion,MAX (scg_cuentas.nivel) AS nivel, MAX (scg_cuentas.status) AS status, 
       				0 as debe_mes,
       				0 as haber_mes, 
      				case SUM(scg_saldos.debe_mes) when null then 0
                    else SUM(scg_saldos.debe_mes)
                    end debe_mes_ant ,
       				Case SUM(scg_saldos.haber_mes) when null then 0
       				else SUM(scg_saldos.haber_mes) 
       				end  haber_mes_ant, 
       				0 as total_debe , 
       				0 as total_haber 
					FROM scg_cuentas, scg_saldos".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal<'".$ld_fecdesde."'". 
				   "   AND scg_cuentas.nivel<=".$ai_nivel."".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion,MAX (scg_cuentas.nivel) AS nivel, MAX (scg_cuentas.status) AS status,  
       				0 as debe_mes, 
       				0 as haber_mes, 
      				0 as debe_mes_ant , 
       				0 as haber_mes_ant, 
       				Case SUM(scg_saldos.debe_mes) when null then 0
       				else SUM(scg_saldos.debe_mes)
       				end  total_debe ,
       				Case SUM(scg_saldos.haber_mes) when null then 0
      				else SUM(scg_saldos.haber_mes)
       				end  total_haber 
					FROM scg_cuentas, scg_saldos  ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.nivel=1".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
                   " ORDER BY sc_cuenta ";
		  }  
		else
		{     
		  $ls_mysql= "SELECT scg_cuentas.sc_cuenta, 
		                     MAX(scg_cuentas.denominacion) AS denominacion,  ".
		           "         MAX(scg_cuentas.nivel) AS nivel, 
				             MAX(scg_cuentas.status) AS status, ".
				   "         COALESCE(SUM(scg_saldos.debe_mes),0) as debe_mes, COALESCE(SUM(scg_saldos.haber_mes),0) as haber_mes,  ".
				   "         COALESCE(0,0) as debe_mes_ant , COALESCE(0,0) as haber_mes_ant,  ".
				   "         COALESCE(0,0) as total_debe , COALESCE(0,0) as total_haber  ".
				   "  FROM scg_cuentas ".
				   "  LEFT OUTER JOIN scg_saldos  ".
				   "    ON scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".				   
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_cuentas.nivel<=".$ai_nivel."".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas.sc_cuenta,
				           MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       MAX(scg_cuentas.nivel) AS nivel, 
				           MAX(scg_cuentas.status) AS status, ".
				   "       COALESCE(0,0) as debe_mes, COALESCE(0,0) as haber_mes, ".
				   "       COALESCE(SUM(scg_saldos.debe_mes),0) as debe_mes_ant ,COALESCE(SUM(scg_saldos.haber_mes),0) as haber_mes_ant, ".
				   "       COALESCE(0,0) as total_debe , COALESCE(0,0) as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal<'".$ld_fecdesde."'". 
				   "   AND scg_cuentas.nivel<=".$ai_nivel."".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       MAX(scg_cuentas.nivel) AS nivel, MAX(scg_cuentas.status) AS status, ".
				   "       COALESCE(0,0) as debe_mes, COALESCE(0,0) as haber_mes,  ".
				   "       COALESCE(0,0) as debe_mes_ant , COALESCE(0,0) as haber_mes_ant,  ".
				   "       COALESCE(SUM(scg_saldos.debe_mes),0) as total_debe , COALESCE(SUM(scg_saldos.haber_mes),0) as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos  ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.nivel=1".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
                   " ORDER BY sc_cuenta ";
		  }
		//print $ls_mysql; 
	
		$rs_balance=$this->SQL->select($ls_mysql);//print $ls_mysql;
		if($rs_balance===false)
		{   	// error interno sql
			$this->io_msg->message("Error en Reporte".$this->fun->uf_convertirmsg($this->SQL->message));
			//print $this->SQL->message;
			$lb_valido = false;	 
		}
		else
		{
			if($row=$this->SQL->fetch_row($rs_balance))
			{
				$this->dts_reporte->data=$this->SQL->obtener_datos($rs_balance);
				$this->dts_reporte->group_by(array('0'=>'sc_cuenta'),array('0'=>'debe_mes','1'=>'haber_mes','2'=>'debe_mes_ant',
				                                   '3'=>'haber_mes_ant','4'=>'total_debe','5'=>'total_haber'),'debe_mes');
			}
			else
			{
				$lb_valido = false;	 
			}
			$this->SQL->free_result($rs_balance);   
		}
        return $lb_valido;
	}//fin balance comprobaci???

	function uf_scg_reporte_balance_comprobante_consolidado($as_cuenta_desde,$as_cuenta_hasta,$ad_fecdesde,$ad_fechasta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_balance_comprobante
	 //         Access :	public
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
     //	       Returns :	Retorna un Boleano y genera un datastore con datos preparado
	 //	   Description :	Reporte que genera el balance de comprobanciona una detewrminada fecha y cuenta
	 //     Creado por :    Ing. Wilmer Brice??? 
	 // Fecha Creaci??? :    04/02/2006          Fecha ltima Modificacion : 04/02/2006 Hora :
	 	//  Modificado por: Jennifer Rivero
	//  Fecha de la Modificaci??n: 31/01/2008
  	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido = true;	 
	    $ls_codemp = $this->dts_empresa["codemp"];
		$ld_fecdesde=$this->fun->uf_convertirdatetobd($ad_fecdesde);
		$ld_fechasta=$this->fun->uf_convertirdatetobd($ad_fechasta);
		if ($_SESSION["ls_gestor"]=='INFORMIX')
		   {$ls_mysql= "SELECT scg_cuentas_consolida.sc_cuenta,
		                       MAX(scg_cuentas_consolida.denominacion) AS denominacion,
							   MAX (scg_cuentas_consolida.nivel) AS nivel,
							   MAX (scg_cuentas.status) AS status, 
							   case SUM(scg_saldos_consolida.debe_mes) when null then 0
							   else SUM(scg_saldos_consolida.debe_mes)
							   end  debe_mes,
							   Case SUM(scg_saldos_consolida.haber_mes) when null then 0
							   else SUM(scg_saldos_consolida.haber_mes)
							   end  haber_mes,
							   0 as debe_mes_ant , 
							   0 as haber_mes_ant, 
							   0 as total_debe , 
							   0 as total_haber   
					FROM scg_cuentas_consolida  ".
				   "  LEFT OUTER JOIN scg_saldos_consolida  ".
				   "    ON scg_saldos_consolida.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp ".
				   "   AND scg_cuentas_consolida.sc_cuenta = scg_saldos_consolida.sc_cuenta ".				   
				   " WHERE scg_cuentas_consolida.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas_consolida.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   " GROUP BY scg_cuentas_consolida.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas_consolida.sc_cuenta, MAX(scg_cuentas_consolida.denominacion) AS denominacion,MAX (scg_cuentas_consolida.nivel) AS nivel, MAX (scg_cuentas_consolida.status) AS status, 
       				0 as debe_mes,
       				0 as haber_mes, 
      				case SUM(scg_saldos_consolida.debe_mes) when null then 0
                    else SUM(scg_saldos_consolida.debe_mes)
                    end debe_mes_ant ,
       				Case SUM(scg_saldos_consolida.haber_mes) when null then 0
       				else SUM(scg_saldos_consolida.haber_mes) 
       				end  haber_mes_ant, 
       				0 as total_debe , 
       				0 as total_haber 
					FROM scg_cuentas_consolida, scg_saldos_consolida".
				   " WHERE scg_cuentas_consolida.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas_consolida.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos_consolida.fecsal<'".$ld_fecdesde."'". 
				   "   AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp ".
				   "   AND scg_cuentas_consolida.sc_cuenta = scg_saldos_consolida.sc_cuenta ".
				   " GROUP BY scg_cuentas_consolida.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas_consolida.sc_cuenta, MAX(scg_cuentas_consolida.denominacion) AS denominacion,MAX (scg_cuentas_consolida.nivel) AS nivel, MAX (scg_cuentas_consolida.status) AS status,  
       				0 as debe_mes, 
       				0 as haber_mes, 
      				0 as debe_mes_ant , 
       				0 as haber_mes_ant, 
       				Case SUM(scg_saldos_consolida.debe_mes) when null then 0
       				else SUM(scg_saldos_consolida.debe_mes)
       				end  total_debe ,
       				Case SUM(scg_saldos_consolida.haber_mes) when null then 0
      				else SUM(scg_saldos_consolida.haber_mes)
       				end  total_haber 
					FROM scg_cuentas_consolida, scg_saldos_consolida  ".
				   " WHERE scg_cuentas_consolida.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas_consolida.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos_consolida.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas_consolida.nivel=1".
				   "   AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp ".
				   "   AND scg_cuentas_consolida.sc_cuenta = scg_saldos_consolida.sc_cuenta ".
				   " GROUP BY scg_cuentas_consolida.sc_cuenta  ".
                   " ORDER BY sc_cuenta ";
		  }  
		else
		   {     
		     $ls_mysql = "SELECT scg_cuentas_consolida.sc_cuenta, 
			                     MAX(scg_cuentas_consolida.denominacion) AS denominacion,
			                     MAX(scg_cuentas_consolida.nivel) AS nivel, 
								 MAX(scg_cuentas_consolida.status) AS status,
				                 COALESCE(SUM(scg_saldos_consolida.debe_mes),0) as debe_mes, 
								 COALESCE(SUM(scg_saldos_consolida.haber_mes),0) as haber_mes,
				   				 COALESCE(0,0) as debe_mes_ant,
								 COALESCE(0,0) as haber_mes_ant,
				   				 COALESCE(0,0) as total_debe,
								 COALESCE(0,0) as total_haber
				            FROM scg_cuentas_consolida
				            LEFT OUTER JOIN scg_saldos_consolida
							  ON scg_saldos_consolida.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."'
							 AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp
							 AND trim(scg_cuentas_consolida.sc_cuenta) = trim(scg_saldos_consolida.sc_cuenta)
						   WHERE scg_cuentas_consolida.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."'
						   GROUP BY scg_cuentas_consolida.sc_cuenta
						   UNION
						  SELECT scg_cuentas_consolida.sc_cuenta,
								 MAX(scg_cuentas_consolida.denominacion) AS denominacion,
								 MAX(scg_cuentas_consolida.nivel) AS nivel, 
								 MAX(scg_cuentas_consolida.status) AS status,
								 COALESCE(0,0) as debe_mes, COALESCE(0,0) as haber_mes,
								 COALESCE(SUM(scg_saldos_consolida.debe_mes),0) as debe_mes_ant,
								 COALESCE(SUM(scg_saldos_consolida.haber_mes),0) as haber_mes_ant,
								 COALESCE(0,0) as total_debe,
								 COALESCE(0,0) as total_haber
							FROM scg_cuentas_consolida, scg_saldos_consolida
						   WHERE scg_cuentas_consolida.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."'
							 AND scg_saldos_consolida.fecsal<'".$ld_fecdesde."'
							 AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp
							 AND trim(scg_cuentas_consolida.sc_cuenta) = trim(scg_saldos_consolida.sc_cuenta)
						   GROUP BY scg_cuentas_consolida.sc_cuenta
						   UNION
				   		  SELECT scg_cuentas_consolida.sc_cuenta, 
						         MAX(scg_cuentas_consolida.denominacion) AS denominacion,
								 MAX(scg_cuentas_consolida.nivel) AS nivel, MAX(scg_cuentas_consolida.status) AS status,
								 COALESCE(0,0) as debe_mes,
								 COALESCE(0,0) as haber_mes,
								 COALESCE(0,0) as debe_mes_ant,
								 COALESCE(0,0) as haber_mes_ant,
								 COALESCE(SUM(scg_saldos_consolida.debe_mes),0) as total_debe,
								 COALESCE(SUM(scg_saldos_consolida.haber_mes),0) as total_haber
							FROM scg_cuentas_consolida, scg_saldos_consolida
						   WHERE scg_cuentas_consolida.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."'
							 AND scg_saldos_consolida.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."'
							 AND scg_cuentas_consolida.nivel = 1
							 AND scg_cuentas_consolida.codemp = scg_saldos_consolida.codemp
							 AND trim(scg_cuentas_consolida.sc_cuenta) = trim(scg_saldos_consolida.sc_cuenta)
						   GROUP BY scg_cuentas_consolida.sc_cuenta
						   ORDER BY sc_cuenta";
		  }
		$rs_balance=$this->SQL->select($ls_mysql);
		if ($rs_balance===false)
		   {
		     $this->io_msg->message("Error en Reporte".$this->fun->uf_convertirmsg($this->SQL->message));
             $lb_valido = false;	 
		   }
		else
		   {
   		     if ($row=$this->SQL->fetch_row($rs_balance))
			    {
				  $this->dts_reporte->data=$this->SQL->obtener_datos($rs_balance);
				  $this->dts_reporte->group_by(array('0'=>'sc_cuenta'),array('0'=>'debe_mes','1'=>'haber_mes','2'=>'debe_mes_ant',
																			   '3'=>'haber_mes_ant','4'=>'total_debe','5'=>'total_haber'),'debe_mes');
			    }
			 else
			    {
				  $lb_valido = false;	 
			    }
	         $this->SQL->free_result($rs_balance);   
		   }
        return $lb_valido;
	}//fin uf_scg_reporte_balance_comprobante_consolidado.

/****************************************************************************************************************************************/	
    function uf_spg_reporte_select_cuenta(&$as_sc_cuenta_min,&$as_sc_cuenta_max)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_cuenta
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta minima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    19/07/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(sc_cuenta) as sc_cuenta_min, max(sc_cuenta) as sc_cuenta_max ".
             " FROM scg_cuentas ".
             " WHERE codemp = '".$ls_codemp."'  AND status='C' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_cuenta ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_sc_cuenta_min=$row["sc_cuenta_min"];
		   $as_sc_cuenta_max=$row["sc_cuenta_max"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
	return $lb_valido;
  }//uf_spg_reporte_select_min_cuenta
/****************************************************************************************************************************************/
    function uf_spg_reporte_select_cuenta_min_max(&$as_sc_cuenta_min,&$as_sc_cuenta_max)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_cuenta_min_max
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta minima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    20/07/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(sc_cuenta) as sc_cuenta_min, max(sc_cuenta) as sc_cuenta_max ".
             " FROM scg_cuentas ".
             " WHERE codemp = '".$ls_codemp."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_cuenta_min_max ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_sc_cuenta_min=$row["sc_cuenta_min"];
		   $as_sc_cuenta_max=$row["sc_cuenta_max"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
	return $lb_valido;
  }//uf_spg_reporte_select_cuenta_min_max
/****************************************************************************************************************************************/
	function uf_scg_reporte_movimientos_mes($as_cuenta_desde,$as_cuenta_hasta,$ad_fecdesde,$ad_fechasta,$as_orden=1)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_scg_reporte_balance_comprobante
	 //         Access :	public
	 //     Argumentos :    adt_fecini  // fecha  desde 
     //              	    adt_fecfin  // fecha hasta 
	 //                     ai_nivel    // nivel de la cuenta  
     //	       Returns :	Retorna un Boleano y genera un datastore con datos preparado
	 //	   Description :	Reporte que genera el balance de comprobanciona una detewrminada fecha y cuenta
	 //     Creado por :    Ing. Wilmer Brice??o 
	 // Fecha Creaci??n :    04/02/2006          Fecha ??ltima Modificacion : 04/02/2006 Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($as_orden==0)
		{
			$ls_orden=" ORDER BY sc_cuenta DESC";
		}
		else
		{
			$ls_orden=" ORDER BY sc_cuenta ASC";
		}
        $lb_existe = false;	 
		$ls_codemp = $this->dts_empresa["codemp"];
		$ld_fecdesde=$this->fun->uf_convertirdatetobd($ad_fecdesde);
		$ld_fechasta=$this->fun->uf_convertirdatetobd($ad_fechasta);
		/*$ls_mysql= " SELECT DISTINCT B.sc_cuenta as sc_cuenta,B.denominacion as denominacion,B.saldo_Ant as saldo_ant,B.debe as debe,B.haber as haber,B.saldo_act as saldo_act,C.T_DEBE_MES as t_debe_mes,C.T_HABER_MES as t_haber_mes, ".
		           "       COALESCE(C.T_DEBE_MES,0) as BalDebe,COALESCE(C.T_HABER_MES,0) as BalHABER  ".
		           " FROM (  SELECT A.sc_cuenta,A.denominacion,saldo_ant,COALESCE(curSACT.T_DEBE_MES,0) as Debe,COALESCE(curSACT.T_HABER_MES,0) as Haber,  ".
		           "      	        (COALESCE(Saldo_Ant,0)+COALESCE(curSACT.T_DEBE_MES,0) - COALESCE(curSACT.T_HABER_MES,0)) as Saldo_Act  ".
		           "       	 FROM (SELECT CCT.sc_cuenta,CCT.denominacion,CCT.nivel,COALESCE(curSANT.SANT,0) as Saldo_Ant  ".
				   "  	           FROM scg_cuentas CCT   ".
				   "  	           LEFT OUTER JOIN ( SELECT CSD.sc_cuenta,SUM(debe_mes-haber_mes) AS SANT  ".
                   "                                 FROM scg_saldos CSD   ".
				   "					             WHERE CSD.codemp='".$ls_codemp."' AND (CSD.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND CSD.fecsal < '".$ld_fecdesde."' ".
				   "  					             GROUP BY CSD.sc_cuenta ) curSANT    ".
				   "	           ON  CCT.sc_cuenta=curSANT.sc_cuenta 
				   				   WHERE CCT.status='C') A LEFT OUTER JOIN  ".
				   "	        ( SELECT CSD.sc_cuenta, COALESCE(SUM(debe_mes),0) As T_DEBE_MES, COALESCE(SUM(haber_mes),0) As T_HABER_MES   ".
				   "		      FROM scg_saldos CSD WHERE CSD.codemp='".$ls_codemp."' AND (CSD.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND CSD.fecsal between '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "		      GROUP BY CSD.sc_cuenta )  curSACT   ".
				   "         ON A.sc_cuenta=curSACT.sc_cuenta  ". 		  
				   "       WHERE  (A.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') ) B, ". 
			       "      (  SELECT COALESCE(sum(DEBE_MES),0) as T_DEBE_MES, COALESCE(sum(HABER_MES),0) as T_HABER_MES  ".  
			       "         FROM scg_cuentas CCT, scg_saldos CSD  ".  
			       "         WHERE CCT.codemp='".$ls_codemp."' AND (CCT.sc_cuenta=CSD.sc_cuenta) AND (CSD.sc_cuenta between '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."') AND  ".
			       "               CSD.fecsal between '".$ld_fecdesde."' AND '".$ld_fechasta."'  AND CCT.status='C')  C ".
                   " ORDER BY B.sc_cuenta ";*/
        if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
		$ls_mysql= "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       Case SUM(scg_saldos.debe_mes) when null then 0 else SUM(scg_saldos.debe_mes) end debe_mes, 
				           Case SUM(scg_saldos.haber_mes) when null then 0 else  SUM(scg_saldos.haber_mes) end haber_mes,  ".
				   "       0 as debe_mes_ant , 
				           0 as haber_mes_ant,  ".
				   "       0 as total_debe , 
				           0 as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos  ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       0 as debe_mes, 
				           0 as haber_mes, ".
				   "       case SUM(scg_saldos.debe_mes) when null then 0 else SUM(scg_saldos.debe_mes) end  debe_mes_ant ,
				           Case SUM(scg_saldos.haber_mes) when null then 0 else SUM(scg_saldos.haber_mes) end haber_mes_ant, ".
				   "       0 as total_debe , 
				           0 as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal<'".$ld_fecdesde."'". 
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT  MAX(scg_cuentas.sc_cuenta) AS sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       0 as debe_mes, 
				           0 as haber_mes,  ".
				   "       0 as debe_mes_ant , 
				           0 as haber_mes_ant,  ".
				   "       case SUM(scg_saldos.debe_mes) when null then 0 else SUM(scg_saldos.debe_mes) end total_debe , 
				           case SUM(scg_saldos.haber_mes) when null then 0 else SUM(scg_saldos.haber_mes) end total_haber  ".
				   "  FROM scg_cuentas, scg_saldos  ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.status='C' ".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.codemp  ".
                   " ORDER BY sc_cuenta "; //print $ls_mysql;
			 }
			 else
			 {
			 $ls_mysql= "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       COALESCE(SUM(scg_saldos.debe_mes),0) as debe_mes, COALESCE(SUM(scg_saldos.haber_mes),0) as haber_mes,  ".
				   "       COALESCE(0,0) as debe_mes_ant , COALESCE(0,0) as haber_mes_ant,  ".
				   "       COALESCE(0,0) as total_debe , COALESCE(0,0) as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos  ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT scg_cuentas.sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       COALESCE(0,0) as debe_mes, COALESCE(0,0) as haber_mes, ".
				   "       COALESCE(SUM(scg_saldos.debe_mes),0) as debe_mes_ant ,COALESCE(SUM(scg_saldos.haber_mes),0) as haber_mes_ant, ".
				   "       COALESCE(0,0) as total_debe , COALESCE(0,0) as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal<'".$ld_fecdesde."'". 
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.sc_cuenta  ".
				   "UNION ".
				   "SELECT  MAX(scg_cuentas.sc_cuenta) AS sc_cuenta, MAX(scg_cuentas.denominacion) AS denominacion, ".
				   "       COALESCE(0,0) as debe_mes, COALESCE(0,0) as haber_mes,  ".
				   "       COALESCE(0,0) as debe_mes_ant , COALESCE(0,0) as haber_mes_ant,  ".
				   "       COALESCE(SUM(scg_saldos.debe_mes),0) as total_debe , COALESCE(SUM(scg_saldos.haber_mes),0) as total_haber  ".
				   "  FROM scg_cuentas, scg_saldos  ".
				   " WHERE scg_cuentas.codemp='".$ls_codemp."' ".
				   "   AND scg_cuentas.sc_cuenta BETWEEN '".$as_cuenta_desde."' AND '".$as_cuenta_hasta."' ".
				   "   AND scg_saldos.fecsal BETWEEN '".$ld_fecdesde."' AND '".$ld_fechasta."' ".
				   "   AND scg_cuentas.status='C' ".
				   "   AND scg_cuentas.codemp = scg_saldos.codemp ".
				   "   AND scg_cuentas.sc_cuenta = scg_saldos.sc_cuenta ".
				   " GROUP BY scg_cuentas.codemp  ".
					$ls_orden; 
			 }	   
		   

		//print $ls_mysql."<br>";
		//return false;
		$rs_balance=$this->SQL->select($ls_mysql);
		if($rs_balance===false)
		{   // error interno sql
		   $this->io_msg->message("Error en Reporte".$this->fun->uf_convertirmsg($this->SQL->message));
           return false;
		}
		else
		{
   		   if($row=$this->SQL->fetch_row($rs_balance))
		   {
				$this->dts_reporte->data=$this->SQL->obtener_datos($rs_balance);
				$this->dts_reporte->group_by(array('0'=>'sc_cuenta'),array('0'=>'debe_mes','1'=>'haber_mes','2'=>'debe_mes_ant',
				                                                           '3'=>'haber_mes_ant','4'=>'total_debe','5'=>'total_haber'),'debe_mes');
				$this->SQL->free_result($rs_balance);
				return true;														   
           }
		   else
		   {
		      return false;
		   }
	       //$this->SQL->free_result($rs_balance);   
		}

        //return true;
	}//fin movimientos del mes

    function uf_spg_reporte_select_cuenta_contable($as_desde,$as_hasta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_cuenta_contable
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta minima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barrag???.
	 // Fecha Creaci??? :    20/07/2006          Fecha ltima Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
 	 if((!empty($as_desde))&&(!empty($as_hasta)))
	 {
	 	$ls_aux=" AND sc_cuenta between '".$as_desde."' AND '".$as_hasta."'";
	 }
	 $ls_sql="SELECT distinct(sc_cuenta),denominacion ".
	 		 "  FROM scg_cuentas ".
			 " WHERE codemp='".$ls_codemp."'".
			 $ls_aux.
			 " ORDER BY sc_cuenta ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_cuenta_contable ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
			$this->dts_reporte->data=$this->SQL->obtener_datos($rs_data);
		}
		else
		{
		   $lb_valido = false;
		}
		$this->SQL->free_result($rs_data);
	 }//else
	return $lb_valido;
  }//uf_spg_reporte_select_cuenta_contable
}//Fin de la Clase...
?>
