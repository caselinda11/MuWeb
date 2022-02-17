<?php
/**
 * 货币类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class CreditSystem {

    private $_configId;
    private $_identifier;

    private $_configTitle;
    private $_configDatabase;
    private $_configTable;
    private $_configTableColumn;
    private $_configUserColumn;
    private $_configUserColumnId;
    private $_configBuyLink;
    private $_configCheckOnline = true;
    private $_configDisplay = false;
    private $common,$web,$character;

    private $_allowedUserColId = [
        'userid',
        'username',
        'character'
    ];

    /**
     * 构造函数
     * CreditSystem constructor.
     * @throws Exception
     */
    function __construct() {
        $this->web = Connection::Database('Web');
        # 初始化
        $this->common = new common();
        $this->character = new Character();
    }

    /**
     * 设置标识符
     * 识别积分的id
     * @param $input
     * @throws Exception
     */
    public function setIdentifier($input) {
        if(!$this->_configId) throw new Exception('[货币系统] 您尚未设置配置ID。');
        $config = $this->showConfigs(true);
        switch($config['config_user_col_id']) {
            case 'userid':
                $this->_setUserid($input);
                break;
            case 'username':
                $this->_setUsername($input);
                break;
            case 'character':
                $this->_setCharacter($input);
                break;
            default:
                throw new Exception("[货币系统] 不合法的识别符!");
        }
    }

    /**
     * _setUserId
     * 设置用户ID标识符
     * @param int $input
     * @throws Exception
     */
    private function _setUserid($input) {
        if(!Validator::UnsignedNumber($input)) throw new Exception('[货币系统] 输入的用户ID无效。');
        $this->_identifier = $input;
    }

    /**
     * _setUsername
     * 设置用户名标识符
     * @param string $input
     * @throws Exception
     */
    private function _setUsername($input) {
        if(!Validator::AlphaNumeric($input)) throw new Exception('[货币系统] 输入的用户名包含非法字符。');
        if(!Validator::UsernameLength($input)) throw new Exception('[货币系统] 输入的用户名无效。');
        $this->_identifier = $input;
    }


    /**
     * _setCharacter
     * 设置字符名称标识符
     * @param string $input
     * @throws Exception
     */
    private function _setCharacter($input) {
        if(!check_value($input)) throw new Exception('[货币系统] 输入的角色名称无效。');
        $this->_identifier = $input;
    }

    /**
     * 增加货币
     * @param $group
     * @param $input
     * @return bool
     * @throws Exception
     */
    public function addCredits($group,$input) {
        if(!Validator::UnsignedNumber($input)) throw new Exception('[货币系统] 要操作的货币必须是纯数字。');
        if(!$this->_configId) throw new Exception('[货币系统] 您尚未设置配置ID。');
        if(!$this->_identifier) throw new Exception('[货币系统] 尚未设置用户标识符。');

        # 获取配置
        $config = $this->showConfigs(true);

        # 检查在线状态
        if($config['config_checkonline']) {
            if($this->_isOnline($config['config_user_col_id'])) throw new Exception('您的账号已在线，请断开连接。');
        }

        # 查看当前积分
        $currentCredits = $this->getCredits($group);

        # 新积分
        $newCredits = $input + $currentCredits;

        # 选择数据库
        $database = ($config['config_database'] == "MuOnline" ? "MuOnline" : "Me_MuOnline");

        # 建立查询
        $data = [
            'credits' => $newCredits,
            'identifier' => $this->_identifier
        ];
        $variables = ['{TABLE}','{COLUMN}','{USER_COLUMN}'];
        $values = [$config['config_table'], $config['config_credits_col'], $config['config_user_col']];
        $query = str_replace($variables, $values, "UPDATE {TABLE} SET {COLUMN} = :credits WHERE {USER_COLUMN} = :identifier");

        # 执行
        $addCredits = Connection::Database($database,$group)->query($query, $data);
        if(!$addCredits) throw new Exception('[货币系统] 添加货币时出错了');

        $log =$this->_addLog($config['config_title'], $input, "add");
        if(!$log)  throw new Exception('[货币系统]日志写入失败。');
        if($addCredits && $log) return true;
        return false;
    }

    /**
     * 删减货币
     * @param $group
     * @param $input
     * @return bool
     * @throws Exception
     */
    public function subtractCredits($group,$input) {
        if(!Validator::UnsignedNumber($input)) throw new Exception('[货币系统] 要操作的货币必须是纯数字。');
        if(!$this->_configId) throw new Exception('[货币系统] 您尚未设置配置ID。');
        if(!$this->_identifier) throw new Exception('[货币系统] 尚未设置用户标识符。');

        # 获取配置
        $config = $this->showConfigs(true);

        # 检测在线
        if($config['config_checkonline']) {
            if($this->_isOnline($config['config_user_col_id'])) throw new Exception('您的账号已在线，请断开连接。');
        }

        # 查看当前的积分
        if($this->getCredits($group) < $input) throw new Exception('您没有足够的'.$config['config_title'].'支付这笔费用。');

        # 选择数据库
        $database = ($config['config_database'] == "MuOnline" ? "MuOnline" : "Me_MuOnline");

        # 执行
        $data = [
            'credits' => $input,
            'identifier' => $this->_identifier
        ];
        $variables = ['{TABLE}','{COLUMN}','{USER_COLUMN}'];
        $values = [$config['config_table'], $config['config_credits_col'], $config['config_user_col']];
        $query = str_replace($variables, $values, "UPDATE {TABLE} SET {COLUMN} = {COLUMN} - :credits WHERE {USER_COLUMN} = :identifier");

        # add credits
        $addCredits = Connection::Database($database,$group)->query($query, $data);
        if(!$addCredits) throw new Exception('[货币系统] 操作货币时出错了');

        $log = $this->_addLog($config['config_title'], $input, "subtract");
        if(!$log)  throw new Exception('[货币系统]日志写入失败。');
        if($addCredits && $log) return true;
        return false;
    }

    /**
     * 设置配置ID
     *  来源于数据id
     * @param int $id
     * @throws Exception
     */
    public function setConfigId($id) {
        if(!Validator::UnsignedNumber($id)) throw new Exception('[货币系统] 提供的配置ID无效。');
        if(!$this->_configurationExists($id)) throw new Exception('[货币系统] 提供的配置ID无效。');
        $this->_configId = $id;
    }

    /**
     * 设置新配置的标题
     * @param string $title
     * @throws Exception
     */
    public function setConfigTitle($title) {
        if(!Validator::Chars($title, ['a-z', 'A-Z', '0-9', ' ','\x80-\xff'])) throw new Exception('[货币系统] 标题只能包含字母数字、字符和空格。');
        $this->_configTitle = $title;
    }

    /**
     * 为新配置设置数据库
     * @param $database
     * @throws Exception
     */
    public function setConfigDatabase($database) {
        if(!Validator::Chars($database, ['a-z', 'A-Z', '0-9', '_'])) throw new Exception('[货币系统] 数据库名必须是大小写字母数字下划线组成。');
        $this->_configDatabase = $database;
    }

    /**
     * 为新配置设置表
     * @param string $tableName
     * @throws Exception
     */
    public function setConfigTable($tableName) {
        if(!Validator::Chars($tableName, ['a-z', 'A-Z', '0-9', '_'])) throw new Exception('[货币系统] 数据库表名必须是大小写字母数字下划线组成。');
        $this->_configTable = $tableName;
    }

    /**
     * 设置新配置的积分列
     * @param string $columnName
     * @throws Exception
     */
    public function setConfigCreditsColumn($columnName) {
        if(!Validator::Chars($columnName, ['a-z', 'A-Z', '0-9', '_'])) throw new Exception('[货币系统] 数据库列名必须是大小写字母数字下划线组成。');
        $this->_configTableColumn = $columnName;
    }

    /**
     * 设置新配置的用户列
     * @param string $columnName
     * @throws Exception
     */
    public function setConfigUserColumn($columnName) {
        if(!Validator::Chars($columnName, ['a-z', 'A-Z', '0-9', '_'])) throw new Exception('[货币系统] 数据库用户列名必须是大小写字母数字下划线组成。');
        $this->_configUserColumn = $columnName;
    }

    /**
     * 设置新配置的用户列标识符
     * @param string $type
     * @throws Exception
     */
    public function setConfigUserColumnId($type) {
        if(!Validator::AlphaNumeric($type)) throw new Exception('[货币系统] 用户列标识符无效。');
        if(!in_array($type, $this->_allowedUserColId)) throw new Exception('[货币系统] 用户列标识符无效。');
        $this->_configUserColumnId = $type;
    }
    /**
     * 设置充值链接
     * @param string $link
     * @throws Exception
     */
    public function setConfigBuyLink($link) {
        if(!Validator::Url($link)) throw new Exception('您必须输入有效的链接!');
        $this->_configBuyLink = $link;
    }
    /**
     * 设置新配置的在线检查
     * @param boolean $input
     */
    public function setConfigCheckOnline($input) {
        $this->_configCheckOnline = ($input ? 1 : 0);
    }

    /**
     * 是否在账号个人面板中显示配置
     * @param boolean $input
     */
    public function setConfigDisplay($input) {
        $this->_configDisplay = ($input ? 1 : 0);
    }

    /**
     * 检查配置是否存在于数据库中
     * @param int $input
     * @return boolean
     */
    private function _configurationExists($input) {
        $check = $this->web->query_fetch_single("SELECT * FROM ".X_TEAM_CREDITS_CONFIG." WHERE config_id = ?", array($input));
        if($check) return true;
        return false;
    }

    /**
     * 将新配置插入数据库
     * @throws Exception
     */
    public function saveConfig() {
        if(!$this->_configTitle) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configTable) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configTableColumn) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configUserColumn) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configUserColumnId) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configBuyLink) throw new Exception('您必须输入有效的链接地址');

        $data = [
            'title' => $this->_configTitle,
            'database' => $this->_configDatabase,
            'table' => $this->_configTable,
            'creditsColumn' => $this->_configTableColumn,
            'userColumn' => $this->_configUserColumn,
            'userColumnId' => $this->_configUserColumnId,
            'link'  => $this->_configBuyLink,
            'checkonline' => $this->_configCheckOnline,
            'display' => $this->_configDisplay
        ];

        $query = "INSERT INTO ".X_TEAM_CREDITS_CONFIG." "
            . "(config_title, config_database, config_table, config_credits_col, config_user_col, config_user_col_id, config_buy_link, config_checkonline, config_display) "
            . "VALUES "
            . "(:title, :database, :table, :creditsColumn, :userColumn, :userColumnId, :link, :checkonline, :display)";

        $saveConfig = $this->web->query($query, $data);
        if(!$saveConfig) throw new Exception('[货币系统] 将配置添加到数据库时出错，检查数据库错误。');
        message('success','您的配置已经保存成功!');
    }

    /**
     * editConfig
     * 从数据库中编辑配置
     * @throws Exception
     */
    public function editConfig() {
        if(!$this->_configId) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configTitle) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configTable) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configTableColumn) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configUserColumn) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configUserColumnId) throw new Exception('[货币系统] 请填写所有必填字段。');
        if(!$this->_configBuyLink) throw new Exception('您必须输入有效的链接地址');

        $data = [
            'id' => $this->_configId,
            'title' => $this->_configTitle,
            'database' => $this->_configDatabase,
            'table' => $this->_configTable,
            'TableColumn' => $this->_configTableColumn,
            'UserColumn' => $this->_configUserColumn,
            'UserColumnId' => $this->_configUserColumnId,
            'link'  => $this->_configBuyLink,
            'checkonline' => $this->_configCheckOnline,
            'display' => $this->_configDisplay
        ];

        $query = "UPDATE ".X_TEAM_CREDITS_CONFIG." SET "
            . "config_title = :title, "
            . "config_database = :database, "
            . "config_table = :table, "
            . "config_credits_col = :TableColumn, "
            . "config_user_col= :UserColumn, "
            . "config_user_col_id = :UserColumnId,"
            . "config_buy_link = :link, "
            . "config_checkonline = :checkonline, "
            . "config_display = :display "
            . "WHERE config_id = :id";

        $editConfig = $this->web->query($query, $data);
        if(!$editConfig) throw new Exception('[货币系统] 编辑配置时出错，检查数据库错误。');
        message('success','您的配置已经修改成功!');
    }

    /**
     * 删除配置
     * 从数据库中删除配置
     * @throws Exception
     */
    public function deleteConfig() {
        if(!$this->_configId) throw new Exception('[货币系统] 您尚未设置配置ID。');
        if(!$this->web->query("DELETE FROM ".X_TEAM_CREDITS_CONFIG." WHERE config_id = ?", array($this->_configId))) {
            throw new Exception('[货币系统] 删除配置时出错，检查数据库错误。');
        }
    }

    /**
     * 显示货币配置
     * 从数据库返回全部或单个配置
     * @param bool $singleConfig
     * @return array|bool|mixed|null
     * @throws Exception
     */
    public function showConfigs($singleConfig = false) {
        if($singleConfig) {
            if(!$this->_configId) throw new Exception('[货币系统] 您尚未设置配置ID。');
            return $this->web->query_fetch_single("SELECT * FROM [".X_TEAM_CREDITS_CONFIG."] WHERE config_id = ?", array($this->_configId));
        } else {
            $result = $this->web->query_fetch("SELECT * FROM [".X_TEAM_CREDITS_CONFIG."] ORDER BY config_id ASC");
            if($result) return $result;
            return false;
        }
    }

    /**
     * 使用所有配置构建选择输入
     * @param string $name
     * @param int $default
     * @param string $class
     * @return string
     * @throws Exception
     */
    public function buildSelectInput($name="config", $default=1, $class="") {
        $selectName = (Validator::Chars($name, ['a-z', 'A-Z', '0-9', '_']) ? $name : "config");
        $selectedOption = (Validator::UnsignedNumber($default) ? $default : 1);
        $configs = $this->showConfigs();
        $return = ($class ? '<select name="'.$selectName.'" class="'.$class.'">' : '<select name="'.$selectName.'">');
        if(is_array($configs)) {
            if($default == 0) {
                $return .= '<option value="0" selected>空</option>';
            } else {
                //$return .= '<option value="0">空</option>';
                $return .= '';
            }
            foreach($configs as $config) {
                if($selectedOption == $config['config_id']) {
                    $return .= '<option value="'.$config['config_id'].'" selected>'.$config['config_title'].'</option>';
                } else {
                    $return .= '<option value="'.$config['config_id'].'">'.$config['config_title'].'</option>';
                }
            }
        } else {
            $return .= '<option value="0" selected>空</option>';
        }
        $return .= '</select>';
        return $return;
    }

    /**
     * _isOnline
     * 检查帐户是否在线
     * @param string $input
     * @return boolean
     * @throws Exception
     */
    private function _isOnline($input) {
        if(!$this->_identifier) throw new Exception('[货币系统] 标识符未设置，无法检查在线状态。');
        switch($input) {
            case 'userid':
                # 使用ID获取帐户信息
                $accountInfo = $this->common->getUserInfoForUserGID($_SESSION['group'],$this->_identifier);
                if(!$accountInfo) throw new Exception('无法检索您的账号信息，请稍后再试。');

                # 检查在线状态
                return $this->common->checkUserOnline($_SESSION['group'],$accountInfo[_CLMN_USERNM_]);
                break;
            case 'username':
                # 检查在线状态
                return $this->common->checkUserOnline($_SESSION['group'],$this->_identifier);
                break;
//            case 'email':
//                # 使用电子邮件获取帐户 ID
//                $userId = $this->common->getUserGIDForEmail($_SESSION['group'],$this->_identifier);
//                if(!$userId) throw new Exception('无法检索您的账号信息，请稍后再试。');
//
//                # 使用 id 获取帐户信息
//                $accountInfo = $this->common->getUserInfoForUserGID($_SESSION['group'],$userId);
//                if(!$accountInfo) throw new Exception('无法检索您的账号信息，请稍后再试。');
//
//                # 检查在线状态
//                return $this->common->checkUserOnline($_SESSION['group'],$accountInfo[_CLMN_USERNM_]);
//                break;
            case 'character':
                # 获取账号信息从角色名
                $characterData = $this->character->getCharacterDataForCharacterName($_SESSION['group'],$this->_identifier);
                if(!$characterData) throw new Exception('无法检索您的账号信息，请稍后再试。');

                # 检查在线状态
                return $this->common->checkUserOnline($_SESSION['group'],$characterData[_CLMN_CHR_ACCID_]);
                break;
            default:
                throw new Exception('[货币系统] 标识符未设置，无法检查在线状态。');
        }
    }

    /**
     * 保存货币交易记录
     * @param string $configTitle
     * @param int $credits
     * @param string $transaction
     * @return bool
     */
    private function _addLog($configTitle="unknown", $credits=0, $transaction="unknown") {
        $inAdminCp = access == 'admincp' ? 1 : 0;
        if($inAdminCp == 1) {
            $module = $_GET['module'];
        } else {
            $module = $_GET['page'] . '/' . $_GET['subpage'];
        }
        $ip = (check_value($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0');

        $data = [
            'config' => $configTitle,
            'credits' => $credits,
            'log_date' => logDate(),
            'inAdminCp' => $inAdminCp,
        ];

        $query = "INSERT INTO [".X_TEAM_CREDITS_LOGS."] "
            . "(log_config, log_identifier, log_credits, log_transaction, log_date, log_inadmincp, log_module, log_ip) "
            . "VALUES "
            . "(:config, '".$this->_identifier."', :credits, '".$transaction."', :log_date, :inAdminCp, '".$module."', '".$ip."')";

        $saveLog = $this->web->query($query, $data);
        if(!$saveLog) return false;
        return true;
    }

    /**
     * 获取日志列表
     * @param int $limit
     * @param string $DESC #ASC
     * @return array|bool|void|null
     */
    public function getLogs($limit=50,$DESC = 'DESC') {
        $query = str_replace(['{LIMIT}'], [$limit], "SELECT TOP {LIMIT} * FROM [".X_TEAM_CREDITS_LOGS."] ORDER BY [log_date] ".$DESC."");
        $result = $this->web->query_fetch($query);
        if(is_array($result)) return $result;
        return;
    }

    /**
     * 获取积分
     * 返回用户的可用积分
     * @param $group
     * @return mixed
     * @throws Exception
     */
    public function getCredits($group) {
        if(!$this->_configId) throw new Exception('[货币系统] 您尚未设置配置ID。');
        if(!$this->_identifier) throw new Exception('[货币系统] 您尚未设置配置ID。');

        # 获取配置
        $config = $this->showConfigs(true);
        # 选择数据库
        $database = ($config['config_database'] == "MuOnline" ? "MuOnline" : "Me_MuOnline");

        # 建立查询
        $data = [
            'identifier' => $this->_identifier
        ];
        $variables = ['{TABLE}','{COLUMN}','{USER_COLUMN}'];
        $values = [$config['config_table'], $config['config_credits_col'], $config['config_user_col']];
        $query = str_replace($variables, $values, "SELECT {COLUMN} FROM {TABLE} WHERE {USER_COLUMN} = :identifier");
        # 获取货币配置
        $getCredits = Connection::Database($database,$group)->query_fetch_single($query, $data);

        if(!$getCredits) throw new Exception("[货币系统] 无法获取您的".$config['config_title']."额度，请确保您有足够".$config['config_title']."。");

        return $getCredits[$config['config_credits_col']];
    }


}