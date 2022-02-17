<?php
/**
 * 转身模块
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
            <li class="breadcrumb-item active" aria-current="page">角色转身</li>
        </ol>
    </nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	$Character = new Character();
	$AccountCharacters = $Character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
	if(!is_array($AccountCharacters)) throw new Exception('您的账号没有角色。');
	try{
        if(check_value($_POST['submit'])) {
            if($_POST['submit'] == "receive"){
                $Character->receiveResetAward();
            }else{
                $Character->setCharacterReset($_SESSION['group'],$_SESSION['username'],$_POST['character'],$_SESSION['userid'],$_POST['id']);
            }
        }
    } catch (Exception $ex) {
        message('error', $ex->getMessage());
    }
    $awardList = Connection::Database("Web")->query_fetch("select * from [X_TEAM_RESET_AWARD]");
?>
    <div class="card mb-3">
        <div class="card-header">角色转身</div>
        <div class="">
            <table class="table table-striped table-bordered text-center">
                <tr>
                    <th>类型</th>
                    <th>角色</th>
                    <th>等级</th>
                    <th>金币(Zen)</th>
                    <th>转身</th>
                    <th>操作</th>
                </tr>
                <?
                foreach($AccountCharacters as $id=>$thisCharacter) {
                    $Char = $Character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                ?>
                    <form action="" method="post">
                        <tr>
                            <td><?=$Character->GenerateCharacterClassAvatar($Char[_CLMN_CHR_CLASS_],1,1,40)?></td>
                            <td><?=$Char[_CLMN_CHR_NAME_]?></td>
                            <td><?=$Char[_CLMN_CHR_LVL_]?></td>
                            <td><?=number_format($Char[_CLMN_CHR_ZEN_])?></td>
                            <td><?=number_format($Char[_CLMN_CHR_RSTS_])?></td>
                            <input type="hidden" name="character" value="<?=$Char[_CLMN_CHR_NAME_]?>"/>
                            <input type="hidden" name="id" value="<?=$id?>"/>
                            <input type="hidden" name="key" value="<?=Token::generateToken('reset'.$id)?>" />
                            <td>
                                <div class="btn-group">
                                <? if(is_array($awardList)){
                                    foreach ($awardList as $awardData){
                                        if($awardData['award_count'] <= $Char[_CLMN_CHR_RSTS_]){
                                            $check = Connection::Database("Web")->query_fetch_single("select * from [X_TEAM_RESET_AWARD_LOG] where [award_id] = ? AND [character] = ?",[$awardData['ID'],$Char[_CLMN_CHR_NAME_]]);
                                            if(!is_array($check) || empty($check)){
                                                echo "<input type='hidden' name='award_id' value='".$awardData['ID']."'>";
                                                echo "<button name='submit' value='receive' class='btn btn-danger text-white'>领奖</button>";
                                            }
                                        }
                                    }
                                } ?>
                                <button name="submit" value="submit" class="btn btn-success">转身</button>
                                </div>
                            </td>
                        </tr>
                    </form>
                <?}?>
            </table>

            <footer class="text-muted text-center mt-2 mb-3">
                <p>每个角色最多可转身[<?=mconfig('resets_max')?>]次，满转将无法使用。</p>
                <?if(mconfig('resets_required_level')){?><p>您的角色等级必须达到[<?=mconfig('resets_required_level')?>]级才能使用该功能。</p><?}?>
                <?if(mconfig('resets_price_zen')){?><p>您的角色必须至少拥有[<?=number_format((int)mconfig('resets_price_zen'))?>]金币(Zen)才能使用该功能。</p><?}?>
                <?if(mconfig('resets_stats_point')){?><p>使用该功能您的角色可获得[<?=number_format((int)mconfig('resets_stats_point'))?>]属性点奖励。</p><?}?>
                <?if(mconfig('resets_credits_reward')){?><p>使用该功能您可获得[<?=number_format((int)mconfig('resets_credits_reward'))?>]<?=getPriceType(mconfig('credit_type'))?>奖励。</p><?}?>
            </footer>
        </div>
    </div>
    <? if(is_array($awardList)){
    ?>
    <div class="card mb-3">
        <div class="card-header">转身奖励</div>
        <table class="table table-striped table-bordered text-center">
                <thead>
                <tr>
                    <th>转身要求</th>
                    <th>奖励物品</th>
                    <th width="50%">物品说明</th>
                </tr>
                </thead>
            <? foreach ($awardList as $data) {?>
                    <tr>
                        <td><strong><?= $data['award_count'] ?></strong></td>
                        <td><?= $data['award_name'] ?></td>
                        <td><?= $data['award_description'] ?></td>
                    </tr>
            <? } ?>
        </table>
        <footer class="text-muted text-center mt-2">
            <p>达到要求将可领取上述奖励</p>
        </footer>
    </div>
    <?}?>
<?
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}