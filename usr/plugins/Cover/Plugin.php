<?php

/**
 * Cover - 每天获取必应背景图做为封面
 *
 * @package Cover
 * @author  Double
 * @version 1.0.1
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

        // 获取数据库中后台配置选项
        $row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'theme:jianshu-master'));

        // 将值返序列化为数组
        $optionArray = unserialize($row['value']);
        $coverArray = explode(',', $optionArray['bgPhoto']);

        // 取出时间戳标记
        $lastUpdateTime = (int) end($coverArray);

        // 当取出的最后更新时间为空、不合法且小于当天5点时，则重新获取图片
        if (empty($lastUpdateTime)
            || $lastUpdateTime !== strtotime(date('Y-m-d H:i:s', $lastUpdateTime))
            || $lastUpdateTime - strtotime(date('Y-m-d 05:00:00')) < 0
        ) {
            // 获取失败，直接退出
            if (empty($coverUrl)) {
                die;
            }

            // 替换原背景图数据
            $optionArray['bgPhoto'] = "{$coverUrl}," . time();

            // 序列化处理
            $serializeOption = serialize($optionArray);

            // 更新数据库
            $db->query($db->update('table.options')->rows(array('value' => $serializeOption))->where('name = ?', 'theme:jianshu-master'));
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

        // 获取数据库中后台配置选项
        $row = $db->fetchRow($db->select('value')->from('table.options')->where('name = ?', 'theme:jianshu-master'));

        // 将值返序列化为数组
        $optionArray = unserialize($row['value']);
        $coverArray = explode(',', $optionArray['bgPhoto']);

        // 获取封面图
        $cover = current($coverArray);

        // 如果不是一个合法的图片地址时，返回一个默认封面
        if (strpos($cover, 'https://') === false) {
            echo '/usr/themes/jianshu-master/img/defaultBg.jpg';
        } else {
            echo $cover;
        }
    }

    /**
     * 获取必应每日背景图
     *
     * @return string
     */
    protected static function captureBingImg()
    {
        // 获取图片的数量
        $num = 1;

        // 随机数，13位数字
        $nc = time() . mt_rand(100, 999);

        // 图片请求接口地址
        $apiUrl = "https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n={$num}&nc={$nc}&pid=hp";

        // 获取该返回数据
        $dataJson = file_get_contents($apiUrl);
        $dataArray = json_decode($dataJson, true);

        if (empty($dataArray) || ! isset($dataArray['images'][0]['url'])) {
            return '';
        }

        // 获取图片地址，如：/az/hprichbg/rb/TulipsEquinox_ZH-CN11213785857_1920x1080.jpg
        $imgUrl = $dataArray['images'][0]['url'];

        // 将电脑尺寸转换为手机尺寸
        $imgUrl = 'https://cn.bing.com' . str_replace('1920x1080', '1080x1920', $imgUrl);

        // 返回图片完整地址
        return $imgUrl;
    }
}
