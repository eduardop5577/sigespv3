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

var dstipomodificacion="";
var grid_tipomodificacion="";
var form_busqueda_tipomodificacion="";
var registro_tipomodificacion="";
var ventana_cat_tipomodificacion="";

function crear_data_store_tipomodificacion()
{
	registro_tipomodificacion = Ext.data.Record.create([
						{name: 'codtipmodpre'},    
						{name: 'dentipmodpre'},
						{name: 'pretipmodpre'},
						{name: 'contipmodpre'}
					]);
	
	var objeto_tipomodificacion={"raiz":[{"codtipmodpre":'',"dentipmodpre":'',"pretipmodpre":'',"contipmodpre":''}]};
		
		dstipomodificacion =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objeto_tipomodificacion),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
			registro_tipomodificacion
			),
			data: objeto_tipomodificacion
	  	})	
		

		var myJSONObject ={
		"oper": 'catalogo'
		}
		ObjSon=JSON.stringify(myJSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_spg_tipomodificacion.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objeto_tipomodificacion = eval('(' + datos + ')');
			if(objeto_tipomodificacion!='')
			{
				dstipomodificacion.loadData(objeto_tipomodificacion);
			}
		}	
	})
}

function act_data_store_tipomodificacion(criterio,cadena)
{
	dstipomodificacion.filter(criterio,cadena,true,false);
}


function crear_form_busqueda_tipomodificacion()
{
		form_busqueda_tipomodificacion = new Ext.FormPanel({
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
                name: 'cod_tipmodpre',
				id:'cod_tipmodpre',
				changeCheck: function(){
							var v = this.getValue();
							act_data_store_tipomodificacion('codtipmodpre',v);
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
		crear_form_busqueda_tipomodificacion();
		crear_data_store_tipomodificacion();
		 
	 grid_tipomodificacion = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: form_busqueda_tipomodificacion,
	 autoScroll:true,
     border:true,
     ds: dstipomodificacion,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codtipmodpre'},
          {header: "Descripcion", width: 50, sortable: true, dataIndex: 'dentipmodpre'},
          {header: "Prefijo", width: 50, sortable: true, dataIndex: 'pretipmodpre'}
        ]),
       stripeRows: true,
      viewConfig: {
      	forceFit:true
      }
      ,
      });            
} 

function mostrar_catalogo()
{
	
				   CrearGrid();
				   ObjetoFuente='definicion';
                   ventana_cat_tipomodificacion = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo Tipo de Modificaciones Presupuestarias',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[grid_tipomodificacion],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	Registro = grid_tipomodificacion.getSelectionModel().getSelected();
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
                    	grid_tipomodificacion.destroy();
		      			ventana_cat_tipomodificacion.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	grid_tipomodificacion.destroy();
		      			ventana_cat_tipomodificacion.destroy();
                     }
                    }]
                    
                   });
                  ventana_cat_tipomodificacion.show();       
 }