<?php
/**
 * [MemberReward]模块页面
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/


?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>usercp">我的账号</a></li>
            <li class="breadcrumb-item active" aria-current="page">会员领奖</li>
        </ol>
    </nav>
    <?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $MemberReward = new \Plugin\MemberReward();
    $list = $MemberReward->getMemberRewardList();
    if(!is_array($list)) throw new Exception("暂无奖励可领取，请稍后再试或联系在线客服。");
    $character = new Character();
    $AccountCharacters = $character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
    if(!is_array($AccountCharacters)) throw new Exception('您必须创建至少一个角色才能使用该功能！');
    try{
        if(check_value($_POST['submit'])) {
            $MemberReward->setMemberRewardSend($_SESSION['group'],$_SESSION['username'], $_POST['character_name'],$_POST['package_level']);
        }
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">会员领奖</div>
        <div class="card-body mb-3">
            <?if(is_array($list)){?>
            <table class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th width="20%">礼包名称</th>
                        <th width="20%">礼包会员等级需要</th>
                        <th>礼包详细说明</th>
                        <th width="15%">状态</th>
                    </tr>
                </thead>
                <tbody>
                <?foreach ($list as $data){?>
                <tr>
                    <td><?=$data['reward_name']?></td>
                    <td><strong>VIP-<?=$data['requirement_vip']?></strong></td>
                    <td><?=$data['reward_Description']?></td>
                    <?$status = ($MemberReward->checkMemberRewardSend($_SESSION['group'],$_SESSION['username'],$data['ID'])) ? '<span class="badge badge-success">未领</span>' : '<span class="badge badge-secondary">已领</span>'?>
                    <td><?=$status?></td>
                </tr>
                <?}?>
                </tbody>
            </table>
                <br>
            <?}?>
            <div class="mt-3">
            <form class="form-horizontal mt-3" action="" method="post">

                <div class="form-group row justify-content-md-center">
                    <label for="package_level" class="col-sm-4 col-form-label text-right">
                        选择礼包
                    </label>
                    <div class="col-sm-5">
                        <select id="package_level" name="package_level" class="form-control">
                            <?if(is_array($list)){?>
                                <?foreach ($list as $data){?>
                                <option value="<?=$data['ID']?>"><?=$data['reward_name']?></option>
                                <?}?>
                            <?}else{?>
                                <option value="">暂无礼包信息</option>
                                <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="character" class="col-sm-4 col-form-label text-right">
                        接收角色
                    </label>
                    <div class="col-sm-5">
                        <select id="character" name="character_name" class="form-control">
                            <?
                            # 角色信息
                            if(is_array($AccountCharacters)){?>
                            <?foreach($AccountCharacters as $code=>$thisCharacter) {?>
                                <?$characterData = $character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                                if($characterData['CtlCode']) continue;?>
                                <option value="<?=$thisCharacter?>"><?=$thisCharacter?></option>
                            <?}?>
                            <?}else{?>
                                <option value="">没有角色</option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="level" class="col-sm-4 col-form-label text-right">
                        会员等级
                    </label>
                    <div class="col-sm-5">
                        <input id="level" type="text" name="level" value="<?=$MemberReward->getMemberLevel($_SESSION['group'],$_SESSION['username']);?>" disabled="disabled" class="form-control">
                    </div>
                    <small class="col-sm-3 text-left form-inline text-muted">*您当前会员等级</small>
                </div>
                <div class="form-group row justify-content-md-center">
                    <input type="hidden" name="key" value="<?=Token::generateToken('MemberReward')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-sm-4">
                        确定
                    </button>
                </div>

            </form>
            </div>
            <div class="mt-3">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>领取要求</li>
                    <p class="alert alert-info">领取奖励账号必须离线，每个奖励限领一次，不可重复领取。</p>
                    <li>角色领取</li>
                    <p class="alert alert-info">选择一个角色领取该奖励，奖励将发放至该角色的储物柜(预言盒)中。</p>
                    <li>储物柜说明</li>
                    <p class="alert alert-info">一旦领取后请七日内领取道具，否则将视为放弃奖励自定删除。</p>
                </ol>
            </div>
            <footer class="blockquote-footer text-right mt-2 mb-2 mr-3">
                <cite title="Source Title">会员领奖</cite>
            </footer>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}