<?php
/***********************************************************************************
* @fecha de modificacion: 20/09/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

class sigesp_sno_c_metodo_banco_2
{
    var $io_mensajes;
    var $io_funciones;
    var $io_sno;
    var $io_metbanco;
    var $ls_codemp;
    var $ls_nomemp;
    var $ls_rifemp;
    var $ls_siglas;
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    public function __construct()
    {    
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: sigesp_sno_c_metodo_banco_2
        //           Access: public 
        //      Description: Constructor de la Clase
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");  //clase de mensajes al usuario
        $this->io_mensajes=new class_mensajes();        
        require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");  //Para el uso de funciones
        $this->io_funciones=new class_funciones();
        require_once("sigesp_sno.php");
        $this->io_sno=new sigesp_sno();
        require_once("sigesp_snorh_c_metodobanco.php");
        $this->io_metbanco=new sigesp_snorh_c_metodobanco();
        $this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
        $this->ls_nomemp=$_SESSION["la_empresa"]["nombre"];
        $this->ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
        $this->ls_siglas=$_SESSION["la_empresa"]["titulo"];
    }// end function sigesp_sno_c_metodo_banco_2
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_eap_micasa($as_ruta,$rs_data)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_eap_micasa
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //      Description: genera el archivo txt a disco para  el banco eap_micasa para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,11);
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,11);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,11);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],9);
                //$ls_nacper=$aa_ds_banco->getValue("nacper",$li_i);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_eap_micasa
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_fondo_comun($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_fondo_comun
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //                 as_codmetban // cï¿½digo de mï¿½todo a banco 
        //                 as_desope // descripcion de operacion
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 05/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_nrodebitos=1; // se inicializa en uno, por que solo hay un registro y no es variable
        $li_nrocreditos=0;
        $ls_mondeb=0;
        $ls_moncre=0;
        $ldec_monto=0;
        $lb_valido=false;        
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $ldec_codproc=$this->io_sno->uf_select_config("SNO","GEN_DISK","FONDO COMUN COD PROCESO","1","I");
        $ldec_codproc=$this->io_funciones->uf_cerosizquierda(intval($this->io_funciones->uf_trim($ldec_codproc),10),12);    
        $lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","FONDO COMUN COD PROCESO",$ldec_codproc+1,"I");
        if($lb_valido)
        {
            $ls_codempnom="";
            $ls_codofinom="";
            $ls_tipcuedeb="";
            $ls_tipcuecre="";
            $ls_numconvenio="";
            $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
			$ls_codempnom=$arrResultado['as_codempnom'];
			$ls_codofinom=$arrResultado['as_codofinom'];
			$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
			$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
			$ls_numconvenio=$arrResultado['as_numconnom'];
			$lb_valido=$arrResultado['lb_valido'];		
            $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,6),6);    
            $ls_tipcuecre=$this->io_funciones->uf_cerosizquierda($ls_tipcuecre,2);    
            $ls_tipcuedeb=$this->io_funciones->uf_cerosizquierda($ls_tipcuedeb,2);    
            $ldec_codproc=$this->io_funciones->uf_cerosizquierda($ldec_codproc,12);    
        }
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {
                //Registro de Encabezado
                $as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
                $as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 22);
                $ls_fecha=date("Ymd"); //Fecha de elaboracion
                $ls_hora=date("his"); //Hora de elaboracion
                $ls_fecapl="00000000"; //Fecha de aplicacion
                $ls_horapl="000000";   // Hora de aplicacion
                $ls_codser="000001";   // Codigo de Servicio
                $ls_numcuecre="0000000000000000000000"; //Numero de Cuenta de Credito
                $ls_constante="000000000000000000000000000000000000000000000000";
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,6,4);
                $ld_fecproc=$li_ano.$li_mes.$li_dia;
                $ls_cadena="000000".$ls_fecha.$ls_hora.$ld_fecproc."090000".$ls_fecapl.$ls_horapl.$ls_codempnom.$ls_codser." ".
                           $ls_tipcuedeb.$as_codcueban." ".$ls_tipcuecre.$ls_numcuecre.$ldec_codproc.$ls_constante."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if($lb_valido)
            {
                $as_desope=$this->io_funciones->uf_rellenar_der($as_desope," ",40);
                $ldec_moncre=0;
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $ls_codcueban=$rs_data->fields["codcueban"];
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                    $ldec_neto=$rs_data->fields["monnetres"];
                    $ldec_moncre=$ldec_moncre+$ldec_neto;
                    $ldec_neto=($ldec_neto*100);  
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
                    $ls_nacper=$rs_data->fields["nacper"];
                    $ls_cedper=$rs_data->fields["cedper"];
                    $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
                    $ls_serser="00001" ;     //serial de servicio
                    $ls_numcuo="00000";      //numero de cuotas
                    $ls_numref= "0000000000"; //numero de referencia
                    $ls_cargo="0";          // aplicar cargo
                    $ls_codrech="000";        // Codigo de rechazo
                    $ls_desrech=$this->io_funciones->uf_rellenar_der(""," ",40); //Descripcion del rechazo
                    $ls_relleno="000000000";  //valor fijo de relleno
                    $li_nrocreditos=$li_nrocreditos+1;
                    $li_contador=$this->io_funciones->uf_cerosizquierda($li_i,6);
                    $ls_cadena=$li_contador." ".$ls_tipcuecre.$ls_codcueban.$ls_nacper.$ls_cedper.$ls_serser.$ls_numcuo.
                                     $ls_numref.$ldec_neto."C"."0".$as_desope.$ls_cargo.$ls_codrech.$ls_desrech.$ls_relleno."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }        
                    $rs_data->MoveNext();
                }
            }
            if($lb_valido)
            {
                //Registro de Totales
                $ls_nomemp=str_pad(substr($_SESSION["la_empresa"]["nombre"],0,39),39," ");
                $li_canreg=$li_nrodebitos + $li_nrocreditos;  //Cantidad de registros
                $li_canreg=$this->io_funciones->uf_cerosizquierda($li_canreg,6);
                $ldec_mondeb=0; 
                $ldec_mondeb=($ldec_mondeb*100);  
                $ldec_mondeb=$this->io_funciones->uf_cerosizquierda($ldec_mondeb,15);
                $ldec_moncre=($ldec_moncre*100);
                $ldec_moncre=$this->io_funciones->uf_cerosizquierda($ldec_moncre,15);
                $li_nrodebitos=$this->io_funciones->uf_rellenar_der($li_nrodebitos,"0",6);
                $li_nrocreditos=$this->io_funciones->uf_rellenar_der($li_nrocreditos,"0",6);
                $ls_ceros=$this->io_funciones->uf_cerosizquierda("0",76);
                $ls_cadena="999999"." ".$ls_nomemp.$li_canreg.$ldec_mondeb.$ldec_moncre.$li_nrodebitos.$li_nrocreditos.$ls_ceros."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_fondo_comun
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_fondo_comun_01($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope,$adec_montot) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_fondo_comun_01
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //                 as_codmetban // cï¿½digo de mï¿½todo a banco 
        //                 as_desope // descripcion de operacion
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 05/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //$li_nrodebitos=1; // se inicializa en uno, por que solo hay un registro y no es variable
        $li_nrodebitos=0;
        $li_nrocreditos=0;
        $ls_mondeb=0;
        $ls_moncre=0;
        $ldec_monto=0;
        $lb_valido=false;        
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $ls_codempnom="";
        $ls_codofinom="";
        $ls_tipcuedeb="";
        $ls_tipcuecre="";
        $ls_numconvenio="";
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];		
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,6),6);    
        $ls_tipcuecre=$this->io_funciones->uf_cerosizquierda($ls_tipcuecre,2);    
        $ls_tipcuedeb=$this->io_funciones->uf_cerosizquierda($ls_tipcuedeb,2);    
        $ldec_codproc=$this->io_funciones->uf_cerosizquierda($ldec_codproc,12);    
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {
                //Registro de Encabezado
                $as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
                $as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 20);
                $ls_fecha=date("Ymd"); //Fecha de elaboracion
                $ls_hora=date("his"); //Hora de elaboracion
                $ls_fecapl="00000000"; //Fecha de aplicacion
                $ls_horapl="000000";   // Hora de aplicacion
                $ls_codser="000001";   // Codigo de Servicio
                $ls_constante="0";
                $ls_constante=$this->io_funciones->uf_cerosizquierda($ls_constante, 82);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,6,4);
                $ld_fecproc=$li_ano.$li_mes.$li_dia;
                $ls_cadena="000000".$ls_fecha.$ls_hora.$ld_fecproc."090000".$ls_fecapl.$ls_horapl.$ls_codempnom.$ls_codser." ".
                $ls_tipcuedeb.$ls_tipcuecre.$as_codcueban."   ".$ls_constante."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if($lb_valido)
            {
                $as_desope=$this->io_funciones->uf_rellenar_der($as_desope," ",40);
                $ldec_moncre=0;
                $li_i=0;
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $li_i++;
                    $ls_codcueban=$rs_data->fields["codcueban"];
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                    $ldec_neto=$rs_data->fields["monnetres"];
                    $ldec_moncre=$ldec_moncre+$ldec_neto;
                    $ldec_neto=($ldec_neto*100);  
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
                    $ls_nacper=$rs_data->fields["nacper"];
                    $ls_cedper=$rs_data->fields["cedper"];
                    $ls_tipcuebanper=$rs_data->fields["tipcuebanper"];
                    switch($ls_tipcuebanper)
                    {
                        case 'A':
                            $ls_tipcuecre='LS';
                        break;
                            
                        case 'C':
                            $ls_tipcuecre='cc';
                        break;
                    }
                    
                    $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
                    $ls_serser="00001" ;     //serial de servicio
                    $ls_numcuo="00000";      //numero de cuotas
                    $ls_numref= "0000000000"; //numero de referencia
                    $ls_cargo="0         ";          // aplicar cargo
                    $ls_codrech="000";        // Codigo de rechazo
                    $ls_desrech=$this->io_funciones->uf_rellenar_der(""," ",33); //Descripcion del rechazo
                    $ls_relleno="000000000";  //valor fijo de relleno
                    $li_nrocreditos=$li_nrocreditos+1;
                    $li_contador=$this->io_funciones->uf_cerosizquierda($li_i,6);
                    $ls_cadena=$li_contador." ".$ls_tipcuecre.$ls_codcueban.$ls_nacper.$ls_cedper.$ls_serser.$ls_numcuo.
                                     $ls_numref.$ldec_neto."C ".$as_desope.$ls_cargo.$ls_desrech.$ls_relleno."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }        
                    $rs_data->MoveNext();
                }
            }
            if($lb_valido)
            {
                //Registro de Totales
                $ls_nomemp=str_pad(substr($_SESSION["la_empresa"]["nombre"],0,40),40," ");
                $li_canreg=$li_nrodebitos + $li_nrocreditos;  //Cantidad de registros
                $li_canreg=$this->io_funciones->uf_cerosizquierda($li_canreg,6);
                $ldec_mondeb=$adec_montot; 
                $ldec_mondeb=($ldec_mondeb*100);  
                $ldec_mondeb=$this->io_funciones->uf_cerosizquierda($ldec_mondeb,15);
                $ldec_moncre=($ldec_moncre*100);
                $ldec_moncre=$this->io_funciones->uf_cerosizquierda($ldec_moncre,15);
                $li_nrodebitos=$this->io_funciones->uf_rellenar_izq($li_nrodebitos,"0",6);
                $li_nrocreditos=$this->io_funciones->uf_rellenar_izq($li_nrocreditos,"0",6);
                $ls_ceros=$this->io_funciones->uf_cerosizquierda("0",76);
                $ls_cadena="999999".$ls_nomemp.$li_canreg.$ldec_mondeb.$ldec_moncre.$li_nrodebitos.$li_nrocreditos.$ls_ceros."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_fondo_comun_01
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_industrial($as_ruta,$rs_data)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_industrial
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //      Description: genera el archivo txt a disco para  el banco industrial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_constante="0";
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,13);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],11);
                $ls_constante=$this->io_funciones->uf_cerosizquierda($ls_constante,13);
                $ls_cadena="770".$ls_codcueban.$ls_cedper.$ldec_neto.$ls_constante."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_industrial
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_pueblo($as_ruta,$rs_data)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_pueblo
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //      Description: genera el archivo txt a disco para  el banco industrial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_constante="0";
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],9);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                //$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,15);
                //$ls_constante=$this->io_funciones->uf_cerosizquierda($ls_constante,13);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_nombrepersona=$ls_nomper.", ".$ls_apeper;
                $ls_personal=substr($ls_nombrepersona,0,35);
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto.$ls_personal."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_pueblo
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_pueblo_2($as_ruta,$rs_data)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_pueblo_2
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //      Description: genera el archivo txt a disco para  el banco industrial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_constante="0";
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],9);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,15);
                $ls_tipcuebanper=trim($rs_data->fields["tipcuebanper"]);
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto.$ls_tipcuebanper."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_pueblo_2
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_mercantil($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_mercantil
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/bsf0000w.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,85);
                $ldec_monpre=(substr($ls_cad_previa,26,13)/100);
                $ldec_monacu=round($ldec_monpre+$adec_montot,2);
            }
            else
            {
                //Registro Cabecera (Dï¿½bito)
                $li_filads=$li_count;
                $ldec_totdep=$adec_montot;
                $ldec_totdep=round($ldec_totdep,2);
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep*100,13);
                $as_codcuenta=$this->io_funciones->uf_trim(str_replace("-","",$as_codcuenta));
                $li_inicio=strlen($as_codcuenta)-10;
                $as_codcuenta=substr($as_codcuenta,$li_inicio,10);
                $as_codcuenta=$this->io_funciones->uf_cerosizquierda($as_codcuenta,12);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,6,4);
                $ls_blancos="00000000000000";
                $ls_cadena="640".$as_codcuenta."785"."00000000".$ldec_totdep."0000000000000"."001050".$li_ano.$li_mes.$li_dia.$ls_blancos."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }                    
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,8,20),12);//Modificado por Carlos Zambrano
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,13);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($rs_data->fields["cedper"],0,8),8);
                $ls_cadena="770".$ls_codcueban."222".$ls_cedper.$ldec_neto."0000000000000"."001050"."0000000000000000000000"."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=substr($ls_cad_previa,39,41);
                $ls_cadena=substr($ls_cad_previa,0,26).$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,13).$ls_reemplazar."\r\n";//.$ls_reemplazar;
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_mercantil
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_mercantil2($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_mercantil
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_acum=0;
        $ls_cant=0;
        $ls_nombrearchivo=$as_ruta."/mercantil2.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
        //Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
            //Registro Cabecera (Dï¿½bito)
            $li_filads=$li_count;
            $li_filads=$this->io_funciones->uf_cerosizquierda($li_filads,8);
            $ldec_totdep= number_format(abs($adec_montot),2,'.','');
            $ldec_totdep= str_replace('.','',$ldec_totdep);
            $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,17);
            $li_dia=substr($ad_fecproc,0,2);
            $li_mes=substr($ad_fecproc,3,2);
            $li_ano=substr($ad_fecproc,6,4);
            $ls_fecha=$li_ano.$li_mes.$li_dia;
            $ls_codempnom=str_pad($as_codempnom,15," ",STR_PAD_RIGHT);
            $ls_blanco=str_pad("0",288,"0",STR_PAD_RIGHT);
            $ls_tipiden=substr($_SESSION["la_empresa"]["rifemp"],0,1);
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("-","",$_SESSION["la_empresa"]["rifemp"]));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("J","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("j","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("G","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("g","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("V","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("v","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("E","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("e","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("P","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("p","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_cerosizquierda($ls_rifemp,15);
            $ls_cadena="1"."BAMRVECA    ".$ls_codempnom."NOMIN"."0000000222".$ls_tipiden.$ls_rifemp.$li_filads.$ldec_totdep.$ls_fecha.$as_codcuenta.$ls_blanco."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cant+=$ls_cant;
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);//Modificado por Carlos Zambrano
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ls_acum+=$ldec_neto;  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 17, 0, 0);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_nomapeper=$ls_nomper." ".$ls_apeper;
                $ls_nomapeper=str_pad($ls_nomapeper,60," ",STR_PAD_RIGHT); // rrellena espacio a la izquierda 
                $ls_coreleper=str_pad($rs_data->fields["correo"],50," ",STR_PAD_RIGHT);
                $ls_codper=str_pad($rs_data->fields["codper"],16,"0",STR_PAD_LEFT);
                $ls_blanco=str_pad(" ",30," ",STR_PAD_RIGHT);
                $ls_ceros=str_pad("0",12,"0",STR_PAD_RIGHT);
                $ls_ceros2=str_pad("0",35,"0",STR_PAD_RIGHT);
                $ls_concepto=str_pad("ABONO DE NOMINA",80," ",STR_PAD_RIGHT);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($rs_data->fields["cedper"],0,8),15);
                $ls_cadena="2".$ls_nacper.$ls_cedper."1".$ls_ceros.$ls_blanco.$ls_codcueban.$ls_monto.$ls_codper."0000000222"."000".$ls_nomapeper."000000000000000".$ls_coreleper."0000".$ls_blanco.$ls_concepto.$ls_ceros2."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_mercantil2  
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_bnc2($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_bnc2
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_acum=0;
        $ls_cant=0;
        $ls_nombrearchivo=$as_ruta."/BNC2.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
        //Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
            //Registro Cabecera (Dï¿½bito)
            $li_filads=$li_count;
            $li_filads=$this->io_funciones->uf_cerosizquierda($li_filads,8);
            $ldec_totdep= number_format(abs($adec_montot),2,'.','');
            $ldec_totdep= str_replace('.','',$ldec_totdep);
            $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,13);
            $li_dia=substr($ad_fecproc,0,2);
            $li_mes=substr($ad_fecproc,3,2);
            $li_ano=substr($ad_fecproc,6,4);
            $ls_fecha=$li_ano.$li_mes.$li_dia;
            $as_codcuenta=trim($as_codcuenta);
            $ls_codempnom=str_pad($as_codempnom,15," ",STR_PAD_RIGHT);
            $as_codcuenta=str_pad($as_codcuenta,21,"0",STR_PAD_LEFT);
            $ls_blanco=str_pad("0",288,"0",STR_PAD_RIGHT);
            $ls_tipiden=substr($_SESSION["la_empresa"]["rifemp"],0,1);
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("-","",$_SESSION["la_empresa"]["rifemp"]));
           /* $ls_rifemp=$this->io_funciones->uf_trim(str_replace("J","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("j","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("G","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("g","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("V","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("v","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("E","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("e","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("P","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("p","",$ls_rifemp));*/
            $ls_rifemp=$this->io_funciones->uf_cerosizquierda($ls_rifemp,10);
            $ls_cadena="ND".$as_codcuenta.$ldec_totdep.$ls_rifemp."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cant+=$ls_cant;
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,21);//Modificado por Carlos Zambrano
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ls_acum+=$ldec_neto;  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 13, 0, 0);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_nomapeper=$ls_nomper." ".$ls_apeper;
                $ls_nomapeper=str_pad($ls_nomapeper,60," ",STR_PAD_RIGHT); // rrellena espacio a la izquierda 
                $ls_coreleper=str_pad($rs_data->fields["correo"],50," ",STR_PAD_RIGHT);
                $ls_codper=str_pad($rs_data->fields["codper"],16,"0",STR_PAD_LEFT);
                $ls_blanco=str_pad(" ",30," ",STR_PAD_RIGHT);
                $ls_ceros=str_pad("0",12,"0",STR_PAD_RIGHT);
                $ls_ceros2=str_pad("0",35,"0",STR_PAD_RIGHT);
                $ls_concepto=str_pad("ABONO DE NOMINA",80," ",STR_PAD_RIGHT);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($rs_data->fields["cedper"],0,8),9);
				$ls_cedper="V".$ls_cedper;
                $ls_cadena="NC".$ls_codcueban.$ls_monto.$ls_cedper."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_mercantil2  
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_mercantil3($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot,$as_codmetban)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_mercantil
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_acum=0;
        $ls_cant=0;
        $ls_nombrearchivo=$as_ruta."/BSF000W.txt";
        $li_count=$rs_data->RecordCount();
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=substr(trim($arrResultado['as_codempnom']),0,15);
        if($li_count>0)
        {
        //Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
            //Registro Cabecera (Dï¿½bito)
            $li_filads=$li_count;
            $li_filads=$this->io_funciones->uf_cerosizquierda($li_filads,8);
            $ldec_totdep= number_format(abs($adec_montot),2,'.','');
            $ldec_totdep= str_replace('.','',$ldec_totdep);
            $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,17);
            $li_dia=substr($ad_fecproc,0,2);
            $li_mes=substr($ad_fecproc,3,2);
            $li_ano=substr($ad_fecproc,6,4);
            $ls_fecha=$li_ano.$li_mes.$li_dia;
            $ls_codempnom=str_pad($ls_codempnom,15," ",STR_PAD_RIGHT);
            $ls_blanco=str_pad("0",288,"0",STR_PAD_RIGHT);
            $ls_tipiden=substr($_SESSION["la_empresa"]["rifemp"],0,1);
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("-","",$_SESSION["la_empresa"]["rifemp"]));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("J","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("j","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("G","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("g","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("V","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("v","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("E","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("e","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("P","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("p","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_cerosizquierda($ls_rifemp,15);
            $as_codcuenta= str_replace('-','',$as_codcuenta);
            $as_codcuenta=substr(trim($as_codcuenta),0,20);
            $ls_cadena="1"."BAMRVECA    ".$ls_codempnom."NOMIN"."0000000222".$ls_tipiden.$ls_rifemp.$li_filads.$ldec_totdep.$ls_fecha.$as_codcuenta.$ls_blanco."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cant+=$ls_cant;
                $ls_codcueban=$rs_data->fields["codcueban"];
		$ls_codcueban= str_replace('-','',$ls_codcueban);
		$ls_codcueban=substr(trim($ls_codcueban),0,20);
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);//Modificado por Carlos Zambrano
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ls_acum+=$ldec_neto;  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 17, 0, 0);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_nomapeper=substr($ls_nomper." ".$ls_apeper,0,60);
                $ls_nomapeper=str_pad($ls_nomapeper,60," ",STR_PAD_RIGHT); // rrellena espacio a la izquierda 
                $ls_coreleper=str_pad($rs_data->fields["correo"],50," ",STR_PAD_RIGHT);
                $ls_codper=str_pad($rs_data->fields["codper"],16,"0",STR_PAD_LEFT);
                $ls_blanco=str_pad(" ",30," ",STR_PAD_RIGHT);
                $ls_ceros=str_pad("0",12,"0",STR_PAD_RIGHT);
                $ls_ceros2=str_pad("0",35,"0",STR_PAD_RIGHT);
                $ls_concepto=str_pad("ABONO DE NOMINA",80," ",STR_PAD_RIGHT);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($rs_data->fields["cedper"],0,8),15);
                $ls_cadena="2".$ls_nacper.$ls_cedper."1".$ls_ceros.$ls_blanco.$ls_codcueban.$ls_monto.$ls_codper."0000000222"."000".$ls_nomapeper."000000000000000".$ls_coreleper."0000".$ls_blanco.$ls_concepto.$ls_ceros2."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_mercantil3  
    //-----------------------------------------------------------------------------------------------------------------------------------/*

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_2020($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	//         Function: uf_metodo_banco_venezuela_2020
    	//           Access: public
    	//        Arguments: aa_ds_banco // arreglo (datastore) datos banco
    	//                   ad_fecproc // Fecha de procesamiento
    	//                   as_codcuenta // codigo de cuenta
    	//                   adec_montot // total a depositar
    	//      Description: genera el archivo txt a disco para el Banco Venezuela para pago de proveedores
    	//       Creado Por: Ing. Gerardo Cordero
    	// Fecha Creacion: 15/08/2014
    	// Modificado Por:                                         Fecha Utima Modificacion :
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$lb_valido=true;

    	//Creando el archivo txt prueba
		$nro_archivo=1;
    	$ls_nombrearchivo=$as_ruta."/venezuela_nomina_".$nro_archivo.".txt";
    	if (file_exists("$ls_nombrearchivo"))
        {
    		if(@unlink("$ls_nombrearchivo")===false)
            {
    			$lb_valido=false;
    		}
    		else
            {
    			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
    		}
    	}
    	else
        {
    		$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
    	}
    		
    	//Registro Cabecera 
    	$li_dia=substr($ad_fecproc,0,2);
    	$li_mes=substr($ad_fecproc,3,2);
    	$li_ano=substr($ad_fecproc,6,4);

        $li_numlote=$this->io_sno->uf_select_config("SCB","GEN_DISK","VENEZUELA_PROVEEDORES","11849","I");
        $li_numlote=intval($this->io_funciones->uf_trim($li_numlote));
        $lb_valido=$this->io_sno->uf_insert_config("SCB","GEN_DISK","VENEZUELA_PROVEEDORES",$li_numlote+1,"I");
        
		$as_numref = intval(preg_replace('/[^0-9]+/', '', $li_numlote), 10);     		
    	$ls_numref = substr(trim($as_numref), -8);
    	$ls_numref = str_pad($ls_numref, 8, '0', STR_PAD_LEFT);
    	$ls_numrefdet = substr(trim($as_numref), -6);
    	$ls_numrefdet = str_pad($ls_numrefdet, 6, '0', STR_PAD_LEFT);
    	$ls_rifemp = trim($_SESSION["la_empresa"]["rifemp"]);
    	$ls_letrif = substr($ls_rifemp,0,1);
    	$ls_numrif = substr($ls_rifemp,1,15);
    	$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
    		
    	$arrCueDeb = $this->uf_tipo_cuenta_debito(trim($as_codcuenta));
    	$ls_numnego = str_pad(trim($arrCueDeb['numnego']), 8, '0', STR_PAD_LEFT);
    	$ls_tipcue = $arrCueDeb['tipcta'];
    	$ls_cadena = "HEADER  {$ls_numref}{$ls_numnego}{$ls_letrif}{$ls_numrif}{$li_dia}/{$li_mes}/{$li_ano}{$li_dia}/{$li_mes}/{$li_ano}\r\n";
    	   		    	
        $ls_nomemp = substr(trim($_SESSION["la_empresa"]["nombre"]), 0, 35);
        $ls_numcue = substr(trim($as_codcuenta), 0, 20);
    	//Registro de creditos
    	$conCre = 0;
		$ls_acum = 0;
		while((!$rs_data->EOF)&&($lb_valido))
		{
			$conCre++;
			$nro = str_pad($conCre, 2, '0', STR_PAD_LEFT);
			$ldec_neto=$rs_data->fields["monnetres"];  
			$ld_moncre  = str_pad(number_format(abs($ldec_neto),2,',',''),18,'0',STR_PAD_LEFT);
			//Registro de debito
			$ls_cadena .= "DEBITO  {$ls_numrefdet}{$nro}{$ls_letrif}{$ls_numrif}{$ls_nomemp}{$li_dia}/{$li_mes}/{$li_ano}{$ls_tipcue}{$ls_numcue}{$ld_moncre}VEF20\r\n";
			
			$ls_nacper=$rs_data->fields["nacper"];   //Nacionalidad
			$ls_cedper=$this->io_funciones->uf_rellenar_izq(trim($rs_data->fields["cedper"]),"0",9);  //cedula del personal
			$ls_rif = $ls_nacper.$ls_cedper;
			$ls_apeper=$rs_data->fields["apeper"];
			$ls_nomper=$rs_data->fields["nomper"];
			
			$ls_nombre =  substr($ls_apeper." ".$ls_nomper,0,30);
			$ls_nombre =  str_pad($ls_nombre,30,' ',STR_PAD_RIGHT);
			$ls_email   = '';
			$ls_email  = str_pad($ls_email,54,' ',STR_PAD_RIGHT);
			$ls_tipcta = "00";
			$ls_nrocta=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
			$ls_nrocta=$this->io_funciones->uf_trim(str_replace("-","",$ls_nrocta));
			$ls_nrocta=$this->io_funciones->uf_rellenar_der(substr(trim($ls_nrocta),0,20)," ",20);
			$ls_tippag="10";
			$ls_banco="BPROVECA";
			
			$ls_cadena .= "CREDITO {$ls_numrefdet}{$nro}{$ls_rif}{$ls_nombre}{$ls_tipcta}{$ls_nrocta}{$ld_moncre}{$ls_tippag}{$ls_banco}       {$ls_email}\r\n";
			$ls_acum+=$ldec_neto; 
			if ($conCre == 99)
			{
				$ls_acum  = str_pad(number_format(abs($ls_acum),2,',',''),18,'0',STR_PAD_LEFT);
				$conCre = str_pad($conCre, 5, '0', STR_PAD_LEFT);
				$ls_cadena .= "TOTAL   {$conCre}{$conCre}{$ls_acum}";
				if ($ls_creararchivo)
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido=false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
					$lb_valido=false;
				}
				
				@fclose($ls_creararchivo);
				if ($lb_valido)
				{
					$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
				}
				else
				{
					$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
				}
				
				$nro_archivo++;
				$ls_nombrearchivo=$as_ruta."/venezuela_nomina_".$nro_archivo.".txt";
				if (file_exists("$ls_nombrearchivo"))
				{
					if(@unlink("$ls_nombrearchivo")===false)
					{
						$lb_valido=false;
					}
					else
					{
						$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
					}
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
				}
				
				$li_numlote=$this->io_sno->uf_select_config("SCB","GEN_DISK","VENEZUELA_PROVEEDORES","11849","I");
				$li_numlote=intval($this->io_funciones->uf_trim($li_numlote));
				$lb_valido=$this->io_sno->uf_insert_config("SCB","GEN_DISK","VENEZUELA_PROVEEDORES",$li_numlote+1,"I");
				
				$as_numref = intval(preg_replace('/[^0-9]+/', '', $li_numlote), 10);     		
				$ls_numref = substr(trim($as_numref), -8);
				$ls_numref = str_pad($ls_numref, 8, '0', STR_PAD_LEFT);
				$ls_numrefdet = substr(trim($as_numref), -6);
				$ls_numrefdet = str_pad($ls_numrefdet, 6, '0', STR_PAD_LEFT);
					
				$ls_cadena = "HEADER  {$ls_numref}{$ls_numnego}{$ls_letrif}{$ls_numrif}{$li_dia}/{$li_mes}/{$li_ano}{$li_dia}/{$li_mes}/{$li_ano}\r\n";
								
				$ls_nomemp = substr(trim($_SESSION["la_empresa"]["nombre"]), 0, 35);
				$ls_numcue = substr(trim($as_codcuenta), 0, 20);
				//Registro de creditos
				$conCre = 0;
				$ls_acum  =0; 
			}
			$rs_data->MoveNext();
    	}
		$ls_acum  = str_pad(number_format(abs($ls_acum),2,',',''),18,'0',STR_PAD_LEFT);		
    	$conCre = str_pad($conCre, 5, '0', STR_PAD_LEFT);
    	$ls_cadena .= "TOTAL   {$conCre}{$conCre}{$ls_acum}";
    	if ($ls_creararchivo)
		{
    		if (@fwrite($ls_creararchivo,$ls_cadena)===false)
			{
    			$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
    			$lb_valido=false;
    		}
    	}
    	else
		{
    		$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
    		$lb_valido=false;
    	}
    	
    	@fclose($ls_creararchivo);
    	if ($lb_valido)
		{
    		$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
    	}
    	else
		{
    		$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
    	}
    	
    	return $lb_valido;
    }// uf_metodo_banco_venezuela_proveedores
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_mercantil_proveedores($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot,$as_tipope='')
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_mercantil_proveedores
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de proveedores
        //       Creado Por: Ing. Maryoly Caceres
        // Fecha Creacion: 22/05/2014                                 
        // Modificado Por:                                         Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_acum=0;
        $ls_cant=0;
        $ls_fecha='';
        $ls_nombrearchivo=$as_ruta."/mercantil_proveedores.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
        //Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
            //Registro Cabecera (Debito)
            $li_filads=$li_count;
            $li_filads=$this->io_funciones->uf_cerosizquierda($li_filads,8);
            $ldec_totdep= number_format(abs($adec_montot),2,'.','');
            $ldec_totdep= str_replace('.','',$ldec_totdep);
            $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,17);
            $li_dia=substr($ad_fecproc,0,2);
            $li_mes=substr($ad_fecproc,3,2);
            $li_ano=substr($ad_fecproc,6,4);
            $ls_fecha=$li_ano.$li_mes.$li_dia;
            $li_hora=str_replace(':','',date("h:m:s"));
            $ls_codnumlot=$ls_fecha.$li_hora;
            $ls_codempnom=str_pad($ls_codnumlot,15," ",STR_PAD_RIGHT); //el codigo aqui cual es
            $ls_blanco=str_pad("0",288,"0",STR_PAD_RIGHT);
            $ls_tipiden=substr($_SESSION["la_empresa"]["rifemp"],0,1);
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("-","",$_SESSION["la_empresa"]["rifemp"]));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("J","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("j","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("G","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("g","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("V","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("v","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("E","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("e","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("P","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("p","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_cerosizquierda($ls_rifemp,15);
            $ls_cadena="1"."BAMRVECA    ".$ls_codempnom."PROVE"."0000000062".$ls_tipiden.$ls_rifemp.$li_filads.$ldec_totdep.$ls_fecha.$as_codcuenta.$ls_blanco."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cant+=$ls_cant;
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"]; //monto del proveedor 
                $ls_acum+=$ldec_neto;  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 17, 0, 0);
                $ls_nomper=$rs_data->fields["nomper"];
                if($rs_data->fields["tipdes"]=='P'){
                	$ls_nomapeper=str_pad($ls_nomper,60," ",STR_PAD_RIGHT); // rrellena espacio a la izquierda 
                	$ls_cedper=trim($rs_data->fields["cedper"]);
                	$ls_nacper=substr($ls_cedper,0,1);
                	$ls_cedper=str_replace("-","",$ls_cedper);
                	$ls_codper=str_pad($ls_cedper,16," ",STR_PAD_RIGHT);
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("J","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("j","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("G","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("g","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("V","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("v","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("E","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("e","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("P","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_trim(str_replace("p","",$ls_cedper));
		            $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,15);
		            
                }
                else{
                	$ls_apeper=$rs_data->fields["apeper"];
                	$ls_nomapeper=$ls_nomper." ".$ls_apeper;
                	$ls_nomapeper=str_pad($ls_nomper,60," ",STR_PAD_RIGHT);
                	$ls_cedper=$this->io_funciones->uf_cerosizquierda(trim($rs_data->fields["cedper"]),15);
                	$ls_codper=str_pad($rs_data->fields["cedper"],16," ",STR_PAD_RIGHT);
                	$ls_nacper=$rs_data->fields["nacper"];
                }
                $ls_coreleper=str_pad($rs_data->fields["correo"],50," ",STR_PAD_RIGHT);
                $ls_blanco=str_pad(" ",15," ",STR_PAD_RIGHT);
                $ls_blanco2=str_pad(" ",30," ",STR_PAD_RIGHT);
                $ls_ceros=str_pad("0",12,"0",STR_PAD_RIGHT);
                $ls_ceros2=str_pad("0",15,"0",STR_PAD_RIGHT);
                $ls_ceros3=str_pad("0",35,"0",STR_PAD_RIGHT);
                $ls_concepto=str_pad($ls_nomper,80," ",STR_PAD_RIGHT);
                $ls_tipope= '1';
                if($as_tipope=='1'){
                	$ls_tipope= '3';
                }
                $ls_cadena="2".$ls_nacper.$ls_cedper.$ls_tipope.$ls_ceros.$ls_blanco.$ls_ceros2.$ls_codcueban.$ls_monto.$ls_codper."0000000062"."000".$ls_nomapeper."0000000".$ls_fecha.$ls_coreleper."0000".$ls_blanco2.$ls_concepto.$ls_ceros3."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// uf_metodo_banco_mercantil_proveedores
    //-----------------------------------------------------------------------------------------------------------------------------------
	
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_proveedores($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot,$as_numref,$aa_credito)
    {
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	//         Function: uf_metodo_banco_venezuela_proveedores
    	//           Access: public
    	//        Arguments: aa_ds_banco // arreglo (datastore) datos banco
    	//                   ad_fecproc // Fecha de procesamiento
    	//                   as_codcuenta // codigo de cuenta
    	//                   adec_montot // total a depositar
    	//      Description: genera el archivo txt a disco para el Banco Venezuela para pago de proveedores
    	//       Creado Por: Ing. Gerardo Cordero
    	// Fecha Creacion: 15/08/2014
    	// Modificado Por:                                         Fecha Utima Modificacion :
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$lb_valido=true;

    	//Creando el archivo txt prueba
    	$ls_nombrearchivo=$as_ruta."/venezuela_proveedores.txt";
    	if (file_exists("$ls_nombrearchivo")) {
    		if(@unlink("$ls_nombrearchivo")===false) {
    			$lb_valido=false;
    		}
    		else {
    			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
    		}
    	}
    	else {
    		$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
    	}
    		
    	//Registro Cabecera 
    	$li_dia=substr($ad_fecproc,0,2);
    	$li_mes=substr($ad_fecproc,3,2);
    	$li_ano=substr($ad_fecproc,6,4);

		$as_numref = intval(preg_replace('/[^0-9]+/', '', $as_numref), 10);     		
    	$ls_numref = substr(trim($as_numref), -8);
    	$ls_numref = str_pad($ls_numref, 8, '0', STR_PAD_LEFT);
    	$ls_rifemp = trim($_SESSION["la_empresa"]["rifemp"]);
    	$ls_letrif = substr($ls_rifemp,0,1);
    	$ls_numrif = substr($ls_rifemp,1,15);
    	$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
    		
    	$arrCueDeb = $this->uf_tipo_cuenta_debito(trim($as_codcuenta));
    	$ls_numnego = str_pad(trim($arrCueDeb['numnego']), 8, '0', STR_PAD_LEFT);
    	$ls_tipcue = $arrCueDeb['tipcta'];
    	$ls_cadena = "HEADER  {$ls_numref}{$ls_numnego}{$ls_letrif}{$ls_numrif}{$li_dia}/{$li_mes}/{$li_ano}{$li_dia}/{$li_mes}/{$li_ano}\r\n";
    	   		
    	//Registro de debito
    	$ls_nomemp = substr(trim($_SESSION["la_empresa"]["nombre"]), 0, 35);
    	$ls_numcue = substr(trim($as_codcuenta), 0, 20);
    	$ld_monto  = str_pad(number_format(abs($adec_montot),2,',',''),18,'0',STR_PAD_LEFT);
    	$ls_cadena .= "DEBITO  {$ls_numref}{$ls_letrif}{$ls_numrif}{$ls_nomemp}{$li_dia}/{$li_mes}/{$li_ano}{$ls_tipcue}{$ls_numcue}{$ld_monto}VEB40\r\n";
    	
    	//Registro de creditos
    	$conCre = 0;
    	foreach ($aa_credito as $credito)
        {
			$conCre++;
			$nro = str_pad($conCre, 2, '0', STR_PAD_LEFT);
			$arrCredito = $this->uf_data_solicitudes($credito['numsol'], $credito['tipdes']);
			$ld_moncre  = str_pad(number_format(abs($credito['monsol']),2,',',''),18,'0',STR_PAD_LEFT);
			//Registro de debito
			$ls_cadena .= "DEBITO  {$ls_numrefdet}{$nro}{$ls_letrif}{$ls_numrif}{$ls_nomemp}{$li_dia}/{$li_mes}/{$li_ano}{$ls_tipcue}{$ls_numcue}{$ld_moncre}VEF40\r\n";
			
			$ls_credito = substr(trim($credito['numsol']), -8);
			$ls_nombre =  substr($arrCredito['nombre'],0,30);
			$ls_nombre =  str_pad($ls_nombre,30,' ',STR_PAD_RIGHT);
			$ls_email   = substr(trim($arrCredito['email']),0,50);
    		$ls_email  = str_pad($ls_email,54,' ',STR_PAD_RIGHT);
    		$ls_cadena .= "CREDITO {$ls_credito}{$arrCredito['rif']}{$ls_nombre}{$arrCredito['tipcta']}{$arrCredito['nrocta']}{$ld_moncre}{$arrCredito['tippag']}{$arrCredito['banco']}       {$ls_email}\r\n";
    		$conCre++;
    	}
    	$conCre = str_pad($conCre, 5, '0', STR_PAD_LEFT);
    	$ls_cadena .= "TOTAL   {$conCre}{$conCre}{$ld_monto}";
    	if ($ls_creararchivo)
		{
    		if (@fwrite($ls_creararchivo,$ls_cadena)===false)
			{
    			$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
    			$lb_valido=false;
    		}
    	}
    	else
		{
    		$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
    		$lb_valido=false;
    	}
    	
    	@fclose($ls_creararchivo);
    	if ($lb_valido)
		{
    		$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
    	}
    	else
		{
    		$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
    	}
    	
    	
    	return $lb_valido;
    }// uf_metodo_banco_venezuela_proveedores
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_proveedores2019($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot,$as_numref,$aa_credito)
    {
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	//         Function: uf_metodo_banco_venezuela_proveedores2019
    	//           Access: public
    	//        Arguments: aa_ds_banco // arreglo (datastore) datos banco
    	//                   ad_fecproc // Fecha de procesamiento
    	//                   as_codcuenta // codigo de cuenta
    	//                   adec_montot // total a depositar
    	//      Description: genera el archivo txt a disco para el Banco Venezuela para pago de proveedores
    	//       Creado Por: Ing. Luis Anibal Lang
    	// Fecha Creacion: 05/02/2019
    	// Modificado Por:                                         Fecha Utima Modificacion :
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$lb_valido=true;
    	   
    	//Creando el archivo txt prueba
    	$ls_nombrearchivo=$as_ruta."/venezuela_proveedores.txt";
    	if (file_exists("$ls_nombrearchivo"))
		{
    		if(@unlink("$ls_nombrearchivo")===false)
			{
    			$lb_valido=false;
    		}
    		else
			{
    			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
    		}
    	}
    	else
		{
    		$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
    	}
    		
    	//Registro Cabecera 
    	$li_dia=substr($ad_fecproc,0,2);
    	$li_mes=substr($ad_fecproc,3,2);
    	$li_ano=substr($ad_fecproc,6,4);
		$as_numref = intval(preg_replace('/[^0-9]+/', '', $as_numref), 10);     		
    	$ls_numref = substr(trim($as_numref), -8);
    	$ls_numref = str_pad($ls_numref, 8, '0', STR_PAD_LEFT);
    	$ls_rifemp = trim($_SESSION["la_empresa"]["rifemp"]);
    	$ls_letrif = substr($ls_rifemp,0,1);
    	$ls_numrif = substr($ls_rifemp,1,15);
    	$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
    		
    	$arrCueDeb = $this->uf_tipo_cuenta_debito(trim($as_codcuenta));
    	$ls_numnego = str_pad(trim($arrCueDeb['numnego']), 8, '0', STR_PAD_LEFT);
    	$ls_tipcue = $arrCueDeb['tipcta'];
    //	$ls_cadena = "HEADER  {$ls_numref}{$ls_numnego}{$ls_letrif}{$ls_numrif}{$li_dia}/{$li_mes}/{$li_ano}{$li_dia}/{$li_mes}/{$li_ano}\r\n";
    	   		
    	//Registro de debito
    	$ls_nomemp = substr(trim($_SESSION["la_empresa"]["nombre"]), 0, 35);
    	$ls_numcue = substr(trim($as_codcuenta), 0, 20);
    	$ld_monto  = str_pad(number_format(abs($adec_montot),2,',',''),18,'0',STR_PAD_LEFT);
   //	$ls_cadena .= "DEBITO  {$ls_numref}{$ls_letrif}{$ls_numrif}{$ls_nomemp}{$li_dia}/{$li_mes}/{$li_ano}{$ls_tipcue}{$ls_numcue}{$ld_monto}VEB40\r\n";
    	
    	//Registro de creditos
    	$conCre = 0;
    	foreach ($aa_credito as $credito)
		{
    		$arrCredito = $this->uf_data_solicitudes($credito['numsol'], $credito['tipdes']);
    	    $ls_credito = substr(trim($credito['numsol']), -8);
    		$ld_moncre  = str_pad(number_format(abs($credito['monsol']),2,',',''),18,'0',STR_PAD_LEFT);
			$ls_nombre =  substr($arrCredito['nombre'],0,30);
			$ls_nombre =  str_pad($ls_nombre,30,' ',STR_PAD_RIGHT);
			$ls_email   = substr(trim($arrCredito['email']),0,50);
    		$ls_email  = str_pad($ls_email,54,' ',STR_PAD_RIGHT);
    		//$ls_cadena .= "CREDITO {$ls_credito}{$arrCredito['rif']}{$ls_nombre}{$arrCredito['tipcta']}{$arrCredito['nrocta']}{$ld_moncre}{$arrCredito['tippag']}{$arrCredito['banco']}       {$ls_email}\r\n";
    		$ls_cadena .= " {$ls_nombre}{$ls_credito}{$arrCredito['rif']}{$arrCredito['tipcta']}{$arrCredito['nrocta']}{$ld_moncre}{$arrCredito['tippag']}{$arrCredito['banco']}  \r\n";
    		$conCre++;
    	}
    	$conCre = str_pad($conCre, 5, '0', STR_PAD_LEFT);
    	if ($ls_creararchivo)
		{
    		if (@fwrite($ls_creararchivo,$ls_cadena)===false)
			{
    			$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
    			$lb_valido=false;
    		}
    	}
    	else
		{
    		$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
    		$lb_valido=false;
    	}
    	
    	@fclose($ls_creararchivo);
    	if ($lb_valido)
		{
    		$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
    	}
    	else
		{
    		$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
    	}
    	
    	
    	return $lb_valido;
    }// uf_metodo_banco_venezuela_proveedores
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
 	function uf_metodo_bod_proveedores($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot,$as_numref,$aa_credito)
    {
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	//         Function: uf_metodo_banco_venezuela_proveedores
    	//           Access: public
    	//        Arguments: aa_ds_banco // arreglo (datastore) datos banco
    	//                   ad_fecproc // Fecha de procesamiento
    	//                   as_codcuenta // codigo de cuenta
    	//                   adec_montot // total a depositar
    	//      Description: genera el archivo txt a disco para el Banco Venezuela para pago de proveedores
    	//       Creado Por: Ing. Gerardo Cordero
    	// Fecha Creacion: 15/08/2014
    	// Modificado Por:                                         Fecha Utima Modificacion :
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$lb_valido=true;
    	
    	//Creando el archivo
    	$ls_nombrearchivo=$as_ruta."/bod_proveedores.txt";
    	if (file_exists("$ls_nombrearchivo")) {
    		if(@unlink("$ls_nombrearchivo")===false) {
    			$lb_valido=false;
    		}
    		else {
    			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
    		}
    	}
    	else {
    		$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
    	}
    		
    	//Registro Cabecera 
    	$li_dia=substr($ad_fecproc,0,2);
    	$li_mes=substr($ad_fecproc,3,2);
    	$li_ano=substr($ad_fecproc,6,4);
    		
    	$ls_identi = str_pad('Proveedores', 20, ' ',STR_PAD_RIGHT);
    	$ls_rifemp = trim($_SESSION["la_empresa"]["rifemp"]);
    	$ls_letrif = substr($ls_rifemp,0,1);
    	$ls_numrif = substr($ls_rifemp,1,15);
    	$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
    	$ls_numref = substr(trim($as_numref), -9);
    	$ls_numref = str_pad($ls_numref, 9, '0', STR_PAD_LEFT);
    	
    		
    	$arrCueDeb = $this->uf_tipo_cuenta_debito(trim($as_codcuenta));
    	$ls_numnego = str_pad(trim($arrCueDeb['numnego']), 17, '0', STR_PAD_LEFT);
    	$ld_monto  = str_replace(',', '', $adec_montot);
    	$ld_monto  = str_replace('.', '', $ld_monto);
    	$ld_monto  = str_pad($ld_monto, 17, '0', STR_PAD_LEFT);
    	$li_numope = str_pad(count((Array)$aa_credito), 6, '0', STR_PAD_LEFT);
    	$ls_blanco = str_pad(" ",158," ",STR_PAD_RIGHT);
    	$ls_cadena = "01{$ls_identi}{$ls_letrif}{$ls_numrif}{$ls_numnego}{$ls_numref}{$li_ano}{$li_mes}{$li_dia}{$li_numope}{$ld_monto}VEB{$ls_blanco}  \r\n";
    	   		
    	$conCre = 0;
    	foreach ($aa_credito as $credito) {
    		$arrCredito = $this->uf_data_solicitudes($credito['numsol'], $credito['tipdes']);
    		$ld_moncre  = str_replace(',', '', $credito['monsol']);
    		$ld_moncre  = str_replace('.', '', $ld_moncre);
    		$ld_moncre  = str_pad($ld_moncre, 15, '0', STR_PAD_LEFT);
    		$ld_monret  = str_pad('', 15, '0', STR_PAD_LEFT);
    		$nombre     = str_pad($arrCredito['nombre'], 60, ' ',STR_PAD_RIGHT);
    		$descripcion = substr($arrCredito['consol'], 0, 30);
    		$ls_email = str_pad(substr($arrCredito['email'], 0, 40),40," ",STR_PAD_RIGHT);
    		$telef = substr(preg_replace("/\D/", '', trim($arrCredito['telef'])), 0, 11);
    		$ls_telef = str_pad($telef, 11, '0', STR_PAD_LEFT);
    		$ls_blanco2 = str_pad(" ",20," ",STR_PAD_RIGHT);
    		$ls_modpag = $arrCredito['modpag'];
    		if ($credito['cmbmodpag'] == 'CHQ' || $credito['cmbmodpag'] == 'EFE') {
    			$ls_modpag = $credito['cmbmodpag'];
    		}
    		$li_dia=substr($credito['fecpag'],0,2);
    		$li_mes=substr($credito['fecpag'],3,2);
    		$li_ano=substr($credito['fecpag'],6,4);
    		$ls_cadena .= "02{$arrCredito['rif']}{$nombre}{$ls_numref}{$descripcion}{$ls_modpag}{$arrCredito['nrocta']}{$arrCredito['codban']}{$li_ano}{$li_mes}{$li_dia}{$ld_moncre}VEB{$ld_monret}{$ls_email}{$ls_telef}{$ls_blanco2} \r\n";
    		$conCre++;
    	}
    	
    	if ($ls_creararchivo) {
    		if (@fwrite($ls_creararchivo,$ls_cadena)===false) {
    			$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
    			$lb_valido=false;
    		}
    	}
    	else {
    		$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
    		$lb_valido=false;
    	}
    	
    	@fclose($ls_creararchivo);
    	if ($lb_valido) {
    		$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
    	}
    	else {
    		$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
    	}
    	
    	
    	return $lb_valido;
    }// uf_metodo_banco_mercantil_proveedores
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banesco_proveedores($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot,$as_numref,$aa_credito)
    {
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	//         Function: uf_metodo_banco_venezuela_proveedores
    	//           Access: public
    	//        Arguments: aa_ds_banco // arreglo (datastore) datos banco
    	//                   ad_fecproc // Fecha de procesamiento
    	//                   as_codcuenta // codigo de cuenta
    	//                   adec_montot // total a depositar
    	//      Description: genera el archivo txt a disco para el Banco Venezuela para pago de proveedores
    	//       Creado Por: Ing. Gerardo Cordero
    	// Fecha Creacion: 15/08/2014
    	// Modificado Por:                                         Fecha Utima Modificacion :
    	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    	$lb_valido=true;
    	
    	//Creando el archivo
    	$ls_nombrearchivo=$as_ruta."/banesco_proveedores.txt";
    	if (file_exists("$ls_nombrearchivo")) {
    		if(@unlink("$ls_nombrearchivo")===false) {
    			$lb_valido=false;
    		}
    		else {
    			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
    		}
    	}
    	else {
    		$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
    	}
    		
    	//Registro Cabecera 
    	$li_dia=substr($ad_fecproc,0,2);
    	$li_mes=substr($ad_fecproc,3,2);
    	$li_ano=substr($ad_fecproc,6,4);
    		
    	$ls_numref = str_pad(trim($as_numref),35," ",STR_PAD_RIGHT);
    	$ls_rifemp = trim($_SESSION["la_empresa"]["rifemp"]);
    	$ls_letrif = substr($ls_rifemp,0,1);
    	$ls_numrif = substr($ls_rifemp,1,15);
    	$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
    	
    		
    	$arrCueDeb = $this->uf_tipo_cuenta_debito(trim($as_codcuenta));
    	$ls_numnego = str_pad(trim($arrCueDeb['numnego']), 8, '0', STR_PAD_LEFT);
    	$ls_tipcue = $arrCueDeb['tipcta'];
    	$ls_asocia = str_pad('BANESCO', 15, ' ',STR_PAD_RIGHT);
    	$ls_cadena = "HDR{$ls_asocia}ED  95BPAYMULLP  \r\n";
    	$ls_blanco = str_pad(" ",32," ",STR_PAD_RIGHT);
    	$ls_cadena .= "01SCV{$ls_blanco}9{$ls_numref}{$li_ano}{$li_mes}{$li_dia}103001  \r\n";
    	   		
    	//Registro de debito
    	$ls_nomemp = substr(trim($_SESSION["la_empresa"]["nombre"]), 0, 35);
    	$ls_nomemp = str_pad($ls_nomemp,35," ",STR_PAD_RIGHT);
    	$ls_numcue = substr(trim($as_codcuenta), 0, 20);
    	$ld_monto  = number_format(abs($adec_montot),2,',','');
    	$ld_monto  = str_replace(',', '', $ld_monto);
    	$ld_monto = str_pad($ld_monto, 15, '0', STR_PAD_LEFT);
    	$ls_numref2 = substr($ls_numref, 0, 32);
    	$ls_blanco2 = str_pad(" ",7," ",STR_PAD_RIGHT);
    	$ls_banco = str_pad('BANESCO', 11, ' ',STR_PAD_RIGHT);
    	$ls_cadena .= "02{$ls_numref2}{$ls_letrif}{$ls_numrif}{$ls_blanco2}{$ls_nomemp}{$ld_monto}VEF{$ls_numcue}{$ls_banco}{$li_ano}{$li_mes}{$li_dia} \r\n";
    	
    	//Registro de creditos
    	$conCre = 0;
    	$ls_blanco3 = str_pad(" ",201," ",STR_PAD_RIGHT);
    	foreach ($aa_credito as $credito) {
    		$arrCredito = $this->uf_data_solicitudes($credito['numsol'], $credito['tipdes']);
    		$ld_moncre  = number_format(abs($credito['monsol']),2,',','');
    		$ld_moncre  = str_replace(',', '', $ld_moncre);
    		$ld_moncre  = str_pad($ld_moncre, 15, '0', STR_PAD_LEFT);
    		$ls_numsol  = str_pad(substr($credito['numsol'], -8), 30, ' ',STR_PAD_RIGHT);
    		$ls_nrcuen  = str_pad($arrCredito['nrocta'], 30, ' ',STR_PAD_RIGHT);
    		$ls_noben  = str_pad($arrCredito['nombre'], 70, ' ',STR_PAD_RIGHT);
    		$ls_modpag = '425';
    		if ($arrCredito['codban'] == '0134') {
    			$ls_modpag = '42';
    		}
    		$ls_cadena .= "03{$ls_numsol}{$ld_moncre}VEF{$ls_nrcuen}{$arrCredito['codban']}   {$arrCredito['rif']}{$ls_noben}{$ls_blanco3}{$ls_modpag} \r\n";
    		$conCre++;
    	}
    	$ls_numref3 = substr($ls_numref, 0, 30);
    	$ls_blanco4 = str_pad(" ",70," ",STR_PAD_RIGHT);
    	$ls_cadena .= "04{$ld_monto}{$ld_monto}{$li_ano}{$li_mes}{$li_dia}{$ls_numref3}{$ls_blanco4}380VEFVEF \r\n";
    	$ls_cadena .= "05211VEF000000000000000000{$ls_blanco4} \r\n";
    	$conCre  = str_pad($conCre, 15, '0', STR_PAD_LEFT);
    	$ls_cadena .= "06000000000000001{$conCre}{$ld_monto} \r\n";
    	if ($ls_creararchivo) {
    		if (@fwrite($ls_creararchivo,$ls_cadena)===false) {
    			$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
    			$lb_valido=false;
    		}
    	}
    	else {
    		$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
    		$lb_valido=false;
    	}
    	
    	@fclose($ls_creararchivo);
    	if ($lb_valido) {
    		$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
    	}
    	else {
    		$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
    	}
    	
    	
    	return $lb_valido;
    }// uf_metodo_banco_venezuela_proveedores
    //-----------------------------------------------------------------------------------------------------------------------------------

    function uf_data_solicitudes($numsol, $tipdes) {
    	$arrCredito = array();
    	require_once("../base/librerias/php/general/sigesp_lib_include.php");
    	$io_include=new sigesp_include();
    	$io_conexion=$io_include->uf_conectar();
    	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
    	$io_sql=new class_sql($io_conexion);
    	
    	if ($tipdes == 'P'){
    		$cadenaSQL = "SELECT PRO.rifpro, PRO.nompro, PRO.ctaban, PRO.tipcueban, BAN.codswift, SOL.consol, BAN.codsudeban, PRO.email,
    							 PRO.telpro AS telef
    						FROM cxp_solicitudes SOL
    							INNER JOIN rpc_proveedor PRO ON SOL.codemp=PRO.codemp AND SOL.cod_pro=PRO.cod_pro
    							INNER JOIN scb_banco BAN ON PRO.codemp=BAN.codemp AND PRO.codban=BAN.codban
    							WHERE SOL.codemp = '{$_SESSION["la_empresa"]["codemp"]}'
    						    AND SOL.numsol = '{$numsol}'";
    	}
    	else {
    		$ls_concat = $io_conexion->Concat('BEN.nombene', "' '", 'BEN.apebene');
    		$cadenaSQL = "SELECT SOL.ced_bene, BEN.nacben, {$ls_concat} AS nombre, BEN.ctaban, BEN.tipcuebanben AS tipcueban, BAN.codswift, 
    							 SOL.consol, BAN.codsudeban, BEN.email, BEN.celbene AS telef
    						FROM cxp_solicitudes SOL
    						INNER JOIN rpc_beneficiario BEN ON SOL.codemp=BEN.codemp AND SOL.ced_bene=BEN.ced_bene
    						INNER JOIN scb_banco BAN ON BEN.codemp=BAN.codemp AND BEN.codban=BAN.codban
    						WHERE SOL.codemp = '{$_SESSION["la_empresa"]["codemp"]}' 
    						AND SOL.numsol = '{$numsol}'";
    	}
    	   
    	$rs_data= $io_sql->select($cadenaSQL);
    	if($rs_data===false) {
    		$this->io_mensajes->message("CLASE->Report METODO->uf_data_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
    	}
    	else {
    		if ($tipdes == 'P') {
    			$ls_rifpro = trim($rs_data->fields['rifpro']);
    			$ls_letrif = substr($ls_rifpro,0,1);
    			$ls_numrif = substr($ls_rifpro,1,15);
    			$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
    			$ls_nombre = substr($rs_data->fields['nompro'], 0, 30);
    		}
    		else {
    			$ls_cedula = trim($rs_data->fields['ced_bene']);
    			$ls_letrif = trim($rs_data->fields['nacben']);
    			$ls_numrif = str_pad($ls_cedula, 9, '0', STR_PAD_LEFT);
    			$ls_nombre = substr($rs_data->fields['nombre'], 0, 30);
    		}
    		$ls_swiftban  = $rs_data->fields['codswift'];
    		$ls_tipopago  = '00';
    		if($ls_swiftban == 'VZLAVECA') {
    			$ls_tipopago  = '10';
    		}
    		$ls_tipcta = '00';
    		if ($rs_data->fields['tipcueban'] == '002') {
    			$ls_tipcta = '01';
    		}
    		
    		$ls_modpago = 'BAN';
    		if($ls_swiftban == 'BODEVE2M') {
    			$ls_modpago  = 'CTA';
    		}
    		
    		$arrCredito['rif']    = $ls_letrif.$ls_numrif;
    		$arrCredito['nombre'] = $ls_nombre;
    		$arrCredito['tipcta'] = $ls_tipcta;
    		$arrCredito['nrocta'] = substr(trim($rs_data->fields['ctaban']), 0, 20);
    		$arrCredito['banco']  = $ls_swiftban;
    		$arrCredito['tippag'] = $ls_tipopago;
    		$arrCredito['consol'] = $rs_data->fields['consol'];
    		$arrCredito['modpag'] = $ls_modpago;
    		$arrCredito['codban'] = $rs_data->fields['codsudeban'];
    		$arrCredito['email']  = $rs_data->fields['email'];
    		$arrCredito['telef']  = $rs_data->fields['telef'];
    	}
    	unset($rs_data);
    
    	return $arrCredito;
    }
    
    function uf_tipo_cuenta_debito($numcta)
	{
    	$arrDatCtaDeb = array();
    	$ls_tipcta = '00';
    	$arrCredito = array();
    	require_once("../base/librerias/php/general/sigesp_lib_include.php");
    	$io_include=new sigesp_include();
    	$io_conexion=$io_include->uf_conectar();
    	require_once("../base/librerias/php/general/sigesp_lib_sql.php");
    	$io_sql=new class_sql($io_conexion);
    	
    	$cadenaSQL = "SELECT codtipcta, numnego
    					FROM scb_ctabanco 
    					WHERE codemp = '{$_SESSION["la_empresa"]["codemp"]}' 
    					AND TRIM(ctabanext) = '{$numcta}'"; 
    	
    	$rs_data= $io_sql->select($cadenaSQL);
    	if($rs_data===false) {
    		$this->io_mensajes->message("CLASE->Report METODO->uf_data_solicitudes ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
    	}
    	else {
    		if (!$rs_data->EOF) {
    			if ($rs_data->fields['codtipcta'] == '002') {
    				$ls_tipcta = '01' ;
    			}
    			$arrDatCtaDeb['tipcta']  = $ls_tipcta;
    			$arrDatCtaDeb['numnego'] = $rs_data->fields['numnego'];
    		}
    	}
    	
    	return $arrDatCtaDeb;
    }
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_mi_casa($as_ruta,$rs_data)
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_mi_casa
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //      Description: genera el archivo txt a disco para  el banco Mi Casa para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,12);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,12);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,10);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],9);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_mi_casa
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_provincial_guanare($as_ruta,$rs_data,$ad_fecproc)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_provincial_guanare
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //      Description: genera el archivo txt a disco para  el banco provincial guanare para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,17);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",15); 
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_nomper." ".$ls_apeper," ",94);
                $ls_cadena="02".$ls_codcueban.$ls_nacper.$ls_cedper.$ldec_neto.$ls_personal."*"."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_provincial_guanare
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_e_provincial_04($as_ruta,$rs_data)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_e_provincial_04
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //      Description: genera el archivo txt a disco para  el banco provincial guanare para pago de nomina
        //       Creado Por: Ing. Yesenia Moreno
        // Fecha Creacion: 13/07/2011                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,17);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper,"0",15); 
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_nomper." ".$ls_apeper," ",40);
                $ls_espacio=" ";
                $ls_espacio=$this->io_funciones->uf_rellenar_der($ls_espacio," ",55);
                $ls_cadena="02".$ls_codcueban.$ls_nacper.$ls_cedper.$ldec_neto.$ls_personal.$ls_espacio."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_e_provincial_04
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_provincial($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_provincial
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcuenta // cï¿½digo de cuenta a debitar
        //                 adec_montot // monto total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Provincial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","",$this->ls_rifemp)," ",10);
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,150);
                $ldec_monpre=(substr($ls_cad_previa,31,17)/100);
                $li_countregprev=(substr($ls_cad_previa,24,7));
                $ldec_monacu=($ldec_monpre + $adec_montot);
                $li_countregacum=$li_countregprev+$li_count;
            }
            else
            {    //Registro Cabecera (Dï¿½bito)
                $ls_codcueban=substr($as_codcuenta,0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_totdep=$adec_montot*100;  
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,17);
                $li_contador=$this->io_funciones->uf_cerosizquierda($li_count,7);        
                $ls_fecha=date("Ymd");
                $ls_disponible="                                              ";
                $ls_cadena="01"."01".$ls_codcueban.$li_contador.$ldec_totdep."VEB".$ls_fecha.$ls_rifemp."        ".$this->ls_nomemp.$ls_disponible."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,17);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",15);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper," ",40);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_otrosdatos=$this->io_funciones->uf_rellenar_der(" "," ",30);
                $ls_referencia=$this->io_funciones->uf_rellenar_der(" "," ",8);
                $ls_disponible=$this->io_funciones->uf_rellenar_der(" "," ",15);
                $ls_cadena="02".$ls_codcueban.$ls_nacper.$ls_cedper.$ldec_neto.$ls_personal.$ls_otrosdatos."00".$ls_referencia.$ls_disponible."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=substr($ls_cad_previa,48,102);
                $ldec_montoacumulado=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,17);
                $li_reg_acumulado=$this->io_funciones->uf_cerosizquierda($li_countregacum,7);
                $ls_cadena=substr($ls_cad_previa,0,24).$li_reg_acumulado.$ldec_montoacumulado.$ls_reemplazar;
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_provincial
    //-----------------------------------------------------------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_bicentenario($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_bicentenario
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcuenta // cï¿½digo de cuenta a debitar
        //                 adec_montot // monto total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Provincial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/bicentenario.txt";
        $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","",$this->ls_rifemp)," ",10);
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,150);
                $ldec_monpre=(substr($ls_cad_previa,31,17)/100);
                $li_countregprev=(substr($ls_cad_previa,24,7));
                $ldec_monacu=($ldec_monpre + $adec_montot);
                $li_countregacum=$li_countregprev+$li_count;
            }
            else
            {    //Registro Cabecera (Dï¿½bito)
                $ls_codcueban=substr($as_codcuenta,0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_totdep=$adec_montot*100;  
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,17);
                $li_contador=$this->io_funciones->uf_cerosizquierda($li_count,7);        
                $ls_fecha=date("Ymd");
                $ls_disponible="                                              ";
                $ls_cadena="01"."01".$ls_codcueban.$li_contador.$ldec_totdep."VEB".$ls_fecha.$ls_rifemp."        ".$this->ls_nomemp.$ls_disponible."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,17);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",15);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper," ",40);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_otrosdatos=$this->io_funciones->uf_rellenar_der(" "," ",30);
                $ls_referencia=$this->io_funciones->uf_rellenar_der(" "," ",8);
                $ls_disponible=$this->io_funciones->uf_rellenar_der(" "," ",15);
                $ls_cadena="02".$ls_codcueban.$ls_nacper.$ls_cedper.$ldec_neto.$ls_personal.$ls_otrosdatos."00".$ls_referencia.$ls_disponible."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=substr($ls_cad_previa,48,102);
                $ldec_montoacumulado=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,17);
                $li_reg_acumulado=$this->io_funciones->uf_cerosizquierda($li_countregacum,7);
                $ls_cadena=substr($ls_cad_previa,0,24).$li_reg_acumulado.$ldec_montoacumulado.$ls_reemplazar;
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_bicentenario
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_bicentenario2($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_bicentenario2
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcuenta // cï¿½digo de cuenta a debitar
        //                 adec_montot // monto total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Provincial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/bicentenario.txt";
        $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","",$this->ls_rifemp)," ",10);
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,150);
                $ldec_monpre=(substr($ls_cad_previa,31,17)/100);
                $li_countregprev=(substr($ls_cad_previa,24,7));
                $ldec_monacu=($ldec_monpre + $adec_montot);
                $ldec_monacu=($ldec_monacu*100);       
                $ldec_monacu= number_format(abs($ldec_monacu), 2, '.', '');
                $ldec_monacu= str_replace('.', '', $ldec_monacu);
                $ldec_monacu= str_pad($ldec_monacu, 15, 0, 0);
                $li_countregacum=$li_countregprev+$li_count;
            }
            else
            {    //Registro Cabecera (Dï¿½bito)
                $ls_codcueban=substr($as_codcuenta,0,20);      
                $ls_monto= number_format(abs($adec_montot), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 17, 0, 0);
                $li_contador=$this->io_funciones->uf_cerosizquierda($li_count,4);        
                $ls_fecha=date("Ymd");
                $ls_disponible="                                              ";
                $ls_cadena=$ls_codcueban.$ls_fecha.$ls_monto.$li_contador."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,12);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,0,10);
                $ls_codempnom=substr($as_codempnom,0,4);
                $ls_cadena=$ls_codempnom.$ldec_neto.$ls_codcueban.$ls_cedper."00000"."0"."00"."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_bicentenario2
//-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_bicentenario2019($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$as_codempnom,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_bicentenario2019
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcuenta // cï¿½digo de cuenta a debitar
        //                 adec_montot // monto total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Provincial para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/bicentenario2019.txt";
        $ls_codempnom="";
        $ls_codofinom="";
        $ls_tipcuedeb="";
        $ls_tipcuecre="";
        $ls_numconvenio="";
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
        $ls_codempnom=$arrResultado['as_codempnom'];
        $lb_valido=$arrResultado['lb_valido'];		
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,6),6);    
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            $ls_codcueban=substr($as_codcuenta,0,20);      
            $ls_monto= number_format(abs($adec_montot), 2, '.', '');
            $ls_monto= str_replace('.', '', $ls_monto);
            $ls_monto= str_pad($ls_monto, 15, 0, 0);
            $ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
            $ad_fecproc=str_replace("/","",$ad_fecproc);
            $ad_fecproc=str_replace("-","",$ad_fecproc);
            
            $li_contador=$this->io_funciones->uf_cerosizquierda($li_count,6);        
            $ls_cadena="10".$ls_codempnom.$ls_codcueban.$ad_fecproc.$li_contador.$ls_monto."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }        
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
                $ls_nacper=$this->io_funciones->uf_trim($rs_data->fields["nacper"]);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,0,9);
                $ls_codempnom=substr($as_codempnom,0,4);
                $ls_cadena="20".$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_bicentenario2
