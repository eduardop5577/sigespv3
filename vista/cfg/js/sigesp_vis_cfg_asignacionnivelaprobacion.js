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
var formulario          = '';                           						// Variable que representa y contiene el panel que contiene los objetos
var cambiar     		= false;	                    						// Variable que verifica el Estatus de la Operacion para Modificacion
var pantalla    		= 'sigesp_vis_cfg_asignacionnivelaprobacion.php'; 							// Variable que contiene el nombre f�sico de la Pantalla
var sistema             = "CFG";                        						// Variable que contiene el nombre del sistema al que pertenece la pantalla
var ruta				= '../../controlador/cfg/sigesp_ctr_cfg_nivelaprobacion.php'; 	// Ruta del Controlador de la Pantalla
var Actualizar=null;

var arreglooperaciones = [
                              ['-','Seleccione'],
                              ['1','Solicitud de Ejecucion Presupuestaria'],
                              ['2','Orden de Compra'],
                              ['3','Solicitud de Pago'],
                              ]; 

var dataStoreOperaciones = new Ext.data.SimpleStore({
	  fields: ['idoperaciones', 'operaciones'],
	  data : arreglooperaciones // Se asocian los documentos disponibles
	});

var Campos =new Array(
						['codemp',''],
						['codasiniv','novacio|'],
						['codniv','novacio|'],
						['tipproc','novacio|'],
						['despridoc','novacio|']); // Arreglo que contiene la informacion del Registro, deben coincidir con la Tabla en la Base de Datos

	
//creando datastore y columnmodel para el catalogo de bancos
var nivelaprobacion = Ext.data.Record.create([
					{name: 'codniv'},
					{name: 'monnivdes'},
					{name: 'monnivhas'}
			]);

var dsnivelaprobacion =  new Ext.data.Store({
				reader: new Ext.data.JsonReader({
						root: 'raiz',             
						id: "id"   
						},nivelaprobacion)
			});
					
var colmodelcatnivel = new Ext.grid.ColumnModel([
					{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codniv'},
					{header: "Minimo", width: 40, sortable: true, dataIndex: 'monnivdes'},
					{header: "Maximo", width: 40, sortable: true, dataIndex: 'monnivhas'}
			]);
//fin creando datastore y columnmodel para el catalogo de bancos
	
	//componente campocatalogo para el campo banco
	comcampocatnivel = new com.sigesp.vista.comCampoCatalogo({
							titvencat: 'Cat&#225;logo de Niveles',
							anchoformbus: 450,
							altoformbus:100,
							anchogrid: 450,
							altogrid: 400,
							anchoven: 500,
							altoven: 400,
							anchofieldset:650,
							datosgridcat: dsnivelaprobacion,
							colmodelocat: colmodelcatnivel,
							rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_nivelaprobacion.php',
							parametros: "ObjSon={'oper': 'catalogo'}",
							arrfiltro:[{etiqueta:'C&#243;digo',id:'codiniv',valor:'codniv'},
									   {etiqueta:'Minimo',id:'monnivdes',valor:'monnivdes'},
									   {etiqueta:'Maximo',id:'monnivhas',valor:'monnivhas'}],
							posicion:'margin-top:0px;padding-left:2px;',
							tittxt:'Nivel',
							nametxt:'C&#243;digo Nivel',
							idtxt:'codniv',
							campovalue:'codniv',
							anchoetiquetatext:173,
							anchotext:70,
							anchocoltext:0.40,
							idlabel:'',
							idboton:'btnnivel',
							labelvalue:'',
							anchocoletiqueta:0.55,
							anchoetiqueta:400,
							tipbus:'L',
							binding:'C',
							hiddenvalue:'',
							defaultvalue:'',
							allowblank:false
				});
	//fin componente campocatalogo para el campo banco

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	 
	Xpos = ((screen.width/2)-(600/2));
	Ypos = 75;	
    formulario = new Ext.form.FormPanel({
    	   	 title:"Configuracion de Niveles de Aprobacion",
    		 frame:true,
    		 style: 'position:absolute;margin-left:'+Xpos+'px;margin-top:'+Ypos+'px',
    		 width: 630,
    		 height: 180,
    		 labelPad: 10,
    		 items:[{
				        xtype:"hidden",
				        name:"codemp",
				        id:"codemp",
						value:''
			        },
			        {
				        layout:"form",
						border:false,
						defaultType: "textfield",
						style: "margin-top:30px;padding-left:50px;",
						labelWidth:175,
						items:[
						       {
						        xtype:"textfield",
						        fieldLabel:"C&#243;digo",
						        labelWidth:40,
						        labelSeparator:'',
						        name:"codasiniv",
						        id:"codasiniv",
								autoCreate: {tag: 'input', type: 'text', size: '4', autocomplete: 'off', maxlength: '4'},
						        width:75,
								disabled:true
				        	   },
				        	   {
					                xtype:"combo",
					                labelSeparator:'',
					                store: dataStoreOperaciones,
					                hiddenName:'operaciones',
					                hiddenid:'idoperaciones',
					                displayField:'operaciones',
					                valueField:'idoperaciones',
									id:"tipproc",
					                typeAhead: true,
					                mode: 'local',
					                triggerAction: 'all',
					                selectOnFocus:true,
					                fieldLabel:'Operaciones',
					           	    listWidth:250,
					           	    editable:false,
					                width:250
				         		},
							   {
								xtype: 'textfield',
								fieldLabel: 'Descripci&#243;n',
								name: 'despridoc',
								id: 'despridoc',
								autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '100', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,12345678 ');"},
								width: 300
								},
								comcampocatnivel.fieldsetCatalogo
								]
			        }]
    		});
		 formulario.render("formulario");
		 irNuevo();
	}
);

