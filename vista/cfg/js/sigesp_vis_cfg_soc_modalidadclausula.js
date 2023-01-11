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
var formmodclausula           = null;  //instancia del formulario de agencia
var comcatalogomodclausula    = null;  //instancia del componente campo catalogo agencia
var comliscatclausula		  = null;  //instancia del componente lita catalogo clausulas

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	function buscarCodigo()
	{
		
		var myJSONObject ={
			"operacion":"buscarcodigo" 
		};
		
		ObjSon=Ext.util.JSON.encode(myJSONObject);
		parametros ='ObjSon='+ObjSon;
		Ext.Ajax.request({
			url: '../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
			params: parametros,
			method: 'POST',
			success: function ( result, request ) { 
	            var codigo = result.responseText;
				if (codigo != "") {
					Ext.getCmp('codtipmod').setValue(codigo);
				}
			},
			failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'Problemas de comunicacion con el servidor!'); 
			}
		});		
	        
	}
	
	//creando datastore y columnmodel para el catalogo de clausulas 
	var smclausulacat = new Ext.grid.CheckboxSelectionModel({});
	var rgclausulacat = Ext.data.Record.create([
						{name: 'codcla'},
						{name: 'dencla'}
		]);
	
	var dsclausulacat =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rgclausulacat)
		});
						
	var cmclausulacat = new Ext.grid.ColumnModel([smclausulacat,
          				{header: "C&#243;digo", width: 10, sortable: true,   dataIndex: 'codcla'},
          				{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dencla'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de clausulas
	
	//creando datastore y columnmodel para el grid de clausulas
	var smclausula = new Ext.grid.CheckboxSelectionModel({});
	var dsclausula =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rgclausulacat)
		});
						
	var cmclausula = new Ext.grid.ColumnModel([smclausula,
          				{header: "C&#243;digo", width: 10, sortable: true,   dataIndex: 'codcla'},
          				{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'dencla'}
        ]);
	//fin creando datastore y columnmodel para el el grid de clausulas
	
	comliscatclausula = new com.sigesp.vista.comListaCatalogo({
		titvencat: 'Cat&#225;logo de Cl&#225;usulas',
		idgrid: 'gridclausuala',
		anchoformbus: 450,
		altoformbus:100,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		ancho: 450,
		alto: 200,
		datosgridcat: dsclausulacat,
		colmodelocat: cmclausulacat,
		selmodelocat: smclausulacat,
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
		parametros: "ObjSon={'operacion': 'catalogo_clausuala'}",
		tipbus:'L',
		arrfiltro:[{etiqueta:'C&#243;digo',id:'codigo',valor:'codcla'},
				   {etiqueta:'Descripci&#243;n',id:'descripcion',valor:'dencla'}],
		posicion: 'position:absolute;left:90px;top:90px',
		titgrid: 'Detalles Cl&#225;usulas',
		datosgrid: dsclausula,
		colmodelo: cmclausula,
		selmodelo: smclausula,
		arrcampovalidaori:['codcla'],
		arrcampovalidades:['codcla'],
		guardarEliminados: false,
		rgeliminar: null
	});
	
	var Xpos = ((screen.width/2)-(700/2));
	var Ypos = 100;
	formmodclausula = new Ext.FormPanel({
	applyTo: 'formulario_modclausula',
	width: 700,
	height: 350,
	title: 'Definici&#243;n de Modalidad de Cl&#225;usulas',
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
										id: 'codtipmod',
										autoCreate: {tag: 'input', type: 'text', size: '3', autocomplete: 'off', maxlength: '3'},
										disabled:true,
										width: 50,
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
										name: 'denominacion',
										id: 'denmodcla',
										autoCreate: {tag: 'input', type: 'text', maxlength: 100, onkeypress: "return keyRestrict(event,'0123456789áéíóúÁÉÍÓÚabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!¡;: ');"},
										width: 400,
										binding:true,
										hiddenvalue:'',
										defaultvalue:'',
										allowBlank:false
									}]
						}]
	        },
	        comliscatclausula.dataGrid] 
	});
	
	buscarCodigo();
});

function irNuevo(){
	
	var myJSONObject ={
		"operacion":"buscarcodigo" 
	};
		
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) { 
	            var codigo = result.responseText;
				if (codigo != "") {
					Ext.getCmp('codtipmod').setValue(codigo);
					Ext.getCmp('denmodcla').reset();
					comliscatclausula.dataGrid.getStore().removeAll();
				}
		},
		failure: function ( result, request)
		{ 
				Ext.MessageBox.alert('Error', 'El Registro no pudo ser ubicado'); 
		}
	});		
}

function irCancelar()
{
	irNuevo();
}

