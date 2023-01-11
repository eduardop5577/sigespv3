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

require_once("../base/librerias/php/general/sigesp_lib_sql.php");
require_once("../base/librerias/php/general/sigesp_lib_datastore.php");
require_once("../base/librerias/php/general/sigesp_lib_mensajes.php");
require_once("../base/librerias/php/general/sigesp_lib_include.php");
require_once("../shared/class_folder/sigesp_c_seguridad.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones_db.php");
require_once("../base/librerias/php/general/sigesp_lib_funciones2.php");
require_once("sigesp_siv_c_movimientoinventario.php");

class sigesp_siv_c_despacho
{
	var $obj="";
	var $io_sql;
	var $siginc;
	var $con;
	var $ls_codemp;

	public function __construct()
	{
		$in=              new sigesp_include();
		$this->con=       $in->uf_conectar();
		$this->io_sql=    new class_sql($this->con);
		$this->seguridad= new sigesp_c_seguridad();
		$this->fun=       new class_funciones_db($this->con);
		$this->DS=        new class_datastore();
		$this->io_funcion=new class_funciones();
		$this->io_mov=    new sigesp_siv_c_movimientoinventario();
		$this->io_msg=    new class_mensajes();
		$this->ls_gestor=   $_SESSION["ls_gestor"];
		$this->ls_codemp=$_SESSION["la_empresa"]["codemp"];
		//$this->con->debug=true;
	}
	

	function uf_siv_obtener_dt_solicitud($as_codemp,$as_numsol,$ai_totrows,$ao_object,$as_estartpri="0")
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_solicitud
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numsol    // numero de la solicitud de ejecución presupuestaria
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description:	Funcion que busca los articulos asociados a una solicitud de ejecucion presupuestaria  en la tabla
		//                	de sep_dt_articulos, e igualmante busca las denominaciones  de los articulos en la tabla siv_articulo
		//				  	para luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006 								Fecha Última Modificación :08/02/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
 		$lb_valido=true;
		$li_selmay="";
		$li_seldet="";
		$ls_sql="SELECT sep_dt_articulos.*,".
				"      (SELECT trim(sc_cuenta)".
				"         FROM spg_cuentas".
				"        WHERE spg_cuentas.codemp=sep_dt_articulos.codemp".
				"          AND spg_cuentas.codestpro1=sep_dt_articulos.codestpro1".
				"          AND spg_cuentas.codestpro2=sep_dt_articulos.codestpro2".
				"          AND spg_cuentas.codestpro3=sep_dt_articulos.codestpro3".
				"          AND spg_cuentas.codestpro4=sep_dt_articulos.codestpro4".
				"          AND spg_cuentas.codestpro5=sep_dt_articulos.codestpro5".
				"          AND spg_cuentas.spg_cuenta=sep_dt_articulos.spg_cuenta) as sc_cuentasep".
				"  FROM sep_dt_articulos".
				" WHERE codemp='". $as_codemp ."'".
				"   AND numsol='". $as_numsol ."' AND canart<>0".
				" ORDER BY orden";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_obtener_dt_solicitud_I ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_codart=  $row["codart"];
				$li_cansol=  $row["canart"];
				$ls_unidad=  $row["unidad"];
				$ls_ctasep=  $row["sc_cuentasep"];
				$ls_sql= "SELECT siv_articulo.denart,siv_articulo.sc_cuenta,".
						 "       (SELECT unidad FROM siv_unidadmedida ".
						 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS unidad, ".
						 "       (SELECT denunimed FROM siv_unidadmedida ".
						 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS denunimed, ".
						 "       (SELECT tipart FROM siv_tipoarticulo ".
						 "         WHERE siv_articulo.codtipart=siv_tipoarticulo.codtipart) AS tipart ".
				         "  FROM siv_articulo".
						 " WHERE codemp='". $as_codemp ."'".
						 "   AND codart='".$ls_codart."'"; 
				$rs_data1=$this->io_sql->select($ls_sql);
				if($rs_data1===false)
				{
					$lb_valido=false;
					$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_obtener_dt_solicitud_II ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				}
				else
				{
					$ai_totrows=$ai_totrows+1;
					if($row=$this->io_sql->fetch_row($rs_data1))
					{
						$ls_denart= $row["denart"];
						$ls_denunimed= $row["denunimed"];
						$li_unidad= $row["unidad"];
						$ls_clasif=$row["tipart"];
						$ls_ctagas= trim($row["sc_cuenta"]);
						if($ls_unidad=="M")
						{
							$ls_unidad="Mayor";
							$li_canpendes=($li_cansol*$li_unidad);
							$li_selmay="selected";
							$li_seldet="";
						}
						else
						{
							$ls_unidad="Detal";
							$li_canpendes=$li_cansol;
							$li_seldet="selected";
							$li_selmay="";
						}
						if($as_estartpri==1)
						{
							$ls_href="";
						}
						else
						{
							$ls_href="<a href='javascript: ue_catalmacen(".$ai_totrows.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
						}
						if($li_numdecper!="3")
						{
							$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
							$li_cansol=number_format($li_cansol,2,",",".");
						}
						else
						{
							$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
							$li_cansol=number_format($li_cansol,3,",",".");
						}
						$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."     type=text     id=txtdenart".$ai_totrows."    class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
												   "<input name=txtcodart".$ai_totrows."     type=hidden   id=txtcodart".$ai_totrows."    class=sin-borde size=15 maxlength=15 value='".$ls_codart."' readonly>".
										 	       "<input name=txtcodartpri".$ai_totrows."  type=hidden   id=txtcodartpri".$ai_totrows." class=sin-borde size=15 maxlength=25  readonly>".
												   "<input name=txtctagas".$ai_totrows."     type=hidden   id=txtctagas".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_ctagas."' readonly>".
												   "<input name=txtctasep".$ai_totrows."     type=hidden   id=txtctasep".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_ctasep."' readonly>";
						$ao_object[$ai_totrows][2]="<input name=txtdenunimed".$ai_totrows."  type=text     id=txtdenunimed".$ai_totrows."    class=sin-borde size=13 maxlength=10 value='".$ls_denunimed."' readonly>";
						$ao_object[$ai_totrows][3]="<input name=txtcodalm".$ai_totrows."     type=text     id=txtcodalm".$ai_totrows."    class=sin-borde size=13 maxlength=10 readonly>".
												   $ls_href;
						$ao_object[$ai_totrows][4]="<input name=txtunidad".$ai_totrows."     type=text   id=txtunidad".$ai_totrows."    class=sin-borde  value='". $ls_unidad ."' readonly>".
												   "<input name=hidunidad".$ai_totrows."     type=hidden   id=hidunidad".$ai_totrows."    value='". $li_unidad ."'>";
						$ao_object[$ai_totrows][5]="<input name=txtcansol".$ai_totrows."     type=text     id=txtcansol".$ai_totrows."    class=sin-borde size=12 maxlength=12 value='".$li_cansol."' style='text-align:right' readonly>".
												   "<input name=hidexistencia".$ai_totrows." type=hidden   id=hidexistencia".$ai_totrows.">";
						$ao_object[$ai_totrows][6]="<input name=txtpenart".$ai_totrows."     type=text     id=txtpenart".$ai_totrows."    class=sin-borde size=12 maxlength=12 style='text-align:right' readonly>".
												   "<input name=txthidpenart".$ai_totrows."  type=hidden   id=txthidpenart".$ai_totrows." class=sin-borde size=12 value='".$li_canpendes."'>";
						$ao_object[$ai_totrows][7]="<input name=txtcanart".$ai_totrows."     type=text     id=txtcanart".$ai_totrows."    class=sin-borde size=12 maxlength=12 $ls_funcion  onBlur='javascript: ue_montosfactura(".$ai_totrows.");' style='text-align:right'>";
						$ao_object[$ai_totrows][8]="<input name=txtpreuniart".$ai_totrows."  type=text     id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 style='text-align:right' readonly><input name=hidnumdocori".$ai_totrows." type=hidden id=hidnumdocori".$ai_totrows.">";
						$ao_object[$ai_totrows][9]="<input name=txtmontotart".$ai_totrows."  type=text     id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 style='text-align:right' readonly>";
					    $ao_object[$ai_totrows][10]="<a href=javascript:uf_dt_activo(".$ai_totrows.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0><input name=hclasi".$ai_totrows." type=hidden id=hclasi".$ai_totrows." class=sin-borde size=15 maxlength=25 value='".$ls_clasif."' readonly></a></a>".			
   						                          " <input type=hidden name=hcodact".$ai_totrows."    id=hcodact".$ai_totrows." class=sin-borde size=15 maxlength=25  readonly>";

					}
				}
			}//while($row=$this->io_sql->fetch_row($li_exec))
		}
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$this->io_sql->free_result($rs_data);
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_obtener_dt_solicitud

	function uf_siv_obtener_dt_pendiente($as_codemp,$as_numsol,$ai_totrows,$ao_object,$as_estartpri)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_pendiente
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numsol    // numero de la solicitud de ejecución presupuestaria
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una solicitud de ejecucion presupuestaria  en la tabla
		//                 de sep_dt_articulos, e igualmante busca las denominaciones  de los articulos en la tabla siv_articulo
		//				   para luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 04/01/2007 								Fecha Última Modificación:
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
 		$lb_valido=true;
		$li_selmay="";
		$li_seldet="";
		$ls_sql="SELECT sep_dt_articulos.codart,MAX(sep_dt_articulos.unidad) AS unidad,MAX(sep_dt_articulos.canart) AS canart,siv_dt_despacho.numorddes,".
				" MAX(sep_dt_articulos.codestpro1) AS codestpro1 , MAX(sep_dt_articulos.codestpro2) AS codestpro2, ". 
				" MAX(sep_dt_articulos.codestpro3) AS codestpro3, MAX(sep_dt_articulos.codestpro4) AS codestpro4,".
				" MAX(sep_dt_articulos.codestpro5) AS codestpro5, MAX(sep_dt_articulos.estcla) AS estcla,".
				" MAX(sep_dt_articulos.spg_cuenta) AS spg_cuenta,".
				"      (SELECT canpenart".
				"         FROM siv_dt_despacho".
				"        WHERE siv_despacho.codemp=siv_dt_despacho.codemp".
				"          AND siv_despacho.numorddes=siv_dt_despacho.numorddes".
				"          AND sep_dt_articulos.codart=siv_dt_despacho.codart) AS canpenart".
				"  FROM sep_dt_articulos,siv_despacho,siv_dt_despacho".
				" WHERE sep_dt_articulos.codemp='". $as_codemp ."'".
				"   AND sep_dt_articulos.numsol='". $as_numsol ."'".
				"   AND sep_dt_articulos.canart<>0".	
				"   AND sep_dt_articulos.codemp=siv_despacho.codemp".
				"   AND sep_dt_articulos.numsol=siv_despacho.numsol".
				"   AND siv_despacho.estrevdes=1".
