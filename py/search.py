#!/usr/bin/python

import httplib2

from apiclient import errors
from apiclient.discovery import build
from oauth2client.client import OAuth2WebServerFlow


# Copy your credentials from the console
CLIENT_ID = '128153575894-fmsqsdrifhf2cdbjtjlptgrankkdb853.apps.googleusercontent.com'
CLIENT_SECRET = 'Z4GibOCA5h0g4rsWQlPAQH8E'

# Check https://developers.google.com/webmaster-tools/search-console-api-original/v3/ for all available scopes
OAUTH_SCOPE = 'https://www.googleapis.com/auth/webmasters.readonly'

# Redirect URI for installed apps
REDIRECT_URI = 'https://tools.clads.link'

# Run through the OAuth flow and retrieve credentials
flow = OAuth2WebServerFlow(CLIENT_ID, CLIENT_SECRET, OAUTH_SCOPE, REDIRECT_URI)

authorize_url = flow.step1_get_authorize_url()

print 'Go to the following link in your browser: ' + authorize_url
code = raw_input('Enter verification code: ').strip()
credentials = flow.step2_exchange(code)

# Create an httplib2.Http object and authorize it with our credentials
http = httplib2.Http()
http = credentials.authorize(http)

webmasters_service = build('webmasters', 'v3', http=http)

# Retrieve list of properties in account
site_list = webmasters_service.sites().list().execute()

# Filter for verified websites
verified_sites_urls = [s['siteUrl'] for s in site_list['siteEntry']
                       if s['permissionLevel'] != 'siteUnverifiedUser'
                          and s['siteUrl'][:4] == 'http']

# Printing the URLs of all websites you are verified for.
for site_url in verified_sites_urls:
  print site_url
  # Retrieve list of sitemaps submitted
  sitemaps = webmasters_service.sitemaps().list(siteUrl=site_url).execute()
  if 'sitemap' in sitemaps:
    sitemap_urls = [s['path'] for s in sitemaps['sitemap']]
    print "  " + "\n  ".join(sitemap_urls)
