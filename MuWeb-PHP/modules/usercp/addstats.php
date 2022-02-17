<?php
/**
 * 角色加点页面
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
            <li class="breadcrumb-item active" aria-current="page">在线加点</li>
        </ol>
    </nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    global $custom;
    if(!is_array($custom['character_cmd'])) throw new Exception('无法确定哪个角色需要使用统率，请在自定义扩展文件中配置角色统率。');
	$Character = new Character();
	$AccountCharacters = $Character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
	if(!is_array($AccountCharacters)) throw new Exception('您的账号没有角色。');
    try {
        if(check_value($_POST['submit'])) {
            $Character->setCharacterAddStats($_SESSION['group'],$_SESSION['username'], $_POST['character'],$_POST['id'], $_POST['add_str'], $_POST['add_agi'], $_POST['add_vit'], $_POST['add_ene'], $_POST['add_cmd']);
        }
    } catch (Exception $ex) {
        message('error', $ex->getMessage());
    }
	?>
        <div class="card mb-3">
            <div class="card-header">在线加点</div>
    <?
    foreach($AccountCharacters as $uid => $thisCharacter) {
        $characterData = $Character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
        ?>
        <div class="card mt-2">
            <div class="card-header"><?=$characterData['Name'];?></div>
            <div class="row no-gutters">
                <div class="col-md-5 text-center align-items-center align-self-center">
                    <?=$Character->GenerateCharacterClassAvatar($characterData['Class'])?>
                    <div>剩余[<strong><?=number_format($characterData['LevelUpPoint'])?></strong>]属性点可分配</div>
                    <div><?=(mconfig('addstats_price_zen') > $characterData['Money']) ? "<span class='text-danger'>角色至少拥有[".number_format((int)mconfig('addstats_price_zen'))."]金币(Zen)才能使用该功能</span>" : "拥有[<strong>".number_format((int)$characterData['Money'])."</strong>]金币"?></div>
                </div>
                <div class="col-md-7">
                    <div class="card-body">
                        <table class="table text-center">
                            <form class="form-horizontal" action="" method="post">

                                <tr><td>力量</td><td><input type="number" class="form-control" min="1" step="1" max="<?=mconfig('addstats_max_stats')?>" name="add_str" placeholder="0"></td></tr>
                                <tr><td>敏捷</td><td><input type="number" class="form-control" min="1" step="1" max="<?=mconfig('addstats_max_stats')?>" name="add_agi" placeholder="0"></td></tr>
                                <tr><td>体力</td><td><input type="number" class="form-control" min="1" step="1" max="<?=mconfig('addstats_max_stats')?>" name="add_vit" placeholder="0"></td></tr>
                                <tr><td>智力</td><td><input type="number" class="form-control" min="1" step="1" max="<?=mconfig('addstats_max_stats')?>" name="add_ene" placeholder="0"></td></tr>
                                <?if(in_array($characterData['Class'], $custom['character_cmd'])) {?>
                                    <tr><td>统率</td><td><input type="number" class="form-control" min="1" step="1" max="<?=mconfig('addstats_max_stats')?>" name="add_cmd" placeholder="0"></td></tr>
                                <?}?>
                                <tr>
                                    <td colspan="2">
                                        <input type="hidden" name="character" value="<?=$characterData['Name'];?>"/>
                                        <input type="hidden" name="id" value="<?=$uid?>"/>
                                        <input type="hidden" name="key" value="<?=Token::generateToken('addstats'.$uid)?>" />
                                        <button name="submit" value="submit" class="btn btn-success col-md-8">加点</button>
                                    </td>
                                </tr>
                            </form>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?}?>
            <footer class="text-muted text-center mb-3">
                <?if(mconfig('addstats_price_zen')){?>您的角色必须至少拥有[<?=number_format((int)mconfig('addstats_price_zen'))?>]金币(Zen)才能使用该功能。<?}?>
            </footer>
        </div>
<?
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}