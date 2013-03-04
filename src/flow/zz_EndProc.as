package flow{
	//import flash.display.Sprite;
	import flash.events.MouseEvent;
	
	import mx.core.UIComponent;
	
	//import x2.Fun2;
	

	public class zz_EndProc extends Process
	{
		//new
		private const cnRadius:int = 16;

		
		//constructor, Start/End process.
		//public function Process(p_area:WorkArea, p_point:Point, ps_name:String){
		public function zz_EndProc(ps_type:String, p_data:Object){		
		//public function EndProc(ps_type:String, p_data:Object){			
			super(ps_type, p_data);
		}

	
//		//called by parent constructor
//		override public function drawBound(ps_type:String=""):void{
//			
//			//draw border
//			var tn_color:int = 0XFF0000;
//			this.graphics.lineStyle(1, cnLineColor, 1, false);
//			this.graphics.beginFill(tn_color, 0.3);
//			this.graphics.drawCircle(cnRadius, cnRadius, cnRadius - Node.cnSize);	//左上角為 (0,0), 與 Process相同 !!
//			this.graphics.endFill();
//
//			/*
//			//initialize text field for flow name		
//			//i_text.text = is_type;
//			i_text.selectable = false;
//			i_text.x = (this.width - i_text.width)/2;
//			i_text.y = (this.height - i_text.height)/2;
//			this.addChild(i_text);
//			*/
//			
//			/*
//			//reset nodes, left to right, up to down
//        	ia_node[0] = new ProcessNode(this, 0, new Point(0, - ic_radius));
//        	ia_node[1] = new ProcessNode(this, 1, new Point(- ic_radius, 0));
//        	ia_node[2] = new ProcessNode(this, 2, new Point(ic_radius, 0));
//        	ia_node[3] = new ProcessNode(this, 3, new Point(0, ic_radius));        	
//        	for (var i:int=0;i<ic_nodes;i++){
//        		this.addChild(ia_node[i]);
//   				ia_node[i].visible = false;
//        	}
//        	*/					
//        }


		//get current node no when mouse move
		/*
		override protected function getNodeNo(p_event:MouseEvent):int{
			if (Math.abs(p_event.localX) > Math.abs(p_event.localY)){
				return (p_event.localX > 0) ? 2 : 1;
			}else{
				return (p_event.localY > 0) ? 3 : 0;
			}
		}
		*/
	}
}