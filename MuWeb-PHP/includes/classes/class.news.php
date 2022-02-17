<?php
/**
 * 新闻类相关函数
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

class News {
	
	private $_configFile = 'news';
	private $_shortNewsCharLimit = 10; //截断新闻长度

	private $_id;
	private $_title;
	private $_content;
    private $MuWebLib;

    /**
     * News constructor.
     * @throws Exception
     */
	function __construct() {
		$config = loadConfigurations($this->_configFile);

		#截断新闻长度
		$this->_shortNewsCharLimit = $config['news_short_char_limit'];
        $this->MuWebLib = Connection::Database('Web');
	}

    /**
     * @param $id
     */
	public function setId($id) {
		if(!Validator::UnsignedNumber($id)) return;
		$this->_id = $id;
	}

    /**
     * @param $title
     */
	public function setTitle($title) {
		if(!check_value($title)) return;
		$this->_title = $title;
	}

    /**
     * @param $content
     */
	public function setContent($content) {
		if(!check_value($content)) return;
		$this->_content = $content;
	}

    /**
     * @param $title
     * @param $titleColor
     * @param $sort
     * @param $type
     * @param $typeColor
     * @param $content
     * @param $status
     * @param string $author
     * @param int $comments
     * @return bool
     * @throws Exception
     */
	function addNews($title,$titleColor,$sort,$type,$typeColor,$content,$status,$author='Administrator',$comments=1) {
		if(!check_value($title) && !check_value(!$content)&& !check_value(!$author))  throw new Exception("所有内容都是必填的。");
        if (!$this->checkTitle($title)) throw new Exception("新闻标题必须是大于4个字节且小于80个字节");
        if (!$this->checkContent($content)) throw new Exception("新闻内容必须大于4个字节。");
		// 确保评论为1或0
        if($comments < 0 || $comments > 1) $comments = 1;
        // 收集数据
        $news_data = [
            $sort,
            $author,
            logDate(),
            $comments,
            $type,
            $titleColor,
            $typeColor,
            $status
        ];
        // 添加新闻
        $add_news = $this->MuWebLib->query("INSERT INTO [".X_TEAM_NEWS."] (news_title,news_content,sort,news_author,news_date,allow_comments,news_type,title_color,type_color,status) VALUES ('".$title."','".$content."',?,?,?,?,?,?,?,?)", $news_data);
        if($add_news) return true;
        return false;
	}

    /**
     * @param $id
     * @return bool
     */
	function removeNews($id) {
		if(!Validator::Number($id)) return false;
		if (!$this->newsIdExists($id)) return false;
		$remove = $this->MuWebLib->query("DELETE FROM [" . X_TEAM_NEWS . "] WHERE [news_id] = ?", [$id]);
		if ($remove) {
		    $this->setId($id);
		    return true;
		}
		return false;
	}

    /***
     * @param $id
     * @param $title
     * @param $title_color
     * @param $sort
     * @param $type
     * @param $type_color
     * @param $content
     * @param $author
     * @param $comments
     * @param $date
     * @param $status
     * @return bool
     * @throws Exception
     */
	function editNews($id,$title,$title_color,$sort,$type,$type_color,$content,$author,$comments,$date,$status){
	    if (!check_value($id) && !check_value($title) && !check_value($content) && !check_value($author) && !check_value($comments) && !check_value($date)) throw new Exception("所有内容都是必填的。");
	    if (!$this->newsIdExists($id)) throw new Exception("新闻ID不存在，请重新打开该页面。");
	    if (!$this->checkTitle($title)) throw new Exception("新闻标题必须是大于4个字节且小于80个字节");
        if (!$this->checkContent($content)) throw new Exception("新闻内容必须大于4个字节。");
        $editData = [
                    $sort,
                    $author,
                    $date,
                    $comments,
                    $type,
                    $title_color,
                    $type_color,
                    $status,
                    $id,
                ];
         $query = $this->MuWebLib->query("UPDATE [" . X_TEAM_NEWS . "] SET news_title = '".$title."', news_content = '".$content."', sort = ?, news_author = ?, news_date = ?, allow_comments = ?,news_type = ? ,title_color = ?,type_color = ?,status = ? WHERE news_id = ?", $editData);
         if ($query) return true;
        return false;
    }

    /**
     * @param $title
     * @return bool
     */
	function checkTitle($title) {
		if(!check_value($title)) return false;
		if(strlen($title) < 4 || strlen($title) > 80) return false;
        return true;
	}

    /**
     * @param $content
     * @return bool
     */
	function checkContent($content) {
		if(!check_value($content)) return false;
		if(strlen($content) < 4) return false;
        return true;
	}

    /**
     * 获取新闻列表
     * @return array|bool|null
     */
	function getNewsList() {
		$news = $this->MuWebLib->query_fetch("SELECT * FROM [".X_TEAM_NEWS."] ORDER BY [sort] ASC");
		if(!is_array($news)) return null;
        return $news;
	}

    /**
     * 验证新闻是否存在
     * @param $id
     * @return bool|void
     */
	function newsIdExists($id) {
		if(!Validator::UnsignedNumber($id)) return;
		$cachedNews = loadCache('news.cache');
		if(!is_array($cachedNews)) return;
		foreach($cachedNews as $cacheData) {
			if($cacheData['news_id'] == $id) return true;
		}
		return;
	}

    /**
     * 删除新闻缓存文件
     */
	function deleteNewsFiles() {
		$files = glob(__PATH_INCLUDES_CACHE_NEWS__.'*');
		foreach($files as $file) {
			if(is_file($file)) {
				unlink($file);
			}
		}
	}

    /**
     * 缓存新闻
     * @return bool
     */
	function cacheNews() {
		if(!$this->checkNewsDirWritable()) return false;
        $news_list = $this->getNewsList();
        $this->deleteNewsFiles();
        if(!is_array($news_list)) return false;
        foreach($news_list as $news) {
            $handle = fopen(__PATH_INCLUDES_CACHE_NEWS__."news_".$news['news_id'].".cache", "w");
            fwrite($handle, $news['news_content']);
            fclose($handle);
        }
        return true;
	}

    /**
     * 检查新闻路径是否可写
     * @return bool
     */
	function checkNewsDirWritable() {
		if(is_writable(__PATH_INCLUDES_CACHE_NEWS__)) return true;
        return false;
	}

    /**
     * 更新新闻缓存
     * @return bool
     */
	function updateNewsCacheIndex() {
		$newsList = $this->getNewsList();
		if(!is_array($newsList)) {
			updateCacheFile('news.cache', '');
			return true;
		}
		$cacheData = encodeCache($newsList);
		$updateCache = updateCacheFile('news.cache', $cacheData);
		if(!$updateCache) return false;
		return true;
	}

    /**
     * 加载新闻内容
     * @return false|string|void
     */
	function LoadCachedNews() {
		if(!check_value($this->_id)) return;
		if(!Validator::UnsignedNumber($this->_id)) return;
		// 加载常规新闻缓存
		$file = __PATH_INCLUDES_CACHE_NEWS__ .'news_'.$this->_id.'.cache';
		if(!file_exists($file)) return;
		$content = file_get_contents($file);
		if(!$content) return;
		return $content;
	}

    /**
     * 加载新闻数据
     * @param $id
     * @return mixed|void|null
     */
	function loadNewsData($id) {
		if(check_value($id) && $this->newsIdExists($id)) {
			$query = $this->MuWebLib->query_fetch_single("SELECT * FROM [".X_TEAM_NEWS."] WHERE news_id = ?", [$id]);
			if($query && is_array($query)) {
				return $query;
			}
		}
        return;
	}

    /**
     *  SnakeDrak
     *  https://stackoverflow.com/a/39569929
     * @param $newsData
     * @return string|string[]|null
     */
    private function _getShortVersion($newsData) {
        $value = html_entity_decode($newsData);
        if(mb_strwidth($value,'UTF-8') <= $this->_shortNewsCharLimit) {
            return $value;
        }
        do {
            $len = mb_strwidth( $value, 'UTF-8' );
            $len_stripped = mb_strwidth( strip_tags($value), 'UTF-8' );
            $len_tags = $len - $len_stripped;
            $value = mb_strimwidth($value, 0, $this->_shortNewsCharLimit+$len_tags, '', 'UTF-8');
        } while( $len_stripped > $this->_shortNewsCharLimit);
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $value, LIBXML_HTML_NODEFDTD);
        $value = $dom->saveHtml($dom->getElementsByTagName('body')->item(0));
        $value = mb_strimwidth($value, 6, mb_strwidth($value, 'UTF-8') - 13, '', 'UTF-8');
        return preg_replace('/<(\w+)\b(?:\s+[\w\-.:]+(?:\s*=\s*(?:"[^"]*"|"[^"]*"|[\w\-.:]+))?)*\s*\/?>\s*<\/\1\s*>/', '', $value);
    }


    /**
     * 更新排序从新闻ID
     * @param $number
     * @return int
     */
    public function setSort($number)
    {
        if(!check_value($this->_id)) return false;
        if(!Validator::UnsignedNumber($this->_id)) return false;
        if(!check_value($number)) return false;
        $update = $this->MuWebLib->query("UPDATE [".X_TEAM_NEWS."] SET [sort] = ? WHERE [news_id] = ?",[$number,$this->_id]);
        if ($update) return true;
        return false;
    }

    /**
     * 更新推荐从新闻ID
     * @param $status
     * @return int
     */
    public function setStatus($status)
    {
        if(!check_value($this->_id)) return false;
        if(!Validator::UnsignedNumber($this->_id)) return false;
        if(!check_value($status)) return false;
        $status = ($status) ? 0 : 1;
        $update = $this->MuWebLib->query("UPDATE [".X_TEAM_NEWS."] SET [status] = ? WHERE [news_id] = ?",[$status,$this->_id]);
        if ($update) return true;
        return false;
    }

}