<?php

/**
 * Cover - 每天获取必应背景图做为封面
 *
 * @package Cover
 * @author  Double
 * @version 1.0.0
 * @link    http://blog.hequanxi.com
 */
class Cover_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array('Cover_Plugin', 'upCover');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){}

    /**
     * 获取插件配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form){}

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    /**
     * 加入 upCover
     *
     * @access public
     * @return void
     */
    public static function upCover()
    {
        $db = Typecho_Db::get();
        $row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'theme:jianshu-master'));
        $optionArr = unserialize($row['value']);
        $bgPhotoArr = explode(',', $optionArr['bgPhoto']);

        // 处理数据结构有更改的情况
        $time = preg_match('/^\d{10}$/', end($bgPhotoArr)) ? end($bgPhotoArr) : 0;
        $currentTime = time();
        if ($currentTime - $time > 12 * 60 * 60) {        // 如果时间超过12个小时,重新抓取
            $imgUrl = self::getImgUrl();                  // 抓取必应背景图
            $time == 0 ? array_pop($bgPhotoArr) : '';     // 将时间戳出栈
            $bgPhotoStr = implode(',', $bgPhotoArr);      // 重新拼成字符串
            $bgPhotoStr .= empty($bgPhotoArr) ? '' : ','; // 空值处理，附上前面出现逗号
            $bgPhotoStr .= "$imgUrl,{$currentTime}";      // 加入新图片
            $optionArr['bgPhoto'] = $bgPhotoStr;          // 替换原来的value

            // 序列化处理
            $srzOption = serialize($optionArr);
            // 存入数据库
            $db->query($db->update('table.options')->rows(array('value' => $srzOption))->where('name = ?', 'theme:jianshu-master'));
        }
    }

    /**
     * 输出背景图url
     *
     * @access public
     * @param string $mode 背景图选择模式:order（顺序）或random（随机）
     */
    public static function theCover($mode = 'order')
    {
        $db = Typecho_Db::get();
        $row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'theme:jianshu-master'));
        $optionArr = unserialize($row['value']);
        $bgPhotoArr = explode(',', $optionArr['bgPhoto']);
        if (empty($bgPhotoArr)) {
            // 如果没有设置背景图，返回一个默认图
            echo 'http://img-download.pchome.net/download/1k0/js/4a/o60yqf-1u7e.jpg';return;
        }

        if ($mode === 'order') {
            echo $bgPhotoArr[count($bgPhotoArr) -2];return;
        } else {
            echo $bgPhotoArr[mt_rand(0, count($bgPhotoArr) -2)];return;
        }
    }


    /**
     * 抓取必应背景图
     *
     * @access public
     * @return string
     */
    protected static function getImgUrl()
    {
        $url = 'http://cn.bing.com/HPImageArchive.aspx?format=js&n=1&pid=hp&video=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $jsonData = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($jsonData, true);
        $imgArray = $data['images'][0];
        $imgUrl = $imgArray['url'];

        // 处理为竖屏图片
//         $imgUrl = str_replace('1920x1080', '1080x1920', $imgUrl);

        return $imgUrl;
    }
}