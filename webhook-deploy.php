<?php
error_reporting(E_ALL);

// CPIユーザーID（契約情報で確認してください）
$user_id     = 'ec2-user';
// リポジトリ名（Backlogで確認してください）
$repo_name   = 'management';
// Gitレポジトリの位置の指定
//$git_dir     = '/var/www/dev2/' . $repo_name . '';
$git_dir     = '/var/www/dev2';
// 展開先ディレクトリの指定
//$work_tree   = '/usr/home/' . $user_id . '/html';
// logファイルの指定
$log_file    = '/var/www/deploy.log';
// Gitコマンドパス
$git_command = '/usr/bin/git';
// リリースするブランチの指定
//$deploy_ref  = 'refs/heads/master';
$deploy_ref  = 'refs/heads/develop';


/**
 * Git Webフックを受信する
 * BacklogのWebフックの仕様の解説はこちら
 * http://www.backlog.jp/help/usersguide/git/userguide1710.html
 */
$payload = json_decode($_POST['payload']);

// 指定されたブランチかどうかの確認
$checkout = false;
if ($payload){
    $ref = $payload->ref;
    if ($ref == $deploy_ref) {
        $checkout = true;
    }
}
file_put_contents($log_file, date('r') . var_export($payload,true)   . "\n", FILE_APPEND);

$checkout = true;
$ref = "";

// 指定されたブランチの場合、fetch+checkoutを実行して、最終コミットをlogファイルに保存する
if ($checkout) {
        $_cmd = $git_command . ' -C ' . $git_dir . ' fetch';
        $commit_hash = shell_exec($_cmd);
        file_put_contents($log_file, date('r') .  " fetch: " . $commit_hash . "\n", FILE_APPEND);

        //nginx でログインしてgit id /pw を保存する必要あり sshの鍵があれば不要
        $_cmd = 'cd /var/www/dev2; /usr/bin/git pull';
        $commit_hash = shell_exec($_cmd);
        file_put_contents($log_file, date('r') . " Ref: " .  $ref . " Commit: " . $_cmd . $commit_hash . "\n", FILE_APPEND);
}

~                                                                                            
