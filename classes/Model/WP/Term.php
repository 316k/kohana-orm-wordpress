<?php
class Model_WP_Term extends Model_WP
{
	protected $_primary_key = 'term_id';
	
	protected $_has_many = array(
		'posts' => array(
			'model' => 'WP_Post',
			'through' => 'wp_term_relationships',
			'foreign_key' => 'term_taxonomy_id',
			'far_key' => 'object_id',
		),
		'term_taxonomy' => array(
			'model' => 'WP_Term_Taxonomy',
		)
	);
}
