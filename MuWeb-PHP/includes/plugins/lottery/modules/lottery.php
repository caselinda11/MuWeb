<?php
/**
 * 在线抽奖模块页面
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
            <li class="breadcrumb-item active" aria-current="page">在线夺宝</li>
        </ol>
    </nav>
<?php
try {
    if (!isLoggedIn()) redirect(1, 'login');
    $lottery = new \Plugin\lottery();
    $tConfig = $lottery->loadConfig('config');
    if(!$tConfig['active']) throw new Exception('该功能暂未启用，请稍后再试或联系在线客服！');
    //积分 or 元宝
    try{
        $creditSystem = new CreditSystem();
        $creditSystem->setConfigId($tConfig['credit_type']);
        $creditSystem->setIdentifier($_SESSION['username']);
        $configCredits = $creditSystem->getCredits($_SESSION['group']);
    }catch (Exception $exception){
        message('error',$exception->getMessage());
    }
    ?>

    <div class="card mt-3 mb-3" style="min-width: 730px">
        <div class="card-header">在线夺宝</div>
        <div class="card-body">
            <?if($configCredits < $tConfig['price']){
                message('error',"您的".getPriceType($tConfig['credit_type'])."不足，无法参与此活动！");
            }?>
            <div class=" mb-3">
                <div class="btn-group col-md-12" role="group">
                    <a href="<?=__BASE_URL__?>usercp/lotteryShop" class="btn btn-outline-dark">稀有商店</a>
                    <a href="<?=__BASE_URL__?>usercp/lotteryLog" class="btn btn-outline-dark">夺宝记录</a>
                </div>
            </div>

           <style type="text/css">
                .luckyDraw {
                    position: relative;
                    box-sizing: border-box;
                    width: 690px;
                    height: 512px;
                    margin: auto;
                    padding: 55px 0 0 45px;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/lucky_draw_bg.png') top center no-repeat;
                    background-size: cover;
                }

                .record {
                    position: absolute;
                    width: 60px;
                    height: 23px;
                    line-height: 22px;
                    top: 30px;
                    left: 180px;
                    text-align: left;
                    font-size: 13px;
                    color: #d89e23;
                }
                .record_money{
                    position: absolute;
                    width: 107px;
                    height: 23px;
                    line-height: 22px;
                    top: 30px;
                    left: 244px;
                    text-align: left;
                    font-size: 13px;
                    color: #d89e23;
                }
                .prize {
                    position: absolute;
                    width: 185px;
                    height: 24px;
                    line-height: 22px;
                    padding-left: 75px;
                    top: 30px;
                    left: 175px;
                    text-align: left;
                    font-size: 13px;
                    color: #e6e2d4;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/prize.png') top center no-repeat;
                    background-size: cover;
                }

                .money {
                    position: absolute;
                    width: 180px;
                    height: 24px;
                    line-height: 22px;
                    padding-left: 40px;
                    padding-right: 3px;
                    top: 30px;
                    left: 463px;
                    text-align: left;
                    font-size: 13px;
                    color: #d89e23;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/money.png') top center no-repeat;
                    background-size: cover;
                }

                .luck-val {
                    position: absolute;
                    width: 131px;
                    height: 23px;
                    line-height: 25px;
                    top: 155px;
                    left: 165px;
                    padding-left: 6px;
                    text-align: left;
                    font-size: 12px;
                    color: #d89e23;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/luck_val_bg.png') top center no-repeat;
                    background-size: cover;
                }

                #lottery {
                    position: absolute;
                    width: 100%;
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }

                #lottery li {
                    width: 120px;
                    height: 100px;
                    box-sizing: border-box;
                    text-align: center;
                    line-height: 115px;
                    position: absolute;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/lucky_draw_item_list.png') top center no-repeat;
                    background-size: cover;
                }

                #lottery li:nth-of-type(1) {
                    left: 0;
                    top: 0;
                }

                #lottery li:nth-of-type(1) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #d89e23;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(2) {
                    left: 120px;
                    top: 0;
                }

                #lottery li:nth-of-type(2) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(3) {
                    left: 240px;
                    top: 0;
                }

                #lottery li:nth-of-type(3) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(4) {
                    left: 360px;
                    top: 0;
                }

                #lottery li:nth-of-type(4) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(5) {
                    left: 480px;
                    top: 0;
                }

                #lottery li:nth-of-type(5) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(6) {
                    left: 480px;
                    top: 100px;
                }

                #lottery li:nth-of-type(6) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(7) {
                    left: 480px;
                    top: 200px;
                }

                #lottery li:nth-of-type(7) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(8) {
                    left: 480px;
                    top: 300px;
                }

                #lottery li:nth-of-type(8) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(9) {
                    left: 360px;
                    top: 300px;
                }

                #lottery li:nth-of-type(9) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(10) {
                    left: 240px;
                    top: 300px;
                }

                #lottery li:nth-of-type(10) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(11) {
                    left: 120px;
                    top: 300px;
                }

                #lottery li:nth-of-type(11) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(12) {
                    left: 0;
                    top: 300px;
                }

                #lottery li:nth-of-type(12) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(13) {
                    left: 0;
                    top: 200px;
                }

                #lottery li:nth-of-type(13) span {
                    position: absolute;
                    width: 120px;
                    top: 0;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(14) {
                    left: 0;
                    top: 100px;
                }

                #lottery li:nth-of-type(14) span {
                    position: absolute;
                    width: 120px;
                    left: 0;
                    line-height: 25px;
                    color: #e6e2d4;
                    font-size: 12px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(15) {
                    left: 165px;
                    top: 190px;
                    width: 119px;
                    height: 39px;
                    cursor: pointer;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/newui_btn_empty_big.jpg') top center no-repeat;
                    background-size: cover;
                    text-align: center;
                    line-height: 39px;
                    color: #e6e2d4;
                    font-size: 13px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(15):hover {
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/newui_btn_empty_big.jpg') no-repeat;
                    background-position:0 -39px;
                    background-size: cover;
                    color: #e6e2d4;
                }

                #lottery li:nth-of-type(15):active {
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/newui_btn_empty_big.jpg') no-repeat;
                    background-position:0 -78px;
                    background-size: cover;
                    color: #e6e2d4;
                }

                #lottery li:nth-of-type(16) {
                    left: 320px;
                    top: 190px;
                    width: 119px;
                    height: 39px;
                    cursor: pointer;
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/newui_btn_empty_big.jpg') top center no-repeat;
                    background-size: cover;
                    text-align: center;
                    line-height: 39px;
                    color: #e6e2d4;
                    font-size: 13px;
                    text-decoration: none !important;
                }

                #lottery li:nth-of-type(16):hover {
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/newui_btn_empty_big.jpg') no-repeat;
                    background-position:0 -39px;
                    background-size: cover;
                    color: #e6e2d4;
                }

                #lottery li:nth-of-type(16):active {
                    background: url('<?=__PATH_PUBLIC_IMG__?>/lottery/newui_btn_empty_big.jpg') no-repeat;
                    background-position:0 -78px;
                    background-size: cover;
                    color: #e6e2d4;
                }


                #lottery .active:after {
                    position: absolute;
                    top: 0;
                    left: 0;
                    content: "";
                    width: 100%;
                    height: 100%;
                    border: 2px solid #ff2c2c;
                    border-radius: 2px;
                }
            </style>

            <div class="luckyDraw">
                <span class="prize"></span>
                <span class="record"><?=$tConfig['Crystal_Name']?>:</span>
                <span class="record_money">
                    <span class="float-right">
                        当前拥有 <span class="text-danger" id="record_money"><?=$lottery->getCountLotteryLog($_SESSION['username']);?></span> 颗
                    </span>
                </span>
                <a href="<?=__BASE_URL__?>usercp/creditExchange">
                <span class="money">
                    <span class="float-right">当前余额: <span id="credit_money" class="text-danger"><?=number_format($configCredits)?></span> <?=getPriceType($tConfig['credit_type'])?></span>
                </span>
                </a>
                <ul id="lottery">
                    <li class="lottery-unit lottery-unit-0">
                        <a href="<?=__BASE_URL__?>usercp/lotteryShop">
                            <img src="<?=__PATH_PUBLIC_IMG__?>lottery/diamonds.png" alt="暂无图片" data-toggle="tooltip" data-placement="auto" data-html="true" title="" data-original-title="<span class=&quot;yellow-color&quot;><?=$tConfig['Crystal_Name']?></span><br><span>点击打开可以兑换稀有物品</span><br><br>">
                            <span class="item-name text-danger"><?=$tConfig['Crystal_Name']?></span>
                        </a>
                    </li>
                    <?for($i=1;$i<=13;$i++){?>
                    <li class="lottery-unit lottery-unit-<?=$i?>">
                        <?$item = new \Plugin\equipment()?>
                        <img src="<?=$item->ItemsUrl($tConfig['reward_item_code_'.$i])?>" alt="暂无图片" data-toggle="tooltip" data-placement="auto" data-html="true" title="" data-original-title="<?=$tConfig['reward_item_option_'.$i]?>">
                        <span class="item-name"><?=$tConfig['reward_item_name_'.$i]?></span>
                    </li>
                    <?}?>
                    <li id="me"><?=$tConfig['price']?><?=getPriceType($tConfig['credit_type'])?> 买1次</li>
                    <li id="me_many"><?=$tConfig['many_price']?><?=getPriceType($tConfig['credit_type'])?> 买<?=($tConfig['many_number']) ? '5' : '10'?>次</li>
                </ul>
                <span class="luck-val">当前幸运值：<span id="luck_money"><?=$lottery->getCountLuckLotteryLog($_SESSION['username'])?></span></span>
            </div>

            <script type="text/javascript">
                var lottery = {
                    index: 0, //当前转动到哪个位置，起点位置
                    count: 14, //总共有多少个位置
                    timer: 0, //setTimeout的ID，用clearTimeout清除
                    speed: 20, //初始转动速度
                    times: 0, //转动次数
                    cycle: 50, //转动基本次数：即至少需要转动多少次再进入抽奖环节
                    prize: 0, //中奖位置
                    init: function(id) {
                        if ($("#" + id).find(".lottery-unit").length > 0) {
                            $lottery = $("#" + id);
                            $units = $lottery.find(".lottery-unit");
                            this.obj = $lottery;
                            this.count = $units.length;
                            $lottery.find(".lottery-unit-" + this.index).addClass("active");

                        }
                    },
                    roll: function() {
                        var index = this.index;
                        var count = this.count;
                        var lottery = this.obj;
                        $(lottery).find(".lottery-unit-" + index).removeClass("active");
                        index += 1;
                        if (index > count -1) {
                            index = 0;
                        }
                        $(lottery).find(".lottery-unit-" + index).addClass("active");
                        music2();
                        this.index = index;
                        return false;
                    },
                    stop: function(index) {
                        this.prize = index;
                        return false;
                    }
                };

                function roll() {
                    lottery.times += 1;
                    lottery.roll();
                    var prize_site = $("#lottery").attr("prize_site");
                    if(prize_site > 0) prize_site -= 1;
                    if (lottery.times > lottery.cycle + 10 && lottery.index == prize_site) {
                        var prize_name = $("#lottery").attr("prize_name");
                        var record_money = $("#lottery").attr("record_money");
                        modal_msg("<div class='text-center'><h5>恭喜您中奖["+prize_name+"！</h5></div>");
                        $("#record_money").html(record_money);//转完+1
                        clearTimeout(lottery.timer);
                        lottery.prize = -1;
                        lottery.times = 0;
                        click = false;
                    } else {
                        if (lottery.times < lottery.cycle) {
                            lottery.speed -= 10;
                        } else if (lottery.times == lottery.cycle) {
                            var index = Math.random() * (lottery.count) | 0;
                            lottery.prize = index;
                        } else {
                            if (lottery.times > lottery.cycle + 10 && ((lottery.prize == 0 && lottery.index == 7) || lottery.prize == lottery.index + 1)) {
                                lottery.speed += 110;
                            } else {
                                lottery.speed += 20;
                            }
                        }
                        if (lottery.speed < 40) {
                            lottery.speed = 40;
                        }
                        lottery.timer = setTimeout(roll, lottery.speed);
                    }
                    return false;
                }

                var click = false;

                $(function() {
                    lottery.init('lottery');
                    var gtitle = '';
                    var gmessage = '';
                    $("#lottery #me").click(function() {
                        if (click) {
                            return false;
                        } else {
                            lottery.speed = 200;
                            $.post("<?=__BASE_URL__?>api/ext/lottery.php", {uid: 1,key:'<?=Token::generateToken('lottery')?>'}, function(data) {
                                if (!data.hasOwnProperty("prize_site")){
                                    //modal_url('<?//=__LOTTERY_HOME__?>//', data);
                                    modal_msg(data);
                                    click = false;
                                    return false;
                                }
                                $("#lottery").attr("prize_site", data.prize_site);
                                $("#lottery").attr("prize_name", data.prize_name);
                                $("#lottery").attr("record_money", data.record_money);
                                gtitle = data.title;
                                gmessage = data.message;
                                $("#credit_money").html(data.credit_money);
                                $("#luck_money").html(data.luck_money);
                                $.post("<?=__BASE_URL__?>api/ext/message.php", {uid:1,title:gtitle,message:gmessage});
                                roll();
                                click = true;
                                return false;

                            }, "json")
                        }
                    });
                    $("#lottery #me_many").click(function() {
                        if (click) {
                            return false;
                        } else {
                            lottery.speed = 200;
                            $.post("<?=__BASE_URL__?>api/ext/lottery.php", {uid: 2,key:'<?=Token::generateToken('lottery')?>'}, function(data) {
                                if (!data.hasOwnProperty("prize_site")){
                                    //modal_url('<?//=__LOTTERY_HOME__?>//', data);
                                    modal_msg(data);
                                    click = false;
                                    return false;
                                }
                                $("#lottery").attr("prize_site", data.prize_site);
                                $("#lottery").attr("prize_name", data.prize_name);
                                $("#lottery").attr("record_money", data.record_money);
                                gtitle = data.title;
                                gmessage = data.message;
                                $("#credit_money").html(data.credit_money);
                                $("#luck_money").html(data.luck_money);
                                $.post("<?=__BASE_URL__?>api/ext/message.php", {uid:1,title:gtitle,message:gmessage});
                                roll();
                                click = true;
                                return false;
                            }, "json")
                        }
                    });
                });

                window.AudioContext = window.AudioContext || window.webkitAudioContext;
                var audioCtx = new AudioContext();//实例化AudioContext对象
                // 发出的声音频率数据，表现为音调的高低
                var arrFrequency = [196.00, 220.00, 246.94, 261.63, 293.66, 329.63, 349.23, 392.00, 440.00, 493.88, 523.25, 587.33, 659.25, 698.46, 783.99, 880.00, 987.77, 1046.50];
                // 音调依次递增或者递减处理需要的参数
                var start = 0, direction = 1;

                function music2() {
                    //当前频率
                    var frequency = arrFrequency[start];
                    //如果到头，改变音调的变化规则
                    if (!frequency) {
                        direction = -1 * direction;
                        start = start + 2 * direction;
                        frequency = arrFrequency[start];
                    }
                    //改变索引
                    start = start + direction;
                    //创建一个oscillator，它表示一个周期性波形震荡，基本上来说创造了一个音调
                    var oscillator = audioCtx.createOscillator();
                    //创建一个gainNode，控制音频的总音量
                    var gainNode = audioCtx.createGain();
                    //把音量，音调和终结点进行关联
                    oscillator.connect(gainNode);
                    //audioCtx.destination返回audioDestinationNode对象，表示当前audio context中所有节点的最终节点，表示音频渲染设备
                    gainNode.connect(audioCtx.destination);
                    //指定音调的类型
                    oscillator.type = 'sine';
                    //设置当前播放声音的频率，也就是最终播放声音的调调
                    oscillator.frequency.value = frequency;
                    //当前时间音量设置为0
                    gainNode.gain.setValueAtTime(0, audioCtx.currentTime);
                    //0.01秒后音量为1
                    gainNode.gain.linearRampToValueAtTime(1, audioCtx.currentTime + 0.01);
                    //音调从当前时间开始播放
                    oscillator.start(audioCtx.currentTime);
                    //一秒内声音慢慢降低
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1);
                    //1秒后声音完全停止
                    oscillator.stop(audioCtx.currentTime + 1);
                };
            </script>

            <div class="mt-5">
                <p>常见问题解答:</p>
                <ol style="word-break: break-all;">
                    <li>幸运值有什么用？</li>
                    <p class="alert alert-info">每次转动，会增加1点幸运值，当幸运值达到100时，必出“<?=$tConfig['Crystal_Name']?>”。</p>
                    <li>怎么查看中奖历史记录？</li>
                    <p class="alert alert-info">点击顶部“查看记录”按钮进入后久可以看见中奖历史记录，并且可以领取中奖得物品。</p>
                    <li><?=$tConfig['Crystal_Name']?>有什么用？</li>
                    <p class="alert alert-info">点击稀有商城或转盘中<?=$tConfig['Crystal_Name']?>图标，进入稀有物品兑换界面，里面有很多稀世珍宝。</p>
                </ol>
            </div>
        </div>
    </div>

<?php
} catch(Exception $ex) {
	message('error', $ex->getMessage());
}