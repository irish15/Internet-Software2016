<?php
echo '<div id = board>';
echo "<center>Edit Form</center>";
echo '</div><br>';
echo '<div id = link>';
echo '<center>';
echo $this->Html->link('BACK',array('action' => 'index'));
echo '</center>';
echo '</div>';
echo '<div id = write>';
echo '<center>';
echo $this->Form->create('Post',array('url' => array('action' => 'edit')));
echo $this->Form->input('title',array('label' => 'タイトル'));
echo $this->Form->input('body',array('rows' => '3' ,'label' => '本文'));
echo $this->Form->end('RELOAD');
echo '</center>';
echo '</div>';
?>