//-----------------------------------------------------------------------------------------------------------------------------------
    
//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_bicentenario($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_bicentenario
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcuenta // cï¿½digo de cuenta a debitar
        //                 adec_montot // monto total a depositar
        //      Description: genera el archivo txt a disco para  el Banco bicentenario para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/bicentenario.txt";
        $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","",$this->ls_rifemp)," ",10);
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,150);
                $ldec_monpre=(substr($ls_cad_previa,31,17)/100);
                $li_countregprev=(substr($ls_cad_previa,24,7));
                $ldec_monacu=($ldec_monpre + $adec_montot);
                $li_countregacum=$li_countregprev+$li_count;
            }
            else
            {    //Registro Cabecera (Dï¿½bito)
                $ls_codcueban=substr($as_codcuenta,0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_totdep=$adec_montot*100;  
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,17);
                $li_contador=$this->io_funciones->uf_cerosizquierda($li_count,4);        
                $ls_fecha=date("Ymd");
                $ls_disponible="                                              ";
                $ls_cadena=$ls_codcueban.$ls_fecha.$ldec_totdep.$li_contador."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,12);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",10);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper," ",40);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_otrosdatos=$this->io_funciones->uf_rellenar_der(" "," ",30);
                $ls_referencia=$this->io_funciones->uf_rellenar_der(" "," ",8);
                $ls_disponible=$this->io_funciones->uf_rellenar_der(" "," ",15);
                $ls_cadena="2158".$ldec_neto.$ls_codcueban.$ls_cedper."00000"."0"."00"."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=substr($ls_cad_previa,48,102);
                $ldec_montoacumulado=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,17);
                $li_reg_acumulado=$this->io_funciones->uf_cerosizquierda($li_countregacum,7);
                $ls_cadena=substr($ls_cad_previa,0,24).$li_reg_acumulado.$ldec_montoacumulado.$ls_reemplazar;
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_bicentenario
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_sofitasa($as_ruta,$rs_data,$ad_fecproc,$as_codmetban)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_sofitasa
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codmetban // cï¿½digo del mï¿½todo a banco
        //      Description: genera el archivo txt a disco para  el banco Sofitasa para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_dia=substr($ad_fecproc,0,2); 
        $ls_mes=substr($ad_fecproc,3,2); 
        $ls_ano=substr($ad_fecproc,8,2); 
        $ls_nombrearchivo=$as_ruta."/sofitasa.txt";
        $ls_codempnom="";
        $ls_codofinom="";
        $ls_tipcuedeb="";
        $ls_tipcuecre="";
        $ls_numconvenio="";
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];		
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,5),5);    
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,12);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],10);
                $ls_cadena=$ls_codempnom.$ls_codcueban.$ls_cedper.$ls_dia.$ls_mes.$ls_ano.$ldec_neto."0"."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_sofitasa
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_caroni_v_2($as_ruta,$rs_data,$ad_fecproc)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_sofitasa
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //      Description: genera el archivo txt a disco para  el banco caroni version_2 para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 10/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {        
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosderecha($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"];
                $li_neto_int=substr($ldec_neto,0,17);
                $li_pos=strpos($ldec_neto,".");
                $li_neto_dec=substr($ldec_neto,$li_pos,3);
                $ldec_montot=$this->io_funciones->uf_trim($li_neto_int.$li_neto_dec);
                $ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,10);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10); 
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper," ",40);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_personal.$ls_codcueban.$ldec_montot."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }                
                $rs_data->MoveNext();        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_caroni_v_2
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuela
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_total_deposito=0;
        $li_total_personal=0;
        $li_max=1000;
        $li_numarc=1;
        $li_personal=0;
        $lb_valido=true;
        $li_count=$rs_data->RecordCount();
        if ($li_count>0)
        {
            $ls_cadena = "";
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $li_total_personal=$li_total_personal+$ldec_neto;
                $ldec_neto=number_format($ldec_neto*100,0,"","");
                $ldec_neto=str_pad($ldec_neto,11,"0",0);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=str_pad($this->io_sno->utf8_to_latin9(substr($ls_apeper." ".$ls_nomper,0,40)),40," ");
                $ls_codtipcueban="";
                $ls_tipcuebanper = $this->io_funciones->uf_trim($rs_data->fields["tipcuebanper"]); 
                if ($ls_tipcuebanper == "C")// cuenta corriente
                {
                    $ls_tipcuebanper = "0";
                    $ls_codtipcueban = "0770";
                }
                if ($ls_tipcuebanper == "A") // cuenta de ahorro
                {
                    $ls_tipcuebanper = "1";
                    $ls_codtipcueban = "1770";
                }
                if ($ls_tipcuebanper == "L") // fondo de activos lï¿½quidos
                {
                    $ls_tipcuebanper = "2";
                    $ls_codtipcueban = "1770";
                }
                $ls_cadena .=$ls_tipcuebanper.$ls_codcueban.$ldec_neto.$ls_codtipcueban.$ls_personal.$ls_cedper."003291  "."\r\n";
                $rs_data->MoveNext();            
                $li_personal++;
                if (($li_personal==$li_max)|| ($rs_data->EOF))
                {
                    $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,40)," ",40);
                    $ls_codcueban=$as_codcuenta;
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                    $li_dia=substr($ad_fecproc,0,2);
                    $li_mes=substr($ad_fecproc,3,2);
                    $li_ano=substr($ad_fecproc,8,2);
                    $ldec_totdep=$li_total_personal;
                    $ldec_totdep=number_format($ldec_totdep*100,0,"","");
                    $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,13);
                    $ls_cabecera="H".$ls_nombre.$ls_codcueban."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdep."03291"." "."\r\n";
                    $ls_nombrearchivo=$as_ruta."/venezuela".$li_numarc.".txt";
                    //Chequea si existe el archivo.
                    if (file_exists("$ls_nombrearchivo"))
                    {
                        $ls_creararchivo=fopen("$ls_nombrearchivo","a+"); // abrimos el archivo que ya existe
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                    }                    
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cabecera)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                    $li_total_personal= 0;
                    $li_personal=0;
                    $ls_cadena="";
                    $li_numarc++;
                    if ($lb_valido)
                    {
                        @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                        $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
                    }
                    else
                    {
                        @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                        $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique.");
                    }    
                }
            }
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuela
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuelaespecial($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuelaespecial
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 02/11/2007
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_total_deposito=0;
        $li_total_personal=0;
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/venezuel.txt";
        $li_count=$rs_data->RecordCount();
        if ($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=fopen("$ls_nombrearchivo","a+"); // abrimos el archivo que ya existe
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            //Registro de Cabecera
            $ldec_totdep=$adec_montot;
            $ldec_totdep=number_format($ldec_totdep,2,".","");  //redondea a 2 decimales
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,90);
                $ldec_monpre=substr($ls_cad_previa,71,13)/100;
                $ldec_monacu=$ldec_monpre+$ldec_totdep;
                $li_total_deposito=$li_total_deposito+$ldec_monacu;
            }
            else
            {
                //Registro Cabecera (Dï¿½bito)
                $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,40)," ",40);
                $ls_codcueban=$as_codcuenta;
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,8,2);
                $li_total_deposito=$li_total_deposito+$ldec_totdep;
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep*100,13);
                $ls_cadena="H".$ls_nombre.$ls_codcueban."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdep."03291"." "."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $li_total_personal=$li_total_personal+$ldec_neto;
                $ldec_neto=number_format($ldec_neto*100,0,"","");
                $ldec_neto=str_pad($ldec_neto,11,"0",0);
                $ls_nomper= strtoupper(trim($rs_data->fields["nomper"]));
                $ls_apeper= strtoupper(trim($rs_data->fields["apeper"]));
                $ls_personal=$this->io_funciones->uf_rellenar_der(substr($ls_apeper." ".$ls_nomper,0,40)," ",40);
                $ls_personal= htmlentities($ls_personal);                
                $ls_personal=str_replace("&ntilde;", "N", $ls_personal);
                $ls_personal=str_replace("&Ntilde;", "N", $ls_personal); 
                $ls_personal=str_replace("Ã‘","N",$ls_personal);
                $ls_personal=str_replace("Ã±","N",$ls_personal);            
                $ls_personal=str_replace("&acute;", "", $ls_personal); 
                $ls_personal=str_replace("&aacute;", "A", $ls_personal);
                $ls_personal=str_replace("&eacute;", "E", $ls_personal); 
                $ls_personal=str_replace("&iacute;", "I", $ls_personal); 
                $ls_personal=str_replace("&oacute;", "O", $ls_personal); 
                $ls_personal=str_replace("&uacute;", "U", $ls_personal); 
                $ls_personal=str_replace("&uuml;", "U", $ls_personal);               
                $ls_personal=str_replace("Ãƒâ€˜","N",$ls_personal);
                $ls_personal=str_replace("Ã�","A",$ls_personal);
                $ls_personal=str_replace("Ã‰","E",$ls_personal);
                $ls_personal=str_replace("Ã�","I",$ls_personal);
                $ls_personal=str_replace("Ã“","O",$ls_personal); 
                $ls_personal=str_replace("Ãš","U",$ls_personal);                            
                $ls_personal=str_replace(",","",$ls_personal);
                $ls_personal=str_replace(".","",$ls_personal);                
                $ls_codtipcueban="";
                $ls_tipcuebanper = $this->io_funciones->uf_trim($rs_data->fields["tipcuebanper"]); 
                if ($ls_tipcuebanper == "C")// cuenta corriente
                {
                    $ls_tipcuebanper = "0";
                    $ls_codtipcueban = "0770";
                }
                if ($ls_tipcuebanper == "A") // cuenta de ahorro
                {
                    $ls_tipcuebanper = "1";
                    $ls_codtipcueban = "1770";
                }
                if ($ls_tipcuebanper == "L") // fondo de activos lï¿½quidos
                {
                    $ls_tipcuebanper = "2";
                    $ls_codtipcueban = "1770";
                }
                $ls_cadena=$ls_tipcuebanper.$ls_codcueban.$ldec_neto.$ls_codtipcueban.$ls_personal.$ls_cedper."003291  "."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,13)."03291"." "."\r\n";
                $ls_cadena=substr_replace($ls_cad_previa,$ls_reemplazar,71);
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                $li_total_personal=0;
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    $li_monto=substr($ls_cadena,21,11)/100;
                    $li_total_personal=$li_total_personal+$li_monto;
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if($lb_valido)
            {
                $li_total_personal=number_format($li_total_personal*100,0,"","");
                $li_total_deposito=number_format($li_total_deposito*100,0,"","");
                if(strval($li_total_personal)!=strval($li_total_deposito))
                {
                    $this->io_mensajes->message("El Monto de la Cabecera Difiere de la suma del Personal Total Personal = ".$li_total_personal." Total Cabecera = ".$li_total_deposito);
                    $lb_valido=@unlink("$ls_nombrearchivo");
                    $lb_valido=false;
                }
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuelaespecial
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_sng($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuela_sng
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela_SNG para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/venezuel.txt";
        $li_count=$rs_data->RecordCount();
        if ($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); // abrimos el archivo que ya existe
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            $ldec_monpre=0;
            $ldec_monacu=0;
            //Registro de Cabecera
            $ldec_totdep = round($adec_montot, 2);
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,90);
                $ldec_monpre=substr($ls_cad_previa,71,13);
                $ldec_monacu=$ldec_monpre+$ldec_totdep;
            }
            else
            {
                //Registro Cabecera (Dï¿½bito)
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcuenta));
                $ls_codcueban=substr($ls_codcueban,0,20);
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban, 20);
                $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,40)," ",40);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,8,2);
                $ldec_totdep=number_format($ldec_totdep,2,".","");
                $ldec_totdep=($ldec_totdep*100);
                $ldec_totdep=number_format($ldec_totdep,0,"","");
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,13);
                $ls_cadena="H".$ls_nombre.$ls_codcueban."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdep."03291"." "."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $ldec_neto=($ldec_neto*100);
                $ldec_neto=number_format($ldec_neto,0,"","");
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,11);
                $ls_nomper=$this->io_funciones->uf_trim($rs_data->fields["nomper"]);
                $ls_apeper=$this->io_funciones->uf_trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40)," ",40);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
                $ls_codtipcueban="";
                $ls_tipcuebanper = $this->io_funciones->uf_trim($rs_data->fields["tipcuebanper"]); 
                if ($ls_tipcuebanper == "C")
                {
                    $ls_tipcuebanper = "0";
                    $ls_codtipcueban = "0770";
                }
                if ($ls_tipcuebanper == "A")
                {
                    $ls_tipcuebanper = "1";
                    $ls_codtipcueban = "1770";
                }
                if ($ls_tipcuebanper == "L")
                {
                    $ls_tipcuebanper = "2";
                    $ls_codtipcueban = "1770";
                }
                $ls_cadena=$ls_tipcuebanper.$ls_codcueban.$ldec_neto.$ls_codtipcueban.$ls_personal.$ls_cedper."003291"."  "."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            } 
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda(($ldec_monacu*100),13)."03291"." "."\r\n";
                $ls_cadena=substr_replace($ls_cad_previa,$ls_reemplazar,71);
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  "." Monto Acumulado: ".round($ldec_monacu));
                }
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuela_sng
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_lara($as_ruta,$rs_data,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_lara
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                   $as_codcuenta // Cï¿½digo de cuenta donde se hace el dï¿½bito
        //      Description: genera el archivo txt a disco para  el Banco Lara para pago de nomina
        //       Creado Por: Ing. Yesenia Moreno
        // Fecha Creacion: 24/08/2006                                 
        // Modificado Por:                                                 Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/BSF0000W.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,80);
                $ldec_monpre=(substr($ls_cad_previa,26,18)/100);
                $li_countregprev=(substr($ls_cad_previa,44,5));
                $ldec_monacu=($ldec_monpre + $adec_montot);
                $li_countregacum=$li_countregprev+$li_count;
            }
            else
            {    //Registro Cabecera (Dï¿½bito)
                $ls_codcueban=str_replace("-","",$as_codcuenta);
                $ls_nomina="0000000000";
                $ls_codcueban=substr($ls_codcueban,0,9);
                $ldec_totdep=$adec_montot*100;  
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,18);
                $li_contador=$this->io_funciones->uf_cerosizquierda($li_count,5);        
                $ls_fecha=date("dm").substr(date("Y"),2,2);
                $ls_cadena="T".$ls_nomina.$ls_codcueban.$ls_fecha.$ldec_totdep.$li_contador.substr($this->ls_nomemp,0,30)."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,9),9);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper." ".$ls_nomper," ",30);
                $ls_codcueban=trim($rs_data->fields["codcueban"]);
                $ls_codcueban=substr(str_replace("-","",$ls_codcueban),0,9);
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,0,9);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,14);
                $ls_disponible=$this->io_funciones->uf_rellenar_der(" "," ",17);
                $ls_cadena="E".$ls_cedper.$ls_personal.$ls_codcueban.$ldec_neto.$ls_disponible."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();            
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=substr($ls_cad_previa,49,30);
                $ldec_montoacumulado=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,18);
                $li_reg_acumulado=$this->io_funciones->uf_cerosizquierda($li_countregacum,5);
                $ls_cadena=substr($ls_cad_previa,0,26).$ldec_montoacumulado.$li_reg_acumulado.$ls_reemplazar;
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_lara
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banpro($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banpro
        //           Access: public 
        //        Arguments: as_ruta // ruta donde se va a guardar el archivo
        //                   aa_ds_banco // arreglo (datastore) datos banco 
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // Cï¿½digo de cuenta donde se hace el dï¿½bito
        //                   adec_montot // Monto total a debitar
        //      Description: genera el archivo txt a disco para BANPRO para pago de nomina
        //       Creado Por: Ing. Yesenia Moreno
        // Fecha Creacion: 21/03/2007                                 
        // Modificado Por:                                                 Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ld_fecha=str_replace("/","-",$ad_fecproc);
        $ls_nombrearchivo=$as_ruta."/nomina".$ld_fecha.".txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$rs_data->fields["cedper"];
                $ls_cedper=substr(str_replace(".","",$ls_cedper),0,9);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$rs_data->fields["codcueban"]));
                $ls_codcueban=substr($ls_codcueban,0,20);
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,15);
                $ls_cadena=$ls_cedper.$ls_codcueban.$ldec_neto."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();                    
            }
            $as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
            $as_codcueban=substr($as_codcueban,0,20);
            $as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban,20);
            $adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot*100,15);
            $ls_cadena="000000000".$as_codcueban.$adec_montot."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }                    

            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banpro
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------    
    function uf_metodo_banco_banfotran($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codmetban)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_banfotran
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 adec_montot // Monto total
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //      Description: genera el archivo txt a disco para  el banco BANFOANDES BANFOTRAN
        //       Creado Por: Ing. Yesenia Moreno
        // Fecha Creacion: 02/05/2007                    
        // Modificado Por:                                         Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/banfotran.txt";
        $li_count=$rs_data->RecordCount();
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,4),4);    
        $ls_numconvenio=$this->io_funciones->uf_cerosizquierda(substr($ls_numconvenio,0,8),8);
        if($li_count>0)
        {
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {        
                // Registro de encabezado
                $as_codcueban=str_replace("-","",trim($as_codcueban));
                $as_codcueban=str_pad(substr($as_codcueban,0,20),20,"0",0);
                $ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
                $ad_fecproc=str_replace("/","",$ad_fecproc);
                $ad_fecproc=str_replace("-","",$ad_fecproc);
				$adec_montot=number_format($adec_montot,2,".","");
                $ldec_montot=($adec_montot*100);
                $ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,17);
                $ldec_totreg=$this->io_funciones->uf_cerosizquierda($li_count,4);
                $ls_cadena = $as_codcueban.$ad_fecproc.$ldec_montot.$ldec_totreg."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if($lb_valido)
            {        
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $ldec_neto=$rs_data->fields["monnetres"];  //debo verificar si en el ds ya viene modificado la coma decimal
                    $ldec_neto=number_format($ldec_neto*100,0,"","");
                    //$ldec_neto=($ldec_neto*100);
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,12);
                    $ls_codcueban=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_rellenar_izq(substr(trim($ls_codcueban),0,20),"0",20);
                    $ls_cedper=$rs_data->fields["cedper"];   //cedula del personal
                    $ls_cedper=$this->io_funciones->uf_trim(str_replace(".","",$ls_cedper));
                    $ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0", 10);
                    $ls_cadena=$ls_codempnom.$ldec_neto.$ls_codcueban.$ls_cedper.$ls_numconvenio."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                    $rs_data->MoveNext();        
                }
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_banfotran
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------    
    function uf_metodo_banco_banfotran_02($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codmetban)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_banfotran_02
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 adec_montot // Monto total
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //      Description: genera el archivo txt a disco para  el banco BANFOANDES BANFOTRAN
        //       Creado Por: Ing. Yesenia Moreno
        // Fecha Creacion: 22/11/2010                    
        // Modificado Por:                                         Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/banfotran.txt";
        $li_count=$rs_data->RecordCount();
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,4),4);    
        if($li_count>0)
        {
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {        
                // Registro de encabezado
                $as_codcueban=str_replace("-","",trim($as_codcueban));
                $as_codcueban=str_pad(substr($as_codcueban,0,20),20,"0",0);
                $ad_fecproc=str_replace("/","",$ad_fecproc);
                $ad_fecproc=str_replace("-","",$ad_fecproc);
                $ldec_montot=($adec_montot*100);
                $ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,17);
                $ldec_totreg=$this->io_funciones->uf_cerosizquierda($li_count,4);
                $ls_cadena = $as_codcueban.$ad_fecproc.$ldec_montot.$ldec_totreg."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if($lb_valido)
            {        
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $ldec_neto=$rs_data->fields["monnetres"];  //debo verificar si en el ds ya viene modificado la coma decimal
                    $ldec_neto=number_format($ldec_neto*100,0,"","");
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,12);
                    $ls_codcueban=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_rellenar_izq(substr(trim($ls_codcueban),0,20),"0",20);
                    $ls_cedper=$rs_data->fields["cedper"];   //cedula del personal
                    $ls_cedper=$this->io_funciones->uf_trim(str_replace(".","",$ls_cedper));
                    $ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0", 10);
                    $ls_cadena=$ls_codempnom.$ldec_neto.$ls_codcueban.$ls_cedper."00000000"."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                    $rs_data->MoveNext();        
                }
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_banfotran_02
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_pagotaquilla($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuela_pagotaquilla
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/venezuela_taquilla.txt";
        $li_count=$rs_data->RecordCount();
        if ($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            $ls_rifemp=str_replace("-","",$this->ls_rifemp);
            $ls_rifemp=str_pad($ls_rifemp,10,"0",0);
            $ls_nomemp=substr($_SESSION["la_empresa"]["nombre"],0,40);
            $ls_nomemp=str_pad($ls_nomemp,40," ");
            $as_codcuenta=str_replace("-","",$as_codcuenta);
            $as_codcuenta=str_replace(" ","",$as_codcuenta);
            $as_codcuenta=substr($as_codcuenta,0,20);
            $as_codcuenta=str_pad($as_codcuenta,20,"0",0);
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$rs_data->fields["cedper"];
                $ls_cedper=substr(str_replace(".","",$ls_cedper),0,10);
                $ls_cedper=str_pad($ls_cedper,10,"0",0);
                $ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
                $ldec_neto=$this->io_funciones->uf_cerosizquierda(round($ldec_neto*100),15);
                $ls_nombre=$rs_data->fields["apeper"]." ".$rs_data->fields["nomper"];
                $ls_nombre=strtolower($ls_nombre);
                $ls_nombre=str_replace(".","",$ls_nombre);
                $ls_nombre=str_replace(",","",$ls_nombre);
                $ls_nombre=str_replace("ï¿½","",$ls_nombre);
                $ls_nombre=str_replace("ï¿½","a",$ls_nombre);
                $ls_nombre=str_replace("ï¿½","e",$ls_nombre);
                $ls_nombre=str_replace("ï¿½","i",$ls_nombre);
                $ls_nombre=str_replace("ï¿½","o",$ls_nombre);
                $ls_nombre=str_replace("ï¿½","u",$ls_nombre);
                $ls_nombre=strtoupper($ls_nombre);
                $ls_nombre=str_pad($ls_nombre,40," ");
                $ls_cadena="G".$ls_rifemp.$ls_nomemp.$as_codcuenta."V".$ls_cedper.$ls_nombre."9117".$ldec_neto."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuela_pagotaquilla
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------    
    function uf_metodo_banco_mercantilonline($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codmetban)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_mercantilonline
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 adec_montot // Monto total
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //      Description: genera el archivo txt a disco para  el banco MERCANTIL ONLINE
        //       Creado Por: Ing. Yesenia Moreno
        // Fecha Creacion: 17/10/2007                    
        // Modificado Por:                                         Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/mercantil_online.txt";
        $li_count=$rs_data->RecordCount();
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda($ls_codempnom,0,6);    
        $ls_numconvenio=$this->io_funciones->uf_cerosizquierda(substr($ls_numconvenio,0,6),6);
        $li_numlote=$this->io_sno->uf_select_config("SNO","GEN_DISK","MERCANIL_ONLINE","1","I");
        $li_numlote=intval($this->io_funciones->uf_trim($li_numlote));
        $lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","MERCANIL_ONLINE",$li_numlote+1,"I");
        $li_numlote=$this->io_funciones->uf_rellenar_izq($li_numlote,"0",15);
        if($li_count>0)
        {
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {        
                // Registro de encabezado
                $ls_tipreg="00";
                $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","0",$this->ls_rifemp),"0",10);
                $ls_desnom=str_pad(substr($rs_data->fields["desnom"],0,20),20," ");
                $ls_banco="105";
                $ls_moneda="VEB";
                $as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
                $li_inicio=strlen($as_codcueban)-10;
                $as_codcueban=substr($as_codcueban,$li_inicio,10);
                $ldec_montot=number_format($adec_montot,2,".","");
                $ldec_montot=($ldec_montot*100);
                $ldec_montot=number_format($ldec_montot,0,"","");
                $ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,15);
                $ldec_totreg=$this->io_funciones->uf_cerosizquierda($li_count,5);
                $ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
                $ad_fecproc=str_replace("/","",$ad_fecproc);
                $ad_fecproc=str_replace("-","",$ad_fecproc);
                $ls_cadena = $ls_tipreg.$ls_codempnom.$ls_rifemp.$ls_desnom.$li_numlote.$ls_banco.$ls_moneda.$as_codcueban.$ldec_montot.$ldec_totreg.$ad_fecproc."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if($lb_valido)
            {        
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $li_tipregistro="01";
                    $ls_nacper=$rs_data->fields["nacper"]; //Nacionalidad del Personal
                    $ls_cedper=$rs_data->fields["cedper"];
                    $ls_cedper=$this->io_funciones->uf_trim(str_replace(".","",$ls_cedper));
                    $ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0", 10);
                    $ls_nomper=rtrim($rs_data->fields["nomper"]);   //Nombre del personal
                    $ls_apeper=rtrim($rs_data->fields["apeper"]);   //Apellido del personal
                    $ls_empleado=$ls_apeper." ".$ls_nomper;
                    $ls_empleado=str_pad(substr($ls_empleado,0,60),60," ");
                    $ls_formapago="1";
                    $ls_banco="105";
                    $ls_codcueban=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $li_inicio=strlen($as_codcueban)-10;
                    $ls_codcueban=substr($ls_codcueban,$li_inicio,10);
                    $ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  //
                    $ldec_neto=$ldec_neto*100;
                    $ldec_neto=number_format($ldec_neto,0,"","");
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
                    $ls_cadena=$li_tipregistro.$ls_nacper.$ls_cedper.$ls_empleado.$ls_formapago.$ls_banco.$ls_codcueban.$ldec_neto.$ldec_neto."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                    $rs_data->MoveNext();            
                }

            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_mercantilonline
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_prepagoabono($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuela
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_total_depositoabono=0;
        $lb_valido=true;
        $ls_nombrearchivo1=$as_ruta."/venezuela_abono.txt";
        $ls_nombrearchivo2=$as_ruta."/venezuela_prepago.txt";
        $li_count=$rs_data->RecordCount();
        $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","",$this->ls_rifemp)," ",10);
        if ($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo1"))
            {
                $ls_creararchivo1=fopen("$ls_nombrearchivo1","a+"); // abrimos el archivo que ya existe
                $lb_adicionado1=true;
            }
            else
            {
                $ls_creararchivo1=@fopen("$ls_nombrearchivo1","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado1=false;
            }
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo2"))
            {
                $ls_creararchivo2=fopen("$ls_nombrearchivo2","a+"); // abrimos el archivo que ya existe
                $lb_adicionado2=true;
            }
            else
            {
                $ls_creararchivo2=@fopen("$ls_nombrearchivo2","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado2=false;
            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Registro de Cabecera Archivo 1
            //$ldec_totdepabono=number_format($aa_ds_banco->data["totalabono"][1],2,".","");
            $ls_pagbanper=$rs_data->fields["pagbanper"];
            if($ls_pagbanper==1)
            {
                $ldec_totdepabono=number_format($rs_data->fields["totalabono"],2,".","");
            }
            else
            {
                $ldec_totdepabono=number_format($rs_data->fields["totalprepago"],2,".","");
            }
            
            if ($lb_adicionado1)
            {
                $ls_cad_previaabono=fgets($ls_creararchivo1,90);
                $ldec_monpreabono=substr($ls_cad_previaabono,71,13)/100;
                $ldec_monacuabono=$ldec_monpreabono+$ldec_totdepabono;
                $li_total_depositoabono=$li_total_depositoabono+$ldec_monacuabono;
            }
            else
            {
                //Registro Cabecera (Dï¿½bito)
                $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,34)," ",40);
                $ls_codcueban=$as_codcuenta;
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=str_pad(substr($ls_codcueban,0,20),20," ",0);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,8,2);
                $li_total_deposito=$li_total_deposito+$ldec_totdep;
                $ldec_totdepabono=$this->io_funciones->uf_cerosizquierda($ldec_totdepabono*100,13);

                $ls_cadena="H".$ls_nombre.$ls_codcueban."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdepabono."03291"." "."\r\n";
                if ($ls_creararchivo1)
                {
                    if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo1);
                    $lb_valido=false;
                }        
            }
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Registro de Cabecera Archivo 2
            $ls_pagbanper=$rs_data->fields["pagbanper"];
            if($ls_pagbanper==0)
            {
                $ldec_totdepprepago=number_format($rs_data->fields["totalabono"],2,".","");
            }
            else
            {
                $ldec_totdepprepago=number_format($rs_data->fields["totalprepago"],2,".","");
            }
            
            if ($lb_adicionado2)
            {
                $ls_cad_previaprepago=fgets($ls_creararchivo2,129);
                $ldec_monpreprepago=substr($ls_cad_previaprepago,71,13)/100;
                $ldec_monacuprepago=$ldec_monpreprepago+$ldec_totdepprepago;
                $li_total_depositoprepago=$li_total_depositoprepago+$ldec_monacuprepago;
                $ldec_cantidadprevia=substr($ls_cad_previaprepago,121,7);
            }
            else
            {
                //Registro Cabecera (Dï¿½bito)
                $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,34)," ",40);
                $ls_codcueban=$as_codcuenta;
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=str_pad(substr($ls_codcueban,0,20),20," ",0);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,8,2);
                $li_total_deposito=$li_total_deposito+$ldec_totdep;
                $ldec_totdepprepago=$this->io_funciones->uf_cerosizquierda($ldec_totdepprepago*100,13);
                $li_cantidad=str_pad($rs_data->fields["nroprepago"],7,"0",0);
                $ls_cadena="H".$ls_nombre."00000000000000000000"."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdepprepago."03291"."   "."0102"."000000000000001".$ls_rifemp.$li_count."P"."\r\n";
                if ($ls_creararchivo2)
                {
                    if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo2);
                    $lb_valido=false;
                }        
            }
            $li_totprepago=0;
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=trim($this->io_funciones->uf_trim($rs_data->fields["cedper"]));
                $ls_cedperaux=$ls_cedper;
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcuebanaux=$ls_codcueban;
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $li_total_personal=$li_total_personal+$ldec_neto;
                $ldec_neto=number_format($ldec_neto*100,0,"","");
                $ldec_neto=str_pad($ldec_neto,11,"0",0);
                $ls_pagbanper=$rs_data->fields["pagbanper"];
                $ls_nomper=rtrim($rs_data->fields["nomper"]);
                $ls_apeper=rtrim($rs_data->fields["apeper"]);
                $ls_personal=ltrim(rtrim(substr($ls_apeper.", ".$ls_nomper,0,40)));
                
                $ls_personal=strtolower($ls_personal);
                $ls_personal=str_replace(".","",$ls_personal);
                $ls_personal=str_replace(",","",$ls_personal);
                $ls_personal=str_replace("ï¿½","n",$ls_personal);
                $ls_personal=str_replace("ï¿½","a",$ls_personal);
                $ls_personal=str_replace("ï¿½","e",$ls_personal);
                $ls_personal=str_replace("ï¿½","i",$ls_personal);
                $ls_personal=str_replace("ï¿½","o",$ls_personal);
                $ls_personal=str_replace("ï¿½","u",$ls_personal);

                $ls_personal=str_replace("ï¿½","N",$ls_personal);
                $ls_personal=str_replace("ï¿½","A",$ls_personal);
                $ls_personal=str_replace("ï¿½","E",$ls_personal);
                $ls_personal=str_replace("ï¿½","I",$ls_personal);
                $ls_personal=str_replace("ï¿½","O",$ls_personal);
                $ls_personal=str_replace("ï¿½","U",$ls_personal);
                $ls_personal=strtoupper($ls_personal);

                $ls_personal=str_pad($ls_personal,40," ");
                $ls_codtipcueban="";
                $ls_tipcuebanper = $this->io_funciones->uf_trim($rs_data->fields["tipcuebanper"]); 
                if ($ls_tipcuebanper == "C")// cuenta corriente
                {
                    $ls_tipcuebanper = "0";
                    $ls_codtipcueban = "0770";
                }
                if ($ls_tipcuebanper == "A") // cuenta de ahorro
                {
                    $ls_tipcuebanper = "1";
                    $ls_codtipcueban = "1770";
                }
                if ($ls_tipcuebanper == "L") // fondo de activos lï¿½quidos
                {
                    $ls_tipcuebanper = "2";
                    $ls_codtipcueban = "1770";
                }
                if($ls_pagbanper==0)
                {
                    $li_totprepago++;
                    $ls_totprepago=str_pad($ls_totprepago,15,"0",0);

                    $ls_cadena="1"."00000000000000000000".$ldec_neto."    ".$ls_personal.$ls_cedper."003291"."0102"."000000000000001".$ls_rifemp."        "."\r\n";
                    if ($ls_creararchivo2)
                    {
                        if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo2);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $ls_cadena=$ls_tipcuebanper.$ls_codcueban.$ldec_neto.$ls_codtipcueban.$ls_personal.$ls_cedper."003291  "."\r\n";
                    if ($ls_creararchivo1)
                    {
                        if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo1);
                        $lb_valido=false;
                    }
                }
                $rs_data->MoveNext();        
            }
            // para el archivo 1
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado1))
            {
                $ls_reemplazar=$ldec_monacuabono=$this->io_funciones->uf_cerosizquierda($ldec_monacuabono*100,13)."03291"." "."\r\n";
                $ls_cadena=substr_replace($ls_cad_previaabono,$ls_reemplazar,71);
                $new_archivo=file("$ls_nombrearchivo1"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo1); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo1"))
                {
                    if(@unlink("$ls_nombrearchivo1")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo1=@fopen("$ls_nombrearchivo1","a+");
                    }
                }
                if($ls_creararchivo1)
                {
                    if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo1);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                $li_total_personal=0;
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    $li_monto=substr($ls_cadena,21,11)/100;
                    $li_total_personal=$li_total_personal+$li_monto;
                    if ($ls_creararchivo1)
                    {
                        if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo1);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacuabono));
                }
                unset($new_archivo);
            }
            // para el archivo 2
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado2))
            {
                $ldec_cantidadprevia=intval($ldec_cantidadprevia);
                $li_cantidad=intval($aa_ds_banco->data["nroprepago"][1]);
                $li_cantidad=$ldec_cantidadprevia+$li_cantidad;
                $li_cantidad=str_pad($li_cantidad,7,"0",0);
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda($ldec_monacuprepago*100,13)."03291"."   "."0102"."000000000000001".$ls_rifemp.$li_cantidad."P"."\r\n";
                $ls_cadena=substr_replace($ls_cad_previaprepago,$ls_reemplazar,71);
                $new_archivo=file("$ls_nombrearchivo2"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo2); //cerramos la conexiï¿½n y liberamos la memoria
                if(file_exists("$ls_nombrearchivo2"))
                {
                    if(@unlink("$ls_nombrearchivo2")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo2=@fopen("$ls_nombrearchivo2","a+");
                    }
                }
                if($ls_creararchivo2)
                {
                    if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo2);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                $li_total_personal=0;
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    $li_monto=substr($ls_cadena,21,11)/100;
                    $li_total_personal=$li_total_personal+$li_monto;
                    if ($ls_creararchivo2)
                    {
                        if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo2);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacuprepago));
                }
                unset($new_archivo);
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("los archivos fueron creados.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar los archivoa por favor verifique.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuela
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_federal($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope,$ad_fdesde,$ad_fhasta,
                                     $adec_montot,$as_tipquincena) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_fondo_comun
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //                 as_codmetban // cï¿½digo de mï¿½todo a banco 
        //                 as_desope // descripcion de operacion
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 05/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_nrodebitos=1; // se inicializa en uno, por que solo hay un registro y no es variable
        $li_nrocreditos=0;
        $ls_mondeb=0;
        $ls_moncre=0;
        $ldec_monto=0;
        $lb_valido=false;        
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $ls_codempnom="";
        $ls_codofinom="";
        $ls_tipcuedeb="";
        $ls_tipcuecre="";
        $ls_numconvenio="";
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,3),3);    
        $li_count=$rs_data->RecordCount();//$aa_ds_banco->getRowCount("codcueban");
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {
                //Registro de Encabezado
                $as_identificacion="001";//USO INTERNO
                $as_campolibre1="00000";//USO INTERNO
                $ai_totalreg=str_pad(($li_count+1),7,"0",0);
                $as_campolibre2="0000";//USO INTERNO
                $as_mespago=substr($ad_fecproc,3,2);
                $as_dia=substr($ad_fecproc,0,2);
                $as_quincena="00";
                //switch($aa_ds_banco->data["tippernom"][1])
                switch($rs_data->fields["tippernom"])
                {
                    case "0": // Semanal
                        $as_quincena="03";
                    break;
                    case "1": // Quincenal
                        if(intval($as_dia)<=15)
                        {
                            $as_quincena="01";
                        }
                        else
                        {
                            $as_quincena="02";
                        }
                    break;
                    case "2": // Mensual
                        $as_quincena=str_pad($as_tipquincena,2,"0",0);
                    break;
                }
                $as_codigemp=$ls_codempnom;//FALTA
                $as_codigemp=substr($as_codigemp,3,3);
                $adec_montot=number_format($adec_montot,2,".","");
                $adec_montot=number_format(($adec_montot*100),0,"","");
                $adec_montot=str_pad($adec_montot,13,"0",0);
                $as_montocred=$adec_montot;
                $as_montodeb=$adec_montot;
                $as_formatcuenta="N";//USO INTERNO
                $as_campolibre3="0000000000000000000";//USO INTERNO
                $this->ls_rifemp=str_replace("-","",$this->ls_rifemp);
                $ld_fecha=str_replace("-","",$ad_fecproc);
                $ld_fecha=str_replace("/","",$ld_fecha);
                $ls_cadena=$as_identificacion.$as_campolibre1.$ai_totalreg.$as_campolibre2.$as_mespago.$as_quincena.$ls_codempnom.$as_montocred.$as_montodeb.$ld_fecha.$as_formatcuenta.$as_campolibre3."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        
            }
            if($lb_valido)
            {
                $as_desope=$this->io_funciones->uf_rellenar_der($as_desope," ",40);
                $ldec_moncre=0;
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $ls_identificacion="770";
                    $ls_codcueban=$rs_data->fields["codcueban"];
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                    $ls_campolibre4="0000";
                    $ls_campolibre5="000000000000000000000000000000000";
                    $ls_nacper=$rs_data->fields["nacper"];
                    $ls_cedper=$rs_data->fields["cedper"];
                    $ls_cedper=str_pad($ls_cedper,10,"0",0);
                    $ldec_neto=$rs_data->fields["monnetres"];
                    $ldec_neto=($ldec_neto*100);  
                    $ldec_moncre=$ldec_moncre+$ldec_neto;
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,13);
                    $ls_cadena=$ls_identificacion.$ls_codcueban.$ls_campolibre4.$as_mespago.$as_quincena.$ls_codempnom.$ldec_neto.$ls_campolibre5."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }    
                    $rs_data->MoveNext();    
                }
                //PARA EL Dï¿½BITO
                $ls_identificacion="670";
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ls_cadena=$ls_identificacion.$ls_codcueban.$ls_campolibre4.$as_mespago.$as_quincena.$ls_codempnom.$adec_montot.$ls_campolibre5;                
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        

            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_federal
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_agricola($as_ruta,$rs_data) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_agricola
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //                 as_codmetban // cï¿½digo de mï¿½todo a banco 
        //                 as_desope // descripcion de operacion
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. Yesenia Moreno 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por:                                                            Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;        
        $ls_nombrearchivo=$as_ruta."/abono.txt";
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=str_pad(trim($rs_data->fields["cedper"]),14,"0",0);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,12),12);
                $ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");
                $ldec_neto=($ldec_neto*100);  
                $ldec_neto=number_format($ldec_neto,0,"","");
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto."0";
                if($li_i<$li_count)
                {
                    $ls_cadena=$ls_cadena."\r\n";
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }
                $rs_data->MoveNext();        
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_agricola
    //-----------------------------------------------------------------------------------------------------------------------------------
    
