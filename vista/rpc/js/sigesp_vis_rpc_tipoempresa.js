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

var pltipoempresa      = null;  //instancia del formulario de agencia
var comcattipoempresa = null;  //instancia del componente campo catalogo agencia
var Actualizar          = null;
barraherramienta    = true;

Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';

	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	pltipoempresa = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 700,
		height: 155,
		title:"<H1 align='center'>Tipo de Empresa</H1>",
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		items: [{
				xtype:"fieldset", 
				title:'Definir Tipo de Empresa',
				style: 'position:absolute;left:15px;top:3px',
				border:true,
				width: 650,
				cls :'fondo',
				height: 110,
				items:[{
						layout: "column",
						defaults: {border: false},
						style: 'position:absolute;left:15px;top:20px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								columnWidth: 0.5,
								items: [{
										xtype: 'textfield',
										fieldLabel: eticodigo,
										labelSeparator :'',
										name: 'codigo',
										id: 'codtipoorg',
										autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '3'},
										disabled:true,
										width: 80,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										allowBlank:false
									}]
								}]
					},
					{
					layout: "column",
					defaults: {border: false},
					style: 'position:absolute;left:15px;top:50px',
					items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							items: [{
									xtype: 'textfield',
									labelSeparator :'',
									fieldLabel: 'Denominaci&#243;n',
									name: 'denominacion',
									id: 'dentipoorg',
									autoCreate: {tag: 'input', type: 'text', size: '100', autocomplete: 'off', maxlength: '40', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzñ ABCDEFGHIJKLMNOPQRSTUVWXYZÑ0123456789.,-');"},
									width: 400,
									binding:true,
									hiddenvalue:'',
									defaultvalue:'',
									allowBlank:false
							}]
						}]
					},
					{
					xtype: 'hidden',
					id: 'codsis',
					binding:true,
					defaultvalue:'RPC'
					},
					{
					xtype: 'hidden',
					id: 'nomven',
					binding:true,
					defaultvalue:'sigesp_vis_rpc_tipoempresa.html'
					}] 
			}]
	});
});

buscarCodigo();

function buscarCodigo()
{
	var myJSONObject = {
			"operacion":"buscarcodigo" 
		};
			
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/rpc/sigesp_ctr_rpc_tipoempresa.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
	            var codigo = result.responseText;
				if (codigo != "")
				{
					limpiarFormulario(pltipoempresa);
					Ext.getCmp('codtipoorg').setValue(codigo);
					Actualizar = null;
				}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});		
}

function irNuevo()
{
	buscarCodigo();
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de especialidad
	var registro_tipoempresa = Ext.data.Record.create([
			{name: 'codtipoorg'},
			{name: 'dentipoorg'}
	]);
	
	var dstipoe =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_tipoempresa)
	});
						
	var colmodelcattipoe = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'codtipoorg'},
          	{header: "<H1 align='center'>Denominaci&#243;n</H1>", width: 40, sortable: true, dataIndex: 'dentipoorg'}
    ]);
	//fin creando datastore y columnmodel para el catalogo de especialidad
	
	comcattipoempresa = new com.sigesp.vista.comCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Tipos de Empresa</H1>",
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dstipoe,
		colmodelocat: colmodelcattipoe,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'idcodtipoe',valor:'codtipoorg',longitud:'2',ancho:100},
				   {etiqueta:'Descripci&#243;n',id:'idtipoe',valor:'dentipoorg',longitud:'40',ancho:300}],
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_tipoempresa.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:pltipoempresa
	});
	comcattipoempresa.mostrarVentana();
}

function irGuardar()
{
	var cadjson = '';
	if(Actualizar == null)
	{
		cadjson  = getItems(pltipoempresa,'incluir','N',null,null);
	} 
    else
    {
    	cadjson  = getItems(pltipoempresa,'actualizar','N',null,null);
    }
	try
	{
		var objjson = Ext.util.JSON.decode(cadjson);
		if (typeof(objjson) == 'object')
		{
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_tipoempresa.php',
				params : parametros,
				method: 'POST',
				success: function ( resultado, request)
				{
					datos = resultado.responseText;
					Ext.Msg.hide();
					var datajson = eval('(' + datos + ')');
					if (datajson.raiz.valido==true)
					{	
						Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
					}
					else
					{
						Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
					}
					irNuevo();
				},
				failure: function (result,request) 
				{ 
					Ext.Msg.hide();
					Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
					irNuevo();
				}
			});
		}
	}	
	catch(e)
	{
			alert('Verifique los datos, esta insertando caracteres invalidos '+e);
	}
}

function irCancelar()
{
	irNuevo();
}

function irEliminar()
{
	function respuesta(btn)
	{
		if(btn=='yes')
		{
			var cadjson = getItems(pltipoempresa,'eliminar','N',null,null);
			try
			{
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
						url : '../../controlador/rpc/sigesp_ctr_rpc_tipoempresa.php',
						params : parametros,
						method: 'POST',
						success: function ( resultado, request)
						{
							datos = resultado.responseText;
							Ext.Msg.hide();
							var datajson = eval('(' + datos + ')');
							if (datajson.raiz.valido==true)
							{	
								Ext.MessageBox.alert('Mensaje', datajson.raiz.mensaje);
							}
							else
							{
								Ext.MessageBox.alert('Error', datajson.raiz.mensaje);
							}
							irNuevo();
						},
						failure: function (result,request) 
						{ 
							Ext.Msg.hide();
							Ext.MessageBox.alert('Error', 'Error al procesar la Información'); 
							irNuevo();
						}
					});
				}
			}
			catch(e)
			{
				alert('error'+e);
			}
		}
	}
	if(Actualizar)
	{
		  Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', respuesta);
	}  
	else
	{
		Ext.Msg.show({
				title:'Mensaje',
				msg: 'El registro debe estar guardado para poder eliminarlo, verifique por favor',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
		}); 
	}
}