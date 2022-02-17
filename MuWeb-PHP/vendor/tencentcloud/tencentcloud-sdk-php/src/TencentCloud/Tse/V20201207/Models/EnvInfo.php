<?php
/*
 * Copyright (c) 2017-2018 THL A29 Limited, a Tencent company. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace TencentCloud\Tse\V20201207\Models;
use TencentCloud\Common\AbstractModel;

/**
 * 环境具体信息
 *
 * @method string getEnvName() 获取环境名称
 * @method void setEnvName(string $EnvName) 设置环境名称
 * @method array getVpcInfos() 获取环境对应的网络信息
 * @method void setVpcInfos(array $VpcInfos) 设置环境对应的网络信息
 * @method integer getStorageCapacity() 获取云硬盘容量
 * @method void setStorageCapacity(integer $StorageCapacity) 设置云硬盘容量
 * @method string getStatus() 获取运行状态
 * @method void setStatus(string $Status) 设置运行状态
 * @method string getAdminServiceIp() 获取Admin service 访问地址
 * @method void setAdminServiceIp(string $AdminServiceIp) 设置Admin service 访问地址
 * @method string getConfigServiceIp() 获取Config service访问地址
 * @method void setConfigServiceIp(string $ConfigServiceIp) 设置Config service访问地址
 */
class EnvInfo extends AbstractModel
{
    /**
     * @var string 环境名称
     */
    public $EnvName;

    /**
     * @var array 环境对应的网络信息
     */
    public $VpcInfos;

    /**
     * @var integer 云硬盘容量
     */
    public $StorageCapacity;

    /**
     * @var string 运行状态
     */
    public $Status;

    /**
     * @var string Admin service 访问地址
     */
    public $AdminServiceIp;

    /**
     * @var string Config service访问地址
     */
    public $ConfigServiceIp;

    /**
     * @param string $EnvName 环境名称
     * @param array $VpcInfos 环境对应的网络信息
     * @param integer $StorageCapacity 云硬盘容量
     * @param string $Status 运行状态
     * @param string $AdminServiceIp Admin service 访问地址
     * @param string $ConfigServiceIp Config service访问地址
     */
    function __construct()
    {

    }

    /**
     * For internal only. DO NOT USE IT.
     */
    public function deserialize($param)
    {
        if ($param === null) {
            return;
        }
        if (array_key_exists("EnvName",$param) and $param["EnvName"] !== null) {
            $this->EnvName = $param["EnvName"];
        }

        if (array_key_exists("VpcInfos",$param) and $param["VpcInfos"] !== null) {
            $this->VpcInfos = [];
            foreach ($param["VpcInfos"] as $key => $value){
                $obj = new VpcInfo();
                $obj->deserialize($value);
                array_push($this->VpcInfos, $obj);
            }
        }

        if (array_key_exists("StorageCapacity",$param) and $param["StorageCapacity"] !== null) {
            $this->StorageCapacity = $param["StorageCapacity"];
        }

        if (array_key_exists("Status",$param) and $param["Status"] !== null) {
            $this->Status = $param["Status"];
        }

        if (array_key_exists("AdminServiceIp",$param) and $param["AdminServiceIp"] !== null) {
            $this->AdminServiceIp = $param["AdminServiceIp"];
        }

        if (array_key_exists("ConfigServiceIp",$param) and $param["ConfigServiceIp"] !== null) {
            $this->ConfigServiceIp = $param["ConfigServiceIp"];
        }
    }
}
