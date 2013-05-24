package ide.display{
	import mx.core.Application;
	import mx.controls.Alert;
	import mx.managers.DragManager;
	import mx.core.UIComponent;
	import mx.core.DragSource;
	import flash.display.DisplayObjectContainer;

	import mx.controls.*;
	import mx.events.*;
	import flash.events.*;
	import mx.containers.Canvas;
	import mx.containers.Panel;
	import com.dyd.*;
	import ide.display.*;
	import flash.external.*;
	import flash.net.*;

	//import flash.utils.Dictionary;
/*
Esta clase esta basada en este ejemplo:
http://blogs.adobe.com/flexdoc/2007/03/creating_resizable_and_draggab.html
*/


	public class ideBase extends Panel {
	
	
		public var v1:Canvas;
		private var rbComp:RubberBandComp;
		private var listaComponentes:Array;

		// Define static constant for event type.
		public static const RESIZE_CLICK:String = "resizeClick";
		[Bindable]
		public var cadenaDatos:String;
		[Bindable]
		public var cadenaEspecificacion:String;
		public var especificacion:XML;
		public var datos:XML;
		public var parametros:Object;
		
		function ideBase():void{
			super();
		}

		public function lista():void {
			listaComponentes= new Array();
			
			cadenaDatos = Application.application.parameters.datos;
			cadenaEspecificacion = Application.application.parameters.especificacion;
			//idCasoUso = Application.application.parameters.idCasoUso;

			parametros=Application.application.parameters;
			//Alert.show("++++ "+Application.application.parameters);
			//for (var i:String in parametros) {
			//	Alert.show(i+" - "+parametros[i]);
			//}

			especificacion=new XML(cadenaEspecificacion);
			datos=new XML(cadenaDatos);

			v1= new Canvas();
			v1.addEventListener(DragEvent.DRAG_ENTER, doDragEnter);		
			v1.addEventListener(DragEvent.DRAG_DROP, doDragDrop);	
			v1.width=Application.application.parameters.ancho-40;
			v1.height=Application.application.parameters.alto-60;	
			v1.graphics.clear();
			v1.graphics.lineStyle(0);
			v1.graphics.beginFill(0xFFFFFF, 0.0);
			v1.graphics.drawRect(0, 0, v1.width, v1.height);
			this.addChild(v1)
			rbComp= new RubberBandComp();
			rbComp.visible=false;
			v1.addChild(rbComp);
			//@ToDO: aca falta colocar parametros por defecto

			crearComponente(v1, "Propiedades", especificacion.@inicial,0,0);

			// Creation complete event handler adds the resizing event. 
			// resizeButtonClicked is a custom event type for this application.
			addEventListener(RESIZE_CLICK, resizeHandler);
			ExternalInterface.addCallback("sendToActionScript", procesarDatosJavascript);
			
		}
		private function procesarDatosJavascript(value:String):void {
			var variables:URLVariables= new URLVariables();
			variables.decode(value);
			//Alert.show("El componente es: "+);
			var nombre:String;
			var pareja:String;
			var objetoCampos:Array = new Array();
			var llavesCampos:Array = new Array();
			var arrayVariables:Array = value.split("&");
			var parejaValores:Array;
			var com:ideComponente;
			var contador:Number=0;
			var valor:String;
			try{
				
				for each(pareja in arrayVariables){
					parejaValores = pareja.split("=",2);
					llavesCampos[contador]=parejaValores[0];
					valor=parejaValores[1];
					valor=valor.split("+").join(" ");
					valor=valor.split("%3A").join(":")
					objetoCampos[contador]=valor;
					//Alert.show("Agregando "+llavesCampos[contador]+" = "+objetoCampos[contador]);
					contador++;
				}
	
				//for (contador=0;contador<objetoCampos.length;contador++){
				//	Alert.show("El componente tiene: "+contador+" -> "+llavesCampos[contador]+":"+objetoCampos[contador]);
				//}
				com=ideComponente(listaComponentes[variables["idComponente"]]);
				//Alert.show("El componente a llamar es: "+com+" - "+variables["idComponente"]);
				com.procesar(llavesCampos, objetoCampos);
			}catch (e:Error){
				Alert.show("Un error: "+e);
			}
			//variables
			//Alert.show("procesarDatosJavascript"+
		}
		public function crearComponente(destino:UIComponent, titulo:String, nombre:String, x:Number, y:Number):void{
			var componente:ideComponente;


			componente = new ideComponente();
			componente.title=titulo;
			
			componente.x=x;
			componente.y=y;

			componente.base=this;
			componente.especificacion=especificacion;
			componente.datos=datos;
			componente.padre=destino;
			componente.procesaInformacion(nombre);
			componente.addEventListener(FlexEvent.CREATION_COMPLETE, myPanelCCHandler);
			v1.addChild(componente);
			
			componente.actualizar();
			componente.numero=listaComponentes.length;
			listaComponentes.push(componente);
			
		}
		override public function toString():String{
			return "ideBase";
		}

//
// D&D event handlers.
//
        // Creation complete handler for each panel to add the 
        // mouseMove event handler to the title bar. 
        // Clicking the mouse button, then moving the mouse on the title bar
        // initiates the d&d operation. 
        private function myPanelCCHandler(event:Event):void 
        {
        	event.currentTarget.myTitleBar.addEventListener(MouseEvent.MOUSE_DOWN, tbMouseMoveHandler);
        }
        // Variables used to hold the mouse pointer's location in the title bar.
        // Since the mouse pointer can be anywhere in the title bar, you have to 
        // compensate for it when you drop the panel. 
        public var xOff:Number;
        public var yOff:Number;
        
        // Function called by the canvas dragEnter event; enables dropping
        private function doDragEnter(event:DragEvent):void 
        {
            DragManager.acceptDragDrop(Canvas(event.target));
        }

        // Drag initiator event handler for
        // the title bar's mouseMove event.
        public function tbMouseMoveHandler(event:MouseEvent):void 
        {
            var dragInitiator:Panel=Panel(event.currentTarget.parent);
            var ds:DragSource = new DragSource();
            ds.addData(event.currentTarget.parent, 'panel'); 
            
    	    // Update the xOff and yOff variables to show the
        	// current mouse location in the Panel.  
            xOff = event.currentTarget.mouseX;
            yOff = event.currentTarget.mouseY;
            
            // Initiate d&d. 
            DragManager.doDrag(dragInitiator, ds, event);       
            var componente:ideComponente=ideComponente(event.currentTarget.parent);
            componente.actualizar();             
        }            

        // Function called by the Canvas dragDrop event; 
        // Sets the panel's position, 
        // "dropping" it in its new location.
        private function doDragDrop(event:DragEvent):void 
        {
			// Compensate for the mouse pointer's location in the title bar.
			var tempX:int = event.currentTarget.mouseX - xOff;
			event.dragInitiator.x = tempX;
			
			var tempY:int = event.currentTarget.mouseY - yOff;
			event.dragInitiator.y = tempY;

			// Put the dragged panel on top of all other components.
			v1.setChildIndex(Panel(event.dragInitiator), v1.numChildren-1);		
			
            var componente:ideComponente;
			for(var i:Number = 0; i < listaComponentes.length; i++){
				componente=listaComponentes[i];
				componente.actualizar();
			}
        }

//
// Resizing event handlers.
//

		// Save panel being resized.
		protected var resizingPanel:Panel;
		// Global coordinates of lower left corner of panel.
		protected var initX:Number;
		protected var initY:Number;

		// Resize area of panel clicked.
		protected function resizeHandler(event:MouseEvent):void
		{
			resizingPanel = Panel(event.target);
			initX = event.localX;
			initY = event.localY;
			
			// Place the rubber band over the panel. 
			rbComp.x = event.target.x;
			rbComp.y = event.target.y;
			rbComp.height = event.target.height;
			rbComp.width = event.target.width;
			
			// Make sure rubber band is on top of all other components.
			v1.setChildIndex(rbComp, v1.numChildren-1);
			rbComp.visible=true;
			
			// Add event handlers so that the SystemManager handles 
			// the mouseMove and mouseUp events. 
			// Set useCapure flag to true to handle these events 
			// during the capture phase so no other component tries to handle them.
			systemManager.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveHandler, true);
			systemManager.addEventListener(MouseEvent.MOUSE_UP, mouseUpHandler, true);
            var componente:ideComponente;
			for(var i:Number = 0; i < listaComponentes.length; i++){
				componente=listaComponentes[i];
				componente.actualizar();
			}			
		}
		
		// Resizes the rubber band as the user moves the cursor 
		// with the mouse key down.
		protected function mouseMoveHandler(event:MouseEvent):void
		{
				event.stopImmediatePropagation();		
					
				rbComp.height = rbComp.height + event.stageY - initY;  
				rbComp.width = rbComp.width + event.stageX - initX;
				
				initX = event.stageX;
				initY = event.stageY;						
		}
		
		// Sizes the panel to the size of the rubber band when the 
		// user releases the mouse key. 
		// Also removes the event handlers from the SystemManager.
		protected function mouseUpHandler(event:MouseEvent):void
		{
			event.stopImmediatePropagation();		

			// Use a minimum panel size of 150 x 50.
			if (rbComp.height <= 50)
			{
				resizingPanel.height = 50;  
			}
			else
			{
				resizingPanel.height = rbComp.height;  				
			}				
			
			if (rbComp.width <= 150)
			{
				resizingPanel.width = 150;				
			}
			else
			{
				resizingPanel.width = rbComp.width;				
			}				

			// Put the resized panel on top of all other components.
			v1.setChildIndex(resizingPanel, v1.numChildren-1);

			// Hide the rubber band until next time.
			rbComp.x = 0;
			rbComp.y = 0;
			rbComp.height = 0;
			rbComp.width = 0;
			rbComp.visible = false;
			
			systemManager.removeEventListener(MouseEvent.MOUSE_MOVE, mouseMoveHandler, true);
			systemManager.removeEventListener(MouseEvent.MOUSE_UP, mouseUpHandler, true	);
            var componente:ideComponente;
			for(var i:Number = 0; i < listaComponentes.length; i++){
				componente=listaComponentes[i];
				componente.actualizar();
			}
		}
	}
}

