/***********************************************************************************
* @Proceso para la apertura del ejercicio contable.
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var panel      = '';
var pantalla   = 'aperturaejercicio';
var actualizar = false;
var rutaProceso  =  '../../controlador/apr/sigesp_ctr_apr_apertura_ejercicio.php'; 
barraherramienta    = true;
Ext.onReady
(
	function()
	{
		Ext.QuickTips.init();
		Ext.Ajax.timeout=3600000;
				
		//componentes del formulario
		Xpos = ((screen.width/2)-(450/2)); 
		Ypos = ((screen.height/2)-(450/2));
		
		panel = new Ext.FormPanel({
			title: 'Apertura del Ejercicio Contable',
			bodyStyle:'padding:5px 5px 0px',
			width:400,
			tbar: [],
			style:'position:absolute;top:'+Ypos+'px;left:'+Xpos+'px',
			items:[{
				xtype:'fieldset',
				id:'fsejercicio',				
				autoHeight:true,
				autoWidth:true,
				cls :'fondo',
				items:[{								
				}]
			}]	
		})
		panel.render(document.body);
		
	}
)
	
	
/***********************************************************************************
* @Función para limpiar los campos.
* @parametros: 
* @retorno:
* @fecha de creación: 10/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function irCancelar()
	{
		
	}	
	
	
/***********************************************************************************
* @Función para procesar la apertura del ejercicio contable.
* @parametros: 
* @retorno:
* @fecha de creación: 15/12/2008
* @autor: Ing. Gusmary Balza.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/				
	function irProcesar()
	{
		obtenerMensaje('procesar','','Transfiriendo Datos');				
			
		var objdata ={
			'operacion': 'Procesar', 
			'sistema': sistema,
			'vista': vista
		};	
		objdata=Ext.util.JSON.encode(objdata);	
		parametros = 'objdata='+objdata;
		Ext.Ajax.request({
		url : rutaProceso,
		params : parametros,
		method: 'POST',
		success: function ( resultado, request ) 
		{ 
			datos = resultado.responseText;
			Ext.Msg.hide();						
			var datajson = Ext.util.JSON.decode(datos);	
			if(datajson.raiz.valido==true)
			{								
				Ext.Msg.alert('Mensaje', datajson.raiz.mensaje);
			}
			else
			{
				Ext.Msg.alert('Error', datajson.raiz.mensaje);
			}
		},
        failure: function ( resultado, request)
		{ 
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error', resultado.responseText); 
        }
        });   
	}


/***********************************************************************************
* @Función para Descargar los archivos generados pór el módulo de apertura
* @parametros: 
* @retorno:
* @fecha de creación: 27/10/2008
* @autor: Ing. Yesenia Moreno de Lang.
************************************************************************************
* @fecha modificación:
* @descripción:
* @autor:
***********************************************************************************/	
	function irDescargar()
	{
		objCatDescarga = new catalogoDescarga();
		objCatDescarga.mostrarCatalogo();
	}
		