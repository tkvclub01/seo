from google import google
num_page = 1
search_results = google.search("dantri", num_page,"en",True,"","dantri.com.vn")
index = len(search_results)
result = {"rank":0, "url":""}
if index > 0:
    result = {"rank":search_results[index-1].rank, "url":search_results[index-1].link}
print result    