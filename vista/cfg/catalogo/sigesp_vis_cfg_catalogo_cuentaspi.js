/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al catalgo de cuentas presupuestarias de ingreso  
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

var gridcatcuentaspi = null;
var dscatcuentaspi   = null;

function buscarDataCuentaSpi(estructura){
	var cadenajson = "";
	
	if(!estructura){
		cadenajson = "{'operacion':'catspicuenta',"+
					"'codcuenta':'"+Ext.getCmp('catcodspicuenta').getValue()+"',"+
					"'dencuenta':'"+Ext.getCmp('catdenspicuenta').getValue()+"'}";
	}
	else{
		//aqui lo de la estructura
	}	
		
	parametros = 'ObjSon='+cadenajson; 
	Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_catcuentas.php',
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
						dscatcuentaspi.loadData(objetodata);
					}
				}
		}	
	});
}

function getGridCatCuentaSpi(estructura){
	
	//creando formulario de busqueda
	var formbusquedacuentaspi = new Ext.FormPanel({
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 600,
		height:150,
        items: [{
                	xtype:'textfield',
					fieldLabel: 'Cuenta',
                	id:'catcodspicuenta',
					changeCheck: function(){
									var textvalor = this.getValue();
									dscatcuentaspi.filter('spi_cuenta',textvalor,true);
									if(String(textvalor) !== String(this.startValue)){
										this.fireEvent('change', this, textvalor, this.startValue);
									} 
					},							 
					initEvents : function(){
								AgregarKeyPress(this);
					}               
      			},{
			        xtype:'textfield',
					fieldLabel: 'Denominaci&#243;n',
			        id:'catdenspicuenta',
			        width:300,
					changeCheck: function(){
									var v = this.getValue();
									dscatcuentaspi.filter('denominacion',v,true,false);
									if(String(v) !== String(this.startValue)){
											this.fireEvent('change', this, v, this.startValue);
								} 
					},							 
					initEvents : function(){
											AgregarKeyPress(this);
								}
				},{
					xtype:'button',
				 	text: 'Buscar',
					id:'botcatspicuenta',
				   	style:'position:absolute;left:500px;top:80px;',
					iconCls: 'menubuscar',
					handler: function(){
								buscarDataCuentaSpi(estructura);			
					}
				}]
	});
	//fin creando formulario de busqueda
	
	//creando datastore del catalogo
	var registro_cuentaspi = Ext.data.Record.create([
							{name: 'spi_cuenta'},    
							{name: 'denominacion'}
						]);
	
	dscatcuentaspi =  new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},registro_cuentaspi)
	  	});
	//fin creando datastore del catalogo
	
	//creando la grid del catalgo
	gridcatcuentaspi = new Ext.grid.GridPanel({
	 	width:600,
	 	height:300,
	 	tbar: formbusquedacuentaspi,
	 	autoScroll:true,
     	border:true,
     	ds: dscatcuentaspi,
     	cm: new Ext.grid.ColumnModel([
			{header: "Cuenta", width: 30, sortable: true,   dataIndex: 'spi_cuenta'},
          	{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
       	]),
		stripeRows: true,
      	viewConfig: {
      	forceFit:true
    }});
	//fin creando la grid del catalgo
} 

function catalogocuentaspi(estructura,codigo,denominacion){
	getGridCatCuentaSpi(estructura);				   
    var vencatcuentaspi = new Ext.Window({
    	title: 'Cat&#225;logo de cuentas presupuestarias de ingreso',
		autoScroll:true,
        width:700,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridcatcuentaspi],
        buttons: [{
					text:'Aceptar',  
			        handler: function(){
							var selspicuenta = gridcatcuentaspi.getSelectionModel().getSelected();
							Ext.getCmp(codigo).setValue(selspicuenta.get('spi_cuenta'));
							Ext.getCmp(denominacion).setValue(selspicuenta.get('denominacion'));
			        		gridcatcuentaspi.destroy();
							vencatcuentaspi.destroy();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridcatcuentaspi.destroy();
			      			vencatcuentaspi.destroy();
			       		}
                  }]
	});
    vencatcuentaspi.show();
}
