import requests
import json
import os

def read_env_file(file_path):
    with open(file_path, 'r') as file:
        for line in file:
            line = line.strip()
            if line and not line.startswith('#'):
                key, value = line.split('=')
                os.environ[key] = value

read_env_file('.env')

def call_ojs_api(method):
    endpoint = os.getenv("BASE_URL")	
    url = endpoint+method
    api_token = os.getenv("API_TOKEN")

    headers = {
        "Authorization": f"Bearer {api_token}",
        "Content-Type": "application/json"
    }

    response = requests.get(url, headers=headers)

    if response.status_code == 200:
        print(f"Calling: {url}")
        print(json.dumps(response.json(), indent=4))
    else:
        print(f"Error {response.status_code}: {response.text}")

# Llamar a la función
call_ojs_api("getJournalIdentity")
