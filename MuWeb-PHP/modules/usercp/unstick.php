<?php
/**
 * 角色自救模块文件
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
            <li class="breadcrumb-item active" aria-current="page">角色自救</li>
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
            $Character->setCharacterUnstick($_SESSION['group'],$_SESSION['username'], $_POST['character'],$_POST['id']);
        }
    } catch (Exception $ex) {
        message('error', $ex->getMessage());
    }

    message('','角色将移动至大陆酒吧！','功能说明:');
	?>
    <div class="card">
        <div class="card-header">角色自救</div>
	 <table class="table table-striped table-bordered text-center">
         <tr>
             <td></td>
             <td>角色</td>
             <td>金币(Zen)</td>
             <td></td>
         </tr>
         <?
         foreach($AccountCharacters as $uid=>$thisCharacter) {
             $characterData = $Character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
            ?>
             <form action="" method="post">
                 <tr>
                 <td><?=$Character->GenerateCharacterClassAvatar($characterData[_CLMN_CHR_CLASS_],1,1,35)?></td>
                 <td><?=$characterData[_CLMN_CHR_NAME_]?></td>
                 <td><?=number_format($characterData[_CLMN_CHR_ZEN_])?></td>
                 <td>
                     <input type="hidden" name="character" value="<?=$characterData[_CLMN_CHR_NAME_]?>"/>
                     <input type="hidden" name="id" value="<?=$uid?>"/>
                     <input type="hidden" name="key" value="<?=Token::generateToken('Unstick'.$uid)?>"/>
                     <button name="submit" value="submit" class="btn btn-success col-md-6">自救</button>
                 </td>
                 </tr>
              </form>
         <?}?>
     </table>
        <footer class="text-muted text-center mt-2 mb-3">
            <?if(mconfig('unstick_price_zen')){?> 您的角色必须至少拥有[<?=number_format((int)mconfig('unstick_price_zen'))?>]金币(Zen)才能使用该功能。<?}?>
        </footer>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}