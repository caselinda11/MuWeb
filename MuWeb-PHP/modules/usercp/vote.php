<?php
/**
 * 推广页面模块
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
            <li class="breadcrumb-item active" aria-current="page">推广任务</li>
        </ol>
    </nav>
<?

try {
    if(!isLoggedIn()) redirect(1,'login');
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
	$vote = new Vote();
	if(check_value($_POST['submit'])) {
		try {
            if(!Token::checkToken('vote__'.$_POST['votesite_id'])) throw new Exception('出错了，请您重新输入！');
            $vote->setUserId($_SESSION['group'],$_SESSION['userid']);
			$vote->setIp($_SERVER['REMOTE_ADDR']);
			$machine = new Machine(PHP_OS);
			$vote->setMachineId($machine->mac_addr);
			$vote->setVoteSiteId($_POST['voting_site_id']);
			$vote->vote($_SESSION['group']);
		} catch (Exception $ex) {
			message('error', $ex->getMessage());
		}
	}
    message('','每台电脑将限制每次每个任务在规定的时间内只可领取一次!','提示:  ');

    ?>
    <div class="card">
        <div class="card-header">任务列表</div>
        <div class="">
            <table class="table table-striped table-bordered text-center">
                <tr>
                    <td width="33%">任务类型</td>
                    <td width="25%">奖励</td>
                    <td width="20%">时限[小时]</td>
                    <td>操作</td>
                </tr>

                <?$vote_sites = $vote->retrieveVoteSite();
                if(is_array($vote_sites)) {
                    foreach($vote_sites as $thisVoteSite) {
                        ?>
                        <form action="" method="post">
                            <input type="hidden" name="voting_site_id" value="<?=$thisVoteSite['votesite_id']?>"/>
                            <input type="hidden" name="key" value="<?=Token::generateToken('vote__'.$thisVoteSite['votesite_id'])?>"/>
                            <tr>
                                <td><?=$thisVoteSite['votesite_title']?></td>
                                <td><?=$thisVoteSite['votesite_reward']?>积分</td>
                                <td><?=$thisVoteSite['votesite_time']?>小时</td>
                                <td>
                                    <button name="submit" value="submit" class="btn btn-primary">立即完成</button>
                                </td>
                            </tr>
                        </form>
                    <?}
                }?>
            </table>
            <footer class="blockquote-footer text-right mt-3 mb-3 mr-3">点击完成积分将自动到账！</footer>
        </div>
    </div>
<?
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}