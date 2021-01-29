<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/default/easyui.css">
    <link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/icon.css">
    <link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/themes/color.css">
    <link rel="stylesheet" type="text/css" href="http://www.jeasyui.com/easyui/demo/demo.css">
	<!-- <script type="text/javascript" src="../function.js"></script>-->	
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
	<script type="text/javascript" src="http://www.jeasyui.com/easyui/jquery.easyui.min.js"></script>
	<script type="text/javascript" src="http://www.jeasyui.com/easyui/locale/easyui-lang-zh_TW.js"></script>
	<script type="text/javascript" src="http://www.jeasyui.com/easyui/datagrid-detailview.js"></script>
	<script type="text/javascript">
		var histroy = "no";
    	//$.ajaxSetup({
        //    data: {csrfmiddlewaretoken: '{{ csrf_token() }}'},
        //});
        $.ajaxSetup({
        	headers: {
            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        	}
        });
        
        function formatDate(val,row){
            if (val == "2000-01-01"){
                return '';
            } else {
                return val;
            }
        }
        
    	function myformatter(date){
            var y = date.getFullYear();
            var m = date.getMonth()+1;
            var d = date.getDate();
            return y+'-'+(m<10?('0'+m):m)+'-'+(d<10?('0'+d):d);
        }
        
        function myparser(s){
            if (!s) return new Date();
            var ss = (s.split('-'));
            var y = parseInt(ss[0],10);
            var m = parseInt(ss[1],10);
            var d = parseInt(ss[2],10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)){
                return new Date(y,m-1,d);
            } else {
                return new Date();
            }
        }
		
		
        function newContent(){
        	$('#fc').form('clear');
        	$('#Deadline').datebox({ readonly: false });
			$('#dlc').dialog('open').dialog('setTitle','新增待辦事項');
			$('#fc_token').val($('meta[name="csrf-token"]').attr('content'));
			$('#fc_id').val("-1");
        }
        
        function saveContent(){
        	
        	$('#fc').form('submit',{
				url:'savecontent.php',
				onSubmit: function(){
					return $(this).form('validate');
				},
				success: function(data){
					alert(data);
					$('#dlc').dialog('close');        // close the dialog
					$('#dg').datagrid('reload');    // reload the user data
				}
			});
        }
        
        function insertnecase(id){
        	$.post('updatecontroller.php',{id:id,_token:$('meta[name="csrf-token"]').attr('content')},function(data){
            	//alert(data);
        		$('#dg').datagrid('reload');
        	});        	
        }
        
        function editContent(){
	    	var row = $('#dg').datagrid('getSelected');
	        $('#fc').form('clear');
	        $('#fc_token').val($('meta[name="csrf-token"]').attr('content'));
	        $('#Deadline').datebox({ readonly: true });
	    	if(row){
	    		$('#fc').form('load',row);
				$('#Content').textbox().textbox('setValue',row.Content.replace(/<br \/>/g,""));
	    	   	$('#dlc').dialog('open').dialog('setTitle','編輯待辦事項');
			}
	    	else alert('請選擇一筆資料');
	    }
	    
	    function historylist(){	 
	    	if ( histroy == "no" ) {
	    		histroy = "yes";
	    	}else{
	    		histroy = "no";
	    	}
	    	console.log(histroy);
	    	$('#dg').datagrid({
	    		queryParams: {
	    			history: histroy
				}
			});			
	    }
	    /*
	    function reject(){
	    	var row = $('#dg').datagrid('getSelected');
	    	if(row.Checked == "0") alert("此筆資料尚未確認!!");
	    	else{
	    		if(confirm('是否退回'+row.Account+" "+row.Deadline+"的"+row.Content)){
	    			$.post("reject.php",{id:row.id},function(etc){
	    				if(etc == 1)
	    				{
	    					alert("成功!!");
	    					$('#dg').datagrid('reload');
	    				}
	    				else alert("失敗!!");
	    			});
	    		}
	    	}
	    }*/
        $(function(){
        	$('#dg').datagrid({
        		onLoadSuccess:function(data){
                	$('#dg').datagrid('getPanel').find('div.datagrid-header input[type=checkbox]').attr('disabled','disabled');
	            },
	            rowStyler: function(index,row){
                	var dt = new Date();
                	var month = dt.getMonth()+1;
                	var day = dt.getDate();
                	var year = dt.getFullYear();
                	var tempDeadline = row.Deadline.split('-');
                	
                	var date1 = new Date(tempDeadline[0], tempDeadline[1]-1, tempDeadline[2]);
					var date2 = new Date(year, month-1, day);
					
					var date1_unixtime = parseInt(date1.getTime() / 1000);
					var date2_unixtime = parseInt(date2.getTime() / 1000);                	
                	
                	var timeDifference = date1_unixtime - date2_unixtime;
                	var timeDifferenceInHours = timeDifference / 60 / 60;
					var timeDifferenceInDays = timeDifferenceInHours  / 24;
                	//alert(tempDeadline+"-"+year+"-"+month+"-"+day+"====="+date1_unixtime+"-"+date2_unixtime+"-"+timeDifferenceInDays);
                	if(row.Del == 1)
                	{
                		return 'background-color:black;color:white;font-weight:bold;';
                	}
                	else if(row.Checked == 1)
                	{
                		return 'background-color:gray;color:white;font-weight:bold;';
                	}
                	else if(timeDifferenceInDays < 0)
                	{                		
                		return 'background-color:#FF44AA;color:black;font-weight:bold;';
                	}
                	else if(timeDifferenceInDays>=0 && timeDifferenceInDays<3)
                	{
                		return 'background-color:yellow;color:blue;font-weight:bold;';
                	}
                	//}
                },
	            onCheck: function (index, row) {
	            	//alert(Account);
	            	if(row.Checked == 0) {
	            		insertnecase(row.ID);
	            	}
	            	else {
		            	alert('Already Done!!');
	            	}
              	}
        	});
        });

    </script>
	<style type="text/css">
        .datagrid-row-selected {
            background: #ffe48d !important;
        }
        #fm{
            margin:0;
            padding:10px 30px;
        }
		#fn{
            margin:0;
            padding:10px 30px;
        }
        .ftitle{
            font-size:14px;
            font-weight:bold;
            padding:5px 0;
            margin-bottom:10px;
            border-bottom:1px solid #ccc;
        }
        .fitem{
            margin-bottom:5px;
        }
        .fitem label{
            display:inline-block;
            width:100px;
        }
        .fitem input{
            width:160px;
        }
    </style>
