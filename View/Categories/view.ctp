<div class="category_report">
	<aside>
		<h2>About <?php echo $category['Category']['name']; ?></h2>
		<p>
			<?php echo str_replace("\n", '</p><p>', $category['Category']['description']); ?>
		</p>
	</aside>
	
	<h1>
		<?php echo $year; ?> <?php echo $category['Category']['name']; ?>
	</h1>
	
	<p>
		Click on a state to view its full report card profile.
	</p>
	
	<div id="report_view_controls">
		<a href="#" id="show_map" class="selected controls">
			<img src="/data_center/img/icons/map.png" />
			<span>Map</span>
		</a>
		<a href="#" id="show_table" class="controls">
			<img src="/data_center/img/icons/table.png" />
			<span>Table</span>
		</a>
	</div>
	<?php $this->Js->buffer("
		$('#show_map').click(function (event) {
			event.preventDefault();
			$('#grades_table_wrapper').hide();
			$('#map_us_wrapper').show();
			$('#report_view_controls .selected').removeClass('selected');
			$(this).addClass('selected');
		});
		$('#show_table').click(function (event) {
			event.preventDefault();
			$('#grades_table_wrapper').show();
			$('#map_us_wrapper').hide();
			$('#report_view_controls .selected').removeClass('selected');
			$(this).addClass('selected');
		});
	"); ?>
	
	<div id="map_us_wrapper">
		<?php echo $this->element('map_us'); ?>
		<ul id="legend_grades">
			<li class="grade_a">A</li>
			<li class="grade_b">B</li>
			<li class="grade_c">C</li>
			<li class="grade_d">D</li>
			<li class="grade_f">F</li>
		</ul>
	</div>
	
	<div id="svg_not_supported" style="display: none;">
		Sorry, the version of your browser that you are using can not display this map. 
		Please upgrade to the newest version of your browser to view it:
		<ul>
			<li>
				<a href="http://windows.microsoft.com/en-US/internet-explorer/downloads/ie">Internet Explorer 9+</a>
			</li>
			<li>
				<a href="http://getfirefox.com">Firefox</a>
			</li>
			<li>
				<a href="http://www.google.com/chrome">Chrome</a>
			</li>
			<li>
				<a href="http://www.apple.com/safari/download/">Safari</a>
			</li>
			<li>
				<a href="http://www.opera.com/download/">Opera</a>
			</li>
		</ul>
	</div>
	
	<div id="grades_table_wrapper" style="display: none;">
		<table class="grades">
			<thead>
				<tr>
					<th>State</th>
					<th>Grade</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($grades as $row): ?>
					<tr>
						<th>
							<?php echo $this->Html->link($row['State']['name'], array(
								'controller' => 'states', 'action' => 'view', 'state_abbrev' => $row['State']['abbreviation']
							)); ?>
						</th>
						<td>
							<?php echo $row['Grade']['grade']; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>

<?php
$this->Html->script('modernizr.custom.61874.js', array('inline' => false));
$this->Js->buffer("
	if ($('html').first().hasClass('inlinesvg')) {
		setupMap({".$js_grade_definitions."});
	} else {
		$('#grades_table_wrapper').show();
		$('#map_us_wrapper').hide();
		$('#map_us_wrapper').html($('#svg_not_supported'));
		$('#svg_not_supported').show();
		$('#report_view_controls .selected').removeClass('selected');
		$('#show_table').addClass('selected');
	}
");