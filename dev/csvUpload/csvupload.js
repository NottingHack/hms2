#!/usr/bin/env node
const path = require('path');
const fs = require('fs');
const csv = require('csvtojson')
const https = require('https');
const request = require('request');

const program = require('commander');

const pkg = require('./package.json');
const cerfile = require('../nottinghack-ca.crt.pem');
const casigningcert = fs.readFileSync(cerfile)

program
  .version(pkg.version)
  .description('Programmatic upload CSV to hms2.')
  .option('-t, --token <token>', 'Client token');

program
  .command('upload_csv <in_csv>')
  .description('Upload given csv file')
  .action(function (in_csv, options) {
    if (!program.token) {
        console.error('--token must be specified');
    }

    csv()
    .fromFile(in_csv)
    .then((jsonObj)=>{
        transactions = jsonObj.map(function (transaction) {
            if (transaction['Debit Amount'] != '') {
                amount = '-' . transaction['Debit Amount'];
            } else {
                amount = transaction['Credit Amount'];
            } 

            const dateParts = transaction['Transaction Date'].split('/');
            const formatedDate = dateParts.reverse().join('-');
            
            return {
                'sortCode' : transaction['Sort Code'].replace('\'', ''),
                'accountNumber' : transaction['Account Number'],
                'date' : formatedDate,
                'description' : transaction['Transaction Description'],
                'amount' : amount
                }
        });
        uplaodJsonTransactions(transactions);
    })

  });

program.parse(process.argv);

function uplaodJsonTransactions(transactions)
{
    const data = JSON.stringify(transactions);

    let options = { method: 'POST',
      url: 'https://hmsdev/oauth/token',
      headers: { 'content-type': 'application/json' },
      body: 
       { grant_type: 'client_credentials',
         client_id: '3',
         client_secret: 'zYVY1rmybiKrrBMjam3e6d3kR13jv51tXopG83Iq' },
      json: true,
      ca: casigningcert 
    };

    request(options, function (error, response, body) {
      if (error) throw new Error(error);

      const json = JSON.parse(body);

      // store the token
      console.log("Access Token:", json.access_token);
    });

    // const options = {
    //   hostname: 'hmsdev',
    //   port: 443,
    //   path: '/api/bank_transactions/upload',
    //   method: 'POST',
    //   headers: {
    //     'Content-Type': 'application/json',
    //     'Content-Length': data.length,
    //     'Authorization': 'Bearer '+program.token
    //   },
    //   ca: casigningcert
    // };

    // console.log(options);

    // const req = https.request(options, (res) => {
    //   console.log(`statusCode: ${res.statusCode}`)

    //   res.on('data', (d) => {
    //     process.stdout.write(d)
    //   })
    // })

    // req.on('error', (error) => {
    //   console.error(error)
    // })

    // req.write(data)
    // req.end()

}