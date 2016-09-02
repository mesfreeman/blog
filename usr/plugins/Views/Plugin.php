<?php
/**
 * Typecho 版 Views - 统计访问次数、点赞数
 *
 * @package Views
 * @author  Double
 * @version 1.0.1
 * @link    http://blog.hequanxi.com
 */
class Views_Plugin implements Typecho_Plugin_Interface
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
        Typecho_Plugin::factory('Widget_Archive')->beforeRender = array('Views_Plugin', 'viewsCounter');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('Views_Plugin', 'loadLikeJs');
        Helper::addAction('views', 'Views_Action');

        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();

        // contents 表中若无 views 字段则添加
        if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents'))))
            $db->query('ALTER TABLE `'. $prefix .'contents` ADD `views` INT(10) DEFAULT 0;');

        // contents 表中若无 likesNum 字段则添加
        if (!array_key_exists('likesNum', $db->fetchRow($db->select()->from('table.contents'))))
            $db->query('ALTER TABLE `'. $prefix .'contents` ADD `likesNum` INT(10) DEFAULT 0;');
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        Helper::removeAction('views');
    }

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
     * 加入 beforeRender
     *
     * @access public
     * @return void
     */
    public static function viewsCounter()
    {
        // 访问计数
        if (Typecho_Widget::widget('Widget_Archive')->is('single')) {
            $db = Typecho_Db::get();
            $cid = Typecho_Widget::widget('Widget_Archive')->cid;
            $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views']+1))->where('cid = ?', $cid));
        }
    }

    /**
     * 输出访问次数
     *
     * 语法: Views_Plugin::theViews();
     * 输出: '访问: xx,xxx 次'
     *
     * 语法: Views_Plugin::theViews('有 ', ' 次点击');
     * 输出: '有 xx,xxx 次点击'
     *
     * @access public
     * @param string  $before 前字串
     * @param string  $after  后字串
     * @param bool    $echo   是否显示 (0 用于运算，不显示)
     * @return string
     */
    public static function theViews($before = '访问: ', $after = ' 次', $echo = 1)
    {
        $db = Typecho_Db::get();
        $cid = Typecho_Widget::widget('Widget_Archive')->cid;
        $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
        if ($echo)
            echo $before, number_format($row['views']), $after;
        else
            return $row['views'];
    }

    /**
     * 加入 footer
     *
     * @access public
     * @return void
     */
    public static function loadLikeJs()
    {
        require_once 'likes-js.php';
    }

    /**
     * 输出点赞次数
     *
     * 语法: Views_Plugin::theLikes();
     * 输出: '赞:xxx次'
     *
     * 语法: Views_Plugin::theLikes('赞:(', ')次');
     * 输出: '赞:(xxx)次'
     *
     * @access public
     * @param string  $before 前字串
     * @param string  $after  后字串
     * @param bool    $echo   是否显示 (0 用于运算，不显示)
     * @return string
     */
    public static function theLikes($before = '赞:', $after = '次', $echo = 1)
    {
        $db = Typecho_Db::get();
        $cid = Typecho_Widget::widget('Widget_Archive')->cid;
        $row = $db->fetchRow($db->select('likesNum')->from('table.contents')->where('cid = ?', $cid));
        if ($echo)
            echo $before, number_format($row['likesNum']), $after;
        else
            return $row['likesNum'];
    }

}
