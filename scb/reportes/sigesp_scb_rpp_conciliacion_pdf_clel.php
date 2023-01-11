<?PHP
/***********************************************************************************
* @fecha de modificacion: 26/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

    session_start(); 	  
	header("Pragma: public");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	if(!array_key_exists("la_logusr",$_SESSION))
	{
		print "<script language=JavaScript>";
		print "opener.document.form1.submit();";
		print "close();";
		print "</script>";		
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_encabezado_pagina($ls_banco,$ls_mes,$ls_anio,$ls_ctaban,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_encabezado_pagina
		//		    Acess: private 
		//	    Arguments: ldec_monto : Monto del cheque
		//	    		   ls_nomproben:  Nombre del proveedor o beneficiario
		//	    		   ls_monto : Monto en letras
		//	    		   ls_fecha : Fecha del cheque
		//				   io_pdf   : Instancia de objeto pdf
		//    Description: función que imprime los encabezados por página
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 21/11/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		$io_encabezado=$io_pdf->openObject();
		$io_pdf->saveState();
		$io_pdf->setStrokeColor(0,0,0);
		$io_pdf->addJpegFromFile('../../shared/imagebank/'.$_SESSION["ls_logo"],25,705,$_SESSION["ls_width"],$_SESSION["ls_height"]); // Agregar Logo
		$io_pdf->addText(250,730,12,"<b>Conciliación Bancaria</b>"); // Agregar el título
		$io_pdf->restoreState();
		$io_pdf->closeObject();
		$io_pdf->addObject($io_encabezado,'all');
		$io_pdf->addText(40,680,9,"Cuenta:   ".$ls_ctaban); // Agregar la Fecha
		$io_pdf->addText(400,680,9,"Mes:  ".$ls_mes." de ".$ls_anio); // Agregar la Fecha
		return $io_pdf;	
		
	}// end function uf_print_encabezadopagina
	//--------------------------------------------------------------------------------------------------------------------------------
	
	function uf_print_saldo_libro($ls_fecha,$ls_saldo){
		
//		$io_piesaldo=$io_pdf->openObject();
		$la_data[1]=array('titulo1'=>'<b>Saldo Segun LIBRO al '.$ls_fecha.'</b>','titulo2'=>'','titulo3'=>'','titulo4'=>$ls_saldo);
		$la_columnas=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'');
		$la_config=array('showHeadings'=>0, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>2, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo1'=>array('justification'=>'center','width'=>200), // Justificación y ancho de la columna
						 			   'titulo2'=>array('justification'=>'center','width'=>150), // Justificación y ancho de la columna
									   'titulo3'=>array('justification'=>'center','width'=>100), // Justificación y ancho de la columna
									   'titulo4'=>array('justification'=>'center','width'=>100))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
//		$io_pdf->closeObject();
//		$io_pdf->addObject($io_piesaldo,'all');
		unset($la_data);
		unset($la_columnas);
		unset($la_config);
		return $io_pdf;	
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_detalle($la_data_cheque_mas,$la_data_cheque_menos,$la_data_nota_deb_mas,$la_data_nota_deb_menos,
	$la_data_nota_cred_mas,$la_data_nota_cred_menos,$la_data_retiro_mas,$la_data_retiro_menos,$la_data_deposito_mas,
	$la_data_deposito_menos,$la_data_trans_no_regist_nd_mas,$la_data_trans_no_regist_nd_menos,
	$la_data_trans_no_regist_nc_mas,$la_data_trans_no_regist_nc_menos,$la_data_trans_no_regist_dp_mas,
	$la_data_trans_no_regist_dp_menos,$ld_fechas,$ls_salban,$ls_sallib,$la_data_errbco_cheque_mas,$la_data_errbco_cheque_menos,
	$la_data_errbco_nota_deb_mas,$la_data_errbco_nota_deb_menos,$la_data_errbco_nota_cred_mas,$la_data_errbco_nota_cred_menos,
	$la_data_errbco_retiro_mas,$la_data_errbco_retiro_menos,$la_data_errbco_deposito_mas,$la_data_errbco_deposito_menos,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle
		//	   Creado Por: Ing. Laura Cabré
		// Fecha Creación: 22/11/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$la_data[1]=array('titulo1'=>'Saldo Segun Banco al   '.$ld_fechas,'titulo2'=>'','titulo3'=>'','titulo4'=>$ls_salban);
		$ls_chkpass=0;
		///////////////////////////////////////////CHEQUES MENOS////////////////////////////////////////////////////////////////
		if (!empty($la_data_cheque_menos))
		{
			$la_data[2]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
			$la_data[3]=array('titulo1'=>'   Menos: Cheques','titulo2'=>'','titulo3'=>'','titulo4'=>'');
			$la_data[4]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

			//$la_data[2]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN BANCO</b>','titulo2'=>'','titulo3'=>'');
			//$la_data[3]=array('titulo1'=>'   Menos: Cheques','titulo2'=>'','titulo3'=>'');
			//$la_data[4]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');

			$ls_chkpass=1;
			$ls_contchkmenos=count((array)$la_data_cheque_menos);
			$li_acum_mon_cheq_menos=0;
			for($li_i=0;$li_i<$ls_contchkmenos;$li_i++)
		    {
				if(strlen($la_data_cheque_menos[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_cheque_menos[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_cheque_menos[$li_i]["nombre"];

				}
				else{
					//$ls_nombre1=str_pad($la_data_cheque_menos[$li_i]["nombre"],40," ");
					$ls_nombre1=$la_data_cheque_menos[$li_i]["nombre"];

				}
				$ls_fecha1=$la_data_cheque_menos[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_cheque_menos[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_cheque_menos[$li_i]["numdoc"].'      '.
														 $ls_fecha1.'',
									  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_cheq_menos=$li_acum_mon_cheq_menos+$la_data_cheque_menos[$li_i]["monto"];
			}
			$li_acum_mon_cheq_menos=number_format($li_acum_mon_cheq_menos,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_cheq_menos.')');	
		}
		///////////////////////////////////////////CHEQUES MENOS////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////NOTAS DEBITO MENOS////////////////////////////////////////////////////////////////
		if($ls_chkpass==0)
		{
			if (!empty($la_data_nota_deb_menos))
			{
				$la_data[2]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[3]=array('titulo1'=>'   Menos: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[4]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[2]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN BANCO</b>','titulo2'=>'','titulo3'=>'');
			   	//$la_data[3]=array('titulo1'=>'   Menos: Notas de Debito','titulo2'=>'','titulo3'=>'');
			    	//$la_data[4]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');

				$ls_chkpass=2;
				$ls_contndmenos=count((array)$la_data_nota_deb_menos);
				$li_acum_mon_nd_menos=0;
				for($li_i=0;$li_i<$ls_contndmenos;$li_i++)
				{
					if(strlen($la_data_nota_deb_menos[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_nota_deb_menos[$li_i]["nombre"],0,40);	
						$ls_nombre1=$la_data_nota_deb_menos[$li_i]["nombre"];	

					}
					else{
						$ls_nombre1=str_pad($la_data_nota_deb_menos[$li_i]["nombre"],40," ");	
					}
					$ls_fecha1=$la_data_nota_deb_menos[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_nota_deb_menos[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_nota_deb_menos[$li_i]["numdoc"].'    '.
																	 $ls_fecha1.'',
												  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_nd_menos=$li_acum_mon_nd_menos+$la_data_nota_deb_menos[$li_i]["monto"];
				}
				$li_acum_mon_nd_menos=number_format($li_acum_mon_nd_menos,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_nd_menos.')');	
			}
		}
		else
		{
			if (!empty($la_data_nota_deb_menos))
			{
				$la_data[3]=array('titulo1'=>'   Menos: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[4]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[]=array('titulo1'=>'   Menos: Notas de Debito','titulo2'=>'','titulo3'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
				$ls_chkpass=2;
				$ls_contndmenos=count((array)$la_data_nota_deb_menos);
				$li_acum_mon_nd_menos=0;
				for($li_i=0;$li_i<$ls_contndmenos;$li_i++)
				{
					if(strlen($la_data_nota_deb_menos[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_nota_deb_menos[$li_i]["nombre"],0,40);
						$ls_nombre1=$la_data_nota_deb_menos[$li_i]["nombre"];

					}
					else{
						$ls_nombre1=str_pad($la_data_nota_deb_menos[$li_i]["nombre"],40," ", STR_PAD_RIGHT);
					}
					$ls_fecha1=$la_data_nota_deb_menos[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_nota_deb_menos[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_nota_deb_menos[$li_i]["numdoc"].'    '.
																	 $ls_fecha1.'',
												  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_nd_menos=$li_acum_mon_nd_menos+$la_data_nota_deb_menos[$li_i]["monto"];
				}
				$li_acum_mon_nd_menos=number_format($li_acum_mon_nd_menos,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_nd_menos.')');	
			}
		}
		///////////////////////////////////////////NOTAS DEBITO MENOS////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////NOTAS DEBITO MAS////////////////////////////////////////////////////////////////
		///////////////////////////////////////////DEPOSITO MAS////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////NOTAS DEBITO MAS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		$ls_chkpasserrobco=0;
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_nota_deb_mas))
			{
				$ls_chkpasserrobco=1;
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Mas: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_nota_deb_mas))
			{

				$la_data[]=array('titulo1'=>'   Mas: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[]=array('titulo1'=>'   Mas: Notas de Debito','titulo2'=>'','titulo3'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_nota_deb_mas))
		{
			$li_conterrbcondmas=count((array)$la_data_errbco_nota_deb_mas);
			$li_acum_mon_errbco_nd_mas=0;
			for($li_i=0;$li_i<$$li_conterrbcondmas;$li_i++)
			{
				if(strlen($la_data_errbco_nota_deb_mas[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_nota_deb_mas[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_nota_deb_mas[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_nota_deb_mas[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_nota_deb_mas[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_nota_deb_mas[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_nota_deb_mas[$li_i]["numdoc"].'    '.
																 $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_nd_mas=$li_acum_mon_errbco_nd_mas+$la_data_errbco_nota_deb_mas[$li_i]["monto"];
			}
			$li_acum_mon_errbco_nd_mas=number_format($li_acum_mon_errbco_nd_mas,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_nd_mas.')');
		}
	
		///////////////////////////////////////////NOTAS DEBITO MENOS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_nota_deb_menos))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Menos: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_nota_deb_menos))
			{
				$la_data[]=array('titulo1'=>'   Menos: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_nota_deb_menos))
		{
			$li_conterrbcondmenos=count((array)$la_data_errbco_nota_deb_menos);
			$li_acum_mon_errbco_nd_menos=0;
			for($li_i=0;$li_i<$li_conterrbcondmenos;$li_i++)
			{
				if(strlen($la_data_errbco_nota_deb_menos[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_nota_deb_menos[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_nota_deb_menos[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_nota_deb_menos[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_nota_deb_menos[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_nota_deb_menos[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_nota_deb_menos[$li_i]["numdoc"].'    '.
																$ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_nd_menos=$li_acum_mon_errbco_nd_menos+$la_data_errbco_nota_deb_menos[$li_i]["monto"];
			}
			$li_acum_mon_errbco_nd_menos=number_format($li_acum_mon_errbco_nd_menos,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_nd_menos.')');
		}
		
		///////////////////////////////////////////CHEQUES MAS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_cheque_mas))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Mas: Cheques','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
				$ls_chkpass=2;
				$li_conterrbcochmas=count((array)$la_data_errbco_cheque_mas);
				$li_acum_mon_errbco_ch_mas=0;
				for($li_i=0;$li_i<$li_conterrbcochmas;$li_i++)
				{
					if(strlen($la_data_errbco_cheque_mas[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_errbco_cheque_mas[$li_i]["nombre"],0,40);
						$ls_nombre1=$la_data_errbco_cheque_mas[$li_i]["nombre"];

					}
					else{
						$ls_nombre1=str_pad($la_data_errbco_cheque_mas[$li_i]["nombre"],40," ");
					}
					$ls_fecha1=$la_data_errbco_cheque_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_errbco_cheque_mas[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_errbco_cheque_mas[$li_i]["numdoc"].'    '.
																	 $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_errbco_ch_mas=$li_acum_mon_errbco_ch_mas+$la_data_errbco_cheque_mas[$li_i]["monto"];
					
				}
				$li_acum_mon_errbco_ch_mas=number_format($li_acum_mon_errbco_ch_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_ch_mas.')');	
				$li_c=$li_a+1;
			}
		}
		else
		{
			if (!empty($la_data_errbco_cheque_mas))
			{
				$la_data[]=array('titulo1'=>'   Mas: Cheques','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[]=array('titulo1'=>'   Mas: Cheques','titulo2'=>'','titulo3'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
				$ls_chkpass=2;
				$li_conterrbcochmas=count((array)$la_data_errbco_cheque_mas);
				$li_acum_mon_errbco_ch_mas=0;
				for($li_i=0;$li_i<$li_conterrbcochmas;$li_i++)
				{
					if(strlen($la_data_errbco_cheque_mas[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_errbco_cheque_mas[$li_i]["nombre"],0,40);
						$ls_nombre1=$la_data_errbco_cheque_mas[$li_i]["nombre"];

					}
					else{
						$ls_nombre1=str_pad($la_data_errbco_cheque_mas[$li_i]["nombre"],40," ");
					}
					$ls_fecha1=$la_data_errbco_cheque_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_errbco_cheque_mas[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_errbco_cheque_mas[$li_i]["numdoc"].'    '.
																	  $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_errbco_ch_mas=$li_acum_mon_errbco_ch_mas+$la_data_errbco_cheque_mas[$li_i]["monto"];
					
				}
				$li_acum_mon_errbco_ch_mas=number_format($li_acum_mon_errbco_ch_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_ch_mas.')');	
			}
		}
		
		///////////////////////////////////////////CHEQUES MENOS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_cheque_menos))
			{	
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'');
				$la_data[]=array('titulo1'=>'   Menos: Cheques','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_cheque_menos))
			{
				$la_data[]=array('titulo1'=>'   Menos: Cheques','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_cheque_menos))
		{
			$li_conterrbcochmenos=count((array)$la_data_errbco_cheque_menos);
			$li_acum_mon_errbco_ch_menos=0;
			for($li_i=0;$li_i<$li_conterrbcochmenos;$li_i++)
			{
				if(strlen($la_data_errbco_cheque_menos[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_cheque_menos[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_cheque_menos[$li_i]["nombre"];
				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_cheque_menos[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_cheque_menos[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_cheque_menos[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_cheque_menos[$li_i]["numdoc"].'    '.
																  $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_ch_menos=$li_acum_mon_errbco_ch_menos+$la_data_errbco_cheque_menos[$li_i]["monto"];
				
			}
			$li_acum_mon_errbco_ch_menos=number_format($li_acum_mon_errbco_ch_menos,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_ch_menos.')');
		}
	
		///////////////////////////////////////////NOTAS CREDITO MAS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_nota_cred_mas))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Mas: Nota Credito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_nota_cred_mas))
			{
				$la_data[]=array('titulo1'=>'   Mas: Nota Credito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_nota_cred_mas))
		{
			$li_conterrbconcmas=count((array)$la_data_errbco_nota_cred_mas);
			$li_acum_mon_errbco_nc_mas=0;
			for($li_i=0;$li_i<$li_conterrbconcmas;$li_i++)
			{
				if(strlen($la_data_errbco_nota_cred_mas[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_nota_cred_mas[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_nota_cred_mas[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_nota_cred_mas[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_nota_cred_mas[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_nota_cred_mas[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_nota_cred_mas[$li_i]["numdoc"].'    '.
																 $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_nc_mas=$li_acum_mon_errbco_nc_mas+$la_data_errbco_nota_cred_mas[$li_i]["monto"];
				
			}
			$li_acum_mon_errbco_nc_mas=number_format($li_acum_mon_errbco_nc_mas,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_nc_mas.')');
		}
		
		///////////////////////////////////////////NOTAS CREDITOS MENOS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_nota_cred_menos))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Menos: Nota de Credito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_nota_cred_menos))
			{
				$la_data[]=array('titulo1'=>'   Menos: Nota de Credito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_nota_cred_menos))
		{
			$li_conterrbconcmenos=count((array)$la_data_errbco_nota_cred_menos);
			$li_acum_mon_errbco_nc_menos=0;
			for($li_i=0;$li_i<$li_conterrbconcmenos;$li_i++)
			{
				if(strlen($la_data_errbco_nota_cred_menos[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_nota_cred_menos[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_nota_cred_menos[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($ls_nombre1,40," ", STR_PAD_RIGHT);
				}
				$ls_fecha1=$la_data_errbco_nota_cred_menos[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_nota_cred_menos[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_nota_cred_menos[$li_i]["numdoc"].'    '.
																  $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_nc_menos=$li_acum_mon_errbco_nc_menos+$la_data_errbco_nota_cred_menos[$li_i]["monto"];
				
			}
			$li_acum_mon_errbco_nc_menos=number_format($li_acum_mon_errbco_nc_menos,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_nc_menos.')');
		}

		///////////////////////////////////////////RETIROS MAS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_retiro_mas))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Mas: Retiros','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_retiro_mas))
			{
				$la_data[]=array('titulo1'=>'   Mas: Retiros','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_retiro_mas))
		{
			$li_conterrbcoremas=count($la_data_errbco_retiro_mas);
			$li_acum_mon_errbco_re_mas=0;
			for($li_i=0;$li_i<$li_conterrbcoremas;$li_i++)
			{
				if(strlen($la_data_errbco_retiro_mas[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_retiro_mas[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_retiro_mas[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_retiro_mas[$li_i]["nombre"],50," ");
				}
				$ls_fecha1=$la_data_errbco_retiro_mas[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_retiro_mas[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_retiro_mas[$li_i]["numdoc"].'    '.
																  $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_re_mas=$li_acum_mon_errbco_re_mas+$la_data_errbco_retiro_mas[$li_i]["monto"];
				
			}
			$li_acum_mon_errbco_re_mas=number_format($li_acum_mon_errbco_re_mas,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_re_mas.')');
		}		
		
		///////////////////////////////////////////RETIROS MENOS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_retiro_menos))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Menos: Retiros','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
				$ls_chkpass=2;
				
			}
		}
		else
		{
			if (!empty($la_data_errbco_retiro_menos))
			{
				$la_data[]=array('titulo1'=>'   Menos: Nota de Credito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}	
		}
		
		if (!empty($la_data_errbco_retiro_menos))
		{
			$li_conterrbconcmenos=count((array)$la_data_errbco_retiro_menos);
			$li_acum_mon_errbco_nc_menos=0;
			for($li_i=0;$li_i<$li_conterrbconcmenos;$li_i++)
			{
				if(strlen($la_data_errbco_retiro_menos[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_retiro_menos[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_retiro_menos[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_retiro_menos[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_retiro_menos[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_retiro_menos[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_retiro_menos[$li_i]["numdoc"].'    '.
																 $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_nc_menos=$li_acum_mon_errbco_nc_menos+$la_data_errbco_retiro_menos[$li_i]["monto"];
				
			}
			$li_acum_mon_errbco_nc_menos=number_format($li_acum_mon_errbco_nc_menos,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_nc_menos.')');	
		}
		
		///////////////////////////////////////////DEPOSITOS MAS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_retiro_mas))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Mas: Depositos','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		else
		{
			if (!empty($la_data_errbco_retiro_mas))
			{
				$la_data[]=array('titulo1'=>'   Mas: Depositos','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}
		}
		
		if (!empty($la_data_errbco_deposito_mas))
		{
			$li_conterrbcodpmas=count((array)$la_data_errbco_deposito_mas);
			$li_acum_mon_errbco_dp_mas=0;
			for($li_i=0;$li_i<$li_conterrbcodpmas;$li_i++)
			{
				if(strlen($la_data_errbco_deposito_mas[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_deposito_mas[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_deposito_mas[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_deposito_mas[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_deposito_mas[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_deposito_mas[$li_i]["monto"],2,",",".");
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_deposito_mas[$li_i]["numdoc"].'    '.
																 $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_dp_mas=$li_acum_mon_errbco_dp_mas+$la_data_errbco_deposito_mas[$li_i]["monto"];
			}
			$li_acum_mon_errbco_dp_mas=number_format($li_acum_mon_errbco_dp_mas,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_dp_mas.')');
		}		
		
		///////////////////////////////////////////DEPOSITOS MENOS ERRORES DE BANCO////////////////////////////////////////////////////////////////
		if($ls_chkpasserrobco==0)
		{
			if (!empty($la_data_errbco_deposito_menos))
			{
				$la_data[]=array('titulo1'=>'<b>ERRORES DE BANCO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Menos: Depositos','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
				$ls_chkpass=2;
				
			}
		}
		else
		{
			if (!empty($la_data_errbco_deposito_menos))
			{
				$la_data[]=array('titulo1'=>'   Menos: Depositos','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[]=array('titulo1'=>'   Menos: Depositos','titulo2'=>'','titulo3'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');
			}	
		}
		
		if (!empty($la_data_errbco_deposito_menos))
		{
			$li_conterrbcodpmenos=count((array)$la_data_errbco_deposito_menos);
			$li_acum_mon_errbco_dp_menos=0;
			for($li_i=0;$li_i<$li_conterrbcodpmenos;$li_i++)
			{
				if(strlen($la_data_errbco_deposito_menos[$li_i]["nombre"])>40){
					//$ls_nombre1=substr($la_data_errbco_deposito_menos[$li_i]["nombre"],0,40);
					$ls_nombre1=$la_data_errbco_deposito_menos[$li_i]["nombre"];

				}
				else{
					$ls_nombre1=str_pad($la_data_errbco_deposito_menos[$li_i]["nombre"],40," ");
				}
				$ls_fecha1=$la_data_errbco_deposito_menos[$li_i]["fecha"];
				$ls_monto1=number_format($la_data_errbco_deposito_menos[$li_i]["monto"],2,",",".");
				$io_pdf->addText(330,$li_colfec2,7,$ls_fecha1);
				$la_data[]=array('titulo1'=>'     '.$la_data_errbco_deposito_menos[$li_i]["numdoc"].'    '.
																 $ls_fecha1.'',
											  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
				$li_acum_mon_errbco_dp_menos=$li_acum_mon_errbco_dp_menos+$la_data_errbco_deposito_menos[$li_i]["monto"];
				
			}
			$li_acum_mon_errbco_dp_menos=number_format($li_acum_mon_errbco_dp_menos,2,",",".");
			$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>'('.$li_acum_mon_errbco_dp_menos.')');	
		}
		//$ls_sallib=$ls_sallib+$li_acum_mon_trans_no_regist;
		$la_data[]=array('titulo1'=>'<b>Saldo Segun LIBRO al  '.$ld_fechas.'</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'<b>'.number_format($ls_sallib,2,",",".").'</b>');
		$la_columnas=array('titulo1'=>'<b>Descripcion</b>','titulo2'=>'','titulo3'=>'<b>Cifras Parciales</b>','titulo4'=>'<b>Cifras Totales</b>');
		/*$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 9, // Tamaño de Letras
						 'titleFontSize' => 8,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo1'=>array('justification'=>'left','width'=>200), // Justificación y ancho de la columna
						 			   'titulo2'=>array('justification'=>'right','width'=>150),// Justificación y ancho de la columna
									   'titulo3'=>array('justification'=>'right','width'=>100),// Justificación y ancho de la columna
									   'titulo4'=>array('justification'=>'right','width'=>100))); // Justificación y ancho de la columna*/
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo1'=>array('justification'=>'left','width'=>160), //Justificación y ancho de la columna
  									   'titulo2'=>array('justification'=>'left','width'=>240),// Justificación y ancho de la columna
						 			   'titulo3'=>array('justification'=>'right','width'=>75),// Justificación y ancho de la columna
									   'titulo4'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna

		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		return $io_pdf;	
	}// end function uf_print_detalle
	

	function uf_print_detalle_libro($la_data_cheque_mas,$la_data_cheque_menos,$la_data_nota_deb_mas,$la_data_nota_deb_menos,
	$la_data_nota_cred_mas,$la_data_nota_cred_menos,$la_data_retiro_mas,$la_data_retiro_menos,$la_data_deposito_mas,
	$la_data_deposito_menos,$la_data_trans_no_regist_nd_mas,$la_data_trans_no_regist_nd_menos,
	$la_data_trans_no_regist_nc_mas,$la_data_trans_no_regist_nc_menos,$la_data_trans_no_regist_dp_mas,
	$la_data_trans_no_regist_dp_menos,$ld_fechas,$ls_salban,$ls_sallib,$la_data_errbco_cheque_mas,$la_data_errbco_cheque_menos,
	$la_data_errbco_nota_deb_mas,$la_data_errbco_nota_deb_menos,$la_data_errbco_nota_cred_mas,$la_data_errbco_nota_cred_menos,
	$la_data_errbco_retiro_mas,$la_data_errbco_retiro_menos,$la_data_errbco_deposito_mas,$la_data_errbco_deposito_menos,$io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_detalle_libro
		//		    Acess: private 
		//	    Arguments: la_data // arreglo de información
		//	   			   io_pdf // Objeto PDF
		//    Description: función que imprime el detalle de partidas no registradas en libro
		//	   Creado Por: Ing. Laura Cabré
		//	   Modificado Por: Ing Tony Medina
		// Fecha Creación: 22/11/2006 	Fecha de Modificación: 14/07/2010
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$ls_chkpass=0;
		///////////////////////////////////////////CHEQUES MENOS////////////////////////////////////////////////////////////////
		if (!empty($la_data_cheque_menos))
		{
			$ls_chkpass=1;
		}
		///////////////////////////////////////////CHEQUES MENOS////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////NOTAS DEBITO MENOS////////////////////////////////////////////////////////////////
		if($ls_chkpass==0)
		{
			if (!empty($la_data_nota_deb_menos))
			{
				$ls_chkpass=2;
			}
		}
		else
		{
			if (!empty($la_data_nota_deb_menos))
			{
				$ls_chkpass=2;
			}
		}
		///////////////////////////////////////////NOTAS DEBITO MENOS////////////////////////////////////////////////////////////////
		
		///////////////////////////////////////////NOTAS DEBITO MAS////////////////////////////////////////////////////////////////
		//$li_acum_mon_trans_no_regist=0;

		$io_pdf->ezSetDy(-20);
		if($ls_chkpass==0)
		{
			if (!empty($la_data_trans_no_regist_nd_mas))
			{
				$la_data[1]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN LIBRO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[2]=array('titulo1'=>'   Mas: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[3]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				$ls_chkpass=3;
				$ls_cont_tras_no_reg_nd_mas=count((array)$la_data_trans_no_regist_nd_mas);
				$li_acum_mon_trans_no_regist_nd_mas=0;
				for($li_i=0;$li_i<$ls_cont_tras_no_reg_nd_mas;$li_i++)
				{
					if(strlen($la_data_trans_no_regist_nd_mas[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_trans_no_regist_nd_mas[$li_i]["nombre"],0,40);
						$ls_nombre1=$la_data_trans_no_regist_nd_mas[$li_i]["nombre"];

						

					}
					else{
						$ls_nombre1=str_pad($la_data_trans_no_regist_nd_mas[$li_i]["nombre"],40," ");
					}
					$ls_fecha1=$la_data_trans_no_regist_nd_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_trans_no_regist_nd_mas[$li_i]["monto"],2,",",".");
					$io_pdf->addText(330,$li_colfec3,7,$ls_fecha1);
					$la_data[]=array('titulo1'=>'     '.$la_data_trans_no_regist_nd_mas[$li_i]["numdoc"].'    '.
															 $ls_fecha1.'',
										  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_trans_no_regist_nd_mas=$li_acum_mon_trans_no_regist_nd_mas+$la_data_trans_no_regist_nd_mas[$li_i]["monto"];
				}
				//$li_acum_mon_trans_no_regist=$li_acum_mon_trans_no_regist+$li_acum_mon_trans_no_regist_nd_mas;
				$li_acum_mon_trans_no_regist_nd_mas=number_format($li_acum_mon_trans_no_regist_nd_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>$li_acum_mon_trans_no_regist_nd_mas);
			}
		}
		elseif($ls_chkpass==1)
		{
			if (!empty($la_data_trans_no_regist_nd_mas))
			{
				$la_data[1]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN LIBRO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[2]=array('titulo1'=>'   Mas: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[3]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN LIBRO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'   Mas: Notas de Debito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				//$la_data[]=array('titulo1'=>'     Nro.                               Fecha                  ','titulo2'=>'Beneficiario                                                                                           ','titulo3'=>'','titulo4'=>'');

				$ls_chkpass=3;
				$ls_cont_tras_no_reg_nd_mas=count((array)$la_data_trans_no_regist_nd_mas);
				$li_acum_mon_trans_no_regist_nd_mas=0;
				for($li_i=0;$li_i<$ls_cont_tras_no_reg_nd_mas;$li_i++)
				{
					if(strlen($la_data_trans_no_regist_nd_mas[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_trans_no_regist_nd_mas[$li_i]["nombre"],0,40);	
						$ls_nombre1=$la_data_trans_no_regist_nd_mas[$li_i]["nombre"];
					}
					else{
						$ls_nombre1=str_pad($la_data_trans_no_regist_nd_mas[$li_i]["nombre"],40," ");
					}
					$ls_fecha1=$la_data_trans_no_regist_nd_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_trans_no_regist_nd_mas[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_trans_no_regist_nd_mas[$li_i]["numdoc"].'    '.
															 $ls_fecha1.'',
										  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_trans_no_regist_nd_mas=$li_acum_mon_trans_no_regist_nd_mas+$la_data_trans_no_regist_nd_mas[$li_i]["monto"];
				}
				//$li_acum_mon_trans_no_regist=$li_acum_mon_trans_no_regist+$li_acum_mon_trans_no_regist_nd_mas;
				$li_acum_mon_trans_no_regist_nd_mas=number_format($li_acum_mon_trans_no_regist_nd_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>$li_acum_mon_trans_no_regist_nd_mas);
			}
		}
		///////////////////////////////////////////NOTAS DEBITO MAS////////////////////////////////////////////////////////////////

		///////////////////////////////////////////DEPOSITO MAS////////////////////////////////////////////////////////////////
		if($ls_chkpass==0)
		{
			if (!empty($la_data_trans_no_regist_dp_mas))
			{
				$la_data[1]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN LIBRO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[2]=array('titulo1'=>'   Mas: Deposito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[3]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');

				//$la_data[1]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN LIBRO</b>','titulo2'=>'','titulo3'=>'');
				//$la_data[2]=array('titulo1'=>'   Mas: Deposito','titulo2'=>'','titulo3'=>'');
				//$la_data[3]=array('titulo1'=>'     Nro.                               Fecha                  Beneficiario                                                                                           ','titulo2'=>'','titulo3'=>'');

				$ls_chkpass=4;
				$ls_cont_tras_no_reg_dp_mas=count((array)$la_data_trans_no_regist_dp_mas);
				$li_acum_mon_trans_no_regist_dp_mas=0;
				for($li_i=0;$li_i<$ls_cont_tras_no_reg_dp_mas;$li_i++)
				{
					if(strlen($la_data_trans_no_regist_dp_mas[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_trans_no_regist_dp_mas[$li_i]["nombre"],0,40);	
						$ls_nombre1=$la_data_trans_no_regist_dp_mas[$li_i]["nombre"];	

					}
					else{
						$ls_nombre1=str_pad($la_data_trans_no_regist_dp_mas[$li_i]["nombre"],40," ");
					}
					$ls_fecha1=$la_data_trans_no_regist_dp_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_trans_no_regist_dp_mas[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_trans_no_regist_dp_mas[$li_i]["numdoc"].'    '.
															 $ls_fecha1.'',
										  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_trans_no_regist_dp_mas=$li_acum_mon_trans_no_regist_dp_mas+$la_data_trans_no_regist_dp_mas[$li_i]["monto"];
				}
				//$li_acum_mon_trans_no_regist=$li_acum_mon_trans_no_regist+$li_acum_mon_trans_no_regist_dp_mas;
				$li_acum_mon_trans_no_regist_dp_mas=number_format($li_acum_mon_trans_no_regist_dp_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>$li_acum_mon_trans_no_regist_dp_mas);
			}
		}
		elseif($ls_chkpass==1)
		{
			if (!empty($la_data_trans_no_regist_dp_mas))
			{
				$la_data[]=array('titulo1'=>'<b>PARTIDAS NO REGISTRADAS EN LIBRO</b>','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'   Mas: Depositos','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				$ls_chkpass=4;
				$ls_cont_tras_no_reg_dp_mas=count((array)$la_data_trans_no_regist_dp_mas);
				$li_acum_mon_trans_no_regist_dp_mas=0;
				for($li_i=0;$li_i<$ls_cont_tras_no_reg_dp_mas;$li_i++)
				{
					if(strlen()>40){
						//$ls_nombre1=substr($la_data_trans_no_regist_dp_mas[$li_i]["nombre"],0,40);
						$ls_nombre1=$la_data_trans_no_regist_dp_mas[$li_i]["nombre"];

					}
					else{
						$ls_nombre1=str_pad($la_data_trans_no_regist_dp_mas[$li_i]["nombre"],40," ");
					}
					
					$ls_fecha1=$la_data_trans_no_regist_dp_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_trans_no_regist_dp_mas[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_trans_no_regist_dp_mas[$li_i]["numdoc"].'    '.
															 $ls_fecha1.'        ',
										  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_trans_no_regist_dp_mas=$li_acum_mon_trans_no_regist_dp_mas+$la_data_trans_no_regist_dp_mas[$li_i]["monto"];
				}
				//$li_acum_mon_trans_no_regist=$li_acum_mon_trans_no_regist+$li_acum_mon_trans_no_regist_dp_mas;
				$li_acum_mon_trans_no_regist_dp_mas=number_format($li_acum_mon_trans_no_regist_dp_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>$li_acum_mon_trans_no_regist_dp_mas);
			}
		}
		else
		{
			if (!empty($la_data_trans_no_regist_dp_mas))
			{
				$la_data[]=array('titulo1'=>'   Mas: Deposito','titulo2'=>'','titulo3'=>'','titulo4'=>'');
				$la_data[]=array('titulo1'=>'     Nro.                               Fecha','titulo2'=>'Beneficiario','titulo3'=>'','titulo4'=>'');
				$ls_chkpass=4;
				$ls_cont_tras_no_reg_dp_mas=count((array)$la_data_trans_no_regist_dp_mas);
				$li_acum_mon_trans_no_regist_dp_mas=0;
				for($li_i=0;$li_i<$ls_cont_tras_no_reg_dp_mas;$li_i++)
				{
					if(strlen($la_data_trans_no_regist_dp_mas[$li_i]["nombre"])>40){
						//$ls_nombre1=substr($la_data_trans_no_regist_dp_mas[$li_i]["nombre"],0,40);
						$ls_nombre1=$la_data_trans_no_regist_dp_mas[$li_i]["nombre"];

					}
					else{
						$ls_nombre1=str_pad($la_data_trans_no_regist_dp_mas[$li_i]["nombre"],40," ");	
					}
					$ls_fecha1=$la_data_trans_no_regist_dp_mas[$li_i]["fecha"];
					$ls_monto1=number_format($la_data_trans_no_regist_dp_mas[$li_i]["monto"],2,",",".");
					$la_data[]=array('titulo1'=>'     '.$la_data_trans_no_regist_dp_mas[$li_i]["numdoc"].'    '.
															 $ls_fecha1.'        ',
										  'titulo2'=>$ls_nombre1,'titulo3'=>$ls_monto1,'titulo4'=>'');
					$li_acum_mon_trans_no_regist_nd_mas=$li_acum_mon_trans_no_regist_dp_mas+$la_data_trans_no_regist_dp_mas[$li_i]["monto"];
				}
				//$li_acum_mon_trans_no_regist=$li_acum_mon_trans_no_regist+$li_acum_mon_trans_no_regist_dp_mas;
				$li_acum_mon_trans_no_regist_dp_mas=number_format($li_acum_mon_trans_no_regist_dp_mas,2,",",".");
				$la_data[]=array('titulo1'=>'','titulo2'=>'','titulo3'=>'','titulo4'=>$li_acum_mon_trans_no_regist_dp_mas);
			}
		}	
		$la_columnas=array('titulo1'=>'<b>Descripcion</b>','titulo2'=>'','titulo3'=>'<b>Cifras Parciales</b>','titulo4'=>'<b>Cifras Totales</b>');
		$la_config=array('showHeadings'=>1, // Mostrar encabezados
						 'fontSize' => 7, // Tamaño de Letras
						 'titleFontSize' => 12,  // Tamaño de Letras de los títulos
						 'showLines'=>1, // Mostrar Líneas
						 'shaded'=>0, // Sombra entre líneas
						 'shadeCol'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'shadeCol2'=>array((249/255),(249/255),(249/255),(249/255)), // Color de la sombra
						 'width'=>540, // Ancho de la tabla
						 'maxWidth'=>540, // Ancho Máximo de la tabla
						 'xOrientation'=>'center', // Orientación de la tabla
				         'outerLineThickness'=>0.5,
						 'innerLineThickness' =>0.5,
						 'cols'=>array('titulo1'=>array('justification'=>'left','width'=>160), //Justificación y ancho de la columna
  									   'titulo2'=>array('justification'=>'left','width'=>240),// Justificación y ancho de la columna
						 			   'titulo3'=>array('justification'=>'right','width'=>75),// Justificación y ancho de la columna
									   'titulo4'=>array('justification'=>'right','width'=>75))); // Justificación y ancho de la columna
		$io_pdf->ezTable($la_data,$la_columnas,'',$la_config);
		return $io_pdf;	
	}// end function uf_print_detalle_libro

	
	
	
	//--------------------------------------------------------------------------------------------------------------------------------
	function uf_print_autorizacion($io_pdf)
	{
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//       Function: uf_print_autorizacion
		//		    Acess: private 
		//	    Arguments: io_pdf // Objeto PDF
		//    Description: función el final del voucher 
		//	   Creado Por: Ing. Nelson Barraez
		// Fecha Creación: 25/04/2006 
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       	$io_pie = $io_pdf->openObject();
		$io_pdf->Rectangle(26,20,550,70);
		$io_pdf->line(130,20,130,90);		
		$io_pdf->line(240,20,240,90);		
		$io_pdf->line(380,20,380,90);		
		$io_pdf->addText(40,80,9,"Tesoreria"); // Agregar el título
		$io_pdf->addText(157,80,9,"V.B. Contabilidad"); // Agregar el título
		$io_pdf->addText(275,80,9,"V.B. Presupuesto"); // Agregar el título
		$io_pdf->addText(440,80,9,"Jefatura de Administración"); // Agregar el título
		$io_pdf->closeObject();
		$io_pdf->addObject($io_pie,'all');
		return $io_pdf;	
	}	
	
	function uf_convertir($ls_numero)
	{
		$ls_numero=str_replace(".","",$ls_numero);
		$ls_numero=str_replace(",",".",$ls_numero);
		return $ls_numero;
	}
	//--------------------------------------------------------------------------------------------------------------------------------
	require_once("scb_report_conciliacion.php");
	require_once('../../shared/class_folder/class_pdf.php');
	require_once('../../base/librerias/php/general/sigesp_lib_fecha.php');	
	require_once("../../base/librerias/php/general/sigesp_lib_funciones2.php");
	require_once("../../base/librerias/php/general/sigesp_lib_include.php");
	require_once("../../base/librerias/php/general/sigesp_lib_sql.php");
	require_once("../../base/librerias/php/general/sigesp_lib_mensajes.php");
	require_once("../../base/librerias/php/general/sigesp_lib_datastore.php");	

	$in			  = new sigesp_include();
	$con		  =	$in->uf_conectar();
	$io_sql		  = new class_sql($con);	
	$io_report	  = new scb_report_conciliacion($con);
	$io_funciones = new class_funciones();	
	$io_fecha     = new class_fecha();		
	$ds_concil	  = new class_datastore();	
	$io_fecha	  = new class_fecha();
	
	$ls_codemp      = $_SESSION["la_empresa"]["codemp"];
	$ls_codban      = $_GET["codban"];
	$ls_nomban      = $_GET["nomban"];
	$ls_ctaban      = $_GET["ctaban"];
	$ls_mesano      = $_GET["mesano"];		
	$ls_tipbol      = 'Bs.';
	$ls_tiporeporte = 0;
	$ls_tiporeporte = $_GET["tiporeporte"];
	global $ls_tiporeporte;
	if ($ls_tiporeporte==1)
	   {
		 require_once("scb_report_conciliacionbsf.php");
		 $io_report = new scb_report_conciliacionbsf($con);
		 $ls_tipbol = 'Bs.F.';
		 $ldec_salseglib = $_GET["salseglib"];
		 $ldec_salsegbco = $_GET["salsegbco"];	 
		 $ldec_salseglib=$io_monedabsf->uf_convertir_monedabsf($ldec_salseglib,$_SESSION["la_empresa"]["candeccon"],$_SESSION["la_empresa"]["tipconmon"],1000,$_SESSION["la_empresa"]["redconmon"]);
		 $ldec_salsegbco=$io_monedabsf->uf_convertir_monedabsf($ldec_salsegbco,$_SESSION["la_empresa"]["candeccon"],$_SESSION["la_empresa"]["tipconmon"],1000,$_SESSION["la_empresa"]["redconmon"]);
	   }
	 else
	 {
		 $ldec_salseglib = $_GET["salseglib"];
		 $ldec_salsegbco = $_GET["salsegbco"];	 
	 }
	$la_codban=explode('<<<',$ls_codban);
	$la_ctaban=explode('<<<',$ls_ctaban);
	$la_mesano=explode('<<<',$ls_mesano);
	$la_salseglib=explode('<<<',$ldec_salseglib);
	$la_salsegbco=explode('<<<',$ldec_salsegbco);
	$li_totaldata=count((array)$la_codban);

	$io_pdf=new class_pdf('LETTER','portrait'); // Instancia de la clase PDF
	$io_pdf->selectFont('../../base/librerias/php/ezpdf/fonts/Helvetica.afm'); // Seleccionamos el tipo de letra
	$io_pdf->set_margenes(49,35,3,3); // Configuración de los margenes en centímetros
		
		set_time_limit(1800);

	for ($li_z=0;($li_z<$li_totaldata);$li_z++)
	{
		if($li_z>0)
			$io_pdf->ezNewPage();  
		$ls_codban=$la_codban[$li_z];
		$ls_ctaban=$la_ctaban[$li_z];
		$ls_mesano=$la_mesano[$li_z];
		$ldec_salseglib=$la_salseglib[$li_z];
		$ldec_salsegbco=$la_salsegbco[$li_z];
		$ls_nomban=$io_report->buscar_nombre_banco($ls_codban);
		$arrResultado="";
		$data="";
		$arrResultado=$io_report->uf_obtener_mov_conciliacion($ls_mesano,$ls_codban,$ls_ctaban,$ldec_salseglib,$ldec_salsegbco);
		$data=$arrResultado["data"];
		$ldec_salsegbco=$arrResultado["ldec_salsegbco"];
		$ls_tipo_cuenta=$io_report->uf_tipo_cuenta($ls_codban,$ls_ctaban);
		$ds_concil->data=$data;
		$li_totrow=$ds_concil->getRowCount("numdoc");
		if(($data===false))
		{
			?>
			<script >
				alert("Error al buscar datos de la conciliación");
				close();
			</script>
			<?php
		}	
		$ld_fechasta=$io_fecha->uf_last_day(substr($ls_mesano,0,2),substr($ls_mesano,2,4));	
		$ls_mes=$io_fecha->uf_load_nombre_mes(substr($ls_mesano,0,2));
		$ls_anio=substr($ls_mesano,2,4);
		$io_pdf=uf_print_encabezado_pagina($ls_nomban,$ls_mes,$ls_anio,$ls_ctaban,$io_pdf); //Se imprime la tabla de la cabecera
		$li_temp=1;
		$la_data=array();
		$la_data_cheque_mas      = array();
		$la_data_cheque_menos    = array();
		$la_data_nota_deb_mas    = array();
		$la_data_nota_deb_menos  = array();
		$la_data_nota_cred_mas   = array();
		$la_data_nota_cred_menos = array();
		$la_data_retiro_mas      = array();
		$la_data_retiro_menos    = array();
		$la_data_deposito_mas    = array();
		$la_data_deposito_menos  = array();
		$la_data_trans_no_regist_nd_mas   = array();
		$la_data_trans_no_regist_nd_menos = array();
		$la_data_trans_no_regist_nc_mas   = array();
		$la_data_trans_no_regist_nc_menos = array();
		$la_data_trans_no_regist_dp_mas   = array();
		$la_data_trans_no_regist_dp_menos = array();
		$la_data_errbco_cheque_mas        = array();
		$la_data_errbco_cheque_menos        = array();
		$la_data_errbco_nota_deb_mas      = array();
		$la_data_errbco_nota_deb_menos    = array();
		$la_data_errbco_nota_cred_mas     = array();
		$la_data_errbco_nota_cred_menos   = array();
		$la_data_errbco_retiro_mas        = array();
		$la_data_errbco_retiro_menos      = array();
		$la_data_errbco_deposito_mas      = array();
		$la_data_errbco_deposito_menos      = array();
		$li_temp_cheque_mas      = 0;
		$li_temp_cheque_menos    = 0;
		$li_temp_nota_deb_mas    = 0;
		$li_temp_nota_deb_menos  = 0;
		$li_temp_nota_cred_mas   = 0;
		$li_temp_nota_cred_menos = 0;
		$li_temp_retiro_mas      = 0;
		$li_temp_retiro_menos    = 0;
		$li_temp_deposito_mas    = 0;
		$li_temp_deposito_menos  = 0;
		$li_temp_trans_no_regist_nd_mas   = 0;
		$li_temp_trans_no_regist_nd_menos = 0;
		$li_temp_trans_no_regist_nc_mas   = 0;
		$li_temp_trans_no_regist_nc_menos = 0;
		$li_temp_trans_no_regist_dp_mas   = 0;
		$li_temp_trans_no_regist_dp_menos = 0;
		$li_temp_errbco_cheque_mas        = 0;
		$li_temp_errbco_cheque_menos      = 0;
		$li_temp_errbco_nota_deb_mas      = 0;
		$li_temp_errbco_nota_deb_menos    = 0;
		$li_temp_errbco_nota_cred_mas     = 0;
		$li_temp_errbco_nota_cred_menos   = 0;
		$li_temp_errbco_retiro_mas        = 0;
		$li_temp_errbco_retiro_menos      = 0;
		$li_temp_errbco_deposito_mas      = 0;
		
		for($li_i=1;$li_i<=$li_totrow;$li_i++)
		{
			$li_temp=$li_temp+1;
	
			$li_totprenom = 0;
			$ldec_mondeb  = 0;
			$ldec_monhab  = 0;
			$li_totant    = 0;		
			$ls_tipo      = $ds_concil->getValue("tipo",$li_i);
			$ls_suma      = $ds_concil->getValue("suma",$li_i);
			$ls_codope    = $ds_concil->getValue("codope",$li_i);
			$ls_numdoc    = $ds_concil->getValue("numdoc",$li_i);
			$ls_nomproben = $ds_concil->getValue("nomproben",$li_i);
			$ld_fecmov    = $ds_concil->getValue("fecmov",$li_i);
			$ldec_monto   = $ds_concil->getValue("monto",$li_i);
			$ls_estreglib = $ds_concil->getValue("estreglib",$li_i);
			$ld_fecmov    = $io_funciones->uf_convertirfecmostrar($ld_fecmov);
			$ls_item      = $ls_numdoc."  ".$ls_nomproben."   ".$ld_fecmov;
			if($ls_tipo=="C1"||$ls_tipo=="C2"){
				if($ls_suma=='+'){//En caso que sean mas
					switch($ls_codope){
						case "CH":
							$la_data_errbco_cheque_mas[$li_temp_errbco_cheque_mas]["fecha"]=$ld_fecmov;
							$la_data_errbco_cheque_mas[$li_temp_errbco_cheque_mas]["numdoc"]=$ls_numdoc;
							$la_data_errbco_cheque_mas[$li_temp_errbco_cheque_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_cheque_mas[$li_temp_errbco_cheque_mas]["monto"]=$ldec_monto;					
							$li_temp_errbco_cheque_mas++;
							break;
						
						case "ND":
							$la_data_errbco_nota_deb_mas[$li_temp_errbco_nota_deb_mas]["fecha"]=$ld_fecmov;
							$la_data_errbco_nota_deb_mas[$li_temp_errbco_nota_deb_mas]["numdoc"]=$ls_numdoc;
							$la_data_errbco_nota_deb_mas[$li_temp_errbco_nota_deb_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_nota_deb_mas[$li_temp_errbco_nota_deb_mas]["monto"]=$ldec_monto;					
							$li_temp_errbco_nota_deb_mas++;
							break;
							
						case "NC":
							$la_data_errbco_nota_cred_mas[$li_temp_errbco_nota_cred_mas]["fecha"]=$ld_fecmov;
							$la_data_errbco_nota_cred_mas[$li_temp_errbco_nota_cred_mas]["numdoc"]=$ls_numdoc;
							$la_data_errbco_nota_cred_mas[$li_temp_errbco_nota_cred_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_nota_cred_mas[$li_temp_errbco_nota_cred_mas]["monto"]=$ldec_monto;	
							$li_temp_errbco_nota_cred_mas++;
							
							break;
						
						case "RE":
							$la_data_errbco_retiro_mas[$li_temp_errbco_retiro_mas]["fecha"]=$ld_fecmov;
							$la_data_errbco_retiro_mas[$li_temp_errbco_retiro_mas]["numdoc"]=$ls_numdoc;
							$la_data_errbco_retiro_mas[$li_temp_errbco_retiro_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_retiro_mas[$li_temp_errbco_retiro_mas]["monto"]=$ldec_monto;	
							$li_temp_errbco_retiro_mas++;
							break;
							
						case "DP":
							$la_data_errbco_deposito_mas[$li_temp_errbco_deposito_mas]["fecha"]=$ld_fecmov;
							$la_data_errbco_deposito_mas[$li_temp_errbco_deposito_mas]["numdoc"]=$ls_numdoc;
							$la_data_errbco_deposito_mas[$li_temp_errbco_deposito_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_deposito_mas[$li_temp_errbco_deposito_mas]["monto"]=$ldec_monto;	
							$li_temp_errbco_deposito_mas++;
							break;
					}
				}
				else{
					switch($ls_codope){
						case "CH":
							$la_data_errbco_cheque_menos[$li_temp_errbco_cheque_mas]["fecha"]=$ld_fecmov;
							$la_data_errbco_cheque_menos[$li_temp_errbco_cheque_mas]["numdoc"]=$ls_numdoc;
							$la_data_errbco_cheque_menos[$li_temp_errbco_cheque_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_cheque_menos[$li_temp_errbco_cheque_mas]["monto"]=$ldec_monto;					
							$li_temp_errbco_cheque_menos++;
							break;
						
						case "ND":
							$la_data_errbco_nota_deb_menos[$li_temp_errbco_nota_deb_menos]["fecha"]=$ld_fecmov;
							$la_data_errbco_nota_deb_menos[$li_temp_errbco_nota_deb_menos]["numdoc"]=$ls_numdoc;
							$la_data_errbco_nota_deb_menos[$li_temp_errbco_nota_deb_menos]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_nota_deb_menos[$li_temp_errbco_nota_deb_menos]["monto"]=$ldec_monto;					
							$li_temp_errbco_nota_deb_menos++;
							break;
						
						case "NC":
							$la_data_errbco_nota_cred_menos[$li_temp_errbco_nota_cred_menos]["fecha"]=$ld_fecmov;
							$la_data_errbco_nota_cred_menos[$li_temp_errbco_nota_cred_menos]["numdoc"]=$ls_numdoc;
							$la_data_errbco_nota_cred_menos[$li_temp_errbco_nota_cred_menos]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_nota_cred_menos[$li_temp_errbco_nota_cred_menos]["monto"]=$ldec_monto;	
							$li_temp_errbco_nota_cred_menos++;
							
							break;
						
						case "RE":
							$la_data_errbco_retiro_menos[$li_temp_errbco_retiro_menos]["fecha"]=$ld_fecmov;
							$la_data_errbco_retiro_menos[$li_temp_errbco_retiro_menos]["numdoc"]=$ls_numdoc;
							$la_data_errbco_retiro_menos[$li_temp_errbco_retiro_menos]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_retiro_menos[$li_temp_errbco_retiro_menos]["monto"]=$ldec_monto;	
							$li_temp_errbco_retiro_menos++;
							break;
							
						case "DP":
							$la_data_errbco_deposito_menos[$li_temp_errbco_deposito_menos]["fecha"]=$ld_fecmov;
							$la_data_errbco_deposito_menos[$li_temp_errbco_deposito_menos]["numdoc"]=$ls_numdoc;
							$la_data_errbco_deposito_menos[$li_temp_errbco_deposito_menos]["nombre"]=strtoupper($ls_nomproben);
							$la_data_errbco_deposito_menos[$li_temp_errbco_deposito_menos]["monto"]=$ldec_monto;	
							$li_temp_errbco_deposito_menos++;
							break;
						
					}
				}
			}
			else{//Operaciones scb_movbco
				if($ls_suma=='+'){//En caso que sean mas
					switch($ls_codope){
						case "CH":
							$la_data_cheque_mas[$li_temp_cheque_mas]["fecha"]=$ld_fecmov;
							$la_data_cheque_mas[$li_temp_cheque_mas]["numdoc"]=$ls_numdoc;
							$la_data_cheque_mas[$li_temp_cheque_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_cheque_mas[$li_temp_cheque_mas]["monto"]=$ldec_monto;					
							$li_temp_cheque_mas++;
							break;
							
						case "ND":
							if ($ls_estreglib=='A'){
								$la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["fecha"]=$ld_fecmov;
								$la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["numdoc"]=$ls_numdoc;
								$la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["nombre"]=strtoupper($ls_nomproben);
								$la_data_trans_no_regist_nd_mas[$li_temp_trans_no_regist_nd_mas]["monto"]=$ldec_monto;					
								$li_temp_trans_no_regist_nd_mas++;
							}
							else{
								$la_data_nota_deb_mas[$li_temp_nota_deb_mas]["fecha"]=$ld_fecmov;
								$la_data_nota_deb_mas[$li_temp_nota_deb_mas]["numdoc"]=$ls_numdoc;
								$la_data_nota_deb_mas[$li_temp_nota_deb_mas]["nombre"]=strtoupper($ls_nomproben);
								$la_data_nota_deb_mas[$li_temp_nota_deb_mas]["monto"]=$ldec_monto;	
								$li_temp_nota_deb_mas++;
							}
							break;
							
						case "NC":
							if ($ls_estreglib=='A'){
								$la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["fecha"]=$ld_fecmov;
								$la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["numdoc"]=$ls_numdoc;
								$la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["nombre"]=strtoupper($ls_nomproben);
								$la_data_trans_no_regist_nc_mas[$li_temp_trans_no_regist_nc_mas]["monto"]=$ldec_monto;	
								$li_temp_trans_no_regist_nc_mas++;
							}
							else{
								$la_data_nota_cred_mas[$li_temp_nota_cred_mas]["fecha"]=$ld_fecmov;
								$la_data_nota_cred_mas[$li_temp_nota_cred_mas]["numdoc"]=$ls_numdoc;
								$la_data_nota_cred_mas[$li_temp_nota_cred_mas]["nombre"]=strtoupper($ls_nomproben);
								$la_data_nota_cred_mas[$li_temp_nota_cred_mas]["monto"]=$ldec_monto;	
								$li_temp_nota_cred_mas++;
							}
							break;
						
						case "RE":
							$la_data_retiro_mas[$li_temp_retiro_mas]["fecha"]=$ld_fecmov;
							$la_data_retiro_mas[$li_temp_retiro_mas]["numdoc"]=$ls_numdoc;
							$la_data_retiro_mas[$li_temp_retiro_mas]["nombre"]=strtoupper($ls_nomproben);
							$la_data_retiro_mas[$li_temp_retiro_mas]["monto"]=$ldec_monto;	
							$li_temp_retiro_mas++;
							break;
							
						case "DP":
							if ($ls_estreglib=='A'){
								$la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["fecha"]=$ld_fecmov;
								$la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["numdoc"]=$ls_numdoc;
								$la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["nombre"]=strtoupper($ls_nomproben);
								$la_data_trans_no_regist_dp_mas[$li_temp_trans_no_regist_dp_mas]["monto"]=$ldec_monto;	
								$li_temp_trans_no_regist_dp_mas++;
							}
							else{
								$la_data_deposito_mas[$li_temp_deposito_mas]["fecha"]=$ld_fecmov;
								$la_data_deposito_mas[$li_temp_deposito_mas]["numdoc"]=$ls_numdoc;
								$la_data_deposito_mas[$li_temp_deposito_mas]["nombre"]=strtoupper($ls_nomproben);
								$la_data_deposito_mas[$li_temp_deposito_mas]["monto"]=$ldec_monto;	
								$li_temp_deposito_mas++;
							}
							break;
					}
				}
				else{//en caso de que sean menos
					switch($ls_codope){
						case "CH":
							$la_data_cheque_menos[$li_temp_cheque_menos]["fecha"]=$ld_fecmov;
							$la_data_cheque_menos[$li_temp_cheque_menos]["numdoc"]=$ls_numdoc;
							$la_data_cheque_menos[$li_temp_cheque_menos]["nombre"]=strtoupper($ls_nomproben);
							$la_data_cheque_menos[$li_temp_cheque_menos]["monto"]=$ldec_monto;	
							$li_temp_cheque_menos++;
							break;
							
						case "ND":
							if ($ls_estreglib=='A'){
								$la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["fecha"]=$ld_fecmov;
								$la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["numdoc"]=$ls_numdoc;
								$la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["nombre"]=strtoupper($ls_nomproben);
								$la_data_trans_no_regist_nd_menos[$li_temp_trans_no_regist_nd_menos]["monto"]=$ldec_monto;					
								$li_temp_trans_no_regist_nd_menos++;
							}
							else{
								$la_data_nota_deb_menos[$li_temp_nota_deb_menos]["fecha"]=$ld_fecmov;
								$la_data_nota_deb_menos[$li_temp_nota_deb_menos]["numdoc"]=$ls_numdoc;
								$la_data_nota_deb_menos[$li_temp_nota_deb_menos]["nombre"]=strtoupper($ls_nomproben);
								$la_data_nota_deb_menos[$li_temp_nota_deb_menos]["monto"]=$ldec_monto;	
								$li_temp_nota_deb_menos++;
							}
							break;
							
						case "NC":
							if ($ls_estreglib=='A'){
								$la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["fecha"]=$ld_fecmov;
								$la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["numdoc"]=$ls_numdoc;
								$la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["nombre"]=strtoupper($ls_nomproben);
								$la_data_trans_no_regist_nc_menos[$li_temp_trans_no_regist_nc_menos]["monto"]=$ldec_monto;	
								$li_temp_trans_no_regist_nc_menos++;
							}
							else{
								$la_data_nota_cred_menos[$li_temp_nota_cred_menos]["fecha"]=$ld_fecmov;
								$la_data_nota_cred_menos[$li_temp_nota_cred_menos]["numdoc"]=$ls_numdoc;
								$la_data_nota_cred_menos[$li_temp_nota_cred_menos]["nombre"]=strtoupper($ls_nomproben);
								$la_data_nota_cred_menos[$li_temp_nota_cred_menos]["monto"]=$ldec_monto;	
								$li_temp_nota_cred_menos++;
							}
							break;
						
						case "RE":
							$la_data_retiro_menos[$li_temp_retiro_menos]["fecha"]=$ld_fecmov;
							$la_data_retiro_menos[$li_temp_retiro_menos]["numdoc"]=$ls_numdoc;
							$la_data_retiro_menos[$li_temp_retiro_menos]["nombre"]=strtoupper($ls_nomproben);
							$la_data_retiro_menos[$li_temp_retiro_menos]["monto"]=$ldec_monto;	
							$li_temp_retiro_menos++;
							break;
						
						case "DP":
							if ($ls_estreglib=='A'){
								$la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["fecha"]=$ld_fecmov;
								$la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["numdoc"]=$ls_numdoc;
								$la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["nombre"]=strtoupper($ls_nomproben);
								$la_data_trans_no_regist_dp_menos[$li_temp_trans_no_regist_dp_menos]["monto"]=$ldec_monto;	
								$li_temp_trans_no_regist_dp_menos++;
							}
							else{
								$la_data_deposito_menos[$li_temp_deposito_menos]["fecha"]=$ld_fecmov;
								$la_data_deposito_menos[$li_temp_deposito_menos]["numdoc"]=$ls_numdoc;
								$la_data_deposito_menos[$li_temp_deposito_menos]["nombre"]=strtoupper($ls_nomproben);
								$la_data_deposito_menos[$li_temp_deposito_menos]["monto"]=$ldec_monto;	
								$li_temp_deposito_menos++;
							}
							break;
					}
				}
			}
		}
		/*$la_data=array();
		$la_data=array(array('nombre'=>'CHEQUES','tipo'=>'MAS','data'=>$la_data_cheque_mas),
					   array('nombre'=>'CHEQUES','tipo'=>'MENOS','data'=>$la_data_cheque_menos),
					   array('nombre'=>'NOTAS DE DEBITO','tipo'=>'MAS','data'=>$la_data_nota_deb_mas),
					   array('nombre'=>'NOTAS DE DEBITO','tipo'=>'MENOS','data'=>$la_data_nota_deb_menos),
					   array('nombre'=>'NOTAS DE CREDITO','tipo'=>'MAS','data'=>$la_data_nota_cred_mas),
					   array('nombre'=>'NOTAS DE CREDITO','tipo'=>'MENOS','data'=>$la_data_nota_cred_menos),
					   array('nombre'=>'RETIROS','tipo'=>'MAS','data'=>$la_data_retiro_mas),
					   array('nombre'=>'RETIROS','tipo'=>'MENOS','data'=>$la_data_retiro_menos),
					   array('nombre'=>'DEPOSITOS','tipo'=>'MAS','data'=>$la_data_deposito_mas),
					   array('nombre'=>'DEPOSITOS','tipo'=>'MENOS','data'=>$la_data_deposito_menos),
					   array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO ND','tipo'=>'MAS','data'=>$la_data_trans_no_regist_nd_mas),
					   array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO ND','tipo'=>'MENOS','data'=>$la_data_trans_no_regist_nd_menos),
					   array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO NC','tipo'=>'MAS','data'=>$la_data_trans_no_regist_nc_mas),
					   array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO NC','tipo'=>'MENOS','data'=>$la_data_trans_no_regist_nc_menos),
					   array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO DP','tipo'=>'MAS','data'=>$la_data_trans_no_regist_dp_mas),
					   array('nombre'=>'TRANS. NO REGISTRADAS EN LIBRO DP','tipo'=>'MENOS','data'=>$la_data_trans_no_regist_dp_menos));*/
		
		$io_pdf=uf_print_detalle($la_data_cheque_mas,$la_data_cheque_menos,$la_data_nota_deb_mas,$la_data_nota_deb_menos,
		$la_data_nota_cred_mas,$la_data_nota_cred_menos,$la_data_retiro_mas,$la_data_retiro_menos,$la_data_deposito_mas,
		$la_data_deposito_menos,$la_data_trans_no_regist_nd_mas,$la_data_trans_no_regist_nd_menos,
		$la_data_trans_no_regist_nc_mas,$la_data_trans_no_regist_nc_menos,$la_data_trans_no_regist_dp_mas,
		$la_data_trans_no_regist_dp_menos,$ld_fechasta,number_format($ldec_salsegbco,2,",","."),$ldec_salseglib,
		$la_data_errbco_cheque_mas,$la_data_errbco_cheque_menos,$la_data_errbco_nota_deb_mas,$la_data_errbco_nota_deb_menos,
		$la_data_errbco_nota_cred_mas,$la_data_errbco_nota_cred_menos,$la_data_errbco_retiro_mas,$la_data_errbco_retiro_menos,
		$la_data_errbco_deposito_mas,$la_data_errbco_deposito_menos,$io_pdf); // Imprimimos el detalle	
	//--------------------------
		$io_pdf=uf_print_detalle_libro($la_data_cheque_mas,$la_data_cheque_menos,$la_data_nota_deb_mas,$la_data_nota_deb_menos,
		$la_data_nota_cred_mas,$la_data_nota_cred_menos,$la_data_retiro_mas,$la_data_retiro_menos,$la_data_deposito_mas,
		$la_data_deposito_menos,$la_data_trans_no_regist_nd_mas,$la_data_trans_no_regist_nd_menos,
		$la_data_trans_no_regist_nc_mas,$la_data_trans_no_regist_nc_menos,$la_data_trans_no_regist_dp_mas,
		$la_data_trans_no_regist_dp_menos,$ld_fechasta,number_format($ldec_salsegbco,2,",","."),$ldec_salseglib,
		$la_data_errbco_cheque_mas,$la_data_errbco_cheque_menos,$la_data_errbco_nota_deb_mas,$la_data_errbco_nota_deb_menos,
		$la_data_errbco_nota_cred_mas,$la_data_errbco_nota_cred_menos,$la_data_errbco_retiro_mas,$la_data_errbco_retiro_menos,
		$la_data_errbco_deposito_mas,$la_data_errbco_deposito_menos,$io_pdf); // Imprimimos el detalle
	//--------------------------
		uf_print_autorizacion($io_pdf);
	}
	$io_pdf->ezStream();
?> 