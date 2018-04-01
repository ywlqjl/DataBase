<?php
namespace Model\Notification;
use \Db;
use \PDO;
use \PDOException;
/**
 * Notification model
 *
 * This file contains every db action regarding the notifications
 */

/**
 * Get a liked notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the liked_by attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the reading_date attribute is either a DateTime object or null (if it hasn't been read)
 */
function get_liked_notifications($uid) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM LIKES WHERE id_tweet IN (SELECT id_tweet FROM TWEET WHERE id_user = ? )';
        $sth = $db->prepare($sql);
        $sth->execute(array($uid));

        $array_like_noti = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){

            if($row['READ_DATE_LIKE'] != NULL){
                $date_read = new \DateTime($row['READ_DATE_LIKE']);
            }else{
                $date_read = NULL;
            }
            $obj_like_noti = (object) array(
                "type" => "liked",
                "post" => \Model\Post\get($row['ID_TWEET']),
                "liked_by" => \Model\User\get($row['ID_USER']),
                "date" => new \DateTime($row['DATE_LIKE']),
                "reading_date" => $date_read
            );

            $array_like_noti[] = $obj_like_noti;
        }
        //var_dump($array_like_noti[1]->liked_by->username);
        return $array_like_noti;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Mark a like notification as read (with date of reading)
 * @param pid the post id that has been liked
 * @param uid the user id that has liked the post
 * @return true if everything went ok, false else
 */
function liked_notification_seen($pid, $uid) {
    try{
        $db = \Db::dbc();
        $sql = 'UPDATE LIKES SET read_date_like = ?, read_like = true WHERE id_tweet = ? AND id_user = ?';
        $sth = $db->prepare($sql);

        $objDateTime = new \DateTime('NOW');
        $read_date_like = $objDateTime->format('Y-m-d H:i:s');
        if($sth->execute(array($read_date_like, $pid, $uid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a mentioned notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the post attribute is a post object
 * @warning the mentioned_by attribute is a user object
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_mentioned_notifications($uid) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM MENTION WHERE id_user = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($uid));

        $sql2 = 'SELECT * FROM TWEET WHERE id_tweet = ?';
        $sth2 = $db->prepare($sql2);

        $array_mention_noti = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $sth2->execute(array($row['ID_TWEET']));
            $row2 = $sth2->fetch(PDO::FETCH_ASSOC);

            if($row2){
                $date_mention = new \DateTime((\Model\Post\get($row['ID_TWEET']))->date);
                if($row['READ_DATE_MENTION'] != NULL){
                    $date_read = new \DateTime($row['READ_DATE_MENTION']);
                }else{
                    $date_read = NULL;
                }
                $obj_mention_noti = (object) array(
                    "type" => "mentioned",
                    "post" => \Model\Post\get($row['ID_TWEET']),
                    "mentioned_by" => \Model\User\get($row2['ID_USER']),
                    "date" => $date_mention,
                    "reading_date" => $row['READ_DATE_MENTION']
                );
                $array_mention_noti[] = $obj_mention_noti;
            }
        }
        //var_dump($array_mention_noti[0]->mentioned_by);
        return $array_mention_noti;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Mark a mentioned notification as read (with date of reading)
 * @param uid the user that has been mentioned
 * @param pid the post where the user was mentioned
 * @return true if everything went ok, false else
 */
function mentioned_notification_seen($uid, $pid) {
    try{
        $db = \Db::dbc();
        $sql = 'UPDATE MENTION SET read_date_mention = ?, read_mention = true WHERE id_tweet = ? AND id_user = ?';
        $sth = $db->prepare($sql);

        $objDateTime = new \DateTime('NOW');
        $read_date_mention = $objDateTime->format('Y-m-d H:i:s');

        if($sth->execute(array($read_date_mention, $pid, $uid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a followed notification in db
 * @param uid the id of the user in db
 * @return a list of objects for each like notification
 * @warning the user attribute is a user object which corresponds to the user following.
 * @warning the reading_date object is either a DateTime object or null (if it hasn't been read)
 */
function get_followed_notifications($uid) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM FOLLOW WHERE id_user_host = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($uid));

        $array_follow_noti = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $date_follow = new \DateTime($row['DATE_FOLLOW']);
            if($row['READ_DATE_FOLLOW'] != NULL){
                $date_read = new \DateTime($row['READ_DATE_FOLLOW']);
            }else{
                $date_read = NULL;
            }

            $obj_follow_noti = (object) array(
                "type" => "followed",
                "user" => \Model\User\get($row['ID_USER_FOLLOWER']),
                "date" => $date_follow,
                "reading_date" => $row['READ_DATE_FOLLOW']
            );
            //var_dump($array_follow_noti);
            $array_follow_noti[] = $obj_follow_noti;
        }
        return $array_follow_noti;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Mark a followed notification as read (with date of reading)
 * @param followed_id the user id which has been followed
 * @param follower_id the user id that is following
 * @return true if everything went ok, false else
 */
function followed_notification_seen($followed_id, $follower_id) {
    try{
        $db = \Db::dbc();
        $sql = 'UPDATE FOLLOW SET read_date_follow = ?, read_follow = true
                    WHERE id_user_host = ? AND id_user_follower = ?';
        $sth = $db->prepare($sql);

        $objDateTime = new \DateTime('NOW');
        $read_date_follow = $objDateTime->format('Y-m-d H:i:s');

        if($sth->execute(array($read_date_follow, $followed_id, $follower_id))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get all the notifications sorted by time (descending order)
 * @param uid the user id
 * @return a sorted list of every notifications objects
 */
function list_all_notifications($uid) {
    $ary = array_merge(get_liked_notifications($uid), get_followed_notifications($uid), get_mentioned_notifications($uid));

    usort(
        $ary,
        function($a, $b) {
            return $b->date->format('U') - $a->date->format('U');
        }
    );
    return $ary;
}

/**
 * Mark a notification as read (with date of reading)
 * @param uid the user to whom modify the notifications
 * @param notification the notification object to mark as seen
 * @return true if everything went ok, false else
 */
function notification_seen($uid, $notification) {
    switch($notification->type) {
        case "liked":
            return liked_notification_seen($notification->post->id, $notification->liked_by->id);
        break;
        case "mentioned":
            return mentioned_notification_seen($uid, $notification->post->id);
        break;
        case "followed":
            return followed_notification_seen($uid, $notification->user->id);
        break;
    }
    return false;
}

