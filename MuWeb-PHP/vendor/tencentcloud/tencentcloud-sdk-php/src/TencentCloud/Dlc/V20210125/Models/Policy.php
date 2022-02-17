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
 * 权限对象
 *
 * @method string getCatalog() 获取需要授权的数据源名称，*代表拥有全部数据源权限
 * @method void setCatalog(string $Catalog) 设置需要授权的数据源名称，*代表拥有全部数据源权限
 * @method string getDatabase() 获取需要授权的数据库名称，*代表拥有全部数据库名称
 * @method void setDatabase(string $Database) 设置需要授权的数据库名称，*代表拥有全部数据库名称
 * @method string getTable() 获取需要授权的表名称，*代表拥有全部表权限
 * @method void setTable(string $Table) 设置需要授权的表名称，*代表拥有全部表权限
 * @method string getOperation() 获取授权的操作，当前只支持“ALL”
 * @method void setOperation(string $Operation) 设置授权的操作，当前只支持“ALL”
 */
class Policy extends AbstractModel
{
    /**
     * @var string 需要授权的数据源名称，*代表拥有全部数据源权限
     */
    public $Catalog;

    /**
     * @var string 需要授权的数据库名称，*代表拥有全部数据库名称
     */
    public $Database;

    /**
     * @var string 需要授权的表名称，*代表拥有全部表权限
     */
    public $Table;

    /**
     * @var string 授权的操作，当前只支持“ALL”
     */
    public $Operation;

    /**
     * @param string $Catalog 需要授权的数据源名称，*代表拥有全部数据源权限
     * @param string $Database 需要授权的数据库名称，*代表拥有全部数据库名称
     * @param string $Table 需要授权的表名称，*代表拥有全部表权限
     * @param string $Operation 授权的操作，当前只支持“ALL”
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
        if (array_key_exists("Catalog",$param) and $param["Catalog"] !== null) {
            $this->Catalog = $param["Catalog"];
        }

        if (array_key_exists("Database",$param) and $param["Database"] !== null) {
            $this->Database = $param["Database"];
        }

        if (array_key_exists("Table",$param) and $param["Table"] !== null) {
            $this->Table = $param["Table"];
        }

        if (array_key_exists("Operation",$param) and $param["Operation"] !== null) {
            $this->Operation = $param["Operation"];
        }
    }
}
