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
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('cake.generic');
		echo $this->Html->css('addressbook_style.css');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
		//these variables need to be set by the authentication module or some other process
		$read_only=true;
		$group='';
		$use_ajax=false; 
	?>

</head>
<body>
	<div id="container">
		<div id="header">
			<h1>Library Staff Directory</h1>
		</div>
		<div id="nav">
			<ul>
				<li>
					<a href="/directory/addressbook/addresses/">Home</a>
				</li>
				<?php //$ready_only set from authentication scheme
				if(!$read_only) { ?>
				<li class="all">
					<a href="addresses/edit">Add new</a>
				</li>
				<?php 
				} 
				if(!$read_only && $public_group_edit && $table_groups != "" && !$is_fix_group)
				{ ?>
					<li class="admin">
						<a href="addresses/groups" onclick="javascript:alert('Sorry, I\'m not implemented yet!');return false;">Groups</a>
					</li>
				<?php } ?>					
					<li class="export">
				    	<a href="addresses/export/all&amp;print" onclick="javascript:alert('Sorry, I\'m not implemented yet!');return false;">Print all</a>
				  </li>
					<li class="export">
				      <a href="addresses/export/all&amp;print&amp;phones" onclick="javascript:alert('Sorry, I\'m not implemented yet!');return false;">Print phone numbers</a>
				  </li>
					<li class="export">
				    	<a href="addresses/export" onclick="javascript:alert('Sorry, I\'m not implemented yet!');return false;">Export</a>
				  </li> 
				<?php if(!$read_only) { ?>
					<li class="export">
				    	<a href="addresses/import">Import</a>
				  </li> 
				<?php } ?>
				</ul>
			</div>
		<div id="content">
			<div id="search-az">
			<?php if(! $use_ajax ) { ?>
				<form accept-charset="utf-8" method="get" name="searchform">
					<input type="text" value="<?php echo (isset($searchstring)?$searchstring:''); ?>" name="searchstring" title="search for any text" size="45" tabindex="0"/>
					<input name="submitsearch" type="submit" value="Search" />    
				</form>
			<?php
			//$link = "index${page_ext_qry}alphabet";
			//echo "<div id='a-z'><a href='$link=a'>A</a> | <a href='$link=b'>B</a> | <a href='$link=c'>C</a> | <a href='$link=d'>D</a> | <a href='$link=e'>E</a> | <a href='$link=f'>F</a> | <a href='$link=g'>G</a> | <a href='$link=h'>H</a> | <a href='$link=i'>I</a> | <a href='$link=j'>J</a> | <a href='$link=k'>K</a> | <a href='$link=l'>L</a> | <a href='$link=m'>M</a> | <a href='$link=n'>N</a> | <a href='$link=o'>O</a> | <a href='$link=p'>P</a> | <a href='$link=q'>Q</a> | <a href='$link=r'>R</a> | <a href='$link=s'>S</a> | <a href='$link=t'>T</a> | <a href='$link=u'>U</a> | <a href='$link=v'>V</a> | <a href='$link=w'>W</a> | <a href='$link=x'>X</a> | <a href='$link=y'>Y</a> | <a href='$link=z'>Z</a> | <a href='index$page_ext'>".ucfmsg('ALL')."</a></div>" ;
			} else {
				//search form 
			?>
			<form accept-charset="utf-8" method="get" name="searchform" onsubmit="return false">
				<input type="text" value="<?php echo $searchstring; ?>" name="searchstring" title="Search" size="45" tabindex="0" />
			</form>
			<?php } ?>
			<script type="text/javascript">
				document.searchform.searchstring.focus();
			</script>
			</div><br />
			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
		</div>
		<div id="footer">
			
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
