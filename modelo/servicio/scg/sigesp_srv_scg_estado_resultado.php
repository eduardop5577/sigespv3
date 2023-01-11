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

$dirctrscg = "";
$dirctrscg = dirname(__FILE__);
$dirctrscg = str_replace("\\","/",$dirctrscg);
$dirsrvrpc = str_replace("/modelo/servicio/scg","",$dirctrscg); 
$dirctrscg = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirctrscg."/base/librerias/php/general/sigesp_lib_fabricadao.php");
require_once ($dirctrscg."/modelo/servicio/sss/sigesp_srv_sss_evento.php");
require_once ($dirctrscg."/modelo/servicio/scg/sigesp_srv_scg_iestado_resultado.php");

class servicioEstadoResul implements iEstadoResul
{
	private $daoPago;
	private $daoRegistroEvento;
	private $daoRegistroFalla;
	public  $mensaje; 
	public  $valido; 
	
	public function __construct()
    {
		$this->daoPago = null;
		$this->daoRegistroEvento = null;
		$this->daoRegistroFalla  = null;
		$this->mensaje = '';
		$this->valido = true;
        $this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();	
	}
        
	public function buscarCentroCostos()
    {
		$this->daoPago = FabricaDao::CrearDAO("N", "sigesp_cencosto");
		$datacencos = $this->daoPago->leerTodos('codcencos',1,'');
		unset($this->daoPago);
		return $datacencos;
	}
	
	public function buscarAnios()
        {	
		if($_SESSION["ls_gestor"]=='INFORMIX')
		{
		  $ls_selec="distinct substr(fecsal,1,4) as anuales ";
		}
		else 
		{
		  $ls_selec="distinct substr(cast(fecsal as char(10)),1,4) as anuales ";
		}
		
		$cadenasql= " SELECT $ls_selec ".
                            " FROM scg_saldos WHERE codemp = '".$_SESSION["la_empresa"]["codemp"]."' ".
                            " ORDER BY anuales";

		$conexionbd = ConexionBaseDatos::getInstanciaConexion();
		$resultado = $conexionbd->Execute ( $cadenasql );
		unset($conexionbd);
		return $resultado;
	}

