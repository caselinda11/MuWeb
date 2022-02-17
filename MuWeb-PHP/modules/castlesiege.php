<?php
/**
 * 罗兰峡谷信息模块页面
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
        <li class="breadcrumb-item active" aria-current="page">罗兰峡谷</li>
    </ol>
</nav>
<?php
if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
$castleData = loadCache('castle_siege.cache');
?>
    <?if(is_array($castleData['castle'])){?>
        <?foreach ($castleData['castle'] as $code=>$cData){?>
    <div class="card mb-3">
        <div class="card-header">罗兰峡谷信息 - [<?=getGroupNameForGroupID($code)?>]</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 align-content-center align-self-center">
                    <div class="text-center">
                        <?=getGuildLogo($cData[_CLMN_GUILD_LOGO_], 180,"");?>
                    </div>
                </div>
                <div class="col-md-7">
                    <table class="table table-striped table-hover table-sm text-center">
                        <tr><th>所属大区</th><td><?=getGroupNameForGroupID($code)?></td></tr>
                        <tr><th>所属战盟</th><td><?=guildProfile(getServerCodeForGroupID($code),$cData[_CLMN_MCD_GUILD_OWNER_]);?></td></tr>
                        <tr><th>战盟盟主</th><td><?=playerProfile(getServerCodeForGroupID($code),$cData['G_Master'])?></td></tr>
                        <tr><th>维持费用</th><td><?=number_format(round($cData[_CLMN_MCD_MONEY_]));?></td></tr>
                        <tr><th>玛雅费用</th><td><?=$cData[_CLMN_MCD_TRC_];?></td></tr>
                        <tr><th>商店费用</th><td><?=$cData[_CLMN_MCD_TRS_];?></td></tr>
                        <tr><th>狩猎费用</th><td><?=$cData[_CLMN_MCD_THZ_];?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
        <?}?>
    <?}?>
    <?if(!empty($castleData['guilds'])) {?>
        <div class="card">
            <div class="card-header">罗兰注册信息</div>
            <div class="">
                <table class="table table-striped table-sm text-center">
                    <tr>
                        <th>所属大区</th>
                        <th></th>
                        <th>注册战盟</th>
                    </tr>
                    <?foreach($castleData['guilds'] as $key=>$guild) {?>
                        <?foreach ($guild as $item){?>
                        <tr>
                            <td width="30%"><?=getGroupNameForGroupID($key)?></td>
                            <th width="10%">-</th>
                            <td width="30%"><?=$item?></td></tr>
                        <?}?>
                    <?}?>
                </table>
            </div>
        </div>
<?php
    }
} catch(Exception $ex) {
message('error', $ex->getMessage());
}