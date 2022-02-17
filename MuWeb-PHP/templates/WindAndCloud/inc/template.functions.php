 <?php
/**
 * 主题模版函数库
 *
 * @author      mason X<83213956@qq.com>
 * @version     2.0.0
 *
 **/

/**
 * 导航菜单
 * @throws Exception
 */
function templateBuildNavbar() {
    $cfg = loadConfig('navbar');    //加载数据文件
    if(!is_array($cfg)) return;
    echo '<ul class="nav ">';
    foreach($cfg as $element) {
        if(!is_array($element)) continue;
        # 是否开启
        if(!$element['active']) continue;
        # 链接类型
        $link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);

        # 可见度
        if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
        if($element['visibility'] == 'user') if(!isLoggedIn()) continue;

        #活跃
        $active = ($_REQUEST['page'] == $element['link'] ? 'active' : '' );

        # 输出
        if($element['newtab']) {
            echo '<li class="nav-item"><a class="nav-link '.$active.'" href="'.$link.'" target="_blank">'.$element['phrase'].'<p class="text-center navbar-tag d-none d-lg-block visible-lg-block">'.$element['tag'].'</p></a></li>';
        } else {
            echo '<li class="nav-item"><a class="nav-link '.$active.'" href="'.$link.'">'.$element['phrase'].'<p class="text-center navbar-tag d-none d-lg-block visible-lg-block">'.$element['tag'].'</p></a></li>';
        }
    }
    #扩展
    global $extra_menu_link;
    if(isset($extra_menu_link)){
        if(is_array($extra_menu_link)){
            foreach ($extra_menu_link as $item){
                #活跃
                $active = ($_REQUEST['page'] == $item['1'] ? 'active' : '' );
                echo '<li class="nav-item"><a class="nav-link '.$active.'" href="'.__BASE_URL__.$item[1].'">'.$item[0].'<p class="text-center navbar-tag d-none d-lg-block visible-lg-block">'.$item['tag'].'</p></a></li>';
            }
        }
    }
    echo '</ul>';

}



/**
 * 用户面板菜单栏
 * @throws Exception
 */
function templateBuildUsercp() {
    $cfg = loadConfig('usercp');    //加载数据文件
    if(!is_array($cfg)) return;
    echo '<table class="table table-striped text-center">';
    echo '<tbody>';
    echo '<tr class="User">';
        echo '<td colspan="2"><a href="'.__BASE_URL__.'usercp/myaccount"><strong>'.$_SESSION['username'].'</strong></a></td>';
    echo '</tr>';
    foreach($cfg as $element) {
        if(!is_array($element)) continue;
        # 活跃
        if(!$element['active']) continue;
        # 类型
        $link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
        # 可见
        if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
        if($element['visibility'] == 'user') if(!isLoggedIn()) continue;
        # 输出
        if($element['newtab']) {
            echo '<tr class="User"><td><a href="'.$link.'" target="_blank">'.$element['phrase'].'</a></td></tr>';
        } else {
            echo '<tr class="User"><td><a href="'.$link.'">'.$element['phrase'].'</a></td></tr>';
        }
    }
    #扩展
    global $extra_MyAccount_link;
    if(isset($extra_MyAccount_link)){
        if(is_array($extra_MyAccount_link)){
            echo '<tr style="line-height: 15px;"><td class="text-muted" colspan="2">扩展功能</td></tr>';
            foreach ($extra_MyAccount_link as $item){
                foreach ($item as $name=>$link){
                    echo '<tr class="User"><td><a href="'.__BASE_URL__.$link.'">'.$name.'</a></td></tr>';
                }
            }
        }
    }
    echo '</tbody>';
    echo '</table>';

}


/**
 * 用户导航栏方法
 * @param string $class
 * @throws Exception
 */
function templateBuildMyAccount($class='') {
    $cfg = loadConfig('usercp');    //加载数据文件
    if(!is_array($cfg)) return;

    foreach($cfg as $n=>$element) {
        if(!is_array($element)) continue;
        # 活跃
        if(!$element['active']) continue;
        # 类型
        $link = ($element['type'] == 'internal' ? __BASE_URL__ . $element['link'] : $element['link']);
        # 可见
        if($element['visibility'] == 'guest') if(isLoggedIn()) continue;
        if($element['visibility'] == 'user') if(!isLoggedIn()) continue;
        # 输出
        if($element['newtab']){
            echo '<a href="'.$link.'" target="_blank" class="'.$class.'">'.$element['phrase'].'</a>';//新标签
        } else {
            echo '<a class="'.$class.'" href="'.$link.'">'.$element['phrase'].'</a>';
        }
    }

}?>
