<?php

namespace PHPEMS;

class style
{
    static $pcdefaultcss = array(
        array('css','files/public/css/datetimepicker.css'),
        array('css','files/public/css/bootstrap.css'),
        array('css','files/public/css/font-awesome.css'),
        array('less','files/public/css/pe.less'),
        array('css','files/public/css/swiper.min.css'),
        array('css','files/public/js/videojs/video-js.css'),
    );
    static $pcdefaultjs = array(
        array('js','files/public/js/less.min.js'),
        array('js','files/public/js/jquery.min.js'),
        array('js','files/public/js/swiper.min.js'),
        array('js','files/public/js/bootstrap.min.js'),
        array('js','files/public/js/bootstrap-datetimepicker.js'),
        array('js','files/public/js/all.fine-uploader.min.js'),
        array('js','files/public/js/ckeditor/ckeditor.js'),
        array('js','files/public/js/pe.app.js'),
        array('js','files/public/js/videojs/video.min.js'),
        array('js','files/public/js/exam.app.js')
    );

    static $mobiledefaultcss = array(
        array('css','files/public/css/font-awesome.css'),
        array('less','files/public/css/pe.mobile.less'),
        array('css','files/public/css/animations.css'),
        array('css','files/public/css/swiper.min.css'),
        array('css','files/public/js/videojs/video-js.css'),
    );
    static $mobiledefaultjs = array(
        array('js','files/public/js/less.min.js'),
        array('js','files/public/js/jquery.min.js'),
        array('js','files/public/js/videojs/video.min.js'),
        array('js','files/public/js/swiper.min.js'),
        array('js','files/public/js/sonic.js'),
        array('js','files/public/js/all.fine-uploader.min.js'),
        array('js','files/public/js/pe.mobile.js'),
        array('js','files/public/js/exam.mobile.js')
    );
    static function loadCss()
    {
        if(M('ev')->isMobile())
        {
            $css = self::$mobiledefaultcss;
            $css = M('plugin')->filter('afterLoadMobileCss',$css);
        }
        else
        {
            $css = self::$pcdefaultcss;
            $css = M('plugin')->filter('afterLoadPcCss', $css);
        }
        $cssHTML = [];
        foreach($css as $file)
        {
            switch($file[0])
            {
                case 'less':
                    $file[0] = 'stylesheet/less';
                    break;

                default:
                    $file[0] = 'stylesheet';
                    break;
            }
            $cssHTML[] = "<link rel=\"{$file[0]}\" type=\"text/css\" href=\"{$file[1]}\" />";
        }
        return $cssHTML;
    }

    static function loadJs()
    {
        if(M('ev')->isMobile())
        {
            $js = self::$mobiledefaultjs;
            $js = M('plugin')->filter('afterLoadMobileJs',$js);
        }
        else
        {
            $js = self::$pcdefaultjs;
            $js = M('plugin')->filter('afterLoadPcJs',$js);
        }
        $jsHTML = [];
        foreach($js as $file)
        {
            switch($file[0])
            {
                case 'module':
                    $file[0] = 'module';
                    break;

                case 'json':
                    $file[0] = 'application/json';
                    break;

                default:
                    $file[0] = 'text/javascript';
                    break;
            }
            $jsHTML[] = "<script type=\"{$file[0]}\" src=\"{$file[1]}\"></script>";
        }
        return $jsHTML;
    }

    static function loadStyle()
    {
        $css = self::loadCss();
        $js = self::loadJs();
        return implode("\n\t",array_merge($css,$js));
    }
}