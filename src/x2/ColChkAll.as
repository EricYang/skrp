
package x2{ 
    [Event(name="click", type="flash.events.MouseEvent")]
     
    //import mx.controls.dataGridClasses.DataGridColumn;
	import spark.components.gridClasses.GridColumn;

    public class ColChkAll extends GridColumn{ 
        public function ColChkAll(columnName:String=null){ 
			super(columnName); 
        } 
        
        /**is the checkbox selected**/ 
        public var selected:Boolean = false; 
    } 
} 
