<?php
App::uses('AppController', 'Controller');
/**
 * Categories Controller
 *
 * @property Category $Category
 */
class CategoriesController extends AppController {
	public function view($id = null) {
		if (! is_numeric($id)) { // $id assumed to be a slug
			$slug = $id;
			$id = null;
			foreach ($this->categories as $cat_id => $category_name) {
				if ($slug == Inflector::slug($category_name)) {
					$id = $cat_id;
					break;
				}
			}
		}
		$this->Category->id = $id;
		if (!$this->Category->exists()) {
			throw new NotFoundException(__('Invalid category'));
		}

		$year = 2017;

		App::uses('Grade', 'Model');
		$Grade = new Grade();
		$grades = $Grade->find('all', array(
			'conditions' => array('year' => $year, 'category_id' => $id),
			'fields' => array('grade'),
			'contain' => array('State' => array('abbreviation', 'name')),
			'order' => 'State.name ASC'
		));
		$js_grade_definitions = array();
		foreach ($grades as $grade) {
			$js_grade_definitions[] = $grade['State']['abbreviation'].": '".$grade['Grade']['grade']."'";

		}
		$js_grade_definitions = implode(', ', $js_grade_definitions);

		$this->set(array(
			'title_for_layout' => $this->Category->field('name'),
			'category' => $this->Category->read(null, $id),
			'year' => $year,
			'js_grade_definitions' => $js_grade_definitions,
			'grades' => $grades
		));
	}
}
