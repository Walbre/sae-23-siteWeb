CREATE TABLE IF NOT EXISTS "comptes"(
   "login" VARCHAR PRIMARY KEY NOT NULL,
   "motdepasse" VARCHAR DEFAULT 'lannion',
   "statut" VARCHAR NOT NULL DEFAULT 'utilisateur',
   "photo" VARCHAR NOT NULL DEFAULT 'images/photo1.png'
);

INSERT INTO comptes(login, motdepasse, statut) VALUES ("admin", "lannion", "administrateur");
INSERT INTO comptes(login, motdepasse) VALUES ("user", "lannion");
