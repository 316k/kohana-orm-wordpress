<?php
class Model_WP_Post extends Model_WP
{
	protected $_primary_key = 'ID';
	protected $_table_name = 'wp_posts';

        protected $_belongs_to = array(
                'user' => array(
                        'model' => 'Wp_User',
                        'foreign_key' => 'post_author'
                    ),
                'post' => array(
                        'model' => 'Wp_Post',
                        'foreign_key' => 'post_parent'
                )
        );

	protected $_has_many = array(
		'meta' => array(
			'model' => 'Wp_Postmeta',
			'foreign_key' => 'post_id',
		),
		'comments' => array(
			'model' => 'Wp_Comment',
			'foreign_key' => 'comment_post_ID',
		),
		'terms' => array(
			'model' => 'Wp_Term',
			'through' => 'wp_term_relationships',
			'foreign_key' => 'object_id',
			'far_key' => 'term_taxonomy_id',
		),
                'posts' => array(
                        'model' => 'Wp_Post',
                        'foreign_key' => 'post_parent'
                ),
	);
	
	public function slug($slug)
	{
		$slug = strtolower($slug);
		$slug = str_replace('/',' ', $slug);
		$slug = preg_replace('/([^[:alnum:][:space:]])/', '', $slug);
		$slug = preg_replace('/([[:space:]]+)/',' ',trim($slug));
		$slug = preg_replace('/([[:space:]])/','-',$slug);
		
		return $slug;
	}

	public function add_meta($key, $value)
	{
		$meta = ORM::factory('Wp_Postmeta');
		
		if($meta->exists($this->pk(), $key))
		{
			$meta = ORM::factory('Wp_Postmeta')
				->where('post_id','=', $this->pk())
				->and_where('meta_key','=', $key)
				->find()
			;
		}
		else
		{
			$meta->post_id = $this->pk();
		}
		
		$meta->meta_key = $key;
		$meta->meta_value = $value;
		
		$meta->save();
		
		return $meta->saved();
	}

	public function has_category($name)
	{
		return $this->has_taxonomy($name, 'category');
	}
	
	public function has_tag($name)
	{
		return $this->has_taxonomy($name, 'post_tag');
	}
	
	public function add_category($name, $order = 0)
	{
		return $this->add_taxonomy($name, 'category', $order);
	}
	
	public function add_tag($name, $order = 0)
	{
		return $this->add_taxonomy($name, 'post_tag', $order);
	}
	
	public function remove_category($name)
	{
		return $this->remove_taxonomy($name, 'category');
	}
	
	public function remove_tag($name)
	{
		return $this->remove_taxonomy($name, 'post_tag');
	}
	
	public function add_taxonomy($item, $type, $order = 0)
	{
		$term = ORM::factory('Wp_Term')
			->where('name','=',$item)
			->or_where('slug', '=', $item)
			->find()
		;
		
		$taxonomy = ORM::factory('Wp_Term_Taxonomy')
			->where('term_id','=', $term->pk())
			->and_where('taxonomy','=', $type)
			->find()
		;

		if($taxonomy->loaded())
		{
			$relationship = ORM::factory('Wp_Term_Relationship');
			$relationship->object_id = $this->pk();
			$relationship->term_taxonomy_id = $taxonomy->pk();
			$relationship->term_order = $order;
			$relationship->save();

			return $relationship->saved();
		}
		
		return false;
	}
	
	public function has_taxonomy($item, $type)
	{
		$term = ORM::factory('Wp_Term')
			->where('name','=',$item)
			->or_where('slug', '=', $item)
			->find()
		;
		
		$taxonomy = ORM::factory('Wp_Term_Taxonomy')
			->where('term_id','=', $term->pk())
			->and_where('taxonomy','=', $type)
			->find()
		;
		
		return $this->has('terms', $taxonomy->pk());
	}
	
	public function remove_taxonomy($item, $type)
	{
		if($this->has_taxonomy($item, $type))
		{
			$term = ORM::factory('Wp_Term')
				->where('name','=',$item)
				->or_where('slug', '=', $item)
				->find()
			;

			$taxonomy = ORM::factory('Wp_Term_Taxonomy')
				->where('term_id','=', $term->pk())
				->and_where('taxonomy','=', $type)
				->find()
			;
			
			return $this->remove('terms', $taxonomy->pk());
		}
		
		return false;
	}
}