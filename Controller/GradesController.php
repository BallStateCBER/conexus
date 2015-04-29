<?php
App::uses('AppController', 'Controller');
/**
 * Grades Controller
 *
 * @property Grade $Grade
 */
class GradesController extends AppController {


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Grade->recursive = 0;
		$this->set('grades', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Grade->id = $id;
		if (!$this->Grade->exists()) {
			throw new NotFoundException(__('Invalid grade'));
		}
		$this->set('grade', $this->Grade->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Grade->create();
			if ($this->Grade->save($this->request->data)) {
				$this->Session->setFlash(__('The grade has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The grade could not be saved. Please, try again.'));
			}
		}
		$categories = $this->Grade->Category->find('list');
		$states = $this->Grade->State->find('list');
		$this->set(compact('categories', 'states'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Grade->id = $id;
		if (!$this->Grade->exists()) {
			throw new NotFoundException(__('Invalid grade'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Grade->save($this->request->data)) {
				$this->Session->setFlash(__('The grade has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The grade could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Grade->read(null, $id);
		}
		$categories = $this->Grade->Category->find('list');
		$states = $this->Grade->State->find('list');
		$this->set(compact('categories', 'states'));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Grade->id = $id;
		if (!$this->Grade->exists()) {
			throw new NotFoundException(__('Invalid grade'));
		}
		if ($this->Grade->delete()) {
			$this->Session->setFlash(__('Grade deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Grade was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

	private function __import($year, $columns, $grades) {
		$grades_split = explode("\n", $grades);

		App::uses('Category', 'Model');
		$Category = new Category();
		$categories = $Category->find('list');

		App::uses('State', 'Model');
		$State = new State();

		$message = 'Importing grades:<table>';
		foreach ($grades_split as $line) {
			$line = trim($line);
			if (empty($line)) {
				continue;
			}
			$line_split = explode("\t", $line);
			$state_name = trim(array_shift($line_split));
			$state_id = $State->field('id', array('name' => $state_name));
			if (! $state_id) {
				$message .= "<tr><td>$year</td><td>$state_name not a recognized state</td><td></td><td></td><td></td></tr>";
				continue;
			}
			foreach ($line_split as $i => $grade) {
				$grade = trim($grade);
				$category_id = $columns[$i];

				// Look for an existing entry for this grade
				$count = $this->Grade->find('count',
					array(
						'conditions' => compact('category_id', 'state_id', 'year', 'grade')
					)
				);

				if ($count) {
					$result = 'Previously imported';
				} else {
					$this->Grade->create(array(
						'Grade' => compact('category_id', 'state_id', 'year', 'grade')
					));
					if ($this->Grade->save()) {
						$result = 'Imported';
					} else {
						$result = 'Error importing';
					}
				}
				$message .= "<tr><td>$year</td><td>$state_name</td><td>$categories[$category_id] (#$category_id)</td><td>$grade</td><td>$result</td></tr>";
			}
		}
		$this->set('message', $message.'</table>');
		return $this->render('DataCenter.Common/message');
	}

	public function import_2015() {
		$year = 2015;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5, 	// Tax Climate
			8, 	// Diversification
			6 	// Expected Fiscal Liability Gap
		);

		// Data copied from Excel file
		$grades = "
			Alabama	B	C	F	B-	B	C	C	B	B
			Alaska	F	D	B	D-	F	F	B+	F	C-
			Arizona	C	D	D-	C+	D	C+	B-	F	C
			Arkansas	C	C	D-	A	C	F	D	C+	D-
			California	C	B	C	C	C	A	D	D	D
			Colorado	D	C-	C+	B	D+	B	C	C	C-
			Connecticut	B	C-	C+	F	B+	A	D	D	C-
			Delaware	D	D-	C-	D	A	C	B	C	A
			Florida	D	C	C	C	D	C	A	B+	B
			Georgia	D+	B	D	A	B	C	C-	A	B+
			Hawaii	F	F	C	C-	D-	F	B	D+	F
			Idaho	B-	D	C	B	F	D	C	F	C+
			Illinois	C	A	C	C	C+	B	D-	C+	F
			Indiana	A	A	C	D+	A	B-	A	C	B-
			Iowa	A	B+	B	C	C	C	D-	C	B
			Kansas	A	B-	B-	B	C	C-	C	C-	D
			Kentucky	B+	B	D	D	A	C	C	C	F
			Louisiana	C	B+	F	C+	C	D+	C	C-	C
			Maine	C	F	B	F	D	F	D	C	C-
			Maryland	D	D	C	C-	C	C	C	C-	C
			Massachusetts	C-	D	C+	D+	B-	B+	D	D	D
			Michigan	A	C	D	C+	B	A	C+	D-	D+
			Minnesota	B-	B+	A	B	C	B	F	C	C+
			Mississippi	B-	C-	F	C	D	D-	B	A	D-
			Missouri	C	C+	C	B	C	D	A	A	B
			Montana	F	C	B	C	F	D	A	C	D
			Nebraska	C-	C	A	B+	C-	C-	C-	D+	A
			Nevada	F	D	D+	A	C	C	B	D	D
			New Hampshire	B	F	A	C	B+	C	D+	D	C-
			New Jersey	C-	B	C	C-	C+	B	F	D-	F
			New Mexico	F	F	F	C	F	D	C	F	D
			New York	D-	C	C-	D	C-	C+	F	B-	C
			North Carolina	B-	C	C	B	B	B	C+	B	A
			North Dakota	D	C+	A	C	D+	C-	B	C-	C
			Ohio	B	A	C-	C	A	B	C	C+	C
			Oklahoma	C	C+	D	B-	C-	D-	B	C	C
			Oregon	B	D+	C-	C	C-	B+	C	F	A
			Pennsylvania	C	A	C	D-	C+	C	F	B+	D
			Rhode Island	D+	F	C-	C-	D	C-	F	B	C
			South Carolina	A	D+	D	D	A	C-	C-	B	C
			South Dakota	C-	C	B	B+	F	F	B	C	B
			Tennessee	B	B-	D	C	B	C	C-	B	B-
			Texas	C	A	C-	A	B	A	C	C	A
			Utah	C	C-	B+	A	C-	C	A	B-	B+
			Vermont	C	D-	B	F	C	D	D	C-	C
			Virginia	D	C	C	C-	D	B	C+	A	C
			Washington	C+	C	A	F	C	A	C	A	B
			West Virginia	C-	C-	F	F	B	D+	C	C	C-
			Wisconsin	B+	B	B+	D	C	C+	D+	B	C+
			Wyoming	D-	C	B-	D	D-	D	B+	D	C";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2014() {
		$year = 2014;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5, 	// Tax Climate
			8, 	// Diversification
			6 	// Expected Fiscal Liability Gap
		);

		// Data copied from Excel file
		$grades = "
			Alabama	B-	C	F	C+	B	C-	B-	B	C
			Alaska	F	D	C+	F	D	F	B+	F	C-
			Arizona	C	C-	D	B-	D-	C	B	D	D+
			Arkansas	C	C	D-	A	C-	F	C-	C+	C
			California	C	B	C-	C-	C	A	D	D-	D
			Colorado	D+	D+	C+	B	D	B	C	C	C
			Connecticut	B	D	C	D-	B+	B-	D-	D	C-
			Delaware	D	D-	C-	F	A	C	B	C	A
			Florida	D	C	C	C	D	D	A	B+	B-
			Georgia	D+	B	D	A	B	C	C	A	A
			Hawaii	F	F	C	D	F	F	C+	C-	D-
			Idaho	C+	D	B	B	D	B	C	F	C+
			Illinois	C	A	C	C	C+	A	D-	C	F
			Indiana	A	A	C-	C	A	C+	A	C	C
			Iowa	A	B	B	C	C	C-	F	C-	C
			Kansas	B+	C	B-	B+	C-	C	C	C	C-
			Kentucky	B	B+	D	D	A	D	C-	C+	F
			Louisiana	C	B	F	D	B-	C	C	C-	C
			Maine	C	D-	B	F	D	F	D+	C	C+
			Maryland	D	D	C	C-	C	B	D+	C-	D
			Massachusetts	C	D	C+	D	B	A	D	D	B-
			Michigan	A	B-	D	C+	B	C	B	F	D+
			Minnesota	C+	B	A	B	C	A	F	C	C
			Mississippi	C+	C-	F	C	C	F	B	A	D-
			Missouri	C	B	C	B	C	D-	A	A	B
			Montana	F	C-	B+	C-	F	D-	A	C	F
			Nebraska	C-	C	B+	B+	C-	C	C-	D+	B
			Nevada	F	D	D+	A	D+	C-	B	D	C-
			New Hampshire	B	F	A	C	C+	C+	C-	D	D
			New Jersey	C	C+	C	C-	B-	B	F	D-	D-
			New Mexico	D-	F	F	C	F	C+	C-	F	D
			New York	F	C	C-	D+	C	C	F	B-	B+
			North Carolina	B-	C+	C	C	B	B	D	B	A
			North Dakota	D	C+	A	C+	C-	D	B	C-	D+
			Ohio	B+	A	C	C	A	C	C	C+	C
			Oklahoma	C-	C	D	B	D+	D+	C+	C	C
			Oregon	A	C	C	C	D-	A	C	F	B
			Pennsylvania	C	A	C	D	C	C	D	B+	B
			Rhode Island	D	F	C-	D	F	D	F	B	D
			South Carolina	A	C-	D-	C	A	C-	C	B	C
			South Dakota	C-	D+	B	A	F	D	B	C	B
			Tennessee	B	B+	D	B-	B+	C-	C	B	C+
			Texas	C	A	D+	B	B	A	C	C	B
			Utah	C	C-	B+	A	C	A	A	B-	A
			Vermont	B	F	B	D-	C	C	D	D+	C
			Virginia	D	C	C	D+	C	B	C+	A	C
			Washington	C	C	A	F	C	A	C	A	A
			West Virginia	C-	C	F	F	B-	D	C	C	F
			Wisconsin	B+	B-	A	C	C	C	D	B	A
			Wyoming	D-	C	B-	C-	D	D+	B+	D	C";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2013() {
		$year = 2013;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5, 	// Tax Climate
			8, 	// Diversification
			6 	// Expected Fiscal Liability Gap
		);

		// Data copied from Excel file
		$grades = "
			Alabama	B-	C	F	A	B	D	B	B	C
			Alaska	F	D	C	C-	F	F	B+	F	D
			Arizona	C	C	D	B	D-	C	B	F	C
			Arkansas	C	C	F	A	C	F	C-	C+	C
			California	C+	B	C	F	C	A	D	D	D
			Colorado	D+	C-	B	B	D	B	C	C	C-
			Connecticut	B	D	C	C	A	C+	D-	D	C-
			Delaware	D	F	C-	F	B	C	B	C	A
			Florida	D	C+	C	C+	C-	D+	A	B+	B
			Georgia	D+	B	D	B	C+	C	C	A	A
			Hawaii	F	F	C	C	F	F	C	C	F
			Idaho	C	D	C	C	F	B	C+	F	C
			Illinois	C	A	C	F	B	B+	F	C	F
			Indiana	A	A	D	C-	A	C+	A	C	C+
			Iowa	A	B	A	C	C-	C	D-	C	C+
			Kansas	A	C	C+	B	C	C	C	C-	C-
			Kentucky	B	B	D	C	A	D	C	B-	F
			Louisiana	C	B+	F	B-	B-	C	C	C-	C-
			Maine	C	D-	B-	F	D	D-	D+	C	D-
			Maryland	D	D	C+	C+	D	B	D	C-	C
			Massachusetts	C	D	B-	C	C+	A	D	D	B
			Michigan	A	B-	C-	C	A	C	B-	D-	D
			Minnesota	B	B	A	C	C-	A	F	C	B
			Mississippi	C+	C-	F	B	D	F	B	A	D
			Missouri	C	B	C	A	B-	D+	A	A	B
			Montana	D-	D+	B	D	F	F	A	C	F
			Nebraska	C-	C+	B+	C+	D+	C-	C-	D	B
			Nevada	F	D	D	B	C	C-	B-	D+	C
			New Hampshire	B	F	A	D	C	B-	C-	D	C
			New Jersey	C-	C+	C+	D	B	B-	F	D-	D
			New Mexico	F	D-	D-	C+	D-	B-	C-	F	D+
			New York	F	C	C-	C-	D+	C+	F	B-	B+
			North Carolina	C+	C	C	C	C	B	D	B	A
			North Dakota	D-	C	B+	B+	C	D	B	C-	C
			Ohio	A	A	C-	D	A	C	C-	C+	C
			Oklahoma	C-	C	D+	C	C-	C-	B-	C	D
			Oregon	A	C	C	C-	D	B+	C+	F	B
			Pennsylvania	C	A	C	D+	C	C	D	B	C
			Rhode Island	D	F	C-	C-	D	D	F	B+	D-
			South Carolina	A	D+	D	D	A	C-	C	B	D+
			South Dakota	C-	C-	B	A	F	D-	B	C	C+
			Tennessee	B	B+	D-	B+	B	C-	C	B	B
			Texas	C	A	C-	B	B+	A	C	C	B
			Utah	C	C-	A	A	C	B+	A	B	A
			Vermont	B	F	B	D+	C	C	D	D+	C
			Virginia	D	C-	C	C	C	B	C	A	C
			Washington	C	C	A	F	C+	A	C	A	B+
			West Virginia	C-	C	F	D-	B	D	C	C-	F
			Wisconsin	A	B-	B	D-	C	C	D+	C+	A
			Wyoming	D	C	B	D	C-	D	B+	D	C";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2012() {
		$year = 2012;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5, 	// Tax Climate
			8, 	// Diversification
			10, // Venture Capital Per capita
			6 	// Expected Fiscal Liability Gap
		);

		// Data copied from Excel file
		$grades = "
			Alabama	B-	C	F	B+	B	D	B	A	D-	C
			Alaska	F	D	C	C-	D	D+	B+	F	F	D+
			Arizona	C	C	D	B	F	C	B	D+	C+	C
			Arkansas	C	C	D	A	C	F	C	C	F	C
			California	C+	B	C	D	C	A	D	D	A	C
			Colorado	D+	C-	C	C+	F	B	C+	C-	A	C-
			Connecticut	C+	D	B-	C	A	B+	D-	C	C	D
			Delaware	D	F	D+	F	B+	C	B	C	C	A
			Florida	D	C+	C-	C+	D+	D	A	C+	C	B
			Georgia	D+	B+	C-	B+	B	C	C	B	C	A
			Hawaii	F	F	C	B	F	F	C	C	D-	D
			Idaho	C+	D	C	C-	D-	C	C	F	D	C
			Illinois	C	A	C	F	B	B	F	C	B	F
			Indiana	A	A	C-	D+	A	B+	A	C+	C	B
			Iowa	A	B	A	C	C-	C	D-	C-	C-	B-
			Kansas	B+	C	B	B	D+	C	C	A	C	C-
			Kentucky	B	B	D	C	B+	D	C	B+	D	D-
			Louisiana	C	B	F	C-	B-	D+	C	C-	D+	C-
			Maine	C	D-	B	F	D+	D+	D	C	C	D
			Maryland	D	D	B-	B	D	B	D+	C-	B-	C
			Massachusetts	C	D	B-	B-	C+	A	D	D	A	C
			Michigan	A	C+	C-	C-	B+	A	C-	D-	C-	C-
			Minnesota	B-	B	A	C	C	B	F	C	B	B
			Mississippi	C+	C-	F	B	C	D-	B	A	F	D
			Missouri	C	B	C	A	C	C	A	B+	C	B
			Montana	D-	D+	C	C	F	F	A	C	D	D-
			Nebraska	C-	C+	B+	C	C-	C-	C-	D	F	B
			Nevada	F	D	D-	C	C	C	B-	D+	D+	C
			New Hampshire	B+	F	A	D	C+	C+	C	D	B	C
			New Jersey	C-	B-	C+	D	B	B	F	F	B	C
			New Mexico	F	D-	F	B	F	C+	C	D	C	D
			New York	F	C	C-	C	C	C+	F	B-	A	B+
			North Carolina	B	C	C	C	C+	C	D	B	C	A
			North Dakota	D	C	A	A	C	D	B-	D-	C-	F
			Ohio	A	A	C	D	A	C	C	B-	C	D+
			Oklahoma	C-	C	D	C	C-	C-	B	C	C-	D
			Oregon	B	C	B-	F	D	B-	C+	F	B	C+
			Pennsylvania	C	A	C	D	B-	C	D	B	B-	B-
			Rhode Island	D	F	C-	C	D-	F	F	C+	C+	F
			South Carolina	B+	D+	D	D+	A	C-	C	B	D	C
			South Dakota	C-	C-	B	A	D	F	B	F	D	C
			Tennessee	B	B+	D-	C	B	C-	C-	B	C	D-
			Texas	C	A	D	B-	A	A	C	C	B	A
			Utah	C	C-	B+	A	C	B-	A	B	B+	B
			Vermont	B	F	B	C-	C-	C-	D	C-	C+	C
			Virginia	D	C-	C	C+	D	B	C+	A	B+	C+
			Washington	C	C	A	F	C	A	C	A	A	B+
			West Virginia	C-	C	F	D-	C+	D-	C	C	D	F
			Wisconsin	A	B-	B	D-	C	C	D+	C	C-	A
			Wyoming	D-	C	B	D	C	D	B+	D	F	C+
		";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2011() {
		$year = 2011;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5, 	// Tax Climate
			8, 	// Diversification
			10	// Venture Capital Per capita
		);

		// Data copied from Excel file
		$grades = "
			Alabama	B	C	F	B+	B	C	B	B	F
			Alaska	F	D	C-	C-	D	D	A	F	F
			Arizona	C	C	D+	C	F	C	B	C-	C
			Arkansas	C+	C	D-	A	C-	F	D+	C	D-
			California	C+	B	C	D	C	A	D	D	A
			Colorado	D+	C-	C	C+	D+	B	B	C	A
			Connecticut	C+	D	B	C	B	B+	D-	A	B
			Delaware	C-	F	C-	F	B+	A	B	C	C
			Florida	D	C+	C-	B-	D-	D+	B+	C+	C-
			Georgia	D+	B+	D+	C	C+	F	C	B+	C
			Hawaii	F	F	C	B	F	C-	B	C-	C-
			Idaho	C+	D	C	C	D	D	C+	F	D+
			Illinois	C	A	C	D-	B	C+	D	C	B-
			Indiana	A	A	C	C-	A	C+	A	C	C-
			Iowa	A	B	B	C	D	C	F	C-	C
			Kansas	A	C	C+	A	C	D	C	D	C
			Kentucky	B	B	D-	C	B+	C-	C	C+	D
			Louisiana	C	B	F	C	C+	B	C	C	D
			Maine	C	D-	B	F	C	D	D	C	D-
			Maryland	D	D	B	B-	D+	B	D	C-	B+
			Massachusetts	C-	D	B-	C	C	B+	D	D	A
			Michigan	A	C+	D	D	A	C	C-	F	C
			Minnesota	B-	B	A	C	C	D+	F	C	C
			Mississippi	C+	C-	F	C	C-	C	B	A	F
			Missouri	C	B	C	B+	B	D-	A	B	C-
			Montana	D	D+	C+	C+	F	C-	A	C	D
			Nebraska	C-	C+	A	C	F	D	C	D-	D+
			Nevada	F	D	D	B	D	B	C	D+	C
			New Hampshire	B+	F	A	F	B	C-	C	D	C+
			New Jersey	C-	B-	C+	D+	C+	C	F	F	B
			New Mexico	F	D-	F	B	F	A	C	F	C
			New York	D-	C	C-	C	C	C	F	C+	A
			North Carolina	B-	C	C	D	C	C	C-	B	B
			North Dakota	D	C	A	A	C	B-	C+	D+	F
			Ohio	A	A	C	D	A	C-	D-	B-	C
			Oklahoma	C	C	D	B	D	B	B	C	D
			Oregon	B	C	C	D	C	A	C	D-	B-
			Pennsylvania	C	A	C	D	B-	C	D	B+	C+
			Rhode Island	D	F	C-	C-	D	D	F	B	B+
			South Carolina	B+	D+	D	C-	A	F	C	A	D
			South Dakota	D+	C-	B	A	D	F	B+	C	F
			Tennessee	B	B+	D	B-	A	C+	C-	B	C-
			Texas	C	A	D	B	B+	A	C	C	C+
			Utah	C	C-	B	A	C	C	A	B	B
			Vermont	B	F	B	C-	C	D-	C-	D	B
			Virginia	D-	C-	C	B	C	C+	B	A	B
			Washington	C	C	A	F	C	B	C	A	A
			West Virginia	C-	C	F	F	B	C	D+	C-	D
			Wisconsin	B+	B-	B+	D-	C	D-	C-	B-	C
			Wyoming	D-	C	B+	C-	C-	B	B	D	C
		";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2010() {
		$year = 2010;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5, 	// Tax Climate
			8, 	// Diversification
			10	// Venture Capital Per capita
		);

		// Data copied from Excel file
		$grades = "
			Alabama	B	C	F	A	C+	C-	B	B	C-
			Alaska	F	C-	D	D	C-	C-	B+	F	F
			Arizona	C-	C-	C-	B+	F	C	B	D	C
			Arkansas	C+	C+	D-	B+	C+	F	D	C	F
			California	C	C	B	D+	C	A	D	D-	A
			Colorado	D	D+	C+	C+	D+	B	B	C	A
			Connecticut	C	D-	B	D	A	A	D	B	B
			Delaware	C	D	D	F	B+	A	B-	C	C
			Florida	D	C	C	C	C	C	A	C+	C
			Georgia	D+	B	C-	C	B	D+	C	B+	B-
			Hawaii	F	F	D+	B-	F	C-	B	C	C-
			Idaho	C+	D	C	B	F	D	C+	F	C
			Illinois	C	A	C	D-	C	B	D	C	C
			Indiana	A	B+	C-	C	A	C	A	C-	C
			Iowa	A	A	B	C	D	C	F	C	C+
			Kansas	A	B	B-	B	C	D+	C	A	D
			Kentucky	A	B	D-	B	A	D-	C	C	D
			Louisiana	C	B-	F	A	C	C+	C	C	D
			Maine	C	D	C	F	D	F	D+	C-	C-
			Maryland	D	F	B-	C+	C	B-	D+	C-	B+
			Massachusetts	C	D	B	C	B	B+	F	D	A
			Michigan	A	C	C-	C-	B	C+	D	F	C
			Minnesota	B-	B	A	C-	C	B	D-	C	B
			Mississippi	C	C	F	B	D+	F	B	A	F
			Missouri	C	B-	C	C	B-	D	A	B	D
			Montana	D	C-	C	C	D-	D	A	C	C
			Nebraska	D+	A	B+	C	C-	D	C	D	F
			Nevada	F	D	D	B	D-	C	B-	D+	C-
			New Hampshire	B	F	A	D-	B	C	C-	D	B-
			New Jersey	C-	C+	B	D	B-	B	F	F	A
			New Mexico	D	D	F	A	F	C	C	F	D+
			New York	F	D+	C	D	C	B+	F	B-	B
			North Carolina	B-	C	C	C-	A	B-	C	B	B
			North Dakota	D	B	B	A	C-	D	C	C-	C
			Ohio	A	A	C	D	B	C	D-	C	C
			Oklahoma	C	C	D	B-	D	C	C+	D-	D-
			Oregon	B	C	C	C	D+	B	C	D	C+
			Pennsylvania	C	B	A	C-	C+	B	D	B	B
			Rhode Island	D	F	D+	C-	D-	C+	F	B-	B
			South Carolina	B	C-	D	C	A	F	C	B+	D
			South Dakota	C-	C	C	A	D	F	B+	C+	D-
			Tennessee	B	B+	D	D+	B+	C-	C-	A	C-
			Texas	B-	A	C-	C	B	A	C	C	C
			Utah	C-	C-	A	B	C	C	A	B	B+
			Vermont	B	F	C+	F	C	D	C-	D	C
			Virginia	D-	D	C+	B-	C	C	B-	A	C+
			Washington	C	C	A	F	C	A	C	A	A
			West Virginia	C-	C	F	D	C	D-	C-	C-	D
			Wisconsin	B+	C+	B+	F	C+	C-	C-	C+	D+
			Wyoming	F	C	C	C	D	C	B	D+	F
		";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2009() {
		$year = 2009;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5	// Tax Climate
		);
		//

		// Data copied from Excel file
		$grades = "
			Alabama	B	C	F	A	B	B+	B
			Alaska	F	B	C	F	F	C	C+
			Arizona	C	C	D	A	D	F	C
			Arkansas	C	B	D	B	C	D+	C
			California	C	D	B	D	C	C	D
			Colorado	D	D	C-	C+	D-	A	A
			Connecticut	C+	F	B-	F	B+	C+	C
			Delaware	C-	C	C	F	B	A	B
			Florida	D	C	B+	C	D+	C	A
			Georgia	D	C	C-	B	C	D	C+
			Hawaii	F	D-	B+	C	D-	A	B
			Idaho	B-	C-	D	A	D	D+	C
			Illinois	C	C	B	C	A	C	D
			Indiana	A	B-	D+	C	A	C	A
			Iowa	A	A	C	B	C	C	F
			Kansas	A	B	C+	B+	B	D	C
			Kentucky	B+	A	D-	D+	A	D-	C-
			Louisiana	C+	C+	F	B-	C	C	C
			Maine	C	C-	B	F	D	D	D-
			Maryland	D-	D	C+	C	C-	C	C
			Massachusetts	D+	D-	C	C	C	C+	D
			Michigan	A	D+	C-	D	A	D	C-
			Minnesota	C	C	A	C	C+	A	F
			Mississippi	C	C	D+	B	C-	F	B-
			Missouri	C	B	C	C+	B-	B+	A
			Montana	D	C+	C	C+	F	C-	A
			Nebraska	D+	A	B	B	C	C	D
			Nevada	F	D	F	B+	D	C	B-
			New Hampshire	B	C	B-	D	C+	C	C
			New Jersey	C	B	C	D-	C	C	F
			New Mexico	D	F	C	C	F	B	C
			New York	F	F	A	C-	C	C-	F
			North Carolina	C	C	C	B-	C+	B	C-
			North Dakota	C-	A	C	B	C	F	C
			Ohio	A	B+	D	C	A	D	D-
			Oklahoma	C	C-	D-	C-	C	F	B
			Oregon	B-	C	C	C	C-	A	C+
			Pennsylvania	C	C	C	D	B	B	D+
			Rhode Island	D	F	B	D	F	C+	F
			South Carolina	B	D	D	C	B	D	C
			South Dakota	C-	C	B	A	D	C	B
			Tennessee	B	B+	D	C-	B+	C	C
			Texas	B	A	F	C	B-	B	C
			Utah	C	C+	A	A	C	B	B
			Vermont	B	D	C	F	C	C-	D
			Virginia	F	F	C	D+	D+	B-	B+
			Washington	C+	D+	A	D-	C	B-	C
			West Virginia	C	B-	F	D	D	F	D+
			Wisconsin	B+	C	C+	C	B	D-	D
			Wyoming	D-	B	A	C	F	B	B+
		";

		return $this->__import($year, $columns, $grades);
	}

	public function import_2008() {
		$year = 2008;

		// Category IDs corresponding to each column after state name in the $grades table
		$columns = array(
			1, 	// Manufacturing Industry
			2, 	// Logistics Industry
			3, 	// Human Capital
			4, 	// Benefits Costs
			7, 	// Global Position
			9, 	// Productivity and Innovation
			5	// Tax Climate
		);
		//

		// Data copied from Excel file
		$grades = "
			Alabama	B	C	F	A	B	B+	B
			Alaska	F	B	C	F	F	C	C+
			Arizona	C	C	D	A	D	F	C
			Arkansas	C	B	D	B	C	D+	C
			California	C	D	B	D	C	C	D
			Colorado	D	D	C-	C+	D-	A	A
			Connecticut	C+	F	B-	F	B+	C+	C
			Delaware	C-	C	C	F	B	A	B
			Florida	D	C	B+	C	D+	C	A
			Georgia	D	C	C-	B	C	D	C+
			Hawaii	F	D-	B+	C	D-	A	B
			Idaho	B-	C-	D	A	D	D+	C
			Illinois	C	C	B	C	A	C	D
			Indiana	A	B-	D+	C	A	C	A
			Iowa	A	A	C	B	C	C	F
			Kansas	A	B	C+	B+	B	D	C
			Kentucky	B+	A	D-	D+	A	D-	C-
			Louisiana	C+	C+	F	B-	C	C	C
			Maine	C	C-	B	F	D	D	D-
			Maryland	D-	D	C+	C	C-	C	C
			Massachusetts	D+	D-	C	C	C	C+	D
			Michigan	A	D+	C-	D	A	D	C-
			Minnesota	C	C	A	C	C+	A	F
			Mississippi	C	C	D+	B	C-	F	B-
			Missouri	C	B	C	C+	B-	B+	A
			Montana	D	C+	C	C+	F	C-	A
			Nebraska	D+	A	B	B	C	C	D
			Nevada	F	D	F	B+	D	C	B-
			New Hampshire	B	C	B-	D	C+	C	C
			New Jersey	C	B	C	D-	C	C	F
			New Mexico	D	F	C	C	F	B	C
			New York	F	F	A	C-	C	C-	F
			North Carolina	C	C	C	B-	C+	B	C-
			North Dakota	C-	A	C	B	C	F	C
			Ohio	A	B+	D	C	A	D	D-
			Oklahoma	C	C-	D-	C-	C	F	B
			Oregon	B-	C	C	C	C-	A	C+
			Pennsylvania	C	C	C	D	B	B	D+
			Rhode Island	D	F	B	D	F	C+	F
			South Carolina	B	D	D	C	B	D	C
			South Dakota	C-	C	B	A	D	C	B
			Tennessee	B	B+	D	C-	B+	C	C
			Texas	B	A	F	C	B-	B	C
			Utah	C	C+	A	A	C	B	B
			Vermont	B	D	C	F	C	C-	D
			Virginia	F	F	C	D+	D+	B-	B+
			Washington	C+	D+	A	D-	C	B-	C
			West Virginia	C	B-	F	D	D	F	D+
			Wisconsin	B+	C	C+	C	B	D-	D
			Wyoming	D-	B	A	C	F	B	B+
		";

		return $this->__import($year, $columns, $grades);
	}
}