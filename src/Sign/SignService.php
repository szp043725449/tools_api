<?php 

namespace Tools\Api\Sign;

use Tools\Api\Sign\SignMode;
use Tools\Api\Sign\MdSign;
use \Exception;

class SignService {

    private $_model = self::MD5;

    private $_class = null;

    /**
     * [setMode description]
     * @param [type] $model [description]
     */
    public function setMode($model)
    {
        if ($mode == SignMode::MD5) {
            $this->_class = new MdSign();
        }
    }

    public function getClass()
    {
        if (is_null($this->_class)) {
            throw new Exception("签名类没找到, 请使用setMode方法", 1);
        }

        return $this->_class;
    }
}