function irCancelar()
{
	irNuevo();
}

function irNuevo()
{
	limpiarCampos();
	Actualizar=null;
	var myJSONObject ={
		"oper":"nuevoasignacion"
	};
	
	var ObjSon=Ext.util.JSON.encode(myJSONObject);
	var parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
	url : '../../controlador/cfg/sigesp_ctr_cfg_nivelaprobacion.php',
	params : parametros,
	method: 'POST',
	success: function ( result, request) 
	{ 
		datos = result.responseText;
		var codigo = eval('(' + datos + ')');
		if(codigo != "")
		{
			Ext.getCmp('codasiniv').setValue(codigo);
			comcampocatnivel.boton.disabled=false;
		}
	}	
	})
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var registro_asignacion = Ext.data.Record.create([
							 {name: 'codasiniv'},
							 {name: 'codniv'},
						     {name: 'tipproc'},
						     {name: 'despridoc'}
		]);
	
	var dsasignacion=  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_asignacion)
		});
						
	var colmodelcatasignacion = new Ext.grid.ColumnModel([
          				{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codasiniv'},
          				{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'despridoc'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatalogonivelasignacion = new com.sigesp.vista.comCatalogo({
		titvencat: 'Catalogo de Asignacion de Niveles',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 350,
		anchoven: 500,
		altoven: 450,
		datosgridcat: dsasignacion,
		colmodelocat: colmodelcatasignacion,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codasiniv1',valor:'codasiniv'},
				   {etiqueta:'Descripci&#243;n',id:'despridoc1',valor:'despridoc'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_nivelaprobacion.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogoasignacion'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:formulario
	});
	
	comcatalogonivelasignacion.mostrarVentana();
	comcampocatnivel.boton.disabled=true;
}


function irGuardar()
{
	var mensajeexito = 'Registro <operacion> con &#233;xito';
    var mensajeerror = 'Error al <operacion> registro';
	var cadjson = '';
	if(Actualizar == null)
	{
		operacion='incluirasignacion';
	    mensajeexito = mensajeexito.replace('<operacion>','incluido');
	    mensajeerror = mensajeerror.replace('<operacion>','incluir');
    } 
    else
	{
		operacion='actualizarasignacion';
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
							Ext.MessageBox.alert('Error', 'Ocurri&#243; un error el registro no pudo ser '+mensajeerror);
						}
						limpiarFormulario(formulario);
						irNuevo();
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
			var	cadjson=cargarJson('eliminarasignacion');
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
								if(Registros[1]=='-8')
								{
									Ext.MessageBox.alert('Error', 'El registro no puede ser eliminado, no puede eliminar registros intermedios');
								}
								else
								{
									Ext.MessageBox.alert('Error', 'El registro no pudo ser eliminado');
								}
							}
							limpiarFormulario(formulario);
							irNuevo();
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
