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

namespace TencentCloud\Eiam\V20210420;

use TencentCloud\Common\AbstractClient;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Credential;
use TencentCloud\Eiam\V20210420\Models as Models;

/**
 * @method Models\AddUserToUserGroupResponse AddUserToUserGroup(Models\AddUserToUserGroupRequest $req) 加入用户到用户组
 * @method Models\CreateOrgNodeResponse CreateOrgNode(Models\CreateOrgNodeRequest $req) 新建一个机构节点
 * @method Models\CreateUserResponse CreateUser(Models\CreateUserRequest $req) 新建一个用户
 * @method Models\CreateUserGroupResponse CreateUserGroup(Models\CreateUserGroupRequest $req) 新建用户组
 * @method Models\DeleteOrgNodeResponse DeleteOrgNode(Models\DeleteOrgNodeRequest $req) 删除一个机构节点
 * @method Models\DeleteUserResponse DeleteUser(Models\DeleteUserRequest $req) 通过用户名或用户 id 删除用户。
 * @method Models\DeleteUserGroupResponse DeleteUserGroup(Models\DeleteUserGroupRequest $req) 删除一个用户组
 * @method Models\DescribeApplicationResponse DescribeApplication(Models\DescribeApplicationRequest $req) 获取一个应用的信息。
 * @method Models\DescribeOrgNodeResponse DescribeOrgNode(Models\DescribeOrgNodeRequest $req) 根据机构节点ID读取机构节点信息
 * @method Models\DescribePublicKeyResponse DescribePublicKey(Models\DescribePublicKeyRequest $req) 获取JWT公钥信息。
 * @method Models\DescribeUserGroupResponse DescribeUserGroup(Models\DescribeUserGroupRequest $req) 获取用户组信息
 * @method Models\DescribeUserInfoResponse DescribeUserInfo(Models\DescribeUserInfoRequest $req) 通过用户名或用户 id 搜索用户
 * @method Models\ListApplicationAuthorizationsResponse ListApplicationAuthorizations(Models\ListApplicationAuthorizationsRequest $req) 应用授权关系列表（含搜索条件匹配）。
 * @method Models\ListApplicationsResponse ListApplications(Models\ListApplicationsRequest $req) 获取应用列表信息。
 * @method Models\ListAuthorizedApplicationsToOrgNodeResponse ListAuthorizedApplicationsToOrgNode(Models\ListAuthorizedApplicationsToOrgNodeRequest $req) 通过机构节点ID获得被授权访问的应用列表。
 * @method Models\ListAuthorizedApplicationsToUserResponse ListAuthorizedApplicationsToUser(Models\ListAuthorizedApplicationsToUserRequest $req) 通过用户ID获得被授权访问的应用列表。
 * @method Models\ListAuthorizedApplicationsToUserGroupResponse ListAuthorizedApplicationsToUserGroup(Models\ListAuthorizedApplicationsToUserGroupRequest $req) 通过用户组ID获得被授权访问的应用列表。
 * @method Models\ListUserGroupsResponse ListUserGroups(Models\ListUserGroupsRequest $req) 获取用户组列表信息（包含查询条件）。
 * @method Models\ListUserGroupsOfUserResponse ListUserGroupsOfUser(Models\ListUserGroupsOfUserRequest $req) 获取用户所在的用户组列表
 * @method Models\ListUsersResponse ListUsers(Models\ListUsersRequest $req) 获取用户列表信息。
 * @method Models\ListUsersInOrgNodeResponse ListUsersInOrgNode(Models\ListUsersInOrgNodeRequest $req) 根据机构节点ID读取节点下用户
 * @method Models\ListUsersInUserGroupResponse ListUsersInUserGroup(Models\ListUsersInUserGroupRequest $req) 获取用户组中的用户列表
 * @method Models\ModifyUserInfoResponse ModifyUserInfo(Models\ModifyUserInfoRequest $req) 通过用户名或用户 id 冻结用户
 * @method Models\RemoveUserFromUserGroupResponse RemoveUserFromUserGroup(Models\RemoveUserFromUserGroupRequest $req) 从用户组中移除用户
 * @method Models\UpdateOrgNodeResponse UpdateOrgNode(Models\UpdateOrgNodeRequest $req) 新建一个机构节点，
 */

class EiamClient extends AbstractClient
{
    /**
     * @var string
     */
    protected $endpoint = "eiam.tencentcloudapi.com";

    /**
     * @var string
     */
    protected $service = "eiam";

    /**
     * @var string
     */
    protected $version = "2021-04-20";

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
        $respClass = "TencentCloud"."\\".ucfirst("eiam")."\\"."V20210420\\Models"."\\".ucfirst($action)."Response";
        $obj = new $respClass();
        $obj->deserialize($response);
        return $obj;
    }
}
