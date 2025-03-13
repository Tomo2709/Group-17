CREATE DATABASE petdb;
USE petdb;

drop table IF EXISTS sHistory;
drop table IF EXISTS posts;
drop table IF EXISTS threads;
drop table IF EXISTS boards;
drop table IF EXISTS pets;
drop table IF EXISTS users;

create table users(
	user_id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    CONSTRAINT pri_user PRIMARY KEY(user_id));
    

create table boards(
	board_id INT NOT NULL AUTO_INCREMENT,
    title VARCHAR(50) NOT NULL,
    CONSTRAINT pri_board PRIMARY KEY(board_id));
    
create table threads(
	thread_id INT NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    board INT NOT NULL,
    author INT NOT NULL,
    created DATE NOT NULL,
    CONSTRAINT pri_thread PRIMARY KEY(thread_id),
    CONSTRAINT for_authT FOREIGN KEY(author) REFERENCES users(user_id),
    CONSTRAINT for_bor FOREIGN KEY(board) REFERENCES boards(board_id));
    
create table posts(
	post_id INT NOT NULL AUTO_INCREMENT,
    thread INT NOT NULL,
    author INT NOT NULL,
    created DATE NOT NULL,
    image VARCHAR(255) NULL,
    message VARCHAR(255) NOT NULL,
    CONSTRAINT pri_post PRIMARY KEY(post_id),
    CONSTRAINT for_thr FOREIGN KEY(thread) REFERENCES threads(thread_id),
    CONSTRAINT for_authP FOREIGN KEY(author) REFERENCES users(user_id));

create table sHistory(
	search_id INT NOT NULL AUTO_INCREMENT,
    user INT NOT NULL,
    input VARCHAR(255) NOT NULL,
    CONSTRAINT pri_history PRIMARY KEY(search_id),
    CONSTRAINT for_user FOREIGN KEY(user) REFERENCES users(user_id));

INSERT INTO `boards`(`title`) VALUES ("cat"),("dog"),("hamster"),("fish"),("snake"),("lizard");





