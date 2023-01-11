/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al Registro de Datos para el Servidor de Correo de la Institucion. 
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

barraherramienta    = true;
var formulario          = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var pantalla    		= 'sigesp_cfg_d_correo.php'; 							// Variable que contiene el nombre f�sico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_correo.php'; 	// Ruta del Controlador de la Pantalla
var banderaGrabar 		= true;												// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;												// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;												// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar=null;
var banderaCatalogo = 'estandar';
var banderaImprimir = false;

var Campos =new Array(
						['codemp',''],
						['msjenvio',''], 
						['msjsmtp',''], 
						['msjservidor',''],
						['msjpuerto',''],
						['msjhtml',''],
						['msjremitente','']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

Ext.onReady(function(){
	 Ext.QuickTips.init();
	 Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 
	 Xpos = ((screen.width/2)-(600/2));
	 Ypos = ((screen.height/2)-(600/2));	
     var formulario = new Ext.form.FormPanel({
    	   	 title:"Definici&#243;n de Correo",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 600,
    		 height: 270,
    		 labelPad: 10,
    		 items:[{
				        xtype:"hidden",
				        name:"codemp",
				        id:"codemp",
						value:''
			        },
			        {
				        layout:"form",
						border:false,
						style: "margin-top:30px;margin-left:25px;",
						labelWidth:100,
						items:[{
								xtype: "radiogroup",
								fieldLabel: "Env&#237;o de correo",
								labelSeparator:":",
								columns: [150,150],
								id:"msjenvio",
								items: [
										{boxLabel: 'Si', name: 'correo',inputValue: 1},
										{boxLabel: 'No', name: 'correo', inputValue: 0, checked:true}
	            					   ]
	        				  },
	        				  {
								xtype: "radiogroup",
								fieldLabel: "Servidor SMTP",
								labelSeparator:":",
								columns: [150,150],
								id:"msjsmtp",
								items: [
										{boxLabel: 'S&#237;', name: 'smtp',inputValue: 1},
										{boxLabel: 'No', name: 'smtp', inputValue: 0, checked:true}
	            					   ]
	        				  },
					          {
						        xtype:"textfield",
						        fieldLabel:"Nombre del Servidor",
						        labelWidth:60,
						        name:"servidor",
						        id:"msjservidor",
						        width:400,
								autoCreate: {tag: 'input', type: 'text', size: '60', autocomplete: 'off', maxlength: '60', onkeypress:"return keyRestrict(event,'1234567890abcdefghijklmnopqrstuvwxyz.,-')"}
				        	  },
					          {
						        xtype:"numberfield",
						        fieldLabel:"Puerto",
						        labelWidth:60,
						        name:"denominacion",
						        id:"msjpuerto",
						        width:120,
								autoCreate: {tag: 'input', type: 'text', size: '12', autocomplete: 'off', maxlength: '10'},
								allowDecimals:false,
								allowNegative:false
					          },
	        				  {
								xtype: "radiogroup",
								fieldLabel: "Mensaje HTML",
								labelSeparator:":",
								columns: [150,150],
								id:"msjhtml",
								items: [
										{boxLabel: 'S&#237;', name: 'html',inputValue: 1},
										{boxLabel: 'No', name: 'html', inputValue: 0, checked:true}
	            					   ]
	        				  },
					          {
						        xtype:"textfield",
						        fieldLabel:"Direcci&#243;n de correo del remitente",
						        labelWidth:60,
						        name:"direccion",
						        id:"msjremitente",
						        width:400,
								autoCreate: {tag: 'input', type: 'text', size: '60', autocomplete: 'off', maxlength: '60', onkeypress:"return keyRestrict(event,'1234567890abcdefghijklmnopqrstuvwxyz.-_@')"},
	        				    vtype:'email'
				        	  }]
			        		
			        }]
    		});
     formulario.render("formulario_correo");
     irNuevo();
});

function irNuevo()
{
	var objetoJson ={
			"oper": 'cargarcorreo'
		}
		
		ObjSon=Ext.util.JSON.encode(objetoJson);
		parametros = 'ObjSon='+ObjSon;
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_correo.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var objetoCorreo = eval('(' + datos + ')');
			if(objetoCorreo != '')
			{
				if(Ext.getCmp('msjenvio').items.items[0].inputValue == objetoCorreo.raiz[0]['msjenvio'])
				{
					Ext.getCmp('msjenvio').items.items[0].setValue(true);
					Ext.getCmp('msjenvio').items.items[1].setValue(false);
				}
				else
				{
					Ext.getCmp('msjenvio').items.items[0].setValue(false);
					Ext.getCmp('msjenvio').items.items[1].setValue(true);
				}
				
				if(Ext.getCmp('msjsmtp').items.items[0].inputValue == objetoCorreo.raiz[0]['msjsmtp'])
				{
					Ext.getCmp('msjsmtp').items.items[0].setValue(true);
					Ext.getCmp('msjsmtp').items.items[1].setValue(false);
				}
				else
				{
					Ext.getCmp('msjsmtp').items.items[0].setValue(false);
					Ext.getCmp('msjsmtp').items.items[1].setValue(true);
				}
				
				if(Ext.getCmp('msjhtml').items.items[0].inputValue == objetoCorreo.raiz[0]['msjhtml'])
				{
					Ext.getCmp('msjhtml').items.items[0].setValue(true);
					Ext.getCmp('msjhtml').items.items[1].setValue(false);
				}
				else
				{
					Ext.getCmp('msjhtml').items.items[0].setValue(false);
					Ext.getCmp('msjhtml').items.items[1].setValue(true);
				}
				Ext.getCmp('codemp').setValue(objetoCorreo.raiz[0]['codemp']);
				Ext.getCmp('msjservidor').setValue(objetoCorreo.raiz[0]['msjservidor']);
				Ext.getCmp('msjpuerto').setValue(objetoCorreo.raiz[0]['msjpuerto']);
				Ext.getCmp('msjremitente').setValue(objetoCorreo.raiz[0]['msjremitente']);
			}
		}	
	})
}

