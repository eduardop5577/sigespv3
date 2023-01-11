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

var dataStoreMunicipio=""; //datastore del catalogo
var formBusquedaMunicipio=""; //formulario de busqueda del catalogo
var gridMunicipio=""; //grid del catalogo
var ventanaMunicipio=""; //ventana en donde se ubica el catalogo


function crearDataStoreMunicipio()
{

	registroMunicipio = Ext.data.Record.create([
							{name: 'codmun'},
							{name: 'denmun'}
						]);
	
	var objetoMunicipio={"raiz":[{"codmun":'',"denmun":''}]};
		
		dataStoreMunicipio =  new Ext.data.Store({
			proxy: new Ext.data.MemoryProxy(objetoMunicipio),
			reader: new Ext.data.JsonReader({
			root: 'raiz',             
			id: "id"   
			}
			,
		    registroMunicipio  
			),
			data: objetoMunicipio
	  	})	
		
		var JSONObject ={
			"oper": 'catalogo',
			"codpai": codpai,
			"codest": codest
		}
		ObjSon=JSON.stringify(JSONObject);
		parametros = 'ObjSon='+ObjSon; 
		Ext.Ajax.request({
		url : '../../controlador/cfg/sigesp_ctr_cfg_municipio.php',
		params : parametros,
		method: 'POST',
		success: function ( resultado, request) 
		{ 
			datos = resultado.responseText;
			var objetoMunicipio = eval('(' + datos + ')');
			if(objetoMunicipio.raiz == null)
			{
				obtenerMensaje('informacion',ventanaMunicipio.destroy(),'Mensaje','No se encontro informacion, con los parametros de busqueda seleccionados')	
			}
			else
			{
			 if(objetoMunicipio!='')
			 {
				dataStoreMunicipio.loadData(objetoMunicipio);
			 }
			}
		}	
	})
}

function actDataStoreMunicipio(criterio,cadena)
{
	dataStoreMunicipio.filter(criterio,cadena,true,false);
}


function crearFormBusquedaMunicipio()
{
		formBusquedaMunicipio = new Ext.FormPanel({
        labelWidth: 80, // label settings here cascade unless overridden
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
                name: 'cod',
				id:'cod',
				width: 100,
				changeCheck: function(){
							var v = this.getValue();
							actDataStoreMunicipio('codmun',v);
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
			    fieldLabel: 'Municipio',
			    name: 'den',
			    id:'den',
				changeCheck: function()
							{
							var v = this.getValue();
							actDataStoreMunicipio('denmun',v);
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

function crearGridCatalogoMunicipio()
{
	crearFormBusquedaMunicipio();
	crearDataStoreMunicipio();
		 
	 gridMunicipio = new Ext.grid.GridPanel({
	 width:770,
	 height:400,
	 tbar: formBusquedaMunicipio,
	 autoScroll:true,
     border:true,
     ds: dataStoreMunicipio,
     cm: new Ext.grid.ColumnModel([
          {header: "C&#243;digo", width: 30, sortable: true,   dataIndex: 'codmun'},
          {header: "Municipios", width: 30, sortable: true,   dataIndex: 'denmun'}
       ]),
       	stripeRows: true,
      	viewConfig: {
      	forceFit:true
      }
      });            
} 

function pasarDatosMunicipio(municipio)
{
	Ext.getCmp('codmun').setValue(municipio.get('codmun'));
	Ext.getCmp('denmun').setValue(municipio.get('denmun'));
	Ext.getCmp('codpai').disable();
	Ext.getCmp('codest').disable();
	Ext.getCmp('codmun').disable();
	Actualizar=true;
}

function irBuscar()
{
			   if((codpai==null)||(codpai=='')||(codest==null)||(codest==''))
			   {
			    Ext.Msg.show({
			    title:'Mensaje',
			    msg: 'Debe seleccionar un pais y un estado, verifique por favor!!',
			    buttons: Ext.Msg.OK,
			    animEl: 'elId',
			    icon: Ext.MessageBox.INFO
				});
			   }
			   else
			   {
				   
				   crearGridCatalogoMunicipio();
				   ObjetoFuente='definicion';
                   ventanaMunicipio = new Ext.Window(
                   {
                    //layout:'fit',
                    title: 'Cat&#225;logo de municipios',
		    		autoScroll:true,
                    width:800,
                    height:400,
                    modal: true,
                    closable:false,
                    plain: false,
                    items:[gridMunicipio],
                    buttons: [{
                    text:'Aceptar',  
                    handler: function()
                    { 
                    	registro = gridMunicipio.getSelectionModel().getSelected();
                    	switch(ObjetoFuente)   
                    	{
                    		case 'definicion':
	                    		pasarDatosMunicipio(registro);
								Actualizar=1;
	                    	break;
                    		case 'grid':
		                    	PasDatosGridGrid(registro);
	                    	break;
                    		case 'objeto':
	                    		PasDatosGridObj(registro);
	                    	break;
 
                    		
                    	}          
                    	gridMunicipio.destroy();
		      			ventanaMunicipio.destroy();                      
                    }
                    }
                    ,
                    {
                     text: 'Salir',
                     handler: function()
                     {
                     	
                      	gridMunicipio.destroy();
		      			ventanaMunicipio.destroy();
                     }
                    }]
                    
                   });
                  ventanaMunicipio.show(); 
			   }
 

 }