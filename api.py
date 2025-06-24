import requests
import json
import os


status_names = {
    1: "STATUS_QUEUED",
    3: "STATUS_PUBLISHED",
    4: "STATUS_DECLINED",
    5: "STATUS_SCHEDULED",
}


def read_env_file(file_path):
    with open(file_path, "r") as file:
        for line in file:
            line = line.strip()
            if line and not line.startswith("#"):
                key, value = line.split("=")
                os.environ[key] = value


read_env_file(".env")


def call_ojs_api(method):
    endpoint = os.getenv("BASE_URL")
    url = endpoint + method
    api_token = os.getenv("API_TOKEN")

    headers = {
        "Authorization": f"Bearer {api_token}",
        "Content-Type": "application/json",
    }

    print(f"Calling: {url}")
    response = requests.get(url, headers=headers)

    if response.status_code == 200:
        try:
            data = response.json()
            # print(json.dumps(response.json(), indent=4))
            return data
        except requests.exceptions.JSONDecodeError:
            print(f"Content: {response.content}")
    else:
        print(f"Error {response.status_code}: {response.text}")


# Export contents About
def getAbout():
    call_ojs_api("about")


# Export article documentation
def count_articles():
    data = call_ojs_api("submissions")

    publications = []

    status_count = {}

    # Iterate through the data object
    for key, value in data.items():

        for pub_key, pub_value in value["_data"]["publications"].items():
            publication = pub_value["_data"]
            # Create a simplified publication object
            pub_object = {
                "id": publication["id"],
                "title": publication["title"]["en"],
                "abstract": publication["abstract"]["en"],
                "authors": [
                    {
                        "id": author["_data"]["id"],
                        "country": author["_data"].get("country", ""),
                    }
                    for author in publication["authors"].values()
                ],
                "status": publication["status"],
            }
            status = publication["status"]
            status_name = status_names.get(status, f"STATUS_UNKNOWN_{status}")

            if status_name in status_count:
                status_count[status_name] += 1
            else:
                status_count[status_name] = 1

            publications.append(pub_object)
    print(json.dumps(status_count, indent=4))


# Export author data
def data_author():
    data = call_ojs_api("author")

    authors = []

    for key, value in data.items():
        author = value["_data"]

        pub_object = {
            "id": author["id"],
            "affiliation": author.get("affiliation", {}).get("en", ""),
            "familyName": author.get("familyName", {}).get("en", ""),
            "givenName": author.get("givenName", {}).get("en", ""),
           
        }

        authors.append(pub_object)
    print(json.dumps(authors, indent=4))


# Export reviewer data
def getReviewers():
    data = call_ojs_api("reviewers")

    reviewers = []

    for key, value in data.items():
        reviewer = value["_data"]

        pub_object = {
            "id": reviewer["id"],
            "affiliation": reviewer.get("affiliation", {}).get("en", ""),
            "familyName": reviewer.get("familyName", {}).get("en", ""),
            "givenName": reviewer.get("givenName", {}).get("en", ""),  
        }
        reviewers.append(pub_object)

    print(json.dumps(reviewers, indent=4))


getReviewers()


# Export issues documentation
# Export journal identification data
# Export information from the article submission page
# Export a summary of the last year and statistics on submissions and reviewers
# Export URLs

# Export all archives-documentation

# Export editorial
# Export editorial flow of the selected submission
