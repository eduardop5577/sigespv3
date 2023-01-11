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
var formconmovimiento         = null;  //instancia del formulario de agencia
var comcatalogoconmovimiento  = null;  //instancia del componente campo catalogo agencia
var Actualizar                = null
var banderaGrabar             = false;
var banderaEliminar           = true;
var ruta ='../../controlador/cfg/sigesp_ctr_cfg_scb_conceptomovimiento.php'; //ruta del controlador

var Campos =new Array(['codconmov','novacio|'],
					  ['denconmov','novacio|'],
					  ['codope','novacio|']);

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//creando store para el combo de operaciones
	var datmovimiento = [ [ 'Nota de Debito', 'ND' ],
	                      [ 'Nota de Credito', 'NC' ],
	                      [ 'Deposito', 'DP' ],
						  [ 'Retiro', 'Re' ],
						  [ 'Cheque', 'CH' ]
						]
	var storemovimiento = new Ext.data.SimpleStore({
		fields : [ 'etiqueta', 'valor' ],
		data : datmovimiento
	});
	//fin creando store para el combo de operaciones

	//creando objeto combo de operaciones
	var combomovimiento = new Ext.form.ComboBox({
		store : storemovimiento,
		fieldLabel : 'Operaci&#243;n asociada',
		labelSeparator : '',
		editable : false,
		displayField : 'etiqueta',
		valueField : 'valor',
		name : 'Operaci&#243;n',
		id : 'codope',
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local'
	});
	//fin creando objeto combo de operaciones
	
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 150;
	formconmovimiento = new Ext.FormPanel({
	applyTo: 'formulario_conmovimiento',
	width: 700,
	height: 150,
	title: 'Definici&#243;n de Conceptos de Movimiento',
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
										name: 'C&#243;digo',
										id: 'codconmov',
										autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
										disabled:true,
										width: 50
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
										autoCreate: {tag: 'input', type: 'text', maxlength: 200, onkeypress: "return keyRestrict(event,'0123456789·ÈÌÛ˙¡…Õ”⁄abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!°;: ');"},
										id: 'denconmov',
										width: 400
									}]
						}]
	        },
	        {
				layout: "column",
				defaults: {border: false},
				style: 'position:absolute;left:15px;top:60px',
				items: [{
							layout: "form",
							border: false,
							labelWidth: 130,
							columnWidth: 0.5,
							items: [combomovimiento]
						}]
	        }] 
	});
	irNuevo();
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
		url: '../../controlador/cfg/sigesp_ctr_cfg_scb_conceptomovimiento.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
	            var datos = result.responseText;
				var	resultado = datos.split("|");
				var codigo = resultado[1];
				if (codigo != "") {
					Ext.getCmp('codconmov').setValue(codigo);
					Ext.getCmp('denconmov').reset();
					Ext.getCmp('codope').reset();
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
	var registro_conmov = Ext.data.Record.create([
						{name: 'codconmov'},
						{name: 'denconmov'},
						{name: 'codope'}
		]);
	
	var dsconmov =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_conmov)
		});
						
	var colmodelconmov = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codconmov'},
          				{header: "Nombre", width: 40, sortable: true, dataIndex: 'denconmov'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatalogoconmovimiento = new com.sigesp.vista.comCatalogo({
		titvencat: 'Cat&#225;logo de Conceptos de Movimientos',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 350,
		anchoven: 500,
		altoven: 450,
		datosgridcat: dsconmov,
		colmodelocat: colmodelconmov,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'cocomov',valor:'codconmov'},
				   {etiqueta:'Descripci&#243;n',id:'decomov',valor:'denconmov'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_scb_conceptomovimiento.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:formconmovimiento
	});
	
	comcatalogoconmovimiento.mostrarVentana();
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
						limpiarFormulario(formconmovimiento);
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
							Ext.getCmp('codconmov').reset();
							Ext.getCmp('denconmov').reset();
							Ext.getCmp('codope').reset();
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
