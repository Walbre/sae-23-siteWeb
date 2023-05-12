from flask import Flask, request, session
import os
from random import randint
from base64 import b64encode
from sqlconn import sqldb
import requests

os.chdir(os.path.dirname(__file__))

app = Flask(__name__)
app.secret_key = "".join("abccdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"[randint(0, 61)] for i in range(64))

ALL_FILES = os.listdir("assets/")

db = sqldb("bdd/tokens.sqlite")

session_req = requests.Session()


@app.context_processor
def sess():
    return dict(session=session)

@app.route("/getcaptchat", methods=["GET"])
def create_captchat():
    files = ALL_FILES[:]
    choosen_files = []
    captchat_rep = ""
    for _ in range(9):
        file_path = files.pop(randint(0, len(files) - 1))
        
        if "true" in file_path:
            captchat_rep += "1"
        else:
            captchat_rep += "0"
        
        with open(f"assets/{file_path}", 'rb') as f:
            choosen_files.append(f"data:image/jpeg;base64,{b64encode(f.read()).decode('utf-8')}")
    
    

    
    db.exec(f"INSERT INTO tokens(solve) VALUES ('{captchat_rep}')")
    
    id = db.read(f"SELECT token FROM tokens WHERE token=(SELECT DISTINCT MAX(token) FROM TOKENS)")
    
    response = {
        "id" : id[0][0],
        "images" : choosen_files
    }
    return response
        
    


@app.route("/verifycaptchat", methods=["POST"])
def verify_captchat():
    id = request.form.get("id")
    solve = request.form.get("solve")
    if id == None or solve == None:
        return {"reponse" : "false",
                "error" : "Il faut spécifier l'id et le solve"
                }

    if type(id) != str or type(solve) != str:
        return {"reponse" : "false",
                "error" : "l'id et solve doivent etre de type str"
                }
        
    if len(solve) != 9:
        return {"reponse" : "false",
                "error" : "solve doit faire 9 de long"
                }

    for i in range(len(id)):
        if not id[i] in "0123456789":
            return {"reponse" : "false",
                    "error" : f"l'id n'est composé que de chiffre, '{id[i]}' n'en est pas un"
                    }
    
    for i in range(len(solve)):
        if not solve[i] in "01":
            return {"reponse" : "false",
                    "error" : f"solve n'est composé que de 0 et 1, '{id[i]}' n'en est pas un"
                    }
    
    
    normal_resp = db.read(f"SELECT solve FROM tokens WHERE token='{id}'")
    
    if normal_resp == []:
        return {"reponse" : "false",
                "error" : "Cet id ne corresppond a rien"
                }
    
    if normal_resp[0][0] == solve:
        return {"reponse" : "true"}
    else:
        return {"reponse" : "false",
                "error" : "Mauvaise reponse"
                }


@app.route("/", methods=["GET"])
def presentation():
    with open("html/presentation.html", encoding='utf-8') as f:
        return f.read()
        


@app.route("/exemple", methods=["GET", "POST"])
def exemple():
    if request.method == "POST" and len(session) == 1:
        rep = request.form.get("rep")
        if rep != None and type(rep) == str:
            # verification du captchat
            resp = session_req.post("http://127.0.0.1:8080/verifycaptchat", data={"id":session["id"], "solve":rep}).json()
            if resp["reponse"] == "true":
                with open("html/conn_reussi.html", encoding='utf-8') as f:
                    return f.read()
    
    
    # generation du captchat
    with open("html/exemple.html", encoding='utf-8') as f:
        content = f.read()
    resp = session_req.get("http://127.0.0.1:8080/getcaptchat").json()
    session["id"] = str(resp["id"])
    images = resp["images"]
    for i in range(9):
        content = content.replace("{img"+str(i)+"}", images[i])
    return content


app.run('0.0.0.0', 8080, debug=True)