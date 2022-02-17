<?php
/**
 * 财富排名插件模块页面
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
            <li class="breadcrumb-item active" aria-current="page">封停排名</li>
        </ol>
    </nav>
    <?php
    $ban = new \Plugin\Rankings\ban();
    $mconfig = $ban->loadConfig();
    $Rankings = new Rankings();
    $Rankings->rankingsMenu();
    loadModuleConfigs('rankings');
    if(!$mconfig['active']) throw new Exception('该功能暂已关闭，请稍后尝试访问。');

    ?>
    <div class="card mt-3 mb-3">
        <div class="card-header">封停排名</div>
        <?php
        #获取角色数据
        $ranking_data = LoadCacheData('rankings_ban.cache');
        if(!is_array($ranking_data)) throw new Exception('暂无数据!');
        #搜索功能
        $server = check_value($_GET['servercode']) ? $_GET['servercode'] : "";
        if(check_value($server)) $ranking_data = getArrayKeyForValue($server,$ranking_data,0);
        $name = check_value($_GET['name']) ? $_GET['name'] : "";
        if(check_value($name)) $ranking_data = getArrayKeyForValue($name,$ranking_data,3);
        #分页需求
        $page = ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) ? ($_GET['pagination'] ? intval($_REQUEST['pagination']) : 1) : 1;
        $pageLength = mconfig('show_page'); //每页显示多少条
        $totalPage = ((count($ranking_data)-1) / $pageLength) >= 1 ? ((count($ranking_data)-1) / $pageLength) : 1 ;    //总页数 (数据总数/每页显示多少条)
        $ranking_data = array_slice($ranking_data,($pageLength*($page-1)));
        $pager = new pagination($totalPage,$page,5,__BASE_URL__.'rankings/ban?servercode='.$server.'&name='.$char_name,2);
        ?>
        <form action="">
            <div class="mt-3">
                <div class="form-group row justify-content-md-center">
                    <div class="col-sm-3" style="padding-left: 5px;padding-right: 5px;">
                        <select class="form-control" name="servercode" id="servercode">
                            <option value="">所有大区</option>
                            <? foreach (getServerGroupList() as $code=>$item){?>
                                <option value="<?=$code?>" <?=selected($_GET['servercode'],(string)$code)?>><?=$item?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="col-sm-3" style="padding-left: 5px;padding-right: 5px;">
                        <input type="text" class="form-control" name="name" placeholder="角色名称" />
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

            <table id="" class="table table-striped table-bordered table-hover text-center table-sm">
                <thead>
                    <tr>
                        <?if(mconfig('show_place_number')) {?>
                        <th height="40" style="vertical-align: inherit;">名次</th>
                        <?}?>
                        <th style="vertical-align: inherit;">类型</th>
                        <th style="vertical-align: inherit;">玩家名称</th>
                        <th style="vertical-align: inherit;">账号</th>
                        <th style="vertical-align: inherit;">等级</th>
                        <th style="vertical-align: inherit;">状态</th>
                        <th style="vertical-align: inherit;">所属大区</th>
                    </tr>
                </thead>
                <tbody>
                <?if (empty($ranking_data)){?><tr><td colspan="10"><strong>暂无数据</strong></td></tr><?}else{?>
                    <?$i = ($pageLength*($page-1))+1;?>
                    <?foreach ($ranking_data as $key=> $data){?>
                        <?$characterIMG = getPlayerClassAvatar($data[4], true, true, 'rounded','30');?>
                        <?$Status = "<span class='text-danger'>永久封停</span>"?>
                        <?if($key == $pageLength) break;?>
                        <tr>
                            <?if(mconfig('show_place_number')) {?>
                            <td><?=$i?></td>
                            <?}?>
                            <td><?=$characterIMG?></td>
                            <td><?=playerProfile($data[0],$data[3])?></td>
                            <td><?=substr($data[1],0,-4)?>**<?=substr($data[1],-2)?></td>
                            <td><?=$data[5]?></td>
                            <td><?=$Status?></td>
                            <td><?=getGroupNameForServerCode($data[0])?></td>
                        </tr>
                    <?$i++;}?>
                <?}?>
                </tbody>
            </table>
            <div class="mt-3 mb-2"><?=$pager->showpager()?></div>

            <?if(mconfig('show_date')) {?>
                <footer class=" text-right mt-3 mb-3 mr-3">
                    仅显示排名靠前的前[<strong><?=mconfig('results')?></strong>]条，本页面定时更新，上一次更新时间为
                    [<?=date('Y-m-d H:i',filemtime(__PATH_INCLUDES_CACHE__.'rankings_ban.cache'))?>]
                </footer>
            <?}?>
        </div>
    </div>
<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}