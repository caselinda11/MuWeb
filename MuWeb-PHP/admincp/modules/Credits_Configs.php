<?php
/**
 * 货币配置
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
                        <li class="breadcrumb-item active">货币管理</li>
                        <li class="breadcrumb-item active">货币配置</li>
                    </ol>
                </div>
                <h4 class="page-title">货币配置</h4>
            </div>
        </div>
    </div>
<?php
    message('info','如果处于开服运行状态，一旦发布不建议随意删除！','提示: ');
$creditSystem = new CreditSystem();
if(check_value($_POST)) {

    switch ($_POST['submit']){
        case 'news':
            // 新增配置
            try {
                if(!check_value($_POST['title'])) throw new Exception("请填写配置标题!");
                if(!check_value($_POST['database'])) throw new Exception("请选择有效的数据库名!");
                if(!check_value($_POST['table'])) throw new Exception("请填写有效的数据表");
                if(!check_value($_POST['table_column'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['user_column'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['user_column_id'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['buy_link'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['check_online'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['display'])) throw new Exception("请填写所有必填字段.");
                $creditSystem->setConfigTitle($_POST['title']);
                $creditSystem->setConfigDatabase($_POST['database']);
                $creditSystem->setConfigTable($_POST['table']);
                $creditSystem->setConfigCreditsColumn($_POST['table_column']);
                $creditSystem->setConfigUserColumn($_POST['user_column']);
                $creditSystem->setConfigBuyLink($_POST['buy_link']);
                $creditSystem->setConfigUserColumnId($_POST['user_column_id']);
                $creditSystem->setConfigCheckOnline($_POST['check_online']);
                $creditSystem->setConfigDisplay($_POST['display']);
                $creditSystem->saveConfig();

            } catch (Exception $ex) {
                message('error', $ex->getMessage());
            }
        break;
        case 'edit':
            // 编辑配置
            try {
                if(!check_value($_POST['id'])) throw new Exception("id错误,请刷新页面重新选择!");
                if(!check_value($_POST['title'])) throw new Exception("请填写标题");
                if(!check_value($_POST['database'])) throw new Exception("请选择有效的数据库名");
                if(!check_value($_POST['table'])) throw new Exception("请填写指定的表");
                if(!check_value($_POST['table_column'])) throw new Exception("请填写指定的列");
                if(!check_value($_POST['user_column'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['user_column_id'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['buy_link'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['check_online'])) throw new Exception("请填写所有必填字段.");
                if(!check_value($_POST['display'])) throw new Exception("请填写所有必填字段.");

                $creditSystem->setConfigId($_POST['id']);
                $creditSystem->setConfigTitle($_POST['title']);
                $creditSystem->setConfigDatabase($_POST['database']);
                $creditSystem->setConfigTable($_POST['table']);
                $creditSystem->setConfigCreditsColumn($_POST['table_column']);
                $creditSystem->setConfigUserColumn($_POST['user_column']);
                $creditSystem->setConfigUserColumnId($_POST['user_column_id']);
                $creditSystem->setConfigBuyLink($_POST['buy_link']);
                $creditSystem->setConfigCheckOnline($_POST['check_online']);
                $creditSystem->setConfigDisplay($_POST['display']);
                $creditSystem->editConfig();
            } catch (Exception $ex) {
                message('error', $ex->getMessage());
            }
            break;
        default:
            message('error',"请勿非法提交!");
            break;
    }

}
// 删除配置
if(check_value($_GET['delete'])) {
	try {
		$creditSystem->setConfigId($_GET['delete']);
		$creditSystem->deleteConfig();
	} catch (Exception $ex) {
		message('error', $ex->getMessage());
	}
}
?>
        <div class="row">
	        <div class="col-lg-6">
<?php
        # 添加新的配置
		if(!check_value($_GET['edit'])) {
            ?>
            <div class="card">
			<div class="card-header">创建</div>
			<div class="card-body">
				<form role="form" action="<?=admincp_base("Credits_Configs");?>" method="post">
					<div class="form-group">
						<label for="title">货币名称:</label><span class="text-muted"> 例如: 积分 / 元宝</span>
						<input type="text" class="form-control" id="title" name="title"/>
					</div>
                        <label for="database">数据库类型:<span class="text-muted"> 使用了[Me_MuOnline]则选择[Me_MuOnline]，没有使用则选择MuOnline</span></label>
                        <div class="form-group">
                            <div class="">
                                <select name="database" id="database" class="form-control">
                                    <option value="MuOnline">MuOnline</option>
                                    <option value="Me_MuOnline">Me_MuOnline</option>
                                </select>
                            </div>
                        </div>

					<div class="form-group">
						<label for="table">数据表: </label><span class="text-muted"> 货币所使用的表名,一般是[MEMB_INFO],IGC的是[T_InGameShop_Point]</span>
						<input type="text" class="form-control" id="table" name="table" placeholder="MEMB_INFO / T_InGameShop_Point"/>
					</div>

					<div class="form-group">
						<label for="table_column">币种字段:</label><span class="text-muted"> 基于数据表中的列名,一般是[JF],[YB],[cspoints],[WCoin],[GoblinPoint]</span>
						<input type="text" class="form-control" id="table_column" name="table_column" placeholder="JF/ YB / cspoints / WCoin / GoblinPoint"/>
					</div>

					<div class="form-group">
						<label for="user_column">账号字段:</label><span class="text-muted"> 基于数据表中的账号字段,一般是[memb___id],[AccountID]</span>
						<input type="text" class="form-control" id="user_column" name="user_column" placeholder="memb___id / AccountID"/>
					</div>

					<label for="user_column_id">字段标识符:</label><span class="text-muted"> 用于充值的条件索引</span>
                        <div class="form-group">
                            <div class="">
                                <select name="user_column_id" id="user_column_id" class="form-control">
                                    <option value="userid">ID</option>
                                    <option value="username" selected>账号</option>
                                    <option value="character">角色</option>
                                </select>
                            </div>
                        </div>
                     <div class="form-group">
						<label for="buy_link">充值链接:</label><span class="text-muted"> 用于显示在个人面板中的链接</span>
						<input type="text" class="form-control" id="buy_link" name="buy_link" placeholder="例如:http://www.baidu.com"/>
					</div>

					<div class="form-group">
					<label class="mr-4">检查状态:</label>
					    <?=enableDisableCheckboxes('check_online',1,'是','否')?>
					    <span class="text-muted">每次操作是否检测账号是否在线</span>
                    </div>
					<div class="form-group">
					<label class="mr-4">面板显示:</label>
					    <?=enableDisableCheckboxes('display',1,'是','否')?>
					    <span class="text-muted"> 是否展示在玩家个人账号面板中</span>
					</div>

					<button type="submit" name="submit" value="news" class="btn btn-success col-md-12">添加</button>
				</form>

			</div>
			</div>

			<?php
		} else {
			// 编辑
			$creditSystem->setConfigId($_GET['edit']);
			$configData = $creditSystem->showConfigs(true);
?>
            <div class="card">
                <div class="card-header"><strong>编辑</strong></div>
                <div class="card-body">
                    <form role="form" action="<?=admincp_base("Credits_Configs"); ?>" method="post">
                        <input type="hidden" name="id" value="<?=$configData['config_id']; ?>"/>
                        <div class="form-group">
                            <label for="title">货币名称:</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?=$configData['config_title']; ?>"/>
                        </div>

                        <label for="database">数据库:</label>
                        <div class="form-group">
                            <div class="">
                                <select name="database" id="database" class="form-control">
                                <? $selected = $configData['config_database'] == 'Me_MuOnline' ? 'selected' : ''; ?>
                                    <option value="MuOnline" >MuOnline</option>
                                    <option value="Me_MuOnline" <?=$selected?>>Me_MuOnline</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="table">数据库表名:</label>
                            <input type="text" class="form-control" id="table" name="table" value="<?=$configData['config_table']; ?>"/>
                        </div>

                        <div class="form-group">
                            <label for="table_column">币种列名:</label>
                            <input type="text" class="form-control" id="table_column" name="table_column" value="<?=$configData['config_credits_col']; ?>"/>
                        </div>

                        <div class="form-group">
                            <label for="user_column">用户列名:</label>
                            <input type="text" class="form-control" id="user_column" name="user_column" value="<?=$configData['config_user_col']; ?>"/>
                        </div>
                        <label for="user_column_id">用户标识:</label>
                        <div class="form-group">
                                <select name="user_column_id" id="user_column_id" class="form-control">
                                    <option value="userid" <?if($configData['config_user_col_id'] == "userid") echo 'selected'?>>ID</option>
                                    <option value="username" <?if($configData['config_user_col_id'] == "username") echo 'selected'?>>账号</option>
                                    <option value="character" <?if($configData['config_user_col_id'] == "character") echo 'selected'?>>角色</option>
                                </select>
                        </div>
                    <div class="form-group">
						<label for="buy_link">充值链接:</label><span class="text-muted"> 用于显示在个人面板中的链接</span>
						<input type="text" class="form-control" id="buy_link" name="buy_link" value="<?=$configData['config_buy_link']; ?>"/>
					</div>
					<div class="form-group">
					<label class="mr-4">检查状态:</label>
					    <?=enableDisableCheckboxes('check_online',$configData['config_checkonline'],'是','否')?>

                    </div>
					<div class="form-group">
					<label class="mr-4">面板显示:</label>
					    <?=enableDisableCheckboxes('display',$configData['config_display'],'是','否')?>
					</div>
                        <button type="submit" name="submit" value="edit" class="btn btn-success col-md-12">编辑</button>
                    </form>
                </div>
            </div>
<?php
		}
?>
	</div>
	<div class="col-lg-6">
<?php

		$configList = $creditSystem->showConfigs();
		if(is_array($configList)) {
			foreach($configList as $data) {
				$checkOnline = ($data['config_checkonline'] ? '<span class="label label-success">是</span>' : '<span class="label label-default">否</span>');
				$configDisplay = ($data['config_display'] ? '<span class="label label-success">是</span>' : '<span class="label label-default">否</span>');

?>
                <div class="card">
                    <div class="card-header">
                        <span class="mt-0 header-title"><?=$data['config_title']; ?></span>
                        <span class="float-right button-items">
						<a href="<?=admincp_base("Credits_Configs&delete=".$data['config_id']); ?>" class="btn btn-danger"><span class="ti-trash"></span> 删除</a>
						<a href="<?=admincp_base("Credits_Configs&edit=".$data['config_id']); ?>" class="btn btn-info"><span class="ti-pencil"></span> 编辑</a>
					    </span>
                    </div>
                    <div class="card-body">

                        <table class="table" style="margin-bottom:0px;">
                            <tbody>
                            <tr>
                                <th>配置ID</th>
                                <td><?=$data['config_id']; ?></td>
                                <th>账号标识</th>
                                <td><?=$data['config_user_col_id']; ?></td>
                            </tr>
                            <tr>
                                <th>数据库类型</th>
                                <td><?=$data['config_database']; ?></td>
                                <th>校验在线</th>
                                <td><?=$checkOnline; ?></td>
                            </tr>
                            <tr>
                                <th>数据表</th>
                                <td><?=$data['config_table']; ?></td>
                                <th>面板显示</th>
                                <td><?=$configDisplay; ?></td>
                            </tr>
                            <tr>
                                <th>币种列名</th>
                                <td><?=$data['config_credits_col']; ?></td>
                                <th>用户列名</th>
                                <td><?=$data['config_user_col']; ?></td>
                            </tr>
                            <tr>
                                <th>充值链接</th>
                                <td colspan="3"><a href="<?=$data['config_buy_link']; ?>"><?=$data['config_buy_link']; ?></a></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
<?php   }
			?>
</div>
            <?php
		} else {
			message('warning', '您尚未创建任何配置.');
		}
		

