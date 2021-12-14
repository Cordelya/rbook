create table if not exists comments (id mediumint unsigned not null, comment blob, recipeid mediumint unsigned not null, userid mediumint unsigned not null, rating smallint unsigned, postdate datetime, modifieddate timestamp, createdate timestamp, unique(recipeid,userid), index comments_users (userid), index comments_recipes (recipeid), foreign key (recipeid) references recipes(id) on delete cascade, foreign key (userid) references users(id) on delete cascade, primary key (id)) ENGINE = INNODB;

create table if not exists comments_seq (id INTEGER UNSIGNED AUTO_INCREMENT NOT NULL, PRIMARY KEY(id));

INSERT INTO comments_seq (id) VALUES (0);
UPDATE comments_seq SET id = 0;

commit;

alter table recipes add column description blob, add column preptime smallint, add column cooktime smallint, add column cached_rating smallint, add column cached_ratinghits smallint;
