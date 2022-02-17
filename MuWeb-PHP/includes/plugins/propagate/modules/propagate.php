<?php
/**
 * 系统任务插件模块页面
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
            <li class="breadcrumb-item active" aria-current="page">系统任务</li>
        </ol>
    </nav>
    <?php
try {
    if(!isLoggedIn()) redirect(1,'login');
    $propagate = new \Plugin\propagate();
    $config = $propagate->loadConfig();
    if(!$config['active']) throw new Exception('每日任务插件未启用,请稍后再试或联系在线客服!');

    message('','每台电脑将限制每次每个任务在规定的时间内只可领取一次!','提示:  ');
    $vote = new Vote();
    if(check_value($_POST['submit'])) {
        try {
            $machine = new Machine(PHP_OS);
            $propagate->setPropagate($_SESSION['group'],$_SESSION['userid'],$_SESSION['username'],$_SERVER['REMOTE_ADDR'],$_POST['voting_site_id'],$machine->mac_addr);
        } catch (Exception $ex) {
            message('error', $ex->getMessage());
        }
    }

    ?>
    <div class="card">
        <div class="card-header">任务列表</div>
        <div class="">
            <table class="table table-striped table-bordered text-center">
                <tr>
                    <td>任务名称</td>
                    <td>奖励货币</td>
                    <td>奖励物品<div class="text-muted">鼠标放入可以查看属性</div></td>
                    <td>时限[小时]</td>
                    <td>操作</td>
                </tr>

                <?$vote_sites = $vote->retrieveVoteSite();
                if(is_array($vote_sites)) {
                    foreach($vote_sites as $thisVoteSite) {
                        $credits = ($thisVoteSite['votesite_reward']) ? $thisVoteSite['votesite_reward'] : '-';
                        $item = new \Plugin\equipment();
                        $items = ($thisVoteSite['reward_item']) ? '<div class="data-info" data-info="'.$thisVoteSite['reward_item'].'"><img src="'.$item->ItemsUrl($thisVoteSite['reward_item']).'" alt="" width="35" height="35"></div>' : '-';
                        ?>
                        <form action="" method="post">
                            <input type="hidden" name="voting_site_id" value="<?=$thisVoteSite['votesite_id']?>"/>
                            <input type="hidden" name="key" value="<?=Token::generateToken('propagate__'.$thisVoteSite['votesite_id'])?>"/>
                            <tr class="rankings">
                                <td><?=$thisVoteSite['votesite_title']?></td>
                                <td><?=$credits?></td>
                                <td><?=$items?></td>
                                <td><?=$thisVoteSite['votesite_time']?>小时</td>
                                <td><button name="submit" value="submit" class="btn btn-primary">领取</button></td>
                            </tr>
                        </form>
                    <?}
                }?>
            </table>
            <footer class="blockquote-footer text-right mt-3 mb-3 mr-3">点击完成积分将自动到账！</footer>
        </div>
    </div>

<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}