//				"   AND sep_dt_articulos.codart=siv_dt_despacho.codart".
				"   AND siv_despacho.codemp=siv_dt_despacho.codemp".
				"   AND siv_despacho.numorddes=siv_dt_despacho.numorddes".
				" GROUP BY siv_dt_despacho.numorddes,sep_dt_articulos.codart,sep_dt_articulos.codemp,sep_dt_articulos.numsol,".
				"          siv_despacho.codemp,siv_despacho.numorddes".
				" ORDER BY siv_dt_despacho.numorddes DESC";//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_obtener_dt_pendiente_I ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			$ls_numorddesaux="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_numorddes=$row["numorddes"];
				if(($ls_numorddesaux=="")||($ls_numorddes==$ls_numorddesaux))
				{
					$ls_numorddesaux=$ls_numorddes;
					$ls_codart=  $row["codart"]; 
					$li_despachados=$this->uf_siv_verificar_pendiente($as_codemp,$as_numsol,$ls_codart);
					$arrResultado=$this->uf_select_articulosrelacionados($as_codemp,$as_numsol,$ls_codart,$ai_adicionales);
					$ai_adicionales = $arrResultado['ai_adicionales'];
					$lb_valido = $arrResultado['lb_valido'];
					$li_despachados= $li_despachados + $ai_adicionales;
					$ls_cansol=  $row["canart"];
					$ls_unidad=  $row["unidad"];
					// Parametros para la ctasep
					$ls_codestpro1_sc=$row["codestpro1"];
					$ls_codestpro2_sc=$row["codestpro2"];
					$ls_codestpro3_sc=$row["codestpro3"];
					$ls_codestpro4_sc=$row["codestpro4"];
					$ls_codestpro5_sc=$row["codestpro5"];
					$ls_estcla_sc=$row["estcla"];
					$ls_spgcuenta_sc=$row["spg_cuenta"];
					// Fin Parametros para la ctasep
					//$ls_ctasep=  $row["sc_cuentasep"];
					$ls_ctasep=$this->uf_buscar_cuenta_sep($as_codemp,$ls_codestpro1_sc,$ls_codestpro2_sc,$ls_codestpro3_sc,$ls_codestpro4_sc,
														   $ls_codestpro5_sc,$ls_estcla_sc,$ls_spgcuenta_sc);
					
					$li_canpenart=$ls_cansol-$li_despachados;
					$ls_sql= "SELECT siv_articulo.*,".
							 "       (SELECT unidad FROM siv_unidadmedida ".
							 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS unidad,".
							 "       (SELECT denunimed FROM siv_unidadmedida ".
							 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS denunimed".
							 "  FROM siv_articulo".
							 " WHERE codemp='". $as_codemp ."'".
							 "   AND codart='". $ls_codart ."'";
					$rs_data1=$this->io_sql->select($ls_sql);
					if($rs_data1===false)
					{
						$lb_valido=false;
						$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_obtener_dt_pendiente_II ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
					}
					else
					{
						if($row=$this->io_sql->fetch_row($rs_data1))
						{
							$ls_denart= $row["denart"];
							$ls_denunimed= $row["denunimed"];
							$li_unidad= $row["unidad"];
							$ls_ctagas= $row["sc_cuenta"];
							$li_canpendes=$li_canpenart;
							if($ls_unidad=="M")
							{
								$ls_unidad="Mayor";
								$li_canpendes=($li_canpenart/$li_unidad);
								$li_selmay="selected";
							}
							else
							{

								$ls_unidad="Detal";
								$li_seldet="selected";
							}
							if($li_despachados==0)
							{	
								$li_canpendes=$ls_cansol;
								$li_canpenart=$li_canpendes;
								if($ls_unidad=="Mayor")
								{
									$li_canpenart=($li_canpendes*$li_unidad);
								}
							}

							if($li_canpendes>0)
							{
								$ai_totrows=$ai_totrows+1;
								if($as_estartpri==1)
								{
									$ls_href="";
								}
								else
								{
									$ls_href="<a href='javascript: ue_catalmacen(".$ai_totrows.");'><img src='../shared/imagebank/tools20/buscar.gif' alt='Codigo de Articulo' width='18' height='18' border='0'></a>";
								}
								if($li_numdecper!="3")
								{
									$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
									$ls_cansol=number_format($ls_cansol,2,",",".");
									$li_canpendes=number_format($li_canpendes,2,",",".");
								}
								else
								{
									$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
									$ls_cansol=number_format($ls_cansol,3,",",".");
									$li_canpendes=number_format($li_canpendes,3,",",".");
								}
	
								$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."     type=text     id=txtdenart".$ai_totrows."    class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
														   "<input name=txtcodart".$ai_totrows."     type=hidden   id=txtcodart".$ai_totrows."    class=sin-borde size=15 maxlength=15 value='".$ls_codart."' readonly>".
														   "<input name=txtcodartpri".$ai_totrows."  type=hidden   id=txtcodartpri".$ai_totrows." class=sin-borde size=15 maxlength=25  readonly>".
														   "<input name=txtctagas".$ai_totrows."     type=hidden   id=txtctagas".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_ctagas."' readonly>".
														   "<input name=txtctasep".$ai_totrows."     type=hidden   id=txtctasep".$ai_totrows."    class=sin-borde size=15 maxlength=50 value='".$ls_ctasep."' readonly>";
								$ao_object[$ai_totrows][2]="<input name=txtdenunimed".$ai_totrows."  type=text     id=txtdenunimed".$ai_totrows."    class=sin-borde size=13 maxlength=10 value='".$ls_denunimed."' readonly>";
								$ao_object[$ai_totrows][3]="<input name=txtcodalm".$ai_totrows."     type=text     id=txtcodalm".$ai_totrows."    class=sin-borde size=13 maxlength=10 readonly>".
														   $ls_href;
								$ao_object[$ai_totrows][4]="<input name=txtunidad".$ai_totrows."     type=text   id=txtunidad".$ai_totrows."   class=sin-borde value='". $ls_unidad ."' readonly>".
														   "<input name=hidunidad".$ai_totrows."     type=hidden   id=hidunidad".$ai_totrows."    value='". $li_unidad ."'>";
								$ao_object[$ai_totrows][5]="<input name=txtcansol".$ai_totrows."     type=text     id=txtcansol".$ai_totrows."    class=sin-borde size=12 maxlength=12 value='".$ls_cansol."' style='text-align:right' readonly>".
														   "<input name=hidexistencia".$ai_totrows." type=hidden   id=hidexistencia".$ai_totrows.">";
								$ao_object[$ai_totrows][6]="<input name=txtpenart".$ai_totrows."     type=text     id=txtpenart".$ai_totrows."    class=sin-borde size=12 maxlength=12 style='text-align:right' value='".$li_canpendes."' readonly>".
														   "<input name=txthidpenart".$ai_totrows."  type=hidden   id=txthidpenart".$ai_totrows."    class=sin-borde size=12 value='".$li_canpenart."'>";
								$ao_object[$ai_totrows][7]="<input name=txtcanart".$ai_totrows."     type=text     id=txtcanart".$ai_totrows."    class=sin-borde size=12 maxlength=12 $ls_funcion  onBlur='javascript: ue_montosfactura(".$ai_totrows.");' style='text-align:right'>";
								$ao_object[$ai_totrows][8]="<input name=txtpreuniart".$ai_totrows."  type=text     id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 style='text-align:right' readonly><input name=hidnumdocori".$ai_totrows." type=hidden id=hidnumdocori".$ai_totrows.">";
								$ao_object[$ai_totrows][9]="<input name=txtmontotart".$ai_totrows."  type=text     id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 style='text-align:right' readonly>";
								$ao_object[$ai_totrows][10]="<a href=javascript:uf_dt_activo(".$ai_totrows.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0></a>".			
													  " <input type=hidden name=hcodact".$ai_totrows."    id=hcodact".$ai_totrows." class=sin-borde size=15 maxlength=25  readonly>";
							}
						}
					}
				}
				else
				{
					break;
					if($ai_totrows>0)
					{$lb_valido=true;}
				}
			}//while($row=$this->io_sql->fetch_row($li_exec))
		}
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$this->io_sql->free_result($rs_data);
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_obtener_dt_pendiente


	function uf_siv_verificar_pendiente($as_codemp,$as_numsol,$as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_verificar_pendiente
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numsol    // numero de la solicitud de ejecución presupuestaria
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una solicitud de ejecucion presupuestaria  en la tabla
		//                 de sep_dt_articulos, e igualmante busca las denominaciones  de los articulos en la tabla siv_articulo
		//				   para luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 04/01/2007 								Fecha Última Modificación:
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		$lb_valido=true;
		$li_despachados=0;
		$ls_sql="SELECT SUM(siv_dt_despacho.canart) AS despachados, MAX(sep_dt_articulos.canart) AS solicitados,MAX(siv_dt_despacho.numorddes)".
				"  FROM sep_dt_articulos,siv_despacho,siv_dt_despacho".
				" WHERE sep_dt_articulos.codemp='". $as_codemp ."'".
				"   AND sep_dt_articulos.numsol='". $as_numsol ."'".
				"   AND sep_dt_articulos.codart='". $as_codart ."'".
				"   AND sep_dt_articulos.codemp=siv_despacho.codemp".
				"   AND sep_dt_articulos.numsol=siv_despacho.numsol".
				"   AND siv_despacho.estrevdes=1".
				"   AND sep_dt_articulos.codart=siv_dt_despacho.codart".
				"   AND siv_despacho.codemp=siv_dt_despacho.codemp".
				"   AND siv_despacho.numorddes=siv_dt_despacho.numorddes".
				" GROUP BY sep_dt_articulos.codart,sep_dt_articulos.codemp,sep_dt_articulos.numsol,".
				"          siv_despacho.codemp";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_verificar_pendiente ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_despachados=$row["despachados"];
			}//while($row=$this->io_sql->fetch_row($li_exec))
		}
		$this->io_sql->free_result($rs_data);
		return $li_despachados;
	} // end  function uf_siv_obtener_dt_pendiente
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_select_articulosrelacionados($as_codemp,$as_numsol,$ls_codart,$ai_adicionales)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_articulosrelacionados
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ai_adicionales=0;
		$ls_sql="SELECT SUM(siv_dt_despacho.canart) AS canart".
			  	"  FROM siv_dt_despacho, siv_despacho,siv_articulo".
			  	" WHERE  siv_dt_despacho.codemp=siv_despacho.codemp".
			  	"   AND siv_dt_despacho.codart=siv_articulo.codart".
			  	"   AND siv_dt_despacho.numorddes=siv_despacho.numorddes".
			  	"   AND siv_dt_despacho.codemp='". $as_codemp ."'".
			  	"   AND siv_despacho.numsol='". $as_numsol ."'".
			  	"   AND siv_despacho.estrevdes=1".
				"   AND siv_articulo.codartpri='". $ls_codart ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ai_adicionales=$row["canart"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ai_adicionales']=$ai_adicionales;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------


	function uf_siv_select_despacho($as_codemp,$as_numorddes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_select_despacho
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numorddes // numero de la orden de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que exista un maestro de despacho segun el numero de despacho, en la tabla siv_despacho
		//                 de sep_dt_articulos, e igualmante busca las denominaciones  de los articulos en la tabla siv_articulo
		//				   para luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 08/02/2006 								Fecha Última Modificación :08/02/2006 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT * FROM siv_despacho  ".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND numorddes='".$as_numorddes."'";
		$li_exec=$this->io_sql->select($ls_sql);
		if($li_exec===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_siv_select_despacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($li_exec))
			{
				$lb_valido=true;
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($li_exec);
		}
		return $lb_valido;
	} // end  function uf_siv_select_despacho

	function uf_siv_insert_despacho($as_codemp,$as_numorddes,$as_numsol,$as_coduniadm,$ad_fecdes,$as_obsdes,$as_codusu,
								    $as_estdes,$as_estrevdes,$as_codunides,$as_codfuefin,$aa_seguridad) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_despacho
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 					$as_numorddes // numero de la orden de despacho
		//                 $as_numsol // nro de la SEP						$as_coduniadm // codigo de unidad administrativa
		//                 $ad_fecdes // fecha del despacho					$as_obsdes    // observacion del despacho
		//                 $as_codusu //usuario que ralizo la recepcion		$as_estdes    // estatus dedespacho: 0--> , 1--> 
		//                 $as_estrevdes // estatus de reverso de despacho:   0-->Despacho Reversado , 1-->Despacho Activo 
		//                 $as_codunides // codigo de unidad a despachar 
		//                 $aa_seguridad // arreglo de registro de seguridad 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta  los  datos  maestros  de  un registro de despacho de almacen y genera  un numero  de
		//                 comprobante consecutivo,  en la tabla siv_recepcion
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$io_fun=  new class_funciones_db($this->con);
		$ls_emp="";
		$ls_tabla="siv_despacho";
		$ls_columna="numorddes";
		$as_numorddes=$io_fun->uf_generar_codigo($ls_emp,$as_codemp,$ls_tabla,$ls_columna);
		
		$ls_sql="INSERT INTO siv_despacho (codemp, numorddes, numsol, coduniadm, fecdes, obsdes, codusu, estdes, estrevdes, codunides, codfuefin)".
				"     VALUES ('".$as_codemp."','".$as_numorddes."','".$as_numsol."','".$as_coduniadm."','".$ad_fecdes."',".
				"             '".$as_obsdes."','".$as_codusu."',".$as_estdes.",".$as_estrevdes.",'".$as_codunides."','".$as_codfuefin."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_siv_insert_despacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
				$ls_evento="INSERT";
				$ls_descripcion ="Insertó el registro de la orden de Despacho ".$as_numorddes." proveniente de la SEP ".$as_numsol.
								 " Para la Unidad Administrativa ".$as_coduniadm." Asociada a la Empresa ".$as_codemp;
				$ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////		
		}
		$arrResultado['as_numorddes']=$as_numorddes;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}  // end  function uf_siv_insert_despacho

	function uf_siv_insert_dt_despacho($as_codemp,$as_numorddes,$as_codart,$as_numreg,$as_codalm,$as_unidad,$ai_canorisolsep,
									   $ai_canart,$ai_preuniart,$ai_monsubart,$ai_montotart,$ai_orden,$ai_canpenart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_despacho
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp    //codigo de empresa 				$as_numorddes    // numero de la orden de despacho
		//                 $as_codart    // codigo de articulo				$as_numreg       // numero consecutivo de registro
		//                 $as_codalm    // codigo de almacen				$as_unidad       // codigo de unidad M->Mayor D->Detal
		//                 $ai_canart   // cantidad despachada de articulos	$ai_canorisolsep // cantidad de articulos solicitada en la SEP
		//                 $ai_preuniart // precio unitario del articulo	$ai_monsubart    // monto sub-total por articulo
		//                 $ai_montotart // monto total de articulo   	    $ai_canoriart    // codigo de procedencia del documento
		//                 $ai_orden     // orden consecutivo de registro   $ai_canoriart    // codigo de procedencia del documento
		//  			   $as_canpenart // cantidad de articulos que quedan pendientes por entregar
		//  			   $as_numconrec // comprobante (numero concecutivo para hacer unica la recepcion)
		//                 $aa_seguridad // arreglo de registro de seguridad 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta un detalle de despacho de articulos de almacen asociado a su respectivo
		//                 maestro en la tabla de  siv_dt_despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_despacho(codemp,numorddes,codart,numreg,codalm,unidad,canorisolsep,canart,preuniart,".
		        "                            monsubart,montotart,canpenart,orden)".
				"     VALUES ('".$as_codemp."','".$as_numorddes."','".$as_codart."',".$as_numreg.",'".$as_codalm."','".$as_unidad."',".$ai_canorisolsep.",".
				"             ".$ai_canart.",".$ai_preuniart.",".$ai_monsubart.",".$ai_montotart.",".$ai_canpenart.",".$ai_orden.")";
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_siv_insert_dt_despacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		return $lb_valido;
	} // end  function uf_siv_insert_dt_despacho

	function uf_siv_insert_dt_scg($as_codemp,$as_codart,$as_codcmp,$ad_feccmp,$as_sccuenta,$as_debhab,$ai_monto,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_scg
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp    //codigo de empresa 					
		//                 $as_codart    // codigo de articulo					
		//                 $as_codcmp    // codigo de comprobante (numero de orden de despacho)			
		//                 $ad_feccmp    // fecha del comprobante
		//                 $as_sccuenta  // cuenta contable asociada
		//                 $as_debhab    // indica si el asiento contable es por el debe o por el haber 
		//                 $ai_monto     // monto del asiento contable
		//                 $aa_seguridad // arreglo de registro de seguridad 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el detalle contable asociado a un despacho de suministros en la tabla siv_dt_scg
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/09/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_scg (codemp,codart,codcmp,feccmp,sc_cuenta,debhab,monto,estint) ".
				"     VALUES ('".$as_codemp."','".$as_codart."','".$as_codcmp."','".$ad_feccmp."','".$as_sccuenta."','".$as_debhab."',".
				"             ".$ai_monto.",0)";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		   {
			 $this->io_msg->message("CLASE->sigesp_siv_c_despacho.php;MÉTODO->uf_siv_insert_dt_scg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			 $lb_valido=false;
		   }
		return $lb_valido;
	} // end  function uf_siv_insert_dt_scg

	function uf_siv_insert_dt_spg($as_codemp,$as_numorddes,$ad_fecdes,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,
								  $as_codestpro5,$as_estcla,$as_cuentapre,$as_montopre,$as_codfuefin,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_spg
		//         Access: public
		//      Argumento:  $aa_seguridad // arreglo de registro de seguridad 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el detalle presupuestario asociado a un despacho de suministros
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/09/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_spg (codemp,numorddes,feccmp,codestpro1,codestpro2,codestpro3,codestpro4,codestpro5,estcla,".
				"						 spg_cuenta,monto,estatus,codfuefin) ".
				"     VALUES ('".$as_codemp."','".$as_numorddes."','".$ad_fecdes."','".$as_codestpro1."','".$as_codestpro2."',".
				"             '".$as_codestpro3."','".$as_codestpro4."','".$as_codestpro5."','".$as_estcla."','".$as_cuentapre."',".
				"             ".$as_montopre.",'0','".$as_codfuefin."')";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		   {
			 $this->io_msg->message("CLASE->sigesp_siv_c_despacho.php;MÉTODO->uf_siv_insert_dt_spg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			 $lb_valido=false;
		   }
		return $lb_valido;
	} // end  function uf_siv_insert_dt_scg

	function uf_siv_insert_dt_scg_int($as_codemp,$as_codart,$as_codcmp,$ad_feccmp,$as_sccuenta,$as_debhab,$ad_mondetscg,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_insert_dt_scg_int
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp    //codigo de empresa 					
		//                 $as_codart    // codigo de articulo					
		//                 $as_codcmp    // codigo de comprobante (numero de orden de despacho)			
		//                 $ad_feccmp    // fecha del comprobante
		//                 $as_sccuenta  // cuenta contable asociada
		//                 $as_debhab    // indica si el asiento contable es por el debe o por el haber 
		//                 $ai_monto     // monto del asiento contable
		//                 $aa_seguridad // arreglo de registro de seguridad 
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que inserta el detalle contable asociado a un despacho de suministros en la tabla siv_dt_scg
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/09/2006								Fecha Última Modificación :
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="INSERT INTO siv_dt_scg_int (codemp,codart,codcmp,feccmp,sc_cuenta,debhab,monto,estint,estrepasi) ".
				"     VALUES ('".$as_codemp."','".$as_codart."','".$as_codcmp."','".$ad_feccmp."','".$as_sccuenta."','".$as_debhab."',".
				"             ".$ad_mondetscg.",0,0)";
		$li_row=$this->io_sql->execute($ls_sql);
		if ($li_row===false)
		   {
			 $this->io_msg->message("CLASE->sigesp_siv_c_despacho.php;MÉTODO->uf_siv_insert_dt_scg_int; ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			 $lb_valido=false;
		   }
		else
		   {
		     /////////////////////////////////         SEGURIDAD               /////////////////////////////		
			 $ls_evento="INSERT";
		  	 $ls_descripcion = "Insertó Comprobante Contable Inter Compañia Nro. ".$as_codcmp.", Cuenta $as_sccuenta, Operacion $as_debhab, Monto $ad_mondetscg, 
			                    Asociada a la Empresa ".$as_codemp;
			 $ls_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
			  								$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
											$aa_seguridad["ventanas"],$ls_descripcion);
			 /////////////////////////////////         SEGURIDAD               /////////////////////////////		
		   }
		return $lb_valido;
	} // end  function uf_siv_insert_dt_scg_int


	function uf_select_metodo($ls_metodo)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_metodo
		//         Access: private
		//      Argumento: $ls_metodo    // metodo de inventario
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que metodo de inventario esta siendo utilizado actualmente.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT * FROM siv_config";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_select_metodo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_metodo=$row["metodo"];
			}
			else
			{
				$lb_valido=false;
				$this->io_msg->message("No se ha definido la configuración de inventario");
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['ls_metodo']=$ls_metodo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_select_metodo
	
	function uf_select_movimiento($ls_metodo,$rs_metodo,$as_codart,$as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_movimiento
		//         Access: private
		//      Argumento: $ls_metodo    // metodo de inventario
		//                 $rs_metodo    // result set de la operacion del select
		//                 $as_codart    // codigo de articulo
		//                 $as_codalm    // codigo de almacén
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los movimientos que no han sido reversados y los ordena segun sea el el metodo 
	    //				   de inventario (en caso de ser FIFO ó LIFO), o saca el promedio si es Costo Promedio Ponderado
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		if($ls_metodo=="FIFO")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='". $as_codart ."'".
						"   AND codalm='". $as_codalm ."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						"       (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"         WHERE opeinv ='REV')".
						" ORDER BY nummov";
			}
			else
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='". $as_codart ."'".
						"   AND codalm='". $as_codalm ."'".
						"   AND promov || numdocori NOT IN".
						"       (SELECT promov || numdocori FROM siv_dt_movimiento".
						"         WHERE opeinv ='REV')".
						" ORDER BY nummov";
			}
			
			$rs_metodo=$this->io_sql->select($ls_sql);
		}
		if($ls_metodo=="LIFO")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='". $as_codart ."'".
						"   AND codalm='". $as_codalm ."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						"       (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"         WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			else
			{
				$ls_sql="SELECT * FROM siv_dt_movimiento".
						" WHERE  codart='". $as_codart ."'".
						"   AND codalm='". $as_codalm ."'".
						"   AND promov || numdocori NOT IN".
						"      (SELECT promov || numdocori FROM siv_dt_movimiento".
						"        WHERE opeinv ='REV')".
						" ORDER BY nummov DESC";
			}
			$rs_metodo=$this->io_sql->select($ls_sql);
		}	
		if($ls_metodo=="CPP")
		{
			if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
			{
				$ls_sql="SELECT Avg(cosart) as cosart".
						"  FROM siv_dt_movimiento".
						" WHERE  codart='". $as_codart ."'".
						"   AND codalm='". $as_codalm ."'".
						"   AND CONCAT(promov,numdocori) NOT IN".
						"       (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
						"         WHERE opeinv ='REV')";
			}
			else
			{
				$ls_sql="SELECT Avg(cosart) as cosart".
						"  FROM siv_dt_movimiento".
						" WHERE  codart='". $as_codart ."'".
						"   AND codalm='". $as_codalm ."'".
						"   AND promov || numdocori NOT IN".
						"      (SELECT promov || numdocori FROM siv_dt_movimiento".
						"        WHERE opeinv ='REV')";
			}
			$rs_metodo=$this->io_sql->select($ls_sql);
		}	
		if($rs_metodo===false)
		{
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_select_movimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		$arrResultado['rs_metodo']=$rs_metodo;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end function uf_select_movimiento
																		
	function uf_siv_procesar_dt_despacho($as_codemp,$as_numorddes,$as_codart,$as_codalm,$as_unidad,$ai_canorisolsep,$ai_canart,
										 $ai_preuniart,$ai_monsubart,$ai_montotart,$ai_orden,$as_nummov,$ad_fecdesaux,$as_numsol,
										 $ai_canpenart,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_procesar_dt_despacho
		//         Access: private
		//      Argumento: $as_codemp    // codigo de empresa							$as_numorddes // numero de orden de despacho
		//                 $as_codart    // codigo de articulo							$as_codalm    // codigo de almacén								
		//                 $as_unidad    // codigo de unidad M-->Mayor D->Detal		 	$ai_canorisolsep // cantidad de articulos de la SEP
		//                 $ai_canart    // cantidad despachada de articulos			$ai_preuniart    // precio unitario del articulo
		//                 $ai_canoriart // codigo de procedencia del documento			$as_nummov       // numero de movimiento
		//                 $ad_fecdesaux // fecha del despacho							$as_numsol      // numero de la SEP
		//                 $as_numconrec // comprobante (numero concecutivo para hacer unica la recepcion)
		//                 $aa_seguridad // arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Función que verifica que metodo de inventario se esta utilizando y además va buscando los precios unitarios 
	    //				   en caso de que no existan suficientes artiulos al mismo precio y procede a llamar al metodo de insert_dt_movimientos
	    //				   y al insert_dt_despacho para ingresarlo en la tabla siv_dt_despacho
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_metodo="";
		$rs_metodo="";
		$ls_concat=$this->con->Concat('siv_dt_movimiento.promov','siv_dt_movimiento.numdocori');
		$arrResultado=$this->uf_select_metodo($ls_metodo);
		$ls_metodo = $arrResultado['ls_metodo'];
		$lb_valido = $arrResultado['lb_valido'];
		if ($lb_valido)
		{
			$arrResultado=$this->uf_select_movimiento($ls_metodo,$rs_metodo,$as_codart,$as_codalm);
			$rs_metodo=$arrResultado['rs_metodo'];
			$lb_valido=$arrResultado['lb_valido'];
			if($lb_valido)
			{
				if($ls_metodo!="CPP")
				{
					$lb_break=false;
					$li_diferencia=0;
					$li_i=0;
					$li_canart=$ai_canart;
					while(($row=$this->io_sql->fetch_row($rs_metodo))&&(!$lb_break))
					{
						$li_preuniart=$row["cosart"];
						$ls_numdocori=$row["numdocori"];
						$ls_nummov=$row["nummov"];
						if(($this->ls_gestor=="MYSQLT") || ($this->ls_gestor=="MYSQLI"))
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) total".
									"  FROM siv_dt_movimiento".
									" WHERE codemp='". $as_codemp ."'".
									"   AND codart='". $as_codart ."'".
									"   AND codalm='". $as_codalm ."'".
									"   AND numdocori='". $ls_numdocori ."'".
									"   AND CONCAT(promov,numdocori) NOT IN".
									"      (SELECT CONCAT(promov,numdocori) FROM siv_dt_movimiento".
									"        WHERE opeinv ='REV')".
									" ORDER BY nummov";
						}
						if($this->ls_gestor=="INFORMIX")
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS total".
									"  FROM siv_dt_movimiento".
									" WHERE codemp='". $as_codemp ."'".
									"   AND codart='". $as_codart ."'".
									"   AND codalm='". $as_codalm ."'".
									"   AND numdocori='". $ls_numdocori ."'".
									"   AND promov || numdocori NOT IN".
									"      (SELECT promov || numdocori FROM siv_dt_movimiento".
									"        WHERE opeinv ='REV')".
									" GROUP BY nummov";
						}
						if($this->ls_gestor=="oci8po")
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) total".
									"  FROM siv_dt_movimiento".
									" WHERE codemp='". $as_codemp ."'".
									"   AND codart='". $as_codart ."'".
									"   AND codalm='". $as_codalm ."'".
									"   AND numdocori='". $ls_numdocori ."'".
									"   AND ". $ls_concat." NOT IN".
									"      (SELECT ".$ls_concat." FROM siv_dt_movimiento".
									"        WHERE opeinv ='REV')".
									" ORDER BY nummov";
						}
						else
						{
							$ls_sql="SELECT SUM(CASE opeinv WHEN 'ENT' THEN candesart ELSE -candesart END) AS total".
									"  FROM siv_dt_movimiento".
									" WHERE codemp='". $as_codemp ."'".
									"   AND codart='". $as_codart ."'".
									"   AND codalm='". $as_codalm ."'".
									"   AND numdocori='". $ls_numdocori ."'".
									"   AND promov || numdocori NOT IN".
									"      (SELECT promov || numdocori FROM siv_dt_movimiento".
									"        WHERE opeinv ='REV')".
									" GROUP BY nummov".
									" ORDER BY nummov";
						}

						$li_exec1=$this->io_sql->select($ls_sql);
						if($row1=$this->io_sql->fetch_row($li_exec1))
						{
							$li_existencia=$row1["total"];
							if ($li_existencia > 0)
							{
								$lb_encontrado=true;
								$li_i=$li_i + 1;

								if ($li_existencia < $li_canart)
								{
									$li_canart= $li_canart-$li_existencia;


									$lb_valido=$this->uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$as_codalm,$as_nummov,
																							$ls_numdocori,$li_existencia);
/*									if ($lb_valido)
									{
										$ls_opeinv="SAL";
										$ls_promov="DES";
										$ls_codprodoc="SEP";
										$li_candesart="0.00";
										$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecdesaux,
																						  	  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																							  $as_numsol,$li_existencia,$li_preuniart,$ls_promov,
																						  	  $as_numorddes,$li_candesart,$ad_fecdesaux,
																							  $aa_seguridad);
	
										if($lb_valido)
										{
											$lb_valido=$this->uf_siv_insert_dt_despacho($as_codemp,$as_numorddes,$as_codart,$li_i,$as_codalm,
																						$as_unidad,$ai_canorisolsep,$ai_canart,$ai_preuniart,
																						$ai_monsubart,$ai_montotart,$ai_orden,$aa_seguridad);
										}
										
									}			
*/															
								}  // fin  if ($li_existencia < $ai_canart)
								elseif($li_existencia >= $li_canart)
								{
									$lb_valido=$this->uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$as_codalm,$ls_nummov,
																							$ls_numdocori,$li_canart);
/*									if ($lb_valido)
									{
										$ls_opeinv="SAL";
										$ls_promov="DES";
										$ls_codprodoc="SEP";
										$li_candesart="0.00";
										$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecdesaux,
																						  	  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																							  $as_numsol,$ai_canart,$li_preuniart,$ls_promov,
																						  	  $as_numorddes,$li_candesart,$ad_fecdesaux,
																							  $aa_seguridad);
										if($lb_valido)
										{
											$lb_valido=$this->uf_siv_insert_dt_despacho($as_codemp,$as_numorddes,$as_codart,$li_i,$as_codalm,
																						$as_unidad,$ai_canorisolsep,$ai_canart,$ai_preuniart,
																						$ai_monsubart,$ai_montotart,$ai_orden,$aa_seguridad);
*/											if($lb_valido)
											{
												$lb_break=true;
											}
									//	}
								//	}
								}
								
								if(!$lb_valido)
								{
									$lb_break=true;
								}

							}  // fin  ($li_existencia > 0)
							
						}  //fin  if($row1=$io_sql->fetch_row($li_exec1))
		
					}// fin  while(($row=$io_sql->fetch_row($rs_metodo))&&(!$lb_break))
					if ($lb_valido)
					{
						$ls_opeinv="SAL";
						$ls_promov="DES";
						$ls_codprodoc="SEP";
						$li_candesart="0.00";
						$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecdesaux,
																			  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																			  $as_numsol,$ai_canart,$ai_preuniart,$ls_promov,
																			  $as_numorddes,$li_candesart,$ad_fecdesaux,
																			  $aa_seguridad);

						if($lb_valido)
						{
							$lb_valido=$this->uf_siv_insert_dt_despacho($as_codemp,$as_numorddes,$as_codart,$li_i,$as_codalm,
																		$as_unidad,$ai_canorisolsep,$ai_canart,$ai_preuniart,
																		$ai_monsubart,$ai_montotart,$ai_orden,$ai_canpenart,
																		$aa_seguridad);
						}
						
					}			

					
				}// fin  if($ls_metodo!="CPP")
				else
				{
					if($row=$this->io_sql->fetch_row($rs_metodo))
					{
						$li_preuniart=$row["cosart"];
						$ls_numdocori="";   
						$ls_opeinv="SAL";
						$ls_promov="DES";
						$ls_codprodoc="SEP";
						$li_candesart="0.00";
						$lb_valido=$this->io_mov->uf_siv_insert_dt_movimiento($as_codemp,$as_nummov,$ad_fecdesaux,
																			  $as_codart,$as_codalm,$ls_opeinv,$ls_codprodoc,
																			  $as_numsol,$ai_canart,$li_preuniart,$ls_promov,
																			  $as_numorddes,$li_candesart,$ad_fecdesaux,
																			  $aa_seguridad);

						if($lb_valido)
						{
							$li_i=1;
							$lb_valido=$this->uf_siv_insert_dt_despacho($as_codemp,$as_numorddes,$as_codart,$li_i,$as_codalm,
																		$as_unidad,$ai_canorisolsep,$ai_canart,$ai_preuniart,
																		$ai_monsubart,$ai_montotart,$ai_orden,$ai_canpenart,
																		$aa_seguridad);
					    }
						
					}// fin  if($row=$this->io_sql->fetch_row($rs_metodo))
					
				}// fin  else($ls_metodo!="CPP")
			/*	if($lb_valido)
				{
					$lb_valido=$this->uf_siv_update_sep($as_codemp,$as_numsol);  
				}*/
				
				
			}
			
		}
		return $lb_valido;
	}// end  function uf_siv_procesar_dt_despacho

	function uf_siv_disminuir_articuloxmovimiento($as_codemp,$as_codart,$as_codalm,$as_nummov,$ls_numdocori,$ai_cantidad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_disminuir_articuloxmovimiento
		//         Access: private
		//      Argumento: $as_codemp       // codigo de empresa
		//                 $as_codart       // codigo de articulo
		//                 $as_codalm       // codigo de almacen
		//                 $ls_numdocori    // numero original de la entrada de suministros a almacén
		//                 $as_nummov       // numero de movimiento
		//                 $as_cantidad     // cantidad de articulos
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que disminuye la cantidad de articulos proveniente de un movimiento en la tabla siv_dt_movimiento
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 09/02/2006 								Fecha Última Modificación :09/02/2006
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $rs_disart=-1;
		 $ld_date= date("Y-m-d");
		 $ls_sql= "UPDATE siv_dt_movimiento".
		 		  "   SET candesart= (candesart - '". $ai_cantidad ."'), ".
		 		  "       fecdesart= '" . $ld_date ."'".
				  " WHERE codemp='" . $as_codemp ."'".
				  "   AND opeinv='ENT'".
				  "   AND codart='" . $as_codart ."'".
				  "   AND codalm='" . $as_codalm ."'".
				  "   AND numdocori='" . $ls_numdocori ."'";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_siv_disminuir_articuloxmovimiento ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	}
	
	function uf_siv_update_sep($as_codemp,$as_numsol,$as_estsep) 
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_update_sep
		//         Access: private
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numsol // numero de la solicitud de ejecución presupuestaria
		//                 $as_estsep // estatus en que se va a colocar la SEP
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que actualiza el estatus de la solicitud de ejecución presupuestaria  estsol que indica
		//                 si la SEP fue despachada o no.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 16/02/2006	 								Fecha Última Modificación :16/02/2006	
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 $lb_valido=true;
		 $ls_sql= "UPDATE sep_solicitud SET  estsol='" . $as_estsep ."'".
				  " WHERE codemp='" . $as_codemp ."' ".
				  "   AND numsol='" . $as_numsol ."' ";
		$li_row = $this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_siv_update_sep ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			$lb_valido=true;
		}
	  return $lb_valido;
	} // end function uf_siv_update_sep

	function uf_siv_obtener_dt_despacho($as_codemp,$as_numodrdes,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_despacho
		//         Access: private
		//      Argumento: $as_codemp    //codigo de empresa 
		//                 $as_numodrdes // numero de orden de despacho
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a un despacho en la tabla siv_dt_despacho, e igualmante busca las
		//                 denominaciones  de los articulos en la tabla siv_articulopara luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/02/2006		 								Fecha Última Modificación :24/02/2006		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$li_numdecper=$_SESSION["la_empresa"]["numdecper"];
 		$lb_valido=true;
		$ai_totrows=0;
		$ls_sql= "SELECT siv_dt_despacho.*,siv_articulo.codart,siv_unidadmedida.unidad AS unidades,siv_unidadmedida.denunimed AS denunimed,".
				 "       (SELECT denart FROM siv_articulo ".
				 "         WHERE siv_articulo.codart=siv_dt_despacho.codart) AS denart,".
				 "       (SELECT nomfisalm FROM siv_almacen ".
				 "         WHERE siv_almacen.codalm=siv_dt_despacho.codalm) AS nomfisalm,".
				 "       (SELECT sep_dt_articulos.unidad FROM sep_dt_articulos,siv_despacho".
				 "         WHERE sep_dt_articulos.codemp=siv_despacho.codemp".
				 "           AND sep_dt_articulos.numsol=siv_despacho.numsol".
				 "           AND siv_despacho.codemp=siv_dt_despacho.codemp".
				 "           AND sep_dt_articulos.codemp=siv_dt_despacho.codemp".
				 "           AND siv_despacho.numorddes=siv_dt_despacho.numorddes".
				 "           AND sep_dt_articulos.codart=siv_dt_despacho.codart) AS unisol, ".
				 "        (SELECT tipart FROM siv_tipoarticulo ".
				 "         WHERE siv_articulo.codtipart =siv_tipoarticulo.codtipart) as tipart ".
				 "  FROM siv_dt_despacho,siv_articulo,siv_unidadmedida".
				 " WHERE siv_dt_despacho.codart=siv_articulo.codart".
				 "   AND siv_articulo.codunimed=siv_unidadmedida.codunimed".
				 "   AND siv_dt_despacho.codemp='". $as_codemp ."'".
				 "   AND siv_dt_despacho.numorddes='". $as_numodrdes ."'"; 
		$li_exec1=$this->io_sql->select($ls_sql);
		if($li_exec1===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_obtener_dt_despacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($li_exec1))
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codart=    $row["codart"];
				$ls_codalm=    $row["codalm"];
				$ls_denart=    $row["denart"];
				$ls_denunimed= $row["denunimed"];
				$ls_unidad=    $row["unidad"];
				$ls_unisol=    $row["unisol"];
				$li_unidad=    $row["unidades"];
				$li_preuniart= $row["preuniart"];
				$li_canart=    $row["canart"];
				$li_cansol=    $row["canorisolsep"];
				$li_montotart= $row["montotart"];
				$li_canpendes= $row["canpenart"];
				$ls_clasif   = $row["tipart"];
				switch ($ls_unidad) 
				{
					case "M":
						$ls_unidadaux="Mayor";
						break;
					case "D":
						$ls_unidadaux="Detal";
						break;
				}
				if($ls_unisol=="M")
				{
						$li_canpendes= ($li_canpendes/$li_unidad);
				}
				if($li_numdecper!="3")
				{
					$ls_funcion="onKeyPress=return(ue_formatonumero(this,'.',',',event));";
					$li_cansol=number_format($li_cansol,2,",",".");
					$li_canpendes=number_format($li_canpendes,2,",",".");
					$li_canart=number_format($li_canart,2,",",".");
				}
				else
				{
					$ls_funcion="onKeyPress=return(ue_formatonumero3(this,'.',',',event));";
					$li_cansol=number_format($li_cansol,3,",",".");
					$li_canpendes=number_format($li_canpendes,3,",",".");
					$li_canart=number_format($li_canart,3,",",".");
				}
				$ao_object[$ai_totrows][1]="<input name=txtdenart".$ai_totrows."     type=text     id=txtdenart".$ai_totrows." class=sin-borde size=25 maxlength=50 value='".$ls_denart."' readonly>".
										   "<input name=txtcodart".$ai_totrows."     type=hidden   id=txtcodart".$ai_totrows." class=sin-borde size=15 maxlength=30 value='".$ls_codart."' readonly>".
										   "<input name=txtcodartpri".$ai_totrows."  type=hidden   id=txtcodartpri".$ai_totrows." class=sin-borde size=15 maxlength=25  readonly>";
				$ao_object[$ai_totrows][2]="<input name=txtdenunimed".$ai_totrows."  type=text     id=txtdenunimed".$ai_totrows." class=sin-borde size=13 maxlength=10 value='". $ls_denunimed."' readonly>";
				$ao_object[$ai_totrows][3]="<input name=txtcodalm".$ai_totrows."     type=text     id=txtcodalm".$ai_totrows." class=sin-borde size=13 maxlength=10 value='". $ls_codalm."' readonly>";
				$ao_object[$ai_totrows][4]="<input name=txtunidad".$ai_totrows."     type=text     id=txtunidad".$ai_totrows." class=sin-borde size=12 maxlength=12 value='". $ls_unidadaux."' style='text-align:center' readonly></div>".
										   "<input name='hidunidad".$ai_totrows."'   type='hidden' id='hidunidad".$ai_totrows."' value='". $li_unidad ."'>";
				$ao_object[$ai_totrows][5]="<input name=txtcansol".$ai_totrows."     type=text     id=txtcansol".$ai_totrows." class=sin-borde size=12 maxlength=12 value='".$li_cansol."'       style='text-align:right' readonly>".
										   "<input name=hidexistencia".$ai_totrows." type=hidden   id=hidexistencia".$ai_totrows.">";
				$ao_object[$ai_totrows][6]="<input name=txtpenart".$ai_totrows."     type=text     id=txtpenart".$ai_totrows."    class=sin-borde size=12 maxlength=12 style='text-align:right' value='".$li_canpendes."' readonly>";
				$ao_object[$ai_totrows][7]="<input name=txtcanart".$ai_totrows."     type=text     id=txtcanart".$ai_totrows." class=sin-borde size=12 maxlength=12 value='".$li_canart."'       style='text-align:right' readonly'>";
				$ao_object[$ai_totrows][8]="<input name=txtpreuniart".$ai_totrows."  type=text     id=txtpreuniart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".number_format ($li_preuniart,2,",",".")."' style='text-align:right' readonly>".
									       "<input name=hidnumdocori".$ai_totrows."  type=hidden   id=hidnumdocori".$ai_totrows.">";
				$ao_object[$ai_totrows][9]="<input name=txtmontotart".$ai_totrows."  type=text     id=txtmontotart".$ai_totrows." class=sin-borde size=14 maxlength=15 value='".number_format ($li_montotart,2,",",".")."' style='text-align:right' readonly>";
				$ao_object[$ai_totrows][10]="<a href=javascript:uf_dt_activo(".$ai_totrows.");><img src=../shared/imagebank/mas.gif alt=Agregar Seriales width=15 height=15 border=0><input name=hclasi".$ai_totrows." type=hidden id=hclasi".$ai_totrows." class=sin-borde size=15 maxlength=25 value='".$ls_clasif."' readonly></a>".			
			      						   " <input type=hidden name=hcodact".$ai_totrows."    id=hcodact".$ai_totrows." class=sin-borde size=15 maxlength=25  readonly>";
			}
		}
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$this->io_sql->free_result($li_exec1);
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_obtener_dt_despacho

	function uf_siv_obtener_dt_scg($as_codemp,$as_numodrdes,$ai_totrows,$ao_object)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_scg
		//         Access: private
		//      Argumento: $as_codemp    //codigo de empresa 
		//                 $as_numodrdes // numero de orden de despacho
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a un despacho en la tabla siv_dt_despacho, e igualmante busca las
		//                 denominaciones  de los articulos en la tabla siv_articulopara luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 24/02/2006		 								Fecha Última Modificación :24/02/2006		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		$lb_valido=true;
		$ai_totrows=0;
		$ls_sql= "SELECT siv_dt_scg.*,siv_articulo.codart,".
				 "       (SELECT denart FROM siv_articulo ".
				 "         WHERE siv_articulo.codart=siv_dt_scg.codart) AS denart".
				 "  FROM siv_dt_scg,siv_articulo".
				 " WHERE siv_dt_scg.codart=siv_articulo.codart".
				 "   AND siv_dt_scg.codemp='". $as_codemp ."'".
				 "   AND siv_dt_scg.codcmp='". $as_numodrdes ."'".
				 " ORDER BY denart,debhab";
		$li_exec=$this->io_sql->select($ls_sql);
		if($li_exec===false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_obtener_dt_scg ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			while($row=$this->io_sql->fetch_row($li_exec))
			{
				$ai_totrows=$ai_totrows+1;
				$ls_codart=    $row["codart"];
				$ls_denart=    $row["denart"];
				$ls_sccuenta=  $row["sc_cuenta"];
				$ls_debhab=    $row["debhab"];
				$li_montoc=    $row["monto"];
				
				$ao_object[$ai_totrows][1]="<input  name=txtdenartc".$ai_totrows."  type=text   id=txtdenartc".$ai_totrows."  class=sin-borde size=50  value='".$ls_denart."'   readonly>".
										   "<input  name=txtcodartc".$ai_totrows."  type=hidden id=txtcodartc".$ai_totrows."  class=sin-borde size=30  value='".$ls_codart."'   readonly>";
				$ao_object[$ai_totrows][2]="<input  name=txtsccuenta".$ai_totrows." type=text   id=txtsccuenta".$ai_totrows." class=sin-borde size=30  value='".$ls_sccuenta."' readonly>";
				$ao_object[$ai_totrows][3]="<input  name=txtdebhab".$ai_totrows."   type=text   id=txtdebhab".$ai_totrows."   class=sin-borde size=15  value='".$ls_debhab."'   readonly style='text-align:center'>";
				$ao_object[$ai_totrows][4]="<input  name=txtmonto".$ai_totrows."    type=text   id=txtcansolc".$ai_totrows."  class=sin-borde size=30  value='".number_format ($li_montoc,2,",",".")."' style='text-align:right' readonly>";
			}
		}
		if ($ai_totrows==0)
		{$lb_valido=false;}
		$this->io_sql->free_result($li_exec);
		$arrResultado['ai_totrows']=$ai_totrows;
		$arrResultado['ao_object']=$ao_object;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end  function uf_siv_obtener_dt_scg

	function uf_siv_load_contabilizacion($as_codemp,$li_value)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_contabilizacion
		//         Access: private
		//      Argumento: $as_codemp   // codigo de empresa
		//                 $li_value    // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica que metodo de inventario esta siendo utilizado actualmente.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/01/2007 								Fecha Última Modificación:
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$li_value=0;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='CONTA DESPACHO'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_siv_load_contabilizacion ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$li_value=$row["value"];
			}
			else
			{
				$lb_valido=false;
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['li_value']=$li_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	} // end  function uf_siv_load_contabilizacion
//-----------------------------------------------------------------------------------------------------------------------------------
     function uf_cierrecontable()
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_cierrecontable
		//         Access: private
		//      Argumento: $as_codemp   // codigo de empresa
		//                 $li_value    // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica el cierre contable
		//	   Creado Por: Ing. Jennifer Rivero
		// Fecha Creación: 12/09/2008 								Fecha Última Modificación:
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = " SELECT estciescg FROM sigesp_empresa WHERE codemp='".$this->ls_codemp."'"; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Despacho MÉTODO->uf_cierrecontable ERROR->"
			                       .$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_estciescg=$row["estciescg"];
			}
			else
			{
				$ls_estciescg=0;
			}
			$this->io_sql->free_result($rs_data);
		}
		return $ls_estciescg;
	} // end  uf_cierrecontable
