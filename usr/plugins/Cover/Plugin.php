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
        if ($currentTime - $time > 24 * 60 * 60) { // 如果时间超过24个小时，重新抓取且替换掉原来的图片地址，因为必应图片是24小时换一张
            // 获取最新背景图
            $dataArr = json_decode(file_get_contents('http://super.hequanxi.com/tools.php/BingPic/getNewPic'), true);
            $bgPhotoUlr = $dataArr['picUrl'];
            $optionArr['bgPhoto'] = "{$bgPhotoUlr},{$currentTime}";

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
     * @return void
     */
    public static function theCover()
    {
        $db = Typecho_Db::get();
        $row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'theme:jianshu-master'));
        $optionArr = unserialize($row['value']);
        $bgPhotoArr = explode(',', $optionArr['bgPhoto']);
        if (empty($bgPhotoArr)) {
            // 如果没有设置背景图，返回一个默认图
            echo 'http://img-download.pchome.net/download/1k0/js/4a/o60yqf-1u7e.jpg';
        }

        echo $bgPhotoArr[0];
    }
}
