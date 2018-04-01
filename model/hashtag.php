<?php
namespace Model\Hashtag;
use \Db;
use \PDO;
use \PDOException;
/**
 * Hashtag model
 *
 * This file contains every db action regarding the hashtags
 */

/**
 * Attach a hashtag to a post
 * @param pid the post id to which attach the hashtag
 * @param hashtag_name the name of the hashtag to attach
 * @return true or false (if something went wrong)
 */
function attach($pid, $hashtag_name) {
    try{
        $db = \Db::dbc();

        $sql_insert1 = 'INSERT INTO TAG (name_tag) VALUES (?)';
        $sth_insert1 = $db->prepare($sql_insert1);
        if(!$sth_insert1->execute(array($hashtag_name))){
            return false;
        }
        $id_tag = $db->lastinsertid();

        $sql_insert2 = 'INSERT INTO MAKE_TAG (id_tweet, id_tag) VALUES (?, ?)';
        $sth_insert2 = $db->prepare($sql_insert2);
        if(!$sth_insert2->execute(array($pid, $id_tag))){
            return false;
        }
        return true;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * List hashtags
 * @return a list of hashtags names
 */
function list_hashtags() {
     try{
        $db = \Db::dbc();
        $sql = 'SELECT DISTINCT name_tag FROM TAG';
        $sth = $db->query($sql);

        $array_all = array();

        foreach($sth as $row){
            $array_all[] = $row['name_tag'];
        }
        return $array_all;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * List hashtags sorted per popularity (number of posts using each)
 * @param length number of hashtags to get at most
 * @return a list of hashtags
 */
function list_popular_hashtags($length) {
    try{
        $db = \Db::dbc();
        $sql = 'SELECT name_tag, COUNT(*) AS nb_use FROM TAG GROUP BY name_tag ORDER BY nb_use DESC';
        $sth = $db->query($sql);
        $array_pop = array();

        for($i = 0; $i < $length; $i ++){
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            $array_pop[] = $row['name_tag'];
        }
        return $array_pop;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/**
 * Get posts for a hashtag
 * @param hashtag the hashtag name
 * @return a list of posts objects or null if the hashtag doesn't exist
 */
function get_posts($hashtag_name) {
    try{
        $db = \Db::dbc();

        $sql = 'SELECT id_tweet FROM MAKE_TAG WHERE id_tag IN
                                (SELECT id_tag FROM TAG WHERE name_tag = ?)';
        $sth = $db->prepare($sql);
        $sth->execute(array($hashtag_name));

        $array_tag_posts = array();

        while($row = $sth->fetch(PDO::FETCH_ASSOC)){
		    $tag_posts = \Model\Post\get($row['id_tweet']);
            $array_tag_posts[] = $tag_posts;
        }
        return $array_tag_posts;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}

/** Get related hashtags
 * @param hashtag_name the hashtag name
 * @param length the size of the returned list at most
 * @return an array of hashtags names
 */
function get_related_hashtags($hashtag_name, $length) {
    try{
        $db = \Db::dbc();

        $sql = 'SELECT id_tag FROM MAKE_TAG WHERE id_tweet = ?';
        $sql2 = 'SELECT name_tag FROM TAG WHERE id_tag = ?';

        $array_post = get_posts($hashtag_name);
        $array_tags = array();

        foreach($array_post as $post){
            $id_post = $post->id;

            $sth = $db->prepare($sql);
            $sth->execute(array($id_post));

            for($i = 0; $i < $length; $i++){
                while($row = $sth->fetch(PDO::FETCH_ASSOC)){
                    $id_tag = $row['id_tag'];
                    $sth2 = $db->prepare($sql2);
                    $sth2->execute(array($id_tag));
                    $row2 = $sth2->fetch(PDO::FETCH_ASSOC);
                    // we don't put the hashtag itself in the array
                    if($row2['name_tag'] != $hashtag_name){
                        $array_tags [] = $row2['name_tag'];
                    }
                }
            }
        }
        return $array_tags;
    }catch(\PDOException $e){
        echo $e->getMessage();
    }
}
