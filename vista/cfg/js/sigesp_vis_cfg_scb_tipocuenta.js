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

barraherramienta    = true; 
 
var Actualizar=null;
var ruta 		='../../controlador/cfg/sigesp_ctr_cfg_scb_tipocuenta.php'; //ruta del controlador
var banderaGrabar 		= false;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= false;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var banderaCatalogo = 'estandar';
var banderaImprimir = false;
var formulario = '';
var Campos =new Array(
		
	        ['codtipcta','novacio|'],
	        ['nomtipcta','novacio|']
	        
	    )



Ext.onReady(function(){
	
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	formulario = new Ext.FormPanel({
	applyTo: 'formulario_tipocuenta',
	width: 700,
	height: 150,
	title: 'Definici&#243;n Tipo de Cuenta',
	frame:true,
	labelWidth:150,
	defaultType: 'textfield',
	style:'position:absolute;margin-left:163px;margin-top:150px;',
	items: [{
		xtype: 'textfield',
		fieldLabel: 'C&#243;digo',
		disabled:true,
		name: 'codigo',
		id: 'codtipcta',
		autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
		width:50
		},{
		xtype: 'textfield',
		fieldLabel: 'Descripci&#243;n',
		autoCreate: {tag: 'input', type: 'text', maxlength: 30, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
		name: 'Descripci&#243;n',
		id: 'nomtipcta',		
		width: 400
		
		}]
		
		
	});
	
});

function irCancelar()
{
	irNuevo();
}

function irBuscar()
{
	catalogoTipoCuenta();
}

function irNuevo()
{
	limpiarCampos();
	var myJSONObject ={
		"oper":"nuevo"
	};
	
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_scb_tipocuenta.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codtipcta').setValue(codigo);
		}
	}	
	})
}

function irGuardar()
{
	var mensajeexito = 'Registro <operacion> con &#233;xito';
    var mensajeerror = 'Error al <operacion> registro';
	var cadjson = '';

	if(Actualizar == null)
	{
		operacion='incluir';
	    mensajeexito = mensajeexito.replace('<operacion>','incluido');
	    mensajeerror = mensajeerror.replace('<operacion>','incluir');
    } 
    else
	{
		operacion='actualizar';
    	mensajeexito = mensajeexito.replace('<operacion>','actualizado');
    	mensajeerror = mensajeerror.replace('<operacion>','actualizar');
    }
	try
	{
		if(validarObjetos2()==false)
		{
			return false;
		}
		else
		{
			cadjson=cargarJson(operacion);
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + cadjson;
				obtenerMensaje('procesar','','Guardando Datos');
				Ext.Ajax.request({
					url : ruta,
					params : parametros,
					method: 'POST',	
					success: function ( resultad, request )
					{ 
						Ext.Msg.hide();
						var datos = resultad.responseText;
						var respuesta = datos.split("|");
						if (respuesta[1] == '1')
						{
							Ext.MessageBox.alert('mensaje','Registro '+mensajeexito+ '');
							Actualizar=null;
						}
						else
						{
							if(respuesta[0]!='')
							{
								Ext.MessageBox.alert('Error', respuesta[0]);
							}
							else
							{
								Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensajeerror);
							}
						}
						limpiarFormulario(formulario);
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', resultad.responseText);
						mascara.hide();
					}
				});
			}
		}
	}
	catch(e)
	{
	}
}

function irEliminar()
{
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			var	cadjson=cargarJson('eliminar');
			try
			{
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + cadjson;
					obtenerMensaje('procesar','','Eiminando Datos');
					Ext.Ajax.request({
						url : ruta,
						params : parametros,
						method: 'POST',	
						success: function ( resultad, request )
						{ 
							Ext.Msg.hide();
							var datos = resultad.responseText;
							var Registros = datos.split("|");
						 	if (Registros[1] == '1')
							{
						 		Ext.MessageBox.alert('mensaje','Registro Eliminado con &#233;xito');
								Actualizar=null;
							}
							else
							{
								if(Registros[1]=='-9')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
							  	}
								else
								{
									if(Registros[1]=='-8')
									{
										Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
									}
									else
									{
										Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
									}
								}
							}
							limpiarFormulario(formulario);
						},
						failure: function (result, request)
						{ 
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error', resultad.responseText);
						}
					});
				}
			}
			catch(e)
			{
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminarRegistro);
}
