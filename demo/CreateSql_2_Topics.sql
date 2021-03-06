USE teamActivity4;

GRANT SELECT, INSERT, UPDATE ON teamActivity4.* TO 'ta4User'@'localhost' IDENTIFIED BY 'ta4pass';
FLUSH PRIVILEGES;

CREATE TABLE topic (
  id int PRIMARY KEY AUTO_INCREMENT,
  name varchar(40) NOT NULL
  );

INSERT INTO topic (name) VALUES ('Faith');
INSERT INTO topic (name) VALUES ('Sacrifice');
INSERT INTO topic (name) VALUES ('Charity');

CREATE TABLE scripture_topic (
  scriptureId int NOT NULL,
  topicId int NOT NULL,
  FOREIGN KEY (scriptureId) REFERENCES scriptures(id),
  FOREIGN KEY (topicId) REFERENCES topic(id)
  );