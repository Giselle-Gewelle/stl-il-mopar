<?php
final class Hashing {
	
	const SALT_LEN = 128;
	const ALPHA = [
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'
	];
	const SPECIAL = [
		'!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '=', '+', '{', '[', '}', ']', ':', ';', '<', '>', ',', '.', '/', '?'
	];
	
	public static function hashPassword(string $password, string $salt) : string {
		return self::sha512($salt . $password);
	}
	
	public static function sha512(string $string) : string {
		return hash('sha512', $string);
	}
	
	public static function generateSalt() : string {
		$salt = '';
		
		while(strlen($salt) < self::SALT_LEN) {
			$type = rand(0, 3);
			
			switch($type) {
				case 0:
					$index = rand(0, count(self::ALPHA) - 1);
					$char = self::ALPHA[$index];
					
					$upper = rand(0, 1);
					if($upper) {
						$char = strtoupper($char);
					}
					
					$salt .= $char;
					break;
				case 1:
					$salt .= rand(0, 9);
					break;
				case 2:
					$index = rand(0, count(self::SPECIAL) - 1);
					$salt .= self::SPECIAL[$index];
					break;
			}
		}
		
		return $salt;
	}
	
}
?>