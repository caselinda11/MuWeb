<?php
/**
 * 语言系统
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
                        <li class="breadcrumb-item active">语言系统</li>
                        <li class="breadcrumb-item active">语言短语</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    语言短语</h4>
            </div>
        </div>
    </div>
<?php
try {
	global $lang;
	if(!is_array($lang)) throw new Exception('语言文件为空!');
    echo '<div class="card">
    <div class="card-body">';
	echo '<table id="datatable" class="table table-condensed table-bordered table-hover table-striped">';
	echo '<thead>';
		echo '<tr>';
			echo '<th>短语名</th>';
			echo '<th>内容</th>';
		echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
		foreach($lang as $phrase => $value) {
		echo '<tr>';
			echo '<td>'.$phrase.'</td>';
			echo '<td>'.htmlspecialchars($value).'</td>';
		echo '</tr>';
		}
	echo '</tbody>';
	echo '</table>';
	echo '</div></div>';
	
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}