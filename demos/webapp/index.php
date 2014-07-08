<?php

require_once('baiduapi.inc.php');

if ($user) {
    $apiClient = $baidu->getBaiduApiClientService();
    $profile = $apiClient->api('/rest/2.0/passport/users/getInfo',
                               array('fields' => 'userid,username,realname,portrait,userdetail,birthday,marriage,sex,blood,figure,constellation,education,trade,job'));
    if ($profile === false) {
        //get user profile failed
        var_dump(var_export(array('errcode' => $baidu->errcode(), 'errmsg' => $baidu->errmsg()), true));
        $user = null;
        exit();
    }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
    $logoutUrl = $baidu->getLogoutUrl('http://robin928.sinaapp.com/demos/webapp/logout_callback.php?u=' .
                                      urlencode(BaiduUtils::getCurrentUrl()));
} else {
    $loginUrl = $baidu->getLoginUrl('', 'popup');
}

?>
<!doctype html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="../css/LightFace.css"/>
</head>
<body>
<?php if ($user): ?>
    <div>
        <img id="logoutfrombaidu" src="../images/bd_logout_short.png"/>
    </div>
<?php else: ?>
    <div>
        <img id="loginwithbaidu" src="../images/bd_login_short.png"/>
    </div>
<?php endif ?>

<?php if ($user): ?>
    <h3>Your User Object:</h3>
    <pre><?php print_r(var_export($profile, true))?></pre>
    <div>
        <iframe name="api_caller" style="display:none" height="0"></iframe>
        <form action="../webapp/upload_picture.php" method="post" target="api_caller">
            <label for="pic_url">Try to upload this picture to your baidu cloud album:</label>

            <p>
                <input type="url" id="pic_url" name="pic_url" value="" size="100"
                       placeholder="input picture url here, e.g. http://365jia.cn/uploads/11/0212/4d569577bafed.jpg"/>
                <input type="submit" name="upload" value="Upload It!">
            </p>
        </form>
        <div id="tip"></div>

        <div>
            <form action="../webapp/translate.php" method="post" target="api_caller">
                <label for="translate">Try to translate the following chinese to English:</label>

                <p>
                    <textarea id="translate" name="translate" value="" rows="6" cols="60"></textarea>
                </p>
                <input type="submit" name="search" value="Search"/>
            </form>
            <div id="translate_result"></div>
        </div>
    </div>
<?php else: ?>
    <strong><em>You are not Connected.</em></strong>
<?php endif ?>
<script type="text/javascript" src="../js/mootools-1.3.js"></script>
<script type="text/javascript" src="../js/LightFace.js"></script>
<script type="text/javascript" src="../js/LightFace.IFrame.js"></script>

<script>
    <?php if (!$user): ?>
    function onLoginSuccess() {
        window.location.href = "<?php echo BaiduUtils::getCurrentUrl(); ?>";
    }
    document.id('loginwithbaidu').addEvent('click', function () {
        new LightFace.IFrame({height: 320, width: 500, url: '<?php echo $loginUrl;?>'}).open();
    });
    new LightFace.IFrame({height: 320, width: 500, url: '<?php echo $loginUrl;?>'}).open();
    <?php else: ?>
    function onTranslate(is_success, result) {
        if (is_success) {
            var result = result.trans_result;
            var str = '';
            for (var i = 0; i < result.length; i++) {
                if (i != 0) {
                    str += '<br/>';
                }
                str += result[i].dst;
            }
            document.id('translate_result').innerHTML = '<span style="color:blue">' + str + '</span>';
        } else {
            document.id('translate_result').innerHTML = 'Translate failed: <span style="color:red;">' + result + '</span>';
        }
        return false;
    }
    function onUploadFailed(error) {
        document.id('tip').innerHTML = 'Upload failed: <span style="color:red">' + error + '</span>';
        return false;
    }
    function onUploadSuccess(pic_url) {
        document.id('tip').innerHTML = 'Upload success, your can see it by this url: <a href="' + pic_url + '" target="_blank">' + pic_url + '</a>';
        return false;
    }
    document.id('logoutfrombaidu').addEvent('click', function () {
        window.location.href = "<?php echo $logoutUrl; ?>";
    });
    <?php endif ?>
</script>
<script src="http://app.baidu.com/static/appstore/monitor.st"></script>
<script>
    baidu.app.autoHeight();
    baidu.app.setHeight(400);
</script>
</body>
</html>