//--------------------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_codigoactivo($as_codart,$as_codact)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_codigoactivo
		//		   Access: public
		//		 Argumens: as_codart  // Codigo de Articulo
		//	  Description: Funcion que obtiene el codigo del activo asociado al articulo.
		//	   Creado Por: Ing. Yesenia Moreno/ Ing. Luis Lang
		// Fecha Creación: 11/11/2007 								Fecha Última Modificación : 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql="SELECT codact".
				"  FROM siv_articulo".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codart='".$as_codart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->Registro_Activo MÉTODO->uf_siv_load_codigoactivo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$as_codact=$row["codact"];				
			}
			else
			{
				$as_codact="---------------";
			}
			$this->io_sql->free_result($rs_data);
		}
		$arrResultado['as_codact']=$as_codact;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	} // end function uf_siv_select_movimiento
	//-----------------------------------------------------------------------------------------------------------------------------


	function uf_siv_validar_pendientes($as_codemp,$as_numsol)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_obtener_dt_pendiente
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numsol    // numero de la solicitud de ejecución presupuestaria
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una solicitud de ejecucion presupuestaria  en la tabla
		//                 de sep_dt_articulos, e igualmante busca las denominaciones  de los articulos en la tabla siv_articulo
		//				   para luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 04/01/2007 								Fecha Última Modificación:
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		$lb_valido=true;
		$li_selmay="";
		$li_seldet="";
		$lb_pendientes=false;
		$ls_sql="SELECT sep_dt_articulos.codart,MAX(sep_dt_articulos.unidad) AS unidad,MAX(sep_dt_articulos.canart) AS canart,siv_dt_despacho.numorddes,".
				" MAX(sep_dt_articulos.codestpro1) AS codestpro1 , MAX(sep_dt_articulos.codestpro2) AS codestpro2, ". 
				" MAX(sep_dt_articulos.codestpro3) AS codestpro3, MAX(sep_dt_articulos.codestpro4) AS codestpro4,".
				" MAX(sep_dt_articulos.codestpro5) AS codestpro5, MAX(sep_dt_articulos.estcla) AS estcla,".
				" MAX(sep_dt_articulos.spg_cuenta) AS spg_cuenta,".
				"      (SELECT canpenart".
				"         FROM siv_dt_despacho".
				"        WHERE siv_despacho.codemp=siv_dt_despacho.codemp".
				"          AND siv_despacho.numorddes=siv_dt_despacho.numorddes".
				"          AND sep_dt_articulos.codart=siv_dt_despacho.codart) AS canpenart".
				"  FROM sep_dt_articulos,siv_despacho,siv_dt_despacho".
				" WHERE sep_dt_articulos.codemp='". $as_codemp ."'".
				"   AND sep_dt_articulos.numsol='". $as_numsol ."'".
				"   AND sep_dt_articulos.codemp=siv_despacho.codemp".
				"   AND sep_dt_articulos.numsol=siv_despacho.numsol".
				"   AND siv_despacho.estrevdes=1".
