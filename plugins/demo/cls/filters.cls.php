<?php

namespace PHPEMS\plugins\demo;
use function \PHPEMS\M;
use function \PHPEMS\P;
class filters
{
    /**
     * @return void
     * 此方法必须存在
     */
    public function register()
    {
        M('plugin')->registerFilter('afterGetArticle',array($this,'afterGetArticle'),10);
        M('plugin')->registerFilter('afterLoadPcCss',array($this,'afterLoadPcCss'),10);
    }

    public function afterGetArticle($content)
    {
        $plugin = M('plugin')->getPluginByName('demo');
        $number = intval($plugin['pluginsetting']['viewnumber']);
        if($number)
        {
            $content['contentview'] = $content['contentview'] + $number;
        }
        return $content;
    }

    public function afterLoadPcCss($css)
    {
        return $css;
    }
}