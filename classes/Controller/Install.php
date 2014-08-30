<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Controller for first time installing.
 *
 * It creates tables in the database and adds the first user as an
 * administrator.
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 8, 2009
 * @package kwalbum
 * @since 3.0 Jul 8, 2009
 */

class Controller_Install extends Controller_Kwalbum
{
	private $_user = array('minNameLength' => 2, 'maxNameLength' => 40);

	public function action_index()
	{
		$this->template->title = 'Install';
		$this->template->set_global('location', '');
		$this->template->set_global('date', '');
		$this->template->set_global('tags', '');
		$this->template->set_global('people', '');
		$this->template->set_global('in_edit_mode', false);

		// Uncomment to delete everything and start over
		//$this->_drop_tables();

		// Do not continue installation if at least 1 user exists in the database
		try
		{
			$user = Model::factory('kwalbum_user');
			if ($user->load(1)->loaded == true)
			{
				$view = View::factory('kwalbum/install/2');
				$this->template->bind('content', $view);
				return;
			}
		}
		catch (Exception $e){}

		// Continue installation

		$form = array
		(
			'name' => '',
			'login_name' => '',
			'email' => '',
			'password' => '',
		);

		// Copy the form as errors so the errors will be stored with keys
		// matching the form field names
		$errors = $form;

		if ($_POST)
		{
			// TODO: add rules for login_name, email, and password
			$_POST['name'] = htmlspecialchars(trim($_POST['name']));
			$_POST['login_name'] = htmlspecialchars(trim($_POST['login_name']));
			$_POST['email'] = htmlspecialchars(trim($_POST['email']));
			$_POST['password'] = htmlspecialchars(trim($_POST['password']));
			$post = Validation::factory($_POST)
				->rule('name', 'not_empty')
				->rule('login_name', 'not_empty')
				->rule('email', 'not_empty')
				->rule('password', 'not_empty')
				->rule('name', 'min_length', array(':value', $this->_user['minNameLength']))
				->rule('name', 'max_length', array(':value', $this->_user['maxNameLength']));

			if ($post->check())
			{
				$data = $post->as_array();
				$name = $data['name'];
				$login_name = $data['login_name'];
				$email = $data['email'];
				$password = $data['password'];

				try
				{
					$this->_create_tables();

					// admin user
					$user = Model::factory('Kwalbum_User');
					$user->name = $name;
					$user->login_name = $login_name;
					$user->email = $email;
					$user->password = $password;
					$user->permission_level = 5;
					$user->save();

					// default owner of items once owned by a deleted user
					$user->clear();
					$user->name = 'Deleted User';

					$temp = '';
					$length = mt_rand(40,60);
					for ($i = 0; $i < $length; $i++)
						$temp = chr(mt_rand(0,122));
					$user->login_name = sha1($temp);

					$temp = '';
					$length = mt_rand(50,100);
					for ($i = 0; $i < $length; $i++)
						$temp = chr(mt_rand(0,122));
					$user->password = sha1($temp);

					// Normally, permission level should never be 0.
					$user->permission_level = 0;
					$user->save();

					// default location
					$location = Model::factory('kwalbum_location');
					$location->name = 'Unknown Location';
					$location->save();
					$this->template->content = new View('kwalbum/install/2');
					return;
				}
				catch (Exception $e)
				{
//					echo '<pre>'.print_r($e, 1).'</pre>';
					$errors = array('db'=>'There was an error creating the database tables.');
				}
			}
			else // Did not validate
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				// Pass the error message file name to the errors() method
				// Default error message file is in i18n/en_US/
				$errors = arr::overwrite($errors, $post->errors('install_form/errors'));
			}
		}

