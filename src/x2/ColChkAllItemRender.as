
package x2{ 
    import flash.events.MouseEvent; 
    import mx.controls.Alert; 
    import mx.controls.CheckBox; 
    import mx.controls.DataGrid;
    import x2.*;
    
     
    public class ColChkAllItemRender extends CheckBox{
    	 
        public function ColChkAllItemRender(){ 
			super(); 
        }
        
         
        override public function validateProperties():void{ 
            super.validateProperties();
             
            if (listData){ 
                var dg:DataGrid = DataGrid(listData.owner); 
                var column:ColChkAll = dg.columns[listData.columnIndex]; 
                column.addEventListener("click",columnHeaderClickHandler); 
                selected = data[column.dataField]; 
            } 
        }
        
         
        private function columnHeaderClickHandler(event:MouseEvent):void{ 
            //why this alery shows three times for a data of 2 rows 
            //mx.controls.Alert.show("alert"); 
            selected = event.target.selected; 
        } 
    } 
} 
