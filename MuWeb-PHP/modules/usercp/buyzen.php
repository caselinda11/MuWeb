<?php
/**
 * 购买金币模块文件
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
            <li class="breadcrumb-item active" aria-current="page">购买金币</li>
        </ol>
    </nav>
<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	// load database
	$db = Connection::Database('MuOnline',$_SESSION['group']);

	// common class
	$common = new common();
	
	$Character = new Character();
	$AccountCharacters = $Character->getCharacterNameForUsername($_SESSION['group'],$_SESSION['username']);
	if(!is_array($AccountCharacters)) throw new Exception('您的账号没有角色。');
	
	# 加载配置文件
	$maxZen = mconfig('max_zen');
	$exchangeRatio = mconfig('exchange_ratio');
	$incrementRate = mconfig('increment_rate');
	
	# zen buying configuration
	$buyOptions = [];
	for($multiplier = 1; $multiplier<=floor($maxZen/$incrementRate); $multiplier++) {
		$zenAmount = $multiplier*$incrementRate;
		$creditAmount = ceil($zenAmount/$exchangeRatio);
		$buyOptions[] = $creditAmount;
	}
	
	# process request
	if(check_value($_POST['submit']) && check_value($_POST['character']) && check_value($_POST['credits'])) {
		try {
            if(!Token::checkToken('buyzen')) throw new Exception('出错了，请您重新输入！');
			# check if account is online
			if($common->checkUserOnline($_SESSION['group'],$_SESSION['username']))  throw new Exception('您的账号已在线，请断开连接。');

			# check if credit value is allowed
			if(!in_array($_POST['credits'], $buyOptions)) throw new Exception('您的请求中提供的信息无效。');

			$char = $_POST['character'];
			$zen = $_POST['credits']*$exchangeRatio;

			# validate form data
			if(!Validator::UnsignedNumber($_POST['credits'])) throw new Exception('您的请求无法完成，请再试一次。');
			if($zen > $maxZen) throw new Exception('您的请求无法完成，请再试一次。');
			if(!in_array($char, $AccountCharacters)) throw new Exception('您的请求中提供的信息无效。');

			# gather character information
			$characterData = $Character->getCharacterDataForCharacterName($_SESSION['group'],$char);
			if(!is_array($characterData)) throw new Exception('您的请求无法完成，请再试一次。');

			# check zen
			$charZen = $characterData[_CLMN_CHR_ZEN_];
			if($charZen + $zen > $maxZen) throw new Exception('当前角色拥有['.number_format($charZen).']金币，每个角色最多可装['.number_format((int)$maxZen).']金币，请您重新选择合适的金额。');

			# subtract credits
			$creditSystem = new CreditSystem();
			$creditSystem->setConfigId((int)mconfig('credit_config'));
			$configSettings = $creditSystem->showConfigs(true);
			switch($configSettings['config_user_col_id']) {
				case 'userid':
					$creditSystem->setIdentifier($_SESSION['userid']);
					break;
				case 'username':
					$creditSystem->setIdentifier($_SESSION['username']);
					break;
				case 'character':
					$creditSystem->setIdentifier($char);
					break;
				default:
					throw new Exception("[货币系统] - 无效标识符。");
			}
			$creditSystem->subtractCredits($_SESSION['group'],$_POST['credits']);

			# send zen
			if(!$db->query("UPDATE "._TBL_CHR_." SET "._CLMN_CHR_ZEN_." = "._CLMN_CHR_ZEN_." + ? WHERE "._CLMN_CHR_NAME_." = ?", [$zen, $characterData[_CLMN_CHR_NAME_]]));

			alert('usercp/buyzen','恭喜您，金币购买完成，['.number_format($zen).']金币成功送达['.$char.']角色中！');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	?>
    <div class="card mb-3">
        <div class="card-header">购买金币</div>
        <div class="card-body">
        <form class="form-horizontal" action="" method="post">
            <div class="col-md-12">
                <div class="form-group row justify-content-md-center">
                    <label for="credits" class="col-sm-4 col-form-label text-right">角色</label>
                    <div class="col-md-4">
                    <select name="character" class="form-control">
                        <?foreach($AccountCharacters as $char) {?>
                            <option value="<?=$char?>"><?=$char?></option>
                        <?}?>
                    </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                    <label for="credits" class="col-sm-4 col-form-label text-right">金币</label>
                    <div class="col-md-4">
                        <select name="credits" class="form-control">
                            <?
                            foreach($buyOptions as $creditValue) {
                                $zenValue = $creditValue*$exchangeRatio;
                                if($zenValue > $maxZen) continue;
                                ?>
                                <option value="<?=$creditValue?>"><?=number_format($zenValue)?> - <?=$creditValue?> <?=getPriceType(mconfig('credit_config'))?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group row justify-content-md-center">
                        <input type="hidden" name="key" value="<?=Token::generateToken('buyzen')?>"/>
                        <button name="submit" value="submit" class="btn btn-success col-md-4">购买</button>
                </div>
            </div>
        </form>
        </div>
    </div>
	<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}