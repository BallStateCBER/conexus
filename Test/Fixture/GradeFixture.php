<?php
/**
 * GradeFixture
 *
 */
class GradeFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'category_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'state_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'year' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'grade' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'category_id' => 1,
			'state_id' => 1,
			'year' => 1,
			'grade' => '',
			'created' => '2012-05-23 15:44:53',
			'modified' => '2012-05-23 15:44:53'
		),
	);
}
