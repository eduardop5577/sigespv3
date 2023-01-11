<?PHP 
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


/*********************************************************************************************************************************/	
class sigesp_spg_funciones_reportes
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
	var $sigesp_int_spg;
	
	/**********************************************************************************************************************************/	
    public function __construct()
    {
		$this->io_function=new class_funciones() ;
		$this->io_include=new sigesp_include();
		$this->io_connect=$this->io_include->uf_conectar();
	
		$this->io_sql=new class_sql($this->io_connect);		
		$this->obj=new class_datastore();
		$this->dts_reporte=new class_datastore();
		$this->io_fecha = new class_fecha();
		$this->io_msg=new class_mensajes();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
    }
/********************************************************************************************************************************/
    function uf_spg_reporte_select_denestpro1($as_codestpro1,$as_denestpro1,$as_estcla)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_denestpro1
	 //         Access :	private
	 //     Argumentos :    $as_procede_ori  // procede origen
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la descripcion de la estructura programatica 1
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    27/04/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro1 ".
             " FROM   spg_ep1 ".
             " WHERE  codemp='".$this->ls_codemp."' AND codestpro1='".$as_codestpro1."' AND estcla='".$as_estcla."' ";	 
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
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    27/04/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp='".$this->ls_codemp."' AND  ".
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
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    27/04/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro3 ".
             " FROM   spg_ep3 ".
             " WHERE  codemp='".$this->ls_codemp."' AND  ".
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
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    31/10/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro4 ".
             " FROM   spg_ep4 ".
             " WHERE  codemp='".$this->ls_codemp."' AND  ".
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
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    31/10/2006         Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT denestpro5 ".
             " FROM   spg_ep5 ".
             " WHERE  codemp='".$this->ls_codemp."' AND  ".
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
		$arrResultado['as_denestpro5']=$as_denestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_denestpro5
/********************************************************************************************************************************/	
    function uf_spg_reporte_select_min_programatica($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_programatica
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1..as_codestpro5  // estructura presupuestaria (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 
	 if(($as_codestpro1=='') || ($as_codestpro1=='0000000000000000000000000'))
	 { 
		$arrResultado=$this->uf_spg_reporte_select_min_codestpro1($as_codestpro1,$as_estclades);
		$as_codestpro1=$arrResultado['as_codestpro1'];
		$as_estclades=$arrResultado['as_estclades'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro2=='') || ($as_codestpro2=='0000000000000000000000000'))
	 {
		$arrResultado=$this->uf_spg_reporte_select_min_codestpro2($as_codestpro1,$as_codestpro2,$as_estclades);
		$as_codestpro2=$arrResultado['as_codestpro2'];
		$lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro3=='') || ($as_codestpro3=='0000000000000000000000000'))
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_estclades);
		 $as_codestpro3=$arrResultado['as_codestpro3'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	  if(($as_codestpro4=='') || ($as_codestpro4=='0000000000000000000000000'))
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_estclades);
		 $as_codestpro4=$arrResultado['as_codestpro4'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro5=='') || ($as_codestpro5=='0000000000000000000000000'))
	 {
		 $arrResultado=$this->uf_spg_reporte_select_min_codestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estclades);
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
  }//uf_spg_reporte_select_min_programatica
