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

var dsplanunico         ="";
var formulario_busqueda ="";
var gridcuenta          ="";
var ventanacatalogo     ="";

function creardatastore()
{

	registro = Ext.data.Record.create([
							{name: 'sc_cuenta'},    
							{name: 'denominacion'}
						]);
	
	var objeto={"raiz":[{"sc_cuenta":'',"denominacion":''}]};
		
		dsplanunico =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registro  
			),
			data: objeto
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo',
			"estatus": 'C'
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_plan_cta_patrimonial.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			info = resultado.responseText;
			var objeto = eval('(' + info + ')');
			if(objeto!='')
			{
				dsplanunico.loadData(objeto);
			}
		}	
	})
}

function act_data_store_cod_denominacion(criterio,cadena)
{
	dsplanunico.filter(criterio,cadena,true,false);
}


function crearFormulario()
{
		formulario_busqueda = new Ext.FormPanel({
        labelWidth: 75,
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 450,
		height:100,
        defaults: {width: 300},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'codigo',
				id:'codigoplanunico',
				width:100,
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_cod_denominacion('sc_cuenta',v);
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
			    name: 'denominacion',
			    id:'denplanunico',
			    changeCheck: function(){
							var v = this.getValue();
							act_data_store_cod_denominacion('denominacion',v);
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


function crear_grid()
{
	crearFormulario();
	creardatastore();
		 
	 gridcuenta = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formulario_busqueda,
	 autoScroll:true,
     border:true,
     ds: dsplanunico,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'sc_cuenta'},
          {header: "Denominaci&#243;n", width: 50, sortable: true, dataIndex: 'denominacion'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      
      });            
} 

function mostrar_catalogo()
{
				   crear_grid();
				   ObjetoFuente='definicion';
                   ventanacatalogo = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo plan &#250;nico de cuentas',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridcuenta],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = gridcuenta.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
                    			limpiarCampos();
	                    		PasDatosGridDef(Registro);
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(Registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(Registro);
	                    	break;
 
                    		
                    	}          
                    	gridcuenta.destroy();
		      			ventanacatalogo.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridcuenta.destroy();
		      			ventanacatalogo.destroy();
                     }
                    }]
                    
                   });
                  ventanacatalogo.show();       
 

 }

function pasardatosagrid(registroActual)
{
	registroActual.set('sc_cuenta',gridcuenta.getSelectionModel().getSelected().get('sc_cuenta'));
	
}

 
function mostrarcatalogocuentas(arrcuentascg){

	crear_grid();
	ventanacatalogo = new Ext.Window(
    	{
        title: 'Cat&#225;logo plan &#250;nico de cuentas',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[gridcuenta],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
				        	for (i = 0; i < arrcuentascg.length; i++) {
								pasardatosagrid(arrcuentascg[i]);
							}	    
						    ventanacatalogo.hide();
						    gridcuenta.destroy();
		      				ventanacatalogo.destroy(); 
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			gridcuenta.destroy();
		      				ventanacatalogo.destroy(); 
			      			ventanacatalogo.hide();
			       		}
                  }]
                    
                   });
                  ventanacatalogo.show();       

}


//var catalogoPersonalizado = mostrarcatalogocuentas(arrcuentascg);

function catalogoPersonalizado(arrcuentasscg)
{
	mostrarcatalogocuentas(arrcuentasscg);
}

//var catalogoPersonalizado = mostrarcatalogocuentas;

