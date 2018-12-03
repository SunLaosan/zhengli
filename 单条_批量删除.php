/**引入jquery**/
/*引入layer*/
<script type="text/javascript" src="layer.js"></script>
<script>
/*批量删除*/
window.location.href="{:url('index/address')}";

function batch_del()
{
	layer.confirm('确认要删除吗?',function(index)
	{
		var cks=$("[name='delids']");  
		var str='';  //拼接id  
		for(var i=0;i<cks.length;i++){  
			if(cks[i].checked){  
				if(cks[i].value!=0){
					str+=cks[i].value+","; 
				}
				
		 	}  
		 }  
		 //去掉字符串末尾的‘,'  
		 str=str.substring(0, str.length-1); 
		console.log(str);
		$.ajax({
			type: 'POST',
			url: "{:url('news/newsrecycledo')}",
			dataType: 'json',
			data:{
				id:str
			},
			success: function(data){
				if(data.code==1){
					layer.msg('已删除!',{icon:1,time:1000});
					 location.reload(true);
				}else{
					layer.msg(data.msg+'删除失败!',{icon:5,time:1000});
				}
			},
			error:function(data) {
				console.log(data);
			},
		});		
	});
}
/*单条删除*/
function single_del(obj,id){
	layer.confirm('确认要删除吗？删除可在回收站还原！',function(index){
		$.ajax({
			type: 'POST',
			url: "{:url('模块/控制器/方法')}",
			data:{
				id:id
			},
			dataType: 'json',
			success: function(data){
				if(data.code==1){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
				}else{
					layer.msg(data.msg+'删除失败!',{icon:5,time:1000});
				}
			},
			error:function(data) {
				console.log(data.msg);
			},
		});		
	});
}
</script>