	public function filtroSeguridad($tabla,$nivel)
	{
		$gestor    = $_SESSION["ls_gestor"];
		$estmodest = $_SESSION["la_empresa"]["estmodest"];
		$usuario   = $_SESSION["la_logusr"];
		$codemp    = $_SESSION["la_empresa"]["codemp"];
		$filtro = "";
		$filtroa = "";
		$filtrob = "";
		$filtroc = "";
		if ((strtoupper($gestor)=="MYSQLT") || (strtoupper($gestor)=="MYSQLI"))
		{
			$filtroa = " CONCAT('".$codemp."','SPG','".$usuario."'";
			$filtrob = " IN (SELECT distinct CONCAT(codemp,codsis,codusu";
			$filtroc = " FROM sss_permisos_internos WHERE codemp = '".$codemp."' AND codusu = '".$usuario."' AND codsis = 'SPG' AND enabled=1)";
			switch ($nivel)
			{
				case "1":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,25),substr(codintper,126,1)) ";
				break;
				
				case "2":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,50),substr(codintper,126,1)) ";
				break;
				
				case "3":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".codestpro3,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,75),substr(codintper,126,1)) ";
				break;

				case "4":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".codestpro3,".$tabla.".codestpro4,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,100),substr(codintper,126,1)) ";
				break;

				case "5":
					$filtroa .= ",".$tabla.".codestpro1,".$tabla.".codestpro2,".$tabla.".codestpro3,".$tabla.".codestpro4,".$tabla.".codestpro5,".$tabla.".estcla) ";
					$filtrob .= ",substr(codintper,1,125),substr(codintper,126,1)) ";
				break;
			}
		 }
		 else
		 {
			$filtroa = " '".$codemp."'||'SPG'||'".$usuario."'";
			$filtrob = " IN (SELECT distinct codemp||codsis||codusu";
			$filtroc = " FROM sss_permisos_internos WHERE codemp = '".$codemp."' AND codusu = '".$usuario."' AND codsis = 'SPG' AND enabled=1)";
			switch ($nivel)
			{
				case "1":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,25)||substr(codintper,126,1) ";
				break;
				
				case "2":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,50)||substr(codintper,126,1) ";
				break;
				
				case "3":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".codestpro3||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,75)||substr(codintper,126,1)  ";
				break;

				case "4":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".codestpro3||".$tabla.".codestpro4||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,100)||substr(codintper,126,1) ";
				break;

				case "5":
					$filtroa .= "||".$tabla.".codestpro1||".$tabla.".codestpro2||".$tabla.".codestpro3||".$tabla.".codestpro4||".$tabla.".codestpro5||".$tabla.".estcla ";
					$filtrob .= "||substr(codintper,1,125)||substr(codintper,126,1) ";
				break;
			}
		}
		$filtro = $filtroa.$filtrob.$filtroc;
		return $filtro;	 
	}

	public function buscarEstructuras($codemp) 
	{
            switch ($_SESSION["la_empresa"]["estmodest"])
            {
                    case "1":
                            $filtro=$this->filtroSeguridad("spg_ep3","3");
                            $denominacion = $this->conexionBaseDatos->Concat('spg_ep1.denestpro1',"' - '",'spg_ep2.denestpro2',"' - '",'spg_ep3.denestpro3');
                            $codest1 = "SUBSTR(spg_ep5.codestpro1,length(spg_ep5.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
                            $codest2 = "SUBSTR(spg_ep5.codestpro2,length(spg_ep5.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
                            $codest3 = "SUBSTR(spg_ep5.codestpro3,length(spg_ep5.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
                            $cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'","spg_ep5.estcla");
                            break;
                    case "2":
                            $filtro=$this->filtroSeguridad("spg_ep5","5");
                            $denominacion = $this->conexionBaseDatos->Concat('spg_ep1.denestpro1',"' - '",'spg_ep2.denestpro2',"' - '",'spg_ep3.denestpro3',"' - '",'spg_ep4.denestpro4',"' - '",'spg_ep5.denestpro5');
                            $codest1 = "SUBSTR(spg_ep5.codestpro1,length(spg_ep5.codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
                            $codest2 = "SUBSTR(spg_ep5.codestpro2,length(spg_ep5.codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
                            $codest3 = "SUBSTR(spg_ep5.codestpro3,length(spg_ep5.codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
                            $codest4 = "SUBSTR(spg_ep5.codestpro4,length(spg_ep5.codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
                            $codest5 = "SUBSTR(spg_ep5.codestpro5,length(spg_ep5.codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
                            $cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5,"'-'","spg_ep5.estcla");
                            break;

            }
            $cadenaSQL= "SELECT spg_ep5.codestpro1, spg_ep5.codestpro2, spg_ep5.codestpro3, spg_ep5.codestpro4, spg_ep5.codestpro5, spg_ep5.estcla, ".
                        "        ".$denominacion." AS denominacion,  ".$cadenaEstructura." AS estructura ".
                        "	FROM spg_ep1 ". 
                        " INNER JOIN spg_ep2 ".
                        "    ON spg_ep1.codemp = spg_ep2.codemp ".
                        "   AND spg_ep1.codestpro1 = spg_ep2.codestpro1 ".
                        "   AND spg_ep1.estcla = spg_ep2.estcla ".
                        " INNER JOIN spg_ep3  ".
                        "    ON spg_ep2.codemp = spg_ep3.codemp  ".
                        "   AND spg_ep2.codestpro1 = spg_ep3.codestpro1 ".
                        "   AND spg_ep2.codestpro2 = spg_ep3.codestpro2 ". 
                        "   AND spg_ep2.estcla = spg_ep3.estcla ". 
                        " INNER JOIN spg_ep4 ".
                        "    ON spg_ep3.codemp = spg_ep4.codemp ".
                        "   AND spg_ep3.codestpro1 = spg_ep4.codestpro1 ".
                        "   AND spg_ep3.codestpro2 = spg_ep3.codestpro2 ". 
                        "   AND spg_ep3.codestpro3 = spg_ep4.codestpro3 ".
                        "   AND spg_ep3.estcla = spg_ep4.estcla ".
                        " INNER JOIN spg_ep5 ".
                        "    ON spg_ep4.codemp = spg_ep5.codemp ".
                        "   AND spg_ep4.codestpro1 = spg_ep5.codestpro1 ".
                        "   AND spg_ep4.codestpro2 = spg_ep5.codestpro2 ".
                        "   AND spg_ep4.codestpro3 = spg_ep5.codestpro3 ".
                        "   AND spg_ep4.codestpro4 = spg_ep5.codestpro4 ".
                        "   AND spg_ep4.estcla = spg_ep5.estcla ".
                        "   AND ".$filtro.
                        " WHERE spg_ep1.codemp = '{$codemp}' ".
                        "   AND spg_ep5.codestpro1<>'-------------------------' ".
                        "   AND spg_ep5.codestpro2<>'-------------------------' ".
                        "   AND spg_ep5.codestpro3<>'-------------------------' ".
                        " ORDER BY spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5 ";
		return $this->conexionBaseDatos->Execute($cadenaSQL);
	}

        
}
?>