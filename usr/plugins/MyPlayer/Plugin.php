<?php
/**
 * MyPlayer - 基于虾米音乐、网易云音乐歌曲ID全自动解析的云播放器
 *
 * @package MyPlayer
 * @author MyTypecho
 * @version 1.1
 * @link http://mytypecho.com
 */
class MyPlayer_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
		Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'footer');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
	}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){
		$options = Typecho_Widget::widget('Widget_Options');
		echo '<p style="margin-bottom:14px;font-size:13px;text-align:center;">播放器序列号: '.md5($options->secret).' <span id="playertype"></span></p>';
		echo '<p style="margin-bottom:14px;font-size:13px;text-align:center;">免费版/测试版只支持解析3个曲目！</p>';
// 		echo '<script>document.onready=function(){$.ajax({dataType:"jsonp",jsonp: "callback",url: "http://typecho.wx.jaeapp.com/MyPlayerApi/index.php?action=query&public='.md5($options->secret).'",success: function(data){if(data["result"]) {$("#playertype").addClass("success").html(data["result"]).css("padding","3px")}}});};</script>';

		$edit = new Typecho_Widget_Helper_Form_Element_Textarea('mp_playlist', NULL, '', '音乐曲目URL', _t("一行一个，支持虾米/网易云音乐自动解析\n示例：http://music.163.com/#/album?id=14947"));
        $form->addInput($edit);

	    $edit = new Typecho_Widget_Helper_Form_Element_Select("apisource", array("http://mytypecho.com" => "默认 (http://mytypecho.com)", "http://typecho.wx.jaeapp.com" => "MyTypecho TAE (http://typecho.wx.jaeapp.com)"), "http://typecho.wx.jaeapp.com", "API源");
		$form->addInput($edit);

	    $edit = new Typecho_Widget_Helper_Form_Element_Radio("smload", array("yes" => "加载", "no" => "不加载"), "yes", "载入 SoundManager");
		$form->addInput($edit);

	    $edit = new Typecho_Widget_Helper_Form_Element_Text("themeload", NULL, "", "指定主题加载", "留空所有主题都加载");
		$form->addInput($edit);

	    $edit = new Typecho_Widget_Helper_Form_Element_Text("loadfrom", NULL, "http://typecho.wx.jaeapp.com/MyPlayerApi/static", "静态文件加载地址", "留空从本站加载");
		$form->addInput($edit);
	}

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

	public static function configHandle($settings, $isInit)
	{
		Widget_Plugins_Edit::configPlugin("MyPlayer", $settings);

		//upload API
		if(!$isInit)
		{
			$options = Typecho_Widget::widget('Widget_Options');
			$client = Typecho_Http_Client::get();

			$list = json_encode(@explode("\n", str_replace("\r", "", $settings['mp_playlist'])));
			$apiURL = $settings['apisource'].'/MyPlayerApi/index.php?action=modify&public='.md5($options->secret).'&private='.sha1($options->secret)."&domain=".$options->siteUrl;
			@$client->setHeader('User-Agent', $options->generator)->setTimeout(10)->setData(array("list" => $list))->send($apiURL);

			$result = @json_decode($client->getResponseBody(), true);
			if(!$result) throw new Typecho_Plugin_Exception("保存设置失败: 网络错误");

			if(!isset($result['success']))
			{
				throw new Typecho_Plugin_Exception("保存设置失败: " . $result['error']);
			}
		}
	}

    /* 是否手机访问 */
    public static function isMobile()
    {
	    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	    return preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$userAgent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($userAgent,0,4));
    }

	public static function footer()
	{
		if(!self::isMobile()) {
			$options = Typecho_Widget::widget('Widget_Options');
			$plugin = $options->plugin("MyPlayer");
			if(!$plugin->themeload || strtolower($plugin->themeload) == strtolower($options->theme))
			{
				$loadfrom = empty($plugin->loadfrom) ? $options->pluginUrl.'/MyPlayer' : $plugin->loadfrom;
// 				echo '<script type="text/javascript">/* <![CDATA[ */ var MyPlayerCfg={ajax:"',$plugin->apisource,'/MyPlayerApi/index.php?action=playlist&public=',md5($options->secret),'&domain=',$options->siteUrl,'",flash:"',$loadfrom,'/sm.swf",css:"' . $loadfrom . '/css.css"}; /* ]]> */</script>';
				echo '<script type="text/javascript">/* <![CDATA[ */ var MyPlayerCfg={ajax:"',$plugin->apisource,'/MyPlayerApi/index.php?action=playlist&public=860422a0ddf9da096d11664667f75cc9','&domain=',$options->siteUrl,'",flash:"',$loadfrom,'/sm.swf",css:"' . $loadfrom . '/css.css"}; /* ]]> */</script>';
				if($plugin->smload == "yes") echo '<script type="text/javascript" src="'.$loadfrom.'/sm.js"></script>';
				echo '<script type="text/javascript" src="'.$loadfrom.'/myplayer.js"></script>';
			}
		}
	}
}
