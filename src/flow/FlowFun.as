package flow
{
	import flash.display.*;
	import flash.events.MouseEvent;
	import flash.geom.*;
	
	//import mx.controls.*;
	import mx.core.*;
	import mx.managers.DragManager;
	import mx.managers.CursorManager;

	
	/**
	 * FlowFun 是流程引擎裡用到的一些公用函數和 static 變數.
	 */ 
	public class FlowFun
	{
		
		//global variables
		public static var gArea:WorkArea;		
		//public static var gbDragStart:Boolean;			//true(start node), false(end node)
		//public static var gDragLineNode:LineNode;		//current drag lineNode object
		//public static var gDropProcessNode:ProcessNode;	//current drop ProcessNode object
		
		/*
		public static var gnOffsetX:int, gnOffsetY:int;
				
		//for dragdrop		
		public static var goSelectItem:Object;			//drag source object: Process, Line, null		
		public static var gsSelectType:String;			//P(Process), L(Line)		
		public static var goDragItem:Object;			//drag source object: Process, Line, LineNode, null		
		public static var gsDragType:String;			//P(Process), L(Line), N(LineNode)		
		*/
		
		//for test
		//public static var goLabel:Object;		
		//public static var gf:Function;
		
		/*
		private static var in_cursor:int;		
		private static var in_cursor2:int;
		
        [Bindable]
        [Embed(source="image/move.png")]
        private static var i_move:Class;

        [Bindable]
        [Embed(source="image/dragNode.png")]
        private static var i_dragNode:Class;


		public static function onRollOver(p_event:MouseEvent):void{
			in_cursor = CursorManager.setCursor(i_move, 2, -10, -10);
		}
		public static function onRollOut(p_event:MouseEvent):void{
			CursorManager.removeCursor(in_cursor);
		}
		public static function onRollOver2(p_event:MouseEvent):void{
			in_cursor2 = CursorManager.setCursor(i_dragNode, 1, -10, -10);	//dragNode 的優先權高於 move (第2個參數 0:high, 1:mid, 2:low)
		}
		public static function onRollOut2(p_event:MouseEvent):void{
			CursorManager.removeCursor(in_cursor2);
		}
		*/


		/**
		 * @param {string} ps_itemType P(process), L(line), N(node)
		 */ 
		/*
		public static function startDrag(p_event:MouseEvent, ps_itemType:String, p_selectItem:Object, p_dragItem:Object):void{
			
			//if (p_event.eventPhase != (ps_itemType=="P"?3:2)){
			if (p_event.eventPhase != 2){
				return;
			}			
			

			//var t_target:Object = p_event.currentTarget;
			
			if (p_selectItem != null){
				//gsSelecType = ps_itemType;
				//gSelectItem = p_selectItem;
				gArea.selectItem(p_selectItem, ps_itemType);
			}
			
			if (p_dragItem != null){
				gsDragType = ps_itemType;
				goDragItem = p_dragItem;
			}
			
            var t_drag:UIComponent = UIComponent(p_event.target);
			t_drag.startDrag();
			return;	
            
            
			//save offset
			var t_target:Object = p_event.target;
            FlowFun.gnOffsetX = t_target.mouseX; 
            FlowFun.gnOffsetY = t_target.mouseY; 
			FlowFun.goLabel.text = FlowFun.gnOffsetX+","+FlowFun.gnOffsetY;
			
			//set dragsource
            
            var t_ds:DragSource = new DragSource();
            //t_ds.addData(1, 'value');
            //var t_drag:UIComponent = UIComponent(p_event.target);
            
            //drag			
            //if (pb_proxy){            
				// Create a copy of the coin image to use as a drag proxy.
                //var t_proxy:Image = new Image();
                //t_proxy.source = t_target.source;
                
                            	    
	            //Add proxy image for UIComponent
			    //var t_bd:BitmapData = new BitmapData(t_drag.width, t_drag.height);   
	            var t_rect2:Rectangle = t_drag.getRect(t_drag);
	            var t_rect:Rectangle = (ps_itemType == "P") ? t_drag.getRect(t_drag) : p_selectItem.getRect2();
	            //t_rect.x = 0;
	            //t_rect.y = 0;
	            //t_rect.x = t_rect.x - Node.ic_size;
	            //t_rect.y = t_rect.y - Node.ic_size;
			    var t_bd:BitmapData = new BitmapData(t_rect.width, t_rect.height);    
			    var t_m:Matrix = new Matrix();    
			    //t_m.translate(20, 20);
			    t_bd.draw(t_drag, t_m);
			    //
	            var t_proxy:Image = new Image();
	            t_proxy.source = new Bitmap(t_bd);
	            
	            
            	DragManager.doDrag(t_drag, t_ds, p_event, t_proxy, 0, 0, 0.35);
            	//DragManager.doDrag(t_drag, t_ds, p_event);
            //}else{
            //	t_ds.addData(t_drag, "img");
            //	DragManager.doDrag(t_drag, t_ds, p_event);            	
            //}
		}
		*/
	}
}