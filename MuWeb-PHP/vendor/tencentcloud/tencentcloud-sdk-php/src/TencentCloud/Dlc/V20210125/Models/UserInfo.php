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
 * 授权用户信息
 *
 * @method string getUserId() 获取用户Id，和CAM侧Uin匹配
 * @method void setUserId(string $UserId) 设置用户Id，和CAM侧Uin匹配
 * @method string getUserDescription() 获取用户描述
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setUserDescription(string $UserDescription) 设置用户描述
注意：此字段可能返回 null，表示取不到有效值。
 * @method array getPolicySet() 获取单独给用户绑定的权限集合
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setPolicySet(array $PolicySet) 设置单独给用户绑定的权限集合
注意：此字段可能返回 null，表示取不到有效值。
 * @method string getCreator() 获取创建者
 * @method void setCreator(string $Creator) 设置创建者
 * @method string getCreateTime() 获取创建时间
 * @method void setCreateTime(string $CreateTime) 设置创建时间
 * @method array getWorkGroupSet() 获取关联的工作组集合
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setWorkGroupSet(array $WorkGroupSet) 设置关联的工作组集合
注意：此字段可能返回 null，表示取不到有效值。
 * @method boolean getIsOwner() 获取是否是管理员账号
注意：此字段可能返回 null，表示取不到有效值。
 * @method void setIsOwner(boolean $IsOwner) 设置是否是管理员账号
注意：此字段可能返回 null，表示取不到有效值。
 */
class UserInfo extends AbstractModel
{
    /**
     * @var string 用户Id，和CAM侧Uin匹配
     */
    public $UserId;

    /**
     * @var string 用户描述
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $UserDescription;

    /**
     * @var array 单独给用户绑定的权限集合
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $PolicySet;

    /**
     * @var string 创建者
     */
    public $Creator;

    /**
     * @var string 创建时间
     */
    public $CreateTime;

    /**
     * @var array 关联的工作组集合
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $WorkGroupSet;

    /**
     * @var boolean 是否是管理员账号
注意：此字段可能返回 null，表示取不到有效值。
     */
    public $IsOwner;

    /**
     * @param string $UserId 用户Id，和CAM侧Uin匹配
     * @param string $UserDescription 用户描述
注意：此字段可能返回 null，表示取不到有效值。
     * @param array $PolicySet 单独给用户绑定的权限集合
注意：此字段可能返回 null，表示取不到有效值。
     * @param string $Creator 创建者
     * @param string $CreateTime 创建时间
     * @param array $WorkGroupSet 关联的工作组集合
注意：此字段可能返回 null，表示取不到有效值。
     * @param boolean $IsOwner 是否是管理员账号
注意：此字段可能返回 null，表示取不到有效值。
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

        if (array_key_exists("Creator",$param) and $param["Creator"] !== null) {
            $this->Creator = $param["Creator"];
        }

        if (array_key_exists("CreateTime",$param) and $param["CreateTime"] !== null) {
            $this->CreateTime = $param["CreateTime"];
        }

        if (array_key_exists("WorkGroupSet",$param) and $param["WorkGroupSet"] !== null) {
            $this->WorkGroupSet = [];
            foreach ($param["WorkGroupSet"] as $key => $value){
                $obj = new WorkGroupMessage();
                $obj->deserialize($value);
                array_push($this->WorkGroupSet, $obj);
            }
        }

        if (array_key_exists("IsOwner",$param) and $param["IsOwner"] !== null) {
            $this->IsOwner = $param["IsOwner"];
        }
    }
}
