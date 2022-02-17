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
 * DescribeUsers请求参数结构体
 *
 * @method string getUserId() 获取查询的用户Id，和CAM侧Uin匹配
 * @method void setUserId(string $UserId) 设置查询的用户Id，和CAM侧Uin匹配
 * @method integer getOffset() 获取偏移量，默认为0
 * @method void setOffset(integer $Offset) 设置偏移量，默认为0
 * @method integer getLimit() 获取返回数量，默认20，最大值100
 * @method void setLimit(integer $Limit) 设置返回数量，默认20，最大值100
 * @method string getSortBy() 获取排序字段，支持如下字段类型，create-time
 * @method void setSortBy(string $SortBy) 设置排序字段，支持如下字段类型，create-time
 * @method string getSorting() 获取排序方式，desc表示正序，asc表示反序， 默认为asc
 * @method void setSorting(string $Sorting) 设置排序方式，desc表示正序，asc表示反序， 默认为asc
 */
class DescribeUsersRequest extends AbstractModel
{
    /**
     * @var string 查询的用户Id，和CAM侧Uin匹配
     */
    public $UserId;

    /**
     * @var integer 偏移量，默认为0
     */
    public $Offset;

    /**
     * @var integer 返回数量，默认20，最大值100
     */
    public $Limit;

    /**
     * @var string 排序字段，支持如下字段类型，create-time
     */
    public $SortBy;

    /**
     * @var string 排序方式，desc表示正序，asc表示反序， 默认为asc
     */
    public $Sorting;

    /**
     * @param string $UserId 查询的用户Id，和CAM侧Uin匹配
     * @param integer $Offset 偏移量，默认为0
     * @param integer $Limit 返回数量，默认20，最大值100
     * @param string $SortBy 排序字段，支持如下字段类型，create-time
     * @param string $Sorting 排序方式，desc表示正序，asc表示反序， 默认为asc
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

        if (array_key_exists("Offset",$param) and $param["Offset"] !== null) {
            $this->Offset = $param["Offset"];
        }

        if (array_key_exists("Limit",$param) and $param["Limit"] !== null) {
            $this->Limit = $param["Limit"];
        }

        if (array_key_exists("SortBy",$param) and $param["SortBy"] !== null) {
            $this->SortBy = $param["SortBy"];
        }

        if (array_key_exists("Sorting",$param) and $param["Sorting"] !== null) {
            $this->Sorting = $param["Sorting"];
        }
    }
}
