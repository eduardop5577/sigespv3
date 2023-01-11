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

class sigesp_sno_c_metodo_banco_1
{
	var $io_mensajes;
	var $io_metbanco;
	var $io_sno;
	var $ls_codemp;
	var $ls_nomemp;
	var $ls_codnom;
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	public function __construct()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: sigesp_sno_c_metodo_banco_1
		//		   Access: public 
		//	  Description: Constructor de la Clase
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		$io_include=new sigesp_include();
		$io_conexion=$io_include->uf_conectar();
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		$this->io_mensajes=new class_mensajes();		
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
		$this->io_funciones=new class_funciones();
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		$this->io_sql=new class_sql($io_conexion);	
		require_once("sigesp_sno.php");
		$this->io_sno=new sigesp_sno();
		require_once("sigesp_snorh_c_metodobanco.php");
		$this->io_metbanco=new sigesp_snorh_c_metodobanco();
   		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		$this->ls_nomemp=$_SESSION["la_empresa"]["nombre"];
		$this->ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
		$this->ls_siglas=$_SESSION["la_empresa"]["titulo"];
		
	}// end function sigesp_sno_c_metodo_banco_1
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_bod($as_ruta,$ac_nroperi,$ad_fdesde,$ad_fhasta,$as_numref,$ad_fecproc,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_bod
		//		   Access: public 
		//	    Arguments: ac_nroperi  // codigo del periodo
		//                 ad_fdesde   // fecha desde
		//                 ad_fhasta   // fecha hasta
		//                 aa_ds_banco // arreglo (datastore) datos banco      
		//	  Description: genera el archivo txt a disco para  el banco BOD para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$ldec_MontoPrevio=0;
		$ldec_MontoAcumulado=0;
		$li_NroDebitosPrev=0;
		$li_NroCreditosPrev=0;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$ls_desperi="";
		$li_count=$rs_data->RecordCount();
		$ls_tipo_nom=$this->ls_codnom=$_SESSION["la_nomina"]["tipnom"];
		$ls_quinc=substr($ad_fhasta,8,2);
		if ($ls_quinc==15)
		{
			$ls_tipquinc="1";
		}
		else
		{
			$ls_tipquinc="2";
		}
		$ls_mes=substr($ad_fecproc,3,2);
		$ls_ano=substr($ad_fecproc,6,4);
		if ($li_count > 0)
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			while((!$rs_data->EOF)&&($lb_valido))
			{	//Numero de cuenta del empleado
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,-10),12);
				//Monto total a cancelar 
				$ldec_neto = $rs_data->fields["monnetres"]; //debo verificar si en el ds ya viene modificado la coma decimal
				$ldec_neto = ($ldec_neto*100);  
				$ldec_neto = $this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				//cedula del empleado
				//$ls_cedemp = $this->io_funciones->uf_rellenar_izq($rs_data->fields["cedper"]," ",15);
				$ls_cedemp = $this->io_funciones->uf_rellenar_der($rs_data->fields["cedper"]," ",15);
				$ls_cadena = $ls_cedemp.$ls_codcueban.$ldec_neto.$as_numref."N-".$ls_tipquinc."-".$ls_mes."-".$ls_ano."-".$ls_tipo_nom."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
				$li_NroCreditosPrev = ($li_NroCreditosPrev + 1);
                               $rs_data->MoveNext();
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_bod
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_bod_internet($as_ruta,$ac_nroperi,$ad_fdesde,$ad_fhasta,$as_numref,$ad_fecproc,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_bod
		//		   Access: public 
		//	    Arguments: ac_nroperi  // codigo del periodo
		//                 ad_fdesde   // fecha desde
		//                 ad_fhasta   // fecha hasta
		//                 aa_ds_banco // arreglo (datastore) datos banco      
		//	  Description: genera el archivo txt a disco para  el banco BOD para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido= true;
		$ldec_MontoPrevio=0;
		$ldec_MontoAcumulado=0;
		$li_NroDebitosPrev=0;
		$li_NroCreditosPrev=0;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$ls_desperi="";
		$li_count=$rs_data->RecordCount();
		$ls_tipo_nom=$this->ls_codnom=$_SESSION["la_nomina"]["tipnom"];
		$ls_quinc=substr($ad_fhasta,8,2);
		if ($ls_quinc==15)
		{
			$ls_tipquinc="1";
		}
		else
		{
			$ls_tipquinc="2";
		}
		$ls_mes=substr($ad_fecproc,3,2);
		$ls_ano=substr($ad_fecproc,6,4);
		if ($li_count > 0)
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			while((!$rs_data->EOF)&&($lb_valido))
			{	//Numero de cuenta del empleado
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,12),12);
				//Monto total a cancelar 
				$ldec_neto = $rs_data->fields["monnetres"]; //debo verificar si en el ds ya viene modificado la coma decimal
				$ldec_neto = ($ldec_neto*100);  
				$ldec_neto = $this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				//cedula del empleado
				$ls_cedemp = $this->io_funciones->uf_rellenar_der($rs_data->fields["cedper"]," ",15);
				$ls_cadena = $ls_cedemp.$ls_codcueban.$ldec_neto.$as_numref."\r\n";
				if ($ls_creararchivo)  //Chequea que el archivo este abierto				
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				else
				{
					$this->io_mensajes->message("Error al abrir el archivo ".$ls_nombrearchivo);
					$lb_valido = false;
				}
				$li_NroCreditosPrev = ($li_NroCreditosPrev + 1);
                               $rs_data->MoveNext();
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_bod
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_metodo_banco_banesco_paymul($as_ruta,$rs_data,$ad_fecproc,$adec_montot,$as_codcueban,$as_ref)
	{  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banesco_paymul
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//                 adec_montot // Monto total
		//                 as_codcueban // c�digo de la cuenta bancaria a debitar 
		//	  Description: genera el archivo txt a disco para  el banco Banesco Paymul para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_max=2500;
		$li_numarc=1;
		$li_personal=0;
		$ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
		$ad_fecproc=str_replace("-","",$ad_fecproc);				
		$li_count=$rs_data->RecordCount();                
		if($li_count>0)
		{		
			//Registro de credito
			$li_nrocreditos=0;
			$li_numrecibo=0;
                        $ls_cadena = "";
                        $li_total_personal=0;
		        while((!$rs_data->EOF)&&($lb_valido))
			{
				$li_numrecibo=$li_numrecibo+1;
				$ls_codcueban=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_rellenar_der(substr(trim($ls_codcueban),0,30)," ",30);
				$li_numrecibo=$this->io_funciones->uf_cerosizquierda($li_numrecibo,8);
				$li_numrecibo=$this->io_funciones->uf_rellenar_der($li_numrecibo," ", 30);
				$ldec_neto=$rs_data->fields["monnetres"];  //debo verificar si en el ds ya viene modificado la coma decimal
				$ldec_neto=number_format($ldec_neto,2,".","");
                                $li_total_personal=$li_total_personal+$ldec_neto;
				$ldec_neto=number_format($ldec_neto*100,0,"","");
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				$ls_nacper=$rs_data->fields["nacper"];   //Nacionalidad
				$ls_cedper=$rs_data->fields["cedper"];   //cedula del personal
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_nacper.$ls_cedper," ",17);
				$ls_apeper=$rs_data->fields["apeper"];
				$ls_nomper=$rs_data->fields["nomper"];
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper." ".$ls_nomper, " ", 70);
				$ls_const=$this->uf_nombre_empresa('BANESCO_PAYMUL');
				$ls_const=$this->io_funciones->uf_rellenar_der($ls_const, " ", 11);
				$ls_space= $this->io_funciones->uf_rellenar_der(""," ",3); // (3)
				$ls_spacedir=$this->io_funciones->uf_rellenar_der(""," ",70);  //direccion (70)
				$ls_spacetel=$this->io_funciones->uf_rellenar_der(""," ",25);              //telefono (25)
				$ls_spacecicon=$this->io_funciones->uf_rellenar_der(""," ",17);                    //C.I. persona contacto  (17)
				$ls_spacenomcon=$this->io_funciones->uf_rellenar_der(""," ",35);  //Nombre persona contacto (35)
				$ls_spaceficha=$this->io_funciones->uf_rellenar_der(""," ",30);       //Ficha del personal (30)
				$ls_spaceubic=$this->io_funciones->uf_rellenar_der(""," ",21);                //Ubicacion Geografica (21)
				$li_nrocreditos=$li_nrocreditos + 1;
				$ls_cadena .="03".$li_numrecibo.$ldec_neto."VES".$ls_codcueban.$ls_const.$ls_space.$ls_cedper.$ls_personal.
					     $ls_spacedir.$ls_spacetel.$ls_spacecicon.$ls_spacenomcon." ".$ls_spaceficha."  ".$ls_spaceubic."42 "."\r\n";
                                $rs_data->MoveNext();
                                $li_personal++;
                                if (($li_personal==$li_max)|| ($rs_data->EOF))
                                {
                                    $ls_nombrearchivo=$as_ruta."/b_paymul".$li_numarc.".txt";
                                    $ls_numope=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANESCO_PAYMUL_OP","1","I");
                                    $ls_numope=intval($this->io_funciones->uf_trim($ls_numope), 10);
                                    $lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANESCO_PAYMUL_OP",$ls_numope+1,"I");
                                    if($lb_valido)
                                    {		
                                            $ls_numref=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANESCO_PAYMUL_REF","1","I");
                                            $ls_numref=intval($this->io_funciones->uf_trim($ls_numref),10);
                                            if ($as_ref==1)
                                            {
                                                    $lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANESCO_PAYMUL_REF",$ls_numref+1,"I");
                                            }
                                    }
                                    if($lb_valido)
                                    {		
                                            $ls_numref=$this->io_funciones->uf_cerosizquierda($ls_numref,8);					 
                                            $ls_numope=substr($ls_numope,0,9); 
                                            $ls_numope=str_pad($ls_numope,11,"0",1);
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
                                    }
                                    if($lb_valido)
                                    {		
                                            // Registro de control (Datos Fijos)
                                            $ls_encabezado="HDR"."BANESCO        "."E"."D  95B"."PAYMUL"."P"."\r\n";
                                            if ($ls_creararchivo)
                                            {
                                                    if (@fwrite($ls_creararchivo,$ls_encabezado)===false)//Escritura
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
                                            // Registro de encabezado
                                            $ls_encabezado = "01".$this->io_funciones->uf_rellenar_der("SAL"," ",35).$this->io_funciones->uf_rellenar_der("9"," ",3).$this->io_funciones->uf_rellenar_der($ls_numope," ",35).$this->io_funciones->uf_cerosderecha($ad_fecproc,14)."\r\n";
                                            if ($ls_creararchivo)
                                            {
                                                    if (@fwrite($ls_creararchivo,$ls_encabezado)===false)//Escritura
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
                                            // Registro de debito
                                            $ls_rifempresa=str_replace("-","",$_SESSION["la_empresa"]["rifemp"]);
                                            $ls_rif=$this->io_funciones->uf_rellenar_der($ls_rifempresa," ",17);
                                            $ldec_montot=number_format($li_total_personal,2,'.','');           
                                            $ldec_montot=($ldec_montot*100);  
                                            $ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,15);
                                            $as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
                                            $as_codcueban=$this->io_funciones->uf_rellenar_der(substr(trim($as_codcueban),0,34), " ", 34);
                                            $ls_nomemp=$_SESSION["la_empresa"]["nombre"];
                                            $ls_nomemp=$this->io_funciones->uf_rellenar_der(substr(rtrim($ls_nomemp),0,35)," ",35);
                                            $ls_banesco=$this->io_funciones->uf_rellenar_der("BANESCO"," ",11);
                                            $li_nrodebitos=1;
                                            $ls_numref=$this->io_funciones->uf_rellenar_der($ls_numref," ",30);
                                            $ls_encabezado="02".$ls_numref.$ls_rif.$ls_nomemp.$ldec_montot."VES"." ".$as_codcueban.$ls_banesco.$ad_fecproc."\r\n";
                                            if ($ls_creararchivo)
                                            {
                                                    if (@fwrite($ls_creararchivo, $ls_encabezado)===FALSE)//Escritura
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
                                    
                                    if($lb_valido)
                                    {		
                                            //Registro de totales
                                            $li_nrodebitos=$this->io_funciones->uf_cerosizquierda($li_nrodebitos,15);
                                            $li_nrocreditos=$this->io_funciones->uf_cerosizquierda($li_nrocreditos,15);
                                            $ls_cadena="06".$li_nrodebitos.$li_nrocreditos.$ldec_montot."\r\n";
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
                                            @fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
                                            $this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
                                    }
                                    else
                                    {
                                            @fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
                                            $this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
                                    }	

                                    $li_total_personal= 0;
                                    $li_personal=0;
                                    $ls_cadena="";
                                    $li_numarc++;
                                    $li_nrocreditos=0;
                                    $li_numrecibo=0;
                                    
                                }
			} 
		 
		}
		return $lb_valido;		
    }// end function uf_metodo_banco_banesco_paymul
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_metodo_banco_banesco_paymul_terceros($as_ruta,$rs_data,$ad_fecproc,$adec_montot,$as_codcueban,$as_ref)
	{  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banesco_paymul
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//                 adec_montot // Monto total
		//                 as_codcueban // c�digo de la cuenta bancaria a debitar 
		//	  Description: genera el archivo txt a disco para  el banco Banesco Paymul para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$as_codcueban='XXXXXXXXXXCUENTABANCOXXXXXXXXXXXXX';
		$ls_nombrearchivo=$as_ruta."/b_paymul.txt";
		$ls_numope=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANESCO_PAYMUL_OP","1","I");
		$ls_numope=intval($this->io_funciones->uf_trim($ls_numope), 10);
		$lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANESCO_PAYMUL_OP",$ls_numope+1,"I");
		if($lb_valido)
		{		
			$ls_numref=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANESCO_PAYMUL_REF","1","I");
			$ls_numref=intval($this->io_funciones->uf_trim($ls_numref),10);
			if ($as_ref==1)
			{
				$lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANESCO_PAYMUL_REF",$ls_numref+1,"I");
			}
		}
		if($lb_valido)
		{		
			$ls_numref=$this->io_funciones->uf_cerosizquierda($ls_numref,8);					 
			$ls_numope=substr($ls_numope,0,9); 
			$ls_numope=str_pad($ls_numope,11,"0",1);
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
		}
		if($lb_valido)
		{		
			// Registro de control (Datos Fijos)
			$ls_cadena="HDR"."BANESCO        "."E"."D  95B"."PAYMUL"."P"."\r\n";
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
			// Registro de encabezado
			$ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
			$ad_fecproc=str_replace("-","",$ad_fecproc);
			$ls_cadena = "01".$this->io_funciones->uf_rellenar_der("SCV"," ",35).$this->io_funciones->uf_rellenar_der("9"," ",3).$this->io_funciones->uf_rellenar_der($ls_numope," ",35).$this->io_funciones->uf_cerosderecha($ad_fecproc,14)."\r\n";
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
			// Registro de debito
			$ls_rifempresa=str_replace("-","",$_SESSION["la_empresa"]["rifemp"]);
			$ls_rif=$this->io_funciones->uf_rellenar_der($ls_rifempresa," ",17);
			$ldec_montot=$adec_montot;           
			$ldec_montot=($ldec_montot*100);  
			$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,15);
			$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			$as_codcueban=$this->io_funciones->uf_rellenar_der(substr(trim($as_codcueban),0,34), " ", 34);
			$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
			$ls_nomemp=$this->io_funciones->uf_rellenar_der(substr(rtrim($ls_nomemp),0,35)," ",35);
			$ls_banesco=$this->io_funciones->uf_rellenar_der("BANESCO"," ",11);
			$li_nrodebitos=1;
			$ls_numref=$this->io_funciones->uf_rellenar_der($ls_numref," ",30);
			$ls_cadena="02".$ls_numref.$ls_rif.$ls_nomemp.$ldec_montot."VES"." ".$as_codcueban.$ls_banesco.$ad_fecproc."\r\n";
			if ($ls_creararchivo)
			{
				if (@fwrite($ls_creararchivo, $ls_cadena)===FALSE)//Escritura
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
			//Registro de credito
			$li_nrocreditos=0;
			$li_numrecibo=0;
			$li_count=$rs_data->RecordCount();//$aa_ds_banco->getRowCount("codcueban");
			//for($li_i=1;(($li_i<=$li_count)&&($lb_valido));$li_i++)
		        while((!$rs_data->EOF)&&($lb_valido))
			{
				$li_numrecibo=$li_numrecibo+1;
				$ls_codcueban=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_rellenar_der(substr(trim($ls_codcueban),0,30)," ",30);
				$li_numrecibo=$this->io_funciones->uf_cerosizquierda($li_numrecibo,8);
				$li_numrecibo=$this->io_funciones->uf_rellenar_der($li_numrecibo," ", 30);
				$ldec_neto=$rs_data->fields["monnetres"];  //debo verificar si en el ds ya viene modificado la coma decimal
				$ldec_neto=number_format($ldec_neto,2,".","");
				$ldec_neto=number_format($ldec_neto*100,0,"","");
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				$ls_nacper=$rs_data->fields["nacper"];   //Nacionalidad
				$ls_cedper=$rs_data->fields["cedper"];   //cedula del personal
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_nacper.$ls_cedper," ",17);
				$ls_apeper=$rs_data->fields["apeper"];
				$ls_nomper=$rs_data->fields["nomper"];
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper." ".$ls_nomper, " ", 70);
				$ls_const=$this->io_funciones->uf_rellenar_der($rs_data->fields["codban"], " ", 11);
				$ls_space= $this->io_funciones->uf_rellenar_der(""," ",3); // (3)
				$ls_spacedir=$this->io_funciones->uf_rellenar_der(""," ",70);  //direccion (70)
				$ls_spacetel=$this->io_funciones->uf_rellenar_der(""," ",25);              //telefono (25)
				$ls_spacecicon=$this->io_funciones->uf_rellenar_der(""," ",17);                    //C.I. persona contacto  (17)
				$ls_spacenomcon=$this->io_funciones->uf_rellenar_der(""," ",35);  //Nombre persona contacto (35)
				$ls_spaceficha=$this->io_funciones->uf_rellenar_der(""," ",30);       //Ficha del personal (30)
				$ls_spaceubic=$this->io_funciones->uf_rellenar_der(""," ",21);                //Ubicacion Geografica (21)
				$li_nrocreditos=$li_nrocreditos + 1;
				$ls_cadena="03".$li_numrecibo.$ldec_neto."VES".$ls_codcueban.$ls_const.$ls_space.$ls_cedper.$ls_personal.
							$ls_spacedir.$ls_spacetel.$ls_spacecicon.$ls_spacenomcon." ".$ls_spaceficha."  ".$ls_spaceubic."425"."\r\n";
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
			//Registro de totales
			$li_nrodebitos=$this->io_funciones->uf_cerosizquierda($li_nrodebitos,15);
			$li_nrocreditos=$this->io_funciones->uf_cerosizquierda($li_nrocreditos,15);
			$ls_cadena="06".$li_nrodebitos.$li_nrocreditos.$ldec_montot."\r\n";
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
			@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
			$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
		}
		else
		{
			@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
			$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
		}	
		return $lb_valido;		
    }// end function uf_metodo_banco_banesco_paymul
	//-----------------------------------------------------------------------------------------------------------------------------------	

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_confederado($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_confederado
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Cofederado para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
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
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,10);
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=($ldec_neto*100);  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,13);
			    $ls_cadena=$ls_codcueban.substr($ldec_neto, 0, 10).".".substr($ldec_neto, 11)."+"."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_confederado
	//-----------------------------------------------------------------------------------------------------------------------------------

	function uf_metodo_banco_deltesoro_2012($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_confederado
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Cofederado para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
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
				$ls_cedper=trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=($ldec_neto*100);  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_confederado
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_corp_banca_nuevo($as_ruta,$rs_data,$as_numref)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_corp_banca_nuevo
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Cofederado para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/corp_banca.txt";
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
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				//$long=strlen($ls_cedper);
				//$long_ced=15-$long;
				$ls_cedper=str_pad($ls_cedper, 15);
				//$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",$long_ced);
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=substr($ls_codcueban,-10);
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=($ldec_neto*100);  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
			    $ls_cadena=$ls_cedper."00".$ls_codcueban.$ldec_neto.$as_numref."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_corp_banca_nuevo
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro_nomina($as_ruta,$rs_data,$as_numref)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro_nomina
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Cofederado para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Carlos Zambrano						Fecha ultima Modificacion : 08/05/2006
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
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				//$long=strlen($ls_cedper);
				//$long_ced=15-$long;
				//$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",$long_ced);
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=substr($ls_codcueban,-20);
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=$this->io_funciones->uf_trim(str_replace(".",",",$ldec_neto));
				//$ldec_neto=($ldec_neto*100);  
				//$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
			    $ls_cadena=$ls_codcueban.";".$ls_nacper.$ls_cedper.";".$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_corp_banca_nuevo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_casa_propia_2003($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_casa_propia_2003
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Casa Propia 2003 para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/transfer.txt";
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
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_nomper=trim($rs_data->fieldsa["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=$this->io_funciones->uf_rellenar_izq(($ls_nomper." ".$ls_apeper)," ",40);
				$ls_personal=trim($ls_personal);
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban,"0",10);
			    $ls_codcueban=substr($ls_codcueban,0,10);
				$ls_codcueban=$this->io_funciones->uf_rellenar_izq($ls_codcueban,"0",10);
				$ls_tipcuebanper=$rs_data->fields["tipcuebanper"];								
				$ldec_montot=number_format($rs_data->fields["monnetres"],2,".","");
			    $ls_cadena = $ls_cedper.",".$ls_personal.",".$ls_tipcuebanper.",".$ls_codcueban.",".$ldec_montot.","."0"."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_casa_propia_2003
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_bicentenario($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_bicentenario
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Casa Propia 2003 para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once ("../base/librerias/php/writeexcel/class.writeexcel_workbook.inc.php");
		require_once ("../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");	
		$lb_valido=true;
		$as_ruta1="txt/general";
		$ls_origen=$as_ruta1."/bicentenario.xls";
		$ls_destino=$as_ruta."/bicentenario_nomina.xls";
		copy($ls_origen,$ls_destino);
		chmod($ls_destino,0777);
		$fname = fopen ($ls_destino,"r+");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = &$workbook->addworksheet();
		$li_count=$rs_data->RecordCount();
		$lo_titulo= &$workbook->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_datacenter= &$workbook->addformat();
		$lo_datacenter->set_font("Verdana");
		$lo_datacenter->set_align('center');
		$lo_datacenter->set_size('9');
		$worksheet->set_column(0,0,20);
		$worksheet->set_column(1,1,20);
		$worksheet->set_column(2,2,20);
		$worksheet->set_column(3,3,20);
		$worksheet->set_column(4,4,20);
		$worksheet->set_column(5,5,20);
		if($li_count>0)
		{	
			$li_fila=1;
			$li_i=0;
			$li_total=0;
			$worksheet->write($li_i,0,"NACIONALIDAD",$lo_titulo);
			$worksheet->write($li_i,1,"C.I",$lo_titulo);
			$worksheet->write($li_i,2,"NOMBRE 1",$lo_titulo);
			$worksheet->write($li_i,3,"APELLIDO 1",$lo_titulo);
			$worksheet->write($li_i,4,"MONTO A PAGAR",$lo_titulo);
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ldec_montot=number_format($rs_data->fields["monnetres"],2,".","");
				//Diviendo nombres y Apellidos
				$pos1 =strpos(rtrim($ls_nomper)," ",0);
				$ls_nomper1=substr(rtrim($ls_nomper),0,$pos1);  //Nombre de Persona 1
				$ls_nomper2=substr(ltrim(rtrim($ls_nomper)),$pos1+1,60);  //Nombre de Persona 2
				if($pos1==0)
				{
					$ls_nomper1=$ls_nomper;
					$ls_nomper2="";
				}
				$pos2 =strpos(rtrim($ls_apeper),' ',0);
				$ls_apeper1=substr(rtrim($ls_apeper),0,$pos2);  //Apellido de Persona 1
				$ls_apeper2=substr(ltrim(rtrim($ls_apeper)),$pos2+1,60);  //Apellido de Persona 2
				if($pos2==0)
				{
					$ls_apeper1=$ls_apeper;
					$ls_apeper2="";
				}
				//Diviendo nombres y Apellidos
				
				$worksheet->write($li_fila,0," ".$ls_nacper,$lo_datacenter);
				$worksheet->write($li_fila,1," ".$ls_cedper,$lo_datacenter);
				$worksheet->write($li_fila,2," ".$ls_nomper1,$lo_datacenter);
				$worksheet->write($li_fila,3," ".$ls_apeper1,$lo_datacenter);
				$worksheet->write($li_fila,4," ".$ldec_montot,$lo_datacenter);
				
				$li_fila=$li_fila+1;
				$rs_data->MoveNext();

			}
			if ($lb_valido)
			{
				$this->io_mensajes->message("El archivo ".$ls_destino." fue creado.");
				$workbook->close();
			}
			else
			{
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
				unset($worksheet);
				unset($workbook);
				unset($fname);
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_bicentenario
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_caribe($as_ruta,$rs_data,$adec_montot,$ad_fecproc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_casa_propia_2003
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 adec_montot // monto toal a depositar
		//                 ad_fecproc // fecha de procesamiento
		//	  Description: genera el archivo txt a disco para  el banco Caribe para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/carga.txt";
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
			//Registro de Cabecera
			$ldec_montot=$adec_montot*100;
			$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,20);
			$li_dia=substr($ad_fecproc,0,2);
			$li_mes=substr($ad_fecproc,3,2);
			$li_ano=substr($ad_fecproc,6,4);
			$ld_fecproc=$li_dia.$li_mes.$li_ano;
			$ls_cadena=$ld_fecproc."/".intval($li_count,10)."/".$ldec_montot."\r\n";
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
			//Registro de Credito
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ldec_neto = $rs_data->fields["monnetres"]*100;
			    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,20);
				//$ls_cedper=$this->io_funciones->uf_trim($aa_ds_banco->data["cedper"][$li_i]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				//$ls_codcueban=$aa_ds_banco->data["codcueban"][$li_i];
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_cadena="C"."/".$ls_codcueban."/".$ls_cedper."/".$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_caribe
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_venezuela_viatico($as_ruta,$rs_data,$adec_montot,$ad_fecproc,$as_codcueban,$as_numdoc,$as_codban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_casa_propia_2003
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 adec_montot // monto toal a depositar
		//                 ad_fecproc // fecha de procesamiento
		//	  Description: genera el archivo txt a disco para  el banco Caribe para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/venezuela_viatico.txt";
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
			//Registro de Cabecera
			$li_dia=substr($ad_fecproc,0,2);
			$li_mes=substr($ad_fecproc,3,2);
			$li_ano=substr($ad_fecproc,6,4);
			$ld_fecproc=$li_dia."/".$li_mes."/".$li_ano;
			$ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
			$ls_cadena="HEADER "."12345678"."12345678".$ls_rifemp.$ld_fecproc.$ld_fecproc."\r\n";
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
			//Registro de Debito
			$ldec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,17);
			$ldec_montot=str_replace(".",",",$ldec_montot);
			$li_dia=substr($ad_fecproc,0,2);
			$li_mes=substr($ad_fecproc,3,2);
			$li_ano=substr($ad_fecproc,6,4);
			$ld_fecproc=$li_dia."/".$li_mes."/".$li_ano;
			$ls_rifemp=$_SESSION["la_empresa"]["rifemp"];
			$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
			$ls_tipcuebandeb=$this->uf_tipocuentabancaria($as_codban,$as_codcueban);
			$as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban,20);
			if ($ls_tipcuebandeb=='002')
			{
				$ls_tcuenta='01';
			}
			else
			{
				$ls_tcuenta='00';
			}
			$ls_cadena="DEBITO ".$as_numdoc.$ls_rifemp.$ls_nomemp.$ld_fecproc.$ls_tcuenta.$as_codcueban.$ldec_montot."VES40"."\r\n";
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
			//Registro de Credito
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ldec_neto = $rs_data->fields["monnetres"];
			    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,17);
				$ldec_neto=str_replace(".",",",$ldec_neto);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_codbanben=$rs_data->fields["codban"];
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nacionalidad=$rs_data->fields["nacben"];
				$ls_nomper=$rs_data->fields["nomper"];
				$ls_apeper=$rs_data->fields["apeper"];
				$ls_tipcuebancre=$rs_data->fields["tipcuebanper"];
				if ($ls_tipcuebancre=='002')
				{
					$ls_tcuentacre='01';
				}
				else
				{
					$ls_tcuentacre='00';
				}
				$ls_nomape=$ls_nomper." ".$ls_apeper;
				$ls_nombrebanco=$this->uf_nombrebanco_beneficiario($ls_codbanben);
				$ls_nombrebanco=substr($ls_nombrebanco,0,12);
				$ls_correo=$rs_data->fields["coreleper"];				
				$ls_cadena="CREDITO ".$as_numdoc.$ls_nacionalidad.$ls_cedper.$ls_nomape.$ls_tcuentacre.$ls_codcueban.$ldec_neto."10".$ls_nombrebanco."900501".$ls_correo."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_caribe
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_banfoandes($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banfoandes
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//	  Description: genera el archivo txt a disco para  el banco Banfoandes para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/banfoandes.txt";
		$li_dia=substr($ad_fecproc,0,2);
		$li_mes=substr($ad_fecproc,3,2);
		$li_ano=substr($ad_fecproc,6,4);
		$ld_fecproc=$li_dia.$li_mes.$li_ano;
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

				// Registro de la cabecera (Datos Fijos)
				$ldec_montot=$adec_montot;           
				$ldec_montot=($ldec_montot*100);  
				$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,17);
				$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			        $as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 20);
				$cant_registros=str_pad($li_count,4,0,"LEFT");
				if($lb_valido)
				{		
				    $ls_cadena=$as_codcueban.$li_ano.$li_mes.$li_dia.$ldec_montot.$cant_registros."\r\n";
							  
				}
				if ($ls_creararchivo)
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=str_replace("-","",$ls_codcueban);
			    $ls_codcueban=trim($ls_codcueban);
			    $ls_codcueban=substr($ls_codcueban,0,20);
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10); 
				$ldec_neto=$rs_data->fields["monnetres"]*100;
			    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto, 12);
				$ls_cadena="0651".$ldec_neto.$ls_codcueban.$ls_cedper."00000000"."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_banfoandes
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_banfoandes2($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot,$as_codempnom)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banfoandes2
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//	  Description: genera el archivo txt a disco para  el banco Banfoandes para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/banfoandes2.txt";
		$li_dia=substr($ad_fecproc,0,2);
		$li_mes=substr($ad_fecproc,3,2);
		$li_ano=substr($ad_fecproc,6,4);
		$ld_fecproc=$li_dia.$li_mes.$li_ano;
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

				// Registro de la cabecera (Datos Fijos)
				$ldec_montot=$adec_montot;           
				$ldec_montot=($ldec_montot*100);  
				$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,17);
				$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			    $as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 20);
				$cant_registros=$this->io_funciones->uf_cerosizquierda($li_count,4);
				if($lb_valido)
				{		
				    $ls_cadena=$as_codcueban.$li_ano.$li_mes.$li_dia.$ldec_montot.$cant_registros."\r\n";
							  
				}
				if ($ls_creararchivo)
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=str_replace("-","",$ls_codcueban);
			    $ls_codcueban=trim($ls_codcueban);
			    $ls_codcueban=substr($ls_codcueban,0,20);
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10); 
				$ldec_neto=$rs_data->fields["monnetres"]*100;
			    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto, 12);
				$ls_cadena=$as_codempnom.$ldec_neto.$ls_codcueban.$ls_cedper."00000000"."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_banfoandes2
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_banfoandes_ipsfa($as_ruta,$rs_data,$ad_fecproc,$as_codcueban,$adec_montot)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banfoandes_ipsfa
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//	  Description: genera el archivo txt a disco para  el banco Banfoandes para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/banfoandes.txt";
		$li_dia=substr($ad_fecproc,0,2);
		$li_mes=substr($ad_fecproc,3,2);
		$li_ano=substr($ad_fecproc,6,4);
		$ld_fecproc=$li_dia.$li_mes.$li_ano;
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

				// Registro de la cabecera (Datos Fijos)
				$ldec_montot=$adec_montot;           
				$ldec_montot=($ldec_montot*100);  
				$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,17);
				$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			        $as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 20);
				$cant_registros=str_pad($li_count,4,0,"LEFT");
				if($lb_valido)
				{		
				    $ls_cadena=$as_codcueban.$li_ano.$li_mes.$li_dia.$ldec_montot.$cant_registros."\r\n";
							  
				}
				if ($ls_creararchivo)
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=str_replace("-","",$ls_codcueban);
			    $ls_codcueban=trim($ls_codcueban);
			    $ls_codcueban=substr($ls_codcueban,0,20);
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9); 
				$ldec_neto=$rs_data->fields["monnetres"]*100;
			    $ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto, 12);
				$ls_cadena="0651".$ldec_neto.$ls_codcueban."000".$ls_cedper."00000000"."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_banfoandes_ipsfa
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_bod_version_3($as_ruta,$rs_data,$ad_fecproc,$as_codmetban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_bod_version_3
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//                 as_codmetban // c�digo del m�todo a banco
		//	  Description: genera el archivo txt a disco para  el banco BOD_version_3 para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
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
		$ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,4),4);	
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
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,12);
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban, 12);
				$ldec_neto=$rs_data->fields["monnetres"];
				$li_neto_int=$ldec_neto;
				$li_pos=strpos($ldec_neto,".");
				$li_neto_dec=substr($ldec_neto,$li_pos,3);
				$ldec_montot=$this->io_funciones->uf_trim($li_neto_int.$li_neto_dec);
				$ldec_montot=$this->io_funciones->uf_rellenar_izq($ldec_montot,"0",12);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper," ",15); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_nomper.", ".$ls_apeper," ",30);
				$ls_codper=$this->io_funciones->uf_rellenar_der(substr($rs_data->fields["codper"],0,10)," ",10);
				$li_dia=substr($ad_fecproc,0,2);
				$li_mes=substr($ad_fecproc,3,2);
				$li_ano=substr($ad_fecproc,6,4);
				$ld_fecproc=$li_ano.$li_mes.$li_dia;
				$ls_cadena='"'.$ls_codempnom.'","'.$ls_codcueban.'","'.$ls_cedper.'","'.$ls_personal.'",'.$ldec_montot.','.$ld_fecproc.','.
				           '"C"'.',"'.$ls_codper.'"'."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_bod_version_3
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	function uf_obtener_numero_negociacion($numcta) {
		$numnego = '';
		$cadenaSQL = "SELECT numnego
						FROM scb_ctabanco
						WHERE codemp = '{$_SESSION["la_empresa"]["codemp"]}'
						AND TRIM(ctabanext) = '{$numcta}'";
				 
		$rs_data=$this->io_sql->select($cadenaSQL);				  
		if ($rs_data===false) {
			$this->io_mensajes->message("CLASE->METODO BANCO->uf_obtener_numero_negociacion ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		}
		else {
			if (!$rs_data->EOF) {
				$numnego = $rs_data->fields['numnego'];
			}
		}
		unset($rs_data);		 
		return $numnego;
	}
	
	
	function uf_metodo_banco_bod_version_4($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$as_codcue,$as_numref)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_bod_version_4
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//                 as_codmetban // c�digo del m�todo a banco
		//	  Description: genera el archivo txt a disco para  el banco BOD_version_3 para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 04/09/2015 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
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
			$ld_monto = 0;
			$li_dia=substr($ad_fecproc,0,2);
			$li_mes=substr($ad_fecproc,3,2);
			$li_ano=substr($ad_fecproc,6,4);
			$ls_numref = substr($as_numref, 0, 9);
			$ls_numref = str_pad($ls_numref, 9, '0', STR_PAD_LEFT);
			
			//registro deatalle
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_rifper    = $rs_data->fields["nacper"].str_pad(trim($rs_data->fields["cedper"]),9,'0',STR_PAD_LEFT);
				$ls_codcueban = trim(str_replace("-","",$rs_data->fields["codcueban"]));
				$ldec_neto    = $rs_data->fields["monnetres"];
				$ls_nomper    = trim($rs_data->fields["nomper"]);
				$ls_apeper    = trim($rs_data->fields["apeper"]);
				$ls_codper=$this->io_funciones->uf_rellenar_der(substr($rs_data->fields["codper"],0,10)," ",10);
				$ld_monto = $ld_monto + $ldec_neto;
				$ls_codsudeban = trim($rs_data->fields["codsudeban"]);
				$ld_moncre  = str_replace(',', '', $ldec_neto);
				$ld_moncre  = str_replace('.', '', $ld_moncre);
				$ld_moncre  = str_pad($ld_moncre, 15, '0', STR_PAD_LEFT);
				$ld_monret  = str_pad('', 15, '0', STR_PAD_LEFT);
				$nombre     = str_pad($ls_apeper.' '.$ls_nomper, 60, ' ',STR_PAD_RIGHT);
				$descripcion = str_pad(substr($rs_data->fields["desnom"], 0, 30), 30, ' ',STR_PAD_RIGHT);
				$ls_email = str_pad(substr($rs_data->fields["correo"], 0, 40),40," ",STR_PAD_RIGHT);
				$telef = substr(preg_replace("/\D/", '', trim($rs_data->fields["telmovper"])), 0, 11);
				$ls_telef = str_pad($telef, 11, '0', STR_PAD_LEFT);
				$ls_blanco2 = str_pad("",20);
				$ls_swiftban  = $rs_data->fields['codswift'];
				$ls_modpag = 'BAN';
    			if($ls_swiftban == 'BODEVE2M')
				{
    				$ls_modpag  = 'CTA';
    			}
				
				$ls_cadena_det .= "02{$ls_rifper}{$nombre}{$ls_numref}{$descripcion}{$ls_modpag}{$ls_codcueban}{$ls_codsudeban}{$li_ano}{$li_mes}{$li_dia}{$ld_moncre}VEB{$ld_monret}{$ls_email}{$ls_telef}{$ls_blanco2}\r\n";
				
				$rs_data->MoveNext();	
			}
			
			//Registro Cabecera
			$ls_identi = str_pad('Proveedores', 20, ' ',STR_PAD_RIGHT);
			$ls_rifemp = trim($_SESSION["la_empresa"]["rifemp"]);
			$ls_letrif = substr($ls_rifemp,0,1);
			$ls_numrif = substr($ls_rifemp,1,15);
			$ls_numrif = str_pad(str_replace("-","",$ls_numrif), 9, '0', STR_PAD_LEFT);
			$ls_numnego = $this->uf_obtener_numero_negociacion(trim($as_codcue));
			$ls_numnego = str_pad(trim($ls_numnego), 17, '0', STR_PAD_LEFT);
			$ld_monto  = str_replace(',', '', $ld_monto);
			$ld_monto  = str_replace('.', '', $ld_monto);
			$ld_monto  = str_pad($ld_monto, 17, '0', STR_PAD_LEFT);
			$li_numope = str_pad($li_count, 6, '0', STR_PAD_LEFT);//numero de pagos
			$ls_blanco = str_pad("",158);
			$ls_cadena_cab = "01{$ls_identi}{$ls_letrif}{$ls_numrif}{$ls_numnego}{$ls_numref}{$li_ano}{$li_mes}{$li_dia}{$li_numope}{$ld_monto}VEB{$ls_blanco}\r\n";
			
			if ($ls_creararchivo) {
    			if (@fwrite($ls_creararchivo,$ls_cadena_cab.$ls_cadena_det)===false) {
    				$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
    				$lb_valido=false;
    			}
    		}
    		else {
    			$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
    			$lb_valido=false;
    		}
    	
    		@fclose($ls_creararchivo);	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_bod_version_3
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_bod_viejo($as_ruta,$rs_data,$ad_fecproc,$as_codmetban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_bod_viejo
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//                 as_codmetban // c�digo del m�todo a banco
		//	  Description: genera el archivo txt a disco para  el banco BOD_version_3 para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 08/05/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomiter.txt";
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
		$ls_codempnom=$this->io_funciones->uf_cerosizquierda(substr($ls_codempnom,0,4),4);	
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
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,10);
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_rellenar_der($ls_codcueban,"0",10);
				$ldec_neto=$rs_data->fields["monnetres"];
				$li_neto_int=$ldec_neto;
				$li_pos=strpos($ldec_neto,".");
				$li_neto_dec=substr($ldec_neto,$li_pos,3);
				$ldec_montot=$this->io_funciones->uf_trim($li_neto_int.$li_neto_dec);
				$ldec_montot=$this->io_funciones->uf_rellenar_izq($ldec_montot,"0",12);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",9); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_nomper.", ".$ls_apeper," ",30);
				$ls_codper=$this->io_funciones->uf_rellenar_der(substr($rs_data->fields["codper"],0,10)," ",10);
				$li_dia=substr($ad_fecproc,0,2);
				$li_mes=substr($ad_fecproc,3,2);
				$li_ano=substr($ad_fecproc,6,4);
				$ld_fecproc=$li_ano.$li_mes.$li_dia;
				$ls_cadena=$ls_codempnom.",".$ls_codcueban.",".$ls_cedper.",".$ls_personal.",".$ldec_montot.",".$ld_fecproc.","."C".",".$ls_codper."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_bod_viejo
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_banesco($as_ruta,$rs_data,$ad_fecproc)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banesco
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//	  Description: genera el archivo txt a disco para  el banco BANESCO para pago de nomina
		//	   Creado Por: Ing. Maria Roa
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;		
		$ls_space="         "; // 9 espacios
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
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
			//Cabecera del archivo
			$ls_cadena="NACIONALIDAD".$ls_space."CEDULA".$ls_space."CUENTA".$ls_space."SUELDO"."\r\n";
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
			//Registro de Detalle
			while((!$rs_data->EOF)&&($lb_valido))
			//for($li_i=1;(($li_i<=$li_count)&&($lb_valido));$li_i++)
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ldec_neto=$rs_data->fields["monnetres"]; 
				$ldec_montot=number_format($ldec_neto*100,0,"","");  
				$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,15);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
			    $ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cadena=$ls_nacper.$ls_space.$ls_cedper.$ls_space.$ls_codcueban.$ls_space.$ldec_montot."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_banesco
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_canarias($as_ruta,$rs_data,$ad_fhasta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_canarias
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fhasta // fecha donde termina el per�odo
		//	  Description: genera el archivo txt a disco para  el banco CANARIAS para pago de nomina
		//	   Creado Por: Ing. Maria Roa
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_dia=substr($ad_fhasta,8,2);
		$li_mes=substr($ad_fhasta,5,2);
		$li_ano=substr($ad_fhasta,0,4);
		$ls_nombrearchivo=$as_ruta."/nomina".$li_dia.$li_mes.$li_ano.".txt";
		//Registro tipo E
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
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=($ldec_neto*100);  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,11);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],9);
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_canarias
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_caracas($as_ruta,$rs_data,$adec_montot,$as_codcueban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_caracas
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   adec_montot // Monto Total a depositar
		//	    		   as_codcueban // c�digo cuenta bancaria a debitar
		//	  Description: genera el archivo txt a disco para  el banco CARACAS para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		//Registro tipo E
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
			while((!$rs_data->EOF)&&($lb_valido))
			{
				//$ls_codcueban=substr($aa_ds_banco->data["codcueban"][$li_i],0,11);
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,11);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban, 11);
				//$ldec_neto=$aa_ds_banco->data["monnetres"][$li_i];
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=($ldec_neto*100);  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,13);
				$ls_space="    ";
				$ls_cadena="NC".$ls_codcueban.$ldec_neto.$ls_space."\r\n";
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
			//Resumen del deposito
			$as_codcueban=substr($as_codcueban,0,11);
			$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			$as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 11);
			$adec_montot=round($adec_montot,2); 
			$adec_montot=($adec_montot*100);  
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,13);
			$ls_cadena="ND".$as_codcueban.$adec_montot.$ls_space."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_caracas
	//-----------------------------------------------------------------------------------------------------------------------------------/*
	
	//-----------------------------------------------------------------------------------------------------------------------------------/*
	function uf_metodo_banco_caroni($as_ruta,$rs_data)
	{  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_caroni
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	  Description: genera el archivo txt a disco para  el banco CARONI para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 09/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		//Registro tipo E
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
			while((!$rs_data->EOF)&&($lb_valido))
			{				
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,20);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=($ldec_neto*100);  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,10);				
				$ls_nacper=$this->io_funciones->uf_rellenar_izq($rs_data->fields["nacper"]," ",1);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($rs_data->fields["cedper"],10);				
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_apeper.",".$ls_nomper," ",40);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_nombre.$ls_codcueban.$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_caroni
	//-----------------------------------------------------------------------------------------------------------------------------------/*

	//-----------------------------------------------------------------------------------------------------------------------------------/*
	function uf_metodo_banco_casapropia($as_ruta,$rs_data,$ad_fecproc,$as_codcuenta,$adec_montot)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_casapropia
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codcuenta // c�digo de cuenta a debitar
		//	    		   adec_montot // monto total a depositar
		//	  Description: genera el archivo txt a disco para  el Banco Casa Propia para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 09/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ldec_monpre=0;
		$ldec_monacu=0;
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
			//Registro del deposito
			$ls_rifemp=str_replace("-","",$this->ls_rifemp);
			$ls_siglas=substr($this->ls_siglas,0,7);
			$li_numdebprev=1;
			$li_numcreprev=$li_count;
			$li_numcreprev=$this->io_funciones->uf_cerosizquierda($li_numcreprev,5);
			$ldec_totdep=$adec_montot;
			$ldec_monacu=$ldec_totdep;
			$ldec_monacu=$this->io_funciones->uf_cerosizquierda($ldec_monacu*100,12); 
			$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcuenta));
			$ls_codcueban=$this->io_funciones->uf_rellenar_der($ls_codcueban," ",25);
			$li_dia=substr($ad_fecproc,0,2);
			$li_mes=substr($ad_fecproc,3,2);
			$li_ano=substr($ad_fecproc,6,4);
			$ld_fecha=$li_ano.$li_mes.$li_dia;
			$ls_cadena=$ls_rifemp.$ls_siglas.$li_numcreprev.$ls_codcueban.$ld_fecha.$ldec_monacu."\r\n";
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
			$li_numcreprev=0;
			$li_numcreprev=0;
			//Registro tipo E
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,10);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban, 10);
				$ldec_neto=$rs_data->fields["monnetres"];  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100, 12);
				$ls_cadena=$ls_codcueban.$ldec_neto."\r\n";
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
				$li_numcreprev = $li_numcreprev + 1;
				$rs_data->MoveNext();	
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
	}// end function metodo_banco_casapropia
	//-----------------------------------------------------------------------------------------------------------------------------------/*

	//-----------------------------------------------------------------------------------------------------------------------------------/*
	function uf_metodo_banco_central($as_ruta,$rs_data,$as_codcueban,$adec_montot)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_central
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codcuenta // c�digo de cuenta a debitar
		//	    		   adec_montot // monto total a depositar
		//	  Description: genera el archivo txt a disco para  el banco CENTRAL para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 09/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomdes.txt";
		//Registro tipo E
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
			$li_i=1;
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=substr($rs_data->fields["codcueban"],-10);//Modificado estaba antes $ls_codcueban=substr($rs_data->fields["codcueban"],0,10);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban, 10);
				$ldec_neto=$rs_data->fields["monnetres"];  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto*100,13);
				$ls_tipcueban=$rs_data->fields["tipcuebanper"];
				switch ($ls_tipcueban)
				{
					case "C":
						  $ls_tipocuenta="C";  //Corriente
						  break;
						  
					case "A":
						  $ls_tipocuenta="H";  //Ahorro
						  break;	
						  
					case "L":
						  $ls_tipocuenta="H";  //Activos Liquidos
						  break;	
						  
					default:	
						 $ls_tipocuenta="H";  
						 break;	    
				}
				$li_consecutivo=$this->io_funciones->uf_cerosizquierda($li_i, 8);
				$ls_cadena="A".$ls_tipocuenta."202".$ls_codcueban.$li_consecutivo.$ldec_neto."0506"."\r\n";
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
				$li_i++;					
			} 
			//Registro tipo T
			$li_consecutivo=$this->io_funciones->uf_cerosizquierda($li_i, 8);
			$as_codcueban=substr($as_codcueban,-10);// Modificado estaba antes $as_codcueban=substr($as_codcueban,0,10);
			$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			$as_codcueban=$this->io_funciones->uf_cerosizquierda($as_codcueban, 10);
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot*100,13);
            $ls_cadena="AC402".$as_codcueban.$li_consecutivo.$adec_montot."0506"."\r\n";	
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_central
	//-----------------------------------------------------------------------------------------------------------------------------------/*

	//-----------------------------------------------------------------------------------------------------------------------------------/*
	function uf_metodo_banco_del_sur_eap($as_ruta,$rs_data,$ad_fhasta,$as_codmetban)  
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_del_sur_eap
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fhasta // Fecha hasta del per�odo
		//	    		   as_codmetban // C�digo del M�todo a banco
		//	  Description: genera el archivo txt a disco para  el banco del_sur_eap para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 09/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		//Registro tipo E
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
		$ls_codempnom=$this->io_funciones->uf_cerosizquierda(trim(substr($ls_codempnom,0,4)),4);	
		$ls_numconvenio=$this->io_funciones->uf_cerosizquierda(trim(substr($ls_numconvenio,0,8)),8);	
		$li_count=$rs_data->RecordCount();
		if(($li_count>0)&&($lb_valido))
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
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,10);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban, 10);
				$ldec_neto = $rs_data->fields["monnetres"];
				$ldec_neto=$this->io_funciones->uf_cerosizquierda(($ldec_neto*100),10);
				$ls_cedper=$this->io_funciones->uf_cerosizquierda(trim($rs_data->fields["cedper"]),10);
				$ls_nomper=rtrim($rs_data->fields["nomper"]);
				$ls_apeper=rtrim($rs_data->fields["apeper"]);
				$ls_nombre=substr($ls_nomper." ".$ls_apeper,0,30);
				$ls_nombre=$this->io_funciones->uf_rellenar_der($ls_nombre," ",30);
				$ls_cadena=$ls_codempnom.$ls_cedper.$ls_codcueban.$ldec_neto."C".$ls_numconvenio.$ls_nombre."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_del_sur_eap
	//-----------------------------------------------------------------------------------------------------------------------------------/*

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_biv_version_2($as_ruta,$rs_data,$as_codmetban,$adec_montot)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_eap_micasa
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   as_codmetban // c�digo de m�todo a banco
		//	    		   adec_montot // monto total a depositar
		//	  Description: genera el archivo txt a disco para  el banco industrial de venezuela version_2 para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 10/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
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
		$ls_codofinom=substr($ls_codofinom,0,3);
		$li_count=$rs_data->RecordCount();
		if(($li_count>0)&&($lb_valido))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				if ($aa_ds_banco->data["tipcuebanper"][$li_i]=="A")
				{
					$ls_tipcuebanper = "2";
				}
				else
				{
					$ls_tipcuebanper = "1";
				}
				//$ls_codcueban=substr($aa_ds_banco->data["codcueban"][$li_i],0,12);
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,12);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,12);
				$ldec_neto = $rs_data->fields["monnetres"];
				$li_neto_int=substr($ldec_neto,0,10);
				$li_pos=strpos($ldec_neto, ".");
				$li_neto_dec=substr($ldec_neto,$li_pos,3);
				$ldec_montot=$this->io_funciones->uf_trim($li_neto_int.$li_neto_dec);
				$ldec_montot=$this->io_funciones->uf_cerosderecha($ldec_montot,12);				
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper," ",10); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_nomper=$this->io_funciones->uf_rellenar_der($ls_nomper," ",15);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_apeper=$this->io_funciones->uf_rellenar_der($ls_apeper," ",15);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cadena=$ls_tipcuebanper.$ls_nacper.$ls_cedper.$ls_apeper.$ls_nomper.$ls_codofinom."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function metodo_banco_biv_version_2
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_central_v1($as_ruta,$rs_data,$as_codcuenta,$adec_montot)
	{  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_eap_micasa
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   as_codmetban // c�digo de m�todo a banco
		//	    		   adec_montot // Monto total a depositar
		//	  Description: genera el archivo txt a disco para  el banco CENTRAL version 1 para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 10/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomdes.txt";
		//Registro tipo E
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=substr($rs_data->fields["codcueban"],0,10);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,10);
				$ldec_neto=$rs_data->fields["monnetres"]*100;  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,13);
				$li_reg=$this->io_funciones->uf_cerosizquierda($li_i,8);
				$ls_cadena = "AC202".$ls_codcueban.$li_reg.$ldec_neto."0506"."\r\n";			
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
			if($lb_valido)
			{
				//Registro tipo T
				$ls_codcueban=substr($as_codcuenta,0,10);
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,10);
				$ldec_totdep=$this->io_funciones->uf_cerosizquierda($adec_montot*100,13);
				$ls_cadena="AC402".$ls_codcueban.$li_reg.$ldec_totdep."0506"."\r\n";	
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
    }// end function uf_metodo_banco_central_v1
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_e_provincial($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_e_provincial
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 28/08/2006 								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/BSF0000W.txt";
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
				$ls_codcueban=$rs_data->fields["codcueban"];
			   	$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=str_pad($ls_codcueban,20," ",0);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",8); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_nomper.", ".$ls_apeper," ",35);
				$ldec_neto=($rs_data->fields["monnetres"]*100);
				$li_neto_int=$this->io_funciones->uf_rellenar_izq($ldec_neto,"0",15);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ls_personal.$li_neto_int."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_e_provincial
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_e_provincial_02($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_e_provincial_02
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 28/08/2006 								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/BSF0000W.txt";
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			        $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=str_pad($ls_codcueban,20," ",0);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",9); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=substr($ls_nomper.", ".$ls_apeper,0,30);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_personal," ",30);
				$ldec_neto=($rs_data->fields["monnetres"]*100);
				$li_neto_int=$this->io_funciones->uf_rellenar_izq($ldec_neto,"0",13);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ls_personal.$li_neto_int."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_e_provincial_02
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_e_provincial_03($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_e_provincial_03
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 05/06/2007 								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/BSF0000W.txt";
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=rtrim($rs_data->fields["codcueban"]);
			        $ls_codcueban=str_replace("-","",$ls_codcueban);
				$ls_codcueban=str_pad($ls_codcueban,20," ",0);
				$ls_nacper=rtrim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",8); 
				$ls_nomper=rtrim($rs_data->fields["nomper"]);
				$ls_apeper=rtrim($rs_data->fields["apeper"]);
				$ls_personal=substr($ls_nomper." ".$ls_apeper,0,32);
				$ls_personal=str_pad($ls_personal,32," ");
				$ldec_neto=($rs_data->fields["monnetres"]*100);
				$li_neto_int=$this->io_funciones->uf_rellenar_izq($ldec_neto,"0",15);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_personal.$ls_codcueban.$li_neto_int."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_e_provincial_03
	//-----------------------------------------------------------------------------------------------------------------------------------   
	function uf_metodo_banco_provincial_altamira($as_ruta,$rs_data,$as_metodo,$as_codcueban,$adec_montot,$ad_fecproc)
	{		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_provincial_altamira
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creacion: 05/06/2008 								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	    $lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/NOMINA.txt";
		//Chequea si existe el archivo.
		$li_count=$rs_data->RecordCount();
		if ($li_count>0)
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
				// Registro de la cabecera (Datos Fijos)
				$ldec_montot=$adec_montot;           
				$ldec_montot=($ldec_montot*100);  
				$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,17);
				$ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
			    $ad_fecproc=str_replace("-","",$ad_fecproc);
				$ls_rifempresa=str_replace("-","",$_SESSION["la_empresa"]["rifemp"]);
			    $ls_rif=$this->io_funciones->uf_rellenar_izq($ls_rifempresa," ",10);
				$ls_refdebcre="        ";
				$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
			    $ls_nomemp=$this->io_funciones->uf_rellenar_der(substr(rtrim($ls_nomemp),0,27)," ",27);				
				$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			    $as_codcueban=substr($as_codcueban,12,8); 
				$cant_registros=str_pad($li_count,7,0,"LEFT");
				$arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_metodo,"0",$ls_codempnom,
				                                                          $ls_codofinom,$ls_tipcuecre,
																		  $ls_tipcuedeb,$ls_numconvenio);
				$ls_codempnom=$arrResultado['as_codempnom'];
				$ls_codofinom=$arrResultado['as_codofinom'];
				$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
				$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
				$ls_numconvenio=$arrResultado['as_numconnom'];
				$lb_valido=$arrResultado['lb_valido'];		
				if ($ls_numconvenio=="")
				{
				 	$ls_numconvenio="XXXX";
				}
				if ($ls_codofinom=="")
				{
					$ls_codofinom="XXXX";
				}
				
				$ls_disponible_C="                                              ";
				if($lb_valido)
				{		
				    $ls_cadena="01"."01".$ls_numconvenio.$ls_codofinom."00".$ls_tipcuedeb.$as_codcueban.$cant_registros.
					           $ldec_montot."VEB".$ad_fecproc.$ls_rif.$ls_refdebcre.$ls_nomemp.$ls_disponible_C."\r\n";
							  
				}
				if ($ls_creararchivo)
				{
					if (@fwrite($ls_creararchivo,$ls_cadena)===false)//Escritura
					{
						$this->io_mensajes->message("No se puede escribir el archivo ".$ls_nombrearchivo);
						$lb_valido = false;
					}
				}
				///----------registro individual obligatorio-------------------------------------------------------------------
				while((!$rs_data->EOF)&&($lb_valido))
			   	 {
				    $ls_tipo_registro="02";
					$l_codban="0108";
					$ls_dig_cheq="00";
					$ls_codcueban=rtrim($rs_data->fields["codcueban"]);
			    		$ls_codcueban=str_replace("-","",$ls_codcueban);
					$li_inicio=strlen($ls_codcueban)-12;
					$ls_codcueban=substr($ls_codcueban,$li_inicio,8);
					$ls_codcueban=$this->io_funciones->uf_rellenar_der($ls_codcueban," ",8);
					
					$ls_tipcta=rtrim($rs_data->fields["tipcuebanper"]);
					if($ls_tipcta=="A")
					{
						$ls_tipo="01";
					}
					else
					{
						$ls_tipo="02";
					}
					$ls_nacper=rtrim($rs_data->fields["nacper"]);
					$ls_cedper=$this->io_funciones->uf_trim($aa_ds_banco->data["cedper"]);
					$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",9);
					$referencia=$ls_nacper.$ls_cedper;
					$referencia=str_pad($referencia,16," ");
					$ls_nomper=rtrim($rs_data->fields["nomper"]);
					$ls_apeper=rtrim($rs_data->fields["apeper"]);
					$ls_personal=substr($ls_apeper.", ".$ls_nomper,0,40);
					$ls_personal=str_pad($ls_personal,40," ");
					$ldec_neto=($rs_data->fields["monnetres"]*100);
					$otros_datos="                              ";					
					$resultado="00";
					$refdebcre="        ";	
					$ls_disponible_IO="               ";						
					$li_neto_int=$this->io_funciones->uf_rellenar_izq($ldec_neto,"0",17);				
					$ls_cadena=$ls_tipo_registro.$l_codban.$ls_codofinom.$ls_dig_cheq.$ls_tipo.$ls_codcueban.
					           $referencia.$li_neto_int.$ls_personal.$otros_datos.$resultado.$refdebcre.$ls_disponible_IO."\r\n";
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
			    }//fin  del for				
			    //----------------------fin del regitro obligatorio----------------------------------------------------		
			}
			if ($lb_valido)
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
		
	}// end function uf_metodo_banco_provincial_altamira
