<?php
namespace library;
/**
 * 上传类
 */
class Upload {
    protected $config = [
      'maxSize'    => 1024*1024*10, // 上传文件的最大值，默认10M
      'allowExts'  => ["jpg","jpeg","gif","png"], //允许的文件后缀
      'savePath'   => '',   //上传文件保存路径
    ];
    public $uploadFileInfo; //上传成功的文件信息
    public $errorMsg; //错误信息

    public function __construct($config) {
      if (is_array($config)) {
        $this->config = array_merge($this->config, $config);
      }
    }

    /**
     * 上传方法
     * @param type $key
     * @return boolean
     */
    public function upload($key) {

		if(!$key){
      $this->errorMsg = '找不到上传字段';
      return false;
		}
    if (empty($_FILES)) {
        $this->errorMsg = '没有文件上传！';
        return false;
    }
    if($_FILES[$key]['error'] > 0){
      $this->errorMsg = $this->get_error($_FILES[$key]['error']);
      return false;
    }

		$file = $_FILES[$key];
		$file['extension'] = $this->get_filetype($file['name']); // 获得文件后缀名

    //检查文件类型大小和合法性
    if(!$this->check_size($file)){
      return false;
    }

		//上传成功
    $date_folder = date("ymd");
		$this->config["savePath"] = $this->config["savePath"] . $date_folder;
		$file_name = date("His") . rand(10000, 99999) . '.' . $file['extension'];

		$ret = $this->localSave($file['tmp_name'], $this->config["savePath"]. '/' . $file_name);
		if($ret) {
      $this->uploadFileInfo = $date_folder . '/' . $file_name;
      return true;
		}else{
			return false;
		}
  }


    /**
     * 本地存储
     * @param type $srcFileName
     * @param type $destFileName
     * @return boolean
     */
    public function localSave($srcFileName, $destFileName) {
        $dir = dirname($destFileName);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                $this->errorMsg = '上传目录' . $dir . '不存在';
                return false;
            }
        } else {
            if (!is_writeable($dir)) {
                $this->errorMsg = '上传目录' . $dir . '不可写';
                return false;
            }
        }

        if(move_uploaded_file($srcFileName, $destFileName)) {
            return true;
        }else{
          $this->errorMsg = '文件上传保存错误！'.$srcFileName . '目标：'.$destFileName;
          return false;
        }

    }

	/**
     *  返回文件后缀名，如‘.php’
     *
     * @access  public
     * @param
     *
     * @return  string      文件后缀名
     */
    public function get_filetype($path)
    {
        $pos = strrpos($path, '.');
        if ($pos !== false)
        {
            return str_replace('.','',substr($path, $pos));
        }
        else
        {
            return '';
        }
    }
	   /**
     * 检查文件类型大小和合法性
     * @param type $file
     * @return boolean
     */
    public function check_size($file) {
        //文件上传失败
        if ($file['error'] !== 0) {
            $this->errorMsg = '文件上传失败！';
            return false;
        }
		    //检查是否合法上传
        if (!is_uploaded_file($file['tmp_name'])) {
            $this->errorMsg = '非法上传文件！';
            return false;
        }
        //检查文件类型
        if (!in_array($file['extension'], $this->config["allowExts"])) {
            $this->errorMsg = '上传文件类型不允许！';
            return false;
        }
        //检查文件大小
        if ($file['size'] > $this->config["maxSize"]) {
            $this->errorMsg = '上传文件大小超出限制！';
            return false;
        }

        //检查通过，返回true
        return true;
    }

    private function get_error($code){
      /*
      0： 上传成功
      1： 上传文件超出php配置max_upload_filesize限制
      2： 上传文件超出html表单限制
      3： 文件只有部分被上传 
      4： 没有上传文件
      6： 没有找不到临时文件夹 
      7： 文件写入失败（可能是文件权限不足）
      8： php文件上传扩展file没有打开 
      */
      switch ($code) {
        case 1:
          $msg = '上传文件超出php配置限制';
          break;
        case 2:
          $msg = '上传文件超出html表单限制';
          break;
        case 3:
          $msg = '文件只有部分被上传 ';
          break;
        case 4:
          $msg = '没有上传文件';
          break;
        case 6:
          $msg = '没有找不到临时文件夹 ';
          break;
        case 7:
          $msg = '文件写入失败（可能是文件权限不足）';
          break;
        case 8:
          $msg = 'php文件上传扩展file没有打开 ';
          break;
        default:
          // code...
          break;
      }
      return $msg;
    }

    /**
     * 上传成功获取返回信息
     * @return type
     */
    public function getUploadFileInfo() {
        return $this->uploadFileInfo;
    }

    /**
     * 获取错误信息
     * @return type
     */
    public function getErrorMsg() {
        return $this->errorMsg;
    }

}

?>
