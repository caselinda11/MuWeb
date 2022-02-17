<?php
/**
 * 数据库连接基类
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class Connection {

    /**
     * 数据库加载
     * @param string $database
     * @param int $group
     * @return dB|void
     * @throws Exception
     */
	public static function Database($database='',$group=0) {
	switch($database) {
        case 'Web':
            $db = new dB(
                self::_config('SQL_DB_HOST'),
                self::_config('SQL_DB_PORT'),
                self::_config('SQL_DB_NAME'),
                self::_config('SQL_DB_USER'),
                self::_config('SQL_DB_PASS'),
                self::_config('SQL_PDO_DRIVER')
            );
            if($db->dead) {
                if(self::_config('error_reporting')) {
                    throw new Exception($db->error);
                }
                throw new Exception('与网站数据库['.self::_config('SQL_DB_NAME').']连接失败！');
            }
            return $db;
            break;
        case 'MuOnline':
            $db = new dB(
                self::_serverGroup('SERVER_IP',$group),
                self::_serverGroup('SERVER_DB_POST',$group),
                self::_serverGroup('SERVER_DB_NAME',$group),
                self::_serverGroup('SERVER_DB_USER',$group),
                self::_serverGroup('SERVER_DB_PASS',$group),
                self::_config('SQL_PDO_DRIVER')
            );
            if($db->dead) {
                if(self::_config('error_reporting')) {
                    throw new Exception($db->error);
                }
                throw new Exception('与游戏数据库 ('.self::_serverGroup('SERVER_DB_NAME',$group).') 连接失败！');
            }
            return $db;
            break;
		case 'Me_MuOnline':
            if(!self::_serverGroup('SQL_USE_2_DB',$group)) return self::Database('MuOnline',$group);
            $db = new dB(
                self::_serverGroup('SERVER_IP',$group),
                self::_serverGroup('SERVER_DB_POST',$group),
                self::_serverGroup('SERVER_DB2_NAME',$group),
                self::_serverGroup('SERVER_DB_USER',$group),
                self::_serverGroup('SERVER_DB_PASS',$group),
                self::_config('SQL_PDO_DRIVER'));
            if($db->dead) {
                if(self::_config('error_reporting')) {
                    throw new Exception($db->error);
                }
                throw new Exception('与游戏数据库 ('.self::_serverGroup('SERVER_DB2_NAME',$group).') 连接失败！');
            }
            return $db;
            break;
		default:
			return;
		}
	}

    /**
     * 网站数据表加载
     * @param $config
     * @return mixed|void
     * @throws Exception
     */
	private static function _config($config) {
		$Config = webConfigs();
		if(!is_array($Config)) return;
		if(!array_key_exists($config, $Config)) return;
		return $Config[$config];
	}

    /**
     * 游戏数据表加载
     * @param $config
     * @param int $id
     * @return mixed|void
     * @throws Exception
     */
	private static function _serverGroup($config,$id=0){
        $server = localServerGroupConfigs($id);
        if(!is_array($server)) return;
        if(!array_key_exists($config, $server)) return;
        return $server[$config];
    }
	
}