//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_metodo_banco_provincial_BBVAcash($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_provincial_BBVAcash
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creacion: 19/02/2009								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/banco_provincial.txt";
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=str_pad($ls_codcueban,20,"0",0);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",9); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=substr($ls_apeper." ".$ls_nomper,0,30);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_personal," ",30);
				$ldec_neto=($rs_data->fields["monnetres"]*100);
				$li_neto_int=$this->io_funciones->uf_rellenar_izq($ldec_neto,"0",14);
				$ls_relleno=str_pad(' ',6," ",0);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_personal.$ls_codcueban.$li_neto_int.$ls_relleno."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_provincial_BBVAcash
	
//--------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_provincial_BBVAcash_1($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_provincial_BBVAcash
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creacion: 19/02/2009								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/BBVACASH.txt";
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=str_pad($ls_codcueban,20,"0",0);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",8); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=substr($ls_apeper." ".$ls_nomper,0,30);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_personal," ",30);
				$ldec_neto=($rs_data->fields["monnetres"]);
				
					
				
				$li_pos=strpos($ldec_neto,".");
				if ($li_pos!="")
				{
					$ldec_neto_dec=substr($ldec_neto,$li_pos+1,2);
					$ldec_neto_dec=$this->io_funciones->uf_rellenar_der($ldec_neto_dec,"0",2);
					$ldec_neto_mon=substr($ldec_neto,0,$li_pos);
					$ldec_neto_mon=$this->io_funciones->uf_rellenar_izq($ldec_neto_mon,"0",8);
				}
				else
				{
					$ldec_neto_dec="";
					$ldec_neto_dec=$this->io_funciones->uf_rellenar_der($ldec_neto_dec,"0",2);
					$ldec_neto_mon=$ldec_neto;
					$ldec_neto_mon=$this->io_funciones->uf_rellenar_izq($ldec_neto_mon,"0",8);
				}
				
				$li_pos_nom=strpos($ls_nomper," ");
				if ($li_pos_nom!="")
				{
					$ls_prinom=substr($ls_nomper,0,$li_pos_nom);
				}
				else
				{
					$ls_prinom=$ls_nomper;
				}
				
				$li_pos_ape=strpos($ls_apeper," ");
				if ($li_pos_ape!="")
				{
					$ls_priape=substr($ls_apeper,0,$li_pos_ape);
				}
				else
				{
					$ls_priape=$ls_apeper;
				}
				$ls_relleno=str_pad(' ',6," ",0);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto_mon.$ldec_neto_dec.$ls_prinom." ".$ls_priape."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_banesco

