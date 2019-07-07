#!/usr/bin/env node
const path = require('path');
const fs = require('fs');
const csv = require('csvtojson')
const program = require('commander');
const axios = require('axios');
const oauth = require('axios-oauth-client');
const tokenProvider = require('axios-token-interceptor');

const pkg = require('./package.json');
const cerfile = require('../nottinghack-ca.crt.pem');
const casigningcert = fs.readFileSync(cerfile)

program
  .version(pkg.version)
  .description('Programmatic upload CSV to hms2.')
  .arguments('<client_id> <client_secret> <in_csv>')
  .action(function (client_id, client_secret) {
     clientIdValue = client_id;
     clientSecretValue = client_secret;
     inCSVValue = in_csv;
  });

program.parse(process.argv);

if (typeof clientIdValue === 'undefined') {
   console.error('no client ID given!');
   process.exit(1);
}
if (typeof clientSecretValue === 'undefined') {
   console.error('no client Secret given!');
   process.exit(1);
}
if (typeof inCSVValue === 'undefined') {
   console.error('no CSV given!');
   process.exit(1);
}

csv()
.fromFile(inCSVValue)
.then((jsonObj)=>{
    transactions = jsonObj.map(function (transaction) {
        if (transaction['Debit Amount'] != '') {
            amount = '-' . transaction['Debit Amount'];
        } else {
            amount = transaction['Credit Amount'];
        }

        const txnDate = new Date(transaction['Transaction Date']. + ' UTC');

        return {
            'sortCode' : transaction['Sort Code'].replace('\'', ''),
            'accountNumber' : transaction['Account Number'],
            'date' : txnDate->toJSON(),
            'description' : transaction['Transaction Description'],
            'amount' : amount
            }
    });
    uplaodJsonTransactions(transactions);
});

function uplaodJsonTransactions(transactions)
{
  console.log(transactions);
  program.exit(0);
  // setup oauth and axios
  const getOwnerCredentials = oauth.client(axios.create(), {
    url: 'https://hmsdev/oauth/token',
    grant_type: 'client_credentials',
    client_id: clientIdValue,
    client_secret: clientSecretValue,
  });

  const instance = axios.create();
  instance.interceptors.request.use(
    // Wraps axios-token-interceptor with oauth-specific configuration,
    // fetches the token using the desired claim method, and caches
    // until the token expires
    oauth.interceptor(tokenProvider, getOwnerCredentials)
  );

  instance.post('https://hmsdev/api/bank_transactions/upload', transactions)
  .then(function (response) {
    console.log('Transactions Uploaded');
    // console.log(response);
  })
  .catch(function (error) {
    console.log(error);
  });
}