/**********************************************************************************************************************************/
 function uf_spg_reporte_select_min_codestpro1($as_codestpro1,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_codestpro1
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
	 {
	    if ($_SESSION["ls_gestor"]=='oci8po')
		{
	    	$ls_sql="SELECT * FROM
	  				(SELECT * FROM spg_ep1 WHERE codemp='".$this->ls_codemp."' AND codestpro1<>'-------------------------' AND codestpro1<>'0000000000000000000000000' ORDER BY codestpro1,estcla)
	  			 	WHERE rownum = 1";	
	    }
	    else
		{
	    	$ls_sql="SELECT * FROM spg_ep1 WHERE codemp='".$this->ls_codemp."' AND codestpro1<>'-------------------------' AND codestpro1<>'0000000000000000000000000' ORDER BY codestpro1,estcla  asc  limit 1";
	    }
	 	
	 }
	 else
	 {
	  $ls_sql="SELECT first 1 * FROM spg_ep1 WHERE codemp='".$this->ls_codemp."' AND codestpro1<>'-------------------------' ORDER BY codestpro1,estcla  asc";
	 }
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		   $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
		                           MÉTODO->uf_spg_reporte_select_min_codestpro1  
								   ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		   $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro1=$row["codestpro1"];
           $as_estclades=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_estclades']=$as_estclades;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro1
/**********************************************************************************************************************************/
	function uf_spg_reporte_select_min_codestpro2($as_codestpro1,$as_codestpro2,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro2
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 1 (referencia)          
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro2) as codestpro2 ".
             " FROM   spg_ep2 ".
             " WHERE  codemp = '".$this->ls_codemp."' AND codestpro1='".$as_codestpro1."' AND estcla='".$as_estclades."' ".
			 " AND codestpro1<>'-------------------------' AND codestpro2<>'-------------------------'";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		   $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
		                           MÉTODO->uf_spg_reporte_select_min_codestpro2  
								   ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		   $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro2=$row["codestpro2"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro2
/**********************************************************************************************************************************/
 function uf_spg_reporte_select_min_codestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro3
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3 (referencia)
	 //                     $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2           
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro3) as codestpro3 ".
             " FROM   spg_ep3                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        codestpro2='".$as_codestpro2."' AND   ".
			 "        estcla='".$as_estclades."'  		    ".
			 " AND codestpro1<>'-------------------------'  ".
			 " AND codestpro2<>'-------------------------'  ".
			 " AND codestpro3<>'-------------------------'  ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_min_codestpro3  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro3=$row["codestpro3"];
		   //$as_estclades=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro3
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_min_codestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro4
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  (referencia)         
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro4) as codestpro4 ".
             " FROM   spg_ep4                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        codestpro2='".$as_codestpro2."' AND   ".
			 "        codestpro3='".$as_codestpro3."' AND   ".
			 "        estcla='".$as_estclades."'  ".
			 " AND codestpro1<>'-------------------------'  ".
			 " AND codestpro2<>'-------------------------'  ".
			 " AND codestpro3<>'-------------------------'  ".
			 " AND codestpro4<>'-------------------------'  ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_min_codestpro4  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro4=$row["codestpro4"];
		   //$as_estclades=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro4
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_min_codestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
	                                              $as_codestpro5,$as_estclades)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro5
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  
	 //                     $as_codestpro4  // codigo de estructura programatica 5  (referencia) 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT min(codestpro5) as codestpro5 ".
             " FROM   spg_ep5                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        codestpro2='".$as_codestpro2."' AND   ".
			 "        codestpro3='".$as_codestpro3."' AND   ".
			 "        codestpro4='".$as_codestpro4."' AND   ".
			 "        estcla='".$as_estclades."'  ".
			 " AND codestpro1<>'-------------------------'  ".
			 " AND codestpro2<>'-------------------------'  ".
			 " AND codestpro3<>'-------------------------'  ".
			 " AND codestpro4<>'-------------------------'  ".
			 " AND codestpro5<>'-------------------------'  ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_min_codestpro5  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro5=$row["codestpro5"];
		   //$as_estclades=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_min_codestpro3
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_programatica($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_programatica
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1..as_codestpro5  // codigo de la estructura (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	if(($as_codestpro1=='') || ($as_codestpro1=='0000000000000000000000000'))
	 { 
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro1($as_codestpro1,$as_estclahas);
		 $as_codestpro1=$arrResultado['as_codestpro1'];
		 $as_estclahas=$arrResultado['as_estclahas'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro2=='') || ($as_codestpro2=='0000000000000000000000000'))
	 { 
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro2($as_codestpro1,$as_codestpro2,$as_estclahas);
		 $as_codestpro2=$arrResultado['as_codestpro2'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro3=='') || ($as_codestpro3=='0000000000000000000000000'))
	 { 
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_estclahas);
		 $as_codestpro3=$arrResultado['as_codestpro3'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro4=='') || ($as_codestpro4=='0000000000000000000000000'))
	 { 
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_estclahas);
		 $as_codestpro4=$arrResultado['as_codestpro4'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
	 if(($as_codestpro5=='') || ($as_codestpro5=='0000000000000000000000000'))
	 { 
		 $arrResultado=$this->uf_spg_reporte_select_max_codestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estclahas);
		 $as_codestpro5=$arrResultado['as_codestpro5'];
		 $lb_valido=$arrResultado['lb_valido'];
	 }
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estclahas']=$as_estclahas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_programatica
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro1($as_codestpro1,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro1
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 (referencia)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 if (trim(strtoupper($_SESSION["ls_gestor"])) <> "INFORMIX")
	 {
	 	if ($_SESSION["ls_gestor"]=='oci8po') {
	    	$ls_sql="SELECT * FROM
	  				(SELECT * FROM spg_ep1 WHERE codemp='".$this->ls_codemp."' ORDER BY codestpro1  desc)
	  			 	WHERE rownum = 1";	
	    }
	    else {
	    	$ls_sql="SELECT * FROM spg_ep1 WHERE codemp='".$this->ls_codemp."' ORDER BY codestpro1  desc  limit 1 ";
	    }
	  
	 }
	 else
	 {
	  $ls_sql="SELECT first 1 * FROM spg_ep1 WHERE codemp='".$this->ls_codemp."' ORDER BY codestpro1  desc";
	 } 
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		   $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
		                           MÉTODO->uf_spg_reporte_select_max_codestpro1  
								   ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		   $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro1=$row["codestpro1"];
		   $as_estclahas=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_estclahas']=$as_estclahas;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro1
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro2($as_codestpro1,$as_codestpro2,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro2
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 1 (referencia)          
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1  maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro2) as codestpro2         ".
             " FROM   spg_ep2                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        estcla='".$as_estclahas."'  ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
		   $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
		                           MÉTODO->uf_spg_reporte_select_max_codestpro2  
								   ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		   $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro2=$row["codestpro2"];
		   //$as_estclahas=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro2
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro3($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro3
	 //         Access :	private
	 //     Argumentos :    $as_codestpro3  // codigo de estructura programatica 3 (referencia)
	 //                     $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2           
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $ls_codemp = $this->dts_empresa["codemp"];
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro3) as codestpro3 ".
             " FROM   spg_ep3                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        codestpro2='".$as_codestpro2."' AND   ".
			 "        estcla='".$as_estclahas."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_max_codestpro3  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro3=$row["codestpro3"];
		   //$as_estclahas=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro3
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro4($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro4
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  (referencia)         
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro4) as codestpro4 ".
             " FROM   spg_ep4                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        codestpro2='".$as_codestpro2."' AND   ".
			 "        codestpro3='".$as_codestpro3."' AND   ".
			 "        estcla='".$as_estclahas."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_max_codestpro4  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro4=$row["codestpro4"];
		   //$as_estclahas=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro4
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_codestpro5($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estclahas)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_codestpro5
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  
	 //                     $as_codestpro4  // codigo de estructura programatica 5  (referencia) 
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT max(codestpro5) as codestpro5 ".
             " FROM   spg_ep5                               ".
             " WHERE  codemp = '".$this->ls_codemp."' AND   ".
			 "        codestpro1='".$as_codestpro1."' AND   ".
			 "        codestpro2='".$as_codestpro2."' AND   ".
			 "        codestpro3='".$as_codestpro3."' AND   ".
			 "        codestpro4='".$as_codestpro4."' AND   ".
			 "        estcla='".$as_estclahas."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_max_codestpro5  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_codestpro5=$row["codestpro5"];
		   //$as_estclahas=$row["estcla"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro3
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_min_coduniadm($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_coduniadm)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_coduniadm
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  
	 //                     $as_codestpro5  // codigo de estructura programatica 5  (referencia)
	 //                     $coduniadm     //  codigo de la unidad administrativas
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_gestor = $_SESSION["ls_gestor"];
	 $ls_estructura=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5;
	 if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	 {
		   if($ls_estructura!="")
		   {
		      $ls_cadena=" AND CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5)=";
		   }
		   else
		   {
		      $ls_cadena="";
		   }
	 }
	 else
	 {
		   if($ls_estructura!="")
		   {
		       $ls_cadena=" AND codestpro1||codestpro2||codestpro3||codestpro4||codestpro5=";
		   }
		   else
		   {
		       $ls_cadena="";
		   }
	 }
	 $ls_sql=" SELECT min(coduniadm) as coduniadm ".
			 " FROM   spg_unidadadministrativa ".
             " WHERE  codemp='".$this->ls_codemp."'  ".
			 "        ".$ls_cadena." '".$ls_estructura."' ".
             " ORDER BY coduniadm ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_min_coduniadm  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_coduniadm=$row["coduniadm"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_coduniadm']=$as_coduniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_codestpro3
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_coduniadm($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_coduniadm)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_coduniadm
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  
	 //                     $as_codestpro5  // codigo de estructura programatica 5  (referencia)
	 //                     $coduniadm     //  codigo de la unidad administrativas
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_gestor = $_SESSION["ls_gestor"];
	 $ls_estructura=$as_codestpro1.$as_codestpro2.$as_codestpro3.$as_codestpro4.$as_codestpro5;
	 if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
	 {
		   if($ls_estructura!="")
		   {
		      $ls_cadena=" AND CONCAT(codestpro1,codestpro2,codestpro3,codestpro4,codestpro5)=";
		   }
		   else
		   {
		      $ls_cadena="";
		   }
	 }
	 else
	 {
		   if($ls_estructura!="")
		   {
		       $ls_cadena=" AND codestpro1||codestpro2||codestpro3||codestpro4||codestpro5=";
		   }
		   else
		   {
		       $ls_cadena="";
		   }
	 }
	 $ls_sql=" SELECT max(coduniadm) as coduniadm ".
			 " FROM   spg_unidadadministrativa ".
             " WHERE  codemp='".$this->ls_codemp."'  ".
			 "        ".$ls_cadena." '".$ls_estructura."' ".
             " ORDER BY coduniadm ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_max_coduniadm  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_coduniadm=$row["coduniadm"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_coduniadm']=$as_coduniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_reporte_select_max_coduniadm
/********************************************************************************************************************************/
    function uf_spg_min_coduniadm_sinprogramatica($as_coduniadm)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_coduniadm
	 //         Access :	private
	 //     Argumentos :    $as_codestpro1  // codigo de estructura programatica 1 
	 //                     $as_codestpro2  // codigo de estructura programatica 2 
	 //                     $as_codestpro3  // codigo de estructura programatica 3 
	 //                     $as_codestpro4  // codigo de estructura programatica 4  
	 //                     $as_codestpro5  // codigo de estructura programatica 5  (referencia)
	 //                     $coduniadm     //  codigo de la unidad administrativas
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_gestor = $_SESSION["ls_gestor"];	
	 $ls_sql=" SELECT min(coduniadm) as coduniadm ".
			 " FROM   spg_unidadadministrativa ".
             " WHERE  codemp='".$this->ls_codemp."'  ".
             " ORDER BY coduniadm ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_min_coduniadm_sinprogramatica  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_coduniadm=$row["coduniadm"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_coduniadm']=$as_coduniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_min_coduniadm_sinprogramatica
/**********************************************************************************************************************************/
    function uf_spg_max_coduniadm_sinprogramatica($as_coduniadm)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_coduniadm
	 //         Access :	private
	 //     Argumentos :    $coduniadm     //  codigo de la unidad administrativas
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve el codigo de estructura programatica 1 maxima de la tabla spg_unidadadministrativa
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :08/09/2006      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_gestor = $_SESSION["ls_gestor"];	 
	 $ls_sql=" SELECT max(coduniadm) as coduniadm ".
			 " FROM   spg_unidadadministrativa ".
			 " WHERE  codemp='".$this->ls_codemp."'  ".
             " ORDER BY coduniadm ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_max_coduniadm _sinprogramatica
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	      $lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_coduniadm=$row["coduniadm"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
		$arrResultado['as_coduniadm']=$as_coduniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
  }//uf_spg_max_coduniadm_sinprogramatica
/********************************************************************************************************************************/
	function uf_spg_reporte_select_min_cuenta($as_spg_cuenta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_min_cuenta
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta minima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT min(spg_cuenta) as spg_cuenta ".
             " FROM spg_cuentas ".
             " WHERE codemp = '".$this->ls_codemp."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_min_cuenta  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_spg_cuenta=$row["spg_cuenta"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	$arrResultado['as_spg_cuenta']=$as_spg_cuenta;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
  }//uf_spg_reporte_select_min_cuenta
/**********************************************************************************************************************************/
    function uf_spg_reporte_select_max_cuenta($as_spg_cuenta)
    {//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_spg_reporte_select_max_cuenta
	 //         Access :	private
	 //     Argumentos :    $as_spg_cuenta  // cuenta maxima (referencias)
     //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	 //	   Description :	devuelve la cuenta minima de la tabla spg_cuentas
	 //     Creado por :    Ing. Yozelin Barragán.
	 // Fecha Creación :    19/07/2006          Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 $lb_valido=true;
	 $ls_sql=" SELECT max(spg_cuenta) as spg_cuenta ".
             " FROM spg_cuentas ".
             " WHERE codemp = '".$this->ls_codemp."' ";
	 $rs_data=$this->io_sql->select($ls_sql);
	 if($rs_data===false)
	 {   // error interno sql
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reporte_select_max_cuenta  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		$lb_valido = false;
	 }
	 else
	 {
		if($row=$this->io_sql->fetch_row($rs_data))
		{
		   $as_spg_cuenta=$row["spg_cuenta"];
		}
		else
		{
		   $lb_valido = false;
		}
		$this->io_sql->free_result($rs_data);
	 }//else
	$arrResultado['as_spg_cuenta']=$as_spg_cuenta;
	$arrResultado['lb_valido']=$lb_valido;
	return $arrResultado;		
  }//uf_spg_reporte_select_max_cuenta
/*********************************************************************************************************************************/
    function uf_spg_reportes_select_denominacion($as_spg_cuenta,$as_denominacion)
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_spg_reportes_select_denominacion
	  //        Argumentos :    
      //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Yozelin Barragán.
	  //    Fecha Creación :    28/08/2006                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT denominacion FROM spg_cuentas WHERE codemp='".$this->ls_codemp."' AND spg_cuenta='".$as_spg_cuenta."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_reportes_select_denominacion  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		   if($row=$this->io_sql->fetch_row($rs_data))
		   {
			  $as_denominacion=$row["denominacion"];
		   }
		   $this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_denominacion']=$as_denominacion;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   }//fin uf_spg_reportes_llenar_datastore_cuentas()
/****************************************************************************************************************************************/	
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
 }