		$view = new View('kwalbum/install/1');
		$view->_user = $this->_user;
		$view->form = $form;
		$view->errors = $errors;
		$this->template->content = $view;
		return;
	}

	/** Drop all Kwalbum tables
	 *
	 * @param  Database to connect to
	 * @return void
	 */
	private function _drop_tables()
	{
		// Drop order is arranged based on foreign key restraints.
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_comments`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_items_tags`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_items_persons`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_tags`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_persons`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_items`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_users`')->execute();
		DB::query('', 'DROP TABLE IF EXISTS `kwalbum_locations`')->execute();
	}

	/** Create new Kwalbum tables
	 *
	 * @param  Database to connect to
	 * @return void
	 */
	private function _create_tables()
	{
		// Users
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_users`(
		          `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
				  `login_name` CHAR('.$this->_user['maxNameLength'].') NOT NULL ,
				  `email` TINYTEXT NOT NULL ,
				  `password` CHAR(40) NOT NULL ,
		          `visit_dt` DATETIME NOT NULL ,
		          `permission_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
				  `token` CHAR(40) NOT NULL ,
				  `reset_code` VARCHAR(40) NOT NULL ,
				  INDEX `login` (`login_name` ASC, `password` ASC) ,
				  INDEX `token` (`token` ASC) ,
				  PRIMARY KEY (`id`)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8
		        PACK_KEYS = DEFAULT')
			->execute();

		// Locations
		DB::query('', 'CREATE TABLE IF NOT EXISTS `kwalbum_locations`(
		          `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` VARCHAR(100) NOT NULL ,
		          `latitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
		          `longitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
		          `count` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 ,
		          `child_count` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 ,
			  `thumbnail_item_id` INT UNSIGNED NOT NULL DEFAULT 0 ,
			  `parent_location_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 ,
			  `name_hide_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
			  `coordinate_hide_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
			  `description` TEXT NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `location` (`name`(10) ASC) ,
		          INDEX `coordinates` (`latitude` ASC, `longitude` ASC) ,
			  INDEX `count` (`count` ASC) ,
			  INDEX `child_count` (`child_count` ASC) ,
			  INDEX `parent_location` (`parent_location_id` ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();

		// Items
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_items`(
		          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `type_id` TINYINT UNSIGNED NOT NULL ,
		          `user_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `location_id` MEDIUMINT UNSIGNED NOT NULL ,
		          `visible_dt` DATETIME NOT NULL ,
		          `sort_dt` DATETIME NOT NULL ,
		          `description` TEXT NOT NULL ,
		          `path` TINYTEXT NOT NULL ,
		          `filename` TINYTEXT NOT NULL ,
		          `has_comments` TINYINT NOT NULL DEFAULT 0 ,
		          `hide_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
		          `count` MEDIUMINT UNSIGNED NOT NULL DEFAULT 0 ,
		          `latitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
		          `longitude` DECIMAL(10,7) NOT NULL DEFAULT 0,
		          `update_dt` DATETIME NOT NULL ,
		          `create_dt` DATETIME NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `location_id` (`location_id` ASC) ,
		          INDEX `user_id` (`user_id` ASC) ,
		          INDEX `coordinates` (`latitude` ASC, `longitude` ASC) ,
		          INDEX `sort_dt` (`sort_dt` ASC) ,
		          CONSTRAINT `user_id_i`
		            FOREIGN KEY (`user_id` )
		            REFERENCES `kwalbum_users` (`id` ),
		          CONSTRAINT `location_id_i`
		            FOREIGN KEY (`location_id` )
		            REFERENCES `kwalbum_locations` (`id` )
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();

		// Comments
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_comments`(
		          `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
		          `item_id` INT UNSIGNED NOT NULL,
		          `name` TINYTEXT NOT NULL,
		          `text` TEXT NOT NULL,
		          `create_dt` DATETIME NOT NULL,
		          `ip` INT UNSIGNED NOT NULL,
		          PRIMARY KEY (`id`) ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `create_dt` (`create_dt` ASC) ,
		          CONSTRAINT `item_id`
		            FOREIGN KEY (`item_id`)
		            REFERENCES `kwalbum_items` (`id`)
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();

		// Tags
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_tags`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `count` MEDIUMINT UNSIGNED NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `tag` (`name`(10) ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();

		// Items_Tags relationship
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_items_tags`(
		          `item_id` INT UNSIGNED NOT NULL ,
		          `tag_id` SMALLINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `tag_id` (`tag_id` ASC) ,
		          PRIMARY KEY (`item_id`, `tag_id`) ,
		          CONSTRAINT `item_id_t`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `kwalbum_items` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `tag_id`
		            FOREIGN KEY (`tag_id` )
		            REFERENCES `kwalbum_tags` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();

		// Persons
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_persons`(
		          `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,
		          `name` TINYTEXT NOT NULL ,
		          `count` MEDIUMINT UNSIGNED NOT NULL ,
		          PRIMARY KEY (`id`) ,
		          INDEX `person` (`name`(10) ASC)
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();

		// Items_Persons relationship
		DB::query('', 'CREATE  TABLE IF NOT EXISTS `kwalbum_items_persons`(
		          `item_id` INT UNSIGNED NOT NULL ,
		          `person_id` SMALLINT UNSIGNED NOT NULL ,
		          INDEX `item_id` (`item_id` ASC) ,
		          INDEX `person_id` (`person_id` ASC) ,
		          PRIMARY KEY (`item_id`, `person_id`) ,
		          CONSTRAINT `item_id_p`
		            FOREIGN KEY (`item_id` )
		            REFERENCES `kwalbum_items` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE,
		          CONSTRAINT `person_id`
		            FOREIGN KEY (`person_id` )
		            REFERENCES `kwalbum_persons` (`id` )
		            ON DELETE CASCADE
		            ON UPDATE CASCADE
		        ) ENGINE = InnoDB
		        DEFAULT CHARACTER SET = utf8')
			->execute();
	}
}
