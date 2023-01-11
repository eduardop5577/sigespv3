<?php
/***********************************************************************************
* @fecha de modificacion: 25/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_scb_c_conciliacionautomatica
{
    private $io_sql;
    public $io_msg;
    private $io_seguridad;
    private $io_excel;
    private $io_conalt;
    private $io_fecha; 
	
    public function __construct()
    {
        require_once("../base/librerias/php/general/sigesp_lib_sql.php");
        require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
        require_once("../base/librerias/php/general/sigesp_lib_include.php");
        require_once("../shared/class_folder/sigesp_c_seguridad.php");
        require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
        $io_include 		= new sigesp_include();
        $io_connect         = $io_include->uf_conectar();
        //$io_connect->debug  = true;
        $this->io_sql       = new class_sql($io_connect);
        $this->io_msg       = new class_mensajes();
        $this->io_seguridad = new sigesp_c_seguridad();
        $this->io_fecha     = new class_fecha();
        $this->io_conalt    = null;
        $this->codemp    = $_SESSION["la_empresa"]["codemp"];
    }
	
    public function uf_verificar_existe($codban,$ctaban,$mes,$periodo)
    {
        $ls_existe=0;
        $ls_sql = "SELECT codban ".
                  "  FROM scb_movimientoconciliar ".
                  " WHERE codban='{$codban}' ".
                  "   AND ctaban='{$ctaban}' ".
                  "   AND EXTRACT(MONTH FROM fecmov)='{$mes}' ".
                  "   AND EXTRACT(YEAR FROM fecmov)='{$periodo}' ";
        
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_verificar_existe ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        }
        else
        {
            if(!$rs_data->EOF)
            {            
               $ls_existe=1;
            }
        }
        
        return $ls_existe;
    }
    
    public function uf_verificar_conciliacioncerrada($codban,$ctaban,$mes,$periodo)
    {
        $ls_cerrada=0;
        $ls_sql = "SELECT codban ".
                  "  FROM scb_conciliacion ".
                  " WHERE codban='{$codban}' ".
                  "   AND ctaban='{$ctaban}' ".
                  "   AND mesano='{$mes}{$periodo}' ".
                  "   AND estcon=1 ";
        
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_verificar_conciliacioncerrada ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        }
        else
        {
            if(!$rs_data->EOF)
            {            
               $ls_cerrada=1;
            }
        }
        
        return $ls_cerrada;
    }    

    public function uf_eliminar_movimientoconciliar($codban,$ctaban,$mes,$periodo)
    {
        $lb_valido=true;
        $ls_sql = "DELETE ".
                  "  FROM scb_movimientoconciliar ".
                  " WHERE codban='{$codban}' ".
                  "   AND ctaban='{$ctaban}' ".
                  "   AND EXTRACT(MONTH FROM fecmov)='{$mes}' ".
                  "   AND EXTRACT(YEAR FROM fecmov)='{$periodo}' ";
        
        $rs_data=$this->io_sql->execute($ls_sql);
        if($rs_data===false)
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_eliminar_movimientoconciliar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        }
        
        if($lb_valido)
        {
            $ls_sql = "DELETE ".
                      "  FROM scb_movnobanco ".
                      " WHERE codban='{$codban}' ".
                      "   AND ctaban='{$ctaban}' ".
                      "   AND EXTRACT(MONTH FROM fecmov)='{$mes}' ".
                      "   AND EXTRACT(YEAR FROM fecmov)='{$periodo}' ";

            $rs_data=$this->io_sql->execute($ls_sql);
            if($rs_data===false)
            {
                    $lb_valido=false;
                    $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_eliminar_movimientoconciliar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            }
        }

        if($lb_valido)
        {
            $ls_sql = "DELETE ".
                      "  FROM scb_movnolibro ".
                      " WHERE codban='{$codban}' ".
                      "   AND ctaban='{$ctaban}' ".
                      "   AND EXTRACT(MONTH FROM fecmov)='{$mes}' ".
                      "   AND EXTRACT(YEAR FROM fecmov)='{$periodo}' ";

            $rs_data=$this->io_sql->execute($ls_sql);
            if($rs_data===false)
            {
                    $lb_valido=false;
                    $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_eliminar_movimientoconciliar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
            }
        }
        return $lb_valido;
    }

    public function uf_buscar_configuracion_archivo($codigo)
    {
        $ls_sql = "SELECT * FROM scb_archivoconciliacion WHERE codarc='{$codigo}'";
        
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_buscar_configuracion_archivo ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        }
        
        return $rs_data;
    }
	
    public function uf_abrir_archivo($as_nombrearchivo,$ao_archivo)
    {
        $lb_valido=true;
        if (file_exists("$as_nombrearchivo"))
        {
                $ao_archivo=@file("$as_nombrearchivo");
        }
        else
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_abrir_archivo ERROR->el archivo no existe."); 
        }
        $arrResultado['ao_archivo']=$ao_archivo;
        $arrResultado['lb_valido']=$lb_valido;
        return $arrResultado;
    }

    public function uf_load_configuracion_campos($as_codarc,$ai_totrows,$ao_object)
    {
        $lb_valido=true;
        $ls_sql="SELECT codcam, inicam, loncam, colcam, camrel, forcam, cricam ".
                "  FROM scb_dt_archivoconciliacion".
                " WHERE codemp='".$this->codemp."'".	
                "   AND codarc = '".$as_codarc."' ".	
                " ORDER BY codcam, inicam ";
        $rs_data=$this->io_sql->select($ls_sql);
        if($rs_data===false)
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_load_configuracion_campos ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        }
        else
        {
                $ai_totrows=0;
                while(!$rs_data->EOF)
                {
                        $ai_totrows++;
                        $li_codcam=$rs_data->fields["codcam"];
                        $li_inicam=$rs_data->fields["inicam"];
                        $li_loncam=$rs_data->fields["loncam"];
                        $ls_colcam=$rs_data->fields["colcam"];
                        $ls_camrel=$rs_data->fields["camrel"];
                        $ls_forcam=$rs_data->fields["forcam"];
                        $ls_cricam=$rs_data->fields["cricam"];
                        $ao_object["codcam"][$ai_totrows]=$li_codcam;
                        $ao_object["inicam"][$ai_totrows]=$li_inicam;
                        $ao_object["loncam"][$ai_totrows]=$li_loncam;
                        $ao_object["colcam"][$ai_totrows]=$ls_colcam;
                        $ao_object["camrel"][$ai_totrows]=$ls_camrel;
                        $ao_object["forcam"][$ai_totrows]=$ls_forcam;
                        $ao_object["cricam"][$ai_totrows]=$ls_cricam;
                        $rs_data->MoveNext();
                }
                $this->io_sql->free_result($rs_data);
        }
        $arrResultado['ai_totrows']=$ai_totrows;
        $arrResultado['ao_object']=$ao_object;
        $arrResultado['lb_valido']=$lb_valido;
        return $arrResultado;
    }
        
    public function uf_existe_MovimientoConciliar($codban, $ctaban, $numref, $fecmov, $tipmov) 
    {
        $existe = false;
        $cadenaSQL = "SELECT codban ".
                     "  FROM scb_movimientoconciliar ".
                     "  WHERE codban ='{$codban}' ".
                     "    AND ctaban = '{$ctaban}' ".
                     "    AND numref = '{$numref}' ".
                     "    AND fecmov = '{$fecmov}' ".
                     "    AND tipmov='{$tipmov}'";
        $resultado = $this->io_sql->execute($cadenaSQL);
        if ($resultado->_numOfRows > 0)
        {
            $existe = true;
        }
        return $existe;
    }
    
    public function uf_cargar_estado_cuenta($archivo, $codarc, $codban, $ctaban, $saldoInicial, $mes, $periodo)
    {
        $ls_nombrearchivo=$archivo;
        $lo_archivo="";
        $arrResultado=$this->uf_abrir_archivo($ls_nombrearchivo,$lo_archivo);
        $lo_archivo=$arrResultado['ao_archivo'];
        $lb_valido=$arrResultado['lb_valido'];
        $li_totrows=0;
        $lo_object="";
        if($lb_valido)
        {
            $dataConf = $this->uf_buscar_configuracion_archivo($codarc);
            $tiparc = $dataConf->fields['tiparc'];
            $separc = $dataConf->fields['separc'];
            $filiniarc  = $dataConf->fields['filiniarc'];
            $ndequarc  = $dataConf->fields['ndequarc'];
            $ncequarc  = $dataConf->fields['ncequarc'];
            $chequarc  = $dataConf->fields['chequarc'];
            $dpequarc  = $dataConf->fields['dpequarc'];
            $rtequarc  = $dataConf->fields['rtequarc'];
            $saldoInicial = str_replace('.', '', $saldoInicial);
            $saldoInicial = str_replace(',', '.', $saldoInicial);

            $arrResultado=$this->uf_load_configuracion_campos($codarc,$li_totrows,$lo_object);
            $li_totrows=$arrResultado['ai_totrows'];
            $lo_object=$arrResultado['ao_object'];
            $lb_valido=$arrResultado['lb_valido'];	
            
            if($lb_valido)
            {
                $contRegistros = 0;
                $contInsert    = 0;
                $contError     = 0;
                $saldoBanco    = 0;
                if (($tiparc == '0')||(($tiparc == '1'))) //ARCHIVO TXT 
                {
                    $lb_valido=true;
                    $li_total=count((array)$lo_archivo);
                    for($li_i=$filiniarc;($li_i<$li_total);$li_i++)
                    {
                        $valNumdoc = "";
                        $valTipdoc = "";
                        $valFecha  = "1900-01-01";
                        $valMonto = 0;
                        for($li_z=1;($li_z<=$li_totrows);$li_z++)
                        {
                            $li_codcam=$lo_object["codcam"][$li_z];
                            $li_inicam=$lo_object["inicam"][$li_z];
                            $li_loncam=$lo_object["loncam"][$li_z];
                            $ls_camrel=$lo_object["camrel"][$li_z];
                            $ls_forcam=ltrim(rtrim($lo_object["forcam"][$li_z]));
                            $ls_cricam=ltrim(rtrim($lo_object["cricam"][$li_z]));
                            if ($tiparc == '0')
                            {
                                $ls_campo=substr($lo_archivo[$li_i],$li_inicam,$li_loncam);
                                $ls_campo= str_replace('"', '', $ls_campo);
                                $ls_campo= str_replace("'", "", $ls_campo);
                            }
                            else
                            {
                                $ls_campos = explode($separc,$lo_archivo[$li_i]);
                                $ls_campo = $ls_campos[$li_z-1];
                                $ls_campo= str_replace('"', '', $ls_campo);
                                $ls_campo= str_replace("'", "", $ls_campo);
                            }
                            if(($ls_camrel=="monto")||($ls_camrel=="cargo")||($ls_camrel=="abono"))
                            {
                                $ls_campo = str_replace(".", "", $ls_campo);
                                $ls_campo = str_replace(",", ".", $ls_campo);
                                $ls_campo=number_format($ls_campo,2,".","");
                            }
                            if($ls_cricam!="")
                            {
                                if(($ls_camrel=="monto")||($ls_camrel=="cargo")||($ls_camrel=="abono"))
                                {
                                    $ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
                                    $ls_campo=$this->io_eval->uf_evaluar_formula($ls_cricam,$ls_campo);
                                }
                                else
                                {
                                    $ls_campo="'".ltrim(rtrim($ls_campo))."'";
                                    $ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
                                    $ls_campo=@eval(" return $ls_cricam;");
                                }
                            }
                            if($ls_camrel=="numdoc")
                            {
                                $valNumdoc = str_pad($ls_campo, 15, 0, 0);
                            }
                            if(($ls_camrel=="fecmov")&&($valFecha=='1900-01-01'))
                            {
                                $arrFecha=explode( "/",$ls_campo);
                                if (count((array)$arrFecha)>0)
                                {
                                    $dia= str_pad($arrFecha[0],2,"0",STR_PAD_LEFT);
                                    $mes=str_pad($arrFecha[1],2,"0",STR_PAD_LEFT);
                                    $anio=$arrFecha[2];
                                    $ls_campo=$dia."/".$mes."/".$anio;
                                }
                                else
                                {
                                    $arrFecha=explode( "-",$ls_campo);
                                    if (count((array)$arrFecha)>0)
                                    {
                                        $dia= str_pad($arrFecha[0],2,"0",STR_PAD_LEFT);
                                        $mes=str_pad($arrFecha[1],2,"0",STR_PAD_LEFT);
                                        $anio=$arrFecha[2];
                                        $ls_campo=$dia."-".$mes."-".$anio;
                                    }                                    
                                }
                                $valFecha = $this->io_fecha->uf_convert_date_to_db($ls_campo);
                            }
                            if(($ls_camrel=="fecmovs")&&($valFecha=='1900-01-01'))
                            {   
                                $dia= substr($ls_campo,0,2);
                                $mes=substr($ls_campo,2,2);
                                $anio=substr($ls_campo,4,4);
                                $ls_campo=$dia."-".$mes."-".$anio;
                                $valFecha = $this->io_fecha->uf_convert_date_to_db($ls_campo);
                            }
                            if(($ls_camrel=="dia")&&($valFecha=='1900-01-01'))
                            {
                                $dia= str_pad($ls_campo,2,"0",STR_PAD_LEFT);
                                $mes=str_pad($mes,2,"0",STR_PAD_LEFT);
                                $ls_campo=$dia."-".$mes."-".$periodo;
                                $valFecha = $this->io_fecha->uf_convert_date_to_db($ls_campo);
                            }
                            if(($ls_camrel=="monto")&&($valMonto==0))
                            {
                                $valMonto = $ls_campo;
                            }
                            if(($ls_camrel=="cargo")&&($valMonto==0))
                            {
                                $valMonto = $ls_campo*(-1);
                            }
                            if(($ls_camrel=="abono")&&($valMonto==0))
                            {
                                $valMonto = $ls_campo;
                            }
                            if($ls_camrel=="codope")
                            {
                                if (trim($dpequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'DP';
                                }
                                elseif (trim($ncequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'NC';
                                }
                                elseif (trim($ndequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'ND';
                                }
                                elseif (trim($chequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'CH';
                                }
                                elseif (trim($rtequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'RE';
                                }
                            }            
                        }
                        if ($valTipdoc=='')
                        {
                            if ($valMonto>=0)
                            {
                                $valTipdoc='NC';
                            }
                            else
                            {
                                $valTipdoc='ND'; 
                            }
                        }    
                        $valMonto = abs($valMonto);                        
                        if ($valMonto<>0)
                        {
                            if ($this->uf_existe_MovimientoConciliar($codban, $ctaban, $valNumdoc, $valFecha, $valTipdoc))
                            {
                                $contador=1;
                                $existe=true;
                                while ($existe)
                                {
                                    $valNumdoc = $valNumdoc."-".$contador;
                                    $existe=$this->uf_existe_MovimientoConciliar($codban, $ctaban, $valNumdoc, $valFecha, $valTipdoc);
                                    $contador++;
                                }
                            }

                            $cadenaSQL = "INSERT INTO scb_movimientoconciliar(codban, ctaban, numref, fecmov, tipmov, monto) ".
                                         "     VALUES ('{$codban}', '{$ctaban}', '{$valNumdoc}', '{$valFecha}', '{$valTipdoc}', {$valMonto})";
                            $respuesta = $this->io_sql->execute($cadenaSQL);
                            if($respuesta === false)
                            {
                                    $ls_cadena .= "Ocurrio un error en la linea ".$contRegistros." Error BD:".$this->io_sql->conn->ErrorMsg()." .\r\n";
                                    $contError++;
                            }
                            else
                            {
                                    $contInsert++;
                            }
                        }
                        $contRegistros++;
                    }
                }
                
                if ($tiparc == '2') //ARCHIVO EXCEL 
                {
                    require_once("../base/librerias/php/readexcel/reader.php");
                    $this->io_excel = new Spreadsheet_Excel_Reader();
                    $this->io_excel->setOutputEncoding("CP1251");
                    $this->io_excel->read($archivo);
                    $lb_valido=true;
                    for($li_indexfil=$filiniarc;($li_indexfil<=$this->io_excel->sheets[0]['numRows']);$li_indexfil++)
                    {
                        $valNumdoc = "";
                        $valTipdoc = "";
                        $valFecha  = "1900-01-01";
                        $valMonto = 0;
                        for($li_z=1;($li_z<=$li_totrows);$li_z++)
                        {
                            $li_codcam=$lo_object["codcam"][$li_z];
                            $li_colcam=number_format($lo_object["colcam"][$li_z],0,"","");
                            $ls_camrel=$lo_object["camrel"][$li_z];
                            $ls_forcam=ltrim(rtrim($lo_object["forcam"][$li_z]));
                            $ls_cricam=ltrim(rtrim($lo_object["cricam"][$li_z]));
                            $ls_campo = trim($this->io_excel->sheets[0]['cells'][$li_indexfil][$li_colcam]);
                            if(($ls_camrel=="monto")||($ls_camrel=="cargo")||($ls_camrel=="abono"))
                            {
                                $posPunto = strpos($ls_campo, ".");
                                $posComa = strpos($ls_campo, ",");
                                if ($posComa>0)
                                {
                                    if ($posPunto>0)
                                    {
                                        $ls_campo= str_replace(".","", $ls_campo);
                                        $ls_campo= str_replace(",",".", $ls_campo);
                                    }
                                    else
                                    {
                                        $ls_campo= str_replace(",",".", $ls_campo);
                                    }
                                }
                                $ls_campo=number_format($ls_campo,2,".","");
                            }
                            if($ls_cricam!="")
                            {
                                if(($ls_camrel=="monto")||($ls_camrel=="cargo")||($ls_camrel=="abono"))
                                {
                                    $ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
                                    $ls_campo=$this->io_eval->uf_evaluar_formula($ls_cricam,$ls_campo);
                                }
                                else
                                {
                                    $ls_campo="'".ltrim(rtrim($ls_campo))."'";
                                    $ls_cricam=str_replace("campo",$ls_campo,$ls_cricam);
                                    $ls_campo=@eval(" return $ls_cricam;");
                                }
                            }
                            if($ls_camrel=="numdoc")
                            {
                                $valNumdoc = str_pad($ls_campo, 15, 0, 0);
                            }
                            if(($ls_camrel=="fecmov")&&($valFecha=='1900-01-01'))
                            {
                                $arrFecha=explode( "/",$ls_campo);
                                if (count((array)$arrFecha)>0)
                                {
                                    $dia= str_pad($arrFecha[0],2,"0",STR_PAD_LEFT);
                                    $mes=str_pad($arrFecha[1],2,"0",STR_PAD_LEFT);
                                    $anio=$arrFecha[2];
                                    $ls_campo=$dia."/".$mes."/".$anio;
                                }
                                else
                                {
                                    $arrFecha=explode( "-",$ls_campo);
                                    if (count((array)$arrFecha)>0)
                                    {
                                        $dia= str_pad($arrFecha[0],2,"0",STR_PAD_LEFT);
                                        $mes=str_pad($arrFecha[1],2,"0",STR_PAD_LEFT);
                                        $anio=$arrFecha[2];
                                        $ls_campo=$dia."-".$mes."-".$anio;
                                    }                                    
                                }
                                $valFecha = $this->io_fecha->uf_convert_date_to_db($ls_campo);
                            }
                            if(($ls_camrel=="fecmovs")&&($valFecha=='1900-01-01'))
                            {   
                                $dia= substr($ls_campo,0,2);
                                $mes=substr($ls_campo,2,2);
                                $anio=substr($ls_campo,4,4);
                                $ls_campo=$dia."-".$mes."-".$anio;
                                $valFecha = $this->io_fecha->uf_convert_date_to_db($ls_campo);
                            }
                            if(($ls_camrel=="dia")&&($valFecha=='1900-01-01'))
                            {
                                $dia= str_pad($ls_campo,2,"0",STR_PAD_LEFT);
                                $mes=str_pad($mes,2,"0",STR_PAD_LEFT);
                                $ls_campo=$dia."-".$mes."-".$periodo;
                                $valFecha = $this->io_fecha->uf_convert_date_to_db($ls_campo);
                            }
                            if(($ls_camrel=="monto")&&($valMonto==0))
                            {
                                $valMonto = $ls_campo;
                                
                            }
                            if(($ls_camrel=="cargo")&&($valMonto==0))
                            {
                                $valMonto = $ls_campo*(-1);
                            }
                            if(($ls_camrel=="abono")&&($valMonto==0))
                            {
                                $valMonto = $ls_campo;
                            }
                            if($ls_camrel=="codope")
                            {
                                if (trim($dpequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'DP';
                                }
                                elseif (trim($ncequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'NC';
                                }
                                elseif (trim($ndequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'ND';
                                }
                                elseif (trim($chequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'CH';
                                }
                                elseif (trim($rtequarc) == trim($ls_campo))
                                {
                                    $valTipdoc = 'RE';
                                }
                            }         
                        }
                        if ($valTipdoc=='')
                        {
                            if ($valMonto>=0)
                            {
                                $valTipdoc='NC';
                            }
                            else
                            {
                                $valTipdoc='ND'; 
                            }
                        }    
                        $valMonto = abs($valMonto);
                        if ($valMonto<>0)
                        {
                            if ($this->uf_existe_MovimientoConciliar($codban, $ctaban, $valNumdoc, $valFecha, $valTipdoc))
                            {
                                $contador=1;
                                $existe=true;
                                while ($existe)
                                {
                                    $valNumdoc = $contador.substr($valNumdoc, 1, 14);
                                    $existe=$this->uf_existe_MovimientoConciliar($codban, $ctaban, $valNumdoc, $valFecha, $valTipdoc);
                                    $contador++;
                                }
                            }

                            $cadenaSQL = "INSERT INTO scb_movimientoconciliar(codban, ctaban, numref, fecmov, tipmov, monto) ".
                                         "     VALUES ('{$codban}', '{$ctaban}', '{$valNumdoc}', '{$valFecha}', '{$valTipdoc}', {$valMonto})";
                            $respuesta = $this->io_sql->execute($cadenaSQL);
                            if($respuesta === false)
                            {
                                    $ls_cadena .= "Ocurrio un error en la linea ".$contRegistros." Error BD:".$this->io_sql->conn->ErrorMsg()." .\r\n";
                                    $contError++;
                            }
                            else
                            {
                                    $contInsert++;
                            }
                        }
                        $contRegistros++;
                    }
                }
            }
            $this->io_msg->message('La lectura del estado de cuenta finalizo.');
            unset($dataConf);                        
        }
    }

    public function uf_obtener_movimiento_conciliar($codban, $ctaban, $fechasta)
    {
        $cadenaSQL = "SELECT MOVCON.codban, MOVCON.ctaban, MOVCON.numref, MOVCON.tipmov, MOVCON.monto ". 
                     "  FROM scb_movimientoconciliar MOVCON ".
                     " INNER JOIN scb_movbco MOVBCO ".
                     "    ON MOVCON.codban=MOVBCO.codban ".
                     "   AND MOVCON.ctaban=MOVBCO.ctaban ".
                     "   AND MOVCON.numref=MOVBCO.numdoc ".
                     "   AND MOVCON.tipmov=MOVBCO.codope ".
                     "   AND MOVCON.monto=(MOVBCO.monto-MOVBCO.monret) ".
                     " WHERE MOVCON.codban='{$codban}' ".
                     "   AND MOVCON.ctaban='{$ctaban}' ".
                     "   AND MOVCON.estcon = '0' ".
                     "   AND MOVCON.fecmov <='{$fechasta}' ".
                     "   AND estmov <>'N' ".
                     "   AND (feccon='1900-01-01' AND MOVBCO.fecmov<='{$fechasta}') ";
        $rs_data=$this->io_sql->execute($cadenaSQL);
        if($rs_data===false)
        {
                $lb_valido=false;
                $this->io_msg->message("CLASE->Conciliacion Automatica MÉTODO->uf_obtener_movimiento_conciliar ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
        }
                     
        return $rs_data;
    }
    
    public function uf_actualizar_movbco_estcon($codban, $ctaban, $numref, $tipmov, $fechasta)
    {
        $actualizo = true;
        $mensajeError = '';
        $arrRespuesta = array();
        $cadenaSQL = "UPDATE scb_movbco ".
                     "   SET estcon = '1', feccon='{$fechasta}' ".
                     " WHERE codban='{$codban}' ".
                     "   AND ctaban='{$ctaban}' ".
                     "   AND numdoc='{$numref}' ".
                     "   AND codope='{$tipmov}'";
        if ($this->io_sql->execute($cadenaSQL) === false)
        {
                $actualizo = false;
                $mensajeError .= "Ocurrio un error actualizando estatus de conciliacion en movbco, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n";
        }

        $arrRespuesta['exito'] = $actualizo;
        $arrRespuesta['mensajeError'] = $mensajeError;
        return $arrRespuesta;
    }
    
    public function uf_actualizar_movcon_estcon($codban, $ctaban, $numref, $tipmov)
    {
        $actualizo = true;
        $mensajeError = '';
        $arrRespuesta = array();
        $cadenaSQL = "UPDATE scb_movimientoconciliar ".
                     "   SET estcon = '1' ".
                     " WHERE codban='{$codban}' ".
                     "   AND ctaban='{$ctaban}' ".
                     "   AND numref='{$numref}' ".
                     "   AND tipmov='{$tipmov}' ";
        if ($this->io_sql->execute($cadenaSQL) === false)
        {
            $actualizo = false;
            $mensajeError .= "Ocurrio un error actualizando estatus de conciliacion en movimientoconciliar, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n";
        }

        $arrRespuesta['exito'] = $actualizo;
        $arrRespuesta['mensajeError'] = $mensajeError;
        
        return $arrRespuesta;
    }
    
    public function uf_calcular_saldo_libro($codban,$ctaban,$fecha)
    {
        $saldoDebe = 0;
        $saldoHaber = 0;
        $cadSQLDeb = "SELECT SUM(monto - monret) As mondeb, estmov ".
                     "  FROM scb_movbco ".
                     " WHERE codban='{$codban}' ".
                     "   AND ctaban='{$ctaban}' ".
                     "   AND (codope='NC' OR codope='DP') ".
                     "   AND ((estreglib IS NULL) or estreglib<>'A') ".
                     "   AND codemp='{$_SESSION['la_empresa']['codemp']}' ".
                     "   AND fecmov<='{$fecha}' ".
                     " GROUP BY estmov";
        $resDebe = $this->io_sql->execute($cadSQLDeb);
        while (!$resDebe->EOF)
        {
            if ($resDebe->fields['estmov'] != 'A')
            {
                $saldoDebe = number_format($saldoDebe + $resDebe->fields['mondeb'], 2, ".", "");
            }
            else
            {
                $saldoDebe = number_format($saldoDebe - $resDebe->fields['mondeb'], 2, ".", "");
            }
            $resDebe->MoveNext();
        }
        unset($resDebe);

        $cadSQLHab = "SELECT SUM(monto - monret) As monhab,estmov ".
                     "  FROM  scb_movbco ".
                     " WHERE codban='{$codban}' ".
                     "   AND ctaban='{$ctaban}' ".
                     "   AND (codope='RE' OR codope='ND' OR codope='CH') ".
                     "   AND ((estreglib IS NULL) or estreglib<>'A') ".
                     "   AND codemp='{$_SESSION['la_empresa']['codemp']}' ".
                     "   AND fecmov<='{$fecha}' ".
                     " GROUP BY estmov";

        $resHaber = $this->io_sql->execute($cadSQLHab);
        while (!$resHaber->EOF)
        {
            if ($resHaber->fields['estmov'] != 'A')
            {
                $saldoHaber = number_format( $saldoHaber + $resHaber->fields['monhab'], 2, ".", "");
            }
            else
            {
                $saldoHaber = number_format($saldoHaber - $resHaber->fields['monhab'], 2, ".", "");
            }
            $resHaber->MoveNext();
        }
        unset($resHaber);

        $saldoLibro = number_format($saldoDebe - $saldoHaber, 2, ".", "");
        return $saldoLibro;
    }
    
    public function uf_existe_MovnoLibro($codban, $ctaban, $numref, $fecmov, $tipmov) 
    {
        $existe = false;
        $cadenaSQL = "SELECT codban ".
                     "  FROM scb_movnolibro ".
                     "  WHERE codban ='{$codban}' ".
                     "    AND ctaban = '{$ctaban}' ".
                     "    AND numref = '{$numref}' ".
                     "    AND fecmov = '{$fecmov}' ".
                     "    AND tipmov='{$tipmov}'";
        $resultado = $this->io_sql->execute($cadenaSQL);
        if ($resultado->_numOfRows > 0)
        {
            $existe = true;
        }
        return $existe;
    }
    
    public function procesar_movnolibro($codban, $ctaban, $fechasta)
    {
        $arrResultado = array();
	$contNoLibro  = 0;
	$saldoNoLibro = 0;
	$cadenaError  = '';
        $cadenaMovNoLibro = '';
        
        $cadenaSQL = "SELECT MOVCON.*, MOVBCO.numdoc ".
                     "  FROM scb_movimientoconciliar MOVCON ".
		     "  LEFT OUTER JOIN scb_movbco MOVBCO ".
                     "    ON MOVCON.codban=MOVBCO.codban ".
                     "   AND MOVCON.ctaban=MOVBCO.ctaban ".
                     "   AND MOVCON.numref=MOVBCO.numdoc ".
                     "   AND MOVCON.tipmov=MOVBCO.codope ".
                     "   AND MOVCON.monto=(MOVBCO.monto-MOVBCO.monret) ".
		     " WHERE MOVCON.codban='{$codban}' ".
                     "   AND MOVCON.ctaban='{$ctaban}' ".
                     "   AND MOVCON.estcon = '0' ".
		     "   AND MOVCON.fecmov <='{$fechasta}'";
        $dataNolibro = $this->io_sql->execute($cadenaSQL);
        while (!$dataNolibro->EOF)
        {
            if ($dataNolibro->fields['numdoc'] == NULL)
            {
                $numref = $dataNolibro->fields['numref'];
                $fecmov = $dataNolibro->fields['fecmov'];
                $tipmov = $dataNolibro->fields['tipmov'];
                $monto  = $dataNolibro->fields['monto'];

                if (!$this->uf_existe_MovnoLibro($codban, $ctaban, $numref, $fecmov, $tipmov))
                {
                    $insertSQL = "INSERT INTO scb_movnolibro(codban, ctaban, numref, fecmov, tipmov, monto) ".
                                 " VALUES ('{$codban}', '{$ctaban}', '{$numref}', '{$fecmov}', '{$tipmov}', {$monto})"; 
                    if ($this->io_sql->execute($insertSQL) === false)
                    {
                        $cadenaError .= "Ocurrio un insertando un movimiento no registrado en libro, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n"; 
                    }
                    else
                    {
                        $contNoLibro++;
                        $saldoNoLibro = $saldoNoLibro + $monto; 
                        $cadenaMovNoLibro .= " ".$numref." ".$fecmov." ".$tipmov." ".$monto."  \r\n";
                    }
                }
            }				
            $dataNolibro->MoveNext();
        }
        unset($dataNolibro);

        $arrResultado['contNoLibro']  = $contNoLibro;
        $arrResultado['saldoNoLibro'] = $saldoNoLibro;
        $arrResultado['cadenaError']  = $cadenaError;
        $arrResultado['cadenaMovNoLibro']  = $cadenaMovNoLibro;

        return $arrResultado;
    }
    
    public function uf_existe_MovnoBanco($codban, $ctaban, $numref, $fecmov, $tipmov) 
    {
        $existe = false;
        $cadenaSQL = "SELECT codban ".
                     "  FROM scb_movnobanco ".
                     "  WHERE codban ='{$codban}' ".
                     "    AND ctaban = '{$ctaban}' ".
                     "    AND numref = '{$numref}' ".
                     "    AND fecmov = '{$fecmov}' ".
                     "    AND tipmov='{$tipmov}'";
        $resultado = $this->io_sql->execute($cadenaSQL);
        if ($resultado->_numOfRows > 0)
        {
            $existe = true;
        }
        return $existe;
    }
    
    public function procesar_movnobanco($codban, $ctaban, $fechasta)
    {
        $arrResultado = array();
        $contNoBanco  = 0;
        $saldoNoBanco = 0;
        $cadenaError  = '';
        $cadenaMovNoBanco = '';
        
        $cadenaSQL = "SELECT MOVCON.numref, MOVBCO.numdoc, MOVBCO.fecmov, MOVBCO.codope, MOVBCO.monto ".
                     "  FROM scb_movimientoconciliar MOVCON ".
                     " RIGHT OUTER JOIN scb_movbco MOVBCO ".
                     "    ON MOVCON.codban=MOVBCO.codban ".
                     "   AND MOVCON.ctaban=MOVBCO.ctaban ".
                     "   AND MOVCON.numref=MOVBCO.numdoc ".
                     "   AND MOVCON.tipmov=MOVBCO.codope ".
                     "   AND MOVCON.monto=(MOVBCO.monto-MOVBCO.monret) ".
                     " WHERE MOVBCO.codban='{$codban}' ".
                     "   AND MOVBCO.ctaban='{$ctaban}' ".
                     "   AND estmov <>'N' ".
                     "   AND (MOVBCO.feccon='1900-01-01' AND MOVBCO.fecmov<='{$fechasta}')";
        $dataNoBanco = $this->io_sql->execute($cadenaSQL);
        while (!$dataNoBanco->EOF)
        {
            if ($dataNoBanco->fields['numref'] == NULL)
            {
                $numref = $dataNoBanco->fields['numdoc'];
                $fecmov = $dataNoBanco->fields['fecmov'];
                $tipmov = $dataNoBanco->fields['codope'];
                $monto  = $dataNoBanco->fields['monto'];
                
                if ($this->uf_existe_MovnoBanco($codban, $ctaban, $numref, $fecmov, $tipmov))
                {
                    $insertSQL = "INSERT INTO scb_movnobanco(codban, ctaban, numref, fecmov, tipmov, monto) ".
                                 " VALUES ('{$codban}', '{$ctaban}', '{$numref}', '{$fecmov}', '{$tipmov}', {$monto})";
                    if ($this->io_sql->execute($insertSQL) === false)
                    {
                        $cadenaError .= "Ocurrio un error insertando un movimiento no registrado en banco, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n";
                    }
                    else
                    {
                        $contNoBanco++;
                        $saldoNoBanco = $saldoNoBanco + $monto;
                        $cadenaMovNoBanco .= " ".$numref." ".$fecmov." ".$tipmov." ".$monto."  \r\n";
                    }
                }
            }
            $dataNoBanco->MoveNext();
        }
        unset($dataNoBanco);

        $arrResultado['contNoBanco']  = $contNoBanco;
        $arrResultado['saldoNoBanco'] = $saldoNoBanco;
        $arrResultado['cadenaError']  = $cadenaError;
        $arrResultado['cadenaMovNoBanco']  = $cadenaMovNoBanco;

        return $arrResultado;
    }
    
    public function uf_buscar_cabecera_conciliacion($codban, $ctaban, $mesano)
    {
        $existe = false;
        $cadenaSQL = "SELECT estcon ".
                     "  FROM scb_conciliacion ".
                     " WHERE codemp = '{$_SESSION['la_empresa']['codemp']}' ".
                     "   AND codban ='{$codban}' ".
                     "   AND ctaban = '{$ctaban}' ".
                     "   AND mesano='{$mesano}'";
        $resultado = $this->io_sql->execute($cadenaSQL);
        if ($resultado->_numOfRows > 0)
        {
            $existe = true;
        }

        return $existe;
    }
    
    public function uf_guardar_cabecera_conciliacion($codban, $ctaban, $mesano, $saldoConcilia, $saldoBanco, $saldoLibro)
    {
        $guardo = true;
        $arrRespuesta = array();
        $mensajeError = '';
        $mesano = str_replace("/","",$mesano);

        if (!$this->uf_buscar_cabecera_conciliacion($codban, $ctaban, $mesano))
        {
            $cadenaSQL = "INSERT INTO scb_conciliacion(codemp,codban,ctaban,salseglib,salsegbco,conciliacion,mesano,estcon) ".
                         " VALUES ('{$_SESSION['la_empresa']['codemp']}','{$codban}','{$ctaban}',{$saldoLibro},{$saldoBanco},{$saldoConcilia},'{$mesano}',0)";
        }
        else
        {
            $cadenaSQL = "UPDATE scb_conciliacion ".
                         "   SET salseglib={$saldoLibro}, ".
                         "       salsegbco={$saldoBanco}, ".
                         "       conciliacion={$saldoConcilia}, ".
                         "       mesano='{$mesano}' ".
                         " WHERE codban='{$codban}' ".
                         "   AND ctaban='{$ctaban}' ".
                         "   AND codemp='{$_SESSION['la_empresa']['codemp']}' ".
                         "   AND mesano='{$mesano}'";
        }

        if ($this->io_sql->execute($cadenaSQL) === false)
        {
            $guardo = false;
            $mensajeError = "Ocurrio un error guardando la cabecera de la conciliacion, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n";
        }

        $arrRespuesta['exito'] = $guardo;
        $arrRespuesta['mensajeError'] = $mensajeError;

        return $arrRespuesta;
    }
    
    public function uf_conciliar_movimientos($codban, $denban, $ctaban, $mesano, $saldoBanco)
    {
        //CREANDO DIRECTORIO Y ARCHIVO DE RESULTADO
        $directorioResultado = 'resultado_conciliacion';
        if(!file_exists ($directorioResultado))
        {
                $exito = mkdir($directorioResultado,0777);
                if(!$exito)
                {
                        $this->io_msg->message('No se pudo crear la capeta de resultados');
                        return false;
                }
        }
        $nombrearchivo=$directorioResultado.'/resultado_conciliacion_'.$codban.'_'.$ctaban.'_'.str_replace("/","",$mesano).'.txt';
        if (file_exists("$nombrearchivo"))
        {
            if(@unlink("$nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
            {
                $lb_valido = false;
            }
        }
        $arcResultado = @fopen($nombrearchivo,"a+");

        //INICILIZANDO VARIABLES
        $cadenaError   = '';
        $contRegistros = 0;
        $contConci     = 0;
        $saldoConcilia = 0;
        $saldoLibro    = 0;

        $mes  = substr($mesano,0,2);
        $anno = substr($mesano,3,4);
        $fechasta = $this->io_fecha->uf_last_day($mes, $anno);
        $fechasta = $this->io_fecha->uf_convert_date_to_db($fechasta);
        $fechacon = $anno.'-'.$mes.'-01';
        
        //INICIANDO PROCESO
        $dataConcilian = $this->uf_obtener_movimiento_conciliar($codban, $ctaban, $fechasta);
        if ($dataConcilian!==false)
        {
            while (!$dataConcilian->EOF)
            {
                $codban = $dataConcilian->fields['codban'];
                $ctaban = $dataConcilian->fields['ctaban'];
                $numref = $dataConcilian->fields['numref'];
                $tipmov = $dataConcilian->fields['tipmov'];
                $monto = $dataConcilian->fields['monto'];
                $arrResActMovbco = $this->uf_actualizar_movbco_estcon($codban, $ctaban, $numref, $tipmov, $fechacon); 
                if ($arrResActMovbco['exito'])
                {
                    $arrResActMovcon = $this->uf_actualizar_movcon_estcon($codban, $ctaban, $numref, $tipmov);
                    if($arrResActMovcon['exito'])
                    {
                        $contConci++;
                        $saldoConcilia = $saldoConcilia + $monto;
                    }
                    else
                    {
                        $cadenaError .= $arrResActMovcon['mensajeError'];
                    }
                }
                else
                {
                    $cadenaError .= $arrResActMovbco['mensajeError'];
                }
                $dataConcilian->MoveNext();
            }
        }
        else
        {
            $cadenaError .= "Ocurrio un error obteniendo los movimientos que concilian, Error SQL:".$this->io_sql->conn->ErrorMsg()."\r\n";
        }

        //PROCESANDO SALDO LIBRO, MOV NO EN LIBRO, MOV NO EN BCO, CABECER DE CONCILIACION
        $saldoLibro    = $this->uf_calcular_saldo_libro($codban, $ctaban, $fechasta);
        $arrResNoLibro = $this->procesar_movnolibro($codban, $ctaban, $fechasta);
        $cadenaError  .= $arrResNoLibro['cadenaError'];
        $arrResNoBanco = $this->procesar_movnobanco($codban, $ctaban, $fechasta);
        $cadenaError  .= $arrResNoBanco['cadenaError'];
        $saldoBanco = str_replace('.', '', $saldoBanco);
        $saldoBanco = str_replace(',', '.', $saldoBanco);
        $arrResCon = $this->uf_guardar_cabecera_conciliacion($codban, $ctaban, $mesano, $saldoConcilia, $saldoBanco, $saldoLibro);
        $cadenaError  .= $arrResCon['mensajeError'];

        //ESCRIBIENDO RESULTADO DEL PROCESO
        $etiquetaBanco    = $codban.' - '.$denban;
        $cadenaResultado  = "RESULTADO DEL PROCESO DE CONCILIACION \r\n BANCO:{$etiquetaBanco} \r\n CUENTA:{$ctaban}  \r\n";
        $cadenaResultado .= "Saldo segun libro: ".number_format($saldoLibro,2,',','.')."  \r\n";
        $cadenaResultado .= "Saldo segun banco: ".number_format($saldoBanco,2,',','.')."  \r\n";
        $cadenaResultado .= "Saldo conciliado : ".number_format($saldoConcilia,2,',','.')." \r\n";
        $cadenaResultado .= "Saldo transacciones no registradas en banco: ".number_format($arrResNoBanco['saldoNoBanco'],2,',','.')." \r\n";
        $cadenaResultado .= $arrResNoLibro["cadenaMovNoBanco"]; 
        $cadenaResultado .= "Saldo transacciones no registradas en libro: ".number_format($arrResNoLibro['saldoNoLibro'],2,',','.')."  \r\n";
        $cadenaResultado .= $arrResNoLibro["cadenaMovNoLibro"]; 
        $cadenaResultado .= "Errores en el proceso: \r\n".$cadenaError; 
        @fwrite($arcResultado,$cadenaResultado);
        fclose($arcResultado);
        $this->io_msg->message('El proceso de conciliacion finalizo descargue el archivo con los resultados');
    }
    
    public function buscar_movnobanco($codban, $ctaban, $mes, $periodo)
    {
        $arrResultado = array();
        $objectB = array();
        $contNoBanco  = 0;
        
        $cadenaSQL = "SELECT numref, fecmov, tipmov, monto ".
                     "  FROM scb_movnolibro ".
                     " WHERE codban='{$codban}' ".
                     "   AND ctaban='{$ctaban}' ".
                     "   AND EXTRACT(MONTH FROM fecmov)='{$mes}' ".
                     "   AND EXTRACT(YEAR FROM fecmov)='{$periodo}' ".
                     " ORDER BY fecmov ASC ";
        $dataNoBanco = $this->io_sql->execute($cadenaSQL);
        while (!$dataNoBanco->EOF)
        {
            $contNoBanco++;
            $numref = $dataNoBanco->fields['numref'];
            $fecmov = $dataNoBanco->fields['fecmov'];
            $tipmov = $dataNoBanco->fields['tipmov'];
            $monto  = $dataNoBanco->fields['monto'];

            $objectB[$contNoBanco][1]  = "<input type=text name=txtnumdocB".$contNoBanco." value='".$numref."' class=sin-borde readonly style=text-align:center size=18 maxlength=15>";
            $objectB[$contNoBanco][2]  = "<input type=text name=txtfecmovB".$contNoBanco." value='".$fecmov."' class=sin-borde readonly style=text-align:center size=10 maxlength=10>";
            $objectB[$contNoBanco][3]  = "<input type=text name=txtmontoB".$contNoBanco."  value='".number_format($monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=20 maxlength=20>";
            $objectB[$contNoBanco][4]  = "<input type=text name=txtcodopeB".$contNoBanco." value='".$tipmov."' class=sin-borde readonly style=text-align:center size=5 maxlength=5>";				
            $dataNoBanco->MoveNext();
        }
        unset($dataNoBanco);
        $arrResultado['contNoBanco']  = $contNoBanco;
        $arrResultado['objectB'] = $objectB;

        return $arrResultado;
    }

    public function buscar_porconciliar($codban, $ctaban, $mesano)
    {
        $arrResultado = array();
        $objectP = array();
        $contP  = 0;
        $mes  = substr($mesano,0,2);
        $anno = substr($mesano,3,4);
        $fechasta = $this->io_fecha->uf_last_day($mes, $anno);
        $fechasta = $this->io_fecha->uf_convert_date_to_db($fechasta);        
        
        $cadenaSQL = "SELECT numdoc,fecmov,codope,conmov,(monto - monret) as monto,estmov ".
		     "  FROM scb_movbco ".
		     " WHERE codemp = '".$this->codemp."'".
                     "   AND codban = '".$codban."' ".
		     "   AND ctaban = '".$ctaban."' ".
                     "   AND estcon = '0' ".
                     "   AND fecmov <='{$fechasta}' ".
                     "   AND estmov <>'N' ".
                     "   AND (feccon='1900-01-01' AND fecmov<='{$fechasta}') ".
                     " ORDER BY fecmov ASC ";                             
                     
        $dataPorConciliar = $this->io_sql->execute($cadenaSQL);
        while (!$dataPorConciliar->EOF)
        {
            $contP++;
            $numref = $dataPorConciliar->fields['numdoc'];
            $fecmov = $dataPorConciliar->fields['fecmov'];
            $conmov = $dataPorConciliar->fields['conmov'];
            $tipmov = $dataPorConciliar->fields['codope'];
            $estmov = $dataPorConciliar->fields['estmov'];
            $monto  = $dataPorConciliar->fields['monto'];

            $objectP[$contP][1]  = "<input name=chk".$contP." type=checkbox id=chk".$contP." value=1 class=sin-borde>";
            $objectP[$contP][2]  = "<input type=text name=txtnumdocP".$contP." value='".$numref."' class=sin-borde readonly style=text-align:center size=18 maxlength=15>";
            $objectP[$contP][3]  = "<input type=text name=txtfecmovP".$contP." value='".$fecmov."' class=sin-borde readonly style=text-align:center size=10 maxlength=10>";
            $objectP[$contP][4]  = "<input type=text name=txtconmovP".$contP." value='".$conmov."' class=sin-borde readonly style=text-align:left size=20 maxlength=20>";
            $objectP[$contP][5]  = "<input type=text name=txtmontoP".$contP."  value='".number_format($monto,2,",",".")."' class=sin-borde readonly style=text-align:right size=20 maxlength=20>";
            $objectP[$contP][6]  = "<input type=text name=txtcodopeP".$contP." value='".$tipmov."' class=sin-borde readonly style=text-align:center size=5 maxlength=5> ".
                                   "<input name=txtestmovP".$contP." type='hidden' value='".$estmov."'>";				
            $dataPorConciliar->MoveNext();
        }
        unset($dataPorConciliar);
        $arrResultado['contP']  = $contP;
        $arrResultado['objectP'] = $objectP;

        return $arrResultado;
    }
    
	function uf_conciliar_movimientos_manual($codban,$ctaban,$numdoc,$estmov,$codope,$estcon,$feccon)
	{
            $valido = true;
            $cadenaSQL    = "UPDATE scb_movbco ".
                            "   SET estcon = '".$estcon."', ".
                            "       feccon = '".$feccon."' ".
                            " WHERE codemp = '".$this->codemp."'  ".
                            "   AND codban = '".$codban."'  ".
                            "   AND ctaban = '".$ctaban."'  ".
                            "   AND numdoc = '".$numdoc."' ".
                            "   AND estmov = '".$estmov."' ".
                            "   AND codope = '".$codope."'";
            if ($this->io_sql->execute($cadenaSQL) === false)
            {
                $valido = false;
                $mensajeError = "Ocurrio un error guardando la cabecera de la conciliacion, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n";
            }

            return $valido;
	}	
	
    public function uf_actualizar_conciliacion($codban, $ctaban, $mesano, $saldoConcilia)
    {
        $guardo = true;
        $arrRespuesta = array();
        $mensajeError = '';
        $mesano = str_replace("/","",$mesano);

        if ($this->uf_buscar_cabecera_conciliacion($codban, $ctaban, $mesano))
        {
            $cadenaSQL = "UPDATE scb_conciliacion ".
                         "   SET conciliacion=conciliacion+{$saldoConcilia} ".
                         " WHERE codban='{$codban}' ".
                         "   AND ctaban='{$ctaban}' ".
                         "   AND codemp = '".$this->codemp."' ".
                         "   AND mesano='{$mesano}'";
        }

        if ($this->io_sql->execute($cadenaSQL) === false)
        {
            $guardo = false;
            $mensajeError = "Ocurrio un error guardando la cabecera de la conciliacion, Error SQL:".$this->io_sql->conn->ErrorMsg()." .\r\n";
        }

        $arrRespuesta['exito'] = $guardo;
        $arrRespuesta['mensajeError'] = $mensajeError;

        return $arrRespuesta;
    }
    
    
}