/****************************************************************************************************************************************/	
   function uf_load_seguridad_reporte($as_sistema,$as_ventanas,$as_descripcion)
   {
   
		//////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_load_seguridad_reporte
		//		   Access: public (en todas las clases que usen seguridad)
		//	    Arguments: as_sistema // Sistema del que se desea verificar la seguridad
		//				   as_ventanas // Ventana del que se desea verificar la seguridad
		//				   aa_seguridad  // arreglo de las variables de seguridad
		//	  Description: Función que obtiene el valor de una variable que viene de un submit
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 27/04/2006 					Fecha Última Modificación : 
		////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../../../shared/class_folder/sigesp_c_seguridad.php");
		$io_seguridad= new sigesp_c_seguridad();
		$ls_empresa=$_SESSION["la_empresa"]["codemp"];
		$ls_logusr=$_SESSION["la_logusr"];
		$la_seguridad["empresa"]=$ls_empresa;
		$la_seguridad["logusr"]=$ls_logusr;
		$la_seguridad["sistema"]=$as_sistema;
		$la_seguridad["ventanas"]=$as_ventanas;
		$ls_evento="REPORT";
			
		$lb_valido= $io_seguridad->uf_sss_insert_eventos_ventana($la_seguridad["empresa"],
								$la_seguridad["sistema"],$ls_evento,$la_seguridad["logusr"],
								$la_seguridad["ventanas"],$as_descripcion);
		
		unset($io_seguridad);
		return $lb_valido;
   }// end function uf_load_seguridad
