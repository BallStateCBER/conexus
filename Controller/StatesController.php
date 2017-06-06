<?php
App::uses('AppController', 'Controller');
App::uses('GoogleCharts', 'GoogleCharts.Lib');

/**
 * States Controller
 *
 * @property State $State
 */
class StatesController extends AppController {
	public $helpers = array('GoogleCharts.GoogleCharts');

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
			$chart = new GoogleCharts();
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
			'years' => range(2009, RELEASE2017 ? 2017 : 2016)
		));
	}
}
