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

var pldocumentos      = null;  //instancia del formulario de agencia
var comcatdocumento  = null;  //instancia del componente campo catalogo agencia
var Actualizar          = null;
barraherramienta    = true;

Ext.onReady(function()
{
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	  //creando store para el combo tipo iva
	var tipodoc = [['-- Seleccione --','--'],
	               ['Legal','01'],
	               ['Según especialidad','02'],
	               ['Financiera','03']
	]; // Arreglo que contiene los Documentos que se pueden controlar
	
	var sttipodoc = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : tipodoc
	});
	//fin creando store para el combo tipo iva

	//creando objeto combo tipo iva
	var cmbtipdoc = new Ext.form.ComboBox({
		store : sttipodoc,
		fieldLabel : 'Tipo ',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		id : 'tipdoc',
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local',
		binding:true,
		hiddenvalue:'',
		defaultvalue:'',
		allowBlank:false
	});
	//fin creando objeto combo tipo iva

	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	pldocumentos = new Ext.FormPanel({
		applyTo: 'formulario',
		width: 700,
		height: 175,
		title:"<H1 align='center'>Recaudos o Documentos</H1>",
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
		items: [{
				xtype:"fieldset", 
				title:'Definir Recaudo o Documento',
				style: 'position:absolute;left:15px;top:3px',
				border:true,
				width: 650,
				cls :'fondo',
				height: 125,
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
										id: 'coddoc',
										disabled: true,
										autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '2'},
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
										id: 'dendoc',
										width: 400,
										autoCreate: {tag: 'input', type: 'text', size: '50', maxlength: '254', autocomplete: 'off', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzñ ABCDEFGHIJKLMNOPQRSTUVWXYZÑ0123456789.,-');"},
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
						style: 'position:absolute;left:15px;top:80px',
						items: [{
								layout: "form",
								border: false,
								labelWidth: 130,
								columnWidth: 0.5,
								items: [cmbtipdoc]
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
						defaultvalue:'sigesp_vis_rpc_documentos.html'
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
						defaultvalue:'sigesp_vis_rpc_documentos.html'
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
		url: '../../controlador/rpc/sigesp_ctr_rpc_documentos.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request )
		{ 
	        var codigo = result.responseText;
			if (codigo != "") {
				limpiarFormulario(pldocumentos);
				Ext.getCmp('coddoc').setValue(codigo);
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

function mostrarTipo(valor)
{
	if (valor=="01"){
		return 'Legal';
	}
	else if (valor=="02"){
		return 'Segun Especialidad';	
	}
	else{
		return 'Financiera';
	}
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var registro_documento = Ext.data.Record.create([
			{name: 'coddoc'},
			{name: 'dendoc'},
			{name: 'tipdoc'}
	]);
	
	var dsdocumento =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_documento)
	});
						
	var colmodelcatdocumento = new Ext.grid.ColumnModel([
          	{header: "<H1 align='center'>C&#243;digo</H1>", width: 20, sortable: true,   dataIndex: 'coddoc'},
          	{header: "<H1 align='center'>Nombre</H1>", width: 50, sortable: true, dataIndex: 'dendoc'},
          	{header: "<H1 align='center'>Tipo</H1>", width: 30, sortable: true, dataIndex: 'tipdoc',renderer:mostrarTipo}
    ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatdocumento = new com.sigesp.vista.comCatalogo({
		titvencat: "<H1 align='center'>Cat&#225;logo de Recaudo o Documentos</H1>",
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dsdocumento,
		colmodelocat: colmodelcatdocumento,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codoc',valor:'coddoc',longitud:'3',ancho:100},
				   {etiqueta:'Descripci&#243;n',id:'dedoc',valor:'dendoc',longitud:'254',ancho:300}],
		rutacontrolador:'../../controlador/rpc/sigesp_ctr_rpc_documentos.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:pldocumentos
	});
	
	comcatdocumento.mostrarVentana();
}

function irGuardar()
{
	var cadjson = '';
	if(Actualizar == null)
	{
		cadjson = getItems(pldocumentos,'incluir','N',null,null);
	} 
    else
    {
    	cadjson = getItems(pldocumentos,'actualizar','N',null,null);
    }
	try
	{
		var objjson = Ext.util.JSON.decode(cadjson);
		if (typeof(objjson) == 'object') {
			var parametros = 'ObjSon=' + cadjson;
			Ext.Ajax.request({
				url : '../../controlador/rpc/sigesp_ctr_rpc_documentos.php',
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
			var cadjson = getItems(pldocumentos,'eliminar','N',null,null);
			try
			{
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object')
				{
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
						url : '../../controlador/rpc/sigesp_ctr_rpc_documentos.php',
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
			catch(e){
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