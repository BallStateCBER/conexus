<?php
	$this->Js->buffer("
		// Select the same state in the dropdown menu
		var options = $('#select_state option');
		var len = options.length;
		for (var i = 0; i < len; i++) {
			if (options[i].value == '$abbreviation') {
				options[i].selected = true;
			}
		}
		
		// Remove now-purposeless blank options in the same dropdown menu
		for (var i = 0; i < len; i++) {
			if (options[i].value == '') {
				$(options[i]).remove();
			}
		}
	");
?>

<aside class="about_state">
	<h2>About <?php echo $state['State']['name']; ?></h2>
	<p>
		<?php echo $state['State']['name']; ?> has a population of <?php echo number_format($state['State']['population']); ?>. 
		The manufacturing industry is <?php echo $state['State']['manufacturing_share']; ?>% of the state economy.
		The total personal income in <?php echo $state['State']['name']; ?> is $<?php echo number_format($state['State']['total_personal_income']); ?>,000
		and earnings from manufacturing total $<?php echo number_format($state['State']['manufacturing_earnings']); ?>,000.
	</p>
	<p class="source">
		Source: Bureau of Economic Analysis, 2011
	</p>
</aside>

<h1>
	<?php echo $state['State']['name']; ?>
</h1>

<p>
	Click on a category to view state performance in that category
</p>

<table class="report_card">
	<thead>
		<tr>
			<td></td>
			<?php foreach ($years as $year): ?>
				<th><?php echo $year; ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($categories_list as $cat_id => $cat_name): ?>
			<tr>
				<th>
					<?php echo $this->Html->link(
						$cat_name, 
						array(
							'controller' => 'categories', 
							'action' => 'view', 
							'cat_slug' => Inflector::slug($cat_name)
						)
					); ?>
				</th>
				<?php foreach ($years as $year): ?>
					<?php if (isset($grades[$cat_id][$year])): ?>
						<td><?php echo $grades[$cat_id][$year]; ?></td>
					<?php else: ?>
						<td class="na">n/a</td>
					<?php endif; ?>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php foreach ($categories as $category): ?>
	<?php 
		$category_id = $category['Category']['id'];
		if (count($grades[$category_id]) == 1) {
			continue;
		}
		$chart = $charts[$category_id];
		$chart->div("grade_graph_$category_id");
	?>
	
	<div class="grade_graph">
		<aside>
			<h2>
				About <?php echo $category['Category']['name']; ?>
			</h2>
			<p>
				<?php echo str_replace("\n", '</p><p>', $category['Category']['short_description']); ?>
			</p>
		</aside>
		<div id="grade_graph_<?php echo $category_id; ?>">
			<?php $this->GoogleChart->createJsChart($chart); ?>
		</div>
		<p class="graph_note">
			<strong>GPA Key</strong>:
			4 = A; 3 = B; 2 = C; 1 = D; 0 = F
		</p>
		<br class="clear" />
	</div>
<?php endforeach; ?>