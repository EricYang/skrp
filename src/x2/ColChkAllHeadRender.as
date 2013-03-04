
package x2{ 
    import flash.events.MouseEvent; 
    import mx.controls.CheckBox; 
    import mx.controls.DataGrid; 
    import mx.events.DataGridEvent;
    import x2.*;
         
    public class ColChkAllHeadRender extends CheckBox{ 

	    private var _data:ColChkAll;
	     
	    public function ColChkAllHeadRender(){ 
            super(); 
            //addEventListener("click", clickHandler); 
		}
		
		 
	    override public function get data():Object{ 
			return _data; 
	    } 
	
	
	    override public function set data(value:Object):void{ 
			_data = value as ColChkAll; 
	
			DataGrid(listData.owner).addEventListener(DataGridEvent.HEADER_RELEASE, sortEventHandler); 
			selected = _data.selected; 
	    } 
	
	
	    private function sortEventHandler(event:DataGridEvent):void{ 
			if (event.itemRenderer == this) 
				event.preventDefault(); 
	    } 
	    
	    
	    override protected function clickHandler(event:MouseEvent):void{ 
			super.clickHandler(event); 
			data.selected = selected; 
			data.dispatchEvent(event); 
	    }
	     
	} 
} 

