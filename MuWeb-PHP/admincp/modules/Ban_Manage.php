<?php
/**
 * 文件说明
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
                    <li class="breadcrumb-item active">禁用系统</li>
                    <li class="breadcrumb-item active">封停管理</li>
                </ol>
            </div>
            <h4 class="page-title">封停管理</h4>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">搜索</div>
    <div class="card-body mb-3">
        <div class="col-md-12">
            <form action="" method="post" role="form">
                <div class="row justify-content-center">
                    <div class="input-group col-md-3 mt-2">
                        <input type="text" class="form-control" name="username" placeholder="搜索账号.." aria-label="搜索账号..">
                        <span class="input-group-append">
                           <button class="btn btn-primary" type="submit" name="search_ban" value="Search">Go!</button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<br />
<?php
	$database = Connection::Database("Web");
	
	if(check_value($_POST['username'])) {
		try {
            $common = new common();
			$searchRequest = '%'.$_POST['username'].'%';
			$search = $database->query_fetch("SELECT TOP 25 * FROM ".X_TEAM_BAN_LOG." WHERE account_id LIKE ?", [$searchRequest]);
			if(is_array($search)) {
				echo '<div class="card">';
				echo '<div class="card-header">搜索结果 <span style="color:red;"><i>'.$_POST['username'].'</i></span></div>';
				echo '<div class="row">';
				echo '<div class="col-md-12">';
				echo '<table class="table table-striped table-condensed table-hover text-center">';
					echo '<thead>';
						echo '<tr>';
							echo '<th>封停账号</th>';
							echo '<th>操作用户</th>';
							echo '<th>类型</th>';
							echo '<th>日期</th>';
							echo '<th>天</th>';
							echo '<th>操作</th>';
						echo '</tr>';
					echo '</thead>';
					echo '<tbody>';
					foreach($search as $ban) {

						$banType = ($ban['ban_type'] == "temporal" ? '<span class="label label-default">Temporal</span>' : '<span class="label label-danger">永久</span>');
						echo '<tr>';
							echo '<td><a class="btn btn-outline-danger" href="'.admincp_base("Account_Info&group=".$ban['servercode']."&id=".$common->getUserGIDForUsername(getGroupIDForServerCode($ban['servercode']),$ban['account_id'])).'">'.$ban['account_id'].'</a></td>';
							echo '<td><a class="btn btn-outline-success" href="'.admincp_base("Account_Info&group=".$ban['servercode']."&id=".$common->getUserGIDForUsername(getGroupIDForServerCode($ban['servercode']),$ban['banned_by'])).'">'.$ban['banned_by'].'</a></td>';
							echo '<td>'.$banType.'</td>';
							echo '<td>'.date("Y-m-d H:i", $ban['ban_date']).'</td>';
							echo '<td>'.$ban['ban_days'].'</td>';
							echo '<td><a href="index.php?module=Ban_Latest&liftban='.$ban['id'].'" class="btn btn-danger btn-xs">解除</a></td>';
						echo '</tr>';
					}
					echo '</tbody>';
				echo '</table>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
			} else {
				throw new Exception("未找到");
			}
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
?>

