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
namespace TencentCloud\Dlc\V20210125\Models;
use TencentCloud\Common\AbstractModel;

/**
 * CreateUser请求参数结构体
 *
 * @method string getUserId() 获取用户Id，当前主账号的子账号Uin，和CAM侧匹配
 * @method void setUserId(string $UserId) 设置用户Id，当前主账号的子账号Uin，和CAM侧匹配
 * @method string getUserDescription() 获取用户描述
 * @method void setUserDescription(string $UserDescription) 设置用户描述
 * @method array getPolicySet() 获取绑定到用户的权限集合
 * @method void setPolicySet(array $PolicySet) 设置绑定到用户的权限集合
 */
class CreateUserRequest extends AbstractModel
{
    /**
     * @var string 用户Id，当前主账号的子账号Uin，和CAM侧匹配
     */
    public $UserId;

    /**
     * @var string 用户描述
     */
    public $UserDescription;

    /**
     * @var array 绑定到用户的权限集合
     */
    public $PolicySet;

    /**
     * @param string $UserId 用户Id，当前主账号的子账号Uin，和CAM侧匹配
     * @param string $UserDescription 用户描述
     * @param array $PolicySet 绑定到用户的权限集合
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
        if (array_key_exists("UserId",$param) and $param["UserId"] !== null) {
            $this->UserId = $param["UserId"];
        }

        if (array_key_exists("UserDescription",$param) and $param["UserDescription"] !== null) {
            $this->UserDescription = $param["UserDescription"];
        }

        if (array_key_exists("PolicySet",$param) and $param["PolicySet"] !== null) {
            $this->PolicySet = [];
            foreach ($param["PolicySet"] as $key => $value){
                $obj = new Policy();
                $obj->deserialize($value);
                array_push($this->PolicySet, $obj);
            }
        }
    }
}