/*********************************************************************************************************************************/
    function uf_spg_select_provee_benef($as_cod_pro,$as_ced_bene,$as_nompro,$as_nombene)
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :	uf_spg_select_provee_benef
	  //        Argumentos :    
      //	       Returns :	Retorna true o false si se realizo la consulta para el reporte
	  //	   Description :	Reporte que genera salida  del Ejecucion Financiera Formato 3
	  //        Creado por :    Ing. Yozelin Barragán.
	  //    Fecha Creación :    17/10/2006                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_gestor = $_SESSION["ls_gestor"];
		if((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		{
			$ls_cadena="CONCAT( rtrim(XBF.apebene),', ',XBF.nombene)";
		}
		else
		{
			$ls_cadena="rtrim(XBF.apebene)||', '||XBF.nombene";
		}
		$ls_sql = " SELECT PRV.nompro as nompro,  ".$ls_cadena."  as nombene ".
                  " FROM   sigesp_cmp CMP, rpc_beneficiario BEN, rpc_proveedor PRV ".
                  " WHERE  CMP.cod_pro=PRV.cod_pro AND CMP.ced_bene=BEN.ced_bene AND CMP.cod_pro='".$as_cod_pro."' AND ".
                  "        CMP.ced_bene='".$as_nompro."' ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_select_provee_benef  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		   if($row=$this->io_sql->fetch_row($rs_data))
		   {
			  $as_nompro=$row["nompro"];
			  $as_nombene=$row["nombene"];
		   }
		   $this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_nompro']=$as_nompro;
		$arrResultado['as_nombene']=$as_nombene;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   }//fin uf_spg_reportes_llenar_datastore_cuentas()
/*********************************************************************************************************************************/
    function uf_spg_select_fuentefinanciamiento($as_minfuefin,$as_maxfuefin)
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function : uf_spg_select_fuentefinanciamiento
	  //        Argumentos :  
      //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	  //	   Description : Envia por referencia el minimo y el maximo de las fuente financiamineto
	  //        Creado por : Ing. Yozelin Barragán.
	  //    Fecha Creación : 31/10/2007                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_gestor = $_SESSION["ls_gestor"];
		$ls_sql = " SELECT min(codfuefin) as minfuefin,      ".
		          "        max(codfuefin) as maxfuefin       ". 
                  " FROM   sigesp_fuentefinanciamiento       ".
                  " WHERE  codemp='".$this->ls_codemp."'     ";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$lb_valido=false;
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_select_fuentefinanciamiento  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		   if($row=$this->io_sql->fetch_row($rs_data))
		   {
			  $as_minfuefin=$row["minfuefin"];
			  $as_maxfuefin=$row["maxfuefin"];
		   }
		   $this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_minfuefin']=$as_minfuefin;
		$arrResultado['as_maxfuefin']=$as_maxfuefin;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   }//fin uf_spg_select_fuentefinanciamiento()
