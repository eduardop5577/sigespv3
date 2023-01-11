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

$dirsrv = $_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['sigesp_sitioweb'];
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_daogenerico.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_fabricadao.php');
require_once ($dirsrv.'/base/librerias/php/general/sigesp_lib_funciones.php');
require_once ($dirsrv."/modelo/servicio/spg/sigesp_srv_spg_isigeproden.php");
require_once ($dirsrv.'/modelo/servicio/mis/sigesp_srv_mis_comprobante.php');
require_once ($dirsrv.'/modelo/servicio/sss/sigesp_srv_sss_evento.php');
require_once ($dirsrv."/base/librerias/php/general/sigesp_lib_relaciones.php");


class ServicioSIGEPRODEN implements ISigeproden
{

    public  $mensaje; 
    public  $valido; 
    private $conexionBaseDatos; 
    public  $daogenerico;
    private $daoProyecto;

    public function __construct()
    {
        $this->mensaje = '';
        $this->valido = true;
        $this->conexionBaseDatos = ConexionBaseDatos::getInstanciaConexion();	
        $this->codemp=$_SESSION['la_empresa']['codemp'];
        $this->daogenerico = new DaoGenerico ('spg_sigeproden_proyecto');
        $this->daoComprobante = FabricaDao::CrearDAO('N', 'sigesp_cmp');
        $this->daoComprobante->codemp=$this->codemp;
        $this->utilizaprefijo = $this->daoComprobante->utilizaPrefijo('SPG','SPGCMP',$_SESSION['la_logusr']);
        if($this->utilizaprefijo)
        {
                $this->nronuevo=$this->daoComprobante->buscarCodigo('comprobante',true,15,array('procede'=>'SPGCMP'),'SPG','SPGCMP',$_SESSION['la_logusr'],'',$prefijo);
        }
        unset($this->daoComprobante);
        
    }

    public function buscarCodigoProyecto()
    {
        return $this->daogenerico->buscarCodigo ('codprosig',false,10);
    }
        
    public function buscarProyectos($codprosig,$despro)
    {
        switch($_SESSION["la_empresa"]["estmodest"]){
            case "1": // Modalidad por Proyecto
                    $codest1 = "SUBSTR(codestpro1,length(codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
                    $codest2 = "SUBSTR(codestpro2,length(codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
                    $codest3 = "SUBSTR(codestpro3,length(codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
                    $cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3);
            break;
            case "2": // Modalidad por Programatica
                    $codest1 = "SUBSTR(codestpro1,length(codestpro1)-{$_SESSION["la_empresa"]["loncodestpro1"]})";
                    $codest2 = "SUBSTR(codestpro2,length(codestpro2)-{$_SESSION["la_empresa"]["loncodestpro2"]})";
                    $codest3 = "SUBSTR(codestpro3,length(codestpro3)-{$_SESSION["la_empresa"]["loncodestpro3"]})";
                    $codest4 = "SUBSTR(codestpro4,length(codestpro4)-{$_SESSION["la_empresa"]["loncodestpro4"]})";
                    $codest5 = "SUBSTR(codestpro5,length(codestpro5)-{$_SESSION["la_empresa"]["loncodestpro5"]})";
                    $cadenaEstructura = $this->conexionBaseDatos->Concat($codest1,"'-'",$codest2,"'-'",$codest3,"'-'",$codest4,"'-'",$codest5);
            break;
        }

