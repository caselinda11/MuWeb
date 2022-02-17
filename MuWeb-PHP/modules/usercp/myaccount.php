<?php
/**
 * 用户个人面板
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?=__BASE_URL__?>">官方主页</a></li>
        <li class="breadcrumb-item active" aria-current="page">我的账号</li>
    </ol>
</nav>

<?
try {
    if(!isLoggedIn()) redirect(1,'login');
    # 模块状态
    if(!mconfig('active')) throw new Exception('该功能暂已关闭，请稍后尝试访问。');
    # 调用基础类
    $common = new common();
    # 获取账号信息
    $accountInfo = $common->getUserInfoForUserGID($_SESSION['group'], $_SESSION['userid']);
    if(!is_array($accountInfo)) throw new Exception('无法检索您的账号信息，请稍后再试。');
    # 账号在线状态
    $onlineStatus = (!$common->checkUserOnline($_SESSION['group'],$_SESSION['username']) ? '<span class="badge badge-danger">离线</span>' : '<span class="badge badge-success">在线</span>');
    # 账号状态
    $accountStatus = ($accountInfo[_CLMN_BLOCCODE_] ? '<span class="badge badge-danger">封停</span>' : '<span class="badge badge-success">正常</span>');
?>
    <div class="card mb-3">
        <div class="card-header">我的账号<span class="float-right"><?=$accountStatus;?></span></div>
        <table class="table table-striped text-center">
            <tbody>
            <tr>
                <td rowspan="20" width="40%">
                    <div><span class="fa fa-user-circle" style="font-size:100px"></span></div>
                    <div><kbd><?=$accountInfo[_CLMN_USERNM_]?></kbd></div>
                    <div><?=$onlineStatus?></div>
                    <div><kbd>ID : 00<?=$_SESSION['userid']?></kbd></div>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="table table-striped text-center">
                        <tbody>
                        <tr>
                            <th>分区</th>
                            <td colspan="2">
                                <input type="text" class="form-control" disabled="disabled" value="<?=getGroupNameForGroupID($_SESSION['group']);?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>账号</th>
                            <td colspan="2">
                                <input type="text" class="form-control" disabled="disabled" value="<?=$accountInfo[_CLMN_USERNM_];?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>邮箱</th>
                            <td>
                                <input type="text" class="form-control" disabled="disabled"  value="<?=$accountInfo[_CLMN_EMAIL_];?>">
                            </td>
                            <td>
                                <a href="<?=__BASE_URL__?>usercp/myemail" class="btn btn-xs btn-outline-danger btn-sm">更改</a>
                            </td>
                        </tr>
                        <tr>
                            <th>密码</th>
                            <td>
                                <input type="text" class="form-control" disabled="disabled" value="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;">
                            </td>
                            <td>
                                <a href="<?=__BASE_URL__?>usercp/mypassword" class="btn btn-xs btn-outline-danger btn-sm">更改</a>
                            </td>
                        </tr>
                        <? //积分 or 元宝
                        try {
                            $creditSystem = new CreditSystem();
                            $creditConfigList = $creditSystem->showConfigs();
                            if(is_array($creditConfigList)) {
                                foreach($creditConfigList as $myCredits) {
                                    if(!$myCredits['config_display']) continue;

                                    $creditSystem->setConfigId($myCredits['config_id']);
                                    switch($myCredits['config_user_col_id']) {
                                        case 'userid':
                                            $creditSystem->setIdentifier($accountInfo[_CLMN_MEMBID_]);
                                            break;
                                        case 'character':
                                            $creditSystem->setIdentifier($_SESSION['character']);
                                            break;
                                        default:
                                        case 'username':
                                            $creditSystem->setIdentifier($accountInfo[_CLMN_USERNM_]);
                                            break;
                                    }
                                    $configCredits = $creditSystem->getCredits($_SESSION['group']); ?>
                                    <tr>
                                        <th><?=$myCredits['config_title'];?></th>
                                        <td><strong><?=number_format($configCredits)?></strong></td>
                                        <td><a href="<?=$myCredits['config_buy_link']?>" class="btn btn-xs btn-outline-danger btn-sm">充值</a></td>
                                    </tr>
                                <?}
                            }
                        } catch(Exception $ex) {}
                        ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?if(mconfig('invite')){?>
<!--    推荐人-->
    <div class="row">
        <table class="col-md-12 mb-2">
            <tbody>
            <tr>
                <td>
                    <script type="text/javascript">
                        /**
                         * @param text
                         * @returns {boolean}
                         */
                        function copyText (text) {
                            if (!document.execCommand) return false;
                            const textarea = document.createElement("textarea");
                            textarea.value = text;
                            document.body.appendChild(textarea);
                            textarea.focus();
                            textarea.setSelectionRange ? textarea.setSelectionRange(0, textarea.value.length) : textarea.select();
                            const result = document.execCommand("copy");
                            document.body.removeChild(textarea);
                            return result;
                        }

                        /**
                         * @param id
                         */
                        function copyContent(id){
                            const copyCon = document.getElementById(id).value;
                            const state = copyText(copyCon);
                            if (state === true){
                                modal_msg("推广链接:【"+ copyCon + "】<br>已经成功复制到剪帖板上，您可张贴使用了！");
                            } else if (state === false){
                                modal_msg("复制失败，请手动复制内容。");
                            }
                        }
                    </script>
                    <div class="col-auto col-md-12">
                        <div class="input-group mb-2 shadow-sm">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <span class="" style="display: inline-block;background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAUCAMAAADSpG8HAAAAjVBMVEUAAAD/Y2P/Y2P/Y2P/YmL/Y2P/ZGT/ZGT/Zmb/bW3/YmL/////aWn/u7v/mJj/jo7/+/v/goL/nJz/29v/ysr/c3P/ior/enr/b2//9/f/8/P/4eH/xcX/tbX/p6f/n5//dnb/Zmb/9fX/6ur/6en/5eX/1NT/0ND/v7//s7P/q6v/k5P/kJD/fn7/wsLdxGk5AAAACnRSTlMA2Pm1j4tAMzIHqetxhwAAAMxJREFUKM+t0McOwyAQRVHSkzeAcYxL3J3e///zYqIUvIlZ5C6QGB2BNGyE3kYMDrmjRlwA+KKJhelSixsQiQRYnj6I06puB8Q90nmeS64KQJMEttpGwQuFMO3ba5oekVBlofOqtFGp+I3OW0gKLSQrFT8RKaUixBSVWU3hMYONcMgqg66+73NgczoU2Cx3RRcl6e77HUSw9nDfU9RFkPR+KQY8UrydrIEugjbIFACNao+EtNPG/47Gg58Nn6in+cABscUEQ9bfbMpcegAleh0ZHd6gHwAAAABJRU5ErkJggg==) no-repeat 50%;background-size: contain;cursor: pointer;width: 20px;height: 10px;position: absolute;LEFT: 2px;top: 1px;"></span>
                                    推荐链接
                                </div>
                            </div>
                            <input type="text" class="form-control" id="copy" disabled value="<?=__BASE_URL__?>register?inviteID=00<?=$_SESSION['userid']?>" placeholder="点击复制">
                            <div class="input-group-prepend">
                                <div class="input-group-text btn" onclick="copyContent('copy')">点击复制</div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?}?>
