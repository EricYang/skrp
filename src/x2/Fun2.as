package x2{
	
	//import flow.*;
	
	import Louis;
	
	import com.Rijndael;
	
	import flash.display.Loader;
	import flash.events.Event;
	import flash.media.SoundMixer;
	import flash.net.FileReference;
	
	import mx.collections.*;
	import mx.collections.ArrayCollection;
	import mx.containers.TabNavigator;
	import mx.controls.SWFLoader;
	import mx.controls.dataGridClasses.DataGridColumn;
	import mx.core.UIComponent;
	import mx.utils.StringUtil;
	
	import spark.components.List;
	import spark.components.NavigatorContent;
	import spark.components.VideoPlayer;
	import spark.components.gridClasses.GridColumn;
	import spark.primitives.BitmapImage;

	//declare global variables
   	public class Fun2{
		//public for system
		public static const csVer:String = "2.0 b11"			//build version for cache function
		public static var sDebugPara:String = "XDEBUG_SESSION_START=netbeans-xdebug";	//debug parameter
		//public static var sDebugPara:String = "";	//debug parameter
			
		public static const cbJsonToStr:Boolean = true;		//將 json object 轉換成字串再傳到後端, for 斷行問題, true(php), false(.net), java??
		public static const cbAutoUserId:Boolean = true;	//使用userId做為DW的 自動欄位(autos[]), false:使用loginId
		public static const csAppExt:String = ".php";		//has '.'
		public static const csEmptyLabel:String = "--全部--";
		public static const cnFileSize:int = 3000000;		//上傳檔案上限為 3MB
		public static const cnVideoSize:int = 100000000;	//上傳影片檔案上限為 100MB		
		public static const csKey:String = "tqmtmkshjrrmgwghvifvfyiu";
		public static const cnKeySize:int = 256;
		
		public static var sLang:String = "tw";		//使用者選用的語系
		public static var oVar:Object = {};			//for 傳遞參數				
		public static var nMaxApp:int = 1;
		public static var nLoginWin:int = 0;		//是否開啟登入畫面, 1(是),-1(否),0(由傳入參數決定)
				
		
		//=== 自訂變數 begin ===
		public static var sBackImage:String;		//主畫面背景圖
		public static var oGlobal:Object = {};		//for 傳遞參數
		public static var gbClassLeaves:String;		//for 傳遞參數班級別
		public static var aGlobal:Array = [];		//for 傳遞參數
		public static var bGlobal:ArrayCollection;	//for 傳遞參數
		public static var accType:int;				//for 傳遞參數班級別
		
		//檔案路逕處理
		public static var fImages:String = "dbUpLoadFiles/images/"; //檔案路逕 圖檔
		public static var fPdf:String = "dbUpLoadFiles/PdfFolder/"; //檔案路逕 PDF		
		public static var fStudents:String = "dbUpLoadFiles/StudentsFolder/"; //檔案路逕 學生
		public static var fTeachers:String = "dbUpLoadFiles/TeachersFolder/"; //檔案路逕 教師
		public static var fMana:String = "dbUpLoadFiles/TeachersFolder/FilesMana/"; //檔案路逕 教師
		public static var fLearnPro:String = "dbUpLoadFiles/TeachersFolder/LearnPro/img/"; //檔案路逕 教師
		
		//=== 自訂變數 end ===
		
	    
		//=== 必須實作此函數  begin ===
		public static function init():void{	
		}
		
		
		public static function encrypt(p_data:Object):String {

			//here !!, 暫不加密  for php
			if (false){
				var t_aes:Rijndael = new Rijndael(Fun.aKeyByte);
				return t_aes.encrypt(ST.jsonToStr(p_data));
			}else{
				return ST.jsonToStr(p_data);				
			}
		} 	
		//=== end ===
		
		// william Start
		
		//主程式清單
		public static function programMDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "ProgramM", pb_addEmpty);
		}
		//程式清單
		public static function progDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "Program", pb_addEmpty);
		}
		//部門
		public static function deptDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "Dept", pb_addEmpty);
		}
		//班級清單
		public static function classInfoListDS(ps_app:String, classType:int, pb_addEmpty:Boolean=true):Array{
			var t_data:Object = {
				type: "classInfoList",
				classType: classType
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
			
			//return Fun.comboBoxDS(ps_app, "classInfoList", pb_addEmpty);
		}		
		//班別清單
		public static function classLeavesListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "classLeavesList", pb_addEmpty);
		}
		//班(級)別清單別名
		public static function classInfoNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "classInfoName", pb_addEmpty);
		}
		//班(級)別清單別名  給 sn 秀資料 
		public static function classInfoNameSpecDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "classInfoNameSpec", pb_addEmpty);
		}
		//班級清單(已開班)
		public static function classInfoStartDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "classInfoStart", pb_addEmpty);
		}
		//班級別清單合併(已開班)
		public static function classInfoStartConbDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "classInfoStartConb", pb_addEmpty);
		}
		//班級別清單(已開班) 普通班
		public static function classInfoStartConbADS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "classInfoStartConbA", pb_addEmpty);
		}
		//給學號取年級班別(已開班)
		public static function studentGetClassNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "studentGetClassName", pb_addEmpty);
		}
		//權限
		public static function funRangeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "funRange", pb_addEmpty);
		}				
		//職員清單
		public static function staffListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "staffList", pb_addEmpty);
		}
		//家長清單
		public static function parentListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "parentList", pb_addEmpty);
		}
		//家長姓名
		public static function parentNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "parentName", pb_addEmpty);
		}
		//學生清單
		public static function studentListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "studentList", pb_addEmpty);
		}
		//學生姓名
		public static function studentNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "studentName", pb_addEmpty);
		}
		//學生(保險)出生日期, 身分證號
		public static function studentBIDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "studentBI", pb_addEmpty);
		}
		//學生清單(已開單)
		public static function studentStartDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "studentStart", pb_addEmpty);
		}
		//家長之學生清單
		public static function pStudentDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "pStudent", pb_addEmpty);
		}		
		//綜合 1 保健, 2 保險, 3 身體異常, 4 專案, 5 病症, 6 行為觀察
		public static function itemNameDS(ps_app:String, sysType:int, pb_addEmpty:Boolean=true):Array{
			var t_data:Object = {
				type: "itemName",
				sysType: sysType
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
		}
		//發展檢核
		public static function controlItemDS(ps_app:String, sysType:int, pb_addEmpty:Boolean=true):Array{
			var t_data:Object = {
				type: "controlItem",
				sysType: sysType
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
		}
		//發展檢核名稱
		public static function devControlNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "devControlName", pb_addEmpty);
		}
		//通報項目
		public static function leagalInfectItemNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "leagalInfectItemName", pb_addEmpty);
		}
		//期末評量
		public static function finalEvaluateItemDS(ps_app:String, sysType:int, pb_addEmpty:Boolean=true):Array{
			var t_data:Object = {
				type: "finalEvaluateItem",
				sysType: sysType
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
		}
		
		//職稱
		public static function titlesDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "Titles", pb_addEmpty);
		}
		//角色
		public static function roleDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "Role", pb_addEmpty);
		}
		//性別
		public static function genderDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "gender", pb_addEmpty);
		}
		//婚姻狀況
		public static function maritalStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "maritalStatus", pb_addEmpty);
		}
		//血型
		public static function bloodTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "bloodType", pb_addEmpty);
		}
		//在職狀況
		public static function dutyFlagDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "dutyFlag", pb_addEmpty);
		}
		//帳號使用狀況
		public static function deleFlagDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "deleFlag", pb_addEmpty);
		}
		//是否
		public static function ynFlagDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "ynFlag", pb_addEmpty);
		}
		//時段
		public static function timeInterDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "timeInter", pb_addEmpty);
		}
		//職稱別
		public static function cTitlesDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "cTitles", pb_addEmpty);
		}
		//幼兒觀察(主題)
		public static function subjectWatchDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "subjectWatch", pb_addEmpty);
		}		
		//電子聯絡簿(用餐情況)
		public static function dinesStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "dinesStatus", pb_addEmpty);
		}
		//電子聯絡簿(學習表現)
		public static function learnStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "learnStatus", pb_addEmpty);
		}
		//電子聯絡簿(午睡情況)
		public static function sleepStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sleepStatus", pb_addEmpty);
		}
		//電子聯絡簿(如廁情況 大便)
		public static function toiletStatusADS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "toiletStatusA", pb_addEmpty);
		}
		//電子聯絡簿(如廁情況 小便)
		public static function toiletStatusBDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "toiletStatusB", pb_addEmpty);
		}
		//幼兒保健(篩檢)
		public static function healthResultDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "healthResult", pb_addEmpty);
		}
		//排班表(工作項目)
		public static function jobsDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "jobs", pb_addEmpty);
		}
		//用藥時間
		public static function medTimeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "medTime", pb_addEmpty);
		}
		//狀態
		public static function urgentFlagDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "urgentFlag", pb_addEmpty);
		}
		//1 保健, 2 保險, 3 身體異常, 4 專案, 5 病症 項目資料
		public static function sysTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sysType", pb_addEmpty);
		}
		//教師(清潔及消毒工作)
		public static function checkSignDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "checkSign", pb_addEmpty);
		}
		//檢查者(清潔及消毒工作)
		public static function checkFlagDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "checkFlag", pb_addEmpty);
		}
		//學位
		public static function degreeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "degree", pb_addEmpty);
		}
		//就學狀態
		public static function studyStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "studyStatus", pb_addEmpty);
		}
		//期末評量選項
		public static function feItemDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "feItem", pb_addEmpty);
		}
		//特殊生
		public static function diagnosisDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "diagnosis", pb_addEmpty);
		}
		public static function cbStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "cbStatus", pb_addEmpty);
		}
		public static function mhBodyDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "mhBody", pb_addEmpty);
		}
		public static function sp51DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp51", pb_addEmpty);
		}
		public static function sp52DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp52", pb_addEmpty);
		}
		public static function sp53DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp53", pb_addEmpty);
		}
		public static function sp06DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp06", pb_addEmpty);
		}
		public static function sp07DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp07", pb_addEmpty);
		}
		public static function sp08DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp08", pb_addEmpty);
		}
		public static function sp09DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp09", pb_addEmpty);
		}
		public static function sp10DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp10", pb_addEmpty);
		}
		public static function sp11DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sp11", pb_addEmpty);
		}		
		//暴力通報
		public static function identityDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "identity", pb_addEmpty);
		}
		public static function uniStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "uniStatus", pb_addEmpty);
		}
		//公務單位
		public static function infoCategoryDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "infoCategory", pb_addEmpty);
		}
		//多筆項目資料
		public static function multiSysTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "multiSysType", pb_addEmpty);
		}
		//消防安全任務編組職稱
		public static function fireTitlesDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "fireTitles", pb_addEmpty);
		}
		//消防安全演練
		public static function drillContentDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "drillContent", pb_addEmpty);
		}
		public static function drillTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "drillType", pb_addEmpty);
		}
		//資料匯入項目 EXCEL
		public static function inputExcelTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "inputExcelType", pb_addEmpty);
		}
		//評鑑自評項目資料
		public static function selfEvaluateItemDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "selfEvaluateItem", pb_addEmpty);
		}
		//角色
		public static function userTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "userType", pb_addEmpty);
		}
		//角色 家長 + 職員
		public static function userTypeSearchDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "userTypeSearch", pb_addEmpty);
		}
		
		//交通車
		public static function babyCarInfoDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "BabyCarInfo", pb_addEmpty);
		}
		//學期年清單
		public static function acaSetupListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "AcaSetupList", pb_addEmpty);
		}
		//學年清單
		public static function academicYearListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "academicYearList", pb_addEmpty);
		}
		//學期清單
		public static function semesterListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "semesterList", pb_addEmpty);
		}
		//最近一學期之學期年清單
		public static function oriAcaSetupListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "oriAcaSetupList", pb_addEmpty);
		}
		//舊 學年(期) 班級(別)
		public static function oriStartClassListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "oriStartClassList", pb_addEmpty);
		}
		//新 學年(期) 班級(別)
		public static function nowStartClassListDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "nowStartClassList", pb_addEmpty);
		}
		//待辦事項 處理狀態
		public static function processFlagDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "processFlag", pb_addEmpty);
		}
		//待辦事項 處理狀態
		public static function proFlagReplyDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "proFlagReply", pb_addEmpty);
		}
		public static function excuseDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "excuse", pb_addEmpty);
		}
		
		//會計
		//收入/支出別
		public static function accMainTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "accMainType", pb_addEmpty);
		}
		//註冊/月費別
		public static function accTitleTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "accTitleType", pb_addEmpty);
		}
		//取 金額合計
		public static function sumOfMoneyDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "sumOfMoney", pb_addEmpty);
		}
		//取 金額合計
		public static function sumOfMoneyPaidDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "sumOfMoneyPaid", pb_addEmpty);
		}
		//取 金額合計
		public static function sumOfMoneyRequestPaidDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "sumOfMoneyRequestPaid", pb_addEmpty);
		}
		//支出選項
		public static function accTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "accType", pb_addEmpty);
		}
		public static function openAccMTI(ps_app:String, accType:int, p_win:Object, p_fn:Function, pb_all:Boolean=true):void{			
			var t_win:FindAccMTI = new FindAccMTI();
			Fun2.accType = accType;
			
			//show query window
			Fun.openPopup(t_win, p_win);			
			t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		//取總目名稱
		public static function accNameDS(ps_app:String, accType:int, pb_addEmpty:Boolean=true):Array{
			var t_data:Object = {
				type: "accName",
				accType: accType
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);
		}
		//取總目科目項目名稱
		/*
		public static function accIocNameDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "accIocName", pb_addEmpty);
		}
		*/
		public static function classDS(ps_app:String, pb_addEmpty:Boolean=true, p_getAll:Boolean=true):Array{			
			//return Fun.comboBoxDS(ps_app, "classInfo", pb_addEmpty);
			var t_data:Object = {
				type: "classInfo",
				getAll: p_getAll
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty);			
		}
		
		public static function staffDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.comboDS(ps_app, "staffInfo", pb_addEmpty);
		}
		
		/**
		 * 開啟一個搜尋學生的畫面
		 * @param ps_app app id
		 * @param p_win window object 上一層視窗
		 * @param p_fn 選取資料後返回處理資料的function
		 * @param pb_all 是否取得全部班別、班級資料，false則只會取得目前登入的老師所負責的班別、班級
		 */
		/**
		 * 開啟一個搜尋員工的畫面
		 * @param ps_app app id
		 * @param p_win window object 上一層視窗
		 * @param p_fn 選取資料後返回處理資料的function
		 */
		public static function openStudent(ps_app:String, p_win:Object, p_fn:Function, pb_all:Boolean=true):void{			
			var t_win:FindStu = new FindStu();
			t_win.ib_all = pb_all;			
			
			//show query window
			Fun.openPopup(t_win, p_win);			
			t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}		
		public static function openPStuList(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindPStuList = new FindPStuList();
			
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.init(ps_app, p_fn);
			//t_win.qry_1.fSelectRow = p_fn;
		}
		public static function openPStuListALL(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindPStuListAll = new FindPStuListAll();
		
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.init(ps_app, p_fn);
		}
		public static function openClassTeacherList(ps_app:String, p_win:Object, p_fn:Function, clSn:int):void{			
			var t_win:FindClassTeacherList = new FindClassTeacherList();
			t_win.classLeavesSn = clSn;
			
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.init(ps_app, p_fn);			
		}
		
		public static function openCalendarDetail(ps_app:String, p_win:Object, ymd:String):void{			
			var t_win:gCalendarDetail = new gCalendarDetail();
			t_win.theYearMonthDay = ymd;
			
			//show query window
			Fun.openPopup(t_win, p_win);
			//t_win.init(ps_app, p_fn);			
			t_win.init(ps_app);
		}

		public static function openStaff(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindStaff = new FindStaff();
			
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		public static function openStaffCal(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindStaffCal = new FindStaffCal();
			
			//show query window
			Fun.openPopup(t_win, p_win);
			//t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		//教具
		public static function openTeachCard(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindTeachCard = new FindTeachCard();
			
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.qry_1.fSelectRow = p_fn;
			
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		//圖書
		public static function openBookNo(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindBookNo = new FindBookNo();
			
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		//財產
		public static function openFortuneNo(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindFortuneNo = new FindFortuneNo();
			
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		//醫院
		public static function openPublicDept(ps_app:String, p_win:Object, p_fn:Function, clSn:int):void{			
			var t_win:FindPublicDept = new FindPublicDept();
			t_win.infoCategoryValue = clSn;
			//show query window
			Fun.openPopup(t_win, p_win);
			t_win.qry_1.fSelectRow = p_fn;
			
			t_win.qry_1.bCloseAfterQuery = false;
			t_win.fAfterOk = p_fn;
		}
		//週期
		public static function openWeekList(ps_app:String, p_win:Object, p_fn:Function):void{			
			var t_win:FindWeekList = new FindWeekList();
			
			Fun.openPopup(t_win, p_win);
			t_win.init(ps_app, p_fn);
		}

		// william End
		
		
		//=== for combobox and dataGrid labelFunction begin ===
		public static function yesEmptyDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "yesEmpty", pb_addEmpty);
		}

		public static function zeroEmpty(p_row:Object, p_column:GridColumn):String {	
			return (p_row[p_column.dataField] == 0) ? "" : p_row[p_column.dataField];
		}
		
		public static function rowStatusDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "rowStatus", pb_addEmpty);
		}
		
		//word property: 單字, 片語
		//public static function wordTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
		public static function wordPropDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "wordProp", pb_addEmpty);
		}
		
		//角色
		/*
		public static function roleDS(ps_app:String, pb_addEmpty:Boolean=true, pb_editWin:Boolean=true):Array{
			var ta_1:Array = Fun.comboDS(ps_app, "Role");
			return AR.addEmpty(ta_1, pb_addEmpty, pb_editWin);
		}
		
		//程式
		public static function progDS(ps_app:String, pb_addEmpty:Boolean=true, pb_editWin:Boolean=true):Array{
			var ta_1:Array = Fun.comboDS(ps_app, "Program");
			return AR.addEmpty(ta_1, pb_addEmpty, pb_editWin);		
		}
		*/
		
		/**
		 * 用戶資料
		 * @param ps_app
		 * @param ps_userType user type, S(student),T(teacher),A(admin),SM(smarten:睿采使用者)
		 */ 
		//public static function userDS(ps_app:String, ps_client:String, pb_addEmpty:Boolean=true):Array{
		public static function userDS(ps_app:String, ps_userType:String, pb_addEmpty:Boolean=true):Array{
			var t_data:Object = {
				type: "User",
				userType: ps_userType
			};
			return Fun.comboDS2(ps_app, t_data, pb_addEmpty, true);		 	
		}

		//老師
		public static function tchDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "teacher", pb_addEmpty, true);		 	
		}
		
		//班級
		public static function clsDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "cls", pb_addEmpty, true);		 	
		}
		
		/*
		//user type ds
		public static function userTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "userType", pb_addEmpty);
		}
		*/
		
		//user type ds
		public static function userType2DS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.comboDS(ps_app, "userType2", pb_addEmpty);
		}
		
		/*
		public static function funRangeDS(ps_app:String):Array{			
			return Fun.codeDS(ps_app, "funRange", false);
		}
		*/
		
		
		public static function rangeTypeDS(ps_app:String):Array{		
			return Fun.codeDS(ps_app, "rangeType", false);
		}
		
		public static function progTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.codeDS(ps_app, "progType", pb_addEmpty);
		}

		
		public static function custTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.codeDS(ps_app, "custType", pb_addEmpty);
		}
		
		//單字
		public static function wordTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS(ps_app, "wordType", pb_addEmpty);
		}
		public static function wordType2DS(ps_app:String, pn_typeSn:int, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS2(ps_app, {type:"wordType2", typeSn:pn_typeSn}, pb_addEmpty, true);		 	
		}
		
		//功能表群組
		public static function progGroupDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.codeDS(ps_app, "progGroup", pb_addEmpty);
		}

		//教材類別
		public static function matTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS(ps_app, "matType", pb_addEmpty);
		}
		public static function matType0DS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS(ps_app, "matType0", pb_addEmpty);
		}
		
		//系統題庫類別
		public static function examTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS(ps_app, "examType", pb_addEmpty);
		}
		public static function examType0DS(ps_app:String, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS(ps_app, "examType0", pb_addEmpty);
		}
		
		//傳回某個類別的類別和教材, 傳回 : isMat(0/1), sn, cName
		public static function typeMatDS(ps_app:String, pn_typeSn:int):Array{
			var t_data:Object = {
				data: "typeMat",
				typeSn: pn_typeSn
			};
			return Fun.readRows(ps_app, t_data);
		}
		
		//模考練習
		public static function stdSysExamDS(ps_app:String, pn_upSn:int):Array{
			var t_data:Object = {
				data: "StdSysExam",
				upSn: pn_upSn
			};
			return Fun.readRows(ps_app, t_data);
		}
		
		
		public static function sexDS(ps_app:String, pb_add:Boolean=true, pb_tw:Boolean=true):Array{						
			var ta_1:Array = (pb_tw) ?
				[
					{data:"F", label:"女"},
					{data:"M", label:"男"}
				]:
				[
					{data:"F", label:"Female"},
					{data:"M", label:"Male"}
				];
			return AR.addEmpty(ta_1, pb_add);
		}		
		
		
		//check
		public static function checkUser(ps_app:String, ps_userId:String, pf_callback:Function):String{
			var t_data:Object = {
				data: "User",
				userId: ps_userId
			};
			var ta_row:Array = Fun.readRows(ps_app, t_data);
			if (ta_row == null){
				return  "用戶資料不存在 !";				
			}else{
				pf_callback(ta_row[0]);
				return "";
			}
		}

		/*
		//open patent query window
		public static function winFindUser(ps_app:String, p_win:Object, pw_user:FindUser, p_data:Object, pf_callback:Function):void{
			
			if (pw_user == null){
				pw_user = new FindUser();
			}
			
			Fun.openPopup(pw_user, p_win);
			
			pw_user.qry_1.sApp = ps_app;
			pw_user.qry_1.fSelectRow = pf_callback;
			pw_user.qry_1.init();
			
			if (p_data.hasOwnProperty("userId")){
				Fun.setItem(pw_user.userId, p_data.userId);
			} 
		}	
		*/
		//=== for combobox and dataGrid labelFunction end ===
  					
  		
  		//=== get related field name begin ===
  		/**
  		 * get related field name
  		 * @param {string} ps_type return type.
  		 * @param {object} p_data data with query field value.
  		 * @param {boolean} pb_emptyMsg show msg if not found.
  		 * @return {string} name string.
  		 */ 
  		//public static function getName(ps_type:String, p_data:Object):String {
  		//public static function getRelatName(ps_type:String, p_data:Object, pb_emptyMsg:Boolean=true):String {
		/*
  		public static function getRelatInfo(ps_app:String, ps_type:String, p_data:Object, pb_emptyMsg:Boolean=true):Object {
  			switch (ps_type){
  				case "Staff":					
  					//var t_data:Object = staffInfo(ps_app, p_data);
  					//if (t_data == null && pb_emptyMsg){
					//	Fun.msg("E","用戶資料不存在 !");				 					
  					//}
  					//return t_data;					
					//return null;
  				default:
  					Fun.msg("E","Fun2.getRelatInfo().ps_type = '" + ps_type + "' is wrong !");
  					return null;
  			}  			
  		}   		
  		*/
  		//=== get related field name end ===


		/**
		 * return query field info.
		 * called by:
		 *   (1).DW.queryField()
  		 * @param {string} ps_type query type id.
  		 * //@param {object} p_data data with query field value, has below elements :
  		 *   //upWin: parent window.
  		 *   dw: dw no of this field.
  		 *   toFields: field array to return, ex:[memberType,memberId,memberName].
		 */   		
		/*
		public static function getQueryField(ps_type:String):Object {
  			switch (ps_type){
  				default:
  					return null;
  			}  			
		}
		*/
		
		
		//顯示主畫面
		public static function showMain():void{
			Object(Fun.wMain).showMain();
		}
		
		
		//顯示上一個畫面
		public static function showPreWin():void{
			Object(Fun.wMain).showItem(true);
		}
		
		/**
		 * 動態嵌入影片檔
		 * @param ps_path 影片檔案路徑
		 * @param p_cont 嵌入影片的容器 
		 * @param pb_border 是否顯示邊框，預設為是  
		 * @param pb_auto 是否自動撥放，預設為false
		 */  		
		public static function showVideo(ps_path:String, p_cont:Object, pb_border:Boolean=true, pb_auto:Boolean=false):void {
			var t_video:comVideo = new comVideo();
			
			t_video.is_path = ps_path;
			t_video.ib_border = pb_border;
			t_video.ib_autoPlay = pb_auto;
			
			if (p_cont.hasOwnProperty("width") && p_cont.width > 0)
				t_video.width = p_cont.width;
			
			if (p_cont.hasOwnProperty("height") && p_cont.height > 0)
				t_video.height = p_cont.height;
			
			p_cont.addElement(t_video);
			t_video.show();
		}
		
		/**
		 * 載入考試畫面, p_data 包含下列變數:
		 *   app, rowType, examSn, //isExam, canBack, exam, fAfterExam, groupH(optional), examMode, btnExit 
		 *   srcExam, shareFromSys(0/1)
		 * //param p_win 目前的 window/application, should be "this"
		 * //param ps_app app id
		 * //param ps_rowType 考試類型: S(sys:系統題庫), M(material:系統教材考試), TM(老師教材考試), TS(老師考試), R(run:闖關遊戲), W(word:單字測驗練習) 
		 * //param pn_examSn 考卷序號, 如果是單字練習, 則為單字 sn 
		 * ////param pb_sysExam true(模考)/false(, V(view:檢視), S(self:模考), L(lesson:課程), M(material:自學教材)
		 * //param pb_exam true(考試)/false(檢視)
		 * //param pn_canBack 是否可以回到上一題, 1(yes)/0(false)/-1(由資料表決定)
		 * //return error msg if any.
		 */  				
		//public static function startExam(p_win:Object, ps_app:String, ps_rowType:String, pn_examSn:int, ps_examTypeName:String="", pb_exam:Boolean=false, pb_canBack:Boolean=false):String{
		//public static function startExam(p_win:Object, ps_app:String, ps_rowType:String, pn_examSn:int, pb_exam:Boolean=false, pn_canBack:int=0):String{
		public static function loadExam(p_data:Object):void{
			//new and open WinExam
			/*
			var t_exam:StartExam = new StartExam();
			Fun.openPopup(t_exam, p_win);						
			return t_exam.show(ps_app, ps_rowType, pn_examSn, null, pb_exam, pn_canBack);
			*/
			//調整資料
			//if (!p_data.hasOwnProperty("shareFromExam"))
			//	p_data.shareFromExam = 0;
				//p_data.fromExam = p_data.examSn;
			
			//if (!p_data.hasOwnProperty("isSysExam"))
			//	p_data.isSysExam = 1;
			if (!p_data.hasOwnProperty("shareFromSys"))
				p_data.shareFromSys = true;
				
			p_data.isSysExam = Fun2.isSysExam(p_data.rowType);
			
			//set global variables
			Fun2.oVar.subItem = p_data;

			//load swf
			var t_item:Object = {
				data: p_data.app,
				swf: "appExam"
			};
			Object(Fun.wMain).openApp(t_item, false);
		}

		
		/**
		 * 整頁顯示考卷內容, p_data 包含下列變數:
		 * app, rowType, examSn, isExam, canBack, exam, fAfterExam
		 * 2012-5-14d Malcom add 加上一個變數: scoreSn(用來讀取用戶的答案) 
		 */  				
		public static function loadFullExam(p_data:Object, pf_afterClose:Function=null):void{
			//調整資料
			if (!p_data.hasOwnProperty("srcExam"))
				p_data.srcExam = p_data.examSn;
			//if (!p_data.hasOwnProperty("isFromTch"))
			//	p_data.isFromTch = 0;
					
			//set global variables
			Fun2.oVar.subItem = p_data;
			Fun2.oVar.fullExamAfterClose = pf_afterClose;
			
			//load swf
			var t_item:Object = {
				data: p_data.app,
				swf: "appFullExam"
			};
			Object(Fun.wMain).openApp(t_item, false);
		}

		
		//======= AI 單字相關功能 begin =========
		//3個傳回函數(以下皆同): dir, path, url
		//後面的文字(前面有底線)表示資料種類, ex: 100_b.png
		/**
		 * 利用單字序號傳回目錄名稱
		 * @param pn_sn {int} 單字序號
		 * @param pb_root {boolean} 是否包含 web site root 路徑
		 */ 		
		public static function getWordDir(ps_word:String, pb_root:Boolean):String{
			//var ts_dir:String = "Files/Word/" + ps_word.substr(0,1).toUpperCase() + "/" ; 
			//var ts_dir:String = "SysFiles/Word/" + ps_word.substr(0,1).toUpperCase() + "/" ; 
			//return (pb_root) ? (Fun.sDirRoot + ts_dir) : ts_dir;
			return Fun2.getPSDir("W", pb_root) + ps_word.substr(0,1).toUpperCase() + "/" ;
		}
		
		/**
		 * 取得單字相對路徑, for upload
		 * @param p_data 包含變數 word, sn, picExt
		 * //param pn_sn {int} 單字序號
		 * @param pb_root {boolean} 是否包含 web site root 路徑
		 */ 		
		//public static function getWordFile(p_row:Object, pb_sound:Boolean):String{
		public static function getWordPath(p_data:Object):Object{
			//return getWordDir(p_row.word, true) + p_row.sn + "." + (pb_sound ? "mp3" : p_row.picExt);
			var ts_file:String = getWordDir(p_data.word, false) + p_data.sn;
			var t_row:Object = {
				pic: ts_file + "." + p_data.picExt,
				sound: ts_file + ".mp3"
			};
			return t_row;
		}
		
		
		/**
		 * 傳回 AI 單字影音檔路徑  for view
		 * @param p_data 包含變數: word, sn, picExt, soundFlag, sound2Flag
		 * @return Object of dir, pic, sound, sound2 路徑(url) 
		 */ 
		//public static function getWordSource(p_data:Object):Object{
		public static function getWordUrl(p_data:Object):Object{
			var ts_dir:String = getWordDir(p_data.word, true);
			var ts_file:String = ts_dir + p_data.sn;
			var t_row:Object = {
				dir: ts_dir,
				pic: (p_data.picExt == "") ? "" : ts_file + "." + p_data.picExt,
				sound: (p_data.soundFlag == 1) ? ts_file + ".mp3" : ""
				//sound2: (p_data.sound2Flag == 1) ? ts_file + "_b.mp3" : ""
			};
			return t_row;
		}
		
		/**
		 * 傳回 word sample sound 檔名
		 * @param pn_sn sample sn.
		 */ 
		public static function getWordExpSound(pn_sn:int):String{
			//return "ex" + pn_sn + ".mp3";
			return pn_sn + "_ex.mp3";
		}
		//======= AI 單字相關功能 end =========

		
		//======= 考卷相關功能 begin =========		
		/**
		 * 傳回圖片(Picture)聲音(Sound)檔的目錄
		 * @param ps_rowType S(sys 系統題庫), T(teacher 老師考卷)
		 * @param pb_root {boolean} 是否包含 web site root 路徑
		 */ 		
		//public static function getExamDir(ps_rowType:String, pb_root:Boolean):String{
		public static function getPSDir(ps_rowType:String, pb_root:Boolean):String{
			var ts_dir:String;
			switch (ps_rowType){
				//case "S":	//系統題庫
				case "SE":	//系統題庫, will move to SysFiles/..
				case "SME":	//開放教材考卷, will move to SysFiles/..
					//ts_dir = "Files/SysExam/";
					ts_dir = "SysFiles/SysExam/";
					break;
				case "SM":	//開放教材, will move to SysFiles/..
					//ts_dir = "Material";
					//ts_dir = "Files/SysMat/";
					ts_dir = "SysFiles/SysMate/";
					break;
				case "PN":	//phone 音標
					ts_dir = "SysFiles/Phone/";
					break;
				
				/*
				case "R":	//闖關遊戲
					ts_dir = "RunExam";
					break;
				*/
				case "R":	//闖關遊戲
				//case "T":	//老師考卷
				//case "T":	//老師考卷, will remove, //temp add !! 	
				case "TE":	//老師考卷
				case "TME":	//老師教材考卷
					//ts_dir = "TchExam";
					ts_dir = "Files/Exam/";
					break;
				case "TM":	//老師教材
					//ts_dir = "Files/Material/";
					ts_dir = "Files/Mate/";
					break;
				case "W":	//單字, will move to SysFiles/..
					ts_dir = "SysFiles/Word/";
					break;
                
                //=== 2012-5-8 Malcom begin ===
                case "WT":	//單字類別
                    ts_dir = "SysFiles/WordType/";
                    break;
                //=== 2012-5-8 Malcom end ===                
                
				case "A":	//學生答案
					ts_dir = "Files/StdAns/";
					break;
				case "SET":	//system exam type
					ts_dir = "SysFiles/ExamType/";
					break;
				case "SMT":	//system material type
					//ts_dir = "SysFiles/MatType/";
					ts_dir = "SysFiles/MateType/";
					break;
				default:
					Fun.msg("E", "ps_rowType="+ ps_rowType+" is wrong in Fun2.as getPSDir().");
					return "";
			}
			
			//ts_dir = "Files/" + ts_dir + "/"; 
			return (pb_root) ? (Fun.sDirRoot + ts_dir) : ts_dir;
		}

		
		public static function getExamTail(ps_type:String):String{
			//case of 考試
			switch (ps_type){
				case "E":
					return "";
				case "S":
				case "B":
					return "_b";
				case "I":
					return "_i";
				case "A":
					return "_a";
				default:
					return "";	
			}			
		}
		
		
		/**
		 * 取得考卷結構的相對路徑, for upload
		 * @param p_data 包含變數 word, sn, picExt
		 * //param pn_sn {int} 單字序號
		 * @param pb_root {boolean} 是否包含 web site root 路徑
		 */ 		
		//public static function getWordFile(p_row:Object, pb_sound:Boolean):String{
		public static function getBigPath(ps_rowType:String, p_data:Object):Object{
			//return getWordDir(p_row.word, true) + p_row.sn + "." + (pb_sound ? "mp3" : p_row.picExt);
			var ts_file:String = getPSDir(ps_rowType, false) + p_data.sn;
			var t_row:Object = {
				pic: ts_file + "_b." + p_data.picExt,
				sound: ts_file + "_b.mp3"
			};
			return t_row;
		}
		
		
		/**
		 * 傳回考卷結構/題目/答案的影音檔路徑 for view
		 * @param ps_examType 考卷種類: S(sys 系統題庫),T(老師考卷)
		 * @param ps_rowType 資料種類: E(exam 考卷),B(big 結構),I(item 題目),A(ans 答案選項)
		 * @param p_data 包含變數: sn, picExt, soundFlag
		 * @return Object of dir, pic, sound 路徑(url) 
		 */ 
		//public static function getBigUrl(ps_type:String, p_data:Object):Object{
		public static function getExamUrl(ps_rowType:String, ps_itemType:String, p_data:Object):Object{
			var ts_tail:String = getExamTail(ps_itemType);
			var ts_dir:String = getPSDir(ps_rowType, true);
			var ts_file:String = ts_dir + p_data.sn + ts_tail;
			var t_row:Object = {
				dir: ts_dir,
				pic: (p_data.picExt == "") ? "" : ts_file + "." + p_data.picExt,
				sound: (p_data.soundFlag == 1) ? ts_file + ".mp3" : ""
			};
			return t_row;
		}
		//======= 考卷相關功能 end =========		
		
		
