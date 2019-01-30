<?php
//--当前程序只监控周二的主任（副主任）未满的号源，如果改为其他时间需要注意网址及正则内容。


set_time_limit(0);//0表示不限时
ignore_user_abort(false);//如果设置为 false，网页断开会导致脚本停止运行。
ini_set("date.timezone","Asia/Chongqing");


for($i=0;$i<=3600;$i++){
    echo '第'.($i+1).'次</br>';
    zhuhanshu();

    sleep(rand(10,30)); //rand(1,3)表示1到3秒内，sleep里面不支持小数点，如果输入小数点直接取去掉小数点的数值。
    ob_flush();
    flush();//这一部会使cache新增的内容被挤出去，显示到阅读器上
}

function zhuhanshu(){
    $post_fields='strSta=/UrpOnline/Home/Index/7_330_2019-01-29_1__&orgId=7&deptCode=330&sex=0&date=2019-01-29&page=1&orderType=1&orgType=1';
    $url='https://www.xmsmjk.com/UrpOnline/Home/GetIndexList';
    $referer='https://www.xmsmjk.com/UrpOnline/Home/Index/7_330_2019-01-29_1__1';//伪造来源referer



    $ch = curl_init();//初始化curl模块
    curl_setopt($ch, CURLOPT_URL, $url);//登录提交的地址
    curl_setopt($ch, CURLOPT_HEADER, 0);//是否显示头信息
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息 1表示不自动显示？0表示自动显示?
    //curl_setopt($ch, CURLOPT_COOKIE,$cookie_str);//两种不同的cookie方式
    //curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);//两种不同的cookie方式
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0');//新增浏览器头
    curl_setopt ($ch,CURLOPT_REFERER,$referer);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//禁用后cURL将终止从服务端进行验证。
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);//为1 检查服务器SSL证书中是否存在一个公用名(common name)。 还可能为2
    curl_setopt($ch, CURLOPT_POST, 1);//是否是post方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    //curl_setopt($ch ,CURLOPT_PROXY,'192.168.1.39:8888');//设置代理服务器
    curl_setopt($ch, CURLOPT_TIMEOUT,30);//设置cURL允许执行的最长秒数。curl超时限制，避免碰到ddos等防御导致非常长时间没有返回一直卡住（无论是否长期运行，此项目记得要设置，另外如果对方网站网速慢则该数值不宜太小否则容易返回空内容）。

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8','X-Requested-With: XMLHttpRequest', 'DNT: 1','Connection: keep-alive' ,'Pragma: no-cache','Cache-Control: no-cache', 'Accept-Language: zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2', 'Accept-Encoding: gzip, deflate, br'));

    $neirong0=curl_exec($ch);//执行cURL
    curl_close($ch);//关闭cURL资源，并且释放系统资源

    $shuliang=preg_match_all('%<div class="index_top_in">(.*?)</ul>  </div>%si', $neirong0, $tiquneirong);
    //echo '总匹配数量为：'.$shuliang.'</br>'; //结果匹配的条数，未有符合的则为0

    for($i=0;$i<=($shuliang-1);$i++){


        if(preg_match_all('%主任%si', $tiquneirong[1][$i])){

            if(preg_match_all('%下周二01/29\|满%si', $tiquneirong[1][$i])){ //|为特殊字符，需要转义
                echo '该日尚未有需要的预约号，总数对应该日主任、副主任号数</br>';
            }else{
                echo $tiquneirong[1][$i].'</br>';
                echo '下周二已有需要的预约号，赶紧抢票,发送邮件后间隔较久时间再继续发送邮件，避免频繁发送邮件，后续手动终止程序。</br>';
                $youjianbiaoti='下周二已有需要的预约号，赶紧抢票,发送邮件后间隔较久时间再继续发送邮件，避免频繁发送邮件，后续手动终止程序。';
                file_get_contents('http://XXX.com/fayoujian.php?biaoti='.$youjianbiaoti);//发邮件通知或者发短信通知，可使用接口，自行设置。

                sleep(rand(5*60,10*60)); //rand(1,3)表示1到3秒内，sleep里面不支持小数点，如果输入小数点直接取去掉小数点的数值。
                ob_flush();
                flush();//这一部会使cache新增的内容被挤出去，显示到阅读器上


            }
        }

    }
}


?>