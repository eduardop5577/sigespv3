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
var ruta 				= '../../controlador/cfg/sigesp_ctr_cfg_scb_tipocolocacion.php'; // Ruta del controlador
var banderaGrabar 		= false;													// Indicador si se posee un Metodo de Guardar distinto al Original de funciones.js
var banderaEliminar		= true;													// Indicador si se posee un Metodo de Eliminar distinto al Original de funciones.js
var banderaNuevo		= true;													// Indicador si se posee un Metodo Nuevo distinto al Original de funciones.js
var Actualizar=null;
var banderaCatalogo = 'estandar';
var banderaImprimir = false;
var panel = '';
var Campos =new Array(
		
	        ['codtipcol','novacio|'],
	        ['nomtipcol','novacio|']
	    );



Ext.onReady(function(){
	
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	var Xpos = ((screen.width/2)-(600/2));
	panel = new Ext.FormPanel({
	applyTo: 'formulario_tipocolocacion',
	width: 600,
	height: 100,
	title: 'Tipo de Colocaci&#243;n',
	frame:true,
	labelWidth:120,
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:150px;',
	items: [{
				xtype: 'textfield',
				fieldLabel: 'C&#243;digo',
				name: 'codigo',
				id: 'codtipcol',
				maxLength: 3,
				width:40,
				disabled:true,
				style:'margin-left:20px;',
				autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'}
			},
			{
				xtype: 'textfield',
				fieldLabel: 'Denominaci&#243;n',
				name: 'denominacion',
				id: 'nomtipcol',
				autoCreate: {tag: 'input', type: 'text', maxlength: 60, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
				style:'margin-left:20px;',
				width: 400
			}]
	});	
});

function irCancelar()
{
	irNuevo();
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
	url : ruta,
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codtipcol').setValue(codigo);
		}
	}	
	})
}

function irBuscar()
{
	mostrar_catalogo();
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
						limpiarFormulario(panel);
					},
					failure: function (result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', resultad.responseText);
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
	if(Actualizar)
	{
		function respuesta(btn)
		{
			if(btn=='yes')
			{
				obtenerMensaje('procesar','','Eliminando Datos');
				Json=cargarJson('eliminar');
				Ob=Ext.util.JSON.decode(Json);
				ObjSon=JSON.stringify(Ob);
				parametros = 'ObjSon='+ObjSon;
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
							Ext.MessageBox.alert('mensaje','Registro eliminado con &#233;xito');
							limpiarCampos();
							Actualizar=null;
						}
						else
						{
							if(respuesta[1]=='-9')
							{
								Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, esta vinculado con otros registros');
							}
							else
							{
								if(respuesta[1]=='-8')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
								}
								else
								{
									Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								}
							}
						}
					},
					failure: function ( result, request)
					{ 
						Ext.Msg.hide();
						Ext.MessageBox.alert('Error', result.responseText); 
					} 
				});
			}
		};
		Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta);
	}
	else
	{
		Ext.Msg.show({
			title:'Mensaje',
			msg: 'Opci&#243;n inv&#225;lida, el registro debe estar previamente guardado, verifique por favor',
			buttons: Ext.Msg.OK,
			icon: Ext.MessageBox.ERROR
		});  
	}	

}
