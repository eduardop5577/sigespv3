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
var formulario =null;
ruta ='../../controlador/cfg/sigesp_ctr_cfg_spg_fuentefinanciamiento.php'; //ruta del controlador

var Campos =new Array(

		['codfuefin','novacio|'],
		['denfuefin','novacio|'],
		['expfuefin','novacio|']
)



Ext.onReady(function(){

	Ext.QuickTips.init();
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	formulario = new Ext.FormPanel({
		applyTo: 'formulario_fuentefinanciamiento',
		width: 700,
		height: 150,
		title: 'Fuente Financiamiento',
		frame:true,
		labelWidth:200,
		defaults: {width: 100},
		defaultType: 'textfield',
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		items: [{
			xtype: 'textfield',
			fieldLabel: 'C&#243;digo',
			name: 'codigo',
			id: 'codfuefin',
			disabled:true,
			autoCreate: {tag: 'input', type: 'text', size: '2', autocomplete: 'off', maxlength: '2'},
			width: 30
		},{
			xtype: 'textfield',
			fieldLabel: 'Denominaci&#243;n',
			name: 'denominacion',
			autoCreate: {tag: 'input', type: 'text', maxlength: '80', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
			id: 'denfuefin',
			width: 400
		},{
			xtype: 'textfield',
			fieldLabel: 'Explicaci&#243;n',
			autoCreate: {tag: 'input', type: 'text', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
			name: 'explicacion',
			id: 'expfuefin',		
			width: 400
		}]
	});

});

function irNuevo()
{
	var myJSONObject ={
			"oper":"buscarcodigo" 
	};

	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
		var datos = result.responseText;
		var	resultado = datos.split("|");
		var codigo = resultado[1];
		if (codigo != "") {
			Ext.getCmp('codfuefin').setValue(codigo);
			Ext.getCmp('denfuefin').setValue('');
			Ext.getCmp('expfuefin').setValue('');
		}
	},
	failure: function ( result, request){ 
		Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
	}
	});		

}

function irBuscar()
{
	mostrar_catalogo();
}

function irCancelar()
{
	irNuevo();
}

function irGuardar()
{
	if(Actualizar==null)
	{
		operacion='incluir';
		mensaje='incluido';
	}
	else
	{	
		operacion='actualizar';
		mensaje='modificado';			
	}
	if(validarObjetos2()==false)
	{
		return false;
	}
	else
	{
		var Json    = cargarJson(operacion);
		try
		{
			var objjson = Ext.util.JSON.decode(Json);
			if (typeof(objjson) == 'object')
			{
				var parametros = 'ObjSon=' + Json;
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
							Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
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
								Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensaje);
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
		catch(e)
		{
			//no imprimo la excepcion
		}
	}
}

function irEliminar()
{
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			var Json    = cargarJson('eliminar');
			try
			{
				var objjson = Ext.util.JSON.decode(Json);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + Json;
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
				//no imprimo la excepcion
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminarRegistro);
}