<?php
class Model_WP_Link extends Model_WP
{
	protected $_primary_key = 'link_id';
	protected $_table_name = 'wp_links';
	
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'WP_User',
			'foreign_key' => 'link_owner',
		),
	);
}
