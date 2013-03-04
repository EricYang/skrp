package {

	import x2.*;

	
   	public class Louis {
        
        //傳回類別或某個類別的子類別
        public static function mateTypesDS(ps_app:String, pn_typeSn:int):Array{
            var t_data:Object = {
                _pg: "Louis",
                data: "mateTypes",
                typeSn: pn_typeSn
            };
            return Fun.readRows(ps_app, t_data);
        }	
        
        //取得老師課程
        public static function lessonDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
            var t_data:Object = {
                _pg: "Louis",
                type: "lesson"
            }
            return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
        }        
        
        
        //由Code Table取得子教材類型供Combobox或DropDownList使用
        public static function mateSubTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
            return Fun.codeDS(ps_app, "mateSubType", pb_addEmpty);
        }		
        
        /*
        public static function loadMat(p_data:Object):void{
            //new and open WinMaterial
            //set global variables
            var t_item:Object = {
                data: p_data.app,
                swf: "LoadMat"
            };
            Fun2.oGlobal.material = p_data;
            Object(Fun.wMain).openApp(t_item, false);
        }        
        */
		
        /**
         * 分辨教材的附加檔案類型並傳回檔案URL
         * @param ps_type 子教材類型 CT:雙語對照  FA:單獨影片 VC:垂直文章
         * @param p_data 包含sn, ext(不含.副檔名)
         * @param pb_root 是否包含 web site root 路徑
         */          
        public static function getFileData(ps_type:String, p_data:Object, pb_root:Boolean):Object {
            var ts_fileName:String = "";
            switch (ps_type) {
                case 'CT':  //雙語子教材
                    ts_fileName = 'm';
                    break;
                case 'FA':  //單獨影片,動畫
                    ts_fileName = 'f';
                    break;
                case 'VC':  //垂直文章
                    ts_fileName = 'v';
                    break;
                default:
                    Fun.msg("E", "Fun2.getFileData().ps_type = No such material sub type '" + ps_type + "'");
                    return null;                    
            }
            var ts_ext:String = String(p_data.ext).toLowerCase();
            if (!Fun.isEmpty(ts_ext)) {
                var ts_type:String = "";
                if (String(Fun.csPicTypes+",").indexOf(ts_ext+",") >= 0) {
                    ts_type = "pic";        //圖檔
                } else if (String(Fun.csVideoTypes+",").indexOf(ts_ext+",") >= 0) {
                    if (ts_ext != "swf")
                        ts_type = "mov";    //影片檔
                    else
                        ts_type = "swf";    //flash動畫
                } else {
                    Fun.msg("E", "Fun2.getFileData().p_data.ext = illegal file type '." + ts_ext + "'");
                    return null;
                }
                //var ts_file:String = Fun2.getExamDir('M',pb_root) + ts_fileName + p_data.sn + "." + ts_ext;
                var ts_more:String = "";
                if (p_data.hasOwnProperty("more"))
                    ts_more = p_data.more;
                
				var ts_path:String = Fun2.getPSDir('SM',pb_root) + ts_fileName + p_data.sn + ts_more + "." + ts_ext;
                var t_data:Object = {
                    type: ts_type,
                    path: ts_path 
                }
                return t_data;
            } else {
                Fun.msg("E", "Fun2.getFileData().p_data.ext = file type cannot be empty.");
                return null;
            }
        }
   	}//class
}
