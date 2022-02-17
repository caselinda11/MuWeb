<?php
/**
 * 角色管理
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <div class="btn-group float-right">
                <ol class="breadcrumb hide-phone p-0 m-0">
                    <li class="breadcrumb-item">
                        <a href="<?=admincp_base()?>">官方主页</a>
                    </li>
                    <li class="breadcrumb-item active">用户管理</li>
                    <li class="breadcrumb-item active">角色管理</li>
                </ol>
            </div>
            <h4 class="page-title">角色管理</h4>
        </div>
    </div>
</div>
<?php
try {
    global $serverGrouping;
    $character = new Character();
    $common = new common();
    if ($_POST['submit']) {
        try {
            if (!check_value($_POST['group'])) {
                if (!check_value($_POST['character_name'])) throw new Exception('请正确输入角色名');
                foreach ($serverGrouping AS $key => $item) {
                    $DB = ($item['SERVER_DB2_NAME']) ? $item['SERVER_DB2_NAME'] : $item['SERVER_DB_NAME'];
                    $result[$key] = Connection::Database('MuOnline', $key)->query_fetch_single("SELECT CharacterSystem.AccountID,AccountSystem.servercode FROM Character AS CharacterSystem LEFT JOIN [" . $DB . "].[dbo].[MEMB_INFO] AS AccountSystem ON CharacterSystem.AccountID = AccountSystem.memb___id COLLATE Chinese_PRC_CI_AS WHERE Name = ? AND AccountSystem.servercode = ?", [$_POST['character_name'], $item['SERVER_GROUP']]);
                    if (empty($result[$key])) continue;
                    $characterData[$key] = $character->getCharacterDataForCharacterName(getGroupIDForServerCode($result[$key]['servercode']), $_POST['character_name']);
                }
            } else {
                if (!check_value($_POST['group'])) throw new Exception('请正确选择分区!');
                if (!check_value($_POST['character_name'])) throw new Exception('请正确输入角色名');
                $group = getGroupIDForServerCode($_POST['group']);

                if (!$character->_checkCharacterExists($group, $_POST['character_name'])) throw new Exception('该角色不存在该大区!');
                $characterData[getGroupIDForServerCode($_POST['group'])] = $character->getCharacterDataForCharacterName(getGroupIDForServerCode($group), $_POST['character_name']);
                if (empty($characterData[getGroupIDForServerCode($_POST['group'])])) throw new Exception('该角色不存在该大区!');
            }
        }catch (Exception $exception){
            message('error',$exception->getMessage());
        }
    }
        ?>
        <div class="card">
            <div class="card-header">搜索</div>
            <div class="card-body mb-3">
                <div class="col-md-12">
                    <form action="" method="post" role="form">
                        <div class="row justify-content-center">
                            <div class="input-group col-md-3 mt-2">
                                <select class="form-control" name="group" id="group">
                                    <option value="">所有大区搜索</option>
                                    <? foreach (getServerGroupList() as $key => $item) {
                                        ?>
                                        <option value="<?=$key?>"><?= $item?></option>
                                    <?
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="input-group col-md-3 mt-2">
                                <input type="text" class="form-control" name="character_name" placeholder="搜索角色名.."
                                       aria-label="搜索角色名..">
                                <span class="input-group-append">
                           <button class="btn btn-primary" type="submit" name="submit" value="Search">Go!</button>
                        </span>
                            </div>
                        </div>
                    </form>
                </div>
                <footer class="blockquote-footer text-right mt-2 mb-2 mr-3  font-14">
                    使用所有大区搜索将消耗一定的资源
                </footer>
            </div>
        </div>
    <?
    if (!empty($characterData)) {
        $data = array_filter($characterData);
        ?>
            <div class="card">
                <div class="card-body">

                    <table id="datatable" class="table text-center">
                        <thead>
                        <tr>
                            <th>账号</th>
                            <th>名称</th>
                            <th>类型</th>
                            <th>等级</th>
                            <th>大师</th>
                            <th>升级点</th>
                            <th>大师点</th>
                            <th>力量</th>
                            <th>敏捷</th>
                            <th>体力</th>
                            <th>智力</th>
                            <th>统率</th>
                            <th>状态</th>
                            <th>PK</th>
                            <th>位置</th>
                            <th>Mu币</th>
                            <th>家族</th>
                            <td>所属大区</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?foreach ($data as $key=>$thisCharacter) {?>
                            <tr>
                                <td><?=$thisCharacter['AccountID']?></td>
                                <td><a href="<?=admincp_base("Character_Edit&group=".$key."&name=".$thisCharacter['Name'])?>"><?=$thisCharacter['Name']?></a></td>
                                <td><?=$thisCharacter['Class']?></td>
                                <td><?=$thisCharacter['cLevel']?></td>
                                <td><?=$thisCharacter['mLevel']?></td>
                                <td><?=$thisCharacter['LevelUpPoint']?></td>
                                <td><?=$thisCharacter['mPoint']?></td>
                                <td><?=$thisCharacter['Strength']?></td>
                                <td><?=$thisCharacter['Dexterity']?></td>
                                <td><?=$thisCharacter['Vitality']?></td>
                                <td><?=$thisCharacter['Energy']?></td>
                                <td><?=$thisCharacter['Leadership']?></td>
                                <td><?=$thisCharacter['CtlCode']?></td>
                                <td><?=$thisCharacter['PkLevel']?></td>
                                <td><?=$thisCharacter['MapNumber']?></td>
                                <td><?=$thisCharacter['Money']?></td>
                                <td><?=$thisCharacter['GenFamily']?></td>
                                <td><?=getGroupNameForGroupID($key)?></td>
                            </tr>
                        <?}?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?}?>
        <?php
    } catch(Exception $ex) {
        message('error', $ex->getMessage());
    }