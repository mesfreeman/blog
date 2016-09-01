<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<script>
var id = $(".btn-like").attr('data-cid');
var zan = id + "yi_zan";
if (localStorage.cid == zan || sessionStorage.cid == zan) {
    $("#zanId").text("已赞");
}
$(".btn-like").on("click", function(){
    if (localStorage.cid == zan || sessionStorage.cid == zan) {
        return;
    }
	$.post('<?php Helper::options()->index('/action/views?up'); ?>',{
		cid:id
	},function(data){
		$("#zanId").text("已赞");
	    $(".post-likes-num").text(parseInt($(".post-likes-num").text()) + 1);
	    localStorage.cid = zan;
	    sessionStorage.cid = zan;
	},'json');
});
</script>