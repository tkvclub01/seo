from google import google
num_page = 1
search_results = google.search("dantri", num_page,"en",True,"my","fanny.com.vn")
for search_result in search_results:
    print str(search_result.rank) + "--" + str(search_result.link)
    