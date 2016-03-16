<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>
<?php
	$sources = array(
		'U.S. Department of the Census' => 'http://www.census.gov',
		'U.S. Department of Transportation Center for Transportation Statistics' => 'http://www.bts.gov/',
		'National Center for Educational Statistics' => 'http://nces.ed.gov',
		'American Association of Retired Persons (AARP)' => 'http://www.aarp.org',
		'IMPLAN' => 'http://implan.com',
		'Tax Foundation' => 'http://taxfoundation.org',
		'U.S. Internal Revenue Service' => 'http://www.irs.gov',
		'Boston College Center for Retirement Research' => 'http://crr.bc.edu',
		'U.S. Department of the Census 2012 Statistical Abstract' => 'https://www.census.gov/library/publications/2011/compendia/statab/131ed.html',
		'U.S. Department of Commerce International Trade Administration' => 'http://trade.gov',
		'U.S. Bureau of Economic Analysis' => 'http://www.bea.gov',
		'North American Industrial Classification System (NAICS)' => 'http://www.census.gov/eos/www/naics/',
		'Census of Manufacturers' => 'http://www.census.gov/manufacturing/asm/',
		'National Science Foundation' => 'http://www.nsf.gov',
		'U.S. Patent and Trademark Office' => 'http://www.uspto.gov'
	);
	ksort($sources);
?>

<ul>
	<?php foreach ($sources as $source => $url): ?>
		<li>
			<?php if ($url): ?>
				<a href="<?php echo $url; ?>">
					<?php echo $source; ?>
				</a>
			<?php else: ?>
				<?php echo $source; ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>