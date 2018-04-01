<?php
namespace Model\Post;
use \Db;
use \PDO;
use \PDOException;
/**
 * Post
 *
 * This file contains every db action regarding the posts
 */

/**
 * Get a post in db
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or null if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 */
function get($id) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM TWEET WHERE id_tweet = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($id));

        $row_tweet = $sth->fetch(PDO::FETCH_ASSOC);

        if(!$row_tweet){
            return NULL;
        }

        return (object) array(
            "id" => $row_tweet['ID_TWEET'],
            "text" => $row_tweet['TEXT'],
            "date" => $row_tweet['DATE_PUB'],
            "author" => \Model\User\get($row_tweet['ID_USER'])
        );
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a post with its likes, responses, the hashtags used and the post it was the response of
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the likes attribute is an array of users objects
 * @warning the hashtags attribute is an of hashtags objects
 * @warning the responds_to attribute is either null (if the post is not a response) or a post object
 */
function get_with_joins($id) {
    try{
        $db = \Db::dbc();

        $sql_tweet = 'SELECT * FROM TWEET WHERE id_tweet = ?';
        $sth_tweet = $db->prepare($sql_tweet);
        $sth_tweet->execute(array($id));
        $row_tweet = $sth_tweet->fetch(PDO::FETCH_ASSOC);
        if(!$row_tweet){
            return NULL;
        }

        // get responds_to
        $id_author = $row_tweet['ID_USER'];
        $date = $row_tweet['DATE_PUB'];
        $text = $row_tweet['TEXT'];
        $id_responds_to = $row_tweet['ID_TWEET_RESPONDED'];

        if($id_responds_to != NULL){
            $obj_responds_to = get($id_responds_to);
            $date_respond = $date;
        }else{
            $obj_responds_to = NULL;
        }

        // get the likes
        $array_likes = get_likes($id);

        // get the hashtags
        $sql_tag = 'SELECT * FROM TAG WHERE id_tag IN
                        (SELECT id_tag FROM MAKE_TAG WHERE id_tweet = ?)';
        $sth_tag = $db->prepare($sql_tag);
        $sth_tag->execute(array($id));
        $array_tags = array();
        while($row_tag = $sth_tag->fetch(PDO::FETCH_ASSOC)){
            $array_tags[] = $row_tag['NAME_TAG'];
        }

        $obj_get = (object) array(
            "id" => $id,
            "text" => $text,
            "date" => $date,
            "author" => \Model\User\get($id_author),
            "likes" => $array_likes,
            "hashtags" => $array_tags,
            "responds_to" => $obj_responds_to
        );

        return $obj_get;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Create a post in db
 * @param author_id the author user's id
 * @param text the message
 * @param mentioned_authors the array of ids of users who are mentioned in the post
 * @param response_to the id of the post which the creating post responds to
 * @return the id which was assigned to the created post, null if anything got wrong
 * @warning this function computes the date
 * @warning this function adds the mentions (after checking the users' existence)
 * @warning this function adds the hashtags
 * @warning this function takes care to rollback if one of the queries comes to fail.
 */
function create($author_id, $text, $response_to = null) {
    try{
        $db = \Db::dbc();
        //get the time
        $objDateTime_now = new \DateTime('NOW');
        $date_now = $objDateTime_now->format('Y-m-d H:i:s');

        //insert the attributes into table TWEET
        $sql = 'INSERT INTO TWEET (id_user, date_pub, text, id_tweet_responded)
                VALUES (?, ?, ?, ?)';
        $sth = $db->prepare($sql);
        $sth->execute(array($author_id, $date_now, $text, $response_to));

        $id_tweet = $db->lastinsertid();

        // hashtags
        $array_tags = extract_hashtags($text);
        foreach($array_tags as $tag_name){
            \Model\hashtag\attach($id_tweet, $tag_name);
        }

        // if some users have been mentioned in this text
        $array_mentions = extract_mentions($text);
        foreach($array_mentions as $username_mentioned){
            $obj_user = \Model\user\get_by_username($username_mentioned);
            mention_user($id_tweet, $obj_user->id);
        }

        return $id_tweet;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get the list of used hashtags in message
 * @param text the message
 * @return an array of hashtags
 */
function extract_hashtags($text) {
    return array_map(
        function($el) { return substr($el, 1); },
        array_filter(
            explode(" ", $text),
            function($c) {
                return $c !== "" && $c[0] == "#";
            }
        )
    );
}

/**
 * Get the list of mentioned users in message
 * @param text the message
 * @return an array of usernames
 */
function extract_mentions($text) {
    return array_map(
        function($el) { return substr($el, 1); },
        array_filter(
            explode(" ", $text),
            function($c) {
                return $c !== "" && $c[0] == "@";
            }
        )
    );
}

/**
 * Mention a user in a post
 * @param pid the post id
 * @param uid the user id to mention
 * @return true if everything went ok, false else
 */
function mention_user($pid, $uid) {
    try{
        $db = \Db::dbc();
        $sql = 'INSERT INTO MENTION (id_user, id_tweet, read_mention)
                            VALUES (?, ?, false)';
        $sth = $db->prepare($sql);
        if($sth->execute(array($uid, $pid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get mentioned user in post
 * @param pid the post id
 * @return the array of user objects mentioned
 */
function get_mentioned($pid) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT id_user FROM MENTION WHERE id_tweet = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($pid));
        $array_mentioned = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $obj_users = \Model\User\get($row['id_user']);
            array_push($array_mentioned, $obj_users);
        }

        return $array_mentioned;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }

}

/**
 * Delete a post in db
 * @param id the id of the post to delete
 * @return true if the post has been correctly deleted, false else
 */
function destroy($id) {
    try{
        $db = \Db::dbc();
        $sql = 'DELETE FROM TWEET WHERE id_tweet = ?';
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
 * Search for posts
 * @param string the string to search in the text
 * @return an array of find objects
 */
function search($string) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT * FROM TWEET WHERE text LIKE ?';
        $sth = $db->prepare($sql);
        $string_new = '%'.$string.'%';
        $sth->execute(array($string_new));

        $array_tweets = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $obj_tweet = get($row['ID_TWEET']);
            array_push($array_tweets, $obj_tweet);
        }
        return $array_tweets;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * List posts
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return an array of the objects of each post
 * @warning this function does not return the passwords
 */
function list_all($date_sorted = false) {
    try{
        $db = \Db::dbc();
        $array_all = array();

        if($date_sorted){
            $sql = 'SELECT * FROM TWEET ORDER BY DATE_PUB ASC';
            $sth = $db->query($sql);

            while($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $obj = get($row['ID_TWEET']);
                unset($obj->author->password);
                if($date_sorted == "DESC"){
                    array_unshift($array_all, $obj);
                }else if($date_sorted == "ASC"){
                    array_push($array_all, $obj);
                }
            }
        }else{
            $sql = 'SELECT * FROM TWEET';
            $sth = $db->query($sql);
            while($row = $sth->fetch(PDO::FETCH_ASSOC)){
                $obj = get($row['ID_TWEET']);
                // delete the password from obj
                unset($obj->author->password);
                array_push($array_all, $obj);
            }
        }
        return $array_all;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a user's posts
 * @param id the user's id
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return the list of posts objects
 */
function list_user_posts($id, $date_sorted="DESC") {
    try{
        $db = \Db::dbc();

        if($date_sorted == "ASC"){
            $sql = 'SELECT id_tweet FROM TWEET WHERE id_user = ? ORDER BY date_pub ASC';
        }else{
            $sql = 'SELECT id_tweet FROM TWEET WHERE id_user = ? ORDER BY date_pub DESC';
        }

        $sth = $db->prepare($sql);
        $sth->execute(array($id));

        $array_tweets = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            $array_tweets[] = get($row['id_tweet']);
        }
        return $array_tweets;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a post's likes
 * @param pid the post's id
 * @return the users objects who liked the post
 */
function get_likes($pid) {
     try{
        $db = \Db::dbc();
        $sql = 'SELECT id_user FROM LIKES WHERE id_tweet = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($pid));
        $array_likes = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            //get the likes and its object
            $obj_likes = \Model\User\get($row['id_user']);
            array_push($array_likes, $obj_likes);
        }
        return $array_likes;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get a post's responses
 * @param pid the post's id
 * @return the posts objects which are a response to the actual post
 */
function get_responses($pid) {
   try{
        // this post is responded by other posts
        // so $pid = id_tweet_responded
        // we search for id_tweet
        $db = \Db::dbc();
        $sql = 'SELECT * FROM TWEET WHERE id_tweet_responded = ?';
        $sth = $db->prepare($sql);
        $sth->execute(array($pid));
        $array_responses = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
            //get the id_tweet_responded and its object
            $obj_responses = get($row['ID_TWEET']);
            array_push($array_responses, $obj_responses);
        }

        return $array_responses;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get stats from a post (number of responses and number of likes
 */
function get_stats($pid) {
    try{
        $db = \Db::dbc();

        $sql_posts_likes = 'SELECT COUNT(*) AS nb_likes FROM LIKES WHERE id_tweet = ?';
        $sth_posts_likes = $db->prepare($sql_posts_likes);
        $sth_posts_likes->execute(array($pid));
        $row_posts_likes = $sth_posts_likes->fetch(PDO::FETCH_NUM);
        $nb_posts_likes = $row_posts_likes[0];

        $sql_posts_responses = 'SELECT COUNT(id_tweet_responded) AS nb_responses FROM TWEET WHERE id_tweet = ?';
        $sql_posts_responses = $db->prepare($sql_posts_responses);
        $sql_posts_responses->execute(array($pid));
        $row_posts_responses = $sql_posts_responses->fetch(PDO::FETCH_NUM);
        $nb_posts_responses = $row_posts_responses[0];

        return (object) array(
            "nb_likes" => $nb_posts_likes,
            "nb_responses" => $nb_posts_responses
        );
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Like a post
 * @param uid the user's id to like the post
 * @param pid the post's id to be liked
 * @return true if the post has been liked, false else
 */
function like($uid, $pid) {
    try{
        $db = \Db::dbc();
        $sql = 'INSERT INTO LIKES
                (id_tweet, id_user, date_like, read_like)
                VALUES(?, ?, ?, false)';
        $objDateTime = new \DateTime('NOW');
        $date_now = $objDateTime->format('Y-m-d H:i:s');
        $sth = $db->prepare($sql);
        if($sth->execute(array($pid, $uid, $date_now))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Unlike a post
 * @param uid the user's id to unlike the post
 * @param pid the post's id to be unliked
 * @return true if the post has been unliked, false else
 */
function unlike($uid, $pid) {
    try{
        $db = \Db::dbc();
        $sql = 'DELETE FROM LIKES
                WHERE id_user = ? AND id_tweet = ?';
        $sth = $db->prepare($sql);

        if($sth->execute(array($uid, $pid))){
            return true;
        }else{
            return false;
        }
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}
