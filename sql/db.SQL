DROP DATABASE IF EXISTS dbproject_test;

CREATE DATABASE IF NOT EXISTS dbproject_test;
USE dbproject_test;
# -----------------------------------------------------------------------------
#       TABLE : USER
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS USER
 (
   ID_USER INTEGER NOT NULL AUTO_INCREMENT ,
   USERNAME VARCHAR(128) NOT NULL  ,
   NAME VARCHAR(128) NOT NULL  ,
   DATE_INSCRI DATETIME NOT NULL  ,
   EMAIL VARCHAR(128) NULL  ,
   PASSWORD VARCHAR(128) NOT NULL  ,
   AVATAR LONGBLOB NULL  
   , PRIMARY KEY (ID_USER) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : TWEET
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TWEET
 (
   ID_TWEET INTEGER NOT NULL AUTO_INCREMENT ,
   ID_TWEET_RESPONDED INTEGER NULL  ,
   ID_USER INTEGER NOT NULL  ,
   DATE_PUB DATETIME NOT NULL  ,
   TEXT VARCHAR(255) NOT NULL  ,
   DATE_RESPONSE DATETIME NULL  
   , PRIMARY KEY (ID_TWEET) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : TAG
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS TAG
 (
   ID_TAG INTEGER NOT NULL AUTO_INCREMENT ,
   NAME_TAG VARCHAR(128) NULL  
   , PRIMARY KEY (ID_TAG) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MENTION
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MENTION
 (
   ID_USER INTEGER NOT NULL  ,
   ID_TWEET INTEGER NOT NULL  ,
   READ_DATE_MENTION DATETIME NULL ,
   READ_MENTION BOOL NOT NULL  
   , PRIMARY KEY (ID_USER,ID_TWEET) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : MAKE_TAG
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS MAKE_TAG
 (
   ID_TWEET INTEGER NOT NULL  ,
   ID_TAG INTEGER NOT NULL  
   , PRIMARY KEY (ID_TWEET,ID_TAG) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : LIKES
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS LIKES
 (
   ID_TWEET INTEGER NOT NULL  ,
   ID_USER INTEGER NOT NULL  ,
   DATE_LIKE DATETIME NOT NULL  ,
   READ_DATE_LIKE DATETIME NULL ,
   READ_LIKE BOOL NOT NULL  
   , PRIMARY KEY (ID_TWEET,ID_USER) 
 ) 
 comment = "";

# -----------------------------------------------------------------------------
#       TABLE : FOLLOW
# -----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS FOLLOW
 (
   ID_USER_HOST INTEGER NOT NULL  ,
   ID_USER_FOLLOWER INTEGER NOT NULL  ,
   DATE_FOLLOW DATETIME NOT NULL  ,
   READ_DATE_FOLLOW DATETIME NULL ,
   READ_FOLLOW BOOL NOT NULL  
   , PRIMARY KEY (ID_USER_HOST,ID_USER_FOLLOWER) 
 ) 
 comment = "";


# -----------------------------------------------------------------------------
#       CREATION DES REFERENCES DE TABLE
# -----------------------------------------------------------------------------


ALTER TABLE TWEET 
  ADD FOREIGN KEY FK_TWEET_TWEET (ID_TWEET_RESPONDED)
      REFERENCES TWEET (ID_TWEET) ;


ALTER TABLE TWEET 
  ADD FOREIGN KEY FK_TWEET_USER (ID_USER)
      REFERENCES USER (ID_USER) ;


ALTER TABLE MENTION 
  ADD FOREIGN KEY FK_MENTION_USER (ID_USER)
      REFERENCES USER (ID_USER) ;


ALTER TABLE MENTION 
  ADD FOREIGN KEY FK_MENTION_TWEET (ID_TWEET)
      REFERENCES TWEET (ID_TWEET) ;


ALTER TABLE MAKE_TAG 
  ADD FOREIGN KEY FK_MAKE_TAG_TWEET (ID_TWEET)
      REFERENCES TWEET (ID_TWEET) ;


ALTER TABLE MAKE_TAG 
  ADD FOREIGN KEY FK_MAKE_TAG_TAG (ID_TAG)
      REFERENCES TAG (ID_TAG) ;


ALTER TABLE LIKES 
  ADD FOREIGN KEY FK_LIKES_TWEET (ID_TWEET)
      REFERENCES TWEET (ID_TWEET) ;


ALTER TABLE LIKES 
  ADD FOREIGN KEY FK_LIKES_USER (ID_USER)
      REFERENCES USER (ID_USER) ;


ALTER TABLE FOLLOW 
  ADD FOREIGN KEY FK_FOLLOW_USER (ID_USER_HOST)
      REFERENCES USER (ID_USER) ;


ALTER TABLE FOLLOW 
  ADD FOREIGN KEY FK_FOLLOW_USER1 (ID_USER_FOLLOWER)
      REFERENCES USER (ID_USER) ;

