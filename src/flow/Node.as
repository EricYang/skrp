package flow{
	
	//import flash.display.Sprite;
	import flash.geom.Point;	
	import mx.core.UIComponent;
	//import mx.controls.Alert;
	import flow.FlowFun;
	

	/**
	 * Node 為一個圓形的小點, 附著在 Process 和 Line 上面, 可以用來連結不同的流程物件.
	 */ 
	public class Node extends UIComponent
	{
		/**
		 * 節點的半徑大小, 單位為 pixel.
		 */ 
        public static const cnSize:int = 4;

		/**
		 * 無色.
		 */ 
        public static const cnNoColor:int = 0;
        
		/**
		 * 紅色, 表示此節點已經連結物件.
		 */ 
        public static const cnRed:int = 1;

		/**
		 * 綠色, 表示此節點可以被拖曳.
		 */ 
        public static const cnGreen:int = 2;

        
        //protected property.
        /**
         * 表示此節點是否正被拖曳. 
         */ 
		//protected var ib_drag:Boolean = false;	//select for dragging
		
		
		//instance variable
		private var in_color:int = cnNoColor;
		//protected var ib_connect:Boolean=false;	//connect or not
		//protected var i_parent:Object;			//parent object(Line or Process)
							
		
		/**
		 * Constructor.
		 * @param p_point 相對於父元件的 xy 座標.
		 */ 
		public function Node(p_point:Point){
			super();
			
			//i_parent = parent;
			
			this.graphics.lineStyle(1, 0);
            //this.graphics.drawCircle(0, 0, cnSize);             
            this.x = p_point.x;
            this.y = p_point.y;  
            this.height = cnSize * 2;
            this.width = cnSize * 2;
            setColor(cnNoColor);            
		}
		

		//public function zz_getParent():Object{
		//	return i_parent;
		//}


		/**
		 * 傳回節點目前的顏色
		 * @return 節點顏色: cnRed, cnGreen, cnNoColor.
		 */ 		
		public function getColor():int{
			return in_color;
		}
		
		
		/**
		 * 設定節點的顏色.
		 * @param pn_color cnRed, cnGreen, cnNoColor.
		 */ 
		public function setColor(pn_color:int):void{
			in_color = pn_color;
			
			var tn_color:int;
			switch (pn_color){
				case cnRed:
					tn_color = 0XFF0000;
					break;
				case cnGreen:
					tn_color = 0X00FF00;
					break;
				/*	
				case "B":
					tn_color = 0X0000FF;
					break;
				*/	
				default:
					tn_color = 0XF0F0F0;	//gray
			}
			
            this.graphics.beginFill(tn_color);
            this.graphics.drawCircle(0, 0, cnSize); 
            this.graphics.endFill();						
		}
				

		/*
		protected function msg(ps_msg:String):void {
			Alert.show(ps_msg,"Information");
		}
		*/
	}
}