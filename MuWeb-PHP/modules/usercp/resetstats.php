<?php
/**
 * 文件说明
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
			<li class="breadcrumb-item active" aria-current="page">在线洗点</li>
		</ol>
	</nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	$Character = new Character();
	$AccountCharacters = $Character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
	if(!is_array($AccountCharacters)) throw new Exception('您的账号没有角色。');
    try {
        if(check_value($_POST['submit'])) {
            $Character->setCharacterResetStats($_SESSION['group'],$_SESSION['username'], $_POST['character'],$_POST['id']);
        }
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }
    ?>
    <div class="card mb-3">
        <div class="card-header">在线洗点</div>
        <div class="">
            <table class="table table-striped table-bordered text-center">
                <tr>
                    <td>职业</td>
                    <td>角色</td>
                    <td>等级</td>
                    <td>力量</td>
                    <td>敏捷</td>
                    <td>体力</td>
                    <td>智力</td>
                    <td>统率</td>
                    <td>操作</td>
                </tr>
                <?
                foreach($AccountCharacters as $uid=>$thisCharacter) {
                    $characterData = $Character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
                    $characterIMG = $Character->GenerateCharacterClassAvatar($characterData[_CLMN_CHR_CLASS_],1,1,35);
                ?>
                    <form action="" method="post">
                        <tr>
                            <td><?=$characterIMG?></td>
                            <td><?=$characterData[_CLMN_CHR_NAME_]?></td>
                            <td><?=($characterData['cLevel']+(int)$characterData['mLevel'])?></td>
                            <td><?=number_format($characterData[_CLMN_CHR_STAT_STR_])?></td>
                            <td><?=number_format($characterData[_CLMN_CHR_STAT_AGI_])?></td>
                            <td><?=number_format($characterData[_CLMN_CHR_STAT_VIT_])?></td>
                            <td><?=number_format($characterData[_CLMN_CHR_STAT_ENE_])?></td>
                            <td><?=number_format($characterData[_CLMN_CHR_STAT_CMD_])?></td>
                            <input type="hidden" name="character" value="<?=$characterData[_CLMN_CHR_NAME_]?>"/>
                            <input type="hidden" name="id" value="<?=$uid?>"/>
                            <input type="hidden" name="key" value="<?=Token::generateToken('ResetStats'.$uid)?>"/>
                            <td>
                                <button name="submit" value="submit" class="btn btn-success col-md-12">洗点</button>
                            </td>
                        </tr>
                    </form>
                <?}?>
            </table>
            <footer class="text-muted text-center mt-2 mb-3">
                <?if(mconfig('resetstats_price_zen')){?><p>您的角色必须至少拥有[<?=number_format((int)mconfig('resetstats_price_zen'))?>]金币(Zen)才能使用该功能。</p><?}?>
                <?if(mconfig('resetstats_level')){?><p>您的角色必须达到[<?=number_format((int)mconfig('resetstats_level'))?>]级(等级+大师)才能使用该功能。</p><?}?>
                <?if(mconfig('time_requirement')){?><p>每次使用洗点功能的角色将有[<?=mconfig('time_requirement')?>]天的冷却时间限制。</p><?}?>
            </footer>
        </div>
    </div>
<?
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}