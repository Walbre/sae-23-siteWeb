DROP TABLE tokens;

CREATE TABLE IF NOT EXISTS tokens(
   token INTEGER PRIMARY KEY AUTOINCREMENT,
   solve VARCHAR(9)
)