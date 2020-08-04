CREATE TABLE usuarios (
  id bigint not null primary key auto_increment,
  email varchar(255) not null,
  senha varchar(255) not null,
  nome varchar(255) not null,
  adm tinyint(1) not null default 0,
  token varchar(255),
  confirmado tinyint(1) default 0
);