CREATE TABLE IF NOT EXISTS "comptes"(
   "login" VARCHAR PRIMARY KEY NOT NULL,
   "motdepasse" VARCHAR DEFAULT lannion,
   "statut" VARCHAR NOT NULL DEFAULT utilisateur
);

INSERT INTO comptes VALUES ("admin", "lannion", "administrateur");
INSERT INTO comptes(login, motdepasse) VALUES ("user", "lannion");
