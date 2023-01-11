<?php
/***********************************************************************************
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_ins_c_integracionsargus
{

    public $mensaje; 
    public $valido; 
    public $gestor_int;
    public $puerto_int;
    public $servidor_int;
    public $basedatos_int;
    public $login_int;
    public $password_int;
    public $cuenta_ingreso;
    public $cuenta_ingreso_iva;
    public $cuenta_contable_iva_retenido;
    public $iva;
    public $retencion_iva;
    public $fechadesde;
    public $fechahasta;
    public $totalregistros;    
    public $totalerrores; 
    private $codban;
    private $cuentadebe;
    private $cuentahaber;
    private $cuentahaberiva;
    private $cuentasargus;
    private $arrCuentaBanco;
    private $movimientos;
    private $io_conexion;
    private $io_sargus;

    //-----------------------------------------------------------------------------------------------------------------------------------
    public function __construct()
    {	
        require_once("../base/librerias/php/general/sigesp_lib_include.php");
        $this->io_include=new sigesp_include();
        $this->io_conexion=$this->io_include->uf_conectar();
        require_once("../base/librerias/php/general/sigesp_lib_sql.php");
        $this->io_sql=new class_sql($this->io_conexion);	
        require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
        $this->io_mensajes=new class_mensajes();		
        require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
        $this->io_funciones=new class_funciones();				
        require_once("../shared/class_folder/sigesp_c_seguridad.php");
        $this->io_seguridad= new sigesp_c_seguridad();
        $this->codemp=$_SESSION["la_empresa"]["codemp"];
        require_once ('../modelo/servicio/scb/sigesp_srv_scb_movimientos_scb.php');            
        $this->valido=true;
        $this->mensaje="";
        $this->totalregistros=0;
        $this->totalerrores=0;
        $this->cuentasargus="";
        $this->movimientos="";
        $this->arrCuentaBanco = Array();
    }// end function 

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
    {
        $ls_valor="";
        $ls_sql="SELECT value ".
                "  FROM sigesp_config ".
                " WHERE codemp='".$this->codemp."' ".
                "   AND codsis='".$as_sistema."' ".
                "   AND seccion='".$as_seccion."' ".
                "   AND entry='".$as_variable."' ";

        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_select_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
            $this->valido=false;
        }
        else
        {
            if(!$rs_data->EOF)
            {
                $ls_valor=$rs_data->fields["value"];
            }
            else
            {
                $this->valido=$this->uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
                if ($this->valido)
                {
                    $ls_valor=$this->uf_select_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo);
                }
            }
                $this->io_sql->free_result($rs_data);		
        }
        return rtrim($ls_valor);
    }// end function uf_select_config
    //-----------------------------------------------------------------------------------------------------------------------------------	

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_insert_config($as_sistema, $as_seccion, $as_variable, $as_valor, $as_tipo)
    {
        $this->io_sql->begin_transaction();		
        $ls_sql="DELETE ".
                "  FROM sigesp_config ".
                " WHERE codemp='".$this->codemp."' ".
                "   AND codsis='".$as_sistema."' ".
                "   AND seccion='".$as_seccion."' ".
                "   AND entry='".$as_variable."' ";		
        $li_row=$this->io_sql->execute($ls_sql);
        if($li_row===false)
        {
            $this->valido=false;
            $this->io_mensajes->message("CLASE->SNO MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            $this->io_sql->rollback();
        }
        else
        {
            switch ($as_tipo)
            {
                case "C"://Caracter
                    $valor = $as_valor;
                    break;

                case "D"://Double
                    $as_valor=str_replace(".","",$as_valor);
                    $as_valor=str_replace(",",".",$as_valor);
                    $valor = $as_valor;
                    break;

                case "B"://Boolean
                    $valor = $as_valor;
                    break;

                case "I"://Integer
                    $valor = intval($as_valor);
                    break;
            }
            $ls_sql="INSERT INTO sigesp_config(codemp, codsis, seccion, entry, value, type) VALUES ".
                    "('".$this->codemp."','".$as_sistema."','".$as_seccion."','".$as_variable."','".$valor."','".$as_tipo."')";

            $li_row=$this->io_sql->execute($ls_sql);
            if($li_row===false)
            {
                    $this->valido=false;
                    $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_insert_config ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
                    $this->io_sql->rollback();
            }
            else
            {
                    $this->io_sql->commit();
            }
        }
        return $this->valido;
    }// end function uf_insert_config	
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_load_datos()
    {

        $total=0;
        $this->io_sargus=$this->io_include->uf_conectar_otra_bd($this->servidor_int,$this->login_int,$this->password_int,$this->basedatos_int,
                                                               $this->gestor_int,$this->puerto_int);
        $this->io_sargus->io_sql=new class_sql($this->io_sargus);	

        $this->fechadesde=$this->io_funciones->uf_convertirdatetobd($this->fechadesde);
        $this->fechahasta=$this->io_funciones->uf_convertirdatetobd($this->fechahasta);

        $ls_sql="SELECT COUNT(*) AS total ".
                "  FROM integracion ".
                " WHERE estatus_integracion=0 ".
                "   AND fecha_operacion BETWEEN '".$this->fechadesde."' AND '".$this->fechahasta."'";
        $rs_data=$this->io_sargus->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->valido=false;
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_load_datos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sargus->io_sql->message));
        }
        else
        {
            while(!$rs_data->EOF)
            {
                $total= $rs_data->fields["total"];
                $rs_data->MoveNext();
            }
            $this->io_sargus->io_sql->free_result($rs_data);
        }
        unset($this->io_sargus);
        return $total;		
    }// end function uf_load_datos
    //-----------------------------------------------------------------------------------------------------------------------------------	

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_integrar_sargus($aa_seguridad)
    {
        $arrEvento['codemp']    = $this->codemp;
        $arrEvento['codusu']    = $_SESSION['la_logusr'];
        $arrEvento['codsis']    = 'INS';
        $arrEvento['evento']    = 'PROCESAR';
        $arrEvento['nomfisico'] = 'sigesp_ins_p_integracionsargus.php';                
        
        $this->io_sargus=$this->io_include->uf_conectar_otra_bd($this->servidor_int,$this->login_int,$this->password_int,$this->basedatos_int,
                                                               $this->gestor_int,$this->puerto_int);
        
        $this->io_sargus->io_sql=new class_sql($this->io_sargus);	
        //$this->io_sargus->debug=true;

        $this->fechadesde=$this->io_funciones->uf_convertirdatetobd($this->fechadesde);
        $this->fechahasta=$this->io_funciones->uf_convertirdatetobd($this->fechahasta);

        $this->valido = $this->uf_verificar_existencia_spicuenta($this->cuenta_ingreso,"1");
        if ($this->valido)
        {
            $this->valido = $this->uf_verificar_existencia_spicuenta($this->cuenta_ingreso_iva,"2");
        }
        if ($this->valido)
        {
            $this->valido = $this->uf_verificar_existencia_scgcuenta($this->cuenta_contable_iva_retenido);
        }
        
        $this->iva=number_format(($this->iva/100), 2, ".", "");
        $this->retencion_iva=number_format(($this->retencion_iva/100), 2, ".", "");
        
        if ($this->valido)
        {                        
            $ls_sql="SELECT cod_factura, count(cod_factura) AS nrotransacciones ".
                    "  FROM integracion ".
                    " WHERE estatus_integracion=0 ".
                    "   AND fecha_operacion BETWEEN '".$this->fechadesde."' AND '".$this->fechahasta."' ".
                    " GROUP BY cod_factura ".
                    " ORDER BY fecha_operacion ASC";
            $rs_data=$this->io_sargus->io_sql->select($ls_sql);
            if($rs_data===false)
            {
                $this->valido=false;
                $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_integrar_sargus ERROR->".$this->io_funciones->uf_convertirmsg($io_sigefirrhh->io_sql->message));
            }
            else
            {
                while((!$rs_data->EOF)&($this->valido))
                {
                    $cod_factura=$rs_data->fields["cod_factura"];
                    if ($cod_factura!="")
                    {
                        $cod_factura=$rs_data->fields["cod_factura"];
                        $nrotransacciones=$rs_data->fields["nrotransacciones"];
                        $this->valido = $this->uf_integrar_detalles($cod_factura,$nrotransacciones,$arrEvento);
                    }
                    $rs_data->MoveNext();
                }
                $this->io_sargus->io_sql->free_result($rs_data);
            }
            if($this->valido)
            {
                /////////////////////////////////         SEGURIDAD               /////////////////////////////		
                $ls_evento="PROCESS";
                $ls_descripcion="Integro de SARGUS los movimientos de banco ".$this->movimientos;
                $this->valido= $this->io_seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],$aa_seguridad["sistema"],$ls_evento,
                                                                                  $aa_seguridad["logusr"],$aa_seguridad["ventanas"],$ls_descripcion);
                /////////////////////////////////         SEGURIDAD               /////////////////////////////
            }
        }
        unset($this->io_sargus);
        return $this->valido;
    }// end function uf_integrar_sargus
    //-----------------------------------------------------------------------------------------------------------------------------------	

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_integrar_detalles($cod_factura,$nrotransacciones,$arrEvento)
    {
        $ls_sql="SELECT nro_operacion, fecha_operacion, monto_operacion, monto_base, monto_iva, monto_total, cod_cuenta, codigo_factura ".
                "  FROM integracion ".
                " WHERE estatus_integracion=0 ".
                "   AND cod_factura = ".$cod_factura." ".
                " ORDER BY cod_cuenta ASC";
        $rs_data=$this->io_sargus->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->valido=false;
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_integrar_sargus ERROR->".$this->io_funciones->uf_convertirmsg($io_sigefirrhh->io_sql->message));
        }
        else
        {
            while((!$rs_data->EOF)&($this->valido))
            {
                $nro_operacion="'".$rs_data->fields["nro_operacion"]."'";
                $comprobanteSigesp=trim($rs_data->fields["nro_operacion"]);
                $comprobanteSigesp= substr($comprobanteSigesp,0,15);
                $comprobanteSigesp=str_pad($comprobanteSigesp, 15, '0', STR_PAD_LEFT);
                $fechaMovimiento=$rs_data->fields["fecha_operacion"];
                $ls_cod_cuenta=$rs_data->fields["cod_cuenta"];
                $codfactura=$rs_data->fields["codigo_factura"];
                $li_monto_retencioniva=0;
                $movenext=false;
                if ($nrotransacciones == 1) // Caso con o sin iva, en un solo deposito LISTO
                {
                    $li_monto_base= number_format($rs_data->fields["monto_base"], 2, ".", "");
                    $li_monto_iva=number_format($rs_data->fields["monto_iva"], 2, ".", "");
                    $li_monto_total=number_format($rs_data->fields["monto_total"], 2, ".", "");
                }
                else
                {
                    if ($nrotransacciones == 2) // Mas de una transaccion
                    {
                        $li_monto_iva=number_format($rs_data->fields["monto_iva"], 2, ".", "");
                        if ($li_monto_iva==0) // sin iva LISTO
                        {
                            $li_monto_base= number_format($rs_data->fields["monto_operacion"], 2, ".", "");
                            $li_monto_total=number_format($rs_data->fields["monto_operacion"], 2, ".", "");
                        }
                        else // con iva 
                        {
                            $monto_operacion=$rs_data->fields["monto_operacion"];
                            $monto_base=$rs_data->fields["monto_base"];
                            $monto_iva=$rs_data->fields["monto_iva"];
                            $rs_data->MoveNext();
                            if ($rs_data->fields["cod_cuenta"]!=7) // sin retencion de iva le sacamos el iva a cada deposito
                            {
                                $movenext=true;
                                $li_monto_total=number_format($monto_operacion, 2, ".", "");
                                $li_monto_base= number_format($monto_operacion/($this->iva+1), 2, ".", "");
                                $li_monto_iva=number_format($li_monto_base*$this->iva, 2, ".", "");
                            }
                            else
                            {
                                $li_monto_retencioniva=number_format(($rs_data->fields["monto_operacion"]), 2, ".", "");
                                $nro_operacion .= ",'".$rs_data->fields["nro_operacion"]."'";
                                $li_monto_total=number_format($monto_operacion, 2, ".", "");
                                $li_monto_base= number_format($monto_base, 2, ".", "");
                                $li_monto_iva=number_format($monto_iva, 2, ".", "");
                            }
                        }
                    }      
                    else
                    {
                        $li_monto_iva=number_format($rs_data->fields["monto_iva"], 2, ".", "");
                        if ($li_monto_iva==0) // sin iva LISTO
                        {
                            $li_monto_base= number_format($rs_data->fields["monto_operacion"], 2, ".", "");
                            $li_monto_total=number_format($rs_data->fields["monto_operacion"], 2, ".", "");
                        }  
                        else // con iva LISTO
                        {
                            $li_monto_total=number_format($rs_data->fields["monto_operacion"], 2, ".", "");
                            $li_monto_base= number_format($rs_data->fields["monto_operacion"]/($this->iva+1), 2, ".", "");
                            $li_monto_iva=number_format($li_monto_base*$this->iva, 2, ".", "");                        
                        }
                    }
                }

                $this->codban="";
                $this->ctaban="";
                $this->cuentadebe="";
                $descripcion = "Movimiento de Banco Referente a la factura ".$codfactura;
                if ($this->uf_obtenerbancocuenta($ls_cod_cuenta))
                {
                    $arrCabecera=array();
                    $arregloSCG=array();
                    $arregloSPG=array();
                    $arregloSPI=array();

                    $arrCabecera["codemp"]	     = $this->codemp;
                    $arrCabecera["codban"]	     = $this->codban;
                    $arrCabecera["ctaban"]	     = $this->ctaban;
                    $arrCabecera["numdoc"]	     = $comprobanteSigesp;
                    $arrCabecera["codope"]	     = 'NC';
                    $arrCabecera["estmov"]	     = 'N';
                    $arrCabecera["cod_pro"]	     = '----------';
                    $arrCabecera["ced_bene"]     = '----------';
                    $arrCabecera["tipo_destino"] = '-';
                    $arrCabecera["codconmov"]    = '---';
                    $arrCabecera["fecmov"]	     = $fechaMovimiento;
                    $arrCabecera["conmov"]	     = $descripcion;
                    $arrCabecera["nomproben"]    = 'Ninguno';
                    $arrCabecera["monto"]	     = $li_monto_total;
                    $arrCabecera["estbpd"]	     = 'M';
                    $arrCabecera["estcon"]	     = 0;
                    $arrCabecera["estcobing"]    = 1;
                    $arrCabecera["esttra"]       = 0;
                    $arrCabecera["chevau"]	     = "";
                    $arrCabecera["estimpche"]    = 0;
                    $arrCabecera["monobjret"]    = 0;
                    $arrCabecera["monret"]	     = 0;
                    $arrCabecera["procede"]	     = 'SCBBNC';
                    $arrCabecera["comprobante"]  = $comprobanteSigesp;
                    $arrCabecera["fecha"]	     = '1900-01-01';
                    $arrCabecera["id_mco"]       = ' ';
                    $arrCabecera["emicheproc"]   = 0;
                    $arrCabecera["emicheced"]    = ' ';
                    $arrCabecera["emichenom"]    = ' ';
                    $arrCabecera["emichefec"]    = '1900-01-01';
                    $arrCabecera["estmovint"]    = 0;
                    $arrCabecera["codusu"]       = $_SESSION['la_logusr'];
                    $arrCabecera["codopeidb"]    = ' ';
                    $arrCabecera["aliidb"]       = 0;
                    $arrCabecera["feccon"]       = '1900-01-01';
                    $arrCabecera["estreglib"]    = ' ';
                    $arrCabecera["numcarord"]    = ' ';
                    $arrCabecera["numpolcon"]    = 0;
                    $arrCabecera["coduniadmsig"] = ' ';
                    $arrCabecera["codbansig"]    = ' ';
                    $arrCabecera["fecordpagsig"] = '1900-01-01';
                    $arrCabecera["tipdocressig"] = ' ';
                    $arrCabecera["numdocressig"] = ' ';
                    $arrCabecera["estmodordpag"] = '0';
                    $arrCabecera["codfuefin"]    = '--';
                    $arrCabecera["forpagsig"]    = ' ';
                    $arrCabecera["medpagsig"]    = ' ';
                    $arrCabecera["codestprosig"] = ' ';
                    $arrCabecera["tranoreglib"]  = 0;
                    $arrCabecera["numordpagmin"] = '-';
                    $arrCabecera["codtipfon"]    = '----';
                    $arrCabecera["estmovcob"]    = 0;
                    $arrCabecera["numconint"]    = NULL;
                    $arrCabecera["numchequera"]  = "";
                    $arrCabecera["codmon"]  = "---";
                    $arrCabecera["tascam"]  = 1;
                    $arrCabecera["montot"]  = $li_monto_total;

                    $i=0;
                    $arregloSPI['codemp'][$i]      = $arrCabecera['codemp'];
                    $arregloSPI['codban'][$i]      = $arrCabecera['codban'];
                    $arregloSPI['ctaban'][$i]      = $arrCabecera['ctaban'];
                    $arregloSPI['numdoc'][$i]      = $arrCabecera['numdoc'];
                    $arregloSPI['codope'][$i]      = $arrCabecera['codope'];
                    $arregloSPI['estmov'][$i]      = $arrCabecera['estmov'];
                    $arregloSPI['spicuenta'][$i]   = trim($this->cuenta_ingreso);
                    $arregloSPI['documento'][$i]   = $arrCabecera['numdoc'];
                    $arregloSPI['operacion'][$i]   = 'DC';
                    $arregloSPI['desmov'][$i]      = $descripcion." Cuenta Recaudadora ";
                    $arregloSPI['procede_doc'][$i] = $arrCabecera['procede'];
                    $arregloSPI['monto'][$i]       = $li_monto_base;
                    $arregloSPI['codestpro1'][$i]  = '-------------------------';
                    $arregloSPI['codestpro2'][$i]  = '-------------------------';
                    $arregloSPI['codestpro3'][$i]  = '-------------------------';
                    $arregloSPI['codestpro4'][$i]  = '-------------------------';
                    $arregloSPI['codestpro5'][$i]  = '-------------------------';
                    $arregloSPI['estcla'][$i]      = '-';

                    if ($li_monto_iva>0)
                    {
                        $i++;
                        $arregloSPI['codemp'][$i]      = $arrCabecera['codemp'];
                        $arregloSPI['codban'][$i]      = $arrCabecera['codban'];
                        $arregloSPI['ctaban'][$i]      = $arrCabecera['ctaban'];
                        $arregloSPI['numdoc'][$i]      = $arrCabecera['numdoc'];
                        $arregloSPI['codope'][$i]      = $arrCabecera['codope'];
                        $arregloSPI['estmov'][$i]      = $arrCabecera['estmov'];
                        $arregloSPI['spicuenta'][$i]   = trim($this->cuenta_ingreso_iva);
                        $arregloSPI['documento'][$i]   = $arrCabecera['numdoc'];
                        $arregloSPI['operacion'][$i]   = 'DC';
                        $arregloSPI['desmov'][$i]      = $descripcion." Cuenta Recaudadora IVA";
                        $arregloSPI['procede_doc'][$i] = $arrCabecera['procede'];
                        $arregloSPI['monto'][$i]       = $li_monto_iva;
                        $arregloSPI['codestpro1'][$i]  = '-------------------------';
                        $arregloSPI['codestpro2'][$i]  = '-------------------------';
                        $arregloSPI['codestpro3'][$i]  = '-------------------------';
                        $arregloSPI['codestpro4'][$i]  = '-------------------------';
                        $arregloSPI['codestpro5'][$i]  = '-------------------------';
                        $arregloSPI['estcla'][$i]      = '-';
                    }

                    $i=0;
                    $arregloSCG['codemp'][$i]      = $arrCabecera['codemp'];
                    $arregloSCG['codban'][$i]      = $arrCabecera['codban'];
                    $arregloSCG['ctaban'][$i]      = $arrCabecera['ctaban'];
                    $arregloSCG['numdoc'][$i]      = $arrCabecera['numdoc'];
                    $arregloSCG['codope'][$i]      = $arrCabecera['codope'];
                    $arregloSCG['estmov'][$i]      = $arrCabecera['estmov'];
                    $arregloSCG['scg_cuenta'][$i]  = trim($this->cuentadebe);
                    $arregloSCG['debhab'][$i]      = 'D';
                    $arregloSCG['codded'][$i]      = '00000';
                    $arregloSCG['documento'][$i]   = $arrCabecera['numdoc'];
                    $arregloSCG['desmov'][$i]      =  $descripcion;
                    $arregloSCG['procede_doc'][$i] = $arrCabecera['procede'];
                    $arregloSCG['monto'][$i]       = $li_monto_total;
                    $arregloSCG['monobjret'][$i]   = 0;                            

                    if ($li_monto_retencioniva>0)
                    {
                        $i++;
                        $arregloSCG['codemp'][$i]      = $arrCabecera['codemp'];
                        $arregloSCG['codban'][$i]      = $arrCabecera['codban'];
                        $arregloSCG['ctaban'][$i]      = $arrCabecera['ctaban'];
                        $arregloSCG['numdoc'][$i]      = $arrCabecera['numdoc'];
                        $arregloSCG['codope'][$i]      = $arrCabecera['codope'];
                        $arregloSCG['estmov'][$i]      = $arrCabecera['estmov'];
                        $arregloSCG['scg_cuenta'][$i]  = trim($this->cuenta_contable_iva_retenido);
                        $arregloSCG['debhab'][$i]      = 'D';
                        $arregloSCG['codded'][$i]      = '00000';
                        $arregloSCG['documento'][$i]   = $arrCabecera['numdoc'];
                        $arregloSCG['desmov'][$i]      =  $descripcion;
                        $arregloSCG['procede_doc'][$i] = $arrCabecera['procede'];
                        $arregloSCG['monto'][$i]       = $li_monto_retencioniva;
                        $arregloSCG['monobjret'][$i]   = 0;                            
                    }
                    
                    $i++;
                    $arregloSCG['codemp'][$i]      = $arrCabecera['codemp'];
                    $arregloSCG['codban'][$i]      = $arrCabecera['codban'];
                    $arregloSCG['ctaban'][$i]      = $arrCabecera['ctaban'];
                    $arregloSCG['numdoc'][$i]      = $arrCabecera['numdoc'];
                    $arregloSCG['codope'][$i]      = $arrCabecera['codope'];
                    $arregloSCG['estmov'][$i]      = $arrCabecera['estmov'];
                    $arregloSCG['scg_cuenta'][$i]  = trim($this->cuentahaber);
                    $arregloSCG['debhab'][$i]      = 'H';
                    $arregloSCG['codded'][$i]      = '00000';
                    $arregloSCG['documento'][$i]   = $arrCabecera['numdoc'];
                    $arregloSCG['desmov'][$i]      =  $descripcion;
                    $arregloSCG['procede_doc'][$i] = $arrCabecera['procede'];
                    $arregloSCG['monto'][$i]       =  $li_monto_base;
                    $arregloSCG['monobjret'][$i]   = 0;   

                    if ($li_monto_iva>0)
                    {
                        $i++;
                        $arregloSCG['codemp'][$i]      = $arrCabecera['codemp'];
                        $arregloSCG['codban'][$i]      = $arrCabecera['codban'];
                        $arregloSCG['ctaban'][$i]      = $arrCabecera['ctaban'];
                        $arregloSCG['numdoc'][$i]      = $arrCabecera['numdoc'];
                        $arregloSCG['codope'][$i]      = $arrCabecera['codope'];
                        $arregloSCG['estmov'][$i]      = $arrCabecera['estmov'];
                        $arregloSCG['scg_cuenta'][$i]  = trim($this->cuentahaberiva);
                        $arregloSCG['debhab'][$i]      = 'H';
                        $arregloSCG['codded'][$i]      = '00000';
                        $arregloSCG['documento'][$i]   = $arrCabecera['numdoc'];
                        $arregloSCG['desmov'][$i]      =  $descripcion;
                        $arregloSCG['procede_doc'][$i] = $arrCabecera['procede'];
                        $arregloSCG['monto'][$i]       = $li_monto_iva;
                        $arregloSCG['monobjret'][$i]   = 0;   
                    }
                    if (!$this->existeMovimientoBanco($arrCabecera['codban'], $arrCabecera['ctaban'], $arrCabecera['numdoc'], $arrCabecera['codope']))
                    {   
                        $this->io_sql->begin_transaction();

                        $servicioBancario = new ServicioMovimientoScb();
                        $continuar = $servicioBancario->GuardarAutomatico($arrCabecera,$arregloSCG,$arregloSPG,$arregloSPI,$arrEvento);
                        if ($continuar)
                        {
                            $this->movimientos .= $arrCabecera['codban']."-".$arrCabecera['ctaban']."-".$arrCabecera['numdoc']."-".$arrCabecera['codope']."//";
                            if($this->actualizarEstatusFacturaOperacion($cod_factura,$nro_operacion,1))
                            {
                                $this->totalregistros++;
                                $this->io_sql->commit(); 
                            }
                            else
                            {
                                $this->totalerrores++;
                                $this->io_sql->rollback();
                            }
                        }
                        else
                        {
                            $this->mensaje .= $servicioBancario->mensaje."<br>";
                            $this->totalerrores++;
                            $this->io_sql->rollback();
                        }
                        unset($servicioBancario);
                    }
                    else
                    {
                        $this->totalerrores++;                
                    }
                }
                else 
                {
                    $this->totalerrores++;
                }
                if (!$movenext)
                {
                    $rs_data->MoveNext();
                }
            }
            $this->io_sargus->io_sql->free_result($rs_data);
        }        
        return $this->valido;
    }// end function uf_integrar_sargus
    //-----------------------------------------------------------------------------------------------------------------------------------	
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_verificar_existencia_spicuenta($cuenta,$tipo)
    {
        $ls_sql="SELECT spi_cuenta, sc_cuenta ".
                "  FROM spi_cuentas ".
                " WHERE codemp='".$this->codemp."' ".
                "   AND spi_cuenta='".$cuenta."' ".
                "   AND status = 'C' ";

        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->valido=false;
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_verificar_existencia_spicuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
        }
        else
        {
            if($rs_data->EOF)
            {
                $this->valido=false;
                $this->io_mensajes->message("ERROR->La cuenta de Ingresos ".$cuenta.", No Existe en el plan de cuentas."); 
            }
            else
            {
                if ($tipo=="1")
                {
                    $this->cuentahaber = $rs_data->fields["sc_cuenta"];
                }
                else
                {
                    $this->cuentahaberiva = $rs_data->fields["sc_cuenta"];
                }
            }
        }
        return $this->valido;
    }// end function uf_verificar_existencia_spicuenta
    //-----------------------------------------------------------------------------------------------------------------------------------	

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_verificar_existencia_scgcuenta($cuenta)
    {
        $this->valido=true;;
        $ls_sql="SELECT sc_cuenta ".
                "  FROM scg_cuentas ".
                " WHERE codemp='".$this->codemp."' ".
                "   AND sc_cuenta='".$cuenta."' ".
                "   AND status = 'C' ";

        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->valido=false;
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_verificar_existencia_scgcuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
        }
        else
        {
            if($rs_data->EOF)
            {
                $this->valido=false;
                $this->io_mensajes->message("ERROR->La cuenta Contable ".$cuenta.", No Existe en el plan de cuentas."); 
            }
        }
        return $this->valido;
    }// end function uf_verificar_existencia_scgcuenta
    //-----------------------------------------------------------------------------------------------------------------------------------	

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_obtenerbancocuenta($codigo)
    {
        $pos = strpos($this->cuentasargus, $codigo);
        if ($pos === false)
        {
            $ls_sql="SELECT scb_ctabanco.codban, scb_ctabanco.ctaban, scb_ctabanco.sc_cuenta ".
                    "  FROM scb_ctabanco, scb_banco  ".
                    " WHERE scb_ctabanco.codemp='".$this->codemp."' ".
                    "   AND scb_ctabanco.codsargus=".$codigo." ".
                    "   AND scb_ctabanco.codemp = scb_banco.codemp ".
                    "   AND scb_ctabanco.codban = scb_banco.codban";

            $rs_data=$this->io_sql->select($ls_sql);
            if($rs_data===false)
            {
                $this->valido=false;
                $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->uf_obtenerbancocuenta ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
            }
            else
            {
                if($rs_data->EOF)
                {
                    $continuar=false;
                    $this->mensaje .= "La cuenta ".$codigo." en Sargus, no tiene relacion en sigesp.<br>"; 
                }
                else
                {
                    $continuar=true;
                    $this->codban=$rs_data->fields["codban"];
                    $this->ctaban=$rs_data->fields["ctaban"];
                    $this->cuentadebe=$rs_data->fields["sc_cuenta"];

                    $this->cuentasargus .= "-".$codigo;
                    $this->arrCuentaBanco[$codigo]["codban"]=$this->codban;
                    $this->arrCuentaBanco[$codigo]["ctaban"]=$this->ctaban;
                    $this->arrCuentaBanco[$codigo]["sc_cuenta"]=$this->cuentadebe;
                }
            }
        }
        else
        {
            $continuar=true;
            $this->codban=$this->arrCuentaBanco[$codigo]["codban"];
            $this->ctaban=$this->arrCuentaBanco[$codigo]["ctaban"];
            $this->cuentadebe=$this->arrCuentaBanco[$codigo]["sc_cuenta"];
            
        }
        return $continuar;
    }// end function uf_obtenerbancocuenta
    //-----------------------------------------------------------------------------------------------------------------------------------	

    //-----------------------------------------------------------------------------------------------------------------------------------
    function existeMovimientoBanco($codban, $ctaban, $comprobante, $operacion) 
    {
        $existe=false;
        $ls_sql = "SELECT numdoc ".
                  "  FROM scb_movbco ". 
                  " WHERE codemp='".$this->codemp."' ". 
                  "   AND codban='".$codban."' ". 
                  "   AND ctaban='".$ctaban."' ". 
                  "   AND numdoc='".$comprobante."'  ".
                  "   AND codope='".$operacion."' ";
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
            $this->valido=false;
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->existeMovimientoBanco ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message)); 
        }
        else
        {
            if(!$rs_data->EOF)
            {
                $existe=true;
                $this->mensaje .= "El movimiento de Banco ".$codban."-".$ctaban."-".$comprobante."-".$operacion.", ya existe en sigesp.<br>"; 
            }
        }
        return $existe;
    }// end function existeMovimientoBanco
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function actualizarEstatusFacturaOperacion($cod_factura,$nro_operacion,$estatus) 
    {
        $ls_sql ="UPDATE integracion ".
                "   SET estatus_integracion=".$estatus." ".
                " WHERE cod_factura=".$cod_factura." ";
                " WHERE nro_operacion IN ('".$nro_operacion."') ";
        
        $resultado = $this->io_sargus->io_sql->execute($ls_sql);
        if($resultado===false)
        {
            $this->io_mensajes->message("CLASE->Integracion SARGUS MÉTODO->actualizarEstatusFacturaOperacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sargus->io_sql->message));
            $this->valido=false;
        }
        return $this->valido;
    }// end function actualizarEstatusFacturaOperacion
    //-----------------------------------------------------------------------------------------------------------------------------------
    
}
?>