//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_federal_consolidado($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$as_codmetban,$as_desope,$ad_fdesde,$ad_fhasta, $adec_montot,$as_tipquincena) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_federal_consolidado
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //                 as_codmetban // cï¿½digo de mï¿½todo a banco 
        //                 as_desope // descripcion de operacion
        //      Description: genera el archivo txt a disco para  el Banco Federal
        //       Creado Por: Ing. Marï¿½a Beatriz Unda
        // Fecha Creacion: 12/11/2008                                 
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_nrodebitos=1; // se inicializa en uno, por que solo hay un registro y no es variable
        $li_nrocreditos=0;
        $ls_mondeb=0;
        $ls_moncre=0;
        $ldec_monto=0;
        $lb_valido=false;        
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $ls_codempnom="";
        $ls_codofinom="";
        $ls_tipcuedeb="";
        $ls_tipcuecre="";
        $ls_numconvenio="";
        $arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];
        $ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,3),3);    
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); // abrimos el archivo que ya existe
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            $ldec_mondebpre=0;
            $ldec_moncrepre=0;
            $ldec_mondebacu=0;
            $ldec_moncreacu=0;
            $li_totalreg=0;
            //Registro de Encabezado
                $as_identificacion="001";//USO INTERNO
                $as_campolibre1="00000";//USO INTERNO
                $ai_totalreg=str_pad(($li_count+1),7,"0",0);
                $as_campolibre2="0000";//USO INTERNO
                $as_mespago=substr($ad_fecproc,3,2);
                $as_dia=substr($ad_fecproc,0,2);
                $as_quincena="00";
                //switch($aa_ds_banco->data["tippernom"][1])
                switch($rs_data->fields["tippernom"])
                {
                    case "0": // Semanal
                        $as_quincena="03";
                        break;
                    case "1": // Quincenal
                        if(intval($as_dia)<=15)
                        {
                            $as_quincena="01";
                        }
                        else
                        {
                            $as_quincena="02";
                        }
                        break;
                    case "2": // Mensual
                        $as_quincena=str_pad($as_tipquincena,2,"0",0);
                        break;
                }
            $ldec_totdep = round($adec_montot, 2);
            $adec_montot=number_format($adec_montot,2,".","");
            $adec_montot=number_format(($adec_montot*100),0,"","");
            $adec_montot=str_pad($adec_montot,13,"0",0);
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,81);
                $ldec_mondebpre=substr($ls_cad_previa,26,13);
                $ldec_mondebacu=number_format(($ldec_mondebpre/100),2,'.','')+$ldec_totdep;
                $ldec_moncrepre=substr($ls_cad_previa,39,13);
                $ldec_moncreacu=number_format(($ldec_moncrepre/100),2,'.','')+$ldec_totdep;
                $li_totalregpre=substr($ls_cad_previa,8,7);
                $li_totalregacu=$li_totalregpre+$li_count+1;
            }
            else
            {
                if($lb_valido)
                {
                    
                    $as_codigemp=$ls_codempnom;//FALTA
                    $as_codigemp=substr($as_codigemp,3,3);                    
                    $as_montocred=$adec_montot;
                    $as_montodeb=$adec_montot;
                    $as_formatcuenta="N";//USO INTERNO
                    $as_campolibre3="0000000000000000000";//USO INTERNO
                    $this->ls_rifemp=str_replace("-","",$this->ls_rifemp);
                    $ld_fecha=str_replace("-","",$ad_fecproc);
                    $ld_fecha=str_replace("/","",$ld_fecha);
                    $ls_cadena=$as_identificacion.$as_campolibre1.$ai_totalreg.$as_campolibre2.$as_mespago.$as_quincena.$ls_codempnom.$as_montocred.$as_montodeb.$ld_fecha.$as_formatcuenta.$as_campolibre3."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }        
                }
            }
            if($lb_valido)
            {
                $as_desope=$this->io_funciones->uf_rellenar_der($as_desope," ",40);
                $ldec_moncre=0;
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $ls_identificacion="770";
                    $ls_codcueban=$rs_data->fields["codcueban"];
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                    $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                    $ls_campolibre4="0000";
                    $ls_campolibre5="000000000000000000000000000000000";
                    $ls_nacper=$rs_data->fields["nacper"];
                    $ls_cedper=$rs_data->fields["cedper"];
                    $ls_cedper=str_pad($ls_cedper,10,"0",0);
                    $ldec_neto=$rs_data->fields["monnetres"];
                    $ldec_neto=($ldec_neto*100);  
                    $ldec_moncre=$ldec_moncre+$ldec_neto;
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,13);
                    $ls_cadena=$ls_identificacion.$ls_codcueban.$ls_campolibre4.$as_mespago.$as_quincena.$ls_codempnom.$ldec_neto.$ls_campolibre5."\r\n";
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }    
                    $rs_data->MoveNext();    
                }
                //PARA EL Dï¿½BITO
                $ls_identificacion="670";
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ls_cadena=$ls_identificacion.$ls_codcueban.$ls_campolibre4.$as_mespago.$as_quincena.$ls_codempnom.$adec_montot.$ls_campolibre5."\r\n";                
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }        

            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda($li_totalregacu,7);                
                $ls_cad_previa=substr_replace($ls_cad_previa,$ls_reemplazar,8,7);
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda(($ldec_mondebacu*100),13);                
                $ls_cad_previa=substr_replace($ls_cad_previa,$ls_reemplazar,26,13);
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda(($ldec_moncreacu*100),13);                
                $ls_cadena=substr_replace($ls_cad_previa,$ls_reemplazar,39,13)."\r\n";                
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  "." Numero de registros agregados: ".$li_count. " Numero total de registros ".$li_totalregacu);
                }
            }
                    
            
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_banco_federal_consolidado
    //-----------------------------------------------------------------------------------------------------------------------------------
