<?php
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
namespace PHPEMS;
class action extends app
{
	public function display()
	{
		$action = M('ev')->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}

	public function index()
	{
		$args = array();
		$path = 'files/attach/images/content/'.date('Ymd').'/';
		$upfile = M('ev')->getFile('qqfile');
		$args['attext'] = M('files')->getFileExtName($upfile['name']);
		if(!in_array(strtolower($args['attext']),$this->allowexts) || in_array(strtolower($args['attext']),M('config','document')->forbidden))
		{
			$message = array(
				'statusCode' => 300,
				'status' => 'fail',
				'message' => '上传失败，附件类型不符!'
			);
			R($message);
		}
		if($upfile)
		{
			$upfile = M('plugin')->filter('beforeUpload',$upfile);
			if($upfile['error'])
			{
				$errormessage = $upfile['errormessage']?$upfile['errormessage']:'附件上传失败!';
				$message = array(
					'statusCode' => 300,
					'message' => $errormessage,
					'status' => 'fail'
				);
				R($message);
			}
			$fileurl = M('files')->uploadFile($upfile,$path,NULL,NULL,$this->allowexts);
		}
		if($fileurl)
		{
			$info = array(
				'title' => $upfile['name'],
				'size' => $upfile['size'],
				'path' => $fileurl,
				'type' => $upfile['type']
			);
			$info = M('plugin')->filter('afterUpload',$info);
			$args = array();
			$args['attpath'] = $info['path'];
			$args['atttitle'] = $info['title'];
			$args['attsize'] = $info['size'];
			$args['attuserid'] = $this->user['userid'];
			$args['attcntype'] = $info['type'];
			M('attach','document')->addAttach($args);
			$message = array(
				'statusCode' => 200,
				'message' => '上传成功！',
				'success' => true,
				'thumb' => $info['path'],
				'title' => $info['title']
			);
			R($message);
		}
		else
		{
			$message = array(
				'status' => 'fail',
				'statusCode' => 300,
				'message' => '上传失败！'
			);
			R($message);
		}
	}
}


?>