<!--    用户导航按钮-->
    <div class="card mb-3">
        <div class="card-header">账号操作</div>
        <div class="row">
        <div class="btn-group col-md-12 mb-3">
            <?=templateBuildMyAccount('btn btn-default');?>
        </div>
        <div class="btn-group col-md-12">

        <!--  扩展按钮 -->
        <? global $extra_MyAccount_link;?>
        <?if(isset($extra_MyAccount_link)){?>
            <?if(is_array($extra_MyAccount_link)){?>
                <?foreach ($extra_MyAccount_link as $item){?>
                    <?foreach ($item as $name=>$link){?>
                        <a class="btn btn-default" href="<?=__BASE_URL__.$link?>"><?=$name?></a>
                    <?}?>
                <?}?>
            <?}?>
        <?}?>
        </div>
        </div>
    </div>
    <?php
    # 角色信息
    $character = new Character();
    $AccountCharacters = $character->getAccountCharacterNameForAccount($_SESSION['group'],$_SESSION['username']);
    ?>
    <div class="card mb-3">
        <div class="card-header">角色管理</div>
            <table class="table text-center">
                <tbody>
                <tr>
                <?
                if(is_array($AccountCharacters)) {
                foreach($AccountCharacters as $characterName) {
                    if(empty($characterName)) continue;
                    $class = $character->getCharacterClassForName($characterName);
                    $characterIMG = $character->GenerateCharacterClassAvatar($class,1,1,30,'rounded-circle');
                    ?>

                        <td class="">
                            <div class="character-img"><?=$characterIMG?></div>
                            <div class="character-name"><?=playerProfile(getServerCodeForGroupID($_SESSION['group']),$characterName,'');?></div>
                        </td>

                <?}?>

                <?} else {?>
                    <td class="text-center">暂无角色信息</td>
                <?}?>
                </tr>
                </tbody>
            </table>
    </div>
