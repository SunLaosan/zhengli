<script src="__ADMIN__fileupload/jquery-1.8.2.min.js"></script>
<script src="__ADMIN__fileupload/jquery.ui.widget.js"></script>
<script src="__ADMIN__fileupload/jquery.iframe-transport.js"></script>
<script src="__ADMIN__fileupload/jquery.fileupload.js"></script>


<img src="__IMAGE__{$content.img9}" alt="" style="width:200px;height:150px" id="image1">
<input id="fileupload1" type="file" name="files" style="width:68px;height:22px;" multiple>
<input type="hidden" id="image9" value="">


<script>
//后台处理上传地址
var uploadUrl="{:url('contact/uploadimg')}";

$(function () {

    $('#fileupload1').fileupload({
        url:uploadUrl,
        dataType: 'json',
        multipart:true,
        done: function (e, data) {
            //console.log(data.result);
            $('#image9').val(data.result.url);
            $('#image1').attr('src',"__IMAGE__"+data.result.url);
        }
    });

});
</script>