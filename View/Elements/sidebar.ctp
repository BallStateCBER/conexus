<h3>Categories (All States)</h3>
<ul id="categories" class="unstyled">
	<?php foreach ($categories_list as $cat_id => $cat_name): ?>
		<li>
			<?php echo $this->Html->link(
				$cat_name,
				array(
					'controller' => 'categories',
					'action' => 'view',
					'cat_slug' => Inflector::slug($cat_name)
				)
			); ?>
		</li>
	<?php endforeach; ?>
</ul>

<h3>View State Report Card</h3>

<select id="select_state">
	<option value="">Select state...</option>
	<option value=""></option>
	<?php foreach ($states_list as $state): ?>
		<option value="<?php echo $state['State']['abbreviation']; ?>">
			<?php echo $state['State']['name']; ?>
		</option>
	<?php endforeach; ?>
</select>
<input type="button" value="View" id="select_state_button" />
<?php $this->Js->buffer("
	$('#select_state_button').click(function(event) {
		var state_abbrev = $('#select_state').val();
		if (state_abbrev == '') {
			alert('Please select a state from the drop-down menu');
		} else {
			var url = '/state/'+state_abbrev;
			window.location = url;
		}
	});
"); ?>

<ul class="unstyled misc_links">
	<li>
		<?php echo $this->Html->link('About', array('controller' => 'pages', 'action' => 'home')); ?>
	</li>
	<li>
		<?php echo $this->Html->link('Methodology', array('controller' => 'pages', 'action' => 'methodology')); ?>
	</li>
	<li>
		<?php echo $this->Html->link('Data Sources', array('controller' => 'pages', 'action' => 'data_sources')); ?>
	</li>
	<li>
		<?php echo $this->Html->link('Glossary', array('controller' => 'pages', 'action' => 'glossary')); ?>
	</li>
	<li>
		<?php echo $this->Html->link('Credits', array('controller' => 'pages', 'action' => 'credits')); ?>
	</li>
</ul>

<h3>Printed Reports (PDF)</h3>
<section class="printed_reports">
	<h4>
		National Report Cards
	</h4>
	<ul class="inline">
		<li>
			<?php echo $this->Html->link('2014', '/files/National2014.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2013', '/files/National2013.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2012', '/files/National2012.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2011', '/files/National2011.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2010', '/files/National2010.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2009', '/files/National2009.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2008 Analysis', '/files/National2008-1.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2008 Scorecard', '/files/National2008-2.pdf'); ?>
		</li>
	</ul>

	<h4>
		Indiana Report Cards
	</h4>
	<ul class="inline">
		<li>
			<?php echo $this->Html->link('2014', '/files/Indiana2014.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2013', '/files/Indiana2013.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2012', '/files/Indiana2012.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2011', '/files/Indiana2011.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2010', '/files/Indiana2010.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2009', '/files/Indiana2009.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('2008', '/files/Indiana2008.pdf'); ?>
		</li>
	</ul>

	<h4>
		Related Studies
	</h4>
	<ul>
		<li>
			<?php echo $this->Html->link('Manufacturing and Labor Market Frictions (2014)', '/files/MfgLaborMktFrictions.pdf'); ?>
		</li>
		<li>
			<?php echo $this->Html->link('Manufacturing Productivity Through the Great Recession (2013)', '/files/MfgProductivity2013.pdf'); ?>
		</li>
	</ul>
</section>