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
/****************************************************************************************************************************************/	
class sigesp_spg_class_report
{
    //conexion	
	var $sqlca;   
	//Instancia de la clase funciones.
    var $is_msg_error;
	var $dts_empresa; // datastore empresa
	var $dts_reporte;
	var $obj="";
	var $SQL;
	var $siginc;
	var $con;
	var $fun;	
	var $io_msg;
	var $sigesp_int_spg;
/****************************************************************************************************************************************/	
    public function __construct()
    {
		$this->fun=new class_funciones();
		$this->siginc=new sigesp_include();
		$this->con=$this->siginc->uf_conectar();
		
		$this->SQL=new class_sql($this->con);		
		$this->obj=new class_datastore();
		$this->dts_empresa=$_SESSION["la_empresa"];
		$this->dts_reporte=new class_datastore();
		$this->io_msg=new class_mensajes();
		$this->sigesp_int_spg=new class_sigesp_int_spg();
    }
/****************************************************************************************************************************************/	

	function uf_select_todasest()
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_select_denestpro
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1 ... $as_codestpro5 //rango nivel estructura presupuestaria 
	 //                     $as_codemp  // variabvle que determina el mor nivel de la cuenta
     //	       Returns :	Retorna denominación de la estructura presupuestaria 
	 //	   Description :	Selecciona la denominación de la estructura presupuestarias 
	 //     Creado por :    Ing. Yozelin Barragan
	 // Fecha Creación :    12/04/2006          Fecha última Modificacion : 
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  
	    $lb_valido=true;
		$ls_codemp = $this->dts_empresa["codemp"];
		$lonco1 =  $_SESSION["la_empresa"]["loncodestpro1"];
		$lonco2 =  $_SESSION["la_empresa"]["loncodestpro2"];
		$lonco3 =  $_SESSION["la_empresa"]["loncodestpro3"];
		$pos1=(25-$lonco1)+1;
		$pos2=(25-$lonco2)+1;
		$pos3=(25-$lonco3)+1;
		 $ls_sql  =" select substr(spg_ep1.codestpro1,{$pos1},{$lonco1}) 
					as codestpro1,substr(spg_ep2.codestpro2,{$pos2},{$lonco2}) as 
					codestpro2,substr(spg_ep3.codestpro3,{$pos3},{$lonco3}) 
					as codestpro3,spg_ep1.denestpro1,spg_ep2.denestpro2,
					spg_ep3.denestpro3,spg_ep1.estcla from spg_ep3 
					inner join spg_ep2 
					on spg_ep3.codestpro1=spg_ep2.codestpro1 
					and spg_ep3.codestpro2=spg_ep2.codestpro2 
					and spg_ep3.estcla=spg_ep2.estcla 
					inner join spg_ep1 on
					spg_ep2.codestpro1=spg_ep1.codestpro1 
					and spg_ep2.estcla=spg_ep1.estcla "; 
		 $li_select=$this->SQL->select($ls_sql);                                                                                                                                                                                          
		 if($li_select===false)
		 {
			  $lb_valido=false;
			  $this->io_msg->message("CLASE->class_apertura MÉTODO->uf_select_denestpro ERROR->".$this->io_function->uf_convertirmsg($this->SQL->message));
		 }
		 return   $li_select;
	}
	
	/****************************************************************************************************************************************/	
    function uf_spg_reporte_select_denestpro1($as_codestpro1,&$as_denestpro1)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    27/04/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro1 ".
             " FROM   spg_ep1 ".
             " WHERE  codemp='".$ls_codemp."' AND codestpro1='".$as_codestpro1."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro1 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_denestpro1=$row["denestpro1"];
		}
		$this->SQL->free_result($rs_data);   
	 }//else
	return $lb_valido;
  }//uf_spg_reporte_select_denestpro1



  /****************************************************************************************************************************************/	
    function uf_spg_reporte_select_denestpro2($as_codestpro1,$as_codestpro2,&$as_denestpro2)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro2
	 //         Access :	private
	 //     Argumentos :    $as_codestpro2 // codigo 
	 //                     $as_denestpro2  // denominacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    27/04/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp='".$ls_codemp."' AND  codestpro1='".$as_codestpro1."' AND codestpro2='".$as_codestpro2."' ";
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro2 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_denestpro2=$row["denestpro2"];
		}
		$this->SQL->free_result($rs_data);   
	 }//else
	return $lb_valido;
  }//uf_spg_reporte_select_denestpro1
/****************************************************************************************************************************************/	
    function uf_spg_reporte_select_denestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3,&$as_denestpro3)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro3
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3 // codigo 
	 //                     $as_denestpro3  // denominacion
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    27/04/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro3 ".
             " FROM   spg_ep3 ".
             " WHERE  codemp='".$ls_codemp."' AND  codestpro1='".$as_codestpro1."' AND codestpro2='".$as_codestpro2."' AND ".
			 "        codestpro3='".$as_codestpro3."' "; 
	 $rs_data=$this->SQL->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		$this->is_msg_error="Error en consulta metodo uf_spg_reporte_select_denestpro3 ".$this->fun->uf_convertirmsg($this->SQL->message);
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->SQL->fetch_row($rs_data))
		{
		   $as_denestpro3=$row["denestpro3"];
		}
		$this->SQL->free_result($rs_data);   
	 }//else
	return $lb_valido;
  }//uf_spg_reporte_select_denestpro1
/****************************************************************************************************************************************/	

}//end clase 
?>