</head>
<body>
<center>
    <font size="20" color="green">代辦事項</font><br>
    <table id="dg" class="easyui-datagrid" style="width:auto;height:auto" striped="true"
            url="get_controllerlist.php" pagination="true" pageList="[20,30,40]" pageSize="20"
            toolbar="#toolbar" pagination="true" nowrap="false" checkOnSelect="false" selectOnCheck="true"
            rownumbers="true" fitColumns="true" singleSelect="true">
        <thead>
            <tr>
                <th field="ID" width="50" hidden="true" ></th>
                <th field="Checked" width="50" hidden="true"></th>
                <th field="Del" width="50" hidden="true" ></th>
                <th data-options="field:'ck',checkbox:true"></th>
                <!-- <th field="Account" width="50" align='center' sortable="true">執行者</th> -->
                <th field="Content" width="320" align='left' >待辦事項</th>
                <th field="CreateDate" width="50" align='center' >建立日期</th>
                <th field="DealDate" width="50" align='center' >更新日期</th>
                <th field="Deadline" width="50" align='center' sortable="true">時限</th>
                <th field="CompleteDate" width="50" align='center' sortable="true" formatter="formatDate">完成日期</th>
            </tr>
        </thead>
		<tbody>
		</tbody>
    </table>
	<div id="toolbar">
        <!--<a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="newUser()">New User</a>-->
        <a href="javascript:void(0)" class="easyui-linkbutton c7" iconCls="icon-add" plain="true" onclick="newContent()">新增待辦事項</a>
        <a href="javascript:void(0)" class="easyui-linkbutton c5" iconCls="icon-edit" plain="true" onclick="editContent()">編輯待辦事項</a>
        <a href="javascript:void(0)" class="easyui-linkbutton c3" iconCls="icon-man" plain="true" onclick="historylist()">歷史紀錄</a>
        <!-- <a href="javascript:void(0)" class="easyui-linkbutton c2" id="reject" data-options="disabled:true" plain="true" onclick="reject()">退回</a> -->
    </div>
	
	<div id="dlc" class="easyui-dialog" style="width:550px;height:auto;padding:10px 10px;top:5%; left:30%;"
            closed="true" buttons="#dlc-buttons">
        <form id="fc" method="post" novalidate>
        	<input id="fc_token" name="_token" type="hidden" value="" />
		    <!-- <input name="ID" type="hidden">-->
		    <input id="fc_id" name="ID">
		    <div class="fitem">
                <label>待辦事項：</label>
                <input id="Content" name="Content" class="easyui-textbox" data-options="required:true,multiline:true" style="width:400px;height:150px">
            </div>
			<div class="fitem">
                <label>時限：</label>
                <input id='Deadline' name="Deadline" class="easyui-datebox" data-options="required:true,editable:false,formatter:myformatter,parser:myparser">
            </div>
        </form>
    </div>
    <div id="dlc-buttons">
        <a href="javascript:void(0)" class="easyui-linkbutton c6" iconCls="icon-ok" onclick="saveContent()" style="width:90px">存檔</a>
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-cancel" onclick="javascript:$('#dlc').dialog('close')" style="width:90px">取消</a>
    </div>
</center>	
</body>
</html>