function irGuardar()
{
	var dataDetalle = comliscatclausula.dataGrid.getStore();
	if(dataDetalle.getCount()>0)
	{
		var arrtablas = [{nomtabla:'esp_soc_dtm_clausulas',
						comstore:comliscatclausula.dataGrid.getStore(),
						numcampo:1,
						arrclave:['codtipmod']}];
		
		var arrcampostablas = [{nomcampo:'codcla',
								tipocampo:'texto',
								formato:''}];
		
		var cadjson = getItems(formmodclausula,'incluir','A',arrtablas,arrcampostablas);
		try {
			var objjson = Ext.util.JSON.decode(cadjson);
			if (typeof(objjson) == 'object') {
				var parametros = 'ObjSon=' + cadjson;
				Ext.Ajax.request({
					url: '../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
					params: parametros,
					method: 'POST',
					success: function(resultad, request){
						var datos = resultad.responseText;
						var resultado = datos.split("|");
						if (resultado[2] == "1") {
							switch (resultado[1]) {
								case "0":
									Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
									break;
								case "1":
									Ext.MessageBox.alert('Mensaje', 'El registro fue actualizado');
									break;
								case "2":
									Ext.MessageBox.alert('Mensaje', 'El registro fue incluido');
									break;
							}
						}
						else {
							Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
						}
						limpiarFormulario(formmodclausula);
						comliscatclausula.dataGrid.getStore().removeAll();
					},
					failure: function(result, request){
						Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
					}
				});
			}
		}
		catch(e){
			//alert('error'+e);
		}
	}
	else{
		Ext.MessageBox.alert('Mensaje', 'Debe agregar al menos un detalle');
	}
}

function obtenerDetalles(){
	
	var cadenaJson="{'operacion':'buscardetalle',codtipmod:'"+Ext.getCmp('codtipmod').getValue()+"'}";
	var parametros = 'ObjSon='+cadenaJson;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request)	{ 
			datos = resultado.responseText;
			var objetodata = eval('(' + datos + ')');
			if(objetodata!=''){
				comliscatclausula.dataGrid.getStore().loadData(objetodata);//ds nivel N
			}
		}
	});
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var rgmodcla = Ext.data.Record.create([
						{name: 'codtipmod'},
						{name: 'denmodcla'}
		]);
	
	var dsmodcla =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rgmodcla)
		});
						
	var cmmodcla = new Ext.grid.ColumnModel([
          				{header: "Codigo", width: 20, sortable: true,   dataIndex: 'codtipmod'},
          				{header: "Nombre", width: 40, sortable: true, dataIndex: 'denmodcla'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatalogomodclausula = new com.sigesp.vista.comCatalogo({
		titvencat: 'Cat&#225;logo de Modalidad de Cl&#225;usula',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dsmodcla,
		colmodelocat: cmmodcla,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'cotimod',valor:'codtipmod'},
				   {etiqueta:'Descripci&#243;n',id:'democla',valor:'denmodcla'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'operacion': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:formmodclausula,
		onAceptar: true,
		fnOnAceptar: obtenerDetalles
	});
	
	comcatalogomodclausula.mostrarVentana();
}

function irEliminar()
{
	function eliminando(btn)
	{
		if(btn=='yes')
		{
			var arrtablas = [{nomtabla:'esp_soc_dtm_clausulas',
							comstore:comliscatclausula.dataGrid.getStore(),
							numcampo:1,
							arrclave:['codtipmod']}];
			
			var arrcampostablas = [{nomcampo:'codcla',
									tipocampo:'texto',
									formato:''}];
			
			var cadjson = getItems(formmodclausula,'eliminar','A',arrtablas,arrcampostablas);
			try {
				var objjson = Ext.util.JSON.decode(cadjson);
				if (typeof(objjson) == 'object') {
					var parametros = 'ObjSon=' + cadjson;
					Ext.Ajax.request({
						url: '../../controlador/cfg/sigesp_ctr_cfg_soc_modalidadclausula.php',
						params: parametros,
						method: 'POST',
						success: function(resultad, request){
								datos = resultad.responseText;
								resultado = datos.split("|");
								switch(resultado[1]) {
									case "-1":
			    						Ext.MessageBox.alert('Mensaje', 'El registro no puede ser eliminado, ya que ha sido referenciado en otros procesos');
										break;
									case "1":
			    						Ext.MessageBox.alert('Mensaje', 'El registro fue eliminado');
										break;
									case "0":
			    						Ext.MessageBox.alert('Error','Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
										break;
									case "-8":
			    						Ext.MessageBox.alert('Error','El registro no puede ser eliminado, no puede eliminar registros intermedios');
										break;
								}
								limpiarFormulario(formmodclausula);
								comliscatclausula.dataGrid.getStore().removeAll();
						},
						failure: function(result, request){
							Ext.MessageBox.alert('Error', 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo');
						}
					});
				}
			}
			catch(e){
				//alert('error'+e);
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminando);
}