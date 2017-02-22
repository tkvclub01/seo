from oauth2client import client

flow = client.flow_from_clientsecrets(
    'client_secrets.json',
    scope='https://www.googleapis.com/auth/drive.metadata.readonly',
    redirect_uri='https://tools.clads.link/oauth2callback') 