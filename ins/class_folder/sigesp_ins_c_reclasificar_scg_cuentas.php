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

class sigesp_ins_c_reclasificar_scg_cuentas
{
	var $io_sql;
	var $io_message;
	var $io_function;
	var $is_msg_error;
	var $ls_codemp;

	public function __construct()
	{
		require_once("../base/librerias/php/general/sigesp_lib_sql.php");
		require_once("../base/librerias/php/general/sigesp_lib_include.php");
		require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
		require_once("../base/librerias/php/general/sigesp_lib_fecha.php");
		require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");	
		require_once("../shared/class_folder/sigesp_c_seguridad.php");
		$io_siginc=new sigesp_include();
		$con=$io_siginc->uf_conectar();
		$this->io_sql=new class_sql($con);
		$this->io_message=new class_mensajes();
		$this->io_function=new class_funciones();
		$this->io_seguridad=new sigesp_c_seguridad();
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
	}// end function sigesp_ins_c_reclasificar_scg_cuentas
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_upload($as_nombre,$as_tipo,$as_tamano,$as_nombretemporal)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_upload
		//		   Access: public (sigesp_snorh_d_constanciatrabajo)
		//	    Arguments: as_nombre  // Nombre 
		//				   as_tipo  // Tipo 
		//				   as_tamano  // Tamaño 
		//				   as_nombretemporal  // Nombre Temporal
		//	      Returns: as_nombre sale vacio si da un error y con el mismo valor si se subio correctamente
		//	  Description: Funcion que sube un archivo al servidor
		//	   Creado Por: Ing. Yesenia Moreno
		// Fecha Creación: 12/06/2007 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$valido=true;
		if($as_nombre!="")
		{
			if (!((strpos($as_tipo, "excel")) && ($as_tamano < 1000000))) 
			{ 
				$valido=false;
				$this->io_message->message("El archivo no es válido, es muy grande o no es de Extención xls.");
			}
			else
			{ 
				if (!((move_uploaded_file($as_nombretemporal, "reclasificar/scg/".$as_nombre.".xls"))))
				{
					$valido=false;
		        	$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_upload ERROR-> No tiene Permiso para copiar en la carpeta Contacte con el administrador del sistema."); 
				}
				else
				{
					@chmod("reclasificar/scg/".$as_nombre.".xls",0755);
				}
			}
		}
		return $valido;	
    }
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------------
	function uf_reclasificar_scg_cuentas($as_nombre)
	{
		$valido=true;
		$directorio = "";
		$directorio = dirname(__FILE__);
		$directorio = str_replace("\\","/",$directorio); 
		$directorio = str_replace("/ins/class_folder","",$directorio);
		
		$nombrearchivo = $directorio.'/ins/reclasificar/scg/'.$as_nombre.'.xls';
		if (file_exists("$nombrearchivo"))
		{
			require_once("../base/librerias/php/readexcel/reader.php");
			$excel = new Spreadsheet_Excel_Reader();
			$archivo=@file("$nombrearchivo");
			$excel->setOutputEncoding("CP1251");
			$excel->read($nombrearchivo);
			$contador=0;
			for($li_indexfil=$excel->sheets[0]['numRows'];($li_indexfil>=2);$li_indexfil--)
			{
				$contador++;
				$sccuentaorigen=trim($excel->sheets[0]['cells'][$li_indexfil][1]);
				$sccuentadestino=trim($excel->sheets[0]['cells'][$li_indexfil][2]);
				
				
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_empresa ".
								" SET c_resultad='".$sccuentadestino."' ".
								" WHERE c_resultad='".$sccuentaorigen."' ";					
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_empresa ".
								" SET c_resultan='".$sccuentadestino."' ".
								" WHERE c_resultan='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_empresa ".
								" SET scctaben='".$sccuentadestino."' ".
								" WHERE scctaben='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_empresa ".
								" SET c_financiera='".$sccuentadestino."' ".
								" WHERE c_financiera='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_empresa ".
								" SET c_fiscal='".$sccuentadestino."' ".
								" WHERE c_fiscal='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE rpc_beneficiario ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE rpc_beneficiario ".
								" SET sc_cuentarecdoc='".$sccuentadestino."' ".
								" WHERE sc_cuentarecdoc='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}								
				if ($valido)					
				{
					$consulta = " UPDATE rpc_proveedor ".
								" SET sc_cuentarecdoc='".$sccuentadestino."' ".
								" WHERE sc_cuentarecdoc='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE rpc_proveedor ".
								" SET sc_ctaant='".$sccuentadestino."' ".
								" WHERE sc_ctaant='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE rpc_proveedor ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE saf_activo ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE saf_contable ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE saf_depreciacion_int ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_deducciones ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sigesp_deducciones ".
								" SET sc_ctaasu='".$sccuentadestino."' ".
								" WHERE sc_ctaasu='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE siv_articulo ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE siv_articulo ".
								" SET sc_cuentainv='".$sccuentadestino."' ".
								" WHERE sc_cuentainv='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE siv_tipoarticulo ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE siv_dt_transferencia_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE siv_dt_scg_int ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE siv_dt_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE siv_dt_produccion_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE siv_dt_empaquetado_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE siv_almacen ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}								
				if ($valido)					
				{
					$consulta = " UPDATE scb_caja ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE scb_ctabanco ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE scb_colocacion ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE scb_colocacion ".
								" SET sc_cuentacob='".$sccuentadestino."' ".
								" WHERE sc_cuentacob='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE scb_movbco_anticipo ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scb_movbco_scg ".
								" SET scg_cuenta='".$sccuentadestino."' ".
								" WHERE scg_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scb_movcol_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_saldos_consolida ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_saldos ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_pc_reporte_ant ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_pc_reporte ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_oa_reporte ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_dtmp_cmp ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_dt_cmp ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_cuentas_consolida ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_confvariacion ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scg_casa_presu ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}								
				if ($valido)					
				{
					$consulta = " UPDATE sep_solicitudcargos ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE scv_dt_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				

				if ($valido)					
				{
					$consulta = " UPDATE sno_beneficiario ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_nomina ".
								" SET cueconnom='".$sccuentadestino."' ".
								" WHERE cueconnom='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_hnomina ".
								" SET cueconnom='".$sccuentadestino."' ".
								" WHERE cueconnom='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_personalnomina ".
								" SET cueaboper='".$sccuentadestino."' ".
								" WHERE cueaboper='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_hpersonalnomina ".
								" SET cueaboper='".$sccuentadestino."' ".
								" WHERE cueaboper='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_concepto ".
								" SET cueconcon='".$sccuentadestino."' ".
								" WHERE cueconcon='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_hconcepto ".
								" SET cueconcon='".$sccuentadestino."' ".
								" WHERE cueconcon='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_concepto ".
								" SET cueconpatcon='".$sccuentadestino."' ".
								" WHERE cueconpatcon='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}
				if ($valido)					
				{
					$consulta = " UPDATE sno_hconcepto ".
								" SET cueconpatcon='".$sccuentadestino."' ".
								" WHERE cueconpatcon='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE cxp_clasificador_rd ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE cxp_dc_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE cxp_rd_deducciones ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE cxp_rd_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE cxp_scg_inter ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE cxp_solicitudes_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE sno_dt_scg ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE sno_dt_scg_int ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE sno_fideicomiso ".
								" SET scg_cuentafid='".$sccuentadestino."' ".
								" WHERE scg_cuentafid='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE sno_fideicomiso ".
								" SET scg_cuentaintfid='".$sccuentadestino."' ".
								" WHERE scg_cuentaintfid='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE sno_rd ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE sob_anticipo ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE soc_solicitudcargos ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE spg_cuentas ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}		
				if ($valido)					
				{
					$consulta = " UPDATE spg_cuentas ".
								" SET sc_cuenta_art='".$sccuentadestino."' ".
								" WHERE sc_cuenta_art='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE spg_cuentas ".
								" SET scgctaint='".$sccuentadestino."' ".
								" WHERE scgctaint='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE spg_ep1 ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE spg_plantillareporte ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE spi_cuentas ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}				
				if ($valido)					
				{
					$consulta = " UPDATE spi_plantillacuentareporte ".
								" SET sc_cuenta='".$sccuentadestino."' ".
								" WHERE sc_cuenta='".$sccuentaorigen."' ";
					$rs_data=$this->io_sql->select($consulta);
					if($rs_data===false)
					{
						$valido=false;
						$this->io_message->message("CLASE->Reclasificar Cuentas SCG MÉTODO->uf_reclasificar_scg_cuentas ERROR->Error al actualizar."); 
					}
				}								
			}
		}
		return $valido;	
    }
	//-----------------------------------------------------------------------------------------------------------------------------------
}
?>