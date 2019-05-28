<?php

use PHPMailer\PHPMailer\PHPMailer;
define('SINA_APPKEY', 'test');
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//成功消息
function success_view($result, $msg = 'ok')
{
    $res = array();
    $res['error_code'] = 200;
    $res['msg'] = $msg;
    $res['server_time'] = time();
    $res['data'] = $result;
    return $res;

}

//失败消息
function error_view($code, $msg)
{
    $res = array();
    $res['error_code'] = $code;
    $res['msg'] = $msg;
    $res['server_time'] = time();
    $res['data'] = null;
    return $res;
}

//获取错误码
function get_error_code($server, $model, $bussiness)
{

    $server_code = config('server_error_code.' . $server);
    $model_code = config('model_error_code.' . $model);
    $bussiness_code = config('api_error_code.' . $bussiness);
    $model_length = strlen($model_code);
    $bussiness_length = strlen($bussiness_code);
    if ($model_length == 1) {
        $model_code = '00' . $model_code;
    } elseif ($model_length == 2) {
        $model_code = '0' . $model_code;
    }
    if ($bussiness_length == 1) {
        $bussiness_code = '0' . $bussiness_code;
    }
    return $server_code . $model_code . $bussiness_code;
}

//验证参数是否为空
function check_empty_param($param)
{
    foreach ($param as $v) {
        if (empty($v)) {
            return 0;
        }
    }
    return 1;
}

//发送手机信息
function send_sms($mobile, $msg)
{
    return true;
}

//发送语音验证码
function send_voice_sms($mobile, $code)
{
    return true;
}

//获取字符串中文第一个字符
function getinitial($str)
{
    $asc = ord(substr($str, 0, 1));
    if ($asc < 160) {
        //非中文
        if ($asc >= 48 && $asc <= 57) {
            return '1'; //数字
        } elseif ($asc >= 65 && $asc <= 90) {
            return chr($asc); // A--Z
        } elseif ($asc >= 97 && $asc <= 122) {
            return chr($asc - 32); // a--z
        } else {
            return '~'; //其他
        }
    } else {
        //中文
        $asc = $asc * 1000 + ord(substr($str, 1, 1));
        //获取拼音首字母A--Z
        if ($asc >= 176161 && $asc < 176197) {
            return 'a';
        } elseif ($asc >= 176197 && $asc < 178193) {
            return 'b';
        } elseif ($asc >= 178193 && $asc < 180238) {
            return 'c';
        } elseif ($asc >= 180238 && $asc < 182234) {
            return 'd';
        } elseif ($asc >= 182234 && $asc < 183162) {
            return 'e';
        } elseif ($asc >= 183162 && $asc < 184193) {
            return 'f';
        } elseif ($asc >= 184193 && $asc < 185254) {
            return 'g';
        } elseif ($asc >= 185254 && $asc < 187247) {
            return 'h';
        } elseif ($asc >= 187247 && $asc < 191166) {
            return 'j';
        } elseif ($asc >= 191166 && $asc < 192172) {
            return 'k';
        } elseif ($asc >= 192172 && $asc < 194232) {
            return 'l';
        } elseif ($asc >= 194232 && $asc < 196195) {
            return 'm';
        } elseif ($asc >= 196195 && $asc < 197182) {
            return 'n';
        } elseif ($asc >= 197182 && $asc < 197190) {
            return 'o';
        } elseif ($asc >= 197190 && $asc < 198218) {
            return 'p';
        } elseif ($asc >= 198218 && $asc < 200187) {
            return 'q';
        } elseif ($asc >= 200187 && $asc < 200246) {
            return 'r';
        } elseif ($asc >= 200246 && $asc < 203250) {
            return 's';
        } elseif ($asc >= 203250 && $asc < 205218) {
            return 't';
        } elseif ($asc >= 205218 && $asc < 206244) {
            return 'w';
        } elseif ($asc >= 206244 && $asc < 209185) {
            return 'x';
        } elseif ($asc >= 209185 && $asc < 212209) {
            return 'y';
        } elseif ($asc >= 212209) {
            return 'z';
        } else {
            return '~';
        }
    }
}

