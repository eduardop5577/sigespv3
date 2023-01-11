<?php
/***********************************************************************************
* @fecha de modificacion: 02/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

require_once("../../../base/librerias/php/general/sigesp_lib_funciones.php");
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

class sigesp_scg_reporte_comparado_0206
{
    //conexion	
    var $sqlca;   
    //Instancia de la clase funciones.
    var $is_msg_error;
    var $dts_empresa; // datastore empresa
    var $dts_reporte;
    var $obj="";
    var $io_sql;
    var $io_include;
    var $io_connect;
    var $io_function;	
    var $io_msg;
    var $io_fecha;
    var $sigesp_int;
    var $sigesp_int_scg;
    var $sigesp_int_spg;
    var $dts_reporte_final;
    var $dts_scg_cuentas;
    var $dts_reporte_prestamo;
    var $dts_reporte_venta;
    var $dts_spg_cuentas;
    var $dts_spi_cuentas;
    var $int_spi;
/**********************************************************************************************************************************/	
    public function __construct()
    {
		$this->io_function=new class_funciones() ;
		$this->io_include=new sigesp_include();
		$this->io_connect=$this->io_include->uf_conectar();
		$this->io_sql=new class_sql($this->io_connect);		
		$this->obj=new class_datastore();
		$this->dts_reporte=new class_datastore();
		$this->dts_reporte_final=new class_datastore();
		$this->dts_scg_cuentas=new class_datastore();
		$this->dts_spg_cuentas=new class_datastore();
		$this->dts_spi_cuentas=new class_datastore();
		$this->dts_reporte_prestamo=new class_datastore();
		$this->dts_reporte_venta=new class_datastore();
		$this->io_fecha = new class_fecha();
		$this->io_msg=new class_mensajes();
		$this->sigesp_int=new class_sigesp_int();
		$this->sigesp_int_scg=new class_sigesp_int_scg();
		$this->sigesp_int_spg=new class_sigesp_int_spg();
		$this->int_spi=new class_sigesp_int_spi();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->cargarNiveles();
    }

    function uf_scg_reportes_comparados_0206($adt_fecdes,$adt_fechas,$ai_mesdes,$ai_meshas,$ai_cant_mes,$tipo)
    {
            if ($tipo==1)
            {
                $criterio = " AND (sigesp_clasificador_economico.codcuecla LIKE '111%' ";
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '112%' ";                
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '211%') ";                
            }
            if ($tipo==2)
            {
                $criterio = " AND (sigesp_clasificador_economico.codcuecla LIKE '113%' ";
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '212%') ";                
            }
            if ($tipo==3)
            {
                $criterio = " AND (sigesp_clasificador_economico.codcuecla LIKE '121%' ";
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '122%' ";                
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '124%') ";                
            }
            if ($tipo==4)
            {
                $criterio = " AND (sigesp_clasificador_economico.codcuecla LIKE '221%' ";
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '222%' ";                
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '223%' ";                
                $criterio .= " OR sigesp_clasificador_economico.codcuecla LIKE '224%') ";                
            }
            $this->dts_reporte_final->reset_ds();
            $this->dts_reporte->reset_ds();
	    $lb_valido=$this->uf_scg_reportes_init_array($criterio);// INGRESOS CORRIENTES
	    $lb_paso=false;
	    if($lb_valido)
	    {
		$li_total=$this->dts_reporte_final->getRowCount("sc_cuenta");
		for($li_i=1;$li_i<=$li_total;$li_i++)
		{
		    $ls_sc_cuenta=$this->dts_reporte_final->getValue("sc_cuenta",$li_i);
                    $lb_valido=$this->uf_spg_reportes_procesar_cuentas($ls_sc_cuenta,$adt_fecdes,$adt_fechas,$ai_mesdes,$ai_meshas,$ai_cant_mes);
                    if ($lb_valido)
                    {
                        $lb_valido=$this->uf_spi_reportes_procesar_cuentas($ls_sc_cuenta,$adt_fecdes,$adt_fechas,$ai_mesdes,$ai_meshas,$ai_cant_mes);
                    }
                    if ($lb_valido)
                    {
                        $lb_paso=true;
                    }	        
		}//for
            if ($lb_paso)
            {
                $lb_valido=true;
            }	
            if($lb_valido)
            {
                $lb_valido=$this->uf_scg_reportes_organizar_datastore();  
            }
		    
	}//if
	    
	return $lb_valido;
    }//fin uf_scg_reportes_comparados_inversiones_0714
    
    function uf_scg_reportes_organizar_datastore()
    { 
	  $lb_valido=true;
	  $li_total=$this->dts_reporte_final->getRowCount("sc_cuenta");
          
	  for($li_i=1;$li_i<=$li_total;$li_i++)
	  {  
		$ls_sc_cuenta=$this->dts_reporte_final->getValue("sc_cuenta",$li_i);
		$resultado = $this->obtenerReferencia($ls_sc_cuenta);
                $ls_denominacion=$resultado["as_denominacion"];
                $ls_status=$resultado["as_status"]; 
                $li_nivel=$this->dts_reporte_final->getValue("nivel",$li_i);
                $ld_monto_asignado=$this->dts_reporte_final->getValue("monto_asignado",$li_i);
                $ld_monto_programado=$this->dts_reporte_final->getValue("monto_programado",$li_i);
                $ld_monto_modificado=$this->dts_reporte_final->getValue("monto_modificado",$li_i);
                $ld_monto_programado_acumulado=$this->dts_reporte_final->getValue("programado_acumulado",$li_i);
                $ld_monto_ejecutado=$this->dts_reporte_final->getValue("monto_ejecutado",$li_i);
                $ld_monto_ejecutado_acumulado=$this->dts_reporte_final->getValue("ejecutado_acumulado",$li_i);
                $ld_variacion_absoluta=$this->dts_reporte_final->getValue("variacion_absoluta",$li_i);
                $ld_porcentaje_variacion=$this->dts_reporte_final->getValue("porcentaje_variacion",$li_i);
                $ld_variacion_absoluta_acumulada=$this->dts_reporte_final->getValue("variacion_absoluta_acumulada",$li_i);
                $ld_porcentaje_variacion_acumulada=$this->dts_reporte_final->getValue("porcentaje_variacion_acumulado",$li_i);
                $ld_reprog_proxima=$this->dts_reporte_final->getValue("reprogr_prox_periodo",$li_i);

                $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
                $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
                $this->dts_reporte->insertRow("status",$ls_status);
                $this->dts_reporte->insertRow("nivel",$li_nivel);
                $this->dts_reporte->insertRow("monto_asignado",$ld_monto_asignado);
                $this->dts_reporte->insertRow("monto_programado",$ld_monto_programado);
                $this->dts_reporte->insertRow("monto_modificado",$ld_monto_modificado);
                $this->dts_reporte->insertRow("programado_acumulado",$ld_monto_programado_acumulado);
                $this->dts_reporte->insertRow("monto_ejecutado",$ld_monto_ejecutado);
                $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado);
                $this->dts_reporte->insertRow("variacion_absoluta",$ld_variacion_absoluta);
                $this->dts_reporte->insertRow("porcentaje_variacion",$ld_porcentaje_variacion);
                $this->dts_reporte->insertRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada);
                $this->dts_reporte->insertRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada);
                $this->dts_reporte->insertRow("reprogr_prox_periodo",$ld_reprog_proxima);
                $lb_valido=true;
                
                $ls_sc_cuenta=$this->obtenerCuentaSiguiente($ls_sc_cuenta);
                $nivel=$this->obtenerNivel($ls_sc_cuenta);
		while(($nivel>=1)and($lb_valido)and($nivel!=''))
		{  
                    $li_pos=$this->dts_reporte->find("sc_cuenta",$ls_sc_cuenta);
                    if (($ls_sc_cuenta <>"10000000000") && ($ls_sc_cuenta <>"11000000000") && ($ls_sc_cuenta <>"20000000000")  && ($ls_sc_cuenta <>"21000000000"))
                    {
                        if($li_pos>0)
                        {
                           $ld_asignado=$this->dts_reporte->getValue("monto_asignado",$li_pos) +$ld_monto_asignado;
                           $ld_programado=$this->dts_reporte->getValue("monto_programado",$li_pos) + $ld_monto_programado;
                           $ld_modificado=$this->dts_reporte->getValue("monto_modificado",$li_pos) + $ld_monto_modificado;
                           $ld_programado_acumulado=$this->dts_reporte->getValue("programado_acumulado",$li_pos) + $ld_monto_programado_acumulado;
                           $ld_ejecutado=$this->dts_reporte->getValue("monto_ejecutado",$li_pos) + $ld_monto_ejecutado;
                           $ld_ejecutado_acumulado=$this->dts_reporte->getValue("ejecutado_acumulado",$li_pos) + $ld_monto_ejecutado_acumulado;
                           $ld_variacion_absoluta=$this->dts_reporte->getValue("variacion_absoluta",$li_pos) + $ld_variacion_absoluta;
                           $ld_porcentajevariacion=$this->dts_reporte->getValue("porcentaje_variacion",$li_pos) + $ld_porcentaje_variacion;
                           $ld_variacion_absolutaacumulada=$this->dts_reporte->getValue("variacion_absoluta_acumulada",$li_pos) + $ld_variacion_absoluta_acumulada;
                           $ld_porcentaje_variacionacumulado=$this->dts_reporte->getValue("porcentaje_variacion_acumulado",$li_pos) + $ld_porcentaje_variacion_acumulada;

                           $this->dts_reporte->updateRow("monto_asignado",$ld_asignado,$li_pos);
                           $this->dts_reporte->updateRow("monto_programado",$ld_programado,$li_pos);
                           $this->dts_reporte->updateRow("monto_modificado",$ld_modificado,$li_pos);
                           $this->dts_reporte->updateRow("programado_acumulado",$ld_programado_acumulado,$li_pos);
                           $this->dts_reporte->updateRow("monto_ejecutado",$ld_ejecutado,$li_pos);
                           $this->dts_reporte->updateRow("ejecutado_acumulado",$ld_ejecutado_acumulado,$li_pos);
                           $this->dts_reporte->updateRow("variacion_absoluta",$ld_variacion_absoluta,$li_pos);
                           $this->dts_reporte->updateRow("porcentaje_variacion",$ld_porcentajevariacion,$li_pos);
                           $this->dts_reporte->updateRow("variacion_absoluta_acumulada",$ld_variacion_absolutaacumulada,$li_pos);
                           $this->dts_reporte->updateRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacionacumulado,$li_pos);
                        }
                        else
                        {
                            $resultado = $this->obtenerReferencia($ls_sc_cuenta);
                            $ls_denominacion=$resultado["as_denominacion"];
                            $ls_status=$resultado["as_status"]; 
                            $li_nivel=$li_nivel-1;
                            if ($ls_denominacion<>"")
                            {
                                $this->dts_reporte->insertRow("sc_cuenta",$ls_sc_cuenta);
                                $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
                                $this->dts_reporte->insertRow("status",$ls_status);
                                $this->dts_reporte->insertRow("nivel",$li_nivel);
                                $this->dts_reporte->insertRow("monto_asignado",$ld_monto_asignado);
                                $this->dts_reporte->insertRow("monto_programado",$ld_monto_programado);
                                $this->dts_reporte->insertRow("monto_modificado",$ld_monto_modificado);
                                $this->dts_reporte->insertRow("programado_acumulado",$ld_monto_programado_acumulado);
                                $this->dts_reporte->insertRow("monto_ejecutado",$ld_monto_ejecutado);
                                $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado);
                                $this->dts_reporte->insertRow("variacion_absoluta",$ld_variacion_absoluta);
                                $this->dts_reporte->insertRow("porcentaje_variacion",$ld_porcentaje_variacion);
                                $this->dts_reporte->insertRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada);
                                $this->dts_reporte->insertRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada);
                                $this->dts_reporte->insertRow("reprogr_prox_periodo",$ld_reprog_proxima);
                            }                        
                        }
                    }
                    if(($this->obtenerNivel($ls_sc_cuenta)==1)||(!$lb_valido))
                    {
                            break;
                    }
                    $ls_sc_cuenta=$this->obtenerCuentaSiguiente($ls_sc_cuenta);
                    $nivel=$this->obtenerNivel($ls_sc_cuenta);
		}
                $this->dts_reporte->group("sc_cuenta");
	  }//for	
        return $lb_valido;	
    }// fin uf_scg_reportes_organizar_datastore

    function uf_scg_reportes_init_array($criterio)
    { 
        $la_cuenta=array();
        $lb_valido = true;
        $ls_sql = "SELECT sigesp_clasificador_economico.codcuecla ". 
                  "  FROM sigesp_clasificador_economico ".
                  " INNER JOIN spg_cuentas ".
                  "    ON sigesp_clasificador_economico.codemp=spg_cuentas.codemp ".
                  "   AND sigesp_clasificador_economico.codcuecla=spg_cuentas.cueclaeco ".
                  " WHERE sigesp_clasificador_economico.codemp='".$this->ls_codemp."' ".
                   $criterio. 
                  " GROUP BY codcuecla ".
                  " UNION ".
                  "SELECT sigesp_clasificador_economico.codcuecla ". 
                  "  FROM sigesp_clasificador_economico ".
                  " INNER JOIN spi_cuentas ".
                  "    ON sigesp_clasificador_economico.codemp=spi_cuentas.codemp ".
                  "   AND sigesp_clasificador_economico.codcuecla=spi_cuentas.cueclaeco ".
                  " WHERE sigesp_clasificador_economico.codemp='".$this->ls_codemp."' ".
                   $criterio. 
                  " GROUP BY codcuecla ".
                  " ORDER BY codcuecla ";
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $lb_valido=false;
            $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0206   ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
        }
        else
        {
           $i=1;
           while (!$rs_data->EOF)
           {
               $ls_cuenta=$rs_data->fields["codcuecla"];
               $ls_denom="";
               $ls_status="";
               $ls_referencia="";
               $i++;
                $ld_monto_programado=0;		   			$ld_monto_programado_acumulado=0;
                $ld_monto_ejecutado=0; 		 		    $ld_monto_ejecutado_acumulado=0;	
                $ld_variacion_absoluta=0;	   			    $ld_porcentaje_variacion=0;
                $ld_variacion_absoluta_acumulada=0;	    $ld_porcentaje_variacion_acumulada=0;
                $ld_reprog_proxima=0;					    $ls_tipo="";     $li_nivel=""; 
                $ld_monto_asignado=0;                
                $this->dts_reporte_final->insertRow("sc_cuenta",$ls_cuenta);
                $this->dts_reporte_final->insertRow("denominacion",$ls_denom);
                $this->dts_reporte_final->insertRow("tipo","1");
                $this->dts_reporte_final->insertRow("referencia",$ls_referencia);
                $this->dts_reporte_final->insertRow("status",$ls_status);
                $this->dts_reporte_final->insertRow("nivel",$li_nivel);
                $this->dts_reporte_final->insertRow("monto_asignado",$ld_monto_asignado);
                $this->dts_reporte_final->insertRow("monto_programado",$ld_monto_programado);
                $this->dts_reporte_final->insertRow("programado_acumulado",$ld_monto_programado_acumulado);
                $this->dts_reporte_final->insertRow("monto_ejecutado",$ld_monto_ejecutado);
                $this->dts_reporte_final->insertRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado);
                $this->dts_reporte_final->insertRow("variacion_absoluta",$ld_variacion_absoluta);
                $this->dts_reporte_final->insertRow("porcentaje_variacion",$ld_porcentaje_variacion);
                $this->dts_reporte_final->insertRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada);
                $this->dts_reporte_final->insertRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada);
                $this->dts_reporte_final->insertRow("reprogr_prox_periodo",$ld_reprog_proxima);
               
               $rs_data->MoveNext();
           }
           $this->io_sql->free_result($rs_data);
        }    
        return $lb_valido;
    }//fin uf_scg_reportes_init_array()
   
    function obtenerReferencia($as_sc_cuenta)
    { 
        $lb_valido=true;
        $ls_sql = "SELECT descuecla, status   FROM   sigesp_clasificador_economico   WHERE  codcuecla='".$as_sc_cuenta."' ";
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
                $lb_valido=false;
            $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0206  MÉTODO->obtenerReferencia  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
        }
        else
        {
           if($row=$this->io_sql->fetch_row($rs_data))
           {
                $as_denominacion=$row["descuecla"];
                $as_status=$row["status"];
           }
           $this->io_sql->free_result($rs_data);
        }
        $arrResultado['lb_valido']=$lb_valido;
        $arrResultado['as_denominacion']=$as_denominacion;
        $arrResultado['as_status']=$as_status;
        return $arrResultado;		
    }//fin obtenerReferencia()
   
    function uf_scg_reportes_llenar_datastore_cuentas()
    { 
        $ls_sql=" SELECT * FROM   scg_cuentas WHERE  codemp='".$this->ls_codemp."'";
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {   // error interno sql
            $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0206  MÉTODO->uf_spg_reportes_llenar_datastore_cuentas  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
            $lb_valido = false;
        }
        else
        {
            if($row=$this->io_sql->fetch_row($rs_data))
            {
                $datos=$this->io_sql->obtener_datos($rs_data);
                $this->dts_scg_cuentas->data=$datos;
                $lb_valido=true;			
            }
            else
            {
                $lb_valido = false;
            }
            $this->io_sql->free_result($rs_data);   
        }//else
        return  $lb_valido;
    }//fin uf_spg_reportes_llenar_datastore_cuentas()

    function uf_scg_reportes_procesar_cuentas($as_sc_cuenta,$adt_fesdes,$adt_feshas,$ai_mesdes,$ai_meshas,$ai_cant_mes)
    { 
        $ls_gasto=$_SESSION["la_empresa"]["gasto"];	
        $lb_valido=$this->uf_scg_reportes_llenar_datastore_cuentas();
        if($lb_valido)
        {
            $li_total=$this->dts_scg_cuentas->getRowCount("sc_cuenta");
            $li_pos=$this->dts_scg_cuentas->find("sc_cuenta",$as_sc_cuenta);
            if($li_pos>0)
            {
                $ld_enero=0;		   $ld_febrero=0;
                $ld_marzo=0;		   $ld_abril=0;
                $ld_mayo=0;		   $ld_junio=0;
                $ld_julio=0;		   $ld_agosto=0;
                $ld_septiembre=0;    $ld_octubre=0;
                $ld_noviembre=0;	   $ld_diciembre=0;
                $ls_codrep="0714";   $li_nivel="";     $ls_status="";
                $arrResultado=$this->uf_scg_reporte_cargar_programado($ls_codrep,$as_sc_cuenta,$ld_enero,$ld_febrero,$li_nivel,
                                                                                                                       $ls_status,$ld_marzo,$ld_abril,$ld_mayo,$ld_junio,$ld_julio,
                                                                                                                       $ld_agosto,$ld_septiembre,$ld_octubre,$ld_noviembre,$ld_diciembre);
                $ls_status=$arrResultado['as_status'];
                $li_nivel=$arrResultado['ai_nivel'];
                $ld_enero=$arrResultado['ad_enero'];
                $ld_febrero=$arrResultado['ad_febrero'];
                $ld_marzo=$arrResultado['ad_marzo'];
                $ld_abril=$arrResultado['ad_abril'];
                $ld_mayo=$arrResultado['ad_mayo'];
                $ld_junio=$arrResultado['ad_junio'];
                $ld_julio=$arrResultado['ad_julio'];
                $ld_agosto=$arrResultado['ad_agosto'];
                $ld_septiembre=$arrResultado['ad_septiembre'];
                $ld_octubre=$arrResultado['ad_octubre'];
                $ld_noviembre=$arrResultado['ad_noviembre'];
                $ld_diciembre=$arrResultado['ad_diciembre'];
                $lb_valido=$arrResultado['lb_valido'];
                if($lb_valido)
                {
                    // monto programado y programado ejecutado
                    $ld_monto_programado=0;
                    $ld_monto_programado_acumulado=0;
                    $arrResultado=$this->uf_scg_reporte_calcular_programado($ai_mesdes,$ai_meshas,$ld_monto_programado,$ld_monto_programado_acumulado,
                                                                        $ld_enero,$ld_febrero,$ld_marzo,$ld_abril,$ld_mayo,$ld_junio,
                                                                        $ld_julio,$ld_agosto,$ld_septiembre,$ld_octubre,$ld_noviembre,
                                                                        $ld_diciembre);
                    $ld_monto_programado=$arrResultado['ad_monto_programado'];
                    $ld_monto_programado_acumulado=$arrResultado['ad_monto_acumulado'];
                    $lb_valido=$arrResultado['lb_valido'];
                    if($lb_valido)
                    {
                                    //monto ejecutado
                                    $ld_monto_ejecutado=0;
                                    $lb_mayor_dh=true;
                                    $ldt_fesdes=$this->io_fecha->uf_convert_date_to_db($adt_fesdes);
                                    $ldt_feshas=$this->io_fecha->uf_convert_date_to_db($adt_feshas);
                                    $arrResultado=$this->uf_scg_reporte_calcular_ejecutado($as_sc_cuenta,$ldt_fesdes,$ldt_feshas,$ld_monto_ejecutado,
                                                                                        $lb_mayor_dh);
                                    $lb_mayor_dh=$arrResultado['ab_mayor_dh'];
                                    $ld_monto_ejecutado=$arrResultado['ad_monto_ejecutado'];
                                    $lb_valido=$arrResultado['lb_valido'];
                                    if($lb_valido)													
                                    {
                                            //monto ejecutado acumulado
                                            $ld_monto_ejecutado_acumulado=0;
                                            $arrResultado=$this->uf_scg_reporte_calcular_ejecutado_acumulado($as_sc_cuenta,$ldt_fesdes,$ldt_feshas,
                                                                                            $ld_monto_ejecutado_acumulado);
                                            $ld_monto_ejecutado_acumulado=$arrResultado['ad_monto_ejecutado_acumulado'];
                                            $lb_valido=$arrResultado['lb_valido'];
                                            if($lb_valido)
                                            {
                                                    $ls_cuenta=substr($as_sc_cuenta,0,1);
                                                    if($ls_cuenta==$ls_gasto)
                                                    {
                                                            $ls_signo=1;
                                                    }//if
                                                    else
                                                    {
                                                            $ls_signo=-1;
                                                    }//else
                                                    //variacion absoluta  del periodo entre el  monto ejecutado y monto programado
                                                    if($ld_monto_programado>$ld_monto_ejecutado)
                                                    {
                                                     $ld_variacion_absoluta=0-($ld_monto_programado-$ld_monto_ejecutado); 
                                                    }
                                                    else
                                                    {
                                                            if($ld_monto_programado==0)
                                                            { 
                                                                    $ld_variacion_absoluta=$ld_monto_ejecutado; 
                                                            } 
                                                            else 
                                                            { 
                                                                    $ld_variacion_absoluta=$ld_monto_programado-$ld_monto_ejecutado;  
                                                            }
                                                    }
                                                    //variacion porcentual  del periodo entre el  monto ejecutado y monto programado
                                                    if($ld_monto_programado>0)
                                                    { 
                                                           $ld_porcentaje_variacion=(($ld_monto_programado-$ld_monto_ejecutado)/$ld_monto_programado)*100;  
                                                    }
                                                    else
                                                    {
                                                            $ld_porcentaje_variacion=0;  
                                                    }
                                                    if($ld_monto_programado_acumulado==0)
                                                    {
                                                            $ld_varia_acum=$ld_monto_ejecutado_acumulado;
                                                    }
                                                    else
                                                    {
                                                            $ld_varia_acum=$ld_monto_programado_acumulado-$ld_monto_ejecutado_acumulado;
                                                    }
                                                    //variacion absoluta  del monto acumulado
                                                    if($ld_monto_programado_acumulado>$ld_monto_ejecutado_acumulado)
                                                    {
                                                            $ld_variacion_absoluta_acumulada=0-($ld_varia_acum);
                                                    }
                                                    else
                                                    {
                                                            $ld_variacion_absoluta_acumulada=$ld_varia_acum;
                                                    }
                                                    //variacion porcentual del monto acumulado
                                                    if($ld_monto_programado_acumulado>0)
                                                    { 
                                                            $ld_porcentaje_variacion_acumulada=(($ld_monto_programado_acumulado-$ld_monto_ejecutado_acumulado)/$ld_monto_programado_acumulado)*100; 
                                                    }
                                                    else
                                                    { 
                                                            $ld_porcentaje_variacion_acumulada=0; 
                                                    }
                                                    // monto de la inversion proximo mes
                                                    $ld_reprog_proxima=0;
                                                    $ldt_fechadesde=$ai_meshas+1;
                                                    $ldt_fechahasta=$ai_meshas+$ai_cant_mes;
                                                    $arrResultado=$this->uf_scg_reporte_calcular_programado_prox_mes($ldt_fechadesde,$ldt_fechahasta,$ld_reprog_proxima,$ls_codrep,$as_sc_cuenta);
                                                    $ld_reprog_proxima=$arrResultado['ad_monto_programado'];
                                                    $lb_valido=$arrResultado['lb_valido'];
                                                    if($lb_valido)
                                                    {
                                                            $li_pos=$this->dts_reporte_final->find("sc_cuenta",$as_sc_cuenta);
                                                            if($li_pos>0)
                                                            {
                                                                    $ls_denominacion=$this->dts_reporte_final->getValue("denominacion",$li_pos);
                                                                    $ls_tipo=$this->dts_reporte_final->getValue("tipo",$li_pos);   
                                                                    $ls_status=$this->dts_reporte_final->getValue("status",$li_pos);  
                                                                    $li_nivel=$this->dts_reporte_final->getValue("nivel",$li_pos);
                                                                    $this->dts_reporte_final->updateRow("sc_cuenta",$as_sc_cuenta,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("denominacion",$ls_denominacion,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("tipo",$ls_tipo,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("status",$ls_status,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("nivel",$li_nivel,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("monto_programado",$ld_monto_programado,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("programado_acumulado",$ld_monto_programado_acumulado,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("monto_ejecutado",$ld_monto_ejecutado,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("variacion_absoluta",$ld_variacion_absoluta,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("porcentaje_variacion",$ld_porcentaje_variacion,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada,$li_pos);
                                                                    $this->dts_reporte_final->updateRow("reprogr_prox_periodo",$ld_reprog_proxima,$li_pos);
                                                                    $lb_valido=true;
                                                            }//if
                                                    }//if 
                                            }//if
                                    }//if
                            }//if      
                    }//if
            }//if
            else
            {
                    $li_pos=$this->dts_reporte_final->find("sc_cuenta",$as_sc_cuenta);
                    if($li_pos>0)
                    {
                            $ls_denominacion=$this->dts_reporte_final->getValue("denominacion",$li_pos);
                            $ls_tipo=$this->dts_reporte_final->getValue("tipo",$li_pos);   
                            $ls_status=$this->dts_reporte_final->getValue("status",$li_pos);  
                            $li_nivel=$this->dts_reporte_final->getValue("nivel",$li_pos);
                            $ld_monto_programado=0;		   	$ld_monto_programado_acumulado=0;
                            $ld_monto_ejecutado=0; 		 	$ld_monto_ejecutado_acumulado=0;	
                            $ld_variacion_absoluta=0;	   	$ld_porcentaje_variacion=0;
                            $ld_variacion_absoluta_acumulada=0;	$ld_porcentaje_variacion_acumulada=0;
                            $ld_reprog_proxima=0;					      			 

                            $this->dts_reporte_final->updateRow("sc_cuenta",$as_sc_cuenta,$li_pos);
                            $this->dts_reporte_final->updateRow("denominacion",$ls_denominacion,$li_pos);
                            $this->dts_reporte_final->updateRow("tipo",$ls_tipo,$li_pos);
                            $this->dts_reporte_final->updateRow("status",$ls_status,$li_pos);
                            $this->dts_reporte_final->updateRow("nivel",$li_nivel,$li_pos);
                            $this->dts_reporte_final->updateRow("monto_programado",$ld_monto_programado,$li_pos);
                            $this->dts_reporte_final->updateRow("programado_acumulado",$ld_monto_programado_acumulado,$li_pos);
                            $this->dts_reporte_final->updateRow("monto_ejecutado",$ld_monto_ejecutado,$li_pos);
                            $this->dts_reporte_final->updateRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado,$li_pos);
                            $this->dts_reporte_final->updateRow("variacion_absoluta",$ld_variacion_absoluta,$li_pos);
                            $this->dts_reporte_final->updateRow("porcentaje_variacion",$ld_porcentaje_variacion,$li_pos);
                            $this->dts_reporte_final->updateRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada,$li_pos);
                            $this->dts_reporte_final->updateRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada,$li_pos);
                            $this->dts_reporte_final->updateRow("reprogr_prox_periodo",$ld_reprog_proxima,$li_pos);
                            $lb_valido=true;
                    }//if
            }//else
            //}//for
        }//if 
        return $lb_valido;
    }//fin uf_scg_reportes_procesar_cuentas
    

   function uf_scg_reporte_calcular_programado($ai_mesdes,$ai_meshas,$ad_monto_programado,$ad_monto_acumulado,$ad_enero,$ad_febrero,
												$ad_marzo,$ad_abril,$ad_mayo,$ad_junio,$ad_julio,$ad_agosto,$ad_septiembre,
												$ad_octubre,$ad_noviembre,$ad_diciembre)
    {
        $lb_valido=true;
        $li_mesdes=intval($ai_mesdes);
        $li_meshas=intval($ai_meshas);
        if(!(($li_mesdes>=1)&&($li_meshas<=12)))
        {
            $lb_valido=false;
        }
        if($lb_valido)
        {
	   for($i=$li_mesdes;$i<=$li_meshas;$i++)
	   {
		 switch ($li_mesdes)
		 {
			 case 1:
				  $ad_monto_programado=$ad_monto_programado+$ad_enero;
			 break;
			 case 2:
				  $ad_monto_programado=$ad_monto_programado+$ad_febrero;
			 break;
			 case 3:
				  $ad_monto_programado=$ad_monto_programado+$ad_marzo;
			 break;
			 case 4:
				  $ad_monto_programado=$ad_monto_programado+$ad_abril;
			 break;
			 case 5:
				  $ad_monto_programado=$ad_monto_programado+$ad_mayo;
			 break;
			 case 6:
				  $ad_monto_programado=$ad_monto_programado+$ad_junio;
			 break;
			 case 7:
				  $ad_monto_programado=$ad_monto_programado+$ad_julio;
			 break;
			 case 8:
				  $ad_monto_programado=$ad_monto_programado+$ad_agosto;
			 break;
			 case 9:
				  $ad_monto_programado=$ad_monto_programado+$ad_septiembre;
			 break;
			 case 10:
				  $ad_monto_programado=$ad_monto_programado+$ad_octubre;
			 break;
			 case 11:
				  $ad_monto_programado=$ad_monto_programado+$ad_noviembre;
			 break;
			 case 12:
				  $ad_monto_programado=$ad_monto_programado+$ad_diciembre;
			 break;
		 }//switch
	   }//for
	   for($i=1;$i<=$li_meshas;$i++)
	   {
		 switch ($i)
		 {
			 case 1:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_enero;
			 break;
			 case 2:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_febrero;
			 break;
			 case 3:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_marzo;
			 break;
			 case 4:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_abril;
			 break;
			 case 5:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_mayo;
			 break;
			 case 6:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_junio;
			 break;
			 case 7:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_julio;
			 break;
			 case 8:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_agosto;
			 break;
			 case 9:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_septiembre;
			 break;
			 case 10:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_octubre;
			 break;
			 case 11:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_noviembre;
			 break;
			 case 12:
				  $ad_monto_acumulado=$ad_monto_acumulado+$ad_diciembre;
			 break;
		 }//switch
	   }//for		
	   }//if
	$arrResultado['ad_monto_programado']=$ad_monto_programado;
	$arrResultado['ad_monto_acumulado']=$ad_monto_acumulado;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   }//fin uf_scg_reporte_calcular_programado

	function uf_spg_reportes_procesar_cuentas($as_sc_cuenta,$adt_fesdes,$adt_feshas,$ai_mesdes,$ai_meshas,$ai_cant_mes)
	{ 
	$ls_gasto=$_SESSION["la_empresa"]["gasto"];	
	$lb_valido=$this->uf_spg_reportes_llenar_datastore_cuentas();
	if($lb_valido)
	{
			$li_pos=$this->dts_spg_cuentas->find("cueclaeco",$as_sc_cuenta);
			if($li_pos>0)
			{
                            $ls_spg_cuenta=$this->dts_spg_cuentas->getValue("spg_cuenta",$li_pos);
			    $ld_enero=0;		   $ld_febrero=0;
			    $ld_marzo=0;		   $ld_abril=0;
			    $ld_mayo=0;		   $ld_junio=0;
			    $ld_julio=0;		   $ld_agosto=0;
			    $ld_septiembre=0;    $ld_octubre=0;
			    $ld_noviembre=0;	   $ld_diciembre=0;
			    $ls_codrep="0206";   $li_nivel="";     $ls_status="";
			    $arrResultado=$this->uf_spg_reporte_cargar_programado($ls_codrep,$ls_spg_cuenta,$ld_enero,$ld_febrero,$li_nivel,
										$ls_status,$ld_marzo,$ld_abril,$ld_mayo,$ld_junio,$ld_julio,
										$ld_agosto,$ld_septiembre,$ld_octubre,$ld_noviembre,$ld_diciembre);
			    
				$ls_status=$arrResultado['as_status'];
				$li_nivel=$arrResultado['ai_nivel'];
                                $li_asignado=$arrResultado['asignado'];
				$ld_enero=$arrResultado['ad_enero'];
				$ld_febrero=$arrResultado['ad_febrero'];
				$ld_marzo=$arrResultado['ad_marzo'];
				$ld_abril=$arrResultado['ad_abril'];
				$ld_mayo=$arrResultado['ad_mayo'];
				$ld_junio=$arrResultado['ad_junio'];
				$ld_julio=$arrResultado['ad_julio'];
				$ld_agosto=$arrResultado['ad_agosto'];
				$ld_septiembre=$arrResultado['ad_septiembre'];
				$ld_octubre=$arrResultado['ad_octubre'];
				$ld_noviembre=$arrResultado['ad_noviembre'];
				$ld_diciembre=$arrResultado['ad_diciembre'];
				$lb_valido=$arrResultado['lb_valido'];
			  if($lb_valido)
			  {
				  // monto programado y programado ejecutado
				  $ld_monto_programado=0;
				  $ld_monto_programado_acumulado=0;
				  $arrResultado=$this->uf_scg_reporte_calcular_programado($ai_mesdes,$ai_meshas,$ld_monto_programado,$ld_monto_programado_acumulado,
																	   $ld_enero,$ld_febrero,$ld_marzo,$ld_abril,$ld_mayo,$ld_junio,
																	   $ld_julio,$ld_agosto,$ld_septiembre,$ld_octubre,$ld_noviembre,
																	   $ld_diciembre);
                                $ld_monto_programado=$arrResultado['ad_monto_programado'];
                                $ld_monto_programado_acumulado=$arrResultado['ad_monto_acumulado'];
                                $lb_valido=$arrResultado['lb_valido'];

				 if($lb_valido)
				 {
				    //monto ejecutado
					$ld_monto_ejecutado=0;
					$ld_monto_ejecutado_acumulado=0;
					$ldt_fesdes=$this->io_fecha->uf_convert_date_to_db($adt_fesdes);
					$ldt_feshas=$this->io_fecha->uf_convert_date_to_db($adt_feshas);
					$arrResultado=$this->uf_spg_reporte_calcular_ejecutado($ls_spg_cuenta,$ld_monto_ejecutado,$ld_monto_ejecutado_acumulado,
					                                                    $ldt_fesdes,$ldt_feshas);
					$ld_monto_ejecutado=$arrResultado['ad_monto_acumulado'];
					$ld_monto_modificado=$arrResultado['ad_monto_modificado'];
					$ld_monto_ejecutado_acumulado=$arrResultado['ad_monto_ejecutado'];
                                        $ld_monto_modificado_acumulado=$arrResultado['ad_monto_modificado_acumulado'];
					$lb_valido=$arrResultado['lb_valido'];
					  if($lb_valido)
					  {
						  $ls_cuenta=substr($as_sc_cuenta,0,1);
						  if($ls_cuenta==$ls_gasto)
						  {
							 $ls_signo=1;
						  }//if
						  else
						  {
							 $ls_signo=-1;
						  }//else
						  //variacion absoluta  del periodo entre el  monto ejecutado y monto programado
						  if($ld_monto_programado>$ld_monto_ejecutado)
						  {
						   $ld_variacion_absoluta=0-($ld_monto_programado-$ld_monto_ejecutado); 
						  }
						  else
						  {
							   if($ld_monto_programado==0)
							   { 
								  $ld_variacion_absoluta=$ld_monto_ejecutado; 
							   } 
							   else 
							   { 
								  $ld_variacion_absoluta=$ld_monto_programado-$ld_monto_ejecutado;  
							   }
						  }
						 //variacion porcentual  del periodo entre el  monto ejecutado y monto programado
						 if($ld_monto_programado>0)
						 { 
						    $ld_porcentaje_variacion=(($ld_monto_programado-$ld_monto_ejecutado)/$ld_monto_programado)*100;  
						 }
						 else
						 {
						   $ld_porcentaje_variacion=0;  
						 }
						 if($ld_monto_programado_acumulado==0)
						 {
						   $ld_varia_acum=$ld_monto_ejecutado_acumulado;
						 }
						 else
						 {
						   $ld_varia_acum=$ld_monto_programado_acumulado-$ld_monto_ejecutado_acumulado;
						 }
						 //variacion absoluta  del monto acumulado
						 if($ld_monto_programado_acumulado>$ld_monto_ejecutado_acumulado)
						 {
						   $ld_variacion_absoluta_acumulada=0-($ld_varia_acum);
						 }
						 else
						 {
						   $ld_variacion_absoluta_acumulada=$ld_varia_acum;
						 }
						 //variacion porcentual del monto acumulado
						 if($ld_monto_programado_acumulado>0)
						 { 
						    $ld_porcentaje_variacion_acumulada=(($ld_monto_programado_acumulado-$ld_monto_ejecutado_acumulado)/$ld_monto_programado_acumulado)*100; 
						 }
						 else
						 { 
						    $ld_porcentaje_variacion_acumulada=0; 
						 }
						 // monto de la inversion proximo mes
						 $ld_reprog_proxima=0;
						 $ldt_fechadesde=$ai_meshas+1;
						 $ldt_fechahasta=$ai_meshas+$ai_cant_mes;
					     $arrResultado=$this->uf_spg_reporte_calcular_programado_prox_mes($ldt_fechadesde,$ldt_fechahasta,$ld_reprog_proxima,
						                                                               $ls_codrep,$ls_spg_cuenta);
						 $ld_reprog_proxima=$arrResultado['ad_monto_programado'];
						 $lb_valido=$arrResultado['lb_valido'];
					     if($lb_valido)
						 {
							$li_pos=$this->dts_reporte_final->find("sc_cuenta",$as_sc_cuenta);
							//print "posicion en final=".$li_pos;
							if($li_pos>0)
							{
								 $ls_denominacion=$this->dts_reporte_final->getValue("denominacion",$li_pos);
								 $ls_tipo=$this->dts_reporte_final->getValue("tipo",$li_pos);   
								 $ls_status=$this->dts_reporte_final->getValue("status",$li_pos);  
								 $li_nivel=$this->dts_reporte_final->getValue("nivel",$li_pos);
                                                                 $ld_monto_modificado = $li_asignado + $ld_monto_modificado;
                                                                 $ld_monto_programado_acumulado = $ld_monto_programado_acumulado +  $ld_monto_modificado_acumulado;
								 $this->dts_reporte_final->updateRow("sc_cuenta",$as_sc_cuenta,$li_pos);
								 $this->dts_reporte_final->updateRow("denominacion",$ls_denominacion,$li_pos);
								 $this->dts_reporte_final->updateRow("tipo",$ls_tipo,$li_pos);
								 //$this->dts_reporte_final->updateRow("status",$ls_status,$li_pos);
								 $this->dts_reporte_final->updateRow("nivel",$li_nivel,$li_pos);
                                                                 $this->dts_reporte_final->updateRow("monto_asignado",$li_asignado,$li_pos);
								 $this->dts_reporte_final->updateRow("monto_programado",$ld_monto_programado,$li_pos);
                                                                 $this->dts_reporte_final->updateRow("monto_modificado",$ld_monto_modificado,$li_pos);
								 $this->dts_reporte_final->updateRow("programado_acumulado",$ld_monto_programado_acumulado,$li_pos);
								 $this->dts_reporte_final->updateRow("monto_ejecutado",$ld_monto_ejecutado,$li_pos);
								 $this->dts_reporte_final->updateRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado,$li_pos);
								 $this->dts_reporte_final->updateRow("variacion_absoluta",$ld_variacion_absoluta,$li_pos);
								 $this->dts_reporte_final->updateRow("porcentaje_variacion",$ld_porcentaje_variacion,$li_pos);
								 $this->dts_reporte_final->updateRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada,$li_pos);
								 $this->dts_reporte_final->updateRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada,$li_pos);
								 $this->dts_reporte_final->updateRow("reprogr_prox_periodo",$ld_reprog_proxima,$li_pos);
								// print_r($this->dts_reporte_final->data);
						         $lb_valido=true;
						   }//if
						}//if 
					 }//if
				   //}//if
				 }//if      
			  }//if
            }//if
      }//if 
	  return $lb_valido;
	}//fin uf_scg_reportes_procesar_cuentas
/**********************************************************************************************************************************/
    function uf_spg_reportes_llenar_datastore_cuentas()
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_spg_reportes_llenar_datastore_cuentas
	  //        Argumentos :    
      //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Yozelin Barragán.
	  //    Fecha Creación :    03/09/2006                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $ls_sql=" SELECT * ".
              " FROM   spg_cuentas ".
              " WHERE  codemp='0001' ";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {   // error interno sql
		    $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_spg_reportes_llenar_datastore_cuentas  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido = false;
	  }
	  else
	  {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		  $datos=$this->io_sql->obtener_datos($rs_data);
		  $this->dts_spg_cuentas->data=$datos;	
		  $lb_valido=true;			
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);   
	  }//else
    return  $lb_valido;
   }//fin uf_spg_reportes_llenar_datastore_cuentas()
   
/****************************************************************************************************************************************/	
    function uf_spg_reporte_cargar_programado($as_codrep,$as_sc_cuenta,$ad_enero,$ad_febrero,$ai_nivel,$as_status,
					      $ad_marzo,$ad_abril,$ad_mayo,$ad_junio,$ad_julio,$ad_agosto,
					      $ad_septiembre,$ad_octubre,$ad_noviembre,$ad_diciembre)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_cargar_programado
	 //         Access :	private
	 //     Argumentos :    $as_codrep  -->  codigo del reporte
	 //                     $as_sc_cuenta -->  codigo de la  cuenta 
	 //                     $ad_enero .. $ad_diciembre --> monto programado para cada  mes    
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte por referencia los saldos iniciales programados y ejecutados.   
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    24/08/2006               Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = false;
	  if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	    $ls_sql=" SELECT sum(asignado) as asignado, sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spg_plantillareporte ".
              " WHERE  codemp='".$this->ls_codemp."' AND codrep='".$as_codrep."' AND spg_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spg_cuenta, nivel, status, denominacion  ";
	   }
	   else
	   {			  
		 $ls_sql=" SELECT sum(asignado) as asignado, sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spg_cuentas ".
              " WHERE  codemp='".$this->ls_codemp."' AND spg_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spg_cuenta, nivel, status, denominacion";
	   }
	   
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {   // error interno sql
		 // $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_scg_reporte_cargar_programado  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		  $lb_valido = false;
	  }
	  else
	  {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
			   $ai_nivel=$row["nivel"];
			   $as_status=$row["status"];
			   $ad_asignado=$row["asignado"];
			   $ad_enero=$row["enero"];
			   $ad_febrero=$row["febrero"];
			   $ad_marzo=$row["marzo"];
			   $ad_abril=$row["abril"];
			   $ad_mayo=$row["mayo"];
			   $ad_junio=$row["junio"];
			   $ad_julio=$row["julio"];
			   $ad_agosto=$row["agosto"];
			   $ad_septiembre=$row["septiembre"];
			   $ad_octubre=$row["octubre"];
			   $ad_noviembre=$row["noviembre"];
			   $ad_diciembre=$row["diciembre"];
		       $lb_valido = true;
	    }
		else
		{
			   $ai_nivel="";
			   $as_status="";
			   $ad_asignado=0;
			   $ad_enero=0;
			   $ad_febrero=0;
			   $ad_marzo=0;
			   $ad_abril=0;
			   $ad_mayo=0;
			   $ad_junio=0;
			   $ad_julio=0;
			   $ad_agosto=0;
			   $ad_septiembre=0;
			   $ad_octubre=0;
			   $ad_noviembre=0;
			   $ad_diciembre=0;
		       $lb_valido = true;
		}
		$this->io_sql->free_result($rs_data);
      }//else
	$arrResultado['as_status']=$as_status;
	$arrResultado['ai_nivel']=$ai_nivel;
	$arrResultado['asignado']=$ad_asignado;
	$arrResultado['ad_enero']=$ad_enero;
	$arrResultado['ad_febrero']=$ad_febrero;
	$arrResultado['ad_marzo']=$ad_marzo;
	$arrResultado['ad_abril']=$ad_abril;
	$arrResultado['ad_mayo']=$ad_mayo;
	$arrResultado['ad_junio']=$ad_junio;
	$arrResultado['ad_julio']=$ad_julio;
	$arrResultado['ad_agosto']=$ad_agosto;
	$arrResultado['ad_septiembre']=$ad_septiembre;
	$arrResultado['ad_octubre']=$ad_octubre;
	$arrResultado['ad_noviembre']=$ad_noviembre;
	$arrResultado['ad_diciembre']=$ad_diciembre;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   }//fin uf_scg_reporte_select_saldo_empresa
