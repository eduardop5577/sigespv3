/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var actualizar = false;
var rutaProceso  =  '../../controlador/sfd/sigesp_ctr_sfd_verificar.php'; 
barraherramienta    = true;
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.Ajax.timeout=36000000000;
		Ext.form.Field.prototype.msgTarget = 'side';		
		
		//componentes del formulario
		Xpos = ((screen.width/2)-(450/2)); 
		Ypos = ((screen.height/2)-(450/2));
	}
)
	
	
/***********************************************************************************
* @Funci�n para limpiar los campos.
* @parametros: 
* @retorno:
* @fecha de creaci�n: 10/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function irCancelar()
	{
		
	}	
	
/***********************************************************************************
* @Funci�n para procesar el movimiento inicial de existencias de inventario.
* @parametros: 
* @retorno:
* @fecha de creaci�n: 15/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/				
	function irProcesar()
	{

	}

/***********************************************************************************
* @Funci�n para Descargar los archivos generados p�r el m�dulo de apertura
* @parametros: 
* @retorno:
* @fecha de creaci�n: 27/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificaci�n:
* @descripci�n:
* @autor:
***********************************************************************************/	
	function irDescargar()
	{
		
	}

	