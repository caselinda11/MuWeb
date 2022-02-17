<?php
/**
 * 推广页面模块
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

try {
    ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
            <li class="breadcrumb-item active" aria-current="page">玩家排名</li>
            <li class="breadcrumb-item active" aria-current="page">推广排名</li>
        </ol>
    </nav>
	<?php
	$Rankings = new Rankings();
	$Rankings->rankingsMenu();
	loadModuleConfigs('rankings');
	
	if(!mconfig('enable_votes')) throw new Exception('无法加载请求的玩家排名。');
	if(!mconfig('active')) throw new Exception('无法加载请求的玩家排名。');

	$ranking_data = LoadCacheData('rankings_votes.cache');

	if(!is_array($ranking_data)) throw new Exception('暂时没有要显示的排名结果。');
    #搜索功能
    $server = check_value($_GET['servercode']) ? $_GET['servercode'] : "";
    if(check_value($server)) $ranking_data = getArrayKeyForValue($server,$ranking_data,0);
    $name = check_value($_GET['name']) ? $_GET['name'] : "";
    if(check_value($name)) $ranking_data = getArrayKeyForValue($name,$ranking_data,4);
    #分页需求
    $page = ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) ? ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) : 1;
    $pageLength = mconfig('show_page'); //每页显示多少条
    $totalPage = ((count($ranking_data)-1) / $pageLength) >= 1 ? ((count($ranking_data)-1) / $pageLength) : 1 ;    //总页数 (数据总数/每页显示多少条)
    $ranking_data = array_slice($ranking_data,($pageLength*($page-1)));
    $pager = new pagination($totalPage,$page,5,__BASE_URL__.'rankings/votes?servercode='.$server.'&name='.$char_name,2);

	?>
    <div class="card mt-3 mb-3">
        <div class="card-header">推广排名</div>
        <form action="">
            <div class="mt-3">
                <div class="form-group row justify-content-md-center">
                    <div class="col-sm-3" style="padding-left: 5px;padding-right: 5px;">
                        <select class="form-control" name="servercode" id="servercode">
                            <option value="">所有大区</option>
                            <? foreach (getServerGroupList() as $group=> $item){ ?>
                                <option value="<?=$group;?>" <?=selected($_GET['servercode'],(string)$group)?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3" style="padding-left: 5px;padding-right: 5px;">
                        <input type="text" class="form-control" name="name" placeholder="玩家名称" />
                    </div>
                    <div class="" style="padding-left: 5px;padding-right: 5px;">
                        <div class="input-group-prepend">
                            <button class="btn btn-success" type="submit">查找</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="rankings mt-3 mb-3">
<?php
	echo '<table id="ranking" class="table table-hover text-center  table-sm">';
	echo '<thead>';
	echo '<tr>';
        if(mconfig('show_place_number')) {
            echo '<th height="40" style="vertical-align: inherit;">名次</th>';
        }
        echo '<th height="40" style="vertical-align: inherit;">类型</th>';
        echo '<th style="vertical-align: inherit;">角色名</th>';
        echo '<th style="vertical-align: inherit;">推广次数</th>';
        if(mconfig('show_location')) echo '<th style="vertical-align: inherit;">所在地图</th>';
        echo '<th style="vertical-align: inherit;">所属大区</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';

	$i = 0;
	foreach($ranking_data as $rData) {
        if(Validator::UnsignedNumber(mconfig('show_level_group'))) if(mconfig('show_level_group') == $rData[0]) continue;
        if($i>=1) {
            $characterIMG = getPlayerClassAvatar($rData[2], true, true, 'rounded','30');
            $onlineStatus = $rData[5] == 1 ? '<img src="'.__PATH_ONLINE_STATUS__.'" />' : '<img src="'.__PATH_OFFLINE_STATUS__.'" />' ;
            $no = ($i<=3) ? '<img class="ImgTop" src="'.__PATH_PUBLIC_IMG__.'Top/ico_rank'.$i.'.png" width="30" />' : $i;
			echo '<tr>';
			if(mconfig('show_place_number')) {
				echo '<td>'.$no.'</td>';
			}
			echo '<td>'.$characterIMG.'</td>';
			echo '<td>'.playerProfile($rData[0],$rData[1]).$onlineStatus.'</td>';
			echo '<td>'.number_format($rData[4]).'</td>';
			if(mconfig('show_location')) echo '<td>'.getMapName($rData[3]).'</td>';
			echo  '<td>'.getGroupNameForServerCode($rData[0]).'</td>';
			echo '</tr>';
		}

		$i++;
	}
        echo '</tbody>';
	echo '</table>';
	?>
            <div class="mt-3 mb-2">
                <?=$pager->showpager()?>
            </div>
        <?if(mconfig('show_date')) {?>
            <footer class="blockquote-footer text-right mt-3 mb-3 mr-3">
                仅显示排名靠前的前[<strong><?=mconfig('results')?></strong>]条，本页面定时更新，上一次更新时间为
                [<?=date('Y-m-d H:i',filemtime(__PATH_INCLUDES_CACHE__.'rankings_votes.cache'))?>]
            </footer>
        <?}?>
    </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}