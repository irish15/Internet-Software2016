<?php
App::uses('AppController', 'Controller');
	//Posts Controller

class PostsController extends AppController {
   public $uses =array('Post','User');
   public $components = array('Auth','Cookie','DebugKit.Toolbar','Security');
   public $helpers = array('Html','Form','Flash');

   public function beforeFilter(){
   		$this->Auth->userModel = 'User';
      $this->Auth->allow('login','callback','logout');
      $this->Post->postRedirect = array('controller' => 'posts','action' => 'index');
       $this->Auth->loginRedirect = array('controller' => 'posts','action' => 'index');
       $this->Auth->loginAction = '/examples/login';
      parent::beforeFilter();
   }

    public function index(){
        $posts = $this->Post->find('all');
        $this->set('posts',$posts);
    }

    public function add(){
        $user = $this->Auth->user();
        if($this->request->is('post')){
            $this->Post->create();
            $temp = $this->request->data;
            $temp["Post"]["user_id"] =  $user["id"];
            if($this->Post->saveAssociated($temp)){
      		}else{
         $this->Session->setFlash(__('Post don\'t posted .'), 'default', array('class'=>'error-message'), 'auth');
      }
   }
 }

	 public function edit($id){
		 $this->Post->id = $id;
    	if($this->request->is('get')){
      	$this->request->data = $this->Post->read();
    	}else{
      	if($this->Post->save($this->request->data)){
        	$this->redirect("index");
      	}else{
        	$this->Session->setFlash('failed');
				}
			}
		}

	 public function view($id){
		 $posts = $this->Post->find('first', array(
			 'conditions' => array('Post.id' => $id)
		 ));
			$this->set('posts', $posts);
	}

	public function delete($id) {
    $this->Post->delete($id);
    $this->redirect("index");
	}

}
