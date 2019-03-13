<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>微信公众号文章远程下载系统</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
</head>
<body>



<?php
function replaceImage($img)
{
    $url = $img;

    $dataType = 'jepg';

    $dataTypeViaWX = strpos($url,"wx_fmt=");


    $dataType = substr($url,$dataTypeViaWX+7);


    $ch = curl_init();
    $httpheader = array(
        'Host' => 'mmbiz.qpic.cn',
        'Connection' => 'keep-alive',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'no-cache',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,/;q=0.8',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36',
        'Accept-Encoding' => 'gzip, deflate, sdch',
        'Accept-Language' => 'zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4'
    );
    $options = array(
        CURLOPT_HTTPHEADER => $httpheader,
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true
    );

    curl_setopt_array( $ch , $options );
    $result = curl_exec( $ch );
    curl_close($ch);

    $fileName = md5($result);


    file_put_contents( "images/".$fileName.".".$dataType, $result );
    $actual_link = "http://$_SERVER[HTTP_HOST]/mp/";


    return $actual_link."images/".$fileName.".".$dataType;
}



if(isset($_GET['img'])) {
    $url = $_GET['img'];

    //$url = 'http://mmbiz.qpic.cn/mmbiz/vpR4I9Ay4AVpgpRM76I4C19gmAycRtcmvxddq77LhKKrkicczgBUfiaGiaC034EnzM5G9FEekaAosbxiaeLxwuK3ug/0?wx_fmt=jpeg';//微信图片地址

    $dataType = 'jepg';

    $parts = parse_url($url);
    parse_str($parts['query'], $query);
    $dataType = $query['wx_fmt'];

    $ch = curl_init();
    $httpheader = array(
        'Host' => 'mmbiz.qpic.cn',
        'Connection' => 'keep-alive',
        'Pragma' => 'no-cache',
        'Cache-Control' => 'no-cache',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,/;q=0.8',
        'User-Agent' => 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.89 Safari/537.36',
        'Accept-Encoding' => 'gzip, deflate, sdch',
        'Accept-Language' => 'zh-CN,zh;q=0.8,en;q=0.6,zh-TW;q=0.4'
    );
    $options = array(
        CURLOPT_HTTPHEADER => $httpheader,
        CURLOPT_URL => $url,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => true
    );

    curl_setopt_array( $ch , $options );
    $result = curl_exec( $ch );
    curl_close($ch);

    $fileName = md5($result);


    file_put_contents( $fileName.".".$dataType, $result );


}
else if(isset($_GET['url']))
{
    $url = "http://mmbiz.qpic.cn/mmbiz_jpg/qsR9TErSFr3bbbAfZFglWU0RzmgiaoMCrYvkTd21aITyxFEFtSLSMZk7Q6Pna04f3qWGA1jPNXf9v2y7Ters0iaQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5&wx_lazy=1";


    /*
    $url = "http://mp.weixin.qq.com/s?__biz=MzI2NDIyODA0Nw==&mid=100010618&idx=1&sn=7d0745b971f65a9600aab8624631db3a&chksm=6aad77da5ddafecc631daeddacae07ea454e575b1b68ce2ca905326c0b7b7b04ab016e40ae14#rd";

    //echo $url;

    $html = file_get_contents($url);

    $doc = new DOMDocument();
    @$doc->loadHTML($html);

    $tags = $doc->getElementsByTagName('img');

    foreach ($tags as $tag) {
        $imgUrl = $tag->getAttribute('data-src');

        $server = htmlspecialchars($_SERVER["PHP_SELF"]);

        echo "<form action=\"$server\" method=\"get\">";
        echo "<input type=\"text\" name=\"img\" value=\"$imgUrl\">";
        echo '<a href="'.$imgUrl.'">'.$imgUrl.'</a>';
        echo "  <input type=\"submit\" value=\"Submit\">";
        echo "</form>";
    }
    */
}
else if(isset($_POST['post'])){
    $url = $_POST['post'];

    $ctx = stream_context_create(array('http' => array('timeout' => 10)));

    libxml_use_internal_errors(TRUE);

    $newHtml = '';

    if ($html = @file_get_contents($url, false, $ctx)) {

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $result = '';
        $div = $xpath->query('//div[@class="rich_media_content "]');

        foreach ($div as $childNode) {
            $newHtml = $dom->saveHtml($childNode);
        }
        // print($result);

        $tags = $dom->getElementsByTagName('img');
        foreach ($tags as $tag) {
            $old_src = $tag->getAttribute('data-src');
            $new_src_url = replaceImage($old_src);
            $tag->setAttribute('src', $new_src_url);
            $tag->removeAttribute('data-src');
        }
        echo $dom->saveHTML();

    } else {
        echo "下载失败，请稍后尝试";
    }

}
else
{
    $server = htmlspecialchars($_SERVER["PHP_SELF"]);
    echo "
    <div class=\"container\" style=\"margin-top: 5%;\">
    <div class=\"col-md-6 col-md-offset-3\">";

    echo "        <!-- Search Form -->
        <form role=\"form\" action=\"$server\" method=\"post\">

            <!-- Search Field -->
            <div class=\"row\">
                <h1 class=\"text-center\">微信公众号文章远程下载系统 v2.0</h1>
                <div class=\"form-group\">
                    <div class=\"input-group\">
                        <input class=\"form-control\" type=\"text\" name=\"post\" placeholder=\"请输入公众号文章网址\" required/>
                        <span class=\"input-group-btn\">
                            <button class=\"btn btn-success\" type=\"submit\"><span class=\"glyphicon glyphicon-download\" aria-hidden=\"true\"><span style=\"margin-left:10px;\">立即下载</span></button>
                        </span>
                        </span>
                    </div>
                </div>
                <p class=\"text-muted text-center\">2017 By Yihua</p>
            </div>

        </form>
        <!-- End of Search Form -->";
    echo "    </div>
</div>";
}

?>






</body>
</html>