/****************************************************************************************************************************************/	
    function uf_spg_reporte_calcular_ejecutado($as_spg_cuenta,$ad_monto_ejecutado,$ad_monto_acumulado,$adt_fecini,$adt_fecfin)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_calcular_ejecutado
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta
	 //                     $as_tipo  //  tipo
	 //                     $ad_monto_ejecutado  //  monto ejecutado (referencia)
	 //                     $ad_monto_acumulado  //  monto  acumulado (referencia)
     //              	    $ad_aumdismes  // aumento  disminucion (referencia)
     //              	    $ad_aumdisacum  // aumento  disminucion acumulada (referencia)
	 //                     $ad_comprometer  //  monto comprometido (referencia)
	 //                     $ad_causado  // monto causado (referencia)
	 //                     $ad_pagado  // monto pagado (referencia)
	 //                     $adt_fecini  // fecha inicio
	 //                     $adt_fecfin  // fecha fin
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	Reporte que genera salida para el Formato 3 de la ejecucucion financiera
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    25/08/2006         Fecha última Modificacion :      Hora :
  	 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;
	  $ad_monto_ejecutado=0;	  $ad_monto_acumulado=0;
          $ad_monto_modificado=0;
          $ad_monto_modificado_acumulado;
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $ls_codemp = $_SESSION["la_empresa"]["codemp"];
	  $l_mesdes=substr($adt_fecini,5,2);
	  $l_meshas=substr($adt_fecfin,5,2);
	  $li_mesdes=intval($l_mesdes);
	  $li_meshas=intval($l_meshas);
	  $ldt_mesdes=$li_ano."-".$this->io_function->uf_cerosizquierda($li_mesdes,2);
	  $ldt_meshas=$li_ano."-".$this->io_function->uf_cerosizquierda($li_meshas,2);
	  if($li_mesdes>3)
	  {
			$ldt_mesantdes=$li_ano."-".$this->io_function->uf_cerosizquierda(($li_mesdes-3),2);
			$ldt_mesanthas=$li_ano."-".$this->io_function->uf_cerosizquierda(($li_mesdes-1),2);
	  }
	  else
	  {
			$ldt_mesantdes=$ldt_mesdes;
			$ldt_mesanthas=$ldt_meshas;
	  }	  
	  
	  //$as_spg_cuenta=$as_spg_cuenta."%";
	  $as_spg_cuenta=$this->sigesp_int_spg->uf_spg_cuenta_sin_cero($as_spg_cuenta)."%";
	  $ls_sql=" SELECT DT.fecha, DT.monto, OP.aumento, OP.disminucion, OP.precomprometer,OP.comprometer, OP.causar, OP.pagar ".
			  " FROM   spg_dt_cmp DT, spg_operaciones OP ".
			  " WHERE  DT.codemp='".$ls_codemp."' AND (DT.operacion = OP.operacion) AND ".
			  "        spg_cuenta like '".$as_spg_cuenta."' ";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_spg_reporte_calcular_ejecutado  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_data))
		{
			  $li_aumento=$row["aumento"];
			  $li_disminucion=$row["disminucion"];
			  $li_precomprometer=$row["precomprometer"];
			  $li_comprometer=$row["comprometer"];
			  $li_causar=$row["causar"];
			  $li_pagar=$row["pagar"];
			  $ld_monto=$row["monto"];
			  $ldt_fecha_db=$row["fecha"];
			  $ldt_fecha=substr($ldt_fecha_db,0,7);
			 if(($li_comprometer)&&($ldt_fecha>=$ldt_mesdes)&&($ldt_fecha<=$ldt_meshas))
			 { 
				$ad_monto_ejecutado=$ad_monto_ejecutado+$ld_monto;
			 }//if
			 if(($li_comprometer)&&($ldt_fecha<=$ldt_meshas))
			 {  
				$ad_monto_acumulado=$ad_monto_acumulado+$ld_monto;
			 }//if
			 if(($li_aumento)&&($ldt_fecha>=$ldt_mesdes)&&($ldt_fecha<=$ldt_meshas))
			 { 
				$ad_monto_modificado=$ad_monto_modificado+$ld_monto;
			 }//if
			 if(($li_disminucion)&&($ldt_fecha>=$ldt_mesdes)&&($ldt_fecha<=$ldt_meshas))
			 { 
				$ad_monto_modificado=$ad_monto_modificado+$ld_monto;
			 }//if
			 if(($li_aumento)&&($ldt_fecha<=$ldt_meshas))
			 { 
				$ad_monto_modificado_acumulado=$ad_monto_modificado_acumulado+$ld_monto;
			 }//if
			 if(($li_disminucion)&&($ldt_fecha<=$ldt_meshas))
			 { 
				$ad_monto_modificado_acumulado=$ad_monto_modificado_acumulado+$ld_monto;
			 }//if
                         
		}//while
	   $this->io_sql->free_result($rs_data);
	  }//else	
	$arrResultado['ad_monto_acumulado']=$ad_monto_acumulado;
	$arrResultado['ad_monto_modificado']=$ad_monto_modificado;
	$arrResultado['ad_monto_ejecutado']=$ad_monto_ejecutado;
        $arrResultado['ad_monto_modificado_acumulado']=$ad_monto_modificado_acumulado;
        
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   }//fin uf_spg_reporte_calcular_ejecutado
/**********************************************************************************************************************************/	
    function uf_spg_reporte_calcular_programado_prox_mes($li_mesdes,$li_meshas,$ad_monto_programado,$as_codrep,$as_sc_cuenta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_calcular_programado_prox_mes
	 //         Access :	private
	 //     Argumentos :    $as_mesdes  // mes  desde
     //              	    $as_meshas  // mes hasta
	 //                     $ad_monto_programado // monto programado del mes (referencia)  
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	metodo que calcula los montos programados y los acumulados
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    04/09/2006          Fecha última Modificacion :              Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;	
	  $ad_monto_programado=0; 
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	    $ls_sql=" SELECT sum(asignado) as asignado, sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spg_plantillareporte ".
              " WHERE  codemp='".$this->ls_codemp."' AND codrep='".$as_codrep."' AND spg_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spg_cuenta, nivel, status, denominacion ";
	   }
	   else
	   {
	   $ls_sql=" SELECT sum(asignado) as asignado, sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spg_plantillareporte ".
              " WHERE  codemp='".$this->ls_codemp."' AND codrep='".$as_codrep."' AND spg_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spg_cuenta, nivel, status, denominacion ";
	   }
	   
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	     $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_spg_reporte_calcular_programado_prox_mes  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		 $lb_valido = false;
	 }
	 else
	 {
	  	if($row=$this->io_sql->fetch_row($rs_data))
	    {
		   if(!(($li_mesdes>=1)&&($li_meshas<=12)))
		   {
		     return false;
		   }
		   for($i=$li_mesdes;$i<=$li_meshas;$i++)
		   {
		     switch ($li_mesdes)
			 {   
			     case 1:
			          $ad_monto_programado=$ad_monto_programado+$row["enero"];
				 break;
			     case 2:
			          $ad_monto_programado=$ad_monto_programado+$row["febrero"];
				 break;
			     case 3:
			          $ad_monto_programado=$ad_monto_programado+$row["marzo"];
				 break;
			     case 4:
			          $ad_monto_programado=$ad_monto_programado+$row["abril"];
				 break;
			     case 5:
			          $ad_monto_programado=$ad_monto_programado+$row["mayo"];
				 break;
			     case 6:
			          $ad_monto_programado=$ad_monto_programado+$row["junio"];
				 break;
			     case 7:
			          $ad_monto_programado=$ad_monto_programado+$row["julio"];
				 break;
			     case 8:
			          $ad_monto_programado=$ad_monto_programado+$row["agosto"];
				 break;
			     case 9:
			          $ad_monto_programado=$ad_monto_programado+$row["septiembre"];
				 break;
			     case 10:
			          $ad_monto_programado=$ad_monto_programado+$row["octubre"];
				 break;
			     case 11:
			          $ad_monto_programado=$ad_monto_programado+$row["noviembre"];
				 break;
			     case 12:
			          $ad_monto_programado=$ad_monto_programado+$row["diciembre"];
				 break;
			 }//switch
		   }//for		   
		}//if
	    $this->io_sql->free_result($rs_data);
     }//else
	$arrResultado['ad_monto_programado']=$ad_monto_programado;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   }//fin uf_spg_reporte_calcular_programado_prox_mes
