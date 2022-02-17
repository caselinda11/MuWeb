<?php
/**
 * 战盟个人资料页面
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>/rankings/guilds">战盟排名</a></li>
            <li class="breadcrumb-item active" aria-current="page">战盟简要</li>
        </ol>
    </nav>
<?php

loadModuleConfigs('profiles');
if(mconfig('active')) {
	if(check_value($_GET['page'])) {
		try {
			$weProfiles = new weProfiles();
			$weProfiles->setRequest($_GET['group'],$_GET['subpage'],$_GET['gname']);
            $guildData = $weProfiles->cacheGuildData();
			$guildMember = explode(",", $guildData[5][0]);
			$displayData = [
				'gName' => $guildData[1],
				'gLogo' => getGuildLogo($guildData[2],150),
				'gMaster' => $guildData[4],
				'gScore' => $guildData[3] ? $guildData[3] : 0,
				'gMember' => count($guildMember),
			];
			?>
            <div class="profiles_guild_card">
                <div class="card-header">[<?=$displayData['gName']?>] - 战盟简要</div>
                <div class="">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row justify-content-center">
                            <?=$displayData['gLogo']?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <table class="table table-sm table-borderless text-white text-center">
                                <tr>
                                    <th colspan="2"><?=$displayData['gName']?></th>
                                </tr>
                                <tr>
                                    <th>战盟盟主</th>
                                    <td><?=$displayData['gMaster']?></td>
                                </tr>
                                <tr>
                                    <th>战盟评分</th>
                                    <td><?=$displayData['gScore']?></td>
                                </tr>
                                <tr>
                                    <th>战盟人数</th>
                                    <td><?=number_format($displayData['gMember'])?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <footer class="blockquote-footer text-right mb-2 mr-3">
                        最后更新时间:
                        <cite title="Source Title">
                            <?=date('Y-m-d h:i A',$guildData[0])?>
                        </cite>
                    </footer>
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="card-header">战盟成员</div>
                <div class="card-body">
                    <div class="w-100 guild-member text-center">
                        <?foreach ($guildMember AS $key => $member){?>
                            <?=playerProfile($_GET['group'],$member,'guild-member btn btn-default mb-1')?>
                        <?}?>
                </div>
                </div>
            </div>
<?php
		} catch(Exception $e) {
			message('error', $e->getMessage());
		}
	} else {
		message('error', '您的请求无法完成，请再试一次。');
	}
} else {
	message('error', '该功能暂已关闭，请稍后尝试访问。');
}