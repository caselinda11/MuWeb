<?php
/**
 * [MemberReward]类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

namespace Plugin;

use Exception;
use Connection;
use Validator;
use Plugin\equipment;
use Token;
use common;

class MemberReward {

	private $_modulesPath = 'modules';
    private $serverFiles,$web,$config,$common;

    /**
     * 构造函数
     * MemberReward constructor.
     * @throws Exception
     */
	public function __construct()
    {
        //服务器类型
        global $config;
        $this->serverFiles = strtolower($config['server_files']);
        #网站库
        $this->web = Connection::Database("Web");
        #配置文件
        $this->config = $this->loadConfig();
        $this->common = new common();
    }
    /***********************************************************************/

    /**
     * 放置您的自定义方法
     */

    /**
     * 领取礼包
     * @param $group
     * @param $username
     * @param $char_name
     * @param $UID
     * @throws Exception
     */
    public function setMemberRewardSend($group, $username, $char_name, $UID)
    {
        if(!Token::checkToken('MemberReward')) throw new Exception('出错了，请您重新输入！');
        if(!check_value($group)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($username)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($char_name)) throw new Exception('出错了，请您重新输入！');
        if(!check_value($UID)) throw new Exception('出错了，请您重新输入！');
        #检查是否在线
        if ($this->common->checkUserOnline($group, $username)) throw new Exception('您的账号已在线，请断开连接。');
        #获取列表
        $bag = $this->getMemberRewardList($UID);
        if(!is_array($bag)) throw new Exception("您所选择的礼包已经下架，请重新选择。");
        #验证会员等级
        $MemberLevel =$this->getMemberLevel($group, $username);
        if($MemberLevel < $bag['requirement_vip']) throw new Exception("您的会员等级过低无法领取该礼包，请重新选择。");
        #验证是否已经领取
        if(!$this->checkMemberRewardSend($group, $username, $UID)) throw new Exception("您已领取过该奖励，无法反复领取。");

        $userID = $this->common->getUserGIDForUsername($group,$username);
        #获取物品的总编码
        $items = new equipment();
        $item = $items->convertItem($bag['reward_code']);

        $StorageBox = [
            'UserGuid'      => $userID,
            'CharacterName' => $char_name,
            'Type'          => 1,
            'ItemCode'      => $item['type'],#总编码
            'ItemData'      => $bag['reward_code'],
            'ValueType'     => '-1',
            'ValueCnt'      => 0,
            'CustomData'    => 0,
            'GetDate'       => date('Y-m-d h:i:s'),
            'ExpireDate'    => date('Y-m-d h:i:s',strtotime('+7 day')),
            'UsedInfo'      => 1, # 0已接收,1未接收
            //物品信息
            'index'                 =>  $item['index'],#小编吗
            'level'                 =>  $item['level'],#等级
            'skill'                 =>  $item['skill'],#技能
            'lucky'                 =>  $item['lucky'],#幸运
            'option'                =>  $item['option'],#追加
            'durability'            =>  $item['durability'],#耐久
            'section'               =>  $item['section'],#大编码
            'setOption'             =>  $item['setOption'],#套装
            'newOption'             =>  $item['newOption'],#卓越
            'itemOptionEx'          =>  $item['itemOptionEx'],#380
            'jewelOfHarmonyOption'  =>  $item['jewelOfHarmonyOption'],#再生强化属性
            'socketOption1'         =>  $item['socketOption'][1],#镶嵌[0-4]
            'socketOption2'         =>  $item['socketOption'][2],#镶嵌[0-4]
            'socketOption3'         =>  $item['socketOption'][3],#镶嵌[0-4]
            'socketOption4'         =>  $item['socketOption'][4],#镶嵌[0-4]
            'socketOption5'         =>  $item['socketOption'][5],#镶嵌[0-4]
            'socketBonusPotion'     =>  $item['socketBonusPotion'],#荧光
            'periodItemOption'      =>  $item['periodItemOption'],#时限
        ];
        $muOnline = Connection::Database("MuOnline",$group);

        try {
            $muOnline->beginTransaction();
            $this->web->beginTransaction();
            switch ($this->serverFiles){
                case "egames":
                    $muOnline->query("INSERT INTO [T_StorageBox] ([UserGuid],[CharacterName],[Type],[ItemCode],[ItemData],[ValueType],[ValueCnt],[CustomData],[GetDate],[ExpireDate],[UsedInfo]) VALUES (
                                                                         ".$StorageBox['UserGuid'].",
                                                                         ".$StorageBox['CharacterName'].",
                                                                         ".$StorageBox['Type'].",
                                                                         ".$StorageBox['ItemCode'].",
                                                                         ".$StorageBox['ItemData'].",
                                                                         ".$StorageBox['ValueType'].",
                                                                         ".$StorageBox['ValueCnt'].",
                                                                         ".$StorageBox['CustomData'].",
                                                                         ".$StorageBox['GetDate'].",
                                                                         ".$StorageBox['ExpireDate'].",
                                                                         ".$StorageBox['UsedInfo'].")");
                    break;
                    break;
                case "igcn":
                    $muOnline->query("INSERT INTO [IGC_GremoryCase] ([AccountID],[Name],[GCType],[GiveType],[ItemType],[ItemIndex],[Level],[ItemDur],[Skill],[Luck],[Opt],[SetOpt],[NewOpt],[BonusSocketOpt],[SocketOpt1],[SocketOpt2],[SocketOpt3],[SocketOpt4],[SocketOpt5],[UsedInfo],[Serial],[RecvDate],[ReceiptDate],[RecvExpireDate],[ItemExpireDate],[RecvDateConvert],[RecvExpireDateConvert],[ItemExpireDateConvert],[ItemOptionEx],[ItemSerial]) VALUES ('".$username."','".$StorageBox['CharacterName']."', 2, 100, ".$StorageBox['section'].", ".$StorageBox['index'].", ".$StorageBox['level'].", ".$StorageBox['durability'].", ".$StorageBox['skill'].", ".$StorageBox['lucky'].", ".$StorageBox['option'].", ".$StorageBox['setOption'].", ".$StorageBox['newOption'].", ".$StorageBox['socketBonusPotion'].", ".$StorageBox['socketOption1'].", ".$StorageBox['socketOption2'].", ".$StorageBox['socketOption3'].", ".$StorageBox['socketOption4'].", ".$StorageBox['socketOption5'].", ".$StorageBox['UsedInfo'].", 0, '".$StorageBox['GetDate']."', NULL, '".$StorageBox['ExpireDate']."', '1970-01-01 00:00:00', ".time().", ".strtotime($StorageBox['ExpireDate']).", 0, 0, 0)");
                    break;
                default:
                    throw new Exception("暂不支持您的游戏版本。");
            }

            $this->web->query("INSERT INTO [X_TEAM_MEMBER_REWARD_LOG] ([AccountID],[Servercode],[Name],[Reward_id],[Receive_VIP],[Date]) VALUES (?,?,?,?,?,?)",[$username,getServerCodeForGroupID($group),$char_name,$bag['ID'],$bag['requirement_vip'],logDate()]);
            $muOnline->commit();
            $this->web->commit();
            alert('usercp/MemberReward',"奖励领取成功，请点击确定将回到主界面。");
        }catch (Exception $exception){
            $muOnline->rollBack();
            $this->web->rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * 获取当前会员等级
     * @param $group
     * @param $username
     * @return array|bool|void|null
     * @throws Exception
     */
    public function getMemberLevel($group, $username)
    {
        if(!check_value($group)) return 0;
        if(!check_value($username)) return 0;
        switch ($this->serverFiles){
            case "igcn":
                $level = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [Type] as vip FROM [T_VIPList] WHERE [AccountID] = ?",[$username]);
                break;
            case "muemu":
                $level = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [AccountLevel] as vip FROM ["._TBL_MI_."] WHERE ["._CLMN_USERNM_."] = ?",[$username]);
                break;
            default:
                $level = Connection::Database("Me_MuOnline",$group)->query_fetch_single("SELECT [vip] FROM ["._TBL_MI_."] WHERE ["._CLMN_USERNM_."] = ?",[$username]);
                break;
        }
        if(!is_array($level)) return 0;
        if(!check_value($level['vip'])) return 0;
        return $level['vip'];
    }
    /**
     * 检测奖励是否已经领取
     * @param $group
     * @param $username
     * @param $UID
     * @return bool|void
     */
    public function checkMemberRewardSend($group, $username, $UID)
    {
        if(!check_value($group)) return;
        if(!check_value($username)) return;
        if(!check_value($UID)) return;
        $check = $this->web->query_fetch_single("SELECT [ID] FROM [X_TEAM_MEMBER_REWARD_LOG] WHERE [AccountID] = ? AND [Reward_id] = ?",[$username,$UID]);
        if(!is_array($check)) return true;
        return;
    }
    /**
     * 获取配置列表
     * @param string $where
     * @return array|bool|mixed|void|null
     */
    public function getMemberRewardList($where = '')
    {
        if($where){
            $data = $this->web->query_fetch_single("SELECT * FROM [X_TEAM_MEMBER_REWARD] WHERE [ID] = ?",[$where]);
        }else{
            $data = $this->web->query_fetch("SELECT * FROM [X_TEAM_MEMBER_REWARD]");
        }
        if(!$data) return;
        return $data;
    }

    /***********************************************************************/
    /**
     * 加载插件模块
     * @param $module
     * @throws Exception
     */
	public function loadModule($module) {
		if(!Validator::Alpha($module)) throw new Exception('无法加载[会员领奖]插件模块。');
		if(!$this->_moduleExists($module)) throw new Exception('无法加载[会员领奖]插件模块。');
		if(!@include_once(__PATH_MEMBER_REWARD_ROOT__.$this->_modulesPath.'/'.$module.'.php')) throw new Exception('无法加载[会员领奖]插件模块。');
	}

    /**
     * 验证模块是否存在
     * @param $module
     * @return bool|void
     */
	private function _moduleExists($module) {
		if(!check_value($module)) return;
		if(!file_exists(__PATH_MEMBER_REWARD_ROOT__ . $this->_modulesPath . '/' . $module . '.php')) return;
		return true;
	}

    /**
     * 加载配置文件
     * @param string $file
     * @return mixed
     * @throws Exception
     */
    public function loadConfig($file='config')
    {
        $xmlPath = __PATH_MEMBER_REWARD_ROOT__.$file.'.xml';
        if(!file_exists($xmlPath)) throw new Exception('[会员领奖]配置文件不存在。');
        $xml = simplexml_load_file($xmlPath);
        if(!$xml) throw new Exception('无法加载[会员领奖]插件配置文件。');
        if($xml){
            $moduleCONFIGS = convertXML($xml->children());
        }
        if (isset($moduleCONFIGS)) {
            return $moduleCONFIGS;
        }
        return null;
    }
}