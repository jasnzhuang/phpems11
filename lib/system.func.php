<?php
namespace PHPEMS;
session_start();
require PEPATH."/lib/init.cls.php";
function M($G,$app = NULL,$param = 'default'){
    if($G == 'db')$G = 'pepdo';
    return ginkgo::make($G,$app,$param);
}
function R($message){
	ginkgo::R($message);
}
ginkgo::loadMoudle();