function setupMap(grades) {
	// for each state: if path, color. if group, color child paths
	var states = {AL: 'Alabama', AK: 'Alaska', AZ: 'Arizona', AR: 'Arkansas', CA: 'California', CO: 'Colorado', CT: 'Connecticut', DE: 'Delaware', FL: 'Florida', GA: 'Georgia', HI: 'Hawaii', ID: 'Idaho', IL: 'Illinois', IN: 'Indiana', IA: 'Iowa', KS: 'Kansas', KY: 'Kentucky', LA: 'Louisiana', ME: 'Maine', MD: 'Maryland', MA: 'Massachusetts', MI: 'Michigan', MN: 'Minnesota', MS: 'Mississippi', MO: 'Missouri', MT: 'Montana', NE: 'Nebraska', NV: 'Nevada', NH: 'New Hampshire', NJ: 'New Jersey', NM: 'New Mexico', NY: 'New York', NC: 'North Carolina', ND: 'North Dakota', OH: 'Ohio', OK: 'Oklahoma', OR: 'Oregon', PA: 'Pennsylvania', RI: 'Rhode Island', SC: 'South Carolina', SD: 'South Dakota', TN: 'Tennessee', TX: 'Texas', UT: 'Utah', VT: 'Vermont', VA: 'Virginia', WA: 'Washington', WV: 'West Virginia', WI: 'Wisconsin', WY: 'Wyoming'};
	
	$('#map_us path.state').each(function() {
		var state_abbrev = this.id.replace('map_us_', '');
		if (state_abbrev.length > 2) {
			// Michigan's path is split between #map_us_MI_upper and #map_us_MI_lower
			state_abbrev = state_abbrev.substring(0, 2);
		}
		
		// Skip the District of Columbia
		if (state_abbrev == 'DC') {
			return;
		}
		
		// Get grade
		var full_grade = grades[state_abbrev];
		var simple_grade = full_grade;
		if (simple_grade.length > 1) {
			simple_grade = full_grade.substring(0, 1);
		}
		simple_grade = simple_grade.toLowerCase();
		
		// Color according to grade
		$(this).addClass('grade_'+simple_grade);
		
		$(this).qtip({
			content: states[state_abbrev]+': '+full_grade,
			solo: true,
			effect: false,
			show: {delay: 0, effect: false},
			hide: {delay: 0, effect: false},
			position: {
				my: 'bottom center',
				at: 'top center',
				target: 'mouse',
				adjust: {y: -20, mouse: true}
			}
		});
		this.addEventListener('click', function(event) {
			showStateReport(state_abbrev);
		});
	});
}

function showStateReport(state_abbrev) {
	var url = '/state/'+state_abbrev;
	window.location = url;
}

function removeCategoryHighlight() {
	$('#categories .selected').removeClass('selected');
}