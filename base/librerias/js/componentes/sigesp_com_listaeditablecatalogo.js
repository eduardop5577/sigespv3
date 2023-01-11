/***********************************************************************************
* @Archivo JavaScript que incluye el componente lista editable catalogo 
* @fecha de modificacion: 05/08/2022, para la version de php 8.1 
* @autor: Ing. Yesenia Moreno 0412-5191342 / 0424-5575862 lang.solucionesintegrales@gmail.com
* @autor: Ing. Luis Anibal Lang 0412-2880716 lang.solucionesintegrales@gmail.com
* @autor: SIGESP C.A. 58 251 254.06.68 / 254.38.76 
* ********************************************
* @fecha modificacion  
* @autor 
* @descripcion  
***********************************************************************************/

Ext.namespace('com.sigesp.vista');
var copiadatastorecatalogo = '';

com.sigesp.vista.comListaEditableCatalogo = function(options){
	
	this.dataStoreCatalogo = options.datosgridcat;
	
	
	if(options.guardarEliminados){
		this.dataStoreEliminados = 	 new Ext.data.Store({
			reader: new Ext.data.JsonReader({root: 'raiz',id: "id"},options.rgeliminar)
		});
	}
		
	//Creando el Json para la configuracion de los items del formulario de busqueda
	var cadenafiltro="[";
	for (var i = 0; i < options.arrfiltro.length; i++) {
       	if(i==options.arrfiltro.length-1){
			cadenafiltro =  cadenafiltro + "{fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"',"+
							"autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"'},"+
							"width: "+validarAncho(options.arrfiltro[i].ancho)+","+
							"changeCheck: function(){"+
							"var valor = this.getValue();"+
							"copiadatastorecatalogo.filter('"+options.arrfiltro[i].valor+"',valor,true,false);"+
							"if(String(valor) !== String(this.startValue)){"+
								"this.fireEvent('change', this, valor, this.startValue);"+
							"}"+ 
							"},"+								 
							"initEvents : function(){"+
								"AgregarKeyPress(this);"+
							"}"+              
    						"}";
		}else{
			cadenafiltro =  cadenafiltro + "{fieldLabel:'"+options.arrfiltro[i].etiqueta+"',id:'"+options.arrfiltro[i].id+"',"+
							"autoCreate: {tag: 'input', type: 'text', maxlength: '"+validarLongitud(options.arrfiltro[i].longitud)+"'},"+
							"width: "+validarAncho(options.arrfiltro[i].ancho)+","+				
							"changeCheck: function(){"+
							"var valor = this.getValue();"+
							"copiadatastorecatalogo.filter('"+options.arrfiltro[i].valor+"',valor,true,false);"+
							"if(String(valor) !== String(this.startValue)){"+
								"this.fireEvent('change', this, valor, this.startValue);"+
							"}"+ 
							"},"+							 
							"initEvents : function(){"+
								"AgregarKeyPress(this);"+
							"}"+               
     						"},";
		}
	}
	cadenafiltro=  cadenafiltro + "]";
	var objetofiltro = Ext.util.JSON.decode(cadenafiltro);
	//Fin creando el Json para la configuracion de los items del formulario de busqueda
	
	//Inicio de la funcion que retorna la longitud del textfield
	function validarLongitud(valor){
		if(valor!=undefined){
			return valor;
		}
		else{
			return '150';
		}
	}
	//Fin de la funcion que retorna la longitud del textfield
	
	//Inicio de la funcion que retorna el ancho del textfield
	function validarAncho(valor){
		if(valor!=undefined){
			return valor;
		}
		else{
			return '200';
		}
	}
	//Fin de la funcion que retorna el ancho del textfield
	
	//Creando el fieldset del formBusquedaCat
	this.fieldcatalogo = new Ext.form.FieldSet({
		xtype:"fieldset", 
		title:'B&#250;squeda',
		width: options.anchoformbus-25,
		height:options.altoformbus-15,
		border:true,
		defaultType: 'textfield',
		style: 'position:absolute;left:5px;top:5px',
    	defaults: {width: 230, labelSeparator:''},
		cls:'fondo',
		items: objetofiltro
	})
	//Fin del fieldset del formBusquedaCat
		
	//Creando formulario de busqueda del catalogo
	this.formBusquedaCat = new Ext.FormPanel({
        	labelWidth: 80, 
			frame:true,
        	width: options.anchoformbus,
			height: options.altoformbus+10,
			items: [this.fieldcatalogo]
		});
	//Fin creando formulario de busqueda del catalogo
		
	//Creando la instacia de la grid del catalogo
	this.gridcatalogo = new Ext.grid.GridPanel({
	 		width:options.anchogrid,
	 		height:options.altogrid,
	 		tbar: this.formBusquedaCat,
	 		autoScroll:true,
     		border:true,
     		ds: this.dataStoreCatalogo,
       		cm: options.colmodelocat,
			sm: options.selmodelocat,
       		stripeRows: true,
      		viewConfig: {forceFit:true}
		});
	//Fin Creando la instacia de la grid del catalogo
	
	//Eventos de la ventana catalogo
	this.cerrarVentana = function(){
		this.dataStoreCatalogo.removeAll();
		copiadatastorecatalogo = '';
		this.vencatalogo.hide();
	}
	
	this.cargarDatosCat = function (){
		var datos = arguments[0].responseText;
		var objetodata = eval('(' + datos + ')');
		if(objetodata!=''){
			copiadatastorecatalogo = options.datosgridcat;
			this.dataStoreCatalogo.loadData(objetodata);
			copiadatastorecatalogo.loadData(objetodata);
		}
	}
	
	this.mostrarVentana = function(){
		Ext.Ajax.request({
			url : options.rutacontrolador,
			params : options.parametros,
			method: 'POST',
			success: this.cargarDatosCat.createDelegate(this, arguments, 2)
		});
		this.vencatalogo.show();
	}
	
	this.setDataGrid = function(){
		var arregloreg =  this.gridcatalogo.getSelectionModel().getSelections();
		for (i=0; i<arregloreg.length; i++){
			var validareg = arregloreg[i];
			if(validarExistenciaRegistroStore(validareg,this.dataGridEditable.store,options.arrcampovalidaori,options.arrcampovalidades)){
				arregloreg[i].set('registrocat','1');
				this.dataGridEditable.store.insert(0,arregloreg[i]);
			}
			else{
				Ext.MessageBox.alert('Advertencia','El item seleccionado ya fue cargado');
			}
		}
		this.vencatalogo.hide();
	}
	//Fin de los eventos de la ventana catalogo
	
	//Creando la instacia de la window para la ventana del catalogo
	this.vencatalogo = new Ext.Window({
    		title: options.titvencat,
			autoScroll:true,
        	width:options.anchoven,
        	height:options.altoven,
        	modal: true,
        	closable:false,
        	plain: false,
			items:[this.gridcatalogo],
			buttons: [{
						text:'Aceptar',  
			        	handler: this.setDataGrid.createDelegate(this)
			       	},{
			      		text: 'Salir',
			        	handler:this.cerrarVentana.createDelegate(this)
                  	}]
      	});
	//Fin creando la instacia de la window para la ventana del catalogo
    
    //Creando funcion para la eliminacion de registros de la grid
	this.eliminarRegistro = function (){
		var arregloregistros = this.dataGridEditable.getSelectionModel().getSelections();
		if (arregloregistros.length >0){
			for (var i = arregloregistros.length - 1; i >= 0; i--){
				this.dataGridEditable.getStore().remove(arregloregistros[i]);
				if(arregloregistros[i].get('registrocat')!='1' && options.guardarEliminados){
					this.dataStoreEliminados.add(arregloregistros[i]);
				}
			};
		}
	}
	//Fin creando funcion para la eliminacion de registros de la grid
	
	//Creando grid de datos que se llenara con el catalgo
	this.dataGridEditable =new Ext.grid.EditorGridPanel({
        id: options.idgrid,
		width:options.ancho,
        height:options.alto,
       	style:options.posicion,
        title:options.titgrid,
	    ds: options.datosgrid,
       	cm: options.colmodelo,
       	sm: options.selmodelo,
       	enableColumnHide: false,
        frame:true,
       	viewConfig: {forceFit:true},
        columnLines: true,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar un registro',
            iconCls:'agregar',
            id:'agregar',
			handler: this.mostrarVentana.createDelegate(this)
        	}, '-', {
            text:'Eliminar',
            tooltip:'Eliminar un registro',
            iconCls:'remover',
            id:'remover',
			handler: this.eliminarRegistro.createDelegate(this)
			}]
	    
	});
	//Fin creando grid de datos que se llenara con el catalgo
	

}//Fin componente lista editable catalogo

