<?php
/**
 * 清洗红名模块文件
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
            <li class="breadcrumb-item active" aria-current="page">清洗红名</li>
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
            $Character->setCharacterClearPK($_SESSION['group'],$_SESSION['username'], $_POST['character'], $_POST['id']);
        }
    }catch (Exception $exception){
        message('error', $exception->getMessage());
    }
	?>
    <div class="card">
        <div class="card-header">清洗红名</div>
        <table class="table table-striped table-bordered text-center">
            <tr>
                <td>类型</td>
                <td>角色</td>
                <td>金币(Zen)</td>
                <td>状态</td>
                <td>杀人数</td>
                <td>操作</td>
            </tr>
        <?
		foreach($AccountCharacters as $uid=>$thisCharacter) {
		    $data = $Character->getCharacterDataForCharacterName($_SESSION['group'],$thisCharacter);
            ?>
			<form action="" method="post">
				<tr>
                    <td><?=$Character->GenerateCharacterClassAvatar($data['Class'],1,1,35)?></td>
                    <td><?=$data['Name']?></td>
                    <td><?=$data['Money']?></td>
                    <td><?=getPkLevel($data['PkLevel'])?></td>
                    <td><?=$data['PkCount']?></td>
                    <td>
                        <input type="hidden" name="character" value="<?=$data['Name']?>"/>
                        <input type="hidden" name="id" value="<?=$uid?>"/>
                        <input type="hidden" name="key" value="<?=Token::generateToken('clearpk'.$uid)?>"/>
                        <button name="submit" value="submit" class="btn btn-success col-md-8">确定</button>
                    </td>
				</tr>
			 </form>
		<?}?>
	</table>
        <footer class="text-muted text-center mt-2 mb-3">
		    <?if(mconfig('clearpk_price_zen')){?> 您的角色必须至少拥有[<?=number_format((int)mconfig('clearpk_price_zen'))?>]金币(Zen)才能使用该功能。<?}?>
	    </footer>
	</div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}