/**********************************************************************************************************************************/	
	function uf_spi_reportes_procesar_cuentas($as_sc_cuenta,$adt_fesdes,$adt_feshas,$ai_mesdes,$ai_meshas,$ai_cant_mes)
	{ ///////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_spi_reportes_procesar_cuentas
	  //        Argumentos :    $as_sc_cuenta --> codigo de la cuentas
	  //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Yozelin Barragán.
	  //    Fecha Creación :    03/09/2006                       Fecha última Modificacion :      Hora :
	  ////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=$this->uf_spi_reportes_llenar_datastore_cuentas();
      if($lb_valido)
	  {
	    $li_total=$this->dts_spi_cuentas->getRowCount("spi_cuenta");
		$li_pos=$this->dts_spi_cuentas->find("cueclaeco",$as_sc_cuenta);
			if($li_pos>0)
			{
                          $ls_spi_cuenta=$this->dts_spi_cuentas->getValue("spi_cuenta",$li_pos);
			  $ld_enero=0;		   $ld_febrero=0;
			  $ld_marzo=0;		   $ld_abril=0;
			  $ld_mayo=0;		   $ld_junio=0;
			  $ld_julio=0;		   $ld_agosto=0;
			  $ld_septiembre=0;    $ld_octubre=0;
			  $ld_noviembre=0;	   $ld_diciembre=0;
			  $ls_codrep="0714";   $li_nivel="";     $ls_status="";
			  $arrResultado=$this->uf_spi_reporte_cargar_programado($ls_codrep,$ls_spi_cuenta,$ld_enero,$ld_febrero,$li_nivel,
																    $ls_status,$ld_marzo,$ld_abril,$ld_mayo,$ld_junio,$ld_julio,
																    $ld_agosto,$ld_septiembre,$ld_octubre,$ld_noviembre,$ld_diciembre);
				$ls_status=$arrResultado['as_status'];
				$li_nivel=$arrResultado['ai_nivel'];
                                $ld_asignado=$arrResultado['ad_asignado'];
				$ld_enero=$arrResultado['ad_enero'];
				$ld_febrero=$arrResultado['ad_febrero'];
				$ld_marzo=$arrResultado['ad_marzo'];
				$ld_abril=$arrResultado['ad_abril'];
				$ld_mayo=$arrResultado['ad_mayo'];
				$ld_junio=$arrResultado['ad_junio'];
				$ld_julio=$arrResultado['ad_julio'];
				$ld_agosto=$arrResultado['ad_agosto'];
				$ld_septiembre=$arrResultado['ad_septiembre'];
				$ld_octubre=$arrResultado['ad_octubre'];
				$ld_noviembre=$arrResultado['ad_noviembre'];
				$ld_diciembre=$arrResultado['ad_diciembre'];
				$lb_valido=$arrResultado['lb_valido'];
			  if($lb_valido)
			  {
				  // monto programado y programado ejecutado
				  $ld_monto_programado=0;
				  $ld_monto_programado_acumulado=0;
				  $arrResultado=$this->uf_scg_reporte_calcular_programado($ai_mesdes,$ai_meshas,$ld_monto_programado,$ld_monto_programado_acumulado,
																	   $ld_enero,$ld_febrero,$ld_marzo,$ld_abril,$ld_mayo,$ld_junio,
																	   $ld_julio,$ld_agosto,$ld_septiembre,$ld_octubre,$ld_noviembre,
																	   $ld_diciembre);
                                $ld_monto_programado=$arrResultado['ad_monto_programado'];
                                $ld_monto_programado_acumulado=$arrResultado['ad_monto_acumulado'];
                                $lb_valido=$arrResultado['lb_valido'];
				 
                                 if($lb_valido)
				 {
				    //monto ejecutado
					$ld_monto_ejecutado=0;
					$ld_monto_ejecutado_acumulado=0;
					$ld_previsto=0;
					$ld_devengado=0;
					$ld_cobrado_anticipado=0;
					$ld_aumento=0;
					$ld_disminucion=0;
					$ldt_fesdes=$this->io_fecha->uf_convert_date_to_db($adt_fesdes);
					$ldt_feshas=$this->io_fecha->uf_convert_date_to_db($adt_feshas);
					$arrResultado=$this->uf_spi_reporte_calcular_ejecutado($ls_spi_cuenta,$ld_previsto,$ld_devengado,$ld_monto_ejecutado,
					                                                    $ld_monto_ejecutado_acumulado,$ld_cobrado_anticipado,
																		$ld_aumento,$ld_disminucion,$ldt_fesdes,$ldt_feshas);
					$ld_previsto=$arrResultado['ad_previsto'];
					$ld_devengado=$arrResultado['ad_devengado'];
					$ld_monto_ejecutado=$arrResultado['ad_cobrado'];
					$ld_monto_ejecutado_acumulado=$arrResultado['ad_cobrado_acumulado'];
					$ld_cobrado_anticipado=$arrResultado['ad_cobrado_anticipado'];
					$ld_aumento=$arrResultado['ad_aumento'];
					$ld_disminucion=$arrResultado['ad_disminucion'];
					$ld_aumento_acumulado=$arrResultado['ad_aumento_acumulado'];
					$ld_disminucion_acumulado=$arrResultado['ad_disminucion_acumulado'];
                                        $ld_monto_modificado = $ld_aumento - $ld_disminucion;
                                        $ld_monto_modificado_acumulado = $ld_aumento_acumulado - $ld_disminucion_acumulado;
					$lb_valido=$arrResultado['lb_valido'];
					  if($lb_valido)
					  {
						  //variacion absoluta  del periodo entre el  monto ejecutado y monto programado
						  if($ld_monto_programado>$ld_monto_ejecutado)
						  {
						   $ld_variacion_absoluta=0-($ld_monto_programado-$ld_monto_ejecutado); 
						  }
						  else
						  {
							   if($ld_monto_programado==0)
							   { 
								  $ld_variacion_absoluta=$ld_monto_ejecutado; 
							   } 
							   else 
							   { 
								  $ld_variacion_absoluta=$ld_monto_programado-$ld_monto_ejecutado;  
							   }
						  }
						 //variacion porcentual  del periodo entre el  monto ejecutado y monto programado
						 if(($ld_monto_programado>0)&&($ld_monto_ejecutado>0))
						 { 
						    $ld_porcentaje_variacion=($ld_monto_programado-$ld_monto_ejecutado)/($ld_monto_ejecutado*100);  
						 	//print " cta=".$as_sc_cuenta."  ".$ld_porcentaje_variacion." prog=".$ld_monto_programado."  ejecutado=".$ld_monto_ejecutado."<br>";
						 }
						 else
						 {
						   $ld_porcentaje_variacion=0;  
						 }
						 if($ld_monto_programado_acumulado==0)
						 {
						   $ld_varia_acum=$ld_monto_ejecutado_acumulado;
						 }
						 else
						 {
						   $ld_varia_acum=$ld_monto_programado_acumulado-$ld_monto_ejecutado_acumulado;
						 }
						 //variacion absoluta  del monto acumulado
						 if($ld_monto_programado_acumulado>$ld_monto_ejecutado_acumulado)
						 {
						   $ld_variacion_absoluta_acumulada=0-($ld_varia_acum);
						 }
						 else
						 {
						   $ld_variacion_absoluta_acumulada=$ld_varia_acum;
						 }
						 //variacion porcentual del monto acumulado
						 if(($ld_monto_programado_acumulado>0)&&($ld_monto_ejecutado_acumulado>0))
						 { 
						    $ld_porcentaje_variacion_acumulada=($ld_monto_programado_acumulado-$ld_monto_ejecutado_acumulado)/($ld_monto_ejecutado_acumulado*100); 
						 }
						 else
						 { 
						    $ld_porcentaje_variacion_acumulada=0; 
						 }
						 // monto de la inversion proximo mes
						 $ld_reprog_proxima=0;
						 $ldt_fechadesde=$ai_meshas+1;
						 $ldt_fechahasta=$ai_meshas+$ai_cant_mes;
					     $arrResultado=$this->uf_spi_reporte_calcular_programado_prox_mes($ldt_fechadesde,$ldt_fechahasta,$ld_reprog_proxima,
						                                                               $ls_codrep,$as_sc_cuenta);
						 $lb_valido=$arrResultado['lb_valido'];
						 $ld_reprog_proxima=$arrResultado['ad_monto_programado'];
					     if($lb_valido)
						 {
							$li_pos=$this->dts_reporte_final->find("sc_cuenta",$as_sc_cuenta);
							if($li_pos>0)
							{
								 $ls_denominacion=$this->dts_reporte_final->getValue("denominacion",$li_pos);
								 $ls_tipo=$this->dts_reporte_final->getValue("tipo",$li_pos);   
								 $ls_status=$this->dts_reporte_final->getValue("status",$li_pos);  
								 $li_nivel=$this->dts_reporte_final->getValue("nivel",$li_pos);
                                                                 $ld_monto_modificado = $ld_previsto + $ld_monto_modificado;
                                                                 $ld_monto_programado_acumulado = $ld_monto_programado_acumulado + $ld_monto_modificado_acumulado;

                                                                 $this->dts_reporte_final->updateRow("sc_cuenta",$as_sc_cuenta,$li_pos);
								 $this->dts_reporte_final->updateRow("denominacion",$ls_denominacion,$li_pos);
								 $this->dts_reporte_final->updateRow("tipo",$ls_tipo,$li_pos);
								 $this->dts_reporte_final->updateRow("status",$ls_status,$li_pos);
								 $this->dts_reporte_final->updateRow("nivel",$li_nivel,$li_pos);
                                                                 $this->dts_reporte_final->updateRow("monto_asignado",$ld_previsto,$li_pos);
								 $this->dts_reporte_final->updateRow("monto_programado",$ld_monto_programado,$li_pos);
                                                                 $this->dts_reporte_final->updateRow("monto_modificado",$ld_monto_modificado,$li_pos);
								 $this->dts_reporte_final->updateRow("programado_acumulado",$ld_monto_programado_acumulado,$li_pos);
								 $this->dts_reporte_final->updateRow("monto_ejecutado",$ld_monto_ejecutado,$li_pos);
								 $this->dts_reporte_final->updateRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado,$li_pos);
								 $this->dts_reporte_final->updateRow("variacion_absoluta",$ld_variacion_absoluta,$li_pos);
								 $this->dts_reporte_final->updateRow("porcentaje_variacion",$ld_porcentaje_variacion,$li_pos);
								 $this->dts_reporte_final->updateRow("variacion_absoluta_acumulada",$ld_variacion_absoluta_acumulada,$li_pos);
								 $this->dts_reporte_final->updateRow("porcentaje_variacion_acumulado",$ld_porcentaje_variacion_acumulada,$li_pos);
								 $this->dts_reporte_final->updateRow("reprogr_prox_periodo",$ld_reprog_proxima,$li_pos);
						         $lb_valido=true;
						   }//if
						}//if 
					 }//if
				   //}//if
				 }//if      
			  }//if
            }//if
      }//if 
	  return $lb_valido;
	}//fin uf_spi_reportes_procesar_cuentas
