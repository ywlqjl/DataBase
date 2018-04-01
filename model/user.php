<?php
namespace Model\User;
use \Db;
use \PDO;
use \PDOException;
/**
 * User model
 *
 * This file contains every db action regarding the users
 */

/**
 * Get a user in db
 * @param id the id of the user in db
 * @return an object containing the attributes of the user or null if error or the user doesn't exist
 */
function get($id) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM USER WHERE id_user = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($id));

        $row = $sth->fetch(PDO::FETCH_ASSOC);

        if($row == NULL){
            return NULL;
        }
        return (object) array(
            'id' => $row['ID_USER'],
            'username' => $row['USERNAME'],
            'name' => $row['NAME'],
            'date_inscri' => $row['DATE_INSCRI'],
            'email' => $row['EMAIL'],
            'password' => $row['PASSWORD'],
            'avatar' => $row['AVATAR']
        );
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Create a user in db
 * @param username the user's username
 * @param name the user's name
 * @param password the user's password
 * @param email the user's email
 * @param avatar_path the temporary path to the user's avatar
 * @return the id which was assigned to the created user, null if an error occured
 * @warning this function doesn't check whether a user with a similar username exists
 * @warning this function hashes the password
 */
function create($username, $name, $password, $email, $avatar_path) {
    try{
        $db = \Db::dbc();
        //hash the passworrd
        $password_hashed = hash_password($password);
        //get the time
        $objDateTime = new \DateTime('NOW');
        $date = $objDateTime->format('Y-m-d H:i:s');
        //insert the attributes into table USER
        $sql = 'INSERT INTO USER (username, name, password, email, avatar, date_inscri)
                VALUES (?, ?, ?, ?, ?, ?)';
        $sth = $db->prepare($sql);
        $sth->execute(array($username, $name, $password_hashed, $email, $avatar_path, $date));

        //search the id
        $sql2 = 'SELECT id_user FROM USER WHERE username = ?';
        $sth2 = $db->prepare($sql2);
        $sth2->execute(array($username));

        if($row = $sth2->fetch(PDO::FETCH_ASSOC)){
            return $row['id_user'];
        }else{
            return NULL;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param username the user's username
 * @param name the user's name
 * @param email the user's email
 * @return true if everything went fine, false else
 * @warning this function doesn't check whether a user with a similar username exists
 */
function modify($uid, $username, $name, $email) {
    try{
        $db = \Db::dbc();
        $sql = 'UPDATE USER SET username = ?, name = ?, email = ? WHERE id_user = ?';
        $sth = $db->prepare($sql);
        if($sth->execute(array($username, $name, $email, $uid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @return true if everything went fine, false else
 * @warning this function hashes the password
 */
function change_password($uid, $new_password) {
    try{
        $db = \Db::dbc();
        $password_hashed = hash_password($new_password);
        $sql = 'UPDATE USER SET password = ? WHERE id_user = ?';
        $sth = $db->prepare($sql);
        if($sth->execute(array($password_hashed, $uid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param avatar_path the temporary path to the user's avatar
 * @return true if everything went fine, false else
 */
function change_avatar($uid, $avatar_path) {
    try{
        $db = \Db::dbc();
        $sql = 'UPDATE USER SET avatar = ? WHERE id_user = ?';
        $sth = $db->prepare($sql);
        if($sth->execute(array($avatar_path, $uid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Delete a user in db
 * @param id the id of the user to delete
 * @return true if the user has been correctly deleted, false else
 */
function destroy($id) {
    try{
        $db = \Db::dbc();
        $sql = 'DELETE FROM USER WHERE id_user = ?';
        $sth = $db->prepare($sql);
        if($sth->execute(array($id))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
    //return password_hash($password, PASSWORD_DEFAULT);
    return md5($password);
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM USER WHERE username LIKE ? OR name LIKE ?';
        $sth = $db->prepare($sql);
        $string_new = '%'.$string.'%';
        $sth->execute(array($string_new, $string_new));

        $array_users = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $obj = get($row['ID_USER']);
            array_push($array_users, $obj);
        }
        return $array_users;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * List users
 * @return an array of the objects of every users
 */
function list_all() {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM USER';
        $sth = $db->query($sql);

        /*
        $i = 0;
        while($row = $sth->fetch(PDO::FETCH_NUM)){
            $array_list[$i] = get($row[0]);
            $i++;
        }
        return $array_list;
        */

        $array_all = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $obj = get($row['ID_USER']);
            array_push($array_all, $obj);
        }
        return $array_all;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM USER WHERE username = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($username));

        $row = $sth->fetch(PDO::FETCH_ASSOC);
        if($row == NULL){
            return NULL;
        }

        $id = $row['ID_USER'];
        return get($id);
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM USER WHERE id_user IN
                    (SELECT id_user_follower FROM FOLLOW WHERE id_user_host = ?)';
        $sth = $db->prepare($sql);
        $sth->execute(array($uid));
        $array_followers = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            //get the id_usesr_follower and its object
            $obj_user= get($row['ID_USER']);
            array_push($array_followers, $obj_user);
        }

        return $array_followers;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM USER WHERE id_user IN
                    (SELECT id_user_host FROM FOLLOW WHERE id_user_follower = ?)';
        $sth = $db->prepare($sql);
        $sth->execute(array($uid));
        $array_followings = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            //get the id_user_host and its object
            $obj_user= get($row['ID_USER']);
            array_push($array_followings, $obj_user);
        }

        return $array_followings;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a user's stats
 * @param uid the user's id
 * @return an object which describes the stats
 */
function get_stats($uid) {
    try{
        $db = \Db::dbc();

        $sql_posts = 'SELECT COUNT(*) AS nb_posts FROM TWEET WHERE id_user = ?';
        $sth_posts = $db->prepare($sql_posts);
        $sth_posts->execute(array($uid));
        $row_posts = $sth_posts->fetch(PDO::FETCH_ASSOC);
        $nb_posts = $row_posts['nb_posts'];

        $sql_followers = 'SELECT COUNT(*) AS nb_followers FROM FOLLOW WHERE id_user_host = ?';
        $sth_followers = $db->prepare($sql_followers);
        $sth_followers->execute(array($uid));
        $row_followers = $sth_followers->fetch(PDO::FETCH_ASSOC);
        $nb_followers = $row_followers['nb_followers'];

        $sql_following = 'SELECT COUNT(*) AS nb_followings FROM FOLLOW WHERE id_user_follower = ?';
        $sth_following = $db->prepare($sql_following);
        $sth_following->execute(array($uid));
        $row_following = $sth_following->fetch(PDO::FETCH_ASSOC);
        $nb_following = $row_following['nb_followings'];

        return (object) array(
            "nb_posts" => $nb_posts,
            "nb_followers" => $nb_followers,
            "nb_following" => $nb_following
        );
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**mi ma mei jia mi
 * Verify the user authentification
 * @param username the user's username
 * @param password the user's password
 * @return the user object or null if authentification failed
 * @warning this function must perform the password hashing
 */
function check_auth($username, $password) {
    try{
        $obj_user = get_by_username($username);
        // if username doesn't exist
        if($obj_user == NULL){
            return NULL;
        }

        $password_get = $obj_user->password;
        $password_hashed = hash_password($password);

        if($password_hashed == $password_get){
            return $obj_user;
        }else{
            return NULL;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Verify the user authentification based on id
 * @param id the user's id
 * @param password the user's password (already hashed)
 * @return the user object or null if authentification failed
 */
function check_auth_id($id, $password) {
    try{
        $obj_user = get($id);
        // if ID doesn't exist
        if($obj_user == NULL){
            return NULL;
        }

        $password_get = $obj_user->password;

        if($password == $password_get){
            return get($id);
        }else{
            return NULL;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 * @return true if the user has been followed, false else
 */
function follow($id, $id_to_follow) {
    try{
        $db = \Db::dbc();

        $sql = 'INSERT INTO FOLLOW
                (id_user_host, id_user_follower, date_follow, read_follow)
                VALUES(?, ?, ?, false)';
        $objDateTime = new \DateTime('NOW');
        $date_now = $objDateTime->format('Y-m-d H:i:s');
        $sth = $db->prepare($sql);
        if($sth->execute(array($id_to_follow, $id, $date_now))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 * @return true if the user has been unfollowed, false else
 */
function unfollow($id, $id_to_unfollow) {
    try{
        $db = \Db::dbc();

        $sql = 'DELETE FROM FOLLOW
                WHERE id_user_follower = ? AND id_user_host = ?';
        $sth = $db->prepare($sql);
        if($sth->execute(array($id, $id_to_unfollow))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