/****************************************************************************************************************************************/	

function uf_filtro_seguridad_programatica($as_tabla,$as_filtro)
	{
	 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	     Function: uf_filtro_seguridad_programatica
	 //		   Access: public
	 //	    Arguments: $as_codemp      -- Codigo de la Empresa
	 //                $as_tabla       -- Nombre de la Tabla utilizada en la consulta para buscar las programaticas
	 //	      Returns: $as_filtro -- String que contiene la sentencia que filtra las programaticas de las consultas.
	 //	  Descripcion: Función que retorna el filtro de programaticas dado los permisos del usuario logueado 
	 //	   Creado Por: Ing. Arnaldo Suárez
	 // Fecha Creación: 22/02/2008 								Fecha Última Modificación : 
	 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $ls_gestor    = $_SESSION["ls_gestor"];
		$li_estmodest = $_SESSION["la_empresa"]["estmodest"];
		$ls_usuario   = $_SESSION["la_logusr"];
		$ls_codemp    = $_SESSION["la_empresa"]["codemp"];
		 if ((strtoupper($ls_gestor)=="MYSQLT") || (strtoupper($ls_gestor)=="MYSQLI"))
		 {
		  if ($li_estmodest == 2)
		  {
		   $as_filtro = " AND CONCAT('".$ls_codemp."','SPG','".$ls_usuario."',".$as_tabla.".codestpro1,".$as_tabla.".codestpro2,".$as_tabla.".codestpro3,".$as_tabla.".codestpro4,".$as_tabla.".codestpro5,".$as_tabla.".estcla) IN (SELECT distinct CONCAT(codemp,codsis,codusu,codintper) 
		                       FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
		  }
		  else
		  {
		   $as_filtro = " AND CONCAT('".$ls_codemp."','SPG','".$ls_usuario."',".$as_tabla.".codestpro1,".$as_tabla.".codestpro2,".$as_tabla.".codestpro3,'00000000000000000000000000000000000000000000000000',".$as_tabla.".estcla) IN (SELECT distinct CONCAT(codemp,codsis,codusu,codintper) 
		                       FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
		  }				   
		 }
		 else
		 {
		  if ($li_estmodest == 2)
		  {
		   $as_filtro = " AND '".$ls_codemp."'||'SPG'||'".$ls_usuario."'||".$as_tabla.".codestpro1||".$as_tabla.".codestpro2||".$as_tabla.".codestpro3||".$as_tabla.".codestpro4||".$as_tabla.".codestpro5||".$as_tabla.".estcla IN (SELECT distinct codemp||codsis||codusu||codintper
		                        FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
		  }
		  else
		  {
		   $as_filtro = " AND '".$ls_codemp."'||'SPG'||'".$ls_usuario."'||".$as_tabla.".codestpro1||".$as_tabla.".codestpro2||".$as_tabla.".codestpro3||'00000000000000000000000000000000000000000000000000'||".$as_tabla.".estcla IN (SELECT distinct codemp||codsis||codusu||codintper
		                        FROM sss_permisos_internos WHERE codemp = '".$ls_codemp."' AND codusu = '".$ls_usuario."' AND codsis = 'SPG' AND enabled=1)";
		  }						
		 }
		//}
	return $as_filtro;	 
	}
	
	function uf_spg_select_unidadadministrativa($as_minuniadm,$as_maxuniadm)
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function : uf_spg_select_unidadadministrativa
	  //        Argumentos :  
      //	       Returns : Retorna true o false si se realizo la consulta para el reporte
	  //	   Description : Envia por referencia el minimo y el maximo de las Unidades Administrativas
	  //        Creado por : Ing. Arnaldo Suárez
	  //    Fecha Creación : 30/07/2008                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = " SELECT min(coduniadm) as minuniadm,      ".
		          "        max(coduniadm) as maxuniadm       ". 
                  " FROM   spg_unidadadministrativa       ".
                  " WHERE  codemp='".$this->ls_codemp."'     ";	  
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
		  $lb_valido=false;
	      $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_select_unidadadministrativa  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
		   if($row=$this->io_sql->fetch_row($rs_data))
		   {
			  $as_minuniadm=$row["minuniadm"];
			  $as_maxuniadm=$row["maxuniadm"];
		   }
		   $this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_minuniadm']=$as_minuniadm;
		$arrResultado['as_maxuniadm']=$as_maxuniadm;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   }//fin uf_spg_select_fuentefinanciamiento()
