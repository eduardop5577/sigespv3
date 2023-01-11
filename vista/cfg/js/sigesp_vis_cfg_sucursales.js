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
var formSucursales = null;
var fieldSetEstructura = null;

Ext.onReady(function(){
	Ext.QuickTips.init();
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	fieldSetEstructura = new com.sigesp.vista.comFieldSetEstructuraPresupuesto({
		titform: 'Estructura Presupuestaria',
		mostrarDenominacion:true,
		idtxt:'comfs'
	});
	
	
	var Xpos = ((screen.width/2)-(475));
	formSucursales = new Ext.FormPanel({
		applyTo: 'formulario_sucursales',
		width: 950,
		height: 400,
		title: 'Sucursales',
		frame:true,
		style:'position:absolute;margin-left:'+Xpos+'px;margin-top:50px;',
		items: [{
			xtype: 'textfield',
			fieldLabel: 'C&#243;digo',
			id: 'codsuc',
			autoCreate: {tag: 'input', type: 'text', size: '10', autocomplete: 'off', maxlength: '10'},
			width: 100
		},{
			xtype: 'textfield',
			fieldLabel: 'Denominaci&#243;n',
			autoCreate: {tag: 'input', type: 'text', autocomplete: 'off', maxlength: '254', onkeypress: "return keyRestrict(event,'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.,-_@#%()*+!=°;:[]{}·ÈÌÛ˙¡…Õ”⁄ ');"},
			id: 'nomsuc',
			width: 400
		},fieldSetEstructura.fieldSetEstPre]
	});

});

function irNuevo()
{
	limpiarFormulario(formSucursales);
	fieldSetEstructura.limpiarDenominaciones();
}

function irCancelar()
{
	irNuevo();
}

