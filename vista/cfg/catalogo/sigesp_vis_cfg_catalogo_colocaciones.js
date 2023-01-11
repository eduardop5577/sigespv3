/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al catalogo de colocaciones   
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var comcampocatbancocol  = null;  //componente campo catalogo bancos
var gridcatcolocacion    = null;  //grid del catalogo
var dscatcolocacion      = null;  //datastore del catalogo 

function buscarDataColocacion(){
	var cadenajson = "{'operacion':'catalogo',"+
					"'ctaban':'"+Ext.getCmp('catctaban').getValue()+"',"+
					"'dencol':'"+Ext.getCmp('catdencol').getValue()+"',"+
					"'nomban':'"+Ext.getCmp('catnomban').getValue()+"'}";
	
	parametros = 'ObjSon='+cadenajson; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_colocacion.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){ 
				var datos = resultado.responseText;
				var objetodata = eval('(' + datos + ')');
				if(objetodata != ''){
					if(objetodata.raiz == null){
						Ext.MessageBox.alert('Informaci&#243;n','No se encontraron datos')
					}
					else{
						dscatcolocacion.loadData(objetodata);
					}
				}
		}	
	});
}

function buscarDetalleColocacion(codban,ctaban,numcol){
	var cadenajson = "{'operacion':'buscardetalle',"+
					"'codban':'"+codban+"',"+
					"'ctaban':'"+ctaban+"',"+
					"'numcol':'"+numcol+"'}";
	
	parametros = 'ObjSon='+cadenajson; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_scb_colocacion.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request){ 
				var datos = resultado.responseText;
				var objetodata = eval('(' + datos + ')');
				if(objetodata != ''){
					if(objetodata.raiz == null){
						Ext.MessageBox.alert('Informaci&#243;n','No se encontraron datos')
					}
					else{
						gridreintegro.store.loadData(objetodata);
					}
				}
		}	
	});
}

function getGridCatColocacion(){
	
	//creando formulario de busqueda
	var formbusquedacolocacion = new Ext.FormPanel({
        frame:true,
        title: 'B&uacute;squeda',
		bodyStyle:'padding:5px 5px 0',
        width: 600,
		height:150,
		items: [{
					xtype:'textfield',
					fieldLabel: 'Cuenta Bancaria',
                	id:'catctaban',
					changeCheck: function(){
								var textvalor = this.getValue();
								dscatcolocacion.filter('ctaban',textvalor,true);
								if(String(textvalor) !== String(this.startValue)){
									this.fireEvent('change', this, textvalor, this.startValue);
								} 
					},							 
					initEvents : function(){
								AgregarKeyPress(this);
					}               
      			},{
					xtype: 'textfield',
					fieldLabel: 'Denominaci&#243;n',
					id: 'catdencol',
					width: 300,
					changeCheck: function(){
								var v = this.getValue();
								dscatcolocacion.filter('dencol', v, true, false);
								if (String(v) !== String(this.startValue)) {
									this.fireEvent('change', this, v, this.startValue);
								}
					},
					initEvents: function(){
								AgregarKeyPress(this);
					}
				},{
					xtype: 'textfield',
					fieldLabel: 'Banco',
					id: 'catnomban',
					width: 200,
					changeCheck: function(){
								var v = this.getValue();
								dscatcolocacion.filter('nomban', v, true, false);
								if (String(v) !== String(this.startValue)) {
									this.fireEvent('change', this, v, this.startValue);
								}
					},
					initEvents: function(){
								AgregarKeyPress(this);
					}
				},{
					xtype:'button',
					id:'botcatcolocacion',
					text: 'Buscar',
					style:'position:absolute;left:500px;top:80px;',
					iconCls: 'menubuscar',
					handler: function(){
							buscarDataColocacion();
					}
				}]
	})
	//fin creando formulario de busqueda
	
	//creando datastore del catalogo
	var registro_colocacion = Ext.data.Record.create([
							{name: 'codban'},
							{name: 'ctaban'},
							{name: 'nomban'},
							{name: 'numcol'},    
							{name: 'dencol'},
							{name: 'codtipcol'},
							{name: 'nomtipcol'},
							{name: 'feccol'},
							{name: 'diacol'},
							{name: 'tascol'},
							{name: 'monto'},
							{name: 'fecvencol'},
							{name: 'monint'},
							{name: 'sc_cuenta'},
							{name: 'spi_cuenta'},
							{name: 'sc_cuentacob'},
							{name: 'estreicol'},
							{name: 'codestpro1'},
							{name: 'codestpro2'},
							{name: 'codestpro3'},
							{name: 'codestpro4'},
							{name: 'codestpro5'},
							{name: 'estcla'},
							{name: 'scgctadeno'},
							{name: 'dencta'},
							{name: 'spictadeno'},
							{name: 'denocob'}
						]);
	
	dscatcolocacion =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_colocacion)
	  	});
	//fin creando datastore del catalogo
	
	//creando la grid del catalgo
	gridcatcolocacion = new Ext.grid.GridPanel({
	 	width:600,
	 	height:300,
	 	tbar: formbusquedacolocacion,
	 	autoScroll:true,
     	border:true,
     	ds: dscatcolocacion,
     	cm: new Ext.grid.ColumnModel([
			{header: "N&uacute;mero", width: 30, sortable: true,   dataIndex: 'numcol'},
          	{header: "Denominaci&#243;n", width: 40, sortable: true, dataIndex: 'dencol'},
			{header: "Banco", width: 40, sortable: true, dataIndex: 'nomban'},
			{header: "Cuenta", width: 40, sortable: true, dataIndex: 'ctaban'},
			{header: "Monto", width: 40, sortable: true, dataIndex: 'monto'},
			{header: "Intereses", width: 40, sortable: true, dataIndex: 'monint'},
			{header: "Tasa", width: 40, sortable: true, dataIndex: 'tascol'},
			{header: "Plazo", width: 20, sortable: true, dataIndex: 'diacol'},
			{header: "Estatus", width: 40, sortable: true, dataIndex: 'estreicol'}
			
       	]),
		stripeRows: true,
      	viewConfig: {
      	forceFit:true
    }});
	//fin creando la grid del catalgo
} 

function catalogoColocacion(){
	getGridCatColocacion();				   
    var vencatcolocacion = new Ext.Window({
    	title: 'Cat&#225;logo de colocaciones',
		autoScroll:true,
        width:700,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridcatcolocacion],
        buttons: [{
					text:'Aceptar',  
			        handler: function(){
							var registro = gridcatcolocacion.getSelectionModel().getSelected();
							setDataFrom(formcolocacion,registro);
							buscarDetalleColocacion(registro.get('codban'),registro.get('ctaban'),registro.get('numcol'))
							gridcatcolocacion.destroy();
			      			vencatcolocacion.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridcatcolocacion.destroy();
			      			vencatcolocacion.destroy();
			       		}
                  }]
	});
    vencatcolocacion.show();
}
