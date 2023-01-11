<?Php
/***********************************************************************************
* @fecha de modificacion: 07/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_srh_c_parroquia
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;
	var $ls_codemp;

	public function __construct($path)
	{   require_once($path."base/librerias/php/general/sigesp_lib_sql.php");
		require_once($path."base/librerias/php/general/sigesp_lib_datastore.php");
		require_once($path."base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once($path."base/librerias/php/general/sigesp_lib_include.php");
		require_once($path."shared/class_folder/sigesp_c_seguridad.php");
		require_once($path."base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_msg=new class_mensajes();
		$this->io_funcion = new class_funciones();
		$this->la_empresa=$_SESSION["la_empresa"];
		$in=new sigesp_include();
		$this->con=$in->uf_conectar();
		$this->io_sql=new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];

}
  
  public function getProximoCodigo($ps_codest,$ps_codmun)
  {
    $ls_codpar = "01";
    $ls_sql = "SELECT MAX(codpar) AS codigo FROM sigesp_parroquia ".
	          "WHERE codest = '$ps_codest'".
	          " AND codmun = '$ps_codmun'";
   $rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->parroquia MÉTODO->getProximoCodigo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			
		}
		else
		{
      $ls_codpar = $la_datos["codigo"][0]+1;
	  }
    if ($ls_codpar < 10)
     $ls_codpar = "0".$ls_codpar;
    return $ls_codpar;
  }
  
  public function getparroquias($ps_codpai,$ps_codest,$ps_codmun,$ps_orden="",$pa_datos="")
  {
   $lb_valido=true;
    $ls_sql = "SELECT * FROM sigesp_parroquia ".
	          " WHERE codpai= '$ps_codpai' ".
			  " AND codest = '$ps_codest' ".
	          " AND codmun = '$ps_codmun' ".
			  " AND codpar <> '---' ".$ps_orden;
			 
			
	$arrResultado=$this->io_sql->seleccionar($ls_sql, $pa_datos);
	$lb_valido = $arrResultado['valido'];
	$pa_datos = $arrResultado['pa_datos'];
		$arrResultado['pa_datos']=$pa_datos;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	
  }
 
}
?>