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
                        <li class="breadcrumb-item active">账号管理</li>
                        <li class="breadcrumb-item active">个人账号</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    个人账号</h4>
            </div>
        </div>
    </div>
<?php
try{
if(!check_value($_GET['group']) && !check_value($_GET['id'])) throw new  Exception("无法获取帐号信息，分区或帐号错误！");
$accountInfoConfig['showGeneralInfo'] = true;
$accountInfoConfig['showStatusInfo'] = true;
$accountInfoConfig['showIpInfo'] = true;
$accountInfoConfig['showCharacters'] = true;
if(check_value($_GET['id'])) {
	try {
        $common = new common();
        $group = getGroupIDForServerCode($_GET['group']);
		if(check_value($_POST['edit_submit'])) {
			try {
                if(!check_value($_POST['action'])) throw new Exception("无效请求。");
				$sendEmail = (check_value($_POST['edit_sendmail']) && $_POST['edit_sendmail'] == 1 ? true : false);
				$accountInfo = $common->getUserInfoForUserGID($group,$_GET['id']);

				if(!$accountInfo) throw new Exception("无法获取帐号信息[无效帐号]！");
				switch($_POST['action']) {
					case "changepassword":
						if(!check_value($_POST['changepassword_newPass'])) throw new Exception("请输入新密码。");
						if(!Validator::PasswordLength($_POST['changepassword_newPass'])) throw new Exception("密码无效。");
						if(!$common->changePassword($group,$accountInfo[_CLMN_MEMBID_], $accountInfo[_CLMN_USERNM_], $_POST['changepassword_newPass'])) throw new Exception("无法更改密码。");
						message('success', '密码已更新！');
						
						# 发送新密码到邮箱
						if(check_value($_POST['edit_sendmail'])) {
							$email = new Email();
							$email->setTemplate('ADMIN_CHANGE_PASSWORD');
							$email->addVariable('{USERNAME}', $accountInfo[_CLMN_USERNM_]);
							$email->addVariable('{NEW_PASSWORD}', $_POST['changepassword_newPass']);
							$email->addAddress($accountInfo[_CLMN_EMAIL_]);
							$email->send();
						}
						break;
					case "changeemail":
						if(!check_value($_POST['changeEmail_newEmail'])) throw new Exception("请输入新的邮箱。");
						if(!Validator::Email($_POST['changeEmail_newEmail'])) throw new Exception("邮箱地址无效。");
						if($common->_checkEmailExists($group,$_POST['changeEmail_newEmail'])) throw new Exception("具有相同邮箱的另一个帐户已存在。");
						if(!$common->updateEmail($group,$accountInfo[_CLMN_MEMBID_], $_POST['changeEmail_newEmail'])) throw new Exception("无法更新邮箱。");
						message('success', '邮箱地址已更新！');
						
						# 向当前邮箱发送新的邮箱地址
						if(check_value($_POST['edit_sendmail'])) {
							$email = new Email();
							$email->setTemplate('ADMIN_CHANGE_EMAIL');
							$email->addVariable('{USERNAME}', $accountInfo[_CLMN_USERNM_]);
							$email->addVariable('{NEW_EMAIL}', $_POST['changeEmail_newEmail']);
							$email->addAddress($accountInfo[_CLMN_EMAIL_]);
							$email->send();
						}
						break;
					default:
						throw new Exception("无效请求。");
				}
			} catch(Exception $ex) {
				message('error', $ex->getMessage());
			}
		}
        $accountInfo = $common->getUserInfoForUserGID($group,$_GET['id']);
		if(!is_array($accountInfo)) throw new Exception("[1]无法获取帐号信息，分区或帐号错误！");
		?>
                        <div class="row">
                            <div class="col-md-5">
                                <?php
                    if($accountInfoConfig['showGeneralInfo']) {
                        // 一般账户信息
                        echo '<div class="card">';
                        echo '<div class="card-header">基础信息</div>';
                        echo '<div class="card-body">';

                            $isBanned = ($accountInfo[_CLMN_BLOCCODE_] == 0 ? '<span class="badge badge-success">活跃</span>' : '<span class="badge badge-danger">封停</span>');
                            echo '<table class="table table-no-border table-hover">';
                                echo '<tr>';
                                    echo '<th>用户ID:</th>';
                                    echo '<td>'.$accountInfo[_CLMN_MEMBID_].'</td>';
                                echo '</tr>';
                                echo '<tr>';
                                    echo '<th>账号:</th>';
                                    echo '<td>'.$accountInfo[_CLMN_USERNM_].'</td>';
                                echo '</tr>';
                                echo '<tr>';
                                    echo '<th>邮箱:</th>';
                                    echo '<td>'.$accountInfo[_CLMN_EMAIL_].'</td>';
                                echo '</tr>';

                                echo '<tr>';
                                    echo '<th>账号:</th>';
                                    echo '<td>'.$isBanned.'</td>';
                                echo '</tr>';
                            echo '</table>';
                        echo '</div>';
                        echo '</div>';
                    }

                    if($accountInfoConfig['showStatusInfo']) {
                        // ACCOUNT STATUS
                        $statusData = Connection::Database('MuOnline',$group)->query_fetch_single("SELECT * FROM "._TBL_MS_." WHERE "._CLMN_MS_MEMBID_." = ?", [$accountInfo[_CLMN_USERNM_]]);
                        echo '<div class="card">';
                        echo '<div class="card-header">游戏状态</div>';
                        echo '<div class="card-body">';
                            if(is_array($statusData)) {
                                $onlineStatus = ($statusData[_CLMN_CONNSTAT_] == 1 ? '<span class="label label-success">在线</span>' : '<span class="label label-danger">离线</span>');
                                echo '<table class="table table-no-border table-hover">';
                                    echo '<tr>';
                                        echo '<td>游戏状态:</td>';
                                        echo '<td>'.$onlineStatus.'</td>';
                                    echo '</tr>';
                                    echo '<tr>';
                                        echo '<td>所在服务器:</td>';
                                        echo '<td>'.$statusData[_CLMN_MS_GS_].'</td>';
                                    echo '</tr>';
                                echo '</table>';
                            } else {
                                message('warning', '该账号没有上过游戏，暂无信息。');
                            }
                        echo '</div>';
                        echo '</div>';
                    }

                    if($accountInfoConfig['showCharacters']) {
                        // 账号角色信息
                        $Character = new Character();
                        $accountCharacters = $Character->getCharacterNameForUsername($group,$accountInfo['memb___id']);
                        echo '<div class="card">';
                        echo '<div class="card-header">角色信息</div>';
                        echo '<div class="card-body">';
                            if(is_array($accountCharacters)) {
                                echo '<div class="list-group">';
                                    foreach($accountCharacters as $characterName) {
                                        echo '<a href="'.admincp_base("Character_Edit&group=".$group."&name=".$characterName).'" class="list-group-item list-group-item-action text-center" aria-current="true">'.$characterName.'</a>';
                                    }
                                echo '</div>';
                            } else {
                                message('warning', '该账号暂无角色信息');
                            }
                        echo '</div>';
                        echo '</div>';
                    }
        ?>
                                 <div class="card">
                                     <div class="card-header">更改账户密码</div>
                                     <div class="card-body">
                                         <form role="form" method="post">
                                         <input type="hidden" name="action" value="changepassword"/>
                                             <div class="form-group">
                                                 <label for="input_1">新密码:</label>
                                                 <input type="text" class="form-control" id="input_1" name="changepassword_newPass" placeholder="新密码">
                                             </div>
                                             <div class="checkbox">
                                                 <label><input type="checkbox" name="edit_sendmail" value="1" checked> 向用户发送变更密码的邮件。</label>
                                             </div>
                                             <button type="submit" name="edit_submit" class="col-md-12 btn btn-success" value="ok">更改</button>
                                         </form>
                                     </div>
                                 </div>
                                    <div class="card">
                                        <div class="card-header">更改帐户的邮箱</div>
                                        <div class="card-body">
                                            <form role="form" method="post">
                                                <input type="hidden" name="action" value="changeemail"/>
                                                <div class="form-group">
                                                    <label for="input_2">新邮箱:</label>
                                                    <input type="email" class="form-control" id="input_2" name="changeEmail_newEmail" placeholder="新邮箱地址">
                                                </div>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" name="edit_sendmail" value="1" checked> 向用户发送变更邮箱地址的邮件。</label>
                                                </div>
                                                <button type="submit" name="edit_submit" class="col-md-12 btn btn-success" value="ok">更改</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <div class="col-md-7">
                                 <?php
                    if($accountInfoConfig['showIpInfo']) {

                        if(strtolower(config('server_files')) == 'igcn') {
                            // ACCOUNT IP LIST
                            echo '<div class="card">';
                            echo '<div class="card-header">帐号IP地址</div>';
                            echo '<div class="card-body">';

                                $accountIpHistory = Connection::Database('MuOnline',$group)->query_fetch("SELECT DISTINCT("._CLMN_CH_IP_.") FROM "._TBL_CH_." WHERE "._CLMN_CH_ACCID_." = ?", array($accountInfo[_CLMN_USERNM_]));
                                if(is_array($accountIpHistory)) {
                                    echo '<table class="table table-no-border table-hover">';
                                        foreach($accountIpHistory as $accountIp) {
                                            echo '<tr>';
                                                echo '<td><a href="http://ip.tool.chinaz.com/'.urlencode($accountIp[_CLMN_CH_IP_]).'" target="_blank">'.$accountIp[_CLMN_CH_IP_].'</a></td>';
                                            echo '</tr>';
                                        }
                                    echo '</table>';
                                } else {
                                    message('warning', '在数据库中找不到IP地址。');
                                }

                            echo '</div>';
                            echo '</div>';

                            // ACCOUNT CONNECTION HISTORY
                            echo '<div class="card">';
                            echo '<div class="card-header">帐户连接历史记录（最近25个）</div>';
                            echo '<div class="card-body">';

                                $accountConHistory = Connection::Database('MuOnline',$group)->query_fetch("SELECT TOP 25 * FROM "._TBL_CH_." WHERE "._CLMN_CH_ACCID_." = ? AND "._CLMN_CH_STATE_." = ? ORDER BY "._CLMN_CH_ID_." ASC", array($accountInfo[_CLMN_USERNM_], 'Connect'));
                                if(is_array($accountConHistory)) {
                                    echo '<table class="table table-no-border table-hover">';
                                        echo '<tr >';
                                            echo '<th style="text-align:center">日期</th>';
                                            echo '<th style="text-align:center" class="hidden-xs">服务器</th>';
                                            echo '<th style="text-align:center">IP</th>';
                                            echo '<th style="text-align:center">硬件ID</th>';
                                        echo '</tr>';
                                        foreach($accountConHistory as $connection) {
                                            echo '<tr>';
                                                echo '<td align="center">'.$connection[_CLMN_CH_DATE_].'</td>';
                                                echo '<td class="hidden-xs" align="center">'.$connection[_CLMN_CH_SRVNM_].'</td>';
                                                echo '<td align="center">'.$connection[_CLMN_CH_IP_].'</td>';
                                                echo '<td align="center">'.$connection[_CLMN_CH_HWID_].'</td>';
                                            echo '</tr>';
                                        }
                                    echo '</table>';
                                } else {
                                    message('warning', '找不到帐户的连接历史记录。');
                                }

                            echo '</div>';
                            echo '</div>';
                        }

                    }
                    ?>
                        </div>
                        </div>
<?php
	} catch(Exception $ex) {
		message('error', $ex->getMessage());
	}
	
    } else {
        message('error', '请提供有效的账号!');
    }
} catch(Exception $ex) {
    message('error', $ex->getMessage());
}
?>