/**********************************************************************************************************************************/
    function uf_spi_reportes_llenar_datastore_cuentas()
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_spi_reportes_llenar_datastore_cuentas
	  //        Argumentos :    
      //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Yozelin Barragán.
	  //    Fecha Creación :    03/09/2006                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $ls_sql=" SELECT * ".
              " FROM   spi_cuentas ".
              " WHERE  codemp='0001' ";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {   // error interno sql
		    $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_spg_reportes_llenar_datastore_cuentas  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
			$lb_valido = false;
	  }
	  else
	  {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		  $datos=$this->io_sql->obtener_datos($rs_data);
		  $this->dts_spi_cuentas->data=$datos;
		  $lb_valido=true;			
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);   
	  }//else
    return  $lb_valido;
   }//fin uf_spi_reportes_llenar_datastore_cuentas()
/****************************************************************************************************************************************/	
    function uf_spi_reporte_calcular_ejecutado($as_spi_cuenta,$ad_previsto,$ad_devengado,$ad_cobrado,$ad_cobrado_acumulado,
	                                           $ad_cobrado_anticipado,$ad_aumento,$ad_disminucion,$adt_fecini,$adt_fecfin)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_calcular_ejecutado
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta
	 //                     $ad_previsto  //  monto previsto (referencia)
	 //                     $ad_devengado  //  monto  devengado (referencia)
     //              	    $ad_cobrado  // monto cobrado (referencia)
     //              	    $ad_cobrado_anticipado  // cobrado anticipado (referencia)
	 //                     $ad_aumento  //  monto aumento (referencia)
	 //                     $ad_disminucion  // monto disminucion (referencia)
	 //                     $adt_fecini  // fecha inicio
	 //                     $adt_fecfin  // fecha fin
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	Reporte que genera salida para el Formato 3 de la ejecucucion financiera
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    25/08/2006         Fecha última Modificacion :      Hora :
  	 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido=true;	
	  $ad_previsto=0;	          $ad_devengado=0;
	  $ad_cobrado=0;	          $ad_cobrado_anticipado=0;
	  $ad_aumento=0;	          $ad_disminucion=0;
	  $ad_cobrado_acumulado=0;
          $ad_aumento_acumulado=0;	          $ad_disminucion_acumulado=0;
	  $ldt_periodo=$_SESSION["la_empresa"]["periodo"];
	  $li_ano=substr($ldt_periodo,0,4);
	  $ls_gestor = $_SESSION["ls_gestor"];
	  $l_mesdes=substr($adt_fecini,5,2);
	  $l_meshas=substr($adt_fecfin,5,2);
	  $li_mesdes=intval($l_mesdes);
	  $li_meshas=intval($l_meshas);
	  $ldt_mesdes=$li_ano."-".$this->io_function->uf_cerosizquierda($li_mesdes,2);
	  $ldt_meshas=$li_ano."-".$this->io_function->uf_cerosizquierda($li_meshas,2);
	  $as_spi_cuenta=$this->int_spi->uf_spi_cuenta_sin_cero($as_spi_cuenta);
	  $as_spi_cuenta=$as_spi_cuenta."%";
	  $ls_sql=" SELECT * ".
			  " FROM   spi_dt_cmp ".
			  " WHERE  codemp='".$this->ls_codemp."'  AND  spi_cuenta like '".$as_spi_cuenta."'";
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  { // error interno sql
		$this->io_msg->message("CLASE->sigesp_scg_reporte_comparado_0206  MÉTODO->uf_spi_reporte_calcular_ejecutado  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
 	  }
	  else
	  {
		while($row=$this->io_sql->fetch_row($rs_data))
		{
		  $ls_operacion=$row["operacion"];
		  $ld_monto=$row["monto"];
		  $ldt_fecha_db=$row["fecha"];
		  $ldt_fecha=substr($ldt_fecha_db,0,7);
		  $ldt_fecha=str_replace("-","",$ldt_fecha);
		  $ldt_mesdes=str_replace("-","",$ldt_mesdes);
		  $ldt_meshas=str_replace("-","",$ldt_meshas);
		  $ls_opera=$this->int_spi->uf_operacion_codigo_mensaje($ls_operacion);
		  $ls_mensaje=strtoupper($ls_opera); // devuelve cadena en MAYUSCULAS
		  $li_pos_i=strpos($ls_mensaje,"I"); 
		  if (!($li_pos_i===false)) 
		  { 
		    $ad_previsto=$ad_previsto+$ld_monto; 
		  }
		 
		  if($ldt_fecha<=$ldt_meshas)
		  {
			  $li_pos_c=strpos($ls_mensaje,"C"); 
			  if (!($li_pos_c===false)) 
			  {	
				 $ad_cobrado_acumulado=$ad_cobrado_acumulado+$ld_monto;
			  }  
			  $li_pos_a=strpos($ls_mensaje,"A"); 
			  if (!($li_pos_a===false))
			  {	
			    $ad_aumento_acumulado = $ad_aumento_acumulado+$ld_monto; 
			  }
			  $li_pos_d=strpos($ls_mensaje,"D"); 
			  if (!($li_pos_d===false))
			  {	
			    $ad_disminucion_acumulado = $ad_disminucion_acumulado+$ld_monto; 
			  }

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
	$arrResultado['ad_devengado']=$ad_devengado;
	$arrResultado['ad_cobrado']=$ad_cobrado;
	$arrResultado['ad_cobrado_acumulado']=$ad_cobrado_acumulado;
	$arrResultado['ad_cobrado_anticipado']=$ad_cobrado_anticipado;
	$arrResultado['ad_aumento']=$ad_aumento;
	$arrResultado['ad_disminucion']=$ad_disminucion;
	$arrResultado['ad_aumento_acumulado']=$ad_aumento_acumulado;
	$arrResultado['ad_disminucion_acumulado']=$ad_disminucion_acumulado;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   }//fin uf_spi_reporte_calcular_ejecutado
/****************************************************************************************************************************************/
    function uf_spi_reporte_calcular_programado_prox_mes($li_mesdes,$li_meshas,$ad_monto_programado,$as_codrep,$as_sc_cuenta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spi_reporte_calcular_programado_prox_mes
	 //         Access :	private
	 //     Argumentos :    $as_mesdes  // mes  desde
     //              	    $as_meshas  // mes hasta
	 //                     $ad_monto_programado // monto programado del mes (referencia)  
     //	       Returns :	Retorna true o false si se realizo el metodo para el reporte
	 //	   Description :	metodo que calcula los montos programados y los acumulados
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    04/09/2006          Fecha última Modificacion :              Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = true;	
	  $ad_monto_programado=0; 
	   if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	     $ls_sql=" SELECT sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spi_plantillacuentareporte ".
              " WHERE  codemp='".$this->ls_codemp."' AND cod_report='".$as_codrep."' AND spi_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spi_cuenta, nivel, status, denominacion ";
	  }
	  else
	  {
	    $ls_sql=" SELECT sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spi_plantillacuentareporte ".
              " WHERE  codemp='".$this->ls_codemp."' AND cod_report='".$as_codrep."' AND spi_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spi_cuenta,nivel, status, denominacion ";
	  }
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	     $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_spi_reporte_calcular_programado_prox_mes  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		 $lb_valido = false;
	 }
	 else
	 {
	  	if($row=$this->io_sql->fetch_row($rs_data))
	    {
		   if(!(($li_mesdes>=1)&&($li_meshas<=12)))
		   {
		     return false;
		   }
		   for($i=$li_mesdes;$i<=$li_meshas;$i++)
		   {
		     switch ($li_mesdes)
			 {   
			     case 1:
			          $ad_monto_programado=$ad_monto_programado+$row["enero"];
				 break;
			     case 2:
			          $ad_monto_programado=$ad_monto_programado+$row["febrero"];
				 break;
			     case 3:
			          $ad_monto_programado=$ad_monto_programado+$row["marzo"];
				 break;
			     case 4:
			          $ad_monto_programado=$ad_monto_programado+$row["abril"];
				 break;
			     case 5:
			          $ad_monto_programado=$ad_monto_programado+$row["mayo"];
				 break;
			     case 6:
			          $ad_monto_programado=$ad_monto_programado+$row["junio"];
				 break;
			     case 7:
			          $ad_monto_programado=$ad_monto_programado+$row["julio"];
				 break;
			     case 8:
			          $ad_monto_programado=$ad_monto_programado+$row["agosto"];
				 break;
			     case 9:
			          $ad_monto_programado=$ad_monto_programado+$row["septiembre"];
				 break;
			     case 10:
			          $ad_monto_programado=$ad_monto_programado+$row["octubre"];
				 break;
			     case 11:
			          $ad_monto_programado=$ad_monto_programado+$row["noviembre"];
				 break;
			     case 12:
			          $ad_monto_programado=$ad_monto_programado+$row["diciembre"];
				 break;
			 }//switch
		   }//for		   
		}//if
	    $this->io_sql->free_result($rs_data);
     }//else
	$arrResultado['lb_valido']=$lb_valido;
	$arrResultado['ad_monto_programado']=$ad_monto_programado;
	return $arrResultado;		
   }//fin uf_spi_reporte_calcular_programado_prox_mes
/**********************************************************************************************************************************/	
    function uf_spi_reporte_cargar_programado($as_codrep,$as_sc_cuenta,$ad_enero,$ad_febrero,$ai_nivel,$as_status,
											  $ad_marzo,$ad_abril,$ad_mayo,$ad_junio,$ad_julio,$ad_agosto,
											  $ad_septiembre,$ad_octubre,$ad_noviembre,$ad_diciembre)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_cargar_programado
	 //         Access :	private
	 //     Argumentos :    $as_codrep  -->  codigo del reporte
	 //                     $as_sc_cuenta -->  codigo de la  cuenta 
	 //                     $ad_enero .. $ad_diciembre --> monto programado para cada  mes    
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	Reporte por referencia los saldos iniciales programados y ejecutados.   
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    24/08/2006               Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  $lb_valido = false;
	  if($_SESSION["ls_gestor"]=='INFORMIX')
	   {
	    $ls_sql=" SELECT sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spi_plantillacuentareporte ".
              " WHERE  codemp='".$this->ls_codemp."' AND cod_report='".$as_codrep."' AND spi_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spi_cuenta, nivel, status, denominacion ";
		}
		else
		{
		  $ls_sql=" SELECT sum(previsto) as asignado,sum(enero) as enero,sum(febrero) as febrero, sum(marzo) as marzo, ".
              "        sum(abril) as abril, sum(mayo) as mayo,sum(junio) as junio, sum(julio) as julio, ".
       		  "		   sum(agosto) as agosto, sum(septiembre) as septiembre,sum(octubre) as octubre, ".
              "        sum(noviembre) as noviembre,sum(diciembre) as diciembre, nivel, status, denominacion ".
              " FROM   spi_cuentas ".
              " WHERE  codemp='".$this->ls_codemp."' AND spi_cuenta='".$as_sc_cuenta."' ".
              " GROUP BY spi_cuenta,nivel, status, denominacion ";		
		}
	  $rs_data=$this->io_sql->select($ls_sql);
	  if($rs_data===false)
	  {   // error interno sql
		  $this->io_msg->message("CLASE->sigesp_spg_reporte_comparado_0714  MÉTODO->uf_scg_reporte_cargar_programado  ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		  $lb_valido = false;
	  }
	  else
	  {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
			   $ai_nivel=$row["nivel"];
			   $as_status=$row["status"];
                           $ad_asignado=$row["asignado"];
			   $ad_enero=$row["enero"];
			   $ad_febrero=$row["febrero"];
			   $ad_marzo=$row["marzo"];
			   $ad_abril=$row["abril"];
			   $ad_mayo=$row["mayo"];
			   $ad_junio=$row["junio"];
			   $ad_julio=$row["julio"];
			   $ad_agosto=$row["agosto"];
			   $ad_septiembre=$row["septiembre"];
			   $ad_octubre=$row["octubre"];
			   $ad_noviembre=$row["noviembre"];
			   $ad_diciembre=$row["diciembre"];
		       $lb_valido = true;
	    }
		else
		{
			   $ai_nivel="";
			   $as_status="";
			   $ad_asignado=0;
			   $ad_enero=0;
			   $ad_febrero=0;
			   $ad_marzo=0;
			   $ad_abril=0;
			   $ad_mayo=0;
			   $ad_junio=0;
			   $ad_julio=0;
			   $ad_agosto=0;
			   $ad_septiembre=0;
			   $ad_octubre=0;
			   $ad_noviembre=0;
			   $ad_diciembre=0;
		       $lb_valido = true;
		}
		$this->io_sql->free_result($rs_data);
      }//else
	$arrResultado['as_status']=$as_status;
	$arrResultado['ai_nivel']=$ai_nivel;
        $arrResultado['ad_asignado']=$ad_asignado;
	$arrResultado['ad_enero']=$ad_enero;
	$arrResultado['ad_febrero']=$ad_febrero;
	$arrResultado['ad_marzo']=$ad_marzo;
	$arrResultado['ad_abril']=$ad_abril;
	$arrResultado['ad_mayo']=$ad_mayo;
	$arrResultado['ad_junio']=$ad_junio;
	$arrResultado['ad_julio']=$ad_julio;
	$arrResultado['ad_agosto']=$ad_agosto;
	$arrResultado['ad_septiembre']=$ad_septiembre;
	$arrResultado['ad_octubre']=$ad_octubre;
	$arrResultado['ad_noviembre']=$ad_noviembre;
	$arrResultado['ad_diciembre']=$ad_diciembre;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
   }//fin uf_scg_reporte_select_saldo_empresa