//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_corp_banca($as_ruta,$rs_data,$adec_montot,$as_codperi,$as_perides,$as_perihas)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_corp_banca
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Corp Banca para pago de nomina
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creacion: 14/05/2008 								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=rtrim($rs_data->fields["codcueban"]);				
			    $ls_codcueban=str_replace("-","",$ls_codcueban);
				$tamano=strlen($ls_codcueban);
				if ($tamano>10)
				 {
				   $ls_codcueban=substr($ls_codcueban,$tamano-10,$tamano);				
				 }
				$ls_codcueban=str_pad($ls_codcueban,12,"0","left");
				$ls_nacper=rtrim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_cedper," ",15); 
				$ls_nomper=rtrim($rs_data->fields["nomper"]);
				$ls_apeper=rtrim($rs_data->fields["apeper"]);
				$ls_personal=substr($ls_apeper.", ".$ls_nomper,0,70);
				$ls_personal=str_pad($ls_personal,70," ");
				$ldec_neto=($rs_data->fields["monnetres"]*100);
				$li_neto_int=$this->io_funciones->uf_rellenar_izq($ldec_neto,"0",15);				
				$ls_ano1=substr($as_perides,2,2);
				$ls_mes1=substr($as_perides,5,2);
				$ls_dia1=substr($as_perides,8,2);
				$ls_ano2=substr($as_perihas,2,2);
				$ls_mes2=substr($as_perihas,5,2);
				$ls_dia2=substr($as_perihas,8,2);
				$ls_cadena=$ls_cedper.$ls_codcueban.$li_neto_int.$as_codperi.$ls_dia1.
				           $ls_mes1.$ls_ano1.$ls_dia2.$ls_mes2.$ls_ano2."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_corp_banca
	//--------------------------------------------------------------------------------------------------------------------------------
	
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codmetban // C�digo del Metodo
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 12/06/2008 								
		// Modificado Por: 											Fecha ultima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$ls_nombre_nom=$ls_nombre_nom=$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
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
		$ls_numope=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANCO_DEL_TESORO","1","I");
		$ls_numope=intval($this->io_funciones->uf_trim($ls_numope), 10);
                $lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANCO_DEL_TESORO",$ls_numope+1,"I");
		$ls_numope=str_pad($ls_numope,10,"0",0); // se completa hasta cuatro digitos	
                $ls_codempnom=substr($ls_codempnom,0,4);
		$ls_codempnom=str_pad($ls_codempnom,4,"0",0); // se completa hasta cuatro digitos	
		$li_diapp=substr($ad_fecproc,0,2);
		$li_mespp=substr($ad_fecproc,3,2);
		$li_anopp=substr($ad_fecproc,8,2);
		$li_count=$rs_data->RecordCount();
		$ls_nombrearchivo=$as_ruta."/".$ls_nombre_nom.$ls_codempnom.$li_diapp.$li_mespp.$li_anopp.".txt";
		if(($li_count>0)&&($lb_valido))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			/// PARA LA CABECERA DEL ARCHIVO
			$ls_rif=$this->ls_rifemp;
			$ls_rif=str_replace("-","",$ls_rif);
			$ls_rif=str_pad($ls_rif,15," ");
			$adec_montot=number_format($adec_montot,2,".","");  
			$adec_montot=number_format($adec_montot*100,0,"","");  
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,15);
			$ld_fecreg=$ad_fecproc;
			$ld_anoreg=substr($ld_fecreg,8,2);
			$ld_mesreg=substr($ld_fecreg,3,2);
			$ld_diareg=substr($ld_fecreg,0,2);
			$ld_fecreg=$ld_diareg.$ld_mesreg.$ld_anoreg;
			$li_totreg=$li_count;
			$ld_fecpagotot=date("d/m/Y");
			$li_anopp=substr($ld_fecpagotot,8,2);
			$li_mespp=substr($ld_fecpagotot,3,2);
			$li_diapp=substr($ld_fecpagotot,0,2);
			$ld_fecpago=$li_diapp.$li_mespp.$li_anopp;
			$ls_nrocuebanemp=$as_codcueban;
			$ls_nrocuebanemp=$this->io_funciones->uf_trim(str_replace("-","",$ls_nrocuebanemp));
			$ls_nrocuebanemp=$this->io_funciones->uf_cerosizquierda($ls_nrocuebanemp,20);
			$ls_numconvenio=str_pad($ls_numconvenio,10,"0","left");
			$ls_cabecera='H'.$ls_codempnom.$ls_numope.$ls_nrocuebanemp.$ls_rif.$ld_fecreg.$ld_fecpago.$li_totreg.$adec_montot."\r\n";
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
				$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
				$lb_valido=false;
			}		
			/// PARA EL DETALLE DEL ARCHIVO
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_tipreg='D';
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedula=$ls_nacper.$ls_cedper;
				$ls_cedula=str_pad($ls_cedula,15," ");
				$ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
				$ldec_neto=number_format($ldec_neto*100,0,"","");  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				
				$ls_cadena=$ls_tipreg.$ls_codcueban.$ls_cedula.$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro_esp($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codmetban // C�digo del Metodo
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 12/06/2008 								
		// Modificado Por: 											Fecha ultima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$ls_nombre_nom=$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
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
		$ls_codempnom=substr($ls_codempnom,0,4);
		$li_diapp=substr($ad_fecproc,0,2);
		$li_mespp=substr($ad_fecproc,3,2);
		$li_anopp=substr($ad_fecproc,8,2);
		$li_count=$rs_data->RecordCount();
		$ls_cuantos=$this->io_funciones->uf_cerosizquierda($li_count,4);
		$ls_empresanew=$this->io_funciones->uf_cerosizquierda($ls_codempnom,4);
		$ls_nombrearchivo=$as_ruta."/".$ls_empresanew.$ls_cuantos.$li_diapp.$li_mespp.$li_anopp.".txt";
		if(($li_count>0)&&($lb_valido))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			/// PARA LA CABECERA DEL ARCHIVO
			$ls_codempnom=str_pad($ls_codempnom,4,"0",0); // se completa hasta cuatro digitos	
			$ls_rif=$this->ls_rifemp;
			$ls_rif=str_replace("-","",$ls_rif);
			$ls_rifcomp=substr($ls_rif,1,10);
			$ls_rifcomp=$this->io_funciones->uf_cerosizquierda($ls_rifcomp,10);
			$ls_rifini=substr($ls_rif,0,1);
			$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
			$ls_nomemp=str_pad($ls_nomemp,40," ");
			$adec_montot=number_format($adec_montot,2,".","");  
			$adec_montot=number_format($adec_montot*100,0,"","");  
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,15);
			$ld_fecreg=date("d/m/Y");
			$ld_anoreg=substr($ld_fecreg,8,2);
			$ld_mesreg=substr($ld_fecreg,3,2);
			$ld_diareg=substr($ld_fecreg,0,2);
			$ld_fecreg=$ld_anoreg.$ld_mesreg.$ld_diareg;
			$li_totreg=str_pad($li_count,5,"0","left");
			$ld_fecpago=$li_anopp.$li_mespp.$li_diapp;
			$ls_nrocuebanemp=$as_codcueban;
			$ls_nrocuebanemp=$this->io_funciones->uf_trim(str_replace("-","",$ls_nrocuebanemp));
			$ls_nrocuebanemp=$this->io_funciones->uf_cerosizquierda($ls_nrocuebanemp,20);
			//$ls_codnom=$aa_ds_banco->data["codnom"][1];	
			//$ls_numconvenio=$rs_data->fields["codnom"];	
			$ls_numconvenio=str_pad($ls_numconvenio,10,"0","left");
			//$ls_cabecera='H'.$ls_codempnom.$ls_numconvenio.$ls_nrocuebanemp.$ls_rif.$ld_fecreg.$ld_fecpago.$li_totreg.$adec_montot."\r\n";
			
			/// PARA EL DETALLE DEL ARCHIVO
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_tipreg='D';
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				//$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nombre=$rs_data->fields["nomper"];
				$ls_apellido=$rs_data->fields["apeper"];
				$ls_nombrecompleto=$ls_nombre." ".$ls_apellido;
				$ls_nombrecompleto=str_pad($ls_nombrecompleto,40," ");
				$ls_nombrecompleto=str_replace(".","",$ls_nombrecompleto);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,10);
				$ls_cedula=$ls_nacper.$ls_cedper;
				//$ls_cedula=str_pad($ls_cedula,15," ");
				$ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
				$ldec_neto=number_format($ldec_neto*100,0,"","");  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				
				$ls_cadena=$ls_rifini.$ls_rifcomp.$ls_nomemp.$ls_nrocuebanemp.$ls_cedula.$ls_nombrecompleto.$ls_codempnom.$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro_2008($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro_2008
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // fecha de procesamiento
		//                 as_codmetban // c�digo del m�todo a banco
		//	    		   adec_montot // monto total a ser aplicado
		//                 as_codcueban // c�digo de cuenta de banco
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina, seg�n instrutivo
		//                 este m�todo contiene datos en la cabecera y el detalle.
		//	   Creado Por: Ing. Mar�a Beatriz Unda
		// Fecha Creacion: 02/10/2005 								
		// Modificado Por: 											Fecha ultima Modificacion : /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
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
		$ls_codempnom=substr($ls_codempnom,0,4);
		$li_diapp=substr($ad_fecproc,0,2);
		$li_mespp=substr($ad_fecproc,3,2);
		$li_anopp=substr($ad_fecproc,8,2);
		$li_count=$rs_data->RecordCount();
		if(($li_count>0)&&($lb_valido))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			/// PARA LA CABECERA DEL ARCHIVO
			$ls_codempnom=str_pad($ls_codempnom,4,"0","left"); // se completa hasta cuatro digitos	
			$ls_rif=$this->ls_rifemp;
			$ls_rif=str_replace("-","",$ls_rif);
			$ls_rif=str_pad($ls_rif,15," ");
			$adec_montot=number_format($adec_montot,2,".","");  
			$adec_montot=number_format($adec_montot*100,0,"","");  
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,15);
			$ld_fecreg=date("d/m/Y");
			$ld_anoreg=substr($ld_fecreg,8,2);
			$ld_mesreg=substr($ld_fecreg,3,2);
			$ld_diareg=substr($ld_fecreg,0,2);
			$ld_fecreg=$ld_anoreg.$ld_mesreg.$ld_diareg;
			$li_totreg=str_pad($li_count,5,"0","left");
			$ld_fecpago=$li_anopp.$li_mespp.$li_diapp;
			$ls_nrocuebanemp=$as_codcueban;
			$ls_nrocuebanemp=$this->io_funciones->uf_trim(str_replace("-","",$ls_nrocuebanemp));
			$ls_nrocuebanemp=$this->io_funciones->uf_cerosizquierda($ls_nrocuebanemp,20);
			//$ls_codnom=$aa_ds_banco->data["codnom"][1];	
			$ls_codnom=$rs_data->fields["codnom"];	
			$ls_codnom=str_pad($ls_codnom,10,"0","left");
			$ls_cabecera='H'.$ls_codempnom.$ls_codnom.$ls_nrocuebanemp.$ls_rif.$ld_fecreg.$ld_fecpago.$li_totreg.$adec_montot."\r\n";
			
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
				$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
				$lb_valido=false;
			}		
			/// PARA EL DETALLE DEL ARCHIVO
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_tipreg='D';
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedula=$ls_nacper.$ls_cedper;
				$ls_cedula=str_pad($ls_cedula,15," ");
				$ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
				$ldec_neto=number_format($ldec_neto*100,0,"","");  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				
				$ls_cadena=$ls_tipreg.$ls_codcueban.$ls_cedula.$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro_2008
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro_2012_2($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codmetban // C�digo del Metodo
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 12/06/2008 								
		// Modificado Por: 											Fecha ultima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		//$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$ls_nombre_nom=$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
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
		$ls_codempnom=substr($ls_codempnom,0,4);
		$li_diapp=substr($ad_fecproc,0,2);
		$li_mespp=substr($ad_fecproc,3,2);
		$li_anopp=substr($ad_fecproc,8,2);
		$li_count=$rs_data->RecordCount();
		$ls_nombrearchivo=$as_ruta."/".$ls_nombre_nom.$ls_codempnom.$li_diapp.$li_mespp.$li_anopp.".txt";
		if(($li_count>0)&&($lb_valido))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			/// PARA LA CABECERA DEL ARCHIVO
			$ls_codempnom=str_pad($ls_codempnom,4,"0","left"); // se completa hasta cuatro digitos	
			$ls_rif=$this->ls_rifemp;
			$ls_rif=str_replace("-","",$ls_rif);
			$ls_rif=str_pad($ls_rif,15," ");
			$adec_montot=number_format($adec_montot,2,".","");  
			$adec_montot=number_format($adec_montot*100,0,"","");  
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,15);
			$ld_fecreg=$ad_fecproc;
			$ld_anoreg=substr($ld_fecreg,8,2);
			$ld_mesreg=substr($ld_fecreg,3,2);
			$ld_diareg=substr($ld_fecreg,0,2);
			//$ld_fecreg=$ld_anoreg.$ld_mesreg.$ld_diareg;
			$ld_fecreg=$ld_diareg.$ld_mesreg.$ld_anoreg;
			//$li_totreg=str_pad($li_count,5,"0","left");
			$li_totreg=$li_count;
			//$ld_fecpago=$li_anopp.$li_mespp.$li_diapp;
			$ld_fecpagotot=date("d/m/Y");
			$li_anopp=substr($ld_fecpagotot,8,2);
			$li_mespp=substr($ld_fecpagotot,3,2);
			$li_diapp=substr($ld_fecpagotot,0,2);
			$ld_fecpago=$li_diapp.$li_mespp.$li_anopp;
			$ls_nrocuebanemp=$as_codcueban;
			$ls_nrocuebanemp=$this->io_funciones->uf_trim(str_replace("-","",$ls_nrocuebanemp));
			$ls_nrocuebanemp=$this->io_funciones->uf_cerosizquierda($ls_nrocuebanemp,20);
			//$ls_codnom=$aa_ds_banco->data["codnom"][1];	
			//$ls_numconvenio=$rs_data->fields["codnom"];	
			$ls_numconvenio=str_pad($ls_numconvenio,10,"0","left");
			$ls_cabecera='H'.$ls_codempnom.$ls_numconvenio.$ls_nrocuebanemp.$ls_rif.$ld_fecreg.$ld_fecpago.$li_totreg.$adec_montot."\r\n";
			
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
				$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
				$lb_valido=false;
			}		
			/// PARA EL DETALLE DEL ARCHIVO
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_tipreg='D';
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=substr($ls_codcueban,10,20);
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedula=$ls_nacper.$ls_cedper;
				$ls_cedula=str_pad($ls_cedula,15," ");
				$ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
				$ldec_neto=number_format($ldec_neto*100,0,"","");  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				
				$ls_cadena=$ls_tipreg.$ls_codcueban.$ls_cedula.$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_bnc($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codmetban // C�digo del Metodo
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 12/06/2008 								
		// Modificado Por: 											Fecha ultima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$ls_nombre_nom=$this->ls_codnom=$_SESSION["la_nomina"]["codnom"];
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
		$ls_codempnom=substr($ls_codempnom,0,4);
		$li_diapp=substr($ad_fecproc,0,2);
		$li_mespp=substr($ad_fecproc,3,2);
		$li_anopp=substr($ad_fecproc,8,2);
		$li_count=$rs_data->RecordCount();
		//$ls_nombrearchivo=$as_ruta."/".$ls_nombre_nom.$ls_codempnom.$li_diapp.$li_mespp.$li_anopp.".txt";
		if(($li_count>0)&&($lb_valido))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			/// PARA LA CABECERA DEL ARCHIVO
			$ls_codempnom=str_pad($ls_codempnom,4,"0","left"); // se completa hasta cuatro digitos	
			$ls_rif=$this->ls_rifemp;
			$ls_rif=str_replace("-","",$ls_rif);
			$ls_rif=$this->io_funciones->uf_cerosizquierda($ls_rif,9);
			$ls_rif1=substr($ls_rif,0,1);
			$ls_rif2=substr($ls_rif,1,9);
			$adec_montot=number_format($adec_montot,2,".","");  
			$adec_montot=number_format($adec_montot*100,0,"","");  
			$adec_montot=$this->io_funciones->uf_cerosizquierda($adec_montot,13);
			$ld_fecreg=$ad_fecproc;
			$ld_anoreg=substr($ld_fecreg,8,2);
			$ld_mesreg=substr($ld_fecreg,3,2);
			$ld_diareg=substr($ld_fecreg,0,2);
			//$ld_fecreg=$ld_anoreg.$ld_mesreg.$ld_diareg;
			$ld_fecreg=$ld_diareg.$ld_mesreg.$ld_anoreg;
			//$li_totreg=str_pad($li_count,5,"0","left");
			$li_totreg=$li_count;
			//$ld_fecpago=$li_anopp.$li_mespp.$li_diapp;
			$ld_fecpagotot=date("d/m/Y");
			$li_anopp=substr($ld_fecpagotot,8,2);
			$li_mespp=substr($ld_fecpagotot,3,2);
			$li_diapp=substr($ld_fecpagotot,0,2);
			$ld_fecpago=$li_diapp.$li_mespp.$li_anopp;
			$ls_nrocuebanemp=$as_codcueban;
			$ls_nrocuebanemp=$this->io_funciones->uf_trim(str_replace("-","",$ls_nrocuebanemp));
			$ls_nrocuebanemp=$this->io_funciones->uf_cerosizquierda($ls_nrocuebanemp,20);
			//$ls_codnom=$aa_ds_banco->data["codnom"][1];	
			//$ls_numconvenio=$rs_data->fields["codnom"];	
			$ls_numconvenio=str_pad($ls_numconvenio,10,"0","left");
			$ls_cabecera='ND0'.$ls_nrocuebanemp.$adec_montot.$ls_rif1.$ls_rif2."\r\n";
			
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
				$this->io_mensajes->message("Error al abrir el archivo  ".$ls_nombrearchivo);
				$lb_valido=false;
			}		
			/// PARA EL DETALLE DEL ARCHIVO
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_tipreg='NC0';
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				//$ls_codcueban=substr($ls_codcueban,10,20);
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedula=$ls_nacper.$ls_cedper;
				$ls_cedula=str_pad($ls_cedula,15," ");
				$ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
				$ldec_neto=number_format($ldec_neto*100,0,"","");  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,13);
				$ls_cadena=$ls_tipreg.$ls_codcueban.$ldec_neto.$ls_cedula."\r\n";
				
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro
	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_mintra_txt($as_ruta,$rs_data)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_mintra_txt
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codmetban // C�digo del Metodo
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina
		//	   Creado Por: Ing. Carlos Zambrano
		// Fecha Creacion: 12/06/2008 								
		// Modificado Por: 											Fecha ultima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina.txt";
		$li_count=$rs_data->RecordCount();
		if(($li_count>0))
		{
			//Chequea si existe el archivo.
			if (file_exists("$ls_nombrearchivo"))
			{
				if(@unlink("$ls_nombrearchivo")===false)//Borrar el archivo de texto existente para crearlo nuevo.
				{
					$lb_valido = false;
				}
				else
				{
					$ls_creararchivo = @fopen("$ls_nombrearchivo","a+");
				}
			}
			else
			{
				$ls_creararchivo = @fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			}
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_nombre=$rs_data->fields["nomper"];
				$ls_apellido=$rs_data->fields["apeper"];
				$pos1 =strpos(rtrim($ls_nombre)," ",0);
				$ls_nomper1=substr(rtrim($ls_nombre),0,$pos1);  //Nombre de Persona 1
				$ls_nomper2=substr(ltrim(rtrim($ls_nombre)),$pos1+1,60);  //Nombre de Persona 2
				if($pos1==0)
				{
					$ls_nomper1=$ls_nombre;
					$ls_nomper2="";
				}
				$pos2 =strpos(rtrim($ls_apellido),' ',0);
				$ls_apeper1=substr(rtrim($ls_apellido),0,$pos2);  //Apellido de Persona 1
				$ls_apeper2=substr(ltrim(rtrim($ls_apellido)),$pos2+1,60);  //Apellido de Persona 2
				if($pos2==0)
				{
					$ls_apeper1=$ls_apellido;
					$ls_apeper2="";
				}
				$ls_nacionalidad=$rs_data->fields["nacper"];
				if ($ls_nacionalidad=='V')
				{
					$ls_nacionalidad=1;
				}
				else
				{
					$ls_nacionalidad=2;
				}
				$ls_cedula=$rs_data->fields["cedper"];
				$ls_sexo=$rs_data->fields["sexper"];
				if ($ls_sexo=='M')
				{
					$ls_sexo=1;
				}
				else
				{
					$ls_sexo=2;
				}
				$ls_fechanac=$rs_data->fields["fecnacper"];
				$li_dianac=substr($ls_fechanac,8,2);
		        $li_mesnac=substr($ls_fechanac,5,2);
		        $li_anonac=substr($ls_fechanac,0,4);
				$ls_fechanac=$li_dianac.$li_mesnac.$li_anonac;
				$ls_descar=rtrim($rs_data->fields["descar"]);
				if($rs_data->fields["racnom"]==1)
				{
					$ls_descar=rtrim($rs_data->fields["denasicar"]);
				}
				$ls_tiptrabajador=$rs_data->fields["tipnom"];
				if (($ls_tiptrabajador=='3') || ($ls_tiptrabajador=='4'))
				{
					$ls_tiptrabajador=2;
				}
				else
				{
					$ls_tiptrabajador=1;
				}
				$ls_fechaing=$rs_data->fields["fecingper"];
				$li_diaing=substr($ls_fechaing,8,2);
		        $li_mesing=substr($ls_fechaing,5,2);
		        $li_anoing=substr($ls_fechaing,0,4);
				$ls_fechaing=$li_diaing.$li_mesing.$li_anoing;
				$ls_estatus=$rs_data->fields["estper"];
				if($ls_estatus!=3)
				{
					$ls_estatus=1;
				}
				else
				{
					$ls_estatus=2;
				}
				$ls_sueldo=$rs_data->fields["sueintper"];
				$li_possuel =strpos(rtrim($ls_sueldo),".",0);
				if($li_possuel > 0)
				{
					$ls_sueldoesp=number_format($ls_sueldo,2,".","");
					$ls_sueldoesp=str_replace(".","",$ls_sueldoesp);
				}
				else
				{
					$ls_sueldoesp=$ls_sueldo."00";
				}
				$ls_cadena=$ls_nomper1.";".$ls_nomper2.";".$ls_apeper1.";".$ls_apeper2.";".$ls_nacionalidad.";".$ls_cedula.";".$ls_sexo.";".$ls_fechanac.";".$ls_descar.";".$ls_tiptrabajador.";".$ls_fechaing.";".$ls_estatus.";".$ls_sueldoesp."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_mintra_txt
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro_excel($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro_excel
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco del tesoro en excel para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 11/11/2011 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 08/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		require_once ("../base/librerias/php/writeexcel/class.writeexcel_workbook.inc.php");
		require_once ("../base/librerias/php/writeexcel/class.writeexcel_worksheet.inc.php");	
		$lb_valido=true;
		$as_ruta1="txt/general";
		$ls_origen=$as_ruta1."/tesoro.xls";
		$ls_destino=$as_ruta."/tesoro_nomina.xls";
		copy($ls_origen,$ls_destino);
		chmod($ls_destino,0777);
		$fname = fopen ($ls_destino,"r+");
		$workbook = new writeexcel_workbook($fname);
		$worksheet = &$workbook->addworksheet();
		$li_count=$rs_data->RecordCount();
		$lo_titulo= &$workbook->addformat();
		$lo_titulo->set_bold();
		$lo_titulo->set_font("Verdana");
		$lo_titulo->set_align('center');
		$lo_titulo->set_size('9');
		$lo_dataleft= &$workbook->addformat(array("num_format"=> "#"));
		$lo_dataleft->set_text_wrap();
		$lo_dataleft->set_font("Verdana");
		$lo_dataleft->set_align('left');
		$lo_dataleft->set_size('9');
		
		$lo_especial= &$workbook->addformat(array("num_format"=> "###"));
		$lo_especial->set_text_wrap();
		$lo_especial->set_font("Verdana");
		$lo_especial->set_align('left');
		$lo_especial->set_size('9');
		/*$lo_especial= &$workbook->addformat();
		$lo_especial->set_text_wrap();
		$lo_especial->set_font("Verdana");
		$lo_especial->set_align('left');
		$lo_especial->set_size('9');*/
		
		$lo_dataright= &$workbook->addformat(array("num_format"=> "#,##0.00"));
		$lo_dataright->set_font("Verdana");
		$lo_dataright->set_align('right');
		$lo_dataright->set_size('9');
		$worksheet->set_column(0,0,10);
		$worksheet->set_column(1,1,40);
		$worksheet->set_column(2,2,20);
		$worksheet->set_column(3,3,30);
		$worksheet->set_column(4,4,20);
		if($li_count>0)
		{	
			$li_fila=1;
			$li_i=0;
			$li_total=0;
			$worksheet->write($li_i,0,"NRO",$lo_titulo);
			$worksheet->write($li_i,1,"APELLIDOS Y NOMBRE",$lo_titulo);
			$worksheet->write($li_i,2,"CEDULA",$lo_titulo);
			$worksheet->write($li_i,3,"NRO DE CUENTA",$lo_titulo);
			$worksheet->write($li_i,4,"MONTO A PAGAR",$lo_titulo);
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_cedper=trim($rs_data->fields["nacper"]).$this->io_funciones->uf_cerosizquierda($this->io_funciones->uf_trim($rs_data->fields["cedper"]),9);
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ldec_montot=number_format($rs_data->fields["monnetres"],2,",","");

				$worksheet->write($li_fila,0,$li_fila,$lo_datacenter);
				$worksheet->write($li_fila,1," ".$ls_apeper." ".$ls_nomper,$lo_dataleft);
				$worksheet->write($li_fila,2,$ls_cedper,$lo_dataleft);
				$worksheet->write($li_fila,3," ".$ls_codcueban,$lo_especial);
				$worksheet->write($li_fila,4,$ldec_montot,$lo_dataright);
				
				$li_fila=$li_fila+1;
				$rs_data->MoveNext();

			}
			if ($lb_valido)
			{
				$this->io_mensajes->message("El archivo ".$ls_destino." fue creado.");
				$workbook->close();
			}
			else
			{
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
				unset($worksheet);
				unset($workbook);
				unset($fname);
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro_excel
	//-----------------------------------------------------------------------------------------------------------------------------------
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_provincial_BBVAcash_2($as_ruta,$rs_data)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_provincial_BBVAcash
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//	  Description: genera el archivo txt a disco para  el banco Provincial para pago de nomina
		//	   Creado Por: Ing. María Beatriz Unda
		// Fecha Creacion: 19/02/2009								
		// Modificado Por: 										Fecha ultima Modificacion :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/BBVACASH2.txt";
		$li_count=$rs_data->RecordCount();
		if($li_count>0)
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
			    $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=str_pad($ls_codcueban,20,"0",0);
				$ls_nacper=trim($rs_data->fields["nacper"]);
				$ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
				$ls_cedper=$this->io_funciones->uf_rellenar_izq($ls_cedper,"0",8); 
				$ls_nomper=trim($rs_data->fields["nomper"]);
				$ls_apeper=trim($rs_data->fields["apeper"]);
				$ls_personal=substr($ls_apeper." ".$ls_nomper,0,30);
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_personal," ",30);
				$ldec_neto=($rs_data->fields["monnetres"]);
				
					
				
				$li_pos=strpos($ldec_neto,".");
				if ($li_pos!="")
				{
					$ldec_neto_dec=substr($ldec_neto,$li_pos+1,2);
					$ldec_neto_dec=$this->io_funciones->uf_rellenar_der($ldec_neto_dec,"0",2);
					$ldec_neto_mon=substr($ldec_neto,0,$li_pos);
					$ldec_neto_mon=$this->io_funciones->uf_rellenar_izq($ldec_neto_mon,"0",13);
				}
				else
				{
					$ldec_neto_dec="";
					$ldec_neto_dec=$this->io_funciones->uf_rellenar_der($ldec_neto_dec,"0",2);
					$ldec_neto_mon=$ldec_neto;
					$ldec_neto_mon=$this->io_funciones->uf_rellenar_izq($ldec_neto_mon,"0",13);
				}
				
				$li_pos_nom=strpos($ls_nomper," ");
				if ($li_pos_nom!="")
				{
					$ls_prinom=substr($ls_nomper,0,$li_pos_nom);
				}
				else
				{
					$ls_prinom=$ls_nomper;
				}
				
				$li_pos_ape=strpos($ls_apeper," ");
				if ($li_pos_ape!="")
				{
					$ls_priape=substr($ls_apeper,0,$li_pos_ape);
				}
				else
				{
					$ls_priape=$ls_apeper;
				}
				$ls_relleno=str_pad(' ',6," ",0);
				$ls_cadena=$ls_nacper.$ls_cedper.$ls_codcueban.$ldec_neto_mon.$ldec_neto_dec.$ls_prinom." ".$ls_priape."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_provincial_BBVAcash_2


	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_metodo_banco_deltesoro_2014($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
	{ 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_deltesoro_2014
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco  
		//	    		   ad_fecproc // Fecha de procesamiento
		//	    		   as_codmetban // C�digo del Metodo
		//	  Description: genera el archivo txt a disco para  el banco idel Tesoro para pago de nomina
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creacion: 12/06/2008 								
		// Modificado Por: 											Fecha ultima Modificacion : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/nomina_tesoro.txt";
		$li_count=$rs_data->RecordCount();
		if(($li_count>0)&&($lb_valido))
		{
			$ls_creararchivo=@fopen("$ls_nombrearchivo","a+"); //creamos y abrimos el archivo para escritura
			/// PARA EL DETALLE DEL ARCHIVO
			while((!$rs_data->EOF)&&($lb_valido))
			{
				$ls_codcueban=$rs_data->fields["codcueban"];
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_cerosizquierda($ls_codcueban,20);
				$ls_nacper=$rs_data->fields["nacper"];
				$ls_cedper=$rs_data->fields["cedper"];
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedula=$ls_nacper.$ls_cedper;
				$ldec_neto=number_format($rs_data->fields["monnetres"],2,".","");  
				$ldec_neto=number_format($ldec_neto*100,0,"","");  
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				$ls_cadena=$ls_codcueban.$ls_cedula.$ldec_neto."\r\n";
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
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
			}
			else
			{
				@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
				$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
			}	
		}
		else
		{
			$this->io_mensajes->message("No hay datos que generar.");
			$lb_valido=false;
		}
		return $lb_valido;
	}// end function uf_metodo_banco_deltesoro_2014
	//-----------------------------------------------------------------------------------------------------------------------------------

    //-----------------------------------------------------------------------------------------------------------------------------------
    function uf_metodo_banco_banfanb($as_ruta,$rs_data,$ad_fecproc,$as_codmetban,$adec_montot,$as_codcueban)
    { 
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //         Function: uf_metodo_banco_banfanb
        //           Access: public 
        //        Arguments: aa_ds_banco // arreglo (datastore) datos banco  
        //                 ad_fecproc   // fecha de procesamiento
        //                 as_codcuenta   // Cï¿½digo de cuenta a debitar
        //                 adec_montot   // Monto total a depositar
        //      Description: genera el archivo txt a disco para  el banco Venezuela para pago de nomina
        //       Creado Por: Ing. Marï¿½a Roa
        // Fecha Creaciï¿½n: 01/01/2006                                 
        // Modificado Por: Ing. Yesenia Moreno                        Fecha ï¿½ltima Modificaciï¿½n : 08/05/2006
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $li_total_deposito=0;
        $li_total_personal=0;
        $lb_valido=true;
        $ldec_monpre=0;
        $ldec_monacu=0;
        $ls_nombrearchivo=$as_ruta."/banfanb_nomina.txt";
		$arrResultado=$this->io_metbanco->uf_load_metodobanco_nomina($as_codmetban,"0",$ls_codempnom,$ls_codofinom,$ls_tipcuecre,$ls_tipcuedeb,$ls_numconvenio);
		$ls_codempnom=$arrResultado['as_codempnom'];
		$ls_codofinom=$arrResultado['as_codofinom'];
		$ls_tipcuecre=$arrResultado['as_tipcuecrenom'];
		$ls_tipcuedeb=$arrResultado['as_tipcuedebnom'];
		$ls_numconvenio=$arrResultado['as_numconnom'];
		$lb_valido=$arrResultado['lb_valido'];		
		$ls_codempnom=substr($ls_codempnom,0,4);
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
			$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			$ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
			$li_dia=substr($ad_fecproc,0,2);
			$li_mes=substr($ad_fecproc,3,2);
			$li_ano=substr($ad_fecproc,6,4);
			$ldec_totdep=number_format($adec_montot*100,0,"","");
			$ldec_totdep=$this->io_funciones->uf_cerosizquierda($ldec_totdep,14);
			$li_count=$this->io_funciones->uf_cerosizquierda($li_count,4);
			//$ls_cadena=$ls_codcueban.$li_ano.$li_mes.$li_dia.$ldec_totdep.$li_count."\r\n";
			//$ls_cadena=$ls_codcueban.$li_ano.$li_mes.$li_dia.$ldec_totdep.$li_count."\r\n";
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
            while((!$rs_data->EOF)&&($lb_valido))
            {
                $ldec_neto=$rs_data->fields["monnetres"];  
                $ldec_neto=number_format($ldec_neto*100,0,"","");
                $ldec_neto=str_pad($ldec_neto,14,"0",0);
                $ls_codcueban=$rs_data->fields["codcueban"];
 				$ls_nacper=$rs_data->fields["nacper"];
                $ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
                $ls_codcueban=$this->io_funciones->uf_cerosizquierda(substr($ls_codcueban,0,20),20);
                $ls_cedper=$this->io_funciones->uf_trim($rs_data->fields["cedper"]);
                $ls_cedper=$this->io_funciones->uf_cerosizquierda(substr($ls_cedper,0,10),10);
               // $ls_cadena=$ls_codempnom.$ldec_neto.$ls_codcueban.$ls_cedper."00000000"."\r\n";
                $ls_cadena=$ls_nacper.$ldec_neto.$ls_cedper."\r\n";
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
    }// end function uf_metodo_banco_banfanb
    //-----------------------------------------------------------------------------------------------------------------------------------



function uf_tipocuentabancaria($as_codban,$as_codcueban)
 { 
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que retorna el tipo de cuenta asociada 
	//  al banco correspondiente.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////

	$ls_tipcta = "";
	$ls_sql="SELECT codtipcta
	           FROM scb_ctabanco
			  WHERE codemp='".$this->ls_codemp."' 	 
			    AND codban= '".$as_codban."'
				AND ctaban= '".$as_codcueban."'" ;
	$rs_data=$this->io_sql->select($ls_sql);				  
	if ($rs_data===false)	
	   {
	     $this->io_mensajes->message("CLASE->MÉTODO BANCO->uf_tipocuentabancaria ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  $ls_tipcta = $row["codtipcta"];
		    }
	   }
    return $ls_tipcta;
 }
//-----------------------------------------------------------------------------------------------------------------------------------
function uf_nombrebanco_beneficiario($as_codban)
 { 
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que retorna el tipo de cuenta asociada 
	//  al banco correspondiente.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////

	$ls_nombre = "";
	$ls_sql="SELECT TRIM(nomban) as nomban
	           FROM scb_banco
			  WHERE codemp='".$this->ls_codemp."' 	 
			    AND codban= '".$as_codban."'" ;
	$rs_data=$this->io_sql->select($ls_sql);				  
	if ($rs_data===false)	
	   {
	     $this->io_mensajes->message("CLASE->MÉTODO BANCO->uf_nombrebanco_beneficiario ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  $ls_nombre = $row["nomban"];
		    }
	   }
    return $ls_nombre;
 }
//----------------------------------------------------------------------------------------------------------------------------------- 
function uf_nombre_empresa($as_nommet)
 { 
	////////////////////////////////////////////////////////////////////////////////////////////////
	//
	// -Funcion que retorna el tipo de cuenta asociada 
	//  al banco correspondiente.
	//
	///////////////////////////////////////////////////////////////////////////////////////////////

	$ls_nombre = "";
	$ls_sql="SELECT TRIM(codempnom) as codigo
	           FROM sno_metodobanco
			  WHERE codemp='".$this->ls_codemp."' 	 
			    AND desmet= '".$as_nommet."'" ;
	$rs_data=$this->io_sql->select($ls_sql);				  
	if ($rs_data===false)	
	   {
	     $this->io_mensajes->message("CLASE->MÉTODO BANCO->uf_nombre_empresa ERROR->".$this->io_funciones->uf_convertirmsg($this->io_sql->message));
		 $lb_valido=false;
	   }
	else
	   {
		 if ($row=$this->io_sql->fetch_row($rs_data))
		    {
			  $ls_nombre = $row["codigo"];
		    }
	   }
    return $ls_nombre;
 }
 
	//-----------------------------------------------------------------------------------------------------------------------------------	
	function uf_metodo_banco_banesco_paymul_old($as_ruta,$rs_data,$ad_fecproc,$adec_montot,$as_codcueban,$as_ref)
	{  
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_metodo_banco_banesco_paymul
		//		   Access: public 
		//	    Arguments: aa_ds_banco // arreglo (datastore) datos banco 
		//                 ad_fecproc // fecha de procesamiento
		//                 adec_montot // Monto total
		//                 as_codcueban // c�digo de la cuenta bancaria a debitar 
		//	  Description: genera el archivo txt a disco para  el banco Banesco Paymul para pago de nomina
		//	   Creado Por: Ing. 
		// Fecha Creacion: 01/01/2006 								
		// Modificado Por: Ing. Yesenia Moreno						Fecha ultima Modificacion : 04/05/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_nombrearchivo=$as_ruta."/b_paymul.txt";
		$ls_numope=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANESCO_PAYMUL_OP","1","I");
		$ls_numope=intval($this->io_funciones->uf_trim($ls_numope), 10);
		$lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANESCO_PAYMUL_OP",$ls_numope+1,"I");
		if($lb_valido)
		{		
			$ls_numref=$this->io_sno->uf_select_config("SNO","GEN_DISK","BANESCO_PAYMUL_REF","1","I");
			$ls_numref=intval($this->io_funciones->uf_trim($ls_numref),10);
			if ($as_ref==1)
			{
				$lb_valido=$this->io_sno->uf_insert_config("SNO","GEN_DISK","BANESCO_PAYMUL_REF",$ls_numref+1,"I");
			}
		}
		if($lb_valido)
		{		
			$ls_numref=$this->io_funciones->uf_cerosizquierda($ls_numref,8);					 
			$ls_numope=substr($ls_numope,0,9); 
			$ls_numope=str_pad($ls_numope,11,"0",1);
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
		}
		if($lb_valido)
		{		
			// Registro de control (Datos Fijos)
			$ls_cadena="HDR"."BANESCO        "."E"."D  95B"."PAYMUL"."P"."\r\n";
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
			// Registro de encabezado
			$ad_fecproc=$this->io_funciones->uf_convertirdatetobd($ad_fecproc);
			$ad_fecproc=str_replace("-","",$ad_fecproc);
			$ls_cadena = "01".$this->io_funciones->uf_rellenar_der("SAL"," ",35).$this->io_funciones->uf_rellenar_der("9"," ",3).$this->io_funciones->uf_rellenar_der($ls_numope," ",35).$this->io_funciones->uf_cerosderecha($ad_fecproc,14)."\r\n";
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
			// Registro de debito
			$ls_rifempresa=str_replace("-","",$_SESSION["la_empresa"]["rifemp"]);
			$ls_rif=$this->io_funciones->uf_rellenar_der($ls_rifempresa," ",17);
			$ldec_montot=$adec_montot;           
			$ldec_montot=($ldec_montot*100);  
			$ldec_montot=$this->io_funciones->uf_cerosizquierda($ldec_montot,15);
			$as_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$as_codcueban));
			$as_codcueban=$this->io_funciones->uf_rellenar_der(substr(trim($as_codcueban),0,34), " ", 34);
			$ls_nomemp=$_SESSION["la_empresa"]["nombre"];
			$ls_nomemp=$this->io_funciones->uf_rellenar_der(substr(rtrim($ls_nomemp),0,35)," ",35);
			//$ls_banesco=$this->uf_nombre_empresa('BANESCO_PAYMUL');
			$ls_banesco=$this->io_funciones->uf_rellenar_der("BANESCO"," ",11);
			$li_nrodebitos=1;
			$ls_numref=$this->io_funciones->uf_rellenar_der($ls_numref," ",30);
			$ls_cadena="02".$ls_numref.$ls_rif.$ls_nomemp.$ldec_montot."VES"." ".$as_codcueban.$ls_banesco.$ad_fecproc."\r\n";
			if ($ls_creararchivo)
			{
				if (@fwrite($ls_creararchivo, $ls_cadena)===FALSE)//Escritura
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
			//Registro de credito
			$li_nrocreditos=0;
			$li_numrecibo=0;
			$li_count=$rs_data->RecordCount();//$aa_ds_banco->getRowCount("codcueban");
			//for($li_i=1;(($li_i<=$li_count)&&($lb_valido));$li_i++)
		        while((!$rs_data->EOF)&&($lb_valido))
			{
				$li_numrecibo=$li_numrecibo+1;
				$ls_codcueban=$rs_data->fields["codcueban"]; //Numero de cuenta del empleado
				$ls_codcueban=$this->io_funciones->uf_trim(str_replace("-","",$ls_codcueban));
				$ls_codcueban=$this->io_funciones->uf_rellenar_der(substr(trim($ls_codcueban),0,30)," ",30);
				$li_numrecibo=$this->io_funciones->uf_cerosizquierda($li_numrecibo,8);
				$li_numrecibo=$this->io_funciones->uf_rellenar_der($li_numrecibo," ", 30);
				$ldec_neto=$rs_data->fields["monnetres"];  //debo verificar si en el ds ya viene modificado la coma decimal
				$ldec_neto=number_format($ldec_neto,2,".","");
				$ldec_neto=number_format($ldec_neto*100,0,"","");
				$ldec_neto=$this->io_funciones->uf_cerosizquierda($ldec_neto,15);
				$ls_nacper=$rs_data->fields["nacper"];   //Nacionalidad
				$ls_cedper=$rs_data->fields["cedper"];   //cedula del personal
				$ls_cedper=$this->io_funciones->uf_cerosizquierda($ls_cedper,9);
				$ls_cedper=$this->io_funciones->uf_rellenar_der($ls_nacper.$ls_cedper," ",17);
				$ls_apeper=$rs_data->fields["apeper"];
				$ls_nomper=$rs_data->fields["nomper"];
				$ls_personal=$this->io_funciones->uf_rellenar_der($ls_apeper." ".$ls_nomper, " ", 70);
				//$ls_const=$this->io_funciones->uf_rellenar_der("BANSVECA", " ", 11);
				$ls_const=$this->uf_nombre_empresa('BANESCO_PAYMUL');
				$ls_const=$this->io_funciones->uf_rellenar_der($ls_const, " ", 11);
				$ls_space= $this->io_funciones->uf_rellenar_der(""," ",3); // (3)
				$ls_spacedir=$this->io_funciones->uf_rellenar_der(""," ",70);  //direccion (70)
				$ls_spacetel=$this->io_funciones->uf_rellenar_der(""," ",25);              //telefono (25)
				$ls_spacecicon=$this->io_funciones->uf_rellenar_der(""," ",17);                    //C.I. persona contacto  (17)
				$ls_spacenomcon=$this->io_funciones->uf_rellenar_der(""," ",35);  //Nombre persona contacto (35)
				$ls_spaceficha=$this->io_funciones->uf_rellenar_der(""," ",30);       //Ficha del personal (30)
				$ls_spaceubic=$this->io_funciones->uf_rellenar_der(""," ",21);                //Ubicacion Geografica (21)
				$li_nrocreditos=$li_nrocreditos + 1;
				$ls_cadena="03".$li_numrecibo.$ldec_neto."VES".$ls_codcueban.$ls_const.$ls_space.$ls_cedper.$ls_personal.
							$ls_spacedir.$ls_spacetel.$ls_spacecicon.$ls_spacenomcon." ".$ls_spaceficha."  ".$ls_spaceubic."42 "."\r\n";
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
			//Registro de totales
			$li_nrodebitos=$this->io_funciones->uf_cerosizquierda($li_nrodebitos,15);
			$li_nrocreditos=$this->io_funciones->uf_cerosizquierda($li_nrocreditos,15);
			$ls_cadena="06".$li_nrodebitos.$li_nrocreditos.$ldec_montot."\r\n";
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
			@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
			$this->io_mensajes->message("El archivo ".$ls_nombrearchivo." fue creado.");
		}
		else
		{
			@fclose($ls_creararchivo); //cerramos la conexion y liberamos la memoria
			$this->io_mensajes->message("Ocurrio un error al generar el archivo por favor verifique el diskette.");
		}	
		return $lb_valido;		
    }// end function uf_metodo_banco_banesco_paymul
	//-----------------------------------------------------------------------------------------------------------------------------------
 
 
}
?>


