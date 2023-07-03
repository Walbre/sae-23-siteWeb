# Site web SAE 23

Site web créé pour le projet sae23, année scolaire 2022-2023

Auteurs : Brewal et Morgan

## Structure

- [site web en php](./site/)

- [api captchat créé pour l'ocasion](./captchat/)

## Présentation

Le but du projet était de créer un site web en php, les sources du site web sont dans le dossier site, elle contient les sources de toutes les pages. Il y a 2 bases de données, `comptes` qui permet de gérer les comptes pour se connecter sur le site et `representants` qui regroupe les données affichées sur le site web.

L'api captchat quant a elle à été créée pour faire un captchat plus sécurisée que ce qui était proposé. Tout est expliqué dans / une fois lancée.

## Installation

- Pour captchat :

  - Avoir Python 3 d'installé

  - Avoir Flask d'installé
    ```sh
    pip install flask
    ```

- Pour le site en php:

  - Avoir Apache d'installé:
    ```sh
    sudo apt install Apache2
    ```

  - Avoir php d'installé
    ```sh
    sudo apt install php
    ```

  - Avoir sqlite d'installé
    ```sh
    sudo apt install sqlite3
    ```
  - Avoir le driver php sqlite d'installé
    ```sh
    sudo apt install php-sqlite3
    ```

  - Activer d'extension pdo dans le php.ini

  
  - Copier le contenu de site/ dans /var/www/html et restart apache2
    ```sh
    cp -r site/* /var/www/html/
    systemctl restart apache2
    ```