/****************************************************************************************************************************************/	

	public function cargarNiveles()
	{
		$formato='1-1-2-0-4-3-02-01-0-';
		$posicion=1;
		$indice=1;
		$posicion = posocurrencia($formato,'-' , $indice ) - $indice;	
		do
		{
			$this->niveles_spg[$indice] = $posicion ;
			$indice = $indice + 1;
			$posicion = posocurrencia($formato,'-' , $indice ) - $indice;
		} while ($posicion>=0);
	}

	public function obtenerNivel($cuenta)
	{
		$nivel=0;
		$anterior=0;
		$longitud=0;
		$cadena='';
		$nivel=count((array)$this->niveles_spg);
		do
		{
			$anterior=$this->niveles_spg[$nivel-1]+1;
			$longitud=$this->niveles_spg[$nivel]-$this->niveles_spg[$nivel-1];
			$cadena=substr(trim($cuenta),$anterior,$longitud); 
			$li=intval($cadena);
		    if($li>0)
			{
				return $nivel;
			}
			$nivel=$nivel-1;
		}while($nivel>1);
		return $nivel;
	}

	public function obtenerCuentaSiguiente($cuenta)
	{
  		$MaxNivel=count((array)$this->niveles_spg);
		$nivel=$this->obtenerNivel($cuenta);
		$anterior=0;
		$longitud=0;
		$cadena='';
		if($nivel>1)
		{
			$anterior=$this->niveles_spg[$nivel - 1]; 
			$cadena=substr($cuenta,0,$anterior+1);
			$longitud=strlen($cadena);
			$long=(($this->niveles_spg[$MaxNivel]+1) - $longitud);
			$cadena=str_pad(trim($cadena),$long+$longitud,'0');
		} 
		return $cadena;
	} 
   
}//fin de clase
?>