function array_sort($array, $key)
{
    //二维数组根据指定的key值排序
    if (is_array($array)) {
        $key_array = null;
        $new_array = null;
        for ($i = 0; $i < count($array); $i++) {
            $key_array[$array[$i][$key]] = $i;
        }
        ksort($key_array);
        $j = 0;
        foreach ($key_array as $k => $v) {
            $new_array[$j] = $array[$v];
            $j++;
        }
        unset($key_array);
        return $new_array;
    } else {
        return $array;
    }
}

//获取客户端IP地址
function get_real_ip()
{
    $ip = false;
    if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) {
            array_unshift($ips, $ip);
            $ip = false;
        }
        for ($i = 0; $i < count($ips); $i++) {
            if (!eregi("^(10|172\.16|192\.168)\.", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

function telAttr($tel)
{
//查询手机号码归属地
    $url = 'http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile=' . $tel;
    $xmldata = file_get_contents($url);
    $data = (array) simplexml_load_string($xmldata);
    if ($data['retmsg'] == 'OK') {
        return trim($data['province'] . ' ' . $data['city'] . ' ' . $data['supplier']);
    } else {
        return '无法识别';
    }
}

function telAttrArray($tel)
{
//查询手机号码归属地 返回数组
    $url = 'http://life.tenpay.com/cgi-bin/mobile/MobileQueryAttribution.cgi?chgmobile=' . $tel;
    $xmldata = file_get_contents($url);
    $data = (array) simplexml_load_string($xmldata);
    if ($data['retmsg'] == 'OK') {
        //return $data['province'] . ' ' . $data['city'] . ' ' . $data['supplier']);
        return $data;
    } else {
        return '无法识别';
    }
}

function cut($content, $from, $end)
{
//截取函数
    $content = explode($from, $content);
    if (count($content) > 1) {
        $content = explode($end, $content[1]);
        return $content[0];
    } else {
        return '';
    }
}

/**
 * 计算时间，规则如下，如果一小时内，显示分钟，如果大于1小时小于1天显示小时，如果大于天且小于3天，显示天数，否则显示日期时间
 * @param type $the_time    Y-m-d H:i:s
 * @param type 最后时间单位 hour最后单位为小时
 * @return type
 */
function time_tran($the_time, $time_units, $formart = 'n月j日')
{
    $now_time = date("Y-m-d H:i:s");
    $now_time = strtotime($now_time);
    $show_time = strtotime($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 60) {
        return '刚刚';
    } else {
        if ($dur < 3600) {
            return floor($dur / 60) . '分钟前';
        } else {
            if ($dur < 86400) {
                return floor($dur / 3600) . '小时前';
            } else {
                if ($dur < 604800) {
//7天内
                    if ($time_units == 'hour') {
                        $res_time = strtotime($the_time);
                        return date($formart, $res_time);
                    } else {
                        return floor($dur / 86400) . '天前';
                    }
                } else {
                    $res_time = strtotime($the_time);
                    return date($formart, $res_time);
                }
            }
        }
    }
}

/**
 * 获取随机字符
 * @param $length 随机字符的长度
 */

function get_rand_str($length)
{
    $output = '';
    $chars = "a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z,0,1,2,3,4,5,6,7,8,9";
    $chars_array = explode(',', $chars);
    shuffle($chars_array);
    for ($i = 0; $i < $length; $i++) {
        $output .= $chars_array[$i];
    }
    return $output;
}

function remove_xss($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len = 6, $type = '', $addChars = '')
{
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default:
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {
//位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

/**
 * 发送邮件
 *
 */

function sendEmail($toemail, $subject, $content)
{
    require_once __DIR__ . '/../extend/PHPMailer-master/PHPMailerAutoload.php';
    try {
        $username = '';
        $password = '';
        $replyname = '';
        $mail = new PHPMailer(true);
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
        $mail->SMTPAuth = true; //开启认证
        $mail->Port = 80; //端口请保持默认
        //$mail->Host = "smtp.163.com"; //使用QQ邮箱发送
        $mail->Host = "smtp.mxhichina.com";
        $mail->Username = $username; //这个可以替换成自己的邮箱
        $mail->Password = $password; //注意 这里是写smtp的授权码 写的不是QQ密码，此授权码不可用
        //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $mail->AddReplyTo($username, $replyname); //回复地址
        $mail->From = $username;
        $mail->FromName = $replyname;
        $mail->AddAddress($toemail);
        $mail->Subject = $subject;
        $mail->Body = $content;
        // $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
        $mail->WordWrap = 80; // 设置每行字符串的长度
        //$mail->AddAttachment("f:/test.png"); //可以添加附件
        $mail->IsHTML(true);
        $mail->Send();
        return true;
        echo '邮件已发送';

    } catch (phpmailerException $e) {
        return false;
        echo "邮件发送失败：".$e->errorMessage();
    }
    return true;
}

/**
 * 远程请求函数
 * @author fanhe           2014-1-3
 * @param type $url    请求网址
 * @param type $post   post参数
 * @param type $cookie
 * @return type
 */
function vcurl($url, $post = '', $cookie = '', $cookiejar = '', $referer = '')
{
    $tmpInfo = '';
    $cookiepath = getcwd() . './' . $cookiejar;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    if ($referer) {
        curl_setopt($curl, CURLOPT_REFERER, $referer);
    } else {
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    }
    if ($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    }
    if ($cookie) {
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    if ($cookiejar) {
        curl_setopt($curl, CURLOPT_COOKIEJAR, $cookiepath);
        curl_setopt($curl, CURLOPT_COOKIEFILE, $cookiepath);
    }
    //curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //是否抓取跳转后的页面
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($curl);
    if (curl_errno($curl)) {
        //echo '<pre><b>错误:</b><br />' . curl_error ( $curl );
    }
    curl_close($curl);
    return $tmpInfo;
}

function curls($url, $data_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 * 发送post请求
 * @param string $url 请求地址
 * @param array $post_data post键值对数据
 * @return string
 */
function send_post($url, $post_data)
{

    $postdata = http_build_query($post_data);
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type:application/x-www-form-urlencoded',
            'content' => $postdata,
            'timeout' => 15 * 60, // 超时时间（单位:s）
        ),
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

function is_mobile($tel)
{
    if (preg_match('/^(1(3|4|5|7|8)[0-9])\d{8}$/', $tel)) {
        return true;
    } else {
        return false;
    }
}

function is_email($email){
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 验证身份证号
 * @param $vStr
 * @return bool
 *
 */
function is_idcard($vStr)
{
    $vCity = array(
        '11', '12', '13', '14', '15', '21', '22',
        '23', '31', '32', '33', '34', '35', '36',
        '37', '41', '42', '43', '44', '45', '46',
        '50', '51', '52', '53', '54', '61', '62',
        '63', '64', '65', '71', '81', '82', '91',
    );

    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) {
        return false;
    }

    if (!in_array(substr($vStr, 0, 2), $vCity)) {
        return false;
    }

    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);

    if ($vLength == 18) {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }

    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) {
        return false;
    }

    if ($vLength == 18) {
        $vSum = 0;

        for ($i = 17; $i >= 0; $i--) {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
        }

        if ($vSum % 11 != 1) {
            return false;
        }

    }

    return true;
}

function is_passport($passport)
{
    if (preg_match('/^1[45][0-9]{7}|([P|p|S|s]\d{7})|([S|s|G|g]\d{8})|([Gg|Tt|Ss|Ll|Qq|Dd|Aa|Ff]\d{8})|([H|h|M|m]\d{8，10})$/', $passport)) {
        return true;
    }
    return false;
}

/*
 * 身份证判断性别 1男2女0未知
 */
function get_sex_byidentity($identity_cardnumber)
{
    $id_length = strlen($identity_cardnumber);
    if ($id_length == 15) {
        $sex_str = substr($identity_cardnumber, -1, 1);
    } else if ($id_length == 18) {
        $sex_str = substr($identity_cardnumber, -2, 1);
    }
    if (is_numeric($sex_str)) {
        if ($sex_str & 1) {
            //奇男 偶女
            return 1;
        } else {
            return 2;
        }
    }
    return 0;
}

/*
 * 根据身份证号码获得年龄
 * @autho lilong
 */
function get_age_byidentity($identity_cardnumber)
{
    $id_length = strlen($identity_cardnumber);
    if ($id_length == 15) {
        $year_str = substr($identity_cardnumber, 6, 2);
        $year_str = '19' . $year_str;
    } else if ($id_length == 18) {
        $year_str = substr($identity_cardnumber, 6, 4);
    }
    if (is_numeric($year_str)) {
        $year = date('Y');
        $age = $year - $year_str;
        return $age;
    }
    return 0;
}

/*
 * 根据身份证号码获得出生年月
 * @author lilong
 */
function get_yearmonth_byidentity($identity_cardnumber)
{
    $id_length = strlen($identity_cardnumber);
    if ($id_length == 15) {
        $year_str = substr($identity_cardnumber, 6, 2);
        $year_str = '19' . $year_str;
        $month_str = (int) substr($identity_cardnumber, 8, 2);
    } else if ($id_length == 18) {
        $year_str = substr($identity_cardnumber, 6, 4);
        $month_str = (int) substr($identity_cardnumber, 10, 2);
    }
    if (is_numeric($year_str) && is_numeric($month_str)) {
        $res['year'] = $year_str;
        $res['month'] = $month_str;
        return $res;
    }
    return 0;
}

/**
 * 截取字符串(支持汉字)
 * @param type $str 字符串
 * @param type $site 起始位置
 * @param type $len 长度
 * @return str
 */
function utf8Substr($str, $start, $len)
{
    $str = preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $start . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
    return $str;
}

/**
 * @desc 在指定参数下显示调试信息
 * @author liuyaya 2015.05.22
 * @param mixed $var 变量
 * @param string $debug 浏览器GET参数  如果参数值等于1 退出当前脚本 否则继续执行
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dumps($var, $debug = 'debug', $echo = true, $label = null, $strict = true)
{
    if (isset($_GET[$debug])) {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (!$strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (!extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            if ($_GET[$debug] == 1) {
                exit($output);
            } else {
                echo ($output);
            }
            return null;
        } else {
            return $output;
        }

    }
}

/**
 * @desc 根据经纬度获取城市名称
 * @param $latitude 纬度
 * @param $longitude 经度
 * @return string
 */
function get_cityname_bygeocoding($latitude, $longitude)
{
    $cityJson = vcurl("http://api.map.baidu.com/geocoder/v2/?ak=tAGNm8k1z5KTfuGjgDKKv8LS&location=" . $latitude . "," . $longitude . "&output=json");
    $arraycity = json_decode($cityJson, true);
    if (empty($arraycity['result'])) {
        return false;
    }
    $cityname = $arraycity['result']['addressComponent']['city'];

    return $cityname;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0)
{
    $type = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) {
            unset($arr[$pos]);
        }

        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 根据ip获取城市名称
 * 优先级: 淘宝>新浪>百度
 * @return string
 */
function get_cityname_byip($cityip = false)
{
    if (empty($cityip)) {
        $cityip = get_client_ip();
    }
    $taobao_url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $cityip;
    $sina_url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=" . $cityip;
    $baidu_url = "http://api.map.baidu.com/location/ip?ak=tAGNm8k1z5KTfuGjgDKKv8LS&ip=" . $cityip;
    $cityjson = vcurl($taobao_url); //获取淘宝的IP库
    $arraycity = json_decode($cityjson, true);
    $cityname = $arraycity['data']['city'];
    if (empty($cityname)) {
        $cityjson = vcurl($sina_url); //获取新浪的IP库
        $arraycitysina = json_decode($cityjson, true);
        $cityname = $arraycitysina['city'];
        if (empty($cityname)) {
            $cityjson = vcurl($baidu_url); //获取百度的IP库
            $arraycitybaidu = json_decode($cityjson, true);
            if (empty($arraycitybaidu['content'])) {
                return false;
            }
            $cityname = $arraycitybaidu['content']['address_detail']['city'];
            if (empty($cityname)) {
                return false;
            }
        }
    }
    return $cityname;

}

/**
 * 创建目录
 * @return string
 */
function make_dir($path)
{
    if (is_dir($path)) {
        return 0;
    } else {
        $oldmask = umask(0);
        mkdir($path, 0777, true);
        umask($oldmask);
        return 1;
    }
}

/**
 * 发送广播消息
 * @param $content 推送的消息内容
 * @return string
 */
function send_broadcast($content)
{
    import('umengpush.Push', EXTEND_PATH);
    $push = new \Push();
    $push->sendBroadcast($content);

    return true;
}

/**
 * 给单个设备或者多个设备推送消息
 * @param $token 设备token
 * @param $content 推送消息内容
 * @return string
 */
function send_unicast($token, $content)
{
    import('umengpush.Push', EXTEND_PATH);
    $push = new \Push();
    $push->sendUnicast($token, $content);

    return true;
}

/**
 * 生成订单id
 * @return string
 */
function make_order_id()
{
    $order_id = date('ymd') . uniqid() . strtolower(get_rand_str(2));
    return $order_id;
}

/**
 * 生成token
 * @return string
 */
function make_token($str)
{
    $token = base64_encode(md5($str . 'qjy_dsb'));
    return $token;
}

/**
 * 解析base64图片
 * @param $content 推送的消息内容
 * @return string
 */
function decodeFile($base64_url)
{
    preg_match('/^data:image\/(\w+);base64/', $base64_url, $out);

    $type = $out[1];
    $type_param = 'data:image/' . $type . ';base64,';
    $fileStream = str_replace($type_param, '', $base64_url);
    $fileStream = base64_decode($fileStream);

    return array(
        'type' => $type,
        'fileStream' => $fileStream,
    );

}

/**
 * 身份证图片识别
 * @param $file_name 带路径的图片地址
 * @return string
 */
function iden_card_ocr($file_name)
{
    // 定义常量
    require_once '../extend/ocr/AipOcr.php';
    //初始化
    $aipOcr = new \AipOcr(APP_ID, API_KEY, SECRET_KEY);
    //身份证识别
    //$res = json_encode($aipOcr->idcard(file_get_contents($file_name), true), JSON_PRETTY_PRINT);
    $res = $aipOcr->idcard(file_get_contents($file_name), true);
    return $res;
}

/**
 * 检查某一天是否是节假日
 * @param $date 日期，格式为20170506
 * @return string
 */
function is_holiday($date)
{
    $is_holiday = 0;
    $unix_time = strtotime($date);
    $num = date('w', $unix_time);
    if ($num == 0 || $num == 6) {
        $is_holiday = 1;
    }
    if ($date >= 20171001 && $date <= 20171008) {
        $is_holiday = 1;
    }
    return $is_holiday;
}

/**
 * 寻找假期最近的工作日
 * @param $date 日期，格式为20170506
 * @return string
 */
function get_latest_work_date($date)
{
    $latest_work_date = '';
    $is_holiday = is_holiday($date);
    if ($is_holiday == 1) {
        $pre_date = date('Ymd', strtotime('-1 day', strtotime($date)));
        return get_latest_work_date($pre_date);
    } else {
        return $date;
    }
}

/**
 * 判断是否是微信浏览器
 * @return string
 */
function is_wechat_explorer()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    return false;
}

/**
 * 新浪生成短链接
 * @param $long_url 长url
 * @return string
 */
function sinaShortenUrl($long_url)
{
    $url = trim(strtolower($long_url));
    $url = trim(preg_replace('/^http:\/\//', '', $url));
    if ($url == '') {
        echo 'url格式不正确';exit;
    }
    $url = urlencode('http://' . $url);

    //拼接请求地址，此地址你可以在官方的文档中查看到
    $url = 'http://api.t.sina.com.cn/short_url/shorten.json?source=' . SINA_APPKEY . '&url_long=' . $url;
    //获取请求结果
    $result = vcurl($url);
    //下面这行注释用于调试，
    // print_r($result);exit();
    //解析json
    $json = json_decode($result);
    //异常情况返回false
    if (isset($json->error) || !isset($json[0]->url_short) || $json[0]->url_short == '') {
        return false;
    } else {
        return $json[0]->url_short;
    }

}