/****************************************************************************************************************************************/	

	function uf_get_spg_cuenta($as_spg_cuenta,$as_spg_partida,$as_spg_generica,$as_spg_especifica,$as_spg_subesp,$as_spg_int='')
    { /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function : uf_get_spg_cuenta
	  //        Argumentos :  
      //	       Returns : Cuenta separada
	  //	   Description : Envía por referencia una cuenta por partida, generica, especifica y sub-específica
	  //        Creado por : Ing. Arnaldo Suárez
	  //    Fecha Creación : 25/05/2008                       Fecha última Modificacion :      Hora :
  	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_spg_partida = substr($as_spg_cuenta,0,3);
		$as_spg_generica = substr($as_spg_cuenta,3,2);
		$as_spg_especifica = substr($as_spg_cuenta,5,2);
		$as_spg_subesp = substr($as_spg_cuenta,7,2);
		$as_spg_int = substr($as_spg_cuenta,9,2);
		$arrResultado['as_spg_partida']=$as_spg_partida;
		$arrResultado['as_spg_generica']=$as_spg_generica;
		$arrResultado['as_spg_especifica']=$as_spg_especifica;
		$arrResultado['as_spg_subesp']=$as_spg_subesp;
		$arrResultado['as_spg_int']=$as_spg_int;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
   }//fin uf_get_spg_cuenta()
   
   function uf_get_nom_mes($ai_mes,$as_nommes)
	{ /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //	      Function :  uf_get_nom_mes
	  //        Argumentos :  
	  //	       Returns : Cuenta separada
	  //	   Description : Devuelve el nombre de un mes
	  //        Creado por : Ing. Arnaldo Suárez
	  //    Fecha Creación : 15/09/2008                       Fecha última Modificacion :      Hora :
	  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
	
		switch($ai_mes)
		{
		 case 1: $as_nommes = "ENERO";
		         break;
				 
		 case 2: $as_nommes = "FEBRERO";
		         break;
				 
		 case 3: $as_nommes = "MARZO";
		         break;
				 
		 case 4: $as_nommes = "ABRIL";
		         break;
				 
		 case 5 : $as_nommes = "MAYO";
		         break;
				 
		 case 6 : $as_nommes = "JUNIO";
		         break;
				 
		 case 7 : $as_nommes = "JULIO";
		         break;		 		 		 		 		 		 
				 
		 case 8 : $as_nommes = "AGOSTO";
		         break;
				 
		 case 9 : $as_nommes = "SEPTIEMBRE";
		         break;
				 
		 case 10: $as_nommes = "OCTUBRE";
		         break;
				 
		 case 11: $as_nommes = "NOVIEMBRE";
		         break; 
				 
		 case 12: $as_nommes = "DICIEMBRE";
		         break; 		 		  		 		 		 
		}
		return $as_nommes;
	}//fin uf_get_spg_cuenta()
	
	
