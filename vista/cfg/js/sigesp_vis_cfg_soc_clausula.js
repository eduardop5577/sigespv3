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
var Actualizar           = null
var comcatalogoclausula  = null;  //instancia del componente campo catalogo agencia
var formClausula         = null;  //formulario
var ruta			     = '../../controlador/cfg/sigesp_ctr_cfg_soc_clausula.php'; 	// Ruta del Controlador de la Pantalla

var Campos = new Array(
			['codcla','novacio|'],
	        ['dencla','novacio|']
	    )
	    
Ext.onReady(function(){
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';	
	Ext.QuickTips.init();
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	
	formClausula = new Ext.FormPanel({
	applyTo: 'formulario_soc_clausula',
	width: 700,
	height: 200,
	labelWidth: 120,
	title: 'Cl&#225;usulas',
	frame:true,
	defaultType: 'textfield',
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
	items: [{
		xtype: 'textfield',
		labelSeparator:'',
		fieldLabel: 'C&#243;digo',
		name: 'c&#243;digo',
		id: 'codcla',
		disabled:true,
		width: 100		
		},{		
		xtype: 'textfield',
		labelSeparator:'',
		fieldLabel: 'Denominaci&#243;n',
		name: 'denominaci&#243;n',
		autoCreate: {tag: 'input', type: 'text', onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
		id: 'dencla',		
		width: 500
		}]//fin de items del panel
		
	});
});

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	var myJSONObject =
	{
			"oper":"buscarcodigo" 
	};

	var ObjSon = Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: ruta,
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
		var codigo = result.responseText;
		if (codigo != "") {
			Ext.getCmp('codcla').setValue(codigo);
			Ext.getCmp('dencla').setValue('');
		}
	},
	failure: function ( result, request)
	{ 
		Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
	}
	});		
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de clausulas
	var reClausula = Ext.data.Record.create([
						{name: 'codcla'},
						{name: 'dencla'}
		]);
	
	var dsClausula =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},reClausula)
		});
						
	var cmClausula = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codcla'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'dencla'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatalogoclausula = new com.sigesp.vista.comCatalogo({
		titvencat: 'Cat&#225;logo de Cl&#225;usulas',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dsClausula,
		colmodelocat: cmClausula,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codcl',valor:'codcla'},
				   {etiqueta:'Denominaci&#243;n',id:'dencl',valor:'dencla'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_soc_clausula.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:formClausula
	});
	
	comcatalogoclausula.mostrarVentana();
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
				limpiarFormulario(formClausula);
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

function irEliminar()
{
	function eliminarRegistro(btn)
	{
		if(btn=='yes')
		{
			Json=cargarJson('eliminar');
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
										Ext.MessageBox.alert('Error','El registro no puede ser eliminado, no puede eliminar registros intermedios');
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
							}
							limpiarFormulario(formClausula);
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

