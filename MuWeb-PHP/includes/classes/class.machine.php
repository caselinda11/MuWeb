<?php
/**
 * 获取用户机器码相关函数类
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/


class Machine
{
    var $result   = [];
    var $mac_addr;           //第一个mac地址
    var $mac_addrAll = [];   //所有mac地址
    function __construct($OS){
        $this->GetMac($OS);
    }

    function GetMac($OS){
        switch ( strtolower($OS) ){
            case "unix":
                break;
            case "solaris":
                break;
            case "aix":
                break;
            case "linux":
                $this->getLinux();
                break;
            default:
                $this->getWindows();
                break;
        }
        $tem = array();
        foreach($this->result as $val){
            if(preg_match("/[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f][:-]"."[0-9a-f][0-9a-f]/i",$val,$tem) ){
                $this->mac_addr = $tem[0];
                break; //多个网卡时，会返回第一个网卡的mac地址，一般够用。
//                $this->mac_addrAll[] = $tem[0];//返回所有的mac地址
            }
        }
        unset($temp_array);
        return $this->mac_addr;
    }
    //Linux系统
    function getLinux(){
        @exec("ifconfig -a", $this->result);
        return $this->result;
    }
    //Windows系统
    function getWindows(){
        @exec("ipconfig /all", $this->result);
        if ($this->result) {
            if (is_array($this->result)){
                foreach ($this->result as $key=>$value){
                    $this->result[$key] = iconv('GBK','UTF-8',$value);
                }
            }
            return $this->result;
        } else {
            $ipconfig = $_SERVER["WINDIR"]."\system32\ipconfig.exe";
            if(is_file($ipconfig)) {
                @exec($ipconfig." /all", $this->result);
            } else {
                @exec($_SERVER["WINDIR"]."\system\ipconfig.exe /all", $this->result);
                return $this->result;
            }
        }
    }
}