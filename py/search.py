from google import google
num_page = 10
search_results = google.search("dantri", num_page,"en",True,"my")
for search_result in search_results:
    print search_result.link