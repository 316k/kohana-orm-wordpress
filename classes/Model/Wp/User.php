<?php
class Model_WP_User extends Model_WP
{
	protected $_table_name = 'wp_users';
	protected $_primary_key = 'ID';
	
	protected $_has_many = array(
		'posts' => array(
			'model' => 'WP_Post',
			'foreign_key' => 'post_author',
		),
		'comments' => array(
			'model' => 'WP_Comment',
			'foreign_key' => 'user_id',
		),
		'links' => array(
			'model' => 'WP_Link',
			'foreign_key' => 'link_owner',
		),
	);
	
	public function add_meta($key, $value)
	{
		
		$meta = ORM::factory('WP_Usermeta');
		
		if($meta->exists($this->pk(), $key))
		{
			$meta = ORM::factory('WP_Usermeta')
				->where('user_id', $this->pk())
				->and_where('meta_key', '=', $key)
				->find()
			;
		}
		else
		{
			$meta->user_id = $this->pk();
		}
		
		$meta->meta_key = $key;
		$meta->meta_value = $value;
		$meta->save();
		
		return $meta->saved();
	}
}
