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
namespace TencentCloud\Ame\V20190916\Models;
use TencentCloud\Common\AbstractModel;

/**
 * DescribeKTVMusicDetail返回参数结构体
 *
 * @method KTVMusicBaseInfo getKTVMusicBaseInfo() 获取歌曲基础信息
 * @method void setKTVMusicBaseInfo(KTVMusicBaseInfo $KTVMusicBaseInfo) 设置歌曲基础信息
 * @method string getPlayToken() 获取播放凭证
 * @method void setPlayToken(string $PlayToken) 设置播放凭证
 * @method string getLyricsUrl() 获取歌词下载地址
 * @method void setLyricsUrl(string $LyricsUrl) 设置歌词下载地址
 * @method string getRequestId() 获取唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
 * @method void setRequestId(string $RequestId) 设置唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
 */
class DescribeKTVMusicDetailResponse extends AbstractModel
{
    /**
     * @var KTVMusicBaseInfo 歌曲基础信息
     */
    public $KTVMusicBaseInfo;

    /**
     * @var string 播放凭证
     */
    public $PlayToken;

    /**
     * @var string 歌词下载地址
     */
    public $LyricsUrl;

    /**
     * @var string 唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
     */
    public $RequestId;

    /**
     * @param KTVMusicBaseInfo $KTVMusicBaseInfo 歌曲基础信息
     * @param string $PlayToken 播放凭证
     * @param string $LyricsUrl 歌词下载地址
     * @param string $RequestId 唯一请求 ID，每次请求都会返回。定位问题时需要提供该次请求的 RequestId。
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
        if (array_key_exists("KTVMusicBaseInfo",$param) and $param["KTVMusicBaseInfo"] !== null) {
            $this->KTVMusicBaseInfo = new KTVMusicBaseInfo();
            $this->KTVMusicBaseInfo->deserialize($param["KTVMusicBaseInfo"]);
        }

        if (array_key_exists("PlayToken",$param) and $param["PlayToken"] !== null) {
            $this->PlayToken = $param["PlayToken"];
        }

        if (array_key_exists("LyricsUrl",$param) and $param["LyricsUrl"] !== null) {
            $this->LyricsUrl = $param["LyricsUrl"];
        }

        if (array_key_exists("RequestId",$param) and $param["RequestId"] !== null) {
            $this->RequestId = $param["RequestId"];
        }
    }
}
