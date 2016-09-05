<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<div class="main-container">
    <article class="post preview" itemscope itemtype="http://schema.org/BlogPosting">
		<div class="post-author clearfix">
			<a class="fl" href="<?php $this->author->permalink(); ?>" title="<?php $this->author(); ?>">
			<?php if($this->options->avatarUrl):?>
    		  <img class="avatar" width="32" src="<?php $this->options->avatarUrl();?>" alt="" />
            <?php else: ?>
              <img class="avatar" width="32" src="<?php $this->options->themeUrl('img/touxiang.jpg'); ?>" alt="头像"/>
    	    <?php endif;?>
			</a>
			<a href="<?php $this->author->permalink(); ?>"><?php $this->author(); ?></a>
			<span title="<?php _e('最后编辑于');echo date('Y-m-d H:i:s',$this->modified); ?>"><?php $this->date('Y-m-d H:i:s'); ?></span>
		</div>
        <h1 class="post-title" itemprop="name headline"><?php $this->title() ?></h1>
        <ul class="post-meta clearfix">
            <li><?php _e('阅读');Views_Plugin::theViews(':(',')'); ?></li>
            <li><?php $this->commentsNum('评论:(%d)'); ?></li>
			<li><?php _e('赞');Views_Plugin::theLikes(':(',')'); ?></li>
        </ul>
        <div class="post-content" itemprop="articleBody">
            <?php parseContent($this); ?>
        </div>
		<div class="post-tool">
			<span class="post-like"><a class="btn s3 btn-like" data-cid="<?php $this->cid();?>" data-num="<?php $this->likesNum();?>" href="javascript:void(0)"><i class="fa fa-thumbs-up"></i> <span id="zanId"><?php _e('赞'); ?></span> <span class="post-likes-num"><?php Views_Plugin::theLikes('','');?></span></a></span>
			<span class="post-share"><a class="btn s3 btn-dialog" data-dialog="#dialog-share" href="#"><i class="fa fa-share-alt"></i> <?php _e('分享'); ?></a></span>
			<span class="post-donate">
				<a href="#" class="btn s3 btn-dialog" data-dialog="#dialog-donate"><?php _e('赏'); ?></a>
			</span>
			<div class="dialog" id="dialog-donate">
			    <?php if(!ismobile()):?>
			      <h4><?php _e('支付宝扫码赞助本站'); ?></h4>
			      <img src="<?php $this->options->themeUrl('img/alipay.png')?>" alt="支付宝扫码赞助" width="200" />
			    <?php else: ?>
			      <h4><?php _e('长按识别二维码赞助本站'); ?></h4>
			      <img src="<?php $this->options->themeUrl('img/wechat.png')?>" alt="支付宝扫码赞助" width="200" />
			    <?php endif;?>
			</div>
			<div class="dialog" id="dialog-share">
				<div class="bdsharebuttonbox" data-tag="share_1">
					<a class="bds_tsina fa fa-weibo" data-cmd="tsina"></a>
					<a class="bds_weixin fa fa-weixin" data-cmd="weixin"></a>
					<a class="bds_mail fa fa-envelope" data-cmd="mail"></a>
					<a class="bds_more fa fa-plus" data-cmd="more"></a>
				</div>
				<script>
					window._bd_share_config = {
						common:{
							bdText:'<?php $this->title();?>',
							bdDesc:'<?php $this->description();?>',
							bdUrl:'<?php $this->permalink();?>',
							bdPic:''
						},
						share : [{
							bdCustomStyle:'<?php $this->options->themeUrl('css/bdshare.css');?>'
						}],
					}
					with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?cdnversion='+~(-new Date()/36e5)];
				</script>
			</div>
		</div>

		<div class="post-foot clearfix">
			<div class="post-tags"><?php _e('标签: '); ?><?php $this->tags(', ', true, 'none'); ?></div>
		</div>
    </article>

    <ul class="post-near">
        <li>上一篇: <?php $this->thePrev('%s','没有了'); ?></li>
        <li>下一篇: <?php $this->theNext('%s','没有了'); ?></li>
    </ul>
    <?php $this->need('comments.php'); ?>
</div>
<?php $this->need('footer.php'); ?>
