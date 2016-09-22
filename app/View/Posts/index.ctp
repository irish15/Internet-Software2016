<?php
//print_r($posts);
	foreach($posts as $post){
		echo $post['Post']['title']."<br>";
		echo $post['Post']['body']."<br>";
        echo $post['Users']['name']."<br>";
        echo "@".$post['Users']['screen_name']."<br>"."_______";
        echo "<br>";
	}
		echo "<br>";
		echo $this->Html->link('新規書き込み',array('action' => 'add'));
        echo "<br>";
        echo $this->Html->link('TwitterTL',array('action' => '../examples/login'));
?>
