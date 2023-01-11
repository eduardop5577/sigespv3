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

var dsfuentefinanciamiento="";
var grid_fuentefinanciamiento="";
var form_busqueda_fuentefinanciamiento="";
var registro_fuentefinanciamiento="";
var ventana_cat_fuentefinanciamiento="";

function crear_data_store_fuentefinanciamiento()
{
	registro_fuentefinanciamiento = Ext.data.Record.create([
						{name: 'codfuefin'},    
						{name: 'denfuefin'},
						{name: 'expfuefin'}
					]);
	
	var objeto_fuentefinanciamiento={"raiz":[{"codfuefin":'',"denfuefin":'',"expfuefin":''}]};
		
		dsfuentefinanciamiento =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_fuentefinanciamiento),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registro_fuentefinanciamiento
			),
			data: objeto_fuentefinanciamiento
	  	})	
		

		var myJSONObject ={
		"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_fuentefinanciamiento.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objeto_fuentefinanciamiento = eval('(' + datos + ')');
			if(objeto_fuentefinanciamiento!='')
			{
				dsfuentefinanciamiento.loadData(objeto_fuentefinanciamiento);
			}
		}	
	})
}

function act_data_store_fuentefinanciamiento(criterio,cadena)
{
	dsfuentefinanciamiento.filter(criterio,cadena,true,false);
}


function crear_form_busqueda_fuentefinanciamiento()
{
		form_busqueda_fuentefinanciamiento= new Ext.FormPanel({
        labelWidth: 75, // label settings here cascade unless overridden
        url:'save-form.php',
        frame:true,
        title: 'B&uacute;squeda',
        bodyStyle:'padding:5px 5px 0',
        width: 350,
		height:100,
        defaults: {width: 230},
        defaultType: 'textfield',
		items: [{
                fieldLabel: 'C&#243;digo',
                name: 'cod_fuefin',
				id:'cod_fuefin',
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_fuentefinanciamiento('codfuefin',v);
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
		crear_form_busqueda_fuentefinanciamiento();
		crear_data_store_fuentefinanciamiento();
		 
	 grid_fuentefinanciamiento = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: form_busqueda_fuentefinanciamiento,
	 autoScroll:true,
     border:true,
     ds: dsfuentefinanciamiento,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codfuefin'},
          {header: "Descripcion", width: 50, sortable: true, dataIndex: 'denfuefin'}
        ]),
       stripeRows: true,
      viewConfig: {
      	forceFit:true
      }
      });            
} 

function mostrar_catalogo()
{
	
				   CrearGrid();
				   ObjetoFuente='definicion';
                   ventana_cat_fuentefinanciamiento = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo Fuente de Financiamiento',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[grid_fuentefinanciamiento],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = grid_fuentefinanciamiento.getSelectionModel().getSelected();
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
                    	grid_fuentefinanciamiento.destroy();
		      			ventana_cat_fuentefinanciamiento.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	grid_fuentefinanciamiento.destroy();
		      			ventana_cat_fuentefinanciamiento.destroy();
                     }
                    }]
                    
                   });
                  ventana_cat_fuentefinanciamiento.show();       
 }
 
 function pasardatosagridfuentes(registroActual)
{
	registroActual.set('codfuefin',grid_fuentefinanciamiento.getSelectionModel().getSelected().get('codfuefin'));
	
}

 
function mostrarcatalogofuentes(registroActual){

	CrearGrid();
	ventanacatalogo = new Ext.Window(
    	{
        title: 'Cat&#225;logo Fuente de Financiamiento',
		autoScroll:true,
        width:800,
        height:400,
        modal: true,
        closable:false,
        plain: false,
        items:[grid_fuentefinanciamiento],
        buttons: [{
					text:'Aceptar',  
			        handler: function()
			        	{
				        	pasardatosagridfuentes(registroActual);	    
						    ventanacatalogo.hide();
						}
			       },
			       {
			      	text: 'Salir',
			        handler: function()
			      		{
			      			ventanacatalogo.hide();
			       		}
                  }]
                    
                   });
                  ventanacatalogo.show();       

}