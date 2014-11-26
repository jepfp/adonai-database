<?php
namespace Scotty\restinterface\dao;

class UserDAO extends AbstractDAO {
	
	protected function transformResult($row){
		unset($row['hash']);
		return $row;
	}
	
}

