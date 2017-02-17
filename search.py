import sys, json
from google import google

datas = sys.argv[1].replace("+", " ").split('@@')
print datas[2]