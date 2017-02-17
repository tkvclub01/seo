import sys, json
from google import google

datas = sys.argv[1].split('@@')
num_page = 1
search_results = google.search(datas[2], num_page,"en",True,datas[0],datas[1])
index = len(search_results)
result = '0@@'
if index > 0:
    result =   str(search_results[index-1].rank) + "@@" + str(search_results[index-1].link)
print result    