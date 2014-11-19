<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserRepository
 *
 * @author adamsabik
 */
class UserRepository extends Repository {
    
    public function emailExists($email) {
        $row = $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $email)->fetch();
        
        return count($row) == 1;
    }
    
    public function addResetPasswordHash($email, $hash) {
        return $this->database->table(self::TABLE_NAME)->where(self::COLUMN_NAME, $email)->update(['str_pass_hash' => $hash]);
    }
    
}
