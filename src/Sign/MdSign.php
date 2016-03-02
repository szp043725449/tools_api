<?php 

namespace Tools\Api\Sign;

use Illuminate\Support\Arr;

class MdSign {

    const SORT = 'sort';

    const RSORT = 'rsort';

    const NO_SORT = 'no';

    private $_sortType;

    private $_data;

    public function __construct($data, $sortType = self::SORT)
    {
        $this->setSortType($sortType);
        $this->setData($data);
    }

    public function setSortType($sortType)
    {
        $this->_sortType = $sortType;
    }

    public function setData($data)
    {
        if (is_array($data)) {
            $this->_data = $this->paraFilter($data);
            return true;
        }
        $jsonArray = json_decode($data);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->_data = $this->paraFilter($jsonArray);
            return true;
        }

        return false;
    }

    public function getSign($suffix="")
    {
        if (is_array($this->_data) && count($this->_data)>0) {
            if ($this->_sortType == self::SORT) {
                $this->_data = Arr::sortRecursive($this->_data);
            }
            return md5($this->getSignString().$suffix);
        }

        return false;
    }

    private function getSignString()
    {
        $arg  = "";
        foreach($this->_data as $key => $val)
        {
            $arg.=$key."=".$val."&";
        }

        //去掉最后一个&字符
        $arg = trim($arg,'&');

        //如果存在转义字符，那么去掉转义
        if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc())
        {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    private function paraFilter($para)
    {
        $para_filter = array();
        foreach($para as $key => $val)
        {
            if($val != "")
            {
                $para_filter[$key] = $para[$key];
            }
        }

        return $para_filter;
    }
}