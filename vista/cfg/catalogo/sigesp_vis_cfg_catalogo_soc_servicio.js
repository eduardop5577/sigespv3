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

var dsServicio="";
var formbusquedaservicio="";

function crearDsServicio()
{

}

function mostrarCatalogoServicio() {
	
	var reServicio = Ext.data.Record.create([
		{name: 'codser'},    
		{name: 'codtipser'},
		{name: 'dentipser'},
		{name: 'denser'},
		{name: 'preser'},
		{name: 'spg_cuenta'},
		{name: 'denominacion'},
		{name: 'codunimed'},
		{name: 'denunimed'}
	]);	
	
	var dsServicio =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz', id: "id"},reServicio)			
	});	
		
	var JSONObject ={
		"oper": 'catalogo'
	}
	var ObjSon=JSON.stringify(JSONObject);
	var parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_soc_servicio.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) {
			var datos = resultado.responseText;
			var objetoservicio = eval('(' + datos + ')');
			if(objetoservicio!='')
			{
				dsServicio.loadData(objetoservicio);
			}
		}	
	});
	
	var formbusquedaservicio = new Ext.FormPanel({
		labelWidth: 90, 
        frame:true,
        title: 'B&#250;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 600,
		height:100,
        defaultType: 'textfield',
		items: [{
			fieldLabel: 'C&#243;digo',
            name: 'cod',
			id:'cod',
			width:100,
			labelSeparator : '',
			autoCreate: {tag: 'input', type: 'text', maxlength: 10},
			changeCheck: function(){
				var v = this.getValue();
				dsServicio.filter('codser',v,true,false);
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}               
      	},{
      		fieldLabel: 'Denominaci&#243;n',
            name: 'den',
            id:'den',
            width:400,
            labelSeparator : '',
			autoCreate: {tag: 'input', type: 'text', maxlength: 254},
			changeCheck: function() {
				var v = this.getValue();
				dsServicio.filter('denser',v,true,false); 
			},							 
			initEvents : function(){
				AgregarKeyPress(this);
			}
        }]
	});
	
	var gridServicio = new Ext.grid.GridPanel({
		width:700,
	 	height:350,
	 	tbar: formbusquedaservicio,
	 	autoScroll:true,
     	border:true,
     	ds: dsServicio,
     	cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 20, sortable: true,   dataIndex: 'codser'},
          {header: "Denominaci&#243;n", width: 80, sortable: true, dataIndex: 'denser'}
       	]),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
	});
	
	gridServicio.on({
		'celldblclick': {
			fn: function(){
				var registro = gridServicio.getSelectionModel().getSelected();
            	Ext.getCmp('codser').setValue(registro.get('codser'));
				Ext.getCmp('codtipser').setValue(registro.get('codtipser'));
				Ext.getCmp('dentipser').setValue(registro.get('dentipser'));
				Ext.getCmp('denser').setValue(registro.get('denser'));
				Ext.getCmp('preser').setValue(registro.get('preser'));
				Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
				Ext.getCmp('denominacionspg').setValue(registro.get('denominacion'));
				Ext.getCmp('codunimed').setValue(registro.get('codunimed'));
				Ext.getCmp('denunimed').setValue(registro.get('denunimed'));
       			cargarDetalleCargos(registro);
				Actualizar=true;
                gridServicio.destroy();
		    	ventanaservicio.destroy();
			}
		}
	});
	
    var ventanaservicio = new Ext.Window({
    	title: 'Cat&#225;logo de servicios',
		autoScroll:true,
        width:750,
        height:450,
        modal: true,
        closable:false,
        plain: false,
        items:[gridServicio],
        buttons: [{
        	text:'Aceptar',  
            handler: function() {
            	var registro = gridServicio.getSelectionModel().getSelected();
            	Ext.getCmp('codser').setValue(registro.get('codser'));
				Ext.getCmp('codtipser').setValue(registro.get('codtipser'));
				Ext.getCmp('dentipser').setValue(registro.get('dentipser'));
				Ext.getCmp('denser').setValue(registro.get('denser'));
				Ext.getCmp('preser').setValue(registro.get('preser'));
				Ext.getCmp('spg_cuenta').setValue(registro.get('spg_cuenta'));
				Ext.getCmp('denominacionspg').setValue(registro.get('denominacion'));
				Ext.getCmp('codunimed').setValue(registro.get('codunimed'));
				Ext.getCmp('denunimed').setValue(registro.get('denunimed'));
       			cargarDetalleCargos(registro);
				Actualizar=true;
                gridServicio.destroy();
		    	ventanaservicio.destroy();                      
			}
		},{
			text: 'Salir',
			handler: function(){
				gridServicio.destroy();
		      	ventanaservicio.destroy();
			}
		}]
	});
    ventanaservicio.show();       
 }
 
function cargarDetalleCargos(registro)
{
	var myJSONObject ={
			'oper': 'detalles_cargos',
			'codser':registro.get('codser')					
	};
	ObjSon=Ext.util.JSON.encode(myJSONObject);
	parametros = 'ObjSon='+ObjSon;
	Ext.Ajax.request({
		url : ruta,
		params : parametros,
		method: 'POST',
		success: function (resultado,request)
		{
			datos = resultado.responseText;
			if (datos!='')
			{
				var objetoDetalle = eval('(' + datos + ')');
				if(objetoDetalle != '')
				{
					gridDetalles.store.loadData(objetoDetalle);
				}
				else
				{
					Ext.MessageBox.alert('Error', datos.raiz[0].mensaje+' Al cargar los detalles.');
				}
			}
		}
	});
}
