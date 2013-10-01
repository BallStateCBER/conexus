<?php
App::uses('AppController', 'Controller');
App::uses('GoogleChart', 'GoogleChart.Lib');

/**
 * States Controller
 *
 * @property State $State
 */
class StatesController extends AppController {
	public $helpers = array('GoogleChart.GoogleChart');
	
	public function view($abbreviation = null) {
		$state = $this->State->find('first', array(
			'conditions' => array('State.abbreviation' => $abbreviation),
			'contain' => false
		));
		if (empty($state)) {
			throw new NotFoundException(__('Invalid state'));
		}
		
		$this->loadModel('Grade');
		$grades = $this->Grade->find('all', array(
			'conditions' => array('Grade.state_id' => $state['State']['id']),
			'order' => 'Grade.year ASC',
			'contain' => false
		));
		$arranged_grades = array();
		foreach ($grades as $grade) {
			$cat_id = $grade['Grade']['category_id'];
			$year = $grade['Grade']['year'];
			$arranged_grades[$cat_id][$year] = $grade['Grade']['grade'];
		}
		$grades = $arranged_grades;
		$grade_values = array('A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, 'F' => 0);
		
		$this->loadModel('Category');
		$categories = $this->Category->find('all', array(
			'contain' => false,
			'fields' => array('id', 'name', 'short_description')
		));
		
		// Assumption: Each series is continuous (so no series will skip a year and then resume at a later year)
		$charts = array();
		foreach ($categories as $category) {
			// Set up Google Chart
			$chart = new GoogleChart();
			$chart->type('LineChart');
			$chart->options(array(
				'title' => $state['State']['name'].' - '.$category['Category']['name'],
				'vAxis' => array(
					'title' => 'GPA',
					'maxValue' => 4,
					'minValue' => 0,
					'format' => '#.#'
				),
				'legend' => array(
					'position' => 'none'
				)
			));
			$chart->columns(array(
				'year' => array(
					'type' => 'string',
					'label' => 'Year'
				),
				'grade' => array(
					'type' => 'number',
					'label' => 'GPA'
				)
			));
			
			$category_id = $category['Category']['id'];
			foreach ($grades[$category_id] as $year => &$grade) {
				// Determine and assign numeric value for each grade
				$stripped_grade = strtoupper(substr($grade, 0, 1));
				if (! isset($grade_values[$stripped_grade])) {
					break;
				}
				$value = $grade_values[$stripped_grade];
				if (substr($grade, 1, 1) == '+') {
					$value += 0.3;
				} elseif (substr($grade, 1, 1) == '-') {
					$value -= 0.3;
				}
				$chart->addRow(array(
					'year' => $year,
					'grade' => min($value, 4) // Maximum of 4.0
				));
			}
			$charts[$category_id] = $chart;
		}
		
		$this->set(compact('abbreviation', 'state', 'grades', 'categories', 'charts'));
		$this->set(array(
			'title_for_layout' => $state['State']['name'],
			'years' => range(2013, 2009)
		));
	}
	
	/*
	public function import_data() {
		
		// Columns:
		// 		State	
		// 		Population (2011)	
		// 		Total Personal Income (in $1000s) - 2011	
		// 		Manufacturing Earnings (in $1000s) - 2011	
		// 		% Manufacturing share of economy - 2011
		// Source: Bureau of Economic Analysis
		
		$columns = array(
			'population',
			'total_personal_income',
			'manufacturing_earnings',
			'manufacturing_share'
		);
		
		$data = "
			Alabama	4802740	166414200	14553748	8.7%
			Alaska	722718	32904983	748279	2.3%
			Arizona	6482505	232559527	13605328	5.9%
			Arkansas	2937979	99933270	8510384	8.5%
			California	37691912	1676564972	124196726	7.4%
			Colorado	5116796	225591393	10221387	4.5%
			Connecticut	3580709	203703411	16591678	8.1%
			Delaware	907135	37768813	2183160	5.8%
			Florida	19057542	753982674	23368488	3.1%
			Georgia	9815210	354371740	24327219	6.9%
			Hawaii	1374810	59189985	786571	1.3%
			Idaho	1584985	52821134	3611298	6.8%
			Illinois	12869257	568049349	45592296	8.0%
			Indiana	6516922	231673951	32939743	14.2%
			Iowa	3062309	123933051	13958698	11.3%
			Kansas	2871238	116230434	11509317	9.9%
			Kentucky	4369356	147103393	12562974	8.5%
			Louisiana	4574836	176488770	12254675	6.9%
			Maine	1328188	50435496	3765363	7.5%
			Maryland	5828289	297464666	10370728	3.5%
			Massachusetts	6587536	353228041	25686515	7.3%
			Michigan	9876187	360806046	42186011	11.7%
			Minnesota	5344861	238767813	23757377	9.9%
			Mississippi	2978512	95835415	7708717	8.0%
			Missouri	6010688	229897646	18061788	7.9%
			Montana	998199	36507395	1069144	2.9%
			Nebraska	1842641	76624087	5584758	7.3%
			Nevada	2723322	103956791	2734099	2.6%
			New Hampshire	1318194	60356243	5461377	9.0%
			New Jersey	8821155	469115365	27213178	5.8%
			New Mexico	2082224	71992889	2302339	3.2%
			New York	19465197	983867508	38581673	3.9%
			North Carolina	9656401	349211807	31743533	9.1%
			North Dakota	683932	31287765	1530989	4.9%
			Ohio	11544951	436297197	49990141	11.5%
			Oklahoma	3791508	141334880	9626808	6.8%
			Oregon	3871859	146778178	13103171	8.9%
			Pennsylvania	12742886	541297313	41159486	7.6%
			Rhode Island	1051302	46248437	2640557	5.7%
			South Carolina	4679230	157564533	14744106	9.4%
			South Dakota	824082	34273530	2144226	6.3%
			Tennessee	6403353	233933162	20700332	8.8%
			Texas	25674681	1016529366	76351141	7.5%
			Utah	2817222	95194414	7810811	8.2%
			Vermont	626431	26205071	2153652	8.2%
			Virginia	8096604	371796308	14968273	4.0%
			Washington	6830038	302529308	21889625	7.2%
			West Virginia	1855364	62178478	3554495	5.7%
			Wisconsin	5711767	228887665	32065201	14.0%
			Wyoming	568158	26874672	793383	3.0%
		";
		
		$data_split = explode("\n", $data);
		
		$message = 'Importing data:<table>';
		foreach ($data_split as $line) {
			$line = trim($line);
			if (empty($line)) {
				continue;	
			}
			$line_split = explode("\t", $line);
			$state_name = trim(array_shift($line_split));
			$state_id = $this->State->field('id', array('name' => $state_name));
			if (! $state_id) {
				$message .= "<tr><td>$state_name not a recognized state</td><td></td><td></td><td></td></tr>";
				continue;
			}
			$this->State->id = $state_id;
			foreach ($line_split as $i => $value) {
				$value = str_replace('%', '', $value);
				$value = trim($value);
				$category = $columns[$i];
				$this->State->saveField($category, $value);
				$message .= "<tr><td>$state_name</td><td>$category</td><td>$value</td><td>Imported</td></tr>";
			}
		}
		$this->set('message', $message.'</table>');
		return $this->render('DataCenter.Common/message');
	}
	*/
}
