<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    </div>
</div>
<footer>
   <div class="footer-inner">
    <p>
		<?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
        <?php while($pages->next()): ?>
        <a href="<?php $pages->permalink(); ?>" title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a> |
        <?php endwhile; ?>
		<a href="<?php $this->options->feedUrl(); ?>"><?php _e('文章 RSS'); ?></a> |
        <a href="<?php $this->options->commentsFeedUrl(); ?>"><?php _e('评论 RSS'); ?></a>
	</p>
    <p> &copy; <?php echo date('Y');?> <a href="<?php $this->options->siteUrl(); ?>" target="_blank"> <?php $this->options->title() ?> </a>
        <?php _e(' / Wechat id <a href="javascript:void(0)" target="_blank">mesfreeman</a>'); ?>
        <?php _e(' / Author is <a href="http://wpa.qq.com/msgrd?v=3&uin=1287103625&site=qq&menu=yes" target="_blank">自由人</a>'); ?>
        <?php if ($this->options->icpNum): ?>
           / <a href="javascript:void(0)"><?php $this->options->icpNum(); ?></a>
        <?php endif; ?>
		<?php if($this->options->siteStat):?><?php $this->options->siteStat();?><?php endif;?>
	</p>
   </div>
</footer>
<div class="fixed-btn">
    <a class="back-to-top" href="#" title="返回顶部"><i class="fa fa-chevron-up"></i></a>
	<?php if(!ismobile()):?>
	<a class="page-qrcode"><i class="fa fa-qrcode"></i>
		<div id="qrcode-img">
			<p>扫一扫手机读</p>
		</div>
	</a>
	<?php endif; ?>
     <?php if($this->is('post')): ?>
    <a class="go-comments" href="#comments" title="评论"><i class="fa fa-comments"></i></a>
    <?php endif; ?>
</div>
<?php $this->footer(); ?>
<script src="<?php getSelfUrl('js/common.js'); ?>"></script>
<?php if ($this->is('post')) :?>
<script src="<?php getSelfUrl('js/highlight.min.js'); ?>"></script>
<script>
$(function(){
	$(window).load(function(){
	     $('pre code').each(function(i, block) {
			hljs.highlightBlock(block);
		  });
	});
})
</script>
<?php endif;?>
<script>
window.isArchive = <?php if($this->is('index') || $this->is('archive')){echo 'true';}else{echo 'false';}?>;
<?php if(!ismobile()):?>
$(function(){
	var qrcode = new QRCode(document.getElementById("qrcode-img"), {
        width : 96,//设置宽高
        height : 96
    });
	qrcode.makeCode("<?php echo $this->request->getRequestUrl();?>");
});
<?php endif; ?>
</script>
</body>
</html>