function irGuardar()
{
	var cadenaJson = " " ;
	if(Actualizar)
	{
		cadenaJson = "{'oper':'actualizar','codmenu':'"+codmenu+"', ";
	}
	else
	{
		cadenaJson = "{'oper':'incluir','codmenu':'"+codmenu+"', ";
	}
	
	cadenaJson = cadenaJson + "'codsuc':'" + Ext.getCmp('codsuc').getValue() +"'," +
				"'nomsuc':'" + Ext.getCmp('nomsuc').getValue() +"'," +
				"'codestpro1':'" + fieldSetEstructura.obtenerCodigoEstructuraNivel(1)+"'," +
				"'codestpro2':'" + fieldSetEstructura.obtenerCodigoEstructuraNivel(2)+"'," +
				"'codestpro3':'" + fieldSetEstructura.obtenerCodigoEstructuraNivel(3)+"'," +
				"'codestpro4':'" + fieldSetEstructura.obtenerCodigoEstructuraNivel(4)+"'," +
				"'codestpro5':'" + fieldSetEstructura.obtenerCodigoEstructuraNivel(5)+"'," +
				"'estcla':'" + fieldSetEstructura.obtenerEstClaEstructura()+"'}";
	var cadenaTemp = eval('(' + cadenaJson + ')');
	var ObjSon     = JSON.stringify(cadenaTemp);
	var parametros = 'ObjSon='+ObjSon; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_sucursales.php',
		params : parametros,
		method: 'POST',
		success: function ( resultad, request ){
			var respuesta = resultad.responseText;
			if(respuesta == '1'){
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Registro modificado con &#233;xito',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
			}
			else if(respuesta == '2'){
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Registro incluido con &#233;xito',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
			}
			else {
				Ext.Msg.show({
					title:'Error',
					msg: 'Ha ocurrido un error el registro no fue procesado',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
	       	limpiarFormulario(formSucursales);
			fieldSetEstructura.limpiarDenominaciones();
		},
		failure: function ( result, request){ 
			Ext.Msg.show({
				title:'Error',
				msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
				buttons: Ext.Msg.OK,
				icon: Ext.MessageBox.ERROR
			}); 
		} 
	});
}

function irEliminar()
{
	function eliminar(btn)
	{
		if(btn=='yes')
		{
			var codsuc = Ext.getCmp('codsuc').getValue();
			if(codsuc!='')
			{
				var cadenaJson = "{'oper':'eliminar','codmenu':'"+codmenu+"', "+
						"'codsuc':'" + codsuc +"'}";
				var cadenaTemp = eval('(' + cadenaJson + ')');
				var ObjSon     = JSON.stringify(cadenaTemp);
				var parametros = 'ObjSon='+ObjSon; 
				Ext.Ajax.request({
					url : '../../controlador/cfg/sigesp_ctr_cfg_sucursales.php',
					params : parametros,
					method: 'POST',
					success: function ( resultad, request ){ 
						var respuesta = resultad.responseText;
						if(respuesta == '1'){
							Ext.Msg.show({
								title:'Mensaje',
								msg: 'Registro eliminado con &#233;xito',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.INFO
							});
						}
						else {
							Ext.Msg.show({
								title:'Error',
								msg: 'Ha ocurrido un error el registro no fue eliminado',
								buttons: Ext.Msg.OK,
								icon: Ext.MessageBox.ERROR
							});
						}
						limpiarFormulario(formSucursales);
						fieldSetEstructura.limpiarDenominaciones();
				    },
					failure: function ( result, request){ 
						Ext.Msg.show({
							title:'Error',
							msg: 'Ha ocurrido un error en la operaci&#243;n, por favor intente de nuevo',
							buttons: Ext.Msg.OK,
							icon: Ext.MessageBox.ERROR
						});
					} 
				});
			}
			else{
				Ext.Msg.show({
					title:'Advertencia',
					msg: 'Debe seleccionar una sucursal ha ser eliminada',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
				});
			}
		}
	}
	Ext.MessageBox.confirm('Confirmar', '&#191;Desea eliminar este registro&#63;', eliminar);
}

function irBuscar()
{
	//creando datastore y columnmodel para el catalogo de agencias
	var resucursal = Ext.data.Record.create([
						{name: 'codsuc'},
						{name: 'nomsuc'},
						{name: 'codestcomfs0'},
						{name: 'codestcomfs1'},
						{name: 'codestcomfs2'},
						{name: 'codestcomfs3'},
						{name: 'codestcomfs4'},
						{name: 'denestpro1'},
						{name: 'denestpro2'},
						{name: 'denestpro3'},
						{name: 'denestpro4'},
						{name: 'denestpro5'},
						{name: 'estcla'}
		]);
	
	var dssucursal =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},resucursal)
		});
						
	var cmsucursal = new Ext.grid.ColumnModel([
          	{header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codsuc'},
          	{header: "Descripci&#243;n", width: 40, sortable: true, dataIndex: 'nomsuc'}
        ]);
	//fin creando datastore y columnmodel para el catalogo de agencias
	
	comcatsucursal = new com.sigesp.vista.comCatalogo({
		titvencat: 'Catalogo de Sucursales',
		anchoformbus: 450,
		altoformbus:130,
		anchogrid: 450,
		altogrid: 400,
		anchoven: 500,
		altoven: 400,
		datosgridcat: dssucursal,
		colmodelocat: cmsucursal,
		arrfiltro:[{etiqueta:'C&#243;digo',id:'coage',valor:'codage'},
				   {etiqueta:'Descripci&#243;n',id:'noage',valor:'nomage'}],
		rutacontrolador:'../../controlador/cfg/sigesp_ctr_cfg_sucursales.php',
		parametros: 'ObjSon='+Ext.util.JSON.encode({'oper': 'catalogo'}),
		tipbus:'L',
		setdatastyle:'F',
		formulario:formSucursales,
		fieldSetEst:fieldSetEstructura
	});
	
	comcatsucursal.mostrarVentana();
}
