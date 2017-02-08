<?php
	$this->extend('DataCenter.default');

	$this->assign('sidebar', $this->element('sidebar'));

	$this->start('subsite_title');
		echo '<h1 id="subsite_title" class="max_width">';
		echo $this->Html->link(
			'<img src="/img/Conexus.png" alt="Manufacturing and Logistics National Report, Sponsored by Conexus Indiana" />',
			array('controller' => 'pages', 'action' => 'home'),
			array('escape' => false)
		);
		echo '</h1>';
	$this->end();

	// Load CSS files at the top of the page
	$this->Html->css('/DataCenter/css/jquery.qtip.min.css', null, array('inline' => false));

	// Load JS files at the bottom of the page
	$this->Html->script('/DataCenter/js/jquery.svg.min.js', array('inline' => false));
	$this->Html->script('/DataCenter/js/jquery.svgdom.min.js', array('inline' => false));
	$this->Html->script('/DataCenter/js/jquery.qtip.min.js', array('inline' => false));
	$this->Html->script('script', array('inline' => false));

	// Being phased out in favor of HTML5 Google Charts
	//$this->Html->script('gchart/jquery.gchart.min.js', array('inline' => false));

	$this->start('flash_messages');
	    echo $this->element('flash_messages', array(), array('plugin' => 'DataCenter'));
    $this->end();

	echo '<div id="content">'.$this->fetch('content').'</div>';