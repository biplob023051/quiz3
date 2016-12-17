<?php
namespace App\Auth;

use Cake\Auth\AbstractPasswordHasher;
use Cake\Utility\Security;

/**
 * Simple password hashing class.
 *
 * @package       Cake.Controller.Component.Auth
 */
class SimplePasswordHasher extends AbstractPasswordHasher {

/**
 * Config for this object.
 *
 * @var array
 */
	protected $_config = array('hashType' => null);

/**
 * Generates password hash.
 *
 * @param string $password Plain text password to hash.
 * @return string Password hash
 * @link http://book.cakephp.org/2.0/en/core-libraries/components/authentication.html#hashing-passwords
 */
	public function hash($password) {
		return Security::hash($password, $this->_config['hashType'], true);
	}

/**
 * Check hash. Generate hash for user provided password and check against existing hash.
 *
 * @param string $password Plain text password to hash.
 * @param string Existing hashed password.
 * @return boolean True if hashes match else false.
 */
	public function check($password, $hashedPassword) {
		return $hashedPassword === $this->hash($password);
	}

}