//				"   AND sep_dt_articulos.codart=siv_dt_despacho.codart".
				"   AND siv_despacho.codemp=siv_dt_despacho.codemp".
				"   AND siv_despacho.numorddes=siv_dt_despacho.numorddes".
				" GROUP BY siv_dt_despacho.numorddes,sep_dt_articulos.codart,sep_dt_articulos.codemp,sep_dt_articulos.numsol,".
				"          siv_despacho.codemp,siv_despacho.numorddes".
				" ORDER BY siv_dt_despacho.numorddes DESC";//print $ls_sql;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_validar_pendientes ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			$ai_totrows=0;
			$ls_numorddesaux="";
			while($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_numorddes=$row["numorddes"];
				if(($ls_numorddesaux=="")||($ls_numorddes==$ls_numorddesaux))
				{
					$ls_numorddesaux=$ls_numorddes;
					$ls_codart=  $row["codart"]; 
					$li_despachados=$this->uf_siv_verificar_pendiente($as_codemp,$as_numsol,$ls_codart);
					$arrResultado=$this->uf_select_articulosrelacionados($as_codemp,$as_numsol,$ls_codart,$ai_adicionales);
					$ai_adicionales = $arrResultado['ai_adicionales'];
					$lb_valido = $arrResultado['lb_valido'];
					$li_despachados= $li_despachados + $ai_adicionales;
					$ls_cansol=  $row["canart"];
					$ls_unidad=  $row["unidad"];
					// Parametros para la ctasep
					$ls_codestpro1_sc=$row["codestpro1"];
					$ls_codestpro2_sc=$row["codestpro2"];
					$ls_codestpro3_sc=$row["codestpro3"];
					$ls_codestpro4_sc=$row["codestpro4"];
					$ls_codestpro5_sc=$row["codestpro5"];
					$ls_estcla_sc=$row["estcla"];
					$ls_spgcuenta_sc=$row["spg_cuenta"];
					// Fin Parametros para la ctasep
					//$ls_ctasep=  $row["sc_cuentasep"];
					$ls_ctasep=$this->uf_buscar_cuenta_sep($as_codemp,$ls_codestpro1_sc,$ls_codestpro2_sc,$ls_codestpro3_sc,$ls_codestpro4_sc,
														   $ls_codestpro5_sc,$ls_estcla_sc,$ls_spgcuenta_sc);
					$li_canpenart=$ls_cansol-$li_despachados;
					if($li_canpenart>0)
					{
						$lb_pendientes=true;
					}
					
				}
			}//while($row=$this->io_sql->fetch_row($li_exec))
		}
		$this->io_sql->free_result($rs_data);
		return $lb_pendientes;
	} // end  function uf_siv_obtener_dt_pendiente
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_select_articulo($as_codart,$as_origen,$ls_codartpri,$as_denart,$ai_unidad,$as_denartpri)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_select_articulo
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_denart="";
		$ai_unidad=0;
		$ls_sql="SELECT codart,denart,unidad,codartpri,".
				"	    (SELECT denart FROM siv_articulo AS articulo".
				"		  WHERE articulo.codemp=siv_articulo.codemp".
				"           AND articulo.codart=siv_articulo.codartpri) AS denartpri".
				"  FROM siv_articulo,siv_unidadmedida  ".
				" WHERE siv_articulo.codemp='".$this->ls_codemp."'".
				"   AND siv_articulo.codart='".$as_codart."'".
				"   AND siv_articulo.codunimed=siv_unidadmedida.codunimed" ;
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codartpri=$row["codartpri"];
				$as_denartpri=$row["denartpri"];
				$as_denart=$row["denart"];
				$ai_unidad=$row["unidad"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ls_codartpri']=$ls_codartpri;
		$arrResultado['as_denart']=$as_denart;
		$arrResultado['ai_unidad']=$ai_unidad;
		$arrResultado['as_denartpri']=$as_denartpri;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;		
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------
   //---------------------------------------------------------------------------------------------------------------------------
	function uf_siv_load_articulos_primarios($as_codemp,$as_value)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_load_articulos_primarios
		//         Access: public (sigesp_siv_d_configuracion)
		//      Argumento: $as_codemp     // codigo de empresa
		//                 $as_estcatsig  // estatus de contabilizacion de despacho
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que realiza una busqueda del estatus de contabilizacion de los despachos
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 11/01/2007							Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql = "SELECT value".
		          "  FROM sigesp_config".
				  " WHERE codemp='".$as_codemp."'".
				  "   AND codsis='SIV'".
				  "   AND seccion='CONFIG'".
				  "   AND entry='ARTICULO_PRI'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->configuracion MÉTODO->uf_siv_load_articulos_primarios ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_value=$row["value"];
				$this->io_sql->free_result($rs_data);
			}
			else
			{
				$lb_valido=false;
			}
		}
		$arrResultado['as_value']=$as_value;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end  function uf_siv_load_articulos_primarios
   //---------------------------------------------------------------------------------------------------------------------------
  //-----------------------------------------------------------------------------------------------------------------------------------
   	function uf_obtener_datos_articulo($as_codart,$ls_denart,$ls_sccuenta,$li_unidad,$ls_denunimed)
   	{
		//////////////////////////////////////////////////////////////////////////////
		//	Function:     uf_obtener_datos_articulo
		//	Arguments:    as_codart  // Codigo de articulo
		//	Returns:	  $la_alternos arreglo que contiene codigos alternos
		//	Description:  Función que obtiene los datos relacionados al articulo.
		//////////////////////////////////////////////////////////////////////////////
		global $io_sql,$io_msg;
		$ls_denart="";
		$ls_denunimed="";
		$ls_sccuenta="";
		$li_unidad="";
		$lb_valido=true;
		$ls_sql= "SELECT denart,sc_cuenta,".
				 "       (SELECT unidad FROM siv_unidadmedida ".
				 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS unidad,".
				 "       (SELECT denunimed FROM siv_unidadmedida ".
				 "         WHERE siv_articulo.codunimed=siv_unidadmedida.codunimed) AS denunimed".
				 "  FROM siv_articulo".
				 " WHERE codemp='". $_SESSION["la_empresa"]["codemp"] ."'".
				 "   AND codart='". $as_codart ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			print $io_sql->message;
			$lb_valido=false;
		}
		else
		{
			while(!$rs_data->EOF)
			{
				$ls_denart= $rs_data->fields["denart"];
				$ls_denunimed= $rs_data->fields["denunimed"];
				$ls_sccuenta= $rs_data->fields["sc_cuenta"];
				$li_unidad= $rs_data->fields["unidad"];
				$rs_data->MoveNext();
			}
		}
		$arrResultado['ls_denart']=$ls_denart;
		$arrResultado['ls_sccuenta']=$ls_sccuenta;
		$arrResultado['li_unidad']=$li_unidad;
		$arrResultado['ls_denunimed']=$ls_denunimed;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}
	//-----------------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_datos_presupuestarios($as_codart,$as_cuentaart,$as_cuentaiva,$as_formula)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_datos_presupuestarios
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_cuentaart="";
		$as_cuentaiva="";
		$as_formula="";
		$ls_sql="SELECT siv_articulo.spg_cuenta as cuentaart, sigesp_cargos.spg_cuenta as cuentaiva,sigesp_cargos.formula".
				"  FROM siv_articulo,siv_cargosarticulo,sigesp_cargos  ".
				" WHERE siv_articulo.codemp='".$this->ls_codemp."'".
				"   AND siv_articulo.codart='".$as_codart."'".
				"   AND siv_articulo.codemp=siv_cargosarticulo.codemp".
				"   AND siv_articulo.codart=siv_cargosarticulo.codart".
				"   AND siv_cargosarticulo.codemp=sigesp_cargos.codemp".
				"   AND siv_cargosarticulo.codcar=sigesp_cargos.codcar" ; 
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_siv_select_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_cuentaart=$row["cuentaart"];
				$as_cuentaiva=$row["cuentaiva"];
				$as_formula=$row["formula"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_cuentaart']=$as_cuentaart;
		$arrResultado['as_cuentaiva']=$as_cuentaiva;
		$arrResultado['as_formula']=$as_formula;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_siv_select_articulo
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_esructura_costos($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_esructura_costos
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_codestpro1="";
		$as_codestpro2="";
		$as_codestpro3="";
		$as_codestpro4="";
		$as_codestpro5="";
		$as_estcla="";
		$ls_sql="SELECT spg_ep5.codestpro1,spg_ep5.codestpro2,spg_ep5.codestpro3,spg_ep5.codestpro4,spg_ep5.codestpro5,spg_ep5.estcla".
				"  FROM spg_ep5,spg_ep1  ".
				" WHERE spg_ep1.codemp='".$this->ls_codemp."'".
				"   AND spg_ep1.estcencos='1'".
				"   AND spg_ep1.codemp=spg_ep5.codemp".
				"   AND spg_ep1.codestpro1=spg_ep5.codestpro1";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_esructura_costos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_codestpro1=$row["codestpro1"];
				$as_codestpro2=$row["codestpro2"];
				$as_codestpro3=$row["codestpro3"];
				$as_codestpro4=$row["codestpro4"];
				$as_codestpro5=$row["codestpro5"];
				$as_estcla=$row["estcla"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_buscar_esructura_costos
	//-----------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_esructura_sep($as_numsol,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_esructura_sep
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_codestpro1="";
		$as_codestpro2="";
		$as_codestpro3="";
		$as_codestpro4="";
		$as_codestpro5="";
		$as_estcla="";
		$ls_sql="SELECT sep_solicitud.codestpro1,sep_solicitud.codestpro2,sep_solicitud.codestpro3,".
				"       sep_solicitud.codestpro4,sep_solicitud.codestpro5,sep_solicitud.estcla".
				"  FROM sep_solicitud  ".
				" WHERE sep_solicitud.codemp='".$this->ls_codemp."'".
				"   AND sep_solicitud.numsol='".$as_numsol."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_esructura_sep ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_codestpro1=$row["codestpro1"];
				$as_codestpro2=$row["codestpro2"];
				$as_codestpro3=$row["codestpro3"];
				$as_codestpro4=$row["codestpro4"];
				$as_codestpro5=$row["codestpro5"];
				$as_estcla=$row["estcla"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_buscar_esructura_sep
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_esructura_iva($as_numsol,$as_cuenta,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_esructura_sep
		//         Access: public (sigesp_siv_d_articulo)
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$as_codestpro1="";
		$as_codestpro2="";
		$as_codestpro3="";
		$as_codestpro4="";
		$as_codestpro5="";
		$as_estcla="";
		$ls_sql="SELECT sep_cuentagasto.codestpro1,sep_cuentagasto.codestpro2,sep_cuentagasto.codestpro3,".
				"       sep_cuentagasto.codestpro4,sep_cuentagasto.codestpro5,sep_cuentagasto.estcla".
				"  FROM sep_cuentagasto  ".
				" WHERE sep_cuentagasto.codemp='".$this->ls_codemp."'".
				"   AND sep_cuentagasto.numsol='".$as_numsol."'".
				"   AND sep_cuentagasto.spg_cuenta='".$as_cuenta."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_esructura_sep ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$as_codestpro1=$row["codestpro1"];
				$as_codestpro2=$row["codestpro2"];
				$as_codestpro3=$row["codestpro3"];
				$as_codestpro4=$row["codestpro4"];
				$as_codestpro5=$row["codestpro5"];
				$as_estcla=$row["estcla"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['as_codestpro1']=$as_codestpro1;
		$arrResultado['as_codestpro2']=$as_codestpro2;
		$arrResultado['as_codestpro3']=$as_codestpro3;
		$arrResultado['as_codestpro4']=$as_codestpro4;
		$arrResultado['as_codestpro5']=$as_codestpro5;
		$arrResultado['as_estcla']=$as_estcla;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_buscar_esructura_sep
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_contable_costos($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$ls_spgcuenta,$ls_scgcuenta)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_contable_costos
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_scgcuenta="";
		$ls_sql="SELECT sc_cuenta".
				"  FROM spg_cuentas  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codestpro1='".$as_codestpro1."'".
				"   AND codestpro2='".$as_codestpro2."'".
				"   AND codestpro3='".$as_codestpro3."'".
				"   AND codestpro4='".$as_codestpro4."'".
				"   AND codestpro5='".$as_codestpro5."'".
				"   AND estcla='".$as_estcla."'".
				"   AND spg_cuenta='".$ls_spgcuenta."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_contable_costos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_scgcuenta=$row["sc_cuenta"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ls_scgcuenta']=$ls_scgcuenta;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_buscar_contable_costos
	//-----------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_centrocostos_almacen($as_codalm)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_almacen
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codcencos="";
		$ls_sql="SELECT codcencos".
				"  FROM siv_almacen  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codalm='".$as_codalm."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_centrocostos_almacen ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codcencos=$row["codcencos"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_codcencos;
	}// end function uf_buscar_centrocostos_almacen
	//-----------------------------------------------------------------------------------------------------------------------------


	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_centrocostos_articulo($as_codart)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_centrocostos_articulo
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_sc_cuentainv="";
		$ls_sql="SELECT sc_cuenta".
				"  FROM siv_articulo  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codart='".$as_codart."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_centrocostos_articulo ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_sc_cuentainv=$row["sc_cuenta"];
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_sc_cuentainv;
	}// end function uf_buscar_centrocostos_articulo
	//-----------------------------------------------------------------------------------------------------------------------------

	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_ccostos_estructura($as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$ls_codcencos)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_contable_costos
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=false;
		$ls_codcencos="";
		$ls_sql="SELECT codcencos".
				"  FROM spg_ep5  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codestpro1='".$as_codestpro1."'".
				"   AND codestpro2='".$as_codestpro2."'".
				"   AND codestpro3='".$as_codestpro3."'".
				"   AND codestpro4='".$as_codestpro4."'".
				"   AND codestpro5='".$as_codestpro5."'".
				"   AND estcla='".$as_estcla."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_contable_costos ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_codcencos=$row["codcencos"];
				$this->io_sql->free_result($rs_data);
			}
		}
		$arrResultado['ls_codcencos']=$ls_codcencos;
		$arrResultado['lb_valido']=$lb_valido;
		return $arrResultado;
	}// end function uf_buscar_contable_costos
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_buscar_cuenta_sep($as_codemp,$as_codestpro1,$as_codestpro2,$as_codestpro3,$as_codestpro4,$as_codestpro5,$as_estcla,$as_spgcuenta)

	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_buscar_cuenta_sep
		//         Access: public
		//      Argumento: $as_codart //codigo de articulo
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que verifica si existe un determinado articulo en la tabla siv_articulo
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 05/04/2010 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$ls_cuenta='-------------------------';
		$lb_valido=false;
		$ls_codcencos="";
		$ls_sql="SELECT sc_cuenta".
				"  FROM spg_cuentas  ".
				" WHERE codemp='".$this->ls_codemp."'".
				"   AND codestpro1='".$as_codestpro1."'".
				"   AND codestpro2='".$as_codestpro2."'".
				"   AND codestpro3='".$as_codestpro3."'".
				"   AND codestpro4='".$as_codestpro4."'".
				"   AND codestpro5='".$as_codestpro5."'".
				"   AND estcla='".$as_estcla."' ".
				"   AND spg_cuenta='".$as_spgcuenta."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data===false)
		{
			$this->io_msg->message("CLASE->articulo MÉTODO->uf_buscar_cuenta_sep ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$lb_valido=true;
				$ls_cuenta=trim($row["sc_cuenta"]);
				$this->io_sql->free_result($rs_data);
			}
		}
		return $ls_cuenta;
	}// end function uf_buscar_cuenta_sep
	//-----------------------------------------------------------------------------------------------------------------------------
	function uf_siv_verificar_estatus_reverso($as_codemp,$as_numorddes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_verificar_estatus_reverso
		//         Access: public (sigesp_siv_p_despacho)
		//      Argumento: $as_codemp //codigo de empresa 
		//                 $as_numsol    // numero de la solicitud de ejecución presupuestaria
		//                 $ai_totrows   // total de filas encontradas
		//                 $ao_object    // arreglo de objetos para pintar el grid
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que busca los articulos asociados a una solicitud de ejecucion presupuestaria  en la tabla
		//                 de sep_dt_articulos, e igualmante busca las denominaciones  de los articulos en la tabla siv_articulo
		//				   para luego imprimirlos en el grid de la pagina.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 04/01/2007 								Fecha Última Modificación:
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 		$lb_valido=true;
		$ls_estrevdes=1;
		$ls_sql="SELECT estrevdes".
				"  FROM siv_despacho".
				" WHERE siv_despacho.codemp='". $as_codemp ."'".
				"   AND siv_despacho.numorddes='". $as_numorddes ."'";
		$rs_data=$this->io_sql->select($ls_sql);
		if($rs_data==false)
		{
			$lb_valido=false;
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_siv_verificar_estatus_reverso ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
		}
		else
		{
			if($row=$this->io_sql->fetch_row($rs_data))
			{
				$ls_estrevdes=$row["estrevdes"];
			}
		}
		$this->io_sql->free_result($rs_data);
		return $ls_estrevdes;
	} // end  function uf_siv_obtener_dt_pendiente
	//-----------------------------------------------------------------------------------------------------------------------------

	function uf_delete_despacho($as_codemp,$as_numorddes,$aa_seguridad)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_delete_almacen
		//         Access: public (sigesp_siv_d_almacen)
		//      Argumento: $as_codemp //codigo de empresa ,$as_codalm //codigo de almacen, $aa_seguridad //arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un almacén determinado que no contenga articulos de la tabla de  siv_almacen.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$this->io_sql->begin_transaction();	
		$lb_valido= $this->uf_delete_dt_despacho($as_codemp,$as_numorddes);
		if($lb_valido)
		{
			$ls_sql= "DELETE FROM siv_despacho".
					 " WHERE codemp= '".$as_codemp. "'".
					 "   AND numorddes= '".$as_numorddes. "'"; 
			$li_row=$this->io_sql->execute($ls_sql);
			if($li_row===false)
			{
				$this->io_msg->message("CLASE->despacho MÉTODO->uf_delete_despacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
				$lb_valido=false;
				$this->io_sql->rollback();
			}
			else
			{
				$lb_valido=true;
				/////////////////////////////////         SEGURIDAD               /////////////////////////////
				$ls_evento="DELETE";
				$ls_descripcion ="Eliminó el Despacho ".$as_numorddes." Asociado a la Empresa ".$as_codemp;
				$lb_variable= $this->seguridad->uf_sss_insert_eventos_ventana($aa_seguridad["empresa"],
												$aa_seguridad["sistema"],$ls_evento,$aa_seguridad["logusr"],
												$aa_seguridad["ventanas"],$ls_descripcion);
				/////////////////////////////////         SEGURIDAD               /////////////////////////////			
				$this->io_sql->commit();
			}
		}			
		return $lb_valido;
	} //end function uf_siv_delete_almacen


	function uf_delete_dt_despacho($as_codemp,$as_numorddes)
	{
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//	     Function: uf_siv_delete_almacen
		//         Access: public (sigesp_siv_d_almacen)
		//      Argumento: $as_codemp //codigo de empresa ,$as_codalm //codigo de almacen, $aa_seguridad //arreglo de registro de seguridad
		//	      Returns: Retorna un Booleano
		//    Description: Funcion que elimina un almacén determinado que no contenga articulos de la tabla de  siv_almacen.
		//	   Creado Por: Ing. Luis Anibal Lang
		// Fecha Creación: 01/01/2006 								Fecha Última Modificación : 
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$lb_valido=true;
		$ls_sql= "DELETE FROM siv_dt_despacho".
				 " WHERE codemp= '".$as_codemp. "'".
				 "   AND numorddes= '".$as_numorddes. "'"; 
		$li_row=$this->io_sql->execute($ls_sql);
		if($li_row===false)
		{
			$this->io_msg->message("CLASE->despacho MÉTODO->uf_delete_despacho ERROR->".$this->io_funcion->uf_convertirmsg($this->io_sql->message));
			$lb_valido=false;
			$this->io_sql->rollback();
		}
		else
		{
			$lb_valido=true;
		}
		return $lb_valido;
	} //end function uf_siv_delete_almacen

}//end  class sigesp_siv_c_recepcion
?>
