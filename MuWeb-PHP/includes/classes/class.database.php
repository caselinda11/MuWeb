<?php
/**
 * 数据库核心基类
 *  申明:不可随意更改!
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class dB {

	public $error;
	public $ok;
	public $dead;
    private $db;
	private $_enableErrorLogs = true;
	public $sql = '';
	public $dump = false; //输出SQL语句
    /**
     * dB 连接
     * @param $SQLHOST  //地址
     * @param $SQLPORT  //端口
     * @param $SQLDB    //名称
     * @param $SQLUSER  //用户
     * @param $SQLPWD   //密码
     * @param $SQLDRIVER    //协议
     */
    function __construct($SQLHOST, $SQLPORT, $SQLDB, $SQLUSER, $SQLPWD, $SQLDRIVER) {
		try {
			if($SQLDRIVER == 3) {
				#ODBC 传送方式
				$this->db = new PDO("odbc:Driver={SQL Server};Server=".$SQLHOST.";Database=".$SQLDB."; Uid=".$SQLUSER.";Pwd=".$SQLPWD.";");
			} else {
				if($SQLDRIVER == 2) {
                #SQLSEV 传送方式
					$pdo_connect = "sqlsrv:Server=".$SQLHOST.",".$SQLPORT.";Database=".$SQLDB."";
				} else {
				#pdo_dblib 传送方式
					$pdo_connect = 'dblib:host='.$SQLHOST.':'.$SQLPORT.';dbname='.$SQLDB;
				}
				$this->db = new PDO($pdo_connect, $SQLUSER, $SQLPWD);
			}
		} catch (PDOException $e) {
			$this->dead = true;
			$this->error = "PDOException[错误]: ".$e->getMessage();
		}

	}


    /**
     * 查询
     * @param $sql
     * @param string $array
     * @return bool
     */
	public function query($sql, $array='') {
        #如果不是数组把让当做数组处理.
		if(!is_array($array)) $array = [$array];
//        foreach ($array as $string){if(strpos($string,",") || strpos($string,"/") || strpos($string,"\\")){ $this->error = "本次操作已被记录，请勿非法操作！"; return false;}}
        #准备要执行的语句，并返回语句对象
		$query = $this->db->prepare($sql);
		if (!$query) { #错误
			$this->error = $this->trow_error();
			$query->closeCursor();
			return false;
		} else {	#正确
			if($query->execute($array)) {
				$query->closeCursor();
				return true;
			} else {
			    if($this->dump) $query->debugDumpParams();
				$this->error = $this->trow_error($query);
				return false;
			}
		}
	}

    /**
     * 多条查询
     * @param $sql
     * @param string $array
     * @return array|bool|null
     */
	public function query_fetch($sql, $array='') {
		if(!is_array($array)) $array = [$array];
//        foreach ($array as $string){if(strpos($string,",") || (strpos($string,"/")) || strpos($string,"\\")){ $this->error = "本次操作已被记录，请勿非法操作！";return false;}}
        $query = $this->db->prepare($sql);
		if (!$query) {
			$this->error = $this->trow_error();
			$query->closeCursor();
			return false;
		} else {
			if($query->execute($array)) {
				$result = $query->fetchAll(PDO::FETCH_ASSOC);
				$query->closeCursor();
				return (check_value($result)) ? $result : NULL;
			} else {
                if($this->dump) $query->debugDumpParams();
				$this->error = $this->trow_error($query);
				return false;
			}
		}
	}

    /**
     * 单条数据查询
     * @param $sql
     * @param string $array
     * @return mixed|null
     */
	public function query_fetch_single($sql, $array='') {
		$result = $this->query_fetch($sql, $array);
		return (isset($result[0])) ? $result[0] : NULL;
	}

    /**
     * 输出错误信息
     * @param string $state
     * @return string
     */
	private function trow_error($state='') {
		if(!check_value($state)) {
			$error = $this->db->errorInfo();
		} else {
			$error = $state->errorInfo();
		}
		$errorMessage = '[SQL '.$error[0].'] ['.$this->db->getAttribute(PDO::ATTR_DRIVER_NAME).' '.$error[1].'] > '.$error[2];
		if($this->_enableErrorLogs) @error_log("[".date('Y-m-d h:i:s', time())."] ".$errorMessage. "\r\n", 3, X_TEAM_DATABASE_ERRORLOG);
		return $errorMessage;
	}

    /************************************************************/
    /**
     * 事务启动
     */
    public function beginTransaction(){
        #开启异常处理
        $this->db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        #开始一个事务，关闭自动提交
        $this->db->beginTransaction();
    }


    /**
     * 自动完成
     */
    public function commit()
    {
        $this->db->commit();
    }

    /**
     * 回滚
     */
    public function rollBack(){

        $this->db->rollBack();
    }

    /************************************************************/
}