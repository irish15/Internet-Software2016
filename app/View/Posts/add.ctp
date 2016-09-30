<h1>Add Form </h1>
<?php
        echo $this->Form->create('Post',array('url' => array('action' => 'add')));
        echo $this->Form->input('title',array('label' => 'タイトル'));
        echo $this->Form->input('body',array('rows' => '3' ,'label' => '本文'));
        echo $this->Form->end('書き込み');
    echo "<br>";
    echo $this->Html->link('掲示板に戻る',array('action' => 'index'));
?>
