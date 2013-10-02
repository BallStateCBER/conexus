<h1 class="page_title">
	<?php echo $title_for_layout; ?>
</h1>

<?php
$definitions = array(
	array("Adult Basic Education", "Education in basic reading and writing, offered either through community/technical colleges or through state workforce development agencies."),
	array("Educational Attainment", "The highest level of school completed by a person or group."),
	array("Exports", "Products or commodities sold to foreign individuals and firms (businesses)."),
	array("Foreign Direct Investment", "Expenditures (spending) by foreign-owned firms on plant and equipment in a region."),
	array("Human Capital", "A measure of education and skill level, and (in some settings) health of residents and workers within a region."),
	array("Imports", "Products or commodities purchased from foreign firms (businesses)."),	
	array("Infrastructure", "Roads, railways, bridges and other transportation-related public goods."),	
	array("Logistics", "Transportation and warehousing industry groups that make it possible to move and store goods."),	
	array("Manufacturing", "The production of consumer durable and non-durable goods."),	
	array("Productivity", "The value of goods sold by a firm adjusted to a per-worker basis."),	
	array("R&D", "Research and development, both in primary and applied science, usually measured in dollars."),	
	array("Unemployment Insurance", "A federal program dating to 1933 that requires firms to participate in state-regulated insurance plans to compensate workers who are laid off or discharged from work."),	
	array("Value-Added", "Firm or industry measure of the value of the product sold, minus all input costs."),	
	array("Workers' Compensation", "A federal program dating to 1913 that requires firms to provide disability and death insurance through state-administered or regulated insurance plans.")
);
?>

<dl class="glossary">
	<?php foreach ($definitions as $definition): ?>
		<dt><?php echo $definition[0]; ?></dt>
		<dd><?php echo $definition[1]; ?></dd>
	<?php endforeach; ?>
</dl>