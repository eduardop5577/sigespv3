<?php
/***********************************************************************************
* @fecha de modificacion: 28/07/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

session_start();
require_once('../../base/librerias/php/general/sigesp_lib_funciones.php');
require_once('../../base/librerias/php/tcpdf/tcpdf.php');
require_once('../../base/librerias/php/fpdi-1.6.1/fpdi.php');
$sessionvalida = validarSession();
$_SESSION['session_activa'] = time();
$hoy = date("H-i-s");  
if ((isset($_FILES['archivopdf'])) || (isset($_FILES['archivocrt'])) || (isset($_FILES['archivokey'])))
{
	if ((($_FILES['archivopdf']['error']) && ($_FILES['archivocrt']['error']) && ($_FILES['archivokey']['error'])) == UPLOAD_ERR_OK)   
	{     
		$orientacion = $_POST["orientacion"];
		$tamanopagina = $_POST["tamanopagina"];
		$archivoPDF = $_FILES['archivopdf']['name'];
		$archivoCert = $_FILES['archivocrt']['name'];
		$archivoClave = $_FILES['archivokey']['name'];

		$pos =strpos(rtrim($archivoPDF),".",0);
		$extensionPDF=substr(rtrim($archivoPDF),$pos+1); 
		$pos =strpos(rtrim($archivoCert),".",0);
		$extensionCert=substr(rtrim($archivoCert),$pos+1); 
		$pos =strpos(rtrim($archivoClave),".",0);
		$extensionClave=substr(rtrim($archivoClave),$pos+1); 

		//- El combo envia la etiqueta por lo tanto la transformamos en l(horizontal) o p(vertical)
		//- Ambas coordenadas
		//- Estas coordenadas son las correctas para tamaño de hoja carta*/
		$AuxOrientacion='p';   
		$x=145;
		$y=258;
		if($orientacion=="Horizontal")
		{
			$AuxOrientacion='l';
			$x=270;
			$y=170;
		}
		//Combo de tamaño de página, el metodo post envia el valor de la etiqueta
		$AuxTamanno="";
		if($tamanopagina=='Carta')
		{
			$AuxTamanno='letter';
		}
		else
		{ 
			if($tamanopagina=='Oficio')
			{
				$AuxTamanno="legal";
			}
			else
			{
				$AuxTamanno=$tamanopagina;
			}
		}
		//Comprueba que no se puedan seleccionar mas de 1 archivo ademas de documentos distintos a extensión .pdf
		if (($extensionPDF!='pdf')||($extensionCert!='crt')||($extensionClave!='key'))
		{
			echo '{
			"success": false,
			"errorMsg": "Archivos inconsistentes."
			}';
		}
		else
		{  
			//Arreglo de información de la firma digital.
			$info = array('Name' => 'Firma Digital','Location' => 'Lara','Reason' => 'Aprobación','ContactInfo' => 'www.sigesp.com');
			
			//Se almacena en archivos temporales el certificado digital y su clave privada.
			$certificado= 'file://'.$_FILES['archivocrt']['tmp_name'];   
			$clave_privada='file://'.$_FILES['archivokey']['tmp_name'];   

			$nombre=$_FILES['archivopdf']['name'];
			//Se cre el objeto FPDI donde se genera el nuevo documento firmado.
			$pdf = new FPDI($AuxOrientacion, 'mm', $AuxTamanno); 
			
			//Se genera la plantilla del documento existente.
			$pages = $pdf->setSourceFile($_FILES['archivopdf']['tmp_name']);
			//Verifica que la clave pertenece al certificado digital seleccionado
			if((openssl_x509_check_private_key ( $certificado ,$clave_privada))==true)
			{   
				if($_POST["visibilidad"]=="Si")
				{ //Ciclo en el cual el nuevo documento recibe la plantilla y la firma.           
					for ($i = 1; $i <= $pages; $i++)
					{
						$pdf->AddPage();
						$page = $pdf->importPage($i);
						$pdf->useTemplate($page, 0, 0);
						$pdf->SetFont('helvetica','', 8);
						$pdf->SetXY(25,230);
						$pdf->setSignature($certificado, $clave_privada, '', '', 1, $info);      
						$pdf->setSignatureAppearance($x, $y, 15, 15);
						$pdf->Image('../../base/imagenes/certificado.png', $x, $y, 15, 15, 'PNG');
					}
				}
				else
				{
					for ($i = 1; $i <= $pages; $i++)
					{
						$pdf->AddPage();
						$page = $pdf->importPage($i);
						$pdf->useTemplate($page, 0, 0);
						$pdf->SetFont('helvetica','', 8);
						$pdf->SetXY(25,230);
						$pdf->setSignature($certificado, $clave_privada, '', '', 1, $info);      
					}  
				}
				$nombreDoc=$hoy."_firmado_".$nombre;
				//Salida del documento en en la ruta establecida, primer parámetro es el nombre del archivo, el
				//    segundo parámetro 'f' descarga en ruta establecida ó "D" descarga forzada.
				$pdf->Output(__DIR__.'/../../vista/sfd/descargas/'.$nombreDoc, 'f');
				echo '{
				"success": true,
				"errorMsg":""
				}';
			}
			else
			{
				echo '{
				"success": false,
				"errorMsg":"No coinciden el certificado y clave."
				}';
			}        
		}
	}
	else
	{
		echo '{
		"success": false,
		"errorMsg":"Error al cargar  archivos."
		}';
	} 
}
else
{
	echo '{
	"success": false,
	"errorMsg":"Los Archivos estan Vacios."
	}';
} 
?>