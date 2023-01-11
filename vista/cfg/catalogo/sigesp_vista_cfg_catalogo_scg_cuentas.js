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

var dsscgcuentas             ="";
var grid_scgcuentas          ="";
var form_busqueda_scgcuentas ="";
var registro_scgcuentas      ="";
var ventana_cat_scgcuentas   = "";

function crear_data_store_scg_cuenta()


{
	registro_scgcuentas = Ext.data.Record.create([
						{name: 'sc_cuenta'},    
						{name: 'denominacion'}
					]);
	
	var objeto_scgcuentas={"raiz":[{"sc_cuenta":'',"denominacion":''}]};		
		dsscgcuentas =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_scgcuentas),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registro_scgcuentas  
			),
			data: objeto_scgcuentas
	  	})	
		

		var myJSONObject ={
		"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_cuenta_contable.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objeto_scgcuentas = eval('(' + datos + ')');
			if(objeto_scgcuentas!='')
			{
				dsscgcuentas.loadData(objeto_scgcuentas);
			}
		}	
	})
}

function act_data_store_scg_cuentas(criterio,cadena)
{
	dsscgcuentas.filter(criterio,cadena,true,false);
}


function crear_form_busqueda_scgcuentas()
{
		form_busqueda_scgcuentas = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 650,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cuenta',
				id:'sc_cuenta',
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_scg_cuentas('sc_cuenta',v);
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
			                fieldLabel: 'Denominaci&#243;n',
			                name: 'den',
			                id:'denominacion',
			                width:500,
							changeCheck: function()
							{
										var v = this.getValue();
										act_data_store_scg_cuentas('denominacion',v);
										if(String(v) !== String(this.startValue))
										{
											this.fireEvent('change', this, v, this.startValue);
										} 
										},							 
										initEvents : function()
										{
											AgregarKeyPress(this);
										}
			            }]
					});				  			  

}


function CrearGrid()
{
		crear_form_busqueda_scgcuentas();
		crear_data_store_scg_cuenta();
		 
	 grid_scgcuentas = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: form_busqueda_scgcuentas,
	 autoScroll:true,
     border:true,
     ds: dsscgcuentas,
     cm: new Ext.grid.ColumnModel([
          {header: "Cuenta", width: 30, sortable: true,   dataIndex: 'sc_cuenta'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
       ]),
       stripeRows: true,
      viewConfig: {
      	forceFit:true
      }
      });            
} 

function pasardatosagrid(registroActual)
{
	registroActual.set('sc_cuenta',grid_scgcuentas.getSelectionModel().getSelected().get('sc_cuenta'));
	
}


function catalogoCuentaContable(registroActual)
{
	
	CrearGrid();
	ventana_cat_scgcuentas = new Ext.Window(
    {
    	title: 'Cat&#225;logo de cuentas contables',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[grid_scgcuentas],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
				        	pasardatosagrid(registroActual);	    
						    ventana_cat_scgcuentas.hide();
						    grid_scgcuentas.destroy();
		      				ventana_cat_scgcuentas.destroy(); 
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			grid_scgcuentas.destroy();
		      				ventana_cat_scgcuentas.destroy(); 
			      			ventana_cat_scgcuentas.hide();
			       		}
                  }]
                    
		});
        ventana_cat_scgcuentas.show();       
}