//-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_provincial_pensiones($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_provincial_pensiones
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcuenta // cï¿½digo de cuenta a debitar
        //                 adec_montot // monto total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Provincial para pago de nomina
        //       Creado Por: Ing. Marï¿½a Beatriz Unda
        // Fecha Creacion: 27/01/2009                                         
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/nomina.txt";
        $ls_rifemp=$this->io_funciones->uf_rellenar_izq(str_replace("-","",$this->ls_rifemp)," ",10);
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");                
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                
            }        
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban," ",20);
                $ldec_neto=$rs_data->fields["monnetres"]*100;
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,17);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",15);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper.", ".$ls_nomper," ",40);
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_otrosdatos=$this->io_funciones->uf_rellenar_der(" "," ",30);
                $ls_referencia=$this->io_funciones->uf_rellenar_der(" "," ",8);
                $ls_disponible=$this->io_funciones->uf_rellenar_der(" "," ",15);
                $ls_cadena="02".$ls_codcueban.$ls_nacper.$ls_cedper.$ldec_neto.$ls_personal.$ls_otrosdatos."00".$ls_referencia.$ls_disponible."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();        
            }
            
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_provincial_pensiones
    //-----------------------------------------------------------------------------------------------------------------------------------
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_pensiones($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuela_pensiones
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_total_deposito=0;
        $li_total_personal=0;
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo1=$as_ruta."/venezuela_10digitos.txt";
        $ls_nombrearchivo2=$as_ruta."/venezuela_20digitos.txt";
        $li_count=$rs_data->RecordCount();
        if ($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo1"))
            {
                $ls_creararchivo1=fopen("$ls_nombrearchivo1","a+"); // abrimos el archivo que ya existe                
            }
            else
            {
                $ls_creararchivo1=@fopen("$ls_nombrearchivo1","a+"); //creamos y abrimos el archivo para escritura
            
            }

            if (file_exists("$ls_nombrearchivo2"))
            {
                $ls_creararchivo2=fopen("$ls_nombrearchivo2","a+"); // abrimos el archivo que ya existe
                
            }
            else
            {
                $ls_creararchivo2=@fopen("$ls_nombrearchivo2","a+"); //creamos y abrimos el archivo para escritura
                
            }

            //Registro de Cabecera
            
                $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,40)," ",40);
                    $ls_codcueban=$as_codcuenta;
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,8,2);
                $li_total_deposito=$li_total_deposito+$ldec_totdep;
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep*100,13);
                $ls_cadena="H".$ls_nombre.$ls_codcueban."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdep."03291"." "."\r\n";
                /*if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }*/        
            $lb_titulo1=false;
            $lb_titulo2=false;
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
                $ls_codcueban=$rs_data->fields["codcueban"];
                    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                if (strlen($ls_codcueban)==10)
                {
                    if($lb_titulo1===false)
                    {                    
                        if ($ls_creararchivo1)
                        {
                            if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
                            {
                                $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
                                $lb_valido = false;
                            }
                            else
                            {
                                $lb_titulo1=true;
                            }
                        }
                        else
                        {
                            $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo1);
                            $lb_valido = false;
                        }
                    }        
                }
                else
                {
                    if($lb_titulo2===false)
                    {                    
                        if ($ls_creararchivo2)
                        {
                            if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
                            {
                                $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
                                $lb_valido = false;
                            }
                            else
                            {
                                $lb_titulo2=true;
                            }
                        }
                        else
                        {
                            $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo2);
                            $lb_valido = false;
                        }
                    }        
                }
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $li_total_personal=$li_total_personal+$ldec_neto;
                $ldec_neto=number_format($ldec_neto*100,0,"","");
                $ldec_neto=str_pad($ldec_neto,11,"0",0);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=$this->io_funciones->uf_rellenar_der(substr($ls_apeper.", ".$ls_nomper,0,40)," ",40);
                $ls_codtipcueban="";
                $ls_tipcuebanper = $this->io_funciones->uf_trim($rs_data->fields["tipcuebanper"]); 
                if ($ls_tipcuebanper == "C")// cuenta corriente
                {
                    $ls_tipcuebanper = "0";
                    $ls_codtipcueban = "0770";
                }
                if ($ls_tipcuebanper == "A") // cuenta de ahorro
                {
                    $ls_tipcuebanper = "1";
                    $ls_codtipcueban = "1770";
                }
                if ($ls_tipcuebanper == "L") // fondo de activos lï¿½quidos
                {
                    $ls_tipcuebanper = "2";
                    $ls_codtipcueban = "1770";
                }
                $ls_cadena=$ls_tipcuebanper.$ls_codcueban.$ldec_neto.$ls_codtipcueban.$ls_personal.$ls_cedper."003291  "."\r\n";
                if (strlen($ls_codcueban)==10)
                {
                    if ($ls_creararchivo1)
                    {
                        if (@fwrite($ls_creararchivo1,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo1);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo1);
                        $lb_valido=false;
                    }
                }
                else
                {
                    if ($ls_creararchivo2)
                    {
                        if (@fwrite($ls_creararchivo2,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo2);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo2);
                        $lb_valido=false;
                    }
                }
                $rs_data->MoveNext();            
            }
            
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuela_pensiones
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_fonz03_militar($as_ruta,$rs_data) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_fonz03_militar
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // cï¿½digo de la cuenta bancaria a debitar 
        //                 as_codmetban // cï¿½digo de mï¿½todo a banco 
        //                 as_desope // descripcion de operacion
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. Maria Beatriz Unda
        // Fecha Creacion: 28/01/2009                                
        // Modificado Por:                     Fecha ultima Modificacion : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;                
        $ls_nombrearchivo=$as_ruta."/fonz03.txt";        
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            if($lb_valido)
            {
                $li_i=0;
                while((!$rs_data->EOF)&&($lb_valido))
                {
                    $li_i=$li_i+1;
                    $ldec_neto=$rs_data->fields["monnetres"];                    
                    $ldec_neto=($ldec_neto*100);  
                    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,18);
                    $ls_cedper=$rs_data->fields["cedper"];
                    //$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
                    $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
                    $ls_relleno=str_pad("0",71,"0",0);
                    if($li_i!=$li_count)
                    {
                        $ls_cadena="2911".$ls_cedper."000"."AFAN"."0000000000".$ldec_neto.$ls_relleno."0"."\r\n";
                    }
                    else
                    {
                        $ls_cadena="2911".$ls_cedper."000"."AFAN"."0000000000".$ldec_neto.$ls_relleno."0";
                    }
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido = false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }        
                    $rs_data->MoveNext();
                }
            }
            
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_fonz03_militar
    //-----------------------------------------------------------------------------------------------------------------------------------
      function uf_metodo_fonz03($as_ruta,$rs_data) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_fonz03_militar
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // c?digo de la cuenta bancaria a debitar 
        //                 as_codmetban // c?digo de m?todo a banco 
        //                 as_desope // descripci?n de operaci?n
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. Jennifer Rivero
        // Fecha Creaci?n: 28/01/2009                                
        // Modificado Por:                     Fecha ?ltima Modificaci?n : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;        
        $ls_nombrearchivo=$as_ruta;
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false; 
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {    
                $ls_cedper=str_pad(trim($rs_data->fields["cedper"]),9,"0",0);                
                $ldec_neto=number_format(abs($rs_data->fields["monto"]),2,".","");
                $ldec_neto=($ldec_neto*100);  
                $ldec_neto=number_format($ldec_neto,0,"","");
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,18);
                $ls_relleno1=str_pad("0",3,"0",0);
                $ls_concepto=trim($rs_data->fields["codconc"]);
                switch ($ls_concepto)
                {
                    case "0000020007":
                        $ls_nomconc="APU6";
                    break;
                    
                    case "0000020014":
                        $ls_nomconc="ACU6";
                    break;
                    
                    case "0000020003":
                        $ls_nomconc="AHV6";
                    break;
                    
                    case "0000020005":
                        $ls_nomconc="AHV6";
                    break;
                    
                    case "0000020008":
                        $ls_nomconc="PES6";
                    break;

                    case "0000020034":
                        $ls_nomconc="AHV6";
                    break;

                    case "0000020024":
                        $ls_nomconc="AHV6";
                    break;

                    case "0000020025":
                        $ls_nomconc="AHV6";
                    break;

                    case "0000020026":
                        $ls_nomconc="AHV6";
                    break;
                }
                
                $ls_relleno2=str_pad("0",10,"0",0);
                $ls_relleno3=str_pad("0",72,"0",0);
                $ls_cadena="2911".$ls_cedper.$ls_relleno1.$ls_nomconc.$ls_relleno2.$ldec_neto.$ls_relleno3."\r\n";
                
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }    
                $rs_data->MoveNext();    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiÃ³n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiÃ³n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_fonz03
    //--------------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
      function uf_metodo_fonz03_1($as_ruta,$rs_data) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_fonz03_militar
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco 
        //                 ad_fecproc // fecha de procesamiento
        //                 as_codcueban // c?digo de la cuenta bancaria a debitar 
        //                 as_codmetban // c?digo de m?todo a banco 
        //                 as_desope // descripci?n de operaci?n
        //      Description: genera el archivo txt a disco para  el banco Fondo Comun para pago de nomina
        //       Creado Por: Ing. Jennifer Rivero
        // Fecha Creaci?n: 28/01/2009                                
        // Modificado Por:                     Fecha ?ltima Modificaci?n : 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;        
        $ls_nombrearchivo=$as_ruta;
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false; 
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {    
                $ls_cedper=str_pad(trim($rs_data->fields["cedper"]),9,"0",0);                
                $ldec_neto=number_format(abs($rs_data->fields["monto"]),2,".","");
                $ldec_neto=($ldec_neto*100);  
                $ldec_neto=number_format($ldec_neto,0,"","");
                $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,18);
                $ls_relleno1=str_pad("0",3,"0",0);
                $ls_concepto=trim($rs_data->fields["codconc"]);
                switch ($ls_concepto)
                {
                    case "0000020007":
                        $ls_nomconc="APU6";
                    break;
                    
                    case "0000020014":
                        $ls_nomconc="ACU6";
                    break;
                    
                    case "0000020003":
                        $ls_nomconc="AHV7";
                    break;
                    
                    case "0000020005":
                        $ls_nomconc="AHV6";
                    break;
                    
                    case "0000020008":
                        $ls_nomconc="PES6";
                    break;

                    case "0000020034":
                        $ls_nomconc="AHV6";
                    break;

                    case "0000020024":
                        $ls_nomconc="AHV6";
                    break;

                    case "0000020025":
                        $ls_nomconc="AHV6";
                    break;

                    case "0000020026":
                        $ls_nomconc="AHV6";
                    break;
                }
                
                $ls_relleno2=str_pad("0",10,"0",0);
                $ls_relleno3=str_pad("0",72,"0",0);
                $ls_cadena="2911".$ls_cedper.$ls_relleno1."AHV7".$ls_relleno2.$ldec_neto.$ls_relleno3."\r\n";
                
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }    
                $rs_data->MoveNext();    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiÃ³n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiÃ³n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_fonz03
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_tarjeta_prepagada($as_ruta,$rs_data,$ls_fecproc) 
    {
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //    Function: uf_metodo_tarjeta_prepagada
        //    Access: public
        //    Arguments: $rs_data // arreglo (datastore) datos banco
        //           $as_ruta    ruta del documento
        //    Description: genera el archivo txt a disco para  la EmisiÃ³n de Tarjetas Prepagadas del Banco de Venezuela
        //    Creado Por: Ramon Tineo y Yolenis Gamez
        //    Fecha CreaciÃ³n: 02/06/2010
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta.'EMISION_IPSFA'.$ls_fecproc.'.txt';
        $li_count=$rs_data->RecordCount();
        if(($li_count>0)&&($lb_valido))
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false; 
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
        //encabezado
            $ls_encabezado='01G0200036923'.str_pad($li_count, 15, "0", STR_PAD_LEFT).'IPSFA                                   '.str_pad($li_count, 7, "0", STR_PAD_LEFT).'                                                                                                         '."\r\n";
            if (@fwrite($ls_creararchivo,$ls_encabezado)===false)//Escritura
            {
                $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                $lb_valido = false;
            }
            
        //Detalle
            while((!$rs_data->EOF)&&($lb_valido))
            {

                $ls_tipreg='02';
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$rs_data->fields["cedper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_cadena2 = explode (" ", $ls_apeper);
                $ls_apeper1 =str_pad($ls_cadena2[0], 10);
                $ls_reservado1='          ';
                $ls_apeper2 = str_pad(substr($ls_cadena2[1],0, 1),1);
                $ls_reservado2='                   ';
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_cadena3 = explode (" ", $ls_nomper);
                $ls_nomper1 = str_pad($ls_cadena3[0], 10);
                $ls_reservado3='                              ';
                $ls_ciudad=str_pad($rs_data->fields["ciudad"],20);
                $ls_estado=str_pad($rs_data->fields["estado"],25);
                $ls_codofic='0102';
                $ls_sexper=str_pad($rs_data->fields["sexper"],1);
                $ls_codarea='0000000';
                $ls_telefono='00000000';
                $ls_destelf='          ';
                $ls_fecnacper=str_pad($rs_data->fields["fecnacper"],8);
                $ls_libre='    ';
                $ls_cadena=$ls_tipreg.$ls_nacper.$ls_cedper.$ls_apeper1.$ls_reservado1.$ls_apeper2.$ls_reservado2.$ls_nomper1.$ls_reservado3.$ls_ciudad.$ls_estado.$ls_codofic.$ls_sexper.$ls_codarea.$ls_telefono.$ls_destelf.$ls_fecnacper.$ls_libre."\r\n";
                

                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido = false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido = false;
                }    
                $rs_data->MoveNext();    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiÃ³n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiÃ³n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;        
    }// end function uf_metodo_tarjeta_prepagada
    //-----------------------------------------------------------------------------------------------------------------------------------
    
    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_patria($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcuenta)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_patria
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 23/03/2021                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/patria.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
        //Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
            //Registro Cabecera (Debito)
            $li_filads=$li_count;
            $li_filads=$this->io_funciones->uf_cerosizquierda($li_filads,7);
            $ldec_totdep= number_format(abs($adec_montot),2,'.','');
            $ldec_totdep= str_replace('.','',$ldec_totdep);
            $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,15);
            $li_dia=substr($ad_fecproc,0,2);
            $li_mes=substr($ad_fecproc,3,2);
            $li_ano=substr($ad_fecproc,6,4);
            $ls_fecha=$li_ano.$li_mes.$li_dia;
            $ls_tipiden=substr($_SESSION["la_empresa"]["rifemp"],0,1);
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("-","",$_SESSION["la_empresa"]["rifemp"]));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("J","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("j","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("G","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("g","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("V","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("v","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("E","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("e","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("P","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("p","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_cerosizquierda($ls_rifemp,9);
            $ls_cadena="ONTNOM".$ls_tipiden.$ls_rifemp.$li_filads.$ldec_totdep."VES".$ls_fecha."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($rs_data->fields["cedper"],0,8),8);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);//Modificado por Carlos Zambrano
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 11, 0, 0);
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_nomapeper=substr($ls_nomper." ".$ls_apeper,0,40);
                $ls_nomapeper=str_pad($ls_nomapeper,40," ",STR_PAD_RIGHT); // rrellena espacio a la izquierda 
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ls_monto.$ls_nomapeper."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_patria  
    //-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------/*
    function uf_metodo_banco_generico($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_generico
        //           Access: public 
        //        Arguments: 
        //      Description: genera el archivo txt a disco para  el Banco Generico para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 13/07/2021                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $ls_nombrearchivo=$as_ruta."/generico.txt";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido=false;
				}
				else
				{
					$ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
            //Registro Cabecera 
            $li_filads=$li_count;
            $li_filads=$this->io_funciones->uf_cerosizquierda($li_filads,7);
			
            $ldec_totdep= number_format(abs($adec_montot),2,'.','');
            $ldec_totdep= str_replace('.','',$ldec_totdep);
            $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,15);
			
            $li_dia=substr($ad_fecproc,0,2);
            $li_mes=substr($ad_fecproc,3,2);
            $li_ano=substr($ad_fecproc,6,4);
            $ls_fecha=$li_ano.$li_mes.$li_dia;
            $ls_tipiden=substr($_SESSION["la_empresa"]["rifemp"],0,1);
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("-","",$_SESSION["la_empresa"]["rifemp"]));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("J","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("j","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("G","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("g","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("V","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("v","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("E","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("e","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("P","",$ls_rifemp));
            $ls_rifemp=$this->io_funciones->uf_trim(str_replace("p","",$ls_rifemp));
            $ls_rifemp=substr($ls_rifemp,0,9);
            $ls_rifemp=$this->io_funciones->uf_cerosizquierda($ls_rifemp,9);
			
            $ls_cadena="ONTNOM".$ls_tipiden.$ls_rifemp.$li_filads.$ldec_totdep."VES".$ls_fecha."\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($rs_data->fields["cedper"],0,8),8);
                $ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);//Modificado por Carlos Zambrano
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', '', $ls_monto);
                $ls_monto= str_pad($ls_monto, 11, 0, 0);
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_nomapeper=$ls_nomper." ".$ls_apeper;
				$ls_nomapeper=str_replace("Ñ","N",$ls_nomapeper);
				$ls_nomapeper=str_replace("ñ","n",$ls_nomapeper);
				$ls_nomapeper=str_replace("á","a",$ls_nomapeper);
				$ls_nomapeper=str_replace("é","e",$ls_nomapeper);
				$ls_nomapeper=str_replace("í","i",$ls_nomapeper);
				$ls_nomapeper=str_replace("ó","o",$ls_nomapeper);
				$ls_nomapeper=str_replace("ú","u",$ls_nomapeper);
				$ls_nomapeper=str_replace("Á","A",$ls_nomapeper);
				$ls_nomapeper=str_replace("É","E",$ls_nomapeper);
				$ls_nomapeper=str_replace("Í","I",$ls_nomapeper);
				$ls_nomapeper=str_replace("Ó","O",$ls_nomapeper);
				$ls_nomapeper=str_replace("Ú","U",$ls_nomapeper);
				$ls_nomapeper=substr($ls_nomapeper,0,40);
                $ls_nomapeper=str_pad($ls_nomapeper,40," ",STR_PAD_RIGHT); // rrellena espacio a la izquierda 
				
				
                $ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ls_monto.$ls_nomapeper."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function metodo_banco_generico  
    //-----------------------------------------------------------------------------------------------------------------------------------/*


    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_bancamiga($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcuenta)
    {  
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_bancamiga
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                   ad_fecproc // Fecha de procesamiento
        //                   as_codcuenta // cï¿½digo de cuenta
        //                   adec_montot // total a depositar
        //      Description: genera el archivo txt a disco para  el Banco Mercantil para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 23/03/2021                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 09/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $lb_valido=true;
        $li_dia=substr($ad_fecproc,0,2);
        $li_mes=substr($ad_fecproc,3,2);
        $li_ano=substr($ad_fecproc,6,4);
        $ls_fecha="-".$li_dia."-".$li_mes."-".$li_ano;
        $ls_nombrearchivo=$as_ruta."/Nomina".$ls_fecha.".csv";
        $li_count=$rs_data->RecordCount();
        if($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                {
                    $lb_valido=false;
                }
                else
                {
                    $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                }
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
            }
            $ls_cadena="Tipo Documento;Nro Documento;Cuenta;Nombre Beneficiario;Monto\r\n";
            if ($ls_creararchivo)
            {
                if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                {
                    $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
            }
            else
            {
                $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                $lb_valido=false;
            }
            //Registro tipo E
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_nacper=$rs_data->fields["nacper"];
                $ls_cedper=trim($rs_data->fields["cedper"]);
                $ls_codcueban=trim($rs_data->fields["codcueban"]);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ls_monto= number_format(abs($ldec_neto), 2, '.', '');
                $ls_monto= str_replace('.', ',', $ls_monto);
                $ls_nomper=$rs_data->fields["nomper"];
                $ls_apeper=$rs_data->fields["apeper"];
                $ls_nomapeper=$ls_nomper." ".$ls_apeper;
                $ls_cadena=$ls_nacper.";".$ls_cedper.";".$ls_codcueban.";".$ls_nomapeper.";".$ls_monto."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                    $lb_valido=false;
                }    
                $rs_data->MoveNext();                    
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_bancamiga  


    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_venezuela_old($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_venezuela
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. 
        // Fecha Creacion: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ultima Modificacion : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_total_deposito=0;
        $li_total_personal=0;
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/venezuela.txt";
        $li_count=$rs_data->RecordCount();
        if ($li_count>0)
        {
            //Chequea si existe el archivo.
            if (file_exists("$ls_nombrearchivo"))
            {
                $ls_creararchivo=fopen("$ls_nombrearchivo","a+"); // abrimos el archivo que ya existe
                $lb_adicionado=true;
            }
            else
            {
                $ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
                $lb_adicionado=false;
            }
            //Registro de Cabecera
            $ldec_totdep=$adec_montot;
            $ldec_totdep=round($ldec_totdep,2);  //redondea a 2 decimales
            if ($lb_adicionado)
            {
                $ls_cad_previa=fgets($ls_creararchivo,90);
                $ldec_monpre=substr($ls_cad_previa,71,13)/100;
                $ldec_monacu=$ldec_monpre+$ldec_totdep;
                $li_total_deposito=$li_total_deposito+$ldec_monacu;
            }
            else
            {
                //Registro Cabecera (Dï¿½bito)
                $ls_nombre=$this->io_funciones->uf_rellenar_der(substr($this->ls_nomemp,0,40)," ",40);
                $ls_codcueban=$as_codcuenta;
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $li_dia=substr($ad_fecproc,0,2);
                $li_mes=substr($ad_fecproc,3,2);
                $li_ano=substr($ad_fecproc,8,2);
                $li_total_deposito=$li_total_deposito+$ldec_totdep;
                $ldec_totdep=number_format($ldec_totdep*100,0,"","");
                $ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,13);
                $ls_cadena="H".$ls_nombre.$ls_codcueban."01".$li_dia."/".$li_mes."/".$li_ano.$ldec_totdep."03291"." "."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
            }
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
                $ls_codcueban=$rs_data->fields["codcueban"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto,2,".","");
                $li_total_personal=$li_total_personal+$ldec_neto;
                $ldec_neto=number_format($ldec_neto*100,0,"","");
                $ldec_neto=str_pad($ldec_neto,11,"0",0);
                $ls_nomper=trim($rs_data->fields["nomper"]);
                $ls_apeper=trim($rs_data->fields["apeper"]);
                $ls_personal=str_pad($this->io_sno->utf8_to_latin9(substr($ls_apeper.", ".$ls_nomper,0,40)),40," ");
                $ls_codtipcueban="";
                $ls_tipcuebanper = $this->io_funciones->uf_trim($rs_data->fields["tipcuebanper"]); 
                if ($ls_tipcuebanper == "C")// cuenta corriente
                {
                    $ls_tipcuebanper = "0";
                    $ls_codtipcueban = "0770";
                }
                if ($ls_tipcuebanper == "A") // cuenta de ahorro
                {
                    $ls_tipcuebanper = "1";
                    $ls_codtipcueban = "1770";
                }
                if ($ls_tipcuebanper == "L") // fondo de activos lï¿½quidos
                {
                    $ls_tipcuebanper = "2";
                    $ls_codtipcueban = "1770";
                }
                $ls_cadena=$ls_tipcuebanper.$ls_codcueban.$ldec_neto.$ls_codtipcueban.$ls_personal.$ls_cedper."003291  "."\r\n";
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }
                $rs_data->MoveNext();            
            }
            //*-Si estoy acumulando reemplazo la cabecera con la informacion acumulada
            if (($lb_valido)&&($lb_adicionado))
            {
                $ls_reemplazar=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,13)."03291"." "."\r\n";
                $ls_cadena=substr_replace($ls_cad_previa,$ls_reemplazar,71);
                $new_archivo=file("$ls_nombrearchivo"); //creamos el array con las lineas del archivo
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                if (file_exists("$ls_nombrearchivo"))
                {
                    if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
                    {
                        $lb_valido=false;
                    }
                    else
                    {
                        $ls_creararchivo=@fopen("$ls_nombrearchivo","a+");
                    }
                }
                if ($ls_creararchivo)
                {
                    if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                    {
                        $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }
                }
                else
                {
                    $this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
                    $lb_valido=false;
                }        
                $li_numlin=count((Array)$new_archivo); //contamos los elementos del array, es decir el total de lineas
                $li_total_personal=0;
                for($li_i=1;(($li_i<$li_numlin)&&($lb_valido));$li_i++)
                {
                    $ls_cadena=$new_archivo[$li_i];
                    $li_monto=substr($ls_cadena,21,11)/100;
                    $li_total_personal=$li_total_personal+$li_monto;
                    if ($ls_creararchivo)
                    {
                        if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
                        {
                            $this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
                            $lb_valido=false;
                        }
                    }
                    else
                    {
                        $this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
                        $lb_valido=false;
                    }        
                }
                if ($lb_valido)
                {
                    $this->io_mensajes->message("Listado adicionado  Monto Acumulado: ".round($ldec_monacu));
                }
                unset($new_archivo);
            }
            if($lb_valido)
            {
                $li_total_personal=number_format($li_total_personal*100,0,"","");
                $li_total_deposito=number_format($li_total_deposito*100,0,"","");
                if(strval($li_total_personal)!=strval($li_total_deposito))
                {
                    $this->io_mensajes->message("El Monto de la Cabecera Difiere de la suma del Personal Total Personal = ".$li_total_personal." Total Cabecera = ".$li_total_deposito);
                    $lb_valido=@unlink("$ls_nombrearchivo");
                    $lb_valido=false;
                }
            }
            if ($lb_valido)
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
            }
            else
            {
                @fclose($ls_creararchivo); //cerramos la conexiï¿½n y liberamos la memoria
                $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique.");
            }    
        }
        else
        {
            $this->io_mensajes->message("No hay datos que generar.");
            $lb_valido=false;
        }
        return $lb_valido;
    }// end function uf_metodo_banco_venezuela
    //-----------------------------------------------------------------------------------------------------------------------------------
    
}
?> 