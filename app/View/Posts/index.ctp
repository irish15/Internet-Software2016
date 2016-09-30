<?php
//print_r($posts);
	foreach($posts as $post){
		echo $post['Post']['title']."<br>";
		echo $post['Post']['body']."<br>";
        //echo $post['Users']['name']."<br>";
        //echo "@".$post['Users']['screen_name']."<br>"."_______";

        echo $this->Html->link('EDIT',array('action' => 'edit',$post['Post']['id']));
          echo " ";
        echo $this->Html->link('DELETE',array('action' => 'delete',$post['Post']['id']));

    echo "<br>";
    echo "<br>";
      }
      echo "<br>";
      		echo $this->Html->link('ADD',array('action' => 'add'));
          echo " ";
        echo $this->Html->link('TwitterTL',array('action' => '../examples/login'));

?>
