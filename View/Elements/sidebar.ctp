<h3>
    Categories (All States)
</h3>
<ul id="categories" class="unstyled">
	<?php foreach ($categories_list as $cat_id => $cat_name): ?>
		<li>
			<?= $this->Html->link(
				$cat_name,
				[
					'controller' => 'categories',
					'action' => 'view',
					'cat_slug' => Inflector::slug($cat_name)
                ]
			) ?>
		</li>
	<?php endforeach; ?>
</ul>

<h3>
    View State Report Card
</h3>

<select id="select_state">
	<option value="">
        Select state...
    </option>
	<option value=""></option>
	<?php foreach ($states_list as $state): ?>
		<option value="<?= $state['State']['abbreviation'] ?>">
			<?= $state['State']['name'] ?>
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
		<?= $this->Html->link('About', ['controller' => 'pages', 'action' => 'home']) ?>
	</li>
	<li>
		<?= $this->Html->link('Methodology', ['controller' => 'pages', 'action' => 'methodology']) ?>
	</li>
	<li>
		<?= $this->Html->link('Data Sources', ['controller' => 'pages', 'action' => 'data_sources']) ?>
	</li>
	<li>
		<?= $this->Html->link('Glossary', ['controller' => 'pages', 'action' => 'glossary']) ?>
	</li>
	<li>
		<?= $this->Html->link('Credits', ['controller' => 'pages', 'action' => 'credits']) ?>
	</li>
</ul>

<h3>Downloads</h3>
<section class="printed_reports">
	<h4>
		National Report Cards (PDF)
	</h4>
	<ul class="inline">
	    <?php for ($year = RELEASE_YEAR; $year >= 2009; $year--): ?>
    		<li>
                <?= $this->Html->link($year, "/files/Manufacturing and Logistics Scorecard - National - $year.pdf") ?>
            </li>
        <?php endfor; ?>
		<li>
			<?= $this->Html->link('2008 Analysis', '/files/Manufacturing and Logistics Scorecard - National - 2008-1.pdf') ?>
		</li>
		<li>
			<?= $this->Html->link('2008 Scorecard', '/files/Manufacturing and Logistics Scorecard - National - 2008-2.pdf') ?>
		</li>
	</ul>

	<h4>
		Indiana Report Cards (PDF)
	</h4>
	<ul class="inline">
	    <?php for ($year = RELEASE_YEAR; $year >= 2008; $year--): ?>
    		<li>
                <?= $this->Html->link($year, "/files/Manufacturing and Logistics Scorecard - Indiana - $year.pdf") ?>
            </li>
        <?php endfor; ?>
	</ul>

	<h4>
		Spreadsheets
	</h4>
	<ul class="inline">
        <?php for ($year = RELEASE_YEAR; $year >= 2018; $year--): ?>
            <li>
                <?= $this->Html->link($year, "/files/Manufacturing and Logistics Scorecard - Scorecard - $year.csv") ?>
            </li>
        <?php endfor; ?>
        <?php for ($year = 2017; $year >= 2014; $year--): ?>
            <li>
                <?= $this->Html->link($year, "/files/Manufacturing and Logistics Scorecard - Scorecard - $year.xlsx") ?>
            </li>
        <?php endfor; ?>
	</ul>

	<h4>
		Related Studies (PDF)
	</h4>
	<ul>
        <li>
            <?= $this->Html->link(
                'Manufacturing & Logistics: A Generation of Volatility & Growth (2017)',
                '/files/Conexus2017-Volatility.pdf'
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Advanced Manufacturing in the United States (2016)',
                '/files/Conexus2016-AdvMfg.pdf'
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'The Myth and the Reality of Manufacturing in America (2017)',
                '/files/MfgReality.pdf'
            ) ?>
        </li>
		<li>
			<?= $this->Html->link(
                'Manufacturing and Labor Market Frictions (2014)',
                '/files/MfgLaborMktFrictions.pdf'
            ) ?>
		</li>
		<li>
			<?= $this->Html->link(
                'Manufacturing Productivity Through the Great Recession (2013)',
                '/files/MfgProductivity2013.pdf'
            ) ?>
		</li>
	</ul>
</section>