//============================
// by Daisy
		
		//by Daisy  試卷亂序類型
		public static function randTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "randType", pb_addEmpty);
		}
		
		/*
		//by Daisy  試卷大題類型
		public static function examTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			//return Fun.codeDS(ps_app, "examType", pb_addEmpty);
			return Fun.comboBoxDS(ps_app, "examType", pb_addEmpty);
		}
		*/
		public static function examType2DS(ps_app:String, pn_upSn:int, pb_addEmpty:Boolean=true):Array{		
			return Fun.comboDS2(ps_app, {type:"examType2", upSn:pn_upSn}, pb_addEmpty, true);		 	
		}
		
		public static function bigTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "bigType", pb_addEmpty);
		}
		
		//不包含題組的題型
		public static function bigType2DS(ps_app:String, ps_cond:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "bigType", pb_addEmpty, ps_cond);
		}
		
		//bigType
		public static function showBigDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "showBig", pb_addEmpty);
		}
		public static function scoreTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "scoreType", pb_addEmpty);
		}
		public static function sharedTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "sharedType", pb_addEmpty);
		}
		public static function timeTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "timeType", pb_addEmpty);
		}

		public static function wordForget(ps_app:String, pb_status:Boolean, ps_list:String):void{
			if (ps_list == "")
				return;
			
			var t_data:Object = {
				type: "WordForget",
				status: pb_status ? 1 : 0,
				list: ps_list
			};
			
			Fun.updateDB(ps_app, t_data);
		}

		
		/**
		 * 讀取考卷資料, 先判斷是否取得分享, 再檢查是否自動組卷
		 * 自動組卷時, 無法讀取整張考卷, 所以在這裡只能讀取考卷和結構的記錄
		 * 進行考試時, 再即時讀取大題所對應的題目和答案選項
		 * @param ps_app
		 * @param ps_rowType
		 * @param pn_examSn
		 * @param pn_srcExam 取得分享的來源考卷序號, 0 表示無, 如果為取得分享則無自動組卷功能
		 * @param pb_shareFromSys 是否從系統題庫取得分享, default false
		 * //param pb_userValue true(同時讀取user答案)
		 * 傳回Object包含以下變數: exam, stru, autoExamList(自動組卷來源考卷清單, 逗號分隔)	//item, //ans
		 */ 
		//public static function readExam(ps_app:String, ps_rowType:String, pn_examSn:int, pb_userValue:Boolean):Object{
		//public static function readExam(ps_app:String, ps_rowType:String, pn_examSn:int, pn_fromExam:int, pn_isFromTch:int, pb_userValue:Boolean):Object{
		public static function readExam(ps_app:String, ps_rowType:String, pn_examSn:int, pn_srcExam:int=0, pb_shareFromSys:Boolean=false):Object{
			//var tn_fromExam:int; 
			
			var tn_isSysExam:int = 0;
			//var tn_autoFrom:int = -1;	//-1(none),0(Exam),1(SysExam)
			//var tn_shareFrom:int = -1;	//-1(none),0(Exam),1(SysExam)
			//var tb_auto:Boolean = false;
			//var tb_shared:Boolean = false;
			//var tn_isSysExam:int;	//for SysExam
			//var tn_isSysExam2:int;	//for SysExamXXX
			switch (ps_rowType){
				case "SE":		//system exam, 有自動組整張考卷功能
					//tn_autoFrom = 1;
					//tn_shareFrom = 1;
				case "SME":		//system material exam
				case "W":		//word
					//pn_fromExam = pn_examSn;
					//tn_isSysExam = 1;
					tn_isSysExam = 1;
					//tn_isSysExam2 = 1;
					break;
				case "TE":		//teacher exam(lesson), 有自動組整張考卷功能, 可取得分享考卷
				//case "TME":	//teacher material exam
					//tn_autoFrom = 0;
					//tn_shareFrom = 0;
					//tb_shared = true;
					//tn_isSysExam = 0;
					//tn_isSysExam2 = pn_isFromTch;
					break;
				case "R":		//run
					//tn_isSysExam = 0;
					//tn_isSysExam2 = 0;
					break;
				default:
					Fun.msg("E", "ps_rowType=" + ps_rowType + " is Wrong !");
					return null;
					//tn_isSysExam = 1;
					//tn_isSysExam2 = 1;
					//break;					
			}

			//調整
			var tn_srcExam:int = pn_srcExam;
			//var tn_shareFromSys:int;
			if (tn_srcExam == 0){
				tn_srcExam = pn_examSn;
				//tn_shareFromSys = tn_isSysExam;
			//}else{
			//	tn_shareFromSys = pb_shareFromSys ? 1 : 0;
			}
			
			//var tn_fromExam:int; 
			//var tn_examSn:int; 
			//var tn_isFromTch:int;
			/*
			if (p_exam.hasOwnProperty("fromExam")){
				tn_fromExam = int(p_exam.fromExam);
				tn_examSn = int(p_exam.examSn);
				//tn_isFromTch = int(p_exam.tn_isFromTch);
			}else{
				tn_fromExam = int(p_exam.examSn);
				tn_examSn = tn_fromExam;				
				//tn_isFromTch = 0;
			}
			*/
			
			
			//由 rowType 決定 Exam or SysExam
			//get Exam
			var t_out:Object = {};
			var t_in:Object = {
				data: "Exam",
				//rowType: ps_rowType,
				isSysExam: tn_isSysExam,
				examSn: pn_examSn
				//fromExam: pn_fromExam,
			};
			t_in.type = "exam";
			t_out.exam = Fun.readRow(ps_app, t_in);
			
			
			//判斷是否有自動組卷功能			
			var ts_autoExamList:String = "";
			//if (tn_autoFrom >= 0 && int(t_out.exam.isAuto) == 1){
			if (int(t_out.exam.isAuto) == 1){
				t_in.type = "auto";
				var ta_auto:Array = Fun.readRows(ps_app, t_in);
				if (ta_auto == null){
					Fun.msg("E", "自動組卷的來源考卷清單為空白 !!");
					return null;
				}
				
				for (var i:int=0; i<ta_auto.length; i++){
					ts_autoExamList += ta_auto[i].srcExam + ",";
				}
				ts_autoExamList = ts_autoExamList.substr(0, ts_autoExamList.length - 1); 
			}
			t_out.autoExamList = ts_autoExamList;	//傳回 for 讀取item, ans rows
			
			
			//由 rowType 和 isSysExam 決定 ExamXXX or SysExamXXX
			//考卷結構
			t_in.examSn = tn_srcExam;
			t_in.isSysExam = tn_isSysExam;
			t_in.type = "big";
			t_out.stru = Fun.readRows(ps_app, t_in);
			
			/*
			//題目
			t_in.type = "item";
			t_out.item = Fun.readRows(ps_app, t_in);
			
			//答案選項
			t_in.type = "ans";
			t_out.ans = Fun.readRows(ps_app, t_in);
			*/
			
			return t_out;
		}

		
		/**
		 * 傳回 big[], called by appExam.mxml, appFullExam.mxml
		 * 大題相關資料, 陣列長度為實際的大題數, 不是考卷結構數目
		 * 大題資料, 包含欄位: struNo, bigType, right(答對的題目數), score(大題分配分數), okScore(及格分數),
		 *   showItems(顯示的題目數), itemScore(每題的得分)
		 *   anItem(大題題目序號陣列), 
		 * @param pb_random 是否考慮亂序
		 */ 
		//public static function bindExam(p_exam:Object, pb_random:Boolean):Array{
		public static function bindExam(p_exam:Object):Array{
			var t_exam:Object = p_exam.exam;
			var ta_stru:Array = p_exam.stru;
			//var ta_item:Array = p_exam.item;
			//var ta_ans:Array = p_exam.ans;
			var ta_big:Array = [];
			
			//binding big(anItem), item(anAns), ans, 考慮亂序
			var t_stru:Object;
			var tn_bigSn:int;
			//var t_item:Object;
			//var tn_itemSn:int;
			//var tn_items:int = (ta_item == null) ? 0 : ta_item.length;
			//var tn_anss:int = (ta_ans == null) ? 0: ta_ans.length;
			var tn_findBig:int = 0;
			
			/*
			var tb_randAns:Boolean = pb_random ? ((t_exam.randType as String).indexOf("A") >= 0) : false;
			//答案不能亂序的題型
			if (tb_randAns){	
				var ts_noList:String = "BF";
				if (ts_noList.indexOf(t_exam.bigType) >= 0){
					//tb_randAns = false;	//open ??
				}
			}
			*/
			
			//var tn_nextItem:int = 0;
			//var tn_nextAns:int = 0;
			var tn_bigs:int = 0;
			var tn_strus:int = (ta_stru != null) ? ta_stru.length : 0;
			for (var tn_stru:int=0; tn_stru<tn_strus; tn_stru++){
				t_stru = ta_stru[tn_stru];
				if (t_stru.bigType == null || t_stru.bigType == "")	//只處理有大題的考卷結構
					continue;
				
				tn_bigSn = t_stru.sn;
				/*
				tn_bigs++;
				
				var tan_item:Array = [];
				var tn_findItem:int = 0;
				//for (var tn_item:int=tn_nextItem; tn_item<tn_items; tn_item++){
				for (var tn_item:int=0; tn_item<tn_items; tn_item++){
					
					//binding item to big
					t_item = ta_item[tn_item];
					if (t_item.bigSn == tn_bigSn){
						tan_item[tn_findItem] = tn_item;
						tn_findItem++;
					}else if(tn_findItem > 0){	//alread find
						//tn_nextItem = tn_item+1;	//下一個要查詢的 item, 不需要從0開始
						break;
					}else{
						continue;
					}
					
					//binding ans to item
					tn_itemSn = t_item.sn;
					var tan_ans:Array = [];
					var tn_findAns:int = 0;
					//for (var tn_ans:int=tn_nextAns; tn_ans<tn_anss; tn_ans++){
					for (var tn_ans:int=0; tn_ans<tn_anss; tn_ans++){
						if (ta_ans[tn_ans].itemSn == tn_itemSn){
							tan_ans[tn_findAns] = tn_ans;
							tn_findAns++;
						}else if(tn_findAns > 0){	//already find
							//tn_nextAns = tn_ans+1;	//下一個要查詢的 ans, 不需要從0開始
							break;
						}else{
							continue;
						}							
					}
					
					//答案亂序
					//t_item.anAns = (tb_randAns) ? AR.random(tan_ans, true) : tan_ans;						
					t_item.anAns = (tb_randAns) ? AR.random(tan_ans, "") : tan_ans;
				}
				*/
				
				//ian_bigItem[tn_findBig] = tan_item;
				var tn_score:int = int(t_stru.score);
				var tn_showItems:int = int(t_stru.showItems);
				/*
				if (tn_showItems == 0){
					tn_showItems = tan_item.length;
				}else if (tn_showItems > tan_item.length){
					tn_showItems = tan_item.length;						
				}
				*/
				ta_big[tn_findBig] = {
					struNo: tn_stru,
					bigType: t_stru.bigType,
					right: 0,
					//anItem: tan_item,	//??
					showItems: tn_showItems,
					score: tn_score,
					okScore: int(t_stru.okScore)
					//itemScore: (t_exam.scoreType == "1") ? 1 : Math.round(tn_score / tn_showItems)	//2012-5-15b Malcom remark: 移到 appExam.mxml, 上一行後面的逗號要移除
				};
				
				//t_stru.anItem = tan_item;
				t_stru.bigRow = tn_findBig;
				//t_stru.struItems = tan_item.length;		//同時記錄這個結構有幾個題目(for big only 限制答題時間)
				tn_findBig++;
			}
			
			//return tn_bigs;
			return ta_big;
			
		}
		

		/**
		 * 傳回 {item, ans}
		 * called by appExam, appFullExam
		 * @param pb_userValue 是否讀取用戶答案 (2012-5-9 Malcom add)  
		 * @param pn_scoreSn 是否讀取用戶答案 (2012-5-14d Malcom add)  
		 */ 
		//public static function getBigItem(ps_app:String, p_stru:Object, p_data:Object):Object{
		//public static function getBigItem(ps_app:String, p_stru:Object, p_data:Object, pb_userValue:Boolean=false):Object{
		public static function getBigItem(ps_app:String, p_stru:Object, p_data:Object, pb_userValue:Boolean=false, pn_scoreSn:int=0):Object{
			//讀取題目和答案(因為自動組卷功能, 系統無法在一開始讀取整張考卷的內容 !!)
			//1.正常的狀況
			//2.取得分享考卷
			//3.自動組卷
			//讀取 item
			var t_data:Object = {
				data: "Exam",
				type: "item",
				isSysExam: (p_data.isSysExam)? 1 : 0,
				autoExamList: p_data.autoExamList,
				orderNo: p_stru.orderNo,		//for auto Exam
				bigSn: p_stru.sn,
				isGroup: (p_stru.bigType == "GP") ? 1 : 0,	//如果是自動組卷而且是題組, 則只會讀取某一回的題目(不混卷)
				showItems: p_data.showItems,						
				randItem: (p_data.randItem) ? 1 : 0
				//randAns: p_data.randAns
			};

			var ta_item:Array = Fun.readRows(ps_app, t_data);
			var tn_items:int = ta_item.length;
            
			//if (p_stru.showItems > 0 && tn_items > p_stru.showItems){
			//	tn_items = p_stru.showItems;
			//}
			
			
			//讀取 ans
			//get item list
			var ts_items:String = "";
			//var tn_items:int = ta_item.length;
			var i:int;
			for (i=0; i<tn_items; i++){
				ts_items += ta_item[i].sn + ",";
			}
			t_data.type = "ans";
			t_data.itemList = ts_items.substr(0, ts_items.length - 1);
			var ta_ans:Array = Fun.readRows(ps_app, t_data);
			//i_itemData.ansList = ta_ans;	
			
			
			//讀取用戶答案(某個大題)
			var ta_userValue:Array;
			var tn_values:int;
			if (pb_userValue){
				t_data.type = "userAns";
				t_data.scoreSn = pn_scoreSn;		//2012-5-14d Malcom add				
				t_data.bigSn = p_stru.sn;				
				ta_userValue = Fun.readRows(ps_app, t_data);
				tn_values = (ta_userValue != null) ? ta_userValue.length : 0;				
			}
			
			
			//binding ans to item
			var tn_itemSn:int;
			var tn_ansLen:int = (ta_ans != null) ? ta_ans.length : 0;
			for (i=0; i<tn_items; i++){
				//ts_items += ia_item[i].sn + ",";
				tn_itemSn = ta_item[i].sn;
				var tan_ans:Array = [];
				var tn_findAns:int = 0;
				//for (var tn_ans:int=tn_nextAns; tn_ans<tn_anss; tn_ans++){
				for (var tn_ans:int=0; tn_ans<tn_ansLen; tn_ans++){
					if (ta_ans[tn_ans].itemSn == tn_itemSn){
						tan_ans[tn_findAns] = tn_ans;
						tn_findAns++;
					}else if(tn_findAns > 0){	//already find
						//tn_nextAns = tn_ans+1;	//下一個要查詢的 ans, 不需要從0開始
						break;
					}else{
						continue;
					}							
				}
				
				//答案亂序
				ta_item[i].anAns = (p_data.randAns) ? AR.random(tan_ans, "") : tan_ans;

				//set userValue to item
				if (pb_userValue){
					for (var j:int=0; j<tn_values; j++){
						if (ta_userValue[j].itemSn == tn_itemSn){
							ta_item[i].userValue = ta_userValue[j].userValue;
							ta_item[i].scoreAnsSn = ta_userValue[j].sn;		//2012-5-12 Malcom add							
							continue;
						}													
					}					
				}
			}
			
			return {item:ta_item, ans:ta_ans};
		}
		
		
		/**
		 * 顯示單字音標(圖檔) 
		 */ 
		import spark.components.HGroup;
		import spark.components.Image;
		public static function showPhoneImage(p_box:HGroup, ps_phone:String):void{
			//顯示音標, 暫時使用 e-touch 的做法
			var ts_phone:String = "[" + ps_phone + "]";
			//var ts_dir:String = Fun.sDirRoot + "Files/Phone/" ;
			var ts_dir:String = Fun2.getPSDir("PN", true); 
				//Fun.sDirRoot + "Files/Phone/" ;
			
			//var ta_image:Array = [];
			p_box.removeAllElements();
			var t_image:Image;
			for (var i:int=0; i<ts_phone.length; i++){
				//ta_image[i] = new Image();
				t_image = new Image();
				//Fun.loadImage(ta_image[i], ts_dir + ts_phone.substr(i, 1) + ".gif") 
				//phone.addElement(ta_image[i]);
				Fun.loadImage(t_image, ts_dir + ts_phone.substr(i, 1) + ".gif") 
				p_box.addElement(t_image);
			}			
		}
		
		
		//load background image
		private static var oBGImage:Object;		//主畫面背景圖 cache !!		
		public static function loadBG(p_image:Object):void{
			if (oBGImage == null){
				Fun.loadImage(p_image, Fun2.sBackImage, false, loadBG2);
			}else{
				p_image.source = oBGImage; 
			}
		}
		
		private static function loadBG2(p_image:Object):void{
			oBGImage = p_image;
		}
		
		
		//利用 rowType 來判斷是否為 SysExam/Exam
		public static function isSysExam(ps_rowType:String):Boolean {
			switch (ps_rowType){
				case "SE":		//system exam, 有自動組整張考卷功能
				case "SME":		//system material exam
				case "W":		//word
					return true;
				case "TE":		//teacher exam(lesson), 有自動組整張考卷功能, 可取得分享考卷
				case "TME":
				case "R":		//run
					return false;
				default:
					Fun.msg("E", "ps_rowType=" + ps_rowType + " is Wrong !");
					return false;
			}			
		}
		
		/**
		 * get exam table name
		 * @param ps_rowType 
		 */ 
		/*
		public static function examTable(ps_rowType:String, ps_examType:String):String{
			var ts_table:String = (ps_rowType=="S" || ps_rowType=="SM") ? "SysExam" : "Exam";
			switch (ps_examType){
				case "E":
					break;
				case "B":
					ts_table += "Big";
					break;
				case "I":
					ts_table += "Item";
					break;
				case "A":
					ts_table += "Ans";
					break;
			}
			return ts_table;
		}
		*/
		/*
		//by Daisy  試卷图片在上
		public static function locationDS(ps_app:String, pb_add:Boolean=true, pb_tw:Boolean=true):Array{						
			var ta_1:Array = (pb_tw) ?
				[
					{data:"0", label:"是"},
					{data:"1", label:"否"}
				]:
				[
					{data:"0", label:"Yes"},
					{data:"1", label:"No"}
				];
			return Fun.arrayAddEmpty(ta_1, pb_add);
		}
		*/
		//============================
		
		
		//=== add by Louis - start ===
		//取得老師所開的課程及考卷
		public static function lessonExamDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "lessonExam", pb_addEmpty, true);
		}
		
		//傳回學生進行某測驗次數
		public static function examTimesDS(ps_app:String, pn_examSn:int):Array{
			var t_data:Object = {
				data: "examTimes",
				examSn: pn_examSn
			};
			return Fun.readRows(ps_app, t_data);
		}		
		
		public static function schoolYearDS(ps_app:String, pb_addEmpty:Boolean=true):Array{
			return Fun.comboDS(ps_app, "schoolYear", pb_addEmpty);	 	
		}
		
		/**
		 * 依照容器大小等比例換算嵌入的元件的長寬(填滿容器)
		 * @param p_data 元件資料，包含X座標(locX)、Y座標(locY)、寬(width)、高(height)
		 * @param p_cont 嵌入元件的容器 
		 * @return Object資料，其中包含locX: X座標, locY: Y座標, width: 寬, height: 高
		 */  		
		/*
		public static function checkWidthHeight(p_data:Object, p_cont:Object):Object {
			var tn_temp:Number;
			var tn_locX:Number, tn_locY:Number, tn_width:Number, tn_height:Number;
			
			if (p_data.hasOwnProperty("width") && p_data.width <= p_cont.width) {
				tn_width = p_data.width;
			} else {
				tn_width = p_cont.width;
			}
			if (p_data.hasOwnProperty("height") && p_data.height <= p_cont.height) {
				if (p_data.hasOwnProperty("width")) {
					if (p_data.width <= p_cont.width) {
						tn_height = p_data.height;
					} else {
						tn_height = tn_width * p_data.height / p_data.width;
					}					
				} else {
					tn_temp = p_data.height * 16 / 9;
					if (tn_temp > p_cont.width) {
						tn_width = p_cont.width;
						tn_height = tn_width * 9 / 16;
					} else {
						tn_width = tn_temp;
						tn_height = p_data.height;
					}
				}
			} else {
				tn_height = (tn_width * 9) / 16;
			}
			
			if (p_data.hasOwnProperty("locX")) {
				tn_temp = int(p_data.locX) + tn_width;
				if (tn_temp <= p_cont.width) {
					tn_locX = p_data.locX;
				} else {
					tn_locX = 0;
				}
			}
			
			if (p_data.hasOwnProperty("locY")) {
				tn_temp = int(p_data.locY) + tn_height;
				if (tn_temp <= p_cont.height) {
					tn_locY = p_data.locY;
				} else {
					tn_locY = 10;
				}
			}
			return {locX: tn_locX, locY: tn_locY, width: tn_width, height: tn_height};
		}	
		*/
		//=== add by Louis - end ===

		
		//=== add by Louis - start: will remark ===
		//傳回類別或某個類別的子類別
		/*
		public static function matTypesDS(ps_app:String, pn_typeSn:int):Array{
			var t_data:Object = {
				data: "matTypes",
				typeSn: pn_typeSn
			};
			return Fun.readRows(ps_app, t_data);
		}		

		
		//由Code Table取得子教材類型供Combobox或DropDownList使用
		public static function matSubTypeDS(ps_app:String, pb_addEmpty:Boolean=true):Array{			
			return Fun.codeDS(ps_app, "matSubType", pb_addEmpty);
		}		
		
		
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
				var ts_fileType:String = "";
				if (String(Fun.csPicTypes+",").indexOf(ts_ext+",") >= 0) {
					ts_fileType = "pic";        //圖檔
				} else if (String(Fun.csVideoTypes+",").indexOf(ts_ext+",") >= 0) {
					if (ts_ext != "swf")
						ts_fileType = "mov";    //影片檔
					else
						ts_fileType = "swf";    //flash動畫
				} else {
					Fun.msg("E", "Fun2.getFileData().p_data.ext = illegal file type '." + ts_ext + "'");
					return null;
				}
				//var ts_file:String = getExamDir('M',pb_root) + ts_fileName + p_data.sn + "." + ts_ext;
				var ts_file:String = getPSDir('SM',pb_root) + ts_fileName + p_data.sn + "." + ts_ext;
				var t_data:Object = {
					type: ts_fileType,
					file: ts_file 
				}
				return t_data;
			} else {
				Fun.msg("E", "Fun2.getFileData().p_data.ext = file type cannot be empty.");
				return null;
			}
		}   		
		*/
			

		//modify by Louis 20120420 - start
		public static function loadMate(p_data:Object):void{
			//new and open WinMaterial
			//set global variables
			Fun2.oVar.material = p_data;
			
			//load sdf
			var t_item:Object = {
				data: p_data.app,
				swf: "LoadMate"
			};
			Object(Fun.wMain).openApp(t_item, false);
		}
		//modify by Louis 20120420 - end		
		
		
   	}//class
}
