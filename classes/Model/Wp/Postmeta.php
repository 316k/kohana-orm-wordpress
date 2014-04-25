<?php
class Model_WP_Postmeta extends Model_WP
{
	protected $_primary_key = 'meta_id';
	protected $_table_name = 'wp_postmeta';
	
	protected $_belongs_to = array(
		'post' => array(
			'model' => 'Wp_Post',
		)
	);
	
	public function exists($post_id, $key)
	{
		return (bool) $this
			->where('post_id','=', $post_id)
			->and_where('meta_key', '=', $key)
			->count_all()
		;
	}
}
