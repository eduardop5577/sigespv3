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
var ruta 		='../../controlador/cfg/sigesp_ctr_cfg_pais.php'; //ruta del controlador

var Campos =new Array(
		['codpai','novacio|'],
		['despai','novacio|']
);

Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	var formulario = new Ext.FormPanel({
		applyTo: 'formulario_paises',
		width: 700,
		height: 150,
		title: 'Pa&#237;ses',
		frame:true,
		labelWidth:200,
		defaults: {width: 100},
		defaultType: 'textfield',
		style:'position:absolute;margin-left:163px;margin-top:150px;',
		items: [{
			xtype: 'textfield',
			fieldLabel: 'C&#243;digo',
			name: 'codigo',
			id: 'codpai',
			autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
			width:30,
			readOnly: true
		},{
			xtype: 'textfield',
			fieldLabel: 'Descripci&#243;n',
			name: 'descripcion',
			id: 'despai',
			autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '50', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ., ');"},
			width: 400
		}]
	});
});

function irNuevo()
{
	limpiarCampos();
	Ext.getCmp('codpai').enable();
	var myJSONObject ={
			"oper":"nuevo"
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_pais.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
			datos = result.responseText;
			var codigo = eval('(' + datos + ')');
			if(codigo != "")
			{
				Ext.getCmp('codpai').setValue(codigo);
			}
		}	
	})
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
		Json=cargarJson(operacion);
		myJSONObject=Ext.util.JSON.decode(Json);	
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon;
		obtenerMensaje('procesar','','Guardando Datos');
		Ext.Ajax.request({
			url : ruta,
			params : parametros,
			method: 'POST',	
			success: function ( resultad, request ) 
			{ 
				Ext.Msg.hide();
				datos = resultad.responseText;
				var respuesta = datos.split("|");
				if (respuesta[1] == '1')
				{
					Ext.MessageBox.alert('mensaje','Registro '+mensaje + ' con &#233;xito');
					limpiarCampos();
					Ext.getCmp('codpai').enable();
					Actualizar=null;
				}
				else
				{
					Ext.MessageBox.alert('Error', respuesta[0]);
				}
			},
			failure: function (resultad, request)
			{ 
				Ext.Msg.hide();
				Ext.MessageBox.alert('Error', resultad.responseText);
			}
		});
	}
}

function verificarPrefijo()
{

	var myJSONObject ={
			"oper":"verificarcodigo",
			"codpai":Ext.getCmp('codpai').getValue()
	};

	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_pais.php',
		params : parametros,
		method: 'POST',
		success: function ( result, request) 
		{ 
		datos = result.responseText;
		var respuesta = eval('(' + datos + ')');
		pais = Ext.getCmp('codpai').getValue();
		if((respuesta.existe)&&(Actualizar==null))
		{
			Ext.Msg.show({
				title:'Mensaje',
				msg: 'Ya existe un pais con c&#243;digo '+pais+', debe indicar uno distinto',
				buttons: Ext.Msg.OK,
				fn: '',
				animEl: 'elId',
				icon: Ext.MessageBox.ERROR
			});
			Ext.getCmp('codpai').setValue('');
		}
		}	
	});
}

function irCancelar()
{
	irNuevo();
}

function irBuscar()
{
	mostrarCatalogoPais('definicion', Ext.getCmp('codpai'), Ext.getCmp('despai'));
}

function irEliminar()
{
	if(Actualizar)
	{
		function respuesta(btn)
		{
			if(btn=='yes')
			{
				obtenerMensaje('procesar','','Eiminando Datos');
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
