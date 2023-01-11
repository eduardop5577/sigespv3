/***********************************************************************************
* @fecha de modificacion: 08/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var plInterfaz = null;
var gridCuenta = null;

barraherramienta    = true;
Ext.onReady(function() {
	Ext.BLANK_IMAGE_URL = '../../base/librerias/js/ext/resources/images/default/s.gif';
	
	//combo actividad
    var actividad = [['Migrar a Fonas', 'M'], ['baja en FONAS', 'B']]
    var stActividad = new Ext.data.SimpleStore({
        fields: ['descripcion', 'valor'],
        data: actividad
    });
    
    var cmbActividad = new Ext.form.ComboBox({
		store: stActividad,
		fieldLabel:'Actividad',
		labelSeparator: '',
		displayField:'descripcion',
		valueField:'valor',
        id:'codact',
        //listWidth : 250,
        forceSelection: true,  
        typeAhead: true,
        mode: 'local',
        binding:true,
        editable: false,
        triggerAction: 'all'
	});
    //fin combo actividad
	
	//registro y store de la grid de comprobante
	var rePersonal = Ext.data.Record.create([
	    {name: 'codper'},
	    {name: 'cedper'},
	    {name: 'nomper'},
	    {name: 'apeper'},
	    {name: 'dirper'},
	    {name: 'fecnacper'},
	    {name: 'edocivper'},
	    {name: 'telhabper'},
	    {name: 'telmovper'},
	    {name: 'sexper'},
	    {name: 'estaper'},
	    {name: 'pesper'},
	    {name: 'nacper'},
	    {name: 'coreleper'},
	    {name: 'codtippersss'},
	    {name: 'lugnac'},
	    {name: 'codper'},
	    {name: 'numfam'},
	    {name: 'migfonas'}
	]);
	
	var dsPersonal =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rePersonal)
	});
	
	var cmPersonal = new Ext.grid.ColumnModel([
	    new Ext.grid.CheckboxSelectionModel(),                                          
	    {header: "Cedula", width: 20, sortable: true, dataIndex: 'cedper'},
	    {header: "Nombre", width: 40, sortable: true, dataIndex: 'nomper'},
	    {header: "Apellido", width: 40, sortable: true, dataIndex: 'apeper'},
	    {header: "Cant. Familiares", width: 15, sortable: true, dataIndex: 'numfam'}
	]);
	
	//Grid de personal
	gridPersonal = new Ext.grid.GridPanel({
		title: "<H1 align='center'>Data Personal a Migrar</H1>",
		width:800,
	    height:250,
	    frame:true,
	    style: 'position:absolute;left:15px;top:140px',
	    ds: dsPersonal,
       	cm: cmPersonal,
       	sm: new Ext.grid.CheckboxSelectionModel({}),
		viewConfig: {forceFit:true},
        columnLines: true
    });
	
	
	//PANEL PRINCIPAL CONFIGURACION INTERFAZ SIGESP - FONAS
	plInterfazFONAS = new Ext.FormPanel({
		title: "<H1 align='center'>Interfaz SIGESP - FONAS</H1>",
		style: 'position:relative;top:35px;left:100px', 
		height: 460,
		width: 850,
	   	applyTo:'formIntSigGes',
	   	frame: true,
	   	defaults: {labelWidth: 70},
	   	items:[{
	   		layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:20px',
			items: [{
				layout: "form",
				border: false,
				items: [cmbActividad]
			}]
		},{
	   		layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:50px',
			items: [{
				layout: "form",
				border: false,
				items: [{
					xtype:"textfield",
    				fieldLabel:"Cedula",
					labelSeparator :'',
    				width:80,
					id:"cedper"
				}]
			}]
		},{
	   		layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:80px',
			items: [{
				layout: "form",
				border: false,
				items: [{
					xtype:"textfield",
    				fieldLabel:"Nombre",
					labelSeparator :'',
    				width:300,
					id:"nomper"
				}]
			}]
		},{
	   		layout: "column",
			defaults: {border: false},
			style: 'position:absolute;left:15px;top:110px',
			items: [{
				layout: "form",
				border: false,
				items: [{
					xtype:"textfield",
    				fieldLabel:"Apellido",
					labelSeparator :'',
    				width:300,
					id:"apeper"
				}]
			}]
		},
	   	gridPersonal]
	});
});

function irCancelar() {
	limpiarFormulario(plInterfazFONAS);
	gridPersonal.store.removeAll();
}

function irBuscar() {
	var myJSONObject = {"operacion":"BUS_PER",
						"codact":Ext.getCmp('codact').getValue(),
						"cedper":Ext.getCmp('cedper').getValue(),
						"nomper":Ext.getCmp('nomper').getValue(),
						"apeper":Ext.getCmp('apeper').getValue()};
	var ObjSon = Ext.util.JSON.encode(myJSONObject);
	var parametros ='ObjSon='+ObjSon;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_intsigfonas.php',
		params: parametros,
		method: 'POST',
		success: function ( result, request ) {
			var datos = result.responseText;
			var objData = eval('(' + datos + ')');
			if(objData.raiz == null || objData.raiz ==''){
				Ext.MessageBox.show({
					title:'Advertencia',
					msg:'No existen datos para mostrar',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.WARNING
 				});
			}
			else{
				gridPersonal.store.loadData(objData);
			}
		},
		failure: function ( result, request){ 
			Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		}
	});
}

function irProcesar() {
	var arrPersonal = gridPersonal.getSelectionModel().getSelections();
	var cadenaJson  = "{'operacion':'PRO_PER','codact':'"+Ext.getCmp('codact').getValue()+"','arrPersonal':[";  
	for ( var int = 0; int < arrPersonal.length; int++) {
		var dt = new Date(arrPersonal[int].get('fecnacper'));
		var fechaNacPer = dt.format('Y-m-d');
		if (int == 0) {
			cadenaJson += "{'cedper':'"+arrPersonal[int].get('cedper')+"','nomper':'"+arrPersonal[int].get('nomper')+"'," +
					      " 'apeper':'"+arrPersonal[int].get('apeper')+"','dirper':'"+arrPersonal[int].get('dirper')+"'," +
					      " 'fecnacper':'"+fechaNacPer+"','edocivper':'"+arrPersonal[int].get('edocivper')+"'," +
					      " 'telhabper':'"+arrPersonal[int].get('telhabper')+"','telmovper':'"+arrPersonal[int].get('telmovper')+"'," +
					      " 'sexper':'"+arrPersonal[int].get('sexper')+"','estaper':'"+arrPersonal[int].get('estaper')+"'," +
					      " 'pesper':'"+arrPersonal[int].get('pesper')+"','nacper':'"+arrPersonal[int].get('nacper')+"'," +
					      " 'coreleper':'"+arrPersonal[int].get('coreleper')+"','codtippersss':'"+arrPersonal[int].get('codtippersss')+"'," +
					      " 'lugnac':'"+arrPersonal[int].get('lugnac')+"','codper':'"+arrPersonal[int].get('codper')+"'," +
					      " 'migfonas':'"+arrPersonal[int].get('migfonas')+"'}";
		}
		else {
			cadenaJson += ",{'cedper':'"+arrPersonal[int].get('cedper')+"','nomper':'"+arrPersonal[int].get('nomper')+"'," +
		      			  " 'apeper':'"+arrPersonal[int].get('apeper')+"','dirper':'"+arrPersonal[int].get('dirper')+"'," +
		      			  " 'fecnacper':'"+fechaNacPer+"','edocivper':'"+arrPersonal[int].get('edocivper')+"'," +
		      			  " 'telhabper':'"+arrPersonal[int].get('telhabper')+"','telmovper':'"+arrPersonal[int].get('telmovper')+"'," +
		      			  " 'sexper':'"+arrPersonal[int].get('sexper')+"','estaper':'"+arrPersonal[int].get('estaper')+"'," +
		      			  " 'pesper':'"+arrPersonal[int].get('pesper')+"','nacper':'"+arrPersonal[int].get('nacper')+"'," +
		      			  " 'coreleper':'"+arrPersonal[int].get('coreleper')+"','codtippersss':'"+arrPersonal[int].get('codtippersss')+"'," +
		      			  " 'lugnac':'"+arrPersonal[int].get('lugnac')+"','codper':'"+arrPersonal[int].get('codper')+"'," +
		      			  " 'migfonas':'"+arrPersonal[int].get('migfonas')+"'}";
		}
	}
	cadenaJson  += "]}";
	var parametros ='ObjSon='+cadenaJson;
	Ext.Ajax.request({
		url: '../../controlador/mis/sigesp_ctr_mis_intsigfonas.php',
		params: parametros,
		timeout: 9999999999,
		method: 'POST',
		success: function ( result, request ) {
			var respuesta = result.responseText;
			var datajson = eval('(' + respuesta + ')');
			if(datajson.raiz.valido==true){
				Ext.Msg.show({
					title:'Mensaje',
					msg: 'Los datos fueron procesados correctamente',
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.INFO
				});
				irCancelar();
			}
			else {
				Ext.Msg.show({
					title:'Mensaje',
					msg: datajson.raiz.mensaje,
					buttons: Ext.Msg.OK,
					icon: Ext.MessageBox.ERROR
				});
			}
		},
		failure: function ( result, request){ 
				Ext.MessageBox.alert('Error', 'Error de comunicacion con el servidor'); 
		}
	});
}