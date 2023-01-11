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
var formagencia         = null;  //instancia del formulario de agencia
var comcampocatbanco    = null;  //instancia del componente campo catalogo bancos
var comcatalogoagencia  = null;  //instancia del componente campo catalogo agencia
var Actualizar          = null
var banderaNuevo        = true;
var banderaGrabar       = false;
var banderaEliminar     = false;
var ruta ='../../controlador/cfg/sigesp_ctr_cfg_scb_agencia.php'; //ruta del controlador

var Campos =new Array(['codage','novacio|'],
					  ['nomage','novacio|'],
					  ['codban','novacio|'],
					  ['nomban','|']);

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	function buscarCodigo(){
		
		var myJSONObject ={
			"oper":"buscarcodigo" 
		};
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/cfg/sigesp_ctr_cfg_scb_agencia.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request )
			{ 
	            var datos = result.responseText;
				var	resultado = datos.split("|");
				var codigo = resultado[1];
				if (codigo != "")
				{
					Ext.getCmp('codage').setValue(codigo);
				}
			},
			failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
			}
		});		
	        
	}
	
	//creando datastore y columnmodel para el catalogo de bancos
	var registro_banco = Ext.data.Record.create([
						{name: 'codban'},
						{name: 'nomban'}
				]);
	
	var dsbanco =  new Ext.data.Store({
					reader: new Ext.data.JsonReader({
							root: 'raiz',             
							id: "id"   
							},registro_banco)
	  			});
						
	var colmodelcatbanco = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codban'},
          				{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'nomban'}
				]);
	//fin creando datastore y columnmodel para el catalogo de bancos
	
	//componente campocatalogo para el campo banco
	comcampocatbanco = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo de Bancos',
							anchoformbus: 450,
							altoformbus:100,
							anchogrid: 450,
							altogrid: 350,
							anchoven: 500,
							altoven: 450,
							anchofieldset:650,
							datosgridcat: dsbanco,
							colmodelocat: colmodelcatbanco,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scb_banco.php',
							parametros: "ObjSon={'oper': 'catalogo'}",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'codiban',valor:'codban'},
									   {etiqueta:'Descripci&#243;n',id:'desban',valor:'nomban'}],
							posicion:'position:absolute;left:5px;top:50px',
							tittxt:'Banco',
							nametxt:'Banco',
							idtxt:'codban',
							campovalue:'codban',
							anchoetiquetatext:130,
							anchotext:70,
							anchocoltext:0.40,
							idlabel:'nomban',
							labelvalue:'nomban',
							anchocoletiqueta:0.55,
							anchoetiqueta:400,
							tipbus:'L',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
				});
	//fin componente campocatalogo para el campo banco
	
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	formagencia = new Ext.FormPanel({
	applyTo: 'formulario_agencia',
	width: 700,
	height: 150,
	title: 'Definici&#243;n de Agencias',
	frame:true,
	style:'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px;',
	items: [{
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:0px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							items: [{
										xtype: 'textfield',
										fieldLabel: 'C&#243;digo',
										labelSeparator :'',
										name: 'codigo',
										id: 'codage',
										autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10'},
										disabled:true,
										width: 80
									}]
						}]
			},
	        {
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:30px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							items: [{
										xtype: 'textfield',
										labelSeparator :'',
										fieldLabel: 'Denominaci&#243;n',
										name: 'Denominaci&#243;n',
										id: 'nomage',
										width: 400
									}]
						}]
	        },
			comcampocatbanco.fieldsetCatalogo] 
	});
	buscarCodigo();
});

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	
	var myJSONObject ={
		"oper":"buscarcodigo" 
	};
		
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/cfg/sigesp_ctr_cfg_scb_agencia.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
	            var datos = result.responseText;
				var	resultado = datos.split("|");
				var codigo = resultado[1];
				if (codigo != "") {
					Ext.getCmp('codage').setValue(codigo);
					Ext.getCmp('nomage').setValue('');
					Ext.getCmp('codban').setValue('');
					Ext.getCmp('nomban').setValue('');
				}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser '+mensaje); 
		}
	});		
        
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var registro_agencia = Ext.data.Record.create([
						{name: 'codage'},
						{name: 'nomage'},
						{name: 'codban'},
						{name: 'nomban'}
		]);
	
	var dsagencia =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_agencia)
		});
						
	var colmodelcatagencia = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codage'},
          				{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'nomage'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatalogoagencia = new com.sigesp.vista.comCatalogo({
		titvencat: 'Catalogo de Agencias',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 350,
		anchoven: 500,
		altoven: 450,
		datosgridcat: dsagencia,
		colmodelocat: colmodelcatagencia,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'coage',valor:'codage'},
				   {etiqueta:'Descripci&#243;n',id:'noage',valor:'nomage'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scb_agencia.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:formagencia
	});
	
	comcatalogoagencia.mostrarVentana();
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
									Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensajeerror);
								}
							}
						}
						limpiarFormulario(formagencia);
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
							limpiarFormulario(formagencia);
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
