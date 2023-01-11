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

class sigesp_scg_reporte_comparado_0809
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
    }
/**********************************************************************************************************************************/
	////////////////////////////////////////////////////////////////////////////
	//   CLASE REPORTES SCG  COMPARADOS " INVERSIONES    "                   //
	///////////////////////////////////////////////////////////////////////////
    function uf_scg_reportes_comparados_resumen_inversiones_ins08($adt_fecdes,$adt_fechas,$ai_mesdes,$ai_meshas,$ai_cant_mes)
    { //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_scg_reportes_comparados_resumen_inversiones_ins08
	  //        Argumentos :   
	  //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Arnaldo Suárez
	  //    Fecha Creación :    10/08/2010                       Fecha última Modificacion : 10/08/2010     Hora :
  	  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$lb_valido = true;
		$ls_cadena_cuenta = "";
		$ls_cuentas_gasto = "";
		$ls_formpre = $_SESSION["la_empresa"]["formpre"];
		$ls_codemp  = $_SESSION["la_empresa"]["codemp"];
		$ld_periodo = $_SESSION["la_empresa"]["periodo"];
		
		$ldt_fesdes=$this->io_fecha->uf_convert_date_to_db($adt_fecdes);
		$ldt_feshas=$this->io_fecha->uf_convert_date_to_db($adt_fechas);
		
		$la_cuentas[] = "'".$this->sigesp_int_spg->uf_spg_padcuenta_plan($ls_formpre,'401000000')."'";
        $la_cuentas[] = "'".$this->sigesp_int_spg->uf_spg_padcuenta_plan($ls_formpre,'402000000')."'";
        $la_cuentas[] = "'".$this->sigesp_int_spg->uf_spg_padcuenta_plan($ls_formpre,'403000000')."'";
        $la_cuentas[] = "'".$this->sigesp_int_spg->uf_spg_padcuenta_plan($ls_formpre,'407000000')."'";
        $la_cuentas[] = "'".$this->sigesp_int_spg->uf_spg_padcuenta_plan($ls_formpre,'408000000')."'";
		
		$ls_cuentas_gasto = implode(",",$la_cuentas);
		
		switch(strtoupper($_SESSION["ls_gestor"]))
		{
		 case 'POSTGRES': 
		       $ls_cadena_cuenta = "substr(sigesp_plan_unico_re.sig_cuenta,1,3)||'%'";
		       break;
		 case 'MYSQLT':
		 	   $ls_cadena_cuenta = "CONCAT(substr(sigesp_plan_unico_re.sig_cuenta,1,3),'%')";
		       break;
		}
		
		$ls_sql = "SELECT sc_cuenta as cuenta, denominacion, asignado, enero, febrero, marzo, abril, mayo, junio, julio, ".
                  "       agosto, septiembre, octubre, noviembre, diciembre, nivel, ".
				  "       (SELECT COALESCE((COALESCE(SUM(debe_mes),0)-COALESCE(SUM(haber_mes),0)),0) ".
				  "          FROM scg_saldos ".
				  "        WHERE scg_saldos.sc_cuenta = scg_pc_reporte.sc_cuenta ".
				  "              AND fecsal BETWEEN '".$ldt_fesdes."' AND '".$ldt_feshas."') as ejecutado, ".
				  "       (SELECT COALESCE((COALESCE(SUM(debe_mes),0)-COALESCE(SUM(haber_mes),0)),0) ".
				  "          FROM scg_saldos ".
				  "        WHERE scg_saldos.sc_cuenta = scg_pc_reporte.sc_cuenta ".
				  "              AND fecsal BETWEEN '".$ld_periodo."' AND '".$ldt_feshas."') as ejecutado_acumulado, ".
				  "        0.00 as aumentodisminucion,no_fila, saldo_mod, 1 as tipo ".
                  "  FROM scg_pc_reporte ".
                  "WHERE cod_report = '0801' ".
                  "UNION ".
                  "SELECT sigesp_plan_unico_re.sig_cuenta as cuenta, ".
				  "       MAX(sigesp_plan_unico_re.denominacion) AS denominacion, ".
				  "       COALESCE(SUM(spg_cuentas.asignado),0) AS asignado,  ".
				  "       COALESCE(SUM(spg_cuentas.enero),0) AS enero,  ". 
				  "       COALESCE(SUM(spg_cuentas.febrero),0) AS febrero,  ". 
				  "       COALESCE(SUM(spg_cuentas.marzo),0) AS marzo,  ". 
				  "       COALESCE(SUM(spg_cuentas.abril),0) AS  abril,  ".
				  "       COALESCE(SUM(spg_cuentas.mayo),0) AS mayo,  ". 
				  "       COALESCE(SUM(spg_cuentas.junio),0) AS junio,  ". 
				  "       COALESCE(SUM(spg_cuentas.julio),0) AS julio, ". 
				  "       COALESCE(SUM(spg_cuentas.agosto),0) AS agosto,  ". 
				  "       COALESCE(SUM(spg_cuentas.septiembre),0) AS septiembre, ". 
				  "       COALESCE(SUM(spg_cuentas.octubre),0) AS octubre,   ".
				  "       COALESCE(SUM(spg_cuentas.noviembre),0) AS noviembre, ". 
				  "       COALESCE(SUM(spg_cuentas.diciembre),0) AS diciembre, ".
				  "       COALESCE(MAX(spg_cuentas.nivel),1) AS nivel, ".
                  "       (SELECT COALESCE(SUM(DT.monto),0.00) ".
				  "		     FROM   spg_dt_cmp DT, spg_operaciones OP ".
				  "		   WHERE  DT.codemp='".$ls_codemp."' ".
				  "               AND DT.operacion = OP.operacion ".
				  "               AND (OP.comprometer = 1 OR OP.precomprometer = 1) ".
				  "               AND DT.spg_cuenta LIKE ".$ls_cadena_cuenta." ".
				  "               AND DT.fecha BETWEEN '".$ldt_fesdes."' AND  '".$ldt_feshas."') AS ejecutado, ".
                  "       (SELECT COALESCE(SUM(DT.monto),0.00) ".
		          "		     FROM   spg_dt_cmp DT, spg_operaciones OP ".
				  "		   WHERE  DT.codemp='".$ls_codemp."' ".
				  "               AND DT.operacion = OP.operacion ".
				  "               AND (OP.comprometer = 1 OR OP.precomprometer = 1) ".
				  "               AND  DT.spg_cuenta LIKE ".$ls_cadena_cuenta." ".
				  "               AND DT.fecha BETWEEN '".$ld_periodo."' AND  '".$ldt_feshas."') AS ejecutado_acumulado, ".
                  "       ((SELECT COALESCE(SUM(DT.monto),0.00) ". 
				  "		      FROM   spg_dt_cmp DT, spg_operaciones OP ".
				  "		  	WHERE  DT.codemp='".$ls_codemp."' ".
				  "                AND DT.operacion = OP.operacion ".
				  "                AND (OP.aumento = 1) ".
				  "                AND DT.spg_cuenta LIKE ".$ls_cadena_cuenta." ".
				  "                AND DT.fecha BETWEEN '".$ld_periodo."' AND  '".$ldt_feshas."') ".
	              "      - (SELECT COALESCE(SUM(DT.monto),0.00) ". 
				  "		      FROM   spg_dt_cmp DT, spg_operaciones OP ".
				  "		  	WHERE  DT.codemp='".$ls_codemp."' ".
				  "                AND DT.operacion = OP.operacion ".
				  "                AND (OP.disminucion = 1) ".
				  "                AND  DT.spg_cuenta LIKE ".$ls_cadena_cuenta." ".
				  "                AND DT.fecha BETWEEN '".$ld_periodo."' AND  '".$ldt_feshas."')) AS aumentodisminucion, ".
	              "      0 AS no_fila, 0 AS saldo_mod, 2 AS tipo  ".
                  "FROM sigesp_plan_unico_re ".
                  "LEFT OUTER JOIN spg_cuentas ON spg_cuentas.spg_cuenta = sigesp_plan_unico_re.sig_cuenta ".
                  "WHERE sigesp_plan_unico_re.sig_cuenta IN (".$ls_cuentas_gasto.") ".
                  "GROUP BY sigesp_plan_unico_re.sig_cuenta, no_fila, tipo ".
                  "ORDER BY tipo, no_fila";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data === false)
		{
		 $lb_valido = false;
		
		}
		else
		{
		 if($rs_data->EOF)
		 {
		  $lb_valido = false;
		 }
		 else
		 {
		  while(!$rs_data->EOF)
		  {
		   $ls_cuenta = $rs_data->fields["cuenta"];  
		   $ls_denominacion=$rs_data->fields["denominacion"];
		   $ls_tipo=$rs_data->fields["tipo"];   
		   $li_nivel=$rs_data->fields["nivel"];
		   $ld_monto_programado=0;
		   $ld_monto_programado_acumulado=0;
		   $ai_mesdes." - ".$ai_meshas;
	       for($i=1;$i<=$ai_meshas;$i++)
		   {
		    if($i>=$ai_mesdes)
			{
			 switch($i)
			 {
			  case 1  :
			           $ld_monto_programado += $rs_data->fields["enero"];
			           break;
			  case 2  :
			           $ld_monto_programado += $rs_data->fields["febrero"];
			           break;
			  case 3  :
			           $ld_monto_programado += $rs_data->fields["marzo"];
			           break;
			  case 4  :
			           $ld_monto_programado += $rs_data->fields["abril"];
			           break;
			  case 5  :
			           $ld_monto_programado += $rs_data->fields["mayo"];
			           break;
			  case 6  :
			           $ld_monto_programado += $rs_data->fields["junio"];
			           break;
			  case 7  :
			           $ld_monto_programado += $rs_data->fields["julio"];
			           break;
			  case 8  :
			           $ld_monto_programado += $rs_data->fields["agosto"];
			           break;
			  case 9  :
			           $ld_monto_programado += $rs_data->fields["septiembre"];
			           break;
			  case 10 :
			           $ld_monto_programado += $rs_data->fields["octubre"];
			           break;
			  case 11 :
			           $ld_monto_programado += $rs_data->fields["noviembre"];
			           break;
			  case 12 :
			           $ld_monto_programado += $rs_data->fields["diciembre"];
			           break;
			 }
			}
			
			if($i<=$ai_meshas)
		    {
			 switch($i)
			 {
			  case 1  :
			           $ld_monto_programado_acumulado += $rs_data->fields["enero"];
			           break;
			  case 2  :
			           $ld_monto_programado_acumulado += $rs_data->fields["febrero"];
			           break;
			  case 3  :
			           $ld_monto_programado_acumulado += $rs_data->fields["marzo"];
			           break;
			  case 4  :
			           $ld_monto_programado_acumulado += $rs_data->fields["abril"];
			           break;
			  case 5  :
			           $ld_monto_programado_acumulado += $rs_data->fields["mayo"];
			           break;
			  case 6  :
			           $ld_monto_programado_acumulado += $rs_data->fields["junio"];
			           break;
			  case 7  :
			           $ld_monto_programado_acumulado += $rs_data->fields["julio"];
			           break;
			  case 8  :
			           $ld_monto_programado_acumulado += $rs_data->fields["agosto"];
			           break;
			  case 9  :
			           $ld_monto_programado_acumulado += $rs_data->fields["septiembre"];
			           break;
			  case 10 :
			           $ld_monto_programado_acumulado += $rs_data->fields["octubre"];
			           break;
			  case 11 :
			           $ld_monto_programado_acumulado += $rs_data->fields["noviembre"];
			           break;
			  case 12 :
			           $ld_monto_programado_acumulado += $rs_data->fields["diciembre"];
			           break;
			 }
			}
		   }
		   
		   $ld_monto_ejecutado=$rs_data->fields["ejecutado"];
		   $ld_monto_ejecutado_acumulado=$rs_data->fields["ejecutado_acumulado"];
		   $ld_variacion_absoluta=abs($ld_monto_programado - $ld_monto_ejecutado);
		   $ld_porcentaje_variacion=0;
		   if($ld_monto_programado>0)
		   {
		    $ld_porcentaje_variacion=($ld_monto_ejecutado/$ld_monto_programado)*100;
		   }
		   
           $ld_presupuesto_aprobado=$rs_data->fields["asignado"];
           $ld_presupuesto_modificado= $rs_data->fields["asignado"] + $rs_data->fields["aumentodisminucion"];
		   $this->dts_reporte->insertRow("cuenta",$ls_cuenta);
		   $this->dts_reporte->insertRow("denominacion",$ls_denominacion);
		   $this->dts_reporte->insertRow("tipo",$ls_tipo);
		   $this->dts_reporte->insertRow("nivel",$li_nivel);
		   $this->dts_reporte->insertRow("monto_programado",$ld_monto_programado);
		   $this->dts_reporte->insertRow("programado_acumulado",$ld_monto_programado_acumulado);
		   $this->dts_reporte->insertRow("monto_ejecutado",$ld_monto_ejecutado);
		   $this->dts_reporte->insertRow("ejecutado_acumulado",$ld_monto_ejecutado_acumulado);
		   $this->dts_reporte->insertRow("variacion_absoluta",$ld_variacion_absoluta);
		   $this->dts_reporte->insertRow("porcentaje_variacion",$ld_porcentaje_variacion);
           $this->dts_reporte->insertRow("presupuesto_aprobado",$ld_presupuesto_aprobado);
           $this->dts_reporte->insertRow("presupuesto_modificado",$ld_presupuesto_modificado);
		   $rs_data->MoveNext();
		  }
		 }
		}		  
	    return $lb_valido;
    }//fin uf_scg_reportes_comparados_resumen_inversiones_ins08
	
/********************************************************************************************************************************/
	function uf_nombre_mes_desde_hasta($ai_mesdes,$ai_meshas)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	Function: 	  uf_load_nombre_mes
		//	Description:  Funcion que se encarga de obtener el numero de un mes a partir de su nombre.
		//	Arguments:	  - $ls_mes: Mes de la fecha a obtener el ultimo dia.	
		//				  - $ls_ano: Año de la fecha a obtener el ultimo dia.
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_nombre_mesdes=$this->io_fecha->uf_load_nombre_mes($ai_mesdes);
		$ls_nombre_meshas=$this->io_fecha->uf_load_nombre_mes($ai_meshas);
		$ls_nombremes=$ls_nombre_mesdes."-".$ls_nombre_meshas;
		return $ls_nombremes;
	 }//uf_nombre_mes_desde_hasta
/********************************************************************************************************************************/	

}//fin de clase
?>