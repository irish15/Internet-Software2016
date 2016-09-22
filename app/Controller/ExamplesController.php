<?php

// 外部ライブラリとして OAuth(Twitter) をインポート
App::import('Vendor','OAuth/OAuthClient');

class ExamplesController extends AppController {
 // Examples コントローラで用いる他のモデル(テーブル)
 public $uses = array('User', 'Post');
 // 利用するコンポーネント(プラグイン)
 public $components = array('Auth', 'Cookie', 'DebugKit.Toolbar');

 // コントローラ内の各アクション(関数)を実行する前に処理
 public function beforefilter(){
   // 認証用モデルの指定
   $this->Auth->userModel = 'User';
   // 認証なしでアクセス出来るアクション一覧
   $this->Auth->allow('login','twitter','callback', 'logout');
   // ログイン完了後に移動する標準アクション(v2.2 以前：Auth->redirec()  v2.3以降 Auth->redirectUrl() で利用)
   $this->Auth->loginRedirect = array('controller' => 'examples','action' => 'index');
   // ログアウト後に移動する標準アクション
   $this->Auth->logoutRedirect = array('controller' => 'examples','action' => 'logout');
   // ログイン処理を記述するアクション
   $this->Auth->loginAction = '/examples/login';

   // 認証で利用するフィールド名
   $this->Auth->fields = array(
       'username' => 'id',
       'password' => 'access_token_key');

   // その他の処理は上位層の beforeFilter を利用
   parent::beforeFilter();
 }

 // login 画面からリンクで呼び出されるアクション(Twitter認証)
 public function twitter(){
   // コンシューマ・キーを用いたインスタンス生成
   $comsumer = $this->__createComsumer();
   // Twitter認証用のリクエスト・トークン生成
   $requestToken = $comsumer->getRequestToken(
     'https://api.twitter.com/oauth/request_token',
     'http://127.0.0.1:8080/cakephp/examples/callback');
   if ($requestToken) {
     // callback で利用するリクエスト・トークンを Session 変数として保存
     $this->Session->write('twitter_request_token', $requestToken);
     // Twitter認証の呼び出し
     $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
   } else {
     $this->Session->setFlash(__('Create Comsumer Failure'), 'default', array('class'=>'error-message'), 'auth');
   }
 }

 // 認証後に呼び出されるアクション
 public function callback() {
   // Session 変数からリクエスト・トークンを取得
   $requestToken=$this->Session->read('twitter_request_token');
   $comsumer = $this->__createComsumer();
   // 認証ユーザのアクセス・トークン取得
   $accessToken = $comsumer->getAccessToken(
         'https://api.twitter.com/oauth/access_token',
         $requestToken);

   if($accessToken){
     // 認証ユーザ情報の取得（戻り値は json 形式）
     $json=$comsumer->get(
       $accessToken->key,
       $accessToken->secret,
       'https://api.twitter.com/1.1/account/verify_credentials.json',
       array());
     // json => 配列変換
     $twitterData = json_decode($json,true);

     // データベース保存用のデータ生成
     $user['id'] = $twitterData['id_str'];
     $user['name'] = $twitterData['name'];
     $user['screen_name'] = $twitterData['screen_name'];
     $user['access_token_key'] = $accessToken->key;
     $user['access_token_secret'] = $accessToken->secret;

     // Users テーブルの更新
     $this->User->save($user);
     // Cookie 用に id  を保存
     $this->Cookie->write('id', $user['id']);

     // Auth Component 内のログイン処理呼び出し
     if ($this->Auth->login($user)) {
       // ログイン完了後のアクションへ遷移
       $this->redirect($this->Auth->redirect());
     }
   }else {
     // アクセス・トークンが取得できない旨をエラー表示
     $this->Session->setFlash(__('AccessToken Getting Failure'), 'default', array('class'=>'error-message'), 'auth');
   }
   $this->redirect('index');
 }


 public function login(){
   // ユーザの認証情報を取得（ログイン済みかどうかを判定）
   $user = $this->Auth->user();

   // Cookie ログインを処理するならこの辺りで・・
   $cookieValue = $this->Cookie->read('id');
   //  第一引数を追加しないとダメなようです
   //print_r($cookieValue);
  // print_r($user);
   // ログイン済みであれば index に遷移
   if(isset($cookieValue)){
     $user = $this->User->read(null, $cookieValue);
     if($this->Auth->login($user[$this->Auth->userModel])){
     return $this->redirect($this->Auth->redirect());
    }
   }
  }

 public function logout(){
   $this->Auth->logout();
   $this->flash('再ログインはこちら','index');
 }


 public function index() {
   $users =$this->Auth->user();

   // Twitter Timeline の表示

   $comsumer = $this->__createComsumer();

   $twitterData="";
   $json=$comsumer->get(
     $users['access_token_key'],
     $users['access_token_secret'],
     'https://api.twitter.com/1.1/statuses/home_timeline.json',
     array('count' => '30')
     );
   $twitterData = json_decode($json,true);


   // Posts テーブル内の全ての情報を読み出す
   $posts = $this->Post->find('all');

   // View に各変数を引き渡す
   $this->set(compact(
     'users',
     'posts',
     'twitterData'
   ));
   //print_r($data);
 }

  // OAuthClient インスタンス生成 (__ で始まる関数はプライベート関数)
  function __createComsumer(){

		// コンシューマ・キーは  https://apps.twitter.com/ で取得
		return new OAuthClient(
		'JjWvWDuWAF44875aWmozUVHpL',
		'oexQgx9WR2IPHVV2zqpLvTE2r455tqCYWriVcthNQYRhZtuJZi');
	}

}
?>
