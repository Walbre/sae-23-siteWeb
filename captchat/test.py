import requests

s = requests.Session()

req = s.post("http://192.168.1.17:8080/verifycaptchat", data={"id":"7", "solve":"011001001"})

print(req.content.decode())