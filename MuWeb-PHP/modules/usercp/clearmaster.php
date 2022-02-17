<?php
/**
 * 清洗大师
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
        <li class="breadcrumb-item active" aria-current="page">大师洗点</li>
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
            $Character->setCharacterClearMaster($_SESSION['group'],$_SESSION['username'],$_POST['character'],$_POST['id']);
        }
    }catch (Exception $exception){
        message('error', $exception->getMessage());
    }
	?>
<div class="card">
    <div class="card-header">大师洗点</div>
    <table class="table table-striped table-bordered text-center">
        <tr>
            <td>职业</td>
            <td>角色</td>
            <td>大师等级</td>
            <td>大师点</td>
            <td>金币(Zen)</td>
            <td>操作</td>
        </tr>
        <?
        foreach($AccountCharacters as $uid=>$thisCharacter) {
        $characterData = $Character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
        ?>
        <form action="" method="post">
            <tr>
                <td><?=$Character->GenerateCharacterClassAvatar($characterData['Class'],1,1,35)?></td>
                <td><?=$characterData['Name']?></td>
                <td><?=number_format((int)$characterData['mLevel'])?></td>
                <td><?=number_format((int)$characterData['mPoint'])?></td>
                <td><?=number_format($characterData['Money'])?></td>
                <td>
                    <input type="hidden" name="character" value="<?=$characterData[_CLMN_CHR_NAME_]?>"/>
                    <input type="hidden" name="id" value="<?=$uid?>"/>
                    <input type="hidden" name="key" value="<?=Token::generateToken('clearMaster'.$uid)?>"/>
                    <button name="submit" value="submit" class="btn btn-success col-md-8">清洗</button>
                </td>
            </tr>
        </form>
        <?}?>
    </table>
    <footer class="text-muted text-center mt-2 mb-3">
        <?if(mconfig('clearst_required_level') > 0){?> <div>您的角色大师等级必须达到[<?=number_format((int)mconfig('clearst_required_level'))?>]级才能使用该功能。</div><?}?>
        <?if(mconfig('clearst_enable_zen_requirement')){?> <div>您的角色必须至少拥有[<?=number_format((int)mconfig('clearst_price'))?>]金币(Zen)才能使用该功能。</div><?}?>
        <?if(!mconfig('clearst_enable_zen_requirement')){?> <div>您的角色必须至少拥有[<?=number_format((int)mconfig('clearst_price'))?>]<?=getPriceType(mconfig('clearst_credit_type'))?>才能使用该功能。</div><?}?>
    </footer>
</div>
	<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}