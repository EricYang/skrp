package x2
{
		
	import flash.events.MouseEvent;
	
	import mx.controls.CheckBox;
	import mx.controls.DataGrid;
	import mx.events.DataGridEvent;
	
	
	public class CheckHeaderRender extends CheckBox{	
	
		private var i_header:CheckHeaderColumn;
		
		public function CheckHeaderRender(){
			super();
			
			//addEventListener("click", clickHandler);
		}
		

		override public function get data():Object{
			return i_header;
		}

	
		override public function set data(p_value:Object):void{
			if (i_header == null){
				i_header = p_value as CheckHeaderColumn;
				this.label = i_header.headerText;
				
				//set fontWeight
				var ts_set:String = i_header.getStyle("fontWeight");
				if (!Fun.isEmpty(ts_set)){
					this.setStyle("fontWeight", ts_set);
				}
				
				//set paddingLeft
				ts_set = i_header.getStyle("paddingLeft");
				if (!Fun.isEmpty(ts_set)){
					this.setStyle("paddingLeft", ts_set);
				}
					
			}
			
			DataGrid(listData.owner).addEventListener(DataGridEvent.HEADER_RELEASE, sortEventHandler);
			this.selected = i_header.bSelected;
		}


		private function sortEventHandler(event:DataGridEvent):void{
			if (event.itemRenderer == this)
				event.preventDefault();
		}
		
		
		override protected function clickHandler(event:MouseEvent):void	{
			super.clickHandler(event);
			i_header.bSelected = selected;
			i_header.dispatchEvent(event);
		}
		
		/*
		import flash.display.DisplayObject;
		import mx.controls.listClasses.ListBase;
		import flash.text.TextField;
		import mx.controls.dataGridClasses.DataGridListData;
		
		// center the checkbox if we're in a datagrid 
		override protected function updateDisplayList(w:Number, h:Number):void
		{
			super.updateDisplayList(w, h);
			
			if (listData is DataGridListData)
			{
				var n:int = numChildren;
				for (var i:int = 0; i < n; i++)
				{
					var c:DisplayObject = getChildAt(i);
					if (!(c is TextField))
					{
						c.x = (w - c.width) / 2;
						c.y = 0;
					}
				}
			}
		}
		*/
	}	
}  