<?php
class Model_WP_Comment extends Model_WP
{
	protected $_primary_key = 'comment_ID';
	protected $_table_name = 'wp_comments';
        
        protected $_belongs_to = array(
                'user' => array(
                        'model' => 'WP_User'
                )
        );
	
	protected $_has_many = array(
		'meta' => array(
			'model' => 'Wp_Commentmeta',
			'foreign_key' => 'comment_id',
		),
                'comments' => array(
                        'model' => 'WP_Comment',
                        'foreign_key' => 'comment_parent'
                )
	);
	
	public function add_meta($key, $value)
	{
		$meta = ORM::factory('WP_Commentmeta');
		
		if($meta->exists($this->pk(), $key))
		{
			$meta = ORM::factory('WP_Commentmeta')
				->where('comment_id', $this->pk())
				->and_where('meta_key', '=', $key)
				->find()
			;
		}
		else
		{
			$meta->comment_id = $this->pk();
		}
		
		
		$meta->meta_key = $key;
		$meta->meta_value = $value;
		$meta->save();
		
		return $meta->saved();
	}
}
