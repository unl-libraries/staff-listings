<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'Library Directory');
?>
<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?>:
		<?php echo $title_for_layout; ?>
	</title>
	<meta name="robots" content="noindex">
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('directory');
		echo $this->Html->css('addressbook_style');
		echo $this->Html->css('fontello');
    	echo $this->Html->css('animation');


		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		//these variables need to be set by the authentication module or some other process

	?>

</head>
<body>
	<div id="container">
		<div id="header">
			<h1>Library Staff Directory</h1>
		</div>
		<div id="nav">
			<div id="login">
				<?php if (!AuthComponent::user('id')) echo $this->Html->link('Admin Login',array('controller'=>'users','action'=>'login'));
					else echo "Logged in as <em>".$this->Html->link(AuthComponent::user('username'),array('controller'=>'users','action'=>'view',AuthComponent::user('id')))."</em>. ". $this->Html->Link('Logout',array('controller'=>'users','action'=>'logout'));
				?>
			</div>
			<ul>
				<li>
					<?php echo $this->Html->link('Home',array('controller'=>'addresses','action'=>'index'))?>
				</li>				
				<li>
					<?php echo $this->Html->link('Departments',array('controller'=>'departments','action'=>'index'))?>
				</li>
				<li>
					<?php echo $this->Html->link('Subject Assignments',array('controller'=>'subjects','action'=>'index'))?>
				</li>
				<li class="export">
				     <?php echo $this->Html->link('Download phone list',array('controller'=>'addresses','action'=>'export','all'),array('class'=>'icon-download-alt'),"This will download an excel file of phone numbers");?>
				 </li>
<!-- 				 <li> -->
				 	<?php //echo $this->Html->link('FAQ',array('controller'=>'pages','action'=>'display','faq'));?>
<!-- 				 </li> -->
				 <?php if(AuthComponent::user('id')):?>
				 <li>
				 	<?php echo $this->Html->link('Records needing update',array('controller'=>'addresses','action'=>'new_records'));?>
				 </li>
				 <?php endif;?>
			</ul>
		
		</div>
		<div id="content">
			<div id="search-az">

 				<form accept-charset="utf-8" method="post" id="AddressSearchForm" action="<?php echo $this->Html->url(array("controller" => "addresses","action" => "search","?" => $this->params->query));?>" name="searchform"> 
					<input type="text" id="search-query" value="<?php echo (isset($query)?$query:''); ?>" name="query" title="search for any text" size="45" tabindex="0"/>
 					<input type="submit" value="Search" />     
 					<?php // if we have a search query, add a clear button
						if (isset($query)):
						?>
						<input type=submit value="clear search" onclick="javascript:this.form.elements['search-query'].setAttribute('value','');javascript:this.form.submit();">
						<?php endif;?>
 				</form> 
			
			

			</div><br />
			<?php echo $this->Session->flash(); echo $this->Session->flash('auth');?>

			<?php echo $this->fetch('content'); ?>
		</div>
		
	</div>
	<?php if (AuthComponent::user('username') == 'srickel') echo $this->element('sql_dump'); ?>
	<div id="footer">
			<span>&copy; University of Nebraska Libraries <?php echo date('Y'); ?></span>
			<span class="footer_right">Feedback/Corrections? <?php echo $this->Html->link('Contact us',array('controller'=>'pages','action'=>'display','contact'))?></span>
	</div>
	
</body>
</html>
