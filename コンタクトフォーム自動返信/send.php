<?php 
    session_start();
    if(!$_SESSION) {
    header('Location: ./index.php');
    }

    //任意入力項目の配列が空の場合のエラーメッセージ制御
    error_reporting(E_ALL ^ E_NOTICE);

    //メール件名
    $title = "お問い合わせありがとうございます";

    //メール差出人
    $sender = "メール差出人";

    //メール送信元
    $mailFrom = "laboratory1121@gmail.com";

    //メール返信先
    $replyTo = "laboratory1121@gmail.com";

    //管理者用件名
    $title_admin = "お問い合わせ：";

    //管理者メールアドレス
    $adminEmail = "laboratory1121@gmail.com";

    //メールヘッダ設定
    $addHeader ="From:".mb_encode_mimeheader($sender)."<".$mailFrom.">\n";
    $addHeader .= "Reply-to: ".$replyTo."\n";
    $addHeader .= "X-Mailer: PHP/". phpversion();

    // 迷惑メール対策
    $addOption = '-f'.$mailFrom;

    //タイムスタンプ
    date_default_timezone_set('Asia/Tokyo');
    $timeStamp = time();
    $week = array('日', '月', '火', '水', '木', '金', '土');
    $dateFormatYMD = date('Y年m月d日',$timeStamp);
    $dateFormatHIS = date('H時i分s秒',$timeStamp);
    $weekFormat = "（".$week[date('w',$timeStamp)]."）";
    $outputDate = $dateFormatYMD.$weekFormat.$dateFormatHIS;

    //XSS対策用サニタイズ
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    //メール本文内に表示するデータの変数化
    $name = h($_SESSION['input_name']);
    $email = h($_SESSION['input_email']);

    //自動返信メール本文（ヒアドキュメント）
    $messageUser = <<< EOD
    お問い合わせありがとうございます。
    下記の内容で承りました。
    
    ----------------------------------------------------
    
    【全角テキスト】{$name}
    【メールアドレス】{$email}
    
    ----------------------------------------------------

    折り返しご返信させていただきますので、しばらくお待ちください。
    EOD;

     //管理者確認用メール本文（ヒアドキュメント）
    $messageAdmin = <<< EOD
    ウェブサイトより下記の内容でお問い合わせがありました。
    
    ----------------------------------------------------
    
    【全角テキスト】{$name}
    【メールアドレス】{$email}
    
    ----------------------------------------------------
    EOD;

    //メール共通送信設定
    mb_language("ja");
    mb_internal_encoding("UTF-8");
    
    if(!empty($_SESSION['input_email'])) {
        
        //自動返信メール送信設定
        mb_send_mail($_SESSION['input_email'],$title,$messageUser,$addHeader,$addOption);
    
        // 管理者確認用メール送信設定
        mb_send_mail($adminEmail,$title_admin.$outputDate,$messageAdmin,$addHeader,$addOption); 
        
        $isSend = true;
    } else {
        $isSend = false;
    }

    session_destroy();
    ?>

    <?php if($isSend): ?>
        <p>フォームの内容が【<?php echo h($_SESSION['input_email']); ?>】宛にメールで送信されました。 
        </p> 
    <?php else: ?> 
        <p>送信エラー：メールフォームからの送信に失敗しました。お手数ですが再度お試しください。 
        </p>
    <?php endif; ?>