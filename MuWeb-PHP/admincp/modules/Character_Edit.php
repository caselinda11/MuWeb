<?php
/**
 * 文件说明
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

if(check_value($_GET['name'])) {
	try {
		if(!Validator::Number($_GET['group'])) throw new Exception("无效的分区!");
		$Character = new Character();
        $dB = Connection::Database('MuOnline',$_GET['group']);
		if(!$Character->_checkCharacterExists($_GET['group'],$_GET['name'])) throw new Exception("角色不存在!");
        $common = new common();
		if(check_value($_POST['submit'])) {
			try {
				if($_POST['name'] != $_GET['name']) throw new Exception("无效的角色名！");
				if(!check_value($_POST['account'])) throw new Exception("无效的账号！");
				if(!Validator::UnsignedNumber($_POST['class'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['level'])) throw new Exception("所有输入的值必须是数字!");
				if(check_value($_POST['resets'])) if(!Validator::UnsignedNumber($_POST['resets'])) throw new Exception("所有输入的值必须是数字!");
				if(check_value($_POST['gresets'])) if(!Validator::UnsignedNumber($_POST['gresets'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['zen'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['lvlpoints'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['pklevel'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['CtlCode'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['str'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['agi'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['vit'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['ene'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['cmd'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['mlevel'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['mlexp'])) throw new Exception("所有输入的值必须是数字!");
				if(check_value($_POST['mlnextexp'])) if(!Validator::UnsignedNumber($_POST['mlnextexp'])) throw new Exception("所有输入的值必须是数字!");
				if(!Validator::UnsignedNumber($_POST['mlpoint'])) throw new Exception("所有输入的值必须是数字!");
				
				// 检查是否在线
				if($common->checkUserOnline($_GET['group'],$_POST['account'])) throw new Exception("该帐号当前在线!");
				
				// 更新数据
				$updateData = array(
					'name' => $_POST['name'],
					'class' => $_POST['class'],
					'level' => $_POST['level'],
					'zen' => $_POST['zen'],
					'lvlpoints' => $_POST['lvlpoints'],
					'pklevel' => $_POST['pklevel'],
					'CtlCode' => $_POST['CtlCode'],
					'str' => $_POST['str'],
					'agi' => $_POST['agi'],
					'vit' => $_POST['vit'],
					'ene' => $_POST['ene'],
					'cmd' => $_POST['cmd']
				);
				
				if(check_value($_POST['resets'])) {
					$updateData['resets'] = $_POST['resets'];
				}

				$query = "UPDATE "._TBL_CHR_." SET ";
					$query .= _CLMN_CHR_CLASS_ . " = :class,";
					$query .= _CLMN_CHR_LVL_ . " = :level,";
					if(check_value($updateData['resets'])) $query .= _CLMN_CHR_RSTS_ . " = :resets,";
					$query .= _CLMN_CHR_ZEN_ . " = :zen,";
					$query .= _CLMN_CHR_LVLUP_POINT_ . " = :lvlpoints,";
					$query .= _CLMN_CHR_PK_LEVEL_ . " = :pklevel,";
					$query .= "CtlCode = :CtlCode,";
					$query .= _CLMN_CHR_STAT_STR_ . " = :str,";
					$query .= _CLMN_CHR_STAT_AGI_ . " = :agi,";
					$query .= _CLMN_CHR_STAT_VIT_ . " = :vit,";
					$query .= _CLMN_CHR_STAT_ENE_ . " = :ene,";
					$query .= _CLMN_CHR_STAT_CMD_ . " = :cmd";
					$query .= " WHERE [Name] = :name";
				
				$updateCharacter = $dB->query($query, $updateData);
				if(!$updateCharacter) throw new Exception("无法更新角色数据!");
				
				// 更新大师等级信息
				$updateMlData = array(
					'name' => $_POST['name'],
					'level' => $_POST['mlevel'],
					'exp' => $_POST['mlexp'],
					'points' => $_POST['mlpoint']
				);
				
				if(check_value($_POST['mlnextexp'])) {
					$updateMlData['nextexp'] = $_POST['mlnextexp'];
				}
				
				$mlQuery = "UPDATE "._TBL_MASTERLVL_." SET ";
					$mlQuery .= _CLMN_ML_LVL_ . " = :level,";
					$mlQuery .= _CLMN_ML_EXP_ . " = :exp,";
					if(check_value($updateMlData['nextexp'])) $mlQuery .= _CLMN_ML_NEXP_ . " = :nextexp,";
					$mlQuery .= _CLMN_ML_POINT_ . " = :points";
					$mlQuery .= " WHERE "._CLMN_ML_NAME_." = :name";
				
				$updateMlCharacter = $dB->query($mlQuery, $updateMlData);
				if(!$updateCharacter) throw new Exception("大师数据无法更新!");
				message('success','编辑更新完成!');
			} catch(Exception $ex) {
				message('error', $ex->getMessage());
			}
		}
		
		$charData = $Character->getCharacterDataForCharacterName($_GET['group'],$_GET['name']);
		if(!$charData) throw new Exception("无法获取角色信息（无效角色）。");
        ?>

        <form role="form" method="post" class="mt-3">

            <!--                基础操作-->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">基础</div>
                    <div class="card-body">
                        <table class="table table-no-border table-hover">
                            <tr>
                                <th>账号:</th>
                                <td><a href="<?=admincp_base("Account_Info&id=".$common->getUserGIDForUsername($_GET['group'],$charData[_CLMN_CHR_ACCID_]))?>"><?=$charData[_CLMN_CHR_ACCID_]?></a></td>
                            </tr>
                            <tr>
                                <th>角色:</th>
                                <td>
                                    <select class="form-control" name="class">
                                        <?global $custom;
                                        foreach($custom['character_class'] as $classID => $thisClass) {
                                            if($classID == $charData[_CLMN_CHR_CLASS_]) {?>
                                                <option value="<?=$classID?>" selected="selected"><?=$thisClass[0]?> (<?=$thisClass[1]?>)</option>
                                            <?} else {?>
                                                <option value="<?=$classID?>"><?=$thisClass[0]?> (<?=$thisClass[1]?>)</option>
                                            <?}
                                        }?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>等级:</th>
                                <td><input class="form-control" type="number" name="level" value="<?=$charData[_CLMN_CHR_LVL_]?>"/></td>
                            </tr>

                            <?if(defined('_CLMN_CHR_RSTS_')) {?>
                                <tr>
                                    <th>转身:</th>
                                    <td><input class="form-control" type="number" name="resets" value="<?=$charData[_CLMN_CHR_RSTS_]?>"/></td>
                                </tr>
                            <?}?>

                            <tr>
                                <th>金*(Zen): <span class="text-danger">*最大不可超过21亿</span></th>
                                <td><input class="form-control" type="number" name="zen" value="<?=$charData[_CLMN_CHR_ZEN_]?>"/></td>
                            </tr>
                            <tr>
                                <th>升级点数:</th>
                                <td><input class="form-control" type="number" name="lvlpoints" value="<?=$charData[_CLMN_CHR_LVLUP_POINT_]?>"/></td>
                            </tr>
                            <tr>
                                <th>角色状态:</th>
                                <td>
                                    <select name="CtlCode" class="form-control">
                                        <option value="" selected>未知</option>
                                        <option value="0" <?=selected($charData['CtlCode'],(string)0)?>>正常</option>
                                        <option value="1" <?=selected($charData['CtlCode'],(string)1)?>>封停</option>
                                        <option value="2" <?=selected($charData['CtlCode'],(string)2)?>>GM角色</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>红名状态:</th>
                                <td>
                                    <select name="pklevel" class="form-control">
                                        <?global $custom;
                                        foreach ($custom['pk_level'] as $key=>$name){
                                        ?>
                                        <option value="<?=$key?>" <?=selected($charData[_CLMN_CHR_PK_LEVEL_],(string)$key)?>><?=$name?></option>
                                        <?}?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!--                属性点操作-->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">属性点</div>
                    <div class="card-body">
                        <table class="table table-no-border table-hover">
                            <tr>
                                <th>力量:</th>
                                <td><input class="form-control" type="number" name="str" value="<?=$charData[_CLMN_CHR_STAT_STR_]?>"/></td>
                            </tr>
                            <tr>
                                <th>敏捷:</th>
                                <td><input class="form-control" type="number" name="agi" value="<?=$charData[_CLMN_CHR_STAT_AGI_]?>"/></td>
                            </tr>
                            <tr>
                                <th>体力:</th>
                                <td><input class="form-control" type="number" name="vit" value="<?=$charData[_CLMN_CHR_STAT_VIT_]?>"/></td>
                            </tr>
                            <tr>
                                <th>智力:</th>
                                <td><input class="form-control" type="number" name="ene" value="<?=$charData[_CLMN_CHR_STAT_ENE_]?>"/></td>
                            </tr>
                            <tr>
                                <th>统率:</th>
                                <td><input class="form-control" type="number" name="cmd" value="<?=$charData[_CLMN_CHR_STAT_CMD_]?>"/></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!--                大师操作-->
            <?
            if(defined('_TBL_MASTERLVL_')) {
                $mLInfo = $dB->query_fetch_single("SELECT "._CLMN_ML_LVL_.","._CLMN_ML_EXP_.","._CLMN_ML_NEXP_.","._CLMN_ML_POINT_." FROM "._TBL_MASTERLVL_." WHERE "._CLMN_ML_NAME_." = ?", [$charData["Name"]]);?>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">大师</div>
                        <div class="card-body">
                            <?if(is_array($mLInfo)) {?>
                                <table class="table table-no-border table-hover">
                                    <tr>
                                        <th>大师等级:</th>
                                        <td><input class="form-control" type="number" name="mlevel" value="<?=$mLInfo[_CLMN_ML_LVL_]?>"/></td>
                                    </tr>
                                    <tr>
                                        <th>经验:</th>
                                        <td><input class="form-control" type="number" name="mlexp" value="<?=$mLInfo[_CLMN_ML_EXP_]?>"/></td>
                                    </tr>
                                    <?if(defined('_CLMN_ML_NEXP_')) {?>
                                        <tr>
                                            <th>下一级经验:</th>
                                            <td><input class="form-control" type="number" name="mlnextexp" value="<?=$mLInfo[_CLMN_ML_NEXP_]?>"/></td>
                                        </tr>
                                    <?}?>
                                    <tr>
                                        <th>点数:</th>
                                        <td><input class="form-control" type="number" name="mlpoint" value="<?=$mLInfo[_CLMN_ML_POINT_]?>"/></td>
                                    </tr>
                                </table>
                            <?} else {
                                message('warning', '无法获取大师信息!', ' ');
                            }?>
                        </div>
                    </div>
                </div>
            <?}?>

            <div class="row justify-content-center mb-3">
                <div class="col-md-4">
                    <input type="hidden" name="name" value="<?=$charData["Name"]?>"/>
                    <input type="hidden" name="account" value="<?=$charData[_CLMN_CHR_ACCID_]?>"/>
                    <button type="submit" class="btn btn-large btn-block btn-success" name="submit" value="ok">保存</button>
                </div>
            </div>
        </form>

<?php
	} catch(Exception $ex) {
		message('error', $ex->getMessage());
	}
	
} else {
	message('error', '请提供有效的用户ID!');
}