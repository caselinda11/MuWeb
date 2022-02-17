<?php
/**
 * 侧边栏
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

#读取侧边栏配置文件
$sidebarConfig = gconfig('sidebar');     //加载侧边栏配置文件
?>


<!--    # 登录模块-->
<? try{
    if($sidebarConfig['login_sidebar'] && !isLoggedIn()) {?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                账号登录
                <a href="<?=__BASE_URL__;?>forgotpassword" class="more" title="找回密码">
                    忘记密码?
                </a>
            </div>
            <div class="card-body">
                <form action="<?=__BASE_URL__;?>login" method="post">
                    <div class="form-group">
                        <select class="form-control" name="group" id="group">
                            <?foreach (getServerGroupList() as $group=> $item){?>
                                <option value="<?=$group;?>"><?=$item;?></option>
                            <?}?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="user" name="user" placeholder="游戏账号" required>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pwd" name="pwd" placeholder="游戏密码" required autocomplete="off">
                    </div>
                    <?$regConfig = loadConfigurations('register');
                    if($regConfig['register_enable_sno__numb']) {?>
                        <div class="form-group">
                            <input type="password" class="form-control" id="levelPassword" name="levelPassword" placeholder="二级密码" minlength="7" maxlength="7" required>
                        </div>
                    <?}?>
                    <input type="hidden" name="key" value="<?=Token::generateToken('login_sidebar')?>"/>
                    <button type="submit" name="submit" value="submit" class="btn btn-success col-md-12">登录</button>
                </form>
            </div>
        </div>
    <?}
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!--    #用户面板-->
<? try {
    if ($sidebarConfig['usercp_sidebar'] && isLoggedIn()) { ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                个人面板
                <a href="<?= __BASE_URL__?>logout" class="more">
                    退出登录
                </a>
            </div>
            <?=templateBuildUsercp()?>
        </div>
        <?
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!--    # 服务器信息模块-->
<? try{
    if($sidebarConfig['count_sidebar']){
        $srvInfoCache = LoadCacheData('server_info.cache');    //加载数据文件
        if(is_array($srvInfoCache)) {?>
            <div class="card mb-3">
                <div class="card-header">
                    服务器信息
                </div>
                <table class="table table-hover table-striped text-center" style="margin-bottom: 0">
                    <tr><td width="50%">总账号</td><td style="font-weight:bold;"><?=number_format($srvInfoCache[0][0]*1);?></td></tr>
                    <tr><td>总角色</td><td style="font-weight:bold;"><?=number_format($srvInfoCache[0][1]*1);?></td></tr>
                    <tr><td>总战盟</td><td style="font-weight:bold;"><?=number_format($srvInfoCache[0][2]*1);?></td></tr>
                    <tr><td>总在线</td><td style="color:#00aa00;font-weight:bold;"><?=number_format($srvInfoCache[0][3]*1);?></td></tr>
                    <?
                    $onlineDays = 1; // 边栏服务器在线天数开关
                    if($onlineDays){//如果$onlineDays为真
                        $sod['onlineSince'] = $sidebarConfig['open_time']; // YYYY-MM-DD
                        $sod['onlineDays'] = floor((time()-strtotime($sod['onlineSince']))/86400);
                        ?>
                        <tr><td colspan="2" style="text-align:center;"><?=config('server_name')?>已稳定运行<font style="color:#00aa00;font-weight:bold;"> <?=$sod['onlineDays'];?> </font>天</td></tr>
                    <?}?>
                </table>
            </div>
        <?}
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- QQ 按钮-->
<? try{
    if($sidebarConfig['qq_link_sidebar']){
        ?>
        <div class="mb-3">
            <a href="//wpa.qq.com/msgrd?v=3&uin=<?=config('QQ')?>&site=qq&menu=yes" class="btn-social-counter qq" target="_blank">
                <div class="btn-social-counter__icon">
                    <i class="fa fa-qq"></i>
                </div>
                <h6 class="btn-social-counter__title">联系在线客服</h6>
                <span class="btn-social-counter__count">
                <span class="btn-social-counter__count-num"></span>
                 联系在线客服任何问题随时解答
            </span>
                <span class="btn-social-counter__add-icon"></span>
            </a>
        </div>
    <?  }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- QQ群 按钮-->
<? try{
    if($sidebarConfig['qqun_link_sidebar']){?>
        <div class="mb-3">
            <a href="<?=config('QQUN')?>" class="btn-social-counter qqQun" target="_blank">
                <div class="btn-social-counter__icon">
                    <i class="fa fa-wechat"></i>
                </div>
                <h6 class="btn-social-counter__title">加入玩家交流群</h6>
                <span class="btn-social-counter__count">
                <span class="btn-social-counter__count-num"></span>
                 加入玩家交流群与我们一起互动
            </span>
                <span class="btn-social-counter__add-icon"></span>
            </a>
        </div>
    <?  }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- 罗兰城主信息 -->
<? try{
    if($sidebarConfig['cc_sidebar']) {
        $ranking_data = loadCache('castle_siege.cache');    //加载数据文件
        $cs = cs_CalculateTimeLeft();
        $timeLeft = sec_to_dhms($cs);
        if (!empty($ranking_data)){?>
            <?if(is_array($ranking_data['castle'])) {?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        罗兰信息
                        <a href="<?=__BASE_URL__;?>castlesiege" class="more" title="查看更多">&nbsp;＋&nbsp;</a>
                    </div>
                    <div id="castleSiege" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#castleSiege" data-slide-to="0" class="active"></li>
                            <?if(is_array($ranking_data['castle'])) {
                                for($i=1;$i<count($ranking_data['castle']);$i++){?>
                                    <li data-target="#castleSiege" data-slide-to="<?=$i?>"></li>
                                <?}
                            }?>
                        </ol>
                        <div class="carousel-inner">
                            <?	if(is_array($ranking_data['castle'])) {
                                foreach ($ranking_data['castle'] as $code=>$cData){
                                    $active = $code ?  'active' : '';
                                    $logo = getGuildLogo($cData[_CLMN_GUILD_LOGO_], 130,"d-block");
                                    $guild = guildProfile(getServerCodeForGroupID($code),$cData[_CLMN_MCD_GUILD_OWNER_]);
                                    $master = playerProfile(getServerCodeForGroupID($code),$cData[_CLMN_GUILD_MASTER_]);
                                    ?>
                                    <div class="carousel-item <?=$active?>">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-5 align-content-center align-self-center d-none d-lg-block visible-lg-block">
                                                    <?=$logo;?>
                                                </div>
                                                <div class="col-md-7 text-center">
                                                    <table class="table table-sm text-center">
                                                        <tr><td>大区</td><td><?=getGroupNameForGroupID($code)?></td></tr>
                                                        <tr><td>战盟</td><td>[<?=guildProfile(getServerCodeForGroupID($code),$guild)?>]</td></tr>
                                                        <tr><td>盟主</td><td><?=playerProfile(getServerCodeForGroupID($code),$master)?></td></tr>
                                                        <tr><td colspan="2">下一次攻城时间</td></tr>
                                                    </table>
                                                    <div id="csCountDown">
                                                        <?if($cs > 86400) echo $timeLeft[0];?>天
                                                        <?if($cs > 3600) echo $timeLeft[1];?>时
                                                        <?if($cs > 60) echo $timeLeft[2];?>秒
                                                        <?=$timeLeft[3]?>分
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?}
                            }?>
                        </div>
                        <a class="carousel-control-prev" href="#castleSiege" role="button" data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">上</span>
                        </a>
                        <a class="carousel-control-next" href="#castleSiege" role="button" data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">下</span>
                        </a>
                    </div>
                </div>
            <?}}
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- 冰风谷堡主信息-->
<? try{
    if($sidebarConfig['bfk_sidebar']) {
        #冰风谷霸主
        $BFB = loadCache('BFB_info.cache');    //加载数据文件
        if(is_array($BFB)){
            ?>
            <div class="card mb-3">
                <div class="card-header">冰风谷城主信息</div>
                <div class="card-body">
                    <div id="fbKInfo" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            <li data-target="#fbKInfo" data-slide-to="0" class="active"></li>
                            <?if(is_array($BFB)) {
                                for($i=1;$i<count($BFB);$i++){?>
                                    <li data-target="#fbKInfo" data-slide-to="<?=$i?>"></li>
                                <?}
                            }?>
                        </ol>
                        <div class="carousel-inner">
                            <?
                            foreach ($BFB as $code=>$data){
                                $active = $code ?  '' : 'active';
                                $logo = getGuildLogo($data[3], 130,"d-block");
                                $group = getGroupNameForGroupID($data[0]);
                                $guild = guildProfile(getServerCodeForGroupID($code),$data[2]);
                                $master = playerProfile(getServerCodeForGroupID($code),$data[1]);
                                ?>
                                <div class="carousel-item <?=$active?>" data-interval="10000">
                                    <div class="row">
                                        <div class="col-md-5 align-content-center align-self-center d-none d-lg-block visible-lg-block">
                                            <?=$logo;?>
                                        </div>
                                        <div class="col-md-7 text-center">
                                            <table class="table table-sm text-center">
                                                <th colspan="3">冰风谷霸主</th>
                                                <tr><td>大区</td><td><?=$group?></td></tr>
                                                <tr><td>战盟</td><td><?=$guild?></td></tr>
                                                <tr><td>城主</td><td><?=$master?></td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?}?>
                            <a class="carousel-control-prev" href="#fbKInfo" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#fbKInfo" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?}
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- 等级排名模块 -->
<? try {
    if($sidebarConfig['level_ranking_sidebar']){
        $topLevelLimit = 5; //显示排名多少条
        $levelRankingData = LoadCacheData('rankings_level.cache');    //加载数据文件
        if(!empty($levelRankingData)) {
            $topLevel = array_slice($levelRankingData, 0, $topLevelLimit);
            ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    角色排名
                    <a href="<?=__BASE_URL__;?>rankings/level" class="more" title="查看更多">&nbsp;＋&nbsp;</a>
                </div>
                <table class="table table-hover table-striped text-center">
                    <tr>
                        <th>角色</th>
                        <th>玩家</th>
                        <th>等级<sup style="color: red;">大师</sup></th>
                    </tr>
                    <?
                    foreach($topLevel as $key => $row) {
                        #两位数自动左边补0;
                        $no = str_pad($key+1,2,'0',STR_PAD_LEFT);
                        ?>
                        <tr>
                            <td style="padding: 0.15rem;">
                                <div style="background: url('<?=__PATH_PUBLIC_IMG__?>Top/rank_<?=$no?>.png')no-repeat;width: 40px;height: 40px;margin: 0 auto;background-size: cover;padding: 8px;">
                                    <div style="margin-top:1px">
                                        <?=getPlayerClassAvatar($row[5], true,true,'rounded-circle','25');?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.15rem;"><?=playerProfile($row[0],$row[2])?></td>
                            <td style="padding: 0.15rem;"><?=$row[3]?><sup style="color: red;"><?=$row[16]?></sup></td>
                        </tr>
                    <?}?>
                </table>
            </div>
        <?}
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- 家族排名模块 -->
<? try {
    if($sidebarConfig['gens_ranking_sidebar']){
        $topLevelLimit = 5; //显示排名多少
        $ranking_data = LoadCacheData('rankings_gens.cache');    //加载数据文件
        if(is_array($ranking_data)) {
            $ranking_data = array_slice($ranking_data, 0, $topLevelLimit);
            ?>
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    家族排名
                    <a href="<?=__BASE_URL__;?>rankings/gens" class="more" title="查看更多">&nbsp;＋&nbsp;</a>
                </div>
                <table class="table table-hover table-striped text-center">
                    <tr>
                        <th>角色</th>
                        <th>玩家</th>
                        <th>家族<sup style="color: red;">称号</sup></th>
                    </tr>
                    <? foreach($ranking_data as $key => $row) {
                        $no = str_pad((int)$key+1,2,'0',STR_PAD_LEFT); ?>
                        <tr>
                            <td style="padding: 0.15rem;">
                                <div style="background: url('<?=__PATH_PUBLIC_IMG__?>Top/rank_<?=$no?>.png')no-repeat;width: 40px;height: 40px;margin: 0 auto;background-size: cover;padding: 8px;">
                                    <div style="margin-top:1px">
                                        <?=getPlayerClassAvatar($row[4], true,true,'rounded-circle','25');?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.15rem;"><?=playerProfile($row[0],$row[2])?></td>
                            <td style="padding: 0.15rem;"><?=getImgForGensTypeId($row[7],$row[8])?><sup style="color: red;"><?=getGensRank($row[9])?></sup></td>
                        </tr>
                    <?}?>
                </table>
            </div>
        <?}
    }
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- 家族统计信息 -->
<? try{
    if($sidebarConfig['gens_count_sidebar']) {
        $srvInfoCache = LoadCacheData('server_info.cache');    //加载数据文件
        if(is_array($srvInfoCache)) {?>
            <div class="card mb-3">
                <div class="card-header">
                    家族信息
                </div>
                <table class="table text-center">
                    <tr>
                        <td>
                            <div><img src="<?=__PATH_PUBLIC_IMG__?>Gens/vanert.png"  alt="巴纳尔特" /></div>
                            <div>「巴内尔特」<sup class="text-danger"><?=$srvInfoCache[0][5]?></sup></div>
                        </td>
                        <td>
                            <div><strong>“巅峰对决”</strong></div>
                            <div style="font-size:50px">VS</div>
                            <div>家族比例</div>
                        </td>
                        <td>
                            <div><img src="<?=__PATH_PUBLIC_IMG__?>Gens/duprian.png"  alt="多普瑞恩" /></div>
                            <div>「多普瑞恩」<sup class="text-danger"><?=$srvInfoCache[0][4]?></sup></div>
                        </td>
                    </tr>
                </table>
            </div>
        <?}?>
    <?}
}catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!-- 事件倒计时间 -->
<? try{ ?>
	<?if($sidebarConfig['event_sidebar']) {?>
    <div class="card mb-3">
        <div class="card-header">游戏事件</div>
        <div class="">
            <ul class="list-group" id="events"></ul>
            <script type="text/javascript">
                $(document).ready(function() {
                    eventsTime.loadEventSchedule();
                });
            </script>
        </div>
    </div>
	<?}?>
<? }catch (Exception $exception){
    message('error',$exception->getMessage());
}?>

<!--自定义模块-->
<?// try{?>
<!--        <div class="card mb-3">-->
<!--            <div class="card-header">标题</div>-->
<!--            <div class="card-body">-->
<!--                内容-->
<!--                样式: <a href="https://v4.bootcss.com/docs/components/card/">https://v4.bootcss.com/docs/components/card/</a>-->
<!--            </div>-->
<!--        </div>-->
<?//}catch (Exception $exception){
//    message('error',$exception->getMessage());
//}?>

