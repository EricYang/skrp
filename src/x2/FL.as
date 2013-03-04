/**
 * File
 */ 
package x2
{	
	import flash.events.*;
	import flash.net.*;

	
	public class FL{
		//public function FL(){}
				
		/**
		 * for download file, must be instance variables, or will fail !!
		 */ 
		private static var i_file:FileReference;

		
		/**
		 * get file for upload
		 * @param ps_fileType {String} S(sound),P(pic),A(all)
		 */ 			
		//public static function getFile(p_file:FileReference, pf_callback:Function, ps_fileType:String="", ps_fileDesc:String=""):void{
		//public static function getFile(ps_fileType:String, p_file:FileReference, pf_callback:Function, ps_fileDesc:String=""):void{
		public static function fileGet(ps_fileType:String, pf_callback:Function, ps_fileDesc:String="", p_file:FileReference=null):void{
			var ts_fileType:String = "";
			if (ps_fileType.indexOf("A") >= 0){
				ts_fileType = Fun.csPicTypes + "," + Fun.csSoundTypes + "," + Fun.csVideoTypes;
			}else{
				if (ps_fileType.indexOf("P") >= 0)
					ts_fileType += Fun.csPicTypes + ",";				
				if (ps_fileType.indexOf("S") >= 0)
					ts_fileType += Fun.csSoundTypes + ",";
				if (ps_fileType.indexOf("V") >= 0)
					ts_fileType += Fun.csVideoTypes + ",";
				
				if (ts_fileType == "")
					return;
				else
					ts_fileType = ts_fileType.substr(0, ts_fileType.length - 1);
				
			}
			
			if (p_file == null){
				p_file = new FileReference();
			}
			if (!p_file.hasEventListener("SELECT")){
				p_file.addEventListener(Event.SELECT, fileGet2);				
			}
			
			//set global
			Fun.oVar.fn = pf_callback;
			Fun.oVar.fileType = ts_fileType;
			
			
			//限制檔案類型的呈現結果有些奇怪, 暫不使用 (2011-12-16)
			var ts_list:String = "";
			var ts_comm:String = "";
			var ta_type:Array = ts_fileType.split(",");				
			for (var i:int=0; i<ta_type.length; i++){
				ts_list += ts_comm + "*." + ta_type[i];
				ts_comm = ";";
			}
			
			//set fileDesc, or will got error !! (2012-1-3)
			if (ps_fileDesc == null)
				ps_fileDesc = "Select File";
			
			var t_filter:FileFilter = new FileFilter(ps_fileDesc, ts_list); 
			p_file.browse([t_filter]);			
			
			//p_file.browse();				
		}
		
		private static function fileGet2(p_event:Event):void{
			//檢查檔案類型
			var t_file:FileReference = FileReference(p_event.target);
			var ts_fileType:String = Fun.oVar.fileType;
			if (ts_fileType != ""){
				var ts_nowType:String = t_file.name;
				ts_nowType = ts_nowType.substr(ts_nowType.lastIndexOf(".")+1).toLowerCase();
				if (String(ts_fileType+",").indexOf(ts_nowType+",") < 0){
					Fun.msg("E", ST.format(Fun.R.fileTypeWrong, [ts_fileType]));
					return;
				}
			}
			
			var tn_size:int;
			try {
				tn_size = p_event.target.size;
			}catch (e:Error){
				Fun.msg("E", "File size can not be zero !!");
				return;
			}
			
			//check file size
			var ts_fileExt:String = t_file.type.substr(1).toLowerCase();
			tn_size = (Fun.csVideoTypes.indexOf(ts_fileExt) >= 0) ? Fun2.cnVideoSize : Fun2.cnFileSize; 
			if (t_file.size > tn_size){
				Fun.msg("E", ST.format(Fun.R.fileOverSize, [tn_size/1000000+" MB !"]));
				return;
				//}else if(t_file.type){
				
			}
			
			var t_fn:Function = Fun.oVar.fn;
			//setFile(FileReference(p_event.target));
			
			if (t_fn != null){
				t_fn(p_event.target);
			}
		}
		
		/**
		 * get file name without path
		 */  
		public static function getName(ps_file:String):String{
			var tn_pos:int = ps_file.lastIndexOf("/");
			if (tn_pos < 0)
				tn_pos = ps_file.lastIndexOf("\\");
			
			return (tn_pos >= 0) ? ps_file.substr(tn_pos+1) : ps_file;
		}
		
		/**
		 * get file stem without extension
		 */  
		public static function getStem(ps_file:String):String{
			var tn_pos:int = ps_file.lastIndexOf(".");
			return (tn_pos >= 0) ? ps_file.substr(0, tn_pos) : ps_file;
		}
		
		/**
		 * get file extension
		 */  		
		public static function getExt(ps_file:String):String{
			var tn_pos:int = ps_file.lastIndexOf(".");
			return (tn_pos >= 0) ? ps_file.substr(tn_pos+1) : "";
		}
		
		
		/**
		 * upload files, //考慮 Fun2.bUseFtp 的設定!!
		 * @param p_win window object call this function.
		 * @param ps_app app id.
		 * //param pa_file array of files
		 * //param p_file 可使用以下變數: //file(單檔), files(多檔), //grid(DataGrid), grids(DataGrids),
		 * //  必須存在變數: _file(FileReference), _fileName(optional, 包含路徑, 如果無路徑, 則使用 Fun.csUploadPath)
		 * @param pa_file 單筆欄位陣列, 一般用在 DW, 每個陣列元素包含變數 Fun.csFile(FileReference), Fun.csFilePath(檔案相對路徑, 含檔名)
		 * @param pa_grid 多筆欄位陣列, 一般用在 DW2, 陣列元素包含變數與 pa_file 相同, 但是也可以自行定義, 寫在 pa_gridFname
		 *    內容為 datagrid, DG2, 或是 DW2
		 * @param pa_gridFname 如果一個grid裡面只有一個上傳欄位, 則會使用預設名稱, 有2個以上則必須定義, 否則上傳的欄位名稱會重複, 
		 *    定義時, ex: [["f1","p1","f2","p2"],[]...], 陣列元素的內容是一個字串陣列, 長度為這個grid上傳欄位數的2倍,
		 *    依次為 fileReference 欄位名稱, file path 欄位相對路徑, 含檔名...    
		 * //param p_data input parameter for WinUpload window.
		 * //array of files, 必須存在變數: file(FileReference), fileName(optional)
		 * //param pa_grid 要上傳檔案的 DataGrid 陣列
		 * //param pa_fname pa_grid 裡面的上傳 欄位名稱, 如果空白, 則使用 "fileName"
		 * //param ps_dir 上傳的檔案目錄, 如果空白, 則使用 Fun.csUploadPath
		 */ 			
		//public static function uploadFiles(p_win:Object, ps_app:String, p_data:Object, pa_file:Array, pa_grid:Array=null, pa_fname:Array=null, ps_dir:String=""):void{
		//public static function uploadFiles(p_win:Object, ps_app:String, p_file:Object, p_data:Object=null):void{
		public static function upload(p_win:Object, ps_app:String, pa_file:Array, pa_grid:Array=null, pa_gridFname:Array=null):void{
			//if (ps_dir == "")
			//	ps_dir = Fun.csUploadPath;
			
			//check field array
			//file
			//const tc_file:String = "_file";
			//const tc_fileName:String = "_fileName";
			//
			var ta_file:Array = [];		//file array count
			var tn_file:int = 0;	
			/*
			if (p_file.hasOwnProperty("file")){
			ta_file[0] = p_file[csFile];
			tn_file++;
			}
			*/
			//files
			var i:int;
			if (pa_file != null){
				//if (p_file.hasOwnProperty("files")){
				//var ta_file2:Array = p_file.files as Array;
				for (i=0; i<pa_file.length; i++){
					if (pa_file[i] != null && pa_file[i][Fun.csFile] != null){
						//ta_file[tn_file] = {file: pa_file[i]};
						ta_file[tn_file] = pa_file[i];
						tn_file++;
					}
				}
				//}
			}
			/*
			var ta_grid:Array = [];
			var tn_grid:int = 0;	//grid array count
			if (p_file.hasOwnProperty("grids")){
			ta_grid = p_file.grids as Array;
			tn_grid = ta_grid.length;
			}
			if (p_file.hasOwnProperty("grid")){
			ta_grid[tn_grid] = p_file.grid;
			tn_grid++;
			}
			*/
			//check grid array
			if (pa_grid != null){
				var ta_fname0:Array = [Fun.csFile, Fun.csFilePath];	//default field name
				//if (p_file.hasOwnProperty("grids")){
				//var ta_grid:Array = p_file.grids as Array;
				//var ta_row:Array;
				//var tn_len:int = pa_grid.length;
				var ta_fname:Array;
				var tn_field:int;
				for (i=0; i<pa_grid.length; i++){
					if (pa_gridFname != null && (i <= pa_gridFname.length - 1) && pa_gridFname[i] != null && pa_gridFname[i].length > 0){
						ta_fname = pa_gridFname[i];
						tn_field = ta_fname.length;
					}else{
						ta_fname = ta_fname0;
						tn_field = 2;	//will step 2
					}
					
					//ts_fname = (ta_grid[i] == null || ta_grid[i] == "") ? "fileName" : pa_fname[i];
					var ts_clsName:String = pa_grid[i].className; 
					var ta_row:Array = (ts_clsName == "DW2") ? DW2(pa_grid[i]).getAC().source : pa_grid[i].dataProvider.source;
					for (var j:int=0; j<ta_row.length; j++){
						for (var k:int=0; k<tn_field; k=k+2){
							if (ta_row[j][ta_fname[k]] == null)
								continue;
							
							ta_file[tn_file] = {};
							ta_file[tn_file][Fun.csFile] = ta_row[j][ta_fname[k]]; 
							
							if (ta_row[j][ta_fname[k+1]] != null){
								ta_file[tn_file][Fun.csFilePath] = ta_row[j][ta_fname[k+1]];								
							}
							tn_file++;
						}
					}					
				}
				//}
			}
			if (tn_file <= 0){
				return;
			}
			
			//open upload window
			var t_win:winUpload = new winUpload();
			Fun.openPopup(t_win, p_win);		
			//t_win.startUpload(ps_app, p_data, ta_file, uploadFiles2);
			t_win.upload(ps_app, ta_file, upload2);
		}
		
		//callback, show error if any.
		private static function upload2(p_data:Object):void{		
			if (p_data != null && p_data.error != null){
				Fun.msg("E", p_data.error);
			}
		}
		
		
		import flash.utils.ByteArray;
		/**
		 * 上傳 ByteArray 形式的檔案到主機
		 * @param ps_app app id
		 * @param pa_byte byte array
		 * @param ps_file 包含相對路和副檔名的檔案名稱 
		 */ 
		public static function uploadByteArray(ps_app:String, pa_byte:ByteArray, ps_file:String):void{
			
			//傳入參數
			var t_data:Object = {
				app: ps_app, 
				fun: "UploadByteArray"
			};
			t_data[Fun.csFilePath] = ps_file;
			
			//chrome上傳檔案時, session Id 會跑掉, 必須重傳, 
			//see http://www.cnblogs.com/rupeng/archive/2012/01/30/2332427.html
			t_data[Fun.csSessId] = Fun.sSessId;
			
			
			var t_urlReq:URLRequest = new URLRequest();
			//uploadURL.url = Fun.sDirApp + Fun.sService + Fun2.csAppExt;
			//t_urlReq.url = Fun.sDirApp + "UploadByteArray" + Fun2.csAppExt + "?data=" + Fun.encode(t_data);
			t_urlReq.url = Fun.sDirApp + Fun.sService + Fun2.csAppExt + "?data=" + Fun.encode(t_data);
			t_urlReq.contentType = "application/octet-stream";
			t_urlReq.method = URLRequestMethod.POST;
			t_urlReq.data = pa_byte;
			
			var t_loader:URLLoader = new URLLoader();
			//t_loader.addEventListener(Event.COMPLETE, completeHandler);
			t_loader.load(t_urlReq);			
		}
				
		
		/**
		 * download file and delete if need ??
		 */ 			
		public static function download(ps_app:String, ps_file:String, pb_delete:Boolean=false):void{
			i_file = new FileReference();
			if (pb_delete){
				i_file.addEventListener(Event.COMPLETE, download2);
				//t_file.addEventListener(IOErrorEvent.IO_ERROR, downFile2);
				
				/*	
				t_file.addEventListener(Event.OPEN, downFile2);
				t_file.addEventListener(Event.SELECT, downFile2);
				t_file.addEventListener(Event.SELECT, downFile2);
				t_file.addEventListener(HTTPStatusEvent.HTTP_STATUS, downFile2);
				t_file.addEventListener(IOErrorEvent.IO_ERROR, downFile2);
				t_file.addEventListener(ProgressEvent.PROGRESS, downFile2);
				t_file.addEventListener(SecurityErrorEvent.SECURITY_ERROR, downFile2);
				*/	
				//set global
				Fun.oVar.app = ps_app;
				Fun.oVar.file = ps_file;
			}			
			
			//open dialog for save excel file
			var ts_file:String = Fun.sDirTemp + ps_file;
			var t_request:URLRequest = new URLRequest(ts_file);
			var t_data:URLVariables = new URLVariables();
			t_request.method = URLRequestMethod.POST;	//使用post, 不然會有2k的限制!!
			//t_data.tt = new Date().getTime();
			t_data.tt = new Date().seconds;
			t_request.data = t_data;            
			i_file.download(t_request);			
		}
		
		
		private static function download2(p_event:Event):void{
			/*
			var ts_app:String = Fun.gObject.app
			var t_data:Object = {
			fun: "DeleteFile",
			file: Fun.gObject.file
			};
			Fun.async(ts_app, Fun.CRUD, null, t_data);
			*/
			//var tt:String = "tt";
		}
		
		
		/**
		 * 判斷檔案是否存在
		 */ 			
		public static function isExist(ps_file:String):Boolean{
			var t_data:Object = {
				fun: "FileExist",
				file: ps_file
			};
			var ts_exist:String = Fun.service2(t_data);  
			return (ts_exist == "1");
		}
						
	}//class
}//package