function uf_formato_cuenta_instructivo($as_cuenta)
{
	 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //	      Function :	uf_formato_cuenta_instructivo
	 //         Access :	private
	 //     Argumentos :    $as_cuenta // cuenta de ingreso
     //	       Returns :	Retorna cuenta con el formato para el instructivo
	 //	   Description :	devuelve la cuenta de ingreso con el formato mostrado en los instructivos
	 //     Creado por :    Ing. Arnaldo Suárez
	 // Fecha Creación :    25/09/2009         Fecha última Modificacion :      Hora :
  	 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 $ls_cuenta="";
 if(!empty($as_cuenta))
 {
  $arreglo = str_split(substr($as_cuenta,1,strlen($as_cuenta)-1),2);
  $total = count($arreglo);
 
  for($i=0;$i<$total;$i++)
  {
   $ls_cuenta .=".".$arreglo[$i];
  }
 
  $ls_cuenta = substr($as_cuenta,0,1).$ls_cuenta;
 }

return $ls_cuenta;
}

function uf_formato_estructura($as_codest1,$as_codest2,$as_codest3,$as_codest4,$as_codest5) {
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ls_loncodestpro1 = $_SESSION["la_empresa"]["loncodestpro1"];
	$ls_loncodestpro2 = $_SESSION["la_empresa"]["loncodestpro2"];
	$ls_loncodestpro3 = $_SESSION["la_empresa"]["loncodestpro3"];
	$ls_loncodestpro4 = $_SESSION["la_empresa"]["loncodestpro4"];
	$ls_loncodestpro5 = $_SESSION["la_empresa"]["loncodestpro5"];
	$ls_codest='';
	
	if ($li_estmodest==1){
		$ls_codest=substr($as_codest1,-$ls_loncodestpro1)."-".substr($as_codest2,-$ls_loncodestpro2)."-".substr($as_codest3,-$ls_loncodestpro3);
	}
	elseif ($li_estmodest==2){
		$ls_codest=substr($as_codest1,-$ls_loncodestpro1)."-".substr($as_codest2,-$ls_loncodestpro2)."-".substr($as_codest3,-$ls_loncodestpro3)."-".substr($as_codest4,-$ls_loncodestpro4)."-".substr($as_codest5,-$ls_loncodestpro5);
	}
	
	return $ls_codest;
}
function uf_denominacion_estructura($as_codest1,$as_codest2,$as_codest3,$as_codest4,$as_codest5,$as_estcla) {
	$ls_denestpro='';
	$li_estmodest=$_SESSION["la_empresa"]["estmodest"];
	$ls_sql="SELECT ep1.denestpro1,ep2.denestpro2,ep3.denestpro3,ep4.denestpro4,ep5.denestpro5
			 FROM   spg_ep1 ep1
			 INNER JOIN spg_ep2 ep2 ON ep1.codemp=ep2.codemp AND ep1.codestpro1=ep2.codestpro1 AND ep1.estcla=ep2.estcla
			 INNER JOIN spg_ep3 ep3 ON ep2.codemp=ep3.codemp AND ep2.codestpro1=ep3.codestpro1 AND ep2.estcla=ep3.estcla AND ep2.codestpro2=ep3.codestpro2
			 INNER JOIN spg_ep4 ep4 ON ep3.codemp=ep4.codemp AND ep3.codestpro1=ep4.codestpro1 AND ep3.estcla=ep4.estcla AND ep3.codestpro2=ep4.codestpro2 AND ep3.codestpro3=ep4.codestpro3
			 INNER JOIN spg_ep5 ep5 ON ep4.codemp=ep5.codemp AND ep4.codestpro1=ep5.codestpro1 AND ep4.estcla=ep5.estcla AND ep4.codestpro2=ep5.codestpro2 AND ep4.codestpro3=ep5.codestpro3 AND ep4.codestpro4=ep5.codestpro4
			 WHERE ep1.codestpro1='".$as_codest1."' AND
			 	   ep2.codestpro2='".$as_codest2."' AND
			       ep3.codestpro3='".$as_codest3."' AND
			       ep4.codestpro4='".$as_codest4."' AND
			       ep5.codestpro5='".$as_codest5."' AND
			       ep5.estcla='".$as_estcla."'";
	$rs_data=$this->io_sql->select($ls_sql);
	if($rs_data===false){
		  $this->io_msg->message("CLASE->sigesp_spg_funciones_reportes  
							      MÉTODO->uf_spg_select_unidadadministrativa  
							      ERROR->".$this->io_function->uf_convertirmsg($this->io_sql->message));
	}
	else{
		if($row=$this->io_sql->fetch_row($rs_data)){
			$ls_denestpro1=$row["denestpro1"];
			$ls_denestpro2=$row["denestpro2"];
			$ls_denestpro3=$row["denestpro3"];
			$ls_denestpro4=$row["denestpro4"];
			$ls_denestpro5=$row["denestpro5"];
			if ($li_estmodest==1){
				$ls_denestpro=$ls_denestpro1.' - '.$ls_denestpro2.' - '.$ls_denestpro3;
			}
			elseif ($li_estmodest==2){
				$ls_denestpro=$ls_denestpro1.' - '.$ls_denestpro2.' - '.$ls_denestpro3.' - '.$ls_denestpro4.' - '.$ls_denestpro5;
			}
		}
		$this->io_sql->free_result($rs_data);
	}
	return $ls_denestpro;
}
}//fin de la clase
?>