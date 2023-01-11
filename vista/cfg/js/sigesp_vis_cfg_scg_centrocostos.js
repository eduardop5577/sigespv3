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
var formCentroCosto = null;
ruta ='../../controlador/cfg/sigesp_ctr_cfg_scg_centrocostos.php'; //ruta del controlador


var Campos =new Array(
	['codcencos','novacio|'],
	['denominacion','novacio|']
);

Ext.onReady(function()
{
	Ext.QuickTips.init();
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	formCentroCosto = new Ext.FormPanel({
		applyTo: 'formulario_centrocosto',
		width: 600,
		height: 120,
		title: 'Centro de Costos Contables',
		frame:true,
		labelWidth:100,
		defaults: {width: 100,labelSeparator:''},
		defaultType: 'textfield',
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		items: [{
			xtype: 'textfield',
			fieldLabel: 'C&#243;digo',
			name:'C&#243;digo',
			id: 'codcencos',
			autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3', onkeypress: "return keyRestrict(event,'0123456789');"},
			width: 40,
			listeners:{
				'blur':function(campo){
					var codigo = campo.getValue();
					campo.setValue(ue_rellenarcampo(codigo,3));
				}
			}
		},{
			xtype: 'textfield',
			fieldLabel: 'Denominaci&#243;n',
			name: 'Denominaci&#243;n',
			autoCreate: {tag: 'input', type: 'text', maxlength: 254, onkeypress: "return keyRestrict(event,'0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
			id: 'denominacion',
			width: 400
		}]
	});

});

function irNuevo()
{
	limpiarFormulario(formCentroCosto);		
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
			Ext.getCmp('codcencos').setValue(codigo);
			var codigo = Ext.getCmp('codcencos').getValue();
			Ext.getCmp('codcencos').setValue(ue_rellenarcampo(codigo,3));
		}
	}	
	})
}

function irCancelar()
{
	 irNuevo();
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var recentrocosto = Ext.data.Record.create([
						{name: 'codcencos'},
						{name: 'denominacion'}
		]);
	
	var dscentrocosto =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},recentrocosto)
		});
						
	var cmcentrocosto = new Ext.grid.ColumnModel([
          	{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcencos'},
          	{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'denominacion'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatalogoagencia = new com.sigesp.vista.comCatalogo({
		titvencat: 'Catalogo de Centros de Costo',
		anchoformbus: 500,
		altoformbus:130,
		anchogrid: 500,
		altogrid: 400,
		anchoven: 550,
		altoven: 470,
		datosgridcat: dscentrocosto,
		colmodelocat: cmcentrocosto,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codcen',valor:'codcencos',ancho:50,longitud:'3'},
				   {etiqueta:'Descripci&#243;n',id:'dencen',valor:'denominacion',ancho:250,longitud:'254'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scg_centrocostos.php',
		parametros: "ObjSon={'oper': 'catalogo'",
		tipbus:'P',
		setdatastyle:'F',
		formulario:formCentroCosto
	});
	
	comcatalogoagencia.mostrarVentana();
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
							if(respuesta[1] == '-10')
							{
								Ext.MessageBox.alert('Error', 'La combinaci&#243;n afectaci&#243;n presupuestaria y tipo ya existe');
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
						}
						limpiarFormulario(formCentroCosto);
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
									if (Registros[0]!='')
									{
										Ext.MessageBox.alert('Error', Registros[0]);
								  	}
									else
									{
										Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								  	}
								}
							}
							limpiarFormulario(formCentroCosto);
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