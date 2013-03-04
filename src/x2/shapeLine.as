package x2
{
	import flash.display.Shape;
	import flash.events.MouseEvent;
	import x2.Fun;
	
	
	public class shapeLine extends Shape
	{
		
		private var ib_move:Boolean = false;
		private var in_x:int;
		private var in_y:int;
		
		
		public function shapeLine()
		{
			super();
			
			//add event
			this.addEventListener(MouseEvent.MOUSE_DOWN, mouseDown);
			this.addEventListener(MouseEvent.MOUSE_UP, mouseUp);
			this.addEventListener(MouseEvent.MOUSE_MOVE, mouseMove);
		}
		
		
		private function mouseDown(p_event:MouseEvent):void{
			Fun.msg("I","test");
			
			ib_move = true;
			in_x = p_event.localX;
			in_y = p_event.localY;
		}
		
		private function mouseUp(p_event:MouseEvent):void{
			ib_move = false;
		}

		private function mouseMove(p_event:MouseEvent):void{
			if (ib_move){
				this.x = this.x + p_event.localX - in_x;
				this.y = this.y + p_event.localY - in_y;
			}
		}
		
	}
}