        $criterio = "";
        if ((!is_null($codprosig))&&(!($codprosig=='')))
        {
            $criterio .= "   AND codprosig  = '".$codprosig."' ";
        }
        if ((!is_null($despro))&&(!($despro=='')))
        {
            $criterio .= "   AND despro  LIKE  '%".$despro."%' ";
        }
        $cadenasql="SELECT spg_sigeproden_proyecto.codemp, codprosig, despro, nroptocta, fecptocta, monptocta, enteejecutor, rifenteejecutor, ".
                   "       spg_sigeproden_proyecto.codmon, codestpro1, codestpro2, codestpro3, codestpro4, codestpro5, estcla, spg_cuenta, ".
                   "       codfuefin, sc_cuentad, sc_cuentah, sigesp_moneda.denmon, {$cadenaEstructura} as codestpro ".
                   "  FROM spg_sigeproden_proyecto, sigesp_moneda ".
                   " WHERE spg_sigeproden_proyecto.codemp = '".$this->codemp."'  ".
                   $criterio.
                   "   AND spg_sigeproden_proyecto.codemp = sigesp_moneda.codemp ".
                   "   AND spg_sigeproden_proyecto.codmon = sigesp_moneda.codmon ".
                   " ORDER BY codprosig ";	
        $resultado = $this->conexionBaseDatos->Execute($cadenasql);
        if($resultado===false)
        {
                $this->mensaje .= ' CLASE->SPG MÉTODO->buscarProyectos ERROR->'.$this->conexionbd->ErrorMsg();
                $this->valido = false;
        }
        return $resultado;
    }
    
    public function guardarProyecto($objson,$arrevento)
    {
        $arrDetPre = $objson->arrCuenta;
        
	$this->daoProyecto = FabricaDao::CrearDAO('N', 'spg_sigeproden_proyecto');
        $this->daoProyecto->codemp       = $this->codemp;
        $this->daoProyecto->codprosig    = $objson->codprosig;
        $this->daoProyecto->despro       = $objson->despro;
        $this->daoProyecto->nroptocta    = $objson->nroptocta;
        $this->daoProyecto->fecptocta    = $objson->fecptocta;
        $this->daoProyecto->monptocta    = $objson->monptocta;
        $this->daoProyecto->enteejecutor = $objson->enteejecutor;
        $this->daoProyecto->rifenteejecutor   = $objson->rifenteejecutor;
        $this->daoProyecto->codmon       = $objson->codmon;
        $this->daoProyecto->sc_cuentad   = $objson->sc_cuentad;
        $this->daoProyecto->sc_cuentah   = $objson->sc_cuentah;
        foreach ($arrDetPre as $detalle)
	{
            $i++;
            $codfuefin='--';
            if(!empty($detalle->codfuefin))
            {
		$codfuefin=$detalle->codfuefin;
            }        
            $this->daoProyecto->codestpro1   = $detalle->codestpro1;
            $this->daoProyecto->codestpro2   = $detalle->codestpro2;
            $this->daoProyecto->codestpro3   = $detalle->codestpro3;
            $this->daoProyecto->codestpro4   = $detalle->codestpro4;
            $this->daoProyecto->codestpro5   = $detalle->codestpro5;
            $this->daoProyecto->codfuefin    = $codfuefin;
            $this->daoProyecto->estcla       = $detalle->estcla;
            $this->daoProyecto->spg_cuenta   = $detalle->spg_cuenta;
        }
	$this->valido = $this->daoProyecto->incluir();
        if($this->valido)
        {
            $this->mensaje .= "El Proyecto ".$this->daoProyecto->codprosig.", se incluyo de forma satisfactoria. ";
        }
        else
        {
            $this->mensaje .= $this->daoProyecto->ErrorMsg;
        }

        $servicioEvento = new ServicioEvento();
        $servicioEvento->evento=$arrevento['evento'];
        $servicioEvento->tipoevento=$this->valido; 
        $servicioEvento->codemp=$arrevento['codemp'];
        $servicioEvento->codsis=$arrevento['codsis'];
        $servicioEvento->nomfisico=$arrevento['nomfisico'];
        $servicioEvento->desevetra='Incluyo el proyecto '.$this->daoProyecto->codemp.'::'.$this->daoProyecto->codprosig.'::'.$this->daoProyecto->despro;			
        if ($this->valido) 
        {
                $servicioEvento->incluirEvento();
        }
        else
        {
                $servicioEvento->desevetra=$this->mensaje;
                $servicioEvento->incluirEvento();
        }
        unset($servicioEvento);
        unset($this->daoProyecto);
        return $this->valido;
    }

    public function actualizarProyecto($objson,$arrevento)
    {
        $arrDetPre = $objson->arrCuenta;
        
        //obteniendo las instacias de los dao's
        $this->daoProyecto = FabricaDao::CrearDAO("N", "spg_sigeproden_proyecto");

        //seteando la data e iniciando transaccion de base de datos
        $this->daoProyecto->setData($objson);
        $this->daoProyecto->codemp=$this->codemp;
        foreach ($arrDetPre as $detalle)
	{
            $i++;
            $codfuefin='--';
            if(!empty($detalle->codfuefin))
            {
		$codfuefin=$detalle->codfuefin;
            }        
            $this->daoProyecto->codestpro1   = $detalle->codestpro1;
            $this->daoProyecto->codestpro2   = $detalle->codestpro2;
            $this->daoProyecto->codestpro3   = $detalle->codestpro3;
            $this->daoProyecto->codestpro4   = $detalle->codestpro4;
            $this->daoProyecto->codestpro5   = $detalle->codestpro5;
            $this->daoProyecto->codfuefin    = $codfuefin;
            $this->daoProyecto->estcla       = $detalle->estcla;
            $this->daoProyecto->spg_cuenta   = $detalle->spg_cuenta;
        }
        
        DaoGenerico::iniciarTrans();

        //modificando el registro y escribiendo en el log
        if($this->daoProyecto->modificar())
        {
                $this->valido=true;
                $this->mensaje .= "El Proyecto ".$this->daoProyecto->codprosig.", se actualizo de forma satisfactoria. ";            
        }
        else
        {
                $this->valido=false;
                $this->mensaje .= $this->daoProyecto->ErrorMsg;
        }
        $servicioEvento = new ServicioEvento();
        $servicioEvento->evento=$arrevento['evento'];
        $servicioEvento->codemp=$arrevento['codemp'];
        $servicioEvento->codsis=$arrevento['codsis'];
        $servicioEvento->nomfisico=$arrevento['nomfisico'];
        $servicioEvento->desevetra=$arrevento['desevetra'];
        if (DaoGenerico::completarTrans($this->valido))
        {
                $servicioEvento->tipoevento=true;
                $servicioEvento->incluirEvento();
        }
        else
        {
                $servicioEvento->tipoevento=false;
                $servicioEvento->desevetra=$this->mensaje;
                $servicioEvento->incluirEvento();
        }
        //liberando variables y retornando el resultado de la operacion
        unset($servicioEvento);
        unset($this->daoProyecto);
        return $this->valido;
    }
 
    public function eliminarProyecto($codprosig,$arrevento)
    {
        //obteniendo las instacias de los dao's
        $this->daoProyecto = FabricaDao::CrearDAO("N", "spg_sigeproden_proyecto");

        //seteando la data e iniciando transaccion de base de datos
        $this->daoProyecto->codemp=$this->codemp;
        $this->daoProyecto->codprosig=$codprosig;
        $relaciones = new servicioRelaciones();
        $condicion="AND  column_name='codprosig'";
        $tabla= 'spg_sigeproden_proyecto';
        $valor=$this->daoProyecto->codprosig;
        $mensaje='';
        if(!$relaciones->verificarRelaciones($condicion,$tabla,$valor,$mensaje))
        {
            DaoGenerico::iniciarTrans();

            if($this->daoProyecto->eliminar())
            {
                    $this->valido=true;
                    $this->mensaje .= "El Proyecto ".$this->daoProyecto->codprosig.", se elimino de forma satisfactoria. ";
            }
            else
            {
                    $this->valido=false;
                    $this->mensaje .= $this->daoProyecto->ErrorMsg;
            }		
            $servicioEvento = new ServicioEvento();
            $servicioEvento->evento=$arrevento['evento'];
            $servicioEvento->codemp=$arrevento['codemp'];
            $servicioEvento->codsis=$arrevento['codsis'];
            $servicioEvento->nomfisico=$arrevento['nomfisico'];
            $servicioEvento->desevetra=$arrevento['desevetra'];
            //completando la transaccion retorna 1 si no hay errores
            if (DaoGenerico::completarTrans($this->valido))
            {
                    $resultado = 1;
                    $servicioEvento->tipoevento=true;
                    $servicioEvento->incluirEvento();
            }
            else
            {
                    $servicioEvento->tipoevento=false;
                    $servicioEvento->desevetra=$this->mensaje;
                    $servicioEvento->incluirEvento();
            }
        }	
        else
        {
            $this->valido=false;
            $this->mensaje .= 'El Proyecto esta asociado a comprobantes, no puede ser Eliminado';
        }
        //liberando variables y retornando el resultado de la operacion
        unset($this->daoProyecto);
        unset($relaciones);		
        return $this->valido;
    }

    public function generarComprobante($objson,$arrevento)
    {
        $i=1;
        $arrcabecera = array();
        $arregloSPG = array();
        $fecha = convertirFechaBd($objson->fecha);
        $objson->evento='INSERT';
        DaoGenerico::iniciarTrans();  		
        if(!validarFechaPeriodo($fecha))
        {
                $this->mensaje .=  'Verifique que el periodo, y el mes de la empresa est&#233;n abiertos';
                $this->valido = false;	
        }
        if($this->valido)
        {
            if($objson->tipo_destino=='P')
            {
                    $codpro = $objson->cod_pro;
                    $cedbene = '----------';
            }
            else if($objson->tipo_destino=='B')
            {
                    $cedbene = $objson->cod_pro;
                    $codpro = '----------';
            }
            else
            {
                    $cedbene = '----------';
                    $codpro = '----------';
            }
            if(($this->utilizaprefijo)&&($objson->evento=='INSERT'))
            {
                if (fillComprobante($objson->comprobante)!=$this->nronuevo)
                {
                        $objson->comprobante=$this->nronuevo;
                        $this->mensaje .= " Le fue asignado el numero de comprobante ".$this->nronuevo.", ";
                }
            }
            $codfuefin='--';
            if(!empty($objson->codfuefin))
            {
                    $codfuefin=$objson->codfuefin;
            }
            $monto = formatoNumericoBd($objson->monto*$objson->tascam,1);
            $arrcabecera['codemp'] = $this->codemp;
            $arrcabecera['procede'] = $objson->procede;
            $arrcabecera['comprobante'] = fillComprobante($objson->comprobante);
            $arrcabecera['codban'] = '---';
            $arrcabecera['ctaban'] = '-------------------------';
            $arrcabecera['fecha'] = $fecha;
            $arrcabecera['descripcion'] = $objson->descripcion;
            $arrcabecera['tipo_comp'] = 1;
            $arrcabecera['tipo_destino'] = $objson->tipo_destino;
            $arrcabecera['cod_pro'] = $codpro;
            $arrcabecera['ced_bene'] = $cedbene;
            $arrcabecera['numpolcon'] = 0;
            $arrcabecera['esttrfcmp'] = 0;
            $arrcabecera['estrenfon'] = 0;
            $arrcabecera['codfuefin'] = $codfuefin;
            $arrcabecera['total'] = $monto;
            $arrcabecera['numconcom'] = '';
            $arrcabecera['codusu'] = $_SESSION['la_logusr'];

            $arregloSPG[$i]['codemp']=$arrcabecera['codemp'];
            $arregloSPG[$i]['procede']= $arrcabecera['procede'];
            $arregloSPG[$i]['comprobante']= $arrcabecera['comprobante'];
            $arregloSPG[$i]['codban']= $arrcabecera['codban'];
            $arregloSPG[$i]['ctaban']= $arrcabecera['ctaban'];
            $arregloSPG[$i]['fecha']= $arrcabecera['fecha'];
            $arregloSPG[$i]['descripcion']= $arrcabecera['descripcion'];		
            $arregloSPG[$i]['procede_doc'] = $arrcabecera['procede'];
            $arregloSPG[$i]['documento'] = $arrcabecera['comprobante'];
            $arregloSPG[$i]['orden']= $i;	
            $arregloSPG[$i]['operacion'] = $objson->operaciones;
            $arregloSPG[$i]['estcla'] = $objson->estcla;
            $arregloSPG[$i]['codestpro1'] = $objson->codestpro1;
            $arregloSPG[$i]['codestpro2'] = $objson->codestpro2;
            $arregloSPG[$i]['codestpro3'] = $objson->codestpro3;
            $arregloSPG[$i]['codestpro4'] = $objson->codestpro4;
            $arregloSPG[$i]['codestpro5'] = $objson->codestpro5;
            $arregloSPG[$i]['codfuefin'] = $codfuefin;
            $arregloSPG[$i]['spg_cuenta'] = $objson->spg_cuenta;
            $arregloSPG[$i]['monto'] = $arrcabecera['total'] ;

            $numconcom="000000000000000";
            if(($arregloSPG[1]['operacion']=='CS')||($arregloSPG[1]['operacion']=='CG')||($arregloSPG[1]['operacion']=='CCP'))
            {
                    $this->daoComprobante = FabricaDao::CrearDAO("N", "sigesp_cmp");
                    $this->daoComprobante->codemp=$codemp;
                    $numconcom=$this->daoComprobante->buscarCodigo('numconcom',true,15,'','MIS','NUMCON',$_SESSION['la_logusr'],'nroinicom','');
            }
            if(($arrcabecera['numconcom']=="")||(($arrcabecera['numconcom']=="000000000000000")))
            {
                    $arrcabecera['numconcom'] = $numconcom;
            }

            $serviciocomprobante = new ServicioComprobante();
            $serviciocomprobante->prefijo = $objson->prefijo;                                        
            $this->valido = $serviciocomprobante->guardarComprobante($arrcabecera,$arregloSPG,null,null,$arrevento,$this->utilizaprefijo);
            $this->mensaje .= $serviciocomprobante->mensaje;
            unset($serviciocomprobante);

            if ($this->valido)
            {
                //obteniendo las instacias de los dao's
                $this->daoProyecto = FabricaDao::CrearDAO("N", "spg_dt_sigeproden_proyecto");

                //seteando la data e iniciando transaccion de base de datos
                $this->daoProyecto->codemp=$this->codemp;
                $this->daoProyecto->codprosig=$objson->codprosig;
                $this->daoProyecto->codmon=$objson->codmon;
                $this->daoProyecto->procede=$arrcabecera['procede'];
                $this->daoProyecto->comprobante=$arrcabecera['comprobante'];
                $this->daoProyecto->codban=$arrcabecera['codban'];
                $this->daoProyecto->ctaban=$arrcabecera['ctaban'];
                $this->daoProyecto->descripcion=$arrcabecera['descripcion'];
                $this->daoProyecto->fecha=$arrcabecera['fecha'];
                $this->daoProyecto->operacion=$objson->operaciones;
                $this->daoProyecto->tipo_destino=$arrcabecera['tipo_destino'];
                $this->daoProyecto->cod_pro=$arrcabecera['cod_pro'];
                $this->daoProyecto->ced_bene=$arrcabecera['ced_bene'];
                $this->daoProyecto->monto=$arrcabecera['total']; 
                $this->daoProyecto->tascam=$objson->tascam;
                $this->valido = $this->daoProyecto->incluir();
                unset($this->daoProyecto);
            }
            
            $servicioEvento = new ServicioEvento();
            $servicioEvento->evento=$arrevento['evento'];
            $servicioEvento->codemp=$arrevento['codemp'];
            $servicioEvento->codsis=$arrevento['codsis'];
            $servicioEvento->nomfisico=$arrevento['nomfisico'];
            $servicioEvento->desevetra=$arrevento['desevetra'];
            //completando la transaccion retorna 1 si no hay errores
            if (DaoGenerico::completarTrans($this->valido)) 
            {
                    $servicioEvento->tipoevento=true;
                    $servicioEvento->incluirEvento();
                    $this->mensaje .= 'Registro guardado con &#233;xito'; 		
            }
            else{
                    $servicioEvento->tipoevento=false;
                    $servicioEvento->desevetra=$arrevento['desevetra'];
                    $servicioEvento->incluirEvento();
                    $this->valido=false;
            }

            //liberando variables y retornando el resultado de la operacion
            unset($this->daoRegistroEvento);
	}
	return $this->valido;
    }

    public function buscarTasaCambio($codmon, $fecha)
    {
        $tascam=1;
        $cadenasql = "SELECT tascam1 ".
                     "  FROM sigesp_dt_moneda ".
                     " WHERE codemp='".$this->codemp."' ".
                     "   AND codmon='".$codmon."' ".
                     "   AND fecha<='".$fecha."' ".
                     " ORDER BY fecha DESC ";

        $resultado = $this->conexionBaseDatos->Execute($cadenasql);
        if($resultado===false)
        {
                $this->mensaje .= ' CLASE->SPG MÉTODO->buscarTasaCambio ERROR->'.$this->conexionbd->ErrorMsg();
                $this->valido = false;
        }
        else
        {
            if (!$resultado->EOF)
            {
                $tascam = number_format($resultado->fields['tascam1'],8,'.',''); 
            }
        }
        return $tascam;        
    }


    public function buscarComprobantesAsociados($codprosig)
    {
        $cadenasql="SELECT comprobante, descripcion, fecha, monto ".
                   "  FROM spg_dt_sigeproden_proyecto ".
                   " WHERE codemp = '".$this->codemp."'  ".
                   "   AND codprosig = '".$codprosig."'  ".
                   " ORDER BY fecha ";	
        $resultado = $this->conexionBaseDatos->Execute($cadenasql);
        if($resultado===false)
        {
                $this->mensaje .= ' CLASE->SPG MÉTODO->buscarComprobantesAsociados ERROR->'.$this->conexionbd->ErrorMsg();
                $this->valido = false;
        }
        return $resultado;
    }
    
}
?>