function irGuardar()
{
	msjenvio = msjsmtp = msjhtml = 0;
	
	if(Ext.getCmp('msjenvio').items.items[0].checked)
	{
		msjenvio = Ext.getCmp('msjenvio').items.items[0].inputValue;
		if(Ext.getCmp('msjremitente').getValue()=='')
		{
			Ext.Msg.alert('Error', 'Debe indicar la direccion de correo del remitente');
			return false;
		}
	}
	else
	{
		msjenvio = Ext.getCmp('msjenvio').items.items[1].inputValue;
	}
	
	if(Ext.getCmp('msjsmtp').items.items[0].checked)
	{
		msjsmtp = Ext.getCmp('msjsmtp').items.items[0].inputValue;
		if(Ext.getCmp('msjservidor').getValue()=='')
		{
			Ext.Msg.alert('Error', 'Debe indicar el nombre del servidor');
			return false;
		}
		else if(Ext.getCmp('msjpuerto').getValue()=='')
		{
			Ext.Msg.alert('Error', 'Debe indicar el puerto del servidor');
			return false;
		}
	}
	else
	{
		msjsmtp = Ext.getCmp('msjsmtp').items.items[1].inputValue;
	}
	
	if(Ext.getCmp('msjhtml').items.items[0].checked)
	{
		msjhtml = Ext.getCmp('msjhtml').items.items[0].inputValue;
	}
	else
	{
		msjhtml = Ext.getCmp('msjhtml').items.items[1].inputValue;
	}
	
	if(Ext.getCmp('msjpuerto').getValue() == '')
	{
		Ext.getCmp('msjpuerto').setValue(0);
	}
	
	obtenerMensaje('procesar','','Guardando Datos');
	var arregloJson = "{'oper':'incluir','codmenu':"+codmenu+",'msjenvio':"+msjenvio+",'msjsmtp':"+msjsmtp+",'msjservidor':'"+Ext.getCmp('msjservidor').getValue()+"','msjpuerto':"+Ext.getCmp('msjpuerto').getValue()+",'msjhtml':"+msjhtml+",'msjremitente':'"+Ext.getCmp('msjremitente').getValue()+"'}";
	correo= eval('(' + arregloJson + ')');
	ObjSon=Ext.util.JSON.encode(correo);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : ruta,
	params : parametros,
	method: 'POST',
	success: function ( resultado, request )
	{ 
		Ext.Msg.hide();
		datos = resultado.responseText;
		registro = datos.split("|");
		codresultado = registro[1];
		switch(codresultado)
		{
			case '0': 
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Ha ocurrido un error, vuelva a intentar',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.ERROR
								});
				    break;
			case '1': 
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro actualizado con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
					});
				    break;
				    
			case '2': 
					Ext.Msg.show({
									title:'Mensaje',
									msg: 'Registro incluido con &#233;xito',
									buttons: Ext.Msg.OK,
									icon: Ext.MessageBox.INFO
					});
				    break;
		
		}
	  },
	  failure: function ( result, request)
	  { 
			Ext.Msg.hide();
			Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo'); 
	  } 
});

}

function irCancelar()
{
	irNuevo();
}