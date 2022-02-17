<?php
/**
 * 下载页面模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">游戏下载</li>
        </ol>
    </nav>
<?php
	if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	$downloadsCACHE = loadCache('downloads.cache');

	if(is_array($downloadsCACHE)) {
		foreach($downloadsCACHE as $tempDownloadsData) {
			switch($tempDownloadsData['download_type']) {
				case 1:
					$downloadClients[] = $tempDownloadsData;
				break;
				case 2:
					$downloadPATCHES[] = $tempDownloadsData;
				break;
				case 3:
					$downloadTOOLS[] = $tempDownloadsData;
				break;
			}
		}
	}
?>

    <?php
    #客户端类
	if(mconfig('show_client_downloads')) {
		if(!empty($downloadClients)) {?>
			<div class="card mb-3">
                <div class="card-header">游戏下载</div>
				<div class="">
					<table class="table table-striped">
					<?foreach($downloadClients as $download) {?>
						<tr>
							<td style="width: 60%">
                                <span class="font-weight-bold"><?=$download['download_title'];?></span>
                                <br />
                                <small class="text-muted ml-3"><?=$download['download_description'];?></small>
                            </td>
							<td style="width: 20%" class="text-center"><?=$download['download_size'];?></td>
							<td style="width: 20%" class="text-right"><a href="<?=$download['download_link'];?>" class="btn btn-primary" target="_blank" data-agl-cvt="6"><i class="fa fa-cloud-download mr-2" aria-hidden="true"></i>点击下载</a></td>
						</tr>
					<?}?>
					</table>
				</div>
			</div>
        <?}
	}?>

    <?php
    #补丁类
	if(mconfig('show_patch_downloads')) {
		if(!empty($downloadPATCHES)) {?>
            <div class="card mb-3">
                <div class="card-header">补丁下载</div>
                <div class="">
                    <table class="table">
                        <?foreach($downloadPATCHES as $download) {?>
                            <tr>
                                <td style="width: 60%">
                                    <span class="font-weight-bold"><?=$download['download_title'];?></span>
                                    <br />
                                    <small class="text-muted ml-3"><?=$download['download_description'];?></small>
                                </td>
                                <td style="width: 20%" class="text-center"><?=$download['download_size']?></td>
                                <td style="width: 20%" class="text-right"><a href="<?=$download['download_link'];?>" class="btn btn-primary" target="_blank" data-agl-cvt="6"><i class="fa fa-cloud-download mr-2" aria-hidden="true"></i>补丁下载</a></td>
                            </tr>
                        <?}?>
                    </table>
                </div>
            </div>
        <?}
	}?>

    <?php
    #工具类
	if(mconfig('show_tool_downloads')) {
		if(!empty($downloadTOOLS)) {?>
            <div class="card mb-3">
                <div class="card-header">工具下载</div>
                <div class="">
                    <table class="table table-striped">
                        <?foreach($downloadTOOLS as $download) {?>
                            <tr>
                                <td style="width: 60%">
                                    <span class="font-weight-bold"><?=$download['download_title'];?></span>
                                    <br />
                                    <small class="text-muted ml-3"><?=$download['download_description'];?></small>
                                </td>
                                <td style="width: 20%" class="text-center"><?=$download['download_size']?></td>
                                <td style="width: 20%" class="text-right"><a href="<?=$download['download_link'];?>" class="btn btn-default" target="_blank" data-agl-cvt="6"><i class="fa fa-cloud-download mr-2" aria-hidden="true"></i>工具下载</a></td>
                            </tr>
                        <?}?>
                    </table>
                </div>
            </div>
        <?}
	}?>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}
?>
<div class="card mb-3">
    <div class="card-header">配置要求</div>
    <div class="">
            <table class="table  text-center">
                <thead>
                <tr>
                    <th width="20%">#</th>
                    <th width="40%" >最低配置要求</th>
                    <th width="40%" >推荐配置</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th>处理器</th>
                    <td>单核 - 1.0 Ghz</td>
                    <td>双核 1.5 Ghz（或更高）</td>
                </tr>
                <tr>
                    <th>内存</th>
                    <td>512MB</td>
                    <td>1GB（或更多）</td>
                </tr>
                <tr>
                    <th>操作系统</th>
                    <td>Windows 7</td>
                    <td>Windows 10</td>
                </tr>
                <tr>
                    <th>显卡</th>
                    <td>64MB / 64 Bits</td>
                    <td>128MB / 128 Bits（或更高）</td>
                </tr>
                <tr>
                    <th>宽带</th>
                    <td>1 Mbps （ADSL）</td>
                    <td>5 Mbps （ADSL/光纤）</td>
                </tr>
                </tbody>
            </table>
    </div>
</div>

<div class="card mb-3 d-none d-lg-block">
    <div class="card-header">驱动程序</div>
    <div class="card-body">
            <table class="table text-center">
                <tbody>
                <tr>
                    <td class="team-standings__pos">
                        <a href="https://www.amd.com/zh-hans/support" target="_blank">
                            <img src="<?=__PATH_PUBLIC_IMG__?>down/ati.gif" />
                        </a>
                    </td>
                    <td class="team-standings__pos">
                        <a href="https://downloadcenter.intel.com/zh-cn/" target="_blank">
                            <img src="<?=__PATH_PUBLIC_IMG__?>down/intel.gif" />
                        </a>
                    </td>
                    <td class="team-standings__pos">
                        <a href="http://www.matrox.com/graphics/en/support/drivers/" target="_blank">
                            <img src="<?=__PATH_PUBLIC_IMG__?>down/matrox.gif" />
                        </a>
                    </td>
                    <td class="team-standings__pos">
                        <a href="https://www.nvidia.cn/Download/index.aspx?lang=cn" target="_blank">
                            <img src="<?=__PATH_PUBLIC_IMG__?>down/nvidia.gif" />
                        </a>
                    </td>
                    <td class="team-standings__pos">
                        <a href="https://www.sis.com/DriverDownload_CN.aspx" target="_blank">
                            <img src="<?=__PATH_PUBLIC_IMG__?>down/sis.gif" />
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
    </div>
</div>