<!--    扩展-->
    <?
    if(class_exists('Plugin\MentoringSystem')) {
        $MentoringSystem = new \Plugin\MentoringSystem();
        $MentoringData = $MentoringSystem->getMentoringInfo();
        $applyListData = $MentoringSystem->getApplyList();
        ?>
        <div class="card mb-3">
        <div class="card-header">师傅系统</div>
            <?if(is_array($applyListData)){?>
                <table class="table table-bordered text-center">
                    <tbody>
                    <tr>
                        <th>申请账号</th>
                        <th>申请时间</th>
                        <th>操作</th>
                    </tr>
                    <?foreach ($applyListData as $data){?>
                        <tr>
                            <td width="40%"><?=$data['apprentice']?></td>
                            <td width="30%"><?=date("m-d H:i:s",strtotime($data['Date']))?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?=__BASE_URL__?>usercp/MentoringSystem?apply=<?=$data['apprentice']?>" class="btn btn-success">收Ta为徒</a>
                                    <a href="<?=__BASE_URL__?>usercp/MentoringSystem?unapply=<?=$data['apprentice']?>" class="btn btn-danger">拒绝</a>
                                </div>
                            </td>
                        </tr>
                    <?}?>
                    </tbody>
                </table>
            <?}?>
            <?if(is_array($MentoringData)){?>
                <?foreach ($MentoringData as $key => $data){?>
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            徒弟:[<?=$key?>]
                            <a href="<?=__BASE_URL__?>usercp/MentoringSystem?uid=<?=$key?>" class="more badge badge-danger">逐出师门</a>
                        </div>
                        <table class="table text-center">
                            <tbody>
                            <tr>
                                <?if(is_array($data)){?>
                                    <?foreach ($data as $datum){
                                        $characterIMG = $character->GenerateCharacterClassAvatar($datum['Class'],1,1,30,'rounded-circle');
                                        ?>
                                        <td class="">
                                            <div class="character-img"><?=$characterIMG?></div>
                                            <div class="character-name"><?=playerProfile(getServerCodeForGroupID($_SESSION['group']),$datum['Name'],'');?></div>
                                        </td>
                                    <?}?>
                                <?}else{?>
                                    <td class="text-center"><strong>暂无角色信息</strong></td>
                                <?}?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                <?}?>
            <?}else{?>
                <p class="text-center"><strong>暂无师徒信息</strong></p>
            <?}?>
        </div>
    <?}?>
<?php
} catch (Exception $e) {
    $e->getMessage();
}