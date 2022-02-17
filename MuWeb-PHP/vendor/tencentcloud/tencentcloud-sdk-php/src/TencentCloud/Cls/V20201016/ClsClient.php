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

namespace TencentCloud\Cls\V20201016;

use TencentCloud\Common\AbstractClient;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Credential;
use TencentCloud\Cls\V20201016\Models as Models;

/**
 * @method Models\ApplyConfigToMachineGroupResponse ApplyConfigToMachineGroup(Models\ApplyConfigToMachineGroupRequest $req) 应用采集配置到指定机器组
 * @method Models\CreateAlarmResponse CreateAlarm(Models\CreateAlarmRequest $req) 本接口用于创建告警策略。
 * @method Models\CreateAlarmNoticeResponse CreateAlarmNotice(Models\CreateAlarmNoticeRequest $req) 该接口用户创建告警通知模板。
 * @method Models\CreateConfigResponse CreateConfig(Models\CreateConfigRequest $req) 创建采集规则配置
 * @method Models\CreateExportResponse CreateExport(Models\CreateExportRequest $req) 本接口用于创建日志导出
 * @method Models\CreateIndexResponse CreateIndex(Models\CreateIndexRequest $req) 本接口用于创建索引
 * @method Models\CreateLogsetResponse CreateLogset(Models\CreateLogsetRequest $req) 本接口用于创建日志集，返回新创建的日志集的 ID。
 * @method Models\CreateMachineGroupResponse CreateMachineGroup(Models\CreateMachineGroupRequest $req) 创建机器组
 * @method Models\CreateShipperResponse CreateShipper(Models\CreateShipperRequest $req) 创建新的投递规则，客户如果使用此接口，需要自行处理CLS对指定bucket的写权限。
 * @method Models\CreateTopicResponse CreateTopic(Models\CreateTopicRequest $req) 本接口用于创建日志主题。
 * @method Models\DeleteAlarmResponse DeleteAlarm(Models\DeleteAlarmRequest $req) 本接口用于删除告警策略。
 * @method Models\DeleteAlarmNoticeResponse DeleteAlarmNotice(Models\DeleteAlarmNoticeRequest $req) 该接口用于删除告警通知模板
 * @method Models\DeleteConfigResponse DeleteConfig(Models\DeleteConfigRequest $req) 删除采集规则配置
 * @method Models\DeleteConfigFromMachineGroupResponse DeleteConfigFromMachineGroup(Models\DeleteConfigFromMachineGroupRequest $req) 删除应用到机器组的采集配置
 * @method Models\DeleteExportResponse DeleteExport(Models\DeleteExportRequest $req) 本接口用于删除日志导出
 * @method Models\DeleteIndexResponse DeleteIndex(Models\DeleteIndexRequest $req) 本接口用于日志主题的索引配置
 * @method Models\DeleteLogsetResponse DeleteLogset(Models\DeleteLogsetRequest $req) 本接口用于删除日志集。
 * @method Models\DeleteMachineGroupResponse DeleteMachineGroup(Models\DeleteMachineGroupRequest $req) 删除机器组
 * @method Models\DeleteShipperResponse DeleteShipper(Models\DeleteShipperRequest $req) 删除投递规则
 * @method Models\DeleteTopicResponse DeleteTopic(Models\DeleteTopicRequest $req) 本接口用于删除日志主题。
 * @method Models\DescribeAlarmNoticesResponse DescribeAlarmNotices(Models\DescribeAlarmNoticesRequest $req) 该接口用于获取告警通知模板列表
 * @method Models\DescribeAlarmsResponse DescribeAlarms(Models\DescribeAlarmsRequest $req) 本接口用于获取告警策略。
 * @method Models\DescribeConfigMachineGroupsResponse DescribeConfigMachineGroups(Models\DescribeConfigMachineGroupsRequest $req) 获取采集规则配置所绑定的机器组
 * @method Models\DescribeConfigsResponse DescribeConfigs(Models\DescribeConfigsRequest $req) 获取采集规则配置
 * @method Models\DescribeExportsResponse DescribeExports(Models\DescribeExportsRequest $req) 本接口用于获取日志导出列表
 * @method Models\DescribeIndexResponse DescribeIndex(Models\DescribeIndexRequest $req) 本接口用于获取索引配置信息
 * @method Models\DescribeLogContextResponse DescribeLogContext(Models\DescribeLogContextRequest $req) 本接口用于搜索日志上下文附近的内容
 * @method Models\DescribeLogsetsResponse DescribeLogsets(Models\DescribeLogsetsRequest $req) 本接口用于获取日志集信息列表。
 * @method Models\DescribeMachineGroupConfigsResponse DescribeMachineGroupConfigs(Models\DescribeMachineGroupConfigsRequest $req) 获取机器组绑定的采集规则配置
 * @method Models\DescribeMachineGroupsResponse DescribeMachineGroups(Models\DescribeMachineGroupsRequest $req) 获取机器组信息列表
 * @method Models\DescribeMachinesResponse DescribeMachines(Models\DescribeMachinesRequest $req) 获取制定机器组下的机器状态
 * @method Models\DescribePartitionsResponse DescribePartitions(Models\DescribePartitionsRequest $req) 本接口用于获取主题分区列表。
 * @method Models\DescribeShipperTasksResponse DescribeShipperTasks(Models\DescribeShipperTasksRequest $req) 获取投递任务列表
 * @method Models\DescribeShippersResponse DescribeShippers(Models\DescribeShippersRequest $req) 获取投递规则信息列表
 * @method Models\DescribeTopicsResponse DescribeTopics(Models\DescribeTopicsRequest $req)  本接口用于获取日志主题列表，支持分页
 * @method Models\GetAlarmLogResponse GetAlarmLog(Models\GetAlarmLogRequest $req) 本接口用于获取告警任务历史
 * @method Models\MergePartitionResponse MergePartition(Models\MergePartitionRequest $req) 本接口用于合并一个读写态的主题分区，合并时指定一个主题分区 ID，日志服务会自动合并范围右相邻的分区。
 * @method Models\ModifyAlarmResponse ModifyAlarm(Models\ModifyAlarmRequest $req) 本接口用于修改告警策略。需要至少修改一项有效内容。
 * @method Models\ModifyAlarmNoticeResponse ModifyAlarmNotice(Models\ModifyAlarmNoticeRequest $req) 该接口用于修改告警通知模板。
 * @method Models\ModifyConfigResponse ModifyConfig(Models\ModifyConfigRequest $req) 修改采集规则配置
 * @method Models\ModifyIndexResponse ModifyIndex(Models\ModifyIndexRequest $req) 本接口用于修改索引配置

 * @method Models\ModifyLogsetResponse ModifyLogset(Models\ModifyLogsetRequest $req) 本接口用于修改日志集信息
 * @method Models\ModifyMachineGroupResponse ModifyMachineGroup(Models\ModifyMachineGroupRequest $req) 修改机器组
 * @method Models\ModifyShipperResponse ModifyShipper(Models\ModifyShipperRequest $req) 修改现有的投递规则，客户如果使用此接口，需要自行处理CLS对指定bucket的写权限。
 * @method Models\ModifyTopicResponse ModifyTopic(Models\ModifyTopicRequest $req) 本接口用于修改日志主题。
 * @method Models\RetryShipperTaskResponse RetryShipperTask(Models\RetryShipperTaskRequest $req) 重试失败的投递任务
 * @method Models\SearchLogResponse SearchLog(Models\SearchLogRequest $req) 本接口用于搜索日志
 * @method Models\SplitPartitionResponse SplitPartition(Models\SplitPartitionRequest $req) 本接口用于分裂主题分区
 */

class ClsClient extends AbstractClient
{
    /**
     * @var string
     */
    protected $endpoint = "cls.tencentcloudapi.com";

    /**
     * @var string
     */
    protected $service = "cls";

    /**
     * @var string
     */
    protected $version = "2020-10-16";

    /**
     * @param Credential $credential
     * @param string $region
     * @param ClientProfile|null $profile
     * @throws TencentCloudSDKException
     */
    function __construct($credential, $region, $profile=null)
    {
        parent::__construct($this->endpoint, $this->version, $credential, $region, $profile);
    }

    public function returnResponse($action, $response)
    {
        $respClass = "TencentCloud"."\\".ucfirst("cls")."\\"."V20201016\\Models"."\\".ucfirst($action)."Response";
        $obj = new $respClass();
        $obj->deserialize($response);
        return $obj;
    }
}
