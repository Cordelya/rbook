alter table `users` add column username varchar(50);

; update existing users here


alter table `users` add constraint unique(username);
alter table `users` modify username varchar(50) not null;








