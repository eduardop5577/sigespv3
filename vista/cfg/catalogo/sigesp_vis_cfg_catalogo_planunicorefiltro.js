/***********************************************************************************
* @Archivo JavaScript que incluye tanto los componentes como los eventos asociados 
* al catalogo de cuentas presupuestarias aplicando filtros segun la configuracion 
* de la empresa  
* @fecha de modificacion: 03/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

function catalogoPlanunicorefiltro(operacion,objeto) {
	
	var rePlanunicorefiltro = Ext.data.Record.create([
		{name: 'sig_cuenta'},    
		{name: 'denominacion'},
		{name: 'sc_cuenta'}
	]);
	
	var dsPlanunicorefiltro =  new Ext.data.Store({
		reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},rePlanunicorefiltro)
	})
	
	var formBusquedaPlanunicoregasto = new Ext.FormPanel({
		frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 750,
		height: 130,
        items: [{
			xtype: 'textfield',
			fieldLabel: 'Cuenta',
            labelSeparator : '',
            id:'codigoplanunicore',
            autoCreate: {tag: 'input', type: 'text', maxlength: 25},
			changeCheck: function(){
				var v = this.getValue();
				dsPlanunicorefiltro.filter('sig_cuenta',v);
				if(String(v) !== String(this.startValue))
				{
					this.fireEvent('change', this, v, this.startValue);
				} 
			},							 
			initEvents : function()
			{
				AgregarKeyPress(this);
			}               
      	},{
      		xtype:  'textfield',
            fieldLabel: 'Denominaci&#243;n',
            labelSeparator : '',
            autoCreate: {tag: 'input', type: 'text', maxlength: 254},
            id:'denplanunicore',
            width:500,
			changeCheck: function(){
				var v = this.getValue();
				dsPlanunicorefiltro.filter('denominacion',v,true,false);
				if(String(v) !== String(this.startValue))
				{
					this.fireEvent('change', this, v, this.startValue);
				} 
			},							 
			initEvents : function() {
				AgregarKeyPress(this);
			}
		},{
			xtype: 'button',
		   	fieldLabel: '',
		   	id: 'btbuscar',
		   	text: 'Buscar',
		   	style:'position:absolute;left:550px;top:70px;',
		   	iconCls: 'menubuscar',
		   	handler: function()
			{
		   		obtenerMensaje('procesar','','Buscando Datos');
		   					
	   			var JSONObject = {
	   				'oper'   : operacion,
   					'codcue' : Ext.getCmp('codigoplanunicore').getValue(),
   					'dencue' : Ext.getCmp('denplanunicore').getValue()
   				}
	   				
			   	var ObjSon = JSON.stringify(JSONObject);
   				var parametros = 'ObjSon='+ObjSon; 
   				Ext.Ajax.request({
   					url : '../../controlador/cfg/sigesp_ctr_cfg_scg_planunico.php',
   					params : parametros,
   					method: 'POST',
   					success: function ( resultado, request)
					{
   						Ext.Msg.hide();
   						var datos = resultado.responseText;
   						var objData = eval('(' + datos + ')');
   						if(objData!='')
						{
   							if(objData.raiz == null || objData.raiz =='')
							{
   								Ext.MessageBox.show({
					 				title:'Advertencia',
					 				msg:'No existen datos para mostrar',
					 				buttons: Ext.Msg.OK,
					 				icon: Ext.MessageBox.WARNING
					 			});
							}
							else
							{
								dsPlanunicorefiltro.loadData(objData);
							}
   						}
   					}//fin del success	
   				});//fin del ajax request
		   	}
		}]
	});
	
	
	
	var gridPlanunicore = new Ext.grid.GridPanel({
	 	width:750,
	 	height:320,
	 	tbar: formBusquedaPlanunicoregasto,
	 	autoScroll:true,
     	border:true,
     	ds: dsPlanunicorefiltro,
     	cm: new Ext.grid.ColumnModel([new Ext.grid.CheckboxSelectionModel({}),
          	{header: "Cuenta", width: 30, sortable: true,   dataIndex: 'sig_cuenta'},
          	{header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
       	]),
		sm:new Ext.grid.CheckboxSelectionModel({}),
       	stripeRows: true,
      	viewConfig: {forceFit:true}
	});
	
	function pasarDatosGridPlancta(grid,registro)
	{
		var registrocuentacat = Ext.data.Record.create([
				{name: 'sig_cuenta'},     
				{name: 'denominacion'},
				{name: 'sc_cuenta'},
                                {name: 'cueclaeco'},
				{name: 'editable'}
			]);
			
		cuentapre = new registrocuentacat
			({
				'sig_cuenta':'',
				'denominacion':'',
				'sc_cuenta':'',
                                'cueclaeco':'-',
				'editable':'1'
			});
		grid.store.insert(0,cuentapre);
		cuentapre.set('sig_cuenta',registro.get('sig_cuenta'));
		cuentapre.set('denominacion',registro.get('denominacion'));
		if(registro.get('sc_cuenta')!='')
		{
			cuentapre.set('sc_cuenta',registro.get('sc_cuenta'));
		}
	}
	
	var ventanaPlanunicoregasto = new Ext.Window({
		title: 'Cat&#225;logo de cuentas del plan &#250;nico de recursos y egresos',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridPlanunicore],
        buttons: [{
        	text:'Aceptar',  
			handler: function()
			{
				arreglocuenta = gridPlanunicore.getSelectionModel().getSelections();
                for (i=0; i<arreglocuenta.length; i++)
				{
					validacuenta = arreglocuenta[i];
					if(validarExistenciaRegistroGrid(validacuenta,objeto,'sig_cuenta','sig_cuenta',true))
					{
						pasarDatosGridPlancta(objeto,arreglocuenta[i]);
					}
				}
				gridPlanunicore.destroy();
				ventanaPlanunicoregasto.destroy();
			}
		},
		{
			text: 'Salir',
			handler: function()
			{
				gridPlanunicore.destroy();
			    ventanaPlanunicoregasto.destroy();
			}
        }]
	});
	ventanaPlanunicoregasto.show();
} 



function catalogoplanunicoregasto(objeto)
{
	catalogoPlanunicorefiltro('catalogogastos',objeto);				       
}
 
function catalogoplanunicoreingreso(objeto)
{
	catalogoPlanunicorefiltro('catalogoingresos',objeto);				   
}