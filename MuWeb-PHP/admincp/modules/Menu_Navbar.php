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
                        <li class="breadcrumb-item"><a href="<?=admincp_base()?>">官方主页</a></li>
                        <li class="breadcrumb-item active">网站设置</li>
                        <li class="breadcrumb-item active">导航菜单</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    导航菜单</h4>
            </div>
        </div>
    </div>
<?php
try {
	
	if(check_value($_GET['delete'])) {
		try {
			# 配置
			$newCfg = loadConfig('navbar');
			if(!is_array($newCfg)) throw new Exception('导航栏配置为空。');
			
			if(!check_value($_GET['delete'])) throw new Exception('无效ID');
			if(!array_key_exists($_GET['delete'], $newCfg)) throw new Exception('无效ID');
			
			unset($newCfg[$_GET['delete']]);
			
			# 转码
			$navbarJson = json_encode($newCfg, JSON_PRETTY_PRINT);
			
			# 保持更改
			$cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'navbar.json', 'w');
			if(!$cfgFile) throw new Exception('打开导航栏文件时出现问题。');
			fwrite($cfgFile, $navbarJson);
			fclose($cfgFile);
			
			message('success', '更改已成功保存!');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	if(check_value($_POST['navbar_submit'])) {
		try {
			# cfg
			$newCfg = loadConfig('navbar');
			if(!is_array($newCfg)) throw new Exception('导航栏配置为空。');
			
			if(!check_value($_POST['navbar_id'])) throw new Exception('请填写所有表单字段。');
			if(!check_value($_POST['navbar_type'])) throw new Exception('请填写所有表单字段。');
			if(!check_value($_POST['navbar_phrase'])) throw new Exception('请填写所有表单字段。');
			if(!in_array($_POST['navbar_type'], array('internal','external'))) throw new Exception('链接类型无效。');
			if(!in_array($_POST['navbar_visibility'], array('user','guest','always'))) throw new Exception('链接可见性不是有效的选项。');
			
			$elementId = $_POST['navbar_id'];
			
			# build new element data array
			$newElementData = [
				'active' => (bool) ($_POST['navbar_status'] == 1 ? true : false),
				'type' => $_POST['navbar_type'],
				'phrase' => $_POST['navbar_phrase'],
                'tag'   => $_POST['navbar_tag'],
				'link' => (check_value($_POST['navbar_link']) ? $_POST['navbar_link'] : ''),
				'visibility' => $_POST['navbar_visibility'],
				'newtab' => (bool) ($_POST['navbar_newtab'] == 1 ? true : false),
				'order' => (int) $_POST['navbar_order']
			];
			
			# modify navbar array
			$newCfg[$elementId] = $newElementData;
			
			# sort by order
			# http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
			usort($newCfg, function($a, $b) {
				return $a['order'] - $b['order'];
			});
			
			# encode
			$navbarJson = json_encode($newCfg, JSON_PRETTY_PRINT);
			
			# save changes
			$cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'navbar.json', 'w');
			if(!$cfgFile) throw new Exception('打开导航栏文件时出现问题!');
			fwrite($cfgFile, $navbarJson);
			fclose($cfgFile);
			
			message('success', '更改已成功保存!');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	if(check_value($_POST['new_submit'])) {
		try {
			# cfg
			$newCfg = loadConfig('navbar');
			if(!is_array($newCfg)) throw new Exception('导航栏配置为空!');
			
			if(!check_value($_POST['navbar_type'])) throw new Exception('请填写所有表单字段。');
			if(!check_value($_POST['navbar_phrase'])) throw new Exception('请填写所有表单字段。');
			if(!in_array($_POST['navbar_type'], array('internal','external'))) throw new Exception('链接类型无效。');
			if(!in_array($_POST['navbar_visibility'], array('user','guest','always'))) throw new Exception('链接可见性不是有效的选项。');
			
			# build new element data array
			$newElementData = array(
				'active' => (bool) ($_POST['navbar_status'] == 1 ? true : false),
				'type' => $_POST['navbar_type'],
				'phrase' => $_POST['navbar_phrase'],
				'tag'   => $_POST['navbar_tag'],
				'link' => (check_value($_POST['navbar_link']) ? $_POST['navbar_link'] : ''),
				'visibility' => $_POST['navbar_visibility'],
				'newtab' => (bool) ($_POST['navbar_newtab'] == 1 ? true : false),
				'order' => (int) $_POST['navbar_order']
			);
			
			# modify navbar array
			$newCfg[] = $newElementData;
			
			# sort by order
			# http://stackoverflow.com/questions/2699086/sort-multi-dimensional-array-by-value
			usort($newCfg, function($a, $b) {
				return $a['order'] - $b['order'];
			});
			
			# encode
			$navbarJson = json_encode($newCfg, JSON_PRETTY_PRINT);
			
			# save changes
			$cfgFile = fopen(__PATH_INCLUDES_CONFIGS__.'navbar.json', 'w');
			if(!$cfgFile) throw new Exception('打开导航栏文件时出现问题。');
			fwrite($cfgFile, $navbarJson);
			fclose($cfgFile);
			
			message('success', '导航栏已成功更新!');
		} catch(Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
	
	$cfg = loadConfig('navbar');
	if(!is_array($cfg)) throw new Exception('导航栏配置为空!');
	echo '<div class="card">';
    echo '<div class="card-header">前端导航设置 <a href="" data-toggle="modal" data-target="#myModal" class="text-danger"><i class="dripicons-question"></i>功能说明</a></div>';
        echo '<div class="card-body">';
	echo '<table class="table table-condensed table-bordered table-hover table-striped text-center">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>排序</th>';
			echo '<th width="100">状态</th>';
			echo '<th>链接类型</th>';
			echo '<th>链接</th>';
			echo '<th>标题</th>';
			echo '<th>标签</th>';
			echo '<th width="120">可见度</th>';
			echo '<th width="90">新窗口</th>';
			echo '<th>操作</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
		foreach($cfg as $id => $navbarElement) {
			echo '<form action="?module=Menu_Navbar" method="post">';
			echo '<tr>';
				echo '<td style="max-width:70px;"><input type="text" name="navbar_order" class="form-control" value="'.$navbarElement['order'].'"/></td>';
				echo '<td>';
                    echo '<select name="navbar_status" class="form-control">';
                        echo '<option value="1" '.($navbarElement['active'] ? 'selected' : '').'>显示</option>';
                        echo '<option value="0" '.(!$navbarElement['active'] ? 'selected' : '').'>隐藏</option>';
                    echo '</select>';
				echo '</td>';
				echo '<td>';
					echo '<select name="navbar_type" class="form-control">';
						echo '<option value="internal" '.($navbarElement['type'] == 'internal' ? 'selected' : '').'>内部</option>';
						echo '<option value="external" '.($navbarElement['type'] == 'external' ? 'selected' : '').'>外部</option>';
					echo '</select>';
				echo '</td>';
				echo '<td><input type="text" name="navbar_link" class="form-control" value="'.$navbarElement['link'].'"/></td>';
				echo '<td><input type="text" name="navbar_phrase" class="form-control" value="'.$navbarElement['phrase'].'"/></td>';
				echo '<td><input type="text" name="navbar_tag" class="form-control" value="'.$navbarElement['tag'].'"/></td>';
				echo '<td>';
					echo '<select name="navbar_visibility" class="form-control">';
						echo '<option value="user" '.($navbarElement['visibility'] == 'user' ? 'selected' : '').'>仅用户</option>';
						echo '<option value="guest" '.($navbarElement['visibility'] == 'guest' ? 'selected' : '').'>仅访客</option>';
						echo '<option value="always" '.($navbarElement['visibility'] == 'always' ? 'selected' : '').'>所有人</option>';
					echo '</select>';
				echo '</td>';
				echo '<td>';
                echo '<select name="navbar_newtab" class="form-control">';
                    echo '<option value="1" '.($navbarElement['newtab'] ? 'selected' : '').'>是</option>';
                    echo '<option value="0" '.(!$navbarElement['newtab'] ? 'selected' : '').'>否</option>';
                echo '</select>';
				echo '</td>';
				echo '<td>';
                        echo '<input type="hidden" name="navbar_id" value="'.$id.'"/>';
                        echo '<div class="btn-group">';
                        echo '<button type="submit" name="navbar_submit" value="ok" class="btn btn-primary"><span class="ti-pencil"></span> 保存</button>';
                        echo '<a href="?module=Menu_Navbar&delete='.$id.'" class="btn btn-danger btn-xs"><span class="ti-trash"></span> 删除</a>';
                        echo '</div>';
                        echo '</td>';
			echo '</tr>';
			echo '</form>';
		}
		
		# 添加新的菜单
		echo '<form action="?module=Menu_Navbar" method="post">';
		echo '<tr><th colspan="9" class="text-center"><strong>添加新的导航链接</strong></th></tr>';
		echo '<tr>';
			echo '<td style="max-width:70px;"><input type="text" name="navbar_order" class="form-control" value="10"/></td>';
			echo '<td>';
                echo '<select class="form-control"  name="navbar_status">';
                echo '<option value="1">显示</opion>';
                echo '<option value="0">隐藏</opion>';
                echo '</select>';
			echo '</td>';
			echo '<td>';
				echo '<select name="navbar_type" class="form-control">';
					echo '<option value="internal" selected>内部</option>';
					echo '<option value="external">外部</option>';
				echo '</select>';
			echo '</td>';
			echo '<td><input type="text" name="navbar_link" class="form-control" placeholder="contact"/></td>';
			echo '<td><input type="text" name="navbar_phrase" class="form-control" placeholder="标题"/></td>';
			echo '<td><input type="text" name="navbar_tag" class="form-control" placeholder="标签"/></td>';
			echo '<td>';
				echo '<select name="navbar_visibility" class="form-control">';
					echo '<option value="user" selected>仅用户</option>';
					echo '<option value="guest">仅访客</option>';
					echo '<option value="always">所有人</option>';
				echo '</select>';
			echo '</td>';
			echo '<td>';
                echo '<select name="navbar_newtab"  class="form-control">';
                    echo '<option value="1"> 是</option>';
                    echo '<option value="0"> 否</option>';
                echo '</select>';
			echo '</td>';
			echo '<td><button type="submit" name="new_submit" value="ok" class="btn btn-success col-md-12">添加</button></td>';
		echo '</tr>';
		echo '</form>';
	echo '</tbody>';
	echo '</table>';
	echo '</div></div>';
?>
    <!--modal-content -->
    <div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myModalLabel">功能说明</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h6 class="mt-0">此页面功能为前台顶部的导航菜单栏。</h6>
                    <p>
                        <strong>排序</strong>：用于整理菜单链接按钮的前后顺序。<br>
                        <strong>状态</strong>：隐藏或显示该菜单链接按钮。<br>
                        <strong>链接类型</strong>：内部-表示访问站内的文件地址，外部-表示访问站外的一些链接地址。<br>
                        <strong>链接</strong>：链接访问的文件名。<br>
                        <strong>标题</strong>：链接标题。<br>
                        <strong>可见度</strong>：规定该菜单链接按钮谁可能看到。<br>
                        <strong>新窗口</strong>：是否弹出一个新的窗口或在原有的窗口中访问。<br>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}